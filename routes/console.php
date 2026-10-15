<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorios de cuotas por WhatsApp: solo si están activados en Ajustes →
// WhatsApp y en los días configurados. NO envía: prepara los lotes desde la
// línea elegida y quedan esperando confirmación en Seguimiento ESAM.
Schedule::command('whatsapp:recordatorios')
    ->dailyAt('07:30')
    ->timezone('America/La_Paz')
    ->withoutOverlapping();
