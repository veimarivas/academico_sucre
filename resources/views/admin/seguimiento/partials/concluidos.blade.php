@php
    $estadoColor = $estadoColor ?? function ($e) {
        return [
            'Confirmado' => '#0ab39c',
            'Programado' => '#299cdb',
            'Desarrollado' => '#405189',
            'Postergado' => '#f7b84b',
        ][$e] ?? '#878a99';
    };
    [$dIni, $myIni] = $desdeFmt;
    [$dFin, $myFin] = $hastaFmt;
@endphp

<div class="alert alert-soft-success border-0 d-flex align-items-center gap-2 mb-3" role="alert" style="font-size:.82rem;">
    <i class="ri-calendar-check-line fs-5"></i>
    <div>
        Mostrando módulos concluidos del mes de <strong>{{ $labelMes }}</strong>
        (sesiones entre el <strong>{{ $dIni }} de {{ $myIni }}</strong> y el <strong>{{ $dFin }} de {{ $myFin }}</strong>).
    </div>
</div>

@if(count($programas) === 0)
    <div class="empty-state">
        <div class="empty-icon"><i class="ri-award-line"></i></div>
        <h5 class="text-dark fw-bold mb-1">Sin módulos concluidos en {{ $labelMes }}</h5>
        <p class="text-muted mb-0">No hay módulos en estado "Concluido" con sesiones en ese mes.</p>
    </div>
@else
    @foreach($programas as $prog)
    <div class="programa-block">
        <div class="programa-header programa-header--success">
            <div class="d-flex align-items-center gap-2" style="min-width:0;">
                <span class="programa-icon" style="background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%); box-shadow: 0 4px 10px -4px rgba(10,179,156,.55);">
                    <i class="ri-checkbox-circle-line"></i>
                </span>
                <div style="min-width:0;">
                    <h6 class="programa-title text-truncate" title="{{ $prog['programa_nombre'] }}">
                        <a href="{{ route('admin.programas.show', $prog['programa_id']) }}" class="programa-title-link" onclick="event.stopPropagation();">{{ $prog['programa_nombre'] }}</a>
                    </h6>
                    <div class="programa-meta">
                        <span><i class="ri-hashtag"></i>{{ $prog['programa_codigo'] }}</span>
                        @if($prog['tipo_nombre'])<span>·</span><span><i class="ri-price-tag-3-line"></i>{{ $prog['tipo_nombre'] }}</span>@endif
                        <span>·</span><span><i class="ri-calendar-line"></i>Gestión {{ $prog['programa_gestion'] }}</span>
                    </div>
                </div>
            </div>
            <span class="programa-count" style="background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%); box-shadow: 0 4px 10px -4px rgba(10,179,156,.5);">
                <i class="ri-award-line"></i>{{ count($prog['modulos']) }} módulo{{ count($prog['modulos']) > 1 ? 's' : '' }} concluido{{ count($prog['modulos']) > 1 ? 's' : '' }}
            </span>
        </div>

        @foreach($prog['modulos'] as $idx => $mod)
        @php
            $iniDoc = $mod['docente_nombres'] ? strtoupper(substr($mod['docente_nombres'], 0, 1) . substr($mod['docente_apellidos'], 0, 1)) : '?';
            $totalAll = count($mod['horarios']);
            $totalEnRango = $mod['horarios_en_rango'] ?? 0;
            $rangoDesde = $mod['rango_desde'] ?? null;
            $rangoHasta = $mod['rango_hasta'] ?? null;
            $primerPrograma = $loop->parent->first;
            $modHeaderClass = '';
            $pillText = '';
            $pillClass = '';
            if (!empty($mod['en_curso'])) {
                $modHeaderClass = 'modulo-card-header--curso';
                $pillText = 'En curso';
                $pillClass = 'modulo-highlight-chip--active';
            } elseif (!empty($mod['reciente'])) {
                $modHeaderClass = 'modulo-card-header--reciente';
                $pillText = 'Recientemente en curso';
                $pillClass = 'modulo-highlight-chip--active';
            } elseif (!empty($mod['proximo'])) {
                $modHeaderClass = 'modulo-card-header--proximo';
                $pillText = 'Próximo';
                $pillClass = 'modulo-highlight-chip--proximo';
            }
        @endphp
        <div class="modulo-card">
            <div class="modulo-card-header modulo-collapse-toggle {{ $modHeaderClass }}" data-collapse-target="#mod-collapse-c-{{ $prog['programa_id'] }}-{{ $mod['id'] }}" aria-expanded="{{ $primerPrograma ? 'true' : 'false' }}" role="button">
                <div class="modulo-title-block">
                    <span class="modulo-num-badge" style="background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);">{{ $idx + 1 }}</span>
                    <div style="min-width:0;">
                        <div class="modulo-nombre">
                            <a href="{{ route('admin.programas.modulo-detalle', $mod['id']) }}" class="modulo-nombre-link">{{ $mod['nombre'] }}</a>
                            @if(!empty($mod['moodle_link']))
                                <a href="{{ $mod['moodle_link'] }}" target="_blank" rel="noopener" class="modulo-moodle-link" title="Abrir en Moodle"><i class="ri-external-link-line"></i></a>
                            @endif
                        </div>
                        <div class="modulo-docente">
                            @if($mod['docente_nombres'])
                                <span class="docente-avatar-mini">{{ $iniDoc }}</span>
                                <span><i class="ri-user-star-line"></i> {{ $mod['docente_apellidos'] }}, {{ $mod['docente_nombres'] }}</span>
                            @else
                                <span class="fst-italic"><i class="ri-user-unfollow-line"></i> Sin docente asignado</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="modulo-chips">
                        <span class="chip chip-culminado"><i class="ri-checkbox-circle-line"></i> Concluido</span>
                        <span class="chip chip-no-inicio">{{ $totalAll }} sesion{{ $totalAll != 1 ? 'es' : '' }} totales</span>
                        @if($pillText)
                            <span class="modulo-highlight-chip {{ $pillClass }}">{{ $pillText }}</span>
                        @endif
                        @if($totalEnRango > 0)
                            <span class="chip chip-warning"><i class="ri-calendar-check-line"></i> {{ $totalEnRango }} en {{ $labelMes }}</span>
                        @endif
                    </div>
                    <span class="collapse-arrow"><i class="ri-arrow-down-s-line"></i></span>
                </div>
            </div>

            <div class="modulo-progress">
                <div class="progress-label">
                    <span><strong>{{ $totalAll }}/{{ $totalAll }}</strong> sesiones completadas</span>
                    <span>100%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 100%; background: #0ab39c;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="collapse {{ $primerPrograma ? 'show' : '' }}" id="mod-collapse-c-{{ $prog['programa_id'] }}-{{ $mod['id'] }}">
            <div class="table-responsive">
                <table class="horarios-table table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Fecha</th>
                            <th style="width: 95px;" class="text-center">Horario</th>
                            <th style="width: 110px;">Estado</th>
                            <th>Asignado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mod['horarios'] as $h)
                            @php
                                $enRango = $rangoDesde && $rangoHasta
                                    && $h['fecha_desarrollo'] >= $rangoDesde
                                    && $h['fecha_desarrollo'] <= $rangoHasta;
                            @endphp
                            @include('admin.seguimiento.partials.horario_row', ['enRango' => $enRango, 'labelMes' => $labelMes])
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>{{-- collapse --}}
        </div>
        @endforeach
    </div>
    @endforeach
@endif
