@extends('admin.layouts.master')

@section('title', $modulo->nombre . ' - ' . config('app.name'))

@push('css')
<style>
/* ===================================================================
   Módulo · Detalle — sistema visual (modo Operate)
   Tokens locales + componentes. Se conserva el lenguaje del panel.
   =================================================================== */
:root {
    --md-ink:       #1c2430;
    --md-ink-2:     #3f4a59;
    --md-muted:     #5c6878;
    --md-faint:     #8b95a3;
    --md-surface:   #ffffff;
    --md-surface-2: #f6f8fb;
    --md-surface-3: #eef1f6;
    --md-line:      #e4e8ef;
    --md-line-2:    #eef1f6;
    --md-accent:    #405189;
    --md-accent-2:  #5e72b8;
    --md-success:   #0c7a6a;
    --md-warning:   #925f0a;
    --md-danger:    #b23c29;
    --md-info:      #1a6bb8;
    --md-radius:    16px;
    --md-radius-sm: 12px;
    --md-shadow-sm: 0 1px 2px rgba(16,24,40,.05), 0 4px 14px -8px rgba(16,24,40,.14);
    --md-shadow-md: 0 18px 40px -20px rgba(24,36,64,.35);
    --md-ring:      0 0 0 3px rgba(64,81,137,.28);
}
[data-layout-mode="dark"] {
    --md-ink:       #eef2f7;
    --md-ink-2:     #c6cfdb;
    --md-muted:     #9aa5b4;
    --md-faint:     #7c8694;
    --md-surface:   #1b2026;
    --md-surface-2: #20262d;
    --md-surface-3: #262d35;
    --md-line:      #2c333c;
    --md-line-2:    #242a31;
    --md-accent:    #93a4ff;
    --md-accent-2:  #b6c1ff;
    --md-success:   #5fd6c2;
    --md-warning:   #f0b64d;
    --md-danger:    #ff9b85;
    --md-info:      #6bb6f5;
    --md-shadow-sm: 0 1px 2px rgba(0,0,0,.3), 0 6px 18px -10px rgba(0,0,0,.5);
    --md-shadow-md: 0 22px 48px -24px rgba(0,0,0,.7);
    --md-ring:      0 0 0 3px rgba(147,164,255,.32);
}

/* Texto legible sobre cualquier superficie, en ambos temas */
.acad-page .text-muted { color: var(--md-muted) !important; }
.acad-page .text-dark  { color: var(--md-ink) !important; }
[data-layout-mode="dark"] .text-dark { color: var(--md-ink) !important; }
.acad-page .text-success { color: var(--md-success) !important; }
.acad-page .text-danger  { color: var(--md-danger) !important; }
.acad-page .text-warning { color: var(--md-warning) !important; }
.acad-page .text-info    { color: var(--md-info) !important; }
[data-layout-mode="dark"] .acad-page .text-success { color: var(--md-success) !important; }
[data-layout-mode="dark"] .acad-page .text-danger  { color: var(--md-danger) !important; }
[data-layout-mode="dark"] .acad-page .text-warning { color: var(--md-warning) !important; }
[data-layout-mode="dark"] .acad-page .text-info    { color: var(--md-info) !important; }

/* Superficies del navegador: selección, foco y scrollbars */
.acad-page ::selection { background: rgba(64,81,137,.22); }
[data-layout-mode="dark"] .acad-page ::selection { background: rgba(147,164,255,.32); }
.acad-page a:focus-visible,
.acad-page button:focus-visible { outline: 0; box-shadow: var(--md-ring); border-radius: 8px; }
.acad-page .table-responsive { scrollbar-width: thin; scrollbar-color: var(--md-line) transparent; }
.acad-page .table-responsive::-webkit-scrollbar { height: 8px; width: 8px; }
.acad-page .table-responsive::-webkit-scrollbar-track { background: transparent; }
.acad-page .table-responsive::-webkit-scrollbar-thumb { background: var(--md-line); border-radius: 999px; }
.acad-page .table-responsive::-webkit-scrollbar-thumb:hover { background: var(--md-faint); }

/* ===== Hero ===== */
.modulo-hero {
    position: relative;
    background: linear-gradient(135deg, #38497d 0%, #4f63a8 46%, #2f7fbf 100%);
    border-radius: var(--md-radius);
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: var(--md-shadow-md);
    margin-bottom: 1.25rem;
}
.modulo-hero::before,
.modulo-hero::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; }
.modulo-hero::before { top: -60px; right: -40px; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.22) 0%, transparent 68%); }
.modulo-hero::after { bottom: -90px; left: 22%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,.09) 0%, transparent 70%); }
.modulo-hero > * { position: relative; z-index: 1; }
.modulo-hero .hero-icon {
    width: 60px; height: 60px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.32);
    border-radius: 14px;
    font-size: 1.7rem;
    color: #fff;
    backdrop-filter: blur(6px);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25);
    flex-shrink: 0;
}
.modulo-hero h4 { color: #fff; font-weight: 700; letter-spacing: .1px; line-height: 1.22; margin: 0; text-wrap: balance; }
.modulo-hero .hero-sub { color: rgba(255,255,255,.9); font-size: .85rem; margin: .2rem 0 0; }
.modulo-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.modulo-hero .breadcrumb-item,
.modulo-hero .breadcrumb-item a { color: rgba(255,255,255,.9); font-size: .8rem; }
.modulo-hero .breadcrumb-item a:hover { color: #fff; }
.modulo-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.modulo-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }
.hero-chip {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    padding: .35rem .8rem;
    border-radius: 999px;
    font-size: .73rem;
    font-weight: 600;
    line-height: 1.2;
    backdrop-filter: blur(6px);
    transition: background-color .18s ease, transform .18s ease;
}
.hero-chip i { color: currentColor; }
.btn-editar-modulo-detalle { cursor: pointer; }
.btn-editar-modulo-detalle:hover { border-color: var(--ui-accent) !important; }

/* ===== Cabecera del módulo (compone con la capa UI global) ===== */
.modulo-hero { padding: 1.35rem 1.45rem !important; }
.modulo-hero .breadcrumb { margin-bottom: 0 !important; }

.mh-top {
    display: flex; align-items: center; justify-content: space-between;
    gap: .75rem 1rem; flex-wrap: wrap; margin-bottom: 1rem;
}
.mh-back {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .76rem; font-weight: 600;
    color: var(--ui-ink-2) !important;
    background: var(--ui-surface-2);
    border: 1px solid var(--ui-line);
    border-radius: 999px;
    padding: .32rem .8rem;
    text-decoration: none;
    white-space: nowrap;
    transition: border-color .15s ease, color .15s ease;
}
.mh-back:hover { color: var(--ui-accent) !important; border-color: var(--ui-accent); }

.mh-main {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1.25rem 1.75rem; flex-wrap: wrap;
}
.mh-identity { display: flex; align-items: center; gap: 1rem; min-width: 0; flex: 1 1 380px; }
.mh-title { margin: 0 !important; }
.mh-meta { display: flex; align-items: center; flex-wrap: wrap; gap: .3rem 1rem; margin-top: .3rem; }
.mh-meta span { display: inline-flex; align-items: center; gap: .35rem; font-size: .82rem; color: var(--ui-muted) !important; }
.mh-meta span i { color: var(--ui-accent); font-size: .95em; }
.mh-chips { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .8rem; }

.mh-metrics {
    flex: 0 0 auto;
    display: grid; grid-template-columns: repeat(4, minmax(86px, 1fr));
    gap: .6rem;
}
.modulo-hero .hero-stat { text-align: center; padding: .7rem .8rem !important; }
.modulo-hero .hero-stat .stat-ic {
    width: 26px; height: 26px; margin: 0 auto .35rem;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: .92rem;
    background: var(--ui-accent-soft); color: var(--ui-accent);
}
.modulo-hero .hero-stat .stat-val {
    color: var(--ui-ink) !important;
    font-size: 1.3rem; font-weight: 800; line-height: 1.1; letter-spacing: -.01em;
}
.modulo-hero .hero-stat .stat-lbl { margin-top: .15rem; }

.mh-progress { margin-top: 1.15rem; padding-top: 1rem; border-top: 1px dashed var(--ui-line); }
.mh-progress-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: .5rem; }
.mh-progress-head .lbl {
    font-size: .72rem; font-weight: 700; letter-spacing: .3px; text-transform: uppercase;
    color: var(--ui-muted) !important;
    display: inline-flex; align-items: center; gap: .4rem;
}
.mh-progress-head .lbl i { color: var(--ui-accent); }
.mh-progress-head .val { font-size: .78rem; font-weight: 700; color: var(--ui-ink) !important; font-variant-numeric: tabular-nums; }
.mh-track { height: 8px; border-radius: 999px; background: var(--ui-surface-2); border: 1px solid var(--ui-line); overflow: hidden; }
.mh-fill { height: 100%; width: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--ui-brand) 0%, var(--ui-brand-2) 100%); transform-origin: left center; transform: scaleX(0); transition: transform .6s cubic-bezier(.16,1,.3,1); }

@media (max-width: 991.98px) {
    .mh-identity { flex: 1 1 100%; }
    .mh-metrics { width: 100%; }
}
@media (max-width: 575.98px) {
    .mh-metrics { grid-template-columns: repeat(2, 1fr); }
}

/* Hero stats */
.hero-stat {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: var(--md-radius-sm);
    padding: .75rem 1rem;
    backdrop-filter: blur(6px);
    text-align: center;
    transition: background-color .2s ease, transform .2s ease;
}
.hero-stat:hover { background: rgba(255,255,255,.22); transform: translateY(-2px); }
.hero-stat .stat-val { color: var(--ui-ink); font-weight: 700; font-size: 1.3rem; line-height: 1.1; font-variant-numeric: tabular-nums; }
.hero-stat .stat-lbl { color: rgba(255,255,255,.88); font-size: .65rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; margin-top: 2px; }

/* ===== Info Cards ===== */
.info-card {
    border: 1px solid var(--md-line);
    border-radius: var(--md-radius);
    background: var(--md-surface);
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.info-card .card-body { color: var(--md-ink-2); }
.info-card .card-header {
    background: var(--md-surface-2);
    border-bottom: 1px solid var(--md-line);
    padding: .9rem 1.25rem;
    display: flex; align-items: center; gap: .75rem;
}
.info-card .card-header .card-title { color: var(--md-ink); }
.info-card .section-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.05rem;
    flex-shrink: 0;
}

