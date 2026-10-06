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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('contact_1')->nullable();
            $table->string('contact_2')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('facebook', 2048)->nullable();
            $table->string('instagram', 2048)->nullable();
            $table->string('youtube', 2048)->nullable();
            $table->string('tiktok', 2048)->nullable();
            $table->string('x', 2048)->nullable();
            $table->unsignedBigInteger('default_language_id')->nullable();
            $table->unsignedInteger('max_devices_per_user')->nullable();
            $table->unsignedInteger('max_concurrent_sessions')->nullable();
            $table->boolean('cookie_consent_enabled')->nullable();
            $table->boolean('maintenance_mode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
