<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El rol "Asesor de Marketing" no accede a Cronogramas.
 */
return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::where('name', 'Asesor de Marketing')->first();

        if ($role) {
            $ids = Permission::whereIn('name', ['cronogramas.listar'])->pluck('id');

            $role->permissions()->detach($ids);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Intencional: no se restaura el acceso a Cronogramas.
    }
};
