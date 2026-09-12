<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('category', 50)->default('gallery'); // main, exterior, room, restaurant, facility, gallery
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('hotel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_photos');
    }
};
