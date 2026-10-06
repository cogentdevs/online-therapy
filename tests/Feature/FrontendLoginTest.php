<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->frontendUser = User::factory()->create(['email' => 'reader@example.com', 'password' => 'password123', 'is_active' => true]);
    $this->frontendUser->assignRole('user');
    $this->credentials = ['email' => $this->frontendUser->email, 'password' => 'password123'];
});

test('homepage guest sees the existing frontend login form and account recovery links', function () {
    $this->get(route('frontend.home'))
        ->assertOk()
        ->assertSee('action="'.route('front.login.store').'"', false)
        ->assertSee('name="login_source" value="homepage"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="remember"', false)
        ->assertSee('data-front-password-toggle', false)
        ->assertSee('aria-controls="front-login-password"', false)
        ->assertSee('href="'.route('front.register').'"', false)
        ->assertSee('href="'.route('front.password.request').'"', false);
});

test('normal homepage login authenticates remembers the user and returns home', function () {
    $response = $this->post(route('front.login.store'), $this->credentials + [
        'remember' => true,
        'login_source' => 'homepage',
    ]);

    $response->assertRedirect(route('frontend.home'))
        ->assertCookie(Auth::guard('web')->getRecallerName());
    $this->assertAuthenticatedAs($this->frontendUser, 'web');
});

test('homepage login source uses home instead of a normal intended destination', function () {
    $this->withSession(['url.intended' => route('front.taaruf')])
        ->post(route('front.login.store'), $this->credentials + ['login_source' => 'homepage'])
        ->assertRedirect(route('frontend.home'));
});

test('invalid homepage credentials return an error without exposing the password', function () {
    $this->from(route('frontend.home'))->post(route('front.login.store'), [
        'email' => $this->frontendUser->email,
        'password' => 'wrong-password',
        'login_source' => 'homepage',
    ])->assertRedirect(route('frontend.home'))
        ->assertSessionHasErrors('email')
        ->assertSessionHasInput('email', $this->frontendUser->email)
        ->assertSessionMissing('_old_input.password');

    $this->assertGuest('web');
});

test('authenticated user does not see an active homepage login form', function () {
    $this->actingAs($this->frontendUser)->get(route('frontend.home'))
        ->assertOk()
        ->assertDontSee('name="login_source" value="homepage"', false)
        ->assertDontSee('action="'.route('front.login.store').'"', false)
        ->assertSee('href="'.route('front.account').'"', false);
});

test('guest login renders its action controls and activation flash', function () {
    $this->withSession(['success' => 'آپ کا اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا ہے۔'])
        ->get(route('front.login'))->assertOk()
        ->assertSee('آپ کا اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا ہے۔')
        ->assertSee('action="'.route('front.login.store').'"', false)
        ->assertSee('data-front-password-toggle', false)
        ->assertDontSee('data-front-auth-form', false);
});

test('active frontend login authenticates regenerates session and redirects home', function () {
    $this->withSession(['marker' => 'preserved']);
    $sessionId = session()->getId();
    $this->post(route('front.login.store'), $this->credentials)->assertRedirect(route('front.account'));
    $this->assertAuthenticatedAs($this->frontendUser, 'web');
    expect(session()->getId())->not->toBe($sessionId);
    expect(session('marker'))->toBe('preserved');
    $this->get(route('frontend.home'))->assertOk()->assertSee($this->frontendUser->name)
        ->assertSee('action="'.route('front.logout').'"', false)
        ->assertDontSee('href="'.route('front.register').'"', false);
});

test('inactive user is rejected only after a correct password', function () {
    $this->frontendUser->update(['is_active' => false]);
    $this->from(route('front.login'))->post(route('front.login.store'), $this->credentials)
        ->assertRedirect(route('front.login'))
        ->assertSessionHasErrors(['email' => 'آپ کا اکاؤنٹ ابھی ایکٹیویٹ نہیں ہوا۔ براہ کرم اپنے ای میل میں موجود ایکٹیویشن لنک استعمال کریں۔']);
    $this->assertGuest('web');
});

test('incorrect credentials and non frontend accounts have the same safe error', function (string $kind) {
    $credentials = $this->credentials;
    if ($kind === 'unknown') {
        $credentials['email'] = 'unknown@example.com';
    } elseif ($kind === 'wrong' || $kind === 'inactive-wrong') {
        $credentials['password'] = 'wrong-password';
        if ($kind === 'inactive-wrong') {
            $this->frontendUser->update(['is_active' => false]);
        }
    } else {
        $this->frontendUser->syncRoles(Role::findOrCreate('super-admin', 'web'));
    }
    $this->from(route('front.login'))->post(route('front.login.store'), $credentials)
        ->assertRedirect(route('front.login'))
        ->assertSessionHasErrors(['email' => 'ای میل یا پاس ورڈ درست نہیں ہے۔'])
        ->assertSessionHasInput('email', $credentials['email'])
        ->assertSessionMissing('_old_input.password');
    $this->assertGuest('web');
})->with(['unknown', 'wrong', 'inactive-wrong', 'admin']);

