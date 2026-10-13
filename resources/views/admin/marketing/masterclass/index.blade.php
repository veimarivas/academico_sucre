@extends('admin.layouts.master')

@section('title', 'Masterclass - Marketing - ' . config('app.name'))

@push('css')
<style>
.mkt-hero {
    position: relative;
    background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 50%, #d63384 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(106,27,154,.4);
    margin-bottom: 1.25rem;
}
.mkt-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero > * { position: relative; z-index: 1; }
.mkt-hero .hero-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px); }
.mkt-hero h4 { color: #fff !important; font-weight: 700; letter-spacing: .2px; margin: 0; }
.mkt-hero .hero-sub { color: rgba(255,255,255,.85) !important; font-size: .88rem; margin: 0; }
.mkt-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.mkt-hero .breadcrumb-item, .mkt-hero .breadcrumb-item a { color: rgba(255,255,255,.85) !important; }
.mkt-hero .breadcrumb-item.active { color: #fff !important; font-weight: 600; }
.mkt-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55) !important; }
.mkt-hero .btn-hero { background: #fff; border: 1px solid #fff; color: #6a1b9a; font-weight: 700; box-shadow: 0 6px 16px -6px rgba(0,0,0,.35); white-space: nowrap; flex-shrink: 0; }
.mkt-hero .btn-hero:hover { background: #f3e8f9; border-color: #f3e8f9; color: #6a1b9a; transform: translateY(-1px); }

.mkt-calendar-card, .mkt-sidebar-card { border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .mkt-calendar-card, [data-layout-mode="dark"] .mkt-sidebar-card { border-color: #2a2f34; }

.filter-chips { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; margin-bottom: 1rem; }
.chip-btn { --chip-bg: #f3f5f9; --chip-color: #495057; background: var(--chip-bg); color: var(--chip-color); font-size: .78rem; border-radius: 999px; padding: .35rem 1rem; cursor: pointer; border: 1px solid transparent; font-weight: 600; transition: all .15s ease; }
.chip-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 14px -6px rgba(0,0,0,.2); }
.chip-btn.active { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--chip-bg); }
[data-layout-mode="dark"] .chip-btn { background: #212529; color: #ced4da; }

#mkt-calendar { max-width: 100%; }
.fc-event { cursor: pointer; border-radius: 6px; border: 0; padding: 2px 6px; font-weight: 500; }
.fc .fc-toolbar.fc-header-toolbar { margin-bottom: 1.25rem; flex-wrap: wrap; gap: .5rem; }
.fc .fc-toolbar-title { font-size: 1.15rem !important; font-weight: 700; color: #212529; text-transform: capitalize; }
[data-layout-mode="dark"] .fc .fc-toolbar-title { color: #fff; }
.fc .fc-button { background: #fff !important; border: 1px solid #e9ebec !important; color: #495057 !important; border-radius: 8px !important; padding: .4rem .85rem !important; font-size: .82rem !important; font-weight: 600 !important; box-shadow: none !important; transition: all .15s ease; }
.fc .fc-button:hover { background: #f8f9fc !important; border-color: #6a1b9a !important; color: #6a1b9a !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 100%) !important; border-color: transparent !important; color: #fff !important; box-shadow: 0 4px 10px -4px rgba(106,27,154,.5) !important; }
.fc .fc-col-header-cell { background: #f8f9fc; text-transform: uppercase; font-size: .72rem; font-weight: 700; letter-spacing: .4px; padding: .65rem 0; color: #6c757d; border-color: #eef0f4 !important; }
[data-layout-mode="dark"] .fc .fc-col-header-cell { background: #1a1d20; border-color: #2a2f34 !important; color: #adb5bd; }
.fc .fc-daygrid-day { border-color: #eef0f4 !important; }
.fc .fc-daygrid-day:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .fc .fc-daygrid-day { border-color: #2a2f34 !important; }
.fc .fc-day-today { background-color: rgba(106,27,154,.06) !important; }
.fc-day-today .fc-daygrid-day-number { background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 100%); color: #fff; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; margin: .2rem; }

.programa-item { border: 1px solid #eef0f4; border-radius: 12px; padding: .75rem .9rem; margin-bottom: .65rem; transition: all .15s ease; }
.programa-item:hover { border-color: #d8b4e2; box-shadow: 0 4px 14px -8px rgba(106,27,154,.3); }
[data-layout-mode="dark"] .programa-item { border-color: #2a2f34; }
.programa-item .p-nombre { font-size: .84rem; font-weight: 700; color: #212529; line-height: 1.3; }
[data-layout-mode="dark"] .programa-item .p-nombre { color: #fff; }
.programa-item .p-meta { font-size: .7rem; color: #878a99; }
#buscarPrograma { border-radius: 999px; }
.btn-soft-purple { background: rgba(106,27,154,.1); color: #6a1b9a; border: none; }
.btn-soft-purple:hover { background: rgba(106,27,154,.2); color: #6a1b9a; }
[data-layout-mode="dark"] .btn-soft-purple { background: rgba(106,27,154,.2); color: #ce93d8; }
[data-layout-mode="dark"] .btn-soft-purple:hover { background: rgba(106,27,154,.35); }

.detail-section { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: .85rem; }
.detail-section:last-child { margin-bottom: 0; }
[data-layout-mode="dark"] .detail-section { background: #212529; border-color: #2a2f34; }
.detail-row { display: flex; gap: .75rem; padding: .35rem 0; align-items: flex-start; }
.detail-row + .detail-row { border-top: 1px solid #f1f3f5; }
[data-layout-mode="dark"] .detail-row + .detail-row { border-top-color: #2a2f34; }
.detail-label { flex: 0 0 140px; font-size: .72rem; text-transform: uppercase; letter-spacing: .3px; font-weight: 600; color: #878a99; padding-top: 2px; }
.detail-value { flex: 1; font-size: .88rem; font-weight: 500; color: #212529; min-width: 0; word-break: break-word; }
[data-layout-mode="dark"] .detail-value { color: #fff; }
.estado-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; padding: .25em .75em; border-radius: 999px; border: 1px solid currentColor; }
.estado-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

#modalMasterclass .modal-content, #modalMasterclassDetalle .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(0,0,0,.25); }
#modalMasterclass .modal-header, #modalMasterclassDetalle .modal-header { background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 60%, #d63384 100%); color: #fff; border: 0; padding: 1.1rem 1.5rem; }
#modalMasterclass .modal-title, #modalMasterclassDetalle .modal-title { color: #fff !important; font-weight: 700; }
#modalMasterclass .modal-header .btn-close, #modalMasterclassDetalle .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalMasterclass .modal-body, #modalMasterclassDetalle .modal-body { padding: 1.25rem 1.4rem; background: #fafbfd; }
[data-layout-mode="dark"] #modalMasterclass .modal-body, [data-layout-mode="dark"] #modalMasterclassDetalle .modal-body { background: #1a1d20; }
.modal-header-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3); border-radius: 12px; font-size: 1.25rem; color: #fff; }
.modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }

/* El form envuelve body + footer: debe integrarse al flex del modal scrollable
   para que el footer (botón Registrar/Guardar) siempre quede visible */
#modalMasterclass form { display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
#modalMasterclass .modal-body { overflow-y: auto; }
#modalMasterclass .modal-footer, #modalMasterclassDetalle .modal-footer { background: #fff; border-top: 1px solid #eef0f4; }
[data-layout-mode="dark"] #modalMasterclass .modal-footer, [data-layout-mode="dark"] #modalMasterclassDetalle .modal-footer { background: #212529; border-top-color: #2a2f34; }

/* Secciones del formulario */
.mc-section { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: 1rem 1.1rem; margin-bottom: .85rem; }
.mc-section:last-child { margin-bottom: 0; }
[data-layout-mode="dark"] .mc-section { background: #212529; border-color: #2a2f34; }
.mc-section-title {
    font-size: .7rem; font-weight: 700; color: #878a99;
    text-transform: uppercase; letter-spacing: .4px; margin-bottom: .75rem;
    display: flex; align-items: center; gap: .4rem;
}
.mc-section-title i { color: #8e24aa; font-size: .9rem; }
.mc-required { color: #f06548; font-weight: 700; }
.btn-mc-primary {
    background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 100%);
    color: #fff; border: 0; font-weight: 600;
}
.btn-mc-primary:hover { color: #fff; filter: brightness(1.08); }
.btn-mc-primary:disabled { color: #fff; opacity: .7; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="mkt-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-presentation-line"></i></div>
                <div>
                    <h4>Masterclass</h4>
                    <p class="hero-sub">Programa y visualiza las masterclass de los programas en inscripción</p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-start align-items-md-end gap-2">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                    <li class="breadcrumb-item">Marketing</li>
                    <li class="breadcrumb-item active">Masterclass</li>
                </ol>
                <button type="button" class="btn btn-hero" id="btnNuevaMasterclass">
                    <i class="ri-add-line align-bottom me-1"></i> Nueva Masterclass
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-12 mb-3 mb-xl-0">
        <div class="card mkt-calendar-card">
            <div class="card-body">
                <div class="filter-chips">
                    <button class="chip-btn active" data-estado="" style="--chip-bg:#f8f9fc;--chip-color:#495057;">Todos</button>
                    <button class="chip-btn" data-estado="programado" style="--chip-bg:#e7f4fb;--chip-color:#299cdb;">Programado</button>
                    <button class="chip-btn" data-estado="en_curso" style="--chip-bg:#fef6e7;--chip-color:#f7b84b;">En curso</button>
                    <button class="chip-btn" data-estado="finalizado" style="--chip-bg:#e6f7f4;--chip-color:#0ab39c;">Finalizado</button>
                    <button class="chip-btn" data-estado="cancelado" style="--chip-bg:#fdeae7;--chip-color:#f06548;">Cancelado</button>
                </div>
                <div id="mkt-calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-12">
        <div class="card mkt-sidebar-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold mb-0 fs-14"><i class="ri-booklet-line me-1 text-primary"></i> Programas en Inscripciones</h6>
                    <span class="badge bg-soft-secondary text-secondary">{{ count($programasInscripcion) }}</span>
                </div>
                <p class="text-muted fs-12 mb-2">Selecciona un programa para registrar su masterclass</p>
                <input type="text" id="buscarPrograma" class="form-control form-control-sm mb-3" placeholder="Buscar programa...">
                <div id="listaProgramas" style="max-height: 560px; overflow-y: auto;">
                    @forelse($programasInscripcion as $p)
                        <div class="programa-item" data-nombre="{{ strtolower($p->nombre) }}">
                            <div class="p-nombre">{{ $p->nombre }}</div>
                            <div class="p-meta mb-2">{{ $p->codigo }} · {{ $p->tipo_nombre ?? 'Sin tipo' }} · Gestión {{ $p->gestion }}</div>
                            <button type="button" class="btn btn-sm btn-soft-purple w-100 btn-registrar-programa"
                                    data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}">
                                <i class="ri-calendar-event-line align-bottom me-1"></i> Registrar Masterclass
                            </button>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="ri-inbox-2-line fs-3 mb-2 d-block"></i>
                            <span>No hay programas en estado de Inscripciones</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Registrar / Editar Masterclass --}}
<div class="modal fade" id="modalMasterclass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-presentation-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalMasterclassTitle">Registrar Masterclass</h5>
                        <small class="modal-subtitle" id="modalMasterclassPrograma">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMasterclass">
                @csrf
                <input type="hidden" id="mc-id" value="">
                <input type="hidden" id="mc-programa-id" name="programa_id" value="">
                <div class="modal-body">
                    <div class="mc-section">
                        <div class="mc-section-title"><i class="ri-booklet-line"></i> Información del evento</div>
                        <div class="mb-3">
                            <label class="form-label fs-12 text-muted fw-semibold">Programa <span class="mc-required">*</span></label>
                            <select class="form-select" id="mc-programa-select">
                                <option value="">Selecciona un programa...</option>
                                @foreach($programasInscripcion as $p)
                                    <option value="{{ $p->id }}" data-nombre="{{ $p->nombre }}">{{ $p->nombre }} ({{ $p->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fs-12 text-muted fw-semibold">Nombre del evento <span class="mc-required">*</span></label>
                            <input type="text" class="form-control" id="mc-nombre-evento" name="nombre_evento" maxlength="200" placeholder="Ej: Masterclass Diplomado en...">
                        </div>
                    </div>

                    <div class="mc-section">
                        <div class="mc-section-title"><i class="ri-calendar-event-line"></i> Fecha y modalidad</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-12 text-muted fw-semibold">Fecha y hora de inicio <span class="mc-required">*</span></label>
                                <input type="datetime-local" class="form-control" id="mc-fecha-inicio" name="fecha_inicio">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-12 text-muted fw-semibold">Fecha y hora de fin</label>
                                <input type="datetime-local" class="form-control" id="mc-fecha-fin" name="fecha_fin">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fs-12 text-muted fw-semibold d-block">Modalidad</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="modalidad" id="mc-modalidad-virtual" value="virtual" checked>
                                    <label class="btn btn-outline-secondary btn-sm" for="mc-modalidad-virtual"><i class="ri-vidicon-line me-1"></i>Virtual</label>
                                    <input type="radio" class="btn-check" name="modalidad" id="mc-modalidad-presencial" value="presencial">
                                    <label class="btn btn-outline-secondary btn-sm" for="mc-modalidad-presencial"><i class="ri-map-pin-line me-1"></i>Presencial</label>
                                </div>
                            </div>
                            <div class="col-md-7" id="mc-grupo-virtual">
                                <label class="form-label fs-12 text-muted fw-semibold">Enlace virtual</label>
                                <input type="url" class="form-control" id="mc-enlace-virtual" name="enlace_virtual" maxlength="500" placeholder="https://meet.google.com/...">
                            </div>
                            <div class="col-md-7 d-none" id="mc-grupo-presencial">
                                <label class="form-label fs-12 text-muted fw-semibold">Ubicación</label>
                                <input type="text" class="form-control" id="mc-ubicacion" name="ubicacion" maxlength="300" placeholder="Dirección o sede">
                            </div>
                        </div>
                    </div>

                    <div class="mc-section">
                        <div class="mc-section-title"><i class="ri-money-dollar-circle-line"></i> Inscripción</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-12 text-muted fw-semibold">Costo (Bs.)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="mc-costo" name="costo_inscripcion" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 text-muted fw-semibold">Capacidad máxima</label>
                                <input type="number" min="1" class="form-control" id="mc-capacidad" name="capacidad_maxima" placeholder="Sin límite">
                            </div>
                            <div class="col-md-4 d-none" id="mc-grupo-estado">
                                <label class="form-label fs-12 text-muted fw-semibold">Estado</label>
                                <select class="form-select" id="mc-estado" name="estado">
                                    <option value="programado">Programado</option>
                                    <option value="en_curso">En curso</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-mc-primary" id="btnGuardarMasterclass">
                        <i class="ri-save-3-line me-1"></i> <span id="btnGuardarMasterclassLbl">Registrar Masterclass</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Detalle Masterclass --}}
<div class="modal fade" id="modalMasterclassDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-presentation-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Detalle de la Masterclass</h5>
                        <small class="modal-subtitle">Información completa del evento</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalMasterclassDetalleBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" id="btnEliminarMasterclass"><i class="ri-delete-bin-line align-bottom me-1"></i>Eliminar</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm" style="background:linear-gradient(135deg,#6a1b9a 0%,#8e24aa 100%);color:#fff;" id="btnEditarMasterclass">
                    <i class="ri-edit-line align-bottom me-1"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script src="{{ asset('backend/assets/libs/fullcalendar/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';
    const urlEvents = '{{ route("admin.marketing.masterclass.events") }}';
    const urlStore = '{{ route("admin.marketing.masterclass.store") }}';
    const urlUpdateBase = '{{ route("admin.marketing.masterclass.update", ["id" => "MCID"]) }}';
    const urlDestroyBase = '{{ route("admin.marketing.masterclass.destroy", ["id" => "MCID"]) }}';

    let selectedEstado = '';
    let currentEventId = null;

    const estadoLabels = { programado: 'Programado', en_curso: 'En curso', finalizado: 'Finalizado', cancelado: 'Cancelado' };
    const estadoColors = { programado: '#299cdb', en_curso: '#f7b84b', finalizado: '#0ab39c', cancelado: '#f06548' };

    // ======= FILTROS =======
    document.querySelectorAll('.chip-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.chip-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedEstado = this.dataset.estado;
            calendar.refetchEvents();
        });
    });

    // ======= CALENDARIO =======
    const calendarEl = document.getElementById('mkt-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        buttonText: { today: 'Hoy' },
        events: {
            url: urlEvents,
            extraParams: function () { return {}; }
        },
        eventDidMount: function (info) {
            const p = info.event.extendedProps;
            info.el.title = p.nombre_evento + (p.programa ? ' — ' + p.programa : '');
        },
        eventSourceSuccess: function (content) {
            if (!selectedEstado) return content;
            return content.filter(e => e.extendedProps.estado === selectedEstado);
        },
        loading: function (loading) { calendarEl.style.opacity = loading ? '0.5' : '1'; },
        eventClick: function (info) { abrirDetalle(info.event); }
    });
    calendar.render();

    // ======= BUSCADOR DE PROGRAMAS =======
    document.getElementById('buscarPrograma').addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        document.querySelectorAll('#listaProgramas .programa-item').forEach(item => {
            item.style.display = item.dataset.nombre.includes(term) ? '' : 'none';
        });
    });

    // ======= MODALIDAD TOGGLE =======
    function toggleModalidad() {
        const virtual = document.getElementById('mc-modalidad-virtual').checked;
        document.getElementById('mc-grupo-virtual').classList.toggle('d-none', !virtual);
        document.getElementById('mc-grupo-presencial').classList.toggle('d-none', virtual);
    }
    document.getElementById('mc-modalidad-virtual').addEventListener('change', toggleModalidad);
    document.getElementById('mc-modalidad-presencial').addEventListener('change', toggleModalidad);

    // ======= FECHAS: inicio por defecto 19:30, fin automático +1h (editable) =======
    const pad2 = (n) => String(n).padStart(2, '0');
    const toInputVal = (d) => d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()) + 'T' + pad2(d.getHours()) + ':' + pad2(d.getMinutes());

    function syncFechaFin() {
        const iniEl = document.getElementById('mc-fecha-inicio');
        const finEl = document.getElementById('mc-fecha-fin');
        if (!iniEl.value || finEl.dataset.editado) return;
        const d = new Date(iniEl.value);
        if (isNaN(d.getTime())) return;
        d.setMinutes(d.getMinutes() + 60);
        finEl.value = toInputVal(d);
    }

    document.getElementById('mc-fecha-inicio').addEventListener('change', function () {
        // Si sólo se eligió la fecha (hora 00:00), aplicar la hora habitual 19:30
        if (this.value && this.value.slice(11, 16) === '00:00') {
            this.value = this.value.slice(0, 10) + 'T19:30';
        }
        syncFechaFin();
    });
    document.getElementById('mc-fecha-fin').addEventListener('change', function () {
        // El usuario ajustó manualmente el fin: dejar de sincronizarlo
        if (this.value) this.dataset.editado = '1';
        else delete this.dataset.editado;
    });

    // ======= ABRIR MODAL: NUEVA (botón hero) =======
    document.getElementById('btnNuevaMasterclass').addEventListener('click', function () {
        abrirFormulario();
    });

    // ======= ABRIR MODAL: DESDE LISTA DE PROGRAMAS =======
    document.getElementById('listaProgramas').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-registrar-programa');
        if (!btn) return;
        abrirFormulario({ id: btn.dataset.id, nombre: btn.dataset.nombre });
    });

    // ======= CAMBIO DE PROGRAMA EN EL SELECT =======
    document.getElementById('mc-programa-select').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        document.getElementById('mc-programa-id').value = this.value;
        document.getElementById('modalMasterclassPrograma').textContent = this.value ? opt.dataset.nombre : 'Selecciona un programa';
        if (!document.getElementById('mc-nombre-evento').dataset.editado && this.value) {
            document.getElementById('mc-nombre-evento').value = 'Masterclass: ' + opt.dataset.nombre;
        }
    });
    document.getElementById('mc-nombre-evento').addEventListener('input', function () {
        this.dataset.editado = '1';
    });

    function abrirFormulario(programa) {
        currentEventId = null;
        document.getElementById('formMasterclass').reset();
        document.getElementById('mc-nombre-evento').removeAttribute('data-editado');
        document.getElementById('mc-id').value = '';
        document.getElementById('mc-grupo-estado').classList.add('d-none');
        document.getElementById('modalMasterclassTitle').textContent = 'Registrar Masterclass';
        document.getElementById('btnGuardarMasterclassLbl').textContent = 'Registrar Masterclass';
        document.getElementById('mc-modalidad-virtual').checked = true;
        toggleModalidad();

        // Fechas por defecto: hoy 19:30 → 20:30 (editables)
        delete document.getElementById('mc-fecha-fin').dataset.editado;
        const iniDefecto = new Date();
        iniDefecto.setHours(19, 30, 0, 0);
        document.getElementById('mc-fecha-inicio').value = toInputVal(iniDefecto);
        const finDefecto = new Date(iniDefecto);
        finDefecto.setMinutes(finDefecto.getMinutes() + 60);
        document.getElementById('mc-fecha-fin').value = toInputVal(finDefecto);

        const select = document.getElementById('mc-programa-select');
        if (programa) {
            select.value = programa.id;
            document.getElementById('mc-programa-id').value = programa.id;
            document.getElementById('mc-nombre-evento').value = 'Masterclass: ' + programa.nombre;
            document.getElementById('modalMasterclassPrograma').textContent = programa.nombre;
        } else {
            select.value = '';
            document.getElementById('mc-programa-id').value = '';
            document.getElementById('modalMasterclassPrograma').textContent = 'Selecciona un programa';
        }

        new bootstrap.Modal(document.getElementById('modalMasterclass')).show();
    }

    // ======= GUARDAR (CREATE / UPDATE) =======
    document.getElementById('formMasterclass').addEventListener('submit', function (e) {
        e.preventDefault();

        const programaId = document.getElementById('mc-programa-id').value;
        if (!programaId) { mostrarToast('error', 'Selecciona un programa'); return; }

        const modalidad = document.querySelector('input[name="modalidad"]:checked').value;
        const payload = {
            programa_id: programaId,
            nombre_evento: document.getElementById('mc-nombre-evento').value,
            fecha_inicio: document.getElementById('mc-fecha-inicio').value,
            fecha_fin: document.getElementById('mc-fecha-fin').value || null,
            modalidad: modalidad,
            ubicacion: document.getElementById('mc-ubicacion').value || null,
            enlace_virtual: document.getElementById('mc-enlace-virtual').value || null,
            costo_inscripcion: document.getElementById('mc-costo').value || 0,
            capacidad_maxima: document.getElementById('mc-capacidad').value || null,
        };

        const id = document.getElementById('mc-id').value;
        const isEdit = !!id;
        if (isEdit) {
            payload.estado = document.getElementById('mc-estado').value;
        }

        const btn = document.getElementById('btnGuardarMasterclass');
        const lblEl = document.getElementById('btnGuardarMasterclassLbl');
        const lblTxt = lblEl ? lblEl.textContent : 'Guardar';
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        fetch(isEdit ? urlUpdateBase.replace('MCID', id) : urlStore, {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalMasterclass')).hide();
                mostrarToast('success', data.message);
                calendar.refetchEvents();
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Error al guardar');
                mostrarToast('error', msg);
            }
        })
        .catch(function () { mostrarToast('error', 'Error de conexión'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="ri-save-3-line me-1"></i> <span id="btnGuardarMasterclassLbl">' + lblTxt + '</span>'; });
    });

    // ======= DETALLE =======
    function abrirDetalle(event) {
        currentEventId = event.id;
        const p = event.extendedProps;
        const color = estadoColors[p.estado] || '#878a99';
        const modalidad = p.enlace_virtual ? 'Virtual' : 'Presencial';

        const fInicio = p.fecha_inicio ? new Date(p.fecha_inicio.replace(' ', 'T')) : null;
        const fFin = p.fecha_fin ? new Date(p.fecha_fin.replace(' ', 'T')) : null;
        const fmt = (d) => d ? d.toLocaleString('es-BO', { dateStyle: 'full', timeStyle: 'short' }) : '—';

        const html =
            '<div class="detail-section">' +
                '<div class="detail-row"><div class="detail-label">Evento</div><div class="detail-value fw-semibold">' + (p.nombre_evento || '—') + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Programa</div><div class="detail-value">' + (p.programa || '—') + (p.programa_codigo ? ' <span class="badge bg-soft-dark text-dark">' + p.programa_codigo + '</span>' : '') + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Estado</div><div class="detail-value"><span class="estado-pill" style="color:' + color + ';">' + (estadoLabels[p.estado] || p.estado) + '</span></div></div>' +
            '</div>' +
            '<div class="detail-section">' +
                '<div class="detail-row"><div class="detail-label">Inicio</div><div class="detail-value">' + fmt(fInicio) + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Fin</div><div class="detail-value">' + fmt(fFin) + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Modalidad</div><div class="detail-value">' + modalidad + '</div></div>' +
                (p.enlace_virtual ? '<div class="detail-row"><div class="detail-label">Enlace</div><div class="detail-value"><a href="' + p.enlace_virtual + '" target="_blank" rel="noopener">' + p.enlace_virtual + '</a></div></div>' : '') +
                (p.ubicacion ? '<div class="detail-row"><div class="detail-label">Ubicación</div><div class="detail-value">' + p.ubicacion + '</div></div>' : '') +
            '</div>' +
            '<div class="detail-section">' +
                '<div class="detail-row"><div class="detail-label">Costo</div><div class="detail-value">Bs. ' + (parseFloat(p.costo_inscripcion || 0)).toFixed(2) + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Capacidad</div><div class="detail-value">' + (p.capacidad_maxima || 'Sin límite') + '</div></div>' +
                '<div class="detail-row"><div class="detail-label">Registrados</div><div class="detail-value">' + (p.asistentes_registrados || 0) + '</div></div>' +
            '</div>';

        document.getElementById('modalMasterclassDetalleBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalMasterclassDetalle')).show();
    }

    // ======= EDITAR (desde el detalle) =======
    document.getElementById('btnEditarMasterclass').addEventListener('click', function () {
        if (!currentEventId) return;
        const event = calendar.getEventById(currentEventId);
        if (!event) return;
        const p = event.extendedProps;

        bootstrap.Modal.getInstance(document.getElementById('modalMasterclassDetalle')).hide();

        document.getElementById('formMasterclass').reset();
        document.getElementById('mc-id').value = event.id;
        document.getElementById('mc-programa-id').value = p.programa_id || '';
        document.getElementById('mc-programa-select').value = p.programa_id || '';
        document.getElementById('mc-nombre-evento').value = p.nombre_evento || '';
        document.getElementById('mc-nombre-evento').dataset.editado = '1';
        document.getElementById('modalMasterclassPrograma').textContent = p.programa || '—';
        document.getElementById('modalMasterclassTitle').textContent = 'Editar Masterclass';
        document.getElementById('btnGuardarMasterclassLbl').textContent = 'Guardar cambios';

        const toLocalInput = (val) => {
            if (!val) return '';
            const d = new Date(val.replace(' ', 'T'));
            const pad = (n) => String(n).padStart(2, '0');
            return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        };
        document.getElementById('mc-fecha-inicio').value = toLocalInput(p.fecha_inicio);
        document.getElementById('mc-fecha-fin').value = toLocalInput(p.fecha_fin);
        // Respetar la hora fin guardada: no sincronizar automáticamente al editar
        if (p.fecha_fin) document.getElementById('mc-fecha-fin').dataset.editado = '1';
        else delete document.getElementById('mc-fecha-fin').dataset.editado;

        if (p.enlace_virtual) {
            document.getElementById('mc-modalidad-virtual').checked = true;
            document.getElementById('mc-enlace-virtual').value = p.enlace_virtual;
        } else {
            document.getElementById('mc-modalidad-presencial').checked = true;
            document.getElementById('mc-ubicacion').value = p.ubicacion || '';
        }
        toggleModalidad();

        document.getElementById('mc-costo').value = p.costo_inscripcion || 0;
        document.getElementById('mc-capacidad').value = p.capacidad_maxima || '';
        document.getElementById('mc-grupo-estado').classList.remove('d-none');
        document.getElementById('mc-estado').value = p.estado || 'programado';

        new bootstrap.Modal(document.getElementById('modalMasterclass')).show();
    });

    // ======= ELIMINAR =======
    document.getElementById('btnEliminarMasterclass').addEventListener('click', function () {
        if (!currentEventId) return;
        if (!confirm('¿Estás seguro de eliminar esta masterclass? Esta acción no se puede deshacer.')) return;

        fetch(urlDestroyBase.replace('MCID', currentEventId), {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalMasterclassDetalle')).hide();
                mostrarToast('success', data.message);
                calendar.refetchEvents();
            } else {
                mostrarToast('error', data.message || 'Error al eliminar');
            }
        })
        .catch(function () { mostrarToast('error', 'Error de conexión'); });
    });

    // ======= TOAST =======
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
