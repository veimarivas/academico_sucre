@extends('admin.layouts.master')

@section('title', $docente->apellidos . ', ' . $docente->nombres . ' - ' . config('app.name'))

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
    font-weight: 700;
    font-size: 1.75rem;
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
    font-size: .72rem;
    font-weight: 600;
    padding: .35em .75em;
    border-radius: 999px;
    backdrop-filter: blur(4px);
    display: inline-flex; align-items: center; gap: .35rem;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.btn-volver {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    font-weight: 600;
    backdrop-filter: blur(4px);
    transition: all .2s ease;
}
.btn-volver:hover { background: rgba(255,255,255,.28); color: #fff; transform: translateY(-1px); }

/* Hero stats */
.hero-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 12px;
    padding: .85rem 1rem;
    backdrop-filter: blur(4px);
    transition: all .2s ease;
    text-align: center;
}
.hero-stat:hover { background: rgba(255,255,255,.18); transform: translateY(-2px); }
.hero-stat .stat-val { color: #fff; font-weight: 700; font-size: 1.4rem; line-height: 1.1; }
.hero-stat .stat-lbl { color: rgba(255,255,255,.8); font-size: .68rem; text-transform: uppercase; letter-spacing: .4px; font-weight: 600; margin-top: 2px; }

/* ===== Tabs Pill ===== */
.show-tabs-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    background: #fff;
}
[data-layout-mode="dark"] .show-tabs-card { background: #212529; border-color: #2a2f34; }
.show-tabs-card .card-body { padding: 1.25rem; }

.nav-tabs-pill {
    border: 0;
    gap: .35rem;
    background: #f3f5f9;
    padding: .4rem;
    border-radius: 12px;
    display: inline-flex;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
[data-layout-mode="dark"] .nav-tabs-pill { background: #1a1d20; }
.nav-tabs-pill .nav-link {
    border: 0 !important;
    border-radius: 9px;
    color: #495057;
    font-weight: 600;
    font-size: .82rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: all .2s ease;
    display: inline-flex; align-items: center; gap: .35rem;
    background: transparent;
}
.nav-tabs-pill .nav-link:hover { color: #405189; background: rgba(64,81,137,.06); }
.nav-tabs-pill .nav-link.active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff !important;
    box-shadow: 0 6px 14px -6px rgba(64,81,137,.55);
}
[data-layout-mode="dark"] .nav-tabs-pill .nav-link { color: #ced4da; }

/* ===== Sub-tabs por estado ===== */
.estado-subtabs {
    display: flex; flex-wrap: wrap; gap: .5rem;
    margin-bottom: 1.25rem;
}
.estado-subtab {
    border: 1px solid #eef0f4;
    background: #fff;
    border-radius: 999px;
    padding: .5rem 1rem;
    font-size: .82rem;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all .2s ease;
    display: inline-flex; align-items: center; gap: .45rem;
}
.estado-subtab:hover { border-color: #c3cfe2; background: #f8f9fc; color: #405189; }
.estado-subtab .count-pill {
    background: #eef0f4;
    color: #495057;
    font-size: .7rem;
    font-weight: 700;
    padding: .15em .55em;
    border-radius: 999px;
    transition: all .2s ease;
}
.estado-subtab.active { color: #fff; border-color: transparent; box-shadow: 0 6px 14px -6px rgba(0,0,0,.25); }
.estado-subtab.active.is-dev { background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%); }
.estado-subtab.active.is-end { background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%); }
.estado-subtab.active.is-other { background: linear-gradient(135deg, #878a99 0%, #6c757d 100%); }
.estado-subtab.active .count-pill { background: rgba(255,255,255,.25); color: #fff; }
[data-layout-mode="dark"] .estado-subtab { background: #1d2125; border-color: #2a2f34; color: #ced4da; }
[data-layout-mode="dark"] .estado-subtab:hover { background: #212529; }

.subtab-section { display: none; }
.subtab-section.active { display: block; animation: fadeIn .25s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }

/* Status indicator en program card */
.program-status-bar {
    height: 4px; width: 100%;
    background: linear-gradient(90deg, var(--status-color, #878a99), transparent);
}
.program-status-bar.is-dev { background: linear-gradient(90deg, #299cdb, #3577f1); }
.program-status-bar.is-end { background: linear-gradient(90deg, #0ab39c, #14b8a6); }
.program-status-bar.is-other { background: linear-gradient(90deg, #878a99, #adb5bd); }

.program-estado-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px;
    padding: .25em .65em; border-radius: 999px;
    border: 1px solid;
}
.program-estado-badge.is-dev { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
.program-estado-badge.is-end { background: #d1fae5; color: #047857; border-color: #a7f3d8; }
.program-estado-badge.is-other { background: #f1f3f5; color: #495057; border-color: #e9ebec; }

/* ===== Filtro de módulos por estado dentro del program-card ===== */
.mod-filter-row {
    display: flex; flex-wrap: wrap; gap: .4rem;
    padding: .75rem 1.25rem;
    background: #fafbfd;
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .mod-filter-row { background: #1a1d20; border-bottom-color: #2a2f34; }
.mod-filter-chip {
    border: 1px solid #e9ebec;
    background: #fff;
    color: #495057;
    border-radius: 999px;
    padding: .3rem .7rem;
    font-size: .72rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .3rem;
}
.mod-filter-chip:hover { border-color: #c3cfe2; color: #405189; }
.mod-filter-chip .chip-count {
    background: #eef0f4;
    color: #495057;
    font-size: .65rem;
    font-weight: 700;
    padding: .1em .5em;
    border-radius: 999px;
}
.mod-filter-chip.active { color: #fff; border-color: transparent; box-shadow: 0 4px 10px -4px rgba(0,0,0,.2); }
.mod-filter-chip.active .chip-count { background: rgba(255,255,255,.25); color: #fff; }
.mod-filter-chip.active.is-all { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); }
.mod-filter-chip.active.is-dev { background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%); }
.mod-filter-chip.active.is-end { background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%); }
.mod-filter-chip.active.is-no  { background: linear-gradient(135deg, #878a99 0%, #6c757d 100%); }
[data-layout-mode="dark"] .mod-filter-chip { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

.mod-empty-row td {
    text-align: center;
    color: #878a99;
    font-style: italic;
    padding: 1.5rem !important;
}

/* ===== Program cards ===== */
.program-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    overflow: hidden;
    transition: all .2s ease;
    background: #fff;
    margin-bottom: 1rem;
}
.program-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px -12px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .program-card { background: #212529; border-color: #2a2f34; }
.program-card-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .program-card-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.program-icon {
    width: 40px; height: 40px;
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
.program-meta { font-size: .72rem; color: #878a99; margin-top: 2px; }

.progress-wrap { display: flex; align-items: center; gap: .65rem; min-width: 0; }
.progress-bar-wrap { flex-grow: 1; height: 8px; background: #eef0f4; border-radius: 999px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #0ab39c, #299cdb); border-radius: 999px; transition: width .5s ease; }
.progress-pct { font-size: .72rem; font-weight: 700; color: #495057; min-width: 32px; text-align: right; }

.program-badges { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: flex-end; }
.prog-badge {
    font-size: .68rem;
    font-weight: 600;
    padding: .3em .65em;
    border-radius: 999px;
    display: inline-flex; align-items: center; gap: .3rem;
    border: 1px solid;
}
.prog-badge-code { background: #eef1f8; color: #405189; border-color: #c3cfe2; }
.prog-badge-mods { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.prog-badge-pago { background: #fef3c7; color: #d97706; border-color: #fde68a; }

/* Tabla módulos del programa */
.modern-table { margin: 0; width: 100%; }
.modern-table thead th {
    background: #fff !important;
    font-weight: 700;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .75rem 1rem;
}
.modern-table tbody td {
    padding: .75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .85rem;
}
.modern-table tbody tr { transition: background-color .15s ease; }
.modern-table tbody tr:hover { background-color: #f8f9fc; }
.modern-table tbody tr:last-child td { border-bottom: none; }
[data-layout-mode="dark"] .modern-table thead th { background: #1d2125 !important; border-bottom-color: #2a2f34 !important; }
[data-layout-mode="dark"] .modern-table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .modern-table tbody tr:hover { background-color: #1a1d20; }

.estado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 600;
    padding: .25em .65em; border-radius: 999px;
    border: 1px solid currentColor;
}
.estado-pill::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

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

/* ===== Calendar ===== */
#calendar { max-width: 100%; min-height: 500px; }
.fc-event { cursor: pointer; border-radius: 6px; border: 0; padding: 2px 6px; font-weight: 500; }
.fc .fc-toolbar.fc-header-toolbar { margin-bottom: 1.25rem; flex-wrap: wrap; gap: .5rem; }
.fc .fc-toolbar-title { font-size: 1.15rem !important; font-weight: 700; color: #212529; text-transform: capitalize; }
[data-layout-mode="dark"] .fc .fc-toolbar-title { color: #fff; }
.fc .fc-button {
    background: #fff !important;
    border: 1px solid #e9ebec !important;
    color: #495057 !important;
    border-radius: 8px !important;
    padding: .4rem .85rem !important;
    font-size: .82rem !important;
    font-weight: 600 !important;
    box-shadow: none !important;
    transition: all .15s ease;
}
.fc .fc-button:hover { background: #f8f9fc !important; border-color: #405189 !important; color: #405189 !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%) !important;
    border-color: transparent !important;
    color: #fff !important;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.5) !important;
}
[data-layout-mode="dark"] .fc .fc-button { background: #1d2125 !important; border-color: #2a2f34 !important; color: #ced4da !important; }
.fc .fc-col-header-cell {
    background: #f8f9fc;
    text-transform: uppercase;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .4px;
    padding: .65rem 0;
    color: #6c757d;
    border-color: #eef0f4 !important;
}
[data-layout-mode="dark"] .fc .fc-col-header-cell { background: #1a1d20; border-color: #2a2f34 !important; color: #adb5bd; }
.fc .fc-daygrid-day { border-color: #eef0f4 !important; transition: background-color .15s ease; }
.fc .fc-daygrid-day:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .fc .fc-daygrid-day { border-color: #2a2f34 !important; }
.fc .fc-day-today { background-color: rgba(64,81,137,.08) !important; }
.fc .fc-daygrid-day-number { padding: .4rem .55rem; font-weight: 600; color: #495057; }
[data-layout-mode="dark"] .fc .fc-daygrid-day-number { color: #ced4da; }
.fc-day-today .fc-daygrid-day-number {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 50%;
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    margin: .2rem;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}

/* ===== Modal Horario ===== */
#modalHorario .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalHorario .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalHorario .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalHorario .modal-header > * { position: relative; z-index: 1; }
#modalHorario .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalHorario .modal-title { color: #fff; font-weight: 700; }
#modalHorario .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalHorario .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalHorario .modal-body { padding: 1.5rem; background: #fafbfd; max-height: 75vh; overflow-y: auto; }
[data-layout-mode="dark"] #modalHorario .modal-body { background: #1a1d20; }
#modalHorario .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalHorario .modal-footer { background: #212529; border-top-color: #2a2f34; }

.detail-section {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: .85rem;
}
.detail-section:last-child { margin-bottom: 0; }
[data-layout-mode="dark"] .detail-section { background: #212529; border-color: #2a2f34; }
.detail-section-title {
    display: flex; align-items: center; gap: .5rem;
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: #6c757d;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px dashed #eef0f4;
}
[data-layout-mode="dark"] .detail-section-title { color: #adb5bd; border-bottom-color: #2a2f34; }
.detail-section-icon {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px;
    font-size: .95rem;
}
.detail-row { display: flex; gap: .75rem; padding: .35rem 0; align-items: flex-start; }
.detail-row + .detail-row { border-top: 1px solid #f1f3f5; }
[data-layout-mode="dark"] .detail-row + .detail-row { border-top-color: #2a2f34; }
.detail-label {
    flex: 0 0 110px;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .3px;
    font-weight: 600;
    color: #878a99;
    padding-top: 2px;
}
.detail-value { flex: 1; font-size: .88rem; font-weight: 500; color: #212529; min-width: 0; word-break: break-word; }
[data-layout-mode="dark"] .detail-value { color: #fff; }
</style>
@endpush

@section('content')
@php
    $totalPago = $modulos->sum('pago_docente');
    $completados = $modulos->where('desarrollo', 'Desarrollado')->count();
    $enDesarrollo = $modulos->where('desarrollo', 'En desarrollo')->count();
    $ini = strtoupper(substr($docente->nombres, 0, 1) . substr($docente->apellidos, 0, 1));

    // Normaliza el estado del módulo en 4 grupos
    $modKey = function ($d) {
        $d = strtolower((string) $d);
        if (str_contains($d, 'conclui') || str_contains($d, 'desarrollado')) return 'end';
        if (str_contains($d, 'en desarrollo')) return 'dev';
        if (str_contains($d, 'no inicio')) return 'no';
        return 'otro';
    };

    // Agrupa los módulos por estado
    $modsPorEstado = [
        'no'  => $modulos->filter(fn($m) => $modKey($m->desarrollo) === 'no')->values(),
        'dev' => $modulos->filter(fn($m) => $modKey($m->desarrollo) === 'dev')->values(),
        'end' => $modulos->filter(fn($m) => $modKey($m->desarrollo) === 'end')->values(),
    ];
@endphp

<div class="show-hero">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar-lg">{{ $ini }}</div>
                <div style="min-width:0;">
                    <h3 class="fs-22 mb-1">{{ $docente->apellidos }}, {{ $docente->nombres }}</h3>
                    <div class="hero-info-pills">
                        <span class="hero-info-pill"><i class="ri-passport-line"></i>CI: {{ $docente->ci }}</span>
                        @if($docente->celular)
                            <span class="hero-info-pill"><i class="ri-phone-line"></i>{{ $docente->celular }}</span>
                        @endif
                        @if($docente->correo)
                            <span class="hero-info-pill" title="{{ $docente->correo }}"><i class="ri-mail-line"></i>{{ $docente->correo }}</span>
                        @endif
                        @if($docente->usuario)
                            <span class="hero-info-pill"><i class="ri-user-3-line"></i>{{ $docente->usuario }}</span>
                        @endif
                        @if($docente->banco)
                            <span class="hero-info-pill"><i class="ri-bank-line"></i>{{ $docente->banco }}@if($docente->n_cuenta) · {{ $docente->n_cuenta }}@endif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-2">
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ count($programas) }}</div><div class="stat-lbl">Programas</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $modulos->count() }}</div><div class="stat-lbl">Módulos</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ $completados + $enDesarrollo }}</div><div class="stat-lbl">Activos</div></div></div>
                <div class="col-3"><div class="hero-stat"><div class="stat-val">{{ number_format($totalPago, 0) }}</div><div class="stat-lbl">Pagos (Bs)</div></div></div>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex justify-content-end">
        <a href="{{ route('admin.docentes.index') }}" class="btn btn-volver d-inline-flex align-items-center gap-1">
            <i class="ri-arrow-left-line"></i> Volver a Docentes
        </a>
    </div>
</div>

{{-- Tabs --}}
<div class="card show-tabs-card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-pill" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-programas" data-bs-toggle="tab" data-bs-target="#tab-programas-pane" type="button" role="tab">
                    <i class="ri-booklet-line"></i> Programas y Módulos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-horario" data-bs-toggle="tab" data-bs-target="#tab-horario-pane" type="button" role="tab">
                    <i class="ri-calendar-line"></i> Horario
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Tab Programas y Módulos --}}
            <div class="tab-pane active show" id="tab-programas-pane" role="tabpanel">
                @if(count($programas) > 0)
                    {{-- Sub-tabs por ESTADO DEL MÓDULO --}}
                    <div class="estado-subtabs" role="tablist">
                        <button type="button" class="estado-subtab is-other active" data-subtab="subtab-no">
                            <i class="ri-pause-circle-line"></i> No Inicio
                            <span class="count-pill">{{ $modsPorEstado['no']->count() }}</span>
                        </button>
                        <button type="button" class="estado-subtab is-dev" data-subtab="subtab-dev">
                            <i class="ri-loader-4-line"></i> En Desarrollo
                            <span class="count-pill">{{ $modsPorEstado['dev']->count() }}</span>
                        </button>
                        <button type="button" class="estado-subtab is-end" data-subtab="subtab-end">
                            <i class="ri-checkbox-circle-line"></i> Concluido
                            <span class="count-pill">{{ $modsPorEstado['end']->count() }}</span>
                        </button>
                    </div>

                    @foreach([
                        ['key' => 'subtab-no',  'mods' => $modsPorEstado['no'],  'cls' => 'is-other', 'label' => 'No Inicio',     'icon' => 'ri-pause-circle-line',    'empty' => 'No hay módulos sin iniciar para este docente.'],
                        ['key' => 'subtab-dev', 'mods' => $modsPorEstado['dev'], 'cls' => 'is-dev',   'label' => 'En Desarrollo', 'icon' => 'ri-loader-4-line',        'empty' => 'No hay módulos en desarrollo para este docente.'],
                        ['key' => 'subtab-end', 'mods' => $modsPorEstado['end'], 'cls' => 'is-end',   'label' => 'Concluido',     'icon' => 'ri-checkbox-circle-line', 'empty' => 'No hay módulos concluidos para este docente.'],
                    ] as $section)
                    @php
                        // Agrupa los módulos filtrados por programa
                        $modsBySection = $section['mods']->groupBy('programa_id');
                    @endphp
                    <div class="subtab-section @if($section['key'] === 'subtab-no') active @endif" id="{{ $section['key'] }}">
                        @if($section['mods']->count() > 0)
                            @foreach($programas as $prog)
                                @php
                                    $modsProg = $modsBySection->get($prog->programa_id, collect());
                                    if ($modsProg->isEmpty()) continue;
                                    $totalModsProg = $modulos->where('programa_id', $prog->programa_id)->count();
                                    $pagoSeccion = $modsProg->sum('pago_docente');
                                @endphp
                                <div class="program-card">
                                    <div class="program-status-bar {{ $section['cls'] }}"></div>
                                    <div class="program-card-header">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="program-icon"><i class="ri-graduation-cap-line"></i></span>
                                                    <div style="min-width:0;">
                                                        <h6 class="program-title text-truncate" title="{{ $prog->programa_nombre }}">{{ $prog->programa_nombre }}</h6>
                                                        <div class="program-meta">
                                                            <i class="ri-calendar-line"></i> Gestión {{ $prog->gestion }} · {{ $prog->tipo_nombre }}
                                                            @if($prog->estado_nombre)
                                                                <span class="program-estado-badge {{ $section['cls'] }} ms-1">
                                                                    <i class="{{ $section['icon'] }}"></i>{{ $prog->estado_nombre }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="program-badges justify-content-md-end">
                                                    <span class="prog-badge prog-badge-code"><i class="ri-hashtag"></i>{{ $prog->codigo }}</span>
                                                    <span class="prog-badge prog-badge-mods"><i class="ri-stack-line"></i>{{ $modsProg->count() }} / {{ $totalModsProg }} mód.</span>
                                                    <span class="prog-badge prog-badge-pago"><i class="ri-money-dollar-circle-line"></i>{{ number_format($pagoSeccion, 0) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="modern-table table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;" class="text-center">#</th>
                                                    <th>Módulo</th>
                                                    <th style="width: 140px;">Estado</th>
                                                    <th style="width: 110px;" class="text-end">Pago (Bs)</th>
                                                    <th style="width: 160px;">Proceso Pago</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($modsProg as $idx => $mod)
                                                @php
                                                    $colorMap = [
                                                        'En desarrollo' => '#299cdb',
                                                        'Desarrollado' => '#0ab39c',
                                                        'Concluido' => '#0ab39c',
                                                        'No Inicio' => '#878a99',
                                                        'Pendiente' => '#f7b84b',
                                                        'Suspendido' => '#f06548',
                                                    ];
                                                    $color = $colorMap[$mod->desarrollo] ?? '#878a99';
                                                @endphp
                                                <tr>
                                                    <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="ri-book-2-line text-primary"></i>
                                                            <span class="fw-semibold text-dark">{{ $mod->nombre }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="estado-pill" style="color: {{ $color }};">{{ $mod->desarrollo }}</span>
                                                    </td>
                                                    <td class="text-end fw-semibold text-success">Bs {{ number_format($mod->pago_docente, 2) }}</td>
                                                    <td class="text-muted text-truncate" style="max-width: 160px;" title="{{ $mod->proceso_pago }}">{{ $mod->proceso_pago ?: '—' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"><i class="{{ $section['icon'] }}"></i></div>
                                <h5 class="text-dark fw-bold mb-1">Sin módulos {{ strtolower($section['label']) }}</h5>
                                <p class="text-muted mb-0">{{ $section['empty'] }}</p>
                            </div>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="ri-booklet-line"></i></div>
                        <h5 class="text-dark fw-bold mb-1">Sin programas asignados</h5>
                        <p class="text-muted mb-0">Este docente aún no tiene módulos asignados a ningún programa.</p>
                    </div>
                @endif
            </div>

            {{-- Tab Horario --}}
            <div class="tab-pane" id="tab-horario-pane" role="tabpanel">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detalle Horario --}}
<div class="modal fade" id="modalHorario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-check-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Detalle del Horario</h5>
                        <small class="modal-subtitle">Información completa de la sesión programada</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalHorarioBody"></div>
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
<script src="{{ asset('backend/assets/libs/fullcalendar/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sub-tabs por estado del programa
    document.querySelectorAll('.estado-subtab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.dataset.subtab;
            document.querySelectorAll('.estado-subtab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.subtab-section').forEach(s => s.classList.remove('active'));
            const sec = document.getElementById(target);
            if (sec) sec.classList.add('active');
        });
    });

    const calendarEl = document.getElementById('calendar');

    const estadoColors = {
        'Confirmado': '#0ab39c',
        'Programado': '#299cdb',
        'Desarrollado': '#405189',
        'Postergado': '#f7b84b',
        'Suspendido': '#f06548',
        'Pendiente': '#878a99',
        'No Inicio': '#878a99',
        'Concluido': '#0ab39c',
        'En Desarrollo': '#299cdb',
    };

    function pill(val) {
        if (!val || val === '—') return '<span class="text-muted">—</span>';
        const color = estadoColors[val] || '#878a99';
        return '<span class="estado-pill" style="color:' + color + ';">' + val + '</span>';
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        buttonText: { today: 'Hoy' },
        events: '{{ route("admin.docentes.events", $docente->id) }}',
        loading: function(loading) { calendarEl.style.opacity = loading ? '0.5' : '1'; },
        eventDidMount: function(info) { info.el.setAttribute('title', info.event.title); },
        eventClick: function(info) {
            const e = info.event.extendedProps;
            const fecha = info.event.start.toLocaleDateString('es-BO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const asignado = (e.asignado_nombres || e.asignado_apellidos)
                ? '<span class="fw-semibold">' + (e.asignado_nombres || '') + ' ' + (e.asignado_apellidos || '') + '</span>'
                : '<span class="text-muted">—</span>';

            document.getElementById('modalHorarioBody').innerHTML =
                '<div class="detail-section">' +
                    '<div class="detail-section-title"><span class="detail-section-icon bg-soft-primary text-primary"><i class="ri-graduation-cap-line"></i></span>Programa</div>' +
                    '<div class="detail-row"><div class="detail-label">Programa</div><div class="detail-value">' + (e.programa || '—') + ' <span class="badge bg-soft-dark text-dark ms-1">' + (e.programa_codigo || '') + '</span></div></div>' +
                    '<div class="detail-row"><div class="detail-label">Tipo</div><div class="detail-value">' + (e.tipo || '—') + '</div></div>' +
                    '<div class="detail-row"><div class="detail-label">Gestión</div><div class="detail-value">' + (e.gestion || '—') + '</div></div>' +
                '</div>' +

                '<div class="detail-section">' +
                    '<div class="detail-section-title"><span class="detail-section-icon bg-soft-info text-info"><i class="ri-book-2-line"></i></span>Módulo</div>' +
                    '<div class="detail-row"><div class="detail-label">Nombre</div><div class="detail-value">' + (e.modulo || '—') + '</div></div>' +
                '</div>' +

                '<div class="detail-section">' +
                    '<div class="detail-section-title"><span class="detail-section-icon bg-soft-warning text-warning"><i class="ri-time-line"></i></span>Horario</div>' +
                    '<div class="detail-row"><div class="detail-label">Fecha</div><div class="detail-value text-capitalize">' + fecha + '</div></div>' +
                    '<div class="detail-row"><div class="detail-label">Hora Inicio</div><div class="detail-value fw-semibold text-primary">' + (e.hora_inicio || '—') + '</div></div>' +
                    '<div class="detail-row"><div class="detail-label">Hora Fin</div><div class="detail-value fw-semibold text-danger">' + (e.hora_fin || '—') + '</div></div>' +
                    '<div class="detail-row"><div class="detail-label">Estado</div><div class="detail-value">' + pill(e.horario_estado) + '</div></div>' +
                '</div>' +

                '<div class="detail-section">' +
                    '<div class="detail-section-title"><span class="detail-section-icon bg-soft-success text-success"><i class="ri-user-star-line"></i></span>Asignado</div>' +
                    '<div class="detail-value">' + asignado + '</div>' +
                '</div>' +

                '<div class="detail-section">' +
                    '<div class="detail-section-title"><span class="detail-section-icon bg-soft-secondary text-secondary"><i class="ri-chat-1-line"></i></span>Observaciones</div>' +
                    '<div class="detail-value" style="font-size:.88rem;">' + (e.observaciones && e.observaciones !== '—' ? e.observaciones : '<span class="text-muted fst-italic">Sin observaciones</span>') + '</div>' +
                '</div>';

            new bootstrap.Modal(document.getElementById('modalHorario')).show();
        }
    });

    calendar.render();

    document.querySelector('[data-bs-target="#tab-horario-pane"]').addEventListener('shown.bs.tab', function () {
        setTimeout(function () { calendar.updateSize(); }, 100);
    });
});
</script>
@endpush
