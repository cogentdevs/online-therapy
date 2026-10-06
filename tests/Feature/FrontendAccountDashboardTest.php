<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
});

function frontendAccountUser(array $attributes = []): User
{
    $user = User::factory()->create(array_replace([
        'name' => 'علی رضا',
        'email' => 'ali@example.com',
        'phone' => '03001234567',
        'is_active' => true,
    ], $attributes));
    $user->assignRole('user');

    return $user;
}

test('guest is redirected from account to frontend login', function () {
    $this->get(route('front.account'))->assertRedirect(route('front.login'));
});

test('active exact frontend user can view real account data and empty states', function () {
    $user = frontendAccountUser();

    $this->actingAs($user)->get(route('front.account'))
        ->assertSuccessful()
        ->assertViewIs('frontend.user-account.index')
        ->assertViewHas('user', fn (User $viewUser): bool => $viewUser->is($user))
        ->assertSee($user->name)
        ->assertSee($user->email)
        ->assertSee($user->phone)
        ->assertSee('فعال رکن')
        ->assertSee('آپ کی کوئی فعال سبسکرپشن موجود نہیں ہے۔')
        ->assertSee('href="'.route('front.subscriptions').'"', false)
        ->assertSee('ابھی کوئی بک مارک محفوظ نہیں کیا گیا۔')
        ->assertSee('ابھی کوئی حالیہ سرگرمی موجود نہیں۔')
        ->assertSee('action="'.route('front.logout').'"', false)
        ->assertDontSee('ذاتی معلومات');
});

test('inactive frontend user is logged out and redirected safely', function () {
    $user = frontendAccountUser(['is_active' => false]);

    $this->actingAs($user)->get(route('front.account'))
        ->assertRedirect(route('front.login'))
        ->assertSessionHasErrors('email');
    $this->assertGuest('web');
});

test('non frontend roles cannot access the frontend account', function (string $role) {
    $user = User::factory()->create(['is_active' => true]);
    if ($role !== 'none') {
        $user->assignRole(Role::findOrCreate($role, 'web'));
    }

    $this->actingAs($user)->get(route('front.account'))->assertForbidden();
    $this->assertAuthenticatedAs($user, 'web');
})->with(['super-admin', 'Content Editor', 'none']);

test('successful login defaults to account and keeps frontend intended destinations', function () {
    $user = frontendAccountUser(['password' => 'password123']);

    $this->post(route('front.login.store'), ['email' => $user->email, 'password' => 'password123'])
        ->assertRedirect(route('front.account'));

    auth('web')->logout();

    $this->withSession(['url.intended' => route('front.taaruf')])
        ->post(route('front.login.store'), ['email' => $user->email, 'password' => 'password123'])
        ->assertRedirect(route('front.taaruf'));
});

test('header distinguishes guests frontend users and admin sessions', function () {
    $this->get(route('frontend.home'))
        ->assertSee(route('front.login'), false)
        ->assertSee(route('front.register'), false)
        ->assertDontSee(route('front.account'), false);

    $frontendUser = frontendAccountUser();
    $this->actingAs($frontendUser)->get(route('frontend.home'))
        ->assertSee(route('front.account'), false)
        ->assertSee('میرا اکاؤنٹ')
        ->assertSee('action="'.route('front.logout').'"', false)
        ->assertDontSee(route('front.login'), false)
        ->assertDontSee(route('front.register'), false);

    auth('web')->logout();
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->actingAs($admin)->get(route('frontend.home'))
        ->assertDontSee(route('front.account'), false)
        ->assertDontSee('action="'.route('front.logout').'"', false);
});

test('logout remains POST only from account flow', function () {
    $user = frontendAccountUser();

    $this->actingAs($user)->post(route('front.logout'))->assertRedirect(route('frontend.home'));
    $this->assertGuest('web');
    $this->get('/logout')->assertMethodNotAllowed();
});
