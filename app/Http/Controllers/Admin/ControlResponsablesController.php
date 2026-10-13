<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlResponsablesController extends Controller
{
    public function index(Request $request)
    {
        $hoy = date('Y-m-d');

        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()
            ->orderByDesc('gestion')->pluck('gestion');

        $estados = DB::table('estados')->orderBy('id')->get();

        $gestionFiltro = $request->get('gestion');
        if ($gestionFiltro === null && $gestiones->contains((int) date('Y'))) {
            $gestionFiltro = (int) date('Y');
        }

        // Estado por defecto: En desarrollo (1). 'todos' desactiva el filtro.
        $estadoFiltro = $request->get('estado', '1');

        // ===== Responsables con sus programas =====
        $rows = DB::table('programas_responsables as pr')
            ->join('responsables as r', 'pr.responsable_id', '=', 'r.id')
            ->join('programas as p', 'pr.programa_id', '=', 'p.id')
            ->leftJoin('estados as e', 'p.estado_id', '=', 'e.id')
            ->leftJoin('tipos as t', 'p.tipo_id', '=', 't.id')
            ->when($gestionFiltro, fn ($q) => $q->where('p.gestion', $gestionFiltro))
            ->when($estadoFiltro !== 'todos', fn ($q) => $q->where('p.estado_id', (int) $estadoFiltro))
            ->select(
                'r.id as responsable_id',
                'r.ci as responsable_ci',
                'r.nombres as responsable_nombres',
                'r.apellidos as responsable_apellidos',
                'r.color as responsable_color',
                'p.id as programa_id',
                'p.nombre as programa_nombre',
                'p.codigo as programa_codigo',
                'p.gestion as programa_gestion',
                'e.nombre as estado_nombre',
                't.nombre as tipo_nombre'
            )
            ->orderBy('r.apellidos')->orderBy('r.nombres')->orderBy('p.nombre')
            ->get();

        $programaIds = $rows->pluck('programa_id')->unique()->values();

        // ===== Módulos de esos programas =====
        $modulos = collect();
        if ($programaIds->isNotEmpty()) {
            $modulos = DB::table('modulos')
                ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
                ->whereIn('modulos.programa_id', $programaIds)
                ->select(
                    'modulos.id',
                    'modulos.programa_id',
                    'modulos.nombre',
                    'modulos.desarrollo',
                    'docentes.nombres as docente_nombres',
                    'docentes.apellidos as docente_apellidos'
                )
                ->orderBy('modulos.programa_id')
                ->orderBy('modulos.id')
                ->get();
        }
        $moduloIds = $modulos->pluck('id');
        $modulosPorPrograma = $modulos->groupBy('programa_id');

        // ===== Estadísticas de horarios por módulo =====
        $statsHorarios = collect();
        $sinAsistencia = collect();
        if ($moduloIds->isNotEmpty()) {
            $statsHorarios = DB::table('horarios')
                ->whereIn('modulo_id', $moduloIds)
                ->selectRaw("
                    modulo_id,
                    COUNT(*) as total,
                    SUM(CASE WHEN TRIM(estado) = 'Desarrollado' THEN 1 ELSE 0 END) as desarrolladas,
                    SUM(CASE WHEN fecha_desarrollo < ? AND TRIM(estado) = 'No Inicio' THEN 1 ELSE 0 END) as atrasadas,
                    SUM(CASE WHEN fecha_desarrollo < ? AND TRIM(estado) = 'Confirmado' THEN 1 ELSE 0 END) as conf_pasadas,
                    SUM(CASE WHEN fecha_desarrollo = ? THEN 1 ELSE 0 END) as hoy_sesiones,
                    MIN(CASE WHEN fecha_desarrollo >= ? THEN fecha_desarrollo END) as proxima_fecha,
                    MIN(fecha_desarrollo) as fecha_inicio,
                    MAX(fecha_desarrollo) as fecha_fin
                ", [$hoy, $hoy, $hoy, $hoy])
                ->groupBy('modulo_id')
                ->get()
                ->keyBy('modulo_id');

            // Sesiones Desarrolladas sin lista de asistencia registrada
            $sinAsistencia = DB::table('horarios as h')
                ->whereIn('h.modulo_id', $moduloIds)
                ->whereRaw("TRIM(h.estado) = 'Desarrollado'")
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('asistencias as a')
                      ->whereColumn('a.horario_id', 'h.id');
                })
                ->selectRaw('h.modulo_id, COUNT(*) as sin_asist')
                ->groupBy('h.modulo_id')
                ->get()
                ->keyBy('modulo_id');
        }

        // ===== Armar estructura responsable → programas → módulos =====
        $responsables = [];
        foreach ($rows as $row) {
            $rid = $row->responsable_id;
            if (!isset($responsables[$rid])) {
                $responsables[$rid] = [
                    'id' => $rid,
                    'ci' => $row->responsable_ci,
                    'nombres' => $row->responsable_nombres,
                    'apellidos' => $row->responsable_apellidos,
                    'color' => $row->responsable_color ?: '#405189',
                    'programas' => [],
                    'total_programas' => 0,
                    'total_modulos' => 0,
                    'modulos_concluidos' => 0,
                    'modulos_en_desarrollo' => 0,
                    'total_sesiones' => 0,
                    'sesiones_desarrolladas' => 0,
                    'sesiones_hoy' => 0,
                    'alert_atrasadas' => 0,
                    'alert_conf_pasadas' => 0,
                    'alert_sin_asistencia' => 0,
                ];
            }

            $mods = $modulosPorPrograma->get($row->programa_id, collect());
            $progModulos = [];
            $pTotalSes = 0;
            $pDesarrolladas = 0;
            $pAtrasadas = 0;
            $pConfPasadas = 0;
            $pSinAsist = 0;
            $pHoy = 0;
            $pConcluidos = 0;
            $proximaProg = null;

            foreach ($mods as $m) {
                $st = $statsHorarios->get($m->id);
                $sa = (int) ($sinAsistencia->get($m->id)->sin_asist ?? 0);
                $total = (int) ($st->total ?? 0);
                $des = (int) ($st->desarrolladas ?? 0);
                $atr = (int) ($st->atrasadas ?? 0);
                $cp = (int) ($st->conf_pasadas ?? 0);
                $hoyC = (int) ($st->hoy_sesiones ?? 0);
                $prox = $st->proxima_fecha ?? null;

                if (($m->desarrollo ?? '') === 'Concluido') $pConcluidos++;
                if ($prox && (!$proximaProg || $prox < $proximaProg)) $proximaProg = $prox;

                $progModulos[] = [
                    'id' => $m->id,
                    'nombre' => $m->nombre,
                    'desarrollo' => $m->desarrollo,
                    'docente' => $m->docente_apellidos ? trim($m->docente_apellidos . ' ' . $m->docente_nombres) : null,
                    'total_sesiones' => $total,
                    'desarrolladas' => $des,
                    'atrasadas' => $atr,
                    'conf_pasadas' => $cp,
                    'sin_asistencia' => $sa,
                    'hoy' => $hoyC,
                    'proxima_fecha' => $prox,
                    'fecha_inicio' => $st->fecha_inicio ?? null,
                    'fecha_fin' => $st->fecha_fin ?? null,
                ];

                $pTotalSes += $total;
                $pDesarrolladas += $des;
                $pAtrasadas += $atr;
                $pConfPasadas += $cp;
                $pSinAsist += $sa;
                $pHoy += $hoyC;
            }

            $responsables[$rid]['programas'][] = [
                'id' => $row->programa_id,
                'nombre' => $row->programa_nombre,
                'codigo' => $row->programa_codigo,
                'gestion' => $row->programa_gestion,
                'tipo_nombre' => $row->tipo_nombre,
                'estado_nombre' => $row->estado_nombre,
                'modulos' => $progModulos,
                'total_modulos' => count($progModulos),
                'modulos_concluidos' => $pConcluidos,
                'total_sesiones' => $pTotalSes,
                'desarrolladas' => $pDesarrolladas,
                'atrasadas' => $pAtrasadas,
                'conf_pasadas' => $pConfPasadas,
                'sin_asistencia' => $pSinAsist,
                'hoy' => $pHoy,
                'proxima_fecha' => $proximaProg,
                'avance' => $pTotalSes > 0 ? (int) round($pDesarrolladas * 100 / $pTotalSes) : 0,
            ];

            $responsables[$rid]['total_programas']++;
            $responsables[$rid]['total_modulos'] += count($progModulos);
            $responsables[$rid]['modulos_concluidos'] += $pConcluidos;
            $responsables[$rid]['modulos_en_desarrollo'] += $mods->where('desarrollo', 'En Desarrollo')->count();
            $responsables[$rid]['total_sesiones'] += $pTotalSes;
            $responsables[$rid]['sesiones_desarrolladas'] += $pDesarrolladas;
            $responsables[$rid]['sesiones_hoy'] += $pHoy;
            $responsables[$rid]['alert_atrasadas'] += $pAtrasadas;
            $responsables[$rid]['alert_conf_pasadas'] += $pConfPasadas;
            $responsables[$rid]['alert_sin_asistencia'] += $pSinAsist;
        }

        // Avance y total de alertas por responsable; ordenar por alertas desc
        foreach ($responsables as &$r) {
            $r['avance'] = $r['total_sesiones'] > 0
                ? (int) round($r['sesiones_desarrolladas'] * 100 / $r['total_sesiones'])
                : 0;
            $r['total_alertas'] = $r['alert_atrasadas'] + $r['alert_conf_pasadas'] + $r['alert_sin_asistencia'];
        }
        unset($r);

        $responsables = collect($responsables)
            ->sortByDesc(fn ($r) => [$r['total_alertas'], $r['total_programas']])
            ->values();

        // ===== Resumen global =====
        $resumen = [
            'responsables' => $responsables->count(),
            'programas' => $programaIds->count(),
            'modulos' => $modulos->count(),
            'sesiones' => $responsables->sum('total_sesiones'),
            'desarrolladas' => $responsables->sum('sesiones_desarrolladas'),
            'hoy' => $responsables->sum('sesiones_hoy'),
            'atrasadas' => $responsables->sum('alert_atrasadas'),
            'conf_pasadas' => $responsables->sum('alert_conf_pasadas'),
            'sin_asistencia' => $responsables->sum('alert_sin_asistencia'),
        ];
        $resumen['avance'] = $resumen['sesiones'] > 0
            ? (int) round($resumen['desarrolladas'] * 100 / $resumen['sesiones'])
            : 0;
        $resumen['alertas'] = $resumen['atrasadas'] + $resumen['conf_pasadas'] + $resumen['sin_asistencia'];

        return view('admin.control-responsables.index', compact(
            'responsables', 'resumen', 'gestiones', 'gestionFiltro',
            'estados', 'estadoFiltro', 'hoy'
        ));
    }
}
