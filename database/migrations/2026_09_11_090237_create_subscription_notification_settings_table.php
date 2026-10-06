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
        Schema::create('subscription_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('first_reminder_days')->nullable();
            $table->unsignedInteger('second_reminder_days')->nullable();
            $table->unsignedInteger('third_reminder_days')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_notification_settings');
    }
};
