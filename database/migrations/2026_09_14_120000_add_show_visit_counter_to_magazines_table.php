<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magazines', function (Blueprint $table): void {
            $table->boolean('show_visit_counter')->default(false)->after('isFeatured');
        });
    }

    public function down(): void
    {
        Schema::table('magazines', function (Blueprint $table): void {
            $table->dropColumn('show_visit_counter');
        });
    }
};
