<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asesores_metas_historial')) {
            return;
        }

        Schema::create('asesores_metas_historial', function (Blueprint $table) {
            $table->id();
            $table->integer('asesor_id')->index();
            $table->integer('cargo_id')->nullable()->index();
            $table->string('cargo_nombre', 255)->nullable();
            $table->smallInteger('gestion');
            $table->tinyInteger('mes');

            $table->decimal('meta_puntos', 8, 2)->default(0);
            $table->integer('inscritos')->default(0);
            $table->integer('inscritos_reales')->default(0);
            $table->decimal('puntos', 10, 2)->default(0);
            $table->decimal('puntos_reales', 10, 2)->default(0);
            $table->string('estado', 20)->default('sin_meta');

            $table->timestamps();

            $table->unique(['asesor_id', 'gestion', 'mes'], 'asesor_meta_periodo_unique');
            $table->index(['gestion', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asesores_metas_historial');
    }
};
