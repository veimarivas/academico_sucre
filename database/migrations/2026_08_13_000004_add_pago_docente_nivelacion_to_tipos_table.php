<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos', function (Blueprint $table) {
            $table->decimal('pago_docente_nivelacion', 10, 2)->default(0)->after('puntos');
        });
    }

    public function down(): void
    {
        Schema::table('tipos', function (Blueprint $table) {
            $table->dropColumn('pago_docente_nivelacion');
        });
    }
};
