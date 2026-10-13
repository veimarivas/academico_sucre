@extends('admin.layouts.master')

@section('title', 'Inversiones - Marketing - ' . config('app.name'))

@push('css')
<style>
.inv-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18, 52, 91, .75);
    margin-bottom: 1.25rem;
}
.inv-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.inv-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.inv-hero > * { position: relative; z-index: 1; }
.inv-hero .hero-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px); }
.inv-hero h4 { color: #fff !important; font-weight: 700; letter-spacing: .2px; margin: 0; }
.inv-hero .hero-sub { color: rgba(255,255,255,.85) !important; font-size: .88rem; margin: 0; }
.inv-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.inv-hero .breadcrumb-item, .inv-hero .breadcrumb-item a { color: rgba(255,255,255,.85) !important; }
.inv-hero .breadcrumb-item.active { color: #fff !important; font-weight: 600; }
.inv-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55) !important; }

.stat-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; transition: transform .2s ease, box-shadow .2s ease; overflow: hidden; position: relative; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before { content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--stat-accent, #405189); }
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .stat-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: #878a99; font-weight: 700; margin-bottom: .4rem; }
.stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #212529; line-height: 1.2; }
.inv-ic { width: 44px; height: 44px; border-radius: 13px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }

.filter-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-header { background: transparent; border-bottom: 1px dashed #eef0f4; padding: 1rem 1.25rem; }
.filter-card .form-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #878a99; margin-bottom: .35rem; }

.chart-card { border: 1px solid #eef0f4; border-radius: 12px; }
[data-layout-mode="dark"] .chart-card { border-color: #2a2f34; }
.apexcharts-tooltip,
.apexcharts-tooltip-container,
.apexcharts-tooltip-series-group,
.apexcharts-tooltip-custom { z-index: 99999 !important; }
.chart-title { text-align: center; margin-bottom: 1rem; padding-bottom: .75rem; border-bottom: 1px dashed #eef0f4; color: #495057; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; display: flex; align-items: center; justify-content: center; gap: .5rem; }
.chart-title i { font-size: 1rem; color: #0ab39c; }

.tabla-inv { width: 100%; font-size: .82rem; }
.tabla-inv th, .tabla-inv td { vertical-align: middle; padding: .55rem .5rem; }
.tabla-inv thead th { background: #f3f6f9 !important; font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #495057; border-bottom: none; white-space: nowrap; }
.tabla-inv tbody tr td { border-bottom: 1px solid #f1f3f5; }
.tabla-inv .prog-name { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.inv-ranking-wrap { border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; }
.tabla-ranking { table-layout: fixed; width: 100%; }
.tabla-ranking th, .tabla-ranking td { white-space: normal; }
.tabla-ranking .prog-name { max-width: 100%; }
[data-layout-mode="dark"] .inv-ranking-wrap { border-color: #2a2f34; }
.tabla-inv thead th { position: sticky; top: 0; z-index: 2; }
.tabla-inv thead th:first-child { border-top-left-radius: 12px; }
.tabla-inv thead th:last-child { border-top-right-radius: 12px; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="inv-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-bar-chart-grouped-line"></i></div>
                <div>
                    <h4>Inversiones en Publicidad</h4>
                    <p class="hero-sub">Análisis de inversión por programa</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item active">Inversiones</li>
            </ol>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card h-100" style="--stat-accent: #f7b84b;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between gap-2">
                <div class="min-width-0">
                    <div class="stat-label">Total Gastado</div>
                    <div class="stat-value">Bs {{ number_format($resumen['total_gastado'], 2) }}</div>
                </div>
                <span class="inv-ic" style="background:#fff7e6;color:#b7791f;"><i class="ri-wallet-3-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card h-100" style="--stat-accent: #0ab39c;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between gap-2">
                <div class="min-width-0">
                    <div class="stat-label">Total Aprobado</div>
                    <div class="stat-value">Bs {{ number_format($resumen['total_invertido'], 2) }}</div>
                </div>
                <span class="inv-ic" style="background:#e6f7ee;color:#0a7d5a;"><i class="ri-checkbox-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card h-100" style="--stat-accent: #299cdb;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between gap-2">
                <div class="min-width-0">
                    <div class="stat-label">Programas</div>
                    <div class="stat-value">{{ $resumen['total_programas'] }}</div>
                </div>
                <span class="inv-ic" style="background:#e8f4fd;color:#1f6fbf;"><i class="ri-booklet-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card h-100" style="--stat-accent: #405189;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between gap-2">
                <div class="min-width-0">
                    <div class="stat-label">Comprobantes</div>
                    <div class="stat-value">{{ $resumen['total_comprobantes'] }}</div>
                </div>
                <span class="inv-ic" style="background:#eef1f8;color:#405189;"><i class="ri-receipt-line"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="filter-card">
            <div class="card-header d-flex align-items-center gap-2 flex-wrap">
                <i class="ri-filter-3-line fs-5" style="color:#405189;"></i>
                <span class="fw-semibold fs-13">Filtros</span>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.marketing.inversiones') }}" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">Gestión</label>
                        <select name="gestion" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" {{ (string) $g == $gestion ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Mes</label>
                        <select name="mes" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($meses as $val => $nom)
                                <option value="{{ $val }}" {{ (string) $val == $mes ? 'selected' : '' }}>{{ $nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Quincena</label>
                        <select name="quincena" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="todas" {{ $quincena == 'todas' ? 'selected' : '' }}>Todas</option>
                            <option value="1" {{ $quincena == '1' ? 'selected' : '' }}>Quincena 1 (1–15)</option>
                            <option value="2" {{ $quincena == '2' ? 'selected' : '' }}>Quincena 2 (16–fin)</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.marketing.inversiones') }}" class="btn btn-soft-danger btn-sm d-flex align-items-center gap-1">
                            <i class="ri-refresh-line"></i> Restablecer
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-bar-chart-2-line"></i>Inversión por Programa — {{ $meses[$mes] ?? 'Todos' }} {{ $gestion }}</h6>
                <div id="chart-programas" style="height:420px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-5">
        <div class="card chart-card mb-0 h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="chart-title"><i class="ri-pie-chart-2-line"></i>Inversión: Programas vs Reclutamiento</h6>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <div id="chart-pie-tipos" style="width:100%;min-height:340px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card chart-card mb-0 h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="chart-title"><i class="ri-bar-chart-2-line"></i>Inversión en Programas en Inscripciones — Mayor a Menor</h6>
                <div class="flex-grow-1">
                    <div id="chart-pie-inscripciones" style="width:100%;min-height:340px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="chart-title mb-0" style="border:0;padding-bottom:0;"><i class="ri-list-ordered-2"></i>Ranking de Programas — {{ $meses[$mes] ?? 'Todos' }} {{ $gestion }}</h6>
                    <span class="badge bg-soft-primary text-primary">{{ $porPrograma->count() }} programas</span>
                </div>
                <div class="inv-ranking-wrap mt-2">
                    <table class="table tabla-inv tabla-ranking mb-0">
                        <thead>
                            <tr>
                                <th style="width:38px;" class="text-center">#</th>
                                <th>Programa</th>
                                <th class="text-end" style="width:108px;">Gastado</th>
                                <th class="text-end" style="width:108px;">Aprobado</th>
                                <th class="text-end" style="width:118px;">No Aprobado</th>
                                <th class="text-end" style="width:44px;">C</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($porPrograma as $i => $p)
                            @php $noAprob = $p->total_monto - $p->total_aprobado; @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="prog-name" title="{{ $p->nombre }} ({{ $p->codigo }})">
                                        <span class="fw-semibold fs-12">{{ $p->nombre }}</span>
                                        <small class="d-block text-muted">{{ $p->codigo }}</small>
                                    </div>
                                </td>
                                <td class="text-end text-warning fw-semibold fs-11">Bs {{ number_format($p->total_monto, 2) }}</td>
                                <td class="text-end fw-bold text-success fs-11">Bs {{ number_format($p->total_aprobado, 2) }}</td>
                                <td class="text-end text-danger fw-semibold fs-11">Bs {{ number_format($noAprob, 2) }}</td>
                                <td class="text-end text-muted">{{ $p->total_comprobantes }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin datos para el período seleccionado</td></tr>
                            @endforelse
                        </tbody>
                        @if($porPrograma->isNotEmpty())
                        @php
                            $sumGastado = $porPrograma->sum('total_monto');
                            $sumAprobado = $porPrograma->sum('total_aprobado');
                            $sumNoAprob = $sumGastado - $sumAprobado;
                        @endphp
                        <tfoot>
                            <tr style="border-top:2px solid #405189;">
                                <td></td>
                                <td class="fw-bold text-uppercase" style="font-size:.72rem;">Total</td>
                                <td class="text-end fw-bold fs-11">Bs {{ number_format($sumGastado, 2) }}</td>
                                <td class="text-end fw-bold fs-11 text-success">Bs {{ number_format($sumAprobado, 2) }}</td>
                                <td class="text-end fw-bold fs-11 text-danger">Bs {{ number_format($sumNoAprob, 2) }}</td>
                                <td class="text-end fw-bold">{{ $porPrograma->sum('total_comprobantes') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Autogestión por Programa (chart) --}}
@if($soloAuto->isNotEmpty())
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-hand-coin-line"></i>Gastos de Autogestión por Programa — {{ $meses[$mes] ?? 'Todos' }} {{ $gestion }}</h6>
                <div id="chart-auto-programas" style="height:420px;"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Total Invertido por Programa (Asignado + Autogestión) — chart --}}
@if($programasResumen->isNotEmpty())
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card mb-0">
            <div class="card-body">
                <h6 class="chart-title"><i class="ri-bar-chart-grouped-line"></i>Total Invertido por Programa (Asignado + Autogestión) — {{ $meses[$mes] ?? 'Todos' }} {{ $gestion }}</h6>
                <div id="chart-total-programas" style="height:420px;"></div>
            </div>
        </div>
    </div>
</div>
@endif

</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
try {
    var progLabels = @json($porPrograma->pluck('nombre'));
    var progMonto  = @json($porPrograma->pluck('total_monto')->values()->map(fn($v) => (float) $v));
    var progApob   = @json($porPrograma->pluck('total_aprobado')->values()->map(fn($v) => (float) $v));

    var progNoApob = progMonto.map(function (v, i) { return v - progApob[i]; });

    var elProg = document.getElementById('chart-programas');
    if (elProg && progLabels.length) {
        var chartHeight = Math.max(420, progLabels.length * 40);
        new ApexCharts(elProg, {
            chart: { height: chartHeight, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '58%', borderRadiusApplication: 'end' } },
            dataLabels: {
                enabled: true,
                formatter: function (v) { return v != null && !isNaN(v) && Number(v) > 0 ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; },
                textAnchor: 'end',
                offsetX: -6,
                style: { fontSize: '9px', fontWeight: 700, colors: ['#fff'] },
                background: { enabled: true, foreColor: '#fff', borderRadius: 4, padding: 3, opacity: 0.92, dropShadow: { enabled: false } },
                dropShadow: { enabled: false }
            },
            series: [
                { name: 'Gastado', data: progMonto, color: '#cba135' },
                { name: 'Aprobado', data: progApob, color: '#0ab39c' },
                { name: 'No Aprobado', data: progNoApob, color: '#b3243a' }
            ],
            xaxis: {
                categories: progLabels,
                labels: { formatter: function (v) { return v != null ? '' + v : ''; }, style: { fontSize: '11px' }, maxHeight: 80 }
            },
            yaxis: { labels: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; }, style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2}) : ''; } } },
            grid: { borderColor: '#eef1f8', strokeDashArray: 4, padding: { right: 25 } },
            legend: { position: 'bottom', horizontalAlign: 'center', markers: { width: 10, height: 10, radius: 3 }, itemMargin: { horizontal: 12 } }
        }).render();
    } else if (elProg) {
        elProg.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line fs-1 d-block mb-2"></i>Sin datos de inversión</div>';
    }
} catch (e) { console.error('chart-programas error:', e); }

try {
    var progTotal = {{ (float) $porPrograma->sum('total_aprobado') }};
    var reclTotal = {{ (float) ($reclutamiento->total_aprobado ?? 0) }};

    var elPieTipos = document.getElementById('chart-pie-tipos');
    if (elPieTipos && (progTotal > 0 || reclTotal > 0)) {
        new ApexCharts(elPieTipos, {
            chart: { type: 'donut', fontFamily: 'inherit', toolbar: { show: false } },
            labels: ['Programas', 'Reclutamiento de Personal'],
            series: [progTotal, reclTotal],
            colors: ['#0d8574', '#b7791f'],
            plotOptions: { pie: { donut: { size: '55%', labels: { show: true, name: { show: true, fontSize: '13px', fontWeight: 600 }, value: { show: true, fontSize: '14px', fontWeight: 700, formatter: function (v) { return 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2}); } }, total: { show: true, fontSize: '12px', fontWeight: 700, label: 'Total', formatter: function () { return 'Bs ' + Number(progTotal + reclTotal).toLocaleString('es-BO', {minimumFractionDigits: 2}); } } } } } },
            dataLabels: { enabled: true, formatter: function (v) { return v.toFixed(1) + '%'; }, style: { fontSize: '12px', fontWeight: 600, colors: ['#fff'] } },
            tooltip: { y: { formatter: function (v) { return v != null ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2}) : ''; } } },
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 12 } },
            responsive: [{ breakpoint: 480, options: { chart: { width: '100%' }, legend: { position: 'bottom' } } }]
        }).render();
    } else if (elPieTipos) {
        elPieTipos.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line fs-1 d-block mb-2"></i>Sin datos para el período seleccionado</div>';
    }
} catch (e) { console.error('chart-pie-tipos error:', e); }

