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
        Schema::create('taza_shumara', function (Blueprint $table) {
            $table->id();
            $table->string('language')->index();
            $table->foreignId('magazine_id')->constrained('magazines')->restrictOnDelete();
            $table->string('cover_image')->nullable();
            $table->boolean('show_title')->default(true);
            $table->boolean('show_short_description')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['language', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taza_shumara');
    }
};
