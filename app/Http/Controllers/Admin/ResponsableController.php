<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponsableController extends Controller
{
    public function index(Request $request)
    {
        $estadoDefault = 'En desarrollo';

        $responsables = $this->countsQuery($request->get('gestion'), $request->get('estado', $estadoDefault));

        $gestiones = DB::table('programas_responsables')
            ->join('programas', 'programas_responsables.programa_id', '=', 'programas.id')
            ->select('programas.gestion')
            ->distinct()
            ->orderByDesc('programas.gestion')
            ->pluck('gestion');

        $estados = DB::table('estados')->select('id', 'nombre')->orderBy('nombre')->get();
        $estadoActual = $estadoDefault;

        return view('admin.responsables.index', compact('responsables', 'gestiones', 'estados', 'estadoActual'));
    }

    /**
     * Devuelve counts por responsable filtrados (AJAX).
     */
    public function counts(Request $request)
    {
        $responsables = $this->countsQuery($request->get('gestion'), $request->get('estado'));

        return response()->json([
            'success' => true,
            'responsables' => $responsables->map(fn ($r) => [
                'id' => $r->id,
                'total_programas' => (int) $r->total_programas,
            ])->values(),
        ]);
    }

    private function countsQuery($gestion, $estado)
    {
        // Subconsulta: counts por responsable filtrando por gestion + estado
        $countsQ = DB::table('programas_responsables')
            ->join('programas', 'programas_responsables.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id');

        if ($gestion !== null && $gestion !== '') {
            $countsQ->where('programas.gestion', $gestion);
        }
        if ($estado !== null && $estado !== '') {
            $countsQ->where('estados.nombre', $estado);
        }

        $counts = $countsQ
            ->select('programas_responsables.responsable_id', DB::raw('COUNT(DISTINCT programas.id) as total'))
            ->groupBy('programas_responsables.responsable_id')
            ->pluck('total', 'programas_responsables.responsable_id');

        // Todos los responsables, con el count (0 si no tiene)
        $responsables = DB::table('responsables')
            ->select('id', 'ci', 'nombres', 'apellidos', 'correo', 'color')
            ->orderBy('apellidos')->orderBy('nombres')
            ->get();

        foreach ($responsables as $r) {
            $r->total_programas = (int) ($counts->get($r->id, 0));
        }

        // Ordenar por count desc, luego por apellido
        return $responsables->sortByDesc(fn ($r) => $r->total_programas)->values();
    }

    public function seguimientoSemanal($id, Request $request)
    {
        $this->autorizarAccesoResponsable((int) $id);

        $responsable = DB::table('responsables')->where('id', $id)->first();
        if (! $responsable) {
            return response()->json(['success' => false, 'message' => 'Responsable no encontrado'], 404);
        }

        $hoy = date('Y-m-d');

        $inicioParam = $request->get('inicio');
        if ($inicioParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicioParam)) {
            $base = strtotime($inicioParam);
        } else {
            $base = strtotime($hoy);
        }
        $dow = (int) date('N', $base);
        $inicio = date('Y-m-d', strtotime('-'.($dow - 1).' days', $base));
        $fin = date('Y-m-d', strtotime('+6 days', strtotime($inicio)));

        $semanaAnterior = date('Y-m-d', strtotime('-7 days', strtotime($inicio)));
        $semanaSiguiente = date('Y-m-d', strtotime('+7 days', strtotime($inicio)));
        $semanaHoy = date('Y-m-d', strtotime('-'.((int) date('N', strtotime($hoy)) - 1).' days', strtotime($hoy)));

        $horarios = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('horarios.asignado_id', $id)
            ->whereBetween('horarios.fecha_desarrollo', [$inicio, $fin])
            ->select(
                'horarios.id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado',
                'horarios.observaciones',
                'modulos.id as modulo_id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_desarrollo',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.id as docente_id',
                'docentes.ci as docente_ci',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'asignados.id as asignado_id',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        $horarios = $horarios->map(function ($h) use ($hoy) {
            $h->atrasado = ($h->fecha_desarrollo < $hoy) && (trim($h->estado) === 'No Inicio');

            return $h;
        });

        $idsDesarrollados = $horarios
            ->filter(fn ($h) => trim($h->estado) === 'Desarrollado')
            ->pluck('id')
            ->all();

        $asistenciasPorHorario = collect();
        if (! empty($idsDesarrollados)) {
            $asistenciasPorHorario = DB::table('asistencias')
                ->join('participantes', 'asistencias.participante_id', '=', 'participantes.id')
                ->whereIn('asistencias.horario_id', $idsDesarrollados)
                ->select(
                    'asistencias.horario_id',
                    'asistencias.asistio',
                    'asistencias.observacion',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos'
                )
                ->orderBy('participantes.apellidos')
                ->orderBy('participantes.nombres')
                ->get()
                ->groupBy('horario_id');
        }

        $dias = [];
        for ($i = 0; $i < 7; $i++) {
            $f = date('Y-m-d', strtotime('+'.$i.' days', strtotime($inicio)));
            $dias[$f] = collect();
        }
        foreach ($horarios as $h) {
            if (isset($dias[$h->fecha_desarrollo])) {
                $dias[$h->fecha_desarrollo]->push($h);
            }
        }

        $resumen = [
            'total' => $horarios->count(),
            'desarrollados' => $horarios->filter(fn ($h) => trim($h->estado) === 'Desarrollado')->count(),
            'pendientes' => $horarios->filter(fn ($h) => in_array(trim($h->estado), ['No Inicio', 'Pendiente', 'Programado', 'Confirmado']))->count(),
            'atrasados' => $horarios->filter(fn ($h) => $h->atrasado)->count(),
        ];

        $html = view('admin.responsables._seguimiento-semanal', compact(
            'inicio', 'fin', 'hoy', 'responsable',
            'semanaAnterior', 'semanaSiguiente', 'semanaHoy',
            'dias', 'asistenciasPorHorario', 'resumen'
        ))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'responsable' => $responsable,
            'resumen' => $resumen,
        ]);
    }

    /**
     * Autoriza el detalle/seguimiento de un responsable: gestores (Super Admin,
     * Responsable Académico) o el propio usuario vinculado a ese responsable.
     */
    private function autorizarAccesoResponsable(int $id): void
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin') || $user->hasRole('Responsable Académico')) {
            return;
        }

        if ((int) $user->responsable_id === $id) {
            return;
        }

        abort(403, 'No estás autorizado para ver este responsable.');
    }

    public function detalle($id, Request $request)
    {
        $this->autorizarAccesoResponsable((int) $id);

        $responsable = DB::table('responsables')->where('id', $id)->first();
        if (! $responsable) {
            return response()->json(['success' => false, 'message' => 'Responsable no encontrado'], 404);
        }

        $gestionFiltro = $request->get('gestion');
        $estadoFiltro = $request->get('estado');

        // Gestiones disponibles para este responsable
        $gestiones = DB::table('programas_responsables')
            ->join('programas', 'programas_responsables.programa_id', '=', 'programas.id')
            ->where('programas_responsables.responsable_id', $id)
            ->select('programas.gestion')
            ->distinct()
            ->orderByDesc('programas.gestion')
            ->pluck('gestion');

        // Programas de este responsable
        $programasQ = DB::table('programas_responsables')
            ->join('programas', 'programas_responsables.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->where('programas_responsables.responsable_id', $id)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.gestion',
                'programas.desarrollo',
                'programas.tipo_id',
                'programas.moodle_link as moodle_link',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre'
            )
            ->orderByDesc('programas.gestion')
            ->orderBy('programas.nombre');

        if ($gestionFiltro !== null && $gestionFiltro !== '') {
            $programasQ->where('programas.gestion', $gestionFiltro);
        }
        if ($estadoFiltro !== null && $estadoFiltro !== '') {
            $programasQ->where('estados.nombre', $estadoFiltro);
        }

        $programas = $programasQ->get();

        if ($programas->isEmpty()) {
            return response()->json([
                'success' => true,
                'responsable' => $responsable,
                'tipos' => [],
                'gestiones' => $gestiones,
                'gestion_actual' => $gestionFiltro,
                'resumen' => [
                    'total_programas' => 0,
                    'total_modulos' => 0,
                    'concluidos' => 0,
                    'en_desarrollo' => 0,
                    'no_inicio' => 0,
                ],
            ]);
        }

        $programaIds = $programas->pluck('id')->toArray();

        // Cargar módulos con docentes (el rango de fechas se resuelve aparte,
        // solo para los módulos de este responsable: evita escanear toda la
        // tabla de horarios con una subconsulta global).
        $modulos = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->whereIn('modulos.programa_id', $programaIds)
            ->select(
                'modulos.id',
                'modulos.programa_id',
                'modulos.nombre',
                'modulos.desarrollo',
                'modulos.moodle_link as moodle_link',
                'docentes.id as docente_id',
                'docentes.ci as docente_ci',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos'
            )
            ->orderBy('modulos.id')
            ->get()
            ->groupBy('programa_id');

        $hoy = now()->format('Y-m-d');

        $moduloIds = $modulos->flatten()->pluck('id')->toArray();

        // Rango de fechas por módulo, acotado a los módulos de este responsable.
        $rangosHorario = collect();
        if (! empty($moduloIds)) {
            $rangosHorario = DB::table('horarios')
                ->whereIn('modulo_id', $moduloIds)
                ->select(
                    'modulo_id',
                    DB::raw('MIN(fecha_desarrollo) as fecha_inicio'),
                    DB::raw('MAX(fecha_desarrollo) as fecha_fin')
                )
                ->groupBy('modulo_id')
                ->get()
                ->keyBy('modulo_id');
        }

        // WhatsApp de notas: solo Super Admin o responsable del programa (quien mira).
        $visor = auth()->user();
        $puedeWa = [];
        $puedeWhatsapp = function (int $programaId) use ($visor, &$puedeWa) {
            return $puedeWa[$programaId] ??= (bool) $visor?->puedeWhatsappPrograma($programaId);
        };

        $estadoNotasPorModulo = DB::table('modulos_participantes')
            ->whereIn('modulo_id', $moduloIds)
            ->select('modulo_id',
                DB::raw('SUM(CASE WHEN nota_modulo IS NOT NULL AND nota_modulo > 0 THEN 1 ELSE 0 END) as con_nota_modulo'),
                DB::raw('SUM(CASE WHEN nota_nivelacion IS NOT NULL AND nota_nivelacion > 0 THEN 1 ELSE 0 END) as con_nota_nivelacion'),
                DB::raw('SUM(CASE WHEN nota_modulo IS NOT NULL AND nota_modulo < 71 THEN 1 ELSE 0 END) as total_reprobados')
            )
            ->groupBy('modulo_id')
            ->get()
            ->keyBy('modulo_id');

        // Inscritos por programa
        $inscritos = DB::table('inscripciones')
            ->whereIn('programa_id', $programaIds)
            ->select('programa_id', DB::raw('COUNT(*) as total'))
            ->groupBy('programa_id')
            ->pluck('total', 'programa_id');

        // Estructura
        $programasOut = [];
        $totalMods = 0;
        $countByEstado = ['Concluido' => 0, 'En Desarrollo' => 0, 'No Inicio' => 0, 'Pendiente' => 0, 'Suspendido' => 0];

        foreach ($programas as $p) {
            $modsP = ($modulos->get($p->id, collect()))->map(function ($m) use (&$countByEstado, &$totalMods, $estadoNotasPorModulo, $rangosHorario, $hoy) {
                $totalMods++;
                if (isset($countByEstado[$m->desarrollo])) {
                    $countByEstado[$m->desarrollo]++;
                }
                $enm = $estadoNotasPorModulo->get($m->id);
                $rango = $rangosHorario->get($m->id);
                $fechaInicio = $rango->fecha_inicio ?? null;
                $fechaFin = $rango->fecha_fin ?? null;
                $enCurso = $fechaInicio && $fechaFin && $hoy >= $fechaInicio && $hoy <= $fechaFin;

                return [
                    'id' => $m->id,
                    'nombre' => $m->nombre,
                    'desarrollo' => $m->desarrollo,
                    'en_curso' => $enCurso,
                    'reciente' => false,
                    'proximo' => false,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'con_nota_modulo' => $enm ? (int) $enm->con_nota_modulo : 0,
                    'con_nota_nivelacion' => $enm ? (int) $enm->con_nota_nivelacion : 0,
                    'total_reprobados' => $enm ? (int) $enm->total_reprobados : 0,
                    'puede_whatsapp' => $puedeWhatsapp((int) $m->programa_id),
                    'docente_id' => $m->docente_id,
                    'docente_nombres' => $m->docente_nombres,
                    'docente_apellidos' => $m->docente_apellidos,
                    'moodle_link' => $m->moodle_link,
                ];
            })->values()->toArray();

            // Si ningun modulo esta en curso, marcar el ultimo que finalizo (max 1 semana atras)
            $tieneEnCurso = count(array_filter($modsP, fn ($x) => $x['en_curso'])) > 0;
            if (! $tieneEnCurso) {
                $semanaAtras = date('Y-m-d', strtotime($hoy.' -14 days'));
                $ultimoIdx = null;
                $ultimaFecha = null;
                foreach ($modsP as $i => $mod) {
                    if ($mod['fecha_fin'] && $mod['fecha_fin'] >= $semanaAtras && $mod['fecha_fin'] <= $hoy) {
                        if ($ultimoIdx === null || $mod['fecha_fin'] > $ultimaFecha) {
                            $ultimaFecha = $mod['fecha_fin'];
                            $ultimoIdx = $i;
                        }
                    }
                }
                if ($ultimoIdx !== null) {
                    $modsP[$ultimoIdx]['reciente'] = true;
                }
            }

            // Proximo: el modulo que sigue al que esta destacado (en_curso o reciente)
            $highlightedFechaFin = null;
            foreach ($modsP as $mod) {
                if (($mod['en_curso'] || $mod['reciente']) && $mod['fecha_fin']) {
                    $highlightedFechaFin = $mod['fecha_fin'];
                    break;
                }
            }
            if ($highlightedFechaFin) {
                $semanaAdelante = date('Y-m-d', strtotime($hoy.' +14 days'));
                $proximoIdx = null;
                $proximaFecha = null;
                foreach ($modsP as $i => $mod) {
                    if ($mod['fecha_inicio'] && $mod['fecha_inicio'] > $highlightedFechaFin && $mod['fecha_inicio'] <= $semanaAdelante) {
                        if ($proximoIdx === null || $mod['fecha_inicio'] < $proximaFecha) {
                            $proximaFecha = $mod['fecha_inicio'];
                            $proximoIdx = $i;
                        }
                    }
                }
                if ($proximoIdx !== null) {
                    $modsP[$proximoIdx]['proximo'] = true;
                }
            }

            $modsResumen = [
                'total' => count($modsP),
                'concluido' => count(array_filter($modsP, fn ($x) => $x['desarrollo'] === 'Concluido')),
                'en_desarrollo' => count(array_filter($modsP, fn ($x) => $x['desarrollo'] === 'En Desarrollo')),
                'no_inicio' => count(array_filter($modsP, fn ($x) => $x['desarrollo'] === 'No Inicio')),
            ];
            $progresoPct = $modsResumen['total'] > 0
                ? round(($modsResumen['concluido'] / $modsResumen['total']) * 100)
                : 0;

            $programasOut[] = [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo,
                'gestion' => $p->gestion,
                'tipo_id' => $p->tipo_id,
                'tipo_nombre' => $p->tipo_nombre,
                'estado_nombre' => $p->estado_nombre,
                'moodle_link' => $p->moodle_link,
                'inscritos' => (int) ($inscritos->get($p->id, 0)),
                'modulos' => $modsP,
                'modulos_resumen' => $modsResumen,
                'progreso_pct' => $progresoPct,
            ];
        }

        // Agrupar por tipo
        $tiposMap = [];
        foreach ($programasOut as $prog) {
            $tipoName = $prog['tipo_nombre'] ?? 'Sin tipo';
            $tipoKey = $prog['tipo_id'] ?? 'sin-tipo';
            if (! isset($tiposMap[$tipoKey])) {
                $tiposMap[$tipoKey] = [
                    'tipo_id' => $tipoKey,
                    'tipo_nombre' => $tipoName,
                    'programas' => [],
                ];
            }
            $tiposMap[$tipoKey]['programas'][] = $prog;
        }
        // Ordenar tipos por cantidad de programas descendente
        $tiposOut = array_values($tiposMap);
        usort($tiposOut, fn ($a, $b) => count($b['programas']) <=> count($a['programas']));

        return response()->json([
            'success' => true,
            'responsable' => $responsable,
            'tipos' => $tiposOut,
            'gestiones' => $gestiones,
            'gestion_actual' => $gestionFiltro,
            'resumen' => [
                'total_programas' => count($programasOut),
                'total_modulos' => $totalMods,
                'concluidos' => $countByEstado['Concluido'],
                'en_desarrollo' => $countByEstado['En Desarrollo'],
                'no_inicio' => $countByEstado['No Inicio'],
            ],
        ]);
    }
}