try {
    var insLabels = @json($porProgramaInscripciones->pluck('nombre')->values());
    var insData   = @json($porProgramaInscripciones->pluck('total_aprobado')->values()->map(fn($v) => (float) $v));

    var insPairs = insLabels.map(function (l, i) { return { label: l, value: insData[i] }; });
    insPairs.sort(function (a, b) { return b.value - a.value; });
    var sortedLabels = insPairs.map(function (p) { return p.label; });
    var sortedData = insPairs.map(function (p) { return p.value; });

    var elPie = document.getElementById('chart-pie-inscripciones');
    if (elPie && sortedLabels.length && sortedData.some(function (v) { return v > 0; })) {
        var total = sortedData.reduce(function (a, b) { return a + b; }, 0);
        var chartHeight = Math.max(380, sortedLabels.length * 44);
        new ApexCharts(elPie, {
            chart: { type: 'bar', height: chartHeight, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '55%', distributed: true } },
            colors: ['#163b6b', '#1f6fbf', '#0d8574', '#cba135', '#b3243a', '#2a5d97', '#b7791f', '#4c7bb0', '#7d9ec8', '#ddb84d', '#8fbbe6', '#e0a94a'],
            dataLabels: {
                enabled: true,
                formatter: function (v, opts) {
                    if (v == null || isNaN(v)) return '';
                    var idx = 0;
                    if (typeof opts === 'number') idx = opts;
                    else if (opts && opts.dataPointIndex != null) idx = opts.dataPointIndex;
                    var name = sortedLabels[idx] || 'Programa ' + (idx + 1);
                    var pct = total > 0 ? ((v / total) * 100).toFixed(1) : '0.0';
                    return name + '\nBs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) + ' (' + pct + '%)';
                },
                style: { fontSize: '9px', fontWeight: 600, colors: ['#fff'] },
                offsetX: 4,
                offsetY: 0
            },
            series: [{ name: 'Aprobado', data: sortedData }],
            xaxis: {
                categories: sortedLabels,
                labels: { formatter: function (v) { return v != null ? '' + v : ''; }, style: { fontSize: '11px', fontWeight: 500 }, maxWidth: 260, trim: true },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; },
                    style: { fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            tooltip: {
                appendToBody: true,
                custom: function(opts) {
                    var name = opts.w.globals.labels[opts.dataPointIndex] || sortedLabels[opts.dataPointIndex] || '';
                    var val = opts.series[opts.seriesIndex][opts.dataPointIndex];
                    if (val == null || isNaN(val)) return '<div style="padding:6px 10px;font-size:12px;">Sin datos</div>';
                    var pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                    return '<div style="padding:8px 12px;font-size:12px;line-height:1.5;">' +
                        '<strong>' + name + '</strong><br/>' +
                        'Bs ' + Number(val).toLocaleString('es-BO', {minimumFractionDigits: 2}) + ' (' + pct + '%)' +
                        '</div>';
                }
            },
            grid: { borderColor: '#eef0f4', padding: { right: 20 } },
            legend: { show: false }
        }).render();
    } else if (elPie) {
        elPie.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line fs-1 d-block mb-2"></i>Sin datos para el período seleccionado</div>';
    }
} catch (e) { console.error('chart-pie-inscripciones error:', e); }

