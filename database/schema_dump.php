<?php

use Illuminate\Support\Facades\DB;

echo "=== TIPOS ===\n";
foreach (DB::table('tipos')->get() as $t) {
    echo "{$t->id} | {$t->nombre}\n";
}

echo "\n=== MODULOS: desarrollo values ===\n";
foreach (DB::table('modulos')->selectRaw('desarrollo, COUNT(*) as c')->groupBy('desarrollo')->get() as $r) {
    echo "{$r->desarrollo} | {$r->c}\n";
}

echo "\n=== programas con tipo_id (muestra) ===\n";
$programas = DB::table('programas')->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
    ->select('programas.id', 'programas.nombre', 'tipos.nombre as tipo_nombre', 'programas.estado_id')
    ->orderByDesc('programas.id')->limit(15)->get();
foreach ($programas as $p) {
    echo "{$p->id} | {$p->tipo_nombre} | {$p->nombre} | estado_id={$p->estado_id}\n";
}
