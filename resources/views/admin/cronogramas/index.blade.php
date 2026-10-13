@extends('admin.layouts.master')

@section('title', 'Cronogramas - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.cron-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.cron-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.cron-hero::after {
    content: ""; position: absolute;
    bottom: -60px; left: 30%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%);
    border-radius: 50%;
}
.cron-hero > * { position: relative; z-index: 1; }
.cron-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.cron-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.cron-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.cron-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.cron-hero .breadcrumb-item, .cron-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.cron-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.cron-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

/* ===== Filter card ===== */
.cron-filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    overflow: hidden;
}
[data-layout-mode="dark"] .cron-filter-card { background: #1d2125; border-color: #2a2f34; }
.cron-filter-card .filter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .85rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .cron-filter-card .filter-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.filter-header-left { display: flex; align-items: center; gap: .65rem; min-width: 0; }
.filter-header-icon {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 10px;
    font-size: 1rem;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
    flex-shrink: 0;
}
.filter-header-title {
    font-size: .85rem;
    font-weight: 700;
    color: #212529;
    line-height: 1.2;
    margin: 0;
}
.filter-header-sub {
    font-size: .72rem;
    color: #878a99;
    margin: 0;
    font-weight: 500;
}
[data-layout-mode="dark"] .filter-header-title { color: #fff; }

.filter-count {
    background: #fff;
    border: 1px solid #e9ebec;
    border-radius: 999px;
    padding: .3em .8em;
    font-size: .72rem;
    font-weight: 700;
    color: #495057;
    display: inline-flex; align-items: center; gap: .35rem;
}
.filter-count .count-num { color: #405189; font-size: .85rem; }
[data-layout-mode="dark"] .filter-count { background: #212529; border-color: #2a2f34; color: #ced4da; }

.cron-filter-card .filter-body {
    padding: .9rem 1.25rem;
    max-height: 180px;
    overflow-y: auto;
}
.cron-filter-card .filter-body::-webkit-scrollbar { width: 6px; }
.cron-filter-card .filter-body::-webkit-scrollbar-thumb { background: #e9ebec; border-radius: 3px; }

.filter-chips { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
.filter-btn {
    --pill-bg: #f3f5f9;
    --pill-color: #495057;
    background: var(--pill-bg);
    color: var(--pill-color);
    font-size: .78rem;
    border-radius: 999px;
    padding: .3rem .85rem .3rem .35rem;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
    border: 1px solid transparent;
    font-weight: 600;
    line-height: 1.3;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    position: relative;
}
.filter-btn .avatar-init {
    width: 24px; height: 24px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.3);
    color: inherit;
    font-size: .68rem;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,.25);
}
.filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px -6px rgba(0,0,0,.2);
}
.filter-btn.active {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--pill-bg), 0 6px 14px -6px rgba(0,0,0,.25);
    transform: translateY(-1px);
}
.filter-btn.active::after {
    content: "\f00c";
    font-family: "Font Awesome 6 Free", sans-serif;
    position: absolute;
    top: -4px; right: -4px;
    width: 16px; height: 16px;
    background: #fff;
    color: var(--pill-color);
    border-radius: 50%;
    font-size: .55rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}
.filter-btn.active::after {
    /* Fallback: usar ri-check con un span en lugar de pseudo */
    content: "✓";
    font-family: inherit;
    font-weight: 900;
    font-size: .65rem;
    line-height: 1;
}

/* Botón Todos especial */
.filter-btn-all {
    --pill-bg: #f8f9fc;
    --pill-color: #495057;
    border: 1px solid #e9ebec;
}
.filter-btn-all .avatar-init {
    background: linear-gradient(135deg, #878a99 0%, #adb5bd 100%);
    color: #fff;
    border-color: transparent;
}
.filter-btn-all.active {
    --pill-bg: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    background: var(--pill-bg);
    color: #fff;
    border-color: transparent;
}
.filter-btn-all.active .avatar-init { background: rgba(255,255,255,.25); color: #fff; }
.filter-btn-all.active::after { color: #405189; }

[data-layout-mode="dark"] .filter-btn { background: #212529; color: #ced4da; }

/* ===== Tabs ===== */
.cron-calendar-card .nav-tabs-custom {
    border-bottom: 1px solid #eef0f4;
    padding-bottom: 0;
    margin-top: -6px;
}
.cron-calendar-card .nav-tabs-custom .nav-link {
    border: 0;
    color: #878a99;
    font-weight: 600;
    font-size: .82rem;
    padding: .6rem 1rem;
    border-radius: 0;
    position: relative;
    transition: color .15s ease;
}
.cron-calendar-card .nav-tabs-custom .nav-link.active {
    color: #405189;
    background: transparent;
}
.cron-calendar-card .nav-tabs-custom .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    border-radius: 2px 2px 0 0;
}
.cron-calendar-card .nav-tabs-custom .nav-link:hover {
    color: #405189;
}
[data-layout-mode="dark"] .cron-calendar-card .nav-tabs-custom .nav-link { color: #878a99; }
[data-layout-mode="dark"] .cron-calendar-card .nav-tabs-custom .nav-link.active { color: #fff; }
[data-layout-mode="dark"] .cron-calendar-card .nav-tabs-custom { border-bottom-color: #2a2f34; }

/* ===== Calendar card ===== */
.cron-calendar-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .cron-calendar-card { border-color: #2a2f34; }

/* FullCalendar polish */
#calendar { max-width: 100%; }
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
.fc .fc-button-group { gap: .25rem; }
.fc .fc-button-group > .fc-button { border-radius: 8px !important; }
[data-layout-mode="dark"] .fc .fc-button { background: #1d2125 !important; border-color: #2a2f34 !important; color: #ced4da !important; }
[data-layout-mode="dark"] .fc .fc-button:hover { background: #212529 !important; }

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
[data-layout-mode="dark"] .fc .fc-daygrid-day:hover { background-color: #1a1d20; }
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

/* ===== Modal ===== */
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
#modalHorario .modal-footer {
    background: #fff;
    border-top: 1px solid #eef0f4;
    padding: 1rem 1.5rem;
}
[data-layout-mode="dark"] #modalHorario .modal-footer { background: #212529; border-top-color: #2a2f34; }

/* Detail rows in modal */
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
    flex: 0 0 130px;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .3px;
    font-weight: 600;
    color: #878a99;
    padding-top: 2px;
}
.detail-value { flex: 1; font-size: .88rem; font-weight: 500; color: #212529; min-width: 0; word-break: break-word; }
[data-layout-mode="dark"] .detail-value { color: #fff; }
.detail-value .estado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .72rem; font-weight: 600;
    padding: .25em .65em; border-radius: 999px;
    border: 1px solid currentColor;
}
.detail-value .estado-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* ===== Modulos Inicio ===== */
#modulosInicioTable table { font-size: .82rem; }
#modulosInicioTable table th { font-size: .7rem; text-transform: uppercase; letter-spacing: .3px; background: #f8f9fc; padding: .5rem .4rem; }
#modulosInicioTable table td { padding: .45rem .4rem; vertical-align: middle; }
#modulosInicioTable .sesion-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: .7rem; font-weight: 600; padding: .2em .6em; border-radius: 999px;
}
#modulosInicioTable .avatar-mini {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .65rem; font-weight: 700; color: #fff; flex-shrink: 0;
}
.btn-soft-purple {
    background: rgba(106,27,154,.1);
    color: #6a1b9a;
    border: none;
}
.btn-soft-purple:hover {
    background: rgba(106,27,154,.2);
    color: #6a1b9a;
}
[data-layout-mode="dark"] .btn-soft-purple { background: rgba(106,27,154,.2); color: #ce93d8; }
[data-layout-mode="dark"] .btn-soft-purple:hover { background: rgba(106,27,154,.35); }

/* ===== Enlaces de nombre + moodle (compatibles con responsables) ===== */
.prog-acc-name-link, .modulo-nombre-link {
    color: inherit;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: color .15s, border-color .15s;
}
.prog-acc-name-link:hover, .modulo-nombre-link:hover {
    color: #405189;
    border-bottom-color: #405189;
}
.moodle-icon-link {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px;
    background: #fff4e6;
    color: #f76707;
    border: 1px solid #ffd8a8;
    border-radius: 5px;
    margin-left: 6px;
    text-decoration: none;
    font-size: .72rem;
    vertical-align: middle;
    transition: background .15s, color .15s, transform .15s;
}
.moodle-icon-link:hover {
    background: #f76707;
    color: #fff;
    transform: translateY(-1px);
}
</style>
@endpush

@section('content')
<div class="acad-page">
<div class="row">
    <div class="col-12">
        <div class="cron-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-calendar-2-line"></i></div>
                <div>
                    <h4>Cronograma de Horarios</h4>
                    <p class="hero-sub">Visualiza y gestiona las sesiones programadas por asignado</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item active">Cronogramas</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card cron-calendar-card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-calendario" role="tab" aria-selected="true">
                            <i class="ri-calendar-2-line me-1"></i> Calendario
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-modulos-inicio" role="tab" aria-selected="false">
                            <i class="ri-book-open-line me-1"></i> Módulos que inician primer horario
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-calendario" role="tabpanel">
                        <div class="cron-filter-card mb-3">
                            <div class="filter-header">
                                <div class="filter-header-left">
                                    <span class="filter-header-icon"><i class="ri-team-line"></i></span>
                                    <div>
                                        <p class="filter-header-title">Filtrar por asignado</p>
                                        <p class="filter-header-sub">Selecciona una persona para ver solo sus sesiones</p>
                                    </div>
                                </div>
                                <span class="filter-count">
                                    <i class="ri-user-line"></i> <span class="count-num">{{ count($asignados) }}</span> asignados
                                </span>
                            </div>
                            <div class="filter-body">
                                <div class="filter-chips">
                                    <button class="filter-btn filter-btn-all active" data-asignado="">
                                        <span class="avatar-init"><i class="ri-apps-2-line"></i></span>
                                        Todos
                                    </button>
                                    @foreach($asignados as $a)
                                        @php
                                            $bg = $a->hexadecimal ?? '#6c757d';
                                            $ini = strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1));
                                        @endphp
                                        <button class="filter-btn" style="--pill-bg: {{ $bg }}; --pill-color: #fff; background: {{ $bg }}; color: #fff;" data-asignado="{{ $a->id }}" title="{{ $a->nombres }} {{ $a->apellidos }}">
                                            <span class="avatar-init">{{ $ini }}</span>
                                            {{ $a->apellidos }}, {{ $a->nombres }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div id="calendar"></div>
                    </div>
                    <div class="tab-pane" id="tab-modulos-inicio" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold mb-0 fs-14">Módulos que inician primer horario</h6>
                                <small class="text-muted">Módulos cuya primera sesión cae en la semana seleccionada</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSemanaPrev"><i class="ri-arrow-left-s-line"></i></button>
                                <span id="semanaLabel" class="fw-semibold fs-13 text-nowrap" style="min-width:220px;text-align:center;">—</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSemanaNext"><i class="ri-arrow-right-s-line"></i></button>
                            </div>
                        </div>
                        <div id="modulosInicioTable">
                            <div class="text-center text-muted py-4">
                                <i class="ri-loader-4-line ri-spin fs-4 mb-2 d-block"></i>
                                <span>Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal detalle horario --}}
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
            <div class="modal-body" id="modalHorarioBody">
                {{-- Contenido inyectado por JS --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Horarios del Módulo (desde modulos-inicio) --}}
<div class="modal fade" id="modalModuloHorarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-2-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold" id="modalModuloTitle" style="color:#fff;">—</h5>
                        <small class="modal-subtitle" style="color:rgba(255,255,255,.85);font-size:.78rem;" id="modalModuloPrograma">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.25rem;background:#fafbfd;">
                <div class="row g-3">
                    <div class="col-7">
                        <div class="detail-section" style="padding:.85rem 1rem;">
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <span class="detail-section-icon bg-soft-primary text-primary" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-time-line"></i></span>
                                <span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Horarios</span>
                                <span class="badge bg-soft-primary text-primary ms-auto" id="modalModuloCount">0</span>
                            </div>
                            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                                <table class="table table-bordered align-middle mb-0" style="font-size:.78rem;">
                                    <thead class="table-light text-muted text-uppercase" style="font-size:.62rem;">
                                        <tr>
                                            <th style="width:30px;" class="text-center">#</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Estado</th>
                                            <th>Asignado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalModuloHorariosBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="detail-section" style="padding:.85rem 1rem;">
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <span class="detail-section-icon bg-soft-success text-success" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-user-star-line"></i></span>
                                <span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Docente</span>
                            </div>
                            <div id="modalDocenteInfo" class="mb-2">
                                <div class="text-center text-muted py-2"><span class="spinner-border spinner-border-sm"></span></div>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="detail-section-icon bg-soft-info text-info" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-search-2-line"></i></span>
                                <span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Buscar / Asignar</span>
                            </div>
                            <div class="input-group input-group-sm mb-1">
                                <input type="text" id="inputBuscarDocente" class="form-control" placeholder="Carnet de identidad" maxlength="10">
                                <button class="btn btn-primary" type="button" id="btnBuscarDocente"><i class="ri-search-line"></i></button>
                            </div>
                            <div id="docenteResultadoBusqueda"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Enviar credenciales al docente --}}
<div class="modal fade" id="modalEnviarCredenciales" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-lock-line me-1"></i>Credenciales de Acceso</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem;">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-2" style="width:56px;height:56px;border-radius:50%;background:#f0f4ff;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-user-star-fill" style="font-size:1.5rem;color:#405189;"></i>
                    </div>
                    <h6 class="fw-semibold mb-0" id="credDocenteNombre">—</h6>
                </div>
                <div class="bg-light rounded p-3 mb-3" style="font-size:.88rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Usuario</span>
                        <div>
                            <span class="fw-semibold" id="credDocenteUsuario">—</span>
                            <button class="btn btn-sm btn-icon btn-soft-primary px-1 py-0 ms-1 btn-copy-credencial" data-copy="credDocenteUsuario" title="Copiar"><i class="ri-file-copy-line" style="font-size:.75rem;"></i></button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Contraseña</span>
                        <div>
                            <span class="fw-semibold" id="credDocenteContrasena">—</span>
                            <button class="btn btn-sm btn-icon btn-soft-primary px-1 py-0 ms-1 btn-copy-credencial" data-copy="credDocenteContrasena" title="Copiar"><i class="ri-file-copy-line" style="font-size:.75rem;"></i></button>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-success btn-sm w-100" id="btnEnviarWhatsApp" target="_blank">
                    <i class="ri-whatsapp-line"></i> Enviar por WhatsApp
                </a>
            </div>
            <div class="modal-footer" style="padding:.65rem 1rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Registro rápido de docente --}}
<div class="modal fade" id="modalRegistrarDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-user-add-line me-1"></i>Registrar Docente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem;">
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">CI</label>
                    <input type="text" id="regDocCI" class="form-control form-control-sm" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Nombres</label>
                    <input type="text" id="regDocNombres" class="form-control form-control-sm" placeholder="Nombres">
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Apellidos</label>
                    <input type="text" id="regDocApellidos" class="form-control form-control-sm" placeholder="Apellidos">
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Correo</label>
                    <input type="email" id="regDocCorreo" class="form-control form-control-sm" placeholder="correo@ejemplo.com">
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Celular</label>
                    <input type="text" id="regDocCelular" class="form-control form-control-sm" placeholder="Celular">
                </div>
            </div>
            <div class="modal-footer" style="padding:.75rem 1rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnGuardarDocente"><i class="ri-save-3-line"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Usuario docente --}}
<div class="modal fade" id="modalDocenteUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-lock-line me-1"></i>Acceso de Docente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem;">
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Usuario</label>
                    <input type="text" id="docUserUsuario" class="form-control form-control-sm" placeholder="usuario">
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted fw-semibold fs-12">Contraseña</label>
                    <input type="text" id="docUserContrasena" class="form-control form-control-sm" placeholder="contraseña">
                </div>
                <small class="text-muted">Estos datos serán enviados al docente para acceder al sistema.</small>
            </div>
            <div class="modal-footer" style="padding:.75rem 1rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning btn-sm" id="btnGuardarDocenteUsuario"><i class="ri-save-3-line"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Editar Enlace Moodle del Programa --}}
<div class="modal fade" id="modalProgramaMoodle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-graduation-cap-line me-1"></i>Enlace Moodle del Programa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProgramaMoodle">
                @csrf
                <input type="hidden" name="programa_id" id="edit-programa-moodle-id">
                <div class="modal-body" style="padding:1rem;">
                    <div class="mb-2">
                        <label class="form-label fs-12 text-muted">URL del curso en Moodle</label>
                        <input type="url" name="moodle_link" id="edit-programa-moodle-link"
                               class="form-control" maxlength="500"
                               placeholder="https://moodle.ejemplo.com/course/...">
                    </div>
                    <small class="text-muted">Deja en blanco para eliminar el enlace.</small>
                </div>
                <div class="modal-footer" style="padding:.65rem 1rem;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnGuardarProgramaMoodle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal: Editar Enlace Moodle del Módulo --}}
<div class="modal fade" id="modalModuloMoodle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-book-2-line me-1"></i>Enlace Moodle del Módulo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModuloMoodle">
                @csrf
                <input type="hidden" name="modulo_id" id="edit-modulo-moodle-id">
                <div class="modal-body" style="padding:1rem;">
                    <div class="mb-2">
                        <label class="form-label fs-12 text-muted">URL del curso en Moodle</label>
                        <input type="url" name="moodle_link" id="edit-modulo-moodle-link"
                               class="form-control" maxlength="500"
                               placeholder="https://moodle.ejemplo.com/course/...">
                    </div>
                    <small class="text-muted">Deja en blanco para eliminar el enlace.</small>
                </div>
                <div class="modal-footer" style="padding:.65rem 1rem;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnGuardarModuloMoodle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/fullcalendar/index.global.min.js') }}"></script>
<script>
const ROUTE_PROGRAMA_BASE = '{{ route("admin.programas.show", ["id" => "PID"]) }}';
const ROUTE_MODULO_BASE = '{{ route("admin.programas.modulo-detalle", ["modulo" => "MID"]) }}';
const urlModulosInicio = '{{ route("admin.cronogramas.modulos-inicio") }}';
const ROUTE_MODULO_HORARIOS = '{{ route("admin.cronogramas.modulo-horarios", ["modulo" => "MID"]) }}';
const csrfToken = '{{ csrf_token() }}';
const ADMIN_NOMBRE = '{{ Auth::user()->fullname ?? Auth::user()->username ?? "Administrador" }}';

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    let selectedAsignado = '';
    let semanaInicio = new Date();
    let semanaInitialized = false;

    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedAsignado = this.dataset.asignado;
            calendar.refetchEvents();
        });
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        buttonText: {
            today: 'Hoy'
        },
        events: {
            url: '{{ route("admin.cronogramas.events") }}',
            extraParams: function() {
                return { asignado_id: selectedAsignado };
            }
        },
        eventDidMount: function(info) {
            const p = info.event.extendedProps;
            if (p.source === 'masterclass') {
                info.el.title = 'Masterclass: ' + p.nombre_evento;
                info.el.style.background = 'transparent';
                info.el.style.border = '3px solid #8b5cf6';
                info.el.style.color = '#000';
            } else {
                info.el.title = p.programa + ' — ' + p.modulo;
                if (p.horario_tipo === 'Nivelacion') {
                    info.el.style.background = 'repeating-linear-gradient(45deg, #f7b84b, #f7b84b 6px, #fff3cd 6px, #fff3cd 12px)';
                    info.el.style.border = '2px solid #d97706';
                    info.el.style.color = '#7c2d12';
                    info.el.style.fontWeight = '600';
                }
            }
        },
        datesSet: function(info) {
            if (semanaInitialized) {
                const primeraSemana = getWeekRange(info.start);
                const currentRange = getWeekRange(semanaInicio);
                if (fmtDate(primeraSemana.start) !== fmtDate(currentRange.start)) {
                    semanaInicio = new Date(primeraSemana.start);
                    actualizarSemana();
                }
            }
        },
        loading: function(loading) {
            calendarEl.style.opacity = loading ? '0.5' : '1';
        },
        eventClick: function(info) {
            const e = info.event.extendedProps;

            if (e.source === 'masterclass') {
                const estLabel = { programado: 'Programado', en_curso: 'En curso', finalizado: 'Finalizado', cancelado: 'Cancelado' };
                const estColor = { programado: '#299cdb', en_curso: '#f7b84b', finalizado: '#0ab39c', cancelado: '#f06548' };
                const st = e.estado || 'programado';
                const costo = e.costo_inscripcion ? 'Bs ' + parseFloat(e.costo_inscripcion).toFixed(2) : 'Gratuito';
                const capacidad = e.capacidad_maxima ? e.asistentes_registrados + ' / ' + e.capacidad_maxima : (e.asistentes_registrados || 0);
                const ubicacion = e.enlace_virtual
                    ? '<a href="' + e.enlace_virtual + '" target="_blank" rel="noopener" class="text-decoration-none">' + e.enlace_virtual + '</a>'
                    : (e.ubicacion || '—');
                const html =
                    '<div class="detail-section" style="padding:.75rem 1rem;">' +
                        '<div class="row g-2">' +
                            '<div class="col-12">' +
                                '<div class="d-flex align-items-center gap-1 mb-1">' +
                                    '<span class="detail-section-icon bg-soft-primary text-primary" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-presentation-line"></i></span>' +
                                    '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Evento</span>' +
                                '</div>' +
                                '<div class="fw-semibold fs-13">' + (e.nombre_evento || '—') + '</div>' +
                                (e.programa ? '<div class="text-muted fs-12">Programa: ' + e.programa + (e.programa_codigo ? ' <span class="badge bg-soft-dark text-dark">' + e.programa_codigo + '</span>' : '') + '</div>' : '') +
                            '</div>' +
                            '<div class="col-6">' +
                                '<div class="d-flex align-items-center gap-1 mb-1">' +
                                    '<span class="detail-section-icon bg-soft-info text-info" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-calendar-line"></i></span>' +
                                    '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Fechas</span>' +
                                '</div>' +
                                '<div class="fw-semibold fs-12">Inicio: ' + (e.fecha_inicio || '—') + '</div>' +
                                '<div class="fw-semibold fs-12">Fin: ' + (e.fecha_fin || '—') + '</div>' +
                            '</div>' +
                            '<div class="col-6">' +
                                '<div class="d-flex align-items-center gap-1 mb-1">' +
                                    '<span class="detail-section-icon bg-soft-success text-success" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-map-pin-line"></i></span>' +
                                    '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Ubicación</span>' +
                                '</div>' +
                                '<div class="fs-12">' + ubicacion + '</div>' +
                            '</div>' +
                            '<div class="col-6">' +
                                '<div class="d-flex align-items-center gap-1 mb-1">' +
                                    '<span class="detail-section-icon bg-soft-warning text-warning" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-money-dollar-circle-line"></i></span>' +
                                    '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Costo</span>' +
                                '</div>' +
                                '<div class="fw-semibold fs-13">' + costo + '</div>' +
                            '</div>' +
                            '<div class="col-6">' +
                                '<div class="d-flex align-items-center gap-1 mb-1">' +
                                    '<span class="detail-section-icon bg-soft-secondary text-secondary" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-group-line"></i></span>' +
                                    '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Capacidad</span>' +
                                '</div>' +
                                '<div class="fw-semibold fs-13">' + capacidad + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<hr class="my-2">' +
                        '<div class="d-flex align-items-center gap-1 mb-1">' +
                            '<span class="detail-section-icon bg-soft-danger text-danger" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-flag-line"></i></span>' +
                            '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Estado</span>' +
                        '</div>' +
                        '<div><span class="badge" style="background:' + (estColor[st] || '#878a99') + ';color:#fff;">' + (estLabel[st] || st) + '</span></div>' +
                    '</div>';
                document.getElementById('modalHorarioBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalHorario')).show();
                return;
            }

            const estadoColors = {
                'Confirmado': '#0ab39c',
                'Programado': '#299cdb',
                'Desarrollado': '#405189',
                'Postergado': '#f7b84b',
                'Suspendido': '#f06548',
                'Concluido': '#0ab39c',
                'En Desarrollo': '#299cdb',
                'No Inicio': '#878a99',
                'Pendiente': '#f7b84b'
            };

            function pill(val) {
                if (!val || val === '—') return '<span class="text-muted">—</span>';
                const color = estadoColors[val] || '#878a99';
                return '<span class="estado-pill" style="color:' + color + ';">' + val + '</span>';
            }

            const docente = (e.docente_nombres || e.docente_apellidos)
                ? '<span class="fw-semibold">' + (e.docente_nombres || '') + ' ' + (e.docente_apellidos || '') + '</span>'
                  + (e.docente_ci ? ' <span class="text-muted ms-1">CI: ' + e.docente_ci + '</span>' : '')
                : '<span class="text-muted">— Sin asignar</span>';

            const asignado = e.asignado_nombres
                ? '<span class="fw-semibold">' + e.asignado_nombres + ' ' + (e.asignado_apellidos || '') + '</span>'
                : '<span class="text-muted">—</span>';

            const html =
                '<div class="detail-section" style="padding:.75rem 1rem;">' +
                    '<div class="row g-2">' +
                        '<div class="col-6">' +
                            '<div class="d-flex align-items-center gap-1 mb-1">' +
                                '<span class="detail-section-icon bg-soft-primary text-primary" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-graduation-cap-line"></i></span>' +
                                '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Programa</span>' +
                            '</div>' +
                            '<div class="fw-semibold fs-13"><a href="' + ROUTE_PROGRAMA_BASE.replace('PID', e.programa_id) + '" class="text-decoration-none">' + (e.programa || '—') + '</a>' +
                            (e.programa_moodle_link ? '<a href="' + e.programa_moodle_link + '" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del programa"><i class="ri-graduation-cap-line"></i></a>' : '') +
                            ' <span class="badge bg-soft-dark text-dark">' + (e.programa_codigo || '') + '</span></div>' +
                            '<div class="text-muted fs-12">' + (e.tipo || '—') + ' · ' + (e.gestion || '—') + '</div>' +
                        '</div>' +
                        '<div class="col-6">' +
                            '<div class="d-flex align-items-center gap-1 mb-1">' +
                                '<span class="detail-section-icon bg-soft-info text-info" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-book-2-line"></i></span>' +
                                '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Módulo</span>' +
                            '</div>' +
                            '<div class="fw-semibold fs-13"><a href="' + ROUTE_MODULO_BASE.replace('MID', e.modulo_id) + '" class="text-decoration-none">' + (e.modulo || '—') + '</a>' +
                            (e.modulo_moodle_link ? '<a href="' + e.modulo_moodle_link + '" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del módulo"><i class="ri-graduation-cap-line"></i></a>' : '') +
                            '</div>' +
                            '<div class="fs-12">' + pill(e.modulo_desarrollo) + '</div>' +
                            (e.horario_tipo === 'Nivelacion'
                                ? '<div class="mt-1"><span class="badge" style="background:#f7b84b;color:#fff;"><i class="ri-refund-2-line me-1"></i>Nivelación</span></div>'
                                : '') +
                        '</div>' +
                        '<div class="col-6">' +
                            '<div class="d-flex align-items-center gap-1 mb-1">' +
                                '<span class="detail-section-icon bg-soft-warning text-warning" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-time-line"></i></span>' +
                                '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Horario</span>' +
                            '</div>' +
                            '<div class="fw-semibold fs-13">' + (e.hora_inicio || '—') + ' — ' + (e.hora_fin || '—') + '</div>' +
                            '<div class="fs-12">' + pill(e.horario_estado) + '</div>' +
                        '</div>' +
                        '<div class="col-6">' +
                            '<div class="d-flex align-items-center gap-1 mb-1">' +
                                '<span class="detail-section-icon bg-soft-success text-success" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-user-star-line"></i></span>' +
                                '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Personas</span>' +
                            '</div>' +
                            '<div class="fs-12"><span class="text-muted">Docente:</span> ' + docente + '</div>' +
                            '<div class="fs-12"><span class="text-muted">Asignado:</span> ' + asignado + '</div>' +
                        '</div>' +
                    '</div>' +
                    (e.observaciones && e.observaciones !== '—'
                        ? '<hr class="my-2">' +
                          '<div class="d-flex align-items-center gap-1 mb-1">' +
                              '<span class="detail-section-icon bg-soft-secondary text-secondary" style="width:22px;height:22px;font-size:.7rem;border-radius:6px;"><i class="ri-chat-1-line"></i></span>' +
                              '<span class="text-muted text-uppercase" style="font-size:.65rem;font-weight:700;letter-spacing:.3px;">Observaciones</span>' +
                          '</div>' +
                          '<div class="fs-12">' + e.observaciones + '</div>'
                        : '') +
                '</div>';

            document.getElementById('modalHorarioBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalHorario')).show();
        }
    });

    calendar.render();
    semanaInitialized = true;
    actualizarSemana();

    // Re-render calendar when tab is shown (fixes layout issues)
    const tabCalendario = document.querySelector('[data-bs-toggle="tab"][href="#tab-calendario"]');
    if (tabCalendario) {
        tabCalendario.addEventListener('shown.bs.tab', function () {
            calendar.updateSize();
        });
    }

    // ======= MÓDULOS QUE INICIAN (SEMANA) =======
    function getWeekRange(date) {
        const d = new Date(date);
        const day = d.getDay();
        const start = new Date(d);
        start.setDate(d.getDate() - day);
        const end = new Date(start);
        end.setDate(start.getDate() + 6);
        return { start, end };
    }

    function fmtDate(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function actualizarSemana() {
        const range = getWeekRange(semanaInicio);
        const ini = range.start;
        const fin = range.end;

        const opts = { day: '2-digit', month: 'short' };
        document.getElementById('semanaLabel').textContent =
            'Semana del ' + ini.toLocaleDateString('es-BO', opts) + ' al ' + fin.toLocaleDateString('es-BO', opts) + ' del ' + fin.getFullYear();

        cargarModulosInicio(fmtDate(ini), fmtDate(fin));
    }

    function cargarModulosInicio(inicio, fin) {
        const container = document.getElementById('modulosInicioTable');
        container.innerHTML = '<div class="text-center text-muted py-4"><i class="ri-loader-4-line ri-spin fs-4 mb-2 d-block"></i><span>Cargando...</span></div>';

        fetch(urlModulosInicio + '?inicio=' + inicio + '&fin=' + fin)
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    container.innerHTML = '<div class="text-center text-muted py-4"><i class="ri-inbox-2-line fs-3 mb-2 d-block"></i><span>No hay módulos con primer horario en esta semana</span></div>';
                    return;
                }

                let html = '<div class="table-responsive"><table class="table align-middle mb-0 border">' +
                    '<thead><tr>' +
                        '<th>#</th>' +
                        '<th>Programa</th>' +
                        '<th>Módulo</th>' +
                        '<th>Primera Sesión</th>' +
                        '<th>Docente</th>' +
                        '<th>Responsable</th>' +
                        '<th>Sesiones</th>' +
                    '</tr></thead><tbody>';

                data.forEach(function (m, i) {
                    const fecha = m.primera_fecha ? new Date(m.primera_fecha + 'T' + (m.primera_hora || '00:00')) : null;
                    const fechaStr = fecha
                        ? fecha.toLocaleDateString('es-BO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).replace(',', '').replace(/(\d{4})$/, 'del $1')
                        : '—';
                    const horaStr = m.primera_hora || '—';

                    const docente = (m.docente_nombres || m.docente_apellidos)
                        ? m.docente_apellidos + ', ' + m.docente_nombres
                        : '<span class="text-muted">—</span>';

                    const tieneUser = m.docente_usuario && m.docente_contrasena;
                    const docenteHtml = '<div class="d-flex align-items-center gap-1">' +
                        '<span class="fw-semibold">' + docente + '</span>' +
                        (tieneUser
                            ? '<button class="btn btn-sm btn-icon btn-soft-warning px-1 py-0 ms-1 btn-enviar-credenciales" title="Enviar credenciales" data-nombres="' + (m.docente_nombres || '') + '" data-apellidos="' + (m.docente_apellidos || '') + '" data-usuario="' + m.docente_usuario + '" data-contrasena="' + m.docente_contrasena + '" data-celular="' + (m.docente_celular || '') + '" data-programa="' + (m.programa_nombre || '') + '" data-modulo="' + (m.modulo_nombre || '') + '" data-moodle-link="' + (m.modulo_moodle_link || '') + '"><i class="ri-send-plane-fill" style="font-size:.7rem;"></i></button>'
                            : '') +
                        '</div>';

                    const asignado = (m.asignado_nombres || m.asignado_apellidos)
                        ? '<span class="d-inline-flex align-items-center gap-1"><span class="avatar-mini" style="background:' + (m.asignado_hex || '#6c757d') + '">' + (m.asignado_nombres ? m.asignado_nombres.charAt(0) : '') + '</span>' + m.asignado_nombres + ' ' + (m.asignado_apellidos || '') + '</span>'
                        : '<span class="text-muted">—</span>';

                    const badgeMod = m.modulo_desarrollo === 'Desarrollado'
                        ? '<span class="sesion-badge" style="background:#d4edda;color:#155724;">' + m.modulo_desarrollo + '</span>'
                        : '<span class="sesion-badge" style="background:#fff3cd;color:#856404;">' + (m.modulo_desarrollo || 'Pendiente') + '</span>';

                    const moodleProg = m.programa_moodle_link
                        ? '<a href="' + m.programa_moodle_link + '" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del programa"><i class="ri-graduation-cap-line"></i></a>'
                        : '';
                    const editProg = '<button class="btn btn-sm btn-icon px-1 py-0 btn-moodle-programa" style="font-size:.65rem;" title="Editar enlace Moodle del programa" data-programa-id="' + m.programa_id + '" data-moodle-link="' + (m.programa_moodle_link || '') + '"><i class="ri-edit-line"></i></button>';

                    const moodleMod = m.modulo_moodle_link
                        ? '<a href="' + m.modulo_moodle_link + '" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del módulo"><i class="ri-graduation-cap-line"></i></a>'
                        : '';
                    const editMod = '<button class="btn btn-sm btn-icon px-1 py-0 btn-moodle-modulo" style="font-size:.65rem;" title="Editar enlace Moodle del módulo" data-modulo-id="' + m.id + '" data-moodle-link="' + (m.modulo_moodle_link || '') + '"><i class="ri-edit-line"></i></button>';

                    html += '<tr>' +
                        '<td class="fw-semibold text-muted">' + (i + 1) + '</td>' +
                        '<td><div class="d-flex align-items-center gap-1"><a href="' + ROUTE_PROGRAMA_BASE.replace('PID', m.programa_id) + '" class="fw-semibold text-decoration-none prog-acc-name-link">' + (m.programa_nombre || '—') + '</a>' + moodleProg + editProg + '</div><small class="text-muted">' + (m.programa_codigo || '') + ' · ' + (m.tipo_nombre || '') + '</small></td>' +
                        '<td><div class="d-flex align-items-center gap-1"><a href="' + ROUTE_MODULO_BASE.replace('MID', m.id) + '" class="fw-semibold text-decoration-none modulo-nombre-link">' + (m.modulo_nombre || '—') + '</a>' + moodleMod + editMod + '</div>' + badgeMod + '</td>' +
                        '<td><span class="fw-semibold">' + fechaStr + '</span><br><small class="text-muted">' + horaStr + '</small></td>' +
                        '<td>' + docenteHtml + '</td>' +
                        '<td>' + asignado + '</td>' +
                        '<td class="text-center"><button class="btn btn-sm btn-soft-primary px-2 py-0 fs-12 fw-semibold btn-ver-horarios" data-modulo-id="' + m.id + '" data-modulo-nombre="' + (m.modulo_nombre || '') + '" data-programa="' + (m.programa_nombre || '') + '" title="Ver horarios">' + m.total_sesiones + ' <i class="ri-calendar-event-line"></i></button></td>' +
                    '</tr>';
                });

                html += '</tbody></table></div>';
                container.innerHTML = html;
            })
            .catch(function () {
                container.innerHTML = '<div class="text-center text-danger py-4"><i class="ri-error-warning-line fs-3 mb-2 d-block"></i><span>Error al cargar datos</span></div>';
            });
    }

    document.getElementById('btnSemanaPrev').addEventListener('click', function () {
        semanaInicio.setDate(semanaInicio.getDate() - 7);
        actualizarSemana();
    });

    document.getElementById('btnSemanaNext').addEventListener('click', function () {
        semanaInicio.setDate(semanaInicio.getDate() + 7);
        actualizarSemana();
    });

    // ======= MODAL HORARIOS DEL MÓDULO =======
    let currentModuloId = null;

    document.getElementById('modulosInicioTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-horarios');
        if (!btn) return;
        currentModuloId = btn.dataset.moduloId;
        const nombre = btn.dataset.moduloNombre;
        const programa = btn.dataset.programa;

        document.getElementById('modalModuloTitle').textContent = nombre;
        document.getElementById('modalModuloPrograma').textContent = programa;
        document.getElementById('modalModuloHorariosBody').innerHTML = '';
        document.getElementById('modalDocenteInfo').innerHTML = '<div class="text-center text-muted py-2"><span class="spinner-border spinner-border-sm"></span></div>';
        document.getElementById('docenteResultadoBusqueda').innerHTML = '';
        document.getElementById('inputBuscarDocente').value = '';

        new bootstrap.Modal(document.getElementById('modalModuloHorarios')).show();

        fetch(ROUTE_MODULO_HORARIOS.replace('MID', currentModuloId))
            .then(r => r.json())
            .then(data => {
                if (data.error) { renderError(data.error); return; }
                renderHorarios(data.horarios);
                renderDocenteInfo(data.modulo);
            })
            .catch(function () { renderError('Error al cargar'); });
    });

    function renderHorarios(horarios) {
        const tbody = document.getElementById('modalModuloHorariosBody');
        document.getElementById('modalModuloCount').textContent = horarios.length;

        if (!horarios.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Sin horarios registrados</td></tr>';
            return;
        }

        const estadoColors = { 'Confirmado': '#0ab39c', 'Programado': '#299cdb', 'Desarrollado': '#405189', 'Postergado': '#f7b84b', 'Suspendido': '#f06548', 'Concluido': '#0ab39c' };

        const dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        tbody.innerHTML = horarios.map(function (h, i) {
            const fecha = new Date(h.fecha_desarrollo + 'T' + (h.hora_inicio || '00:00'));
            const fechaStr = dias[fecha.getDay()] + ' ' + fecha.getDate() + ' de ' + meses[fecha.getMonth()] + ' del ' + fecha.getFullYear();
            const color = estadoColors[h.estado] || '#878a99';
            const asig = h.asignado_nombres
                ? '<span style="display:inline-flex;align-items:center;gap:3px;"><span style="width:16px;height:16px;border-radius:50%;display:inline-block;background:' + (h.asignado_hex || '#6c757d') + ';font-size:.5rem;color:#fff;text-align:center;line-height:16px;">' + h.asignado_nombres.charAt(0) + '</span>' + h.asignado_apellidos + '</span>'
                : '<span class="text-muted">—</span>';
            return '<tr>' +
                '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                '<td class="fw-semibold">' + fechaStr + '</td>' +
                '<td>' + (h.hora_inicio || '—') + ' - ' + (h.hora_fin || '—') + '</td>' +
                '<td><span class="sesion-badge" style="color:' + color + ';background:' + color + '18;">' + (h.estado || '—') + '</span>' +
                    (h.tipo === 'Nivelacion' ? ' <span class="sesion-badge" style="background:#f7b84b;color:#fff;">Nivelación</span>' : '') +
                '</td>' +
                '<td>' + asig + '</td>' +
                '</tr>';
        }).join('');
    }

    function renderDocenteInfo(mod) {
        const container = document.getElementById('modalDocenteInfo');

        if (mod.docente_id) {
            const tieneUser = mod.docente_usuario && mod.docente_contrasena;
            container.innerHTML =
                '<div class="d-flex align-items-center gap-2 mb-2">' +
                    '<span class="avatar-mini" style="background:#405189;">' + (mod.docente_nombres ? mod.docente_nombres.charAt(0) : '') + '</span>' +
                    '<div class="flex-grow-1">' +
                        '<div class="fw-semibold fs-13">' + mod.docente_apellidos + ', ' + mod.docente_nombres + '</div>' +
                        '<small class="text-muted">CI: ' + (mod.docente_ci || '—') + '</small>' +
                    '</div>' +
                    '<div class="dropdown">' +
                        '<button class="btn btn-sm btn-soft-secondary px-1 py-0" data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>' +
                        '<ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item" href="#" id="docenteEditarAcceso"><i class="ri-lock-line me-1"></i>' + (tieneUser ? 'Editar' : 'Crear') + ' Acceso</a></li>' +
                            '<li><a class="dropdown-item text-danger" href="#" id="docenteQuitar"><i class="ri-close-circle-line me-1"></i>Quitar</a></li>' +
                        '</ul>' +
                    '</div>' +
                '</div>' +
                (tieneUser
                    ? '<div class="d-flex align-items-center gap-2 text-success fs-12"><i class="ri-checkbox-circle-fill"></i>Usuario: <b>' + mod.docente_usuario + '</b> · Contraseña: <b>' + mod.docente_contrasena + '</b></div>'
                    : '<div class="text-warning fs-12"><i class="ri-error-warning-line"></i> Sin usuario de acceso configurado</div>');

            document.getElementById('docenteEditarAcceso')?.addEventListener('click', function (e) {
                e.preventDefault();
                abrirModalUsuario(mod);
            });
            document.getElementById('docenteQuitar')?.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('¿Quitar docente de este módulo?')) {
                    fetch('{{ route("admin.programas.asignar-docente") }}', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ modulo_id: currentModuloId, docente_id: 0 })
                    }).then(function () { location.reload(); });
                }
            });
        } else {
            container.innerHTML = '<div class="text-center text-muted py-2"><i class="ri-user-unfollow-line fs-4 d-block mb-1"></i>Sin docente asignado</div>';
        }
    }

    function renderError(msg) {
        document.getElementById('modalModuloHorariosBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">' + msg + '</td></tr>';
        document.getElementById('modalDocenteInfo').innerHTML = '';
    }

    // ======= BUSCAR DOCENTE =======
    document.getElementById('btnBuscarDocente').addEventListener('click', function () {
        const ci = document.getElementById('inputBuscarDocente').value.trim();
        const resultDiv = document.getElementById('docenteResultadoBusqueda');
        if (!ci) { resultDiv.innerHTML = '<small class="text-danger">Ingrese un CI</small>'; return; }
        resultDiv.innerHTML = '<small class="text-muted"><span class="spinner-border spinner-border-sm"></span> Buscando...</small>';

        fetch('{{ route("admin.programas.docente-por-ci") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ ci: ci })
        })
        .then(r => r.json())
        .then(data => {
            if (data.docente) {
                const d = data.docente;
                resultDiv.innerHTML =
                    '<div class="d-flex align-items-center gap-2 mt-1 p-2 border rounded">' +
                        '<span class="avatar-mini" style="background:#405189;">' + (d.nombres ? d.nombres.charAt(0) : '') + '</span>' +
                        '<div class="flex-grow-1"><div class="fw-semibold fs-12">' + d.apellidos + ', ' + d.nombres + '</div><small class="text-muted">' + d.ci + '</small></div>' +
                        '<button class="btn btn-sm btn-success px-2 py-0" id="btnAsignarDocente" data-docente-id="' + d.id + '"><i class="ri-check-line"></i></button>' +
                    '</div>';
                document.getElementById('btnAsignarDocente')?.addEventListener('click', function () {
                    asignarDocente(this.dataset.docenteId);
                });
            } else {
                resultDiv.innerHTML =
                    '<div class="text-center mt-1 p-2 border rounded">' +
                        '<small class="text-muted">Docente no encontrado</small><br>' +
                        '<button class="btn btn-sm btn-primary mt-1" id="btnRegistrarNuevoDocente"><i class="ri-user-add-line"></i> Registrar</button>' +
                    '</div>';
                document.getElementById('btnRegistrarNuevoDocente')?.addEventListener('click', function () {
                    abrirRegistroDocente(ci);
                });
            }
        })
        .catch(function () { resultDiv.innerHTML = '<small class="text-danger">Error de conexión</small>'; });
    });

    function asignarDocente(docenteId) {
        fetch('{{ route("admin.programas.asignar-docente") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ modulo_id: currentModuloId, docente_id: docenteId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('docenteResultadoBusqueda').innerHTML = '<small class="text-success"><i class="ri-checkbox-circle-fill"></i> ' + (data.message || 'Asignado') + '</small>';
                setTimeout(function () { location.reload(); }, 600);
            } else {
                document.getElementById('docenteResultadoBusqueda').innerHTML = '<small class="text-danger">' + (data.message || 'Error') + '</small>';
            }
        })
        .catch(function () { document.getElementById('docenteResultadoBusqueda').innerHTML = '<small class="text-danger">Error de conexión</small>'; });
    }

    // ======= REGISTRAR DOCENTE =======
    function abrirRegistroDocente(ci) {
        document.getElementById('regDocCI').value = ci;
        document.getElementById('regDocNombres').value = '';
        document.getElementById('regDocApellidos').value = '';
        document.getElementById('regDocCorreo').value = '';
        document.getElementById('regDocCelular').value = '';
        new bootstrap.Modal(document.getElementById('modalRegistrarDocente')).show();
    }

    document.getElementById('btnGuardarDocente').addEventListener('click', function () {
        const data = {
            ci: document.getElementById('regDocCI').value,
            nombres: document.getElementById('regDocNombres').value,
            apellidos: document.getElementById('regDocApellidos').value,
            correo: document.getElementById('regDocCorreo').value,
            celular: document.getElementById('regDocCelular').value,
        };
        if (!data.nombres || !data.apellidos) { alert('Nombres y apellidos son requeridos'); return; }

        const btn = this;
        btn.disabled = true;

        fetch('{{ route("admin.programas.docente-registrar") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalRegistrarDocente')).hide();
                document.getElementById('docenteResultadoBusqueda').innerHTML =
                    '<div class="d-flex align-items-center gap-2 mt-1 p-2 border rounded">' +
                        '<span class="avatar-mini" style="background:#405189;">' + (data.docente.nombres ? data.docente.nombres.charAt(0) : '') + '</span>' +
                        '<div class="flex-grow-1"><div class="fw-semibold fs-12">' + data.docente.apellidos + ', ' + data.docente.nombres + '</div><small class="text-muted">' + data.docente.ci + '</small></div>' +
                        '<button class="btn btn-sm btn-success px-2 py-0" id="btnAsignarDocente2" data-docente-id="' + data.docente.id + '"><i class="ri-check-line"></i></button>' +
                    '</div>';
                document.getElementById('btnAsignarDocente2')?.addEventListener('click', function () {
                    asignarDocente(this.dataset.docenteId);
                });
            } else {
                alert(data.message || 'Error al registrar');
            }
        })
        .catch(function () { alert('Error de conexión'); })
        .finally(function () { btn.disabled = false; });
    });

    // ======= USUARIO DOCENTE =======
    let currentDocenteId = null;

    function abrirModalUsuario(mod) {
        currentDocenteId = mod.docente_id;
        document.getElementById('docUserUsuario').value = mod.docente_usuario || '';
        document.getElementById('docUserContrasena').value = mod.docente_contrasena || '';
        new bootstrap.Modal(document.getElementById('modalDocenteUsuario')).show();
    }

    document.getElementById('btnGuardarDocenteUsuario').addEventListener('click', function () {
        const usuario = document.getElementById('docUserUsuario').value.trim();
        const contrasena = document.getElementById('docUserContrasena').value.trim();
        if (!usuario || !contrasena) { alert('Usuario y contraseña son requeridos'); return; }

        const btn = this;
        btn.disabled = true;

        fetch('{{ route("admin.programas.docente-update-credenciales") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ docente_id: currentDocenteId, usuario: usuario, contrasena: contrasena })
        })
        .then(function () {
            bootstrap.Modal.getInstance(document.getElementById('modalDocenteUsuario')).hide();
            location.reload();
        })
        .catch(function () { alert('Error al guardar'); })
        .finally(function () { btn.disabled = false; });
    });

    // ======= ENVIAR CREDENCIALES =======
    document.getElementById('modulosInicioTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-enviar-credenciales');
        if (!btn) return;

        const nombres = btn.dataset.nombres;
        const apellidos = btn.dataset.apellidos;
        const usuario = btn.dataset.usuario;
        const contrasena = btn.dataset.contrasena;
        const celular = btn.dataset.celular;
        const programa = btn.dataset.programa;
        const modulo = btn.dataset.modulo;
        const moodleLink = btn.dataset.moodleLink;

        document.getElementById('credDocenteNombre').textContent = apellidos + ', ' + nombres;
        document.getElementById('credDocenteUsuario').textContent = usuario;
        document.getElementById('credDocenteContrasena').textContent = contrasena;

        const mensaje = encodeURIComponent(
            'Estimado/a ' + apellidos + ', ' + nombres + ',\n\n' +
            'Se le asignó al programa: ' + programa + '\n' +
            'Módulo: ' + modulo + (moodleLink ? '\nEnlace Moodle: ' + moodleLink : '') + '\n\n' +
            'Sus credenciales de acceso al sistema:\n' +
            'Usuario: ' + usuario + '\n' +
            'Contraseña: ' + contrasena + '\n\n' +
            'Área Académica ESAM Sucre Central\n' +
            'Enviado por: ' + ADMIN_NOMBRE
        );
        const waLink = celular ? 'https://wa.me/' + celular + '?text=' + mensaje : '#';
        document.getElementById('btnEnviarWhatsApp').href = waLink;
        document.getElementById('btnEnviarWhatsApp').style.opacity = celular ? '1' : '.4';

        new bootstrap.Modal(document.getElementById('modalEnviarCredenciales')).show();
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-copy-credencial');
        if (!btn) return;
        const targetId = btn.dataset.copy;
        const text = document.getElementById(targetId).textContent;
        navigator.clipboard.writeText(text).then(function () {
            const icon = btn.querySelector('i');
            icon.className = 'ri-check-line';
            setTimeout(function () { icon.className = 'ri-file-copy-line'; }, 1500);
        });
    });

    // ======= MOODLE LINKS =======
    const ROUTE_PROGRAMA_MOODLE = '{{ route("admin.programas.programa-moodle-link", ["programa" => "PID"]) }}';
    const ROUTE_MODULO_MOODLE = '{{ route("admin.programas.modulo-moodle-link", ["modulo" => "MID"]) }}';

    // Programa moodle link - click handler
    document.getElementById('modulosInicioTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-moodle-programa');
        if (!btn) return;
        document.getElementById('edit-programa-moodle-id').value = btn.dataset.programaId;
        document.getElementById('edit-programa-moodle-link').value = btn.dataset.moodleLink || '';
        new bootstrap.Modal(document.getElementById('modalProgramaMoodle')).show();
    });

    document.getElementById('formProgramaMoodle').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarProgramaMoodle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        const formData = new FormData(this);
        const programaId = document.getElementById('edit-programa-moodle-id').value;
        fetch(ROUTE_PROGRAMA_MOODLE.replace('PID', programaId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalProgramaMoodle')).hide();
                mostrarToast('success', data.message || 'Enlace actualizado');
                actualizarSemana();
            } else {
                mostrarToast('error', data.message || 'Error al guardar');
            }
        })
        .catch(function () { mostrarToast('error', 'Error de conexión'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar'; });
    });

    // Módulo moodle link - click handler
    document.getElementById('modulosInicioTable').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-moodle-modulo');
        if (!btn) return;
        document.getElementById('edit-modulo-moodle-id').value = btn.dataset.moduloId;
        document.getElementById('edit-modulo-moodle-link').value = btn.dataset.moodleLink || '';
        new bootstrap.Modal(document.getElementById('modalModuloMoodle')).show();
    });

    document.getElementById('formModuloMoodle').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarModuloMoodle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        const formData = new FormData(this);
        const moduloId = document.getElementById('edit-modulo-moodle-id').value;
        fetch(ROUTE_MODULO_MOODLE.replace('MID', moduloId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalModuloMoodle')).hide();
                mostrarToast('success', data.message || 'Enlace actualizado');
                actualizarSemana();
            } else {
                mostrarToast('error', data.message || 'Error al guardar');
            }
        })
        .catch(function () { mostrarToast('error', 'Error de conexión'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar'; });
    });

    function mostrarToast(type, message) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;
        const bg = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line';
        const html = '<div class="toast align-items-center text-white ' + bg + ' border-0 show" role="alert">' +
            '<div class="d-flex">' +
                '<div class="toast-body d-flex align-items-center gap-2"><i class="' + icon + '"></i>' + message + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>' +
        '</div>';
        toastContainer.insertAdjacentHTML('beforeend', html);
        setTimeout(function () {
            const t = toastContainer.querySelector('.toast');
            if (t) t.remove();
        }, 4000);
    }
});
</script>
@endpush
