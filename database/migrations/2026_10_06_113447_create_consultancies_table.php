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
        Schema::create('consultancies', function (Blueprint $table) {
            $table->id();
            $table->string('language')->nullable()->index();
            $table->string('title');
            $table->string('button_label', 100);
            $table->string('image')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description');
            $table->string('duration_type');
            $table->unsignedInteger('duration_value');
            $table->string('consultancy_medium');
            $table->boolean('isActive')->default(true)->index();
            $table->boolean('isFeatured')->default(false)->index();
            $table->string('status')->default('draft')->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_admin_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultancies');
    }
};
