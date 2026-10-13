@extends('admin.layouts.master')

@section('title', 'Folios - ' . config('app.name'))

@push('css')
<style>
.is-invalid { border-color: #f06548 !important; }
.invalid-feedback { display: block; }

.folios-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.folios-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.folios-hero > * { position: relative; z-index: 1; }
.folios-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.folios-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.folios-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }

.stat-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
    position: relative;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before {
    content: ""; position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: var(--stat-accent, #405189);
}
.stat-card .stat-label {
    font-size: .68rem; text-transform: uppercase; letter-spacing: .5px;
    color: #878a99; font-weight: 700; margin-bottom: .4rem;
}
.stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #212529; line-height: 1.2; }
.stat-card .stat-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px;
    font-size: 1.25rem;
}

.filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
.filter-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
.filter-card .form-label {
    font-size: .72rem; text-transform: uppercase; letter-spacing: .5px;
    font-weight: 700; color: #878a99; margin-bottom: .35rem;
}
.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid #e9ebec; background-color: #fff; font-weight: 500;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color: #405189; box-shadow: 0 0 0 .15rem rgba(64,81,137,.12); }

.btn-nuevo {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0; color: #fff; font-weight: 600; letter-spacing: .3px;
    box-shadow: 0 6px 18px -8px rgba(10,179,156,.6);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-nuevo:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 10px 22px -10px rgba(10,179,156,.75); }

.results-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}

