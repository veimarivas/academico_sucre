<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->decimal('costo_universidad_alumno', 10, 2)->default(0)->after('presupuesto_campana');
            $table->decimal('gastos_admin_mensual', 10, 2)->default(0)->after('costo_universidad_alumno');
            $table->unsignedSmallInteger('meses_campana')->default(1)->after('gastos_admin_mensual');
            $table->unsignedSmallInteger('duracion_meses')->nullable()->after('meses_campana');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['costo_universidad_alumno', 'gastos_admin_mensual', 'meses_campana', 'duracion_meses']);
        });
    }
};
