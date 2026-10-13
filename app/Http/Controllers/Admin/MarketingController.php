<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    public function programas(Request $request)
    {
        $gestionActual = (int) now()->format('Y');
        $gestion = $request->get('gestion', $gestionActual);
        $estadoId = $request->get('estado_id', '4');
        $search = trim($request->get('search', ''));

        $programas = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->when($gestion !== '' && $gestion !== 'todas', fn($q) => $q->where('programas.gestion', $gestion))
            ->when($estadoId !== '', fn($q) => $q->where('programas.estado_id', $estadoId))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('programas.nombre', 'like', "%{$search}%")
                       ->orWhere('programas.codigo', 'like', "%{$search}%");
                });
            })
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.gestion',
                'programas.fecha_inicio_campana',
                'programas.fecha_fin_campana',
                'programas.cantidad_inscritos_minimo',
                'programas.fecha_inicio',
                'tipos.nombre as tipo_nombre',
                'estados.id as estado_id',
                'estados.nombre as estado_nombre'
            )
            ->selectRaw('(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND inscripciones.estado = 1) as inscritos')
            ->selectRaw('COALESCE((SELECT SUM(monto_aprobado) FROM marketing_comprobantes_publicidad WHERE marketing_comprobantes_publicidad.programa_id = programas.id), 0) as inversion_publicidad')
            ->orderByRaw("CASE WHEN estados.nombre = 'Inscripciones' THEN 0 ELSE 1 END")
            ->orderBy('programas.fecha_inicio_campana')
            ->orderBy('programas.nombre')
            ->get();

        // Fechas (inicio y fin) de todos los módulos de cada programa
        $programaIds = $programas->pluck('id');
        $modulosFechas = DB::table('modulos')
            ->join('horarios', 'horarios.modulo_id', '=', 'modulos.id')
            ->whereIn('modulos.programa_id', $programaIds)
            ->select(
                'modulos.id',
                'modulos.programa_id',
                'modulos.nombre',
                DB::raw('MIN(horarios.fecha_desarrollo) as fecha_inicio'),
                DB::raw('MAX(horarios.fecha_desarrollo) as fecha_fin')
            )
            ->groupBy('modulos.id', 'modulos.programa_id', 'modulos.nombre')
            ->orderBy('modulos.programa_id')
            ->orderBy('modulos.id')
            ->get()
            ->groupBy('programa_id')
            ->map(fn($mods) => $mods->values());

        $estados = DB::table('estados')->orderBy('id')->get();

        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()->orderByDesc('gestion')->pluck('gestion');
        if ($gestiones->isEmpty()) {
            $gestiones = collect([$gestionActual]);
        }

        $resumen = [
            'total_programas' => $programas->count(),
            'en_inscripciones' => $programas->where('estado_nombre', 'Inscripciones')->count(),
            'total_inscritos' => (int) $programas->sum('inscritos'),
            'meta_alcanzada' => $programas->filter(fn($p) => $p->cantidad_inscritos_minimo > 0 && $p->inscritos >= $p->cantidad_inscritos_minimo)->count(),
        ];

        $inversionesPorEstado = DB::table('marketing_comprobantes_publicidad')
            ->whereIn('programa_id', $programaIds)
            ->select('programa_id', 'estado', DB::raw('SUM(monto) as total_monto'), DB::raw('SUM(monto_aprobado) as total_aprobado'))
            ->groupBy('programa_id', 'estado')
            ->get()
            ->groupBy('programa_id');

        return view('admin.marketing.programas.index', compact(
            'programas', 'modulosFechas', 'estados', 'gestiones', 'gestion', 'estadoId', 'search', 'resumen', 'inversionesPorEstado'
        ));
    }

    public function updateInscritosMinimo(Request $request, $id)
    {
        $validated = $request->validate([
            'cantidad_inscritos_minimo' => 'required|integer|min:0|max:65535',
        ]);

        $updated = DB::table('programas')->where('id', $id)->update([
            'cantidad_inscritos_minimo' => $validated['cantidad_inscritos_minimo'],
        ]);

        if (!$updated && !DB::table('programas')->where('id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Mínimo de inscritos actualizado']);
    }

    public function inversiones(Request $request)
    {
        $gestionActual = now()->format('Y');
        $mesActual = (int) now()->format('m');
        $diaActual = (int) now()->format('d');
        $quincenaActual = $diaActual <= 15 ? 1 : 2;

        $gestion = $request->get('gestion', $gestionActual);
        $mes = $request->get('mes', (string) $mesActual);
        $quincena = $request->get('quincena', (string) $quincenaActual);

        $filtrarMes = $mes !== 'todas';
        $filtrarQuincena = $quincena !== 'todas';

        $montoGastado = "SUM(CASE WHEN estado = 'aprobado_obs' THEN COALESCE(monto_aprobado, monto) ELSE monto END)";
        $montoAprobado = "SUM(CASE WHEN estado = 'aprobado' THEN monto WHEN estado = 'aprobado_obs' THEN COALESCE(monto_aprobado, monto) ELSE 0 END)";

        $base = DB::table('marketing_comprobantes_publicidad')
            ->where('marketing_comprobantes_publicidad.gestion', $gestion)
            ->when($filtrarMes, fn($q) => $q->where('marketing_comprobantes_publicidad.mes', (int) $mes))
            ->when($filtrarQuincena, fn($q) => $q->where('marketing_comprobantes_publicidad.quincena', (int) $quincena));

        $porPrograma = (clone $base)
            ->join('programas', 'marketing_comprobantes_publicidad.programa_id', '=', 'programas.id')
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                DB::raw('COUNT(*) as total_comprobantes'),
                DB::raw("{$montoGastado} as total_monto"),
                DB::raw("{$montoAprobado} as total_aprobado")
            )
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total_aprobado')
            ->get();

        $porProgramaInscripciones = (clone $base)
            ->join('programas', 'marketing_comprobantes_publicidad.programa_id', '=', 'programas.id')
            ->where('programas.estado_id', 4)
            ->select(
                'programas.id', 'programas.nombre', 'programas.codigo',
                DB::raw("{$montoGastado} as total_monto"),
                DB::raw("{$montoAprobado} as total_aprobado")
            )
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total_aprobado')
            ->get();

        $reclutamiento = (clone $base)
            ->whereNull('marketing_comprobantes_publicidad.programa_id')
            ->select(
                DB::raw('COUNT(*) as total_comprobantes'),
                DB::raw("{$montoGastado} as total_monto"),
                DB::raw("{$montoAprobado} as total_aprobado")
            )
            ->first();

        // ─── Autogestión por programa ───
        $autoPorPrograma = DB::table('marketing_gastos_autogestion as g')
            ->leftJoin('programas', 'g.programa_id', '=', 'programas.id')
            ->where('g.gestion', $gestion)
            ->when($filtrarMes, fn($q) => $q->where('g.mes', (int) $mes))
            ->select(
                DB::raw('COALESCE(g.programa_id, 0) as id'),
                'programas.nombre',
                'programas.codigo',
                DB::raw('COUNT(*) as total_gastos'),
                DB::raw('SUM(g.monto) as total_monto')
            )
            ->groupBy(DB::raw('COALESCE(g.programa_id, 0)'), 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total_monto')
            ->get();

        // ─── Merge comprobantes + autogestión ───
        $compKeyed = $porPrograma->keyBy('id');
        $autoKeyed = $autoPorPrograma->keyBy('id');
        $allIds = $compKeyed->keys()->merge($autoKeyed->keys())->unique()->sort();

        $programasResumen = $allIds->map(function ($id) use ($compKeyed, $autoKeyed) {
            $comp = $compKeyed->get($id);
            $auto = $autoKeyed->get($id);
            $compMonto = $comp ? (float) $comp->total_monto : 0;
            $autoMonto = $auto ? (float) $auto->total_monto : 0;
            $nombre = $comp->nombre ?? $auto->nombre ?? 'Sin programa';
            $codigo = $comp->codigo ?? $auto->codigo ?? '';
            return (object) [
                'programa_label' => $nombre . ($codigo ? " ({$codigo})" : ''),
                'comp_monto' => $compMonto,
                'comp_comprobantes' => $comp ? (int) $comp->total_comprobantes : 0,
                'comp_aprobado' => $comp ? (float) $comp->total_aprobado : 0,
                'auto_monto' => $autoMonto,
                'auto_gastos' => $auto ? (int) $auto->total_gastos : 0,
                'total_invertido' => $compMonto + $autoMonto,
            ];
        })->sortByDesc('total_invertido')->values();

        $soloAuto = $programasResumen->filter(fn($p) => $p->auto_monto > 0)->values();

        $gestiones = DB::table('marketing_comprobantes_publicidad')
            ->select('gestion')->distinct()->orderByDesc('gestion')->pluck('gestion');
        if ($gestiones->isEmpty()) {
            $gestiones = collect([(int) $gestionActual]);
        }

        $resumen = [
            'total_programas' => $porPrograma->count(),
            'total_gastado' => (float) $porPrograma->sum('total_monto') + (float) ($reclutamiento->total_monto ?? 0),
            'total_invertido' => (float) $porPrograma->sum('total_aprobado') + (float) ($reclutamiento->total_aprobado ?? 0),
            'total_comprobantes' => (int) $porPrograma->sum('total_comprobantes') + (int) ($reclutamiento->total_comprobantes ?? 0),
        ];

        $meses = [
            'todas' => 'Todos los meses',
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return view('admin.marketing.inversiones.index', compact(
            'porPrograma', 'porProgramaInscripciones',
            'gestion', 'mes', 'quincena', 'gestiones', 'resumen', 'meses',
            'reclutamiento', 'soloAuto', 'programasResumen'
        ));
    }

    public function estadisticas(Request $request)
    {
        $mesActual = now()->format('m');
        $gestionActual = now()->format('Y');

        $mes = $request->get('mes', $mesActual);
        $gestion = $request->get('gestion', $gestionActual);
        $tipo = $request->get('tipo', 'general');
        $filtrarMes = $mes !== 'todos';

        $scopeTipo = fn($q) => match ($tipo) {
            'upi' => $q->where('programas.tipo_id', 6),
            'upi-auto' => $q->where('programas.tipo_id', 7),
            'otros' => $q->whereNotIn('programas.tipo_id', [6, 7]),
            default => $q,
        };

        $porArea = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('areas_programas', 'programas.id', '=', 'areas_programas.programa_id')
            ->join('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select('areas.id', 'areas.nombre', 'areas.color', DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 0 THEN 1 ELSE 0 END) as total'))
            ->groupBy('areas.id', 'areas.nombre', 'areas.color')
            ->orderByDesc('total')->get();

        $porTipo = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select('tipos.id', 'tipos.nombre', DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 0 THEN 1 ELSE 0 END) as total'))
            ->groupBy('tipos.id', 'tipos.nombre')
            ->orderByDesc('total')->get();

        $porPrograma = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select('programas.id', 'programas.nombre', 'programas.codigo', DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 0 THEN 1 ELSE 0 END) as total'))
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total')->get();

        // --- Inscripciones por mes (gestión) ---
        $porMes = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo)
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $chartMeses = ['total' => [], 'contado' => [], 'regulares' => [], 'traspasos' => []];
        foreach (range(1, 12) as $m) {
            $r = $porMes->get($m);
            $chartMeses['total'][]     = $r ? (int) $r->total - (int) $r->traspasos : 0;
            $chartMeses['contado'][]   = $r ? (int) $r->contado : 0;
            $chartMeses['regulares'][] = $r ? (int) $r->regulares : 0;
            $chartMeses['traspasos'][] = $r ? (int) $r->traspasos : 0;
        }

        // --- Top Asesores ---
        $topAsesores = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select(
                'asesores.id', 'asesores.nombres', 'asesores.apellidos',
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 0 THEN 1 ELSE 0 END) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 1 THEN 1 ELSE 0 END) as activos'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 0 THEN 1 ELSE 0 END) as retirados'),
            )
            ->groupBy('asesores.id', 'asesores.nombres', 'asesores.apellidos')
            ->get();

        $sinAsesor = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereNull('inscripciones.asesor_id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select(
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 0 THEN 1 ELSE 0 END) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 1 THEN 1 ELSE 0 END) as activos'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 0 THEN 1 ELSE 0 END) as retirados'),
            )
            ->first();

        // --- Top Asesores: Puntos por tipo ---
        $tiposEncontrados = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereNotNull('inscripciones.asesor_id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select('tipos.nombre')
            ->distinct()
            ->orderBy('tipos.nombre')
            ->pluck('tipos.nombre');

        $puntosPorAsesor = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereNotNull('inscripciones.asesor_id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->select(
                'inscripciones.asesor_id',
                'tipos.nombre as tipo_nombre',
                DB::raw('SUM(tipos.puntos) as puntos')
            )
            ->groupBy('inscripciones.asesor_id', 'tipos.nombre')
            ->get()
            ->groupBy('asesor_id');

        $topAsesores->each(function ($a) use ($puntosPorAsesor, $tiposEncontrados) {
            $pa = $puntosPorAsesor->get($a->id, collect())->keyBy('tipo_nombre');
            foreach ($tiposEncontrados as $tipo) {
                $col = 'puntos_' . Str::slug($tipo, '_');
                $a->$col = (float) ($pa->get($tipo)?->puntos ?? 0);
            }
            $a->total_puntos = $pa->sum('puntos');
        });

        $topAsesores = $topAsesores->sortByDesc(fn($a) => $a->total_puntos)->values()->take(10);

        // --- Programas con sus inscritos ---
        $programas = DB::table('programas')
            ->where('gestion', $gestion)
            ->when($scopeTipo)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo', 'gestion']);

        $inscripcionesPorPrograma = [];
        foreach ($programas as $p) {
            $rows = DB::table('inscripciones')
                ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
                ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
                ->where('inscripciones.programa_id', $p->id)
                ->whereYear('inscripciones.fecha_inscripcion', $gestion)
                ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
                ->select(
                    'participantes.id as participante_id', 'participantes.ci', 'participantes.nombres',
                    'participantes.apellidos', 'participantes.sexo', 'participantes.departamento',
                    'inscripciones.fecha_inscripcion', 'inscripciones.estado', 'inscripciones.contado',
                    'inscripciones.es_traspaso',
                    'asesores.id as asesor_id', 'asesores.nombres as asesor_nombres', 'asesores.apellidos as asesor_apellidos',
                )
                ->orderBy('participantes.apellidos')
                ->get();

            if ($rows->count() > 0) {
                $inscripcionesPorPrograma[$p->id] = [
                    'programa' => $p,
                    'inscripciones' => $rows,
                    'total' => $rows->where('es_traspaso', 0)->count(),
                ];
            }
        }

        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
        ];

        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()->orderByDesc('gestion')->pluck('gestion');

        $totalTraspasos = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($filtrarMes, fn($q) => $q->whereMonth('inscripciones.fecha_inscripcion', $mes))
            ->when($scopeTipo)
            ->where('inscripciones.es_traspaso', 1)
            ->count();

        $resumen = [
            'total_inscritos' => $porPrograma->sum('total'),
            'total_programas' => $porPrograma->count(),
            'total_areas' => $porArea->count(),
            'total_asesores' => $topAsesores->count(),
            'total_traspasos' => $totalTraspasos,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'resumen' => $resumen,
                'chart_meses' => $chartMeses,
                'por_area' => $porArea,
                'por_tipo' => $porTipo,
                'por_programa' => $porPrograma,
                'top_asesores' => $topAsesores,
                'sin_asesor' => $sinAsesor,
                'tipos_encontrados' => $tiposEncontrados,
            ]);
        }

        return view('admin.marketing.estadisticas.index', compact(
            'porArea', 'porTipo', 'porPrograma', 'chartMeses',
            'topAsesores', 'sinAsesor', 'tiposEncontrados', 'inscripcionesPorPrograma',
            'mes', 'gestion', 'tipo', 'meses', 'gestiones', 'resumen'
        ));
    }

    public function asesores(Request $request)
    {
        $gestionActual = now()->format('Y');
        $gestion = $request->get('gestion', $gestionActual);
        $asesorId = $request->get('asesor_id', '');
        $tipo = $request->get('tipo', 'general');

        $scopeTipo = fn($q) => match ($tipo) {
            'upi' => $q->where('programas.tipo_id', 6),
            'upi-auto' => $q->where('programas.tipo_id', 7),
            'otros' => $q->whereNotIn('programas.tipo_id', [6, 7]),
            default => $q,
        };

        // Asesores con conteo de inscritos en la gestión seleccionada, orden descendente
        $asesores = DB::table('asesores')
            ->leftJoin('inscripciones', function ($j) use ($gestion) {
                $j->on('inscripciones.asesor_id', '=', 'asesores.id')
                  ->whereYear('inscripciones.fecha_inscripcion', $gestion);
            })
            ->leftJoin('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->when($scopeTipo)
            ->select(
                'asesores.id', 'asesores.nombres', 'asesores.apellidos',
                DB::raw('COUNT(inscripciones.id) as total_inscritos'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as total_puntos')
            )
            ->groupBy('asesores.id', 'asesores.nombres', 'asesores.apellidos')
            ->orderByDesc('total_inscritos')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->get();

        $gestiones = DB::table('programas')
            ->select('gestion')
            ->distinct()
            ->orderByDesc('gestion')
            ->pluck('gestion');

        if ($gestiones->isEmpty()) {
            $gestiones = collect([(int) $gestionActual]);
        }

        $filtrarPorAsesor = $asesorId !== '' && $asesorId !== null;

        // --- Inscritos por mes, desglosados por tipo de programa ---
        $inscritosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $inscritosPorTipoMesQ->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $inscritosPorTipoMes = $inscritosPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $inscritosPorMesTipo = [];
        $tiposInscritosSet = collect();
        foreach ($inscritosPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $tiposInscritosSet->push($f->tipo_nombre);
                $inscritosPorMesTipo[(int)$mes][$f->tipo_nombre] = (int)$f->total;
            }
        }
        $tipoInscritosLabels = $tiposInscritosSet->unique()->sort()->values()->toArray();

        $inscritosTipoSeries = [];
        $coloresInscritos = ['#299cdb', '#8b5cf6', '#0ab39c', '#f7b84b', '#f06548', '#405189'];
        foreach ($tipoInscritosLabels as $i => $tipo) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $inscritosPorMesTipo[$m][$tipo] ?? 0;
            }
            $inscritosTipoSeries[] = [
                'name' => $tipo,
                'data' => $serie,
                'color' => $coloresInscritos[$i % count($coloresInscritos)],
            ];
        }

        // --- Puntos por mes, desglosados por tipo de programa ---
        $puntosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $puntosPorTipoMesQ->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $puntosPorTipoMes = $puntosPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $puntosPorMesTipo = [];
        $tiposSet = collect();
        foreach ($puntosPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $tiposSet->push($f->tipo_nombre);
                $puntosPorMesTipo[(int)$mes][$f->tipo_nombre] = (float)$f->puntos;
            }
        }
        $tipoLabels = $tiposSet->unique()->sort()->values()->toArray();

        $puntosTipoSeries = [];
        $puntosTipoColores = ['#299cdb', '#8b5cf6', '#0ab39c', '#f7b84b', '#f06548', '#405189'];
        foreach ($tipoLabels as $i => $tipo) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosPorMesTipo[$m][$tipo] ?? 0;
            }
            $puntosTipoSeries[] = [
                'name' => $tipo,
                'data' => $serie,
                'color' => $puntosTipoColores[$i % count($puntosTipoColores)],
            ];
        }

        // --- Puntos Reales por mes, desglosados por tipo de programa ---
        $realesCond = "
            (inscripciones.contado = 0
             AND COALESCE((
                 SELECT SUM(d.monto_bs)
                 FROM modulos_participantes mp
                 JOIN deudas d ON d.modulo_participante_id = mp.id
                 WHERE mp.participante_id = inscripciones.participante_id AND mp.tipo = 'Matricula'
             ), 0) >= (inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0))
             AND EXISTS (
                 SELECT 1
                 FROM modulos_participantes mp2
                 JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                 WHERE mp2.participante_id = inscripciones.participante_id AND mp2.tipo = 'Colegiatura' AND d2.monto_bs > 0
             ))
            OR
            (inscripciones.contado = 1
             AND (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) > 0
             AND COALESCE((
                 SELECT SUM(d3.monto_bs)
                 FROM modulos_participantes mp3
                 JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                 WHERE mp3.participante_id = inscripciones.participante_id AND mp3.tipo = 'Colegiatura'
             ), 0) >= (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = inscripciones.programa_id))
        ";

        $puntosRealesPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $puntosRealesPorTipoMesQ->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $puntosRealesPorTipoMesQ->whereRaw("({$realesCond})");

        $puntosRealesPorTipoMes = $puntosRealesPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $puntosRealesPorMesTipo = [];
        $realesTiposSet = collect();
        foreach ($puntosRealesPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $realesTiposSet->push($f->tipo_nombre);
                $puntosRealesPorMesTipo[(int)$mes][$f->tipo_nombre] = (float)$f->puntos;
            }
        }
        $realesTipoLabels = $realesTiposSet->unique()->sort()->values()->toArray();

        $puntosRealesTipoSeries = [];
        foreach ($realesTipoLabels as $i => $tipo) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosRealesPorMesTipo[$m][$tipo] ?? 0;
            }
            $puntosRealesTipoSeries[] = [
                'name' => $tipo,
                'data' => $serie,
                'color' => $puntosTipoColores[$i % count($puntosTipoColores)],
            ];
        }

        // --- Reales por mes desglosados por tipo de programa ---
        $realesInscritosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $realesInscritosPorTipoMesQ->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $realesInscritosPorTipoMesQ->whereRaw("({$realesCond})");

        $realesInscritosPorTipoMes = $realesInscritosPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $realesInscritosPorMesTipo = [];
        $realesInscritosTiposSet = collect();
        foreach ($realesInscritosPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $realesInscritosTiposSet->push($f->tipo_nombre);
                $realesInscritosPorMesTipo[(int)$mes][$f->tipo_nombre] = (int)$f->total;
            }
        }
        $realesInscritosTipoLabels = $realesInscritosTiposSet->unique()->sort()->values()->toArray();

        $realesInscritosTipoSeries = [];
        foreach ($tipoInscritosLabels as $i => $tipo) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $realesInscritosPorMesTipo[$m][$tipo] ?? 0;
            }
            $realesInscritosTipoSeries[] = [
                'name' => $tipo,
                'data' => $serie,
                'color' => $coloresInscritos[$i % count($coloresInscritos)],
            ];
        }

        // No Reales by tipo = total - reales
        $noRealesInscritosTipoSeries = [];
        foreach ($inscritosTipoSeries as $i => $s) {
            $serie = [];
            foreach (range(0, 11) as $m) {
                $total = (int)($s['data'][$m] ?? 0);
                $reales = (int)($realesInscritosTipoSeries[$i]['data'][$m] ?? 0);
                $serie[] = max(0, $total - $reales);
            }
            $noRealesInscritosTipoSeries[] = [
                'name' => $s['name'],
                'data' => $serie,
                'color' => $coloresInscritos[$i % count($coloresInscritos)],
            ];
        }

        // --- Inscripciones por mes (gestión, todos los asesores o uno específico) ---
        $porMesQuery = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $porMesQuery->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $porMes = $porMesQuery
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Monthly reales (separate query to avoid GROUP BY / correlated subquery conflict)
        $realesSql = "
            SELECT MONTH(i.fecha_inscripcion) as mes, COUNT(*) as reales, COALESCE(SUM(t.puntos), 0) as puntos_reales
            FROM inscripciones i
            JOIN programas p ON i.programa_id = p.id
            LEFT JOIN tipos t ON p.tipo_id = t.id
            WHERE YEAR(i.fecha_inscripcion) = ?
        ";
        $realesBindings = [$gestion];

        if ($filtrarPorAsesor) {
            $realesSql .= " AND i.asesor_id = ?";
            $realesBindings[] = (int) $asesorId;
        }

        if ($tipo === 'upi') {
            $realesSql .= " AND p.tipo_id = 6";
        } elseif ($tipo === 'upi-auto') {
            $realesSql .= " AND p.tipo_id = 7";
        } elseif ($tipo === 'otros') {
            $realesSql .= " AND p.tipo_id NOT IN (6, 7)";
        }

        $realesSql .= "
            AND (
                (i.contado = 0
                 AND COALESCE((
                     SELECT SUM(d.monto_bs)
                     FROM modulos_participantes mp
                     JOIN deudas d ON d.modulo_participante_id = mp.id
                     WHERE mp.participante_id = i.participante_id AND mp.tipo = 'Matricula'
                 ), 0) >= (i.matricula - COALESCE(i.descuento_matricula, 0))
                 AND EXISTS (
                     SELECT 1
                     FROM modulos_participantes mp2
                     JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                     WHERE mp2.participante_id = i.participante_id AND mp2.tipo = 'Colegiatura' AND d2.monto_bs > 0
                 ))
                OR
                (i.contado = 1
                 AND (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) > 0
                 AND COALESCE((
                     SELECT SUM(d3.monto_bs)
                     FROM modulos_participantes mp3
                     JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                     WHERE mp3.participante_id = i.participante_id AND mp3.tipo = 'Colegiatura'
                 ), 0) >= (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = i.programa_id))
            )
            GROUP BY MONTH(i.fecha_inscripcion)
            ORDER BY mes
        ";

        $realesPorMes = collect(DB::select($realesSql, $realesBindings))->keyBy('mes');

        $mesesData = ['total' => [], 'regulares' => [], 'contado' => [], 'traspasos' => [], 'puntos' => [], 'reales' => [], 'puntos_reales' => []];
        foreach (range(1, 12) as $m) {
            $r = $porMes->get($m);
            $rr = $realesPorMes->get($m);
            $mesesData['total'][]       = $r ? (int) $r->total : 0;
            $mesesData['regulares'][]   = $r ? (int) $r->regulares : 0;
            $mesesData['contado'][]     = $r ? (int) $r->contado : 0;
            $mesesData['traspasos'][]   = $r ? (int) $r->traspasos : 0;
            $mesesData['puntos'][]      = $r ? (float) $r->puntos : 0;
            $mesesData['reales'][]      = $rr ? (int) $rr->reales : 0;
            $mesesData['puntos_reales'][] = $rr ? (float) $rr->puntos_reales : 0;
        }

        // --- Inscritos por cada programa (gestión, todos los asesores o uno específico) ---
        $porProgramaQuery = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $porProgramaQuery->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $gestionInt = (int) $gestion;
        $asesorFilterSub = $filtrarPorAsesor ? " AND i2.asesor_id = " . (int) $asesorId : "";
        $inscritosRealesSubquery = "
            SELECT COUNT(*)
            FROM inscripciones i2
            WHERE i2.programa_id = programas.id
              AND YEAR(i2.fecha_inscripcion) = {$gestionInt}{$asesorFilterSub}
              AND (
                (i2.contado = 0
                 AND COALESCE((
                     SELECT SUM(d.monto_bs)
                     FROM modulos_participantes mp
                     JOIN deudas d ON d.modulo_participante_id = mp.id
                     WHERE mp.participante_id = i2.participante_id AND mp.tipo = 'Matricula'
                 ), 0) >= (i2.matricula - COALESCE(i2.descuento_matricula, 0))
                 AND EXISTS (
                     SELECT 1
                     FROM modulos_participantes mp2
                     JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                     WHERE mp2.participante_id = i2.participante_id AND mp2.tipo = 'Colegiatura' AND d2.monto_bs > 0
                 )
                )
                OR
                (i2.contado = 1
                 AND (i2.monto_programa - COALESCE(i2.descuento_colegiatura, 0)) > 0
                 AND COALESCE((
                     SELECT SUM(d3.monto_bs)
                     FROM modulos_participantes mp3
                     JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                     WHERE mp3.participante_id = i2.participante_id AND mp3.tipo = 'Colegiatura'
                 ), 0) >= (i2.monto_programa - COALESCE(i2.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = programas.id)
                )
              )
        ";

        $puntosRealesSubquery = "
            SELECT COALESCE(SUM(t.puntos), 0)
            FROM inscripciones i2
            JOIN programas p2 ON i2.programa_id = p2.id
            LEFT JOIN tipos t ON p2.tipo_id = t.id
            WHERE i2.programa_id = programas.id
              AND YEAR(i2.fecha_inscripcion) = {$gestionInt}{$asesorFilterSub}
              AND (
                (i2.contado = 0
                 AND COALESCE((
                     SELECT SUM(d.monto_bs)
                     FROM modulos_participantes mp
                     JOIN deudas d ON d.modulo_participante_id = mp.id
                     WHERE mp.participante_id = i2.participante_id AND mp.tipo = 'Matricula'
                 ), 0) >= (i2.matricula - COALESCE(i2.descuento_matricula, 0))
                 AND EXISTS (
                     SELECT 1
                     FROM modulos_participantes mp2
                     JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                     WHERE mp2.participante_id = i2.participante_id AND mp2.tipo = 'Colegiatura' AND d2.monto_bs > 0
                 ))
                OR
                (i2.contado = 1
                 AND (i2.monto_programa - COALESCE(i2.descuento_colegiatura, 0)) > 0
                 AND COALESCE((
                     SELECT SUM(d3.monto_bs)
                     FROM modulos_participantes mp3
                     JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                     WHERE mp3.participante_id = i2.participante_id AND mp3.tipo = 'Colegiatura'
                 ), 0) >= (i2.monto_programa - COALESCE(i2.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = programas.id))
              )
        ";

        $porPrograma = $porProgramaQuery
            ->select(
                'programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion',
                'tipos.nombre as tipo_nombre',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 1 AND (inscripciones.baja_operativa = 0 OR inscripciones.baja_operativa IS NULL) AND (inscripciones.baja_academica = 0 OR inscripciones.baja_academica IS NULL) THEN 1 ELSE 0 END) as activos'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 0 OR inscripciones.baja_operativa = 1 OR inscripciones.baja_academica = 1 THEN 1 ELSE 0 END) as retirados'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regular'),
                DB::raw('SUM(CASE WHEN inscripciones.es_traspaso = 1 THEN 1 ELSE 0 END) as traspasos'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos'),
                DB::raw("({$inscritosRealesSubquery}) as inscritos_reales"),
                DB::raw("({$puntosRealesSubquery}) as puntos_reales"),
            )
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion', 'tipos.nombre')
            ->orderByDesc('total')
            ->get();

        // --- Inscritos detallados (nombre + plan + estado + asesor) por programa ---
        $programaIds = $porPrograma->pluck('id');
        $inscritosDetalleQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->whereIn('inscripciones.programa_id', $programaIds)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $inscritosDetalleQ->where('inscripciones.asesor_id', (int) $asesorId);
        }

        $inscritosPorPrograma = $inscritosDetalleQ
            ->select(
                'inscripciones.programa_id',
                'inscripciones.contado',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica',
                'inscripciones.es_traspaso',
                'inscripciones.fecha_inscripcion',
                'inscripciones.matricula',
                'inscripciones.descuento_matricula',
                'inscripciones.monto_programa',
                'inscripciones.descuento_colegiatura',
                'programas.modulo_cero',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'asesores.nombres as asesor_nombres',
                'asesores.apellidos as asesor_apellidos',
                DB::raw("CASE WHEN ({$realesCond}) THEN 1 ELSE 0 END as es_real"),
                DB::raw("(inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0)) as matricula_requerido"),
                DB::raw("COALESCE((
                    SELECT SUM(d.monto_bs)
                    FROM modulos_participantes mp
                    JOIN deudas d ON d.modulo_participante_id = mp.id
                    WHERE mp.participante_id = inscripciones.participante_id AND mp.tipo = 'Matricula'
                ), 0) as matricula_pagado"),
                DB::raw("COALESCE((
                    SELECT SUM(d2.monto_bs)
                    FROM modulos_participantes mp2
                    JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                    WHERE mp2.participante_id = inscripciones.participante_id AND mp2.tipo = 'Colegiatura'
                ), 0) as colegiatura_pagado"),
                DB::raw("(SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = inscripciones.programa_id) as num_modulos"),
                DB::raw("COALESCE((
                    SELECT MIN(d3.monto_bs)
                    FROM modulos_participantes mp3
                    JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                    WHERE mp3.participante_id = inscripciones.participante_id AND mp3.tipo = 'Colegiatura' AND d3.monto_bs > 0
                ), 0) as colegiatura_primer_monto"),
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->groupBy('programa_id');

        $asesorActual = null;
        if ($asesorId !== '' && $asesorId !== null) {
            $asesorActual = DB::table('asesores')->where('id', (int) $asesorId)->first();
        }

        $totalReales = (int) $porPrograma->sum('inscritos_reales');
        $resumen = [
            'total_inscritos' => (int) $porPrograma->sum('total'),
            'total_programas' => $porPrograma->count(),
            'total_asesores' => $asesorActual ? 1 : $asesores->count(),
            'total_activos'  => (int) $porPrograma->sum('activos'),
            'total_puntos'   => (float) $porPrograma->sum('puntos'),
            'total_reales'   => $totalReales,
            'total_no_reales' => (int) $porPrograma->sum('total') - $totalReales,
            'total_puntos_reales' => (float) $porPrograma->sum('puntos_reales'),
            'total_traspasos' => (int) $porPrograma->sum('traspasos'),
        ];

        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
        ];

        $cumplimientoMetas = $this->metasCumplimiento($gestion, $tipo, $scopeTipo, $realesCond, $asesorId);

        return view('admin.marketing.asesores.index', compact(
            'asesores', 'gestiones', 'gestion', 'asesorId', 'asesorActual',
            'mesesData', 'porPrograma', 'inscritosPorPrograma', 'resumen', 'meses', 'tipo',
            'puntosTipoSeries', 'inscritosTipoSeries', 'puntosRealesTipoSeries',
            'realesInscritosTipoSeries', 'noRealesInscritosTipoSeries',
            'cumplimientoMetas'
        ));
    }

    public function asesorDetalle(Request $request)
    {
        $asesorId = $request->get('asesor_id');
        $gestion = $request->get('gestion', now()->format('Y'));
        $mes = $request->get('mes', now()->format('m'));

        $asesor = DB::table('asesores')->where('id', $asesorId)->first();
        if (!$asesor) {
            return response()->json(['success' => false, 'message' => 'Asesor no encontrado']);
        }

        $programas = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('inscripciones.asesor_id', $asesorId)
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->whereMonth('inscripciones.fecha_inscripcion', $mes)
            ->select(
                'programas.id as programa_id', 'programas.nombre as programa_nombre',
                'participantes.id as participante_id', 'participantes.ci',
                'participantes.nombres', 'participantes.apellidos',
                'inscripciones.fecha_inscripcion', 'inscripciones.estado', 'inscripciones.contado',
            )
            ->orderBy('programas.nombre')
            ->orderBy('participantes.apellidos')
            ->get()
            ->groupBy('programa_id');

        $html = '';
        foreach ($programas as $pid => $rows) {
            $pnombre = $rows->first()->programa_nombre;
            $html .= '<div class="mb-3"><h6 class="fw-semibold fs-13 mb-2 text-primary"><i class="ri-booklet-line me-1"></i>' . e($pnombre) . ' <span class="text-muted fs-11 fw-normal">(' . $rows->count() . ' inscritos)</span></h6>';
            $html .= '<div class="table-responsive"><table class="table table-sm table-bordered mb-0 fs-12"><thead class="table-light"><tr><th>#</th><th>Participante</th><th>CI</th><th>Fecha</th><th>Pago</th><th>Estado</th></tr></thead><tbody>';
            foreach ($rows as $i => $r) {
                $pago = $r->contado ? 'Contado' : 'Regular';
                $estado = $r->estado ? 'Activo' : 'Retirado';
                $estadoClass = $r->estado ? 'success' : 'danger';
                $html .= '<tr>';
                $html .= '<td class="text-center text-muted">' . ($i + 1) . '</td>';
                $html .= '<td class="fw-semibold">' . e($r->apellidos) . ', ' . e($r->nombres) . '</td>';
                $html .= '<td>' . e($r->ci) . '</td>';
                $html .= '<td>' . \Carbon\Carbon::parse($r->fecha_inscripcion)->format('d/m/Y') . '</td>';
                $html .= '<td><span class="badge bg-soft-' . ($pago === 'Contado' ? 'success' : 'info') . ' text-' . ($pago === 'Contado' ? 'success' : 'info') . ' fs-10">' . $pago . '</span></td>';
                $html .= '<td><span class="badge bg-soft-' . $estadoClass . ' text-' . $estadoClass . ' fs-10">' . $estado . '</span></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table></div></div>';
        }

        if (!$html) {
            $html = '<p class="text-muted text-center py-3 mb-0"><i class="ri-inbox-2-line me-1"></i>No hay inscripciones de este asesor en el período</p>';
        }

        return response()->json([
            'success' => true,
            'asesor' => $asesor,
            'html' => $html,
            'total' => $programas->flatten()->count(),
        ]);
    }

    public function storeAsesor(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:10|unique:asesores,ci',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'sexo' => 'required|string|max:100',
            'celular' => 'required|string|max:10',
            'correo' => 'required|email|max:255|unique:asesores,correo',
            'cargo_id' => 'nullable|integer|exists:cargos,id',
            'estado' => 'nullable|string|max:255',
        ]);

        $id = DB::table('asesores')->insertGetId([
            'ci' => $validated['ci'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'sexo' => $validated['sexo'],
            'celular' => $validated['celular'],
            'correo' => $validated['correo'],
            'cargo_id' => $validated['cargo_id'] ?? null,
            'estado' => $validated['estado'] ?? 'Habilitado',
            'foto' => '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asesor registrado correctamente',
            'id' => $id,
            'asesor' => DB::table('asesores')->where('id', $id)->first(),
        ]);
    }

    public function asesorShow(Request $request, $id)
    {
        $asesor = DB::table('asesores')->where('id', $id)->first();
        if (!$asesor) {
            abort(404, 'Asesor no encontrado');
        }

        $cargo = $asesor->cargo_id ? DB::table('cargos')->where('id', $asesor->cargo_id)->first() : null;
        $asesor->cargo_nombre = $cargo->nombre ?? null;
        $metaMensual = $cargo ? (float) ($cargo->metas_puntos ?? 0) : 0;

        $gestionFiltro = $request->get('gestion', now()->format('Y'));
        $mesFiltro = $request->get('mes', now()->format('m'));
        $tipo = $request->get('tipo', 'general');

        $scopeTipo = fn($q) => match ($tipo) {
            'upi' => $q->where('programas.tipo_id', 6),
            'upi-auto' => $q->where('programas.tipo_id', 7),
            'otros' => $q->whereNotIn('programas.tipo_id', [6, 7]),
            default => $q,
        };

        $query = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.asesor_id', $id)
            ->when($scopeTipo);

        if ($gestionFiltro !== '') {
            $query->whereYear('inscripciones.fecha_inscripcion', $gestionFiltro);
        }
        if ($mesFiltro !== '') {
            $query->whereMonth('inscripciones.fecha_inscripcion', $mesFiltro);
        }

        $inscripciones = $query->select(
            'inscripciones.id as inscripcion_id',
            'participantes.id as participante_id',
            'participantes.ci',
            'participantes.nombres',
            'participantes.apellidos',
            'participantes.sexo',
            'participantes.celular',
            'participantes.correo',
            'participantes.departamento',
            'programas.id as programa_id',
            'programas.nombre as programa_nombre',
            'programas.codigo as programa_codigo',
            'programas.gestion',
            'tipos.nombre as tipo_nombre',
            'tipos.puntos',
            'inscripciones.fecha_inscripcion',
            'inscripciones.estado',
            'inscripciones.baja_operativa',
            'inscripciones.baja_academica',
            'inscripciones.contado'
        )->orderByDesc('inscripciones.fecha_inscripcion')->get();

        $puntosTotal = $inscripciones->sum(fn($r) => (float)($r->puntos ?? 0));

        $stats = [
            'total' => $inscripciones->count(),
            'activos' => $inscripciones->filter(fn($r) => (int)$r->estado === 1 && !(int)($r->baja_operativa ?? 0) && !(int)($r->baja_academica ?? 0))->count(),
            'retirados' => $inscripciones->filter(fn($r) => (int)$r->estado === 0 || (int)($r->baja_operativa ?? 0) || (int)($r->baja_academica ?? 0))->count(),
            'contado' => $inscripciones->where('contado', 1)->count(),
            'regular' => $inscripciones->where('contado', 0)->count(),
            'programas' => $inscripciones->pluck('programa_id')->unique()->count(),
            'hombres' => $inscripciones->where('sexo', 'Hombre')->count(),
            'mujeres' => $inscripciones->where('sexo', 'Mujer')->count(),
            'puntos' => $puntosTotal,
        ];

        $porPrograma = $inscripciones->groupBy('programa_id');

        // --- Inscripciones de otras sedes del asesor ---
        $inscripcionesOtraSede = DB::table('inscripciones_otras_sedes')
            ->join('participantes', 'participantes.id', '=', 'inscripciones_otras_sedes.participante_id')
            ->leftJoin('programas_otras_sedes', 'programas_otras_sedes.id', '=', 'inscripciones_otras_sedes.programa_id')
            ->where('inscripciones_otras_sedes.asesor_id', $id)
            ->when($gestionFiltro !== '', function ($q) use ($gestionFiltro) {
                $q->where('programas_otras_sedes.gestion', $gestionFiltro);
            })
            ->select(
                'inscripciones_otras_sedes.*',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.sexo',
                'participantes.celular',
                'participantes.correo',
                'participantes.departamento',
                'programas_otras_sedes.nombre as programa_nombre',
                'programas_otras_sedes.codigo as programa_codigo',
                'programas_otras_sedes.sede_origen'
            )
            ->orderByDesc('inscripciones_otras_sedes.id')
            ->get();

        $statsOtraSede = [
            'total' => $inscripcionesOtraSede->count(),
            'activos' => $inscripcionesOtraSede->filter(fn($r) => (int) $r->estado === 1)->count(),
            'monto' => (float) $inscripcionesOtraSede->sum('monto_total'),
        ];

        // --- Chart data: inscripciones por mes ---
        $chartQuery = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.asesor_id', $id)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->when($scopeTipo);

        if ($gestionFiltro !== '') {
            $chartQuery->whereYear('inscripciones.fecha_inscripcion', $gestionFiltro);
        }

        $porMes = $chartQuery
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regulares'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $otrasPorMes = DB::table('inscripciones_otras_sedes')
            ->leftJoin('programas_otras_sedes', 'programas_otras_sedes.id', '=', 'inscripciones_otras_sedes.programa_id')
            ->leftJoin('tipos', 'tipos.id', '=', 'programas_otras_sedes.tipo_id')
            ->where('inscripciones_otras_sedes.asesor_id', $id)
            ->whereNotNull('inscripciones_otras_sedes.fecha_inscripcion')
            ->when($gestionFiltro !== '', function ($q) use ($gestionFiltro) {
                $q->where('programas_otras_sedes.gestion', $gestionFiltro);
            })
            ->select(
                DB::raw('MONTH(inscripciones_otras_sedes.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones_otras_sedes.fecha_inscripcion)'))
            ->get()
            ->keyBy('mes');

        $chartMeses = ['total' => [], 'contado' => [], 'regulares' => [], 'puntos' => [], 'otrasSedes' => []];
        $puntosOtrasSedes = [];
        foreach (range(1, 12) as $m) {
            $r = $porMes->get($m);
            $o = $otrasPorMes->get($m);
            $otras = $o ? (int) $o->total : 0;
            $chartMeses['total'][]     = ($r ? (int) $r->total : 0) + $otras;
            $chartMeses['contado'][]   = $r ? (int) $r->contado : 0;
            $chartMeses['regulares'][] = $r ? (int) $r->regulares : 0;
            $chartMeses['puntos'][]    = $r ? (float) $r->puntos : 0;
            $chartMeses['otrasSedes'][] = $otras;
            $puntosOtrasSedes[$m] = $o ? (float) $o->puntos : 0;
        }
        $puntosOtrasSedesArr = [];
        foreach (range(1, 12) as $m) {
            $puntosOtrasSedesArr[$m - 1] = $puntosOtrasSedes[$m];
        }

        // --- Inscritos por mes desglosados por tipo ---
        $inscritosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.asesor_id', $id)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->when($scopeTipo);

        if ($gestionFiltro !== '') {
            $inscritosPorTipoMesQ->whereYear('inscripciones.fecha_inscripcion', $gestionFiltro);
        }

        $inscritosPorTipoMes = $inscritosPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $inscritosPorMesTipo = [];
        $tiposInscritosSet = collect();
        foreach ($inscritosPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $tiposInscritosSet->push($f->tipo_nombre);
                $inscritosPorMesTipo[(int)$mes][$f->tipo_nombre] = (int)$f->total;
            }
        }
        $tipoInscritosLabels = $tiposInscritosSet->unique()->sort()->values()->toArray();
        $inscritosTipoSeries = [];
        $coloresInscritos = ['#299cdb', '#8b5cf6', '#0ab39c', '#f7b84b', '#f06548', '#405189'];
        foreach ($tipoInscritosLabels as $i => $t) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $inscritosPorMesTipo[$m][$t] ?? 0;
            }
            $inscritosTipoSeries[] = ['name' => $t, 'data' => $serie, 'color' => $coloresInscritos[$i % count($coloresInscritos)]];
        }

        // --- Puntos por mes desglosados por tipo ---
        $puntosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.asesor_id', $id)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->when($scopeTipo);

        if ($gestionFiltro !== '') {
            $puntosPorTipoMesQ->whereYear('inscripciones.fecha_inscripcion', $gestionFiltro);
        }

        $puntosPorTipoMes = $puntosPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $puntosPorMesTipo = [];
        $tiposPuntosSet = collect();
        foreach ($puntosPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $tiposPuntosSet->push($f->tipo_nombre);
                $puntosPorMesTipo[(int)$mes][$f->tipo_nombre] = (float)$f->puntos;
            }
        }
        $tipoPuntosLabels = $tiposPuntosSet->unique()->sort()->values()->toArray();
        $puntosTipoSeries = [];
        $coloresPuntos = ['#299cdb', '#8b5cf6', '#0ab39c', '#f7b84b', '#f06548', '#405189'];
        foreach ($tipoPuntosLabels as $i => $t) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosPorMesTipo[$m][$t] ?? 0;
            }
            $puntosTipoSeries[] = ['name' => $t, 'data' => $serie, 'color' => $coloresPuntos[$i % count($coloresPuntos)]];
        }
        $puntosTipoSeries[] = ['name' => 'Otras sedes', 'data' => $puntosOtrasSedesArr, 'color' => '#8b5cf6'];

        // --- Puntos Reales por mes desglosados por tipo ---
        $realesCond = "
            (inscripciones.contado = 0
             AND COALESCE((
                 SELECT SUM(d.monto_bs)
                 FROM modulos_participantes mp
                 JOIN deudas d ON d.modulo_participante_id = mp.id
                 WHERE mp.participante_id = inscripciones.participante_id AND mp.tipo = 'Matricula'
             ), 0) >= (inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0))
             AND EXISTS (
                 SELECT 1
                 FROM modulos_participantes mp2
                 JOIN deudas d2 ON d2.modulo_participante_id = mp2.id
                 WHERE mp2.participante_id = inscripciones.participante_id AND mp2.tipo = 'Colegiatura' AND d2.monto_bs > 0
             ))
            OR
            (inscripciones.contado = 1
             AND (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) > 0
             AND COALESCE((
                 SELECT SUM(d3.monto_bs)
                 FROM modulos_participantes mp3
                 JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                 WHERE mp3.participante_id = inscripciones.participante_id AND mp3.tipo = 'Colegiatura'
             ), 0) >= (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = inscripciones.programa_id))
        ";

        $puntosRealesPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.asesor_id', $id)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->when($scopeTipo);

        if ($gestionFiltro !== '') {
            $puntosRealesPorTipoMesQ->whereYear('inscripciones.fecha_inscripcion', $gestionFiltro);
        }

        $puntosRealesPorTipoMesQ->whereRaw("({$realesCond})");

        $puntosRealesPorTipoMes = $puntosRealesPorTipoMesQ
            ->select(
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw("COALESCE(tipos.nombre, 'Sin tipo') as tipo_nombre"),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy(DB::raw('MONTH(inscripciones.fecha_inscripcion)'), 'tipos.nombre')
            ->orderBy('mes')
            ->orderBy('tipo_nombre')
            ->get()
            ->groupBy('mes');

        $puntosRealesPorMesTipo = [];
        $realesTiposSet = collect();
        foreach ($puntosRealesPorTipoMes as $mes => $filas) {
            foreach ($filas as $f) {
                $realesTiposSet->push($f->tipo_nombre);
                $puntosRealesPorMesTipo[(int)$mes][$f->tipo_nombre] = (float)$f->puntos;
            }
        }
        $realesTipoLabels = $realesTiposSet->unique()->sort()->values()->toArray();

        $puntosRealesTipoSeries = [];
        foreach ($realesTipoLabels as $i => $t) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosRealesPorMesTipo[$m][$t] ?? 0;
            }
            $puntosRealesTipoSeries[] = ['name' => $t, 'data' => $serie, 'color' => $coloresPuntos[$i % count($coloresPuntos)]];
        }
        $puntosRealesTipoSeries[] = ['name' => 'Otras sedes', 'data' => $puntosOtrasSedesArr, 'color' => '#8b5cf6'];

        $gestiones = DB::table('inscripciones')
            ->where('inscripciones.asesor_id', $id)
            ->whereNotNull('inscripciones.fecha_inscripcion')
            ->select(DB::raw('YEAR(inscripciones.fecha_inscripcion) as gestion'))
            ->distinct()
            ->orderByDesc('gestion')
            ->pluck('gestion');

        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
        ];

        // --- Comprobantes de publicidad del asesor ---
        $comprobantesAsesor = DB::table('marketing_comprobantes_publicidad as c')
            ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
            ->leftJoin('users as v', 'v.id', '=', 'c.verificado_por')
            ->leftJoin('marketing_presupuestos_publicidad as p', 'p.id', '=', 'c.presupuesto_id')
            ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
            ->where('c.asesor_id', $id)
            ->select(
                'c.*',
                'a.nombres as asesor_nombres',
                'a.apellidos as asesor_apellidos',
                'v.fullname as verificador_nombre',
                'p.asesor_id as presupuesto_asesor_id',
                'p.monto_asignado as presupuesto_monto',
                'prg.nombre as programa_nombre'
            )
            ->orderBy('c.fecha_inicio', 'desc')
            ->limit(50)
            ->get();

        // Presupuestos activos donde el asesor es titular o supervisor (para el modal de subir)
        $presupuestosAsesor = DB::table('marketing_presupuestos_publicidad')
            ->where('estado', 'activo')
            ->where(function ($q) use ($id) {
                $q->where('asesor_id', $id)->orWhere('supervisor_id', $id);
            })
            ->orderBy('gestion', 'desc')
            ->orderBy('mes', 'desc')
            ->orderBy('quincena', 'desc')
            ->get();

        // Programas con estado "Inscripciones" (para el selector del modal)
        $programas = DB::table('programas')->where('estado_id', 4)->orderBy('nombre')->get(['id', 'nombre', 'codigo']);

        // Todos los asesores habilitados para el select del modal
        $asesores = DB::table('asesores')
            ->join('cargos', 'cargos.id', '=', 'asesores.cargo_id')
            ->where('asesores.estado', 'Habilitado')
            ->orderBy('cargos.nombre')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->select('asesores.id', 'asesores.nombres', 'asesores.apellidos', 'asesores.ci', 'cargos.nombre as cargo_nombre')
            ->get();

        // --- Gastos de autogestión del asesor por programa ---
        $filtrarMesAuto = $mesFiltro !== '';
        $autoPorProgramaAsesor = DB::table('marketing_gastos_autogestion as g')
            ->leftJoin('programas', 'g.programa_id', '=', 'programas.id')
            ->where('g.asesor_id', $id)
            ->where('g.gestion', $gestionFiltro)
            ->when($filtrarMesAuto, fn($q) => $q->where('g.mes', (int) $mesFiltro))
            ->select(
                DB::raw('COALESCE(g.programa_id, 0) as id'),
                'programas.nombre',
                'programas.codigo',
                DB::raw('COUNT(*) as total_gastos'),
                DB::raw('SUM(g.monto) as total_monto')
            )
            ->groupBy(DB::raw('COALESCE(g.programa_id, 0)'), 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total_monto')
            ->get();

        // --- Comprobantes de publicidad (asignado empresa) del asesor por programa ---
        $compPorProgramaAsesor = DB::table('marketing_comprobantes_publicidad')
            ->join('programas', 'marketing_comprobantes_publicidad.programa_id', '=', 'programas.id')
            ->where('marketing_comprobantes_publicidad.asesor_id', $id)
            ->where('marketing_comprobantes_publicidad.gestion', $gestionFiltro)
            ->when($filtrarMesAuto, fn($q) => $q->where('marketing_comprobantes_publicidad.mes', (int) $mesFiltro))
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                DB::raw('COUNT(*) as total_comprobantes'),
                DB::raw("SUM(CASE WHEN estado = 'aprobado_obs' THEN COALESCE(monto_aprobado, monto) ELSE monto END) as total_monto"),
                DB::raw("SUM(CASE WHEN estado = 'aprobado' THEN monto WHEN estado = 'aprobado_obs' THEN COALESCE(monto_aprobado, monto) ELSE 0 END) as total_aprobado")
            )
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo')
            ->orderByDesc('total_aprobado')
            ->get();

        // Merge comprobantes + autogestión for this asesor
        $compKeyedAsesor = $compPorProgramaAsesor->keyBy('id');
        $autoKeyedAsesor = $autoPorProgramaAsesor->keyBy('id');
        $allIdsAsesor = $compKeyedAsesor->keys()->merge($autoKeyedAsesor->keys())->unique()->sort();

        $programasResumenAsesor = $allIdsAsesor->map(function ($id) use ($compKeyedAsesor, $autoKeyedAsesor) {
            $comp = $compKeyedAsesor->get($id);
            $auto = $autoKeyedAsesor->get($id);
            $compMonto = $comp ? (float) $comp->total_monto : 0;
            $autoMonto = $auto ? (float) $auto->total_monto : 0;
            $nombre = $comp->nombre ?? $auto->nombre ?? 'Sin programa';
            $codigo = $comp->codigo ?? $auto->codigo ?? '';
            return (object) [
                'programa_label' => $nombre . ($codigo ? " ({$codigo})" : ''),
                'comp_monto' => $compMonto,
                'auto_monto' => $autoMonto,
                'total_invertido' => $compMonto + $autoMonto,
            ];
        })->sortByDesc('total_invertido')->values();

        $soloAutoAsesor = $programasResumenAsesor->filter(fn($p) => $p->auto_monto > 0)->values();

        $metasAsesor = [];
        if ($gestionFiltro !== '') {
            $metaRows = $this->metasCumplimiento($gestionFiltro, $tipo, $scopeTipo, $realesCond, $id);
            $metasAsesor = isset($metaRows[0]['meses']) ? $metaRows[0]['meses'] : [];
        }

        foreach (range(1, 12) as $m) {
            $otras = (float) ($puntosOtrasSedes[$m] ?? 0);
            if ($otras > 0) {
                $sede = (float) ($metasAsesor[$m]['real'] ?? 0);
                $metasAsesor[$m]['real_otras'] = $otras;
                $metasAsesor[$m]['real'] = $sede + $otras;
            }
        }

        $viewData = compact(
            'asesor', 'inscripciones', 'stats', 'porPrograma',
            'gestiones', 'gestionFiltro', 'mesFiltro', 'meses', 'chartMeses',
            'tipo', 'inscritosTipoSeries', 'puntosTipoSeries', 'puntosRealesTipoSeries',
            'comprobantesAsesor', 'presupuestosAsesor', 'asesores', 'programas',
            'programasResumenAsesor', 'soloAutoAsesor', 'metasAsesor', 'metaMensual',
            'inscripcionesOtraSede', 'statsOtraSede'
        );

        if ($request->ajax()) {
            $programasHtml = $porPrograma->count() > 0
                ? view('admin.marketing.asesores._programas', $viewData)->render()
                : '';
            $desgloseHtml = view('admin.marketing.asesores._desglose', $viewData)->render();
            $puntosChartHtml = view('admin.marketing.asesores._puntos_chart', $viewData)->render();
            return response()->json([
                'stats'           => $stats,
                'chartMeses'      => $chartMeses,
                'inscritosTipoSeries' => $inscritosTipoSeries,
                'puntosTipoSeries'    => $puntosTipoSeries,
                'puntosRealesTipoSeries' => $puntosRealesTipoSeries,
                'programasHtml'   => $programasHtml,
                'desgloseHtml'    => $desgloseHtml,
                'puntosChartHtml' => $puntosChartHtml,
            ]);
        }

        return view('admin.marketing.asesores.show', $viewData);
    }

    private function metasCumplimiento($gestion, $tipo, $scopeTipo, $realesCond, $asesorId)
    {
        $realesPorMesQ = DB::table('inscripciones')
            ->join('asesores as asr', 'asr.id', '=', 'inscripciones.asesor_id')
            ->join('cargos as cg', 'cg.id', '=', 'asr.cargo_id')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->where('asr.estado', 'Habilitado')
            ->whereNotNull('cg.id')
            ->when($scopeTipo)
            ->whereRaw("({$realesCond})")
            ->select(
                'asr.id as asesor_id',
                DB::raw('MONTH(inscripciones.fecha_inscripcion) as mes'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos')
            )
            ->groupBy('asr.id', DB::raw('MONTH(inscripciones.fecha_inscripcion)'))
            ->orderBy('asr.apellidos')
            ->orderBy('asr.nombres')
            ->get();

        $realPorMes = [];
        foreach ($realesPorMesQ as $f) {
            $realPorMes[$f->asesor_id][(int) $f->mes] = (float) $f->puntos;
        }

        $asesoresConCargo = DB::table('asesores as asr')
            ->join('cargos as cg', 'cg.id', '=', 'asr.cargo_id')
            ->where('asr.estado', 'Habilitado')
            ->orderBy('cg.id')
            ->orderBy('asr.apellidos')
            ->orderBy('asr.nombres')
            ->select(
                'asr.id', 'asr.nombres', 'asr.apellidos', 'asr.ci',
                'cg.id as cargo_id', 'cg.nombre as cargo_nombre', 'cg.metas_puntos'
            )
            ->get();

        $filas = [];
        foreach ($asesoresConCargo as $a) {
            if ($asesorId !== '' && $asesorId !== null && (int) $asesorId !== (int) $a->id) {
                continue;
            }
            $meta = (float) ($a->metas_puntos ?? 0);
            $meses = [];
            foreach (range(1, 12) as $m) {
                $real = (float) ($realPorMes[$a->id][$m] ?? 0);
                $meses[$m] = [
                    'real' => $real,
                    'meta' => $meta,
                    'estado' => $meta > 0 ? ($real >= $meta ? 'cumplido' : 'falto') : 'sin_meta',
                    'diff' => $meta > 0 ? $real - $meta : null,
                ];
            }
            $filas[] = [
                'id' => $a->id,
                'apellidos' => $a->apellidos,
                'nombres' => $a->nombres,
                'ci' => $a->ci,
                'cargo_nombre' => $a->cargo_nombre,
                'meta' => $meta,
                'meses' => $meses,
            ];
        }

        return $filas;
    }

    public function cargosAsesores(Request $request)
    {
        $cargos = DB::table('cargos')->orderBy('id')->get();

        $allAsesores = DB::table('asesores')
            ->leftJoin('cargos', 'asesores.cargo_id', '=', 'cargos.id')
            ->select('asesores.*', 'cargos.nombre as cargo_nombre')
            ->orderBy('cargos.id')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->get();

        $asesoresPorCargo = $allAsesores
            ->whereNotNull('cargo_id')
            ->groupBy(fn($a) => $a->cargo_id);

        $sinCargo = $allAsesores->whereNull('cargo_id');

        $habilitadosPorCargo = $asesoresPorCargo->map(fn($g) => $g->where('estado', 'Habilitado'));
        $deshabilitadosPorCargo = $asesoresPorCargo->map(fn($g) => $g->where('estado', '!=', 'Habilitado'));

        $sinCargoHab = $sinCargo->where('estado', 'Habilitado');
        $sinCargoDes = $sinCargo->where('estado', '!=', 'Habilitado');

        $resumen = [
            'total_asesores' => DB::table('asesores')->count(),
            'total_cargos' => $cargos->count(),
            'habilitados' => DB::table('asesores')->where('estado', 'Habilitado')->count(),
            'deshabilitados' => DB::table('asesores')->where('estado', '!=', 'Habilitado')->count(),
        ];

        return view('admin.marketing.asesores-lista.index', compact(
            'cargos', 'habilitadosPorCargo', 'deshabilitadosPorCargo', 'sinCargoHab', 'sinCargoDes', 'resumen'
        ));
    }

    public function asesorData($id)
    {
        $asesor = DB::table('asesores')->where('id', $id)->first();
        if (!$asesor) {
            return response()->json(['success' => false, 'message' => 'Asesor no encontrado'], 404);
        }
        return response()->json(['success' => true, 'asesor' => $asesor]);
    }

    public function updateAsesor(Request $request, $id)
    {
        $asesor = DB::table('asesores')->where('id', $id)->first();
        if (!$asesor) {
            return response()->json(['success' => false, 'message' => 'Asesor no encontrado'], 404);
        }

        $validated = $request->validate([
            'ci' => 'required|string|max:10|unique:asesores,ci,' . $id,
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'sexo' => 'required|string|max:100',
            'celular' => 'required|string|max:10',
            'correo' => 'required|email|max:255|unique:asesores,correo,' . $id,
            'cargo_id' => 'nullable|integer|exists:cargos,id',
            'estado' => 'required|string|max:255',
        ]);

        DB::table('asesores')->where('id', $id)->update([
            'ci' => $validated['ci'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'sexo' => $validated['sexo'],
            'celular' => $validated['celular'],
            'correo' => $validated['correo'],
            'cargo_id' => $validated['cargo_id'],
            'estado' => $validated['estado'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asesor actualizado correctamente',
        ]);
    }

    public function toggleAsesorEstado($id)
    {
        $asesor = DB::table('asesores')->where('id', $id)->first();
        if (!$asesor) {
            return response()->json(['success' => false, 'message' => 'Asesor no encontrado'], 404);
        }

        $nuevoEstado = $asesor->estado === 'Habilitado' ? 'Deshabilitado' : 'Habilitado';

        DB::table('asesores')->where('id', $id)->update(['estado' => $nuevoEstado]);

        return response()->json([
            'success' => true,
            'message' => "Asesor {$nuevoEstado} correctamente",
            'nuevo_estado' => $nuevoEstado,
        ]);
    }

    public function deleteAsesor($id)
    {
        $asesor = DB::table('asesores')->where('id', $id)->first();
        if (!$asesor) {
            return response()->json(['success' => false, 'message' => 'Asesor no encontrado'], 404);
        }

        $inscripciones = DB::table('inscripciones')->where('asesor_id', $id)->count();
        if ($inscripciones > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: existen {$inscripciones} inscripciones asociadas a este asesor.",
            ], 422);
        }

        DB::table('asesores')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asesor eliminado correctamente',
        ]);
    }

    public function storeCargo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre',
            'metas_puntos' => 'nullable|numeric|min:0|max:9999999.99',
        ]);

        $id = DB::table('cargos')->insertGetId([
            'nombre' => $validated['nombre'],
            'metas_puntos' => filled($validated['metas_puntos'] ?? null) ? (float) $validated['metas_puntos'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cargo creado correctamente',
            'id' => $id,
            'cargo' => DB::table('cargos')->where('id', $id)->first(),
        ]);
    }

    public function updateCargo(Request $request, $id)
    {
        $cargo = DB::table('cargos')->where('id', $id)->first();
        if (!$cargo) {
            return response()->json(['success' => false, 'message' => 'Cargo no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre,' . $id,
            'metas_puntos' => 'nullable|numeric|min:0|max:9999999.99',
        ]);

        DB::table('cargos')->where('id', $id)->update([
            'nombre' => $validated['nombre'],
            'metas_puntos' => filled($validated['metas_puntos'] ?? null) ? (float) $validated['metas_puntos'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cargo actualizado correctamente',
        ]);
    }

    public function deleteCargo($id)
    {
        $cargo = DB::table('cargos')->where('id', $id)->first();
        if (!$cargo) {
            return response()->json(['success' => false, 'message' => 'Cargo no encontrado'], 404);
        }

        $asesores = DB::table('asesores')->where('cargo_id', $id)->count();
        if ($asesores > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: existen {$asesores} asesores asignados a este cargo.",
            ], 422);
        }

        DB::table('cargos')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cargo eliminado correctamente',
        ]);
    }

    public function programaInscritosReales($id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado']);
        }

        $numModulos = DB::table('modulos')->where('programa_id', $id)->count();

        $inscripciones = DB::table('inscripciones as i')
            ->join('participantes as p', 'i.participante_id', '=', 'p.id')
            ->leftJoin('asesores as a', 'i.asesor_id', '=', 'a.id')
            ->where('i.programa_id', $id)
            ->select(
                'p.id as participante_id',
                'p.ci',
                'p.nombres',
                'p.apellidos',
                'i.contado',
                'i.matricula',
                'i.descuento_matricula',
                'i.monto_programa',
                'i.descuento_colegiatura',
                'a.id as asesor_id',
                'a.nombres as asesor_nombres',
                'a.apellidos as asesor_apellidos',
                'i.es_traspaso',
                DB::raw("(i.matricula - COALESCE(i.descuento_matricula, 0)) as mat_neto"),
                DB::raw("(i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) as col_neto")
            )
            ->orderBy('p.apellidos')
            ->orderBy('p.nombres')
            ->get();

        $participanteIds = $inscripciones->pluck('participante_id')->unique()->toArray();

        $pagos = DB::table('modulos_participantes as mp')
            ->join('deudas as d', 'd.modulo_participante_id', '=', 'mp.id')
            ->whereIn('mp.participante_id', $participanteIds)
            ->select('mp.participante_id', 'mp.tipo', DB::raw('SUM(d.monto_bs) as total_pagado'))
            ->groupBy('mp.participante_id', 'mp.tipo')
            ->get()
            ->groupBy('participante_id');

        $valoresCol = DB::table('modulos_participantes as mp')
            ->join('deudas as d', 'd.modulo_participante_id', '=', 'mp.id')
            ->whereIn('mp.participante_id', $participanteIds)
            ->where('mp.tipo', 'Colegiatura')
            ->where('d.monto_bs', '>', 0)
            ->select('mp.participante_id', DB::raw('MIN(d.monto_bs) as primer_monto'))
            ->groupBy('mp.participante_id')
            ->get()
            ->keyBy('participante_id');

        $reales = [];
        $noReales = [];

        foreach ($inscripciones as $ins) {
            $matNeto = (float)$ins->mat_neto;
            $colNeto = (float)$ins->col_neto;

            $pagosPart = $pagos->get($ins->participante_id, collect());
            $pagadoMat = (float) ($pagosPart->firstWhere('tipo', 'Matricula')?->total_pagado ?? 0);
            $pagadoCol = (float) ($pagosPart->firstWhere('tipo', 'Colegiatura')?->total_pagado ?? 0);

            $matOk = $matNeto == 0 || $pagadoMat >= $matNeto;
            $esContado = (int)$ins->contado === 1;

            if ($esContado) {
                $cuotaMinCol = $numModulos > 0 ? $colNeto / $numModulos : $colNeto;
                $esReal = $pagadoCol >= $cuotaMinCol && $colNeto > 0;
            } else {
                $colegiaturasPagadas = DB::table('modulos_participantes as mp')
                    ->join('deudas as d', 'd.modulo_participante_id', '=', 'mp.id')
                    ->where('mp.participante_id', $ins->participante_id)
                    ->where('mp.tipo', 'Colegiatura')
                    ->where('d.monto_bs', '>', 0)
                    ->distinct('mp.modulo_id')
                    ->count('mp.modulo_id');
                $esReal = $matOk && $colegiaturasPagadas >= 1;
            }

            $faltaMat = max(0, $matNeto - $pagadoMat);
            $faltaCol = 0;

            if ($esContado) {
                $cuotaMinCol = $numModulos > 0 ? $colNeto / $numModulos : $colNeto;
                $faltaCol = max(0, $cuotaMinCol - $pagadoCol);
            } else {
                $faltaCol = $pagadoCol > 0 ? 0 : (float)($valoresCol->get($ins->participante_id)?->primer_monto ?? 0);
            }

            $item = [
                'participante_id' => $ins->participante_id,
                'ci' => $ins->ci,
                'nombres' => $ins->nombres,
                'apellidos' => $ins->apellidos,
                'contado' => (int)$ins->contado,
                'mat_neto' => $matNeto,
                'mat_pagado' => $pagadoMat,
                'col_neto' => $colNeto,
                'col_pagado' => $pagadoCol,
                'es_real' => $esReal ? 1 : 0,
                'es_traspaso' => (int)($ins->es_traspaso ?? 0),
                'asesor_nombres' => $ins->asesor_nombres,
                'asesor_apellidos' => $ins->asesor_apellidos,
                'falta_mat' => $faltaMat,
                'falta_col' => $faltaCol,
            ];

            if ($esReal) {
                $reales[] = $item;
            } else {
                $noReales[] = $item;
            }
        }

        $totalReales = count($reales);
        $totalNoReales = count($noReales);
        $total = $totalReales + $totalNoReales;
        $porcentajeReales = $total > 0 ? round(($totalReales / $total) * 100) : 0;
        $traspasosReales = collect($reales)->where('es_traspaso', 1)->count();
        $traspasosNoReales = collect($noReales)->where('es_traspaso', 1)->count();

        $html = view('admin.marketing.programas._inscritos_reales_table', [
            'reales' => $reales,
            'noReales' => $noReales,
            'totalReales' => $totalReales,
            'totalNoReales' => $totalNoReales,
            'total' => $total,
            'porcentajeReales' => $porcentajeReales,
            'numModulos' => $numModulos,
            'programa' => $programa,
        ])->render();

        return response()->json([
            'success' => true,
            'total_reales' => $totalReales,
            'total_no_reales' => $totalNoReales,
            'traspasos_reales' => $traspasosReales,
            'traspasos_no_reales' => $traspasosNoReales,
            'html' => $html,
        ]);
    }
}
