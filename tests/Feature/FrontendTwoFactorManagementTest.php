<?php

use App\Mail\TwoFactorOtpMailToUser;
use App\Models\User;
use App\Models\UserTwoFactorChallenge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['password' => 'password123', 'is_active' => true]);
    $this->user->assignRole('user');
    $this->setting = $this->user->twoFactorSetting()->create([
        'method' => 'email', 'is_enabled' => true, 'verified_at' => now(),
    ]);
    RateLimiter::clear('front-2fa-disable:'.$this->user->id);
    RateLimiter::clear('front-2fa-change_method:'.$this->user->id);
});

function latestManagementOtp(): string
{
    return Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
}

test('guest and non frontend accounts cannot use disable or change actions', function (string $kind) {
    if ($kind === 'admin') {
        $this->user->syncRoles(Role::findOrCreate('super-admin', 'web'));
        $this->actingAs($this->user);
    }
    $this->post(route('front.account.two-factor-authentication.disable.send'), ['current_password' => 'password123'])
        ->assertStatus($kind === 'admin' ? 403 : 302);
    $this->post(route('front.account.two-factor-authentication.change-method.send'), ['method' => 'email'])
        ->assertStatus($kind === 'admin' ? 403 : 302);
    Mail::assertNothingSent();
})->with(['guest', 'admin']);

test('enabled settings page shows method verified date and management actions', function () {
    $this->actingAs($this->user)->get(route('front.account.two-factor-authentication'))
        ->assertOk()->assertSee('موجودہ طریقہ: ای میل')
        ->assertSee(route('front.account.two-factor-authentication.disable.send'), false)
        ->assertSee(route('front.account.two-factor-authentication.change-method.send'), false);
});

test('disabled user and wrong current password cannot start disable flow', function (string $case) {
    if ($case === 'disabled') {
        $this->setting->update(['is_enabled' => false, 'method' => null, 'verified_at' => null]);
    }
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.disable.send'), [
        'current_password' => $case === 'wrong' ? 'incorrect' : 'password123',
    ])->assertSessionHasErrors('current_password');
    $this->assertDatabaseCount('user_two_factor_challenges', 0);
    Mail::assertNothingSent();
})->with(['disabled', 'wrong']);

test('correct password creates hashed disable challenge and purpose aware mail', function () {
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.disable.send'), [
        'current_password' => 'password123',
    ])->assertRedirect(route('front.account.two-factor-authentication.disable.challenge'));
    $otp = latestManagementOtp();
    $challenge = UserTwoFactorChallenge::sole();
    expect($challenge->purpose)->toBe('disable')->and($challenge->channel)->toBe('email')
        ->and($challenge->otp_hash)->not->toBe($otp)->and(Hash::check($otp, $challenge->otp_hash))->toBeTrue();
    Mail::assertSent(TwoFactorOtpMailToUser::class, fn ($mail) => $mail->purpose === 'disable');
});

test('disable invalid otp increments attempts and enforces maximum', function () {
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.disable.send'), ['current_password' => 'password123']);
    foreach (range(1, 5) as $attempt) {
        $this->post(route('front.account.two-factor-authentication.disable.verify'), ['otp' => '999999'])
            ->assertSessionHasErrors('otp');
    }
    expect(UserTwoFactorChallenge::sole()->attempts)->toBe(5);
    $this->post(route('front.account.two-factor-authentication.disable.verify'), ['otp' => '999999'])
        ->assertSessionHasErrors(['otp' => 'تصدیقی کوششوں کی حد مکمل ہو چکی ہے۔ نیا کوڈ حاصل کریں۔']);
});

test('expired and consumed disable otp cannot change settings', function (string $state) {
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.disable.send'), ['current_password' => 'password123']);
    $otp = latestManagementOtp();
    UserTwoFactorChallenge::sole()->update([
        $state === 'expired' ? 'expires_at' : 'consumed_at' => $state === 'expired' ? now()->subSecond() : now(),
    ]);
    $this->post(route('front.account.two-factor-authentication.disable.verify'), ['otp' => $otp])->assertSessionHasErrors('otp');
    expect($this->setting->fresh()->is_enabled)->toBeTrue()->and($this->setting->fresh()->method)->toBe('email');
})->with(['expired', 'consumed']);

