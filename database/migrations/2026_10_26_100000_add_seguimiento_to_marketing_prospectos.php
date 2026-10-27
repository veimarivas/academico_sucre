<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Prospectos de marketing: sincronización con Seguimiento ESAM (nota de
 * seguimiento del asesor en /notes) y registro de los envíos masivos.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('marketing_prospectos', function (Blueprint $table) {
            if (! Schema::hasColumn('marketing_prospectos', 'seguimiento_nota_id')) {
                $table->unsignedBigInteger('seguimiento_nota_id')->nullable()->after('estado');
            }
            if (! Schema::hasColumn('marketing_prospectos', 'seguimiento_error')) {
                $table->string('seguimiento_error', 255)->nullable()->after('seguimiento_nota_id');
            }
            if (! Schema::hasColumn('marketing_prospectos', 'seguimiento_at')) {
                $table->timestamp('seguimiento_at')->nullable()->after('seguimiento_error');
            }
        });

        $indices = collect(Schema::getIndexes('marketing_prospectos'))->pluck('name');
        if (! $indices->contains('mp_programa_celular_idx')) {
            Schema::table('marketing_prospectos', function (Blueprint $table) {
                $table->index(['programa_id', 'celular'], 'mp_programa_celular_idx');
            });
        }

        // Una corrida anterior pudo dejar la tabla creada sin índices: se rehace (está vacía).
        if (Schema::hasTable('marketing_prospecto_envios') && ! DB::table('marketing_prospecto_envios')->exists()
            && ! collect(Schema::getIndexes('marketing_prospecto_envios'))->pluck('name')->contains('mpe_user_created_idx')) {
            Schema::drop('marketing_prospecto_envios');
        }

        if (! Schema::hasTable('marketing_prospecto_envios')) {
            Schema::create('marketing_prospecto_envios', function (Blueprint $table) {
                $table->id();
                // users.id es int(11): mismo tipo, sin FK (esquema heredado).
                $table->integer('user_id');
                $table->unsignedBigInteger('batch_id')->nullable();
                $table->unsignedBigInteger('programa_id')->nullable();
                $table->unsignedInteger('total')->default(0);
                $table->string('resumen', 255)->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at'], 'mpe_user_created_idx');
                $table->index('batch_id', 'mpe_batch_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_prospecto_envios');
        Schema::table('marketing_prospectos', function (Blueprint $table) {
            $table->dropIndex('mp_programa_celular_idx');
            $table->dropColumn(['seguimiento_nota_id', 'seguimiento_error', 'seguimiento_at']);
        });
    }
};
