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
        Schema::create('taza_shumara_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taza_shumara_id')->constrained('taza_shumara')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->restrictOnDelete();
            $table->string('display_width');
            $table->string('position')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['taza_shumara_id', 'article_id']);
            $table->index(['taza_shumara_id', 'position', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taza_shumara_articles');
    }
};
