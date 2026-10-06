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
        Schema::create('subscription_expiry_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_subscription_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('reminder_days');
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['user_subscription_id', 'reminder_days'], 'subscription_expiry_reminders_subscription_days_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_expiry_reminders');
    }
};
