@extends('admin.layouts.master')

@section('title', 'Pagos a Docentes - ' . config('app.name'))

@push('css')
<style>
.pd-header-wrap {
    background:var(--ui-surface);
    border:1px solid var(--ui-line);
    border-radius:var(--ui-radius); padding:1.1rem 1.25rem; margin-bottom:1.25rem;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
    box-shadow:var(--ui-shadow);
}
.pd-header-title {
    display:flex; align-items:center; gap:.65rem; color:var(--ui-ink); font-size:1.05rem; font-weight:700;
}
.pd-header-title i { font-size:1.3rem; color:var(--ui-accent); }
.pd-header-filter { display:flex; align-items:center; gap:.5rem; }
.pd-header-filter select {
    background:#fff; border:1px solid var(--ui-line-strong); color:var(--ui-ink-2);
    border-radius:var(--ui-radius-sm); padding:.45rem .75rem; font-size:.82rem; font-weight:600;
    min-width:220px; cursor:pointer;
}
.pd-header-filter select:focus { outline:none; border-color:var(--ui-accent); box-shadow:var(--ui-ring); }
.pd-header-badge {
    display:inline-flex; align-items:center; gap:.35rem;
    background:var(--ui-accent-soft); color:var(--ui-accent-ink);
    padding:.3rem .85rem; border-radius:999px;
    font-size:.7rem; font-weight:700; letter-spacing:.3px;
}

.pd-stat { border-radius:var(--ui-radius) !important; overflow:hidden; border:1px solid var(--ui-line) !important; box-shadow:var(--ui-shadow) !important; }
.pd-stat-body { padding:1.1rem 1.25rem; display:flex; align-items:center; gap:1rem; }
.pd-stat-icon {
    width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    font-size:1.25rem; background:var(--ui-accent-soft) !important; color:var(--ui-accent) !important; flex-shrink:0;
}
.pd-stat-info { flex:1; min-width:0; }
.pd-stat-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.35px; color:var(--ui-muted); margin-bottom:.15rem; }
.pd-stat-value { font-size:1.35rem; font-weight:800; color:var(--ui-ink); line-height:1.2; }

.pd-card {
    border:1px solid var(--ui-line) !important; border-radius:var(--ui-radius) !important; overflow:hidden;
    box-shadow:var(--ui-shadow) !important;
}
.pd-card-header {
    background:var(--ui-surface-2) !important;
    border-bottom:1px solid var(--ui-line);
    padding:.9rem 1.25rem; display:flex; align-items:center; gap:.5rem;
}
.pd-card-header-icon {
    width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center;
    background:var(--ui-accent-soft) !important; color:var(--ui-accent) !important; font-size:.85rem; flex-shrink:0;
}
.pd-card-header-title { font-size:.82rem; font-weight:700; color:var(--ui-ink); margin:0; }
.pd-card-body { padding:0; }

.pd-table { margin:0; width:100%; font-size:.78rem; }
.pd-table thead th {
    font-size:.64rem; font-weight:700; white-space:nowrap;
}
.pd-table tbody td {
    padding:.6rem .75rem; border-bottom:1px solid var(--ui-line); vertical-align:middle;
}
.pd-table tbody tr:last-child td { border-bottom:0; }
.pd-table tbody tr { transition:background .15s; }
.pd-table tbody tr:hover { background:var(--ui-surface-2); }

.pd-docente { display:flex; align-items:center; gap:.45rem; }
.pd-docente-avatar {
    width:28px; height:28px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    background:var(--ui-accent) !important;
    color:#fff; font-weight:700; font-size:.6rem; flex-shrink:0;
}
.pd-docente-nombre { font-weight:600; color:var(--ui-ink); font-size:.78rem; line-height:1.2; }
.pd-docente-ci { font-size:.62rem; color:var(--ui-muted); }

.pd-monto { font-weight:700; color:var(--ui-ink); font-size:.8rem; white-space:nowrap; }
.pd-monto-negativo { color:var(--ui-danger); font-weight:600; font-size:.72rem; white-space:nowrap; }

