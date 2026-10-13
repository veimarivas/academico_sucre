<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Eliminar permisos existentes (las tablas pivote se limpian por FK cascade o manual)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $permisos = [
            // General
            'dashboard.listar',
            'programas.vigentes.listar',

            // Académico
            'programas.listar',
            'tipos.listar',
            'areas.listar',
            'cronogramas.listar',
            'seguimiento.listar',
            'responsables.listar',

            // Contable
            'contable.listar',
            'contable.asignaciones.listar',
            'contable.asignaciones.definir',
            'contable.cobrar',
            'contable.cuotas-vencidas.listar',
            'contable.cuotas-proximas.listar',

            // Personas
            'participantes.listar',
            'participantes.ver',
            'participantes.buscar',
            'docentes.listar',
            'docentes.registrar',
            'docentes.ver',
            'docentes.credenciales',
            'docentes.editar',

            // Marketing
            'marketing.estadisticas.listar',
            'marketing.asesores.listar',
            'asesores.registrar',
            'asesores.verdetalle',
            'asesores.vercompleto',
            'asesores.inscritos.ver',
            'marketing.publicidad.listar',
            'marketing.publicidad.subir',
            'marketing.publicidad.verificar',
            'marketing.publicidad.presupuestos',

            // Seguridad — acceso a listados (sidebar)
            'usuarios.listar',
            'roles.listar',
            'permisos.listar',

            // Tipos — Acciones
            'tipos.registrar',
            'tipos.ver',
            'tipos.editar',
            'tipos.eliminar',

            // Áreas — Acciones
            'areas.registrar',
            'areas.ver',
            'areas.editar',
            'areas.eliminar',

            // Programas — Acciones
            'programas.registrar',
            'programas.moodle.enlace',
            'programas.ver',
            'programas.asignar.responsable',
            'programas.editar',
            'programas.eliminar',

            // Inscripciones (show.blade.php)
            'inscripciones.registrar',
            'inscripciones.cambiarestado',
            'inscripciones.asesor.cambiar',
            'inscripciones.eliminar',

            // Tabs de programas (show.blade.php)
            'programas.tab.inscritos',
            'programas.tab.matriculas',
            'programas.tab.colegiaturas',
            'programas.tab.contabilidad',
            'programas.tab.historial',
            'programas.tab.desarrollo',
            'programas.tab.contable',

            // Módulos (show.blade.php)
            'programas.modulos.agregar',
            'programas.modulos.estado',
            'programas.modulos.cambiar.estado',
            'programas.modulos.asignar.docente',
            'programas.modulos.sesion.extra',
            'programas.modulos.registrar.horarios',
            'programas.modulos.pago.docente',
            'programas.modulos.whatsapp',

            // WhatsApp — gestión del grupo/configuración
            'whatsapp.admin',

            // Seguridad — CRUD
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
            'permisos.ver', 'permisos.crear', 'permisos.editar', 'permisos.eliminar',
        ];

        foreach ($permisos as $p) {
            Permission::create(['name' => $p, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Administrador: sin módulo Académico (reservado a "Academicos").
        Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web'])
            ->syncPermissions(Permission::whereIn('name', [
                'dashboard.listar',
                'participantes.listar',
                'participantes.buscar',
                'docentes.listar',
                'marketing.estadisticas.listar',
                'marketing.asesores.listar',
                'whatsapp.admin',
            ])->get());

        // Consulta: sólo listados operativos (sin Seguridad ni Académico).
        Role::firstOrCreate(['name' => 'Consulta', 'guard_name' => 'web'])
            ->syncPermissions(
                Permission::where('name', 'like', '%.listar')
                    ->whereNotIn('name', [
                        'usuarios.listar', 'roles.listar', 'permisos.listar',
                        'programas.listar', 'tipos.listar', 'areas.listar',
                        'cronogramas.listar', 'seguimiento.listar', 'responsables.listar',
                    ])
                    ->get()
            );

        $baseAcademicos = [
            'dashboard.listar',
            'programas.vigentes.listar',
            'programas.listar',
            'programas.ver',
            'programas.editar',
            'programas.modulos.agregar',
            'programas.modulos.estado',
            'programas.tab.inscritos',
            'programas.tab.matriculas',
            'programas.tab.colegiaturas',
            'programas.tab.desarrollo',
            'tipos.listar',
            'tipos.ver',
            'tipos.editar',
            'tipos.registrar',
            'areas.listar',
            'areas.ver',
            'areas.editar',
            'areas.registrar',
            'cronogramas.listar',
            'seguimiento.listar',
            'participantes.listar',
            'participantes.ver',
            'participantes.buscar',
            'docentes.listar',
            'docentes.ver',
            'docentes.registrar',
            'docentes.editar',
            'docentes.credenciales',
        ];

        Role::firstOrCreate(['name' => 'Academicos', 'guard_name' => 'web'])
            ->syncPermissions(Permission::whereIn('name', $baseAcademicos)->get());

        // Responsable Académico: base académica + asignar docente, sesión extra,
        // registrar sesiones, pago docente y cambiar estado.
        Role::firstOrCreate(['name' => 'Responsable Académico', 'guard_name' => 'web'])
            ->syncPermissions(Permission::whereIn('name', array_merge($baseAcademicos, [
                'programas.modulos.asignar.docente',
                'programas.modulos.sesion.extra',
                'programas.modulos.registrar.horarios',
                'programas.modulos.pago.docente',
                'programas.modulos.cambiar.estado',
            ]))->get());

        // Responsable Académico UPI: solo asignar docentes, y únicamente en
        // programas UPI (Diplomados / Diplomado Autogestionado). La restricción
        // por tipo se aplica en el middleware `programa.acceso:asignar-docente`.
        Role::firstOrCreate(['name' => 'Responsable Académico UPI', 'guard_name' => 'web'])
            ->syncPermissions(Permission::whereIn('name', [
                'programas.listar',
                'programas.ver',
                'programas.modulos.asignar.docente',
                'docentes.listar',
                'docentes.ver',
            ])->get());

        // Asignar Super Admin a usuarios con role='admin' que no tengan rol
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $user) {
            if (!$user->hasRole('Super Admin')) {
                $user->assignRole('Super Admin');
            }
        }
    }
}
