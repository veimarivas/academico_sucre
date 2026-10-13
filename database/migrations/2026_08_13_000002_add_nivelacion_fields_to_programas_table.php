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
        Schema::table('programas', function (Blueprint $table) {
            $table->tinyInteger('nivelacion_habilitada')->default(0)->after('costo_certificacion_personal');
            $table->decimal('nivelacion_costo_participante', 10, 2)->default(0)->after('nivelacion_habilitada');
            $table->decimal('nivelacion_honorario_docente', 10, 2)->default(0)->after('nivelacion_costo_participante');
            $table->integer('nivelacion_docente_id')->nullable()->after('nivelacion_honorario_docente');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['nivelacion_habilitada', 'nivelacion_costo_participante', 'nivelacion_honorario_docente', 'nivelacion_docente_id']);
        });
    }
};
