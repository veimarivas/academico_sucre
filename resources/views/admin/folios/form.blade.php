@extends('admin.layouts.master')

@section('title', (isset($folio) ? 'Editar' : 'Nuevo') . ' Folio - ' . config('app.name'))

@push('css')
<style>
.is-invalid { border-color: #f06548 !important; }
.invalid-feedback { display: block; }
.part-table-wrap {
    max-height: 300px; overflow-y: auto;
    border: 1px solid var(--ui-line); border-radius: var(--ui-radius-sm);
}
.part-table-wrap thead { position: sticky; top: 0; z-index: 2; }
</style>
@endpush

@section('content')
<div class="folios-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-government-line"></i></div>
        <div>
            <h4 class="mb-1">{{ isset($folio) ? 'Editar Folio' : 'Nuevo Folio' }}</h4>
            <p class="hero-sub mb-0">{{ isset($folio) ? 'Modifique los datos del folio' : 'Registro de pagos para la universidad' }}</p>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.folios.index') }}">Folios</a></li>
        <li class="breadcrumb-item active">{{ isset($folio) ? 'Editar' : 'Nuevo' }}</li>
    </ol>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <form id="form-folio" data-mode="{{ isset($folio) ? 'edit' : 'create' }}">
                    @csrf
                    @if(isset($folio))
                        @method('PUT')
                        <input type="hidden" id="folio-id" value="{{ $folio->id }}">
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label class="form-label text-muted fw-semibold fs-12">Nombre del Folio</label>
                            <input type="text" class="form-control form-control-sm" id="folio-nombre"
                                   value="{{ $folio->nombre ?? '' }}" placeholder="Ej: Lote 1 - Julio 2026" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold fs-12">Fecha de Registro</label>
                            <input type="date" class="form-control form-control-sm" id="folio-fecha"
                                   value="{{ isset($folio) ? $folio->fecha_registro : date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold fs-12">Tipos de Pago a Incluir</label>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @php
                                    $conceptosSel = isset($folio) ? (json_decode($folio->concepto, true) ?? ['Inscripcion','Matricula','Colegiatura','Certificacion']) : ['Inscripcion','Matricula','Colegiatura','Certificacion'];
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-concepto-cb" value="Inscripcion" {{ in_array('Inscripcion', $conceptosSel) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-12">Inscripción</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-concepto-cb" value="Matricula" {{ in_array('Matricula', $conceptosSel) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-12">Matrícula</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-concepto-cb" value="Colegiatura" {{ in_array('Colegiatura', $conceptosSel) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-12">Colegiatura</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-concepto-cb" value="Certificacion" {{ in_array('Certificacion', $conceptosSel) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-12">Certificación</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($folio))
                    <div class="row g-2 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold fs-12">Estado</label>
                            <select class="form-select form-select-sm" id="folio-status">
                                <option value="abierto" {{ $folio->status === 'abierto' ? 'selected' : '' }}>Abierto</option>
                                <option value="cerrado" {{ $folio->status === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <hr class="my-3">

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12">Seleccione los participantes a incluir en el folio</span>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select form-select-sm" id="folio-filtro-programa" style="width:200px;">
                                <option value="">Todos los programas</option>
                                @foreach($programas as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->nombre }} ({{ $prog->tipo_nombre }})</option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control form-control-sm" id="folio-filtro" placeholder="Buscar..." style="width:160px;">
                        </div>
                    </div>

                    <div class="part-table-wrap">
                        <table class="table table-sm table-bordered align-middle mb-0" style="font-size:.85rem;">
                            <thead class="table-light text-muted text-uppercase fs-11">
                                <tr>
                                    <th style="width:36px;" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="folio-select-all">
                                    </th>
                                    <th>Participante</th>
                                    <th style="width:80px;" class="text-center">CI</th>
                                    <th style="width:160px;">Programa</th>
                                    <th style="width:180px;" class="text-center">Conflictos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programas as $prog)
                                    @php $progParts = $participantes->get($prog->id, collect()); @endphp
                                    @foreach($progParts as $part)
                                    @php
                                        $isSelected = isset($participantesEnFolio) && in_array($part->id, $participantesEnFolio);
                                        $conflictos = isset($conceptosRegistrados) ? $conceptosRegistrados->get($part->id, collect()) : collect();
                                    @endphp
                                    <tr class="folio-part-row"
                                        data-search="{{ strtolower($part->apellidos . ' ' . $part->nombres . ' ' . $part->ci) }}"
                                        data-programa="{{ $prog->id }}"
                                        data-participante="{{ $part->id }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input folio-part-cb"
                                                   value="{{ $part->id }}" {{ $isSelected ? 'checked' : '' }}>
                                        </td>
                                        <td><span class="fs-13">{{ $part->apellidos }}, {{ $part->nombres }}</span></td>
                                        <td class="text-center text-muted fs-12">{{ $part->ci }}</td>
                                        <td><span class="badge bg-soft-primary text-primary fs-10">{{ $part->programa_nombre }}</span></td>
                                        <td class="text-center folio-conflictos-cell">
                                            @if($conflictos->count() > 0)
                                                @foreach($conflictos as $conf)
                                                    <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1 me-1" title="Folio: {{ $conf['folio_nombre'] }}">
                                                        <i class="ri-information-line"></i>
                                                        {{ implode(', ', $conf['conceptos']) }}
                                                        <small>({{ $conf['folio_nombre'] }})</small>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted fs-11">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="folio-conflict-alert" class="mt-2" style="display:none;">
                        <div class="alert alert-soft-warning border-0 py-2 px-3 mb-0 d-flex align-items-center gap-2">
                            <i class="ri-alert-line"></i>
                            <small id="folio-conflict-text"></small>
                        </div>
                    </div>

                    <div class="row g-2 mt-3">
                        <div class="col-12">
                            <div class="bg-light rounded-3 p-3 border">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <small class="text-muted fw-medium">Costos por participante</small>
                                    <span class="badge bg-soft-primary text-primary fs-10" id="folio-selected-count">0 seleccionados</span>
                                </div>
                                <div class="row g-2 mt-2 text-center">
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Inscripción</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-total" id="folio-total-insc">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Matrícula</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-total" id="folio-total-mat">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Colegiatura</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-total" id="folio-total-col">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Certificación</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-total" id="folio-total-cert">Bs 0.00</span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-end">
                                    <span class="fw-bold fs-5 text-primary">Total: Bs <span id="folio-total-general">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.folios.index') }}" class="btn btn-sm btn-soft-secondary px-3">
                            <i class="ri-close-line me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-sm btn-success px-3" id="btn-guardar-folio">
                            <i class="ri-save-line me-1"></i>{{ isset($folio) ? 'Actualizar Folio' : 'Guardar Folio' }}
                        </button>
                    </div>
                </form>
            </div>
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

    function getConceptosSeleccionados() {
        const active = [];
        document.querySelectorAll('.folio-concepto-cb:checked').forEach(function (cb) { active.push(cb.value); });
        return active;
    }

    function actualizarTotales() {
        const cbs = document.querySelectorAll('.folio-part-cb:checked');
        const count = cbs.length;
        const conceptos = getConceptosSeleccionados();
        document.getElementById('folio-selected-count').textContent = count + ' seleccionado' + (count !== 1 ? 's' : '');
        document.getElementById('folio-total-insc').textContent = 'Bs 0.00';
        document.getElementById('folio-total-mat').textContent = 'Bs 0.00';
        document.getElementById('folio-total-col').textContent = 'Bs 0.00';
        document.getElementById('folio-total-cert').textContent = 'Bs 0.00';
        document.getElementById('folio-total-general').textContent = '0.00';

        const conflictAlert = document.getElementById('folio-conflict-alert');
        const conflictText = document.getElementById('folio-conflict-text');
        let conflictos = [];
        cbs.forEach(function (cb) {
            const row = cb.closest('.folio-part-row');
            const cell = row ? row.querySelector('.folio-conflictos-cell') : null;
            if (cell) {
                const badges = cell.querySelectorAll('.badge');
                badges.forEach(function (b) { conflictos.push(b.textContent.trim()); });
            }
        });
        if (conflictos.length > 0) {
            conflictAlert.style.display = 'block';
            conflictText.textContent = conflictos.length + ' participante(s) ya tienen conceptos en otros folios. Revise la columna Conflictos.';
        } else {
            conflictAlert.style.display = 'none';
        }
    }

    document.querySelectorAll('.folio-concepto-cb').forEach(function (cb) {
        cb.addEventListener('change', actualizarTotales);
    });

    document.getElementById('folio-select-all').addEventListener('change', function () {
        document.querySelectorAll('.folio-part-cb').forEach(function (cb) {
            const row = cb.closest('.folio-part-row');
            if (row.style.display !== 'none') cb.checked = this.checked;
        }, this);
        actualizarTotales();
    });

    document.querySelectorAll('.folio-part-cb').forEach(function (cb) {
        cb.addEventListener('change', actualizarTotales);
    });

    document.getElementById('folio-filtro').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.folio-part-row').forEach(function (r) {
            r.style.display = q === '' || r.dataset.search.includes(q) ? '' : 'none';
        });
    });

    document.getElementById('folio-filtro-programa').addEventListener('change', function () {
        const progId = this.value;
        document.querySelectorAll('.folio-part-row').forEach(function (r) {
            r.style.display = !progId || r.dataset.programa === progId ? '' : 'none';
        });
    });

    document.getElementById('form-folio').addEventListener('submit', function (e) {
        e.preventDefault();
        const nombre = document.getElementById('folio-nombre').value.trim();
        const fecha = document.getElementById('folio-fecha').value;
        const conceptos = getConceptosSeleccionados();
        const seleccionados = Array.from(document.querySelectorAll('.folio-part-cb:checked')).map(function (cb) {
            return parseInt(cb.value);
        });
        const mode = this.dataset.mode;
        const folioId = document.getElementById('folio-id') ? document.getElementById('folio-id').value : null;

        if (!nombre) { showToast('Ingrese un nombre para el folio', 'warning'); return; }
        if (!fecha) { showToast('Seleccione una fecha', 'warning'); return; }
        if (conceptos.length === 0) { showToast('Seleccione al menos un tipo de pago', 'warning'); return; }
        if (seleccionados.length === 0) { showToast('Seleccione al menos un participante', 'warning'); return; }

        const btn = document.getElementById('btn-guardar-folio');
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('nombre', nombre);
        fd.append('fecha_registro', fecha);
        if (mode === 'edit' && document.getElementById('folio-status')) {
            fd.append('status', document.getElementById('folio-status').value);
        }
        conceptos.forEach(function (c, i) { fd.append('conceptos[' + i + ']', c); });
        seleccionados.forEach(function (pid, i) { fd.append('participantes[' + i + ']', pid); });

        const url = mode === 'edit'
            ? '{{ url("admin/folios") }}/' + folioId
            : '{{ route("admin.folios.store") }}';

        const method = mode === 'edit' ? 'PUT' : 'POST';
        if (mode === 'edit') fd.append('_method', 'PUT');

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message || 'Folio ' + (mode === 'edit' ? 'actualizado' : 'creado') + ' correctamente', 'success');
                setTimeout(function () { window.location.href = '{{ route("admin.folios.index") }}'; }, 1200);
            } else {
                showToast(data.message || 'Error al guardar folio', 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
    });

    actualizarTotales();
});
</script>
@endpush
