<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use App\Services\SeguimientoEsam\SenderAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Envío por WhatsApp de un programa: al grupo del programa y a participantes
 * seleccionados, con mensaje personalizado y media (imagen/audio/video/documento).
 */
class WhatsappProgramaController extends Controller
{
    public function index($programaId): View
    {
        $programa = DB::table('programas')->where('id', $programaId)->first();
        abort_unless($programa, 404);

        $participantes = DB::table('inscripciones as i')
            ->join('participantes as p', 'i.participante_id', '=', 'p.id')
            ->where('i.programa_id', $programaId)
            ->select('p.id', 'p.nombres', 'p.apellidos', 'p.celular', 'p.correo')
            ->distinct()
            ->orderBy('p.apellidos')
            ->orderBy('p.nombres')
            ->get();

        $client = app(SeguimientoEsamClient::class);
        $error = $client->enabled() ? null : 'La integración con Seguimiento ESAM no está configurada.';

        return view('admin.programas.whatsapp', compact('programa', 'participantes', 'error'));
    }

    public function guardarGrupo(Request $request, $programaId): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_grupo_id' => 'nullable|string|max:120',
            'whatsapp_grupo_nombre' => 'nullable|string|max:180',
        ]);

        DB::table('programas')->where('id', $programaId)->update([
            'whatsapp_grupo_id' => $validated['whatsapp_grupo_id'] ?: null,
            'whatsapp_grupo_nombre' => $validated['whatsapp_grupo_nombre'] ?: null,
        ]);

        return back()->with('success', 'Grupo del programa guardado.');
    }

    public function resolverGrupo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite' => 'required|string|max:300',
        ]);

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        try {
            $info = $client->resolveGroup($validated['invite']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        if (empty($info['id'])) {
            return response()->json(['success' => false, 'message' => 'No se pudo resolver el grupo desde ese enlace.'], 404);
        }

        return response()->json([
            'success' => true,
            'id' => $info['id'],
            'name' => $info['name'] ?? '',
        ]);
    }

    public function enviarGrupo(Request $request, $programaId): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'nullable|string|max:4096',
            'file' => 'nullable|file|max:51200',
            'dry_run' => 'nullable|boolean',
        ]);

        $programa = DB::table('programas')->where('id', $programaId)->first();

        if (! $programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        if (! $programa->whatsapp_grupo_id) {
            return response()->json(['success' => false, 'message' => 'El programa no tiene grupo asignado.'], 422);
        }

        if (blank($validated['text'] ?? null) && ! $request->hasFile('file')) {
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

        $item = [
            'chat_id' => $programa->whatsapp_grupo_id,
            'contact_name' => $programa->whatsapp_grupo_nombre ?: $programa->nombre,
            'body_override' => $validated['text'] ?? null,
            'external_ref' => 'grupo:'.$programaId.':'.now()->format('YmdHis'),
        ];

        $meta = ['area' => auth()->user()->whatsapp_area ?: 'general', 'tipo' => 'grupo', 'programa_id' => $programa->id, 'programa' => $programa->nombre, 'gestion' => $programa->gestion ?? null];

        try {
            $result = $client->sendBatch(
                'Grupo — '.$programa->nombre,
                [$item],
                null,
                null,
                (bool) ($validated['dry_run'] ?? false),
                $this->mediaPayload($request->file('file')),
                $account,
                $meta,
            );
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        $this->logMensajes($programa, [[
            'inscripcione_id' => 0,
            'persona' => 'Grupo: '.($programa->whatsapp_grupo_nombre ?: $programa->whatsapp_grupo_id),
        ]], 'whatsapp_grupo', $result['batch_id'] ?? null);

        return response()->json(['success' => true, 'batch_id' => $result['batch_id'] ?? null]);
    }

    public function enviarParticipantes(Request $request, $programaId): JsonResponse
    {
        $validated = $request->validate([
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'integer',
            'text' => 'nullable|string|max:4096',
            'file' => 'nullable|file|max:51200',
            'dry_run' => 'nullable|boolean',
        ]);

        $programa = DB::table('programas')->where('id', $programaId)->first();

        if (! $programa) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        if (blank($validated['text'] ?? null) && ! $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Escribí un mensaje o adjuntá un archivo.'], 422);
        }

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        $participantes = DB::table('participantes')->whereIn('id', $validated['participantes'])->get();
        $inscMap = DB::table('inscripciones')->where('programa_id', $programaId)->pluck('id', 'participante_id');

        $items = [];
        $logRows = [];
        $sinTelefono = 0;
        $stamp = now()->format('YmdHis');

        foreach ($participantes as $p) {
            $chatId = $this->chatId($p->celular);

            if (! $chatId) {
                $sinTelefono++;

                continue;
            }

            $nombre = $p->apellidos.', '.$p->nombres;
            $body = str_replace(
                ['{{nombre}}', '{nombre}', '{{programa}}', '{programa}'],
                [$nombre, $nombre, $programa->nombre, $programa->nombre],
                (string) ($validated['text'] ?? ''),
            );

            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $nombre,
                'body_override' => $body !== '' ? $body : null,
                'external_ref' => 'prog'.$programaId.':'.$p->id.':'.$stamp,
            ];

            $logRows[] = [
                'inscripcione_id' => (int) $inscMap->get($p->id, 0),
                'persona' => $nombre,
            ];
        }

        if ($items === []) {
            return response()->json(['success' => false, 'message' => 'Ningún participante con teléfono válido.'], 404);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        $meta = ['area' => auth()->user()->whatsapp_area ?: 'general', 'tipo' => 'participantes', 'programa_id' => $programa->id, 'programa' => $programa->nombre, 'gestion' => $programa->gestion ?? null];

        try {
            $result = $client->sendBatch(
                'Participantes — '.$programa->nombre,
                $items,
                null,
                null,
                (bool) ($validated['dry_run'] ?? false),
                $this->mediaPayload($request->file('file')),
                $account,
                $meta,
            );
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        $this->logMensajes($programa, $logRows, 'whatsapp_programa', $result['batch_id'] ?? null);

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($items),
            'sin_telefono' => $sinTelefono,
        ]);
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

    private function chatId(?string $celular): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $celular);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 8) {
            $digits = '591'.$digits;
        }

        if (strlen($digits) < 8) {
            return null;
        }

        return $digits.'@c.us';
    }

    private function logMensajes($programa, array $rows, string $tipo, ?int $batchId): void
    {
        if ($rows === []) {
            return;
        }

        $now = now();

        DB::table('mensajes')->insert(array_map(fn ($r) => [
            'mensaje' => 'WhatsApp '.$tipo.' — '.$programa->nombre.' — lote #'.$batchId,
            'inscripcione_id' => $r['inscripcione_id'] ?? 0,
            'fecha_registro' => $now->toDateString(),
            'tipo' => $tipo,
            'persona' => $r['persona'] ?? null,
        ], $rows));
    }
}
