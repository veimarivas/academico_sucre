<?php

namespace App\Services\SeguimientoEsam;

use RuntimeException;

/** Error de la API de Seguimiento ESAM con su código HTTP (p. ej. 409 = ya enviado hoy). */
class SeguimientoEsamException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 0, public readonly array $payload = [])
    {
        parent::__construct($message);
    }

    /** Todos los mensajes ya se habían enviado hoy (no se creó lote). */
    public function isDuplicate(): bool
    {
        return $this->status === 409 && ! empty($this->payload['duplicate']);
    }
}
