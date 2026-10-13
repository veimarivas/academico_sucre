<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_gastos_autogestion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asesor_id');
            $table->smallInteger('gestion');
            $table->tinyInteger('mes');
            $table->decimal('monto', 10, 2);
            $table->text('descripcion')->nullable();
            $table->string('plataforma', 50)->nullable();
            $table->string('campana_nombre', 255)->nullable();
            $table->string('enlace_campana', 500)->nullable();
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->string('archivo_path', 500)->nullable();
            $table->string('archivo_original', 255)->nullable();
            $table->string('archivo_mime', 100)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['asesor_id', 'gestion', 'mes']);
            $table->index('programa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_gastos_autogestion');
    }
};
