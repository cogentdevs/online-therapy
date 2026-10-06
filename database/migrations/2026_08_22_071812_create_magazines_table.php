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
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('language')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('issue_number')->nullable();
            $table->date('publish_date')->nullable()->index();
            $table->string('cover_image')->nullable();
            $table->dateTime('scheduled_at')->nullable()->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->text('description')->nullable();
            $table->boolean('isFree')->nullable()->default(false)->index();
            $table->boolean('isFeatured')->nullable()->default(false)->index();
            $table->boolean('isActive')->nullable()->default(true)->index();
            $table->string('status')->nullable()->default('draft')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