/* Docente block */
.docente-block {
    background: linear-gradient(135deg, #fffaf1 0%, #fff3df 100%);
    border: 1px solid #f3dcae;
    border-radius: var(--md-radius-sm);
    padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: 1rem;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .docente-block { background: linear-gradient(135deg, #2a2418 0%, #2f2918 100%); border-color: #4a3f1f; }
.docente-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #f7b84b 0%, #f59e0b 100%);
    color: #fff; font-weight: 700; font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px -8px rgba(217,119,6,.7);
}
.docente-name { font-weight: 700; font-size: 1rem; color: #3f3524; line-height: 1.25; margin: 0; }
[data-layout-mode="dark"] .docente-name { color: #f6ead4; }
.docente-meta { color: #7a6a4d; font-size: .78rem; }
[data-layout-mode="dark"] .docente-meta { color: #c9b693; }
.docente-meta i { color: #d97706; }

/* Stats row */
.stats-row {
    display: flex; align-items: center; gap: .5rem;
    flex-wrap: wrap;
    padding: .7rem 1rem;
    background: var(--md-surface-2);
    border-radius: var(--md-radius-sm);
    margin-bottom: 1rem;
    border: 1px solid var(--md-line);
}
.stats-row .badge { font-size: .72rem; font-weight: 700; padding: .42em .8em; border: 1px solid var(--md-line); }

/* ===== Tabla sesiones ===== */
.sessions-table { margin: 0; width: 100%; }
.sessions-table thead th {
    background: var(--md-surface-2) !important;
    font-weight: 700;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--md-muted);
    border-bottom: 1px solid var(--md-line) !important;
    padding: .75rem 1rem;
    white-space: nowrap;
}
.sessions-table tbody td {
    padding: .8rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--md-line-2);
    font-size: .85rem;
    color: var(--md-ink-2);
}
.sessions-table tbody tr:last-child td { border-bottom: 0; }
.sessions-table tbody tr { transition: background-color .15s ease; }
.sessions-table tbody tr:hover { background-color: var(--md-surface-2); }
[data-layout-mode="dark"] .sessions-table thead th { background: var(--md-surface-2) !important; color: var(--md-muted); border-bottom-color: var(--md-line) !important; }
[data-layout-mode="dark"] .sessions-table tbody td { border-bottom-color: var(--md-line-2); }
[data-layout-mode="dark"] .sessions-table tbody tr:hover { background-color: var(--md-surface-2); }

.sessions-table tbody tr.row-vencida {
    background: linear-gradient(90deg, rgba(194,69,47,.08) 0%, rgba(194,69,47,0) 100%);
}
.session-num {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--md-surface-3);
    color: var(--md-accent);
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: .8rem;
    font-variant-numeric: tabular-nums;
}
.fecha-cell { display: flex; flex-direction: column; gap: 2px; }
.fecha-cell .fecha-main { font-weight: 600; color: var(--md-ink); font-size: .85rem; text-transform: capitalize; }
.fecha-cell .fecha-sub { font-size: .72rem; color: var(--md-muted); }

.hora-pill {
    display: inline-flex; align-items: center; gap: 4px;
    background: var(--md-surface-3);
    color: var(--md-ink-2);
    border-radius: 7px;
    padding: .22rem .55rem;
    font-size: .78rem;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

/* Estado pill: relleno suave + texto con contraste AA en ambos temas */
.estado-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .7rem; font-weight: 700; letter-spacing: .2px;
    padding: .32em .72em; border-radius: 999px;
    border: 1px solid var(--md-line);
    background: var(--md-surface-2);
    color: var(--md-muted);
    white-space: nowrap;
}
.estado-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.estado-pill[data-estado="Desarrollado"] { color: #076e5e; background: #e7f7f3; border-color: #b9e9df; }
.estado-pill[data-estado="Confirmado"]  { color: #3b4d86; background: #e9edf8; border-color: #c4cfe9; }
.estado-pill[data-estado="Programado"]  { color: #495566; background: #eef1f5; border-color: #d5dbe3; }
.estado-pill[data-estado="Postergado"]  { color: #925f0a; background: #fdf3dd; border-color: #f2ddac; }
.estado-pill[data-estado="Suspendido"]  { color: #b23c29; background: #fdeae6; border-color: #f6cabe; }
[data-layout-mode="dark"] .estado-pill[data-estado="Desarrollado"] { color: #63dcc6; background: rgba(10,179,156,.16); border-color: rgba(10,179,156,.42); }
[data-layout-mode="dark"] .estado-pill[data-estado="Confirmado"]  { color: #aeb9ff; background: rgba(94,114,184,.2); border-color: rgba(94,114,184,.46); }
[data-layout-mode="dark"] .estado-pill[data-estado="Programado"]  { color: #c3ccd8; background: rgba(135,138,153,.18); border-color: rgba(135,138,153,.42); }
[data-layout-mode="dark"] .estado-pill[data-estado="Postergado"]  { color: #f3c465; background: rgba(247,184,75,.16); border-color: rgba(247,184,75,.42); }
[data-layout-mode="dark"] .estado-pill[data-estado="Suspendido"]  { color: #ff9b85; background: rgba(240,101,72,.16); border-color: rgba(240,101,72,.42); }

.btn-estado {
    border: 1px solid var(--md-line);
    background: var(--md-surface);
    color: var(--md-ink-2);
    border-radius: 9px;
    padding: .32rem .62rem;
    font-size: .72rem;
    font-weight: 600;
    line-height: 1.2;
    transition: border-color .15s ease, color .15s ease, background-color .15s ease;
    display: inline-flex; align-items: center; gap: .28rem;
}
.btn-estado:hover { border-color: var(--md-accent-2); color: var(--md-accent); background: var(--md-surface-2); }
.btn-estado:focus-visible { outline: 0; box-shadow: var(--md-ring); }
[data-layout-mode="dark"] .btn-estado { background: var(--md-surface); border-color: var(--md-line); color: var(--md-ink-2); }

/* ===== PDF button ===== */
.btn-pdf {
    background: linear-gradient(135deg, #e0553a 0%, #f0735a 100%);
    border: 0;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 6px 16px -8px rgba(224,85,58,.6);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-pdf:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 10px 22px -10px rgba(224,85,58,.75); }

.btn-whatsapp-ausentes:hover { background: #25d366 !important; color: #fff !important; border-color: #25d366 !important; }

/* Empty */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background: var(--md-surface-2);
    border-radius: var(--md-radius-sm);
    border: 1px dashed var(--md-line);
}
.empty-state h6 { color: var(--md-ink); }
.empty-state p { color: var(--md-muted); }
.empty-state .empty-icon {
    width: 70px; height: 70px;
    margin: 0 auto .75rem;
    display: flex; align-items: center; justify-content: center;
    background: var(--md-surface);
    border: 1px solid var(--md-line);
    border-radius: 50%;
    font-size: 2rem;
    color: var(--md-faint);
}

/* Notas */
.nota-cell {
    display: inline-flex;
    align-items: center; justify-content: center;
    min-width: 64px;
    padding: .3rem .6rem;
    border-radius: 9px;
    font-weight: 700;
    font-size: .85rem;
    font-variant-numeric: tabular-nums;
    border: 1px solid transparent;
}
.bg-soft-success-badge { background: rgba(10,179,156,.12); border-color: rgba(10,179,156,.3); }
.bg-soft-danger-badge  { background: rgba(240,101,72,.12); border-color: rgba(240,101,72,.3); }
[data-layout-mode="dark"] .bg-soft-success-badge { background: rgba(10,179,156,.18); border-color: rgba(10,179,156,.4); }
[data-layout-mode="dark"] .bg-soft-danger-badge  { background: rgba(240,101,72,.18); border-color: rgba(240,101,72,.4); }
.nota-cell.bg-soft-success-badge { color: var(--md-success) !important; }
.nota-cell.bg-soft-danger-badge  { color: var(--md-danger) !important; }

/* ===== Modal Sesión / Asistencia ===== */
#modalSesion .modal-content,
#modalAsistencia .modal-content {
    border: 0;
    border-radius: var(--md-radius);
    overflow: hidden;
    box-shadow: 0 30px 70px -20px rgba(15,23,42,.45);
}
#modalSesion .modal-header,
#modalAsistencia .modal-header {
    background: linear-gradient(135deg, #38497d 0%, #4f63a8 48%, #2f7fbf 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalSesion .modal-header::after,
#modalAsistencia .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalSesion .modal-header > *,
#modalAsistencia .modal-header > * { position: relative; z-index: 1; }
.modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.32);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalSesion .modal-title,
#modalAsistencia .modal-title { color: #fff; font-weight: 700; }
#modalSesion .modal-subtitle,
#modalAsistencia .modal-subtitle { color: rgba(255,255,255,.9); font-size: .78rem; }
#modalSesion .modal-header .btn-close,
#modalAsistencia .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalSesion .modal-body,
#modalAsistencia .modal-body { padding: 1.5rem; background: var(--md-surface-2); }
[data-layout-mode="dark"] #modalSesion .modal-body,
[data-layout-mode="dark"] #modalAsistencia .modal-body { background: #171b21; }
#modalSesion .modal-footer,
#modalAsistencia .modal-footer { background: var(--md-surface); border-top: 1px solid var(--md-line); padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalSesion .modal-footer,
[data-layout-mode="dark"] #modalAsistencia .modal-footer { background: var(--md-surface); border-top-color: var(--md-line); }

/* Estado picker cards */
.estado-picker { display: grid; grid-template-columns: repeat(2, 1fr); gap: .65rem; }
@media (min-width: 576px) { .estado-picker { grid-template-columns: repeat(4, 1fr); } }
.estado-option {
    border: 1.5px solid var(--md-line);
    background: var(--md-surface);
    border-radius: var(--md-radius-sm);
    padding: .85rem .65rem;
    cursor: pointer;
    text-align: center;
    transition: border-color .15s ease, transform .15s ease, background-color .15s ease;
}
.estado-option:hover { border-color: #b9c2d6; transform: translateY(-1px); background: var(--md-surface-2); }
.estado-option i { display: block; font-size: 1.5rem; margin-bottom: .35rem; color: var(--md-faint); }
.estado-option .nombre { font-size: .82rem; font-weight: 700; color: var(--md-ink-2); }
.estado-option.is-active {
    border-color: transparent;
    transform: translateY(-1px);
    box-shadow: 0 0 0 2px var(--md-accent), 0 12px 24px -14px rgba(15,23,42,.45);
}
.estado-option.is-active i,
.estado-option.is-active .nombre { color: var(--md-ink); }
.estado-option.is-active[data-estado="Confirmado"]   { background: #e9edf8; box-shadow: 0 0 0 2px #405189, 0 12px 24px -14px rgba(15,23,42,.45); }
.estado-option.is-active[data-estado="Programado"]   { background: #eef1f5; box-shadow: 0 0 0 2px #6c757d, 0 12px 24px -14px rgba(15,23,42,.45); }
.estado-option.is-active[data-estado="Desarrollado"] { background: #e7f7f3; box-shadow: 0 0 0 2px #0a8f7d, 0 12px 24px -14px rgba(15,23,42,.45); }
.estado-option.is-active[data-estado="Postergado"]   { background: #fdf3dd; box-shadow: 0 0 0 2px #b45309, 0 12px 24px -14px rgba(15,23,42,.45); }
.estado-option.is-active[data-estado="Confirmado"] i,
.estado-option.is-active[data-estado="Confirmado"] .nombre   { color: #3b4d86; }
.estado-option.is-active[data-estado="Programado"] i,
.estado-option.is-active[data-estado="Programado"] .nombre   { color: #495566; }
.estado-option.is-active[data-estado="Desarrollado"] i,
.estado-option.is-active[data-estado="Desarrollado"] .nombre { color: #076e5e; }
.estado-option.is-active[data-estado="Postergado"] i,
.estado-option.is-active[data-estado="Postergado"] .nombre   { color: #925f0a; }
[data-layout-mode="dark"] .estado-option { background: var(--md-surface); border-color: var(--md-line); }
[data-layout-mode="dark"] .estado-option .nombre { color: var(--md-ink-2); }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Confirmado"]   { background: rgba(94,114,184,.2); }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Programado"]   { background: rgba(135,138,153,.2); }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Desarrollado"] { background: rgba(10,179,156,.2); }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Postergado"]   { background: rgba(247,184,75,.2); }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Confirmado"] i,
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Confirmado"] .nombre   { color: #aeb9ff; }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Programado"] i,
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Programado"] .nombre   { color: #c3ccd8; }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Desarrollado"] i,
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Desarrollado"] .nombre { color: #63dcc6; }
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Postergado"] i,
[data-layout-mode="dark"] .estado-option.is-active[data-estado="Postergado"] .nombre   { color: #f3c465; }

/* Modal asistencia */
#modalAsistencia .modal-dialog { max-width: 920px; }
.asistencia-toolbar {
    display: flex; align-items: center; gap: .5rem;
    flex-wrap: wrap;
    background: var(--md-surface);
    border: 1px solid var(--md-line);
    border-radius: var(--md-radius-sm);
    padding: .65rem .85rem;
    margin-bottom: .85rem;
}
.asist-search {
    flex: 1; min-width: 200px;
    border: 1px solid var(--md-line);
    border-radius: 9px;
    padding: .45rem .75rem .45rem 2rem;
    font-size: .85rem;
    color: var(--md-ink);
    background: var(--md-surface) url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%238b95a3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>') .55rem center no-repeat;
}
.asist-search::placeholder { color: var(--md-faint); }
.asist-search:focus { outline: 0; border-color: var(--md-accent-2); box-shadow: var(--md-ring); }
[data-layout-mode="dark"] .asist-search { background-color: var(--md-surface); border-color: var(--md-line); color: var(--md-ink); }
.bulk-action {
    border: 1px solid var(--md-line);
    background: var(--md-surface);
    color: var(--md-ink-2);
    border-radius: 9px;
    padding: .4rem .75rem;
    font-size: .76rem;
    font-weight: 700;
    transition: border-color .15s ease, color .15s ease, background-color .15s ease;
    cursor: pointer;
}
.bulk-action:hover { background: var(--md-surface-2); color: var(--md-accent); border-color: var(--md-accent-2); }
[data-layout-mode="dark"] .bulk-action { background: var(--md-surface); border-color: var(--md-line); color: var(--md-ink-2); }

.asistencia-table { margin: 0; width: 100%; }
.asistencia-table thead th {
    background: var(--md-surface-2) !important;
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--md-muted);
    border-bottom: 1px solid var(--md-line) !important;
    padding: .65rem .85rem;
    position: sticky; top: 0; z-index: 1;
}
.asistencia-table tbody td {
    padding: .65rem .85rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--md-line-2);
    font-size: .85rem;
    color: var(--md-ink-2);
}
[data-layout-mode="dark"] .asistencia-table thead th { background: var(--md-surface-2) !important; color: var(--md-muted); border-bottom-color: var(--md-line) !important; }
[data-layout-mode="dark"] .asistencia-table tbody td { border-bottom-color: var(--md-line-2); }

.part-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-weight: 700; font-size: .72rem;
    flex-shrink: 0;
}

.asistio-select { border-radius: 8px; font-size: .8rem; font-weight: 600; min-width: 130px; }
.asistio-select[data-val="Presente"]    { color: #076e5e; border-color: #b9e9df; background-color: #e7f7f3; }
.asistio-select[data-val="Ausente"]     { color: #b23c29; border-color: #f6cabe; background-color: #fdeae6; }
.asistio-select[data-val="Tardanza"]    { color: #925f0a; border-color: #f2ddac; background-color: #fdf3dd; }
.asistio-select[data-val="Justificado"] { color: #1a6bb8; border-color: #bcdcf6; background-color: #e8f3fd; }
[data-layout-mode="dark"] .asistio-select[data-val="Presente"]    { color: #5fd6c2; background-color: #10302a; border-color: #1f5c50; }
[data-layout-mode="dark"] .asistio-select[data-val="Ausente"]     { color: #ff9b85; background-color: #3a1e18; border-color: #6b3226; }
[data-layout-mode="dark"] .asistio-select[data-val="Tardanza"]    { color: #f0b64d; background-color: #362a10; border-color: #6b5218; }
[data-layout-mode="dark"] .asistio-select[data-val="Justificado"] { color: #6bb6f5; background-color: #122436; border-color: #234a6b; }

/* Dados de baja: fila en rojo, sin registro de asistencia */
.asistencia-table tr.asist-baja > td { background: #fff1f1 !important; }
.asistencia-table tr.asist-baja > td:first-child { box-shadow: inset 3px 0 0 #dc3545; }
.asistencia-table tr.asist-baja .part-avatar { background: #dc3545; color: #fff; }
.asistencia-table tr.asist-baja .asistio-select:disabled { color: #8a5a5a; background-color: #f6e3e3; border-color: #efc6c6; }
[data-layout-mode="dark"] .asistencia-table tr.asist-baja > td { background: #3a1d20 !important; }
.asist-baja-tag { display: inline-flex; align-items: center; gap: .2rem; margin-top: .15rem; padding: 0 .45rem; border-radius: 999px;
    font-size: .66rem; font-weight: 700; color: #fff; background: #dc3545; }
.asist-bajas-hint { margin-bottom: .75rem; padding: .45rem .75rem; border-radius: 8px; font-size: .8rem; color: #b42318; background: #fff1f1; }
[data-layout-mode="dark"] .asist-bajas-hint { color: #f19a9a; background: #3a1d20; }

.obs-input {
    border: 1px solid var(--md-line);
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .8rem;
    color: var(--md-ink);
    width: 100%;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.obs-input::placeholder { color: var(--md-faint); }
.obs-input:focus { border-color: var(--md-accent-2); box-shadow: var(--md-ring); outline: 0; }
[data-layout-mode="dark"] .obs-input { background: var(--md-surface); border-color: var(--md-line); color: var(--md-ink); }

#asistenciaList tr.hidden-row { display: none; }

/* ===== Estadísticas de asistencia ===== */
.stat-mini {
    background: var(--md-surface);
    border: 1px solid var(--md-line);
    border-radius: var(--md-radius);
    padding: 1rem;
    height: 100%;
    transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
}
.stat-mini:hover { transform: translateY(-2px); box-shadow: var(--md-shadow-sm); }
.stat-mini .lbl { font-size: .65rem; text-transform: uppercase; letter-spacing: .5px; color: var(--md-muted); font-weight: 700; }
.stat-mini .val { font-size: 1.4rem; font-weight: 700; color: var(--md-ink); line-height: 1.2; font-variant-numeric: tabular-nums; }
.stat-mini .ic {
    width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.1rem;
}

.chart-wrap {
    background: var(--md-surface);
    border: 1px solid var(--md-line);
    border-radius: var(--md-radius);
    padding: 1rem 1.25rem;
    height: 100%;
}
.chart-wrap h6 { font-weight: 700; font-size: .85rem; margin: 0 0 .35rem; color: var(--md-ink); }
.chart-wrap .chart-sub { font-size: .72rem; color: var(--md-muted); margin-bottom: .5rem; }

.parts-table { margin: 0; width: 100%; table-layout: fixed; }
.parts-table th:nth-child(1) { width: 50px; }
.parts-table th:nth-child(3),
.parts-table th:nth-child(4),
.parts-table th:nth-child(5),
.parts-table th:nth-child(6) { width: 10%; }
.parts-table th:nth-child(7) { width: 11%; }
.parts-table th:nth-child(8) { width: 25%; }
.parts-table td:nth-child(2) { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.parts-table thead th {
    background: var(--md-surface-2) !important;
    font-size: .67rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--md-muted);
    border-bottom: 1px solid var(--md-line) !important;
    padding: .65rem .75rem;
}
.parts-table tbody td {
    padding: .6rem .75rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--md-line-2);
    font-size: .82rem;
    color: var(--md-ink-2);
}
.parts-table tbody tr:last-child td { border-bottom: 0; }
[data-layout-mode="dark"] .parts-table thead th { background: var(--md-surface-2) !important; color: var(--md-muted); border-bottom-color: var(--md-line) !important; }
[data-layout-mode="dark"] .parts-table tbody td { border-bottom-color: var(--md-line-2); }

.pct-bar {
    width: 100%; height: 8px;
    background: var(--md-surface-3);
    border-radius: 999px;
    overflow: hidden;
    margin-top: 4px;
}
.pct-bar-fill { height: 100%; width: 100%; border-radius: 999px; transform-origin: left center; transform: scaleX(0); transition: transform .5s ease; }
.pct-text { font-weight: 700; font-size: .8rem; font-variant-numeric: tabular-nums; }
.pct-text.is-high { color: var(--md-success); }
.pct-text.is-mid  { color: var(--md-warning); }
.pct-text.is-low  { color: var(--md-danger); }
.mini-tag {
    display: inline-block;
    font-size: .68rem;
    font-weight: 700;
    padding: .15em .55em;
    border-radius: 999px;
    border: 1px solid;
}
.tag-pres { background: #e7f7f3; color: #076e5e; border-color: #b9e9df; }
.tag-aus  { background: #fdeae6; color: #b23c29; border-color: #f6cabe; }
.tag-tar  { background: #fdf3dd; color: #925f0a; border-color: #f2ddac; }
.tag-jus  { background: #e8f3fd; color: #1a6bb8; border-color: #bcdcf6; }
.tag-sin  { background: var(--md-surface-3); color: var(--md-muted); border-color: var(--md-line); }
[data-layout-mode="dark"] .tag-pres { background: rgba(10,179,156,.16); color: #5fd6c2; border-color: rgba(10,179,156,.42); }
[data-layout-mode="dark"] .tag-aus  { background: rgba(240,101,72,.16); color: #ff9b85; border-color: rgba(240,101,72,.42); }
[data-layout-mode="dark"] .tag-tar  { background: rgba(247,184,75,.16); color: #f0b64d; border-color: rgba(247,184,75,.42); }
[data-layout-mode="dark"] .tag-jus  { background: rgba(41,156,219,.18); color: #6bb6f5; border-color: rgba(41,156,219,.44); }
[data-layout-mode="dark"] .tag-sin  { background: var(--md-surface-3); color: var(--md-muted); border-color: var(--md-line); }
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $estadoColorMap = [
        'Desarrollado' => '#0ab39c',
        'Confirmado'   => '#405189',
        'Programado'   => '#878a99',
        'Postergado'   => '#f7b84b',
    ];
    $estadoIconMap = [
        'Desarrollado' => 'ri-checkbox-circle-line',
        'Confirmado'   => 'ri-shield-check-line',
        'Programado'   => 'ri-calendar-line',
        'Postergado'   => 'ri-time-line',
    ];
    $devIcon = match($modulo->desarrollo) {
        'Concluido' => 'ri-checkbox-circle-line',
        'En Desarrollo' => 'ri-loader-4-line',
        'No Inicio' => 'ri-time-line',
        'Pendiente' => 'ri-hourglass-line',
        'Suspendido' => 'ri-close-circle-line',
        default => 'ri-information-line'
    };
    $sesionesTotal = count($horarios);
    $sesionesDesarrolladas = collect($horarios)->where('estado','Desarrollado')->count();
    $progresoSes = $sesionesTotal > 0 ? round(($sesionesDesarrolladas / $sesionesTotal) * 100) : 0;

    // Permisos de gestión del módulo: Super Admin, personal académico con
    // permiso o responsable asignado del programa (asistencia, notas, nivelación).
    $authUser = auth()->user();
    $esSuperAdmin = $authUser->hasRole('Super Admin');
    $esResponsablePrograma = $authUser->esResponsableDelPrograma($programa->id);
    $puedeGestionarModulo = $esSuperAdmin
        || $authUser->hasRole('Responsable Académico')
        || $authUser->can('programas.modulos.estado')
        || $authUser->can('programas.modulos.cambiar.estado')
        || $esResponsablePrograma;
    $puedeWhatsappPrograma = $authUser->puedeWhatsappPrograma($programa->id);
@endphp

{{-- HERO --}}
<div class="modulo-hero">
    <div class="mh-top">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.programas.index') }}"><i class="ri-graduation-cap-line align-bottom me-1"></i>Programas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.programas.show', $programa->id) }}">{{ $programa->nombre }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Módulo</li>
        </ol>
        <a href="{{ route('admin.programas.show', $programa->id) }}" class="mh-back">
            <i class="ri-arrow-left-line"></i> Volver al programa
        </a>
    </div>

    <div class="mh-main">
        <div class="mh-identity">
            <div class="hero-icon"><i class="ri-book-open-line"></i></div>
            <div style="min-width:0;">
                <h4 class="mh-title">{{ $modulo->nombre }}</h4>
                <div class="mh-meta">
                    <span><i class="ri-folder-3-line"></i>{{ $programa->tipo_nombre }}</span>
                    <span><i class="ri-calendar-2-line"></i>Gestión {{ $programa->gestion ?? '—' }}</span>
                    @if(!empty($programa->codigo))
                    <span><i class="ri-hashtag"></i>{{ $programa->codigo }}</span>
                    @endif
                </div>
                <div class="mh-chips">
                    @if($puedeGestionarModulo)
                    <span class="hero-chip btn-editar-modulo-detalle" role="button" id="moduloDevBadge" data-modulo-id="{{ $modulo->id }}" data-desarrollo="{{ $modulo->desarrollo }}" data-fecha-conclusion="{{ $modulo->fecha_conclusion }}" title="Haz clic para cambiar estado">
                    @else
                    <span class="hero-chip">
                    @endif
                        <i class="{{ $devIcon }}"></i>{{ $modulo->desarrollo }}
                    </span>
                    @if($modulo->fecha_conclusion)
                    <span class="hero-chip"><i class="ri-calendar-check-line"></i>Concluido: {{ \Carbon\Carbon::parse($modulo->fecha_conclusion)->format('d/m/Y') }} · Límite nivelación: {{ \Carbon\Carbon::parse($modulo->fecha_conclusion)->addMonths(2)->format('d/m/Y') }}</span>
                    @endif
                    <span class="hero-chip"><i class="ri-group-line"></i>{{ $inscritosCount }} inscritos</span>
                    <span class="hero-chip"><i class="ri-checkbox-circle-line"></i>{{ $conNota }} con nota</span>
                    <span class="hero-chip hero-chip-moodle" id="moduloMoodleBadge" style="cursor:pointer;" title="{{ $modulo->moodle_link ? 'Ir a Moodle' : 'Agregar enlace Moodle' }}" data-tiene-link="{{ $modulo->moodle_link ? '1' : '0' }}" data-url="{{ $modulo->moodle_link ?? '' }}">
                        <i class="ri-{{ $modulo->moodle_link ? 'external-link-line' : 'add-line' }}"></i>
                        <span>Moodle</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="mh-metrics">
            <div class="hero-stat">
                <span class="stat-ic"><i class="ri-calendar-event-line"></i></span>
                <div class="stat-val">{{ $sesionesTotal }}</div>
                <div class="stat-lbl">Sesiones</div>
            </div>
            <div class="hero-stat">
                <span class="stat-ic"><i class="ri-checkbox-circle-line"></i></span>
                <div class="stat-val">{{ $sesionesDesarrolladas }}</div>
                <div class="stat-lbl">Desarrolladas</div>
            </div>
            <div class="hero-stat">
                <span class="stat-ic"><i class="ri-progress-4-line"></i></span>
                <div class="stat-val">{{ $progresoSes }}%</div>
                <div class="stat-lbl">Avance</div>
            </div>
            <div class="hero-stat">
                <span class="stat-ic"><i class="ri-group-line"></i></span>
                <div class="stat-val">{{ $inscritosCount }}</div>
                <div class="stat-lbl">Alumnos</div>
            </div>
        </div>
    </div>

    <div class="mh-progress">
        <div class="mh-progress-head">
            <span class="lbl"><i class="ri-progress-4-line"></i>Avance de sesiones</span>
            <span class="val">{{ $sesionesDesarrolladas }} de {{ $sesionesTotal }} desarrolladas · {{ $progresoSes }}%</span>
        </div>
        <div class="mh-track" role="progressbar" aria-valuenow="{{ $progresoSes }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avance de sesiones">
            <div class="mh-fill" style="transform: scaleX({{ $sesionesTotal > 0 ? round($progresoSes / 100, 4) : 0 }});"></div>
        </div>
    </div>
</div>

{{-- DOCENTE --}}
<div class="info-card">
    <div class="card-header">
        <span class="section-icon bg-soft-warning text-warning"><i class="ri-user-star-line"></i></span>
        <h5 class="card-title mb-0 fs-14 fw-semibold">Docente Asignado</h5>
    </div>
    <div class="card-body">
        @if($modulo->docente_apellidos)
        <div class="docente-block">
            <div class="docente-avatar">
                {{ strtoupper(substr($modulo->docente_nombres, 0, 1) . substr($modulo->docente_apellidos, 0, 1)) }}
            </div>
            <div class="row g-2 flex-grow-1">
                <div class="col-md-6">
                    <p class="docente-name">{{ $modulo->docente_apellidos }}, {{ $modulo->docente_nombres }}</p>
                    <p class="docente-meta mb-0"><i class="ri-id-card-line me-1"></i>CI: {{ $modulo->docente_ci }}</p>
                </div>
                <div class="col-md-6">
                    @if($modulo->docente_correo)
                    <p class="docente-meta mb-1"><i class="ri-mail-line me-1"></i>{{ $modulo->docente_correo }}</p>
                    @endif
                    @if($modulo->docente_celular)
                    <p class="docente-meta mb-0"><i class="ri-phone-line me-1"></i>{{ $modulo->docente_celular }}</p>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="ri-user-unfollow-line"></i></div>
            <h6 class="text-dark fw-bold mb-1">Sin docente asignado</h6>
            <p class="text-muted mb-0">Este módulo aún no tiene un docente vinculado.</p>
        </div>
        @endif
    </div>
</div>

{{-- HORARIOS / SESIONES --}}
<div class="info-card">
    <div class="card-header">
        <span class="section-icon bg-soft-info text-info"><i class="ri-calendar-check-line"></i></span>
        <h5 class="card-title mb-0 fs-14 fw-semibold">Sesiones de Clase</h5>
        <span class="badge bg-soft-info text-info fs-11 ms-2">{{ $sesionesTotal }} sesiones</span>
    </div>
    <div class="card-body">
        @if($sesionesTotal > 0)
            @php
                $byEstado = collect($horarios)->groupBy('estado');
            @endphp
            <div class="stats-row">
                @foreach($byEstado as $estado => $items)
                    @php
                        $color = $estadoColorMap[$estado] ?? '#878a99';
                        $icon = $estadoIconMap[$estado] ?? 'ri-information-line';
                    @endphp
                    <span class="estado-pill" style="color: {{ $color }};">
                        <i class="{{ $icon }} ms-1"></i>{{ $estado }}: {{ count($items) }}
                    </span>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="sessions-table table align-middle">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            <th>Fecha</th>
                            <th class="text-center" style="width:90px;">Inicio</th>
                            <th class="text-center" style="width:90px;">Fin</th>
                            <th>Asignado</th>
                            <th style="width:140px;">Estado</th>
                            <th>Observaciones</th>
                            <th class="text-center" style="width:120px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horarios as $h)
                        @php
                            $fecha = \Carbon\Carbon::parse($h->fecha_desarrollo);
                            $dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
                            $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                            $diaTxt = $dias[$fecha->dayOfWeek];
                            $fechaTxt = $fecha->day . ' de ' . $meses[$fecha->month - 1] . ' del ' . $fecha->year;
                            $color = $estadoColorMap[$h->estado] ?? '#878a99';
                            $icon = $estadoIconMap[$h->estado] ?? 'ri-information-line';
                            $vencida = $h->estado === 'Confirmado' && $fecha->isPast();
                        @endphp
                        <tr id="sesion-row-{{ $h->id }}" class="{{ $vencida ? 'row-vencida' : '' }}">
                            <td class="text-center"><span class="session-num">{{ $loop->iteration }}</span></td>
                            <td>
                                <div class="fecha-cell">
                                    <span class="fecha-main">{{ $diaTxt }}</span>
                                    <span class="fecha-sub">{{ $fechaTxt }}</span>
                                </div>
                            </td>
                            <td class="text-center"><span class="hora-pill"><i class="ri-time-line"></i>{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}</span></td>
                            <td class="text-center"><span class="hora-pill"><i class="ri-time-line"></i>{{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</span></td>
                            <td>
                                @if($h->asignado_apellidos)
                                    <i class="ri-user-line text-muted me-1"></i>{{ $h->asignado_apellidos }}, {{ $h->asignado_nombres }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="estado-pill estado-pill-cell" data-estado="{{ $h->estado }}" data-estado-cell="{{ $h->id }}">
                                    <i class="{{ $icon }}"></i>{{ $h->estado }}
                                </span>
                                @if($vencida)
                                <div><span class="badge bg-soft-danger text-danger fs-10 mt-1">Vencido</span></div>
                                @endif
                            </td>
                            <td class="text-muted" style="max-width: 200px;">
                                <span data-obs-cell="{{ $h->id }}" class="d-inline-block text-truncate" style="max-width: 100%;" title="{{ $h->observaciones }}">{{ $h->observaciones ?: '—' }}</span>
                            </td>
                            <td class="text-center" data-action-cell="{{ $h->id }}">
                                @if($puedeGestionarModulo)
                                @if($h->estado === 'Desarrollado')
                                    <button class="btn-estado btn-ver-asistencia"
                                            data-horario-id="{{ $h->id }}"
                                            data-fecha="{{ $diaTxt }} {{ $fechaTxt }}"
                                            data-hora="{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}"
                                            data-observaciones="{{ $h->observaciones }}"
                                            style="border-color:#b9e9df;color:#076e5e;background:#e7f7f3;">
                                        <i class="ri-team-line"></i> Asistencia
                                    </button>
                                    @if($puedeWhatsappPrograma)
                                    <button class="btn-estado btn-whatsapp-ausentes mt-1"
                                            data-horario-id="{{ $h->id }}"
                                            data-fecha="{{ $diaTxt }} {{ $fechaTxt }}"
                                            data-hora="{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}"
                                            data-programa="{{ $programa->nombre }}"
                                            data-modulo="{{ $modulo->nombre }}"
                                            title="Enviar WhatsApp a participantes ausentes"
                                            style="border-color:#b7e7c6;color:#127a3e;background:#ecfdf3;">
                                        <i class="ri-whatsapp-line"></i> Ausentes WhatsApp
                                    </button>
                                    @endif
                                @endif
                                <button class="btn-estado btn-cambiar-estado mt-1"
                                        data-horario-id="{{ $h->id }}"
                                        data-estado="{{ $h->estado }}"
                                        data-fecha="{{ $diaTxt }} {{ $fechaTxt }}"
                                        data-hora="{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}"
                                        data-observaciones="{{ $h->observaciones }}">
                                    <i class="ri-edit-line"></i> Estado
                                </button>
                                <button class="btn-estado btn-editar-fecha-hora mt-1"
                                        data-horario-id="{{ $h->id }}"
                                        data-fecha="{{ $h->fecha_desarrollo }}"
                                        data-hora-inicio="{{ $h->hora_inicio }}"
                                        data-hora-fin="{{ $h->hora_fin }}">
                                    <i class="ri-calendar-line"></i> Fecha/Hora
                                </button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-calendar-close-line"></i></div>
                <h6 class="text-dark fw-bold mb-1">Sin sesiones registradas</h6>
                <p class="text-muted mb-0">No hay sesiones de clase para este módulo.</p>
            </div>
        @endif
    </div>
</div>

{{-- ESTADÍSTICAS DE ASISTENCIA --}}
<div class="info-card" id="asist-stats-card">
    <div class="card-header">
        <span class="section-icon bg-soft-success text-success"><i class="ri-pie-chart-2-line"></i></span>
        <h5 class="card-title mb-0 fs-14 fw-semibold">Estadísticas de Asistencia</h5>
        <span class="badge bg-soft-success text-success fs-11 ms-2" id="asistStatsBadge">—</span>
        <button class="btn btn-sm btn-soft-primary ms-auto d-inline-flex align-items-center gap-1" id="btnReloadStats">
            <i class="ri-refresh-line"></i> Actualizar
        </button>
    </div>
    <div class="card-body">
        <div id="asistStatsLoader" class="text-center py-4 text-muted">
            <span class="spinner-border spinner-border-sm me-2"></span>Cargando estadísticas...
        </div>
        <div id="asistStatsEmpty" class="empty-state" style="display:none;">
            <div class="empty-icon"><i class="ri-bar-chart-2-line"></i></div>
            <h6 class="text-dark fw-bold mb-1">Sin asistencia registrada</h6>
            <p class="text-muted mb-0">Marca sesiones como <b>Desarrollado</b> y registra asistencia para ver estadísticas.</p>
        </div>
        <div id="asistStatsContent" style="display:none;">
            {{-- KPIs --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="stat-mini" style="--mini-accent:#0ab39c;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div><div class="lbl">% Asistencia</div><div class="val" id="kpiPct">0%</div></div>
                            <span class="ic bg-soft-success text-success"><i class="ri-percent-line"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini" style="--mini-accent:#405189;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div><div class="lbl">Sesiones Desarrolladas</div><div class="val" id="kpiSes">0</div></div>
                            <span class="ic bg-soft-primary text-primary"><i class="ri-calendar-check-line"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini" style="--mini-accent:#299cdb;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div><div class="lbl">Participantes</div><div class="val" id="kpiParts">0</div></div>
                            <span class="ic bg-soft-info text-info"><i class="ri-group-line"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini" style="--mini-accent:#f7b84b;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div><div class="lbl">Registros</div><div class="val" id="kpiReg">0</div></div>
                            <span class="ic bg-soft-warning text-warning"><i class="ri-file-list-3-line"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row g-3 mb-3">
                <div class="col-lg-5">
                    <div class="chart-wrap">
                        <h6><i class="ri-donut-chart-line text-success"></i> Distribución Global</h6>
                        <div class="chart-sub">Proporción de cada tipo de asistencia</div>
                        <div id="chartDonut" style="min-height: 280px;"></div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="chart-wrap">
                        <h6><i class="ri-bar-chart-grouped-line text-primary"></i> Asistencia por Sesión</h6>
                        <div class="chart-sub">Conteo por estado en cada sesión desarrollada</div>
                        <div id="chartBar" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Tabla por participante --}}
            <div class="chart-wrap">
                <h6><i class="ri-user-star-line text-info"></i> Ranking por Participante</h6>
                <div class="chart-sub">% de asistencia efectiva (Presente + Tardanza + Justificado) sobre sesiones desarrolladas</div>
                <div class="table-responsive mt-2">
                    <table class="parts-table table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Participante</th>
                                <th class="text-center">Pres.</th>
                                <th class="text-center">Aus.</th>
                                <th class="text-center">Tard.</th>
                                <th class="text-center">Just.</th>
                                <th class="text-center">Sin Reg.</th>
                                <th>% Asistencia</th>
                            </tr>
                        </thead>
                        <tbody id="partsStatsBody"></tbody>
                    </table>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2" id="partsPagination" style="display:none;">
                    <small class="text-muted" id="partsPageInfo"></small>
                    <nav><ul class="pagination pagination-sm mb-0" id="partsPageNav"></ul></nav>
                </div>
            </div>
        </div>

            {{-- NOTAS --}}
@php
    $aprobadosMod = 0; $reprobadosMod = 0;
    foreach($participantes as $pp) {
        $nmVal = $pp->nota_modulo !== null ? (float) $pp->nota_modulo : 0;
        if ($nmVal > 0) {
            ($nmVal >= 71) ? $aprobadosMod++ : $reprobadosMod++;
        }
    }
    $puedeNivelacion = !$sinNotas && $participantes->contains(fn($p) => $p->nota_modulo !== null && (float) $p->nota_modulo < 71);
    $participantesNivelacion = $puedeNivelacion ? $participantes->filter(fn($p) => $p->nota_modulo !== null && (float) $p->nota_modulo < 71)->values() : collect();
    $sinNivelacion = !$sinNotas && $participantes->filter(fn($p) => $p->nota_nivelacion !== null && (float) $p->nota_nivelacion > 0)->count() === 0;
    $hayNivelacion = $participantes->contains(fn($p) => $p->nota_nivelacion !== null && (float) $p->nota_nivelacion > 0);
    $mostrarBtnNivelacion = $puedeNivelacion || $hayNivelacion;
    if ($mostrarBtnNivelacion && $participantesNivelacion->isEmpty()) {
        $participantesNivelacion = $participantes->filter(fn($p) => $p->nota_nivelacion !== null && (float) $p->nota_nivelacion > 0)->values();
    }
@endphp
<div class="info-card">
    <div class="card-header">
        <span class="section-icon bg-soft-success text-success"><i class="ri-file-list-3-line"></i></span>
        <h5 class="card-title mb-0 fs-14 fw-semibold">Notas de Estudiantes</h5>
        <span class="badge bg-soft-success text-success fs-11 ms-2">{{ $inscritosCount }} participantes</span>
        @if($puedeGestionarModulo && $todasSesionesDesarrolladas && $sinNotas)
            <button type="button" class="btn btn-sm btn-success ms-auto d-inline-flex align-items-center gap-1" id="btnRegistrarNotas">
                <i class="ri-add-circle-line"></i> Registrar Notas
            </button>
        @elseif($puedeGestionarModulo && !$sinNotas && !$hayNivelacion)
            <button type="button" class="btn btn-sm btn-primary ms-auto d-inline-flex align-items-center gap-1" id="btnRegistrarNotas">
                <i class="ri-edit-line"></i> Editar Notas
            </button>
        @endif
        @if($puedeGestionarModulo && $mostrarBtnNivelacion)
            <button type="button" class="btn btn-sm btn-warning ms-auto d-inline-flex align-items-center gap-1" id="btnRegistrarNivelacion">
                <i class="ri-refund-2-line"></i> {{ $hayNivelacion ? 'Editar' : 'Registrar' }} Nivelación (<span id="nivelacionCount">{{ $participantesNivelacion->count() }}</span>)
            </button>
        @endif
        <a href="{{ route('admin.programas.modulo-pdf', $modulo->id) }}" class="btn btn-sm btn-pdf d-inline-flex align-items-center gap-1" target="_blank">
            <i class="ri-file-pdf-2-line"></i> Notas
        </a>
        @if($hayNivelacion)
        <a href="{{ route('admin.programas.modulo-pdf-nivelacion', $modulo->id) }}" class="btn btn-sm btn-pdf d-inline-flex align-items-center gap-1" target="_blank">
            <i class="ri-file-pdf-2-line"></i> Nivelación
        </a>
        @endif
    </div>
    <div class="card-body">
        @if(count($participantes) > 0)
            @php
                $hayNotaModulo = !$sinNotas;
                $hayNotaNivelacion = $participantes->contains(fn($p) => $p->nota_nivelacion !== null);
                $hayNivelacionPago = $participantes->contains(fn($p) => $p->nota_nivelacion_pago !== null && (float) $p->nota_nivelacion_pago > 0);
            @endphp
            @if($puedeGestionarModulo && $sinNotas && $todasSesionesDesarrolladas)
            <div class="text-end mb-3">
                <button type="button" class="btn btn-success d-inline-flex align-items-center gap-1" id="btnRegistrarNotasEmpty">
                    <i class="ri-add-circle-line"></i> Registrar Notas
                </button>
            </div>
            @endif
            <div class="stats-row">
                <span class="badge bg-soft-success text-success fs-11">Aprobados: {{ $aprobadosMod }}</span>
                <span class="badge bg-soft-danger text-danger fs-11">Reprobados: {{ $reprobadosMod }}</span>
                <span class="badge bg-soft-info text-info fs-11">Con nota: {{ $conNota }}</span>
            </div>
            <div class="table-responsive">
                <table class="sessions-table table align-middle">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            <th style="width:12%">CI</th>
                            <th>Apellidos, Nombres</th>
                            @if($hayNotaModulo)
                            <th style="width:13%" class="text-center">Nota Módulo</th>
                            @endif
                            @if($hayNotaNivelacion)
                            <th style="width:13%" class="text-center">Nota Nivelación</th>
                            @endif
                            @if($hayNivelacionPago)
                            <th style="width:13%" class="text-center">Nivelación de Pago</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participantes as $p)
                        @php
                            $nm = $p->nota_modulo !== null ? (float) $p->nota_modulo : null;
                            $nn = $p->nota_nivelacion !== null ? (float) $p->nota_nivelacion : null;
                            $bgMod = $nm !== null ? ($nm >= 71 ? 'bg-soft-success-badge' : 'bg-soft-danger-badge') : '';
                            $txtMod = $nm !== null ? ($nm >= 71 ? 'text-success' : 'text-danger') : 'text-muted';
                            $necesitaNiv = $nm !== null && $nm < 71;
                            $mostrarNiv = $hayNotaNivelacion && ($necesitaNiv || $nn !== null);
                            $bgNiv = $mostrarNiv && $nn !== null ? ($nn >= 71 ? 'bg-soft-success-badge' : 'bg-soft-danger-badge') : '';
                            $txtNiv = $mostrarNiv && $nn !== null ? ($nn >= 71 ? 'text-success' : 'text-danger') : 'text-muted';
                            $nnPago = $p->nota_nivelacion_pago !== null ? (float) $p->nota_nivelacion_pago : null;
                            $bgNivPago = $nnPago !== null && $nnPago > 0 ? ($nnPago >= 71 ? 'bg-soft-secondary' : 'bg-soft-danger') : '';
                            $txtNivPago = $nnPago !== null && $nnPago > 0 ? ($nnPago >= 71 ? 'text-dark' : 'text-danger') : 'text-muted';
                        @endphp
                        <tr>
                            <td class="text-center"><span class="session-num">{{ $loop->iteration }}</span></td>
                            <td class="fw-medium">{{ $p->ci }}</td>
                            <td class="fw-medium">{{ $p->apellidos }}, {{ $p->nombres }}</td>
                            @if($hayNotaModulo)
                            <td class="text-center">
                                <span class="nota-cell {{ $bgMod }} {{ $txtMod }}">{{ $nm !== null ? number_format($nm, 2) : '—' }}</span>
                            </td>
                            @endif
                            @if($hayNotaNivelacion)
                            <td class="text-center">
                                <span class="nota-cell {{ $bgNiv }} {{ $txtNiv }}">{{ $mostrarNiv && $nn !== null ? number_format($nn, 2) : '—' }}</span>
                            </td>
                            @endif
                            @if($hayNivelacionPago)
                            <td class="text-center">
                                <span class="nota-cell {{ $bgNivPago }} {{ $txtNivPago }}">{{ $nnPago !== null && $nnPago > 0 ? number_format($nnPago, 2) : '—' }}</span>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-user-search-line"></i></div>
                <h6 class="text-dark fw-bold mb-1">Sin participantes inscritos</h6>
                <p class="text-muted mb-0">No hay participantes inscritos en este módulo.</p>
            </div>
        @endif
    </div>
</div>

{{-- Modal Registrar / Editar Notas --}}
@if($puedeGestionarModulo && (($todasSesionesDesarrolladas && $sinNotas) || (!$sinNotas && !$hayNivelacion)))
<div class="modal fade" id="modalRegistrarNotas" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-file-list-3-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">{{ $sinNotas ? 'Registrar' : 'Editar' }} Notas</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $modulo->nombre }} · {{ $inscritosCount }} participantes</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size:.68rem;">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th style="width:12%">CI</th>
                                <th>Apellidos, Nombres</th>
                                <th style="width:18%;" class="text-center">Nota Módulo <small class="text-muted d-block fs-10 fw-normal">(0-100)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participantes as $p)
                            @php $notaExistente = $p->nota_modulo !== null ? number_format((float) $p->nota_modulo, 2) : ''; @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $p->ci }}</td>
                                <td class="fw-medium">{{ $p->apellidos }}, {{ $p->nombres }}</td>
                                <td class="text-center">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm modal-nota-input" data-mp-id="{{ $p->mp_id }}" data-part-id="{{ $p->id }}" data-tipo="nota_modulo" value="{{ $notaExistente }}" placeholder="0.00" style="width:110px;margin:0 auto;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cancelar</button>
                <button type="button" class="btn btn-{{ $sinNotas ? 'success' : 'primary' }}" id="btnGuardarNotasModal"><i class="ri-save-3-line"></i> {{ $sinNotas ? 'Guardar' : 'Actualizar' }} Notas</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Registrar / Editar Nivelación --}}
@if($puedeGestionarModulo && $mostrarBtnNivelacion)
<div class="modal fade" id="modalRegistrarNivelacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f7b84b 0%,#ffb547 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-refund-2-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">{{ $hayNivelacion ? 'Editar' : 'Registrar' }} Nota de Nivelación</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $modulo->nombre }} · {{ $participantesNivelacion->count() }} {{ $hayNivelacion ? 'con nivelación registrada' : 'participantes con nota &lt; 71' }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size:.68rem;">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th style="width:12%">CI</th>
                                <th>Apellidos, Nombres</th>
                                <th style="width:12%;" class="text-center">Nota Módulo</th>
                                <th style="width:18%;" class="text-center">Nota Nivelación <small class="text-muted d-block fs-10 fw-normal">(0-100)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participantesNivelacion as $p)
                            @php $nm = (float) $p->nota_modulo; @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $p->ci }}</td>
                                <td class="fw-medium">{{ $p->apellidos }}, {{ $p->nombres }}</td>
                                <td class="text-center">
                                    <span class="nota-cell bg-soft-danger-badge text-danger">{{ number_format($nm, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm modal-nivelacion-input" data-mp-id="{{ $p->mp_id }}" value="{{ $p->nota_nivelacion !== null ? number_format((float)$p->nota_nivelacion, 2) : '' }}" placeholder="0.00" style="width:110px;margin:0 auto;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarNivelacionModal"><i class="ri-save-3-line"></i> {{ $hayNivelacion ? 'Actualizar' : 'Guardar' }} Nivelación</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal: Cambiar Estado de Sesión --}}
<div class="modal fade" id="modalSesion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-event-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Estado de la Sesión</h5>
                        <small class="modal-subtitle" id="mSesionSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSesionEstado">
                @csrf
                <input type="hidden" id="mSesion-horarioId">
                <div class="modal-body">
                    <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Nuevo estado</label>
                    <div class="estado-picker mb-3" id="estadoPicker">
                        <div class="estado-option" data-estado="Confirmado"><i class="ri-shield-check-line"></i><div class="nombre">Confirmado</div></div>
                        <div class="estado-option" data-estado="Programado"><i class="ri-calendar-line"></i><div class="nombre">Programado</div></div>
                        <div class="estado-option" data-estado="Postergado"><i class="ri-time-line"></i><div class="nombre">Postergado</div></div>
                        <div class="estado-option" data-estado="Desarrollado"><i class="ri-checkbox-circle-line"></i><div class="nombre">Desarrollado</div></div>
                    </div>

                    <div id="postergadoWrap" style="display:none;" class="mb-3">
                        <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Nueva fecha</label>
                        <input type="date" id="mSesion-nuevaFecha" class="form-control">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-12 text-uppercase text-muted">Observaciones</label>
                        <textarea id="mSesion-observaciones" rows="3" class="form-control" placeholder="Notas sobre la sesión..."></textarea>
                    </div>

                    <div class="alert alert-soft-info mt-3 mb-0 d-flex align-items-start gap-2 fs-13" id="msgDesarrollado" style="display:none;">
                        <i class="ri-information-line fs-16"></i>
                        <div>Al marcar como <b>Desarrollado</b>, deberás registrar la asistencia de los estudiantes.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnContinuarSesion">
                        <i class="ri-arrow-right-line"></i> <span id="btnContinuarTxt">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Editar Fecha y Hora --}}
<div class="modal fade" id="modalEditarFechaSesion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarFechaSesion">
                @csrf
                <input type="hidden" name="horario_id" id="edit-fecha-horario-id">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-14">
                                <i class="ri-calendar-line"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold">Editar Fecha y Hora</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Fecha</label>
                        <input type="date" name="fecha_desarrollo" id="edit-fecha-fecha" class="form-control">
                        <small class="text-muted" id="edit-fecha-formateada"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Hora Inicio</label>
                        <input type="time" name="hora_inicio" id="edit-fecha-hora-inicio" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Hora Fin</label>
                        <input type="time" name="hora_fin" id="edit-fecha-hora-fin" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarFechaSesion">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Asistencia --}}
<div class="modal fade" id="modalAsistencia" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-user-voice-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Registro de Asistencia</h5>
                        <small class="modal-subtitle" id="mAsistSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="asistencia-toolbar">
                    <input type="text" class="asist-search" id="asistSearch" placeholder="Buscar por CI, nombres o apellidos...">
                    <button type="button" class="bulk-action" data-bulk="Presente"><i class="ri-check-line text-success"></i> Todos presentes</button>
                    <button type="button" class="bulk-action" data-bulk="Ausente"><i class="ri-close-line text-danger"></i> Todos ausentes</button>
                </div>
                <div class="alert alert-soft-warning d-flex align-items-start gap-2 fs-13 mb-3 py-2" id="mAsistEmpty" style="display:none;">
                    <i class="ri-error-warning-line fs-16"></i>
                    <div>No hay participantes inscritos en este módulo.</div>
                </div>
                <div class="asist-bajas-hint" id="asistBajasHint" style="display:none;">
                    <i class="ri-user-unfollow-line"></i>
                    <span>0</span> participante(s) dado(s) de baja en rojo: no se les registra asistencia ni entran en «Todos presentes / ausentes».
                </div>
                <div class="table-responsive">
                    <table class="asistencia-table table">
                        <thead>
                            <tr>
                                <th style="width:50px;" class="text-center">#</th>
                                <th>Participante</th>
                                <th style="width:160px;">Asistencia</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody id="asistenciaList"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarAsistencia">
                    <i class="ri-save-3-line"></i> Guardar Asistencia
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModuloMoodle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-book-2-line me-1"></i>Enlace Moodle del Módulo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModuloMoodle">
                @csrf
                <div class="modal-body" style="padding:1rem;">
                    <div class="mb-2">
                        <label class="form-label fs-12 text-muted">URL del curso en Moodle</label>
                        <input type="url" name="moodle_link" id="edit-modulo-moodle-link" class="form-control" maxlength="500" placeholder="https://moodle.ejemplo.com/course/...">
                    </div>
                    <small class="text-muted">Deja en blanco para eliminar el enlace.</small>
                </div>
                <div class="modal-footer" style="padding:.65rem 1rem;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnGuardarModuloMoodle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: WhatsApp a ausentes --}}
<div class="modal fade" id="modalWhatsAppAusentes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#25d366 0%,#128C7E 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-whatsapp-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold" style="color:#fff;">WhatsApp - Participantes Ausentes</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="waAusentesSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.25rem;background:#fafbfd;">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="waAusentesSelectAll" checked>
                            <label class="form-check-label fw-semibold fs-13" for="waAusentesSelectAll">Seleccionar todos</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1" id="waAusentesEnviarTodos">
                        <i class="ri-whatsapp-line"></i> Enviar a seleccionados
                    </button>
                </div>
                <div class="table-responsive" style="max-height:350px;overflow-y:auto;">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size:.65rem;">
                            <tr>
                                <th style="width:40px;" class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="waAusentesSelectAllHead" checked>
                                    </div>
                                </th>
                                <th style="width:40px;" class="text-center">#</th>
                                <th>Participante</th>
                                <th style="width:120px;" class="text-center">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody id="waAusentesBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
                <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1" id="waAusentesEnviarFooter">
                    <i class="ri-whatsapp-line"></i> Enviar a seleccionados
                </button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /acad-page --}}
@endsection

@if($puedeGestionarModulo)
{{-- Modal Cambiar Estado del Módulo --}}
<div class="modal fade" id="modalEditarModuloDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarModuloDetalle">
                @csrf
                <input type="hidden" name="modulo_id" id="edit-modulo-id-detalle">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-14">
                                <i class="ri-flag-line"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold">Cambiar Estado</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Estado</label>
                        <select name="desarrollo" class="form-select" id="edit-modulo-desarrollo-detalle">
                            <option value="No Inicio">No Inicio</option>
                            <option value="En Desarrollo">En Desarrollo</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Suspendido">Suspendido</option>
                            <option value="Concluido">Concluido</option>
                        </select>
                    </div>
                    <div class="mb-0" id="edit-modulo-fecha-wrap-detalle" style="display:none;">
                        <label class="form-label text-muted fw-medium fs-12">Fecha de conclusión</label>
                        <input type="date" name="fecha_conclusion" id="edit-modulo-fecha-conclusion-detalle" class="form-control">
                        <small class="text-muted mt-1 d-block"><i class="ri-information-line"></i> La fecha límite para nivelar será 2 meses después de esta fecha.</small>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarModuloDetalle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('js')
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = @json(csrf_token());
    var urlHorarioEstado  = @json(route("admin.programas.horario-estado"));
    var urlAsistDataBase  = @json(url("admin/programas/horario"));
    var urlStats = @json(url("admin/programas/modulo/" . $modulo->id . "/asistencia-stats"));
    var PROGRAMA_NOMBRE = @json($programa->nombre);
    var MODULO_NOMBRE = @json($modulo->nombre);
    var ADMIN_NOMBRE = @json(Auth::user()->fullname ?? Auth::user()->username ?? "Administrador");
    var PUEDE_GESTIONAR_MODULO = @json($puedeGestionarModulo);
    var PUEDE_WHATSAPP_PROGRAMA = @json($puedeWhatsappPrograma);

    const estadoColors = {
        'Confirmado':   '#405189',
        'Programado':   '#878a99',
        'Desarrollado': '#0ab39c',
        'Postergado':   '#f7b84b',
    };
    const estadoIcons = {
        'Confirmado':   'ri-shield-check-line',
        'Programado':   'ri-calendar-line',
        'Desarrollado': 'ri-checkbox-circle-line',
        'Postergado':   'ri-time-line',
    };

    let currentHorarioId = null;
    let currentSelectedEstado = null;
    let currentSesionInfo = '';

    const modalSesionEl = document.getElementById('modalSesion');
    const modalAsistEl  = document.getElementById('modalAsistencia');
    const modalSesion = new bootstrap.Modal(modalSesionEl);
    const modalAsistencia = new bootstrap.Modal(modalAsistEl);

    // Abrir modal cambio de estado (o asistencia directamente si ya está Desarrollado)
    document.querySelectorAll('.btn-cambiar-estado').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentHorarioId = this.dataset.horarioId;
            currentSesionInfo = this.dataset.fecha + ' · ' + this.dataset.hora;
            document.getElementById('mSesion-horarioId').value = currentHorarioId;
            document.getElementById('mSesionSubtitle').textContent = currentSesionInfo;
            document.getElementById('mSesion-observaciones').value = this.dataset.observaciones === '—' ? '' : (this.dataset.observaciones || '');
            document.getElementById('mSesion-nuevaFecha').value = '';
            selectEstado(this.dataset.estado);
            modalSesion.show();
        });
    });

    // Abrir directamente la asistencia
    document.querySelectorAll('.btn-ver-asistencia').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentHorarioId = this.dataset.horarioId;
            currentSesionInfo = this.dataset.fecha + ' · ' + this.dataset.hora;
            currentSelectedEstado = 'Desarrollado';
            document.getElementById('mSesion-observaciones').value = this.dataset.observaciones === '—' ? '' : (this.dataset.observaciones || '');
            abrirAsistencia(currentHorarioId, currentSesionInfo);
        });
    });

    // WhatsApp a ausentes: abrir modal con lista
    let waAusentesData = [];
    let waAusentesSesionInfo = '';

    document.querySelectorAll('.btn-whatsapp-ausentes').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const horarioId = this.dataset.horarioId;
            waAusentesSesionInfo = this.dataset.fecha + ' · ' + this.dataset.hora;
            const btnEl = this;
            btnEl.disabled = true;
            btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(urlAsistDataBase + '/' + horarioId + '/asistencia', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.participantes) {
                    showToast('No se pudo cargar la asistencia', 'danger');
                    return;
                }
                const ausentes = data.participantes.filter(function (p) {
                    return p.asistio === 'Ausente' && p.celular && !p.baja; // sin dados de baja
                });
                if (!ausentes.length) {
                    showToast('No hay participantes ausentes con WhatsApp (sin contar los dados de baja)', 'warning');
                    return;
                }
                waAusentesData = ausentes;
                abrirModalWAusentes(ausentes, waAusentesSesionInfo);
            })
            .catch(function () {
                showToast('Error de conexión', 'danger');
            })
            .finally(function () {
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="ri-whatsapp-line"></i> Ausentes WhatsApp';
            });
        });
    });

    function abrirModalWAusentes(ausentes, sesionInfo) {
        document.getElementById('waAusentesSubtitle').textContent = sesionInfo + ' — ' + ausentes.length + ' ausente(s)';
        const tbody = document.getElementById('waAusentesBody');
        let html = '';
        ausentes.forEach(function (p, i) {
            const mensajeIndiv = 'Estimado/a ' + p.apellidos + ', ' + p.nombres +
                ', registró inasistencia en la sesión del ' + sesionInfo +
                ' del programa ' + PROGRAMA_NOMBRE +
                ', módulo ' + MODULO_NOMBRE + '.' +
                '\n\nPor favor, comuníquese con su responsable para justificar su falta.' +
                '\n\nÁrea Académica ESAM Sucre Central\n' +
                'Enviado por: ' + ADMIN_NOMBRE;
            const waLink = 'https://wa.me/' + p.celular + '?text=' + encodeURIComponent(mensajeIndiv);
            html += '<tr>' +
                '<td class="text-center"><div class="form-check"><input class="form-check-input wa-ausente-check" type="checkbox" checked data-idx="' + i + '"></div></td>' +
                '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                '<td><div class="fw-semibold">' + escHtml(p.apellidos) + ', ' + escHtml(p.nombres) + '</div><small class="text-muted">CI: ' + escHtml(p.ci) + ' · ' + p.celular + '</small></td>' +
                '<td class="text-center"><a href="' + waLink + '" target="_blank" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1" style="width:fit-content;margin:0 auto;"><i class="ri-whatsapp-line"></i> Enviar</a></td>' +
            '</tr>';
        });
        tbody.innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalWhatsAppAusentes')).show();
    }

    function waAusentesEnviarSeleccionados() {
        const checks = document.querySelectorAll('.wa-ausente-check:checked');
        if (!checks.length) {
            showToast('Selecciona al menos un participante', 'warning');
            return;
        }
        checks.forEach(function (ch, i) {
            const idx = parseInt(ch.dataset.idx);
            const p = waAusentesData[idx];
            if (!p) return;
            const mensaje = 'Estimado/a ' + p.apellidos + ', ' + p.nombres +
                ', registró inasistencia en la sesión del ' + waAusentesSesionInfo +
                ' del programa ' + PROGRAMA_NOMBRE +
                ', módulo ' + MODULO_NOMBRE + '.' +
                '\n\nPor favor, comuníquese con su responsable para justificar su falta.' +
                '\n\nÁrea Académica ESAM Sucre Central\n' +
                'Enviado por: ' + ADMIN_NOMBRE;
            const waLink = 'https://wa.me/' + p.celular + '?text=' + encodeURIComponent(mensaje);
            setTimeout(function () {
                window.open(waLink, '_blank');
            }, i * 500);
        });
        bootstrap.Modal.getInstance(document.getElementById('modalWhatsAppAusentes')).hide();
        showToast('Abriendo WhatsApp para ' + checks.length + ' participante(s)', 'success');
    }

    document.getElementById('waAusentesEnviarTodos').addEventListener('click', waAusentesEnviarSeleccionados);
    document.getElementById('waAusentesEnviarFooter').addEventListener('click', waAusentesEnviarSeleccionados);

    document.getElementById('waAusentesSelectAll').addEventListener('change', function () {
        document.querySelectorAll('.wa-ausente-check').forEach(function (ch) { ch.checked = this.checked; }, this);
        document.getElementById('waAusentesSelectAllHead').checked = this.checked;
    });
    document.getElementById('waAusentesSelectAllHead').addEventListener('change', function () {
        document.querySelectorAll('.wa-ausente-check').forEach(function (ch) { ch.checked = this.checked; }, this);
        document.getElementById('waAusentesSelectAll').checked = this.checked;
    });

    document.querySelectorAll('#estadoPicker .estado-option').forEach(function (op) {
        op.addEventListener('click', function () { selectEstado(this.dataset.estado); });
    });

    function selectEstado(estado) {
        currentSelectedEstado = estado;
        document.querySelectorAll('#estadoPicker .estado-option').forEach(o => {
            o.classList.toggle('is-active', o.dataset.estado === estado);
        });
        document.getElementById('postergadoWrap').style.display = estado === 'Postergado' ? 'block' : 'none';
        document.getElementById('msgDesarrollado').style.display = estado === 'Desarrollado' ? 'flex' : 'none';
        document.getElementById('btnContinuarTxt').textContent = estado === 'Desarrollado' ? 'Continuar a Asistencia' : 'Guardar';
    }

    document.getElementById('btnContinuarSesion').addEventListener('click', function () {
        if (!currentSelectedEstado) {
            showToast('Selecciona un estado', 'warning');
            return;
        }
        if (currentSelectedEstado === 'Desarrollado') {
            modalSesion.hide();
            abrirAsistencia(currentHorarioId, currentSesionInfo);
            return;
        }
        guardarEstadoSimple();
    });

    function guardarEstadoSimple() {
        const btn = document.getElementById('btnContinuarSesion');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const fd = new FormData();
        fd.append('_token', csrfToken);
        fd.append('horario_id', currentHorarioId);
        fd.append('estado', currentSelectedEstado);
        fd.append('observaciones', document.getElementById('mSesion-observaciones').value || '');
        if (currentSelectedEstado === 'Postergado') {
            fd.append('nueva_fecha', document.getElementById('mSesion-nuevaFecha').value || '');
        }

        fetch(urlHorarioEstado, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                actualizarFilaSesion(currentHorarioId, currentSelectedEstado, document.getElementById('mSesion-observaciones').value);
                modalSesion.hide();
                showToast(data.message || 'Estado actualizado', 'success');
                if (data.nuevo_horario) {
                    setTimeout(() => location.reload(), 800);
                }
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión', 'danger'))
        .finally(() => { btn.disabled = false; btn.innerHTML = original; });
    }

    // ========= ASISTENCIA =========
    let participantesCache = [];

    function abrirAsistencia(horarioId, info) {
        document.getElementById('mAsistSubtitle').textContent = info;
        document.getElementById('asistenciaList').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Cargando...</td></tr>';
        document.getElementById('mAsistEmpty').style.display = 'none';
        document.getElementById('asistSearch').value = '';
        modalAsistencia.show();

        fetch(urlAsistDataBase + '/' + horarioId + '/asistencia', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('asistenciaList').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error al cargar</td></tr>';
                return;
            }
            participantesCache = data.participantes || [];
            renderAsistencia(participantesCache);
        })
        .catch(() => {
            document.getElementById('asistenciaList').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error de conexión</td></tr>';
        });
    }

    function renderAsistencia(parts) {
        const list = document.getElementById('asistenciaList');
        if (!parts.length) {
            list.innerHTML = '';
            document.getElementById('mAsistEmpty').style.display = 'flex';
            return;
        }
        document.getElementById('mAsistEmpty').style.display = 'none';
        const opciones = ['Presente', 'Ausente', 'Tardanza', 'Justificado'];
        let html = '';
        parts.forEach(function (p, i) {
            const ini = ((p.nombres || '').charAt(0) + (p.apellidos || '').charAt(0)).toUpperCase();
            const baja = !!p.baja;
            // A los dados de baja no se les registra asistencia (salvo que se habilite a mano).
            const sel = p.asistio || (baja ? '' : 'Presente');
            const obs = p.observacion || '';
            html += '<tr data-part-id="' + p.id + '"' + (baja ? ' class="asist-baja" data-baja="1"' : '') +
                ' data-search="' + (p.ci + ' ' + p.nombres + ' ' + p.apellidos).toLowerCase() + '">' +
                '<td class="text-center"><span class="session-num">' + (i + 1) + '</span></td>' +
                '<td><div class="d-flex align-items-center gap-2">' +
                    '<span class="part-avatar">' + escHtml(ini) + '</span>' +
                    '<div style="min-width:0;">' +
                        '<div class="fw-semibold ' + (baja ? 'text-danger' : 'text-dark') + '">' + escHtml(p.apellidos) + ', ' + escHtml(p.nombres) + '</div>' +
                        '<div class="text-muted fs-11"><i class="ri-passport-line"></i> CI: ' + escHtml(p.ci) + '</div>' +
                        (baja ? '<span class="asist-baja-tag"><i class="ri-user-unfollow-line"></i> ' + escHtml(p.baja) + '</span>' : '') +
                    '</div>' +
                '</div></td>' +
                '<td>' +
                    '<select class="form-select form-select-sm asistio-select" data-val="' + sel + '"' + (baja ? ' disabled' : '') + '>' +
                        (baja && !p.asistio ? '<option value="" selected>— De baja —</option>' : '') +
                        opciones.map(o => '<option value="' + o + '"' + (o === sel ? ' selected' : '') + '>' + o + '</option>').join('') +
                    '</select>' +
                    (baja ? '<button type="button" class="btn btn-link btn-sm p-0 mt-1 fs-11 asist-habilitar" title="Por ejemplo, si la baja fue posterior a esta sesión">Registrar igual</button>' : '') +
                '</td>' +
                '<td><input type="text" class="obs-input" placeholder="Observación opcional" value="' + escHtml(obs) + '" maxlength="500"' + (baja ? ' disabled' : '') + '></td>' +
            '</tr>';
        });
        list.innerHTML = html;

        // Sync data-val al cambiar select (para el color)
        list.querySelectorAll('.asistio-select').forEach(function (sel) {
            sel.addEventListener('change', function () { this.dataset.val = this.value; });
        });
        // Habilitar a mano el registro de un dado de baja.
        list.querySelectorAll('.asist-habilitar').forEach(function (b) {
            b.addEventListener('click', function () {
                const tr = this.closest('tr');
                const sel = tr.querySelector('.asistio-select');
                const vacio = sel.querySelector('option[value=""]');
                if (vacio) vacio.remove();
                if (!sel.value) sel.value = 'Ausente';
                sel.dataset.val = sel.value;
                sel.disabled = false;
                tr.querySelector('.obs-input').disabled = false;
                this.remove();
            });
        });
        const nBajas = parts.filter(function (p) { return p.baja; }).length;
        const hint = document.getElementById('asistBajasHint');
        if (hint) {
            hint.style.display = nBajas ? 'block' : 'none';
            hint.querySelector('span').textContent = nBajas;
        }
    }

    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    }

    // Buscar
    document.getElementById('asistSearch').addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('#asistenciaList tr[data-search]').forEach(function (tr) {
            const match = !q || tr.dataset.search.includes(q);
            tr.classList.toggle('hidden-row', !match);
        });
    });

    // Bulk actions
    document.querySelectorAll('.bulk-action').forEach(function (b) {
        b.addEventListener('click', function () {
            const val = this.dataset.bulk;
            // Los dados de baja (select deshabilitado) no entran en las acciones masivas.
            document.querySelectorAll('#asistenciaList tr:not(.hidden-row) .asistio-select:not(:disabled)').forEach(function (sel) {
                sel.value = val;
                sel.dataset.val = val;
            });
        });
    });

    // Guardar asistencia (deja el modal abierto y refresca la lista)
    document.getElementById('btnGuardarAsistencia').addEventListener('click', function () {
        const rows = document.querySelectorAll('#asistenciaList tr[data-part-id]');
        if (rows.length === 0) { showToast('No hay participantes', 'warning'); return; }
        const asistencias = [];
        rows.forEach(function (tr) {
            // Dados de baja sin habilitar: no se registra (se conserva lo que ya hubiera).
            if (tr.querySelector('.asistio-select').disabled) return;
            asistencias.push({
                participante_id: parseInt(tr.dataset.partId),
                asistio: tr.querySelector('.asistio-select').value,
                observacion: tr.querySelector('.obs-input').value || null,
            });
        });
        if (asistencias.length === 0) { showToast('No hay participantes activos para registrar', 'warning'); return; }

        const btn = this;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch(urlAsistDataBase + '/' + currentHorarioId + '/asistencia', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                observaciones: document.getElementById('mSesion-observaciones').value || '',
                asistencias: asistencias,
            }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                actualizarFilaSesion(currentHorarioId, 'Desarrollado', document.getElementById('mSesion-observaciones').value);
                asegurarBotonAsistencia(currentHorarioId);
                // Refresca la lista con los datos persistidos (modal queda abierto)
                recargarAsistencia(currentHorarioId);
                loadStats();
                showToast('Asistencia guardada (' + data.total + ' participantes). Puedes seguir modificando.', 'success');
            } else {
                showToast(data.message || 'No se pudo guardar', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión', 'danger'))
        .finally(() => { btn.disabled = false; btn.innerHTML = original; });
    });

    function recargarAsistencia(horarioId) {
        fetch(urlAsistDataBase + '/' + horarioId + '/asistencia', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                participantesCache = data.participantes || [];
                renderAsistencia(participantesCache);
            }
        })
        .catch(() => {});
    }

    function asegurarBotonAsistencia(horarioId) {
        const cell = document.querySelector('[data-action-cell="' + horarioId + '"]');
        if (!cell) return;
        const btnEstado = cell.querySelector('.btn-cambiar-estado');
        const fecha = btnEstado ? btnEstado.dataset.fecha : '';
        const hora  = btnEstado ? btnEstado.dataset.hora : '';
        const obs   = btnEstado ? (btnEstado.dataset.observaciones || '') : '';

        if (!cell.querySelector('.btn-ver-asistencia')) {
            const newBtn = document.createElement('button');
            newBtn.className = 'btn-estado btn-ver-asistencia';
            newBtn.setAttribute('data-horario-id', horarioId);
            newBtn.setAttribute('data-fecha', fecha);
            newBtn.setAttribute('data-hora', hora);
            newBtn.setAttribute('data-observaciones', obs);
            newBtn.style.borderColor = '#b9e9df';
            newBtn.style.color = '#076e5e';
            newBtn.style.background = '#e7f7f3';
            newBtn.innerHTML = '<i class="ri-team-line"></i> Asistencia';
            newBtn.addEventListener('click', function () {
                currentHorarioId = horarioId;
                currentSesionInfo = fecha + ' · ' + hora;
                currentSelectedEstado = 'Desarrollado';
                document.getElementById('mSesion-observaciones').value = obs;
                abrirAsistencia(horarioId, currentSesionInfo);
            });
            cell.insertBefore(newBtn, cell.firstChild);
            if (btnEstado) btnEstado.classList.add('mt-1');
        }

        if (PUEDE_WHATSAPP_PROGRAMA && !cell.querySelector('.btn-whatsapp-ausentes')) {
            const waBtn = document.createElement('button');
            waBtn.className = 'btn-estado btn-whatsapp-ausentes mt-1';
            waBtn.setAttribute('data-horario-id', horarioId);
            waBtn.setAttribute('data-fecha', fecha);
            waBtn.setAttribute('data-hora', hora);
            waBtn.title = 'Enviar WhatsApp a participantes ausentes';
            waBtn.style.borderColor = '#b7e7c6';
            waBtn.style.color = '#127a3e';
            waBtn.style.background = '#ecfdf3';
            waBtn.innerHTML = '<i class="ri-whatsapp-line"></i> Ausentes WhatsApp';
            waBtn.addEventListener('click', function () {
                const btnEl = this;
                btnEl.disabled = true;
                btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(urlAsistDataBase + '/' + horarioId + '/asistencia', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success || !data.participantes) {
                        showToast('No se pudo cargar la asistencia', 'danger');
                        return;
                    }
                    const ausentes = data.participantes.filter(function (p) {
                        return p.asistio === 'Ausente' && p.celular && !p.baja; // sin dados de baja
                    });
                    if (!ausentes.length) {
                        showToast('No hay participantes ausentes con WhatsApp (sin contar los dados de baja)', 'warning');
                        return;
                    }
                    waAusentesData = ausentes;
                    waAusentesSesionInfo = fecha + ' · ' + hora;
                    abrirModalWAusentes(ausentes, waAusentesSesionInfo);
                })
                .catch(function () {
                    showToast('Error de conexión', 'danger');
                })
                .finally(function () {
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="ri-whatsapp-line"></i> Ausentes WhatsApp';
                });
            });
            cell.insertBefore(waBtn, cell.querySelector('.btn-cambiar-estado')?.nextSibling || null);
        }
    }

    function actualizarFilaSesion(horarioId, estado, observaciones) {
        const cell = document.querySelector('[data-estado-cell="' + horarioId + '"]');
        if (cell) {
            cell.dataset.estado = estado;
            cell.innerHTML = '<i class="' + (estadoIcons[estado] || 'ri-information-line') + '"></i>' + estado;
        }
        const obsCell = document.querySelector('[data-obs-cell="' + horarioId + '"]');
        if (obsCell) {
            obsCell.textContent = observaciones || '—';
            obsCell.title = observaciones || '';
        }
        const btn = document.querySelector('.btn-cambiar-estado[data-horario-id="' + horarioId + '"]');
        if (btn) {
            btn.dataset.estado = estado;
            btn.dataset.observaciones = observaciones || '';
        }
    }

    // ======= ESTADÍSTICAS =======
    let donutChart = null, barChart = null;

    function loadStats() {
        var loader = document.getElementById('asistStatsLoader');
        var content = document.getElementById('asistStatsContent');
        var empty = document.getElementById('asistStatsEmpty');
        loader.style.display = 'block';
        content.style.display = 'none';
        empty.style.display = 'none';

        var controller = new AbortController();
        var timeout = setTimeout(function () { controller.abort(); }, 15000);

        fetch(urlStats, {
            signal: controller.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) {
            clearTimeout(timeout);
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            loader.style.display = 'none';
            if (!data.success) {
                empty.style.display = 'block';
                return;
            }
            var r = data.resumen;
            if (!r || !r.total_registros) {
                empty.style.display = 'block';
                document.getElementById('asistStatsBadge').textContent = '0%';
                return;
            }
            content.style.display = 'block';

            document.getElementById('kpiPct').textContent = r.porcentaje_asistencia + '%';
            document.getElementById('kpiSes').textContent = r.sesiones_desarrolladas + ' / ' + r.total_sesiones;
            document.getElementById('kpiParts').textContent = r.total_participantes;
            document.getElementById('kpiReg').textContent = r.total_registros;
            document.getElementById('asistStatsBadge').textContent = r.porcentaje_asistencia + '% asistencia';

            try { renderDonut(r.distribucion); } catch (e) {}
            try { renderBar(data.por_sesion); } catch (e) {}
            try { renderPartsTable(data.por_participante); } catch (e) {}
        })
        .catch(function () {
            clearTimeout(timeout);
            loader.style.display = 'none';
            empty.style.display = 'block';
        });
    }

    function renderDonut(dist) {
        const series = [dist.Presente, dist.Ausente, dist.Tardanza, dist.Justificado];
        const opts = {
            series: series,
            chart: { type: 'donut', height: 280, fontFamily: 'Manrope, system-ui, sans-serif' },
            labels: ['Presente', 'Ausente', 'Tardanza', 'Justificado'],
            colors: ['#0d8574', '#b3243a', '#b7791f', '#1f6fbf'],
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, markers: { radius: 5 }, itemMargin: { horizontal: 8, vertical: 4 } },
            dataLabels: {
                enabled: true,
                formatter: v => v.toFixed(1) + '%',
                style: { fontSize: '11px', fontWeight: 700, colors: ['#ffffff'] }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 700,
                                color: '#878a99',
                                formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            },
                            value: { fontSize: '20px', fontWeight: 700 }
                        }
                    }
                }
            },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { y: { formatter: v => v + ' registros' } }
        };
        const el = document.getElementById('chartDonut');
        if (donutChart) donutChart.destroy();
        donutChart = new ApexCharts(el, opts);
        donutChart.render();
    }

    function renderBar(porSesion) {
        const categories = porSesion.map(s => s.label);
        const tooltipFechas = porSesion.map(s => s.dia + ' ' + s.fecha);
        const opts = {
            series: [
                { name: 'Presente', data: porSesion.map(s => s.Presente) },
                { name: 'Ausente', data: porSesion.map(s => s.Ausente) },
                { name: 'Tardanza', data: porSesion.map(s => s.Tardanza) },
                { name: 'Justificado', data: porSesion.map(s => s.Justificado) },
            ],
            chart: { type: 'bar', height: 280, stacked: true, toolbar: { show: false }, fontFamily: 'Manrope, system-ui, sans-serif' },
            colors: ['#0d8574', '#b3243a', '#b7791f', '#1f6fbf'],
            plotOptions: { bar: { borderRadius: 5, columnWidth: '52%' } },
            stroke: { width: 1, colors: ['#fff'] },
            dataLabels: { enabled: false },
            xaxis: {
                categories: categories,
                labels: { style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: { labels: { style: { fontSize: '11px' } } },
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, markers: { radius: 5 }, itemMargin: { horizontal: 8, vertical: 4 } },
            tooltip: {
                custom: function ({ series, dataPointIndex, w }) {
                    const f = tooltipFechas[dataPointIndex] || '';
                    let body = '';
                    w.config.series.forEach((s, i) => {
                        body += '<div><span style="color:' + w.config.colors[i] + ';font-weight:700;">' + s.name + ':</span> ' + series[i][dataPointIndex] + '</div>';
                    });
                    return '<div class="px-2 py-1"><div class="fw-bold text-capitalize mb-1">' + f + '</div>' + body + '</div>';
                }
            },
            grid: { borderColor: '#eef0f4', strokeDashArray: 4 }
        };
        const el = document.getElementById('chartBar');
        if (barChart) barChart.destroy();
        barChart = new ApexCharts(el, opts);
        barChart.render();
    }

    let partsData = [];
    const PARTS_PAGE_SIZE = 10;
    let partsCurrentPage = 1;

    function renderPartsTable(parts) {
        partsData = parts;
        partsCurrentPage = 1;
        renderPartsPage(1);
    }

    function renderPartsPage(page) {
        const tbody = document.getElementById('partsStatsBody');
        const pagEl = document.getElementById('partsPagination');
        const total = partsData.length;
        const totalPages = Math.ceil(total / PARTS_PAGE_SIZE) || 1;

        if (!total) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">Sin datos</td></tr>';
            pagEl.style.display = 'none';
            return;
        }
        pagEl.style.display = 'flex';

        const start = (page - 1) * PARTS_PAGE_SIZE;
        const end = Math.min(start + PARTS_PAGE_SIZE, total);
        const pageParts = partsData.slice(start, end);

        let html = '';
        pageParts.forEach(function (p, i) {
            const globalIdx = start + i + 1;
            const ini = p.nombre.split(',').map(s => (s.trim().charAt(0) || '').toUpperCase()).join('');
            const cls = p.porcentaje >= 80 ? 'is-high' : (p.porcentaje >= 60 ? 'is-mid' : 'is-low');
            const color = p.porcentaje >= 80 ? '#0ab39c' : (p.porcentaje >= 60 ? '#f7b84b' : '#f06548');
            html += '<tr>' +
                '<td class="text-center"><span class="session-num">' + globalIdx + '</span></td>' +
                '<td><div class="d-flex align-items-center gap-2">' +
                    '<span class="part-avatar">' + ini + '</span>' +
                    '<div style="min-width:0;">' +
                        '<div class="fw-semibold text-dark">' + escHtml(p.nombre) + '</div>' +
                        '<div class="text-muted fs-11">CI: ' + escHtml(p.ci) + '</div>' +
                    '</div>' +
                '</div></td>' +
                '<td class="text-center"><span class="mini-tag tag-pres">' + p.Presente + '</span></td>' +
                '<td class="text-center"><span class="mini-tag tag-aus">' + p.Ausente + '</span></td>' +
                '<td class="text-center"><span class="mini-tag tag-tar">' + p.Tardanza + '</span></td>' +
                '<td class="text-center"><span class="mini-tag tag-jus">' + p.Justificado + '</span></td>' +
                '<td class="text-center"><span class="mini-tag tag-sin">' + p.sin_registro + '</span></td>' +
                '<td>' +
                    '<div class="d-flex align-items-center justify-content-between gap-2">' +
                        '<span class="pct-text ' + cls + '">' + p.porcentaje + '%</span>' +
                    '</div>' +
                    '<div class="pct-bar"><div class="pct-bar-fill" style="transform:scaleX(' + (Math.max(0, Math.min(100, Number(p.porcentaje))) / 100) + ');background:' + color + ';"></div></div>' +
                '</td>' +
            '</tr>';
        });
        tbody.innerHTML = html;
        renderPartsPagination(page, totalPages, total);
    }

    function renderPartsPagination(current, totalPages, total) {
        const nav = document.getElementById('partsPageNav');
        const info = document.getElementById('partsPageInfo');
        const start = (current - 1) * PARTS_PAGE_SIZE + 1;
        const end = Math.min(current * PARTS_PAGE_SIZE, total);
        info.textContent = 'Mostrando ' + start + '–' + end + ' de ' + total;

        function li(label, cls, clickPage) {
            return '<li class="page-item' + (cls ? ' ' + cls : '') + '">' +
                '<a class="page-link" href="#" data-page="' + clickPage + '">' + label + '</a></li>';
        }

        let items = '';
        items += li('«', current === 1 ? 'disabled' : '', current - 1);
        for (let i = 1; i <= totalPages; i++) {
            items += li(i, i === current ? 'active' : '', i);
        }
        items += li('»', current === totalPages ? 'disabled' : '', current + 1);
        nav.innerHTML = items;
    }

    document.getElementById('partsPageNav').addEventListener('click', function (e) {
        const a = e.target.closest('a[data-page]');
        if (!a || a.parentElement.classList.contains('disabled')) return;
        e.preventDefault();
        renderPartsPage(parseInt(a.dataset.page));
    });

    document.getElementById('btnReloadStats').addEventListener('click', loadStats);

    // Carga inicial
    loadStats();

    // ======= REGISTRAR NOTAS (MODAL) =======
    @if($puedeGestionarModulo && (($todasSesionesDesarrolladas && $sinNotas) || (!$sinNotas && !$hayNivelacion)))
    var urlGuardarNotas = @json(route("admin.programas.modulo-notas", $modulo->id));
    const modalNotas = new bootstrap.Modal(document.getElementById('modalRegistrarNotas'));

    function abrirModalNotas() {
        modalNotas.show();
    }

    document.getElementById('btnRegistrarNotas').addEventListener('click', abrirModalNotas);
    const btnEmpty = document.getElementById('btnRegistrarNotasEmpty');
    if (btnEmpty) btnEmpty.addEventListener('click', abrirModalNotas);

    document.getElementById('btnGuardarNotasModal').addEventListener('click', function () {
        const inputs = document.querySelectorAll('#modalRegistrarNotas .modal-nota-input');
        const notas = {};
        inputs.forEach(function (inp) {
            const mpId = inp.dataset.mpId;
            const partId = inp.dataset.partId;
            const tipo = inp.dataset.tipo;
            if (!tipo) return;
            const key = mpId ? mpId : 'new_' + partId;
            if (!notas[key]) notas[key] = {};
            notas[key][tipo] = inp.value === '' ? '0' : inp.value;
        });

        const btn = this;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch(urlGuardarNotas, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notas: notas }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalNotas.hide();
                showToast(data.message || 'Notas guardadas correctamente', 'success');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                showToast(data.message || 'No se pudieron guardar las notas', 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = original; });
    });
    @endif

    @if($puedeGestionarModulo && $mostrarBtnNivelacion)
    var urlGuardarNivelacion = @json(route("admin.programas.modulo-nivelacion", $modulo->id));
    const modalNivelacion = new bootstrap.Modal(document.getElementById('modalRegistrarNivelacion'));

    document.getElementById('btnRegistrarNivelacion').addEventListener('click', function () {
        modalNivelacion.show();
    });

    document.getElementById('btnGuardarNivelacionModal').addEventListener('click', function () {
        const inputs = document.querySelectorAll('#modalRegistrarNivelacion .modal-nivelacion-input');
        const notas = {};
        inputs.forEach(function (inp) {
            const mpId = inp.dataset.mpId;
            if (!mpId) return;
            notas[mpId] = inp.value === '' ? '0' : inp.value;
        });

        const btn = this;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch(urlGuardarNivelacion, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notas: notas }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalNivelacion.hide();
                showToast(data.message || 'Nivelación guardada correctamente', 'success');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                showToast(data.message || 'No se pudieron guardar las notas de nivelación', 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = original; });
    });
    @endif

    // ======= Toast =======
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

    // ======= EDITAR FECHA / HORA (modal propio) =======
    var urlHorarioFecha = @json(route("admin.programas.horario-fecha"));

    document.querySelectorAll('.btn-editar-fecha-hora').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('edit-fecha-horario-id').value = this.dataset.horarioId;
            document.getElementById('edit-fecha-fecha').value = this.dataset.fecha || '';
            document.getElementById('edit-fecha-hora-inicio').value = this.dataset.horaInicio ? this.dataset.horaInicio.substring(0,5) : '';
            document.getElementById('edit-fecha-hora-fin').value = this.dataset.horaFin ? this.dataset.horaFin.substring(0,5) : '';

            const val = this.dataset.fecha || '';
            if (val) {
                const p = val.split('-');
                const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                document.getElementById('edit-fecha-formateada').textContent = parseInt(p[2]) + ' de ' + meses[parseInt(p[1]) - 1] + ' del ' + p[0];
            } else {
                document.getElementById('edit-fecha-formateada').textContent = '';
            }

            new bootstrap.Modal(document.getElementById('modalEditarFechaSesion')).show();
        });
    });

    document.getElementById('formEditarFechaSesion').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarFechaSesion');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch(urlHorarioFecha, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarFechaSesion')).hide();
                showToast(data.message || 'Fecha y hora actualizadas correctamente', 'success');
                setTimeout(() => location.reload(), 600);
            } else {
                showToast('Error: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión al guardar', 'danger'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
        });
    });

    // ======= ENLACE MOODLE DEL MÓDULO =======
    document.getElementById('moduloMoodleBadge')?.addEventListener('click', function (e) {
        e.preventDefault();
        const tieneLink = this.dataset.tieneLink === '1';
        const url = this.dataset.url;
        if (tieneLink && url && !e.shiftKey) {
            window.open(url, '_blank');
            return;
        }
        document.getElementById('edit-modulo-moodle-link').value = url || '';
        new bootstrap.Modal(document.getElementById('modalModuloMoodle')).show();
    });

    document.getElementById('formModuloMoodle')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarModuloMoodle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        const moduloId = {{ $modulo->id }};
        fetch('{{ route("admin.programas.modulo-moodle-link", ["modulo" => "_MID_"]) }}'.replace('_MID_', moduloId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalModuloMoodle')).hide();
                showToast(data.message || 'Enlace actualizado correctamente', 'success');
                const nuevoLink = document.getElementById('edit-modulo-moodle-link').value.trim();
                const badge = document.getElementById('moduloMoodleBadge');
                badge.dataset.url = nuevoLink;
                badge.dataset.tieneLink = nuevoLink ? '1' : '0';
                if (nuevoLink) {
                    badge.style.background = 'rgba(106,27,154,.25)';
                    badge.style.borderColor = 'rgba(106,27,154,.4)';
                    badge.innerHTML = '<i class="ri-external-link-line"></i><span>Moodle</span>';
                    badge.title = 'Ir a Moodle';
                } else {
                    badge.style.background = 'rgba(173,181,189,.25)';
                    badge.style.borderColor = 'rgba(173,181,189,.4)';
                    badge.innerHTML = '<i class="ri-add-line"></i><span>Moodle</span>';
                    badge.title = 'Agregar enlace Moodle';
                }
            } else {
                showToast('Error: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
        });
    });

    // Actualizar formato al cambiar la fecha
    document.getElementById('edit-fecha-fecha').addEventListener('change', function () {
        const val = this.value;
        if (val) {
            const p = val.split('-');
            const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            document.getElementById('edit-fecha-formateada').textContent = parseInt(p[2]) + ' de ' + meses[parseInt(p[1]) - 1] + ' del ' + p[0];
        } else {
            document.getElementById('edit-fecha-formateada').textContent = '';
        }
    });

    // ---- Cambiar estado del módulo (detalle) ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-editar-modulo-detalle');
        if (!btn) return;

        const modalElDetalle = document.getElementById('modalEditarModuloDetalle');
        if (!modalElDetalle) return;

        const moduloId = btn.dataset.moduloId;
        if (!moduloId) return;

        document.getElementById('edit-modulo-id-detalle').value = moduloId;
        document.querySelector('#formEditarModuloDetalle select[name="desarrollo"]').value = btn.dataset.desarrollo;
        document.getElementById('edit-modulo-fecha-conclusion-detalle').value = btn.dataset.fechaConclusion || '';

        const selDevDetalle = document.getElementById('edit-modulo-desarrollo-detalle');
        const fechaWrapDetalle = document.getElementById('edit-modulo-fecha-wrap-detalle');
        if (fechaWrapDetalle) {
            fechaWrapDetalle.style.display = selDevDetalle.value === 'Concluido' ? '' : 'none';
        }

        new bootstrap.Modal(modalElDetalle).show();
    });

    const selDevDetalle = document.getElementById('edit-modulo-desarrollo-detalle');
    const fechaWrapDetalle = document.getElementById('edit-modulo-fecha-wrap-detalle');
    if (selDevDetalle && fechaWrapDetalle) {
        selDevDetalle.addEventListener('change', function () {
            fechaWrapDetalle.style.display = this.value === 'Concluido' ? '' : 'none';
            if (this.value === 'Concluido' && !document.getElementById('edit-modulo-fecha-conclusion-detalle').value) {
                document.getElementById('edit-modulo-fecha-conclusion-detalle').value = new Date().toISOString().split('T')[0];
            }
        });
    }

    const formEditarModuloDetalle = document.getElementById('formEditarModuloDetalle');
    if (formEditarModuloDetalle) formEditarModuloDetalle.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarModuloDetalle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch('{{ route("admin.programas.modulo-estado") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarModuloDetalle')).hide();
                showToast(data.message || 'Estado actualizado correctamente', 'success');
                const badge = document.getElementById('moduloDevBadge');
                if (badge) {
                    const nuevo = document.querySelector('#formEditarModuloDetalle select[name="desarrollo"]').value;
                    const devIconMap = { 'Concluido':'ri-checkbox-circle-line', 'En Desarrollo':'ri-loader-4-line', 'No Inicio':'ri-time-line', 'Pendiente':'ri-hourglass-line', 'Suspendido':'ri-close-circle-line' };
                    badge.className = 'hero-chip btn-editar-modulo-detalle';
                    badge.style.color = '#fff';
                    badge.innerHTML = '<i class="' + (devIconMap[nuevo] || 'ri-information-line') + '"></i>' + nuevo;
                    badge.dataset.desarrollo = nuevo;
                }
            } else {
                showToast('Error: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
        });
    });
});
</script>
@endpush
