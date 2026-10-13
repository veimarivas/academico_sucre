@extends('admin.layouts.master')

@section('title', 'Seguimiento Académico - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.seg-hero {
    position: relative;
    background: linear-gradient(135deg, #3b4a80 0%, #5e72b8 45%, #299cdb 100%);
    border-radius: 18px;
    padding: 1.6rem 1.85rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 14px 34px -14px rgba(64,81,137,.5);
    margin-bottom: 1.4rem;
}
.seg-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 240px; height: 240px; background: radial-gradient(circle, rgba(255,255,255,.2) 0%, transparent 70%); border-radius: 50%; }
.seg-hero::after { content: ""; position: absolute; bottom: -70px; left: 28%; width: 280px; height: 280px; background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%); border-radius: 50%; }
.seg-hero > * { position: relative; z-index: 1; }
.seg-hero .hero-icon {
    width: 58px; height: 58px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 16px;
    font-size: 1.65rem;
    backdrop-filter: blur(6px);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25);
}
.seg-hero h4 { color: #fff !important; font-weight: 700; letter-spacing: .2px; margin: 0; }
.seg-hero .hero-sub { color: rgba(255,255,255,.88) !important; font-size: .88rem; margin: 0; }
.seg-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.seg-hero .breadcrumb-item, .seg-hero .breadcrumb-item a { color: rgba(255,255,255,.85) !important; }
.seg-hero .breadcrumb-item.active { color: #fff !important; font-weight: 600; }
.seg-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55) !important; }
.seg-hero .hero-date {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 999px;
    padding: .28rem .85rem;
    font-size: .74rem; font-weight: 600;
    color: #fff;
    backdrop-filter: blur(4px);
    text-transform: capitalize;
}

