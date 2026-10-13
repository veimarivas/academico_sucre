<?php

namespace App\Console\Commands;

use App\Models\Configuracion;
use App\Services\Cobranzas\RecordatorioCobranza;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Envía recordatorios de cuotas (vencidas y/o próximas) de todos los programas
 * con cuotas impagas. Corre a diario y respeta los días configurados.
 */
class WhatsappRecordatorios extends Command
{
    protected $signature = 'whatsapp:recordatorios
        {--modo=ambos : vencidas|proximas|ambos}
        {--gestion= : Filtrar por gestión}
        {--force : Ignorar los días configurados}
        {--dry-run : No envía, solo cuenta}';

    protected $description = 'Envía recordatorios de cuotas por WhatsApp (vencidas y/o próximas)';

    public function handle(RecordatorioCobranza $builder, SeguimientoEsamClient $client): int
    {
        if (! $client->enabled()) {
            $this->error('La integración con Seguimiento ESAM no está configurada.');

            return self::FAILURE;
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

        $modos = array_values(array_intersect(
            $this->option('modo') === 'ambos' ? ['vencidas', 'proximas'] : [$this->option('modo')],
            ['vencidas', 'proximas'],
        ));

        $programas = DB::table('programas')
            ->when($this->option('gestion'), fn ($q, $g) => $q->where('gestion', $g))
            ->where('estado_id', '!=', 3)
            ->pluck('id');

        $dryRun = (bool) $this->option('dry-run');
        $totalItems = 0;
        $totalProgramas = 0;

        foreach ($programas as $programaId) {
            foreach ($modos as $modo) {
                $data = $builder->construir($programaId, $modo);

                if (empty($data['items'])) {
                    continue;
                }

                $nombre = $builder->nombre($data['programa'], $modo);

                try {
                    $result = $client->sendBatch($nombre, $data['items'], 'cobranza', null, $dryRun);
                } catch (\Throwable $e) {
                    $this->warn("Programa {$programaId} ({$modo}): ".$e->getMessage());

                    continue;
                }

                $totalItems += count($data['items']);
                $totalProgramas++;

                if (! $dryRun && ! empty($data['logRows'])) {
                    $now = now();
                    DB::table('mensajes')->insert(array_map(fn ($r) => [
                        'mensaje' => $nombre.' — lote #'.($result['batch_id'] ?? '').' (programado)',
                        'inscripcione_id' => $r['inscripcione_id'] ?? 0,
                        'fecha_registro' => $now->toDateString(),
                        'tipo' => 'whatsapp_cobranza',
                        'persona' => $r['persona'] ?? null,
                    ], $data['logRows']));
                }
            }
        }

        $this->info('Listo. Programas: '.$totalProgramas.', destinatarios: '.$totalItems.($dryRun ? ' (dry-run)' : ''));

        return self::SUCCESS;
    }
}
