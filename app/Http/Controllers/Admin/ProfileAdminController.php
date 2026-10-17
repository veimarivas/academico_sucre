<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GastoAutogestion;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $gestion = $request->get('gestion', now()->format('Y'));
        $tipo = $request->get('tipo', 'general');

        // Filtrar por asesor si el usuario tiene asesor_id asignado
        $asesorId = $user->asesor_id ?? null;

        $scopeTipo = fn($q) => match ($tipo) {
            'upi' => $q->where('programas.tipo_id', 6),
            'upi-auto' => $q->where('programas.tipo_id', 7),
            'otros' => $q->whereNotIn('programas.tipo_id', [6, 7]),
            default => $q,
        };

        $gestionesQuery = DB::table('inscripciones')
            ->whereNotNull('fecha_inscripcion');

        if ($asesorId) {
            $gestionesQuery->where('inscripciones.asesor_id', $asesorId);
        }

        $gestiones = $gestionesQuery
            ->select(DB::raw('YEAR(fecha_inscripcion) as gestion'))
            ->distinct()
            ->orderByDesc('gestion')
            ->pluck('gestion');

        if ($gestiones->isEmpty()) {
            $gestiones = collect([(int) now()->format('Y')]);
        }

        // Ofertas académicas en estado "Inscripciones" (estado_id = 4)

        $ofertas = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('areas_programas', 'programas.id', '=', 'areas_programas.programa_id')
            ->leftJoin('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->leftJoin('sedes', 'areas_programas.sede_id', '=', 'sedes.id')
            ->where('programas.estado_id', 4)
            ->select(
                'programas.*',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre',
                'estados.id as estado_id',
                'areas.nombre as area_nombre',
                'areas.color as area_color',
                'areas.id as area_id',
                'sedes.nombre as sede_nombre'
            )
            ->selectRaw("(
                SELECT GROUP_CONCAT(
                    CONCAT(r.ci, '||', r.apellidos, '||', r.nombres, '||', COALESCE(r.color, '#6c757d'))
                    ORDER BY r.apellidos, r.nombres
                    SEPARATOR ';;'
                )
                FROM programas_responsables pr
                JOIN responsables r ON pr.responsable_id = r.id
                WHERE pr.programa_id = programas.id
            ) as responsables_raw")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id) as inscritos")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND es_traspaso = 1) as traspasos")
            ->selectRaw("(
                SELECT COUNT(*)
                FROM inscripciones i
                WHERE i.programa_id = programas.id
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
                     )
                    )
                    OR
                    (i.contado = 1
                     AND (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) > 0
                     AND COALESCE((
                         SELECT SUM(d3.monto_bs)
                         FROM modulos_participantes mp3
                         JOIN deudas d3 ON d3.modulo_participante_id = mp3.id
                         WHERE mp3.participante_id = i.participante_id AND mp3.tipo = 'Colegiatura'
                     ), 0) >= (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) / (SELECT GREATEST(COUNT(*), 1) FROM modulos WHERE modulos.programa_id = programas.id)
                    )
                  )
            ) as inscritos_reales")
            ->selectRaw("(SELECT COUNT(*) FROM modulos WHERE modulos.programa_id = programas.id) as total_modulos")
            ->selectRaw("(SELECT GROUP_CONCAT(m.nombre ORDER BY m.id SEPARATOR '||') FROM modulos m WHERE m.programa_id = programas.id) as modulos_nombres")
            ->orderBy('programas.gestion', 'desc')
            ->orderBy('programas.nombre', 'asc')
            ->get();

        $filtrarPorAsesor = $asesorId !== null;

        // --- Inscritos por mes, desglosados por tipo de programa ---
        $inscritosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $inscritosPorTipoMesQ->where('inscripciones.asesor_id', $asesorId);
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
        foreach ($tipoInscritosLabels as $i => $tipoLabel) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $inscritosPorMesTipo[$m][$tipoLabel] ?? 0;
            }
            $inscritosTipoSeries[] = [
                'name' => $tipoLabel,
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
            $puntosPorTipoMesQ->where('inscripciones.asesor_id', $asesorId);
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
        foreach ($tipoLabels as $i => $tipoLabel) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosPorMesTipo[$m][$tipoLabel] ?? 0;
            }
            $puntosTipoSeries[] = [
                'name' => $tipoLabel,
                'data' => $serie,
                'color' => $puntosTipoColores[$i % count($puntosTipoColores)],
            ];
        }

        // --- Real condition (same as MarketingController) ---
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

        // --- Puntos Reales por mes ---
        $puntosRealesPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $puntosRealesPorTipoMesQ->where('inscripciones.asesor_id', $asesorId);
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
        foreach ($realesTipoLabels as $i => $tipoLabel) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $puntosRealesPorMesTipo[$m][$tipoLabel] ?? 0;
            }
            $puntosRealesTipoSeries[] = [
                'name' => $tipoLabel,
                'data' => $serie,
                'color' => $puntosTipoColores[$i % count($puntosTipoColores)],
            ];
        }

        // --- Reales por mes desglosados por tipo ---
        $realesInscritosPorTipoMesQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $realesInscritosPorTipoMesQ->where('inscripciones.asesor_id', $asesorId);
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
        foreach ($tipoInscritosLabels as $i => $tipoLabel) {
            $serie = [];
            foreach (range(1, 12) as $m) {
                $serie[] = $realesInscritosPorMesTipo[$m][$tipoLabel] ?? 0;
            }
            $realesInscritosTipoSeries[] = [
                'name' => $tipoLabel,
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

        // --- Inscripciones por mes (gestión) ---
        $porMesQuery = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $porMesQuery->where('inscripciones.asesor_id', $asesorId);
        }

        $porMes = $porMesQuery
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

        // --- Deudas pre-agregadas por participante ---
        $deudasAgg = DB::table('modulos_participantes as mp')
            ->join('deudas as d', 'd.modulo_participante_id', '=', 'mp.id')
            ->select('mp.participante_id',
                DB::raw("COALESCE(SUM(CASE WHEN mp.tipo = 'Matricula' THEN d.monto_bs ELSE 0 END), 0) as matricula_pagado"),
                DB::raw("COALESCE(SUM(CASE WHEN mp.tipo = 'Colegiatura' THEN d.monto_bs ELSE 0 END), 0) as colegiatura_pagado"),
                DB::raw("MIN(CASE WHEN mp.tipo = 'Colegiatura' AND d.monto_bs > 0 THEN d.monto_bs END) as colegiatura_primer_monto")
            )
            ->groupBy('mp.participante_id');

        // --- Módulos por programa ---
        $modulosCount = DB::table('modulos')
            ->select('programa_id', DB::raw('GREATEST(COUNT(*), 1) as num_modulos'))
            ->groupBy('programa_id');

        // Monthly reales (usando LEFT JOIN a deudas agregadas en vez de subconsultas)
        $realesQ = DB::table('inscripciones as i')
            ->join('programas as p', 'i.programa_id', '=', 'p.id')
            ->leftJoin('tipos as t', 'p.tipo_id', '=', 't.id')
            ->leftJoinSub($deudasAgg, 'deudas_agg_r', fn($j) => $j->on('i.participante_id', '=', 'deudas_agg_r.participante_id'))
            ->leftJoinSub($modulosCount, 'modulos_count_r', fn($j) => $j->on('i.programa_id', '=', 'modulos_count_r.programa_id'))
            ->whereYear('i.fecha_inscripcion', $gestion);

        if ($filtrarPorAsesor) {
            $realesQ->where('i.asesor_id', $asesorId);
        }
        if ($tipo === 'upi') {
            $realesQ->where('p.tipo_id', 6);
        } elseif ($tipo === 'upi-auto') {
            $realesQ->where('p.tipo_id', 7);
        } elseif ($tipo === 'otros') {
            $realesQ->whereNotIn('p.tipo_id', [6, 7]);
        }

        $realesQ->where(function ($q) {
            $q->where(function ($q2) {
                $q2->where('i.contado', 0)
                    ->whereRaw('COALESCE(deudas_agg_r.matricula_pagado, 0) >= (i.matricula - COALESCE(i.descuento_matricula, 0))')
                    ->whereRaw('COALESCE(deudas_agg_r.colegiatura_primer_monto, 0) > 0');
            })->orWhere(function ($q2) {
                $q2->where('i.contado', 1)
                    ->whereRaw('(i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) > 0')
                    ->whereRaw('COALESCE(deudas_agg_r.colegiatura_pagado, 0) >= (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) / COALESCE(modulos_count_r.num_modulos, 1)');
            });
        });

        $realesPorMes = $realesQ
            ->select(DB::raw('MONTH(i.fecha_inscripcion) as mes'),
                DB::raw('COUNT(*) as reales'),
                DB::raw('COALESCE(SUM(t.puntos), 0) as puntos_reales')
            )
            ->groupBy(DB::raw('MONTH(i.fecha_inscripcion)'))
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $mesesData = ['total' => [], 'regulares' => [], 'contado' => [], 'puntos' => [], 'reales' => [], 'puntos_reales' => []];
        foreach (range(1, 12) as $m) {
            $r = $porMes->get($m);
            $rr = $realesPorMes->get($m);
            $mesesData['total'][]     = $r ? (int) $r->total : 0;
            $mesesData['regulares'][] = $r ? (int) $r->regulares : 0;
            $mesesData['contado'][]   = $r ? (int) $r->contado : 0;
            $mesesData['puntos'][]    = $r ? (float) $r->puntos : 0;
            $mesesData['reales'][]    = $rr ? (int) $rr->reales : 0;
            $mesesData['puntos_reales'][] = $rr ? (float) $rr->puntos_reales : 0;
        }

        // --- Inscritos por programa en la gestión seleccionada ---
        $porProgramaQuery = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $porProgramaQuery->where('inscripciones.asesor_id', $asesorId);
        }

        $porPrograma = $porProgramaQuery
            ->select(
                'programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion',
                'tipos.nombre as tipo_nombre',
                DB::raw('COUNT(inscripciones.id) as total'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 1 AND (inscripciones.baja_operativa = 0 OR inscripciones.baja_operativa IS NULL) AND (inscripciones.baja_academica = 0 OR inscripciones.baja_academica IS NULL) THEN 1 ELSE 0 END) as activos'),
                DB::raw('SUM(CASE WHEN inscripciones.estado = 0 OR inscripciones.baja_operativa = 1 OR inscripciones.baja_academica = 1 THEN 1 ELSE 0 END) as retirados'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN inscripciones.contado = 0 THEN 1 ELSE 0 END) as regular'),
                DB::raw('COALESCE(SUM(tipos.puntos), 0) as puntos'),
            )
            ->groupBy('programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion', 'tipos.nombre')
            ->orderByDesc('total')
            ->get();

        $programaIds = $porPrograma->pluck('id');

        // --- Reales por programa (cálculo separado en vez de subconsultas correlacionadas) ---
        if ($programaIds->isNotEmpty()) {
            $realesData = DB::table('inscripciones as i')
                ->join('programas as p', 'i.programa_id', '=', 'p.id')
                ->leftJoin('tipos as t', 'p.tipo_id', '=', 't.id')
                ->leftJoinSub($deudasAgg, 'deudas_agg2', fn($j) => $j->on('i.participante_id', '=', 'deudas_agg2.participante_id'))
                ->leftJoinSub($modulosCount, 'modulos_count2', fn($j) => $j->on('i.programa_id', '=', 'modulos_count2.programa_id'))
                ->whereYear('i.fecha_inscripcion', $gestion)
                ->whereIn('i.programa_id', $programaIds);

            if ($filtrarPorAsesor) {
                $realesData->where('i.asesor_id', $asesorId);
            }
            if ($tipo === 'upi') {
                $realesData->where('p.tipo_id', 6);
            } elseif ($tipo === 'upi-auto') {
                $realesData->where('p.tipo_id', 7);
            } elseif ($tipo === 'otros') {
                $realesData->whereNotIn('p.tipo_id', [6, 7]);
            }

            $realesData->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('i.contado', 0)
                        ->whereRaw('COALESCE(deudas_agg2.matricula_pagado, 0) >= (i.matricula - COALESCE(i.descuento_matricula, 0))')
                        ->whereRaw('COALESCE(deudas_agg2.colegiatura_primer_monto, 0) > 0');
                })->orWhere(function ($q2) {
                    $q2->where('i.contado', 1)
                        ->whereRaw('(i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) > 0')
                        ->whereRaw('COALESCE(deudas_agg2.colegiatura_pagado, 0) >= (i.monto_programa - COALESCE(i.descuento_colegiatura, 0)) / COALESCE(modulos_count2.num_modulos, 1)');
                });
            });

            $realesData = $realesData
                ->select('i.programa_id',
                    DB::raw('COUNT(*) as inscritos_reales'),
                    DB::raw('COALESCE(SUM(t.puntos), 0) as puntos_reales')
                )
                ->groupBy('i.programa_id')
                ->get()
                ->keyBy('programa_id');
        } else {
            $realesData = collect();
        }

        $porPrograma = $porPrograma->map(function ($p) use ($realesData) {
            $r = $realesData->get($p->id);
            $p->inscritos_reales = $r ? (int) $r->inscritos_reales : 0;
            $p->puntos_reales = $r ? (float) $r->puntos_reales : 0;
            return $p;
        });

        // --- Inscritos detallados por programa ---
        $inscritosDetalleQ = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->leftJoinSub($deudasAgg, 'deudas_agg', function ($join) {
                $join->on('inscripciones.participante_id', '=', 'deudas_agg.participante_id');
            })
            ->leftJoinSub($modulosCount, 'modulos_count', function ($join) {
                $join->on('inscripciones.programa_id', '=', 'modulos_count.programa_id');
            })
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->whereIn('inscripciones.programa_id', $programaIds)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $inscritosDetalleQ->where('inscripciones.asesor_id', $asesorId);
        }

        $inscritosPorPrograma = $inscritosDetalleQ
            ->select(
                'inscripciones.programa_id',
                'inscripciones.contado',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica',
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
                DB::raw("(inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0)) as matricula_requerido"),
                'deudas_agg.matricula_pagado',
                'deudas_agg.colegiatura_pagado',
                'deudas_agg.colegiatura_primer_monto',
                'modulos_count.num_modulos',
                DB::raw("CASE
                    WHEN (inscripciones.contado = 0
                        AND COALESCE(deudas_agg.matricula_pagado, 0) >= (inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0))
                        AND COALESCE(deudas_agg.colegiatura_primer_monto, 0) > 0)
                    OR (inscripciones.contado = 1
                        AND (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) > 0
                        AND COALESCE(deudas_agg.colegiatura_pagado, 0) >= (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) / COALESCE(modulos_count.num_modulos, 1))
                    THEN 1 ELSE 0 END as es_real")
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->groupBy('programa_id');

        $totalReales = (int) $porPrograma->sum('inscritos_reales');
        $resumen = [
            'total_ofertas' => $ofertas->count(),
            'total_inscritos' => (int) $porPrograma->sum('total'),
            'total_programas' => $porPrograma->count(),
            'total_activos'  => (int) $porPrograma->sum('activos'),
            'total_puntos'   => (float) $porPrograma->sum('puntos'),
            'total_reales'   => $totalReales,
            'total_no_reales' => (int) $porPrograma->sum('total') - $totalReales,
            'total_puntos_reales' => (float) $porPrograma->sum('puntos_reales'),
        ];

        // Nombre del asesor si está filtrado
        $asesorNombre = null;
        if ($asesorId) {
            $asesor = DB::table('asesores')->where('id', $asesorId)->first();
            $asesorNombre = $asesor ? trim($asesor->nombres . ' ' . $asesor->apellidos) : null;
        }

        // Responsable vinculado al usuario
        $responsable = null;
        if (!empty($user->responsable_id)) {
            $responsable = DB::table('responsables')->where('id', $user->responsable_id)->first();
        }

        return view('admin.profile.index', compact(
            'user', 'ofertas', 'porPrograma', 'inscritosPorPrograma',
            'resumen', 'gestion', 'gestiones', 'mesesData', 'asesorId', 'asesorNombre',
            'responsable', 'tipo',
            'puntosTipoSeries', 'inscritosTipoSeries', 'puntosRealesTipoSeries',
            'realesInscritosTipoSeries', 'noRealesInscritosTipoSeries'
        ));
    }

    /**
     * Inscritos de un programa (para cargar el modal del perfil por AJAX).
     */
    /** Sólo marketing (y Super Admin) accede a ofertas/inscripciones/publicidad. */
    private function authorizeMarketing(): void
    {
        abort_unless((bool) Auth::user()?->canAccessMarketing(), 403);
    }

    public function inscritosPrograma(Request $request, $programaId)
    {
        $this->authorizeMarketing();

        $user = Auth::user();
        $gestion = $request->get('gestion', now()->format('Y'));
        $tipo = $request->get('tipo', 'general');
        $asesorId = $user->asesor_id ?? null;
        $filtrarPorAsesor = $asesorId !== null;

        $scopeTipo = fn($q) => match ($tipo) {
            'upi' => $q->where('programas.tipo_id', 6),
            'upi-auto' => $q->where('programas.tipo_id', 7),
            'otros' => $q->whereNotIn('programas.tipo_id', [6, 7]),
            default => $q,
        };

        $deudasAgg = DB::table('modulos_participantes as mp')
            ->join('deudas as d', 'd.modulo_participante_id', '=', 'mp.id')
            ->select('mp.participante_id',
                DB::raw("COALESCE(SUM(CASE WHEN mp.tipo = 'Matricula' THEN d.monto_bs ELSE 0 END), 0) as matricula_pagado"),
                DB::raw("COALESCE(SUM(CASE WHEN mp.tipo = 'Colegiatura' THEN d.monto_bs ELSE 0 END), 0) as colegiatura_pagado"),
                DB::raw("MIN(CASE WHEN mp.tipo = 'Colegiatura' AND d.monto_bs > 0 THEN d.monto_bs END) as colegiatura_primer_monto")
            )
            ->groupBy('mp.participante_id');

        $modulosCount = DB::table('modulos')
            ->select('programa_id', DB::raw('GREATEST(COUNT(*), 1) as num_modulos'))
            ->groupBy('programa_id');

        $q = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->leftJoinSub($deudasAgg, 'deudas_agg', fn($j) => $j->on('inscripciones.participante_id', '=', 'deudas_agg.participante_id'))
            ->leftJoinSub($modulosCount, 'modulos_count', fn($j) => $j->on('inscripciones.programa_id', '=', 'modulos_count.programa_id'))
            ->where('inscripciones.programa_id', $programaId)
            ->whereYear('inscripciones.fecha_inscripcion', $gestion)
            ->when($scopeTipo);

        if ($filtrarPorAsesor) {
            $q->where('inscripciones.asesor_id', $asesorId);
        }

        $listaInsc = $q->select(
                'inscripciones.programa_id',
                'inscripciones.contado',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica',
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
                DB::raw('(inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0)) as matricula_requerido'),
                'deudas_agg.matricula_pagado',
                'deudas_agg.colegiatura_pagado',
                'deudas_agg.colegiatura_primer_monto',
                'modulos_count.num_modulos',
                DB::raw("CASE
                    WHEN (inscripciones.contado = 0
                        AND COALESCE(deudas_agg.matricula_pagado, 0) >= (inscripciones.matricula - COALESCE(inscripciones.descuento_matricula, 0))
                        AND COALESCE(deudas_agg.colegiatura_primer_monto, 0) > 0)
                    OR (inscripciones.contado = 1
                        AND (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) > 0
                        AND COALESCE(deudas_agg.colegiatura_pagado, 0) >= (inscripciones.monto_programa - COALESCE(inscripciones.descuento_colegiatura, 0)) / COALESCE(modulos_count.num_modulos, 1))
                    THEN 1 ELSE 0 END as es_real")
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.profile._inscritos_rows', compact('listaInsc'))->render(),
        ]);
    }

    public function publicidadData(Request $request)
    {
        $this->authorizeMarketing();

        $user = Auth::user();
        $asesorId = $user->asesor_id;

        if (!$asesorId) {
            return response()->json(['success' => false, 'html' => '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i><p>No tienes un perfil de asesor vinculado.</p></div>']);
        }

        $asesor = DB::table('asesores')->where('id', $asesorId)->first();
        if (!$asesor || $asesor->estado !== 'Habilitado') {
            return response()->json(['success' => false, 'html' => '<div class="text-center text-muted py-5"><i class="ri-close-circle-line ri-2x d-block mb-2"></i><p>Tu perfil de asesor no está habilitado.</p></div>']);
        }

        $cargo = DB::table('cargos')->where('id', $asesor->cargo_id)->first();
        $esPrincipal = in_array($asesor->cargo_id, [1, 2]);
        $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

        // Presupuestos donde este asesor es el asignado (como responsable del gasto)
        $presupuestos = DB::table('marketing_presupuestos_publicidad as p')
            ->leftJoin('asesores as s', 's.id', '=', 'p.supervisor_id')
            ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->where('p.asesor_id', $asesorId)
            ->where('p.estado', 'activo')
            ->select(
                'p.*',
                's.nombres as sup_nombres', 's.apellidos as sup_apellidos',
                'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos'
            )
            ->selectRaw('(SELECT COUNT(*) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_comprobantes')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE c.monto END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado" THEN c.monto WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado')
            ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id AND c.estado = "pendiente") as total_pendiente')
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.mes', 'desc')
            ->orderBy('p.quincena', 'desc')
            ->get();

        // Presupuestos derivados desde los presupuestos de este asesor
        $misPresupuestoIds = $presupuestos->pluck('id');
        $presupuestosDerivados = collect();
        if ($misPresupuestoIds->isNotEmpty()) {
            $presupuestosDerivados = DB::table('marketing_presupuestos_publicidad as p')
                ->leftJoin('asesores as s', 's.id', '=', 'p.supervisor_id')
                ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
                ->whereIn('p.presupuesto_origen_id', $misPresupuestoIds)
                ->where('p.estado', 'activo')
                ->select(
                    'p.*',
                    's.nombres as sup_nombres', 's.apellidos as sup_apellidos',
                    'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos'
                )
                ->selectRaw('(SELECT COUNT(*) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_comprobantes')
                ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE c.monto END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
                ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado" THEN c.monto WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado')
                ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id AND c.estado = "pendiente") as total_pendiente')
                ->orderBy('p.gestion', 'desc')
                ->orderBy('p.mes', 'desc')
                ->orderBy('p.quincena', 'desc')
                ->get();
        }

        // Todos los IDs de presupuestos a considerar (propios + derivados)
        $todosPresupuestoIds = $misPresupuestoIds
            ->merge($presupuestosDerivados->pluck('id'))
            ->unique();

        // Comprobantes de todos los presupuestos (propios y derivados)
        $comprobantes = collect();
        if ($todosPresupuestoIds->isNotEmpty()) {
            $comprobantes = DB::table('marketing_comprobantes_publicidad as c')
                ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
                ->leftJoin('users as v', 'v.id', '=', 'c.verificado_por')
                ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
                ->whereIn('c.presupuesto_id', $todosPresupuestoIds)
                ->select(
                    'c.*',
                    'a.nombres as asesor_nombres', 'a.apellidos as asesor_apellidos',
                    'v.fullname as verificador_nombre',
                    'prg.nombre as programa_nombre'
                )
                ->orderBy('c.created_at', 'desc')
                ->limit(100)
                ->get();
        }

        // Presupuestos activos para upload modal (todos donde participa el asesor)
        $todosPresupuestos = DB::table('marketing_presupuestos_publicidad as p')
            ->leftJoin('asesores as s', 's.id', '=', 'p.supervisor_id')
            ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->where('p.estado', 'activo')
            ->where(function ($q) use ($asesorId) {
                $q->where('p.asesor_id', $asesorId)->orWhere('p.supervisor_id', $asesorId);
            })
            ->select(
                'p.*',
                's.nombres as sup_nombres', 's.apellidos as sup_apellidos',
                'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos'
            )
            ->selectRaw('(SELECT COUNT(*) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_comprobantes')
            ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.mes', 'desc')
            ->orderBy('p.quincena', 'desc')
            ->get();

        $presupuestosAsesor = $todosPresupuestos;

        // Asesores derivados (cargo 3,4,5)
        $asesoresDerivados = DB::table('asesores')
            ->join('cargos', 'cargos.id', '=', 'asesores.cargo_id')
            ->where('asesores.estado', 'Habilitado')
            ->whereIn('asesores.cargo_id', [3, 4, 5])
            ->orderBy('cargos.nombre')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->select('asesores.id', 'asesores.nombres', 'asesores.apellidos', 'asesores.ci', 'cargos.nombre as cargo_nombre')
            ->get();

        // Programas en inscripciones
        $programas = DB::table('programas')->where('estado_id', 4)->orderBy('nombre')->get(['id', 'nombre', 'codigo']);

        // Presupuestos origen disponibles para derivar (solo si es principal y es titular)
        $presupuestosOrigen = collect();
        if ($esPrincipal) {
            $presupuestosOrigen = DB::table('marketing_presupuestos_publicidad as p')
                ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
                ->where('p.asesor_id', $asesorId)
                ->where('p.estado', 'activo')
                ->whereNull('p.presupuesto_origen_id')
                ->select('p.*', 'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos')
                ->selectRaw('(SELECT COALESCE(SUM(monto_asignado),0) FROM marketing_presupuestos_publicidad d WHERE d.presupuesto_origen_id = p.id) as total_derivado')
                ->orderBy('p.gestion', 'desc')
                ->orderBy('p.mes', 'desc')
                ->orderBy('p.quincena', 'desc')
                ->get();
        }

        // Detectar presupuesto del periodo actual (mes-gestión-quincena) para subir comprobante
        $gestionActual = now()->format('Y');
        $mesActual = now()->month;
        $quincenaActual = now()->day <= 15 ? 1 : 2;

        $presupuestoActual = DB::table('marketing_presupuestos_publicidad')
            ->where('asesor_id', $asesorId)
            ->where('estado', 'activo')
            ->where('gestion', $gestionActual)
            ->where('mes', $mesActual)
            ->where('quincena', $quincenaActual)
            ->first();

        // Presupuesto origen del periodo actual para derivar
        $presupuestoOrigenActual = null;
        if ($esPrincipal) {
            $presupuestoOrigenActual = DB::table('marketing_presupuestos_publicidad')
                ->where('asesor_id', $asesorId)
                ->where('estado', 'activo')
                ->where('periodo_cerrado', false)
                ->whereNull('presupuesto_origen_id')
                ->where('gestion', $gestionActual)
                ->where('mes', $mesActual)
                ->where('quincena', $quincenaActual)
                ->first();
        }

        // Gastos de autogestión del asesor
        $gastosAutogestion = GastoAutogestion::where('asesor_id', $asesorId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $totalAutogestion = $gastosAutogestion->sum('monto');

        // --- Resumen por periodo (para vista tipo presupuestos) ---
        $periodosData = collect();
        if ($todosPresupuestoIds->isNotEmpty()) {
            $raw = DB::table('marketing_presupuestos_publicidad as p')
                ->whereIn('p.id', $todosPresupuestoIds)
                ->leftJoin(
                    DB::raw('(SELECT presupuesto_id,
                        COALESCE(SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE monto END),0) as gastado,
                        COALESCE(SUM(CASE WHEN estado = "aprobado" THEN monto WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END),0) as aprobado,
                        COALESCE(SUM(CASE WHEN estado = "pendiente" THEN monto ELSE 0 END),0) as pendiente,
                        COUNT(*) as comprobantes
                        FROM marketing_comprobantes_publicidad GROUP BY presupuesto_id) as c'),
                    'c.presupuesto_id', '=', 'p.id'
                )
                ->select(
                    'p.gestion', 'p.mes', 'p.quincena',
                    DB::raw('COUNT(DISTINCT p.id) as total_presupuestos'),
                    DB::raw('COALESCE(SUM(p.monto_asignado),0) as total_asignado'),
                    DB::raw('COALESCE(SUM(c.gastado),0) as total_gastado'),
                    DB::raw('COALESCE(SUM(c.aprobado),0) as total_aprobado'),
                    DB::raw('COALESCE(SUM(c.pendiente),0) as total_pendiente'),
                    DB::raw('COALESCE(SUM(c.comprobantes),0) as total_comprobantes')
                )
                ->groupBy('p.gestion', 'p.mes', 'p.quincena')
                ->orderBy('p.gestion', 'desc')
                ->orderBy('p.mes', 'desc')
                ->orderBy('p.quincena', 'desc')
                ->get();

            $periodoStatus = DB::table('marketing_presupuestos_publicidad')
                ->whereIn('id', $todosPresupuestoIds)
                ->select('gestion', 'mes', 'quincena', DB::raw('MAX(periodo_cerrado) as cerrado'))
                ->groupBy('gestion', 'mes', 'quincena')
                ->get()
                ->keyBy(fn($s) => $s->gestion . '-' . $s->mes . '-' . $s->quincena);

            foreach ($raw as $r) {
                $pk = $r->gestion . '-' . $r->mes . '-' . $r->quincena;
                $status = $periodoStatus->get($pk);
                $periodosData->push((object) [
                    'gestion' => $r->gestion,
                    'mes' => $r->mes,
                    'quincena' => $r->quincena,
                    'total_asignado' => (float) $r->total_asignado,
                    'total_gastado' => (float) $r->total_gastado,
                    'total_aprobado' => (float) $r->total_aprobado,
                    'total_pendiente' => (float) $r->total_pendiente,
                    'total_comprobantes' => (int) $r->total_comprobantes,
                    'total_presupuestos' => (int) $r->total_presupuestos,
                    'periodo_cerrado' => $status ? (bool) $status->cerrado : false,
                ]);
            }
        }

        // --- Lista completa de comprobantes (para la tabla de "Comprobantes") ---
        $todosComprobantes = collect();
        if ($todosPresupuestoIds->isNotEmpty()) {
            $todosComprobantes = DB::table('marketing_comprobantes_publicidad as c')
                ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
                ->leftJoin('users as v', 'v.id', '=', 'c.verificado_por')
                ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
                ->leftJoin('marketing_presupuestos_publicidad as p', 'p.id', '=', 'c.presupuesto_id')
                ->whereIn('c.presupuesto_id', $todosPresupuestoIds)
                ->select(
                    'c.*',
                    'a.nombres as asesor_nombres', 'a.apellidos as asesor_apellidos',
                    'v.fullname as verificador_nombre',
                    'prg.nombre as programa_nombre',
                    'p.gestion as pres_gestion',
                    'p.mes as pres_mes',
                    'p.quincena as pres_quincena',
                    'p.monto_asignado as pres_monto'
                )
                ->orderBy('c.created_at', 'desc')
                ->get();
        }

        // Agrupar comprobantes por presupuesto_id para la vista detallada
        $comprobantesPorPresupuesto = $todosComprobantes->groupBy('presupuesto_id');

        $html = view('admin.profile._tab_publicidad', compact(
            'asesor', 'cargo', 'esPrincipal', 'meses',
            'presupuestos', 'presupuestosDerivados', 'todosPresupuestos', 'comprobantes',
            'presupuestosAsesor', 'asesoresDerivados', 'programas',
            'presupuestosOrigen', 'presupuestoActual', 'presupuestoOrigenActual',
            'gestionActual', 'mesActual', 'quincenaActual',
            'gastosAutogestion', 'totalAutogestion',
            'periodosData', 'todosComprobantes', 'comprobantesPorPresupuesto'
        ))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function storeGastoAutogestion(Request $request)
    {
        $this->authorizeMarketing();

        $user = Auth::user();
        $asesorId = $user->asesor_id;

        if (!$asesorId) {
            return back()->with('publicidad_error', 'No tienes un perfil de asesor vinculado.');
        }

        // Fallos de subida que PHP reporta en $_FILES y que Laravel NO convierte en error
        // de validación (superar upload_max_filesize/post_max_size, permisos, etc.).
        // Sin esta revisión el gasto se registraba sin comprobante y la columna "Archivo"
        // quedaba vacía ("—") sin ningún aviso.
        if ($errorSubida = $this->mensajeErrorSubida($request)) {
            return back()->withInput()->with('publicidad_error', $errorSubida);
        }

        $validator = Validator::make($request->all(), [
            'gestion' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'monto' => 'required|numeric|min:0.01|max:9999999.99',
            'descripcion' => 'required|string|max:500',
            'plataforma' => 'nullable|string|max:50',
            'campana_nombre' => 'nullable|string|max:255',
            'enlace_campana' => 'nullable|url|max:500',
            'pagina_facebook' => 'nullable|url|max:500',
            'programa_id' => 'nullable|exists:programas,id',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'archivo_debito' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        // publicidad_tab marca que los errores pertenecen a este formulario, para que el
        // perfil abra la pestaña de publicidad (y no la de contraseña) al volver.
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors())->with('publicidad_tab', true);
        }

        $validated = $validator->validated();

        // Los archivos se guardan ANTES de crear el registro: si el disco público no
        // permite escribir, store() devuelve false o lanza una excepción de Flysystem y
        // el gasto quedaría sin comprobante.
        $rutaComprobante = null;
        if ($request->hasFile('archivo')) {
            $rutaComprobante = $this->guardarArchivoAutogestion($request->file('archivo'));

            if (!$rutaComprobante) {
                return back()->withInput()->with('publicidad_error', 'No se pudo guardar el comprobante en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). El gasto no fue registrado.');
            }
        }

        $rutaDebito = null;
        if ($request->hasFile('archivo_debito')) {
            $rutaDebito = $this->guardarArchivoAutogestion($request->file('archivo_debito'));

            if (!$rutaDebito) {
                if ($rutaComprobante) {
                    Storage::disk('public')->delete($rutaComprobante);
                }

                return back()->withInput()->with('publicidad_error', 'No se pudo guardar el comprobante de débito en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). El gasto no fue registrado.');
            }
        }

        $gasto = new GastoAutogestion();
        $gasto->asesor_id = $asesorId;
        $gasto->gestion = $validated['gestion'];
        $gasto->mes = $validated['mes'];
        $gasto->fecha_inicio = $validated['fecha_inicio'] ?? null;
        $gasto->fecha_fin = $validated['fecha_fin'] ?? null;
        $gasto->monto = $validated['monto'];
        $gasto->descripcion = $validated['descripcion'];
        $gasto->plataforma = $validated['plataforma'] ?? null;
        $gasto->campana_nombre = $validated['campana_nombre'] ?? null;
        $gasto->enlace_campana = $validated['enlace_campana'] ?? null;
        $gasto->pagina_facebook = $validated['pagina_facebook'] ?? null;
        $gasto->programa_id = $validated['programa_id'] ?? null;

        if ($rutaComprobante) {
            $file = $request->file('archivo');
            $gasto->archivo_path = $rutaComprobante;
            $gasto->archivo_original = $file->getClientOriginalName();
            $gasto->archivo_mime = $file->getMimeType();
        }

        if ($rutaDebito) {
            $file = $request->file('archivo_debito');
            $gasto->archivo_debito_path = $rutaDebito;
            $gasto->archivo_debito_original = $file->getClientOriginalName();
            $gasto->archivo_debito_mime = $file->getMimeType();
        }

        $gasto->save();

        return back()->with('publicidad_success', 'Gasto de publicidad registrado correctamente.');
    }

    /**
     * Revisa los códigos de error de subida de PHP (UPLOAD_ERR_*) y la pérdida del
     * formulario por post_max_size. Devuelve un mensaje listo para mostrar o null.
     */
    private function mensajeErrorSubida(Request $request): ?string
    {
        foreach (['archivo' => 'comprobante', 'archivo_debito' => 'comprobante de débito'] as $campo => $etiqueta) {
            $file = $request->file($campo);

            if (!$file instanceof \Illuminate\Http\UploadedFile || $file->getError() === UPLOAD_ERR_OK) {
                continue;
            }

            if (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                return sprintf(
                    'El %s supera el límite de subida del servidor (%s). Reduce el archivo o incrementa "upload_max_filesize" en el php.ini del servidor.',
                    $etiqueta,
                    ini_get('upload_max_filesize') ?: 'límite no definido'
                );
            }

            if (in_array($file->getError(), [UPLOAD_ERR_PARTIAL, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE], true)) {
                return 'El servidor no pudo recibir el ' . $etiqueta . ' correctamente (subida incompleta o sin permisos de escritura). Intenta nuevamente.';
            }

            return 'No se pudo procesar el ' . $etiqueta . ' (código de error ' . $file->getError() . ').';
        }

        // Si se supera post_max_size, PHP descarta todo el formulario: $_POST y $_FILES
        // quedan vacíos y la validación falla sin indicar la causa real.
        if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            return 'El formulario supera el límite post_max_size del servidor (' . (ini_get('post_max_size') ?: 'límite no definido') . '). Reduce el tamaño de los archivos o incrementa ese valor en el php.ini del servidor.';
        }

        return null;
    }

    /**
     * Guarda el archivo en el disco público y devuelve la ruta, o null si falla
     * (permisos de escritura, carpeta destino inexistente, etc.).
     */
    private function guardarArchivoAutogestion(\Illuminate\Http\UploadedFile $file): ?string
    {
        try {
            return $file->store('publicidad/autogestion', 'public') ?: null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('No se pudo guardar archivo de autogestión de publicidad', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'Debes ingresar la nueva contraseña.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Mantiene sincronizado el acceso directo a Seguimiento ESAM.
        $client = app(SeguimientoEsamClient::class);

        if ($client->enabled()) {
            try {
                $client->registerAccount($request->user()->fresh(), $request->password);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('No se pudo sincronizar la contraseña con Seguimiento ESAM', [
                    'user' => $request->user()->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
