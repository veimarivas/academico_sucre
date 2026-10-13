<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modulos_participantes', function (Blueprint $table) {
            $table->string('comprobante_pago')->nullable()->after('detalle_pagos');
            $table->string('comprobante_original')->nullable()->after('comprobante_pago');
            $table->tinyInteger('comprobante_validado')->default(0)->after('comprobante_original');
            $table->decimal('pago_docente_nivelacion', 10, 2)->default(0)->after('comprobante_validado');
        });
    }

    public function down(): void
    {
        Schema::table('modulos_participantes', function (Blueprint $table) {
            $table->dropColumn(['comprobante_pago', 'comprobante_original', 'comprobante_validado', 'pago_docente_nivelacion']);
        });
    }
};
