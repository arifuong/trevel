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
        Schema::disableForeignKeyConstraints();

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gallery_category_id');
            $table->boolean('is_profile_hero')->default(false)->after('type');
        });

        Schema::dropIfExists('gallery_categories');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('is_profile_hero');
            $table->foreignId('gallery_category_id')->nullable()->constrained('gallery_categories')->nullOnDelete();
        });
    }
};
