<?php

use App\Mail\TwoFactorOtpMailToUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
});

function accountApiUser(array $attributes = []): User
{
    $user = User::factory()->create(array_replace(['name' => 'Account User', 'email' => 'account@example.com', 'phone' => '03001234567', 'password' => 'Password123!', 'profile_image' => 'user-avatar.png', 'is_active' => true], $attributes));
    $user->assignRole('user');
    $user->twoFactorSetting()->create(['method' => null, 'is_enabled' => false, 'verified_at' => null]);

    return $user;
}

test('every account endpoint requires Sanctum authentication', function (string $method, string $uri) {
    $this->json($method, $uri)->assertUnauthorized();
})->with([['GET', '/api/me'], ['PATCH', '/api/me'], ['DELETE', '/api/me/profile-image'], ['PATCH', '/api/me/password'], ['GET', '/api/me/2fa'], ['POST', '/api/me/2fa/challenges'], ['POST', '/api/me/2fa/challenges/verify'], ['POST', '/api/me/2fa/challenges/resend']]);

test('profile returns safe current user data and updates only website editable fields', function () {
    $user = accountApiUser();
    $other = accountApiUser(['email' => 'other@example.com']);
    Sanctum::actingAs($user);
    $this->getJson('/api/me')->assertOk()->assertJsonPath('data.user.phone', '03001234567')->assertJsonMissingPath('data.user.password');
    $this->patchJson('/api/me', ['name' => 'Updated Name', 'email' => 'updated@example.com', 'phone' => '03123456789', 'is_active' => false, 'user_id' => $other->id])
        ->assertOk()->assertJsonPath('data.user.name', 'Updated Name')->assertJsonPath('data.user.phone', '03123456789');
    $this->getJson('/api/me')->assertOk()->assertJsonPath('data.user.phone', '03123456789');
    expect($user->fresh()->is_active)->toBeTrue()->and($other->fresh()->name)->toBe('Account User');
    $this->patchJson('/api/me', ['name' => '', 'email' => 'bad', 'phone' => ''])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'phone']);
});

test('profile image upload replacement removal and validation follow website rules', function () {
    $user = accountApiUser();
    Sanctum::actingAs($user);
    $base = ['_method' => 'PATCH', 'name' => 'Image User', 'email' => $user->email, 'phone' => '03211234567'];
    $this->post('/api/me', $base + ['profile_image' => UploadedFile::fake()->image('avatar.png')], ['Accept' => 'application/json'])->assertOk();
    $first = $user->fresh()->profile_image;
    expect(File::exists(public_path('images/frontend-images/users/'.$first)))->toBeTrue()
        ->and($user->fresh()->name)->toBe('Image User')->and($user->fresh()->phone)->toBe('03211234567');
    $this->post('/api/me', $base + ['profile_image' => UploadedFile::fake()->image('new.webp')], ['Accept' => 'application/json'])->assertOk();
    $second = $user->fresh()->profile_image;
    expect(File::exists(public_path('images/frontend-images/users/'.$first)))->toBeFalse();
    $this->deleteJson('/api/me/profile-image')->assertOk();
    expect($user->fresh()->profile_image)->toBe('user-avatar.png')->and(File::exists(public_path('images/frontend-images/users/'.$second)))->toBeFalse();
    $this->post('/api/me', $base + ['profile_image' => UploadedFile::fake()->create('bad.pdf', 10)], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('profile_image');
    $this->post('/api/me', $base + ['profile_image' => UploadedFile::fake()->image('large.jpg')->size(2049)], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('profile_image');
});

test('password change verifies current password hashes replacement and keeps current token', function () {
    $user = accountApiUser();
    $token = $user->createToken('mobile-app')->plainTextToken;
    $this->withToken($token)->patchJson('/api/me/password', ['current_password' => 'wrong', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertUnprocessable();
    $this->withToken($token)->patchJson('/api/me/password', ['current_password' => 'Password123!', 'password' => 'short', 'password_confirmation' => 'different'])->assertUnprocessable();
    $this->withToken($token)->patchJson('/api/me/password', ['current_password' => 'Password123!', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertOk()->assertJsonMissing(['password']);
    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue()->and(Hash::check('Password123!', $user->fresh()->password))->toBeFalse();
    $this->app['auth']->forgetGuards();
    $this->withToken($token)->getJson('/api/me')->assertOk();
});

test('two factor state is safe and enable requires valid emailed otp', function () {
    $user = accountApiUser();
    Sanctum::actingAs($user);
    $this->getJson('/api/me/2fa')->assertOk()->assertJsonPath('data.two_factor.enabled', false)->assertJsonMissingPath('data.two_factor.otp_hash');
    $start = $this->postJson('/api/me/2fa/challenges', ['action' => 'enable'])->assertOk()->assertJsonMissingPath('data.otp');
    $challenge = $start->json('data.challenge_id');
    $otp = Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
    expect($user->twoFactorSetting->fresh()->is_enabled)->toBeFalse();
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => '999999'])->assertUnprocessable();
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => $otp])->assertOk()->assertJsonPath('data.two_factor.enabled', true);
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => $otp])->assertUnprocessable();
});

