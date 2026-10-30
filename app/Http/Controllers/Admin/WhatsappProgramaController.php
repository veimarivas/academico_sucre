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
            ->select('p.id', 'p.nombres', 'p.apellidos', 'p.celular', 'p.correo',
                'i.estado', 'i.baja_operativa', 'i.baja_academica', 'i.baja_contable')
            ->orderBy('p.apellidos')
            ->orderBy('p.nombres')
            ->get()
            ->unique('id')
            ->values()
            // Dados de baja: se resaltan en la lista para no incluirlos sin querer.
            ->each(function ($p) {
                $p->baja = \App\Support\BajaInscripcion::motivo($p);
            });

        $client = app(SeguimientoEsamClient::class);
        $error = $client->enabled() ? null : 'La integración con Seguimiento ESAM no está configurada.';

        return view('admin.programas.whatsapp', compact('programa', 'participantes', 'error'));
    }

    /** Estados de programa que se pueden elegir al enviar a varios grupos. */
    private const ESTADOS_GRUPOS = [1 => 'En desarrollo', 4 => 'Inscripciones', 3 => 'No ejecutado', 2 => 'Concluido'];

    /**
     * Envío a varios grupos de WhatsApp: programas por estado (por defecto, En
     * desarrollo), con su grupo o la opción de registrarlo ahí mismo.
     */
    public function grupos(): View
    {
        $user = auth()->user();

        $programas = DB::table('programas as p')
            ->leftJoin('tipos as t', 'p.tipo_id', '=', 't.id')
            ->whereIn('p.estado_id', array_keys(self::ESTADOS_GRUPOS))
            ->when(! $user->hasRole('Super Admin'), function ($q) use ($user) {
                // Responsables: solo sus programas.
                $q->whereIn('p.id', DB::table('programas_responsables')
                    ->where('responsable_id', $user->responsable_id ?: 0)->select('programa_id'));
            })
            ->select('p.id', 'p.codigo', 'p.nombre', 'p.gestion', 'p.estado_id', 'p.whatsapp_grupo_id', 'p.whatsapp_grupo_nombre',
                't.nombre as tipo_nombre',
                DB::raw('(SELECT COUNT(*) FROM inscripciones i WHERE i.programa_id = p.id AND i.estado = 1
                    AND COALESCE(i.baja_operativa,0) = 0 AND COALESCE(i.baja_academica,0) = 0 AND COALESCE(i.baja_contable,0) = 0) as vigentes'))
            ->orderByDesc('p.gestion')
            ->orderBy('p.nombre')
            ->get();

        $estados = self::ESTADOS_GRUPOS;
        $error = app(SeguimientoEsamClient::class)->enabled() ? null : 'La integración con Seguimiento ESAM no está configurada.';
        $puedeGestionarGrupo = $user->can('whatsapp.admin');

        // QR de pago vigentes para adjuntar en el mensaje a los grupos.
        $qrs = \App\Support\PagosQr::vigentes();

        return view('admin.programas.whatsapp-grupos', compact('programas', 'estados', 'error', 'puedeGestionarGrupo', 'qrs'));
    }

    /** Envía el mismo mensaje (y media) a los grupos de los programas elegidos, en un solo lote. */
    public function enviarGrupos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programas' => 'required|array|min:1',
            'programas.*' => 'integer',
            'text' => 'nullable|string|max:4096',
            'file' => 'nullable|file|max:51200',
            'qr_id' => 'nullable|integer',
            'qr_datos' => 'nullable|boolean',
        ]);

        // QR de pago (solo vigente). Un mensaje lleva un solo adjunto.
        $qr = \App\Support\PagosQr::elegido($validated['qr_id'] ?? null);
        if ($qr && $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Elegí el QR o un archivo: cada mensaje lleva un solo adjunto.'], 422);
        }

        if (blank($validated['text'] ?? null) && ! $request->hasFile('file') && ! $qr) {
            return response()->json(['success' => false, 'message' => 'Escribí un mensaje o adjuntá un archivo.'], 422);
        }

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        $user = auth()->user();
        $programas = DB::table('programas')->whereIn('id', $validated['programas'])->get()
            ->filter(fn ($p) => $user->puedeWhatsappPrograma((int) $p->id));
        $conGrupo = $programas->filter(fn ($p) => filled($p->whatsapp_grupo_id));

        if ($conGrupo->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Ninguno de los programas elegidos tiene grupo registrado.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        $stamp = now()->format('YmdHis');
        $cuenta = $qr && $request->boolean('qr_datos') ? \App\Support\PagosQr::textoCuenta($qr) : '';
        $items = $conGrupo->map(function ($p) use ($validated, $stamp, $cuenta) {
            $body = str_replace(['{{programa}}', '{programa}'], [$p->nombre, $p->nombre], (string) ($validated['text'] ?? ''));
            if ($cuenta !== '') {
                $body = trim($body."\n\n".$cuenta);
            }

            return [
                'chat_id' => $p->whatsapp_grupo_id,
                'contact_name' => $p->whatsapp_grupo_nombre ?: $p->nombre,
                'body_override' => $body !== '' ? $body : null,
                'external_ref' => 'grupo:'.$p->id.':'.$stamp,
            ];
        })->values()->all();

        $meta = ['area' => $user->whatsapp_area ?: 'general', 'tipo' => 'grupos', 'programas' => $conGrupo->pluck('id')->values()->all()];
        if ($qr) {
            $meta['qr_id'] = $qr->id;
            $meta['qr'] = $qr->nombre.' · '.$qr->banco;
        }

        try {
            $result = $client->sendBatch(
                'Grupos — '.count($items).' programa(s)',
                $items,
                null,
                null,
                false,
                $qr ? \App\Support\PagosQr::adjunto($qr) : $this->mediaPayload($request->file('file')),
                $account,
                $meta,
            );
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        $now = now();
        DB::table('mensajes')->insert($conGrupo->map(fn ($p) => [
            'mensaje' => 'WhatsApp whatsapp_grupo — '.$p->nombre.' — lote #'.($result['batch_id'] ?? ''),
            'inscripcione_id' => 0,
            'fecha_registro' => $now->toDateString(),
            'tipo' => 'whatsapp_grupo',
            'persona' => 'Grupo: '.($p->whatsapp_grupo_nombre ?: $p->whatsapp_grupo_id),
        ])->values()->all());

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($items),
            'sin_grupo' => $programas->count() - $conGrupo->count(),
        ]);
    }

    public function guardarGrupo(Request $request, $programaId): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'whatsapp_grupo_id' => ['nullable', 'string', 'max:120', 'regex:/@g\.us$/'],
            'whatsapp_grupo_nombre' => 'nullable|string|max:180',
        ], ['whatsapp_grupo_id.regex' => 'El ID del grupo debe terminar en @g.us.']);

        DB::table('programas')->where('id', $programaId)->update([
            'whatsapp_grupo_id' => $validated['whatsapp_grupo_id'] ?: null,
            'whatsapp_grupo_nombre' => $validated['whatsapp_grupo_nombre'] ?: null,
        ]);

        // Desde «Enviar a varios grupos» se registra sin recargar la página.
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => $validated['whatsapp_grupo_id'] ?: null,
                'name' => $validated['whatsapp_grupo_nombre'] ?: null,
            ]);
        }

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
            return $this->errorEsam($e);
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
            return $this->errorEsam($e);
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
            return $this->errorEsam($e);
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
