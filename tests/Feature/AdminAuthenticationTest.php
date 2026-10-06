<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

function createSuperAdmin(): User
{
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $user->assignRole(Role::findOrCreate('super-admin'));

    return $user;
}

test('the admin login page is available', function () {
    $this->get(route('admin.login'))
        ->assertSuccessful()
        ->assertSee('Sign in to access the admin dashboard')
        ->assertSee('Email Address');
});

test('guests are redirected from the admin dashboard to the admin login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('a super admin can log in with email and password', function () {
    $superAdmin = createSuperAdmin();

    $this->post(route('admin.login.submit'), [
        'email' => $superAdmin->email,
        'password' => 'password',
        'remember' => true,
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($superAdmin);
});

test('invalid credentials are rejected without exposing account existence', function () {
    $user = User::factory()->create();

    $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors([
            'email' => 'The provided credentials are incorrect.',
        ]);

    $this->assertGuest();
});

test('a user without admin access cannot log in to the admin panel', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors([
            'email' => 'The provided credentials are incorrect.',
        ]);

    $this->assertGuest();
});

test('an authenticated non admin receives a forbidden response', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('an inactive super admin is logged out by the admin middleware', function () {
    $superAdmin = createSuperAdmin();
    $superAdmin->setAttribute('is_active', false);
    $superAdmin->syncOriginalAttribute('is_active');

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an authenticated super admin visiting admin login is redirected to dashboard', function () {
    $superAdmin = createSuperAdmin();

    $this->actingAs($superAdmin)
        ->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

test('the admin dashboard renders through the reusable master layout', function () {
    $superAdmin = createSuperAdmin();

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Digital Magazine')
        ->assertSee('Content Setup')
        ->assertSee('Super Admin')
        ->assertSee('Subscription Trend')
        ->assertSee(route('admin.logout'));
});

test('a super admin can log out securely', function () {
    $superAdmin = createSuperAdmin();

    $this->actingAs($superAdmin)
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
