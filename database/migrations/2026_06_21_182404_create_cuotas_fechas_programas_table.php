<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuotas_fechas_programas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programa_id');
            $table->string('plan', 20);
            $table->string('tipo', 20);
            $table->unsignedSmallInteger('numero');
            $table->date('fecha_pago');
            $table->timestamps();

            $table->unique(['programa_id', 'plan', 'tipo', 'numero'], 'uq_cuotas_fechas');
            $table->index('programa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas_fechas_programas');
    }
};
