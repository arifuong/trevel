<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('PT. ZEIN INTERNASIONAL');
            $table->string('video_url', 500)->nullable();
            $table->string('video_title', 255)->nullable()->default('Profil PT. Zein Internasional');
            $table->timestamps();
        });

        // Seed default profile dengan URL YouTube yang ditentukan
        DB::table('company_profiles')->insert([
            'name' => 'PT. ZEIN INTERNASIONAL',
            'video_url' => 'https://youtu.be/QrYcpXEC0RU',
            'video_title' => 'Profil PT. Zein Internasional',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};