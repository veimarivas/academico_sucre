<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Cobranza y retención: mora por programa, efectividad de avisos y bajas.
 */
class RetencionController extends Controller
{
    public function index(Request $request): View
    {
        $hoy = now();
        $gestion = (int) $request->get('gestion', $hoy->year);

        $gestiones = DB::table('programas')->select('gestion')->distinct()
            ->orderByDesc('gestion')->pluck('gestion');
        if ($gestiones->isEmpty()) {
            $gestiones = collect([$hoy->year]);
        }

        $programas = DB::table('programas')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('programas.gestion', $gestion)
            ->where('programas.estado_id', '!=', 3)
            ->select('programas.id', 'programas.nombre', 'programas.codigo', 'tipos.nombre as tipo')
            ->orderBy('programas.nombre')
            ->get();

        $ids = $programas->pluck('id');

        // Inscritos, bajas y deuda neta por programa.
        $insc = collect();
        if ($ids->isNotEmpty()) {
            $insc = DB::table('inscripciones')
                ->whereIn('programa_id', $ids)
                ->selectRaw("
                    programa_id,
                    COUNT(*) as inscritos,
                    SUM(CASE WHEN baja_operativa=1 OR baja_academica=1 OR baja_contable=1 THEN 1 ELSE 0 END) as bajas,
                    SUM(GREATEST(0, matricula - descuento_matricula) + GREATEST(0, monto_programa - descuento_colegiatura)) as deuda
                ")
                ->groupBy('programa_id')
                ->get()
                ->keyBy('programa_id');
        }

        // Pagos y cuotas en mora por programa.
        $pagos = collect();
        if ($ids->isNotEmpty()) {
            $pagos = DB::table('modulos_participantes as mp')
                ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
                ->whereIn('m.programa_id', $ids)
                ->where(function ($w) {
                    $w->where('mp.retirado', 0)->orWhereNull('mp.retirado');
                })
                ->selectRaw('m.programa_id, SUM(mp.total_pagado) as pagado,
                    SUM(CASE WHEN mp.deuda - mp.total_pagado > 0 THEN 1 ELSE 0 END) as cuotas_mora,
                    COUNT(DISTINCT CASE WHEN mp.deuda - mp.total_pagado > 0 THEN mp.participante_id END) as con_mora')
                ->groupBy('m.programa_id')
                ->get()
                ->keyBy('programa_id');
        }

        // Recordatorios/avisos WhatsApp enviados por programa (tabla mensajes).
        $mensajes = collect();
        if ($ids->isNotEmpty()) {
            $mensajes = DB::table('mensajes as me')
                ->join('inscripciones as i', 'me.inscripcione_id', '=', 'i.id')
                ->whereIn('i.programa_id', $ids)
                ->where('me.tipo', 'like', 'whatsapp%')
                ->selectRaw('i.programa_id, COUNT(*) as total')
                ->groupBy('i.programa_id')
                ->pluck('total', 'i.programa_id');
        }

        $filas = $programas->map(function ($p) use ($insc, $pagos, $mensajes) {
            $i = $insc->get($p->id);
            $pa = $pagos->get($p->id);

            $deuda = (float) ($i->deuda ?? 0);
            $pagado = (float) ($pa->pagado ?? 0);
            $saldo = max(0, $deuda - $pagado);

            return (object) [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo,
                'tipo' => $p->tipo ?? 'Sin tipo',
                'inscritos' => (int) ($i->inscritos ?? 0),
                'bajas' => (int) ($i->bajas ?? 0),
                'con_mora' => (int) ($pa->con_mora ?? 0),
                'cuotas_mora' => (int) ($pa->cuotas_mora ?? 0),
                'deuda' => $deuda,
                'pagado' => $pagado,
                'saldo' => $saldo,
                'pct' => $deuda > 0 ? (int) round(($pagado / $deuda) * 100) : 0,
                'mensajes' => (int) ($mensajes[$p->id] ?? 0),
            ];
        });

        $totales = [
            'programas' => $filas->count(),
            'inscritos' => $filas->sum('inscritos'),
            'bajas' => $filas->sum('bajas'),
            'con_mora' => $filas->sum('con_mora'),
            'deuda' => $filas->sum('deuda'),
            'pagado' => $filas->sum('pagado'),
            'saldo' => $filas->sum('saldo'),
            'mensajes' => $filas->sum('mensajes'),
        ];
        $totales['pct'] = $totales['deuda'] > 0 ? (int) round(($totales['pagado'] / $totales['deuda']) * 100) : 0;

        // Bajas recientes de la gestión.
        $bajas = collect();
        if ($ids->isNotEmpty()) {
            $bajas = DB::table('inscripciones as i')
                ->join('programas as pr', 'i.programa_id', '=', 'pr.id')
                ->join('participantes as pa', 'i.participante_id', '=', 'pa.id')
                ->leftJoin('baja_historial as bh', function ($j) {
                    $j->on('bh.inscripcion_id', '=', 'i.id')->where('bh.accion', '=', 'baja');
                })
                ->whereIn('i.programa_id', $ids)
                ->where(function ($q) {
                    $q->where('i.baja_operativa', 1)->orWhere('i.baja_academica', 1)->orWhere('i.baja_contable', 1);
                })
                ->select(
                    'pa.nombres', 'pa.apellidos', 'pr.nombre as programa', 'bh.motivo', 'bh.tipo as tipo_baja',
                    'bh.created_at as fecha_baja', 'i.baja_operativa', 'i.baja_academica', 'i.baja_contable'
                )
                ->orderByDesc('bh.created_at')
                ->orderByDesc('i.id')
                ->limit(40)
                ->get();
        }

        // Mensajes WhatsApp por tipo en la gestión.
        $mensajesTipo = DB::table('mensajes as me')
            ->join('inscripciones as i', 'me.inscripcione_id', '=', 'i.id')
            ->join('programas as pr', 'i.programa_id', '=', 'pr.id')
            ->where('pr.gestion', $gestion)
            ->where('me.tipo', 'like', 'whatsapp%')
            ->selectRaw('me.tipo, COUNT(*) as total')
            ->groupBy('me.tipo')
            ->pluck('total', 'me.tipo');

        return view('admin.contable.retencion', compact(
            'gestiones', 'gestion', 'filas', 'totales', 'bajas', 'mensajesTipo'
        ));
    }
}
