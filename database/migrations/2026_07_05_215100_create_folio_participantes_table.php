<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folio_participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('folio_id');
            $table->unsignedInteger('participante_id');
            $table->decimal('monto_inscripcion', 10, 2)->default(0);
            $table->decimal('monto_matricula', 10, 2)->default(0);
            $table->decimal('monto_colegiatura', 10, 2)->default(0);
            $table->decimal('monto_certificacion', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_participantes');
    }
};
