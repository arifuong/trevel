<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_variants', function (Blueprint $table) {
            // Add FK references to master hotels
            $table->foreignId('hotel_makkah_id')
                  ->nullable()
                  ->after('sort_order')
                  ->constrained('hotels')
                  ->nullOnDelete();

            $table->foreignId('hotel_madinah_id')
                  ->nullable()
                  ->after('hotel_makkah_id')
                  ->constrained('hotels')
                  ->nullOnDelete();

            // Override flags for include/exclude at variant level
            $table->boolean('has_include_override')->default(false)->after('hotel_madinah_id');
            $table->boolean('has_exclude_override')->default(false)->after('has_include_override');
        });
    }

    public function down(): void
    {
        Schema::table('package_variants', function (Blueprint $table) {
            $table->dropForeign(['hotel_makkah_id']);
            $table->dropForeign(['hotel_madinah_id']);
            $table->dropColumn([
                'hotel_makkah_id',
                'hotel_madinah_id',
                'has_include_override',
                'has_exclude_override',
            ]);
        });
    }
};
