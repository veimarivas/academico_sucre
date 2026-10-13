<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguimientoDiarioController extends Controller
{
    public function index(Request $request)
    {
        $hoy = date('Y-m-d');

        // Lunes de la semana objetivo (param ?inicio=YYYY-MM-DD opcional)
        $inicioParam = $request->get('inicio');
        if ($inicioParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicioParam)) {
            $base = strtotime($inicioParam);
        } else {
            $base = strtotime($hoy);
        }
        // PHP: 1=lunes ... 7=domingo
        $dow = (int) date('N', $base);
        $inicio = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days', $base));
        $fin    = date('Y-m-d', strtotime('+6 days', strtotime($inicio)));

        $semanaAnterior  = date('Y-m-d', strtotime('-7 days', strtotime($inicio)));
        $semanaSiguiente = date('Y-m-d', strtotime('+7 days', strtotime($inicio)));
        $semanaHoy       = date('Y-m-d', strtotime('-' . ((int) date('N', strtotime($hoy)) - 1) . ' days', strtotime($hoy)));

        $horarios = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereBetween('horarios.fecha_desarrollo', [$inicio, $fin])
            ->select(
                'horarios.id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado',
                'horarios.observaciones',
                'modulos.id as modulo_id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_desarrollo',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.id as docente_id',
                'docentes.ci as docente_ci',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'asignados.id as asignado_id',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        // Marcar atrasados (fecha < hoy y estado === 'No Inicio')
        $horarios = $horarios->map(function ($h) use ($hoy) {
            $h->atrasado = ($h->fecha_desarrollo < $hoy) && (trim($h->estado) === 'No Inicio');
            return $h;
        });

        // Cargar asistencias para horarios desarrollados
        $idsDesarrollados = $horarios
            ->filter(fn ($h) => trim($h->estado) === 'Desarrollado')
            ->pluck('id')
            ->all();

        $asistenciasPorHorario = collect();
        if (!empty($idsDesarrollados)) {
            $asistenciasPorHorario = DB::table('asistencias')
                ->join('participantes', 'asistencias.participante_id', '=', 'participantes.id')
                ->whereIn('asistencias.horario_id', $idsDesarrollados)
                ->select(
                    'asistencias.horario_id',
                    'asistencias.asistio',
                    'asistencias.observacion',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos'
                )
                ->orderBy('participantes.apellidos')
                ->orderBy('participantes.nombres')
                ->get()
                ->groupBy('horario_id');
        }

        // Agrupar por fecha (7 días, aún si vacíos)
        $dias = [];
        for ($i = 0; $i < 7; $i++) {
            $f = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($inicio)));
            $dias[$f] = collect();
        }
        foreach ($horarios as $h) {
            if (isset($dias[$h->fecha_desarrollo])) {
                $dias[$h->fecha_desarrollo]->push($h);
            }
        }

        // Resumen semanal
        $resumen = [
            'total'         => $horarios->count(),
            'desarrollados' => $horarios->filter(fn ($h) => trim($h->estado) === 'Desarrollado')->count(),
            'pendientes'    => $horarios->filter(fn ($h) => in_array(trim($h->estado), ['No Inicio', 'Pendiente', 'Programado', 'Confirmado']))->count(),
            'atrasados'     => $horarios->filter(fn ($h) => $h->atrasado)->count(),
        ];

        // ===== Desglose por responsable (asignado) =====
        // Plantilla de días vacíos para el mini-calendario por responsable
        $diasVacios = [];
        for ($i = 0; $i < 7; $i++) {
            $f = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($inicio)));
            $diasVacios[$f] = ['count' => 0, 'paralelo' => 0, 'horarios' => []];
        }

        $responsables = [];
        foreach ($horarios as $h) {
            $rid = $h->asignado_id ?: 0;
            if (!isset($responsables[$rid])) {
                $nombre = trim(($h->asignado_nombres ?? '') . ' ' . ($h->asignado_apellidos ?? ''));
                $responsables[$rid] = [
                    'id'                  => $rid,
                    'nombre'              => $nombre !== '' ? $nombre : 'Sin asignar',
                    'sin_asignar'         => $rid === 0,
                    'color'               => $h->asignado_hex ? ('#' . ltrim($h->asignado_hex, '#')) : '#6c757d',
                    'total'               => 0,
                    'pasados'             => 0,
                    'futuros'             => 0,
                    'pas_desarrollado'    => 0,
                    'pas_confirmado'      => 0,
                    'pas_no_inicio'       => 0,
                    'pas_postergado'      => 0,
                    'pas_otro'            => 0,
                    'des_con_asist'       => 0,
                    'des_sin_asist'       => 0,
                    'por_dia'             => $diasVacios,
                    'max_paralelo_semana' => 0,
                    'dias_con_paralelo'   => 0,
                ];
            }
            $r = &$responsables[$rid];
            $r['total']++;
            $esPasada = $h->fecha_desarrollo < $hoy;
            $est = trim((string) $h->estado);

            if ($esPasada) {
                $r['pasados']++;
                switch ($est) {
                    case 'Desarrollado':
                        $r['pas_desarrollado']++;
                        if (isset($asistenciasPorHorario[$h->id]) && $asistenciasPorHorario[$h->id]->count() > 0) {
                            $r['des_con_asist']++;
                        } else {
                            $r['des_sin_asist']++;
                        }
                        break;
                    case 'Confirmado':
                        $r['pas_confirmado']++;
                        break;
                    case 'No Inicio':
                        $r['pas_no_inicio']++;
                        break;
                    case 'Postergado':
                        $r['pas_postergado']++;
                        break;
                    default:
                        $r['pas_otro']++;
                }
            } else {
                $r['futuros']++;
            }

            // Acumular en el día correspondiente
            if (isset($r['por_dia'][$h->fecha_desarrollo])) {
                $r['por_dia'][$h->fecha_desarrollo]['count']++;
                $r['por_dia'][$h->fecha_desarrollo]['horarios'][] = $h;
            }
            unset($r);
        }

        // Calcular paralelos por día (max sesiones solapadas en tiempo) por responsable
        foreach ($responsables as $rid => &$r) {
            $maxSemana = 0;
            $diasParalelo = 0;
            foreach ($r['por_dia'] as $f => &$dia) {
                if (empty($dia['horarios'])) continue;
                $eventos = [];
                foreach ($dia['horarios'] as $hh) {
                    $eventos[] = [strtotime($hh->fecha_desarrollo . ' ' . $hh->hora_inicio), +1];
                    $eventos[] = [strtotime($hh->fecha_desarrollo . ' ' . $hh->hora_fin),   -1];
                }
                usort($eventos, fn ($a, $b) => $a[0] === $b[0] ? $a[1] - $b[1] : $a[0] - $b[0]);
                $cur = 0; $max = 0;
                foreach ($eventos as $e) { $cur += $e[1]; if ($cur > $max) $max = $cur; }
                $dia['paralelo'] = $max;
                if ($max > 1) $diasParalelo++;
                if ($max > $maxSemana) $maxSemana = $max;
                unset($dia['horarios']); // no serializamos los objetos
            }
            unset($dia);
            $r['max_paralelo_semana'] = $maxSemana;
            $r['dias_con_paralelo']   = $diasParalelo;
        }
        unset($r);

        // Ordenar: con asignado primero por nombre, "Sin asignar" al final
        usort($responsables, function ($a, $b) {
            if ($a['sin_asignar'] !== $b['sin_asignar']) {
                return $a['sin_asignar'] ? 1 : -1;
            }
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        return view('admin.seguimiento-diario.index', compact(
            'inicio', 'fin', 'hoy',
            'semanaAnterior', 'semanaSiguiente', 'semanaHoy',
            'dias', 'asistenciasPorHorario', 'resumen', 'responsables'
        ));
    }
}
