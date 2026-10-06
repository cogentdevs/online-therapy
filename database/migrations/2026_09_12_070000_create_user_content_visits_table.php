<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_content_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('visitable');
            $table->timestamp('last_visited_at')->index();
            $table->timestamps();

            $table->unique(['user_id', 'visitable_type', 'visitable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_content_visits');
    }
};
