@php
    /**
     * Variables esperadas:
     *  $h           array horario (con flags pasado/hoy/atrasado/confirmado_pasado/confirmado_futuro/falta_registro/tiene_*)
     *  $fechaCorta  callable
     *  $weekday     callable
     *  $weekdayHoy  callable
     *  $estadoColor callable
     *  $enRango     bool  (opcional, sólo concluidos)
     *  $labelMes    string (opcional, sólo concluidos)
     */
    [$dia, $mesYear] = $fechaCorta($h['fecha_desarrollo']);
    $color = $estadoColor($h['estado']);
    $asignadoColor = $h['asignado_color'] ?? '#878a99';
    $enRango = $enRango ?? false;
    $labelMes = $labelMes ?? '';

    // Clase de estado (prioridad: atrasado > confirm-late > missing > confirm-future > en-rango > hoy > pasado)
    if (!empty($h['atrasado'])) {
        $stateClass = 'row-atrasado';
    } elseif (!empty($h['confirmado_pasado'])) {
        $stateClass = 'row-confirm-late';
    } elseif (!empty($h['falta_registro'])) {
        $stateClass = 'row-missing';
    } elseif (!empty($h['confirmado_futuro'])) {
        $stateClass = 'row-confirm-future';
    } elseif ($enRango) {
        $stateClass = 'row-en-rango';
    } elseif (!empty($h['hoy'])) {
        $stateClass = 'row-hoy';
    } elseif (!empty($h['pasado'])) {
        $stateClass = 'row-pasado';
    } else {
        $stateClass = '';
    }
@endphp
<tr class="seg-row {{ $stateClass }}" data-horario-id="{{ $h['id'] }}" title="Click para ver detalle del horario">
    <td>
        <div class="horario-fecha">
            <span class="dia">{{ $dia }}</span>
            <span class="mes">{{ $mesYear }}</span>
            <span class="weekday-badge {{ $weekdayHoy($h['fecha_desarrollo']) ? 'weekday-hoy' : '' }}">{{ $weekday($h['fecha_desarrollo']) }}</span>
        </div>
    </td>
    <td class="text-center">
        <span class="horario-hora">{{ substr($h['hora_inicio'] ?? '', 0, 5) }}—{{ substr($h['hora_fin'] ?? '', 0, 5) }}</span>
        @if(!empty($h['hoy']))<span class="today-pulse">Hoy</span>@endif
    </td>
    <td>
        <span class="chip" style="color: {{ $color }};">{{ $h['estado'] }}</span>
        @if(!empty($h['atrasado']))
            <span class="seg-flag f-danger"><i class="ri-alarm-warning-line"></i> Atrasado</span>
        @elseif(!empty($h['confirmado_pasado']))
            <span class="seg-flag f-warn"><i class="ri-time-line"></i> Sin actualizar</span>
        @elseif(!empty($h['falta_registro']))
            <span class="seg-flag f-info"><i class="ri-information-line"></i> Falta asistencia</span>
        @elseif(trim((string)($h['estado'] ?? '')) === 'Desarrollado' && !empty($h['tiene_asistencias']))
            <span class="seg-flag f-success"><i class="ri-check-line"></i>
                @if(!empty($h['tiene_observaciones'])) Con asistencia y obs. @else Con asistencia @endif
            </span>
        @elseif(!empty($h['confirmado_futuro']))
            <span class="seg-flag f-success"><i class="ri-check-line"></i> Confirmado</span>
        @elseif($enRango && $labelMes)
            <span class="chip chip-culminado mt-1" style="font-size:.62rem; padding:.18em .55em;"><i class="ri-calendar-check-line"></i> {{ $labelMes }}</span>
        @elseif(!empty($h['pasado']))
            <span class="chip chip-vencido mt-1" style="font-size:.62rem; padding:.18em .55em;"><i class="ri-time-line"></i> Pasada</span>
        @endif
    </td>
    <td>
        @if(!empty($h['asignado_nombres']))
            <span class="asignado-pill" style="background: {{ $asignadoColor }};">
                <i class="ri-user-line"></i>
                {{ $h['asignado_apellidos'] }}, {{ $h['asignado_nombres'] }}
            </span>
        @else
            <span class="text-muted fst-italic" style="font-size:.72rem;">—</span>
        @endif
    </td>
    <td>
        @if(!empty($h['observaciones']))
            <div class="obs-cell" title="{{ $h['observaciones'] }}">{{ $h['observaciones'] }}</div>
        @else
            <span class="text-muted" style="font-size:.7rem;">—</span>
        @endif
    </td>
</tr>
