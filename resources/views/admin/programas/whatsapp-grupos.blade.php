@extends('admin.layouts.master')

@section('title', 'WhatsApp a varios grupos - ' . config('app.name'))

@php
    $estadoColor = [1 => 'success', 4 => 'info', 3 => 'warning', 2 => 'secondary'];
    $conGrupoTotal = $programas->filter(fn ($p) => filled($p->whatsapp_grupo_id))->count();
@endphp

@push('css')
<style>
    :root { --wa-green: #25D366; --wa-green-dark: #128C7E; }

    .wa-hero {
        position: relative; overflow: hidden; margin-bottom: 1.25rem;
        background: linear-gradient(135deg, #128C7E 0%, #25a56a 45%, #405189 100%);
        border-radius: 18px; padding: 1.6rem 1.9rem; color: #fff;
        box-shadow: 0 18px 40px -22px rgba(18, 140, 126, .6);
    }
    .wa-hero::before { content:""; position:absolute; top:-60px; right:-50px; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.2) 0%, transparent 68%); border-radius:50%; }
    .wa-hero > * { position: relative; z-index: 1; }
    .wa-hero .hero-icon { width:58px; height:58px; display:inline-flex; align-items:center; justify-content:center; background: rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.3); border-radius:16px; font-size:1.8rem; flex-shrink:0; }
    .wa-hero h4 { color:#fff; font-weight:800; margin:0; }
    .wa-hero .hero-sub { color: rgba(255,255,255,.9); font-size:.86rem; margin:3px 0 0; }
    .wa-hero .wa-chip { display:inline-flex; align-items:center; gap:.35rem; background: rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.24); border-radius:999px; padding:.24rem .72rem; font-size:.72rem; font-weight:700; color:#fff; }

    .wa-card { background:#fff; border:1px solid #eef0f4; border-radius:16px; box-shadow: 0 14px 34px -26px rgba(64,81,137,.45); overflow:hidden; height:100%; display:flex; flex-direction:column; }
    [data-layout-mode="dark"] .wa-card { background:#1d2125; border-color:#2a2f34; }
    .wa-card-head { display:flex; align-items:center; gap:.8rem; padding:1rem 1.25rem; border-bottom:1px solid #f1f3f5; background: linear-gradient(135deg, #fbfcfe 0%, #f1f4fa 100%); }
    [data-layout-mode="dark"] .wa-card-head { background:#1a1d20; border-bottom-color:#2a2f34; }
    .wa-card-ic { width:40px; height:40px; border-radius:12px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; color:#fff; background: linear-gradient(135deg, var(--wa-green-dark), var(--wa-green)); }
    .wa-card-head h6 { margin:0; font-weight:800; color:#1c2434; font-size:.96rem; }
    [data-layout-mode="dark"] .wa-card-head h6 { color:#e9ecef; }
    .wa-card-head p { margin:0; font-size:.75rem; color:#6b7280; }
    .wa-card-body { padding:1.1rem 1.25rem; flex:1; }

    .wa-label { display:block; font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.45px; color:#6c757d; margin-bottom:.32rem; }
    .wa-input, .wa-textarea { width:100%; border:1px solid #e3e6ea; border-radius:10px; padding:.55rem .82rem; font-size:.86rem; color:#2b3440; background:#fff; }
    [data-layout-mode="dark"] .wa-input, [data-layout-mode="dark"] .wa-textarea { background:#1d2125; border-color:#2a2f34; color:#ced4da; }
    .wa-input:focus, .wa-textarea:focus { outline:0; border-color:#5e72b8; box-shadow: 0 0 0 .18rem rgba(94,114,184,.18); }
    .wa-help { font-size:.72rem; color:#6b7280; margin-top:.32rem; }
    .wa-help code { background:#eef1f8; color:#405189; padding:.05rem .38rem; border-radius:5px; }
    .wa-btn { display:inline-flex; align-items:center; justify-content:center; gap:.42rem; border-radius:10px; padding:.56rem 1.05rem; font-weight:700; font-size:.82rem; border:1px solid transparent; cursor:pointer; white-space:nowrap; }
    .wa-btn-primary { background: linear-gradient(135deg, var(--wa-green-dark) 0%, var(--wa-green) 100%); color:#fff; }
    .wa-btn-primary:hover { color:#fff; filter: brightness(1.05); }
    .wa-btn-soft { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
    .wa-btn-ghost { background:#fff; color:#495057; border-color:#e3e6ea; }
    .wa-btn-sm { padding:.34rem .7rem; font-size:.74rem; }
    .wa-btn:disabled { opacity:.5; cursor:not-allowed; }

    /* Estados (filtro) */
    .estado-chips { display:flex; flex-wrap:wrap; gap:.4rem; }
    .estado-chip { display:inline-flex; align-items:center; gap:.35rem; border:1px solid #e3e6ea; background:#fff; border-radius:999px; padding:.3rem .8rem; font-size:.78rem; font-weight:700; color:#495057; cursor:pointer; user-select:none; }
    .estado-chip input { display:none; }
    .estado-chip .n { background:#f1f3f5; border-radius:999px; padding:0 .45rem; font-size:.7rem; }
    .estado-chip.on { background:#405189; border-color:#405189; color:#fff; }
    .estado-chip.on .n { background: rgba(255,255,255,.25); }
    [data-layout-mode="dark"] .estado-chip { background:#1d2125; border-color:#2a2f34; color:#ced4da; }

    .wa-search { position:relative; }
    .wa-search i { position:absolute; top:50%; left:.78rem; transform:translateY(-50%); color:#adb5bd; }
    .wa-search input { padding-left:2.35rem; }

    .prog-list { border:1px solid #eef0f4; border-radius:12px; max-height:560px; overflow-y:auto; }
    [data-layout-mode="dark"] .prog-list { border-color:#2a2f34; }
    .prog-row { display:flex; align-items:center; gap:.75rem; padding:.65rem .85rem; border-bottom:1px solid #f4f6f8; }
    .prog-row:last-child { border-bottom:0; }
    .prog-row.selected { background:#effaf4; }
    [data-layout-mode="dark"] .prog-row { border-bottom-color:#232829; }
    [data-layout-mode="dark"] .prog-row.selected { background:#16302a; }
    .prog-row .nm { font-weight:800; font-size:.84rem; color:#1c2434; line-height:1.2; }
    [data-layout-mode="dark"] .prog-row .nm { color:#e9ecef; }
    .prog-row .meta { font-size:.7rem; color:#6b7280; display:flex; flex-wrap:wrap; gap:.15rem .6rem; margin-top:2px; }
    .grp { display:flex; align-items:center; gap:.4rem; font-size:.74rem; margin-top:.3rem; }
    .grp.ok { color:#0f766e; }
    .grp.off { color:#b54708; font-weight:700; }
    .grp .gid { font-family: ui-monospace, Menlo, monospace; color:#6b7280; font-size:.66rem; }
    .grp-edit { border:0; background:none; color:#6b7280; padding:0 .2rem; }
    .grp-edit:hover { color:#405189; }

    .wa-preview { background: linear-gradient(180deg, #e5ddd5, #dcd3c9); border-radius:14px; padding:.85rem; }
    [data-layout-mode="dark"] .wa-preview { background:#15191c; }
    .wa-preview .meta { font-size:.68rem; color:#5d6b73; margin-bottom:.45rem; font-weight:800; text-transform:uppercase; }
    .wa-preview-bubble { background:#dcf8c6; border-radius:10px 10px 10px 3px; padding:.7rem .8rem; font-size:.82rem; color:#1f2a24; white-space:pre-wrap; word-break:break-word; min-height:3rem; }
    [data-layout-mode="dark"] .wa-preview-bubble { background:#005c4b; color:#e9edef; }
    .wa-var { display:inline-flex; align-items:center; gap:.28rem; background:#eef1f8; color:#405189; border:1px dashed #b9c5e0; border-radius:999px; padding:.2rem .62rem; font-size:.72rem; font-weight:700; cursor:pointer; }
    .sel-summary { background:#f6f9fe; border:1px solid #e1e9f6; border-radius:10px; padding:.55rem .8rem; font-size:.8rem; }
    [data-layout-mode="dark"] .sel-summary { background:#1a2233; border-color:#2b3853; }
    .form-check-input:checked { background-color:#128C7E; border-color:#128C7E; }
</style>
@endpush

@section('content')
<div class="wa-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon"><i class="ri-chat-group-line"></i></div>
            <div>
                <h4>Enviar a varios grupos de WhatsApp</h4>
                <p class="hero-sub">El mismo mensaje a los grupos de los programas que elijas, en un solo envío por Seguimiento ESAM.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="wa-chip"><i class="ri-graduation-cap-line"></i> {{ $programas->count() }} programas</span>
            <span class="wa-chip"><i class="ri-chat-group-line"></i> <span id="chipConGrupo">{{ $conGrupoTotal }}</span> con grupo</span>
        </div>
    </div>
</div>

@if($error)
    <div class="alert alert-warning d-flex align-items-center gap-2"><i class="ri-error-warning-line"></i> {{ $error }}</div>
@endif

<div class="row g-3">
    {{-- PROGRAMAS --}}
    <div class="col-xl-7">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-list-check-2"></i></span>
                <div class="flex-grow-1">
                    <h6>Programas y sus grupos</h6>
                    <p>Elegí los estados y marcá los grupos que recibirán el mensaje.</p>
                </div>
            </div>
            <div class="wa-card-body">
                <label class="wa-label">Estado del programa</label>
                <div class="estado-chips mb-3" id="estadoChips">
                    @foreach($estados as $id => $nombre)
                        @php $n = $programas->where('estado_id', $id)->count(); @endphp
                        <label class="estado-chip {{ $id === 1 ? 'on' : '' }}">
                            <input type="checkbox" value="{{ $id }}" {{ $id === 1 ? 'checked' : '' }}>
                            {{ $nombre }} <span class="n">{{ $n }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <div class="wa-search flex-grow-1">
                        <i class="ri-search-line"></i>
                        <input type="text" id="buscar" class="wa-input" placeholder="Buscar programa, código o gestión...">
                    </div>
                    <label class="form-check d-flex align-items-center gap-1 mb-0">
                        <input type="checkbox" class="form-check-input" id="checkTodos">
                        <span class="fs-13 fw-semibold">Todos con grupo</span>
                    </label>
                </div>
                <div class="wa-help mb-2" id="listInfo"></div>

                <div class="prog-list" id="progList">
                    @forelse($programas as $p)
                        @php
                            $tiene = filled($p->whatsapp_grupo_id);
                            $grupoData = ['id' => $p->id, 'nombre' => $p->nombre, 'gid' => $p->whatsapp_grupo_id, 'gnombre' => $p->whatsapp_grupo_nombre];
                        @endphp
                        <div class="prog-row" data-id="{{ $p->id }}" data-estado="{{ $p->estado_id }}"
                             data-search="{{ strtolower($p->nombre.' '.$p->codigo.' '.$p->gestion.' '.($p->whatsapp_grupo_nombre ?? '')) }}">
                            <input type="checkbox" class="form-check-input progCheck" value="{{ $p->id }}" {{ $tiene ? '' : 'disabled' }}
                                   title="{{ $tiene ? 'Incluir este grupo' : 'Registrá el grupo para poder incluirlo' }}">
                            <div class="flex-grow-1 min-w-0">
                                <div class="nm text-truncate" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                <div class="meta">
                                    <span><i class="ri-hashtag"></i> {{ $p->codigo }}</span>
                                    <span><i class="ri-calendar-2-line"></i> {{ $p->gestion }}</span>
                                    <span class="badge bg-soft-{{ $estadoColor[$p->estado_id] ?? 'secondary' }} text-{{ $estadoColor[$p->estado_id] ?? 'secondary' }}">{{ $estados[$p->estado_id] ?? '' }}</span>
                                    <span><i class="ri-group-line"></i> {{ $p->vigentes }} vigentes</span>
                                </div>
                                <div class="grp-box">
                                    @if($tiene)
                                        <div class="grp ok">
                                            <i class="ri-chat-group-line"></i>
                                            <span class="gname">{{ $p->whatsapp_grupo_nombre ?: 'Grupo sin nombre' }}</span>
                                            <span class="gid d-none d-md-inline">{{ $p->whatsapp_grupo_id }}</span>
                                            @if($puedeGestionarGrupo)
                                                <button type="button" class="grp-edit" title="Editar grupo"
                                                        data-grupo='@json($grupoData)'><i class="ri-edit-line"></i></button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="grp off">
                                            <i class="ri-error-warning-line"></i> Sin grupo
                                            @if($puedeGestionarGrupo)
                                                <button type="button" class="wa-btn wa-btn-soft wa-btn-sm ms-1 grp-edit"
                                                        data-grupo='@json($grupoData)'><i class="ri-add-line"></i> Registrar grupo</button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.programas.whatsapp.index', $p->id) }}" class="wa-btn wa-btn-ghost wa-btn-sm" title="Abrir el envío de este programa"><i class="ri-external-link-line"></i></a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No hay programas disponibles.</div>
                    @endforelse
                    <div class="text-center text-muted py-4" id="listEmpty" style="display:none;">Ningún programa coincide con el filtro.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MENSAJE --}}
    <div class="col-xl-5">
        <div class="wa-card">
            <div class="wa-card-head">
                <span class="wa-card-ic"><i class="ri-send-plane-2-line"></i></span>
                <div class="flex-grow-1">
                    <h6>Mensaje para los grupos</h6>
                    <p>Se envía a cada grupo marcado, con pausas anti-baneo.</p>
                </div>
            </div>
            <div class="wa-card-body">
                <div class="sel-summary mb-3" id="selSummary">Ningún grupo seleccionado.</div>
                <label class="wa-label" for="texto">Mensaje</label>
                <textarea id="texto" rows="6" class="wa-textarea" placeholder="Escribí el mensaje para los grupos..."></textarea>
                <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                    <span class="wa-help m-0">Variable:</span>
                    <span class="wa-var" onclick="insertVar('{programa}')"><i class="ri-graduation-cap-line"></i> {programa}</span>
                    <span class="wa-help m-0">se reemplaza por el nombre de cada programa.</span>
                </div>
                <div class="mt-3">
                    <label class="wa-label" for="qrSel">QR de pago (opcional)</label>
                    @if($qrs->isEmpty())
                        <div class="wa-help m-0">No hay QR vigentes para adjuntar.</div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <select id="qrSel" class="wa-input" style="flex:1">
                                <option value="">Sin QR</option>
                                @foreach($qrs as $q)
                                    <option value="{{ $q->id }}" data-img="{{ $q->imagen_url }}">{{ $q->nombre }} · {{ $q->banco }} (vence {{ \Carbon\Carbon::parse($q->fecha_vencimiento)->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                            <a id="qrThumb" href="#" target="_blank" rel="noopener" hidden style="width:46px;height:46px;border:1px solid #e3e6ea;border-radius:8px;overflow:hidden;background:#fff;flex-shrink:0"><img alt="QR" style="width:100%;height:100%;object-fit:contain"></a>
                        </div>
                        <label class="d-inline-flex align-items-center gap-1 mt-1 wa-help"><input type="checkbox" id="qrDatos" class="form-check-input m-0" checked disabled> Incluir datos de la cuenta en el mensaje</label>
                        <div class="wa-help m-0">Solo se listan los QR vigentes. Con QR no se adjunta otro archivo.</div>
                    @endif
                </div>
                <div class="mt-3">
                    <label class="wa-label" for="archivo">Adjuntar imagen / audio / video / PDF</label>
                    <input type="file" id="archivo" class="wa-input" accept="image/*,audio/*,video/*,application/pdf">
                </div>
                <div class="mt-3">
                    <label class="wa-label">Vista previa</label>
                    <div class="wa-preview">
                        <div class="meta"><i class="ri-eye-line"></i> <span id="previewWho">Grupo</span></div>
                        <div class="wa-preview-bubble" id="previewBubble">Escribí un mensaje para ver la vista previa…</div>
                    </div>
                </div>
                <button type="button" class="wa-btn wa-btn-primary w-100 mt-3" id="btnEnviar" onclick="enviar(this)" disabled>
                    <i class="ri-send-plane-fill"></i> <span>Enviar a los grupos</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: registrar / editar grupo --}}
@if($puedeGestionarGrupo)
<div class="modal fade" id="modalGrupo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0"><i class="ri-chat-group-line text-success"></i> Grupo del programa</h5>
                    <small class="text-muted" id="mgPrograma">—</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="wa-label" for="mgInvite">Enlace de invitación del grupo</label>
                    <div class="input-group">
                        <input type="text" id="mgInvite" class="wa-input" style="border-top-right-radius:0;border-bottom-right-radius:0;" placeholder="https://chat.whatsapp.com/...">
                        <button class="wa-btn wa-btn-soft" type="button" id="mgResolver"><i class="ri-magic-line"></i> Resolver</button>
                    </div>
                    <div class="wa-help">Pegá el enlace del grupo y se completa el ID y el nombre.</div>
                </div>
                <div class="mb-3">
                    <label class="wa-label" for="mgId">ID del grupo</label>
                    <input type="text" id="mgId" class="wa-input" placeholder="120363000000000000@g.us">
                    <div class="wa-help">Debe terminar en <code>@g.us</code>.</div>
                </div>
                <div>
                    <label class="wa-label" for="mgNombre">Nombre (referencia)</label>
                    <input type="text" id="mgNombre" class="wa-input" placeholder="Ej: Grupo Diplomado X">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="wa-btn wa-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="wa-btn wa-btn-primary" id="mgGuardar"><i class="ri-save-3-line"></i> Guardar grupo</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
@include('admin.partials.whatsapp-lote-modal')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const BASE_PROG = @json(url('admin/programas'));

    function rows() { return Array.prototype.slice.call(document.querySelectorAll('#progList .prog-row')); }
    function estadosOn() {
        return Array.prototype.map.call(document.querySelectorAll('#estadoChips input:checked'), c => c.value);
    }
    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch])); }

    // Filtro por estado + búsqueda. Al ocultar un programa se desmarca (solo se envía a lo visible).
    function aplicarFiltro() {
        const est = estadosOn();
        const q = document.getElementById('buscar').value.trim().toLowerCase();
        let visibles = 0, conGrupo = 0;
        rows().forEach(function (r) {
            const show = est.includes(r.dataset.estado) && (!q || r.dataset.search.includes(q));
            r.style.display = show ? '' : 'none';
            const c = r.querySelector('.progCheck');
            if (!show && c.checked) c.checked = false;
            if (show) { visibles++; if (!c.disabled) conGrupo++; }
        });
        document.getElementById('listEmpty').style.display = visibles ? 'none' : 'block';
        document.getElementById('listInfo').textContent = visibles + ' programa(s) visibles · ' + conGrupo + ' con grupo · ' + (visibles - conGrupo) + ' sin grupo';
        actualizarSel();
    }

    function seleccionados() {
        return rows().filter(r => r.style.display !== 'none' && r.querySelector('.progCheck').checked);
    }

    function actualizarSel() {
        const sel = seleccionados();
        rows().forEach(r => r.classList.toggle('selected', r.querySelector('.progCheck').checked));
        const box = document.getElementById('selSummary');
        box.innerHTML = sel.length
            ? '<i class="ri-checkbox-multiple-line text-success"></i> <b>' + sel.length + '</b> grupo(s) seleccionado(s): ' +
              sel.slice(0, 4).map(r => esc(r.querySelector('.nm').textContent.trim())).join(', ') + (sel.length > 4 ? ' y ' + (sel.length - 4) + ' más' : '')
            : 'Ningún grupo seleccionado.';
        const btn = document.getElementById('btnEnviar');
        btn.disabled = sel.length === 0;
        btn.querySelector('span').textContent = sel.length ? 'Enviar a ' + sel.length + ' grupo(s)' : 'Enviar a los grupos';
        const visiblesConGrupo = rows().filter(r => r.style.display !== 'none' && !r.querySelector('.progCheck').disabled);
        const todos = document.getElementById('checkTodos');
        todos.checked = visiblesConGrupo.length > 0 && visiblesConGrupo.every(r => r.querySelector('.progCheck').checked);
        todos.indeterminate = sel.length > 0 && !todos.checked;
        actualizarPreview();
    }

    function actualizarPreview() {
        const sel = seleccionados();
        const primero = sel[0] || rows().find(r => r.style.display !== 'none');
        const programa = primero ? primero.querySelector('.nm').textContent.trim() : 'Programa';
        const grupo = primero && primero.querySelector('.gname') ? primero.querySelector('.gname').textContent.trim() : 'Grupo';
        const texto = document.getElementById('texto').value;
        document.getElementById('previewWho').textContent = grupo;
        document.getElementById('previewBubble').textContent = texto
            ? texto.split('{programa}').join(programa)
            : 'Escribí un mensaje para ver la vista previa…';
    }

    function insertVar(token) {
        const ta = document.getElementById('texto');
        const s = ta.selectionStart ?? ta.value.length, e = ta.selectionEnd ?? ta.value.length;
        ta.value = ta.value.slice(0, s) + token + ta.value.slice(e);
        ta.focus(); ta.selectionStart = ta.selectionEnd = s + token.length;
        actualizarPreview();
    }

    document.querySelectorAll('#estadoChips input').forEach(function (c) {
        c.addEventListener('change', function () { c.closest('.estado-chip').classList.toggle('on', c.checked); aplicarFiltro(); });
    });
    document.getElementById('buscar').addEventListener('input', aplicarFiltro);
    document.getElementById('texto').addEventListener('input', actualizarPreview);
    document.getElementById('progList').addEventListener('change', function (e) { if (e.target.classList.contains('progCheck')) actualizarSel(); });
    document.getElementById('checkTodos').addEventListener('change', function () {
        const on = this.checked;
        rows().forEach(function (r) {
            const c = r.querySelector('.progCheck');
            if (r.style.display !== 'none' && !c.disabled) c.checked = on;
        });
        actualizarSel();
    });

    // QR elegido: miniatura y opción de datos de la cuenta.
    (function () {
        const qrSel = document.getElementById('qrSel');
        if (!qrSel) return;
        qrSel.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const thumb = document.getElementById('qrThumb');
            thumb.hidden = !this.value;
            if (this.value) { thumb.href = opt.dataset.img; thumb.querySelector('img').src = opt.dataset.img; }
            document.getElementById('qrDatos').disabled = !this.value;
        });
    })();

    async function enviar(btn) {
        const sel = seleccionados();
        if (!sel.length) return;
        const texto = document.getElementById('texto').value.trim();
        const archivo = document.getElementById('archivo').files[0];
        const qrSel = document.getElementById('qrSel');
        const qrId = qrSel ? qrSel.value : '';
        if (qrId && archivo) { alert('Elegí el QR o un archivo: cada mensaje lleva un solo adjunto.'); return; }
        if (!texto && !archivo && !qrId) { alert('Escribí un mensaje o adjuntá un archivo.'); return; }
        if (!confirm('¿Enviar el mensaje a ' + sel.length + ' grupo(s) de WhatsApp?' + (qrId ? '\n\nSe adjunta el QR: ' + qrSel.options[qrSel.selectedIndex].text : ''))) return;

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('text', texto);
        sel.forEach(r => fd.append('programas[]', r.dataset.id));
        if (archivo) fd.append('file', archivo);
        if (qrId) {
            fd.append('qr_id', qrId);
            fd.append('qr_datos', document.getElementById('qrDatos').checked ? '1' : '0');
        }
        try {
            const res = await fetch('{{ route('admin.programas.whatsapp.grupos-enviar') }}', {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success && data.batch_id) {
                EsamLote.abrir(data.batch_id, 'Grupos · Lote #' + data.batch_id + ' · ' + data.total + ' grupo(s)');
            } else {
                alert((res.ok && data.success ? '✓ ' : '⚠ ') + (data.message || ('Encolado. Lote #' + (data.batch_id || ''))));
            }
        } catch (e) { alert('⚠ ' + e.message); }
        finally { btn.innerHTML = original; actualizarSel(); }
    }

    @if($puedeGestionarGrupo)
    // ── Registrar / editar el grupo de un programa sin salir de la página ──
    let mgActual = null;
    const modalGrupo = new bootstrap.Modal(document.getElementById('modalGrupo'));

    document.getElementById('progList').addEventListener('click', function (e) {
        const b = e.target.closest('.grp-edit');
        if (!b) return;
        mgActual = JSON.parse(b.dataset.grupo);
        document.getElementById('mgPrograma').textContent = mgActual.nombre;
        document.getElementById('mgInvite').value = '';
        document.getElementById('mgId').value = mgActual.gid || '';
        document.getElementById('mgNombre').value = mgActual.gnombre || '';
        modalGrupo.show();
    });

    document.getElementById('mgResolver').addEventListener('click', async function () {
        const invite = document.getElementById('mgInvite').value.trim();
        if (!invite) { alert('Pegá el enlace de invitación del grupo.'); return; }
        this.disabled = true;
        try {
            const res = await fetch(BASE_PROG + '/' + mgActual.id + '/whatsapp/grupo/resolver', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ invite: invite }),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                document.getElementById('mgId').value = data.id;
                if (data.name) document.getElementById('mgNombre').value = data.name;
            } else {
                alert(data.message || 'No se pudo resolver el grupo.');
            }
        } catch (err) { alert(err.message); }
        finally { this.disabled = false; }
    });

    document.getElementById('mgGuardar').addEventListener('click', async function () {
        const gid = document.getElementById('mgId').value.trim();
        const gnombre = document.getElementById('mgNombre').value.trim();
        if (gid && !/@g\.us$/.test(gid)) { alert('El ID del grupo debe terminar en @g.us.'); return; }
        this.disabled = true;
        try {
            const res = await fetch(BASE_PROG + '/' + mgActual.id + '/whatsapp/grupo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ whatsapp_grupo_id: gid, whatsapp_grupo_nombre: gnombre }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                alert((data.errors && Object.values(data.errors)[0][0]) || data.message || 'No se pudo guardar el grupo.');
                return;
            }
            actualizarFilaGrupo(mgActual.id, mgActual.nombre, data.id, data.name);
            modalGrupo.hide();
        } catch (err) { alert(err.message); }
        finally { this.disabled = false; }
    });

    function actualizarFilaGrupo(id, nombre, gid, gnombre) {
        const row = document.querySelector('#progList .prog-row[data-id="' + id + '"]');
        if (!row) return;
        const box = row.querySelector('.grp-box');
        const check = row.querySelector('.progCheck');
        const payload = esc(JSON.stringify({ id: id, nombre: nombre, gid: gid, gnombre: gnombre }));
        if (gid) {
            box.innerHTML = '<div class="grp ok"><i class="ri-chat-group-line"></i> <span class="gname">' + esc(gnombre || 'Grupo sin nombre') + '</span>' +
                ' <span class="gid d-none d-md-inline">' + esc(gid) + '</span>' +
                '<button type="button" class="grp-edit" title="Editar grupo" data-grupo="' + payload + '"><i class="ri-edit-line"></i></button></div>';
            check.disabled = false;
            check.checked = true;
            check.title = 'Incluir este grupo';
        } else {
            box.innerHTML = '<div class="grp off"><i class="ri-error-warning-line"></i> Sin grupo ' +
                '<button type="button" class="wa-btn wa-btn-soft wa-btn-sm ms-1 grp-edit" data-grupo="' + payload + '"><i class="ri-add-line"></i> Registrar grupo</button></div>';
            check.checked = false;
            check.disabled = true;
        }
        row.dataset.search = (nombre + ' ' + (gnombre || '')).toLowerCase() + ' ' + row.dataset.search;
        document.getElementById('chipConGrupo').textContent = rows().filter(r => !r.querySelector('.progCheck').disabled).length;
        aplicarFiltro();
    }
    @endif

    aplicarFiltro();
</script>
@endpush
