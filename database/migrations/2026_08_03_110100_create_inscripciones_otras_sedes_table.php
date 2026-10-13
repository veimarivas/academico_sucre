<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones_otras_sedes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programa_id')->index();
            $table->unsignedInteger('participante_id')->index();
            $table->unsignedInteger('asesor_id')->nullable()->index();
            $table->date('fecha_inscripcion')->nullable();
            $table->boolean('contado')->default(false);
            $table->boolean('estado')->default(true)->comment('1 activo / 0 retirado');
            $table->decimal('monto_total', 12, 2)->default(0);
            $table->string('recibo_path', 300)->nullable();
            $table->string('recibo_original', 200)->nullable();
            $table->string('factura_path', 300)->nullable();
            $table->string('factura_original', 200)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['programa_id', 'participante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_otras_sedes');
    }
};