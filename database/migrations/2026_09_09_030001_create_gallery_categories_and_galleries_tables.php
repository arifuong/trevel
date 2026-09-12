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
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_category_id')
                  ->nullable()
                  ->constrained('gallery_categories')
                  ->nullOnDelete();
            $table->string('title');
            $table->string('type', 20)->default('photo'); // 'photo' or 'video'
            $table->string('image_path', 500)->nullable(); // Photo file or custom video thumbnail
            $table->string('video_url', 500)->nullable();  // YouTube link or video ID
            $table->text('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('gallery_categories');
    }
};
