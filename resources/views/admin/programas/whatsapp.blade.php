@extends('admin.layouts.master')

@section('title', 'WhatsApp - ' . $programa->nombre . ' - ' . config('app.name'))

@php
    $totalInscritos = $participantes->count();
    $conCelular = $participantes->filter(fn ($p) => trim((string) $p->celular) !== '')->count();
    $puedeEditar = auth()->user()->puedeWhatsappPrograma($programa->id);
    $puedeGestionarGrupo = auth()->user()->can('whatsapp.admin');
    $tieneGrupo = (bool) $programa->whatsapp_grupo_id;
@endphp

@push('css')
@include('admin.partials.whatsapp-styles')
@endpush

@section('content')
<div class="wa-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon"><i class="ri-whatsapp-line"></i></div>
            <div>
                <h4>Envío por WhatsApp</h4>
                <p class="hero-sub">{{ $programa->nombre }}</p>
            </div>
        </div>
        <a href="{{ route('admin.programas.show', $programa->id) }}" class="btn-back">
            <i class="ri-arrow-left-line"></i> Volver al programa
        </a>
    </div>
    <div class="wa-chips">
        <span class="wa-chip"><i class="ri-hashtag"></i> {{ $programa->codigo }}</span>
        <span class="wa-chip"><i class="ri-calendar-2-line"></i> Gestión {{ $programa->gestion }}</span>
        <span class="wa-chip"><i class="ri-group-line"></i> {{ $totalInscritos }} participantes</span>
        <span class="wa-chip"><i class="ri-smartphone-line"></i> {{ $conCelular }} con celular</span>
    </div>
</div>

@if($error)
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="ri-error-warning-line"></i> {{ $error }}
    </div>
@endif

