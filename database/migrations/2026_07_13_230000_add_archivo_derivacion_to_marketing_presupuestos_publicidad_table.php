<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->string('archivo_derivacion_path', 500)->nullable()->after('observaciones');
            $table->string('archivo_derivacion_original', 255)->nullable()->after('archivo_derivacion_path');
            $table->string('archivo_derivacion_mime', 100)->nullable()->after('archivo_derivacion_original');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->dropColumn(['archivo_derivacion_path', 'archivo_derivacion_original', 'archivo_derivacion_mime']);
        });
    }
};
