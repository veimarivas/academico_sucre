<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE folios MODIFY COLUMN programa_id INT UNSIGNED NULL');

        Schema::table('folios', function (Blueprint $table) {
            $table->string('status', 20)->default('abierto')->after('concepto');
        });
    }

    public function down(): void
    {
        Schema::table('folios', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        DB::statement('ALTER TABLE folios MODIFY COLUMN programa_id INT UNSIGNED NOT NULL');
    }
};
