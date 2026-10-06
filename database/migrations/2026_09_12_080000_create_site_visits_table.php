<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('visitor_id')->index();
            $table->string('public_token_hash', 64)->unique();
            $table->string('page_key', 80)->index();
            $table->string('route_name')->nullable()->index();
            $table->text('url')->nullable();
            $table->text('referrer')->nullable();
            $table->nullableMorphs('visitable');
            $table->string('ip_address', 45)->nullable();
            $table->string('country')->nullable()->index();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable()->index();
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('device')->nullable();
            $table->string('device_type', 30)->nullable()->index();
            $table->string('browser')->nullable()->index();
            $table->string('browser_version', 40)->nullable();
            $table->string('os')->nullable();
            $table->string('os_version', 40)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('started_at')->index();
            $table->timestamp('last_activity_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
