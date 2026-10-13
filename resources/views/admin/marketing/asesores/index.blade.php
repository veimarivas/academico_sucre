@extends('admin.layouts.master')

@section('title', 'Asesores - Marketing - ' . config('app.name'))

@push('css')
<style>
.mkt-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.mkt-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.mkt-hero::after  { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.mkt-hero > * { position:relative; z-index:1; }
.mkt-hero .hero-icon { width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.25); border-radius:14px; font-size:1.6rem; backdrop-filter: blur(4px); }
.mkt-hero h4 { color:#fff; font-weight:700; margin:0; }
.mkt-hero .hero-sub { color: rgba(255,255,255,.85); font-size:.88rem; margin: 2px 0 0; }
.mkt-hero .breadcrumb { background:transparent; padding:0; margin:0; }
.mkt-hero .breadcrumb-item, .mkt-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.mkt-hero .breadcrumb-item.active { color:#fff; font-weight:600; }

.filter-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); margin-bottom:1.25rem; }
[data-layout-mode="dark"] .filter-card { background:#1d2125; border-color:#2a2f34; }
.filter-card .card-body { padding: .9rem 1.25rem; }
.filter-card label { font-size:.65rem; text-transform:uppercase; letter-spacing:.45px; font-weight:700; color:#878a99; margin-bottom:2px; display:block; }
.filter-card select { border:1px solid #e9ebec; border-radius:8px; padding:.45rem .75rem; font-size:.85rem; font-weight:700; color:#405189; min-width:180px; }
.filter-card select:focus { outline:0; border-color:#299cdb; box-shadow: 0 0 0 .15rem rgba(41,156,219,.15); }
[data-layout-mode="dark"] .filter-card select { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

.kpi { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.15rem; height:100%; position:relative; overflow:hidden; transition: all .2s ease; }
.kpi::before { content:""; position:absolute; top:0; left:0; height:100%; width:4px; background: var(--k-accent, #299cdb); }
.kpi:hover { transform: translateY(-2px); box-shadow: 0 12px 26px -14px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .kpi { background:#1d2125; border-color:#2a2f34; }
.kpi .k-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.kpi .k-val { font-size:1.4rem; font-weight:800; color:#212529; line-height:1.1; }
[data-layout-mode="dark"] .kpi .k-val { color:#fff; }
.kpi .k-sub { font-size:.7rem; color:#878a99; margin-top:2px; }
.kpi .k-ic { width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; background: var(--k-bg, #eef1f8); color: var(--k-color, #405189); }

.chart-card { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1.25rem; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .chart-card { background:#1d2125; border-color:#2a2f34; }
.chart-card-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#495057; display:flex; align-items:center; gap:.5rem; margin-bottom:1rem; padding-bottom:.65rem; border-bottom: 1px dashed #eef0f4; }
[data-layout-mode="dark"] .chart-card-title { color:#ced4da; border-bottom-color:#2a2f34; }
.chart-card-title i { color:#405189; font-size:1rem; }
.chart-wrapper { position:relative; width:100%; height:300px; }
canvas.chart-canvas { width:100% !important; height:300px !important; }

.prog-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; transition: all .2s ease; overflow:hidden; }
[data-layout-mode="dark"] .prog-card { background:#1d2125; border-color:#2a2f34; }
.prog-card:hover { box-shadow:0 10px 24px -12px rgba(64,81,137,.25); transform: translateY(-2px); }
.prog-card-body { padding: 1rem 1.15rem; display:flex; align-items:center; gap:.9rem; }
.prog-card-icon { width:46px; height:46px; flex-shrink:0; background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.3rem; box-shadow: 0 6px 14px -6px rgba(64,81,137,.55); }
.prog-card-info { flex:1; min-width:0; }
.prog-card-name { font-weight:800; color:#212529; font-size:.92rem; line-height:1.2; }
[data-layout-mode="dark"] .prog-card-name { color:#fff; }
.prog-card-meta { font-size:.72rem; color:#878a99; margin-top:2px; }
.prog-card-count { display:flex; flex-direction:column; align-items:flex-end; }
.prog-card-count .num { font-size:1.6rem; font-weight:800; color:#405189; line-height:1; }
.prog-card-count .lbl { font-size:.65rem; text-transform:uppercase; letter-spacing:.4px; color:#878a99; font-weight:700; }

.prog-pills { display:flex; flex-wrap:wrap; gap:.4rem; padding: 0 1.15rem .65rem; align-items:center; }
.prog-card-footer { padding: .65rem 1.15rem; background:#f8f9fc; border-top:1px solid #eef0f4; display:flex; justify-content:flex-end; }
[data-layout-mode="dark"] .prog-card-footer { background:#1a1d20; border-top-color:#2a2f34; }
.btn-ver-inscritos {
    display:inline-flex; align-items:center; gap:5px;
    font-size:.72rem; font-weight:700;
    padding:.32em .85em; border-radius:8px;
    background:#fff; color:#405189; border:1px solid #c3cfe2;
    cursor:pointer; transition: all .15s ease;
}
.btn-ver-inscritos:hover { background:#405189; color:#fff; border-color:#405189; }

/* Modal inscritos */
.insc-modal-header { background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; border:0; }
.insc-modal-header .btn-close { filter: brightness(0) invert(1); opacity:.85; }
.insc-row {
    display:flex; align-items:center; gap:.7rem;
    padding:.55rem .85rem; border:1px solid #eef0f4; border-radius:10px;
    margin-bottom:.45rem; background:#fff;
}
[data-layout-mode="dark"] .insc-row { background:#1d2125; border-color:#2a2f34; }
.insc-row:hover { border-color:#c3cfe2; }
.insc-avatar {
    width:30px; height:30px; border-radius:50%;
    background: linear-gradient(135deg, #405189, #5e72b8);
    color:#fff; font-weight:800; font-size:.7rem;
    display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.insc-info { flex:1; min-width:0; }
.insc-name { font-weight:700; color:#212529; font-size:.84rem; line-height:1.2; }
[data-layout-mode="dark"] .insc-name { color:#fff; }
.insc-meta { font-size:.68rem; color:#878a99; margin-top:1px; }
.plan-pill {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.68rem; font-weight:800;
    padding:.22em .65em; border-radius:999px;
    border:1px solid;
    white-space:nowrap;
}
.plan-con { background:#dbeafe; color:#299cdb; border-color:#bae6fd; }
.plan-reg { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
.estado-pill {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.65rem; font-weight:700;
    padding:.18em .55em; border-radius:6px;
    border:1px solid;
    white-space:nowrap;
}
.est-act { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.est-ret { background:#fee2e2; color:#f06548; border-color:#fecaca; }
.prog-pill { display:inline-flex; align-items:center; gap:4px; font-size:.7rem; font-weight:700; padding:.25em .7em; border-radius:999px; border:1px solid; }
.pp-act { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.pp-ret { background:#fee2e2; color:#f06548; border-color:#fecaca; }
.pp-con { background:#dbeafe; color:#299cdb; border-color:#bae6fd; }
.pp-reg { background:#eef1f8; color:#405189; border-color:#c3cfe2; }

.empty-state { text-align:center; padding:3rem 1rem; background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-radius:14px; border:1px dashed #d6dbe0; }
.empty-state .empty-icon { width:80px; height:80px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:50%; font-size:2.5rem; color:#adb5bd; box-shadow: 0 8px 20px -10px rgba(0,0,0,.15); }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="mkt-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-user-voice-line"></i></div>
        <div>
            <h4>Asesores</h4>
            <p class="hero-sub">Inscritos por mes y por programa — Gestión <span class="fw-bold">{{ $gestion }}</span>
                @if($asesorActual)
                    · Asesor: <span class="fw-bold">{{ $asesorActual->apellidos }}, {{ $asesorActual->nombres }}</span>
                @else
                    · <span class="fw-bold">Todos los asesores</span>
                @endif
                @php
                    $tipoLabel = match($tipo) { 'upi' => 'Solo UPI Diplomados', 'upi-auto' => 'UPI Autogestionados', 'otros' => 'Programas Sucre central', default => 'General' };
                @endphp
                · Tipo: <span class="fw-bold">{{ $tipoLabel }}</span>
            </p>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Marketing</a></li>
        <li class="breadcrumb-item active">Asesores</li>
    </ol>
    @can('asesores.registrar')
    <button type="button" class="btn btn-sm btn-soft-primary d-inline-flex align-items-center gap-1" id="btn-nuevo-asesor">
        <i class="ri-add-circle-line"></i> Registrar Asesor
    </button>
    @endcan
</div>

{{-- Filtros --}}
<div class="card filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.marketing.asesores') }}" class="d-flex align-items-end flex-wrap gap-3">
            <div>
                <label><i class="ri-user-voice-line"></i> Asesor</label>
                <select name="asesor_id" onchange="this.form.submit()">
                    <option value="">Todos los asesores</option>
                    @foreach($asesores as $a)
                        <option value="{{ $a->id }}" @selected((string)$asesorId === (string)$a->id)>
                            {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->total_inscritos }} · {{ number_format($a->total_puntos, 1, ',', '.') }} pts)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label><i class="ri-calendar-2-line"></i> Gestión</label>
                <select name="gestion" onchange="this.form.submit()">
                    @foreach($gestiones as $g)
                        <option value="{{ $g }}" @selected((int)$g === (int)$gestion)>{{ $g }}</option>
                    @endforeach
                    @if(!$gestiones->contains((string)$gestion) && !$gestiones->contains((int)$gestion))
                        <option value="{{ $gestion }}" selected>{{ $gestion }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label><i class="ri-price-tag-3-line"></i> Tipo</label>
                    <select name="tipo" onchange="this.form.submit()">
                        <option value="general" @selected($tipo === 'general')>General</option>
                        <option value="upi" @selected($tipo === 'upi')>Solo UPI Diplomados</option>
                        <option value="upi-auto" @selected($tipo === 'upi-auto')>UPI Autogestionados</option>
                        <option value="otros" @selected($tipo === 'otros')>Programas Sucre central</option>
                    </select>
            </div>
            @if($asesorId !== '' && $asesorId !== null)
                <a href="{{ route('admin.marketing.asesores', ['gestion' => $gestion, 'tipo' => $tipo]) }}" class="btn btn-sm btn-light ms-2" style="font-size:.75rem;">
                    <i class="ri-close-circle-line"></i> Quitar filtro de asesor
                </a>
            @endif
            <small class="text-muted ms-auto" style="font-size:.72rem;">
                <i class="ri-information-line"></i> Sólo se cuentan inscripciones con asesor asignado
            </small>
        </form>
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
                <div><div class="k-lbl">Asesores</div><div class="k-val">{{ $resumen['total_asesores'] }}</div><div class="k-sub">{{ $asesorActual ? 'seleccionado' : 'totales' }}</div></div>
                <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-user-voice-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#8b5cf6;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Puntos totales</div><div class="k-val">{{ number_format($resumen['total_puntos'], 1, ',', '.') }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                <span class="k-ic" style="--k-bg:#f3e8ff; --k-color:#8b5cf6;"><i class="ri-star-line"></i></span>
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
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#f06548;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Traspasos</div>
                    <div class="k-val">{{ $resumen['total_traspasos'] }}</div>
                    <div class="k-sub">de otras unidades</div>
                </div>
                <span class="k-ic" style="--k-bg:#fef2f2; --k-color:#f06548;"><i class="ri-swap-line"></i></span>
            </div>
        </div>
    </div>
</div>

@php $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp

{{-- Gráfico de inscripciones por mes (estilo dashboard) --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-bar-chart-grouped-line"></i> Inscripciones por Mes — Gestión {{ $gestion }}
        @if($asesorActual)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorActual->apellidos }}, {{ $asesorActual->nombres }}</strong>
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
    <div style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:8px;">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($inscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div style="padding:6px 10px; background:#f8f9fc; border-radius:8px; border:1px solid #eef0f4; min-width:140px; flex:1;">
                    <div style="font-size:.7rem; font-weight:700; color:#495057; margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                    @foreach($inscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div style="font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0;">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                    <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                </span>
                                <span style="font-weight:700;color:#212529;">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Gráfico de puntos por mes con desglose por tipo --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-star-line"></i> Puntos por Mes — Gestión {{ $gestion }}
        @if($asesorActual)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorActual->apellidos }}, {{ $asesorActual->nombres }}</strong>
            </span>
        @endif
    </div>
    <div id="chartPuntosMes" style="height:300px;"></div>
    <div style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:8px;">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($puntosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div style="padding:6px 10px; background:#f8f9fc; border-radius:8px; border:1px solid #eef0f4; min-width:150px; flex:1;">
                    <div style="font-size:.7rem; font-weight:700; color:#495057; margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                    @foreach($puntosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div style="font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0;">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                    <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                </span>
                                <span style="font-weight:700;color:#212529;">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
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
        @if($asesorActual)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorActual->apellidos }}, {{ $asesorActual->nombres }}</strong>
            </span>
        @endif
    </div>
    <div class="chart-wrapper">
        <div id="chartPuntosRealesMes" style="height:300px;"></div>
    </div>
    <div style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:8px;">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; @endphp
            @if(collect($puntosRealesTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                <div style="padding:6px 10px; background:#f8f9fc; border-radius:8px; border:1px solid #eef0f4; min-width:150px; flex:1;">
                    <div style="font-size:.7rem; font-weight:700; color:#495057; margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                    @foreach($puntosRealesTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div style="font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0;">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                    <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                </span>
                                <span style="font-weight:700;color:#212529;">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- Cumplimiento de metas por asesor (puntos reales vs meta del cargo) --}}
@if(!empty($cumplimientoMetas))
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-target-line"></i> Cumplimiento de Metas por Asesor — Gestión {{ $gestion }}
        <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
            Puntos reales por mes vs meta del cargo
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-1" style="width:100%;">
            <thead>
                <tr>
                    <th style="min-width:180px;">Asesor</th>
                    <th style="min-width:140px;">Cargo</th>
                    <th class="text-center" style="min-width:80px;">Meta/mes</th>
                    @foreach(range(1, 12) as $m)
                        <th class="text-center" style="min-width:74px;">{{ \Illuminate\Support\Str::limit($mesesNombres[$m - 1], 3, '') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($cumplimientoMetas as $fila)
                <tr>
                    <td class="fw-semibold fs-12">{{ $fila['apellidos'] }}, {{ $fila['nombres'] }}</td>
                    <td class="text-muted fs-11">{{ $fila['cargo_nombre'] }}</td>
                    <td class="text-center fw-bold fs-11">{{ $fila['meta'] > 0 ? number_format($fila['meta'], 0, ',', '.') : '—' }}</td>
                    @foreach($fila['meses'] as $mm)
                        @if($mm['estado'] === 'sin_meta')
                            <td class="text-center text-muted fs-11">—</td>
                        @elseif($mm['estado'] === 'cumplido')
                            <td class="text-center">
                                <div class="badge bg-soft-success text-success fs-10" style="font-size:.62rem!important; line-height:1.25;" title="Meta cumplida">
                                    {{ number_format($mm['real'], 1, ',', '.') }}
                                    <span class="d-block">+{{ number_format($mm['diff'], 1, ',', '.') }}</span>
                                </div>
                            </td>
                        @else
                            <td class="text-center">
                                <div class="badge bg-soft-danger text-danger fs-10" style="font-size:.62rem!important; line-height:1.25;" title="No alcanzó la meta">
                                    {{ number_format($mm['real'], 1, ',', '.') }}
                                    <span class="d-block">-{{ number_format(abs($mm['diff']), 1, ',', '.') }}</span>
                                </div>
                            </td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <small class="text-muted fs-11">
        <i class="ri-information-line"></i> Verde: meta cumplida · Rojo: no alcanzó la meta. El segundo valor indica cuántos puntos le faltaron (-) o superó (+).
    </small>
</div>
@endif

{{-- Gráfico de Inscritos Reales vs No Reales por Mes --}}
<div class="chart-card mb-3">
    <div class="chart-card-title">
        <i class="ri-check-double-line"></i> Inscritos Reales vs No Reales por Tipo — Gestión {{ $gestion }}
        @if($asesorActual)
            <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                Asesor: <strong>{{ $asesorActual->apellidos }}, {{ $asesorActual->nombres }}</strong>
            </span>
        @endif
    </div>
    <div class="chart-wrapper">
        <div id="chartRealesMes" style="height:300px;"></div>
    </div>
    <div style="margin-top:.75rem; display:flex; flex-wrap:wrap; gap:8px;">
        @foreach(range(1, 12) as $m)
            @php $idx = $m - 1; $mesTotal = collect($realesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) + collect($noRealesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]); @endphp
            @if($mesTotal > 0)
                <div style="padding:6px 10px; background:#f8f9fc; border-radius:8px; border:1px solid #eef0f4; min-width:150px; flex:1;">
                    <div style="font-size:.7rem; font-weight:700; color:#495057; margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                    @foreach($realesInscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div style="font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0;">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                    <span style="color:#0ab39c;">R: {{ $serie['name'] }}</span>
                                </span>
                                <span style="font-weight:700;color:#0ab39c;">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                    @foreach($noRealesInscritosTipoSeries as $serie)
                        @if(($serie['data'][$idx] ?? 0) > 0)
                            <div style="font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0;">
                                <span style="display:flex; align-items:center; gap:4px;">
                                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;opacity:.5;"></span>
                                    <span style="color:#f06548;">NR: {{ $serie['name'] }}</span>
                                </span>
                                <span style="font-weight:700;color:#f06548;">{{ $serie['data'][$idx] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- Inscritos por programa --}}
<div class="chart-card-title" style="background:none; border:0; padding:0; margin-bottom:1rem;">
    <i class="ri-booklet-line"></i> Inscritos por Programa
    <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
        {{ $porPrograma->count() }} programa(s) — {{ $resumen['total_inscritos'] }} inscrito(s) — {{ number_format($resumen['total_puntos'], 1, ',', '.') }} pts
    </span>
</div>

@if($porPrograma->count() === 0)
    <div class="empty-state">
        <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
        <h5 class="text-dark fw-bold mb-1">Sin inscritos en este periodo</h5>
        <p class="text-muted mb-0">No hay inscripciones registradas para la gestión {{ $gestion }}@if($asesorActual) con el asesor seleccionado @endif.</p>
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
                        @if(isset($p->traspasos) && $p->traspasos > 0)<span class="prog-pill" style="background:#fef2f2;color:#f06548;border-color:#fecaca;"><i class="ri-swap-line"></i> {{ $p->traspasos }} traspaso(s)</span>@endif
                        <span class="prog-pill" style="background:#ecfdf5;color:#0ab39c;"><i class="ri-check-double-line"></i> {{ (int)($p->inscritos_reales ?? 0) }} reales</span>
                    </div>
                    <div class="prog-card-footer">
                        @can('asesores.inscritos.ver')
                        <button type="button" class="btn-ver-inscritos" data-bs-toggle="modal" data-bs-target="#modalInscritos-{{ $p->id }}">
                            <i class="ri-eye-line"></i> Ver inscritos ({{ $p->total }})
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modales: lista de inscritos por programa --}}
    @can('asesores.inscritos.ver')
    @foreach($porPrograma as $p)
        @php $listaInsc = $inscritosPorPrograma->get($p->id, collect()); @endphp
        <div class="modal fade" id="modalInscritos-{{ $p->id }}" tabindex="-1" aria-hidden="true">
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
                    <div class="modal-body" style="background:#f8f9fc;padding:1rem;">
                        @forelse($listaInsc as $i => $r)
                            @php
                                $iniIns = strtoupper(substr($r->nombres ?? '', 0, 1) . substr($r->apellidos ?? '', 0, 1));
                                $plan = $r->contado ? 'Contado' : 'Regular';
                                $planCls = $r->contado ? 'plan-con' : 'plan-reg';
                                $planIcon = $r->contado ? 'ri-bank-card-line' : 'ri-funds-line';
                                $esBajaOperativa = (int)($r->baja_operativa ?? 0);
                                $esBajaAcademica = (int)($r->baja_academica ?? 0);
                                $esRetirado = !(int)$r->estado || $esBajaOperativa || $esBajaAcademica;
                                if ($esBajaOperativa) {
                                    $estCls = 'est-ret'; $estTxt = 'Baja Operativa'; $estIcon = 'ri-close-circle-line';
                                } elseif ($esBajaAcademica) {
                                    $estCls = 'est-ret'; $estTxt = 'Baja Académica'; $estIcon = 'ri-close-circle-line';
                                } elseif (!(int)$r->estado) {
                                    $estCls = 'est-ret'; $estTxt = 'Baja Temprana'; $estIcon = 'ri-close-line';
                                } else {
                                    $estCls = 'est-act'; $estTxt = 'Activo'; $estIcon = 'ri-check-line';
                                }
                                $esReal = (int)($r->es_real ?? 0);
                                $matReq = (float)($r->matricula_requerido ?? 0);
                                $matPag = (float)($r->matricula_pagado ?? 0);
                                $matFalta = max(0, $matReq - $matPag);
                                $colPag = (float)($r->colegiatura_pagado ?? 0);
                                $colFalta = 0;
                                $colLabel = 'Colegiatura';
                                if ((int)($r->contado ?? 0) === 1) {
                                    $numMod = max(1, (int)($r->num_modulos ?? 1));
                                    if ((int)($r->modulo_cero ?? 0) === 1) { $numMod = max(1, $numMod - 1); }
                                    $colReq = ((float)($r->monto_programa ?? 0) - (float)($r->descuento_colegiatura ?? 0)) / $numMod;
                                    $colFalta = max(0, $colReq - $colPag);
                                    $colLabel = 'Coleg. (1/'.$numMod.')';
                                } else {
                                    $colFalta = $colPag > 0 ? 0 : (float)($r->colegiatura_primer_monto ?? 0);
                                    $colLabel = 'Coleg. (1.er pago)';
                                }
                            @endphp
                            <div class="insc-row" style="background:#fff;border:1px solid #eef0f4;border-radius:10px;padding:10px 14px;margin-bottom:6px;box-shadow:0 1px 4px rgba(0,0,0,.04);display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                                <span class="insc-avatar" style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,{{ $esReal ? '#0d8574' : '#b3243a' }},{{ $esReal ? '#6cdb9c' : '#fb8c7a' }});color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">{{ $iniIns }}</span>
                                <div class="insc-info" style="flex:1;min-width:180px;">
                                    <div class="insc-name" style="font-size:.82rem;font-weight:600;color:#212529;">{{ $loop->iteration }}. {{ $r->apellidos }}, {{ $r->nombres }}</div>
                                    <div class="insc-meta" style="font-size:.67rem;color:#878a99;display:flex;flex-wrap:wrap;gap:2px 10px;margin-top:1px;">
                                        <span><i class="ri-passport-line"></i> {{ $r->ci }}</span>
                                        @if(!empty($r->asesor_nombres) || !empty($r->asesor_apellidos))
                                            <span><i class="ri-user-voice-line"></i> {{ $r->asesor_apellidos }}, {{ $r->asesor_nombres }}</span>
                                        @endif
                                        @if($r->fecha_inscripcion)
                                            <span><i class="ri-calendar-check-line"></i> {{ \Carbon\Carbon::parse($r->fecha_inscripcion)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                    <span class="plan-pill {{ $planCls }}" style="font-size:.65rem;padding:2px 8px;border-radius:5px;"><i class="{{ $planIcon }}" style="font-size:.6rem;"></i> {{ $plan }}</span>
                                    @if($r->es_traspaso)
                                        <span class="estado-pill" style="font-size:.65rem;padding:2px 8px;border-radius:5px;background:#fef2f2;color:#f06548;border:1px solid #fecaca;"><i class="ri-swap-line" style="font-size:.6rem;"></i> Traspaso</span>
                                    @endif
                                    <span class="estado-pill {{ $estCls }}" style="font-size:.65rem;padding:2px 8px;border-radius:5px;"><i class="{{ $estIcon }}" style="font-size:.6rem;"></i> {{ $estTxt }}</span>
                                    <span class="estado-pill" style="font-size:.65rem;padding:2px 8px;border-radius:5px;background:{{ $esReal ? '#ecfdf5' : '#fef2f2' }};color:{{ $esReal ? '#0d8574' : '#b3243a' }};border:1px solid {{ $esReal ? '#a7f3d0' : '#fecaca' }};">
                                        <i class="{{ $esReal ? 'ri-check-double-line' : 'ri-close-circle-line' }}" style="font-size:.6rem;"></i> {{ $esReal ? 'Real' : 'No Real' }}
                                    </span>
                                </div>
                                @if(!$esReal && ($matFalta > 0 || $colFalta > 0))
                                    <div style="width:100%;display:flex;flex-wrap:wrap;gap:6px;padding-top:4px;border-top:1px dashed #eef0f4;margin-top:2px;">
                                        @if($matFalta > 0)
                                            <span style="font-size:.62rem;color:#f06548;background:#fef2f2;padding:2px 8px;border-radius:4px;">
                                                <i class="ri-money-dollar-circle-line"></i> Matrícula: Bs {{ number_format($matFalta, 2, ',', '.') }}
                                            </span>
                                        @endif
                                        @if($colFalta > 0)
                                            <span style="font-size:.62rem;color:#f06548;background:#fef2f2;padding:2px 8px;border-radius:4px;">
                                                <i class="ri-money-dollar-circle-line"></i> {{ $colLabel }}: Bs {{ number_format($colFalta, 2, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div style="text-align:center;padding:2.5rem 1rem;color:#878a99;">
                                <i class="ri-inbox-2-line" style="font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.5;"></i>
                                <p class="mb-0" style="font-size:.88rem;">No hay inscritos para mostrar.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #eef0f4;padding:.75rem 1.25rem;background:#fff;">
                        <div style="font-size:.72rem;display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                            <span class="text-muted" style="font-weight:600;text-transform:uppercase;letter-spacing:.3px;font-size:.6rem;">Plan:</span>
                            @if($p->contado > 0)<span class="plan-pill plan-con" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-bank-card-line"></i> {{ $p->contado }} Contado</span>@endif
                            @if($p->regular > 0)<span class="plan-pill plan-reg" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-funds-line"></i> {{ $p->regular }} Regular</span>@endif
                            @if(isset($p->traspasos) && $p->traspasos > 0)<span style="font-size:.62rem;padding:2px 8px;border-radius:5px;background:#fef2f2;color:#f06548;border:1px solid #fecaca;"><i class="ri-swap-line"></i> {{ $p->traspasos }} Traspaso(s)</span>@endif
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
    @endcan
@endif
</div>{{-- /mkt-page --}}
@endsection

@can('asesores.registrar')
{{-- Modal Registrar Asesor --}}
<div class="modal fade" id="modalRegistrarAsesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;position:relative;overflow:hidden;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;">
                        <i class="ri-user-voice-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;">Registrar Asesor</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">Nuevo asesor de marketing</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="formRegistrarAsesor" novalidate>
                @csrf
                <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Nombres <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-user-line"></i></span>
                                <input type="text" name="nombres" id="f-nombres" class="form-control" required maxlength="255" placeholder="Nombres" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Apellidos <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-user-line"></i></span>
                                <input type="text" name="apellidos" id="f-apellidos" class="form-control" required maxlength="255" placeholder="Apellidos" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Cédula (CI) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-passport-line"></i></span>
                                <input type="text" name="ci" id="f-ci" class="form-control" required maxlength="10" placeholder="1234567890" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Sexo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-men-line"></i></span>
                                <select name="sexo" id="f-sexo" class="form-control" required style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                                    <option value="">Seleccionar...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Celular <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-phone-line"></i></span>
                                <input type="text" name="celular" id="f-celular" class="form-control" required maxlength="10" placeholder="0987654321" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Correo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-mail-line"></i></span>
                                <input type="email" name="correo" id="f-correo" class="form-control" required maxlength="255" placeholder="correo@ejemplo.com" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" id="btnSaveAsesor"
                        style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:600;padding:.5rem 1.25rem;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);transition:transform .15s ease;"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mesesLbl = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const data = @json($mesesData);

    // Gráfico de inscripciones por mes (estilo original)
    const el = document.getElementById('chartInscritosMes');
    if (el) {
        new ApexCharts(el, {
            chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: data.regulares },
                { name: 'Contado',   type: 'column', data: data.contado },
                { name: 'Traspasos', type: 'column', data: data.traspasos },
                { name: 'Total',     type: 'line',   data: data.total },
            ],
            xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#1f6fbf', '#0d8574', '#b3243a', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#b7791f'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false, columnWidth: '65%',
                    borderRadius: 5, borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true, enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#1f6fbf', '#0d8574', '#b3243a', '#163b6b'] },
                background: {
                    enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                    borderWidth: 1, borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -8, textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
        }).render();
    }

    // Gráfico de puntos por mes
    const elPuntos = document.getElementById('chartPuntosMes');
    if (elPuntos) {
        const tipoSeries = @json($puntosTipoSeries);
        if (tipoSeries.length > 0) {
            const stackedSeries = tipoSeries.map(s => ({ name: s.name, data: s.data, type: 'column' }));
            const totalData = Array.from({ length: 12 }, (_, i) =>
                tipoSeries.reduce((sum, s) => sum + (s.data[i] || 0), 0)
            );
            const allSeries = [...stackedSeries, { name: 'Total', data: totalData, type: 'line' }];
            const colorsArr = [...tipoSeries.map(s => s.color), '#163b6b'];
            new ApexCharts(elPuntos, {
                chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                series: allSeries,
                colors: colorsArr,
                xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Puntos', style: { fontSize: '11px', fontWeight: 600 } } },
                stroke: { width: [tipoSeries.map(() => 0), 3].flat(), curve: 'smooth' },
                markers: {
                    size: [tipoSeries.map(() => 0), 6].flat(),
                    colors: ['#fff'],
                    strokeColors: ['#163b6b'],
                    strokeWidth: 2,
                    hover: { size: 8 }
                },
                plotOptions: {
                    bar: { horizontal: false, columnWidth: '60%', dataLabels: { position: 'top' } }
                },
                dataLabels: {
                    enabled: true, enabledOnSeries: [allSeries.length - 1],
                    formatter: function (v) { return v > 0 ? v.toLocaleString('es-BO') : ''; },
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#163b6b'] },
                    background: {
                        enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                        borderWidth: 1, borderColor: '#eef0f4',
                        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                    },
                    offsetY: -8, textAnchor: 'middle'
                },
                legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
                grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v.toLocaleString('es-BO') + ' pts'; } } },
            }).render();
        } else {
            elPuntos.innerHTML = '<div class="text-muted text-center py-5"><i class="ri-inbox-2-line ri-2x mb-2 d-block"></i>Sin datos de puntos</div>';
        }
    }

    // Gráfico de Puntos Reales por mes
    const elPuntosReales = document.getElementById('chartPuntosRealesMes');
    if (elPuntosReales) {
        const tipoSeriesReales = @json($puntosRealesTipoSeries);
        if (tipoSeriesReales.length > 0) {
            const stackedSeriesReales = tipoSeriesReales.map(s => ({ name: s.name, data: s.data, type: 'column' }));
            const totalDataReales = Array.from({ length: 12 }, (_, i) =>
                tipoSeriesReales.reduce((sum, s) => sum + (s.data[i] || 0), 0)
            );
            const allSeriesReales = [...stackedSeriesReales, { name: 'Total', data: totalDataReales, type: 'line' }];
            const colorsArrReales = [...tipoSeriesReales.map(s => s.color), '#0d8574'];
            new ApexCharts(elPuntosReales, {
                chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                series: allSeriesReales,
                colors: colorsArrReales,
                xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Puntos Reales', style: { fontSize: '11px', fontWeight: 600 } } },
                stroke: { width: [tipoSeriesReales.map(() => 0), 3].flat(), curve: 'smooth' },
                markers: {
                    size: [tipoSeriesReales.map(() => 0), 6].flat(),
                    colors: ['#fff'],
                    strokeColors: ['#0d8574'],
                    strokeWidth: 2,
                    hover: { size: 8 }
                },
                plotOptions: {
                    bar: { horizontal: false, columnWidth: '60%', dataLabels: { position: 'top' } }
                },
                dataLabels: {
                    enabled: true, enabledOnSeries: [allSeriesReales.length - 1],
                    formatter: function (v) { return v > 0 ? v.toLocaleString('es-BO') : ''; },
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#0d8574'] },
                    background: {
                        enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                        borderWidth: 1, borderColor: '#eef0f4',
                        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                    },
                    offsetY: -8, textAnchor: 'middle'
                },
                legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
                grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v.toLocaleString('es-BO') + ' pts'; } } },
            }).render();
        } else {
            elPuntosReales.innerHTML = '<div class="text-muted text-center py-5"><i class="ri-inbox-2-line ri-2x mb-2 d-block"></i>Sin datos de puntos reales</div>';
        }
    }

    // Gráfico de Inscritos Reales vs No Reales por Tipo
    function lightenHex(hex, pct) {
        const r = parseInt(hex.slice(1,3), 16), g = parseInt(hex.slice(3,5), 16), b = parseInt(hex.slice(5,7), 16);
        return '#' + [r,g,b].map(c => Math.min(255, Math.round(c + (255 - c) * pct)).toString(16).padStart(2,'0')).join('');
    }
    const elReales = document.getElementById('chartRealesMes');
    if (elReales) {
        const seriesRealesTipo = @json($realesInscritosTipoSeries);
        const seriesNoRealesTipo = @json($noRealesInscritosTipoSeries);
        const totalInsc = @json($mesesData['total']);
        const realesGroup = seriesRealesTipo.map(s => ({ name: 'Reales - ' + s.name, data: s.data, type: 'column', group: 'Reales', color: s.color }));
        const noRealesGroup = seriesNoRealesTipo.map(s => ({ name: 'No Reales - ' + s.name, data: s.data, type: 'column', group: 'No Reales', color: lightenHex(s.color, 0.55) }));
        const allRealesSeries = [...realesGroup, ...noRealesGroup, { name: 'Total', type: 'line', data: totalInsc }];
        const totalIdx = allRealesSeries.length - 1;
        new ApexCharts(elReales, {
            chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
            series: allRealesSeries,
            xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            stroke: { width: [...realesGroup.map(() => 0), ...noRealesGroup.map(() => 0), 3], curve: 'smooth' },
            markers: {
                size: [...realesGroup.map(() => 0), ...noRealesGroup.map(() => 0), 6],
                colors: ['#fff'],
                strokeColors: ['#163b6b'],
                strokeWidth: 2,
                hover: { size: 8 }
            },
            plotOptions: {
                bar: {
                    horizontal: false, columnWidth: '60%',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true, enabledOnSeries: [totalIdx],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#163b6b'] },
                background: {
                    enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                    borderWidth: 1, borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -8, textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
        }).render();
    }

    // === Registrar Asesor ===
    const modalAsesor = new bootstrap.Modal(document.getElementById('modalRegistrarAsesor'));

    document.getElementById('btn-nuevo-asesor')?.addEventListener('click', function () {
        document.getElementById('formRegistrarAsesor').reset();
        document.getElementById('formRegistrarAsesor').querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.getElementById('formRegistrarAsesor').querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        modalAsesor.show();
    });

    document.getElementById('formRegistrarAsesor').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('btnSaveAsesor');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        fetch('{{ route("admin.marketing.asesores.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalAsesor.hide();
                location.reload();
            } else if (data.errors) {
                for (const field in data.errors) {
                    const input = document.getElementById('f-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const fb = input.closest('.col-md-6, .col-md-12').querySelector('.invalid-feedback');
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(() => alert('Error al registrar el asesor'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });
});
</script>
@endpush
