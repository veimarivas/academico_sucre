<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorios de cuotas por WhatsApp: corre a diario y respeta los días
// configurados en Ajustes → WhatsApp (por defecto 1, 5, 10 y 15).
Schedule::command('whatsapp:recordatorios')
    ->dailyAt('08:00')
    ->timezone('America/La_Paz')
    ->withoutOverlapping();
