@extends('admin.layouts.master')

@section('title', 'QR y cuentas bancarias - ' . config('app.name'))

@php
    $vigentes = $qrs->whereIn('estado', ['vigente', 'por_vencer'])->count();
    $porVencer = $qrs->where('estado', 'por_vencer')->count();
    $vencidos = $qrs->where('estado', 'vencido')->count();
    $estados = [
        'vigente' => ['Vigente', 'ok', 'ri-checkbox-circle-line'],
        'por_vencer' => ['Por vencer', 'warn', 'ri-time-line'],
        'vencido' => ['Vencido', 'bad', 'ri-close-circle-line'],
        'inactivo' => ['Desactivado', 'off', 'ri-pause-circle-line'],
    ];
    $data = $qrs->map(fn ($q) => [
        'id' => (int) $q->id, 'nombre' => $q->nombre, 'banco' => $q->banco, 'titular' => $q->titular,
        'numero_cuenta' => $q->numero_cuenta, 'tipo_cuenta' => $q->tipo_cuenta, 'moneda' => $q->moneda,
        'fecha_vencimiento' => $q->fecha_vencimiento, 'observacion' => $q->observacion, 'imagen_url' => $q->imagen_url,
    ])->keyBy('id');
@endphp

@push('css')
@include('admin.partials.whatsapp-styles')
<style>
    .qr-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(290px, 1fr)); gap:1rem; }
    .qr-card { background:#fff; border:1px solid #eef0f4; border-radius:16px; overflow:hidden; display:flex; flex-direction:column;
        box-shadow:0 14px 34px -26px rgba(64,81,137,.45); border-top:4px solid var(--tone); }
    [data-layout-mode="dark"] .qr-card { background:#1d2125; border-color:#2a2f34; }
    .qr-card.is-ok { --tone:#0e9f6e; } .qr-card.is-warn { --tone:#d97706; } .qr-card.is-bad { --tone:#dc3545; } .qr-card.is-off { --tone:#94a3b8; }
    .qr-card.is-bad .qr-img img, .qr-card.is-off .qr-img img { filter:grayscale(1); opacity:.55; }
    .qr-top { display:flex; gap:.85rem; padding:1rem; }
    .qr-img { width:110px; height:110px; flex-shrink:0; border-radius:12px; background:#f6f8fb; border:1px solid #eef0f4; display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:zoom-in; }
    .qr-img img { width:100%; height:100%; object-fit:contain; }
    .qr-info { min-width:0; flex:1; }
    .qr-name { font-weight:800; font-size:.95rem; color:#1c2434; line-height:1.2; }
    [data-layout-mode="dark"] .qr-name { color:#e9ecef; }
    .qr-bank { font-size:.78rem; color:#405189; font-weight:700; margin:.15rem 0 .35rem; }
    .qr-kv { font-size:.76rem; color:#6b7280; line-height:1.45; overflow-wrap:anywhere; }
    .qr-kv b { color:#2b3440; font-weight:600; }
    [data-layout-mode="dark"] .qr-kv b { color:#dee2e6; }
    .qr-st { display:inline-flex; align-items:center; gap:.25rem; border-radius:999px; padding:.12rem .6rem; font-size:.7rem; font-weight:800; color:var(--tone); background:color-mix(in srgb, var(--tone) 12%, transparent); }
    .qr-foot { margin-top:auto; display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.65rem 1rem; border-top:1px solid #f1f3f5; background:#fbfcfe; }
    [data-layout-mode="dark"] .qr-foot { background:#1a1d20; border-top-color:#2a2f34; }
    .qr-due { font-size:.74rem; color:#6b7280; }
    .qr-due b { color:var(--tone); }
    .qr-actions { display:flex; gap:.3rem; }
    .qr-ic { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; border:1px solid #e3e6ea; background:#fff; color:#495057; cursor:pointer; }
    .qr-ic:hover { border-color:#405189; color:#405189; }
    .qr-ic.danger:hover { border-color:#dc3545; color:#dc3545; }
    [data-layout-mode="dark"] .qr-ic { background:#232829; border-color:#2a2f34; color:#ced4da; }
    .qr-empty { text-align:center; padding:3rem 1rem; color:#6b7280; }
    .qr-empty i { font-size:2.5rem; color:#c3cfe2; display:block; margin-bottom:.5rem; }
    .qr-preview { width:100%; max-width:220px; aspect-ratio:1; margin:0 auto; border:2px dashed #c3cfe2; border-radius:14px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f6f8fb; color:#6b7280; font-size:.8rem; text-align:center; cursor:pointer; }
    .qr-preview img { width:100%; height:100%; object-fit:contain; }
</style>
@endpush

@section('content')
<div class="wa-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon"><i class="ri-qr-code-line"></i></div>
            <div>
                <h4>QR y cuentas bancarias</h4>
                <p class="hero-sub">Registrá los QR de pago con su fecha de vencimiento para adjuntarlos en los mensajes de cobranza y de grupos. Un QR vencido no se puede elegir.</p>
            </div>
        </div>
        <button type="button" class="btn-back" style="background:#fff;color:#405189;border:0;" onclick="abrirQr()"><i class="ri-add-line"></i> Registrar QR</button>
    </div>
    <div class="wa-chips">
        <span class="wa-chip"><i class="ri-checkbox-circle-line"></i> {{ $vigentes }} vigente(s)</span>
        <span class="wa-chip"><i class="ri-time-line"></i> {{ $porVencer }} por vencer (≤ {{ $avisoDias }} días)</span>
        <span class="wa-chip"><i class="ri-close-circle-line"></i> {{ $vencidos }} vencido(s)</span>
    </div>
</div>

@if($qrs->isEmpty())
    <div class="wa-card"><div class="qr-empty"><i class="ri-qr-code-line"></i>Todavía no hay QR registrados. Registrá el primero para poder adjuntarlo en los mensajes masivos.</div></div>
@else
    <div class="qr-grid">
        @foreach($qrs as $q)
            @php [$label, $tone, $icon] = $estados[$q->estado]; @endphp
            <div class="qr-card is-{{ $tone }}">
                <div class="qr-top">
                    <a class="qr-img" href="{{ $q->imagen_url }}" target="_blank" rel="noopener" title="Ver el QR en tamaño completo">
                        <img src="{{ $q->imagen_url }}" alt="QR {{ $q->nombre }}" loading="lazy">
                    </a>
                    <div class="qr-info">
                        <div class="qr-name">{{ $q->nombre }}</div>
                        <div class="qr-bank"><i class="ri-bank-line"></i> {{ $q->banco }}</div>
                        @if($q->numero_cuenta)<div class="qr-kv">Cuenta: <b>{{ $q->numero_cuenta }}</b>{{ $q->tipo_cuenta ? ' · '.$q->tipo_cuenta : '' }} · {{ $q->moneda }}</div>@endif
                        @if($q->titular)<div class="qr-kv">Titular: <b>{{ $q->titular }}</b></div>@endif
                        @if($q->observacion)<div class="qr-kv fst-italic">{{ $q->observacion }}</div>@endif
                        <div class="mt-2"><span class="qr-st"><i class="{{ $icon }}"></i> {{ $label }}</span></div>
                    </div>
                </div>
                <div class="qr-foot">
                    <span class="qr-due">Vence: <b>{{ \Carbon\Carbon::parse($q->fecha_vencimiento)->format('d/m/Y') }}</b>
                        @if($q->estado === 'vencido') · hace {{ abs($q->dias_para_vencer) }} día(s)
                        @elseif($q->activo) · {{ $q->dias_para_vencer === 0 ? 'hoy' : 'en '.$q->dias_para_vencer.' día(s)' }}
                        @endif
                    </span>
                    <span class="qr-actions">
                        <button type="button" class="qr-ic" title="Editar" onclick="abrirQr({{ $q->id }})"><i class="ri-pencil-line"></i></button>
                        <button type="button" class="qr-ic" title="{{ $q->activo ? 'Desactivar' : 'Activar' }}" onclick="toggleQr({{ $q->id }})"><i class="{{ $q->activo ? 'ri-pause-line' : 'ri-play-line' }}"></i></button>
                        <button type="button" class="qr-ic danger" title="Eliminar" onclick="eliminarQr({{ $q->id }}, @js($q->nombre))"><i class="ri-delete-bin-line"></i></button>
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="qrForm" class="modal-content" enctype="multipart/form-data" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalTitle">Registrar QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="qrError" class="alert alert-danger py-2 small" style="display:none"></div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="wa-label">Imagen del QR *</label>
                        <label class="qr-preview" id="qrPreview" for="qrImagen"><span><i class="ri-image-add-line fs-3 d-block"></i>Elegí la imagen<br>(PNG, JPG o WEBP · máx. 5 MB)</span></label>
                        <input type="file" id="qrImagen" name="imagen" accept="image/png,image/jpeg,image/webp" class="d-none">
                        <div class="wa-help text-center" id="qrImagenHelp"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2">
                            <div class="col-sm-6"><label class="wa-label" for="qNombre">Nombre *</label><input id="qNombre" name="nombre" class="wa-input" maxlength="120" placeholder="Cobranzas Diplomados" required></div>
                            <div class="col-sm-6"><label class="wa-label" for="qBanco">Banco *</label><input id="qBanco" name="banco" class="wa-input" maxlength="120" placeholder="Banco Nacional de Bolivia" required></div>
                            <div class="col-sm-6"><label class="wa-label" for="qTitular">Titular</label><input id="qTitular" name="titular" class="wa-input" maxlength="160" placeholder="ESAM Sucre"></div>
                            <div class="col-sm-6"><label class="wa-label" for="qCuenta">Número de cuenta</label><input id="qCuenta" name="numero_cuenta" class="wa-input" maxlength="60"></div>
                            <div class="col-sm-4"><label class="wa-label" for="qTipo">Tipo de cuenta</label>
                                <select id="qTipo" name="tipo_cuenta" class="wa-select"><option value="">—</option><option>Caja de ahorro</option><option>Cuenta corriente</option></select></div>
                            <div class="col-sm-3"><label class="wa-label" for="qMoneda">Moneda</label>
                                <select id="qMoneda" name="moneda" class="wa-select"><option>Bs</option><option>$us</option></select></div>
                            <div class="col-sm-5"><label class="wa-label" for="qVence">Fecha de vencimiento *</label><input id="qVence" name="fecha_vencimiento" type="date" class="wa-input" required></div>
                            <div class="col-12"><label class="wa-label" for="qObs">Observación</label><input id="qObs" name="observacion" class="wa-input" maxlength="500" placeholder="Para qué pagos usarlo"></div>
                        </div>
                        <div class="wa-help mt-2"><i class="ri-information-line"></i> Desde el día siguiente al vencimiento el QR deja de aparecer para los envíos.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="wa-btn wa-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="wa-btn wa-btn-primary" id="qrGuardar"><i class="ri-save-3-line"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const BASE = @json(url('admin/contable/qr'));
    const QRS = @json($data);
    let editId = null;
    let modal;
    const $ = (id) => document.getElementById(id);

    function preview(src) {
        $('qrPreview').innerHTML = src ? '<img src="' + src + '" alt="Vista previa del QR">'
            : '<span><i class="ri-image-add-line fs-3 d-block"></i>Elegí la imagen<br>(PNG, JPG o WEBP · máx. 5 MB)</span>';
    }

    window.abrirQr = function (id) {
        const q = id ? QRS[id] : null;
        editId = q ? q.id : null;
        $('qrModalTitle').textContent = q ? 'Editar QR' : 'Registrar QR';
        $('qrError').style.display = 'none';
        $('qrForm').reset();
        $('qNombre').value = q ? q.nombre || '' : '';
        $('qBanco').value = q ? q.banco || '' : '';
        $('qTitular').value = q ? q.titular || '' : '';
        $('qCuenta').value = q ? q.numero_cuenta || '' : '';
        $('qTipo').value = q ? q.tipo_cuenta || '' : '';
        $('qMoneda').value = q ? q.moneda || 'Bs' : 'Bs';
        $('qVence').value = q ? q.fecha_vencimiento : '';
        $('qObs').value = q ? q.observacion || '' : '';
        $('qrImagenHelp').textContent = q ? 'Elegí otra imagen solo si querés reemplazarla.' : '';
        preview(q ? q.imagen_url : null);
        modal = modal || new bootstrap.Modal($('qrModal'));
        modal.show();
    };

    $('qrImagen').addEventListener('change', function () {
        const f = this.files[0];
        if (f) preview(URL.createObjectURL(f));
    });

    async function api(url, method, body) {
        const res = await fetch(url, { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            throw new Error(data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'No se pudo completar la acción.'));
        }
        return data;
    }

    $('qrForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = $('qrGuardar');
        btn.disabled = true;
        try {
            await api(editId ? BASE + '/' + editId : BASE, 'POST', new FormData(this));
            location.reload();
        } catch (err) {
            $('qrError').textContent = err.message;
            $('qrError').style.display = '';
        } finally { btn.disabled = false; }
    });

    window.toggleQr = async function (id) {
        try { await api(BASE + '/' + id + '/activo', 'PATCH'); location.reload(); } catch (e) { alert(e.message); }
    };
    window.eliminarQr = async function (id, nombre) {
        if (!confirm('¿Eliminar el QR «' + nombre + '»? Ya no se podrá adjuntar en los envíos.')) return;
        try { await api(BASE + '/' + id, 'DELETE'); location.reload(); } catch (e) { alert(e.message); }
    };
})();
</script>
@endpush
