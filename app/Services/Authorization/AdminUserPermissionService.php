<?php

namespace App\Services\Authorization;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class AdminUserPermissionService
{
    public const MODE_ROLE = 'role';

    public const MODE_CUSTOM = 'custom';

    /** @return Collection<int, string> */
    public function registeredPermissionNames(): Collection
    {
        return collect((array) config('admin_modules.modules', []))
            ->flatMap(fn (array $module, string $moduleName) => collect($module['actions'])
                ->keys()
                ->map(fn (string $action): string => $moduleName.'.'.$action))
            ->values();
    }

    /** @return Collection<int, string> */
    public function assignableRolePermissionNames(Role $role): Collection
    {
        return $role->permissions()
            ->where('guard_name', $this->guardName())
            ->whereIn('name', $this->registeredPermissionNames())
            ->pluck('name');
    }

    /** @return Collection<int, string> */
    public function effectivePermissionNames(User $user): Collection
    {
        if ($user->hasRole('super-admin')) {
            return $this->registeredPermissionNames();
        }

        if ($user->permission_mode === self::MODE_CUSTOM) {
            $role = $user->roles()
                ->where('guard_name', $this->guardName())
                ->whereNotIn('name', array_keys((array) config('admin_modules.system_roles', [])))
                ->first();

            if (! $role instanceof Role) {
                return collect();
            }

            return $this->assignableRolePermissionNames($role)
                ->intersect($user->permissions()->pluck('name'))
                ->values();
        }

        return $user->getAllPermissions()
            ->where('guard_name', $this->guardName())
            ->pluck('name')
            ->intersect($this->registeredPermissionNames())
            ->values();
    }

    public function canAssignRole(User $actor, Role $role): bool
    {
        if ($actor->hasRole('super-admin')) {
            return true;
        }

        return $this->assignableRolePermissionNames($role)
            ->diff($this->effectivePermissionNames($actor))
            ->isEmpty();
    }

    /** @param array<int, string> $permissions */
    public function permissionsWithinActorScope(User $actor, array $permissions): bool
    {
        if ($actor->hasRole('super-admin')) {
            return true;
        }

        return collect($permissions)
            ->diff($this->effectivePermissionNames($actor))
            ->isEmpty();
    }

    /** @return Collection<int, string> */
    public function visibleRolePermissionNames(User $actor, Role $role): Collection
    {
        $rolePermissions = $this->assignableRolePermissionNames($role);

        if ($actor->hasRole('super-admin')) {
            return $rolePermissions;
        }

        return $rolePermissions
            ->intersect($this->effectivePermissionNames($actor))
            ->values();
    }

    /** @param array<int, string> $permissions */
    public function synchronize(User $user, Role $role, string $mode, array $permissions): void
    {
        $user->syncRoles([$role]);
        $user->syncPermissions($mode === self::MODE_CUSTOM ? $permissions : []);
        $user->forceFill(['permission_mode' => $mode])->save();
    }

    public function customModeDecision(User $user, string $ability): ?bool
    {
        if ($user->permission_mode !== self::MODE_CUSTOM || ! $this->isRegisteredAdminPermission($ability)) {
            return null;
        }

        $user->loadMissing(['roles.permissions', 'permissions']);
        $systemRoles = array_keys((array) config('admin_modules.system_roles', []));
        $role = $user->roles->first(fn (Role $candidate): bool => $candidate->guard_name === $this->guardName()
            && ! in_array($candidate->name, $systemRoles, true));

        if (! $role || ! $role->permissions->contains('name', $this->adminAccessPermission())) {
            return false;
        }

        if ($ability === $this->adminAccessPermission()) {
            return true;
        }

        return $role->permissions->contains('name', $ability)
            && $user->permissions->contains('name', $ability);
    }

    public function isRegisteredAdminPermission(string $ability): bool
    {
        return $ability === $this->adminAccessPermission()
            || $this->registeredPermissionNames()->contains($ability);
    }

    private function adminAccessPermission(): string
    {
        return (string) config('admin_modules.access_permission', 'admin.access');
    }

    private function guardName(): string
    {
        return (string) config('admin_modules.guard', 'web');
    }
}