.table-card-wrap {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
.tabla-folios th, .tabla-folios td {
    vertical-align: middle;
    padding: .8rem .75rem;
}
.tabla-folios thead th {
    background: #f8f9fc !important;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #495057;
    border-bottom: 2px solid #eef0f4;
}
.tabla-folios tbody tr { border-bottom: 1px solid #f1f3f5; transition: background-color .15s ease; }
.tabla-folios tbody tr:hover { background-color: #f8f9fc; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Hero --}}
        <div class="folios-hero d-flex align-items-center gap-3">
            <div class="hero-icon"><i class="ri-government-line"></i></div>
            <div class="flex-grow-1">
                <h4>Folios</h4>
                <p class="hero-sub">Registros de pago para la universidad</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-2 mb-3" id="resumen-stats">
            @include('admin.folios.partials.stats')
        </div>

        {{-- Filters --}}
        <div class="card filter-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold fs-13"><i class="ri-filter-3-line me-1"></i>Filtros</span>
                <button type="button" class="btn btn-sm btn-nuevo px-3" id="btn-nuevo-folio">
                    <i class="ri-add-line me-1"></i>Nuevo Folio
                </button>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="filter-search" placeholder="Nombre del folio..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select form-select-sm" id="filter-status">
                            <option value="">Todos</option>
                            <option value="abierto" @selected(request('status')=='abierto')>Abierto</option>
                            <option value="cerrado" @selected(request('status')=='cerrado')>Cerrado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-soft-primary w-100" id="btn-filtrar">
                            <i class="ri-search-line me-1"></i>Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-soft-secondary w-100" id="btn-limpiar">
                            <i class="ri-refresh-line me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card results-card">
            <div class="card-body p-3">
                <div id="table-container">
                    @include('admin.folios.partials.table')
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Nuevo Folio --}}
<div class="modal fade" id="modalNuevoFolio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd 0%,#0a58ca 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.25);border-radius:10px;">
                        <i class="ri-government-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Nuevo Folio</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.72rem;">Registro de pago para la universidad</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="form-nuevo-folio">
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Nombre del Folio</label>
                        <input type="text" class="form-control form-control-sm" id="folio-nombre" placeholder="Ej: Lote 1 - Julio 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Fecha de Registro</label>
                        <input type="date" class="form-control form-control-sm" id="folio-fecha" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted fw-medium fs-12">Tipos de Pago a Incluir</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input folio-concepto-cb" value="Inscripcion" checked>
                                <label class="form-check-label fs-12">Inscripción</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input folio-concepto-cb" value="Matricula" checked>
                                <label class="form-check-label fs-12">Matrícula</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input folio-concepto-cb" value="Colegiatura" checked>
                                <label class="form-check-label fs-12">Colegiatura</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input folio-concepto-cb" value="Certificacion" checked>
                                <label class="form-check-label fs-12">Certificación</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success px-3" id="btn-guardar-folio">
                        <i class="ri-save-line me-1"></i>Guardar Folio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function showToast(msg, type) {
        type = type || 'success';
        const colors = { success:'#0abf53', danger:'#f06548', warning:'#f7b84b', info:'#4facfe' };
        const el = document.createElement('div');
        el.className = 'toast align-items-center text-white border-0';
        el.setAttribute('role','alert'); el.setAttribute('aria-live','assertive'); el.setAttribute('aria-atomic','true');
        el.style.background = colors[type] || colors.success;
        el.innerHTML = '<div class="d-flex"><div class="toast-body fs-12 fw-medium">' + msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        const container = document.getElementById('toast-container');
        if (!container) {
            const c = document.createElement('div'); c.id = 'toast-container';
            c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(c);
        }
        const t = new bootstrap.Toast(el, { delay: 3500, autohide: true });
        document.getElementById('toast-container').appendChild(el);
        t.show();
        el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    }

    function cargarFolios() {
        const params = new URLSearchParams();
        const search = document.getElementById('filter-search').value;
        const status = document.getElementById('filter-status').value;
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        params.set('ajax', '1');

        fetch('{{ route("admin.folios.index") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            document.getElementById('table-container').innerHTML = data.html;
            document.getElementById('resumen-stats').innerHTML = data.resumen ? `
                <div class="col-md-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#405189;">
                        <div class="stat-icon" style="background:rgba(64,81,137,.12);color:#405189;"><i class="ri-file-list-line"></i></div>
                        <div><div class="stat-label">Total Folios</div><div class="stat-value">${data.resumen.total}</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#0ab39c;">
                        <div class="stat-icon" style="background:rgba(10,179,156,.12);color:#0ab39c;"><i class="ri-lock-unlock-line"></i></div>
                        <div><div class="stat-label">Abiertos</div><div class="stat-value">${data.resumen.abiertos}</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#f06548;">
                        <div class="stat-icon" style="background:rgba(240,101,72,.12);color:#f06548;"><i class="ri-lock-line"></i></div>
                        <div><div class="stat-label">Cerrados</div><div class="stat-value">${data.resumen.cerrados}</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#299cdb;">
                        <div class="stat-icon" style="background:rgba(41,156,219,.12);color:#299cdb;"><i class="ri-team-line"></i></div>
                        <div><div class="stat-label">Total Participantes</div><div class="stat-value">${data.resumen.total_participantes}</div></div>
                    </div>
                </div>
            ` : '';
        })
        .catch(function () { showToast('Error al cargar folios', 'danger'); });
    }

    document.getElementById('btn-filtrar').addEventListener('click', cargarFolios);
    document.getElementById('btn-limpiar').addEventListener('click', function () {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-status').value = '';
        cargarFolios();
    });

    // Delete
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-folio');
        if (!btn) return;
        e.preventDefault();
        var id = btn.dataset.id;
        var name = btn.dataset.name;
        if (!confirm('¿Eliminar el folio "' + name + '"?\nSe eliminarán también todos los participantes asociados.')) return;
        fetch('{{ url("admin/folios") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) { showToast(data.message, 'success'); cargarFolios(); }
            else { showToast(data.message, 'danger'); }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); });
    });

    // Nuevo Folio modal
    document.getElementById('btn-nuevo-folio').addEventListener('click', function () {
        document.getElementById('folio-nombre').value = '';
        document.getElementById('folio-fecha').value = new Date().toISOString().split('T')[0];
        document.querySelectorAll('#form-nuevo-folio .folio-concepto-cb').forEach(function (cb) { cb.checked = true; });
        new bootstrap.Modal(document.getElementById('modalNuevoFolio')).show();
    });

    document.getElementById('form-nuevo-folio').addEventListener('submit', function (e) {
        e.preventDefault();
        var nombre = document.getElementById('folio-nombre').value.trim();
        var fecha = document.getElementById('folio-fecha').value;
        var conceptos = [];
        document.querySelectorAll('#form-nuevo-folio .folio-concepto-cb:checked').forEach(function (cb) { conceptos.push(cb.value); });
        if (!nombre) { showToast('Ingrese un nombre para el folio', 'warning'); return; }
        if (!fecha) { showToast('Seleccione una fecha', 'warning'); return; }
        if (conceptos.length === 0) { showToast('Seleccione al menos un tipo de pago', 'warning'); return; }

        var btn = document.getElementById('btn-guardar-folio');
        btn.disabled = true;
        var orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('nombre', nombre);
        fd.append('fecha_registro', fecha);
        conceptos.forEach(function (c, i) { fd.append('conceptos[' + i + ']', c); });

        fetch('{{ route("admin.folios.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevoFolio')).hide();
                showToast(data.message, 'success');
                cargarFolios();
            } else {
                showToast(data.message || 'Error al crear folio', 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
    });
});
</script>
@endpush
