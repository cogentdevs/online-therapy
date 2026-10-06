<?php

use App\Models\User;
use App\Services\Authorization\AdminUserPermissionService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

function createEligibleRoleForAdminUserTest(string $name, array $permissions = []): Role
{
    $role = Role::findOrCreate($name, 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', ...$permissions])));

    return $role;
}

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

test('super admin can view backend users while frontend users remain excluded', function () {
    $adminRole = createEligibleRoleForAdminUserTest('content-admin');
    $admin = User::factory()->create(['name' => 'Backend Editor']);
    $admin->assignRole($adminRole);

    $frontendUser = User::factory()->create(['name' => 'Frontend Reader']);
    $frontendUser->assignRole(Role::findOrCreate('user', 'web'));
    $consultant = User::factory()->create(['name' => 'Frontend Consultant']);
    $consultant->assignRole(Role::findOrCreate('consultant', 'web'));

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('System / Super Admin')
        ->assertSee('Backend Editor')
        ->assertDontSee('Frontend Reader')
        ->assertDontSee('Frontend Consultant');
});

test('super admin can create an admin user with exactly one custom role', function () {
    $role = createEligibleRoleForAdminUserTest('magazine-manager', ['dashboard.view', 'magazines.view']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.store'), [
            'name' => ' Magazine   Admin ',
            'email' => ' MAGAZINE.ADMIN@EXAMPLE.COM ',
            'phone' => ' 03001234567 ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    $admin = User::query()->where('email', 'magazine.admin@example.com')->firstOrFail();

    expect($admin->name)->toBe('Magazine Admin')
        ->and($admin->phone)->toBe('03001234567')
        ->and($admin->is_active)->toBeTrue()
        ->and(Hash::check('password123', $admin->password))->toBeTrue()
        ->and($admin->roles()->pluck('name')->all())->toBe(['magazine-manager']);

    $this->post(route('admin.logout'));
    $this->post(route('admin.login.submit'), [
        'email' => $admin->email,
        'password' => 'password123',
    ])->assertRedirect(route('admin.dashboard'));
    $this->get(route('admin.dashboard'))->assertSuccessful();
    $this->get(route('admin.slider.index'))->assertForbidden();
});

test('super admin can edit details change role and activate or deactivate an admin user', function () {
    $oldRole = createEligibleRoleForAdminUserTest('old-admin-role', ['sliders.view']);
    $newRole = createEligibleRoleForAdminUserTest('new-admin-role', ['articles.view']);
    $admin = User::factory()->create(['password' => 'original-password', 'is_active' => true]);
    $admin->assignRole($oldRole);
    $originalPassword = $admin->password;

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.update', ['id' => $admin->id]), [
            'name' => 'Updated Admin',
            'email' => 'updated-admin@example.com',
            'phone' => null,
            'password' => null,
            'password_confirmation' => null,
            'role_id' => $newRole->id,
            'is_active' => '0',
        ])
        ->assertRedirect(route('admin.users.index'));

    $admin->refresh();
    expect($admin->name)->toBe('Updated Admin')
        ->and($admin->is_active)->toBeFalse()
        ->and($admin->password)->toBe($originalPassword)
        ->and($admin->roles()->pluck('name')->all())->toBe(['new-admin-role']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.update', ['id' => $admin->id]), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $newRole->id,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($admin->fresh()->is_active)->toBeTrue();
});

test('super admin can delete a safe custom admin user and its role pivot', function () {
    $role = createEligibleRoleForAdminUserTest('temporary-admin-role');
    $admin = User::factory()->create();
    $admin->assignRole($role);

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.users.destroy', ['id' => $admin->id]))
        ->assertRedirect(route('admin.users.index'));

    $this->assertModelMissing($admin);
    expect($role->users()->whereKey($admin->id)->exists())->toBeFalse();
});

test('system frontend nonexistent and non admin roles cannot be assigned', function (string $roleName) {
    $roleId = match ($roleName) {
        'missing' => 999999,
        'without-access' => Role::findOrCreate('without-admin-access', 'web')->id,
        default => Role::findOrCreate($roleName, 'web')->id,
    };

    $this->actingAs($this->superAdmin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'name' => 'Invalid Admin',
            'email' => $roleName.'@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $roleId,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.users.create'))
        ->assertSessionHasErrors('role_id');

    expect(User::query()->where('name', 'Invalid Admin')->exists())->toBeFalse();
})->with(['super-admin', 'user', 'consultant', 'without-access', 'missing']);

