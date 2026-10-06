<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $eligibleAdminIds = DB::table('users')
            ->whereNotExists(function (Builder $query): void {
                $query->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.guard_name', 'web')
                    ->whereIn('roles.name', ['user', 'consultant']);
            })
            ->whereExists(function (Builder $query): void {
                $query->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.guard_name', 'web')
                    ->where(function (Builder $roleQuery): void {
                        $roleQuery->where('roles.name', 'super-admin')
                            ->orWhere(function (Builder $customRoleQuery): void {
                                $customRoleQuery
                                    ->whereNotIn('roles.name', ['super-admin', 'user', 'consultant'])
                                    ->whereExists(function (Builder $permissionQuery): void {
                                        $permissionQuery->selectRaw('1')
                                            ->from('role_has_permissions')
                                            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                                            ->whereColumn('role_has_permissions.role_id', 'roles.id')
                                            ->where('permissions.name', 'admin.access')
                                            ->where('permissions.guard_name', 'web');
                                    });
                            });
                    });
            })
            ->pluck('users.id');

        if ($eligibleAdminIds->isEmpty()) {
            return;
        }

        foreach (['magazines', 'articles'] as $tableName) {
            DB::table($tableName)
                ->whereNull('owner_admin_id')
                ->whereIn('created_by', $eligibleAdminIds)
                ->update(['owner_admin_id' => DB::raw('created_by')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Historical ownership cannot be distinguished safely from later assignments.
    }
};
