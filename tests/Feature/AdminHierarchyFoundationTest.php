<?php

use App\Models\User;
use App\Services\AdminHierarchyService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->hierarchyService = app(AdminHierarchyService::class);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function createHierarchyAdmin(string $roleName, ?User $parentAdmin = null): User
{
    $role = Role::findOrCreate($roleName, 'web');
    $role->syncPermissions(['admin.access']);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    if ($parentAdmin instanceof User) {
        $admin->forceFill(['parent_admin_id' => $parentAdmin->id])->save();
    }

    return $admin;
}

test('users table has a nullable indexed parent Admin foreign key', function () {
    $parentAdminForeignKey = collect(Schema::getForeignKeys('users'))
        ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['parent_admin_id']);
    $parentAdminIndex = collect(Schema::getIndexes('users'))
        ->first(fn (array $index): bool => $index['columns'] === ['parent_admin_id']);

    expect(Schema::hasColumn('users', 'parent_admin_id'))->toBeTrue();

    expect($parentAdminForeignKey)->not->toBeNull()
        ->and($parentAdminForeignKey['foreign_table'])->toBe('users')
        ->and($parentAdminForeignKey['foreign_columns'])->toBe(['id'])
        ->and($parentAdminForeignKey['on_delete'])->toBe('set null')
        ->and($parentAdminIndex)->not->toBeNull();

    $admin = createHierarchyAdmin('nullable-parent-admin');

    expect($admin->parent_admin_id)->toBeNull();
});

test('User exposes Parent Admin and Child Admin relationships', function () {
    $parentAdmin = createHierarchyAdmin('relationship-parent-admin', $this->superAdmin);
    $childAdmin = createHierarchyAdmin('relationship-child-admin', $parentAdmin);

    expect($childAdmin->parentAdmin)->toBeInstanceOf(User::class)
        ->and($childAdmin->parentAdmin->is($parentAdmin))->toBeTrue()
        ->and($parentAdmin->childAdmins->modelKeys())->toBe([$childAdmin->id]);
});

test('full descendants and allowed Admin IDs exclude unrelated Admins', function () {
    $adminA = createHierarchyAdmin('hierarchy-admin-a', $this->superAdmin);
    $adminB = createHierarchyAdmin('hierarchy-admin-b', $adminA);
    $adminC = createHierarchyAdmin('hierarchy-admin-c', $adminB);
    $unrelatedAdmin = createHierarchyAdmin('unrelated-admin', $this->superAdmin);

    expect($this->hierarchyService->getDescendantIds($adminA)->all())->toBe([$adminB->id, $adminC->id])
        ->and($this->hierarchyService->getDescendantIds($adminB)->all())->toBe([$adminC->id])
        ->and($this->hierarchyService->getDescendantIds($adminC))->toBeEmpty()
        ->and($this->hierarchyService->getAllowedAdminIds($adminA)?->all())->toBe([$adminA->id, $adminB->id, $adminC->id])
        ->and($this->hierarchyService->getAllowedAdminIds($adminB)?->all())->toBe([$adminB->id, $adminC->id])
        ->and($this->hierarchyService->getAllowedAdminIds($adminC)?->all())->toBe([$adminC->id])
        ->and($this->hierarchyService->getAllowedAdminIds($adminA)?->contains($unrelatedAdmin->id))->toBeFalse();
});

test('Super Admin is recognized as unrestricted without materializing allowed IDs', function () {
    expect($this->hierarchyService->isUnrestricted($this->superAdmin))->toBeTrue()
        ->and($this->hierarchyService->getAllowedAdminIds($this->superAdmin))->toBeNull();
});

test('self-parenting is rejected', function () {
    $admin = createHierarchyAdmin('self-parent-admin', $this->superAdmin);

    expect(fn () => $this->hierarchyService->assertValidParent($admin, $admin))
        ->toThrow(DomainException::class, 'An Admin cannot be its own Parent Admin.');
});

test('assigning an Admin under its own Descendant Admin is rejected', function () {
    $adminA = createHierarchyAdmin('cycle-admin-a', $this->superAdmin);
    $adminB = createHierarchyAdmin('cycle-admin-b', $adminA);
    $adminC = createHierarchyAdmin('cycle-admin-c', $adminB);

    expect(fn () => $this->hierarchyService->assertValidParent($adminA, $adminC))
        ->toThrow(DomainException::class, 'An Admin cannot be assigned under one of its own Descendant Admins.');
});

test('frontend system roles are excluded from the Admin Hierarchy', function (string $roleName) {
    $frontendUser = User::factory()->create(['is_active' => true]);
    $frontendUser->assignRole(Role::findOrCreate($roleName, 'web'));

    expect($this->hierarchyService->isAdminHierarchyMember($frontendUser))->toBeFalse()
        ->and($this->hierarchyService->getAllowedAdminIds($frontendUser))->toBeEmpty()
        ->and($this->hierarchyService->canManageAdmin($this->superAdmin, $frontendUser))->toBeFalse();

    $admin = createHierarchyAdmin('parent-for-'.$roleName, $this->superAdmin);

    expect(fn () => $this->hierarchyService->assertValidParent($admin, $frontendUser))
        ->toThrow(DomainException::class, 'The selected Parent Admin is not an Admin Hierarchy member.');
})->with(['user', 'consultant']);

test('hierarchy management recognizes self descendants and Super Admin access', function () {
    $adminA = createHierarchyAdmin('manageable-admin-a', $this->superAdmin);
    $adminB = createHierarchyAdmin('manageable-admin-b', $adminA);
    $unrelatedAdmin = createHierarchyAdmin('manageable-unrelated-admin', $this->superAdmin);

    expect($this->hierarchyService->canManageAdmin($adminA, $adminA))->toBeTrue()
        ->and($this->hierarchyService->canManageAdmin($adminA, $adminB))->toBeTrue()
        ->and($this->hierarchyService->canManageAdmin($adminA, $unrelatedAdmin))->toBeFalse()
        ->and($this->hierarchyService->canManageAdmin($this->superAdmin, $unrelatedAdmin))->toBeTrue();
});
