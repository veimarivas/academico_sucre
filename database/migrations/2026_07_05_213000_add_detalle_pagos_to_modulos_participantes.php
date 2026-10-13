<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modulos_participantes', function (Blueprint $table) {
            $table->json('detalle_pagos')->nullable()->after('total_pagado');
        });
    }

    public function down(): void
    {
        Schema::table('modulos_participantes', function (Blueprint $table) {
            $table->dropColumn('detalle_pagos');
        });
    }
};
