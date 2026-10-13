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
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->json('montos_verificados')->nullable()->after('deudas_seleccionadas')
                ->comment('{mpId: montoPagado} registrados por contabilidad al verificar');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes_pago', function (Blueprint $table) {
            $table->dropColumn('montos_verificados');
        });
    }
};
