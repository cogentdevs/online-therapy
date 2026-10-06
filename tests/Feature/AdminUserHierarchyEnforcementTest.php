<?php

use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['name' => 'Hierarchy Root Admin', 'is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function createHierarchyEnforcementRole(string $name, array $permissions = []): Role
{
    $role = Role::findOrCreate($name, 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', ...$permissions])));

    return $role;
}

function createHierarchyEnforcementAdmin(
    string $name,
    Role $role,
    ?User $parentAdmin = null,
    string $permissionMode = 'role',
    array $directPermissions = [],
): User {
    $admin = User::factory()->create([
        'name' => $name,
        'is_active' => true,
        'permission_mode' => $permissionMode,
        'parent_admin_id' => $parentAdmin?->id,
    ]);
    $admin->syncRoles([$role]);
    $admin->syncPermissions($directPermissions);

    return $admin;
}

test('Admin User listings contain only self and full descendants for Custom Admins', function () {
    $role = createHierarchyEnforcementRole('hierarchy-list-manager', ['users.view']);
    $adminA = createHierarchyEnforcementAdmin('Hierarchy Admin A', $role, $this->superAdmin);
    $adminB = createHierarchyEnforcementAdmin('Hierarchy Admin B', $role, $adminA);
    $adminC = createHierarchyEnforcementAdmin('Hierarchy Admin C', $role, $adminB);
    $adminX = createHierarchyEnforcementAdmin('Hierarchy Admin X', $role, $this->superAdmin);

    $this->actingAs($adminA)->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Hierarchy Admin A')
        ->assertSee('Hierarchy Admin B')
        ->assertSee('Hierarchy Admin C')
        ->assertDontSee('Hierarchy Admin X')
        ->assertDontSee('Hierarchy Root Admin');

    $this->actingAs($adminB)->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Hierarchy Admin B')
        ->assertSee('Hierarchy Admin C')
        ->assertDontSee('Hierarchy Admin A')
        ->assertDontSee('Hierarchy Admin X');

    $this->actingAs($adminC)->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Hierarchy Admin C')
        ->assertDontSee('Hierarchy Admin A')
        ->assertDontSee('Hierarchy Admin B')
        ->assertDontSee('Hierarchy Admin X');

    $this->actingAs($this->superAdmin)->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Hierarchy Root Admin')
        ->assertSee('Hierarchy Admin A')
        ->assertSee('Hierarchy Admin B')
        ->assertSee('Hierarchy Admin C')
        ->assertSee('Hierarchy Admin X');
});

test('direct User URLs reject ancestors and unrelated Admins', function () {
    $managerRole = createHierarchyEnforcementRole('hierarchy-direct-manager', [
        'users.view', 'users.edit', 'users.delete',
    ]);
    $targetRole = createHierarchyEnforcementRole('hierarchy-direct-target');
    $adminA = createHierarchyEnforcementAdmin('Direct Admin A', $managerRole, $this->superAdmin);
    $adminB = createHierarchyEnforcementAdmin('Direct Admin B', $managerRole, $adminA);
    $adminC = createHierarchyEnforcementAdmin('Direct Admin C', $managerRole, $adminB);
    $adminX = createHierarchyEnforcementAdmin('Direct Admin X', $targetRole, $this->superAdmin);

    $this->actingAs($adminA)->get(route('admin.users.edit', ['id' => $adminX->id]))->assertForbidden();
    $this->actingAs($adminA)->post(route('admin.users.update', ['id' => $adminX->id]), [
        'name' => $adminX->name,
        'email' => $adminX->email,
        'role_id' => $targetRole->id,
        'permission_mode' => 'role',
        'permissions' => [],
        'is_active' => '0',
    ])->assertForbidden();
    $this->actingAs($adminA)->delete(route('admin.users.destroy', ['id' => $adminX->id]))->assertForbidden();
    $this->actingAs($adminB)->get(route('admin.users.edit', ['id' => $adminA->id]))->assertForbidden();
    $this->actingAs($adminC)->get(route('admin.users.edit', ['id' => $adminB->id]))->assertForbidden();

    expect($adminX->fresh()->is_active)->toBeTrue();
});

test('creating an Admin automatically assigns creator and Parent Admin independently', function () {
    $parentRole = createHierarchyEnforcementRole('hierarchy-creator', ['users.create', 'users.view']);
    $childRole = createHierarchyEnforcementRole('hierarchy-created-child', ['users.view']);
    $adminA = createHierarchyEnforcementAdmin('Creating Admin A', $parentRole, $this->superAdmin);

    $this->actingAs($adminA)->post(route('admin.users.store'), [
        'name' => 'Created Admin B',
        'email' => 'created-admin-b@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $childRole->id,
        'permission_mode' => 'role',
        'permissions' => [],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $adminB = User::query()->where('email', 'created-admin-b@example.com')->firstOrFail();

    expect($adminB->parent_admin_id)->toBe($adminA->id)
        ->and($adminB->created_by)->toBe($adminA->id)
        ->and($adminB->parentAdmin->is($adminA))->toBeTrue();
});

test('Custom Admin cannot assign a role stronger than its effective permissions', function (string $permissionMode, array $directPermissions) {
    $parentRole = createHierarchyEnforcementRole('ceiling-parent-'.$permissionMode, [
        'users.create', 'users.view', 'magazines.view',
    ]);
    $strongerRole = createHierarchyEnforcementRole('ceiling-stronger-'.$permissionMode, [
        'users.view', 'magazines.delete',
    ]);
    $parent = createHierarchyEnforcementAdmin(
        'Ceiling Parent '.$permissionMode,
        $parentRole,
        $this->superAdmin,
        $permissionMode,
        $directPermissions,
    );

    $this->actingAs($parent)->post(route('admin.users.store'), [
        'name' => 'Rejected Stronger Child '.$permissionMode,
        'email' => 'rejected-'.$permissionMode.'@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $strongerRole->id,
        'permission_mode' => 'role',
        'permissions' => [],
        'is_active' => '1',
    ])->assertSessionHasErrors('role_id');

    expect(User::query()->where('email', 'rejected-'.$permissionMode.'@example.com')->exists())->toBeFalse();
})->with([
    'Full Role mode' => ['role', []],
    'Limited mode' => ['custom', ['users.create', 'users.view']],
]);

test('Limited Child permissions cannot exceed the Parent effective scope', function () {
    $parentRole = createHierarchyEnforcementRole('limited-ceiling-parent', [
        'users.create', 'users.view', 'magazines.view',
    ]);
    $childRole = createHierarchyEnforcementRole('limited-ceiling-child', [
        'users.view', 'magazines.view',
    ]);
    $parent = createHierarchyEnforcementAdmin(
        'Limited Ceiling Parent',
        $parentRole,
        $this->superAdmin,
        'custom',
        ['users.create', 'users.view'],
    );

    $this->actingAs($parent)->post(route('admin.users.store'), [
        'name' => 'Limited Ceiling Child',
        'email' => 'limited-ceiling-child@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $childRole->id,
        'permission_mode' => 'custom',
        'permissions' => ['users.view', 'magazines.view'],
        'is_active' => '1',
    ])->assertSessionHasErrors(['role_id', 'permissions']);
});

test('Custom Admin self-protection prevents permission mode and limited permission changes', function () {
    $role = createHierarchyEnforcementRole('self-scope-manager', [
        'users.view', 'users.edit', 'sliders.view', 'sliders.edit',
    ]);
    $admin = createHierarchyEnforcementAdmin(
        'Self Scope Admin',
        $role,
        $this->superAdmin,
        'custom',
        ['users.view', 'users.edit', 'sliders.view'],
    );

    $this->actingAs($admin)->post(route('admin.users.update', ['id' => $admin->id]), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role_id' => $role->id,
        'permission_mode' => 'custom',
        'permissions' => ['users.view', 'users.edit'],
        'is_active' => '1',
    ])->assertSessionHas('error', 'You cannot change your own Admin permission scope.');

    $this->actingAs($admin)->post(route('admin.users.update', ['id' => $admin->id]), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role_id' => $role->id,
        'permission_mode' => 'role',
        'permissions' => [],
        'is_active' => '1',
    ])->assertSessionHas('error', 'You cannot change your own Admin permission scope.');

    expect($admin->fresh()->permission_mode)->toBe('custom')
        ->and($admin->fresh()->permissions()->pluck('name')->sort()->values()->all())
        ->toBe(['sliders.view', 'users.edit', 'users.view']);
});

test('Admin deletion and deactivation are blocked while Child Admins remain assigned', function () {
    $managerRole = createHierarchyEnforcementRole('child-safety-manager', [
        'users.view', 'users.edit', 'users.delete',
    ]);
    $childRole = createHierarchyEnforcementRole('child-safety-target');
    $adminA = createHierarchyEnforcementAdmin('Safety Admin A', $managerRole, $this->superAdmin);
    $adminB = createHierarchyEnforcementAdmin('Safety Admin B', $childRole, $adminA);
    createHierarchyEnforcementAdmin('Safety Admin C', $childRole, $adminB);

    $this->actingAs($adminA)
        ->delete(route('admin.users.destroy', ['id' => $adminB->id]))
        ->assertSessionHas('error', 'This Admin cannot be deleted while Child Admins are assigned to it.');

    $this->actingAs($adminA)->post(route('admin.users.update', ['id' => $adminB->id]), [
        'name' => $adminB->name,
        'email' => $adminB->email,
        'role_id' => $childRole->id,
        'permission_mode' => 'role',
        'permissions' => [],
        'is_active' => '0',
    ])->assertSessionHas('error', 'This Admin cannot be deactivated while active Child Admins are assigned to it.');

    expect($adminB->fresh()->is_active)->toBeTrue();
});

test('role and permission UI only expose choices within the acting Admin effective scope', function () {
    $parentRole = createHierarchyEnforcementRole('ui-ceiling-parent', [
        'users.view', 'users.create', 'magazines.view',
    ]);
    $allowedRole = createHierarchyEnforcementRole('ui-allowed-role', ['magazines.view']);
    $strongerRole = createHierarchyEnforcementRole('ui-stronger-role', ['magazines.delete']);
    $parent = createHierarchyEnforcementAdmin(
        'UI Ceiling Parent',
        $parentRole,
        $this->superAdmin,
        'custom',
        ['users.view', 'users.create', 'magazines.view'],
    );

    $this->actingAs($parent)->get(route('admin.users.create'))
        ->assertSuccessful()
        ->assertSee('Ui Allowed Role')
        ->assertDontSee('Ui Stronger Role');

    $this->actingAs($parent)
        ->getJson(route('admin.users.role-permissions', ['role' => $allowedRole->id]))
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'magazines.view'])
        ->assertJsonMissing(['name' => 'magazines.delete']);

    $this->actingAs($parent)
        ->getJson(route('admin.users.role-permissions', ['role' => $strongerRole->id]))
        ->assertForbidden();
});

test('Super Admin remains unrestricted while protected and frontend accounts remain outside management', function () {
    $role = createHierarchyEnforcementRole('super-root-visible-role');
    $customAdmin = createHierarchyEnforcementAdmin('Super Visible Custom Admin', $role, $this->superAdmin);
    $frontendUser = User::factory()->create(['name' => 'Hierarchy Frontend User']);
    $frontendUser->assignRole(Role::findOrCreate('user', 'web'));

    $this->actingAs($this->superAdmin)->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Super Visible Custom Admin')
        ->assertDontSee('Hierarchy Frontend User');

    $this->actingAs($customAdmin)
        ->get(route('admin.users.edit', ['id' => $this->superAdmin->id]))
        ->assertForbidden();
});
