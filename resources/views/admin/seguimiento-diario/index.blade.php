@extends('admin.layouts.master')

@section('title', 'Seguimiento Diario - ' . config('app.name'))

@push('css')
<style>
/* ====== Hero ====== */
.sd-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
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

/* ====== Stats ====== */
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

/* ====== Day section ====== */
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

/* ====== Horario row ====== */
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

/* Estado: atrasado (No Inicio + fecha pasada) — rojo */
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

/* Estado: confirmado pendiente (Confirmado + fecha pasada) — naranja */
.sd-h-row.is-confirm-late {
    background: linear-gradient(135deg,#fff8ec 0%,#ffeecc 100%);
    border-color: #f7b84b;
    border-left-color: #d68910;
}
.sd-h-row.is-confirm-late .sd-h-estado .sd-pill { background:#d68910; color:#fff; }

/* Estado: confirmado futuro — borde verde-azulado para seguimiento */
.sd-h-row.is-confirm {
    background: linear-gradient(135deg,#f0fbf7 0%,#e0f5ed 100%);
    border-color: #69cbb1;
    border-left-color: #0ab39c;
}

/* Estado: desarrollado sin observaciones ni asistencia — azul para solicitar */
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

/* ====== Modal detalle ====== */
#sdModal .modal-content { border:0; border-radius:14px; overflow:hidden; }
#sdModal .modal-header {
    background: linear-gradient(135deg,#405189 0%,#5e72b8 100%);
    color:#fff; border:0; padding: 1rem 1.3rem;
}
#sdModal.is-late .modal-header   { background: linear-gradient(135deg,#d63031 0%,#f06548 100%); }
#sdModal.is-warn .modal-header   { background: linear-gradient(135deg,#d68910 0%,#f7b84b 100%); }
#sdModal.is-info .modal-header   { background: linear-gradient(135deg,#0b5ed7 0%,#3d8bf5 100%); }
#sdModal .modal-header .btn-close { filter: brightness(0) invert(1); opacity:.9; }
#sdModal .modal-title { font-size:1rem; font-weight:700; margin-bottom:.15rem; }
#sdModal .modal-subtitle { font-size:.78rem; opacity:.85; }
#sdModal .modal-body { padding:1.1rem 1.3rem; background:#fafbfd; }

.sd-modal-info-grid {
    display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:.85rem 1rem;
    background:#fff; border:1px solid #eef0f5; border-radius:12px; padding:1rem 1.1rem; margin-bottom:1rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.sd-modal-info-grid .sd-info-cell { min-width:0; display:flex; align-items:flex-start; gap:.6rem; }
.sd-info-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:#eef1f8; color:#405189; font-size:1rem;
}
.sd-modal-info-grid .sd-info-label { font-size:.62rem; font-weight:700; color:#878a99; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.1rem; }
.sd-modal-info-grid .sd-info-value { font-size:.82rem; color:#2a2f43; font-weight:600; word-break:break-word; line-height:1.35; }
.sd-modal-info-grid .sd-info-value .text-muted { font-weight:400; }
.sd-modal-info-grid .sd-info-value a { color:#0b5ed7; text-decoration:none; }
.sd-modal-info-grid .sd-info-value a:hover { text-decoration:underline; }
@media (max-width: 992px) {
    .sd-modal-info-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

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
/* ===== Resumen de asistencia (tarjetas) ===== */
.sd-asist-stats {
    display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.6rem; margin-bottom:.75rem;
}
.sd-stat {
    background:#fff; border:1px solid #eef0f5; border-radius:10px;
    padding:.55rem .7rem; display:flex; align-items:center; gap:.6rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.sd-stat-ico {
    width:32px; height:32px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
.sd-stat .num { font-size:1.05rem; font-weight:800; line-height:1.1; color:#2a2f43; }
.sd-stat .lbl { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#878a99; }
.sd-stat .pct { font-size:.66rem; font-weight:600; color:#aab0c0; margin-left:auto; }
@media (max-width: 640px) {
    .sd-asist-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

/* ===== Lista de asistencia ===== */
.sd-asist-wrap {
    background:#fff; border:1px solid #eef0f5; border-radius:12px; overflow:hidden;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.sd-asist-toolbar {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
    padding:.6rem .8rem; border-bottom:1px solid #eef0f5;
}
.sd-asist-search { position:relative; flex:1 1 200px; max-width:280px; }
.sd-asist-search i {
    position:absolute; left:.65rem; top:50%; transform:translateY(-50%);
    color:#aab0c0; font-size:.9rem; pointer-events:none;
}
.sd-asist-search input {
    width:100%; border:1px solid #e3e6ef; border-radius:8px;
    padding:.38rem .65rem .38rem 1.9rem; font-size:.78rem; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.sd-asist-search input:focus { border-color:#8fa2d8; box-shadow:0 0 0 3px rgba(64,81,137,.08); }
.sd-asist-countlbl { font-size:.7rem; color:#98a0b5; font-weight:600; white-space:nowrap; }
.sd-asist-scroll { max-height:340px; overflow-y:auto; }
.sd-asist-scroll::-webkit-scrollbar { width:8px; }
.sd-asist-scroll::-webkit-scrollbar-thumb { background:#dfe3ec; border-radius:4px; }

.sd-asist-table { width:100%; font-size:.78rem; margin:0; border-collapse:separate; border-spacing:0; }
.sd-asist-table thead th {
    background:#f6f8fc; color:#5d6478; font-weight:700;
    font-size:.64rem; text-transform:uppercase; letter-spacing:.3px;
    padding:.5rem .7rem; position:sticky; top:0; z-index:1;
    border-bottom:1px solid #e8ebf3;
}
.sd-asist-table tbody td { padding:.45rem .7rem; border-top:1px solid #f2f4f9; vertical-align:middle; }
.sd-asist-table tbody tr:first-child td { border-top:0; }
.sd-asist-table tbody tr:hover { background:#f8fafd; }

.sd-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.64rem; font-weight:700; color:#fff; letter-spacing:.3px;
}
.sd-part-cell { display:flex; align-items:center; gap:.6rem; min-width:0; }
.sd-part-name { font-weight:600; font-size:.79rem; color:#2a2f43; line-height:1.25; }
.sd-part-ci { font-size:.68rem; color:#98a0b5; }
.sd-asist-obs { font-size:.72rem; color:#98a0b5; }

.sd-asist-table .a-pill, .a-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.14rem .6rem; border-radius:999px; font-size:.68rem; font-weight:700;
    white-space:nowrap;
}
.sd-asist-table .a-Presente, .a-Presente { background:#d4f6ee; color:#0a7059; }
.sd-asist-table .a-Atraso,   .a-Atraso   { background:#fff4d6; color:#8a6100; }
.sd-asist-table .a-Falta,    .a-Falta    { background:#ffd9d3; color:#b1240f; }
.sd-asist-table .a-Justificado, .a-Justificado { background:#dde7ff; color:#1c3e9e; }

.sd-tpl { display:none; }

/* ===== Responsables breakdown ===== */
.sd-resp-section {
    background:#fff; border:1px solid #eef0f5; border-radius:12px;
    padding:.9rem 1rem 1rem; margin-bottom:1rem;
}
.sd-resp-section-header {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.5rem; margin-bottom:.65rem;
}
.sd-resp-section-title {
    font-size:.78rem; font-weight:700; color:#2a2f43;
    text-transform:uppercase; letter-spacing:.4px;
    display:flex; align-items:center; gap:.4rem;
}
.sd-resp-section-title i { color:#405189; }
.sd-resp-section-hint { font-size:.7rem; color:#878a99; }
.sd-resp-grid {
    display:grid;
    grid-template-columns: 1fr;
    gap:.7rem;
}
@media (min-width: 768px) {
    .sd-resp-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
.sd-resp-card {
    border:1px solid #eef0f5; border-radius:10px;
    background:#fbfcfe; overflow:hidden;
    display:flex; flex-direction:column;
}
.sd-resp-card-head {
    display:flex; align-items:center; gap:.55rem;
    padding:.55rem .75rem;
    background: linear-gradient(135deg, var(--sd-resp-color,#6c757d) 0%, color-mix(in srgb, var(--sd-resp-color,#6c757d) 70%, #ffffff) 100%);
    color:#fff;
}
.sd-resp-avatar {
    width:30px; height:30px; border-radius:50%;
    background: rgba(255,255,255,.25);
    color:#fff; font-weight:800; font-size:.72rem;
    display:inline-flex; align-items:center; justify-content:center;
    border:1px solid rgba(255,255,255,.3);
    flex-shrink:0;
}
.sd-resp-name { font-size:.82rem; font-weight:700; line-height:1.1; flex:1; min-width:0; }
.sd-resp-name .sd-resp-sub { display:block; font-size:.65rem; opacity:.85; font-weight:500; margin-top:1px; }
.sd-resp-total-pill {
    background: rgba(255,255,255,.25); color:#fff;
    padding:.18rem .55rem; border-radius:999px;
    font-size:.7rem; font-weight:700;
}
.sd-resp-body { padding:.55rem .75rem .65rem; display:flex; flex-direction:column; gap:.45rem; }

.sd-resp-bar {
    display:flex; height:6px; border-radius:4px; overflow:hidden; background:#eef0f5;
}
.sd-resp-bar > span { display:block; height:100%; }
.sd-resp-bar .bs-desarrollado { background:#0ab39c; }
.sd-resp-bar .bs-confirmado   { background:#f7b84b; }
.sd-resp-bar .bs-noinicio     { background:#d63031; }
.sd-resp-bar .bs-postergado   { background:#a78bfa; }
.sd-resp-bar .bs-otro         { background:#9aa5b4; }
.sd-resp-bar .bs-futuro       { background:#cdd7e6; }

.sd-resp-row {
    display:flex; justify-content:space-between; align-items:center;
    font-size:.72rem; color:#5d6478;
}
.sd-resp-row .label { display:inline-flex; align-items:center; gap:.3rem; }
.sd-resp-row .label::before {
    content:""; width:8px; height:8px; border-radius:2px;
    background: var(--dot, #6c757d);
}
.sd-resp-row .val { font-weight:700; color:#2a2f43; }

.sd-resp-chips {
    display:flex; flex-wrap:wrap; gap:.3rem;
}
.sd-resp-chip {
    display:inline-flex; align-items:center; gap:.25rem;
    padding:.12rem .5rem; border-radius:999px;
    font-size:.65rem; font-weight:700; letter-spacing:.2px;
    background:#eef0f5; color:#5d6478;
}
.sd-resp-chip i { font-size:.75rem; }
.sd-resp-chip.c-desarrollado { background:#d4f6ee; color:#0a7059; }
.sd-resp-chip.c-confirmado   { background:#fff4d6; color:#8a6100; }
.sd-resp-chip.c-noinicio     { background:#ffd9d3; color:#b1240f; }
.sd-resp-chip.c-postergado   { background:#ece4ff; color:#5b3fb0; }
.sd-resp-chip.c-asistok      { background:#d4f6ee; color:#0a7059; }
.sd-resp-chip.c-asistmiss    { background:#dde7ff; color:#1c3e9e; }

.sd-resp-sub-block {
    background:#fff; border:1px dashed #dfe3ec; border-radius:8px;
    padding:.4rem .55rem; font-size:.7rem; color:#5d6478;
}
.sd-resp-sub-block b { color:#2a2f43; font-weight:700; }
.sd-resp-sub-block .miss { color:#b1240f; font-weight:700; }
.sd-resp-sub-block .ok   { color:#0a7059; font-weight:700; }

.sd-resp-empty {
    text-align:center; color:#878a99; font-size:.78rem; padding:.85rem;
    border:1px dashed #dfe3ec; border-radius:8px;
}

/* ===== Mini calendario semanal por responsable ===== */
.sd-resp-week {
    display:grid; grid-template-columns: repeat(7, 1fr); gap:.25rem;
}
.sd-resp-week .day {
    position:relative;
    border:1px solid #eef0f5; border-radius:6px;
    padding:.32rem .15rem .35rem;
    text-align:center; background:#fff;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    min-height:48px;
}
.sd-resp-week .day .dow {
    font-size:.58rem; font-weight:700; color:#878a99;
    text-transform:uppercase; letter-spacing:.4px; line-height:1;
}
.sd-resp-week .day .num {
    font-size:.85rem; font-weight:800; color:#2a2f43; line-height:1.05; margin-top:1px;
}
.sd-resp-week .day .cnt {
    margin-top:2px;
    background:#eef0f5; color:#5d6478;
    font-size:.6rem; font-weight:700;
    border-radius:999px; padding:.05rem .4rem;
    line-height:1.1;
}
.sd-resp-week .day.is-empty { opacity:.55; }
.sd-resp-week .day.is-empty .cnt { background:transparent; color:#cdd2dc; }
.sd-resp-week .day.is-today { border-color:#9bc7f0; background:linear-gradient(180deg,#eaf4ff,#dceaff); }
.sd-resp-week .day.is-today .num { color:#0b5ed7; }
.sd-resp-week .day.has-parallel {
    border-color:#f7b84b;
    background: linear-gradient(180deg,#fff8ec,#ffeecc);
}
.sd-resp-week .day.has-parallel .cnt { background:#d68910; color:#fff; }
.sd-resp-week .day .par-flag {
    position:absolute; top:-6px; right:-4px;
    background:#d68910; color:#fff;
    font-size:.55rem; font-weight:800;
    padding:.05rem .35rem; border-radius:999px;
    box-shadow:0 2px 6px -2px rgba(214,137,16,.6);
    letter-spacing:.2px;
}
.sd-resp-week-foot {
    display:flex; align-items:center; justify-content:space-between;
    font-size:.7rem; color:#5d6478; margin-top:.35rem;
}
.sd-resp-week-foot .total-week { font-weight:700; color:#2a2f43; }
.sd-resp-week-foot .par-badge {
    display:inline-flex; align-items:center; gap:.25rem;
    background:#fff4d6; color:#8a6100;
    padding:.1rem .5rem; border-radius:999px;
    font-size:.62rem; font-weight:700;
}

@media (max-width: 640px) {
    .sd-h-row { grid-template-columns: 1fr; }
    .sd-h-side { flex-direction:row; align-items:center; justify-content:space-between; min-height:auto; }
    .sd-modal-info-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $diasNombres = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    $fmtFechaCorta = function ($ymd) use ($mesesEs) {
        $t = strtotime($ymd);
        return date('j', $t) . ' ' . $mesesEs[(int) date('n', $t) - 1];
    };
    $fmtFechaLarga = function ($ymd) use ($mesesEs, $diasNombres) {
        $t = strtotime($ymd);
        $diaIdx = (int) date('N', $t) - 1;
        return $diasNombres[$diaIdx] . ' ' . date('j', $t) . ' de ' . $mesesEs[(int) date('n', $t) - 1] . ' del ' . date('Y', $t);
    };
    $fmtHora = fn ($h) => $h ? date('H:i', strtotime($h)) : '—';

    $estadoStyles = [
        'Confirmado'    => ['bg' => '#d4f6ee', 'color' => '#0a7059'],
        'Programado'    => ['bg' => '#dde7ff', 'color' => '#1c3e9e'],
        'Desarrollado'  => ['bg' => '#e3e9f7', 'color' => '#283f7a'],
        'Concluido'     => ['bg' => '#d4f6ee', 'color' => '#0a7059'],
        'En Desarrollo' => ['bg' => '#cfe7ff', 'color' => '#0b5ed7'],
        'No Inicio'     => ['bg' => '#eef0f5', 'color' => '#5d6478'],
        'Pendiente'     => ['bg' => '#fff4d6', 'color' => '#8a6100'],
        'Postergado'    => ['bg' => '#fff4d6', 'color' => '#8a6100'],
        'Suspendido'    => ['bg' => '#ffd9d3', 'color' => '#b1240f'],
    ];
    $estadoStyle = function ($estado) use ($estadoStyles) {
        $e = trim((string) $estado);
        return $estadoStyles[$e] ?? ['bg' => '#eef0f5', 'color' => '#5d6478'];
    };

    $rowAccent = function ($estado) {
        $e = trim((string) $estado);
        return match ($e) {
            'Desarrollado', 'Concluido' => '#0ab39c',
            'Confirmado'                 => '#0ab39c',
            'En Desarrollo'              => '#299cdb',
            'Programado'                 => '#405189',
            'Pendiente', 'Postergado'    => '#f7b84b',
            'Suspendido'                 => '#f06548',
            'No Inicio'                  => '#878a99',
            default                      => '#878a99',
        };
    };
@endphp

<div class="container-fluid">

    {{-- ============ HERO ============ --}}
    <div class="sd-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative">
            <div>
                <div class="sd-hero-title"><i class="ri-radar-line align-middle me-1"></i> Seguimiento Diario</div>
                <div class="sd-hero-sub">Horarios programados de la semana — responsables, estado y desarrollo de cada sesión</div>
            </div>
            <div class="sd-week-nav d-flex align-items-center gap-2">
                <a href="{{ route('admin.seguimiento-diario.index', ['inicio' => $semanaAnterior]) }}"
                   class="btn btn-sm" title="Semana anterior">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
                <span class="sd-week-label">
                    {{ $fmtFechaCorta($inicio) }} — {{ $fmtFechaCorta($fin) }} · {{ date('Y', strtotime($inicio)) }}
                </span>
                <a href="{{ route('admin.seguimiento-diario.index', ['inicio' => $semanaHoy]) }}"
                   class="btn btn-sm" title="Semana actual">
                    <i class="ri-calendar-check-line"></i> Hoy
                </a>
                <a href="{{ route('admin.seguimiento-diario.index', ['inicio' => $semanaSiguiente]) }}"
                   class="btn btn-sm" title="Semana siguiente">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ============ STATS ============ --}}
    <div class="row g-2 mb-3">
        @php
            $cards = [
                ['l' => 'Sesiones de la semana', 'v' => $resumen['total'],         'i' => 'ri-calendar-event-line', 'a' => '#405189', 'b' => '#e9eef8', 'c' => '#405189'],
                ['l' => 'Desarrolladas',         'v' => $resumen['desarrollados'], 'i' => 'ri-checkbox-circle-line','a' => '#0ab39c', 'b' => '#d4f6ee', 'c' => '#0a7059'],
                ['l' => 'Pendientes',            'v' => $resumen['pendientes'],    'i' => 'ri-time-line',           'a' => '#f7b84b', 'b' => '#fff4d6', 'c' => '#8a6100'],
                ['l' => 'Atrasadas sin iniciar', 'v' => $resumen['atrasados'],     'i' => 'ri-error-warning-line',  'a' => '#d63031', 'b' => '#ffd9d3', 'c' => '#b1240f'],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="col-6 col-md-3">
                <div class="sd-stat-card" style="--sd-accent: {{ $c['a'] }};">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="min-w-0">
                            <div class="sd-stat-label">{{ $c['l'] }}</div>
                            <div class="sd-stat-value">{{ $c['v'] }}</div>
                        </div>
                        <div class="sd-stat-icon" style="background:{{ $c['b'] }}; color:{{ $c['c'] }};">
                            <i class="{{ $c['i'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($resumen['atrasados'] > 0)
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="font-size:.82rem;">
            <i class="ri-alert-line fs-5"></i>
            <div>
                Hay <b>{{ $resumen['atrasados'] }}</b> sesión(es) cuya fecha ya pasó y siguen en estado <b>No Inicio</b>.
                Comunícate con el responsable para regularizar el estado de cada sesión.
            </div>
        </div>
    @endif

    {{-- ============ DESGLOSE POR RESPONSABLE ============ --}}
    <div class="sd-resp-section">
        <div class="sd-resp-section-header">
            <div class="sd-resp-section-title">
                <i class="ri-user-settings-line"></i>
                Detalle por responsable
                <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.65rem;">{{ count($responsables) }}</span>
            </div>
            <div class="sd-resp-section-hint">
                Horarios asignados a cada responsable durante la semana, con estado de los ya transcurridos
            </div>
        </div>

        @if (count($responsables) === 0)
            <div class="sd-resp-empty">
                <i class="ri-inbox-2-line align-middle me-1"></i> No hay horarios asignados en la semana.
            </div>
        @else
            <div class="sd-resp-grid">
                @foreach ($responsables as $r)
                    @php
                        $iniciales = '?';
                        if (!$r['sin_asignar']) {
                            $partes = preg_split('/\s+/', trim($r['nombre']));
                            $iniciales = strtoupper(mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[1] ?? ($partes[0] ?? ''), 0, 1));
                        }
                        $color = $r['sin_asignar'] ? '#878a99' : $r['color'];
                        // Porcentajes para la barra (sobre total)
                        $tot = max(1, $r['total']);
                        $pctDes  = $r['pas_desarrollado'] * 100 / $tot;
                        $pctConf = $r['pas_confirmado']   * 100 / $tot;
                        $pctNI   = $r['pas_no_inicio']    * 100 / $tot;
                        $pctPost = $r['pas_postergado']   * 100 / $tot;
                        $pctOtro = $r['pas_otro']         * 100 / $tot;
                        $pctFut  = $r['futuros']          * 100 / $tot;
                    @endphp
                    <div class="sd-resp-card" style="--sd-resp-color: {{ $color }};">
                        <div class="sd-resp-card-head">
                            <span class="sd-resp-avatar">{{ $iniciales }}</span>
                            <div class="sd-resp-name">
                                {{ $r['nombre'] }}
                                <span class="sd-resp-sub">
                                    {{ $r['pasados'] }} pasada{{ $r['pasados'] === 1 ? '' : 's' }} ·
                                    {{ $r['futuros'] }} por venir
                                </span>
                            </div>
                            <span class="sd-resp-total-pill">{{ $r['total'] }}</span>
                        </div>
                        <div class="sd-resp-body">
                            {{-- Mini calendario semanal --}}
                            <div>
                                <div class="sd-resp-week">
                                    @php $diasAbr = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']; @endphp
                                    @foreach ($r['por_dia'] as $fechaDia => $infoDia)
                                        @php
                                            $td = strtotime($fechaDia);
                                            $dowIdx = (int) date('N', $td) - 1;
                                            $esHoyDia = $fechaDia === $hoy;
                                            $tieneParalelo = $infoDia['paralelo'] > 1;
                                            $cls = '';
                                            if ($infoDia['count'] === 0) $cls .= ' is-empty';
                                            if ($esHoyDia) $cls .= ' is-today';
                                            if ($tieneParalelo) $cls .= ' has-parallel';
                                        @endphp
                                        <div class="day{{ $cls }}"
                                             title="{{ $diasAbr[$dowIdx] }} {{ date('j', $td) }} · {{ $infoDia['count'] }} sesión{{ $infoDia['count'] === 1 ? '' : 'es' }}@if ($tieneParalelo) · {{ $infoDia['paralelo'] }} en paralelo @endif">
                                            <span class="dow">{{ $diasAbr[$dowIdx] }}</span>
                                            <span class="num">{{ date('j', $td) }}</span>
                                            <span class="cnt">{{ $infoDia['count'] }}</span>
                                            @if ($tieneParalelo)
                                                <span class="par-flag" title="{{ $infoDia['paralelo'] }} horarios solapados en tiempo">×{{ $infoDia['paralelo'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="sd-resp-week-foot">
                                    <span><i class="ri-calendar-event-line align-bottom me-1"></i><span class="total-week">{{ $r['total'] }}</span> en la semana</span>
                                    @if ($r['dias_con_paralelo'] > 0)
                                        <span class="par-badge" title="Días con sesiones que se solapan en tiempo">
                                            <i class="ri-stack-line"></i>
                                            {{ $r['dias_con_paralelo'] }} día{{ $r['dias_con_paralelo'] === 1 ? '' : 's' }} con paralelo
                                            (máx ×{{ $r['max_paralelo_semana'] }})
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Barra apilada --}}
                            <div class="sd-resp-bar" title="Composición de la semana">
                                @if ($pctDes  > 0) <span class="bs-desarrollado" style="width: {{ $pctDes }}%;"  title="Desarrollado: {{ $r['pas_desarrollado'] }}"></span> @endif
                                @if ($pctConf > 0) <span class="bs-confirmado"   style="width: {{ $pctConf }}%;" title="Confirmado pasado: {{ $r['pas_confirmado'] }}"></span> @endif
                                @if ($pctNI   > 0) <span class="bs-noinicio"     style="width: {{ $pctNI }}%;"   title="No Inicio pasado: {{ $r['pas_no_inicio'] }}"></span> @endif
                                @if ($pctPost > 0) <span class="bs-postergado"   style="width: {{ $pctPost }}%;" title="Postergado pasado: {{ $r['pas_postergado'] }}"></span> @endif
                                @if ($pctOtro > 0) <span class="bs-otro"         style="width: {{ $pctOtro }}%;" title="Otros pasados: {{ $r['pas_otro'] }}"></span> @endif
                                @if ($pctFut  > 0) <span class="bs-futuro"       style="width: {{ $pctFut }}%;"  title="Por venir: {{ $r['futuros'] }}"></span> @endif
                            </div>

                            {{-- Resumen pasados / por venir --}}
                            <div class="sd-resp-row">
                                <span class="label" style="--dot:#6c757d;">Pasadas</span>
                                <span class="val">{{ $r['pasados'] }} / {{ $r['total'] }}</span>
                            </div>

                            {{-- Chips de estado de las pasadas --}}
                            @if ($r['pasados'] > 0)
                                <div class="sd-resp-chips">
                                    @if ($r['pas_desarrollado'] > 0)
                                        <span class="sd-resp-chip c-desarrollado"><i class="ri-checkbox-circle-line"></i> Desarrollado · {{ $r['pas_desarrollado'] }}</span>
                                    @endif
                                    @if ($r['pas_confirmado'] > 0)
                                        <span class="sd-resp-chip c-confirmado"><i class="ri-time-line"></i> Confirmado · {{ $r['pas_confirmado'] }}</span>
                                    @endif
                                    @if ($r['pas_no_inicio'] > 0)
                                        <span class="sd-resp-chip c-noinicio"><i class="ri-alarm-warning-line"></i> No Inicio · {{ $r['pas_no_inicio'] }}</span>
                                    @endif
                                    @if ($r['pas_postergado'] > 0)
                                        <span class="sd-resp-chip c-postergado"><i class="ri-calendar-event-line"></i> Postergado · {{ $r['pas_postergado'] }}</span>
                                    @endif
                                    @if ($r['pas_otro'] > 0)
                                        <span class="sd-resp-chip"><i class="ri-question-line"></i> Otros · {{ $r['pas_otro'] }}</span>
                                    @endif
                                </div>
                            @else
                                <div class="sd-resp-row text-muted" style="font-style:italic;">
                                    <span>Ninguna sesión pasada todavía esta semana</span>
                                </div>
                            @endif

                            {{-- Asistencias de las Desarrolladas --}}
                            @if ($r['pas_desarrollado'] > 0)
                                <div class="sd-resp-sub-block">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span><i class="ri-team-line"></i> Asistencia de las Desarrolladas</span>
                                        <b>{{ $r['des_con_asist'] }} / {{ $r['pas_desarrollado'] }}</b>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="sd-resp-chip c-asistok">
                                            <i class="ri-checkbox-circle-line"></i> Con asistencia · {{ $r['des_con_asist'] }}
                                        </span>
                                        @if ($r['des_sin_asist'] > 0)
                                            <span class="sd-resp-chip c-asistmiss">
                                                <i class="ri-information-line"></i> Pendientes · {{ $r['des_sin_asist'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ DÍAS ============ --}}
    @foreach ($dias as $fecha => $items)
        @php
            $t = strtotime($fecha);
            $diaIdx = (int) date('N', $t) - 1;
            $esHoy = $fecha === $hoy;
            $countDia = $items->count();
            $atrasadosDia = $items->where('atrasado', true)->count();
        @endphp
        <div class="sd-day">
            <div class="sd-day-header">
                <div class="sd-day-pill {{ $esHoy ? 'is-today' : '' }}">
                    <span class="sd-day-num">{{ date('j', $t) }}</span>
                    <span class="sd-day-name">{{ substr($diasNombres[$diaIdx], 0, 3) }}</span>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color:#2a2f43;">
                        {{ $diasNombres[$diaIdx] }}
                        <span class="text-muted fw-normal ms-1" style="font-size:.78rem;">· {{ $fmtFechaCorta($fecha) }}</span>
                        @if ($esHoy)
                            <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.65rem;">HOY</span>
                        @endif
                    </div>
                    <div class="sd-day-count">
                        {{ $countDia }} {{ $countDia === 1 ? 'sesión' : 'sesiones' }}
                        @if ($atrasadosDia > 0)
                            · <span class="text-danger fw-semibold">{{ $atrasadosDia }} atrasada{{ $atrasadosDia === 1 ? '' : 's' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @if ($countDia === 0)
                <div class="sd-day-empty">
                    <i class="ri-inbox-2-line align-middle me-1"></i> Sin sesiones programadas para este día
                </div>
            @else
                @foreach ($items as $h)
                    @php
                        $est = trim((string) $h->estado);
                        $styEst = $estadoStyle($est);
                        $accent = $rowAccent($est);
                        $esPasada = $h->fecha_desarrollo < $hoy;
                        $esDesarrollado = $est === 'Desarrollado';
                        $esConfirmado = $est === 'Confirmado';
                        $tieneObs = !empty(trim((string) $h->observaciones));
                        $tieneAsist = isset($asistenciasPorHorario[$h->id]) && $asistenciasPorHorario[$h->id]->count() > 0;
                        // Sólo la asistencia se considera obligatoria; las observaciones son opcionales
                        $faltaRegistro = $esDesarrollado && !$tieneAsist;

                        // Estado de tarjeta (clases visuales)
                        $rowClasses = [];
                        $modalKind = '';      // late | warn | info | (none)
                        $btnKind = '';        // is-danger | is-warn | is-info | ''
                        $btnLabel = 'Ver detalle';
                        $flagHtml = '';
                        $alertHtml = '';

                        if ($h->atrasado) {
                            $rowClasses[] = 'is-late';
                            $modalKind = 'is-late';
                            $btnKind = 'is-danger';
                            $btnLabel = 'Atender ahora';
                            $flagHtml = '<span class="sd-flag is-danger"><i class="ri-alarm-warning-line"></i> Atrasado</span>';
                            $alertHtml = '<div class="sd-modal-alert is-danger"><i class="ri-alarm-warning-line"></i><div><b>Sesión atrasada.</b> La fecha ya pasó y el horario sigue en <b>No Inicio</b>. Solicita al responsable que confirme o desarrolle esta sesión.</div></div>';
                        } elseif ($esConfirmado && $esPasada) {
                            $rowClasses[] = 'is-confirm-late';
                            $modalKind = 'is-warn';
                            $btnKind = 'is-warn';
                            $btnLabel = 'No cambió estado de la sesión';
                            $flagHtml = '<span class="sd-flag is-warn"><i class="ri-time-line"></i> Sin actualizar</span>';
                            $alertHtml = '<div class="sd-modal-alert is-warn"><i class="ri-time-line"></i><div><b>El responsable no cambió el estado de la sesión.</b> La fecha ya pasó pero el horario sigue en <b>Confirmado</b>. Solicita que actualice el estado a Desarrollado o Postergado según corresponda.</div></div>';
                        } elseif ($esConfirmado) {
                            $rowClasses[] = 'is-confirm';
                        } elseif ($faltaRegistro) {
                            $rowClasses[] = 'is-missing';
                            $modalKind = 'is-info';
                            $btnKind = 'is-info';
                            $btnLabel = 'Falta registrar asistencia';
                            $flagHtml = '<span class="sd-flag is-info"><i class="ri-information-line"></i> Falta asistencia</span>';
                            $alertHtml = '<div class="sd-modal-alert is-info"><i class="ri-information-line"></i><div><b>Sesión marcada como desarrollada pero falta registrar la lista de asistencia.</b> Solicita al responsable que complete la información.</div></div>';
                        } elseif ($esDesarrollado && $tieneAsist) {
                            // Desarrollado con asistencia: mostrar si también hay observaciones
                            $btnLabel = $tieneObs ? 'Con asistencia y observaciones' : 'Con asistencia';
                        }
                        $rowClassStr = implode(' ', $rowClasses);

                        $collapseId = 'sdDetail_' . $h->id;
                        $tplId = 'sdTpl_' . $h->id;
                        $asignadoColor = $h->asignado_hex ? ('#' . ltrim($h->asignado_hex, '#')) : '#6c757d';
                        $programaUrl = $h->programa_id ? route('admin.programas.show', $h->programa_id) : null;
                        $docenteNombre = trim(($h->docente_nombres ?? '') . ' ' . ($h->docente_apellidos ?? ''));
                        $asignadoNombre = trim(($h->asignado_nombres ?? '') . ' ' . ($h->asignado_apellidos ?? ''));
                        $tituloModal = ($h->programa_nombre ?? 'Programa') . ($h->programa_codigo ? ' · ' . $h->programa_codigo : '');
                        $subtituloModal = $fmtFechaLarga($h->fecha_desarrollo) . ' · ' . $fmtHora($h->hora_inicio) . ' a ' . $fmtHora($h->hora_fin);
                    @endphp
                    <div class="sd-h-row {{ $rowClassStr }}" style="--sd-row-accent: {{ $accent }};">
                        <div class="sd-h-time">
                            <span class="h-i">{{ $fmtHora($h->hora_inicio) }}</span>
                            <span class="h-s">a {{ $fmtHora($h->hora_fin) }}</span>
                        </div>
                        <div class="sd-h-body">
                            <div class="sd-h-title">
                                @if ($programaUrl)
                                    <a href="{{ $programaUrl }}" class="sd-link" title="Ir al programa">
                                        {{ $h->programa_nombre ?? '— Programa sin nombre' }}
                                    </a>
                                @else
                                    {{ $h->programa_nombre ?? '— Programa sin nombre' }}
                                @endif
                                @if (!empty($h->programa_codigo))
                                    @if ($programaUrl)
                                        <a href="{{ $programaUrl }}" class="sd-codigo" style="text-decoration:none;">{{ $h->programa_codigo }}</a>
                                    @else
                                        <span class="sd-codigo">{{ $h->programa_codigo }}</span>
                                    @endif
                                @endif
                            </div>
                            <div class="sd-h-modulo">
                                <i class="ri-book-2-line align-bottom text-muted me-1"></i>
                                @if ($programaUrl)
                                    <a href="{{ $programaUrl }}" class="sd-link" title="Ir al programa / módulo">
                                        {{ $h->modulo_nombre ?? '— Módulo —' }}
                                    </a>
                                @else
                                    {{ $h->modulo_nombre ?? '— Módulo —' }}
                                @endif
                            </div>
                            <div class="sd-h-meta">
                                <span class="sd-meta-i" title="Docente">
                                    <i class="ri-user-star-line"></i>
                                    <b>Docente:</b>
                                    @if ($docenteNombre !== '')
                                        {{ $docenteNombre }}
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </span>
                                <span class="sd-meta-i" title="Responsable de la sesión">
                                    <i class="ri-user-settings-line"></i>
                                    <b>Responsable:</b>
                                    @if ($asignadoNombre !== '')
                                        <span class="sd-asignado-chip" style="background: {{ $asignadoColor }};">
                                            <i class="ri-user-line"></i>
                                            {{ $asignadoNombre }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </span>
                                @if (!empty($h->tipo_nombre))
                                    <span class="sd-meta-i" title="Tipo de programa">
                                        <i class="ri-price-tag-3-line"></i>{{ $h->tipo_nombre }}
                                    </span>
                                @endif
                                @if (!empty($h->gestion))
                                    <span class="sd-meta-i" title="Gestión">
                                        <i class="ri-calendar-line"></i>{{ $h->gestion }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="sd-h-side">
                            <div class="sd-h-estado">
                                <span class="sd-pill" style="--sd-pill-bg: {{ $styEst['bg'] }}; --sd-pill-color: {{ $styEst['color'] }};">
                                    {{ $est ?: '—' }}
                                </span>
                            </div>
                            {!! $flagHtml !!}
                            <button type="button"
                                    class="sd-btn-toggle {{ $btnKind }} sd-open-modal"
                                    data-horario-id="{{ $h->id }}"
                                    data-modal-kind="{{ $modalKind }}"
                                    data-modal-title="{{ $tituloModal }}"
                                    data-modal-subtitle="{{ $subtituloModal }}">
                                <i class="ri-eye-line"></i> {{ $btnLabel }}
                            </button>
                        </div>
                    </div>

                    {{-- Template oculto con el contenido del modal --}}
                    <div class="sd-tpl" id="{{ $tplId }}">
                        {!! $alertHtml !!}

                        <div class="sd-modal-info-grid">
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-graduation-cap-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Programa</div>
                                    <div class="sd-info-value">
                                        @if ($programaUrl)
                                            <a href="{{ $programaUrl }}">{{ $h->programa_nombre ?? '—' }}</a>
                                        @else
                                            {{ $h->programa_nombre ?? '—' }}
                                        @endif
                                        @if (!empty($h->programa_codigo))
                                            <span class="text-muted">· {{ $h->programa_codigo }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-book-2-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Módulo</div>
                                    <div class="sd-info-value">
                                        @if ($programaUrl)
                                            <a href="{{ $programaUrl }}">{{ $h->modulo_nombre ?? '—' }}</a>
                                        @else
                                            {{ $h->modulo_nombre ?? '—' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-user-star-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Docente</div>
                                    <div class="sd-info-value">
                                        @if ($docenteNombre !== '')
                                            {{ $docenteNombre }}
                                            @if (!empty($h->docente_ci))
                                                <span class="text-muted">· CI {{ $h->docente_ci }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-shield-user-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Responsable</div>
                                    <div class="sd-info-value">
                                        @if ($asignadoNombre !== '')
                                            <span class="sd-asignado-chip" style="background: {{ $asignadoColor }};">
                                                <i class="ri-user-line"></i>{{ $asignadoNombre }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-calendar-2-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Fecha y horario</div>
                                    <div class="sd-info-value">
                                        {{ $fmtFechaLarga($h->fecha_desarrollo) }}<br>
                                        <span class="text-muted"><i class="ri-time-line"></i> {{ $fmtHora($h->hora_inicio) }} a {{ $fmtHora($h->hora_fin) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="sd-info-cell">
                                <div class="sd-info-ico"><i class="ri-pulse-line"></i></div>
                                <div style="min-width:0;">
                                    <div class="sd-info-label">Estado</div>
                                    <div class="sd-info-value">
                                        <span class="sd-pill" style="--sd-pill-bg: {{ $styEst['bg'] }}; --sd-pill-color: {{ $styEst['color'] }};">
                                            {{ $est ?: '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="sd-detail-title"><i class="ri-message-2-line"></i> Observaciones</div>
                            <div class="sd-obs-box {{ $tieneObs ? '' : 'sd-obs-empty' }}">
                                {{ $tieneObs ? $h->observaciones : 'Sin observaciones registradas.' }}
                            </div>
                        </div>

                        <div class="sd-detail-title">
                            <i class="ri-team-line"></i> Lista de asistencia
                            @if ($tieneAsist)
                                <span class="badge bg-soft-primary text-primary ms-1">
                                    {{ $asistenciasPorHorario[$h->id]->count() }} participantes
                                </span>
                            @endif
                        </div>
                        @if (!$tieneAsist)
                            <div class="sd-obs-box sd-obs-empty">
                                Sin registros de asistencia — solicita al responsable que registre la lista.
                            </div>
                        @else
                            @php
                                $asis = $asistenciasPorHorario[$h->id];
                                $totalAsis = $asis->count();
                                $resumenAsis = $asis->groupBy(fn ($a) => trim($a->asistio ?? ''))->map->count();
                                $asistCfg = [
                                    'Presente'    => ['ico' => 'ri-checkbox-circle-line', 'bg' => '#d4f6ee', 'color' => '#0a7059'],
                                    'Atraso'      => ['ico' => 'ri-time-line',            'bg' => '#fff4d6', 'color' => '#8a6100'],
                                    'Falta'       => ['ico' => 'ri-close-circle-line',    'bg' => '#ffd9d3', 'color' => '#b1240f'],
                                    'Justificado' => ['ico' => 'ri-file-shield-2-line',   'bg' => '#dde7ff', 'color' => '#1c3e9e'],
                                ];
                                $statKeys = array_merge(
                                    array_values(array_filter(array_keys($asistCfg), fn ($k) => isset($resumenAsis[$k]))),
                                    array_values(array_diff($resumenAsis->keys()->all(), array_keys($asistCfg)))
                                );
                                $avatarColors = ['#405189', '#0ab39c', '#299cdb', '#f06548', '#f7b84b', '#6559cc', '#02a8b5', '#d63384'];
                            @endphp
                            <div class="sd-asist-stats">
                                @foreach ($statKeys as $tipo)
                                    @php
                                        $cfg = $asistCfg[$tipo] ?? ['ico' => 'ri-question-line', 'bg' => '#eef0f5', 'color' => '#5d6478'];
                                        $cant = $resumenAsis[$tipo] ?? 0;
                                    @endphp
                                    <div class="sd-stat">
                                        <div class="sd-stat-ico" style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};"><i class="{{ $cfg['ico'] }}"></i></div>
                                        <div>
                                            <div class="num">{{ $cant }}</div>
                                            <div class="lbl">{{ $tipo }}</div>
                                        </div>
                                        <div class="pct">{{ $totalAsis > 0 ? round($cant * 100 / $totalAsis) : 0 }}%</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="sd-asist-wrap">
                                <div class="sd-asist-toolbar">
                                    <div class="sd-asist-search">
                                        <i class="ri-search-line"></i>
                                        <input type="text" class="sd-asist-search-input" placeholder="Buscar por nombre o CI..." autocomplete="off">
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="sd-asist-countlbl sd-asist-count">{{ $totalAsis }} de {{ $totalAsis }}</span>
                                        <a class="btn btn-sm btn-soft-danger d-inline-flex align-items-center gap-1"
                                           href="{{ route('admin.programas.asistencia-pdf', $h->id) }}"
                                           title="Descargar reporte de asistencia en PDF">
                                            <i class="ri-file-pdf-line"></i> Reporte PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="sd-asist-scroll">
                                    <table class="sd-asist-table">
                                        <thead>
                                            <tr>
                                                <th style="width:38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width:120px;" class="text-center">Asistencia</th>
                                                <th style="width:28%;">Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($asis as $idx => $a)
                                                @php
                                                    $nombreCompleto = trim(($a->apellidos ?? '') . ', ' . ($a->nombres ?? ''), ', ');
                                                    $iniciales = mb_strtoupper(mb_substr($a->nombres ?? '?', 0, 1) . mb_substr($a->apellidos ?? '', 0, 1));
                                                    $colorAvatar = $avatarColors[crc32($nombreCompleto) % count($avatarColors)];
                                                @endphp
                                                <tr data-search="{{ mb_strtolower($nombreCompleto . ' ' . ($a->ci ?? '')) }}">
                                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <div class="sd-part-cell">
                                                            <span class="sd-avatar" style="background: {{ $colorAvatar }};">{{ $iniciales }}</span>
                                                            <div style="min-width:0;">
                                                                <div class="sd-part-name">{{ $nombreCompleto }}</div>
                                                                <div class="sd-part-ci">CI: {{ $a->ci ?: '—' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="a-pill a-{{ trim($a->asistio) }}">{{ $a->asistio }}</span>
                                                    </td>
                                                    <td class="sd-asist-obs">{{ $a->observacion ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

    {{-- ============ MODAL DETALLE ============ --}}
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
                <div class="modal-body" id="sdModalBody">
                    {{-- Contenido inyectado --}}
                </div>
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

</div>
</div>{{-- /acad-page --}}
@endsection

@push('js')
<script>
(function () {
    const modalEl = document.getElementById('sdModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const titleEl = document.getElementById('sdModalTitle');
    const subtitleEl = document.getElementById('sdModalSubtitle');
    const bodyEl = document.getElementById('sdModalBody');
    const gotoEl = document.getElementById('sdModalGoto');

    const KIND_CLASSES = ['is-late', 'is-warn', 'is-info'];

    document.querySelectorAll('.sd-open-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id    = btn.getAttribute('data-horario-id');
            const kind  = btn.getAttribute('data-modal-kind') || '';
            const ttl   = btn.getAttribute('data-modal-title') || 'Detalle del horario';
            const stl   = btn.getAttribute('data-modal-subtitle') || '';

            const tpl = document.getElementById('sdTpl_' + id);
            if (!tpl) return;

            // Header color por tipo
            KIND_CLASSES.forEach(c => modalEl.classList.remove(c));
            if (kind) modalEl.classList.add(kind);

            titleEl.textContent = ttl;
            subtitleEl.textContent = stl;

            // Clonar contenido del template
            bodyEl.innerHTML = '';
            const clone = tpl.cloneNode(true);
            clone.classList.remove('sd-tpl');
            clone.removeAttribute('id');
            clone.style.display = '';
            bodyEl.appendChild(clone);

            // Link "Abrir programa" en footer
            const programaLink = bodyEl.querySelector('.sd-info-value a[href]');
            if (programaLink) {
                gotoEl.href = programaLink.getAttribute('href');
                gotoEl.style.display = '';
            } else {
                gotoEl.removeAttribute('href');
                gotoEl.style.display = 'none';
            }

            modal.show();
        });
    });

    // Buscador de participantes en la lista de asistencia (delegado, el contenido se clona)
    bodyEl.addEventListener('input', function (ev) {
        if (!ev.target.classList.contains('sd-asist-search-input')) return;
        const q = ev.target.value.toLowerCase().trim();
        const rows = bodyEl.querySelectorAll('.sd-asist-table tbody tr');
        let visibles = 0;
        rows.forEach(function (r) {
            const show = !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
            r.style.display = show ? '' : 'none';
            if (show) visibles++;
        });
        const cnt = bodyEl.querySelector('.sd-asist-count');
        if (cnt) cnt.textContent = visibles + ' de ' + rows.length;
    });
})();
</script>
@endpush