test('two factor challenge is user bound and expiry is enforced', function () {
    $user = accountApiUser();
    Sanctum::actingAs($user);
    $challenge = $this->postJson('/api/me/2fa/challenges', ['action' => 'enable'])->json('data.challenge_id');
    $other = accountApiUser(['email' => 'other@example.com']);
    Sanctum::actingAs($other);
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => '123456'])->assertUnprocessable();
    Sanctum::actingAs($user);
    $user->twoFactorChallenges()->update(['expires_at' => now()->subSecond()]);
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => '123456'])->assertUnprocessable();
});

test('two factor maximum attempts and current state protections are enforced', function () {
    $user = accountApiUser();
    Sanctum::actingAs($user);
    $challenge = $this->postJson('/api/me/2fa/challenges', ['action' => 'enable'])->json('data.challenge_id');
    $user->twoFactorChallenges()->update(['attempts' => 5]);
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $challenge, 'otp' => '123456'])->assertUnprocessable();
    $this->postJson('/api/me/2fa/challenges/resend', ['challenge_id' => $challenge])->assertUnprocessable();

    $user->twoFactorSetting->update(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    $this->travel(61)->seconds();
    $this->postJson('/api/me/2fa/challenges', ['action' => 'enable'])->assertUnprocessable();
    $user->twoFactorSetting->update(['method' => null, 'is_enabled' => false, 'verified_at' => null]);
    $this->postJson('/api/me/2fa/challenges', ['action' => 'disable', 'current_password' => 'Password123!'])->assertUnprocessable();
});

test('disable requires current password and otp before changing state', function () {
    $user = accountApiUser();
    $user->twoFactorSetting->update(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    Sanctum::actingAs($user);
    $this->postJson('/api/me/2fa/challenges', ['action' => 'disable'])->assertUnprocessable()->assertJsonValidationErrors('current_password');
    $start = $this->postJson('/api/me/2fa/challenges', ['action' => 'disable', 'current_password' => 'Password123!'])->assertOk();
    expect($user->twoFactorSetting->fresh()->is_enabled)->toBeTrue();
    $otp = Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $start->json('data.challenge_id'), 'otp' => $otp])->assertOk()->assertJsonPath('data.two_factor.enabled', false);
});

test('two factor validates actions and resend replaces challenge after cooldown', function () {
    $user = accountApiUser();
    Sanctum::actingAs($user);
    $this->postJson('/api/me/2fa/challenges', ['action' => 'change'])->assertUnprocessable()->assertJsonValidationErrors('action');
    $old = $this->postJson('/api/me/2fa/challenges', ['action' => 'enable'])->json('data.challenge_id');
    $this->postJson('/api/me/2fa/challenges/resend', ['challenge_id' => $old])->assertTooManyRequests();
    $this->travel(61)->seconds();
    $new = $this->postJson('/api/me/2fa/challenges/resend', ['challenge_id' => $old])->assertOk()->json('data.challenge_id');
    expect($new)->not->toBe($old);
    $this->postJson('/api/me/2fa/challenges/verify', ['challenge_id' => $old, 'otp' => '123456'])->assertUnprocessable();
});

test('account routes are unversioned and read-only account modules are available', function () {
    Sanctum::actingAs(accountApiUser());
    $this->getJson('/api/v1/me')->assertNotFound();
    $this->getJson('/api/api/me')->assertNotFound();
    $this->getJson('/api/me/subscriptions')->assertOk();
    $this->getJson('/api/me/bookmarks')->assertOk();
});
