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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('language')->nullable()->index();
            $table->string('title');
            $table->string('page_name')->index();
            $table->string('place')->index();
            $table->string('ad_image')->nullable();
            $table->string('ad_url', 2048)->nullable();
            $table->longText('google_ad_code')->nullable();
            $table->date('start_date')->nullable()->index();
            $table->date('expiry_date')->nullable()->index();
            $table->boolean('isActive')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['page_name', 'place', 'isActive']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
