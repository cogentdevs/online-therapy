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
        Schema::table('ads', function (Blueprint $table) {
            $table->foreignId('ad_request_id')->nullable()->constrained('ad_requests')->nullOnDelete();
            $table->foreignId('ad_request_placement_id')->nullable()->unique()->constrained('ad_request_placements')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ad_request_placement_id');
            $table->dropConstrainedForeignId('ad_request_id');
        });
    }
};