test('valid disable otp clears settings keeps session and next login skips otp', function () {
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.disable.send'), ['current_password' => 'password123']);
    $this->post(route('front.account.two-factor-authentication.disable.verify'), ['otp' => latestManagementOtp()])
        ->assertRedirect(route('front.account.two-factor-authentication'))->assertSessionHas('success');
    $this->assertAuthenticatedAs($this->user, 'web');
    expect($this->setting->fresh()->is_enabled)->toBeFalse()
        ->and($this->setting->fresh()->method)->toBeNull()
        ->and($this->setting->fresh()->verified_at)->toBeNull()
        ->and(UserTwoFactorChallenge::sole()->consumed_at)->not->toBeNull();

    auth('web')->logout();
    Mail::fake();
    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'password123'])
        ->assertRedirect(route('front.account'));
    $this->assertAuthenticatedAs($this->user, 'web');
    Mail::assertNothingSent();
});

test('same method and unavailable phone are rejected without state or delivery', function (string $method) {
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.change-method.send'), ['method' => $method])
        ->assertSessionHasErrors('method');
    expect($this->setting->fresh()->method)->toBe('email')->and($this->setting->fresh()->is_enabled)->toBeTrue();
    $this->assertDatabaseCount('user_two_factor_challenges', 0);
    Mail::assertNothingSent();
})->with(['email', 'phone']);

test('email change challenge leaves old method until valid verification', function () {
    $this->setting->update(['method' => 'phone']);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.change-method.send'), ['method' => 'email'])
        ->assertRedirect(route('front.account.two-factor-authentication.change-method.challenge'));
    $otp = latestManagementOtp();
    $challenge = UserTwoFactorChallenge::sole();
    expect($this->setting->fresh()->method)->toBe('phone')
        ->and($challenge->purpose)->toBe('change_method')->and($challenge->channel)->toBe('email')
        ->and(Hash::check($otp, $challenge->otp_hash))->toBeTrue();
    Mail::assertSent(TwoFactorOtpMailToUser::class, fn ($mail) => $mail->purpose === 'change_method');

    $this->post(route('front.account.two-factor-authentication.change-method.verify'), ['otp' => $otp])
        ->assertRedirect(route('front.account.two-factor-authentication'))->assertSessionHas('success');
    expect($this->setting->fresh()->method)->toBe('email')
        ->and($this->setting->fresh()->is_enabled)->toBeTrue()
        ->and($this->setting->fresh()->verified_at)->not->toBeNull();
    $this->assertAuthenticatedAs($this->user, 'web');
});

test('failed or expired change otp preserves current active method', function (string $case) {
    $this->setting->update(['method' => 'phone']);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.change-method.send'), ['method' => 'email']);
    $otp = latestManagementOtp();
    if ($case === 'expired') {
        UserTwoFactorChallenge::sole()->update(['expires_at' => now()->subSecond()]);
    } else {
        $otp = '999999';
    }
    $this->post(route('front.account.two-factor-authentication.change-method.verify'), ['otp' => $otp])
        ->assertSessionHasErrors('otp');
    expect($this->setting->fresh()->method)->toBe('phone')->and($this->setting->fresh()->is_enabled)->toBeTrue();
})->with(['invalid', 'expired']);

test('management resend cooldown and old challenge invalidation work', function (string $purpose) {
    if ($purpose === 'change_method') {
        $this->setting->update(['method' => 'phone']);
        $sendRoute = route('front.account.two-factor-authentication.change-method.send');
        $resendRoute = route('front.account.two-factor-authentication.change-method.resend');
        $payload = ['method' => 'email'];
    } else {
        $sendRoute = route('front.account.two-factor-authentication.disable.send');
        $resendRoute = route('front.account.two-factor-authentication.disable.resend');
        $payload = ['current_password' => 'password123'];
    }
    $this->actingAs($this->user)->post($sendRoute, $payload);
    $oldOtp = latestManagementOtp();
    $oldChallenge = UserTwoFactorChallenge::sole();
    $this->post($resendRoute)->assertSessionHasErrors('otp');
    $this->travel(61)->seconds();
    $this->post($resendRoute)->assertSessionHas('success');
    $newOtp = latestManagementOtp();
    expect($oldChallenge->fresh()->consumed_at)->not->toBeNull()->and($newOtp)->not->toBe($oldOtp);
})->with(['disable', 'change_method']);
