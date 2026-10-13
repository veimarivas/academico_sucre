@extends('admin.layouts.master')

@section('title', 'Áreas - ' . config('app.name'))

@push('css')
<style>
.is-invalid { border-color: #f06548 !important; }
.invalid-feedback { display: block; }

/* ===== Hero ===== */
.areas-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.areas-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.areas-hero::after {
    content: ""; position: absolute;
    bottom: -60px; left: 30%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%);
    border-radius: 50%;
}
.areas-hero > * { position: relative; z-index: 1; }
.areas-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.areas-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.areas-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.areas-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.areas-hero .breadcrumb-item, .areas-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.areas-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.areas-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

/* ===== Stat cards ===== */
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
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .stat-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #878a99;
    font-weight: 700;
    margin-bottom: .4rem;
}
.stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }
.stat-card .stat-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px;
    font-size: 1.25rem;
}

/* ===== Filter Card ===== */
.filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: #2a2f34; }
.filter-card .form-label {
    font-size: .72rem; text-transform: uppercase; letter-spacing: .5px;
    font-weight: 700; color: #878a99; margin-bottom: .35rem;
}
.filter-card .input-group-text {
    background: #fff; border: 1px solid #e9ebec; border-right: 0; color: #878a99;
}
.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid #e9ebec; background-color: #fff; font-weight: 500; transition: all .2s ease;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color: #405189; box-shadow: 0 0 0 .15rem rgba(64,81,137,.12); }
.filter-card .input-group:focus-within .input-group-text { border-color: #405189; color: #405189; }
[data-layout-mode="dark"] .filter-card .input-group-text,
[data-layout-mode="dark"] .filter-card .form-control,
[data-layout-mode="dark"] .filter-card .form-select { background-color: #1d2125; border-color: #2a2f34; color: #ced4da; }

.btn-nuevo {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0; color: #fff; font-weight: 600; letter-spacing: .3px;
    box-shadow: 0 6px 18px -8px rgba(10,179,156,.6);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-nuevo:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 10px 22px -10px rgba(10,179,156,.75); }

/* ===== Results Card ===== */
.results-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .results-card { border-color: #2a2f34; }
.results-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: .75rem; margin-bottom: 1rem;
    padding-bottom: .75rem; border-bottom: 1px dashed #eef0f4;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .results-header { border-bottom-color: #2a2f34; }
.results-count {
    display: inline-flex; align-items: center; gap: .5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    padding: .4em .9em; border-radius: 999px;
    font-size: .82rem; font-weight: 600; color: #495057;
    border: 1px solid #eef0f4;
}
.results-count .count-num {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-weight: 700;
    padding: .15em .6em; border-radius: 999px;
    font-size: .78rem;
}

/* ===== Tabla ===== */
.table-card-wrap {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
[data-layout-mode="dark"] .table-card-wrap { border-color: #2a2f34; }
.tabla-areas { table-layout: fixed; width: 100%; }
.tabla-areas th, .tabla-areas td {
    vertical-align: middle;
    padding: .8rem .75rem;
    overflow: hidden;
}
.tabla-areas thead th {
    background: linear-gradient(180deg, #f8f9fc 0%, #eef1f8 100%) !important;
    font-size: .69rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #5d6478;
    border-bottom: 1px solid #e6eaf0;
}
[data-layout-mode="dark"] .tabla-areas thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }
.tabla-areas tbody tr { border-bottom: 1px solid #f1f3f5; transition: background-color .15s ease, box-shadow .15s ease; }
.tabla-areas tbody tr:hover { background-color: #f8f9fc; box-shadow: inset 3px 0 0 #405189; }
[data-layout-mode="dark"] .tabla-areas tbody tr { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .tabla-areas tbody tr:hover { background-color: #1a1d20; }

.col-color-preview {
    width: 40px; height: 26px;
    border-radius: 8px;
    display: inline-block;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px rgba(0,0,0,.1);
    flex-shrink: 0;
}
[data-layout-mode="dark"] .col-color-preview { border-color: #212529; }
.area-nombre { font-weight: 700; color: #212529; font-size: .9rem; line-height: 1.2; }
.area-id { font-size: .68rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .area-nombre { color: #fff; }

.metric-chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .72rem; font-weight: 700;
    padding: .3em .75em; border-radius: 999px;
    border: 1px solid;
}
.metric-progs { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.metric-empty { background: #f1f3f5; color: #878a99; border-color: #e9ebec; }

.descripcion-cell {
    font-size: .8rem;
    color: #495057;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
[data-layout-mode="dark"] .descripcion-cell { color: #ced4da; }

/* Empty / loading */
.tabla-loading, .tabla-empty {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 12px;
}
.tabla-empty .empty-icon {
    width: 80px; height: 80px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

/* ===== Modales ===== */
#modalArea .modal-content,
#modalDeleteArea .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalArea .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalArea .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalArea .modal-header > * { position: relative; z-index: 1; }
#modalArea .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalArea .modal-title { color: #fff; font-weight: 700; }
#modalArea .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalArea .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalArea .modal-body { padding: 1.5rem; background: #fafbfd; }
[data-layout-mode="dark"] #modalArea .modal-body { background: #1a1d20; }
#modalArea .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalArea .modal-footer { background: #212529; border-top-color: #2a2f34; }

#modalArea .form-label {
    font-weight: 600; font-size: .72rem;
    color: #6c757d;
    text-transform: uppercase; letter-spacing: .4px;
    margin-bottom: .35rem;
}
#modalArea .form-control,
#modalArea .input-group-text {
    border-radius: 8px;
    border: 1px solid #e9ebec;
    transition: all .2s ease;
    font-size: .9rem;
}
#modalArea .input-group .form-control {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
#modalArea .input-group-text {
    background: #f8f9fc; color: #878a99; border-right: 0;
}
#modalArea .input-group:focus-within .input-group-text {
    border-color: #405189; color: #405189; background: #fff;
}
#modalArea .form-control:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}

.btn-save-area {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0; color: #fff; font-weight: 600;
    padding: .5rem 1.25rem;
    box-shadow: 0 6px 16px -6px rgba(10,179,156,.55);
    transition: transform .15s ease;
}
.btn-save-area:hover:not(:disabled) { color: #fff; transform: translateY(-1px); }

/* Modal delete */
.delete-icon-wrap {
    width: 80px; height: 80px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #fff5f3 0%, #ffe7e2 100%);
    color: #f06548;
    border-radius: 50%;
    font-size: 2.5rem;
    box-shadow: 0 0 0 8px rgba(240,101,72,.08);
    animation: pulseDelete 2s ease-in-out infinite;
}
@keyframes pulseDelete {
    0%, 100% { box-shadow: 0 0 0 8px rgba(240,101,72,.08); }
    50% { box-shadow: 0 0 0 14px rgba(240,101,72,.04); }
}

/* Color picker */
#f-color {
    width: 44px;
    height: 44px;
    padding: 3px;
    cursor: pointer;
    border-radius: 8px;
}
</style>
@endpush

@section('content')
<div class="acad-page">
<div class="row">
    <div class="col-12">
        <div class="areas-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-apps-2-line"></i></div>
                <div>
                    <h4>Áreas de Programa</h4>
                    <p class="hero-sub">Administra las áreas o disciplinas disponibles para los programas académicos</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item active">Áreas</li>
            </ol>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4" id="stats-container">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent: #405189;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Total Áreas</p>
                        <h4 class="stat-value mb-0" id="stat-total">{{ $resumen['total_areas'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-apps-2-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent: #0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Programas Asociados</p>
                        <h4 class="stat-value mb-0" id="stat-programas">{{ $resumen['total_programas'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-booklet-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent: #f7b84b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Sin Programas</p>
                        <h4 class="stat-value mb-0" id="stat-sinuso">{{ $resumen['sin_uso'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-information-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros + Nuevo --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-2">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                            <i class="ri-equalizer-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros y Acciones</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filter-search" class="form-control" placeholder="Buscar por nombre o descripción..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button id="btn-filter-clear" class="btn btn-soft-danger w-50 d-flex align-items-center justify-content-center gap-1">
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                        @can('areas.registrar')
                        <button class="btn btn-nuevo w-50 d-flex align-items-center justify-content-center gap-1" id="btn-nuevo-area">
                            <i class="ri-add-circle-line fs-16"></i> Nueva Área
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="row">
    <div class="col-12">
        <div class="card results-card">
            <div class="card-body">
                <div class="results-header">
                    <div class="results-count">
                        <i class="ri-apps-2-line"></i>
                        <span><span id="area-count" class="count-num">{{ $resumen['total_areas'] }}</span> áreas registradas</span>
                    </div>
                </div>
                <div id="table-container">
                    @include('admin.areas.partials.table', ['areas' => $areas])
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar --}}
<div class="modal fade" id="modalArea" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-apps-2-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalAreaTitle">Nueva Área</h5>
                        <small class="modal-subtitle">Define la información del área o disciplina</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formArea" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="area-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-apps-2-line"></i></span>
                                <input type="text" name="nombre" id="f-nombre" class="form-control" required maxlength="100" placeholder="Ej. Social, Salud, Ingeniería...">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <input type="color" name="color" id="f-color" class="form-control" value="#6c757d">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="f-descripcion" class="form-control" rows="3" maxlength="200" placeholder="Breve descripción del área..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-save-area" id="btnSaveArea">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Eliminar --}}
<div class="modal fade" id="modalDeleteArea" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="delete-icon-wrap mx-auto mb-3">
                    <i class="ri-error-warning-line"></i>
                </div>
                <h4 class="mb-2 fw-bold">¿Eliminar esta área?</h4>
                <p class="text-muted mb-1">Estás a punto de eliminar</p>
                <p class="fw-semibold fs-15 text-dark mb-3" id="deleteAreaNombre"></p>
                <div class="alert alert-soft-danger border-0 d-inline-flex align-items-center gap-2 py-2 px-3 mb-0">
                    <i class="ri-alert-line"></i>
                    <small class="mb-0">Esta acción no se puede deshacer</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4" id="btnConfirmDelete">
                    <i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSearch = document.getElementById('filter-search');
    const tableContainer = document.getElementById('table-container');
    const countEl = document.getElementById('area-count');
    let debounceTimer = null;

    function loadData() {
        const params = new URLSearchParams();
        const search = filterSearch.value.trim();
        if (search) params.append('search', search);

        tableContainer.innerHTML = `
            <div class="tabla-loading">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                <p class="mt-2 text-muted mb-0">Cargando áreas...</p>
            </div>`;

        fetch('{{ route("admin.areas.index") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            tableContainer.innerHTML = data.html;
            if (countEl) countEl.textContent = data.total;
            if (data.resumen) {
                document.getElementById('stat-total').textContent = data.resumen.total_areas;
                document.getElementById('stat-programas').textContent = data.resumen.total_programas;
                document.getElementById('stat-sinuso').textContent = data.resumen.sin_uso;
            }
        })
        .catch(() => {
            tableContainer.innerHTML = '<div class="text-center text-danger py-4">Error al cargar áreas</div>';
        });
    }

    filterSearch.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadData, 300);
    });

    document.getElementById('btn-filter-clear').addEventListener('click', function () {
        filterSearch.value = '';
        loadData();
    });

    function resetForm() {
        const form = document.getElementById('formArea');
        form.reset();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.getElementById('area-id').value = '';
        document.getElementById('f-color').value = '#6c757d';
    }

    // Nuevo
    document.getElementById('btn-nuevo-area')?.addEventListener('click', function () {
        resetForm();
        document.getElementById('modalAreaTitle').textContent = 'Nueva Área';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('btnSaveArea').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        new bootstrap.Modal(document.getElementById('modalArea')).show();
    });

    // Editar
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-area');
        if (!btn) return;
        const id = btn.dataset.id;
        fetch('{{ url("admin/areas") }}/' + id + '/data', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.area) return;
            resetForm();
            document.getElementById('modalAreaTitle').textContent = 'Editar Área';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('area-id').value = data.area.id;
            document.getElementById('f-nombre').value = data.area.nombre || '';
            document.getElementById('f-color').value = data.area.color || '#6c757d';
            document.getElementById('f-descripcion').value = data.area.descripcion || '';
            document.getElementById('btnSaveArea').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Actualizar';
            new bootstrap.Modal(document.getElementById('modalArea')).show();
        })
        .catch(() => alert('Error al cargar el área'));
    });

    // Submit
    document.getElementById('formArea').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('area-id').value;
        const method = document.getElementById('formMethod').value;
        const url = method === 'PUT' ? '{{ url("admin/areas") }}/' + id : '{{ url("admin/areas") }}';
        const formData = new FormData(this);
        if (method === 'PUT') formData.append('_method', 'PUT');

        const btn = document.getElementById('btnSaveArea');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalArea')).hide();
                loadData();
            } else if (data.errors) {
                for (const field in data.errors) {
                    const input = document.getElementById('f-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const wrapper = input.closest('.col-md-4, .col-md-8, .col-md-12');
                        const fb = wrapper ? wrapper.querySelector('.invalid-feedback') : null;
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(() => alert('Error al guardar el área'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = method === 'PUT'
                ? '<i class="ri-save-3-line align-bottom"></i> Actualizar'
                : '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // Eliminar
    let deleteId = null;
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-area');
        if (!btn) return;
        deleteId = btn.dataset.id;
        document.getElementById('deleteAreaNombre').textContent = btn.dataset.nombre;
        new bootstrap.Modal(document.getElementById('modalDeleteArea')).show();
    });

    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        if (!deleteId) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Eliminando...';
        fetch('{{ url("admin/areas") }}/' + deleteId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            bootstrap.Modal.getInstance(document.getElementById('modalDeleteArea')).hide();
            if (ok && data.success) {
                loadData();
            } else {
                alert(data.message || 'Error al eliminar');
            }
        })
        .catch(() => alert('Error al eliminar el área'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar';
        });
    });
});
</script>
@endpush
