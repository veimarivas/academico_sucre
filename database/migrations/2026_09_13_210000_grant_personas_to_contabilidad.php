<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El área contable puede ver el apartado Personas (participantes/docentes).
 * Registrar y editar queda en Super Admin + Responsable Académico (por rol).
 */
return new class extends Migration
{
    private const PERMISOS = [
        'participantes.listar',
        'participantes.ver',
        'participantes.buscar',
        'docentes.listar',
        'docentes.ver',
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
