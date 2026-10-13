<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function index()
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
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('modulos.desarrollo', 'No Inicio')
            ->where('horarios.fecha_desarrollo', '<', $hoy);

        if (!empty($programaIds)) {
            $qModulos->whereIn('modulos.programa_id', $programaIds);
        }

        $modulosNoInicio = $qModulos->select(
                'modulos.id as modulo_id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_estado',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                DB::raw('COUNT(DISTINCT horarios.id) as horarios_pasados'),
                DB::raw('MIN(horarios.fecha_desarrollo) as primera_fecha'),
                DB::raw('MAX(horarios.fecha_desarrollo) as ultima_fecha')
            )
            ->groupBy('modulos.id', 'modulos.nombre', 'modulos.desarrollo', 'programas.id', 'programas.nombre', 'programas.gestion', 'tipos.nombre', 'docentes.nombres', 'docentes.apellidos')
            ->orderByDesc('ultima_fecha')
            ->get()
            ->map(function ($m) {
                return (object) [
                    'tipo' => 'modulo_no_inicio',
                    'tipo_label' => 'Módulo sin iniciar',
                    'modulo_id' => $m->modulo_id,
                    'modulo_nombre' => $m->modulo_nombre,
                    'modulo_estado' => $m->modulo_estado,
                    'programa_id' => $m->programa_id,
                    'programa_nombre' => $m->programa_nombre,
                    'programa_gestion' => $m->gestion,
                    'tipo_nombre' => $m->tipo_nombre,
                    'docente' => $m->docente_nombres ? trim("{$m->docente_nombres} {$m->docente_apellidos}") : '—',
                    'horarios_pasados' => $m->horarios_pasados,
                    'primera_fecha' => $m->primera_fecha,
                    'ultima_fecha' => $m->ultima_fecha,
                    'mensaje' => "Módulo «{$m->modulo_nombre}» del programa «{$m->programa_nombre}» sigue en <strong>No Inicio</strong> con {$m->horarios_pasados} horario(s) pasados.",
                    'url' => route('admin.programas.show', $m->programa_id) . "#modulo-{$m->modulo_id}",
                    'fecha' => $m->ultima_fecha,
                ];
            });

        foreach ($modulosNoInicio as $m) {
            $items[] = $m;
        }

        // 2. Horarios con fecha pasada que NO están "Desarrollados" (sin asistencia registrada)
        $qHorarios = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
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
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado',
                'modulos.id as modulo_id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_estado',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos'
            )
            ->distinct()
            ->orderByDesc('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get()
            ->map(function ($h) {
                return (object) [
                    'tipo' => 'horario_vencido',
                    'tipo_label' => 'Sesión vencida',
                    'horario_id' => $h->horario_id,
                    'fecha_desarrollo' => $h->fecha_desarrollo,
                    'hora_inicio' => $h->hora_inicio,
                    'hora_fin' => $h->hora_fin,
                    'estado' => $h->estado,
                    'modulo_id' => $h->modulo_id,
                    'modulo_nombre' => $h->modulo_nombre,
                    'modulo_estado' => $h->modulo_estado,
                    'programa_id' => $h->programa_id,
                    'programa_nombre' => $h->programa_nombre,
                    'programa_gestion' => $h->gestion,
                    'tipo_nombre' => $h->tipo_nombre,
                    'docente' => $h->docente_nombres ? trim("{$h->docente_nombres} {$h->docente_apellidos}") : '—',
                    'mensaje' => "Sesión del {$h->fecha_desarrollo} del módulo «{$h->modulo_nombre}» ({$h->programa_nombre}) está como <strong>{$h->estado}</strong> sin asistencia registrada.",
                    'url' => route('admin.programas.modulo-detalle', $h->modulo_id),
                    'fecha' => $h->fecha_desarrollo,
                ];
            });

        foreach ($horariosVencidos as $h) {
            $items[] = $h;
        }

        $total = count($items);

        // Agrupar por programa → módulo → tipo → estado
        $grouped = [];
        $programaMap = [];

        // Agrupar cambios de estado
        foreach ($modulosNoInicio as $item) {
            $pid = $item->programa_id;
            $mid = $item->modulo_id;

            if (!isset($programaMap[$pid])) {
                $programaMap[$pid] = [
                    'programa_id' => $pid,
                    'programa_nombre' => $item->programa_nombre,
                    'gestion' => $item->programa_gestion,
                    'tipo_nombre' => $item->tipo_nombre,
                    'modulos' => [],
                ];
            }
            if (!isset($programaMap[$pid]['modulos'][$mid])) {
                $programaMap[$pid]['modulos'][$mid] = [
                    'modulo_id' => $mid,
                    'modulo_nombre' => $item->modulo_nombre,
                    'docente' => $item->docente,
                    'tipos' => [],
                ];
            }

            $tipoKey = 'cambio_estado';
            if (!isset($programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey])) {
                $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey] = [
                    'tipo_key' => $tipoKey,
                    'tipo_label' => 'Cambio de estado',
                    'icono' => 'ri-refresh-line',
                    'color' => '#f7b84b',
                    'estados' => [],
                ];
            }

            $estadoValor = $item->modulo_estado;
            if (!isset($programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor])) {
                $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor] = [
                    'estado' => $estadoValor,
                    'items' => [],
                ];
            }
            $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor]['items'][] = $item;
        }

        // Agrupar registro de asistencia
        foreach ($horariosVencidos as $item) {
            $pid = $item->programa_id;
            $mid = $item->modulo_id;

            if (!isset($programaMap[$pid])) {
                $programaMap[$pid] = [
                    'programa_id' => $pid,
                    'programa_nombre' => $item->programa_nombre,
                    'gestion' => $item->programa_gestion,
                    'tipo_nombre' => $item->tipo_nombre,
                    'modulos' => [],
                ];
            }
            if (!isset($programaMap[$pid]['modulos'][$mid])) {
                $programaMap[$pid]['modulos'][$mid] = [
                    'modulo_id' => $mid,
                    'modulo_nombre' => $item->modulo_nombre,
                    'docente' => $item->docente,
                    'tipos' => [],
                ];
            }

            $tipoKey = 'registro_asistencia';
            if (!isset($programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey])) {
                $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey] = [
                    'tipo_key' => $tipoKey,
                    'tipo_label' => 'Registro de asistencia',
                    'icono' => 'ri-clipboard-line',
                    'color' => '#f06548',
                    'estados' => [],
                ];
            }

            $estadoValor = $item->estado;
            if (!isset($programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor])) {
                $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor] = [
                    'estado' => $estadoValor,
                    'items' => [],
                ];
            }
            $programaMap[$pid]['modulos'][$mid]['tipos'][$tipoKey]['estados'][$estadoValor]['items'][] = $item;
        }

        // Re-indexar para la vista
        $grouped = array_values(array_map(function ($prog) {
            $prog['modulos'] = array_values(array_map(function ($mod) {
                $mod['tipos'] = array_values($mod['tipos']);
                // En cada tipo, re-indexar estados
                $mod['tipos'] = array_map(function ($tipo) {
                    $tipo['estados'] = array_values($tipo['estados']);
                    // Ordenar items dentro de cada estado por fecha descendente
                    foreach ($tipo['estados'] as &$est) {
                        usort($est['items'], function ($a, $b) {
                            return strcmp($b->fecha, $a->fecha);
                        });
                    }
                    return $tipo;
                }, $mod['tipos']);
                return $mod;
            }, $prog['modulos']));
            return $prog;
        }, $programaMap));

        // Ordenar programas por nombre
        usort($grouped, function ($a, $b) {
            return strcmp($a['programa_nombre'], $b['programa_nombre']);
        });

        // Agrupar por gestión
        $groupedByGestion = [];
        foreach ($grouped as $prog) {
            $g = $prog['gestion'] ?: 'Sin gestión';
            $groupedByGestion[$g][] = $prog;
        }

        // Ordenar gestiones descendente (sin gestión al final)
        uksort($groupedByGestion, function ($a, $b) {
            if ($a === 'Sin gestión') return 1;
            if ($b === 'Sin gestión') return -1;
            return $b <=> $a;
        });
        $gestiones = array_keys($groupedByGestion);

        return view('admin.notificaciones.index', compact(
            'grouped', 'total',
            'groupedByGestion', 'gestiones'
        ));
    }
}
