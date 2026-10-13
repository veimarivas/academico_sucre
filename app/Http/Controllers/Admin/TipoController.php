<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tipos')
            ->select('tipos.*')
            ->selectRaw('(SELECT COUNT(*) FROM programas WHERE programas.tipo_id = tipos.id) as programas_count');

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('tipos.nombre', 'like', "%{$search}%")
                  ->orWhere('tipos.descripcion', 'like', "%{$search}%");
            });
        }

        $tipos = $query->orderBy('tipos.nombre', 'asc')->get();

        $resumen = [
            'total_tipos' => $tipos->count(),
            'total_programas' => (int) $tipos->sum('programas_count'),
            'sin_uso' => $tipos->where('programas_count', 0)->count(),
            'total_modulos' => (int) $tipos->sum('cantidad_modulo'),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.tipos.partials.table', compact('tipos'))->render(),
                'total' => $tipos->count(),
                'resumen' => $resumen,
            ]);
        }

        return view('admin.tipos.index', compact('tipos', 'resumen'));
    }

    public function data($id)
    {
        $tipo = DB::table('tipos')->where('id', $id)->first();
        if (!$tipo) {
            return response()->json(['success' => false, 'message' => 'Tipo no encontrado'], 404);
        }
        return response()->json(['tipo' => $tipo]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120|unique:tipos,nombre',
            'cantidad_modulo' => 'required|integer|min:0|max:200',
            'puntos' => 'nullable|numeric|min:0',
            'pago_docente_nivelacion' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $id = DB::table('tipos')->insertGetId([
            'nombre' => $validated['nombre'],
            'cantidad_modulo' => $validated['cantidad_modulo'],
            'puntos' => $validated['puntos'] ?? 0,
            'pago_docente_nivelacion' => $validated['pago_docente_nivelacion'] ?? 0,
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo creado correctamente',
            'id' => $id,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!DB::table('tipos')->where('id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tipo no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:120|unique:tipos,nombre,' . $id,
            'cantidad_modulo' => 'required|integer|min:0|max:200',
            'puntos' => 'nullable|numeric|min:0',
            'pago_docente_nivelacion' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        DB::table('tipos')->where('id', $id)->update([
            'nombre' => $validated['nombre'],
            'cantidad_modulo' => $validated['cantidad_modulo'],
            'puntos' => $validated['puntos'] ?? 0,
            'pago_docente_nivelacion' => $validated['pago_docente_nivelacion'] ?? 0,
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo actualizado correctamente',
        ]);
    }

    public function destroy($id)
    {
        $tipo = DB::table('tipos')->where('id', $id)->first();
        if (!$tipo) {
            return response()->json(['success' => false, 'message' => 'Tipo no encontrado'], 404);
        }

        $enUso = DB::table('programas')->where('tipo_id', $id)->count();
        if ($enUso > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: existen {$enUso} programa(s) asociado(s) a este tipo.",
            ], 422);
        }

        DB::table('tipos')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo eliminado correctamente',
        ]);
    }
}
