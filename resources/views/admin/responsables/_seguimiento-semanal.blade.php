@php
    $diasNombres = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    $fmtFechaCorta = function ($ymd) use ($mesesEs) {
        $t = strtotime($ymd);
        return date('j', $t) . ' ' . $mesesEs[(int) date('n', $t) - 1];
    };
    $fmtFechaLarga = function ($ymd) use ($mesesEs, $diasNombres) {
        $t = strtotime($ymd);
        $diaIdx = (int) date('N', $t) - 1;
        return $diasNombres[$diaIdx] . ' ' . date('j', $t) . ' de ' . $mesesEs[(int) date('n', $t) - 1] . ' del ' . date('Y', $t);
    };
    $fmtHora = fn ($h) => $h ? date('H:i', strtotime($h)) : '—';

    $estadoStyles = [
        'Confirmado'    => ['bg' => '#d4f6ee', 'color' => '#0a7059'],
        'Programado'    => ['bg' => '#dde7ff', 'color' => '#1c3e9e'],
        'Desarrollado'  => ['bg' => '#e3e9f7', 'color' => '#283f7a'],
        'Concluido'     => ['bg' => '#d4f6ee', 'color' => '#0a7059'],
        'En Desarrollo' => ['bg' => '#cfe7ff', 'color' => '#0b5ed7'],
        'No Inicio'     => ['bg' => '#eef0f5', 'color' => '#5d6478'],
        'Pendiente'     => ['bg' => '#fff4d6', 'color' => '#8a6100'],
        'Postergado'    => ['bg' => '#fff4d6', 'color' => '#8a6100'],
        'Suspendido'    => ['bg' => '#ffd9d3', 'color' => '#b1240f'],
    ];
    $estadoStyle = function ($estado) use ($estadoStyles) {
        $e = trim((string) $estado);
        return $estadoStyles[$e] ?? ['bg' => '#eef0f5', 'color' => '#5d6478'];
    };

    $rowAccent = function ($estado) {
        $e = trim((string) $estado);
        return match ($e) {
            'Desarrollado', 'Concluido' => '#0ab39c',
            'Confirmado'                 => '#0ab39c',
            'En Desarrollo'              => '#299cdb',
            'Programado'                 => '#405189',
            'Pendiente', 'Postergado'    => '#f7b84b',
            'Suspendido'                 => '#f06548',
            'No Inicio'                  => '#878a99',
            default                      => '#878a99',
        };
    };
@endphp