test('login validation is enforced by server', function (string $field, mixed $value) {
    $this->post(route('front.login.store'), array_replace($this->credentials, [$field => $value]))
        ->assertSessionHasErrors($field);
    $this->assertGuest('web');
})->with([['email', ''], ['email', 'invalid'], ['password', ''], ['password', ['bad']], ['remember', 'invalid']]);

test('remember me issues Laravel recaller cookie only when selected', function (bool $remember) {
    $response = $this->post(route('front.login.store'), $this->credentials + ['remember' => $remember]);
    $response->assertRedirect(route('front.account'));
    $cookieName = Auth::guard('web')->getRecallerName();
    if ($remember) {
        $response->assertCookie($cookieName);
        expect($this->frontendUser->fresh()->remember_token)->not->toBeNull();
    } else {
        $response->assertCookieMissing($cookieName);
    }
    $this->assertAuthenticatedAs($this->frontendUser, 'web');
})->with([true, false]);

test('login preserves a frontend intended URL', function () {
    $this->withSession(['url.intended' => route('front.taaruf')])
        ->post(route('front.login.store'), $this->credentials)->assertRedirect(route('front.taaruf'));
});

test('login discards admin and external intended URLs', function (string $destination) {
    $intended = $destination === 'admin' ? route('admin.dashboard') : 'https://untrusted.example/path';
    $this->withSession(['url.intended' => $intended])
        ->post(route('front.login.store'), $this->credentials)->assertRedirect(route('front.account'));
})->with(['admin', 'external']);

test('authenticated users are redirected away from guest auth forms and posts', function () {
    $this->actingAs($this->frontendUser);
    foreach (['/login', '/register'] as $path) {
        $this->get($path)->assertRedirect('/');
        $this->post($path, [])->assertRedirect('/');
    }
});

test('logout clears authentication session and rotates csrf token', function () {
    $this->actingAs($this->frontendUser)->withSession(['private-data' => 'secret', '_token' => 'old-token']);
    $sessionId = session()->getId();
    $this->post(route('front.logout'))->assertRedirect(route('frontend.home'))
        ->assertSessionMissing('private-data');
    $this->assertGuest('web');
    expect(session()->getId())->not->toBe($sessionId)
        ->and(session()->token())->not->toBe('old-token');
});

test('logout requires authentication and is not available via GET', function () {
    $this->post(route('front.logout'))->assertRedirect(route('front.login'));
    $this->get('/logout')->assertStatus(405);
});

test('login throttles five failures and becomes available after a minute', function () {
    $wrong = array_replace($this->credentials, ['password' => 'wrong']);
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('front.login.store'), $wrong)->assertSessionHasErrors('email');
    }
    $this->post(route('front.login.store'), $this->credentials)->assertSessionHasErrors('email');
    expect(session('errors')->first('email'))->toContain('بہت زیادہ کوششیں');
    $this->assertGuest('web');
    $this->travel(61)->seconds();
    $this->post(route('front.login.store'), $this->credentials)->assertRedirect(route('front.account'));
    $this->assertAuthenticatedAs($this->frontendUser, 'web');
});

test('successful login clears throttle attempts', function () {
    $key = 'front-login:'.hash('sha256', 'reader@example.com|127.0.0.1');
    $this->post(route('front.login.store'), array_replace($this->credentials, ['password' => 'wrong']));
    expect(RateLimiter::attempts($key))->toBe(1);
    $this->post(route('front.login.store'), $this->credentials)->assertRedirect(route('front.account'));
    expect(RateLimiter::attempts($key))->toBe(0);
});

test('admin session remains intact when visiting frontend auth routes or posting frontend logout', function () {
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->actingAs($admin)->get(route('front.login'))->assertRedirect('/');
    $this->post(route('front.logout'))->assertForbidden();
    $this->assertAuthenticatedAs($admin, 'web');
});

test('deactivated frontend sessions are ended on the next frontend request', function () {
    $this->frontendUser->update(['is_active' => false]);
    $this->actingAs($this->frontendUser)->get(route('frontend.home'))->assertRedirect(route('front.login'));
    $this->assertGuest('web');
});

test('login and logout enforce CSRF outside the test bypass', function () {
    $this->app->instance('env', 'local');
    $this->post(route('front.login.store'), $this->credentials)->assertStatus(419);
    $this->actingAs($this->frontendUser)->post(route('front.logout'))->assertStatus(419);
    $this->assertAuthenticatedAs($this->frontendUser, 'web');
});

test('remember cookie restores an active session but cannot restore a deactivated account', function (bool $active) {
    $response = $this->post(route('front.login.store'), $this->credentials + ['remember' => true]);
    $cookieName = Auth::guard('web')->getRecallerName();
    $cookieValue = $response->getCookie($cookieName)->getValue();
    $this->frontendUser->update(['is_active' => $active]);
    session()->flush();
    Auth::forgetGuards();

    $response = $this->withCookie($cookieName, $cookieValue)->get(route('frontend.home'));
    if ($active) {
        $response->assertOk();
        $this->assertAuthenticatedAs($this->frontendUser, 'web');
        expect(Auth::guard('web')->viaRemember())->toBeTrue();
    } else {
        $response->assertRedirect(route('front.login'));
        $this->assertGuest('web');
    }
})->with([true, false]);
