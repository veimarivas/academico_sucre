<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El tab "Desarrollo Académico" también es accesible para el área contable.
 */
return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', 'Contabilidad')->first()
            ?->givePermissionTo('programas.tab.desarrollo');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Role::where('name', 'Contabilidad')->first()
            ?->revokePermissionTo('programas.tab.desarrollo');
    }
};
