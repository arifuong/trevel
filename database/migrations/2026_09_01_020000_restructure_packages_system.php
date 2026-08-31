<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. MODIFY packages table
        Schema::table('packages', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('main_photo')->nullable()->after('status');
            $table->string('brochure_file')->nullable()->after('main_photo');
        });

        // 2. CREATE package_photos table
        Schema::create('package_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. CREATE package_variants table
        Schema::create('package_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('main_photo')->nullable();
            $table->string('brochure_file')->nullable();
            $table->integer('quota')->default(0);
            $table->string('status', 20)->default('aktif');
            $table->integer('sort_order')->default(0);
            $table->string('airline_departure')->nullable();
            $table->string('airline_departure_logo')->nullable();
            $table->string('airline_return')->nullable();
            $table->string('airline_return_logo')->nullable();
            $table->string('hotel_makkah_name')->nullable();
            $table->string('hotel_makkah_star', 50)->nullable();
            $table->text('hotel_makkah_description')->nullable();
            $table->string('hotel_madinah_name')->nullable();
            $table->string('hotel_madinah_star', 50)->nullable();
            $table->text('hotel_madinah_description')->nullable();
            $table->timestamps();
        });

        // 4. CREATE package_variant_prices table
        Schema::create('package_variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->string('room_type', 50);
            $table->decimal('normal_price', 15, 2);
            $table->decimal('promo_price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. CREATE package_variant_photos table
        Schema::create('package_variant_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->string('category', 50)->default('gallery');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. CREATE package_variant_hotel_photos table
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

        // 7. CREATE package_variant_includes table
        Schema::create('package_variant_includes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->string('item');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 8. CREATE package_variant_excludes table
        Schema::create('package_variant_excludes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->string('item');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 9. CREATE hotel_facilities table
        Schema::create('hotel_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $facilities = [
            'Kamar Keluarga', 'AC', 'Akses Wi-Fi Gratis', 'Lift', 'Makan 3x Sehari',
            'Kolam Renang', 'Gym / Pusat Kebugaran', 'Layanan Laundry',
            'Musholla / Ruang Sholat', 'Parkir Gratis', 'Restoran',
            'Layanan Kamar 24 Jam', 'Brankas', 'Pemandangan Kota', 'Pemandangan Ka\'bah'
        ];

        foreach ($facilities as $index => $facility) {
            DB::table('hotel_facilities')->insert([
                'name' => $facility,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 10. CREATE package_variant_hotel_facilities table
        Schema::create('package_variant_hotel_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->foreignId('hotel_facility_id')->constrained('hotel_facilities')->cascadeOnDelete();
            $table->string('hotel_type', 20);
            $table->timestamps();

            $table->unique(['package_variant_id', 'hotel_facility_id', 'hotel_type'], 'pvhf_unique');
        });

        // 11. MODIFY registrations table
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('package_variant_id')->nullable()->after('package_id')->constrained('package_variants')->nullOnDelete();
            $table->string('room_type', 50)->nullable()->after('package_variant_id');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['package_variant_id']);
            $table->dropColumn(['package_variant_id', 'room_type']);
        });

        Schema::dropIfExists('package_variant_hotel_facilities');
        Schema::dropIfExists('hotel_facilities');
        Schema::dropIfExists('package_variant_excludes');
        Schema::dropIfExists('package_variant_includes');
        Schema::dropIfExists('package_variant_hotel_photos');
        Schema::dropIfExists('package_variant_photos');
        Schema::dropIfExists('package_variant_prices');
        Schema::dropIfExists('package_variants');
        Schema::dropIfExists('package_photos');

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['description', 'main_photo', 'brochure_file']);
        });
    }
};
