<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('hero_image', 500)->nullable()->after('video_title');
        });

        // Set default foto gedung kantor Zein Tour untuk profil utama
        DB::table('company_profiles')->where('id', 1)->update([
            'hero_image' => 'company/kantor_zein_tour.jpg',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn('hero_image');
        });
    }
};