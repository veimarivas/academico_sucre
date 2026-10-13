@push('css')
<style>
/* ============================================================
   Seguridad — sistema de diseño (Roles / Permisos / Usuarios)
   ============================================================ */

/* Hero */
.sec-hero { position:relative; background: linear-gradient(120deg, #0e2f52 0%, #1c486c 50%, #0a6a90 100%); border-radius:20px; padding:1.7rem 1.9rem; color:#fff; overflow:hidden; box-shadow:0 22px 45px -26px rgba(14,47,82,.85); margin-bottom:1.5rem; }
.sec-hero::before { content:""; position:absolute; top:-70px; right:-40px; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.16) 0%, transparent 70%); border-radius:50%; }
.sec-hero::after { content:""; position:absolute; bottom:-80px; left:26%; width:300px; height:300px; background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%); border-radius:50%; }
.sec-hero > * { position:relative; z-index:1; }
.sec-hero .hero-icon { width:62px; height:62px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.28); border-radius:18px; font-size:1.8rem; backdrop-filter: blur(6px); box-shadow:0 10px 24px -14px rgba(0,0,0,.5); }
.sec-hero h4 { color:#fff; font-weight:800; letter-spacing:-.02em; margin:0; }
.sec-hero .hero-sub { color: rgba(255,255,255,.82); font-size:.88rem; margin:3px 0 0; }
.sec-hero .breadcrumb { background:transparent; padding:0; margin:0; }
.sec-hero .breadcrumb-item, .sec-hero .breadcrumb-item a { color: rgba(255,255,255,.8); }
.sec-hero .breadcrumb-item.active { color:#fff; font-weight:600; }
.sec-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.45); }
.sec-hero-badges { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.7rem; }
.sec-hero-badge { display:inline-flex; align-items:center; gap:6px; font-size:.72rem; font-weight:700; padding:.3em .8em; border-radius:999px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; }

