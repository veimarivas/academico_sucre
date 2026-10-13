<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas_otras_sedes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 200);
            $table->string('gestion', 4)->nullable();
            $table->string('sede_origen', 150)->nullable();
            $table->string('tipo', 120)->nullable();
            $table->string('modalidad', 80)->nullable();
            $table->decimal('matricula', 12, 2)->default(0);
            $table->decimal('colegiatura', 12, 2)->default(0);
            $table->integer('n_modulos')->default(1);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('inscripciones_habilitadas')->default(true);
            $table->boolean('estado')->default(true)->comment('1 activo / 0 inactivo');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_otras_sedes');
    }
};