<?php

use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('super admin can login and access protected admin routes without explicit permissions', function () {
    $role = Role::findOrCreate('super-admin', 'web');
    $user = User::factory()->create([
        'email' => 'rbac-super-admin@example.com',
        'password' => 'password',
        'is_active' => true,
    ]);
    $user->assignRole($role);

    $this->post(route('admin.login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
    $this->get(route('admin.dashboard'))->assertSuccessful();
    $this->get(route('admin.slider.index'))->assertSuccessful();
    expect($role->permissions()->count())->toBe(0);
});

test('gate bypass allows a super admin to use a newly synchronized permission', function () {
    config()->set('admin_modules.modules.future-audio', [
        'label' => 'Future Audio',
        'group' => 'Content',
        'actions' => ['publish' => 'Publish'],
    ]);

    app(PermissionSyncService::class)->sync();

    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole(Role::findOrCreate('super-admin', 'web'));

    expect(Permission::findByName('future-audio.publish', 'web'))->not->toBeNull()
        ->and($user->can('future-audio.publish'))->toBeTrue();
});

test('ordinary admin can login and access only routes granted by its permissions', function () {
    $role = Role::findOrCreate('content-admin', 'web');
    $role->givePermissionTo(collect([
        'admin.access',
        'dashboard.view',
        'sliders.view',
    ])->map(fn (string $name) => Permission::findOrCreate($name, 'web')));

    $user = User::factory()->create([
        'password' => 'password',
        'is_active' => true,
    ]);
    $user->assignRole($role);

    $this->post(route('admin.login.submit'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))->assertSuccessful();
    $this->get(route('admin.slider.index'))->assertSuccessful();
    $this->get(route('admin.slider.create'))->assertForbidden();
});

test('users without admin access and inactive admins cannot enter the admin area', function () {
    Permission::findOrCreate('admin.access', 'web');
    Permission::findOrCreate('dashboard.view', 'web');

    $unauthorized = User::factory()->create(['is_active' => true]);
    $this->actingAs($unauthorized)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $adminRole = Role::findOrCreate('inactive-admin', 'web');
    $adminRole->givePermissionTo(['admin.access', 'dashboard.view']);
    $inactive = User::factory()->create(['is_active' => false]);
    $inactive->assignRole($adminRole);

    $this->actingAs($inactive)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

test('frontend system roles do not grant admin access by themselves', function (string $roleName) {
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole(Role::findOrCreate($roleName, 'web'));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
})->with(['user', 'consultant']);

test('permission synchronization is idempotent and preserves unregistered permissions', function () {
    Permission::findOrCreate('legacy.permission', 'web');
    $service = app(PermissionSyncService::class);

    $first = $service->sync();
    $countAfterFirstSync = Permission::query()->where('guard_name', 'web')->count();
    $second = $service->sync();

    expect($first['obsolete'])->toContain('legacy.permission')
        ->and($second['created'])->toBeEmpty()
        ->and($second['obsolete'])->toContain('legacy.permission')
        ->and(Permission::query()->where('guard_name', 'web')->count())->toBe($countAfterFirstSync)
        ->and(Permission::findByName('legacy.permission', 'web'))->not->toBeNull();
});