/* KPIs */
.sec-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.sec-kpi { position:relative; background:#fff; border:1px solid #eef0f4; border-radius:16px; padding:1rem 1.1rem; overflow:hidden; transition:transform .18s ease, box-shadow .18s ease; }
.sec-kpi::before { content:""; position:absolute; top:0; left:0; bottom:0; width:4px; background:var(--k,#405189); }
.sec-kpi:hover { transform:translateY(-3px); box-shadow:0 16px 32px -22px rgba(16,24,40,.55); }
.sec-kpi .lbl { font-size:.64rem; text-transform:uppercase; letter-spacing:.45px; color:#98a2b3; font-weight:800; }
.sec-kpi .val { font-size:1.5rem; font-weight:800; color:#1b2537; line-height:1.05; margin-top:.15rem; font-variant-numeric: tabular-nums; }
.sec-kpi .sub { font-size:.72rem; color:#98a2b3; }
.sec-kpi .ic { width:42px; height:42px; border-radius:13px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
[data-layout-mode="dark"] .sec-kpi { background:#1d2125; border-color:#2a2f34; }
[data-layout-mode="dark"] .sec-kpi .val { color:#fff; }

/* Cards */
.sec-card { background:#fff; border:1px solid #eef0f4; border-radius:16px; box-shadow:0 10px 30px -22px rgba(16,24,40,.5); overflow:hidden; }
[data-layout-mode="dark"] .sec-card { background:#1d2125; border-color:#2a2f34; }
.sec-card .card-header { background:transparent; border-bottom:1px solid #f1f3f5; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
[data-layout-mode="dark"] .sec-card .card-header { border-bottom-color:#2a2f34; }
.sec-card .card-header h5 { font-size:.86rem; font-weight:800; letter-spacing:.01em; color:#1b2537; margin:0; display:flex; align-items:center; gap:.5rem; }
[data-layout-mode="dark"] .sec-card .card-header h5 { color:#fff; }
.sec-card .card-header h5 i { color:#405189; font-size:1.05rem; }
.sec-card .card-body { padding:1.1rem 1.25rem; }

.btn-add { display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#405189,#0a6a90); color:#fff !important; font-weight:700; font-size:.8rem; padding:.55rem 1.05rem; border-radius:11px; border:0; transition:all .2s ease; box-shadow:0 10px 22px -14px rgba(64,81,137,.8); }
.btn-add:hover { transform:translateY(-1px); box-shadow:0 14px 26px -14px rgba(64,81,137,.9); }

/* Búsqueda */
.sec-search { position:relative; margin-bottom:1.15rem; }
.sec-search input { padding-left:2.6rem; border-radius:12px; border:1px solid #e9ebec; font-size:.85rem; font-weight:600; height:44px; }
.sec-search input:focus { border-color:#299cdb; box-shadow:0 0 0 .2rem rgba(41,156,219,.12); }
.sec-search .search-icon { position:absolute; top:50%; left:.9rem; transform:translateY(-50%); color:#98a2b3; font-size:1.1rem; pointer-events:none; }

/* Roles */
.role-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:1rem; }
.role-card { background:#fff; border:1px solid #eef0f4; border-radius:16px; padding:1.1rem 1.2rem; transition:all .18s ease; display:flex; flex-direction:column; gap:.85rem; }
.role-card:hover { border-color:#d7dfec; box-shadow:0 16px 32px -20px rgba(16,24,40,.5); transform:translateY(-2px); }
[data-layout-mode="dark"] .role-card { background:#1d2125; border-color:#2a2f34; }
.role-card-top { display:flex; align-items:center; justify-content:space-between; gap:.75rem; }
.role-id { display:flex; align-items:center; gap:.65rem; min-width:0; }
.role-avatar { width:44px; height:44px; border-radius:13px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; color:#fff; background:linear-gradient(135deg,#405189,#5e72b8); flex-shrink:0; }
.role-avatar.super { background:linear-gradient(135deg,#c0392b,#f06548); }
.role-name { font-weight:800; color:#1b2537; font-size:.92rem; line-height:1.15; }
[data-layout-mode="dark"] .role-name { color:#fff; }
.role-meta { font-size:.7rem; color:#98a2b3; }
.role-actions { display:flex; gap:.35rem; flex-shrink:0; }
.role-coverage { display:flex; align-items:center; gap:.6rem; }
.role-coverage .bar { flex:1; height:7px; border-radius:999px; background:#eef1f8; overflow:hidden; }
.role-coverage .bar > span { display:block; height:100%; background:linear-gradient(90deg,#0a6a90,#0ab39c); }
.role-coverage .pct { font-size:.7rem; font-weight:800; color:#405189; min-width:34px; text-align:right; }
.role-perms { display:flex; flex-wrap:wrap; gap:.3rem; }

.role-chip { display:inline-flex; align-items:center; gap:5px; font-size:.7rem; font-weight:700; letter-spacing:.1px; padding:.26em .7em; border-radius:999px; background:#475467; color:#fff; border:1px solid #475467; max-width:100%; white-space:normal; overflow-wrap:anywhere; line-height:1.3; margin:2px 3px 2px 0; vertical-align:middle; }
.role-chip i { flex-shrink:0; font-size:.82rem; }
.role-chip.super { background:linear-gradient(135deg,#c0392b,#f06548); color:#fff; border-color:#f06548; }
.role-chip.muted { background:#eef2f6; color:#475467; border-color:#e0e6f0; }
.perm-chip { display:inline-flex; align-items:center; gap:4px; font-size:.66rem; font-weight:700; padding:.2em .55em; border-radius:7px; background:#eef1f8; color:#405189; border:1px solid #e0e6f2; max-width:100%; white-space:normal; overflow-wrap:anywhere; line-height:1.3; }

/* Tabla usuarios */
.sec-table-wrap { border:1px solid #eef0f4; border-radius:14px; overflow:visible; }
[data-layout-mode="dark"] .sec-table-wrap { border-color:#2a2f34; }

/* Estado */
.estado-pill { display:inline-flex; align-items:center; gap:6px; font-size:.72rem; font-weight:700; padding:.3em .7em; border-radius:999px; border:1px solid; white-space:nowrap; max-width:100%; overflow-wrap:anywhere; }
.estado-pill .dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.estado-pill.on { background:#e6f7ee; color:#067647; border-color:#b7e8d6; }
.estado-pill.on .dot { background:#12b76a; box-shadow:0 0 0 3px rgba(18,183,106,.18); }
.estado-pill.recent { background:#fff4d6; color:#8a6100; border-color:#fbe3a3; }
.estado-pill.recent .dot { background:#f59e0b; }
.estado-pill.none { background:#fee4e2; color:#b42318; border-color:#fbc9c4; }
.estado-pill.none .dot { background:#f06548; }
.sec-table { width:100%; table-layout:fixed; border-collapse:separate; border-spacing:0; }
.sec-table th { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#5d6478; font-weight:800; padding:.8rem .85rem; background:#f8fafc; border-bottom:1px solid #e6eaf0; }
[data-layout-mode="dark"] .sec-table th { background:#1a1d20; border-bottom-color:#2a2f34; color:#adb5bd; }
.sec-table td { padding:.75rem .85rem; border-bottom:1px solid #f1f3f5; font-size:.82rem; vertical-align:middle; white-space:normal; word-break:break-word; }
[data-layout-mode="dark"] .sec-table td { border-bottom-color:#2a2f34; color:#dee2e6; }
.sec-table tbody tr { transition:background-color .15s ease; }
.sec-table tbody tr:hover { background:#f8fafc; }
[data-layout-mode="dark"] .sec-table tbody tr:hover { background:#1a1d20; }
.sec-table tbody tr:last-child td { border-bottom:0; }
.sec-table thead th:first-child { border-top-left-radius:13px; }
.sec-table thead th:last-child { border-top-right-radius:13px; }
.sec-table tbody tr:last-child td:first-child { border-bottom-left-radius:13px; }
.sec-table tbody tr:last-child td:last-child { border-bottom-right-radius:13px; }
.sec-table td .role-chip,
.sec-table td .badge { max-width:100%; white-space:normal; overflow-wrap:anywhere; line-height:1.35; }
.sec-table td > .role-chip { margin:1px 0; }
.sec-table td .badge { display:inline-flex; align-items:center; }

.user-row { display:flex; align-items:center; gap:.7rem; min-width:0; }
.user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#405189,#0a6a90); color:#fff; font-weight:800; font-size:.82rem; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.user-name { font-weight:700; color:#1b2537; font-size:.86rem; line-height:1.25; overflow-wrap:anywhere; }
[data-layout-mode="dark"] .user-name { color:#fff; }
.user-sub { font-size:.7rem; color:#98a2b3; }

/* Tabs por rol */
.user-tabs { display:flex; flex-wrap:wrap; gap:.4rem; margin:0 0 1rem; padding:0; list-style:none; }
.user-tab { display:inline-flex; align-items:center; gap:6px; font-size:.78rem; font-weight:700; color:#475467; background:#fff; border:1px solid #e6eaf0; border-radius:999px; padding:.4rem .85rem; cursor:pointer; transition:all .16s ease; }
.user-tab:hover { border-color:#c3cfe2; color:#405189; }
.user-tab.active { background:linear-gradient(135deg,#405189,#0a6a90); color:#fff; border-color:transparent; box-shadow:0 10px 20px -14px rgba(64,81,137,.8); }
.user-tab .tab-count { font-size:.66rem; font-weight:800; background:#eef1f8; color:#405189; border-radius:999px; padding:.1em .55em; }
.user-tab.active .tab-count { background:rgba(255,255,255,.25); color:#fff; }
[data-layout-mode="dark"] .user-tab { background:#1d2125; border-color:#2a2f34; color:#c3cfe2; }
[data-layout-mode="dark"] .user-tab .tab-count { background:#2a2f34; color:#c3cfe2; }
[data-layout-mode="dark"] .user-tab.active { background:linear-gradient(135deg,#405189,#0a6a90); color:#fff; }

/* Listado de usuarios como tarjetas */
.user-list { display:flex; flex-direction:column; gap:.7rem; }
.user-item { display:grid; grid-template-columns: minmax(200px, 1.3fr) 2.7fr auto; gap:.85rem 1.25rem; align-items:center; background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:.85rem 1rem; transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
.user-item:hover { border-color:#d7dfec; box-shadow:0 14px 28px -22px rgba(16,24,40,.55); transform:translateY(-1px); }
[data-layout-mode="dark"] .user-item { background:#1d2125; border-color:#2a2f34; }
[data-layout-mode="dark"] .user-item:hover { border-color:#3a4048; }
.user-item-main { display:flex; align-items:center; gap:.7rem; min-width:0; }
.user-item-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:.6rem 1.4rem; min-width:0; }
.user-field { min-width:0; display:flex; flex-direction:column; gap:2px; }
.user-field-lbl { font-size:.58rem; text-transform:uppercase; letter-spacing:.45px; color:#98a2b3; font-weight:800; display:flex; align-items:center; gap:4px; }
.user-field-lbl i { font-size:.72rem; }
.user-field-val { font-size:.8rem; color:#344054; font-weight:600; overflow-wrap:anywhere; display:flex; flex-wrap:wrap; gap:.25rem; align-items:center; }
[data-layout-mode="dark"] .user-field-val { color:#dee2e6; }
.user-item-actions { display:flex; gap:.35rem; justify-content:flex-end; flex-shrink:0; }
@media (max-width: 991.98px) {
    .user-item { grid-template-columns: 1fr; }
    .user-item-grid { grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); }
    .user-item-actions { justify-content:flex-start; }
}

/* Botones icono */
.btn-icon { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; border:1px solid; background:#fff; cursor:pointer; transition:all .15s ease; }
.btn-edit { color:#405189; border-color:#dbe2f0; }
.btn-edit:hover { background:#405189; color:#fff; border-color:#405189; }
.btn-del { color:#f06548; border-color:#fecaca; }
.btn-del:hover { background:#f06548; color:#fff; border-color:#f06548; }
.btn-reset { color:#b7791f; border-color:#fde68a; }
.btn-reset:hover { background:#f59e0b; color:#fff; border-color:#f59e0b; }
[data-layout-mode="dark"] .btn-icon { background:#1d2125; }
.sec-table .btn-icon { width:30px; height:30px; border-radius:9px; font-size:.8rem; }

/* Permisos */
.perm-section-title { font-size:.78rem; font-weight:800; letter-spacing:.05em; color:#1b2537; margin-bottom:.85rem; display:flex; align-items:center; gap:.5rem; }
[data-layout-mode="dark"] .perm-section-title { color:#fff; }
.perm-section-title i { color:#405189; }
.count-badge { font-size:.65rem; font-weight:800; background:#eef1f8; color:#405189; padding:.15em .65em; border-radius:999px; }
.perm-list { display:flex; flex-direction:column; gap:.5rem; }
.perm-card { background:#fff; border:1px solid #eef0f4; border-radius:13px; padding:.7rem .9rem; transition:all .16s ease; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
.perm-card:hover { border-color:#d7dfec; box-shadow:0 10px 22px -16px rgba(16,24,40,.5); }
[data-layout-mode="dark"] .perm-card { background:#15181c; border-color:#2a2f34; }
.perm-card-name { font-weight:700; font-size:.82rem; color:#1b2537; display:flex; align-items:center; gap:.5rem; }
[data-layout-mode="dark"] .perm-card-name { color:#dee2e6; }
.perm-card-name i { color:#405189; }
.perm-card-roles { display:flex; align-items:center; gap:.3rem; flex-wrap:wrap; }
.perm-card-role { display:inline-flex; align-items:center; gap:3px; font-size:.62rem; font-weight:800; padding:.15em .55em; border-radius:999px; background:#eef1f8; color:#405189; }
.perm-card-role.super { background:linear-gradient(135deg,#f06548,#fb7e6e); color:#fff; }
.perm-card-actions { display:flex; align-items:center; gap:.3rem; flex-shrink:0; }
.perm-card-actions .btn-icon { width:30px; height:30px; font-size:.78rem; }

/* Grid de permisos en modales */
.perm-group { background:#f8fafc; border:1px solid #eef0f4; border-radius:12px; padding:.7rem .9rem; margin-bottom:.5rem; }
[data-layout-mode="dark"] .perm-group { background:#1a1d20; border-color:#2a2f34; }
.perm-group-title { font-size:.7rem; text-transform:uppercase; letter-spacing:.5px; font-weight:800; color:#405189; margin-bottom:.45rem; display:flex; align-items:center; justify-content:space-between; }
[data-layout-mode="dark"] .perm-group-title { color:#dee2e6; }
.perm-check { display:inline-flex; align-items:center; gap:5px; margin-right:.6rem; margin-bottom:.3rem; font-size:.78rem; font-weight:600; color:#495057; cursor:pointer; }
[data-layout-mode="dark"] .perm-check { color:#dee2e6; }
.perm-check input { accent-color:#405189; }
.toggle-all { font-size:.65rem; font-weight:700; color:#405189; cursor:pointer; text-decoration:underline; }
.perm-grid-search-box { position:relative; margin-bottom:.65rem; }
.perm-grid-search-icon { position:absolute; top:50%; left:.75rem; transform:translateY(-50%); color:#adb5bd; font-size:.9rem; pointer-events:none; }
.perm-grid-filter { padding-left:2.2rem; font-size:.8rem; border-radius:9px; border:1px solid #e9ebec; }
.perm-grid-filter:focus { border-color:#299cdb; box-shadow:0 0 0 .2rem rgba(41,156,219,.15); }

/* Empty */
.empty-state { text-align:center; padding:2.75rem 1rem; background: linear-gradient(135deg,#f8fafc,#eef1f8); border-radius:16px; border:1px dashed #d6dbe0; }
.empty-state .empty-icon { width:74px; height:74px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:50%; font-size:2.2rem; color:#adb5bd; box-shadow:0 10px 24px -12px rgba(0,0,0,.18); }
.sec-empty-filter { text-align:center; padding:2rem; color:#98a2b3; font-size:.88rem; }
.sec-empty-filter i { font-size:2rem; display:block; margin-bottom:.5rem; color:#c3cfe2; }

/* Modales */
.modal-sec .modal-content { border:0; border-radius:18px; overflow:hidden; box-shadow:0 30px 60px -20px rgba(16,24,40,.5); }
.modal-sec .modal-header { background: linear-gradient(135deg,#12345b,#1c486c 50%,#0a6a90); color:#fff; border:0; padding:1.05rem 1.3rem; }
.modal-sec .modal-header .btn-close { filter: brightness(0) invert(1); opacity:.85; }
.modal-sec .modal-title { font-weight:800; font-size:.95rem; }
.modal-sec label { font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; font-weight:800; color:#5d6478; margin-bottom:5px; }
[data-layout-mode="dark"] .modal-sec label { color:#adb5bd; }
.modal-sec .form-control, .modal-sec .form-select { border:1px solid #e9ebec; border-radius:11px; font-size:.85rem; padding:.55rem .85rem; font-weight:600; }
.modal-sec .form-control:focus { border-color:#299cdb; box-shadow: 0 0 0 .2rem rgba(41,156,219,.15); }
.modal-sec .modal-footer { border-top:1px solid #f1f3f5; padding:.85rem 1.3rem; }

.invalid-feedback-custom { display:none; width:100%; margin-top:3px; font-size:.72rem; font-weight:600; color:#f06548; }
.form-control.is-invalid, .form-select.is-invalid { border-color:#f06548; box-shadow: 0 0 0 .2rem rgba(240,101,72,.15); }
.form-control.is-valid, .form-select.is-valid { border-color:#0ab39c; box-shadow: 0 0 0 .2rem rgba(10,179,156,.15); }
.form-control.is-invalid ~ .invalid-feedback-custom, .form-select.is-invalid ~ .invalid-feedback-custom { display:block; }

/* Indicador Seguimiento ESAM */
.esam-pill { display:inline-flex; align-items:center; gap:5px; font-size:.7rem; font-weight:800; padding:.28em .7em; border-radius:999px; border:1px solid; white-space:nowrap; }
.esam-pill i { font-size:.85rem; }
.esam-pill.on { background:#e6f7ee; color:#067647; border-color:#b7e8d6; }
.esam-pill.warn { background:#fff4d6; color:#8a6100; border-color:#fbe3a3; }
.esam-pill.off { background:#f1f3f7; color:#667085; border-color:#e0e6f0; }
.esam-meta { font-size:.7rem; color:#98a2b3; font-variant-numeric: tabular-nums; }

/* Secciones dentro de modales */
.modal-sec .sec-block { border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.1rem; margin-bottom:1rem; }
[data-layout-mode="dark"] .modal-sec .sec-block { border-color:#2a2f34; }
.modal-sec .sec-block-title { display:flex; align-items:center; gap:.5rem; font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#405189; margin-bottom:.9rem; }
.modal-sec .sec-block-title i { font-size:1rem; }
.modal-sec .sec-block-title.wa { color:#0f766e; }
.modal-sec .sec-block-title .hint { margin-left:auto; font-size:.66rem; font-weight:600; text-transform:none; letter-spacing:0; color:#98a2b3; }
.modal-sec .modal-header .modal-subtitle { font-size:.74rem; color:rgba(255,255,255,.78); margin:1px 0 0; }
.modal-sec .switch-row { display:flex; align-items:center; gap:.65rem; background:#f8fafc; border:1px solid #eef0f4; border-radius:12px; padding:.6rem .8rem; }
[data-layout-mode="dark"] .modal-sec .switch-row { background:#1a1d20; border-color:#2a2f34; }
.modal-sec .switch-row .txt { font-size:.8rem; font-weight:700; color:#344054; }
[data-layout-mode="dark"] .modal-sec .switch-row .txt { color:#dee2e6; }
</style>
@endpush
