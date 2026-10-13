<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->string('archivo_debito_path', 500)->nullable()->after('archivo_mime');
            $table->string('archivo_debito_original', 255)->nullable()->after('archivo_debito_path');
            $table->string('archivo_debito_mime', 100)->nullable()->after('archivo_debito_original');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->dropColumn(['archivo_debito_path', 'archivo_debito_original', 'archivo_debito_mime']);
        });
    }
};
