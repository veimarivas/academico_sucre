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
<style>
    :root { --wa-green: #25D366; --wa-green-dark: #128C7E; }

    /* ── Hero ─────────────────────────────────────────────── */
    .wa-hero {
        position: relative;
        background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
        border-radius: 18px;
        padding: 1.65rem 1.9rem;
        color: #fff;
        overflow: hidden;
        box-shadow: 0 18px 40px -22px rgba(64, 81, 137, .55);
        margin-bottom: 1.25rem;
    }
    .wa-hero::before { content:""; position:absolute; top:-60px; right:-50px; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.20) 0%, transparent 68%); border-radius:50%; }
    .wa-hero::after  { content:""; position:absolute; bottom:-80px; left:30%; width:300px; height:300px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
    .wa-hero > * { position: relative; z-index: 1; }
    .wa-hero .hero-icon {
        width: 58px; height: 58px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 16px; font-size: 1.8rem; flex-shrink: 0;
        backdrop-filter: blur(5px);
    }
    .wa-hero h4 { color:#fff; font-weight: 800; margin: 0; letter-spacing: -.3px; }
    .wa-hero .hero-sub { color: rgba(255,255,255,.9); font-size: .86rem; margin: 3px 0 0; }
    .wa-hero .wa-chips { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.9rem; }
    .wa-hero .wa-chip {
        display:inline-flex; align-items:center; gap:.35rem;
        background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.24);
        border-radius: 999px; padding: .24rem .72rem; font-size: .72rem; font-weight: 700; color:#fff;
    }
    .wa-hero .btn-back {
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.32); color:#fff;
        border-radius: 11px; padding: .52rem 1rem; font-weight: 700; font-size: .82rem;
        display:inline-flex; align-items:center; gap:.4rem; transition: all .15s ease;
    }
    .wa-hero .btn-back:hover { background: rgba(255,255,255,.28); color:#fff; transform: translateY(-1px); }

    /* ── Cards ────────────────────────────────────────────── */
    .wa-card {
        background: #fff; border: 1px solid #eef0f4; border-radius: 16px;
        box-shadow: 0 14px 34px -26px rgba(64, 81, 137, .45);
        overflow: hidden; height: 100%; display: flex; flex-direction: column;
    }
    [data-layout-mode="dark"] .wa-card { background: #1d2125; border-color: #2a2f34; }
    .wa-card-head {
        display: flex; align-items: center; gap: .8rem;
        padding: 1rem 1.25rem; border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(135deg, #fbfcfe 0%, #f1f4fa 100%);
    }
    [data-layout-mode="dark"] .wa-card-head { background: #1a1d20; border-bottom-color: #2a2f34; }
    .wa-card-ic {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;
        color: #fff; background: linear-gradient(135deg, var(--wa-green-dark), var(--wa-green));
        box-shadow: 0 6px 14px -8px rgba(18,140,126,.85);
    }
    .wa-card-head h6 { margin: 0; font-weight: 800; color: #1c2434; font-size: .96rem; }
    [data-layout-mode="dark"] .wa-card-head h6 { color: #e9ecef; }
    .wa-card-head p { margin: 0; font-size: .75rem; color: #6b7280; }
    .wa-card-body { padding: 1.2rem 1.3rem; flex: 1; display: flex; flex-direction: column; }

    /* status pills */
    .wa-status { display:inline-flex; align-items:center; gap:.35rem; flex-shrink:0; border-radius: 999px; padding: .24rem .66rem; font-size: .68rem; font-weight: 800; font-variant-numeric: tabular-nums; }
    .wa-status.ok { background: #e8f8f0; color: #0f766e; border: 1px solid #b7ebd3; }
    .wa-status.off { background: #fff4ed; color: #b54708; border: 1px solid #fde3c8; }
    .wa-status.info { background: #eef1f8; color: #405189; border: 1px solid #c3cfe2; }
    [data-layout-mode="dark"] .wa-status.info { background:#232a3d; color:#a9b7e0; border-color:#313b57; }

    /* controls */
    .wa-label { display:block; font-size:.68rem; font-weight: 800; text-transform: uppercase; letter-spacing:.45px; color:#6c757d; margin-bottom:.32rem; }
    [data-layout-mode="dark"] .wa-label { color:#98a2b3; }
    .wa-input, .wa-select, .wa-textarea {
        width:100%; border:1px solid #e3e6ea; border-radius: 10px;
        padding: .55rem .82rem; font-size: .86rem; color:#2b3440; background:#fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    [data-layout-mode="dark"] .wa-input, [data-layout-mode="dark"] .wa-select, [data-layout-mode="dark"] .wa-textarea { background:#1d2125; border-color:#2a2f34; color:#ced4da; }
    .wa-input:focus, .wa-select:focus, .wa-textarea:focus { outline:0; border-color:#5e72b8; box-shadow: 0 0 0 .18rem rgba(94,114,184,.18); }
    .wa-textarea { resize: vertical; }
    .wa-help { font-size:.72rem; color:#6b7280; margin-top:.32rem; }
    .wa-help code { background:#eef1f8; color:#405189; padding:.05rem .38rem; border-radius:5px; font-size:.72rem; }

    .wa-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:.42rem;
        border-radius:10px; padding:.56rem 1.05rem; font-weight:700; font-size:.82rem;
        border:1px solid transparent; cursor:pointer; transition: all .15s ease; white-space:nowrap;
    }
    .wa-btn-primary { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color:#fff; box-shadow: 0 10px 20px -12px rgba(64,81,137,.85); }
    .wa-btn-primary:hover { color:#fff; transform: translateY(-1px); box-shadow: 0 14px 26px -12px rgba(64,81,137,.95); }
    .wa-btn-soft { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
    .wa-btn-soft:hover { background:#e1e7f3; color:#405189; }
    .wa-btn-ghost { background:#fff; color:#495057; border-color:#e3e6ea; }
    [data-layout-mode="dark"] .wa-btn-ghost { background:#232829; color:#ced4da; border-color:#2a2f34; }
    .wa-btn-sm { padding:.42rem .8rem; font-size:.78rem; }
    .wa-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

    .wa-divider { height:1px; background:#f1f3f5; margin:1.15rem 0; }
    [data-layout-mode="dark"] .wa-divider { background:#2a2f34; }

    /* grupo registrado (solo lectura) */
    .wa-group-current {
        display:flex; align-items:center; justify-content:space-between; gap:.75rem;
        background: linear-gradient(135deg, #f6f9fe, #eef3fb); border:1px solid #e1e9f6;
        border-radius:12px; padding:.75rem .85rem;
    }
    [data-layout-mode="dark"] .wa-group-current { background:#1a2233; border-color:#2b3853; }
    .wa-group-info { display:flex; align-items:center; gap:.6rem; min-width:0; }
    .wa-group-ic { width:38px; height:38px; border-radius:10px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; color:#0f766e; background:#e8f8f0; font-size:1.15rem; }
    .wa-group-current .nm { font-weight:800; font-size:.9rem; color:#1c2434; line-height:1.2; }
    [data-layout-mode="dark"] .wa-group-current .nm { color:#e9ecef; }
    .wa-group-current .id { font-size:.72rem; color:#6b7280; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; word-break:break-all; }

    .wa-note { display:flex; align-items:center; gap:.5rem; background:#fff8ec; border:1px solid #fde3c8; color:#b54708; border-radius:10px; padding:.65rem .85rem; font-size:.82rem; font-weight:600; }

    /* variables */
    .wa-var {
        display:inline-flex; align-items:center; gap:.28rem;
        background:#eef1f8; color:#405189; border:1px dashed #b9c5e0;
        border-radius:999px; padding:.2rem .62rem; font-size:.72rem; font-weight:700; cursor:pointer;
        transition: all .12s ease;
    }
    .wa-var:hover { background:#e1e7f3; transform: translateY(-1px); }

    /* preview */
    .wa-preview { background: linear-gradient(180deg, #e5ddd5, #dcd3c9); border-radius: 14px; padding: .85rem; position: sticky; top: 1rem; }
    [data-layout-mode="dark"] .wa-preview { background:#15191c; }
    .wa-preview .meta { font-size:.68rem; color:#5d6b73; margin-bottom:.45rem; font-weight:800; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:.3rem; }
    [data-layout-mode="dark"] .wa-preview .meta { color:#8a97a0; }
    .wa-preview-bubble {
        background:#dcf8c6; border-radius: 10px 10px 10px 3px; padding:.7rem .8rem;
        font-size:.82rem; color:#1f2a24; white-space:pre-wrap; word-break:break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,.1); min-height: 3rem;
    }
    [data-layout-mode="dark"] .wa-preview-bubble { background:#005c4b; color:#e9edef; }
    .wa-preview-file { display:flex; align-items:center; gap:.4rem; margin-top:.5rem; font-size:.72rem; color:#4b5a63; background:rgba(255,255,255,.55); border-radius:8px; padding:.35rem .55rem; }
    [data-layout-mode="dark"] .wa-preview-file { color:#cfd8dc; background:rgba(255,255,255,.06); }

    /* participants */
    .wa-search { position:relative; }
    .wa-search i { position:absolute; top:50%; left:.78rem; transform:translateY(-50%); color:#adb5bd; }
    .wa-search input { padding-left:2.35rem; }
    .wa-list { border:1px solid #eef0f4; border-radius: 12px; max-height: 320px; overflow-y:auto; }
    [data-layout-mode="dark"] .wa-list { border-color:#2a2f34; }
    .wa-part { display:flex; align-items:center; gap:.75rem; padding:.6rem .8rem; border-bottom:1px solid #f4f6f8; cursor:pointer; transition: background .12s ease; }
    [data-layout-mode="dark"] .wa-part { border-bottom-color:#232829; }
    .wa-part:last-child { border-bottom:0; }
    .wa-part:hover { background:#f8fafd; }
    [data-layout-mode="dark"] .wa-part:hover { background:#1a1d20; }
    .wa-part.selected { background:#f0f4fc; }
    [data-layout-mode="dark"] .wa-part.selected { background:#1c2338; }
    .wa-avatar {
        width:36px; height:36px; border-radius:50%; flex-shrink:0; color:#fff;
        display:inline-flex; align-items:center; justify-content:center; font-size:.74rem; font-weight:800;
        background: linear-gradient(135deg, var(--wa-green-dark), var(--wa-green));
    }
    .wa-part .nm { font-size:.83rem; font-weight:700; color:#2b3440; line-height:1.15; }
    [data-layout-mode="dark"] .wa-part .nm { color:#dee2e6; }
    .wa-part .ph { font-size:.72rem; color:#6b7280; font-variant-numeric: tabular-nums; }
    .wa-part .no-tel { color:#e8833a; font-weight:700; }
    /* Dados de baja: en rojo para no enviarles mensajes personalizados */
    .wa-part.wa-part-baja { background:#fff1f1; border-left:3px solid #dc3545; }
    .wa-part.wa-part-baja:hover { background:#ffe6e6; }
    .wa-part.wa-part-baja.selected { background:#ffd9d9; }
    .wa-part.wa-part-baja .nm { color:#b42318; }
    .wa-part.wa-part-baja .wa-avatar { background:#dc3545; color:#fff; }
    [data-layout-mode="dark"] .wa-part.wa-part-baja { background:#3a1d20; }
    [data-layout-mode="dark"] .wa-part.wa-part-baja .nm { color:#f19a9a; }
    .wa-baja-tag { display:inline-flex; align-items:center; gap:.2rem; margin:.1rem 0; padding:0 .4rem; border-radius:999px;
        font-size:.66rem; font-weight:700; color:#fff; background:#dc3545; }
    .wa-bajas-hint { margin-bottom:.5rem; padding:.4rem .7rem; border-radius:8px; font-size:.78rem; color:#b42318; background:#fff1f1; }
    [data-layout-mode="dark"] .wa-bajas-hint { background:#3a1d20; color:#f19a9a; }

    .form-check-input:checked { background-color:#405189; border-color:#405189; }
</style>
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
