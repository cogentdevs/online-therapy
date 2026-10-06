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
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

test('super admin can view the dynamic role management screen', function () {
    Role::findOrCreate('user', 'web');
    Role::findOrCreate('consultant', 'web');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.roles.index'))
        ->assertSuccessful()
        ->assertSee('Super Admin')
        ->assertSee('User')
        ->assertSee('Consultant');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.roles.create'))
        ->assertSuccessful()
        ->assertSee('Module Permissions')
        ->assertSee('View PDF')
        ->assertSee('Remove Related Magazine');
});

test('super admin can create a web role with registered permissions and required admin access', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.roles.store'), [
            'name' => ' Magazine   Manager ',
            'permissions' => ['magazines.view', 'magazines.publish'],
        ])
        ->assertRedirect(route('admin.roles.index'))
        ->assertSessionHas('status');

    $role = Role::findByName('Magazine Manager', 'web');

    expect($role->guard_name)->toBe('web')
        ->and($role->hasPermissionTo('admin.access'))->toBeTrue()
        ->and($role->hasPermissionTo('magazines.view'))->toBeTrue()
        ->and($role->hasPermissionTo('magazines.publish'))->toBeTrue();
});

test('editing a custom role renames it and synchronizes permission changes', function () {
    $role = Role::findOrCreate('content-editor', 'web');
    $role->givePermissionTo(['admin.access', 'sliders.view', 'sliders.edit']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.roles.update', ['id' => $role->id]), [
            'name' => 'Senior Content Editor',
            'permissions' => ['articles.view', 'articles.edit'],
        ])
        ->assertRedirect(route('admin.roles.index'));

    $role->refresh();

    expect($role->name)->toBe('Senior Content Editor')
        ->and($role->permissions()->pluck('name')->sort()->values()->all())->toBe([
            'admin.access',
            'articles.edit',
            'articles.view',
        ]);
});

test('super admin can delete an unused custom role', function () {
    $role = Role::findOrCreate('unused-admin-role', 'web');

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.roles.destroy', ['id' => $role->id]))
        ->assertRedirect(route('admin.roles.index'));

    expect(Role::query()->find($role->id))->toBeNull();
});

test('protected system roles cannot be edited or deleted', function (string $roleName) {
    $role = Role::findOrCreate($roleName, 'web');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.roles.edit', ['id' => $role->id]))
        ->assertForbidden();

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.roles.destroy', ['id' => $role->id]))
        ->assertForbidden();

    expect(Role::query()->find($role->id))->not->toBeNull();
})->with(['super-admin', 'user', 'consultant']);

test('duplicate and protected role names are rejected', function () {
    Role::findOrCreate('Finance Manager', 'web');

    $this->actingAs($this->superAdmin)
        ->from(route('admin.roles.create'))
        ->post(route('admin.roles.store'), ['name' => 'Finance Manager'])
        ->assertRedirect(route('admin.roles.create'))
        ->assertSessionHasErrors('name');

    $this->actingAs($this->superAdmin)
        ->from(route('admin.roles.create'))
        ->post(route('admin.roles.store'), ['name' => 'consultant'])
        ->assertRedirect(route('admin.roles.create'))
        ->assertSessionHasErrors('name');
});

test('unregistered or unsynchronized permission names are rejected', function () {
    Permission::findOrCreate('tampered.permission', 'web');

    $this->actingAs($this->superAdmin)
        ->from(route('admin.roles.create'))
        ->post(route('admin.roles.store'), [
            'name' => 'Tampered Role',
            'permissions' => ['tampered.permission'],
        ])
        ->assertRedirect(route('admin.roles.create'))
        ->assertSessionHasErrors('permissions.0');

    expect(Role::query()->where('name', 'Tampered Role')->exists())->toBeFalse();
});

test('a custom role assigned to users cannot be deleted', function () {
    $role = Role::findOrCreate('assigned-admin-role', 'web');
    User::factory()->create()->assignRole($role);

    $this->actingAs($this->superAdmin)
        ->from(route('admin.roles.index'))
        ->delete(route('admin.roles.destroy', ['id' => $role->id]))
        ->assertRedirect(route('admin.roles.index'))
        ->assertSessionHas('error', 'This role is assigned to users and cannot be deleted.');

    expect(Role::query()->find($role->id))->not->toBeNull();
});

test('roles view permission cannot bypass create edit or delete permissions', function () {
    $viewerRole = Role::findOrCreate('role-viewer', 'web');
    $viewerRole->givePermissionTo(['admin.access', 'roles.view']);
    $viewer = User::factory()->create(['is_active' => true]);
    $viewer->assignRole($viewerRole);
    $managedRole = Role::findOrCreate('managed-role', 'web');

    $this->actingAs($viewer)->get(route('admin.roles.index'))->assertSuccessful();
    $this->actingAs($viewer)->get(route('admin.roles.create'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.roles.edit', ['id' => $managedRole->id]))->assertForbidden();
    $this->actingAs($viewer)->delete(route('admin.roles.destroy', ['id' => $managedRole->id]))->assertForbidden();
});