/* ===== Stat Cards (fila compacta unificada) ===== */
.stat-card {
    border: 1px solid #eef1f6;
    border-radius: 16px;
    background: #fff;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    overflow: hidden;
    position: relative;
    height: 100%;
}
.stat-card:hover { transform: translateY(-4px); border-color: transparent; box-shadow: 0 16px 32px -16px rgba(64,81,137,.35); }
.stat-card::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--stat-accent, #405189), transparent 85%); border-radius: 16px 16px 0 0; opacity: .85; }
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .card-body { padding: .9rem 1rem; }
.stat-card .stat-label { font-size: .62rem; text-transform: uppercase; letter-spacing: .5px; color: #878a99; font-weight: 700; margin-bottom: .3rem; line-height: 1.3; white-space: normal; }
.stat-card .stat-value { font-size: 1.35rem; font-weight: 800; color: #212529; line-height: 1.15; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }
.stat-card .stat-icon {
    width: 40px; height: 40px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px; font-size: 1.15rem; flex-shrink: 0;
    transition: transform .25s ease;
}
.stat-card:hover .stat-icon { transform: scale(1.08) rotate(-4deg); }

/* ===== Filter + Search bar ===== */
.filter-card { border: 1px solid #eef1f6; border-radius: 16px; background: #fff; box-shadow: 0 4px 20px -12px rgba(64,81,137,.2); }
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-body { padding: 1rem 1.25rem; }
.filter-card .form-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #878a99; margin-bottom: .35rem; }
.filter-card .form-select { border: 1px solid #e9ebec; border-radius: 10px; font-weight: 500; }
.filter-card .form-select:focus { border-color: #405189; box-shadow: 0 0 0 .18rem rgba(64,81,137,.1); }
.filter-card .btn { border-radius: 10px; }

/* ===== Tabs ===== */
.show-tabs-card {
    border: 1px solid #eef1f6;
    border-radius: 18px;
    box-shadow: 0 6px 24px -14px rgba(64,81,137,.22);
    background: #fff;
}
[data-layout-mode="dark"] .show-tabs-card { background: #212529; border-color: #2a2f34; }
.nav-tabs-pill {
    border: 0; gap: .35rem;
    background: #f3f5f9; padding: .45rem;
    border-radius: 14px;
    display: inline-flex; flex-wrap: wrap;
    margin-bottom: 1.4rem;
}
[data-layout-mode="dark"] .nav-tabs-pill { background: #1a1d20; }
.nav-tabs-pill .nav-link {
    border: 0 !important;
    border-radius: 10px;
    color: #495057; font-weight: 600; font-size: .82rem;
    padding: .55rem 1.05rem; cursor: pointer;
    transition: all .22s ease;
    display: inline-flex; align-items: center; gap: .4rem;
    background: transparent;
}
.nav-tabs-pill .nav-link:hover { color: #405189; background: rgba(64,81,137,.07); }
.nav-tabs-pill .nav-link.active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff !important;
    box-shadow: 0 8px 18px -8px rgba(64,81,137,.6);
}
.nav-tabs-pill .nav-link .badge { background: rgba(64,81,137,.14); color: #405189; font-weight: 700; border-radius: 999px; padding: .3em .6em; }
.nav-tabs-pill .nav-link.active .badge { background: rgba(255,255,255,.25) !important; color: #fff !important; }

/* ===== Programa block ===== */
.programa-block {
    border: 1px solid #eaedf3;
    border-radius: 16px;
    background: #fff;
    margin-bottom: 1.35rem;
    overflow: hidden;
    transition: box-shadow .25s ease, transform .25s ease;
    box-shadow: 0 2px 10px -8px rgba(64,81,137,.14);
    animation: segFadeUp .4s ease both;
}
@keyframes segFadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
@media (prefers-reduced-motion: reduce) {
    .programa-block { animation: none; }
}
.programa-block:hover { box-shadow: 0 14px 30px -14px rgba(64,81,137,.32); }
[data-layout-mode="dark"] .programa-block { background: #212529; border-color: #2a2f34; box-shadow: none; }
.programa-header {
    padding: 1rem 1.3rem;
    background: linear-gradient(135deg, #f2f5fb 0%, #e8edf7 100%);
    border-bottom: 1px solid #eaedf3;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
    border-left: 4px solid #405189;
    border-radius: 16px 16px 0 0;
}
.programa-header--danger { border-left-color: #f06548; background: linear-gradient(135deg, #fef0ee 0%, #fde5e2 100%); }
.programa-header--success { border-left-color: #0ab39c; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
[data-layout-mode="dark"] .programa-header { background: #1a1d20; border-bottom-color: #2a2f34; border-left-color: #5e72b8; }
[data-layout-mode="dark"] .programa-header--danger { border-left-color: #f8796b; background: rgba(240,101,72,.1); }
[data-layout-mode="dark"] .programa-header--success { border-left-color: #0ab39c; background: rgba(10,179,156,.1); }
.programa-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 11px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.programa-title { font-weight: 700; color: #1a1d21; font-size: 1rem; margin: 0; line-height: 1.2; }
[data-layout-mode="dark"] .programa-title { color: #e8eaed; }
.programa-meta { font-size: .72rem; color: #878a99; margin-top: 3px; display: inline-flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
.programa-count {
    display: inline-flex; align-items: center; gap: .35rem;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff; border-radius: 999px;
    padding: .35em .85em; font-size: .78rem; font-weight: 700;
}

/* ===== Module highlight ===== */
.modulo-card-header--curso,
.modulo-card-header--reciente {
    border-left-color: #299cdb !important;
    background: #e0f3ff !important;
}
.modulo-card-header--proximo {
    border-left-color: #52c41a !important;
    background: #e6f7e6 !important;
}
[data-layout-mode="dark"] .modulo-card-header--curso,
[data-layout-mode="dark"] .modulo-card-header--reciente {
    border-left-color: #299cdb !important;
    background: rgba(41,156,219,.12) !important;
}
[data-layout-mode="dark"] .modulo-card-header--proximo {
    border-left-color: #52c41a !important;
    background: rgba(82,196,26,.12) !important;
}
.modulo-moodle-link {
    display:inline-flex;align-items:center;justify-content:center;
    width:20px;height:20px;background:#fff4e6;color:#f76707;
    border:1px solid #ffd8a8;border-radius:4px;margin-left:5px;vertical-align:middle;
    text-decoration:none;font-size:.65rem;transition:all .15s ease;
}
.modulo-moodle-link:hover { background:#f76707;color:#fff;transform:translateY(-1px); }
.modulo-highlight-chip {
    display:inline-flex;align-items:center;gap:4px;
    font-size:.65rem;font-weight:700;
    padding:.25em .65em;border-radius:999px;
    border:1px solid;
}
.modulo-highlight-chip--active {
    background:#dbeafe;color:#299cdb;border-color:#bae6fd;
}
.modulo-highlight-chip--proximo {
    background:#e6f7e6;color:#52c41a;border-color:#b7eb8f;
}
/* ===== Módulo card ===== */
.modulo-card {
    border: 1px solid #edeff4;
    border-radius: 12px;
    margin: .7rem .85rem;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 5px -4px rgba(0,0,0,.07);
    transition: box-shadow .22s ease, border-color .22s ease;
}
.modulo-card:hover { box-shadow: 0 6px 16px -10px rgba(64,81,137,.22); border-color: #dfe4ee; }
[data-layout-mode="dark"] .modulo-card { background: #1d2125; border-color: #2a2f34; }
.modulo-card-header {
    padding: .7rem 1.05rem;
    background: #fafcff;
    border-bottom: 1px solid #edeff4;
    border-left: 3px solid #cdd7e6;
    display: flex; align-items: center; justify-content: space-between;
    gap: .75rem; flex-wrap: wrap;
}
[data-layout-mode="dark"] .modulo-card-header { background: #22262a; border-bottom-color: #2a2f34; border-left-color: #3a3f44; }
.modulo-title-block { display: flex; align-items: center; gap: .65rem; min-width: 0; flex: 1; }
.modulo-num-badge {
    width: 30px; height: 30px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 800;
    flex-shrink: 0;
}
.modulo-nombre { font-weight: 700; color: #1a1d21; font-size: .9rem; line-height: 1.2; }
[data-layout-mode="dark"] .modulo-nombre { color: #e8eaed; }
.modulo-docente { font-size: .72rem; color: #878a99; margin-top: 2px; display: inline-flex; align-items: center; gap: .35rem; }
.docente-avatar-mini {
    width: 22px; height: 22px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff; font-weight: 700; font-size: .6rem;
}

.modulo-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
.chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: .3em .75em;
    border-radius: 999px;
    border: 1px solid;
}
.chip::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.chip-en-desarrollo { background: #dbeafe; color: #299cdb; border-color: #c3ddfa; }
.chip-no-inicio { background: #f1f3f5; color: #495057; border-color: #d6dbe0; }
.chip-culminado { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.chip-vencido { background: #fee2e2; color: #f06548; border-color: #fecaca; }
.chip-warning { background: #fef3c7; color: #d97706; border-color: #fde68a; }

/* ===== Tabla horarios ===== */
.horarios-table { margin: 0; width: 100%; }
.horarios-table thead th {
    background: #fafbfd !important;
    font-weight: 700; font-size: .65rem;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .6rem .75rem;
    border-top: 0;
}
.horarios-table tbody td {
    padding: .6rem .75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f5;
    font-size: .78rem; border-top: 0;
}
.horarios-table tbody tr { transition: background-color .15s ease; }
.horarios-table tbody tr:hover { background-color: #f8f9fc; }
.horarios-table tbody tr:last-child td { border-bottom: 0; }
.horarios-table tbody tr.row-pasado { background-color: #fff7f5; }
.horarios-table tbody tr.row-pasado:hover { background-color: #ffeae5; }
.horarios-table tbody tr.row-hoy { background-color: #fffbeb; }
.horarios-table tbody tr.row-en-rango { background-color: #ecfdf5; border-left: 3px solid #0ab39c; }
.horarios-table tbody tr.row-en-rango:hover { background-color: #d1fae5; }
[data-layout-mode="dark"] .horarios-table tbody tr.row-en-rango { background-color: rgba(10,179,156,.08); }
[data-layout-mode="dark"] .horarios-table thead th { background: #1d2125 !important; border-bottom-color: #2a2f34 !important; }
[data-layout-mode="dark"] .horarios-table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .horarios-table tbody tr:hover { background-color: #1a1d20; }

.horario-fecha {
    display: inline-flex; flex-direction: column; align-items: center;
    line-height: 1.1;
    background: #fff;
    border: 1px solid #e9edf4;
    border-radius: 10px;
    padding: .3rem .55rem;
    min-width: 58px;
    box-shadow: 0 1px 3px -2px rgba(0,0,0,.08);
}
.horario-fecha .dia { font-weight: 800; color: #212529; font-size: .95rem; }
.horario-fecha .mes { font-size: .62rem; color: #878a99; text-transform: capitalize; font-weight: 600; }
[data-layout-mode="dark"] .horario-fecha { background: #22262a; border-color: #2a2f34; }
[data-layout-mode="dark"] .horario-fecha .dia { color: #fff; }
.horario-hora {
    font-weight: 700; color: #495057;
    font-variant-numeric: tabular-nums;
    font-size: .76rem;
    background: #f5f7fa;
    border-radius: 8px;
    padding: .22rem .55rem;
    display: inline-block;
}
[data-layout-mode="dark"] .horario-hora { background: #22262a; color: #ced4da; }

.asignado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 600;
    padding: .25em .65em;
    border-radius: 999px;
    color: #fff;
}

.obs-cell {
    font-size: .72rem;
    color: #495057;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-width: 200px;
}
[data-layout-mode="dark"] .obs-cell { color: #ced4da; }

/* Empty */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 16px;
    border: 1px dashed #d6dbe0;
}
.empty-state .empty-icon {
    width: 84px; height: 84px;
    margin: 0 auto 1.1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #0ab39c;
    box-shadow: 0 10px 24px -12px rgba(10,179,156,.35);
    animation: emptyFloat 3s ease-in-out infinite;
}
@keyframes emptyFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
@media (prefers-reduced-motion: reduce) { .empty-state .empty-icon { animation: none; } }
[data-layout-mode="dark"] .empty-state { background: #1a1d20; border-color: #2a2f34; }
[data-layout-mode="dark"] .empty-state .empty-icon { background: #212529; }

/* ===== Live Search ===== */
.seguimiento-search {
    border: 1px solid #e9ebec;
    border-radius: 10px;
    padding: .5rem 1rem .5rem 2.5rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23878a99' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") .75rem 50% / 16px no-repeat;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}
.seguimiento-search:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
    outline: 0;
}
[data-layout-mode="dark"] .seguimiento-search { background-color: #1d2125; border-color: #2a2f34; color: #fff; }
[data-layout-mode="dark"] .seguimiento-search:focus { border-color: #5e72b8; box-shadow: 0 0 0 .15rem rgba(94,114,184,.2); }

.seguimiento-search-wrapper { position: relative; }
.seguimiento-clear-search {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    border: 0; background: none; color: #878a99; font-size: 1rem;
    cursor: pointer; padding: 2px 6px; border-radius: 4px; display: none;
    line-height: 1;
}
.seguimiento-clear-search:hover { background: #f1f3f5; color: #495057; }
[data-layout-mode="dark"] .seguimiento-clear-search:hover { background: #2a2f34; }

.programa-block.is-hidden { display: none !important; }

/* ===== Progress Bar ===== */
.modulo-progress { padding: .35rem 1.05rem .65rem; }
.modulo-progress .progress { height: 6px; border-radius: 999px; background: #eef0f4; overflow: hidden; }
.modulo-progress .progress-bar { border-radius: 999px; transition: width .8s cubic-bezier(.22,.61,.36,1); }
.modulo-progress .progress-label {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .68rem; color: #878a99; margin-bottom: 4px;
}
.modulo-progress .progress-label strong { color: #495057; }
[data-layout-mode="dark"] .modulo-progress .progress { background: #2a2f34; }
[data-layout-mode="dark"] .modulo-progress .progress-label strong { color: #ced4da; }

/* ===== Collapse ===== */
.modulo-collapse-toggle {
    cursor: pointer; user-select: none;
    transition: background .15s ease;
}
.modulo-collapse-toggle:hover { background: rgba(64,81,137,.03); }
.modulo-collapse-toggle .collapse-arrow {
    transition: transform .25s ease; font-size: .75rem; color: #878a99;
    display: inline-flex; align-items: center;
}
.modulo-collapse-toggle[aria-expanded="true"] .collapse-arrow { transform: rotate(180deg); }
[data-layout-mode="dark"] .modulo-collapse-toggle:hover { background: rgba(255,255,255,.03); }

/* ===== Weekday badge ===== */
.weekday-badge {
    font-size: .6rem; text-transform: uppercase; letter-spacing: .3px;
    font-weight: 600; color: #878a99;
}
.weekday-hoy { color: #f06548; }
[data-layout-mode="dark"] .weekday-hoy { color: #f8796b; }

/* ===== Today pulse ===== */
.today-pulse {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .6rem; font-weight: 700; text-transform: uppercase;
    color: #f06548; letter-spacing: .3px;
}
.today-pulse::before {
    content: ""; width: 6px; height: 6px; border-radius: 50%;
    background: #f06548;
    animation: todayPulse 1.5s ease-in-out infinite;
}
@keyframes todayPulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: .4; transform: scale(1.4); } }

/* ===== Animated counter ===== */
.stat-value .counter-suffix { font-size: .75rem; font-weight: 400; color: #878a99; }

/* ===== Module filter highlight ===== */
.modulo-highlight { background-color: #fff9db !important; outline: 2px solid #f7b84b; outline-offset: -2px; border-radius: 10px; }
[data-layout-mode="dark"] .modulo-highlight { background-color: rgba(247,184,75,.1) !important; }

.banner-culminado {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d8 100%);
    color: #0ab39c;
    border-radius: 8px;
    padding: .5rem .85rem;
    margin: .5rem .75rem 0;
    font-size: .78rem;
    font-weight: 600;
    display: flex; align-items: center; gap: .5rem;
    border-left: 4px solid #0ab39c;
}
.banner-vencido {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #f06548;
    border-radius: 8px;
    padding: .5rem .85rem;
    margin: .5rem .75rem 0;
    font-size: .78rem;
    font-weight: 600;
    display: flex; align-items: center; gap: .5rem;
    border-left: 4px solid #f06548;
}

/* ===== Seguimiento → estados visuales por fila ===== */
.horarios-table tbody tr.seg-row { cursor: pointer; }
.horarios-table tbody tr.seg-row.row-atrasado {
    background-color: #fff2f1 !important;
    border-left: 3px solid #d63031 !important;
    animation: segPulseLate 2.6s ease-in-out infinite;
}
.horarios-table tbody tr.seg-row.row-atrasado:hover { background-color: #ffe5e2 !important; }
@keyframes segPulseLate {
    0%, 100% { box-shadow: inset 3px 0 0 #d63031; }
    50%      { box-shadow: inset 6px 0 0 #d63031; }
}
.horarios-table tbody tr.seg-row.row-confirm-late {
    background-color: #fff8ec !important;
    border-left: 3px solid #d68910 !important;
}
.horarios-table tbody tr.seg-row.row-confirm-late:hover { background-color: #ffeecc !important; }
.horarios-table tbody tr.seg-row.row-confirm-future {
    background-color: #f0fbf7 !important;
    border-left: 3px solid #0ab39c !important;
}
.horarios-table tbody tr.seg-row.row-confirm-future:hover { background-color: #e0f5ed !important; }
.horarios-table tbody tr.seg-row.row-missing {
    background-color: #eef6ff !important;
    border-left: 3px solid #0b5ed7 !important;
}
.horarios-table tbody tr.seg-row.row-missing:hover { background-color: #dcebff !important; }

.seg-flag {
    display:inline-flex; align-items:center; gap:.25rem;
    padding:.12rem .5rem; border-radius:6px;
    font-size:.62rem; font-weight:700; letter-spacing:.3px; text-transform:uppercase;
    margin-top:3px;
}
.seg-flag.f-danger { background:#d63031; color:#fff; }
.seg-flag.f-warn   { background:#d68910; color:#fff; }
.seg-flag.f-info   { background:#0b5ed7; color:#fff; }
.seg-flag.f-success{ background:#0ab39c; color:#fff; }

/* ===== Enlace programa / módulo ===== */
.programa-title-link,
.modulo-nombre-link {
    color: inherit;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: color .15s, border-color .15s;
}
.programa-title-link:hover,
.modulo-nombre-link:hover {
    color: #405189;
    border-bottom-color: #405189;
}

/* ===== Modal de detalle de horario ===== */
#segModal .modal-content { border:0; border-radius:14px; overflow:hidden; }
#segModal .modal-header {
    background: linear-gradient(135deg,#405189 0%,#5e72b8 100%);
    color:#fff; border:0; padding: 1rem 1.3rem;
}
#segModal.is-late .modal-header { background: linear-gradient(135deg,#d63031 0%,#f06548 100%); }
#segModal.is-warn .modal-header { background: linear-gradient(135deg,#d68910 0%,#f7b84b 100%); }
#segModal.is-info .modal-header { background: linear-gradient(135deg,#0b5ed7 0%,#3d8bf5 100%); }
#segModal.is-ok   .modal-header { background: linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%); }
#segModal .modal-header .btn-close { filter: brightness(0) invert(1); opacity:.9; }
#segModal .modal-title { font-size:1rem; font-weight:700; margin-bottom:.15rem; }
#segModal .modal-subtitle { font-size:.78rem; opacity:.85; }
#segModal .modal-body { padding:1.1rem 1.3rem; background:#fafbfd; }

.seg-modal-alert {
    display:flex; align-items:flex-start; gap:.55rem;
    padding:.7rem .9rem; border-radius:10px;
    font-size:.82rem; margin-bottom:1rem;
}
.seg-modal-alert.is-danger { background:#fff2f1; color:#b1240f; border:1px solid #f3aaa1; }
.seg-modal-alert.is-warn   { background:#fff8ec; color:#8a6100; border:1px solid #f0cf86; }
.seg-modal-alert.is-info   { background:#eef6ff; color:#1c3e9e; border:1px solid #b8d4f5; }
.seg-modal-alert i { font-size:1.1rem; margin-top:.05rem; }

.seg-modal-info-grid {
    display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:.85rem 1rem;
    background:#fff; border:1px solid #eef0f5; border-radius:12px; padding:1rem 1.1rem; margin-bottom:1rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.seg-info-item { display:flex; align-items:flex-start; gap:.6rem; min-width:0; }
.seg-info-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:#eef1f8; color:#405189; font-size:1rem;
}
.seg-info-label { font-size:.62rem; font-weight:700; color:#878a99; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.1rem; }
.seg-info-value { font-size:.82rem; color:#2a2f43; font-weight:600; word-break:break-word; line-height:1.35; }
.seg-info-value .text-muted { font-weight:400; }
.seg-info-value a { color:#0b5ed7; text-decoration:none; }
.seg-info-value a:hover { text-decoration:underline; }
.seg-info-value .asignado-pill { font-size:.7rem; }

.seg-detail-title {
    font-size:.7rem; font-weight:700; color:#878a99;
    text-transform:uppercase; letter-spacing:.4px; margin-bottom:.45rem;
    display:flex; align-items:center; gap:.35rem;
}
.seg-obs-box {
    background:#fff; border:1px solid #eef0f5; border-radius:10px;
    padding:.6rem .8rem; font-size:.82rem; color:#2a2f43;
    white-space:pre-wrap; word-break:break-word;
}
.seg-obs-empty { color:#aab0c0; font-style:italic; background:#fff; border:1px dashed #dfe3ec; }

/* ===== Resumen de asistencia (tarjetas) ===== */
.seg-asist-stats {
    display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.6rem; margin-bottom:.75rem;
}
.seg-stat {
    background:#fff; border:1px solid #eef0f5; border-radius:10px;
    padding:.55rem .7rem; display:flex; align-items:center; gap:.6rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.seg-stat-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
.seg-stat .num { font-size:1.05rem; font-weight:800; line-height:1.1; color:#2a2f43; }
.seg-stat .lbl { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#878a99; }
.seg-stat .pct { font-size:.66rem; font-weight:600; color:#aab0c0; margin-left:auto; }

/* ===== Lista de asistencia ===== */
.seg-asist-wrap {
    background:#fff; border:1px solid #eef0f5; border-radius:12px; overflow:hidden;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.seg-asist-toolbar {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
    padding:.6rem .8rem; border-bottom:1px solid #eef0f5;
}
.seg-asist-search { position:relative; flex:1 1 200px; max-width:280px; }
.seg-asist-search i {
    position:absolute; left:.65rem; top:50%; transform:translateY(-50%);
    color:#aab0c0; font-size:.9rem; pointer-events:none;
}
.seg-asist-search input {
    width:100%; border:1px solid #e3e6ef; border-radius:8px;
    padding:.38rem .65rem .38rem 1.9rem; font-size:.78rem; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.seg-asist-search input:focus { border-color:#8fa2d8; box-shadow:0 0 0 3px rgba(64,81,137,.08); }
.seg-asist-countlbl { font-size:.7rem; color:#98a0b5; font-weight:600; white-space:nowrap; }
.seg-asist-scroll { max-height:340px; overflow-y:auto; }
.seg-asist-scroll::-webkit-scrollbar { width:8px; }
.seg-asist-scroll::-webkit-scrollbar-thumb { background:#dfe3ec; border-radius:4px; }

.seg-asist-table { width:100%; font-size:.78rem; margin:0; border-collapse:separate; border-spacing:0; }
.seg-asist-table thead th {
    background:#f6f8fc; color:#5d6478; font-weight:700;
    font-size:.64rem; text-transform:uppercase; letter-spacing:.3px;
    padding:.5rem .7rem; position:sticky; top:0; z-index:1;
    border-bottom:1px solid #e8ebf3;
}
.seg-asist-table tbody td { padding:.45rem .7rem; border-top:1px solid #f2f4f9; vertical-align:middle; }
.seg-asist-table tbody tr:first-child td { border-top:0; }
.seg-asist-table tbody tr:hover { background:#f8fafd; }

.seg-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.64rem; font-weight:700; color:#fff; letter-spacing:.3px;
}
.seg-part-cell { display:flex; align-items:center; gap:.6rem; min-width:0; }
.seg-part-name { font-weight:600; font-size:.79rem; color:#2a2f43; line-height:1.25; }
.seg-part-ci { font-size:.68rem; color:#98a0b5; }

.seg-a-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.14rem .6rem; border-radius:999px; font-size:.68rem; font-weight:700;
    white-space:nowrap;
}
.seg-a-Presente    { background:#d4f6ee; color:#0a7059; }
.seg-a-Atraso      { background:#fff4d6; color:#8a6100; }
.seg-a-Falta       { background:#ffd9d3; color:#b1240f; }
.seg-a-Justificado { background:#dde7ff; color:#1c3e9e; }
.seg-asist-obs { font-size:.72rem; color:#98a0b5; }

@media (max-width: 992px) {
    .seg-modal-info-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (max-width: 640px) {
    .seg-modal-info-grid { grid-template-columns: 1fr; }
    .seg-asist-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $mesesAbr = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    $diasSemana = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    $diasSemanaAbr = ['dom','lun','mar','mié','jue','vie','sáb'];
    $fechaCorta = function ($val) use ($mesesAbr) {
        if (!$val) return ['—', ''];
        $t = strtotime((string) $val);
        if (!$t) return [$val, ''];
        return [(int) date('d', $t), ($mesesAbr[(int)date('n', $t) - 1] ?? '') . ' ' . date('Y', $t)];
    };
    $weekday = function ($val) use ($diasSemanaAbr) {
        if (!$val) return '';
        $t = strtotime((string) $val);
        return $t ? $diasSemanaAbr[(int) date('w', $t)] : '';
    };
    $weekdayHoy = function ($val) use ($hoy) {
        return $val === $hoy;
    };
    $estadoColor = function ($e) {
        return [
            'Confirmado' => '#0ab39c',
            'Programado' => '#299cdb',
            'Desarrollado' => '#405189',
            'Postergado' => '#f7b84b',
        ][$e] ?? '#878a99';
    };

    // ===== Mapa de detalle de horarios para el modal compartido =====
    $segHorariosDetail = [];
    $segCollect = function ($listas) use (&$segHorariosDetail) {
        foreach ($listas as $programas) {
            foreach ($programas as $prog) {
                foreach ($prog['modulos'] as $mod) {
                    foreach ($mod['horarios'] as $h) {
                        $hid = $h['id'] ?? null;
                        if (!$hid || isset($segHorariosDetail[$hid])) continue;
                        $segHorariosDetail[$hid] = [
                            'programa_id'        => $prog['programa_id'],
                            'programa_nombre'    => $prog['programa_nombre'],
                            'programa_codigo'    => $prog['programa_codigo'],
                            'programa_gestion'   => $prog['programa_gestion'],
                            'tipo_nombre'        => $prog['tipo_nombre'] ?? null,
                            'programa_url'       => route('admin.programas.show', $prog['programa_id']),
                            'modulo_nombre'      => $mod['nombre'],
                            'modulo_estado'      => $mod['estado'] ?? null,
                            'docente_nombres'    => $mod['docente_nombres'] ?? null,
                            'docente_apellidos'  => $mod['docente_apellidos'] ?? null,
                            'docente_ci'         => $mod['docente_ci'] ?? null,
                            'fecha_desarrollo'   => $h['fecha_desarrollo'],
                            'hora_inicio'        => $h['hora_inicio'],
                            'hora_fin'           => $h['hora_fin'],
                            'estado'             => $h['estado'],
                            'observaciones'      => $h['observaciones'] ?? '',
                            'asignado_nombres'   => $h['asignado_nombres'] ?? null,
                            'asignado_apellidos' => $h['asignado_apellidos'] ?? null,
                            'asignado_color'     => $h['asignado_color'] ?? null,
                            'atrasado'           => !empty($h['atrasado']),
                            'confirmado_pasado'  => !empty($h['confirmado_pasado']),
                            'confirmado_futuro'  => !empty($h['confirmado_futuro']),
                            'falta_registro'     => !empty($h['falta_registro']),
                            'tiene_observaciones' => !empty($h['tiene_observaciones']),
                            'tiene_asistencias'  => !empty($h['tiene_asistencias']),
                            'asistencias'        => $h['asistencias'] ?? [],
                            'asistencia_pdf_url' => route('admin.programas.asistencia-pdf', $hid),
                        ];
                    }
                }
            }
        }
    };
    $segCollect([$enDesarrollo, $noInicioVencidos, $concluidosActual, $concluidosAnterior]);
@endphp

<div class="row">
    <div class="col-12">
        <div class="seg-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-radar-line"></i></div>
                <div>
                    <h4>Seguimiento Académico</h4>
                    <p class="hero-sub">Módulos en desarrollo y módulos sin iniciar con sesiones vencidas</p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-start align-items-md-end gap-2">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                    <li class="breadcrumb-item active">Seguimiento</li>
                </ol>
                <span class="hero-date"><i class="ri-calendar-event-line"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #299cdb;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Programas En Desarrollo</p>
                        <h4 class="stat-value mb-0" data-target="{{ $resumen['en_desarrollo_progs'] }}">{{ $resumen['en_desarrollo_progs'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-info text-info"><i class="ri-booklet-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #0ab39c;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Módulos En Desarrollo</p>
                        <h4 class="stat-value mb-0" data-target="{{ $resumen['en_desarrollo_mods'] }}">{{ $resumen['en_desarrollo_mods'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-loader-4-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #f06548;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Programas c/ Vencidos</p>
                        <h4 class="stat-value mb-0" data-target="{{ $resumen['no_inicio_progs'] }}">{{ $resumen['no_inicio_progs'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-danger text-danger"><i class="ri-alert-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #f7b84b;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Módulos Vencidos</p>
                        <h4 class="stat-value mb-0" data-target="{{ $resumen['no_inicio_mods'] }}">{{ $resumen['no_inicio_mods'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-error-warning-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #0ab39c;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Concluidos · {{ $mesActualLabel }}</p>
                        <h4 class="stat-value mb-0"><span data-target="{{ $resumen['concluidos_actual_mods'] }}">{{ $resumen['concluidos_actual_mods'] }}</span> <small class="text-muted fw-normal counter-suffix">/ {{ $resumen['concluidos_actual_progs'] }} prog.</small></h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card" style="--stat-accent: #405189;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="min-width-0">
                        <p class="stat-label">Concluidos · {{ $mesAnteriorLabel }}</p>
                        <h4 class="stat-value mb-0"><span data-target="{{ $resumen['concluidos_anterior_mods'] }}">{{ $resumen['concluidos_anterior_mods'] }}</span> <small class="text-muted fw-normal counter-suffix">/ {{ $resumen['concluidos_anterior_progs'] }} prog.</small></h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-award-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtro + Búsqueda --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <form method="GET" action="{{ route('admin.seguimiento.index') }}" class="row g-2 align-items-end">
                            <div class="col-6">
                                <label class="form-label">Gestión</label>
                                <select name="gestion" class="form-select">
                                    <option value="">Todas las gestiones</option>
                                    @foreach($gestiones as $g)
                                        <option value="{{ $g }}" @if((string)$gestionFiltro === (string)$g) selected @endif>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 d-flex gap-2">
                                <a href="{{ route('admin.seguimiento.index') }}" class="btn btn-soft-danger w-50 d-flex align-items-center justify-content-center gap-1" title="Limpiar filtros">
                                    <i class="ri-filter-off-line"></i><span class="d-none d-xl-inline"> Limpiar</span>
                                </a>
                                <button type="submit" class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-1">
                                    <i class="ri-filter-3-line"></i><span class="d-none d-xl-inline"> Aplicar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-7">
                        <label class="form-label">Búsqueda rápida</label>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="seguimiento-search-wrapper flex-grow-1">
                                <input type="text" class="seguimiento-search" id="seguimientoSearch" placeholder="Buscar programa, módulo o docente..." autocomplete="off">
                                <button class="seguimiento-clear-search" id="seguimientoClearSearch" type="button">&times;</button>
                            </div>
                            <small class="text-muted" id="seguimientoResultCount"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="card show-tabs-card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-pill" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-en-desarrollo" type="button" role="tab">
                    <i class="ri-loader-4-line"></i> En Desarrollo
                    <span class="badge">{{ $resumen['en_desarrollo_mods'] }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-no-inicio" type="button" role="tab">
                    <i class="ri-error-warning-line"></i> Sin Iniciar (vencidos)
                    <span class="badge">{{ $resumen['no_inicio_mods'] }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-concluidos" type="button" role="tab">
                    <i class="ri-checkbox-circle-line"></i> Concluidos
                    <span class="badge">{{ $resumen['concluidos_actual_mods'] + $resumen['concluidos_anterior_mods'] }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ============== TAB EN DESARROLLO ============== --}}
            <div class="tab-pane show active" id="tab-en-desarrollo" role="tabpanel">
                @if(count($enDesarrollo) === 0)
                    <div class="empty-state">
                        <div class="empty-icon"><i class="ri-loader-4-line"></i></div>
                        <h5 class="text-dark fw-bold mb-1">Sin módulos en desarrollo</h5>
                        <p class="text-muted mb-0">No hay programas con módulos en estado "En Desarrollo" para los filtros aplicados.</p>
                    </div>
                @else
                    @foreach($enDesarrollo as $prog)
                    <div class="programa-block">
                        <div class="programa-header">
                            <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                <span class="programa-icon"><i class="ri-graduation-cap-line"></i></span>
                                <div style="min-width:0;">
                                    <h6 class="programa-title text-truncate" title="{{ $prog['programa_nombre'] }}">
                                        <a href="{{ route('admin.programas.show', $prog['programa_id']) }}" class="programa-title-link" onclick="event.stopPropagation();">{{ $prog['programa_nombre'] }}</a>
                                    </h6>
                                    <div class="programa-meta">
                                        <span><i class="ri-hashtag"></i>{{ $prog['programa_codigo'] }}</span>
                                        @if($prog['tipo_nombre'])<span>·</span><span><i class="ri-price-tag-3-line"></i>{{ $prog['tipo_nombre'] }}</span>@endif
                                        <span>·</span><span><i class="ri-calendar-line"></i>Gestión {{ $prog['programa_gestion'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="programa-count"><i class="ri-stack-line"></i>{{ count($prog['modulos']) }} módulo{{ count($prog['modulos']) > 1 ? 's' : '' }}</span>
                        </div>

                        @foreach($prog['modulos'] as $idx => $mod)
                        @php
                            $iniDoc = $mod['docente_nombres'] ? strtoupper(substr($mod['docente_nombres'], 0, 1) . substr($mod['docente_apellidos'], 0, 1)) : '?';
                            $sesionesCompletadas = count(array_filter($mod['horarios'], fn($h) => $h['pasado']));
                            $pctCompletado = $mod['total_horarios'] > 0 ? round(($sesionesCompletadas / $mod['total_horarios']) * 100) : 0;
                            $pctColor = $pctCompletado === 100 ? '#0ab39c' : ($pctCompletado > 50 ? '#299cdb' : ($pctCompletado > 0 ? '#f7b84b' : '#f06548'));
                            $primerPrograma = $loop->parent->first;
                            $modHeaderClass = '';
                            $pillText = '';
                            $pillClass = '';
                            if (!empty($mod['en_curso'])) {
                                $modHeaderClass = 'modulo-card-header--curso';
                                $pillText = 'En curso';
                                $pillClass = 'modulo-highlight-chip--active';
                            } elseif (!empty($mod['reciente'])) {
                                $modHeaderClass = 'modulo-card-header--reciente';
                                $pillText = 'Recientemente en curso';
                                $pillClass = 'modulo-highlight-chip--active';
                            } elseif (!empty($mod['proximo'])) {
                                $modHeaderClass = 'modulo-card-header--proximo';
                                $pillText = 'Próximo';
                                $pillClass = 'modulo-highlight-chip--proximo';
                            }
                        @endphp
                        <div class="modulo-card">
                            <div class="modulo-card-header modulo-collapse-toggle {{ $modHeaderClass }}" data-collapse-target="#mod-collapse-{{ $mod['id'] }}" aria-expanded="{{ $primerPrograma ? 'true' : 'false' }}" role="button">
                                <div class="modulo-title-block">
                                    <span class="modulo-num-badge">{{ $idx + 1 }}</span>
                                    <div style="min-width:0;">
                                        <div class="modulo-nombre">
                                            <a href="{{ route('admin.programas.modulo-detalle', $mod['id']) }}" class="modulo-nombre-link">{{ $mod['nombre'] }}</a>
                                            @if(!empty($mod['moodle_link']))
                                                <a href="{{ $mod['moodle_link'] }}" target="_blank" rel="noopener" class="modulo-moodle-link" title="Abrir en Moodle"><i class="ri-external-link-line"></i></a>
                                            @endif
                                        </div>
                                        <div class="modulo-docente">
                                            @if($mod['docente_nombres'])
                                                <span class="docente-avatar-mini">{{ $iniDoc }}</span>
                                                <span><i class="ri-user-star-line"></i> {{ $mod['docente_apellidos'] }}, {{ $mod['docente_nombres'] }}</span>
                                            @else
                                                <span class="fst-italic"><i class="ri-user-unfollow-line"></i> Sin docente asignado</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="modulo-chips">
                                        <span class="chip chip-en-desarrollo">En Desarrollo</span>
                                        @if($mod['sesiones_culminadas'])
                                            <span class="chip chip-culminado"><i class="ri-checkbox-circle-line"></i> Sesiones culminadas</span>
                                        @endif
                                        @if($pillText)
                                            <span class="modulo-highlight-chip {{ $pillClass }}">{{ $pillText }}</span>
                                        @endif
                                        <span class="chip chip-no-inicio">{{ $mod['total_horarios'] }} sesiones</span>
                                    </div>
                                    <span class="collapse-arrow"><i class="ri-arrow-down-s-line"></i></span>
                                </div>
                            </div>

                            <div class="modulo-progress">
                                <div class="progress-label">
                                    <span><strong>{{ $sesionesCompletadas }}/{{ $mod['total_horarios'] }}</strong> sesiones completadas</span>
                                    <span>{{ $pctCompletado }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pctCompletado }}%; background: {{ $pctColor }};" aria-valuenow="{{ $pctCompletado }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="collapse {{ $primerPrograma ? 'show' : '' }}" id="mod-collapse-{{ $mod['id'] }}">
                            @if($mod['sesiones_culminadas'])
                                <div class="banner-culminado">
                                    <i class="ri-checkbox-circle-line"></i>
                                    Todas las sesiones del módulo ya fueron desarrolladas (fecha de la última sesión: {{ $fechaCorta(end($mod['horarios'])['fecha_desarrollo'])[0] }} de {{ $fechaCorta(end($mod['horarios'])['fecha_desarrollo'])[1] }}). El módulo aún figura "En Desarrollo".
                                </div>
                            @endif

                            @if(count($mod['horarios']) === 0)
                                <div class="px-3 py-2 text-muted fst-italic" style="font-size:.78rem;"><i class="ri-inbox-2-line"></i> Sin horarios registrados</div>
                            @else
                            <div class="table-responsive">
                                <table class="horarios-table table">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;">Fecha</th>
                                            <th style="width: 95px;" class="text-center">Horario</th>
                                            <th style="width: 110px;">Estado</th>
                                            <th>Asignado</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mod['horarios'] as $h)
                                            @include('admin.seguimiento.partials.horario_row')
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            </div>{{-- collapse --}}
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- ============== TAB NO INICIO VENCIDOS ============== --}}
            <div class="tab-pane" id="tab-no-inicio" role="tabpanel">
                @if(count($noInicioVencidos) === 0)
                    <div class="empty-state">
                        <div class="empty-icon"><i class="ri-checkbox-circle-line"></i></div>
                        <h5 class="text-dark fw-bold mb-1">Sin módulos vencidos</h5>
                        <p class="text-muted mb-0">No hay módulos "Sin Iniciar" cuyos horarios hayan pasado de la fecha actual.</p>
                    </div>
                @else
                    @foreach($noInicioVencidos as $prog)
                    <div class="programa-block">
                        <div class="programa-header programa-header--danger">
                            <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                <span class="programa-icon" style="background: linear-gradient(135deg, #f06548 0%, #ff8a73 100%); box-shadow: 0 4px 10px -4px rgba(240,101,72,.55);">
                                    <i class="ri-alert-line"></i>
                                </span>
                                <div style="min-width:0;">
                                    <h6 class="programa-title text-truncate" title="{{ $prog['programa_nombre'] }}">
                                        <a href="{{ route('admin.programas.show', $prog['programa_id']) }}" class="programa-title-link" onclick="event.stopPropagation();">{{ $prog['programa_nombre'] }}</a>
                                    </h6>
                                    <div class="programa-meta">
                                        <span><i class="ri-hashtag"></i>{{ $prog['programa_codigo'] }}</span>
                                        @if($prog['tipo_nombre'])<span>·</span><span><i class="ri-price-tag-3-line"></i>{{ $prog['tipo_nombre'] }}</span>@endif
                                        <span>·</span><span><i class="ri-calendar-line"></i>Gestión {{ $prog['programa_gestion'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="programa-count" style="background: linear-gradient(135deg, #f06548 0%, #ff8a73 100%); box-shadow: 0 4px 10px -4px rgba(240,101,72,.5);">
                                <i class="ri-error-warning-line"></i>{{ count($prog['modulos']) }} módulo{{ count($prog['modulos']) > 1 ? 's' : '' }} vencido{{ count($prog['modulos']) > 1 ? 's' : '' }}
                            </span>
                        </div>

                        @foreach($prog['modulos'] as $idx => $mod)
                        @php
                            $iniDoc = $mod['docente_nombres'] ? strtoupper(substr($mod['docente_nombres'], 0, 1) . substr($mod['docente_apellidos'], 0, 1)) : '?';
                            $primerPrograma = $loop->parent->first;
                            $modHeaderClass = '';
                            $pillText = '';
                            $pillClass = '';
                            if (!empty($mod['en_curso'])) {
                                $modHeaderClass = 'modulo-card-header--curso';
                                $pillText = 'En curso';
                                $pillClass = 'modulo-highlight-chip--active';
                            } elseif (!empty($mod['reciente'])) {
                                $modHeaderClass = 'modulo-card-header--reciente';
                                $pillText = 'Recientemente en curso';
                                $pillClass = 'modulo-highlight-chip--active';
                            } elseif (!empty($mod['proximo'])) {
                                $modHeaderClass = 'modulo-card-header--proximo';
                                $pillText = 'Próximo';
                                $pillClass = 'modulo-highlight-chip--proximo';
                            }
                        @endphp
                        <div class="modulo-card">
                            <div class="modulo-card-header modulo-collapse-toggle {{ $modHeaderClass }}" data-collapse-target="#mod-collapse-ni-{{ $mod['id'] }}" aria-expanded="{{ $primerPrograma ? 'true' : 'false' }}" role="button">
                                <div class="modulo-title-block">
                                    <span class="modulo-num-badge" style="background: linear-gradient(135deg, #f06548 0%, #ff8a73 100%);">{{ $idx + 1 }}</span>
                                    <div style="min-width:0;">
                                        <div class="modulo-nombre">
                                            <a href="{{ route('admin.programas.modulo-detalle', $mod['id']) }}" class="modulo-nombre-link">{{ $mod['nombre'] }}</a>
                                            @if(!empty($mod['moodle_link']))
                                                <a href="{{ $mod['moodle_link'] }}" target="_blank" rel="noopener" class="modulo-moodle-link" title="Abrir en Moodle"><i class="ri-external-link-line"></i></a>
                                            @endif
                                        </div>
                                        <div class="modulo-docente">
                                            @if($mod['docente_nombres'])
                                                <span class="docente-avatar-mini">{{ $iniDoc }}</span>
                                                <span><i class="ri-user-star-line"></i> {{ $mod['docente_apellidos'] }}, {{ $mod['docente_nombres'] }}</span>
                                            @else
                                                <span class="fst-italic"><i class="ri-user-unfollow-line"></i> Sin docente asignado</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="modulo-chips">
                                        <span class="chip chip-no-inicio">No Inicio</span>
                                        <span class="chip chip-vencido"><i class="ri-time-line"></i> Sesiones vencidas</span>
                                        @if($pillText)
                                            <span class="modulo-highlight-chip {{ $pillClass }}">{{ $pillText }}</span>
                                        @endif
                                    </div>
                                    <span class="collapse-arrow"><i class="ri-arrow-down-s-line"></i></span>
                                </div>
                            </div>

                            <div class="modulo-progress">
                                <div class="progress-label">
                                    <span><strong>0/{{ $mod['total_horarios'] }}</strong> sesiones completadas</span>
                                    <span>0%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%; background: #f06548;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="banner-vencido">
                                <i class="ri-alert-line"></i>
                                Módulo sin iniciar pero con sesiones cuya fecha ya pasó. Requiere atención.
                            </div>

                            <div class="collapse {{ $primerPrograma ? 'show' : '' }}" id="mod-collapse-ni-{{ $mod['id'] }}">
                            <div class="table-responsive">
                                <table class="horarios-table table">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;">Fecha</th>
                                            <th style="width: 95px;" class="text-center">Horario</th>
                                            <th style="width: 110px;">Estado</th>
                                            <th>Asignado</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mod['horarios'] as $h)
                                            @include('admin.seguimiento.partials.horario_row')
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </div>{{-- collapse --}}
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- ============== TAB CONCLUIDOS (con sub-tabs por mes) ============== --}}
            <div class="tab-pane" id="tab-concluidos" role="tabpanel">
                {{-- Sub-tabs --}}
                <ul class="nav nav-tabs-pill mb-3" role="tablist" style="background: #eef1f8;">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sub-concluidos-actual" type="button" role="tab">
                            <i class="ri-calendar-check-line"></i> {{ $mesActualLabel }}
                            <span class="badge">{{ $resumen['concluidos_actual_mods'] }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sub-concluidos-anterior" type="button" role="tab">
                            <i class="ri-calendar-2-line"></i> {{ $mesAnteriorLabel }}
                            <span class="badge">{{ $resumen['concluidos_anterior_mods'] }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane show active" id="sub-concluidos-actual" role="tabpanel">
                        @include('admin.seguimiento.partials.concluidos', [
                            'programas' => $concluidosActual,
                            'labelMes' => $mesActualLabel,
                            'desdeFmt' => $fechaCorta($mesActualInicio),
                            'hastaFmt' => $fechaCorta($mesActualFin),
                        ])
                    </div>

                    <div class="tab-pane" id="sub-concluidos-anterior" role="tabpanel">
                        @include('admin.seguimiento.partials.concluidos', [
                            'programas' => $concluidosAnterior,
                            'labelMes' => $mesAnteriorLabel,
                            'desdeFmt' => $fechaCorta($mesAnteriorInicio),
                            'hastaFmt' => $fechaCorta($mesAnteriorFin),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ MODAL DETALLE HORARIO ============ --}}
<div class="modal fade" id="segModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title" id="segModalTitle">—</div>
                    <div class="modal-subtitle" id="segModalSubtitle">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="segModalBody">
                {{-- Contenido inyectado --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
                <a href="#" id="segModalGoto" class="btn btn-primary" target="_blank" rel="noopener" style="display:none;">
                    <i class="ri-external-link-line align-bottom"></i> Abrir programa
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Datos para el modal --}}
<script>
    window.__segHorarios = @json($segHorariosDetail);
</script>
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== Bootstrap Tooltips ==========
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // ========== Live Search ==========
    var searchInput = document.getElementById('seguimientoSearch');
    var clearBtn = document.getElementById('seguimientoClearSearch');
    var resultCount = document.getElementById('seguimientoResultCount');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var visible = 0;
            var blocks = document.querySelectorAll('.programa-block');
            blocks.forEach(function (block) {
                var text = block.textContent.toLowerCase();
                var match = !q || text.indexOf(q) > -1;
                block.classList.toggle('is-hidden', !match);
                if (match) visible++;
            });
            clearBtn.style.display = q ? 'block' : 'none';
            if (q) {
                resultCount.textContent = visible + ' bloque' + (visible !== 1 ? 's' : '') + ' mostrado' + (visible !== 1 ? 's' : '');
                // Highlight matching module cards
                document.querySelectorAll('.modulo-card').forEach(function (card) {
                    var txt = card.textContent.toLowerCase();
                    card.classList.toggle('modulo-highlight', q && txt.indexOf(q) > -1);
                });
            } else {
                resultCount.textContent = '';
                document.querySelectorAll('.modulo-card').forEach(function (c) { c.classList.remove('modulo-highlight'); });
            }
        });
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }

    // ========== Collapse de módulo (manual, para que los links naveguen) ==========
    document.querySelectorAll('.modulo-card-header[data-collapse-target]').forEach(function (header) {
        var targetId = header.getAttribute('data-collapse-target');
        if (!targetId) return;
        var target = document.querySelector(targetId);
        if (!target) return;

        target.addEventListener('shown.bs.collapse', function () {
            header.setAttribute('aria-expanded', 'true');
        });
        target.addEventListener('hidden.bs.collapse', function () {
            header.setAttribute('aria-expanded', 'false');
        });

        header.addEventListener('click', function (ev) {
            if (ev.target.closest('a')) return;
            bootstrap.Collapse.getOrCreateInstance(target, { toggle: false }).toggle();
        });
    });

    // ========== Animated Counters ==========
    function animateCounter(el, target) {
        var current = 0;
        var step = Math.max(1, Math.ceil(target / 30));
        var timer = setInterval(function () {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current;
        }, 30);
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                var val = parseInt(el.getAttribute('data-target'), 10);
                if (!isNaN(val) && val > 0) {
                    animateCounter(el, val);
                } else {
                    el.textContent = val || 0;
                }
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-value [data-target], .stat-value[data-target]').forEach(function (el) {
        el.textContent = '0';
        observer.observe(el);
    });

    // ========== Modal de detalle de horario ==========
    (function () {
        var modalEl = document.getElementById('segModal');
        if (!modalEl) return;
        var modal = new bootstrap.Modal(modalEl);
        var titleEl = document.getElementById('segModalTitle');
        var subtitleEl = document.getElementById('segModalSubtitle');
        var bodyEl = document.getElementById('segModalBody');
        var gotoEl = document.getElementById('segModalGoto');
        var KINDS = ['is-late','is-warn','is-info','is-ok'];

        var mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        var diasEs  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];

        function esc(s) {
            return (s == null ? '' : String(s))
                .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }
        function fmtFecha(ymd) {
            if (!ymd) return '—';
            var p = ymd.split('-');
            if (p.length < 3) return ymd;
            var d = new Date(parseInt(p[0],10), parseInt(p[1],10)-1, parseInt(p[2],10));
            return diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()] + ' del ' + d.getFullYear();
        }
        function fmtHora(h) { return h ? (h+'').slice(0,5) : '—'; }
        function fullName(nombres, apellidos) {
            var n = ((nombres||'') + ' ' + (apellidos||'')).trim();
            return n || '';
        }

        var estadoStyles = {
            'Confirmado':    { bg:'#d4f6ee', color:'#0a7059' },
            'Programado':    { bg:'#dde7ff', color:'#1c3e9e' },
            'Desarrollado':  { bg:'#e3e9f7', color:'#283f7a' },
            'Concluido':     { bg:'#d4f6ee', color:'#0a7059' },
            'En Desarrollo': { bg:'#cfe7ff', color:'#0b5ed7' },
            'No Inicio':     { bg:'#eef0f5', color:'#5d6478' },
            'Pendiente':     { bg:'#fff4d6', color:'#8a6100' },
            'Postergado':    { bg:'#fff4d6', color:'#8a6100' },
            'Suspendido':    { bg:'#ffd9d3', color:'#b1240f' }
        };

        function buildBody(d) {
            var estado = (d.estado || '').trim();
            var sty = estadoStyles[estado] || { bg:'#eef0f5', color:'#5d6478' };
            var kind = '';
            var alertHtml = '';

            if (d.atrasado) {
                kind = 'is-late';
                alertHtml = '<div class="seg-modal-alert is-danger"><i class="ri-alarm-warning-line"></i><div><b>Sesión atrasada.</b> La fecha ya pasó y el horario sigue en <b>No Inicio</b>. Solicita al responsable que confirme o desarrolle esta sesión.</div></div>';
            } else if (d.confirmado_pasado) {
                kind = 'is-warn';
                alertHtml = '<div class="seg-modal-alert is-warn"><i class="ri-time-line"></i><div><b>El responsable no cambió el estado de la sesión.</b> La fecha ya pasó pero el horario sigue en <b>Confirmado</b>. Solicita que actualice el estado a Desarrollado o Postergado según corresponda.</div></div>';
            } else if (d.falta_registro) {
                kind = 'is-info';
                alertHtml = '<div class="seg-modal-alert is-info"><i class="ri-information-line"></i><div><b>Sesión marcada como desarrollada pero falta registrar la lista de asistencia.</b> Solicita al responsable que complete la información.</div></div>';
            } else if (estado === 'Desarrollado' || estado === 'Concluido') {
                kind = 'is-ok';
            } else if (d.confirmado_futuro) {
                kind = 'is-ok';
            }

            KINDS.forEach(function (c) { modalEl.classList.remove(c); });
            if (kind) modalEl.classList.add(kind);

            var asignadoHtml = '<span class="text-muted">Sin asignar</span>';
            var asignadoNombre = fullName(d.asignado_nombres, d.asignado_apellidos);
            if (asignadoNombre) {
                var color = d.asignado_color ? '#' + String(d.asignado_color).replace(/^#/,'') : '#6c757d';
                asignadoHtml = '<span class="asignado-pill" style="background:' + esc(color) + ';">' +
                               '<i class="ri-user-line"></i> ' + esc(asignadoNombre) + '</span>';
            }
            var docenteHtml = '<span class="text-muted">Sin asignar</span>';
            var docenteNombre = fullName(d.docente_nombres, d.docente_apellidos);
            if (docenteNombre) {
                docenteHtml = esc(docenteNombre) + (d.docente_ci ? ' <span class="text-muted">· CI ' + esc(d.docente_ci) + '</span>' : '');
            }

            function infoItem(ico, label, valueHtml) {
                return '<div class="seg-info-item">' +
                       '<div class="seg-info-ico"><i class="' + ico + '"></i></div>' +
                       '<div style="min-width:0;">' +
                       '<div class="seg-info-label">' + label + '</div>' +
                       '<div class="seg-info-value">' + valueHtml + '</div>' +
                       '</div></div>';
            }

            var html = '';
            html += alertHtml;

            var progHtml = '<a href="' + esc(d.programa_url) + '">' + esc(d.programa_nombre || '—') + '</a>' +
                           (d.programa_codigo ? ' <span class="text-muted">· ' + esc(d.programa_codigo) + '</span>' : '');

            html += '<div class="seg-modal-info-grid">';
            html += infoItem('ri-graduation-cap-line', 'Programa', progHtml);
            html += infoItem('ri-book-2-line', 'Módulo', '<a href="' + esc(d.programa_url) + '">' + esc(d.modulo_nombre || '—') + '</a>');
            html += infoItem('ri-user-star-line', 'Docente', docenteHtml);
            html += infoItem('ri-shield-user-line', 'Responsable', asignadoHtml);
            html += infoItem('ri-calendar-2-line', 'Fecha y horario',
                    esc(fmtFecha(d.fecha_desarrollo)) +
                    '<br><span class="text-muted"><i class="ri-time-line"></i> ' + esc(fmtHora(d.hora_inicio)) + ' a ' + esc(fmtHora(d.hora_fin)) + '</span>');
            html += infoItem('ri-pulse-line', 'Estado',
                    '<span class="chip" style="color:' + esc(sty.color) + ';background:' + esc(sty.bg) + ';">' + esc(estado || '—') + '</span>');
            html += '</div>'; // grid

            // Observaciones
            html += '<div class="mb-3">';
            html += '<div class="seg-detail-title"><i class="ri-message-2-line"></i> Observaciones</div>';
            if (d.tiene_observaciones) {
                html += '<div class="seg-obs-box">' + esc(d.observaciones || '') + '</div>';
            } else {
                html += '<div class="seg-obs-box seg-obs-empty">Sin observaciones registradas.</div>';
            }
            html += '</div>';

            // Lista de asistencia
            html += '<div class="seg-detail-title"><i class="ri-team-line"></i> Lista de asistencia';
            if (d.tiene_asistencias) {
                html += ' <span class="badge bg-soft-primary text-primary ms-1">' + (d.asistencias.length) + ' participantes</span>';
            }
            html += '</div>';

            if (!d.tiene_asistencias) {
                if (estado === 'Desarrollado') {
                    html += '<div class="seg-obs-box seg-obs-empty">Sin registros de asistencia — solicita al responsable que registre la lista.</div>';
                } else {
                    html += '<div class="seg-obs-box seg-obs-empty">La asistencia se registra una vez la sesión esté Desarrollada.</div>';
                }
            } else {
                var asistCfg = {
                    'Presente':    { ico:'ri-checkbox-circle-line', bg:'#d4f6ee', color:'#0a7059' },
                    'Atraso':      { ico:'ri-time-line',            bg:'#fff4d6', color:'#8a6100' },
                    'Falta':       { ico:'ri-close-circle-line',    bg:'#ffd9d3', color:'#b1240f' },
                    'Justificado': { ico:'ri-file-shield-2-line',   bg:'#dde7ff', color:'#1c3e9e' }
                };
                var resumen = {};
                d.asistencias.forEach(function (a) {
                    var k = (a.asistio || '').trim();
                    resumen[k] = (resumen[k] || 0) + 1;
                });
                var total = d.asistencias.length;

                // Tarjetas de resumen: estados canónicos primero, luego cualquier otro
                var statKeys = Object.keys(asistCfg).filter(function (k) { return resumen[k]; });
                Object.keys(resumen).forEach(function (k) {
                    if (statKeys.indexOf(k) === -1) statKeys.push(k);
                });
                html += '<div class="seg-asist-stats">';
                statKeys.forEach(function (k) {
                    var cfg = asistCfg[k] || { ico:'ri-question-line', bg:'#eef0f5', color:'#5d6478' };
                    var count = resumen[k] || 0;
                    var pct = total > 0 ? Math.round(count * 100 / total) : 0;
                    html += '<div class="seg-stat">' +
                            '<div class="seg-stat-ico" style="background:' + cfg.bg + ';color:' + cfg.color + ';"><i class="' + cfg.ico + '"></i></div>' +
                            '<div><div class="num">' + count + '</div><div class="lbl">' + esc(k) + '</div></div>' +
                            '<div class="pct">' + pct + '%</div>' +
                            '</div>';
                });
                html += '</div>';

                // Tabla con buscador
                var avatarColors = ['#405189','#0ab39c','#299cdb','#f06548','#f7b84b','#6559cc','#02a8b5','#d63384'];
                html += '<div class="seg-asist-wrap">';
                html += '<div class="seg-asist-toolbar">';
                html += '<div class="seg-asist-search"><i class="ri-search-line"></i>' +
                        '<input type="text" id="segAsistSearch" placeholder="Buscar por nombre o CI..." autocomplete="off"></div>';
                html += '<div class="d-flex align-items-center gap-2">';
                html += '<span class="seg-asist-countlbl" id="segAsistCount">' + total + ' de ' + total + '</span>';
                if (d.asistencia_pdf_url) {
                    html += '<a class="btn btn-sm btn-soft-danger d-inline-flex align-items-center gap-1" href="' + esc(d.asistencia_pdf_url) + '" title="Descargar reporte de asistencia en PDF">' +
                            '<i class="ri-file-pdf-line"></i> Reporte PDF</a>';
                }
                html += '</div>';
                html += '</div>';
                html += '<div class="seg-asist-scroll">';
                html += '<table class="seg-asist-table">';
                html += '<thead><tr><th style="width:38px;" class="text-center">#</th><th>Participante</th><th style="width:120px;" class="text-center">Asistencia</th><th style="width:28%;">Observación</th></tr></thead>';
                html += '<tbody>';
                d.asistencias.forEach(function (a, i) {
                    var nombre = ((a.apellidos||'') + ', ' + (a.nombres||'')).replace(/^, /,'').replace(/, $/,'');
                    var iniciales = ((a.nombres||' ').charAt(0) + (a.apellidos||' ').charAt(0)).toUpperCase().trim() || '?';
                    var colorIdx = 0;
                    for (var c = 0; c < nombre.length; c++) colorIdx = (colorIdx + nombre.charCodeAt(c)) % avatarColors.length;
                    var searchTxt = (nombre + ' ' + (a.ci || '')).toLowerCase();
                    html += '<tr data-search="' + esc(searchTxt) + '">' +
                            '<td class="text-center text-muted">' + (i+1) + '</td>' +
                            '<td><div class="seg-part-cell">' +
                            '<span class="seg-avatar" style="background:' + avatarColors[colorIdx] + ';">' + esc(iniciales) + '</span>' +
                            '<div style="min-width:0;"><div class="seg-part-name">' + esc(nombre) + '</div>' +
                            '<div class="seg-part-ci">CI: ' + esc(a.ci || '—') + '</div></div>' +
                            '</div></td>' +
                            '<td class="text-center"><span class="seg-a-pill seg-a-' + esc((a.asistio||'').trim()) + '">' + esc(a.asistio || '') + '</span></td>' +
                            '<td class="seg-asist-obs">' + esc(a.observacion || '—') + '</td>' +
                            '</tr>';
                });
                html += '</tbody></table></div></div>';
            }

            bodyEl.innerHTML = html;

            // Buscador de participantes
            var searchInput = bodyEl.querySelector('#segAsistSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    var q = this.value.toLowerCase().trim();
                    var rows = bodyEl.querySelectorAll('.seg-asist-table tbody tr');
                    var visibles = 0;
                    rows.forEach(function (r) {
                        var show = !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
                        r.style.display = show ? '' : 'none';
                        if (show) visibles++;
                    });
                    var cnt = bodyEl.querySelector('#segAsistCount');
                    if (cnt) cnt.textContent = visibles + ' de ' + rows.length;
                });
            }

            // Header
            var ttl = (d.programa_nombre || 'Programa') + (d.programa_codigo ? ' · ' + d.programa_codigo : '');
            var stl = (d.modulo_nombre ? d.modulo_nombre + ' · ' : '') + fmtFecha(d.fecha_desarrollo) + ' · ' + fmtHora(d.hora_inicio) + ' a ' + fmtHora(d.hora_fin);
            titleEl.textContent = ttl;
            subtitleEl.textContent = stl;

            if (d.programa_url) {
                gotoEl.href = d.programa_url;
                gotoEl.style.display = '';
            } else {
                gotoEl.removeAttribute('href');
                gotoEl.style.display = 'none';
            }
        }

        document.addEventListener('click', function (ev) {
            // Ignorar clicks sobre links explícitos dentro de la fila
            if (ev.target.closest('a')) return;
            var tr = ev.target.closest('tr.seg-row');
            if (!tr) return;
            var id = tr.getAttribute('data-horario-id');
            if (!id) return;
            var data = (window.__segHorarios || {})[id];
            if (!data) return;
            buildBody(data);
            modal.show();
        });
    })();

});
</script>
@endpush
