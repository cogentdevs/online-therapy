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
        Schema::create('ad_request_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_request_id')->constrained()->cascadeOnDelete();
            $table->string('page_name', 50);
            $table->string('place', 100);
            $table->string('status', 32)->default('pending');
            $table->timestamps();
            $table->unique(['ad_request_id', 'page_name', 'place'], 'ad_request_placements_request_slot_unique');
            $table->index(['page_name', 'place', 'status'], 'ad_request_placements_slot_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_request_placements');
    }
};
