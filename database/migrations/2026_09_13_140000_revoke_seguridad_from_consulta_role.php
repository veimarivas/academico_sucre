<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El rol Consulta sólo debe ver listados operativos: se le quitan los permisos
 * de Seguridad (usuarios/roles/permisos).
 */
return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::where('name', 'Consulta')->first();

        if (! $role) {
            return;
        }

        $ids = Permission::whereIn('name', [
            'usuarios.listar', 'usuarios.ver',
            'roles.listar', 'roles.ver',
            'permisos.listar', 'permisos.ver',
        ])->pluck('id');

        $role->permissions()->detach($ids);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // No se restaura: quitar acceso a Seguridad es intencional.
    }
};
