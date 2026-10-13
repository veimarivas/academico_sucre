<?php

namespace App\Http\Controllers\Admin\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Asesor;
use App\Models\Responsable;
use App\Models\User;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'asesor', 'responsable')->orderBy('fullname')->get();
        $roles = Role::orderBy('name')->get();
        $asesores = Asesor::orderBy('apellidos')->orderBy('nombres')->get();
        $responsables = Responsable::orderBy('apellidos')->orderBy('nombres')->get();
        return view('admin.seguridad.usuarios', compact('users', 'roles', 'asesores', 'responsables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:150',
            'username' => 'required|string|max:80|unique:users,username',
            'email'    => 'nullable|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'nullable|string|max:80',
            'roles'    => 'array',
            'roles.*'  => 'integer|exists:roles,id',
            'asesor_id' => 'nullable|integer|exists:asesores,id',
            'responsable_id' => 'nullable|integer|exists:responsables,id',
            'whatsapp_numero' => ['nullable', 'string', 'max:20', Rule::unique('users', 'whatsapp_numero')],
            'whatsapp_area' => ['nullable', Rule::in(array_keys(User::AREAS_WHATSAPP))],
            'whatsapp_autorizado' => 'boolean',
        ], [
            'fullname.required' => 'El nombre completo es obligatorio.',
            'username.required' => 'El usuario es obligatorio.',
            'username.unique'   => 'Ese nombre de usuario ya está en uso.',
            'email.unique'      => 'Ese correo ya está registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'whatsapp_numero.unique' => 'Ese número de WhatsApp ya está asignado a otro usuario.',
        ]);

        $user = User::create([
            'fullname' => $data['fullname'],
            'username' => $data['username'],
            'email'    => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'user',
            'asesor_id' => $data['asesor_id'] ?? null,
            'responsable_id' => (int) ($data['responsable_id'] ?? 0),
            'whatsapp_numero' => $this->normalizarNumero($data['whatsapp_numero'] ?? null),
            'whatsapp_area' => $data['whatsapp_area'] ?? null,
            'whatsapp_autorizado' => (bool) ($data['whatsapp_autorizado'] ?? false),
            'created'  => now(),
            'modified' => now(),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles(Role::whereIn('id', $data['roles'])->get());
        }

        $this->syncWhatsapp($user, $data['password'] ?? null);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'fullname' => 'required|string|max:150',
            'username' => ['required', 'string', 'max:80', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['nullable', 'email', 'max:150', Rule::unique('users','email')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'nullable|string|max:80',
            'roles'    => 'array',
            'roles.*'  => 'integer|exists:roles,id',
            'asesor_id' => 'nullable|integer|exists:asesores,id',
            'responsable_id' => 'nullable|integer|exists:responsables,id',
            'whatsapp_numero' => ['nullable', 'string', 'max:20', Rule::unique('users', 'whatsapp_numero')->ignore($user->id)],
            'whatsapp_area' => ['nullable', Rule::in(array_keys(User::AREAS_WHATSAPP))],
            'whatsapp_autorizado' => 'boolean',
        ]);

        $user->fullname = $data['fullname'];
        $user->username = $data['username'];
        $user->email = $data['email'] ?? null;
        $user->role = $data['role'] ?? $user->role;
        $user->asesor_id = $data['asesor_id'] ?? null;
        $user->responsable_id = (int) ($data['responsable_id'] ?? 0);
        $user->whatsapp_numero = $this->normalizarNumero($data['whatsapp_numero'] ?? null);
        $user->whatsapp_area = $data['whatsapp_area'] ?? null;
        $user->whatsapp_autorizado = (bool) ($data['whatsapp_autorizado'] ?? false);
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->modified = now();
        $user->save();

        $user->syncRoles(Role::whereIn('id', $data['roles'] ?? [])->get());

        $this->syncWhatsapp($user, ! empty($data['password']) ? $data['password'] : null);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
            return back()->with('error', 'Debe existir al menos un Super Admin.');
        }
        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    private function normalizarNumero(?string $numero): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $numero);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 8) {
            $digits = '591'.$digits;
        }

        return $digits;
    }

    /** Registra/actualiza la cuenta corporativa en Seguimiento ESAM. */
    private function syncWhatsapp(User $user, ?string $password = null): void
    {
        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return;
        }

        try {
            $client->registerAccount($user, $password);
        } catch (\Throwable $e) {
            Log::warning('No se pudo registrar la cuenta de WhatsApp en Seguimiento ESAM', [
                'user' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('12345678');
        $user->modified = now();
        $user->save();

        $this->syncWhatsapp($user, '12345678');

        $userName = $user->fullname ?? $user->username;
        return back()->with('success', "Contraseña de {$userName} restablecida a 12345678.");
    }
}
