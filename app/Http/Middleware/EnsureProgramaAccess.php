<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autoriza acciones puntuales de un programa.
 *
 * Modos:
 * - `responsable`: Super Admin o responsable asignado del programa.
 * - `baja`: Super Admin, responsable del programa o área contable.
 * - `cambiar-estado`: Super Admin, rol Responsable Académico o responsable del programa.
 * - `modulo`: Super Admin, personal con permiso de módulos, o responsable del programa.
 *   Autoriza registro/edición de asistencia, notas regulares y nivelación.
 * - `asignar-docente`: restringe al rol "Responsable Académico UPI" a programas
 *   de tipo UPI (Diplomados / Diplomado Autogestionado). El resto de los
 *   usuarios se rige por el permiso `programas.modulos.asignar.docente`.
 *
 * Resuelve el programa desde `programa`/`id`, o desde `modulo`/`horario`.
 */
class EnsureProgramaAccess
{
    public function handle(Request $request, Closure $next, string $modo = 'responsable'): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        if ($modo === 'baja' && $user->esAreaContable()) {
            return $next($request);
        }

        if ($modo === 'cambiar-estado' && $user->hasRole('Responsable Académico')) {
            return $next($request);
        }

        if ($modo === 'modulo'
            && ($user->hasRole('Responsable Académico')
                || $user->can('programas.modulos.estado')
                || $user->can('programas.modulos.cambiar.estado'))) {
            return $next($request);
        }

        if ($modo === 'asignar-docente') {
            // El rol "Responsable Académico UPI" solo puede asignar docentes en
            // programas de tipo UPI (Diplomados = 6, Diplomado Autogestionado = 7).
            // El resto de los usuarios queda cubierto por su permiso.
            if ($user->hasRole('Responsable Académico UPI') && ! $user->hasRole('Responsable Académico')) {
                $programaId = $this->resolveProgramaId($request);
                $tipoId = $programaId
                    ? (int) DB::table('programas')->where('id', $programaId)->value('tipo_id')
                    : 0;

                abort_unless(
                    in_array($tipoId, [6, 7], true),
                    403,
                    'Solo podés asignar docentes en programas UPI.'
                );
            }

            return $next($request);
        }

        $programaId = $this->resolveProgramaId($request);

        if (! empty($user->responsable_id) && $programaId) {
            $esResponsable = DB::table('programas_responsables')
                ->where('programa_id', $programaId)
                ->where('responsable_id', $user->responsable_id)
                ->exists();

            if ($esResponsable) {
                return $next($request);
            }
        }

        abort(403, 'No tenés permisos para acceder a este programa.');
    }

    private function resolveProgramaId(Request $request): ?int
    {
        $direct = $request->route('programa')
            ?? $request->route('id')
            ?? $request->input('programa_id')
            ?? $request->input('programa');

        if ($direct) {
            return (int) $direct;
        }

        $moduloId = $request->route('modulo') ?? $request->input('modulo_id') ?? $request->input('modulo');
        if ($moduloId) {
            return DB::table('modulos')->where('id', $moduloId)->value('programa_id');
        }

        $horarioId = $request->route('horario') ?? $request->input('horario_id') ?? $request->input('horario');
        if ($horarioId) {
            $moduloId = DB::table('horarios')->where('id', $horarioId)->value('modulo_id');

            return $moduloId ? DB::table('modulos')->where('id', $moduloId)->value('programa_id') : null;
        }

        return null;
    }
}
