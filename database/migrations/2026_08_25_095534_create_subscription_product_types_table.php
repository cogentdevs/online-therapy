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
        Schema::create('subscription_product_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subscription_product_id', 'subscription_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_product_types');
    }
};
