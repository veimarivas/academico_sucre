@extends('admin.layouts.master')

@section('title', 'Presupuestos de Publicidad - ' . config('app.name'))

@push('css')
<style>
/* ─── Variables & Animations ─── */
:root {
    --transition-speed: 0.3s;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* ─── Hero Section ─── */
.pub-hero { 
    position: relative; 
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%); 
    border-radius: 18px; 
    padding: 1.8rem 2rem; 
    color: #fff; 
    overflow: hidden; 
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75); 
    margin-bottom: 1.5rem; 
    z-index: 1;
}
.pub-hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.15), transparent 40%),
                radial-gradient(circle at bottom left, rgba(0,0,0,0.15), transparent 40%);
    z-index: -1;
}
.pub-hero .hero-icon { 
    width: 64px; height: 64px; 
    display: inline-flex; align-items: center; justify-content: center; 
    background: rgba(255,255,255,0.15); 
    border: 1px solid rgba(255,255,255,0.3); 
    backdrop-filter: blur(8px);
    border-radius: 18px; 
    font-size: 2rem; 
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.pub-hero h4 { color: #fff; font-weight: 800; font-size: 1.5rem; margin: 0; letter-spacing: -0.5px; }
.pub-hero .hero-sub { color: rgba(255,255,255,0.9); font-size: 0.95rem; margin: 0; font-weight: 500; }
.pub-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.pub-hero .breadcrumb-item, .pub-hero .breadcrumb-item a { color: rgba(255,255,255,0.75); font-size: 0.85rem; font-weight: 500; transition: color var(--transition-speed); }
.pub-hero .breadcrumb-item a:hover { color: #fff; }
.pub-hero .breadcrumb-item.active { color: #fff; font-weight: 700; }
.pub-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }

/* ─── Cards & Panels ─── */
.filter-card, .card-panel { 
    border: none; 
    border-radius: 16px; 
    background: #fff; 
    box-shadow: 0 6px 24px -8px rgba(64,81,137,0.12); 
    transition: box-shadow var(--transition-speed);
}
[data-layout-mode="dark"] .filter-card, [data-layout-mode="dark"] .card-panel { background: #1a1d21; box-shadow: 0 6px 24px -8px rgba(0,0,0,0.3); }
.filter-card .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1.25rem 1.5rem; }
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: rgba(255,255,255,0.05); }
.form-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #878a99; margin-bottom: 0.4rem; }

/* ─── Stat Cards ─── */
.stat-card { 
    border: 1px solid rgba(0,0,0,0.04); 
    border-radius: 16px; 
    background: #fff; 
    overflow: hidden; 
    position: relative; 
    transition: all var(--transition-speed) cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 15px -5px rgba(0,0,0,0.05);
    animation: fadeInUp 0.5s ease backwards;
}
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px -8px rgba(64,81,137,0.25);
}
.stat-card::before { 
    content: ""; position: absolute; top: 0; left: 0; width: 5px; height: 100%; 
    background: var(--stat-accent, #405189); 
    border-radius: 5px 0 0 5px;
}
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: rgba(255,255,255,0.05); }
[data-layout-mode="dark"] .stat-card:hover { box-shadow: 0 12px 25px -8px rgba(0,0,0,0.5); }
.stat-card .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #878a99; font-weight: 700; margin-bottom: 0.5rem; }
.stat-card .stat-value { font-size: 1.35rem; font-weight: 800; color: #212529; line-height: 1.2; letter-spacing: -0.5px; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #f3f3f9; }
.stat-card .stat-icon { 
    width: 48px; height: 48px; 
    display: inline-flex; align-items: center; justify-content: center; 
    border-radius: 14px; font-size: 1.4rem; 
    background: rgba(0,0,0,0.03); /* Fallback */
}

/* ─── Table Design ─── */
.tabla-pres { width: 100%; border-collapse: separate; border-spacing: 0; }
.tabla-pres th, .tabla-pres td { vertical-align: middle; padding: 0.85rem 0.75rem; border-bottom: 1px solid #f1f3f5; }
.tabla-pres thead th { 
    background: #f3f6f9 !important; 
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; 
    color: #495057; border-bottom: none; 
}
[data-layout-mode="dark"] .tabla-pres th, [data-layout-mode="dark"] .tabla-pres td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .tabla-pres thead th { background: #1a1d20 !important; color: #ced4da; }
.tabla-pres tbody tr { transition: all var(--transition-speed) ease; }
.tabla-pres tbody tr:hover { background-color: rgba(64,81,137,0.03); }
[data-layout-mode="dark"] .tabla-pres tbody tr:hover { background-color: rgba(255,255,255,0.02); }

/* Group Period Row */
.group-period { background: linear-gradient(to right, #f0f4ff, transparent); border-left: 4px solid #405189; }
[data-layout-mode="dark"] .group-period { background: linear-gradient(to right, #1a1f2e, transparent); }
.group-period > td { border-bottom: 2px solid #e2e8f0; }
[data-layout-mode="dark"] .group-period > td { border-bottom-color: #2a2f34; }
.group-period .period-icon { 
    width: 40px; height: 40px; 
    display: inline-flex; align-items: center; justify-content: center; 
    background: linear-gradient(135deg, #405189, #5e72b8); 
    color: #fff; border-radius: 12px; font-size: 1.15rem; 
    box-shadow: 0 4px 10px rgba(64,81,137,0.3);
}
.group-period strong { font-size: 1.1rem; font-weight: 700; letter-spacing: -0.3px; color: #1e293b; }
[data-layout-mode="dark"] .group-period strong { color: #f1f5f9; }

/* Tags & Badges */
.badge-q1, .badge-q2 { font-weight: 700; padding: 0.35rem 0.8rem; font-size: 0.72rem; letter-spacing: 0.3px; }
.badge-q1 { background: #dbeafe; color: #1d4ed8; }
.badge-q2 { background: #fef3c7; color: #b45309; }
[data-layout-mode="dark"] .badge-q1 { background: rgba(29,78,216,0.2); color: #93c5fd; }
[data-layout-mode="dark"] .badge-q2 { background: rgba(180,83,9,0.2); color: #fbbf24; }

.tag-asesor { 
    display: inline-flex; align-items: center; gap: 6px; 
    padding: 0.25rem 0.85rem; border-radius: 99px; 
    font-size: 0.78rem; font-weight: 600; 
    transition: transform var(--transition-speed);
}
.tag-asesor:hover { transform: translateY(-1px); }
.tag-asesor-q1 { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
.tag-asesor-q2 { background: #fffbeb; border: 1px solid #fde68a; color: #b45309; }
[data-layout-mode="dark"] .tag-asesor-q1 { background: rgba(29,78,216,0.1); border-color: rgba(147,197,253,0.2); color: #93c5fd; }
[data-layout-mode="dark"] .tag-asesor-q2 { background: rgba(180,83,9,0.1); border-color: rgba(251,191,36,0.2); color: #fbbf24; }
.tag-asesor i { font-size: 0.85rem; opacity: 0.8; }

/* Progress Bar */
.bar-progress { 
    width: 120px; height: 8px; 
    background: #e2e8f0; border-radius: 99px; 
    overflow: hidden; position: relative;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}
.bar-progress > span { 
    display: block; height: 100%; 
    background: linear-gradient(90deg, #0ab39c, #299cdb); 
    border-radius: 99px; 
    position: relative;
}
.bar-progress > span::after {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; right: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    background-size: 200% 100%;
    animation: shimmer 2s infinite linear;
}
.bar-progress.warn > span { background: linear-gradient(90deg, #f7b84b, #f06548); }
[data-layout-mode="dark"] .bar-progress { background: #334155; }
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
                    <p class="hero-sub">Asignaciones presupuestarias agrupadas por periodo</p>
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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
        <div class="stat-card h-100" style="--stat-accent:#405189;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Total presupuestado</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalAsignado, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-wallet-3-line"></i></div>
                </div>
                <div class="fs-11 text-muted mt-1">{{ $resTotalPeriodos }} periodo(s)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent:#0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Gastado (reportado)</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalGastado, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent:#299cdb;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Aprobado por contable</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($resTotalAprobado, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-info text-info"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent:#f7b84b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Comprobantes cargados</p>
                        <h4 class="stat-value mb-0">{{ $resTotalRecibos }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-receipt-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ri-equalizer-line text-primary"></i>
                <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros</h5>
                <a href="{{ route('admin.marketing.publicidad.index') }}" class="btn btn-sm btn-soft-info ms-auto">
                    <i class="ri-arrow-left-line me-1"></i>Volver a comprobantes
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketing.publicidad.presupuestos') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Gestión</label>
                        <select name="gestion" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" @selected((string)$gestionFiltro === (string)$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-10 d-flex align-items-end justify-content-end">
                        <a href="{{ route('admin.marketing.publicidad.presupuestos') }}" class="btn btn-soft-secondary btn-sm">
                            <i class="ri-refresh-line me-1"></i>Limpiar filtro
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de presupuestos --}}
<div class="row">
    <div class="col-12">
        <div class="card card-panel mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-wallet-3-line text-primary"></i>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Asignaciones por periodo</h5>
                </div>
                @if($grupos->isNotEmpty())
                <span class="badge bg-soft-primary fs-11 px-2 py-1">{{ $resTotalPeriodos }} periodo(s)</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($grupos->isEmpty())
                <div class="text-center py-5">
                    <i class="ri-inbox-2-line ri-3x text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">Sin presupuestos registrados</h5>
                    <p class="text-muted mb-0">Aún no se han asignado presupuestos de publicidad.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table tabla-pres mb-0">
                        <thead>
                            <tr>
                                <th style="width:130px;">Periodo</th>
                                <th style="width:80px;">Quincena</th>
                                <th>Asesores principales</th>
                                <th class="text-end" style="width:100px;">Asignado</th>
                                <th class="text-end" style="width:100px;">Gastado</th>
                                <th class="text-end" style="width:100px;">Aprobado</th>
                                <th class="text-center" style="width:60px;">Recibos</th>
                                <th class="text-center" style="width:80px;">Estado</th>
                                <th class="text-center" style="width:130px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grupos as $key => $g)
                            @php
                                $totalAsignado = ($g->q1->total_asignado ?? 0) + ($g->q2->total_asignado ?? 0);
                                $totalGastado = ($g->q1->total_gastado ?? 0) + ($g->q2->total_gastado ?? 0);
                                $totalAprobado = ($g->q1->total_aprobado ?? 0) + ($g->q2->total_aprobado ?? 0);
                                $totalAprobadoObs = ($g->q1->total_aprobado_obs ?? 0) + ($g->q2->total_aprobado_obs ?? 0);
                                $totalComprobantes = ($g->q1->total_comprobantes ?? 0) + ($g->q2->total_comprobantes ?? 0);
                                $gastoPct = $totalAsignado > 0 ? min(100, round(($totalGastado / $totalAsignado) * 100, 1)) : 0;
                            @endphp
                            {{-- Fila de grupo (gestión - mes) --}}
                            <tr class="group-period">
                                <td colspan="9">
                                    <div class="d-flex align-items-center gap-3 px-2 py-1">
                                        <span class="period-icon"><i class="ri-calendar-line"></i></span>
                                        <strong class="fs-14">{{ $meses[$g->mes] }} {{ $g->gestion }}</strong>
                                        @if($gastoPct > 0)
                                        <div class="bar-progress {{ $gastoPct > 100 ? 'warn' : '' }} ms-2">
                                            <span style="width:{{ $gastoPct }}%;"></span>
                                        </div>
                                        <span class="text-muted fs-11">{{ $gastoPct }}%</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Fila Q1 --}}
                            @if($g->q1)
                            @php $q = $g->q1; @endphp
                            <tr>
                                <td></td>
                                <td><span class="badge badge-q1 fs-11 px-3 py-2 rounded-pill">Q1 (1-15)</span></td>
                                <td>
                                    @if($q->principales->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                        @foreach($q->principales as $pr)
                                            <span class="tag-asesor tag-asesor-q1">
                                                <i class="ri-user-star-line" style="font-size:.65rem;"></i>
                                                {{ $pr->apellidos }}, {{ $pr->nombres }}
                                                <span style="font-weight:400;color:#6b7280;font-size:.65rem;">({{ $pr->cargo_nombre }})</span>
                                            </span>
                                        @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic fs-12">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">Bs {{ number_format($q->total_asignado, 2) }}</td>
                                <td class="text-end">Bs {{ number_format($q->total_gastado, 2) }}</td>
                                <td class="text-end text-success fw-semibold">Bs {{ number_format($q->total_aprobado, 2) }}</td>
                                <td class="text-center">{{ $q->total_comprobantes }}</td>
                                <td class="text-center">
                                    @if($q->periodo_cerrado)
                                        <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                                    @else
                                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        <a href="{{ route('admin.marketing.publicidad.index', ['gestion' => $q->gestion, 'mes' => $q->mes, 'quincena' => $q->quincena]) }}" class="btn btn-sm btn-soft-primary" title="Ver detalle de Q{{ $q->quincena }}">
                                            <i class="ri-eye-line"></i>
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
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td></td>
                                <td><span class="badge badge-q1 fs-11 px-3 py-2 rounded-pill">Q1 (1-15)</span></td>
                                <td colspan="7" class="text-muted fst-italic fs-12">Sin presupuesto asignado</td>
                            </tr>
                            @endif

                            {{-- Fila Q2 --}}
                            @if($g->q2)
                            @php $q = $g->q2; @endphp
                            <tr>
                                <td></td>
                                <td><span class="badge badge-q2 fs-11 px-3 py-2 rounded-pill">Q2 (16-fin)</span></td>
                                <td>
                                    @if($q->principales->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                        @foreach($q->principales as $pr)
                                            <span class="tag-asesor tag-asesor-q2">
                                                <i class="ri-user-star-line" style="font-size:.65rem;"></i>
                                                {{ $pr->apellidos }}, {{ $pr->nombres }}
                                                <span style="font-weight:400;color:#6b7280;font-size:.65rem;">({{ $pr->cargo_nombre }})</span>
                                            </span>
                                        @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic fs-12">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">Bs {{ number_format($q->total_asignado, 2) }}</td>
                                <td class="text-end">Bs {{ number_format($q->total_gastado, 2) }}</td>
                                <td class="text-end text-success fw-semibold">Bs {{ number_format($q->total_aprobado, 2) }}
                                    @if(($q->total_aprobado_obs ?? 0) > 0)
                                    <br><span class="text-info fs-11"><i class="ri-chat-4-line"></i> {{ number_format($q->total_aprobado_obs, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $q->total_comprobantes }}</td>
                                <td class="text-center">
                                    @if($q->periodo_cerrado)
                                        <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                                    @else
                                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        <a href="{{ route('admin.marketing.publicidad.index', ['gestion' => $q->gestion, 'mes' => $q->mes, 'quincena' => $q->quincena]) }}" class="btn btn-sm btn-soft-primary" title="Ver detalle de Q{{ $q->quincena }}">
                                            <i class="ri-eye-line"></i>
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
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td></td>
                                <td><span class="badge badge-q2 fs-11 px-3 py-2 rounded-pill">Q2 (16-fin)</span></td>
                                <td colspan="7" class="text-muted fst-italic fs-12">Sin presupuesto asignado</td>
                            </tr>
                            @endif

                            {{-- Totales del grupo --}}
                            @php
                                $totalPendiente = ($g->q1->total_pendiente ?? 0) + ($g->q2->total_pendiente ?? 0);
                            @endphp
                            <tr style="background:#f8f9fc;">
                                <td colspan="3" class="text-end fw-bold fs-13 text-dark py-2">
                                    <i class="ri-calculator-line me-1 text-muted"></i>Total {{ $meses[$g->mes] }} {{ $g->gestion }}
                                </td>
                                <td class="text-end fw-bold fs-14 text-primary py-2">Bs {{ number_format($totalAsignado, 2) }}</td>
                                <td class="text-end fw-bold fs-14 py-2">Bs {{ number_format($totalGastado, 2) }}</td>
                                <td class="text-end fw-bold fs-14 text-success py-2">Bs {{ number_format($totalAprobado, 2) }}
                                    @if($totalAprobadoObs > 0)
                                    <br><span class="text-info fs-11"><i class="ri-chat-4-line"></i> {{ number_format($totalAprobadoObs, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold py-2">{{ $totalComprobantes }}</td>
                                <td colspan="2" class="py-2"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-toggle-periodo');
    if (!btn) return;
    const gestion = btn.dataset.gestion;
    const mes = btn.dataset.mes;
    const quincena = btn.dataset.quincena;
    const cerrado = btn.dataset.cerrado === '1';
    const nuevaAccion = cerrado ? 'abrir' : 'cerrar';
    if (!confirm('¿Estás seguro de ' + nuevaAccion + ' el periodo ' + gestion + '/' + mes + ' Q' + quincena + '?')) return;
    const formData = new FormData();
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
</script>
@endpush
