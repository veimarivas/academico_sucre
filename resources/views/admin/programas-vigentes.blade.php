@extends('admin.layouts.master')

@section('title', 'Programas Vigentes - ' . config('app.name'))

@push('css')
<style>
/* ── Stat cards ── */
.pv-stat-card {
    --stat-accent: var(--ui-accent);
    background: var(--ui-surface);
    border: 1px solid var(--ui-line);
    border-radius: var(--ui-radius);
    box-shadow: var(--ui-shadow);
    transition: transform .2s ease, box-shadow .2s ease;
}
.pv-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--ui-shadow-hover);
}
.pv-stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    background: color-mix(in srgb, var(--stat-accent) 12%, transparent);
    color: var(--stat-accent);
}
.pv-stat-label {
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--ui-muted); margin-bottom: .2rem;
}
.pv-stat-value {
    font-weight: 800; font-size: 1.35rem;
    color: var(--ui-ink); line-height: 1;
    font-variant-numeric: tabular-nums;
}

/* ── Main tabs ── */
.pv-main-tabs {
    display: inline-flex;
    background: var(--ui-surface);
    border: 1px solid var(--ui-line);
    border-radius: 14px;
    padding: 5px;
    gap: 4px;
    box-shadow: var(--ui-shadow);
}
.pv-main-tabs .nav-link {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1rem;
    border-radius: 10px;
    font-size: .82rem; font-weight: 600;
    color: var(--ui-muted);
    transition: all .2s ease;
    border: 1px solid transparent;
    white-space: nowrap;
}
.pv-main-tabs .nav-link i {
    font-size: 1rem; opacity: .7;
    transition: opacity .2s ease;
}
.pv-main-tabs .nav-link:hover {
    color: var(--ui-accent);
    background: var(--ui-surface-2);
}
.pv-main-tabs .nav-link:hover i { opacity: 1; }
.pv-main-tabs .nav-link.active {
    background: var(--ui-brand);
    color: #fff;
    border-color: var(--ui-brand);
    box-shadow: 0 2px 8px -2px rgba(22, 59, 107, .45);
}
.pv-main-tabs .nav-link.active i { opacity: 1; }

/* ── Charts ── */
.pv-chart-wrap {
    padding: .5rem .25rem .25rem;
}
.pv-chart { position: relative; min-height: 320px; }
.pv-chart-sm { min-height: 260px; }
.pv-chart-card .card-body { padding: 1rem 1.25rem 1.25rem; }

/* ── Tab summary bar ── */
.pv-summary-bar {
    display: flex; align-items: center; gap: .65rem; flex-wrap: wrap;
    padding: .7rem 1.25rem;
    background: var(--ui-surface-2);
    border-bottom: 1px solid var(--ui-line);
}
.pv-chip {
    display: inline-flex; align-items: center; gap: .4rem;
    background: var(--ui-surface);
    border: 1px solid var(--ui-line);
    border-radius: 999px;
    padding: .35em .8em;
    font-size: .75rem; font-weight: 600;
    color: var(--ui-ink-2);
}
.pv-chip strong {
    font-weight: 800; font-size: .85rem;
    color: var(--ui-ink);
}
.pv-dot {
    width: 7px; height: 7px;
    border-radius: 50%; display: inline-block; flex-shrink: 0;
}

/* ── Group headers ── */
.pv-group-header {
    display: flex; align-items: center; gap: .75rem;
    padding: .65rem 1.25rem;
    background: var(--ui-surface-2);
    border-bottom: 1px solid var(--ui-line);
    border-top: 1px solid var(--ui-line);
}
.pv-group-header:first-child { border-top: 0; }
.pv-gest-badge {
    background: var(--ui-brand);
    color: #fff;
    font-size: .72rem; font-weight: 700;
    padding: .3em .75em; border-radius: 8px;
    min-width: 42px; text-align: center;
}
.pv-gest-info {
    font-size: .76rem; color: var(--ui-ink-2); font-weight: 600;
    display: flex; align-items: center; gap: .75rem;
}
.pv-gest-info strong { font-size: .88rem; color: var(--ui-ink); }

