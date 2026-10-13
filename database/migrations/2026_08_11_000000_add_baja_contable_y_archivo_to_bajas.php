<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('baja_historial')) {
            Schema::table('baja_historial', function (Blueprint $table) {
                if (!Schema::hasColumn('baja_historial', 'baja_academica')) {
                    $table->boolean('baja_academica')->default(0)->after('motivo');
                }
                if (!Schema::hasColumn('baja_historial', 'baja_contable')) {
                    $table->boolean('baja_contable')->default(0)->after('baja_academica');
                }
                if (!Schema::hasColumn('baja_historial', 'archivo_path')) {
                    $table->string('archivo_path')->nullable()->after('baja_contable');
                }
                if (!Schema::hasColumn('baja_historial', 'archivo_original')) {
                    $table->string('archivo_original')->nullable()->after('archivo_path');
                }
            });
        }

        if (Schema::hasTable('inscripciones') && !Schema::hasColumn('inscripciones', 'baja_contable')) {
            Schema::table('inscripciones', function (Blueprint $table) {
                $table->boolean('baja_contable')->default(0)->after('baja_academica');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('baja_historial')) {
            Schema::table('baja_historial', function (Blueprint $table) {
                $table->dropColumn(['baja_academica', 'baja_contable', 'archivo_path', 'archivo_original']);
            });
        }

        if (Schema::hasTable('inscripciones') && Schema::hasColumn('inscripciones', 'baja_contable')) {
            Schema::table('inscripciones', function (Blueprint $table) {
                $table->dropColumn('baja_contable');
            });
        }
    }
};
