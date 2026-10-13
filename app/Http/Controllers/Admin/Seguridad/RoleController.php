<?php

namespace App\Http\Controllers\Admin\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->with('permissions')->orderBy('name')->get();
        $permisos = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'general';
        });
        return view('admin.seguridad.roles', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permissions'])) {
            $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
        }
        return back()->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:80|unique:roles,name,'.$role->id,
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if (strtolower($role->name) === 'super admin' && $data['name'] !== $role->name) {
            return back()->with('error', 'No puedes renombrar el rol Super Admin.');
        }

        $role->update(['name' => $data['name']]);
        $role->syncPermissions(Permission::whereIn('id', $data['permissions'] ?? [])->get());

        return back()->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if (strtolower($role->name) === 'super admin') {
            return back()->with('error', 'No puedes eliminar el rol Super Admin.');
        }
        if ($role->users()->exists()) {
            return back()->with('error', 'No puedes eliminar un rol que tiene usuarios asignados.');
        }
        $role->delete();
        return back()->with('success', 'Rol eliminado correctamente.');
    }
}
