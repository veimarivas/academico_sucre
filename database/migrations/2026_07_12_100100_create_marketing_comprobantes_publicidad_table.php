<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asesor_id');
            $table->unsignedBigInteger('presupuesto_id')->nullable();
            $table->smallInteger('gestion');
            $table->tinyInteger('mes');
            $table->tinyInteger('quincena');
            $table->date('fecha_gasto');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion', 500);
            $table->string('plataforma', 60)->default('Facebook');
            $table->string('campana_nombre', 200)->nullable();
            $table->string('archivo_path', 500);
            $table->string('archivo_original', 255)->nullable();
            $table->string('archivo_mime', 100)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->unsignedBigInteger('verificado_por')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->text('observaciones_contable')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['gestion', 'mes', 'quincena']);
            $table->index(['asesor_id', 'estado']);
            $table->index('presupuesto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_comprobantes_publicidad');
    }
};
