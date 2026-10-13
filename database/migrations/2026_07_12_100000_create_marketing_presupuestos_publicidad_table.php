<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('asesor_id')->nullable();
            $table->smallInteger('gestion');
            $table->tinyInteger('mes');
            $table->tinyInteger('quincena');
            $table->decimal('monto_asignado', 10, 2)->default(0);
            $table->decimal('monto_reembolsado', 10, 2)->default(0);
            $table->enum('estado', ['activo', 'cerrado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['gestion', 'mes', 'quincena']);
            $table->index('asesor_id');
            $table->index('supervisor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_presupuestos_publicidad');
    }
};
