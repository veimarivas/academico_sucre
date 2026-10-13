<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * El módulo Académico queda reservado al rol "Academicos" (+ Super Admin).
 * Se revocan los permisos académicos de "Administrador" y "Consulta"; así el
 * sidebar los oculta y las rutas responden 403 → perfil (handler global).
 */
return new class extends Migration
{
    private const ACADEMICOS = [
        'programas.listar', 'programas.registrar', 'programas.ver', 'programas.editar',
        'programas.eliminar', 'programas.moodle.enlace', 'programas.asignar.responsable',
        'programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas',
        'programas.tab.contabilidad', 'programas.tab.historial', 'programas.tab.desarrollo',
        'programas.tab.contable',
        'programas.modulos.agregar', 'programas.modulos.estado', 'programas.modulos.asignar.docente',
        'programas.modulos.sesion.extra', 'programas.modulos.registrar.horarios',
        'programas.modulos.whatsapp',
        'inscripciones.registrar', 'inscripciones.cambiarestado', 'inscripciones.asesor.cambiar',
        'inscripciones.eliminar',
        'tipos.listar', 'tipos.ver', 'tipos.editar', 'tipos.registrar', 'tipos.eliminar',
        'areas.listar', 'areas.ver', 'areas.editar', 'areas.registrar', 'areas.eliminar',
        'cronogramas.listar', 'seguimiento.listar', 'responsables.listar',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $ids = Permission::whereIn('name', self::ACADEMICOS)->pluck('id');

        foreach (['Administrador', 'Consulta'] as $nombre) {
            $role = Role::where('name', $nombre)->first();

            if ($role) {
                $role->permissions()->detach($ids);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Intencional: no se restaura el acceso académico a estos roles.
    }
};
