<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city', 20); // 'makkah' | 'madinah'
            $table->string('star_rating', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('main_photo')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('distance_to_haram', 100)->nullable();
            $table->string('status', 20)->default('aktif'); // 'aktif' | 'nonaktif'
            $table->timestamps();

            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
