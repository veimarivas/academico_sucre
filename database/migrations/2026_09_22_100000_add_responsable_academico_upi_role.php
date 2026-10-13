<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Nuevo rol "Responsable Académico UPI".
 *
 * Puede ingresar a la vista de programas y asignar docentes a los módulos,
 * pero SOLO en programas de tipo:
 *   - UPI Diplomados (tipo_id = 6)
 *   - UPI Diplomado Autogestionado (tipo_id = 7)
 *
 * La restricción por tipo se aplica en el middleware `programa.acceso:asignar-docente`
 * y en el gate `$puedeAsignarDocente` de `admin/programas/show.blade.php`.
 */
return new class extends Migration
{
    private const ROL = 'Responsable Académico UPI';

    private const PERMISOS = [
        'programas.listar',
        'programas.ver',
        'programas.modulos.asignar.docente',
        'docentes.listar',
        'docentes.ver',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rol = Role::firstOrCreate(['name' => self::ROL, 'guard_name' => 'web']);

        $rol->syncPermissions(
            Permission::whereIn('name', self::PERMISOS)->pluck('name')->all()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', self::ROL)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
