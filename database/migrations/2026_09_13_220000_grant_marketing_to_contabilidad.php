<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El área contable accede al apartado Marketing y a Publicidad
 * (Publicidad queda reservada a Super Admin + Contabilidad).
 */
return new class extends Migration
{
    private const PERMISOS = [
        'marketing.estadisticas.listar',
        'marketing.asesores.listar',
        'asesores.verdetalle',
        'asesores.vercompleto',
        'marketing.publicidad.listar',
        'marketing.publicidad.subir',
        'marketing.publicidad.verificar',
        'marketing.publicidad.presupuestos',
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
