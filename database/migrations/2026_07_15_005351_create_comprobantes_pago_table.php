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
        Schema::create('comprobantes_pago', function (Blueprint $table) {
            $table->id();
            $table->integer('programa_id');
            $table->integer('participante_id');
            $table->integer('inscripcion_id');
            $table->integer('asesor_id');
            $table->decimal('monto', 10, 2);
            $table->string('archivo_path', 500);
            $table->string('archivo_original', 255)->nullable();
            $table->string('archivo_mime', 100)->nullable();
            $table->json('deudas_seleccionadas')->nullable()->comment('IDs de modulos_participantes que cubre este pago');
            $table->string('estado', 20)->default('pendiente')->comment('pendiente, verificado, rechazado');
            $table->integer('verificado_por')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes_pago');
    }
};
