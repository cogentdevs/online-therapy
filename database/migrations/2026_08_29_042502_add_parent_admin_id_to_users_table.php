<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('parent_admin_id')
                ->nullable()
                ->after('updated_by')
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });

        $superAdminIds = DB::table('users')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.name', 'super-admin')
                    ->where('roles.guard_name', 'web');
            })
            ->orderBy('users.id')
            ->limit(2)
            ->pluck('users.id');

        if ($superAdminIds->count() !== 1) {
            return;
        }

        $superAdminId = $superAdminIds->first();

        $customAdminIds = DB::table('users')
            ->where('users.created_by', $superAdminId)
            ->whereNull('users.parent_admin_id')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.guard_name', 'web')
                    ->whereNotIn('roles.name', ['super-admin', 'user', 'consultant'])
                    ->whereExists(function ($permissionQuery): void {
                        $permissionQuery->selectRaw('1')
                            ->from('role_has_permissions')
                            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                            ->whereColumn('role_has_permissions.role_id', 'roles.id')
                            ->where('permissions.name', 'admin.access')
                            ->where('permissions.guard_name', 'web');
                    });
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.guard_name', 'web')
                    ->whereIn('roles.name', ['super-admin', 'user', 'consultant']);
            })
            ->pluck('users.id');

        if ($customAdminIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $customAdminIds)
                ->update(['parent_admin_id' => $superAdminId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('parent_admin_id');
        });
    }
};
