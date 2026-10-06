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
        Schema::create('related_magazines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magazine_id')->constrained('magazines')->cascadeOnDelete();
            $table->foreignId('related_magazine_id')->constrained('magazines')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['magazine_id', 'related_magazine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_magazines');
    }
};
