@extends('admin.layouts.master')

@section('title', 'Seguimiento Contable - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.cont-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.cont-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.cont-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.cont-hero > * { position: relative; z-index: 1; }
.cont-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px);
}
.cont-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.cont-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.cont-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.cont-hero .breadcrumb-item, .cont-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.cont-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }

/* Filtro */
.filter-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-body { padding: 1rem 1.25rem; }
.filter-card label { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #6c757d; margin: 0; }
.filter-card select {
    border: 1px solid #e9ebec; border-radius: 8px;
    padding: .4rem .75rem; font-size: .85rem; font-weight: 600; color: #405189;
}
.filter-card select:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .15rem rgba(10,179,156,.12); }

/* Summary cards (reusadas) */
.cont-summary-card {
    background: #fff; border: 1px solid #eef0f4; border-radius: 14px;
    padding: 1.1rem 1.25rem; height: 100%;
    transition: box-shadow .2s ease;
    box-shadow: 0 4px 14px -8px rgba(64,81,137,.15);
}
.cont-summary-card:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .cont-summary-card { background: #212529; border-color: #2a2f34; }
.cont-card-mat { border-left: 4px solid #299cdb; }
.cont-card-col { border-left: 4px solid #0ab39c; }
.cont-card-total {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    border: 0; color: #fff;
}
.cont-card-header { display: flex; align-items: center; gap: .65rem; margin-bottom: .85rem; padding-bottom: .65rem; border-bottom: 1px dashed #eef0f4; }
.cont-card-total .cont-card-header { border-bottom-color: rgba(255,255,255,.18); }
.cont-card-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.15rem; flex-shrink: 0;
}
.cont-card-title { font-weight: 700; color: #212529; font-size: .95rem; line-height: 1.1; }
[data-layout-mode="dark"] .cont-card-title { color: #fff; }
.cont-card-sub { font-size: .7rem; color: #878a99; margin-top: 2px; }
.cont-card-rows { display: flex; flex-direction: column; gap: .3rem; }
.cont-row {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .85rem; color: #495057; padding: .15rem 0;
}
.cont-row > span { display: inline-flex; align-items: center; gap: .35rem; font-weight: 600; }
.cont-row strong { font-size: 1rem; font-weight: 800; }
.cont-row.saldo-row { border-top: 1px dashed #eef0f4; padding-top: .35rem; margin-top: .2rem; }
.cont-progress { height: 8px; background: rgba(255,255,255,.18); border-radius: 999px; overflow: hidden; }
.cont-progress-fill { height: 100%; background: linear-gradient(90deg, #0ab39c, #fff); border-radius: 999px; transition: width .5s ease; }

/* Tabs por tipo */
.tipo-tabs {
    border: 0; gap: .35rem;
    background: #f3f5f9; padding: .4rem;
    border-radius: 12px; display: inline-flex; flex-wrap: wrap;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .tipo-tabs { background: #1a1d20; }
.tipo-tabs .tipo-tab {
    border: 0; background: transparent; border-radius: 9px;
    color: #495057; font-weight: 600; font-size: .82rem;
    padding: .55rem 1rem; cursor: pointer; transition: all .2s ease;
    display: inline-flex; align-items: center; gap: .4rem;
}
.tipo-tabs .tipo-tab:hover { color: #0ab39c; background: rgba(10,179,156,.06); }
.tipo-tabs .tipo-tab.active {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    color: #fff;
    box-shadow: 0 6px 14px -6px rgba(10,179,156,.55);
}
.tipo-tabs .tipo-tab .badge {
    background: rgba(10,179,156,.15); color: #0ab39c;
    font-weight: 700; font-size: .65rem; padding: .2em .55em; border-radius: 999px;
}
.tipo-tabs .tipo-tab.active .badge { background: rgba(255,255,255,.25); color: #fff; }
[data-layout-mode="dark"] .tipo-tabs .tipo-tab { color: #ced4da; }

/* Tabla por programa */
.tabla-prog-cont {
    border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden;
    background: #fff;
}
[data-layout-mode="dark"] .tabla-prog-cont { background: #212529; border-color: #2a2f34; }
.tabla-prog-cont table { margin: 0; width: 100%; }
.tabla-prog-cont thead th {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%) !important;
    color: #495057; font-weight: 700; font-size: .68rem;
    text-transform: uppercase; letter-spacing: .4px;
    padding: .75rem .75rem; border-bottom: 2px solid #eef0f4;
}
[data-layout-mode="dark"] .tabla-prog-cont thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }
.tabla-prog-cont tbody td {
    padding: .65rem .7rem; vertical-align: middle;
    font-size: .82rem; border-bottom: 1px solid #f1f3f5;
}
[data-layout-mode="dark"] .tabla-prog-cont tbody td { border-bottom-color: #2a2f34; }
.tabla-prog-cont tbody tr { transition: background .15s ease; }
.tabla-prog-cont tbody tr:hover { background: #f8f9fc; }
[data-layout-mode="dark"] .tabla-prog-cont tbody tr:hover { background: #1a1d20; }
.tabla-prog-cont tbody tr.tr-no-ejecutado { background: rgba(240, 101, 72, .06); }
.tabla-prog-cont tbody tr.tr-no-ejecutado:hover { background: rgba(240, 101, 72, .12); }
.tabla-prog-cont tbody tr.tr-no-ejecutado td { border-bottom-color: rgba(240, 101, 72, .12); }

.compact-td { padding-top: .45rem !important; padding-bottom: .45rem !important; }

.prog-icon-sm {
    width: 30px; height: 30px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .82rem; flex-shrink: 0;
}
.prog-name { font-weight: 600; color: #212529; font-size: .84rem; line-height: 1.2; }
[data-layout-mode="dark"] .prog-name { color: #fff; }
.prog-meta { font-size: .66rem; color: #878a99; margin-top: 2px; }

.metric-block { display: flex; flex-direction: column; gap: 1px; align-items: flex-end; line-height: 1.15; }
.metric-block .deuda { font-weight: 700; color: #212529; font-size: .82rem; }
[data-layout-mode="dark"] .metric-block .deuda { color: #fff; }
.metric-block .pagado { font-size: .67rem; color: #0ab39c; font-weight: 700; }
.metric-block .saldo { font-size: .67rem; font-weight: 700; }
.metric-block .saldo.danger { color: #f06548; }
.metric-block .saldo.ok { color: #0ab39c; }
.metric-reales {
    display: block; margin-top: 2px; padding-top: 3px;
    border-top: 1px dashed #e2e5ea; width: 100%;
    font-size: .66rem; line-height: 1.2; color: #0ab39c; font-weight: 700;
}

.prog-progress-cell { display: flex; align-items: center; gap: .5rem; }
.prog-progress-bar { flex: 1; height: 7px; background: #eef0f4; border-radius: 999px; overflow: hidden; min-width: 80px; }
.prog-progress-fill { height: 100%; background: linear-gradient(90deg, #0ab39c, #299cdb); border-radius: 999px; transition: width .5s ease; }
.prog-progress-pct { font-size: .72rem; font-weight: 800; color: #495057; min-width: 32px; text-align: right; }
.prog-bar-reales { height: 4px; border-radius: 999px; overflow: hidden; background: #eef0f4; }
.prog-bar-reales .fill { height: 100%; background: linear-gradient(90deg, #6cc8b0, #0ab39c); border-radius: 999px; }

.saldo-pill-big {
    display: inline-flex; align-items: center; gap: 4px;
    font-weight: 800; padding: .3em .75em; border-radius: 999px;
    font-size: .8rem; border: 1px solid;
}
.saldo-pill-big.deuda { background: #fee2e2; color: #f06548; border-color: #fecaca; }
.saldo-pill-big.ok { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }

.metric-reales-val { display: inline-flex; align-items: center; gap: 3px; font-size: .66rem; color: #0ab39c; font-weight: 700; }
.metric-reales-val .dot { width: 5px; height: 5px; border-radius: 50%; background: #0ab39c; display: inline-block; }

.empty-state {
    text-align: center; padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px; border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 80px; height: 80px; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff; border-radius: 50%; font-size: 2.5rem; color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

.tipo-header {
    display: flex; align-items: center; gap: .65rem; margin-bottom: .75rem;
}
.tipo-header-icon {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    color: #fff; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.05rem;
}
.tipo-header-title { font-weight: 700; color: #212529; font-size: .95rem; margin: 0; }
[data-layout-mode="dark"] .tipo-header-title { color: #fff; }
.tipo-header-sub { font-size: .72rem; color: #878a99; }

/* ===== Botón fechas ===== */
.btn-fechas {
    border: 1px solid #e9ebec;
    background: #fff;
    color: #495057;
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .72rem;
    font-weight: 700;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .3rem;
}
.btn-fechas:hover { border-color: #0ab39c; color: #0ab39c; background: #f0fdfa; }
.btn-fechas .bdg {
    background: #eef0f4; color: #495057;
    border-radius: 999px;
    font-size: .62rem; font-weight: 700;
    padding: .1em .5em;
}
.btn-fechas.has-data { border-color: #0ab39c; color: #0ab39c; background: #ecfdf5; }
.btn-fechas.has-data .bdg { background: #0ab39c; color: #fff; }
[data-layout-mode="dark"] .btn-fechas { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

/* ===== Modal fechas ===== */
#modalFechas .modal-dialog { max-width: 760px; }
#modalFechas .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(0,0,0,.25); }
#modalFechas .modal-header {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%);
    color: #fff; border: 0;
    padding: 1.1rem 1.5rem;
    position: relative; overflow: hidden;
}
#modalFechas .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalFechas .modal-header > * { position: relative; z-index: 1; }
#modalFechas .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
}
#modalFechas .modal-title { color: #fff; font-weight: 700; }
#modalFechas .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalFechas .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalFechas .modal-body { padding: 1.5rem; background: #fafbfd; }
[data-layout-mode="dark"] #modalFechas .modal-body { background: #1a1d20; }
#modalFechas .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalFechas .modal-footer { background: #212529; border-top-color: #2a2f34; }

.plan-switch {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .65rem;
    margin-bottom: 1rem;
}
.plan-card {
    border: 2px solid #eef0f4;
    background: #fff;
    border-radius: 12px;
    padding: .85rem 1rem;
    cursor: pointer;
    text-align: left;
    transition: all .2s ease;
    display: flex; align-items: center; gap: .75rem;
}
.plan-card:hover { border-color: #c3cfe2; }
.plan-card .pc-icon {
    width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px; font-size: 1.1rem; flex-shrink: 0;
    background: #eef1f8; color: #405189;
}
.plan-card.is-active { border-color: transparent; box-shadow: 0 8px 18px -8px rgba(0,0,0,.25); color: #fff; }
.plan-card.is-active .pc-icon { background: rgba(255,255,255,.2); color: #fff; }
.plan-card.is-active.contado { background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%); }
.plan-card.is-active.regular { background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%); }
.pc-nombre { font-weight: 700; font-size: .9rem; line-height: 1.1; }
.pc-meta { font-size: .72rem; opacity: .85; }
[data-layout-mode="dark"] .plan-card { background: #1d2125; border-color: #2a2f34; }

.fechas-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 575.98px) { .fechas-grid { grid-template-columns: 1fr; } }
.fechas-col {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: .85rem;
}
[data-layout-mode="dark"] .fechas-col { background: #1d2125; border-color: #2a2f34; }
.fechas-col-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .6rem;
    padding-bottom: .5rem;
    border-bottom: 1px dashed #eef0f4;
}
[data-layout-mode="dark"] .fechas-col-header { border-bottom-color: #2a2f34; }
.fechas-col-title { font-weight: 700; font-size: .78rem; color: #495057; text-transform: uppercase; letter-spacing: .4px; display: inline-flex; align-items: center; gap: .4rem; }
[data-layout-mode="dark"] .fechas-col-title { color: #ced4da; }
.btn-add-fecha {
    border: 1px dashed #c3cfe2;
    background: transparent;
    color: #405189;
    border-radius: 8px;
    padding: .25rem .55rem;
    font-size: .7rem;
    font-weight: 700;
    transition: all .15s ease;
}
.btn-add-fecha:hover { background: #eef1f8; }
.fecha-row {
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: .5rem;
}
.fecha-row .num {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%);
    color: #405189;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .72rem;
    flex-shrink: 0;
}
.fecha-row input[type=date] {
    flex: 1;
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: .35rem .6rem;
    font-size: .82rem;
    transition: all .15s ease;
}
.fecha-row input[type=date]:focus { border-color: #0ab39c; box-shadow: 0 0 0 .15rem rgba(10,179,156,.12); outline: 0; }
.btn-rm-fecha {
    width: 28px; height: 28px;
    border: 1px solid #fecaca;
    background: #fff;
    color: #f06548;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .85rem;
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
}
.btn-rm-fecha:hover { background: #fef2f2; }
[data-layout-mode="dark"] .fecha-row input[type=date] { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

/* Generador de fechas */
.fechas-gen {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border: 1px dashed #c3cfe2;
    border-radius: 10px;
    padding: .65rem .75rem;
    margin-bottom: .75rem;
}
[data-layout-mode="dark"] .fechas-gen { background: #1a1d20; border-color: #2a2f34; }
.fechas-gen-title {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #6c757d;
    font-weight: 700;
    margin-bottom: .35rem;
    display: flex; align-items: center; gap: .3rem;
}
.fechas-gen-row { display: flex; gap: .4rem; flex-wrap: wrap; align-items: center; }
.fechas-gen-row .grp { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 80px; }
.fechas-gen-row label { font-size: .62rem; color: #878a99; text-transform: uppercase; letter-spacing: .3px; font-weight: 700; margin: 0; }
.fechas-gen-row input {
    border: 1px solid #e9ebec;
    border-radius: 7px;
    padding: .3rem .55rem;
    font-size: .8rem;
    font-weight: 600;
    color: #495057;
    background: #fff;
}
.fechas-gen-row input:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .12rem rgba(10,179,156,.15); }
[data-layout-mode="dark"] .fechas-gen-row input { background: #1d2125; border-color: #2a2f34; color: #ced4da; }
.btn-apply-gen {
    border: 0;
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    color: #fff;
    border-radius: 7px;
    padding: .3rem .65rem;
    font-size: .7rem;
    font-weight: 700;
    transition: all .15s ease;
    align-self: flex-end;
    height: 28px;
    display: inline-flex; align-items: center; gap: .25rem;
}
.btn-apply-gen:hover { transform: translateY(-1px); box-shadow: 0 6px 14px -6px rgba(10,179,156,.45); color: #fff; }
</style>
@endpush

@push('css-after')
<link href="{{ asset('backend/assets/css/avip-contable.css') }}?v={{ @filemtime(public_path('backend/assets/css/avip-contable.css')) ?: time() }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="cont-page">
<div class="row">
    <div class="col-12">
        <div class="cont-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-line-chart-line"></i></div>
                <div>
                    <h4>Seguimiento Contable</h4>
                    <p class="hero-sub">Resumen general de deudas, pagos y saldos por programa</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Contable</a></li>
                <li class="breadcrumb-item active">Seguimiento</li>
            </ol>
        </div>
    </div>
</div>

{{-- Filtro --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.contable.seguimiento') }}" class="d-flex align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label><i class="ri-calendar-2-line"></i> Gestión:</label>
                        <select name="gestion" onchange="this.form.submit()">
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" @if((int)$gestionFiltro === (int)$g) selected @endif>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted ms-auto" style="font-size:.72rem;"><i class="ri-information-line"></i> Datos consolidados de todos los programas de la gestión seleccionada</small>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 3 Cards de resumen --}}
@php $r = $mainStats['resumen']; @endphp
<div class="row g-3 mb-3">
    <div class="col-lg-4 col-md-6">
        <div class="cont-summary-card cont-card-mat">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:#dbeafe; color:#299cdb;"><i class="ri-bank-card-line"></i></span>
                <div>
                    <div class="cont-card-title">Matrícula</div>
                    <div class="cont-card-sub">Bruto Bs {{ number_format($r['totMatBruto'], 2) }}@if($r['totMatDesc'] > 0) · Dto Bs {{ number_format($r['totMatDesc'], 2) }}@endif</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda neta</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-primary d-block">Bs {{ number_format($r['totMatNeto'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totMatNeto_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-success d-block">Bs {{ number_format($r['totMatPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totMatPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="padding-top:.35rem;">
                    <span><i class="ri-{{ $r['totMatSaldo'] > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="{{ $r['totMatSaldo'] > 0 ? 'text-danger' : 'text-success' }} d-block">Bs {{ number_format($r['totMatSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totMatSaldo_r'], 2) }}</small>
                    </div>
                </div>
                @if($r['totMatNeto_ne'] > 0)
                <div class="cont-row" style="padding-top:.2rem;font-size:.68rem;color:#f06548;font-weight:600;border-top:1px dashed #fecaca;">
                    <span><i class="ri-close-circle-line"></i> No Ejecutado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <span>Bs {{ number_format($r['totMatNeto_ne'], 2) }}</span>
                        <small class="d-block fw-bold" style="font-size:.6rem;color:#f06548;white-space:nowrap;">Pag Bs {{ number_format($r['totMatPagado_ne'], 2) }} · Saldo Bs {{ number_format($r['totMatSaldo_ne'], 2) }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="cont-summary-card cont-card-col">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:#d1fae5; color:#0ab39c;"><i class="ri-funds-line"></i></span>
                <div>
                    <div class="cont-card-title">Colegiatura</div>
                    <div class="cont-card-sub">Bruto Bs {{ number_format($r['totColBruto'], 2) }}@if($r['totColDesc'] > 0) · Dto Bs {{ number_format($r['totColDesc'], 2) }}@endif</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda neta</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-info d-block">Bs {{ number_format($r['totColNeto'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totColNeto_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-success d-block">Bs {{ number_format($r['totColPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totColPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="padding-top:.35rem;">
                    <span><i class="ri-{{ $r['totColSaldo'] > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="{{ $r['totColSaldo'] > 0 ? 'text-danger' : 'text-success' }} d-block">Bs {{ number_format($r['totColSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: Bs {{ number_format($r['totColSaldo_r'], 2) }}</small>
                    </div>
                </div>
                @if($r['totColNeto_ne'] > 0)
                <div class="cont-row" style="padding-top:.2rem;font-size:.68rem;color:#f06548;font-weight:600;border-top:1px dashed #fecaca;">
                    <span><i class="ri-close-circle-line"></i> No Ejecutado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <span>Bs {{ number_format($r['totColNeto_ne'], 2) }}</span>
                        <small class="d-block fw-bold" style="font-size:.6rem;color:#f06548;white-space:nowrap;">Pag Bs {{ number_format($r['totColPagado_ne'], 2) }} · Saldo Bs {{ number_format($r['totColSaldo_ne'], 2) }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="cont-summary-card cont-card-total">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:rgba(255,255,255,.25); color:#fff;"><i class="ri-coins-line"></i></span>
                <div>
                    <div class="cont-card-title" style="color:#fff;">Total General</div>
                    <div class="cont-card-sub" style="color:rgba(255,255,255,.85);">{{ $r['pctCobrado'] }}% cobrado · {{ $r['totInscritosReales'] }} reales</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="color:rgba(255,255,255,.9);padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda total</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;">Bs {{ number_format($r['totGenDeuda'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#6cc8b0;white-space:nowrap;">Reales: Bs {{ number_format($r['totGenDeuda_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="color:rgba(255,255,255,.9);padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;">Bs {{ number_format($r['totGenPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#6cc8b0;white-space:nowrap;">Reales: Bs {{ number_format($r['totGenPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="color:rgba(255,255,255,.9);border-top-color:rgba(255,255,255,.2);padding-top:.35rem;">
                    <span><i class="ri-time-line"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;background:rgba(255,255,255,.2);padding:.1em .5em;border-radius:5px;">Bs {{ number_format($r['totGenSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#6cc8b0;white-space:nowrap;">Reales: Bs {{ number_format($r['totGenSaldo_r'], 2) }}</small>
                    </div>
                </div>
                @if($r['totGenDeuda_ne'] > 0)
                <div class="cont-row" style="padding-top:.25rem;font-size:.68rem;color:#fca5a5;font-weight:600;border-top:1px dashed rgba(255,255,255,.15);">
                    <span><i class="ri-close-circle-line"></i> No Ejecutado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <span style="color:#fff;">Bs {{ number_format($r['totGenDeuda_ne'], 2) }}</span>
                        <small class="d-block fw-bold" style="font-size:.6rem;color:#fca5a5;white-space:nowrap;">Pag Bs {{ number_format($r['totGenPagado_ne'], 2) }} · Saldo Bs {{ number_format($r['totGenSaldo_ne'], 2) }}</small>
                    </div>
                </div>
                @endif
            </div>
            <div class="cont-progress mt-2" style="margin-bottom:.2rem;">
                <div class="cont-progress-fill" style="width: {{ $r['pctCobrado'] }}%;"></div>
            </div>
            <div class="cont-progress" style="height:4px;background:rgba(255,255,255,.12);">
                <div class="cont-progress-fill" style="width: {{ $r['pctCobrado_r'] }}%;background:linear-gradient(90deg,#6cc8b0,#fff);"></div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs por tipo + tabla por programa --}}
<div class="cont-panel">
    <div class="cont-panel-head">
        <span class="cont-panel-icon"><i class="ri-stack-line"></i></span>
        <div>
            <h6 class="cont-panel-title">Detalle por Programa</h6>
            <p class="cont-panel-sub">Agrupado por tipo · {{ array_sum(array_map(fn($t) => count($t['programas']), $mainStats['porTipo'])) }} programas en {{ count($mainStats['porTipo']) }} tipos</p>
        </div>
    </div>
    <div class="cont-panel-body">
        @if(count($mainStats['porTipo']) === 0)
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Sin programas</h5>
                <p class="text-muted mb-0">No hay programas registrados para la gestión {{ $gestionFiltro }}.</p>
            </div>
        @else
        <ul class="nav tipo-tabs" role="tablist">
            @foreach($mainStats['porTipo'] as $idx => $t)
                <li class="nav-item" role="presentation">
                    <button class="tipo-tab @if($idx === 0) active @endif" data-bs-toggle="tab" data-bs-target="#tipo-pane-{{ $t['tipo_id'] }}" type="button" role="tab">
                        <i class="ri-price-tag-3-line"></i> {{ $t['tipo_nombre'] }}
                        <span class="badge">{{ count($t['programas']) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($mainStats['porTipo'] as $idx => $t)
            <div class="tab-pane @if($idx === 0) show active @endif" id="tipo-pane-{{ $t['tipo_id'] }}" role="tabpanel">
                <div class="tipo-header">
                    <span class="tipo-header-icon"><i class="ri-price-tag-3-line"></i></span>
                    <div>
                        <h6 class="tipo-header-title">{{ $t['tipo_nombre'] }}</h6>
                        <span class="tipo-header-sub">{{ count($t['programas']) }} programas en esta categoría</span>
                    </div>
                </div>

                <div class="tabla-prog-cont">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th>Programa</th>
                                    <th style="width: 90px;" class="text-center">Insc<br><small class="text-muted fw-normal">Total / Reales</small></th>
                                    <th style="width: 150px;" class="text-end"><i class="ri-bank-card-line text-primary"></i> Matrícula<br><small class="text-muted fw-normal">Total / Reales</small></th>
                                    <th style="width: 150px;" class="text-end"><i class="ri-funds-line text-info"></i> Colegiatura<br><small class="text-muted fw-normal">Total / Reales</small></th>
                                    <th style="width: 170px;" class="text-center">Avance<br><small class="text-muted fw-normal">Total / Reales</small></th>
                                    <th style="width: 160px;" class="text-end">Saldo Total<br><small class="text-muted fw-normal">Total / Reales</small></th>
                                    <th style="width: 130px;" class="text-center">Fechas pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($t['programas'] as $idxP => $p)
                                <tr @if($p['is_no_ejecutado'] ?? false) class="tr-no-ejecutado" @endif>
                                    <td class="text-center text-muted fw-bold">{{ $idxP + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="prog-icon-sm" @if($p['is_no_ejecutado'] ?? false) style="background:linear-gradient(135deg,#f06548 0%,#e11d48 100%);" @endif><i class="ri-graduation-cap-line"></i></span>
                                            <div style="min-width:0;">
                                                <div class="prog-name"><a href="{{ route('admin.programas.show', ['id' => $p['id']]) }}" class="text-reset text-decoration-none fw-semibold">{{ $p['nombre'] }}</a></div>
                                                <div class="prog-meta"><i class="ri-hashtag"></i> {{ $p['codigo'] }} @if($p['estado_nombre']) · <i class="ri-flag-2-line"></i> {{ $p['estado_nombre'] }}@endif</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <div class="fw-bold fs-14">{{ $p['inscritos'] }}</div>
                                        <span class="metric-reales-val"><span class="dot"></span> {{ $p['inscritos_reales'] }} reales</span>
                                    </td>
                                    <td class="text-end compact-td">
                                        <div class="metric-block">
                                            <span class="deuda">Bs {{ number_format($p['mat_neto'], 2) }}</span>
                                            @if($p['mat_pagado'] > 0)
                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($p['mat_pagado'], 2) }}</span>
                                            @endif
                                            <span class="saldo {{ $p['mat_saldo'] > 0 ? 'danger' : 'ok' }}">
                                                <i class="ri-{{ $p['mat_saldo'] > 0 ? 'time-line' : 'check-line' }}"></i>
                                                {{ $p['mat_saldo'] > 0 ? 'Saldo' : 'Saldado' }} {{ number_format($p['mat_saldo'], 2) }}
                                            </span>
                                            <span class="metric-reales">
                                                <span class="dot"></span>
                                                Bs {{ number_format($p['mat_neto_r'], 2) }}
                                                @if($p['mat_pagado_r'] > 0) · Pag {{ number_format($p['mat_pagado_r'], 2) }}@endif
                                                · Saldo {{ number_format($p['mat_saldo_r'], 2) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end compact-td">
                                        <div class="metric-block">
                                            <span class="deuda">Bs {{ number_format($p['col_neto'], 2) }}</span>
                                            @if($p['col_pagado'] > 0)
                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($p['col_pagado'], 2) }}</span>
                                            @endif
                                            <span class="saldo {{ $p['col_saldo'] > 0 ? 'danger' : 'ok' }}">
                                                <i class="ri-{{ $p['col_saldo'] > 0 ? 'time-line' : 'check-line' }}"></i>
                                                {{ $p['col_saldo'] > 0 ? 'Saldo' : 'Saldado' }} {{ number_format($p['col_saldo'], 2) }}
                                            </span>
                                            <span class="metric-reales">
                                                <span class="dot"></span>
                                                Bs {{ number_format($p['col_neto_r'], 2) }}
                                                @if($p['col_pagado_r'] > 0) · Pag {{ number_format($p['col_pagado_r'], 2) }}@endif
                                                · Saldo {{ number_format($p['col_saldo_r'], 2) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="compact-td">
                                        <div style="display:flex;flex-direction:column;gap:.25rem;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="prog-progress-bar"><div class="prog-progress-fill" style="width: {{ $p['pct'] }}%;"></div></div>
                                                <span class="prog-progress-pct" style="min-width:28px;">{{ $p['pct'] }}%</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="prog-bar-reales flex-grow-1"><div class="fill" style="width: {{ $p['pct_r'] }}%;"></div></div>
                                                <span style="font-size:.65rem;font-weight:700;color:#0ab39c;min-width:28px;text-align:right;">{{ $p['pct_r'] }}%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end compact-td">
                                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.25rem;">
                                        @if($p['saldo_total'] > 0)
                                            <span class="saldo-pill-big deuda"><i class="ri-error-warning-line"></i> Bs {{ number_format($p['saldo_total'], 2) }}</span>
                                        @else
                                            <span class="saldo-pill-big ok"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                        @endif
                                        <span class="metric-reales-val">
                                            <span class="dot"></span>
                                            @if($p['saldo_total_r'] > 0) Bs {{ number_format($p['saldo_total_r'], 2) }} @else Cancelado @endif
                                        </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php $totFechas = ($p['fechas_contado'] ?? 0) + ($p['fechas_regular'] ?? 0); @endphp
                                        <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                        @can('contable.asignaciones.definir')
                                        <button type="button"
                                                class="btn-fechas {{ $totFechas > 0 ? 'has-data' : '' }} btn-open-fechas"
                                                data-id="{{ $p['id'] }}"
                                                data-nombre="{{ $p['nombre'] }}"
                                                data-codigo="{{ $p['codigo'] }}"
                                                title="Registrar fechas de pago">
                                            <i class="ri-calendar-check-line"></i>
                                            {{ $totFechas > 0 ? 'Editar' : 'Definir' }}
                                            <span class="bdg">{{ $totFechas }}</span>
                                        </button>
                                        <button type="button"
                                                class="btn-fechas btn-recalcular-contado"
                                                data-id="{{ $p['id'] }}"
                                                data-nombre="{{ $p['nombre'] }}"
                                                title="Recalcular cuotas Contado">
                                            <i class="ri-refresh-line"></i>
                                            Recalcular
                                        </button>
                                        @endcan
                                        </div>
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
        @endif
    </div>
</div>

{{-- ===== SECCIÓN UPI DIPLOMADOS (USD) ===== --}}
@if(count($upiStats['porTipo']) > 0)
@php $u = $upiStats['resumen']; @endphp
<div class="cont-panel mt-2">
    <div class="cont-panel-head">
        <span class="cont-panel-icon" style="background:var(--cont-gold-soft,#faf3df);color:var(--cont-gold,#cba135);"><i class="ri-money-dollar-circle-line"></i></span>
        <div>
            <h6 class="cont-panel-title">UPI Diplomados <small class="text-muted fw-normal" style="font-size:.72rem;">(USD)</small></h6>
            <p class="cont-panel-sub">{{ array_sum(array_map(fn($t) => count($t['programas']), $upiStats['porTipo'])) }} programa(s) · valores en dólares</p>
        </div>
    </div>
    <div class="cont-panel-body">
    <div class="row g-3 mb-1">
    <div class="col-lg-4 col-md-6">
        <div class="cont-summary-card cont-card-mat">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:#fff3cd;color:#e6a700;"><i class="ri-bank-card-line"></i></span>
                <div>
                    <div class="cont-card-title">Matrícula <small class="text-muted fw-normal" style="font-size:.62rem;">USD</small></div>
                    <div class="cont-card-sub">Bruto ${{ number_format($u['totMatBruto'], 2) }}@if($u['totMatDesc'] > 0) · Dto ${{ number_format($u['totMatDesc'], 2) }}@endif</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda neta</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-primary d-block">${{ number_format($u['totMatNeto'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totMatNeto_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-success d-block">${{ number_format($u['totMatPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totMatPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="padding-top:.35rem;">
                    <span><i class="ri-{{ $u['totMatSaldo'] > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="{{ $u['totMatSaldo'] > 0 ? 'text-danger' : 'text-success' }} d-block">${{ number_format($u['totMatSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totMatSaldo_r'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="cont-summary-card cont-card-col">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:#d1fae5;color:#0ab39c;"><i class="ri-funds-line"></i></span>
                <div>
                    <div class="cont-card-title">Colegiatura <small class="text-muted fw-normal" style="font-size:.62rem;">USD</small></div>
                    <div class="cont-card-sub">Bruto ${{ number_format($u['totColBruto'], 2) }}@if($u['totColDesc'] > 0) · Dto ${{ number_format($u['totColDesc'], 2) }}@endif</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda neta</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-info d-block">${{ number_format($u['totColNeto'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totColNeto_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="text-success d-block">${{ number_format($u['totColPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totColPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="padding-top:.35rem;">
                    <span><i class="ri-{{ $u['totColSaldo'] > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong class="{{ $u['totColSaldo'] > 0 ? 'text-danger' : 'text-success' }} d-block">${{ number_format($u['totColSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#0ab39c;white-space:nowrap;">Reales: ${{ number_format($u['totColSaldo_r'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="cont-summary-card cont-card-total" style="background:linear-gradient(135deg,#e6a700 0%,#f59e0b 60%,#f97316 100%);">
            <div class="cont-card-header">
                <span class="cont-card-icon" style="background:rgba(255,255,255,.25);color:#fff;"><i class="ri-money-dollar-circle-line"></i></span>
                <div>
                    <div class="cont-card-title" style="color:#fff;">Total UPI</div>
                    <div class="cont-card-sub" style="color:rgba(255,255,255,.85);">{{ $u['pctCobrado'] }}% cobrado · {{ $u['totInscritosReales'] }} reales</div>
                </div>
            </div>
            <div class="cont-card-rows">
                <div class="cont-row" style="color:rgba(255,255,255,.9);padding-top:0;">
                    <span><i class="ri-error-warning-line"></i> Deuda total</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;">${{ number_format($u['totGenDeuda'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#fedf9a;white-space:nowrap;">Reales: ${{ number_format($u['totGenDeuda_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row" style="color:rgba(255,255,255,.9);padding-top:0;">
                    <span><i class="ri-check-line"></i> Cobrado</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;">${{ number_format($u['totGenPagado'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#fedf9a;white-space:nowrap;">Reales: ${{ number_format($u['totGenPagado_r'], 2) }}</small>
                    </div>
                </div>
                <div class="cont-row saldo-row" style="color:rgba(255,255,255,.9);border-top-color:rgba(255,255,255,.2);padding-top:.35rem;">
                    <span><i class="ri-time-line"></i> Saldo</span>
                    <div class="text-end" style="line-height:1.1;">
                        <strong style="color:#fff;display:block;background:rgba(255,255,255,.2);padding:.1em .5em;border-radius:5px;">${{ number_format($u['totGenSaldo'], 2) }}</strong>
                        <small class="fw-bold" style="font-size:.62rem;color:#fedf9a;white-space:nowrap;">Reales: ${{ number_format($u['totGenSaldo_r'], 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="cont-progress mt-2" style="margin-bottom:.2rem;">
                <div class="cont-progress-fill" style="width: {{ $u['pctCobrado'] }}%;"></div>
            </div>
            <div class="cont-progress" style="height:4px;background:rgba(255,255,255,.12);">
                <div class="cont-progress-fill" style="width: {{ $u['pctCobrado_r'] }}%;background:linear-gradient(90deg,#fedf9a,#fff);"></div>
            </div>
        </div>
    </div>
    </div>

        @php $tUPI = $upiStats['porTipo'][0]; @endphp
        <div class="tabla-prog-cont">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th>Programa</th>
                            <th style="width: 90px;" class="text-center">Insc<br><small class="text-muted fw-normal">Total / Reales</small></th>
                            <th style="width: 150px;" class="text-end">Matrícula USD<br><small class="text-muted fw-normal">Total / Reales</small></th>
                            <th style="width: 150px;" class="text-end">Colegiatura USD<br><small class="text-muted fw-normal">Total / Reales</small></th>
                            <th style="width: 170px;" class="text-center">Avance<br><small class="text-muted fw-normal">Total / Reales</small></th>
                            <th style="width: 160px;" class="text-end">Saldo Total USD<br><small class="text-muted fw-normal">Total / Reales</small></th>
                            <th style="width: 130px;" class="text-center">Fechas pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tUPI['programas'] as $idxP => $p)
                        <tr @if($p['is_no_ejecutado'] ?? false) class="tr-no-ejecutado" @endif>
                            <td class="text-center text-muted fw-bold">{{ $idxP + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="prog-icon-sm" style="background:linear-gradient(135deg,{{ ($p['is_no_ejecutado'] ?? false) ? '#f06548,#e11d48' : '#e6a700,#f59e0b' }});"><i class="ri-money-dollar-circle-line"></i></span>
                                    <div style="min-width:0;">
                                        <div class="prog-name"><a href="{{ route('admin.programas.show', ['id' => $p['id']]) }}" class="text-reset text-decoration-none fw-semibold">{{ $p['nombre'] }}</a></div>
                                        <div class="prog-meta"><i class="ri-hashtag"></i> {{ $p['codigo'] }} @if($p['estado_nombre']) · <i class="ri-flag-2-line"></i> {{ $p['estado_nombre'] }}@endif</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" style="white-space:nowrap;">
                                <div class="fw-bold fs-14">{{ $p['inscritos'] }}</div>
                                <span class="metric-reales-val"><span class="dot"></span> {{ $p['inscritos_reales'] }} reales</span>
                            </td>
                            <td class="text-end compact-td">
                                <div class="metric-block">
                                    <span class="deuda">${{ number_format($p['mat_neto'], 2) }}</span>
                                    @if($p['mat_pagado'] > 0)
                                    <span class="pagado"><i class="ri-check-line"></i> Pag ${{ number_format($p['mat_pagado'], 2) }}</span>
                                    @endif
                                    <span class="saldo {{ $p['mat_saldo'] > 0 ? 'danger' : 'ok' }}">
                                        <i class="ri-{{ $p['mat_saldo'] > 0 ? 'time-line' : 'check-line' }}"></i>
                                        {{ $p['mat_saldo'] > 0 ? 'Saldo' : 'Saldado' }} ${{ number_format($p['mat_saldo'], 2) }}
                                    </span>
                                    <span class="metric-reales">
                                        <span class="dot"></span>
                                        ${{ number_format($p['mat_neto_r'], 2) }}
                                        @if($p['mat_pagado_r'] > 0) · Pag ${{ number_format($p['mat_pagado_r'], 2) }}@endif
                                        · Saldo ${{ number_format($p['mat_saldo_r'], 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-end compact-td">
                                <div class="metric-block">
                                    <span class="deuda">${{ number_format($p['col_neto'], 2) }}</span>
                                    @if($p['col_pagado'] > 0)
                                    <span class="pagado"><i class="ri-check-line"></i> Pag ${{ number_format($p['col_pagado'], 2) }}</span>
                                    @endif
                                    <span class="saldo {{ $p['col_saldo'] > 0 ? 'danger' : 'ok' }}">
                                        <i class="ri-{{ $p['col_saldo'] > 0 ? 'time-line' : 'check-line' }}"></i>
                                        {{ $p['col_saldo'] > 0 ? 'Saldo' : 'Saldado' }} ${{ number_format($p['col_saldo'], 2) }}
                                    </span>
                                    <span class="metric-reales">
                                        <span class="dot"></span>
                                        ${{ number_format($p['col_neto_r'], 2) }}
                                        @if($p['col_pagado_r'] > 0) · Pag ${{ number_format($p['col_pagado_r'], 2) }}@endif
                                        · Saldo ${{ number_format($p['col_saldo_r'], 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="compact-td">
                                <div style="display:flex;flex-direction:column;gap:.25rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="prog-progress-bar"><div class="prog-progress-fill" style="width: {{ $p['pct'] }}%;"></div></div>
                                        <span class="prog-progress-pct" style="min-width:28px;">{{ $p['pct'] }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="prog-bar-reales flex-grow-1"><div class="fill" style="width: {{ $p['pct_r'] }}%;"></div></div>
                                        <span style="font-size:.65rem;font-weight:700;color:#0ab39c;min-width:28px;text-align:right;">{{ $p['pct_r'] }}%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end compact-td">
                                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.25rem;">
                                @if($p['saldo_total'] > 0)
                                    <span class="saldo-pill-big" style="background:#fef3c7;color:#e6a700;border-color:#fde68a;"><i class="ri-error-warning-line"></i> ${{ number_format($p['saldo_total'], 2) }}</span>
                                @else
                                    <span class="saldo-pill-big ok"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                @endif
                                <span class="metric-reales-val">
                                    <span class="dot" style="background:#e6a700;"></span>
                                    @if($p['saldo_total_r'] > 0) ${{ number_format($p['saldo_total_r'], 2) }} @else Cancelado @endif
                                </span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php $totFechas = ($p['fechas_contado'] ?? 0) + ($p['fechas_regular'] ?? 0); @endphp
                                <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                @can('contable.asignaciones.definir')
                                <button type="button"
                                        class="btn-fechas {{ $totFechas > 0 ? 'has-data' : '' }} btn-open-fechas"
                                        data-id="{{ $p['id'] }}"
                                        data-nombre="{{ $p['nombre'] }}"
                                        data-codigo="{{ $p['codigo'] }}"
                                        title="Registrar fechas de pago">
                                    <i class="ri-calendar-check-line"></i>
                                    {{ $totFechas > 0 ? 'Editar' : 'Definir' }}
                                    <span class="bdg">{{ $totFechas }}</span>
                                </button>
                                <button type="button"
                                        class="btn-fechas btn-recalcular-contado"
                                        data-id="{{ $p['id'] }}"
                                        data-nombre="{{ $p['nombre'] }}"
                                        title="Recalcular cuotas Contado">
                                    <i class="ri-refresh-line"></i>
                                    Recalcular
                                </button>
                                @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Fechas de Pago --}}
<div class="modal fade" id="modalFechas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-check-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Fechas de Pago por Programa</h5>
                        <small class="modal-subtitle" id="mfSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-soft-info d-flex align-items-start gap-2 mb-3 py-2 fs-13">
                    <i class="ri-information-line fs-16"></i>
                    <div>
                        Las fechas se aplican a <b>todos los participantes</b> de este programa según el plan elegido.
                        <span id="mfPlanCounts" class="d-block mt-1"></span>
                    </div>
                </div>

                <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Plan</label>
                <div class="plan-switch">
                    <button type="button" class="plan-card contado" data-plan="contado">
                        <span class="pc-icon"><i class="ri-flashlight-line"></i></span>
                        <div>
                            <div class="pc-nombre">Contado</div>
                            <div class="pc-meta">1 cuota Matrícula + 2 cuotas Colegiatura</div>
                        </div>
                    </button>
                    <button type="button" class="plan-card regular" data-plan="regular">
                        <span class="pc-icon"><i class="ri-calendar-2-line"></i></span>
                        <div>
                            <div class="pc-nombre">Regular</div>
                            <div class="pc-meta">Una fecha por cada cuota mensual</div>
                        </div>
                    </button>
                </div>

                <div id="mfLoader" class="text-center py-3 text-muted" style="display:none;">
                    <span class="spinner-border spinner-border-sm me-1"></span>Cargando...
                </div>

                <div id="mfContent">
                    <div class="fechas-grid">
                        <div class="fechas-col" data-tipo="Matricula">
                            <div class="fechas-col-header">
                                <span class="fechas-col-title"><i class="ri-bank-card-line text-primary"></i> Matrícula</span>
                                <button type="button" class="btn-add-fecha" data-add="Matricula">
                                    <i class="ri-add-line"></i> Agregar
                                </button>
                            </div>
                            <div class="fechas-gen">
                                <div class="fechas-gen-title"><i class="ri-magic-line"></i> Generar fechas</div>
                                <div class="fechas-gen-row">
                                    <div class="grp">
                                        <label>Día</label>
                                        <input type="number" min="1" max="31" placeholder="15" data-gen-dia="Matricula">
                                    </div>
                                    <div class="grp">
                                        <label>Mes inicio</label>
                                        <input type="month" data-gen-mes="Matricula">
                                    </div>
                                    <button type="button" class="btn-apply-gen" data-gen-apply="Matricula">
                                        <i class="ri-check-line"></i> Aplicar
                                    </button>
                                </div>
                            </div>
                            <div class="fechas-list" data-list="Matricula"></div>
                        </div>
                        <div class="fechas-col" data-tipo="Colegiatura">
                            <div class="fechas-col-header">
                                <span class="fechas-col-title"><i class="ri-funds-line text-success"></i> Colegiatura</span>
                                <button type="button" class="btn-add-fecha" data-add="Colegiatura">
                                    <i class="ri-add-line"></i> Agregar
                                </button>
                            </div>
                            <div class="fechas-gen">
                                <div class="fechas-gen-title"><i class="ri-magic-line"></i> Generar fechas</div>
                                <div class="fechas-gen-row">
                                    <div class="grp">
                                        <label>Día</label>
                                        <input type="number" min="1" max="31" placeholder="15" data-gen-dia="Colegiatura">
                                    </div>
                                    <div class="grp">
                                        <label>Mes inicio</label>
                                        <input type="month" data-gen-mes="Colegiatura">
                                    </div>
                                    <button type="button" class="btn-apply-gen" data-gen-apply="Colegiatura">
                                        <i class="ri-check-line"></i> Aplicar
                                    </button>
                                </div>
                            </div>
                            <div class="fechas-list" data-list="Colegiatura"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnSaveFechas">
                    <i class="ri-save-3-line"></i> Guardar Fechas
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Preview Recalcular Contado --}}
<div class="modal fade" id="modalPreviewRecalcular" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#f06548 0%,#e11d48 100%);color:#fff;border:0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);">
                        <i class="ri-refresh-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;">Recalcular Cuotas Contado</h5>
                        <small class="modal-subtitle" style="color:rgba(255,255,255,.85);" id="mprSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="background:#fafbfd;padding:1.25rem;">
                <div id="mprLoader" class="text-center py-4 text-muted" style="display:none;">
                    <span class="spinner-border spinner-border-sm me-1"></span>Analizando cuotas...
                </div>
                <div id="mprEmpty" class="text-center py-4 text-muted" style="display:none;">
                    <i class="ri-checkbox-circle-line fs-32 text-success d-block mb-2"></i>
                    <span style="font-size:.9rem;">No se encontraron participantes Contado con más de 2 cuotas de Colegiatura</span>
                </div>
                <div id="mprContent" style="display:none;">
                    <div class="alert alert-soft-warning d-flex align-items-start gap-2 mb-3 py-2 fs-13">
                        <i class="ri-alert-line fs-16"></i>
                        <div>
                            Se detectaron <strong id="mprCount">0</strong> participante(s) con más de 2 cuotas de Colegiatura.
                            Se consolidará la deuda en las <strong>2 primeras cuotas</strong>.
                        </div>
                    </div>
                    <div id="mprList"></div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmRecalcular">
                    <i class="ri-refresh-line"></i> Confirmar Recálculo
                </button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /cont-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = '{{ csrf_token() }}';
    const baseUrl = '{{ url("admin/contable/programa") }}';
    let currentProgId = null;
    let currentPlan = 'contado';
    let dataDefaults = { contado: { Matricula: 1, Colegiatura: 2 }, regular: { Matricula: 1, Colegiatura: 1 } };
    let dataFechas = { contado: { Matricula: [], Colegiatura: [] }, regular: { Matricula: [], Colegiatura: [] } };
    let planCounts = { contado: 0, regular: 0 };
    let dataNeedsRecalculo = false;

    const modalFechas = new bootstrap.Modal(document.getElementById('modalFechas'));

    document.querySelectorAll('.btn-open-fechas').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentProgId = this.dataset.id;
            document.getElementById('mfSubtitle').textContent = this.dataset.nombre + ' · ' + this.dataset.codigo;
            cargar();
        });
    });

    function cargar() {
        document.getElementById('mfLoader').style.display = 'block';
        document.getElementById('mfContent').style.display = 'none';
        document.getElementById('mfPlanCounts').textContent = '';
        modalFechas.show();

        fetch(baseUrl + '/' + currentProgId + '/fechas', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('mfLoader').style.display = 'none';
            document.getElementById('mfContent').style.display = 'block';
            if (!data.success) {
                showToast(data.message || 'Error al cargar', 'danger');
                return;
            }
            dataDefaults = data.defaults;
            dataFechas = data.fechas;
            planCounts = data.planes_count;
            dataNeedsRecalculo = data.needs_recalculo === true;
            document.getElementById('mfPlanCounts').innerHTML =
                'Participantes: <span class="badge bg-soft-info text-info">Contado: ' + planCounts.contado + '</span> ' +
                '<span class="badge bg-soft-success text-success">Regular: ' + planCounts.regular + '</span>';

            // Plan inicial: el que más participantes tenga
            currentPlan = (planCounts.contado >= planCounts.regular) ? 'contado' : 'regular';
            if (dataFechas.contado.Matricula.length + dataFechas.contado.Colegiatura.length === 0 &&
                dataFechas.regular.Matricula.length + dataFechas.regular.Colegiatura.length > 0) {
                currentPlan = 'regular';
            }
            // Si el plan contado necesita recalculo, forzar a regular
            if (currentPlan === 'contado' && dataNeedsRecalculo) {
                currentPlan = 'regular';
            }
            applyPlan();
        })
        .catch(() => {
            document.getElementById('mfLoader').style.display = 'none';
            document.getElementById('mfContent').style.display = 'block';
            showToast('Error de conexión', 'danger');
        });
    }

    document.querySelectorAll('.plan-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var plan = this.dataset.plan;
            // Si es contado y necesita recalculo, bloquear
            if (plan === 'contado' && dataNeedsRecalculo) {
                showToast('No se puede configurar el plan Contado porque existen participantes con más de 2 cuotas de Colegiatura. Use la opción "Recalcular" primero.', 'warning');
                return;
            }
            // Antes de cambiar, guarda en memoria lo escrito
            recogerInputs();
            currentPlan = plan;
            applyPlan();
        });
    });

    function applyPlan() {
        document.querySelectorAll('.plan-card').forEach(c => c.classList.toggle('is-active', c.dataset.plan === currentPlan));
        renderTipo('Matricula');
        renderTipo('Colegiatura');
    }

    function renderTipo(tipo) {
        const list = document.querySelector('[data-list="' + tipo + '"]');
        const arr = dataFechas[currentPlan][tipo] || [];
        if (arr.length === 0) {
            const defCount = dataDefaults[currentPlan][tipo] || 1;
            for (let i = 0; i < defCount; i++) arr.push({ numero: i + 1, fecha_pago: '' });
            dataFechas[currentPlan][tipo] = arr;
        }
        let html = '';
        arr.forEach(function (it, idx) {
            html +=
                '<div class="fecha-row" data-idx="' + idx + '">' +
                    '<span class="num">' + (idx + 1) + '</span>' +
                    '<input type="date" value="' + (it.fecha_pago || '') + '">' +
                    '<button type="button" class="btn-rm-fecha" data-rm="' + tipo + '" data-idx="' + idx + '" title="Eliminar"><i class="ri-close-line"></i></button>' +
                '</div>';
        });
        list.innerHTML = html;

        list.querySelectorAll('[data-rm]').forEach(function (b) {
            b.addEventListener('click', function () {
                const t = this.dataset.rm;
                const i = parseInt(this.dataset.idx);
                recogerInputs();
                dataFechas[currentPlan][t].splice(i, 1);
                renderTipo(t);
            });
        });
    }

    document.querySelectorAll('[data-add]').forEach(function (b) {
        b.addEventListener('click', function () {
            recogerInputs();
            const t = this.dataset.add;
            const next = (dataFechas[currentPlan][t] || []).length + 1;
            dataFechas[currentPlan][t].push({ numero: next, fecha_pago: '' });
            renderTipo(t);
        });
    });

    // ===== Generador de fechas =====
    function pad2(n) { return String(n).padStart(2, '0'); }
    function toISO(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }

    // Genera una fecha. Si el día no existe en (year, monthIdx), usa día 1 del mes siguiente.
    function buildSafeDate(year, monthIdx, day) {
        const maxDay = new Date(year, monthIdx + 1, 0).getDate();
        if (day <= maxDay) return new Date(year, monthIdx, day);
        return new Date(year, monthIdx + 1, 1);
    }

    document.querySelectorAll('[data-gen-apply]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tipo = this.dataset.genApply;
            const diaInp = document.querySelector('[data-gen-dia="' + tipo + '"]');
            const mesInp = document.querySelector('[data-gen-mes="' + tipo + '"]');
            const dia = parseInt(diaInp.value) || 0;
            const mesVal = mesInp.value; // formato 'YYYY-MM'

            recogerInputs();
            const arr = dataFechas[currentPlan][tipo] || [];
            if (arr.length === 0) {
                showToast('Agrega al menos una fila de fecha primero', 'warning');
                return;
            }

            if (!dia && !mesVal) {
                showToast('Ingresa al menos el día o el mes/año de inicio', 'warning');
                return;
            }

            arr.forEach(function (it, i) {
                // Día base
                let useDay = dia;
                if (!useDay && it.fecha_pago) {
                    useDay = parseInt(it.fecha_pago.split('-')[2]);
                }
                if (!useDay) useDay = 1;

                let year, month;
                if (mesVal) {
                    const parts = mesVal.split('-').map(Number);
                    year = parts[0];
                    const startMonth0 = parts[1] - 1; // 0-based
                    const total = startMonth0 + i;
                    year = year + Math.floor(total / 12);
                    month = total % 12;
                } else if (it.fecha_pago) {
                    const p = it.fecha_pago.split('-').map(Number);
                    year = p[0]; month = p[1] - 1;
                } else {
                    const t = new Date();
                    year = t.getFullYear(); month = t.getMonth();
                }

                const d = buildSafeDate(year, month, useDay);
                it.fecha_pago = toISO(d);
            });

            renderTipo(tipo);
            showToast(arr.length + ' fechas generadas para ' + tipo, 'info');
        });
    });

    function recogerInputs() {
        ['Matricula', 'Colegiatura'].forEach(function (tipo) {
            const inputs = document.querySelectorAll('[data-list="' + tipo + '"] input[type=date]');
            inputs.forEach(function (inp, i) {
                if (!dataFechas[currentPlan][tipo][i]) dataFechas[currentPlan][tipo][i] = { numero: i + 1, fecha_pago: '' };
                dataFechas[currentPlan][tipo][i].fecha_pago = inp.value;
            });
        });
    }

    document.getElementById('btnSaveFechas').addEventListener('click', function () {
        recogerInputs();
        const btn = this;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const fd = new FormData();
        fd.append('_token', csrf);
        fd.append('plan', currentPlan);
        ['Matricula', 'Colegiatura'].forEach(function (tipo) {
            const arr = dataFechas[currentPlan][tipo] || [];
            arr.forEach(function (it) {
                if (it.fecha_pago) fd.append('fechas[' + tipo + '][]', it.fecha_pago);
            });
        });

        fetch(baseUrl + '/' + currentProgId + '/fechas', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalFechas.hide();
                showToast(data.message, 'success');
                setTimeout(function () { location.reload(); }, 500);
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión', 'danger'))
        .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
    });

    // Modal Preview Recalcular
    const modalPreviewRec = new bootstrap.Modal(document.getElementById('modalPreviewRecalcular'));
    let previewProgId = null;

    document.querySelectorAll('.btn-recalcular-contado').forEach(function (btn) {
        btn.addEventListener('click', function () {
            previewProgId = this.dataset.id;
            document.getElementById('mprSubtitle').textContent = this.dataset.nombre;
            document.getElementById('mprLoader').style.display = 'block';
            document.getElementById('mprEmpty').style.display = 'none';
            document.getElementById('mprContent').style.display = 'none';
            document.getElementById('mprList').innerHTML = '';
            modalPreviewRec.show();

            fetch('{{ url("admin/contable/programa") }}/' + previewProgId + '/preview-recalcular', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                document.getElementById('mprLoader').style.display = 'none';
                if (!ok || !data.success) {
                    document.getElementById('mprEmpty').style.display = 'block';
                    document.getElementById('mprEmpty').innerHTML = '<i class="ri-error-warning-line fs-32 text-danger d-block mb-2"></i><span style="font-size:.9rem;">' + (data.message || 'Error al cargar') + '</span>';
                    return;
                }
                if (data.total === 0) {
                    document.getElementById('mprEmpty').style.display = 'block';
                    return;
                }
                document.getElementById('mprContent').style.display = 'block';
                document.getElementById('mprCount').textContent = data.total;

                var html = '';
                data.participantes.forEach(function (p, idx) {
                    html +=
                        '<div class="card mb-3" style="border:1px solid #eef0f4;border-radius:12px;overflow:hidden;">' +
                            '<div class="card-header d-flex align-items-center gap-2 py-2 px-3" style="background:#f8f9fc;border-bottom:1px solid #eef0f4;">' +
                                '<span class="badge bg-soft-danger text-danger fs-11">#' + (idx + 1) + '</span>' +
                                '<div class="fw-semibold fs-13">' + p.nombre_completo + '</div>' +
                                '<span class="badge bg-soft-primary text-primary fs-11">CI ' + p.ci + '</span>' +
                            '</div>' +
                            '<div class="card-body p-3">' +
                                '<table class="table table-sm table-bordered mb-0" style="font-size:.78rem;">' +
                                    '<thead class="table-light">' +
                                        '<tr>' +
                                            '<th>#</th>' +
                                            '<th class="text-end">Deuda Actual</th>' +
                                            '<th class="text-end">Desc. Actual</th>' +
                                            '<th class="text-end">Pagado Actual</th>' +
                                            '<th class="text-end">Deuda Nueva</th>' +
                                            '<th class="text-end">Desc. Nuevo</th>' +
                                            '<th class="text-end">Pagado Nuevo</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>';

                    p.cuotas.forEach(function (c, i) {
                        var nuevaDeuda = (i === 0) ? p.despues.cuota1_deuda : (i === 1 ? p.despues.cuota2_deuda : 0);
                        var nuevaDesc = (i === 0) ? p.despues.cuota1_descuento : (i === 1 ? p.despues.cuota2_descuento : 0);
                        var nuevaPag = (i === 0) ? p.despues.cuota1_pagado : (i === 1 ? p.despues.cuota2_pagado : 0);
                        var esEliminada = i >= 2 && c.deuda > 0;
                        html +=
                            '<tr class="' + (esEliminada ? 'table-danger' : '') + '">' +
                                '<td class="fw-bold">' + (i + 1) + '</td>' +
                                '<td class="text-end">Bs ' + c.deuda.toFixed(2) + '</td>' +
                                '<td class="text-end">Bs ' + c.descuento.toFixed(2) + '</td>' +
                                '<td class="text-end">Bs ' + c.total_pagado.toFixed(2) + '</td>' +
                                '<td class="text-end fw-bold ' + (nuevaDeuda > 0 ? 'text-primary' : 'text-muted') + '">Bs ' + nuevaDeuda.toFixed(2) + '</td>' +
                                '<td class="text-end ' + (nuevaDesc > 0 ? '' : 'text-muted') + '">Bs ' + nuevaDesc.toFixed(2) + '</td>' +
                                '<td class="text-end ' + (nuevaPag > 0 ? 'text-success fw-bold' : 'text-muted') + '">Bs ' + nuevaPag.toFixed(2) + '</td>' +
                            '</tr>';
                    });

                    html +=
                                    '</tbody>' +
                                    '<tfoot>' +
                                        '<tr class="table-active fw-bold">' +
                                            '<td>Total</td>' +
                                            '<td class="text-end">Bs ' + p.total_deuda.toFixed(2) + '</td>' +
                                            '<td class="text-end">Bs ' + p.total_descuento.toFixed(2) + '</td>' +
                                            '<td class="text-end">Bs ' + p.total_pagado.toFixed(2) + '</td>' +
                                            '<td class="text-end text-primary">Bs ' + (p.despues.cuota1_deuda + p.despues.cuota2_deuda).toFixed(2) + '</td>' +
                                            '<td class="text-end">Bs ' + (p.despues.cuota1_descuento + p.despues.cuota2_descuento).toFixed(2) + '</td>' +
                                            '<td class="text-end text-success">Bs ' + (p.despues.cuota1_pagado + p.despues.cuota2_pagado).toFixed(2) + '</td>' +
                                        '</tr>' +
                                    '</tfoot>' +
                                '</table>' +
                            '</div>' +
                        '</div>';
                });
                document.getElementById('mprList').innerHTML = html;
            })
            .catch(function () {
                document.getElementById('mprLoader').style.display = 'none';
                document.getElementById('mprEmpty').style.display = 'block';
                document.getElementById('mprEmpty').innerHTML = '<i class="ri-error-warning-line fs-32 text-danger d-block mb-2"></i><span style="font-size:.9rem;">Error de conexión</span>';
            });
        });
    });

    document.getElementById('btnConfirmRecalcular').addEventListener('click', function () {
        if (!previewProgId) return;
        var btn = this;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Recalculando...';

        fetch('{{ url("admin/contable/programa") }}/' + previewProgId + '/recalcular-contado', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            modalPreviewRec.hide();
            showToast(data.message || 'Hecho', ok ? 'success' : 'danger');
        })
        .catch(function () {
            showToast('Error de conexión', 'danger');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });

    function showToast(msg, type) {
        type = type || 'success';
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        const colors = { success: '#0ab39c', danger: '#f06548', warning: '#f7b84b', info: '#299cdb' };
        const icons = { success: 'ri-checkbox-circle-line', danger: 'ri-error-warning-line', warning: 'ri-alert-line', info: 'ri-information-line' };
        const el = document.createElement('div');
        el.className = 'toast align-items-center text-white border-0 show';
        el.style.background = colors[type] || colors.success;
        el.style.minWidth = '280px';
        el.style.boxShadow = '0 10px 24px -10px rgba(0,0,0,.3)';
        el.innerHTML =
            '<div class="d-flex align-items-center">' +
                '<div class="toast-body d-flex align-items-center gap-2 fs-13 fw-semibold">' +
                    '<i class="' + (icons[type] || icons.success) + ' fs-5"></i>' +
                    '<span>' + msg + '</span>' +
                '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>';
        container.appendChild(el);
        try {
            const inst = new bootstrap.Toast(el, { delay: 3500 });
            inst.show();
            el.addEventListener('hidden.bs.toast', () => el.remove());
        } catch (e) {
            setTimeout(() => el.remove(), 3500);
        }
    }
});
</script>
@endpush
