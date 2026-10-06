<?php

use App\Mail\TwoFactorOtpMailToUser;
use App\Models\User;
use App\Models\UserTwoFactorChallenge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create([
        'email' => 'secure-reader@example.com',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->user->assignRole('user');
    $this->user->twoFactorSetting()->create(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    RateLimiter::clear('front-2fa-login:'.$this->user->id);
});

function beginTwoFactorLogin(User $user, bool $remember = false): string
{
    test()->post(route('front.login.store'), [
        'email' => $user->email,
        'password' => 'password123',
        'remember' => $remember,
    ])->assertRedirect(route('front.two-factor.challenge'));

    return Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
}

test('disabled two factor user keeps normal login behavior', function () {
    $this->user->twoFactorSetting->update(['is_enabled' => false, 'method' => null]);
    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'password123'])
        ->assertRedirect(route('front.account'));
    $this->assertAuthenticatedAs($this->user, 'web');
    Mail::assertNothingSent();
});

test('enabled user remains guest with minimal pending state and hashed login challenge', function () {
    $otp = beginTwoFactorLogin($this->user, true);
    $this->assertGuest('web');
    $this->assertDatabaseHas('user_two_factor_challenges', [
        'user_id' => $this->user->id, 'purpose' => 'login', 'channel' => 'email', 'attempts' => 0,
    ]);
    $challenge = UserTwoFactorChallenge::sole();
    expect(Hash::check($otp, $challenge->otp_hash))->toBeTrue()
        ->and($challenge->otp_hash)->not->toBe($otp)
        ->and(session('front_two_factor_login'))->toMatchArray([
            'user_id' => $this->user->id, 'remember' => true, 'method' => 'email',
        ])
        ->and(session('front_two_factor_login'))->not->toHaveKeys(['password', 'otp']);
    Mail::assertSent(TwoFactorOtpMailToUser::class, fn ($mail) => $mail->purpose === 'login');
});

test('challenge page requires pending session and masks email', function () {
    $this->get(route('front.two-factor.challenge'))->assertRedirect(route('front.login'));
    beginTwoFactorLogin($this->user);
    $this->get(route('front.two-factor.challenge'))->assertOk()
        ->assertSee('se***********@example.com')->assertDontSee($this->user->email);
});

test('valid otp completes login regenerates session clears pending and preserves remember choice', function (bool $remember) {
    $otp = beginTwoFactorLogin($this->user, $remember);
    $sessionId = session()->getId();
    $response = $this->post(route('front.two-factor.verify'), ['otp' => $otp])
        ->assertRedirect(route('front.account'));

    $this->assertAuthenticatedAs($this->user, 'web');
    expect(session()->getId())->not->toBe($sessionId)
        ->and(session()->has('front_two_factor_login'))->toBeFalse()
        ->and(UserTwoFactorChallenge::sole()->consumed_at)->not->toBeNull();
    $cookieName = Auth::guard('web')->getRecallerName();
    $remember ? $response->assertCookie($cookieName) : $response->assertCookieMissing($cookieName);
})->with([false, true]);

test('invalid otp increments attempts and five attempt limit is enforced', function () {
    beginTwoFactorLogin($this->user);
    foreach (range(1, 5) as $attempt) {
        $this->post(route('front.two-factor.verify'), ['otp' => '999999'])->assertSessionHasErrors('otp');
    }
    expect(UserTwoFactorChallenge::sole()->attempts)->toBe(5);
    $this->post(route('front.two-factor.verify'), ['otp' => '999999'])
        ->assertRedirect(route('front.login'))->assertSessionHasErrors('email');
    $this->assertGuest('web');
    expect(session()->has('front_two_factor_login'))->toBeFalse();
});

test('expired and consumed login challenges cannot authenticate', function (string $state) {
    $otp = beginTwoFactorLogin($this->user);
    UserTwoFactorChallenge::sole()->update([
        $state === 'expired' ? 'expires_at' : 'consumed_at' => $state === 'expired' ? now()->subSecond() : now(),
    ]);
    $this->post(route('front.two-factor.verify'), ['otp' => $otp])->assertRedirect(route('front.login'));
    $this->assertGuest('web');
    expect(session()->has('front_two_factor_login'))->toBeFalse();
})->with(['expired', 'consumed']);

test('resend cooldown applies and old otp is invalidated', function () {
    $oldOtp = beginTwoFactorLogin($this->user);
    $oldChallenge = UserTwoFactorChallenge::sole();
    $this->post(route('front.two-factor.resend'))->assertSessionHasErrors('otp');
    Mail::assertSentCount(1);

    $this->travel(61)->seconds();
    $this->post(route('front.two-factor.resend'))->assertSessionHas('success');
    $newOtp = Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
    expect($oldChallenge->fresh()->consumed_at)->not->toBeNull()
        ->and($newOtp)->not->toBe($oldOtp)
        ->and(UserTwoFactorChallenge::count())->toBe(2);
    $this->post(route('front.two-factor.verify'), ['otp' => $oldOtp])->assertSessionHasErrors('otp');
    $this->assertGuest('web');
});

test('pending user security changes cancel login safely', function (string $change) {
    beginTwoFactorLogin($this->user);
    if ($change === 'inactive') {
        $this->user->update(['is_active' => false]);
    } elseif ($change === 'role') {
        $this->user->syncRoles([]);
    } elseif ($change === 'disabled') {
        $this->user->twoFactorSetting->update(['is_enabled' => false]);
    } elseif ($change === 'method') {
        $this->user->twoFactorSetting->update(['method' => 'phone']);
    } else {
        $this->user->delete();
    }
    $this->post(route('front.two-factor.verify'), ['otp' => '123456'])
        ->assertRedirect(route('front.login'))->assertSessionHasErrors('email');
    $this->assertGuest('web');
})->with(['inactive', 'role', 'disabled', 'method', 'deleted']);

test('valid frontend intended destination survives two factor login', function () {
    $this->withSession(['url.intended' => route('front.taaruf')]);
    $otp = beginTwoFactorLogin($this->user);
    $this->post(route('front.two-factor.verify'), ['otp' => $otp])->assertRedirect(route('front.taaruf'));
});

test('homepage login still requires two factor and returns home only after verification', function () {
    $this->post(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
        'login_source' => 'homepage',
    ])->assertRedirect(route('front.two-factor.challenge'));

    $this->assertGuest('web');
    expect(session('front_two_factor_login.intended'))->toBe(route('frontend.home'));

    $otp = Mail::sent(TwoFactorOtpMailToUser::class)->last()->otp;
    $this->post(route('front.two-factor.verify'), ['otp' => $otp])
        ->assertRedirect(route('frontend.home'));
    $this->assertAuthenticatedAs($this->user, 'web');
});

test('phone configured two factor fails safely without authentication or mail', function () {
    $this->user->twoFactorSetting->update(['method' => 'phone']);
    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'password123'])
        ->assertSessionHasErrors('email');
    $this->assertGuest('web');
    Mail::assertNothingSent();
});
