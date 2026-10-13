<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $page = (int) $request->get('page', 1);
            $perPage = 20;

            $query = DB::table('docentes as d')
                ->leftJoin('modulos as m', 'd.id', '=', 'm.docente_id')
                ->leftJoin('programas as p', 'm.programa_id', '=', 'p.id');

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('d.ci', 'like', "%{$search}%")
                      ->orWhere('d.nombres', 'like', "%{$search}%")
                      ->orWhere('d.apellidos', 'like', "%{$search}%");
                });
            }

            $countQuery = clone $query;
            $total = $countQuery->distinct()->count('d.id');

            $docenteIds = $query
                ->select('d.id')
                ->groupBy('d.id')
                ->orderBy('d.apellidos')
                ->orderBy('d.nombres')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->pluck('d.id');

            $docentes = collect();
            if ($docenteIds->isNotEmpty()) {
                $docentes = DB::table('docentes as d')
                    ->leftJoin('modulos as m', 'd.id', '=', 'm.docente_id')
                    ->leftJoin('programas as p', 'm.programa_id', '=', 'p.id')
                    ->whereIn('d.id', $docenteIds)
                    ->select(
                        'd.id',
                        'd.ci',
                        'd.nombres',
                        'd.apellidos',
                        'd.correo',
                        'd.celular',
                        'd.banco',
                        'd.n_cuenta',
                        DB::raw('COUNT(DISTINCT m.id) as total_modulos'),
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT(p.nombre, " (", p.gestion, ")") SEPARATOR "||") as programas')
                    )
                    ->groupBy('d.id', 'd.ci', 'd.nombres', 'd.apellidos', 'd.correo', 'd.celular', 'd.banco', 'd.n_cuenta')
                    ->orderBy('d.apellidos')
                    ->orderBy('d.nombres')
                    ->get();

                $docentes->transform(function ($d) {
                    $d->programas = $d->programas ? explode('||', $d->programas) : [];
                    return $d;
                });
            }

            $lastPage = (int) ceil($total / $perPage);

            $html = view('admin.docentes.partials.table', compact('docentes', 'page', 'lastPage', 'total'))->render();
            return response()->json([
                'html' => $html,
                'total' => $total,
                'page' => $page,
                'lastPage' => $lastPage,
            ]);
        }

        return view('admin.docentes.index');
    }

    public function modulos($id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first(['id', 'nombres', 'apellidos', 'ci']);

        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }

        $programas = DB::table('modulos as m')
            ->join('programas as p', 'm.programa_id', '=', 'p.id')
            ->where('m.docente_id', $id)
            ->select(
                'p.id as programa_id',
                'p.nombre as programa_nombre',
                'p.gestion'
            )
            ->distinct()
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.nombre')
            ->get();

        $data = [];

        foreach ($programas as $prog) {
            $modulos = DB::table('modulos')
                ->where('docente_id', $id)
                ->where('programa_id', $prog->programa_id)
                ->select('id', 'nombre', 'desarrollo', 'pago_docente', 'proceso_pago')
                ->orderBy('id')
                ->get();

            $data[] = [
                'programa' => $prog->programa_nombre . ' (' . $prog->gestion . ')',
                'modulos' => $modulos,
            ];
        }

        return response()->json([
            'docente' => $docente,
            'programas' => $data,
        ]);
    }

    public function catalogos()
    {
        $universidades = DB::table('universidades')->orderBy('nombre')->get(['id', 'nombre']);
        $grados = DB::table('grados_academicos')->orderBy('nombre')->get(['id', 'nombre']);
        $profesiones = DB::table('profesiones')->orderBy('nombre')->get(['id', 'nombre']);
        $areas = DB::table('areas')->orderBy('nombre')->get(['id', 'nombre', 'color']);

        return response()->json([
            'universidades' => $universidades,
            'grados_academicos' => $grados,
            'profesiones' => $profesiones,
            'areas' => $areas,
        ]);
    }

    public function storeCatalogo(Request $request)
    {
        $tipo = $request->input('tipo');
        $nombre = $request->input('nombre');

        if (!in_array($tipo, ['universidad', 'grado_academico', 'profesion'])) {
            return response()->json(['error' => 'Tipo de catálogo inválido'], 422);
        }

        $request->validate(['nombre' => 'required|string|max:255']);

        $tabla = match ($tipo) {
            'universidad' => 'universidades',
            'grado_academico' => 'grados_academicos',
            'profesion' => 'profesiones',
        };

        $exists = DB::table($tabla)->where('nombre', $nombre)->exists();
        if ($exists) {
            return response()->json(['error' => 'El valor ya existe en el catálogo'], 422);
        }

        $id = DB::table($tabla)->insertGetId(['nombre' => $nombre]);

        return response()->json(['success' => true, 'id' => $id, 'nombre' => $nombre]);
    }

    public function estudios($id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first(['id']);
        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }

        $estudios = DB::table('docente_estudios as de')
            ->join('universidades as u', 'de.universidad_id', '=', 'u.id')
            ->join('grados_academicos as ga', 'de.grado_academico_id', '=', 'ga.id')
            ->join('profesiones as p', 'de.profesion_id', '=', 'p.id')
            ->where('de.docente_id', $id)
            ->select(
                'de.*',
                'u.id as universidad_id',
                'u.nombre as universidad',
                'ga.id as grado_academico_id',
                'ga.nombre as grado_academico',
                'p.id as profesion_id',
                'p.nombre as profesion'
            )
            ->orderBy('de.created_at', 'desc')
            ->get();

        $estudioIds = $estudios->pluck('id');
        $areasPorEstudio = [];
        if ($estudioIds->isNotEmpty()) {
            $pivots = DB::table('docente_estudio_areas')
                ->whereIn('docente_estudio_id', $estudioIds)
                ->get();
            foreach ($pivots as $p) {
                $areasPorEstudio[$p->docente_estudio_id][] = $p->area_id;
            }
        }

        $estudios->transform(function ($e) use ($areasPorEstudio) {
            $e->area_ids = $areasPorEstudio[$e->id] ?? [];
            return $e;
        });

        return response()->json(['estudios' => $estudios]);
    }

    public function storeEstudio(Request $request, $id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first(['id']);
        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }

        $validated = $request->validate([
            'universidad_id' => 'required|integer|exists:universidades,id',
            'grado_academico_id' => 'required|integer|exists:grados_academicos,id',
            'profesion_id' => 'required|integer|exists:profesiones,id',
            'estado' => 'required|string|max:50',
            'area_ids' => 'nullable|array',
            'area_ids.*' => 'integer|exists:areas,id',
        ]);

        $validated['docente_id'] = (int) $id;
        $areaIds = $validated['area_ids'] ?? [];
        unset($validated['area_ids']);

        $estudioId = DB::table('docente_estudios')->insertGetId($validated);

        if (!empty($areaIds)) {
            $pivot = array_map(fn($aId) => [
                'docente_estudio_id' => $estudioId,
                'area_id' => $aId,
            ], $areaIds);
            DB::table('docente_estudio_areas')->insert($pivot);
        }

        return response()->json(['success' => true, 'message' => 'Estudio registrado correctamente']);
    }

    public function updateEstudio(Request $request, $id)
    {
        $estudio = DB::table('docente_estudios')->where('id', $id)->first();
        if (!$estudio) {
            return response()->json(['error' => 'Estudio no encontrado'], 404);
        }

        $validated = $request->validate([
            'universidad_id' => 'required|integer|exists:universidades,id',
            'grado_academico_id' => 'required|integer|exists:grados_academicos,id',
            'profesion_id' => 'required|integer|exists:profesiones,id',
            'estado' => 'required|string|max:50',
            'area_ids' => 'nullable|array',
            'area_ids.*' => 'integer|exists:areas,id',
        ]);

        DB::table('docente_estudios')->where('id', $id)->update($validated);

        $areaIds = $validated['area_ids'] ?? [];
        DB::table('docente_estudio_areas')->where('docente_estudio_id', $id)->delete();
        if (!empty($areaIds)) {
            $pivot = array_map(fn($aId) => [
                'docente_estudio_id' => (int) $id,
                'area_id' => $aId,
            ], $areaIds);
            DB::table('docente_estudio_areas')->insert($pivot);
        }

        return response()->json(['success' => true, 'message' => 'Estudio actualizado correctamente']);
    }

    public function destroyEstudio($id)
    {
        $estudio = DB::table('docente_estudios')->where('id', $id)->first();
        if (!$estudio) {
            return response()->json(['error' => 'Estudio no encontrado'], 404);
        }

        DB::table('docente_estudio_areas')->where('docente_estudio_id', $id)->delete();
        DB::table('docente_estudios')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Estudio eliminado correctamente']);
    }

    public function data($id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first();
        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }
        return response()->json(['docente' => $docente]);
    }

    public function update(Request $request, $id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first();
        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }

        $validated = $request->validate([
            'ci' => 'required|string|min:7|max:13|' . Rule::unique('docentes', 'ci')->ignore($id),
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => 'required|string|max:100',
            'celular' => 'required|string|max:20',
            'usuario' => 'nullable|string|max:50',
            'contrasena' => 'nullable|string|max:50',
            'banco' => 'nullable|string|max:200',
            'n_cuenta' => 'nullable|string|max:100',
        ]);

        DB::table('docentes')->where('id', $id)->update([
            'ci' => $validated['ci'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'correo' => $validated['correo'],
            'celular' => $validated['celular'],
            'usuario' => $validated['usuario'],
            'contrasena' => $validated['contrasena'],
            'banco' => $validated['banco'],
            'n_cuenta' => $validated['n_cuenta'],
        ]);

        return response()->json(['success' => true, 'message' => 'Docente actualizado correctamente']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|min:7|max:13|unique:docentes,ci',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => 'required|string|max:100',
            'celular' => 'required|string|max:20',
            'usuario' => 'nullable|string|max:50',
            'contrasena' => 'nullable|string|max:50',
            'banco' => 'nullable|string|max:200',
            'n_cuenta' => 'nullable|string|max:100',
        ]);

        DB::table('docentes')->insert([
            'ci' => $validated['ci'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'correo' => $validated['correo'],
            'celular' => $validated['celular'],
            'usuario' => $validated['usuario'],
            'contrasena' => $validated['contrasena'],
            'banco' => $validated['banco'],
            'n_cuenta' => $validated['n_cuenta'],
        ]);

        return response()->json(['success' => true, 'message' => 'Docente registrado correctamente']);
    }

    public function checkCi(Request $request)
    {
        $request->validate(['ci' => 'required|string|min:7|max:13']);
        $ci = $request->input('ci');
        $exists = DB::table('docentes')->where('ci', $ci)->exists();
        $docente = null;
        if ($exists) {
            $docente = DB::table('docentes')->where('ci', $ci)->first(['id', 'nombres', 'apellidos', 'ci', 'correo']);
        }
        return response()->json(['exists' => $exists, 'docente' => $docente]);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = DB::table('docentes')->where('correo', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function show($id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first();
        if (!$docente) {
            abort(404, 'Docente no encontrado');
        }

        $programas = DB::table('modulos as m')
            ->join('programas as p', 'm.programa_id', '=', 'p.id')
            ->leftJoin('tipos', 'p.tipo_id', '=', 'tipos.id')
            ->leftJoin('estados', 'p.estado_id', '=', 'estados.id')
            ->where('m.docente_id', $id)
            ->select(
                'p.id as programa_id',
                'p.nombre as programa_nombre',
                'p.gestion',
                'p.codigo',
                'tipos.nombre as tipo_nombre',
                'estados.id as estado_id',
                'estados.nombre as estado_nombre',
                DB::raw('COUNT(DISTINCT m.id) as total_modulos'),
                DB::raw('SUM(m.pago_docente) as total_pago')
            )
            ->groupBy('p.id', 'p.nombre', 'p.gestion', 'p.codigo', 'tipos.nombre', 'estados.id', 'estados.nombre')
            ->orderBy('p.gestion', 'desc')
            ->orderBy('p.nombre')
            ->get();

        $modulos = DB::table('modulos')
            ->where('docente_id', $id)
            ->select('id', 'nombre', 'desarrollo', 'programa_id', 'pago_docente', 'proceso_pago')
            ->orderBy('programa_id')
            ->orderBy('id')
            ->get();

        return view('admin.docentes.show', compact('docente', 'programas', 'modulos'));
    }

    public function events(Request $request, $id)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $docente = DB::table('docentes')->where('id', $id)->first(['id', 'nombres', 'apellidos', 'ci']);
        if (!$docente) {
            return response()->json([]);
        }

        $moduloIds = DB::table('modulos')
            ->where('docente_id', $id)
            ->pluck('id');

        if ($moduloIds->isEmpty()) {
            return response()->json([]);
        }

        $horarios = DB::table('horarios')
            ->join('modulos', 'horarios.modulo_id', '=', 'modulos.id')
            ->leftJoin('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->whereIn('horarios.modulo_id', $moduloIds)
            ->whereBetween('horarios.fecha_desarrollo', [$start, $end])
            ->select(
                'horarios.id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado as horario_estado',
                'horarios.modulo_id',
                'horarios.asignado_id',
                'horarios.observaciones',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_desarrollo',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion',
                'tipos.nombre as tipo_nombre',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.color as asignado_color',
                'asignados.hexadecimal as asignado_hex'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        $events = $horarios->map(function ($h) {
            return [
                'id' => $h->id,
                'title' => $h->modulo_nombre . ' (' . $h->programa_codigo . ')',
                'start' => $h->fecha_desarrollo . 'T' . $h->hora_inicio,
                'end' => $h->fecha_desarrollo . 'T' . $h->hora_fin,
                'color' => $h->asignado_hex ?? '#6c757d',
                'display' => 'block',
                'extendedProps' => [
                    'horario_id' => $h->id,
                    'modulo' => $h->modulo_nombre,
                    'modulo_desarrollo' => $h->modulo_desarrollo,
                    'programa' => $h->programa_nombre,
                    'programa_codigo' => $h->programa_codigo,
                    'gestion' => $h->gestion,
                    'tipo' => $h->tipo_nombre,
                    'horario_estado' => $h->horario_estado,
                    'hora_inicio' => $h->hora_inicio,
                    'hora_fin' => $h->hora_fin,
                    'observaciones' => $h->observaciones,
                    'asignado_nombres' => $h->asignado_nombres,
                    'asignado_apellidos' => $h->asignado_apellidos,
                    'asignado_color' => $h->asignado_color,
                    'asignado_hex' => $h->asignado_hex,
                ],
            ];
        });

        return response()->json($events);
    }
}
