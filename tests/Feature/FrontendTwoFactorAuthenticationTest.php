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
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    RateLimiter::clear('front-2fa-enable:'.$this->user->getKey());
});

function sendTwoFactorOtpFor(User $user): string
{
    test()->actingAs($user)->post(route('front.account.two-factor-authentication.send'), ['method' => 'email'])
        ->assertRedirect(route('front.account.two-factor-authentication.verify'));
    $mail = Mail::sent(TwoFactorOtpMailToUser::class)->last();

    return $mail->otp;
}

test('guest inactive and non frontend accounts are blocked', function (string $kind) {
    if ($kind === 'guest') {
        $this->get(route('front.account.two-factor-authentication'))->assertRedirect(route('front.login'));

        return;
    }
    if ($kind === 'inactive') {
        $this->user->update(['is_active' => false]);
        $this->actingAs($this->user)->get(route('front.account.two-factor-authentication'))->assertRedirect(route('front.login'));
        $this->assertGuest('web');

        return;
    }
    $this->user->syncRoles(Role::findOrCreate('super-admin', 'web'));
    $this->actingAs($this->user)->get(route('front.account.two-factor-authentication'))->assertForbidden();
})->with(['guest', 'inactive', 'admin']);

test('settings page creates missing disabled settings and activates sidebar', function () {
    $this->actingAs($this->user)->get(route('front.account.two-factor-authentication'))
        ->assertOk()->assertSee('aria-current="page"', false)->assertSee('غیر فعال');
    $this->assertDatabaseHas('user_two_factor_settings', [
        'user_id' => $this->user->id, 'method' => null, 'is_enabled' => false, 'verified_at' => null,
    ]);
});

test('email otp challenge stores only hash and sends direct html mail', function () {
    $otp = sendTwoFactorOtpFor($this->user);
    $challenge = UserTwoFactorChallenge::sole();

    expect($otp)->toMatch('/^\d{6}$/')
        ->and($challenge->otp_hash)->not->toBe($otp)
        ->and(Hash::check($otp, $challenge->otp_hash))->toBeTrue()
        ->and($challenge->purpose)->toBe('enable')
        ->and($challenge->channel)->toBe('email')
        ->and($challenge->expires_at->diffInMinutes(now()))->toBeLessThanOrEqual(10)
        ->and($challenge->attempts)->toBe(0)
        ->and($challenge->consumed_at)->toBeNull();
    Mail::assertSent(TwoFactorOtpMailToUser::class, fn ($mail) => $mail->hasTo($this->user->email)
        && $mail->content()->view === 'frontend.mails.two-factor-otp-mail-to-user');
});

test('phone option is visible but unavailable', function () {
    $this->actingAs($this->user)->get(route('front.account.two-factor-authentication'))
        ->assertSee('value="phone" disabled', false)->assertSee('جلد دستیاب ہوگا');
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.send'), ['method' => 'phone'])
        ->assertSessionHasErrors('method');
    Mail::assertNothingSent();
});

test('valid otp enables email two factor once', function () {
    $otp = sendTwoFactorOtpFor($this->user);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => $otp])
        ->assertRedirect(route('front.account.two-factor-authentication'))->assertSessionHas('success');

    $setting = $this->user->twoFactorSetting;
    expect($setting->method)->toBe('email')->and($setting->is_enabled)->toBeTrue()
        ->and($setting->verified_at)->not->toBeNull()
        ->and(UserTwoFactorChallenge::sole()->consumed_at)->not->toBeNull();
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => $otp])
        ->assertSessionHasErrors('otp');
});

test('invalid otp increments attempts and maximum is enforced', function () {
    sendTwoFactorOtpFor($this->user);
    foreach (range(1, 5) as $attempt) {
        $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => '999999'])
            ->assertSessionHasErrors('otp');
    }
    expect(UserTwoFactorChallenge::sole()->attempts)->toBe(5);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => '999999'])
        ->assertSessionHasErrors(['otp' => 'تصدیقی کوششوں کی حد مکمل ہو چکی ہے۔ نیا کوڈ حاصل کریں۔']);
});

test('expired otp cannot enable two factor', function () {
    $otp = sendTwoFactorOtpFor($this->user);
    UserTwoFactorChallenge::sole()->update(['expires_at' => now()->subSecond()]);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => $otp])
        ->assertSessionHasErrors('otp');
    expect($this->user->twoFactorSetting->is_enabled)->toBeFalse();
});

test('resend cooldown applies and resend invalidates previous otp', function () {
    $oldOtp = sendTwoFactorOtpFor($this->user);
    $oldChallenge = UserTwoFactorChallenge::sole();
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.send'), ['method' => 'email'])
        ->assertSessionHasErrors('method');
    Mail::assertSentCount(1);

    $this->travel(61)->seconds();
    $newOtp = sendTwoFactorOtpFor($this->user);
    expect($oldChallenge->fresh()->consumed_at)->not->toBeNull()
        ->and($newOtp)->not->toBe($oldOtp)
        ->and(UserTwoFactorChallenge::count())->toBe(2);
    $this->actingAs($this->user)->post(route('front.account.two-factor-authentication.verify.store'), ['otp' => $oldOtp])
        ->assertSessionHasErrors('otp');
});
