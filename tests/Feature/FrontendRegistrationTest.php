<?php

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Mail\AccountActivationLinkMailToUser;
use App\Mail\NewUserRegisterMailToAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    config(['mail.admin_address' => 'cogentdevs@gmail.com']);
    Role::findOrCreate('user', 'web');
});

function registrationPayload(): array
{
    return [
        'first_name' => 'Ali',
        'last_name' => 'Khan',
        'email' => 'ali@example.com',
        'phone' => '03001234567',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'g-recaptcha-response' => 'google-token',
    ];
}

test('registration creates only an inactive frontend user and sends both mails', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->with('google-token', '127.0.0.1')->andReturn(true);

    $this->post('/register', registrationPayload() + ['is_active' => true, 'role' => 'super-admin'])
        ->assertRedirect(route('front.register'))->assertSessionHas('success');

    $user = User::where('email', 'ali@example.com')->firstOrFail();
    expect($user->name)->toBe('Ali Khan')
        ->and($user->phone)->toBe('03001234567')
        ->and($user->is_active)->toBeFalse()
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->profile_image)->toBe('user-avatar.png')
        ->and($user->getRoleNames()->all())->toBe(['user'])
        ->and(Hash::check('password123', $user->password))->toBeTrue()
        ->and($user->toArray())->not->toHaveKey('activation_token')
        ->and($user->activation_token_expires_at->isFuture())->toBeTrue();
    expect($user->twoFactorSetting)->not->toBeNull()
        ->and($user->twoFactorSetting->method)->toBeNull()
        ->and($user->twoFactorSetting->is_enabled)->toBeFalse()
        ->and($user->twoFactorSetting->verified_at)->toBeNull();
    $this->assertGuest();
    Mail::assertSent(NewUserRegisterMailToAdmin::class, fn ($mail) => $mail->hasTo('cogentdevs@gmail.com'));
    Mail::assertSent(AccountActivationLinkMailToUser::class, function ($mail) use ($user) {
        parse_str(parse_url($mail->activationUrl, PHP_URL_QUERY), $query);

        return $mail->hasTo($user->email) && hash('sha256', $query['token']) === $user->activation_token;
    });
    Mail::assertSentCount(2);
    $activationMail = Mail::sent(AccountActivationLinkMailToUser::class)->first();
    $this->get($activationMail->activationUrl)->assertOk()->assertViewHas('state', 'pending');
});

test('registration rejects invalid input without creating users or mail', function (string $field, mixed $value, string $error) {
    NoCaptcha::shouldReceive('verifyResponse')->zeroOrMoreTimes()->andReturn(true);
    $payload = array_replace(registrationPayload(), [$field => $value]);
    $this->post('/register', $payload)->assertSessionHasErrors($error);
    $this->assertDatabaseCount('users', 0);
    Mail::assertNothingOutgoing();
})->with([
    ['first_name', '', 'first_name'],
    ['last_name', '', 'last_name'],
    ['email', 'invalid', 'email'],
    ['phone', '', 'phone'],
    ['password', 'short', 'password'],
    ['password_confirmation', 'mismatch', 'password'],
    ['g-recaptcha-response', '', 'g-recaptcha-response'],
]);

test('registration rejects duplicate email', function () {
    User::factory()->create(['email' => 'ali@example.com']);
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    $this->post('/register', registrationPayload())->assertSessionHasErrors('email');
    $this->assertDatabaseCount('users', 1);
    Mail::assertNothingOutgoing();
});

test('registration rejects a token Google does not verify', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(false);
    $this->post('/register', registrationPayload())->assertSessionHasErrors('g-recaptcha-response');
    $this->assertDatabaseCount('users', 0);
    Mail::assertNothingOutgoing();
});

test('registration page renders the real widget and disabled submit', function () {
    config(['captcha.sitekey' => 'test-site-key', 'captcha.secret' => 'test-secret']);
    $this->get('/register')->assertOk()
        ->assertSee('data-front-register-form', false)
        ->assertSee('g-recaptcha', false)
        ->assertSee('test-site-key', false)
        ->assertSee('type="submit" disabled', false)
        ->assertDontSee('Google reCAPTCHA placeholder');
});

test('registration mails render user details and activation link without credentials', function () {
    $user = User::factory()->make(['name' => 'Ali Khan', 'phone' => '03001234567']);
    (new NewUserRegisterMailToAdmin($user))->assertSeeInHtml('Ali Khan')->assertSeeInHtml('03001234567');
    (new AccountActivationLinkMailToUser($user, 'https://example.com/account/activate?token=test'))
        ->assertSeeInHtml('https://example.com/account/activate?token=test')
        ->assertDontSeeInHtml($user->password);
});
