<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El rol "Responsable Académico" puede asignar docentes a los módulos del
 * programa (antes reservado a Super Admin).
 */
return new class extends Migration
{
    private const PERMISOS = [
        'programas.modulos.asignar.docente',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $responsable = Role::where('name', 'Responsable Académico')->first();
        if ($responsable) {
            $responsable->givePermissionTo(
                Permission::whereIn('name', self::PERMISOS)->pluck('name')->all()
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $responsable = Role::where('name', 'Responsable Académico')->first();
        if ($responsable) {
            $responsable->revokePermissionTo(
                Permission::whereIn('name', self::PERMISOS)->pluck('name')->all()
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
