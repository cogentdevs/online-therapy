<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\TransferAdminContentOwnershipRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AdminContentOwnershipTransferService;
use App\Services\AdminHierarchyService;
use App\Services\Authorization\AdminUserPermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private AdminUserPermissionService $adminUserPermissionService,
        private AdminHierarchyService $adminHierarchyService,
        private AdminContentOwnershipTransferService $adminContentOwnershipTransferService,
        private ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): View
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $users = $this->scopeAdminUsersForActor($this->adminUsersQuery(), $actor)
            ->with(['roles.permissions', 'creator.roles'])
            ->latest()
            ->get();

        return view('admin.users.view-users', [
            'users' => $users,
            'transferableSourceIds' => $this->adminContentOwnershipTransferService
                ->transferableSourceIds($actor, $users),
        ]);
    }

    public function transferOwnership(Request $request, int $id): View
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $sourceAdmin = $this->adminContentOwnershipTransferService->findAuthorizedSource($actor, $id);
        $newOwners = $this->adminContentOwnershipTransferService->eligibleNewOwners($actor, $sourceAdmin);
        abort_if($newOwners->isEmpty(), 403);

        return view('admin.users.transfer-ownership', [
            'sourceAdmin' => $sourceAdmin,
            'newOwners' => $newOwners,
            'ownedContentCounts' => $this->adminContentOwnershipTransferService->previewCounts($sourceAdmin),
        ]);
    }

    public function storeOwnershipTransfer(
        TransferAdminContentOwnershipRequest $request,
        int $id,
    ): RedirectResponse {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $sourceAdmin = $this->adminContentOwnershipTransferService->findAuthorizedSource($actor, $id);
        $newOwner = User::query()->findOrFail((int) $request->validated('new_owner_id'));
        $result = $this->adminContentOwnershipTransferService->transfer(
            $actor,
            $sourceAdmin,
            $newOwner,
            $request->validated('content_types'),
            $request->boolean('deactivate_source'),
        );

        return redirect()->route('admin.users.index')->with(
            'status',
            "Ownership transferred successfully. Magazines transferred: {$result['magazines']}; "
                ."Articles transferred: {$result['articles']}; "
                ."Child Admins reassigned: {$result['child_admins']}; "
                .'Current Owner deactivated: '.($result['source_deactivated'] ? 'Yes' : 'No').'.',
        );
    }

    public function create(Request $request): View
    {
        return view('admin.users.add-user', [
            'roles' => $this->rolesAssignableBy($request->user()),
            'selectedPermissionMode' => old('permission_mode', AdminUserPermissionService::MODE_ROLE),
            'selectedCustomPermissions' => old('permissions', []),
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $role = $this->findEligibleAdminRole((int) $request->validated('role_id'));
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        abort_unless($this->adminUserPermissionService->canAssignRole($actor, $role), 403);

        $user = DB::transaction(function () use ($actor, $request, $role): User {
            $user = User::query()->newModelInstance([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'password' => Hash::make($request->validated('password')),
                'is_active' => $request->boolean('is_active'),
            ]);
            $user->parent_admin_id = $actor->id;
            $user->save();

            $this->adminUserPermissionService->synchronize(
                $user,
                $role,
                $request->validated('permission_mode'),
                $request->validated('permissions', []),
            );
            $this->adminHierarchyService->assertValidParent($user, $actor);

            return $user;
        });

        $this->activityLogService->log(
            'users',
            'created',
            $user,
            "Created Admin user \"{$user->name}\" with role {$role->name}.",
            null,
            $this->userAuditValues($user, $role->name),
        );

        return redirect()->route('admin.users.index')->with('status', "Admin user {$user->name} created successfully.");
    }

    public function edit(Request $request, int $id): View
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $user = $this->findManagedAdminUser($id, $actor);
        $selectedRole = $this->eligibleAdminRolesQuery()
            ->whereHas('users', fn (Builder $query) => $query->whereKey($user->id))
            ->firstOrFail();

        if (! $actor->is($user)) {
            abort_unless($this->adminUserPermissionService->canAssignRole($actor, $selectedRole), 403);
        }

        return view('admin.users.edit-user', [
            'managedUser' => $user,
            'roles' => $actor->is($user) ? collect([$selectedRole]) : $this->rolesAssignableBy($actor),
            'selectedRoleId' => $selectedRole->id,
            'selectedPermissionMode' => old('permission_mode', $user->permission_mode),
            'selectedCustomPermissions' => old('permissions', $user->permissions()->pluck('name')->all()),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, int $id): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $user = $this->findManagedAdminUser($id, $actor);
        $role = $this->findEligibleAdminRole((int) $request->validated('role_id'));
        $currentRoleId = $this->eligibleAdminRolesQuery()
            ->whereHas('users', fn (Builder $query) => $query->whereKey($user->id))
            ->value('id');
        $currentRoleName = $user->roles()->value('name');
        $oldValues = $this->userAuditValues($user, $currentRoleName);

        if ($request->user()->is($user) && ! $request->boolean('is_active')) {
            return back()->withInput()->with('error', 'You cannot deactivate your own Admin account.');
        }

        if ($request->user()->is($user) && $currentRoleId !== $role->id) {
            return back()->withInput()->with('error', 'You cannot change your own Admin role.');
        }

        if ($request->user()->is($user) && $this->changesOwnPermissionScope($request, $user)) {
            return back()->withInput()->with('error', 'You cannot change your own Admin permission scope.');
        }

        if (! $actor->is($user)) {
            abort_unless($this->adminUserPermissionService->canAssignRole($actor, $role), 403);
            abort_unless($this->adminUserPermissionService->permissionsWithinActorScope(
                $actor,
                $request->validated('permission_mode') === AdminUserPermissionService::MODE_CUSTOM
                    ? $request->validated('permissions', [])
                    : [],
            ), 403);
        }

        if ($user->is_active && ! $request->boolean('is_active')
            && $this->adminHierarchyService->hasActiveChildAdmins($user)) {
            return back()->withInput()->with('error', 'This Admin cannot be deactivated while active Child Admins are assigned to it.');
        }

        $passwordChanged = filled($request->validated('password'));

        DB::transaction(function () use ($request, $role, $user): void {
            $attributes = [
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'is_active' => $request->boolean('is_active'),
            ];

            if (filled($request->validated('password'))) {
                $attributes['password'] = Hash::make($request->validated('password'));
            }

            $user->update($attributes);
            $this->adminUserPermissionService->synchronize(
                $user,
                $role,
                $request->validated('permission_mode'),
                $request->validated('permissions', []),
            );
        });

        $user->refresh();
        $newValues = $this->userAuditValues($user, $role->name);
        $this->logUserChanges($user, $oldValues, $newValues, $passwordChanged);

        return redirect()->route('admin.users.index')->with('status', 'Admin user updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $user = $this->findManagedAdminUser($id, $actor);

        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot delete your own Admin account.');
        }

        if ($this->adminHierarchyService->hasChildAdmins($user)) {
            return back()->with('error', 'This Admin cannot be deleted while Child Admins are assigned to it.');
        }

        $roleName = $user->roles()->value('name');
        $oldValues = $this->userAuditValues($user, $roleName);
        $userName = $user->name;

        DB::transaction(function () use ($user): void {
            $user->syncPermissions([]);
            $user->syncRoles([]);
            $user->delete();
        });

        $this->activityLogService->log(
            'users',
            'deleted',
            $user,
            "Deleted Admin user \"{$userName}\".",
            $oldValues,
        );

        return redirect()->route('admin.users.index')->with('status', 'Admin user deleted successfully.');
    }

    public function rolePermissions(Request $request, int $role): JsonResponse
    {
        $eligibleRole = $this->findEligibleAdminRole($role);
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $actorHasRole = $actor->roles()->whereKey($eligibleRole->id)->exists();
        abort_unless($actorHasRole || $this->adminUserPermissionService->canAssignRole($actor, $eligibleRole), 403);
        $assignedPermissions = $this->adminUserPermissionService
            ->visibleRolePermissionNames($actor, $eligibleRole)
            ->flip();

        $groups = collect((array) config('admin_modules.modules', []))
            ->groupBy('group', true)
            ->map(function ($modules, string $group) use ($assignedPermissions): array {
                return [
                    'label' => $group,
                    'modules' => $modules->map(function (array $module, string $moduleName) use ($assignedPermissions): array {
                        $actions = collect($module['actions'])->map(function (string $label, string $action) use ($moduleName): array {
                            $permissionName = $moduleName.'.'.$action;

                            return [
                                'name' => $permissionName,
                                'label' => $label,
                            ];
                        })->filter(fn (array $action): bool => $assignedPermissions->has($action['name']))->values();

                        return [
                            'name' => $moduleName,
                            'label' => $module['label'],
                            'actions' => $actions,
                        ];
                    })->filter(fn (array $module): bool => $module['actions']->isNotEmpty())->values(),
                ];
            })->filter(fn (array $group): bool => $group['modules']->isNotEmpty())->values();

        return response()->json([
            'role' => ['id' => $eligibleRole->id, 'name' => $eligibleRole->name],
            'admin_access' => true,
            'groups' => $groups,
        ]);
    }

    private function findManagedAdminUser(int $id, User $actor): User
    {
        $user = $this->managedAdminUsersQuery()->findOrFail($id);
        abort_unless($this->adminHierarchyService->canManageAdmin($actor, $user), 403);

        return $user;
    }

    private function findEligibleAdminRole(int $id): Role
    {
        return $this->eligibleAdminRolesQuery()->findOrFail($id);
    }

    private function adminUsersQuery(): Builder
    {
        return User::query()->whereHas('roles', function (Builder $query): void {
            $query->where('guard_name', $this->guardName())
                ->where(function (Builder $roleQuery): void {
                    $roleQuery->where('name', 'super-admin')
                        ->orWhere(function (Builder $customRoleQuery): void {
                            $this->scopeEligibleAdminRoles($customRoleQuery);
                        });
                });
        });
    }

    private function managedAdminUsersQuery(): Builder
    {
        return User::query()->whereHas('roles', function (Builder $query): void {
            $this->scopeEligibleAdminRoles($query);
        });
    }

    private function eligibleAdminRolesQuery(): Builder
    {
        return Role::query()->where(function (Builder $query): void {
            $this->scopeEligibleAdminRoles($query);
        });
    }

    /** @return Collection<int, Role> */
    private function rolesAssignableBy(User $actor): Collection
    {
        return $this->eligibleAdminRolesQuery()
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->filter(fn (Role $role): bool => $this->adminUserPermissionService->canAssignRole($actor, $role))
            ->values();
    }

    private function scopeAdminUsersForActor(Builder $query, User $actor): Builder
    {
        $allowedAdminIds = $this->adminHierarchyService->getAllowedAdminIds($actor);

        return $allowedAdminIds === null ? $query : $query->whereKey($allowedAdminIds);
    }

    private function scopeEligibleAdminRoles(Builder $query): void
    {
        $query->where('guard_name', $this->guardName())
            ->whereNotIn('name', $this->systemRoleNames())
            ->whereHas('permissions', fn (Builder $permissionQuery) => $permissionQuery
                ->where('name', (string) config('admin_modules.access_permission'))
                ->where('guard_name', $this->guardName()));
    }

    /** @return array<int, string> */
    private function systemRoleNames(): array
    {
        return array_keys((array) config('admin_modules.system_roles', []));
    }

    private function guardName(): string
    {
        return (string) config('admin_modules.guard', 'web');
    }

    private function changesOwnPermissionScope(UpdateAdminUserRequest $request, User $user): bool
    {
        $submittedMode = $request->validated('permission_mode');

        if ($submittedMode !== $user->permission_mode) {
            return true;
        }

        if ($submittedMode !== AdminUserPermissionService::MODE_CUSTOM) {
            return false;
        }

        $currentPermissions = $user->permissions()->pluck('name')->sort()->values();
        $submittedPermissions = collect($request->validated('permissions', []))->sort()->values();

        return $currentPermissions->all() !== $submittedPermissions->all();
    }

    /** @return array<string, mixed> */
    private function userAuditValues(User $user, ?string $roleName): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'role' => $roleName,
            'permission_mode' => $user->permission_mode,
            'permissions' => $user->permissions()->pluck('name')->sort()->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    private function logUserChanges(User $user, array $oldValues, array $newValues, bool $passwordChanged): void
    {
        $changed = collect($newValues)
            ->filter(fn (mixed $value, string $key): bool => $value !== $oldValues[$key])
            ->keys();

        $profileChanges = $changed->intersect(['name', 'email', 'phone']);

        if ($profileChanges->isNotEmpty()) {
            $this->activityLogService->log(
                'users',
                'updated',
                $user,
                "Updated Admin user \"{$user->name}\".",
                collect($oldValues)->only($profileChanges)->all(),
                collect($newValues)->only($profileChanges)->all(),
            );
        }

        if ($changed->contains('role')) {
            $this->activityLogService->log('users', 'role_changed', $user, "Changed Admin user \"{$user->name}\" role from {$oldValues['role']} to {$newValues['role']}.", ['role' => $oldValues['role']], ['role' => $newValues['role']]);
        }

        if ($changed->contains('permission_mode')) {
            $this->activityLogService->log('users', 'permission_mode_changed', $user, "Changed Admin user \"{$user->name}\" permission mode.", ['permission_mode' => $oldValues['permission_mode']], ['permission_mode' => $newValues['permission_mode']]);
        }

        if ($changed->contains('permissions')) {
            $this->activityLogService->log('users', 'permissions_changed', $user, "Changed Admin user \"{$user->name}\" limited permissions.", ['permissions' => $oldValues['permissions']], ['permissions' => $newValues['permissions']]);
        }

        if ($changed->contains('is_active')) {
            $description = $newValues['is_active'] ? 'Activated' : 'Deactivated';
            $this->activityLogService->log('users', 'status_changed', $user, "{$description} Admin user \"{$user->name}\".", ['is_active' => $oldValues['is_active']], ['is_active' => $newValues['is_active']]);
        }

        if ($passwordChanged) {
            $this->activityLogService->log('users', 'password_changed', $user, "Changed Admin user \"{$user->name}\" password.");
        }
    }
}
