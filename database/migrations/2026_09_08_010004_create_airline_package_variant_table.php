<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airline_package_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->cascadeOnDelete();
            $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['airline_id', 'package_variant_id'], 'airline_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airline_package_variant');
    }
};
