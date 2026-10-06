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
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('show_on_latest')->default(false)->after('isFeatured');
            $table->boolean('show_on_editorial_center')->default(false)->after('show_on_latest');
            $table->boolean('show_on_editorial_featured')->default(false)->after('show_on_editorial_center');
            $table->boolean('show_visit_counter')->default(false)->after('show_on_editorial_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'show_on_latest',
                'show_on_editorial_center',
                'show_on_editorial_featured',
                'show_visit_counter',
            ]);
        });
    }
};
