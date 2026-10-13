<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horario_id');
            $table->unsignedBigInteger('participante_id');
            $table->string('asistio', 20)->default('Presente');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['horario_id', 'participante_id']);
            $table->index('horario_id');
            $table->index('participante_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
