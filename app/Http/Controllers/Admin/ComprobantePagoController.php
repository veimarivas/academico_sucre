<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComprobantePagoController extends Controller
{
    public function index()
    {
        $comprobantes = DB::table('comprobantes_pago')
            ->leftJoin('participantes', 'comprobantes_pago.participante_id', '=', 'participantes.id')
            ->leftJoin('programas', 'comprobantes_pago.programa_id', '=', 'programas.id')
            ->leftJoin('asesores', 'comprobantes_pago.asesor_id', '=', 'asesores.id')
            ->select(
                'comprobantes_pago.*',
                'participantes.nombres as p_nombres',
                'participantes.apellidos as p_apellidos',
                'participantes.ci as p_ci',
                'programas.nombre as prog_nombre',
                'programas.codigo as prog_codigo',
                'programas.gestion as prog_gestion',
                DB::raw("CONCAT(COALESCE(asesores.nombres, ''), ' ', COALESCE(asesores.apellidos, '')) as asesor_nombre")
            )
            ->orderByRaw("FIELD(comprobantes_pago.estado, 'pendiente', 'verificado', 'rechazado')")
            ->orderBy('comprobantes_pago.created_at', 'desc')
            ->paginate(25);

        foreach ($comprobantes as $c) {
            if ($c->estado === 'verificado' && $c->montos_verificados) {
                $montos = json_decode($c->montos_verificados, true);
                if (is_array($montos) && !empty($montos)) {
                    $c->monto = array_sum($montos);
                }
            }
        }

        return view('admin.contable.comprobantes', compact('comprobantes'));
    }

    public function show($id)
    {
        $c = DB::table('comprobantes_pago')
            ->leftJoin('participantes', 'comprobantes_pago.participante_id', '=', 'participantes.id')
            ->leftJoin('programas', 'comprobantes_pago.programa_id', '=', 'programas.id')
            ->leftJoin('inscripciones', 'comprobantes_pago.inscripcion_id', '=', 'inscripciones.id')
            ->leftJoin('asesores', 'comprobantes_pago.asesor_id', '=', 'asesores.id')
            ->select(
                'comprobantes_pago.*',
                'participantes.nombres as p_nombres',
                'participantes.apellidos as p_apellidos',
                'participantes.ci as p_ci',
                'participantes.celular as p_celular',
                'participantes.correo as p_correo',
                'programas.nombre as prog_nombre',
                'programas.codigo as prog_codigo',
                'programas.gestion as prog_gestion',
                'inscripciones.matricula as ins_matricula',
                'inscripciones.deuda_matricula',
                'inscripciones.monto_programa',
                'inscripciones.deuda',
                'inscripciones.descuento_matricula',
                'inscripciones.descuento_colegiatura',
                'inscripciones.contado',
                DB::raw("CONCAT(COALESCE(asesores.nombres, ''), ' ', COALESCE(asesores.apellidos, '')) as asesor_nombre")
            )
            ->where('comprobantes_pago.id', $id)
            ->first();

        if (!$c) {
            return response()->json(['success' => false, 'message' => 'Comprobante no encontrado'], 404);
        }

        $deudasIdsSeleccionadas = $c->deudas_seleccionadas
            ? json_decode($c->deudas_seleccionadas, true)
            : [];

        $deudasDetalle = [];

        if ($c->estado === 'verificado') {
            $montosVerificados = $c->montos_verificados
                ? json_decode($c->montos_verificados, true)
                : [];

            if (!empty($montosVerificados)) {
                $mpIds = array_keys($montosVerificados);
                $modulos = DB::table('modulos_participantes')
                    ->leftJoin('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                    ->whereIn('modulos_participantes.id', $mpIds)
                    ->select(
                        'modulos_participantes.*',
                        'modulos.nombre as modulo_nombre'
                    )
                    ->get();
                foreach ($modulos as $m) {
                    $montoPagado = (float) ($montosVerificados[$m->id] ?? 0);
                    $deudasDetalle[] = [
                        'id' => $m->id,
                        'modulo_nombre' => $m->modulo_nombre ?? '—',
                        'tipo' => $m->tipo,
                        'deuda' => (float) $m->deuda,
                        'total_pagado' => (float) $m->total_pagado,
                        'saldo' => max(0, (float) $m->deuda - (float) $m->total_pagado),
                        'monto_pagado' => $montoPagado,
                        'seleccionado' => true,
                    ];
                }
            }
        } elseif ($c->estado === 'pendiente') {
            $selectedSet = array_flip($deudasIdsSeleccionadas);
            $allModulos = DB::table('modulos_participantes')
                ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                ->where('modulos_participantes.participante_id', $c->participante_id)
                ->where('modulos.programa_id', $c->programa_id)
                ->select(
                    'modulos_participantes.*',
                    'modulos.nombre as modulo_nombre'
                )
                ->orderBy('modulos_participantes.id')
                ->get();

            foreach ($allModulos as $m) {
                $deudasDetalle[] = [
                    'id' => $m->id,
                    'modulo_nombre' => $m->modulo_nombre ?? '—',
                    'tipo' => $m->tipo,
                    'deuda' => (float) $m->deuda,
                    'total_pagado' => (float) $m->total_pagado,
                    'saldo' => max(0, (float) $m->deuda - (float) $m->total_pagado),
                    'seleccionado' => isset($selectedSet[$m->id]),
                ];
            }
        }
        // rechazado: deudasDetalle stays empty

        return response()->json([
            'success' => true,
            'comprobante' => $c,
            'deudas_detalle' => $deudasDetalle,
        ]);
    }

    public function verificar(Request $request, $id)
    {
        $validated = $request->validate([
            'accion' => 'required|in:verificar,rechazar',
            'observaciones' => 'nullable|string|max:500',
            'montos' => 'nullable|array',
            'montos.*' => 'nullable|numeric|min:0',
        ]);

        $comprobante = DB::table('comprobantes_pago')->where('id', $id)->first();
        if (!$comprobante) {
            return response()->json(['success' => false, 'message' => 'Comprobante no encontrado'], 404);
        }
        if ($comprobante->estado !== 'pendiente') {
            return response()->json(['success' => false, 'message' => 'Este comprobante ya fue procesado (estado: ' . $comprobante->estado . ')'], 422);
        }

        $nuevoEstado = $validated['accion'] === 'verificar' ? 'verificado' : 'rechazado';

        DB::beginTransaction();
        try {
            DB::table('comprobantes_pago')
                ->where('id', $id)
                ->update([
                    'estado' => $nuevoEstado,
                    'verificado_por' => Auth::id(),
                    'verificado_at' => now(),
                    'observaciones' => $validated['observaciones'] ?? null,
                    'updated_at' => now(),
                ]);

            if ($nuevoEstado === 'verificado') {
                $montos = $validated['montos'] ?? [];

                $cuotas = DB::table('modulos_participantes')
                    ->join('modulos', 'modulos_participantes.modulo_id', '=', 'modulos.id')
                    ->where('modulos_participantes.participante_id', $comprobante->participante_id)
                    ->where('modulos.programa_id', $comprobante->programa_id)
                    ->select('modulos_participantes.*')
                    ->get()
                    ->keyBy('id');

                $selectedMpIds = [];
                $montosVerificados = [];
                $totalVerificado = 0;

                foreach ($cuotas as $mpId => $cuota) {
                    $saldo = max(0, (float) $cuota->deuda - (float) $cuota->total_pagado);
                    if ($saldo <= 0) continue;

                    $montoAplicar = isset($montos[$mpId]) ? (float) $montos[$mpId] : 0;
                    $montoAplicar = max(0, min($montoAplicar, $saldo));
                    if ($montoAplicar <= 0) continue;

                    $selectedMpIds[] = $mpId;
                    $montosVerificados[$mpId] = $montoAplicar;
                    $totalVerificado += $montoAplicar;

                    $pagoId = DB::table('pagos')->insertGetId([
                        'pago_bs' => $montoAplicar,
                        'created' => $comprobante->created_at ? date('Y-m-d', strtotime($comprobante->created_at)) : now()->toDateString(),
                        'modified' => now()->toDateString(),
                    ]);

                    $nuevoPagado = (float) $cuota->total_pagado + $montoAplicar;
                    $terminado = $nuevoPagado >= (float) $cuota->deuda ? 1 : 0;

                    DB::table('deudas')->insert([
                        'modulo_participante_id' => $cuota->id,
                        'pago_id' => $pagoId,
                        'monto_bs' => $montoAplicar,
                        'terminado' => $terminado,
                    ]);

                    DB::table('modulos_participantes')
                        ->where('id', $cuota->id)
                        ->update(['total_pagado' => $nuevoPagado]);
                }

                // Update the comprobante with verified data
                DB::table('comprobantes_pago')
                    ->where('id', $id)
                    ->update([
                        'monto' => $totalVerificado,
                        'deudas_seleccionadas' => json_encode($selectedMpIds),
                        'montos_verificados' => json_encode($montosVerificados),
                    ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comprobante ' . ($nuevoEstado === 'verificado' ? 'verificado y pagos registrados' : 'rechazado') . ' correctamente.',
        ]);
    }
}
