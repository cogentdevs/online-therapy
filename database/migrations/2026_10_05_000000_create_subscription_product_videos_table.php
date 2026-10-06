<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_product_videos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscription_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subscription_product_id', 'video_id'], 'sp_video_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_product_videos');
    }
};
