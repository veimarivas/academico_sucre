<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProgramaController extends Controller
{
    public function show($id)
    {
        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('areas_programas', 'programas.id', '=', 'areas_programas.programa_id')
            ->leftJoin('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->leftJoin('sedes', 'areas_programas.sede_id', '=', 'sedes.id')
            ->where('programas.id', $id)
            ->select(
                'programas.*',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre',
                'areas.nombre as area_nombre',
                'areas.color as area_color',
                'sedes.nombre as sede_nombre'
            )
            ->first();

        if (!$programa) {
            abort(404);
        }

        $asignados = DB::table('asignados')->orderBy('apellidos')->get();

        $responsables = DB::table('programas_responsables')
            ->join('responsables', 'programas_responsables.responsable_id', '=', 'responsables.id')
            ->where('programas_responsables.programa_id', $id)
            ->orderBy('responsables.apellidos')
            ->select('responsables.*')
            ->get();

        $modulos = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.programa_id', $id)
            ->select('modulos.*', 'docentes.nombres as docente_nombres', 'docentes.apellidos as docente_apellidos', 'docentes.ci as docente_ci')
            ->orderBy('modulos.id')
            ->get();

        // Estado de notas por módulo (solo para módulos Concluido)
        $modulosIds = $modulos->pluck('id');
        $estadoNotasPorModulo = DB::table('modulos_participantes')
            ->whereIn('modulo_id', $modulosIds)
            ->selectRaw('modulo_id,
                SUM(CASE WHEN nota_modulo IS NOT NULL AND nota_modulo > 0 THEN 1 ELSE 0 END) as con_nota_modulo,
                SUM(CASE WHEN nota_nivelacion IS NOT NULL AND nota_nivelacion > 0 THEN 1 ELSE 0 END) as con_nota_nivelacion,
                SUM(CASE WHEN nota_modulo IS NOT NULL AND nota_modulo < 71 THEN 1 ELSE 0 END) as total_reprobados,
                COUNT(*) as total_participantes')
            ->groupBy('modulo_id')
            ->get()
            ->keyBy('modulo_id');

        $horariosModulos = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('modulos.programa_id', $id)
            ->select(
                'horarios.*',
                'modulos.nombre as modulo_nombre',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        $inscripciones = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->where('inscripciones.programa_id', $id)
            ->select(
                'inscripciones.*',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.correo',
                'participantes.celular',
                'participantes.sexo',
                'participantes.departamento',
                'asesores.nombres as asesor_nombres',
                'asesores.apellidos as asesor_apellidos'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $inscripcionesConBaja = DB::table('baja_historial')
            ->whereIn('inscripcion_id', $inscripciones->pluck('id'))
            ->pluck('inscripcion_id')
            ->unique()
            ->values();

        $archivosBaja = DB::table('baja_historial')
            ->whereIn('inscripcion_id', $inscripciones->pluck('id'))
            ->whereNotNull('archivo_path')
            ->orderByDesc('created_at')
            ->get(['inscripcion_id', 'archivo_path', 'archivo_original'])
            ->keyBy('inscripcion_id');

        $modulosParticipantes = DB::table('modulos_participantes')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos.programa_id', $id)
            ->leftJoin('deudas', 'modulos_participantes.id', '=', 'deudas.modulo_participante_id')
            ->leftJoin('pagos', 'deudas.pago_id', '=', 'pagos.id')
            ->select(
                'modulos_participantes.id',
                'modulos_participantes.modulo_id',
                'modulos_participantes.participante_id',
                'modulos_participantes.tipo',
                'modulos_participantes.deuda as deuda_monto',
                'modulos_participantes.total_pagado',
                'modulos_participantes.descuento',
                'modulos_participantes.estado as mp_estado',
                'modulos.nombre as modulo_nombre',
                'modulos.id as modulo_id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                DB::raw('COALESCE(SUM(deudas.monto_bs), 0) as pagado'),
                DB::raw('COUNT(deudas.id) as num_pagos')
            )
            ->groupBy(
                'modulos_participantes.id',
                'modulos_participantes.modulo_id',
                'modulos_participantes.participante_id',
                'modulos_participantes.tipo',
                'modulos_participantes.deuda',
                'modulos_participantes.total_pagado',
                'modulos_participantes.descuento',
                'modulos_participantes.estado',
                'modulos.nombre',
                'modulos.id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->orderBy('modulos.id')
            ->get()
            ->map(function ($r) {
                $r->total_pagado = (float) $r->pagado;
                return $r;
            });

        $colegiaturas = $modulosParticipantes->where('tipo', 'Colegiatura');
        $matriculas = $modulosParticipantes->where('tipo', 'Matricula');

        $netoMatriculasTotal = $inscripciones->sum(fn($i) => max(0, (float)$i->matricula - (float)($i->descuento_matricula ?? 0)));
        $netoColegiaturasTotal = $inscripciones->sum(fn($i) => max(0, (float)$i->monto_programa - (float)($i->descuento_colegiatura ?? 0)));

        $netoColPorParticipante = $inscripciones->keyBy('participante_id')->map(function ($ins) {
            return [
                'neto' => max(0, (float)$ins->monto_programa - (float)($ins->descuento_colegiatura ?? 0)),
                'descuento' => (float)($ins->descuento_colegiatura ?? 0),
            ];
        });

        $colegiaturaResumen = $colegiaturas->groupBy('participante_id')->map(function ($items) use ($netoColPorParticipante) {
            $first = $items->first();
            $netoInfo = $netoColPorParticipante->get($first->participante_id);
            $neto = $netoInfo ? $netoInfo['neto'] : $items->sum('deuda_monto');
            return (object) [
                'participante_id' => $first->participante_id,
                'ci' => $first->ci,
                'nombres' => $first->nombres,
                'apellidos' => $first->apellidos,
                'total_deuda' => $neto,
                'total_pagado' => $items->sum('total_pagado'),
                'total_descuento' => $netoInfo ? $netoInfo['descuento'] : $items->sum('descuento'),
                'num_pagos' => $items->sum('num_pagos'),
            ];
        })->values();

        $colegiaturaResumen = $colegiaturaResumen->sortBy(function ($item) {
            return $item->apellidos . ' ' . $item->nombres;
        })->values();

        $colegiaturaStats = [
            'total_programa_bruto' => $inscripciones->sum('monto_programa'),
            'descuento_inscripciones' => $inscripciones->sum('descuento_colegiatura'),
            'neto_inscripciones' => $netoColegiaturasTotal,
            'cobrado_modulos' => $colegiaturas->sum('total_pagado'),
            'pendiente_modulos' => max(0, $netoColegiaturasTotal - $colegiaturas->sum('total_pagado')),
            'deuda_modulos' => $netoColegiaturasTotal,
            'descuento_modulos' => $colegiaturas->sum('descuento'),
            'pagados_count' => $colegiaturaResumen->filter(fn($c) => $c->total_pagado > 0 && $c->total_pagado >= $c->total_deuda)->count(),
            'pendientes_count' => $colegiaturaResumen->filter(fn($c) => $c->total_pagado <= 0 || $c->total_pagado < $c->total_deuda)->count(),
            'participantes_count' => $colegiaturaResumen->count(),
        ];

        $total = $inscripciones->count();
        $operativos = $inscripciones->where('estado', 1)->where('baja_operativa', 0)->where('baja_academica', 0)->count();
        $bajas_tempranas = $inscripciones->where('estado', 0)->where('baja_operativa', 0)->where('baja_academica', 0)->count();
        $bajas_operativas = $inscripciones->where('baja_operativa', 1)->count();
        $bajas_academicas = $inscripciones->where('baja_academica', 1)->count();
        $planes_contado = $inscripciones->where('contado', 1)->count();
        $planes_regulares = $inscripciones->where('contado', 0)->count();
        $registrados_unsxx = $inscripciones->where('registrado_u', 1)->count();
        $concluidos = $inscripciones->where('aprobado', 1)->count();

        $hombres = $inscripciones->where('sexo', 'Hombre')->count();
        $mujeres = $inscripciones->where('sexo', 'Mujer')->count();

        $departamentos = $inscripciones
            ->groupBy('departamento')
            ->map(fn($group) => count($group))
            ->sortDesc();

        $resumen = [
            'total_inscritos' => $total,
            'total_matricula' => $inscripciones->sum('matricula'),
            'total_colegiatura' => $inscripciones->sum('monto_programa'),
            'total_pagado_matriculas' => $matriculas->sum('total_pagado'),
            'total_pagado_colegiaturas' => $colegiaturas->sum('total_pagado'),
            'total_deuda_matriculas' => $netoMatriculasTotal,
            'total_deuda_colegiaturas' => $netoColegiaturasTotal,
            'inscritos_activos' => $inscripciones->where('estado', 1)->count(),
            'operativos' => $operativos,
            'bajas_tempranas' => $bajas_tempranas,
            'bajas_operativas' => $bajas_operativas,
            'bajas_academicas' => $bajas_academicas,
            'planes_contado' => $planes_contado,
            'planes_regulares' => $planes_regulares,
            'registrados_unsxx' => $registrados_unsxx,
            'concluidos' => $concluidos,
        ];

        $matriculaStats = [
            'total_matricula_bruto' => $inscripciones->sum('matricula'),
            'descuento_inscripciones' => $inscripciones->sum('descuento_matricula'),
            'neto_inscripciones' => $netoMatriculasTotal,
            'deuda_inscripciones' => $netoMatriculasTotal,
            'cobrado_modulos' => $matriculas->sum('total_pagado'),
            'pendiente_modulos' => max(0, $netoMatriculasTotal - $matriculas->sum('total_pagado')),
            'deuda_modulos' => $netoMatriculasTotal,
            'descuento_modulos' => $matriculas->sum('descuento'),
            'pagados_count' => $matriculas->filter(fn($m) => $m->total_pagado > 0 && $m->total_pagado >= $m->deuda_monto)->count(),
            'pendientes_count' => $matriculas->filter(fn($m) => $m->total_pagado <= 0 || $m->total_pagado < $m->deuda_monto)->count(),
            'participantes_count' => $matriculas->pluck('participante_id')->unique()->count(),
        ];

        $pagoMatriculaPorParticipante = $modulosParticipantes->where('tipo', 'Matricula')->groupBy('participante_id')->map(function ($items) {
            $deuda = $items->sum('deuda_monto');
            $pagado = $items->sum('total_pagado');
            return (object) [
                'pagado' => $pagado,
                'deuda' => $deuda,
                'pendiente' => $deuda - $pagado,
            ];
        });

        $pagoColegiaturaPorParticipante = $modulosParticipantes->where('tipo', 'Colegiatura')->groupBy('participante_id')->map(function ($items) {
            $deuda = $items->sum('deuda_monto');
            $pagado = $items->sum('total_pagado');
            return (object) [
                'pagado' => $pagado,
                'deuda' => $deuda,
                'pendiente' => $deuda - $pagado,
            ];
        });

        $ingresosPorMes = DB::table('pagos')
            ->join('deudas', 'pagos.id', '=', 'deudas.pago_id')
            ->join('modulos_participantes', 'deudas.modulo_participante_id', '=', 'modulos_participantes.id')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(pagos.created, "%Y-%m") as mes'),
                'modulos_participantes.tipo',
                DB::raw('CAST(SUM(deudas.monto_bs) AS DECIMAL(10,2)) as total')
            )
            ->groupBy('mes', 'modulos_participantes.tipo')
            ->orderBy('mes')
            ->get();

        $historialInscripciones = DB::table('inscripciones')
            ->where('programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(fecha_inscripcion, "%Y-%m") as mes'),
                DB::raw('SUM(CASE WHEN contado = 1 THEN 1 ELSE 0 END) as contado'),
                DB::raw('SUM(CASE WHEN contado = 0 THEN 1 ELSE 0 END) as regular'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $inscritosPorMes = DB::table('inscripciones')
            ->where('programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(fecha_inscripcion, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as inscritos')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $pagaronPorMes = DB::table('pagos')
            ->join('deudas', 'pagos.id', '=', 'deudas.pago_id')
            ->join('modulos_participantes', 'deudas.modulo_participante_id', '=', 'modulos_participantes.id')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(pagos.created, "%Y-%m") as mes'),
                DB::raw('COUNT(DISTINCT modulos_participantes.participante_id) as pagaron')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $pagosDetalle = DB::table('pagos')
            ->join('deudas', 'pagos.id', '=', 'deudas.pago_id')
            ->join('modulos_participantes', 'deudas.modulo_participante_id', '=', 'modulos_participantes.id')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos.programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(pagos.created, "%Y-%m") as mes'),
                'participantes.id as participante_id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'modulos_participantes.tipo',
                DB::raw('CAST(deudas.monto_bs AS DECIMAL(10,2)) as monto')
            )
            ->orderBy('mes')
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $inscripcionesDetalle = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('inscripciones.programa_id', $id)
            ->select(
                DB::raw('DATE_FORMAT(inscripciones.fecha_inscripcion, "%Y-%m") as mes'),
                'inscripciones.participante_id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'inscripciones.contado',
                'inscripciones.estado as insc_estado'
            )
            ->orderBy('mes')
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $pagosAgrupados = $pagosDetalle->groupBy('mes')->map(function ($items) {
            return $items->groupBy('participante_id')->map(function ($pagos) {
                $first = $pagos->first();
                return (object) [
                    'ci' => $first->ci,
                    'nombres' => $first->nombres,
                    'apellidos' => $first->apellidos,
                    'participante_id' => $first->participante_id,
                    'total' => $pagos->sum('monto'),
                    'matricula' => $pagos->where('tipo', 'Matricula')->sum('monto'),
                    'colegiatura' => $pagos->where('tipo', 'Colegiatura')->sum('monto'),
                    'nivelacion' => $pagos->where('tipo', 'Nivelacion')->sum('monto'),
                ];
            })->values();
        });

        $inscripcionesAgrupadas = $inscripcionesDetalle->groupBy('mes')->map(function ($items) {
            return $items->values();
        });

        $participantesDesarrollo = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('inscripciones.programa_id', $id)
            ->select('participantes.id', 'participantes.ci', 'participantes.nombres', 'participantes.apellidos', 'participantes.celular', 'inscripciones.estado', 'inscripciones.plataforma_bloqueado', 'inscripciones.baja_academica', 'inscripciones.baja_operativa', 'inscripciones.baja_contable', 'inscripciones.es_traspaso')
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $modulosDesarrollo = DB::table('modulos')
            ->where('programa_id', $id)
            ->orderBy('id')
            ->get(['id', 'nombre', 'desarrollo']);

        $notasDesarrollo = DB::table('modulos_participantes')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $id)
            ->where('modulos_participantes.tipo', '!=', 'Nivelacion')
            ->select(
                'modulos_participantes.participante_id',
                'modulos_participantes.modulo_id',
                DB::raw('MAX(modulos_participantes.nota_modulo) as nota_modulo'),
                DB::raw('MAX(modulos_participantes.nota_nivelacion) as nota_nivelacion'),
                DB::raw('(SELECT MAX(mpn.nota_nivelacion) FROM modulos_participantes mpn WHERE mpn.participante_id = modulos_participantes.participante_id AND mpn.modulo_id = modulos_participantes.modulo_id AND mpn.tipo = "Nivelacion") as nota_nivelacion_pago')
            )
            ->groupBy('modulos_participantes.participante_id', 'modulos_participantes.modulo_id')
            ->get()
            ->keyBy(function ($item) {
                return $item->participante_id . '_' . $item->modulo_id;
            });

        // === Matriz contable: [participante_id][modulo_id] = deuda/descuento/pagado agregado + cuotas ===
        $contableMatrix = [];
        $mpRowsContable = DB::table('modulos_participantes')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->leftJoin('deudas', 'modulos_participantes.id', '=', 'deudas.modulo_participante_id')
            ->where('modulos.programa_id', $id)
            ->select(
                'modulos_participantes.id',
                'modulos_participantes.participante_id',
                'modulos_participantes.modulo_id',
                'modulos_participantes.tipo',
                'modulos_participantes.deuda',
                'modulos_participantes.descuento',
                DB::raw('COALESCE(SUM(deudas.monto_bs), 0) as total_pagado'),
                'modulos_participantes.fecha_pago'
            )
            ->groupBy(
                'modulos_participantes.id',
                'modulos_participantes.participante_id',
                'modulos_participantes.modulo_id',
                'modulos_participantes.tipo',
                'modulos_participantes.deuda',
                'modulos_participantes.descuento',
                'modulos_participantes.fecha_pago'
            )->get();
        $extrasMatrix = [];
        $seenCounts = [];
        foreach ($mpRowsContable as $r) {
            $key = $r->participante_id . '_' . $r->modulo_id;
            if (!isset($contableMatrix[$key])) {
                $contableMatrix[$key] = ['deuda' => 0, 'descuento' => 0, 'total_pagado' => 0, 'tipos' => [], 'cuotas' => []];
            }
            $contableMatrix[$key]['deuda'] += (float) $r->deuda;
            $contableMatrix[$key]['descuento'] += (float) $r->descuento;
            $contableMatrix[$key]['total_pagado'] += (float) $r->total_pagado;
            $contableMatrix[$key]['tipos'][] = $r->tipo;
            $contableMatrix[$key]['cuotas'][] = [
                'id' => $r->id,
                'tipo' => $r->tipo,
                'deuda' => (float) $r->deuda,
                'descuento' => (float) $r->descuento,
                'total_pagado' => (float) $r->total_pagado,
                'fecha_pago' => $r->fecha_pago,
            ];

            // Identificar cuotas extras (Colegiatura duplicada en el mismo módulo)
            if ($r->tipo === 'Colegiatura') {
                $sk = $r->participante_id . '_' . $r->modulo_id . '_Col';
                if (!isset($seenCounts[$sk])) $seenCounts[$sk] = 0;
                $seenCounts[$sk]++;
                if ($seenCounts[$sk] > 1) {
                    $compKey = $r->participante_id . '_' . $r->modulo_id;
                    $matCompanion = null;
                    foreach (($contableMatrix[$compKey]['cuotas'] ?? []) as $c) {
                        if ($c['tipo'] === 'Matricula') {
                            $matCompanion = $c;
                            break;
                        }
                    }
                    $extrasMatrix[$r->participante_id][] = [
                        'modulo_id' => $r->modulo_id,
                        'tipo' => $r->tipo,
                        'deuda' => (float) $r->deuda,
                        'descuento' => (float) $r->descuento,
                        'total_pagado' => (float) $r->total_pagado,
                        'fecha_pago' => $r->fecha_pago,
                        'matricula' => $matCompanion,
                    ];
                }
            }
        }
        $maxExtras = 0;
        foreach ($extrasMatrix as $pid => $extras) {
            $maxExtras = max($maxExtras, count($extras));
        }

        // === Verificar si todos los módulos están concluidos y si es tipo Diplomado ===
        $todosModulosConcluidos = $modulos->count() > 0 && $modulos->every(fn($m) => ($m->desarrollo ?? '') === 'Concluido');
        $esDiplomado = (int) ($programa->tipo_id ?? 0) === 2;

        // === Datos para Seguimiento concluido fase académica ===
        $certificacionDebts = DB::table('modulos_participantes')
            ->whereNull('modulo_id')
            ->whereIn('participante_id', $inscripciones->pluck('participante_id'))
            ->where('tipo', 'Certificacion')
            ->get()
            ->keyBy('participante_id');

        $seguimientoConcluido = [];
        if ($todosModulosConcluidos && $esDiplomado) {
            $seguimientosPorInscripcion = DB::table('seguimientos')
                ->whereIn('inscripcione_id', $inscripciones->pluck('id'))
                ->orderBy('etapa')
                ->get()
                ->groupBy('inscripcione_id');

            foreach ($inscripciones as $ins) {
                $pId = $ins->participante_id;
                // Detalle de módulos concluidos
                $modulosAprobados = 0;
                $modulosTotales = 0;
                $detalleModulos = [];
                foreach ($modulosDesarrollo as $mod) {
                    if (($mod->desarrollo ?? '') === 'Concluido') {
                        $modulosTotales++;
                        $key = $pId . '_' . $mod->id;
                        $nota = $notasDesarrollo->get($key);
                        $nm = $nota ? (float) $nota->nota_modulo : null;
                        $nn = $nota ? (float) $nota->nota_nivelacion_pago : null;
                        $notaFinal = $nm;
                        if ($nn !== null && $nn > 0 && ($nm === null || $nn > $nm)) {
                            $notaFinal = $nn;
                        } elseif ($nm === null && $nn !== null) {
                            $notaFinal = $nn;
                        }
                        $aprobo = $notaFinal !== null && $notaFinal >= 51;
                        if ($aprobo) {
                            $modulosAprobados++;
                        }
                        $detalleModulos[] = [
                            'modulo_nombre' => $mod->nombre,
                            'nota_modulo' => $nm,
                            'nota_nivelacion' => $nn,
                            'nota_final' => $notaFinal,
                            'aprobo' => $aprobo,
                        ];
                    }
                }
                $aproboModulos = $modulosTotales > 0 && $modulosAprobados >= $modulosTotales;

                // Deudas de matrícula
                $pMat = $pagoMatriculaPorParticipante->get($pId);
                $deudaMat = $pMat ? max(0, (float) $pMat->deuda - (float) $pMat->pagado) : 0;
                $pagoMatOk = $deudaMat == 0;

                // Deudas de colegiatura
                $pCol = $pagoColegiaturaPorParticipante->get($pId);
                $deudaCol = $pCol ? max(0, (float) $pCol->deuda - (float) $pCol->pagado) : 0;
                $pagoColOk = $deudaCol == 0;

                $inscSeguimientos = isset($seguimientosPorInscripcion[$ins->id])
                    ? $seguimientosPorInscripcion[$ins->id]->toArray()
                    : [];

                $seguimientoConcluido[] = [
                    'participante_id' => $pId,
                    'ci' => $ins->ci,
                    'nombres' => $ins->nombres,
                    'apellidos' => $ins->apellidos,
                    'aprobo_modulos' => $aproboModulos,
                    'detalle_modulos' => $detalleModulos,
                    'deuda_matricula_ok' => $pagoMatOk,
                    'deuda_matricula_monto' => $deudaMat,
                    'deuda_colegiatura_ok' => $pagoColOk,
                    'deuda_colegiatura_monto' => $deudaCol,
                    'modalidad_titulacion' => $ins->modalidad_titulacion ?? null,
                    'tiene_deuda_certificacion' => $certificacionDebts->has($pId),
                    'pago_titulacion' => $certificacionDebts->has($pId) && (float)$certificacionDebts->get($pId)->total_pagado >= (float)$certificacionDebts->get($pId)->deuda,
                    'registrado_u' => (int) ($ins->registrado_u ?? 0),
                    'seguimientos' => $inscSeguimientos,
                    'es_traspaso' => (int) ($ins->es_traspaso ?? 0),
                ];
            }
        }

        // === Cálculo de costos docentes para Rentabilidad ===
        $costosDocentes = DB::table('modulos')
            ->where('programa_id', $id)
            ->selectRaw('
                COALESCE(SUM(pago_docente), 0) as total_pago_docente,
                COALESCE(SUM(iva), 0) as total_iva,
                COALESCE(SUM(it), 0) as total_it,
                COALESCE(SUM(gastos_extra), 0) as total_gastos_extra,
                COALESCE(SUM(total_descuento), 0) as total_descuento,
                COALESCE(SUM(total_pagado), 0) as total_pagado_docente,
                SUM(CASE WHEN factura = 1 THEN 1 ELSE 0 END) as modulos_con_factura,
                COUNT(*) as total_modulos
            ')
            ->first();

        $totalPagoDocente = (float) $costosDocentes->total_pago_docente;
        $totalIva = (float) $costosDocentes->total_iva;
        $totalIt = (float) $costosDocentes->total_it;
        $totalGastosExtra = (float) $costosDocentes->total_gastos_extra;
        $modulosConFactura = (int) $costosDocentes->modulos_con_factura;
        $totalModulos = (int) $costosDocentes->total_modulos;

        // Costo real del programa en docencia: pago_docente + retenciones (IVA+IT cuando factura)
        $costoDocenteReal = $totalPagoDocente + $totalIva + $totalIt + $totalGastosExtra;

        // Ingreso bruto y neto (con descuentos aplicados)
        $totalMatriculaBruto = $inscripciones->sum('matricula');
        $totalMatriculaDesc = $inscripciones->sum('descuento_matricula');
        $totalColegiaturaBruto = $inscripciones->sum('monto_programa');
        $totalColegiaturaDesc = $inscripciones->sum('descuento_colegiatura');
        $ingresoBrutoTotal = $totalMatriculaBruto + $totalColegiaturaBruto;
        $ingresoNetoReal = ($totalMatriculaBruto - $totalMatriculaDesc) + ($totalColegiaturaBruto - $totalColegiaturaDesc);
        $totalInscritos = $resumen['total_inscritos'];
        $ingresoPromedioParticipante = $totalInscritos > 0 ? $ingresoNetoReal / $totalInscritos : 0;

        // Datos de campaña/marketing del programa
        $presupuestoCampana = (float) ($programa->presupuesto_campana ?? 0);
        $tieneCampana = (int) ($programa->tiene_presupuesto_campana ?? 0);

        // === Parámetros persistentes de rentabilidad ===
        $costoUniversidadAlumno = (float) ($programa->costo_universidad_alumno ?? 0);
        $gastosAdminMensual = (float) ($programa->gastos_admin_mensual ?? 0);
        $mesesCampana = max(1, (int) ($programa->meses_campana ?? 1));

        // Duración en meses: valor guardado o derivada de las fechas del programa
        $duracionDerivada = 0;
        if (!empty($programa->fecha_inicio) && !empty($programa->fecha_conclusion)) {
            $ini = \Carbon\Carbon::parse($programa->fecha_inicio);
            $fin = \Carbon\Carbon::parse($programa->fecha_conclusion);
            $duracionDerivada = max(1, (int) ceil($ini->diffInDays($fin) / 30.44));
        }
        $duracionMeses = (int) ($programa->duracion_meses ?? 0) ?: ($duracionDerivada ?: 1);

        // Precio de lista por alumno (para escenario de viabilidad de inicio)
        $precioListaAlumno = (float) ($programa->matricula ?? 0) + (float) ($programa->colegiatura ?? 0);

        // Deserción real (bajas de cualquier tipo sobre el total)
        $totalBajas = $bajas_tempranas + $bajas_operativas + $bajas_academicas;

        // $certificacionDebts ya fue cargado arriba para seguimiento

        // Folios (Registros Universidad) — incluye folios globales con participantes de este programa
        $folios = DB::table('folios')
            ->leftJoin('folio_participantes', 'folios.id', '=', 'folio_participantes.folio_id')
            ->leftJoin('inscripciones', 'folio_participantes.participante_id', '=', 'inscripciones.participante_id')
            ->where(function ($q) use ($id) {
                $q->where('folios.programa_id', $id)
                  ->orWhere('inscripciones.programa_id', $id);
            })
            ->select('folios.*')
            ->distinct()
            ->orderBy('folios.created_at', 'desc')
            ->get();

        // Conceptos ya registrados por participante en otros folios (para conflictos)
        $conceptosRegistrados = DB::table('folio_participantes')
            ->join('folios', 'folio_participantes.folio_id', '=', 'folios.id')
            ->where('folios.programa_id', $id)
            ->select(
                'folio_participantes.participante_id',
                DB::raw("CONCAT_WS(',',
                    IF(monto_inscripcion > 0, 'Inscripcion', NULL),
                    IF(monto_matricula > 0, 'Matricula', NULL),
                    IF(monto_colegiatura > 0, 'Colegiatura', NULL),
                    IF(monto_certificacion > 0, 'Certificacion', NULL)
                ) as conceptos"),
                'folios.nombre as folio_nombre',
                'folios.id as folio_id'
            )
            ->get()
            ->groupBy('participante_id')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'folio_id' => $item->folio_id,
                        'folio_nombre' => $item->folio_nombre,
                        'conceptos' => array_filter(explode(',', $item->conceptos)),
                    ];
                });
            });

        $traspasoPorParticipante = $inscripciones->keyBy('participante_id')->map(fn($i) => (int) ($i->es_traspaso ?? 0));

        // === Nivelación (por módulo concluido, etapa de pago posterior a la etapa regular) ===
        $nivelacionCostoParticipante = (float) ($programa->nivelacion_costo_participante ?? 0);
        $nivelacionHonorarioDocente = (float) ($programa->nivelacion_honorario_docente ?? 0);

        // Honorario del docente de nivelación POR PARTICIPANTE, según el tipo del programa
        $tipoRow = DB::table('tipos')->where('id', $programa->tipo_id)->first();
        $nivelacionPagoDocente = $tipoRow ? (float) ($tipoRow->pago_docente_nivelacion ?? 0) : 0;
        $nivelacionPagoDocenteDefault = $nivelacionPagoDocente > 0 ? $nivelacionPagoDocente : 0;

        // Costo por defecto según tipo del programa (Maestría 500, Especialidad 300, Diplomado 100)
        $costosNivelacionPorTipo = [
            2 => 100, // Diplomado
            3 => 300, // Especialidad
            4 => 500, // Maestrías
        ];
        $nivelacionCostoDefault = $costosNivelacionPorTipo[(int) ($programa->tipo_id ?? 0)] ?? 0;

        // Docente asignado a la nivelación
        $nivelacionDocente = null;
        if (!empty($programa->nivelacion_docente_id)) {
            $nivelacionDocente = DB::table('docentes')->where('id', $programa->nivelacion_docente_id)->first();
        }
        $docentes = DB::table('docentes')->orderBy('apellidos')->orderBy('nombres')->get();

        // Módulos en estado Concluido (cada uno habilita su propia nivelación)
        $modulosConcluidos = $modulos->where('desarrollo', 'Concluido')->values();
        $modulosConcluidosIds = $modulosConcluidos->pluck('id');

        // Participantes que no llegaron a la nota mínima (71) por módulo concluido.
        // Se considera reprobado la nota 0, vacía (NULL) o menor a 71, siempre que
        // además NO hayan aprobado la nivelación gratuita (nota_nivelacion < 71 o sin nota).
        $reprobadosRows = collect();
        if ($modulosConcluidosIds->isNotEmpty()) {
            $reprobadosRows = DB::table('modulos_participantes')
                ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
                ->whereIn('modulos_participantes.modulo_id', $modulosConcluidosIds)
                ->where('modulos_participantes.tipo', '!=', 'Nivelacion')
                ->select(
                    'modulos_participantes.modulo_id',
                    'participantes.id as participante_id',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos',
                    DB::raw('MAX(modulos_participantes.nota_modulo) as nota_modulo'),
                    DB::raw('MAX(modulos_participantes.nota_nivelacion) as nota_nivelacion')
                )
                ->groupBy(
                    'modulos_participantes.modulo_id',
                    'participantes.id',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos'
                )
                ->havingRaw('
                    (MAX(modulos_participantes.nota_modulo) IS NULL OR MAX(modulos_participantes.nota_modulo) < 71)
                    AND
                    (MAX(modulos_participantes.nota_nivelacion) IS NULL OR MAX(modulos_participantes.nota_nivelacion) <= 0 OR MAX(modulos_participantes.nota_nivelacion) < 71)
                ')
                ->orderBy('participantes.apellidos')
                ->orderBy('participantes.nombres')
                ->get();
        }

        // Solo participantes con inscripción activa (sin bajas)
        $inscritosActivosIds = $inscripciones
            ->where('estado', 1)
            ->where('baja_operativa', 0)
            ->where('baja_academica', 0)
            ->pluck('participante_id')
            ->all();

        // Cuotas de nivelación existentes (tipo Nivelacion)
        $nivelacionCuotas = collect();
        if ($modulosConcluidosIds->isNotEmpty()) {
            $nivelacionCuotas = DB::table('modulos_participantes')
                ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
                ->whereIn('modulos_participantes.modulo_id', $modulosConcluidosIds)
                ->where('modulos_participantes.tipo', 'Nivelacion')
                ->select(
                    'modulos_participantes.id as mp_id',
                    'modulos_participantes.modulo_id',
                    'modulos_participantes.participante_id',
                    'modulos_participantes.deuda',
                    'modulos_participantes.descuento',
                    'modulos_participantes.total_pagado',
                    'modulos_participantes.fecha_pago',
                    'modulos_participantes.nota_nivelacion',
                    'modulos_participantes.comprobante_pago',
                    'modulos_participantes.comprobante_original',
                    'modulos_participantes.comprobante_validado',
                    'modulos_participantes.pago_docente_nivelacion',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos'
                )
                ->orderBy('participantes.apellidos')
                ->orderBy('participantes.nombres')
                ->get()
                ->map(function ($r) {
                    $deuda = (float) $r->deuda;
                    $pagado = (float) $r->total_pagado;
                    $r->saldo = max(0, $deuda - $pagado);
                    $r->pagado_ok = $deuda > 0 && $pagado >= $deuda;
                    return $r;
                });
        }

        // Horarios de nivelación de los módulos concluidos
        $horariosNivelacion = collect();
        if ($modulosConcluidosIds->isNotEmpty()) {
            $horariosNivelacion = DB::table('horarios')
                ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
                ->whereIn('horarios.modulo_id', $modulosConcluidosIds)
                ->where('horarios.tipo', 'Nivelacion')
                ->select(
                    'horarios.*',
                    'asignados.nombres as asignado_nombres',
                    'asignados.apellidos as asignado_apellidos'
                )
                ->orderBy('horarios.fecha_desarrollo')
                ->orderBy('horarios.hora_inicio')
                ->get();
        }

        // Estructura por módulo concluido
        $nivelacionModulos = $modulosConcluidos->map(function ($m) use ($reprobadosRows, $nivelacionCuotas, $horariosNivelacion, $inscritosActivosIds, $nivelacionPagoDocente) {
            $elegibles = $reprobadosRows
                ->where('modulo_id', $m->id)
                ->filter(fn($r) => in_array((int) $r->participante_id, $inscritosActivosIds))
                ->values();

            $cuotas = $nivelacionCuotas->where('modulo_id', $m->id)->values();

            $rows = $elegibles->map(function ($r) use ($cuotas) {
                $cuota = $cuotas->firstWhere('participante_id', (int) $r->participante_id);
                return (object) [
                    'participante_id' => (int) $r->participante_id,
                    'ci' => $r->ci,
                    'nombres' => $r->nombres,
                    'apellidos' => $r->apellidos,
                    'nota_modulo' => (float) $r->nota_modulo,
                    'nota_nivelacion' => $r->nota_nivelacion !== null ? (float) $r->nota_nivelacion : null,
                    'tiene_cuota' => (bool) $cuota,
                    'mp_id' => $cuota->mp_id ?? null,
                    'deuda' => (float) ($cuota->deuda ?? 0),
                    'saldo' => $cuota ? (float) $cuota->saldo : null,
                    'pagado_ok' => $cuota->pagado_ok ?? false,
                    'nota_nivelacion' => $cuota->nota_nivelacion ?? null,
                    'comprobante_pago' => $cuota->comprobante_pago ?? null,
                    'comprobante_original' => $cuota->comprobante_original ?? null,
                    'comprobante_validado' => (int) ($cuota->comprobante_validado ?? 0),
                    'pago_docente_cuota' => (float) ($cuota->pago_docente_nivelacion ?? 0),
                ];
            })->values();

            $docenteModulo = $m->docente_id ? (object) [
                'id' => (int) $m->docente_id,
                'nombres' => $m->docente_nombres ?? '',
                'apellidos' => $m->docente_apellidos ?? '',
            ] : null;

            return (object) [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'docente' => $docenteModulo,
                'pago_docente_por_participante' => $nivelacionPagoDocente,
                'fecha_conclusion' => $m->fecha_conclusion,
                'fecha_limite' => $m->fecha_conclusion ? \Carbon\Carbon::parse($m->fecha_conclusion)->addMonths(2)->format('Y-m-d') : null,
                'rows' => $rows,
                'elegibles_count' => $elegibles->count(),
                'cuotas_count' => $cuotas->count(),
                'total_esperado' => $cuotas->sum('deuda'),
                'total_cobrado' => $cuotas->sum('total_pagado'),
                'validados_count' => $cuotas->filter(fn($c) => (int) ($c->comprobante_validado ?? 0) === 1)->count(),
                'total_honorario' => $cuotas->filter(fn($c) => (int) ($c->comprobante_validado ?? 0) === 1)->sum('pago_docente_nivelacion'),
                'horarios' => $horariosNivelacion->where('modulo_id', $m->id)->values(),
            ];
        })->values();

        $nivelacionTotalEsperado = $nivelacionModulos->sum('total_esperado');
        $nivelacionTotalCobrado = $nivelacionModulos->sum('total_cobrado');
        $nivelacionTotalCuotas = $nivelacionModulos->sum('cuotas_count');
        $nivelacionTotalValidados = $nivelacionModulos->sum('validados_count');
        $nivelacionTotalHonorario = $nivelacionModulos->sum('total_honorario');

        return view('admin.programas.show', compact(
            'programa',
            'responsables',
            'modulos',
            'inscripciones',
            'inscripcionesConBaja',
            'archivosBaja',
            'traspasoPorParticipante',
            'matriculas',
            'colegiaturas',
            'certificacionDebts',
            'colegiaturaResumen',
            'colegiaturaStats',
            'resumen',
            'hombres',
            'mujeres',
            'departamentos',
            'horariosModulos',
            'asignados',
            'matriculaStats',
            'pagoMatriculaPorParticipante',
            'pagoColegiaturaPorParticipante',
            'ingresosPorMes',
            'historialInscripciones',
            'inscritosPorMes',
            'pagaronPorMes',
            'pagosDetalle',
            'inscripcionesDetalle',
            'pagosAgrupados',
            'inscripcionesAgrupadas',
            'participantesDesarrollo',
            'modulosDesarrollo',
            'notasDesarrollo',
            'contableMatrix',
            'extrasMatrix',
            'maxExtras',
            'todosModulosConcluidos',
            'esDiplomado',
            'seguimientoConcluido',
            'estadoNotasPorModulo',
            'totalPagoDocente',
            'totalIva',
            'totalIt',
            'totalGastosExtra',
            'costoDocenteReal',
            'modulosConFactura',
            'totalModulos',
            'ingresoBrutoTotal',
            'ingresoNetoReal',
            'ingresoPromedioParticipante',
            'totalInscritos',
            'presupuestoCampana',
            'tieneCampana',
            'costoUniversidadAlumno',
            'gastosAdminMensual',
            'mesesCampana',
            'duracionMeses',
            'duracionDerivada',
            'precioListaAlumno',
            'totalBajas',
            'folios',
            'conceptosRegistrados',
            'nivelacionCostoParticipante',
            'nivelacionHonorarioDocente',
            'nivelacionCostoDefault',
            'nivelacionPagoDocente',
            'nivelacionPagoDocenteDefault',
            'nivelacionDocente',
            'docentes',
            'nivelacionModulos',
            'nivelacionTotalEsperado',
            'nivelacionTotalCobrado',
            'nivelacionTotalCuotas',
            'nivelacionTotalValidados',
            'nivelacionTotalHonorario',
            'horariosNivelacion'
        ));
    }

    public function guardarRentabilidad(Request $request, $id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $validated = $request->validate([
            'costo_universidad_alumno' => 'required|numeric|min:0|max:999999',
            'gastos_admin_mensual' => 'required|numeric|min:0|max:9999999',
            'presupuesto_campana' => 'required|numeric|min:0|max:9999999',
            'meses_campana' => 'required|integer|min:1|max:60',
            'duracion_meses' => 'required|integer|min:1|max:120',
        ]);

        DB::table('programas')->where('id', $id)->update([
            'costo_universidad_alumno' => $validated['costo_universidad_alumno'],
            'gastos_admin_mensual' => $validated['gastos_admin_mensual'],
            'presupuesto_campana' => $validated['presupuesto_campana'],
            'tiene_presupuesto_campana' => $validated['presupuesto_campana'] > 0 ? 1 : 0,
            'meses_campana' => $validated['meses_campana'],
            'duracion_meses' => $validated['duracion_meses'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parámetros de rentabilidad guardados correctamente',
        ]);
    }

    public function index()
    {
        $tipos = DB::table('tipos')
            ->select('tipos.*', DB::raw('(SELECT COUNT(*) FROM programas WHERE programas.tipo_id = tipos.id) as programas_count'))
            ->orderByDesc('programas_count')
            ->orderBy('tipos.nombre')
            ->get();
        $areas = DB::table('areas')->orderBy('nombre')->get();
        $estados = DB::table('estados')->orderBy('id')->get();
        $sedes = DB::table('sedes')->orderBy('nombre')->get();
        $responsables = DB::table('responsables')->orderBy('apellidos')->orderBy('nombres')->get();

        $gestiones = DB::table('programas')
            ->select('gestion')
            ->distinct()
            ->orderBy('gestion', 'desc')
            ->pluck('gestion');

        $gestionActual = (int) date('Y');

        $statsQuery = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->where('programas.gestion', $gestionActual);

        $statsRows = (clone $statsQuery)
            ->select(
                'tipos.id as tipo_id',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tipos.id', 'tipos.nombre', 'estados.nombre')
            ->orderBy('tipos.id')
            ->get();

        $resumen = (clone $statsQuery)
            ->select(
                DB::raw('COUNT(*) as total_programas'),
                DB::raw('COALESCE(SUM(programas.cantidad_sesiones), 0) as total_sesiones'),
                DB::raw('COALESCE(AVG(programas.matricula), 0) as promedio_matricula'),
                DB::raw('COALESCE(SUM(programas.matricula), 0) as total_matricula'),
                DB::raw('COALESCE(SUM(programas.colegiatura), 0) as total_colegiatura')
            )
            ->first();

        $initialStats = ['resumen' => [
            'total_programas' => (int) $resumen->total_programas,
            'total_sesiones' => (int) $resumen->total_sesiones,
            'promedio_matricula' => round((float) $resumen->promedio_matricula, 2),
            'total_matricula' => round((float) $resumen->total_matricula, 2),
            'total_colegiatura' => round((float) $resumen->total_colegiatura, 2),
        ]];
        $tiposMap = [];
        foreach ($statsRows as $row) {
            $tid = $row->tipo_id;
            if (!isset($tiposMap[$tid])) {
                $tiposMap[$tid] = ['tipo_id' => $row->tipo_id, 'tipo_nombre' => $row->tipo_nombre, 'estados' => [], 'total' => 0];
            }
            $tiposMap[$tid]['estados'][] = ['nombre' => $row->estado_nombre ?? 'Sin estado', 'total' => (int) $row->total];
            $tiposMap[$tid]['total'] += (int) $row->total;
        }
        $initialStats['tipos'] = array_values($tiposMap);

        return view('admin.programas.index', compact('tipos', 'areas', 'estados', 'sedes', 'responsables', 'gestiones', 'gestionActual', 'initialStats'));
    }

    public function stats(Request $request)
    {
        $query = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id');

        if ($request->filled('gestion')) {
            $query->where('programas.gestion', $request->gestion);
        }

        if ($request->filled('area_id')) {
            $query->join('areas_programas', 'programas.id', '=', 'areas_programas.programa_id');
            $query->where('areas_programas.area_id', $request->area_id);
        }

        if ($request->filled('estado_id')) {
            $query->where('programas.estado_id', $request->estado_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('programas.nombre', 'like', "%{$s}%")
                    ->orWhere('programas.codigo', 'like', "%{$s}%");
            });
        }

        $stats = (clone $query)
            ->select(
                'tipos.id as tipo_id',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tipos.id', 'tipos.nombre', 'estados.nombre')
            ->orderBy('tipos.id')
            ->get();

        $resumen = (clone $query)
            ->select(
                DB::raw('COUNT(*) as total_programas'),
                DB::raw('COALESCE(SUM(programas.cantidad_sesiones), 0) as total_sesiones'),
                DB::raw('COALESCE(AVG(programas.matricula), 0) as promedio_matricula'),
                DB::raw('COALESCE(SUM(programas.matricula), 0) as total_matricula'),
                DB::raw('COALESCE(SUM(programas.colegiatura), 0) as total_colegiatura')
            )
            ->first();

        $result = [];
        foreach ($stats as $row) {
            $tid = $row->tipo_id;
            if (!isset($result[$tid])) {
                $result[$tid] = [
                    'tipo_id' => $row->tipo_id,
                    'tipo_nombre' => $row->tipo_nombre,
                    'estados' => [],
                    'total' => 0,
                ];
            }
            $result[$tid]['estados'][] = [
                'nombre' => $row->estado_nombre ?? 'Sin estado',
                'total' => (int) $row->total,
            ];
            $result[$tid]['total'] += (int) $row->total;
        }

        return response()->json([
            'tipos' => array_values($result),
            'resumen' => [
                'total_programas' => (int) $resumen->total_programas,
                'total_sesiones' => (int) $resumen->total_sesiones,
                'promedio_matricula' => round((float) $resumen->promedio_matricula, 2),
                'total_matricula' => round((float) $resumen->total_matricula, 2),
                'total_colegiatura' => round((float) $resumen->total_colegiatura, 2),
            ],
        ]);
    }

    public function byTipo(Request $request, $tipoId)
    {
        $tiposConPago = ['Curso', 'Diplomado', 'Maestrías', 'UPI Diplomados', 'UPI Diplomado Autogestionado'];
        $tipoNombre = DB::table('tipos')->where('id', $tipoId)->value('nombre');
        $usaPago = in_array($tipoNombre, $tiposConPago);

        $realesSubquery = $usaPago
            ? "(SELECT COUNT(*)
                FROM inscripciones i2
                WHERE i2.programa_id = programas.id
                  AND i2.baja_operativa = 0
                  AND i2.baja_academica = 0
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
               )"
            : "(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND baja_operativa = 0 AND baja_academica = 0)";

        $query = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('areas_programas', 'programas.id', '=', 'areas_programas.programa_id')
            ->leftJoin('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->leftJoin('sedes', 'areas_programas.sede_id', '=', 'sedes.id')
            ->where('programas.tipo_id', $tipoId)
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
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id) as inscritos")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND es_traspaso = 1) as traspasos")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND estado = 0 AND baja_operativa = 0 AND baja_academica = 0) as bajas_tempranas")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND baja_operativa = 1) as bajas_operativas")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND baja_operativa = 1 AND baja_academica = 1) as bajas_operativas_academicas")
            ->selectRaw("(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND baja_operativa = 1 AND baja_contable = 1) as bajas_operativas_contables")
            ->selectRaw($realesSubquery . " as inscritos_reales")
            ->selectRaw("(
                SELECT GROUP_CONCAT(
                    CONCAT(r.ci, '||', r.apellidos, '||', r.nombres, '||', COALESCE(r.color, '#6c757d'))
                    ORDER BY r.apellidos, r.nombres
                    SEPARATOR ';;'
                )
                FROM programas_responsables pr
                JOIN responsables r ON pr.responsable_id = r.id
                WHERE pr.programa_id = programas.id
            ) as responsables_raw");

        if ($request->filled('gestion')) {
            $query->where('programas.gestion', $request->gestion);
        }

        if ($request->filled('area_id')) {
            $query->where('areas_programas.area_id', $request->area_id);
        }

        if ($request->filled('estado_id')) {
            $query->where('programas.estado_id', $request->estado_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('programas.nombre', 'like', "%{$s}%")
                    ->orWhere('programas.codigo', 'like', "%{$s}%");
            });
        }

        $programas = $query->orderBy('programas.gestion', 'desc')
            ->orderBy('programas.nombre', 'asc')
            ->paginate(10);

        $user = auth()->user();
        $programasEditables = null;
        if ($user->hasRole('Academicos') && !empty($user->responsable_id)) {
            $programasEditables = DB::table('programas_responsables')
                ->where('responsable_id', $user->responsable_id)
                ->pluck('programa_id')
                ->toArray();
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.programas.partials.table', compact('programas', 'programasEditables'))->render(),
                'pagination' => (string) $programas->links('admin.programas.partials.pagination'),
                'page' => $programas->currentPage(),
                'total' => $programas->total(),
            ]);
        }

        return redirect()->route('admin.programas.index');
    }

    public function crearHorarios(Request $request, $programaId)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|integer|exists:modulos,id',
            'horarios' => 'required|array|min:1',
            'horarios.*.fecha_desarrollo' => 'required|date',
            'horarios.*.hora_inicio' => 'required',
            'horarios.*.hora_fin' => 'required',
        ]);

        $modulo = DB::table('modulos')
            ->where('id', $validated['modulo_id'])
            ->where('programa_id', $programaId)
            ->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no pertenece al programa'], 404);
        }

        // Determinar el asignado_id por defecto: primer responsable del programa,
        // si no hay responsable o el id no existe en asignados, usar id 6.
        $asignadoDefault = DB::table('programas_responsables')
            ->where('programa_id', $programaId)
            ->value('responsable_id');

        if (!$asignadoDefault || !DB::table('asignados')->where('id', $asignadoDefault)->exists()) {
            $asignadoDefault = 6;
        }

        $rows = [];
        foreach ($validated['horarios'] as $h) {
            $rows[] = [
                'modulo_id' => $validated['modulo_id'],
                'fecha_desarrollo' => $h['fecha_desarrollo'],
                'hora_inicio' => $h['hora_inicio'],
                'hora_fin' => $h['hora_fin'],
                'asignado_id' => $asignadoDefault,
                'estado' => 'Confirmado',
                'observaciones' => null,
                'inicio' => 0,
                'cuenta_id' => 2,
                'reprogramar_id' => 0,
            ];
        }
        DB::table('horarios')->insert($rows);

        return response()->json([
            'success' => true,
            'message' => count($rows) . ' sesiones registradas correctamente',
            'cantidad' => count($rows),
        ]);
    }

    public function toggleInscripciones($programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $nuevoEstado = $programa->inscripciones_habilitadas ? 0 : 1;
        DB::table('programas')->where('id', $programaId)->update([
            'inscripciones_habilitadas' => $nuevoEstado,
        ]);

        return response()->json([
            'success' => true,
            'inscripciones_habilitadas' => $nuevoEstado,
            'message' => $nuevoEstado ? 'Inscripciones habilitadas' : 'Inscripciones deshabilitadas',
        ]);
    }

    public function crearModulos(Request $request, $programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombres' => 'required|array|min:1',
            'nombres.*' => 'required|string|max:255',
        ]);

        $nombres = array_values(array_filter(array_map('trim', $validated['nombres']), fn($n) => $n !== ''));
        if (empty($nombres)) {
            return response()->json(['success' => false, 'message' => 'Debe proporcionar al menos un nombre de módulo'], 422);
        }

        $rows = array_map(fn($nombre) => [
            'programa_id' => $programaId,
            'docente_id' => null,
            'nombre' => $nombre,
            'desarrollo' => 'No Inicio',
            'proceso_pago' => 'Procesado no iniciado',
            'factura' => 0,
            'nacional_confirma' => 0,
            'pago_docente' => 0,
            'iva' => 0,
            'it' => 0,
            'total_descuento' => 0,
            'total_pagado' => 0,
            'se_pago' => 0,
            'notas_registradas' => 0,
            'generado_nivelacion' => 0,
        ], $nombres);

        DB::table('modulos')->insert($rows);

        return response()->json([
            'success' => true,
            'message' => count($rows) . ' módulo' . (count($rows) > 1 ? 's creados' : ' creado') . ' correctamente',
            'cantidad' => count($rows),
        ]);
    }

    public function eliminarInscripcion($programaId, $inscripcionId)
    {
        $ins = DB::table('inscripciones')
            ->where('id', $inscripcionId)
            ->where('programa_id', $programaId)
            ->first();

        if (!$ins) {
            return response()->json(['success' => false, 'message' => 'Inscripción no encontrada en este programa.'], 404);
        }

        DB::beginTransaction();
        try {
            // Borrar modulos_participantes del participante en los módulos de este programa
            $moduloIds = DB::table('modulos')->where('programa_id', $programaId)->pluck('id');
            $mpDeleted = DB::table('modulos_participantes')
                ->where('participante_id', $ins->participante_id)
                ->whereIn('modulo_id', $moduloIds)
                ->delete();

            DB::table('inscripciones')->where('id', $inscripcionId)->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inscripción eliminada correctamente',
            'modulos_borrados' => $mpDeleted,
        ]);
    }

    public function updateInscripcion(Request $request, $programaId, $inscripcionId)
    {
        $ins = DB::table('inscripciones')
            ->where('id', $inscripcionId)
            ->where('programa_id', $programaId)
            ->first();

        if (!$ins) {
            return response()->json(['success' => false, 'message' => 'Inscripción no encontrada.'], 404);
        }

        $validated = $request->validate([
            'asesor_id' => 'nullable|integer|exists:asesores,id',
        ]);

        $asesorId = !empty($validated['asesor_id']) ? (int) $validated['asesor_id'] : null;

        DB::table('inscripciones')
            ->where('id', $inscripcionId)
            ->update(['asesor_id' => $asesorId]);

        return response()->json([
            'success' => true,
            'message' => 'Asesor actualizado correctamente.',
        ]);
    }

    public function toggleBaja(Request $request, $programaId, $inscripcionId)
    {
        $ins = DB::table('inscripciones')
            ->where('id', $inscripcionId)
            ->where('programa_id', $programaId)
            ->first();

        if (!$ins) {
            return response()->json(['success' => false, 'message' => 'Inscripción no encontrada.'], 404);
        }

        $validated = $request->validate([
            'tipo' => 'required|in:temprana,operativa,academica',
            'accion' => 'required|in:baja,alta',
            'motivo' => 'nullable|string|max:500',
            'baja_academica' => 'nullable',
            'baja_contable' => 'nullable',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $tipo = $validated['tipo'];
        $accion = $validated['accion'];
        $bajaAcademica = $request->boolean('baja_academica');
        $bajaContable = $request->boolean('baja_contable');

        DB::beginTransaction();
        try {
            if ($accion === 'alta') {
                $update = [
                    'baja_operativa' => 0,
                    'baja_academica' => 0,
                    'baja_contable' => 0,
                ];
                if ($tipo === 'temprana') {
                    $update['estado'] = 1;
                }
                DB::table('inscripciones')
                    ->where('id', $inscripcionId)
                    ->update($update);
            } elseif ($tipo === 'temprana') {
                DB::table('inscripciones')
                    ->where('id', $inscripcionId)
                    ->update(['estado' => 0]);
            } elseif ($tipo === 'academica') {
                DB::table('inscripciones')
                    ->where('id', $inscripcionId)
                    ->update(['baja_academica' => 1]);
            } else {
                $update = ['baja_operativa' => 1];
                if ($bajaAcademica) {
                    $update['baja_academica'] = 1;
                }
                if ($bajaContable) {
                    $update['baja_contable'] = 1;
                }
                DB::table('inscripciones')
                    ->where('id', $inscripcionId)
                    ->update($update);
            }

            $archivoPath = null;
            $archivoOriginal = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $folder = 'bajas/' . $programaId;
                $filename = 'baja_' . $inscripcionId . '_' . time() . '.' . $file->extension();
                $archivoPath = $file->storeAs($folder, $filename, 'public');
                $archivoOriginal = $file->getClientOriginalName();
            }

            DB::table('baja_historial')->insert([
                'inscripcion_id' => $inscripcionId,
                'tipo' => $tipo,
                'accion' => $accion,
                'motivo' => $validated['motivo'] ?? null,
                'baja_academica' => ($tipo === 'academica' || $bajaAcademica) ? 1 : 0,
                'baja_contable' => $bajaContable ? 1 : 0,
                'archivo_path' => $archivoPath,
                'archivo_original' => $archivoOriginal,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al registrar la baja: ' . $e->getMessage()], 500);
        }

        $mensaje = $accion === 'alta'
            ? 'Se revocó la baja correctamente.'
            : "Baja {$tipo} registrada correctamente.";

        return response()->json(['success' => true, 'message' => $mensaje]);
    }

    public function bajaHistorial($programaId, $inscripcionId)
    {
        $historial = DB::table('baja_historial')
            ->join('users', 'baja_historial.user_id', '=', 'users.id')
            ->where('baja_historial.inscripcion_id', $inscripcionId)
            ->orderBy('baja_historial.created_at', 'desc')
            ->select('baja_historial.*', 'users.fullname as user_nombre')
            ->get();

        return response()->json(['success' => true, 'historial' => $historial]);
    }

    public function bajaPdf($programaId, $inscripcionId)
    {
        $inscripcion = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('asesores', 'inscripciones.asesor_id', '=', 'asesores.id')
            ->where('inscripciones.id', $inscripcionId)
            ->where('inscripciones.programa_id', $programaId)
            ->select(
                'inscripciones.*',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.sexo',
                'participantes.celular',
                'participantes.correo',
                'participantes.departamento',
                'programas.nombre as programa_nombre',
                'programas.codigo',
                'programas.gestion',
                'programas.modulo_cero',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre',
                'asesores.nombres as asesor_nombres',
                'asesores.apellidos as asesor_apellidos'
            )
            ->first();

        if (!$inscripcion) {
            abort(404, 'Inscripción no encontrada');
        }

        $historial = DB::table('baja_historial')
            ->join('users', 'baja_historial.user_id', '=', 'users.id')
            ->where('baja_historial.inscripcion_id', $inscripcionId)
            ->orderBy('baja_historial.created_at', 'desc')
            ->select('baja_historial.*', 'users.fullname as user_nombre')
            ->get();

        $modulos = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.programa_id', $programaId)
            ->select(
                'modulos.*',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos'
            )
            ->orderBy('modulos.id')
            ->get();

        $moduloIds = $modulos->pluck('id');

        $notas = DB::table('modulos_participantes')
            ->where('modulos_participantes.participante_id', $inscripcion->participante_id)
            ->whereIn('modulos_participantes.modulo_id', $moduloIds)
            ->select('modulo_id', 'nota_modulo', 'nota_nivelacion')
            ->get()
            ->keyBy('modulo_id');

        $cuotas = DB::table('modulos_participantes as mp')
            ->where('mp.participante_id', $inscripcion->participante_id)
            ->whereIn('mp.modulo_id', $moduloIds)
            ->select('mp.id', 'mp.tipo', 'mp.total_pagado', 'mp.deuda', 'mp.fecha_pago', 'mp.descuento')
            ->orderByRaw("FIELD(mp.tipo, 'Matricula', 'Colegiatura')")
            ->orderBy('mp.modulo_id')
            ->get()
            ->map(function ($c) {
                $pagado = max(0, (float) $c->total_pagado);
                $total = max(0, (float) $c->deuda);
                return (object) [
                    'id'         => $c->id,
                    'tipo'       => $c->tipo,
                    'total'      => $total,
                    'pagado'     => $pagado,
                    'saldo'      => max(0, $total - $pagado),
                    'descuento'  => (float) ($c->descuento ?? 0),
                    'fecha_pago' => $c->fecha_pago ?? null,
                ];
            });

        $tituloPrograma = \Illuminate\Support\Str::startsWith(mb_strtolower(trim($inscripcion->programa_nombre)), mb_strtolower(trim($inscripcion->tipo_nombre)))
            ? $inscripcion->programa_nombre
            : $inscripcion->tipo_nombre . ' ' . $inscripcion->programa_nombre;

        $responsablesPrograma = DB::table('programas_responsables')
            ->join('responsables', 'programas_responsables.responsable_id', '=', 'responsables.id')
            ->where('programas_responsables.programa_id', $programaId)
            ->orderBy('responsables.apellidos')
            ->select('responsables.*')
            ->get();

        $pdf = Pdf::loadView('admin.programas.baja-pdf', compact(
            'inscripcion', 'historial', 'modulos', 'notas', 'cuotas', 'tituloPrograma', 'responsablesPrograma'
        ));

        $nombreArchivo = 'baja_' . $inscripcion->apellidos . '_' . $inscripcion->nombres . '_' . $inscripcionId . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    public function bajaArchivo($programaId, $inscripcionId)
    {
        $historial = DB::table('baja_historial')
            ->where('inscripcion_id', $inscripcionId)
            ->whereNotNull('archivo_path')
            ->orderByDesc('created_at')
            ->first();

        if (!$historial || !$historial->archivo_path) {
            abort(404, 'No se encontró un documento de respaldo para esta baja.');
        }

        $path = \Storage::disk('public')->path($historial->archivo_path);

        if (!file_exists($path)) {
            abort(404, 'El documento de respaldo ya no existe en el servidor.');
        }

        return response()->download($path, $historial->archivo_original ?: basename($path));
    }

    public function buscarAsesores(Request $request)
    {
        $q = trim($request->get('q', ''));
        $base = DB::table('asesores')
            ->select('id', 'ci', 'nombres', 'apellidos', 'celular', 'correo')
            ->where(function ($w) {
                // Solo asesores activos (nullable o 'Habilitado'/1)
                $w->whereNull('estado')
                    ->orWhereIn('estado', ['Habilitado', '1', 1]);
            });

        if (mb_strlen($q) >= 1) {
            $base->where(function ($w) use ($q) {
                $w->where('ci', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(apellidos, ' ', nombres) LIKE ?", ["%{$q}%"]);
            });
        }

        $asesores = $base->orderBy('apellidos')->orderBy('nombres')->limit(25)->get();
        return response()->json(['asesores' => $asesores]);
    }

    public function buscarParticipante(Request $request, $programaId)
    {
        $ci = trim($request->get('ci', ''));
        if (mb_strlen($ci) < 7 || mb_strlen($ci) > 13) {
            return response()->json(['found' => false]);
        }

        $p = DB::table('participantes')
            ->where('ci', $ci)
            ->select('id', 'ci', 'nombres', 'apellidos', 'sexo', 'celular', 'correo', 'departamento')
            ->first();

        if (!$p) {
            return response()->json(['found' => false, 'ci' => $ci]);
        }

        // ¿Ya está inscrito en este programa?
        $yaInscrito = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('participante_id', $p->id)
            ->exists();

        return response()->json([
            'found' => true,
            'participante' => $p,
            'ya_inscrito' => $yaInscrito,
        ]);
    }

    public function registrarParticipante(Request $request, $programaId)
    {
        $validated = $request->validate([
            'ci' => 'required|string|min:7|max:13|unique:participantes,ci',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'sexo' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:120',
            'departamento' => 'nullable|string|max:80',
        ]);

        $id = DB::table('participantes')->insertGetId($validated);
        $p = DB::table('participantes')->where('id', $id)
            ->select('id', 'ci', 'nombres', 'apellidos', 'sexo', 'celular', 'correo', 'departamento')
            ->first();

        return response()->json([
            'success' => true,
            'participante' => $p,
        ]);
    }

    public function inscribirParticipante(Request $request, $programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $validated = $request->validate([
            'participante_id' => 'required|integer|exists:participantes,id',
            'asesor_id' => 'nullable|integer|exists:asesores,id',
            'descuento_matricula' => 'nullable|numeric|min:0',
            'descuento_colegiatura' => 'nullable|numeric|min:0',
            'contado' => 'nullable|boolean',
            'es_traspaso' => 'nullable|boolean',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            'deudas_seleccionadas' => 'nullable|json',
        ]);

        $participanteId = (int) $validated['participante_id'];
        $asesorId = !empty($validated['asesor_id']) ? (int) $validated['asesor_id'] : null;
        $descMat = (float) ($validated['descuento_matricula'] ?? 0);
        $descCol = (float) ($validated['descuento_colegiatura'] ?? 0);
        $contado = !empty($validated['contado']) ? 1 : 0;
        $esTraspaso = !empty($validated['es_traspaso']) ? 1 : 0;
        $subirComprobante = $request->hasFile('comprobante');

        $matricula = (float) ($programa->matricula ?? 0);
        $colegiatura = (float) ($programa->colegiatura ?? 0);

        if ($descMat > $matricula) {
            return response()->json(['success' => false, 'message' => 'El descuento de matrícula no puede ser mayor a la matrícula.'], 422);
        }
        if ($descCol > $colegiatura) {
            return response()->json(['success' => false, 'message' => 'El descuento de colegiatura no puede ser mayor a la colegiatura.'], 422);
        }

        $exists = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('participante_id', $participanteId)
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'El participante ya está inscrito en este programa.'], 422);
        }

        $deudaMatricula = max(0, $matricula - $descMat);
        $deudaColegiatura = max(0, $colegiatura - $descCol);

        DB::beginTransaction();
        try {
            $inscripcionId = DB::table('inscripciones')->insertGetId([
                'programa_id' => $programaId,
                'participante_id' => $participanteId,
                'asesor_id' => $asesorId,
                'matricula' => $matricula,
                'monto_programa' => $colegiatura,
                'descuento_matricula' => $descMat,
                'descuento_colegiatura' => $descCol,
                'deuda_matricula' => $deudaMatricula,
                'deuda' => $deudaColegiatura,
                'fecha_inscripcion' => date('Y-m-d'),
                'estado' => 1,
                'contado' => $contado,
                'es_traspaso' => $esTraspaso,
            ]);

            $modulos = DB::table('modulos')->where('programa_id', $programaId)->orderBy('id')->get(['id']);
            $n = $modulos->count();
            $rows = [];
            $cuotasColegiatura = 0;

            if ($n > 0) {
                $modulosArr = $modulos->values()->all();

                $rows[] = [
                    'modulo_id' => $modulosArr[0]->id,
                    'participante_id' => $participanteId,
                    'tipo' => 'Matricula',
                    'deuda' => $deudaMatricula,
                    'descuento' => $descMat,
                    'total_pagado' => 0,
                    'estado' => 1,
                ];

                $nModulosPrograma = (int) ($programa->n_modulos ?? 0);
                $totalCuotasCol = $nModulosPrograma > 0 ? $nModulosPrograma : max(1, $n - 1);

                if ($contado === 1 && $totalCuotasCol >= 2) {
                    $cuotaEntera = (int) floor($deudaColegiatura / 2);
                    $segundaCuota = round($deudaColegiatura - $cuotaEntera, 2);
                    $colOffset = ($n >= 3) ? 1 : 0;
                    $maxRows = ($n >= 3) ? 2 : $totalCuotasCol;
                    $cuotasColegiatura = $maxRows;

                    for ($i = 0; $i < $maxRows; $i++) {
                        $modIdx = ($i + $colOffset) % $n;
                        $monto = $i === 0 ? (float) $cuotaEntera : (float) $segundaCuota;
                        $rows[] = [
                            'modulo_id' => $modulosArr[$modIdx]->id,
                            'participante_id' => $participanteId,
                            'tipo' => 'Colegiatura',
                            'deuda' => max(0, $monto),
                            'descuento' => 0,
                            'total_pagado' => 0,
                            'estado' => 1,
                        ];
                    }
                } elseif ($contado === 1 && $totalCuotasCol === 1) {
                    $modDest = ($n >= 2) ? 1 : 0;
                    $rows[] = [
                        'modulo_id' => $modulosArr[$modDest]->id,
                        'participante_id' => $participanteId,
                        'tipo' => 'Colegiatura',
                        'deuda' => max(0, $deudaColegiatura),
                        'descuento' => 0,
                        'total_pagado' => 0,
                        'estado' => 1,
                    ];
                    $cuotasColegiatura = 1;
                } elseif ($totalCuotasCol > 0) {
                    $cuotasColegiatura = $totalCuotasCol;
                    $cuotaEntera = (int) floor($deudaColegiatura / $totalCuotasCol);
                    for ($i = 0; $i < $totalCuotasCol; $i++) {
                        $modIdx = $i % $n;
                        $esUltima = ($i === $totalCuotasCol - 1);
                        $monto = $esUltima
                            ? round($deudaColegiatura - ($cuotaEntera * ($totalCuotasCol - 1)), 2)
                            : (float) $cuotaEntera;
                        $rows[] = [
                            'modulo_id' => $modulosArr[$modIdx]->id,
                            'participante_id' => $participanteId,
                            'tipo' => 'Colegiatura',
                            'deuda' => max(0, $monto),
                            'descuento' => 0,
                            'total_pagado' => 0,
                            'estado' => 1,
                        ];
                    }
                }
            }

            if (!empty($rows) && Schema::hasColumn('modulos_participantes', 'fecha_pago')) {
                $plan = $contado === 1 ? 'contado' : 'regular';
                $fechasPorTipo = ['Matricula' => [], 'Colegiatura' => []];

                if (Schema::hasTable('cuotas_fechas_programas')) {
                    $fechas = DB::table('cuotas_fechas_programas')
                        ->where('programa_id', $programaId)
                        ->where('plan', $plan)
                        ->orderBy('tipo')->orderBy('numero')
                        ->get(['tipo', 'numero', 'fecha_pago']);

                    foreach ($fechas as $f) {
                        if (!isset($fechasPorTipo[$f->tipo])) continue;
                        $fechasPorTipo[$f->tipo][] = $f->fecha_pago;
                    }
                }

                $idxPorTipo = ['Matricula' => 0, 'Colegiatura' => 0];
                foreach ($rows as $k => $r) {
                    $tipo = $r['tipo'];
                    $i = $idxPorTipo[$tipo] ?? 0;
                    $rows[$k]['fecha_pago'] = $fechasPorTipo[$tipo][$i] ?? null;
                    $idxPorTipo[$tipo] = $i + 1;
                }
            }

            $modulosParticipantesIds = [];
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $modulosParticipantesIds[] = DB::table('modulos_participantes')->insertGetId($row);
                }
            }

            // --- Comprobante de pago ---
            $comprobanteCreado = false;
            if ($subirComprobante) {
                $file = $request->file('comprobante');
                $gestion = $programa->gestion ?? date('Y');
                $folder = "comprobantes_pago/{$gestion}/{$programaId}";
                $filename = "pago_{$participanteId}_{$inscripcionId}_" . time() . '.' . $file->extension();
                $path = $file->storeAs($folder, $filename, 'public');

                $deudasData = $request->input('deudas_seleccionadas');
                $deudasArr = $deudasData ? json_decode($deudasData, true) : [];
                $selectedMpIds = [];
                foreach ($deudasArr as $sel) {
                    $idx = (int) ($sel['index'] ?? -1);
                    if (isset($modulosParticipantesIds[$idx])) {
                        $selectedMpIds[] = $modulosParticipantesIds[$idx];
                    }
                }

                DB::table('comprobantes_pago')->insert([
                    'programa_id' => $programaId,
                    'participante_id' => $participanteId,
                    'inscripcion_id' => $inscripcionId,
                    'asesor_id' => $asesorId,
                    'monto' => (float) ($request->input('comprobante_monto') ?: 0),
                    'archivo_path' => $path,
                    'archivo_original' => $file->getClientOriginalName(),
                    'archivo_mime' => $file->getMimeType(),
                    'deudas_seleccionadas' => json_encode($selectedMpIds),
                    'estado' => 'pendiente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $comprobanteCreado = true;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al inscribir: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Participante inscrito correctamente' . ($comprobanteCreado ? ' con comprobante de pago adjunto.' : ''),
            'inscripcion_id' => $inscripcionId ?? null,
            'matricula_creada' => 1,
            'cuotas_colegiatura' => $cuotasColegiatura,
            'comprobante_creado' => $comprobanteCreado,
        ]);
    }

    public function rebalancearColegiaturas($programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $nModulosPrograma = (int) ($programa->n_modulos ?? 0);
        if ($nModulosPrograma <= 0) {
            return response()->json(['success' => false, 'message' => 'Este programa no tiene n_modulos configurado. Establezca la cantidad deseada en la edición del programa.'], 422);
        }

        $modulos = DB::table('modulos')->where('programa_id', $programaId)->orderBy('id')->get(['id']);
        $nModulos = $modulos->count();
        if ($nModulos <= 0) {
            return response()->json(['success' => false, 'message' => 'El programa no tiene módulos registrados'], 422);
        }
        $modulosArr = $modulos->values()->all();

        $inscripciones = DB::table('inscripciones')->where('programa_id', $programaId)->get();
        $stats = ['procesados' => 0, 'sin_pagos' => 0, 'con_pagos' => 0, 'saltados' => 0, 'errores' => 0];

        DB::beginTransaction();
        try {
            foreach ($inscripciones as $ins) {
                $participanteId = $ins->participante_id;

                $cuotasActuales = DB::table('modulos_participantes')
                    ->where('participante_id', $participanteId)
                    ->whereIn('modulo_id', $modulos->pluck('id'))
                    ->get();

                $colegiaturaCount = $cuotasActuales->where('tipo', 'Colegiatura')->count();
                $matriculaCount = $cuotasActuales->where('tipo', 'Matricula')->count();

                if ($colegiaturaCount === $nModulosPrograma && $matriculaCount === 1) {
                    $stats['saltados']++;
                    continue;
                }

                $idsActuales = $cuotasActuales->pluck('id')->toArray();
                $tienePagos = !empty($idsActuales) && DB::table('deudas')
                    ->whereIn('modulo_participante_id', $idsActuales)
                    ->exists();

                if ($tienePagos) {
                    $stats['con_pagos']++;
                    continue;
                }

                if (!empty($idsActuales)) {
                    DB::table('modulos_participantes')->whereIn('id', $idsActuales)->delete();
                }

                $deudaMatricula = max(0, (float)($ins->matricula ?? 0) - (float)($ins->descuento_matricula ?? 0));
                $deudaColegiatura = max(0, (float)($ins->monto_programa ?? 0) - (float)($ins->descuento_colegiatura ?? 0));
                $contado = (int)($ins->contado ?? 0);

                $rows = [];

                $rows[] = [
                    'modulo_id' => $modulosArr[0]->id,
                    'participante_id' => $participanteId,
                    'tipo' => 'Matricula',
                    'deuda' => $deudaMatricula,
                    'descuento' => (float)($ins->descuento_matricula ?? 0),
                    'total_pagado' => 0,
                    'estado' => 1,
                ];

                if ($contado === 1 && $nModulosPrograma >= 2) {
                    $cuotaEntera = (int) floor($deudaColegiatura / 2);
                    $segundaCuota = round($deudaColegiatura - $cuotaEntera, 2);
                    $colOffset = ($nModulos >= 3) ? 1 : 0;
                    $maxRows = ($nModulos >= 3) ? 2 : $nModulosPrograma;
                    for ($i = 0; $i < $maxRows; $i++) {
                        $modIdx = ($i + $colOffset) % $nModulos;
                        $monto = $i === 0 ? (float)$cuotaEntera : (float)$segundaCuota;
                        $rows[] = [
                            'modulo_id' => $modulosArr[$modIdx]->id,
                            'participante_id' => $participanteId,
                            'tipo' => 'Colegiatura',
                            'deuda' => max(0, $monto),
                            'descuento' => 0,
                            'total_pagado' => 0,
                            'estado' => 1,
                        ];
                    }
                } elseif ($contado === 1 && $nModulosPrograma === 1) {
                    $modDest = ($nModulos >= 2) ? 1 : 0;
                    $rows[] = [
                        'modulo_id' => $modulosArr[$modDest]->id,
                        'participante_id' => $participanteId,
                        'tipo' => 'Colegiatura',
                        'deuda' => max(0, $deudaColegiatura),
                        'descuento' => 0,
                        'total_pagado' => 0,
                        'estado' => 1,
                    ];
                } else {
                    $cuotaEntera = (int) floor($deudaColegiatura / $nModulosPrograma);
                    for ($i = 0; $i < $nModulosPrograma; $i++) {
                        $modIdx = $i % $nModulos;
                        $esUltima = ($i === $nModulosPrograma - 1);
                        $monto = $esUltima
                            ? round($deudaColegiatura - ($cuotaEntera * ($nModulosPrograma - 1)), 2)
                            : (float) $cuotaEntera;
                        $rows[] = [
                            'modulo_id' => $modulosArr[$modIdx]->id,
                            'participante_id' => $participanteId,
                            'tipo' => 'Colegiatura',
                            'deuda' => max(0, $monto),
                            'descuento' => 0,
                            'total_pagado' => 0,
                            'estado' => 1,
                        ];
                    }
                }

                DB::table('modulos_participantes')->insert($rows);
                $stats['sin_pagos']++;
                $stats['procesados']++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error durante el rebalanceo: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Rebalanceo completado. {$stats['sin_pagos']} participante(s) corregido(s), {$stats['con_pagos']} con pagos (omitidos), {$stats['saltados']} ya estaban correctos.",
            'stats' => $stats,
        ]);
    }

    public function responsablesData($id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $todos = DB::table('responsables')
            ->select('id', 'ci', 'nombres', 'apellidos', 'correo', 'color')
            ->orderBy('apellidos')->orderBy('nombres')->get();

        $asignados = DB::table('programas_responsables')
            ->where('programa_id', $id)
            ->pluck('responsable_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'programa' => [
                'id' => $programa->id,
                'nombre' => $programa->nombre,
                'codigo' => $programa->codigo,
            ],
            'todos' => $todos,
            'asignados' => $asignados,
        ]);
    }

    public function asignarResponsables(Request $request, $id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $ids = array_values(array_filter(array_map('intval', (array) $request->input('responsables', []))));

        // Validar que existan
        if (!empty($ids)) {
            $existentes = DB::table('responsables')->whereIn('id', $ids)->pluck('id')->toArray();
            $ids = array_values(array_intersect($ids, $existentes));
        }

        DB::table('programas_responsables')->where('programa_id', $id)->delete();
        if (!empty($ids)) {
            $rows = array_map(fn($rid) => ['programa_id' => $id, 'responsable_id' => $rid], $ids);
            DB::table('programas_responsables')->insert($rows);
        }

        // Determinar el nuevo asignado_id para los horarios del programa
        // (primer responsable válido en asignados; fallback a 6)
        $nuevoAsignadoId = null;
        foreach ($ids as $rid) {
            if (DB::table('asignados')->where('id', $rid)->exists()) {
                $nuevoAsignadoId = $rid;
                break;
            }
        }
        if (!$nuevoAsignadoId) {
            $nuevoAsignadoId = 6;
        }

        // Actualizar asignado_id de todos los horarios de los módulos del programa
        $moduloIds = DB::table('modulos')->where('programa_id', $id)->pluck('id');
        $horariosActualizados = 0;
        if ($moduloIds->isNotEmpty()) {
            $horariosActualizados = DB::table('horarios')
                ->whereIn('modulo_id', $moduloIds)
                ->update(['asignado_id' => $nuevoAsignadoId]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Responsables actualizados correctamente'
                . ($horariosActualizados > 0 ? ' · ' . $horariosActualizados . ' horario(s) reasignado(s)' : ''),
            'total' => count($ids),
            'horarios_actualizados' => $horariosActualizados,
            'nuevo_asignado_id' => $nuevoAsignadoId,
        ]);
    }

    public function data($id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();

        $areas = DB::table('areas_programas')->where('programa_id', $id)->get();
        $responsables = DB::table('programas_responsables')->where('programa_id', $id)->pluck('responsable_id')->toArray();

        return response()->json([
            'programa' => $programa,
            'area_id' => $areas->first()?->area_id,
            'sede_id' => $areas->first()?->sede_id,
            'responsables' => $responsables,
        ]);
    }

    public function costosUniversidadData($id)
    {
        $programa = DB::table('programas')->where('id', $id)->first(['id', 'nombre', 'codigo', 'costo_pago_inscripcion_universidad', 'costo_pago_matricula_universidad', 'costo_pago_colegiatura_universidad', 'costo_pago_certificacion_universidad']);
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }
        return response()->json(['success' => true, 'programa' => $programa]);
    }

    public function guardarCostosUniversidad(Request $request, $id)
    {
        $programa = DB::table('programas')->where('id', $id)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $validated = $request->validate([
            'costo_pago_inscripcion_universidad' => 'nullable|numeric|min:0',
            'costo_pago_matricula_universidad' => 'nullable|numeric|min:0',
            'costo_pago_colegiatura_universidad' => 'nullable|numeric|min:0',
            'costo_pago_certificacion_universidad' => 'nullable|numeric|min:0',
        ]);

        DB::table('programas')->where('id', $id)->update([
            'costo_pago_inscripcion_universidad' => $validated['costo_pago_inscripcion_universidad'] ?? 0,
            'costo_pago_matricula_universidad' => $validated['costo_pago_matricula_universidad'] ?? 0,
            'costo_pago_colegiatura_universidad' => $validated['costo_pago_colegiatura_universidad'] ?? 0,
            'costo_pago_certificacion_universidad' => $validated['costo_pago_certificacion_universidad'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Costos de pago a la universidad guardados correctamente',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:40',
            'codigo_academico' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'gestion' => 'required|integer|min:2000|max:2100',
            'tipo_id' => 'required|exists:tipos,id',
            'estado_id' => 'required|exists:estados,id',
            'cantidad_sesiones' => 'required|integer|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_conclusion' => 'nullable|date|after_or_equal:fecha_inicio',
            'matricula' => 'nullable|numeric|min:0',
            'colegiatura' => 'nullable|numeric|min:0',
            'costo_certificacion' => 'nullable|numeric|min:0',
            'costo_certificacion_personal' => 'nullable|numeric|min:0',
            'costo_pago_inscripcion_universidad' => 'nullable|numeric|min:0',
            'costo_pago_matricula_universidad' => 'nullable|numeric|min:0',
            'costo_pago_colegiatura_universidad' => 'nullable|numeric|min:0',
            'costo_pago_certificacion_universidad' => 'nullable|numeric|min:0',
            'moodle_link' => 'nullable|string|max:500',
            'ceub' => 'nullable|boolean',
            'inscripciones_habilitadas' => 'nullable|boolean',
            'area_id' => 'nullable|exists:areas,id',
            'sede_id' => 'nullable|exists:sedes,id',
            'responsables' => 'nullable|array',
            'responsables.*' => 'exists:responsables,id',
            'n_modulos' => 'nullable|integer|min:0',
        ]);

        $id = DB::table('programas')->insertGetId([
            'codigo' => $validated['codigo'],
            'codigo_academico' => $validated['codigo_academico'] ?? null,
            'nombre' => $validated['nombre'],
            'gestion' => $validated['gestion'],
            'tipo_id' => $validated['tipo_id'],
            'estado_id' => $validated['estado_id'],
            'cantidad_sesiones' => $validated['cantidad_sesiones'],
            'fecha_inicio' => $validated['fecha_inicio'] ?? null,
            'fecha_conclusion' => $validated['fecha_conclusion'] ?? null,
            'matricula' => $validated['matricula'] ?? 0,
            'colegiatura' => $validated['colegiatura'] ?? 0,
            'costo_certificacion' => $validated['costo_certificacion'] ?? 0,
            'costo_certificacion_personal' => $validated['costo_certificacion_personal'] ?? 0,
            'costo_pago_inscripcion_universidad' => $validated['costo_pago_inscripcion_universidad'] ?? 0,
            'costo_pago_matricula_universidad' => $validated['costo_pago_matricula_universidad'] ?? 0,
            'costo_pago_colegiatura_universidad' => $validated['costo_pago_colegiatura_universidad'] ?? 0,
            'costo_pago_certificacion_universidad' => $validated['costo_pago_certificacion_universidad'] ?? 0,
            'moodle_link' => $validated['moodle_link'] ?? null,
            'ceub' => $request->boolean('ceub'),
            'desarrollo' => 'En Desarrollo',
            'inscripciones_habilitadas' => $request->boolean('inscripciones_habilitadas'),
            'n_modulos' => $validated['n_modulos'] ?? 0,
            'inscritos_portal_esam' => 0,
            'registrados_universidad' => 0,
            'inscritos_universidad' => 0,
            'vigentes' => 0,
            'contado' => 0,
            'abandonos' => 0,
            'congelados' => 0,
            'cambio_programa' => 0,
            'retirados' => 0,
            'suspendidos' => 0,
            'ingresos_matriculas' => 0,
            'costo_matricula' => 0,
            'costo_programa' => 0,
            'total_cobrado' => 0,
            'por_cobrar' => 0,
            'costo_matricula_sin_descuentos' => 0,
            'costo_programa_sin_descuentos' => 0,
            'ingresos_colegiaturas' => 0,
            'perdida_colegiaturas' => 0,
            'tiene_presupuesto_campana' => 0,
            'presupuesto_campana' => 0,
            'cronograma_generado' => 0,
            'modulo_cero' => 1,
            'asesor_id' => null,
        ]);

        if ($request->filled('area_id')) {
            DB::table('areas_programas')->insert([
                'area_id' => $validated['area_id'],
                'programa_id' => $id,
                'sede_id' => $validated['sede_id'] ?? 1,
            ]);
        }

        if ($request->has('responsables')) {
            foreach ($validated['responsables'] as $responsableId) {
                DB::table('programas_responsables')->insert([
                    'programa_id' => $id,
                    'responsable_id' => $responsableId,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Programa creado correctamente']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:40',
            'codigo_academico' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'gestion' => 'required|integer|min:2000|max:2100',
            'tipo_id' => 'required|exists:tipos,id',
            'estado_id' => 'required|exists:estados,id',
            'cantidad_sesiones' => 'required|integer|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_conclusion' => 'nullable|date|after_or_equal:fecha_inicio',
            'matricula' => 'nullable|numeric|min:0',
            'colegiatura' => 'nullable|numeric|min:0',
            'costo_certificacion' => 'nullable|numeric|min:0',
            'costo_certificacion_personal' => 'nullable|numeric|min:0',
            'costo_pago_inscripcion_universidad' => 'nullable|numeric|min:0',
            'costo_pago_matricula_universidad' => 'nullable|numeric|min:0',
            'costo_pago_colegiatura_universidad' => 'nullable|numeric|min:0',
            'costo_pago_certificacion_universidad' => 'nullable|numeric|min:0',
            'moodle_link' => 'nullable|string|max:500',
            'ceub' => 'nullable|boolean',
            'inscripciones_habilitadas' => 'nullable|boolean',
            'area_id' => 'nullable|exists:areas,id',
            'sede_id' => 'nullable|exists:sedes,id',
            'responsables' => 'nullable|array',
            'responsables.*' => 'exists:responsables,id',
            'n_modulos' => 'nullable|integer|min:0',
        ]);

        DB::table('programas')->where('id', $id)->update([
            'codigo' => $validated['codigo'],
            'codigo_academico' => $validated['codigo_academico'] ?? null,
            'nombre' => $validated['nombre'],
            'gestion' => $validated['gestion'],
            'tipo_id' => $validated['tipo_id'],
            'estado_id' => $validated['estado_id'],
            'cantidad_sesiones' => $validated['cantidad_sesiones'],
            'fecha_inicio' => $validated['fecha_inicio'] ?? null,
            'fecha_conclusion' => $validated['fecha_conclusion'] ?? null,
            'matricula' => $validated['matricula'] ?? 0,
            'colegiatura' => $validated['colegiatura'] ?? 0,
            'costo_certificacion' => $validated['costo_certificacion'] ?? 0,
            'costo_certificacion_personal' => $validated['costo_certificacion_personal'] ?? 0,
            'costo_pago_inscripcion_universidad' => $validated['costo_pago_inscripcion_universidad'] ?? 0,
            'costo_pago_matricula_universidad' => $validated['costo_pago_matricula_universidad'] ?? 0,
            'costo_pago_colegiatura_universidad' => $validated['costo_pago_colegiatura_universidad'] ?? 0,
            'costo_pago_certificacion_universidad' => $validated['costo_pago_certificacion_universidad'] ?? 0,
            'moodle_link' => $validated['moodle_link'] ?? null,
            'ceub' => $request->boolean('ceub'),
            'desarrollo' => 'En Desarrollo',
            'inscripciones_habilitadas' => $request->boolean('inscripciones_habilitadas'),
            'n_modulos' => $validated['n_modulos'] ?? 0,
        ]);

        DB::table('areas_programas')->where('programa_id', $id)->delete();
        if ($request->filled('area_id')) {
            DB::table('areas_programas')->insert([
                'area_id' => $validated['area_id'],
                'programa_id' => $id,
                'sede_id' => $validated['sede_id'] ?? 1,
            ]);
        }

        DB::table('programas_responsables')->where('programa_id', $id)->delete();
        if ($request->has('responsables')) {
            foreach ($validated['responsables'] as $responsableId) {
                DB::table('programas_responsables')->insert([
                    'programa_id' => $id,
                    'responsable_id' => $responsableId,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Programa actualizado correctamente']);
    }

    public function storeSesionExtra(Request $request)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'fecha_desarrollo' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'asignado_id' => 'nullable|exists:asignados,id',
            'observaciones' => 'nullable|string|max:1000',
            'estado' => 'nullable|in:Programado,Confirmado,Postergado',
            'nueva_fecha' => 'nullable|required_if:estado,Postergado|date|after_or_equal:today',
        ]);

        $estado = $validated['estado'] ?? 'Programado';
        $nuevoId = DB::table('horarios')->insertGetId([
            'modulo_id' => $validated['modulo_id'],
            'fecha_desarrollo' => $validated['fecha_desarrollo'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'asignado_id' => $validated['asignado_id'] ?? 0,
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => $estado,
            'inicio' => 0,
            'cuenta_id' => 1,
            'reprogramar_id' => 0,
        ]);

        $nuevoHorario = DB::table('horarios')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('horarios.id', $nuevoId)
            ->select(
                'horarios.*',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Sesión extra agregada correctamente',
            'nuevo_horario' => $nuevoHorario,
        ]);
    }

    public function updateHorarioEstado(Request $request)
    {
        $validated = $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'estado' => 'required|in:Confirmado,Programado,Desarrollado,Postergado',
            'nueva_fecha' => 'nullable|required_if:estado,Postergado|date|after_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $horario = DB::table('horarios')->where('id', $validated['horario_id'])->first();

        $updateData = ['estado' => $validated['estado']];
        if (isset($validated['observaciones'])) {
            $updateData['observaciones'] = $validated['observaciones'];
        }

        DB::table('horarios')->where('id', $validated['horario_id'])->update($updateData);

        $nuevoHorario = null;

        if ($validated['estado'] === 'Postergado') {
            $obsNuevo = 'Reprogramado desde sesión del ' . $horario->fecha_desarrollo;

            $nuevoId = DB::table('horarios')->insertGetId([
                'modulo_id' => $horario->modulo_id,
                'fecha_desarrollo' => $validated['nueva_fecha'],
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'asignado_id' => $horario->asignado_id,
                'observaciones' => $obsNuevo,
                'estado' => 'Programado',
                'inicio' => 0,
                'cuenta_id' => 1,
                'reprogramar_id' => $horario->id,
            ]);

            $nuevoHorario = DB::table('horarios')
                ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
                ->where('horarios.id', $nuevoId)
                ->select(
                    'horarios.*',
                    'asignados.nombres as asignado_nombres',
                    'asignados.apellidos as asignado_apellidos',
                    'asignados.hexadecimal as asignado_hex'
                )
                ->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'nuevo_horario' => $nuevoHorario,
        ]);
    }

    public function updateHorarioFecha(Request $request)
    {
        $validated = $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'fecha_desarrollo' => 'nullable|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
        ]);

        $horario = DB::table('horarios')->where('id', $validated['horario_id'])->first();

        $updateData = [];
        if ($request->filled('fecha_desarrollo')) {
            $updateData['fecha_desarrollo'] = $validated['fecha_desarrollo'];
        }
        if ($request->filled('hora_inicio')) {
            $updateData['hora_inicio'] = $validated['hora_inicio'];
        }
        if ($request->filled('hora_fin')) {
            $updateData['hora_fin'] = $validated['hora_fin'];
        }

        if (!empty($updateData)) {
            DB::table('horarios')->where('id', $validated['horario_id'])->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fecha y hora actualizadas correctamente',
        ]);
    }

    public function asistenciaStats($moduloId)
    {
        try {
            $this->ensureAsistenciasTable();

            $horarios = DB::table('horarios')
                ->where('modulo_id', $moduloId)
                ->orderBy('fecha_desarrollo')
                ->orderBy('hora_inicio')
                ->get(['id', 'fecha_desarrollo', 'hora_inicio', 'estado']);

            $horariosDes = $horarios->where('estado', 'Desarrollado')->values();
            $horarioIds = $horariosDes->pluck('id');

            $programaId = DB::table('modulos')->where('id', $moduloId)->value('programa_id');

            $participantes = DB::table('modulos_participantes as mp')
                ->join('participantes as p', 'mp.participante_id', '=', 'p.id')
                ->leftJoin('inscripciones as i', function ($j) use ($programaId) {
                    $j->on('i.participante_id', '=', 'p.id')->where('i.programa_id', '=', $programaId);
                })
                ->where('mp.modulo_id', $moduloId)
                ->where(function ($q) {
                    $q->where('mp.retirado', 0)->orWhereNull('mp.retirado');
                })
                ->select('p.id', 'p.ci', 'p.nombres', 'p.apellidos', 'i.estado', 'i.baja_operativa', 'i.baja_academica', 'i.baja_contable')
                ->orderBy('p.apellidos')
                ->orderBy('p.nombres')
                ->get()
                ->unique('id')
                ->each(function ($p) {
                    $p->baja = \App\Support\BajaInscripcion::motivo($p);
                });

            // Los dados de baja se listan, pero no entran en los cálculos.
            $activosIds = $participantes->whereNull('baja')->pluck('id')->flip();

            $asistencias = $horarioIds->isEmpty()
                ? collect()
                : DB::table('asistencias')->whereIn('horario_id', $horarioIds)->get()
                    ->filter(fn ($a) => $activosIds->has($a->participante_id))->values();

            // Distribución global
            $tipos = ['Presente' => 0, 'Ausente' => 0, 'Tardanza' => 0, 'Justificado' => 0];
            foreach ($asistencias as $a) {
                if (isset($tipos[$a->asistio])) $tipos[$a->asistio]++;
            }

            // Por sesión
            $porSesion = [];
            $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            foreach ($horariosDes as $idx => $h) {
                $f = \Carbon\Carbon::parse($h->fecha_desarrollo);
                $hAsist = $asistencias->where('horario_id', $h->id);
                $row = ['Presente' => 0, 'Ausente' => 0, 'Tardanza' => 0, 'Justificado' => 0];
                foreach ($hAsist as $a) {
                    if (isset($row[$a->asistio])) $row[$a->asistio]++;
                }
                $porSesion[] = [
                    'horario_id' => $h->id,
                    'label' => 'S' . ($idx + 1),
                    'fecha' => $f->format('d/m/Y'),
                    'dia' => $dias[$f->dayOfWeek],
                    'Presente' => $row['Presente'],
                    'Ausente' => $row['Ausente'],
                    'Tardanza' => $row['Tardanza'],
                    'Justificado' => $row['Justificado'],
                    'total' => $row['Presente'] + $row['Ausente'] + $row['Tardanza'] + $row['Justificado'],
                ];
            }

            // Por participante
            $totalSes = $horariosDes->count();
            $porParticipante = [];
            foreach ($participantes as $p) {
                if ($p->baja) {
                    $porParticipante[] = [
                        'id' => $p->id,
                        'ci' => $p->ci,
                        'nombre' => $p->apellidos . ', ' . $p->nombres,
                        'Presente' => null,
                        'Ausente' => null,
                        'Tardanza' => null,
                        'Justificado' => null,
                        'sin_registro' => null,
                        'porcentaje' => null,
                        'baja' => $p->baja,
                    ];
                    continue;
                }
                $pAsist = $asistencias->where('participante_id', $p->id);
                $cnt = ['Presente' => 0, 'Ausente' => 0, 'Tardanza' => 0, 'Justificado' => 0];
                foreach ($pAsist as $a) {
                    if (isset($cnt[$a->asistio])) $cnt[$a->asistio]++;
                }
                $registradas = array_sum($cnt);
                $efectivas = $cnt['Presente'] + $cnt['Tardanza'] + $cnt['Justificado'];
                $pct = $totalSes > 0 ? round(($efectivas / $totalSes) * 100, 1) : 0;
                $porParticipante[] = [
                    'id' => $p->id,
                    'ci' => $p->ci,
                    'nombre' => $p->apellidos . ', ' . $p->nombres,
                    'Presente' => $cnt['Presente'],
                    'Ausente' => $cnt['Ausente'],
                    'Tardanza' => $cnt['Tardanza'],
                    'Justificado' => $cnt['Justificado'],
                    'sin_registro' => max(0, $totalSes - $registradas),
                    'porcentaje' => $pct,
                    'baja' => null,
                ];
            }

            // Ordena por % desc; los dados de baja, al final.
            usort($porParticipante, function ($a, $b) {
                if (($a['baja'] !== null) !== ($b['baja'] !== null)) return $a['baja'] !== null ? 1 : -1;
                return ($b['porcentaje'] ?? 0) <=> ($a['porcentaje'] ?? 0);
            });

            $totalRegistros = $asistencias->count();
            $pctAsistGlobal = $totalRegistros > 0
                ? round((($tipos['Presente'] + $tipos['Tardanza'] + $tipos['Justificado']) / $totalRegistros) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'resumen' => [
                    'total_sesiones' => $horarios->count(),
                    'sesiones_desarrolladas' => $totalSes,
                    'total_participantes' => $activosIds->count(),
                    'total_bajas' => $participantes->count() - $activosIds->count(),
                    'total_registros' => $totalRegistros,
                    'porcentaje_asistencia' => $pctAsistGlobal,
                    'distribucion' => $tipos,
                ],
                'por_sesion' => $porSesion,
                'por_participante' => $porParticipante,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular estadísticas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function asistenciaSesionData($horarioId)
    {
        try {
            $horario = DB::table('horarios')
                ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
                ->where('horarios.id', $horarioId)
                ->select(
                    'horarios.*',
                    'asignados.nombres as asignado_nombres',
                    'asignados.apellidos as asignado_apellidos'
                )
                ->first();

            if (!$horario) {
                return response()->json(['success' => false, 'message' => 'Sesión no encontrada'], 404);
            }

            $this->ensureAsistenciasTable();

            $modulo = DB::table('modulos')->where('id', $horario->modulo_id)->first(['id', 'programa_id']);
            if (!$modulo) {
                return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
            }

            $participantes = DB::table('inscripciones')
                ->join('participantes as p', 'inscripciones.participante_id', '=', 'p.id')
                ->where('inscripciones.programa_id', $modulo->programa_id)
                ->select('p.id', 'p.ci', 'p.nombres', 'p.apellidos', 'p.celular',
                    'inscripciones.estado', 'inscripciones.baja_operativa', 'inscripciones.baja_academica', 'inscripciones.baja_contable')
                ->orderBy('p.apellidos')
                ->orderBy('p.nombres')
                ->get()
                ->unique('id');

            $previas = DB::table('asistencias')
                ->where('horario_id', $horarioId)
                ->get()
                ->keyBy('participante_id');

            $participantes = $participantes->map(function ($p) use ($previas) {
                $prev = $previas->get($p->id);
                $p->asistio = $prev->asistio ?? null;
                $p->observacion = $prev->observacion ?? null;
                // Dados de baja: se resaltan y no se les registra asistencia.
                $p->baja = \App\Support\BajaInscripcion::motivo($p);
                unset($p->estado, $p->baja_operativa, $p->baja_academica, $p->baja_contable);
                return $p;
            });

            return response()->json([
                'success' => true,
                'horario' => $horario,
                'participantes' => $participantes->values(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la asistencia: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function asistenciaSesionPdf($horarioId)
    {
        $horario = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('horarios.id', $horarioId)
            ->select(
                'horarios.*',
                'modulos.nombre as modulo_nombre',
                'modulos.programa_id',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos'
            )
            ->first();

        if (!$horario) {
            abort(404, 'Sesión no encontrada');
        }

        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.id', $horario->programa_id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre')
            ->first();

        $this->ensureAsistenciasTable();

        $asistencias = DB::table('asistencias')
            ->join('participantes', 'asistencias.participante_id', '=', 'participantes.id')
            ->where('asistencias.horario_id', $horarioId)
            ->select(
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'asistencias.asistio',
                'asistencias.observacion'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        if ($asistencias->isEmpty()) {
            abort(404, 'Esta sesión no tiene asistencia registrada');
        }

        $resumen = $asistencias->groupBy(fn($a) => trim($a->asistio ?? ''))->map->count();

        $pdf = Pdf::loadView('admin.programas.asistencia-sesion-pdf', compact(
            'horario', 'programa', 'asistencias', 'resumen'
        ));

        $fecha = str_replace('-', '', (string) $horario->fecha_desarrollo);
        $nombreModulo = str_replace(['/', '\\', ' '], '_', $horario->modulo_nombre);
        return $pdf->download("asistencia_{$fecha}_{$nombreModulo}.pdf");
    }

    private function ensureAsistenciasTable(): void
    {
        if (!Schema::hasTable('asistencias')) {
            Schema::create('asistencias', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('horario_id');
                $table->unsignedBigInteger('participante_id');
                $table->string('asistio', 20)->default('Presente');
                $table->text('observacion')->nullable();
                $table->timestamps();
                $table->unique(['horario_id', 'participante_id']);
                $table->index('horario_id');
                $table->index('participante_id');
            });
        }
    }

    public function guardarAsistenciaSesion(Request $request, $horarioId)
    {
        $horario = DB::table('horarios')->where('id', $horarioId)->first();
        if (!$horario) {
            return response()->json(['success' => false, 'message' => 'Sesión no encontrada'], 404);
        }

        $validated = $request->validate([
            'observaciones' => 'nullable|string|max:1000',
            'asistencias' => 'required|array',
            'asistencias.*.participante_id' => 'required|integer',
            'asistencias.*.asistio' => 'required|string|in:Presente,Ausente,Justificado,Tardanza',
            'asistencias.*.observacion' => 'nullable|string|max:500',
        ]);

        $this->ensureAsistenciasTable();

        DB::beginTransaction();
        try {
            DB::table('horarios')->where('id', $horarioId)->update([
                'estado' => 'Desarrollado',
                'observaciones' => $validated['observaciones'] ?? $horario->observaciones,
            ]);

            $now = now();
            foreach ($validated['asistencias'] as $a) {
                DB::table('asistencias')->updateOrInsert(
                    ['horario_id' => $horarioId, 'participante_id' => $a['participante_id']],
                    [
                        'asistio' => $a['asistio'],
                        'observacion' => $a['observacion'] ?? null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar la asistencia: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada correctamente',
            'total' => count($validated['asistencias']),
        ]);
    }

    public function updateModuloEstado(Request $request)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'desarrollo' => 'required|in:No Inicio,En Desarrollo,Pendiente,Suspendido,Concluido',
            'fecha_conclusion' => 'nullable|date',
        ]);

        $data = [
            'desarrollo' => $validated['desarrollo'],
        ];

        // Al concluir el módulo se registra/actualiza la fecha de conclusión
        if ($validated['desarrollo'] === 'Concluido') {
            $data['fecha_conclusion'] = !empty($validated['fecha_conclusion']) ? $validated['fecha_conclusion'] : date('Y-m-d');
        }

        DB::table('modulos')->where('id', $validated['modulo_id'])->update($data);

        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
    }

    public function updateModuloPagoDocente(Request $request, $moduloId)
    {
        $validated = $request->validate([
            'pago_docente' => 'nullable|numeric|min:0',
            'factura' => 'nullable|boolean',
            'iva' => 'nullable|numeric|min:0',
            'it' => 'nullable|numeric|min:0',
            'gastos_extra' => 'nullable|numeric|min:0',
            'fecha_pago' => 'nullable|date',
        ]);

        $factura = $request->has('factura') ? ($validated['factura'] ? 1 : 0) : null;
        $pagoDocente = $request->has('pago_docente') ? (float) $validated['pago_docente'] : null;

        $data = [];
        if ($pagoDocente !== null) $data['pago_docente'] = $pagoDocente;
        if ($factura !== null) $data['factura'] = $factura;
        if ($request->has('fecha_pago')) $data['fecha_pago'] = $validated['fecha_pago'];

        if ($factura === 1) {
            $data['iva'] = 0;
            $data['it'] = 0;
        } elseif ($factura === 0 && $pagoDocente !== null) {
            $data['iva'] = round($pagoDocente * 0.13, 2);
            $data['it'] = round($pagoDocente * 0.03, 2);
        } else {
            if ($request->has('iva')) $data['iva'] = (float) $validated['iva'];
            if ($request->has('it')) $data['it'] = (float) $validated['it'];
        }

        if ($request->has('gastos_extra')) $data['gastos_extra'] = (float) $validated['gastos_extra'];

        if (!empty($data)) {
            DB::table('modulos')->where('id', $moduloId)->update($data);
        }

        $modulo = DB::table('modulos')->where('id', $moduloId)->first([
            'pago_docente',
            'factura',
            'iva',
            'it',
            'gastos_extra',
            'fecha_pago'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datos de pago docente actualizados correctamente',
            'modulo' => $modulo,
        ]);
    }

    public function procesarPagoDocente(Request $request, $moduloId)
    {
        $modulo = DB::table('modulos')->where('id', $moduloId)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        if ($modulo->desarrollo !== 'Concluido') {
            return response()->json(['success' => false, 'message' => 'El módulo debe estar en estado Concluido para procesar el pago'], 422);
        }

        if ((int)($modulo->se_pago ?? 0) === 1) {
            return response()->json(['success' => false, 'message' => 'El pago de este módulo ya fue procesado'], 422);
        }

        $validated = $request->validate([
            'fecha_pago' => 'nullable|date',
            'detalle_pago' => 'nullable|in:factura,retencion',
        ]);

        $pagoDocente = (float) $modulo->pago_docente;
        $iva = (float) $modulo->iva;
        $it = (float) $modulo->it;
        $detalle = $validated['detalle_pago'] ?? ($modulo->factura ? 'factura' : 'retencion');
        $totalPagado = $detalle === 'factura' ? $pagoDocente : max(0, $pagoDocente - $iva - $it);
        $procesoPago = $detalle === 'factura' ? 'Pagado con Factura' : 'Pagado con Retención';

        DB::table('modulos')->where('id', $moduloId)->update([
            'se_pago' => 1,
            'total_pagado' => $totalPagado,
            'fecha_pago' => $validated['fecha_pago'] ?? now()->toDateString(),
            'proceso_pago' => $procesoPago,
        ]);

        $moduloActualizado = DB::table('modulos')->where('id', $moduloId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Pago docente procesado correctamente',
            'modulo' => $moduloActualizado,
        ]);
    }

    public function updateModuloMoodleLink(Request $request, $moduloId)
    {
        $validated = $request->validate([
            'moodle_link' => 'nullable|string|max:500',
        ]);

        $modulo = DB::table('modulos')->where('id', $moduloId)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        DB::table('modulos')->where('id', $moduloId)->update([
            'moodle_link' => !empty($validated['moodle_link']) ? $validated['moodle_link'] : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Enlace Moodle actualizado correctamente']);
    }

    public function updateProgramaMoodleLink(Request $request, $programaId)
    {
        $validated = $request->validate([
            'moodle_link' => 'nullable|string|max:500',
        ]);

        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        DB::table('programas')->where('id', $programaId)->update([
            'moodle_link' => !empty($validated['moodle_link']) ? $validated['moodle_link'] : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Enlace Moodle actualizado correctamente']);
    }

    public function buscarDocentePorCi(Request $request)
    {
        $request->validate(['ci' => 'required|string']);
        $docente = DB::table('docentes')->where('ci', $request->ci)->first();
        return response()->json(['success' => true, 'docente' => $docente]);
    }

    public function registrarDocente(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|unique:docentes,ci',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'correo' => 'nullable|email',
            'celular' => 'nullable|string',
        ]);

        $id = DB::table('docentes')->insertGetId([
            'ci' => $validated['ci'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'correo' => $validated['correo'] ?? '',
            'celular' => $validated['celular'] ?? '',
        ]);

        $docente = DB::table('docentes')->where('id', $id)->first();
        return response()->json(['success' => true, 'docente' => $docente, 'message' => 'Docente registrado correctamente']);
    }

    public function asignarDocenteModulo(Request $request)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'docente_id' => 'required|exists:docentes,id',
        ]);

        DB::table('modulos')->where('id', $validated['modulo_id'])->update([
            'docente_id' => $validated['docente_id'],
        ]);

        $docente = DB::table('docentes')->where('id', $validated['docente_id'])->first();
        return response()->json(['success' => true, 'docente' => $docente, 'message' => 'Docente asignado correctamente']);
    }

    public function moduloDetalle($moduloId)
    {
        $modulo = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.id', $moduloId)
            ->select('modulos.*', 'docentes.nombres as docente_nombres', 'docentes.apellidos as docente_apellidos', 'docentes.ci as docente_ci', 'docentes.correo as docente_correo', 'docentes.celular as docente_celular')
            ->first();

        if (!$modulo) {
            abort(404, 'Módulo no encontrado');
        }

        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.id', $modulo->programa_id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre')
            ->first();

        $horarios = DB::table('horarios')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('horarios.modulo_id', $moduloId)
            ->select('horarios.*', 'asignados.nombres as asignado_nombres', 'asignados.apellidos as asignado_apellidos')
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        $participantes = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->leftJoin('modulos_participantes', function ($join) use ($moduloId) {
                $join->on('modulos_participantes.participante_id', '=', 'participantes.id')
                    ->where('modulos_participantes.modulo_id', '=', $moduloId)
                    ->where('modulos_participantes.tipo', '!=', 'Nivelacion');
            })
            ->where('inscripciones.programa_id', $modulo->programa_id)
            ->select(
                'participantes.id',
                'participantes.ci',
                'participantes.apellidos',
                'participantes.nombres',
                DB::raw('MAX(modulos_participantes.id) as mp_id'),
                DB::raw('MAX(modulos_participantes.nota_modulo) as nota_modulo'),
                DB::raw('MAX(modulos_participantes.nota_nivelacion) as nota_nivelacion'),
                DB::raw('(SELECT MAX(mpn.nota_nivelacion) FROM modulos_participantes mpn WHERE mpn.participante_id = participantes.id AND mpn.modulo_id = ' . (int) $moduloId . ' AND mpn.tipo = "Nivelacion") as nota_nivelacion_pago'),
                DB::raw('MIN(inscripciones.estado) as estado'),
                DB::raw('MAX(inscripciones.baja_operativa) as baja_operativa'),
                DB::raw('MAX(inscripciones.baja_academica) as baja_academica'),
                DB::raw('MAX(inscripciones.baja_contable) as baja_contable')
            )
            ->groupBy('participantes.id', 'participantes.ci', 'participantes.apellidos', 'participantes.nombres')
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->each(function ($p) {
                $p->baja = \App\Support\BajaInscripcion::motivo($p);
            });

        // Dados de baja: se muestran al final y no entran en los conteos ni promedios.
        $participantes = $participantes->whereNull('baja')->values()->concat($participantes->whereNotNull('baja')->values());
        $activos = $participantes->whereNull('baja');
        $bajasCount = $participantes->count() - $activos->count();

        $inscritosCount = $activos->count();
        $conNota = $activos->filter(fn($p) => $p->nota_modulo !== null && (float) $p->nota_modulo > 0)->count();
        $todasSesionesDesarrolladas = $horarios->count() > 0 && $horarios->every(fn($h) => ($h->estado ?? '') === 'Desarrollado');
        $sinNotas = $conNota === 0;

        return view('admin.programas.modulo-detalle', compact(
            'modulo',
            'programa',
            'horarios',
            'participantes',
            'inscritosCount',
            'bajasCount',
            'conNota',
            'todasSesionesDesarrolladas',
            'sinNotas'
        ));
    }

    public function guardarNotasModulo(Request $request, $moduloId)
    {
        $modulo = DB::table('modulos')->where('id', $moduloId)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        $horarios = DB::table('horarios')->where('modulo_id', $moduloId)->get();
        $todasDesarrolladas = $horarios->count() > 0 && $horarios->every(fn($h) => $h->estado === 'Desarrollado');
        if (!$todasDesarrolladas) {
            return response()->json(['success' => false, 'message' => 'No todas las sesiones están desarrolladas'], 403);
        }

        $notas = $request->input('notas', []);
        DB::beginTransaction();
        try {
            foreach ($notas as $key => $data) {
                $mpId = null;
                if (str_starts_with((string)$key, 'new_')) {
                    $participanteId = (int) substr((string)$key, 4);
                    $mpId = DB::table('modulos_participantes')->insertGetId([
                        'modulo_id' => $moduloId,
                        'participante_id' => $participanteId,
                        'tipo' => 'Colegiatura',
                        'deuda' => 0,
                        'total_pagado' => 0,
                        'estado' => 1,
                    ]);
                } else {
                    $mpId = (int) $key;
                }
                $update = [];
                if (array_key_exists('nota_modulo', $data)) {
                    $val = $data['nota_modulo'];
                    $update['nota_modulo'] = ($val !== '' && $val !== null) ? (float) $val : 0;
                }
                if (array_key_exists('nota_nivelacion', $data)) {
                    $val = $data['nota_nivelacion'];
                    $update['nota_nivelacion'] = ($val !== '' && $val !== null) ? (float) $val : 0;
                }
                if (!empty($update) && $mpId) {
                    DB::table('modulos_participantes')->where('id', $mpId)->update($update);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Notas guardadas correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function guardarNivelacionModulo(Request $request, $moduloId)
    {
        $modulo = DB::table('modulos')->where('id', $moduloId)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        $notas = $request->input('notas', []);
        DB::beginTransaction();
        try {
            foreach ($notas as $mpId => $val) {
                $notaNiv = ($val !== '' && $val !== null) ? (float) $val : 0;
                DB::table('modulos_participantes')
                    ->where('id', $mpId)
                    ->where('modulo_id', $moduloId)
                    ->update(['nota_nivelacion' => $notaNiv]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Notas de nivelación guardadas correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function guardarNivelacionModuloPrograma(Request $request, $programa, $modulo)
    {
        $programaRow = DB::table('programas')->where('id', $programa)->first();
        if (!$programaRow) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $moduloRow = DB::table('modulos')->where('id', $modulo)->where('programa_id', $programa)->first();
        if (!$moduloRow) {
            return response()->json(['success' => false, 'message' => 'Módulo no pertenece al programa'], 404);
        }
        if (($moduloRow->desarrollo ?? '') !== 'Concluido') {
            return response()->json(['success' => false, 'message' => 'La nivelación solo puede configurarse cuando el módulo está en estado Concluido'], 422);
        }

        $validated = $request->validate([
            'costo_participante' => 'required|numeric|min:0|max:999999',
            'participantes' => 'nullable|array',
            'participantes.*' => 'integer',
        ]);

        $costo = (float) $validated['costo_participante'];

        // Honorario del docente de nivelación POR PARTICIPANTE, según el tipo del programa.
        // El docente de la nivelación es FIJO: el mismo que desarrolló el módulo regular.
        $tipoRow = DB::table('tipos')->where('id', $programaRow->tipo_id)->first();
        $pagoDocentePorParticipante = $tipoRow ? (float) ($tipoRow->pago_docente_nivelacion ?? 0) : 0;
        $docenteId = $moduloRow->docente_id ? (int) $moduloRow->docente_id : null;

        DB::table('programas')->where('id', $programa)->update([
            'nivelacion_costo_participante' => $costo,
            'nivelacion_honorario_docente' => $pagoDocentePorParticipante,
            'nivelacion_docente_id' => $docenteId,
        ]);

        $seleccionados = array_values(array_map('intval', $validated['participantes'] ?? []));

        DB::beginTransaction();
        try {
            // Solo se permiten participantes con nota de módulo < 71 en este módulo
            // (incluye nota 0 o vacía) y que NO aprobaron la nivelación gratuita
            $validos = DB::table('modulos_participantes')
                ->where('modulo_id', $modulo)
                ->where('tipo', '!=', 'Nivelacion')
                ->select('participante_id')
                ->selectRaw('MAX(nota_modulo) as nota_modulo')
                ->selectRaw('MAX(nota_nivelacion) as nota_nivelacion')
                ->groupBy('participante_id')
                ->havingRaw('
                    (MAX(nota_modulo) IS NULL OR MAX(nota_modulo) < 71)
                    AND
                    (MAX(nota_nivelacion) IS NULL OR MAX(nota_nivelacion) <= 0 OR MAX(nota_nivelacion) < 71)
                ')
                ->pluck('participante_id')
                ->all();

            $seleccionados = array_values(array_filter($seleccionados, fn($pid) => in_array($pid, $validos)));

            DB::table('modulos_participantes')
                ->where('modulo_id', $modulo)
                ->where('tipo', 'Nivelacion')
                ->delete();

            $rows = [];
            foreach ($seleccionados as $pid) {
                $rows[] = [
                    'modulo_id' => $modulo,
                    'participante_id' => $pid,
                    'tipo' => 'Nivelacion',
                    'retirado' => 0,
                    'estado' => 1,
                    'deuda' => $costo,
                    'descuento' => 0,
                    'total_pagado' => 0,
                    'pago_docente_nivelacion' => $pagoDocentePorParticipante,
                ];
            }
            if (!empty($rows)) {
                DB::table('modulos_participantes')->insert($rows);
            }

            // Marca el programa con nivelación activa si hay alguna cuota generada
            $tieneCuotas = DB::table('modulos_participantes')
                ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                ->where('modulos.programa_id', $programa)
                ->where('modulos_participantes.tipo', 'Nivelacion')
                ->exists();
            DB::table('programas')->where('id', $programa)->update([
                'nivelacion_habilitada' => $tieneCuotas ? 1 : 0,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar participantes: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Nivelación del módulo guardada correctamente',
            'participantes' => count($seleccionados),
        ]);
    }

    private function nivelacionFilaHtml($cuotaId)
    {
        $cuota = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos_participantes.id', $cuotaId)
            ->where('modulos_participantes.tipo', 'Nivelacion')
            ->select(
                'modulos_participantes.*',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos'
            )
            ->first();
        if (!$cuota) {
            return null;
        }

        $notaModulo = DB::table('modulos_participantes')
            ->where('modulo_id', $cuota->modulo_id)
            ->where('participante_id', $cuota->participante_id)
            ->where('tipo', '!=', 'Nivelacion')
            ->max('nota_modulo');

        $deuda = (float) $cuota->deuda;
        $pagado = (float) $cuota->total_pagado;

        $row = (object) [
            'tiene_cuota' => true,
            'mp_id' => (int) $cuota->id,
            'apellidos' => $cuota->apellidos,
            'nombres' => $cuota->nombres,
            'ci' => $cuota->ci,
            'nota_modulo' => (float) ($notaModulo ?? 0),
            'nota_nivelacion' => $cuota->nota_nivelacion !== null ? (float) $cuota->nota_nivelacion : null,
            'deuda' => $deuda,
            'saldo' => max(0, $deuda - $pagado),
            'comprobante_pago' => $cuota->comprobante_pago,
            'comprobante_original' => $cuota->comprobante_original,
            'comprobante_validado' => (int) ($cuota->comprobante_validado ?? 0),
            'pago_docente_cuota' => (float) ($cuota->pago_docente_nivelacion ?? 0),
        ];

        return view('admin.programas.partials.nivelacion_fila', ['r' => $row, 'idx' => 0])->render();
    }

    public function guardarNotasNivelacionModulo(Request $request, $moduloId)
    {
        $validated = $request->validate([
            'notas' => 'required|array',
            'notas.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $modulo = DB::table('modulos')->where('id', $moduloId)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        $cuotas = DB::table('modulos_participantes')
            ->where('modulo_id', $moduloId)
            ->where('tipo', 'Nivelacion')
            ->get();

        $rowsHtml = [];
        DB::beginTransaction();
        try {
            foreach ($validated['notas'] as $cuotaId => $notaRaw) {
                $cuota = $cuotas->firstWhere('id', (int) $cuotaId);
                if (!$cuota || (int) ($cuota->comprobante_validado ?? 0) !== 1) {
                    continue;
                }
                $nota = $notaRaw !== null && $notaRaw !== '' ? (float) $notaRaw : null;
                if ($nota === null) {
                    continue;
                }
                DB::table('modulos_participantes')->where('id', $cuota->id)->update([
                    'nota_nivelacion' => min(100, max(0, $nota)),
                ]);
                $rowsHtml[$cuota->id] = $this->nivelacionFilaHtml($cuota->id);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar las notas: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notas de nivelación guardadas correctamente',
            'rows_html' => $rowsHtml,
        ]);
    }

    public function guardarNotaNivelacionCuota(Request $request, $cuotaId)
    {
        $validated = $request->validate([
            'nota_nivelacion' => 'required|numeric|min:0|max:100',
        ]);

        $cuota = DB::table('modulos_participantes')->where('id', $cuotaId)->first();
        if (!$cuota) {
            return response()->json(['success' => false, 'message' => 'Cuota no encontrada'], 404);
        }
        if (($cuota->tipo ?? '') !== 'Nivelacion') {
            return response()->json(['success' => false, 'message' => 'La cuota no es de nivelación'], 422);
        }
        if ((int) ($cuota->comprobante_validado ?? 0) !== 1) {
            return response()->json(['success' => false, 'message' => 'Primero valida el comprobante de pago'], 422);
        }

        $nota = (float) $validated['nota_nivelacion'];

        DB::table('modulos_participantes')->where('id', $cuotaId)->update([
            'nota_nivelacion' => $nota,
        ]);

        return response()->json([
            'success' => true,
            'message' => $nota >= 71 ? 'Nota de nivelación guardada. El participante aprueba la nivelación.' : 'Nota de nivelación guardada.',
            'row_html' => $this->nivelacionFilaHtml($cuotaId),
        ]);
    }

    public function subirComprobanteNivelacion(Request $request, $cuotaId)
    {
        $validated = $request->validate([
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $cuota = DB::table('modulos_participantes')->where('id', $cuotaId)->first();
        if (!$cuota) {
            return response()->json(['success' => false, 'message' => 'Cuota no encontrada'], 404);
        }
        if (($cuota->tipo ?? '') !== 'Nivelacion') {
            return response()->json(['success' => false, 'message' => 'La cuota no es de nivelación'], 422);
        }

        $file = $request->file('comprobante');
        $folder = "comprobantes_nivelacion/{$cuota->modulo_id}";
        $filename = "pago_{$cuota->participante_id}_{$cuotaId}_" . time() . '.' . $file->extension();

        DB::beginTransaction();
        try {
            if (!empty($cuota->comprobante_pago)) {
                Storage::disk('public')->delete($cuota->comprobante_pago);
            }
            $path = $file->storeAs($folder, $filename, 'public');

            DB::table('modulos_participantes')->where('id', $cuotaId)->update([
                'comprobante_pago' => $path,
                'comprobante_original' => $file->getClientOriginalName(),
                'comprobante_validado' => 0,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($path) && $path) {
                Storage::disk('public')->delete($path);
            }
            return response()->json(['success' => false, 'message' => 'Error al subir el comprobante: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comprobante subido correctamente',
            'comprobante_pago' => $path,
            'comprobante_original' => $file->getClientOriginalName(),
            'url' => asset('storage/' . $path),
            'row_html' => $this->nivelacionFilaHtml($cuotaId),
        ]);
    }

    public function validarPagoNivelacion(Request $request, $cuotaId)
    {
        $cuota = DB::table('modulos_participantes')->where('id', $cuotaId)->first();
        if (!$cuota) {
            return response()->json(['success' => false, 'message' => 'Cuota no encontrada'], 404);
        }
        if (($cuota->tipo ?? '') !== 'Nivelacion') {
            return response()->json(['success' => false, 'message' => 'La cuota no es de nivelación'], 422);
        }
        if (empty($cuota->comprobante_pago)) {
            return response()->json(['success' => false, 'message' => 'Primero sube el comprobante de pago del participante'], 422);
        }
        if ((int) ($cuota->comprobante_validado ?? 0) === 1) {
            return response()->json(['success' => false, 'message' => 'El pago de este participante ya fue validado'], 422);
        }

        DB::beginTransaction();
        try {
            $saldo = (float) $cuota->deuda - (float) $cuota->total_pagado;
            if ($saldo > 0) {
                $fecha = now()->toDateString();
                $pagoId = DB::table('pagos')->insertGetId([
                    'pago_bs' => $saldo,
                    'created' => $fecha,
                    'modified' => $fecha,
                ]);

                DB::table('deudas')->insert([
                    'modulo_participante_id' => $cuotaId,
                    'pago_id' => $pagoId,
                    'monto_bs' => $saldo,
                    'terminado' => 1,
                ]);

                DB::table('modulos_participantes')->where('id', $cuotaId)->update([
                    'total_pagado' => (float) $cuota->total_pagado + $saldo,
                ]);
            }

            DB::table('modulos_participantes')->where('id', $cuotaId)->update([
                'comprobante_validado' => 1,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al validar el pago: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago del participante validado. El honorario del docente queda considerado.',
            'row_html' => $this->nivelacionFilaHtml($cuotaId),
        ]);
    }

    public function agregarHorarioNivelacion(Request $request, $programa)
    {
        $validated = $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'fecha_desarrollo' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'asignado_id' => 'required|exists:responsables,id',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $modulo = DB::table('modulos')->where('id', $validated['modulo_id'])->where('programa_id', $programa)->first();
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no pertenece al programa'], 404);
        }

        $nuevoId = DB::table('horarios')->insertGetId([
            'modulo_id' => $validated['modulo_id'],
            'fecha_desarrollo' => $validated['fecha_desarrollo'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'asignado_id' => $validated['asignado_id'],
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => 'Programado',
            'tipo' => 'Nivelacion',
            'inicio' => 0,
            'cuenta_id' => 1,
            'reprogramar_id' => 0,
        ]);

        $nuevoHorario = DB::table('horarios')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('horarios.id', $nuevoId)
            ->select(
                'horarios.*',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Sesión de nivelación agregada correctamente',
            'nuevo_horario' => $nuevoHorario,
        ]);
    }

    public function notasEstadoModulo($moduloId)
    {
        $modulo = DB::table('modulos')->where('id', $moduloId)->first(['id', 'desarrollo']);
        if (!$modulo) {
            return response()->json(['success' => false]);
        }
        $estado = DB::table('modulos_participantes')
            ->where('modulo_id', $moduloId)
            ->selectRaw('
                SUM(CASE WHEN nota_modulo IS NOT NULL AND nota_modulo > 0 THEN 1 ELSE 0 END) as con_nota_modulo,
                SUM(CASE WHEN nota_nivelacion IS NOT NULL AND nota_nivelacion > 0 THEN 1 ELSE 0 END) as con_nota_nivelacion,
                COUNT(*) as total_participantes
            ')
            ->first();
        return response()->json([
            'success' => true,
            'desarrollo' => $modulo->desarrollo,
            'con_nota_modulo' => (int) ($estado->con_nota_modulo ?? 0),
            'con_nota_nivelacion' => (int) ($estado->con_nota_nivelacion ?? 0),
        ]);
    }

    public function notasDataModulo($moduloId)
    {
        $modulo = DB::table('modulos')->where('id', $moduloId)->first(['id', 'nombre']);
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado']);
        }

        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->select(
                'participantes.ci',
                'participantes.apellidos',
                'participantes.nombres',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $conNotaModulo = $participantes->filter(fn($p) => $p->nota_modulo !== null && (float)$p->nota_modulo > 0)->count();
        $conNotaNivelacion = $participantes->filter(fn($p) => $p->nota_nivelacion !== null && (float)$p->nota_nivelacion > 0)->count();

        return response()->json([
            'success' => true,
            'modulo_nombre' => $modulo->nombre,
            'total' => $participantes->count(),
            'con_nota_modulo' => $conNotaModulo,
            'con_nota_nivelacion' => $conNotaNivelacion,
            'participantes' => $participantes,
        ]);
    }

    /**
     * Vista previa de los mensajes de notas de un módulo (`?modo=regular|nivelacion`).
     * El texto es el mismo que se envía (App\Support\MensajeNotas).
     */
    public function mensajesWhatsApp(\Illuminate\Http\Request $request, $moduloId)
    {
        $modo = $request->query('modo') === 'nivelacion' ? 'nivelacion' : 'regular';
        $data = \App\Support\MensajeNotas::construir((int) $moduloId, $modo);

        if (! $data['modulo']) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'modo' => $modo,
            'modulo' => $data['modulo']->nombre,
            'programa' => $data['programa']->nombre ?? '',
            'tiene_nivelacion' => $data['tiene_nivelacion'],
            'mensajes' => $data['mensajes'],
            // Firma que Seguimiento ESAM agrega al final de cada mensaje.
            'firma' => trim((string) (auth()->user()->fullname ?: auth()->user()->username ?: '')),
        ]);
    }

    public function moduloPdf($moduloId)
    {
        $modulo = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.id', $moduloId)
            ->select('modulos.*', 'docentes.nombres as docente_nombres', 'docentes.apellidos as docente_apellidos', 'docentes.ci as docente_ci', 'docentes.correo as docente_correo', 'docentes.celular as docente_celular')
            ->first();

        if (!$modulo) {
            abort(404, 'Módulo no encontrado');
        }

        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.id', $modulo->programa_id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre')
            ->first();

        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->select(
                'participantes.id',
                'participantes.ci',
                'participantes.apellidos',
                'participantes.nombres',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->map(function ($p) {
                $p->literal_modulo = $this->notaLiteral($p->nota_modulo);
                return $p;
            });

        $inscritosCount = $participantes->count();

        $pdf = Pdf::loadView('admin.programas.modulo-detalle-pdf', compact(
            'modulo',
            'programa',
            'participantes',
            'inscritosCount'
        ));

        $nombreModulo = str_replace(['/', '\\', ' '], '_', $modulo->nombre);
        return $pdf->download("notas_{$nombreModulo}.pdf");
    }

    public function moduloPdfNivelacion($moduloId)
    {
        $modulo = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.id', $moduloId)
            ->select('modulos.*', 'docentes.nombres as docente_nombres', 'docentes.apellidos as docente_apellidos', 'docentes.ci as docente_ci', 'docentes.correo as docente_correo', 'docentes.celular as docente_celular')
            ->first();

        if (!$modulo) {
            abort(404, 'Módulo no encontrado');
        }

        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.id', $modulo->programa_id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre')
            ->first();

        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->where('modulos_participantes.nota_modulo', '>', 0)
            ->where('modulos_participantes.nota_modulo', '<', 71)
            ->select(
                'participantes.id',
                'participantes.ci',
                'participantes.apellidos',
                'participantes.nombres',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->map(function ($p) {
                $p->literal_nivelacion = $this->notaLiteral($p->nota_nivelacion);
                return $p;
            });

        $inscritosCount = $participantes->count();
        if ($inscritosCount === 0) {
            abort(404, 'No hay participantes reprobados en este módulo');
        }

        $pdf = Pdf::loadView('admin.programas.modulo-detalle-pdf-nivelacion', compact(
            'modulo',
            'programa',
            'participantes',
            'inscritosCount'
        ));

        $nombreModulo = str_replace(['/', '\\', ' '], '_', $modulo->nombre);
        return $pdf->download("nivelacion_{$nombreModulo}.pdf");
    }

    private function notaLiteral($nota): ?string
    {
        if ($nota === null) {
            return null;
        }
        $n = (float) $nota;
        $valor = $n == floor($n) ? (int) $n : $n;

        if (class_exists(\NumberFormatter::class)) {
            $f = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
            $texto = $f->format($valor);
        } else {
            $texto = $this->numeroALetras((int) round($n));
        }

        return mb_strtoupper($texto);
    }

    private function numeroALetras(int $n): string
    {
        $unidades = ['cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve'];
        $decenas = [3 => 'treinta', 4 => 'cuarenta', 5 => 'cincuenta', 6 => 'sesenta', 7 => 'setenta', 8 => 'ochenta', 9 => 'noventa'];

        if ($n < 0) return 'menos ' . $this->numeroALetras(-$n);
        if ($n < 30) return $unidades[$n];
        if ($n < 100) {
            $d = intdiv($n, 10);
            $u = $n % 10;
            return $u === 0 ? $decenas[$d] : $decenas[$d] . ' y ' . $unidades[$u];
        }
        if ($n === 100) return 'cien';
        return (string) $n;
    }

    public function notasPdf($id)
    {
        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.id', $id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre')
            ->first();

        if (!$programa) abort(404);

        $modulos = DB::table('modulos')
            ->where('programa_id', $id)
            ->orderBy('id')
            ->get(['id', 'nombre', 'desarrollo']);

        $todosConcluidos = $modulos->count() > 0 && $modulos->every(fn($m) => ($m->desarrollo ?? '') === 'Concluido');
        if (!$todosConcluidos) {
            abort(403, 'No todos los módulos están concluidos');
        }

        $participantes = DB::table('inscripciones')
            ->join('participantes', 'inscripciones.participante_id', '=', 'participantes.id')
            ->where('inscripciones.programa_id', $id)
            ->select(
                'participantes.id',
                'participantes.ci',
                'participantes.nombres',
                'participantes.apellidos',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->filter(fn($p) => !((int)$p->estado === 0 && !(int)($p->baja_operativa ?? 0) && !(int)($p->baja_academica ?? 0)))
            ->values();

        $notas = DB::table('modulos_participantes')
            ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $id)
            ->select(
                'modulos_participantes.participante_id',
                'modulos_participantes.modulo_id',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion'
            )
            ->get()
            ->keyBy(fn($item) => $item->participante_id . '_' . $item->modulo_id);

        $modulosIds = $modulos->pluck('id');
        $estadoNotasPorModulo = DB::table('modulos_participantes')
            ->whereIn('modulo_id', $modulosIds)
            ->selectRaw('modulo_id,
                SUM(CASE WHEN nota_nivelacion IS NOT NULL AND nota_nivelacion > 0 THEN 1 ELSE 0 END) as con_nota_nivelacion')
            ->groupBy('modulo_id')
            ->get()
            ->keyBy('modulo_id');

        $inscritosCount = $participantes->count();

        $tituloPrograma = \Illuminate\Support\Str::startsWith(mb_strtolower(trim($programa->nombre)), mb_strtolower(trim($programa->tipo_nombre)))
            ? $programa->nombre
            : $programa->tipo_nombre . ' ' . $programa->nombre;

        $pdf = Pdf::loadView('admin.programas.notas-pdf', compact(
            'programa', 'modulos', 'participantes', 'notas', 'estadoNotasPorModulo',
            'inscritosCount', 'tituloPrograma'
        ));

        $nombre = str_replace(['/', '\\', ' '], '_', $programa->nombre);
        return $pdf->download("notas_{$nombre}.pdf");
    }

    public function horariosPdf($id)
    {
        return $this->generarHorariosPdf($id, false);
    }

    public function horariosPdfDocentes($id)
    {
        return $this->generarHorariosPdf($id, true);
    }

    private function generarHorariosPdf($id, bool $conDocentes)
    {
        $programa = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('areas_programas', 'programas.id', '=', 'areas_programas.programa_id')
            ->leftJoin('areas', 'areas_programas.area_id', '=', 'areas.id')
            ->where('programas.id', $id)
            ->select('programas.*', 'tipos.nombre as tipo_nombre', 'areas.nombre as area_nombre')
            ->first();

        if (!$programa) {
            abort(404, 'Programa no encontrado');
        }

        $modulos = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.programa_id', $id)
            ->select(
                'modulos.id',
                'modulos.nombre',
                'modulos.desarrollo',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.ci as docente_ci'
            )
            ->orderBy('modulos.id')
            ->get();

        $horariosPorModulo = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $id)
            ->select(
                'horarios.modulo_id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get()
            ->groupBy('modulo_id');

        $pdf = Pdf::loadView('admin.programas.horarios-pdf', compact(
            'programa',
            'modulos',
            'horariosPorModulo',
            'conDocentes'
        ));

        $nombrePrograma = str_replace(['/', '\\', ' '], '_', $programa->nombre);
        $prefijo = $conDocentes ? 'horarios_docentes' : 'horarios';
        return $pdf->download("{$prefijo}_{$nombrePrograma}.pdf");
    }

    public function registrarPagoCuota(Request $request, $cuotaId)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'nullable|date',
        ]);

        $cuota = DB::table('modulos_participantes')->where('id', $cuotaId)->first();
        if (!$cuota) {
            return response()->json(['success' => false, 'message' => 'Cuota no encontrada'], 404);
        }

        $saldo = (float) $cuota->deuda - (float) $cuota->total_pagado;
        if ($saldo <= 0) {
            return response()->json(['success' => false, 'message' => 'Esta cuota ya está pagada'], 422);
        }
        if ($validated['monto'] > $saldo + 0.01) {
            return response()->json(['success' => false, 'message' => 'El monto excede el saldo (Bs ' . number_format($saldo, 2) . ')'], 422);
        }

        DB::beginTransaction();
        try {
            $fecha = $validated['fecha'] ?? now()->toDateString();
            $pagoId = DB::table('pagos')->insertGetId([
                'pago_bs' => $validated['monto'],
                'created' => $fecha,
                'modified' => $fecha,
            ]);

            $aplicar = min($saldo, (float) $validated['monto']);
            $terminado = (($cuota->total_pagado + $aplicar) >= $cuota->deuda) ? 1 : 0;

            DB::table('deudas')->insert([
                'modulo_participante_id' => $cuotaId,
                'pago_id' => $pagoId,
                'monto_bs' => $aplicar,
                'terminado' => $terminado,
            ]);

            DB::table('modulos_participantes')
                ->where('id', $cuotaId)
                ->update(['total_pagado' => $cuota->total_pagado + $aplicar]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente',
            'pago_id' => $pagoId,
            'aplicado' => $aplicar,
        ]);
    }

    public function eliminarPago($pagoId)
    {
        $pago = DB::table('pagos')->where('id', $pagoId)->first();
        if (!$pago) {
            return response()->json(['success' => false, 'message' => 'Pago no encontrado'], 404);
        }

        $deudas = DB::table('deudas')->where('pago_id', $pagoId)->get();
        if ($deudas->count() === 0) {
            DB::table('pagos')->where('id', $pagoId)->delete();
            return response()->json(['success' => true, 'message' => 'Pago eliminado (sin deudas asociadas)']);
        }

        DB::beginTransaction();
        try {
            foreach ($deudas as $deuda) {
                $cuota = DB::table('modulos_participantes')->where('id', $deuda->modulo_participante_id)->first();
                if ($cuota) {
                    $nuevoPagado = max(0, (float) $cuota->total_pagado - (float) $deuda->monto_bs);
                    DB::table('modulos_participantes')
                        ->where('id', $deuda->modulo_participante_id)
                        ->update(['total_pagado' => $nuevoPagado]);
                }
            }

            DB::table('deudas')->where('pago_id', $pagoId)->delete();
            DB::table('pagos')->where('id', $pagoId)->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago eliminado correctamente',
        ]);
    }

    public function updateCuotaFecha(Request $request, $cuotaId)
    {
        $validated = $request->validate([
            'fecha_pago' => 'nullable|date',
        ]);

        $cuota = DB::table('modulos_participantes')->where('id', $cuotaId)->first();
        if (!$cuota) {
            return response()->json(['success' => false, 'message' => 'Cuota no encontrada'], 404);
        }

        DB::table('modulos_participantes')
            ->where('id', $cuotaId)
            ->update(['fecha_pago' => $validated['fecha_pago']]);

        return response()->json([
            'success' => true,
            'message' => 'Fecha actualizada correctamente',
        ]);
    }

    public function getFechasParticipante(Request $request, $programaId, $participanteId)
    {
        $inscripcion = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('participante_id', $participanteId)
            ->first();
        if (!$inscripcion) {
            return response()->json(['success' => false, 'message' => 'Inscripción no encontrada'], 404);
        }

        $plan = ((int) ($inscripcion->contado ?? 0) === 1) ? 'Contado' : 'Regular';

        $cuotas = DB::table('modulos_participantes as mp')
            ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
            ->where('modulos.programa_id', $programaId)
            ->where('mp.participante_id', $participanteId)
            ->orderBy('mp.tipo')
            ->orderBy('mp.id')
            ->select(
                'mp.id',
                'mp.tipo',
                'mp.deuda',
                'mp.descuento',
                'mp.total_pagado',
                'mp.fecha_pago'
            )->get();

        if ($cuotas->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay cuotas para este participante'], 404);
        }

        $matricula = $cuotas->where('tipo', 'Matricula')->values();
        $colegiatura = $cuotas->where('tipo', 'Colegiatura')->values();

        return response()->json([
            'success' => true,
            'participante_id' => $participanteId,
            'plan' => $plan,
            'contado' => (int) ($inscripcion->contado ?? 0),
            'cuotas' => [
                'Matricula' => $matricula->map(fn($c) => ['id' => $c->id, 'fecha_pago' => $c->fecha_pago]),
                'Colegiatura' => $colegiatura->map(fn($c) => ['id' => $c->id, 'fecha_pago' => $c->fecha_pago]),
            ],
            'total_matricula' => $matricula->count(),
            'total_colegiatura' => $colegiatura->count(),
        ]);
    }

    public function saveFechasParticipante(Request $request, $programaId, $participanteId)
    {
        $validated = $request->validate([
            'fechas' => 'required|array',
            'fechas.Matricula' => 'nullable|array',
            'fechas.Matricula.*' => 'nullable|date',
            'fechas.Colegiatura' => 'nullable|array',
            'fechas.Colegiatura.*' => 'nullable|date',
        ]);

        $inscripcion = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('participante_id', $participanteId)
            ->first();
        if (!$inscripcion) {
            return response()->json(['success' => false, 'message' => 'Inscripción no encontrada'], 404);
        }

        $afectados = 0;
        DB::beginTransaction();
        try {
            foreach (['Matricula', 'Colegiatura'] as $tipo) {
                $cuotas = DB::table('modulos_participantes as mp')
                    ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                    ->where('modulos.programa_id', $programaId)
                    ->where('mp.participante_id', $participanteId)
                    ->where('mp.tipo', $tipo)
                    ->orderBy('mp.id')
                    ->pluck('mp.id')
                    ->toArray();

                $fechas = array_values(array_filter($validated['fechas'][$tipo] ?? [], fn($x) => !empty($x)));
                foreach ($cuotas as $i => $mpId) {
                    if (!isset($fechas[$i])) {
                        DB::table('modulos_participantes')
                            ->where('id', $mpId)
                            ->update(['fecha_pago' => null]);
                        $afectados++;
                        continue;
                    }
                    DB::table('modulos_participantes')
                        ->where('id', $mpId)
                        ->update(['fecha_pago' => $fechas[$i]]);
                    $afectados++;
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fechas guardadas correctamente',
            'cuotas_actualizadas' => $afectados,
        ]);
    }

    public function registrarPagoMultiple(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'aplicaciones' => 'required|array|min:1',
            'aplicaciones.*.cuota_id' => 'required|integer',
            'aplicaciones.*.monto' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $fecha = $validated['fecha'] ?? now()->toDateString();
            $totalPago = collect($validated['aplicaciones'])->sum('monto');

            $pagoId = DB::table('pagos')->insertGetId([
                'pago_bs' => $totalPago,
                'created' => $fecha,
                'modified' => $fecha,
            ]);

            $totalAplicado = 0;
            $cuotasAfectadas = 0;
            foreach ($validated['aplicaciones'] as $app) {
                $cuota = DB::table('modulos_participantes')->where('id', $app['cuota_id'])->first();
                if (!$cuota) continue;

                $saldo = (float) $cuota->deuda - (float) $cuota->total_pagado;
                if ($saldo <= 0) continue;

                $aplicar = min($saldo, (float) $app['monto']);
                if ($aplicar <= 0) continue;
                $terminado = (($cuota->total_pagado + $aplicar) >= $cuota->deuda) ? 1 : 0;

                DB::table('deudas')->insert([
                    'modulo_participante_id' => $app['cuota_id'],
                    'pago_id' => $pagoId,
                    'monto_bs' => $aplicar,
                    'terminado' => $terminado,
                ]);

                DB::table('modulos_participantes')
                    ->where('id', $app['cuota_id'])
                    ->update(['total_pagado' => $cuota->total_pagado + $aplicar]);

                $totalAplicado += $aplicar;
                $cuotasAfectadas++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente',
            'pago_id' => $pagoId,
            'total_aplicado' => $totalAplicado,
            'cuotas_afectadas' => $cuotasAfectadas,
        ]);
    }

    public function participanteModuloHorarios($participanteId, $moduloId)
    {
        try {
            $this->ensureAsistenciasTable();

            $horarios = DB::table('horarios')
                ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
                ->where('horarios.modulo_id', $moduloId)
                ->select(
                    'horarios.id',
                    'horarios.fecha_desarrollo',
                    'horarios.hora_inicio',
                    'horarios.hora_fin',
                    'horarios.estado',
                    'horarios.observaciones',
                    'asignados.nombres as asignado_nombres',
                    'asignados.apellidos as asignado_apellidos'
                )
                ->orderBy('horarios.fecha_desarrollo')
                ->orderBy('horarios.hora_inicio')
                ->get();

            $asist = DB::table('asistencias')
                ->where('participante_id', $participanteId)
                ->whereIn('horario_id', $horarios->pluck('id'))
                ->get()
                ->keyBy('horario_id');

            $resumen = ['Presente' => 0, 'Ausente' => 0, 'Tardanza' => 0, 'Justificado' => 0];

            $horarios = $horarios->map(function ($h) use ($asist, &$resumen) {
                $a = $asist->get($h->id);
                $h->asistio = $a->asistio ?? null;
                $h->observacion_asistencia = $a->observacion ?? null;
                if ($a && isset($resumen[$a->asistio])) $resumen[$a->asistio]++;
                return $h;
            });

            $desarrolladas = $horarios->where('estado', 'Desarrollado')->count();
            $efectivas = $resumen['Presente'] + $resumen['Tardanza'] + $resumen['Justificado'];
            $pct = $desarrolladas > 0 ? round(($efectivas / $desarrolladas) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'horarios' => $horarios,
                'resumen' => $resumen,
                'desarrolladas' => $desarrolladas,
                'porcentaje' => $pct,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar horarios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function participanteDetalle($participanteId)
    {
        $participante = DB::table('participantes')->where('id', $participanteId)->first();

        if (!$participante) {
            abort(404, 'Participante no encontrado');
        }

        $inscripciones = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('inscripciones.participante_id', $participanteId)
            ->select('inscripciones.*', 'programas.nombre as programa_nombre', 'tipos.nombre as tipo_nombre', 'programas.modulo_cero')
            ->orderBy('programas.nombre')
            ->get();

        $programasData = [];

        foreach ($inscripciones as $inscripcion) {
            $pid = $inscripcion->programa_id;

            $modulos = DB::table('modulos')
                ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
                ->where('modulos.programa_id', $pid)
                ->select('modulos.*', 'docentes.nombres as docente_nombres', 'docentes.apellidos as docente_apellidos')
                ->orderBy('modulos.id')
                ->get();

            $notas = DB::table('modulos_participantes')
                ->where('modulos_participantes.participante_id', $participanteId)
                ->whereIn('modulos_participantes.modulo_id', $modulos->pluck('id'))
                ->select('modulo_id', 'nota_modulo', 'nota_nivelacion')
                ->get()
                ->keyBy('modulo_id');

            $modulosIds = $modulos->pluck('id');

            $hasFechaPago = Schema::hasColumn('modulos_participantes', 'fecha_pago');
            $cuotasQuery = DB::table('modulos_participantes as mp')
                ->where('mp.participante_id', $participanteId)
                ->whereIn('mp.modulo_id', $modulosIds);
            $cols = ['mp.id', 'mp.tipo', 'mp.total_pagado', 'mp.deuda', 'mp.modulo_id'];
            if ($hasFechaPago) $cols[] = 'mp.fecha_pago';
            $cuotas = $cuotasQuery
                ->select($cols)
                ->orderBy(DB::raw("FIELD(mp.tipo, 'Matricula', 'Colegiatura')"))
                ->orderBy('mp.modulo_id')
                ->get()
                ->map(function ($c) {
                    return (object) [
                        'id'          => $c->id,
                        'tipo'        => $c->tipo,
                        'modulo_id'   => $c->modulo_id,
                        'total_cuota' => (float) $c->deuda,
                        'pagado'      => (float) $c->total_pagado,
                        'saldo'       => (float) $c->deuda - (float) $c->total_pagado,
                        'descuento'   => 0,
                        'fecha_pago'  => $c->fecha_pago ?? null,
                    ];
                });

            $idsCuotas = $cuotas->pluck('id');

            // Limpiar deudas huérfanas (pago_id que ya no existe)
            $idsDeudasHuerfanas = DB::table('deudas')
                ->whereIn('modulo_participante_id', $idsCuotas)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))->from('pagos')->whereColumn('pagos.id', 'deudas.pago_id');
                })
                ->pluck('id');
            if ($idsDeudasHuerfanas->count() > 0) {
                DB::table('deudas')->whereIn('id', $idsDeudasHuerfanas)->delete();
            }

            $pagosPorCuota = DB::table('deudas')
                ->join('pagos', 'deudas.pago_id', '=', 'pagos.id')
                ->whereIn('deudas.modulo_participante_id', $idsCuotas)
                ->select('deudas.modulo_participante_id', 'pagos.id as pago_id', 'deudas.monto_bs as pago_bs', 'pagos.created as fecha')
                ->orderBy('pagos.created')
                ->get()
                ->groupBy('modulo_participante_id');

            // Recalcular pagado desde las deudas reales y corregir DB si está desfasado
            $cuotas = $cuotas->map(function ($c) use ($pagosPorCuota) {
                $deudas = $pagosPorCuota->get($c->id, collect());
                $pagadoReal = (float) $deudas->sum('pago_bs');
                if (abs((float) $c->pagado - $pagadoReal) > 0.01) {
                    DB::table('modulos_participantes')
                        ->where('id', $c->id)
                        ->update(['total_pagado' => $pagadoReal]);
                }
                $c->pagado = $pagadoReal;
                $c->saldo = $c->total_cuota - $pagadoReal;
                return $c;
            });

            $totalMatricula = $cuotas->where('tipo', 'Matricula')->sum('total_cuota');
            $totalPagadoMat = $cuotas->where('tipo', 'Matricula')->sum('pagado');
            $totalDeudaMat  = $cuotas->where('tipo', 'Matricula')->sum('saldo');
            $totalColegiatura = $cuotas->where('tipo', 'Colegiatura')->sum('total_cuota');
            $totalPagadoCol  = $cuotas->where('tipo', 'Colegiatura')->sum('pagado');
            $totalDeudaCol   = $cuotas->where('tipo', 'Colegiatura')->sum('saldo');

            $programasData[] = (object) [
                'inscripcion'  => $inscripcion,
                'modulos'      => $modulos,
                'notas'        => $notas,
                'cuotas'       => $cuotas,
                'pagosPorCuota' => $pagosPorCuota,
                'totalMatriculaPagado' => $totalPagadoMat,
                'totalMatriculaDeuda'  => $totalDeudaMat,
                'totalColegiaturaPagado' => $totalPagadoCol,
                'totalColegiaturaDeuda'  => $totalDeudaCol,
            ];
        }

        return view('admin.programas.participante-detalle', compact('participante', 'programasData'));
    }

    public function updateDocenteCredenciales(Request $request)
    {
        $validated = $request->validate([
            'docente_id' => 'required|integer|exists:docentes,id',
            'usuario' => 'required|string|max:50',
            'contrasena' => 'required|string|max:50',
        ]);

        DB::table('docentes')
            ->where('id', $validated['docente_id'])
            ->update([
                'usuario' => $validated['usuario'],
                'contrasena' => $validated['contrasena'],
            ]);

        return response()->json(['success' => true, 'message' => 'Credenciales actualizadas']);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $moduloIds = DB::table('modulos')->where('programa_id', $id)->pluck('id');

            if ($moduloIds->isNotEmpty()) {
                $horarioIds = DB::table('horarios')->whereIn('modulo_id', $moduloIds)->pluck('id');
                if ($horarioIds->isNotEmpty()) {
                    DB::table('sedes_horarios')->whereIn('horario_id', $horarioIds)->delete();
                }
                DB::table('horarios')->whereIn('modulo_id', $moduloIds)->delete();
                DB::table('planillas_modulos')->whereIn('modulo_id', $moduloIds)->delete();
                DB::table('modulos_participantes')->whereIn('modulo_id', $moduloIds)->delete();
                DB::table('modulos')->whereIn('id', $moduloIds)->delete();
            }

            $inscripcionIds = DB::table('inscripciones')->where('programa_id', $id)->pluck('id');
            if ($inscripcionIds->isNotEmpty()) {
                DB::table('seguimientos')->whereIn('inscripcione_id', $inscripcionIds)->delete();
                DB::table('inscripciones')->whereIn('id', $inscripcionIds)->delete();
            }

            DB::table('areas_programas')->where('programa_id', $id)->delete();
            DB::table('programas_responsables')->where('programa_id', $id)->delete();
            DB::table('campanas_marketing')->where('programa_id', $id)->delete();
            DB::table('programas_dias')->where('programa_id', $id)->delete();
            DB::table('preinscripciones')->where('programa_id', $id)->delete();
            DB::table('programas')->where('id', $id)->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Programa eliminado correctamente']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    public function notificaciones()
    {
        $hoy = date('Y-m-d');
        $user = auth()->user();
        $items = [];

        // Filtrar por responsable_id del usuario
        $programaIds = [];
        if (!empty($user->responsable_id)) {
            $programaIds = DB::table('programas_responsables')
                ->where('responsable_id', $user->responsable_id)
                ->pluck('programa_id')
                ->toArray();
        }

        // 1. Módulos "No Inicio" con horarios pasados
        $qModulos = DB::table('modulos')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->join('horarios', 'horarios.modulo_id', '=', 'modulos.id')
            ->where('modulos.desarrollo', 'No Inicio')
            ->where('horarios.fecha_desarrollo', '<', $hoy);

        if (!empty($programaIds)) {
            $qModulos->whereIn('modulos.programa_id', $programaIds);
        }

        $modulosNoInicio = $qModulos->select(
            'modulos.id as modulo_id',
            'modulos.nombre as modulo_nombre',
            'programas.id as programa_id',
            'programas.nombre as programa_nombre',
            DB::raw('MAX(horarios.fecha_desarrollo) as ultima_fecha')
        )
            ->groupBy('modulos.id', 'modulos.nombre', 'programas.id', 'programas.nombre')
            ->orderByDesc('ultima_fecha')
            ->get();

        foreach ($modulosNoInicio as $m) {
            $items[] = [
                'tipo' => 'modulo_no_inicio',
                'mensaje' => "Módulo «{$m->modulo_nombre}» del programa «{$m->programa_nombre}» sigue en <strong>No Inicio</strong> con horarios pasados.",
                'url' => route('admin.programas.show', $m->programa_id),
                'modulo_id' => $m->modulo_id,
                'programa_id' => $m->programa_id,
                'programa_nombre' => $m->programa_nombre,
                'modulo_nombre' => $m->modulo_nombre,
                'fecha' => $m->ultima_fecha,
            ];
        }

        // 2. Horarios con fecha pasada que NO están "Desarrollados" (sin asistencia registrada)
        $qHorarios = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('asistencias', 'horarios.id', '=', 'asistencias.horario_id')
            ->where('horarios.fecha_desarrollo', '<', $hoy)
            ->where(function ($q) {
                $q->where('horarios.estado', '!=', 'Desarrollado')
                    ->orWhereNull('asistencias.id');
            });

        if (!empty($programaIds)) {
            $qHorarios->whereIn('modulos.programa_id', $programaIds);
        }

        $horariosVencidos = $qHorarios->select(
            'horarios.id as horario_id',
            'horarios.fecha_desarrollo',
            'horarios.estado',
            'modulos.id as modulo_id',
            'modulos.nombre as modulo_nombre',
            'programas.id as programa_id',
            'programas.nombre as programa_nombre'
        )
            ->distinct()
            ->orderByDesc('horarios.fecha_desarrollo')
            ->get();

        foreach ($horariosVencidos as $h) {
            $items[] = [
                'tipo' => 'horario_vencido',
                'mensaje' => "Sesión del {$h->fecha_desarrollo} del módulo «{$h->modulo_nombre}» ({$h->programa_nombre}) está como <strong>{$h->estado}</strong> sin asistencia registrada.",
                'url' => route('admin.programas.modulo-detalle', $h->modulo_id),
                'horario_id' => $h->horario_id,
                'modulo_id' => $h->modulo_id,
                'programa_id' => $h->programa_id,
                'programa_nombre' => $h->programa_nombre,
                'modulo_nombre' => $h->modulo_nombre,
                'estado' => $h->estado,
                'fecha' => $h->fecha_desarrollo,
            ];
        }

        // Ordenar por fecha descendente
        usort($items, function ($a, $b) {
            return strcmp($b['fecha'], $a['fecha']);
        });

        return response()->json([
            'total' => count($items),
            'items' => array_slice($items, 0, 50),
        ]);
    }

    public function guardarFechaCertificacion(Request $request, $programaId, $participanteId)
    {
        $mp = DB::table('modulos_participantes')
            ->whereNull('modulo_id')
            ->where('participante_id', $participanteId)
            ->where('tipo', 'Certificacion')
            ->first();

        if (!$mp) {
            return response()->json(['success' => false, 'message' => 'No hay deuda de certificación para este participante'], 404);
        }

        $validated = $request->validate([
            'fecha_pago' => 'nullable|date',
            'pagado' => 'nullable|boolean',
        ]);

        $update = [];
        if ($request->has('fecha_pago')) {
            $update['fecha_pago'] = $validated['fecha_pago'] ?: null;
        }
        if ($request->has('pagado')) {
            $pagado = $validated['pagado'] ? 1 : 0;
            $update['total_pagado'] = $pagado ? $mp->deuda : 0;
        }

        if (!empty($update)) {
            DB::table('modulos_participantes')
                ->where('id', $mp->id)
                ->update($update);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fecha de certificación guardada correctamente',
        ]);
    }

    public function generarDeudasCertificacion(Request $request, $programaId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $costoConvenio = (float) ($programa->costo_certificacion ?? 0);
        $costoPersonal = (float) ($programa->costo_certificacion_personal ?? 0);

        if ($costoConvenio <= 0 && $costoPersonal <= 0) {
            return response()->json(['success' => false, 'message' => 'El programa no tiene costos de certificación definidos'], 422);
        }

        $participantes = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->select('participante_id', 'modalidad_titulacion')
            ->get();

        $generados = 0;
        foreach ($participantes as $ins) {
            $exists = DB::table('modulos_participantes')
                ->whereNull('modulo_id')
                ->where('participante_id', $ins->participante_id)
                ->where('tipo', 'Certificacion')
                ->exists();

            if (!$exists) {
                $esConvenio = (int)($ins->modalidad_titulacion ?? 1) === 1;
                $deuda = $esConvenio ? $costoConvenio : $costoPersonal;
                if ($deuda <= 0) continue;

                DB::table('modulos_participantes')->insert([
                    'modulo_id' => null,
                    'participante_id' => $ins->participante_id,
                    'tipo' => 'Certificacion',
                    'deuda' => $deuda,
                    'descuento' => 0,
                    'total_pagado' => 0,
                    'estado' => 1,
                    'retirado' => 0,
                ]);
                $generados++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$generados} deudas de certificación generadas correctamente",
            'generados' => $generados,
        ]);
    }

    public function registrarPagoCertificacion(Request $request, $programaId, $participanteId)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
        ]);

        $mp = DB::table('modulos_participantes')
            ->whereNull('modulo_id')
            ->where('participante_id', $participanteId)
            ->where('tipo', 'Certificacion')
            ->first();

        if (!$mp) {
            return response()->json(['success' => false, 'message' => 'No hay deuda de certificación para este participante'], 404);
        }

        $monto = (float) $validated['monto'];
        $fecha = $validated['fecha'];
        $saldo = (float) $mp->deuda - (float) $mp->total_pagado;

        if ($monto > $saldo + 0.01) {
            return response()->json(['success' => false, 'message' => 'El monto excede el saldo pendiente (Bs ' . number_format($saldo, 2) . ')'], 422);
        }

        $detalle = $mp->detalle_pagos ? json_decode($mp->detalle_pagos, true) : [];
        $detalle[] = ['monto' => $monto, 'fecha' => $fecha];

        $nuevoTotal = (float) $mp->total_pagado + $monto;

        DB::table('modulos_participantes')
            ->where('id', $mp->id)
            ->update([
                'total_pagado' => $nuevoTotal,
                'fecha_pago' => $fecha,
                'detalle_pagos' => json_encode($detalle),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pago de certificación registrado correctamente',
            'total_pagado' => $nuevoTotal,
            'detalle' => $detalle,
        ]);
    }

    public function actualizarModalidadTitulacion(Request $request, $programaId)
    {
        $validated = $request->validate([
            'participantes' => 'required|array',
            'participantes.*.participante_id' => 'required|integer',
            'participantes.*.modalidad' => 'required|integer|in:0,1',
        ]);

        $actualizados = 0;
        foreach ($validated['participantes'] as $item) {
            DB::table('inscripciones')
                ->where('programa_id', $programaId)
                ->where('participante_id', $item['participante_id'])
                ->update(['modalidad_titulacion' => $item['modalidad']]);
            $actualizados++;
        }

        return response()->json([
            'success' => true,
            'message' => "Modalidad de titulación actualizada para {$actualizados} participante(s)",
            'actualizados' => $actualizados,
        ]);
    }

    public function getFolios($programaId)
    {
        $folios = DB::table('folios')
            ->leftJoin('folio_participantes', 'folios.id', '=', 'folio_participantes.folio_id')
            ->leftJoin('inscripciones', 'folio_participantes.participante_id', '=', 'inscripciones.participante_id')
            ->where(function ($q) use ($programaId) {
                $q->where('folios.programa_id', $programaId)
                  ->orWhere('inscripciones.programa_id', $programaId);
            })
            ->select('folios.*')
            ->distinct()
            ->orderBy('folios.created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'folios' => $folios]);
    }

    public function storeFolio(Request $request, $programaId)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'fecha_registro' => 'required|date',
            'conceptos' => 'required|array|min:1',
            'conceptos.*' => 'required|string|in:Inscripcion,Matricula,Colegiatura,Certificacion',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'required|integer|exists:participantes,id',
        ]);

        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $costos = [
            'Inscripcion' => (float) ($programa->costo_pago_inscripcion_universidad ?? 0),
            'Matricula' => (float) ($programa->costo_pago_matricula_universidad ?? 0),
            'Colegiatura' => (float) ($programa->costo_pago_colegiatura_universidad ?? 0),
            'Certificacion' => (float) ($programa->costo_pago_certificacion_universidad ?? 0),
        ];

        $participantes = $validated['participantes'];
        $cant = count($participantes);
        $conceptosSel = $validated['conceptos'];

        $totalInsc = in_array('Inscripcion', $conceptosSel) ? $costos['Inscripcion'] * $cant : 0;
        $totalMat = in_array('Matricula', $conceptosSel) ? $costos['Matricula'] * $cant : 0;
        $totalCol = in_array('Colegiatura', $conceptosSel) ? $costos['Colegiatura'] * $cant : 0;
        $totalCert = in_array('Certificacion', $conceptosSel) ? $costos['Certificacion'] * $cant : 0;
        $totalGeneral = $totalInsc + $totalMat + $totalCol + $totalCert;

        $folioId = DB::table('folios')->insertGetId([
            'programa_id' => $programaId,
            'nombre' => $validated['nombre'],
            'fecha_registro' => $validated['fecha_registro'],
            'concepto' => json_encode($conceptosSel),
            'status' => 'abierto',
            'total_inscripcion' => $totalInsc,
            'total_matricula' => $totalMat,
            'total_colegiatura' => $totalCol,
            'total_certificacion' => $totalCert,
            'total_general' => $totalGeneral,
            'cantidad_participantes' => $cant,
        ]);

        $conceptToColumn = [
            'Inscripcion' => 'monto_inscripcion',
            'Matricula' => 'monto_matricula',
            'Colegiatura' => 'monto_colegiatura',
            'Certificacion' => 'monto_certificacion',
        ];

        $rows = [];
        foreach ($participantes as $pid) {
            $row = ['folio_id' => $folioId, 'participante_id' => $pid];
            foreach ($conceptToColumn as $concept => $column) {
                $row[$column] = in_array($concept, $conceptosSel) ? $costos[$concept] : 0;
            }
            $rows[] = $row;
        }
        DB::table('folio_participantes')->insert($rows);

        return response()->json([
            'success' => true,
            'message' => 'Folio creado correctamente',
            'folio_id' => $folioId,
        ]);
    }

    public function registrarParticipantesEnFolio(Request $request, $programaId, $folioId)
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        if (!$programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $folio = DB::table('folios')->where('id', $folioId)->first();
        if (!$folio) {
            return response()->json(['success' => false, 'message' => 'Folio no encontrado'], 404);
        }

        if ($folio->status !== 'abierto') {
            return response()->json(['success' => false, 'message' => 'El folio está cerrado'], 403);
        }

        $validated = $request->validate([
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'required|integer|exists:participantes,id',
            'conceptos' => 'required|array|min:1',
            'conceptos.*' => 'required|string|in:Inscripcion,Matricula,Colegiatura,Certificacion',
        ]);

        $costos = [
            'Inscripcion' => (float) ($programa->costo_pago_inscripcion_universidad ?? 0),
            'Matricula' => (float) ($programa->costo_pago_matricula_universidad ?? 0),
            'Colegiatura' => (float) ($programa->costo_pago_colegiatura_universidad ?? 0),
            'Certificacion' => (float) ($programa->costo_pago_certificacion_universidad ?? 0),
        ];

        $conceptosSel = $validated['conceptos'];
        $participantes = $validated['participantes'];

        $conceptToColumn = [
            'Inscripcion' => 'monto_inscripcion',
            'Matricula' => 'monto_matricula',
            'Colegiatura' => 'monto_colegiatura',
            'Certificacion' => 'monto_certificacion',
        ];

        DB::beginTransaction();
        try {
            foreach ($participantes as $pid) {
                $existing = DB::table('folio_participantes')
                    ->where('folio_id', $folioId)
                    ->where('participante_id', $pid)
                    ->first();

                if ($existing) {
                    $update = [];
                    foreach ($conceptToColumn as $concept => $column) {
                        if (in_array($concept, $conceptosSel)) {
                            $update[$column] = DB::raw($column . ' + ' . $costos[$concept]);
                        }
                    }
                    if (!empty($update)) {
                        DB::table('folio_participantes')
                            ->where('id', $existing->id)
                            ->update($update);
                    }
                } else {
                    $row = ['folio_id' => $folioId, 'participante_id' => $pid];
                    foreach ($conceptToColumn as $concept => $column) {
                        $row[$column] = in_array($concept, $conceptosSel) ? $costos[$concept] : 0;
                    }
                    DB::table('folio_participantes')->insert($row);
                }
            }

            $totals = DB::table('folio_participantes')
                ->where('folio_id', $folioId)
                ->selectRaw('
                    COALESCE(SUM(monto_inscripcion), 0) as total_inscripcion,
                    COALESCE(SUM(monto_matricula), 0) as total_matricula,
                    COALESCE(SUM(monto_colegiatura), 0) as total_colegiatura,
                    COALESCE(SUM(monto_certificacion), 0) as total_certificacion,
                    COUNT(*) as cantidad_participantes
                ')
                ->first();

            $totalGeneral = $totals->total_inscripcion + $totals->total_matricula + $totals->total_colegiatura + $totals->total_certificacion;

            DB::table('folios')->where('id', $folioId)->update([
                'total_inscripcion' => $totals->total_inscripcion,
                'total_matricula' => $totals->total_matricula,
                'total_colegiatura' => $totals->total_colegiatura,
                'total_certificacion' => $totals->total_certificacion,
                'total_general' => $totalGeneral,
                'cantidad_participantes' => $totals->cantidad_participantes,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al registrar participantes: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Participantes registrados en el folio correctamente',
        ]);
    }

    public function pagosDocentes(Request $request)
    {
        $proximaFecha = function () {
            $hoy = now();
            $dia = $hoy->day;
            if ($dia <= 10) {
                return $hoy->format('Y-m-10');
            } elseif ($dia <= 20) {
                return $hoy->format('Y-m-20');
            } else {
                $sig = $hoy->copy()->addMonthNoOverflow();
                return $sig->format('Y-m-10');
            }
        };

        $periodoActual = $proximaFecha();

        $periodoSeleccionado = $request->input('periodo', $periodoActual);

        $query = DB::table('modulos')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.desarrollo', 'Concluido')
            ->where('modulos.se_pago', 0)
            ->where('modulos.pago_docente', '>', 0);

        if ($periodoSeleccionado !== 'todos') {
            $query->where('modulos.fecha_pago', $periodoSeleccionado);
        }

        $modulos = $query->select(
                'modulos.id',
                'modulos.nombre as modulo_nombre',
                'modulos.pago_docente',
                'modulos.factura',
                'modulos.iva',
                'modulos.it',
                'modulos.gastos_extra',
                'modulos.fecha_pago',
                'modulos.programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.ci as docente_ci'
            )
            ->orderBy('programas.nombre')
            ->orderBy('modulos.id')
            ->get();

        $totalesGenerales = (object) [
            'total_pago' => $modulos->sum('pago_docente'),
            'total_iva' => $modulos->sum('iva'),
            'total_it' => $modulos->sum('it'),
            'total_gastos' => $modulos->sum('gastos_extra'),
        ];

        $hoy = now();
        $periodosDisponibles = collect();
        for ($i = -1; $i <= 2; $i++) {
            $fecha = $hoy->copy()->addMonthsNoOverflow($i);
            $periodosDisponibles->push($fecha->format('Y-m-10'));
            $periodosDisponibles->push($fecha->format('Y-m-20'));
        }
        $periodosDisponibles = $periodosDisponibles->unique()->sort()->values();

        return view('admin.programas.pagos-docentes', compact(
            'modulos', 'totalesGenerales', 'proximaFecha',
            'periodoActual', 'periodoSeleccionado', 'periodosDisponibles'
        ));
    }
}
