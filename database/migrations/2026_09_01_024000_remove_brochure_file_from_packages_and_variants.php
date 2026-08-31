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
        if (Schema::hasColumn('packages', 'brochure_file')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('brochure_file');
            });
        }

        if (Schema::hasColumn('package_variants', 'brochure_file')) {
            Schema::table('package_variants', function (Blueprint $table) {
                $table->dropColumn('brochure_file');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('packages', 'brochure_file')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->string('brochure_file')->nullable()->after('main_photo');
            });
        }

        if (!Schema::hasColumn('package_variants', 'brochure_file')) {
            Schema::table('package_variants', function (Blueprint $table) {
                $table->string('brochure_file')->nullable()->after('main_photo');
            });
        }
    }
};
