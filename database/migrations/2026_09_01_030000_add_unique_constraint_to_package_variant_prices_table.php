<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_variant_prices', function (Blueprint $table) {
            $table->unique(['package_variant_id', 'room_type'], 'pkg_var_prices_var_room_unique');
        });
    }

    public function down(): void
    {
        Schema::table('package_variant_prices', function (Blueprint $table) {
            $table->dropUnique('pkg_var_prices_var_room_unique');
        });
    }
};
