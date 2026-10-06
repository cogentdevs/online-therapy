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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->text('footer_text')->nullable()->after('footer_logo');
            $table->string('linkedin', 255)->nullable()->after('youtube');
            $table->string('app_section_heading', 255)->nullable()->after('x');
            $table->text('app_section_text')->nullable()->after('app_section_heading');
            $table->string('play_store_icon', 255)->nullable()->after('app_section_text');
            $table->string('play_store_link', 255)->nullable()->after('play_store_icon');
            $table->string('app_store_icon', 255)->nullable()->after('play_store_link');
            $table->string('app_store_link', 255)->nullable()->after('app_store_icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_text',
                'linkedin',
                'app_section_heading',
                'app_section_text',
                'play_store_icon',
                'play_store_link',
                'app_store_icon',
                'app_store_link',
            ]);
        });
    }
};
