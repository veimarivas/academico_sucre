<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baja_historial', function (Blueprint $table) {
            $table->id();
            $table->integer('inscripcion_id');
            $table->string('tipo', 20);
            $table->string('accion', 10);
            $table->text('motivo')->nullable();
            $table->integer('user_id');
            $table->timestamps();

            $table->foreign('inscripcion_id')->references('id')->on('inscripciones');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baja_historial');
    }
};
