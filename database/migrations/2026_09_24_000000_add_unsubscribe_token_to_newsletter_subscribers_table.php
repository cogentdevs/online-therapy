<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            $table->string('unsubscribe_token', 64)->nullable()->after('email');
            $table->unique('unsubscribe_token', 'nl_sub_unsub_token_uq');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            $table->dropUnique('nl_sub_unsub_token_uq');
            $table->dropColumn('unsubscribe_token');
        });
    }
};
