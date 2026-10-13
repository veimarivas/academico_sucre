<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->unsignedBigInteger('presupuesto_origen_id')->nullable()->after('asesor_id');
            $table->foreign('presupuesto_origen_id')->references('id')->on('marketing_presupuestos_publicidad')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->dropForeign(['presupuesto_origen_id']);
            $table->dropColumn('presupuesto_origen_id');
        });
    }
};
