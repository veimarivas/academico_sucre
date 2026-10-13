@extends('admin.layouts.master')

@section('title', 'Publicidad - Comprobantes - ' . config('app.name'))

@push('css')
<style>
.pub-hero { position: relative; background: linear-gradient(135deg,#12345b 0%,#1c486c 45%,#0a6a90 100%); border-radius: 18px; padding: 1.7rem 1.9rem; color:#fff; overflow:hidden; box-shadow: 0 18px 40px -22px rgba(18,52,91,.75); margin-bottom: 1.25rem; }
.pub-hero .hero-icon { width:56px;height:56px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);border-radius:14px;font-size:1.6rem; }
.pub-hero h4 { color:#fff; font-weight:700; margin:0; }
.pub-hero .hero-sub { color:rgba(255,255,255,.85); font-size:.88rem; margin:0; }
.pub-hero .breadcrumb { background:transparent; padding:0; margin:0; }
.pub-hero .breadcrumb-item, .pub-hero .breadcrumb-item a { color:rgba(255,255,255,.85); }
.pub-hero .breadcrumb-item.active { color:#fff; font-weight:600; }
.pub-hero .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.55); }

.stat-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; overflow:hidden; position:relative; }
.stat-card::before { content:""; position:absolute; top:0; left:0; width:4px; height:100%; background: var(--stat-accent,#405189); }
[data-layout-mode="dark"] .stat-card { background:#212529; border-color:#2a2f34; }
.stat-card .stat-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.5px; color:#878a99; font-weight:700; margin-bottom:.4rem; }
.stat-card .stat-value { font-size:1.15rem; font-weight:700; color:#212529; line-height:1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color:#fff; }
.stat-card .stat-icon { width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; font-size:1.25rem; }

.filter-card, .card-panel { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card, [data-layout-mode="dark"] .card-panel { background:#1d2125; border-color:#2a2f34; }
.filter-card .card-header, .card-panel .card-header { background:transparent; border-bottom:1px dashed #eef0f4; padding:1rem 1.25rem; }
.form-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:#878a99; margin-bottom:.35rem; }

.tabla-pub { width:100%; }
.tabla-pub th, .tabla-pub td { vertical-align: middle; padding:.65rem .6rem; }
.tabla-pub thead th { background:#f8f9fc !important; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#495057; border-bottom:2px solid #eef0f4; }
[data-layout-mode="dark"] .tabla-pub thead th { background:#1a1d20 !important; border-bottom-color:#2a2f34; }
.tabla-pub tbody tr { border-bottom:1px solid #f1f3f5; transition:background-color .15s ease; }
.tabla-pub tbody tr:hover { background-color:#f8f9fc; }

.badge-quincena-1 { background:#dbeafe; color:#1e40af; }
.badge-quincena-2 { background:#fef3c7; color:#92400e; }

.file-preview { display:inline-flex; align-items:center; gap:.4rem; padding:.25rem .6rem; background:#f1f3f5; border-radius:6px; font-size:.75rem; color:#495057; text-decoration:none; }
.file-preview:hover { background:#e9ecef; color:#405189; }

.progress-bar-custom { height:8px; background:#e9ecef; border-radius:99px; overflow:hidden; }
.progress-bar-custom > span { display:block; height:100%; background:linear-gradient(90deg,#0ab39c,#299cdb); border-radius:99px; }
.progress-bar-custom.warn > span { background:linear-gradient(90deg,#f7b84b,#f06548); }

.presupuesto-item { border:1px solid #dde3ed; border-left:4px solid #405189; border-radius:10px; background:#fff; }
[data-layout-mode="dark"] .presupuesto-item { background:#1d2125; border-color:#2a2f34; }
.presupuesto-item .card-monto { border:1px solid #eef0f4; border-radius:8px; background:#fff; }
[data-layout-mode="dark"] .presupuesto-item .card-monto { background:#212529; border-color:#2a2f34; }
.presupuesto-item .amount-original { color:#405189; }
.presupuesto-item .amount-derived { color:#b45309; }
.presupuesto-item .amount-neto { color:#405189; }

.nav-tabs-custom { border-bottom:1px solid #eef0f4; gap:0; }
.nav-tabs-custom .nav-link { border:1px solid transparent; border-bottom:none; font-size:.78rem; font-weight:600; color:#878a99; padding:.55rem .9rem; margin-bottom:-1px; border-radius:8px 8px 0 0; background:transparent; transition:all .15s ease; }
.nav-tabs-custom .nav-link:hover { color:#405189; background:#f8f9fc; }
.nav-tabs-custom .nav-link.active { color:#405189; background:#fff; border-color:#eef0f4; border-bottom-color:#fff; }
.nav-tabs-custom .nav-link .badge { font-size:.62rem; font-weight:600; padding:.2em .55em; vertical-align:middle; }
[data-layout-mode="dark"] .nav-tabs-custom { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link { color:#878a99; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link:hover { color:#fff; background:#1d2125; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link.active { color:#fff; background:#1d2125; border-color:#2a2f34; border-bottom-color:#1d2125; }


.deriv-section { border:1px solid #fde68a; border-radius:10px; overflow:hidden; }
.deriv-section .deriv-header { background:linear-gradient(135deg,#fffbeb,#fef3c7); border-bottom:1px solid #fde68a; }
.deriv-section .deriv-row { background:#fffaf5; }
.deriv-section .deriv-row-alt { background:#fff7ed; border-bottom:1px solid #fde68a; }
[data-layout-mode="dark"] .deriv-section { border-color:#92400e; }
[data-layout-mode="dark"] .deriv-section .deriv-header { background:linear-gradient(135deg,#451a03,#78350f); border-bottom-color:#92400e; }
[data-layout-mode="dark"] .deriv-section .deriv-header .deriv-title { color:#fbbf24; }
[data-layout-mode="dark"] .deriv-section .deriv-row { background:#1a1410; }
[data-layout-mode="dark"] .deriv-section .deriv-row-alt { background:#1f1812; border-bottom-color:#92400e; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="pub-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-shopping-cart-2-line"></i></div>
                <div>
                    <h4>Gastos de Publicidad</h4>
                    <p class="hero-sub">Comprobantes y verificación quincenal · {{ $meses[$mes] }} {{ $gestion }}</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.marketing.publicidad.presupuestos') }}">Presupuestos</a></li>
                <li class="breadcrumb-item active">Publicidad</li>
            </ol>
            <a href="{{ route('admin.marketing.publicidad.presupuestos') }}" class="btn btn-sm btn-soft-primary" title="Ver todos los presupuestos por periodo">
                <i class="ri-wallet-3-line me-1"></i>Presupuestos
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Resumen quincenal --}}
@php
    $q1 = $resumenQuincenas[1] ?? null;
    $q2 = $resumenQuincenas[2] ?? null;
    $totalPresupuesto = $presupuestos->sum('monto_asignado');
    $totalGastadoMes = ($q1->total_gastado ?? 0) + ($q2->total_gastado ?? 0);
    $totalAprobadoMes = ($q1->total_aprobado ?? 0) + ($q2->total_aprobado ?? 0);
    $totalAprobadoObsMes = ($q1->total_aprobado_obs ?? 0) + ($q2->total_aprobado_obs ?? 0);
    $totalPendienteMes = ($q1->total_pendiente ?? 0) + ($q2->total_pendiente ?? 0);
@endphp
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#405189;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Presupuesto asignado</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($totalPresupuesto, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-wallet-3-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Gastado (reportado)</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($totalGastadoMes, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#299cdb;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Aprobado por contable</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($totalAprobadoMes, 2) }}</h4>
                        @if($totalAprobadoObsMes > 0)
                        <div class="fs-11 text-muted mt-1"><i class="ri-chat-4-line"></i> {{ number_format($totalAprobadoObsMes, 2) }} con obs.</div>
                        @endif
                    </div>
                    <div class="stat-icon bg-soft-info text-info"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#f7b84b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Pendiente de verificación</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($totalPendienteMes, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-time-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Barras por quincena --}}
<div class="row g-3 mb-4">
    @foreach([1, 2] as $qNum)
    @php
        $qData = $resumenQuincenas[$qNum] ?? null;
        $rango = $qNum === 1 ? '1 al 15' : '16 a fin de mes';
        $presQ = $presupuestos->where('quincena', $qNum)->sum('monto_asignado');
        $gastado = $qData->total_gastado ?? 0;
        $porc = $presQ > 0 ? min(100, ($gastado / $presQ) * 100) : 0;
    @endphp
    <div class="col-md-6">
        <div class="card card-panel h-100 mb-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge badge-quincena-{{ $qNum }} fs-11 px-2 py-1 me-2">Q{{ $qNum }}</span>
                        <strong class="fs-14">{{ $rango }}</strong>
                    </div>
                    <span class="text-muted fs-12">{{ $qData->total_recibos ?? 0 }} recibos</span>
                </div>
                <div class="d-flex justify-content-between fs-12 text-muted mb-1">
                    <span>Gastado: <strong class="text-dark">Bs {{ number_format($gastado, 2) }}</strong></span>
                    <span>Asignado: <strong class="text-dark">Bs {{ number_format($presQ, 2) }}</strong></span>
                </div>
                <div class="progress-bar-custom {{ $porc > 100 ? 'warn' : '' }}"><span style="width: {{ $porc }}%;"></span></div>
                <div class="d-flex justify-content-between fs-11 mt-2">
                    <span class="text-success"><i class="ri-check-line"></i> Aprobado Bs {{ number_format($qData->total_aprobado ?? 0, 2) }}
                        @if(($qData->total_aprobado_obs ?? 0) > 0)
                        <span class="text-info" title="Con observaciones"><i class="ri-chat-4-line"></i> {{ number_format($qData->total_aprobado_obs, 2) }}</span>
                        @endif
                    </span>
                    <span class="text-warning"><i class="ri-time-line"></i> Pendiente Bs {{ number_format($qData->total_pendiente ?? 0, 2) }}</span>
                    <span class="text-danger"><i class="ri-close-line"></i> Rechazado Bs {{ number_format($qData->total_rechazado ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ri-equalizer-line text-primary"></i>
                <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketing.publicidad.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Gestión</label>
                        <select name="gestion" class="form-select">
                            @foreach($gestiones as $g)
                                <option value="{{ $g }}" @selected((int)$gestion === (int)$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mes</label>
                        <select name="mes" class="form-select">
                            @foreach($meses as $k => $v)
                                <option value="{{ $k }}" @selected((int)$mes === (int)$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quincena</label>
                        <select name="quincena" class="form-select">
                            <option value="todas" @selected($quincena === 'todas')>Ambas</option>
                            <option value="1" @selected((string)$quincena === '1')>Q1 (1-15)</option>
                            <option value="2" @selected((string)$quincena === '2')>Q2 (16-fin)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Asesor</label>
                        <select name="asesor_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($asesores as $a)
                                <option value="{{ $a->id }}" @selected((string)$asesorId === (string)$a->id)>{{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="todos" @selected($estado === 'todos')>Todos</option>
                            <option value="pendiente" @selected($estado === 'pendiente')>Pendiente</option>
                            <option value="aprobado" @selected($estado === 'aprobado')>Aprobado</option>
                            <option value="rechazado" @selected($estado === 'rechazado')>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-3-line"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Asignar presupuesto --}}
@can('marketing.publicidad.presupuestos')
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-panel mb-0">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ri-wallet-3-line text-primary"></i>
                <h5 class="card-title mb-0 fs-14 fw-semibold">Asignar presupuesto de publicidad</h5>
                <span class="badge bg-soft-info text-info fs-11 ms-2"><i class="ri-information-line"></i> Los comprobantes se suben desde cada asignación</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.marketing.publicidad.presupuesto.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Supervisor de marketing</label>
                        <select name="supervisor_id" class="form-select">
                            <option value="">— Sin supervisor —</option>
                            @foreach($asesores as $a)
                                <option value="{{ $a->id }}">{{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Asesor principal <span class="text-danger">*</span></label>
                        <select name="asesor_id" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($asesoresPrincipales as $a)
                                <option value="{{ $a->id }}">{{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted fs-11">Solo cargos principales (Gerente/Supervisor)</small>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Gestión</label>
                        <input type="number" name="gestion" class="form-control" value="{{ $gestion }}" min="2020" max="2100" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mes</label>
                        <select name="mes" class="form-select" required>
                            @foreach($meses as $k => $v)
                                <option value="{{ $k }}" @selected((int)$mes === (int)$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Quincena</label>
                        <select name="quincena" class="form-select" required>
                            <option value="1">Q1</option>
                            <option value="2">Q2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Monto (Bs)</label>
                        <input type="number" step="0.01" min="0" name="monto_asignado" class="form-control" placeholder="670.00" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="ri-add-line me-1"></i>Asignar presupuesto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Presupuestos asignados con sus comprobantes anidados --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-panel">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-wallet-3-line text-primary"></i>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Presupuestos · {{ $meses[$mes] }} {{ $gestion }}</h5>
                </div>
                <span class="badge bg-soft-primary fs-11 px-2 py-1">{{ $presupuestos->count() }} presupuestos</span>
            </div>
            <div class="card-body">
                @php
                    $hasData = false;
                    $asesoresPrincipalesList = $presupuestosPorAsesor->filter(fn($g) => in_array($g->cargo_id, [1, 2]));
                    $tieneGenerales = $presupuestosGenerales->isNotEmpty();
                    $totalPresupCount = $presupuestos->count();
                @endphp

                {{-- Navegación de tabs --}}
                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-todos" role="tab" aria-selected="true">
                            <i class="ri-list-check me-1"></i>Todos
                            @if($totalPresupCount > 0) <span class="badge bg-primary ms-1">{{ $totalPresupCount }}</span> @endif
                        </a>
                    </li>
                    @foreach($asesoresPrincipalesList as $asesorId => $grupo)
                    @php $cantP = $grupo->presupuestos->whereNull('presupuesto_origen_id')->count(); @endphp
                    @if($cantP > 0)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-asesor-{{ $asesorId }}" role="tab" aria-selected="false">
                            <i class="ri-user-star-line me-1"></i>{{ $grupo->apellidos }}, {{ $grupo->nombres }}
                            <span class="badge bg-primary ms-1">{{ $cantP }}</span>
                        </a>
                    </li>
                    @endif
                    @endforeach
                    @if($tieneGenerales)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-generales" role="tab" aria-selected="false">
                            <i class="ri-team-line me-1"></i>Generales
                            <span class="badge bg-primary ms-1">{{ $presupuestosGenerales->count() }}</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">
                    {{-- Tab: Todos --}}
                    <div class="tab-pane active" id="tab-todos" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-muted fs-12 tab-info"></span>
                        </div>
                        <div class="tab-paginator" data-tab-id="tab-todos">
                        @foreach($asesoresPrincipalesList as $asesorId => $grupo)
                        @php $hasData = true; @endphp
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2 mb-3 rounded" style="background:var(--ui-surface-2);border:1px solid var(--ui-line);">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:#fff;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.06);">
                                        <i class="ri-user-star-line text-warning fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0 fw-bold fs-14">{{ $grupo->apellidos }}, {{ $grupo->nombres }}</h6>
                                        <span class="text-muted fs-11">{{ $grupo->presupuestos->whereNull('presupuesto_origen_id')->count() }} presupuesto(s) · {{ $grupo->total_comprobantes }} comprobante(s)</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 fs-12 bg-white rounded px-3 py-2">
                                    @if($grupo->total_derivado > 0)
                                    <span title="Monto original asignado">Original: <strong class="text-dark">Bs {{ number_format($grupo->total_original, 2) }}</strong></span>
                                    <span class="text-muted">|</span>
                                    <span title="Monto derivado a otros asesores" style="color:#b45309;">Derivado: <strong>Bs {{ number_format($grupo->total_derivado, 2) }}</strong></span>
                                    <span class="text-muted">|</span>
                                    @endif
                                    <span>Neto: <strong class="text-primary">Bs {{ number_format($grupo->total_asignado, 2) }}</strong></span>
                                    <span class="text-muted">|</span>
                                    <span>Gastado: <strong class="text-dark">Bs {{ number_format($grupo->total_gastado, 2) }}</strong></span>
                                    <span class="text-muted">|</span>
                                    <span>Aprobado: <strong class="text-success">Bs {{ number_format($grupo->total_aprobado, 2) }}</strong>
                                        @if(($grupo->total_aprobado_obs ?? 0) > 0)
                                        <span class="text-info" title="Con observaciones"><i class="ri-chat-4-line"></i> {{ number_format($grupo->total_aprobado_obs, 2) }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @foreach($grupo->presupuestos as $p)
                                @continue($p->presupuesto_origen_id)
                                @php
                                    $comps = $comprobantesPorPresupuesto[$p->id] ?? collect();
                                    $porc = $p->monto_asignado > 0 ? min(100, ($p->total_gastado / $p->monto_asignado) * 100) : 0;
                                    $rango = $p->quincena === 1 ? '1 al 15' : '16 a fin de mes';
                                @endphp
                                @include('admin.marketing.publicidad._presupuesto_item', ['p' => $p, 'comps' => $comps, 'porc' => $porc, 'rango' => $rango, 'meses' => $meses])
                            @endforeach
                        </div>
                        @endforeach

                        @if($tieneGenerales)
                        @php $hasData = true; @endphp
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2 px-2">
                                <i class="ri-team-line text-muted fs-16"></i>
                                <h6 class="mb-0 fw-bold fs-14 text-muted">Presupuestos generales</h6>
                                <span class="badge bg-soft-secondary fs-10 px-2 py-1">{{ $presupuestosGenerales->count() }} presupuestos</span>
                            </div>
                            @foreach($presupuestosGenerales as $p)
                                @php
                                    $comps = $comprobantesPorPresupuesto[$p->id] ?? collect();
                                    $porc = $p->monto_asignado > 0 ? min(100, ($p->total_gastado / $p->monto_asignado) * 100) : 0;
                                    $rango = $p->quincena === 1 ? '1 al 15' : '16 a fin de mes';
                                @endphp
                                @include('admin.marketing.publicidad._presupuesto_item', ['p' => $p, 'comps' => $comps, 'porc' => $porc, 'rango' => $rango, 'meses' => $meses])
                            @endforeach
                        </div>
                        @endif

                        @if(!$hasData)
                        <div class="text-center text-muted py-5">
                            <i class="ri-wallet-3-line ri-2x d-block mb-2"></i>
                            <p class="mb-0">Aún no hay presupuestos asignados para {{ $meses[$mes] }} {{ $gestion }}.</p>
                            @can('marketing.publicidad.presupuestos')
                            <small>Usa el formulario de arriba para asignar el presupuesto quincenal a cada asesor.</small>
                            @endcan
                        </div>
                        @endif
                        </div>
                        <div class="tab-paginator-nav d-flex justify-content-center align-items-center gap-2 mt-3" data-tab-id="tab-todos"></div>
                    </div>

                    {{-- Tab: Por asesor individual --}}
                    @foreach($asesoresPrincipalesList as $asesorId => $grupo)
                    @php
                        $presupuestosAsesor = $grupo->presupuestos->whereNull('presupuesto_origen_id');
                    @endphp
                    @if($presupuestosAsesor->isNotEmpty())
                    <div class="tab-pane" id="tab-asesor-{{ $asesorId }}" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-muted fs-12 tab-info"></span>
                        </div>
                        <div class="tab-paginator" data-tab-id="tab-asesor-{{ $asesorId }}">
                        @foreach($presupuestosAsesor as $p)
                            @php
                                $comps = $comprobantesPorPresupuesto[$p->id] ?? collect();
                                $porc = $p->monto_asignado > 0 ? min(100, ($p->total_gastado / $p->monto_asignado) * 100) : 0;
                                $rango = $p->quincena === 1 ? '1 al 15' : '16 a fin de mes';
                            @endphp
                            @include('admin.marketing.publicidad._presupuesto_item', ['p' => $p, 'comps' => $comps, 'porc' => $porc, 'rango' => $rango, 'meses' => $meses])
                        @endforeach
                        </div>
                        <div class="tab-paginator-nav d-flex justify-content-center align-items-center gap-2 mt-3" data-tab-id="tab-asesor-{{ $asesorId }}"></div>
                    </div>
                    @endif
                    @endforeach

                    {{-- Tab: Generales --}}
                    @if($tieneGenerales)
                    <div class="tab-pane" id="tab-generales" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="text-muted fs-12 tab-info"></span>
                        </div>
                        <div class="tab-paginator" data-tab-id="tab-generales">
                        @foreach($presupuestosGenerales as $p)
                            @php
                                $comps = $comprobantesPorPresupuesto[$p->id] ?? collect();
                                $porc = $p->monto_asignado > 0 ? min(100, ($p->total_gastado / $p->monto_asignado) * 100) : 0;
                                $rango = $p->quincena === 1 ? '1 al 15' : '16 a fin de mes';
                            @endphp
                            @include('admin.marketing.publicidad._presupuesto_item', ['p' => $p, 'comps' => $comps, 'porc' => $porc, 'rango' => $rango, 'meses' => $meses])
                        @endforeach
                        </div>
                        <div class="tab-paginator-nav d-flex justify-content-center align-items-center gap-2 mt-3" data-tab-id="tab-generales"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modales por presupuesto: Subir comprobante + Reembolsar --}}
@foreach($presupuestos as $p)
    @can('marketing.publicidad.subir')
    @if($p->estado === 'activo' && !($p->periodo_cerrado ?? false))
    @php
        $fechaMin = $p->quincena === 1
            ? \Carbon\Carbon::create($p->gestion, $p->mes, 1)->subDays(2)->format('Y-m-d')
            : \Carbon\Carbon::create($p->gestion, $p->mes, 16)->subDays(2)->format('Y-m-d');
        $fechaMax = sprintf('%04d-%02d-%02d', $p->gestion, $p->mes, $p->quincena === 1 ? 15 : \Carbon\Carbon::create($p->gestion, $p->mes, 1)->endOfMonth()->day);
    @endphp
    <div class="modal fade" id="modalSubir{{ $p->id }}" tabindex="-1" aria-hidden="true" data-presupuesto-id="{{ $p->id }}" data-fecha-min="{{ $fechaMin }}" data-fecha-max="{{ $fechaMax }}">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form class="form-subir-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="presupuesto_id" value="{{ $p->id }}">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0"><i class="ri-upload-cloud-2-line me-1"></i>Subir comprobante</h5>
                            <small class="text-muted">
                                @if($p->ase_apellidos)
                                    Titular: {{ $p->ase_apellidos }}, {{ $p->ase_nombres }} ·
                                @else
                                    Presupuesto general ·
                                @endif
                                Q{{ $p->quincena }} {{ $meses[$p->mes] }} {{ $p->gestion }} · Presupuesto Bs {{ number_format($p->monto_asignado, 2) }}
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info fs-12 py-2 mb-3">
                            <i class="ri-information-line me-1"></i> Puedes registrar varios comprobantes por este presupuesto. Selecciona el asesor que realizó cada gasto (puede ser distinto al titular si se derivó parte de la publicidad).
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Asesor que realizó el gasto <span class="text-danger">*</span></label>
                                <select name="asesor_id" class="form-select validate-required" required>
                                    <option value="">— Seleccionar asesor —</option>
                                    @foreach($asesores as $a)
                                        <option value="{{ $a->id }}" @selected($p->asesor_id && (int)$p->asesor_id === (int)$a->id)>
                                            {{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})
                                            @if($p->asesor_id && (int)$p->asesor_id === (int)$a->id) — Titular del presupuesto @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Seleccione el asesor que realizó el gasto.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control validate-required" min="{{ $fechaMin }}" max="{{ $fechaMax }}" required>
                                <div class="invalid-feedback">La fecha de inicio es obligatoria y debe estar dentro de la quincena ({{ $fechaMin }} → {{ $fechaMax }}).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_fin" class="form-control validate-required validate-after" min="{{ $fechaMin }}" max="{{ $fechaMax }}" required>
                                <div class="invalid-feedback">La fecha de fin debe ser igual o posterior a la de inicio y dentro de la quincena.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="monto" class="form-control validate-required" placeholder="0.00" required>
                                <div class="invalid-feedback">Ingrese un monto válido mayor a 0.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Plataforma</label>
                                <select name="plataforma" class="form-select">
                                    <option value="Facebook">Facebook Ads</option>
                                    <option value="Instagram">Instagram Ads</option>
                                    <option value="TikTok">TikTok Ads</option>
                                    <option value="Google">Google Ads</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nombre de la campaña</label>
                                <input type="text" name="campana_nombre" class="form-control" placeholder="Ej: Diplomado en Auditoría - Julio">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Enlace de la campaña</label>
                                <input type="url" name="enlace_campana" class="form-control validate-url" placeholder="https://www.facebook.com/ads/...">
                                <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Programa promocionado</label>
                                <select name="programa_id" class="form-select">
                                    <option value="">— Sin programa —</option>
                                    @foreach($programas as $prg)
                                        <option value="{{ $prg->id }}">{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Página de Facebook</label>
                                <input type="url" name="pagina_facebook" class="form-control validate-url" placeholder="https://www.facebook.com/tupagina">
                                <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Descripción del gasto <span class="text-danger">*</span></label>
                                <textarea name="descripcion" rows="2" class="form-control validate-required" placeholder="Ej: Publicación pagada Facebook 3 días para promoción del diplomado" required></textarea>
                                <div class="invalid-feedback">La descripción es obligatoria.</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Comprobante (PDF o imagen, máx 5MB) <span class="text-danger">*</span></label>
                                <input type="file" name="archivo" class="form-control validate-file" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                                <div class="invalid-feedback">Seleccione un archivo PDF o imagen (máx 5MB).</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Comprobante de débito de meta (PDF o imagen, máx 5MB)</label>
                                <input type="file" name="archivo_debito" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                <div class="invalid-feedback">Seleccione un archivo PDF o imagen (máx 5MB).</div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-danger d-none" id="error-global-{{ $p->id }}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-2 w-100 justify-content-between">
                            <div class="spinner-border spinner-border-sm d-none text-success" role="status" id="spinner-{{ $p->id }}">
                                <span class="visually-hidden">Subiendo...</span>
                            </div>
                            <div class="d-flex gap-2 ms-auto">
                                <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success btn-subir"><i class="ri-upload-2-line me-1"></i>Subir comprobante</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan

    @can('marketing.publicidad.presupuestos')
    @if($p->estado === 'activo' && !($p->periodo_cerrado ?? false))
    <div class="modal fade" id="modalReembolso{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.marketing.publicidad.presupuesto.reembolsar', $p->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar reembolso — Q{{ $p->quincena }} {{ $meses[$p->mes] }} {{ $p->gestion }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info fs-12 py-2 mb-3">
                            <i class="ri-information-line me-1"></i> Aprobado por contable: <strong>Bs {{ number_format($p->total_aprobado, 2) }}</strong> · Asignado: <strong>Bs {{ number_format($p->monto_asignado, 2) }}</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto reembolsado (Bs)</label>
                            <input type="number" step="0.01" min="0" name="monto_reembolsado" class="form-control" value="{{ number_format($p->total_aprobado, 2, '.', '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="form-control" placeholder="Detalle del reembolso"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="ri-check-line me-1"></i>Confirmar reembolso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan

    @can('marketing.publicidad.presupuestos')
    @if($p->estado === 'activo' && !$p->presupuesto_origen_id && !($p->periodo_cerrado ?? false))
    <div class="modal fade" id="modalDerivar{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.marketing.publicidad.presupuesto.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="presupuesto_origen_id" value="{{ $p->id }}">
                    <input type="hidden" name="supervisor_id" value="{{ $p->asesor_id }}">
                    <input type="hidden" name="gestion" value="{{ $p->gestion }}">
                    <input type="hidden" name="mes" value="{{ $p->mes }}">
                    <input type="hidden" name="quincena" value="{{ $p->quincena }}">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ri-share-forward-line text-info me-1"></i>Derivar presupuesto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info fs-12 py-2 mb-3">
                            <i class="ri-information-line me-1"></i>
                            Estás derivando parte del presupuesto de <strong>{{ $p->ase_apellidos }}, {{ $p->ase_nombres }}</strong>
                            (Q{{ $p->quincena }} · <strong>Disponible: Bs {{ number_format($p->monto_asignado, 2) }}</strong>) a otro asesor.
                            @if($derivadosPorPresupuesto->has($p->id))
                            <br><small class="text-muted">(Ya se derivaron <strong>Bs {{ number_format($derivadosPorPresupuesto->get($p->id)->sum('monto_asignado'), 2) }}</strong> anteriormente)</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asesor (Coordinador/Asesor/Freelance) <span class="text-danger">*</span></label>
                            <select name="asesor_id" class="form-select" required>
                                <option value="">— Seleccionar —</option>
                                @foreach($asesoresDerivados as $a)
                                    <option value="{{ $a->id }}">{{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto a derivar (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="{{ $p->monto_asignado }}" name="monto_asignado" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Respaldo de transferencia</label>
                            <input type="file" name="archivo_derivacion" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                            <small class="text-muted fs-11">Imagen o PDF (máx. 5 MB)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="form-control" placeholder="Motivo de la derivación"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info"><i class="ri-share-forward-line me-1"></i>Derivar presupuesto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan
@endforeach

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

{{-- Modales de verificación --}}
@can('marketing.publicidad.verificar')
@foreach($comprobantes as $c)
<div class="modal fade modal-verif" id="modalVerif{{ $c->id }}" tabindex="-1" aria-hidden="true" data-comprobante-id="{{ $c->id }}" data-presupuesto-id="{{ $c->presupuesto_id }}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form class="form-verificar-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.verificar', $c->id) }}" novalidate>
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-checkbox-circle-line text-success me-1"></i>Verificar comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-2">
                                <span class="badge bg-primary fs-10 px-2 py-1"><i class="ri-attachment-2"></i> Comprobante</span>
                            </div>
                            <div class="p-2 rounded bg-light d-flex align-items-center justify-content-center" style="min-height:300px;border:1px solid #eef0f4;">
                                @php
                                    $ext = strtolower(pathinfo($c->archivo_path, PATHINFO_EXTENSION));
                                @endphp
                                @if(in_array($ext, ['jpg','jpeg','png','webp']))
                                    <img src="{{ asset('storage/'.$c->archivo_path) }}" class="img-fluid rounded" style="max-height:450px;object-fit:contain;" alt="Comprobante">
                                @elseif($ext === 'pdf')
                                    <iframe src="{{ asset('storage/'.$c->archivo_path) }}" class="w-100 rounded" style="height:450px;border:none;"></iframe>
                                @else
                                    <a href="{{ asset('storage/'.$c->archivo_path) }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="ri-file-download-line me-1"></i>Descargar comprobante
                                    </a>
                                @endif
                            </div>
                            @if($c->archivo_debito_path)
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark fs-10 px-2 py-1"><i class="ri-bank-card-line"></i> Comprobante de débito de meta</span>
                            </div>
                            <div class="p-2 rounded bg-light d-flex align-items-center justify-content-center" style="min-height:200px;border:1px solid #eef0f4;">
                                @php
                                    $extDeb = strtolower(pathinfo($c->archivo_debito_path, PATHINFO_EXTENSION));
                                @endphp
                                @if(in_array($extDeb, ['jpg','jpeg','png','webp']))
                                    <img src="{{ asset('storage/'.$c->archivo_debito_path) }}" class="img-fluid rounded" style="max-height:300px;object-fit:contain;" alt="Débito">
                                @elseif($extDeb === 'pdf')
                                    <iframe src="{{ asset('storage/'.$c->archivo_debito_path) }}" class="w-100 rounded" style="height:300px;border:none;"></iframe>
                                @else
                                    <a href="{{ asset('storage/'.$c->archivo_debito_path) }}" target="_blank" class="btn btn-outline-warning">
                                        <i class="ri-file-download-line me-1"></i>Descargar débito
                                    </a>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Asesor</label>
                                <p class="fw-semibold mb-0 fs-14">{{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Periodo</label>
                                @php
                                    $vfi = \Carbon\Carbon::parse($c->fecha_inicio)->locale('es');
                                    $vff = \Carbon\Carbon::parse($c->fecha_fin)->locale('es');
                                @endphp
                                <p class="mb-0 fs-14">{{ $vfi->isoFormat('D [de] MMMM [del] YYYY') }} → {{ $vff->isoFormat('D [de] MMMM [del] YYYY') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Monto</label>
                                <p class="fw-bold text-success mb-0 fs-14">Bs {{ number_format($c->monto, 2) }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Plataforma</label>
                                <p class="mb-0 fs-14">{{ $c->plataforma }}</p>
                            </div>
                            @if($c->campana_nombre)
                            <div class="mb-3">
                                <label class="form-label">Campaña</label>
                                <p class="mb-0 fs-14"><i class="ri-hashtag"></i> {{ $c->campana_nombre }}</p>
                            </div>
                            @endif
                            @if($c->enlace_campana)
                            <div class="mb-3">
                                <label class="form-label">Enlace de la campaña</label>
                                <p class="mb-0 fs-14"><a href="{{ $c->enlace_campana }}" target="_blank" rel="noopener"><i class="ri-external-link-line"></i> Abrir campaña</a></p>
                            </div>
                            @endif
                            @if($c->pagina_facebook)
                            <div class="mb-3">
                                <label class="form-label">Página de Facebook</label>
                                <p class="mb-0 fs-14"><a href="{{ $c->pagina_facebook }}" target="_blank" rel="noopener"><i class="ri-facebook-line"></i> Abrir página</a></p>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">Programa</label>
                                <p class="mb-0 fs-14">{{ $c->programa_nombre ?? '—' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <p class="mb-0 fs-13 text-muted">{{ $c->descripcion }}</p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Resultado <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select select-estado-verif" required>
                                    <option value="aprobado" @selected($c->estado === 'aprobado')>Aprobar</option>
                                    <option value="aprobado_obs" @selected($c->estado === 'aprobado_obs')>Aprobado con observaciones</option>
                                    <option value="rechazado" @selected($c->estado === 'rechazado')>Rechazar</option>
                                </select>
                            </div>
                            <div class="mb-3 div-monto-aprobado" style="display:none;">
                                <label class="form-label">Monto aprobado (Bs)</label>
                                <input type="number" step="0.01" min="0" name="monto_aprobado" class="form-control" value="{{ $c->monto_aprobado ?? $c->monto }}" placeholder="{{ $c->monto }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones_contable" rows="3" class="form-control" placeholder="Comentario del área contable">{{ $c->observaciones_contable }}</textarea>
                            </div>
                            <div class="alert alert-danger d-none error-verif" id="error-verif-{{ $c->id }}"></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-soft-secondary flex-fill" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary flex-fill btn-guardar-verif">
                                    <i class="ri-save-3-line me-1"></i>Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endcan

{{-- Modales de edición de comprobante --}}
@can('marketing.publicidad.subir')
@foreach($comprobantes as $c)
@if($c->estado !== 'aprobado')
@php
    $presupuestoEdit = $presupuestos->firstWhere('id', $c->presupuesto_id);
    $fechaMinE = $presupuestoEdit ? sprintf('%04d-%02d-%02d', $presupuestoEdit->gestion, $presupuestoEdit->mes, $presupuestoEdit->quincena === 1 ? 1 : 16) : '';
    $fechaMaxE = $presupuestoEdit ? sprintf('%04d-%02d-%02d', $presupuestoEdit->gestion, $presupuestoEdit->mes, $presupuestoEdit->quincena === 1 ? 15 : \Carbon\Carbon::create($presupuestoEdit->gestion, $presupuestoEdit->mes, 1)->endOfMonth()->day) : '';
@endphp
<div class="modal fade" id="modalEditar{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form class="form-editar-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.update', $c->id) }}" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="presupuesto_id" value="{{ $c->presupuesto_id }}">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-edit-2-line me-1"></i>Editar comprobante</h5>
                        <small class="text-muted">
                            @if($c->asesor_apellidos)
                                Asesor: {{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }} ·
                            @endif
                            Q{{ $c->quincena }} {{ $meses[$c->mes] }} {{ $c->gestion }}
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info fs-12 py-2 mb-3">
                        <i class="ri-information-line me-1"></i> Modifica los campos que necesites. Los campos de archivo son opcionales: si no seleccionas uno nuevo, se mantendrá el actual.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Asesor que realizó el gasto <span class="text-danger">*</span></label>
                            <select name="asesor_id" class="form-select validate-required" required>
                                <option value="">— Seleccionar asesor —</option>
                                @foreach($asesores as $a)
                                    <option value="{{ $a->id }}" @selected((int)$c->asesor_id === (int)$a->id)>
                                        {{ $a->cargo_nombre }} - {{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Seleccione el asesor que realizó el gasto.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control validate-required" min="{{ $fechaMinE }}" max="{{ $fechaMaxE }}" value="{{ $c->fecha_inicio }}" required>
                            <div class="invalid-feedback">La fecha de inicio es obligatoria.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_fin" class="form-control validate-required validate-after" min="{{ $fechaMinE }}" max="{{ $fechaMaxE }}" value="{{ $c->fecha_fin }}" required>
                            <div class="invalid-feedback">La fecha de fin debe ser igual o posterior a la de inicio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control validate-required" value="{{ $c->monto }}" required>
                            <div class="invalid-feedback">Ingrese un monto válido mayor a 0.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plataforma</label>
                            <select name="plataforma" class="form-select">
                                <option value="Facebook" @selected($c->plataforma === 'Facebook')>Facebook Ads</option>
                                <option value="Instagram" @selected($c->plataforma === 'Instagram')>Instagram Ads</option>
                                <option value="TikTok" @selected($c->plataforma === 'TikTok')>TikTok Ads</option>
                                <option value="Google" @selected($c->plataforma === 'Google')>Google Ads</option>
                                <option value="Otro" @selected($c->plataforma === 'Otro')>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre de la campaña</label>
                            <input type="text" name="campana_nombre" class="form-control" value="{{ $c->campana_nombre }}" placeholder="Ej: Diplomado en Auditoría - Julio">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Enlace de la campaña</label>
                            <input type="url" name="enlace_campana" class="form-control validate-url" value="{{ $c->enlace_campana }}" placeholder="https://www.facebook.com/ads/...">
                            <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Programa promocionado</label>
                            <select name="programa_id" class="form-select">
                                <option value="">— Sin programa —</option>
                                @foreach($programas as $prg)
                                    <option value="{{ $prg->id }}" @selected((int)$c->programa_id === (int)$prg->id)>{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Página de Facebook</label>
                            <input type="url" name="pagina_facebook" class="form-control validate-url" value="{{ $c->pagina_facebook }}" placeholder="https://www.facebook.com/tupagina">
                            <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción del gasto <span class="text-danger">*</span></label>
                            <textarea name="descripcion" rows="2" class="form-control validate-required" placeholder="Ej: Publicación pagada Facebook 3 días para promoción del diplomado" required>{{ $c->descripcion }}</textarea>
                            <div class="invalid-feedback">La descripción es obligatoria.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Comprobante (PDF o imagen, máx 5MB)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="invalid-feedback">Seleccione un archivo PDF o imagen (máx 5MB).</div>
                            @if($c->archivo_path)
                            <small class="text-muted fs-11">Archivo actual: <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_path) }}">{{ $c->archivo_original }}</a></small>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Comprobante de débito de meta (PDF o imagen, máx 5MB)</label>
                            <input type="file" name="archivo_debito" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="invalid-feedback">Seleccione un archivo PDF o imagen (máx 5MB).</div>
                            @if($c->archivo_debito_path)
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-muted fs-11">Archivo actual: <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_debito_path) }}">{{ $c->archivo_debito_original }}</a></small>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="eliminar_archivo_debito" id="elimDeb{{ $c->id }}" value="1">
                                    <label class="form-check-label text-danger fs-11" for="elimDeb{{ $c->id }}">Eliminar</label>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <div class="alert alert-danger d-none" id="error-global-edit-{{ $c->id }}"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-2 w-100 justify-content-between">
                        <div class="spinner-border spinner-border-sm d-none text-primary" role="status" id="spinner-edit-{{ $c->id }}">
                            <span class="visually-hidden">Guardando...</span>
                        </div>
                        <div class="d-flex gap-2 ms-auto">
                            <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-editar"><i class="ri-save-3-line me-1"></i>Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endcan

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // File preview modal
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

    // Clean up file preview when modal is closed
    document.getElementById('modalFilePreview')?.addEventListener('hidden.bs.modal', function () {
        document.getElementById('filePreviewContent').innerHTML = '<div class="text-center p-4"><i class="ri-loader-4-line ri-2x spin"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>';
    });

    // Real-time validation on blur/change
    document.querySelectorAll('.form-subir-comprobante input, .form-subir-comprobante select, .form-subir-comprobante textarea').forEach(el => {
        el.addEventListener('blur', function () { validateField(this); });
        el.addEventListener('change', function () { validateField(this); });
        if (el.type === 'file') {
            el.addEventListener('change', function () { validateField(this); });
        }
    });

    function validateField(el) {
        const form = el.closest('form');
        let valid = true;
        const val = el.value.trim();

        if (el.classList.contains('validate-required')) {
            if (!val) { valid = false; }
        }

        if (el.name === 'fecha_inicio' || el.name === 'fecha_fin') {
            const modal = el.closest('.modal');
            const fechaMin = modal?.dataset.fechaMin;
            const fechaMax = modal?.dataset.fechaMax;
            if (fechaMin && val && val < fechaMin) { valid = false; }
            if (fechaMax && val && val > fechaMax) { valid = false; }
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
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
                if (el.files[0].size > maxSize) { valid = false; }
            }
        }

        if (el.name === 'fecha_fin' && el.classList.contains('validate-after')) {
            const inicio = form.querySelector('[name=fecha_inicio]');
            if (inicio) {
                if (val && inicio.value && val < inicio.value) {
                    el.setCustomValidity('La fecha de fin debe ser igual o posterior a la de inicio.');
                    el.classList.add('is-invalid');
                    el.classList.remove('is-valid');
                    return;
                } else {
                    el.setCustomValidity('');
                }
                validateField(inicio);
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

    // AJAX form submission
    document.querySelectorAll('.form-subir-comprobante').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate all fields
            let allValid = true;
            this.querySelectorAll('input, select, textarea').forEach(el => {
                validateField(el);
                if (el.classList.contains('is-invalid') || (el.hasAttribute('required') && !el.value.trim())) {
                    allValid = false;
                }
            });

            if (!allValid) {
                this.querySelector('.is-invalid')?.focus();
                return;
            }

            const modal = this.closest('.modal');
            const presupuestoId = modal.dataset.presupuestoId;
            const spinner = document.getElementById('spinner-' + presupuestoId);
            const btn = this.querySelector('.btn-subir');
            const errorGlobal = document.getElementById('error-global-' + presupuestoId);

            spinner?.classList.remove('d-none');
            btn.disabled = true;
            errorGlobal?.classList.add('d-none');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async res => {
                let data;
                try { data = await res.json(); } catch (e) { data = null; }
                if (!data) {
                    throw new Error('Respuesta inválida del servidor (código ' + res.status + ').');
                }
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
                    this.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                        el.classList.remove('is-valid', 'is-invalid');
                    });

                    const table = document.getElementById('comprobantes-' + presupuestoId);
                    if (table) {
                        const tbody = table.querySelector('tbody');
                        const emptyRow = tbody.querySelector('.empty-row');
                        if (emptyRow) emptyRow.remove();

                        const temp = document.createElement('tbody');
                        temp.innerHTML = data.html;
                        tbody.appendChild(temp.firstElementChild);
                        initPagination(table);
                    }

                    const bsModal = bootstrap.Modal.getInstance(modal);
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
    });

    // AJAX edit form submission
    document.querySelectorAll('.form-editar-comprobante').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            let allValid = true;
            this.querySelectorAll('input, select, textarea').forEach(el => {
                validateField(el);
                if (el.classList.contains('is-invalid') || (el.hasAttribute('required') && !el.value.trim())) {
                    allValid = false;
                }
            });

            if (!allValid) {
                this.querySelector('.is-invalid')?.focus();
                return;
            }

            const modal = this.closest('.modal');
            const modalId = modal.id.replace('modalEditar', '');
            const spinner = document.getElementById('spinner-edit-' + modalId);
            const btn = this.querySelector('.btn-editar');
            const errorGlobal = document.getElementById('error-global-edit-' + modalId);

            spinner?.classList.remove('d-none');
            btn.disabled = true;
            errorGlobal?.classList.add('d-none');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async res => {
                let data;
                try { data = await res.json(); } catch (e) { data = null; }
                if (!data) {
                    throw new Error('Respuesta inválida del servidor (código ' + res.status + ').');
                }
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
                    const table = document.getElementById('comprobantes-' + this.querySelector('[name="presupuesto_id"]').value);
                    if (table) {
                        const oldRow = table.querySelector('tbody tr[data-id="' + modalId + '"]');
                        if (oldRow) {
                            const temp = document.createElement('tbody');
                            temp.innerHTML = data.html;
                            const newRow = temp.firstElementChild;
                            if (newRow) {
                                newRow.setAttribute('data-id', modalId);
                                oldRow.replaceWith(newRow);
                                initPagination(table);
                            }
                        }
                    }

                    const bsModal = bootstrap.Modal.getInstance(modal);
                    bsModal?.hide();

                    const toastHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;max-width:500px;" role="alert">' +
                        '<i class="ri-check-double-line me-1"></i>' + data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    document.body.insertAdjacentHTML('beforeend', toastHtml);
                    setTimeout(() => document.querySelector('.alert-success')?.remove(), 4000);

                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    if (errorGlobal) {
                        errorGlobal.textContent = data.message || 'Error al actualizar el comprobante.';
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
    });

    // Toggle monto_aprobado field on estado change
    document.querySelectorAll('.modal-verif').forEach(modal => {
        const select = modal.querySelector('.select-estado-verif');
        const divMonto = modal.querySelector('.div-monto-aprobado');
        if (select && divMonto) {
            function toggleMonto() {
                divMonto.style.display = select.value === 'aprobado_obs' ? 'block' : 'none';
            }
            select.addEventListener('change', toggleMonto);
            toggleMonto();
        }
    });

    // AJAX verification
    document.querySelectorAll('.form-verificar-comprobante').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const modal = this.closest('.modal');
            const comprobanteId = modal.dataset.comprobanteId;
            const presupuestoId = modal.dataset.presupuestoId;
            const btn = this.querySelector('.btn-guardar-verif');
            const errorDiv = document.getElementById('error-verif-' + comprobanteId);
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            errorDiv?.classList.add('d-none');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async res => {
                let data;
                try { data = await res.json(); } catch (e) { data = null; }
                if (!data) throw new Error('Respuesta inválida del servidor.');
                return { status: res.status, data };
            })
            .then(({ status, data }) => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;

                if (status === 422 && data.errors) {
                    let msg = Object.values(data.errors).flat().join('<br>');
                    if (errorDiv) {
                        errorDiv.innerHTML = msg;
                        errorDiv.classList.remove('d-none');
                    }
                    return;
                }

                if (data.success) {
                    // Replace row in table
                    const table = document.getElementById('comprobantes-' + presupuestoId);
                    if (table) {
                        const oldRow = table.querySelector('tbody tr[data-id="' + comprobanteId + '"]');
                        if (oldRow) {
                            const temp = document.createElement('tbody');
                            temp.innerHTML = data.html;
                            const newRow = temp.firstElementChild;
                            if (newRow) {
                                newRow.setAttribute('data-id', comprobanteId);
                                oldRow.replaceWith(newRow);
                                initPagination(table);
                            }
                        }
                    }

                    const bsModal = bootstrap.Modal.getInstance(modal);
                    bsModal?.hide();

                    const toastHtml = '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;max-width:500px;" role="alert">' +
                        '<i class="ri-check-double-line me-1"></i>' + data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    document.body.insertAdjacentHTML('beforeend', toastHtml);
                    setTimeout(() => document.querySelector('.alert-success')?.remove(), 4000);

                    // Reload after a short delay
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
    // -----------------------------------------------------------------------
    // Paginación por tabla individual (solo para callbacks AJAX temporales)
    // -----------------------------------------------------------------------
    function initPagination(tbl) {
        // Se mantiene como fallback para AJAX; la paginación real
        // la maneja initTabPagination a nivel de tab.
    }

    // -----------------------------------------------------------------------
    // Paginación UNIFICADA de comprobantes a nivel de tab (10 por página)
    // Recoge TODOS los tr[data-id] de todas las tablas del tab y los
    // pagina como una lista plana. Oculta/muestra presupuestos, grupos
    // y wrappers de asesor según corresponda.
    // -----------------------------------------------------------------------
    function initTabPagination(tabId) {
        try {
            var tab = document.getElementById(tabId);
            if (!tab) return;

            var info = tab.querySelector('.tab-info');
            var nav  = tab.querySelector('.tab-paginator-nav');
            var cont = tab.querySelector('.tab-paginator') || tab;
            if (!nav) return;

            // ─── Ocultar paginadores individuales por tabla ───
            var perTblPag = cont.querySelectorAll('.pagination-comprobantes, .cp-wrap');
            for (var h = 0; h < perTblPag.length; h++) perTblPag[h].style.display = 'none';

            // ─── Recoger TODAS las filas tr[data-id] del tab ───
            var dataRows = Array.prototype.slice.call(
                cont.querySelectorAll('.tabla-pub[id^="comprobantes-"] tbody tr[data-id]')
            );
            var total = dataRows.length;

            // ─── Recoger presupuesto items ───
            var presItems = Array.prototype.slice.call(
                cont.querySelectorAll('.presupuesto-item')
            );

            // ─── Sin comprobantes: mostrar todo y salir ───
            if (total === 0) {
                for (var pi = 0; pi < presItems.length; pi++) presItems[pi].style.display = '';
                var wrapChildren = cont.children;
                for (var wc = 0; wc < wrapChildren.length; wc++) wrapChildren[wc].style.display = '';
                nav.innerHTML = '';
                if (info) info.innerHTML = '<i class="ri-inbox-2-line text-muted me-1"></i>Sin comprobantes registrados';
                return;
            }

            // ─── Construir grupo lógico por tabla ───
            // Para cada tabla construimos: [{header, rows[], footer}]
            // Esto permite ocultar/mostrar las filas de encabezado de
            // asesor y subtotales correctamente.
            var tableMap = {};  // tblId → { presItem, groups[] }

            var allTables = cont.querySelectorAll('.tabla-pub[id^="comprobantes-"]');
            for (var t = 0; t < allTables.length; t++) {
                var tbl = allTables[t];
                var tbody = tbl.querySelector('tbody');
                if (!tbody) continue;

                var pi = tbl.closest('.presupuesto-item');
                var tblRows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                var groups = [];
                var cg = null;

                for (var r = 0; r < tblRows.length; r++) {
                    var row = tblRows[r];
                    if (row.hasAttribute('data-id')) {
                        if (!cg) { cg = { header: null, rows: [], footer: null }; groups.push(cg); }
                        cg.rows.push(row);
                    } else if (row.classList.contains('empty-row')) {
                        row.style.display = 'none';
                    } else {
                        if (cg && cg.rows.length > 0 && !cg.footer) {
                            cg.footer = row;
                            cg = null;
                        } else {
                            cg = { header: row, rows: [], footer: null };
                            groups.push(cg);
                        }
                    }
                }

                tableMap[tbl.id] = { presItem: pi, groups: groups };
            }

            // ─── Paginación ───
            var perPage = 10;
            var pages   = Math.ceil(total / perPage);
            if (pages < 1) pages = 1;
            var current = 1;

            function showPage(pg) {
                current = Math.max(1, Math.min(pg, pages));
                var start = (current - 1) * perPage;
                var end   = Math.min(current * perPage, total);

                // 1. Marcar visibilidad de cada fila de comprobante
                for (var i = 0; i < total; i++) {
                    dataRows[i].style.display = (i >= start && i < end) ? '' : 'none';
                }

                // 2. Para cada tabla: mostrar/ocultar headers y footers de grupo
                //    y determinar si el presupuesto-item tiene alguna fila visible.
                var visiblePresItems = [];

                for (var tblId in tableMap) {
                    if (!tableMap.hasOwnProperty(tblId)) continue;
                    var ti = tableMap[tblId];
                    var tableHasVisible = false;

                    for (var g = 0; g < ti.groups.length; g++) {
                        var grp = ti.groups[g];
                        var groupHasVisible = false;

                        for (var ri = 0; ri < grp.rows.length; ri++) {
                            if (grp.rows[ri].style.display !== 'none') {
                                groupHasVisible = true;
                            }
                        }

                        if (grp.header) grp.header.style.display = groupHasVisible ? '' : 'none';
                        if (grp.footer) grp.footer.style.display = groupHasVisible ? '' : 'none';
                        if (groupHasVisible) tableHasVisible = true;
                    }

                    if (ti.presItem && tableHasVisible) {
                        visiblePresItems.push(ti.presItem);
                    }
                }

                // 3. Ocultar/mostrar presupuesto-items
                for (var p = 0; p < presItems.length; p++) {
                    var esVisible = false;
                    for (var v = 0; v < visiblePresItems.length; v++) {
                        if (visiblePresItems[v] === presItems[p]) { esVisible = true; break; }
                    }
                    presItems[p].style.display = esVisible ? '' : 'none';
                }

                // 4. Ocultar/mostrar wrappers de asesor (hijos directos de cont)
                var children = cont.children;
                for (var c = 0; c < children.length; c++) {
                    var child = children[c];
                    // Saltar si es un presupuesto-item directo
                    if (child.classList.contains('presupuesto-item')) continue;
                    // Buscar si este hijo contiene algún presupuesto-item visible
                    var innerPis = child.querySelectorAll('.presupuesto-item');
                    if (innerPis.length > 0) {
                        var hasVisibleChild = false;
                        for (var ip = 0; ip < innerPis.length; ip++) {
                            if (innerPis[ip].style.display !== 'none') {
                                hasVisibleChild = true;
                                break;
                            }
                        }
                        child.style.display = hasVisibleChild ? '' : 'none';
                    }
                }

                // 5. Info
                if (info) {
                    if (pages <= 1) {
                        info.innerHTML = '<i class="ri-file-list-3-line text-muted me-1"></i>' + total + ' comprobante(s)';
                    } else {
                        info.innerHTML = '<i class="ri-file-list-3-line text-muted me-1"></i>Mostrando ' + (start + 1) + '\u2013' + end + ' de ' + total + ' comprobante(s)';
                    }
                }

                renderNav();
            }

            function renderNav() {
                if (pages <= 1) {
                    nav.innerHTML = '<div class="d-flex align-items-center justify-content-center gap-1 flex-wrap"><span class="text-muted fs-12"><i class="ri-file-list-3-line me-1"></i>' + total + ' comprobante(s)</span></div>';
                    return;
                }

                var start = (current - 1) * perPage + 1;
                var end   = Math.min(current * perPage, total);

                var h = '<div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">';

                // Botón anterior
                h += '<button class="btn btn-sm btn-icon rounded-pill ' + (current <= 1 ? 'btn-soft-secondary disabled' : 'btn-soft-primary') + ' pg-btn" data-page="' + (current - 1) + '" style="width:32px;height:32px;" title="Anterior"><i class="ri-arrow-left-s-line"></i></button>';

                // Números de página
                h += '<div class="d-flex align-items-center gap-1 mx-1">';
                var s = Math.max(1, current - 2);
                var e = Math.min(pages, current + 2);

                if (s > 1) {
                    h += '<button class="btn btn-sm rounded-circle pg-btn" data-page="1" style="width:32px;height:32px;font-size:.78rem;font-weight:600;border:1px solid #dde3ed;background:#fff;color:#495057;">1</button>';
                    if (s > 2) h += '<span class="text-muted px-1 fs-13">…</span>';
                }
                for (var i = s; i <= e; i++) {
                    if (i === current) {
                        h += '<button class="btn btn-sm rounded-circle text-white pg-btn" data-page="' + i + '" style="width:32px;height:32px;font-size:.78rem;font-weight:700;background:#405189;border:1px solid #405189;box-shadow:0 2px 6px rgba(64,81,137,.3);">' + i + '</button>';
                    } else {
                        h += '<button class="btn btn-sm rounded-circle pg-btn" data-page="' + i + '" style="width:32px;height:32px;font-size:.78rem;font-weight:600;border:1px solid #dde3ed;background:#fff;color:#495057;">' + i + '</button>';
                    }
                }
                if (e < pages) {
                    if (e < pages - 1) h += '<span class="text-muted px-1 fs-13">…</span>';
                    h += '<button class="btn btn-sm rounded-circle pg-btn" data-page="' + pages + '" style="width:32px;height:32px;font-size:.78rem;font-weight:600;border:1px solid #dde3ed;background:#fff;color:#495057;">' + pages + '</button>';
                }
                h += '</div>';

                // Botón siguiente
                h += '<button class="btn btn-sm btn-icon rounded-pill ' + (current >= pages ? 'btn-soft-secondary disabled' : 'btn-soft-primary') + ' pg-btn" data-page="' + (current + 1) + '" style="width:32px;height:32px;" title="Siguiente"><i class="ri-arrow-right-s-line"></i></button>';

                // Info
                h += '<span class="text-muted fs-12 ms-2"><i class="ri-file-list-3-line me-1"></i>Mostrando ' + start + '–' + end + ' de ' + total + '</span>';
                h += '</div>';

                nav.innerHTML = h;

                var btns = nav.querySelectorAll('.pg-btn:not(.disabled)');
                for (var b = 0; b < btns.length; b++) {
                    (function(boton) {
                        boton.addEventListener('click', function () {
                            var n = parseInt(this.getAttribute('data-page'));
                            if (!isNaN(n)) {
                                showPage(n);
                                // Scroll suave al inicio del contenido del tab
                                cont.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        });
                    })(btns[b]);
                }
            }

            showPage(1);
        } catch (ex) {
            if (typeof console !== 'undefined') console.error('Error en initTabPagination:', ex);
        }
    }

    // ─── Inicialización al cargar la página ───
    // Activar paginación en el tab activo
    (function () {
        var activeTabs = document.querySelectorAll('.tab-pane.active');
        for (var t = 0; t < activeTabs.length; t++) {
            if (activeTabs[t].id) initTabPagination(activeTabs[t].id);
        }
    })();

    // Observer: detectar cambio de tab por Bootstrap
    var observer = new MutationObserver(function (mutations) {
        for (var m = 0; m < mutations.length; m++) {
            if (mutations[m].type === 'attributes' && mutations[m].attributeName === 'class') {
                var pane = mutations[m].target;
                if (pane.id && pane.classList.contains('active')) {
                    initTabPagination(pane.id);
                }
            }
        }
    });
    var panes = document.querySelectorAll('.tab-pane');
    for (var p = 0; p < panes.length; p++) {
        observer.observe(panes[p], { attributes: true, attributeFilter: ['class'] });
    }

    // Fallback: clic directo en los tab links
    (function () {
        var links = document.querySelectorAll('[data-bs-toggle="tab"]');
        for (var l = 0; l < links.length; l++) {
            (function (link) {
                link.addEventListener('click', function () {
                    var href = this.getAttribute('href');
                    if (href && href !== '#') {
                        setTimeout(function () {
                            initTabPagination(href.replace('#', ''));
                        }, 80);
                    }
                });
            })(links[l]);
        }
    })();
});
</script>
@endpush

</div>{{-- /mkt-page --}}
@endsection
