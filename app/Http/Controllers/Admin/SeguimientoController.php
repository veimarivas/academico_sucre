<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $hoy = date('Y-m-d');
        $gestionFiltro = $request->get('gestion');

        $gestiones = DB::table('programas')
            ->select('gestion')->distinct()
            ->orderByDesc('gestion')->pluck('gestion');

        // ============== Módulos En Desarrollo ==============
        $enDesarrollo = $this->cargarModulos('En Desarrollo', $gestionFiltro);

        // ============== Módulos No Inicio con horarios pasados ==============
        // Sólo módulos cuyos horarios (al menos uno) tienen fecha < hoy
        $noInicioBase = $this->cargarModulos('No Inicio', $gestionFiltro);
        $noInicioVencidos = [];
        foreach ($noInicioBase as $programa) {
            $modulosVencidos = [];
            foreach ($programa['modulos'] as $mod) {
                $tienePasada = false;
                foreach ($mod['horarios'] as $h) {
                    if ($h['fecha_desarrollo'] && $h['fecha_desarrollo'] < $hoy) {
                        $tienePasada = true;
                        break;
                    }
                }
                if ($tienePasada) {
                    $modulosVencidos[] = $mod;
                }
            }
            if (!empty($modulosVencidos)) {
                $programa['modulos'] = $modulosVencidos;
                $noInicioVencidos[] = $programa;
            }
        }

        // ============== Módulos Concluidos: mes actual y mes anterior (por separado) ==============
        $mesActualInicio = date('Y-m-01');
        $mesActualFin = date('Y-m-t');
        $mesAnteriorInicio = date('Y-m-01', strtotime('first day of last month'));
        $mesAnteriorFin = date('Y-m-t', strtotime('last day of last month'));

        $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $mesActualLabel = ucfirst($mesesEs[(int) date('n') - 1]) . ' del ' . date('Y');
        $mesAnteriorLabel = ucfirst($mesesEs[(int) date('n', strtotime('first day of last month')) - 1]) . ' del ' . date('Y', strtotime('first day of last month'));

        $concluidosBase = $this->cargarModulos('Concluido', $gestionFiltro);

        $filtrarPorRango = function ($base, $desde, $hasta) {
            $out = [];
            foreach ($base as $programa) {
                $mods = [];
                foreach ($programa['modulos'] as $mod) {
                    $countEnRango = 0;
                    foreach ($mod['horarios'] as $h) {
                        if ($h['fecha_desarrollo']
                            && $h['fecha_desarrollo'] >= $desde
                            && $h['fecha_desarrollo'] <= $hasta) {
                            $countEnRango++;
                        }
                    }
                    if ($countEnRango > 0) {
                        // Mantener todos los horarios, solo agregar metadata
                        $mod['horarios_en_rango'] = $countEnRango;
                        $mod['rango_desde'] = $desde;
                        $mod['rango_hasta'] = $hasta;
                        $mods[] = $mod;
                    }
                }
                if (!empty($mods)) {
                    $programa['modulos'] = $mods;
                    $out[] = $programa;
                }
            }
            return $out;
        };

        $concluidosActual = $filtrarPorRango($concluidosBase, $mesActualInicio, $mesActualFin);
        $concluidosAnterior = $filtrarPorRango($concluidosBase, $mesAnteriorInicio, $mesAnteriorFin);

        // Resumen
        $resumen = [
            'en_desarrollo_progs' => count($enDesarrollo),
            'en_desarrollo_mods' => array_sum(array_map(fn($p) => count($p['modulos']), $enDesarrollo)),
            'no_inicio_progs' => count($noInicioVencidos),
            'no_inicio_mods' => array_sum(array_map(fn($p) => count($p['modulos']), $noInicioVencidos)),
            'concluidos_actual_progs' => count($concluidosActual),
            'concluidos_actual_mods' => array_sum(array_map(fn($p) => count($p['modulos']), $concluidosActual)),
            'concluidos_anterior_progs' => count($concluidosAnterior),
            'concluidos_anterior_mods' => array_sum(array_map(fn($p) => count($p['modulos']), $concluidosAnterior)),
        ];

        return view('admin.seguimiento.index', compact(
            'enDesarrollo', 'noInicioVencidos',
            'concluidosActual', 'concluidosAnterior',
            'mesActualLabel', 'mesAnteriorLabel',
            'mesActualInicio', 'mesActualFin', 'mesAnteriorInicio', 'mesAnteriorFin',
            'gestiones', 'gestionFiltro', 'resumen', 'hoy'
        ));
    }

    /**
     * Carga programas → módulos → horarios para un estado dado.
     */
    private function cargarModulos($desarrollo, $gestionFiltro = null)
    {
        $hoy = date('Y-m-d');

        $q = DB::table('modulos')
            ->join('programas', 'modulos.programa_id', '=', 'programas.id')
            ->leftJoin('docentes', 'modulos.docente_id', '=', 'docentes.id')
            ->leftJoin(DB::raw('(SELECT modulo_id, MIN(fecha_desarrollo) as fecha_inicio, MAX(fecha_desarrollo) as fecha_fin FROM horarios GROUP BY modulo_id) as hg'), 'modulos.id', '=', 'hg.modulo_id')
            ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->where('modulos.desarrollo', $desarrollo)
            ->select(
                'modulos.id as modulo_id',
                'modulos.nombre as modulo_nombre',
                'modulos.desarrollo as modulo_estado',
                'modulos.pago_docente',
                'modulos.moodle_link',
                'hg.fecha_inicio',
                'hg.fecha_fin',
                'programas.id as programa_id',
                'programas.nombre as programa_nombre',
                'programas.codigo as programa_codigo',
                'programas.gestion as programa_gestion',
                'tipos.nombre as tipo_nombre',
                'docentes.id as docente_id',
                'docentes.ci as docente_ci',
                'docentes.nombres as docente_nombres',
                'docentes.apellidos as docente_apellidos'
            );

        if ($gestionFiltro) {
            $q->where('programas.gestion', $gestionFiltro);
        }

        $modulos = $q->orderBy('programas.nombre')->orderBy('modulos.id')->get();
        if ($modulos->isEmpty()) return [];

        $moduloIds = $modulos->pluck('modulo_id')->toArray();

        // Cargar horarios
        $horariosColl = DB::table('horarios')
            ->leftJoin('asignados', 'horarios.asignado_id', '=', 'asignados.id')
            ->whereIn('horarios.modulo_id', $moduloIds)
            ->select(
                'horarios.id',
                'horarios.modulo_id',
                'horarios.fecha_desarrollo',
                'horarios.hora_inicio',
                'horarios.hora_fin',
                'horarios.estado',
                'horarios.observaciones',
                'asignados.id as asignado_id',
                'asignados.nombres as asignado_nombres',
                'asignados.apellidos as asignado_apellidos',
                'asignados.hexadecimal as asignado_color'
            )
            ->orderBy('horarios.fecha_desarrollo')
            ->orderBy('horarios.hora_inicio')
            ->get();

        // Cargar asistencias para horarios Desarrollado
        $desarrolladosIds = $horariosColl
            ->filter(fn ($h) => trim((string) $h->estado) === 'Desarrollado')
            ->pluck('id')->all();

        $asistenciasMap = collect();
        if (!empty($desarrolladosIds)) {
            $asistenciasMap = DB::table('asistencias')
                ->join('participantes', 'asistencias.participante_id', '=', 'participantes.id')
                ->whereIn('asistencias.horario_id', $desarrolladosIds)
                ->select(
                    'asistencias.horario_id',
                    'asistencias.asistio',
                    'asistencias.observacion',
                    'participantes.ci',
                    'participantes.nombres',
                    'participantes.apellidos'
                )
                ->orderBy('participantes.apellidos')
                ->orderBy('participantes.nombres')
                ->get()
                ->groupBy('horario_id');
        }

        $horarios = $horariosColl->groupBy('modulo_id');

        // Agrupar por programa
        $programasMap = [];
        foreach ($modulos as $m) {
            $pid = $m->programa_id;
            if (!isset($programasMap[$pid])) {
                $programasMap[$pid] = [
                    'programa_id' => $pid,
                    'programa_nombre' => $m->programa_nombre,
                    'programa_codigo' => $m->programa_codigo,
                    'programa_gestion' => $m->programa_gestion,
                    'tipo_nombre' => $m->tipo_nombre,
                    'modulos' => [],
                ];
            }

            $modHorarios = ($horarios->get($m->modulo_id, collect()))->map(function ($h) use ($hoy, $asistenciasMap) {
                $arr = (array) $h;
                $est = trim((string) $h->estado);
                $arr['pasado']             = $h->fecha_desarrollo && $h->fecha_desarrollo < $hoy;
                $arr['hoy']                = $h->fecha_desarrollo === $hoy;
                $arr['atrasado']           = $arr['pasado'] && $est === 'No Inicio';
                $arr['confirmado_pasado']  = $arr['pasado'] && $est === 'Confirmado';
                $arr['confirmado_futuro']  = !$arr['pasado'] && $est === 'Confirmado';
                $arr['tiene_observaciones'] = !empty(trim((string) ($h->observaciones ?? '')));
                $asist = $asistenciasMap->get($h->id, collect());
                $arr['asistencias']        = $asist->values()->toArray();
                $arr['tiene_asistencias']  = $asist->count() > 0;
                // Sólo la asistencia se considera obligatoria; las observaciones son opcionales
                $arr['falta_registro']     = $est === 'Desarrollado' && !$arr['tiene_asistencias'];
                return $arr;
            })->values()->toArray();

            // ¿Todas las sesiones ya pasaron?
            $totalHor = count($modHorarios);
            $sesionesPasadas = $totalHor > 0 && count(array_filter($modHorarios, fn($h) => $h['pasado'])) === $totalHor;

            $enCurso = $m->fecha_inicio && $m->fecha_fin && $hoy >= $m->fecha_inicio && $hoy <= $m->fecha_fin;

            $programasMap[$pid]['modulos'][] = [
                'id' => $m->modulo_id,
                'nombre' => $m->modulo_nombre,
                'estado' => $m->modulo_estado,
                'pago_docente' => $m->pago_docente,
                'moodle_link' => $m->moodle_link,
                'fecha_inicio' => $m->fecha_inicio,
                'fecha_fin' => $m->fecha_fin,
                'en_curso' => $enCurso,
                'reciente' => false,
                'proximo' => false,
                'docente_id' => $m->docente_id,
                'docente_nombres' => $m->docente_nombres,
                'docente_apellidos' => $m->docente_apellidos,
                'docente_ci' => $m->docente_ci,
                'horarios' => $modHorarios,
                'total_horarios' => $totalHor,
                'sesiones_culminadas' => $sesionesPasadas,
            ];
        }

        // Per-program fallback: reciente and proximo
        $semanaAtras = date('Y-m-d', strtotime($hoy . ' -14 days'));
        $semanaAdelante = date('Y-m-d', strtotime($hoy . ' +14 days'));

        foreach ($programasMap as &$prog) {
            $mods = &$prog['modulos'];
            $tieneEnCurso = count(array_filter($mods, fn($x) => $x['en_curso'])) > 0;

            if (!$tieneEnCurso) {
                $ultimoIdx = null;
                $ultimaFecha = null;
                foreach ($mods as $i => $mod) {
                    if ($mod['fecha_fin'] && $mod['fecha_fin'] >= $semanaAtras && $mod['fecha_fin'] <= $hoy) {
                        if ($ultimoIdx === null || $mod['fecha_fin'] > $ultimaFecha) {
                            $ultimaFecha = $mod['fecha_fin'];
                            $ultimoIdx = $i;
                        }
                    }
                }
                if ($ultimoIdx !== null) {
                    $mods[$ultimoIdx]['reciente'] = true;
                }
            }

            $highlightedFechaFin = null;
            foreach ($mods as $mod) {
                if (($mod['en_curso'] || $mod['reciente']) && $mod['fecha_fin']) {
                    $highlightedFechaFin = $mod['fecha_fin'];
                    break;
                }
            }
            if ($highlightedFechaFin) {
                $proximoIdx = null;
                $proximaFecha = null;
                foreach ($mods as $i => $mod) {
                    if ($mod['fecha_inicio'] && $mod['fecha_inicio'] > $highlightedFechaFin && $mod['fecha_inicio'] <= $semanaAdelante) {
                        if ($proximoIdx === null || $mod['fecha_inicio'] < $proximaFecha) {
                            $proximaFecha = $mod['fecha_inicio'];
                            $proximoIdx = $i;
                        }
                    }
                }
                if ($proximoIdx !== null) {
                    $mods[$proximoIdx]['proximo'] = true;
                }
            }
        }
        unset($prog, $mods);

        return array_values($programasMap);
    }
}
