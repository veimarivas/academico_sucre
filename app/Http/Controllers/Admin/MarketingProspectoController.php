<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use App\Services\SeguimientoEsam\SenderAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Prospectos de marketing: los asesores registran a sus prospectos en los
 * programas en etapa de Inscripciones y les envían mensajes por WhatsApp
 * (desde su propia línea), como en el envío de un programa.
 *
 * Cada prospecto se sincroniza con Seguimiento ESAM como una nota de
 * seguimiento del asesor (área Marketing de /notes): ahí lo sigue el asesor y
 * lo supervisan la Gerencia y el Supervisor de Marketing.
 *
 * Alcance: el asesor ve y gestiona solo sus prospectos; Super Admin, Gerencia
 * Marketing y Supervisor de Marketing ven los de todos los asesores.
 */
class MarketingProspectoController extends Controller
{
    /** Estado de programa «Inscripciones» (tabla `estados`). */
    private const ESTADO_INSCRIPCIONES = 4;

    public const ESTADOS = [
        'nuevo' => 'Nuevo',
        'contactado' => 'Contactado',
        'interesado' => 'Interesado',
        'no_interesado' => 'No interesado',
        'inscrito' => 'Inscrito',
    ];

    /** Roles que ven el trabajo de todos los asesores. */
    private const SUPERVISAN = ['Super Admin', 'Gerencia Marketing', 'Supervisor de Marketing'];

    private function supervisa(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(self::SUPERVISAN);
    }

    /** Prospectos visibles para el usuario (con programa y asesor). */
    private function visibles()
    {
        return DB::table('marketing_prospectos as mp')
            ->leftJoin('programas as p', 'p.id', '=', 'mp.programa_id')
            ->leftJoin('users as u', 'u.id', '=', 'mp.user_id')
            ->when(! $this->supervisa(), fn ($q) => $q->where('mp.user_id', auth()->id()));
    }

