@extends('admin.layouts.master')

@section('title', $participante->apellidos . ', ' . $participante->nombres . ' - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.show-hero {
    position: relative;
    background: linear-gradient(120deg, #0e2f52 0%, #1c486c 50%, #0a6a90 100%);
    border-radius: 20px;
    padding: 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 22px 45px -26px rgba(14,47,82,.85);
    margin-bottom: 1.25rem;
}
.show-hero::before {
    content: ""; position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.show-hero::after {
    content: ""; position: absolute;
    bottom: -80px; left: 20%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
    border-radius: 50%;
}
.show-hero > * { position: relative; z-index: 1; }
.profile-avatar-lg {
    width: 78px; height: 78px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: 1.75rem;
    background: rgba(255,255,255,.2);
    color: #fff;
    border: 2px solid rgba(255,255,255,.35);
    backdrop-filter: blur(4px);
    box-shadow: 0 10px 24px -10px rgba(0,0,0,.3);
}
.profile-avatar-lg.female { background: linear-gradient(135deg, rgba(236,72,153,.3), rgba(244,114,182,.3)); border-color: rgba(255,255,255,.4); }
.show-hero h3 { color: #fff; font-weight: 700; letter-spacing: .2px; line-height: 1.3; }
.hero-info-pills { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .65rem; }
.hero-info-pill {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    padding: .35em .75em;
    border-radius: 999px;
    backdrop-filter: blur(4px);
    display: inline-flex; align-items: center; gap: .35rem;
}
.btn-volver {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    font-weight: 600;
    backdrop-filter: blur(4px);
    transition: all .2s ease;
}
.btn-volver:hover { background: rgba(255,255,255,.28); color: #fff; transform: translateY(-1px); }
.hero-top { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.hero-identity { display:flex; align-items:center; gap:1rem; min-width:0; }
.hero-stats { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.75rem; margin-top:1.35rem; }
.hero-stat {
    display:flex; align-items:center; gap:.65rem;
    background: #ffffff;
    border: 1px solid rgba(255,255,255,.7);
    border-radius: 14px;
    padding: .75rem .9rem;
    box-shadow: 0 12px 26px -20px rgba(3,18,38,.8);
    transition: all .2s ease;
    text-align: left;
}
.hero-stat:hover { transform: translateY(-2px); box-shadow: 0 16px 30px -18px rgba(3,18,38,.85); }
.hero-stat .hs-ic { width:38px; height:38px; border-radius:11px; display:inline-flex; align-items:center; justify-content:center; font-size:1.1rem; background:#eef1f8; color:#1c486c; flex-shrink:0; }
.hero-stat .stat-val { color: #12345b; font-weight: 800; font-size: 1.3rem; line-height: 1.05; font-variant-numeric: tabular-nums; }
.hero-stat .stat-lbl { color: #667085; font-size: .64rem; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; margin-top: 1px; }
@media (max-width: 767.98px) { .hero-stats { grid-template-columns: repeat(2, minmax(0,1fr)); } }

/* ===== Tabs Pill ===== */
.show-tabs-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    background: #fff;
}
[data-layout-mode="dark"] .show-tabs-card { background: #212529; border-color: #2a2f34; }
.show-tabs-card .card-body { padding: 1.25rem; }

.nav-tabs-pill {
    border: 0;
    gap: .35rem;
    background: #f3f5f9;
    padding: .4rem;
    border-radius: 12px;
    display: inline-flex;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
[data-layout-mode="dark"] .nav-tabs-pill { background: #1a1d20; }
.nav-tabs-pill .nav-link {
    border: 0 !important;
    border-radius: 9px;
    color: #495057;
    font-weight: 600;
    font-size: .82rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: all .2s ease;
    display: inline-flex; align-items: center; gap: .35rem;
    background: transparent;
}
.nav-tabs-pill .nav-link:hover { color: #405189; background: rgba(64,81,137,.06); }
.nav-tabs-pill .nav-link.active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff !important;
    box-shadow: 0 6px 14px -6px rgba(64,81,137,.55);
}
[data-layout-mode="dark"] .nav-tabs-pill .nav-link { color: #ced4da; }

/* ===== Program header card ===== */
.programa-info-card {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .programa-info-card { background: #1a1d20; border-color: #2a2f34; }
.programa-info-left { display: flex; align-items: center; gap: .65rem; min-width: 0; }
.programa-info-icon {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.programa-info-tipo { font-size: .8rem; font-weight: 700; color: #212529; line-height: 1.2; }
.programa-info-date { font-size: .72rem; color: #6c757d; margin-top: 2px; display: inline-flex; align-items: center; gap: .3rem; }
[data-layout-mode="dark"] .programa-info-tipo { color: #fff; }
.programa-info-badges { display: flex; flex-wrap: wrap; gap: .35rem; }

.tag-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 700;
    padding: .3em .75em;
    border-radius: 999px;
    border: 1px solid;
}
.tag-pill::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.tag-contado { background: #dbeafe; color: #299cdb; border-color: #c3ddfa; }
.tag-regular { background: #eef1f8; color: #405189; border-color: #c3cfe2; }
.tag-activo { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.tag-inactivo { background: #f1f3f5; color: #878a99; border-color: #e9ebec; }

/* ===== Stat cards ===== */
.mini-stat {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    background: #fff;
    padding: .85rem 1rem;
    position: relative;
    overflow: hidden;
    transition: all .2s ease;
}
.mini-stat::before {
    content: ""; position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: var(--accent, #405189);
}
.mini-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 18px -10px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .mini-stat { background: #212529; border-color: #2a2f34; }
.mini-stat .stat-label {
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #878a99;
    font-weight: 700;
    display: flex; align-items: center; gap: .35rem;
    margin-bottom: .35rem;
}
.mini-stat .stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: #212529;
    line-height: 1.2;
}
[data-layout-mode="dark"] .mini-stat .stat-value { color: #fff; }
.mini-stat.success .stat-value { color: #0ab39c; }
.mini-stat.danger .stat-value { color: #f06548; }
.mini-stat.muted .stat-value { color: #878a99; }

/* ===== Section cards ===== */
.section-card {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    background: #fff;
    margin-bottom: 1rem;
    overflow: hidden;
}
[data-layout-mode="dark"] .section-card { background: #212529; border-color: #2a2f34; }
.section-card-header {
    padding: .85rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex; align-items: center; gap: .65rem;
}
[data-layout-mode="dark"] .section-card-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.section-card-icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.section-card-title { font-size: .82rem; font-weight: 700; color: #212529; margin: 0; text-transform: uppercase; letter-spacing: .3px; }
[data-layout-mode="dark"] .section-card-title { color: #fff; }

/* ===== Tables ===== */
.modern-table { margin: 0; width: 100%; }
.modern-table thead th {
    background: #fafbfd !important;
    font-weight: 700;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .75rem .75rem;
    border-top: 0;
}
.modern-table tbody td {
    padding: .75rem .75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .85rem;
    border-top: 0;
}
.modern-table tbody tr { transition: background-color .15s ease; }
.modern-table tbody tr:hover { background-color: #f8f9fc; }
.modern-table tbody tr:last-child td { border-bottom: none; }
[data-layout-mode="dark"] .modern-table thead th { background: #1d2125 !important; border-bottom-color: #2a2f34 !important; }
[data-layout-mode="dark"] .modern-table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .modern-table tbody tr:hover { background-color: #1a1d20; }

.estado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 600;
    padding: .25em .65em; border-radius: 999px;
    border: 1px solid currentColor;
}
.estado-pill::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

.nota-cell {
    font-weight: 700;
    border-radius: 8px;
    padding: .3em .75em;
    display: inline-block;
    min-width: 50px;
    text-align: center;
}
.nota-aprobada { background: #d1fae5; color: #0ab39c; }
.nota-reprobada { background: #fee2e2; color: #f06548; }
.nota-neutral { color: #adb5bd; font-weight: 500; }

.docente-cell { display: inline-flex; align-items: center; gap: .5rem; }
.docente-avatar-sm {
    width: 26px; height: 26px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff;
    font-weight: 700;
    font-size: .65rem;
    flex-shrink: 0;
}

/* ===== Modal pagos ===== */
.modal-pagos .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
.modal-pagos .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1rem 1.25rem;
    position: relative;
    overflow: hidden;
}
.modal-pagos .modal-header::after {
    content: ""; position: absolute;
    top: -30px; right: -30px;
    width: 140px; height: 140px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
.modal-pagos .modal-header > * { position: relative; z-index: 1; }
.modal-pagos .modal-title { color: #fff; font-weight: 700; font-size: .95rem; display: flex; align-items: center; gap: .5rem; }
.modal-pagos .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
.modal-pagos .modal-header-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 10px;
    font-size: 1rem;
}
.modal-pagos .modal-body { padding: 1.25rem; background: #fafbfd; }
[data-layout-mode="dark"] .modal-pagos .modal-body { background: #1a1d20; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 12px;
    border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 70px; height: 70px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2rem;
    color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

.payment-summary-box {
    background: linear-gradient(135deg, #fafbfd 0%, #f3f5f9 100%);
    border: 1px solid #eef0f4;
    border-radius: 10px;
    padding: .85rem 1rem;
    flex: 1;
    min-width: 220px;
}
[data-layout-mode="dark"] .payment-summary-box { background: #1a1d20; border-color: #2a2f34; }
.payment-summary-box .ps-label { font-size: .68rem; color: #878a99; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; }
.payment-summary-box .ps-total { font-size: 1.05rem; font-weight: 700; color: #212529; }
[data-layout-mode="dark"] .payment-summary-box .ps-total { color: #fff; }
.payment-summary-box .ps-meta { font-size: .72rem; }

/* ===== Horarios expand ===== */
.btn-expand {
    width: 28px; height: 28px;
    border: 1px solid #e9ebec;
    background: #fff;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #495057;
    font-size: .85rem;
    cursor: pointer;
    transition: all .15s ease;
}
.btn-expand:hover { border-color: #405189; color: #405189; background: #f8f9fc; }
.btn-expand.is-open { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color: #fff; border-color: transparent; transform: rotate(90deg); }
[data-layout-mode="dark"] .btn-expand { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

.horarios-row td {
    background: #fafbfd !important;
    padding: 0 !important;
    border-top: 0 !important;
}
[data-layout-mode="dark"] .horarios-row td { background: #1a1d20 !important; }
.horarios-wrap {
    padding: 1rem 1.25rem;
    border-left: 3px solid #405189;
}
.horarios-summary {
    display: flex; gap: .75rem; flex-wrap: wrap;
    margin-bottom: .85rem;
    padding: .65rem .85rem;
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 10px;
}
[data-layout-mode="dark"] .horarios-summary { background: #1d2125; border-color: #2a2f34; }
.h-stat {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .78rem; font-weight: 700;
    padding: .25em .7em;
    border-radius: 999px;
    border: 1px solid;
}
.h-stat.is-total { background: #eef1f8; color: #405189; border-color: #c3cfe2; }
.h-stat.is-des   { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.h-stat.is-pres  { background: #ecfdf5; color: #0ab39c; border-color: #a7f3d8; }
.h-stat.is-aus   { background: #fef2f2; color: #f06548; border-color: #fecaca; }
.h-stat.is-tar   { background: #fffbeb; color: #f7b84b; border-color: #fde68a; }
.h-stat.is-jus   { background: #f0f9ff; color: #299cdb; border-color: #bae6fd; }
.h-stat.is-pct   { background: linear-gradient(135deg, #0ab39c, #14b8a6); color: #fff; border-color: transparent; }

.session-table { margin: 0; width: 100%; background: #fff; border-radius: 10px; overflow: hidden; border: 1px solid #eef0f4; }
[data-layout-mode="dark"] .session-table { background: #1d2125; border-color: #2a2f34; }
.session-table thead th {
    background: #f8f9fc !important;
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    padding: .55rem .7rem;
    border: 0;
}
.session-table tbody td {
    padding: .55rem .7rem;
    font-size: .8rem;
    border-bottom: 1px solid #f1f3f5;
    border-top: 0;
}
.session-table tbody tr:last-child td { border-bottom: 0; }
[data-layout-mode="dark"] .session-table thead th { background: #1d2125 !important; color: #adb5bd; }
[data-layout-mode="dark"] .session-table tbody td { border-bottom-color: #2a2f34; }

.asist-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: .22em .7em;
    border-radius: 999px;
    border: 1px solid;
}
.asist-tag.is-pres { background: #ecfdf5; color: #0ab39c; border-color: #a7f3d8; }
.asist-tag.is-aus  { background: #fef2f2; color: #f06548; border-color: #fecaca; }
.asist-tag.is-tar  { background: #fffbeb; color: #f7b84b; border-color: #fde68a; }
.asist-tag.is-jus  { background: #f0f9ff; color: #299cdb; border-color: #bae6fd; }
.asist-tag.is-sin  { background: #f3f5f9; color: #878a99; border-color: #e9ebec; }
.session-fecha-main { font-weight: 700; color: #212529; text-transform: capitalize; font-size: .82rem; }
[data-layout-mode="dark"] .session-fecha-main { color: #fff; }
.session-fecha-sub  { font-size: .7rem; color: #878a99; }

.horarios-loader { text-align: center; padding: 1.5rem; color: #878a99; }
.horarios-empty { text-align: center; padding: 1.5rem; color: #878a99; font-style: italic; }

/* ===== Vencimientos ===== */
.due-row {
    background: linear-gradient(90deg, rgba(240,101,72,.08) 0%, rgba(240,101,72,0) 100%) !important;
}
.due-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: .22em .65em;
    border-radius: 999px;
    border: 1px solid;
    white-space: nowrap;
}
.due-pill.is-venc  { background: #fef2f2; color: #f06548; border-color: #fecaca; }
.due-pill.is-hoy   { background: #fffbeb; color: #f7b84b; border-color: #fde68a; }
.due-pill.is-prox  { background: #fffaf2; color: #f7b84b; border-color: #fde68a; }
.due-pill.is-futuro{ background: #ecfdf5; color: #0ab39c; border-color: #a7f3d8; }
.due-pill.is-pag   { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.due-pill.is-sin   { background: #f3f5f9; color: #878a99; border-color: #e9ebec; }

.due-cell { display: flex; flex-direction: column; gap: 2px; line-height: 1.15; }
.due-cell .due-fecha { font-weight: 700; color: #212529; font-size: .82rem; }
[data-layout-mode="dark"] .due-cell .due-fecha { color: #fff; }

/* Resumen de vencimientos */
.vencimientos-box {
    display: flex; flex-wrap: wrap; gap: .5rem;
    margin-bottom: .85rem;
}
.venc-card {
    flex: 1; min-width: 180px;
    border: 1px solid var(--vc-border, #eef0f4);
    background: var(--vc-bg, #fff);
    border-radius: 12px;
    padding: .65rem .85rem;
    display: flex; align-items: center; gap: .65rem;
}
[data-layout-mode="dark"] .venc-card { background: #1d2125; }
.venc-card .vc-icon {
    width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.1rem;
    color: var(--vc-color, #495057);
    background: var(--vc-icon-bg, #f8f9fc);
    flex-shrink: 0;
}
.venc-card .vc-lbl { font-size: .65rem; text-transform: uppercase; letter-spacing: .4px; color: #878a99; font-weight: 700; }
.venc-card .vc-val { font-size: 1.1rem; font-weight: 800; color: var(--vc-color, #212529); line-height: 1.1; }
.venc-card .vc-sub { font-size: .68rem; color: #878a99; }

/* ===== Botones pago ===== */
.btn-pay-row {
    width: 30px; height: 30px;
    border: 0;
    border-radius: 8px;
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem;
    transition: all .15s ease;
    box-shadow: 0 4px 10px -4px rgba(10,179,156,.55);
}
.btn-pay-row:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 14px -6px rgba(10,179,156,.65); color: #fff; }
.btn-pay-row:disabled { background: #e9ebec; color: #adb5bd; box-shadow: none; cursor: not-allowed; }
.btn-pay-general {
    background: linear-gradient(135deg, #0ab39c 0%, #128c7e 100%);
    border: 0; color: #fff;
    border-radius: 12px;
    padding: .55rem 1.1rem;
    font-weight: 700;
    font-size: .82rem;
    letter-spacing: .1px;
    box-shadow: 0 10px 22px -12px rgba(10,179,156,.8);
    display: inline-flex; align-items: center; gap: .4rem;
    transition: all .18s ease;
}
.btn-pay-general:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 14px 26px -12px rgba(10,179,156,.95); }

/* Modals */
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

.pago-info-box {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: .85rem 1rem;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .pago-info-box { background: #1a1d20; border-color: #2a2f34; }
.pago-info-box .pib-row { display: flex; justify-content: space-between; font-size: .85rem; padding: .15rem 0; }
.pago-info-box .pib-row strong { font-weight: 800; }

.input-monto {
    border: 2px solid #e9ebec;
    border-radius: 10px;
    padding: .65rem .75rem;
    font-size: 1.1rem;
    font-weight: 700;
    width: 100%;
    text-align: right;
    transition: all .15s ease;
}
.input-monto:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .2rem rgba(10,179,156,.15); }
[data-layout-mode="dark"] .input-monto { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

/* Pago general */
#modalPagoGeneral .modal-dialog { max-width: 980px; }
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
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: .45rem .65rem;
    font-size: .9rem;
    font-weight: 600;
    background: #fff;
}
.pg-grp input:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .15rem rgba(10,179,156,.15); }
.pg-grp.monto input { font-size: 1.1rem; font-weight: 700; text-align: right; color: #0ab39c; }
[data-layout-mode="dark"] .pg-grp input, [data-layout-mode="dark"] .pg-grp select { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

.pg-actions { display: flex; gap: .5rem; }
.btn-pg-action {
    border: 1px solid #e9ebec;
    background: #fff;
    color: #495057;
    border-radius: 8px;
    padding: .45rem .85rem;
    font-size: .78rem;
    font-weight: 700;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .25rem;
}
.btn-pg-action:hover { border-color: #405189; color: #405189; background: #f8f9fc; }
[data-layout-mode="dark"] .btn-pg-action { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

.pg-summary {
    display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1rem;
}
.pg-stat {
    flex: 1; min-width: 160px;
    border: 1px solid var(--pg-border, #eef0f4);
    background: #fff;
    border-radius: 10px;
    padding: .65rem .85rem;
    text-align: center;
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
    animation: pgPulse .5s ease;
}
.pg-table tbody tr.is-affected td { color: #064e3b; font-weight: 600; }
.pg-table tbody tr.is-affected .pg-input-monto { background: #ecfdf5; border-color: #0ab39c; color: #0ab39c; }
[data-layout-mode="dark"] .pg-table tbody tr.is-affected td { color: #a7f3d8; }
@keyframes pgPulse {
    0% { transform: translateX(-6px); opacity: .4; }
    100% { transform: translateX(0); opacity: 1; }
}
.pg-table tbody tr.is-venc { background: linear-gradient(90deg, rgba(240,101,72,.07), rgba(240,101,72,0)); }
.pg-table tbody tr.is-pagada td { opacity: .55; }

.pg-input-monto {
    border: 1px solid #e9ebec;
    border-radius: 6px;
    padding: .25rem .5rem;
    font-size: .82rem;
    font-weight: 700;
    width: 110px;
    text-align: right;
    color: #0ab39c;
}
.pg-input-monto:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .12rem rgba(10,179,156,.15); }
.pg-input-monto.is-invalid { border-color: #f06548; background: #fff5f5; box-shadow: 0 0 0 .12rem rgba(240,101,72,.12); }
#pgMontoTotal.is-invalid { border-color: #f06548; box-shadow: 0 0 0 .15rem rgba(240,101,72,.15); }
[data-layout-mode="dark"] .pg-input-monto { background: #1d2125; border-color: #2a2f34; color: #14b8a6; }
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $fechaEs = function ($val) use ($mesesEs) {
        if (!$val) return '—';
        $t = is_numeric($val) ? (int)$val : strtotime((string)$val);
        if (!$t) return $val;
        return (int)date('d', $t) . ' de ' . ($mesesEs[(int)date('n', $t) - 1] ?? '') . ' del ' . date('Y', $t);
    };
    $ini = strtoupper(substr($participante->nombres ?? '', 0, 1) . substr($participante->apellidos ?? '', 0, 1));
    $sexoChar = strtoupper(substr($participante->sexo ?? '', 0, 1));
    $isFemale = $sexoChar === 'F';

    // Registrar pagos: solo Super Admin y Contabilidad.
    $puedePagar = auth()->user()->hasAnyRole(['Super Admin', 'Contabilidad']);

    $totalInscripciones = count($programasData);
    $totalMatriculaPagado = 0;
    $totalColegiaturaPagado = 0;
    $totalDeuda = 0;
    foreach ($programasData as $pd) {
        $totalMatriculaPagado += $pd->totalMatriculaPagado ?? 0;
        $totalColegiaturaPagado += $pd->totalColegiaturaPagado ?? 0;
        $totalDeuda += ($pd->totalMatriculaDeuda ?? 0) + ($pd->totalColegiaturaDeuda ?? 0);
    }
@endphp

<div class="show-hero">
    <div class="hero-top">
        <div class="hero-identity">
            <div class="profile-avatar-lg {{ $isFemale ? 'female' : '' }}">{{ $ini }}</div>
            <div style="min-width:0;">
                <h3 class="fs-22 mb-1">{{ $participante->apellidos }}, {{ $participante->nombres }}</h3>
                <div class="hero-info-pills">
                    <span class="hero-info-pill"><i class="ri-passport-line"></i> CI: {{ $participante->ci }}</span>
                    @if($participante->celular)
                        <span class="hero-info-pill"><i class="ri-phone-line"></i> {{ $participante->celular }}</span>
                    @endif
                    @if($participante->correo)
                        <span class="hero-info-pill" title="{{ $participante->correo }}"><i class="ri-mail-line"></i> {{ $participante->correo }}</span>
                    @endif
                    @if($participante->departamento)
                        <span class="hero-info-pill"><i class="ri-map-pin-line"></i> {{ $participante->departamento }}</span>
                    @endif
                    @if($participante->sexo)
                        <span class="hero-info-pill"><i class="ri-{{ $isFemale ? 'women' : 'men' }}-line"></i> {{ $participante->sexo }}</span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('admin.programas.index') }}" class="btn btn-volver d-inline-flex align-items-center gap-1">
            <i class="ri-arrow-left-line"></i> Volver a Programas
        </a>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <span class="hs-ic"><i class="ri-booklet-line"></i></span>
            <div><div class="stat-val">{{ $totalInscripciones }}</div><div class="stat-lbl">Programas</div></div>
        </div>
        <div class="hero-stat">
            <span class="hs-ic"><i class="ri-bank-card-line"></i></span>
            <div><div class="stat-val">{{ number_format($totalMatriculaPagado, 0) }}</div><div class="stat-lbl">Matrícula pagada</div></div>
        </div>
        <div class="hero-stat">
            <span class="hs-ic"><i class="ri-funds-line"></i></span>
            <div><div class="stat-val">{{ number_format($totalColegiaturaPagado, 0) }}</div><div class="stat-lbl">Colegiatura pagada</div></div>
        </div>
        <div class="hero-stat">
            <span class="hs-ic"><i class="ri-error-warning-line"></i></span>
            <div><div class="stat-val">{{ number_format($totalDeuda, 0) }}</div><div class="stat-lbl">Deuda</div></div>
        </div>
    </div>
</div>

@if(count($programasData) > 0)
<div class="card show-tabs-card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-pill" role="tablist">
            @foreach($programasData as $idx => $pd)
            @php $ins = $pd->inscripcion; @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" id="prog-tab-{{ $idx }}" data-bs-toggle="tab" data-bs-target="#prog-{{ $idx }}" type="button" role="tab">
                    <i class="ri-booklet-line"></i> {{ $ins->programa_nombre }}
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($programasData as $idx => $pd)
            @php $ins = $pd->inscripcion; @endphp
            <div class="tab-pane {{ $idx === 0 ? 'show active' : '' }}" id="prog-{{ $idx }}" role="tabpanel">

                {{-- Programa info header --}}
                <div class="programa-info-card">
                    <div class="programa-info-left">
                        <span class="programa-info-icon"><i class="ri-graduation-cap-line"></i></span>
                        <div>
                            <div class="programa-info-tipo">{{ $ins->tipo_nombre }}</div>
                            <div class="programa-info-date"><i class="ri-calendar-line"></i> Inscrito el {{ $fechaEs($ins->fecha_inscripcion) }}</div>
                        </div>
                    </div>
                    <div class="programa-info-badges">
                        @if($ins->contado)
                            <span class="tag-pill tag-contado">Contado</span>
                        @else
                            <span class="tag-pill tag-regular">Regular</span>
                        @endif
                        @if($ins->estado)
                            <span class="tag-pill tag-activo">Activo</span>
                        @else
                            <span class="tag-pill tag-inactivo">Inactivo</span>
                        @endif
                    </div>
                </div>

                {{-- Resumen contable --}}
                @php
                    $brutoMat = (float) $ins->matricula;
                    $descMat = (float) ($ins->descuento_matricula ?? 0);
                    $netoMat = max(0, $brutoMat - $descMat);
                    $saldoMat = max(0, $netoMat - ($pd->totalMatriculaPagado ?? 0));

                    $brutoCol = (float) $ins->monto_programa;
                    $descCol = (float) ($ins->descuento_colegiatura ?? 0);
                    $netoCol = max(0, $brutoCol - $descCol);
                    $saldoCol = max(0, $netoCol - ($pd->totalColegiaturaPagado ?? 0));
                @endphp
                <div class="row g-2 mb-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="mini-stat" style="--accent: #299cdb;">
                            <div class="stat-label"><i class="ri-bank-card-line"></i>Matrícula</div>
                            <div class="stat-value" style="font-size:.82rem; line-height:1.4;">
                                @if($descMat > 0)
                                    <div class="text-muted" style="font-size:.7rem; text-decoration:line-through;">Bs {{ number_format($brutoMat, 2) }}</div>
                                    <div class="text-muted" style="font-size:.7rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descMat, 2) }}</div>
                                @endif
                                <div style="font-weight:800;">Bs {{ number_format($netoMat, 2) }}</div>
                                @if(($pd->totalMatriculaPagado ?? 0) > 0)
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-check-line"></i> Pag Bs {{ number_format($pd->totalMatriculaPagado, 2) }}</span>
                                @endif
                                @if($saldoMat > 0)
                                    <span style="font-size:.7rem; color:#f06548;"><i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoMat, 2) }}</span>
                                @elseif($netoMat > 0)
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                @else
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="mini-stat" style="--accent: #0ab39c;">
                            <div class="stat-label"><i class="ri-funds-line"></i>Colegiatura</div>
                            <div class="stat-value" style="font-size:.82rem; line-height:1.4;">
                                @if($descCol > 0)
                                    <div class="text-muted" style="font-size:.7rem; text-decoration:line-through;">Bs {{ number_format($brutoCol, 2) }}</div>
                                    <div class="text-muted" style="font-size:.7rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descCol, 2) }}</div>
                                @endif
                                <div style="font-weight:800;">Bs {{ number_format($netoCol, 2) }}</div>
                                @if(($pd->totalColegiaturaPagado ?? 0) > 0)
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-check-line"></i> Pag Bs {{ number_format($pd->totalColegiaturaPagado, 2) }}</span>
                                @endif
                                @if($saldoCol > 0)
                                    <span style="font-size:.7rem; color:#f06548;"><i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoCol, 2) }}</span>
                                @elseif($netoCol > 0)
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                @else
                                    <span style="font-size:.7rem; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detalle Académico --}}
                @if(count($pd->modulos) > 0)
                @php
                    $showNivelacion = false;
                    foreach ($pd->modulos as $m) {
                        if (($m->desarrollo ?? '') === 'Concluido') {
                            $nota = $pd->notas->get($m->id);
                            $nm = $nota ? (float) $nota->nota_modulo : null;
                            if ($nm !== null && $nm < 71) {
                                $showNivelacion = true;
                                break;
                            }
                        }
                    }
                @endphp
                <div class="section-card">
                    <div class="section-card-header">
                        <span class="section-card-icon bg-soft-primary text-primary"><i class="ri-graduation-cap-line"></i></span>
                        <h6 class="section-card-title">Detalle Académico</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Módulo</th>
                                    <th style="width: 140px;">Estado</th>
                                    <th style="width: 220px;">Docente</th>
                                    @if($showNivelacion)
                                    <th style="width: 110px;" class="text-center">Nota Módulo</th>
                                    <th style="width: 110px;" class="text-center">Nota Nivelación</th>
                                    @else
                                    <th style="width: 110px;" class="text-center">Nota Módulo</th>
                                    @endif
                                    <th style="width: 60px;" class="text-center">Sesiones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pd->modulos as $m)
                                @php $esModCero = ($pd->modulo_cero ?? 0) == 1 && $loop->first; @endphp
                                @if(!$esModCero)
                                @php
                                    $nota = $pd->notas->get($m->id);
                                    $nm = $nota ? (float) $nota->nota_modulo : null;
                                    $nn = $nota ? (float) $nota->nota_nivelacion : null;
                                    $esConcluido = ($m->desarrollo ?? '') === 'Concluido';
                                    $colorEstado = [
                                        'Concluido' => '#0ab39c',
                                        'En Desarrollo' => '#299cdb',
                                        'No Inicio' => '#878a99',
                                        'Pendiente' => '#f7b84b',
                                        'Suspendido' => '#f06548',
                                    ][$m->desarrollo] ?? '#878a99';
                                    $iniDoc = $m->docente_nombres ? strtoupper(substr($m->docente_nombres, 0, 1) . substr($m->docente_apellidos, 0, 1)) : '?';
                                    $puedeExpandir = $m->desarrollo === 'En Desarrollo' || $m->desarrollo === 'Concluido';
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-book-2-line text-primary"></i>
                                            <span class="fw-semibold text-dark">{{ $m->nombre }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="estado-pill" style="color: {{ $colorEstado }};">{{ $m->desarrollo }}</span>
                                    </td>
                                    <td>
                                        @if($m->docente_apellidos)
                                            <div class="docente-cell">
                                                <span class="docente-avatar-sm">{{ $iniDoc }}</span>
                                                <span class="fw-semibold text-dark" style="font-size:.82rem;">{{ $m->docente_apellidos }}, {{ $m->docente_nombres }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic"><i class="ri-user-unfollow-line"></i> Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($esConcluido && $nm !== null)
                                            <span class="nota-cell {{ $nm >= 71 ? 'nota-aprobada' : 'nota-reprobada' }}">{{ number_format($nm, 2) }}</span>
                                        @else
                                            <span class="nota-neutral">—</span>
                                        @endif
                                    </td>
                                    @if($showNivelacion)
                                    <td class="text-center">
                                        @if($esConcluido && $nn !== null && $nm !== null && $nm < 71)
                                            <span class="nota-cell {{ $nn >= 71 ? 'nota-aprobada' : 'nota-reprobada' }}">{{ number_format($nn, 2) }}</span>
                                        @else
                                            <span class="nota-neutral">—</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="text-center">
                                        @if($puedeExpandir)
                                            <button type="button" class="btn-expand btn-expand-horarios"
                                                    data-participante="{{ $participante->id }}"
                                                    data-modulo="{{ $m->id }}"
                                                    data-target="horarios-{{ $idx }}-{{ $m->id }}"
                                                    title="Ver sesiones y asistencia">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($puedeExpandir)
                                <tr class="horarios-row" id="horarios-{{ $idx }}-{{ $m->id }}" style="display:none;">
                                    <td colspan="{{ $showNivelacion ? 7 : 6 }}">
                                        <div class="horarios-wrap">
                                            <div class="horarios-loader"><span class="spinner-border spinner-border-sm me-1"></span> Cargando sesiones...</div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="empty-state mb-3">
                    <div class="empty-icon"><i class="ri-book-open-line"></i></div>
                    <p class="text-muted mb-0">No hay módulos registrados en este programa</p>
                </div>
                @endif

                {{-- Cuotas y Pagos --}}
                @php
                    $cuotasJson = $pd->cuotas->filter(fn($c) => (float)$c->total_cuota > 0)->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'tipo' => $c->tipo,
                            'total_cuota' => (float) $c->total_cuota,
                            'pagado' => (float) $c->pagado,
                            'saldo' => max(0, (float) $c->saldo),
                            'fecha_pago' => $c->fecha_pago,
                        ];
                    })->values();
                @endphp
                <div class="section-card" data-prog-idx="{{ $idx }}" data-cuotas='@json($cuotasJson)'>
                    <div class="section-card-header">
                        <span class="section-card-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></span>
                        <h6 class="section-card-title">Cuotas y Pagos</h6>
                        @php
                            $planTipo = ((int)($pd->inscripcion->contado ?? 0) === 1) ? 'Contado' : 'Regular';
                            $tieneSaldo = $pd->cuotas->where('saldo', '>', 0)->count() > 0;
                        @endphp
                        <span class="tag-pill tag-{{ strtolower($planTipo) }}" style="margin-left:0.5rem;">{{ $planTipo }}</span>
                        @if($tieneSaldo && $puedePagar)
                        <button type="button" class="btn-pay-general ms-auto btn-pago-general" data-prog-idx="{{ $idx }}">
                            <i class="ri-money-dollar-circle-line"></i> Pago General
                        </button>
                        @endif
                    </div>
                    <div class="p-3">
                        {{-- Summary --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <div class="payment-summary-box">
                                <div class="ps-label"><i class="ri-bank-card-line"></i> Matrícula</div>
                                <div class="ps-total" style="font-size:.9rem;">
                                    @if(($ins->descuento_matricula ?? 0) > 0)
                                        <span class="text-muted" style="font-size:.7rem; text-decoration:line-through;">Bs {{ number_format($ins->matricula, 2) }}</span>
                                        <span class="text-muted" style="font-size:.7rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($ins->descuento_matricula, 2) }}</span>
                                    @endif
                                    <div class="fw-bold">Bs {{ number_format(max(0, (float)$ins->matricula - (float)($ins->descuento_matricula ?? 0)), 2) }}</div>
                                </div>
                                <div class="ps-meta">
                                    <span class="text-success fw-semibold"><i class="ri-check-line"></i> Pagado Bs {{ number_format($pd->totalMatriculaPagado, 2) }}</span>
                                    @php $saldoMatBox = max(0, max(0, (float)$ins->matricula - (float)($ins->descuento_matricula ?? 0)) - (float)$pd->totalMatriculaPagado); @endphp
                                    @if($saldoMatBox > 0)
                                        <span class="ms-2 text-danger fw-semibold"><i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoMatBox, 2) }}</span>
                                    @elseif(max(0, (float)$ins->matricula - (float)($ins->descuento_matricula ?? 0)) > 0)
                                        <span class="ms-2 text-success fw-semibold"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                    @else
                                        <span class="ms-2 text-muted"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                    @endif
                                </div>
                            </div>
                            <div class="payment-summary-box">
                                <div class="ps-label"><i class="ri-funds-line"></i> Colegiatura</div>
                                <div class="ps-total" style="font-size:.9rem;">
                                    @if(($ins->descuento_colegiatura ?? 0) > 0)
                                        <span class="text-muted" style="font-size:.7rem; text-decoration:line-through;">Bs {{ number_format($ins->monto_programa, 2) }}</span>
                                        <span class="text-muted" style="font-size:.7rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($ins->descuento_colegiatura, 2) }}</span>
                                    @endif
                                    <div class="fw-bold">Bs {{ number_format(max(0, (float)$ins->monto_programa - (float)($ins->descuento_colegiatura ?? 0)), 2) }}</div>
                                </div>
                                <div class="ps-meta">
                                    <span class="text-success fw-semibold"><i class="ri-check-line"></i> Pagado Bs {{ number_format($pd->totalColegiaturaPagado, 2) }}</span>
                                    @php $saldoColBox = max(0, max(0, (float)$ins->monto_programa - (float)($ins->descuento_colegiatura ?? 0)) - (float)$pd->totalColegiaturaPagado); @endphp
                                    @if($saldoColBox > 0)
                                        <span class="ms-2 text-danger fw-semibold"><i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoColBox, 2) }}</span>
                                    @elseif(max(0, (float)$ins->monto_programa - (float)($ins->descuento_colegiatura ?? 0)) > 0)
                                        <span class="ms-2 text-success fw-semibold"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                    @else
                                        <span class="ms-2 text-muted"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(count($pd->cuotas) > 0)
                        @php
                            $hoy = \Carbon\Carbon::today();
                            $countVencidas = 0; $countHoy = 0; $countProx = 0; $countPagadas = 0; $countSin = 0;
                            $sumaVencido = 0; $sumaProx = 0;
                            $proxDias = null;
                            foreach ($pd->cuotas as $cc) {
                                if ((float)$cc->total_cuota <= 0) continue;
                                $pagada = $cc->pagado >= $cc->total_cuota && $cc->total_cuota > 0;
                                if ($pagada) { $countPagadas++; continue; }
                                if (!$cc->fecha_pago) { $countSin++; continue; }
                                $fp = \Carbon\Carbon::parse($cc->fecha_pago)->startOfDay();
                                $diffDias = $hoy->diffInDays($fp, false);
                                if ($diffDias < 0) {
                                    $countVencidas++;
                                    $sumaVencido += max(0, $cc->total_cuota - $cc->pagado);
                                } elseif ($diffDias === 0) {
                                    $countHoy++;
                                    $sumaProx += max(0, $cc->total_cuota - $cc->pagado);
                                } else {
                                    $countProx++;
                                    $sumaProx += max(0, $cc->total_cuota - $cc->pagado);
                                    if ($proxDias === null || $diffDias < $proxDias) $proxDias = $diffDias;
                                }
                            }
                        @endphp

                        <div class="vencimientos-box">
                            <div class="venc-card" style="--vc-color:#f06548; --vc-icon-bg:#fef2f2; --vc-border:#fecaca;">
                                <span class="vc-icon"><i class="ri-error-warning-line"></i></span>
                                <div>
                                    <div class="vc-lbl">Vencidas</div>
                                    <div class="vc-val">{{ $countVencidas }}</div>
                                    <div class="vc-sub">Bs {{ number_format($sumaVencido, 2) }}</div>
                                </div>
                            </div>
                            <div class="venc-card" style="--vc-color:#f7b84b; --vc-icon-bg:#fffbeb; --vc-border:#fde68a;">
                                <span class="vc-icon"><i class="ri-time-line"></i></span>
                                <div>
                                    <div class="vc-lbl">Vence Hoy</div>
                                    <div class="vc-val">{{ $countHoy }}</div>
                                    <div class="vc-sub">cuota(s)</div>
                                </div>
                            </div>
                            <div class="venc-card" style="--vc-color:#299cdb; --vc-icon-bg:#f0f9ff; --vc-border:#bae6fd;">
                                <span class="vc-icon"><i class="ri-calendar-event-line"></i></span>
                                <div>
                                    <div class="vc-lbl">Próximas</div>
                                    <div class="vc-val">{{ $countProx }}</div>
                                    <div class="vc-sub">{{ $proxDias !== null ? 'En '.$proxDias.' días' : 'Bs '.number_format($sumaProx, 2) }}</div>
                                </div>
                            </div>
                            <div class="venc-card" style="--vc-color:#0ab39c; --vc-icon-bg:#ecfdf5; --vc-border:#a7f3d8;">
                                <span class="vc-icon"><i class="ri-checkbox-circle-line"></i></span>
                                <div>
                                    <div class="vc-lbl">Pagadas</div>
                                    <div class="vc-val">{{ $countPagadas }}</div>
                                    <div class="vc-sub">{{ $countSin > 0 ? $countSin.' sin fecha' : 'completo' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="modern-table table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">N°</th>
                                        <th style="width: 110px;">Tipo</th>
                                        <th style="width: 170px;">Vencimiento</th>
                                        <th style="width: 140px;">Estado Vencim.</th>
                                        <th style="width: 160px;">Último Pago</th>
                                        <th class="text-end">Deuda</th>
                                        <th class="text-end">Pagado</th>
                                        <th style="width: 110px;" class="text-center">Estado</th>
                                        <th style="width: 130px;" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pd->cuotas as $ci => $c)
                                    @if((float)$c->total_cuota <= 0) @continue @endif
                                    @php
                                        $tipoLabel = $c->tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad';
                                        $tipoColor = $c->tipo === 'Matricula' ? '#299cdb' : '#878a99';
                                        $pagosCuota = $pd->pagosPorCuota->get($c->id, collect());
                                        $pagada = $c->pagado >= $c->total_cuota && $c->total_cuota > 0;
                                        $estado = $pagada ? 'Cancelado' : 'Pendiente';
                                        $estadoColor = $pagada ? '#0ab39c' : '#f7b84b';
                                        $fechaUltimoPago = null;
                                        if ($pagosCuota->count() > 0) {
                                            $fechas = $pagosCuota->pluck('fecha')->filter()->map(fn($f) => strtotime($f))->sort();
                                            if ($fechas->count() > 0) $fechaUltimoPago = $fechas->last();
                                        }

                                        // Estado del vencimiento
                                        $vencPill = '';
                                        $rowExtraCls = '';
                                        if ($pagada) {
                                            $vencPill = '<span class="due-pill is-pag"><i class="ri-checkbox-circle-line"></i> Pagada</span>';
                                        } elseif (!$c->fecha_pago) {
                                            $vencPill = '<span class="due-pill is-sin"><i class="ri-question-line"></i> Sin fecha</span>';
                                        } else {
                                            $fp = \Carbon\Carbon::parse($c->fecha_pago)->startOfDay();
                                            $diff = $hoy->diffInDays($fp, false);
                                            if ($diff < 0) {
                                                $diasV = abs($diff);
                                                $vencPill = '<span class="due-pill is-venc"><i class="ri-error-warning-line"></i> Vencido · ' . $diasV . ' día' . ($diasV !== 1 ? 's' : '') . '</span>';
                                                $rowExtraCls = 'due-row';
                                            } elseif ($diff === 0) {
                                                $vencPill = '<span class="due-pill is-hoy"><i class="ri-alarm-warning-line"></i> Vence hoy</span>';
                                            } else {
                                                $cls = $diff <= 7 ? 'is-prox' : 'is-futuro';
                                                $vencPill = '<span class="due-pill ' . $cls . '"><i class="ri-time-line"></i> En ' . $diff . ' día' . ($diff !== 1 ? 's' : '') . '</span>';
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $rowExtraCls }}">
                                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="estado-pill" style="color: {{ $tipoColor }};">{{ $tipoLabel }}</span>
                                        </td>
                                        <td>
                                            <div class="due-cell">
                                                @if($c->fecha_pago)
                                                    <span class="due-fecha"><i class="ri-calendar-check-line text-primary"></i> {{ $fechaEs($c->fecha_pago) }}</span>
                                                @else
                                                    <span class="text-muted fst-italic"><i class="ri-calendar-close-line"></i> Sin fecha de pago</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{!! $vencPill !!}</td>
                                        <td class="text-muted" style="font-size: .78rem;">
                                            @if($fechaUltimoPago)
                                                <i class="ri-calendar-2-line align-bottom me-1"></i>{{ $fechaEs($fechaUltimoPago) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold text-dark">Bs {{ number_format($c->total_cuota, 2) }}</td>
                                        <td class="text-end fw-semibold {{ $c->pagado > 0 ? 'text-success' : 'text-muted' }}">{{ $c->pagado > 0 ? 'Bs '.number_format($c->pagado, 2) : '—' }}</td>
                                        <td class="text-center">
                                            <span class="estado-pill" style="color: {{ $estadoColor }};">{{ $estado }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="hstack gap-1 justify-content-center">
                                                @if($puedePagar)
                                                <button type="button" class="btn-pay-row btn-pago-cuota"
                                                        data-cuota-id="{{ $c->id }}"
                                                        data-tipo="{{ $tipoLabel }}"
                                                        data-deuda="{{ $c->total_cuota }}"
                                                        data-pagado="{{ $c->pagado }}"
                                                        data-saldo="{{ max(0, $c->total_cuota - $c->pagado) }}"
                                                        data-fecha="{{ $c->fecha_pago }}"
                                                        {{ $pagada ? 'disabled' : '' }}
                                                        title="{{ $pagada ? 'Cuota ya pagada' : 'Registrar pago' }}">
                                                    <i class="ri-money-dollar-circle-line"></i>
                                                </button>
                                                @endif
                                                @if($pagosCuota->count() > 0)
                                                <button type="button" class="btn btn-sm btn-icon btn-soft-info" data-bs-toggle="modal" data-bs-target="#modal-pagos-{{ $idx }}-{{ $c->id }}" title="Ver pagos">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Modales de pagos por cuota --}}
                        @foreach($pd->cuotas as $c)
                        @php
                            $pagosCuota = $pd->pagosPorCuota->get($c->id, collect());
                            $tipoLabel = $c->tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad';
                        @endphp
                        <div class="modal fade modal-pagos" id="modal-pagos-{{ $idx }}-{{ $c->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="modal-header-icon"><i class="ri-money-dollar-circle-line"></i></div>
                                            <div>
                                                <h6 class="modal-title">Pagos de la Cuota #{{ $loop->iteration }}</h6>
                                                <small class="text-white-50" style="font-size:.72rem;">Tipo: {{ $tipoLabel }} · Deuda Bs {{ number_format($c->total_cuota, 2) }}</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if(count($pagosCuota) > 0)
                                        <div class="section-card mb-0">
                                            <div class="table-responsive">
                                                <table class="modern-table table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 40px;" class="text-center">#</th>
                                                            <th>Fecha</th>
                                                            <th class="text-end">Monto</th>
                                                            <th style="width: 50px;" class="text-center">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pagosCuota as $pi => $p)
                                                        <tr>
                                                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                            <td><i class="ri-calendar-line text-muted me-1"></i>{{ $fechaEs($p->fecha) }}</td>
                                                            <td class="text-end fw-semibold text-success">Bs {{ number_format($p->pago_bs, 2) }}</td>
                                                            <td class="text-center">
                                                                @if($puedePagar)
                                                                <button type="button" class="btn btn-sm btn-icon btn-soft-danger btn-eliminar-pago" data-pago-id="{{ $p->pago_id }}" data-monto="{{ number_format($p->pago_bs, 2) }}" title="Eliminar pago">
                                                                    <i class="ri-delete-bin-6-line"></i>
                                                                </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @else
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="ri-money-dollar-circle-line"></i></div>
                                            <p class="text-muted mb-0">No hay pagos registrados para esta cuota</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
                            <p class="text-muted mb-0">No hay cuotas registradas para este programa</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>
</div>
@else
<div class="empty-state">
    <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">Sin inscripciones</h5>
    <p class="text-muted mb-0">El participante no está inscrito en ningún programa.</p>
</div>
@endif

{{-- Modal Pago Individual --}}
<div class="modal fade pago-modal" id="modalPagoCuota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Registrar Pago</h5>
                        <small class="modal-subtitle" id="mpcSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pago-info-box">
                    <div class="pib-row"><span>Deuda total</span><strong id="mpcDeuda">Bs 0.00</strong></div>
                    <div class="pib-row"><span>Pagado</span><strong class="text-success" id="mpcPagado">Bs 0.00</strong></div>
                    <div class="pib-row" style="border-top:1px dashed #eef0f4; padding-top:.4rem; margin-top:.2rem;"><span>Saldo</span><strong class="text-danger" id="mpcSaldo">Bs 0.00</strong></div>
                    <div class="pib-row" id="mpcFechaWrap" style="display:none;"><span>Vencimiento</span><strong id="mpcFecha">—</strong></div>
                </div>
                <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Monto a pagar</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Bs</span>
                    <input type="number" step="0.01" min="0.01" class="form-control input-monto" id="mpcMonto" placeholder="0.00">
                </div>
                <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Fecha de pago</label>
                <input type="date" class="form-control" id="mpcFechaPago" value="{{ date('Y-m-d') }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarPagoCuota">
                    <i class="ri-save-3-line"></i> Registrar Pago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pago General --}}
<div class="modal fade pago-modal" id="modalPagoGeneral" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-wallet-3-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Pago General</h5>
                        <small class="modal-subtitle">Asigna montos a las cuotas pendientes</small>
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
                    <div class="pg-stat" style="--pg-color:#405189; --pg-border:#c3cfe2;">
                        <div class="pg-stat-lbl">Cuotas Pendientes</div>
                        <div class="pg-stat-val" id="pgCountPend">0</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#f06548; --pg-border:#fecaca;">
                        <div class="pg-stat-lbl">Total Saldo</div>
                        <div class="pg-stat-val" id="pgTotalSaldo">Bs 0.00</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#0ab39c; --pg-border:#a7f3d8;">
                        <div class="pg-stat-lbl">A Aplicar</div>
                        <div class="pg-stat-val" id="pgAplicar">Bs 0.00</div>
                    </div>
                    <div class="pg-stat" style="--pg-color:#299cdb; --pg-border:#bae6fd;">
                        <div class="pg-stat-lbl">Queda Disponible</div>
                        <div class="pg-stat-val" id="pgQueda">Bs 0.00</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="pg-table table">
                        <thead>
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th>Cuota</th>
                                <th style="width:140px;">Vencimiento</th>
                                <th class="text-end" style="width:90px;">Deuda</th>
                                <th class="text-end" style="width:90px;">Pagado</th>
                                <th class="text-end" style="width:90px;">Saldo</th>
                                <th class="text-end" style="width:130px;">A pagar</th>
                            </tr>
                        </thead>
                        <tbody id="pgTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarPagoGeneral" disabled>
                    <i class="ri-save-3-line"></i> <span id="btnPgTxt">Registrar Pago</span>
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
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = '{{ url("admin/programas/participante") }}';
    const diasEs = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    const estadoColors = {
        'Desarrollado': '#0ab39c',
        'Confirmado': '#405189',
        'Programado': '#878a99',
        'Postergado': '#f7b84b',
    };
    const estadoIcons = {
        'Desarrollado': 'ri-checkbox-circle-line',
        'Confirmado': 'ri-shield-check-line',
        'Programado': 'ri-calendar-line',
        'Postergado': 'ri-time-line',
    };

    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    }

    function fechaTxt(f) {
        const d = new Date(f + 'T00:00:00');
        if (isNaN(d)) return f;
        return diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()] + ' del ' + d.getFullYear();
    }

    function hhmm(t) { return (t || '').toString().substring(0, 5); }

    function asistTag(val) {
        if (!val) return '<span class="asist-tag is-sin"><i class="ri-subtract-line"></i> Sin registro</span>';
        const map = {
            'Presente':    ['is-pres', 'ri-checkbox-circle-line'],
            'Ausente':     ['is-aus',  'ri-close-circle-line'],
            'Tardanza':    ['is-tar',  'ri-time-line'],
            'Justificado': ['is-jus',  'ri-file-text-line'],
        };
        const [cls, ic] = map[val] || ['is-sin', 'ri-question-line'];
        return '<span class="asist-tag ' + cls + '"><i class="' + ic + '"></i> ' + val + '</span>';
    }

    const csrf = '{{ csrf_token() }}';
    const urlPagoCuota = '{{ url("admin/programas/cuota") }}';
    const urlPagoMultiple = '{{ route("admin.programas.pago-multiple") }}';

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
        el.style.boxShadow = '0 10px 24px -10px rgba(0,0,0,.3)';
        el.innerHTML = '<div class="d-flex align-items-center"><div class="toast-body d-flex align-items-center gap-2 fs-13 fw-semibold"><i class="' + (icons[type] || icons.success) + ' fs-5"></i><span>' + msg + '</span></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        try { const inst = new bootstrap.Toast(el, { delay: 3500 }); inst.show(); el.addEventListener('hidden.bs.toast', () => el.remove()); }
        catch (e) { setTimeout(() => el.remove(), 3500); }
    }

    // ===== PAGO INDIVIDUAL =====
    const modalPagoCuotaEl = document.getElementById('modalPagoCuota');
    const modalPagoCuota = new bootstrap.Modal(modalPagoCuotaEl);
    let currentCuotaPay = null;

    document.querySelectorAll('.btn-pago-cuota').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (this.disabled) return;
            currentCuotaPay = {
                id: this.dataset.cuotaId,
                tipo: this.dataset.tipo,
                deuda: parseFloat(this.dataset.deuda),
                pagado: parseFloat(this.dataset.pagado),
                saldo: parseFloat(this.dataset.saldo),
                fecha: this.dataset.fecha,
            };
            document.getElementById('mpcSubtitle').textContent = currentCuotaPay.tipo + ' · Cuota #' + currentCuotaPay.id;
            document.getElementById('mpcDeuda').textContent = fmt(currentCuotaPay.deuda);
            document.getElementById('mpcPagado').textContent = fmt(currentCuotaPay.pagado);
            document.getElementById('mpcSaldo').textContent = fmt(currentCuotaPay.saldo);
            document.getElementById('mpcMonto').value = currentCuotaPay.saldo.toFixed(2);
            document.getElementById('mpcMonto').setAttribute('max', currentCuotaPay.saldo.toFixed(2));
            if (currentCuotaPay.fecha) {
                document.getElementById('mpcFecha').textContent = currentCuotaPay.fecha;
                document.getElementById('mpcFechaWrap').style.display = 'flex';
            } else {
                document.getElementById('mpcFechaWrap').style.display = 'none';
            }
            modalPagoCuota.show();
        });
    });

    document.getElementById('btnGuardarPagoCuota').addEventListener('click', function () {
        if (!currentCuotaPay) return;
        const monto = parseFloat(document.getElementById('mpcMonto').value);
        const fecha = document.getElementById('mpcFechaPago').value;
        if (!monto || monto <= 0) { showToast('Ingresa un monto válido', 'warning'); return; }
        if (monto > currentCuotaPay.saldo + 0.01) { showToast('El monto excede el saldo', 'warning'); return; }

        const btn = this;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const fd = new FormData();
        fd.append('_token', csrf);
        fd.append('monto', monto);
        fd.append('fecha', fecha);

        fetch(urlPagoCuota + '/' + currentCuotaPay.id + '/pago', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalPagoCuota.hide();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión', 'danger'))
        .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
    });

    // ===== PAGO GENERAL =====
    const modalPagoGeneralEl = document.getElementById('modalPagoGeneral');
    const modalPagoGeneral = new bootstrap.Modal(modalPagoGeneralEl);
    let pgCuotas = [];

    function tipoLabel(tipo) {
        return tipo === 'Matricula' ? 'Matrícula' : 'Mensualidad';
    }

    function fechaSimple(iso) {
        if (!iso) return '<span class="text-muted fst-italic">Sin fecha</span>';
        const d = new Date(iso + 'T00:00:00');
        if (isNaN(d)) return iso;
        return d.getDate() + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
    }

    function vencInfo(iso) {
        if (!iso) return { html: '<span class="text-muted fst-italic">—</span>', dias: null, isVenc: false };
        const today = new Date(); today.setHours(0,0,0,0);
        const d = new Date(iso + 'T00:00:00');
        const diff = Math.round((d - today) / 86400000);
        if (diff < 0) {
            return { html: '<span class="due-pill is-venc"><i class="ri-error-warning-line"></i> Vencido ' + Math.abs(diff) + 'd</span>', dias: diff, isVenc: true };
        }
        if (diff === 0) {
            return { html: '<span class="due-pill is-hoy"><i class="ri-alarm-warning-line"></i> Hoy</span>', dias: 0, isVenc: false };
        }
        const cls = diff <= 7 ? 'is-prox' : 'is-futuro';
        return { html: '<span class="due-pill ' + cls + '"><i class="ri-time-line"></i> En ' + diff + 'd</span>', dias: diff, isVenc: false };
    }

    document.querySelectorAll('.btn-pago-general').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const idx = this.dataset.progIdx;
            const card = document.querySelector('.section-card[data-prog-idx="' + idx + '"]');
            if (!card) return;
            let cuotas = [];
            try { cuotas = JSON.parse(card.dataset.cuotas || '[]'); } catch (e) { cuotas = []; }
            // Solo pendientes
            pgCuotas = cuotas.filter(c => c.saldo > 0).map(c => {
                return { ...c, a_pagar: 0 };
            });
            // Orden: vencidas primero (fecha asc), luego sin fecha al final
            pgCuotas.sort(function (a, b) {
                if (!a.fecha_pago && b.fecha_pago) return 1;
                if (a.fecha_pago && !b.fecha_pago) return -1;
                if (!a.fecha_pago && !b.fecha_pago) return 0;
                return a.fecha_pago < b.fecha_pago ? -1 : 1;
            });

            document.getElementById('pgMontoTotal').value = '';
            renderPgTable();
            actualizarTotales();
            modalPagoGeneral.show();
        });
    });

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
                    '<td><div class="fw-semibold">' + tipoLabel(c.tipo) + '</div><div class="text-muted" style="font-size:.7rem;">#' + c.id + '</div></td>' +
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
                if (v > pgCuotas[i].saldo) {
                    v = pgCuotas[i].saldo;
                    this.value = v.toFixed(2);
                }
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

        // Validación monto total
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
            montoFeedback.textContent = 'El monto (Bs ' + fmt(monto) + ') supera la deuda total (Bs ' + fmt(total) + ')';
            montoFeedback.className = 'form-text text-danger';
        } else if (monto > 0 && aplicar > 0 && Math.abs(queda) > 0.01) {
            montoFeedback.textContent = 'Queda Bs ' + fmt(queda) + ' sin distribuir';
            montoFeedback.className = 'form-text text-warning';
        } else if (monto <= 0 && aplicar <= 0) {
            montoFeedback.textContent = '';
            montoFeedback.className = 'form-text';
        } else {
            montoFeedback.textContent = '';
            montoFeedback.className = 'form-text';
        }

        // Validación individual por cuota
        let inputsValidos = true;
        document.querySelectorAll('.pg-input-monto').forEach(function (inp) {
            const i = parseInt(inp.dataset.cidx);
            const val = parseFloat(inp.value) || 0;
            inp.classList.remove('is-invalid');
            if (val < 0 || val > pgCuotas[i].saldo) {
                inp.classList.add('is-invalid');
                inputsValidos = false;
            }
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
        if (monto > total) {
            actualizarTotales();
            return;
        }
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

    // Distribución en vivo al escribir el monto total
    document.getElementById('pgMontoTotal').addEventListener('input', distribuirAuto);

    document.getElementById('btnPgLimpiar').addEventListener('click', function () {
        pgCuotas.forEach(c => c.a_pagar = 0);
        document.getElementById('pgMontoTotal').value = '';
        renderPgTable();
        actualizarTotales();
    });

    const modalConfirmarPago = new bootstrap.Modal(document.getElementById('modalConfirmarPago'));

    document.getElementById('btnGuardarPagoGeneral').addEventListener('click', function () {
        const fecha = document.getElementById('pgFecha').value;
        const aplicaciones = pgCuotas.filter(c => c.a_pagar > 0).map(c => ({
            cuota_id: c.id,
            monto: c.a_pagar,
        }));
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

        fetch(urlPagoMultiple, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ fecha: fecha, aplicaciones: aplicaciones }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            modalConfirmarPago.hide();
            if (ok && data.success) {
                showToast(data.message + ' · ' + fmt(data.total_aplicado) + ' en ' + data.cuotas_afectadas + ' cuota(s)', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => { modalConfirmarPago.hide(); showToast('Error de conexión', 'danger'); })
        .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
    });

    document.querySelectorAll('.btn-expand-horarios').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            const isOpen = target.style.display !== 'none';
            if (isOpen) {
                target.style.display = 'none';
                this.classList.remove('is-open');
                return;
            }
            target.style.display = '';
            this.classList.add('is-open');

            if (target.dataset.loaded === '1') return;

            const wrap = target.querySelector('.horarios-wrap');
            fetch(baseUrl + '/' + this.dataset.participante + '/modulo/' + this.dataset.modulo + '/horarios', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    wrap.innerHTML = '<div class="horarios-empty">' + (data.message || 'Error al cargar') + '</div>';
                    return;
                }
                if (!data.horarios.length) {
                    wrap.innerHTML = '<div class="horarios-empty"><i class="ri-calendar-close-line"></i> Sin sesiones programadas</div>';
                    target.dataset.loaded = '1';
                    return;
                }

                const r = data.resumen;
                let summary =
                    '<div class="horarios-summary">' +
                        '<span class="h-stat is-total"><i class="ri-calendar-line"></i> ' + data.horarios.length + ' sesiones</span>' +
                        '<span class="h-stat is-des"><i class="ri-checkbox-circle-line"></i> ' + data.desarrolladas + ' desarrolladas</span>' +
                        '<span class="h-stat is-pres"><i class="ri-checkbox-circle-line"></i> Presente: ' + r.Presente + '</span>' +
                        '<span class="h-stat is-aus"><i class="ri-close-circle-line"></i> Ausente: ' + r.Ausente + '</span>' +
                        '<span class="h-stat is-tar"><i class="ri-time-line"></i> Tardanza: ' + r.Tardanza + '</span>' +
                        '<span class="h-stat is-jus"><i class="ri-file-text-line"></i> Justificado: ' + r.Justificado + '</span>' +
                        '<span class="h-stat is-pct"><i class="ri-percent-line"></i> ' + data.porcentaje + '% asistencia</span>' +
                    '</div>';

                let rows = '';
                data.horarios.forEach(function (h, i) {
                    const color = estadoColors[h.estado] || '#878a99';
                    const icon = estadoIcons[h.estado] || 'ri-information-line';
                    const fecha = fechaTxt(h.fecha_desarrollo);
                    const partes = fecha.split(' ');
                    const dia = partes.shift() || '';
                    const resto = partes.join(' ');
                    const showAsist = h.estado === 'Desarrollado';
                    const asistCell = showAsist
                        ? asistTag(h.asistio)
                        : '<span class="text-muted fst-italic" style="font-size:.75rem;">No desarrollada</span>';
                    const obsAsist = showAsist && h.observacion_asistencia
                        ? '<div class="text-muted fst-italic mt-1" style="font-size:.72rem;"><i class="ri-chat-1-line"></i> ' + escHtml(h.observacion_asistencia) + '</div>'
                        : '';
                    rows +=
                        '<tr>' +
                            '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                            '<td>' +
                                '<div class="session-fecha-main">' + dia + '</div>' +
                                '<div class="session-fecha-sub">' + resto + '</div>' +
                            '</td>' +
                            '<td><span class="estado-pill" style="color:#495057;">' + hhmm(h.hora_inicio) + ' - ' + hhmm(h.hora_fin) + '</span></td>' +
                            '<td><span class="estado-pill" style="color:' + color + ';"><i class="' + icon + '"></i>' + h.estado + '</span></td>' +
                            '<td>' + asistCell + obsAsist + '</td>' +
                            '<td class="text-muted" style="font-size:.75rem;">' + (h.observaciones ? escHtml(h.observaciones) : '—') + '</td>' +
                        '</tr>';
                });

                wrap.innerHTML = summary +
                    '<div class="table-responsive">' +
                        '<table class="session-table table mb-0">' +
                            '<thead><tr>' +
                                '<th style="width:40px;" class="text-center">#</th>' +
                                '<th>Fecha</th>' +
                                '<th style="width:130px;">Horario</th>' +
                                '<th style="width:140px;">Estado</th>' +
                                '<th style="width:170px;">Mi Asistencia</th>' +
                                '<th>Obs. de sesión</th>' +
                            '</tr></thead>' +
                            '<tbody>' + rows + '</tbody>' +
                        '</table>' +
                    '</div>';

                target.dataset.loaded = '1';
            })
            .catch(() => {
                wrap.innerHTML = '<div class="horarios-empty text-danger">Error de conexión al cargar las sesiones</div>';
            });
        });
    });

    // Eliminar pago
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-eliminar-pago');
        if (!btn) return;
        const pagoId = btn.dataset.pagoId;
        const monto = btn.dataset.monto;
        if (!confirm('¿Estás seguro de eliminar este pago de Bs ' + monto + '?\nEsta acción no se puede deshacer.')) return;
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');
        fetch('{{ route("admin.pagos.eliminar", ["pago" => "_ID_"]) }}'.replace('_ID_', pagoId), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(function () {
            alert('Error de conexión al eliminar el pago');
        });
    });
});
</script>
@endpush
