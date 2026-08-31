<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('package_photos');
        Schema::dropIfExists('package_variant_photos');
    }

    public function down()
    {
        if (!Schema::hasTable('package_photos')) {
            Schema::create('package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->string('photo_path');
                $table->string('caption')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('package_variant_photos')) {
            Schema::create('package_variant_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_variant_id')->constrained('package_variants')->cascadeOnDelete();
                $table->string('photo_path');
                $table->string('caption')->nullable();
                $table->string('category', 50)->default('gallery');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }
};
