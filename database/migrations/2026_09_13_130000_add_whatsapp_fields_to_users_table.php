<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_numero', 20)->nullable()->after('responsable_id');
            $table->string('whatsapp_area', 30)->nullable()->after('whatsapp_numero');
            $table->boolean('whatsapp_autorizado')->default(false)->after('whatsapp_area');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_numero', 'whatsapp_area', 'whatsapp_autorizado']);
        });
    }
};
