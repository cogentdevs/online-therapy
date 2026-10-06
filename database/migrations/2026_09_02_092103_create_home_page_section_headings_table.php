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
        Schema::create('home_page_section_headings', function (Blueprint $table) {
            $table->id();
            $table->string('language')->nullable();
            $table->string('section_name')->nullable();
            $table->string('content_position')->nullable();
            $table->string('short_title')->nullable();
            $table->string('main_title')->nullable();
            $table->string('short_detail')->nullable();
            $table->timestamps();

            $table->unique(['language', 'section_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_section_headings');
    }
};
