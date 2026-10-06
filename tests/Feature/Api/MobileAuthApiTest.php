<?php

use App\Mail\AccountActivationLinkMailToUser;
use App\Mail\ForgotPasswordLinkMailToUser;
use App\Mail\NewUserRegisterMailToAdmin;
use App\Mail\TwoFactorOtpMailToUser;
use App\Mail\WelcomeMailToUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
});

function mobileRegistrationPayload(array $changes = []): array
{
    return array_replace([
        'name' => 'API Test User', 'email' => 'apiuser@example.com', 'phone' => '03001234567',
        'password' => 'Password123!', 'password_confirmation' => 'Password123!',
    ], $changes);
}

function activeMobileUser(array $changes = []): User
{
    $user = User::factory()->create(array_replace([
        'email' => 'reader@example.com', 'password' => 'Password123!', 'phone' => '03001234567', 'is_active' => true,
    ], $changes));
    $user->assignRole('user');
    $user->twoFactorSetting()->create(['method' => null, 'is_enabled' => false, 'verified_at' => null]);

    return $user;
}

test('mobile registration needs no captcha and preserves inactive activation mail flow', function () {
    $response = $this->postJson('/api/auth/register', mobileRegistrationPayload())
        ->assertCreated()->assertJsonPath('data.requires_activation', true)
        ->assertJsonPath('data.user.email', 'apiuser@example.com')->assertJsonMissingPath('data.token')
        ->assertJsonMissingPath('data.user.password')->assertJsonMissingPath('data.user.activation_token');

    $user = User::query()->where('email', 'apiuser@example.com')->firstOrFail();
    expect($user->is_active)->toBeFalse()->and(Hash::check('Password123!', $user->password))->toBeTrue()
        ->and($user->activation_token)->toHaveLength(64)->and($user->activation_token_expires_at->isFuture())->toBeTrue()
        ->and($response->json('data'))->not->toHaveKey('g-recaptcha-response');
    Mail::assertSent(NewUserRegisterMailToAdmin::class, fn ($mail) => $mail->hasTo('cogentdevs@gmail.com'));
    Mail::assertSent(AccountActivationLinkMailToUser::class, fn ($mail) => $mail->hasTo($user->email));
});

test('mobile registration enforces normal validation uniqueness and route throttling', function () {
    $this->postJson('/api/auth/register', mobileRegistrationPayload(['email' => 'bad', 'password_confirmation' => 'wrong']))
        ->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);
    $this->postJson('/api/auth/register', mobileRegistrationPayload())->assertCreated();
    $this->postJson('/api/auth/register', mobileRegistrationPayload())->assertUnprocessable()->assertJsonValidationErrors('email');

    foreach (range(1, 4) as $attempt) {
        $this->postJson('/api/auth/register', mobileRegistrationPayload(['email' => "other{$attempt}@example.com"]));
    }
    $this->postJson('/api/auth/register', mobileRegistrationPayload(['email' => 'limited@example.com']))->assertTooManyRequests();
});

test('website registration still requires captcha while API does not expose captcha configuration', function () {
    $this->post('/register', [
        'first_name' => 'Web', 'last_name' => 'User', 'email' => 'web@example.com', 'phone' => '03000000000',
        'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('g-recaptcha-response');

    $this->postJson('/api/auth/register', mobileRegistrationPayload())->assertCreated()
        ->assertJsonMissing(['sitekey'])->assertJsonMissing(['secret'])->assertJsonMissing(['g-recaptcha-response']);
});

test('activation inspection and completion activate once and send one welcome mail', function () {
    $this->postJson('/api/auth/register', mobileRegistrationPayload())->assertCreated();
    $mail = Mail::sent(AccountActivationLinkMailToUser::class)->first();
    parse_str(parse_url($mail->activationUrl, PHP_URL_QUERY), $query);
    $token = $query['token'];

    $this->getJson('/api/auth/activation?token='.$token)->assertOk()->assertJsonPath('data.state', 'pending')->assertJsonPath('data.can_activate', true);
    $this->postJson('/api/auth/activation', ['token' => $token])->assertOk();
    expect(User::query()->where('email', 'apiuser@example.com')->firstOrFail()->is_active)->toBeTrue();
    Mail::assertSent(WelcomeMailToUser::class, 1);
    $this->postJson('/api/auth/activation', ['token' => $token])->assertConflict();
    Mail::assertSent(WelcomeMailToUser::class, 1);
    $this->getJson('/api/auth/activation?token='.str_repeat('x', 64))->assertUnprocessable();
});

test('active non two factor frontend user can login and logout only current Sanctum token', function () {
    $user = activeMobileUser();
    $response = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'Password123!'])
        ->assertOk()->assertJsonPath('data.requires_2fa', false)->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.id', $user->id)->assertJsonMissingPath('data.user.password');
    $token = $response->json('data.token');
    $otherToken = $user->createToken('other-device')->plainTextToken;

    $this->withToken($token)->postJson('/api/auth/logout')->assertOk();
    $this->app['auth']->forgetGuards();
    $this->withToken($token)->postJson('/api/auth/logout')->assertUnauthorized();
    $this->app['auth']->forgetGuards();
    $this->withToken($otherToken)->postJson('/api/auth/logout')->assertOk();
});

