@extends('admin.layouts.master')

@section('title', 'Asesor: ' . $asesor->apellidos . ', ' . $asesor->nombres . ' - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.show-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 16px;
    padding: 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 12px 32px -14px rgba(64,81,137,.5);
    margin-bottom: 1.25rem;
}
.show-hero::before {
    content: ""; position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.show-hero::after {
    content: ""; position: absolute;
    bottom: -80px; left: 20%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
    border-radius: 50%;
}
.show-hero > * { position: relative; z-index: 1; }
.profile-avatar-lg {
    width: 78px; height: 78px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.75rem;
    background: rgba(255,255,255,.2);
    color: #fff;
    border: 2px solid rgba(255,255,255,.35);
    backdrop-filter: blur(4px);
    box-shadow: 0 10px 24px -10px rgba(0,0,0,.3);
}
.show-hero h3 { color: #fff; font-weight: 700; letter-spacing: .2px; line-height: 1.3; }
.hero-info-pills { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .65rem; }
.hero-info-pill {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: .72rem; font-weight: 600;
    padding: .35em .75em;
    border-radius: 999px;
    backdrop-filter: blur(4px);
    display: inline-flex; align-items: center; gap: .35rem;
}
.btn-volver {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff; font-weight: 600;
    backdrop-filter: blur(4px);
    transition: all .2s ease;
}
.btn-volver:hover { background: rgba(255,255,255,.28); color: #fff; transform: translateY(-1px); }
.hero-stat {
    background: #ffffff;
    border: 1px solid rgba(255,255,255,.7);
    border-radius: 12px;
    padding: .85rem 1rem;
    box-shadow: 0 12px 26px -20px rgba(3,18,38,.8);
    transition: all .2s ease;
    text-align: center;
}
.hero-stat:hover { transform: translateY(-2px); box-shadow: 0 16px 30px -18px rgba(3,18,38,.85); }
.hero-stat .stat-val { color: #12345b; font-weight: 800; font-size: 1.4rem; line-height: 1.1; font-variant-numeric: tabular-nums; }
.hero-stat .stat-lbl { color: #667085; font-size: .68rem; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; margin-top: 2px; }

/* ===== Stat cards (bajo hero) ===== */
.stat-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
    position: relative;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before {
    content: ""; position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: var(--stat-accent, #405189);
}
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .stat-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #878a99; font-weight: 700;
    margin-bottom: .4rem;
}
.stat-card .stat-value { font-size: 1.2rem; font-weight: 700; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }
.stat-card .stat-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px; font-size: 1.25rem;
}

/* ===== Filter card ===== */
.filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: #2a2f34; }
.filter-card .form-label {
    font-size: .72rem; text-transform: uppercase; letter-spacing: .5px;
    font-weight: 700; color: #878a99; margin-bottom: .35rem;
}
.filter-card .input-group-text {
    background: #fff; border: 1px solid #e9ebec; border-right: 0; color: #878a99;
}
.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid #e9ebec; background-color: #fff; font-weight: 500; transition: all .2s ease;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color: #405189; box-shadow: 0 0 0 .15rem rgba(64,81,137,.12); }
.filter-card .input-group:focus-within .input-group-text { border-color: #405189; color: #405189; }
[data-layout-mode="dark"] .filter-card .input-group-text,
[data-layout-mode="dark"] .filter-card .form-control,
[data-layout-mode="dark"] .filter-card .form-select { background-color: #1d2125; border-color: #2a2f34; color: #ced4da; }

/* ===== Program block ===== */
.program-block {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.program-block:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .program-block { background: #212529; border-color: #2a2f34; }
.program-block-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
}
[data-layout-mode="dark"] .program-block-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.program-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.program-title { font-weight: 700; color: #212529; font-size: .95rem; margin: 0; line-height: 1.2; }
[data-layout-mode="dark"] .program-title { color: #fff; }
.program-meta { font-size: .72rem; color: #878a99; margin-top: 2px; display: inline-flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
.program-count-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff;
    border-radius: 999px;
    padding: .35em .85em;
    font-size: .78rem; font-weight: 700;
    box-shadow: 0 4px 10px -4px rgba(41,156,219,.55);
}

/* Tabla */
.modern-table { margin: 0; width: 100%; }
.modern-table thead th {
    background: #fafbfd !important;
    font-weight: 700; font-size: .68rem;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .75rem .75rem;
    border-top: 0;
}
.modern-table tbody td {
    padding: .7rem .75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .82rem; border-top: 0;
}
.modern-table tbody tr { transition: background-color .15s ease; }
.modern-table tbody tr:hover { background-color: #f8f9fc; }
.modern-table tbody tr:last-child td { border-bottom: none; }
[data-layout-mode="dark"] .modern-table thead th { background: #1d2125 !important; border-bottom-color: #2a2f34 !important; }
[data-layout-mode="dark"] .modern-table tfoot tr { background: #1a1d20 !important; border-top-color: #2a2f34 !important; }
[data-layout-mode="dark"] .modern-table tfoot td { color: #ced4da !important; }
[data-layout-mode="dark"] .modern-table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .modern-table tbody tr:hover { background-color: #1a1d20; }

.mini-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .7rem; color: #fff; flex-shrink: 0;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
}
.mini-avatar.male { background: linear-gradient(135deg, #3577f1 0%, #299cdb 100%); }
.mini-avatar.female { background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); }
.cell-nombre .nombre { font-weight: 600; color: #212529; font-size: .82rem; line-height: 1.2; }
.cell-nombre .ci-info { font-size: .68rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .cell-nombre .nombre { color: #fff; }

.estado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 700;
    padding: .3em .75em; border-radius: 999px;
    border: 1px solid;
}
.estado-pill::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.est-activo { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.est-retirado { background: #fee2e2; color: #f06548; border-color: #fecaca; }
.plan-contado { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.plan-regular { background: #eef1f8; color: #405189; border-color: #c3cfe2; }
.sexo-male { background: #dbeafe; color: #3577f1; border-color: #c3ddfa; }
.sexo-female { background: #fce7f3; color: #ec4899; border-color: #fbcfe8; }
.sexo-muted { background: #f1f3f5; color: #878a99; border-color: #e9ebec; }

/* Empty */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px;
    border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 80px; height: 80px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

/* Comprobantes table */
.tabla-pub { width: 100%; }
.tabla-pub th, .tabla-pub td { vertical-align: middle; padding:.7rem .65rem; }
.tabla-pub thead th { background:linear-gradient(180deg,#f8fafc,#eef1f8) !important; font-size:.66rem; font-weight:800; text-transform:uppercase; letter-spacing:.45px; color:#5d6478; border-bottom:1px solid #e6eaf0; }
[data-layout-mode="dark"] .tabla-pub thead th { background:#1a1d20 !important; border-bottom-color:#2a2f34; }
.tabla-pub tbody tr { border-bottom:1px solid #f1f3f5; transition:background-color .15s ease; }
.tabla-pub tbody tr:hover { background-color:#f8fafc; }
.badge-quincena-1 { background:#dbeafe; color:#1e40af; }
.badge-quincena-2 { background:#fef3c7; color:#92400e; }
.badge-quincena-1, .badge-quincena-2 { display:inline-flex; align-items:center; font-weight:800; border-radius:999px; }
.file-preview { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .7rem; background:#e8f4fd; border:1px solid #c3ddfa; border-radius:8px; font-size:.74rem; font-weight:700; color:#1f6fbf; text-decoration:none; transition:all .15s ease; }
.file-preview:hover { background:#1f6fbf; color:#fff; border-color:#1f6fbf; }

/* Card comprobantes de publicidad */
.pub-comp-card { border:1px solid #eef0f4; border-radius:16px; background:#fff; box-shadow:0 10px 30px -22px rgba(16,24,40,.5); overflow:hidden; }
.pub-comp-head { display:flex; align-items:center; gap:.75rem; padding:1rem 1.25rem; border-bottom:1px solid #f1f3f5; }
.pub-comp-head .pc-ic { width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; background:#e6f7ee; color:#0a7d5a; flex-shrink:0; }
.pub-comp-head .pc-title { font-size:.95rem; font-weight:800; color:#1b2537; margin:0; }
.pub-comp-head .pc-sub { font-size:.72rem; color:#98a2b3; margin:0; }
.estado-dot { display:inline-flex; align-items:center; gap:5px; font-size:.7rem; font-weight:800; padding:.28em .7em; border-radius:999px; border:1px solid; }
.estado-dot.pendiente { background:#fff4d6; color:#8a6100; border-color:#fbe3a3; }
.estado-dot.aprobado { background:#e6f7ee; color:#067647; border-color:#b7e8d6; }
.estado-dot.rechazado { background:#fee4e2; color:#b42318; border-color:#fbc9c4; }
.plat-chip { display:inline-flex; align-items:center; gap:4px; font-size:.68rem; font-weight:700; padding:.2em .6em; border-radius:7px; background:#eef1f8; color:#405189; border:1px solid #e0e6f2; }
</style>
@endpush

@section('content')
<div class="mkt-page">
@php
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $fechaEs = function ($val) use ($mesesEs) {
        if (!$val) return '—';
        $t = is_numeric($val) ? (int)$val : strtotime((string)$val);
        if (!$t) return $val;
        return (int)date('d', $t) . ' de ' . ($mesesEs[(int)date('n', $t) - 1] ?? '') . ' del ' . date('Y', $t);
    };
    $ini = strtoupper(substr($asesor->nombres ?? '', 0, 1) . substr($asesor->apellidos ?? '', 0, 1));
@endphp

<div class="show-hero">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar-lg">{{ $ini }}</div>
                <div style="min-width:0;">
                    <h3 class="fs-22 mb-1">{{ $asesor->apellidos }}, {{ $asesor->nombres }}</h3>
                    <div class="hero-info-pills">
                        <span class="hero-info-pill"><i class="ri-user-star-line"></i>@if(!empty($asesor->cargo_nombre)) {{ $asesor->cargo_nombre }} @else Asesor de Marketing @endif</span>
                        @if($metaMensual > 0)
                            <span class="hero-info-pill" style="background:rgba(247,184,75,.22);border-color:rgba(247,184,75,.35);"><i class="ri-flag-2-line"></i>Meta: {{ number_format($metaMensual, 0, ',', '.') }} pts/mes</span>
                        @endif
                        @if(!empty($asesor->celular))
                            <span class="hero-info-pill"><i class="ri-phone-line"></i>{{ $asesor->celular }}</span>
                        @endif
                        @if(!empty($asesor->correo))
                            <span class="hero-info-pill"><i class="ri-mail-line"></i>{{ $asesor->correo }}</span>
                        @endif
                        @if(!empty($asesor->ci))
                            <span class="hero-info-pill"><i class="ri-passport-line"></i>CI: {{ $asesor->ci }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-2">
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $stats['total'] }}</div><div class="stat-lbl">Inscritos</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $stats['programas'] }}</div><div class="stat-lbl">Programas</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $stats['activos'] }}</div><div class="stat-lbl">Activos</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $stats['retirados'] }}</div><div class="stat-lbl">Retirados</div></div></div>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-end">
        <a href="{{ route('admin.marketing.estadisticas') }}" class="btn btn-volver d-inline-flex align-items-center gap-1">
            <i class="ri-arrow-left-line"></i> Volver a Marketing
        </a>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-2">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                            <i class="ri-equalizer-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtrar inscripciones</h5>
                </div>
            </div>
            <div class="card-body">
                <form id="filter-form" method="GET" action="{{ route('admin.marketing.asesor-show', $asesor->id) }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Gestión</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                            <select name="gestion" class="form-select">
                                <option value="">Todas</option>
                                @foreach($gestiones as $g)
                                    <option value="{{ $g }}" @if((string)$gestionFiltro === (string)$g) selected @endif>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mes</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                            <select name="mes" class="form-select">
                                <option value="">Todos los meses</option>
                                @foreach($meses as $num => $nombre)
                                    <option value="{{ $num }}" @if((string)$mesFiltro === (string)$num) selected @endif>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-price-tag-3-line"></i></span>
                            <select name="tipo" class="form-select">
                                <option value="general" @selected($tipo === 'general')>General</option>
                                <option value="upi" @selected($tipo === 'upi')>Solo UPI Diplomados</option>
                                <option value="upi-auto" @selected($tipo === 'upi-auto')>UPI Autogestionados</option>
                                <option value="otros" @selected($tipo === 'otros')>Programas Sucre central</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('admin.marketing.asesor-show', $asesor->id) }}?gestion={{ date('Y') }}&mes={{ date('m') }}" class="btn btn-soft-danger w-50 d-flex align-items-center justify-content-center gap-1" title="Volver al mes actual">
                            <i class="ri-filter-off-line"></i> Mes Actual
                        </a>
                        <button type="submit" class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-1">
                            <i class="ri-filter-3-line"></i> Aplicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="asesor-content">
    @include('admin.marketing.asesores._content')
</div>

{{-- Modal de previsualización de archivo --}}
<div class="modal fade" id="modalFilePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-file-text-line me-1"></i>Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="min-height:400px;">
                <div id="filePreviewContent" class="d-flex align-items-center justify-content-center text-muted" style="min-height:400px;">
                    <div class="text-center p-4">
                        <i class="ri-loader-4-line ri-2x spin"></i>
                        <p class="mt-2 mb-0">Cargando archivo...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mesesLbl = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // Gráfico de inscripciones por mes
    const el = document.getElementById('chart-asesor-meses');
    if (el) {
        const c = @json($chartMeses);
        new ApexCharts(el, {
            chart: { height: 340, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Otras sedes', type: 'column', data: c.otrasSedes },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#1f6fbf', '#0d8574', '#2a5d97', '#163b6b'],
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
                    horizontal: false, columnWidth: '60%',
                    borderRadius: 3, borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true, enabledOnSeries: [3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#163b6b'] },
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
    const elPuntos = document.getElementById('chart-asesor-puntos');
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
        }
    }

    // Gráfico de puntos reales por mes
    const elPuntosReales = document.getElementById('chart-asesor-puntos-reales');
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
        }
    }

    // === Gráfico de Gastos de Autogestión por Programa ===
    try {
        var autoLabels = @json(($soloAutoAsesor ?? collect())->pluck('programa_label'));
        var autoMonto  = @json(($soloAutoAsesor ?? collect())->pluck('auto_monto')->values()->map(fn($v) => (float) $v));
        var elAutoAsesor = document.getElementById('chart-auto-asesor');
        if (elAutoAsesor && autoLabels.length) {
            var chartHeight = Math.max(420, autoLabels.length * 40);
            new ApexCharts(elAutoAsesor, {
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
                grid: { borderColor: '#eef0f4', padding: { right: 25 } },
                legend: { position: 'top' }
            }).render();
        }
    } catch (e) { console.error('chart-auto-asesor error:', e); }

    // === Gráfico de Total Invertido por Programa (Asignado + Autogestión) ===
    try {
        var totLabels = @json(($programasResumenAsesor ?? collect())->pluck('programa_label'));
        var totComp   = @json(($programasResumenAsesor ?? collect())->pluck('comp_monto')->values()->map(fn($v) => (float) $v));
        var totAuto   = @json(($programasResumenAsesor ?? collect())->pluck('auto_monto')->values()->map(fn($v) => (float) $v));
        if (totLabels.length) {
            var totTotal = totComp.map(function (v, i) { return v + totAuto[i]; });
            var elTotalAsesor = document.getElementById('chart-total-asesor');
            if (elTotalAsesor) {
                var chartHeight = Math.max(420, totLabels.length * 40);
                new ApexCharts(elTotalAsesor, {
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
                    grid: { borderColor: '#eef0f4', padding: { right: 25 } },
                    legend: { position: 'top' }
                }).render();
            }
        }
    } catch (e) { console.error('chart-total-asesor error:', e); }

    // === File preview modal ===
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-preview-file');
        if (!btn) return;
        e.preventDefault();
        const url = btn.dataset.url;
        const ext = url.split('.').pop().toLowerCase();
        const content = document.getElementById('filePreviewContent');
        if (['jpg','jpeg','png','webp'].includes(ext)) {
            content.innerHTML = '<img src="' + url + '" class="img-fluid" style="max-height:80vh;object-fit:contain;" alt="Comprobante">';
        } else if (ext === 'pdf') {
            content.innerHTML = '<iframe src="' + url + '" class="w-100" style="height:80vh;border:none;"></iframe>';
        } else {
            content.innerHTML = '<div class="text-center p-4"><i class="ri-file-download-line ri-2x"></i><p class="mt-2"><a href="' + url + '" target="_blank" class="btn btn-primary"><i class="ri-download-line me-1"></i>Descargar archivo</a></p></div>';
        }
        const modal = new bootstrap.Modal(document.getElementById('modalFilePreview'));
        modal.show();
    });

    document.getElementById('modalFilePreview')?.addEventListener('hidden.bs.modal', function () {
        document.getElementById('filePreviewContent').innerHTML = '<div class="text-center p-4"><i class="ri-loader-4-line ri-2x spin"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>';
    });

    // === Real-time validation ===
    document.querySelectorAll('.form-subir-comprobante input, .form-subir-comprobante select, .form-subir-comprobante textarea').forEach(el => {
        el.addEventListener('blur', function () { validateFieldPub(this); });
        el.addEventListener('change', function () { validateFieldPub(this); });
        if (el.type === 'file') {
            el.addEventListener('change', function () { validateFieldPub(this); });
        }
    });

    function validateFieldPub(el) {
        const form = el.closest('form');
        let valid = true;
        const val = el.value.trim();
        if (el.classList.contains('validate-required')) {
            if (!val) { valid = false; }
        }
        if (el.name === 'fecha_fin' && el.classList.contains('validate-after')) {
            const inicio = form.querySelector('[name=fecha_inicio]');
            if (inicio && val && inicio.value && val < inicio.value) {
                valid = false;
            }
        }
        if (el.name === 'enlace_campana' && el.classList.contains('validate-url')) {
            if (val && !/^https?:\/\/.+/i.test(val)) {
                valid = false;
            }
        }
        if (el.type === 'file' && el.classList.contains('validate-file')) {
            if (el.files.length === 0) { valid = false; }
            else {
                const maxSize = 5 * 1024 * 1024;
                if (el.files[0].size > maxSize) { valid = false; }
            }
        }
        if (!el.checkValidity() || !valid) {
            el.classList.add('is-invalid');
            el.classList.remove('is-valid');
        } else {
            el.classList.remove('is-invalid');
            el.classList.add('is-valid');
        }
    }

    // === AJAX upload from profile ===
    const uploadForm = document.querySelector('#modalSubirAsesor .form-subir-comprobante');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            let allValid = true;
            this.querySelectorAll('input, select, textarea').forEach(el => {
                validateFieldPub(el);
                if (el.classList.contains('is-invalid') || (el.hasAttribute('required') && !el.value.trim())) {
                    allValid = false;
                }
            });
            if (!allValid) {
                this.querySelector('.is-invalid')?.focus();
                return;
            }
            const spinner = document.getElementById('spinner-asesor');
            const btn = this.querySelector('.btn-subir');
            const errorGlobal = document.getElementById('error-global-asesor');
            spinner?.classList.remove('d-none');
            btn.disabled = true;
            errorGlobal?.classList.add('d-none');
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(async res => {
                let data; try { data = await res.json(); } catch (e) { data = null; }
                if (!data) throw new Error('Respuesta inválida del servidor (código ' + res.status + ').');
                return { status: res.status, data };
            })
            .then(({ status, data }) => {
                spinner?.classList.add('d-none');
                btn.disabled = false;
                if (status === 422 && data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = this.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.closest('.col-md-4,.col-md-6,.col-md-12')?.querySelector('.invalid-feedback');
                            if (feedback) feedback.textContent = data.errors[field][0];
                        }
                    });
                    if (errorGlobal) {
                        errorGlobal.textContent = 'Corrija los errores marcados en el formulario.';
                        errorGlobal.classList.remove('d-none');
                    }
                    return;
                }
                if (status >= 400) {
                    if (errorGlobal) {
                        errorGlobal.textContent = data?.message || 'Error del servidor.';
                        errorGlobal.classList.remove('d-none');
                    }
                    return;
                }
                if (data.success) {
                    this.reset();
                    this.querySelectorAll('.is-valid, .is-invalid').forEach(el => { el.classList.remove('is-valid', 'is-invalid'); });
                    const bsModal = bootstrap.Modal.getInstance(document.getElementById('modalSubirAsesor'));
                    bsModal?.hide();
                    const toastHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;max-width:500px;" role="alert">' +
                        '<i class="ri-check-double-line me-1"></i>' + data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    document.body.insertAdjacentHTML('beforeend', toastHtml);
                    setTimeout(() => document.querySelector('.alert-success')?.remove(), 4000);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    if (errorGlobal) {
                        errorGlobal.textContent = data.message || 'Error al subir el comprobante.';
                        errorGlobal.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                spinner?.classList.add('d-none');
                btn.disabled = false;
                if (errorGlobal) {
                    errorGlobal.textContent = err.message || 'Error de conexión. Intente nuevamente.';
                    errorGlobal.classList.remove('d-none');
                }
            });
        });
    }

    // === AJAX verification from profile ===
    document.querySelectorAll('#asesor-content .form-verificar-comprobante').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const modal = this.closest('.modal');
            const comprobanteId = modal.dataset.comprobanteId;
            const btn = this.querySelector('.btn-guardar-verif');
            const errorDiv = modal.querySelector('.error-verif');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            errorDiv?.classList.add('d-none');
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(async res => {
                let data; try { data = await res.json(); } catch (e) { data = null; }
                if (!data) throw new Error('Respuesta inválida del servidor.');
                return { status: res.status, data };
            })
            .then(({ status, data }) => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (status === 422 && data.errors) {
                    let msg = Object.values(data.errors).flat().join('<br>');
                    if (errorDiv) { errorDiv.innerHTML = msg; errorDiv.classList.remove('d-none'); }
                    return;
                }
                if (data.success) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    bsModal?.hide();
                    const toastHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;max-width:500px;" role="alert">' +
                        '<i class="ri-check-double-line me-1"></i>' + data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    document.body.insertAdjacentHTML('beforeend', toastHtml);
                    setTimeout(() => document.querySelector('.alert-success')?.remove(), 4000);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    if (errorDiv) {
                        errorDiv.textContent = data.message || 'Error al verificar.';
                        errorDiv.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (errorDiv) {
                    errorDiv.textContent = err.message || 'Error de conexión.';
                    errorDiv.classList.remove('d-none');
                }
            });
        });
    });

    // === Comprobantes pagination ===
    const comprobantesBody = document.getElementById('comprobantes-body');
    if (comprobantesBody) {
        const comprobantesUrl = '{{ route("admin.marketing.publicidad.comprobantes", $asesor->id) }}';

        function loadComprobantes(page) {
            fetch(comprobantesUrl + '?page=' + page, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    comprobantesBody.innerHTML = data.rows_html;
                    var wrapper = comprobantesBody.closest('.table-responsive');
                    var existingPagination = wrapper.parentElement.querySelector('.comprobantes-pagination-wrap');
                    if (existingPagination) existingPagination.remove();
                    if (data.pagination_html) {
                        wrapper.insertAdjacentHTML('afterend', data.pagination_html);
                    }
                }
            })
            .catch(function () {
                comprobantesBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-3"><i class="ri-error-warning-line me-1"></i>Error al cargar comprobantes</td></tr>';
            });
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest('.comprobantes-pagination-wrap .pagination a');
            if (!link) return;
            e.preventDefault();
            var href = link.getAttribute('href');
            if (!href) return;
            var m = href.match(/[?&]page=(\d+)/);
            if (m) loadComprobantes(parseInt(m[1]));
        });

        loadComprobantes(1);
    }
});
</script>
@endpush
</div>{{-- /mkt-page --}}
@endsection
