<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // La tabla puede haber sido creada previamente fuera del historial de
        // migraciones. En ese caso se conserva y solo se incorpora el estado.
        if (Schema::hasTable('marketing_prospectos')) {
            if (! Schema::hasColumn('marketing_prospectos', 'estado')) {
                Schema::table('marketing_prospectos', function (Blueprint $table) {
                    $table->string('estado', 30)->default('nuevo');
                });
            }

            return;
        }

        Schema::create('marketing_prospectos', function (Blueprint $table) {
            $table->id();
            // users.id y programas.id son int(11): mismo tipo, sin FK (como el resto del esquema heredado).
            $table->integer('user_id')->index();
            $table->integer('programa_id')->nullable()->index();
            $table->string('nombres', 150)->nullable();
            $table->string('apellidos', 150)->nullable();
            $table->string('celular', 40)->nullable();
            $table->string('correo', 190)->nullable();
            $table->text('nota')->nullable();
            $table->string('estado', 30)->default('nuevo');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }
    public function down(): void
    {
        // No se elimina la tabla porque podría contener prospectos previos.
    }
};
