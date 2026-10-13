<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronogramaController extends Controller
{
    private function programColor($id)
    {
        $palette = ['#0ab39c','#299cdb','#405189','#f7b84b','#f06548','#e83e8c','#6610f2','#20c997','#fd7e14','#17a2b8','#6f42c1','#d63384'];
        return $palette[$id % count($palette)];
    }

    public function index()
    {
        $asignados = DB::table('asignados')->orderBy('apellidos')->orderBy('nombres')->get();

        return view('admin.cronogramas.index', compact('asignados'));
    }

    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $asignadoFilter = $request->filled('asignado_id');

        $query = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereBetween('horarios.fecha_desarrollo', [$start, $end])
            ->select(
                'horarios.id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado as horario_estado',
                'horarios.tipo as horario_tipo',
                'horarios.modulo_id',
                'horarios.asignado_id',
                'horarios.observaciones',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_desarrollo',
                'modulos.moodle_link as modulo_moodle_link',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.moodle_link as programa_moodle_link',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.id as docente_id',
                'docentes.ci as docente_ci',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.correo as docente_correo',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.color as asignado_color',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio');

        if ($request->filled('asignado_id')) {
            $query->where('horarios.asignado_id', $request->asignado_id);
        }

        $horarios = $query->get();

        $events = $horarios->map(function ($h) use ($asignadoFilter) {
            return [
                'id' => $h->id,
                'title' => $h->programa_nombre . ' (' . $h->programa_codigo . ')',
                'start' => $h->fecha_desarrollo . 'T' . $h->hora_inicio,
                'end' => $h->fecha_desarrollo . 'T' . $h->hora_fin,
                'color' => $asignadoFilter ? $this->programColor($h->programa_id) : ($h->asignado_hex ?? '#6c757d'),
                'display' => 'block',
                'extendedProps' => [
                    'source' => 'horario',
                    'horario_id' => $h->id,
                    'modulo' => $h->modulo_nombre,
                    'modulo_id' => $h->modulo_id,
                    'modulo_desarrollo' => $h->modulo_desarrollo,
                    'modulo_moodle_link' => $h->modulo_moodle_link,
                    'programa' => $h->programa_nombre,
                    'programa_id' => $h->programa_id,
                    'programa_codigo' => $h->programa_codigo,
                    'programa_moodle_link' => $h->programa_moodle_link,
                    'gestion' => $h->gestion,
                    'tipo' => $h->tipo_nombre,
                    'horario_estado' => $h->horario_estado,
                    'horario_tipo' => $h->horario_tipo,
                    'hora_inicio' => $h->hora_inicio,
                    'hora_fin' => $h->hora_fin,
                    'observaciones' => $h->observaciones,
                    'docente_nombres' => $h->docente_nombres,
                    'docente_apellidos' => $h->docente_apellidos,
                    'docente_ci' => $h->docente_ci,
                    'asignado_nombres' => $h->asignado_nombres,
                    'asignado_apellidos' => $h->asignado_apellidos,
                    'asignado_color' => $h->asignado_color,
                    'asignado_hex' => $h->asignado_hex,
                ],
            ];
        });

        $masterclassEvents = DB::table('eventos_marketing')
            ->leftJoin('programas', 'eventos_marketing.programa_id', '=', 'programas.id')
            ->where('eventos_marketing.tipo_evento', 'master_class')
            ->whereBetween('eventos_marketing.fecha_inicio', [$start, $end])
            ->select(
                'eventos_marketing.id',
                'eventos_marketing.nombre_evento',
                'eventos_marketing.fecha_inicio',
                'eventos_marketing.fecha_fin',
                'eventos_marketing.ubicacion',
                'eventos_marketing.enlace_virtual',
                'eventos_marketing.costo_inscripcion',
                'eventos_marketing.capacidad_maxima',
                'eventos_marketing.asistentes_registrados',
                'eventos_marketing.estado',
                'eventos_marketing.programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo'
            )
            ->orderBy('eventos_marketing.fecha_inicio')
            ->get();

        $masterclassMapped = $masterclassEvents->map(function ($e) {
            return [
                'id' => 'mc_' . $e->id,
                'title' => 'Masterclass: ' . $e->nombre_evento,
                'start' => $e->fecha_inicio,
                'end' => $e->fecha_fin ?? $e->fecha_inicio,
                'backgroundColor' => 'transparent',
                'borderColor' => '#8b5cf6',
                'textColor' => '#000000',
                'display' => 'block',
                'extendedProps' => [
                    'source' => 'masterclass',
                    'evento_id' => $e->id,
                    'nombre_evento' => $e->nombre_evento,
                    'programa' => $e->programa_nombre,
                    'programa_id' => $e->programa_id,
                    'programa_codigo' => $e->programa_codigo,
                    'fecha_inicio' => $e->fecha_inicio,
                    'fecha_fin' => $e->fecha_fin,
                    'ubicacion' => $e->ubicacion,
                    'enlace_virtual' => $e->enlace_virtual,
                    'costo_inscripcion' => $e->costo_inscripcion,
                    'capacidad_maxima' => $e->capacidad_maxima,
                    'asistentes_registrados' => $e->asistentes_registrados,
                    'estado' => $e->estado,
                ],
            ];
        });

        $events = $events->concat($masterclassMapped);

        return response()->json($events);
    }

    public function modulosInicio(Request $request)
    {
        $inicio = $request->input('inicio');
        $fin = $request->input('fin');

        if (!$inicio || !$fin) {
            return response()->json([]);
        }

        $modulos = DB::table('modulos')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->join('horarios', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->select(
                'modulos.id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_desarrollo',
                'modulos.moodle_link as modulo_moodle_link',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion',
                'programas.moodle_link as programa_moodle_link',
                'tipos.nombre as tipo_nombre',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.ci as docente_ci',
                'docentes.celular as docente_celular',
                'docentes.usuario as docente_usuario',
                'docentes.contrasena as docente_contrasena',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex',
                DB::raw('MIN(horarios.fecha_desarrollo) as primera_fecha'),
                DB::raw('MIN(horarios.hora_inicio) as primera_hora'),
                DB::raw('COUNT(horarios.id) as total_sesiones')
            )
            ->groupBy(
                'modulos.id', 'modulos.nombre', 'modulos.desarrollo', 'modulos.moodle_link',
                'programas.id', 'programas.nombre', 'programas.codigo', 'programas.gestion', 'programas.moodle_link',
                'tipos.nombre',
                'docentes.id', 'docentes.nombres', 'docentes.apellidos', 'docentes.ci', 'docentes.celular', 'docentes.usuario', 'docentes.contrasena',
                'asignados.nombres', 'asignados.apellidos', 'asignados.hexadecimal'
            )
            ->havingBetween('primera_fecha', [$inicio, $fin])
            ->orderBy('primera_fecha')
            ->orderBy('primera_hora')
            ->get();

        return response()->json($modulos);
    }

    public function moduloHorarios($moduloId)
    {
        $modulo = DB::table('modulos')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->where('modulos.id', $moduloId)
            ->select(
                'modulos.id', 'modulos.nombre as modulo_nombre',
                'modulos.docente_id', 'modulos.programa_id',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos',
                'docentes.ci as docente_ci',
                'docentes.correo as docente_correo',
                'docentes.celular as docente_celular',
                'docentes.usuario as docente_usuario',
                'docentes.contrasena as docente_contrasena'
            )
            ->first();

        if (!$modulo) {
            return response()->json(['error' => 'Módulo no encontrado'], 404);
        }

        $horarios = DB::table('horarios')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->where('horarios.modulo_id', $moduloId)
            ->select(
                'horarios.id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado',
                'horarios.tipo',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        return response()->json([
            'modulo' => $modulo,
            'horarios' => $horarios,
        ]);
    }
}