@extends('admin.layouts.master')

@php
    $esVencidas = $modo === 'vencidas';
    $titulo = $esVencidas ? 'Cuotas Vencidas' : 'Próximas Cuotas';
    $colorPrim = $esVencidas ? '#f06548' : '#299cdb';
    $colorPrim2 = $esVencidas ? '#ff8a6c' : '#3577f1';
    $colorAccent = $esVencidas ? '#fecaca' : '#bae6fd';
    $heroIcon = $esVencidas ? 'ri-error-warning-line' : 'ri-time-line';
    $subtitulo = $esVencidas
        ? 'Participantes con cuotas vencidas que requieren cobro inmediato'
        : 'Participantes con la cuota del mes en curso por vencer';
@endphp

@section('title', $titulo . ' - ' . config('app.name'))

@push('css')
<style>
.rep-hero {
    position: relative;
    background: linear-gradient(135deg, {{ $colorPrim }} 0%, {{ $colorPrim2 }} 55%, #1c486c 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.7);
    margin-bottom: 1.25rem;
}
.rep-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.rep-hero::after  { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.rep-hero > * { position: relative; z-index: 1; }
.rep-hero .hero-icon {
    width:56px; height:56px;
    display:inline-flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.25);
    border-radius:14px; font-size:1.6rem;
}
.rep-hero h4 { color:#fff; font-weight:700; margin:0; }
.rep-hero .hero-sub { color: rgba(255,255,255,.85); font-size:.85rem; margin: 2px 0 0; }
.rep-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.rep-hero .breadcrumb-item, .rep-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.rep-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }

.filter-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); margin-bottom:1rem; }
[data-layout-mode="dark"] .filter-card { background:#1d2125; border-color:#2a2f34; }
.filter-card .card-body { padding:.9rem 1.25rem; }
.filter-card label { font-size:.65rem; text-transform:uppercase; letter-spacing:.4px; color:#878a99; font-weight:700; margin-bottom:2px; display:block; }
.filter-card select, .filter-card input {
    border:1px solid #e9ebec; border-radius:8px;
    padding:.45rem .75rem; font-size:.85rem; font-weight:700; color:#405189;
    min-width: 130px;
}
.filter-card select:focus, .filter-card input:focus { outline:0; border-color:{{ $colorPrim }}; box-shadow: 0 0 0 .15rem rgba(240,101,72,.15); }
[data-layout-mode="dark"] .filter-card select, [data-layout-mode="dark"] .filter-card input { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

.kpi { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.15rem; height:100%; position:relative; overflow:hidden; }
.kpi::before { content:""; position:absolute; top:0; left:0; height:100%; width:4px; background: var(--k-accent); }
[data-layout-mode="dark"] .kpi { background:#1d2125; border-color:#2a2f34; }
.kpi .k-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.kpi .k-val { font-size:1.4rem; font-weight:800; color:#212529; line-height:1.1; }
[data-layout-mode="dark"] .kpi .k-val { color:#fff; }
.kpi .k-ic {
    width:42px; height:42px; border-radius:12px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:1.2rem; background: var(--k-bg); color: var(--k-color);
}

/* Programa */
.prog-block {
    border:1px solid #eef0f4;
    border-radius:14px;
    background:#fff;
    margin-bottom:1rem;
    overflow:hidden;
}
[data-layout-mode="dark"] .prog-block { background:#1d2125; border-color:#2a2f34; }
.prog-block-head {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex; align-items: center; gap: 1rem;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .prog-block-head { background:#1a1d20; border-bottom-color:#2a2f34; }
.prog-icon-big {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, {{ $colorPrim }}, {{ $colorPrim2 }});
    color:#fff;
    border-radius:12px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:1.3rem; flex-shrink:0;
    box-shadow: 0 6px 14px -6px rgba(0,0,0,.25);
}
.prog-info { flex:1; min-width:0; }
.prog-info .nm { font-weight: 800; font-size: 1rem; color:#212529; line-height:1.2; }
[data-layout-mode="dark"] .prog-info .nm { color:#fff; }
.prog-info .me { font-size:.75rem; color:#878a99; margin-top:2px; }
.prog-totales { display:flex; flex-direction:column; align-items:flex-end; gap:2px; }
.prog-totales .tt { font-size:1.1rem; font-weight:800; color:{{ $colorPrim }}; }
.prog-totales .ts { font-size:.7rem; color:#878a99; }

/* WhatsApp button */
.btn-wa-bulk {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border:0; color:#fff;
    border-radius: 10px;
    padding: .55rem 1rem;
    font-weight: 700;
    font-size: .82rem;
    display: inline-flex; align-items: center; gap: .35rem;
    box-shadow: 0 6px 16px -8px rgba(37,211,102,.55);
    transition: all .15s ease;
}
.btn-wa-bulk:hover { color:#fff; transform: translateY(-1px); box-shadow: 0 10px 22px -10px rgba(37,211,102,.7); }

.part-row {
    display: grid;
    grid-template-columns: 44px 1fr 280px 120px 110px;
    align-items: center;
    gap: .8rem;
    padding: .85rem 1.25rem;
    border-bottom: 1px solid #f1f3f5;
    transition: background .15s ease;
    position: relative;
}
.part-row:hover { background: #f8f9fc; }
.part-row::before {
    content:''; position:absolute; left:0; top:4px; bottom:4px; width:3px;
    border-radius: 0 3px 3px 0;
    background: transparent;
    transition: background .2s ease;
}
.part-row:hover::before { background: {{ $colorPrim }}; }
.part-row:last-child { border-bottom: 0; }
[data-layout-mode="dark"] .part-row { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .part-row:hover { background:#1a1d20; }
@media (max-width: 991.98px) { .part-row { grid-template-columns: 1fr; gap: .4rem; padding: 1rem; } }

.part-row .pr-num {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, #eef1f8, #dbeafe);
    color:#405189; font-weight:700; font-size:.72rem;
    display:inline-flex; align-items:center; justify-content:center;
    transition: all .2s ease;
}
.part-row:hover .pr-num {
    background: linear-gradient(135deg, {{ $colorPrim }}, {{ $colorPrim2 }});
    color: #fff;
}
.part-row .pr-info { min-width: 0; }
.part-row .pr-name {
    display:flex; align-items:center; gap:.55rem;
    font-weight:700; color:#212529; font-size:.88rem;
    position:relative;
}
[data-layout-mode="dark"] .part-row .pr-name { color:#fff; }
.part-row .pr-name .stretched-link { z-index:2; }
.part-row .cuotas-stack { position:relative; z-index:2; }
.part-row .deuda-total { position:relative; z-index:2; }
.part-row .wa-link,
.part-row .is-disabled { position:relative; z-index:3; }
.part-row .pr-avatar {
    width:32px; height:32px; border-radius:50%;
    background: linear-gradient(135deg, #405189, #5e72b8);
    color:#fff; font-weight:800; font-size:.68rem;
    display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
    transition: transform .2s ease;
}
.part-row:hover .pr-avatar { transform: scale(1.08); }
.part-row .pr-meta { font-size:.68rem; color:#878a99; margin-top:2px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.part-row .pr-meta span { display:inline-flex; align-items:center; gap:3px; }

.cuotas-stack { display: flex; flex-direction: column; gap: 4px; }
.cuota-mini {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight:600; color:#495057;
    font-size:.75rem;
    padding: .35rem .55rem;
    border-radius: 6px;
    background: #fafbfd;
    border: 1px solid #f1f3f5;
    transition: all .15s ease;
}
.cuota-mini:hover { background: #fff; border-color: #dbeafe; box-shadow: 0 1px 6px -3px rgba(41,156,219,.12); }
[data-layout-mode="dark"] .cuota-mini { background:#1a1d20; border-color:#2a2f34; color:#ced4da; }
[data-layout-mode="dark"] .cuota-mini:hover { border-color:#405189; }
.cuota-mini .dot {
    width:6px; height:6px; min-width:6px;
    border-radius:999px; flex-shrink:0;
    display:inline-block; overflow:hidden;
    line-height:0;
}
.cuota-mini .dot.mat { background:#299cdb; }
.cuota-mini .dot.col { background:#f7b84b; }
.cuota-mini .info {
    display:flex; align-items:center; gap:5px;
    min-width:0; overflow:hidden;
}
.cuota-mini .info .tag {
    font-size:.58rem; padding:.12em .5em; border-radius:3px;
    font-weight:700; letter-spacing:.3px;
    white-space:nowrap; flex-shrink:0;
}
.cuota-mini .info .tag.mat { background:#dbeafe; color:#299cdb; }
.cuota-mini .info .tag.col { background:#f3f5f9; color:#495057; }
[data-layout-mode="dark"] .cuota-mini .info .tag.col { background:#2a2f34; color:#ced4da; }
.cuota-mini .info .fch {
    color:#878a99; font-size:.66rem;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.cuota-mini .right {
    display:flex; align-items:center; gap:5px;
    margin-left:auto; flex-shrink:0;
}
.cuota-mini .right .monto {
    font-weight:700; font-size:.76rem;
    white-space:nowrap;
}
.cuota-mini .right .dias {
    font-size:.6rem; font-weight:700;
    padding:.12em .45em; border-radius:999px;
    white-space:nowrap;
}
.cuota-mini .right .dias.venc { background:#fef2f2; color:#f06548; }
.cuota-mini .right .dias.prox { background:#f0f9ff; color:#299cdb; }

.deuda-total {
    font-size:1rem; font-weight:800; color:{{ $colorPrim }};
    text-align:center;
}
.deuda-total .sub { font-size:.62rem; color:#878a99; font-weight:600; display:block; margin-top:1px; }

.wa-link {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color:#fff;
    border-radius:10px;
    padding:.45rem .75rem;
    font-size:.78rem;
    font-weight:700;
    display:inline-flex; align-items:center; gap:.35rem;
    text-decoration:none;
    transition: all .15s ease;
    box-shadow: 0 4px 10px -4px rgba(37,211,102,.55);
    justify-content:center;
}
.wa-link:hover { color:#fff; transform: translateY(-1px); box-shadow: 0 8px 16px -6px rgba(37,211,102,.7); }
.wa-link.is-disabled { background: #e9ebec; color:#878a99; pointer-events:none; box-shadow:none; }

.empty-state {
    text-align: center; padding: 4rem 1rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px; border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 80px; height: 80px; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff; border-radius: 50%; font-size: 2.5rem; color: #adb5bd;
}

/* Modal Bulk */
#modalBulkWa .modal-dialog { max-width: 720px; }
#modalBulkWa .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(0,0,0,.25); }
#modalBulkWa .modal-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: #fff; border: 0; padding: 1.1rem 1.5rem; position: relative; overflow: hidden;
}
#modalBulkWa .modal-header::after {
    content:""; position:absolute; top:-40px; right:-40px;
    width:160px; height:160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius:50%;
}
#modalBulkWa .modal-header > * { position: relative; z-index: 1; }
.bm-header-icon {
    width:44px; height:44px;
    display:inline-flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3);
    border-radius:12px; font-size:1.25rem;
}
#modalBulkWa .modal-title { color:#fff; font-weight:700; }
#modalBulkWa .modal-subtitle { color: rgba(255,255,255,.85); font-size:.78rem; }
#modalBulkWa .modal-header .btn-close { filter: brightness(0) invert(1); opacity:.85; }
#modalBulkWa .modal-body { padding: 1.5rem; background: #fafbfd; max-height: 70vh; overflow-y: auto; }
[data-layout-mode="dark"] #modalBulkWa .modal-body { background:#1a1d20; }
#modalBulkWa .modal-footer { background:#fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalBulkWa .modal-footer { background:#212529; border-top-color:#2a2f34; }

.bm-msg-box {
    background:#fff;
    border: 1px solid #eef0f4;
    border-radius: 10px;
    padding: .85rem;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .bm-msg-box { background:#1d2125; border-color:#2a2f34; }
.bm-msg-box label { font-size:.65rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.bm-msg-box textarea {
    width:100%;
    border:1px solid #e9ebec;
    border-radius:8px;
    padding:.5rem .65rem;
    font-size:.82rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    color:#495057;
    background:#fafbfd;
    min-height: 110px;
}
[data-layout-mode="dark"] .bm-msg-box textarea { background:#1a1d20; border-color:#2a2f34; color:#ced4da; }

.bm-part-item {
    display: grid;
    grid-template-columns: 36px 1fr auto;
    gap: .65rem;
    align-items: center;
    padding: .55rem .75rem;
    border-bottom: 1px solid #f1f3f5;
    background: #fff;
}
[data-layout-mode="dark"] .bm-part-item { background:#1d2125; border-bottom-color:#2a2f34; }
.bm-part-item:first-child { border-top-left-radius: 10px; border-top-right-radius: 10px; }
.bm-part-item:last-child { border-bottom: 0; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; }
.bm-part-item .pi-avatar {
    width:32px; height:32px; border-radius:50%;
    background: linear-gradient(135deg, #405189, #5e72b8);
    color:#fff; font-weight:800; font-size:.7rem;
    display:inline-flex; align-items:center; justify-content:center;
}
.bm-part-item .pi-nm { font-weight:700; color:#212529; font-size:.85rem; }
[data-layout-mode="dark"] .bm-part-item .pi-nm { color:#fff; }
.bm-part-item .pi-ph { font-size:.7rem; color:#878a99; }
.bm-part-item .pi-st {
    font-size:.65rem;
    font-weight:700;
    padding:.15em .55em;
    border-radius:999px;
    background:#eef0f4; color:#495057;
}
.bm-part-item .pi-st.sent { background:#d1fae5; color:#0ab39c; }
.bm-part-item .pi-st.skip { background:#fef2f2; color:#f06548; }

.bm-list-wrap {
    border: 1px solid #eef0f4;
    border-radius: 10px;
    overflow: hidden;
}
[data-layout-mode="dark"] .bm-list-wrap { border-color:#2a2f34; }

/* ===== Collapsible program cards ===== */
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

.prog-search-input {
    border:1px solid #e9ebec; border-radius:8px;
    padding:.35rem .75rem; font-size:.78rem;
    min-width:200px; background:#fff; color:#495057;
    transition: border-color .15s ease;
}
.prog-search-input:focus { outline:0; border-color:{{ $colorPrim }}; box-shadow: 0 0 0 .15rem rgba(240,101,72,.15); }
[data-layout-mode="dark"] .prog-search-input { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

/* Dados de baja: en rojo, sin sumar al total ni recibir recordatorios */
.part-row.part-baja { background:#fff1f1; }
.part-row.part-baja::before { background:#dc3545 !important; }
.part-row.part-baja .pr-name, .part-row.part-baja .deuda-total { color:#b42318; }
.part-row.part-baja .pr-avatar, .part-row.part-baja .pr-num { background:#dc3545 !important; color:#fff !important; }
.part-row.part-baja .cuota-mini { opacity:.7; }
[data-layout-mode="dark"] .part-row.part-baja { background:#3a1d20; }
.baja-tag { display:inline-flex; align-items:center; gap:3px; padding:0 .45rem; border-radius:999px; font-weight:700; color:#fff !important; background:#dc3545; }
.baja-hint { padding:.5rem .85rem; border-radius:10px; font-size:.8rem; color:#b42318; background:#fff1f1; border:1px solid #f6caca; }
[data-layout-mode="dark"] .baja-hint { background:#3a1d20; border-color:#6b2a2a; color:#f19a9a; }
.bm-part-item.bm-baja { background:#fff1f1; }
.bm-part-item.bm-baja .pi-nm { color:#b42318; }

/* QR de pago para los mensajes */
.qr-bar { display:flex; align-items:center; gap:.85rem; background:#fff; border:1px solid #e1e9f6; border-left:4px solid #405189; border-radius:14px; padding:.75rem 1rem; margin-bottom:1rem; box-shadow:0 10px 26px -22px rgba(64,81,137,.6); }
[data-layout-mode="dark"] .qr-bar { background:#1d2125; border-color:#2a2f34; }
.qr-bar-ic { width:42px; height:42px; border-radius:12px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; font-size:1.3rem; color:#405189; background:#eef1f8; }
.qr-bar-main { flex:1; min-width:0; }
.qr-bar-l { display:block; font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.45px; color:#6c757d; margin-bottom:.3rem; }
.qr-bar-row { display:flex; flex-wrap:wrap; align-items:center; gap:.6rem; }
.qr-bar-row .form-select { max-width:420px; flex:1 1 260px; border-radius:10px; }
.qr-bar-chk { display:inline-flex; align-items:center; gap:.35rem; font-size:.8rem; font-weight:600; color:#495057; margin:0; white-space:nowrap; }
.qr-bar-help { font-size:.72rem; color:#6b7280; margin-top:.3rem; }
.qr-bar-none { font-size:.82rem; color:#b54708; }
.qr-bar-thumb { width:64px; height:64px; flex-shrink:0; border-radius:10px; border:1px solid #e3e6ea; overflow:hidden; background:#fff; }
.qr-bar-thumb img { width:100%; height:100%; object-fit:contain; }
.bm-qr-info { font-size:.8rem; padding:.45rem .7rem; border-radius:10px; background:#eef1f8; color:#405189; margin-bottom:.75rem; }
@media (max-width: 575.98px) { .qr-bar { flex-wrap:wrap; } .qr-bar-thumb { order:3; } }
</style>
@endpush

@push('css-after')
<link href="{{ asset('backend/assets/css/avip-contable.css') }}?v={{ @filemtime(public_path('backend/assets/css/avip-contable.css')) ?: time() }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="cont-page">

<div class="rep-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="{{ $heroIcon }}"></i></div>
        <div>
            <h4>{{ $titulo }}</h4>
            <p class="hero-sub">{{ $subtitulo }}</p>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Contable</a></li>
        <li class="breadcrumb-item active">{{ $titulo }}</li>
    </ol>
</div>

{{-- Filtro --}}
<div class="card filter-card">
    <div class="card-body">
        <form method="GET" action="{{ $esVencidas ? route('admin.contable.cuotas-vencidas') : route('admin.contable.cuotas-proximas') }}" class="d-flex align-items-end flex-wrap gap-3">
            <div>
                <label><i class="ri-calendar-2-line"></i> Gestión</label>
                <select name="gestion" onchange="this.form.submit()">
                    @foreach($gestiones as $g)
                        <option value="{{ $g }}" @if((int)$g === (int)$gestion) selected @endif>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$esVencidas)
            <div>
                <label><i class="ri-time-line"></i> Días</label>
                <select name="dias" onchange="this.form.submit()">
                    @foreach([7, 15, 30, 45, 60, 90] as $d)
                        <option value="{{ $d }}" @if($dias === $d) selected @endif>{{ $d }} días</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label><i class="ri-graduation-cap-line"></i> Programa</label>
                <select name="programa_id" onchange="this.form.submit()">
                    <option value="">Todos los programas</option>
                    @foreach($programasFiltro as $pf)
                        <option value="{{ $pf->id }}" @if((int)$programaFiltro === (int)$pf->id) selected @endif>{{ $pf->nombre }} ({{ $pf->codigo }})</option>
                    @endforeach
                </select>
            </div>
            @if($programaFiltro)
                <a href="{{ $esVencidas ? route('admin.contable.cuotas-vencidas', ['gestion' => $gestion]) : route('admin.contable.cuotas-proximas', ['gestion' => $gestion, 'dias' => $dias]) }}" class="btn btn-sm btn-soft-danger">
                    <i class="ri-filter-off-line"></i> Quitar filtro de programa
                </a>
            @endif
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-2 mb-3">
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:{{ $colorPrim }}; --k-bg:#fef2f2; --k-color:{{ $colorPrim }};">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Cuotas</div><div class="k-val">{{ $totales['total_cuotas'] }}</div></div>
                <span class="k-ic"><i class="{{ $heroIcon }}"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#405189; --k-bg:#eef1f8; --k-color:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Participantes</div><div class="k-val">{{ $totales['total_participantes'] }}</div></div>
                <span class="k-ic"><i class="ri-group-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#299cdb; --k-bg:#f0f9ff; --k-color:#299cdb;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Programas</div><div class="k-val">{{ $totales['total_programas'] }}</div></div>
                <span class="k-ic"><i class="ri-graduation-cap-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c; --k-bg:#ecfdf5; --k-color:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Adeudado vigentes</div><div class="k-val">Bs {{ number_format($totales['total_deuda'], 2) }}</div></div>
                <span class="k-ic"><i class="ri-money-dollar-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="kpi" style="--k-accent:#dc3545; --k-bg:#fff1f1; --k-color:#dc3545;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="k-lbl">De baja (no se considera)</div>
                    <div class="k-val" style="color:#b42318;">Bs {{ number_format($totales['bajas_deuda'], 2) }}</div>
                    <div class="fs-11 text-muted">{{ $totales['bajas_participantes'] }} participante(s) · {{ $totales['bajas_cuotas'] }} cuota(s)</div>
                </div>
                <span class="k-ic"><i class="ri-user-unfollow-line"></i></span>
            </div>
        </div>
    </div>
</div>
@if($totales['bajas_participantes'] > 0)
    <div class="baja-hint mb-3">
        <i class="ri-information-line"></i>
        Total general (vigentes + de baja): <b>Bs {{ number_format($totales['total_deuda_general'], 2) }}</b>.
        Los participantes dados de baja se listan en rojo al final de cada programa y <b>no reciben recordatorios</b>.
    </div>
@endif

@if(count($porPrograma) === 0)
    <div class="empty-state">
        <div class="empty-icon"><i class="{{ $heroIcon }}"></i></div>
        <h5 class="text-dark fw-bold mb-1">{{ $esVencidas ? 'No hay cuotas vencidas' : 'No hay cuotas próximas' }}</h5>
        <p class="text-muted mb-0">
            @if($esVencidas)
                Todos los participantes están al día con sus pagos en la gestión {{ $gestion }}.
            @else
                Ningún participante tiene cuotas con vencimiento en los próximos {{ $dias }} días.
            @endif
        </p>
    </div>
@else

{{-- QR de pago para adjuntar en los mensajes (solo vigentes) --}}
<div class="qr-bar">
    <div class="qr-bar-ic"><i class="ri-qr-code-line"></i></div>
    <div class="qr-bar-main">
        <label class="qr-bar-l" for="qrSel">QR de pago para los mensajes</label>
        @if($qrs->isEmpty())
            <div class="qr-bar-none">No hay QR vigentes.
                @if(auth()->user()->hasAnyRole(['Super Admin', 'Contabilidad']))<a href="{{ route('admin.contable.qr.index') }}">Registrá uno en «QR y cuentas bancarias»</a>.@endif
            </div>
        @else
            <div class="qr-bar-row">
                <select id="qrSel" class="form-select form-select-sm">
                    <option value="">Sin QR (solo el mensaje)</option>
                    @foreach($qrs as $q)
                        <option value="{{ $q->id }}" data-img="{{ $q->imagen_url }}" data-banco="{{ $q->banco }}"
                            data-vence="{{ \Carbon\Carbon::parse($q->fecha_vencimiento)->format('d/m/Y') }}">{{ $q->nombre }} · {{ $q->banco }}{{ $q->estado === 'por_vencer' ? ' (vence '.\Carbon\Carbon::parse($q->fecha_vencimiento)->format('d/m').')' : '' }}</option>
                    @endforeach
                </select>
                <label class="qr-bar-chk"><input type="checkbox" id="qrDatos" class="form-check-input" checked> Incluir datos de la cuenta</label>
            </div>
            <div class="qr-bar-help" id="qrHelp">Se adjunta la imagen del QR y el recordatorio va como texto de la imagen. Solo se listan los QR vigentes.</div>
        @endif
    </div>
    <a class="qr-bar-thumb" id="qrThumb" href="#" target="_blank" rel="noopener" hidden title="Ver el QR"><img alt="QR elegido"></a>
</div>

{{-- Toolbar: colapsar/expandir + búsqueda --}}
@if(count($porPrograma) > 1)
<div class="collapse-toolbar">
    <button type="button" class="btn-collapse-toggle" data-action="collapse-all"><i class="ri-contract-up-down-line"></i> Colapsar todo</button>
    <button type="button" class="btn-collapse-toggle" data-action="expand-all"><i class="ri-expand-up-down-line"></i> Expandir todo</button>
    <input type="text" class="prog-search-input" placeholder="Buscar programa..." data-prog-search>
</div>
@endif

@foreach($porPrograma as $pg)
@php
    $bulkParts = $pg->participantes->map(function ($p) {
        return [
            'id' => $p->participante_id,
            'ci' => $p->ci,
            'nombre' => $p->apellidos . ', ' . $p->nombres,
            'celular' => $p->celular,
            'baja' => $p->baja,
            'total_saldo' => $p->total_saldo,
            'cuotas' => $p->cuotas->map(fn($c) => [
                'tipo' => $c->tipo,
                'fecha' => $c->fecha_pago,
                'dias' => $c->dias,
                'saldo' => $c->saldo,
            ])->values(),
        ];
    })->values();
@endphp
<div class="prog-block prog-card" data-prog-card>
    <div class="prog-block-head prog-card-head" data-toggle-prog>
        <span class="prog-icon-big"><i class="ri-graduation-cap-line"></i></span>
        <div class="prog-info">
            <div class="nm">{{ $pg->programa_nombre }}</div>
            <div class="me">
                <i class="ri-hashtag"></i> {{ $pg->programa_codigo }} ·
                <i class="ri-price-tag-3-line"></i> {{ $pg->tipo_nombre ?? 'Sin tipo' }} ·
                <i class="ri-calendar-2-line"></i> Gestión {{ $pg->programa_gestion }} ·
                <i class="ri-group-line"></i> {{ $pg->vigentes_participantes }} participante(s) ·
                <i class="{{ $heroIcon }}"></i> {{ $pg->total_cuotas }} cuota(s)
                @if($pg->bajas_participantes > 0)
                    · <span class="text-danger fw-semibold"><i class="ri-user-unfollow-line"></i> {{ $pg->bajas_participantes }} de baja</span>
                @endif
            </div>
        </div>
        <div class="prog-totales">
            <div class="tt">Bs {{ number_format($pg->total_saldo, 2) }}</div>
            <div class="ts">adeudado vigentes</div>
            @if($pg->bajas_participantes > 0)
                <div class="ts text-danger fw-semibold">+ Bs {{ number_format($pg->bajas_saldo, 2) }} de baja</div>
            @endif
        </div>
        <button type="button" class="btn-wa-bulk btn-open-bulk"
                data-programa-id="{{ $pg->programa_id }}"
                data-dias="{{ $dias }}"
                data-programa-nombre="{{ $pg->programa_nombre }}"
                data-programa-codigo="{{ $pg->programa_codigo }}"
                data-modo="{{ $modo }}"
                data-participantes='@json($bulkParts)'>
            <i class="ri-whatsapp-line"></i> WhatsApp masivo
        </button>
        <span class="collapse-toggle"><i class="ri-arrow-down-s-line"></i></span>
    </div>

    <div class="prog-card-body" data-prog-body>
    @php $nroVig = 0; @endphp
    @foreach($pg->participantes as $pa)
    <div class="part-row {{ $pa->baja ? 'part-baja' : '' }}">
        <div class="text-center"><span class="pr-num">{{ $pa->baja ? '—' : ++$nroVig }}</span></div>
        <div class="pr-info">
            <div class="pr-name">
                <span class="pr-avatar">{{ strtoupper(substr($pa->nombres, 0, 1) . substr($pa->apellidos, 0, 1)) }}</span>
                <a href="{{ route('admin.programas.participante-detalle', $pa->participante_id) }}" class="text-reset text-decoration-none stretched-link">
                    {{ $pa->apellidos }}, {{ $pa->nombres }}
                </a>
            </div>
            <div class="pr-meta">
                <span><i class="ri-passport-line"></i> {{ $pa->ci }}</span>
                @if($pa->celular)
                    <span><i class="ri-phone-line"></i> {{ $pa->celular }}</span>
                @endif
                @if($pa->baja)
                    <span class="baja-tag"><i class="ri-user-unfollow-line"></i> {{ $pa->baja }} · no se considera</span>
                @endif
            </div>
        </div>
        <div class="cuotas-stack">
            @foreach($pa->cuotas as $c)
                @php $fp = \Carbon\Carbon::parse($c->fecha_pago); @endphp
                <div class="cuota-mini">
                    <span class="dot {{ $c->tipo === 'Matricula' ? 'mat' : 'col' }}"></span>
                    <div class="info">
                        <span class="tag {{ $c->tipo === 'Matricula' ? 'mat' : 'col' }}">{{ $c->tipo === 'Matricula' ? 'MAT' : 'COL' }}</span>
                        <span class="fch">{{ $fp->format('d/m/Y') }}</span>
                    </div>
                    <div class="right">
                        <span class="monto">Bs {{ number_format($c->saldo, 2) }}</span>
                        @if($esVencidas)
                            <span class="dias venc">{{ abs($c->dias) }}d</span>
                        @else
                            <span class="dias prox">{{ $c->dias }}d</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="deuda-total">
            Bs {{ number_format($pa->total_saldo, 2) }}
            <span class="sub">{{ $pa->count }} cuota(s)</span>
        </div>
        <div class="text-center">
            @php
                $tieneCel = trim((string)$pa->celular) !== '';
                $waDataPart = [
                    'id' => $pa->participante_id,
                    'nombre' => $pa->apellidos . ', ' . $pa->nombres,
                    'celular' => $pa->celular,
                    'programa' => $pg->programa_nombre,
                    'codigo' => $pg->programa_codigo,
                    'total' => $pa->total_saldo,
                    'cuotas' => $pa->cuotas->map(fn($c) => [
                        'tipo' => $c->tipo,
                        'fecha' => $c->fecha_pago,
                        'dias' => $c->dias,
                        'saldo' => $c->saldo,
                    ])->values(),
                    'modo' => $modo,
                ];
            @endphp
            @if($pa->baja)
                <span class="wa-link is-disabled" title="Dado de baja: no recibe recordatorios">
                    <i class="ri-user-unfollow-line"></i> De baja
                </span>
            @elseif($tieneCel)
                <a href="#" class="wa-link btn-wa-individual"
                   data-programa-id="{{ $pg->programa_id }}" data-dias="{{ $dias }}"
                   data-participante='@json($waDataPart)'>
                    <i class="ri-whatsapp-line"></i> Enviar
                </a>
            @else
                <span class="wa-link is-disabled" title="Sin celular registrado">
                    <i class="ri-phone-off-line"></i> Sin tel.
                </span>
            @endif
        </div>
    </div>
    @endforeach
    </div>
</div>
@endforeach

@endif

{{-- Modal Bulk --}}
<div class="modal fade" id="modalBulkWa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="bm-header-icon"><i class="ri-whatsapp-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Envío masivo por WhatsApp</h5>
                        <small class="modal-subtitle" id="bmSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-soft-info d-flex align-items-start gap-2 mb-3 py-2 fs-13">
                    <i class="ri-information-line fs-16"></i>
                    <div>
                        El envío se realiza por <b>Seguimiento ESAM</b> de forma automática y en segundo plano (con pausas anti-baneo).
                        El mensaje se personaliza con el nombre, las cuotas y el total adeudado de cada participante.
                    </div>
                </div>

                <div class="bm-qr-info" id="bmQrInfo"></div>
                <div class="bm-msg-box">
                    <label class="mb-1"><i class="ri-message-2-line"></i> Plantilla del mensaje</label>
                    <textarea id="bmTemplate"></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Destinatarios <span class="text-muted" id="bmCount">(0)</span></strong>
                    <small class="text-muted" id="bmSinTel"></small>
                </div>
                <div class="bm-list-wrap" id="bmList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success" id="btnBulkEnviar">
                    <i class="ri-whatsapp-line"></i> Enviar a todos
                </button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /cont-page --}}
@endsection

@push('js')
@include('admin.partials.whatsapp-lote-modal')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ES_VENCIDAS = {{ $esVencidas ? 'true' : 'false' }};
    const dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    function normalizePhone(raw) {
        if (!raw) return '';
        let d = String(raw).replace(/[^0-9+]/g, '').replace(/^\+/, '');
        // Bolivia: si tiene 8 digitos, prepend 591
        if (/^\d{8}$/.test(d)) d = '591' + d;
        return d;
    }

    function fmt(n) { return 'Bs ' + Number(n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function fechaLarga(iso) {
        const d = new Date(iso + 'T00:00:00');
        if (isNaN(d)) return iso;
        return dias[d.getDay()] + ' ' + d.getDate() + ' de ' + meses[d.getMonth()] + ' de ' + d.getFullYear();
    }

    function buildMensaje(p) {
        // p: { nombre, programa, codigo, total, cuotas[], modo }
        const intro = ES_VENCIDAS
            ? 'Le informamos que tiene cuotas VENCIDAS pendientes en el programa:'
            : 'Le recordamos que tiene próximas cuotas a pagar en el programa:';
        const cierre = ES_VENCIDAS
            ? 'Por favor regularice su situación a la brevedad. Si ya realizó el pago ignore este mensaje. Gracias.'
            : 'Le agradecemos su pago oportuno. Saludos cordiales.';

        let lineas = '*Estimado/a ' + p.nombre + '*,\n\n' + intro + '\n';
        lineas += '🎓 *' + p.programa + '* (' + p.codigo + ')\n\n';
        lineas += '📋 *Detalle de cuotas:*\n';
        p.cuotas.forEach(function (c) {
            const tipo = c.tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad';
            const f = fechaLarga(c.fecha);
            const det = ES_VENCIDAS
                ? '⚠️ Vencida hace ' + Math.abs(c.dias) + ' día(s)'
                : (c.dias === 0 ? '⏰ Vence HOY' : '⏳ En ' + c.dias + ' día(s)');
            lineas += '• ' + tipo + ' — ' + f + ' — ' + fmt(c.saldo) + ' — ' + det + '\n';
        });
        lineas += '\n💰 *Total adeudado: ' + fmt(p.total) + '*\n\n' + cierre;
        return lineas;
    }

    function buildBulkTemplate() {
        const intro = ES_VENCIDAS
            ? 'Le informamos que tiene cuotas VENCIDAS pendientes.'
            : 'Le recordamos sus próximas cuotas a pagar.';
        const cierre = ES_VENCIDAS
            ? 'Por favor regularice su situación a la brevedad. Si ya realizó el pago ignore este mensaje. Gracias.'
            : 'Le agradecemos su pago oportuno. Saludos cordiales.';
        return '*Estimado/a {nombre}*,\n\n' + intro + '\n' +
               '🎓 *{programa}* ({codigo})\n\n' +
               '{detalle_cuotas}\n' +
               '💰 *Total adeudado: {total}*\n\n' + cierre;
    }

    // QR de pago elegido (vacío = sin QR).
    function qrElegido() {
        const sel = document.getElementById('qrSel');
        return sel ? sel.value : '';
    }
    function qrNombre() {
        const sel = document.getElementById('qrSel');
        return sel && sel.value ? sel.options[sel.selectedIndex].text : '';
    }
    const qrSel = document.getElementById('qrSel');
    if (qrSel) {
        qrSel.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const thumb = document.getElementById('qrThumb');
            const datos = document.getElementById('qrDatos');
            if (this.value) {
                thumb.href = opt.dataset.img;
                thumb.querySelector('img').src = opt.dataset.img;
                thumb.hidden = false;
                document.getElementById('qrHelp').textContent = 'Se adjunta el QR «' + opt.text + '» (vence ' + opt.dataset.vence + '). El recordatorio va como texto de la imagen.';
            } else {
                thumb.hidden = true;
                document.getElementById('qrHelp').textContent = 'Se adjunta la imagen del QR y el recordatorio va como texto de la imagen. Solo se listan los QR vigentes.';
            }
            if (datos) datos.disabled = !this.value;
            const bm = document.getElementById('bmQrInfo');
            if (bm) bm.innerHTML = qrResumen();
        });
        qrSel.dispatchEvent(new Event('change'));
    }
    function qrResumen() {
        return qrElegido()
            ? '<i class="ri-qr-code-line"></i> Con QR: <b>' + qrNombre().replace(/[<>&]/g, '') + '</b>' + ((document.getElementById('qrDatos') || {}).checked ? ' + datos de la cuenta' : '')
            : '<i class="ri-image-line"></i> Sin QR: solo el mensaje.';
    }

    // Envía por la API de Seguimiento ESAM (sin abrir pestañas).
    async function enviarEsam(programaId, dias, modo, participanteId, trigger, reenviar) {
        if (!reenviar && !confirm('¿Enviar por WhatsApp con Seguimiento ESAM desde tu línea?' + (qrElegido() ? '\n\nSe adjunta el QR: ' + qrNombre() : ''))) return;
        if (trigger) trigger.classList.add('is-loading');
        try {
            const res = await fetch('{{ route('admin.contable.whatsapp.enviar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    programa_id: programaId,
                    modo: modo,
                    dias: dias,
                    participante_id: participanteId || null,
                    reenviar: !!reenviar,
                    qr_id: qrElegido() || null,
                    qr_datos: !!(document.getElementById('qrDatos') || {}).checked,
                }),
            });
            const data = await res.json().catch(function () { return {}; });
            if (res.ok && data.success && data.batch_id) {
                var extra = (data.sin_telefono > 0) ? (' · sin teléfono: ' + data.sin_telefono) : '';
                EsamLote.abrir(data.batch_id, 'Lote #' + data.batch_id + ' · ' + data.total + ' mensaje(s)' + extra);
            } else if (res.status === 409 && data.duplicado) {
                // Ya se envió hoy: se explica desde dónde y se ofrece reenviar a propósito.
                if (confirm(data.message + '\n\n¿Querés reenviarlos igual desde tu línea?')) {
                    if (trigger) trigger.classList.remove('is-loading');
                    return enviarEsam(programaId, dias, modo, participanteId, trigger, true);
                }
            } else {
                alert(data.message || 'No se pudo enviar: Seguimiento ESAM no creó el envío.');
            }
        } catch (err) {
            alert('Error: ' + err.message);
        } finally {
            if (trigger) trigger.classList.remove('is-loading');
        }
    }

    // Botón individual
    document.querySelectorAll('.btn-wa-individual').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            const p = JSON.parse(this.dataset.participante);
            if (!normalizePhone(p.celular)) { alert('Sin celular válido'); return; }
            enviarEsam(this.dataset.programaId, this.dataset.dias, p.modo, p.id, this);
        });
    });

    // Bulk
    let bulkData = null;
    let bulkSentIds = new Set();
    const modalBulk = new bootstrap.Modal(document.getElementById('modalBulkWa'));

    document.querySelectorAll('.btn-open-bulk').forEach(function (btn) {
        btn.addEventListener('click', function () {
            bulkData = {
                programaId: this.dataset.programaId,
                dias: this.dataset.dias,
                programa: this.dataset.programaNombre,
                codigo: this.dataset.programaCodigo,
                modo: this.dataset.modo,
                participantes: JSON.parse(this.dataset.participantes),
            };
            bulkSentIds = new Set();
            document.getElementById('bmSubtitle').textContent = bulkData.programa + ' (' + bulkData.codigo + ')';
            document.getElementById('bmTemplate').value = buildBulkTemplate();
            document.getElementById('bmQrInfo').innerHTML = qrResumen();
            renderBulkList();
            modalBulk.show();
        });
    });

    function renderBulkList() {
        const list = document.getElementById('bmList');
        // Los dados de baja se muestran pero no reciben el recordatorio.
        const vigentes = bulkData.participantes.filter(p => !p.baja);
        const con = vigentes.filter(p => normalizePhone(p.celular));
        const sin = vigentes.length - con.length;
        const nBajas = bulkData.participantes.length - vigentes.length;
        document.getElementById('bmCount').textContent = '(' + con.length + ')';
        document.getElementById('bmSinTel').textContent = (sin > 0 ? ('· ' + sin + ' sin celular') : '') + (nBajas > 0 ? (' · ' + nBajas + ' de baja (no se envía)') : '');

        let html = '';
        bulkData.participantes.forEach(function (p) {
            const phone = normalizePhone(p.celular);
            const ini = p.nombre.split(',').map(s => (s.trim().charAt(0) || '').toUpperCase()).slice(0,2).join('');
            const stCls = p.baja ? 'skip' : (bulkSentIds.has(p.id) ? 'sent' : (phone ? '' : 'skip'));
            const stTxt = p.baja ? 'De baja · no se envía' : (bulkSentIds.has(p.id) ? '✓ Enviado' : (phone ? 'Pendiente' : 'Sin tel.'));
            html +=
                '<div class="bm-part-item' + (p.baja ? ' bm-baja' : '') + '">' +
                    '<span class="pi-avatar">' + ini + '</span>' +
                    '<div>' +
                        '<div class="pi-nm">' + p.nombre + '</div>' +
                        '<div class="pi-ph"><i class="ri-phone-line"></i> ' + (p.celular || 'sin teléfono') + ' · ' + fmt(p.total_saldo) + '</div>' +
                    '</div>' +
                    '<span class="pi-st ' + stCls + '">' + stTxt + '</span>' +
                '</div>';
        });
        list.innerHTML = html;
    }

    document.getElementById('btnBulkEnviar').addEventListener('click', function () {
        if (!bulkData) return;
        const conTel = bulkData.participantes.filter(function (p) { return !p.baja && normalizePhone(p.celular); });
        if (conTel.length === 0) { alert('Ningún participante vigente tiene teléfono válido'); return; }
        enviarEsam(bulkData.programaId, bulkData.dias, bulkData.modo, null, this);
    });

    // ===== Collapse / Expand program cards =====
    function collapseProgBody(body) {
        if (!body || body.classList.contains('collapsed')) return;
        body.style.maxHeight = body.scrollHeight + 'px';
        requestAnimationFrame(function () {
            body.classList.add('collapsed');
            body.style.maxHeight = '';
        });
        var toggleIcon = body.closest('[data-prog-card]')?.querySelector('.collapse-toggle');
        if (toggleIcon) toggleIcon.classList.add('collapsed');
    }

    function expandProgBody(body) {
        if (!body || !body.classList.contains('collapsed')) return;
        body.classList.remove('collapsed');
        body.style.maxHeight = body.scrollHeight + 'px';
        var toggleIcon = body.closest('[data-prog-card]')?.querySelector('.collapse-toggle');
        if (toggleIcon) toggleIcon.classList.remove('collapsed');
        body.addEventListener('transitionend', function handler() {
            body.style.maxHeight = '';
            body.removeEventListener('transitionend', handler);
        });
    }

    document.querySelectorAll('[data-prog-card]').forEach(function (card) {
        var body = card.querySelector('[data-prog-body]');
        if (body) collapseProgBody(body);
    });

    document.addEventListener('click', function (e) {
        var head = e.target.closest('[data-toggle-prog]');
        if (!head) return;
        if (e.target.closest('button')) return;
        var card = head.closest('[data-prog-card]');
        if (!card) return;
        var body = card.querySelector('[data-prog-body]');
        if (!body) return;
        if (body.classList.contains('collapsed')) {
            expandProgBody(body);
        } else {
            collapseProgBody(body);
        }
    });

    document.querySelector('[data-action="collapse-all"]')?.addEventListener('click', function () {
        document.querySelectorAll('[data-prog-body]').forEach(function (body) {
            collapseProgBody(body);
        });
    });

    document.querySelector('[data-action="expand-all"]')?.addEventListener('click', function () {
        document.querySelectorAll('[data-prog-body]').forEach(function (body) {
            expandProgBody(body);
        });
    });

    document.querySelector('[data-prog-search]')?.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('[data-prog-card]').forEach(function (card) {
            var name = card.querySelector('.nm')?.textContent?.toLowerCase() || '';
            var code = card.querySelector('.me')?.textContent?.toLowerCase() || '';
            card.style.display = (q === '' || name.includes(q) || code.includes(q)) ? '' : 'none';
        });
    });
});
</script>
@endpush
