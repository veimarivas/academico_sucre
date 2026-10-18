{{-- Pestaña "Inscripciones" (cargada por AJAX). Requiere: $gestion, $tipo, $gestiones, $asesorNombre,
     $resumen, $porPrograma, $mesesData, $puntosTipoSeries, $inscritosTipoSeries,
     $puntosRealesTipoSeries, $realesInscritosTipoSeries, $noRealesInscritosTipoSeries. --}}
@php $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp

<div class="tab-toolbar">
    <div>
        <p class="tb-title"><i class="ri-team-line"></i> Inscripciones — Gestión {{ $gestion }}</p>
        <p class="tb-sub">Inscritos, puntos y conversión real por mes y programa.</p>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-2">
        <span class="tb-label">Gestión</span>
        <select class="form-select form-select-sm prof-filtro" data-filtro="gestion" style="width:auto;">
            @foreach($gestiones as $g)
                <option value="{{ $g }}" @selected((int)$g === (int)$gestion)>{{ $g }}</option>
            @endforeach
        </select>
        <span class="tb-label">Tipo</span>
        <select class="form-select form-select-sm prof-filtro" data-filtro="tipo" style="width:auto;">
            <option value="general" @selected($tipo === 'general')>General</option>
            <option value="upi" @selected($tipo === 'upi')>Solo UPI Diplomados</option>
            <option value="upi-auto" @selected($tipo === 'upi-auto')>UPI Autogestionados</option>
            <option value="otros" @selected($tipo === 'otros')>Programas Sucre central</option>
        </select>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-2 mb-3">
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#299cdb;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Total inscritos</div><div class="k-val">{{ $resumen['total_inscritos'] }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                <span class="k-ic" style="--k-bg:#f0f9ff; --k-color:#299cdb;"><i class="ri-team-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Activos</div><div class="k-val">{{ $resumen['total_activos'] }}</div><div class="k-sub">inscritos activos</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-check-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Programas</div><div class="k-val">{{ $resumen['total_programas'] }}</div><div class="k-sub">con inscritos</div></div>
                <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-booklet-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#f7b84b;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Puntos totales</div><div class="k-val">{{ number_format($resumen['total_puntos'], 1, ',', '.') }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-star-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Puntos Reales</div>
                    <div class="k-val">{{ number_format($resumen['total_puntos_reales'], 1, ',', '.') }}
                        <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                            ({{ $resumen['total_puntos'] > 0 ? round($resumen['total_puntos_reales'] / $resumen['total_puntos'] * 100) : 0 }}%)
                        </small>
                    </div>
                    <div class="k-sub">de inscritos reales</div>
                </div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-checkbox-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Inscritos Reales</div>
                    <div class="k-val">{{ $resumen['total_reales'] }}
                        <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                            ({{ $resumen['total_inscritos'] > 0 ? round($resumen['total_reales'] / $resumen['total_inscritos'] * 100) : 0 }}%)
                        </small>
                    </div>
                    <div class="k-sub">con pagos al día</div>
                </div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-check-double-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#f06548;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">No Reales</div>
                    <div class="k-val">{{ $resumen['total_no_reales'] }}
                        <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                            ({{ $resumen['total_inscritos'] > 0 ? round($resumen['total_no_reales'] / $resumen['total_inscritos'] * 100) : 0 }}%)
                        </small>
                    </div>
                    <div class="k-sub">con pagos pendientes</div>
                </div>
                <span class="k-ic" style="--k-bg:#fef2f2; --k-color:#f06548;"><i class="ri-close-circle-line"></i></span>
            </div>
        </div>
    </div>
</div>

{{-- Gráfico de inscripciones por mes --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-bar-chart-grouped-line"></i> Inscripciones por Mes — Gestión {{ $gestion }}
        @if($asesorNombre)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorNombre }}</strong>
            </span>
        @endif
    </div>
    <div class="chart-wrapper">
        <div id="chartInscritosMes" style="height:300px;"></div>
    </div>
</div>

