<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $gestiones = DB::table('programas')
            ->select('gestion')
            ->distinct()
            ->orderBy('gestion', 'desc')
            ->pluck('gestion');

        $gestionActual = (int) date('Y');

        $stats = [
            'users' => DB::table('users')->count(),
            'programas' => DB::table('programas')->count(),
            'participantes' => DB::table('participantes')->count(),
            'docentes' => DB::table('docentes')->count(),
        ];

        $onlineUsers = User::with(['responsable', 'asesor'])
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->get();

        $onlineGroups = self::buildOnlineGroups($onlineUsers);

        return view('admin.dashboard', compact('gestiones', 'gestionActual', 'stats', 'onlineUsers', 'onlineGroups'));
    }

    public function programasVigentes()
    {
        return view('admin.programas-vigentes');
    }

    public function programasVigentesStats(Request $request)
    {
        $filtro = $request->get('filtro', 'todos');
        $tipo = $request->get('tipo', 'todos');

        $estados = match ($filtro) {
            'desarrollo' => [1],
            'inscripciones' => [4],
            default => [1, 4],
        };

        $programas = DB::table('programas')
            ->join('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereIn('programas.estado_id', $estados)
            ->when($tipo === 'programas', fn($q) => $q->whereNotIn('programas.tipo_id', [6, 7]))
            ->when($tipo === 'upi', fn($q) => $q->where('programas.tipo_id', 6))
            ->when($tipo === 'upi-auto', fn($q) => $q->where('programas.tipo_id', 7))
            ->select('programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion', 'programas.tipo_id', 'tipos.nombre as tipo_nombre', 'estados.nombre as estado')
            ->orderBy('programas.gestion', 'desc')
            ->orderBy('programas.nombre')
            ->get();

        $programaIds = $programas->pluck('id');

        $inscripciones = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->whereIn('inscripciones.programa_id', $programaIds)
            ->where('inscripciones.estado', 1)
            ->select(
                'inscripciones.programa_id',
                'inscripciones.contado',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('programa_id');

        $programasStats = [];
        foreach ($programas as $p) {
            $rows = $inscripciones->get($p->id, collect());
            $tipoNombre = $p->tipo_nombre ?? 'Sin tipo';
            $programasStats[] = [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo,
                'gestion' => $p->gestion,
                'tipo_id' => $p->tipo_id,
                'tipo_nombre' => $tipoNombre,
                'estado' => $p->estado,
                'total' => $rows->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($programasStats, fn($a, $b) => $b['total'] <=> $a['total']);

        $programasInscripciones = array_values(array_filter($programasStats, fn($p) => stripos($p['estado'], 'Inscripciones') !== false));
        $programasDesarrollo = array_values(array_filter($programasStats, fn($p) => stripos($p['estado'], 'Desarrollo') !== false));

        $totalInscripciones = array_sum(array_column($programasInscripciones, 'total'));
        $totalDesarrollo = array_sum(array_column($programasDesarrollo, 'total'));

        // Chart data
        $singleEstado = count($estados) === 1;
        if ($singleEstado) {
            $chartProg = [
                'labels' => [],
                'series' => [
                    ['name' => 'Regular', 'data' => []],
                    ['name' => 'Contado', 'data' => []],
                ],
                'codigos' => [],
                'estados' => [],
            ];
            foreach ($programasStats as $p) {
                $chartProg['labels'][] = $p['nombre'];
                $chartProg['codigos'][] = $p['codigo'];
                $chartProg['estados'][] = $p['estado'];
                $chartProg['series'][0]['data'][] = $p['regulares'];
                $chartProg['series'][1]['data'][] = $p['contado'];
            }
        } else {
            $chartProg = [
                'labels' => [],
                'series' => [
                    ['name' => 'Inscripciones · Regular', 'data' => []],
                    ['name' => 'Inscripciones · Contado', 'data' => []],
                    ['name' => 'En Desarrollo · Regular', 'data' => []],
                    ['name' => 'En Desarrollo · Contado', 'data' => []],
                ],
                'codigos' => [],
                'estados' => [],
            ];
            foreach ($programasStats as $p) {
                $chartProg['labels'][] = $p['nombre'];
                $chartProg['codigos'][] = $p['codigo'];
                $chartProg['estados'][] = $p['estado'];
                $isInsc = stripos($p['estado'], 'Inscripciones') !== false;
                $chartProg['series'][0]['data'][] = $isInsc ? $p['regulares'] : 0;
                $chartProg['series'][1]['data'][] = $isInsc ? $p['contado'] : 0;
                $chartProg['series'][2]['data'][] = $isInsc ? 0 : $p['regulares'];
                $chartProg['series'][3]['data'][] = $isInsc ? 0 : $p['contado'];
            }
        }

        // Resumen
        $totalInscritos = array_sum(array_column($programasStats, 'total'));
        $totalRegulares = array_sum(array_column($programasStats, 'regulares'));
        $totalContado = array_sum(array_column($programasStats, 'contado'));
        $totalHombres = array_sum(array_column($programasStats, 'hombres'));
        $totalMujeres = array_sum(array_column($programasStats, 'mujeres'));

        // Stats por área
        $areaStats = DB::table('inscripciones')
            ->join('areas_programas', 'inscripciones.programa_id', '=', 'areas_programas.programa_id')
            ->join('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->whereIn('inscripciones.programa_id', $programaIds)
            ->where('inscripciones.estado', 1)
            ->select(
                'areas.id',
                'areas.nombre',
                'areas.color',
                'inscripciones.contado',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $areasData = [];
        foreach ($areaStats as $aid => $rows) {
            $first = $rows->first();
            $areasData[] = [
                'id' => $aid,
                'nombre' => $first->nombre,
                'color' => $first->color,
                'total' => $rows->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($areasData, fn($a, $b) => $b['total'] <=> $a['total']);

        return response()->json([
            'resumen' => [
                'total_programas' => count($programasStats),
                'total_inscritos' => $totalInscritos,
                'regulares' => $totalRegulares,
                'contado' => $totalContado,
                'hombres' => $totalHombres,
                'mujeres' => $totalMujeres,
            ],
            'programas' => $programasStats,
            'programas_inscripciones' => $programasInscripciones,
            'programas_desarrollo' => $programasDesarrollo,
            'total_inscripciones' => $totalInscripciones,
            'total_desarrollo' => $totalDesarrollo,
            'areas' => $areasData,
            'chart_programas' => $chartProg,
            'chart_areas' => [
                'labels' => array_map(fn($a) => $a['nombre'], $areasData),
                'total' => array_map(fn($a) => $a['total'], $areasData),
                'colors' => array_map(fn($a) => $a['color'], $areasData),
            ],
        ]);
    }

    public function stats(Request $request)
    {
        $gestion = $request->get('gestion', date('Y'));

        $mesesLabels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        // ============== Chart 1: Inscritos por mes (YEAR(fecha_inscripcion) = gestion) ==============
        // Combo: barras Regulares + barras Contado + línea Total
        $rows1 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart1 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows1->get($m);
            $chart1['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart1['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart1['contado'][]    = $r ? (int) $r->contado : 0;
            $chart1['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        // ============== Chart 2: Inscritos por mes para programas.gestion = $gestion ==============
        $rows2 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('programas.gestion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart2 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows2->get($m);
            $chart2['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart2['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart2['contado'][]    = $r ? (int) $r->contado : 0;
            $chart2['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        // Detalle por mes con programas dentro de cada mes (chart 2)
        $rawDetail = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.gestion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                'tipos.nombre as tipo_nombre',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre', 'tipos.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart2PorMes = [];
        foreach ($rawDetail as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart2PorMes[$m])) {
                $chart2PorMes[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart2PorMes[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'tipo_nombre' => $row->tipo_nombre,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart2PorMes[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        foreach ($chart2PorMes as $m => &$month) {
            $tc = ['Curso' => 0, 'Diplomado' => 0, 'Maestrías' => 0];
            foreach ($month['programas'] as $p) {
                $tn = $p['tipo_nombre'] ?? '';
                if (isset($tc[$tn])) {
                    $tc[$tn] += $p['total'];
                }
            }
            $month['tipo_counts'] = $tc;
        }
        unset($month);
        $chart2PorMes = array_values($chart2PorMes);

        // ============== Chart 1 detalle por mes (sin filtro programas.gestion) ==============
        $rawDetail1 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                'tipos.nombre as tipo_nombre',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre', 'tipos.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart1PorMes = [];
        foreach ($rawDetail1 as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart1PorMes[$m])) {
                $chart1PorMes[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart1PorMes[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'tipo_nombre' => $row->tipo_nombre,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart1PorMes[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        foreach ($chart1PorMes as $m => &$month) {
            $tc = ['Curso' => 0, 'Diplomado' => 0, 'Maestrías' => 0];
            foreach ($month['programas'] as $p) {
                $tn = $p['tipo_nombre'] ?? '';
                if (isset($tc[$tn])) {
                    $tc[$tn] += $p['total'];
                }
            }
            $month['tipo_counts'] = $tc;
        }
        unset($month);
        $chart1PorMes = array_values($chart1PorMes);

        // ============== Chart 3 + tabla: Inscritos por programa para programas.estado_id = 3 ==============
        $programasEstado3 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('programas.gestion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->where('programas.estado_id', 3)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $estado3Stats = [];
        foreach ($programasEstado3 as $pid => $rows) {
            $first = $rows->first();
            $estado3Stats[] = [
                'id' => $pid,
                'nombre' => $first->nombre,
                'codigo' => $first->codigo,
                'tipo_id' => $first->tipo_id,
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'traspasos' => $rows->where('es_traspaso', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
                'total' => $rows->count() - $rows->where('es_traspaso', 1)->count(),
            ];
        }
        usort($estado3Stats, fn($a, $b) => $b['total'] <=> $a['total']);

        $chart3 = [
            'labels' => array_map(fn($p) => $p['nombre'], $estado3Stats),
            'codigos' => array_map(fn($p) => $p['codigo'], $estado3Stats),
            'regulares' => array_map(fn($p) => $p['regulares'], $estado3Stats),
            'contado' => array_map(fn($p) => $p['contado'], $estado3Stats),
            'traspasos' => array_map(fn($p) => $p['traspasos'], $estado3Stats),
        ];

        // ============== Chart 4 + tabla: Por programa (regulares + contado) ==============
        $programasRows = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('programas.gestion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->where('programas.estado_id', '!=', 3)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $programasStats = [];
        foreach ($programasRows as $pid => $rows) {
            $first = $rows->first();
            $programasStats[] = [
                'id' => $pid,
                'nombre' => $first->nombre,
                'codigo' => $first->codigo,
                'tipo_id' => $first->tipo_id,
                'total' => $rows->count() - $rows->where('es_traspaso', 1)->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'traspasos' => $rows->where('es_traspaso', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($programasStats, fn($a, $b) => $b['total'] <=> $a['total']);

        // Para el gráfico 4 (todos los programas con Regulares + Contado)
        $tipoIds = array_map(fn($p) => $p['tipo_id'], $programasStats);
        $tipoCounts = [
            'Curso' => count(array_filter($tipoIds, fn($t) => $t == 1)),
            'Diplomado' => count(array_filter($tipoIds, fn($t) => $t == 2)),
            'Maestrías' => count(array_filter($tipoIds, fn($t) => $t == 4)),
        ];
        $chart4 = [
            'labels' => array_map(fn($p) => $p['nombre'], $programasStats),
            'regulares' => array_map(fn($p) => $p['regulares'], $programasStats),
            'contado' => array_map(fn($p) => $p['contado'], $programasStats),
            'traspasos' => array_map(fn($p) => $p['traspasos'], $programasStats),
            'codigos' => array_map(fn($p) => $p['codigo'], $programasStats),
            'tipo_counts' => $tipoCounts,
        ];

        // ============== Otras gestiones: Inscritos de la gestión del filtro, programas de otras gestiones ==============
        $otrasRows = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('programas.gestion', '!=', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->where('programas.estado_id', '!=', 3)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $otrasStats = [];
        foreach ($otrasRows as $pid => $rows) {
            $first = $rows->first();
            $otrasStats[] = [
                'id' => $pid,
                'nombre' => $first->nombre,
                'codigo' => $first->codigo,
                'tipo_id' => $first->tipo_id,
                'total' => $rows->count() - $rows->where('es_traspaso', 1)->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'traspasos' => $rows->where('es_traspaso', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($otrasStats, fn($a, $b) => $b['total'] <=> $a['total']);

        $chartOtras = [
            'labels' => array_map(fn($p) => $p['nombre'], $otrasStats),
            'regulares' => array_map(fn($p) => $p['regulares'], $otrasStats),
            'contado' => array_map(fn($p) => $p['contado'], $otrasStats),
            'traspasos' => array_map(fn($p) => $p['traspasos'], $otrasStats),
            'codigos' => array_map(fn($p) => $p['codigo'], $otrasStats),
        ];

        // ============== UPI Diplomados (tipo_id = 6) - Chart 1: por mes (sin filtro gestion) ==============
        $rows1Tipo6 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('programas.tipo_id', 6)
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart1Tipo6 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows1Tipo6->get($m);
            $chart1Tipo6['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart1Tipo6['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart1Tipo6['contado'][]    = $r ? (int) $r->contado : 0;
            $chart1Tipo6['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        $rawDetail1Tipo6 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('programas.tipo_id', 6)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart1PorMesTipo6 = [];
        foreach ($rawDetail1Tipo6 as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart1PorMesTipo6[$m])) {
                $chart1PorMesTipo6[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart1PorMesTipo6[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart1PorMesTipo6[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        $chart1PorMesTipo6 = array_values($chart1PorMesTipo6);

        // ============== UPI Diplomados (tipo_id = 6) - Chart 2: por mes (programas.gestion = $gestion) ==============
        $rows2Tipo6 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 6)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart2Tipo6 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows2Tipo6->get($m);
            $chart2Tipo6['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart2Tipo6['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart2Tipo6['contado'][]    = $r ? (int) $r->contado : 0;
            $chart2Tipo6['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        $rawDetail2Tipo6 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 6)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart2PorMesTipo6 = [];
        foreach ($rawDetail2Tipo6 as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart2PorMesTipo6[$m])) {
                $chart2PorMesTipo6[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart2PorMesTipo6[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart2PorMesTipo6[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        $chart2PorMesTipo6 = array_values($chart2PorMesTipo6);

        // ============== UPI Diplomados (tipo_id = 6) - Tabla por programa ==============
        $tipo6Rows = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 6)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $tipo6Stats = [];
        foreach ($tipo6Rows as $pid => $rows) {
            $first = $rows->first();
            $tipo6Stats[] = [
                'id' => $pid,
                'nombre' => $first->nombre,
                'codigo' => $first->codigo,
                'tipo_id' => $first->tipo_id,
                'total' => $rows->count() - $rows->where('es_traspaso', 1)->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'traspasos' => $rows->where('es_traspaso', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($tipo6Stats, fn($a, $b) => $b['total'] <=> $a['total']);

        $chartTipo6 = [
            'labels' => array_map(fn($p) => $p['nombre'], $tipo6Stats),
            'regulares' => array_map(fn($p) => $p['regulares'], $tipo6Stats),
            'contado' => array_map(fn($p) => $p['contado'], $tipo6Stats),
            'traspasos' => array_map(fn($p) => $p['traspasos'], $tipo6Stats),
            'codigos' => array_map(fn($p) => $p['codigo'], $tipo6Stats),
        ];

        // ============== UPI Diplomados Autogestionados (tipo_id = 7) - Chart 1: por mes (sin filtro gestion) ==============
        $rows1Tipo7 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('programas.tipo_id', 7)
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart1Tipo7 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows1Tipo7->get($m);
            $chart1Tipo7['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart1Tipo7['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart1Tipo7['contado'][]    = $r ? (int) $r->contado : 0;
            $chart1Tipo7['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        $rawDetail1Tipo7 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('programas.tipo_id', 7)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart1PorMesTipo7 = [];
        foreach ($rawDetail1Tipo7 as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart1PorMesTipo7[$m])) {
                $chart1PorMesTipo7[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart1PorMesTipo7[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart1PorMesTipo7[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        $chart1PorMesTipo7 = array_values($chart1PorMesTipo7);

        // ============== UPI Diplomados Autogestionados (tipo_id = 7) - Chart 2: por mes (programas.gestion = $gestion) ==============
        $rows2Tipo7 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 7)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')->get()->keyBy('mes');

        $chart2Tipo7 = ['labels' => $mesesLabels, 'total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => []];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows2Tipo7->get($m);
            $chart2Tipo7['total'][]      = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chart2Tipo7['regulares'][]  = $r ? (int) $r->regulares : 0;
            $chart2Tipo7['contado'][]    = $r ? (int) $r->contado : 0;
            $chart2Tipo7['traspasos'][]  = $r ? (int) $r->traspasos : 0;
        }

        $rawDetail2Tipo7 = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 7)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes_num'),
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'estados.nombre as estado',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'programas.id', 'programas.nombre', 'programas.codigo', 'estados.nombre')
            ->orderBy('mes_num')
            ->orderByDesc('total')
            ->get();

        $chart2PorMesTipo7 = [];
        foreach ($rawDetail2Tipo7 as $row) {
            $m = (int) $row->mes_num;
            if (!isset($chart2PorMesTipo7[$m])) {
                $chart2PorMesTipo7[$m] = [
                    'mes' => $m,
                    'mes_label' => $mesesLabels[$m - 1] ?? (string) $m,
                    'total' => 0,
                    'programas' => [],
                ];
            }
            $chart2PorMesTipo7[$m]['programas'][] = [
                'id' => $row->id,
                'nombre' => $row->nombre,
                'codigo' => $row->codigo,
                'estado' => $row->estado,
                'total' => (int) $row->total - (int) $row->traspasos,
                'regulares' => (int) $row->regulares,
                'contado' => (int) $row->contado,
                'traspasos' => (int) $row->traspasos,
            ];
            $chart2PorMesTipo7[$m]['total'] += (int) $row->total - (int) $row->traspasos;
        }
        $chart2PorMesTipo7 = array_values($chart2PorMesTipo7);

        // ============== UPI Diplomados Autogestionados (tipo_id = 7) - Tabla por programa ==============
        $tipo7Rows = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.tipo_id', 7)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'participantes.sexo'
            )
            ->get()
            ->groupBy('id');

        $tipo7Stats = [];
        foreach ($tipo7Rows as $pid => $rows) {
            $first = $rows->first();
            $tipo7Stats[] = [
                'id' => $pid,
                'nombre' => $first->nombre,
                'codigo' => $first->codigo,
                'tipo_id' => $first->tipo_id,
                'total' => $rows->count() - $rows->where('es_traspaso', 1)->count(),
                'regulares' => $rows->where('contado', 0)->count(),
                'contado' => $rows->where('contado', 1)->count(),
                'traspasos' => $rows->where('es_traspaso', 1)->count(),
                'hombres' => $rows->where('sexo', 'Hombre')->count(),
                'mujeres' => $rows->where('sexo', 'Mujer')->count(),
            ];
        }
        usort($tipo7Stats, fn($a, $b) => $b['total'] <=> $a['total']);

        $chartTipo7 = [
            'labels' => array_map(fn($p) => $p['nombre'], $tipo7Stats),
            'regulares' => array_map(fn($p) => $p['regulares'], $tipo7Stats),
            'contado' => array_map(fn($p) => $p['contado'], $tipo7Stats),
            'traspasos' => array_map(fn($p) => $p['traspasos'], $tipo7Stats),
            'codigos' => array_map(fn($p) => $p['codigo'], $tipo7Stats),
        ];

        // Resumen
        $totalInscritos = array_sum(array_column($programasStats, 'total'));
        $regulares = array_sum(array_column($programasStats, 'regulares'));
        $contado = array_sum(array_column($programasStats, 'contado'));
        $hombres = array_sum(array_column($programasStats, 'hombres'));
        $mujeres = array_sum(array_column($programasStats, 'mujeres'));
        $totalProgramas = DB::table('programas')->where('gestion', $gestion)->whereNotIn('tipo_id', [6, 7])->count();
        $traspasos = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('programas.gestion', $gestion)
            ->whereNotIn('programas.tipo_id', [6, 7])
            ->where('inscripciones.es_traspaso', 1)
            ->count();

        return response()->json([
            'resumen' => [
                'total_inscritos' => $totalInscritos,
                'regulares' => $regulares,
                'contado' => $contado,
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'total_programas' => $totalProgramas,
                'traspasos' => $traspasos,
            ],
            'chart1' => $chart1,
            'chart1_por_mes' => $chart1PorMes,
            'chart2' => $chart2,
            'chart2_por_mes' => $chart2PorMes,
            'chart3' => $chart3,
            'chart4' => $chart4,
            'programas' => $programasStats,
            'no_ejecutados' => $estado3Stats,
            'chart_otras' => $chartOtras,
            'otras_gestiones' => $otrasStats,
            'tipo6' => $tipo6Stats,
            'chart_tipo6' => $chartTipo6,
            'chart1_tipo6' => $chart1Tipo6,
            'chart1_por_mes_tipo6' => $chart1PorMesTipo6,
            'chart2_tipo6' => $chart2Tipo6,
            'chart2_por_mes_tipo6' => $chart2PorMesTipo6,
            'tipo7' => $tipo7Stats,
            'chart_tipo7' => $chartTipo7,
            'chart1_tipo7' => $chart1Tipo7,
            'chart1_por_mes_tipo7' => $chart1PorMesTipo7,
            'chart2_tipo7' => $chart2Tipo7,
            'chart2_por_mes_tipo7' => $chart2PorMesTipo7,
        ]);
    }

    public function programaParticipantes($programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $mes = request()->integer('mes');

        $query = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->where('inscripciones.programa_id', $programaId)
            ->where('inscripciones.estado', 1);

        if ($mes >= 1 && $mes <= 12) {
            $query->whereMonth('inscripciones.fecha_inscripcion', $mes);
        }

        $participantes = $query->select(
                'participantes.id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.sexo',
                'participantes.celular',
                'participantes.correo',
                'participantes.departamento',
                'inscripciones.contado',
                'inscripciones.es_traspaso',
                'inscripciones.fecha_inscripcion',
                'asesores.nombres as asesor_nombres',
                'asesores.apellidos as asesor_apellidos'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $resumen = [
            'total' => $participantes->count(),
            'regulares' => $participantes->where('contado', 0)->count(),
            'contado' => $participantes->where('contado', 1)->count(),
            'hombres' => $participantes->where('sexo', 'Hombre')->count(),
            'mujeres' => $participantes->where('sexo', 'Mujer')->count(),
            'traspasos' => $participantes->where('es_traspaso', 1)->count(),
        ];

        return response()->json([
            'success' => true,
            'mes' => $mes,
            'programa' => [
                'id' => $programa->id,
                'nombre' => $programa->nombre,
                'codigo' => $programa->codigo,
            ],
            'resumen' => $resumen,
            'participantes' => $participantes,
        ]);
    }

    public function onlineUsers()
    {
        $users = User::with(['responsable', 'asesor'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $mapped = $users->map(function ($u) {
            $tipoGrupo = self::resolveUserGroup($u);

            return [
                'id' => $u->id,
                'fullname' => $u->fullname ?? $u->username,
                'username' => $u->username,
                'role' => $u->role,
                'is_online' => $u->isOnline(),
                'last_seen' => $u->lastSeen(),
                'last_activity_at' => $u->last_activity_at?->toIso8601String(),
                'iniciales' => strtoupper(collect(explode(' ', trim($u->fullname ?? $u->username)))->filter()->take(2)->map(fn($x) => substr($x, 0, 1))->implode('')),
                'tipo_grupo' => $tipoGrupo,
            ];
        });

        $onlineUsers = $mapped->where('is_online', true)->values();
        $groups = self::groupsFromMapped($onlineUsers);

        return response()->json([
            'total_online' => $onlineUsers->count(),
            'total_users' => $mapped->count(),
            'users' => $mapped,
            'groups' => $groups,
        ]);
    }

    private static function resolveUserGroup($user): ?string
    {
        if ($user->responsable) return 'responsable';
        if ($user->asesor && $user->asesor->cargo_id) return 'cargo';
        return null;
    }

    private static function buildOnlineGroups($users)
    {
        $map = [
            'responsable' => ['name' => 'Área Académica', 'type' => 'responsable', 'users' => []],
            'cargo'       => ['name' => 'Marketing',      'type' => 'cargo',       'users' => []],
            'sin_asignar' => ['name' => 'Administración', 'type' => null,          'users' => []],
        ];

        foreach ($users as $u) {
            $key = self::resolveUserGroup($u) ?? 'sin_asignar';
            $map[$key]['users'][] = $u;
        }

        return array_values(array_filter($map, fn($g) => count($g['users']) > 0));
    }

    private static function groupsFromMapped($onlineUsers)
    {
        $map = [
            'responsable' => ['name' => 'Área Académica', 'type' => 'responsable', 'users' => [], 'count' => 0],
            'cargo'       => ['name' => 'Marketing',      'type' => 'cargo',       'users' => [], 'count' => 0],
            'sin_asignar' => ['name' => 'Sin asignar',    'type' => null,          'users' => [], 'count' => 0],
        ];

        foreach ($onlineUsers as $u) {
            $key = $u['tipo_grupo'] ?? 'sin_asignar';
            $map[$key]['users'][] = $u;
            $map[$key]['count']++;
        }

        return array_values(array_filter($map, fn($g) => $g['count'] > 0));
    }
}
