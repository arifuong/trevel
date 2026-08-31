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
        Schema::dropIfExists('equipment_distributions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('equipment_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('belum_dikirim');
            $table->timestamps();
        });
    }
};
