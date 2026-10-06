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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('payment_method')->constrained('payment_accounts')->nullOnDelete();
            $table->string('payment_slip')->nullable()->after('payment_account_id');
            $table->string('payment_status', 32)->nullable()->after('payment_slip')->index();
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_status');
            $table->timestamp('reviewed_at')->nullable()->after('payment_submitted_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('reviewed_by');
            $table->unsignedInteger('duration_value_snapshot')->nullable()->after('rejection_reason');
            $table->string('duration_unit_snapshot')->nullable()->after('duration_value_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'payment_slip',
                'payment_status',
                'payment_submitted_at',
                'reviewed_at',
                'rejection_reason',
                'duration_value_snapshot',
                'duration_unit_snapshot',
            ]);
        });
    }
};
