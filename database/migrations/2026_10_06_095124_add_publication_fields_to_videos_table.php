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
        Schema::table('videos', function (Blueprint $table): void {
            $table->string('status')->nullable()->default('published')->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('published_by');
            $table->dropIndex(['published_at']);
            $table->dropIndex(['status']);
            $table->dropColumn(['published_at', 'status']);
        });
    }
};
