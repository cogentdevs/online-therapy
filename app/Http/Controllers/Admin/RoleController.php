<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\ActivityLogService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private PermissionSyncService $permissionSyncService,
        private ActivityLogService $activityLogService,
    ) {}

    public function index(): View
    {
        return view('admin.roles.view-roles', [
            'roles' => Role::query()
                ->where('guard_name', $this->permissionSyncService->guardName())
                ->withCount(['permissions', 'users'])
                ->orderBy('name')
                ->get(),
            'systemRoleNames' => $this->systemRoleNames(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.add-role', [
            'modules' => (array) config('admin_modules.modules', []),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = DB::transaction(function () use ($request): Role {
            $role = Role::query()->create([
                'name' => $request->validated('name'),
                'guard_name' => $this->permissionSyncService->guardName(),
            ]);

            $role->syncPermissions($this->permissionsWithAdminAccess($request->validated('permissions', [])));

            return $role;
        });

        $this->activityLogService->log(
            'roles',
            'created',
            $role,
            "Created Admin role \"{$role->name}\".",
            null,
            ['name' => $role->name, 'permissions' => $role->permissions()->pluck('name')->all()],
        );

        return redirect()->route('admin.roles.index')->with('status', "Role {$role->name} created successfully.");
    }

    public function edit(int $id): View
    {
        $role = $this->findCustomRole($id);

        return view('admin.roles.edit-role', [
            'role' => $role,
            'modules' => (array) config('admin_modules.modules', []),
            'selectedPermissions' => $role->permissions()->pluck('name')->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, int $id): RedirectResponse
    {
        $role = $this->findCustomRole($id);
        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->sort()->values()->all(),
        ];

        DB::transaction(function () use ($request, $role): void {
            $role->update(['name' => $request->validated('name')]);
            $role->syncPermissions($this->permissionsWithAdminAccess($request->validated('permissions', [])));
        });

        $role->refresh();
        $newValues = [
            'name' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->sort()->values()->all(),
        ];
        $this->activityLogService->log(
            'roles',
            $oldValues['permissions'] === $newValues['permissions'] ? 'updated' : 'permissions_changed',
            $role,
            $oldValues['permissions'] === $newValues['permissions']
                ? "Updated Admin role \"{$role->name}\"."
                : "Changed {$role->name} role permissions.",
            $oldValues,
            $newValues,
        );

        return redirect()->route('admin.roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = $this->findCustomRole($id);

        if ($role->users()->exists()) {
            return back()->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->all(),
        ];
        $roleName = $role->name;

        DB::transaction(fn () => $role->delete());

        $this->activityLogService->log(
            'roles',
            'deleted',
            $role,
            "Deleted Admin role \"{$roleName}\".",
            $oldValues,
        );

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted successfully.');
    }

    /** @param array<int, string> $permissions */
    private function permissionsWithAdminAccess(array $permissions): array
    {
        $permissionNames = collect($permissions)
            ->push((string) config('admin_modules.access_permission'))
            ->unique()
            ->values();

        return $permissionNames
            ->map(fn (string $permissionName) => Permission::findByName(
                $permissionName,
                $this->permissionSyncService->guardName(),
            ))
            ->all();
    }

    private function findCustomRole(int $id): Role
    {
        $role = Role::query()
            ->where('guard_name', $this->permissionSyncService->guardName())
            ->findOrFail($id);

        abort_if(in_array($role->name, $this->systemRoleNames(), true), 403);

        return $role;
    }

    /** @return array<int, string> */
    private function systemRoleNames(): array
    {
        return array_keys((array) config('admin_modules.system_roles', []));
    }
}
