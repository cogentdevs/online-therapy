<?php

namespace App\Services\Authorization;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSyncService
{
    /**
     * @return array{created: Collection<int, string>, existing: Collection<int, string>, obsolete: Collection<int, string>, registered: Collection<int, string>}
     */
    public function sync(): array
    {
        $guard = $this->guardName();
        $registered = $this->registeredPermissions();
        $existingBeforeSync = Permission::query()->where('guard_name', $guard)->pluck('name');

        foreach ($registered as $permissionName) {
            Permission::findOrCreate($permissionName, $guard);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return [
            'created' => $registered->diff($existingBeforeSync)->values(),
            'existing' => $registered->intersect($existingBeforeSync)->values(),
            'obsolete' => $existingBeforeSync->diff($registered)->values(),
            'registered' => $registered,
        ];
    }

    /** @return Collection<int, string> */
    public function registeredPermissions(): Collection
    {
        $permissions = collect([(string) config('admin_modules.access_permission')]);

        foreach ((array) config('admin_modules.modules', []) as $module => $definition) {
            foreach (array_keys((array) ($definition['actions'] ?? [])) as $action) {
                $permissions->push($module.'.'.$action);
            }
        }

        return $permissions->filter()->unique()->sort()->values();
    }

    public function guardName(): string
    {
        return (string) config('admin_modules.guard', 'web');
    }
}
