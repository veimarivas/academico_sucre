<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('quincena');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->string('enlace_campana', 500)->nullable()->after('campana_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin', 'enlace_campana']);
        });
    }
};
