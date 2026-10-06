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
        Schema::create('media_storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('media_type')->nullable();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->foreignId('storage_provider_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('checksum')->nullable();
            $table->boolean('is_primary')->nullable()->index();
            $table->integer('priority')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('verification_status')->nullable()->index();
            $table->dateTime('last_verified_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['media_type', 'media_id']);
            $table->index(['media_type', 'media_id', 'status', 'is_primary'], 'media_location_resolution_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_storage_locations');
    }
};
