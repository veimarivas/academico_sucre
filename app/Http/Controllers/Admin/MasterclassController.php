<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterclassController extends Controller
{
    public function index()
    {
        $programasInscripcion = DB::table('programas')
            ->join('estados', 'programas.estado_id', '=', 'estados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('estados.nombre', 'Inscripciones')
            ->orderBy('programas.nombre')
            ->select(
                'programas.id',
                'programas.nombre',
                'programas.codigo',
                'programas.gestion',
                'programas.fecha_inicio',
                'tipos.nombre as tipo_nombre'
            )
            ->get();

        return view('admin.marketing.masterclass.index', compact('programasInscripcion'));
    }

    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $eventos = DB::table('eventos_marketing')
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

        $estadoColor = [
            'programado' => '#299cdb',
            'en_curso' => '#f7b84b',
            'finalizado' => '#0ab39c',
            'cancelado' => '#f06548',
        ];

        $events = $eventos->map(function ($e) use ($estadoColor) {
            return [
                'id' => $e->id,
                'title' => $e->nombre_evento,
                'start' => $e->fecha_inicio,
                'end' => $e->fecha_fin,
                'color' => $estadoColor[$e->estado] ?? '#6c757d',
                'display' => 'block',
                'extendedProps' => [
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

        return response()->json($events);
    }

    private function rules($estadoRequired = false)
    {
        $rules = [
            'programa_id' => 'required|integer|exists:programas,id',
            'nombre_evento' => 'required|string|max:200',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'modalidad' => 'required|in:virtual,presencial',
            'ubicacion' => 'nullable|string|max:300',
            'enlace_virtual' => 'nullable|url|max:500',
            'costo_inscripcion' => 'nullable|numeric|min:0',
            'capacidad_maxima' => 'nullable|integer|min:1',
        ];

        if ($estadoRequired) {
            $rules['estado'] = 'required|in:programado,en_curso,finalizado,cancelado';
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $id = DB::table('eventos_marketing')->insertGetId([
            'programa_id' => $validated['programa_id'],
            'nombre_evento' => $validated['nombre_evento'],
            'tipo_evento' => 'master_class',
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'ubicacion' => $validated['modalidad'] === 'presencial' ? ($validated['ubicacion'] ?? null) : null,
            'enlace_virtual' => $validated['modalidad'] === 'virtual' ? ($validated['enlace_virtual'] ?? null) : null,
            'costo_inscripcion' => $validated['costo_inscripcion'] ?? 0,
            'capacidad_maxima' => $validated['capacidad_maxima'] ?? null,
            'asistentes_registrados' => 0,
            'estado' => 'programado',
            'created' => now(),
            'modified' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Masterclass registrada correctamente',
            'id' => $id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $evento = DB::table('eventos_marketing')->where('id', $id)->where('tipo_evento', 'master_class')->first();
        if (!$evento) {
            return response()->json(['success' => false, 'message' => 'Masterclass no encontrada'], 404);
        }

        $validated = $request->validate($this->rules(true));

        DB::table('eventos_marketing')->where('id', $id)->update([
            'programa_id' => $validated['programa_id'],
            'nombre_evento' => $validated['nombre_evento'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'ubicacion' => $validated['modalidad'] === 'presencial' ? ($validated['ubicacion'] ?? null) : null,
            'enlace_virtual' => $validated['modalidad'] === 'virtual' ? ($validated['enlace_virtual'] ?? null) : null,
            'costo_inscripcion' => $validated['costo_inscripcion'] ?? 0,
            'capacidad_maxima' => $validated['capacidad_maxima'] ?? null,
            'estado' => $validated['estado'],
            'modified' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Masterclass actualizada correctamente']);
    }

    public function destroy($id)
    {
        $deleted = DB::table('eventos_marketing')->where('id', $id)->where('tipo_evento', 'master_class')->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Masterclass no encontrada'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Masterclass eliminada correctamente']);
    }
}