test('eligible role permission preview is grouped from the registry', function () {
    $role = createEligibleRoleForAdminUserTest('preview-admin', ['magazines.view', 'magazines.publish']);

    $this->actingAs($this->superAdmin)
        ->getJson(route('admin.users.role-permissions', ['role' => $role->id]))
        ->assertSuccessful()
        ->assertJsonPath('role.name', 'preview-admin')
        ->assertJsonPath('admin_access', true)
        ->assertJsonFragment([
            'name' => 'magazines.publish',
            'label' => 'Publish',
        ])
        ->assertJsonMissing(['name' => 'magazines.delete']);
});

test('full mode inherits every current and future role permission', function () {
    $role = createEligibleRoleForAdminUserTest('full-mode-admin', ['dashboard.view', 'sliders.view']);
    $admin = User::factory()->create(['permission_mode' => 'role']);
    $admin->syncRoles([$role]);

    expect($admin->can('dashboard.view'))->toBeTrue()
        ->and($admin->can('sliders.view'))->toBeTrue()
        ->and($admin->permissions()->exists())->toBeFalse();

    $role->givePermissionTo('banners.view');
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    expect($admin->fresh()->can('banners.view'))->toBeTrue();
});

test('custom mode limits effective access to direct permissions within the selected role', function () {
    $role = createEligibleRoleForAdminUserTest('limited-admin', [
        'dashboard.view', 'sliders.view', 'sliders.create', 'sliders.edit', 'sliders.delete',
    ]);
    $admin = User::factory()->create(['permission_mode' => 'custom', 'is_active' => true]);
    $admin->syncRoles([$role]);
    $admin->syncPermissions(['dashboard.view', 'sliders.view', 'sliders.create']);

    expect(app(AdminUserPermissionService::class)->customModeDecision($admin, 'sliders.edit'))->toBeFalse()
        ->and($admin->can('admin.access'))->toBeTrue()
        ->and($admin->can('sliders.view'))->toBeTrue()
        ->and($admin->can('sliders.create'))->toBeTrue()
        ->and($admin->can('sliders.edit'))->toBeFalse()
        ->and($admin->can('sliders.delete'))->toBeFalse();

    $this->actingAs($admin)->get(route('admin.slider.index'))
        ->assertSuccessful()
        ->assertSee(route('admin.slider.index'))
        ->assertDontSee(route('admin.banner.index'));
    $this->actingAs($admin)->get(route('admin.slider.edit', ['id' => 999999]))->assertForbidden();
});

test('creating a custom mode user stores only the selected role permission subset', function () {
    $role = createEligibleRoleForAdminUserTest('created-limited-admin', ['sliders.view', 'sliders.create', 'sliders.edit']);

    $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
        'name' => 'Limited User',
        'email' => 'limited-user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_id' => $role->id,
        'permission_mode' => 'custom',
        'permissions' => ['sliders.view'],
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $admin = User::query()->where('email', 'limited-user@example.com')->firstOrFail();
    expect($admin->permission_mode)->toBe('custom')
        ->and($admin->permissions()->pluck('name')->all())->toBe(['sliders.view'])
        ->and($admin->can('sliders.view'))->toBeTrue()
        ->and($admin->can('sliders.create'))->toBeFalse();
});

test('custom permissions outside the selected role are rejected', function () {
    $role = createEligibleRoleForAdminUserTest('narrow-admin', ['sliders.view']);

    $this->actingAs($this->superAdmin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'name' => 'Tampered Admin',
            'email' => 'tampered@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'permission_mode' => 'custom',
            'permissions' => ['sliders.view', 'banners.delete'],
            'is_active' => '1',
        ])
        ->assertSessionHasErrors('permissions');
});

