@extends('admin.layouts.master')

@section('title', 'Prospectos y WhatsApp - ' . config('app.name'))

@php
    $yo = auth()->id();
    $enInscripciones = $programas->where('estado_id', $inscripcionesId)->values();
    $conCelular = $prospectos->filter(fn ($p) => strlen(preg_replace('/\D+/', '', (string) $p->celular)) >= 8)->count();
    $puedeEnviar = ! $error && ! $lineaError;
    $data = $prospectos->map(fn ($p) => [
        'id' => (int) $p->id,
        'nombre' => trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) ?: 'Sin nombre',
        'nombres' => $p->nombres,
        'apellidos' => $p->apellidos,
        'celular' => $p->celular,
        'correo' => $p->correo,
        'nota' => $p->nota,
        'estado' => $p->estado,
        'programa_id' => $p->programa_id ? (int) $p->programa_id : null,
        'programa' => $p->programa_nombre,
        'asesor_id' => (int) $p->user_id,
        'asesor' => $p->asesor_nombre ?: $p->asesor_usuario,
        'propio' => (int) $p->user_id === (int) $yo,
        'sync_ok' => (bool) $p->seguimiento_nota_id && ! $p->seguimiento_error,
        'sync_error' => $p->seguimiento_error,
        'fecha' => \Carbon\Carbon::parse($p->created_at)->format('d/m/Y'),
    ])->values();
@endphp

