@extends('admin.layouts.master')

@section('title', 'Comprobantes de Pago - ' . config('app.name'))

@push('css')
<style>
.comp-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.comp-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.comp-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.comp-hero > * { position: relative; z-index: 1; }
.comp-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px);
}
.comp-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.comp-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }

/* Stats row */
.comp-stat { border-radius: 12px; padding: .95rem 1rem; border: 1px solid #eef0f4; background: #fff; display: flex; align-items: center; gap: .85rem; box-shadow: 0 2px 8px -6px rgba(64,81,137,.12); }
.comp-stat .cs-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.2rem; flex-shrink: 0; }
.comp-stat .cs-info { flex: 1; min-width: 0; }
.comp-stat .cs-info .cs-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; color: #878a99; }
.comp-stat .cs-info .cs-value { font-size: 1.15rem; font-weight: 800; line-height: 1.3; }
.comp-stat .cs-info .cs-sub { font-size: .7rem; color: #878a99; }

/* Status styles */
.badge-estado { display: inline-flex; align-items: center; gap: 4px; padding: .25em .7em; border-radius: 999px; font-size: .68rem; font-weight: 700; border: 1px solid; white-space: nowrap; }
.badge-estado.pendiente { background: #fef3c7; color: #d97706; border-color: #fde68a; }
.badge-estado.verificado { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.badge-estado.rechazado { background: #fee2e2; color: #f06548; border-color: #fecaca; }

/* Table */
.comp-table { font-size: .8rem; }
.comp-table thead th { font-size: .63rem; text-transform: uppercase; letter-spacing: .4px; color: #6c757d; font-weight: 700; border-bottom: 2px solid #e9ebec !important; padding: .7rem .6rem; vertical-align: middle; background: #fafbfd; }
.comp-table td { vertical-align: middle; padding: .65rem .6rem; border-bottom: 1px solid #f1f3f5; }
.comp-table tbody tr { transition: background .15s; }
.comp-table tbody tr:hover { background-color: #f6f8fa; }
.comp-table .td-monto { font-weight: 800; font-size: .88rem; white-space: nowrap; text-align: right; }
.comp-table .td-participante { min-width: 140px; }

/* Accent dots for estado */
.comp-estado-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }
.comp-estado-dot.pendiente { background: #d97706; }
.comp-estado-dot.verificado { background: #0ab39c; }
.comp-estado-dot.rechazado { background: #f06548; }

/* Deuda rows */
.deuda-row {
    display: flex; align-items: center; gap: .6rem;
    padding: .5rem .7rem; border: 1px solid #eef0f4; border-radius: 8px;
    margin-bottom: .4rem; background: #fff; flex-wrap: wrap;
}
.deuda-row .dr-tipo { font-size: .63rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; padding: .18em .55em; border-radius: 6px; white-space: nowrap; }
.deuda-row .dr-tipo.Matricula { background: #dbeafe; color: #299cdb; }
.deuda-row .dr-tipo.Colegiatura { background: #ecfdf5; color: #0ab39c; }
.deuda-row .dr-label { flex: 1; font-weight: 600; font-size: .82rem; min-width: 100px; }
.deuda-row .dr-info { font-size: .72rem; color: #6c757d; white-space: nowrap; }
.deuda-row .dr-input { width: 110px; }
.deuda-row .dr-input input {
    width: 100%; border: 1px solid #d4d8e0; border-radius: 6px;
    padding: .28rem .45rem; font-weight: 700; font-size: .82rem; text-align: right;
}
.deuda-row .dr-input input:focus { border-color: #0ab39c; outline: 0; box-shadow: 0 0 0 .15rem rgba(10,179,156,.12); }
.deuda-row.seleccionada { background: #ecfdf5; border-color: #0ab39c; box-shadow: 0 0 0 1px rgba(10,179,156,.2); }
.deuda-row.seleccionada .dr-tipo { border-color: #0ab39c !important; }
.deuda-row .dr-badge-asesor { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; padding: .15em .5em; border-radius: 6px; background: #d1fae5; color: #0ab39c; white-space: nowrap; }
.deuda-row .dr-restante { font-size: .7rem; font-weight: 700; white-space: nowrap; min-width: 85px; text-align: right; }
.deuda-row .dr-restante.positivo { color: #0ab39c; }
.deuda-row .dr-restante.cero { color: #878a99; }
.deuda-row .dr-restante.negativo { color: #f06548; }
.deuda-row .dr-pagando { font-size: .7rem; font-weight: 700; white-space: nowrap; color: #299cdb; min-width: 75px; text-align: right; }

/* Filters bar */
.filtros-bar { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: .75rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: .65rem; flex-wrap: wrap; }
.filtros-bar .form-control, .filtros-bar .form-select { border-radius: 8px; font-size: .8rem; padding: .3rem .6rem; border-color: #d4d8e0; }
.filtros-bar .form-control:focus, .filtros-bar .form-select:focus { border-color: #0ab39c; box-shadow: 0 0 0 .12rem rgba(10,179,156,.12); }

/* Table btn-sm icon */
.btn-icon-table { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e5ea; background: #fff; color: #495057; font-size: .85rem; transition: all .15s; }
.btn-icon-table:hover { background: #f0f2f5; border-color: #c5cad2; color: #212529; }
</style>
@endpush

@push('css-after')
<link href="{{ asset('backend/assets/css/avip-contable.css') }}?v={{ @filemtime(public_path('backend/assets/css/avip-contable.css')) ?: time() }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="cont-page">
<div class="comp-hero d-flex align-items-center gap-3 flex-wrap">
    <span class="hero-icon"><i class="ri-receipt-line"></i></span>
    <div style="flex:1;">
        <h4>Comprobantes de Pago</h4>
        <p class="hero-sub"><i class="ri-file-search-line"></i> Revisión y verificación de comprobantes adjuntos por asesores</p>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white-50">Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.contable.seguimiento') }}" class="text-white-50">Contable</a></li>
        <li class="breadcrumb-item active text-white">Comprobantes</li>
    </ol>
</div>

@php
    $totalMonto = $comprobantes->sum('monto');
    $stats = [
        'pendiente'  => $comprobantes->filter(fn($c) => $c->estado === 'pendiente')->count(),
        'verificado' => $comprobantes->filter(fn($c) => $c->estado === 'verificado')->count(),
        'rechazado'  => $comprobantes->filter(fn($c) => $c->estado === 'rechazado')->count(),
    ];
    $montoPendiente = $comprobantes->filter(fn($c) => $c->estado === 'pendiente')->sum('monto');
@endphp

<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="comp-stat" style="border-left:3px solid #f7b84b;">
            <span class="cs-icon" style="background:#fef3c7;color:#d97706;"><i class="ri-hourglass-line"></i></span>
            <div class="cs-info">
                <div class="cs-label">Pendientes</div>
                <div class="cs-value" style="color:#d97706;">{{ $stats['pendiente'] }}</div>
                <div class="cs-sub">por revisar · Bs {{ number_format($montoPendiente, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="comp-stat" style="border-left:3px solid #0ab39c;">
            <span class="cs-icon" style="background:#d1fae5;color:#0ab39c;"><i class="ri-check-line"></i></span>
            <div class="cs-info">
                <div class="cs-label">Verificados</div>
                <div class="cs-value" style="color:#0ab39c;">{{ $stats['verificado'] }}</div>
                <div class="cs-sub">comprobantes aprobados</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="comp-stat" style="border-left:3px solid #f06548;">
            <span class="cs-icon" style="background:#fee2e2;color:#f06548;"><i class="ri-close-line"></i></span>
            <div class="cs-info">
                <div class="cs-label">Rechazados</div>
                <div class="cs-value" style="color:#f06548;">{{ $stats['rechazado'] }}</div>
                <div class="cs-sub">comprobantes rechazados</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="comp-stat" style="border-left:3px solid #405189;">
            <span class="cs-icon" style="background:#eef1f8;color:#405189;"><i class="ri-file-list-3-line"></i></span>
            <div class="cs-info">
                <div class="cs-label">Total</div>
                <div class="cs-value" style="color:#405189;">{{ $comprobantes->total() }}</div>
                <div class="cs-sub">Bs {{ number_format($totalMonto, 2, ',', '.') }} en comprobantes</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="border:1px solid #eef0f4;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table comp-table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:85px;">Fecha</th>
                        <th>Asesor</th>
                        <th>Participante</th>
                        <th>Programa</th>
                        <th style="width:110px;" class="text-end">Monto</th>
                        <th style="width:115px;" class="text-center">Estado</th>
                        <th style="width:50px;" class="text-center">Archivo</th>
                        <th style="width:100px;" class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comprobantes as $c)
                    <tr>
                        <td class="text-muted fw-bold">{{ $c->id }}</td>
                        <td style="white-space:nowrap;">
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($c->created_at)->format('d/m') }}</span>
                            <small class="d-block text-muted" style="font-size:.65rem;">{{ \Carbon\Carbon::parse($c->created_at)->format('H:i') }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold" style="font-size:.8rem;">{{ $c->asesor_nombre ?? '—' }}</span>
                        </td>
                        <td class="td-participante">
                            <span class="fw-semibold">{{ $c->p_apellidos }}, {{ $c->p_nombres }}</span>
                            <small class="d-block text-muted" style="font-size:.65rem;"><i class="ri-passport-line"></i> {{ $c->p_ci }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold" style="font-size:.8rem;">{{ $c->prog_nombre }}</span>
                            <small class="d-block text-muted" style="font-size:.65rem;"><i class="ri-hashtag"></i> {{ $c->prog_codigo }} · {{ $c->prog_gestion }}</small>
                        </td>
                        <td class="td-monto">Bs {{ number_format($c->monto, 2, ',', '.') }}</td>
                        <td class="text-center">
                            @php
                                $eClass = match($c->estado) {
                                    'pendiente' => 'pendiente',
                                    'verificado' => 'verificado',
                                    'rechazado' => 'rechazado',
                                    default => '',
                                };
                                $eIcon = match($c->estado) {
                                    'pendiente' => 'hourglass-line',
                                    'verificado' => 'check-line',
                                    'rechazado' => 'close-line',
                                    default => 'question-line',
                                };
                            @endphp
                            <span class="badge-estado {{ $eClass }}">
                                <i class="ri-{{ $eIcon }}"></i> {{ ucfirst($c->estado) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($c->archivo_path)
                                <a href="{{ asset('storage/' . $c->archivo_path) }}" target="_blank" class="btn-icon-table" title="Ver archivo">
                                    <i class="ri-file-search-line"></i>
                                </a>
                            @else
                                <span class="text-muted" style="font-size:.7rem;">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($c->estado === 'pendiente')
                                <button type="button" class="btn btn-sm" style="background:linear-gradient(135deg,#0ab39c,#14b8a6);color:#fff;border:0;font-weight:600;border-radius:8px;padding:.3rem .65rem;font-size:.75rem;" onclick="verComprobante({{ $c->id }})">
                                    <i class="ri-check-double-line"></i> Revisar
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-soft-info" style="border-radius:8px;font-weight:600;font-size:.75rem;padding:.3rem .65rem;" onclick="verComprobante({{ $c->id }})">
                                    <i class="ri-eye-line"></i> Ver
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5"><i class="ri-inbox-2-line fs-2 d-block mb-1"></i>No hay comprobantes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($comprobantes->hasPages())
    <div class="card-footer" style="border-top:1px solid #eef0f4;padding:.75rem 1rem;">
        {{ $comprobantes->links() }}
    </div>
    @endif
</div>

{{-- Modal ver comprobante --}}
<div class="modal fade" id="modalVerComprobante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189,#5e72b8);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;"><i class="ri-receipt-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;">Comprobante #<span id="vc-id">—</span></h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="vc-subtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;" id="vc-body">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando datos del comprobante...</p></div>
            </div>
            <div class="modal-footer" id="vc-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;display:none;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
                <button type="button" class="btn" id="btn-rechazar-comp" style="background:#fee2e2;color:#f06548;font-weight:700;border:1px solid #fecaca;border-radius:8px;padding:.45rem 1rem;">
                    <i class="ri-close-circle-line"></i> Rechazar
                </button>
                <button type="button" class="btn" id="btn-verificar-comp" style="background:linear-gradient(135deg,#0ab39c,#14b8a6);color:#fff;font-weight:700;border:0;border-radius:8px;padding:.45rem 1rem;box-shadow:0 6px 14px -6px rgba(10,179,156,.5);">
                    <i class="ri-check-double-line"></i> Aprobar y Registrar Pago
                </button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /cont-page --}}
@endsection

@push('js')
<script>
let currentComprobanteId = null;

function verComprobante(id) {
    currentComprobanteId = id;
    const modal = new bootstrap.Modal(document.getElementById('modalVerComprobante'));
    document.getElementById('vc-body').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando datos del comprobante...</p></div>';
    document.getElementById('vc-footer').style.display = 'none';
    modal.show();

    fetch('{{ url("admin/comprobantes-pago") }}/' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) {
            document.getElementById('vc-body').innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-2"></i><p class="mt-2">' + (data.message || 'Error al cargar') + '</p></div>';
            return;
        }
        renderComprobante(data);
    })
    .catch(function () {
        document.getElementById('vc-body').innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-2"></i><p class="mt-2">Error de conexión</p></div>';
    });
}

function renderComprobante(data) {
    const c = data.comprobante;
    const deudas = data.deudas_detalle || [];

    document.getElementById('vc-id').textContent = c.id;
    document.getElementById('vc-subtitle').textContent = (c.prog_nombre || '—') + ' · ' + (c.prog_codigo || '—') + ' · ' + (c.prog_gestion || '—');

    const estadoHtml = c.estado === 'pendiente'
        ? '<span style="display:inline-flex;align-items:center;gap:4px;padding:.25em .75em;border-radius:999px;background:#fef3c7;color:#d97706;border:1px solid #fde68a;font-weight:700;font-size:.78rem;"><i class="ri-hourglass-line"></i> Pendiente</span>'
        : (c.estado === 'verificado'
            ? '<span style="display:inline-flex;align-items:center;gap:4px;padding:.25em .75em;border-radius:999px;background:#d1fae5;color:#0ab39c;border:1px solid #a7f3d8;font-weight:700;font-size:.78rem;"><i class="ri-check-line"></i> Verificado</span>'
            : '<span style="display:inline-flex;align-items:center;gap:4px;padding:.25em .75em;border-radius:999px;background:#fee2e2;color:#f06548;border:1px solid #fecaca;font-weight:700;font-size:.78rem;"><i class="ri-close-line"></i> Rechazado</span>');

    const isPdf = c.archivo_path && c.archivo_path.toLowerCase().endsWith('.pdf');
    const isImg = c.archivo_path && /\.(jpg|jpeg|png|webp)$/i.test(c.archivo_path);
    const archivoUrl = c.archivo_path ? '{{ asset("storage") }}/' + c.archivo_path : null;

    let archivoHtml = '<span class="text-muted fst-italic">Sin archivo</span>';
    if (archivoUrl) {
        if (isImg) {
            archivoHtml = '<img src="' + archivoUrl + '" class="img-fluid" style="max-height:300px;border-radius:10px;border:1px solid #eef0f4;" alt="Comprobante">';
        } else if (isPdf) {
            archivoHtml = '<iframe src="' + archivoUrl + '" style="width:100%;height:400px;border:1px solid #eef0f4;border-radius:10px;"></iframe>';
        } else {
            archivoHtml = '<a href="' + archivoUrl + '" target="_blank" class="btn btn-primary"><i class="ri-download-line"></i> Descargar archivo</a>';
        }
    }

    let tieneSeleccionadas = deudas.some(function (d) { return d.seleccionado; });
    let deudasHtml = '<div class="text-muted fst-italic">No hay deudas registradas para este programa.</div>';

    if (c.estado === 'verificado') {
        if (deudas.length > 0) {
            deudasHtml = '<div style="font-size:.7rem;color:#878a99;font-weight:700;margin-bottom:8px;"><i class="ri-checkbox-circle-line" style="color:#0ab39c;"></i> Pagos registrados en este comprobante:</div>';
            deudas.forEach(function (d) {
                var montoPagado = d.monto_pagado || 0;
                deudasHtml += '<div class="deuda-row seleccionada" style="cursor:default;">' +
                    '<span class="dr-tipo ' + d.tipo + '">' + d.tipo + '</span>' +
                    '<span class="dr-label">' + (d.modulo_nombre || '—') + '</span>' +
                    '<span style="font-weight:700;color:#0ab39c;">Bs ' + montoPagado.toFixed(2) + '</span>' +
                    '<span class="dr-info">Deuda: <strong>Bs ' + d.deuda.toFixed(2) + '</strong> · Saldo restante: Bs ' + d.saldo.toFixed(2) + '</span>' +
                '</div>';
            });
            deudasHtml += '<div style="display:flex;justify-content:flex-end;gap:.5rem;align-items:center;padding:.4rem .65rem;border-top:1px dashed #eef0f4;margin-top:.35rem;">' +
                '<span style="font-size:.82rem;color:#212529;font-weight:700;">Total verificado: Bs ' + parseFloat(c.monto || 0).toFixed(2) + '</span>' +
            '</div>';
        }
    } else if (c.estado === 'rechazado') {
        deudasHtml = '<div class="text-center py-3 text-muted"><i class="ri-close-circle-line fs-3" style="color:#f06548;"></i><p class="mt-2 mb-0">Comprobante rechazado. No se registraron pagos.</p></div>';
    } else if (deudas.length > 0) {
        deudasHtml = '';
        if (tieneSeleccionadas) {
            deudasHtml += '<div style="font-size:.7rem;color:#878a99;font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">' +
                '<span>Ingresa el monto a pagar para cada deuda:</span>' +
                '<span style="display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:700;padding:.2em .6em;border-radius:6px;background:#d1fae5;color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Seleccionado por asesor</span>' +
            '</div>';
        } else {
            deudasHtml = '<div style="font-size:.7rem;color:#878a99;font-weight:700;margin-bottom:8px;">Ingresa el monto a pagar para cada deuda:</div>';
        }
        // Monto total del comprobante (editable)
        deudasHtml += '<div style="display:flex;align-items:center;gap:.65rem;padding:.45rem .65rem;background:#f8f9fc;border:1px solid #eef0f4;border-radius:8px;margin-bottom:.6rem;flex-wrap:wrap;">' +
            '<span style="font-size:.72rem;font-weight:700;color:#495057;white-space:nowrap;"><i class="ri-money-dollar-circle-line"></i> Monto del comprobante:</span>' +
            '<input type="number" step="0.01" min="0" id="vc-monto-comprobante" value="' + parseFloat(c.monto || 0).toFixed(2) + '" style="width:130px;border:1px solid #d4d8e0;border-radius:6px;padding:.25rem .45rem;font-weight:700;font-size:.9rem;text-align:right;">' +
            '<span style="font-size:.72rem;color:#878a99;">' +
                'Suma deudas: <strong id="vc-suma-deudas">Bs 0.00</strong> ' +
                '<span id="vc-diferencia-msg" style="font-weight:700;"></span>' +
            '</span>' +
        '</div>';
        var totalSugerido = deudas.reduce(function (s, d) {
            return s + (d.seleccionado ? d.saldo : 0);
        }, 0);
        deudasHtml += '<div style="font-size:.7rem;color:#878a99;font-weight:700;margin-bottom:6px;" id="vc-suma-sugerida">Suma sugerida por asesor: <strong style="color:#212529;">Bs ' + totalSugerido.toFixed(2) + '</strong></div>';
        deudas.forEach(function (d) {
            var cls = d.seleccionado ? 'deuda-row seleccionada' : 'deuda-row';
            var badgeHtml = d.seleccionado ? '<span class="dr-badge-asesor"><i class="ri-user-star-line"></i> Asesor</span>' : '';
            var valorDefecto = d.seleccionado ? d.saldo.toFixed(2) : '0.00';
            deudasHtml += '<div class="' + cls + '" data-mp-id="' + d.id + '" data-saldo="' + d.saldo.toFixed(2) + '">' +
                '<span class="dr-tipo ' + d.tipo + '">' + d.tipo + '</span>' +
                '<span class="dr-label">' + (d.modulo_nombre || '—') + '</span>' +
                badgeHtml +
                '<span class="dr-info">Deuda: <strong>Bs ' + d.deuda.toFixed(2) + '</strong> · Saldo: Bs ' + d.saldo.toFixed(2) + '</span>' +
                '<span class="dr-input"><input type="number" step="0.01" min="0" max="' + d.saldo.toFixed(2) + '" class="monto-pagar" value="' + valorDefecto + '"></span>' +
                '<span class="dr-pagando">Paga: <strong class="paga-valor">Bs ' + valorDefecto + '</strong></span>' +
                '<span class="dr-restante positivo">Restante: <strong class="restante-valor">Bs ' + d.saldo.toFixed(2) + '</strong></span>' +
            '</div>';
        });
        deudasHtml += '<div style="display:flex;justify-content:flex-end;gap:.5rem;align-items:center;padding:.4rem .65rem;border-top:1px dashed #eef0f4;margin-top:.35rem;">' +
            '<span style="font-size:.78rem;color:#6c757d;">Total a registrar: <strong id="vc-total-montos" style="color:#212529;font-size:.9rem;">Bs ' + totalSugerido.toFixed(2) + '</strong></span>' +
        '</div>';
    }

    let html =
    '<div class="row g-3">' +
        '<div class="col-lg-7">' +
            '<div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem;">' +
                '<div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#878a99;margin-bottom:.65rem;"><i class="ri-information-line"></i> Información del comprobante</div>' +
                '<div class="row g-2">' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Participante</div><div class="fw-semibold">' + (c.p_apellidos || '') + ', ' + (c.p_nombres || '') + '</div><small class="text-muted"><i class="ri-passport-line"></i> ' + (c.p_ci || '') + '</small></div>' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Asesor</div><div class="fw-semibold">' + (c.asesor_nombre || '—') + '</div></div>' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Programa</div><div class="fw-semibold">' + (c.prog_nombre || '—') + '</div><small class="text-muted"><i class="ri-hashtag"></i> ' + (c.prog_codigo || '') + '</small></div>' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Monto del comprobante</div><div class="fw-bold" style="font-size:1.1rem;color:#212529;">Bs ' + parseFloat(c.monto || 0).toFixed(2) + '</div></div>' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Fecha de registro</div><div class="fw-semibold">' + (c.created_at ? new Date(c.created_at).toLocaleDateString('es-BO', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '—') + '</div></div>' +
                    '<div class="col-md-6"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Estado</div><div>' + estadoHtml + '</div></div>' +
                '</div>' +
                (c.observaciones ? '<div class="mt-2 pt-2" style="border-top:1px dashed #eef0f4;"><div style="font-size:.7rem;color:#878a99;font-weight:700;">Observaciones</div><div class="mt-1" style="background:#f8f9fc;border-radius:8px;padding:.5rem .75rem;font-size:.82rem;">' + c.observaciones + '</div></div>' : '') +
            '</div>' +
            '<div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem;margin-top:.75rem;">' +
                '<div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#878a99;margin-bottom:.65rem;"><i class="ri-stack-line"></i> Deudas que cubre este comprobante</div>' +
                deudasHtml +
            '</div>' +
        '</div>' +
        '<div class="col-lg-5">' +
            '<div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem;">' +
                '<div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#878a99;margin-bottom:.65rem;"><i class="ri-file-search-line"></i> Archivo adjunto</div>' +
                archivoHtml +
            '</div>' +
        '</div>' +
    '</div>';

    document.getElementById('vc-body').innerHTML = html;

    // Evento para actualizar total
    document.querySelectorAll('.monto-pagar').forEach(function (inp) {
        inp.addEventListener('input', actualizarTotalMontos);
    });
    var montoCompInput = document.getElementById('vc-monto-comprobante');
    if (montoCompInput) {
        montoCompInput.addEventListener('input', distribuirMonto);
    }
    actualizarTotalMontos();

    var footer = document.getElementById('vc-footer');
    if (c.estado === 'pendiente') {
        footer.style.display = 'flex';
        footer.style.justifyContent = 'flex-end';
        footer.style.gap = '.5rem';
        footer.querySelector('#btn-rechazar-comp').style.display = '';
        footer.querySelector('#btn-verificar-comp').style.display = '';
    } else {
        footer.style.display = 'flex';
        footer.style.justifyContent = 'flex-end';
        footer.querySelector('#btn-rechazar-comp').style.display = 'none';
        footer.querySelector('#btn-verificar-comp').style.display = 'none';
    }
}

function actualizarTotalMontos() {
    let total = 0;
    document.querySelectorAll('.deuda-row').forEach(function (row) {
        var saldo = parseFloat(row.dataset.saldo) || 0;
        var inp = row.querySelector('.monto-pagar');
        var monto = inp ? (parseFloat(inp.value) || 0) : 0;
        total += monto;

        var restante = Math.max(0, saldo - monto);
        var restanteEl = row.querySelector('.restante-valor');
        var restanteContainer = row.querySelector('.dr-restante');
        if (restanteEl) restanteEl.textContent = 'Bs ' + restante.toFixed(2);
        if (restanteContainer) {
            restanteContainer.className = 'dr-restante';
            if (restante <= 0) restanteContainer.classList.add('cero');
            else if (monto > 0) restanteContainer.classList.add('positivo');
        }

        var pagaEl = row.querySelector('.paga-valor');
        if (pagaEl) pagaEl.textContent = 'Bs ' + monto.toFixed(2);
    });

    const el = document.getElementById('vc-total-montos');
    if (el) el.textContent = 'Bs ' + total.toFixed(2);

    // Actualizar suma deudas y comparar con monto comprobante
    var sumaEl = document.getElementById('vc-suma-deudas');
    var difEl = document.getElementById('vc-diferencia-msg');
    var montoCompEl = document.getElementById('vc-monto-comprobante');
    if (sumaEl && montoCompEl) {
        sumaEl.textContent = 'Bs ' + total.toFixed(2);
        if (difEl) {
            var montoComp = parseFloat(montoCompEl.value) || 0;
            var diff = montoComp - total;
            if (Math.abs(diff) < 0.01) {
                difEl.textContent = '✓ Montos coinciden';
                difEl.style.color = '#0ab39c';
            } else if (diff > 0) {
                difEl.textContent = 'Faltan Bs ' + diff.toFixed(2);
                difEl.style.color = '#f7b84b';
            } else {
                difEl.textContent = 'Sobran Bs ' + Math.abs(diff).toFixed(2);
                difEl.style.color = '#f06548';
            }
        }
    }
}

function distribuirMonto() {
    var montoTotal = parseFloat(document.getElementById('vc-monto-comprobante')?.value) || 0;
    var rows = document.querySelectorAll('.deuda-row');
    var pendiente = montoTotal;

    // First pass: selected deudas get paid first up to their saldo
    rows.forEach(function (row) {
        if (!row.classList.contains('seleccionada')) return;
        var saldo = parseFloat(row.dataset.saldo) || 0;
        if (saldo <= 0 || pendiente <= 0) {
            row.querySelector('.monto-pagar').value = '0';
            return;
        }
        var aplicar = Math.min(pendiente, saldo);
        row.querySelector('.monto-pagar').value = aplicar.toFixed(2);
        pendiente -= aplicar;
    });

    // Second pass: unselected deudas get remaining in order
    rows.forEach(function (row) {
        if (row.classList.contains('seleccionada')) return;
        var saldo = parseFloat(row.dataset.saldo) || 0;
        if (saldo <= 0 || pendiente <= 0) {
            row.querySelector('.monto-pagar').value = '0';
            return;
        }
        var aplicar = Math.min(pendiente, saldo);
        row.querySelector('.monto-pagar').value = aplicar.toFixed(2);
        pendiente -= aplicar;
    });

    actualizarTotalMontos();
}

document.getElementById('btn-verificar-comp')?.addEventListener('click', function () {
    if (!currentComprobanteId) return;
    if (!confirm('¿Confirmar que el comprobante es válido y registrar los pagos con los montos indicados?')) return;
    procesarComprobante(currentComprobanteId, 'verificar');
});

document.getElementById('btn-rechazar-comp')?.addEventListener('click', function () {
    if (!currentComprobanteId) return;
    const obs = prompt('Indique el motivo del rechazo (opcional):');
    procesarComprobante(currentComprobanteId, 'rechazar', obs);
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

function procesarComprobante(id, accion, observaciones) {
    const btn = document.getElementById('btn-' + (accion === 'verificar' ? 'verificar' : 'rechazar') + '-comp');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    fd.append('accion', accion);
    if (observaciones) fd.append('observaciones', observaciones);

    // Collect montos por deuda
    if (accion === 'verificar') {
        document.querySelectorAll('.deuda-row').forEach(function (row) {
            const mpId = row.dataset.mpId;
            const input = row.querySelector('.monto-pagar');
            if (mpId && input) {
                const val = parseFloat(input.value) || 0;
                if (val > 0) {
                    fd.append('montos[' + mpId + ']', val);
                }
            }
        });
    }

    fetch('{{ url("admin/comprobantes-pago") }}/' + id + '/verificar', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: fd
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalVerComprobante')).hide();
            showToast(data.message, 'success');
            setTimeout(function () { location.reload(); }, 1000);
        } else {
            alert(data.message || 'Error al procesar');
        }
    })
    .catch(function () { alert('Error de conexión'); })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = accion === 'verificar' ? '<i class="ri-check-double-line"></i> Aprobar y Registrar Pago' : '<i class="ri-close-circle-line"></i> Rechazar';
    });
}
</script>
@endpush
