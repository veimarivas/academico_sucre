<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobantePublicidad;
use App\Models\PresupuestoPublicidad;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicidadController extends Controller
{
    private array $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    public function index(Request $request)
    {
        $gestion = (int) $request->get('gestion', now()->year);
        $mes = (int) $request->get('mes', now()->month);
        $quincena = $request->get('quincena', 'todas');
        $asesorId = $request->get('asesor_id');
        $estado = $request->get('estado', 'todos');

        $gestiones = collect(range(now()->year - 2, now()->year + 1))->reverse()->values();
        $meses = $this->meses;

        $baseAsesores = fn($cargos) => DB::table('asesores')
            ->join('cargos', 'cargos.id', '=', 'asesores.cargo_id')
            ->where('asesores.estado', 'Habilitado')
            ->whereIn('asesores.cargo_id', $cargos)
            ->orderBy('cargos.nombre')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->orderBy('asesores.ci')
            ->select('asesores.id', 'asesores.nombres', 'asesores.apellidos', 'asesores.ci', 'cargos.nombre as cargo_nombre');

        $asesores = $baseAsesores([1, 2, 3, 4, 5])->get();
        $asesoresPrincipales = $baseAsesores([1, 2])->get();
        $asesoresDerivados = $baseAsesores([3, 4, 5])->get();

        $comprobantesQ = DB::table('marketing_comprobantes_publicidad as c')
            ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
            ->leftJoin('users as v', 'v.id', '=', 'c.verificado_por')
            ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
            ->where('c.gestion', $gestion)
            ->where('c.mes', $mes)
            ->when($quincena !== 'todas', fn($q) => $q->where('c.quincena', (int) $quincena))
            ->when($asesorId, fn($q) => $q->where('c.asesor_id', $asesorId))
            ->when($estado !== 'todos', fn($q) => $q->where('c.estado', $estado))
            ->select(
                'c.*',
                'a.nombres as asesor_nombres',
                'a.apellidos as asesor_apellidos',
                'v.fullname as verificador_nombre',
                'prg.nombre as programa_nombre'
            )
            ->orderBy('a.apellidos')
            ->orderBy('a.nombres')
            ->orderBy('c.fecha_inicio', 'desc')
            ->orderBy('c.id', 'desc');

        $comprobantes = $comprobantesQ->get();

        // Resumen por quincena en el mes
        $resumenQuincenas = DB::table('marketing_comprobantes_publicidad')
            ->where('gestion', $gestion)->where('mes', $mes)
            ->when($asesorId, fn($q) => $q->where('asesor_id', $asesorId))
            ->selectRaw('quincena,
                COUNT(*) as total_recibos,
                SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE monto END) as total_gastado,
                SUM(CASE WHEN estado = "aprobado" THEN monto WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END) as total_aprobado,
                SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END) as total_aprobado_obs,
                SUM(CASE WHEN estado = "pendiente" THEN monto ELSE 0 END) as total_pendiente,
                SUM(CASE WHEN estado = "rechazado" THEN monto ELSE 0 END) as total_rechazado
            ')
            ->groupBy('quincena')
            ->get()
            ->keyBy('quincena');

        $programas = DB::table('programas')->where('estado_id', 4)->orderBy('nombre')->get(['id', 'nombre', 'codigo']);
        $presupuestos = DB::table('marketing_presupuestos_publicidad as p')
            ->leftJoin('asesores as s', 's.id', '=', 'p.supervisor_id')
            ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->where('p.gestion', $gestion)->where('p.mes', $mes)
            ->when($quincena !== 'todas', fn($q) => $q->where('p.quincena', (int) $quincena))
            ->when($asesorId, function ($q) use ($asesorId) {
                $q->where(function ($qq) use ($asesorId) {
                    $qq->where('p.asesor_id', $asesorId)->orWhere('p.supervisor_id', $asesorId);
                });
            })
            ->select(
                'p.*',
                's.nombres as sup_nombres', 's.apellidos as sup_apellidos',
                'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos'
            )
            ->selectRaw('(SELECT COUNT(*) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_comprobantes')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE c.monto END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado" THEN c.monto WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado_obs')
            ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id AND c.estado = "pendiente") as total_pendiente')
            ->selectRaw('(SELECT nombre FROM cargos WHERE id = (SELECT cargo_id FROM asesores WHERE id = p.asesor_id)) as cargo_nombre')
            ->selectRaw('(SELECT cargo_id FROM asesores WHERE id = p.asesor_id) as cargo_id')
            ->orderByRaw('CASE WHEN p.presupuesto_origen_id IS NULL THEN 0 ELSE 1 END, p.quincena')
            ->get();

        // Comprobantes agrupados por presupuesto_id para el listado anidado
        $comprobantesPorPresupuesto = $comprobantes->groupBy('presupuesto_id');

        // Presupuestos derivados agrupados por su presupuesto_origen_id
        $derivadosPorPresupuesto = $presupuestos->filter(fn($p) => $p->presupuesto_origen_id)->groupBy('presupuesto_origen_id');

        // Agrupar presupuestos por asesor
        $presupuestosPorAsesor = collect();
        $presupuestosGenerales = collect();

        foreach ($presupuestos as $p) {
            $grupo = $p->asesor_id ? (int) $p->asesor_id : 'general';
            $montoDerivado = $derivadosPorPresupuesto->has($p->id) ? (float) $derivadosPorPresupuesto->get($p->id)->sum('monto_asignado') : 0;
            $item = (object) [
                'id' => $p->id,
                'supervisor_id' => $p->supervisor_id,
                'asesor_id' => $p->asesor_id,
                'presupuesto_origen_id' => $p->presupuesto_origen_id,
                'gestion' => $p->gestion,
                'mes' => $p->mes,
                'quincena' => $p->quincena,
                'monto_asignado' => $p->monto_asignado,
                'monto_original' => (float) $p->monto_asignado + $montoDerivado,
                'monto_derivado_total' => $montoDerivado,
                'monto_reembolsado' => $p->monto_reembolsado,
                'estado' => $p->estado,
                'observaciones' => $p->observaciones,
                'created_by' => $p->created_by,
                'created_at' => $p->created_at,
                'sup_nombres' => $p->sup_nombres,
                'sup_apellidos' => $p->sup_apellidos,
                'ase_nombres' => $p->ase_nombres,
                'ase_apellidos' => $p->ase_apellidos,
                'cargo_nombre' => $p->cargo_nombre ?? null,
                'cargo_id' => $p->cargo_id ?? null,
                'total_comprobantes' => $p->total_comprobantes,
                'total_gastado' => $p->total_gastado,
                'total_aprobado' => $p->total_aprobado,
                'total_aprobado_obs' => $p->total_aprobado_obs,
                'total_pendiente' => $p->total_pendiente,
                'periodo_cerrado' => $p->periodo_cerrado ?? false,
            ];

            if ($grupo === 'general') {
                $presupuestosGenerales->push($item);
            } else {
                if (!$presupuestosPorAsesor->has($grupo)) {
                    $presupuestosPorAsesor->put($grupo, (object) [
                        'asesor_id' => $p->asesor_id,
                        'cargo_id' => $p->cargo_id ?? null,
                        'nombres' => $p->ase_nombres,
                        'apellidos' => $p->ase_apellidos,
                        'presupuestos' => collect(),
                        'total_original' => 0,
                        'total_asignado' => 0,
                        'total_derivado' => 0,
                        'total_gastado' => 0,
                        'total_aprobado' => 0,
                        'total_aprobado_obs' => 0,
                        'total_pendiente' => 0,
                        'total_comprobantes' => 0,
                    ]);
                }
                $g = $presupuestosPorAsesor->get($grupo);
                $g->presupuestos->push($item);
                $g->total_original += (float) $item->monto_original;
                $g->total_asignado += (float) $p->monto_asignado;
                $g->total_derivado += (float) $item->monto_derivado_total;
                $g->total_gastado += (float) $p->total_gastado;
                $g->total_aprobado += (float) $p->total_aprobado;
                $g->total_aprobado_obs += (float) $p->total_aprobado_obs;
                $g->total_pendiente += (float) $p->total_pendiente;
                $g->total_comprobantes += (int) $p->total_comprobantes;
            }
        }

        return view('admin.marketing.publicidad.index', [
            'gestion' => $gestion,
            'mes' => $mes,
            'quincena' => $quincena,
            'asesorId' => $asesorId,
            'estado' => $estado,
            'gestiones' => $gestiones,
            'meses' => $meses,
            'asesores' => $asesores,
            'comprobantes' => $comprobantes,
            'comprobantesPorPresupuesto' => $comprobantesPorPresupuesto,
            'resumenQuincenas' => $resumenQuincenas,
            'presupuestos' => $presupuestos,
            'presupuestosPorAsesor' => $presupuestosPorAsesor,
            'presupuestosGenerales' => $presupuestosGenerales,
            'asesoresPrincipales' => $asesoresPrincipales,
            'asesoresDerivados' => $asesoresDerivados,
            'derivadosPorPresupuesto' => $derivadosPorPresupuesto,
            'programas' => $programas,
        ]);
    }

    public function presupuestosIndex(Request $request)
    {
        $gestiones = collect(range(now()->year - 2, now()->year + 1))->reverse()->values();
        $meses = $this->meses;

        $gestionFiltro = $request->get('gestion');

        $raw = DB::table('marketing_presupuestos_publicidad as p')
            ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->leftJoin('cargos as c', 'c.id', '=', 'a.cargo_id')
            ->select(
                'p.gestion', 'p.mes', 'p.quincena',
                DB::raw('COUNT(DISTINCT p.id) as total_presupuestos'),
                DB::raw('COALESCE(SUM(p.monto_asignado),0) as total_asignado'),
                DB::raw('(SELECT COALESCE(SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE monto END),0) FROM marketing_comprobantes_publicidad WHERE gestion = p.gestion AND mes = p.mes AND quincena = p.quincena) as total_gastado'),
                DB::raw('(SELECT COALESCE(SUM(CASE WHEN estado = "aprobado" THEN monto WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad WHERE gestion = p.gestion AND mes = p.mes AND quincena = p.quincena) as total_aprobado'),
                DB::raw('(SELECT COALESCE(SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad WHERE gestion = p.gestion AND mes = p.mes AND quincena = p.quincena) as total_aprobado_obs'),
                DB::raw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad WHERE gestion = p.gestion AND mes = p.mes AND quincena = p.quincena AND estado = "pendiente") as total_pendiente'),
                DB::raw('(SELECT COUNT(*) FROM marketing_comprobantes_publicidad WHERE gestion = p.gestion AND mes = p.mes AND quincena = p.quincena) as total_comprobantes')
            )
            ->when($gestionFiltro, fn($q) => $q->where('p.gestion', (int) $gestionFiltro))
            ->groupBy('p.gestion', 'p.mes', 'p.quincena')
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.mes', 'desc')
            ->orderBy('p.quincena', 'desc')
            ->get();

        // For each period, get the list of principales (cargo 1,2) who have presupuestos
        $principalesPorPeriodo = collect();
        $periodos = DB::table('marketing_presupuestos_publicidad as p')
            ->join('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->join('cargos as c', 'c.id', '=', 'a.cargo_id')
            ->whereIn('c.id', [1, 2])
            ->select('p.gestion', 'p.mes', 'p.quincena', 'a.id', 'a.nombres', 'a.apellidos', 'c.nombre as cargo_nombre')
            ->when($gestionFiltro, fn($q) => $q->where('p.gestion', (int) $gestionFiltro))
            ->distinct()
            ->orderBy('a.apellidos')
            ->get();

        foreach ($periodos as $pr) {
            $key = $pr->gestion . '-' . $pr->mes . '-' . $pr->quincena;
            if (!$principalesPorPeriodo->has($key)) {
                $principalesPorPeriodo->put($key, collect());
            }
            $principalesPorPeriodo->get($key)->push($pr);
        }

        // Get periodo_cerrado status for each period
        $periodoStatus = DB::table('marketing_presupuestos_publicidad')
            ->select('gestion', 'mes', 'quincena', DB::raw('MAX(periodo_cerrado) as cerrado'))
            ->when($gestionFiltro, fn($q) => $q->where('gestion', (int) $gestionFiltro))
            ->groupBy('gestion', 'mes', 'quincena')
            ->get()
            ->keyBy(fn($s) => $s->gestion . '-' . $s->mes . '-' . $s->quincena);

        // Agrupar por gestion > mes
        $grupos = collect();
        foreach ($raw as $r) {
            $key = $r->gestion . '-' . $r->mes;
            if (!$grupos->has($key)) {
                $grupos->put($key, (object) [
                    'gestion' => $r->gestion,
                    'mes' => $r->mes,
                    'q1' => null,
                    'q2' => null,
                ]);
            }
            $pk = $r->gestion . '-' . $r->mes . '-' . $r->quincena;
            $status = $periodoStatus->get($pk);
            $item = (object) [
                'gestion' => $r->gestion,
                'mes' => $r->mes,
                'quincena' => $r->quincena,
                'total_asignado' => (float) $r->total_asignado,
                'total_gastado' => (float) $r->total_gastado,
                'total_aprobado' => (float) $r->total_aprobado,
                'total_aprobado_obs' => (float) $r->total_aprobado_obs,
                'total_pendiente' => (float) $r->total_pendiente,
                'total_comprobantes' => (int) $r->total_comprobantes,
                'total_presupuestos' => (int) $r->total_presupuestos,
                'periodo_cerrado' => $status ? (bool) $status->cerrado : false,
                'principales' => $principalesPorPeriodo->get($pk, collect()),
            ];
            if ((int) $r->quincena === 1) {
                $grupos->get($key)->q1 = $item;
            } else {
                $grupos->get($key)->q2 = $item;
            }
        }

        // ── Detalle por presupuesto (uno por asesor) para el interior de cada quincena ──
        // Los comprobantes se vinculan por presupuesto_id, así que el gasto y lo aprobado
        // se calculan por asignación y no por periodo.
        $compsSub = DB::table('marketing_comprobantes_publicidad')
            ->whereNotNull('presupuesto_id')
            ->select(
                'presupuesto_id',
                DB::raw('COUNT(*) as n'),
                DB::raw('COALESCE(SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE monto END), 0) as gastado'),
                DB::raw('COALESCE(SUM(CASE WHEN estado = "aprobado" THEN monto WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END), 0) as aprobado'),
                DB::raw('COALESCE(SUM(CASE WHEN estado = "aprobado_obs" THEN COALESCE(monto_aprobado, monto) ELSE 0 END), 0) as aprobado_obs'),
                DB::raw('COALESCE(SUM(CASE WHEN estado = "pendiente" THEN monto ELSE 0 END), 0) as pendiente')
            )
            ->groupBy('presupuesto_id');

        $derivSub = DB::table('marketing_presupuestos_publicidad')
            ->whereNotNull('presupuesto_origen_id')
            ->select(
                'presupuesto_origen_id',
                DB::raw('COUNT(*) as n_deriv'),
                DB::raw('COALESCE(SUM(monto_asignado), 0) as monto_derivado')
            )
            ->groupBy('presupuesto_origen_id');

        $detalle = DB::table('marketing_presupuestos_publicidad as p')
            ->leftJoin('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->leftJoin('asesores as s', 's.id', '=', 'p.supervisor_id')
            ->leftJoinSub($compsSub, 'c', 'c.presupuesto_id', '=', 'p.id')
            ->leftJoinSub($derivSub, 'd', 'd.presupuesto_origen_id', '=', 'p.id')
            ->select(
                'p.id', 'p.gestion', 'p.mes', 'p.quincena', 'p.monto_asignado', 'p.monto_reembolsado',
                'p.estado', 'p.periodo_cerrado', 'p.presupuesto_origen_id', 'p.asesor_id', 'p.supervisor_id',
                'p.observaciones', 'p.archivo_derivacion_path', 'p.archivo_derivacion_original',
                'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos',
                's.nombres as sup_nombres', 's.apellidos as sup_apellidos',
                DB::raw('COALESCE(c.n, 0) as total_comprobantes'),
                DB::raw('COALESCE(c.gastado, 0) as total_gastado'),
                DB::raw('COALESCE(c.aprobado, 0) as total_aprobado'),
                DB::raw('COALESCE(c.aprobado_obs, 0) as total_aprobado_obs'),
                DB::raw('COALESCE(c.pendiente, 0) as total_pendiente'),
                DB::raw('COALESCE(d.n_deriv, 0) as num_derivados'),
                DB::raw('COALESCE(d.monto_derivado, 0) as monto_derivado_total')
            )
            ->when($gestionFiltro, fn($q) => $q->where('p.gestion', (int) $gestionFiltro))
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.mes', 'desc')
            ->orderBy('p.quincena', 'desc')
            ->orderBy('a.apellidos')
            ->orderBy('a.nombres')
            ->get();

        // Se puede eliminar un presupuesto solo si no tiene gastos, ni derivaciones,
        // y su periodo sigue abierto.
        $detalle->each(function ($x) {
            $x->puede_eliminar = ((int) $x->total_comprobantes === 0)
                && ((int) $x->num_derivados === 0)
                && !$x->periodo_cerrado;
        });

        $presupuestosPorPeriodo = $detalle->groupBy(fn($x) => $x->gestion . '-' . $x->mes . '-' . $x->quincena);
        $totalEliminables = $detalle->where('puede_eliminar', true)->count();

        // Adjuntar el detalle a cada quincena del grupo
        foreach ($grupos as $gr) {
            foreach (['q1', 'q2'] as $qk) {
                if ($gr->$qk) {
                    $pk = $gr->$qk->gestion . '-' . $gr->$qk->mes . '-' . $gr->$qk->quincena;
                    $gr->$qk->presupuestos = $presupuestosPorPeriodo->get($pk, collect());
                }
            }
        }

        return view('admin.marketing.publicidad.presupuestos', compact(
            'grupos', 'gestiones', 'meses', 'gestionFiltro', 'totalEliminables'
        ));
    }

    public function autogestionIndex(Request $request)
    {
        $gestion = (int) $request->get('gestion', now()->year);
        $mes = $request->has('mes') ? (int) $request->get('mes') : 0;
        if (!$request->has('mes')) {
            $ultimoMes = DB::table('marketing_gastos_autogestion')->where('gestion', $gestion)->max('mes');
            $mes = $ultimoMes ?? (int) now()->month;
        }
        $asesorId = $request->get('asesor_id');
        $estado = $request->get('estado', 'todos');

        $gestiones = collect(range(now()->year - 2, now()->year + 1))->reverse()->values();
        $meses = $this->meses;

        $baseAsesores = fn($cargos) => DB::table('asesores')
            ->join('cargos', 'cargos.id', '=', 'asesores.cargo_id')
            ->where('asesores.estado', 'Habilitado')
            ->whereIn('asesores.cargo_id', $cargos)
            ->orderBy('cargos.nombre')
            ->orderBy('asesores.apellidos')
            ->orderBy('asesores.nombres')
            ->orderBy('asesores.ci')
            ->select('asesores.id', 'asesores.nombres', 'asesores.apellidos', 'asesores.ci', 'cargos.nombre as cargo_nombre');

        $asesores = $baseAsesores([1, 2, 3, 4, 5])->get();
        $programas = \App\Models\Programa::where('estado_id', 4)->orderBy('nombre')->get(['id', 'nombre', 'codigo']);

        $query = DB::table('marketing_gastos_autogestion as g')
            ->join('asesores as a', 'a.id', '=', 'g.asesor_id')
            ->leftJoin('programas as prg', 'prg.id', '=', 'g.programa_id')
            ->where('g.gestion', $gestion)
            ->when($mes > 0, fn($q) => $q->where('g.mes', $mes))
            ->when($asesorId, fn($q) => $q->where('g.asesor_id', $asesorId))
            ->when($estado !== 'todos', fn($q) => $q->where('g.estado', $estado))
            ->select(
                'g.*',
                'a.nombres as asesor_nombres',
                'a.apellidos as asesor_apellidos',
                'prg.nombre as programa_nombre'
            )
            ->orderBy('g.created_at', 'desc');

        $totalQuery = clone $query;
        $gastos = $query->paginate(10)->withQueryString();
        $allGastos = $totalQuery->get();

        $totalMonto = $allGastos->sum('monto');
        $totalPendiente = $allGastos->where('estado', 'pendiente')->sum('monto');
        $totalAprobado = $allGastos->whereIn('estado', ['aprobado'])->sum('monto');

        if ($request->ajax()) {
            $rowsHtml = view('admin.marketing.publicidad._autogestion_rows', compact('gastos', 'meses'))->render();
            $paginationHtml = $gastos->links('admin.marketing.publicidad._autogestion_pagination')->render();
            return response()->json([
                'rows_html' => $rowsHtml,
                'pagination_html' => $paginationHtml,
                'totalMonto' => $totalMonto,
                'totalPendiente' => $totalPendiente,
                'totalAprobado' => $totalAprobado,
                'totalRegistros' => $allGastos->count(),
            ]);
        }

        return view('admin.marketing.publicidad.autogestion_index', compact(
            'gastos', 'gestion', 'mes', 'asesorId', 'estado',
            'gestiones', 'meses', 'asesores', 'programas',
            'totalMonto', 'totalPendiente', 'totalAprobado'
        ));
    }

    public function editAutogestion($id)
    {
        $gasto = \App\Models\GastoAutogestion::with(['asesor', 'programa'])->findOrFail($id);
        $programas = \App\Models\Programa::where('estado_id', 4)->orderBy('nombre')->get(['id', 'nombre', 'codigo']);
        return response()->json([
            'success' => true,
            'gasto' => $gasto,
            'programas' => $programas,
        ]);
    }

    /**
     * Registra un gasto de autogestión desde el panel de Marketing (el asesor
     * responsable se elige en el formulario). Sube los archivos de forma segura
     * y sólo crea el registro si ambos se guardaron correctamente.
     */
    public function storeAutogestion(Request $request)
    {
        if ($errorSubida = $this->mensajeErrorSubida($request)) {
            return back()->withInput()->with('error', $errorSubida);
        }

        $validated = $request->validate([
            'asesor_id' => 'required|exists:asesores,id',
            'gestion' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'monto' => 'required|numeric|min:0.01|max:9999999.99',
            'descripcion' => 'required|string|max:500',
            'plataforma' => 'nullable|string|max:50',
            'campana_nombre' => 'nullable|string|max:255',
            'enlace_campana' => 'nullable|url|max:500',
            'pagina_facebook' => 'nullable|url|max:500',
            'programa_id' => 'nullable|exists:programas,id',
            'estado' => 'nullable|in:pendiente,aprobado,rechazado',
            'observaciones' => 'nullable|string|max:500',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'archivo_debito' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        // Se guardan los archivos ANTES de crear el registro: si el disco público no
        // permite escribir, no debe quedar un gasto sin su comprobante.
        $rutaComprobante = null;
        if ($request->hasFile('archivo')) {
            $rutaComprobante = $this->guardarArchivoAutogestion($request->file('archivo'));

            if (!$rutaComprobante) {
                return back()->withInput()->with('error', 'No se pudo guardar el comprobante en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). El gasto no fue registrado.');
            }
        }

        $rutaDebito = null;
        if ($request->hasFile('archivo_debito')) {
            $rutaDebito = $this->guardarArchivoAutogestion($request->file('archivo_debito'));

            if (!$rutaDebito) {
                if ($rutaComprobante) {
                    Storage::disk('public')->delete($rutaComprobante);
                }

                return back()->withInput()->with('error', 'No se pudo guardar el comprobante de débito en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). El gasto no fue registrado.');
            }
        }

        $gasto = new \App\Models\GastoAutogestion();
        $gasto->asesor_id = $validated['asesor_id'];
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
        $gasto->estado = $validated['estado'] ?? 'pendiente';
        $gasto->observaciones = $validated['observaciones'] ?? null;

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

        return back()->with('success', 'Gasto de autogestión registrado correctamente.');
    }

    /**
     * Sirve el archivo (comprobante o débito) de un gasto por una ruta autenticada.
     * Evita depender del enlace directo /storage, que depende del symlink y del
     * docroot configurados en el servidor.
     */
    public function verArchivoAutogestion($id, $tipo)
    {
        $gasto = \App\Models\GastoAutogestion::findOrFail($id);

        $ruta = match ($tipo) {
            'debito' => $gasto->archivo_debito_path,
            default => $gasto->archivo_path,
        };

        abort_unless($ruta, 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($ruta), 404);

        $original = $tipo === 'debito' ? $gasto->archivo_debito_original : $gasto->archivo_original;
        $mime = $tipo === 'debito' ? $gasto->archivo_debito_mime : $gasto->archivo_mime;

        return $disk->response($ruta, $original ?: basename($ruta), [
            'Content-Type' => $mime ?: $disk->mimeType($ruta),
        ], 'inline');
    }

    public function updateAutogestion(Request $request, $id)
    {
        $gasto = \App\Models\GastoAutogestion::findOrFail($id);

        // Fallos de subida que PHP reporta en $_FILES y que Laravel NO convierte en
        // error de validación (upload_max_filesize/post_max_size, permisos, etc.).
        if ($errorSubida = $this->mensajeErrorSubida($request)) {
            return back()->withInput()->with('error', $errorSubida);
        }

        $validated = $request->validate([
            'gestion' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'monto' => 'required|numeric|min:0.01|max:9999999.99',
            'descripcion' => 'required|string|max:500',
            'plataforma' => 'nullable|string|max:50',
            'campana_nombre' => 'nullable|string|max:255',
            'enlace_campana' => 'nullable|url|max:500',
            'pagina_facebook' => 'nullable|url|max:500',
            'programa_id' => 'nullable|exists:programas,id',
            'estado' => 'nullable|in:pendiente,aprobado,rechazado',
            'observaciones' => 'nullable|string|max:500',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'archivo_debito' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

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
        $gasto->estado = $validated['estado'] ?? $gasto->estado;
        $gasto->observaciones = $validated['observaciones'] ?? null;

        // Primero se guardan los archivos nuevos: store() devuelve false si el disco
        // público no permite escribir, y antes eso borraba el archivo anterior y dejaba
        // el gasto sin comprobante (la columna "Archivo" pasaba a mostrar "—").
        $rutaComprobante = null;
        if ($request->hasFile('archivo')) {
            $rutaComprobante = $this->guardarArchivoAutogestion($request->file('archivo'));

            if (!$rutaComprobante) {
                return back()->withInput()->with('error', 'No se pudo guardar el comprobante en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). No se aplicaron cambios.');
            }
        }

        $rutaDebito = null;
        if ($request->hasFile('archivo_debito')) {
            $rutaDebito = $this->guardarArchivoAutogestion($request->file('archivo_debito'));

            if (!$rutaDebito) {
                if ($rutaComprobante) {
                    Storage::disk('public')->delete($rutaComprobante);
                }

                return back()->withInput()->with('error', 'No se pudo guardar el comprobante de débito en el servidor (carpeta "public/storage/publicidad/autogestion" inexistente o sin permisos de escritura). No se aplicaron cambios.');
            }
        }

        if ($rutaComprobante) {
            if ($gasto->archivo_path) {
                Storage::disk('public')->delete($gasto->archivo_path);
            }
            $file = $request->file('archivo');
            $gasto->archivo_path = $rutaComprobante;
            $gasto->archivo_original = $file->getClientOriginalName();
            $gasto->archivo_mime = $file->getMimeType();
        }

        if ($rutaDebito) {
            if ($gasto->archivo_debito_path) {
                Storage::disk('public')->delete($gasto->archivo_debito_path);
            }
            $file = $request->file('archivo_debito');
            $gasto->archivo_debito_path = $rutaDebito;
            $gasto->archivo_debito_original = $file->getClientOriginalName();
            $gasto->archivo_debito_mime = $file->getMimeType();
        }

        $gasto->save();

        return back()->with('success', 'Gasto de autogestión actualizado correctamente.');
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

    public function destroyAutogestion($id)
    {
        $gasto = \App\Models\GastoAutogestion::findOrFail($id);

        if ($gasto->archivo_path) {
            Storage::disk('public')->delete($gasto->archivo_path);
        }
        if ($gasto->archivo_debito_path) {
            Storage::disk('public')->delete($gasto->archivo_debito_path);
        }

        $gasto->delete();

        return back()->with('success', 'Gasto de autogestión eliminado correctamente.');
    }

    public function reportePdf(Request $request)
    {
        $gestion = (int) $request->get('gestion', now()->year);
        $mes = (int) $request->get('mes', now()->month);
        $quincena = (int) $request->get('quincena', now()->day <= 15 ? 1 : 2);
        $meses = $this->meses;

        // Verify period is closed
        $periodoCerrado = DB::table('marketing_presupuestos_publicidad')
            ->where('gestion', $gestion)->where('mes', $mes)->where('quincena', $quincena)
            ->where('periodo_cerrado', true)
            ->exists();
        if (!$periodoCerrado) {
            return back()->with('error', 'El periodo debe estar cerrado para generar el reporte.');
        }

        $periodo = $meses[$mes] . ' ' . $gestion;
        $quincenaLabel = 'Q' . $quincena . ' (' . ($quincena === 1 ? '1-15' : '16-fin') . ')';
        $nombrePeriodo = str_replace(['/', ' ', '\\'], '_', $periodo);

        // Presupuestos origen de cargos 1,2 en el periodo
        $presupuestos = DB::table('marketing_presupuestos_publicidad as p')
            ->join('asesores as a', 'a.id', '=', 'p.asesor_id')
            ->join('cargos as c', 'c.id', '=', 'a.cargo_id')
            ->whereIn('c.id', [1, 2])
            ->where('p.estado', 'activo')
            ->where('p.gestion', $gestion)
            ->where('p.mes', $mes)
            ->where('p.quincena', $quincena)
            ->whereNull('p.presupuesto_origen_id')
            ->select(
                'p.*',
                'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos',
                'c.nombre as cargo_nombre', 'c.id as cargo_id'
            )
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE c.monto END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
            ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado" THEN c.monto WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado')
            ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad WHERE presupuesto_id = p.id AND estado = "pendiente") as total_pendiente')
            ->orderBy('a.apellidos')
            ->get();

        if ($presupuestos->isEmpty()) {
            return back()->with('error', 'No hay presupuestos principales para este periodo.');
        }

        $principalesIds = $presupuestos->pluck('id');

        // Presupuestos derivados agrupados por origen
        $derivados = collect();
        if ($principalesIds->isNotEmpty()) {
            $derivados = DB::table('marketing_presupuestos_publicidad as p')
                ->join('asesores as a', 'a.id', '=', 'p.asesor_id')
                ->join('cargos as c', 'c.id', '=', 'a.cargo_id')
                ->whereIn('p.presupuesto_origen_id', $principalesIds)
                ->where('p.estado', 'activo')
                ->select(
                    'p.*',
                    'a.nombres as ase_nombres', 'a.apellidos as ase_apellidos',
                    'c.nombre as cargo_nombre'
                )
                ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE c.monto END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_gastado')
                ->selectRaw('(SELECT COALESCE(SUM(CASE WHEN c.estado = "aprobado" THEN c.monto WHEN c.estado = "aprobado_obs" THEN COALESCE(c.monto_aprobado, c.monto) ELSE 0 END),0) FROM marketing_comprobantes_publicidad c WHERE c.presupuesto_id = p.id) as total_aprobado')
                ->selectRaw('(SELECT COALESCE(SUM(monto),0) FROM marketing_comprobantes_publicidad WHERE presupuesto_id = p.id AND estado = "pendiente") as total_pendiente')
                ->get()
                ->groupBy('presupuesto_origen_id');
        }

        // Todos los comprobantes del periodo
        $todosIds = $principalesIds->merge($derivados->flatten()->pluck('id'))->unique();
        $comprobantes = collect();
        $comprobantesPorCampana = collect();
        if ($todosIds->isNotEmpty()) {
            $raw = DB::table('marketing_comprobantes_publicidad as c')
                ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
                ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
                ->whereIn('c.presupuesto_id', $todosIds)
                ->select(
                    'c.*',
                    'a.nombres as asesor_nombres', 'a.apellidos as asesor_apellidos',
                    'prg.nombre as programa_nombre'
                )
                ->orderBy('c.campana_nombre')
                ->orderBy('c.created_at', 'desc')
                ->get();

            $comprobantes = $raw->groupBy('presupuesto_id');
            foreach ($todosIds as $pid) {
                $comps = $comprobantes->get($pid, collect());
                $comprobantesPorCampana->put($pid, $comps->groupBy(fn($c) => $c->campana_nombre ?: 'Sin campaña'));
            }
        }

        // Previsualizaciones de archivos (base64 para el PDF)
        $previews = collect();
        $debitoPreviews = collect();
        foreach ($todosIds as $pid) {
            $comps = $comprobantes->get($pid, collect());
            foreach ($comps as $c) {
                $previews->put($c->id, $this->generarMiniatura($c->archivo_path, $c->archivo_original));
                $debitoPreviews->put($c->id, $c->archivo_debito_path ? $this->generarMiniatura($c->archivo_debito_path, $c->archivo_debito_original) : null);
            }
        }

        // Generar un PDF por cada asesor principal y empaquetar en ZIP
        $tempDir = storage_path('app/temp_reports_' . uniqid());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $pdfPaths = [];
        foreach ($presupuestos as $p) {
            $derivs = $derivados->get($p->id, collect());
            $idsPresupuesto = collect([$p->id])->merge($derivs->pluck('id'))->unique();

            $comprobantesFiltrados = $comprobantes->filter(fn($v, $key) => $idsPresupuesto->contains($key));
            $comprobantesPorCampanaFiltrados = $comprobantesPorCampana->filter(fn($v, $key) => $idsPresupuesto->contains($key));
            $derivadosFiltrados = collect([$p->id => $derivs]);

            $totalGeneralAprobado = (float) $p->total_aprobado;
            foreach ($derivs as $d) {
                $totalGeneralAprobado += (float) $d->total_aprobado;
            }

            $pdf = Pdf::loadView('admin.marketing.publicidad.reporte-pdf', [
                'presupuestos' => collect([$p]),
                'derivados' => $derivadosFiltrados,
                'comprobantesPorCampana' => $comprobantesPorCampanaFiltrados,
                'comprobantes' => $comprobantesFiltrados,
                'gestion' => $gestion,
                'mes' => $mes,
                'quincena' => $quincena,
                'periodo' => $periodo,
                'quincenaLabel' => $quincenaLabel,
                'totalGeneralAprobado' => $totalGeneralAprobado,
                'meses' => $meses,
                'previews' => $previews,
                'debitoPreviews' => $debitoPreviews,
            ]);

            $nombreAsesor = str_replace(['/', ' ', '\\', ','], '_', trim($p->ase_apellidos . '_' . $p->ase_nombres));
            $nombreArchivo = "reporte_{$nombreAsesor}_{$nombrePeriodo}_{$quincenaLabel}.pdf";
            $pdfPath = $tempDir . '/' . $nombreArchivo;
            $pdf->save($pdfPath);
            $pdfPaths[] = $pdfPath;
        }

        // Crear ZIP
        $zipFileName = "reportes_publicidad_{$nombrePeriodo}_{$quincenaLabel}.zip";
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        foreach ($pdfPaths as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Limpiar archivos temporales
        foreach ($pdfPaths as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function togglePeriodo(Request $request)
    {
        $data = $request->validate([
            'gestion' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'quincena' => 'required|integer|in:1,2',
            'cerrado' => 'required|boolean',
        ]);

        $updated = DB::table('marketing_presupuestos_publicidad')
            ->where('gestion', $data['gestion'])
            ->where('mes', $data['mes'])
            ->where('quincena', $data['quincena'])
            ->update(['periodo_cerrado' => $data['cerrado']]);

        $accion = $data['cerrado'] ? 'cerrado' : 'abierto';
        $msg = "Periodo {$data['gestion']}/{$data['mes']} Q{$data['quincena']} {$accion} correctamente.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'updated' => $updated]);
        }

        return back()->with('success', $msg);
    }

    public function store(Request $request)
    {
        $rules = [
            'presupuesto_id' => 'required|integer|exists:marketing_presupuestos_publicidad,id',
            'asesor_id' => 'required|integer|exists:asesores,id',
            'programa_id' => 'nullable|integer|exists:programas,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:500',
            'plataforma' => 'nullable|string|max:60',
            'campana_nombre' => 'nullable|string|max:200',
            'enlace_campana' => 'nullable|url|max:500',
            'pagina_facebook' => 'nullable|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'archivo_debito' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'gestion' => 'nullable|integer',
            'mes' => 'nullable|integer|min:1|max:12',
            'quincena' => 'nullable|integer|in:1,2',
            '_redirect_to' => 'nullable|string|in:profile',
        ];

        $messages = [
            'presupuesto_id.required' => 'Debe subir el comprobante desde una asignación de presupuesto.',
            'asesor_id.required' => 'Debe seleccionar el asesor que realizó el gasto.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'enlace_campana.url' => 'El enlace de la campaña debe ser una URL válida.',
            'archivo.mimes' => 'El comprobante debe ser PDF o imagen (JPG, PNG, WEBP).',
            'archivo.max' => 'El comprobante no debe superar 5MB.',
            'archivo_debito.mimes' => 'El comprobante de débito debe ser PDF o imagen (JPG, PNG, WEBP).',
            'archivo_debito.max' => 'El comprobante de débito no debe superar 5MB.',
        ];

        if ($request->expectsJson() || $request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();
        } else {
            $data = $request->validate($rules, $messages);
        }

        $presupuesto = PresupuestoPublicidad::findOrFail($data['presupuesto_id']);

        if ($presupuesto->periodo_cerrado) {
            $msg = 'El periodo de este presupuesto está cerrado. No se pueden subir nuevos comprobantes.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        if ($presupuesto->estado !== 'activo') {
            $msg = 'El presupuesto seleccionado ya está cerrado y no acepta nuevos comprobantes.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // Validate dates are within the period's quincena range
        $gestionReq = (int) ($data['gestion'] ?? $presupuesto->gestion);
        $mesReq = (int) ($data['mes'] ?? $presupuesto->mes);
        $quincenaReq = (int) ($data['quincena'] ?? $presupuesto->quincena);
        $inicio = Carbon::parse($data['fecha_inicio']);
        $fin = Carbon::parse($data['fecha_fin']);
        $inicioPeriodo = Carbon::create($gestionReq, $mesReq, $quincenaReq === 1 ? 1 : 16, 0, 0, 0)->subDays(2);
        $finPeriodo = Carbon::create($gestionReq, $mesReq, $quincenaReq === 1 ? 15 : Carbon::create($gestionReq, $mesReq)->endOfMonth()->day, 23, 59, 59);
        if ($inicio->lt($inicioPeriodo) || $fin->gt($finPeriodo)) {
            $msg = 'Las fechas deben estar dentro del rango de la quincena (' . $inicioPeriodo->format('d/m/Y') . ' - ' . $finPeriodo->format('d/m/Y') . ').';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg)->withInput();
        }

        $gestion = (int) $presupuesto->gestion;
        $mes = (int) $presupuesto->mes;
        $quincena = (int) $presupuesto->quincena;

        $file = $request->file('archivo');
        $ext = strtolower($file->getClientOriginalExtension());
        $nombreArchivo = 'pub_' . $data['asesor_id'] . '_' . time() . '_' . uniqid() . '.' . $ext;
        $path = $file->storeAs('publicidad/' . $gestion . '/' . str_pad($mes, 2, '0', STR_PAD_LEFT), $nombreArchivo, 'public');

        $debitoPath = null;
        $debitoOriginal = null;
        $debitoMime = null;
        if ($request->hasFile('archivo_debito')) {
            $debFile = $request->file('archivo_debito');
            $debExt = strtolower($debFile->getClientOriginalExtension());
            $debNombre = 'deb_' . $data['asesor_id'] . '_' . time() . '_' . uniqid() . '.' . $debExt;
            $debitoPath = $debFile->storeAs('publicidad/' . $gestion . '/' . str_pad($mes, 2, '0', STR_PAD_LEFT), $debNombre, 'public');
            $debitoOriginal = $debFile->getClientOriginalName();
            $debitoMime = $debFile->getMimeType();
        }

        $comprobante = ComprobantePublicidad::create([
            'asesor_id' => $data['asesor_id'],
            'presupuesto_id' => $presupuesto->id,
            'programa_id' => $data['programa_id'] ?? null,
            'gestion' => $gestion,
            'mes' => $mes,
            'quincena' => $quincena,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'monto' => $data['monto'],
            'descripcion' => $data['descripcion'],
            'plataforma' => $data['plataforma'] ?? 'Facebook',
            'campana_nombre' => $data['campana_nombre'] ?? null,
            'enlace_campana' => $data['enlace_campana'] ?? null,
            'pagina_facebook' => $data['pagina_facebook'] ?? null,
            'archivo_path' => $path,
            'archivo_original' => $file->getClientOriginalName(),
            'archivo_mime' => $file->getMimeType(),
            'archivo_debito_path' => $debitoPath,
            'archivo_debito_original' => $debitoOriginal,
            'archivo_debito_mime' => $debitoMime,
            'estado' => 'pendiente',
            'created_by' => optional($request->user())->id,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $comprobante->load('asesor');
            return response()->json([
                'success' => true,
                'message' => 'Comprobante subido correctamente.',
                'comprobante' => $comprobante,
                'html' => $this->renderComprobanteRow($comprobante, $presupuesto),
            ]);
        }

        $redirectTo = $data['_redirect_to'] ?? null;
        if ($redirectTo === 'profile') {
            return redirect()->route('admin.profile', ['reload_pub' => 1])
                ->with('success', 'Comprobante subido correctamente. Queda pendiente de verificación.');
        }

        return redirect()->route('admin.marketing.publicidad.index', [
            'gestion' => $gestion, 'mes' => $mes, 'quincena' => $quincena,
        ])->with('success', 'Comprobante subido correctamente. Queda pendiente de verificación.');
    }

    private function renderComprobanteRow($comprobante, $presupuesto, $idx = '(nuevo)')
    {
        $comprobante->loadMissing('asesor');
        $c = (object) [
            'id' => $comprobante->id,
            'asesor_id' => $comprobante->asesor_id,
            'presupuesto_id' => $comprobante->presupuesto_id,
            'programa_id' => $comprobante->programa_id,
            'programa_nombre' => $comprobante->programa?->nombre,
            'fecha_inicio' => $comprobante->fecha_inicio,
            'fecha_fin' => $comprobante->fecha_fin,
            'descripcion' => $comprobante->descripcion,
            'campana_nombre' => $comprobante->campana_nombre,
            'enlace_campana' => $comprobante->enlace_campana,
            'plataforma' => $comprobante->plataforma,
            'pagina_facebook' => $comprobante->pagina_facebook,
            'monto' => $comprobante->monto,
            'monto_aprobado' => $comprobante->monto_aprobado,
            'observaciones_contable' => $comprobante->observaciones_contable,
            'archivo_path' => $comprobante->archivo_path,
            'archivo_original' => $comprobante->archivo_original,
            'archivo_debito_path' => $comprobante->archivo_debito_path,
            'archivo_debito_original' => $comprobante->archivo_debito_original,
            'estado' => $comprobante->estado,
            'asesor_nombres' => $comprobante->asesor?->nombres,
            'asesor_apellidos' => $comprobante->asesor?->apellidos,
            'verificador_nombre' => optional($comprobante->verificador)->fullname,
            'created_at' => $comprobante->created_at,
        ];
        $mismoTitular = $presupuesto->asesor_id && (int)$c->asesor_id === (int)$presupuesto->asesor_id;
        return view('admin.marketing.publicidad._comprobante_row', [
            'c' => $c, 'p' => $presupuesto, 'mismoTitular' => $mismoTitular, 'idx' => $idx,
        ])->render();
    }

    public function verificar(Request $request, int $id)
    {
        $rules = [
            'estado' => 'required|in:aprobado,aprobado_obs,rechazado',
            'observaciones_contable' => 'nullable|string|max:1000',
            'monto_aprobado' => 'nullable|numeric|min:0',
        ];

        $messages = [
            'estado.required' => 'Debe seleccionar un resultado.',
            'monto_aprobado.numeric' => 'El monto aprobado debe ser un valor numérico.',
        ];

        if ($request->expectsJson() || $request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();
        } else {
            $data = $request->validate($rules, $messages);
        }

        $comp = ComprobantePublicidad::findOrFail($id);
        $comp->estado = $data['estado'];
        $comp->observaciones_contable = $data['observaciones_contable'] ?? null;
        $comp->verificado_por = optional($request->user())->id;
        $comp->verificado_at = now();

        if ($data['estado'] === 'aprobado_obs') {
            $comp->monto_aprobado = $data['monto_aprobado'] ?? $comp->monto;
        } else {
            $comp->monto_aprobado = $data['monto_aprobado'] ?? null;
        }

        $comp->save();

        $etiqueta = match ($data['estado']) {
            'aprobado' => 'aprobado',
            'aprobado_obs' => 'aprobado con observaciones',
            'rechazado' => 'rechazado',
            default => 'actualizado',
        };

        if ($request->expectsJson() || $request->ajax()) {
            $comp->load('asesor');
            $presupuesto = $comp->presupuesto;
            $mismoTitular = $presupuesto && $presupuesto->asesor_id && (int)$comp->asesor_id === (int)$presupuesto->asesor_id;
            return response()->json([
                'success' => true,
                'message' => 'Comprobante ' . $etiqueta . ' correctamente.',
                'html' => $this->renderComprobanteRow($comp, $presupuesto),
                'comprobante_id' => $comp->id,
                'estado' => $comp->estado,
            ]);
        }

        return back()->with('success', 'Comprobante ' . $etiqueta . ' correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $comp = ComprobantePublicidad::findOrFail($id);

        if ($comp->estado === 'aprobado') {
            return response()->json(['success' => false, 'message' => 'No se puede editar un comprobante ya aprobado.'], 400);
        }

        $presupuesto = $comp->presupuesto;
        if ($presupuesto->periodo_cerrado) {
            return response()->json(['success' => false, 'message' => 'El periodo está cerrado. No se puede editar.'], 400);
        }

        $rules = [
            'asesor_id' => 'required|integer|exists:asesores,id',
            'programa_id' => 'nullable|integer|exists:programas,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:500',
            'plataforma' => 'nullable|string|max:60',
            'campana_nombre' => 'nullable|string|max:200',
            'enlace_campana' => 'nullable|url|max:500',
            'pagina_facebook' => 'nullable|string|max:255',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'archivo_debito' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ];

        $messages = [
            'asesor_id.required' => 'Debe seleccionar el asesor que realizó el gasto.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'archivo.mimes' => 'El comprobante debe ser PDF o imagen (JPG, PNG, WEBP).',
            'archivo.max' => 'El comprobante no debe superar 5MB.',
            'archivo_debito.mimes' => 'El comprobante de débito debe ser PDF o imagen (JPG, PNG, WEBP).',
            'archivo_debito.max' => 'El comprobante de débito no debe superar 5MB.',
        ];

        if ($request->expectsJson() || $request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();
        } else {
            $data = $request->validate($rules, $messages);
        }

        $updateData = [
            'asesor_id' => $data['asesor_id'],
            'programa_id' => $data['programa_id'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'monto' => $data['monto'],
            'descripcion' => $data['descripcion'],
            'plataforma' => $data['plataforma'] ?? 'Facebook',
            'campana_nombre' => $data['campana_nombre'] ?? null,
            'enlace_campana' => $data['enlace_campana'] ?? null,
            'pagina_facebook' => $data['pagina_facebook'] ?? null,
        ];

        if ($request->hasFile('archivo')) {
            if ($comp->archivo_path && Storage::disk('public')->exists($comp->archivo_path)) {
                Storage::disk('public')->delete($comp->archivo_path);
            }
            $file = $request->file('archivo');
            $ext = strtolower($file->getClientOriginalExtension());
            $nombreArchivo = 'pub_' . $data['asesor_id'] . '_' . time() . '_' . uniqid() . '.' . $ext;
            $path = $file->storeAs(
                'publicidad/' . $presupuesto->gestion . '/' . str_pad($presupuesto->mes, 2, '0', STR_PAD_LEFT),
                $nombreArchivo,
                'public'
            );
            $updateData['archivo_path'] = $path;
            $updateData['archivo_original'] = $file->getClientOriginalName();
            $updateData['archivo_mime'] = $file->getMimeType();
        }

        if ($request->hasFile('archivo_debito')) {
            if ($comp->archivo_debito_path && Storage::disk('public')->exists($comp->archivo_debito_path)) {
                Storage::disk('public')->delete($comp->archivo_debito_path);
            }
            $debFile = $request->file('archivo_debito');
            $debExt = strtolower($debFile->getClientOriginalExtension());
            $debNombre = 'deb_' . $data['asesor_id'] . '_' . time() . '_' . uniqid() . '.' . $debExt;
            $debitoPath = $debFile->storeAs(
                'publicidad/' . $presupuesto->gestion . '/' . str_pad($presupuesto->mes, 2, '0', STR_PAD_LEFT),
                $debNombre,
                'public'
            );
            $updateData['archivo_debito_path'] = $debitoPath;
            $updateData['archivo_debito_original'] = $debFile->getClientOriginalName();
            $updateData['archivo_debito_mime'] = $debFile->getMimeType();
        } elseif ($request->has('eliminar_archivo_debito') && $request->boolean('eliminar_archivo_debito')) {
            if ($comp->archivo_debito_path && Storage::disk('public')->exists($comp->archivo_debito_path)) {
                Storage::disk('public')->delete($comp->archivo_debito_path);
            }
            $updateData['archivo_debito_path'] = null;
            $updateData['archivo_debito_original'] = null;
            $updateData['archivo_debito_mime'] = null;
        }

        $comp->update($updateData);

        if ($request->expectsJson() || $request->ajax()) {
            $comp->load('asesor');
            return response()->json([
                'success' => true,
                'message' => 'Comprobante actualizado correctamente.',
                'comprobante' => $comp,
                'html' => $this->renderComprobanteRow($comp, $presupuesto),
            ]);
        }

        return redirect()->route('admin.marketing.publicidad.index', [
            'gestion' => $presupuesto->gestion, 'mes' => $presupuesto->mes, 'quincena' => $presupuesto->quincena,
        ])->with('success', 'Comprobante actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $comp = ComprobantePublicidad::findOrFail($id);
        if ($comp->estado === 'aprobado') {
            return back()->with('error', 'No se puede eliminar un comprobante ya aprobado.');
        }
        if ($comp->archivo_path && Storage::disk('public')->exists($comp->archivo_path)) {
            Storage::disk('public')->delete($comp->archivo_path);
        }
        $comp->delete();
        return back()->with('success', 'Comprobante eliminado.');
    }

    public function storePresupuesto(Request $request)
    {
        $data = $request->validate([
            'supervisor_id' => 'nullable|integer|exists:asesores,id',
            'asesor_id' => 'nullable|integer|exists:asesores,id',
            'presupuesto_origen_id' => 'nullable|integer|exists:marketing_presupuestos_publicidad,id',
            'gestion' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'quincena' => 'required|integer|in:1,2',
            'monto_asignado' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
            'archivo_derivacion' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        // Si es una derivación, verificar que el monto no exceda el disponible
        if (!empty($data['presupuesto_origen_id'])) {
            $origen = PresupuestoPublicidad::findOrFail($data['presupuesto_origen_id']);
            if ($origen->periodo_cerrado) {
                $msg = 'El periodo del presupuesto origen está cerrado. No se pueden realizar derivaciones.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }
            if ($data['monto_asignado'] > $origen->monto_asignado) {
                $msg = 'El monto a derivar (Bs ' . number_format($data['monto_asignado'], 2) . ') supera el saldo disponible del presupuesto origen (Bs ' . number_format($origen->monto_asignado, 2) . ').';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }
        }

        $crearData = [
            'supervisor_id' => $data['supervisor_id'] ?? null,
            'asesor_id' => $data['asesor_id'] ?? null,
            'presupuesto_origen_id' => $data['presupuesto_origen_id'] ?? null,
            'gestion' => $data['gestion'],
            'mes' => $data['mes'],
            'quincena' => $data['quincena'],
            'monto_asignado' => $data['monto_asignado'] ?? 0,
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => 'activo',
            'created_by' => optional($request->user())->id,
        ];

        if ($request->hasFile('archivo_derivacion')) {
            $file = $request->file('archivo_derivacion');
            $ext = strtolower($file->getClientOriginalExtension());
            $nombreArchivo = 'deriv_' . ($data['asesor_id'] ?? '0') . '_' . time() . '_' . uniqid() . '.' . $ext;
            $path = $file->storeAs(
                'publicidad/' . $data['gestion'] . '/' . str_pad($data['mes'], 2, '0', STR_PAD_LEFT),
                $nombreArchivo,
                'public'
            );
            $crearData['archivo_derivacion_path'] = $path;
            $crearData['archivo_derivacion_original'] = $file->getClientOriginalName();
            $crearData['archivo_derivacion_mime'] = $file->getMimeType();
        }

        $presupuesto = PresupuestoPublicidad::create($crearData);

        // Decrease the original budget by the derived amount
        if (!empty($data['presupuesto_origen_id'])) {
            DB::table('marketing_presupuestos_publicidad')
                ->where('id', $data['presupuesto_origen_id'])
                ->decrement('monto_asignado', $data['monto_asignado']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Presupuesto asignado correctamente.', 'id' => $presupuesto->id]);
        }

        return back()->with('success', 'Presupuesto asignado correctamente.');
    }

    public function updatePresupuesto(Request $request, int $id)
    {
        $presupuesto = PresupuestoPublicidad::findOrFail($id);

        $data = $request->validate([
            'monto_asignado' => 'required|numeric|min:0',
            'archivo_derivacion' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $updateData = [
            'monto_asignado' => $data['monto_asignado'],
        ];

        if ($request->hasFile('archivo_derivacion')) {
            $file = $request->file('archivo_derivacion');
            $ext = strtolower($file->getClientOriginalExtension());
            $nombreArchivo = 'deriv_' . ($presupuesto->asesor_id ?? '0') . '_' . time() . '_' . uniqid() . '.' . $ext;
            $path = $file->storeAs(
                'publicidad/' . $presupuesto->gestion . '/' . str_pad($presupuesto->mes, 2, '0', STR_PAD_LEFT),
                $nombreArchivo,
                'public'
            );
            $updateData['archivo_derivacion_path'] = $path;
            $updateData['archivo_derivacion_original'] = $file->getClientOriginalName();
            $updateData['archivo_derivacion_mime'] = $file->getMimeType();
        }

        $presupuesto->update($updateData);

        return back()->with('success', 'Presupuesto derivado actualizado correctamente.');
    }

    /**
     * Elimina un presupuesto asignado que no registró ningún gasto.
     * Solo se permite si no tiene comprobantes, ni derivaciones, y el periodo está abierto.
     * Si el presupuesto era una derivación, el monto se devuelve al presupuesto origen.
     */
    public function destroyPresupuesto(Request $request, int $id)
    {
        $presupuesto = PresupuestoPublicidad::findOrFail($id);

        if (ComprobantePublicidad::where('presupuesto_id', $presupuesto->id)->exists()) {
            return back()->with('error', 'No se puede eliminar: el presupuesto ya tiene comprobantes registrados.');
        }

        if (PresupuestoPublicidad::where('presupuesto_origen_id', $presupuesto->id)->exists()) {
            return back()->with('error', 'No se puede eliminar: el presupuesto tiene derivaciones. Elimina primero las derivaciones.');
        }

        if ($presupuesto->periodo_cerrado) {
            return back()->with('error', 'No se puede eliminar: el periodo está cerrado.');
        }

        $monto = (float) $presupuesto->monto_asignado;
        $origenId = $presupuesto->presupuesto_origen_id;
        $archivo = $presupuesto->archivo_derivacion_path;

        DB::transaction(function () use ($presupuesto, $monto, $origenId) {
            if ($origenId) {
                DB::table('marketing_presupuestos_publicidad')
                    ->where('id', $origenId)
                    ->increment('monto_asignado', $monto);
            }

            $presupuesto->delete();
        });

        if ($archivo) {
            Storage::disk('public')->delete($archivo);
        }

        $msg = $origenId
            ? 'Presupuesto sin gastos eliminado. Se devolvieron Bs ' . number_format($monto, 2) . ' al presupuesto origen.'
            : 'Presupuesto sin gastos eliminado correctamente.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function reembolsar(Request $request, int $id)
    {
        $data = $request->validate([
            'monto_reembolsado' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $p = PresupuestoPublicidad::findOrFail($id);
        if ($p->periodo_cerrado) {
            return back()->with('error', 'El periodo está cerrado. No se pueden registrar reembolsos.');
        }
        $p->monto_reembolsado = $data['monto_reembolsado'];
        if (!empty($data['observaciones'])) {
            $p->observaciones = trim(($p->observaciones ? $p->observaciones . "\n" : '') . '[' . now()->format('d/m/Y H:i') . '] ' . $data['observaciones']);
        }
        $p->estado = 'cerrado';
        $p->save();

        return back()->with('success', 'Reembolso registrado.');
    }

    private function generarMiniatura(string $archivoPath, string $archivoOriginal): ?string
    {
        $fullPath = public_path('storage/' . $archivoPath);
        if (!file_exists($fullPath)) {
            return null;
        }

        $ext = strtolower(pathinfo($archivoPath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $mime = mime_content_type($fullPath) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        }

        if ($ext === 'pdf') {
            return $this->generarPlaceholderPdf($archivoOriginal);
        }

        return null;
    }

    private function generarPlaceholderPdf(string $archivoOriginal): string
    {
        $width = 100;
        $height = 70;
        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, 248, 250, 252);
        imagefill($img, 0, 0, $bg);

        $borderColor = imagecolorallocate($img, 200, 210, 220);
        imagerectangle($img, 0, 0, $width - 1, $height - 1, $borderColor);

        $docBg = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 6, 4, $width - 6, $height - 14, $docBg);

        $redBar = imagecolorallocate($img, 192, 57, 43);
        imagefilledrectangle($img, 6, 4, $width - 6, 14, $redBar);

        $white = imagecolorallocate($img, 255, 255, 255);
        $label = 'PDF';
        $fs = 2;
        $tw = strlen($label) * imagefontwidth($fs);
        imagestring($img, $fs, (int)(($width - $tw) / 2), 5, $label, $white);

        $lineColor = imagecolorallocate($img, 220, 225, 232);
        for ($i = 0; $i < 3; $i++) {
            imageline($img, 14, 22 + $i * 10, $width - 14, 22 + $i * 10, $lineColor);
        }

        $textColor = imagecolorallocate($img, 80, 80, 85);
        $displayName = strlen($archivoOriginal) > 14 ? substr($archivoOriginal, 0, 11) . '...' : $archivoOriginal;
        $fs2 = 1;
        $tw2 = strlen($displayName) * imagefontwidth($fs2);
        imagestring($img, $fs2, (int)(($width - $tw2) / 2), $height - 12, $displayName, $textColor);

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    public function comprobantesAsesor($asesorId)
    {
        $perPage = 10;
        $page = (int) request('page', 1);

        $comprobantes = DB::table('marketing_comprobantes_publicidad as c')
            ->join('asesores as a', 'a.id', '=', 'c.asesor_id')
            ->leftJoin('users as v', 'v.id', '=', 'c.verificado_por')
            ->leftJoin('marketing_presupuestos_publicidad as p', 'p.id', '=', 'c.presupuesto_id')
            ->leftJoin('programas as prg', 'prg.id', '=', 'c.programa_id')
            ->where('c.asesor_id', $asesorId)
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
            ->paginate($perPage, ['*'], 'page', $page);

        $rowsHtml = view('admin.marketing.asesores._comprobantes_table', [
            'comprobantes' => $comprobantes,
            'mesesPub' => $this->meses,
        ])->render();

        $paginationHtml = $comprobantes->hasPages()
            ? '<div class="comprobantes-pagination-wrap p-2 border-top">' . $comprobantes->links('pagination::bootstrap-5') . '</div>'
            : '';

        return response()->json([
            'success' => true,
            'rows_html' => $rowsHtml,
            'pagination_html' => $paginationHtml,
            'total' => $comprobantes->total(),
            'current_page' => $comprobantes->currentPage(),
            'last_page' => $comprobantes->lastPage(),
        ]);
    }
}