@if($soloAuto->isNotEmpty())
try {
    var autoLabels = @json($soloAuto->pluck('programa_label'));
    var autoMonto  = @json($soloAuto->pluck('auto_monto')->values()->map(fn($v) => (float) $v));

    var elAuto = document.getElementById('chart-auto-programas');
    if (elAuto && autoLabels.length) {
        var chartHeight = Math.max(420, autoLabels.length * 40);
        new ApexCharts(elAuto, {
            chart: { height: chartHeight, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '62%' } },
            dataLabels: { enabled: true, formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; }, style: { fontSize: '10px', fontWeight: 600 } },
            series: [
                { name: 'Autogestión', data: autoMonto, color: '#f59e0b' }
            ],
            xaxis: {
                categories: autoLabels,
                labels: { formatter: function (v) { return v != null ? '' + v : ''; }, style: { fontSize: '11px' }, maxHeight: 80 }
            },
            yaxis: { labels: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; }, style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2}) : ''; } } },
            grid: { borderColor: '#eef1f8', strokeDashArray: 4, padding: { right: 25 } },
            legend: { position: 'bottom', horizontalAlign: 'center', markers: { width: 10, height: 10, radius: 3 }, itemMargin: { horizontal: 12 } }
        }).render();
    }
} catch (e) { console.error('chart-auto-programas error:', e); }
@endif