.pd-totales {
    background:var(--ui-surface-2) !important;
    border-radius:var(--ui-radius); border:1px solid var(--ui-line); padding:1.25rem 1.5rem;
    box-shadow:var(--ui-shadow);
}
.pd-totales-item { text-align:center; }
.pd-totales-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.1rem; }
.pd-totales-value { font-size:1.15rem; font-weight:800; }
.pd-totales-highlight {
    background:var(--ui-success) !important;
    border-radius:var(--ui-radius-sm); padding:.85rem 1rem; color:#fff;
    box-shadow:none;
}
.pd-totales-highlight .lbl { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; opacity:.85; }
.pd-totales-highlight .num { font-size:1.35rem; font-weight:800; line-height:1.2; color:#fff; }

.pd-empty { text-align:center; padding:2.5rem 1rem; color:var(--ui-muted); }
.pd-empty i { font-size:3rem; display:block; margin-bottom:.75rem; opacity:.35; }
.pd-empty h5 { font-weight:600; color:var(--ui-ink-2); margin-bottom:.25rem; }

@media (max-width:768px) {
    .pd-header-wrap { flex-direction:column; align-items:stretch; text-align:center; }
    .pd-header-filter { justify-content:center; }
}
</style>
@endpush

@section('content')
<div class="acad-page">
<div class="pd-header-wrap">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="pd-header-title">
            <i class="ri-money-dollar-circle-line"></i>
            <span>Pagos a Docentes</span>
        </div>
        <span class="pd-header-badge">
            <i class="ri-calendar-check-line"></i>
            @if($periodoSeleccionado !== 'todos')
                Corte {{ \Carbon\Carbon::parse($periodoSeleccionado)->format('d/m/Y') }}
                ({{ (int)\Carbon\Carbon::parse($periodoSeleccionado)->format('d') === 10 ? 'Corte 10' : 'Corte 20' }})
            @else
                Todos los periodos
            @endif
        </span>
    </div>
    <form method="GET" action="{{ route('admin.programas.pagos-docentes') }}" class="pd-header-filter">
        <select name="periodo" id="periodo" onchange="this.form.submit()">
            <option value="todos" {{ $periodoSeleccionado === 'todos' ? 'selected' : '' }}>Todos los pendientes</option>
            @foreach($periodosDisponibles as $per)
                @php
                    $label = \Carbon\Carbon::parse($per)->locale('es')->isoFormat('MMMM YYYY');
                    $corte = (int)\Carbon\Carbon::parse($per)->format('d') === 10 ? 'Corte 10' : 'Corte 20';
                @endphp
                <option value="{{ $per }}" {{ $periodoSeleccionado === $per ? 'selected' : '' }}>
                    {{ ucfirst($label) }} — {{ $corte }}
                </option>
            @endforeach
        </select>
    </form>
</div>

{{-- Stats bar --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card pd-stat h-100">
            <div class="pd-stat-body">
                <div class="pd-stat-icon" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
                <div class="pd-stat-info">
                    <div class="pd-stat-label">Total a Pagar</div>
                    <div class="pd-stat-value">Bs {{ number_format($totalesGenerales->total_pago, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card pd-stat h-100">
            <div class="pd-stat-body">
                <div class="pd-stat-icon" style="background:linear-gradient(135deg,#299cdb 0%,#4facfe 100%);">
                    <i class="ri-team-line"></i>
                </div>
                <div class="pd-stat-info">
                    <div class="pd-stat-label">Módulos Pendientes</div>
                    <div class="pd-stat-value">{{ $modulos->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card pd-stat h-100">
            <div class="pd-stat-body">
                <div class="pd-stat-icon" style="background:linear-gradient(135deg,#f7b84b 0%,#fcd34d 100%);">
                    <i class="ri-graduation-cap-line"></i>
                </div>
                <div class="pd-stat-info">
                    <div class="pd-stat-label">Programas</div>
                    <div class="pd-stat-value">{{ $modulos->pluck('programa_id')->unique()->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card pd-stat h-100">
            <div class="pd-stat-body">
                <div class="pd-stat-icon" style="background:linear-gradient(135deg,#0ab39c 0%,#34d399 100%);">
                    <i class="ri-percent-line"></i>
                </div>
                <div class="pd-stat-info">
                    <div class="pd-stat-label">Retenciones (IVA+IT)</div>
                    <div class="pd-stat-value">Bs {{ number_format($totalesGenerales->total_iva + $totalesGenerales->total_it, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main table --}}
<div class="pd-card">
    <div class="pd-card-header">
        <div class="pd-card-header-icon"><i class="ri-list-check"></i></div>
        <h6 class="pd-card-header-title">Módulos pendientes de pago</h6>
        <span class="badge bg-soft-primary text-primary ms-auto fs-10 px-3 py-1.5">
            {{ $modulos->count() }} registro(s)
        </span>
    </div>
    <div class="pd-card-body">
        <table class="table pd-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:36px;">#</th>
                    <th>Módulo</th>
                    <th style="width:160px;">Programa</th>
                    <th style="width:195px;">Docente</th>
                    <th class="text-center" style="width:72px;">Factura</th>
                    <th class="text-end" style="width:82px;">Bruto</th>
                    <th class="text-end" style="width:62px;">IVA</th>
                    <th class="text-end" style="width:60px;">IT</th>
                    <th class="text-end" style="width:82px;">Neto</th>
                    <th class="text-center" style="width:58px;">Notas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modulos as $m)
                @php
                    $iniciales = $m->docente_nombres ? strtoupper(substr($m->docente_nombres,0,1).substr($m->docente_apellidos,0,1)) : '?';
                    $neto = $m->factura ? (float)$m->pago_docente : max(0,(float)$m->pago_docente-(float)$m->iva-(float)$m->it);
                @endphp
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                    <td><span class="fw-semibold">{{ $m->modulo_nombre }}</span></td>
                    <td>
                        <div style="font-size:.74rem;line-height:1.3;">
                            <span class="fw-semibold">{{ $m->programa_codigo }}</span>
                            <br><span class="text-muted">{{ $m->programa_nombre }}</span>
                        </div>
                    </td>
                    <td>
                        @if($m->docente_id)
                        <div class="pd-docente">
                            <span class="pd-docente-avatar">{{ $iniciales }}</span>
                            <div>
                                <div class="pd-docente-nombre">{{ $m->docente_apellidos }}, {{ $m->docente_nombres }}</div>
                                <div class="pd-docente-ci">CI: {{ $m->docente_ci }}</div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted fst-italic">Sin asignar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($m->factura)
                        <span class="badge bg-soft-success text-success px-2 py-1" style="font-size:.6rem;font-weight:600;">Sí</span>
                        @else
                        <span class="badge bg-soft-warning text-warning px-2 py-1" style="font-size:.6rem;font-weight:600;">No</span>
                        @endif
                    </td>
                    <td class="text-end pd-monto">Bs {{ number_format($m->pago_docente,2) }}</td>
                    <td class="text-end {{ (float)$m->iva>0 ? 'pd-monto-negativo' : 'text-muted' }}">
                        {{ (float)$m->iva>0 ? 'Bs '.number_format($m->iva,2) : '—' }}
                    </td>
                    <td class="text-end {{ (float)$m->it>0 ? 'pd-monto-negativo' : 'text-muted' }}">
                        {{ (float)$m->it>0 ? 'Bs '.number_format($m->it,2) : '—' }}
                    </td>
                    <td class="text-end pd-monto" style="color:#0ab39c;">Bs {{ number_format($neto,2) }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-soft-info border-0 btn-ver-notas px-2"
                                data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->modulo_nombre }}"
                                title="Ver notas y nivelación">
                            <i class="ri-file-list-3-line"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="pd-empty">
                            <i class="ri-inbox-line"></i>
                            <h5>Sin módulos pendientes</h5>
                            <p>
                                @if($periodoSeleccionado !== 'todos')
                                    No hay módulos para el corte <strong>{{ \Carbon\Carbon::parse($periodoSeleccionado)->format('d/m/Y') }}</strong>.
                                    <br><a href="{{ route('admin.programas.pagos-docentes',['periodo'=>'todos']) }}" class="text-primary fw-semibold">Ver todos los pendientes</a>
                                @else
                                    Todos los módulos concluidos tienen su pago procesado.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Totales generales --}}
@if($modulos->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="pd-totales">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 pd-totales-item">
                    <div class="pd-totales-label" style="color:#878a99;">Bruto</div>
                    <div class="pd-totales-value">Bs {{ number_format($totalesGenerales->total_pago,2) }}</div>
                </div>
                <div class="col-md-2 pd-totales-item">
                    <div class="pd-totales-label" style="color:#f06548;">IVA</div>
                    <div class="pd-totales-value" style="color:#f06548;">Bs {{ number_format($totalesGenerales->total_iva,2) }}</div>
                </div>
                <div class="col-md-2 pd-totales-item">
                    <div class="pd-totales-label" style="color:#d97706;">IT</div>
                    <div class="pd-totales-value" style="color:#d97706;">Bs {{ number_format($totalesGenerales->total_it,2) }}</div>
                </div>
                <div class="col-md-2 pd-totales-item">
                    <div class="pd-totales-label" style="color:#878a99;">Gastos</div>
                    <div class="pd-totales-value">Bs {{ number_format($totalesGenerales->total_gastos,2) }}</div>
                </div>
                <div class="col-md-3 pd-totales-item">
                    <div class="pd-totales-highlight">
                        <div class="lbl">Neto a Pagar</div>
                        <div class="num">Bs {{ number_format(max(0,$totalesGenerales->total_pago-$totalesGenerales->total_iva-$totalesGenerales->total_it)+$totalesGenerales->total_gastos,2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Ver Notas --}}
<div class="modal fade" id="modalVerNotas" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div><i class="ri-file-list-3-line" style="font-size:1.2rem;"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold" id="modalNotasTitle">Notas del Módulo</h5>
                        <small id="modalNotasSubtitle" style="opacity:.85;font-size:.78rem;"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.25rem;background:#fafbfd;">
                <div id="modalNotasLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando notas...</p>
                </div>
                <div id="modalNotasContent" style="display:none;">
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <span class="badge bg-soft-primary text-primary fs-10 px-3 py-2" id="notasBadgeTotal"></span>
                        <span class="badge bg-soft-success text-success fs-10 px-3 py-2" id="notasBadgeModulo"></span>
                        <span class="badge bg-soft-warning text-warning fs-10 px-3 py-2" id="notasBadgeNivelacion"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size:.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:36px;">#</th>
                                    <th>CI</th>
                                    <th>Apellidos y Nombres</th>
                                    <th class="text-center" style="width:105px;">Nota Módulo</th>
                                    <th class="text-center" style="width:105px;" id="thNivelacion">Nota Nivelación</th>
                                </tr>
                            </thead>
                            <tbody id="modalNotasTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.85rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modalVerNotas');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    var tbody = document.getElementById('modalNotasTableBody');
    var loading = document.getElementById('modalNotasLoading');
    var content = document.getElementById('modalNotasContent');
    var title = document.getElementById('modalNotasTitle');
    var subtitle = document.getElementById('modalNotasSubtitle');

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-ver-notas');
        if (!btn) return;
        var moduloId = btn.dataset.moduloId;
        var moduloNombre = btn.dataset.moduloNombre;
        title.textContent = 'Notas del Módulo';
        subtitle.textContent = moduloNombre;
        loading.style.display = 'block';
        content.style.display = 'none';

        fetch('{{ route("admin.programas.modulo-notas-data", ["modulo" => "__ID__"]) }}'.replace('__ID__', moduloId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            loading.style.display = 'none';
            if (!data.success) {
                content.innerHTML = '<div class="alert alert-danger">Error al cargar notas.</div>';
                content.style.display = 'block';
                return;
            }
            document.getElementById('notasBadgeTotal').textContent = 'Total: ' + data.total + ' participantes';
            document.getElementById('notasBadgeModulo').textContent = 'Con nota m\u00f3dulo: ' + data.con_nota_modulo;
            var elNiv = document.getElementById('notasBadgeNivelacion');
            if (data.con_nota_nivelacion === 0) {
                elNiv.style.display = 'none';
            } else {
                elNiv.style.display = '';
                elNiv.textContent = 'Con nivelaci\u00f3n: ' + data.con_nota_nivelacion;
            }

            var sinNivelacion = data.con_nota_nivelacion === 0;
            document.getElementById('thNivelacion').style.display = sinNivelacion ? 'none' : '';
            tbody.innerHTML = '';
            data.participantes.forEach(function (p, i) {
                var notaMod = p.nota_modulo !== null && parseFloat(p.nota_modulo) > 0 ? parseFloat(p.nota_modulo).toFixed(2) : '\u2014';
                var notaNiv = p.nota_nivelacion !== null && parseFloat(p.nota_nivelacion) > 0 ? parseFloat(p.nota_nivelacion).toFixed(2) : '\u2014';
                var clsMod = p.nota_modulo !== null && parseFloat(p.nota_modulo) >= 71 ? 'text-success fw-bold' : (p.nota_modulo !== null && parseFloat(p.nota_modulo) > 0 ? 'text-danger fw-bold' : 'text-muted');
                var clsNiv = p.nota_nivelacion !== null && parseFloat(p.nota_nivelacion) >= 71 ? 'text-success fw-bold' : (p.nota_nivelacion !== null && parseFloat(p.nota_nivelacion) > 0 ? 'text-danger fw-bold' : 'text-muted');
                var cols = '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                    '<td>' + (p.ci || '') + '</td>' +
                    '<td>' + (p.apellidos || '') + ', ' + (p.nombres || '') + '</td>' +
                    '<td class="text-center ' + clsMod + '">' + notaMod + '</td>';
                if (!sinNivelacion) {
                    cols += '<td class="text-center ' + clsNiv + '">' + notaNiv + '</td>';
                }
                var tr = document.createElement('tr');
                tr.innerHTML = cols;
                tbody.appendChild(tr);
            });
            content.style.display = 'block';
            modal.show();
        })
        .catch(function () {
            loading.style.display = 'none';
            content.innerHTML = '<div class="alert alert-danger">Error de conexi\u00f3n.</div>';
            content.style.display = 'block';
        });
    });
});
</script>
@endpush
