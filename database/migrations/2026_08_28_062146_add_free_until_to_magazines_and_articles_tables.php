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
            $table->date('free_until')->nullable()->after('isFree');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->date('free_until')->nullable()->after('isFree');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->dropColumn('free_until');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('free_until');
        });
    }
};
