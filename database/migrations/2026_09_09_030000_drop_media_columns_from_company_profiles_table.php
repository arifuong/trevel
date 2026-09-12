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
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'video_url', 'video_title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('video_url', 500)->nullable();
            $table->string('video_title', 255)->nullable()->default('Profil PT. Zein Internasional');
            $table->string('hero_image', 500)->nullable();
        });
    }
};
