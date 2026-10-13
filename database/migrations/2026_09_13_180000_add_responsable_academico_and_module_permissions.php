<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Módulos / Seguimiento Académico:
 * - Nuevos permisos finos: pago docente y cambiar estado.
 * - Nuevo rol "Responsable Académico" (académicos + sesión extra, registrar
 *   sesiones, pago docente y cambiar estado).
 * - "Academicos" pierde asignar docente, sesión extra, registrar sesiones,
 *   editar Moodle, WhatsApp y el tab Historial.
 * - "Contabilidad" accede a la vista de programas y a los tabs contables.
 */
return new class extends Migration
{
    private const QUITAR_ACADEMICOS = [
        'programas.modulos.asignar.docente',
        'programas.modulos.sesion.extra',
        'programas.modulos.registrar.horarios',
        'programas.moodle.enlace',
        'programas.modulos.whatsapp',
        'programas.tab.historial',
    ];

    private const EXTRAS_RESPONSABLE = [
        'programas.modulos.sesion.extra',
        'programas.modulos.registrar.horarios',
        'programas.modulos.pago.docente',
        'programas.modulos.cambiar.estado',
    ];

    private const CONTABILIDAD = [
        'programas.listar',
        'programas.ver',
        'programas.tab.contabilidad',
        'programas.tab.historial',
        'programas.tab.contable',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $pago = Permission::firstOrCreate(['name' => 'programas.modulos.pago.docente', 'guard_name' => 'web']);
        $estado = Permission::firstOrCreate(['name' => 'programas.modulos.cambiar.estado', 'guard_name' => 'web']);

        // Super Admin recibe los nuevos permisos.
        Role::where('name', 'Super Admin')->first()?->givePermissionTo([$pago, $estado]);

        // Academicos: se le quitan los permisos que pasan a Super Admin / nuevo rol.
        $academicos = Role::where('name', 'Academicos')->first();
        if ($academicos) {
            $academicos->permissions()->detach(Permission::whereIn('name', self::QUITAR_ACADEMICOS)->pluck('id'));
        }

        // Responsable Académico = base actual de Academicos + extras.
        $responsable = Role::firstOrCreate(['name' => 'Responsable Académico', 'guard_name' => 'web']);
        $base = $academicos ? $academicos->permissions()->pluck('name')->all() : [];
        $responsable->syncPermissions(array_values(array_unique(array_merge($base, self::EXTRAS_RESPONSABLE))));

        // Contabilidad: acceso a la vista y tabs contables.
        $contabilidad = Role::where('name', 'Contabilidad')->first();
        if ($contabilidad) {
            $contabilidad->givePermissionTo(self::CONTABILIDAD);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::where('name', 'Responsable Académico')->delete();
        Permission::whereIn('name', ['programas.modulos.pago.docente', 'programas.modulos.cambiar.estado'])->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
