<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QR de pago y cuentas bancarias para adjuntar en los mensajes masivos
 * (cobranzas y grupos). Un QR vencido no se puede elegir.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('pagos_qr')) {
            return;
        }

        Schema::create('pagos_qr', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('banco', 120);
            $table->string('titular', 160)->nullable();
            $table->string('numero_cuenta', 60)->nullable();
            $table->string('tipo_cuenta', 60)->nullable();
            $table->string('moneda', 10)->default('Bs');
            $table->string('imagen_path', 255);
            $table->date('fecha_vencimiento');
            $table->boolean('activo')->default(true);
            $table->string('observacion', 500)->nullable();
            // users.id es int(11): mismo tipo, sin FK (esquema heredado).
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->index(['activo', 'fecha_vencimiento'], 'pagos_qr_vigencia_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_qr');
    }
};