{{-- Desglose de inscritos por tipo --}}
@php $inscritosTipoTotal = collect($inscritosTipoSeries)->sum(fn($s) => array_sum($s['data'])); @endphp
@if($inscritosTipoTotal > 0)
<div class="chart-card mb-3">
    <div class="chart-card-title" style="margin-bottom:0; border:0; padding-bottom:0;">
        <i class="ri-price-tag-3-line"></i> Inscritos por Tipo — {{ $gestion }}
        <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">{{ $inscritosTipoTotal }} inscrito(s)</span>
    </div>
    <div class="m-chip-row">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($inscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div class="m-chip">
                    <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                    @foreach($inscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div class="m-chip-item">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                    <span class="m-chip-name">{{ $serie['name'] }}</span>
                                </span>
                                <span class="m-chip-val">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Gráfico de puntos por mes --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-star-line"></i> Puntos por Mes — Gestión {{ $gestion }}
        @if($asesorNombre)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorNombre }}</strong>
            </span>
        @endif
    </div>
    <div id="chartPuntosMes" style="height:300px;"></div>
    <div class="m-chip-row">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($puntosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div class="m-chip">
                    <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                    @foreach($puntosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div class="m-chip-item">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                    <span class="m-chip-name">{{ $serie['name'] }}</span>
                                </span>
                                <span class="m-chip-val">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- Gráfico de Puntos Reales por Mes --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-checkbox-circle-line"></i> Puntos Reales por Mes — Gestión {{ $gestion }}
        @if($asesorNombre)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorNombre }}</strong>
            </span>
        @endif
    </div>
    <div class="chart-wrapper">
        <div id="chartPuntosRealesMes" style="height:300px;"></div>
    </div>
    <div class="m-chip-row">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($puntosRealesTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div class="m-chip">
                    <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                    @foreach($puntosRealesTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div class="m-chip-item">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                    <span class="m-chip-name">{{ $serie['name'] }}</span>
                                </span>
                                <span class="m-chip-val">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- Gráfico de Inscritos Reales vs No Reales por Mes --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-check-double-line"></i> Inscritos Reales vs No Reales por Tipo — Gestión {{ $gestion }}
        @if($asesorNombre)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorNombre }}</strong>
            </span>
        @endif
    </div>
    <div class="chart-wrapper">
        <div id="chartRealesMes" style="height:300px;"></div>
    </div>
    <div class="m-chip-row">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1;
                $mesTotal = collect($realesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) + collect($noRealesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]);
            @endphp
            @if($mesTotal > 0)
                <div class="m-chip">
                    <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                    @foreach($realesInscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div class="m-chip-item">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                    <span class="m-chip-name" style="color:#0ab39c;">R: {{ $serie['name'] }}</span>
                                </span>
                                <span class="m-chip-val" style="color:#0ab39c;">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                    @foreach($noRealesInscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div class="m-chip-item">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span class="m-chip-dot" style="background:{{ $serie['color'] }};opacity:.5;"></span>
                                    <span class="m-chip-name" style="color:#f06548;">NR: {{ $serie['name'] }}</span>
                                </span>
                                <span class="m-chip-val" style="color:#f06548;">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- Inscritos por programa --}}
<div class="tab-toolbar">
    <div>
        <p class="tb-title"><i class="ri-booklet-line"></i> Inscritos por programa</p>
        <p class="tb-sub">{{ $porPrograma->count() }} programa(s) — {{ $resumen['total_inscritos'] }} inscrito(s) — {{ number_format($resumen['total_puntos'], 1, ',', '.') }} pts</p>
    </div>
</div>

@if($porPrograma->count() === 0)
    <div class="empty-state">
        <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
        <h5 class="text-dark fw-bold mb-1">Sin inscripciones en este periodo</h5>
        <p class="text-muted mb-0">No hay inscripciones registradas para la gestión {{ $gestion }}.</p>
    </div>