    public function index(): View
    {
        $prospectos = $this->visibles()
            ->select('mp.*', 'p.nombre as programa_nombre', 'p.codigo as programa_codigo', 'p.estado_id as programa_estado_id',
                'u.fullname as asesor_nombre', 'u.username as asesor_usuario')
            ->orderByDesc('mp.created_at')
            ->get();

        // Programas en Inscripciones (para registrar) y los que ya tienen prospectos.
        $programas = DB::table('programas')
            ->where('estado_id', self::ESTADO_INSCRIPCIONES)
            ->orWhereIn('id', $prospectos->pluck('programa_id')->filter()->unique()->values())
            ->orderByDesc('gestion')->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'gestion', 'estado_id']);

        $asesores = $this->supervisa()
            ? $prospectos->groupBy('user_id')->map(fn ($g) => [
                'id' => (int) $g->first()->user_id,
                'nombre' => $g->first()->asesor_nombre ?: $g->first()->asesor_usuario ?: 'Usuario '.$g->first()->user_id,
                'total' => $g->count(),
            ])->sortBy('nombre')->values()
            : collect();

        $envios = DB::table('marketing_prospecto_envios as e')
            ->leftJoin('programas as p', 'p.id', '=', 'e.programa_id')
            ->where('e.user_id', auth()->id())
            ->orderByDesc('e.created_at')->limit(5)
            ->get(['e.*', 'p.nombre as programa_nombre']);

        $client = app(SeguimientoEsamClient::class);
        $error = $client->enabled() ? null : 'La integración con Seguimiento ESAM no está configurada: los prospectos se guardan, pero no se pueden enviar mensajes ni seguir en Notas.';
        [, $lineaError] = SenderAccount::resolve();

        return view('admin.marketing.prospectos', [
            'prospectos' => $prospectos,
            'programas' => $programas,
            'asesores' => $asesores,
            'envios' => $envios,
            'estados' => self::ESTADOS,
            'error' => $error,
            'lineaError' => $lineaError,
            'supervisa' => $this->supervisa(),
            'inscripcionesId' => self::ESTADO_INSCRIPCIONES,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validar($request);
        $this->exigirInscripciones((int) $data['programa_id']);
        $this->evitarDuplicado((int) $data['programa_id'], $data['celular']);

        $id = DB::table('marketing_prospectos')->insertGetId($data + [
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $id] + $this->sincronizar($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $actual = $this->propio($id);
        $data = $this->validar($request);
        // Si cambia de programa, el nuevo tiene que estar en Inscripciones.
        if ((int) $data['programa_id'] !== (int) $actual->programa_id) {
            $this->exigirInscripciones((int) $data['programa_id']);
        }
        $this->evitarDuplicado((int) $data['programa_id'], $data['celular'], $id);

        DB::table('marketing_prospectos')->where('id', $id)->update($data + ['updated_at' => now()]);

        return response()->json(['success' => true] + $this->sincronizar($id));
    }

    public function updateEstado(Request $request, int $id): JsonResponse
    {
        $this->propio($id);
        $data = $request->validate(['estado' => 'required|in:'.implode(',', array_keys(self::ESTADOS))]);
        DB::table('marketing_prospectos')->where('id', $id)->update(['estado' => $data['estado'], 'updated_at' => now()]);

        return response()->json(['success' => true] + $this->sincronizar($id));
    }

    /** Reintenta la sincronización con Seguimiento ESAM (p. ej. tras vincular la línea). */
    public function sincronizarUno(int $id): JsonResponse
    {
        $this->propio($id);
        $sync = $this->sincronizar($id);

        return response()->json(['success' => $sync['seguimiento_error'] === null] + $sync);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->propio($id);
        DB::table('marketing_prospectos')->where('id', $id)->delete();

        $client = app(SeguimientoEsamClient::class);
        if ($client->enabled()) {
            try {
                $client->deleteProspect($this->ref($id));
            } catch (\Throwable $e) {
                Log::warning('No se pudo quitar el seguimiento del prospecto en Seguimiento ESAM', ['id' => $id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Envío masivo a prospectos seleccionados (solo los propios: el mensaje sale
     * de la línea de quien envía y el seguimiento es de cada asesor).
     */
    public function enviar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prospectos' => 'required|array|min:1|max:500',
            'prospectos.*' => 'integer',
            'programa_id' => 'nullable|integer',
            'text' => 'nullable|string|max:4096',
            'file' => 'nullable|file|max:51200',
        ]);

        if (blank($data['text'] ?? null) && ! $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Escribí un mensaje o adjuntá un archivo.'], 422);
        }

        $client = app(SeguimientoEsamClient::class);
        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();
        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        $rows = DB::table('marketing_prospectos as mp')
            ->leftJoin('programas as p', 'p.id', '=', 'mp.programa_id')
            ->where('mp.user_id', auth()->id())
            ->whereIn('mp.id', $data['prospectos'])
            ->select('mp.*', 'p.nombre as programa_nombre')
            ->get();

        $items = [];
        $sinTelefono = 0;
        $stamp = now()->format('YmdHis');

        foreach ($rows as $p) {
            $chatId = $this->chatId($p->celular);
            if (! $chatId) {
                $sinTelefono++;

                continue;
            }
            $nombre = trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) ?: 'Prospecto';
            $programa = $p->programa_nombre ?: '';
            $body = str_replace(
                ['{{nombre}}', '{nombre}', '{{programa}}', '{programa}'],
                [$nombre, $nombre, $programa, $programa],
                (string) ($data['text'] ?? ''),
            );
            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $nombre,
                'body_override' => $body !== '' ? $body : null,
                'external_ref' => 'prospecto:'.$p->id.':'.$stamp,
            ];
        }

        if ($items === []) {
            $ajenos = count($data['prospectos']) - $rows->count();

            return response()->json([
                'success' => false,
                'message' => $ajenos > 0 && $rows->isEmpty()
                    ? 'Solo podés enviar a tus propios prospectos (el mensaje sale de tu línea).'
                    : 'Ningún prospecto seleccionado tiene un celular válido.',
            ], 422);
        }

        $programas = $rows->pluck('programa_nombre')->filter()->unique()->values();
        $nombreLote = 'Prospectos — '.($programas->count() === 1 ? $programas->first() : count($items).' contactos');

        try {
            $result = $client->sendBatch($nombreLote, $items, null, null, false, $this->mediaPayload($request->file('file')), $account, [
                'area' => 'marketing',
                'tipo' => 'prospectos',
                'programa_id' => $programas->count() === 1 ? ($rows->first()->programa_id ?? null) : null,
                'programa' => $programas->count() === 1 ? $programas->first() : null,
            ]);
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        DB::table('marketing_prospecto_envios')->insert([
            'user_id' => auth()->id(),
            'batch_id' => $result['batch_id'] ?? null,
            'programa_id' => $programas->count() === 1 ? ($rows->first()->programa_id ?? null) : null,
            'total' => count($items),
            'resumen' => mb_substr($nombreLote, 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Los contactados por primera vez pasan de «Nuevo» a «Contactado».
        $nuevos = $rows->where('estado', 'nuevo')->filter(fn ($p) => $this->chatId($p->celular))->pluck('id');
        if ($nuevos->isNotEmpty()) {
            DB::table('marketing_prospectos')->whereIn('id', $nuevos)->update(['estado' => 'contactado', 'updated_at' => now()]);
            foreach ($nuevos as $id) {
                $this->sincronizar((int) $id);
            }
        }

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($items),
            'sin_telefono' => $sinTelefono,
            'contactados' => $nuevos->count(),
        ]);
    }

    /** Progreso de un lote de prospectos (solo de los envíos propios). */
    public function lote(int $batch): JsonResponse
    {
        $propio = DB::table('marketing_prospecto_envios')->where('batch_id', $batch)->where('user_id', auth()->id())->exists();
        abort_unless($propio, 404);

        try {
            $data = app(SeguimientoEsamClient::class)->batchStatus($batch);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        return response()->json(['success' => true] + $data);
    }

    // ── Apoyo ────────────────────────────────────────────────────────────────

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'programa_id' => 'required|integer|exists:programas,id',
            'nombres' => 'required|string|max:150',
            'apellidos' => 'nullable|string|max:150',
            'celular' => ['required', 'string', 'max:40', function ($attr, $value, $fail) {
                if (strlen(preg_replace('/\D+/', '', (string) $value)) < 8) {
                    $fail('El celular debe tener al menos 8 dígitos.');
                }
            }],
            'correo' => 'nullable|email|max:190',
            'nota' => 'nullable|string|max:1000',
            'estado' => 'nullable|in:'.implode(',', array_keys(self::ESTADOS)),
        ], [
            'programa_id.required' => 'Elegí el programa.',
            'nombres.required' => 'Escribí el nombre del prospecto.',
            'celular.required' => 'El celular es necesario para el WhatsApp y el seguimiento.',
        ]);
        $data['estado'] = $data['estado'] ?? 'nuevo';
        $data['nombres'] = trim($data['nombres']);
        $data['celular'] = trim($data['celular']);

        return $data;
    }

    private function exigirInscripciones(int $programaId): void
    {
        $ok = DB::table('programas')->where('id', $programaId)->where('estado_id', self::ESTADO_INSCRIPCIONES)->exists();
        if (! $ok) {
            throw ValidationException::withMessages(['programa_id' => 'Solo se registran prospectos en programas en etapa de Inscripciones.']);
        }
    }

    /** El mismo celular no se registra dos veces en un programa (sea del asesor que sea). */
    private function evitarDuplicado(int $programaId, string $celular, ?int $exceptoId = null): void
    {
        $digits = $this->digitos($celular);
        $previo = DB::table('marketing_prospectos as mp')
            ->leftJoin('users as u', 'u.id', '=', 'mp.user_id')
            ->where('mp.programa_id', $programaId)
            ->when($exceptoId, fn ($q) => $q->where('mp.id', '<>', $exceptoId))
            ->get(['mp.id', 'mp.celular', 'u.fullname', 'u.username'])
            ->first(fn ($p) => $this->digitos($p->celular) === $digits);

        if ($previo) {
            $quien = $previo->fullname ?: $previo->username ?: 'otro asesor';
            throw ValidationException::withMessages(['celular' => "Ese celular ya está registrado como prospecto de este programa (por {$quien})."]);
        }
    }

    /** Prospecto que el usuario puede modificar: el suyo, o cualquiera si supervisa. */
    private function propio(int $id): object
    {
        $p = DB::table('marketing_prospectos')->where('id', $id)
            ->when(! $this->supervisa(), fn ($q) => $q->where('user_id', auth()->id()))
            ->first();
        abort_unless($p, 404);

        return $p;
    }

    private function ref(int $id): string
    {
        return 'mp-'.$id;
    }

    /**
     * Crea/actualiza la nota de seguimiento del prospecto en Seguimiento ESAM
     * (en la línea de su asesor). Si falla, el prospecto queda guardado igual y
     * se muestra el motivo para reintentar.
     *
     * @return array{seguimiento_nota_id: ?int, seguimiento_error: ?string}
     */
    private function sincronizar(int $id): array
    {
        $p = DB::table('marketing_prospectos as mp')
            ->leftJoin('programas as pr', 'pr.id', '=', 'mp.programa_id')
            ->where('mp.id', $id)
            ->first(['mp.*', 'pr.nombre as programa_nombre']);

        if (! $p) {
            return ['seguimiento_nota_id' => null, 'seguimiento_error' => null];
        }

        $client = app(SeguimientoEsamClient::class);
        $notaId = $p->seguimiento_nota_id;
        $error = null;

        if (! $client->enabled()) {
            $error = 'Integración con Seguimiento ESAM apagada.';
        } else {
            try {
                $res = $client->upsertProspect($this->ref($id), [
                    'account' => (string) $p->user_id,
                    'phone' => (string) $p->celular,
                    'name' => trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) ?: null,
                    'programa' => $p->programa_nombre,
                    'programa_id' => $p->programa_id,
                    'estado' => $p->estado,
                    'nota' => $p->nota,
                ]);
                $notaId = $res['note_id'] ?? $notaId;
            } catch (\Throwable $e) {
                $error = mb_substr($e->getMessage(), 0, 255);
            }
        }

        DB::table('marketing_prospectos')->where('id', $id)->update([
            'seguimiento_nota_id' => $notaId,
            'seguimiento_error' => $error,
            'seguimiento_at' => now(),
        ]);

        return ['seguimiento_nota_id' => $notaId ? (int) $notaId : null, 'seguimiento_error' => $error];
    }

    private function digitos(?string $celular): string
    {
        $d = preg_replace('/\D+/', '', (string) $celular);

        return strlen($d) === 8 ? '591'.$d : $d;
    }

    private function chatId(?string $celular): ?string
    {
        $d = $this->digitos($celular);

        return strlen($d) >= 8 ? $d.'@c.us' : null;
    }

    private function mediaPayload(?UploadedFile $file): ?array
    {
        if (! $file) {
            return null;
        }

        return [
            'base64' => base64_encode((string) file_get_contents($file->getRealPath())),
            'mimetype' => $file->getMimeType() ?: 'application/octet-stream',
            'filename' => $file->getClientOriginalName(),
        ];
    }
}
