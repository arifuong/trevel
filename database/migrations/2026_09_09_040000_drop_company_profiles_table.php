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
        Schema::dropIfExists('company_profiles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('PT. ZEIN INTERNASIONAL');
            $table->text('about_summary')->nullable();
            $table->text('about_description')->nullable();
            $table->string('ppiu_number', 100)->nullable();
            $table->string('pihk_number', 100)->nullable();
            $table->text('visi')->nullable();
            $table->json('misi')->nullable();
            $table->json('tujuan')->nullable();
            $table->json('keunggulan')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }
};
