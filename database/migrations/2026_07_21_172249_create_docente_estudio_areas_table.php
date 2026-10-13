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
        Schema::create('docente_estudio_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docente_estudio_id');
            $table->unsignedInteger('area_id');
            $table->timestamps();

            $table->unique(['docente_estudio_id', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_estudio_areas');
    }
};
