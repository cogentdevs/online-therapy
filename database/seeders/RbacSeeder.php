<?php

namespace Database\Seeders;

use App\Services\Authorization\PermissionSyncService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PermissionSyncService $permissionSyncService): void
    {
        $guard = $permissionSyncService->guardName();

        foreach (array_keys((array) config('admin_modules.system_roles', [])) as $roleName) {
            Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guard,
            ]);
        }

        $permissionSyncService->sync();
    }
}
