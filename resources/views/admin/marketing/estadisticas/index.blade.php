@extends('admin.layouts.master')

@section('title', 'Estadísticas Generales - Marketing - ' . config('app.name'))

@push('css')
<style>
canvas.chart-canvas { width: 100% !important; height: 220px !important; max-height: 220px; }
.chart-wrapper { position: relative; width: 100%; height: 220px; }
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
.mkt-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero > * { position: relative; z-index: 1; }
.mkt-hero .hero-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px); }
.mkt-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.mkt-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.mkt-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.mkt-hero .breadcrumb-item, .mkt-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.mkt-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.mkt-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

.stat-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; transition: transform .2s ease, box-shadow .2s ease; overflow: hidden; position: relative; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before { content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--stat-accent, #405189); }
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .stat-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: #878a99; font-weight: 700; margin-bottom: .4rem; }
.stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }
.stat-card .stat-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.25rem; }

.filter-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-header { background: transparent; border-bottom: 1px dashed #eef0f4; padding: 1rem 1.25rem; }
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: #2a2f34; }
.filter-card .form-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #878a99; margin-bottom: .35rem; }
.filter-card .form-select { border: 1px solid #e9ebec; background-color: #fff; font-weight: 500; transition: all .2s ease; }
.filter-card .form-select:focus { border-color: #405189; box-shadow: 0 0 0 .15rem rgba(64,81,137,.12); }
[data-layout-mode="dark"] .filter-card .form-select { background-color: #1d2125; border-color: #2a2f34; color: #ced4da; }

.chart-card { border: 1px solid #eef0f4; border-radius: 12px; }
[data-layout-mode="dark"] .chart-card { border-color: #2a2f34; }
.chart-title { text-align: center; margin-bottom: 1rem; padding-bottom: .75rem; border-bottom: 1px dashed #eef0f4; color: #495057; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; display: flex; align-items: center; justify-content: center; gap: .5rem; }
[data-layout-mode="dark"] .chart-title { color: #ced4da; border-bottom-color: #2a2f34; }
.chart-title i { font-size: 1rem; color: #405189; }

.table-card-wrap { border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; }
[data-layout-mode="dark"] .table-card-wrap { border-color: #2a2f34; }
.tabla-mkt { width: 100%; }
.tabla-mkt th, .tabla-mkt td { vertical-align: middle; padding: .65rem .6rem; }
.tabla-mkt thead th { background: #f8f9fc !important; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #495057; border-bottom: 2px solid #eef0f4; }
[data-layout-mode="dark"] .tabla-mkt thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }
.tabla-mkt tbody tr { border-bottom: 1px solid #f1f3f5; transition: background-color .15s ease; }
.tabla-mkt tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .tabla-mkt tbody tr { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .tabla-mkt tbody tr:hover { background-color: #1a1d20; }

/* Top Asesores sin scroll horizontal */
.tabla-top-asesores { table-layout: fixed; }
.tabla-top-asesores th, .tabla-top-asesores td { white-space: normal; overflow: hidden; }
.asesor-puntos { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }
.punto-chip { display: inline-flex; align-items: center; gap: 4px; background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d8ff; border-radius: 999px; padding: .1em .5em; font-size: .66rem; font-weight: 700; white-space: nowrap; }
.punto-chip .pc-val { color: #5b21b6; font-weight: 800; }
.punto-total { display: inline-flex; align-items: center; gap: 4px; background: #eef1f8; color: #405189; border: 1px solid #dbe2f0; border-radius: 999px; padding: .1em .55em; font-size: .68rem; font-weight: 800; white-space: nowrap; }
[data-layout-mode="dark"] .punto-total { background: #2a2f34; color: #c3cfe2; border-color: #3a4048; }

.results-card { border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .results-card { border-color: #2a2f34; }

/* Sidebar + Content layout for programas */
.programas-sidebar-layout { display: flex; gap: 1rem; align-items: flex-start; }
.programas-sidebar { flex-shrink: 0; width: 240px; }
.programas-content { flex: 1; min-width: 0; }
@media (max-width: 991.98px) { .programas-sidebar-layout { flex-direction: column; align-items: stretch; } .programas-sidebar { width: 100%; } }
.lista-programas { list-style: none; padding: 0; margin: 0; border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; }
.lista-programas li { border-bottom: 1px solid #f1f3f5; }
.lista-programas li:last-child { border-bottom: 0; }
.lista-programas a { display: flex; align-items: center; justify-content: space-between; padding: .6rem .85rem; color: #495057; font-size: .8rem; font-weight: 600; text-decoration: none; transition: all .15s ease; }
.lista-programas a:hover { background: #f8f9fc; color: #405189; }
.lista-programas a.active { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color: #fff; }
.lista-programas a .badge-count { font-size: .65rem; background: rgba(0,0,0,.08); color: inherit; padding: .15em .5em; border-radius: 999px; }
.lista-programas a.active .badge-count { background: rgba(255,255,255,.2); color: #fff; }

/* Asesor link */
.asesor-link { transition: all .15s ease; padding: .25rem .35rem; margin: -.25rem -.35rem; border-radius: 8px; }
.asesor-link:hover { background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); transform: translateX(2px); }
.asesor-link:hover .asesor-link-name { color: #405189 !important; }

/* Modal asesor */
.asesor-modal-header { background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%); color: #fff; border: 0; padding: 1.1rem 1.5rem; }
.asesor-modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
.asesor-modal-body { padding: 1.5rem; background: #fafbfd; }
[data-layout-mode="dark"] .asesor-modal-body { background: #1a1d20; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="mkt-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-bar-chart-2-line"></i></div>
                <div>
                    <h4>Estadísticas de Inscripciones</h4>
                    <p class="hero-sub">{{ $mes === 'todos' ? 'Todos los meses' : ($meses[$mes] ?? $mes) }} del {{ $gestion }} · {{ match($tipo) { 'general' => 'General', 'upi' => 'Solo UPI Diplomados', 'upi-auto' => 'UPI Autogestionados', default => 'Programas Sucre central' } }}</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item active">Estadísticas</li>
            </ol>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col">
        <div class="stat-card h-100" style="--stat-accent: #405189;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Total Inscripciones</p>
                        <h4 class="stat-value mb-0" id="pv-stat-inscritos">{{ number_format($resumen['total_inscritos']) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-team-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card h-100" style="--stat-accent: #0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Programas</p>
                        <h4 class="stat-value mb-0" id="pv-stat-programas">{{ $resumen['total_programas'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-booklet-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card h-100" style="--stat-accent: #299cdb;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Áreas</p>
                        <h4 class="stat-value mb-0" id="pv-stat-areas">{{ $resumen['total_areas'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-info text-info"><i class="ri-apps-2-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card h-100" style="--stat-accent: #f7b84b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Asesores</p>
                        <h4 class="stat-value mb-0" id="pv-stat-asesores">{{ $resumen['total_asesores'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-user-star-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card h-100" style="--stat-accent: #f06548;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Traspasos</p>
                        <h4 class="stat-value mb-0" id="pv-stat-traspasos">{{ number_format($resumen['total_traspasos']) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-danger text-danger"><i class="ri-swap-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-equalizer-line text-primary"></i>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros</h5>
                </div>
            </div>
            <div class="card-body">
                <form id="formFiltros" method="GET" action="{{ route('admin.marketing.estadisticas') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Gestión</label>
                        <select name="gestion" class="form-select">
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" @selected((string)$gestion === (string)$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mes</label>
                        <select name="mes" class="form-select">
                            <option value="todos" @selected($mes === 'todos')>Todos los meses</option>
                            @foreach($meses as $k => $v)
                                <option value="{{ $k }}" @selected((string)$mes === (string)$k && $mes !== 'todos')>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="general" @selected($tipo === 'general')>General</option>
                            <option value="upi" @selected($tipo === 'upi')>Solo UPI Diplomados</option>
                            <option value="upi-auto" @selected($tipo === 'upi-auto')>UPI Autogestionados</option>
                            <option value="otros" @selected($tipo === 'otros')>Programas Sucre central</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1" id="btnFiltrar">
                            <i class="ri-filter-3-line"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.marketing.estadisticas') }}" class="btn btn-soft-danger w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="ri-refresh-line"></i> Mes Actual
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Chart Inscripciones por Mes (Gestión) --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-bar-chart-grouped-line"></i>Inscripciones por Mes - {{ $gestion }}</h6>
                <div id="chart-meses" style="height:340px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Charts: distribución --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card chart-card h-100 mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-pie-chart-2-line"></i>Inscripciones por Área</h6>
                <div class="chart-wrapper"><canvas id="chart-area" class="chart-canvas"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card chart-card h-100 mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-price-tag-3-line"></i>Inscripciones por Tipo</h6>
                <div class="chart-wrapper"><canvas id="chart-tipo" class="chart-canvas"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- Inscripciones por Programa --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <h6 class="chart-title mb-0"><i class="ri-booklet-line"></i>Inscripciones por Programa</h6>
                    <span class="badge bg-soft-primary text-primary" id="chart-programa-count">—</span>
                </div>
                <div id="chart-programa-wrap" style="height:360px;"><canvas id="chart-programa" class="chart-canvas"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- Top Asesores --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card results-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fs-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                        <i class="ri-trophy-line text-warning"></i> Top Asesores
                    </h5>
                    <span class="badge bg-soft-primary fs-11 px-2 py-1" id="top-asesores-count">{{ $topAsesores->count() + ($sinAsesor->total > 0 ? 1 : 0) }} asesores</span>
                </div>
                <div class="table-card-wrap">
                    <div class="table-responsive">
                        <table class="table mb-0 tabla-mkt tabla-top-asesores">
                            <thead id="top-asesores-head">
                                <tr>
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th>Asesor</th>
                                    <th style="width: 88px;" class="text-center" title="Total de inscripciones del período">Total</th>
                                    <th style="width: 82px;" class="text-center" title="Inscripciones vigentes">Activas</th>
                                    <th style="width: 88px;" class="text-center" title="Inscripciones retiradas">Retiradas</th>
                                    <th style="min-width:220px;" title="Puntos por tipo y total">Puntos por tipo</th>
                                    <th style="width: 90px;" class="text-center">Detalle</th>
                                </tr>
                            </thead>
                            <tbody id="top-asesores-body">
                                @if($topAsesores->count() > 0 || $sinAsesor->total > 0)
                                @foreach($topAsesores as $idx => $a)
                                @php
                                    $medal = $idx === 0 ? '🥇' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ''));
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold fs-5">{{ $medal ?: ($idx + 1) }}</td>
                                    <td>
                                        @can('asesores.vercompleto')
                                        <a href="{{ route('admin.marketing.asesor-show', $a->id) }}?gestion={{ $gestion }}&mes={{ $mes }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset asesor-link" title="Ver detalle completo">
                                        @endcan
                                            <span class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title rounded-circle fs-11 fw-bold" style="background: {{ $idx === 0 ? '#fff8e1' : ($idx === 1 ? '#f1f3f5' : ($idx === 2 ? '#fff3e0' : '#f1f3f5')) }}; color: {{ $idx === 0 ? '#b7791f' : ($idx === 1 ? '#878a99' : ($idx === 2 ? '#cd7f32' : '#878a99')) }};">
                                                    {{ strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1)) }}
                                                </span>
                                            </span>
                                            <div>
                                                <div class="fw-semibold text-dark asesor-link-name" style="font-size:.85rem;">{{ $a->apellidos }}, {{ $a->nombres }}</div>
                                                @can('asesores.vercompleto')
                                                <div class="text-muted" style="font-size:.7rem;"><i class="ri-external-link-line"></i> Ver detalle completo</div>
                                                @endcan
                                            </div>
                                        @can('asesores.vercompleto')
                                        </a>
                                        @endcan
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary fs-11 px-2 py-1 fw-bold">{{ number_format($a->total) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success fs-11 px-2 py-1">{{ number_format($a->activos) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">{{ number_format($a->retirados) }}</span>
                                    </td>
                                    <td>
                                        <div class="asesor-puntos">
                                            @foreach($tiposEncontrados as $tipo)
                                                @php $col = 'puntos_' . \Illuminate\Support\Str::slug($tipo, '_'); $val = (float) ($a->$col ?? 0); @endphp
                                                @if($val > 0)
                                                <span class="punto-chip"><span>{{ $tipo }}</span><span class="pc-val">{{ number_format($val, 1, ',', '.') }}</span></span>
                                                @endif
                                            @endforeach
                                            <span class="punto-total"><i class="ri-star-line"></i> {{ number_format($a->total_puntos ?? 0, 1, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @can('asesores.verdetalle')
                                            <button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-asesor" data-asesor-id="{{ $a->id }}" data-asesor-nombre="{{ $a->apellidos }}, {{ $a->nombres }}" title="Vista rápida">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @endcan
                                            @can('asesores.vercompleto')
                                            <a href="{{ route('admin.marketing.asesor-show', $a->id) }}?gestion={{ $gestion }}&mes={{ $mes }}" class="btn btn-sm btn-icon btn-soft-info" title="Ver detalle completo">
                                                <i class="ri-arrow-right-circle-line"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if($sinAsesor->total > 0)
                                <tr style="background:#fff8e1;">
                                    <td class="text-center text-muted fw-bold">—</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title rounded-circle fs-11 fw-bold" style="background:#f1f3f5;color:#878a99;">
                                                    <i class="ri-user-unfollow-line"></i>
                                                </span>
                                            </span>
                                            <div>
                                                <div class="fw-semibold" style="font-size:.85rem;color:#878a99;">Sin asesor</div>
                                                <div class="text-muted" style="font-size:.7rem;"><i class="ri-information-line"></i> Inscripciones sin asesor asignado</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1 fw-bold">{{ number_format($sinAsesor->total) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success fs-11 px-2 py-1">{{ number_format($sinAsesor->activos) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">{{ number_format($sinAsesor->retirados) }}</span>
                                    </td>
                                    <td><span class="text-muted" style="font-size:.7rem;">—</span></td>
                                    <td class="text-center"><span class="text-muted" style="font-size:.7rem;">—</span></td>
                                </tr>
                                @endif
                                @else
                                <tr><td colspan="7" class="text-center text-muted py-3"><i class="ri-inbox-2-line me-1"></i>No hay inscripciones en este período</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Programas con inscritos (sidebar + content layout) --}}
@if(count($inscripcionesPorPrograma) > 0)
<div class="row">
    <div class="col-12">
        <div class="card results-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fs-14 fw-semibold mb-0 d-flex align-items-center gap-2">
                        <i class="ri-booklet-line text-primary"></i> Inscripciones por Programa
                    </h5>
                    <span class="badge bg-soft-primary fs-11 px-2 py-1">{{ count($inscripcionesPorPrograma) }} programas</span>
                </div>

                <div class="programas-sidebar-layout">
                    <div class="programas-sidebar">
                        <ul class="lista-programas">
                            @php $firstPid = null; @endphp
                            @foreach($inscripcionesPorPrograma as $pid => $data)
                            @php if ($firstPid === null) $firstPid = $pid; @endphp
                            <li>
                                <a href="#" class="programa-tab-link @if($loop->first) active @endif" data-programa-id="{{ $pid }}">
                                    {{ \Illuminate\Support\Str::limit($data['programa']->nombre, 24) }}
                                    <span class="badge-count">{{ $data['total'] }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="programas-content">
                        @foreach($inscripcionesPorPrograma as $pid => $data)
                        <div class="programa-content-pane @if($pid !== $firstPid) d-none @endif" id="prog-content-{{ $pid }}">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold text-dark fs-14">{{ $data['programa']->nombre }}</span>
                                    <span class="text-muted ms-2 fs-12"><i class="ri-hashtag"></i> {{ $data['programa']->codigo }}</span>
                                </div>
                                <span class="badge bg-soft-primary fs-11 px-2 py-1">{{ $data['total'] }} inscrito{{ $data['total'] !== 1 ? 's' : '' }}</span>
                            </div>
                            <div class="table-card-wrap">
                                <div class="table-responsive">
                                    <table class="table mb-0 tabla-mkt">
                                        <thead>
                                            <tr>
                                                <th style="width: 36px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 90px;" class="text-center">CI</th>
                                                <th style="width: 70px;" class="text-center">Sexo</th>
                                                <th style="width: 90px;" class="text-center">Fecha</th>
                                                <th style="width: 70px;" class="text-center">Pago</th>
                                                <th style="width: 80px;" class="text-center">Traspaso</th>
                                                <th style="width: 170px;">Asesor</th>
                                                <th style="width: 65px;" class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['inscripciones'] as $idx2 => $ins)
                                            <tr>
                                                <td class="text-center text-muted fw-semibold">{{ $idx2 + 1 }}</td>
                                                <td class="fw-semibold text-dark" style="font-size:.85rem;">{{ $ins->apellidos }}, {{ $ins->nombres }}</td>
                                                <td class="text-center text-muted" style="font-size:.82rem;">{{ $ins->ci }}</td>
                                                <td class="text-center">
                                                    <span class="d-inline-block px-2 py-1 rounded-3 fw-semibold fs-10" style="background:{{ strtoupper(substr($ins->sexo ?? '', 0, 1)) === 'M' ? '#dbeafe' : '#fce7f3' }}; color:{{ strtoupper(substr($ins->sexo ?? '', 0, 1)) === 'M' ? '#3b82f6' : '#ec4899' }};">
                                                        {{ $ins->sexo }}
                                                    </span>
                                                </td>
                                                <td class="text-center text-muted" style="font-size:.78rem;">{{ \Carbon\Carbon::parse($ins->fecha_inscripcion)->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-{{ $ins->contado ? 'success' : 'info' }} text-{{ $ins->contado ? 'success' : 'info' }} fs-10 px-2 py-1">{{ $ins->contado ? 'Contado' : 'Regular' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($ins->es_traspaso)
                                                        <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1"><i class="ri-swap-line"></i> Traspaso</span>
                                                    @else
                                                        <span class="text-muted" style="font-size:.7rem;">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ins->asesor_id)
                                                        <span class="fw-semibold" style="font-size:.82rem; color:#495057;">
                                                            <i class="ri-user-star-line text-warning me-1"></i>{{ $ins->asesor_apellidos }}, {{ $ins->asesor_nombres }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted fst-italic" style="font-size:.78rem;">Sin asesor</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($ins->estado)
                                                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Retirado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card results-card">
            <div class="card-body text-center py-5">
                <i class="ri-inbox-2-line ri-2x text-muted d-block mb-2"></i>
                <p class="text-muted mb-0">No hay inscripciones para {{ $mes === 'todos' ? 'el' : ($meses[$mes] ?? $mes) }} {{ $mes === 'todos' ? $gestion : ('del ' . $gestion) }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@can('asesores.verdetalle')
{{-- Modal Detalle Asesor --}}
<div class="modal fade" id="modalAsesorDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header asesor-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-xs flex-shrink-0">
                        <span class="avatar-title bg-white bg-opacity-25 text-white rounded-circle fs-14 fw-bold">
                            <i class="ri-user-star-line"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-bold" id="modalAsesorTitle"></h5>
                        <small class="text-white text-opacity-75 fs-12">Inscripciones agrupadas por programa</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body asesor-modal-body" id="modalAsesorBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted mb-0 fs-13">Cargando inscripciones...</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endcan
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/chart.js/chart.umd.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chartMesesInstance = null;
    var chartAreaInstance = null;
    var chartTipoInstance = null;
    var chartProgramaInstance = null;

    function randomColors(count) {
        const palette = ['#163b6b','#cba135','#1f6fbf','#0d8574','#b7791f','#7d9ec8','#b3243a','#2a5d97','#4c7bb0','#ddb84d','#8fbbe6','#e0a94a'];
        return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
    }

    // Custom plugin: muestra números en los gráficos
    const valueLabelsPlugin = {
        id: 'valueLabels',
        afterDraw(chart) {
            const { ctx, data } = chart;
            const type = chart.config.type;
            const ds = data.datasets[0];
            if (!ds || !ds.data.length) return;
            ctx.save();
            ctx.font = '10px Manrope, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            if (type === 'bar' && chart.config.options?.indexAxis !== 'y') {
                const meta = chart.getDatasetMeta(0);
                meta.data.forEach((bar, i) => {
                    const val = ds.data[i];
                    if (val === 0 || val === null || val === undefined) return;
                    ctx.fillStyle = '#495057';
                    ctx.textBaseline = 'bottom';
                    ctx.fillText(val, bar.x, bar.y - 3);
                });
            }

            if (type === 'bar' && chart.config.options?.indexAxis === 'y') {
                const meta = chart.getDatasetMeta(0);
                meta.data.forEach((bar, i) => {
                    const val = ds.data[i];
                    if (val === 0 || val === null || val === undefined) return;
                    ctx.fillStyle = '#495057';
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(val, bar.x + 4, bar.y);
                });
            }

            if (type === 'doughnut') {
                const meta = chart.getDatasetMeta(0);
                meta.data.forEach(function (arc, i) {
                    const val = ds.data[i];
                    if (val === 0 || val === null || val === undefined) return;
                    const midAngle = (arc.startAngle + arc.endAngle) / 2;
                    const radius = (arc.innerRadius + arc.outerRadius) / 2;
                    const x = arc.x + Math.cos(midAngle) * radius;
                    const y = arc.y + Math.sin(midAngle) * radius;
                    ctx.fillStyle = '#fff';
                    ctx.font = 'bold 11px Manrope, system-ui, sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(val, x, y);
                });
            }

            ctx.restore();
        }
    };

    Chart.register(valueLabelsPlugin);

    Chart.defaults.font.family = 'Manrope, system-ui, -apple-system, "Segoe UI", sans-serif';
    Chart.defaults.color = '#667085';
    Chart.defaults.borderColor = 'rgba(102, 112, 133, 0.14)';

    function renderChartMeses(c) {
        const el = document.getElementById('chart-meses');
        if (!el) return;
        if (chartMesesInstance) { chartMesesInstance.destroy(); chartMesesInstance = null; }
        const mesesLbl = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        chartMesesInstance = new ApexCharts(el, {
            chart: { height: 340, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares || [] },
                { name: 'Contado',   type: 'column', data: c.contado || [] },
                { name: 'Traspasos', type: 'column', data: c.traspasos || [] },
                { name: 'Total',     type: 'line',   data: c.total || [] },
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
        });
        chartMesesInstance.render();
    }

    var chartParents = {};

    function saveParent(id) {
        if (!chartParents[id]) {
            var el = document.getElementById(id);
            if (el) chartParents[id] = el.parentElement;
        }
    }

    function ensureCanvas(id) {
        saveParent(id);
        var parent = chartParents[id];
        if (!parent) return null;
        var existing = document.getElementById(id);
        if (existing) return existing;
        parent.innerHTML = '<canvas id="' + id + '" class="chart-canvas"></canvas>';
        return document.getElementById(id);
    }

    function showNoData(id) {
        saveParent(id);
        var parent = chartParents[id];
        if (parent) parent.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>Sin datos</div>';
    }

    function renderChartArea(data) {
        if (chartAreaInstance) { chartAreaInstance.destroy(); chartAreaInstance = null; }
        var labels = (data || []).map(function (d) { return d.nombre; });
        var values = (data || []).map(function (d) { return d.total; });
        if (!labels.length) { showNoData('chart-area'); return; }
        var canvas = ensureCanvas('chart-area');
        if (!canvas) return;
        var colors = data.map(function (d) { return d.color; });
        chartAreaInstance = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: values, backgroundColor: colors.length ? colors : randomColors(labels.length), borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } } } }
        });
    }

    function renderChartTipo(data) {
        if (chartTipoInstance) { chartTipoInstance.destroy(); chartTipoInstance = null; }
        var labels = (data || []).map(function (d) { return d.nombre; });
        var values = (data || []).map(function (d) { return d.total; });
        if (!labels.length) { showNoData('chart-tipo'); return; }
        var canvas = ensureCanvas('chart-tipo');
        if (!canvas) return;
        chartTipoInstance = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: values, backgroundColor: randomColors(labels.length), borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } } } }
        });
    }

    function renderChartPrograma(data) {
        if (chartProgramaInstance) { chartProgramaInstance.destroy(); chartProgramaInstance = null; }
        var labels = (data || []).map(function (d) { return d.nombre; });
        var values = (data || []).map(function (d) { return d.total; });
        var badge = document.getElementById('chart-programa-count');
        if (badge) badge.textContent = labels.length + ' programa' + (labels.length === 1 ? '' : 's');
        if (!labels.length) { showNoData('chart-programa'); return; }
        var wrap = document.getElementById('chart-programa-wrap');
        if (wrap) wrap.style.height = Math.max(300, labels.length * 30 + 70) + 'px';
        var canvas = ensureCanvas('chart-programa');
        var palette = ['#405189','#0a6a90','#0ab39c','#f7b84b','#8b5cf6','#299cdb','#b54708','#be123c','#163b6b','#4c7bb0'];
        chartProgramaInstance = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: labels.map(function (_, i) { return palette[i % palette.length]; }),
                    borderRadius: 7,
                    borderSkipped: false,
                    barThickness: 18
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return ' ' + c.parsed.x + ' inscrito(s)'; } } }
                },
                scales: {
                    x: { grid: { color: '#eef1f8' }, border: { display: false }, ticks: { font: { size: 10 }, precision: 0 } },
                    y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10, weight: '600' }, color: '#475467' } }
                }
            }
        });
    }

    function renderTopAsesores(data, sinAsesor, tipos) {
        const el = document.getElementById('top-asesores-body');
        const gestion = document.querySelector('[name="gestion"]').value;
        const mes = document.querySelector('[name="mes"]').value;
        if (!el) return;
        var cols = 7;
        if (!data || !data.length) {
            el.innerHTML = '<tr><td colspan="' + cols + '" class="text-center text-muted py-3"><i class="ri-inbox-2-line me-1"></i>No hay inscripciones en este per\u00edodo</td></tr>';
            document.getElementById('top-asesores-count').textContent = '0';
            return;
        }
        var head = document.getElementById('top-asesores-head');
        if (head) {
            var headHtml = '<tr>' +
                '<th style="width:44px" class="text-center">#</th>' +
                '<th>Asesor</th>' +
                '<th style="width:88px" class="text-center">Total</th>' +
                '<th style="width:82px" class="text-center">Activas</th>' +
                '<th style="width:88px" class="text-center">Retiradas</th>' +
                '<th style="min-width:220px">Puntos por tipo</th>' +
                '<th style="width:90px" class="text-center">Detalle</th></tr>';
            head.innerHTML = headHtml;
        }
        var html = '';
        data.forEach(function (a, idx) {
            var medal = idx === 0 ? '\u{1F947}' : (idx === 1 ? '\u{1F948}' : (idx === 2 ? '\u{1F949}' : ''));
            var initials = (a.nombres && a.apellidos ? (a.nombres.charAt(0) + a.apellidos.charAt(0)).toUpperCase() : '??');
            var nombreCompleto = (a.apellidos || '') + ', ' + (a.nombres || '');
            var bgColor = idx === 0 ? '#fff8e1' : (idx === 1 ? '#f1f3f5' : (idx === 2 ? '#fff3e0' : '#f1f3f5'));
            var txtColor = idx === 0 ? '#b7791f' : (idx === 1 ? '#878a99' : (idx === 2 ? '#cd7f32' : '#878a99'));
            var detalleUrl = '{{ route("admin.marketing.asesor-show", "ASESOR_ID") }}?gestion=' + gestion + '&mes=' + mes;

            // Chips de puntos por tipo + total
            var chips = '';
            if (tipos) {
                tipos.forEach(function (t) {
                    var col = 'puntos_' + t.replace(/[^a-zA-Z0-9]+/g, '_').replace(/_+$/, '').replace(/^_/, '').toLowerCase();
                    var val = Number(a[col] || 0);
                    if (val > 0) {
                        chips += '<span class="punto-chip"><span>' + t + '</span><span class="pc-val">' + val.toFixed(1).replace('.', ',') + '</span></span>';
                    }
                });
            }
            var totalPts = Number(a.total_puntos || 0).toFixed(1).replace('.', ',');
            var puntosCell = '<td><div class="asesor-puntos">' + chips + '<span class="punto-total"><i class="ri-star-line"></i> ' + totalPts + '</span></div></td>';

            html += '<tr>' +
                '<td class="text-center fw-bold fs-5">' + (medal || (idx + 1)) + '</td>' +
                '<td>' +
                    '<a href="' + detalleUrl.replace('ASESOR_ID', a.id) + '" class="d-flex align-items-center gap-2 text-decoration-none text-reset asesor-link" title="Ver detalle completo">' +
                        '<span class="avatar-xs flex-shrink-0">' +
                            '<span class="avatar-title rounded-circle fs-11 fw-bold" style="background:' + bgColor + ';color:' + txtColor + ';">' + initials + '</span>' +
                        '</span>' +
                        '<div>' +
                            '<div class="fw-semibold text-dark asesor-link-name" style="font-size:.85rem;">' + nombreCompleto + '</div>' +
                            '<div class="text-muted" style="font-size:.7rem;"><i class="ri-external-link-line"></i> Ver detalle completo</div>' +
                        '</div>' +
                    '</a>' +
                '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11 px-2 py-1 fw-bold">' + (a.total || 0) + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-success text-success fs-11 px-2 py-1">' + (a.activos || 0) + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">' + (a.retirados || 0) + '</span></td>' +
                puntosCell +
                '<td class="text-center">' +
                    '<div class="d-flex gap-1 justify-content-center">' +
                        '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-asesor" data-asesor-id="' + a.id + '" data-asesor-nombre="' + nombreCompleto + '" title="Vista rápida">' +
                            '<i class="ri-eye-line"></i>' +
                        '</button>' +
                        '<a href="' + detalleUrl.replace('ASESOR_ID', a.id) + '" class="btn btn-sm btn-icon btn-soft-info" title="Ver detalle completo">' +
                            '<i class="ri-arrow-right-circle-line"></i>' +
                        '</a>' +
                    '</div>' +
                '</td>' +
            '</tr>';
        });
        if (sinAsesor && sinAsesor.total > 0) {
            html += '<tr style="background:#fff8e1;">' +
                '<td class="text-center text-muted fw-bold">\u2014</td>' +
                '<td>' +
                    '<div class="d-flex align-items-center gap-2">' +
                        '<span class="avatar-xs flex-shrink-0"><span class="avatar-title rounded-circle fs-11 fw-bold" style="background:#f1f3f5;color:#878a99;"><i class="ri-user-unfollow-line"></i></span></span>' +
                        '<div><div class="fw-semibold" style="font-size:.85rem;color:#878a99;">Sin asesor</div></div>' +
                    '</div>' +
                '</td>' +
                '<td class="text-center"><span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1 fw-bold">' + (sinAsesor.total || 0) + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-success text-success fs-11 px-2 py-1">' + (sinAsesor.activos || 0) + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">' + (sinAsesor.retirados || 0) + '</span></td>' +
                '<td><span class="text-muted" style="font-size:.7rem;">\u2014</span></td>' +
                '<td class="text-center"><span class="text-muted" style="font-size:.7rem;">\u2014</span></td>' +
            '</tr>';
        }
        el.innerHTML = html;
        document.getElementById('top-asesores-count').textContent = data.length + (sinAsesor && sinAsesor.total > 0 ? 1 : 0);
    }

    function loadData(gestion, mes) {
        var tipo = document.querySelector('[name="tipo"]').value;
        var url = '{{ route("admin.marketing.estadisticas") }}?gestion=' + gestion + '&mes=' + mes + '&tipo=' + tipo;
        var btn = document.getElementById('btnFiltrar');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...'; }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var r = data.resumen || {};
            document.getElementById('pv-stat-inscritos').textContent = (r.total_inscritos || 0).toLocaleString();
            document.getElementById('pv-stat-programas').textContent = r.total_programas || 0;
            document.getElementById('pv-stat-areas').textContent = r.total_areas || 0;
            document.getElementById('pv-stat-asesores').textContent = r.total_asesores || 0;
            document.getElementById('pv-stat-traspasos').textContent = (r.total_traspasos || 0).toLocaleString();

            renderChartMeses(data.chart_meses || {});
            renderChartArea(data.por_area || []);
            renderChartTipo(data.por_tipo || []);
            renderChartPrograma(data.por_programa || []);
            renderTopAsesores(data.top_asesores || [], data.sin_asesor || {}, data.tipos_encontrados || []);
        })
        .catch(function () {
            console.error('Error al cargar datos');
        })
        .finally(function () {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-filter-3-line"></i> Filtrar'; }
        });
    }

    // Cargar datos iniciales
    renderChartArea(@json($porArea));
    renderChartTipo(@json($porTipo));
    renderChartPrograma(@json($porPrograma));
    renderChartMeses(@json($chartMeses));

    // Form submit via AJAX
    document.getElementById('formFiltros').addEventListener('submit', function (e) {
        e.preventDefault();
        var gestion = this.querySelector('[name="gestion"]').value;
        var mes = this.querySelector('[name="mes"]').value;
        loadData(gestion, mes);
    });

    // ---- Tabs programas: sidebar click ----
    document.querySelectorAll('.programa-tab-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.programa-tab-link').forEach(function (l) { l.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.programa-content-pane').forEach(function (p) { p.classList.add('d-none'); });
            const pane = document.getElementById('prog-content-' + this.dataset.programaId);
            if (pane) pane.classList.remove('d-none');
        });
    });

    // ---- Modal detalle asesor ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-asesor');
        if (!btn) return;

        const asesorId = btn.dataset.asesorId;
        const asesorNombre = btn.dataset.asesorNombre;
        document.getElementById('modalAsesorTitle').textContent = asesorNombre;
        document.getElementById('modalAsesorBody').innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 text-muted mb-0 fs-13">Cargando inscripciones...</p></div>';

        new bootstrap.Modal(document.getElementById('modalAsesorDetalle')).show();

        var filtroGestion = document.querySelector('[name="gestion"]').value;
        var filtroMes = document.querySelector('[name="mes"]').value;
        fetch('{{ route("admin.marketing.asesor-detalle") }}?asesor_id=' + asesorId + '&gestion=' + filtroGestion + '&mes=' + filtroMes, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                document.getElementById('modalAsesorBody').innerHTML = data.html;
            } else {
                document.getElementById('modalAsesorBody').innerHTML = '<p class="text-danger text-center py-3 mb-0">' + (data.message || 'Error al cargar') + '</p>';
            }
        })
        .catch(function () {
            document.getElementById('modalAsesorBody').innerHTML = '<p class="text-danger text-center py-3 mb-0">Error de conexión</p>';
        });
    });
});
</script>
@endpush
