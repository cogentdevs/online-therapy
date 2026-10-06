<?php

use App\Mail\ForgotPasswordLinkMailToUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('reader@example.com|127.0.0.1');
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['email' => 'reader@example.com', 'password' => 'old-password', 'is_active' => true]);
    $this->user->assignRole('user');
});

test('forgot page and login link use frontend routes', function () {
    $this->get(route('front.password.request'))->assertOk()->assertSee('action="'.route('front.password.email').'"', false);
    $this->get(route('front.login'))->assertOk()->assertSee('href="'.route('front.password.request').'"', false);
});

test('forgot validates email and throttles repeated submissions', function () {
    $this->post(route('front.password.email'), ['email' => 'bad'])->assertSessionHasErrors('email');
    Mail::fake();
    foreach (range(1, 5) as $attempt) {
        $this->post(route('front.password.email'), ['email' => $this->user->email])->assertSessionHasNoErrors();
    }
    $this->post(route('front.password.email'), ['email' => $this->user->email])->assertSessionHasErrors('email');
});

test('frontend user receives direct custom reset mail with frontend url', function () {
    Mail::fake();
    $this->post(route('front.password.email'), ['email' => $this->user->email])->assertRedirect(route('front.password.request'))->assertSessionHas('success');
    Mail::assertSent(ForgotPasswordLinkMailToUser::class, fn ($mail) => $mail->hasTo($this->user->email)
        && str_contains($mail->resetUrl, '/reset-password/')
        && str_contains($mail->resetUrl, 'email='.urlencode($this->user->email))
        && $mail->expiresInMinutes === (int) config('auth.passwords.users.expire'));
});

test('unknown and non frontend accounts get same response without mail', function (string $kind) {
    Mail::fake();
    $email = 'unknown@example.com';
    if ($kind === 'admin') {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
        $email = $admin->email;
    }
    $this->post(route('front.password.email'), ['email' => $email])->assertRedirect(route('front.password.request'))->assertSessionHas('success');
    Mail::assertNothingSent();
})->with(['unknown', 'admin']);

test('valid token and email display reset form', function () {
    $token = Password::broker('users')->createToken($this->user);
    $this->get(route('front.password.reset', ['token' => $token, 'email' => $this->user->email]))->assertOk()->assertSee('data-front-reset-form', false);
});

test('reset validates minimum password and confirmation', function (array $changes) {
    $token = Password::broker('users')->createToken($this->user);
    $data = ['token' => $token, 'email' => $this->user->email, 'password' => 'new-password', 'password_confirmation' => 'new-password'];
    $this->post(route('front.password.update'), array_replace($data, $changes))->assertSessionHasErrors('password');
})->with([[['password' => 'short', 'password_confirmation' => 'short']], [['password_confirmation' => 'different']]]);

test('valid reset changes password consumes token redirects without login and new credentials work', function () {
    $token = Password::broker('users')->createToken($this->user);
    $data = ['token' => $token, 'email' => $this->user->email, 'password' => 'new-password', 'password_confirmation' => 'new-password'];
    $this->post(route('front.password.update'), $data)->assertRedirect(route('front.login'))->assertSessionHas('success');
    $this->assertGuest('web');
    expect(Hash::check('old-password', $this->user->fresh()->password))->toBeFalse()->and(Hash::check('new-password', $this->user->fresh()->password))->toBeTrue();
    $this->post(route('front.password.update'), $data)->assertSessionHasErrors('email');
    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'new-password'])->assertRedirect(route('front.account'));
});

test('wrong email expired token and non user token cannot reset', function (string $case) {
    $token = Password::broker('users')->createToken($this->user);
    $email = $this->user->email;
    if ($case === 'wrong-email') {
        $email = 'someone@example.com';
    } elseif ($case === 'expired') {
        DB::table('password_reset_tokens')->where('email', $email)->update(['created_at' => now()->subMinutes(61)]);
    } else {
        $this->user->syncRoles(Role::findOrCreate('super-admin', 'web'));
    }
    $data = ['token' => $token, 'email' => $email, 'password' => 'new-password', 'password_confirmation' => 'new-password'];
    $this->post(route('front.password.update'), $data)->assertSessionHasErrors('email');
    expect(Hash::check('old-password', $this->user->fresh()->password))->toBeTrue();
})->with(['wrong-email', 'expired', 'non-user']);
