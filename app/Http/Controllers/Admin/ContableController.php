<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContableController extends Controller
{
    public function seguimiento(Request $request)
    {
        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()
            ->orderByDesc('gestion')->pluck('gestion');

        $gestionFiltro = $request->get('gestion', (int) date('Y'));

        // Cargar todas las inscripciones del filtro
        $upiTipoIds = [6]; // UPI Diplomados
        $inscripciones = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('programas.gestion', $gestionFiltro)
            ->select(
                'inscripciones.id',
                'inscripciones.programa_id',
                'inscripciones.participante_id',
                'inscripciones.matricula',
                'inscripciones.monto_programa',
                'inscripciones.descuento_matricula',
                'inscripciones.descuento_colegiatura',
                'inscripciones.contado',
                'programas.tipo_id'
            )
            ->get();

        $participanteIds = $inscripciones->pluck('participante_id')->unique()->values();
        $programaIds = $inscripciones->pluck('programa_id')->unique()->values();

        // Cargar pagos por participante (suma de total_pagado en modulos_participantes)
        $pagos = collect();
        if ($participanteIds->isNotEmpty() && $programaIds->isNotEmpty()) {
            $pagos = DB::table('modulos_participantes')
                ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                ->whereIn('modulos_participantes.participante_id', $participanteIds)
                ->whereIn('modulos.programa_id', $programaIds)
                ->select(
                    'modulos.programa_id',
                    'modulos_participantes.participante_id',
                    'modulos_participantes.tipo',
                    DB::raw('SUM(modulos_participantes.total_pagado) as pagado')
                )
                ->groupBy('modulos.programa_id', 'modulos_participantes.participante_id', 'modulos_participantes.tipo')
                ->get();
        }

        // Mapa pagos: [programa_id][participante_id][tipo] = pagado
        $pagosMap = [];
        foreach ($pagos as $p) {
            $pagosMap[$p->programa_id][$p->participante_id][$p->tipo] = (float) $p->pagado;
        }

        // Cargar deudas por participante para determinar inscritos reales (usa monto_bs de deudas)
        $deudas = collect();
        if ($participanteIds->isNotEmpty() && $programaIds->isNotEmpty()) {
            $deudas = DB::table('modulos_participantes')
                ->join('deudas', 'deudas.modulo_participante_id', '=', 'modulos_participantes.id')
                ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                ->whereIn('modulos_participantes.participante_id', $participanteIds)
                ->whereIn('modulos.programa_id', $programaIds)
                ->select(
                    'modulos.programa_id',
                    'modulos_participantes.participante_id',
                    'modulos_participantes.tipo',
                    DB::raw('SUM(deudas.monto_bs) as total_deuda')
                )
                ->groupBy('modulos.programa_id', 'modulos_participantes.participante_id', 'modulos_participantes.tipo')
                ->get();
        }

        // Mapa deudas: [programa_id][participante_id][tipo] = total_deuda
        $deudasMap = [];
        foreach ($deudas as $d) {
            $deudasMap[$d->programa_id][$d->participante_id][$d->tipo] = (float) $d->total_deuda;
        }

        // Número de módulos por programa (para plan contado)
        $modulosCount = DB::table('modulos')
            ->whereIn('programa_id', $programaIds)
            ->select('programa_id', DB::raw('COUNT(*) as num_modulos'))
            ->groupBy('programa_id')
            ->get()
            ->keyBy('programa_id');

        // Separar UPI del resto
        $inscMain = $inscripciones->reject(fn($i) => in_array((int) $i->tipo_id, $upiTipoIds));
        $inscUPI  = $inscripciones->filter(fn($i) => in_array((int) $i->tipo_id, $upiTipoIds));

        $programas = DB::table('programas')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
            ->where('programas.gestion', $gestionFiltro)
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.tipo_id',
                'tipos.nombre as tipo_nombre',
                'estados.nombre as estado_nombre'
            )
            ->orderBy('tipos.nombre')->orderBy('programas.nombre')->get();

        $progMain = $programas->reject(fn($p) => in_array((int) $p->tipo_id, $upiTipoIds));
        $progUPI  = $programas->filter(fn($p) => in_array((int) $p->tipo_id, $upiTipoIds));

        $mainStats = $this->buildStats(
            $inscMain, $progMain, $pagosMap, $deudasMap, $modulosCount
        );
        $upiStats = $this->buildStats(
            $inscUPI, $progUPI, $pagosMap, $deudasMap, $modulosCount
        );

        // Adjuntar fechas a ambos
        $this->ensureFechasTable();
        $fechasCount = DB::table('cuotas_fechas_programas')
            ->whereIn('programa_id', $programas->pluck('id'))
            ->select('programa_id', 'plan', DB::raw('COUNT(*) as total'))
            ->groupBy('programa_id', 'plan')
            ->get()
            ->groupBy('programa_id');

        foreach (['porTipo' => &$mainStats['porTipo'], 'porTipoUPI' => &$upiStats['porTipo']] as &$pt) {
            foreach ($pt as &$t) {
                foreach ($t['programas'] as &$p) {
                    $rows = $fechasCount->get($p['id'], collect());
                    $p['fechas_contado'] = (int) ($rows->firstWhere('plan', 'contado')->total ?? 0);
                    $p['fechas_regular'] = (int) ($rows->firstWhere('plan', 'regular')->total ?? 0);
                }
            }
        }
        unset($pt, $t, $p);

        return view('admin.contable.seguimiento', compact(
            'gestiones', 'gestionFiltro',
            'mainStats', 'upiStats'
        ));
    }

    public function cuotasVencidas(Request $request)
    {
        return $this->cuotasReporte($request, 'vencidas');
    }

    public function cuotasProximas(Request $request)
    {
        return $this->cuotasReporte($request, 'proximas');
    }

    private function cuotasReporte(Request $request, string $modo)
    {
        $this->ensureFechasTable();

        $hoy = now()->startOfDay();
        $gestion = (int) $request->get('gestion', $hoy->year);
        $dias = (int) $request->get('dias', 30);
        if ($dias < 1) $dias = 30;
        if ($dias > 180) $dias = 180;
        $programaFiltro = $request->get('programa_id');

        $gestiones = DB::table('programas')->select('gestion')->distinct()
            ->orderByDesc('gestion')->pluck('gestion');
        if ($gestiones->isEmpty()) $gestiones = collect([$hoy->year]);

        $cuotas = collect();
        if (Schema::hasColumn('modulos_participantes', 'fecha_pago')) {
            $q = DB::table('modulos_participantes as mp')
                ->join('participantes as pa', 'mp.participante_id', '=', 'pa.id')
                ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
                ->join('programas as pr', 'm.programa_id', '=', 'pr.id')
                ->leftJoin('tipos as t', 'pr.tipo_id', '=', 't.id')
                ->leftJoin('inscripciones as i', function ($j) {
                    $j->on('i.participante_id', '=', 'pa.id')->on('i.programa_id', '=', 'pr.id');
                })
                ->whereNotNull('mp.fecha_pago')
                ->whereIn('pr.estado_id', [1, 4])
                ->where('pr.gestion', $gestion)
                ->whereRaw('mp.total_pagado < mp.deuda')
                ->select(
                    'mp.id as cuota_id', 'mp.tipo', 'mp.fecha_pago', 'mp.deuda', 'mp.total_pagado',
                    'pa.id as participante_id', 'pa.ci', 'pa.nombres', 'pa.apellidos', 'pa.celular', 'pa.correo',
                    'pr.id as programa_id', 'pr.nombre as programa_nombre', 'pr.codigo as programa_codigo',
                    'pr.gestion as programa_gestion', 't.nombre as tipo_nombre',
                    'i.estado', 'i.baja_operativa', 'i.baja_academica', 'i.baja_contable'
                );

            if ($modo === 'vencidas') {
                // Todas las cuotas impagas con vencimiento anterior a hoy.
                $q->where('mp.fecha_pago', '<', $hoy->toDateString());
            } else { // proximas: solo la cuota del mes en curso que aún no venció
                $q->whereYear('mp.fecha_pago', $hoy->year)
                    ->whereMonth('mp.fecha_pago', $hoy->month)
                    ->where('mp.fecha_pago', '>=', $hoy->toDateString());
            }
            if ($programaFiltro) $q->where('pr.id', $programaFiltro);

            $cuotas = $q->orderBy('mp.fecha_pago')
                ->orderBy('pr.nombre')
                ->orderBy('pa.apellidos')
                ->get()
                ->unique('cuota_id')
                ->values();
        }

        // Calcula campos y agrupa por programa → participante
        $cuotas = $cuotas->map(function ($c) use ($hoy) {
            $deuda = (float) $c->deuda;
            $pagado = (float) $c->total_pagado;
            $c->saldo = max(0, $deuda - $pagado);
            $fp = \Carbon\Carbon::parse($c->fecha_pago)->startOfDay();
            $c->dias = $hoy->diffInDays($fp, false);
            $c->baja = \App\Support\BajaInscripcion::motivo($c);
            return $c;
        });

        // Montos de los vigentes y de los dados de baja por separado: los de baja
        // se muestran pero no se suman al total a cobrar ni reciben recordatorios.
        $vigentes = $cuotas->whereNull('baja');
        $deBaja = $cuotas->whereNotNull('baja');
        $totales = [
            'total_cuotas' => $vigentes->count(),
            'total_deuda' => $vigentes->sum('saldo'),
            'total_participantes' => $vigentes->pluck('participante_id')->unique()->count(),
            'total_programas' => $cuotas->pluck('programa_id')->unique()->count(),
            'bajas_cuotas' => $deBaja->count(),
            'bajas_deuda' => $deBaja->sum('saldo'),
            'bajas_participantes' => $deBaja->pluck('participante_id')->unique()->count(),
            'total_deuda_general' => $cuotas->sum('saldo'),
        ];

        $porPrograma = $cuotas->groupBy('programa_id')->map(function ($cs) {
            $first = $cs->first();
            $porPart = $cs->groupBy('participante_id')->map(function ($items) {
                $p = $items->first();
                return (object) [
                    'participante_id' => $p->participante_id,
                    'ci' => $p->ci,
                    'nombres' => $p->nombres,
                    'apellidos' => $p->apellidos,
                    'celular' => $p->celular,
                    'correo' => $p->correo,
                    'baja' => $p->baja,
                    'cuotas' => $items->values(),
                    'total_saldo' => $items->sum('saldo'),
                    'count' => $items->count(),
                ];
        })->sortBy(fn ($x) => $x->baja ? 1 : 0)->values();

        // ── Mensajería vs cobros del mes (¿el sistema ayuda a cobrar?) ──────────


            return (object) [
                'programa_id' => $first->programa_id,
                'programa_nombre' => $first->programa_nombre,
                'programa_codigo' => $first->programa_codigo,
                'programa_gestion' => $first->programa_gestion,
                'tipo_nombre' => $first->tipo_nombre,
                'participantes' => $porPart,
                'total_saldo' => $cs->whereNull('baja')->sum('saldo'),
                'total_cuotas' => $cs->whereNull('baja')->count(),
                'bajas_saldo' => $cs->whereNotNull('baja')->sum('saldo'),
                'bajas_participantes' => $cs->whereNotNull('baja')->pluck('participante_id')->unique()->count(),
                'vigentes_participantes' => $cs->whereNull('baja')->pluck('participante_id')->unique()->count(),
            ];
        })->sortByDesc('total_saldo')->values();

        // Para el select de programa: programas de la gestion con inscripciones
        $programasFiltro = DB::table('programas')
            ->where('gestion', $gestion)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('inscripciones')->whereColumn('inscripciones.programa_id', 'programas.id');
            })
            ->orderBy('nombre')
            ->select('id', 'nombre', 'codigo')
            ->get();

        return view('admin.contable.cuotas-reporte', [
            'modo' => $modo,
            'gestiones' => $gestiones,
            'gestion' => $gestion,
            'dias' => $dias,
            'programaFiltro' => $programaFiltro,
            'programasFiltro' => $programasFiltro,
            'totales' => $totales,
            'porPrograma' => $porPrograma,
            // QR de pago vigentes para adjuntar en los mensajes.
            'qrs' => \App\Support\PagosQr::vigentes(),
        ]);
    }

    public function asignaciones(Request $request)
    {
        $this->ensureFechasTable();

        $hoy = now();
        $mes = (int) $request->get('mes', $hoy->month);
        $gestion = (int) $request->get('gestion', $hoy->year);
        if ($mes < 1 || $mes > 12) $mes = $hoy->month;

        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()
            ->orderByDesc('gestion')
            ->pluck('gestion');
        if ($gestiones->isEmpty()) {
            $gestiones = collect([$hoy->year]);
        }

        $inicioMes = sprintf('%04d-%02d-01', $gestion, $mes);
        $finMes = date('Y-m-t', strtotime($inicioMes));

        $cuotasMes = collect();
        if (Schema::hasColumn('modulos_participantes', 'fecha_pago')) {
            $cuotasMes = DB::table('modulos_participantes as mp')
                ->join('participantes as pa', 'mp.participante_id', '=', 'pa.id')
                ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
                ->join('programas as pr', 'm.programa_id', '=', 'pr.id')
                ->leftJoin('tipos as t', 'pr.tipo_id', '=', 't.id')
                ->leftJoin('inscripciones as i', function ($j) {
                    $j->on('i.participante_id', '=', 'pa.id')->on('i.programa_id', '=', 'pr.id');
                })
                ->whereNotNull('mp.fecha_pago')
                ->where('pr.estado_id', '!=', 3)
                ->whereBetween('mp.fecha_pago', [$inicioMes, $finMes])
                ->select(
                    'i.estado',
                    'i.baja_operativa',
                    'i.baja_academica',
                    'i.baja_contable',
                    'mp.id as cuota_id',
                    'mp.tipo',
                    'mp.fecha_pago',
                    'mp.deuda',
                    'mp.total_pagado',
                    'pa.id as participante_id',
                    'pa.ci',
                    'pa.nombres',
                    'pa.apellidos',
                    'pr.id as programa_id',
                    'pr.nombre as programa_nombre',
                    'pr.codigo as programa_codigo',
                    'pr.gestion as programa_gestion',
                    't.nombre as tipo_nombre'
                )
                ->orderBy('mp.fecha_pago')
                ->orderBy('pr.nombre')
                ->orderBy('pa.apellidos')
                ->get()
                ->unique('cuota_id')
                ->values()
                ->each(function ($c) {
                    $c->baja = \App\Support\BajaInscripcion::motivo($c);
                });
        }

        // Los montos de los dados de baja se calculan aparte: no entran en lo
        // esperado, cobrado ni pendiente de los vigentes.
        $hoyDate = $hoy->copy()->startOfDay();
        $resumen = [
            'total_cuotas' => 0,
            'total_esperado' => 0.0,
            'total_cobrado' => 0.0,
            'total_pendiente' => 0.0,
            'vencidas' => 0,
            'hoy' => 0,
            'proximas' => 0,
            'pagadas' => 0,
            'participantes' => $cuotasMes->whereNull('baja')->pluck('participante_id')->unique()->count(),
            'programas' => $cuotasMes->pluck('programa_id')->unique()->count(),
            'bajas_cuotas' => 0,
            'bajas_esperado' => 0.0,
            'bajas_cobrado' => 0.0,
            'bajas_pendiente' => 0.0,
            'bajas_participantes' => $cuotasMes->whereNotNull('baja')->pluck('participante_id')->unique()->count(),
        ];

        $cuotasMes = $cuotasMes->map(function ($c) use ($hoyDate, &$resumen) {
            $deuda = (float) $c->deuda;
            $pagado = (float) $c->total_pagado;
            $saldo = max(0, $deuda - $pagado);
            $pagada = $pagado >= $deuda && $deuda > 0;
            $fecha = \Carbon\Carbon::parse($c->fecha_pago)->startOfDay();
            $diff = $hoyDate->diffInDays($fecha, false);

            if ($c->baja) {
                $resumen['bajas_cuotas']++;
                $resumen['bajas_esperado'] += $deuda;
                $resumen['bajas_cobrado'] += min($pagado, $deuda);
                $resumen['bajas_pendiente'] += $saldo;
                $c->saldo = $saldo;
                $c->pagada = $pagada;
                $c->estado_venc = 'baja';
                $c->dias = $diff;
                return $c;
            }

            if ($pagada) {
                $estado = 'pagada'; $resumen['pagadas']++;
            } elseif ($diff < 0) {
                $estado = 'vencida'; $resumen['vencidas']++;
            } elseif ($diff === 0) {
                $estado = 'hoy'; $resumen['hoy']++;
            } else {
                $estado = 'proxima'; $resumen['proximas']++;
            }

            $resumen['total_cuotas']++;
            $resumen['total_esperado'] += $deuda;
            $resumen['total_cobrado']  += min($pagado, $deuda);
            $resumen['total_pendiente']+= $saldo;

            $c->saldo = $saldo;
            $c->pagada = $pagada;
            $c->estado_venc = $estado;
            $c->dias = $diff;
            return $c;
        });

        // Agrupado por programa
        $porPrograma = $cuotasMes->groupBy('programa_id')->map(function ($items) {
            $first = $items->first();
            return (object) [
                'programa_id' => $first->programa_id,
                'programa_nombre' => $first->programa_nombre,
                'programa_codigo' => $first->programa_codigo,
                'programa_gestion' => $first->programa_gestion,
                'tipo_nombre' => $first->tipo_nombre,
                // Vigentes primero; los dados de baja al final.
                'cuotas' => $items->sortBy(fn ($c) => $c->baja ? 1 : 0)->values(),
                'total_esperado' => $items->whereNull('baja')->sum('deuda'),
                'total_cobrado' => $items->whereNull('baja')->sum(fn($c) => min((float)$c->total_pagado, (float)$c->deuda)),
                'total_pendiente' => $items->whereNull('baja')->sum('saldo'),
                'bajas_esperado' => $items->whereNotNull('baja')->sum('deuda'),
                'bajas_cobrado' => $items->whereNotNull('baja')->sum(fn($c) => min((float)$c->total_pagado, (float)$c->deuda)),
                'bajas_pendiente' => $items->whereNotNull('baja')->sum('saldo'),
                'count_bajas' => $items->whereNotNull('baja')->count(),
                'count_vencidas' => $items->where('estado_venc', 'vencida')->count(),
                'count_hoy' => $items->where('estado_venc', 'hoy')->count(),
                'count_proximas' => $items->where('estado_venc', 'proxima')->count(),
                'count_pagadas' => $items->where('estado_venc', 'pagada')->count(),
            ];
        })->values();

        // ── Mensajería vs cobros del mes (¿el sistema ayuda a cobrar?) ──────────
        // Recordatorios de cobranza enviados en el mes (por programa y participante).
        $recPorPrograma = [];
        $recPartPorPrograma = [];
        if (Schema::hasTable('mensajes')) {
            $recordatorios = DB::table('mensajes as ms')
                ->join('inscripciones as i', 'ms.inscripcione_id', '=', 'i.id')
                ->where('ms.tipo', 'whatsapp_cobranza')
                ->whereBetween('ms.fecha_registro', [$inicioMes, $finMes])
                ->select('i.programa_id', 'i.participante_id', DB::raw('COUNT(*) as n'))
                ->groupBy('i.programa_id', 'i.participante_id')
                ->get();

            foreach ($recordatorios as $r) {
                $recPorPrograma[$r->programa_id] = ($recPorPrograma[$r->programa_id] ?? 0) + (int) $r->n;
                $recPartPorPrograma[$r->programa_id][(int) $r->participante_id] = true;
            }
        }

        // Pagos registrados en el mes, atribuidos por programa/participante.
        $pagosMontoPorPrograma = [];
        $pagosPartPorPrograma = [];
        $pagosMes = DB::table('pagos as pg')
            ->join('deudas as d', 'd.pago_id', '=', 'pg.id')
            ->join('modulos_participantes as mp', 'd.modulo_participante_id', '=', 'mp.id')
            ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
            ->whereBetween('pg.created', [$inicioMes, $finMes])
            ->select('m.programa_id', 'mp.participante_id', DB::raw('SUM(d.monto_bs) as monto'))
            ->groupBy('m.programa_id', 'mp.participante_id')
            ->get();

        foreach ($pagosMes as $p) {
            $pagosMontoPorPrograma[$p->programa_id] = ($pagosMontoPorPrograma[$p->programa_id] ?? 0) + (float) $p->monto;
            $pagosPartPorPrograma[$p->programa_id][(int) $p->participante_id] = true;
        }

        // Marca cada cuota con si se le envió recordatorio y si registró pago en el mes.
        foreach ($cuotasMes as $c) {
            $c->recordado = isset($recPartPorPrograma[$c->programa_id][(int) $c->participante_id]);
            $c->pagado_mes = isset($pagosPartPorPrograma[$c->programa_id][(int) $c->participante_id]);
        }

        $porPrograma = $porPrograma->map(function ($pg) use ($recPorPrograma, $recPartPorPrograma, $pagosMontoPorPrograma, $pagosPartPorPrograma) {
            $pid = $pg->programa_id;
            $rec = $recPartPorPrograma[$pid] ?? [];
            $pag = $pagosPartPorPrograma[$pid] ?? [];

            $pg->recordatorios = $recPorPrograma[$pid] ?? 0;
            $pg->recordados = count($rec);
            $pg->pagaron_mes = count($pag);
            $pg->monto_pagado_mes = $pagosMontoPorPrograma[$pid] ?? 0.0;
            // Efectividad: participantes a los que se recordó y registraron pago en el mes.
            $pg->efectividad = count(array_intersect_key($rec, $pag));

            return $pg;
        });

        $resumen['total_recordatorios'] = array_sum($recPorPrograma);
        $resumen['total_pagado_mes'] = array_sum($pagosMontoPorPrograma);

        // Programas sin fechas registradas (gestion + tiene inscripciones)
        $programasSinFechas = DB::table('programas')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.estado_id', '!=', 3)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('inscripciones')
                    ->whereColumn('inscripciones.programa_id', 'programas.id');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('cuotas_fechas_programas')
                    ->whereColumn('cuotas_fechas_programas.programa_id', 'programas.id');
            })
            ->select(
                'programas.id', 'programas.nombre', 'programas.codigo',
                'programas.gestion', 'tipos.nombre as tipo_nombre',
                DB::raw('(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id) as total_inscritos'),
                DB::raw('(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND contado = 1) as inscritos_contado'),
                DB::raw('(SELECT COUNT(*) FROM inscripciones WHERE inscripciones.programa_id = programas.id AND contado = 0) as inscritos_regular')
            )
            ->orderBy('programas.nombre')
            ->get();

        $mesNombres = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $mesNombre = $mesNombres[$mes - 1];

        return view('admin.contable.asignaciones', compact(
            'gestiones', 'gestion', 'mes', 'mesNombre', 'mesNombres',
            'resumen', 'porPrograma', 'programasSinFechas'
        ));
    }

    private function ensureFechasTable(): void
    {
        if (!Schema::hasTable('cuotas_fechas_programas')) {
            Schema::create('cuotas_fechas_programas', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('programa_id');
                $table->string('plan', 20);
                $table->string('tipo', 20);
                $table->unsignedSmallInteger('numero');
                $table->date('fecha_pago');
                $table->timestamps();
                $table->unique(['programa_id', 'plan', 'tipo', 'numero'], 'uq_cuotas_fechas');
                $table->index('programa_id');
            });
        }

        if (Schema::hasTable('modulos_participantes') && !Schema::hasColumn('modulos_participantes', 'fecha_pago')) {
            Schema::table('modulos_participantes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->date('fecha_pago')->nullable()->after('nota_nivelacion');
            });
        }
    }

    public function getFechasPrograma($programaId)
    {
        try {
            $this->ensureFechasTable();

            $prog = DB::table('programas')
                ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
                ->where('programas.id', $programaId)
                ->select(
                    'programas.id', 'programas.nombre', 'programas.codigo',
                    'programas.gestion', 'programas.cantidad_sesiones',
                    'tipos.nombre as tipo_nombre'
                )
                ->first();

            if (!$prog) {
                return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
            }

            // Plan según las inscripciones del programa
            $planCounts = DB::table('inscripciones')
                ->where('programa_id', $programaId)
                ->select('contado', DB::raw('COUNT(*) as total'))
                ->groupBy('contado')
                ->get();

            $contadoCount = (int) ($planCounts->firstWhere('contado', 1)->total ?? 0);
            $regularCount = (int) ($planCounts->firstWhere('contado', 0)->total ?? 0);

            // Verificar si hay participantes Contado con más de 2 cuotas de Colegiatura (necesitan recalculo)
            $needsRecalculo = false;
            if ($contadoCount > 0) {
                $pIds = DB::table('inscripciones')
                    ->where('programa_id', $programaId)
                    ->where('contado', 1)
                    ->pluck('participante_id');

                foreach ($pIds as $pid) {
                    $cnt = DB::table('modulos_participantes as mp')
                        ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                        ->where('modulos.programa_id', $programaId)
                        ->where('mp.participante_id', $pid)
                        ->where('mp.tipo', 'Colegiatura')
                        ->where('mp.deuda', '>', 0)
                        ->count();
                    if ($cnt > 2) {
                        $needsRecalculo = true;
                        break;
                    }
                }
            }

            // Cantidad sugerida de cuotas para Regular Colegiatura: # módulos con cuotas Colegiatura
            $regularColCuotas = (int) DB::table('modulos_participantes as mp')
                ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                ->where('modulos.programa_id', $programaId)
                ->where('mp.tipo', 'Colegiatura')
                ->where(function ($q) {
                    $q->where('mp.retirado', 0)->orWhereNull('mp.retirado');
                })
                ->distinct()
                ->count('modulos.id');
            if ($regularColCuotas <= 0) $regularColCuotas = 1;

            $fechas = DB::table('cuotas_fechas_programas')
                ->where('programa_id', $programaId)
                ->orderBy('plan')->orderBy('tipo')->orderBy('numero')
                ->get(['plan', 'tipo', 'numero', 'fecha_pago']);

            $estructura = [
                'contado' => ['Matricula' => [], 'Colegiatura' => []],
                'regular' => ['Matricula' => [], 'Colegiatura' => []],
            ];
            foreach ($fechas as $f) {
                if (!isset($estructura[$f->plan][$f->tipo])) continue;
                $estructura[$f->plan][$f->tipo][] = [
                    'numero' => (int) $f->numero,
                    'fecha_pago' => $f->fecha_pago,
                ];
            }

            return response()->json([
                'success' => true,
                'programa' => $prog,
                'planes_count' => [
                    'contado' => $contadoCount,
                    'regular' => $regularCount,
                ],
                'defaults' => [
                    'contado' => ['Matricula' => 1, 'Colegiatura' => 2],
                    'regular' => ['Matricula' => 1, 'Colegiatura' => $regularColCuotas],
                ],
                'fechas' => $estructura,
                'needs_recalculo' => $needsRecalculo,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveFechasPrograma(Request $request, $programaId)
    {
        $validated = $request->validate([
            'plan' => 'required|in:contado,regular',
            'fechas' => 'required|array',
            'fechas.Matricula' => 'nullable|array',
            'fechas.Matricula.*' => 'nullable|date',
            'fechas.Colegiatura' => 'nullable|array',
            'fechas.Colegiatura.*' => 'nullable|date',
        ]);

        $prog = DB::table('programas')->where('id', $programaId)->first();
        if (!$prog) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $this->ensureFechasTable();

        DB::beginTransaction();
        try {
            DB::table('cuotas_fechas_programas')
                ->where('programa_id', $programaId)
                ->where('plan', $validated['plan'])
                ->delete();

            $now = now();
            $rows = [];
            foreach (['Matricula', 'Colegiatura'] as $tipo) {
                $arr = $validated['fechas'][$tipo] ?? [];
                $n = 0;
                foreach ($arr as $fecha) {
                    if (!$fecha) continue;
                    $n++;
                    $rows[] = [
                        'programa_id' => $programaId,
                        'plan' => $validated['plan'],
                        'tipo' => $tipo,
                        'numero' => $n,
                        'fecha_pago' => $fecha,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                DB::table('cuotas_fechas_programas')->insert($rows);
            }

            // ===== Propagar las fechas a modulos_participantes =====
            $contadoFlag = $validated['plan'] === 'contado' ? 1 : 0;
            $participanteIds = DB::table('inscripciones')
                ->where('programa_id', $programaId)
                ->where('contado', $contadoFlag)
                ->pluck('participante_id');

            $afectados = 0;
            if ($participanteIds->isNotEmpty()) {
                foreach (['Matricula', 'Colegiatura'] as $tipo) {
                    $arr = array_values(array_filter($validated['fechas'][$tipo] ?? [], fn($x) => !empty($x)));
                    if (empty($arr)) continue;

                    foreach ($participanteIds as $pid) {
                        $mpRows = DB::table('modulos_participantes as mp')
                            ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                            ->where('modulos.programa_id', $programaId)
                            ->where('mp.participante_id', $pid)
                            ->where('mp.tipo', $tipo)
                            ->orderBy('mp.id')
                            ->pluck('mp.id')
                            ->toArray();

                        foreach ($mpRows as $i => $mpId) {
                            if (!isset($arr[$i])) break;
                            DB::table('modulos_participantes')
                                ->where('id', $mpId)
                                ->update(['fecha_pago' => $arr[$i]]);
                            $afectados++;
                        }
                    }
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
            'total' => count($rows),
            'cuotas_actualizadas' => $afectados ?? 0,
            'participantes' => $participanteIds->count() ?? 0,
            'plan' => $validated['plan'],
        ]);
    }

    public function previewRecalcularContado($programaId)
    {
        $prog = DB::table('programas')->where('id', $programaId)->first(['id', 'nombre', 'codigo']);
        if (!$prog) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $participanteIds = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('contado', 1)
            ->pluck('participante_id');

        if ($participanteIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay participantes con plan Contado en este programa', 'participantes' => []]);
        }

        $participantes = [];
        foreach ($participanteIds as $pid) {
            $part = DB::table('participantes')->where('id', $pid)->first(['id', 'nombres', 'apellidos', 'ci']);
            if (!$part) continue;

            $colCuotas = DB::table('modulos_participantes as mp')
                ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                ->where('modulos.programa_id', $programaId)
                ->where('mp.participante_id', $pid)
                ->where('mp.tipo', 'Colegiatura')
                ->orderBy('modulos.id')
                ->orderBy('mp.id')
                ->get(['mp.id as cuota_id', 'mp.deuda', 'mp.descuento', 'mp.total_pagado']);

            $conDeuda = $colCuotas->filter(fn($c) => (float)$c->deuda > 0);
            if ($conDeuda->count() <= 2) continue;

            $totalDeuda = $conDeuda->sum(fn($c) => (float)$c->deuda);
            $totalDescuento = $conDeuda->sum(fn($c) => (float)$c->descuento);
            $totalPagado = $conDeuda->sum(fn($c) => (float)$c->total_pagado);

            $participantes[] = [
                'id' => $part->id,
                'nombre_completo' => $part->nombres . ' ' . $part->apellidos,
                'ci' => $part->ci,
                'cuotas' => $colCuotas->map(fn($c) => [
                    'cuota_id' => $c->cuota_id,
                    'deuda' => (float)$c->deuda,
                    'descuento' => (float)$c->descuento,
                    'total_pagado' => (float)$c->total_pagado,
                ]),
                'total_deuda' => $totalDeuda,
                'total_descuento' => $totalDescuento,
                'total_pagado' => $totalPagado,
                'despues' => [
                    'cuota1_deuda' => intval($totalDeuda / 2),
                    'cuota1_descuento' => intval($totalDescuento / 2),
                    'cuota1_pagado' => min($totalPagado, intval($totalDeuda / 2)),
                    'cuota2_deuda' => $totalDeuda - intval($totalDeuda / 2),
                    'cuota2_descuento' => $totalDescuento - intval($totalDescuento / 2),
                    'cuota2_pagado' => $totalPagado - min($totalPagado, intval($totalDeuda / 2)),
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'programa' => $prog,
            'participantes' => $participantes,
            'total' => count($participantes),
        ]);
    }

    public function recalcularContado($programaId)
    {
        $prog = DB::table('programas')->where('id', $programaId)->first();
        if (!$prog) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        $modulos = DB::table('modulos')
            ->where('programa_id', $programaId)
            ->orderBy('id')
            ->get(['id']);
        $nModulos = $modulos->count();
        $colOffset = ($nModulos >= 3) ? 1 : 0;

        $participanteIds = DB::table('inscripciones')
            ->where('programa_id', $programaId)
            ->where('contado', 1)
            ->pluck('participante_id');

        if ($participanteIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay participantes con plan Contado en este programa']);
        }

        $recalculados = 0;

        foreach ($participanteIds as $pid) {
            $colCuotas = DB::table('modulos_participantes as mp')
                ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                ->where('modulos.programa_id', $programaId)
                ->where('mp.participante_id', $pid)
                ->where('mp.tipo', 'Colegiatura')
                ->orderBy('modulos.id')
                ->orderBy('mp.id')
                ->get(['mp.id', 'mp.deuda', 'mp.descuento', 'mp.total_pagado']);

            $conDeuda = $colCuotas->filter(fn($c) => (float)$c->deuda > 0);
            if ($conDeuda->count() <= 2) continue;

            $totalDeuda = $conDeuda->sum(fn($c) => (float)$c->deuda);
            $totalDescuento = $conDeuda->sum(fn($c) => (float)$c->descuento);
            $totalPagado = $conDeuda->sum(fn($c) => (float)$c->total_pagado);

            $primeraDeuda = intval($totalDeuda / 2);
            $segundaDeuda = $totalDeuda - $primeraDeuda;

            $primeraDesc = intval($totalDescuento / 2);
            $segundaDesc = $totalDescuento - $primeraDesc;

            $primeraPag = min($totalPagado, $primeraDeuda);
            $segundaPag = $totalPagado - $primeraPag;

            foreach ($colCuotas as $i => $cuota) {
                $modIdx = ($i + $colOffset) % $nModulos;
                $nuevoModId = $modulos[$modIdx]->id;
                if ($i === 0) {
                    DB::table('modulos_participantes')->where('id', $cuota->id)->update([
                        'modulo_id' => $nuevoModId,
                        'deuda' => $primeraDeuda,
                        'descuento' => $primeraDesc,
                        'total_pagado' => $primeraPag,
                    ]);
                } elseif ($i === 1) {
                    DB::table('modulos_participantes')->where('id', $cuota->id)->update([
                        'modulo_id' => $nuevoModId,
                        'deuda' => $segundaDeuda,
                        'descuento' => $segundaDesc,
                        'total_pagado' => $segundaPag,
                    ]);
                } else {
                    DB::table('modulos_participantes')->where('id', $cuota->id)->update([
                        'modulo_id' => $nuevoModId,
                        'deuda' => 0,
                        'descuento' => 0,
                        'total_pagado' => 0,
                    ]);
                }
            }
            $recalculados++;
        }

        $msg = $recalculados > 0
            ? "Se recalcularon {$recalculados} participante(s) con plan Contado"
            : "No se encontraron participantes Contado con más de 2 cuotas de Colegiatura";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'recalculados' => $recalculados,
        ]);
    }

    public function cambiarPlanParticipante(Request $request, $programaId, $participanteId)
    {
        try {
            $validated = $request->validate([
                'plan' => 'required|in:contado,regular',
                'descuento_matricula' => 'nullable|numeric|min:0',
                'descuento_colegiatura' => 'nullable|numeric|min:0',
                'es_traspaso' => 'nullable|boolean',
            ]);

            $prog = DB::table('programas')->where('id', $programaId)->first();
            if (!$prog) {
                return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
            }

            $inscripcion = DB::table('inscripciones')
                ->where('programa_id', $programaId)
                ->where('participante_id', $participanteId)
                ->first();

            if (!$inscripcion) {
                return response()->json(['success' => false, 'message' => 'Inscripción no encontrada'], 404);
            }

            $nuevoPlan = $validated['plan'];
            $nuevoContado = $nuevoPlan === 'contado' ? 1 : 0;

            // Costos base del programa
            $matriculaBase     = (float) ($prog->matricula ?? 0);
            $colegiaturaBase   = (float) ($prog->colegiatura ?? 0);

            // Descuentos: usar los enviados o mantener existentes
            $descMat = (float) ($validated['descuento_matricula'] ?? $inscripcion->descuento_matricula ?? 0);
            $descCol = (float) ($validated['descuento_colegiatura'] ?? $inscripcion->descuento_colegiatura ?? 0);

            if ($descMat > $matriculaBase) {
                return response()->json(['success' => false, 'message' => 'El descuento de matrícula no puede ser mayor a la matrícula.'], 422);
            }
            if ($descCol > $colegiaturaBase) {
                return response()->json(['success' => false, 'message' => 'El descuento de colegiatura no puede ser mayor a la colegiatura.'], 422);
            }

            // Deudas netas
            $deudaMatricula = max(0, $matriculaBase - $descMat);
            $deudaColegiatura = max(0, $colegiaturaBase - $descCol);

            // Módulos del programa
            $modulos = DB::table('modulos')
                ->where('programa_id', $programaId)
                ->orderBy('id')
                ->get(['id']);

            $nModulos = $modulos->count();

            // Pagos actuales del participante
            $pagosExistentes = DB::table('modulos_participantes as mp')
                ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                ->where('modulos.programa_id', $programaId)
                ->where('mp.participante_id', $participanteId)
                ->where('mp.tipo', 'Colegiatura')
                ->orderBy('modulos.id')
                ->orderBy('mp.id')
                ->get(['mp.id', 'mp.total_pagado']);

            $totalPagadoCol = $pagosExistentes->sum(fn($c) => (float) $c->total_pagado);

            DB::beginTransaction();
            try {
                // 1. Actualizar plan y descuentos en inscripción
                $esTraspaso = !empty($validated['es_traspaso']) ? 1 : 0;
                DB::table('inscripciones')
                    ->where('programa_id', $programaId)
                    ->where('participante_id', $participanteId)
                    ->update([
                        'contado' => $nuevoContado,
                        'descuento_matricula' => $descMat,
                        'descuento_colegiatura' => $descCol,
                        'deuda_matricula' => $deudaMatricula,
                        'deuda' => $deudaColegiatura,
                        'es_traspaso' => $esTraspaso,
                    ]);

                // 2. Actualizar MATRÍCULA (1 cuota, primer módulo)
                $matRow = DB::table('modulos_participantes as mp')
                    ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                    ->where('modulos.programa_id', $programaId)
                    ->where('mp.participante_id', $participanteId)
                    ->where('mp.tipo', 'Matricula')
                    ->orderBy('modulos.id')
                    ->first(['mp.id', 'mp.total_pagado']);

                if ($matRow) {
                    DB::table('modulos_participantes')
                        ->where('id', $matRow->id)
                        ->update([
                            'deuda' => $deudaMatricula,
                            'descuento' => $descMat,
                            'total_pagado' => min((float) $matRow->total_pagado, $deudaMatricula),
                        ]);
                }

                // 3. Actualizar COLEGIATURA según plan
                $colRows = DB::table('modulos_participantes as mp')
                    ->join('modulos', 'mp.modulo_id', '=', 'modulos.id')
                    ->where('modulos.programa_id', $programaId)
                    ->where('mp.participante_id', $participanteId)
                    ->where('mp.tipo', 'Colegiatura')
                    ->orderBy('modulos.id')
                    ->orderBy('mp.id')
                    ->get(['mp.id', 'mp.total_pagado']);

                $restantePorPagar = $totalPagadoCol;

                if ($nuevoPlan === 'contado') {
                    // CONTADO: Matrícula en módulo 0, Colegiatura 50/50 en módulos 1 y 2
                    $cuotaEntera = (int) ($deudaColegiatura / 2);
                    $segundaCuota = $deudaColegiatura - $cuotaEntera;
                    $colOffset = ($nModulos >= 3) ? 1 : 0;

                    foreach ($colRows as $i => $row) {
                        $modIdx = ($i + $colOffset) % $nModulos;
                        $nuevoModId = $modulos[$modIdx]->id;
                        if ($i === 0) {
                            $pago = min($restantePorPagar, $cuotaEntera);
                            $restantePorPagar -= $pago;
                            DB::table('modulos_participantes')->where('id', $row->id)->update([
                                'modulo_id' => $nuevoModId,
                                'deuda' => $cuotaEntera,
                                'descuento' => 0,
                                'total_pagado' => $pago,
                            ]);
                        } elseif ($i === 1 && $colOffset === 1) {
                            $pago = min($restantePorPagar, $segundaCuota);
                            $restantePorPagar -= $pago;
                            DB::table('modulos_participantes')->where('id', $row->id)->update([
                                'modulo_id' => $nuevoModId,
                                'deuda' => $segundaCuota,
                                'descuento' => 0,
                                'total_pagado' => $pago,
                            ]);
                        } else {
                            DB::table('modulos_participantes')->where('id', $row->id)->update([
                                'modulo_id' => $nuevoModId,
                                'deuda' => 0,
                                'descuento' => 0,
                                'total_pagado' => 0,
                            ]);
                        }
                    }
                } else {
                    // REGULAR: colegiatura distribuida equitativamente entre cuotas
                    $regularCount = max(1, $nModulos - 1);
                    $cuotaEntera = (int) ($deudaColegiatura / $regularCount);

                    foreach ($colRows as $i => $row) {
                        $modIdx = $i % $nModulos;
                        $nuevoModId = $modulos[$modIdx]->id;
                        if ($i >= $regularCount) {
                            DB::table('modulos_participantes')->where('id', $row->id)->update([
                                'modulo_id' => $nuevoModId,
                                'deuda' => 0,
                                'descuento' => 0,
                                'total_pagado' => 0,
                            ]);
                        } else {
                            $esUltima = ($i === $regularCount - 1);
                            $monto = $esUltima
                                ? round($deudaColegiatura - ($cuotaEntera * ($regularCount - 1)), 2)
                                : (float) $cuotaEntera;

                            $pago = min($restantePorPagar, $monto);
                            $restantePorPagar -= $pago;

                            DB::table('modulos_participantes')->where('id', $row->id)->update([
                                'modulo_id' => $nuevoModId,
                                'deuda' => $monto,
                                'descuento' => 0,
                                'total_pagado' => $pago,
                            ]);
                        }
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Error al cambiar plan: ' . $e->getMessage()], 500);
            }

            $planLabel = $nuevoPlan === 'contado' ? 'Contado' : 'Regular';
            return response()->json([
                'success' => true,
                'message' => "Plan cambiado a {$planLabel}. Matrícula: Bs {$deudaMatricula}, Colegiatura: Bs {$deudaColegiatura}. Pagos conservados: Bs {$totalPagadoCol}.",
                'plan' => $nuevoPlan,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function buildStats($inscripciones, $programas, $pagosMap, $deudasMap, $modulosCount)
    {
        $inscripPorPrograma = $inscripciones->groupBy('programa_id');
        $noEjecutadoIds = $programas->where('estado_nombre', 'No Ejecutado')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        $totMatBruto = 0; $totMatDesc = 0; $totMatPagado = 0;
        $totColBruto = 0; $totColDesc = 0; $totColPagado = 0;
        $totMatBruto_ne = 0; $totMatDesc_ne = 0; $totMatPagado_ne = 0;
        $totColBruto_ne = 0; $totColDesc_ne = 0; $totColPagado_ne = 0;
        foreach ($inscripciones as $i) {
            $isNE = in_array((int) $i->programa_id, $noEjecutadoIds);
            $mb = (float) $i->matricula;
            $md = (float) ($i->descuento_matricula ?? 0);
            $cb = (float) $i->monto_programa;
            $cd = (float) ($i->descuento_colegiatura ?? 0);
            $mp = (float) ($pagosMap[$i->programa_id][$i->participante_id]['Matricula'] ?? 0);
            $cp = (float) ($pagosMap[$i->programa_id][$i->participante_id]['Colegiatura'] ?? 0);

            if ($isNE) {
                $totMatBruto_ne += $mb; $totMatDesc_ne += $md;
                $totColBruto_ne += $cb; $totColDesc_ne += $cd;
                $totMatPagado_ne += $mp; $totColPagado_ne += $cp;
            } else {
                $totMatBruto += $mb; $totMatDesc += $md;
                $totColBruto += $cb; $totColDesc += $cd;
                $totMatPagado += $mp; $totColPagado += $cp;
            }
        }

        $totMatNeto = max(0, $totMatBruto - $totMatDesc);
        $totColNeto = max(0, $totColBruto - $totColDesc);
        $totMatSaldo = max(0, $totMatNeto - $totMatPagado);
        $totColSaldo = max(0, $totColNeto - $totColPagado);
        $totGenDeuda = $totMatNeto + $totColNeto;
        $totGenPagado = $totMatPagado + $totColPagado;
        $totGenSaldo = $totMatSaldo + $totColSaldo;
        $pctCobrado = $totGenDeuda > 0 ? round(($totGenPagado / $totGenDeuda) * 100) : 0;

        $totMatNeto_ne = max(0, $totMatBruto_ne - $totMatDesc_ne);
        $totColNeto_ne = max(0, $totColBruto_ne - $totColDesc_ne);
        $totMatSaldo_ne = max(0, $totMatNeto_ne - $totMatPagado_ne);
        $totColSaldo_ne = max(0, $totColNeto_ne - $totColPagado_ne);
        $totGenDeuda_ne = $totMatNeto_ne + $totColNeto_ne;
        $totGenPagado_ne = $totMatPagado_ne + $totColPagado_ne;
        $totGenSaldo_ne = $totMatSaldo_ne + $totColSaldo_ne;

        $programasOut = [];
        foreach ($programas as $p) {
            $isNE = in_array((int) $p->id, $noEjecutadoIds);
            $insP = $inscripPorPrograma->get($p->id, collect());
            $mB = 0; $mD = 0; $mP = 0; $cB = 0; $cD = 0; $cP = 0;
            $mB_r = 0; $mD_r = 0; $mP_r = 0; $cB_r = 0; $cD_r = 0; $cP_r = 0;
            $numModulosProg = max(1, (int) ($modulosCount[$p->id]->num_modulos ?? 1));
            $inscritosReales = 0;
            foreach ($insP as $i) {
                $mB += (float) $i->matricula;
                $mD += (float) ($i->descuento_matricula ?? 0);
                $cB += (float) $i->monto_programa;
                $cD += (float) ($i->descuento_colegiatura ?? 0);
                $mP += (float) ($pagosMap[$p->id][$i->participante_id]['Matricula'] ?? 0);
                $cP += (float) ($pagosMap[$p->id][$i->participante_id]['Colegiatura'] ?? 0);

                $matNeto = (float) $i->matricula - (float) ($i->descuento_matricula ?? 0);
                $colNeto = (float) $i->monto_programa - (float) ($i->descuento_colegiatura ?? 0);
                $pagadoMat = (float) ($deudasMap[$p->id][$i->participante_id]['Matricula'] ?? 0);
                $pagadoCol = (float) ($deudasMap[$p->id][$i->participante_id]['Colegiatura'] ?? 0);
                $esReal = false;
                if ((int) $i->contado === 0) {
                    $esReal = $pagadoMat >= $matNeto && $pagadoCol > 0;
                } else {
                    $esReal = $colNeto > 0 && $pagadoCol >= ($colNeto / $numModulosProg);
                }

                if ($esReal) {
                    $inscritosReales++;
                    $mB_r += (float) $i->matricula;
                    $mD_r += (float) ($i->descuento_matricula ?? 0);
                    $cB_r += (float) $i->monto_programa;
                    $cD_r += (float) ($i->descuento_colegiatura ?? 0);
                    $mP_r += (float) ($pagosMap[$p->id][$i->participante_id]['Matricula'] ?? 0);
                    $cP_r += (float) ($pagosMap[$p->id][$i->participante_id]['Colegiatura'] ?? 0);
                }
            }
            $mN = max(0, $mB - $mD);
            $cN = max(0, $cB - $cD);
            $mS = max(0, $mN - $mP);
            $cS = max(0, $cN - $cP);
            $deudaT = $mN + $cN;
            $pagadoT = $mP + $cP;
            $saldoT = $mS + $cS;
            $pct = $deudaT > 0 ? round(($pagadoT / $deudaT) * 100) : 0;

            $mN_r = max(0, $mB_r - $mD_r);
            $cN_r = max(0, $cB_r - $cD_r);
            $mS_r = max(0, $mN_r - $mP_r);
            $cS_r = max(0, $cN_r - $cP_r);
            $deudaT_r = $mN_r + $cN_r;
            $pagadoT_r = $mP_r + $cP_r;
            $saldoT_r = $mS_r + $cS_r;
            $pct_r = $deudaT_r > 0 ? round(($pagadoT_r / $deudaT_r) * 100) : 0;

            $programasOut[] = [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo,
                'tipo_id' => $p->tipo_id,
                'tipo_nombre' => $p->tipo_nombre ?? 'Sin tipo',
                'estado_nombre' => $p->estado_nombre,
                'is_no_ejecutado' => $isNE,
                'inscritos' => $insP->count(),
                'inscritos_reales' => $inscritosReales,
                'mat_neto' => $mN, 'mat_pagado' => $mP, 'mat_saldo' => $mS,
                'col_neto' => $cN, 'col_pagado' => $cP, 'col_saldo' => $cS,
                'deuda_total' => $deudaT, 'pagado_total' => $pagadoT, 'saldo_total' => $saldoT,
                'pct' => $pct,
                'mat_neto_r' => $mN_r, 'mat_pagado_r' => $mP_r, 'mat_saldo_r' => $mS_r,
                'col_neto_r' => $cN_r, 'col_pagado_r' => $cP_r, 'col_saldo_r' => $cS_r,
                'deuda_total_r' => $deudaT_r, 'pagado_total_r' => $pagadoT_r, 'saldo_total_r' => $saldoT_r,
                'pct_r' => $pct_r,
            ];
        }

        $totMatNeto_r = 0; $totMatPagado_r = 0; $totMatSaldo_r = 0;
        $totColNeto_r = 0; $totColPagado_r = 0; $totColSaldo_r = 0;
        $totInscritosReales = 0;
        foreach ($programasOut as $po) {
            $totMatNeto_r += $po['mat_neto_r'];
            $totMatPagado_r += $po['mat_pagado_r'];
            $totMatSaldo_r += $po['mat_saldo_r'];
            $totColNeto_r += $po['col_neto_r'];
            $totColPagado_r += $po['col_pagado_r'];
            $totColSaldo_r += $po['col_saldo_r'];
            $totInscritosReales += $po['inscritos_reales'];
        }
        $totGenDeuda_r = $totMatNeto_r + $totColNeto_r;
        $totGenPagado_r = $totMatPagado_r + $totColPagado_r;
        $totGenSaldo_r = $totMatSaldo_r + $totColSaldo_r;
        $pctCobrado_r = $totGenDeuda_r > 0 ? round(($totGenPagado_r / $totGenDeuda_r) * 100) : 0;

        $resumen = compact(
            'totMatBruto', 'totMatDesc', 'totMatNeto', 'totMatPagado', 'totMatSaldo',
            'totColBruto', 'totColDesc', 'totColNeto', 'totColPagado', 'totColSaldo',
            'totGenDeuda', 'totGenPagado', 'totGenSaldo', 'pctCobrado',
            'totMatNeto_r', 'totMatPagado_r', 'totMatSaldo_r',
            'totColNeto_r', 'totColPagado_r', 'totColSaldo_r',
            'totGenDeuda_r', 'totGenPagado_r', 'totGenSaldo_r', 'pctCobrado_r',
            'totInscritosReales',
            'totMatNeto_ne', 'totMatPagado_ne', 'totMatSaldo_ne',
            'totColNeto_ne', 'totColPagado_ne', 'totColSaldo_ne',
            'totGenDeuda_ne', 'totGenPagado_ne', 'totGenSaldo_ne'
        );

        // Agrupar por tipo
        $porTipo = [];
        foreach ($programasOut as $p) {
            $tk = $p['tipo_id'] ?? 'sin-tipo';
            if (!isset($porTipo[$tk])) {
                $porTipo[$tk] = [
                    'tipo_id' => $tk,
                    'tipo_nombre' => $p['tipo_nombre'],
                    'programas' => [],
                ];
            }
            $porTipo[$tk]['programas'][] = $p;
        }
        $porTipo = array_values($porTipo);
        usort($porTipo, fn($a, $b) => count($b['programas']) <=> count($a['programas']));

        return compact('resumen', 'porTipo');
    }
}
