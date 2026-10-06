<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->foreignId('owner_admin_id')
                ->nullable()
                ->after('published_by')
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('owner_admin_id')
                ->nullable()
                ->after('published_by')
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_admin_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_admin_id');
        });
    }
};
