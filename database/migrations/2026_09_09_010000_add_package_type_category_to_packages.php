<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menambahkan kolom package_type (umrah/haji) dan category_label
     * untuk menggantikan logika hardcode badge kategori di LandingController.
     *
     * - package_type: enum string 'umrah' atau 'haji', default 'umrah'
     * - category_label: label custom badge (mis. "Umrah Kemerdekaan", "Haji Mujamalah")
     *   Jika null, akan di-fallback ke auto-label dari package_type di accessor model.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('package_type', 20)->default('umrah')->after('status');
            $table->string('category_label')->nullable()->after('package_type');
        });

        // Auto-populate package_type untuk paket existing berdasarkan nama
        DB::table('packages')
            ->where('name', 'LIKE', '%haji%')
            ->orWhere('name', 'LIKE', '%Haji%')
            ->orWhere('name', 'LIKE', '%HAJI%')
            ->update(['package_type' => 'haji']);
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['package_type', 'category_label']);
        });
    }
};
