@extends('admin.layouts.master')

@section('title', 'Dashboard - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero / Page Header ===== */
.dashboard-hero {
    position: relative;
    background: linear-gradient(135deg, #163b6b 0%, #2a5d97 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.dashboard-hero::before {
    content: "";
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.dashboard-hero::after {
    content: "";
    position: absolute;
    bottom: -60px; left: 30%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%);
    border-radius: 50%;
}
.dashboard-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.dashboard-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; }
.dashboard-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .9rem; margin: 0; }
.dashboard-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.dashboard-hero .breadcrumb-item, .dashboard-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.dashboard-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.dashboard-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

/* ===== Gestión Switcher (en hero) ===== */
.hero-gestion {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 12px;
    padding: .5rem .9rem;
    backdrop-filter: blur(4px);
    display: inline-flex; align-items: center; gap: .6rem;
}
.hero-gestion label {
    color: rgba(255,255,255,.85);
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin: 0;
}
.hero-gestion select {
    background: rgba(255,255,255,.95);
    border: 0;
    border-radius: 8px;
    font-weight: 600;
    color: #163b6b;
    padding: .25rem .5rem;
    cursor: pointer;
}

/* ===== Stat Cards (KPIs principales) ===== */
.stat-card-modern {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    transition: transform .2s ease, box-shadow .2s ease;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
.stat-card-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 26px -12px rgba(64,81,137,.28);
}
[data-layout-mode="dark"] .stat-card-modern {
    background: #1d2125; border-color: #2a2f34;
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.stat-card-modern .label {
    font-size: .68rem;
    font-weight: 700;
    color: #878a99;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: .25rem;
}
.stat-card-modern .value {
    font-weight: 700;
    font-size: 1.4rem;
    color: #212529;
    line-height: 1;
}
[data-layout-mode="dark"] .stat-card-modern .value { color: #f3f6f9; }

/* ===== Panel Cards (gráficos / tabla) ===== */
.panel-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .panel-card {
    background: #1d2125; border-color: #2a2f34;
}
.panel-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
[data-layout-mode="dark"] .panel-card .card-header { border-bottom-color: #2a2f34; }
.panel-card .section-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.05rem;
}

.chart-container { position: relative; min-height: 300px; }
.chart-container-sm { min-height: 260px; }

/* Panel de programas (chart 2) */
.chart2-prog-panel {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    background: #fafbfd;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
[data-layout-mode="dark"] .chart2-prog-panel { background: #1a1d20; border-color: #2a2f34; }
.chart2-panel-header {
    display: flex; align-items: center; gap: .5rem;
    padding: .75rem 1rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    font-size: .8rem; font-weight: 700;
    color: #495057; text-transform: uppercase; letter-spacing: .3px;
}
[data-layout-mode="dark"] .chart2-panel-header { background: #212529; border-bottom-color: #2a2f34; color: #ced4da; }
.chart2-count {
    margin-left: auto;
    background: #299cdb; color: #fff;
    border-radius: 999px;
    padding: .15em .65em;
    font-size: .72rem; font-weight: 700;
}
.chart2-prog-list {
    overflow-y: auto;
    max-height: 320px;
    padding: .5rem;
}
.chart2-prog-list::-webkit-scrollbar { width: 6px; }
.chart2-prog-list::-webkit-scrollbar-thumb { background: #d6dbe0; border-radius: 3px; }
.chart2-prog-item {
    display: flex; align-items: center; gap: .5rem;
    padding: .55rem .65rem;
    border-radius: 8px;
    transition: background-color .15s ease;
    border-bottom: 1px dashed #eef0f4;
}
.chart2-prog-item:last-child { border-bottom: 0; }
.chart2-prog-item:hover { background: #fff; }
[data-layout-mode="dark"] .chart2-prog-item { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .chart2-prog-item:hover { background: #212529; }
.chart2-prog-info { flex: 1; min-width: 0; }
.chart2-prog-name { font-weight: 600; font-size: .78rem; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .chart2-prog-name { color: #fff; }
.chart2-prog-meta { font-size: .68rem; color: #878a99; margin-top: 1px; }
.chart2-prog-count-bar {
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff;
    border-radius: 8px;
    padding: .25em .65em;
    font-size: .75rem; font-weight: 700;
    min-width: 36px; text-align: center;
}

/* Bloque de mes con programas dentro */
.chart2-mes-block { margin-bottom: .75rem; }
.chart2-mes-block:last-child { margin-bottom: 0; }
.chart2-mes-header {
    display: flex; align-items: center; gap: .5rem;
    padding: .5rem .75rem;
    background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%);
    border-radius: 8px;
    font-weight: 700;
    color: #163b6b;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .3px;
}
[data-layout-mode="dark"] .chart2-mes-header { background: linear-gradient(135deg, #1a1d20 0%, #212529 100%); color: #2a5d97; }
.chart2-mes-header .mes-total {
    margin-left: auto;
    background: #163b6b;
    color: #fff;
    border-radius: 999px;
    padding: .15em .65em;
    font-size: .7rem; font-weight: 700;
}
.chart2-mes-progs { padding: .25rem .25rem .25rem 1rem; border-left: 2px solid #eef1f8; margin: .4rem 0 .4rem .75rem; }
[data-layout-mode="dark"] .chart2-mes-progs { border-left-color: #2a2f34; }
.chart2-mes-prog-item {
    display: flex; align-items: center; gap: .5rem;
    padding: .4rem .6rem;
    border-radius: 6px;
    transition: background-color .15s ease;
    border-bottom: 1px dashed #f1f3f5;
}
.chart2-mes-prog-item:last-child { border-bottom: 0; }
.chart2-mes-prog-item:hover { background: #fff; }
[data-layout-mode="dark"] .chart2-mes-prog-item { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .chart2-mes-prog-item:hover { background: #1a1d20; }
.chart2-mes-prog-info { flex: 1; min-width: 0; }
.chart2-mes-prog-name { font-weight: 600; font-size: .76rem; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .chart2-mes-prog-name { color: #fff; }
.chart2-mes-prog-meta { font-size: .66rem; color: #878a99; margin-top: 1px; }
.chart2-mes-tipo-breakdown { font-size: .66rem; color: #878a99; padding: 2px 12px 4px; display: flex; flex-wrap: wrap; gap: 4px 8px; border-bottom: 1px solid #e9ebec; }
.chart2-mes-prog-counts {
    display: flex; flex-direction: column; align-items: flex-end;
    gap: 2px; flex-shrink: 0;
}
.chart2-mes-prog-total {
    background: #fff;
    color: #299cdb;
    border: 1px solid #c3ddfa;
    border-radius: 6px;
    padding: .1em .6em;
    font-size: .75rem; font-weight: 800;
    min-width: 34px; text-align: center;
}
[data-layout-mode="dark"] .chart2-mes-prog-total { background: #212529; border-color: #2a2f34; }
.chart2-mes-prog-detail {
    font-size: .6rem; color: #878a99;
    white-space: nowrap;
    line-height: 1;
}

/* ===== Programa Table ===== */
.program-table { margin-bottom: 0; }
.program-table thead th {
    background: #f3f5f9 !important;
    font-weight: 600;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6c757d;
    border: 0;
    padding: .75rem 1rem;
}
[data-layout-mode="dark"] .program-table thead th { background: #212529 !important; color: #adb5bd; }
.program-table td {
    padding: .7rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .85rem;
}
[data-layout-mode="dark"] .program-table td { border-bottom-color: #2a2f34; }
.program-table tbody tr { transition: background-color .15s ease; }
.program-table tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .program-table tbody tr:hover { background-color: #212529; }

/* ===== Resumen Global ===== */
.global-stat {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
    position: relative;
}
.global-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 26px -12px rgba(64,81,137,.28);
}
[data-layout-mode="dark"] .global-stat { background: #1d2125; border-color: #2a2f34; }
.global-stat::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    background: var(--stat-accent, #163b6b);
}
.global-stat .stat-num {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
    line-height: 1.1;
    margin: .75rem 0 .25rem;
}
[data-layout-mode="dark"] .global-stat .stat-num { color: #f3f6f9; }

/* ===== Modal Participantes ===== */
#modalProgramaParticipantes .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalProgramaParticipantes .modal-header {
    background: linear-gradient(135deg, #163b6b 0%, #2a5d97 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalProgramaParticipantes .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalProgramaParticipantes .modal-header > * { position: relative; z-index: 1; }
#modalProgramaParticipantes .mpp-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalProgramaParticipantes .modal-title { color: #fff; font-weight: 700; font-size: 1rem; line-height: 1.2; }
#modalProgramaParticipantes .mpp-subtitle { color: rgba(255,255,255,.85); font-size: .75rem; }
#modalProgramaParticipantes .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalProgramaParticipantes .modal-body { padding: 1.5rem; background: #fafbfd; max-height: 75vh; overflow-y: auto; }
[data-layout-mode="dark"] #modalProgramaParticipantes .modal-body { background: #1a1d20; }
#modalProgramaParticipantes .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalProgramaParticipantes .modal-footer { background: #212529; border-top-color: #2a2f34; }

#mpp-summary {
    display: flex; flex-wrap: wrap; gap: .5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px dashed #eef0f4;
}
[data-layout-mode="dark"] #mpp-summary { border-bottom-color: #2a2f34; }
.mpp-chip {
    display: inline-flex; align-items: center; gap: .4rem;
    background: #fff; border: 1px solid #eef0f4;
    border-radius: 999px;
    padding: .4em .85em;
    font-size: .78rem; font-weight: 600;
    color: #495057;
}
.mpp-chip strong { font-weight: 800; font-size: .88rem; }
.mpp-chip i { font-size: .95rem; }
.mpp-total { color: #495057; }
.mpp-total strong { color: #212529; }
.mpp-info { color: #299cdb; }
.mpp-info strong { color: #1976d2; }
.mpp-success { color: #0ab39c; }
.mpp-success strong { color: #098671; }
.mpp-male { color: #163b6b; }
.mpp-male strong { color: #2c3a6b; }
.mpp-female { color: #ec4899; }
.mpp-female strong { color: #c2185b; }
.mpp-traspaso { color: #f06548; }
.mpp-traspaso strong { color: #c0392b; }
[data-layout-mode="dark"] .mpp-chip { background: #212529; border-color: #2a2f34; color: #ced4da; }

.mpp-table-wrap {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
[data-layout-mode="dark"] .mpp-table-wrap { border-color: #2a2f34; }
.mpp-table { width: 100%; margin: 0; }
.mpp-table thead th {
    background: #f8f9fc !important;
    font-size: .68rem !important;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .75rem .65rem;
}
.mpp-table tbody td {
    padding: .7rem .65rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .82rem;
}
.mpp-table tbody tr { transition: background-color .15s ease; }
.mpp-table tbody tr.mpp-mes-highlight { background-color: #eef3fa; }
.mpp-table tbody tr.mpp-traspaso-row { background-color: #fff5f5 !important; }
.mpp-table tbody tr.mpp-traspaso-row:hover { background-color: #ffe8e8 !important; }
.mpp-table tbody tr:hover { background-color: #f8f9fc; }
.mpp-table tbody tr:last-child td { border-bottom: 0; }
[data-layout-mode="dark"] .mpp-table thead th { background: #1d2125 !important; border-bottom-color: #2a2f34 !important; }
[data-layout-mode="dark"] .mpp-table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .mpp-table tbody tr:hover { background-color: #1a1d20; }

.mpp-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .72rem;
    color: #fff; flex-shrink: 0;
    background: linear-gradient(135deg, #163b6b 0%, #2a5d97 100%);
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.5);
}
.mpp-avatar.male { background: linear-gradient(135deg, #3577f1 0%, #299cdb 100%); }
.mpp-avatar.female { background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); }
.mpp-avatar.muted { background: linear-gradient(135deg, #878a99 0%, #adb5bd 100%); }
.mpp-nombre { font-weight: 600; color: #212529; font-size: .85rem; line-height: 1.2; }
.mpp-ci { font-size: .68rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .mpp-nombre { color: #fff; }
.mpp-contact { font-size: .75rem; line-height: 1.35; color: #495057; }
[data-layout-mode="dark"] .mpp-contact { color: #ced4da; }

.mpp-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 700;
    padding: .3em .75em;
    border-radius: 999px;
    border: 1px solid;
}
.mpp-sexo-male { background: #dbeafe; color: #3577f1; border-color: #c3ddfa; }
.mpp-sexo-female { background: #fce7f3; color: #ec4899; border-color: #fbcfe8; }
.mpp-sexo-muted { background: #f1f3f5; color: #878a99; border-color: #e9ebec; }
.plan-contado { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.plan-regular { background: #eef1f8; color: #163b6b; border-color: #c3cfe2; }
.mpp-pill-traspaso { background: #fff0ee; color: #f06548; border-color: #fcd5cf; }
.mpp-pill-traspaso i { font-size: .8rem; }

.mpp-empty {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 12px;
}
.mpp-empty-icon {
    width: 70px; height: 70px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2rem;
    color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}
</style>
@endpush

@section('content')
{{-- HERO --}}
<div class="row">
    <div class="col-12">
        <div class="dashboard-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 position-relative" style="z-index:1;">
                <div class="hero-icon">
                    <i class="ri-dashboard-3-line"></i>
                </div>
                <div>
                    <h4 class="mb-1">Panel de Control</h4>
                    <p class="hero-sub">Resumen general de inscritos, programas y métricas institucionales</p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-md-end gap-2 position-relative" style="z-index:1;">
                <div class="hero-gestion">
                    <i class="ri-calendar-2-line"></i>
                    <label for="filter-gestion">Gestión</label>
                    <select id="filter-gestion" class="form-select form-select-sm">
                        @foreach($gestiones as $g)
                            <option value="{{ $g }}" @if((int)$g === $gestionActual) selected @endif>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Resumen Stats --}}
<div class="row g-3 mb-4" id="stats-cards">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#163b6b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Inscritos</p>
                        <h2 class="stat-num" id="stat-inscritos">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-arrow-right-up-line text-success"></i> Total registrados</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                            <i class="ri-team-line text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#299cdb;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Regulares</p>
                        <h2 class="stat-num" id="stat-regulares">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-user-line text-info"></i> Plan regular</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-info rounded-circle fs-2">
                            <i class="ri-user-line text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#0ab39c;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Contado</p>
                        <h2 class="stat-num" id="stat-contado">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-money-dollar-circle-line text-success"></i> Plan contado</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-success rounded-circle fs-2">
                            <i class="ri-money-dollar-circle-line text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#3577f1;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Hombres</p>
                        <h2 class="stat-num" id="stat-hombres">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-men-line text-primary"></i> Sexo masculino</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                            <i class="ri-men-line text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#ec4899;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Mujeres</p>
                        <h2 class="stat-num" id="stat-mujeres">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-women-line text-danger"></i> Sexo femenino</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-danger rounded-circle fs-2">
                            <i class="ri-women-line text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#f7b84b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Programas</p>
                        <h2 class="stat-num" id="stat-programas">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-booklet-line text-warning"></i> Total programas</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-warning rounded-circle fs-2">
                            <i class="ri-booklet-line text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card global-stat h-100 mb-0" style="--stat-accent:#f06548;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="fw-semibold text-muted text-uppercase fs-11 mb-0">Traspasos</p>
                        <h2 class="stat-num" id="stat-traspasos">0</h2>
                        <p class="mb-0 text-muted fs-12"><i class="ri-swap-line text-danger"></i> Transferencias de otra unidad</p>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-danger rounded-circle fs-2">
                            <i class="ri-swap-line text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Usuarios en línea --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card panel-card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="section-icon" style="background:#e8f5e9;color:#0ab39c;">
                        <i class="ri-user-heart-line"></i>
                    </span>
                    <h5 class="mb-0" style="font-size:.95rem;">Usuarios en línea</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success d-inline-flex align-items-center gap-1 px-3 py-1" id="onlineCount">
                        <span class="rounded-circle d-inline-block" style="width:7px;height:7px;background:#fff;"></span>
                        <span id="onlineCountNum">{{ $onlineUsers->count() }}</span> conectado(s)
                    </span>
                    <span class="text-muted" style="font-size:.7rem;">
                        <i class="ri-refresh-line"></i> <span id="onlineRefreshLabel">actualizado</span>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div id="onlineUsersContainer" class="row g-2">
                    @forelse($onlineGroups as $group)
                        @php
                            $groupIcon = match($group['type']) {
                                'responsable' => 'ri-user-star-line',
                                'cargo' => 'ri-briefcase-line',
                                default => 'ri-user-unknown-line',
                            };
                        @endphp
                        <div class="col-12 online-group-header" data-group-name="{{ $group['name'] }}" data-group-type="{{ $group['type'] }}">
                            <div class="d-flex align-items-center gap-2 px-1 py-2 mt-1">
                                <i class="{{ $groupIcon }}" style="font-size:.85rem;color:#6c757d;"></i>
                                <span style="font-size:.78rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.03em;">
                                    {{ $group['name'] }}
                                </span>
                                <span class="badge bg-light text-muted rounded-pill" style="font-size:.65rem;font-weight:600;">{{ count($group['users']) }}</span>
                            </div>
                        </div>
                        @foreach($group['users'] as $u)
                            @php
                                $iniU = strtoupper(collect(explode(' ', trim($u->fullname ?? $u->username)))->filter()->take(2)->map(fn($x)=>substr($x,0,1))->implode(''));
                                $colors = ['#163b6b','#299cdb','#0ab39c','#f7b84b','#f06548','#ec4899','#3577f1','#6f42c1'];
                                $color = $colors[crc32($u->id) % count($colors)];
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 online-user-item" data-user-id="{{ $u->id }}">
                                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8f9fa;border:1px solid #eef0f4;transition:all .15s;">
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                          style="width:34px;height:34px;background:{{ $color }};color:#fff;font-weight:700;font-size:.72rem;">
                                        {{ $iniU }}
                                    </span>
                                    <div class="min-width-0" style="flex:1;">
                                        <div class="fw-semibold text-truncate" style="font-size:.78rem;color:#212529;line-height:1.2;">
                                            {{ $u->fullname ?? $u->username }}
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="rounded-circle d-inline-block" style="width:6px;height:6px;background:#0ab39c;flex-shrink:0;"></span>
                                            <span style="font-size:.65rem;color:#0ab39c;font-weight:600;">En línea</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4" id="onlineEmpty">
                                <i class="ri-user-unfollow-line fs-32 d-block mb-2"></i>
                                <span style="font-size:.82rem;">No hay usuarios conectados en este momento</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráficos --}}
<div class="row g-3 mb-4">
    {{-- Chart 1: combo barras + línea --}}
    <div class="col-12">
        <div class="card panel-card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-card1-prog" role="tab" aria-selected="true">
                            <i class="ri-bar-chart-grouped-line me-1"></i> Programas
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-card1-upi" role="tab" aria-selected="false">
                            <i class="ri-graduation-cap-line me-1"></i> UPI Diplomados
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-card1-upi-auto" role="tab" aria-selected="false">
                            <i class="ri-graduation-cap-line me-1"></i> UPI Autogestionados
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted" style="font-size:.72rem;" id="lbl-card1-subtitle">Según fecha de inscripción · gestión <strong id="lbl-chart1-gestion">—</strong></small>
                </div>
            </div>
            <div class="card-body tab-content p-0">
                <div class="tab-pane active" id="tab-card1-prog" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart1">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-mensual" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart1-prog-count">0</span>
                                </div>
                                <div id="chart1-prog-summary" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart1-prog-list" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-card1-upi" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart1-upi">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-mensual-tipo6" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart1-prog-count-tipo6">0</span>
                                </div>
                                <div id="chart1-prog-summary-tipo6" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart1-prog-list-tipo6" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-card1-upi-auto" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart1-upi-auto">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-mensual-tipo7" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart1-prog-count-tipo7">0</span>
                                </div>
                                <div id="chart1-prog-summary-tipo7" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart1-prog-list-tipo7" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart 2 con detalle de programas --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card panel-card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-card2-prog" role="tab" aria-selected="true">
                            <i class="ri-calendar-event-line me-1"></i> Programas
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-card2-upi" role="tab" aria-selected="false">
                            <i class="ri-graduation-cap-line me-1"></i> UPI Diplomados
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-card2-upi-auto" role="tab" aria-selected="false">
                            <i class="ri-graduation-cap-line me-1"></i> UPI Autogestionados
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted" style="font-size:.72rem;" id="lbl-card2-subtitle">Filtrado por <code>programas.gestion = <span id="lbl-chart2-gestion">—</span></code></small>
                </div>
            </div>
            <div class="card-body tab-content p-0">
                <div class="tab-pane active" id="tab-card2-prog" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart2">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-prog-gestion" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart2-prog-count">0</span>
                                </div>
                                <div id="chart2-prog-summary" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart2-prog-list" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-card2-upi" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart2-upi">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-prog-gestion-tipo6" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart2-prog-count-tipo6">0</span>
                                </div>
                                <div id="chart2-prog-summary-tipo6" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart2-prog-list-tipo6" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-card2-upi-auto" role="tabpanel">
                    <div class="d-flex justify-content-end px-3 pt-3">
                        <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2" id="promedio-chart2-upi-auto">Promedio: 0</span>
                    </div>
                    <div class="row g-3 px-3 pb-3">
                        <div class="col-xl-7">
                            <div id="chart-prog-gestion-tipo7" class="chart-container"></div>
                        </div>
                        <div class="col-xl-5">
                            <div class="chart2-prog-panel">
                                <div class="chart2-panel-header">
                                    <i class="ri-list-check-2 text-info"></i>
                                    <span>Inscritos por mes</span>
                                    <span class="chart2-count" id="chart2-prog-count-tipo7">0</span>
                                </div>
                                <div id="chart2-prog-summary-tipo7" class="d-flex flex-wrap gap-1 px-3 pb-2 pt-1" style="min-height:0;"></div>
                                <div id="chart2-prog-list-tipo7" class="chart2-prog-list">
                                    <div class="text-center text-muted py-3" style="font-size:.8rem;">Cargando...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs: Programas + Estado No Ejecutados --}}
<div class="card panel-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-programas" role="tab" aria-selected="true">
                    <i class="ri-list-check me-1"></i> Inscritos por Programa
                    <span class="badge bg-soft-primary text-primary ms-1 fs-11" id="table-count">0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation" id="tab-no-ejecutados-nav">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-no-ejecutados" role="tab" aria-selected="false">
                    <i class="ri-close-circle-line me-1"></i> Estado No Ejecutados
                    <span class="badge bg-soft-danger text-danger ms-1 fs-11" id="table-count-no-ejecutados">0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation" id="tab-otras-nav">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-otras" role="tab" aria-selected="false">
                    <i class="ri-calendar-line me-1"></i> Otras Gestiones
                    <span class="badge bg-soft-warning text-warning ms-1 fs-11" id="table-count-otras">0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation" id="tab-tipo6-nav">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-tipo6" role="tab" aria-selected="false">
                    <i class="ri-graduation-cap-line me-1"></i> UPI Diplomados
                    <span class="badge bg-soft-info text-info ms-1 fs-11" id="table-count-tipo6">0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation" id="tab-tipo7-nav">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-tipo7" role="tab" aria-selected="false">
                    <i class="ri-graduation-cap-line me-1"></i> UPI Autogestionados
                    <span class="badge bg-soft-info text-info ms-1 fs-11" id="table-count-tipo7">0</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content p-0">
        {{-- Tab: Inscritos por Programa (chart4 + tabla) --}}
        <div class="tab-pane active" id="tab-programas" role="tabpanel">
            <div class="p-3 border-bottom">
                <div id="chart-programas" class="chart-container"></div>
                <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center mt-3" id="summary-chart-programas"></div>
            </div>
            <div class="table-responsive">
                <table class="program-table table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Programa</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Regulares</th>
                            <th class="text-center">Contado</th>
                            <th class="text-center">Traspasos</th>
                            <th class="text-center">Hombres</th>
                            <th class="text-center">Mujeres</th>
                            <th class="text-center" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Tab: Estado No Ejecutados (chart3 + tabla) --}}
        <div class="tab-pane" id="tab-no-ejecutados" role="tabpanel">
            <div class="p-3 border-bottom">
                <div id="chart-estado3" class="chart-container"></div>
                <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center mt-3" id="summary-chart-estado3"></div>
            </div>
            <div class="table-responsive">
                <table class="program-table table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Programa</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Regulares</th>
                            <th class="text-center">Contado</th>
                            <th class="text-center">Traspasos</th>
                            <th class="text-center">Hombres</th>
                            <th class="text-center">Mujeres</th>
                            <th class="text-center" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-no-ejecutados">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Tab: Otras Gestiones (chart + tabla) --}}
        <div class="tab-pane" id="tab-otras" role="tabpanel">
            <div class="p-3 border-bottom">
                <div id="chart-otras" class="chart-container"></div>
                <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center mt-3" id="summary-chart-otras"></div>
            </div>
            <div class="table-responsive">
                <table class="program-table table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Programa</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Regulares</th>
                            <th class="text-center">Contado</th>
                            <th class="text-center">Traspasos</th>
                            <th class="text-center">Hombres</th>
                            <th class="text-center">Mujeres</th>
                            <th class="text-center" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-otras">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Tab: UPI Diplomados (tipo_id = 6) --}}
        <div class="tab-pane" id="tab-tipo6" role="tabpanel">
            <div class="p-3 border-bottom">
                <div id="chart-tipo6" class="chart-container"></div>
                <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center mt-3" id="summary-chart-tipo6"></div>
            </div>
            <div class="table-responsive">
                <table class="program-table table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Programa</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Regulares</th>
                            <th class="text-center">Contado</th>
                            <th class="text-center">Traspasos</th>
                            <th class="text-center">Hombres</th>
                            <th class="text-center">Mujeres</th>
                            <th class="text-center" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-tipo6">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Tab: UPI Autogestionados (tipo_id = 7) --}}
        <div class="tab-pane" id="tab-tipo7" role="tabpanel">
            <div class="p-3 border-bottom">
                <div id="chart-tipo7" class="chart-container"></div>
                <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center mt-3" id="summary-chart-tipo7"></div>
            </div>
            <div class="table-responsive">
                <table class="program-table table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Programa</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Regulares</th>
                            <th class="text-center">Contado</th>
                            <th class="text-center">Traspasos</th>
                            <th class="text-center">Hombres</th>
                            <th class="text-center">Mujeres</th>
                            <th class="text-center" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-tipo7">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- Modal Participantes del Programa --}}
<div class="modal fade" id="modalProgramaParticipantes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="mpp-header-icon"><i class="ri-team-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="mpp-title">Participantes del Programa</h5>
                        <small class="mpp-subtitle" id="mpp-subtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="mpp-summary"></div>
                <div id="mpp-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterGestion = document.getElementById('filter-gestion');

    let chartMensual = null;
    let chartMensualTipo6 = null;
    let chartMensualTipo7 = null;
    let chartProgGestion = null;
    let chartProgGestionTipo6 = null;
    let chartProgGestionTipo7 = null;
    let chartEstado3 = null;
    let chartProgramas = null;
    let chartOtras = null;
    let chartTipo6 = null;
    let chartTipo7 = null;

    function loadStats() {
        const gestion = filterGestion.value;
        const params = new URLSearchParams({ gestion });

        document.getElementById('lbl-chart1-gestion').textContent = gestion;
        document.getElementById('lbl-chart2-gestion').textContent = gestion;

        fetch('{{ route("admin.dashboard.stats") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const r = data.resumen;
            document.getElementById('stat-inscritos').textContent = r.total_inscritos;
            document.getElementById('stat-regulares').textContent = r.regulares;
            document.getElementById('stat-contado').textContent = r.contado;
            document.getElementById('stat-hombres').textContent = r.hombres;
            document.getElementById('stat-mujeres').textContent = r.mujeres;
            document.getElementById('stat-programas').textContent = r.total_programas;
            document.getElementById('stat-traspasos').textContent = r.traspasos || 0;

            renderChart1(data.chart1);
            renderChart1PorMes(data.chart1_por_mes || []);
            renderChart1Tipo6(data.chart1_tipo6);
            renderChart1PorMesTipo6(data.chart1_por_mes_tipo6 || []);
            renderChart1Tipo7(data.chart1_tipo7);
            renderChart1PorMesTipo7(data.chart1_por_mes_tipo7 || []);
            renderChart2(data.chart2);
            renderChart2PorMes(data.chart2_por_mes || []);
            renderChart2Tipo6(data.chart2_tipo6);
            renderChart2PorMesTipo6(data.chart2_por_mes_tipo6 || []);
            renderChart2Tipo7(data.chart2_tipo7);
            renderChart2PorMesTipo7(data.chart2_por_mes_tipo7 || []);
            renderChart3(data.chart3);
            renderChart4(data.chart4);
            renderTable(data.programas);
            document.getElementById('table-count-no-ejecutados').textContent = (data.no_ejecutados || []).length + ' programas';
            renderTableNoEjecutados(data.no_ejecutados || []);
            document.getElementById('table-count-otras').textContent = (data.otras_gestiones || []).length + ' programas';
            renderChartOtras(data.chart_otras);
            renderTableOtras(data.otras_gestiones);
            renderChartTipo6(data.chart_tipo6);
            renderTableTipo6(data.tipo6);
            renderChartTipo7(data.chart_tipo7);
            renderTableTipo7(data.tipo7);
        })
        .catch(function () {
            document.getElementById('table-body').innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar datos</td></tr>';
        });
    }

    // --- Force ApexCharts re-render on tab show (fixes hidden container dimensions) ---
    document.addEventListener('shown.bs.tab', function () {
        setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 150);
    });

    // --- Chart 1: Combo (barras Regulares + barras Contado + línea Total) por mes ---
    function renderChart1(c) {
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart1').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartMensual) chartMensual.destroy();
        chartMensual = new ApexCharts(document.getElementById('chart-mensual'), opts);
        chartMensual.render();
    }

    // --- Chart 2: Inscritos por mes (programas.gestion) — mismo estilo que Chart 1 ---
    function renderChart2(c) {
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart2').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartProgGestion) chartProgGestion.destroy();
        chartProgGestion = new ApexCharts(document.getElementById('chart-prog-gestion'), opts);
        chartProgGestion.render();
    }

    // --- Panel agrupado por mes para Chart 1 ---
    function renderChart1PorMes(meses) {
        const cont = document.getElementById('chart1-prog-list');
        const countEl = document.getElementById('chart1-prog-count');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart1-prog-summary', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    // --- Panel agrupado por mes para Chart 2 ---
    function renderChart2PorMes(meses) {
        const cont = document.getElementById('chart2-prog-list');
        const countEl = document.getElementById('chart2-prog-count');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart2-prog-summary', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    // --- UPI Diplomados (tipo_id = 6) - Chart 1 ---
    function renderChart1Tipo6(c) {
        if (!c) return;
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart1-upi').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartMensualTipo6) chartMensualTipo6.destroy();
        chartMensualTipo6 = new ApexCharts(document.getElementById('chart-mensual-tipo6'), opts);
        chartMensualTipo6.render();
    }

    // --- UPI Diplomados (tipo_id = 6) - Chart 1 panel por mes ---
    function renderChart1PorMesTipo6(meses) {
        const cont = document.getElementById('chart1-prog-list-tipo6');
        const countEl = document.getElementById('chart1-prog-count-tipo6');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart1-prog-summary-tipo6', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos UPI registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    // --- UPI Diplomados (tipo_id = 6) - Chart 2 ---
    function renderChart2Tipo6(c) {
        if (!c) return;
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart2-upi').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartProgGestionTipo6) chartProgGestionTipo6.destroy();
        chartProgGestionTipo6 = new ApexCharts(document.getElementById('chart-prog-gestion-tipo6'), opts);
        chartProgGestionTipo6.render();
    }

    // --- UPI Diplomados (tipo_id = 6) - Chart 2 panel por mes ---
    function renderChart2PorMesTipo6(meses) {
        const cont = document.getElementById('chart2-prog-list-tipo6');
        const countEl = document.getElementById('chart2-prog-count-tipo6');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart2-prog-summary-tipo6', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos UPI registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    function mesTipoBreakdownHtml(tc) {
        if (!tc) return '';
        var parts = [];
        if (tc.Curso > 0) parts.push('<span><i class="ri-book-2-line text-info"></i> Cursos: <strong>' + tc.Curso + '</strong></span>');
        if (tc.Diplomado > 0) parts.push('<span><i class="ri-graduation-cap-line text-success"></i> Diplomados: <strong>' + tc.Diplomado + '</strong></span>');
        if (tc['Maestrías'] > 0) parts.push('<span><i class="ri-award-line text-warning"></i> Maestrías: <strong>' + tc['Maestrías'] + '</strong></span>');
        if (parts.length === 0) return '';
        return '<div class="chart2-mes-tipo-breakdown">' + parts.join(' · ') + '</div>';
    }

    // Renderiza resumen de totales debajo del gráfico
    function renderProgSummary(summaryId, meses) {
        const el = document.getElementById(summaryId);
        if (!el) return;
        const tipoInscritos = {};
        let totalInsc = 0;
        (meses || []).forEach(function (m) {
            (m.programas || []).forEach(function (p) {
                var tn = p.tipo_nombre || 'Otro';
                var cnt = p.total || 0;
                tipoInscritos[tn] = (tipoInscritos[tn] || 0) + cnt;
                totalInsc += cnt;
            });
        });
        if (totalInsc === 0) { el.innerHTML = ''; return; }
        var html = '<span class="badge bg-soft-dark text-dark fs-11 px-3 py-2 border-0">Total: <strong>' + totalInsc + '</strong></span>';
        if (tipoInscritos.Curso > 0) {
            html += '<span class="badge bg-soft-info text-info fs-11 px-3 py-2 border"><i class="ri-book-2-line me-1"></i>Cursos: <strong>' + tipoInscritos.Curso + '</strong></span>';
        }
        if (tipoInscritos.Diplomado > 0) {
            html += '<span class="badge bg-soft-success text-success fs-11 px-3 py-2 border"><i class="ri-graduation-cap-line me-1"></i>Diplomados: <strong>' + tipoInscritos.Diplomado + '</strong></span>';
        }
        if (tipoInscritos['Maestrías'] > 0) {
            html += '<span class="badge bg-soft-warning text-warning fs-11 px-3 py-2 border"><i class="ri-award-line me-1"></i>Maestrías: <strong>' + tipoInscritos['Maestrías'] + '</strong></span>';
        }
        el.innerHTML = html;
    }

    function renderChartSummary(summaryId, c) {
        const el = document.getElementById(summaryId);
        if (!el || !c) return;
        const labels = c.labels || [];
        const regs = c.regulares || [];
        const conts = c.contado || [];
        const trasps = c.traspasos || [];
        const nprog = labels.length;
        if (nprog === 0) { el.innerHTML = ''; return; }
        const totalReg = regs.reduce(function (a, b) { return a + b; }, 0);
        const totalCon = conts.reduce(function (a, b) { return a + b; }, 0);
        const totalTrasp = trasps.reduce(function (a, b) { return a + b; }, 0);
        const totalAll = totalReg + totalCon;

        var tipoHtml = '';
        if (c.tipo_counts) {
            if (c.tipo_counts.Curso > 0) {
                tipoHtml += '<span class="badge bg-soft-info text-info fs-11 px-3 py-2 border"><i class="ri-book-2-line me-1"></i>Cursos: <strong>' + c.tipo_counts.Curso + '</strong></span>';
            }
            if (c.tipo_counts.Diplomado > 0) {
                tipoHtml += '<span class="badge bg-soft-success text-success fs-11 px-3 py-2 border"><i class="ri-graduation-cap-line me-1"></i>Diplomados: <strong>' + c.tipo_counts.Diplomado + '</strong></span>';
            }
            if (c.tipo_counts['Maestrías'] > 0) {
                tipoHtml += '<span class="badge bg-soft-warning text-warning fs-11 px-3 py-2 border"><i class="ri-award-line me-1"></i>Maestrías: <strong>' + c.tipo_counts['Maestrías'] + '</strong></span>';
            }
        }

        el.innerHTML =
            '<span class="badge bg-soft-dark text-dark fs-11 px-3 py-2">Programas: <strong>' + nprog + '</strong></span>' +
            tipoHtml +
            '<span class="badge bg-soft-primary text-primary fs-11 px-3 py-2">Total: <strong>' + totalAll + '</strong></span>' +
            '<span class="badge bg-soft-info text-info fs-11 px-3 py-2">Regulares: <strong>' + totalReg + '</strong></span>' +
            '<span class="badge bg-soft-success text-success fs-11 px-3 py-2">Contado: <strong>' + totalCon + '</strong></span>' +
            '<span class="badge bg-soft-danger text-danger fs-11 px-3 py-2">Traspasos: <strong>' + totalTrasp + '</strong></span>';
    }

    // Render genérico para gráficos de barras horizontales apiladas por programa (Chart 3 y 4)
    function renderStackedProgramas(elId, c, instanceKey, summaryId) {
        const labels = c.labels || [];
        const regs = c.regulares || [];
        const conts = c.contado || [];
        const trasps = c.traspasos || [];
        const totals = labels.map(function (_, i) { return (regs[i] || 0) + (conts[i] || 0); });
        const enhancedLabels = labels.map(function (l, i) { return l + ' (' + totals[i] + ')'; });

        const opts = {
            chart: { type: 'bar', height: Math.max(360, labels.length * 38), stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', data: regs },
                { name: 'Contado',   data: conts },
                { name: 'Traspasos', data: trasps },
            ],
            xaxis: {
                categories: enhancedLabels,
                labels: { style: { fontSize: '11px' } },
                title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', fontWeight: 600 },
                    maxWidth: 320
                }
            },
            colors: ['#299cdb', '#0ab39c', '#f06548'],
            plotOptions: {
                bar: { horizontal: true, barHeight: '70%', borderRadius: 4, borderRadiusApplication: 'end' }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] },
                formatter: function (v) { return v > 0 ? v : ''; }
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
            noData: { text: 'Sin datos para la gestión seleccionada', style: { fontSize: '13px', color: '#878a99' } }
        };
        if (instanceKey === 'chart3' && chartEstado3) chartEstado3.destroy();
        if (instanceKey === 'chart4' && chartProgramas) chartProgramas.destroy();
        const inst = new ApexCharts(document.getElementById(elId), opts);
        inst.render();
        if (instanceKey === 'chart3') chartEstado3 = inst;
        if (instanceKey === 'chart4') chartProgramas = inst;
        if (summaryId) renderChartSummary(summaryId, c);
    }

    // --- Chart 3: Por programa filtrado por estado_id=3. Si vacío, oculta el tab ---
    function renderChart3(c) {
        const nav = document.getElementById('tab-no-ejecutados-nav');
        const hasData = c && c.labels && c.labels.length > 0;
        if (!hasData) {
            if (nav) nav.style.display = 'none';
            if (chartEstado3) { chartEstado3.destroy(); chartEstado3 = null; }
            document.getElementById('summary-chart-estado3').innerHTML = '';
            return;
        }
        if (nav) nav.style.display = '';
        renderStackedProgramas('chart-estado3', c, 'chart3', 'summary-chart-estado3');
    }

    // --- Chart 4: Por programa (todos, Regulares + Contado) ---
    function renderChart4(c) {
        renderStackedProgramas('chart-programas', c, 'chart4', 'summary-chart-programas');
    }

    // --- Colores por tipo de programa (id de tabla tipos) ---
    var TIPO_ROW_COLORS = {
        1: '#299cdb',  // Curso (info)
        2: '#0ab39c',  // Diplomado (success)
        3: '#ec4899',  // Especialidad
        4: '#f7b84b',  // Maestrías (warning)
        6: '#fd7e14',  // UPI Diplomados
        7: '#6f42c1'   // UPI Diplomado Autogestionado
    };
    function hexToRgba(hex, alpha) {
        var c = String(hex).replace('#', '');
        if (c.length === 3) { c = c[0] + c[0] + c[1] + c[1] + c[2] + c[2]; }
        var n = parseInt(c, 16);
        return 'rgba(' + ((n >> 16) & 255) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + alpha + ')';
    }
    function tipoRowColor(tipoId) {
        return TIPO_ROW_COLORS[tipoId] || '#6c757d';
    }
    function tipoRowBg(tipoId) {
        return hexToRgba(tipoRowColor(tipoId), 0.12);
    }

    function renderTable(programas) {
        const tbody = document.getElementById('table-body');
        document.getElementById('table-count').textContent = programas.length + ' programas';

        if (programas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Sin datos para la gestión seleccionada</td></tr>';
            return;
        }

        var sumTotal = 0, sumReg = 0, sumCon = 0, sumTrasp = 0, sumH = 0, sumM = 0;
        var html = '';
        programas.forEach(function (p, i) {
            sumTotal += p.total || 0;
            sumReg += p.regulares || 0;
            sumCon += p.contado || 0;
            sumTrasp += p.traspasos || 0;
            sumH += p.hombres || 0;
            sumM += p.mujeres || 0;
            const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
            var rowColor = tipoRowColor(p.tipo_id);
            html += '<tr style="background-color: ' + tipoRowBg(p.tipo_id) + ';">' +
                '<td class="text-center text-muted fw-semibold"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' + rowColor + ';margin-right:6px;vertical-align:middle;"></span>' + (i + 1) + '</td>' +
                '<td class="fw-medium">' + p.nombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-dark text-dark fs-12 fw-bold">' + p.total + '</span></td>' +
                '<td class="text-center text-info fw-semibold">' + p.regulares + '</td>' +
                '<td class="text-center text-success fw-semibold">' + p.contado + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + (p.traspasos || 0) + '</td>' +
                '<td class="text-center fw-semibold" style="color:#163b6b;">' + p.hombres + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + p.mujeres + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-participantes" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" title="Ver participantes inscritos">' +
                        '<i class="ri-eye-line"></i>' +
                    '</button>' +
                '</td>' +
                '</tr>';
        });
        html += '<tr class="fw-bold" style="background:#f3f5f9;">' +
            '<td colspan="3" class="text-end text-uppercase text-muted fs-11">Totales</td>' +
            '<td class="text-center"><span class="badge bg-dark text-white fs-12 fw-bold">' + sumTotal + '</span></td>' +
            '<td class="text-center text-info">' + sumReg + '</td>' +
            '<td class="text-center text-success">' + sumCon + '</td>' +
            '<td class="text-center text-danger">' + sumTrasp + '</td>' +
            '<td class="text-center" style="color:#163b6b;">' + sumH + '</td>' +
            '<td class="text-center text-danger">' + sumM + '</td>' +
            '<td></td>' +
            '</tr>';
        tbody.innerHTML = html;
    }

    // --- Chart Otras Gestiones ---
    function renderChartOtras(c) {
        const nav = document.getElementById('tab-otras-nav');
        if (chartOtras) { chartOtras.destroy(); chartOtras = null; }
        if (!c || !c.labels || !c.labels.length) {
            if (nav) nav.style.display = 'none';
            document.getElementById('chart-otras').innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>Sin datos</div>';
            document.getElementById('summary-chart-otras').innerHTML = '';
            return;
        }
        if (nav) nav.style.display = '';
        document.getElementById('chart-otras').innerHTML = '';

        const labels = c.labels || [];
        const regs = c.regulares || [];
        const conts = c.contado || [];
        const trasps = c.traspasos || [];
        const totals = labels.map(function (_, i) { return (regs[i] || 0) + (conts[i] || 0); });
        const enhancedLabels = labels.map(function (l, i) { return l + ' (' + totals[i] + ')'; });

        const opts = {
            chart: { type: 'bar', height: Math.max(360, labels.length * 38), stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', data: regs },
                { name: 'Contado',   data: conts },
                { name: 'Traspasos', data: trasps },
            ],
            xaxis: {
                categories: enhancedLabels,
                labels: { style: { fontSize: '11px' } },
                title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { fontSize: '11px', fontWeight: 600 }, maxWidth: 320 }
            },
            colors: ['#299cdb', '#0ab39c', '#f06548'],
            plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 4, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] }, formatter: function (v) { return v > 0 ? v : ''; } },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
            noData: { text: 'Sin datos para la gestión seleccionada', style: { fontSize: '13px', color: '#878a99' } }
        };
        chartOtras = new ApexCharts(document.getElementById('chart-otras'), opts);
        chartOtras.render();

        renderChartSummary('summary-chart-otras', c);
    }

    function renderTableNoEjecutados(programas) {
        const tbody = document.getElementById('table-body-no-ejecutados');
        if (!tbody) return;
        if (programas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4"><i class="ri-inbox-2-line me-1"></i>Sin programas no ejecutados</td></tr>';
            return;
        }
        var sumTotal = 0, sumReg = 0, sumCon = 0, sumTrasp = 0, sumH = 0, sumM = 0;
        let html = '';
        programas.forEach(function (p, i) {
            sumTotal += p.total || 0;
            sumReg += p.regulares || 0;
            sumCon += p.contado || 0;
            sumTrasp += p.traspasos || 0;
            sumH += p.hombres || 0;
            sumM += p.mujeres || 0;
            const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
            var rowColor = tipoRowColor(p.tipo_id);
            html += '<tr style="background-color: ' + tipoRowBg(p.tipo_id) + ';">' +
                '<td class="text-center text-muted fw-semibold"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' + rowColor + ';margin-right:6px;vertical-align:middle;"></span>' + (i + 1) + '</td>' +
                '<td class="fw-medium">' + p.nombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-dark text-dark fs-12 fw-bold">' + p.total + '</span></td>' +
                '<td class="text-center text-info fw-semibold">' + p.regulares + '</td>' +
                '<td class="text-center text-success fw-semibold">' + p.contado + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + (p.traspasos || 0) + '</td>' +
                '<td class="text-center fw-semibold" style="color:#163b6b;">' + p.hombres + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + p.mujeres + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-participantes" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" title="Ver participantes inscritos">' +
                        '<i class="ri-eye-line"></i>' +
                    '</button>' +
                '</td>' +
                '</tr>';
        });
        html += '<tr class="fw-bold" style="background:#f3f5f9;">' +
            '<td colspan="3" class="text-end text-uppercase text-muted fs-11">Totales</td>' +
            '<td class="text-center"><span class="badge bg-dark text-white fs-12 fw-bold">' + sumTotal + '</span></td>' +
            '<td class="text-center text-info">' + sumReg + '</td>' +
            '<td class="text-center text-success">' + sumCon + '</td>' +
            '<td class="text-center text-danger">' + sumTrasp + '</td>' +
            '<td class="text-center" style="color:#163b6b;">' + sumH + '</td>' +
            '<td class="text-center text-danger">' + sumM + '</td>' +
            '<td></td>' +
            '</tr>';
        tbody.innerHTML = html;
    }

    function renderTableOtras(programas) {
        const tbody = document.getElementById('table-body-otras');
        if (programas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">Sin datos de otras gestiones</td></tr>';
            return;
        }
        var sumTotal = 0, sumReg = 0, sumCon = 0, sumTrasp = 0, sumH = 0, sumM = 0;
        let html = '';
        programas.forEach(function (p, i) {
            sumTotal += p.total || 0;
            sumReg += p.regulares || 0;
            sumCon += p.contado || 0;
            sumTrasp += p.traspasos || 0;
            sumH += p.hombres || 0;
            sumM += p.mujeres || 0;
            const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
            var rowColor = tipoRowColor(p.tipo_id);
            html += '<tr style="background-color: ' + tipoRowBg(p.tipo_id) + ';">' +
                '<td class="text-center text-muted fw-semibold"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' + rowColor + ';margin-right:6px;vertical-align:middle;"></span>' + (i + 1) + '</td>' +
                '<td class="fw-medium">' + p.nombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-dark text-dark fs-12 fw-bold">' + p.total + '</span></td>' +
                '<td class="text-center text-info fw-semibold">' + p.regulares + '</td>' +
                '<td class="text-center text-success fw-semibold">' + p.contado + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + (p.traspasos || 0) + '</td>' +
                '<td class="text-center fw-semibold" style="color:#163b6b;">' + p.hombres + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + p.mujeres + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-participantes" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" title="Ver participantes inscritos">' +
                        '<i class="ri-eye-line"></i>' +
                    '</button>' +
                '</td>' +
                '</tr>';
        });
        html += '<tr class="fw-bold" style="background:#f3f5f9;">' +
            '<td colspan="3" class="text-end text-uppercase text-muted fs-11">Totales</td>' +
            '<td class="text-center"><span class="badge bg-dark text-white fs-12 fw-bold">' + sumTotal + '</span></td>' +
            '<td class="text-center text-info">' + sumReg + '</td>' +
            '<td class="text-center text-success">' + sumCon + '</td>' +
            '<td class="text-center text-danger">' + sumTrasp + '</td>' +
            '<td class="text-center" style="color:#163b6b;">' + sumH + '</td>' +
            '<td class="text-center text-danger">' + sumM + '</td>' +
            '<td></td>' +
            '</tr>';
        tbody.innerHTML = html;
    }

    // --- UPI Diplomados (tipo_id = 6) ---
    function renderChartTipo6(c) {
        const nav = document.getElementById('tab-tipo6-nav');
        if (chartTipo6) { chartTipo6.destroy(); chartTipo6 = null; }
        if (!c || !c.labels || !c.labels.length) {
            if (nav) nav.style.display = 'none';
            document.getElementById('chart-tipo6').innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>Sin datos de UPI Diplomados</div>';
            document.getElementById('summary-chart-tipo6').innerHTML = '';
            return;
        }
        if (nav) nav.style.display = '';
        document.getElementById('chart-tipo6').innerHTML = '';

        const labels = c.labels || [];
        const regs = c.regulares || [];
        const conts = c.contado || [];
        const trasps = c.traspasos || [];
        const totals = labels.map(function (_, i) { return (regs[i] || 0) + (conts[i] || 0); });
        const enhancedLabels = labels.map(function (l, i) { return l + ' (' + totals[i] + ')'; });

        const opts = {
            chart: { type: 'bar', height: Math.max(360, labels.length * 38), stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', data: regs },
                { name: 'Contado',   data: conts },
                { name: 'Traspasos', data: trasps },
            ],
            xaxis: {
                categories: enhancedLabels,
                labels: { style: { fontSize: '11px' } },
                title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { fontSize: '11px', fontWeight: 600 }, maxWidth: 320 }
            },
            colors: ['#299cdb', '#0ab39c', '#f06548'],
            plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 4, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] }, formatter: function (v) { return v > 0 ? v : ''; } },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
            noData: { text: 'Sin datos para la gestión seleccionada', style: { fontSize: '13px', color: '#878a99' } }
        };
        chartTipo6 = new ApexCharts(document.getElementById('chart-tipo6'), opts);
        chartTipo6.render();

        renderChartSummary('summary-chart-tipo6', c);
    }

    function renderTableTipo6(programas) {
        const tbody = document.getElementById('table-body-tipo6');
        if (!tbody) return;
        const countEl = document.getElementById('table-count-tipo6');
        if (countEl) countEl.textContent = (programas || []).length + ' programas';
        if (!programas || programas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">Sin datos de UPI Diplomados</td></tr>';
            return;
        }
        var sumTotal = 0, sumReg = 0, sumCon = 0, sumTrasp = 0, sumH = 0, sumM = 0;
        let html = '';
        programas.forEach(function (p, i) {
            sumTotal += p.total || 0;
            sumReg += p.regulares || 0;
            sumCon += p.contado || 0;
            sumTrasp += p.traspasos || 0;
            sumH += p.hombres || 0;
            sumM += p.mujeres || 0;
            const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
            var rowColor = tipoRowColor(p.tipo_id);
            html += '<tr style="background-color: ' + tipoRowBg(p.tipo_id) + ';">' +
                '<td class="text-center text-muted fw-semibold"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' + rowColor + ';margin-right:6px;vertical-align:middle;"></span>' + (i + 1) + '</td>' +
                '<td class="fw-medium">' + p.nombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-dark text-dark fs-12 fw-bold">' + p.total + '</span></td>' +
                '<td class="text-center text-info fw-semibold">' + p.regulares + '</td>' +
                '<td class="text-center text-success fw-semibold">' + p.contado + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + (p.traspasos || 0) + '</td>' +
                '<td class="text-center fw-semibold" style="color:#163b6b;">' + p.hombres + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + p.mujeres + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-participantes" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" title="Ver participantes inscritos">' +
                        '<i class="ri-eye-line"></i>' +
                    '</button>' +
                '</td>' +
                '</tr>';
        });
        html += '<tr class="fw-bold" style="background:#f3f5f9;">' +
            '<td colspan="3" class="text-end text-uppercase text-muted fs-11">Totales</td>' +
            '<td class="text-center"><span class="badge bg-dark text-white fs-12 fw-bold">' + sumTotal + '</span></td>' +
            '<td class="text-center text-info">' + sumReg + '</td>' +
            '<td class="text-center text-success">' + sumCon + '</td>' +
            '<td class="text-center text-danger">' + sumTrasp + '</td>' +
            '<td class="text-center" style="color:#163b6b;">' + sumH + '</td>' +
            '<td class="text-center text-danger">' + sumM + '</td>' +
            '<td></td>' +
            '</tr>';
        tbody.innerHTML = html;
    }

    // --- UPI Diplomados Autogestionados (tipo_id = 7) - Chart 1 ---
    function renderChart1Tipo7(c) {
        if (!c) return;
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart1-upi-auto').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartMensualTipo7) chartMensualTipo7.destroy();
        chartMensualTipo7 = new ApexCharts(document.getElementById('chart-mensual-tipo7'), opts);
        chartMensualTipo7.render();
    }

    // --- UPI Diplomados Autogestionados (tipo_id = 7) - Chart 1 panel por mes ---
    function renderChart1PorMesTipo7(meses) {
        const cont = document.getElementById('chart1-prog-list-tipo7');
        const countEl = document.getElementById('chart1-prog-count-tipo7');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart1-prog-summary-tipo7', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos UPI Autogestionados registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    // --- UPI Diplomados Autogestionados (tipo_id = 7) - Chart 2 ---
    function renderChart2Tipo7(c) {
        if (!c) return;
        var monthsWithData = c.total.filter(function (v) { return v > 0; }).length;
        var avg = monthsWithData > 0 ? Math.round(c.total.reduce(function (a, b) { return a + b; }, 0) / monthsWithData) : 0;
        document.getElementById('promedio-chart2-upi-auto').textContent = 'Promedio: ' + avg;

        const opts = {
            chart: { height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', type: 'column', data: c.regulares },
                { name: 'Contado',   type: 'column', data: c.contado },
                { name: 'Traspasos', type: 'column', data: c.traspasos },
                { name: 'Total',     type: 'line',   data: c.total },
            ],
            xaxis: { categories: c.labels, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
            yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
            colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'],
            stroke: { width: [0, 0, 0, 3], curve: 'smooth' },
            markers: {
                size: [0, 0, 0, 7],
                colors: ['#fff', '#fff', '#fff', '#f7b84b'],
                strokeColors: ['#fff', '#fff', '#fff', '#fff'],
                strokeWidth: 3,
                hover: { size: 9 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '65%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1, 2, 3],
                formatter: function (v) { return v > 0 ? v : ''; },
                style: { fontSize: '10px', fontWeight: 800, colors: ['#299cdb', '#0ab39c', '#f06548', '#163b6b'] },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: 1,
                    borderWidth: 1,
                    borderColor: '#eef0f4',
                    dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                },
                offsetY: -12,
                textAnchor: 'middle'
            },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: {
                shared: true, intersect: false,
                y: { formatter: function (v) { return v + ' inscritos'; } }
            },
        };
        if (chartProgGestionTipo7) chartProgGestionTipo7.destroy();
        chartProgGestionTipo7 = new ApexCharts(document.getElementById('chart-prog-gestion-tipo7'), opts);
        chartProgGestionTipo7.render();
    }

    // --- UPI Diplomados Autogestionados (tipo_id = 7) - Chart 2 panel por mes ---
    function renderChart2PorMesTipo7(meses) {
        const cont = document.getElementById('chart2-prog-list-tipo7');
        const countEl = document.getElementById('chart2-prog-count-tipo7');
        const totalMeses = (meses || []).length;
        if (countEl) countEl.textContent = totalMeses + (totalMeses === 1 ? ' mes' : ' meses');
        renderProgSummary('chart2-prog-summary-tipo7', meses);
        if (!meses || totalMeses === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.78rem;"><i class="ri-inbox-2-line d-block fs-4 mb-1"></i>Sin inscritos UPI Autogestionados registrados</div>';
            return;
        }
        var mesesRev = meses.slice().reverse();
        let html = '';
        mesesRev.forEach(function (mes) {
            html += '<div class="chart2-mes-block">' +
                '<div class="chart2-mes-header">' +
                    '<i class="ri-calendar-2-line"></i>' +
                    '<span>' + mes.mes_label + '</span>' +
                    '<span class="mes-total">' + mes.total + ' inscrito' + (mes.total !== 1 ? 's' : '') + '</span>' +
                '</div>' +
                mesTipoBreakdownHtml(mes.tipo_counts) +
                '<div class="chart2-mes-progs">';
            mes.programas.forEach(function (p) {
                const estado = p.estado ? p.estado : '—';
                const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
                html += '<div class="chart2-mes-prog-item">' +
                    '<div class="chart2-mes-prog-info">' +
                        '<div class="chart2-mes-prog-name" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" data-mes="' + mes.mes + '" title="' + p.nombre + '" style="cursor:pointer;">' + p.nombre + '</div>' +
                        '<div class="chart2-mes-prog-meta"><i class="ri-book-2-line"></i> ' + (p.tipo_nombre || '—') + ' · <i class="ri-hashtag"></i> ' + p.codigo + ' · <i class="ri-flag-2-line"></i> ' + estado + '</div>' +
                    '</div>' +
                    '<div class="chart2-mes-prog-counts">' +
                        '<span class="chart2-mes-prog-total">' + p.total + '</span>' +
                        '<span class="chart2-mes-prog-detail"><span class="text-info fw-semibold">' + (p.regulares || 0) + '</span> R · <span class="text-success fw-semibold">' + (p.contado || 0) + '</span> C · <span class="text-danger fw-semibold">' + (p.traspasos || 0) + '</span> T</span>' +
                    '</div>' +
                '</div>';
            });
            html += '</div></div>';
        });
        cont.innerHTML = html;
    }

    // --- UPI Diplomados Autogestionados (tipo_id = 7) - Tabla por programa ---
    function renderChartTipo7(c) {
        const nav = document.getElementById('tab-tipo7-nav');
        if (chartTipo7) { chartTipo7.destroy(); chartTipo7 = null; }
        if (!c || !c.labels || !c.labels.length) {
            if (nav) nav.style.display = 'none';
            document.getElementById('chart-tipo7').innerHTML = '<div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>Sin datos de UPI Autogestionados</div>';
            document.getElementById('summary-chart-tipo7').innerHTML = '';
            return;
        }
        if (nav) nav.style.display = '';
        document.getElementById('chart-tipo7').innerHTML = '';

        const labels = c.labels || [];
        const regs = c.regulares || [];
        const conts = c.contado || [];
        const trasps = c.traspasos || [];
        const totals = labels.map(function (_, i) { return (regs[i] || 0) + (conts[i] || 0); });
        const enhancedLabels = labels.map(function (l, i) { return l + ' (' + totals[i] + ')'; });

        const opts = {
            chart: { type: 'bar', height: Math.max(360, labels.length * 38), stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [
                { name: 'Regulares', data: regs },
                { name: 'Contado',   data: conts },
                { name: 'Traspasos', data: trasps },
            ],
            xaxis: {
                categories: enhancedLabels,
                labels: { style: { fontSize: '11px' } },
                title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { fontSize: '11px', fontWeight: 600 }, maxWidth: 320 }
            },
            colors: ['#299cdb', '#0ab39c', '#f06548'],
            plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 4, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700, colors: ['#fff'] }, formatter: function (v) { return v > 0 ? v : ''; } },
            legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
            grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
            tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
            noData: { text: 'Sin datos para la gestión seleccionada', style: { fontSize: '13px', color: '#878a99' } }
        };
        chartTipo7 = new ApexCharts(document.getElementById('chart-tipo7'), opts);
        chartTipo7.render();

        renderChartSummary('summary-chart-tipo7', c);
    }

    function renderTableTipo7(programas) {
        const tbody = document.getElementById('table-body-tipo7');
        if (!tbody) return;
        const countEl = document.getElementById('table-count-tipo7');
        if (countEl) countEl.textContent = (programas || []).length + ' programas';
        if (!programas || programas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">Sin datos de UPI Autogestionados</td></tr>';
            return;
        }
        var sumTotal = 0, sumReg = 0, sumCon = 0, sumTrasp = 0, sumH = 0, sumM = 0;
        let html = '';
        programas.forEach(function (p, i) {
            sumTotal += p.total || 0;
            sumReg += p.regulares || 0;
            sumCon += p.contado || 0;
            sumTrasp += p.traspasos || 0;
            sumH += p.hombres || 0;
            sumM += p.mujeres || 0;
            const nombreEsc = String(p.nombre).replace(/"/g, '&quot;');
            var rowColor = tipoRowColor(p.tipo_id);
            html += '<tr style="background-color: ' + tipoRowBg(p.tipo_id) + ';">' +
                '<td class="text-center text-muted fw-semibold"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' + rowColor + ';margin-right:6px;vertical-align:middle;"></span>' + (i + 1) + '</td>' +
                '<td class="fw-medium">' + p.nombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary fs-11">' + p.codigo + '</span></td>' +
                '<td class="text-center"><span class="badge bg-soft-dark text-dark fs-12 fw-bold">' + p.total + '</span></td>' +
                '<td class="text-center text-info fw-semibold">' + p.regulares + '</td>' +
                '<td class="text-center text-success fw-semibold">' + p.contado + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + (p.traspasos || 0) + '</td>' +
                '<td class="text-center fw-semibold" style="color:#163b6b;">' + p.hombres + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + p.mujeres + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-soft-primary btn-ver-participantes" data-programa-id="' + p.id + '" data-programa-nombre="' + nombreEsc + '" data-programa-codigo="' + p.codigo + '" title="Ver participantes inscritos">' +
                        '<i class="ri-eye-line"></i>' +
                    '</button>' +
                '</td>' +
                '</tr>';
        });
        html += '<tr class="fw-bold" style="background:#f3f5f9;">' +
            '<td colspan="3" class="text-end text-uppercase text-muted fs-11">Totales</td>' +
            '<td class="text-center"><span class="badge bg-dark text-white fs-12 fw-bold">' + sumTotal + '</span></td>' +
            '<td class="text-center text-info">' + sumReg + '</td>' +
            '<td class="text-center text-success">' + sumCon + '</td>' +
            '<td class="text-center text-danger">' + sumTrasp + '</td>' +
            '<td class="text-center" style="color:#163b6b;">' + sumH + '</td>' +
            '<td class="text-center text-danger">' + sumM + '</td>' +
            '<td></td>' +
            '</tr>';
        tbody.innerHTML = html;
    }

    // ---- Clic en nombre de programa (Inscritos por Mes) ----
    document.addEventListener('click', function (e) {
        const prog = e.target.closest('.chart2-mes-prog-name');
        if (prog && prog.dataset.programaId) {
            const id = prog.dataset.programaId;
            const nombre = prog.dataset.programaNombre;
            const codigo = prog.dataset.programaCodigo;
            const mes = prog.dataset.mes;

            const mesesEsH = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            const mesLabel = mes ? (mesesEsH[parseInt(mes, 10) - 1] || mes) : '';

            document.getElementById('mpp-title').textContent = nombre;
            document.getElementById('mpp-subtitle').textContent = (mesLabel ? mesLabel.charAt(0).toUpperCase() + mesLabel.slice(1) + ' · ' : '') + 'Código: ' + codigo;
            document.getElementById('mpp-body').innerHTML =
                '<div class="text-center py-5">' +
                    '<div class="spinner-border text-primary" role="status"></div>' +
                    '<p class="mt-2 text-muted mb-0">Cargando participantes...</p>' +
                '</div>';
            document.getElementById('mpp-summary').innerHTML = '';

            new bootstrap.Modal(document.getElementById('modalProgramaParticipantes')).show();

            fetch('{{ url("admin/dashboard/programa") }}/' + id + '/participantes?mes=' + mes, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('mpp-body').innerHTML = '<div class="text-center text-danger py-4">' + (data.message || 'Error') + '</div>';
                    return;
                }
                const r = data.resumen;
                document.getElementById('mpp-summary').innerHTML =
                    '<div class="mpp-chip mpp-total"><i class="ri-team-line"></i> Total <strong>' + r.total + '</strong></div>' +
                    '<div class="mpp-chip mpp-info"><i class="ri-calendar-todo-line"></i> Regulares <strong>' + r.regulares + '</strong></div>' +
                    '<div class="mpp-chip mpp-success"><i class="ri-money-dollar-box-line"></i> Contado <strong>' + r.contado + '</strong></div>' +
                    '<div class="mpp-chip mpp-traspaso"><i class="ri-swap-line"></i> Traspasos <strong>' + (r.traspasos || 0) + '</strong></div>' +
                    '<div class="mpp-chip mpp-male"><i class="ri-men-line"></i> Hombres <strong>' + r.hombres + '</strong></div>' +
                    '<div class="mpp-chip mpp-female"><i class="ri-women-line"></i> Mujeres <strong>' + r.mujeres + '</strong></div>';

                if (data.participantes.length === 0) {
                    document.getElementById('mpp-body').innerHTML =
                        '<div class="mpp-empty">' +
                            '<div class="mpp-empty-icon"><i class="ri-user-search-line"></i></div>' +
                            '<h6 class="text-dark fw-bold mb-1">Sin participantes inscritos</h6>' +
                            '<p class="text-muted mb-0">Este programa no tiene inscripciones activas.</p>' +
                        '</div>';
                    return;
                }

                const mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                function fechaEs(v) {
                    if (!v) return '—';
                    const m = String(v).match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (m) return parseInt(m[3], 10) + ' de ' + mesesEs[parseInt(m[2], 10) - 1] + ' del ' + m[1];
                    return v;
                }

                let rows = '';
                data.participantes.forEach(function (p, i) {
                    const ini = (p.nombres.charAt(0) + p.apellidos.charAt(0)).toUpperCase();
                    const isFemale = (p.sexo || '').toUpperCase().charAt(0) === 'M' && p.sexo !== 'Hombre' ? false : (p.sexo === 'Mujer');
                    const sexoLabel = p.sexo || '—';
                    const sexoClass = (p.sexo === 'Mujer') ? 'female' : (p.sexo === 'Hombre' ? 'male' : 'muted');
                    const sexoIcon = (p.sexo === 'Mujer') ? 'ri-women-line' : (p.sexo === 'Hombre' ? 'ri-men-line' : 'ri-user-line');
                    const planClass = p.contado == 1 ? 'plan-contado' : 'plan-regular';
                    const planLabel = p.contado == 1 ? 'Contado' : 'Regular';
                    const asesorLabel = p.asesor_apellidos ? (p.asesor_apellidos + ', ' + p.asesor_nombres) : '—';
                    const traspasoClass = p.es_traspaso == 1 ? 'mpp-traspaso-row' : '';
                    const traspasoBadge = p.es_traspaso == 1 ? '<span class="mpp-pill mpp-pill-traspaso"><i class="ri-swap-line"></i> Traspaso</span>' : '';
                    rows +=
                        '<tr class="mpp-mes-highlight ' + traspasoClass + '">' +
                            '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                            '<td>' +
                                '<div class="d-flex align-items-center gap-2">' +
                                    '<div class="mpp-avatar ' + sexoClass + '">' + ini + '</div>' +
                                    '<div style="min-width:0;">' +
                                        '<div class="mpp-nombre text-truncate" title="' + p.apellidos + ', ' + p.nombres + '">' + p.apellidos + ', ' + p.nombres + '</div>' +
                                        '<div class="mpp-ci"><i class="ri-profile-line align-bottom"></i> CI: ' + p.ci + (p.departamento ? ' · ' + p.departamento : '') + '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td class="text-center">' +
                                '<span class="mpp-pill mpp-sexo-' + sexoClass + '"><i class="' + sexoIcon + '"></i> ' + sexoLabel + '</span>' +
                            '</td>' +
                            '<td class="text-center">' +
                                '<span class="mpp-pill ' + planClass + '">' + planLabel + '</span>' +
                            '</td>' +
                            '<td class="text-center">' +
                                traspasoBadge +
                            '</td>' +
                            '<td style="font-size:.78rem;">' +
                                asesorLabel +
                            '</td>' +
                            '<td class="mpp-contact">' +
                                (p.celular ? '<div><i class="ri-phone-line text-muted me-1"></i>' + p.celular + '</div>' : '') +
                                (p.correo ? '<div class="text-truncate" style="max-width:180px;" title="' + p.correo + '"><i class="ri-mail-line text-muted me-1"></i>' + p.correo + '</div>' : '') +
                                (!p.celular && !p.correo ? '<span class="text-muted fst-italic">Sin contacto</span>' : '') +
                            '</td>' +
                            '<td class="text-muted" style="font-size:.78rem;">' +
                                '<i class="ri-calendar-2-line align-bottom me-1"></i>' + fechaEs(p.fecha_inscripcion) +
                            '</td>' +
                        '</tr>';
                });

                document.getElementById('mpp-body').innerHTML =
                    '<div class="mpp-table-wrap">' +
                        '<div class="table-responsive">' +
                            '<table class="table mb-0 mpp-table">' +
                                '<thead>' +
                                    '<tr>' +
                                        '<th style="width: 42px;" class="text-center">#</th>' +
                                        '<th>Participante</th>' +
                                        '<th style="width: 110px;" class="text-center">Sexo</th>' +
                                        '<th style="width: 90px;" class="text-center">Plan</th>' +
                                        '<th style="width: 100px;" class="text-center">Traspaso</th>' +
                                        '<th style="width: 180px;">Asesor</th>' +
                                        '<th style="width: 220px;">Contacto</th>' +
                                        '<th style="width: 180px;">Fecha Inscripción</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' + rows + '</tbody>' +
                            '</table>' +
                        '</div>' +
                    '</div>';
            })
            .catch(() => {
                document.getElementById('mpp-body').innerHTML = '<div class="text-center text-danger py-4">Error al cargar los participantes</div>';
            });
            return;
        }

        const btn = e.target.closest('.btn-ver-participantes');
        if (!btn) return;
        const id = btn.dataset.programaId;
        const nombre = btn.dataset.programaNombre;
        const codigo = btn.dataset.programaCodigo;

        document.getElementById('mpp-title').textContent = nombre;
        document.getElementById('mpp-subtitle').textContent = 'Código: ' + codigo;
        document.getElementById('mpp-body').innerHTML =
            '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<p class="mt-2 text-muted mb-0">Cargando participantes...</p>' +
            '</div>';
        document.getElementById('mpp-summary').innerHTML = '';

        new bootstrap.Modal(document.getElementById('modalProgramaParticipantes')).show();

        fetch('{{ url("admin/dashboard/programa") }}/' + id + '/participantes', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('mpp-body').innerHTML = '<div class="text-center text-danger py-4">' + (data.message || 'Error') + '</div>';
                return;
            }
            const r = data.resumen;
            document.getElementById('mpp-summary').innerHTML =
                '<div class="mpp-chip mpp-total"><i class="ri-team-line"></i> Total <strong>' + r.total + '</strong></div>' +
                '<div class="mpp-chip mpp-info"><i class="ri-calendar-todo-line"></i> Regulares <strong>' + r.regulares + '</strong></div>' +
                '<div class="mpp-chip mpp-success"><i class="ri-money-dollar-box-line"></i> Contado <strong>' + r.contado + '</strong></div>' +
                '<div class="mpp-chip mpp-traspaso"><i class="ri-swap-line"></i> Traspasos <strong>' + (r.traspasos || 0) + '</strong></div>' +
                '<div class="mpp-chip mpp-male"><i class="ri-men-line"></i> Hombres <strong>' + r.hombres + '</strong></div>' +
                '<div class="mpp-chip mpp-female"><i class="ri-women-line"></i> Mujeres <strong>' + r.mujeres + '</strong></div>';

            if (data.participantes.length === 0) {
                document.getElementById('mpp-body').innerHTML =
                    '<div class="mpp-empty">' +
                        '<div class="mpp-empty-icon"><i class="ri-user-search-line"></i></div>' +
                        '<h6 class="text-dark fw-bold mb-1">Sin participantes inscritos</h6>' +
                        '<p class="text-muted mb-0">Este programa no tiene inscripciones activas.</p>' +
                    '</div>';
                return;
            }

            const mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            function fechaEs(v) {
                if (!v) return '—';
                const m = String(v).match(/^(\d{4})-(\d{2})-(\d{2})/);
                if (m) return parseInt(m[3], 10) + ' de ' + mesesEs[parseInt(m[2], 10) - 1] + ' del ' + m[1];
                return v;
            }

            let rows = '';
            data.participantes.forEach(function (p, i) {
                const ini = (p.nombres.charAt(0) + p.apellidos.charAt(0)).toUpperCase();
                const isFemale = (p.sexo || '').toUpperCase().charAt(0) === 'M' && p.sexo !== 'Hombre' ? false : (p.sexo === 'Mujer');
                const sexoLabel = p.sexo || '—';
                const sexoClass = (p.sexo === 'Mujer') ? 'female' : (p.sexo === 'Hombre' ? 'male' : 'muted');
                const sexoIcon = (p.sexo === 'Mujer') ? 'ri-women-line' : (p.sexo === 'Hombre' ? 'ri-men-line' : 'ri-user-line');
                const planClass = p.contado == 1 ? 'plan-contado' : 'plan-regular';
                    const planLabel = p.contado == 1 ? 'Contado' : 'Regular';
                    const asesorLabel = p.asesor_apellidos ? (p.asesor_apellidos + ', ' + p.asesor_nombres) : '—';
                    const traspasoClass = p.es_traspaso == 1 ? 'mpp-traspaso-row' : '';
                    const traspasoBadge = p.es_traspaso == 1 ? '<span class="mpp-pill mpp-pill-traspaso"><i class="ri-swap-line"></i> Traspaso</span>' : '';
                    rows +=
                        '<tr class="' + traspasoClass + '">' +
                            '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                            '<td>' +
                                '<div class="d-flex align-items-center gap-2">' +
                                    '<div class="mpp-avatar ' + sexoClass + '">' + ini + '</div>' +
                                    '<div style="min-width:0;">' +
                                        '<div class="mpp-nombre text-truncate" title="' + p.apellidos + ', ' + p.nombres + '">' + p.apellidos + ', ' + p.nombres + '</div>' +
                                        '<div class="mpp-ci"><i class="ri-profile-line align-bottom"></i> CI: ' + p.ci + (p.departamento ? ' · ' + p.departamento : '') + '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td class="text-center">' +
                                '<span class="mpp-pill mpp-sexo-' + sexoClass + '"><i class="' + sexoIcon + '"></i> ' + sexoLabel + '</span>' +
                            '</td>' +
                            '<td class="text-center">' +
                                '<span class="mpp-pill ' + planClass + '">' + planLabel + '</span>' +
                            '</td>' +
                            '<td class="text-center">' +
                                traspasoBadge +
                            '</td>' +
                            '<td style="font-size:.78rem;">' +
                                asesorLabel +
                            '</td>' +
                            '<td class="mpp-contact">' +
                                (p.celular ? '<div><i class="ri-phone-line text-muted me-1"></i>' + p.celular + '</div>' : '') +
                                (p.correo ? '<div class="text-truncate" style="max-width:180px;" title="' + p.correo + '"><i class="ri-mail-line text-muted me-1"></i>' + p.correo + '</div>' : '') +
                                (!p.celular && !p.correo ? '<span class="text-muted fst-italic">Sin contacto</span>' : '') +
                            '</td>' +
                            '<td class="text-muted" style="font-size:.78rem;">' +
                                '<i class="ri-calendar-2-line align-bottom me-1"></i>' + fechaEs(p.fecha_inscripcion) +
                            '</td>' +
                        '</tr>';
            });

            document.getElementById('mpp-body').innerHTML =
                '<div class="mpp-table-wrap">' +
                    '<div class="table-responsive">' +
                        '<table class="table mb-0 mpp-table">' +
                            '<thead>' +
                                '<tr>' +
                                    '<th style="width: 42px;" class="text-center">#</th>' +
                                    '<th>Participante</th>' +
                                    '<th style="width: 110px;" class="text-center">Sexo</th>' +
                                    '<th style="width: 90px;" class="text-center">Plan</th>' +
                                        '<th style="width: 100px;" class="text-center">Traspaso</th>' +
                                        '<th style="width: 180px;">Asesor</th>' +
                                        '<th style="width: 220px;">Contacto</th>' +
                                        '<th style="width: 180px;">Fecha Inscripción</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' + rows + '</tbody>' +
                            '</table>' +
                        '</div>' +
                    '</div>';
        })
        .catch(() => {
            document.getElementById('mpp-body').innerHTML = '<div class="text-center text-danger py-4">Error al cargar los participantes</div>';
        });
    });

    // ---- Tabs: redimensiona gráficos al cambiar de tab ----
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (tab) {
        tab.addEventListener('shown.bs.tab', function () {
            if (chartProgramas) chartProgramas.update();
            if (chartEstado3) chartEstado3.update();
            if (chartOtras) chartOtras.update();
        });
    });

    filterGestion.addEventListener('change', loadStats);

    loadStats();

    // ---- Online users polling ----
    const onlineUrl = '{{ route("admin.dashboard.online-users") }}';
    const onlineColors = ['#163b6b','#299cdb','#0ab39c','#f7b84b','#f06548','#ec4899','#3577f1','#6f42c1'];

    function updateOnlineUsers() {
        fetch(onlineUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var totalOnline = data.total_online || 0;
            var countEl = document.getElementById('onlineCountNum');
            if (countEl) countEl.textContent = totalOnline;

            document.getElementById('onlineRefreshLabel').textContent = new Date().toLocaleTimeString();

            var container = document.getElementById('onlineUsersContainer');
            if (!container) return;

            if (totalOnline === 0) {
                container.innerHTML =
                    '<div class="col-12">' +
                        '<div class="text-center text-muted py-4" id="onlineEmpty">' +
                            '<i class="ri-user-unfollow-line fs-32 d-block mb-2"></i>' +
                            '<span style="font-size:.82rem;">No hay usuarios conectados en este momento</span>' +
                        '</div>' +
                    '</div>';
                return;
            }

            function groupIcon(type) {
                if (type === 'responsable') return 'ri-user-star-line';
                if (type === 'cargo') return 'ri-briefcase-line';
                return 'ri-user-unknown-line';
            }

            var html = '';
            (data.groups || []).forEach(function (g) {
                html +=
                    '<div class="col-12 online-group-header" data-group-name="' + g.name + '" data-group-type="' + (g.type || '') + '">' +
                        '<div class="d-flex align-items-center gap-2 px-1 py-2 mt-1">' +
                            '<i class="' + groupIcon(g.type) + '" style="font-size:.85rem;color:#6c757d;"></i>' +
                            '<span style="font-size:.78rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.03em;">' +
                                g.name +
                            '</span>' +
                            '<span class="badge bg-light text-muted rounded-pill" style="font-size:.65rem;font-weight:600;">' + (g.count || g.users.length) + '</span>' +
                        '</div>' +
                    '</div>';

                (g.users || []).forEach(function (u) {
                    if (!u.is_online) return;
                    var color = onlineColors[Math.abs(u.id) % onlineColors.length];
                    html +=
                        '<div class="col-lg-3 col-md-4 col-sm-6 online-user-item" data-user-id="' + u.id + '">' +
                            '<div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8f9fa;border:1px solid #eef0f4;transition:all .15s;">' +
                                '<span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;background:' + color + ';color:#fff;font-weight:700;font-size:.72rem;">' +
                                    u.iniciales +
                                '</span>' +
                                '<div style="flex:1;min-width:0;">' +
                                    '<div class="fw-semibold text-truncate" style="font-size:.78rem;color:#212529;line-height:1.2;">' +
                                        u.fullname +
                                    '</div>' +
                                    '<div class="d-flex align-items-center gap-1">' +
                                        '<span class="rounded-circle d-inline-block" style="width:6px;height:6px;background:#0ab39c;flex-shrink:0;"></span>' +
                                        '<span style="font-size:.65rem;color:#0ab39c;font-weight:600;">En línea</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                });
            });
            container.innerHTML = html;
        })
        .catch(function () {});
    }

    setInterval(updateOnlineUsers, 30000);
});
</script>
@endpush
