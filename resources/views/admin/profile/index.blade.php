@extends('admin.layouts.master')

@section('title', 'Mi Perfil - ' . config('app.name'))

@push('css')
<style>
.prof-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.75rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.prof-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.prof-hero::after  { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.prof-hero > * { position:relative; z-index:1; }
.prof-avatar {
    width:84px; height:84px; border-radius:50%;
    background: rgba(255,255,255,.2);
    border:3px solid rgba(255,255,255,.45);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:2rem; font-weight:800; color:#fff;
    backdrop-filter: blur(4px);
    box-shadow: 0 8px 22px -8px rgba(0,0,0,.35);
}
.prof-hero h4 { color:#fff; font-weight:800; margin:0; letter-spacing:.2px; }
.prof-hero .hero-sub { color: rgba(255,255,255,.85); font-size:.88rem; margin: 2px 0 0; }
.prof-meta { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.65rem; }
.prof-meta .meta-pill {
    display:inline-flex; align-items:center; gap:6px;
    font-size:.72rem; font-weight:700;
    padding:.28em .75em; border-radius:999px;
    background:rgba(255,255,255,.18); color:#fff;
    border:1px solid rgba(255,255,255,.28);
}
.prof-meta .meta-pill i { font-size:.9rem; }

.info-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); height:100%; }
[data-layout-mode="dark"] .info-card { background:#1d2125; border-color:#2a2f34; }
.info-card .card-header { background:transparent; border-bottom:1px dashed #eef0f4; padding: 1rem 1.25rem; }
[data-layout-mode="dark"] .info-card .card-header { border-bottom-color:#2a2f34; }
.info-card .card-header h5 { font-size:.85rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#405189; margin:0; display:flex; align-items:center; gap:.5rem; }
[data-layout-mode="dark"] .info-card .card-header h5 { color:#dee2e6; }
.info-card .card-body { padding: 1rem 1.25rem; }

.info-row { display:flex; justify-content:space-between; gap:1rem; padding:.55rem 0; border-bottom:1px dashed #eef0f4; }
[data-layout-mode="dark"] .info-row { border-bottom-color:#2a2f34; }
.info-row:last-child { border-bottom:0; }
.info-row .lbl { font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; color:#878a99; font-weight:700; }
.info-row .val { font-size:.88rem; font-weight:600; color:#212529; text-align:right; word-break:break-word; }
[data-layout-mode="dark"] .info-row .val { color:#fff; }

.kpi { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.15rem; height:100%; position:relative; overflow:hidden; transition: all .2s ease; }
.kpi::before { content:""; position:absolute; top:0; left:0; height:100%; width:4px; background: var(--k-accent, #299cdb); }
.kpi:hover { transform: translateY(-2px); box-shadow: 0 12px 26px -14px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .kpi { background:#1d2125; border-color:#2a2f34; }
.kpi .k-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.kpi .k-val { font-size:1.4rem; font-weight:800; color:#212529; line-height:1.1; }
[data-layout-mode="dark"] .kpi .k-val { color:#fff; }
.kpi .k-sub { font-size:.7rem; color:#878a99; margin-top:2px; }
.kpi .k-ic { width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; background: var(--k-bg, #eef1f8); color: var(--k-color, #405189); }

/* Ofertas en inscripción */
.oferta-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; transition:all .2s ease; overflow:hidden; height:100%; }
[data-layout-mode="dark"] .oferta-card { background:#1d2125; border-color:#2a2f34; }
.oferta-card:hover { box-shadow:0 10px 24px -12px rgba(64,81,137,.25); transform: translateY(-2px); border-color:#c3cfe2; }
.oferta-header { padding:.9rem 1.1rem; background: linear-gradient(135deg, #0ab39c 0%, #1abc9c 100%); color:#fff; display:flex; align-items:center; gap:.75rem; }
.oferta-header.disabled { background: linear-gradient(135deg, #878a99 0%, #adb5bd 100%); }
.oferta-header .of-icon { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.22); border:1px solid rgba(255,255,255,.3); display:inline-flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.oferta-header .of-name { font-size:.88rem; font-weight:800; line-height:1.2; flex:1; min-width:0; }
.oferta-header .of-badge { font-size:.62rem; text-transform:uppercase; letter-spacing:.5px; background:rgba(255,255,255,.22); padding:.25em .55em; border-radius:6px; font-weight:800; }
.oferta-body { padding: .85rem 1.1rem; }
.oferta-body .o-row { display:flex; justify-content:space-between; gap:.5rem; font-size:.78rem; padding:.3rem 0; border-bottom:1px dashed #f1f3f7; }
[data-layout-mode="dark"] .oferta-body .o-row { border-bottom-color:#2a2f34; }
.oferta-body .o-row:last-child { border-bottom:0; }
.oferta-body .o-row .l { color:#878a99; font-weight:700; }
.oferta-body .o-row .v { color:#212529; font-weight:700; text-align:right; }
[data-layout-mode="dark"] .oferta-body .o-row .v { color:#fff; }
.oferta-footer { padding:.65rem 1.1rem; background:#f8f9fc; border-top:1px solid #eef0f4; display:flex; justify-content:space-between; align-items:center; }
[data-layout-mode="dark"] .oferta-footer { background:#1a1d20; border-top-color:#2a2f34; }
.oferta-footer .insc-count { font-size:.75rem; font-weight:700; color:#405189; }
[data-layout-mode="dark"] .oferta-footer .insc-count { color:#c3cfe2; }
.status-pill { display:inline-flex; align-items:center; gap:4px; font-size:.62rem; font-weight:800; padding:.2em .55em; border-radius:6px; border:1px solid; text-transform:uppercase; letter-spacing:.4px; }
.status-on { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.status-off { background:#fef3c7; color:#d97706; border-color:#fde68a; }

/* Programas con inscritos (estilo asesores) */
.prog-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; transition: all .2s ease; overflow:hidden; }
[data-layout-mode="dark"] .prog-card { background:#1d2125; border-color:#2a2f34; }
.prog-card:hover { box-shadow:0 10px 24px -12px rgba(64,81,137,.25); transform: translateY(-2px); }
.prog-card-body { padding: 1rem 1.15rem; display:flex; align-items:center; gap:.9rem; }
.prog-card-icon { width:46px; height:46px; flex-shrink:0; background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.3rem; box-shadow: 0 6px 14px -6px rgba(64,81,137,.55); }
.prog-card-info { flex:1; min-width:0; }
.prog-card-name { font-weight:800; color:#212529; font-size:.92rem; line-height:1.2; }
[data-layout-mode="dark"] .prog-card-name { color:#fff; }
.prog-card-meta { font-size:.72rem; color:#878a99; margin-top:2px; }
.prog-card-count { display:flex; flex-direction:column; align-items:flex-end; }
.prog-card-count .num { font-size:1.6rem; font-weight:800; color:#405189; line-height:1; }
.prog-card-count .lbl { font-size:.65rem; text-transform:uppercase; letter-spacing:.4px; color:#878a99; font-weight:700; }
.prog-pills { display:flex; flex-wrap:wrap; gap:.4rem; padding: 0 1.15rem .65rem; align-items:center; }
.prog-card-footer { padding: .65rem 1.15rem; background:#f8f9fc; border-top:1px solid #eef0f4; display:flex; justify-content:flex-end; }
[data-layout-mode="dark"] .prog-card-footer { background:#1a1d20; border-top-color:#2a2f34; }
.btn-ver-inscritos { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:.32em .85em; border-radius:8px; background:#fff; color:#405189; border:1px solid #c3cfe2; cursor:pointer; transition: all .15s ease; }
.btn-ver-inscritos:hover { background:#405189; color:#fff; border-color:#405189; }

.insc-modal-header { background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; border:0; }
.insc-modal-header .btn-close { filter: brightness(0) invert(1); opacity:.85; }
.insc-row { display:flex; align-items:center; gap:.7rem; padding:.55rem .85rem; border:1px solid #eef0f4; border-radius:10px; margin-bottom:.45rem; background:#fff; }
[data-layout-mode="dark"] .insc-row { background:#1d2125; border-color:#2a2f34; }
.insc-row:hover { border-color:#c3cfe2; }
.insc-avatar { width:30px; height:30px; border-radius:50%; background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; font-weight:800; font-size:.7rem; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
.insc-info { flex:1; min-width:0; }
.insc-name { font-weight:700; color:#212529; font-size:.84rem; line-height:1.2; }
[data-layout-mode="dark"] .insc-name { color:#fff; }
.insc-meta { font-size:.68rem; color:#878a99; margin-top:1px; }
.plan-pill { display:inline-flex; align-items:center; gap:4px; font-size:.68rem; font-weight:800; padding:.22em .65em; border-radius:999px; border:1px solid; white-space:nowrap; }
.plan-con { background:#dbeafe; color:#299cdb; border-color:#bae6fd; }
.plan-reg { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
.estado-pill { display:inline-flex; align-items:center; gap:4px; font-size:.65rem; font-weight:700; padding:.18em .55em; border-radius:6px; border:1px solid; white-space:nowrap; }
.est-act { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.est-ret { background:#fee2e2; color:#f06548; border-color:#fecaca; }
.prog-pill { display:inline-flex; align-items:center; gap:4px; font-size:.7rem; font-weight:700; padding:.25em .7em; border-radius:999px; border:1px solid; }
.pp-act { background:#d1fae5; color:#0ab39c; border-color:#a7f3d8; }
.pp-ret { background:#fee2e2; color:#f06548; border-color:#fecaca; }
.pp-con { background:#dbeafe; color:#299cdb; border-color:#bae6fd; }
.pp-reg { background:#eef1f8; color:#405189; border-color:#c3cfe2; }

.chart-card { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1.25rem; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); margin-bottom:1rem; }
[data-layout-mode="dark"] .chart-card { background:#1d2125; border-color:#2a2f34; }
.chart-card-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#495057; display:flex; align-items:center; gap:.5rem; margin-bottom:1rem; padding-bottom:.65rem; border-bottom: 1px dashed #eef0f4; }
[data-layout-mode="dark"] .chart-card-title { color:#ced4da; border-bottom-color:#2a2f34; }
.chart-card-title i { color:#405189; font-size:1rem; }
.chart-wrapper { position:relative; width:100%; height:300px; }
canvas.chart-canvas { width:100% !important; height:300px !important; }

.empty-state { text-align:center; padding:2.5rem 1rem; background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-radius:14px; border:1px dashed #d6dbe0; }
.empty-state .empty-icon { width:72px; height:72px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:50%; font-size:2.2rem; color:#adb5bd; box-shadow: 0 8px 20px -10px rgba(0,0,0,.15); }

.section-title { font-size:.85rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#405189; display:flex; align-items:center; gap:.5rem; margin-bottom:1rem; padding-bottom:.65rem; border-bottom: 1px dashed #c3cfe2; }
[data-layout-mode="dark"] .section-title { color:#dee2e6; border-bottom-color:#2a2f34; }
.section-title i { font-size:1.05rem; }

.role-pill { display:inline-flex; align-items:center; gap:5px; font-size:.7rem; font-weight:800; padding:.3em .85em; border-radius:999px; background:rgba(255,255,255,.22); border:1px solid rgba(255,255,255,.32); color:#fff; text-transform:uppercase; letter-spacing:.5px; }

/* Tabs */
.profile-tabs-wrap {
    background:#fff;
    border:1px solid #eef0f4;
    border-radius:14px;
    box-shadow:0 4px 18px -10px rgba(64,81,137,.18);
    padding:.4rem;
    margin-bottom:1.25rem;
    overflow-x:auto;
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
}
.profile-tabs-wrap::-webkit-scrollbar { height:5px; }
.profile-tabs-wrap::-webkit-scrollbar-thumb { background:#dfe3ec; border-radius:999px; }
[data-layout-mode="dark"] .profile-tabs-wrap { background:#1d2125; border-color:#2a2f34; }
.profile-tabs { border:0; margin:0; padding:0; flex-wrap:nowrap; gap:.25rem; }
.profile-tabs .nav-link {
    display:inline-flex; align-items:center; gap:.5rem; white-space:nowrap;
    color:#5d6478; font-weight:700; font-size:.82rem;
    padding:.6rem 1rem; border:0; border-radius:10px; background:transparent;
    transition:color .18s ease, background .18s ease, box-shadow .18s ease;
}
[data-layout-mode="dark"] .profile-tabs .nav-link { color:#c3cfe2; }
.profile-tabs .nav-link:hover { color:#405189; background:#f1f4fb; }
[data-layout-mode="dark"] .profile-tabs .nav-link:hover { color:#fff; background:#252b31; }
.profile-tabs .nav-link.active {
    color:#fff;
    background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);
    box-shadow:0 8px 18px -10px rgba(64,81,137,.75);
}
.profile-tabs .nav-link i { margin:0; font-size:1.02rem; }
.profile-tabs .nav-link .tab-count {
    font-size:.66rem; font-weight:800; line-height:1;
    padding:.2em .55em; border-radius:999px;
    background:#e8f7ef; color:#0a8f74; border:1px solid #b7e8d6;
}
.profile-tabs .nav-link.active .tab-count { background:rgba(255,255,255,.25); color:#fff; border-color:rgba(255,255,255,.45); }

/* Barra de herramientas de cada pestaña */
.tab-toolbar {
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;
    background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:.75rem 1rem; margin-bottom:1rem;
    box-shadow:0 3px 12px -10px rgba(64,81,137,.35);
}
[data-layout-mode="dark"] .tab-toolbar { background:#1d2125; border-color:#2a2f34; }
.tab-toolbar .tb-title { font-size:.86rem; font-weight:800; color:#212529; display:flex; align-items:center; gap:.5rem; margin:0; }
[data-layout-mode="dark"] .tab-toolbar .tb-title { color:#fff; }
.tab-toolbar .tb-title i { color:#405189; font-size:1.05rem; }
.tab-toolbar .tb-sub { font-size:.72rem; color:#878a99; margin:2px 0 0; }
.tab-toolbar .form-select { border:1px solid #e9ebec; border-radius:9px; font-weight:700; color:#405189; }
.tab-toolbar .tb-label { font-size:.66rem; text-transform:uppercase; letter-spacing:.4px; color:#878a99; font-weight:800; margin-right:.15rem; }

/* Chips de desglose por mes (Inscripciones) */
.m-chip-row { margin-top:.75rem; display:flex; flex-wrap:wrap; gap:8px; }
.m-chip { padding:.55rem .7rem; background:#f8f9fc; border-radius:10px; border:1px solid #eef0f4; min-width:150px; flex:1; transition:border-color .15s ease, box-shadow .15s ease; }
[data-layout-mode="dark"] .m-chip { background:#1a1d20; border-color:#2a2f34; }
.m-chip:hover { border-color:#d7dfec; box-shadow:0 6px 16px -12px rgba(64,81,137,.5); }
.m-chip-title { font-size:.68rem; font-weight:800; color:#405189; margin-bottom:4px; text-transform:uppercase; letter-spacing:.3px; }
[data-layout-mode="dark"] .m-chip-title { color:#c3cfe2; }
.m-chip-item { font-size:.68rem; display:flex; justify-content:space-between; align-items:center; padding:1px 0; gap:.5rem; }
.m-chip-dot { width:8px; height:8px; border-radius:2px; display:inline-block; flex-shrink:0; }
.m-chip-name { color:#6c757d; }
[data-layout-mode="dark"] .m-chip-name { color:#adb5bd; }
.m-chip-val { font-weight:700; color:#212529; }
[data-layout-mode="dark"] .m-chip-val { color:#fff; }

/* Identidad dentro de la pestaña Información */
.identity-card {
    position:relative; overflow:hidden; height:100%; border-radius:14px;
    background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);
    color:#fff; padding:1.5rem 1.4rem;
    box-shadow:0 10px 30px -14px rgba(64,81,137,.55);
}
.identity-card::before { content:""; position:absolute; top:-50px; right:-50px; width:190px; height:190px; border-radius:50%; background:radial-gradient(circle,rgba(255,255,255,.18) 0%,transparent 70%); }
.identity-card > * { position:relative; z-index:1; }
.identity-card .identity-avatar {
    width:76px; height:76px; border-radius:50%; font-size:1.7rem; font-weight:800;
    display:inline-flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); border:3px solid rgba(255,255,255,.45);
    backdrop-filter:blur(4px);
}
.identity-card .identity-name { font-size:1.12rem; font-weight:800; margin:.7rem 0 .1rem; }
.identity-card .identity-user { font-size:.82rem; color:rgba(255,255,255,.85); }
.identity-card .identity-role {
    display:inline-flex; align-items:center; gap:5px; margin-top:.65rem;
    font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px;
    padding:.3em .8em; border-radius:999px;
    background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.35);
}
.identity-card .identity-foot { margin-top:1.1rem; padding-top:.9rem; border-top:1px solid rgba(255,255,255,.22); }
.identity-card .btn-identity {
    background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.35); color:#fff;
    font-weight:700; font-size:.76rem; border-radius:9px; padding:.45rem .8rem;
    display:inline-flex; align-items:center; gap:.4rem; transition:all .18s ease;
}
.identity-card .btn-identity:hover { background:#fff; color:#405189; }

/* Password card */
.pwd-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); padding:1.5rem 1.75rem; max-width:600px; }
[data-layout-mode="dark"] .pwd-card { background:#1d2125; border-color:#2a2f34; }
.pwd-card label { font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:800; color:#405189; margin-bottom:6px; }
[data-layout-mode="dark"] .pwd-card label { color:#dee2e6; }
.pwd-card .form-control { border:1px solid #e9ebec; border-radius:10px; padding:.65rem .95rem; font-size:.88rem; font-weight:600; }
.pwd-card .form-control:focus { border-color:#299cdb; box-shadow: 0 0 0 .2rem rgba(41,156,219,.15); }
.pwd-card .btn-save { background: linear-gradient(135deg, #405189, #5e72b8); color:#fff; font-weight:700; padding:.6rem 1.5rem; border-radius:10px; border:0; transition:all .2s ease; }
.pwd-card .btn-save:hover { box-shadow: 0 10px 22px -10px rgba(64,81,137,.55); transform: translateY(-1px); }
.pwd-hint { font-size:.72rem; color:#878a99; margin-top:6px; }
/* ===== Seguimiento styles for profile ===== */
.sd-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45); margin-bottom:1rem;
}
.sd-hero::before { content:"";position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(255,255,255,.18) 0%,transparent 70%);border-radius:50%;}
.sd-hero-title { font-size:1.1rem;font-weight:700;margin-bottom:.15rem;letter-spacing:.2px;}
.sd-hero-sub { font-size:.8rem;opacity:.9;}
.sd-week-nav .btn { background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);color:#fff;font-weight:600;backdrop-filter:blur(4px);}
.sd-week-nav .btn:hover { background:rgba(255,255,255,.32);color:#fff;}
.sd-week-label { display:inline-block;padding:.35rem .9rem;background:rgba(255,255,255,.22);border-radius:999px;font-size:.82rem;font-weight:600;letter-spacing:.3px;}
.sd-stat-card { position:relative;background:#fff;border-radius:12px;border:1px solid #eef0f5;padding:.9rem 1rem;overflow:hidden;}
.sd-stat-card::before { content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--sd-accent,#405189);}
.sd-stat-label { font-size:.68rem;font-weight:700;text-transform:uppercase;color:#878a99;letter-spacing:.4px;margin-bottom:.15rem;}
.sd-stat-value { font-size:1.5rem;font-weight:700;color:#2a2f43;line-height:1.1;}
.sd-stat-icon { width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
.sd-day { background:#fff;border-radius:12px;border:1px solid #eef0f5;padding:.85rem 1rem;margin-bottom:.9rem;}
.sd-day-header { display:flex;align-items:center;gap:.65rem;margin-bottom:.6rem;}
.sd-day-pill { min-width:56px;padding:.45rem .35rem;text-align:center;background:linear-gradient(180deg,#f3f6fc,#e9eef8);border:1px solid #dfe5f0;border-radius:10px;color:#2a2f43;line-height:1;}
.sd-day-pill .sd-day-num { display:block;font-size:1.3rem;font-weight:800;}
.sd-day-pill .sd-day-name { display:block;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c7693;margin-top:.15rem;}
.sd-day-pill.is-today { background:linear-gradient(180deg,#e6f4ff,#cfe7ff);border-color:#9bc7f0;color:#0b5ed7;}
.sd-day-pill.is-today .sd-day-name { color:#0b5ed7; }
.sd-day-count { font-size:.72rem;color:#878a99;font-weight:600;}
.sd-day-empty { text-align:center;padding:.9rem;border:1px dashed #dfe3ec;border-radius:10px;color:#aab0c0;font-size:.8rem;}
.sd-h-row { display:grid;grid-template-columns:90px 1fr auto;gap:.85rem;align-items:stretch;border:1px solid #eef0f5;border-left:4px solid var(--sd-row-accent,#299cdb);border-radius:10px;padding:.65rem .85rem;margin-bottom:.55rem;background:#fbfcfe;transition:box-shadow .15s ease,transform .15s ease;}
.sd-h-row:hover { box-shadow:0 6px 18px -10px rgba(64,81,137,.25);transform:translateY(-1px);}
.sd-h-row.is-late { background:linear-gradient(135deg,#fff2f1,#ffe5e2);border-color:#f06548;border-left-color:#d63031;box-shadow:0 4px 14px -8px rgba(214,48,49,.4);animation:sdPulseLate 2.6s ease-in-out infinite;}
@keyframes sdPulseLate { 0%,100%{box-shadow:0 4px 14px -8px rgba(214,48,49,.4)} 50%{box-shadow:0 6px 22px -6px rgba(214,48,49,.6)} }
.sd-h-row.is-late .sd-h-estado .sd-pill { background:#d63031;color:#fff;}
.sd-h-row.is-confirm-late { background:linear-gradient(135deg,#fff8ec,#ffeecc);border-color:#f7b84b;border-left-color:#d68910;}
.sd-h-row.is-confirm-late .sd-h-estado .sd-pill { background:#d68910;color:#fff;}
.sd-h-row.is-confirm { background:linear-gradient(135deg,#f0fbf7,#e0f5ed);border-color:#69cbb1;border-left-color:#0ab39c;}
.sd-h-row.is-missing { background:linear-gradient(135deg,#eef6ff,#dcebff);border-color:#7fb6f0;border-left-color:#0b5ed7;}
.sd-h-time { display:flex;flex-direction:column;align-items:center;justify-content:center;background:#fff;border:1px solid #eef0f5;border-radius:8px;padding:.35rem .25rem;min-width:90px;text-align:center;}
.sd-h-time .h-i { font-size:.95rem;font-weight:700;color:#2a2f43;line-height:1;}
.sd-h-time .h-s { font-size:.66rem;color:#878a99;margin-top:.15rem;letter-spacing:.5px;}
.sd-h-body { display:flex;flex-direction:column;gap:.25rem;min-width:0;}
.sd-h-title { font-size:.92rem;font-weight:700;color:#2a2f43;line-height:1.2;word-break:break-word;}
.sd-h-title a.sd-link { color:inherit;text-decoration:none;border-bottom:1px dashed transparent;}
.sd-h-title a.sd-link:hover { color:#405189;border-bottom-color:#405189;}
.sd-h-title .sd-codigo { display:inline-block;font-size:.65rem;color:#fff;background:var(--sd-row-accent,#299cdb);padding:.1rem .45rem;border-radius:999px;vertical-align:middle;margin-left:.35rem;font-weight:700;letter-spacing:.3px;}
.sd-h-modulo { font-size:.78rem;color:#5d6478;font-weight:600;}
.sd-h-meta { display:flex;flex-wrap:wrap;gap:.5rem .85rem;font-size:.74rem;color:#5d6478;margin-top:.1rem;}
.sd-h-meta .sd-meta-i { display:inline-flex;align-items:center;gap:.3rem;}
.sd-h-meta .sd-meta-i i { color:#878a99;font-size:.85rem;}
.sd-h-meta .sd-meta-i b { font-weight:600;color:#2a2f43;}
.sd-h-side { display:flex;flex-direction:column;gap:.35rem;align-items:flex-end;justify-content:space-between;min-height:60px;}
.sd-pill { display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .55rem;border-radius:999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px;background:var(--sd-pill-bg,#e9eef8);color:var(--sd-pill-color,#405189);}
.sd-pill::before { content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
.sd-btn-toggle { border:1px solid #d8deeb;background:#fff;color:#5d6478;border-radius:8px;padding:.25rem .65rem;font-size:.72rem;font-weight:600;display:inline-flex;align-items:center;gap:.3rem;cursor:pointer;}
.sd-btn-toggle:hover { background:#f1f4fb;color:#2a2f43;}
.sd-btn-toggle.is-warn { border-color:#f0cf86;background:#fffaf0;color:#8a6100;}
.sd-btn-toggle.is-warn:hover { background:#fff4dc;}
.sd-btn-toggle.is-info { border-color:#9bc7f0;background:#eef6ff;color:#0b5ed7;}
.sd-btn-toggle.is-info:hover { background:#dceaff;}
.sd-btn-toggle.is-danger { border-color:#f3aaa1;background:#fff5f4;color:#b1240f;}
.sd-btn-toggle.is-danger:hover { background:#ffe5e2;}
.sd-flag { display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .55rem;border-radius:6px;font-size:.66rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;}
.sd-flag.is-danger { background:#d63031;color:#fff;}
.sd-flag.is-warn   { background:#d68910;color:#fff;}
.sd-flag.is-info   { background:#0b5ed7;color:#fff;}
.sd-tpl { display:none;}
.sd-modal-info-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem .85rem;background:#fff;border:1px solid #eef0f5;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;}
.sd-modal-info-grid .sd-info-cell { min-width:0;}
.sd-modal-info-grid .sd-info-label { font-size:.65rem;font-weight:700;color:#878a99;text-transform:uppercase;letter-spacing:.4px;}
.sd-modal-info-grid .sd-info-value { font-size:.85rem;color:#2a2f43;font-weight:600;word-break:break-word;}
.sd-modal-info-grid .sd-info-value a { color:#0b5ed7;text-decoration:none;}
.sd-modal-alert { display:flex;align-items:flex-start;gap:.55rem;padding:.7rem .9rem;border-radius:10px;font-size:.82rem;margin-bottom:1rem;}
.sd-modal-alert.is-danger { background:#fff2f1;color:#b1240f;border:1px solid #f3aaa1;}
.sd-modal-alert.is-warn { background:#fff8ec;color:#8a6100;border:1px solid #f0cf86;}
.sd-modal-alert.is-info { background:#eef6ff;color:#1c3e9e;border:1px solid #b8d4f5;}
.sd-detail-title { font-size:.7rem;font-weight:700;color:#878a99;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;display:flex;align-items:center;gap:.35rem;}
.sd-obs-box { background:#fff;border:1px solid #eef0f5;border-radius:8px;padding:.55rem .75rem;font-size:.82rem;color:#2a2f43;white-space:pre-wrap;word-break:break-word;}
.sd-obs-empty { color:#aab0c0;font-style:italic;background:#fff;border:1px dashed #dfe3ec;}
.sd-asist-table { width:100%;font-size:.78rem;margin:0;}
.sd-asist-table thead th { background:#f6f8fc;color:#5d6478;font-weight:700;font-size:.66rem;text-transform:uppercase;letter-spacing:.3px;padding:.45rem .6rem;position:sticky;top:0;}
.sd-asist-table tbody td { padding:.4rem .6rem;border-top:1px solid #eef0f5;}
.sd-asist-table .a-pill { display:inline-flex;align-items:center;gap:.3rem;padding:.1rem .55rem;border-radius:999px;font-size:.68rem;font-weight:700;}
.sd-asist-table .a-Presente { background:#d4f6ee;color:#0a7059;}
.sd-asist-table .a-Atraso   { background:#fff4d6;color:#8a6100;}
.sd-asist-table .a-Falta    { background:#ffd9d3;color:#b1240f;}
.sd-asist-table .a-Justificado { background:#dde7ff;color:#1c3e9e;}
/* ===== Profile program/module links ===== */
.prof-moodle-link {
    display:inline-flex;align-items:center;justify-content:center;
    width:20px;height:20px;background:#fff4e6;color:#f76707;
    border:1px solid #ffd8a8;border-radius:4px;margin-left:5px;vertical-align:middle;
    text-decoration:none;font-size:.65rem;transition:all .15s ease;
}
.prof-moodle-link:hover { background:#f76707;color:#fff;transform:translateY(-1px); }
.prof-prog-link { color:inherit;text-decoration:none;border-bottom:1px dashed transparent;transition:color .15s,border-color .15s; }
.prof-prog-link:hover { color:#405189;border-bottom-color:#405189; }
.prof-docente-link { color:#405189;text-decoration:none;font-weight:600;border-bottom:1px dashed transparent;transition:color .15s,border-color .15s; }
.prof-docente-link:hover { color:#0b5ed7;border-bottom-color:#0b5ed7; }
.prof-horarios-btn {
    display:inline-flex;align-items:center;gap:3px;
    font-size:.62rem;font-weight:700;padding:.2em .55em;border-radius:6px;
    border:1px solid #c3ddfa;background:#eef6ff;color:#0b5ed7;
    cursor:pointer;transition:all .15s ease;text-decoration:none;margin-left:5px;
}
.prof-horarios-btn:hover { background:#0b5ed7;color:#fff;border-color:#0b5ed7; }

/* ===== Tabla Ofertas (estilo programas.index) ===== */
.programas-table { table-layout: fixed; width: 100%; }
.programas-table th, .programas-table td {
    vertical-align: middle;
    padding: .75rem .6rem;
}
.programas-table th {
    font-weight: 700;
    letter-spacing: .4px;
    font-size: .65rem;
    color: #6c757d;
    border-bottom: 2px solid #e9ebec !important;
}
.programas-table tbody tr {
    border-bottom: 1px solid #f1f3f5;
    transition: background-color .15s ease;
}
.programas-table tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .programas-table tbody tr { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .programas-table tbody tr:hover { background-color: #212529; }
.programas-table td > span,
.programas-table td > div { min-width: 0; }
.programas-table .btn-icon {
    width: 40px; height: 40px;
    padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.05rem;
    transition: all .18s ease;
    border: 0;
    background: transparent;
}
.btn-icon-ghost {
    color: inherit;
}
.btn-icon-ghost:hover {
    background: rgba(0,0,0,.04);
    transform: translateY(-1px);
}
.btn-icon-ghost:active {
    transform: translateY(0);
}
[data-layout-mode="dark"] .btn-icon-ghost:hover {
    background: rgba(255,255,255,.06);
}
@media (max-width: 1199.98px) {
    .programas-table { table-layout: auto; }
    .table-card .table-responsive { overflow-x: auto; }
}

/* ===== Modal Inscribir Participante ===== */
#modalInscribirP input[type="text"]:focus,
#modalInscribirP input[type="number"]:focus,
#modalInscribirP select:focus { outline:0;border-color:#0ab39c;box-shadow:0 0 0 .15rem rgba(10,179,156,.15); }
#modalInscribirP .ci-search-box.loading .ci-search-spinner { display:inline-block; }
#modalInscribirP .ci-result-box { margin-top:.85rem; }
#modalInscribirP .ci-result-card {
    border:1px solid #c3ddfa;
    background:linear-gradient(135deg,#dbeafe 0%,#eef1f8 100%);
    border-radius:10px;
    padding:.85rem 1rem;
    display:flex;align-items:center;gap:.85rem;
}
#modalInscribirP .ci-result-card.notfound { background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);border-color:#fde68a; }
#modalInscribirP .ci-result-card.warning { background:linear-gradient(135deg,#fee2e2 0%,#fecaca 100%);border-color:#fecaca; }
#modalInscribirP .ci-avatar {
    width:44px;height:44px;border-radius:50%;
    background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);
    color:#fff;font-weight:700;font-size:.9rem;
    display:inline-flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
#modalInscribirP .ci-info { flex:1;min-width:0; }
#modalInscribirP .ci-nombre { font-weight:700;color:#212529;font-size:.95rem;line-height:1.2; }
#modalInscribirP .ci-meta { font-size:.72rem;color:#6c757d;margin-top:2px; }
#modalInscribirP .monto-deuda { font-size:.7rem;color:#f06548;font-weight:700;margin-top:2px; }
#modalInscribirP .monto-deuda.ok { color:#0ab39c; }
#modalInscribirP #btn-inscribir-confirm:disabled { opacity:.6; }
#modalInscribirP #btn-inscribir-confirm:hover:not(:disabled) { transform:translateY(-1px); }
#modalInscribirP .cuotas-detalle table { width:100%;font-size:.78rem;border-collapse:collapse; }
#modalInscribirP .cuotas-detalle th { background:#f8f9fc;font-weight:700;color:#495057;padding:.45rem .55rem;border-bottom:2px solid #e9ebec; }
#modalInscribirP .cuotas-detalle td { padding:.4rem .55rem;border-bottom:1px solid #f1f3f5; }
#modalInscribirP .cuotas-detalle tr.ultima td { font-weight:700;color:#d97706; }
#modalInscribirP .asesor-selected-card {
    display:inline-flex;align-items:center;gap:.55rem;
    background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);
    border:1px solid #fde68a;
    border-radius:999px;
    padding:.3rem .35rem .3rem .35rem;
    font-size:.82rem;font-weight:600;
    color:#92400e;
}
#modalInscribirP .asesor-selected-card .asesor-opt-avatar {
    width:30px;height:30px;border-radius:50%;
    background:linear-gradient(135deg,#f7b84b 0%,#ff9f43 100%);
    color:#fff;font-weight:700;font-size:.7rem;
    display:inline-flex;align-items:center;justify-content:center;
    flex-shrink:0;
}

/* ===== Mis programas y seguimiento ===== */
.prof-page .prof-resp-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    background: #fff;
    border: 1px solid var(--prof-line, #e9edf3);
    border-radius: 14px;
    padding: .85rem 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 6px 18px -14px rgba(16, 32, 60, .4);
}
.prof-page .prof-resp-head-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-size: 1.2rem; flex-shrink: 0;
    box-shadow: 0 8px 18px -12px rgba(64, 81, 137, .9);
}
.prof-page .prof-resp-head-main {
    font-weight: 800; color: var(--prof-navy, #163b6b);
    font-size: 1rem; line-height: 1.15; letter-spacing: -.01em;
}
.prof-page .prof-resp-head-sub { font-size: .74rem; color: #6c7a91; margin-top: 2px; }
.prof-page .prof-resp-body { min-height: 200px; }
.prof-page .resp-prof-tabs {
    display: inline-flex; align-items: center; gap: .25rem;
    background: #eef1f7; border: 1px solid var(--prof-line, #e9edf3);
    padding: .28rem; border-radius: 10px;
}
.prof-page .resp-prof-tab {
    border: 0; background: transparent; color: #5d6478;
    font-weight: 700; font-size: .8rem; padding: .38rem .9rem; border-radius: 8px;
    display: inline-flex; align-items: center; gap: .35rem; cursor: pointer;
    transition: background .15s ease, color .15s ease;
}
.prof-page .resp-prof-tab:hover { color: #405189; background: rgba(64,81,137,.1); }
.prof-page .resp-prof-tab.active { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color: #fff; }
[data-layout-mode="dark"] .prof-page .resp-prof-tabs { background: #23272c; border-color: #2f353c; }
[data-layout-mode="dark"] .prof-page .resp-prof-tab { color: #c3cfe2; }
[data-layout-mode="dark"] .prof-page .resp-prof-tab:hover { color: #fff; background: rgba(255,255,255,.08); }
[data-bs-theme="dark"] .prof-page .prof-resp-head {
    background: #1b2330; border-color: #2a3342;
}
[data-bs-theme="dark"] .prof-page .prof-resp-head-main { color: #e5ebf5; }
[data-bs-theme="dark"] .prof-page .prof-resp-head-sub { color: #9aa7bd; }

/* Secciones del listado de programas: todo visible */
.prof-page #prof-tipo-tabs { position: sticky; top: calc(var(--avip-topbar-h, 70px) + 8px); z-index: 5; }
.prof-page .prof-tipo-sec-head {
    display: flex; align-items: center; gap: .5rem;
    font-weight: 800; color: var(--prof-navy, #163b6b);
    font-size: .95rem; margin: 0 0 .7rem;
}
.prof-page .prof-tipo-sec-head::after { content: ""; flex: 1; height: 1px; background: var(--prof-line, #e9edf3); }
.prof-page .prof-tipo-sec-icon {
    width: 26px; height: 26px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-size: .85rem; flex-shrink: 0;
}
.prof-page .prof-tipo-sec-count {
    background: rgba(64,81,137,.12); color: #405189;
    font-size: .68rem; font-weight: 800; padding: .15em .6em; border-radius: 999px;
}
[data-bs-theme="dark"] .prof-page .prof-tipo-sec-head,
[data-layout-mode="dark"] .prof-page .prof-tipo-sec-head { color: #e5ebf5; }
[data-bs-theme="dark"] .prof-page .prof-tipo-sec-head::after,
[data-layout-mode="dark"] .prof-page .prof-tipo-sec-head::after { background: #2a3342; }
[data-bs-theme="dark"] .prof-page .prof-tipo-sec-count,
[data-layout-mode="dark"] .prof-page .prof-tipo-sec-count { background: rgba(255,255,255,.12); color: #cdd7e6; }

/* ===== Mis programas: contenido dinámico (contenedores con fondo y texto legible) ===== */
.prof-page {
    --prof-card: #ffffff;
    --prof-card-2: #f6f8fc;
    --prof-ink: #1f2a44;
    --prof-muted: #6b7a94;
    --prof-border: #e6ebf3;
    --prof-chip-bg: rgba(64,81,137,.10);
    --prof-chip-ink: #405189;
    --prof-navy: #163b6b;
    --prof-line: #e9edf3;
}
[data-layout-mode="dark"] .prof-page {
    --prof-card: #1d2125;
    --prof-card-2: #23272c;
    --prof-ink: #e8eefb;
    --prof-muted: #9aa7bd;
    --prof-border: #2f353c;
    --prof-chip-bg: rgba(255,255,255,.12);
    --prof-chip-ink: #cdd7e6;
    --prof-navy: #e5ebf5;
    --prof-line: #2a3342;
}

.prof-resp-hero {
    position: relative; overflow: hidden; color: #fff;
    border-radius: 16px; padding: 1.4rem 1.5rem; margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--prof-accent, #405189) 0%, var(--prof-accent-2, #5e72b8) 100%);
    box-shadow: 0 16px 32px -20px rgba(16,32,60,.75);
}
.prof-resp-hero::after {
    content: ""; position: absolute; top: -60px; right: -40px; width: 210px; height: 210px;
    border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.22), transparent 70%);
}
.prof-resp-hero > * { position: relative; z-index: 1; }
.prof-resp-avatar {
    width: 60px; height: 60px; border-radius: 50%; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.35rem; font-weight: 800; color: #fff;
    background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.42);
    text-shadow: 0 1px 2px rgba(0,0,0,.25);
}
.prof-resp-name { color: #fff; font-weight: 800; margin: 0; font-size: 1.25rem; letter-spacing: .2px; }
.prof-resp-cid { color: rgba(255,255,255,.9); font-size: .82rem; margin-top: .2rem; word-break: break-word; }
.prof-resp-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
.prof-resp-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
    padding: .4em .85em; border-radius: 999px; font-size: .78rem; font-weight: 700; color: #fff;
}
.prof-resp-chip strong { font-weight: 800; }

/* Hero con color claro: se usa texto oscuro para mantener contraste */
.prof-resp-hero.is-light { color: #22304a; }
.prof-resp-hero.is-light .prof-resp-name { color: #1f2a44; }
.prof-resp-hero.is-light .prof-resp-cid { color: rgba(31,42,68,.82); }
.prof-resp-hero.is-light .prof-resp-avatar {
    color: #1f2a44; text-shadow: none;
    background: rgba(255,255,255,.6); border-color: rgba(31,42,68,.25);
}
.prof-resp-hero.is-light .prof-resp-chip {
    color: #1f2a44; background: rgba(255,255,255,.6); border-color: rgba(31,42,68,.22);
}

.prof-panel {
    background: var(--prof-card); border: 1px solid var(--prof-border);
    border-radius: 14px; padding: 1rem;
    box-shadow: 0 8px 24px -20px rgba(16,32,60,.7);
}
.prof-empty {
    text-align: center; padding: 2.5rem 1rem; border-radius: 14px;
    background: var(--prof-card-2); border: 1px dashed var(--prof-border); color: var(--prof-muted);
}
.prof-empty-ic {
    width: 72px; height: 72px; margin: 0 auto 1rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 2.1rem;
    background: var(--prof-card); color: var(--prof-muted); box-shadow: 0 8px 20px -14px rgba(0,0,0,.4);
}
.prof-empty h5 { color: var(--prof-ink); font-weight: 800; margin: 0 0 .25rem; }

.prof-tipo-tabs {
    display: flex; gap: .35rem; flex-wrap: wrap; margin-bottom: 1rem; padding: .4rem;
    background: var(--prof-card-2); border: 1px solid var(--prof-border); border-radius: 12px;
}
.prof-tipo-tab {
    display: inline-flex; align-items: center; gap: .4rem; cursor: pointer;
    border: 0; background: transparent; color: var(--prof-muted);
    border-radius: 9px; font-size: .82rem; font-weight: 700; padding: .55rem 1rem;
    transition: background .15s ease, color .15s ease;
}
.prof-tipo-tab:hover { color: var(--prof-chip-ink); background: var(--prof-chip-bg); }
.prof-tipo-tab.active { background: linear-gradient(135deg, #405189, #5e72b8); color: #fff; }
.prof-tipo-tab .cnt {
    background: var(--prof-chip-bg); color: var(--prof-chip-ink);
    font-weight: 800; font-size: .65rem; padding: .2em .55em; border-radius: 999px;
}
.prof-tipo-tab.active .cnt { background: rgba(255,255,255,.25); color: #fff; }
.prof-tipo-panes { display: flex; flex-direction: column; gap: 1.25rem; }
.prof-tipo-list { display: flex; flex-direction: column; gap: .7rem; }

.prof-prog {
    background: var(--prof-card); border: 1px solid var(--prof-border);
    border-radius: 12px; overflow: hidden; transition: box-shadow .15s ease, border-color .15s ease;
}
.prof-prog.is-open { border-color: var(--prof-chip-bg); box-shadow: 0 12px 26px -18px rgba(64,81,137,.6); }
.prof-prog-toggle {
    width: 100%; border: 0; text-align: left; cursor: pointer;
    background: var(--prof-card-2); padding: .9rem 1.05rem;
    display: flex; align-items: center; gap: .85rem; flex-wrap: wrap;
}
.prof-prog-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center; font-size: 1.05rem;
    color: #fff; background: linear-gradient(135deg, #405189, #5e72b8);
}
.prof-prog-name { font-weight: 700; color: var(--prof-ink); font-size: .93rem; margin: 0; line-height: 1.25; }
.prof-prog-meta { font-size: .7rem; color: var(--prof-muted); margin-top: 3px; display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
.prof-prog-prog { display: flex; align-items: center; gap: .55rem; min-width: 150px; }
.prof-prog-bar { flex: 1; height: 7px; border-radius: 999px; overflow: hidden; background: var(--prof-chip-bg); }
.prof-prog-bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #0ab39c, #299cdb); }
.prof-prog-pct { font-size: .72rem; font-weight: 800; color: var(--prof-ink); min-width: 34px; text-align: right; }
.prof-prog-chevron {
    width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;
    background: var(--prof-chip-bg); color: var(--prof-chip-ink); transition: transform .2s ease;
}
.prof-prog-body { padding: 1rem; border-top: 1px solid var(--prof-border); background: var(--prof-card); }
.prof-chip-row {
    display: flex; flex-wrap: wrap; gap: .35rem;
    margin-bottom: .85rem; padding-bottom: .85rem; border-bottom: 1px dashed var(--prof-border);
}
.prof-chip {
    display: inline-flex; align-items: center; gap: .35rem; padding: .3em .75em;
    border-radius: 999px; font-size: .72rem; font-weight: 700;
    background: var(--prof-chip-bg); color: var(--prof-chip-ink);
}
.prof-chip.is-total { background: linear-gradient(135deg, #405189, #5e72b8); color: #fff; }
.prof-chip.is-ok { background: #d1fae5; color: #0a8f74; }
.prof-chip.is-dev { background: #dbeafe; color: #1d4ed8; }
.prof-chip.is-none { background: var(--prof-card-2); color: var(--prof-muted); }
[data-layout-mode="dark"] .prof-chip.is-ok { background: rgba(16,185,129,.18); color: #5eead4; }
[data-layout-mode="dark"] .prof-chip.is-dev { background: rgba(59,130,246,.2); color: #93c5fd; }

.prof-mod {
    display: flex; align-items: center; gap: .65rem;
    padding: .6rem .8rem; border-radius: 10px;
    border: 1px solid transparent; border-bottom: 1px dashed var(--prof-border);
}
.prof-mod:last-child { border-bottom: 0; }
.prof-mod.is-live { background: rgba(41,156,219,.12); border-color: rgba(41,156,219,.35); }
.prof-mod.is-next { background: rgba(82,196,26,.14); border-color: rgba(82,196,26,.4); }
.prof-mod-num {
    width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800; color: #fff;
    background: linear-gradient(135deg, #405189, #5e72b8);
}
.prof-mod-name { font-weight: 600; color: var(--prof-ink); font-size: .82rem; line-height: 1.25; }
.prof-mod-meta { font-size: .68rem; color: var(--prof-muted); margin-top: 2px; }
.prof-mod-estado {
    display: inline-flex; align-items: center; gap: .3rem; white-space: nowrap;
    font-size: .68rem; font-weight: 700; padding: .3em .75em; border-radius: 999px;
    border: 1px solid transparent; background: #eef2f7; color: #64748b;
}
.prof-mod-estado.is-done { color: #0a8f74; background: #d1fae5; border-color: #a7f3d8; }
.prof-mod-estado.is-dev { color: #1d4ed8; background: #dbeafe; border-color: #bfdbfe; }
.prof-mod-estado.is-idle { color: #64748b; background: #eef2f7; border-color: #e2e8f0; }
.prof-mod-estado.is-pend { color: #b45309; background: #fef3c7; border-color: #fde68a; }
.prof-mod-estado.is-susp { color: #b42318; background: #fee4e2; border-color: #fecdca; }
[data-layout-mode="dark"] .prof-mod-estado.is-done { color: #5eead4; background: rgba(16,185,129,.16); border-color: rgba(16,185,129,.35); }
[data-layout-mode="dark"] .prof-mod-estado.is-dev { color: #93c5fd; background: rgba(59,130,246,.18); border-color: rgba(59,130,246,.4); }
[data-layout-mode="dark"] .prof-mod-estado.is-idle { color: #cbd5e1; background: rgba(148,163,184,.16); border-color: rgba(148,163,184,.35); }
[data-layout-mode="dark"] .prof-mod-estado.is-pend { color: #fcd34d; background: rgba(245,158,11,.16); border-color: rgba(245,158,11,.35); }
[data-layout-mode="dark"] .prof-mod-estado.is-susp { color: #fca5a5; background: rgba(239,68,68,.18); border-color: rgba(239,68,68,.4); }

/* Enlaces dentro del contenido: contraste en modo oscuro */
[data-layout-mode="dark"] .prof-prog-link { color: inherit; }
[data-layout-mode="dark"] .prof-prog-link:hover { color: #93c5fd; border-bottom-color: #93c5fd; }
[data-layout-mode="dark"] .prof-docente-link { color: #93c5fd; }
[data-layout-mode="dark"] .prof-docente-link:hover { color: #bfdbfe; border-bottom-color: #bfdbfe; }
.prof-tag {
    display: inline-flex; align-items: center; gap: 3px; margin-left: 5px; vertical-align: middle;
    font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .4px;
    padding: .15em .55em; border-radius: 999px; color: #fff;
}
.prof-tag.is-live { background: #1f6fbf; }
.prof-tag.is-next { background: #52c41a; }
.prof-loading, .prof-error { text-align: center; padding: 2.5rem 1rem; color: var(--prof-muted); }
.prof-error { color: #f06548; }
</style>
@endpush

@section('content')
@php
    // El tab de publicidad se carga por AJAX (2.ª petición): sin este keep() los mensajes
    // flash (éxito/error de registro de gastos) caducan en la carga del perfil y nunca
    // llegan al partial que los muestra.
    session()->keep(['success', 'error', 'errors', 'publicidad_success', 'publicidad_error', 'publicidad_tab']);
@endphp
<div class="prof-page">
@php
    $iniciales = strtoupper(collect(explode(' ', trim($user->fullname ?? $user->username ?? 'U')))
        ->filter()->take(2)->map(fn($p) => substr($p, 0, 1))->implode(''));
    $rolesUsuario = $user->getRoleNames();
    $rolBonito = $rolesUsuario->isNotEmpty()
        ? $rolesUsuario->implode(' · ')
        : ucwords(str_replace('_', ' ', $user->role ?? 'usuario'));

    // Sólo marketing (y Super Admin) ven las pestañas de ofertas/inscripciones/publicidad.
    $puedeMarketing = $user->canAccessMarketing();
@endphp

<div class="prof-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="prof-avatar">{{ $iniciales }}</div>
        <div>
            <h4>{{ $user->fullname ?? $user->username }}</h4>
            <p class="hero-sub"><i class="ri-at-line"></i> {{ $user->username }} @if($user->email) · <i class="ri-mail-line"></i> {{ $user->email }} @endif</p>
            <div class="prof-meta">
                <span class="role-pill"><i class="ri-shield-user-line"></i> {{ $rolBonito }}</span>
                <span class="meta-pill"><i class="ri-calendar-line"></i> Miembro desde {{ \Carbon\Carbon::parse($user->created ?? $user->created_at ?? now())->format('d/m/Y') }}</span>
                <span class="meta-pill"><i class="ri-time-line"></i> Sesión iniciada</span>
                @if($asesorId && $asesorNombre)
                    <span class="meta-pill"><i class="ri-user-voice-line"></i> Asesor: {{ $asesorNombre }}</span>
                @endif
            </div>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ $user->can('dashboard.listar') ? route('admin.dashboard') : route('admin.profile') }}" class="text-white-50"><i class="ri-home-3-line align-bottom me-1"></i>Inicio</a></li>
        <li class="breadcrumb-item active text-white">Mi perfil</li>
    </ol>
</div>

@if($puedeMarketing)
{{-- KPIs (área de marketing) --}}
<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Ofertas activas</div><div class="k-val">{{ $resumen['total_ofertas'] }}</div><div class="k-sub">en inscripciones</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-megaphone-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#299cdb;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Total inscritos</div><div class="k-val">{{ $resumen['total_inscritos'] }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                <span class="k-ic" style="--k-bg:#f0f9ff; --k-color:#299cdb;"><i class="ri-team-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Activos</div><div class="k-val">{{ $resumen['total_activos'] }}</div><div class="k-sub">inscritos activos</div></div>
                <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-check-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#f7b84b;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Programas</div><div class="k-val">{{ $resumen['total_programas'] }}</div><div class="k-sub">con inscritos</div></div>
                <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-booklet-line"></i></span>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabs --}}
<div class="profile-tabs-wrap">
<ul class="nav profile-tabs" role="tablist">
    @if($responsable)
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-responsable" type="button"><i class="ri-user-settings-line"></i> Mis programas</button></li>
    @endif
    <li class="nav-item"><button class="nav-link @if(!$responsable) active @endif" data-bs-toggle="tab" data-bs-target="#tab-info" type="button"><i class="ri-user-3-line"></i> Información</button></li>
    @if($puedeMarketing)
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ofertas" type="button"><i class="ri-megaphone-line"></i> Ofertas en inscripción <span class="tab-count">{{ $ofertas->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inscripciones" type="button"><i class="ri-team-line"></i> Inscripciones</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-publicidad" type="button"><i class="ri-shopping-cart-2-line"></i> Presupuesto publicidad</button></li>
    @endif
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-password" type="button"><i class="ri-lock-password-line"></i> Cambiar contraseña</button></li>
</ul>
</div>

<div class="tab-content">
    @if($responsable)
    {{-- ============ TAB RESPONSABLE ============ --}}
    <div class="tab-pane fade show active" id="tab-responsable">
        <div class="prof-resp-head">
            <div class="d-flex align-items-center gap-2">
                <span class="prof-resp-head-icon"><i class="ri-user-settings-line"></i></span>
                <div>
                    <div class="prof-resp-head-main">Mis programas y seguimiento</div>
                    <div class="prof-resp-head-sub">Programas asignados, módulos y seguimiento semanal</div>
                </div>
            </div>
            <div class="resp-prof-tabs">
                <button type="button" class="resp-prof-tab active" data-tab="prof-programas">
                    <i class="ri-booklet-line"></i> Programas
                </button>
                <button type="button" class="resp-prof-tab" data-tab="prof-seguimiento">
                    <i class="ri-calendar-check-line"></i> Seguimiento Semanal
                </button>
            </div>
        </div>
        <div id="prof-resp-content" class="prof-resp-body">
            <div class="detail-loading text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                <p class="mt-2 text-muted mb-0">Cargando datos del responsable...</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ============ TAB INFO ============ --}}
    <div class="tab-pane fade @if(!$responsable) show active @endif" id="tab-info">
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="identity-card">
                    <div class="identity-avatar">{{ $iniciales }}</div>
                    <div class="identity-name">{{ $user->fullname ?? $user->username }}</div>
                    <div class="identity-user"><i class="ri-at-line"></i> {{ $user->username }}</div>
                    <span class="identity-role"><i class="ri-shield-user-line"></i> {{ $rolBonito }}</span>

                    <div class="identity-foot d-flex flex-column gap-2">
                        @if($user->email)
                            <div class="d-flex align-items-center gap-2" style="font-size:.8rem; word-break:break-word;">
                                <i class="ri-mail-line"></i> {{ $user->email }}
                            </div>
                        @endif
                        @if($asesorId && $asesorNombre)
                            <div class="d-flex align-items-center gap-2" style="font-size:.8rem;">
                                <i class="ri-user-voice-line"></i> Asesor: <strong>{{ $asesorNombre }}</strong>
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-2" style="font-size:.8rem;">
                            <i class="ri-calendar-line"></i> Miembro desde {{ \Carbon\Carbon::parse($user->created ?? $user->created_at ?? now())->format('d/m/Y') }}
                        </div>
                        <button type="button" class="btn-identity mt-1 align-self-start"
                                onclick="document.querySelector('[data-bs-target=\'#tab-password\']').click()">
                            <i class="ri-lock-password-line"></i> Cambiar contraseña
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="card info-card">
                        <div class="card-header"><h5><i class="ri-user-3-line"></i> Datos de la cuenta</h5></div>
                        <div class="card-body">
                            <div class="info-row"><span class="lbl"><i class="ri-user-line me-1"></i>Nombre completo</span><span class="val">{{ $user->fullname ?? '—' }}</span></div>
                            <div class="info-row"><span class="lbl"><i class="ri-at-line me-1"></i>Usuario</span><span class="val">{{ $user->username }}</span></div>
                            <div class="info-row"><span class="lbl"><i class="ri-mail-line me-1"></i>Correo</span><span class="val">{{ $user->email ?? '—' }}</span></div>
                            <div class="info-row"><span class="lbl"><i class="ri-shield-user-line me-1"></i>Rol</span><span class="val">{{ $rolBonito }}</span></div>
                            @if($user->whatsapp_numero)
                                <div class="info-row"><span class="lbl"><i class="ri-whatsapp-line me-1"></i>WhatsApp</span><span class="val">{{ $user->whatsapp_numero }}@if($user->whatsapp_area) · {{ ucfirst($user->whatsapp_area) }}@endif</span></div>
                            @endif
                            <div class="info-row"><span class="lbl"><i class="ri-id-card-line me-1"></i>ID Usuario</span><span class="val">#{{ $user->id }}</span></div>
                        </div>
                    </div>
                    <div class="card info-card">
                        <div class="card-header"><h5><i class="ri-shield-check-line"></i> Actividad y seguridad</h5></div>
                        <div class="card-body">
                            <div class="info-row"><span class="lbl"><i class="ri-refresh-line me-1"></i>Última modificación</span><span class="val">{{ \Carbon\Carbon::parse($user->modified ?? $user->updated_at ?? now())->format('d/m/Y H:i') }}</span></div>
                            <div class="info-row"><span class="lbl"><i class="ri-time-line me-1"></i>Última actividad</span><span class="val">{{ $user->last_activity_at ? \Carbon\Carbon::parse($user->last_activity_at)->format('d/m/Y H:i') : '—' }}</span></div>
                            <div class="info-row">
                                <span class="lbl"><i class="ri-mail-check-line me-1"></i>Correo verificado</span>
                                <span class="val">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success-subtle text-success-emphasis">Verificado</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Pendiente</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row"><span class="lbl"><i class="ri-lock-line me-1"></i>Contraseña</span><span class="val">••••••••</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TAB OFERTAS (solo marketing) ============ --}}
    @if($puedeMarketing)
    <div class="tab-pane fade" id="tab-ofertas">
        <div class="tab-toolbar">
            <div>
                <p class="tb-title"><i class="ri-megaphone-line"></i> Ofertas académicas en inscripción</p>
                <p class="tb-sub">Programas abiertos con estado de inscripciones activo.</p>
            </div>
            <span class="badge bg-success-subtle text-success-emphasis">{{ $ofertas->count() }} programa(s)</span>
        </div>
        @if($ofertas->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-megaphone-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Sin ofertas activas</h5>
                <p class="text-muted mb-0">No hay programas en estado "Inscripciones" actualmente.</p>
            </div>
        @else
            @include('admin.programas.partials.table', ['programas' => $ofertas, 'soloVer' => true])
        @endif
    </div>
    @endif

    {{-- ============ TAB INSCRIPCIONES (solo marketing) ============ --}}
    @if($puedeMarketing)
    @php $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp
    <div class="tab-pane fade" id="tab-inscripciones">
        <div class="tab-toolbar">
            <div>
                <p class="tb-title"><i class="ri-team-line"></i> Inscripciones — Gestión {{ $gestion }}</p>
                <p class="tb-sub">Inscritos, puntos y conversión real por mes y programa.</p>
            </div>
            <form method="GET" action="{{ route('admin.profile') }}" class="d-flex align-items-center flex-wrap gap-2">
                <span class="tb-label">Gestión</span>
                <select name="gestion" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    @foreach($gestiones as $g)
                        <option value="{{ $g }}" @selected((int)$g === (int)$gestion)>{{ $g }}</option>
                    @endforeach
                </select>
                <span class="tb-label">Tipo</span>
                <select name="tipo" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="general" @selected($tipo === 'general')>General</option>
                    <option value="upi" @selected($tipo === 'upi')>Solo UPI Diplomados</option>
                    <option value="upi-auto" @selected($tipo === 'upi-auto')>UPI Autogestionados</option>
                    <option value="otros" @selected($tipo === 'otros')>Programas Sucre central</option>
                </select>
            </form>
        </div>

        {{-- KPIs --}}
        <div class="row g-2 mb-3">
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#299cdb;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Total inscritos</div><div class="k-val">{{ $resumen['total_inscritos'] }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                        <span class="k-ic" style="--k-bg:#f0f9ff; --k-color:#299cdb;"><i class="ri-team-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#0ab39c;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Activos</div><div class="k-val">{{ $resumen['total_activos'] }}</div><div class="k-sub">inscritos activos</div></div>
                        <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-check-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#405189;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Programas</div><div class="k-val">{{ $resumen['total_programas'] }}</div><div class="k-sub">con inscritos</div></div>
                        <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-booklet-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#f7b84b;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Puntos totales</div><div class="k-val">{{ number_format($resumen['total_puntos'], 1, ',', '.') }}</div><div class="k-sub">en {{ $gestion }}</div></div>
                        <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-star-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#0ab39c;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Puntos Reales</div>
                            <div class="k-val">{{ number_format($resumen['total_puntos_reales'], 1, ',', '.') }}
                                <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                                    ({{ $resumen['total_puntos'] > 0 ? round($resumen['total_puntos_reales'] / $resumen['total_puntos'] * 100) : 0 }}%)
                                </small>
                            </div>
                            <div class="k-sub">de inscritos reales</div>
                        </div>
                        <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-checkbox-circle-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#0ab39c;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">Inscritos Reales</div>
                            <div class="k-val">{{ $resumen['total_reales'] }}
                                <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                                    ({{ $resumen['total_inscritos'] > 0 ? round($resumen['total_reales'] / $resumen['total_inscritos'] * 100) : 0 }}%)
                                </small>
                            </div>
                            <div class="k-sub">con pagos al día</div>
                        </div>
                        <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-check-double-line"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6">
                <div class="kpi" style="--k-accent:#f06548;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="k-lbl">No Reales</div>
                            <div class="k-val">{{ $resumen['total_no_reales'] }}
                                <small class="text-muted" style="font-size:.65rem;font-weight:600;">
                                    ({{ $resumen['total_inscritos'] > 0 ? round($resumen['total_no_reales'] / $resumen['total_inscritos'] * 100) : 0 }}%)
                                </small>
                            </div>
                            <div class="k-sub">con pagos pendientes</div>
                        </div>
                        <span class="k-ic" style="--k-bg:#fef2f2; --k-color:#f06548;"><i class="ri-close-circle-line"></i></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico de inscripciones por mes --}}
        <div class="chart-card mb-3">
            <div class="chart-card-title">
                <i class="ri-bar-chart-grouped-line"></i> Inscripciones por Mes — Gestión {{ $gestion }}
                @if($asesorNombre)
                    <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                        Asesor: <strong>{{ $asesorNombre }}</strong>
                    </span>
                @endif
            </div>
            <div class="chart-wrapper">
                <div id="chartInscritosMes" style="height:300px;"></div>
            </div>
        </div>

        {{-- Desglose de inscritos por tipo --}}
        @php $inscritosTipoTotal = collect($inscritosTipoSeries)->sum(fn($s) => array_sum($s['data'])); @endphp
        @if($inscritosTipoTotal > 0)
        <div class="chart-card mb-3">
            <div class="chart-card-title" style="margin-bottom:0; border:0; padding-bottom:0;">
                <i class="ri-price-tag-3-line"></i> Inscritos por Tipo — {{ $gestion }}
                <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">{{ $inscritosTipoTotal }} inscrito(s)</span>
            </div>
            <div class="m-chip-row">
                @foreach(range(1, 12) as $m)
                    @php $idx = $m - 1; @endphp
                    @if(collect($inscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                        <div class="m-chip">
                            <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                            @foreach($inscritosTipoSeries as $serie)
                                @if(($serie['data'][$idx] ?? 0) > 0)
                                    <div class="m-chip-item">
                                        <span style="display:flex; align-items:center; gap:4px;">
                                            <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                            <span class="m-chip-name">{{ $serie['name'] }}</span>
                                        </span>
                                        <span class="m-chip-val">{{ $serie['data'][$idx] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Gráfico de puntos por mes --}}
        <div class="chart-card mb-3">
            <div class="chart-card-title">
                <i class="ri-star-line"></i> Puntos por Mes — Gestión {{ $gestion }}
                @if($asesorNombre)
                    <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                        Asesor: <strong>{{ $asesorNombre }}</strong>
                    </span>
                @endif
            </div>
            <div id="chartPuntosMes" style="height:300px;"></div>
            <div class="m-chip-row">
                @foreach(range(1, 12) as $m)
                    @php $idx = $m - 1; @endphp
                    @if(collect($puntosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                        <div class="m-chip">
                            <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                            @foreach($puntosTipoSeries as $serie)
                                @if(($serie['data'][$idx] ?? 0) > 0)
                                    <div class="m-chip-item">
                                        <span style="display:flex; align-items:center; gap:4px;">
                                            <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                            <span class="m-chip-name">{{ $serie['name'] }}</span>
                                        </span>
                                        <span class="m-chip-val">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Gráfico de Puntos Reales por Mes --}}
        <div class="chart-card mb-3">
            <div class="chart-card-title">
                <i class="ri-checkbox-circle-line"></i> Puntos Reales por Mes — Gestión {{ $gestion }}
                @if($asesorNombre)
                    <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                        Asesor: <strong>{{ $asesorNombre }}</strong>
                    </span>
                @endif
            </div>
            <div class="chart-wrapper">
                <div id="chartPuntosRealesMes" style="height:300px;"></div>
            </div>
            <div class="m-chip-row">
                @foreach(range(1, 12) as $m)
                    @php $idx = $m - 1; @endphp
                    @if(collect($puntosRealesTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                        <div class="m-chip">
                            <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                            @foreach($puntosRealesTipoSeries as $serie)
                                @if(($serie['data'][$idx] ?? 0) > 0)
                                    <div class="m-chip-item">
                                        <span style="display:flex; align-items:center; gap:4px;">
                                            <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                            <span class="m-chip-name">{{ $serie['name'] }}</span>
                                        </span>
                                        <span class="m-chip-val">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Gráfico de Inscritos Reales vs No Reales por Mes --}}
        <div class="chart-card mb-3">
            <div class="chart-card-title">
                <i class="ri-check-double-line"></i> Inscritos Reales vs No Reales por Tipo — Gestión {{ $gestion }}
                @if($asesorNombre)
                    <span class="text-muted ms-auto" style="font-size:.72rem; text-transform:none; letter-spacing:0;">
                        Asesor: <strong>{{ $asesorNombre }}</strong>
                    </span>
                @endif
            </div>
            <div class="chart-wrapper">
                <div id="chartRealesMes" style="height:300px;"></div>
            </div>
            <div class="m-chip-row">
                @foreach(range(1, 12) as $m)
                    @php $idx = $m - 1;
                        $mesTotal = collect($realesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) + collect($noRealesInscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]);
                    @endphp
                    @if($mesTotal > 0)
                        <div class="m-chip">
                            <div class="m-chip-title">{{ $mesesNombres[$idx] }}</div>
                            @foreach($realesInscritosTipoSeries as $serie)
                                @if(($serie['data'][$idx] ?? 0) > 0)
                                    <div class="m-chip-item">
                                        <span style="display:flex; align-items:center; gap:4px;">
                                            <span class="m-chip-dot" style="background:{{ $serie['color'] }};"></span>
                                            <span class="m-chip-name" style="color:#0ab39c;">R: {{ $serie['name'] }}</span>
                                        </span>
                                        <span class="m-chip-val" style="color:#0ab39c;">{{ $serie['data'][$idx] }}</span>
                                    </div>
                                @endif
                            @endforeach
                            @foreach($noRealesInscritosTipoSeries as $serie)
                                @if(($serie['data'][$idx] ?? 0) > 0)
                                    <div class="m-chip-item">
                                        <span style="display:flex; align-items:center; gap:4px;">
                                            <span class="m-chip-dot" style="background:{{ $serie['color'] }};opacity:.5;"></span>
                                            <span class="m-chip-name" style="color:#f06548;">NR: {{ $serie['name'] }}</span>
                                        </span>
                                        <span class="m-chip-val" style="color:#f06548;">{{ $serie['data'][$idx] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Inscritos por programa --}}
        <div class="tab-toolbar">
            <div>
                <p class="tb-title"><i class="ri-booklet-line"></i> Inscritos por programa</p>
                <p class="tb-sub">{{ $porPrograma->count() }} programa(s) — {{ $resumen['total_inscritos'] }} inscrito(s) — {{ number_format($resumen['total_puntos'], 1, ',', '.') }} pts</p>
            </div>
        </div>

        @if($porPrograma->count() === 0)
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Sin inscripciones en este periodo</h5>
                <p class="text-muted mb-0">No hay inscripciones registradas para la gestión {{ $gestion }}.</p>
            </div>
        @else
            <div class="row g-2">
                @foreach($porPrograma as $p)
                    <div class="col-xl-4 col-lg-6">
                        <div class="prog-card">
                            <div class="prog-card-body">
                                <span class="prog-card-icon"><i class="ri-graduation-cap-line"></i></span>
                                <div class="prog-card-info">
                                    <div class="prog-card-name">{{ $p->nombre }}</div>
                                    <div class="prog-card-meta">
                                        <i class="ri-hashtag"></i> {{ $p->codigo }}
                                        @if($p->tipo_nombre) · <i class="ri-price-tag-3-line"></i> {{ $p->tipo_nombre }} @endif
                                        · <i class="ri-calendar-2-line"></i> {{ $p->gestion }}
                                    </div>
                                </div>
                                <div class="prog-card-count">
                                    <div class="num">{{ $p->total }}</div>
                                    <div class="lbl">inscritos</div>
                                    <div style="font-size:.68rem;color:#8b5cf6;font-weight:700;margin-top:1px;">
                                        <i class="ri-star-line"></i> {{ number_format($p->puntos, 1, ',', '.') }} pts
                                    </div>
                                </div>
                            </div>
                            <div class="prog-pills">
                                @if($p->activos > 0)<span class="prog-pill pp-act"><i class="ri-check-line"></i> {{ $p->activos }} activos</span>@endif
                                @if($p->retirados > 0)<span class="prog-pill pp-ret"><i class="ri-close-line"></i> {{ $p->retirados }} retirados</span>@endif
                                @if($p->contado > 0)<span class="prog-pill pp-con"><i class="ri-bank-card-line"></i> {{ $p->contado }} contado</span>@endif
                                @if($p->regular > 0)<span class="prog-pill pp-reg"><i class="ri-funds-line"></i> {{ $p->regular }} regular</span>@endif
                                <span class="prog-pill" style="background:#ecfdf5;color:#0ab39c;"><i class="ri-check-double-line"></i> {{ (int)($p->inscritos_reales ?? 0) }} reales</span>
                            </div>
                            <div class="prog-card-footer">
                                <button type="button" class="btn-ver-inscritos" data-bs-toggle="modal" data-bs-target="#modalPerfilInsc-{{ $p->id }}">
                                    <i class="ri-eye-line"></i> Ver inscritos ({{ $p->total }})
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Modales: lista de inscritos por programa --}}
            @foreach($porPrograma as $p)
                @php $listaInsc = $inscritosPorPrograma->get($p->id, collect()); @endphp
                <div class="modal fade" id="modalPerfilInsc-{{ $p->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
                            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 50%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;position:relative;overflow:hidden;">
                                <div style="position:relative;z-index:1;">
                                    <div class="d-flex align-items-center gap-3 mb-1">
                                        <div style="width:42px;height:42px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);border-radius:12px;font-size:1.2rem;color:#fff;">
                                            <i class="ri-team-line"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title text-white mb-0" style="font-weight:700;font-size:1rem;">
                                                {{ $p->nombre }}
                                            </h5>
                                            <div style="font-size:.72rem;opacity:.88;display:flex;flex-wrap:wrap;gap:4px 12px;margin-top:2px;">
                                                <span><i class="ri-hashtag"></i> {{ $p->codigo }}</span>
                                                @if($p->tipo_nombre)<span><i class="ri-price-tag-3-line"></i> {{ $p->tipo_nombre }}</span>@endif
                                                <span><i class="ri-calendar-2-line"></i> {{ $p->gestion }}</span>
                                                <span><i class="ri-group-line"></i> {{ $p->total }} inscrito(s)</span>
                                                <span><i class="ri-check-double-line" style="color:#7dffb3;"></i> {{ (int)($p->inscritos_reales ?? 0) }} reales</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="background:#f8f9fc;padding:1rem;" data-inscritos-body data-url="{{ route('admin.profile.programa-inscritos', $p->id) }}?gestion={{ $gestion }}&tipo={{ $tipo }}">
                                <div class="text-center py-5 text-muted">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                                    <p class="mt-2 mb-0">Cargando inscritos...</p>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top:1px solid #eef0f4;padding:.75rem 1.25rem;background:#fff;">
                                <div style="font-size:.72rem;display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                                    <span class="text-muted" style="font-weight:600;text-transform:uppercase;letter-spacing:.3px;font-size:.6rem;">Plan:</span>
                                    @if($p->contado > 0)<span class="plan-pill plan-con" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-bank-card-line"></i> {{ $p->contado }} Contado</span>@endif
                                    @if($p->regular > 0)<span class="plan-pill plan-reg" style="font-size:.62rem;padding:2px 8px;border-radius:5px;"><i class="ri-funds-line"></i> {{ $p->regular }} Regular</span>@endif
                                    @php
                                        $realesCount = (int)($p->inscritos_reales ?? 0);
                                        $noRealesCount = $p->total - $realesCount;
                                    @endphp
                                    <span class="text-muted" style="font-size:.62rem;">|</span>
                                    @if($realesCount > 0)<span style="font-size:.62rem;padding:2px 8px;border-radius:5px;background:#ecfdf5;color:#0ab39c;border:1px solid #a7f3d0;"><i class="ri-check-double-line"></i> {{ $realesCount }} Reales</span>@endif
                                    @if($noRealesCount > 0)<span style="font-size:.62rem;padding:2px 8px;border-radius:5px;background:#fef2f2;color:#f06548;border:1px solid #fecaca;"><i class="ri-close-circle-line"></i> {{ $noRealesCount }} No Reales</span>@endif
                                </div>
                                <button type="button" class="btn btn-sm" style="background:#f1f3f5;color:#495057;border-radius:8px;font-size:.72rem;font-weight:600;padding:.35rem .9rem;border:1px solid #eef0f4;" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    @endif

    {{-- ============ TAB PASSWORD ============ --}}
    <div class="tab-pane fade" id="tab-password">
        <div class="section-title"><i class="ri-lock-password-line"></i> Cambiar contraseña</div>
        <div class="pwd-card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.password.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="current_password"><i class="ri-lock-line me-1"></i>Contraseña actual</label>
                    <div class="position-relative">
                        <input id="current_password" type="password" name="current_password" class="form-control" required autocomplete="current-password">
                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-muted toggle-pwd" data-target="current_password" style="text-decoration:none;"><i class="ri-eye-fill"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password"><i class="ri-key-2-line me-1"></i>Nueva contraseña</label>
                    <div class="position-relative">
                        <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" minlength="6">
                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-muted toggle-pwd" data-target="password" style="text-decoration:none;"><i class="ri-eye-fill"></i></button>
                    </div>
                    <div class="pwd-hint"><i class="ri-information-line"></i> Mínimo 6 caracteres. Usa letras, números y símbolos para mayor seguridad.</div>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation"><i class="ri-key-2-line me-1"></i>Confirmar nueva contraseña</label>
                    <div class="position-relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="6">
                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-muted toggle-pwd" data-target="password_confirmation" style="text-decoration:none;"><i class="ri-eye-fill"></i></button>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-light"><i class="ri-refresh-line me-1"></i>Limpiar</button>
                    <button type="submit" class="btn btn-save"><i class="ri-save-line me-1"></i>Actualizar contraseña</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ TAB PUBLICIDAD (solo marketing) ============ --}}
    @if($puedeMarketing)
    <div class="tab-pane fade" id="tab-publicidad">
        <div id="pub-content">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                <p class="mt-2 text-muted mb-0">Cargando presupuesto de publicidad...</p>
            </div>
        </div>
    </div>
    @endif
</div>
</div>{{-- /prof-page --}}
@endsection

{{-- Modal Seguimiento Semanal --}}
<div class="modal fade" id="sdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title" id="sdModalTitle">—</div>
                    <div class="modal-subtitle" id="sdModalSubtitle">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="sdModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
                <a href="#" id="sdModalGoto" class="btn btn-primary" target="_blank" rel="noopener" style="display:none;">
                    <i class="ri-external-link-line align-bottom"></i> Abrir programa
                </a>
            </div>
        </div>
    </div>
</div>

<style>
#sdModal .modal-content { border:0; border-radius:14px; overflow:hidden; }
#sdModal .modal-header { background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);color:#fff;border:0;padding:1rem 1.3rem; }
#sdModal.is-late .modal-header { background:linear-gradient(135deg,#d63031 0%,#f06548 100%); }
#sdModal.is-warn .modal-header { background:linear-gradient(135deg,#d68910 0%,#f7b84b 100%); }
#sdModal.is-info .modal-header { background:linear-gradient(135deg,#0b5ed7 0%,#3d8bf5 100%); }
#sdModal .modal-header .btn-close { filter:brightness(0) invert(1);opacity:.9; }
#sdModal .modal-title { font-size:1rem;font-weight:700;margin-bottom:.15rem; }
#sdModal .modal-subtitle { font-size:.78rem;opacity:.85; }
#sdModal .modal-body { padding:1.1rem 1.3rem;background:#fafbfd; }
</style>

{{-- Modal Horarios de Módulo --}}
<div class="modal fade" id="profModuloHorariosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profModuloHorariosTitle"><i class="ri-calendar-event-line"></i> Horarios del módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="profModuloHorariosBody" style="background:#fafbfd;min-height:150px;">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando horarios...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<style>
#profModuloHorariosModal .modal-content { border:0;border-radius:14px;overflow:hidden; }
#profModuloHorariosModal .modal-header { background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);border:0; }
#profModuloHorariosModal .modal-header .btn-close { filter:brightness(0) invert(1);opacity:.9; }
#profModuloHorariosModal .modal-title { font-size:.95rem;font-weight:700;color:#fff; }
.prof-horario-row { display:grid;grid-template-columns:140px 100px 1fr auto auto;gap:.5rem;align-items:center;padding:.5rem .75rem;border:1px solid #eef0f4;border-radius:10px;margin-bottom:.4rem;background:#fff;font-size:.8rem; }
.prof-horario-row .h-date { font-weight:700;color:#2a2f43; }
.prof-horario-row .h-time { color:#5d6478;font-weight:600; }
.prof-horario-row .h-estado { font-weight:700;font-size:.7rem;padding:.15em .55em;border-radius:999px;text-align:center;white-space:nowrap; }
.prof-ver-asistencia {
    display:inline-flex;align-items:center;gap:3px;
    font-size:.62rem;font-weight:700;padding:.2em .55em;border-radius:6px;
    border:1px solid #a7f3d8;background:#d1fae5;color:#0a7059;
    cursor:pointer;transition:all .15s ease;text-decoration:none;white-space:nowrap;
}
.prof-ver-asistencia:hover { background:#0a7059;color:#fff;border-color:#0a7059; }
</style>

{{-- Modal Inscribir Participante (desde el tab Ofertas) --}}
<div class="modal fade" id="modalInscribirP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 60%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;position:relative;overflow:hidden;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;"><i class="ri-user-add-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;" id="ip-modal-title">Inscribir Participante</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="ip-modal-subtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                {{-- 0. Asesor (auto-fill con el asesor de la cuenta) --}}
                <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                        <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#fef3c7;color:#d97706;"><i class="ri-user-star-line"></i></span>
                        <span>Asesor que realiza la inscripción</span>
                    </div>
                    <div id="ip-asesor-selected" class="mt-2"></div>
                    <input type="hidden" id="ip-asesor-id" value="{{ $asesorId }}">
                </div>

                {{-- 1. Búsqueda por CI del participante --}}
                <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                    <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                        <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#dbeafe;color:#299cdb;"><i class="ri-search-line"></i></span>
                        <span>Buscar Participante por CI</span>
                    </div>
                    <div style="position:relative;">
                        <i style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#878a99;font-size:1.1rem;" class="ri-passport-line"></i>
                        <input type="text" id="ip-ci" placeholder="Ingrese el CI (mín. 7 dígitos)..." autocomplete="off" maxlength="13" style="width:100%;border:1px solid #e9ebec;border-radius:10px;padding:.65rem 2.5rem .65rem 2.4rem;font-size:1rem;font-weight:600;transition:all .2s ease;">
                        <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#0ab39c;display:none;" class="ci-search-spinner"><span class="spinner-border spinner-border-sm"></span></span>
                    </div>
                    <div class="ci-result-box" id="ip-result"></div>
                </div>

                {{-- 2. Registrar nuevo (oculto) --}}
                <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;display:none;" id="ip-section-nuevo">
                    <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                        <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#fef3c7;color:#d97706;"><i class="ri-user-add-line"></i></span>
                        <span>Registrar Nuevo Participante</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Nombres <span class="text-danger">*</span></label>
                            <input type="text" id="ip-nombres" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" id="ip-apellidos" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Sexo</label>
                            <select id="ip-sexo" class="form-select"><option value="">—</option><option value="Hombre">Hombre</option><option value="Mujer">Mujer</option></select>
                        </div>
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Celular</label>
                            <input type="text" id="ip-celular" class="form-control" maxlength="30">
                        </div>
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Correo</label>
                            <input type="email" id="ip-correo" class="form-control" maxlength="120">
                        </div>
                        <div class="col-md-4">
                            <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Departamento</label>
                            <input type="text" id="ip-departamento" class="form-control" maxlength="80">
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-warning" id="btn-registrar-participante"><i class="ri-save-3-line align-bottom"></i> Registrar y continuar</button>
                        </div>
                    </div>
                </div>

                {{-- 3. Datos de inscripción (oculto) --}}
                <div id="ip-section-inscripcion" style="display:none;">
                    <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                        <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                            <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#d1fae5;color:#0ab39c;"><i class="ri-money-dollar-circle-line"></i></span>
                            <span>Costos del Programa</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div style="display:flex;align-items:center;gap:.65rem;padding:.8rem .9rem;border-radius:10px;border:1px solid #eef0f4;background:#fff;">
                                    <span style="width:36px;height:36px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;background:#eef1f8;color:#405189;"><i class="ri-bank-card-line"></i></span>
                                    <div>
                                        <div style="font-size:.7rem;color:#878a99;text-transform:uppercase;font-weight:700;letter-spacing:.4px;">Matrícula</div>
                                        <div style="font-size:1.05rem;font-weight:800;color:#212529;line-height:1.1;" id="ip-monto-mat">Bs 0.00</div>
                                        <div style="font-size:.7rem;font-weight:700;margin-top:2px;" id="ip-deuda-mat" class="ok">Deuda: Bs 0.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="display:flex;align-items:center;gap:.65rem;padding:.8rem .9rem;border-radius:10px;border:1px solid #eef0f4;background:#fff;">
                                    <span style="width:36px;height:36px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;background:#ecfdf5;color:#0ab39c;"><i class="ri-funds-line"></i></span>
                                    <div>
                                        <div style="font-size:.7rem;color:#878a99;text-transform:uppercase;font-weight:700;letter-spacing:.4px;">Colegiatura</div>
                                        <div style="font-size:1.05rem;font-weight:800;color:#212529;line-height:1.1;" id="ip-monto-col">Bs 0.00</div>
                                        <div style="font-size:.7rem;font-weight:700;margin-top:2px;" id="ip-deuda-col" class="ok">Deuda: Bs 0.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Descuento Matrícula</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" min="0" id="ip-desc-mat" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Descuento Colegiatura</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" min="0" id="ip-desc-col" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ip-contado">
                                    <label class="form-check-label small fw-semibold" for="ip-contado"><i class="ri-money-dollar-box-line text-success"></i> Pago al contado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ip-traspaso">
                                    <label class="form-check-label small fw-semibold" for="ip-traspaso"><i class="ri-swap-line text-warning"></i> Es traspaso de otra unidad de negocio</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                        <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                            <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#ecfdf5;color:#0ab39c;"><i class="ri-stack-line"></i></span>
                            <span>Distribución de la Colegiatura en Cuotas</span>
                        </div>
                        <div class="cuotas-resumen-box">
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:.25em .7em;border-radius:999px;border:1px solid;background:#dbeafe;color:#299cdb;border-color:#c3ddfa;"><i class="ri-bank-card-line"></i> 1<sup>er</sup> módulo: <strong>Matrícula</strong></span>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:.25em .7em;border-radius:999px;border:1px solid #c3cfe2;background:#eef1f8;color:#405189;"><i class="ri-stack-line"></i> <strong id="ip-cuotas-count">0</strong> cuota(s)</span>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:.25em .7em;border-radius:999px;border:1px solid #c3cfe2;background:#eef1f8;color:#405189;"><i class="ri-money-dollar-circle-line"></i> Cuota: <strong id="ip-cuota-entera">Bs 0</strong></span>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:.25em .7em;border-radius:999px;border:1px solid #fde68a;background:#fef3c7;color:#d97706;"><i class="ri-flag-line"></i> Última: <strong id="ip-cuota-ultima">Bs 0</strong></span>
                            </div>
                            <small class="text-muted d-block"><i class="ri-information-line"></i> El <strong>primer módulo</strong> contiene la cuota de <strong>matrícula</strong>; la <strong>colegiatura</strong> se reparte entre los módulos restantes.</small>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-soft-info" id="ip-toggle-cuotas"><i class="ri-eye-line align-bottom"></i> Ver desglose por módulo</button>
                            </div>
                            <div id="ip-cuotas-detalle" class="cuotas-detalle mt-2" style="display:none;"></div>
                            <input type="hidden" id="ip-modulos-data" value="[]">
                        </div>
                    </div>

                    {{-- 4. Comprobante de pago (opcional) --}}
                    <div style="background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                        <div style="display:flex;align-items:center;gap:.55rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#495057;margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px dashed #eef0f4;">
                            <span style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:1rem;background:#fef3c7;color:#d97706;"><i class="ri-upload-cloud-line"></i></span>
                            <span>Comprobante de pago <small style="font-weight:400;text-transform:none;letter-spacing:0;color:#878a99;">(opcional — el área contable verificará)</small></span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:4px;display:block;">Seleccionar archivo (imagen o PDF, máx. 5MB)</label>
                                <input type="file" id="ip-comprobante" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                            </div>
                            <div class="col-md-12" id="ip-deudas-container" style="display:none;">
                                <label style="font-size:.72rem;font-weight:700;color:#878a99;margin-bottom:6px;display:block;">¿Qué deudas cubre este pago?</label>
                                <div id="ip-deudas-checkboxes"></div>
                                <input type="hidden" id="ip-comprobante-monto" value="0">
                                <div style="margin-top:6px;font-size:.85rem;font-weight:700;color:#212529;">
                                    <i class="ri-money-dollar-circle-line text-success"></i> Total a pagar con este comprobante: Bs <span id="ip-total-seleccionado">0.00</span>
                                </div>
                                <small class="text-muted d-block mt-1"><i class="ri-information-line"></i> Marca las deudas que el participante está pagando con este comprobante.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cancelar</button>
                <button type="button" class="btn" id="btn-inscribir-confirm" disabled style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:700;padding:.55rem 1.4rem;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);"><i class="ri-check-line align-bottom"></i> Inscribir Participante</button>
            </div>
        </div>
    </div>
</div>

{{-- File preview modal (for publicidad comprobantes) --}}
<div class="modal fade" id="modalFilePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0;border-radius:14px;overflow:hidden;box-shadow:0 20px 50px -15px rgba(0,0,0,.3);">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);color:#fff;border:0;padding:.75rem 1.25rem;">
                <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;"><i class="ri-file-search-line me-1"></i> Vista previa del comprobante</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:#f8f9fc;">
                <div id="filePreviewContent" class="d-flex align-items-center justify-content-center text-muted" style="min-height:400px;">
                    <div class="text-center p-4"><i class="ri-loader-4-line ri-2x spin"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mesesLbl = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // Lazy: cargar la lista de inscritos del modal de programa al abrirlo
    document.addEventListener('show.bs.modal', function (e) {
        var modal = e.target;
        if (!modal || !modal.id || modal.id.indexOf('modalPerfilInsc-') !== 0) return;
        var body = modal.querySelector('[data-inscritos-body]');
        if (!body || body.dataset.loaded === '1' || !body.dataset.url) return;
        body.dataset.loaded = '1';
        fetch(body.dataset.url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                body.innerHTML = (d && d.success) ? d.html : '<div class="text-center text-danger py-4"><i class="ri-error-warning-line"></i> No se pudo cargar la lista</div>';
            })
            .catch(function () {
                body.innerHTML = '<div class="text-center text-danger py-4"><i class="ri-error-warning-line"></i> Error de conexión</div>';
            });
    });

    // Renderizar gráficos al mostrar el tab inscripciones
    const tabInsc = document.querySelector('[data-bs-target="#tab-inscripciones"]');

    function renderApexCharts() {
        if (document.getElementById('chartInscritosMes') && !document.getElementById('chartInscritosMes').dataset.rendered) {
            document.getElementById('chartInscritosMes').dataset.rendered = '1';
            const data = @json($mesesData);

            new ApexCharts(document.getElementById('chartInscritosMes'), {
                chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [
                    { name: 'Regulares', type: 'column', data: data.regulares },
                    { name: 'Contado',   type: 'column', data: data.contado },
                    { name: 'Total',     type: 'line',   data: data.total },
                ],
                xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
                colors: ['#1f6fbf', '#0d8574', '#163b6b'],
                stroke: { width: [0, 0, 3], curve: 'smooth' },
                markers: {
                    size: [0, 0, 7],
                    colors: ['#fff', '#fff', '#b7791f'],
                    strokeColors: ['#fff', '#fff', '#fff'],
                    strokeWidth: 3,
                    hover: { size: 9 }
                },
                plotOptions: {
                    bar: {
                        horizontal: false, columnWidth: '65%',
                        borderRadius: 5, borderRadiusApplication: 'end',
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: {
                    enabled: true, enabledOnSeries: [0, 1, 2],
                    formatter: function (v) { return v > 0 ? v : ''; },
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#1f6fbf', '#0d8574', '#163b6b'] },
                    background: {
                        enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                        borderWidth: 1, borderColor: '#eef0f4',
                        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                    },
                    offsetY: -8, textAnchor: 'middle'
                },
                legend: { position: 'top', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { width: 12, height: 12, radius: 3 }, itemMargin: { horizontal: 12 } },
                grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
            }).render();
        }

        const elPuntos = document.getElementById('chartPuntosMes');
        if (elPuntos && !elPuntos.dataset.rendered) {
            elPuntos.dataset.rendered = '1';
            const tipoSeries = @json($puntosTipoSeries);
            if (tipoSeries.length > 0) {
                const stackedSeries = tipoSeries.map(s => ({ name: s.name, data: s.data, type: 'column' }));
                const totalData = Array.from({ length: 12 }, (_, i) =>
                    tipoSeries.reduce((sum, s) => sum + (s.data[i] || 0), 0)
                );
                const allSeries = [...stackedSeries, { name: 'Total', data: totalData, type: 'line' }];
                const colorsArr = [...tipoSeries.map(s => s.color), '#163b6b'];
                new ApexCharts(elPuntos, {
                    chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                    series: allSeries,
                    colors: colorsArr,
                    xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                    yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Puntos', style: { fontSize: '11px', fontWeight: 600 } } },
                    stroke: { width: [tipoSeries.map(() => 0), 3].flat(), curve: 'smooth' },
                    markers: {
                        size: [tipoSeries.map(() => 0), 6].flat(),
                        colors: ['#fff'],
                        strokeColors: ['#163b6b'],
                        strokeWidth: 2,
                        hover: { size: 8 }
                    },
                    plotOptions: {
                        bar: { horizontal: false, columnWidth: '60%', dataLabels: { position: 'top' } }
                    },
                    dataLabels: {
                        enabled: true, enabledOnSeries: [allSeries.length - 1],
                        formatter: function (v) { return v > 0 ? v.toLocaleString('es-BO') : ''; },
                        style: { fontSize: '10px', fontWeight: 800, colors: ['#163b6b'] },
                        background: {
                            enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                            borderWidth: 1, borderColor: '#eef0f4',
                            dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                        },
                        offsetY: -8, textAnchor: 'middle'
                    },
                    legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
                    grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                    tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v.toLocaleString('es-BO') + ' pts'; } } },
                }).render();
            } else {
                elPuntos.innerHTML = '<div class="text-muted text-center py-5"><i class="ri-inbox-2-line ri-2x mb-2 d-block"></i>Sin datos de puntos</div>';
            }
        }

        const elPuntosReales = document.getElementById('chartPuntosRealesMes');
        if (elPuntosReales && !elPuntosReales.dataset.rendered) {
            elPuntosReales.dataset.rendered = '1';
            const tipoSeriesReales = @json($puntosRealesTipoSeries);
            if (tipoSeriesReales.length > 0) {
                const stackedSeriesReales = tipoSeriesReales.map(s => ({ name: s.name, data: s.data, type: 'column' }));
                const totalDataReales = Array.from({ length: 12 }, (_, i) =>
                    tipoSeriesReales.reduce((sum, s) => sum + (s.data[i] || 0), 0)
                );
                const allSeriesReales = [...stackedSeriesReales, { name: 'Total', data: totalDataReales, type: 'line' }];
                const colorsArrReales = [...tipoSeriesReales.map(s => s.color), '#0d8574'];
                new ApexCharts(elPuntosReales, {
                    chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                    series: allSeriesReales,
                    colors: colorsArrReales,
                    xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                    yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Puntos Reales', style: { fontSize: '11px', fontWeight: 600 } } },
                    stroke: { width: [tipoSeriesReales.map(() => 0), 3].flat(), curve: 'smooth' },
                    markers: {
                        size: [tipoSeriesReales.map(() => 0), 6].flat(),
                        colors: ['#fff'],
                        strokeColors: ['#0d8574'],
                        strokeWidth: 2,
                        hover: { size: 8 }
                    },
                    plotOptions: {
                        bar: { horizontal: false, columnWidth: '60%', dataLabels: { position: 'top' } }
                    },
                    dataLabels: {
                        enabled: true, enabledOnSeries: [allSeriesReales.length - 1],
                        formatter: function (v) { return v > 0 ? v.toLocaleString('es-BO') : ''; },
                        style: { fontSize: '10px', fontWeight: 800, colors: ['#0d8574'] },
                        background: {
                            enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                            borderWidth: 1, borderColor: '#eef0f4',
                            dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                        },
                        offsetY: -8, textAnchor: 'middle'
                    },
                    legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
                    grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                    tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v.toLocaleString('es-BO') + ' pts'; } } },
                }).render();
            } else {
                elPuntosReales.innerHTML = '<div class="text-muted text-center py-5"><i class="ri-inbox-2-line ri-2x mb-2 d-block"></i>Sin datos de puntos reales</div>';
            }
        }

        const elReales = document.getElementById('chartRealesMes');
        if (elReales && !elReales.dataset.rendered) {
            elReales.dataset.rendered = '1';
            const seriesRealesTipo = @json($realesInscritosTipoSeries);
            const seriesNoRealesTipo = @json($noRealesInscritosTipoSeries);
            const totalInsc = @json($mesesData['total']);

            function lightenHex(hex, pct) {
                const r = parseInt(hex.slice(1,3), 16), g = parseInt(hex.slice(3,5), 16), b = parseInt(hex.slice(5,7), 16);
                return '#' + [r,g,b].map(c => Math.min(255, Math.round(c + (255 - c) * pct)).toString(16).padStart(2,'0')).join('');
            }

            const realesGroup = seriesRealesTipo.map(s => ({ name: 'Reales - ' + s.name, data: s.data, type: 'column', group: 'Reales', color: s.color }));
            const noRealesGroup = seriesNoRealesTipo.map(s => ({ name: 'No Reales - ' + s.name, data: s.data, type: 'column', group: 'No Reales', color: lightenHex(s.color, 0.55) }));
            const allRealesSeries = [...realesGroup, ...noRealesGroup, { name: 'Total', type: 'line', data: totalInsc }];
            new ApexCharts(elReales, {
                chart: { height: 300, toolbar: { show: false }, fontFamily: 'inherit', stacked: true },
                series: allRealesSeries,
                xaxis: { categories: mesesLbl, labels: { style: { fontSize: '11px', fontWeight: 500 } } },
                yaxis: { labels: { style: { fontSize: '11px' } }, min: 0, forceNiceScale: true, title: { text: 'Inscritos', style: { fontSize: '11px', fontWeight: 600 } } },
                stroke: { width: [...realesGroup.map(() => 0), ...noRealesGroup.map(() => 0), 3], curve: 'smooth' },
                markers: {
                    size: [...realesGroup.map(() => 0), ...noRealesGroup.map(() => 0), 6],
                    colors: ['#fff'],
                    strokeColors: ['#163b6b'],
                    strokeWidth: 2,
                    hover: { size: 8 }
                },
                plotOptions: {
                    bar: { horizontal: false, columnWidth: '60%', dataLabels: { position: 'top' } }
                },
                dataLabels: {
                    enabled: true, enabledOnSeries: [allRealesSeries.length - 1],
                    formatter: function (v) { return v > 0 ? v : ''; },
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#163b6b'] },
                    background: {
                        enabled: true, foreColor: '#fff', borderRadius: 4, padding: 4, opacity: 1,
                        borderWidth: 1, borderColor: '#eef0f4',
                        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: .15 }
                    },
                    offsetY: -8, textAnchor: 'middle'
                },
                legend: { position: 'top', horizontalAlign: 'center', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 2 }, itemMargin: { horizontal: 8 } },
                grid: { borderColor: '#f1f3f5', strokeDashArray: 4 },
                tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' inscritos'; } } },
            }).render();
        }
    }

    if (tabInsc) tabInsc.addEventListener('shown.bs.tab', renderApexCharts);

    // ===== Publicidad tab: load content =====
    function cargarPublicidad() {
        const content = document.getElementById('pub-content');
        if (!content) return;
        content.dataset.loaded = '1';
        fetch('{{ route("admin.profile.publicidad-data") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
                initComprobantesPagination();
            } else {
                content.innerHTML = data.html || '<div class="text-center text-muted py-5">Error al cargar los datos.</div>';
            }
        })
        .catch(() => {
            content.innerHTML = '<div class="text-center text-danger py-5">Error de conexión.</div>';
        });
    }
    const tabPub = document.querySelector('[data-bs-target="#tab-publicidad"]');
    if (tabPub) {
        tabPub.addEventListener('shown.bs.tab', function () {
            const content = document.getElementById('pub-content');
            if (content.dataset.loaded) return;
            cargarPublicidad();
        });
    }

    // Pagination (client-side, 10 records) for the Comprobantes tab
    function initComprobantesPagination() {
        const tbody = document.getElementById('tbodyComprobantes');
        if (!tbody) return;
        const pagesEl = document.getElementById('pubPagPages');
        const infoEl = document.getElementById('pubPagInfo');
        if (!pagesEl || !infoEl) return;

        const rows = Array.from(tbody.querySelectorAll('tr'));
        const total = rows.length;
        const perPage = 10;
        const pages = Math.max(1, Math.ceil(total / perPage));
        let current = 1;

        function render() {
            rows.forEach((row, i) => {
                row.style.display = (i >= (current - 1) * perPage && i < current * perPage) ? '' : 'none';
            });
            const from = total === 0 ? 0 : ((current - 1) * perPage) + 1;
            const to = Math.min(current * perPage, total);
            infoEl.innerHTML = 'Mostrando <strong>' + from + '–' + to + '</strong> de <strong>' + total + '</strong> comprobantes';

            pagesEl.innerHTML = '';
            const li = (label, cls, handler, disabled) => {
                const item = document.createElement('li');
                item.className = 'page-item' + (cls ? ' ' + cls : '') + (disabled ? ' disabled' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = 'javascript:void(0)';
                a.innerHTML = label;
                if (!disabled) a.addEventListener('click', handler);
                item.appendChild(a);
                pagesEl.appendChild(item);
            };

            li('<i class="ri-arrow-left-s-line"></i>', '', () => { current--; render(); }, current === 1);
            const start = Math.max(1, current - 2);
            const end = Math.min(pages, current + 2);
            for (let p = start; p <= end; p++) {
                li(String(p), p === current ? 'active' : '', () => { current = p; render(); });
            }
            li('<i class="ri-arrow-right-s-line"></i>', '', () => { current++; render(); }, current === pages);
        }

        render();
    }
    // Force reload if redirected back from comprobante upload
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('reload_pub')) {
        const content = document.getElementById('pub-content');
        if (content) {
            content.dataset.loaded = '';
            if (tabPub) {
                var tab = new bootstrap.Tab(tabPub);
                tab.show();
            }
            cargarPublicidad();
        }
        // Clean URL without reloading
        if (window.history.replaceState) {
            var cleanUrl = window.location.pathname + window.location.hash;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    // File preview modal (delegated, works for AJAX-loaded content)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-preview-file');
        if (!btn) return;
        e.preventDefault();
        const url = btn.dataset.url;
        // La extensión viene en data-ext: la URL ya no la termina en .pdf/.jpg
        // porque los archivos de autogestión se sirven por ruta autenticada.
        const ext = (btn.dataset.ext || url.split('.').pop() || '').toLowerCase();
        const content = document.getElementById('filePreviewContent');
        if (['jpg','jpeg','png','webp'].includes(ext)) {
            content.innerHTML = '<img src="' + url + '" class="img-fluid" style="max-height:80vh;object-fit:contain;" alt="Comprobante">';
        } else if (ext === 'pdf') {
            content.innerHTML = '<iframe src="' + url + '" class="w-100" style="height:80vh;border:none;"></iframe>';
        } else {
            content.innerHTML = '<div class="text-center p-4"><i class="ri-file-download-line ri-2x"></i><p class="mt-2"><a href="' + url + '" target="_blank" class="btn btn-primary"><i class="ri-download-line me-1"></i>Descargar archivo</a></p></div>';
        }
        const modal = new bootstrap.Modal(document.getElementById('modalFilePreview'));
        modal.show();
    });
    document.getElementById('modalFilePreview')?.addEventListener('hidden.bs.modal', function () {
        const el = document.getElementById('filePreviewContent');
        if (el) el.innerHTML = '<div class="text-center p-4"><i class="ri-loader-4-line ri-2x spin"></i><p class="mt-2 mb-0">Cargando archivo...</p></div>';
    });

    // Periodo toggle for publicidad tab (delegated, works for AJAX-loaded content)
    document.addEventListener('click', function (e) {
        const header = e.target.closest('.periodo-header');
        if (!header) return;
        const detalle = header.nextElementSibling;
        if (!detalle || !detalle.classList.contains('periodo-detalle')) return;
        const icon = header.querySelector('.periodo-toggle i');
        if (detalle.style.display === 'none') {
            detalle.style.display = '';
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            detalle.style.display = 'none';
            if (icon) icon.style.transform = '';
        }
    });

    // Dynamic upload modal: set presupuesto_id, period info, and date range
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-bs-target="#modalPubSubir"]');
        if (!btn) return;
        const presupuestoId = btn.getAttribute('data-presupuesto-id');
        const periodo = btn.getAttribute('data-periodo');
        const gestion = btn.getAttribute('data-gestion');
        const mes = btn.getAttribute('data-mes');
        const quincena = btn.getAttribute('data-quincena');
        const monto = btn.getAttribute('data-monto');

        // Set hidden fields
        setVal('modalPubPresupuestoId', presupuestoId);
        setVal('modalPubGestion', gestion);
        setVal('modalPubMes', mes);
        setVal('modalPubQuincena', quincena);

        // Compute date range for the period
        var minDate = '', maxDate = '';
        if (gestion && mes && quincena) {
            var year = parseInt(gestion), month = parseInt(mes) - 1; // JS months are 0-indexed
            var lastDay = new Date(year, month + 1, 0).getDate();
            if (quincena === '1') {
                var firstDay = new Date(year, month, 1);
                firstDay.setDate(firstDay.getDate() - 2);
                minDate = firstDay.getFullYear() + '-' + String(firstDay.getMonth() + 1).padStart(2,'0') + '-' + String(firstDay.getDate()).padStart(2,'0');
                maxDate = year + '-' + String(parseInt(mes)).padStart(2,'0') + '-15';
            } else {
                minDate = year + '-' + String(parseInt(mes)).padStart(2,'0') + '-14';
                maxDate = year + '-' + String(parseInt(mes)).padStart(2,'0') + '-' + String(lastDay).padStart(2,'0');
            }
        }
        var fechaInicio = document.querySelector('[name=fecha_inicio]');
        var fechaFin = document.querySelector('[name=fecha_fin]');
        if (fechaInicio) { fechaInicio.setAttribute('min', minDate); fechaInicio.setAttribute('max', maxDate); }
        if (fechaFin) { fechaFin.setAttribute('min', minDate); fechaFin.setAttribute('max', maxDate); }

        // Period info display
        var info = document.getElementById('modalPubPeriodoInfo');
        var data = document.getElementById('modalPubPeriodoData');
        if (info && data && periodo && quincena) {
            info.style.display = '';
            data.innerHTML =
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:#fff;border-radius:999px;font-size:.82rem;font-weight:700;color:#1e40af;border:1px solid #c3ddfa;">' +
                    '<i class="ri-calendar-line"></i> ' + periodo +
                '</span>' +
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:' + (quincena === '1' ? '#dbeafe' : '#fef3c7') + ';border-radius:999px;font-size:.82rem;font-weight:700;color:' + (quincena === '1' ? '#1e40af' : '#92400e') + ';border:1px solid ' + (quincena === '1' ? '#bae6fd' : '#fde68a') + ';">' +
                    '<i class="ri-calendar-2-line"></i> Q' + quincena + ' (' + (quincena === '1' ? '1-15' : '16-fin') + ')' +
                '</span>' +
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:#fff;border-radius:999px;font-size:.82rem;font-weight:700;color:#405189;border:1px solid #c3cfe2;">' +
                    '<i class="ri-wallet-3-line"></i> Disponible: Bs ' + (parseFloat(monto) || 0).toFixed(2) +
                '</span>';
        } else if (info) {
            info.style.display = 'none';
        }
    });

    // Dynamic derivation modal: set presupuesto_id, period info, and max amount
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-bs-target="#modalPubDerivar"]');
        if (!btn) return;
        const presupuestoId = btn.getAttribute('data-presupuesto-id');
        const periodo = btn.getAttribute('data-periodo');
        const gestion = btn.getAttribute('data-gestion');
        const mes = btn.getAttribute('data-mes');
        const quincena = btn.getAttribute('data-quincena');
        const disponible = btn.getAttribute('data-disponible');

        setVal('modalDerivarPresupuestoId', presupuestoId);
        setVal('modalDerivarGestion', gestion);
        setVal('modalDerivarMes', mes);
        setVal('modalDerivarQuincena', quincena);
        var montoInput = document.getElementById('modalDerivarMonto');
        if (montoInput) {
            montoInput.max = disponible;
            montoInput.placeholder = 'Máx. Bs ' + (parseFloat(disponible) || 0).toFixed(2);
        }

        var info = document.getElementById('modalDerivarPeriodoInfo');
        var data = document.getElementById('modalDerivarPeriodoData');
        if (info && data && periodo && quincena) {
            info.style.display = '';
            data.innerHTML =
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:#fff;border-radius:999px;font-size:.82rem;font-weight:700;color:#1e40af;border:1px solid #c3ddfa;">' +
                    '<i class="ri-calendar-line"></i> ' + periodo +
                '</span>' +
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:' + (quincena === '1' ? '#dbeafe' : '#fef3c7') + ';border-radius:999px;font-size:.82rem;font-weight:700;color:' + (quincena === '1' ? '#1e40af' : '#92400e') + ';border:1px solid ' + (quincena === '1' ? '#bae6fd' : '#fde68a') + ';">' +
                    '<i class="ri-calendar-2-line"></i> Q' + quincena + ' (' + (quincena === '1' ? '1-15' : '16-fin') + ')' +
                '</span>' +
                '<span style="display:inline-flex;align-items:center;gap:6px;padding:.35rem .9rem;background:#fff;border-radius:999px;font-size:.82rem;font-weight:700;color:#405189;border:1px solid #c3cfe2;">' +
                    '<i class="ri-wallet-3-line"></i> Disponible: Bs ' + (parseFloat(disponible) || 0).toFixed(2) +
                '</span>';
        } else if (info) {
            info.style.display = 'none';
        }
    });

    function setVal(id, val) {
        var el = document.getElementById(id);
        if (el) el.value = val || '';
    }

    document.querySelectorAll('.toggle-pwd').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            this.querySelector('i').classList.toggle('ri-eye-fill');
            this.querySelector('i').classList.toggle('ri-eye-off-fill');
        });
    });

    // ===== Responsable: programas + seguimiento semanal =====
    @if($responsable)
    (function () {
        var respId = {{ $responsable->id }};
        var respColor = '{{ $responsable->color ? "#" . ltrim($responsable->color, "#") : "#405189" }}';
        var content = document.getElementById('prof-resp-content');
        var activeSubTab = 'prof-programas';

        function escHtml(s) { return String(s ?? '').replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

        function estadoClass(e) {
            return {
                'Concluido': 'is-done',
                'En Desarrollo': 'is-dev',
                'No Inicio': 'is-idle',
                'Pendiente': 'is-pend',
                'Suspendido': 'is-susp'
            }[e] || 'is-idle';
        }

        // --- Cargar Programas ---
        function loadProgramas() {
            content.innerHTML = '<div class="prof-loading"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 mb-0">Cargando programas...</p></div>';
            var url = '{{ url("admin/responsables") }}/' + respId + '/detalle';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) { content.innerHTML = '<div class="prof-error">No se pudieron cargar los programas.</div>'; return; }
                renderProgramas(data);
            })
            .catch(function () { content.innerHTML = '<div class="prof-error">Error de conexión.</div>'; });
        }

        function renderProgramas(data) {
            var r = data.responsable;
            var ini = ((r.nombres||'').charAt(0)+(r.apellidos||'').charAt(0)).toUpperCase();
            var res = data.resumen;
            var respHex = normalizeHex(respColor) || '#405189';
            var heroLight = hexLuminance(respHex) > 0.5;
            var html =
                '<div class="prof-resp-hero' + (heroLight ? ' is-light' : '') + '" style="--prof-accent:' + respHex + ';--prof-accent-2:' + shadeColor(respHex, heroLight ? -32 : -20) + ';">' +
                    '<div class="d-flex align-items-center gap-3 flex-wrap">' +
                        '<div class="prof-resp-avatar">' + escHtml(ini) + '</div>' +
                        '<div style="min-width:0;">' +
                            '<h3 class="prof-resp-name">' + escHtml(r.apellidos) + ', ' + escHtml(r.nombres) + '</h3>' +
                            '<div class="prof-resp-cid"><i class="ri-passport-line"></i> CI: ' + escHtml(r.ci) + (r.correo ? ' &nbsp;·&nbsp; <i class="ri-mail-line"></i> ' + escHtml(r.correo) : '') + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="prof-resp-chips">' +
                        '<span class="prof-resp-chip"><i class="ri-booklet-line"></i> <strong>' + res.total_programas + '</strong> programa' + (res.total_programas !== 1 ? 's' : '') + '</span>' +
                        '<span class="prof-resp-chip"><i class="ri-stack-line"></i> <strong>' + res.total_modulos + '</strong> módulo' + (res.total_modulos !== 1 ? 's' : '') + '</span>' +
                        '<span class="prof-resp-chip"><i class="ri-checkbox-circle-line"></i> <strong>' + res.concluidos + '</strong> concluidos</span>' +
                        '<span class="prof-resp-chip"><i class="ri-loader-4-line"></i> <strong>' + res.en_desarrollo + '</strong> en desarrollo</span>' +
                        '<span class="prof-resp-chip"><i class="ri-error-warning-line"></i> <strong>' + res.no_inicio + '</strong> sin iniciar</span>' +
                    '</div>' +
                '</div>';

            if (!data.tipos || data.tipos.length === 0) {
                html += '<div class="prof-empty"><div class="prof-empty-ic"><i class="ri-inbox-2-line"></i></div><h5>Sin programas asignados</h5><p class="mb-0">No tenés programas vinculados en esta gestión.</p></div>';
                content.innerHTML = html;
                return;
            }

            // Filter tipos that have programs
            var tiposConProg = data.tipos.filter(function (t) { return t.programas && t.programas.length > 0; });

            // Tipo tabs
            html += '<div class="prof-tipo-tabs" id="prof-tipo-tabs">';
            tiposConProg.forEach(function (t, idx) {
                html += '<button type="button" class="prof-tipo-tab' + (idx === 0 ? ' active' : '') + '" data-tipo-target="prof-pane-' + t.tipo_id + '">' +
                    '<i class="ri-price-tag-3-line"></i> ' + escHtml(t.tipo_nombre) +
                    '<span class="cnt">' + t.programas.length + '</span>' +
                '</button>';
            });
            html += '</div>';

            // Secciones (todas visibles, sin ocultar contenido)
            html += '<div class="prof-tipo-panes" id="prof-tipo-panes">';
            tiposConProg.forEach(function (t, idx) {
                html += '<section class="prof-tipo-pane" id="prof-pane-' + t.tipo_id + '">';
                html += '<div class="prof-tipo-sec-head"><span class="prof-tipo-sec-icon"><i class="ri-price-tag-3-line"></i></span> ' + escHtml(t.tipo_nombre) + ' <span class="prof-tipo-sec-count">' + t.programas.length + '</span></div>';
                html += '<div class="prof-tipo-list">';
                t.programas.forEach(function (p, pidx) {
                    var mr = p.modulos_resumen;
                    var progUrl = '{{ url("admin/programas") }}/' + p.id + '/show';
                    var progMoodle = p.moodle_link
                        ? '<a href="' + escHtml(p.moodle_link) + '" target="_blank" rel="noopener" class="prof-moodle-link" onclick="event.stopPropagation();" title="Abrir aula Moodle"><i class="ri-graduation-cap-line"></i></a>'
                        : '';
                    html += '<div class="prof-prog is-open">' +
                        '<div class="prof-prog-toggle" data-acc-toggle role="button" tabindex="0">' +
                            '<span class="prof-prog-icon"><i class="ri-graduation-cap-line"></i></span>' +
                            '<div style="flex:1;min-width:200px;">' +
                                '<h6 class="prof-prog-name">' +
                                    '<a href="' + progUrl + '" class="prof-prog-link" onclick="event.stopPropagation();" title="Ver detalle del programa" target="_blank">' + escHtml(p.nombre) + '</a>' +
                                    progMoodle +
                                '</h6>' +
                                '<div class="prof-prog-meta"><span><i class="ri-hashtag"></i>' + escHtml(p.codigo) + '</span><span>·</span><span><i class="ri-calendar-line"></i> Gestión ' + escHtml(p.gestion) + '</span><span>·</span><span><i class="ri-team-line"></i> ' + p.inscritos + ' inscritos</span></div>' +
                            '</div>' +
                            '<div class="prof-prog-prog"><div class="prof-prog-bar"><span style="width:' + p.progreso_pct + '%;"></span></div><span class="prof-prog-pct">' + p.progreso_pct + '%</span></div>' +
                            '<span class="prof-prog-chevron" style="transform:rotate(180deg);"><i class="ri-arrow-down-s-line"></i></span>' +
                        '</div>' +
                        '<div class="prof-prog-body" style="display:block;">' +
                            '<div class="prof-chip-row">' +
                                '<span class="prof-chip is-total"><i class="ri-stack-line"></i> <strong>' + mr.total + '</strong> módulos</span>' +
                                '<span class="prof-chip is-ok"><i class="ri-checkbox-circle-line"></i> <strong>' + mr.concluido + '</strong> concluidos</span>' +
                                '<span class="prof-chip is-dev"><i class="ri-loader-4-line"></i> <strong>' + mr.en_desarrollo + '</strong> en desarrollo</span>' +
                                '<span class="prof-chip is-none"><i class="ri-time-line"></i> <strong>' + mr.no_inicio + '</strong> sin iniciar</span>' +
                            '</div>' +
                            '<div style="display:flex;flex-direction:column;gap:.35rem;">';
                    if (p.modulos.length === 0) {
                        html += '<div class="prof-empty" style="padding:1.25rem;"><i class="ri-inbox-2-line"></i> Sin módulos registrados</div>';
                    } else {
                        p.modulos.forEach(function (m, midx) {
                            var c = estadoClass(m.desarrollo);
                            var enCurso = m.en_curso || m.reciente || m.proximo;
                            var rowClass = 'prof-mod' + (enCurso ? (m.proximo ? ' is-next' : ' is-live') : '');
                            var modMoodle = m.moodle_link
                                ? '<a href="' + escHtml(m.moodle_link) + '" target="_blank" rel="noopener" class="prof-moodle-link" title="Abrir aula Moodle del módulo"><i class="ri-graduation-cap-line"></i></a>'
                                : '';
                            var docenteHtml;
                            if (m.docente_nombres && m.docente_id) {
                                var docUrl = '{{ url("admin/docentes") }}/' + m.docente_id + '/show';
                                docenteHtml = '<i class="ri-user-star-line"></i> <a href="' + docUrl + '" class="prof-docente-link" target="_blank" title="Ver docente">' + escHtml(m.docente_apellidos) + ', ' + escHtml(m.docente_nombres) + '</a>';
                            } else if (m.docente_nombres) {
                                docenteHtml = '<i class="ri-user-star-line"></i> ' + escHtml(m.docente_apellidos) + ', ' + escHtml(m.docente_nombres);
                            } else {
                                docenteHtml = '<i class="ri-user-unfollow-line"></i> <span class="fst-italic">Sin docente asignado</span>';
                            }
                            var horariosBtn = '<button type="button" class="prof-horarios-btn" data-modulo-id="' + m.id + '" data-modulo-nombre="' + escHtml(m.nombre) + '" title="Ver horarios del módulo"><i class="ri-calendar-event-line"></i> Horarios</button>';
                            var textoCurso = m.proximo ? 'Próximo' : (m.reciente ? 'Recientemente en curso' : 'En curso');
                            var cursoPill = enCurso ? '<span class="prof-tag ' + (m.proximo ? 'is-next' : 'is-live') + '">' + textoCurso + '</span>' : '';
                            var modUrl = '{{ url("admin/programas/modulo") }}/' + m.id + '/detalle';
                            html += '<div class="' + rowClass + '">' +
                                '<span class="prof-mod-num">' + (midx + 1) + '</span>' +
                                '<div style="flex:1;min-width:0;"><div class="prof-mod-name">' +
                                    '<a href="' + modUrl + '" class="prof-prog-link" title="Ver detalle del módulo" target="_blank">' + escHtml(m.nombre) + '</a>' +
                                    modMoodle + cursoPill +
                                '</div><div class="prof-mod-meta">' + docenteHtml + '</div></div>' +
                                '<span class="prof-mod-estado ' + c + '">' + escHtml(m.desarrollo) + '</span>' +
                                horariosBtn +
                            '</div>';
                        });
                    }
                    html += '</div></div></div>';
                });
                html += '</div></div>';
            });
            html += '</div>';

            content.innerHTML = html;
            bindProfAccordion();
            bindProfTipoTabs();
            bindProfHorarios();
        }

        function bindProfHorarios() {
            content.querySelectorAll('.prof-horarios-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var moduloId = this.dataset.moduloId;
                    var moduloNombre = this.dataset.moduloNombre;
                    var modalEl = document.getElementById('profModuloHorariosModal');
                    var titleEl = document.getElementById('profModuloHorariosTitle');
                    var bodyEl = document.getElementById('profModuloHorariosBody');
                    if (!modalEl || !bodyEl) return;
                    titleEl.innerHTML = '<i class="ri-calendar-event-line"></i> Horarios — ' + escHtml(moduloNombre);
                    bodyEl.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando horarios...</p></div>';
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (!modal) modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    var url = '{{ url("admin/cronogramas/modulo") }}/' + moduloId + '/horarios';
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.horarios) {
                            bodyEl.innerHTML = renderModuloHorarios(data);
                            bindAsistenciaButtons();
                        } else {
                            bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error al cargar horarios</p></div>';
                        }
                    })
                    .catch(function () {
                        bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error de conexión</p></div>';
                    });
                });
            });
        }

        function bindAsistenciaButtons() {
            document.querySelectorAll('.prof-ver-asistencia').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var horarioId = this.dataset.horarioId;
                    var fechaTxt = this.dataset.horarioFecha;
                    // Cerrar modal de horarios primero
                    var horariosModal = document.getElementById('profModuloHorariosModal');
                    if (horariosModal) { var hm = bootstrap.Modal.getInstance(horariosModal); if (hm) hm.hide(); }
                    // Abrir modal de asistencia
                    setTimeout(function () {
                        var modalEl = document.getElementById('sdModal');
                        if (!modalEl) return;
                        modalEl.classList.remove('is-late','is-warn','is-info');
                        document.getElementById('sdModalTitle').textContent = 'Asistencia — ' + fechaTxt;
                        document.getElementById('sdModalSubtitle').textContent = 'Lista de participantes registrados';
                        var bodyEl = document.getElementById('sdModalBody');
                        bodyEl.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando asistencia...</p></div>';
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (!modal) modal = new bootstrap.Modal(modalEl);
                        modal.show();

                    var url = '{{ url("admin/programas/horario") }}/' + horarioId + '/asistencia';
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) {
                            bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line"></i><p class="mt-2">' + (data.message || 'Error al cargar') + '</p></div>';
                            return;
                        }
                        var parts = data.participantes || [];
                        if (parts.length === 0) {
                            bodyEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="ri-inbox-2-line fs-2"></i><p class="mt-2">No hay participantes inscritos en este módulo.</p></div>';
                            return;
                        }
                        var html = '<div style="margin-bottom:.75rem;font-size:.78rem;color:#878a99;"><i class="ri-team-line"></i> ' + parts.length + ' participante' + (parts.length !== 1 ? 's' : '') + '</div>';
                        html += '<div class="table-responsive" style="border:1px solid #eef0f5;border-radius:8px;"><table class="sd-asist-table"><thead><tr>' +
                            '<th style="width:34px;">#</th><th style="width:90px;">CI</th><th>Participante</th><th style="width:110px;">Asistencia</th><th>Observación</th>' +
                        '</tr></thead><tbody>';
                        parts.forEach(function (p, idx) {
                            var aCls = 'a-';
                            if (p.asistio === 'Presente') aCls += 'Presente';
                            else if (p.asistio === 'Atraso') aCls += 'Atraso';
                            else if (p.asistio === 'Falta') aCls += 'Falta';
                            else if (p.asistio === 'Justificado') aCls += 'Justificado';
                            else aCls = '';
                            var estTxt = p.asistio || '<span class="text-muted fst-italic" style="font-size:.72rem;">Sin registro</span>';
                            html += '<tr>' +
                                '<td class="text-muted">' + (idx + 1) + '</td>' +
                                '<td>' + escHtml(p.ci) + '</td>' +
                                '<td>' + escHtml(p.apellidos + ', ' + p.nombres) + '</td>' +
                                '<td>' + (aCls ? '<span class="a-pill ' + aCls + '">' + p.asistio + '</span>' : estTxt) + '</td>' +
                                '<td class="text-muted">' + (p.observacion ? escHtml(p.observacion) : '—') + '</td>' +
                            '</tr>';
                        });
                        html += '</tbody></table></div>';
                        bodyEl.innerHTML = html;
                        // Fix footer link
                        var gotoEl = document.getElementById('sdModalGoto');
                        if (gotoEl) { gotoEl.removeAttribute('href'); gotoEl.style.display = 'none'; }
                    })
                    .catch(function () {
                        bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error de conexión</p></div>';
                    });
                });
            });
            });
        }

        function renderModuloHorarios(data) {
            var horarios = data.horarios || [];
            if (horarios.length === 0) {
                return '<div class="text-center py-5 text-muted"><i class="ri-inbox-2-line fs-2"></i><p class="mt-2">Este módulo no tiene sesiones/horarios registrados.</p></div>';
            }
            var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            var diasSem = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
            var html = '<div style="margin-bottom:.75rem;font-size:.78rem;color:#878a99;"><i class="ri-information-line"></i> ' + horarios.length + ' sesión' + (horarios.length !== 1 ? 'es' : '') + ' programada' + (horarios.length !== 1 ? 's' : '') + '</div>';
            horarios.forEach(function (h) {
                var fechaTxt = '—';
                if (h.fecha_desarrollo) {
                    var d = new Date(h.fecha_desarrollo + 'T12:00:00');
                    fechaTxt = diasSem[d.getDay()] + ' ' + d.getDate() + ' de ' + meses[d.getMonth()] + ' del ' + d.getFullYear();
                }
                var hInicio = h.hora_inicio ? h.hora_inicio.substring(0,5) : '—';
                var hFin = h.hora_fin ? h.hora_fin.substring(0,5) : '—';
                var estado = h.estado || '—';
                var estCls = '';
                var asistenciaBtn = '';
                if (estado === 'Desarrollado') {
                    estCls = 'background:#d4f6ee;color:#0a7059;';
                    asistenciaBtn = '<button type="button" class="prof-ver-asistencia" data-horario-id="' + h.id + '" data-horario-fecha="' + escHtml(fechaTxt) + '" title="Ver lista de asistencia"><i class="ri-team-line"></i> Asistencia</button>';
                } else if (estado === 'Confirmado' || estado === 'Programado') estCls = 'background:#dde7ff;color:#1c3e9e;';
                else if (estado === 'En Desarrollo') estCls = 'background:#cfe7ff;color:#0b5ed7;';
                else if (estado === 'No Inicio' || estado === 'Pendiente') estCls = 'background:#fff4d6;color:#8a6100;';
                else if (estado === 'Postergado') estCls = 'background:#fff4d6;color:#8a6100;';
                else if (estado === 'Suspendido') estCls = 'background:#ffd9d3;color:#b1240f;';
                else estCls = 'background:#eef0f5;color:#5d6478;';
                html += '<div class="prof-horario-row">' +
                    '<span class="h-date">' + fechaTxt + '</span>' +
                    '<span class="h-time">' + hInicio + ' — ' + hFin + '</span>' +
                    '<span class="h-estado" style="' + estCls + '">' + escHtml(estado) + '</span>' +
                    (asistenciaBtn ? '<span>' + asistenciaBtn + '</span>' : '<span></span>') +
                '</div>';
            });
            return html;
        }

        function bindProfTipoTabs() {
            var tabs = content.querySelectorAll('.prof-tipo-tab');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) { t.classList.remove('active'); });
                    this.classList.add('active');
                    var target = document.getElementById(this.dataset.tipoTarget);
                    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        }

        function bindProfAccordion() {
            content.querySelectorAll('[data-acc-toggle]').forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    var item = this.closest('.prof-prog');
                    if (!item) return;
                    var accordion = item.parentElement;
                    var wasOpen = item.classList.contains('is-open');
                    accordion.querySelectorAll('.prof-prog').forEach(function (i) {
                        i.classList.remove('is-open');
                        var body = i.querySelector('.prof-prog-body');
                        if (body) { body.style.display = 'none'; }
                        var ch = i.querySelector('.prof-prog-chevron');
                        if (ch) { ch.style.transform = ''; }
                    });
                    if (!wasOpen) {
                        item.classList.add('is-open');
                        var body = item.querySelector('.prof-prog-body');
                        if (body) { body.style.display = 'block'; }
                        var ch = item.querySelector('.prof-prog-chevron');
                        if (ch) { ch.style.transform = 'rotate(180deg)'; }
                    }
                });
            });
        }

        // --- Cargar Seguimiento ---
        function loadSeguimiento(inicio) {
            content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando seguimiento semanal...</p></div>';
            var url = '{{ url("admin/responsables") }}/' + respId + '/seguimiento-semanal';
            if (inicio) url += '?inicio=' + inicio;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    content.innerHTML = data.html;
                    bindProfSeguimientoEvents();
                } else {
                    content.innerHTML = '<div class="text-center py-5 text-danger">Error al cargar</div>';
                }
            })
            .catch(function () { content.innerHTML = '<div class="text-center py-5 text-danger">Error de conexión</div>'; });
        }

        function bindProfSeguimientoEvents() {
            content.querySelectorAll('.sd-open-modal').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = this.getAttribute('data-horario-id');
                    var kind = this.getAttribute('data-modal-kind') || '';
                    var ttl = this.getAttribute('data-modal-title') || '';
                    var stl = this.getAttribute('data-modal-subtitle') || '';
                    var tpl = document.getElementById('sdTpl_' + id);
                    if (!tpl) return;
                    var KIND_CLASSES = ['is-late','is-warn','is-info'];
                    var modalEl = document.getElementById('sdModal');
                    if (!modalEl) return;
                    KIND_CLASSES.forEach(function (c) { modalEl.classList.remove(c); });
                    if (kind) modalEl.classList.add(kind);
                    document.getElementById('sdModalTitle').textContent = ttl;
                    document.getElementById('sdModalSubtitle').textContent = stl;
                    var bodyEl = document.getElementById('sdModalBody');
                    bodyEl.innerHTML = '';
                    var clone = tpl.cloneNode(true);
                    clone.classList.remove('sd-tpl');
                    clone.removeAttribute('id');
                    clone.style.display = '';
                    bodyEl.appendChild(clone);
                    var gotoEl = document.getElementById('sdModalGoto');
                    var progLink = bodyEl.querySelector('.sd-info-value a[href]');
                    if (progLink) { gotoEl.href = progLink.getAttribute('href'); gotoEl.style.display = ''; }
                    else { gotoEl.removeAttribute('href'); gotoEl.style.display = 'none'; }
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (!modal) modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            });
            content.querySelectorAll('.sd-semana-nav').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    loadSeguimiento(this.dataset.inicio);
                });
            });
        }

        function normalizeHex(color) {
            color = String(color || '').trim();
            if (color.charAt(0) === '#') color = color.substring(1);
            if (/^[0-9a-fA-F]{3}$/.test(color)) {
                color = color.split('').map(function (c) { return c + c; }).join('');
            }
            return /^[0-9a-fA-F]{6}$/.test(color) ? '#' + color.toLowerCase() : null;
        }

        function hexLuminance(hex) {
            var r = parseInt(hex.substring(1, 3), 16) / 255;
            var g = parseInt(hex.substring(3, 5), 16) / 255;
            var b = parseInt(hex.substring(5, 7), 16) / 255;
            var f = function (c) { return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); };
            return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
        }

        function shadeColor(color, percent) {
            var hex = normalizeHex(color) || '#405189';
            var R = parseInt(hex.substring(1, 3), 16);
            var G = parseInt(hex.substring(3, 5), 16);
            var B = parseInt(hex.substring(5, 7), 16);
            R = Math.min(255, Math.max(0, Math.round(R * (100 + percent) / 100)));
            G = Math.min(255, Math.max(0, Math.round(G * (100 + percent) / 100)));
            B = Math.min(255, Math.max(0, Math.round(B * (100 + percent) / 100)));
            var toHex = function (n) { return n.toString(16).padStart(2, '0'); };
            return '#' + toHex(R) + toHex(G) + toHex(B);
        }

        // Sub-tab switching
        document.querySelectorAll('.resp-prof-tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.resp-prof-tab').forEach(function (t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');
                activeSubTab = this.dataset.tab;
                if (activeSubTab === 'prof-seguimiento') loadSeguimiento();
                else loadProgramas();
            });
        });

        // Cargar Programas por defecto
        loadProgramas();
    })();
    @endif

    // Si el backend redirigió con la sección de password (o un flash genérico), abrir su tab
    @if (!session('publicidad_tab') && !session('publicidad_success') && !session('publicidad_error') && ($errors->any() || session('success')))
        const tabBtn = document.querySelector('[data-bs-target="#tab-password"]');
        if (tabBtn) new bootstrap.Tab(tabBtn).show();
    @endif

    // Si el registro de un gasto de publicidad devolvió mensaje (éxito, error de subida o
    // validación), abrir la pestaña de publicidad para que el usuario lo vea.
    @if (session('publicidad_tab') || session('publicidad_success') || session('publicidad_error'))
        const tabPubBtn = document.querySelector('[data-bs-target="#tab-publicidad"]');
        if (tabPubBtn) new bootstrap.Tab(tabPubBtn).show();
    @endif

    // ===== INSCRIBIR PARTICIPANTE (desde tab Ofertas) =====
    (function () {
        const ASESOR_ID = {{ $asesorId ?? 'null' }};
        const ASESOR_NOMBRE = '{{ $asesorNombre }}';

        const $modal = document.getElementById('modalInscribirP');
        const $title = document.getElementById('ip-modal-title');
        const $subtitle = document.getElementById('ip-modal-subtitle');
        const $ci = document.getElementById('ip-ci');
        const $ciBox = $ci.parentElement;
        const $result = document.getElementById('ip-result');
        const $secNuevo = document.getElementById('ip-section-nuevo');
        const $secInsc = document.getElementById('ip-section-inscripcion');
        const $btnReg = document.getElementById('btn-registrar-participante');
        const $btnConfirm = document.getElementById('btn-inscribir-confirm');
        const $descMat = document.getElementById('ip-desc-mat');
        const $descCol = document.getElementById('ip-desc-col');
        const $deudaMat = document.getElementById('ip-deuda-mat');
        const $deudaCol = document.getElementById('ip-deuda-col');
        const $contado = document.getElementById('ip-contado');
        const $traspaso = document.getElementById('ip-traspaso');
        const $montoMat = document.getElementById('ip-monto-mat');
        const $montoCol = document.getElementById('ip-monto-col');
        const $asesorSelected = document.getElementById('ip-asesor-selected');
        const $asesorIdInp = document.getElementById('ip-asesor-id');
        const $cuotaEnt = document.getElementById('ip-cuota-entera');
        const $cuotaUlt = document.getElementById('ip-cuota-ultima');
        const $cuotasDet = document.getElementById('ip-cuotas-detalle');
        const $toggleCuotas = document.getElementById('ip-toggle-cuotas');
        const $cuotasCount = document.getElementById('ip-cuotas-count');
        const $modulosData = document.getElementById('ip-modulos-data');
        const $comprobante = document.getElementById('ip-comprobante');
        const $deudasContainer = document.getElementById('ip-deudas-container');
        const $deudasCheckboxes = document.getElementById('ip-deudas-checkboxes');
        const $comprobanteMonto = document.getElementById('ip-comprobante-monto');
        const $totalSeleccionado = document.getElementById('ip-total-seleccionado');

        let CURRENT_PROG = null;
        let currentParticipante = null;
        let searchTimer = null;

        // Pre-fill asesor
        function fillAsesor() {
            if (!ASESOR_ID) {
                $asesorSelected.innerHTML = '<span class="text-muted" style="font-size:.8rem;"><i class="ri-information-line"></i> Sin asesor asignado — la inscripción se registrará sin asesor.</span>';
                $asesorIdInp.value = '';
                return;
            }
            const ini = ASESOR_NOMBRE.split(' ').map(s => s.charAt(0)).join('').toUpperCase().substring(0,2);
            $asesorSelected.innerHTML =
                '<span class="asesor-selected-card">' +
                    '<span class="asesor-opt-avatar">' + ini + '</span>' +
                    '<span>' + ASESOR_NOMBRE + ' <small style="color:#92400e;font-size:.7rem;font-weight:400;">(mi cuenta)</small></span>' +
                '</span>';
            $asesorIdInp.value = ASESOR_ID;
        }
        fillAsesor();

        function resetAll() {
            $ci.value = '';
            $result.innerHTML = '';
            $secNuevo.style.display = 'none';
            $secInsc.style.display = 'none';
            $btnConfirm.disabled = true;
            currentParticipante = null;
            ['ip-nombres','ip-apellidos','ip-celular','ip-correo','ip-departamento'].forEach(function (id) {
                document.getElementById(id).value = '';
            });
            document.getElementById('ip-sexo').value = '';
            $descMat.value = 0;
            $descCol.value = 0;
            $contado.checked = false;
            $traspaso.checked = false;
            $cuotasDet.style.display = 'none';
            if ($comprobante) {
                $comprobante.value = '';
                $deudasContainer.style.display = 'none';
                $deudasCheckboxes.innerHTML = '';
                $comprobanteMonto.value = '0';
                $totalSeleccionado.textContent = '0.00';
            }
            recalcDeudas();
        }

        function recalcDeudas() {
            if (!CURRENT_PROG) return;
            const dm = Math.min(CURRENT_PROG.matricula, parseFloat($descMat.value || 0));
            const dc = Math.min(CURRENT_PROG.colegiatura, parseFloat($descCol.value || 0));
            const restMat = Math.max(0, CURRENT_PROG.matricula - dm);
            const restCol = Math.max(0, CURRENT_PROG.colegiatura - dc);
            $deudaMat.textContent = 'Deuda: Bs ' + restMat.toFixed(2);
            $deudaCol.textContent = 'Deuda: Bs ' + restCol.toFixed(2);
            $deudaMat.className = restMat === 0 ? 'monto-deuda ok' : 'monto-deuda';
            $deudaCol.className = restCol === 0 ? 'monto-deuda ok' : 'monto-deuda';
            recalcCuotas(restCol);
        }

        $contado.addEventListener('change', function () {
            if (!CURRENT_PROG) return;
            if (this.checked) {
                $descMat.value = CURRENT_PROG.matricula.toFixed(2);
                $descMat.dataset.contadoAutofill = '1';
            } else if ($descMat.dataset.contadoAutofill === '1') {
                $descMat.value = 0;
                $descMat.dataset.contadoAutofill = '';
            }
            recalcDeudas();
        });

        function recalcCuotas(deudaCol) {
            if (!CURRENT_PROG) return;
            const totalMods = CURRENT_PROG.cantidadModulos;
            if (totalMods === 0) {
                $cuotaEnt.textContent = 'Bs 0';
                $cuotaUlt.textContent = 'Bs 0';
                $cuotasCount.textContent = '0';
                $cuotasDet.innerHTML = '';
                return;
            }
            const esContado = $contado.checked;
            const deudaMat = Math.max(0, CURRENT_PROG.matricula - parseFloat($descMat.value || 0));
            let cuotaInt, ultima, cuotasCol;
            if (esContado && totalMods >= 3) {
                cuotasCol = 2;
                cuotaInt = Math.floor(deudaCol / 2);
                ultima = +(deudaCol - cuotaInt).toFixed(2);
            } else if (esContado && totalMods === 2) {
                cuotasCol = 1;
                cuotaInt = deudaCol;
                ultima = deudaCol;
            } else {
                cuotasCol = Math.max(1, totalMods - 1);
                cuotaInt = Math.floor(deudaCol / cuotasCol);
                ultima = +(deudaCol - cuotaInt * (cuotasCol - 1)).toFixed(2);
            }
            $cuotaEnt.textContent = 'Bs ' + cuotaInt.toFixed(2);
            $cuotaUlt.textContent = 'Bs ' + ultima.toFixed(2);
            $cuotasCount.textContent = cuotasCol;

            // Tabla detalle
            const nombres = JSON.parse($modulosData.value || '[]');
            let html = '<table><thead><tr><th style="width:50px;" class="text-center">N°</th><th>Módulo</th><th class="text-center" style="width:90px;">Tipo</th><th class="text-end">Monto</th></tr></thead><tbody>';
            const primerNombre = nombres[0] || 'Módulo 1';
            html += '<tr style="background:#dbeafe;"><td class="text-center text-muted fw-bold">1</td><td>' + primerNombre + '</td><td class="text-center"><span class="badge bg-soft-primary text-primary" style="font-size:.65rem;">Matrícula</span></td><td class="text-end fw-semibold text-primary">Bs ' + deudaMat.toFixed(2) + '</td></tr>';
            if (totalMods > 1) {
                for (let i = 1; i < totalMods; i++) {
                    let monto = 0;
                    let extraClass = '';
                    if (esContado && totalMods >= 3) {
                        if (i === 1) { monto = cuotaInt; }
                        else if (i === 2) { monto = ultima; extraClass = 'ultima'; }
                    } else if (esContado && totalMods === 2) {
                        monto = deudaCol;
                    } else {
                        const esUlt = (i === totalMods - 1);
                        monto = esUlt ? ultima : cuotaInt;
                        if (esUlt) extraClass = 'ultima';
                    }
                    const nomMod = nombres[i] || ('Módulo ' + (i + 1));
                    html += '<tr class="' + extraClass + '" style="' + (monto === 0 && esContado ? 'opacity:.55;' : '') + '">' +
                        '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                        '<td>' + nomMod + (extraClass === 'ultima' ? ' <span style="font-size:.65rem;font-weight:700;color:#d97706;">· saldo</span>' : '') + '</td>' +
                        '<td class="text-center"><span class="badge bg-soft-info text-info" style="font-size:.65rem;">Colegiatura</span></td>' +
                        '<td class="text-end fw-semibold' + (monto === 0 ? ' text-muted' : '') + '">Bs ' + monto.toFixed(2) + '</td></tr>';
                }
            }
            html += '</tbody></table>';
            $cuotasDet.innerHTML = html;

            // --- Generar checkboxes de deudas para comprobante ---
            let checksHtml = '';
            // Matrícula (index 0)
            checksHtml += '<label style="display:flex;align-items:center;gap:.55rem;padding:.4rem .65rem;border:1px solid #eef0f4;border-radius:8px;margin-bottom:.35rem;background:#fff;cursor:pointer;">' +
                '<input type="checkbox" class="ip-deuda-check" data-index="0" data-monto="' + deudaMat.toFixed(2) + '" style="width:16px;height:16px;accent-color:#0ab39c;">' +
                '<span style="flex:1;font-size:.82rem;font-weight:600;color:#212529;">Matrícula</span>' +
                '<span style="font-weight:700;color:#405189;">Bs ' + deudaMat.toFixed(2) + '</span>' +
            '</label>';
            // Colegiatura cuotas (index 1+)
            if (totalMods > 1) {
                for (let i = 1; i < totalMods; i++) {
                    let monto = 0;
                    if (esContado && totalMods >= 3) {
                        if (i === 1) { monto = cuotaInt; }
                        else if (i === 2) { monto = ultima; }
                    } else if (esContado && totalMods === 2) {
                        monto = deudaCol;
                    } else {
                        monto = (i === totalMods - 1) ? ultima : cuotaInt;
                    }
                    const nomMod = nombres[i] || ('Módulo ' + (i + 1));
                    checksHtml += '<label style="display:flex;align-items:center;gap:.55rem;padding:.4rem .65rem;border:1px solid #eef0f4;border-radius:8px;margin-bottom:.35rem;background:#fff;cursor:pointer;">' +
                        '<input type="checkbox" class="ip-deuda-check" data-index="' + i + '" data-monto="' + monto.toFixed(2) + '" style="width:16px;height:16px;accent-color:#0ab39c;">' +
                        '<span style="flex:1;font-size:.82rem;font-weight:600;color:#212529;">Colegiatura — ' + nomMod + (i === totalMods - 1 && !esContado ? ' (saldo)' : '') + '</span>' +
                        '<span style="font-weight:700;color:#405189;">Bs ' + monto.toFixed(2) + '</span>' +
                    '</label>';
                }
            }
            $deudasCheckboxes.innerHTML = checksHtml;
            // Recalcular total si hay checkboxes visibles
            actualizarTotalComprobante();
        }

        function actualizarTotalComprobante() {
            let total = 0;
            document.querySelectorAll('.ip-deuda-check:checked').forEach(function (cb) {
                total += parseFloat(cb.dataset.monto || 0);
            });
            $comprobanteMonto.value = total.toFixed(2);
            $totalSeleccionado.textContent = total.toFixed(2);
        }

        // Event delegation for checkbox changes
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('ip-deuda-check')) {
                actualizarTotalComprobante();
            }
        });

        // File input toggle deudas container
        if ($comprobante) {
            $comprobante.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    $deudasContainer.style.display = '';
                } else {
                    $deudasContainer.style.display = 'none';
                    $deudasCheckboxes.innerHTML = '';
                    $comprobanteMonto.value = '0';
                    $totalSeleccionado.textContent = '0.00';
                }
            });
        }

        $descMat.addEventListener('input', recalcDeudas);
        $descCol.addEventListener('input', recalcDeudas);
        $toggleCuotas.addEventListener('click', function () {
            const visible = $cuotasDet.style.display !== 'none';
            $cuotasDet.style.display = visible ? 'none' : 'block';
            this.innerHTML = visible ? '<i class="ri-eye-line align-bottom"></i> Ver desglose por módulo' : '<i class="ri-eye-off-line align-bottom"></i> Ocultar desglose';
        });

        function showFound(p, yaInscrito) {
            const ini = (p.nombres.charAt(0) + p.apellidos.charAt(0)).toUpperCase();
            if (yaInscrito) {
                $result.innerHTML =
                    '<div class="ci-result-card warning">' +
                        '<div class="ci-avatar" style="background:#f06548;">' + ini + '</div>' +
                        '<div class="ci-info"><div class="ci-nombre">' + p.apellidos + ', ' + p.nombres + '</div>' +
                        '<div class="ci-meta"><i class="ri-error-warning-line text-danger"></i> Ya está inscrito en este programa</div></div></div>';
                $secNuevo.style.display = 'none';
                $secInsc.style.display = 'none';
                $btnConfirm.disabled = true;
                currentParticipante = null;
                return;
            }
            $result.innerHTML =
                '<div class="ci-result-card">' +
                    '<div class="ci-avatar">' + ini + '</div>' +
                    '<div class="ci-info"><div class="ci-nombre">' + p.apellidos + ', ' + p.nombres + '</div>' +
                    '<div class="ci-meta"><i class="ri-passport-line"></i> CI: ' + p.ci +
                    (p.celular ? ' · <i class="ri-phone-line"></i> ' + p.celular : '') +
                    (p.correo ? ' · <i class="ri-mail-line"></i> ' + p.correo : '') +
                    '</div></div><i class="ri-checkbox-circle-line text-success fs-3"></i></div>';
            $secNuevo.style.display = 'none';
            $secInsc.style.display = '';
            currentParticipante = p;
            $btnConfirm.disabled = false;
        }

        function showNotFound(ci) {
            $result.innerHTML =
                '<div class="ci-result-card notfound">' +
                    '<i class="ri-user-search-line fs-3 text-warning"></i>' +
                    '<div class="ci-info"><div class="ci-nombre">Participante no encontrado</div>' +
                    '<div class="ci-meta">Completa el formulario debajo para registrarlo como nuevo.</div></div></div>';
            $secNuevo.style.display = '';
            $secInsc.style.display = 'none';
            currentParticipante = null;
            $btnConfirm.disabled = true;
        }

        $ci.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const ci = this.value.trim();
            $result.innerHTML = '';
            $secNuevo.style.display = 'none';
            $secInsc.style.display = 'none';
            currentParticipante = null;
            $btnConfirm.disabled = true;
            if (ci.length < 7) {
                if (ci.length > 0) {
                    $result.innerHTML = '<div style="padding:.5rem .75rem;"><small class="text-muted"><i class="ri-information-line align-bottom me-1"></i>Mínimo 7 caracteres</small></div>';
                }
                return;
            }
            $ciBox.classList.add('loading');
            searchTimer = setTimeout(function () {
                fetch('{{ url("admin/programas") }}/' + CURRENT_PROG.id + '/buscar-participante?ci=' + encodeURIComponent(ci), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    $ciBox.classList.remove('loading');
                    if (data.found) showFound(data.participante, data.ya_inscrito);
                    else showNotFound(ci);
                })
                .catch(function () { $ciBox.classList.remove('loading'); });
            }, 400);
        });

        $btnReg.addEventListener('click', function () {
            const nombres = document.getElementById('ip-nombres').value.trim();
            const apellidos = document.getElementById('ip-apellidos').value.trim();
            if (!nombres || !apellidos) { alert('Nombres y Apellidos son obligatorios'); return; }
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Registrando...';
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('ci', $ci.value.trim());
            fd.append('nombres', nombres);
            fd.append('apellidos', apellidos);
            fd.append('sexo', document.getElementById('ip-sexo').value);
            fd.append('celular', document.getElementById('ip-celular').value.trim());
            fd.append('correo', document.getElementById('ip-correo').value.trim());
            fd.append('departamento', document.getElementById('ip-departamento').value.trim());
            fetch('{{ url("admin/programas") }}/' + CURRENT_PROG.id + '/registrar-participante', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) showFound(data.participante, false);
                else alert(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Error al registrar'));
            })
            .catch(function () { alert('Error de conexión'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Registrar y continuar';
            });
        });

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
            const colors = { success: '#0d8574', danger: '#b3243a', warning: '#b7791f', info: '#1f6fbf' };
            const icons = { success: 'ri-checkbox-circle-line', danger: 'ri-error-warning-line', warning: 'ri-alert-line', info: 'ri-information-line' };
            const el = document.createElement('div');
            el.className = 'toast align-items-center text-white border-0 show';
            el.style.background = colors[type] || colors.success;
            el.style.minWidth = '280px';
            el.style.boxShadow = '0 10px 24px -10px rgba(0,0,0,.3)';
            el.innerHTML =
                '<div class="d-flex align-items-center">' +
                    '<div class="toast-body d-flex align-items-center gap-2 fs-13 fw-semibold">' +
                        '<i class="' + (icons[type] || icons.success) + ' fs-5"></i>' +
                        '<span>' + msg + '</span>' +
                    '</div>' +
                    '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                '</div>';
            container.appendChild(el);
            try {
                const inst = new bootstrap.Toast(el, { delay: 3500 });
                inst.show();
                el.addEventListener('hidden.bs.toast', () => el.remove());
            } catch (e) {
                setTimeout(() => el.remove(), 3500);
            }
        }

        $btnConfirm.addEventListener('click', function () {
            if (!currentParticipante || !CURRENT_PROG) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Inscribiendo...';
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('participante_id', currentParticipante.id);
            fd.append('descuento_matricula', $descMat.value || 0);
            fd.append('descuento_colegiatura', $descCol.value || 0);
            fd.append('contado', $contado.checked ? 1 : 0);
            fd.append('es_traspaso', $traspaso.checked ? 1 : 0);
            fd.append('asesor_id', $asesorIdInp.value);
            // Comprobante de pago
            if ($comprobante.files && $comprobante.files.length > 0) {
                fd.append('comprobante', $comprobante.files[0]);
                fd.append('comprobante_monto', $comprobanteMonto.value);
                const selected = [];
                document.querySelectorAll('.ip-deuda-check:checked').forEach(function (cb) {
                    selected.push({ index: parseInt(cb.dataset.index) });
                });
                fd.append('deudas_seleccionadas', JSON.stringify(selected));
            }
            fetch('{{ url("admin/programas") }}/' + CURRENT_PROG.id + '/inscribir-participante', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    bootstrap.Modal.getInstance($modal).hide();
                    var msg = data.message || 'Inscripción registrada';
                    if (data.comprobante_creado) msg += ' El comprobante será verificado por contabilidad.';
                    showToast(msg, 'success');
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    alert(data.message || 'Error al inscribir');
                }
            })
            .catch(function () { alert('Error de conexión'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-line align-bottom"></i> Inscribir Participante';
            });
        });

        // Botones inscribir en tabla
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-inscribir-oferta');
            if (!btn) return;
            CURRENT_PROG = {
                id: parseInt(btn.dataset.programaId),
                nombre: btn.dataset.programaNombre,
                codigo: btn.dataset.codigo,
                matricula: parseFloat(btn.dataset.matricula),
                colegiatura: parseFloat(btn.dataset.colegiatura),
                cantidadModulos: Math.max(1, parseInt(btn.dataset.cantidadModulos)),
                habilitadas: parseInt(btn.dataset.inscripcionesHabilitadas)
            };
            if (!CURRENT_PROG.habilitadas) {
                alert('Las inscripciones para este programa están deshabilitadas.');
                return;
            }
            $title.textContent = 'Inscribir Participante';
            $subtitle.textContent = CURRENT_PROG.nombre + ' · ' + CURRENT_PROG.codigo;
            $montoMat.textContent = 'Bs ' + CURRENT_PROG.matricula.toFixed(2);
            $montoCol.textContent = 'Bs ' + CURRENT_PROG.colegiatura.toFixed(2);
            $descMat.max = CURRENT_PROG.matricula;
            $descCol.max = CURRENT_PROG.colegiatura;
            // Set módulos data from data attribute (real names) or generate generic ones
            let modNombres = [];
            if (btn.dataset.modulosNombres) {
                modNombres = btn.dataset.modulosNombres.split('||').filter(Boolean);
            }
            if (modNombres.length === 0) {
                for (let i = 0; i < CURRENT_PROG.cantidadModulos; i++) {
                    modNombres.push('Módulo ' + (i + 1));
                }
            }
            $modulosData.value = JSON.stringify(modNombres);
            resetAll();
            new bootstrap.Modal($modal).show();
            setTimeout(function () { $ci.focus(); }, 300);
        });
    })();
});
</script>
@endpush