test('editing restores custom mode permissions and changing role drops old permissions', function () {
    $oldRole = createEligibleRoleForAdminUserTest('old-limited-role', ['sliders.view', 'sliders.edit']);
    $newRole = createEligibleRoleForAdminUserTest('new-limited-role', ['articles.view', 'articles.edit']);
    $admin = User::factory()->create(['permission_mode' => 'custom']);
    $admin->syncRoles([$oldRole]);
    $admin->syncPermissions(['sliders.view']);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.edit', ['id' => $admin->id]))
        ->assertSuccessful()
        ->assertSee('data-selected-permissions=\'["sliders.view"]\'', false);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.update', ['id' => $admin->id]), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $newRole->id,
            'permission_mode' => 'custom',
            'permissions' => ['articles.view'],
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

    expect($admin->fresh()->roles()->pluck('name')->all())->toBe(['new-limited-role'])
        ->and($admin->fresh()->permissions()->pluck('name')->all())->toBe(['articles.view'])
        ->and($admin->fresh()->can('sliders.view'))->toBeFalse()
        ->and($admin->fresh()->can('articles.view'))->toBeTrue();
});

test('switching permission modes stores or clears the direct subset', function () {
    $role = createEligibleRoleForAdminUserTest('mode-switch-role', ['sliders.view', 'sliders.edit']);
    $admin = User::factory()->create(['permission_mode' => 'role']);
    $admin->syncRoles([$role]);

    $base = ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $role->id, 'is_active' => '1'];
    $this->actingAs($this->superAdmin)->post(route('admin.users.update', ['id' => $admin->id]), [
        ...$base, 'permission_mode' => 'custom', 'permissions' => ['sliders.view'],
    ])->assertRedirect(route('admin.users.index'));
    expect($admin->fresh()->permission_mode)->toBe('custom')
        ->and($admin->fresh()->can('sliders.view'))->toBeTrue()
        ->and($admin->fresh()->can('sliders.edit'))->toBeFalse();

    $this->actingAs($this->superAdmin)->post(route('admin.users.update', ['id' => $admin->id]), [
        ...$base, 'permission_mode' => 'role', 'permissions' => [],
    ])->assertRedirect(route('admin.users.index'));
    expect($admin->fresh()->permission_mode)->toBe('role')
        ->and($admin->fresh()->permissions()->exists())->toBeFalse()
        ->and($admin->fresh()->can('sliders.edit'))->toBeTrue();
});

test('custom mode never gains new role permissions and loses removed role permissions', function () {
    $role = createEligibleRoleForAdminUserTest('changing-limited-role', ['sliders.view', 'sliders.edit']);
    $admin = User::factory()->create(['permission_mode' => 'custom']);
    $admin->syncRoles([$role]);
    $admin->syncPermissions(['sliders.view']);

    $role->givePermissionTo('banners.view');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    expect($admin->fresh()->can('banners.view'))->toBeFalse();

    $role->revokePermissionTo('sliders.view');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    expect($admin->fresh()->can('sliders.view'))->toBeFalse();
});

test('super admin bypass remains unrestricted regardless of permission mode', function () {
    $this->superAdmin->forceFill(['permission_mode' => 'custom'])->save();
    $this->superAdmin->syncPermissions([]);

    expect($this->superAdmin->can('sliders.delete'))->toBeTrue()
        ->and($this->superAdmin->can('future.permission'))->toBeTrue();
});

test('protected and frontend roles cannot be queried through permission preview', function (string $roleName) {
    $role = Role::findOrCreate($roleName, 'web');

    $this->actingAs($this->superAdmin)
        ->getJson(route('admin.users.role-permissions', ['role' => $role->id]))
        ->assertNotFound();
})->with(['super-admin', 'user', 'consultant']);

