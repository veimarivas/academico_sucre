<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PagosQr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * QR de pago y cuentas bancarias (Contable): se registran con su fecha de
 * vencimiento y se adjuntan en los mensajes masivos de cobranza y de grupos.
 */
class PagoQrController extends Controller
{
    public function index(): View
    {
        $qrs = DB::table('pagos_qr as q')
            ->leftJoin('users as u', 'u.id', '=', 'q.user_id')
            ->orderByDesc('q.activo')->orderBy('q.fecha_vencimiento')
            ->get(['q.*', 'u.fullname as registrado_por'])
            ->map(fn ($q) => PagosQr::conEstado($q))
            // Primero los que se pueden usar (por vencer, vigentes), luego vencidos y desactivados.
            ->sortBy(fn ($q) => [['por_vencer' => 0, 'vigente' => 1, 'vencido' => 2, 'inactivo' => 3][$q->estado], $q->fecha_vencimiento])
            ->values();

        return view('admin.contable.pagos-qr', [
            'qrs' => $qrs,
            'avisoDias' => PagosQr::AVISO_DIAS,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validar($request, true);
        $data['imagen_path'] = $this->guardarImagen($request);
        $id = DB::table('pagos_qr')->insertGetId($data + [
            'activo' => true,
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $actual = DB::table('pagos_qr')->where('id', $id)->first();
        abort_unless($actual, 404);
        $data = $this->validar($request, false);
        if ($request->hasFile('imagen')) {
            $data['imagen_path'] = $this->guardarImagen($request);
            Storage::disk(PagosQr::DISCO)->delete($actual->imagen_path);
        }
        DB::table('pagos_qr')->where('id', $id)->update($data + ['updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function toggle(int $id): JsonResponse
    {
        $q = DB::table('pagos_qr')->where('id', $id)->first();
        abort_unless($q, 404);
        DB::table('pagos_qr')->where('id', $id)->update(['activo' => ! $q->activo, 'updated_at' => now()]);

        return response()->json(['success' => true, 'activo' => ! $q->activo]);
    }

    public function destroy(int $id): JsonResponse
    {
        $q = DB::table('pagos_qr')->where('id', $id)->first();
        abort_unless($q, 404);
        DB::table('pagos_qr')->where('id', $id)->delete();
        Storage::disk(PagosQr::DISCO)->delete($q->imagen_path);

        return response()->json(['success' => true]);
    }

    /** Imagen del QR (disco privado: solo con sesión iniciada). */
    public function imagen(int $id): StreamedResponse
    {
        $q = DB::table('pagos_qr')->where('id', $id)->first();
        abort_unless($q && Storage::disk(PagosQr::DISCO)->exists($q->imagen_path), 404);

        return Storage::disk(PagosQr::DISCO)->response($q->imagen_path, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    private function validar(Request $request, bool $nuevo): array
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'banco' => 'required|string|max:120',
            'titular' => 'nullable|string|max:160',
            'numero_cuenta' => 'nullable|string|max:60',
            'tipo_cuenta' => 'nullable|string|max:60',
            'moneda' => 'nullable|string|max:10',
            'fecha_vencimiento' => 'required|date',
            'observacion' => 'nullable|string|max:500',
            'imagen' => ($nuevo ? 'required' : 'nullable').'|image|mimes:png,jpg,jpeg,webp|max:5120',
        ], [
            'nombre.required' => 'Poné un nombre para reconocer el QR (p. ej. «Cobranzas BNB»).',
            'banco.required' => 'Indicá el banco.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria: un QR vencido no se puede enviar.',
            'imagen.required' => 'Subí la imagen del QR.',
            'imagen.image' => 'El archivo debe ser una imagen (PNG, JPG o WEBP).',
        ]);
        unset($data['imagen']);
        $data['moneda'] = $data['moneda'] ?? 'Bs';

        return $data;
    }

    private function guardarImagen(Request $request): string
    {
        $file = $request->file('imagen');

        return $file->storeAs(PagosQr::CARPETA, Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'png'), PagosQr::DISCO);
    }
}
