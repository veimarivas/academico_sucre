<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('areas')
            ->select('areas.*')
            ->selectRaw('(SELECT COUNT(*) FROM areas_programas WHERE areas_programas.area_id = areas.id) as programas_count');

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('areas.nombre', 'like', "%{$search}%")
                  ->orWhere('areas.descripcion', 'like', "%{$search}%");
            });
        }

        $areas = $query->orderBy('areas.nombre', 'asc')->get();

        $resumen = [
            'total_areas' => $areas->count(),
            'total_programas' => (int) $areas->sum('programas_count'),
            'sin_uso' => $areas->where('programas_count', 0)->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.areas.partials.table', compact('areas'))->render(),
                'total' => $areas->count(),
                'resumen' => $resumen,
            ]);
        }

        return view('admin.areas.index', compact('areas', 'resumen'));
    }

    public function data($id)
    {
        $area = DB::table('areas')->where('id', $id)->first();
        if (!$area) {
            return response()->json(['success' => false, 'message' => 'Área no encontrada'], 404);
        }
        return response()->json(['area' => $area]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:areas,nombre',
            'color' => 'nullable|string|max:200',
            'descripcion' => 'nullable|string|max:200',
        ]);

        $id = DB::table('areas')->insertGetId([
            'nombre' => $validated['nombre'],
            'color' => $validated['color'] ?? '#6c757d',
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Área creada correctamente',
            'id' => $id,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!DB::table('areas')->where('id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Área no encontrada'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:areas,nombre,' . $id,
            'color' => 'nullable|string|max:200',
            'descripcion' => 'nullable|string|max:200',
        ]);

        DB::table('areas')->where('id', $id)->update([
            'nombre' => $validated['nombre'],
            'color' => $validated['color'] ?? '#6c757d',
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Área actualizada correctamente',
        ]);
    }

    public function destroy($id)
    {
        $area = DB::table('areas')->where('id', $id)->first();
        if (!$area) {
            return response()->json(['success' => false, 'message' => 'Área no encontrada'], 404);
        }

        $enUso = DB::table('areas_programas')->where('area_id', $id)->count();
        if ($enUso > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: existen {$enUso} programa(s) asociados a esta área.",
            ], 422);
        }

        DB::table('areas')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Área eliminada correctamente',
        ]);
    }
}
