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
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->boolean('periodo_cerrado')->default(false)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_presupuestos_publicidad', function (Blueprint $table) {
            $table->dropColumn('periodo_cerrado');
        });
    }
};