/* ── Program table ── */
.pv-table { table-layout: fixed; width: 100%; margin-bottom: 0; }
.pv-table th {
    font-size: .66rem; font-weight: 700;
    letter-spacing: .05em; text-transform: uppercase;
    color: var(--ui-accent);
    background: var(--ui-header-tint);
    border-bottom: 1px solid var(--ui-line);
    padding: .65rem .55rem;
}
.pv-table td {
    padding: .6rem .55rem; vertical-align: middle;
    border-bottom: 1px solid var(--ui-line);
    font-size: .83rem; color: var(--ui-ink-2);
}
.pv-table tbody tr { transition: background-color .15s ease; }
.pv-table tbody tr:hover { background-color: var(--ui-surface-2); }
.pv-table .pv-col-name { overflow: hidden; }
.pv-table .pv-col-name a {
    max-width: 100%; color: var(--ui-ink);
    text-decoration: none; font-weight: 500;
    transition: color .15s ease;
}
.pv-table .pv-col-name a:hover { color: var(--ui-accent); }
.pv-table .pv-col-codigo .badge {
    display: inline-block; max-width: 100%;
    overflow: hidden; text-overflow: ellipsis; vertical-align: middle;
}
.pv-table .pv-num {
    white-space: nowrap; font-variant-numeric: tabular-nums;
    font-weight: 600;
}
.pv-hombres { color: var(--ui-accent); }
.pv-mujeres { color: #d1458a; }
.pv-idx { color: var(--ui-muted); }

/* ── Badges de estado ── */
.pv-badge-estado {
    font-size: .66rem; font-weight: 700;
    padding: .3em .7em; border-radius: 999px;
}
.pv-badge-inscripciones {
    background: var(--ui-info-soft); color: var(--ui-info);
}
.pv-badge-desarrollo {
    background: var(--ui-success-soft); color: var(--ui-success);
}

/* ── Inner tabs (Desarrollo / Inscripciones) ── */
.pv-inner-tabs {
    background: var(--ui-surface);
    border-bottom: 1px solid var(--ui-line);
    padding: .6rem 1.25rem 0;
    display: flex; align-items: center; gap: .5rem;
}
.pv-inner-tabs .nav-link {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .55rem 1rem;
    font-size: .8rem; font-weight: 600;
    color: var(--ui-muted);
    border: none; border-radius: 10px 10px 0 0;
    border-bottom: 2px solid transparent;
    transition: all .2s ease;
    background: transparent;
}
.pv-inner-tabs .nav-link:hover {
    color: var(--ui-accent);
    background: var(--ui-surface-2);
}
.pv-inner-tabs .nav-link.active {
    color: var(--ui-accent);
    border-bottom-color: var(--ui-gold);
    background: var(--ui-surface-2);
}
.pv-inner-tabs .badge {
    font-size: .62rem; padding: .25em .55em;
    border-radius: 999px; font-weight: 700;
}

/* ── Responsive ── */
@media (max-width: 991.98px) {
    .pv-table .pv-col-sexo { display: none; }
}
@media (max-width: 767.98px) {
    .pv-table .pv-col-codigo { display: none; }
    .pv-table th, .pv-table td { padding: .5rem .35rem; font-size: .78rem; }
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="row">
    <div class="col-12">
        <div class="pv-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon">
                    <i class="ri-bar-chart-grouped-line"></i>
                </div>
                <div>
                    <h4 class="mb-1">Programas Vigentes</h4>
                    <p class="hero-sub">Reportes visuales de programas en <strong>Inscripciones</strong> y <strong>En Desarrollo</strong></p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-md-end gap-2">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                    <li class="breadcrumb-item active">Programas Vigentes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Tabs principales --}}
<div class="mb-3">
    <ul class="nav pv-main-tabs" id="pv-main-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-tipo="todos" href="javascript:void(0);" role="tab" aria-selected="true">
                <i class="ri-dashboard-line"></i> General
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-tipo="programas" href="javascript:void(0);" role="tab" aria-selected="false">
                <i class="ri-booklet-line"></i> Programas
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-tipo="upi" href="javascript:void(0);" role="tab" aria-selected="false">
                <i class="ri-graduation-cap-line"></i> UPI Diplomado
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-tipo="upi-auto" href="javascript:void(0);" role="tab" aria-selected="false">
                <i class="ri-graduation-cap-line"></i> UPI Autogestionados
            </a>
        </li>
    </ul>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4" id="pv-stats-cards">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:var(--ui-accent);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-booklet-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Programas</div>
                    <div class="pv-stat-value" id="pv-stat-programas">0</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:var(--ui-info);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-team-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Inscritos</div>
                    <div class="pv-stat-value" id="pv-stat-inscritos">0</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:#299cdb;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-user-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Regulares</div>
                    <div class="pv-stat-value" id="pv-stat-regulares">0</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:var(--ui-success);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-money-dollar-circle-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Contado</div>
                    <div class="pv-stat-value" id="pv-stat-contado">0</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:var(--avip-navy-600);">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-men-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Hombres</div>
                    <div class="pv-stat-value" id="pv-stat-hombres">0</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="pv-stat-card h-100" style="--stat-accent:#d1458a;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="pv-stat-icon"><i class="ri-women-line"></i></div>
                <div class="min-width-0">
                    <div class="pv-stat-label">Mujeres</div>
                    <div class="pv-stat-value" id="pv-stat-mujeres">0</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card panel-card pv-chart-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <span class="section-icon bg-soft-primary text-primary me-2"><i class="ri-bar-chart-grouped-line"></i></span>
                        <div>
                            <h5 class="card-title mb-0 fs-14 fw-semibold">Inscritos por Programa</h5>
                            <small class="text-muted" style="font-size:.72rem;">Distribución de Regulares y Contado por programa vigente</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" id="pv-chart-filter">
                        <input type="radio" class="btn-check" name="pvChartFilter" id="pv-filtro-todos" value="todos" checked>
                        <label class="btn btn-outline-primary" for="pv-filtro-todos">Todos</label>
                        <input type="radio" class="btn-check" name="pvChartFilter" id="pv-filtro-desarrollo" value="desarrollo">
                        <label class="btn btn-outline-primary" for="pv-filtro-desarrollo">En Desarrollo</label>
                        <input type="radio" class="btn-check" name="pvChartFilter" id="pv-filtro-inscripciones" value="inscripciones">
                        <label class="btn btn-outline-primary" for="pv-filtro-inscripciones">Inscripciones</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="pv-chart-wrap">
                    <div id="chart-programas" class="pv-chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="row g-3">
            <div class="col-12">
                <div class="card panel-card pv-chart-card mb-0">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon bg-soft-success text-success me-2"><i class="ri-pie-chart-2-line"></i></span>
                            <div>
                                <h5 class="card-title mb-0 fs-14 fw-semibold">Inscritos por Área</h5>
                                <small class="text-muted" style="font-size:.72rem;">Distribución por áreas académicas</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pv-chart-wrap">
                            <div id="chart-areas" class="pv-chart-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card panel-card pv-chart-card mb-0">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon bg-soft-warning text-warning me-2"><i class="ri-pie-chart-line"></i></span>
                            <div>
                                <h5 class="card-title mb-0 fs-14 fw-semibold">Distribución por Sexo</h5>
                                <small class="text-muted" style="font-size:.72rem;">Hombres vs Mujeres inscritos</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pv-chart-wrap">
                            <div id="chart-sexo" class="pv-chart-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card panel-card pv-chart-card mb-0">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon bg-soft-info text-info me-2"><i class="ri-funds-line"></i></span>
                            <div>
                                <h5 class="card-title mb-0 fs-14 fw-semibold">Plan de Pago</h5>
                                <small class="text-muted" style="font-size:.72rem;">Regular vs Contado</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pv-chart-wrap">
                            <div id="chart-plan" class="pv-chart-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tablas detalladas --}}
<div class="card panel-card mb-4">
    <div class="pv-inner-tabs" role="tablist">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-desarrollo" role="tab" aria-selected="true">
            <i class="ri-refresh-line"></i> En Desarrollo
            <span class="badge pv-badge-estado pv-badge-desarrollo" id="pv-count-desarrollo">0</span>
        </a>
        <a class="nav-link" data-bs-toggle="tab" href="#tab-inscripciones" role="tab" aria-selected="false">
            <i class="ri-mail-open-line"></i> Inscripciones
            <span class="badge pv-badge-estado pv-badge-inscripciones" id="pv-count-inscripciones">0</span>
        </a>
    </div>
    <div class="card-body tab-content p-0">
        {{-- Tab: En Desarrollo --}}
        <div class="tab-pane active" id="tab-desarrollo" role="tabpanel">
            <div class="pv-summary-bar">
                <span class="pv-chip"><i class="ri-booklet-line"></i> Programas <strong id="pv-desarrollo-count">0</strong></span>
                <span class="pv-chip"><i class="ri-team-line"></i> Inscritos <strong id="pv-desarrollo-inscritos">0</strong></span>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="text-muted fs-11 fw-semibold text-uppercase" style="font-size:.68rem;">Agrupar por</label>
                    <select class="form-select form-select-sm pv-group-select" data-target="desarrollo" style="width:auto;font-size:.75rem;">
                        <option value="gestion">Gestión</option>
                        <option value="tipo">Tipo de Programa</option>
                    </select>
                </div>
            </div>
            <div id="pv-grupos-desarrollo"></div>
        </div>
        {{-- Tab: Inscripciones --}}
        <div class="tab-pane" id="tab-inscripciones" role="tabpanel">
            <div class="pv-summary-bar">
                <span class="pv-chip"><i class="ri-booklet-line"></i> Programas <strong id="pv-inscripciones-count">0</strong></span>
                <span class="pv-chip"><i class="ri-team-line"></i> Inscritos <strong id="pv-inscripciones-inscritos">0</strong></span>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="text-muted fs-11 fw-semibold text-uppercase" style="font-size:.68rem;">Agrupar por</label>
                    <select class="form-select form-select-sm pv-group-select" data-target="inscripciones" style="width:auto;font-size:.75rem;">
                        <option value="gestion">Gestión</option>
                        <option value="tipo">Tipo de Programa</option>
                    </select>
                </div>
            </div>
            <div id="pv-grupos-inscripciones"></div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
var _programaShowUrl = '{{ route("admin.programas.show", ["id" => "_ID_"]) }}';
document.addEventListener('DOMContentLoaded', function () {
    var chartProgramas = null, chartAreas = null, chartSexo = null, chartPlan = null;
    var _storeDesarrollo = [], _storeInscripciones = [], _storeProgramas = [];
    var _currentTipo = 'todos', _currentFilter = 'todos';

    /* ── Paleta AVIP ── */
    var PAL = {
        navy:     '#163b6b',
        navyLight:'#2a5d97',
        teal:     '#0d8574',
        tealLight:'#12a08c',
        gold:     '#cba135',
        blue:     '#1f6fbf',
        blueLight:'#5aa4e0',
        amber:    '#b7791f',
        red:      '#b3243a',
        pink:     '#d1458a',
        purple:   '#7c3aed',
        muted:    '#98a2b3'
    };

    function isDarkMode() {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark'
            || document.body.getAttribute('data-layout-mode') === 'dark';
    }

    function chartVars() {
        var d = isDarkMode();
        return {
            dark: d,
            mode: d ? 'dark' : 'light',
            text: d ? '#b3bcc9' : '#5b6572',
            grid: d ? '#2a303a' : '#edf0f4',
            stroke: d ? '#1b1f27' : '#ffffff',
            muted: d ? '#8b95a3' : '#8b95a3'
        };
    }

    /* ── Carga de datos ── */
    function loadData(filtro, tipo) {
        filtro = filtro || 'todos';
        tipo = tipo || _currentTipo;
        _currentTipo = tipo;
        _currentFilter = filtro;
        var url = '{{ route("admin.programas-vigentes.stats") }}?filtro=' + filtro + '&tipo=' + tipo;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var r = data.resumen;
            document.getElementById('pv-stat-programas').textContent = r.total_programas;
            document.getElementById('pv-stat-inscritos').textContent = r.total_inscritos;
            document.getElementById('pv-stat-regulares').textContent = r.regulares;
            document.getElementById('pv-stat-contado').textContent = r.contado;
            document.getElementById('pv-stat-hombres').textContent = r.hombres;
            document.getElementById('pv-stat-mujeres').textContent = r.mujeres;

            _storeProgramas = data.programas || [];
            _storeDesarrollo = data.programas_desarrollo;
            _storeInscripciones = data.programas_inscripciones;

            renderChartProgramas(data.chart_programas);
            renderChartAreas(data.chart_areas);
            renderChartSexo(r.hombres, r.mujeres);
            renderChartPlan(r.regulares, r.contado);
            renderTableDesarrollo();
            renderTableInscripciones();

            document.getElementById('pv-count-desarrollo').textContent = data.programas_desarrollo.length;
            document.getElementById('pv-count-inscripciones').textContent = data.programas_inscripciones.length;
        })
        .catch(function() {
            document.getElementById('pv-grupos-desarrollo').innerHTML = '<div class="text-center text-danger py-4">Error al cargar datos</div>';
            document.getElementById('pv-grupos-inscripciones').innerHTML = '<div class="text-center text-danger py-4">Error al cargar datos</div>';
        });
    }

    /* ── Gráfico de barras: Inscritos por Programa ── */
    function renderChartProgramas(c) {
        if (chartProgramas) chartProgramas.destroy();
        var labels = c.labels || [];
        if (!labels.length) {
            document.getElementById('chart-programas').innerHTML =
                '<div class="text-center py-5">' +
                    '<div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:var(--ui-surface-2);color:var(--ui-muted);font-size:1.6rem;">' +
                    '<i class="ri-bar-chart-2-line"></i></div>' +
                    '<h6 class="fw-bold mb-1" style="color:var(--ui-ink);">Sin datos</h6>' +
                    '<p class="mb-0" style="font-size:.84rem;color:var(--ui-muted);">No hay programas en esta categoría.</p></div>';
            return;
        }
        var v = chartVars();
        var isTodos = c.series && c.series.length === 4;
        var colors = isTodos
            ? [PAL.navyLight, PAL.navy, PAL.teal, PAL.tealLight]
            : [PAL.navy, PAL.teal];
        var h = Math.max(340, labels.length * 42);

        chartProgramas = new ApexCharts(document.getElementById('chart-programas'), {
            chart: {
                type: 'bar', height: h, stacked: true,
                toolbar: { show: false }, fontFamily: 'inherit', foreColor: v.text
            },
            theme: { mode: v.mode },
            series: c.series || [],
            colors: colors,
            xaxis: {
                categories: labels,
                axisBorder: { show: false }, axisTicks: { show: false },
                labels: { style: { fontSize: '11px', fontWeight: 500 } }
            },
            yaxis: {
                labels: { style: { fontSize: '11px', fontWeight: 600 }, maxWidth: 320 }
            },
            plotOptions: {
                bar: { horizontal: true, barHeight: '62%', borderRadius: 6, borderRadiusApplication: 'end' }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] },
                dropShadow: { enabled: false },
                formatter: function(val) { return val > 0 ? val : ''; }
            },
            legend: {
                position: 'top', horizontalAlign: 'left',
                fontSize: '11px', fontWeight: 600,
                markers: { width: 9, height: 9, radius: 3 },
                itemMargin: { horizontal: 12, vertical: 4 }
            },
            grid: {
                borderColor: v.grid, strokeDashArray: 4,
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } },
                padding: { left: 6, right: 10 }
            },
            fill: { type: 'solid' },
            states: { hover: { filter: { type: 'darken', value: 0.88 } } },
            tooltip: {
                theme: v.mode, shared: true, intersect: false,
                y: { formatter: function(val) { return val + ' inscritos'; } }
            },
            noData: { text: 'No hay programas vigentes', style: { fontSize: '13px', color: v.muted } }
        });
        chartProgramas.render();
    }

    /* ── Donut base ── */
    function buildDonut(o) {
        var v = chartVars();
        var total = (o.series || []).reduce(function(a, b) { return a + (Number(b) || 0); }, 0);
        return {
            chart: { type: 'donut', height: 250, fontFamily: 'inherit', foreColor: v.text },
            theme: { mode: v.mode },
            series: o.series || [],
            labels: o.labels || [],
            colors: o.colors || [],
            stroke: { colors: [v.stroke], width: 2 },
            legend: {
                position: 'bottom', fontSize: '11px', fontWeight: 600,
                markers: { width: 9, height: 9, radius: 3 },
                itemMargin: { horizontal: 8, vertical: 4 }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] },
                dropShadow: { enabled: false },
                formatter: function(val) { return Math.round(val) + '%'; }
            },
            plotOptions: {
                pie: {
                    expandOnClick: false,
                    donut: {
                        size: '62%',
                        labels: {
                            show: true,
                            name: { fontSize: '11px', fontWeight: 600, color: v.text, offsetY: -4 },
                            value: { fontSize: '18px', fontWeight: 800, color: v.dark ? '#eef1f5' : '#1f2733', offsetY: 6 },
                            total: {
                                show: true, label: 'Total', fontSize: '11px', fontWeight: 600, color: v.text,
                                formatter: function() { return total; }
                            }
                        }
                    }
                }
            },
            tooltip: { theme: v.mode, y: { formatter: function(val) { return val + ' inscritos'; } } },
            noData: { text: 'Sin datos', style: { fontSize: '13px', color: v.muted } }
        };
    }

    function renderChartAreas(c) {
        if (chartAreas) chartAreas.destroy();
        chartAreas = new ApexCharts(document.getElementById('chart-areas'), buildDonut({
            series: c.total || [], labels: c.labels || [], colors: c.colors || []
        }));
        chartAreas.render();
    }

    function renderChartSexo(hombres, mujeres) {
        if (chartSexo) chartSexo.destroy();
        chartSexo = new ApexCharts(document.getElementById('chart-sexo'), buildDonut({
            series: [hombres, mujeres],
            labels: ['Hombres', 'Mujeres'],
            colors: [PAL.navy, PAL.pink]
        }));
        chartSexo.render();
    }

    function renderChartPlan(regulares, contado) {
        if (chartPlan) chartPlan.destroy();
        chartPlan = new ApexCharts(document.getElementById('chart-plan'), buildDonut({
            series: [regulares, contado],
            labels: ['Regular', 'Contado'],
            colors: [PAL.blue, PAL.teal]
        }));
        chartPlan.render();
    }

    /* ── Tablas agrupadas ── */
    function getGroupKey(p, groupBy) {
        return groupBy === 'tipo' ? (p.tipo_nombre || 'Sin tipo') : p.gestion.toString();
    }

    var tipoColors = {
        'Curso': PAL.blue, 'Diplomado': PAL.teal, 'Especialidad': PAL.gold,
        'Maestria': PAL.pink, 'Maestrías': PAL.pink, 'Webinar': PAL.purple,
        'UPI Diplomados': PAL.amber
    };
    var gestionColors = [PAL.navy, PAL.blue, PAL.teal, PAL.gold, PAL.pink, PAL.purple, PAL.amber, PAL.red];

    function renderGroupedTable(containerId, programas, emptyMsg, countId, enrolledId, groupBy) {
        document.getElementById(countId).textContent = programas.length;
        var totalEnrolled = programas.reduce(function(s, p) { return s + p.total; }, 0);
        document.getElementById(enrolledId).textContent = totalEnrolled;
        var container = document.getElementById(containerId);

        if (!programas.length) {
            container.innerHTML =
                '<div class="text-center py-5">' +
                    '<div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:var(--ui-surface-2);color:var(--ui-muted);font-size:1.6rem;">' +
                    '<i class="ri-inbox-line"></i></div>' +
                    '<h6 class="fw-bold mb-1" style="color:var(--ui-ink);">Sin registros</h6>' +
                    '<p class="mb-0" style="font-size:.84rem;color:var(--ui-muted);">' + emptyMsg + '</p></div>';
            return;
        }

        var grupos = {}, orderKeys = [];
        programas.forEach(function(p) {
            var key = getGroupKey(p, groupBy);
            if (!grupos[key]) { grupos[key] = []; orderKeys.push(key); }
            grupos[key].push(p);
        });
        if (groupBy === 'gestion') orderKeys.sort(function(a, b) { return b - a; });

        var html = '';
        orderKeys.forEach(function(key) {
            var items = grupos[key];
            var progCount = items.length;
            var insCount = items.reduce(function(s, p) { return s + p.total; }, 0);

            var subGroupBy = groupBy === 'gestion' ? 'tipo' : 'gestion';
            var subGroups = {};
            items.forEach(function(p) {
                var sk = getGroupKey(p, subGroupBy);
                if (!subGroups[sk]) subGroups[sk] = { prog: 0, ins: 0 };
                subGroups[sk].prog++;
                subGroups[sk].ins += p.total;
            });
            var subKeys = Object.keys(subGroups);
            if (subGroupBy === 'gestion') subKeys.sort(function(a, b) { return b - a; });

            var subHtml = '';
            subKeys.forEach(function(sk, idx) {
                var sg = subGroups[sk];
                var chipColor = subGroupBy === 'tipo' ? (tipoColors[sk] || PAL.muted) : gestionColors[idx % gestionColors.length];
                subHtml +=
                    '<span class="pv-chip" style="font-size:.72rem;">' +
                        '<span class="pv-dot" style="background:' + chipColor + ';"></span>' +
                        '<strong>' + sk + '</strong>&middot;' +
                        sg.prog + ' ' + (sg.prog === 1 ? 'programa' : 'programas') + '&middot;' +
                        sg.ins + ' ' + (sg.ins === 1 ? 'inscrito' : 'inscritos') +
                    '</span>';
            });

            html +=
                '<div class="pv-group-header">' +
                    '<span class="pv-gest-badge">' + key + '</span>' +
                    '<span class="pv-gest-info">' +
                        '<span><i class="ri-booklet-line"></i> <strong>' + progCount + '</strong> ' + (progCount === 1 ? 'programa' : 'programas') + '</span>' +
                        '<span><i class="ri-team-line"></i> <strong>' + insCount + '</strong> ' + (insCount === 1 ? 'inscrito' : 'inscritos') + '</span>' +
                    '</span>' +
                '</div>' +
                '<div class="pv-summary-bar" style="padding:.5rem 1.25rem;">' + subHtml + '</div>' +
                '<div class="table-responsive">' +
                '<table class="pv-table table mb-0">' +
                    '<thead><tr>' +
                        '<th class="text-center" style="width:44px;">#</th>' +
                        '<th>Programa</th>' +
                        '<th class="text-center pv-col-codigo" style="width:13%;">Código</th>' +
                        '<th class="text-center" style="width:9%;">Total</th>' +
                        '<th class="text-center" style="width:12%;">Regulares</th>' +
                        '<th class="text-center" style="width:11%;">Contado</th>' +
                        '<th class="text-center pv-col-sexo" style="width:9%;">Hombres</th>' +
                        '<th class="text-center pv-col-sexo" style="width:9%;">Mujeres</th>' +
                    '</tr></thead><tbody>';

            items.forEach(function(p, i) {
                var progUrl = _programaShowUrl.replace('_ID_', p.id);
                var progTitle = String(p.nombre).replace(/"/g, '&quot;');
                html += '<tr>' +
                    '<td class="text-center pv-idx fw-semibold">' + (i + 1) + '</td>' +
                    '<td class="pv-col-name"><a href="' + progUrl + '" class="d-block text-truncate" title="' + progTitle + '"><i class="ri-external-link-line fs-11 me-1" style="color:var(--ui-muted);"></i>' + p.nombre + '</a></td>' +
                    '<td class="text-center pv-col-codigo"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                    '<td class="text-center pv-num"><span class="badge bg-primary fs-12 fw-bold">' + p.total + '</span></td>' +
                    '<td class="text-center fw-semibold pv-num" style="color:var(--ui-info);">' + p.regulares + '</td>' +
                    '<td class="text-center fw-semibold pv-num" style="color:var(--ui-success);">' + p.contado + '</td>' +
                    '<td class="text-center fw-semibold pv-num pv-col-sexo pv-hombres">' + p.hombres + '</td>' +
                    '<td class="text-center fw-semibold pv-num pv-col-sexo pv-mujeres">' + p.mujeres + '</td>' +
                '</tr>';
            });

            html += '</tbody></table></div>';
        });

        container.innerHTML = html;
    }

    function renderTableDesarrollo() {
        var sel = document.querySelector('.pv-group-select[data-target="desarrollo"]');
        renderGroupedTable('pv-grupos-desarrollo', _storeDesarrollo, 'No hay programas en desarrollo', 'pv-desarrollo-count', 'pv-desarrollo-inscritos', sel ? sel.value : 'gestion');
    }

    function renderTableInscripciones() {
        var sel = document.querySelector('.pv-group-select[data-target="inscripciones"]');
        renderGroupedTable('pv-grupos-inscripciones', _storeInscripciones, 'No hay programas en inscripciones', 'pv-inscripciones-count', 'pv-inscripciones-inscritos', sel ? sel.value : 'gestion');
    }

    /* ── Eventos ── */
    document.querySelectorAll('.pv-group-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            if (this.dataset.target === 'desarrollo') renderTableDesarrollo();
            else renderTableInscripciones();
        });
    });

    document.querySelectorAll('#pv-chart-filter input[name="pvChartFilter"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) loadData(this.value, _currentTipo);
        });
    });

    document.querySelectorAll('#pv-main-tabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function() {
            if (this.classList.contains('active')) return;
            document.querySelectorAll('#pv-main-tabs .nav-link').forEach(function(t) { t.classList.remove('active'); t.removeAttribute('aria-selected'); });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            document.getElementById('pv-filtro-todos').checked = true;
            loadData('todos', this.dataset.tipo);
        });
    });

    if (window.MutationObserver) {
        var _lastDark = isDarkMode();
        new MutationObserver(function() {
            var d = isDarkMode();
            if (d !== _lastDark) { _lastDark = d; loadData(_currentFilter, _currentTipo); }
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
    }

    loadData('todos', 'todos');
});
</script>
@endpush
