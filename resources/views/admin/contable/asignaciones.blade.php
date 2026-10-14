@extends('admin.layouts.master')

@section('title', 'Asignaciones del Mes - ' . config('app.name'))

@push('css')
<style>
.asig-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.asig-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.asig-hero::after  { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.asig-hero > * { position: relative; z-index: 1; }
.asig-hero .hero-icon {
    width: 56px; height: 56px;
    display:inline-flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.25);
    border-radius:14px;
    font-size:1.6rem;
    backdrop-filter: blur(4px);
}
.asig-hero h4 { color:#fff; font-weight:700; margin:0; }
.asig-hero .hero-sub { color: rgba(255,255,255,.85); font-size:.88rem; margin: 2px 0 0; }
.asig-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.asig-hero .breadcrumb-item, .asig-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.asig-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }

/* Filtro */
.filter-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); margin-bottom:1.25rem; }
[data-layout-mode="dark"] .filter-card { background:#1d2125; border-color:#2a2f34; }
.filter-card .card-body { padding: .9rem 1.25rem; }
.filter-card label { font-size:.65rem; text-transform:uppercase; letter-spacing:.45px; font-weight:700; color:#878a99; margin-bottom:2px; display:block; }
.filter-card select {
    border:1px solid #e9ebec; border-radius:8px;
    padding:.45rem .75rem; font-size:.85rem; font-weight:700; color:#405189;
    min-width: 160px;
}
.filter-card select:focus { outline:0; border-color:#299cdb; box-shadow: 0 0 0 .15rem rgba(41,156,219,.15); }
[data-layout-mode="dark"] .filter-card select { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

/* KPI cards */
.kpi { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.15rem; height:100%; position:relative; overflow:hidden; transition: all .2s ease; }
.kpi::before { content:""; position:absolute; top:0; left:0; height:100%; width:4px; background: var(--k-accent, #299cdb); }
.kpi:hover { transform: translateY(-2px); box-shadow: 0 12px 26px -14px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .kpi { background:#1d2125; border-color:#2a2f34; }
.kpi .k-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.kpi .k-val { font-size:1.4rem; font-weight:800; color:#212529; line-height:1.1; }
[data-layout-mode="dark"] .kpi .k-val { color:#fff; }
.kpi .k-sub { font-size:.7rem; color:#878a99; margin-top:2px; }
.kpi .k-ic {
    width:42px; height:42px; border-radius:12px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:1.2rem; background: var(--k-bg, #eef1f8); color: var(--k-color, #405189);
}

/* Pills de filtro de estado */
.estado-filter-row { display:flex; gap:.4rem; flex-wrap:wrap; margin-bottom: .85rem; }
.estado-filter {
    border:1px solid #eef0f4; background:#fff; color:#495057;
    border-radius:999px; padding:.4rem .85rem;
    font-size:.78rem; font-weight:700;
    transition: all .15s ease;
    display:inline-flex; align-items:center; gap:.4rem;
    cursor:pointer;
}
.estado-filter:hover { border-color:#c3cfe2; color:#405189; }
.estado-filter .bdg { background:#eef0f4; color:#495057; border-radius:999px; padding:.1em .55em; font-size:.65rem; font-weight:800; }
.estado-filter.active { color:#fff; border-color:transparent; }
.estado-filter.active.is-all  { background: linear-gradient(135deg, #405189, #5e72b8); }
.estado-filter.active.is-venc { background: linear-gradient(135deg, #f06548, #ff8a6c); }
.estado-filter.active.is-hoy  { background: linear-gradient(135deg, #f7b84b, #ffb547); }
.estado-filter.active.is-prox { background: linear-gradient(135deg, #299cdb, #3577f1); }
.estado-filter.active.is-pag  { background: linear-gradient(135deg, #0ab39c, #14b8a6); }
.estado-filter.active .bdg { background: rgba(255,255,255,.25); color:#fff; }
[data-layout-mode="dark"] .estado-filter { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

/* Card por programa */
.prog-card {
    border:1px solid #eef0f4; border-radius:14px; background:#fff;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all .2s ease;
}
.prog-card:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .prog-card { background:#1d2125; border-color:#2a2f34; }
.prog-card-head {
    display:flex; align-items:center; gap:.85rem;
    padding: .9rem 1.15rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .prog-card-head { background:#1a1d20; border-bottom-color:#2a2f34; }
.prog-card-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #405189, #5e72b8);
    color:#fff;
    border-radius: 11px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 6px 14px -6px rgba(64,81,137,.55);
}
.prog-card-info { min-width: 0; flex: 1; }
.prog-card-name { font-weight: 800; color: #212529; font-size: .95rem; line-height: 1.2; }
[data-layout-mode="dark"] .prog-card-name { color:#fff; }
.prog-card-meta { font-size: .72rem; color: #878a99; margin-top: 2px; }
.prog-card-totals { display: flex; flex-wrap: wrap; gap: .5rem; }
.prog-stat-pill {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.7rem; font-weight:800;
    padding:.3em .8em; border-radius:999px;
    border:1px solid;
    white-space: nowrap;
}
.psp-esp { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
.psp-cob { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.psp-pen { background:#fee2e2; color:#f06548; border-color:#fecaca; }
.psp-c-venc { background:#fef2f2; color:#f06548; border-color:#fecaca; }
.psp-c-hoy  { background:#fffbeb; color:#f7b84b; border-color:#fde68a; }
.psp-c-prox { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
.psp-c-pag  { background:#ecfdf5; color:#0ab39c; border-color:#a7f3d8; }

.asig-table { margin: 0; width: 100%; }
.asig-table thead th {
    background: #f8f9fc !important;
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .65rem .85rem;
}
.asig-table tbody td { padding: .65rem .85rem; vertical-align: middle; border-bottom: 1px solid #f1f3f5; font-size: .85rem; }
.asig-table tbody tr.is-venc { background: linear-gradient(90deg, rgba(240,101,72,.08), rgba(240,101,72,0)); }
.asig-table tbody tr.is-hoy  { background: linear-gradient(90deg, rgba(247,184,75,.08), rgba(247,184,75,0)); }
.asig-table tbody tr.is-pag  { opacity: .75; }
.asig-table tbody tr { transition: background-color .15s ease; }
.asig-table tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .asig-table thead th { background:#1d2125 !important; color:#adb5bd; border-bottom-color:#2a2f34 !important; }
[data-layout-mode="dark"] .asig-table tbody td { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .asig-table tbody tr:hover { background-color:#1a1d20; }

.part-avatar {
    width:30px; height:30px; border-radius:50%;
    background: linear-gradient(135deg, #405189, #5e72b8);
    color:#fff; font-weight:800; font-size:.7rem;
    display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.tipo-pill {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.7rem; font-weight:700;
    padding:.22em .65em; border-radius:6px;
    border:1px solid;
}
.tp-mat { background:#dbeafe; color:#299cdb; border-color:#bae6fd; }
.tp-col { background:#f3f5f9; color:#495057; border-color:#e9ebec; }

.venc-pill {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.7rem; font-weight:700;
    padding:.22em .7em; border-radius:999px;
    border:1px solid;
    white-space: nowrap;
}
.vp-venc { background:#fef2f2; color:#f06548; border-color:#fecaca; }
.vp-hoy  { background:#fffbeb; color:#f7b84b; border-color:#fde68a; }
.vp-prox { background:#f0f9ff; color:#299cdb; border-color:#bae6fd; }
.vp-pag  { background:#ecfdf5; color:#0ab39c; border-color:#a7f3d8; }

/* Badge cantidad de cuotas + botón detalle */
.cuotas-count-badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.7rem; font-weight:800;
    padding:.22em .65em; border-radius:999px;
    background:#eef1f8; color:#405189; border:1px solid #c3cfe2;
}
.btn-ver-cuotas {
    display:inline-flex; align-items:center; gap:4px;
    font-size:.72rem; font-weight:700;
    padding:.28em .7em; border-radius:8px;
    border:1px solid #c3cfe2; background:#fff; color:#405189;
    transition: all .15s ease;
    cursor: pointer;
}
.btn-ver-cuotas:hover { background:#405189; color:#fff; border-color:#405189; }

/* Modal detalle cuotas */
.cuota-detail-row {
    display:flex; align-items:center; gap:.65rem;
    padding:.65rem .85rem; border:1px solid #eef0f4; border-radius:10px;
    margin-bottom:.5rem; background:#fff;
}
[data-layout-mode="dark"] .cuota-detail-row { background:#1d2125; border-color:#2a2f34; }
.cuota-detail-row:hover { border-color:#c3cfe2; }
.cuota-detail-info { flex:1; min-width:0; }
.cuota-detail-amounts { display:flex; gap:1rem; flex-wrap:wrap; font-size:.8rem; }
.cuota-detail-amounts .amt-lbl { color:#878a99; font-size:.65rem; text-transform:uppercase; font-weight:700; letter-spacing:.3px; }
.cuota-detail-amounts .amt-val { font-weight:800; }

/* Empty / Sin fechas */
.empty-state {
    text-align: center; padding: 3rem 1rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px; border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 80px; height: 80px; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff; border-radius: 50%; font-size: 2.5rem; color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

.warning-card {
    border: 1px solid #fde68a;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .warning-card { background: #2a2418; border-color: #4a3f1f; }
.warning-card-head {
    display: flex; align-items: center; gap: .65rem;
    margin-bottom: .65rem;
    padding-bottom: .5rem;
    border-bottom: 1px dashed #fde68a;
}
[data-layout-mode="dark"] .warning-card-head { border-bottom-color: #4a3f1f; }
.warning-card-icon {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #f7b84b, #ffb547);
    color: #fff; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.sin-fechas-list { display: grid; gap: .5rem; grid-template-columns: 1fr; }
@media (min-width: 768px) { .sin-fechas-list { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1200px) { .sin-fechas-list { grid-template-columns: repeat(3, 1fr); } }
.sin-fecha-item {
    border: 1px solid #fde68a;
    background: #fff;
    border-radius: 10px;
    padding: .65rem .85rem;
    display: flex; align-items: center; gap: .55rem;
    transition: all .15s ease;
}
.sin-fecha-item:hover { border-color: #f7b84b; transform: translateY(-1px); box-shadow: 0 6px 14px -6px rgba(247,184,75,.45); }
[data-layout-mode="dark"] .sin-fecha-item { background: #1d2125; border-color: #4a3f1f; }
.sin-fecha-icon {
    width: 34px; height: 34px;
    background: #fffbeb; color: #f7b84b;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.sin-fecha-info { min-width: 0; flex: 1; }
.sin-fecha-name { font-weight: 700; color: #212529; font-size: .82rem; line-height: 1.2; }
[data-layout-mode="dark"] .sin-fecha-name { color: #fff; }
.sin-fecha-meta { font-size: .68rem; color: #878a99; }
.sin-fecha-link {
    background: #fef3c7; color: #d97706;
    border-radius: 8px; padding: .25rem .55rem;
    font-size: .7rem; font-weight: 700;
    transition: all .15s ease;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: .25rem;
}
.sin-fecha-link:hover { background: #f7b84b; color: #fff; }

/* ===== Colapsable prog-card ===== */
.prog-card-head { cursor: pointer; user-select: none; }
.prog-card-head .collapse-toggle {
    width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:8px; background:rgba(0,0,0,.06); color:#405189;
    font-size:.9rem; transition:transform .3s ease, background .15s ease; flex-shrink:0;
}
.prog-card-head .collapse-toggle.collapsed { transform: rotate(-90deg); }
.prog-card-head:hover .collapse-toggle { background:rgba(0,0,0,.12); }
[data-layout-mode="dark"] .prog-card-head .collapse-toggle { background:rgba(255,255,255,.08); color:#adb5bd; }
[data-layout-mode="dark"] .prog-card-head:hover .collapse-toggle { background:rgba(255,255,255,.15); }
.prog-card-body { transition: max-height .35s ease, opacity .25s ease; overflow: hidden; max-height: 20000px; opacity:1; }
.prog-card-body.collapsed { max-height: 0 !important; opacity: 0; }
.prog-card-body.collapsing { max-height: var(--ch, 20000px); opacity: 1; }

/* ===== Navegación rápida ===== */
.prog-nav-wrap {
    background:#fff; border:1px solid #eef0f4; border-radius:14px;
    padding:.65rem 1rem; margin-bottom:1rem;
    display:flex; align-items:center; gap:.65rem; flex-wrap:wrap;
}
[data-layout-mode="dark"] .prog-nav-wrap { background:#1d2125; border-color:#2a2f34; }
.prog-nav-label {
    font-size:.65rem; text-transform:uppercase; letter-spacing:.45px; font-weight:700; color:#878a99;
    display:inline-flex; align-items:center; gap:.3rem; white-space:nowrap;
}
.prog-nav-list {
    display:flex; flex-wrap:wrap; gap:.35rem; flex:1;
}
.prog-nav-link {
    font-size:.72rem; font-weight:700; padding:.25em .65em; border-radius:8px;
    border:1px solid #eef0f4; background:#fff; color:#405189;
    text-decoration:none; transition:all .15s ease; white-space:nowrap;
    display:inline-flex; align-items:center; gap:.35rem;
}
.prog-nav-link:hover { background:#405189; color:#fff; border-color:#405189; }
[data-layout-mode="dark"] .prog-nav-link { background:#1d2125; border-color:#2a2f34; color:#ced4da; }
[data-layout-mode="dark"] .prog-nav-link:hover { background:#405189; color:#fff; }
.prog-nav-link .prog-nav-bdg {
    background:#eef1f8; color:#405189; border-radius:999px; padding:.05em .5em; font-size:.6rem; font-weight:800;
}
.prog-nav-link:hover .prog-nav-bdg { background:rgba(255,255,255,.2); color:#fff; }

/* ===== Collapse / Expand all row ===== */
.collapse-toolbar {
    display:flex; align-items:center; gap:.5rem; flex-wrap:wrap;
    padding:.35rem 0 .5rem;
}
.collapse-toolbar .btn-collapse-toggle {
    border:1px solid #eef0f4; background:#fff; color:#495057;
    border-radius:8px; padding:.35rem .85rem;
    font-size:.72rem; font-weight:700;
    transition:all .15s ease; cursor:pointer;
    display:inline-flex; align-items:center; gap:.4rem;
}
.collapse-toolbar .btn-collapse-toggle:hover { background:#405189; color:#fff; border-color:#405189; }
[data-layout-mode="dark"] .collapse-toolbar .btn-collapse-toggle { background:#1d2125; border-color:#2a2f34; color:#ced4da; }
[data-layout-mode="dark"] .collapse-toolbar .btn-collapse-toggle:hover { background:#405189; color:#fff; }

/* ===== Filtro rápido de programas ===== */
.prog-search-input {
    border:1px solid #e9ebec; border-radius:8px;
    padding:.35rem .75rem; font-size:.78rem;
    min-width:200px; background:#fff; color:#495057;
    transition: border-color .15s ease;
}
.prog-search-input:focus { outline:0; border-color:#299cdb; box-shadow: 0 0 0 .15rem rgba(41,156,219,.15); }
[data-layout-mode="dark"] .prog-search-input { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

/* ===== Highlight activo en navegación ===== */
.prog-nav-link.active { background:#299cdb; color:#fff; border-color:#299cdb; }

/* ===== Dados de baja: en rojo y fuera de los montos ===== */
.asig-table tr.is-baja > td { background:#fff1f1 !important; }
.asig-table tr.is-baja > td:first-child { box-shadow: inset 3px 0 0 #dc3545; }
.asig-table tr.is-baja .part-avatar { background:#dc3545 !important; color:#fff !important; }
.asig-table tr.is-baja td.text-end { color:#b42318 !important; opacity:.8; }
[data-layout-mode="dark"] .asig-table tr.is-baja > td { background:#3a1d20 !important; }
.baja-tag { display:inline-flex; align-items:center; gap:3px; margin-top:2px; padding:0 .45rem; border-radius:999px; font-size:.64rem; font-weight:700; color:#fff; background:#dc3545; }
.venc-pill.vp-baja { background:#fde2e2; color:#b42318; }
.prog-stat-pill.psp-baja { background:#fde2e2; color:#b42318; }
.estado-filter.is-baja.active { background:#dc3545; border-color:#dc3545; color:#fff; }
.bajas-resumen { padding:.7rem 1rem; border-radius:12px; background:#fff1f1; border:1px solid #f6caca; color:#7a1d16; }
.bajas-resumen .br-title { font-weight:800; font-size:.85rem; color:#b42318; }
.bajas-resumen .br-title small { font-weight:600; color:#a15c5c; }
.bajas-resumen .br-vals { display:flex; flex-wrap:wrap; gap:.4rem 1.2rem; margin-top:.3rem; font-size:.8rem; }
.bajas-resumen .br-total { color:#495057; }
[data-layout-mode="dark"] .bajas-resumen { background:#3a1d20; border-color:#6b2a2a; color:#f19a9a; }
</style>
@endpush

@push('css-after')
<link href="{{ asset('backend/assets/css/avip-contable.css') }}?v={{ @filemtime(public_path('backend/assets/css/avip-contable.css')) ?: time() }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="cont-page">
{{-- HERO --}}
<div class="asig-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-calendar-todo-line"></i></div>
        <div>
            <h4>Asignaciones del Mes</h4>
            <p class="hero-sub">Cobros programados de <span class="fw-bold text-capitalize">{{ $mesNombre }}</span> de <span class="fw-bold">{{ $gestion }}</span></p>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Contable</a></li>
        <li class="breadcrumb-item active">Asignaciones</li>
    </ol>
</div>

{{-- Filtro --}}
<div class="card filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.contable.asignaciones') }}" class="d-flex align-items-end flex-wrap gap-3">
            <div>
                <label><i class="ri-calendar-event-line"></i> Mes</label>
                <select name="mes" onchange="this.form.submit()">
                    @foreach($mesNombres as $i => $nombre)
                        <option value="{{ $i + 1 }}" @if($mes === $i + 1) selected @endif>{{ ucfirst($nombre) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label><i class="ri-calendar-2-line"></i> Gestión</label>
                <select name="gestion" onchange="this.form.submit()">
                    @foreach($gestiones as $g)
                        <option value="{{ $g }}" @if((int)$g === (int)$gestion) selected @endif>{{ $g }}</option>
                    @endforeach
                    @if(!$gestiones->contains((string)$gestion) && !$gestiones->contains($gestion))
                        <option value="{{ $gestion }}" selected>{{ $gestion }}</option>
                    @endif
                </select>
            </div>
            <small class="text-muted ms-auto" style="font-size:.72rem;">
                <i class="ri-information-line"></i> Muestra todas las cuotas con vencimiento dentro del periodo seleccionado
            </small>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-2 mb-3">
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#299cdb;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Cuotas del mes</div><div class="k-val">{{ $resumen['total_cuotas'] }}</div><div class="k-sub">{{ $resumen['participantes'] }} participantes · {{ $resumen['programas'] }} programas</div></div>
                <span class="k-ic" style="--k-bg:#f0f9ff; --k-color:#299cdb;"><i class="ri-list-check"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Monto esperado</div><div class="k-val">Bs {{ number_format($resumen['total_esperado'], 2) }}</div><div class="k-sub">Total a cobrar este mes</div></div>
                <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-bank-card-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Ya cobrado</div><div class="k-val">Bs {{ number_format($resumen['total_cobrado'], 2) }}</div><div class="k-sub">{{ $resumen['pagadas'] }} cuotas pagadas</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-checkbox-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#f06548;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Pendiente</div><div class="k-val">Bs {{ number_format($resumen['total_pendiente'], 2) }}</div><div class="k-sub">{{ $resumen['vencidas'] }} vencidas · {{ $resumen['proximas'] }} próximas</div></div>
                <span class="k-ic" style="--k-bg:#fef2f2; --k-color:#f06548;"><i class="ri-error-warning-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#7c3aed;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Recordatorios</div><div class="k-val">{{ $resumen['total_recordatorios'] ?? 0 }}</div><div class="k-sub">Mensajes de cobranza enviados</div></div>
                <span class="k-ic" style="--k-bg:#f5f3ff; --k-color:#7c3aed;"><i class="ri-message-3-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Cobros registrados</div><div class="k-val">Bs {{ number_format($resumen['total_pagado_mes'] ?? 0, 2) }}</div><div class="k-sub">Pagos del mes</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-hand-coin-line"></i></span>
            </div>
        </div>
    </div>
</div>
@if(($resumen['bajas_cuotas'] ?? 0) > 0)
<div class="bajas-resumen mb-3">
    <div class="br-title"><i class="ri-user-unfollow-line"></i> Dados de baja <small>(no se consideran en los montos de arriba)</small></div>
    <div class="br-vals">
        <span><b>{{ $resumen['bajas_participantes'] }}</b> participante(s) · <b>{{ $resumen['bajas_cuotas'] }}</b> cuota(s)</span>
        <span>Esperado <b>Bs {{ number_format($resumen['bajas_esperado'], 2) }}</b></span>
        <span>Cobrado <b>Bs {{ number_format($resumen['bajas_cobrado'], 2) }}</b></span>
        <span>Pendiente <b>Bs {{ number_format($resumen['bajas_pendiente'], 2) }}</b></span>
        <span class="br-total">Total general esperado (vigentes + baja): <b>Bs {{ number_format($resumen['total_esperado'] + $resumen['bajas_esperado'], 2) }}</b></span>
    </div>
</div>
@endif

{{-- Gráfico: recordatorios vs cobros por programa --}}
@if($porPrograma->count() > 0)
<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-bar-chart-grouped-line me-1"></i> Recordatorios enviados vs cobros registrados</h5>
        <span class="text-muted fs-12">Mensajería del sistema vs pagos del mes</span>
    </div>
    <div class="card-body">
        <div id="chartCobros"></div>
        <p class="text-muted fs-12 mb-0 mt-2"><i class="ri-information-line"></i> La barra son los recordatorios de cobranza enviados por el sistema; la línea son los Bs registrados como pago en el mes. Si la línea acompaña a la barra, el sistema está ayudando a cobrar.</p>
    </div>
</div>
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
(function () {
    var el = document.getElementById('chartCobros');
    if (!el || !window.ApexCharts) return;
    var labels = @json($porPrograma->pluck('programa_nombre')->map(fn ($n) => \Illuminate\Support\Str::limit($n, 24))->values());
    var recordatorios = @json($porPrograma->pluck('recordatorios')->map(fn ($v) => (int) $v)->values());
    var cobrado = @json($porPrograma->pluck('monto_pagado_mes')->map(fn ($v) => (float) $v)->values());
    new ApexCharts(el, {
        chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Recordatorios', type: 'column', data: recordatorios },
            { name: 'Cobrado (Bs)', type: 'line', data: cobrado }
        ],
        xaxis: { categories: labels, labels: { rotate: -35, style: { fontSize: '10px' } } },
        yaxis: [
            { title: { text: 'Recordatorios' }, seriesName: 'Recordatorios' },
            { opposite: true, title: { text: 'Bs cobrados' }, seriesName: 'Cobrado (Bs)' }
        ],
        colors: ['#7c3aed', '#0ab39c'],
        stroke: { width: [0, 3], curve: 'smooth' },
        plotOptions: { bar: { borderRadius: 3, columnWidth: '45%' } },
        dataLabels: { enabled: false },
        legend: { position: 'top' }
    }).render();
})();
</script>
@endif

{{-- Programas sin fechas registradas --}}
@if(count($programasSinFechas) > 0)
<div class="warning-card">
    <div class="warning-card-head">
        <span class="warning-card-icon"><i class="ri-error-warning-line"></i></span>
        <div>
            <h6 class="mb-0 fw-bold text-dark">Programas sin fechas de pago registradas</h6>
            <small class="text-muted">Tienen participantes inscritos pero aún no se definieron fechas de cobro. Defínelas para que aparezcan en las asignaciones.</small>
        </div>
        <span class="badge bg-soft-warning text-warning ms-auto fs-12 fw-bold">{{ count($programasSinFechas) }}</span>
    </div>
    <div class="sin-fechas-list">
        @foreach($programasSinFechas as $sf)
        <div class="sin-fecha-item">
            <span class="sin-fecha-icon"><i class="ri-graduation-cap-line"></i></span>
            <div class="sin-fecha-info">
                <div class="sin-fecha-name">{{ $sf->nombre }}</div>
                <div class="sin-fecha-meta">
                    <i class="ri-hashtag"></i> {{ $sf->codigo }} ·
                    <i class="ri-price-tag-3-line"></i> {{ $sf->tipo_nombre ?? 'Sin tipo' }} ·
                    <i class="ri-group-line"></i> {{ $sf->total_inscritos }} inscritos
                </div>
            </div>
            @can('contable.asignaciones.definir')
            <a href="{{ route('admin.contable.seguimiento', ['gestion' => $sf->gestion]) }}" class="sin-fecha-link" title="Ir a Seguimiento Contable">
                <i class="ri-calendar-check-line"></i> Definir
            </a>
            @endcan
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Navegación rápida entre programas --}}
@if($resumen['total_cuotas'] > 0 && $porPrograma->count() > 1)
<div class="prog-nav-wrap">
    <span class="prog-nav-label"><i class="ri-bookmark-3-line"></i> Programas</span>
    <div class="prog-nav-list">
        @foreach($porPrograma as $i => $pg)
        <a href="#prog-{{ $i + 1 }}" class="prog-nav-link" data-prog-nav="{{ $i + 1 }}">
            <i class="ri-graduation-cap-line"></i>
            {{ Str::limit($pg->programa_nombre, 28) }}
            <span class="prog-nav-bdg">{{ $pg->cuotas_count ?? $pg->total_participantes ?? $pg->cuotas->groupBy('participante_id')->count() }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Toolbar: colapsar/expandir + búsqueda --}}
@if($resumen['total_cuotas'] > 0 && $porPrograma->count() > 1)
<div class="collapse-toolbar">
    <button type="button" class="btn-collapse-toggle" data-action="collapse-all"><i class="ri-contract-up-down-line"></i> Colapsar todo</button>
    <button type="button" class="btn-collapse-toggle" data-action="expand-all"><i class="ri-expand-up-down-line"></i> Expandir todo</button>
    <input type="text" class="prog-search-input" placeholder="Buscar programa..." data-prog-search>
</div>
@endif

{{-- Filtros de estado --}}
@if($resumen['total_cuotas'] > 0)
<div class="estado-filter-row">
    <button type="button" class="estado-filter is-all active" data-filter="all">
        <i class="ri-list-check"></i> Todas <span class="bdg">{{ $resumen['total_cuotas'] }}</span>
    </button>
    <button type="button" class="estado-filter is-venc" data-filter="vencida">
        <i class="ri-error-warning-line"></i> Vencidas <span class="bdg">{{ $resumen['vencidas'] }}</span>
    </button>
    <button type="button" class="estado-filter is-hoy" data-filter="hoy">
        <i class="ri-alarm-warning-line"></i> Hoy <span class="bdg">{{ $resumen['hoy'] }}</span>
    </button>
    <button type="button" class="estado-filter is-prox" data-filter="proxima">
        <i class="ri-time-line"></i> Próximas <span class="bdg">{{ $resumen['proximas'] }}</span>
    </button>
    <button type="button" class="estado-filter is-pag" data-filter="pagada">
        <i class="ri-checkbox-circle-line"></i> Pagadas <span class="bdg">{{ $resumen['pagadas'] }}</span>
    </button>
    @if(($resumen['bajas_cuotas'] ?? 0) > 0)
    <button type="button" class="estado-filter is-baja" data-filter="baja">
        <i class="ri-user-unfollow-line"></i> De baja <span class="bdg">{{ $resumen['bajas_cuotas'] }}</span>
    </button>
    @endif
</div>

{{-- Programas con cuotas --}}
@foreach($porPrograma as $i => $pg)
@php $progId = $loop->iteration; @endphp
<div class="prog-card" data-prog-card data-prog-id="{{ $progId }}">
    <div class="prog-card-head" data-toggle-prog>
        <span class="prog-card-icon"><i class="ri-graduation-cap-line"></i></span>
        <div class="prog-card-info">
            <div class="prog-card-name">{{ $pg->programa_nombre }}</div>
            <div class="prog-card-meta">
                <i class="ri-hashtag"></i> {{ $pg->programa_codigo }} ·
                <i class="ri-price-tag-3-line"></i> {{ $pg->tipo_nombre ?? 'Sin tipo' }} ·
                <i class="ri-calendar-2-line"></i> Gestión {{ $pg->programa_gestion }}
            </div>
        </div>
        <div class="prog-card-totals">
            <span class="prog-stat-pill psp-esp"><i class="ri-bank-card-line"></i> Esp Bs {{ number_format($pg->total_esperado, 2) }}</span>
            <span class="prog-stat-pill psp-cob"><i class="ri-check-line"></i> Cob Bs {{ number_format($pg->total_cobrado, 2) }}</span>
            <span class="prog-stat-pill psp-pen"><i class="ri-time-line"></i> Pen Bs {{ number_format($pg->total_pendiente, 2) }}</span>
            @if($pg->count_vencidas > 0)<span class="prog-stat-pill psp-c-venc"><i class="ri-error-warning-line"></i> {{ $pg->count_vencidas }}</span>@endif
            @if($pg->count_hoy > 0)<span class="prog-stat-pill psp-c-hoy"><i class="ri-alarm-warning-line"></i> {{ $pg->count_hoy }}</span>@endif
            @if($pg->count_proximas > 0)<span class="prog-stat-pill psp-c-prox"><i class="ri-time-line"></i> {{ $pg->count_proximas }}</span>@endif
            @if($pg->count_pagadas > 0)<span class="prog-stat-pill psp-c-pag"><i class="ri-checkbox-circle-line"></i> {{ $pg->count_pagadas }}</span>@endif
            @if(($pg->count_bajas ?? 0) > 0)<span class="prog-stat-pill psp-baja" title="Dados de baja (no se suman): esperado Bs {{ number_format($pg->bajas_esperado, 2) }} · cobrado Bs {{ number_format($pg->bajas_cobrado, 2) }}"><i class="ri-user-unfollow-line"></i> Baja Bs {{ number_format($pg->bajas_pendiente, 2) }} pend.</span>@endif
            @if(($pg->recordatorios ?? 0) > 0)<span class="prog-stat-pill" title="Recordatorios de cobranza enviados este mes"><i class="ri-message-3-line"></i> Rec {{ $pg->recordatorios }}</span>@endif
            @if(($pg->monto_pagado_mes ?? 0) > 0)<span class="prog-stat-pill psp-cob" title="Pagos registrados este mes"><i class="ri-hand-coin-line"></i> Reg Bs {{ number_format($pg->monto_pagado_mes, 2) }}</span>@endif
            @if(($pg->pagaron_mes ?? 0) > 0)<span class="prog-stat-pill" title="Participantes que registraron pago este mes"><i class="ri-wallet-3-line"></i> Pagaron {{ $pg->pagaron_mes }}</span>@endif
            @if(($pg->efectividad ?? 0) > 0)<span class="prog-stat-pill" title="Participantes recordados que pagaron este mes"><i class="ri-check-double-line"></i> Efect {{ $pg->efectividad }}</span>@endif
        </div>
        <span class="collapse-toggle"><i class="ri-arrow-down-s-line"></i></span>
    </div>
    <div class="prog-card-body" data-prog-body>
    <div class="table-responsive">
        <table class="asig-table table">
            <thead>
                <tr>
                    <th style="width:50px;" class="text-center">#</th>
                    <th>Participante</th>
                    <th style="width:120px;">Tipo</th>
                    <th style="width:110px;" class="text-center">Seguimiento</th>
                    <th style="width:140px;">Fecha</th>
                    <th style="width:140px;">Vencimiento</th>
                    <th class="text-end" style="width:110px;">Deuda</th>
                    <th class="text-end" style="width:110px;">Pagado</th>
                    <th class="text-end" style="width:110px;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dias = ['dom','lun','mar','mié','jue','vie','sáb'];
                    $estadoPrio = ['vencida' => 4, 'hoy' => 3, 'proxima' => 2, 'pagada' => 1];
                    $grupos = collect($pg->cuotas)->groupBy('participante_id');
                    $rowIdx = 0;
                @endphp
                @foreach($grupos as $partId => $cuotas)
                @php
                    $first = $cuotas->first();
                    $esBaja = !empty($first->baja);
                    if (! $esBaja) $rowIdx++;
                    $ini = strtoupper(substr($first->nombres, 0, 1) . substr($first->apellidos, 0, 1));
                    $estados = $cuotas->pluck('estado_venc')->unique()->values();
                    $worst = $estados->sortByDesc(fn($e) => $estadoPrio[$e] ?? 0)->first();
                    $rowCls = $esBaja ? 'is-baja' : match($worst) {
                        'vencida' => 'is-venc',
                        'hoy' => 'is-hoy',
                        'pagada' => 'is-pag',
                        default => '',
                    };
                    $totDeuda  = $cuotas->sum('deuda');
                    $totPagado = $cuotas->sum(fn($c) => min($c->total_pagado, $c->deuda));
                    $totSaldo  = $cuotas->sum('saldo');
                    $tiposUnicos = $cuotas->pluck('tipo')->unique();
                    $modalId = 'modalCuotas-' . $loop->parent->iteration . '-' . $partId;
                @endphp
                <tr class="{{ $rowCls }}" data-estado="{{ $estados->implode(' ') }}">
                    <td class="text-center text-muted fw-bold">{{ $esBaja ? '—' : $rowIdx }}</td>
                    <td>
                        <a href="{{ route('admin.programas.participante-detalle', $partId) }}" class="text-reset text-decoration-none">
                            <div class="d-flex align-items-center gap-2">
                                <span class="part-avatar">{{ $ini }}</span>
                                <div style="min-width:0;">
                                    <div class="fw-bold {{ $esBaja ? 'text-danger' : 'text-dark' }}" style="font-size:.85rem;">{{ $first->apellidos }}, {{ $first->nombres }}</div>
                                    <div class="text-muted" style="font-size:.7rem;"><i class="ri-passport-line"></i> CI: {{ $first->ci }}</div>
                                    @if($esBaja)
                                        <span class="baja-tag" title="No se suma a esperado, cobrado ni pendiente"><i class="ri-user-unfollow-line"></i> {{ $first->baja }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </td>
                    <td>
                        @if($tiposUnicos->count() === 1)
                            @php $tu = $tiposUnicos->first(); @endphp
                            <span class="tipo-pill {{ $tu === 'Matricula' ? 'tp-mat' : 'tp-col' }}">
                                <i class="ri-{{ $tu === 'Matricula' ? 'bank-card-line' : 'funds-line' }}"></i>
                                {{ $tu === 'Matricula' ? 'Matrícula' : 'Mensualidad' }}
                            </span>
                        @else
                            <span class="tipo-pill tp-col"><i class="ri-stack-line"></i> Mixto</span>
                        @endif
                    </td>
                    @php
                        $recPart = $cuotas->contains(fn($c) => ($c->recordado ?? false));
                        $pagMes = $cuotas->contains(fn($c) => ($c->pagado_mes ?? false));
                    @endphp
                    <td class="text-center">
                        @if($recPart)<span class="badge bg-primary-subtle text-primary me-1" title="Se le envió recordatorio de cobranza este mes"><i class="ri-message-3-line"></i></span>@endif
                        @if($pagMes)<span class="badge bg-success-subtle text-success" title="Registró pago este mes"><i class="ri-hand-coin-line"></i></span>@endif
                        @if(!$recPart && !$pagMes)<span class="text-muted">—</span>@endif
                    </td>
                    @if($cuotas->count() === 1)
                        @php
                            $c = $first;
                            $vencPillCls = match($c->estado_venc) {
                                'vencida' => 'vp-venc', 'hoy' => 'vp-hoy', 'proxima' => 'vp-prox', 'pagada' => 'vp-pag', 'baja' => 'vp-baja', default => '',
                            };
                            $vencText = match($c->estado_venc) {
                                'vencida' => 'Vencido ' . abs($c->dias) . 'd',
                                'hoy' => 'Vence hoy',
                                'proxima' => 'En ' . $c->dias . 'd',
                                'pagada' => 'Pagada',
                                'baja' => 'De baja',
                                default => '—',
                            };
                            $vencIcon = match($c->estado_venc) {
                                'vencida' => 'ri-error-warning-line', 'hoy' => 'ri-alarm-warning-line',
                                'proxima' => 'ri-time-line', 'pagada' => 'ri-checkbox-circle-line',
                                default => 'ri-information-line',
                            };
                            $fp = \Carbon\Carbon::parse($c->fecha_pago);
                        @endphp
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:.82rem;">
                                <i class="ri-calendar-check-line text-primary"></i> {{ $fp->format('d/m/Y') }}
                            </div>
                            <div class="text-muted text-capitalize" style="font-size:.7rem;">{{ $dias[$fp->dayOfWeek] }}</div>
                        </td>
                        <td><span class="venc-pill {{ $vencPillCls }}"><i class="{{ $vencIcon }}"></i> {{ $vencText }}</span></td>
                    @else
                        <td>
                            <span class="cuotas-count-badge"><i class="ri-stack-line"></i> {{ $cuotas->count() }} cuotas</span>
                        </td>
                        <td>
                            <button type="button" class="btn-ver-cuotas" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                <i class="ri-eye-line"></i> Ver detalle
                            </button>
                        </td>
                    @endif
                    <td class="text-end fw-bold">Bs {{ number_format($totDeuda, 2) }}</td>
                    <td class="text-end fw-bold text-success">Bs {{ number_format($totPagado, 2) }}</td>
                    <td class="text-end fw-bold {{ $totSaldo > 0 ? 'text-danger' : 'text-muted' }}">Bs {{ number_format($totSaldo, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>{{-- /prog-card-body --}}
</div>

{{-- Modales de detalle por participante con múltiples cuotas --}}
@foreach($grupos as $partId => $cuotas)
    @if($cuotas->count() > 1)
        @php
            $first = $cuotas->first();
            $modalId = 'modalCuotas-' . $loop->parent->iteration . '-' . $partId;
            $totDeuda  = $cuotas->sum('deuda');
            $totPagado = $cuotas->sum(fn($c) => min($c->total_pagado, $c->deuda));
            $totSaldo  = $cuotas->sum('saldo');
        @endphp
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #405189, #5e72b8); color:#fff;">
                        <div>
                            <h5 class="modal-title text-white mb-1">
                                <i class="ri-file-list-3-line"></i> Detalle de cuotas — {{ $first->apellidos }}, {{ $first->nombres }}
                            </h5>
                            <div style="font-size:.75rem; opacity:.85;">
                                <i class="ri-graduation-cap-line"></i> {{ $pg->programa_nombre }} ·
                                <i class="ri-passport-line"></i> CI: {{ $first->ci }} ·
                                <i class="ri-stack-line"></i> {{ $cuotas->count() }} cuotas
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($cuotas as $c)
                            @php
                                $vencPillCls = match($c->estado_venc) {
                                    'vencida' => 'vp-venc', 'hoy' => 'vp-hoy', 'proxima' => 'vp-prox', 'pagada' => 'vp-pag', 'baja' => 'vp-baja', default => '',
                                };
                                $vencText = match($c->estado_venc) {
                                    'vencida' => 'Vencido ' . abs($c->dias) . 'd',
                                    'hoy' => 'Vence hoy',
                                    'proxima' => 'En ' . $c->dias . 'd',
                                    'pagada' => 'Pagada',
                                    'baja' => 'De baja',
                                    default => '—',
                                };
                                $vencIcon = match($c->estado_venc) {
                                    'vencida' => 'ri-error-warning-line', 'hoy' => 'ri-alarm-warning-line',
                                    'proxima' => 'ri-time-line', 'pagada' => 'ri-checkbox-circle-line',
                                    default => 'ri-information-line',
                                };
                                $fp = \Carbon\Carbon::parse($c->fecha_pago);
                                $pagadoCalc = min($c->total_pagado, $c->deuda);
                            @endphp
                            <div class="cuota-detail-row">
                                <div class="cuota-detail-info">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="tipo-pill {{ $c->tipo === 'Matricula' ? 'tp-mat' : 'tp-col' }}">
                                            <i class="ri-{{ $c->tipo === 'Matricula' ? 'bank-card-line' : 'funds-line' }}"></i>
                                            {{ $c->tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad' }}
                                        </span>
                                        <span class="venc-pill {{ $vencPillCls }}"><i class="{{ $vencIcon }}"></i> {{ $vencText }}</span>
                                        <span class="text-muted" style="font-size:.78rem;">
                                            <i class="ri-calendar-check-line"></i> {{ $fp->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="cuota-detail-amounts">
                                        <div><div class="amt-lbl">Deuda</div><div class="amt-val">Bs {{ number_format($c->deuda, 2) }}</div></div>
                                        <div><div class="amt-lbl">Pagado</div><div class="amt-val text-success">Bs {{ number_format($pagadoCalc, 2) }}</div></div>
                                        <div><div class="amt-lbl">Saldo</div><div class="amt-val {{ $c->saldo > 0 ? 'text-danger' : 'text-muted' }}">Bs {{ number_format($c->saldo, 2) }}</div></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div class="d-flex gap-3 flex-wrap" style="font-size:.85rem;">
                            <span><span class="text-muted">Total deuda:</span> <strong>Bs {{ number_format($totDeuda, 2) }}</strong></span>
                            <span><span class="text-muted">Total pagado:</span> <strong class="text-success">Bs {{ number_format($totPagado, 2) }}</strong></span>
                            <span><span class="text-muted">Total saldo:</span> <strong class="{{ $totSaldo > 0 ? 'text-danger' : 'text-muted' }}">Bs {{ number_format($totSaldo, 2) }}</strong></span>
                        </div>
                        <a href="{{ route('admin.programas.participante-detalle', $partId) }}" class="btn btn-sm btn-primary">
                            <i class="ri-user-search-line"></i> Ver participante
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endforeach

@else
<div class="empty-state">
    <div class="empty-icon"><i class="ri-calendar-close-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">Sin asignaciones en este periodo</h5>
    <p class="text-muted mb-0">No hay cuotas con vencimiento en {{ ucfirst($mesNombre) }} de {{ $gestion }}.</p>
    @if(count($programasSinFechas) > 0)
        <p class="text-muted mb-0 mt-2"><i class="ri-information-line"></i> Hay {{ count($programasSinFechas) }} programa(s) sin fechas registradas. Defínelas en <a href="{{ route('admin.contable.seguimiento', ['gestion' => $gestion]) }}">Seguimiento Contable</a>.</p>
    @endif
</div>
@endif
</div>{{-- /cont-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('[data-prog-card]');

    // ============================================================
    // 1. Colapsar / Expandir programa (click en header)
    // ============================================================
    cards.forEach(function (card) {
        var head = card.querySelector('[data-toggle-prog]');
        var body = card.querySelector('[data-prog-body]');
        if (!head || !body) return;

        head.addEventListener('click', function (e) {
            // No colapsar si el click fue en un enlace o botón
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.prog-card-totals')) return;
            toggleProgCard(card, body);
        });
    });

    function toggleProgCard(card, body) {
        var isCollapsed = body.classList.contains('collapsed');
        var toggleIcon = card.querySelector('.collapse-toggle');
        if (isCollapsed) {
            expandProgBody(body, toggleIcon);
        } else {
            collapseProgBody(body, toggleIcon);
        }
    }

    function collapseProgBody(body, toggleIcon) {
        body.style.maxHeight = body.scrollHeight + 'px';
        requestAnimationFrame(function () {
            body.classList.add('collapsing');
            body.classList.remove('collapsed');
            body.style.maxHeight = '0px';
            if (toggleIcon) toggleIcon.classList.add('collapsed');
            setTimeout(function () {
                body.classList.remove('collapsing');
                body.classList.add('collapsed');
                body.style.maxHeight = null;
            }, 350);
        });
    }

    function expandProgBody(body, toggleIcon) {
        body.classList.remove('collapsed');
        body.classList.add('collapsing');
        body.style.maxHeight = '0px';
        requestAnimationFrame(function () {
            body.style.maxHeight = body.scrollHeight + 'px';
            if (toggleIcon) toggleIcon.classList.remove('collapsed');
            setTimeout(function () {
                body.classList.remove('collapsing');
                body.style.maxHeight = null;
            }, 350);
        });
    }

    // ============================================================
    // 2. Colapsar / Expandir todo
    // ============================================================
    document.querySelector('[data-action="collapse-all"]')?.addEventListener('click', function () {
        cards.forEach(function (card) {
            var body = card.querySelector('[data-prog-body]');
            if (!body || body.classList.contains('collapsed')) return;
            var icon = card.querySelector('.collapse-toggle');
            collapseProgBody(body, icon);
        });
    });

    document.querySelector('[data-action="expand-all"]')?.addEventListener('click', function () {
        cards.forEach(function (card) {
            var body = card.querySelector('[data-prog-body]');
            if (!body || !body.classList.contains('collapsed')) return;
            var icon = card.querySelector('.collapse-toggle');
            expandProgBody(body, icon);
        });
    });

    // ============================================================
    // 3. Navegación rápida — scroll suave + highlight
    // ============================================================
    document.querySelectorAll('[data-prog-nav]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var id = this.dataset.progNav;
            var target = document.querySelector('[data-prog-id="' + id + '"]');
            if (!target) return;

            // Expandir si está colapsado
            var body = target.querySelector('[data-prog-body]');
            if (body && body.classList.contains('collapsed')) {
                var icon = target.querySelector('.collapse-toggle');
                expandProgBody(body, icon);
            }

            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // highlight temporal
            document.querySelectorAll('[data-prog-nav]').forEach(function (l) { l.classList.remove('active'); });
            this.classList.add('active');
            setTimeout(function () { link.classList.remove('active'); }, 2000);
        });
    });

    // ============================================================
    // 4. Filtro rápido por nombre de programa
    // ============================================================
    var searchInput = document.querySelector('[data-prog-search]');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            cards.forEach(function (card) {
                var nameEl = card.querySelector('.prog-card-name');
                if (!nameEl) return;
                var name = (nameEl.textContent || '').toLowerCase();
                var match = !q || name.includes(q);
                card.style.display = match ? '' : 'none';
                // También oculta/muestra en navegación
                var id = card.dataset.progId;
                if (id) {
                    var navLink = document.querySelector('[data-prog-nav="' + id + '"]');
                    if (navLink) navLink.style.display = match ? '' : 'none';
                }
            });
        });
    }

    // ============================================================
    // 5. Filtros de estado (original mejorado)
    // ============================================================
    document.querySelectorAll('.estado-filter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.estado-filter').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            var f = this.dataset.filter;
            cards.forEach(function (card) {
                var any = false;
                card.querySelectorAll('tbody tr[data-estado]').forEach(function (tr) {
                    var estados = (tr.dataset.estado || '').split(/\s+/).filter(Boolean);
                    var match = f === 'all' || estados.includes(f);
                    tr.style.display = match ? '' : 'none';
                    if (match) any = true;
                });
                // Si el filtro de búsqueda ya ocultó esta card, respetarlo
                var searchInp = document.querySelector('[data-prog-search]');
                var sq = searchInp ? searchInp.value.toLowerCase().trim() : '';
                var progName = card.querySelector('.prog-card-name');
                var nameMatch = !sq || (progName && (progName.textContent || '').toLowerCase().includes(sq));
                card.style.display = (any && nameMatch) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