test('invalid inactive and admin credentials never issue a token', function () {
    $active = activeMobileUser();
    $this->postJson('/api/auth/login', ['email' => $active->email, 'password' => 'wrong'])
        ->assertUnprocessable()->assertJsonMissingPath('data.token');
    $inactive = activeMobileUser(['email' => 'inactive@example.com', 'is_active' => false]);
    $this->postJson('/api/auth/login', ['email' => $inactive->email, 'password' => 'Password123!'])
        ->assertUnprocessable()->assertJsonMissingPath('data.token');
    $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Password123!', 'is_active' => true]);
    $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->postJson('/api/auth/login', ['email' => $admin->email, 'password' => 'Password123!'])
        ->assertUnprocessable()->assertJsonMissingPath('data.token');
});

test('email two factor login issues encrypted challenge and token only after valid otp', function () {
    $user = activeMobileUser();
    $user->twoFactorSetting->update(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    $login = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'Password123!'])
        ->assertOk()->assertJsonPath('data.requires_2fa', true)->assertJsonPath('data.delivery_method', 'email')
        ->assertJsonMissingPath('data.token');
    $challengeId = $login->json('data.challenge_id');
    $otp = Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
    expect($challengeId)->not->toBe((string) $user->id)->and($otp)->toMatch('/^\d{6}$/');

    $this->postJson('/api/auth/login/2fa/verify', ['challenge_id' => $challengeId, 'otp' => '999999'])
        ->assertUnprocessable()->assertJsonMissingPath('data.token');
    $this->postJson('/api/auth/login/2fa/verify', ['challenge_id' => $challengeId, 'otp' => $otp])
        ->assertOk()->assertJsonPath('data.requires_2fa', false)->assertJsonPath('data.token_type', 'Bearer');
    $this->postJson('/api/auth/login/2fa/verify', ['challenge_id' => $challengeId, 'otp' => $otp])->assertUnprocessable();
});

test('two factor challenge enforces expiry attempts invalid token and resend cooldown replacement', function () {
    $user = activeMobileUser();
    $user->twoFactorSetting->update(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    $challengeId = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'Password123!'])->json('data.challenge_id');
    $this->postJson('/api/auth/login/2fa/resend', ['challenge_id' => $challengeId])->assertTooManyRequests();
    $this->travel(61)->seconds();
    $newChallenge = $this->postJson('/api/auth/login/2fa/resend', ['challenge_id' => $challengeId])
        ->assertOk()->json('data.challenge_id');
    expect($newChallenge)->not->toBe($challengeId);
    $this->postJson('/api/auth/login/2fa/verify', ['challenge_id' => 'tampered', 'otp' => '123456'])->assertUnprocessable();
    DB::table('user_two_factor_challenges')->where('user_id', $user->id)->whereNull('consumed_at')->update(['expires_at' => now()->subSecond()]);
    $this->postJson('/api/auth/login/2fa/verify', ['challenge_id' => $newChallenge, 'otp' => '123456'])->assertUnprocessable();
});

test('forgot password is private rate limited and sends the existing reset mail only to frontend users', function () {
    $user = activeMobileUser();
    $this->postJson('/api/auth/password/forgot', ['email' => $user->email])->assertOk();
    Mail::assertSent(ForgotPasswordLinkMailToUser::class, fn ($mail) => $mail->hasTo($user->email));
    $this->postJson('/api/auth/password/forgot', ['email' => 'unknown@example.com'])->assertOk();
    $this->postJson('/api/auth/password/forgot', ['email' => 'bad'])->assertUnprocessable();
});

test('password reset uses broker rules consumes token and accepts new credentials', function () {
    $user = activeMobileUser();
    $token = Password::broker('users')->createToken($user);
    $body = ['token' => $token, 'email' => $user->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'];
    $this->postJson('/api/auth/password/reset', $body)->assertOk();
    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
    $this->postJson('/api/auth/password/reset', $body)->assertUnprocessable();
    $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'Password123!'])->assertUnprocessable();
    $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'NewPassword123!'])->assertOk();
});

test('auth routes are unversioned JSON endpoints and logout requires authentication', function () {
    $this->postJson('/api/auth/logout')->assertUnauthorized();
    $this->postJson('/api/v1/auth/login', [])->assertNotFound();
    $this->postJson('/api/api/auth/login', [])->assertNotFound();
});
