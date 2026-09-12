<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop deprecated columns from package_variants and old pivot tables.
     *
     * WARNING: Run this AFTER verifying all new master data tables and
     * relationships are working correctly. If data migration from old
     * columns is needed, run it BEFORE this migration.
     */
    public function up(): void
    {
        // 1. Drop old airline/hotel columns from package_variants
        Schema::table('package_variants', function (Blueprint $table) {
            $table->dropColumn([
                'airline_departure',
                'airline_departure_logo',
                'airline_return',
                'airline_return_logo',
                'hotel_makkah_name',
                'hotel_makkah_star',
                'hotel_makkah_description',
                'hotel_madinah_name',
                'hotel_madinah_star',
                'hotel_madinah_description',
            ]);
        });

        // 2. Drop old pivot tables (data now lives in hotel_photos and hotel_hotel_facility)
        Schema::dropIfExists('package_variant_hotel_facilities');
        Schema::dropIfExists('package_variant_hotel_photos');
    }

    public function down(): void
    {
        // Recreate old columns on package_variants
        Schema::table('package_variants', function (Blueprint $table) {
            $table->string('airline_departure')->nullable()->after('sort_order');
            $table->string('airline_departure_logo')->nullable()->after('airline_departure');
            $table->string('airline_return')->nullable()->after('airline_departure_logo');
            $table->string('airline_return_logo')->nullable()->after('airline_return');
            $table->string('hotel_makkah_name')->nullable()->after('airline_return_logo');
            $table->string('hotel_makkah_star', 50)->nullable()->after('hotel_makkah_name');
            $table->text('hotel_makkah_description')->nullable()->after('hotel_makkah_star');
            $table->string('hotel_madinah_name')->nullable()->after('hotel_makkah_description');
            $table->string('hotel_madinah_star', 50)->nullable()->after('hotel_madinah_name');
            $table->text('hotel_madinah_description')->nullable()->after('hotel_madinah_star');
        });

        // Recreate old pivot tables
        Schema::create('package_variant_hotel_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->string('hotel_type', 20);
            $table->string('photo_path');
            $table->string('category', 50)->default('gallery');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('package_variant_hotel_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->foreignId('hotel_facility_id')->constrained('hotel_facilities')->cascadeOnDelete();
            $table->string('hotel_type', 20);
            $table->timestamps();
            $table->unique(['package_variant_id', 'hotel_facility_id', 'hotel_type'], 'pvhf_unique');
        });
    }
};