@else
    <div class="row g-2">
        @foreach($porPrograma as $p)
            <div class="col-xl-4 col-lg-6">
                <div class="prog-card">
                    <div class="prog-card-body">
                        <span class="prog-card-icon"><i class="ri-graduation-cap-line"></i></span>
                        <div class="prog-card-info">
                            <div class="prog-card-name">{{ $p->nombre }}</div>
                            <div class="prog-card-meta">
                                <i class="ri-hashtag"></i> {{ $p->codigo }}
                                @if($p->tipo_nombre) · <i class="ri-price-tag-3-line"></i> {{ $p->tipo_nombre }} @endif
                                · <i class="ri-calendar-2-line"></i> {{ $p->gestion }}
                            </div>
                        </div>
                        <div class="prog-card-count">
                            <div class="num">{{ $p->total }}</div>
                            <div class="lbl">inscritos</div>
                            <div style="font-size:.68rem;color:#8b5cf6;font-weight:700;margin-top:1px;">
                                <i class="ri-star-line"></i> {{ number_format($p->puntos, 1, ',', '.') }} pts
                            </div>
                        </div>
                    </div>
                    <div class="prog-pills">
                        @if($p->activos > 0)<span class="prog-pill pp-act"><i class="ri-check-line"></i> {{ $p->activos }} activos</span>@endif
                        @if($p->retirados > 0)<span class="prog-pill pp-ret"><i class="ri-close-line"></i> {{ $p->retirados }} retirados</span>@endif
                        @if($p->contado > 0)<span class="prog-pill pp-con"><i class="ri-bank-card-line"></i> {{ $p->contado }} contado</span>@endif
                        @if($p->regular > 0)<span class="prog-pill pp-reg"><i class="ri-funds-line"></i> {{ $p->regular }} regular</span>@endif
                        <span class="prog-pill" style="background:#ecfdf5;color:#0ab39c;"><i class="ri-check-double-line"></i> {{ (int)($p->inscritos_reales ?? 0) }} reales</span>
                    </div>
                    <div class="prog-card-footer">
                        <button type="button" class="btn-ver-inscritos" data-bs-toggle="modal" data-bs-target="#modalPerfilInsc-{{ $p->id }}">
                            <i class="ri-eye-line"></i> Ver inscritos ({{ $p->total }})
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modales: lista de inscritos por programa --}}
    @foreach($porPrograma as $p)
        <div class="modal fade" id="modalPerfilInsc-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
                    <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 50%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;position:relative;overflow:hidden;">
                        <div style="position:relative;z-index:1;">
                            <div class="d-flex align-items-center gap-3 mb-1">
                                <div style="width:42px;height:42px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);border-radius:12px;font-size:1.2rem;color:#fff;">
                                    <i class="ri-team-line"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title text-white mb-0" style="font-weight:700;font-size:1rem;">
                                        {{ $p->nombre }}
                                    </h5>
                                    <div style="font-size:.72rem;opacity:.88;display:flex;flex-wrap:wrap;gap:4px 12px;margin-top:2px;">
                                        <span><i class="ri-hashtag"></i> {{ $p->codigo }}</span>
                                        @if($p->tipo_nombre)<span><i class="ri-price-tag-3-line"></i> {{ $p->tipo_nombre }}</span>@endif
                                        <span><i class="ri-calendar-2-line"></i> {{ $p->gestion }}</span>
                                        <span><i class="ri-group-line"></i> {{ $p->total }} inscrito(s)</span>
                                        <span><i class="ri-check-double-line" style="color:#7dffb3;"></i> {{ (int)($p->inscritos_reales ?? 0) }} reales</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background:#f8f9fc;padding:1rem;" data-inscritos-body data-url="{{ route('admin.profile.programa-inscritos', $p->id) }}?gestion={{ $gestion }}&tipo={{ $tipo }}">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                            <p class="mt-2 mb-0">Cargando inscritos...</p>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #eef0f4;padding:.75rem 1.25rem;background:#fff;">
                        <div style="font-size:.72rem;display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                            <span class="text-muted" style="font-weight:600;text-transform:uppercase;letter-spacing:.3px;font-size:.6rem;">Plan:</span>
                            @if($p->contado > 0)<span class="plan-pill plan-con" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-bank-card-line"></i> {{ $p->contado }} Contado</span>@endif
                            @if($p->regular > 0)<span class="plan-pill plan-reg" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-funds-line"></i> {{ $p->regular }} Regular</span>@endif
                            @php
                                $realesCount = (int)($p->inscritos_reales ?? 0);
                                $noRealesCount = $p->total - $realesCount;
                            @endphp
                            <span class="text-muted" style="font-size:.62rem;">|</span>
                            @if($realesCount > 0)<span style="font-size:.62rem;padding:2px 8px;border-radius:5px;background:#ecfdf5;color:#0ab39c;border:1px solid #a7f3d0;"><i class="ri-check-double-line"></i> {{ $realesCount }} Reales</span>@endif
                            @if($noRealesCount > 0)<span style="font-size:.62rem;padding:2px 8px;border-radius:5px;background:#fef2f2;color:#f06548;border:1px solid #fecaca;"><i class="ri-close-circle-line"></i> {{ $noRealesCount }} No Reales</span>@endif
                        </div>
                        <button type="button" class="btn btn-sm" style="background:#f1f3f5;color:#495057;border-radius:8px;font-size:.72rem;font-weight:600;padding:.35rem .9rem;border:1px solid #eef0f4;" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
