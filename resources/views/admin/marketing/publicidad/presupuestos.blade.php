@extends('admin.layouts.master')

@section('title', 'Presupuestos de Publicidad - ' . config('app.name'))

@push('css')
<style>
.mkt-page { --pres-line: #eceff4; --pres-ink: #1e293b; --pres-muted: #6b7280; --pres-soft: #fafbfd; }

/* ─── Hero ─── */
.pub-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.7rem 1.9rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -24px rgba(2,6,23,.5);
    margin-bottom: 1.5rem;
}
.pub-hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 42%),
                radial-gradient(circle at bottom left, rgba(0,0,0,.18), transparent 42%);
    pointer-events: none;
}
.pub-hero .hero-icon {
    width: 60px; height: 60px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 16px;
    font-size: 1.9rem;
}
.pub-hero h4 { color: #fff; font-weight: 800; font-size: 1.45rem; margin: 0; letter-spacing: -.5px; }
.pub-hero .hero-sub { color: rgba(255,255,255,.88); font-size: .93rem; margin: 0; font-weight: 500; }
.pub-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.pub-hero .breadcrumb-item, .pub-hero .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .82rem; font-weight: 500; }
.pub-hero .breadcrumb-item a:hover { color: #fff; }
.pub-hero .breadcrumb-item.active { color: #fff; font-weight: 700; }
.pub-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

/* ─── Resumen ─── */
.stat-card {
    border: 1px solid var(--pres-line);
    border-radius: 14px;
    background: #fff;
    padding: .85rem 1rem;
    transition: transform .25s ease, box-shadow .25s ease;
}
.stat-card .card-body { padding: 0; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 26px -18px rgba(2,6,23,.35); }
.stat-card .stat-label { font-size: .72rem; color: var(--pres-muted); font-weight: 600; margin: 0; }
.stat-card .stat-value { font-size: 1.3rem; font-weight: 800; color: var(--pres-ink); letter-spacing: -.4px; font-variant-numeric: tabular-nums; }
.stat-card .stat-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.3rem; }

/* ─── Filtros ─── */
.filter-card { border: 1px solid var(--pres-line); border-radius: 14px; background: #fff; }
.filter-card .card-header { background: transparent; border-bottom: 1px solid var(--pres-line); padding: 1rem 1.25rem; }
.form-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 700; color: var(--pres-muted); margin-bottom: .35rem; }

/* ─── Acordeón de periodos ─── */
.period-list { display: flex; flex-direction: column; gap: 1rem; }
.period-card {
    background: #fff;
    border: 1px solid var(--pres-line);
    border-radius: 14px;
    overflow: hidden;
}
.period-head { display: flex; align-items: center; gap: 1rem; padding: .9rem 1.1rem; cursor: pointer; user-select: none; flex-wrap: wrap; }
.period-head:hover { background: var(--pres-soft); }
.period-head:focus-visible { outline: 2px solid #405189; outline-offset: -2px; }
.period-icon {
    width: 42px; height: 42px; flex: 0 0 42px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px; background: linear-gradient(135deg, #405189, #5e72b8);
    color: #fff; font-size: 1.2rem; box-shadow: 0 4px 12px -5px rgba(0,0,0,.4);
}
.period-title h6 { font-size: 1rem; font-weight: 700; letter-spacing: -.01em; color: var(--pres-ink); margin: 0; }
.period-sub { font-size: .72rem; color: var(--pres-muted); margin-top: 1px; }
.period-metrics { display: flex; align-items: center; gap: 1.5rem; margin-left: auto; flex-wrap: wrap; }
.pm { display: flex; flex-direction: column; align-items: flex-end; }
.pm-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: var(--pres-muted); font-weight: 700; }
.pm-value { font-size: .92rem; font-weight: 700; color: var(--pres-ink); font-variant-numeric: tabular-nums; }
.bar { width: 108px; height: 7px; background: #e9edf2; border-radius: 99px; overflow: hidden; margin-top: 4px; }
.bar > span { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, #0ab39c, #299cdb); transform-origin: left center; transform: scaleX(var(--v, 0)); animation: barIn .6s cubic-bezier(.2, .8, .2, 1) both; }
.bar.warn > span { background: linear-gradient(90deg, #f7b84b, #f06548); }
@keyframes barIn { from { transform: scaleX(0); } to { transform: scaleX(var(--v, 0)); } }
@media (prefers-reduced-motion: reduce) { .bar > span { animation: none; } }
.pm-pct { font-size: .7rem; font-weight: 700; color: var(--pres-muted); margin-top: 3px; font-variant-numeric: tabular-nums; }
.period-status { font-size: .72rem; font-weight: 700; padding: .3rem .65rem; border-radius: 99px; white-space: nowrap; }
.period-status.open { background: #e7f8f4; color: #067a68; }
.period-status.closed { background: #eef0f4; color: #5a6472; }
.period-chevron { font-size: 1.4rem; color: #9aa4b2; transition: transform .3s ease; }
.period-head[aria-expanded="true"] .period-chevron { transform: rotate(180deg); }
.period-body .q-block:first-child > .q-head { border-top: 1px solid var(--pres-line); }

/* ─── Bloque de quincena ─── */
.q-block + .q-block { border-top: 1px dashed #e6eaf0; }
.q-head { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .7rem 1.1rem; background: var(--pres-soft); flex-wrap: wrap; }
.q-sum { font-size: .72rem; color: var(--pres-muted); }
.q-sum b { color: var(--pres-ink); font-variant-numeric: tabular-nums; }
.q-empty { padding: 1.4rem 1.1rem; text-align: center; color: var(--pres-muted); font-size: .82rem; }

.badge-q1, .badge-q2 { font-weight: 700; padding: .3rem .7rem; font-size: .7rem; border-radius: 99px; letter-spacing: .02em; }
.badge-q1 { background: #e6efff; color: #1d4ed8; }
.badge-q2 { background: #fff3d6; color: #7a4a03; }

/* ─── Tabla de presupuestos ─── */
.pres-table { margin: 0; }
.pres-table thead th {
    font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280;
    font-weight: 700; border-bottom: 1px solid var(--pres-line); background: #fff;
    padding: .55rem .9rem; white-space: nowrap;
}
.pres-table tbody td { padding: .68rem .9rem; border-bottom: 1px solid #f2f4f8; vertical-align: middle; font-size: .82rem; color: #2b3445; }
.pres-table tbody tr:hover { background: var(--pres-soft); }
.num { font-variant-numeric: tabular-nums; }
.amount-strong { font-weight: 700; color: var(--pres-ink); }
.pres-asesor { display: flex; align-items: center; gap: .6rem; }
.pres-avatar { width: 32px; height: 32px; flex: 0 0 32px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; background: #fff7e6; color: #c47f17; font-size: .95rem; }
.pres-avatar-sm { width: 26px; height: 26px; flex: 0 0 26px; border-radius: 8px; font-size: .78rem; background: #fdf1e3; color: #b06a1a; }
/* Filas de derivación anidadas bajo el asesor principal */
.row-derivado > td { background: #fbfcfe; }
.row-derivado:hover > td { background: #f5f8fd; }
.deriv-cell { padding-left: 1.5rem; position: relative; }
.deriv-cell::before { content: ''; position: absolute; left: .5rem; top: 0; bottom: 0; width: 1px; background: #e2e7ef; }
.deriv-cell::after { content: ''; position: absolute; left: .5rem; top: 50%; width: .8rem; height: 1px; background: #e2e7ef; }
.deriv-suelto { padding-left: .9rem; position: relative; }
.deriv-suelto::before { content: ''; position: absolute; left: .15rem; top: .2rem; bottom: .2rem; width: 3px; border-radius: 3px; background: #f6ddae; }
tr.ultimo-derivado .deriv-cell::before { bottom: 50%; }
.pres-name { font-weight: 600; color: var(--pres-ink); display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
.pres-sub { font-size: .75rem; color: var(--pres-muted); }
.pres-sub-id { color: #94a3b8; font-variant-numeric: tabular-nums; letter-spacing: .01em; }
.chip { display: inline-flex; align-items: center; gap: .25rem; padding: .16rem .55rem; border-radius: 99px; font-size: .72rem; font-weight: 700; background: #eef2fb; color: #3b4c86; font-variant-numeric: tabular-nums; }
.chip-deriv { background: #fff2d6; color: #9a6206; }
.pres-table .badge.fs-10 { font-size: .72rem; }
.q-total-row td { background: #f8f9fc; font-weight: 700; font-size: .82rem; color: var(--pres-ink); padding: .6rem .9rem; border-top: 1px solid var(--pres-line); }
.empty-row td { text-align: center; color: var(--pres-muted); padding: 1.1rem; font-size: .8rem; }

/* ─── Detalle de derivaciones (una fila por asesor) ─── */
.row-detalle-derivado > td { background: #fcfcfd; padding: 0; }
.deriv-panel {
    margin: .3rem .9rem .9rem;
    border: 1px solid #ece3d2;
    border-radius: 12px;
    background: linear-gradient(180deg, #fffdf8 0%, #ffffff 55%);
    overflow: hidden;
    box-shadow: 0 6px 18px -16px rgba(120,80,10,.55);
}
.deriv-panel-head {
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    flex-wrap: wrap; padding: .6rem .9rem; background: #fff8ea; border-bottom: 1px solid #f2e7d2;
}
.dp-title { font-size: .78rem; font-weight: 700; color: #92400e; letter-spacing: .01em; }
.dp-title i { color: #d97706; }
.dp-asesor { font-weight: 600; color: #b45309; }
.dp-meta { font-size: .72rem; color: #9a8f7d; }
.dp-meta b { color: #7a5c17; font-variant-numeric: tabular-nums; }
.deriv-detail-table { margin: 0; }
.deriv-detail-table thead th {
    font-size: .66rem; text-transform: uppercase; letter-spacing: .05em; color: #a08d6b;
    font-weight: 700; background: #fff; border-bottom: 1px solid #f0e8d8; padding: .5rem .7rem; white-space: nowrap;
}
.deriv-detail-table tbody td {
    font-size: .78rem; color: #3d4250; border-bottom: 1px dashed #f1f3f7; padding: .55rem .7rem; vertical-align: middle;
}
.deriv-detail-table tbody tr:hover > td { background: #fffcf4; }
.deriv-detail-table tfoot td { background: #fbfaf7; border-top: 1px solid #ece3d2; font-size: .76rem; color: #7a5c17; padding: .5rem .7rem; }
.deriv-detail-table .num { font-variant-numeric: tabular-nums; }
.deriv-obs { display: inline-block; max-width: 340px; font-size: .74rem; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
.deriv-fecha-hora { font-size: .68rem; color: #9aa4b2; }
.chip-warn { background: #fdecd2; color: #a16207; }
.btn-soft-amber { background: #fdf3e3; color: #b45309; border: 0; }
.btn-soft-amber:hover { background: #f9e6c8; color: #92400e; }
.row-detalle-derivado.open > td { animation: derivIn .25s ease both; }
@keyframes derivIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
@media (prefers-reduced-motion: reduce) { .row-detalle-derivado.open > td { animation: none; } }
.btn-detalle-derivado i { transition: transform .25s ease; }
.btn-detalle-derivado.active i { transform: rotate(180deg); }
.row-raiz > td { background: #fff; }
.row-raiz > td { border-bottom: 1px solid var(--pres-line); }

/* ─── Modo oscuro ─── */
[data-layout-mode="dark"] .stat-card, [data-layout-mode="dark"] .filter-card, [data-layout-mode="dark"] .period-card { background: #1a1d21; border-color: #2a2f34; }
[data-layout-mode="dark"] .period-head:hover, [data-layout-mode="dark"] .pres-table tbody tr:hover, [data-layout-mode="dark"] .q-head, [data-layout-mode="dark"] .q-total-row td { background: #20242a; }
[data-layout-mode="dark"] .stat-card .stat-label, [data-layout-mode="dark"] .form-label, [data-layout-mode="dark"] .period-sub, [data-layout-mode="dark"] .q-sum, [data-layout-mode="dark"] .pres-sub, [data-layout-mode="dark"] .pm-label { color: #9099a8; }
[data-layout-mode="dark"] .stat-card .stat-value, [data-layout-mode="dark"] .period-title h6, [data-layout-mode="dark"] .pm-value, [data-layout-mode="dark"] .pres-name, [data-layout-mode="dark"] .amount-strong, [data-layout-mode="dark"] .q-total-row td, [data-layout-mode="dark"] .q-sum b { color: #eceff4; }
[data-layout-mode="dark"] .pres-table thead th { background: #1a1d21; color: #9099a8; }
[data-layout-mode="dark"] .pres-table tbody td { color: #cdd3dc; border-bottom-color: #262b31; }
[data-layout-mode="dark"] .row-derivado > td { background: #1e2227; }
[data-layout-mode="dark"] .row-derivado:hover > td { background: #23282e; }
[data-layout-mode="dark"] .deriv-cell::before, [data-layout-mode="dark"] .deriv-cell::after { background: #333a42; }
[data-layout-mode="dark"] .deriv-suelto::before { background: #4a3d1f; }
[data-layout-mode="dark"] .pres-avatar-sm { background: #3a2f16; color: #f0c070; }
[data-layout-mode="dark"] .q-block + .q-block, [data-layout-mode="dark"] .period-body .q-block:first-child > .q-head { border-color: #2a2f34; }
[data-layout-mode="dark"] .chip { background: #232a3a; color: #a9baf0; }
[data-layout-mode="dark"] .chip-deriv { background: #3a2f16; color: #f0c070; }
[data-layout-mode="dark"] .row-detalle-derivado > td { background: #17191d; }
[data-layout-mode="dark"] .deriv-panel { background: linear-gradient(180deg, #1c1a15 0%, #191b1f 55%); border-color: #33302a; box-shadow: none; }
[data-layout-mode="dark"] .deriv-panel-head { background: #211e18; border-bottom-color: #33302a; }
[data-layout-mode="dark"] .dp-title { color: #f0c070; }
[data-layout-mode="dark"] .dp-title i { color: #f0c070; }
[data-layout-mode="dark"] .dp-asesor { color: #e8b75f; }
[data-layout-mode="dark"] .dp-meta { color: #9a927f; }
[data-layout-mode="dark"] .dp-meta b { color: #e0c68c; }
[data-layout-mode="dark"] .deriv-detail-table thead th { background: #191b1f; color: #9a927f; border-bottom-color: #2c2a26; }
[data-layout-mode="dark"] .deriv-detail-table tbody td { color: #cdd3dc; border-bottom-color: #23262b; }
[data-layout-mode="dark"] .deriv-detail-table tbody tr:hover > td { background: #20242a; }
[data-layout-mode="dark"] .deriv-detail-table tfoot td { background: #1c1a15; border-top-color: #33302a; color: #e0c68c; }
[data-layout-mode="dark"] .deriv-obs { color: #9aa4b2; }
[data-layout-mode="dark"] .chip-warn { background: #3a2f16; color: #f0c070; }
[data-layout-mode="dark"] .btn-soft-amber { background: #3a2f16; color: #f0c070; }
[data-layout-mode="dark"] .btn-soft-amber:hover { background: #463718; color: #ffd28a; }
[data-layout-mode="dark"] .row-raiz > td { background: #1a1d21; border-bottom-color: #262b31; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="pub-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-wallet-3-line"></i></div>
                <div>
                    <h4>Presupuestos de Publicidad</h4>
                    <p class="hero-sub">Asignaciones por asesor, agrupadas por periodo</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.marketing.publicidad.index') }}">Publicidad</a></li>
                <li class="breadcrumb-item active">Presupuestos</li>
            </ol>
        </div>
    </div>
</div>

{{-- Resumen global --}}
@php
    $resTotalAsignado = 0;
    $resTotalGastado = 0;
    $resTotalAprobado = 0;
    $resTotalRecibos = 0;
    $resTotalPeriodos = $grupos->count();
    foreach ($grupos as $g) {
        $resTotalAsignado += ($g->q1->total_asignado ?? 0) + ($g->q2->total_asignado ?? 0);
        $resTotalGastado += ($g->q1->total_gastado ?? 0) + ($g->q2->total_gastado ?? 0);
        $resTotalAprobado += ($g->q1->total_aprobado ?? 0) + ($g->q2->total_aprobado ?? 0);
        $resTotalRecibos += ($g->q1->total_comprobantes ?? 0) + ($g->q2->total_comprobantes ?? 0);
    }
@endphp
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalAsignado, 2) }}</h4>
                        <p class="stat-label">Total presupuestado</p>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-wallet-3-line"></i></div>
                </div>
                <div class="fs-11 text-muted mt-1">{{ $resTotalPeriodos }} periodo(s)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalGastado, 2) }}</h4>
                        <p class="stat-label">Gastado (reportado)</p>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
                <div class="fs-11 text-muted mt-1">{{ $resTotalRecibos }} comprobante(s)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalAprobado, 2) }}</h4>
                        <p class="stat-label">Aprobado por contable</p>
                    </div>
                    <div class="stat-icon bg-soft-info text-info"><i class="ri-checkbox-circle-line"></i></div>
                </div>
                <div class="fs-11 text-muted mt-1">&nbsp;</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="stat-value mb-0">{{ $totalEliminables }}</h4>
                        <p class="stat-label">Sin gastos (eliminables)</p>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-delete-bin-6-line"></i></div>
                </div>
                <div class="fs-11 text-muted mt-1">Presupuestos sin comprobantes</div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header d-flex align-items-center gap-2 flex-wrap">
                <i class="ri-equalizer-line text-primary"></i>
                <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros</h5>
                <a href="{{ route('admin.marketing.publicidad.index') }}" class="btn btn-sm btn-soft-info ms-auto">
                    <i class="ri-arrow-left-line me-1"></i>Volver a comprobantes
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketing.publicidad.presupuestos') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Gestión</label>
                        <select name="gestion" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" @selected((string)$gestionFiltro === (string)$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
                        @if($totalEliminables > 0)
                        <span class="badge bg-soft-warning text-warning fs-11 px-2 py-2">
                            <i class="ri-information-line me-1"></i>{{ $totalEliminables }} presupuesto(s) sin gastos
                        </span>
                        @endif
                        <a href="{{ route('admin.marketing.publicidad.presupuestos') }}" class="btn btn-soft-secondary btn-sm">
                            <i class="ri-refresh-line me-1"></i>Limpiar filtro
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Listado por periodo --}}
@if($grupos->isEmpty())
<div class="card filter-card">
    <div class="card-body text-center py-5">
        <i class="ri-inbox-2-line ri-3x text-muted d-block mb-3"></i>
        <h5 class="fw-bold text-dark mb-1">Sin presupuestos registrados</h5>
        <p class="text-muted mb-0">Aún no se han asignado presupuestos de publicidad.</p>
    </div>
</div>
@else
<div class="period-list">
    @foreach($grupos as $key => $g)
    @php
        $totalAsignado = ($g->q1->total_asignado ?? 0) + ($g->q2->total_asignado ?? 0);
        $totalGastado = ($g->q1->total_gastado ?? 0) + ($g->q2->total_gastado ?? 0);
        $totalAprobado = ($g->q1->total_aprobado ?? 0) + ($g->q2->total_aprobado ?? 0);
        $totalComprobantes = ($g->q1->total_comprobantes ?? 0) + ($g->q2->total_comprobantes ?? 0);
        $totalPresupuestos = ($g->q1->total_presupuestos ?? 0) + ($g->q2->total_presupuestos ?? 0);
        $gastoPct = $totalAsignado > 0 ? min(100, round(($totalGastado / $totalAsignado) * 100, 1)) : 0;
        $cerradoPeriodo = ($g->q1->periodo_cerrado ?? false) || ($g->q2->periodo_cerrado ?? false);
    @endphp
    <div class="period-card">
        <div class="period-head" data-bs-toggle="collapse" data-bs-target="#periodo-{{ $key }}" role="button" tabindex="0" aria-expanded="true" aria-controls="periodo-{{ $key }}">
            <span class="period-icon"><i class="ri-calendar-event-line"></i></span>
            <div class="period-title">
                <h6>{{ $meses[$g->mes] }} <span class="text-muted fw-normal">{{ $g->gestion }}</span></h6>
                <div class="period-sub">{{ $totalPresupuestos }} presupuesto(s) · {{ $totalComprobantes }} comprobante(s)</div>
            </div>
            <div class="period-metrics">
                <div class="pm"><span class="pm-label">Asignado</span><span class="pm-value text-primary">Bs {{ number_format($totalAsignado, 2) }}</span></div>
                <div class="pm"><span class="pm-label">Gastado</span><span class="pm-value">Bs {{ number_format($totalGastado, 2) }}</span></div>
                <div class="pm"><span class="pm-label">Aprobado</span><span class="pm-value text-success">Bs {{ number_format($totalAprobado, 2) }}</span></div>
                <div class="pm">
                    <span class="pm-label">Avance</span>
                    <div class="bar {{ $gastoPct > 100 ? 'warn' : '' }}"><span style="--v:{{ $gastoPct / 100 }};"></span></div>
                    <span class="pm-pct">{{ $gastoPct }}%</span>
                </div>
            </div>
            <span class="period-status {{ $cerradoPeriodo ? 'closed' : 'open' }}">
                <i class="ri-{{ $cerradoPeriodo ? 'lock-line' : 'lock-unlock-line' }} me-1"></i>{{ $cerradoPeriodo ? 'Cerrado' : 'Abierto' }}
            </span>
            <i class="ri-arrow-down-s-line period-chevron"></i>
        </div>

        <div id="periodo-{{ $key }}" class="collapse show">
            <div class="period-body">
                @foreach([['k' => 'q1', 'cls' => 'q1', 'label' => 'Q1', 'rango' => '1 – 15'], ['k' => 'q2', 'cls' => 'q2', 'label' => 'Q2', 'rango' => '16 – fin']] as $meta)
                @php $q = $g->{$meta['k']}; @endphp
                <div class="q-block">
                    <div class="q-head">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge badge-{{ $meta['cls'] }}">{{ $meta['label'] }} · {{ $meta['rango'] }}</span>
                            @if($q)
                            <span class="q-sum">Asignado <b>Bs {{ number_format($q->total_asignado, 2) }}</b> · Gastado <b>Bs {{ number_format($q->total_gastado, 2) }}</b> · Aprobado <b>Bs {{ number_format($q->total_aprobado, 2) }}</b></span>
                            @php
                                $qPrincipales = ($q->presupuestos ?? collect())->filter(fn($x) => !isset($x->grupo_id))->count();
                                $qAsesores = ($q->presupuestos ?? collect())->sum(function ($x) {
                                    if (isset($x->grupo_id)) return 1;
                                    return ($x->derivados ?? collect())->count();
                                });
                                $qDerivaciones = ($q->presupuestos ?? collect())->sum(function ($x) {
                                    if (isset($x->grupo_id)) return (int) $x->cantidad;
                                    return (int) ($x->num_derivados ?? ($x->derivados ?? collect())->sum('cantidad'));
                                });
                            @endphp
                            <span class="chip"><i class="ri-list-check-2"></i>{{ $qPrincipales }} principal(es) · {{ $qAsesores }} asesor(es) · {{ $qDerivaciones }} derivación(es)</span>
                            @if($q->periodo_cerrado)
                            <span class="period-status closed"><i class="ri-lock-line me-1"></i>Cerrado</span>
                            @else
                            <span class="period-status open"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                            @endif
                            @endif
                        </div>
                        @if($q)
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('admin.marketing.publicidad.index', ['gestion' => $q->gestion, 'mes' => $q->mes, 'quincena' => $q->quincena]) }}" class="btn btn-sm btn-soft-primary" title="Ver detalle de comprobantes">
                                <i class="ri-eye-line me-1"></i>Ver detalle
                            </a>
                            @can('marketing.publicidad.presupuestos')
                            <button type="button" class="btn btn-sm {{ $q->periodo_cerrado ? 'btn-soft-secondary' : 'btn-soft-warning' }} btn-toggle-periodo"
                                    data-gestion="{{ $q->gestion }}"
                                    data-mes="{{ $q->mes }}"
                                    data-quincena="{{ $q->quincena }}"
                                    data-cerrado="{{ $q->periodo_cerrado ? '1' : '0' }}"
                                    title="{{ $q->periodo_cerrado ? 'Abrir periodo' : 'Cerrar periodo' }}">
                                <i class="ri-{{ $q->periodo_cerrado ? 'lock-unlock' : 'lock' }}-line"></i>
                            </button>
                            @endcan
                            @if($q->periodo_cerrado)
                            <a href="{{ route('admin.marketing.publicidad.presupuestos.reporte', ['gestion' => $q->gestion, 'mes' => $q->mes, 'quincena' => $q->quincena]) }}" class="btn btn-sm btn-soft-success" title="Generar reporte PDF">
                                <i class="ri-file-pdf-line"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>

                    @if($q)
                    <div class="table-responsive">
                        <table class="table pres-table align-middle">
                            <thead>
                                <tr>
                                    <th style="min-width:230px;">Asesor</th>
                                    <th class="text-end" style="width:130px;">Asignado</th>
                                    <th class="text-end" style="width:110px;">Gastado</th>
                                    <th class="text-end" style="width:120px;">Aprobado</th>
                                    <th class="text-center" style="width:80px;">Recibos</th>
                                    <th class="text-center" style="width:100px;">Estado</th>
                                    <th class="text-center" style="width:110px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($q->presupuestos ?? collect() as $p)
                                @if(isset($p->grupo_id))
                                    {{-- Derivación sin raíz visible en este periodo: fila agrupada por asesor --}}
                                    @include('admin.marketing.publicidad._presupuesto_derivado_fila', ['d' => $p, 'raiz' => null, 'meses' => $meses])
                                @else
                                    @include('admin.marketing.publicidad._presupuesto_fila', ['p' => $p, 'nivel' => 'raiz'])
                                    @foreach($p->derivados ?? collect() as $d)
                                    @include('admin.marketing.publicidad._presupuesto_derivado_fila', ['d' => $d, 'raiz' => $p, 'meses' => $meses, 'ultimoDerivado' => $loop->last])
                                    @endforeach
                                @endif
                                @empty
                                <tr class="empty-row">
                                    <td colspan="7"><i class="ri-inbox-2-line me-1"></i>Sin presupuestos asignados en esta quincena</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="q-total-row">
                                    <td>Total {{ $meta['label'] }} · {{ $meses[$g->mes] }} {{ $g->gestion }}</td>
                                    <td class="text-end num text-primary">Bs {{ number_format($q->total_asignado, 2) }}</td>
                                    <td class="text-end num">Bs {{ number_format($q->total_gastado, 2) }}</td>
                                    <td class="text-end num text-success">Bs {{ number_format($q->total_aprobado, 2) }}</td>
                                    <td class="text-center num">{{ $q->total_comprobantes }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="q-empty"><i class="ri-inbox-2-line me-1"></i>Sin presupuesto asignado en esta quincena</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@can('marketing.publicidad.presupuestos')
{{-- Modal eliminar presupuesto sin gastos --}}
<div class="modal fade" id="modalEliminarPresupuesto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formEliminarPresupuesto">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-delete-bin-6-line text-danger me-1"></i>Eliminar presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">¿Eliminar este presupuesto <strong>sin gastos registrados</strong>?</p>
                    <div class="p-3 rounded fs-12" id="eliminarPresDesc" style="background:#f8f9fc;border:1px solid #eceff4;"></div>
                    <p class="text-muted fs-12 mb-0 mt-3">
                        <i class="ri-information-line me-1"></i>Solo se permite eliminar presupuestos sin comprobantes, sin derivaciones y con el periodo abierto. Si el presupuesto era una derivación, el monto se devolverá al presupuesto origen.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-6-line me-1"></i>Eliminar presupuesto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modal de previsualización de archivo de derivación --}}
<div class="modal fade" id="modalFilePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-attachment-2 text-info me-1"></i>Archivo de derivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-light" id="filePreviewContent">
                <div class="text-center p-4"><i class="ri-loader-4-line ri-2x"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>
            </div>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
// Accesibilidad: abrir/cerrar el acordeón de periodo con teclado
document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    var head = e.target.closest('.period-head');
    if (!head) return;
    e.preventDefault();
    head.click();
});

document.addEventListener('click', function (e) {
    // Previsualizar archivo de derivación
    var preview = e.target.closest('.btn-preview-file');
    if (preview) {
        var url = preview.dataset.url;
        var ext = (preview.dataset.ext || url.split('.').pop() || '').toLowerCase();
        var content = document.getElementById('filePreviewContent');
        if (content) {
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                content.innerHTML = '<div class="text-center"><img src="' + url + '" class="img-fluid rounded" style="max-height:80vh;object-fit:contain;" alt="Archivo de derivación"></div>';
            } else if (ext === 'pdf') {
                content.innerHTML = '<iframe src="' + url + '" class="w-100 rounded" style="height:80vh;border:none;"></iframe>';
            } else {
                content.innerHTML = '<div class="text-center p-4"><i class="ri-file-download-line ri-2x"></i><p class="mt-2"><a href="' + url + '" target="_blank" class="btn btn-primary"><i class="ri-download-line me-1"></i>Descargar archivo</a></p></div>';
            }
        }
        var modal = document.getElementById('modalFilePreview');
        if (modal) new bootstrap.Modal(modal).show();
        return;
    }

    // Abrir / cerrar el detalle de asignaciones de un asesor derivado
    var det = e.target.closest('.btn-detalle-derivado');
    if (det) {
        var fila = document.querySelector('[data-der-detalle="' + det.dataset.der + '"]');
        if (!fila) return;
        var abierto = fila.style.display !== 'none';
        fila.style.display = abierto ? 'none' : '';
        fila.classList.toggle('open', !abierto);
        det.classList.toggle('active', !abierto);
        det.setAttribute('aria-expanded', abierto ? 'false' : 'true');
        return;
    }

    // Eliminar presupuesto sin gastos
    var del = e.target.closest('.btn-eliminar-pres');
    if (del) {
        var form = document.getElementById('formEliminarPresupuesto');
        if (!form) return;
        form.action = '{{ route("admin.marketing.publicidad.presupuesto.destroy", ":id") }}'.replace(':id', del.dataset.id);
        document.getElementById('eliminarPresDesc').innerHTML =
            '<div><i class="ri-user-star-line me-1 text-warning"></i>' + del.dataset.asesor + '</div>' +
            '<div class="mt-1"><i class="ri-calendar-event-line me-1 text-muted"></i>' + del.dataset.periodo + '</div>' +
            '<div class="mt-1"><i class="ri-wallet-3-line me-1 text-primary"></i>Asignado: <strong>Bs ' + del.dataset.monto + '</strong></div>';
        new bootstrap.Modal(document.getElementById('modalEliminarPresupuesto')).show();
        return;
    }

    // Abrir / cerrar periodo
    var btn = e.target.closest('.btn-toggle-periodo');
    if (!btn) return;
    var gestion = btn.dataset.gestion;
    var mes = btn.dataset.mes;
    var quincena = btn.dataset.quincena;
    var cerrado = btn.dataset.cerrado === '1';
    var nuevaAccion = cerrado ? 'abrir' : 'cerrar';
    if (!confirm('¿Estás seguro de ' + nuevaAccion + ' el periodo ' + gestion + '/' + mes + ' Q' + quincena + '?')) return;
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('gestion', gestion);
    formData.append('mes', mes);
    formData.append('quincena', quincena);
    formData.append('cerrado', cerrado ? '0' : '1');
    fetch('{{ route("admin.marketing.publicidad.presupuestos.toggle") }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al cambiar el estado del periodo.');
        }
    })
    .catch(() => alert('Error de conexión.'));
});

document.getElementById('modalFilePreview')?.addEventListener('hidden.bs.modal', function () {
    var el = document.getElementById('filePreviewContent');
    if (el) el.innerHTML = '<div class="text-center p-4"><i class="ri-loader-4-line ri-2x"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>';
});
</script>
@endpush