<div class="sd-hero" style="margin-bottom:1rem;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative">
        <div>
            <div class="sd-hero-title"><i class="ri-radar-line align-middle me-1"></i> Seguimiento Semanal</div>
            <div class="sd-hero-sub">Horarios de la semana para <strong>{{ $responsable->apellidos }}, {{ $responsable->nombres }}</strong></div>
        </div>
        <div class="sd-week-nav d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm sd-semana-nav" data-inicio="{{ $semanaAnterior }}" title="Semana anterior">
                <i class="ri-arrow-left-s-line"></i>
            </button>
            <span class="sd-week-label">
                {{ $fmtFechaCorta($inicio) }} — {{ $fmtFechaCorta($fin) }} · {{ date('Y', strtotime($inicio)) }}
            </span>
            <button type="button" class="btn btn-sm sd-semana-nav" data-inicio="{{ $semanaHoy }}" title="Semana actual">
                <i class="ri-calendar-check-line"></i> Hoy
            </button>
            <button type="button" class="btn btn-sm sd-semana-nav" data-inicio="{{ $semanaSiguiente }}" title="Semana siguiente">
                <i class="ri-arrow-right-s-line"></i>
            </button>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-2 mb-3">
    @php
        $cards = [
            ['l' => 'Sesiones de la semana', 'v' => $resumen['total'],         'i' => 'ri-calendar-event-line', 'a' => '#405189', 'b' => '#e9eef8', 'c' => '#405189'],
            ['l' => 'Desarrolladas',         'v' => $resumen['desarrollados'], 'i' => 'ri-checkbox-circle-line','a' => '#0ab39c', 'b' => '#d4f6ee', 'c' => '#0a7059'],
            ['l' => 'Pendientes',            'v' => $resumen['pendientes'],    'i' => 'ri-time-line',           'a' => '#f7b84b', 'b' => '#fff4d6', 'c' => '#8a6100'],
            ['l' => 'Atrasadas sin iniciar', 'v' => $resumen['atrasados'],     'i' => 'ri-error-warning-line',  'a' => '#d63031', 'b' => '#ffd9d3', 'c' => '#b1240f'],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="col-6 col-md-3">
            <div class="sd-stat-card" style="--sd-accent: {{ $c['a'] }};">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-w-0">
                        <div class="sd-stat-label">{{ $c['l'] }}</div>
                        <div class="sd-stat-value">{{ $c['v'] }}</div>
                    </div>
                    <div class="sd-stat-icon" style="background:{{ $c['b'] }}; color:{{ $c['c'] }};">
                        <i class="{{ $c['i'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($resumen['atrasados'] > 0)
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="font-size:.82rem;">
        <i class="ri-alert-line fs-5"></i>
        <div>
            Hay <b>{{ $resumen['atrasados'] }}</b> sesión(es) cuya fecha ya pasó y siguen en estado <b>No Inicio</b>.
        </div>
    </div>
@endif

{{-- Days --}}
@foreach ($dias as $fecha => $items)
    @php
        $t = strtotime($fecha);
        $diaIdx = (int) date('N', $t) - 1;
        $esHoy = $fecha === $hoy;
        $countDia = $items->count();
        $atrasadosDia = $items->where('atrasado', true)->count();
    @endphp
    <div class="sd-day">
        <div class="sd-day-header">
            <div class="sd-day-pill {{ $esHoy ? 'is-today' : '' }}">
                <span class="sd-day-num">{{ date('j', $t) }}</span>
                <span class="sd-day-name">{{ substr($diasNombres[$diaIdx], 0, 3) }}</span>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#2a2f43;">
                    {{ $diasNombres[$diaIdx] }}
                    <span class="text-muted fw-normal ms-1" style="font-size:.78rem;">· {{ $fmtFechaCorta($fecha) }}</span>
                    @if ($esHoy)
                        <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.65rem;">HOY</span>
                    @endif
                </div>
                <div class="sd-day-count">
                    {{ $countDia }} {{ $countDia === 1 ? 'sesión' : 'sesiones' }}
                    @if ($atrasadosDia > 0)
                        · <span class="text-danger fw-semibold">{{ $atrasadosDia }} atrasada{{ $atrasadosDia === 1 ? '' : 's' }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if ($countDia === 0)
            <div class="sd-day-empty">
                <i class="ri-inbox-2-line align-middle me-1"></i> Sin sesiones programadas para este día
            </div>
        @else
            @foreach ($items as $h)
                @php
                    $est = trim((string) $h->estado);
                    $styEst = $estadoStyle($est);
                    $accent = $rowAccent($est);
                    $esPasada = $h->fecha_desarrollo < $hoy;
                    $esDesarrollado = $est === 'Desarrollado';
                    $esConfirmado = $est === 'Confirmado';
                    $tieneObs = !empty(trim((string) $h->observaciones));
                    $tieneAsist = isset($asistenciasPorHorario[$h->id]) && $asistenciasPorHorario[$h->id]->count() > 0;
                    $faltaRegistro = $esDesarrollado && !$tieneAsist;

                    $rowClasses = [];
                    $modalKind = '';
                    $btnKind = '';
                    $btnLabel = 'Ver detalle';
                    $flagHtml = '';
                    $alertHtml = '';

                    if ($h->atrasado) {
                        $rowClasses[] = 'is-late';
                        $modalKind = 'is-late';
                        $btnKind = 'is-danger';
                        $btnLabel = 'Atender ahora';
                        $flagHtml = '<span class="sd-flag is-danger"><i class="ri-alarm-warning-line"></i> Atrasado</span>';
                        $alertHtml = '<div class="sd-modal-alert is-danger"><i class="ri-alarm-warning-line"></i><div><b>Sesión atrasada.</b> La fecha ya pasó y el horario sigue en <b>No Inicio</b>.</div></div>';
                    } elseif ($esConfirmado && $esPasada) {
                        $rowClasses[] = 'is-confirm-late';
                        $modalKind = 'is-warn';
                        $btnKind = 'is-warn';
                        $btnLabel = 'Sin actualizar';
                        $flagHtml = '<span class="sd-flag is-warn"><i class="ri-time-line"></i> Sin actualizar</span>';
                        $alertHtml = '<div class="sd-modal-alert is-warn"><i class="ri-time-line"></i><div><b>El responsable no cambió el estado de la sesión.</b> La fecha ya pasó pero el horario sigue en <b>Confirmado</b>.</div></div>';
                    } elseif ($esConfirmado) {
                        $rowClasses[] = 'is-confirm';
                    } elseif ($faltaRegistro) {
                        $rowClasses[] = 'is-missing';
                        $modalKind = 'is-info';
                        $btnKind = 'is-info';
                        $btnLabel = 'Falta registrar asistencia';
                        $flagHtml = '<span class="sd-flag is-info"><i class="ri-information-line"></i> Falta asistencia</span>';
                        $alertHtml = '<div class="sd-modal-alert is-info"><i class="ri-information-line"></i><div><b>Sesión marcada como desarrollada pero falta registrar la lista de asistencia.</b></div></div>';
                    } elseif ($esDesarrollado && $tieneAsist) {
                        $btnLabel = $tieneObs ? 'Con asistencia y observaciones' : 'Con asistencia';
                    }
                    $rowClassStr = implode(' ', $rowClasses);

                    $collapseId = 'sdDetail_' . $h->id;
                    $tplId = 'sdTpl_' . $h->id;
                    $asignadoColor = $h->asignado_hex ? ('#' . ltrim($h->asignado_hex, '#')) : '#6c757d';
                    $programaUrl = $h->programa_id ? route('admin.programas.show', $h->programa_id) : null;
                    $docenteNombre = trim(($h->docente_nombres ?? '') . ' ' . ($h->docente_apellidos ?? ''));
                    $asignadoNombre = trim(($h->asignado_nombres ?? '') . ' ' . ($h->asignado_apellidos ?? ''));
                    $tituloModal = ($h->programa_nombre ?? 'Programa') . ($h->programa_codigo ? ' · ' . $h->programa_codigo : '');
                    $subtituloModal = $fmtFechaLarga($h->fecha_desarrollo) . ' · ' . $fmtHora($h->hora_inicio) . ' a ' . $fmtHora($h->hora_fin);
                @endphp
                <div class="sd-h-row {{ $rowClassStr }}" style="--sd-row-accent: {{ $accent }};">
                    <div class="sd-h-time">
                        <span class="h-i">{{ $fmtHora($h->hora_inicio) }}</span>
                        <span class="h-s">a {{ $fmtHora($h->hora_fin) }}</span>
                    </div>
                    <div class="sd-h-body">
                        <div class="sd-h-title">
                            @if ($programaUrl)
                                <a href="{{ $programaUrl }}" class="sd-link" title="Ir al programa" target="_blank">
                                    {{ $h->programa_nombre ?? '— Programa sin nombre' }}
                                </a>
                            @else
                                {{ $h->programa_nombre ?? '— Programa sin nombre' }}
                            @endif
                            @if (!empty($h->programa_codigo))
                                @if ($programaUrl)
                                    <a href="{{ $programaUrl }}" class="sd-codigo" style="text-decoration:none;" target="_blank">{{ $h->programa_codigo }}</a>
                                @else
                                    <span class="sd-codigo">{{ $h->programa_codigo }}</span>
                                @endif
                            @endif
                        </div>
                        <div class="sd-h-modulo">
                            <i class="ri-book-2-line align-bottom text-muted me-1"></i>
                            @if ($programaUrl)
                                <a href="{{ $programaUrl }}" class="sd-link" title="Ir al programa / módulo" target="_blank">
                                    {{ $h->modulo_nombre ?? '— Módulo —' }}
                                </a>
                            @else
                                {{ $h->modulo_nombre ?? '— Módulo —' }}
                            @endif
                        </div>
                        <div class="sd-h-meta">
                            <span class="sd-meta-i" title="Docente">
                                <i class="ri-user-star-line"></i>
                                <b>Docente:</b>
                                @if ($docenteNombre !== '')
                                    {{ $docenteNombre }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </span>
                            @if (!empty($h->tipo_nombre))
                                <span class="sd-meta-i" title="Tipo de programa">
                                    <i class="ri-price-tag-3-line"></i>{{ $h->tipo_nombre }}
                                </span>
                            @endif
                            @if (!empty($h->gestion))
                                <span class="sd-meta-i" title="Gestión">
                                    <i class="ri-calendar-line"></i>{{ $h->gestion }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="sd-h-side">
                        <div class="sd-h-estado">
                            <span class="sd-pill" style="--sd-pill-bg: {{ $styEst['bg'] }}; --sd-pill-color: {{ $styEst['color'] }};">
                                {{ $est ?: '—' }}
                            </span>
                        </div>
                        {!! $flagHtml !!}
                        <button type="button"
                                class="sd-btn-toggle {{ $btnKind }} sd-open-modal"
                                data-horario-id="{{ $h->id }}"
                                data-modal-kind="{{ $modalKind }}"
                                data-modal-title="{{ $tituloModal }}"
                                data-modal-subtitle="{{ $subtituloModal }}">
                            <i class="ri-eye-line"></i> {{ $btnLabel }}
                        </button>
                    </div>
                </div>

                <div class="sd-tpl" id="{{ $tplId }}">
                    {!! $alertHtml !!}

                    <div class="sd-modal-info-grid">
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Programa</div>
                            <div class="sd-info-value">
                                @if ($programaUrl)
                                    <a href="{{ $programaUrl }}" target="_blank">{{ $h->programa_nombre ?? '—' }}</a>
                                @else
                                    {{ $h->programa_nombre ?? '—' }}
                                @endif
                                @if (!empty($h->programa_codigo))
                                    <span class="text-muted">· {{ $h->programa_codigo }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Módulo</div>
                            <div class="sd-info-value">
                                @if ($programaUrl)
                                    <a href="{{ $programaUrl }}" target="_blank">{{ $h->modulo_nombre ?? '—' }}</a>
                                @else
                                    {{ $h->modulo_nombre ?? '—' }}
                                @endif
                            </div>
                        </div>
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Docente</div>
                            <div class="sd-info-value">
                                @if ($docenteNombre !== '')
                                    {{ $docenteNombre }}
                                    @if (!empty($h->docente_ci))
                                        <span class="text-muted">· CI {{ $h->docente_ci }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </div>
                        </div>
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Responsable</div>
                            <div class="sd-info-value">
                                @if ($asignadoNombre !== '')
                                    <span class="sd-asignado-chip" style="background: {{ $asignadoColor }};">
                                        <i class="ri-user-line"></i>{{ $asignadoNombre }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </div>
                        </div>
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Fecha y horario</div>
                            <div class="sd-info-value">
                                {{ $fmtFechaLarga($h->fecha_desarrollo) }}
                                <span class="text-muted">· {{ $fmtHora($h->hora_inicio) }} a {{ $fmtHora($h->hora_fin) }}</span>
                            </div>
                        </div>
                        <div class="sd-info-cell">
                            <div class="sd-info-label">Estado</div>
                            <div class="sd-info-value">
                                <span class="sd-pill" style="--sd-pill-bg: {{ $styEst['bg'] }}; --sd-pill-color: {{ $styEst['color'] }};">
                                    {{ $est ?: '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-5">
                            <div class="sd-detail-title"><i class="ri-message-2-line"></i> Observaciones</div>
                            <div class="sd-obs-box {{ $tieneObs ? '' : 'sd-obs-empty' }}">
                                {{ $tieneObs ? $h->observaciones : 'Sin observaciones registradas.' }}
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
                            <div class="sd-detail-title">
                                <i class="ri-team-line"></i> Lista de asistencia
                                @if ($tieneAsist)
                                    <span class="badge bg-soft-primary text-primary ms-1">
                                        {{ $asistenciasPorHorario[$h->id]->count() }}
                                    </span>
                                @endif
                            </div>
                            @if (!$tieneAsist)
                                <div class="sd-obs-box sd-obs-empty">
                                    Sin registros de asistencia — solicita al responsable que registre la lista.
                                </div>
                            @else
                                @php
                                    $asis = $asistenciasPorHorario[$h->id];
                                    $resumenAsis = $asis->groupBy(fn ($a) => trim($a->asistio));
                                @endphp
                                <div class="d-flex flex-wrap gap-2 mb-2" style="font-size:.72rem;">
                                    @foreach ($resumenAsis as $tipo => $regs)
                                        <span class="a-pill a-{{ $tipo }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .55rem;border-radius:999px;font-weight:700;">
                                            {{ $tipo }} · {{ $regs->count() }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="table-responsive" style="max-height: 320px; overflow-y:auto; border:1px solid #eef0f5; border-radius:8px;">
                                    <table class="sd-asist-table">
                                        <thead>
                                            <tr>
                                                <th style="width:34px;">#</th>
                                                <th style="width:90px;">CI</th>
                                                <th>Participante</th>
                                                <th style="width:110px;">Estado</th>
                                                <th>Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($asis as $idx => $a)
                                                <tr>
                                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                                    <td>{{ $a->ci }}</td>
                                                    <td>{{ trim(($a->apellidos ?? '') . ', ' . ($a->nombres ?? '')) }}</td>
                                                    <td>
                                                        <span class="a-pill a-{{ trim($a->asistio) }}">{{ $a->asistio }}</span>
                                                    </td>
                                                    <td class="text-muted">{{ $a->observacion ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endforeach
