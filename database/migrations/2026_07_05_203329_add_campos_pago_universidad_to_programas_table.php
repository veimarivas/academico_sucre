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
        Schema::table('programas', function (Blueprint $table) {
            $table->string('codigo_academico', 50)->nullable()->after('codigo');
            $table->decimal('costo_certificacion', 10, 2)->default(0)->after('colegiatura');
            $table->decimal('costo_pago_inscripcion_universidad', 10, 2)->default(0)->after('cantidad_inscritos_minimo');
            $table->decimal('costo_pago_matricula_universidad', 10, 2)->default(0)->after('costo_pago_inscripcion_universidad');
            $table->decimal('costo_pago_colegiatura_universidad', 10, 2)->default(0)->after('costo_pago_matricula_universidad');
            $table->decimal('costo_pago_certificacion_universidad', 10, 2)->default(0)->after('costo_pago_colegiatura_universidad');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_academico',
                'costo_certificacion',
                'costo_pago_inscripcion_universidad',
                'costo_pago_matricula_universidad',
                'costo_pago_colegiatura_universidad',
                'costo_pago_certificacion_universidad',
            ]);
        });
    }
};
