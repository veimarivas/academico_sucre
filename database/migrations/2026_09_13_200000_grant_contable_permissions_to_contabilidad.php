<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El área contable queda reservada al rol "Contabilidad" (+ Super Admin);
 * se le otorgan los permisos contables para operar el módulo.
 */
return new class extends Migration
{
    private const PERMISOS = [
        'contable.listar',
        'contable.asignaciones.listar',
        'contable.asignaciones.definir',
        'contable.cobrar',
        'contable.cuotas-vencidas.listar',
        'contable.cuotas-proximas.listar',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', 'Contabilidad')->first()?->givePermissionTo(self::PERMISOS);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Role::where('name', 'Contabilidad')->first()?->revokePermissionTo(self::PERMISOS);
    }
};
