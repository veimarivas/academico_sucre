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
        Schema::table('marketing_gastos_autogestion', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('mes');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->string('pagina_facebook', 500)->nullable()->after('enlace_campana');
            $table->string('archivo_debito_path', 500)->nullable()->after('archivo_mime');
            $table->string('archivo_debito_original', 255)->nullable()->after('archivo_debito_path');
            $table->string('archivo_debito_mime', 100)->nullable()->after('archivo_debito_original');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_gastos_autogestion', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin', 'pagina_facebook', 'archivo_debito_path', 'archivo_debito_original', 'archivo_debito_mime']);
        });
    }

};
