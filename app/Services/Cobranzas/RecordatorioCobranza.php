<?php

namespace App\Services\Cobranzas;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Construye los mensajes de recordatorio de cuotas (vencidas / próximas) de un
 * programa, con el número real de cuota por tipo.
 */
class RecordatorioCobranza
{
    /**
     * @return array{programa: object, items: array, logRows: array, sin_telefono: int}
     */
    public function construir(int $programaId, string $modo, ?int $participanteId = null): array
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();

        if (! $programa) {
            return ['programa' => null, 'items' => [], 'logRows' => [], 'sin_telefono' => 0];
        }

        $hoy = now()->startOfDay();

        $query = DB::table('modulos_participantes as mp')
            ->join('participantes as pa', 'mp.participante_id', '=', 'pa.id')
            ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
            ->where('m.programa_id', $programa->id)
            ->whereNotNull('mp.fecha_pago')
            ->where('mp.deuda', '>', 0)
            ->whereRaw('mp.total_pagado < mp.deuda')
            ->where(function ($w) {
                $w->where('mp.retirado', 0)->orWhereNull('mp.retirado');
            })
            ->select(
                'mp.id as cuota_id', 'mp.tipo', 'mp.fecha_pago', 'mp.deuda', 'mp.total_pagado',
                'pa.id as participante_id', 'pa.nombres', 'pa.apellidos', 'pa.celular'
            );

        if ($modo === 'vencidas') {
            // Todas las cuotas impagas con vencimiento anterior a hoy.
            $query->where('mp.fecha_pago', '<', $hoy->toDateString());
        } else {
            // Solo la cuota del mes en curso que aún no venció.
            $query->whereYear('mp.fecha_pago', $hoy->year)
                ->whereMonth('mp.fecha_pago', $hoy->month)
                ->where('mp.fecha_pago', '>=', $hoy->toDateString());
        }

        if ($participanteId) {
            $query->where('pa.id', $participanteId);
        }

        $cuotas = $query->orderBy('pa.apellidos')->orderBy('pa.nombres')->orderBy('mp.fecha_pago')->get();

        if ($cuotas->isEmpty()) {
            return ['programa' => $programa, 'items' => [], 'logRows' => [], 'sin_telefono' => 0];
        }

        $inscMap = DB::table('inscripciones')->where('programa_id', $programa->id)->pluck('id', 'participante_id');
        $cierre = $modo === 'vencidas'
            ? 'Por favor regularice su situación a la brevedad. Si ya realizó el pago, ignore este mensaje. Gracias.'
            : 'Le agradecemos su pago oportuno. Saludos cordiales.';

        $items = [];
        $logRows = [];
        $sinTelefono = 0;

        foreach ($cuotas->groupBy('participante_id') as $pid => $cs) {
            $p = $cs->first();
            $chatId = $this->chatId($p->celular);

            if (! $chatId) {
                $sinTelefono++;

                continue;
            }

            $numeros = $this->numerosPorTipo(
                DB::table('modulos_participantes as mp')
                    ->join('modulos as m', 'mp.modulo_id', '=', 'm.id')
                    ->where('m.programa_id', $programa->id)
                    ->where('mp.participante_id', $pid)
                    ->get(['mp.id', 'mp.tipo', 'mp.fecha_pago'])
            );

            $detalle = '';
            $total = 0.0;
            $ordenadas = $cs->sortBy(fn ($c) => ($c->tipo === 'Matricula' ? '0' : '1').'-'.$c->fecha_pago);

            foreach ($ordenadas as $c) {
                $saldo = max(0, (float) $c->deuda - (float) $c->total_pagado);

                if ($saldo <= 0) {
                    continue;
                }

                $numero = $numeros[$c->cuota_id] ?? '?';
                $tipoLabel = $c->tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad';
                $fecha = Carbon::parse($c->fecha_pago);
                $dias = $hoy->diffInDays($fecha->copy()->startOfDay(), false);
                $estadoTxt = $modo === 'vencidas'
                    ? '⚠️ Vencida hace '.abs($dias).' día(s)'
                    : ($dias === 0 ? '⏰ Vence HOY' : '⏳ En '.$dias.' día(s)');

                $detalle .= '• Cuota '.$numero.' — '.$tipoLabel.' — '.$fecha->format('d/m/Y').' — Bs '.number_format($saldo, 2).' — '.$estadoTxt."\n";
                $total += $saldo;
            }

            if ($detalle === '') {
                continue;
            }

            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $p->apellidos.', '.$p->nombres,
                'external_ref' => 'cobranza:'.$pid.':'.$modo.':'.$hoy->toDateString(),
                'variables' => [
                    'nombre_completo' => $p->apellidos.', '.$p->nombres,
                    'programa' => $programa->nombre,
                    'programa_codigo' => $programa->codigo,
                    'situacion' => $modo === 'vencidas' ? 'vencidas' : 'próximas a vencer',
                    'detalle_cuotas' => rtrim($detalle),
                    'total_adeudado' => number_format($total, 2),
                    'cierre' => $cierre,
                ],
            ];

            $logRows[] = [
                'inscripcione_id' => (int) $inscMap->get($pid, 0),
                'persona' => $p->apellidos.', '.$p->nombres,
            ];
        }

        return ['programa' => $programa, 'items' => $items, 'logRows' => $logRows, 'sin_telefono' => $sinTelefono];
    }

    public function nombre(?\stdClass $programa, string $modo): string
    {
        return 'Cobranzas '.($modo === 'vencidas' ? 'vencidas' : 'próximas').' — '.($programa->nombre ?? '');
    }

    private function numerosPorTipo($cuotas): array
    {
        $numeros = [];
        $contadores = ['Matricula' => 0, 'Colegiatura' => 0];

        $ordenadas = $cuotas->sortBy(fn ($c) => ($c->tipo === 'Matricula' ? '0' : '1').'-'.$c->fecha_pago);

        foreach ($ordenadas as $c) {
            $key = $c->tipo === 'Matricula' ? 'Matricula' : 'Colegiatura';
            $contadores[$key]++;
            $numeros[$c->id] = $contadores[$key];
        }

        return $numeros;
    }

    private function chatId(?string $celular): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $celular);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 8) {
            $digits = '591'.$digits;
        }

        if (strlen($digits) < 8) {
            return null;
        }

        return $digits.'@c.us';
    }
}
