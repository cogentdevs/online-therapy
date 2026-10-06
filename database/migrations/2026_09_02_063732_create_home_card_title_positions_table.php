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
        Schema::create('home_card_title_positions', function (Blueprint $table) {
            $table->id();
            $table->string('language');
            $table->string('card_position');
            $table->string('title_position');
            $table->timestamps();

            $table->unique(['language', 'card_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_card_title_positions');
    }
};
