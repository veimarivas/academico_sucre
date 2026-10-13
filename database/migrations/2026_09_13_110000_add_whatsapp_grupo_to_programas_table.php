<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->string('whatsapp_grupo_id')->nullable()->after('moodle_link');
            $table->string('whatsapp_grupo_nombre')->nullable()->after('whatsapp_grupo_id');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_grupo_id', 'whatsapp_grupo_nombre']);
        });
    }
};