<div class="row g-3">
    {{-- GRUPO --}}
    <div class="col-lg-5">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-chat-group-line"></i></span>
                <div class="flex-grow-1">
                    <h6>Grupo del programa</h6>
                    <p>Grupo de WhatsApp vinculado al programa.</p>
                </div>
                @if($tieneGrupo)
                    <span class="wa-status ok"><i class="ri-checkbox-circle-line"></i> Registrado</span>
                @else
                    <span class="wa-status off"><i class="ri-error-warning-line"></i> Sin grupo</span>
                @endif
            </div>
            <div class="wa-card-body">
                {{-- Grupo registrado (solo lectura) --}}
                @if($tieneGrupo)
                    <div class="wa-group-current" id="grupoActual">
                        <div class="wa-group-info">
                            <span class="wa-group-ic"><i class="ri-chat-group-line"></i></span>
                            <div class="min-w-0">
                                <div class="nm text-truncate">{{ $programa->whatsapp_grupo_nombre ?: 'Grupo sin nombre' }}</div>
                                <div class="id"><i class="ri-links-line"></i> {{ $programa->whatsapp_grupo_id }}</div>
                            </div>
                        </div>
                        @if($puedeGestionarGrupo)
                            <button type="button" class="wa-btn wa-btn-soft wa-btn-sm" onclick="toggleGrupoForm(true)">
                                <i class="ri-edit-line"></i> Editar
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Formulario: registrar (sin grupo) o editar (con permiso) --}}
                @if($puedeGestionarGrupo)
                    <form method="POST" action="{{ route('admin.programas.whatsapp.grupo', $programa->id) }}"
                          id="grupoForm" style="{{ $tieneGrupo ? 'display:none;margin-top:1rem' : '' }}">
                        @csrf
                        <div class="mb-3">
                            <label class="wa-label" for="grupoInvite">Enlace de invitación del grupo</label>
                            <div class="input-group">
                                <input type="text" id="grupoInvite" class="wa-input" style="border-top-right-radius:0;border-bottom-right-radius:0;" placeholder="https://chat.whatsapp.com/...">
                                <button class="wa-btn wa-btn-soft" type="button" onclick="resolverGrupo()"><i class="ri-magic-line"></i> Resolver</button>
                            </div>
                            <div class="wa-help">Pegá el enlace del grupo y se completa el ID y el nombre.</div>
                        </div>
                        <div class="mb-3">
                            <label class="wa-label" for="grupoId">ID del grupo</label>
                            <input type="text" id="grupoId" name="whatsapp_grupo_id" class="wa-input"
                                   value="{{ $programa->whatsapp_grupo_id }}" placeholder="120363000000000000@g.us">
                            <div class="wa-help">Debe terminar en <code>@g.us</code>.</div>
                        </div>
                        <div class="mb-3">
                            <label class="wa-label" for="grupoNombre">Nombre (referencia)</label>
                            <input type="text" id="grupoNombre" name="whatsapp_grupo_nombre" class="wa-input"
                                   value="{{ $programa->whatsapp_grupo_nombre }}" placeholder="Ej: Grupo Diplomado X">
                        </div>
                        <div class="d-flex gap-2">
                            <button class="wa-btn wa-btn-primary flex-grow-1"><i class="ri-save-3-line"></i> Guardar grupo</button>
                            @if($tieneGrupo)
                                <button type="button" class="wa-btn wa-btn-ghost" onclick="toggleGrupoForm(false)">Cancelar</button>
                            @endif
                        </div>
                    </form>
                @else
                    @unless($tieneGrupo)
                        <div class="wa-note"><i class="ri-lock-line"></i> No tenés permiso para registrar el grupo. Pedile a un administrador.</div>
                    @endunless
                @endif

                <div class="wa-divider"></div>

                <a href="{{ route('admin.programas.whatsapp.grupos') }}" class="wa-btn wa-btn-soft w-100 mb-3">
                    <i class="ri-chat-group-line"></i> Enviar a varios grupos de programas
                </a>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0 fw-bold" style="font-size:.9rem;">Enviar al grupo</h6>
                    @if($puedeEditar && ! $tieneGrupo)
                        <span class="wa-help mb-0">Guardá un grupo para habilitar.</span>
                    @endif
                </div>

                @if($puedeEditar)
                    <div class="mb-2">
                        <textarea id="grupoTexto" rows="3" class="wa-textarea" placeholder="Mensaje para el grupo..." {{ $tieneGrupo ? '' : 'disabled' }}></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="wa-label" for="grupoArchivo">Adjuntar imagen / audio / video</label>
                        <input type="file" id="grupoArchivo" class="wa-input" accept="image/*,audio/*,video/*,application/pdf" {{ $tieneGrupo ? '' : 'disabled' }}>
                    </div>
                    <button type="button" class="wa-btn wa-btn-primary w-100" id="btnEnviarGrupo" {{ $tieneGrupo ? '' : 'disabled' }} onclick="enviarGrupo(this)">
                        <i class="ri-send-plane-fill"></i> Enviar al grupo
                    </button>
                @else
                    <div class="wa-note"><i class="ri-lock-line"></i> No tenés permiso para enviar mensajes.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- PARTICIPANTES --}}
    <div class="col-lg-7">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-user-voice-line"></i></span>
                <div class="flex-grow-1">
                    <h6>Participantes</h6>
                    <p>Mensaje personalizado para los seleccionados.</p>
                </div>
                <span class="wa-status info" id="headSel"><i class="ri-checkbox-multiple-line"></i> 0 seleccionados</span>
            </div>
            <div class="wa-card-body">
                @if($puedeEditar)
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="wa-label" for="partTexto">Mensaje personalizado</label>
                            <textarea id="partTexto" rows="5" class="wa-textarea" placeholder="Escribí el mensaje..." oninput="updatePreview()"></textarea>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                <span class="wa-help mb-0">Insertar:</span>
                                <span class="wa-var" onclick="insertVar('@{{nombre}}')"><i class="ri-user-line"></i> nombre</span>
                                <span class="wa-var" onclick="insertVar('@{{programa}}')"><i class="ri-graduation-cap-line"></i> programa</span>
                                <span class="wa-help mb-0 ms-auto" id="charCount">0 caracteres</span>
                            </div>
                            <div class="mt-3">
                                <label class="wa-label" for="partArchivo">Adjuntar imagen / audio / video</label>
                                <input type="file" id="partArchivo" class="wa-input" accept="image/*,audio/*,video/*,application/pdf" onchange="updatePreview()">
                                <div class="wa-help">Se envía la misma media a todos los seleccionados.</div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="wa-label">Vista previa</label>
                            <div class="wa-preview">
                                <div class="meta"><i class="ri-eye-line"></i> <span id="previewWho">Nombre Apellido</span></div>
                                <div class="wa-preview-bubble" id="previewBubble">Escribí un mensaje para ver la vista previa…</div>
                                <div class="wa-preview-file" id="previewFile" style="display:none;"><i class="ri-attachment-2"></i> <span id="previewFileName"></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="wa-divider"></div>
                @endif

                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="wa-search flex-grow-1">
                        <i class="ri-search-line"></i>
                        <input type="text" id="partBuscar" class="wa-input" placeholder="Buscar por nombre o celular..." oninput="filterParts(this.value)">
                    </div>
                    @if($puedeEditar)
                        <label class="form-check d-flex align-items-center gap-1 mb-0">
                            <input type="checkbox" class="form-check-input" id="checkTodos" onchange="toggleTodos(this)">
                            <span class="fs-13 fw-semibold">Todos</span>
                        </label>
                    @endif
                </div>

                @php $totalBajas = $participantes->filter(fn ($p) => $p->baja)->count(); @endphp
                @if($totalBajas > 0)
                    <div class="wa-bajas-hint">
                        <i class="ri-user-unfollow-line"></i>
                        {{ $totalBajas }} participante(s) dado(s) de baja en rojo. «Todos» no los incluye; no se les recomienda enviar mensajes.
                    </div>
                @endif
                <div class="wa-list" id="partList">
                    @forelse($participantes as $p)
                        @php $tieneTel = trim((string) $p->celular) !== ''; @endphp
                        <label class="wa-part {{ $p->baja ? 'wa-part-baja' : '' }}" data-search="{{ strtolower($p->apellidos.' '.$p->nombres.' '.$p->celular) }}"
                               @if($p->baja) title="{{ $p->baja }}: no se recomienda enviarle mensajes" @endif>
                            <input type="checkbox" class="form-check-input partCheck" value="{{ $p->id }}" @if($p->baja) data-baja="1" @endif
                                   {{ ($puedeEditar && $tieneTel) ? '' : 'disabled' }} onchange="updateSel()">
                            <span class="wa-avatar">{{ strtoupper(mb_substr($p->nombres, 0, 1) . mb_substr($p->apellidos, 0, 1)) }}</span>
                            <span class="flex-grow-1 min-w-0">
                                <span class="nm d-block text-truncate">{{ $p->apellidos }}, {{ $p->nombres }}</span>
                                @if($p->baja)
                                    <span class="wa-baja-tag"><i class="ri-user-unfollow-line"></i> {{ $p->baja }}</span>
                                @endif
                                <span class="ph d-block">
                                    <i class="ri-phone-line"></i>
                                    @if($tieneTel) {{ $p->celular }} @else <span class="no-tel">sin celular</span> @endif
                                </span>
                            </span>
                        </label>
                    @empty
                        <div class="text-center text-muted py-4">Sin participantes en el programa.</div>
                    @endforelse
                </div>

                <div class="d-flex align-items-center justify-content-between mt-3 gap-2">
                    <span class="text-muted fs-13"><span id="selCount" class="fw-bold">0</span> de {{ $totalInscritos }} seleccionados</span>
                    @if($puedeEditar)
                        <button type="button" class="wa-btn wa-btn-primary" id="btnEnviarPart" onclick="enviarParticipantes(this)">
                            <i class="ri-send-plane-fill"></i> Enviar a seleccionados
                        </button>
                    @else
                        <span class="wa-note"><i class="ri-lock-line"></i> Solo lectura</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@include('admin.partials.whatsapp-lote-modal')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const WA_PROGRAMA = @json($programa->nombre);
    const WA_PARTS = @json($participantes->mapWithKeys(fn ($p) => [$p->id => ['nombre' => $p->apellidos . ', ' . $p->nombres, 'celular' => $p->celular]])->all());

    function showResult(ok, msg) {
        alert((ok ? '✓ ' : '⚠ ') + msg);
    }

    function toggleGrupoForm(show) {
        const form = document.getElementById('grupoForm');
        const actual = document.getElementById('grupoActual');
        if (form) form.style.display = show ? '' : 'none';
        if (actual) actual.style.display = show ? 'none' : '';
    }

    async function resolverGrupo() {
        const invite = document.getElementById('grupoInvite').value.trim();
        if (! invite) { showResult(false, 'Pegá el enlace de invitación del grupo.'); return; }
        try {
            const res = await fetch('{{ route('admin.programas.whatsapp.grupo-resolver', $programa->id) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ invite: invite }),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                document.getElementById('grupoId').value = data.id;
                if (data.name) document.getElementById('grupoNombre').value = data.name;
                showResult(true, 'Grupo encontrado: ' + (data.name || data.id) + '. Guardá para confirmar.');
            } else {
                showResult(false, data.message || 'No se pudo resolver el grupo.');
            }
        } catch (e) { showResult(false, e.message); }
    }

    function insertVar(token) {
        const ta = document.getElementById('partTexto');
        if (! ta) return;
        const start = ta.selectionStart ?? ta.value.length;
        const end = ta.selectionEnd ?? ta.value.length;
        ta.value = ta.value.slice(0, start) + token + ta.value.slice(end);
        ta.focus();
        ta.selectionStart = ta.selectionEnd = start + token.length;
        updatePreview();
    }

    function selectedIds() {
        return Array.from(document.querySelectorAll('.partCheck:checked')).map(c => c.value);
    }

    function updateSel() {
        document.querySelectorAll('.wa-part').forEach(function (row) {
            const check = row.querySelector('.partCheck');
            row.classList.toggle('selected', check && check.checked);
        });
        const n = selectedIds().length;
        const sel = document.getElementById('selCount');
        if (sel) sel.textContent = n;
        const head = document.getElementById('headSel');
        if (head) head.innerHTML = '<i class="ri-checkbox-multiple-line"></i> ' + n + ' seleccionados';
        updatePreview();
    }

    function updatePreview() {
        const ta = document.getElementById('partTexto');
        if (! ta) return;
        const texto = ta.value.trim();
        const archivo = document.getElementById('partArchivo').files[0];
        const ids = selectedIds();
        const sample = ids.length && WA_PARTS[ids[0]] ? WA_PARTS[ids[0]].nombre : 'Nombre Apellido';

        document.getElementById('previewWho').textContent = sample;
        document.getElementById('charCount').textContent = texto.length + ' caracteres';

        const out = (texto || '(sin texto)')
            .replace(/\{\{\s*nombre\s*\}\}/g, sample)
            .replace(/\{\{\s*programa\s*\}\}/g, WA_PROGRAMA);

        document.getElementById('previewBubble').textContent = out;

        const fileBox = document.getElementById('previewFile');
        if (archivo) {
            document.getElementById('previewFileName').textContent = archivo.name;
            fileBox.style.display = '';
        } else {
            fileBox.style.display = 'none';
        }
    }

    function filterParts(q) {
        q = (q || '').toLowerCase().trim();
        document.querySelectorAll('.wa-part').forEach(function (row) {
            row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
        });
    }

    function toggleTodos(cb) {
        // «Todos» = todos los activos; los dados de baja solo se marcan a mano.
        document.querySelectorAll('.partCheck').forEach(function (c) {
            if (! c.disabled && ! c.dataset.baja) c.checked = cb.checked;
        });
        updateSel();
    }

    async function enviarGrupo(btn) {
        const texto = document.getElementById('grupoTexto').value.trim();
        const archivo = document.getElementById('grupoArchivo').files[0];
        if (! texto && ! archivo) { showResult(false, 'Escribí un mensaje o adjuntá un archivo.'); return; }
        if (! confirm('¿Enviar el mensaje al grupo del programa?')) return;

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('text', texto);
        if (archivo) fd.append('file', archivo);

        try {
            const res = await fetch('{{ route('admin.programas.whatsapp.grupo-enviar', $programa->id) }}', {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success && data.batch_id) {
                EsamLote.abrir(data.batch_id, 'Grupo del programa · Lote #' + data.batch_id);
            } else {
                showResult(res.ok && data.success, data.message || ('Encolado. Lote #' + (data.batch_id || '')));
            }
        } catch (e) { showResult(false, e.message); }
        finally { btn.disabled = false; btn.innerHTML = original; }
    }

    async function enviarParticipantes(btn) {
        const ids = selectedIds();
        if (ids.length === 0) { showResult(false, 'Seleccioná al menos un participante.'); return; }
        const texto = document.getElementById('partTexto').value.trim();
        const archivo = document.getElementById('partArchivo').files[0];
        if (! texto && ! archivo) { showResult(false, 'Escribí un mensaje o adjuntá un archivo.'); return; }
        const bajas = document.querySelectorAll('.partCheck[data-baja="1"]:checked').length;
        if (! confirm('¿Enviar a ' + ids.length + ' participante(s)?' +
            (bajas ? '\n\nAtención: ' + bajas + ' de ellos está(n) dado(s) de baja.' : ''))) return;

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

        const fd = new FormData();
        fd.append('_token', CSRF);
        ids.forEach(id => fd.append('participantes[]', id));
        fd.append('text', texto);
        if (archivo) fd.append('file', archivo);

        try {
            const res = await fetch('{{ route('admin.programas.whatsapp.participantes', $programa->id) }}', {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: fd,
            });
            const data = await res.json().catch(() => ({}));
            const extra = (data.sin_telefono > 0) ? (' · sin teléfono: ' + data.sin_telefono) : '';
            if (res.ok && data.success && data.batch_id) {
                EsamLote.abrir(data.batch_id, 'Participantes · Lote #' + data.batch_id + ' · ' + data.total + ' mensaje(s)' + extra);
            } else {
                showResult(res.ok && data.success, data.message || ('Encolados ' + data.total + ' (lote #' + (data.batch_id || '') + ')' + extra));
            }
        } catch (e) { showResult(false, e.message); }
        finally { btn.disabled = false; btn.innerHTML = original; }
    }

    updatePreview();
</script>
@endpush
