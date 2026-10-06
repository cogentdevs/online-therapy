<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

function createPasswordSuperAdmin(): User
{
    $user = User::factory()->create([
        'password' => 'current-password',
    ]);
    $user->assignRole(Role::findOrCreate('super-admin'));

    return $user;
}

test('change password routes require authentication', function () {
    $this->get(route('admin.change-password'))
        ->assertRedirect(route('admin.login'));

    $this->put(route('admin.change-password.update'))
        ->assertRedirect(route('admin.login'));
});

test('a super admin can open the change password page', function () {
    $superAdmin = createPasswordSuperAdmin();

    $this->actingAs($superAdmin)
        ->get(route('admin.change-password'))
        ->assertSuccessful()
        ->assertSee('Current Password')
        ->assertSee('New Password')
        ->assertSee(route('admin.change-password.update'));
});

test('the current password must be correct', function () {
    $superAdmin = createPasswordSuperAdmin();

    $this->actingAs($superAdmin)
        ->from(route('admin.change-password'))
        ->put(route('admin.change-password.update'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertRedirect(route('admin.change-password'))
        ->assertSessionHasErrors([
            'current_password' => 'The current password is incorrect.',
        ]);

    expect(Hash::check('current-password', $superAdmin->fresh()->password))->toBeTrue();
});

test('a super admin can update the password securely', function () {
    $superAdmin = createPasswordSuperAdmin();

    $this->actingAs($superAdmin)
        ->put(route('admin.change-password.update'), [
            'current_password' => 'current-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertRedirect(route('admin.change-password'))
        ->assertSessionHas('status', 'Password changed successfully.');

    $updatedPassword = $superAdmin->fresh()->password;

    expect(Hash::check('new-secure-password', $updatedPassword))->toBeTrue()
        ->and(Hash::check('current-password', $updatedPassword))->toBeFalse();

    $this->assertAuthenticatedAs($superAdmin);
});

test('the new password must be confirmed and different from the current password', function () {
    $superAdmin = createPasswordSuperAdmin();

    $this->actingAs($superAdmin)
        ->from(route('admin.change-password'))
        ->put(route('admin.change-password.update'), [
            'current_password' => 'current-password',
            'password' => 'current-password',
            'password_confirmation' => 'different-password',
        ])
        ->assertRedirect(route('admin.change-password'))
        ->assertSessionHasErrors('password');
});
