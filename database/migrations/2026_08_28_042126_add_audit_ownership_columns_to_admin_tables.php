<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<int, string> */
    private array $tables = [
        'users', 'general_settings', 'sliders', 'banners', 'faqs', 'authors',
        'author_general_settings', 'tags', 'categories', 'meta_tags', 'currencies',
        'subscription_products', 'magazines', 'articles',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        foreach (['magazines', 'articles'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        $superAdminIds = DB::table('users')
            ->join('model_has_roles', function ($join): void {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'super-admin')
            ->where('roles.guard_name', 'web')
            ->orderBy('users.id')
            ->limit(2)
            ->pluck('users.id');

        $superAdminId = $superAdminIds->count() === 1 ? $superAdminIds->first() : null;

        if ($superAdminId !== null) {
            foreach ($this->tables as $tableName) {
                DB::table($tableName)->whereNull('created_by')->update(['created_by' => $superAdminId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['magazines', 'articles'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('published_by');
            });
        }

        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('updated_by');
                $table->dropConstrainedForeignId('created_by');
            });
        }
    }
};
