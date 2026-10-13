@extends('admin.layouts.master')

@section('title', 'Responsables - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero ===== */
.resp-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.resp-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.resp-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.resp-hero > * { position: relative; z-index: 1; }
.resp-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.resp-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.resp-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.resp-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.resp-hero .breadcrumb-item, .resp-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.resp-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }

/* ===== Master-detail layout ===== */
.resp-layout { display: flex; gap: 1rem; align-items: flex-start; }
.resp-master {
    flex-shrink: 0;
    width: 320px;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    overflow: hidden;
    position: sticky; top: 80px;
    max-height: calc(100vh - 100px);
    display: flex; flex-direction: column;
}
[data-layout-mode="dark"] .resp-master { background: #1d2125; border-color: #2a2f34; }
.resp-detail { flex: 1; min-width: 0; }
@media (max-width: 991.98px) {
    .resp-layout { flex-direction: column; }
    .resp-master { width: 100%; position: static; max-height: none; }
}

/* Master: search + list */
.master-header {
    padding: .9rem 1rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex; align-items: center; gap: .65rem;
}
[data-layout-mode="dark"] .master-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.master-header-icon {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.master-header-title { font-size: .82rem; font-weight: 700; color: #212529; margin: 0; line-height: 1.2; }
.master-header-sub { font-size: .68rem; color: #878a99; }
[data-layout-mode="dark"] .master-header-title { color: #fff; }

.master-search {
    padding: .65rem .85rem;
    border-bottom: 1px solid #eef0f4;
    position: relative;
}
[data-layout-mode="dark"] .master-search { border-bottom-color: #2a2f34; }
.master-search i {
    position: absolute; left: 1.4rem; top: 50%;
    transform: translateY(-50%);
    color: #878a99; font-size: 1rem;
}
.master-search input {
    width: 100%;
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: .4rem .75rem .4rem 2rem;
    font-size: .82rem;
    background: #fff;
    transition: all .2s ease;
}
.master-search input:focus {
    outline: 0;
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}

.master-list { overflow-y: auto; flex: 1; padding: .4rem; }
.master-list::-webkit-scrollbar { width: 6px; }
.master-list::-webkit-scrollbar-thumb { background: #d6dbe0; border-radius: 3px; }

.resp-item {
    display: flex; align-items: center; gap: .7rem;
    padding: .65rem .75rem;
    border-radius: 9px;
    cursor: pointer;
    transition: all .15s ease;
    margin-bottom: 2px;
    border: 1px solid transparent;
}
.resp-item:hover { background: #f8f9fc; transform: translateX(2px); }
.resp-item.active {
    background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%);
    border-color: rgba(64,81,137,.18);
    box-shadow: 0 4px 10px -6px rgba(64,81,137,.25);
}
[data-layout-mode="dark"] .resp-item:hover { background: #212529; }

.resp-item-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: .8rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px -3px rgba(0,0,0,.2);
}
.resp-item-info { min-width: 0; flex: 1; }
.resp-item-name { font-weight: 600; font-size: .85rem; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .resp-item-name { color: #fff; }
.resp-item-ci { font-size: .68rem; color: #878a99; margin-top: 2px; }
.resp-item-count {
    background: #fff;
    color: #299cdb;
    border: 1px solid #c3ddfa;
    border-radius: 999px;
    padding: .15em .55em;
    font-size: .72rem; font-weight: 700;
    min-width: 32px; text-align: center;
}
.resp-item.active .resp-item-count {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-color: transparent;
}

/* ===== Detail ===== */
.detail-empty {
    text-align: center;
    padding: 5rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 14px;
    border: 1px dashed #d6dbe0;
}
.detail-empty .empty-icon {
    width: 90px; height: 90px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2.8rem;
    color: #adb5bd;
    box-shadow: 0 8px 22px -10px rgba(0,0,0,.15);
}

/* Detail header */
.detail-header {
    background: var(--resp-color, linear-gradient(135deg, #405189 0%, #5e72b8 100%));
    border-radius: 14px;
    padding: 1.5rem;
    color: #fff;
    margin-bottom: 1rem;
    box-shadow: 0 10px 26px -14px rgba(0,0,0,.3);
    position: relative; overflow: hidden;
}
.detail-header::before {
    content: ""; position: absolute;
    top: -50px; right: -50px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.16) 0%, transparent 70%);
    border-radius: 50%;
}
.detail-header > * { position: relative; z-index: 1; }
.detail-avatar {
    width: 70px; height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.35);
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.detail-header h3 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.detail-header-meta { color: rgba(255,255,255,.85); font-size: .82rem; margin-top: .25rem; }

.detail-mini-stats { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
.dms-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    padding: .4em .85em;
    border-radius: 999px;
    font-size: .78rem; font-weight: 700;
    color: #fff;
    backdrop-filter: blur(4px);
}
.dms-pill strong { font-size: .88rem; }

/* Programa block */
.programa-block {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.programa-block:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .programa-block { background: #212529; border-color: #2a2f34; }
.programa-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .programa-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.programa-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.programa-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.programa-title { font-weight: 700; color: #212529; font-size: 1rem; margin: 0; line-height: 1.2; }
[data-layout-mode="dark"] .programa-title { color: #fff; }
.programa-meta { font-size: .72rem; color: #878a99; margin-top: 3px; display: inline-flex; align-items: center; gap: .35rem; flex-wrap: wrap; }

.progress-wrap { display: flex; align-items: center; gap: .65rem; min-width: 180px; }
.progress-bar-wrap { flex-grow: 1; height: 8px; background: #eef0f4; border-radius: 999px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #0ab39c, #299cdb); border-radius: 999px; transition: width .5s ease; }
.progress-pct { font-size: .75rem; font-weight: 800; color: #495057; min-width: 36px; text-align: right; }

.programa-stats { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .75rem; padding-top: .75rem; border-top: 1px dashed #eef0f4; }
[data-layout-mode="dark"] .programa-stats { border-top-color: #2a2f34; }
.stat-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #fff; border: 1px solid #eef0f4;
    padding: .3em .75em; border-radius: 999px;
    font-size: .72rem; font-weight: 700;
    color: #495057;
}
[data-layout-mode="dark"] .stat-chip { background: #212529; border-color: #2a2f34; color: #ced4da; }
.stat-chip strong { font-size: .82rem; }
.stat-chip.green { color: #0ab39c; border-color: #a7f3d8; background: #d1fae5; }
.stat-chip.blue { color: #299cdb; border-color: #c3ddfa; background: #dbeafe; }
.stat-chip.gray { color: #878a99; }
.stat-chip.dark { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color: #fff; border-color: transparent; }

/* Módulos lista */
.modulos-list { padding: .5rem; }
.modulo-row {
    display: flex; align-items: center; gap: .65rem;
    padding: .6rem .85rem;
    border-radius: 8px;
    border-bottom: 1px dashed #eef0f4;
    transition: background-color .15s ease;
}
.modulo-row:hover { background: #f8f9fc; }
.modulo-row:last-child { border-bottom: 0; }
[data-layout-mode="dark"] .modulo-row { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .modulo-row:hover { background: #1a1d20; }
.modulo-num {
    width: 28px; height: 28px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800;
    flex-shrink: 0;
}
.modulo-info { flex: 1; min-width: 0; }
.modulo-nombre { font-weight: 600; color: #212529; font-size: .82rem; line-height: 1.2; }
[data-layout-mode="dark"] .modulo-nombre { color: #fff; }
.modulo-docente { font-size: .68rem; color: #878a99; margin-top: 2px; }

.modulo-estado-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 700;
    padding: .3em .75em; border-radius: 999px;
    border: 1px solid currentColor;
}
.modulo-estado-pill::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

.detail-loading {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    padding: 4rem 2rem;
    text-align: center;
}
[data-layout-mode="dark"] .detail-loading { background: #212529; border-color: #2a2f34; }

.btn-ver-programa {
    background: rgba(64,81,137,.08);
    border: 1px solid rgba(64,81,137,.18);
    color: #405189;
    font-size: .72rem;
    font-weight: 700;
    padding: .35em .85em;
    border-radius: 8px;
    text-decoration: none;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .35rem;
}
.btn-ver-programa:hover { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color: #fff; border-color: transparent; }

/* ===== Enlaces de nombre + moodle ===== */
.prog-acc-name-link, .modulo-nombre-link {
    color: inherit;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: color .15s, border-color .15s;
}
.prog-acc-name-link:hover, .modulo-nombre-link:hover {
    color: #405189;
    border-bottom-color: #405189;
}
.moodle-icon-link {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px;
    background: #fff4e6;
    color: #f76707;
    border: 1px solid #ffd8a8;
    border-radius: 5px;
    margin-left: 6px;
    text-decoration: none;
    font-size: .72rem;
    vertical-align: middle;
    transition: background .15s, color .15s, transform .15s;
}
.moodle-icon-link:hover {
    background: #f76707;
    color: #fff;
    transform: translateY(-1px);
}
.moodle-icon-link.lg {
    width: 26px; height: 26px; font-size: .85rem;
    margin-left: 8px;
}
/* ===== Module highlight ===== */
.modulo-row--curso,
.modulo-row--reciente {
    border-left: 3px solid #299cdb;
    background: #e0f3ff;
}
.modulo-row--proximo {
    border-left: 3px solid #52c41a;
    background: #e6f7e6;
}
[data-layout-mode="dark"] .modulo-row--curso,
[data-layout-mode="dark"] .modulo-row--reciente {
    border-left-color: #299cdb !important;
    background: rgba(41,156,219,.12) !important;
}
[data-layout-mode="dark"] .modulo-row--proximo {
    border-left-color: #52c41a !important;
    background: rgba(82,196,26,.12) !important;
}
.mod-highlight-chip {
    display:inline-flex;align-items:center;gap:4px;
    font-size:.65rem;font-weight:700;
    padding:.25em .65em;border-radius:999px;
    border:1px solid;
    white-space:nowrap;
}
.mod-highlight-chip--active {
    background:#dbeafe;color:#299cdb;border-color:#bae6fd;
}
.mod-highlight-chip--proximo {
    background:#e6f7e6;color:#52c41a;border-color:#b7eb8f;
}
/* ===== Horarios button ===== */
.resp-horarios-btn {
    display:inline-flex;align-items:center;gap:3px;
    font-size:.62rem;font-weight:700;padding:.2em .55em;border-radius:6px;
    border:1px solid #c3ddfa;background:#eef6ff;color:#0b5ed7;
    cursor:pointer;transition:all .15s ease;text-decoration:none;white-space:nowrap;
}
.resp-horarios-btn:hover { background:#0b5ed7;color:#fff;border-color:#0b5ed7; }
.resp-horario-row {
    display:grid;grid-template-columns:140px 100px 1fr auto auto;
    gap:.5rem;align-items:center;
    padding:.5rem .75rem;border:1px solid #eef0f4;border-radius:10px;
    margin-bottom:.4rem;background:#fff;font-size:.8rem;
}
.resp-horario-row .h-date { font-weight:700;color:#2a2f43; }
.resp-horario-row .h-time { color:#5d6478;font-weight:600; }
.resp-horario-row .h-estado { font-weight:700;font-size:.7rem;padding:.15em .55em;border-radius:999px;text-align:center;white-space:nowrap; }
.resp-ver-asistencia {
    display:inline-flex;align-items:center;gap:3px;
    font-size:.62rem;font-weight:700;padding:.2em .55em;border-radius:6px;
    border:1px solid #a7f3d8;background:#d1fae5;color:#0a7059;
    cursor:pointer;transition:all .15s ease;text-decoration:none;white-space:nowrap;
}
.resp-ver-asistencia:hover { background:#0a7059;color:#fff;border-color:#0a7059; }

/* ===== Vista de asistencia dentro del modal de horarios ===== */
.resp-asist-back {
    display:inline-flex; align-items:center; gap:.35rem;
    font-size:.75rem; font-weight:700; padding:.35rem .75rem; border-radius:8px;
    border:1px solid #e3e6ef; background:#fff; color:#405189;
    cursor:pointer; transition:all .15s ease;
}
.resp-asist-back:hover { background:#405189; color:#fff; border-color:#405189; }

.resp-asist-stats {
    display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.6rem; margin-bottom:.75rem;
}
.resp-stat {
    background:#fff; border:1px solid #eef0f5; border-radius:10px;
    padding:.55rem .7rem; display:flex; align-items:center; gap:.6rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.resp-stat-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
.resp-stat .num { font-size:1.05rem; font-weight:800; line-height:1.1; color:#2a2f43; }
.resp-stat .lbl { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#878a99; }
.resp-stat .pct { font-size:.66rem; font-weight:600; color:#aab0c0; margin-left:auto; }
@media (max-width: 640px) {
    .resp-asist-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

.resp-asist-wrap {
    background:#fff; border:1px solid #eef0f5; border-radius:12px; overflow:hidden;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.resp-asist-toolbar {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
    padding:.6rem .8rem; border-bottom:1px solid #eef0f5;
}
.resp-asist-search { position:relative; flex:1 1 200px; max-width:280px; }
.resp-asist-search i {
    position:absolute; left:.65rem; top:50%; transform:translateY(-50%);
    color:#aab0c0; font-size:.9rem; pointer-events:none;
}
.resp-asist-search input {
    width:100%; border:1px solid #e3e6ef; border-radius:8px;
    padding:.38rem .65rem .38rem 1.9rem; font-size:.78rem; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.resp-asist-search input:focus { border-color:#8fa2d8; box-shadow:0 0 0 3px rgba(64,81,137,.08); }
.resp-asist-countlbl { font-size:.7rem; color:#98a0b5; font-weight:600; white-space:nowrap; }
.resp-asist-scroll { max-height:340px; overflow-y:auto; }
.resp-asist-scroll::-webkit-scrollbar { width:8px; }
.resp-asist-scroll::-webkit-scrollbar-thumb { background:#dfe3ec; border-radius:4px; }

.resp-asist-table { width:100%; font-size:.78rem; margin:0; border-collapse:separate; border-spacing:0; }
.resp-asist-table thead th {
    background:#f6f8fc; color:#5d6478; font-weight:700;
    font-size:.64rem; text-transform:uppercase; letter-spacing:.3px;
    padding:.5rem .7rem; position:sticky; top:0; z-index:1;
    border-bottom:1px solid #e8ebf3;
}
.resp-asist-table tbody td { padding:.45rem .7rem; border-top:1px solid #f2f4f9; vertical-align:middle; }
.resp-asist-table tbody tr:first-child td { border-top:0; }
.resp-asist-table tbody tr:hover { background:#f8fafd; }
.resp-asist-table .a-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.14rem .6rem; border-radius:999px; font-size:.68rem; font-weight:700;
    white-space:nowrap;
}
.resp-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.64rem; font-weight:700; color:#fff; letter-spacing:.3px;
}
.resp-part-cell { display:flex; align-items:center; gap:.6rem; min-width:0; }
.resp-part-name { font-weight:600; font-size:.79rem; color:#2a2f43; line-height:1.25; }
.resp-part-ci { font-size:.68rem; color:#98a0b5; }
.resp-asist-obs { font-size:.72rem; color:#98a0b5; }
.btn-moodle {
    background: rgba(247,103,7,.08);
    border: 1px solid rgba(247,103,7,.25);
    color: #c2410c;
    font-size: .72rem;
    font-weight: 700;
    padding: .35em .85em;
    border-radius: 8px;
    text-decoration: none;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .35rem;
    margin-left: .35rem;
}
.btn-moodle:hover { background: linear-gradient(135deg, #f76707 0%, #fd7e14 100%); color: #fff; border-color: transparent; }

/* ===== Filtro y tabs internas ===== */
.detail-filter-bar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: .75rem 1rem;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .detail-filter-bar { background: #1d2125; border-color: #2a2f34; }
.detail-filter-bar label { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #6c757d; margin: 0; }
.detail-filter-bar select {
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .82rem;
    font-weight: 600;
    color: #405189;
    background: #fff;
    cursor: pointer;
    min-width: 160px;
}
.detail-filter-bar select:focus { outline: 0; border-color: #405189; box-shadow: 0 0 0 .15rem rgba(64,81,137,.12); }
[data-layout-mode="dark"] .detail-filter-bar select { background: #1a1d20; border-color: #2a2f34; color: #ced4da; }

.tipo-tabs {
    border: 0; gap: .35rem;
    background: #f3f5f9; padding: .4rem;
    border-radius: 12px;
    display: inline-flex; flex-wrap: wrap;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .tipo-tabs { background: #1a1d20; }
.tipo-tabs .tipo-tab {
    border: 0; background: transparent;
    border-radius: 9px;
    color: #495057; font-weight: 600;
    font-size: .82rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: all .2s ease;
    display: inline-flex; align-items: center; gap: .4rem;
}
.tipo-tabs .tipo-tab:hover { color: #405189; background: rgba(64,81,137,.06); }
.tipo-tabs .tipo-tab.active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    box-shadow: 0 6px 14px -6px rgba(64,81,137,.55);
}
.tipo-tabs .tipo-tab .badge {
    background: rgba(64,81,137,.15); color: #405189;
    font-weight: 700; font-size: .65rem;
    padding: .2em .55em;
    border-radius: 999px;
}
.tipo-tabs .tipo-tab.active .badge { background: rgba(255,255,255,.25); color: #fff; }
[data-layout-mode="dark"] .tipo-tabs .tipo-tab { color: #ced4da; }

/* ===== Acordeón programas ===== */
.prog-accordion { display: flex; flex-direction: column; gap: .65rem; }
.prog-acc-item {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    transition: box-shadow .2s ease;
}
.prog-acc-item.open { box-shadow: 0 10px 24px -12px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .prog-acc-item { background: #212529; border-color: #2a2f34; }
.prog-acc-toggle {
    width: 100%;
    border: 0;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    padding: .9rem 1.1rem;
    display: flex; align-items: center; gap: .85rem; flex-wrap: wrap;
    cursor: pointer; text-align: left;
    transition: background .15s ease;
}
.prog-acc-toggle:hover { background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%); }
[data-layout-mode="dark"] .prog-acc-toggle { background: #1a1d20; }
.prog-acc-item.open .prog-acc-toggle { background: linear-gradient(135deg, #dbeafe 0%, #c3ddfa 100%); border-bottom: 1px solid #eef0f4; }
[data-layout-mode="dark"] .prog-acc-item.open .prog-acc-toggle { background: linear-gradient(135deg, #1a1d20 0%, #212529 100%); border-bottom-color: #2a2f34; }

.prog-acc-icon {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.prog-acc-title-block { flex: 1; min-width: 200px; }
.prog-acc-name { font-weight: 700; color: #212529; font-size: .92rem; margin: 0; line-height: 1.2; }
[data-layout-mode="dark"] .prog-acc-name { color: #fff; }
.prog-acc-meta { font-size: .7rem; color: #878a99; margin-top: 3px; display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }

.prog-acc-progress { display: flex; align-items: center; gap: .55rem; min-width: 150px; }
.prog-acc-progress .bar { flex: 1; height: 7px; background: rgba(64,81,137,.12); border-radius: 999px; overflow: hidden; }
.prog-acc-progress .bar > span { display: block; height: 100%; background: linear-gradient(90deg, #0ab39c, #299cdb); border-radius: 999px; transition: width .5s ease; }
.prog-acc-progress .pct { font-size: .72rem; font-weight: 800; color: #495057; min-width: 32px; text-align: right; }

.prog-acc-chevron {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: rgba(64,81,137,.1);
    color: #405189;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem;
    transition: transform .2s ease;
    flex-shrink: 0;
}
.prog-acc-item.open .prog-acc-chevron { transform: rotate(180deg); background: #405189; color: #fff; }

.prog-acc-body { display: none; padding: 1rem; }
.prog-acc-item.open .prog-acc-body { display: block; }

.prog-acc-stats { display: flex; flex-wrap: wrap; gap: .35rem; margin-bottom: .85rem; padding-bottom: .85rem; border-bottom: 1px dashed #eef0f4; }
[data-layout-mode="dark"] .prog-acc-stats { border-bottom-color: #2a2f34; }
</style>
@endpush

@push('css')
<style>
/* ===== Seguimiento Modal ===== */
#sdModal .modal-content { border:0; border-radius:14px; overflow:hidden; }
#sdModal .modal-header {
    background: linear-gradient(135deg,#163b6b 0%,#2a5d97 100%);
    color:#fff; border:0; padding: 1rem 1.3rem;
}
#sdModal.is-late .modal-header   { background: linear-gradient(135deg,#d63031 0%,#f06548 100%); }
#sdModal.is-warn .modal-header   { background: linear-gradient(135deg,#d68910 0%,#f7b84b 100%); }
#sdModal.is-info .modal-header   { background: linear-gradient(135deg,#0b5ed7 0%,#3d8bf5 100%); }
#sdModal .modal-header .btn-close { filter: brightness(0) invert(1); opacity:.9; }
#sdModal .modal-title { font-size:1rem; font-weight:700; margin-bottom:.15rem; }
#sdModal .modal-subtitle { font-size:.78rem; opacity:.85; }
#sdModal .modal-body { padding:1.1rem 1.3rem; background:#fafbfd; }
/* ===== Seguimiento Diario shared styles ===== */
.sd-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
}
.sd-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.sd-hero-title { font-size: 1.1rem; font-weight: 700; margin-bottom: .15rem; letter-spacing: .2px; }
.sd-hero-sub { font-size: .8rem; opacity: .9; }
.sd-week-nav .btn {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-weight: 600;
    backdrop-filter: blur(4px);
}
.sd-week-nav .btn:hover { background: rgba(255,255,255,.32); color: #fff; }
.sd-week-label {
    display: inline-block;
    padding: .35rem .9rem;
    background: rgba(255,255,255,.22);
    border-radius: 999px;
    font-size: .82rem;
    font-weight: 600;
    letter-spacing: .3px;
}
.sd-stat-card {
    position: relative;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eef0f5;
    padding: .9rem 1rem;
    overflow: hidden;
}
.sd-stat-card::before {
    content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    background: var(--sd-accent, #405189);
}
.sd-stat-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; color: #878a99; letter-spacing: .4px; margin-bottom: .15rem; }
.sd-stat-value { font-size: 1.5rem; font-weight: 700; color: #2a2f43; line-height: 1.1; }
.sd-stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.sd-day {
    background: #fff; border-radius: 12px; border: 1px solid #eef0f5;
    padding: .85rem 1rem; margin-bottom: .9rem;
}
.sd-day-header { display: flex; align-items: center; gap: .65rem; margin-bottom: .6rem; }
.sd-day-pill {
    min-width: 56px; padding: .45rem .35rem; text-align: center;
    background: linear-gradient(180deg,#f3f6fc,#e9eef8);
    border: 1px solid #dfe5f0; border-radius: 10px;
    color: #2a2f43; line-height: 1;
}
.sd-day-pill .sd-day-num { display:block; font-size: 1.3rem; font-weight: 800; }
.sd-day-pill .sd-day-name { display:block; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #6c7693; margin-top: .15rem; }
.sd-day-pill.is-today { background: linear-gradient(180deg,#e6f4ff,#cfe7ff); border-color:#9bc7f0; color:#0b5ed7; }
.sd-day-pill.is-today .sd-day-name { color:#0b5ed7; }
.sd-day-count { font-size: .72rem; color: #878a99; font-weight: 600; }
.sd-day-empty {
    text-align: center; padding: .9rem; border: 1px dashed #dfe3ec; border-radius: 10px;
    color: #aab0c0; font-size: .8rem;
}
.sd-h-row {
    display: grid;
    grid-template-columns: 90px 1fr auto;
    gap: .85rem;
    align-items: stretch;
    border: 1px solid #eef0f5;
    border-left: 4px solid var(--sd-row-accent, #299cdb);
    border-radius: 10px;
    padding: .65rem .85rem;
    margin-bottom: .55rem;
    background: #fbfcfe;
    transition: box-shadow .15s ease, transform .15s ease;
}
.sd-h-row:hover { box-shadow: 0 6px 18px -10px rgba(64,81,137,.25); transform: translateY(-1px); }
.sd-h-row.is-late {
    background: linear-gradient(135deg,#fff2f1 0%,#ffe5e2 100%);
    border-color: #f06548;
    border-left-color: #d63031;
    box-shadow: 0 4px 14px -8px rgba(214,48,49,.4);
    animation: sdPulseLate 2.6s ease-in-out infinite;
}
@keyframes sdPulseLate {
    0%, 100% { box-shadow: 0 4px 14px -8px rgba(214,48,49,.4); }
    50%      { box-shadow: 0 6px 22px -6px rgba(214,48,49,.6); }
}
.sd-h-row.is-late .sd-h-estado .sd-pill { background:#d63031; color:#fff; }
.sd-h-row.is-confirm-late {
    background: linear-gradient(135deg,#fff8ec 0%,#ffeecc 100%);
    border-color: #f7b84b;
    border-left-color: #d68910;
}
.sd-h-row.is-confirm-late .sd-h-estado .sd-pill { background:#d68910; color:#fff; }
.sd-h-row.is-confirm {
    background: linear-gradient(135deg,#f0fbf7 0%,#e0f5ed 100%);
    border-color: #69cbb1;
    border-left-color: #0ab39c;
}
.sd-h-row.is-missing {
    background: linear-gradient(135deg,#eef6ff 0%,#dcebff 100%);
    border-color: #7fb6f0;
    border-left-color: #0b5ed7;
}
.sd-h-time {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    background:#fff; border:1px solid #eef0f5; border-radius:8px;
    padding:.35rem .25rem; min-width:90px; text-align:center;
}
.sd-h-time .h-i { font-size:.95rem; font-weight:700; color:#2a2f43; line-height:1; }
.sd-h-time .h-s { font-size:.66rem; color:#878a99; margin-top:.15rem; letter-spacing:.5px; }
.sd-h-row.is-late .sd-h-time { border-color:#f3aaa1; background:#fff5f4; }
.sd-h-row.is-confirm-late .sd-h-time { border-color:#f0cf86; background:#fffaf0; }
.sd-h-row.is-missing .sd-h-time { border-color:#b8d4f5; background:#f5faff; }
.sd-h-body { display:flex; flex-direction:column; gap:.25rem; min-width:0; }
.sd-h-title { font-size:.92rem; font-weight:700; color:#2a2f43; line-height:1.2; word-break:break-word; }
.sd-h-title a.sd-link { color:inherit; text-decoration:none; border-bottom:1px dashed transparent; transition: color .15s, border-color .15s; }
.sd-h-title a.sd-link:hover { color:#405189; border-bottom-color:#405189; }
.sd-h-title .sd-codigo { display:inline-block; font-size:.65rem; color:#fff; background:var(--sd-row-accent,#299cdb); padding:.1rem .45rem; border-radius:999px; vertical-align:middle; margin-left:.35rem; font-weight:700; letter-spacing:.3px; }
.sd-h-row.is-late .sd-h-title .sd-codigo { background:#d63031; }
.sd-h-row.is-confirm-late .sd-h-title .sd-codigo { background:#d68910; }
.sd-h-row.is-missing .sd-h-title .sd-codigo { background:#0b5ed7; }
.sd-h-modulo { font-size:.78rem; color:#5d6478; font-weight:600; }
.sd-h-modulo a.sd-link { color:#5d6478; text-decoration:none; border-bottom:1px dashed transparent; }
.sd-h-modulo a.sd-link:hover { color:#0b5ed7; border-bottom-color:#0b5ed7; }
.sd-h-meta { display:flex; flex-wrap:wrap; gap:.5rem .85rem; font-size:.74rem; color:#5d6478; margin-top:.1rem; }
.sd-h-meta .sd-meta-i { display:inline-flex; align-items:center; gap:.3rem; }
.sd-h-meta .sd-meta-i i { color:#878a99; font-size:.85rem; }
.sd-h-meta .sd-meta-i b { font-weight:600; color:#2a2f43; }
.sd-h-meta .sd-asignado-chip {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.1rem .55rem; border-radius:999px; font-size:.7rem; font-weight:700;
    color:#fff;
}
.sd-h-meta .sd-asignado-chip i { color:#fff; }
.sd-h-side { display:flex; flex-direction:column; gap:.35rem; align-items:flex-end; justify-content:space-between; min-height:60px; }
.sd-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.18rem .55rem; border-radius:999px;
    font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
    background: var(--sd-pill-bg,#e9eef8); color: var(--sd-pill-color,#405189);
}
.sd-pill::before { content:""; width:6px; height:6px; border-radius:50%; background: currentColor; }
.sd-btn-toggle {
    border:1px solid #d8deeb; background:#fff; color:#5d6478;
    border-radius:8px; padding:.25rem .65rem; font-size:.72rem; font-weight:600;
    display:inline-flex; align-items:center; gap:.3rem; cursor:pointer;
}
.sd-btn-toggle:hover { background:#f1f4fb; color:#2a2f43; }
.sd-btn-toggle.is-warn { border-color:#f0cf86; background:#fffaf0; color:#8a6100; }
.sd-btn-toggle.is-warn:hover { background:#fff4dc; }
.sd-btn-toggle.is-info { border-color:#9bc7f0; background:#eef6ff; color:#0b5ed7; }
.sd-btn-toggle.is-info:hover { background:#dceaff; }
.sd-btn-toggle.is-danger { border-color:#f3aaa1; background:#fff5f4; color:#b1240f; }
.sd-btn-toggle.is-danger:hover { background:#ffe5e2; }
.sd-flag {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.15rem .55rem; border-radius:6px;
    font-size:.66rem; font-weight:700; letter-spacing:.3px; text-transform:uppercase;
}
.sd-flag.is-danger { background:#d63031; color:#fff; }
.sd-flag.is-warn   { background:#d68910; color:#fff; }
.sd-flag.is-info   { background:#0b5ed7; color:#fff; }
.sd-modal-info-grid {
    display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:.6rem .85rem;
    background:#fff; border:1px solid #eef0f5; border-radius:10px; padding:.85rem 1rem; margin-bottom:1rem;
}
.sd-modal-info-grid .sd-info-cell { min-width:0; }
.sd-modal-info-grid .sd-info-label { font-size:.65rem; font-weight:700; color:#878a99; text-transform:uppercase; letter-spacing:.4px; }
.sd-modal-info-grid .sd-info-value { font-size:.85rem; color:#2a2f43; font-weight:600; word-break:break-word; }
.sd-modal-info-grid .sd-info-value .text-muted { font-weight:400; }
.sd-modal-info-grid .sd-info-value a { color:#0b5ed7; text-decoration:none; }
.sd-modal-info-grid .sd-info-value a:hover { text-decoration:underline; }
.sd-modal-alert {
    display:flex; align-items:flex-start; gap:.55rem;
    padding:.7rem .9rem; border-radius:10px;
    font-size:.82rem; margin-bottom:1rem;
}
.sd-modal-alert.is-danger { background:#fff2f1; color:#b1240f; border:1px solid #f3aaa1; }
.sd-modal-alert.is-warn   { background:#fff8ec; color:#8a6100; border:1px solid #f0cf86; }
.sd-modal-alert.is-info   { background:#eef6ff; color:#1c3e9e; border:1px solid #b8d4f5; }
.sd-modal-alert i { font-size:1.1rem; margin-top:.05rem; }
.sd-detail-title {
    font-size:.7rem; font-weight:700; color:#878a99;
    text-transform:uppercase; letter-spacing:.4px; margin-bottom:.35rem;
    display:flex; align-items:center; gap:.35rem;
}
.sd-obs-box {
    background:#fff; border:1px solid #eef0f5; border-radius:8px;
    padding:.55rem .75rem; font-size:.82rem; color:#2a2f43;
    white-space:pre-wrap; word-break:break-word;
}
.sd-obs-empty { color:#aab0c0; font-style:italic; background:#fff; border:1px dashed #dfe3ec; }
.sd-asist-table { width:100%; font-size:.78rem; margin:0; }
.sd-asist-table thead th {
    background:#f6f8fc; color:#5d6478; font-weight:700;
    font-size:.66rem; text-transform:uppercase; letter-spacing:.3px;
    padding:.45rem .6rem; position:sticky; top:0;
}
.sd-asist-table tbody td { padding:.4rem .6rem; border-top:1px solid #eef0f5; }
.sd-tpl { display:none; }
.sd-asist-table .a-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.1rem .55rem; border-radius:999px; font-size:.68rem; font-weight:700;
}
.sd-asist-table .a-Presente { background:#d4f6ee; color:#0a7059; }
.sd-asist-table .a-Atraso   { background:#fff4d6; color:#8a6100; }
.sd-asist-table .a-Falta    { background:#ffd9d3; color:#b1240f; }
.sd-asist-table .a-Justificado { background:#dde7ff; color:#1c3e9e; }
.a-Presente { background:#d4f6ee; color:#0a7059; }
.a-Atraso   { background:#fff4d6; color:#8a6100; }
.a-Falta    { background:#ffd9d3; color:#b1240f; }
.a-Justificado { background:#dde7ff; color:#1c3e9e; }
</style>
@endpush

@section('content')
<div class="acad-page">
<div class="row">
    <div class="col-12">
        <div class="resp-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-user-settings-line"></i></div>
                <div>
                    <h4>Responsables Académicos</h4>
                    <p class="hero-sub">Selecciona un responsable para ver los programas a su cargo y el estado de cada módulo</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Área Académica</a></li>
                <li class="breadcrumb-item active">Responsables</li>
            </ol>
        </div>
    </div>
</div>

<div class="detail-filter-bar mb-3" id="global-filter-bar">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <label><i class="ri-calendar-2-line"></i> Gestión:</label>
            <select id="global-gestion-filter">
                <option value="" selected>Todas</option>
                @foreach($gestiones as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label><i class="ri-flag-2-line"></i> Estado:</label>
            <select id="global-estado-filter">
                <option value="">Todos</option>
                @foreach($estados as $e)
                    <option value="{{ $e->nombre }}" @if($e->nombre === $estadoActual) selected @endif>{{ $e->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <small class="text-muted" style="font-size:.72rem;"><i class="ri-information-line"></i> Los filtros aplican a todos los responsables y actualizan los contadores</small>
</div>

<div class="resp-layout">
    {{-- ============== MASTER: lista de responsables ============== --}}
    <div class="resp-master">
        <div class="master-header">
            <span class="master-header-icon"><i class="ri-team-line"></i></span>
            <div>
                <h6 class="master-header-title">Responsables</h6>
                <span class="master-header-sub">{{ count($responsables) }} en total</span>
            </div>
        </div>
        <div class="master-search">
            <i class="ri-search-line"></i>
            <input type="text" id="resp-search" placeholder="Buscar..." autocomplete="off">
        </div>
        <div class="master-list" id="resp-list">
            @forelse($responsables as $r)
            @php
                $color = $r->color && trim($r->color) ? $r->color : '#878a99';
                $ini = strtoupper(substr($r->nombres ?? '', 0, 1) . substr($r->apellidos ?? '', 0, 1));
            @endphp
            <div class="resp-item" data-id="{{ $r->id }}" data-search="{{ strtolower(($r->ci ?? '') . ' ' . ($r->nombres ?? '') . ' ' . ($r->apellidos ?? '')) }}" data-color="{{ $color }}">
                <div class="resp-item-avatar" style="background: {{ $color }};">{{ $ini }}</div>
                <div class="resp-item-info">
                    <div class="resp-item-name">{{ $r->apellidos }}, {{ $r->nombres }}</div>
                    <div class="resp-item-ci"><i class="ri-passport-line align-bottom"></i> CI: {{ $r->ci }}</div>
                </div>
                <span class="resp-item-count">{{ $r->total_programas }}</span>
            </div>
            @empty
            <div class="text-center text-muted py-4" style="font-size:.82rem;">Sin responsables registrados</div>
            @endforelse
        </div>
    </div>

    {{-- ============== DETAIL ============== --}}
    <div class="resp-detail" id="resp-detail">
        <div class="detail-tabs mb-3" id="detail-tabs" style="display:none;">
            <div class="d-flex align-items-center gap-1 bg-light p-1 rounded" style="width:fit-content;">
                <button type="button" class="btn btn-sm detail-tab active" data-tab="programas" style="font-weight:700;padding:.35rem .85rem;border-radius:8px;border:0;background:linear-gradient(135deg,#163b6b 0%,#2a5d97 100%);color:#fff;">
                    <i class="ri-booklet-line"></i> Programas
                </button>
                <button type="button" class="btn btn-sm detail-tab" data-tab="seguimiento" style="font-weight:600;padding:.35rem .85rem;border-radius:8px;border:0;background:transparent;color:#495057;">
                    <i class="ri-calendar-check-line"></i> Seguimiento Semanal
                </button>
            </div>
        </div>
        <div id="detail-tab-content">
            <div class="detail-empty">
                <div class="empty-icon"><i class="ri-user-search-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Selecciona un responsable</h5>
                <p class="text-muted mb-0">Haz clic sobre uno de la lista para ver los programas que tiene a su cargo y el seguimiento semanal.</p>
            </div>
        </div>
    </div>
</div>
</div>{{-- /acad-page --}}
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

{{-- Modal WhatsApp Masivo --}}
<div class="modal fade" id="modalWhatsAppMasivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWhatsAppTitle">Mensajes WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="whatsapp-loading" class="text-center py-5">
                    <span class="spinner-border spinner-border-sm me-2"></span>Cargando participantes...
                </div>
                <div id="whatsapp-content" style="display:none;">
                    <div class="p-2 border-bottom bg-light d-flex gap-2 flex-wrap align-items-center" id="whatsapp-actions">
                        <span class="fw-semibold text-dark fs-12 me-1"><i class="ri-whatsapp-line"></i> Envío masivo:</span>
                        <button class="btn btn-sm btn-success" id="btn-wa-todos"><i class="ri-whatsapp-line"></i> Todos</button>
                        <button class="btn btn-sm btn-outline-success" id="btn-wa-aprobados"><i class="ri-checkbox-circle-line"></i> Aprobados</button>
                        <button class="btn btn-sm btn-outline-danger" id="btn-wa-reprobados"><i class="ri-close-circle-line"></i> Reprobados</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light text-muted text-uppercase">
                                <tr>
                                    <th style="width:40px" class="text-center">#</th>
                                    <th>Participante</th>
                                    <th>Mensaje</th>
                                    <th style="width:130px" class="text-center">WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody id="whatsapp-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('resp-list');
    const search = document.getElementById('resp-search');
    const detail = document.getElementById('resp-detail');

    // Búsqueda
    search.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        list.querySelectorAll('.resp-item').forEach(function (it) {
            const txt = it.dataset.search || '';
            it.style.display = (!q || txt.includes(q)) ? '' : 'none';
        });
    });

    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    }

    function estadoStyle(e) {
        return {
            'Concluido': '#0ab39c',
            'En Desarrollo': '#299cdb',
            'No Inicio': '#878a99',
            'Pendiente': '#f7b84b',
            'Suspendido': '#f06548',
        }[e] || '#878a99';
    }

    function renderDetail(data, color, currentRespId) {
        const r = data.responsable;
        const ini = ((r.nombres || '').charAt(0) + (r.apellidos || '').charAt(0)).toUpperCase();
        const res = data.resumen;
        const bgGradient = 'linear-gradient(135deg, ' + color + ' 0%, ' + shadeColor(color, -20) + ' 100%)';

        let html = '<div class="detail-header" style="--resp-color:' + bgGradient + '; background: ' + bgGradient + ';">' +
            '<div class="d-flex align-items-center gap-3 flex-wrap">' +
                '<div class="detail-avatar">' + escHtml(ini) + '</div>' +
                '<div style="min-width:0;">' +
                    '<h3 class="fs-20 mb-0">' + escHtml(r.apellidos) + ', ' + escHtml(r.nombres) + '</h3>' +
                    '<div class="detail-header-meta">' +
                        '<i class="ri-passport-line"></i> CI: ' + escHtml(r.ci) +
                        (r.correo ? ' &nbsp;·&nbsp; <i class="ri-mail-line"></i> ' + escHtml(r.correo) : '') +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="detail-mini-stats">' +
                '<span class="dms-pill"><i class="ri-booklet-line"></i> <strong>' + res.total_programas + '</strong> programa' + (res.total_programas !== 1 ? 's' : '') + '</span>' +
                '<span class="dms-pill"><i class="ri-stack-line"></i> <strong>' + res.total_modulos + '</strong> módulo' + (res.total_modulos !== 1 ? 's' : '') + '</span>' +
                '<span class="dms-pill"><i class="ri-checkbox-circle-line"></i> <strong>' + res.concluidos + '</strong> concluidos</span>' +
                '<span class="dms-pill"><i class="ri-loader-4-line"></i> <strong>' + res.en_desarrollo + '</strong> en desarrollo</span>' +
                '<span class="dms-pill"><i class="ri-error-warning-line"></i> <strong>' + res.no_inicio + '</strong> sin iniciar</span>' +
            '</div>' +
        '</div>';

        const gestionActual = data.gestion_actual ?? '';

        if (!data.tipos || data.tipos.length === 0) {
            html += '<div class="detail-empty">' +
                '<div class="empty-icon"><i class="ri-inbox-2-line"></i></div>' +
                '<h5 class="text-dark fw-bold mb-1">Sin programas asignados</h5>' +
                '<p class="text-muted mb-0">' + (gestionActual ? 'No tiene programas en la gestión seleccionada.' : 'Este responsable aún no tiene programas a su cargo.') + '</p>' +
            '</div>';
        } else {
            // ===== Tabs por tipo =====
            html += '<div class="tipo-tabs" id="detail-tipo-tabs">';
            data.tipos.forEach(function (t, idx) {
                html += '<button type="button" class="tipo-tab' + (idx === 0 ? ' active' : '') + '" data-tipo-target="tipo-pane-' + t.tipo_id + '">' +
                    '<i class="ri-price-tag-3-line"></i> ' + escHtml(t.tipo_nombre) +
                    '<span class="badge">' + t.programas.length + '</span>' +
                '</button>';
            });
            html += '</div>';

            // ===== Panes por tipo (acordeón) =====
            html += '<div id="detail-tipo-panes">';
            data.tipos.forEach(function (t, idx) {
                html += '<div class="tipo-pane' + (idx === 0 ? '' : ' d-none') + '" id="tipo-pane-' + t.tipo_id + '">';
                html += '<div class="prog-accordion">';
                t.programas.forEach(function (p, pidx) {
                    const mr = p.modulos_resumen;
                    const isOpen = pidx === 0;
                    const progUrl = '{{ url("admin/programas") }}/' + p.id + '/show';
                    const progMoodleIcon = p.moodle_link
                        ? '<a href="' + escHtml(p.moodle_link) + '" target="_blank" rel="noopener" class="moodle-icon-link" onclick="event.stopPropagation();" title="Abrir aula Moodle del programa"><i class="ri-graduation-cap-line"></i></a>'
                        : '';
                    const progMoodleBtn = p.moodle_link
                        ? '<a href="' + escHtml(p.moodle_link) + '" target="_blank" rel="noopener" class="btn-moodle" title="Abrir aula Moodle del programa"><i class="ri-graduation-cap-line"></i> Moodle</a>'
                        : '';
                    html += '<div class="prog-acc-item' + (isOpen ? ' open' : '') + '">' +
                        '<div class="prog-acc-toggle" data-acc-toggle role="button" tabindex="0">' +
                            '<span class="prog-acc-icon"><i class="ri-graduation-cap-line"></i></span>' +
                            '<div class="prog-acc-title-block">' +
                                '<h6 class="prog-acc-name">' +
                                    '<a href="' + progUrl + '" class="prog-acc-name-link" onclick="event.stopPropagation();" title="Ir al detalle del programa">' + escHtml(p.nombre) + '</a>' +
                                    progMoodleIcon +
                                '</h6>' +
                                '<div class="prog-acc-meta">' +
                                    '<span><i class="ri-hashtag"></i>' + escHtml(p.codigo) + '</span>' +
                                    '<span>·</span><span><i class="ri-calendar-line"></i>Gestión ' + escHtml(p.gestion) + '</span>' +
                                    (p.estado_nombre ? '<span>·</span><span><i class="ri-flag-2-line"></i>' + escHtml(p.estado_nombre) + '</span>' : '') +
                                    '<span>·</span><span><i class="ri-team-line"></i>' + p.inscritos + ' inscritos</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="prog-acc-progress">' +
                                '<div class="bar"><span style="width:' + p.progreso_pct + '%;"></span></div>' +
                                '<span class="pct">' + p.progreso_pct + '%</span>' +
                            '</div>' +
                            '<span class="prog-acc-chevron"><i class="ri-arrow-down-s-line"></i></span>' +
                        '</div>' +

                        '<div class="prog-acc-body">' +
                            '<div class="prog-acc-stats">' +
                                '<span class="stat-chip dark"><i class="ri-stack-line"></i> <strong>' + mr.total + '</strong> módulos</span>' +
                                '<span class="stat-chip green"><i class="ri-checkbox-circle-line"></i> <strong>' + mr.concluido + '</strong> concluidos</span>' +
                                '<span class="stat-chip blue"><i class="ri-loader-4-line"></i> <strong>' + mr.en_desarrollo + '</strong> en desarrollo</span>' +
                                '<span class="stat-chip gray"><i class="ri-time-line"></i> <strong>' + mr.no_inicio + '</strong> sin iniciar</span>' +
                                '<a href="' + progUrl + '" class="btn-ver-programa ms-auto" title="Ver detalle completo del programa">' +
                                    '<i class="ri-external-link-line"></i> Ver programa' +
                                '</a>' +
                                progMoodleBtn +
                            '</div>' +
                            '<div class="modulos-list">';
                    if (p.modulos.length === 0) {
                        html += '<div class="text-center text-muted py-3 fst-italic" style="font-size:.78rem;"><i class="ri-inbox-2-line"></i> Sin módulos registrados</div>';
                    } else {
                        p.modulos.forEach(function (m, midx) {
                            const c = estadoStyle(m.desarrollo);
                            const docente = m.docente_nombres
                                ? '<i class="ri-user-star-line"></i> ' + escHtml(m.docente_apellidos) + ', ' + escHtml(m.docente_nombres)
                                : '<i class="ri-user-unfollow-line"></i> <span class="fst-italic">Sin docente asignado</span>';
                            var notasHtml = '';
                            if (m.desarrollo === 'Concluido') {
                                var nivelacionHtml;
                                if (m.total_reprobados > 0) {
                                    nivelacionHtml = m.con_nota_nivelacion > 0
                                        ? '<span class="badge bg-soft-success text-success" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota nivelaci\u00f3n</span>'
                                        : '<span class="badge bg-soft-secondary text-secondary" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota nivelaci\u00f3n</span>';
                                } else {
                                    nivelacionHtml = '<span class="badge bg-soft-info text-info" style="font-size:.6rem;font-weight:500;"><i class="ri-check-line"></i> No corresponde</span>';
                                }
                                notasHtml = '<div class="mt-1" style="font-size:.6rem;line-height:1.3;">' +
                                    (m.con_nota_modulo > 0
                                        ? '<span class="badge bg-soft-success text-success" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota m\u00f3dulo</span>'
                                        : '<span class="badge bg-soft-secondary text-secondary" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota m\u00f3dulo</span>') +
                                    ' ' +
                                    nivelacionHtml +
                                '</div>';
                            }
                            var waBtn = '';
                            if (m.desarrollo === 'Concluido' && m.con_nota_modulo > 0) {
                                waBtn = '<button class="btn btn-sm btn-icon btn-soft-success btn-whatsapp-masivo" data-modulo-id="' + m.id + '" data-modulo-nombre="' + escHtml(m.nombre) + '" title="Enviar mensajes por WhatsApp" style="width:22px;height:22px;font-size:.6rem;padding:0;border-radius:4px;margin-left:4px;">' +
                                    '<i class="ri-whatsapp-line"></i>' +
                                '</button>';
                            }
                            const modMoodleIcon = m.moodle_link
                                ? '<a href="' + escHtml(m.moodle_link) + '" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del módulo"><i class="ri-graduation-cap-line"></i></a>'
                                : '';
                            var modRowClass = '';
                            var modChipHtml = '';
                            if (m.en_curso) {
                                modRowClass = ' modulo-row--curso';
                                modChipHtml = '<span class="mod-highlight-chip mod-highlight-chip--active">En curso</span>';
                            } else if (m.reciente) {
                                modRowClass = ' modulo-row--reciente';
                                modChipHtml = '<span class="mod-highlight-chip mod-highlight-chip--active">Recientemente en curso</span>';
                            } else if (m.proximo) {
                                modRowClass = ' modulo-row--proximo';
                                modChipHtml = '<span class="mod-highlight-chip mod-highlight-chip--proximo">Pr\u00f3ximo</span>';
                            }
                            const modUrl = '{{ url("admin/programas/modulo") }}/' + m.id + '/detalle';
                            var horariosBtn = '<button type="button" class="resp-horarios-btn" data-modulo-id="' + m.id + '" data-modulo-nombre="' + escHtml(m.nombre) + '" title="Ver horarios del m\u00f3dulo"><i class="ri-calendar-event-line"></i> Horarios</button>';
                            html += '<div class="modulo-row' + modRowClass + '">' +
                                '<span class="modulo-num">' + (midx + 1) + '</span>' +
                                '<div class="modulo-info">' +
                                    '<div class="modulo-nombre">' +
                                        '<a href="' + modUrl + '" class="modulo-nombre-link" title="Ir al detalle del m\u00f3dulo">' + escHtml(m.nombre) + '</a>' +
                                        modMoodleIcon +
                                        waBtn +
                                    '</div>' +
                                    '<div class="modulo-docente">' + docente + '</div>' +
                                    notasHtml +
                                '</div>' +
                                horariosBtn +
                                '<div style="display:inline-flex;align-items:center;gap:.35rem;">' +
                                    modChipHtml +
                                    '<span class="modulo-estado-pill" style="color:' + c + ';">' + escHtml(m.desarrollo) + '</span>' +
                                '</div>' +
                            '</div>';
                        });
                    }
                    html += '</div>' +
                        '</div>' +
                    '</div>';
                });
                html += '</div>'; // /prog-accordion
                html += '</div>'; // /tipo-pane
            });
            html += '</div>'; // /tipo-panes
        }

        detailTabContent.innerHTML = html;
        bindDetailEvents();
    }

    function bindDetailEvents() {
        const ctx = document.getElementById('detail-tab-content');
        if (!ctx) return;
        // Tabs por tipo
        const tabs = ctx.querySelectorAll('.tipo-tab');
        const panes = ctx.querySelectorAll('.tipo-pane');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const target = this.dataset.tipoTarget;
                panes.forEach(p => p.classList.toggle('d-none', p.id !== target));
            });
        });

        // Acordeón (independiente: solo uno abierto a la vez por pane)
        ctx.querySelectorAll('[data-acc-toggle]').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const item = this.closest('.prog-acc-item');
                if (!item) return;
                const accordion = item.parentElement;
                const wasOpen = item.classList.contains('open');
                accordion.querySelectorAll('.prog-acc-item').forEach(i => i.classList.remove('open'));
                if (!wasOpen) item.classList.add('open');
            });
        });

        bindRespHorarios();
    }

    function bindRespHorarios() {
        var container = document.getElementById('detail-tab-content');
        if (!container) return;
        container.querySelectorAll('.resp-horarios-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var moduloId = this.dataset.moduloId;
                var moduloNombre = this.dataset.moduloNombre;
                var modalEl = document.getElementById('respModuloHorariosModal');
                var titleEl = document.getElementById('respModuloHorariosTitle');
                var bodyEl = document.getElementById('respModuloHorariosBody');
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
                    } else {
                        bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error al cargar horarios</p></div>';
                    }
                })
                .catch(function () {
                    bodyEl.innerHTML = '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error de conexi\u00f3n</p></div>';
                });
            });
        });
    }

    function renderModuloHorarios(data) {
        var horarios = data.horarios || [];
        if (horarios.length === 0) {
            return '<div class="text-center py-5 text-muted"><i class="ri-inbox-2-line fs-2"></i><p class="mt-2">Este m\u00f3dulo no tiene sesiones/horarios registrados.</p></div>';
        }
        var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        var diasSem = ['domingo','lunes','martes','mi\u00e9rcoles','jueves','viernes','s\u00e1bado'];
        var html = '<div style="margin-bottom:.75rem;font-size:.78rem;color:#878a99;"><i class="ri-information-line"></i> ' + horarios.length + ' sesi\u00f3n' + (horarios.length !== 1 ? 'es' : '') + ' programada' + (horarios.length !== 1 ? 's' : '') + '</div>';
        horarios.forEach(function (h) {
            var fechaTxt = '\u2014';
            if (h.fecha_desarrollo) {
                var d = new Date(h.fecha_desarrollo + 'T12:00:00');
                fechaTxt = diasSem[d.getDay()] + ' ' + d.getDate() + ' de ' + meses[d.getMonth()] + ' del ' + d.getFullYear();
            }
            var hInicio = h.hora_inicio ? h.hora_inicio.substring(0,5) : '\u2014';
            var hFin = h.hora_fin ? h.hora_fin.substring(0,5) : '\u2014';
            var estado = h.estado || '\u2014';
            var estCls = '';
            var asistenciaBtn = '';
            if (estado === 'Desarrollado') {
                estCls = 'background:#d4f6ee;color:#0a7059;';
                asistenciaBtn = '<button type="button" class="resp-ver-asistencia" data-horario-id="' + h.id + '" data-horario-fecha="' + escHtml(fechaTxt) + '" title="Ver lista de asistencia"><i class="ri-team-line"></i> Asistencia</button>';
            } else if (estado === 'Confirmado' || estado === 'Programado') estCls = 'background:#dde7ff;color:#1c3e9e;';
            else if (estado === 'En Desarrollo') estCls = 'background:#cfe7ff;color:#0b5ed7;';
            else if (estado === 'No Inicio' || estado === 'Pendiente') estCls = 'background:#fff4d6;color:#8a6100;';
            else if (estado === 'Postergado') estCls = 'background:#fff4d6;color:#8a6100;';
            else if (estado === 'Suspendido') estCls = 'background:#ffd9d3;color:#b1240f;';
            else estCls = 'background:#eef0f5;color:#5d6478;';
            html += '<div class="resp-horario-row">' +
                '<span class="h-date">' + fechaTxt + '</span>' +
                '<span class="h-time">' + hInicio + ' \u2014 ' + hFin + '</span>' +
                '<span class="h-estado" style="' + estCls + '">' + escHtml(estado) + '</span>' +
                (asistenciaBtn ? '<span>' + asistenciaBtn + '</span>' : '<span></span>') +
            '</div>';
        });
        return html;
    }

    // ===== Asistencia dentro del mismo modal de horarios =====
    var respHorariosCache = { body: '', title: '' };

    (function () {
        var modalBody = document.getElementById('respModuloHorariosBody');
        if (!modalBody) return;

        // Click delegado: abrir asistencia o volver a horarios
        modalBody.addEventListener('click', function (ev) {
            var aBtn = ev.target.closest('.resp-ver-asistencia');
            if (aBtn) {
                showRespAsistencia(aBtn.dataset.horarioId, aBtn.dataset.horarioFecha);
                return;
            }
            var backBtn = ev.target.closest('.resp-asist-back');
            if (backBtn && respHorariosCache.body) {
                modalBody.innerHTML = respHorariosCache.body;
                var titleEl = document.getElementById('respModuloHorariosTitle');
                if (titleEl && respHorariosCache.title) titleEl.innerHTML = respHorariosCache.title;
            }
        });

        // Buscador de participantes (delegado)
        modalBody.addEventListener('input', function (ev) {
            if (!ev.target.classList.contains('resp-asist-search-input')) return;
            var q = ev.target.value.toLowerCase().trim();
            var rows = modalBody.querySelectorAll('.resp-asist-table tbody tr');
            var visibles = 0;
            rows.forEach(function (r) {
                var show = !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
                r.style.display = show ? '' : 'none';
                if (show) visibles++;
            });
            var cnt = modalBody.querySelector('.resp-asist-count');
            if (cnt) cnt.textContent = visibles + ' de ' + rows.length;
        });
    })();

    function showRespAsistencia(horarioId, fechaTxt) {
        var bodyEl = document.getElementById('respModuloHorariosBody');
        var titleEl = document.getElementById('respModuloHorariosTitle');
        if (!bodyEl) return;

        respHorariosCache.body = bodyEl.innerHTML;
        respHorariosCache.title = titleEl ? titleEl.innerHTML : '';
        if (titleEl) titleEl.innerHTML = '<i class="ri-team-line"></i> Asistencia \u2014 ' + escHtml(fechaTxt);
        bodyEl.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando asistencia...</p></div>';

        var url = '{{ url("admin/programas/horario") }}/' + horarioId + '/asistencia';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) {
                bodyEl.innerHTML = respAsistHeaderHtml(null, 0) + '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line"></i><p class="mt-2">' + escHtml(data.message || 'Error al cargar') + '</p></div>';
                return;
            }
            bodyEl.innerHTML = renderRespAsistencia(data.participantes || [], horarioId);
        })
        .catch(function () {
            bodyEl.innerHTML = respAsistHeaderHtml(null, 0) + '<div class="text-center py-5 text-danger"><i class="ri-error-warning-line fs-3"></i><p class="mt-2">Error de conexi\u00f3n</p></div>';
        });
    }

    function respAsistHeaderHtml(horarioId, registrados) {
        var html = '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">';
        html += '<button type="button" class="resp-asist-back"><i class="ri-arrow-left-line"></i> Volver a horarios</button>';
        if (horarioId && registrados > 0) {
            html += '<a class="btn btn-sm btn-soft-danger d-inline-flex align-items-center gap-1" ' +
                    'href="{{ url("admin/programas/horario") }}/' + horarioId + '/asistencia-pdf" ' +
                    'title="Descargar reporte de asistencia en PDF"><i class="ri-file-pdf-line"></i> Reporte PDF</a>';
        }
        html += '</div>';
        return html;
    }

    function renderRespAsistencia(parts, horarioId) {
        var registrados = parts.filter(function (p) { return p.asistio; }).length;
        var html = respAsistHeaderHtml(horarioId, registrados);

        if (parts.length === 0) {
            return html + '<div class="text-center py-5 text-muted"><i class="ri-inbox-2-line fs-2"></i><p class="mt-2">No hay participantes inscritos en este m\u00f3dulo.</p></div>';
        }

        var cfg = {
            'Presente':    { ico: 'ri-checkbox-circle-line', bg: '#d4f6ee', color: '#0a7059' },
            'Atraso':      { ico: 'ri-time-line',            bg: '#fff4d6', color: '#8a6100' },
            'Falta':       { ico: 'ri-close-circle-line',    bg: '#ffd9d3', color: '#b1240f' },
            'Justificado': { ico: 'ri-file-shield-2-line',   bg: '#dde7ff', color: '#1c3e9e' },
            'Sin registro':{ ico: 'ri-question-line',        bg: '#eef0f5', color: '#5d6478' }
        };
        var resumen = {};
        parts.forEach(function (p) {
            var k = (p.asistio || '').trim() || 'Sin registro';
            resumen[k] = (resumen[k] || 0) + 1;
        });
        var total = parts.length;
        var keys = Object.keys(cfg).filter(function (k) { return resumen[k]; });
        Object.keys(resumen).forEach(function (k) { if (keys.indexOf(k) === -1) keys.push(k); });

        html += '<div class="resp-asist-stats">';
        keys.forEach(function (k) {
            var c = cfg[k] || { ico: 'ri-question-line', bg: '#eef0f5', color: '#5d6478' };
            var count = resumen[k] || 0;
            var pct = total > 0 ? Math.round(count * 100 / total) : 0;
            html += '<div class="resp-stat">' +
                    '<div class="resp-stat-ico" style="background:' + c.bg + ';color:' + c.color + ';"><i class="' + c.ico + '"></i></div>' +
                    '<div><div class="num">' + count + '</div><div class="lbl">' + escHtml(k) + '</div></div>' +
                    '<div class="pct">' + pct + '%</div>' +
                    '</div>';
        });
        html += '</div>';

        var avatarColors = ['#405189', '#0ab39c', '#299cdb', '#f06548', '#f7b84b', '#6559cc', '#02a8b5', '#d63384'];
        html += '<div class="resp-asist-wrap">';
        html += '<div class="resp-asist-toolbar">';
        html += '<div class="resp-asist-search"><i class="ri-search-line"></i>' +
                '<input type="text" class="resp-asist-search-input" placeholder="Buscar por nombre o CI..." autocomplete="off"></div>';
        html += '<span class="resp-asist-countlbl resp-asist-count">' + total + ' de ' + total + '</span>';
        html += '</div>';
        html += '<div class="resp-asist-scroll"><table class="resp-asist-table">';
        html += '<thead><tr><th style="width:38px;" class="text-center">#</th><th>Participante</th><th style="width:120px;" class="text-center">Asistencia</th><th style="width:28%;">Observaci\u00f3n</th></tr></thead><tbody>';
        parts.forEach(function (p, idx) {
            var nombre = ((p.apellidos || '') + ', ' + (p.nombres || '')).replace(/^, /, '').replace(/, $/, '');
            var iniciales = (((p.nombres || ' ').charAt(0)) + ((p.apellidos || ' ').charAt(0))).toUpperCase().trim() || '?';
            var colorIdx = 0;
            for (var ci = 0; ci < nombre.length; ci++) colorIdx = (colorIdx + nombre.charCodeAt(ci)) % avatarColors.length;
            var estadoHtml = p.asistio
                ? '<span class="a-pill a-' + escHtml((p.asistio || '').trim()) + '">' + escHtml(p.asistio) + '</span>'
                : '<span class="text-muted fst-italic" style="font-size:.72rem;">Sin registro</span>';
            html += '<tr data-search="' + escHtml((nombre + ' ' + (p.ci || '')).toLowerCase()) + '">' +
                    '<td class="text-center text-muted">' + (idx + 1) + '</td>' +
                    '<td><div class="resp-part-cell">' +
                    '<span class="resp-avatar" style="background:' + avatarColors[colorIdx] + ';">' + escHtml(iniciales) + '</span>' +
                    '<div style="min-width:0;"><div class="resp-part-name">' + escHtml(nombre) + '</div>' +
                    '<div class="resp-part-ci">CI: ' + escHtml(p.ci || '\u2014') + '</div></div>' +
                    '</div></td>' +
                    '<td class="text-center">' + estadoHtml + '</td>' +
                    '<td class="resp-asist-obs">' + (p.observacion ? escHtml(p.observacion) : '\u2014') + '</td>' +
                    '</tr>';
        });
        html += '</tbody></table></div></div>';
        return html;
    }

    function loadResponsableDetail(id, color, gestion, estado) {
        detailTabContent.innerHTML = '<div class="detail-loading">' +
            '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>' +
            '<p class="mt-2 text-muted mb-0">Cargando detalle del responsable...</p>' +
        '</div>';

        const params = new URLSearchParams();
        if (gestion) params.append('gestion', gestion);
        if (estado) params.append('estado', estado);
        let url = '{{ url("admin/responsables") }}/' + id + '/detalle';
        if (params.toString()) url += '?' + params.toString();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) renderDetail(data, color, id);
            else detailTabContent.innerHTML = '<div class="detail-empty"><div class="empty-icon"><i class="ri-error-warning-line"></i></div><h5 class="text-dark fw-bold mb-1">Error</h5><p class="text-muted mb-0">' + (data.message || 'No se pudo cargar') + '</p></div>';
        })
        .catch(() => {
            detailTabContent.innerHTML = '<div class="detail-empty"><div class="empty-icon"><i class="ri-error-warning-line"></i></div><h5 class="text-dark fw-bold mb-1">Error de conexión</h5></div>';
        });
    }

    // Helper para oscurecer color para gradiente
    function shadeColor(color, percent) {
        let R = parseInt(color.substring(1,3), 16);
        let G = parseInt(color.substring(3,5), 16);
        let B = parseInt(color.substring(5,7), 16);
        R = parseInt(R * (100 + percent) / 100);
        G = parseInt(G * (100 + percent) / 100);
        B = parseInt(B * (100 + percent) / 100);
        R = (R<255)?R:255; G = (G<255)?G:255; B = (B<255)?B:255;
        R = (R>0)?R:0; G = (G>0)?G:0; B = (B>0)?B:0;
        const RR = R.toString(16).padStart(2,'0');
        const GG = G.toString(16).padStart(2,'0');
        const BB = B.toString(16).padStart(2,'0');
        return '#' + RR + GG + BB;
    }

    // Estado: responsable seleccionado actual
    const state = { respId: null, color: '#405189', activeTab: 'programas' };
    const globalGestionSel = document.getElementById('global-gestion-filter');
    const globalEstadoSel = document.getElementById('global-estado-filter');
    const detailTabs = document.getElementById('detail-tabs');
    const detailTabContent = document.getElementById('detail-tab-content');

    function currentFilters() {
        return {
            gestion: globalGestionSel ? globalGestionSel.value : '',
            estado: globalEstadoSel ? globalEstadoSel.value : '',
        };
    }

    // Tab switching
    document.querySelectorAll('.detail-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            const tabs = document.querySelectorAll('.detail-tab');
            tabs.forEach(function (t) {
                t.style.background = 'transparent';
                t.style.color = '#495057';
                t.style.fontWeight = '600';
            });
            this.style.background = 'linear-gradient(135deg,#163b6b 0%,#2a5d97 100%)';
            this.style.color = '#fff';
            this.style.fontWeight = '700';
            state.activeTab = this.dataset.tab;
            loadCurrentTab();
        });
    });

    function loadCurrentTab() {
        if (!state.respId) return;
        if (state.activeTab === 'seguimiento') {
            loadResponsableSeguimiento(state.respId, state.color);
        } else {
            const f = currentFilters();
            loadResponsableDetail(state.respId, state.color, f.gestion, f.estado);
        }
    }

    function loadResponsableSeguimiento(id, color, inicio) {
        detailTabContent.innerHTML = '<div class="detail-loading">' +
            '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>' +
            '<p class="mt-2 text-muted mb-0">Cargando seguimiento semanal...</p>' +
        '</div>';

        let url = '{{ url("admin/responsables") }}/' + id + '/seguimiento-semanal';
        const params = new URLSearchParams();
        if (inicio) params.append('inicio', inicio);
        if (params.toString()) url += '?' + params.toString();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                detailTabContent.innerHTML = data.html;
                bindSeguimientoEvents();
            } else {
                detailTabContent.innerHTML = '<div class="detail-empty"><div class="empty-icon"><i class="ri-error-warning-line"></i></div><h5 class="text-dark fw-bold mb-1">Error</h5><p class="text-muted mb-0">' + (data.message || 'No se pudo cargar') + '</p></div>';
            }
        })
        .catch(function () {
            detailTabContent.innerHTML = '<div class="detail-empty"><div class="empty-icon"><i class="ri-error-warning-line"></i></div><h5 class="text-dark fw-bold mb-1">Error de conexión</h5></div>';
        });
    }

    function bindSeguimientoEvents() {
        // Modal detalle
        detailTabContent.querySelectorAll('.sd-open-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-horario-id');
                const kind = this.getAttribute('data-modal-kind') || '';
                const ttl = this.getAttribute('data-modal-title') || 'Detalle del horario';
                const stl = this.getAttribute('data-modal-subtitle') || '';

                const tpl = document.getElementById('sdTpl_' + id);
                if (!tpl) return;

                const KIND_CLASSES = ['is-late', 'is-warn', 'is-info'];
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
                var programaLink = bodyEl.querySelector('.sd-info-value a[href]');
                if (programaLink) {
                    gotoEl.href = programaLink.getAttribute('href');
                    gotoEl.style.display = '';
                } else {
                    gotoEl.removeAttribute('href');
                    gotoEl.style.display = 'none';
                }

                var modal = bootstrap.Modal.getInstance(modalEl);
                if (!modal) modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        });

        // Navegación de semanas
        detailTabContent.querySelectorAll('.sd-semana-nav').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const inicio = this.dataset.inicio;
                if (state.respId) {
                    loadResponsableSeguimiento(state.respId, state.color, inicio);
                }
            });
        });
    }

    list.addEventListener('click', function (e) {
        const item = e.target.closest('.resp-item');
        if (!item) return;
        list.querySelectorAll('.resp-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        state.respId = item.dataset.id;
        state.color = item.dataset.color || '#405189';
        // Show tabs and load default tab
        detailTabs.style.display = '';
        loadCurrentTab();
    });

    // Refresca los contadores de la lista master
    function refreshCounts() {
        const f = currentFilters();
        const params = new URLSearchParams();
        if (f.gestion) params.append('gestion', f.gestion);
        if (f.estado) params.append('estado', f.estado);

        fetch('{{ route("admin.responsables.counts") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            // Reordenar y actualizar badges
            const items = Array.from(list.querySelectorAll('.resp-item'));
            const byId = {};
            data.responsables.forEach(r => { byId[r.id] = r.total_programas; });
            items.forEach(it => {
                const id = it.dataset.id;
                const count = byId[id] ?? 0;
                const badge = it.querySelector('.resp-item-count');
                if (badge) badge.textContent = count;
                it.dataset.count = count;
            });
            // Reordenar: counts desc
            items.sort((a, b) => (parseInt(b.dataset.count || 0)) - (parseInt(a.dataset.count || 0)));
            items.forEach(it => list.appendChild(it));
        });
    }

    function onFilterChange() {
        refreshCounts();
        if (state.respId) {
            loadCurrentTab();
        }
    }
    if (globalGestionSel) globalGestionSel.addEventListener('change', onFilterChange);
    if (globalEstadoSel) globalEstadoSel.addEventListener('change', onFilterChange);

    // Nota: no se auto-carga el primer responsable. Su detalle dispara
    // consultas pesadas (módulos, notas y rangos de horarios) y hacía lento
    // el primer render. El detalle se carga solo al hacer clic.
    // ---- WhatsApp Masivo ----
    let whatsappMensajes = [];
    let whatsappModuloNombre = '';

    function abrirWhatsAppEnLote(mensajes) {
        mensajes.forEach(function (m, i) {
            var waLink = 'https://wa.me/' + m.celular + '?text=' + encodeURIComponent(m.texto);
            setTimeout(function () {
                window.open(waLink, '_blank');
            }, i * 300);
        });
        var count = mensajes.length;
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-white border-0';
        toastEl.setAttribute('role','alert'); toastEl.setAttribute('aria-live','assertive'); toastEl.setAttribute('aria-atomic','true');
        toastEl.style.background = '#0abf53';
        toastEl.innerHTML = '<div class="d-flex"><div class="toast-body fs-12 fw-medium">Abriendo ' + count + ' enlace' + (count !== 1 ? 's' : '') + ' de WhatsApp...</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        document.body.appendChild(toastEl);
        var t = new bootstrap.Toast(toastEl, { delay: 4000 });
        t.show();
        toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-whatsapp-masivo');
        if (!btn) return;

        const moduloId = btn.dataset.moduloId;
        const moduloNombre = btn.dataset.moduloNombre;

        document.getElementById('modalWhatsAppTitle').textContent = 'WhatsApp - ' + moduloNombre;
        document.getElementById('whatsapp-loading').style.display = 'block';
        document.getElementById('whatsapp-content').style.display = 'none';
        document.getElementById('whatsapp-body').innerHTML = '';
        whatsappMensajes = [];

        new bootstrap.Modal(document.getElementById('modalWhatsAppMasivo')).show();

        fetch('{{ url("admin/programas/modulo") }}/' + moduloId + '/mensajes-whatsapp', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            document.getElementById('whatsapp-loading').style.display = 'none';
            if (!data.success || !data.mensajes || data.mensajes.length === 0) {
                document.getElementById('whatsapp-body').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay participantes con mensajes para generar</td></tr>';
                document.getElementById('whatsapp-content').style.display = 'block';
                return;
            }

            whatsappMensajes = data.mensajes;
            var html = '';
            data.mensajes.forEach(function (m, i) {
                var waLink = 'https://wa.me/' + m.celular + '?text=' + encodeURIComponent(m.texto);
                var badgeClass = m.tipo === 'aprobado' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                html += '<tr>' +
                    '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                    '<td><span class="fw-semibold text-dark">' + m.apellidos + ', ' + m.nombres + '</span></td>' +
                    '<td class="text-wrap" style="max-width:400px;font-size:.8rem;">' +
                        '<span class="badge ' + badgeClass + ' fs-10 me-1">' + m.tipo + '</span> ' + m.texto +
                    '</td>' +
                    '<td class="text-center">' +
                        '<a href="' + waLink + '" target="_blank" class="btn btn-sm btn-success d-flex align-items-center gap-1" style="width:fit-content;margin:0 auto;">' +
                            '<i class="ri-whatsapp-line"></i> Abrir WhatsApp' +
                        '</a>' +
                    '</td>' +
                '</tr>';
            });
            document.getElementById('whatsapp-body').innerHTML = html;
            document.getElementById('whatsapp-content').style.display = 'block';
        })
        .catch(function () {
            document.getElementById('whatsapp-loading').style.display = 'none';
            document.getElementById('whatsapp-body').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Error al cargar los datos</td></tr>';
            document.getElementById('whatsapp-content').style.display = 'block';
        });
    });

    document.getElementById('btn-wa-todos')?.addEventListener('click', function () {
        abrirWhatsAppEnLote(whatsappMensajes);
    });

    document.getElementById('btn-wa-aprobados')?.addEventListener('click', function () {
        abrirWhatsAppEnLote(whatsappMensajes.filter(function (m) { return m.tipo === 'aprobado'; }));
    });

    document.getElementById('btn-wa-reprobados')?.addEventListener('click', function () {
        abrirWhatsAppEnLote(whatsappMensajes.filter(function (m) { return m.tipo === 'reprobado'; }));
    });
});
</script>
@endpush

{{-- Modal Horarios de Módulo --}}
<div class="modal fade" id="respModuloHorariosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="respModuloHorariosTitle"><i class="ri-calendar-event-line"></i> Horarios del m&oacute;dulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="respModuloHorariosBody" style="background:#fafbfd;min-height:150px;">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando horarios...</p></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<style>
#respModuloHorariosModal .modal-content { border:0;border-radius:14px;overflow:hidden; }
#respModuloHorariosModal .modal-header { background:linear-gradient(135deg,#163b6b 0%,#2a5d97 100%);border:0; }
#respModuloHorariosModal .modal-header .btn-close { filter:brightness(0) invert(1);opacity:.9; }
#respModuloHorariosModal .modal-title { font-size:.95rem;font-weight:700;color:#fff; }
</style>