@push('css')
@include('admin.partials.whatsapp-styles')
<style>
    .mp-hero-actions { display:flex; flex-wrap:wrap; gap:.5rem; }
    .mp-hero-actions .btn-new { background:#fff; color:#405189; border:0; border-radius:11px; padding:.52rem 1rem; font-weight:800; font-size:.82rem; display:inline-flex; align-items:center; gap:.4rem; box-shadow:0 10px 22px -14px rgba(0,0,0,.5); }
    .mp-hero-actions .btn-new:hover { transform:translateY(-1px); color:#2f3d70; }

    /* Programas en Inscripciones */
    .mp-progs { display:flex; gap:.6rem; overflow-x:auto; padding:.15rem .1rem .6rem; margin-bottom:.4rem; scrollbar-width:thin; }
    .mp-prog { flex:0 0 auto; min-width:210px; max-width:260px; text-align:left; background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:.7rem .85rem; cursor:pointer; transition:all .15s ease; box-shadow:0 10px 24px -22px rgba(64,81,137,.6); }
    .mp-prog:hover { border-color:#c3cfe2; transform:translateY(-1px); }
    .mp-prog.on { border-color:#405189; box-shadow:0 0 0 2px rgba(64,81,137,.18), 0 14px 26px -20px rgba(64,81,137,.8); }
    .mp-prog .cod { font-size:.66rem; font-weight:800; color:#405189; letter-spacing:.3px; text-transform:uppercase; }
    .mp-prog .nm { font-size:.82rem; font-weight:700; color:#1c2434; line-height:1.2; margin:.15rem 0 .35rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .mp-prog .ft { display:flex; align-items:center; justify-content:space-between; font-size:.7rem; color:#6b7280; }
    .mp-prog .n { background:#eef1f8; color:#405189; border-radius:999px; padding:.05rem .5rem; font-weight:800; }
    .mp-prog.all .nm { -webkit-line-clamp:1; }
    .mp-prog.closed { opacity:.75; }
    [data-layout-mode="dark"] .mp-prog { background:#1d2125; border-color:#2a2f34; }
    [data-layout-mode="dark"] .mp-prog .nm { color:#e9ecef; }

    /* Filtros por estado */
    .mp-states { display:flex; flex-wrap:wrap; gap:.35rem; }
    .mp-state { border:1px solid #e3e6ea; background:#fff; color:#495057; border-radius:999px; padding:.22rem .65rem; font-size:.74rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:.3rem; }
    .mp-state .n { font-size:.68rem; opacity:.75; }
    .mp-state.on { color:#fff; border-color:transparent; }
    [data-layout-mode="dark"] .mp-state { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

    .mp-tone-nuevo { --tone:#64748b; } .mp-tone-contactado { --tone:#2563eb; } .mp-tone-interesado { --tone:#d97706; }
    .mp-tone-no_interesado { --tone:#dc3545; } .mp-tone-inscrito { --tone:#0e9f6e; } .mp-tone-todos { --tone:#405189; }
    .mp-state.on { background:var(--tone); }
    .mp-badge { display:inline-flex; align-items:center; gap:.25rem; border-radius:999px; padding:.08rem .5rem; font-size:.68rem; font-weight:800; color:var(--tone); background:color-mix(in srgb, var(--tone) 12%, transparent); white-space:nowrap; }

    /* Lista de prospectos */
    .mp-list { border:1px solid #eef0f4; border-radius:12px; max-height:560px; overflow-y:auto; overflow-x:hidden; container-type:inline-size; }
    [data-layout-mode="dark"] .mp-list { border-color:#2a2f34; }
    .mp-row { display:grid; grid-template-columns:auto auto minmax(0, 1fr) auto; align-items:center; gap:.7rem; padding:.65rem .8rem; border-bottom:1px solid #f4f6f8; }
    .mp-row:last-child { border-bottom:0; }
    .mp-row:hover { background:#f8fafd; }
    .mp-row.selected { background:#f0f4fc; }
    [data-layout-mode="dark"] .mp-row { border-bottom-color:#232829; }
    [data-layout-mode="dark"] .mp-row:hover { background:#1a1d20; }
    [data-layout-mode="dark"] .mp-row.selected { background:#1c2338; }
    .mp-row .nm { font-size:.85rem; font-weight:700; color:#2b3440; line-height:1.2; }
    [data-layout-mode="dark"] .mp-row .nm { color:#dee2e6; }
    .mp-row .sub { font-size:.72rem; color:#6b7280; display:flex; flex-wrap:wrap; gap:.15rem .7rem; margin-top:.15rem; }
    .mp-row .sub i { opacity:.8; }
    .mp-row .tags { display:flex; flex-wrap:wrap; gap:.3rem; margin-top:.3rem; }
    .mp-row .note { font-size:.72rem; color:#6b7280; font-style:italic; margin-top:.2rem; display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; }
    .mp-actions { display:flex; align-items:center; gap:.3rem; }
    .mp-actions .form-select { width:auto; min-width:8.5rem; font-size:.75rem; padding:.25rem 1.8rem .25rem .55rem; border-radius:8px; }
    .mp-ic { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e3e6ea; background:#fff; color:#495057; cursor:pointer; }
    .mp-ic:hover { border-color:#405189; color:#405189; }
    .mp-ic.danger:hover { border-color:#dc3545; color:#dc3545; }
    [data-layout-mode="dark"] .mp-ic { background:#232829; border-color:#2a2f34; color:#ced4da; }
    .mp-advisor { --tone:#475569; }
    .mp-sync-ok { --tone:#0e9f6e; }
    .mp-sync-err { --tone:#b54708; cursor:pointer; border:0; }
    .mp-empty { text-align:center; color:#6b7280; padding:2.5rem 1rem; }
    .mp-empty i { font-size:2rem; color:#c3cfe2; display:block; margin-bottom:.4rem; }

    .mp-sends { display:flex; flex-direction:column; gap:.45rem; }
    .mp-send { display:flex; align-items:center; justify-content:space-between; gap:.5rem; font-size:.78rem; padding:.5rem .65rem; border:1px solid #eef0f4; border-radius:10px; }
    [data-layout-mode="dark"] .mp-send { border-color:#2a2f34; }
    .mp-send .t { font-weight:700; color:#2b3440; }
    [data-layout-mode="dark"] .mp-send .t { color:#dee2e6; }

    /* Lista angosta: las acciones pasan a una segunda línea bajo los datos. */
    @container (max-width: 660px) {
        .mp-row { grid-template-columns:auto auto minmax(0, 1fr); row-gap:.45rem; }
        .mp-actions { grid-column:3; }
    }
    @container (max-width: 420px) {
        .mp-row { grid-template-columns:auto minmax(0, 1fr); }
        .mp-row .wa-avatar { display:none; }
        .mp-actions { grid-column:2; flex-wrap:wrap; }
    }
</style>
@endpush

@section('content')
<div class="wa-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon"><i class="ri-user-star-line"></i></div>
            <div>
                <h4>{{ $supervisa ? 'Prospectos de marketing' : 'Mis prospectos' }}</h4>
                <p class="hero-sub">Registrá prospectos en los programas en Inscripciones, enviales WhatsApp desde tu línea y seguilos en Notas › Marketing.</p>
            </div>
        </div>
        <div class="mp-hero-actions">
            @if($enInscripciones->isNotEmpty())
                <button type="button" class="btn-new" onclick="abrirFormulario()"><i class="ri-user-add-line"></i> Registrar prospecto</button>
            @endif
            <a href="{{ route('admin.seguimiento-esam', ['next' => '/notes']) }}" target="_blank" rel="noopener" class="btn-back">
                <i class="ri-sticky-note-line"></i> Seguimiento en Notas
            </a>
        </div>
    </div>
    <div class="wa-chips">
        <span class="wa-chip"><i class="ri-door-open-line"></i> {{ $enInscripciones->count() }} programa(s) en Inscripciones</span>
        <span class="wa-chip"><i class="ri-user-star-line"></i> {{ $prospectos->count() }} prospecto(s)</span>
        <span class="wa-chip"><i class="ri-smartphone-line"></i> {{ $conCelular }} con celular</span>
        @if($supervisa)
            <span class="wa-chip"><i class="ri-user-voice-line"></i> {{ $asesores->count() }} asesor(es)</span>
        @endif
    </div>
</div>

@if($error)
    <div class="alert alert-warning d-flex align-items-center gap-2"><i class="ri-error-warning-line"></i> {{ $error }}</div>
@elseif($lineaError)
    <div class="alert alert-warning d-flex align-items-center gap-2"><i class="ri-smartphone-line"></i> {{ $lineaError }} Podés registrar prospectos, pero para enviarles mensajes necesitás tu línea.</div>
@endif
@if($enInscripciones->isEmpty())
    <div class="alert alert-info d-flex align-items-center gap-2"><i class="ri-information-line"></i> No hay programas en etapa de Inscripciones: cuando un programa pase a Inscripciones vas a poder registrar prospectos en él.</div>
@endif

{{-- Programas --}}
<div class="mp-progs" id="mpProgs">
    <button type="button" class="mp-prog all on" data-prog="">
        <div class="cod">Todos</div>
        <div class="nm">Todos los programas</div>
        <div class="ft"><span>Prospectos</span><span class="n" data-count-prog="">{{ $prospectos->count() }}</span></div>
    </button>
    @foreach($programas as $pr)
        @php $n = $prospectos->where('programa_id', $pr->id)->count(); @endphp
        <button type="button" class="mp-prog {{ (int) $pr->estado_id !== (int) $inscripcionesId ? 'closed' : '' }}" data-prog="{{ $pr->id }}" title="{{ $pr->nombre }}">
            <div class="cod">{{ $pr->codigo ?: 'Programa' }} · {{ $pr->gestion }}</div>
            <div class="nm">{{ $pr->nombre }}</div>
            <div class="ft">
                <span>{{ (int) $pr->estado_id === (int) $inscripcionesId ? 'En inscripciones' : 'Inscripciones cerradas' }}</span>
                <span class="n" data-count-prog="{{ $pr->id }}">{{ $n }}</span>
            </div>
        </button>
    @endforeach
</div>

<div class="row g-3">
    {{-- LISTA --}}
    <div class="col-xl-7">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-user-star-line"></i></span>
                <div class="flex-grow-1">
                    <h6>Prospectos</h6>
                    <p id="mpListSub">Marcá a quiénes enviar el mensaje. Solo se envía a tus propios prospectos.</p>
                </div>
                <span class="wa-status info" id="headSel"><i class="ri-checkbox-multiple-line"></i> 0 seleccionados</span>
            </div>
            <div class="wa-card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <div class="wa-search flex-grow-1" style="min-width:200px">
                        <i class="ri-search-line"></i>
                        <input type="text" id="mpBuscar" class="wa-input" placeholder="Buscar por nombre, celular o nota...">
                    </div>
                    @if($supervisa)
                        <select id="mpAsesor" class="wa-select" style="width:auto;min-width:180px">
                            <option value="">Todos los asesores</option>
                            <option value="{{ $yo }}">Solo míos</option>
                            @foreach($asesores as $a)
                                @if($a['id'] !== (int) $yo)
                                    <option value="{{ $a['id'] }}">{{ $a['nombre'] }} ({{ $a['total'] }})</option>
                                @endif
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="mp-states" id="mpStates"></div>
                    <label class="form-check d-flex align-items-center gap-1 mb-0">
                        <input type="checkbox" class="form-check-input" id="checkTodos">
                        <span class="fs-13 fw-semibold">Todos</span>
                    </label>
                </div>
                <div class="mp-list" id="mpList"></div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="text-muted fs-13"><span id="mpShown">0</span> visibles · <span id="selCount" class="fw-bold">0</span> seleccionados</span>
                    <span class="wa-help mb-0"><i class="ri-sticky-note-line"></i> Cada prospecto queda como nota de seguimiento en Notas › Marketing.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MENSAJE --}}
    <div class="col-xl-5">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-whatsapp-line"></i></span>
                <div class="flex-grow-1">
                    <h6>Mensaje por WhatsApp</h6>
                    <p>Personalizado para cada prospecto seleccionado, desde tu línea.</p>
                </div>
            </div>
            <div class="wa-card-body">
                <label class="wa-label" for="partTexto">Mensaje</label>
                <textarea id="partTexto" rows="5" class="wa-textarea" placeholder="Hola @{{nombre}}, te escribo por el programa @{{programa}}..." {{ $puedeEnviar ? '' : 'disabled' }}></textarea>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                    <span class="wa-help mb-0">Insertar:</span>
                    <span class="wa-var" onclick="insertVar('@{{nombre}}')"><i class="ri-user-line"></i> nombre</span>
                    <span class="wa-var" onclick="insertVar('@{{programa}}')"><i class="ri-graduation-cap-line"></i> programa</span>
                    <span class="wa-help mb-0 ms-auto" id="charCount">0 caracteres</span>
                </div>
                <div class="mt-3">
                    <label class="wa-label" for="partArchivo">Adjuntar imagen / audio / video / PDF</label>
                    <input type="file" id="partArchivo" class="wa-input" accept="image/*,audio/*,video/*,application/pdf" {{ $puedeEnviar ? '' : 'disabled' }}>
                    <div class="wa-help">Se envía el mismo archivo a todos los seleccionados (máx. 50 MB).</div>
                </div>
                <div class="mt-3">
                    <label class="wa-label">Vista previa</label>
                    <div class="wa-preview" style="position:static">
                        <div class="meta"><i class="ri-eye-line"></i> <span id="previewWho">Nombre Apellido</span></div>
                        <div class="wa-preview-bubble" id="previewBubble">Escribí un mensaje para ver la vista previa…</div>
                        <div class="wa-preview-file" id="previewFile" style="display:none;"><i class="ri-attachment-2"></i> <span id="previewFileName"></span></div>
                    </div>
                </div>
                <div class="wa-help mt-2"><i class="ri-shield-check-line"></i> Los prospectos en «Nuevo» pasan a «Contactado» al enviarles el mensaje.</div>
                <button type="button" class="wa-btn wa-btn-primary w-100 mt-3" id="btnEnviar" {{ $puedeEnviar ? '' : 'disabled' }} onclick="enviar(this)">
                    <i class="ri-send-plane-fill"></i> Enviar a seleccionados
                </button>

                @if($envios->isNotEmpty())
                    <div class="wa-divider"></div>
                    <label class="wa-label">Mis últimos envíos</label>
                    <div class="mp-sends">
                        @foreach($envios as $e)
                            <div class="mp-send">
                                <div class="min-w-0">
                                    <div class="t text-truncate">{{ $e->resumen }}</div>
                                    <div class="text-muted" style="font-size:.7rem">{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y H:i') }} · {{ $e->total }} mensaje(s)</div>
                                </div>
                                @if($e->batch_id)
                                    <button type="button" class="wa-btn wa-btn-soft wa-btn-sm" onclick="EsamLote.abrir({{ (int) $e->batch_id }}, 'Prospectos · Lote #{{ (int) $e->batch_id }}')"><i class="ri-bar-chart-horizontal-line"></i> Lote #{{ $e->batch_id }}</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Registrar / editar --}}
<div class="modal fade" id="mpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="mpForm" class="modal-content" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="mpModalTitle">Registrar prospecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="mpFormError" class="alert alert-danger py-2 small" style="display:none"></div>
                <div class="mb-3">
                    <label class="wa-label" for="fPrograma">Programa (en Inscripciones) *</label>
                    <select id="fPrograma" name="programa_id" class="wa-select" required>
                        <option value="">Elegí el programa…</option>
                        @foreach($programas as $pr)
                            <option value="{{ $pr->id }}" data-inscripciones="{{ (int) $pr->estado_id === (int) $inscripcionesId ? 1 : 0 }}">{{ $pr->nombre }} · {{ $pr->gestion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-sm-6 mb-2">
                        <label class="wa-label" for="fNombres">Nombres *</label>
                        <input id="fNombres" name="nombres" class="wa-input" maxlength="150" required>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="wa-label" for="fApellidos">Apellidos</label>
                        <input id="fApellidos" name="apellidos" class="wa-input" maxlength="150">
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="wa-label" for="fCelular">Celular (WhatsApp) *</label>
                        <input id="fCelular" name="celular" class="wa-input" maxlength="40" inputmode="tel" placeholder="7XXXXXXX" required>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="wa-label" for="fCorreo">Correo</label>
                        <input id="fCorreo" name="correo" type="email" class="wa-input" maxlength="190">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="wa-label" for="fEstado">Estado</label>
                    <select id="fEstado" name="estado" class="wa-select">
                        @foreach($estados as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="wa-label" for="fNota">Nota</label>
                    <textarea id="fNota" name="nota" class="wa-textarea" rows="2" maxlength="1000" placeholder="¿Cómo llegó? ¿Qué le interesa?"></textarea>
                </div>
                <div class="wa-help mt-2"><i class="ri-sticky-note-line"></i> Se crea una nota de seguimiento en tu línea (Notas › Marketing) para seguirlo desde Seguimiento ESAM.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="wa-btn wa-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="wa-btn wa-btn-primary" id="mpGuardar"><i class="ri-save-3-line"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
@include('admin.partials.whatsapp-lote-modal', ['loteBase' => url('admin/marketing/prospectos/lote')])
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const BASE = @json(url('admin/marketing/prospectos'));
    const ESTADOS = @json($estados);
    const SUPERVISA = @json($supervisa);
    const PUEDE_ENVIAR = @json($puedeEnviar);
    const DATA = @json($data);
    const st = { prog: '', estado: '', asesor: '', q: '', sel: new Set(), editId: null };
    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const digits = (s) => String(s || '').replace(/\D+/g, '');
    const tieneCel = (p) => digits(p.celular).length >= 8;
    const enviable = (p) => p.propio && tieneCel(p);
    const iniciales = (n) => n.split(/\s+/).filter(Boolean).slice(0, 2).map((w) => w[0]).join('').toUpperCase() || '?';

    function base() {
        return DATA.filter((p) => (!st.prog || String(p.programa_id) === st.prog) && (!st.asesor || String(p.asesor_id) === st.asesor));
    }
    function visibles() {
        const q = st.q.toLowerCase();
        return base().filter((p) => (!st.estado || p.estado === st.estado) &&
            (!q || (p.nombre + ' ' + (p.celular || '') + ' ' + (p.nota || '') + ' ' + (p.programa || '') + ' ' + (p.asesor || '')).toLowerCase().includes(q)));
    }

    function renderStates() {
        const b = base();
        const count = (k) => b.filter((p) => p.estado === k).length;
        $('mpStates').innerHTML = `<button type="button" class="mp-state mp-tone-todos ${st.estado ? '' : 'on'}" data-estado="">Todos <span class="n">${b.length}</span></button>` +
            Object.entries(ESTADOS).map(([k, l]) => `<button type="button" class="mp-state mp-tone-${k} ${st.estado === k ? 'on' : ''}" data-estado="${k}">${esc(l)} <span class="n">${count(k)}</span></button>`).join('');
        $('mpStates').querySelectorAll('.mp-state').forEach((btn) => btn.addEventListener('click', () => { st.estado = btn.dataset.estado; render(); }));
    }

    function syncBadge(p) {
        if (p.sync_ok) return '<span class="mp-badge mp-sync-ok" title="Tiene su nota de seguimiento en Seguimiento ESAM"><i class="ri-sticky-note-line"></i> En Notas</span>';
        if (p.sync_error) return `<button type="button" class="mp-badge mp-sync-err" data-sync="${p.id}" title="${esc(p.sync_error)} — clic para reintentar"><i class="ri-refresh-line"></i> Sin seguimiento</button>`;
        return '';
    }

    function render() {
        renderStates();
        const list = visibles();
        $('mpShown').textContent = list.length;
        if (!list.length) {
            $('mpList').innerHTML = `<div class="mp-empty"><i class="ri-user-search-line"></i>${DATA.length ? 'No hay prospectos con estos filtros.' : 'Todavía no hay prospectos. Registrá el primero con «Registrar prospecto».'}</div>`;
        } else {
            $('mpList').innerHTML = list.map((p) => {
                const sel = st.sel.has(p.id);
                const puedeSel = PUEDE_ENVIAR && enviable(p);
                const tip = !p.propio ? 'Es de otro asesor: el envío es solo a tus prospectos' : !tieneCel(p) ? 'Sin celular válido' : '';
                return `<div class="mp-row ${sel ? 'selected' : ''}" data-id="${p.id}">
                    <input type="checkbox" class="form-check-input mpCheck" value="${p.id}" ${sel ? 'checked' : ''} ${puedeSel ? '' : 'disabled'} title="${esc(tip)}">
                    <span class="wa-avatar">${esc(iniciales(p.nombre))}</span>
                    <div class="min-w-0">
                        <div class="nm text-truncate">${esc(p.nombre)}</div>
                        <div class="sub">
                            <span><i class="ri-phone-line"></i> ${tieneCel(p) ? esc(p.celular) : '<span class="text-warning fw-bold">sin celular</span>'}</span>
                            <span class="text-truncate"><i class="ri-graduation-cap-line"></i> ${esc(p.programa || 'Sin programa')}</span>
                            <span><i class="ri-calendar-line"></i> ${esc(p.fecha)}</span>
                        </div>
                        <div class="tags">
                            <span class="mp-badge mp-tone-${esc(p.estado)}">${esc(ESTADOS[p.estado] || p.estado)}</span>
                            ${SUPERVISA ? `<span class="mp-badge mp-advisor"><i class="ri-user-voice-line"></i> ${p.propio ? 'Vos' : esc(p.asesor || 'Asesor')}</span>` : ''}
                            ${syncBadge(p)}
                        </div>
                        ${p.nota ? `<div class="note">${esc(p.nota)}</div>` : ''}
                    </div>
                    <div class="mp-actions">
                        <select class="form-select mpEstado" data-id="${p.id}" aria-label="Estado">${Object.entries(ESTADOS).map(([k, l]) => `<option value="${k}" ${p.estado === k ? 'selected' : ''}>${esc(l)}</option>`).join('')}</select>
                        <button type="button" class="mp-ic" data-edit="${p.id}" title="Editar"><i class="ri-pencil-line"></i></button>
                        <button type="button" class="mp-ic danger" data-del="${p.id}" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>`;
            }).join('');
        }
        bindRows();
        updateSel();
    }

    function bindRows() {
        $('mpList').querySelectorAll('.mpCheck').forEach((c) => c.addEventListener('change', () => {
            const id = Number(c.value);
            c.checked ? st.sel.add(id) : st.sel.delete(id);
            c.closest('.mp-row').classList.toggle('selected', c.checked);
            updateSel();
        }));
        $('mpList').querySelectorAll('.mpEstado').forEach((s) => s.addEventListener('change', () => cambiarEstado(Number(s.dataset.id), s.value, s)));
        $('mpList').querySelectorAll('[data-edit]').forEach((b) => b.addEventListener('click', () => abrirFormulario(Number(b.dataset.edit))));
        $('mpList').querySelectorAll('[data-del]').forEach((b) => b.addEventListener('click', () => eliminar(Number(b.dataset.del))));
        $('mpList').querySelectorAll('[data-sync]').forEach((b) => b.addEventListener('click', () => resincronizar(Number(b.dataset.sync), b)));
    }

    function selected() { return DATA.filter((p) => st.sel.has(p.id)); }

    function updateSel() {
        const n = st.sel.size;
        $('selCount').textContent = n;
        $('headSel').innerHTML = '<i class="ri-checkbox-multiple-line"></i> ' + n + ' seleccionados';
        const posibles = visibles().filter((p) => PUEDE_ENVIAR && enviable(p));
        $('checkTodos').checked = posibles.length > 0 && posibles.every((p) => st.sel.has(p.id));
        $('checkTodos').disabled = !posibles.length;
        updatePreview();
    }

    window.insertVar = function (token) {
        const ta = $('partTexto');
        if (!ta || ta.disabled) return;
        const s = ta.selectionStart ?? ta.value.length, e = ta.selectionEnd ?? ta.value.length;
        ta.value = ta.value.slice(0, s) + token + ta.value.slice(e);
        ta.focus();
        ta.selectionStart = ta.selectionEnd = s + token.length;
        updatePreview();
    };

    function updatePreview() {
        const texto = $('partTexto').value.trim();
        const archivo = $('partArchivo').files[0];
        const sample = selected()[0] || visibles()[0];
        const nombre = sample ? sample.nombre : 'Nombre Apellido';
        const programa = sample ? (sample.programa || '') : 'Programa';
        $('previewWho').textContent = nombre;
        $('charCount').textContent = texto.length + ' caracteres';
        $('previewBubble').textContent = (texto || '(sin texto)').replace(/\{\{\s*nombre\s*\}\}/g, nombre).replace(/\{\{\s*programa\s*\}\}/g, programa);
        $('previewFile').style.display = archivo ? '' : 'none';
        if (archivo) $('previewFileName').textContent = archivo.name;
    }

    async function api(url, method, body) {
        const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } };
        if (body instanceof FormData) opts.body = body;
        else if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res = await fetch(url, opts);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            const msg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'No se pudo completar la acción.');
            const err = new Error(msg); err.data = data; err.status = res.status; throw err;
        }
        return data;
    }

    function aviso(d) {
        if (d && d.seguimiento_error) alert('Guardado. Pero no se pudo crear el seguimiento en Notas:\n' + d.seguimiento_error + '\n\nPodés reintentarlo con «Sin seguimiento».');
    }

    async function cambiarEstado(id, estado, sel) {
        const p = DATA.find((x) => x.id === id);
        const anterior = p.estado;
        sel.disabled = true;
        try {
            const d = await api(`${BASE}/${id}/estado`, 'PATCH', { estado });
            p.estado = estado;
            p.sync_ok = !!d.seguimiento_nota_id && !d.seguimiento_error;
            p.sync_error = d.seguimiento_error;
            render();
        } catch (e) { sel.value = anterior; alert(e.message); }
        finally { sel.disabled = false; }
    }

    async function resincronizar(id, btn) {
        btn.disabled = true;
        try {
            const d = await api(`${BASE}/${id}/sincronizar`, 'POST');
            const p = DATA.find((x) => x.id === id);
            p.sync_ok = true; p.sync_error = null;
            render();
        } catch (e) {
            alert('Sigue sin poder crearse el seguimiento:\n' + ((e.data && e.data.seguimiento_error) || e.message));
            btn.disabled = false;
        }
    }

    async function eliminar(id) {
        const p = DATA.find((x) => x.id === id);
        if (!confirm(`¿Eliminar a ${p.nombre}?\nSu nota de seguimiento pasa a la papelera de Notas.`)) return;
        try {
            await api(`${BASE}/${id}`, 'DELETE');
            DATA.splice(DATA.indexOf(p), 1);
            st.sel.delete(id);
            recount();
            render();
        } catch (e) { alert(e.message); }
    }

    function recount() {
        document.querySelectorAll('[data-count-prog]').forEach((el) => {
            const id = el.dataset.countProg;
            el.textContent = DATA.filter((p) => !id || String(p.programa_id) === id).length;
        });
    }

    let modal;
    window.abrirFormulario = function (id) {
        const p = id ? DATA.find((x) => x.id === id) : null;
        st.editId = p ? p.id : null;
        $('mpModalTitle').textContent = p ? 'Editar prospecto' : 'Registrar prospecto';
        $('mpFormError').style.display = 'none';
        // Solo programas en Inscripciones (y el actual, si se está editando).
        Array.from($('fPrograma').options).forEach((o) => {
            if (!o.value) return;
            o.hidden = o.dataset.inscripciones !== '1' && !(p && String(p.programa_id) === o.value);
        });
        $('fPrograma').value = p ? String(p.programa_id || '') : (st.prog && $('fPrograma').querySelector(`option[value="${st.prog}"][data-inscripciones="1"]`) ? st.prog : '');
        $('fNombres').value = p ? (p.nombres || '') : '';
        $('fApellidos').value = p ? (p.apellidos || '') : '';
        $('fCelular').value = p ? (p.celular || '') : '';
        $('fCorreo').value = p ? (p.correo || '') : '';
        $('fEstado').value = p ? p.estado : 'nuevo';
        $('fNota').value = p ? (p.nota || '') : '';
        modal = modal || new bootstrap.Modal($('mpModal'));
        modal.show();
        setTimeout(() => (p ? $('fNombres') : ($('fPrograma').value ? $('fNombres') : $('fPrograma'))).focus(), 300);
    };

    $('mpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = $('mpGuardar');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        const body = Object.fromEntries(new FormData($('mpForm')).entries());
        try {
            const d = await api(st.editId ? `${BASE}/${st.editId}` : BASE, st.editId ? 'PUT' : 'POST', body);
            aviso(d);
            location.reload();
        } catch (err) {
            $('mpFormError').textContent = err.message;
            $('mpFormError').style.display = '';
        } finally { btn.disabled = false; btn.innerHTML = original; }
    });

    window.enviar = async function (btn) {
        const sel = selected();
        if (!sel.length) { alert('Seleccioná al menos un prospecto.'); return; }
        const texto = $('partTexto').value.trim();
        const archivo = $('partArchivo').files[0];
        if (!texto && !archivo) { alert('Escribí un mensaje o adjuntá un archivo.'); return; }
        const programas = [...new Set(sel.map((p) => p.programa).filter(Boolean))];
        if (!confirm(`¿Enviar el mensaje a ${sel.length} prospecto(s)?` + (programas.length ? '\nPrograma(s): ' + programas.join(', ') : '') + '\n\nSale desde tu línea de WhatsApp, respetando el ritmo anti-baneo.')) return;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
        const fd = new FormData();
        sel.forEach((p) => fd.append('prospectos[]', p.id));
        fd.append('text', texto);
        if (archivo) fd.append('file', archivo);
        try {
            const d = await api(`${BASE}/enviar`, 'POST', fd);
            sel.forEach((p) => { if (p.estado === 'nuevo' && tieneCel(p)) p.estado = 'contactado'; });
            st.sel.clear();
            render();
            const extra = (d.sin_telefono ? ' · sin celular: ' + d.sin_telefono : '') + (d.contactados ? ' · ' + d.contactados + ' pasaron a Contactado' : '');
            if (d.batch_id) EsamLote.abrir(d.batch_id, 'Prospectos · Lote #' + d.batch_id + ' · ' + d.total + ' mensaje(s)' + extra);
            else alert('Encolados ' + d.total + ' mensaje(s)' + extra);
        } catch (e) { alert(e.message); }
        finally { btn.disabled = !PUEDE_ENVIAR; btn.innerHTML = original; }
    };

    // Filtros
    document.querySelectorAll('.mp-prog').forEach((b) => b.addEventListener('click', () => {
        st.prog = b.dataset.prog;
        document.querySelectorAll('.mp-prog').forEach((x) => x.classList.toggle('on', x === b));
        render();
    }));
    $('mpBuscar').addEventListener('input', (e) => { st.q = e.target.value.trim(); render(); });
    if ($('mpAsesor')) $('mpAsesor').addEventListener('change', (e) => { st.asesor = e.target.value; render(); });
    $('checkTodos').addEventListener('change', (e) => {
        visibles().filter((p) => PUEDE_ENVIAR && enviable(p)).forEach((p) => e.target.checked ? st.sel.add(p.id) : st.sel.delete(p.id));
        render();
    });
    $('partTexto').addEventListener('input', updatePreview);
    $('partArchivo').addEventListener('change', updatePreview);

    render();
})();
</script>
@endpush