@if($programasResumen->isNotEmpty())
try {
    var totLabels = @json($programasResumen->pluck('programa_label'));
    var totComp   = @json($programasResumen->pluck('comp_monto')->values()->map(fn($v) => (float) $v));
    var totAuto   = @json($programasResumen->pluck('auto_monto')->values()->map(fn($v) => (float) $v));

    var totTotal = totComp.map(function (v, i) { return v + totAuto[i]; });

    var elTotal = document.getElementById('chart-total-programas');
    if (elTotal && totLabels.length) {
        var chartHeight = Math.max(420, totLabels.length * 40);
        new ApexCharts(elTotal, {
            chart: { height: chartHeight, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '62%' } },
            dataLabels: {
                enabled: true,
                formatter: function (v, opts) {
                    if (v == null || isNaN(v)) return '';
                    if (opts.seriesIndex === 0) return '';
                    if (opts.seriesIndex === 2) return 'Total: Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0});
                    return 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0});
                },
                offsetX: 0,
                style: { fontSize: '10px', fontWeight: 600 },
                background: { enabled: true, foreColor: '#fff', padding: 4, borderRadius: 4, borderWidth: 0, opacity: 0.8 }
            },
            series: [
                { name: 'Asignado (empresa)', data: totComp, color: '#0d8574' },
                { name: 'Autogestión', data: totAuto, color: '#f59e0b' },
                { name: 'Total invertido', data: totTotal, color: '#163b6b' }
            ],
            xaxis: {
                categories: totLabels,
                labels: { formatter: function (v) { return v != null ? '' + v : ''; }, style: { fontSize: '11px' }, maxHeight: 80 }
            },
            yaxis: { labels: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 0}) : ''; }, style: { fontSize: '11px' } } },
            tooltip: {
                y: { formatter: function (v) { return v != null && !isNaN(v) ? 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2}) : ''; } }
            },
            grid: { borderColor: '#eef1f8', strokeDashArray: 4, padding: { right: 25 } },
            legend: { position: 'bottom', horizontalAlign: 'center', markers: { width: 10, height: 10, radius: 3 }, itemMargin: { horizontal: 12 } }
        }).render();
    }
} catch (e) { console.error('chart-total-programas error:', e); }
@endif

</script>
@endpush
