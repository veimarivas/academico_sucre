@extends('admin.layouts.master')

@section('title', 'Buscar Participante - ' . config('app.name'))

@push('css')
<style>
.search-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.search-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.search-hero > * { position: relative; z-index: 1; }
.search-hero h4 { color: #fff; font-weight: 700; margin: 0; }
.search-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; }

.search-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    margin-bottom: 1.25rem;
}
[data-layout-mode="dark"] .search-card { background: #1d2125; border-color: #2a2f34; }
.search-card .card-body { padding: 1.5rem; }

.search-input-wrap {
    max-width: 500px;
    margin: 0 auto;
}

.result-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .result-card { background: #1d2125; border-color: #2a2f34; }

.participante-header {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px 14px 0 0;
    padding: 1.25rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .participante-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.participante-avatar-lg {
    width: 60px; height: 60px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.4rem;
    background: linear-gradient(135deg, #f7b84b, #ff9f43);
    color: #fff;
    flex-shrink: 0;
}
.participante-body { padding: 1.25rem 1.5rem; }

.programa-item {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.programa-item:hover { box-shadow: 0 4px 14px -8px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .programa-item { border-color: #2a2f34; }
.programa-item-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: .85rem 1.1rem;
    background: linear-gradient(135deg, #f8f9fc, #eef1f8);
    border-bottom: 1px solid #eef0f4;
    gap: .75rem; flex-wrap: wrap;
}
[data-layout-mode="dark"] .programa-item-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.programa-item-header .left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
.programa-item-header .left i { color: #405189; font-size: 1.2rem; }
.programa-item-header .prog-name { font-weight: 700; color: #212529; font-size: .9rem; }
[data-layout-mode="dark"] .programa-item-header .prog-name { color: #fff; }
.programa-item-header .prog-meta { font-size: .72rem; color: #6c757d; display: flex; align-items: center; gap: .4rem; }

.debt-grid { padding: 1rem 1.1rem; display: flex; gap: .75rem; flex-wrap: wrap; }
.debt-stat {
    flex: 1; min-width: 140px;
    border: 1px solid #eef0f4; border-radius: 10px;
    padding: .65rem .85rem; text-align: center;
}
[data-layout-mode="dark"] .debt-stat { border-color: #2a2f34; }
.debt-stat .ds-label { font-size: .65rem; text-transform: uppercase; color: #878a99; font-weight: 700; letter-spacing: .4px; }
.debt-stat .ds-val { font-size: 1rem; font-weight: 800; }
.debt-stat.danger .ds-val { color: #f06548; }
.debt-stat.success .ds-val { color: #0ab39c; }
.debt-stat.warning .ds-val { color: #f7b84b; }

.venc-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: .25em .7em; border-radius: 999px;
    border: 1px solid currentColor;
}
.venc-badge.danger { background: #fef2f2; color: #f06548; border-color: #fecaca; }
.venc-badge.success { background: #ecfdf5; color: #0ab39c; border-color: #a7f3d8; }

.pago-modal .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(0,0,0,.25); }
.pago-modal .modal-header {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%);
    color: #fff; border: 0;
    padding: 1.1rem 1.5rem;
    position: relative; overflow: hidden;
}
.pago-modal .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
.pago-modal .modal-header > * { position: relative; z-index: 1; }
.pago-modal .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
}
.pago-modal .modal-title { color: #fff; font-weight: 700; }
.pago-modal .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
.pago-modal .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
.pago-modal .modal-body { padding: 1.5rem; background: #fafbfd; }
[data-layout-mode="dark"] .pago-modal .modal-body { background: #1a1d20; }
.pago-modal .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] .pago-modal .modal-footer { background: #212529; border-top-color: #2a2f34; }

.pg-toolbar {
    display: flex; gap: .75rem; flex-wrap: wrap;
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: .85rem 1rem;
    margin-bottom: 1rem;
    align-items: flex-end;
}
[data-layout-mode="dark"] .pg-toolbar { background: #1d2125; border-color: #2a2f34; }
.pg-grp { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 140px; }
.pg-grp label { font-size: .65rem; color: #878a99; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; margin: 0; }
.pg-grp input, .pg-grp select {
    border: 1px solid #e9ebec; border-radius: 8px;
    padding: .45rem .65rem; font-size: .9rem; font-weight: 600; background: #fff;
}
.pg-grp input:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .15rem rgba(10,179,156,.15); }
.pg-grp.monto input { font-size: 1.1rem; font-weight: 700; text-align: right; color: #0ab39c; }
[data-layout-mode="dark"] .pg-grp input, [data-layout-mode="dark"] .pg-grp select { background: #1d2125; border-color: #2a2f34; color: #ced4da; }
.pg-actions { display: flex; gap: .5rem; }
.btn-pg-action {
    border: 1px solid #e9ebec; background: #fff; color: #495057;
    border-radius: 8px; padding: .45rem .85rem;
    font-size: .78rem; font-weight: 700;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .25rem;
}
.btn-pg-action:hover { border-color: #405189; color: #405189; background: #f8f9fc; }

.pg-summary { display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1rem; }
.pg-stat {
    flex: 1; min-width: 160px;
    border: 1px solid var(--pg-border, #eef0f4);
    background: #fff; border-radius: 10px;
    padding: .65rem .85rem; text-align: center;
}
[data-layout-mode="dark"] .pg-stat { background: #1d2125; }
.pg-stat .pg-stat-lbl { font-size: .65rem; text-transform: uppercase; color: #878a99; font-weight: 700; letter-spacing: .4px; }
.pg-stat .pg-stat-val { font-size: 1.05rem; font-weight: 800; color: var(--pg-color, #212529); }

.pg-table { margin: 0; width: 100%; background: #fff; border: 1px solid #eef0f4; border-radius: 10px; overflow: hidden; }
[data-layout-mode="dark"] .pg-table { background: #1d2125; border-color: #2a2f34; }
.pg-table thead th {
    background: #f8f9fc !important;
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    padding: .55rem .65rem;
    border-bottom: 2px solid #eef0f4;
}
.pg-table tbody td {
    padding: .55rem .65rem;
    font-size: .82rem;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
}
[data-layout-mode="dark"] .pg-table thead th { background: #1d2125 !important; color: #adb5bd; }
.pg-table tbody tr.is-affected {
    background: linear-gradient(90deg, rgba(10,179,156,.18) 0%, rgba(10,179,156,.06) 100%);
    box-shadow: inset 4px 0 0 0 #0ab39c;
}
.pg-table tbody tr.is-venc { background: linear-gradient(90deg, rgba(240,101,72,.07), rgba(240,101,72,0)); }
.pg-input-monto {
    border: 1px solid #e9ebec; border-radius: 6px;
    padding: .25rem .5rem; font-size: .82rem; font-weight: 700;
    width: 110px; text-align: right; color: #0ab39c;
}
.pg-input-monto:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .12rem rgba(10,179,156,.15); }
.pg-input-monto.is-invalid { border-color: #f06548; background: #fff5f5; }
#pgMontoTotal.is-invalid { border-color: #f06548; box-shadow: 0 0 0 .15rem rgba(240,101,72,.15); }

.search-spinner { display: none; }
.search-spinner.show { display: inline-block; }

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}
.empty-state .empty-icon {
    width: 80px; height: 80px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #f8f9fc;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #adb5bd;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="search-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon" style="width:56px;height:56px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);border-radius:14px;font-size:1.6rem;">
                    <i class="ri-user-search-line"></i>
                </div>
                <div>
                    <h4>Buscar Participante</h4>
                    <p class="hero-sub">Busca por CI para ver sus programas, deudas y realizar pagos</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="search-card">
    <div class="card-body">
        <div class="search-input-wrap">
            <label class="form-label fw-semibold text-muted small text-uppercase">Carnet de Identidad</label>
            <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="ri-passport-line"></i></span>
                <input type="text" id="ci-search" class="form-control" placeholder="Ingresa el CI (mín. 7 dígitos)" maxlength="13" autocomplete="off">
                <span class="input-group-text search-spinner" id="search-spinner"><span class="spinner-border spinner-border-sm"></span></span>
            </div>
            <div id="search-feedback" class="mt-2"></div>
        </div>
    </div>
</div>

<div class="result-card" id="result-container" style="display:none;">
    <div class="participante-header" id="participante-header"></div>
    <div class="participante-body" id="participante-body"></div>
</div>

<div class="empty-state" id="empty-state">
    <div class="empty-icon"><i class="ri-user-search-line"></i></div>
    <h6 class="text-muted fw-semibold">Busca un participante por su CI</h6>
    <p class="text-muted small">Los resultados aparecerán aquí con sus programas, deudas y opción de pago</p>
</div>

@can('contable.cobrar')
{{-- Pago General Modal --}}
<div class="modal fade pago-modal" id="modalPagoGeneral" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-wallet-3-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Pago General</h5>
                        <small class="modal-subtitle" id="pg-subtitle">Distribuye un monto entre las cuotas pendientes</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pg-toolbar">
                    <div class="pg-grp monto">
                        <label>Monto total a pagar (Bs)</label>
                        <input type="number" step="0.01" min="0.01" id="pgMontoTotal" placeholder="0.00">
                    </div>
                    <div class="pg-grp">
                        <label>Fecha de pago</label>
                        <input type="date" id="pgFecha" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="pg-actions">
                        <button type="button" class="btn-pg-action" id="btnPgLimpiar">
                            <i class="ri-eraser-line"></i> Limpiar
                        </button>
                    </div>
                </div>
                <div class="pg-summary">
                    <div class="pg-stat" style="--pg-color:#405189;--pg-border:#c3cfe2;">
                        <div class="pg-stat-lbl">Cuotas Pendientes</div>
                        <div class="pg-stat-val" id="pgCountPend">0</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#f06548;--pg-border:#fecaca;">
                        <div class="pg-stat-lbl">Total Saldo</div>
                        <div class="pg-stat-val" id="pgTotalSaldo">Bs 0.00</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#0ab39c;--pg-border:#a7f3d8;">
                        <div class="pg-stat-lbl">A Aplicar</div>
                        <div class="pg-stat-val" id="pgAplicar">Bs 0.00</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#299cdb;--pg-border:#bae6fd;">
                        <div class="pg-stat-lbl">Queda Disponible</div>
                        <div class="pg-stat-val" id="pgQueda">Bs 0.00</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="pg-table">
                        <thead>
                            <tr>
                                <th style="width:45px;" class="text-center">N°</th>
                                <th>Tipo</th>
                                <th style="width:140px;">Vencimiento</th>
                                <th class="text-end" style="width:110px;">Cuota</th>
                                <th class="text-end" style="width:110px;">Pagado</th>
                                <th class="text-end" style="width:110px;">Saldo</th>
                                <th class="text-end" style="width:130px;">A Pagar</th>
                            </tr>
                        </thead>
                        <tbody id="pgTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarPagoGeneral" disabled>
                    <i class="ri-wallet-3-line align-bottom"></i> <span id="btnPgTxt">Registrar Pago</span>
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Confirmación Pago General --}}
<div class="modal fade pago-modal" id="modalConfirmarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-shield-check-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Confirmar Pago</h5>
                        <small class="modal-subtitle" style="color:rgba(255,255,255,.8);">Revisa los datos antes de confirmar</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;">
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <div style="display:flex;align-items:center;gap:.85rem;background:#f8f9fc;border:1px solid #eef0f4;border-radius:12px;padding:.85rem 1rem;">
                        <span style="width:38px;height:38px;border-radius:10px;background:#eef1f8;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#405189;flex-shrink:0;"><i class="ri-stack-line"></i></span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.7rem;color:#878a99;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">Cuotas a pagar</div>
                            <div style="font-size:1.05rem;font-weight:800;color:#212529;line-height:1.2;" id="confirmCuotas">0</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.85rem;background:#f0fdf9;border:1px solid #a7f3d8;border-radius:12px;padding:.85rem 1rem;">
                        <span style="width:38px;height:38px;border-radius:10px;background:#d1fae5;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#0ab39c;flex-shrink:0;"><i class="ri-money-dollar-circle-line"></i></span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.7rem;color:#878a99;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">Total a aplicar</div>
                            <div style="font-size:1.15rem;font-weight:800;color:#0ab39c;line-height:1.2;" id="confirmTotal">Bs 0.00</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.85rem;background:#f0f4ff;border:1px solid #c3ddfa;border-radius:12px;padding:.85rem 1rem;">
                        <span style="width:38px;height:38px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#299cdb;flex-shrink:0;"><i class="ri-calendar-check-line"></i></span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.7rem;color:#878a99;font-weight:700;text-transform:uppercase;letter-spacing:.3px;">Fecha de pago</div>
                            <div style="font-size:.9rem;font-weight:700;color:#212529;line-height:1.2;" id="confirmFecha">—</div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:.85rem;padding:.65rem .85rem;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;display:flex;align-items:flex-start;gap:.55rem;">
                    <i class="ri-alert-line" style="color:#d97706;font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
                    <span style="font-size:.75rem;color:#92400e;font-weight:600;">Esta acción no se puede deshacer automáticamente. Verifica que los montos sean correctos.</span>
                </div>
            </div>
            <div class="modal-footer" style="padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-weight:600;padding:.5rem 1.1rem;">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn" id="btnConfirmarPago" style="border-radius:8px;font-weight:700;padding:.5rem 1.3rem;background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);transition:all .15s;">
                    <i class="ri-check-double-line"></i> Sí, registrar pago
                </button>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('js')
<script>
const CAN_COBRAR = {{ $canCobrar ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function () {
    const ciInput = document.getElementById('ci-search');
    const spinner = document.getElementById('search-spinner');
    const feedback = document.getElementById('search-feedback');
    const resultContainer = document.getElementById('result-container');
    const participanteHeader = document.getElementById('participante-header');
    const participanteBody = document.getElementById('participante-body');
    const emptyState = document.getElementById('empty-state');

    let searchTimer = null;

    const diasEs = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    function fechaTxt(f) {
        const d = new Date(f + 'T00:00:00');
        if (isNaN(d)) return f;
        return diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()] + ' del ' + d.getFullYear();
    }

    function fmt(n) {
        return 'Bs ' + Number(n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function showToast(msg, type) {
        type = type || 'success';
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        const colors = { success: '#0ab39c', danger: '#f06548', warning: '#f7b84b', info: '#299cdb' };
        const icons = { success: 'ri-checkbox-circle-line', danger: 'ri-error-warning-line', warning: 'ri-alert-line', info: 'ri-information-line' };
        const el = document.createElement('div');
        el.className = 'toast align-items-center text-white border-0 show';
        el.style.background = colors[type] || colors.success;
        el.style.minWidth = '280px';
        el.innerHTML = '<div class="d-flex align-items-center"><div class="toast-body d-flex align-items-center gap-2 fs-13 fw-semibold"><i class="' + (icons[type] || icons.success) + ' fs-5"></i><span>' + msg + '</span></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        try { const inst = new bootstrap.Toast(el, { delay: 3500 }); inst.show(); el.addEventListener('hidden.bs.toast', () => el.remove()); }
        catch (e) { setTimeout(() => el.remove(), 3500); }
    }

                    function renderParticipante(data) {
        const p = data.participante;
        const programas = data.programas;
        const canCobrar = data.can_cobrar !== undefined ? data.can_cobrar : CAN_COBRAR;
        const ini = (p.nombres.charAt(0) + p.apellidos.charAt(0)).toUpperCase();

        participanteHeader.innerHTML = `
            <div class="participante-avatar-lg">${ini}</div>
            <div style="min-width:0;">
                <h5 class="fw-bold mb-1 text-dark">${p.apellidos}, ${p.nombres}</h5>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-soft-primary text-primary"><i class="ri-passport-line align-bottom me-1"></i>CI: ${p.ci}</span>
                    ${p.celular ? '<span class="badge bg-soft-info text-info"><i class="ri-phone-line align-bottom me-1"></i>' + p.celular + '</span>' : ''}
                    ${p.correo ? '<span class="badge bg-soft-secondary text-secondary"><i class="ri-mail-line align-bottom me-1"></i>' + p.correo + '</span>' : ''}
                </div>
            </div>
        `;

        function moneyStack(bruto, desc, pagado) {
            const neto = Math.max(0, bruto - desc);
            const saldo = Math.max(0, neto - pagado);
            let h = '';
            if (desc > 0) {
                h += '<div style="font-size:.62rem;color:#878a99;text-decoration:line-through;">' + fmt(bruto) + '</div>';
                h += '<div style="font-size:.62rem;color:#878a99;"><i class="ri-discount-percent-line"></i> Dto ' + fmt(desc) + '</div>';
            }
            h += '<div style="font-weight:700;">' + fmt(neto) + '</div>';
            if (pagado > 0) {
                h += '<div style="font-size:.65rem;color:#0ab39c;"><i class="ri-check-line"></i> Pag ' + fmt(pagado) + '</div>';
            }
            if (saldo > 0) {
                h += '<div style="font-size:.65rem;color:#f06548;"><i class="ri-time-line"></i> Saldo ' + fmt(saldo) + '</div>';
            } else if (neto > 0) {
                h += '<div style="font-size:.65rem;color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Cancelado</div>';
            } else {
                h += '<div style="font-size:.65rem;color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Sin deuda</div>';
            }
            return h;
        }

        if (programas.length === 0) {
            participanteBody.innerHTML = '<div class="text-center text-muted py-3"><i class="ri-inbox-2-line fs-3 d-block mb-2"></i>No tiene programas inscritos</div>';
        } else {
            let html = '<h6 class="fw-bold mb-3"><i class="ri-graduation-cap-line align-bottom me-1"></i>Programas (' + programas.length + ')</h6>';
            programas.forEach(function (prog, idx) {
                const tieneVen = prog.tiene_vencidas;
                const netoMat = Math.max(0, prog.matricula - prog.descuento_matricula);
                const netoCol = Math.max(0, prog.monto_programa - prog.descuento_colegiatura);
                html += `
                <div class="programa-item" data-prog-idx="${idx}">
                    <div class="programa-item-header">
                        <div class="left">
                            <i class="ri-booklet-line"></i>
                            <div>
                                <div class="prog-name">${prog.programa_nombre}</div>
                                <div class="prog-meta">
                                    <span><i class="ri-calendar-line"></i> ${prog.gestion}</span>
                                    <span>·</span>
                                    <span>${prog.tipo_nombre || '—'}</span>
                                    ${prog.contado ? '<span class="badge bg-soft-primary text-primary" style="font-size:.6rem;">Contado</span>' : ''}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            ${tieneVen ? '<span class="venc-badge danger"><i class="ri-error-warning-line"></i> Vencidas</span>' : '<span class="venc-badge success"><i class="ri-check-line"></i> Al día</span>'}
                            ${prog.total_deuda > 0 ? (canCobrar ? '<button type="button" class="btn btn-sm btn-success btn-pago-general-buscar" data-prog-idx="' + idx + '"><i class="ri-money-dollar-circle-line"></i> Pago General</button>' : '') : '<span class="badge bg-soft-success text-success"><i class="ri-checkbox-circle-line"></i> Sin deudas pendientes</span>'}
                        </div>
                    </div>
                    <div class="debt-grid">
                        <div class="debt-stat" style="flex:1 1 220px;">
                            <div class="ds-label" style="color:#299cdb;"><i class="ri-bank-card-line"></i> Matrícula</div>
                            <div class="ds-val" style="font-size:.85rem;line-height:1.4;">${moneyStack(prog.matricula, prog.descuento_matricula, prog.total_pagado_matricula)}</div>
                        </div>
                        <div class="debt-stat" style="flex:1 1 220px;">
                            <div class="ds-label" style="color:#0ab39c;"><i class="ri-funds-line"></i> Colegiatura</div>
                            <div class="ds-val" style="font-size:.85rem;line-height:1.4;">${moneyStack(prog.monto_programa, prog.descuento_colegiatura, prog.total_pagado_colegiatura)}</div>
                        </div>
                        <div class="debt-stat ${prog.total_deuda > 0 ? 'danger' : 'success'}" style="flex:0 1 160px;">
                            <div class="ds-label">Deuda Total</div>
                            <div class="ds-val" style="font-size:.9rem;">${fmt(prog.total_deuda)}</div>
                        </div>
                    </div>
                </div>`;
            });
            participanteBody.innerHTML = html;

            // Store cuotas data for payment modal
            programas.forEach(function (prog, idx) {
                const item = document.querySelector('.programa-item[data-prog-idx="' + idx + '"]');
                if (item) item.dataset.cuotas = JSON.stringify(prog.cuotas);
            });
        }

        resultContainer.style.display = '';
        emptyState.style.display = 'none';
    }

    ciInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const ci = this.value.trim();
        feedback.innerHTML = '';
        resultContainer.style.display = 'none';
        emptyState.style.display = '';

        if (!ci) {
            emptyState.style.display = '';
            return;
        }
        if (ci.length < 7) {
            feedback.innerHTML = '<small class="text-muted"><i class="ri-information-line align-bottom me-1"></i>Mínimo 7 caracteres</small>';
            emptyState.style.display = '';
            return;
        }
        spinner.classList.add('show');
        searchTimer = setTimeout(function () {
            fetch('{{ route("admin.participantes.buscar") }}?ci=' + encodeURIComponent(ci), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                spinner.classList.remove('show');
                if (data.found) {
                    renderParticipante(data);
                } else {
                    feedback.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0"><i class="ri-error-warning-line align-bottom me-1"></i>No se encontró un participante con ese CI</div>';
                    resultContainer.style.display = 'none';
                    emptyState.style.display = 'none';
                }
            })
            .catch(function () {
                spinner.classList.remove('show');
                feedback.innerHTML = '<div class="text-danger small">Error al buscar</div>';
            });
        }, 400);
    });

    // ===== PAGO GENERAL (reused from participante-detalle) =====
    const modalPagoGeneral = new bootstrap.Modal(document.getElementById('modalPagoGeneral'));
    let pgCuotas = [];

    function fechaSimple(iso) {
        if (!iso) return '<span class="text-muted fst-italic">Sin fecha</span>';
        const d = new Date(iso + 'T00:00:00');
        if (isNaN(d)) return iso;
        return d.getDate() + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
    }

    function tipoLabel(t) { return t === 'Matricula' ? 'Matrícula' : 'Mensualidad'; }

    function vencInfo(iso) {
        if (!iso) return { html: '<span class="text-muted fst-italic">—</span>', dias: null, isVenc: false };
        const today = new Date(); today.setHours(0,0,0,0);
        const d = new Date(iso + 'T00:00:00');
        const diff = Math.round((d - today) / 86400000);
        if (diff < 0) return { html: '<span class="due-pill is-venc" style="font-size:.7rem;font-weight:700;padding:.2em .6em;border-radius:999px;border:1px solid;background:#fef2f2;color:#f06548;border-color:#fecaca;"><i class="ri-error-warning-line"></i> Vencido ' + Math.abs(diff) + 'd</span>', dias: diff, isVenc: true };
        if (diff === 0) return { html: '<span class="due-pill is-hoy" style="font-size:.7rem;font-weight:700;padding:.2em .6em;border-radius:999px;border:1px solid;background:#fffbeb;color:#f7b84b;border-color:#fde68a;"><i class="ri-alarm-warning-line"></i> Hoy</span>', dias: 0, isVenc: false };
        const cls = diff <= 7 ? 'is-prox' : 'is-futuro';
        const c = diff <= 7 ? '#f7b84b' : '#0ab39c';
        const bg = diff <= 7 ? '#fffbeb' : '#ecfdf5';
        const bc = diff <= 7 ? '#fde68a' : '#a7f3d8';
        return { html: '<span class="due-pill" style="font-size:.7rem;font-weight:700;padding:.2em .6em;border-radius:999px;border:1px solid;background:' + bg + ';color:' + c + ';border-color:' + bc + ';"><i class="ri-time-line"></i> En ' + diff + 'd</span>', dias: diff, isVenc: false };
    }

    function renderPgTable() {
        const tbody = document.getElementById('pgTableBody');
        if (!pgCuotas.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No hay cuotas pendientes</td></tr>';
            return;
        }
        let html = '';
        pgCuotas.forEach(function (c, i) {
            const venc = vencInfo(c.fecha_pago);
            const rowCls = c.a_pagar > 0 ? 'is-affected' : (venc.isVenc ? 'is-venc' : '');
            html +=
                '<tr class="' + rowCls + '" data-cidx="' + i + '">' +
                    '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                    '<td><div class="fw-semibold">' + tipoLabel(c.tipo) + '</div></td>' +
                    '<td><div>' + fechaSimple(c.fecha_pago) + '</div><div style="margin-top:2px;">' + venc.html + '</div></td>' +
                    '<td class="text-end fw-semibold">' + fmt(c.total_cuota) + '</td>' +
                    '<td class="text-end text-success">' + fmt(c.pagado) + '</td>' +
                    '<td class="text-end fw-bold text-danger">' + fmt(c.saldo) + '</td>' +
                    '<td class="text-end"><input type="number" step="0.01" min="0" max="' + c.saldo + '" class="pg-input-monto" data-cidx="' + i + '" value="' + (c.a_pagar > 0 ? c.a_pagar.toFixed(2) : '') + '" placeholder="0.00"></td>' +
                '</tr>';
        });
        tbody.innerHTML = html;
        tbody.querySelectorAll('.pg-input-monto').forEach(function (inp) {
            inp.addEventListener('input', function () {
                const i = parseInt(this.dataset.cidx);
                let v = parseFloat(this.value) || 0;
                if (v > pgCuotas[i].saldo) { v = pgCuotas[i].saldo; this.value = v.toFixed(2); }
                pgCuotas[i].a_pagar = v;
                const tr = this.closest('tr');
                tr.classList.toggle('is-affected', v > 0);
                actualizarTotales();
            });
        });
    }

    function actualizarTotales() {
        const total = pgCuotas.reduce((s, c) => s + c.saldo, 0);
        const aplicar = pgCuotas.reduce((s, c) => s + c.a_pagar, 0);
        const monto = parseFloat(document.getElementById('pgMontoTotal').value) || 0;
        const queda = monto - aplicar;
        document.getElementById('pgCountPend').textContent = pgCuotas.length;
        document.getElementById('pgTotalSaldo').textContent = fmt(total);
        document.getElementById('pgAplicar').textContent = fmt(aplicar);
        const quedaEl = document.getElementById('pgQueda');
        quedaEl.textContent = fmt(queda);
        quedaEl.style.color = queda < 0 ? '#f06548' : (queda > 0 ? '#299cdb' : '#0ab39c');

        const montoInp = document.getElementById('pgMontoTotal');
        const montoFeedback = document.getElementById('pgMontoFeedback') || (function() {
            const el = document.createElement('small');
            el.id = 'pgMontoFeedback';
            el.className = 'form-text';
            montoInp.parentElement.appendChild(el);
            return el;
        })();
        montoInp.classList.remove('is-invalid');
        if (monto > total) {
            montoInp.classList.add('is-invalid');
            montoFeedback.textContent = 'El monto (' + fmt(monto) + ') supera la deuda total (' + fmt(total) + ')';
            montoFeedback.className = 'form-text text-danger';
        } else if (monto > 0 && aplicar > 0 && Math.abs(queda) > 0.01) {
            montoFeedback.textContent = 'Queda ' + fmt(queda) + ' sin distribuir';
            montoFeedback.className = 'form-text text-warning';
        } else if (monto <= 0 && aplicar <= 0) {
            montoFeedback.textContent = '';
            montoFeedback.className = 'form-text';
        } else {
            montoFeedback.textContent = '';
            montoFeedback.className = 'form-text';
        }

        let inputsValidos = true;
        document.querySelectorAll('.pg-input-monto').forEach(function (inp) {
            const i = parseInt(inp.dataset.cidx);
            const val = parseFloat(inp.value) || 0;
            inp.classList.remove('is-invalid');
            if (val < 0 || val > pgCuotas[i].saldo) { inp.classList.add('is-invalid'); inputsValidos = false; }
        });

        const btn = document.getElementById('btnGuardarPagoGeneral');
        const count = pgCuotas.filter(c => c.a_pagar > 0).length;
        btn.disabled = aplicar <= 0 || !inputsValidos || monto > total;
        document.getElementById('btnPgTxt').textContent = count > 0 ? 'Registrar Pago en ' + count + ' cuota(s)' : 'Registrar Pago';
    }

    function distribuirAuto() {
        const monto = parseFloat(document.getElementById('pgMontoTotal').value) || 0;
        const total = pgCuotas.reduce((s, c) => s + c.saldo, 0);
        if (monto <= 0) {
            pgCuotas.forEach(c => c.a_pagar = 0);
            renderPgTable();
            actualizarTotales();
            return;
        }
        if (monto > total) { actualizarTotales(); return; }
        let restante = monto;
        pgCuotas.forEach(function (c) {
            if (restante <= 0) { c.a_pagar = 0; return; }
            const ap = Math.min(c.saldo, restante);
            c.a_pagar = Math.round(ap * 100) / 100;
            restante -= ap;
        });
        renderPgTable();
        actualizarTotales();
    }

    document.getElementById('pgMontoTotal').addEventListener('input', distribuirAuto);
    document.getElementById('btnPgLimpiar').addEventListener('click', function () {
        pgCuotas.forEach(c => c.a_pagar = 0);
        document.getElementById('pgMontoTotal').value = '';
        renderPgTable();
        actualizarTotales();
    });

    // Open Pago General from search results
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-pago-general-buscar');
        if (!btn) return;
        const progIdx = parseInt(btn.dataset.progIdx);
        const item = document.querySelector('.programa-item[data-prog-idx="' + progIdx + '"]');
        if (!item) return;
        let cuotas = [];
        try { cuotas = JSON.parse(item.dataset.cuotas || '[]'); } catch (e) { cuotas = []; }
        pgCuotas = cuotas.filter(c => c.saldo > 0).map(c => ({ ...c, a_pagar: 0 }));
        pgCuotas.sort(function (a, b) {
            if (!a.fecha_pago && b.fecha_pago) return 1;
            if (a.fecha_pago && !b.fecha_pago) return -1;
            if (!a.fecha_pago && !b.fecha_pago) return 0;
            return a.fecha_pago < b.fecha_pago ? -1 : 1;
        });
        const progName = item.querySelector('.prog-name')?.textContent || '';
        document.getElementById('pg-subtitle').textContent = progName ? progName + ' — Asigna montos a las cuotas pendientes' : 'Asigna montos a las cuotas pendientes';
        document.getElementById('pgMontoTotal').value = '';
        renderPgTable();
        actualizarTotales();
        modalPagoGeneral.show();
    });

    const modalConfirmarPago = new bootstrap.Modal(document.getElementById('modalConfirmarPago'));

    // Submit pago general
    document.getElementById('btnGuardarPagoGeneral').addEventListener('click', function () {
        const fecha = document.getElementById('pgFecha').value;
        const aplicaciones = pgCuotas.filter(c => c.a_pagar > 0).map(c => ({ cuota_id: c.id, monto: c.a_pagar }));
        if (aplicaciones.length === 0) { showToast('No hay cuotas con monto a pagar', 'warning'); return; }

        const totalAp = aplicaciones.reduce((s, a) => s + a.monto, 0);
        document.getElementById('confirmCuotas').textContent = aplicaciones.length + ' cuota(s)';
        document.getElementById('confirmTotal').textContent = fmt(totalAp);
        document.getElementById('confirmFecha').textContent = fechaTxt(fecha);
        document.getElementById('btnConfirmarPago').dataset.aplicaciones = JSON.stringify(aplicaciones);
        document.getElementById('btnConfirmarPago').dataset.fecha = fecha;
        modalPagoGeneral.hide();
        modalConfirmarPago.show();
    });

    document.getElementById('btnConfirmarPago').addEventListener('click', function () {
        const fecha = this.dataset.fecha;
        const aplicaciones = JSON.parse(this.dataset.aplicaciones || '[]');
        if (aplicaciones.length === 0) return;

        const btn = this;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

        fetch('{{ route("admin.programas.pago-multiple") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ fecha: fecha, aplicaciones: aplicaciones }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            modalConfirmarPago.hide();
            if (ok && data.success) {
                showToast(data.message + ' · ' + fmt(data.total_aplicado) + ' en ' + data.cuotas_afectadas + ' cuota(s)', 'success');
                const ci = ciInput.value.trim();
                if (ci) { ciInput.dispatchEvent(new Event('input')); }
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => { modalConfirmarPago.hide(); showToast('Error de conexión', 'danger'); })
        .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
    });
});
</script>
@endpush
