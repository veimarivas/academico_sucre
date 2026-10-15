<?php

namespace App\Console\Commands;

use App\Models\Configuracion;
use App\Models\User;
use App\Services\Cobranzas\RecordatorioCobranza;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Prepara los recordatorios de cuotas (vencidas y/o próximas) de los días
 * configurados. NO envía nada por sí solo:
 *
 * - Está apagado hasta que se active en Ajustes › WhatsApp.
 * - Sale solo desde la línea de la cuenta elegida (nunca desde otras).
 * - Solo programas «En desarrollo» e «Inscripciones» de la gestión actual.
 * - Los lotes quedan «esperando confirmación» en Seguimiento ESAM: el dueño de la
 *   línea recibe una notificación y decide. Sin confirmación no se envían y se
 *   cancelan solos a las 20 horas.
 */
class WhatsappRecordatorios extends Command
{
    protected $signature = 'whatsapp:recordatorios
        {--modo=ambos : vencidas|proximas|ambos}
        {--gestion= : Filtrar por gestión (por defecto, la actual)}
        {--force : Ignorar los días configurados y el interruptor de envío automático}
        {--dry-run : No crea lotes, solo cuenta}';

    protected $description = 'Prepara recordatorios de cuotas por WhatsApp para confirmar en Seguimiento ESAM';

    public function handle(RecordatorioCobranza $builder, SeguimientoEsamClient $client): int
    {
        if (! $client->enabled()) {
            $this->error('La integración con Seguimiento ESAM no está configurada.');

            return self::FAILURE;
        }

        if (! $this->option('force') && Configuracion::get('whatsapp_recordatorios_auto', '0') !== '1') {
            $this->info('Los recordatorios automáticos están desactivados (Ajustes › WhatsApp).');

            return self::SUCCESS;
        }

        $dias = collect(explode(',', (string) Configuracion::get('whatsapp_dias_envio', '1,5,10,15')))
            ->map(fn ($d) => (int) trim((string) $d))
            ->filter(fn ($d) => $d >= 1 && $d <= 31)
            ->values()
            ->all();

        $hoy = now();

        if (! $this->option('force') && ! in_array((int) $hoy->day, $dias, true)) {
            $this->info("Hoy ({$hoy->day}) no es día de envío. Días configurados: ".implode(', ', $dias));

            return self::SUCCESS;
        }

        // Una sola línea de envío: la de la cuenta elegida en Ajustes (con número autorizado).
        $cuentaId = (int) Configuracion::get('whatsapp_recordatorios_cuenta', '0');
        $cuenta = $cuentaId ? User::find($cuentaId) : null;

        if (! $cuenta || ! $cuenta->whatsapp_autorizado || blank($cuenta->whatsapp_numero)) {
            $this->warn('No hay una cuenta de envío válida para los recordatorios (Ajustes › WhatsApp). No se envía nada.');
            Log::warning('Recordatorios de cobranza: sin cuenta de envío configurada; no se prepararon.');

            return self::SUCCESS;
        }

        $modos = array_values(array_intersect(
            $this->option('modo') === 'ambos' ? ['vencidas', 'proximas'] : [$this->option('modo')],
            ['vencidas', 'proximas'],
        ));

        $gestion = $this->option('gestion') ?: $hoy->year;
        $programas = DB::table('programas')
            ->where('gestion', $gestion)
            ->whereIn('estado_id', [1, 4]) // En desarrollo e Inscripciones
            ->pluck('id');

        $hora = (string) Configuracion::get('whatsapp_recordatorios_hora', '09:00');
        $scheduledAt = $hoy->copy()->setTimeFromTimeString(preg_match('/^\d{2}:\d{2}$/', $hora) ? $hora : '09:00');
        if ($scheduledAt->isPast()) {
            $scheduledAt = $hoy->copy()->addMinutes(30);
        }

        $dryRun = (bool) $this->option('dry-run');
        $grupo = 'cobranza-'.$hoy->format('Ymd-His');
        $totalItems = 0;
        $totalLotes = 0;

        foreach ($programas as $programaId) {
            foreach ($modos as $modo) {
                $data = $builder->construir($programaId, $modo);

                if (empty($data['items'])) {
                    continue;
                }

                $nombre = $builder->nombre($data['programa'], $modo);
                $meta = [
                    'area' => 'contabilidad',
                    'tipo' => 'cobranza',
                    'modo' => $modo,
                    'programa_id' => $data['programa']->id,
                    'programa' => $data['programa']->nombre,
                    'gestion' => $data['programa']->gestion ?? null,
                    'origen' => 'recordatorio_automatico',
                ];

                try {
                    $result = $client->sendBatch($nombre, $data['items'], 'cobranza', null, $dryRun, null, (string) $cuenta->id, $meta, [
                        'require_confirmation' => true,
                        'confirm_group' => $grupo,
                        'scheduled_at' => $scheduledAt->toIso8601String(),
                        'notify' => false,
                    ]);
                } catch (\Throwable $e) {
                    $this->warn("Programa {$programaId} ({$modo}): ".$e->getMessage());

                    continue;
                }

                $totalItems += count($data['items']);
                $totalLotes++;

                if (! $dryRun && ! empty($data['logRows'])) {
                    $now = now();
                    DB::table('mensajes')->insert(array_map(fn ($r) => [
                        'mensaje' => $nombre.' — lote #'.($result['batch_id'] ?? '').' (programado, por confirmar)',
                        'inscripcione_id' => $r['inscripcione_id'] ?? 0,
                        'fecha_registro' => $now->toDateString(),
                        'tipo' => 'whatsapp_cobranza',
                        'persona' => $r['persona'] ?? null,
                    ], $data['logRows']));
                }
            }
        }

        // Una sola notificación por toda la programación.
        if (! $dryRun && $totalLotes > 0) {
            try {
                $client->notifyConfirmGroup($grupo, "Recordatorios de cobranza de {$totalLotes} lote(s), gestión {$gestion}");
            } catch (\Throwable $e) {
                $this->warn('No se pudo enviar la notificación de confirmación: '.$e->getMessage());
            }
        }

        $this->info("Listo. Lotes preparados: {$totalLotes}, destinatarios: {$totalItems}, desde la línea de {$cuenta->name}. "
            .($dryRun ? '(dry-run)' : 'Esperan confirmación en Seguimiento ESAM.'));

        return self::SUCCESS;
    }
}
