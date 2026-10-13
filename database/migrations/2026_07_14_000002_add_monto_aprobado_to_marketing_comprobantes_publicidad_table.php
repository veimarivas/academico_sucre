<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->decimal('monto_aprobado', 10, 2)->nullable()->after('monto');
        });

        DB::statement("ALTER TABLE marketing_comprobantes_publicidad MODIFY COLUMN estado ENUM('pendiente','aprobado','aprobado_obs','rechazado') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE marketing_comprobantes_publicidad MODIFY COLUMN estado ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente'");
        Schema::table('marketing_comprobantes_publicidad', function (Blueprint $table) {
            $table->dropColumn('monto_aprobado');
        });
    }
};
