@extends('admin.layouts.master')

@section('title', 'Participantes - ' . config('app.name'))

@push('css')
<style>
/* ── Hero ── */
.participantes-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px; padding: 1.5rem 1.75rem; color: #fff; overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75); margin-bottom: 1.35rem;
}
.participantes-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background:radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.participantes-hero::after { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background:radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.participantes-hero > * { position: relative; z-index: 1; }
.participantes-hero .hero-icon {
    width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px; font-size: 1.6rem;
}
.participantes-hero h4 { color: #fff; font-weight: 800; margin: 0; letter-spacing: -.01em; }
.participantes-hero .hero-sub { color: rgba(255,255,255,.82); font-size: .85rem; margin: 2px 0 0; }
.participantes-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.participantes-hero .breadcrumb-item,
.participantes-hero .breadcrumb-item a { color: rgba(255,255,255,.78); }
.participantes-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.participantes-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

/* ── Filter card ── */
.filter-card {
    background: var(--ui-surface, #fff);
    border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 16px;
    box-shadow: var(--ui-shadow, 0 4px 18px -10px rgba(64,81,137,.18));
}
.filter-card .card-header {
    background: var(--ui-header-tint, rgba(29,74,128,.045));
    border-bottom: 1px solid var(--ui-line, #eef0f4);
    padding: .9rem 1.25rem;
}
.filter-card .form-label {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: var(--ui-muted, #878a99); margin-bottom: .35rem;
}
.filter-card .input-group-text {
    background: var(--ui-surface-2, #f7f9fc);
    border: 1px solid var(--ui-line-strong, #d4dae5);
    border-right: 0; color: var(--ui-muted, #878a99); font-size: .9rem;
}
.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid var(--ui-line-strong, #d4dae5);
    font-weight: 500; font-size: .85rem;
    transition: border-color .18s ease, box-shadow .18s ease;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: var(--ui-accent, #163b6b);
    box-shadow: 0 0 0 3px var(--ui-ring, rgba(29,74,128,.16));
}
.filter-card .input-group:focus-within .input-group-text {
    border-color: var(--ui-accent, #163b6b); color: var(--ui-accent, #163b6b);
}

/* ── Results card ── */
.results-card {
    background: var(--ui-surface, #fff);
    border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 16px;
    box-shadow: var(--ui-shadow, 0 4px 18px -10px rgba(64,81,137,.18));
}
.results-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: .75rem; padding-bottom: .75rem; margin-bottom: .75rem;
    border-bottom: 1px dashed var(--ui-line, #eef0f4); flex-wrap: wrap;
}
.results-count {
    display: inline-flex; align-items: center; gap: .5rem;
    background: var(--ui-surface-2, #f7f9fc);
    padding: .4em .9em; border-radius: 999px;
    font-size: .8rem; font-weight: 600; color: var(--ui-ink-2, #495057);
    border: 1px solid var(--ui-line, #eef0f4);
}
.results-count .count-num {
    background: var(--ui-brand, #163b6b); color: #fff; font-weight: 700;
    padding: .15em .6em; border-radius: 999px; font-size: .78rem;
}

/* ── Table ── */
.tabla-participantes { table-layout: fixed; width: 100%; }
.tabla-participantes th, .tabla-participantes td {
    vertical-align: middle; padding: .65rem .55rem; overflow: hidden;
}
.tabla-participantes thead th {
    background: var(--ui-header-tint, rgba(29,74,128,.045)) !important;
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: var(--ui-accent, #163b6b);
    border-bottom: 2px solid var(--ui-line, #eef0f4);
}
.tabla-participantes tbody tr {
    border-bottom: 1px solid var(--ui-line, #eef0f4);
    transition: background-color .15s ease;
}
.tabla-participantes tbody tr:hover { background-color: var(--ui-surface-2, #f8f9fc); }

.table-card-wrap {
    border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 12px; overflow: hidden;
}

.participante-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .75rem; flex-shrink: 0;
    background: linear-gradient(135deg, var(--ui-accent, #163b6b) 0%, var(--ui-brand-2, #2a5d97) 100%);
    color: #fff; box-shadow: 0 4px 10px -4px rgba(22,59,107,.5);
}
.participante-avatar.female { background: linear-gradient(135deg, #d1458a 0%, #ec4899 100%); box-shadow: 0 4px 10px -4px rgba(209,69,138,.5); }

.cell-nombre .nombre { font-weight: 600; color: var(--ui-ink, #1b2537); font-size: .84rem; line-height: 1.2; }
.cell-nombre .ci-info { font-size: .7rem; color: var(--ui-muted, #98a2b3); margin-top: 2px; }

.cell-contacto { font-size: .74rem; min-width: 0; }
.cell-contacto .line {
    display: flex; align-items: center; gap: .35rem; line-height: 1.3;
    color: var(--ui-ink-2, #475467); overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}
.cell-contacto .line + .line { margin-top: 2px; }
.cell-contacto .line i { color: var(--ui-muted, #98a2b3); flex-shrink: 0; font-size: .85rem; }

.programas-list { display: flex; flex-wrap: wrap; gap: 4px; }
.programa-pill {
    font-size: .66rem; font-weight: 600; padding: .22em .55em;
    border-radius: 6px; background: var(--ui-info-soft, #e7f1fb);
    color: var(--ui-info, #1f6fbf); border: 1px solid rgba(31,111,191,.15);
    max-width: 140px; overflow: hidden; text-overflow: ellipsis;
    white-space: nowrap; display: inline-block;
}
.programa-pill-more {
    background: var(--ui-surface-2, #f1f3f5); color: var(--ui-muted, #667085);
    border-color: var(--ui-line, #e9ebec);
}

.sexo-badge {
    font-size: .64rem; font-weight: 700; padding: .18em .55em;
    border-radius: 999px; text-transform: uppercase; letter-spacing: .3px;
}
.sexo-badge.male { background: var(--ui-info-soft, #e7f1fb); color: var(--ui-info, #1f6fbf); }
.sexo-badge.female { background: #fce7f3; color: #d1458a; }

/* ── Action buttons ── */
.btn-action-group { display: inline-flex; gap: 3px; }
.btn-action {
    width: 32px; height: 32px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--ui-line, #eef0f4);
    background: var(--ui-surface, #fff); color: var(--ui-muted, #667085);
    font-size: .9rem; transition: all .18s ease; cursor: pointer;
}
.btn-action:hover { border-color: var(--ui-accent, #163b6b); color: var(--ui-accent, #163b6b); background: var(--ui-accent-soft, #eef1f8); }
.btn-action.edit:hover { border-color: var(--ui-accent, #163b6b); color: var(--ui-accent, #163b6b); background: var(--ui-accent-soft, #eef1f8); }
.btn-action.view:hover { border-color: var(--ui-success, #0d8574); color: var(--ui-success, #0d8574); background: var(--ui-success-soft, #e6f7ee); }

/* ── Loading / Empty ── */
.tabla-loading, .tabla-empty {
    text-align: center; padding: 3.5rem 2rem;
    background: var(--ui-surface-2, #f7f9fc); border-radius: 12px;
}
.tabla-empty .empty-icon {
    width: 72px; height: 72px; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: var(--ui-surface, #fff); border-radius: 50%;
    font-size: 2.2rem; color: var(--ui-muted, #adb5bd);
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.12);
}

/* ── Pagination ── */
.pagination .page-link {
    border-radius: 8px !important; margin: 0 2px;
    border: 1px solid var(--ui-line, #e9ebec);
    color: var(--ui-ink-2, #495057); font-weight: 600; font-size: .8rem;
    transition: all .15s ease;
}
.pagination .page-link:hover { background: var(--ui-surface-2, #f8f9fc); border-color: var(--ui-accent, #163b6b); color: var(--ui-accent, #163b6b); }
.pagination .page-item.active .page-link {
    background: var(--ui-brand, #163b6b); border-color: transparent;
    color: #fff; box-shadow: 0 4px 10px -4px rgba(22,59,107,.55);
}
.pagination .page-item.disabled .page-link { color: var(--ui-muted, #adb5bd); background: var(--ui-surface-2, #fafbfd); }

/* ── Modal editar ── */
.modal-edit-participante .modal-header {
    background: linear-gradient(135deg, var(--avip-navy-800, #102e54) 0%, var(--avip-navy-700, #163b6b) 100%);
    color: #fff; border-bottom: 0; padding: 1rem 1.3rem;
}
.modal-edit-participante .modal-title { color: #fff; font-weight: 700; }
.modal-edit-participante .modal-body { background: var(--ui-surface-2, #f7f9fc); }
.modal-edit-participante .form-label { font-size: .78rem; font-weight: 700; color: var(--ui-ink-2, #475467); margin-bottom: .35rem; }
.modal-edit-participante .form-control,
.modal-edit-participante .form-select {
    border-radius: 10px; font-size: .88rem; padding: .55rem .8rem;
    border-color: var(--ui-line-strong, #d4dae5);
}
.modal-edit-participante .form-control:focus,
.modal-edit-participante .form-select:focus {
    border-color: var(--ui-accent, #163b6b);
    box-shadow: 0 0 0 3px var(--ui-ring, rgba(29,74,128,.16));
}
.modal-edit-participante .edit-field-group {
    background: var(--ui-surface, #fff); border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 12px; padding: 1rem 1.15rem; margin-bottom: .75rem;
}
.modal-edit-participante .edit-field-group-title {
    font-size: .7rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .05em; color: var(--ui-muted, #98a2b3); margin-bottom: .75rem;
    display: flex; align-items: center; gap: .4rem;
}
.modal-edit-participante .edit-field-group-title i { font-size: .9rem; }
.modal-edit-participante .edit-avatar {
    width: 52px; height: 52px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .9rem; color: #fff;
    background: linear-gradient(135deg, var(--ui-accent, #163b6b) 0%, var(--ui-brand-2, #2a5d97) 100%);
    box-shadow: 0 6px 16px -4px rgba(22,59,107,.55); flex-shrink: 0;
}
.modal-edit-participante .edit-avatar.female { background: linear-gradient(135deg, #d1458a 0%, #ec4899 100%); }
.modal-edit-participante .edit-identity { display: flex; align-items: center; gap: .85rem; }
.modal-edit-participante .edit-identity-name { font-weight: 700; font-size: .95rem; color: var(--ui-ink, #1b2537); }
.modal-edit-participante .edit-identity-ci { font-size: .75rem; color: var(--ui-muted, #98a2b3); }
.modal-edit-participante .invalid-feedback { font-size: .72rem; }

@media (max-width: 767.98px) {
    .tabla-participantes .th-contacto, .tabla-participantes .td-contacto { display: none; }
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="row">
    <div class="col-12">
        <div class="participantes-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-team-line"></i></div>
                <div>
                    <h4>Participantes</h4>
                    <p class="hero-sub">Consulta y administra la base de participantes inscritos</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item active">Participantes</li>
            </ol>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="section-icon bg-soft-primary text-primary me-2"><i class="ri-equalizer-line"></i></div>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros de Búsqueda</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Buscar participante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filter-ci" class="form-control" placeholder="CI, nombre o apellido..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gestión</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                            <select id="filter-gestion" class="form-select">
                                <option value="" selected>Todas las gestiones</option>
                                @foreach($gestiones as $g)
                                    <option value="{{ $g }}">{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Programa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-booklet-line"></i></span>
                            <select id="filter-programa" class="form-select">
                                <option value="">Todos los programas</option>
                                @foreach($programas as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->nombre }} ({{ $prog->gestion }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button id="btn-filter-clear" class="btn btn-soft-danger w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Resultados --}}
<div class="row">
    <div class="col-12">
        <div class="card results-card mb-0">
            <div class="card-body">
                <div class="results-header">
                    <div class="results-count">
                        <i class="ri-user-3-line"></i>
                        <span><span id="participante-count" class="count-num">0</span> participantes encontrados</span>
                    </div>
                </div>
                <div id="table-container">
                    <div class="tabla-loading">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                        <p class="mt-2 text-muted mb-0">Cargando participantes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Participante --}}
<div class="modal fade modal-edit-participante" id="modalEditParticipante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-user-settings-line fs-5"></i>
                    <div>
                        <h5 class="modal-title mb-0">Editar Participante</h5>
                        <small class="opacity-75" id="modal-edit-subtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="modal-edit-body">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted mb-0">Cargando datos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterCi = document.getElementById('filter-ci');
    const filterGestion = document.getElementById('filter-gestion');
    const filterPrograma = document.getElementById('filter-programa');
    const tableContainer = document.getElementById('table-container');
    const countEl = document.getElementById('participante-count');

    let debounceTimer = null;
    let currentPage = 1;

    /* ── Carga de datos ── */
    function loadData(page) {
        const params = new URLSearchParams();
        const search = filterCi.value.trim();
        const gestion = filterGestion.value;
        const programa = filterPrograma.value;

        if (page) currentPage = page;
        params.append('page', currentPage);
        if (search) params.append('search', search);
        if (gestion) params.append('gestion', gestion);
        if (programa) params.append('programa_id', programa);

        tableContainer.innerHTML = '<div class="tabla-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando participantes...</p></div>';

        fetch('{{ route("admin.participantes.index") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            tableContainer.innerHTML = data.html;
            if (countEl) countEl.textContent = data.total;
        })
        .catch(() => {
            tableContainer.innerHTML = '<div class="text-center text-danger py-4">Error al cargar participantes</div>';
        });
    }

    filterCi.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        currentPage = 1;
        debounceTimer = setTimeout(loadData, 300);
    });
    filterGestion.addEventListener('change', function () { currentPage = 1; loadData(); });
    filterPrograma.addEventListener('change', function () { currentPage = 1; loadData(); });

    document.getElementById('btn-filter-clear').addEventListener('click', function () {
        filterCi.value = ''; filterGestion.value = ''; filterPrograma.value = '';
        currentPage = 1; loadData();
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.page-link[data-page]');
        if (btn) { e.preventDefault(); loadData(parseInt(btn.dataset.page)); }
    });

    /* ── Modal Editar ── */
    const modalEl = document.getElementById('modalEditParticipante');
    const modalBody = document.getElementById('modal-edit-body');
    const modalSubtitle = document.getElementById('modal-edit-subtitle');
    let bsModal = null;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-participante');
        if (!btn) return;
        e.preventDefault();

        const id = btn.dataset.id;
        const name = btn.dataset.name || '';
        modalSubtitle.textContent = name;
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando datos...</p></div>';

        if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        fetch('{{ url("admin/participantes") }}/' + id + '/edit', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                modalBody.innerHTML = '<div class="alert alert-danger mb-0">' + (data.error || 'Error al cargar') + '</div>';
                return;
            }
            renderEditForm(data.participante);
        })
        .catch(() => {
            modalBody.innerHTML = '<div class="alert alert-danger mb-0">Error de conexión</div>';
        });
    });

    function renderEditForm(p) {
        const sexoNorm = (p.sexo || '').trim().toUpperCase();
        const isFemale = ['F', 'MUJER', 'FEMENINO', 'FEMALE'].includes(sexoNorm);
        const isMale = ['M', 'H', 'HOMBRE', 'MASCULINO', 'MALE'].includes(sexoNorm);
        const ini = ((p.nombres || '').substring(0, 1) + (p.apellidos || '').substring(0, 1)).toUpperCase();
        const avatarClass = isFemale ? 'female' : '';

        modalBody.innerHTML = `
            <form id="form-edit-participante" novalidate>
                <div class="edit-identity mb-3">
                    <div class="edit-avatar ${avatarClass}">${ini}</div>
                    <div>
                        <div class="edit-identity-name">${p.apellidos || ''}, ${p.nombres || ''}</div>
                        <div class="edit-identity-ci">CI: ${p.ci || '—'}</div>
                    </div>
                </div>

                <div class="edit-field-group">
                    <div class="edit-field-group-title"><i class="ri-user-line"></i> Datos personales</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres *</label>
                            <input type="text" name="nombres" class="form-control" value="${escHtml(p.nombres || '')}" required maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" value="${escHtml(p.apellidos || '')}" required maxlength="100">
                            <div class="invalid-feedback">El apellido es obligatorio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CI *</label>
                            <input type="text" name="ci" class="form-control" value="${escHtml(p.ci || '')}" required maxlength="13">
                            <div class="invalid-feedback">El CI es obligatorio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" class="form-select" id="edit-sexo">
                                <option value="" ${!sexoNorm ? 'selected' : ''}>—</option>
                                <option value="Hombre" ${isMale ? 'selected' : ''}>Masculino</option>
                                <option value="Mujer" ${isFemale ? 'selected' : ''}>Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" class="form-control" value="${escHtml(p.departamento || '')}" maxlength="100">
                        </div>
                    </div>
                </div>

                <div class="edit-field-group mb-0">
                    <div class="edit-field-group-title"><i class="ri-phone-line"></i> Contacto</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" class="form-control" value="${escHtml(p.celular || '')}" maxlength="20" placeholder="77112345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" class="form-control" value="${escHtml(p.correo || '')}" maxlength="150" placeholder="correo@ejemplo.com">
                            <div class="invalid-feedback">Ingrese un correo válido.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        `;

        document.getElementById('form-edit-participante').addEventListener('submit', function (e) {
            e.preventDefault();
            submitEditForm(p.id);
        });


    }

    function submitEditForm(id) {
        const form = document.getElementById('form-edit-participante');
        const submitBtn = form.querySelector('[type="submit"]');
        const origHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(form);
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ url("admin/participantes") }}/' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bsModal.hide();
                loadData(currentPage);
                showToast('success', data.message || 'Participante actualizado correctamente.');
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            input.nextElementSibling && input.nextElementSibling.textContent && (input.nextElementSibling.textContent = data.errors[field][0]);
                        }
                    });
                } else {
                    showToast('error', data.error || 'Error al guardar.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;
            }
        })
        .catch(() => {
            showToast('error', 'Error de conexión.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = origHtml;
        });
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function showToast(type, msg) {
        const colors = { success: '#0d8574', error: '#b3243a' };
        const icons = { success: 'ri-check-line', error: 'ri-error-warning-line' };
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;display:flex;align-items:center;gap:.6rem;padding:.7rem 1.1rem;border-radius:12px;background:#fff;border:1px solid ' + (colors[type] || '#ccc') + ';box-shadow:0 8px 24px -8px rgba(0,0,0,.25);font-size:.84rem;font-weight:600;color:' + (colors[type] || '#333') + ';transform:translateX(120%);transition:transform .3s ease;';
        toast.innerHTML = '<i class="' + (icons[type] || 'ri-information-line') + '" style="font-size:1.1rem;"></i>' + msg;
        document.body.appendChild(toast);
        requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; });
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 350);
        }, 3000);
    }

    loadData();
});
</script>
@endpush
