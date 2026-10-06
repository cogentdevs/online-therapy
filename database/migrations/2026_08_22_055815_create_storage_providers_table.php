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
        Schema::create('storage_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('disk')->nullable()->unique();
            $table->string('provider_type')->nullable();
            $table->boolean('is_active')->nullable()->index();
            $table->boolean('is_default')->nullable()->index();
            $table->integer('priority')->nullable()->index();
            $table->string('health_status')->nullable()->index();
            $table->dateTime('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_providers');
    }
};