test('super admin and frontend accounts cannot be edited or deleted through admin user management', function (string $roleName) {
    $protectedUser = $roleName === 'super-admin'
        ? $this->superAdmin
        : User::factory()->create();
    $protectedUser->syncRoles([Role::findOrCreate($roleName, 'web')]);
    $customRole = createEligibleRoleForAdminUserTest('replacement-admin-role');

    $this->actingAs($this->superAdmin)
        ->get(route('admin.users.edit', ['id' => $protectedUser->id]))
        ->assertNotFound();
    $this->actingAs($this->superAdmin)
        ->delete(route('admin.users.destroy', ['id' => $protectedUser->id]))
        ->assertNotFound();
    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.update', ['id' => $protectedUser->id]), [
            'name' => $protectedUser->name,
            'email' => $protectedUser->email,
            'role_id' => $customRole->id,
            'is_active' => '0',
        ])
        ->assertNotFound();

    expect(User::query()->find($protectedUser->id))->not->toBeNull();
})->with(['super-admin', 'user', 'consultant']);

test('current admin cannot delete deactivate or change their own role', function () {
    $currentRole = createEligibleRoleForAdminUserTest('user-manager', ['users.view', 'users.edit', 'users.delete']);
    $otherRole = createEligibleRoleForAdminUserTest('other-admin-role');
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($currentRole);

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', ['id' => $admin->id]))
        ->assertSessionHas('error', 'You cannot delete your own Admin account.');

    $baseUpdate = [
        'name' => $admin->name,
        'email' => $admin->email,
        'role_id' => $currentRole->id,
        'is_active' => '0',
    ];
    $this->actingAs($admin)
        ->post(route('admin.users.update', ['id' => $admin->id]), $baseUpdate)
        ->assertSessionHas('error', 'You cannot deactivate your own Admin account.');

    $this->actingAs($admin)
        ->post(route('admin.users.update', ['id' => $admin->id]), [
            ...$baseUpdate,
            'role_id' => $otherRole->id,
            'is_active' => '1',
        ])
        ->assertSessionHas('error', 'You cannot change your own Admin role.');

    expect($admin->fresh()->is_active)->toBeTrue()
        ->and($admin->fresh()->roles()->pluck('name')->all())->toBe(['user-manager']);
});

test('users view permission does not grant create edit or delete access', function () {
    $viewerRole = createEligibleRoleForAdminUserTest('user-viewer', ['users.view']);
    $viewer = User::factory()->create(['is_active' => true]);
    $viewer->assignRole($viewerRole);
    $managedRole = createEligibleRoleForAdminUserTest('managed-admin-role');
    $managedUser = User::factory()->create();
    $managedUser->assignRole($managedRole);

    $this->actingAs($viewer)->get(route('admin.users.index'))->assertSuccessful();
    $this->actingAs($viewer)->get(route('admin.users.create'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.users.edit', ['id' => $managedUser->id]))->assertForbidden();
    $this->actingAs($viewer)->delete(route('admin.users.destroy', ['id' => $managedUser->id]))->assertForbidden();
});

test('each user management mutation requires and accepts its matching permission', function (string $permission, string $action) {
    $operatorRole = createEligibleRoleForAdminUserTest('operator-'.$action, [$permission]);
    $operator = User::factory()->create(['is_active' => true]);
    $operator->assignRole($operatorRole);
    $managedRole = createEligibleRoleForAdminUserTest('target-role-'.$action);
    $managedUser = User::factory()->create(['parent_admin_id' => $operator->id]);
    $managedUser->assignRole($managedRole);

    match ($action) {
        'create' => $this->actingAs($operator)->get(route('admin.users.create'))->assertSuccessful(),
        'edit' => $this->actingAs($operator)->get(route('admin.users.edit', ['id' => $managedUser->id]))->assertSuccessful(),
        'delete' => $this->actingAs($operator)->delete(route('admin.users.destroy', ['id' => $managedUser->id]))->assertRedirect(route('admin.users.index')),
    };
})->with([
    ['users.create', 'create'],
    ['users.edit', 'edit'],
    ['users.delete', 'delete'],
]);
