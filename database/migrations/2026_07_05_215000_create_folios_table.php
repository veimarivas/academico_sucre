<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('programa_id');
            $table->string('nombre', 200);
            $table->date('fecha_registro');
            $table->string('concepto', 300)->nullable();
            $table->decimal('total_inscripcion', 10, 2)->default(0);
            $table->decimal('total_matricula', 10, 2)->default(0);
            $table->decimal('total_colegiatura', 10, 2)->default(0);
            $table->decimal('total_certificacion', 10, 2)->default(0);
            $table->decimal('total_general', 10, 2)->default(0);
            $table->integer('cantidad_participantes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folios');
    }
};
