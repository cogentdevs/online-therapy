<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_campaign_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('newsletter_campaign_id')->constrained()->cascadeOnDelete();
            $table->string('content_type');
            $table->unsignedBigInteger('content_id');
            $table->unsignedInteger('sort_order');
            $table->timestamps();
            $table->index(
                ['newsletter_campaign_id', 'sort_order'],
                'nl_campaign_sort_idx'
            );

            $table->unique(
                ['newsletter_campaign_id', 'content_type', 'content_id'],
                'nl_campaign_content_uq'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaign_contents');
    }
};
