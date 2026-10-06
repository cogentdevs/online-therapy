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
        Schema::create('related_consultancies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consultancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_consultancy_id')->constrained('consultancies')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['consultancy_id', 'related_consultancy_id'], 'rel_consultancy_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_consultancies');
    }
};
