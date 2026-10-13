<?php

namespace App\Http\Controllers\Admin\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permisos = Permission::with('roles')->withCount('roles')->orderBy('name')->get();
        $grupos = $permisos->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'general';
        });
        return view('admin.seguridad.permisos', compact('permisos', 'grupos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:permissions,name',
        ], [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique' => 'Ya existe un permiso con ese nombre.',
        ]);
        Permission::create(['name' => $data['name'], 'guard_name' => 'web']);
        return back()->with('success', 'Permiso creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $perm = Permission::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:permissions,name,'.$perm->id,
        ]);
        $perm->update(['name' => $data['name']]);
        return back()->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy($id)
    {
        $perm = Permission::findOrFail($id);
        $perm->delete();
        return back()->with('success', 'Permiso eliminado correctamente.');
    }
}
