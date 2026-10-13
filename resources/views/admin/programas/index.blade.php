@extends('admin.layouts.master')

@section('title', 'Programas - ' . config('app.name'))

@push('css')
<style>
/* ===== Hero / Page Header ===== */
.programas-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.programas-hero::before {
    content: "";
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.programas-hero::after {
    content: "";
    position: absolute;
    bottom: -60px; left: 30%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%);
    border-radius: 50%;
}
.programas-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.programas-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; }
.programas-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .9rem; margin: 0; }
.programas-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.programas-hero .breadcrumb-item, .programas-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.programas-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.programas-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

/* ===== Filtros Card ===== */
.filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .filter-card {
    background: #1d2125;
    border-color: #2a2f34;
}
.filter-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: #2a2f34; }
.filter-card .form-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    color: #878a99;
    margin-bottom: .35rem;
}
.filter-card .input-group-text {
    background: #fff;
    border: 1px solid #e9ebec;
    border-right: 0;
    color: #878a99;
}
.filter-card .form-select {
    border: 1px solid #e9ebec;
    background-color: #fff;
    font-weight: 500;
    transition: all .2s ease;
}
.filter-card .form-select:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}
.filter-card .input-group:focus-within .input-group-text {
    border-color: #405189;
    color: #405189;
}
[data-layout-mode="dark"] .filter-card .input-group-text,
[data-layout-mode="dark"] .filter-card .form-select {
    background-color: #1d2125;
    border-color: #2a2f34;
    color: #ced4da;
}
.btn-nuevo {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0;
    color: #fff;
    font-weight: 600;
    letter-spacing: .3px;
    box-shadow: 0 6px 18px -8px rgba(10,179,156,.6);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-nuevo:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 10px 22px -10px rgba(10,179,156,.75); }

/* ===== Tabs ===== */
.tabs-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
.tabs-card > .card-body { padding: 1rem 1rem 1.25rem; }
[data-layout-mode="dark"] .tabs-card { border-color: #2a2f34; }
.nav-tabs-pill {
    border: 0;
    gap: .35rem;
    background: #f3f5f9;
    padding: .35rem;
    border-radius: 12px;
    display: inline-flex;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .nav-tabs-pill { background: #212529; }
.nav-tabs-pill .nav-link {
    border: 0 !important;
    border-radius: 9px;
    color: #495057;
    font-weight: 600;
    font-size: .85rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: all .2s ease;
}
.nav-tabs-pill .nav-link:hover { color: #405189; background: rgba(64,81,137,.06); }
.nav-tabs-pill .nav-link.active {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff !important;
    box-shadow: 0 6px 14px -6px rgba(64,81,137,.55);
}
[data-layout-mode="dark"] .nav-tabs-pill .nav-link { color: #ced4da; }

.tab-header-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1rem;
}
.programa-count {
    font-size: .8rem;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%) !important;
    color: #fff;
    padding: .35em .7em;
    border-radius: 999px;
    font-weight: 700;
}

.tab-content-loader { text-align: center; padding: 3rem; }
.is-invalid { border-color: #f06548 !important; }
.invalid-feedback { display: block; }

/* ===== Tab Stats Row ===== */
.tab-stats-row {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding: .9rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    border: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .tab-stats-row {
    background: #212529;
    border-color: #2a2f34;
}
.tab-stat-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .85rem;
}
.tab-stat-item .num {
    font-weight: 700;
    font-size: 1.1rem;
}

/* ===== Modal Programa ===== */
#modalPrograma .modal-content,
#modalDeletePrograma .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalPrograma .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalPrograma .modal-header::after {
    content: "";
    position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalPrograma .modal-header > * { position: relative; z-index: 1; }
#modalPrograma .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalPrograma .modal-title { font-weight: 700; color: #fff; letter-spacing: .2px; }
#modalPrograma .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalPrograma .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }

/* Modal con scroll fijo en el body, header + footer siempre visibles */
#modalPrograma .modal-dialog {
    max-height: calc(100vh - 2rem);
    margin-top: 1rem;
    margin-bottom: 1rem;
}
#modalPrograma .modal-content {
    max-height: calc(100vh - 2rem);
    display: flex !important;
    flex-direction: column !important;
}
#modalPrograma .modal-header,
#modalPrograma .modal-footer { flex-shrink: 0; }
#modalPrograma form { display: flex; flex-direction: column; min-height: 0; flex: 1; overflow: hidden; }
#modalPrograma .modal-body {
    padding: 1.5rem;
    background: #fafbfd;
    overflow-y: auto !important;
    flex: 1 1 auto;
    min-height: 0;
}
/* Scrollbar bonita */
#modalPrograma .modal-body::-webkit-scrollbar { width: 8px; }
#modalPrograma .modal-body::-webkit-scrollbar-thumb { background: #c3cfe2; border-radius: 4px; }
#modalPrograma .modal-body::-webkit-scrollbar-thumb:hover { background: #405189; }
[data-layout-mode="dark"] #modalPrograma .modal-body { background: #1a1d20; }

/* Secciones del formulario */
.form-section {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    margin-bottom: 1rem;
    transition: box-shadow .2s ease;
}
.form-section:hover { box-shadow: 0 4px 14px -8px rgba(64,81,137,.18); }
.form-section:last-child { margin-bottom: 0; }
[data-layout-mode="dark"] .form-section { background: #212529; border-color: #2a2f34; }

.form-section-title {
    display: flex;
    align-items: center;
    gap: .6rem;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: .65rem;
    border-bottom: 1px dashed #e9ebec;
}
[data-layout-mode="dark"] .form-section-title { color: #ced4da; border-bottom-color: #2a2f34; }

.form-section-icon {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 9px;
    font-size: 1rem;
}

#modalPrograma .form-label {
    font-weight: 600;
    font-size: .76rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-bottom: .35rem;
}
[data-layout-mode="dark"] #modalPrograma .form-label { color: #adb5bd; }

#modalPrograma .input-group-text {
    background: #f8f9fc;
    border: 1px solid #e9ebec;
    border-right: 0;
    color: #878a99;
    font-weight: 500;
}
[data-layout-mode="dark"] #modalPrograma .input-group-text { background: #1d2125; border-color: #2a2f34; color: #adb5bd; }

#modalPrograma .form-control,
#modalPrograma .form-select {
    border-radius: 8px;
    border: 1px solid #e9ebec;
    transition: all .2s ease;
    font-size: .9rem;
}
#modalPrograma .input-group .form-control,
#modalPrograma .input-group .form-select {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
#modalPrograma .form-control:focus,
#modalPrograma .form-select:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}
#modalPrograma .input-group:focus-within .input-group-text {
    border-color: #405189;
    color: #405189;
    background: #fff;
}

#modalPrograma select[multiple].form-select {
    padding: .35rem .5rem;
}
#modalPrograma select[multiple].form-select option {
    padding: .35rem .5rem;
    border-radius: 4px;
}

#modalPrograma .form-switch .form-check-input {
    width: 2.4em; height: 1.25em;
    cursor: pointer;
}
#modalPrograma .form-switch .form-check-input:checked {
    background-color: #0ab39c;
    border-color: #0ab39c;
}

#modalPrograma .modal-footer {
    background: #fff;
    border-top: 1px solid #eef0f4;
    padding: 1rem 1.5rem;
}
[data-layout-mode="dark"] #modalPrograma .modal-footer { background: #212529; border-top-color: #2a2f34; }

.btn-save-programa {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0;
    color: #fff;
    font-weight: 600;
    padding: .5rem 1.25rem;
    box-shadow: 0 6px 16px -6px rgba(10,179,156,.55);
    transition: transform .15s ease, box-shadow .15s ease;
}
.btn-save-programa:hover:not(:disabled) {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 22px -8px rgba(10,179,156,.7);
}
.btn-save-programa:disabled { opacity: .7; }

/* ===== Responsables Picker (reusable) ===== */
.resp-picker {
    border: 1px solid #e9ebec;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}
[data-layout-mode="dark"] .resp-picker { background: #1d2125; border-color: #2a2f34; }
.resp-picker-chips {
    min-height: 42px;
    padding: .5rem .65rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex; flex-wrap: wrap; gap: .4rem;
    align-items: center;
}
[data-layout-mode="dark"] .resp-picker-chips { background: #1a1d20; border-bottom-color: #2a2f34; }
.resp-picker-chips:empty::before {
    content: "Sin responsables seleccionados";
    color: #adb5bd;
    font-size: .8rem;
    font-style: italic;
}
.resp-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #fff;
    border: 1px solid;
    border-radius: 999px;
    padding: .25em .35em .25em .25em;
    font-size: .78rem;
    font-weight: 600;
    box-shadow: 0 2px 6px -2px rgba(0,0,0,.08);
}
.resp-chip-avatar {
    width: 22px; height: 22px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: .62rem;
    flex-shrink: 0;
}
.resp-chip-x {
    width: 18px; height: 18px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,.06);
    color: #495057;
    cursor: pointer;
    transition: all .15s ease;
    font-size: .7rem;
}
.resp-chip-x:hover { background: #f06548; color: #fff; }

.resp-picker-search {
    position: relative;
    padding: .5rem .65rem;
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .resp-picker-search { border-bottom-color: #2a2f34; }
.resp-picker-search i {
    position: absolute;
    left: 1.2rem; top: 50%;
    transform: translateY(-50%);
    color: #878a99;
    font-size: 1rem;
}
.resp-picker-search input {
    width: 100%;
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: .45rem .75rem .45rem 2rem;
    font-size: .85rem;
    transition: all .2s ease;
    background: #fff;
}
.resp-picker-search input:focus {
    outline: 0;
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}
[data-layout-mode="dark"] .resp-picker-search input { background: #1d2125; border-color: #2a2f34; color: #ced4da; }

.resp-picker-list {
    max-height: 260px;
    overflow-y: auto;
    padding: .25rem;
}
.resp-picker-list::-webkit-scrollbar { width: 6px; }
.resp-picker-list::-webkit-scrollbar-thumb { background: #d6dbe0; border-radius: 3px; }
.resp-item {
    display: flex; align-items: center; gap: .65rem;
    padding: .55rem .75rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all .15s ease;
}
.resp-item:hover { background: #f8f9fc; }
.resp-item.selected {
    background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%);
}
.resp-item.selected::after {
    content: "\eb7b";
    font-family: "remixicon";
    margin-left: auto;
    color: #0ab39c;
    font-size: 1.15rem;
}
[data-layout-mode="dark"] .resp-item:hover { background: #212529; }
[data-layout-mode="dark"] .resp-item.selected { background: rgba(64,81,137,.18); }

.resp-item-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: .75rem;
    flex-shrink: 0;
    box-shadow: 0 3px 8px -3px rgba(0,0,0,.2);
}
.resp-item-info { min-width: 0; flex: 1; }
.resp-item-nombre { font-weight: 600; color: #212529; font-size: .85rem; line-height: 1.2; }
.resp-item-meta { font-size: .7rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .resp-item-nombre { color: #fff; }

.resp-empty { text-align: center; padding: 1.5rem; color: #878a99; font-size: .82rem; }

/* ===== Modal Asignar Responsables ===== */
#modalAsignarResp .modal-content {
    border: 0; border-radius: 16px; overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalAsignarResp .modal-header {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%);
    color: #fff; border: 0;
    padding: 1.1rem 1.5rem;
    position: relative; overflow: hidden;
}
#modalAsignarResp .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px; width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalAsignarResp .modal-header > * { position: relative; z-index: 1; }
#modalAsignarResp .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem; color: #fff;
}
#modalAsignarResp .modal-title { color: #fff; font-weight: 700; }
#modalAsignarResp .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalAsignarResp .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalAsignarResp .modal-body { padding: 1.25rem; background: #fafbfd; }
[data-layout-mode="dark"] #modalAsignarResp .modal-body { background: #1a1d20; }
#modalAsignarResp .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }
[data-layout-mode="dark"] #modalAsignarResp .modal-footer { background: #212529; border-top-color: #2a2f34; }

/* Modal Eliminar */
.delete-icon-wrap {
    width: 80px; height: 80px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #fff5f3 0%, #ffe7e2 100%);
    color: #f06548;
    border-radius: 50%;
    font-size: 2.5rem;
    box-shadow: 0 0 0 8px rgba(240,101,72,.08);
    animation: pulseDelete 2s ease-in-out infinite;
}
@keyframes pulseDelete {
    0%, 100% { box-shadow: 0 0 0 8px rgba(240,101,72,.08); }
    50% { box-shadow: 0 0 0 14px rgba(240,101,72,.04); }
}
[data-layout-mode="dark"] .delete-icon-wrap { background: linear-gradient(135deg, #2a1f1d 0%, #3a2522 100%); }

.tipo-badge {
    font-size: .75rem;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
}

/* ===== Tabs ===== */
.tabs-card .nav-tabs-pill {
    display: flex; flex-wrap: wrap; gap: .35rem;
    background: var(--ui-surface-2);
    padding: .35rem; border-radius: 12px; margin-bottom: 1rem;
    border: 1px solid var(--ui-line);
}
.tabs-card .nav-tabs-pill .nav-link {
    display: inline-flex; align-items: center; gap: .45rem;
    border: 0 !important; border-radius: 9px;
    font-weight: 600; font-size: .82rem; padding: .5rem .9rem;
    color: var(--ui-ink-2); background: transparent;
    transition: background-color .15s ease, color .15s ease;
}
.tabs-card .nav-tabs-pill .nav-link:hover { color: var(--ui-accent); background: rgba(64,81,137,.06); }
.tabs-card .nav-tabs-pill .nav-link.active {
    background: #405189 !important; color: #fff !important;
    box-shadow: 0 6px 14px -8px rgba(64,81,137,.6);
}
.tabs-card .nav-tabs-pill .nav-link .tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; padding: .1em .5em; border-radius: 999px;
    font-size: .68rem; font-weight: 700;
    background: rgba(64,81,137,.1); color: var(--ui-accent);
}
.tabs-card .nav-tabs-pill .nav-link.active .tab-count { background: rgba(255,255,255,.22); color: #fff; }
.tab-header-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: .9rem; flex-wrap: wrap; }

/* ===== Tabla Programas (compacta, sin scroll) ===== */
.programas-table { table-layout: fixed; width: 100%; margin: 0; }
.programas-table th, .programas-table td {
    vertical-align: middle;
    padding: .7rem .6rem;
}
.programas-table th {
    font-weight: 700;
    letter-spacing: .4px;
    font-size: .64rem;
    text-transform: uppercase;
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
.programas-table td { overflow: hidden; }

.pv-prog-name {
    display: block; font-weight: 600; font-size: .86rem; line-height: 1.35;
    color: var(--ui-ink); text-decoration: none;
    white-space: normal; overflow-wrap: anywhere;
}
.pv-prog-name:hover { color: var(--ui-accent); }
.pv-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 5px; }
.mini-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: .62rem; font-weight: 600; line-height: 1;
    padding: .3em .55em; border-radius: 6px;
    background: var(--ui-surface-2); color: var(--ui-ink-2);
    border: 1px solid var(--ui-line); white-space: nowrap;
}
.mini-badge.area { border-color: transparent; color: #fff; background: var(--area-color, #6c757d); }
.mini-badge.ceub { background: rgba(10,179,156,.12); color: #0f9d8a; border-color: rgba(10,179,156,.25); }

.pv-estado { display: inline-flex; flex-direction: column; align-items: center; gap: 4px; }
.pv-estado .badge { font-size: .62rem; font-weight: 700; }

.pv-resp { display: flex; flex-direction: column; gap: 4px; align-items: flex-start; min-width: 0; }
.pv-resp .rv { display: inline-flex; align-items: center; gap: 6px; font-size: .72rem; color: var(--ui-ink-2); max-width: 100%; }
.pv-resp .rv .dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.pv-resp .rv span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.metric { display: inline-flex; flex-direction: column; align-items: center; line-height: 1.1; }
.metric .metric-main { font-weight: 700; font-size: .95rem; color: var(--ui-ink); font-variant-numeric: tabular-nums; }
.metric .metric-sub { font-size: .62rem; color: var(--ui-muted); margin-top: 3px; white-space: nowrap; }

@media (max-width: 1199.98px) {
    .programas-table .pv-col-bajas { display: none; }
}
@media (max-width: 991.98px) {
    .programas-table .pv-col-resp { display: none; }
}
@media (max-width: 767.98px) {
    .programas-table .pv-col-periodo { display: none; }
    .programas-table th, .programas-table td { padding: .55rem .4rem; }
}

.programas-table .btn-icon {
    width: 32px; height: 32px;
    padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px;
    font-size: .95rem;
    transition: all .18s ease;
    border: 0;
    background: transparent;
}
.programas-table td:last-child { overflow: visible; }
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
.moodle-icon-link {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px;
    background: #fff4e6;
    color: #f76707;
    border: 1px solid #ffd8a8;
    border-radius: 5px;
    margin-left: 2px;
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
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $responsablesJson = $responsables->map(function ($r) {
        return [
            'id' => $r->id,
            'ci' => $r->ci,
            'nombres' => $r->nombres,
            'apellidos' => $r->apellidos,
            'color' => $r->color ?? null,
        ];
    })->values();
@endphp
<div class="row">
    <div class="col-12">
        <div class="programas-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 position-relative" style="z-index:1;">
                <div class="hero-icon">
                    <i class="ri-graduation-cap-line"></i>
                </div>
                <div>
                    <h4 class="mb-1">Gestión de Programas</h4>
                    <p class="hero-sub">Administra, organiza y monitorea los programas académicos de la institución</p>
                </div>
            </div>
            <div class="position-relative" style="z-index:1;">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                    <li class="breadcrumb-item active">Programas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-2">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                            <i class="ri-equalizer-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros de Búsqueda</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Gestión</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                            <select id="filter-gestion" class="form-select">
                                <option value="">Todas las gestiones</option>
                                @foreach($gestiones as $g)
                                    <option value="{{ $g }}" @if((int)$g === $gestionActual) selected @endif>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Área de Programa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-apps-2-line"></i></span>
                            <select id="filter-area" class="form-select">
                                <option value="">Todas las áreas</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-flag-2-line"></i></span>
                            <select id="filter-estado" class="form-select">
                                <option value="">Todos los estados</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Buscar programa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filter-search" class="form-control" placeholder="Nombre o código..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button id="btn-filter-clear" class="btn btn-soft-danger d-flex align-items-center justify-content-center gap-1 px-3">
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                        @if(auth()->user()->hasRole('Super Admin'))
                        <button class="btn btn-nuevo d-flex align-items-center justify-content-center gap-1 px-3" id="btn-nuevo-programa">
                            <i class="ri-add-circle-line fs-16"></i> Nuevo
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="stats-container" class="row g-3 mb-1"></div>

{{-- Tarjetas por tipo (globales) — todas en una fila, clicables --}}
<div id="tipo-cards-container" class="tipo-stats-row mb-4"></div>

<div class="row">
    <div class="col-12">
        <div class="card tabs-card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-pill mb-3" role="tablist">
                    @foreach($tipos as $tipo)
                    <li class="nav-item">
                        <a class="nav-link @if($loop->first) active @endif" data-tipo-id="{{ $tipo->id }}" id="tab-{{ $tipo->id }}" data-bs-toggle="tab" href="#tipo-{{ $tipo->id }}" role="tab">
                            <i class="ri-folder-3-line"></i> {{ $tipo->nombre }}
                            <span class="tab-count">{{ $tipo->programas_count }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($tipos as $tipo)
                    <div class="tab-pane @if($loop->first) active show @endif" id="tipo-{{ $tipo->id }}" role="tabpanel">
                        <div class="tab-header-row">
                            <h6 class="mb-0 d-flex align-items-center gap-2">
                                <span class="programa-count" id="count-{{ $tipo->id }}">0</span>
                                <span class="text-muted fw-medium">programas en <span class="text-dark fw-semibold">{{ $tipo->nombre }}</span></span>
                            </h6>
                            <small class="text-muted" id="info-{{ $tipo->id }}"></small>
                        </div>
                        <div id="tab-stats-{{ $tipo->id }}" class="tab-stats-row"></div>
                        <div id="content-{{ $tipo->id }}">
                            <div class="tab-content-loader">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando programas...</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear/Editar --}}
<div class="modal fade" id="modalPrograma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content programa-modal">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">
                        <i class="ri-folder-add-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalProgramaTitle">Nuevo Programa</h5>
                        <small class="modal-subtitle">Complete la información del programa académico</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPrograma" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="programa_id" id="programaId">
                <div class="modal-body">

                    {{-- Sección: Información Básica --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-primary text-primary"><i class="ri-information-line"></i></span>
                            <span>Información Básica</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del Programa <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-book-2-line"></i></span>
                                    <input type="text" name="nombre" id="f-nombre" class="form-control" required maxlength="255" placeholder="Ej. Diplomado en Educación Superior">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Código <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                    <input type="text" name="codigo" id="f-codigo" class="form-control" required maxlength="40" placeholder="Ej. DES-2026">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Código Académico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-school-line"></i></span>
                                    <input type="text" name="codigo_academico" id="f-codigo_academico" class="form-control" maxlength="50" placeholder="Ej. DIP-EDU-001">
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> Código interno de la universidad</small>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Clasificación --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-info text-info"><i class="ri-price-tag-3-line"></i></span>
                            <span>Clasificación y Estado</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-folder-3-line"></i></span>
                                    <select name="tipo_id" id="f-tipo_id" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-flag-2-line"></i></span>
                                    <select name="estado_id" id="f-estado_id" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Periodo y Sesiones --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-warning text-warning"><i class="ri-calendar-event-line"></i></span>
                            <span>Periodo y Sesiones</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Gestión <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                                    <input type="number" name="gestion" id="f-gestion" class="form-control" required min="2000" max="2100" value="{{ date('Y') }}">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cant. Sesiones <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-list-ordered"></i></span>
                                    <input type="number" name="cantidad_sesiones" id="f-cantidad_sesiones" class="form-control" required min="0" placeholder="0">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Inicio</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-check-line"></i></span>
                                    <input type="date" name="fecha_inicio" id="f-fecha_inicio" class="form-control">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Conclusión</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-close-line"></i></span>
                                    <input type="date" name="fecha_conclusion" id="f-fecha_conclusion" class="form-control">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Asignación --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-success text-success"><i class="ri-user-settings-line"></i></span>
                            <span>Asignación</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Área</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-apps-2-line"></i></span>
                                    <select name="area_id" id="f-area_id" class="form-select">
                                        <option value="">Seleccionar</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sede</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-building-line"></i></span>
                                    <select name="sede_id" id="f-sede_id" class="form-select">
                                        <option value="">Seleccionar</option>
                                        @foreach($sedes as $sede)
                                            <option value="{{ $sede->id }}" @if($sede->id === 1) selected @endif>{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Responsable(s)</label>
                                <div class="resp-picker" id="picker-edit"
                                     data-todos='{{ $responsablesJson->toJson() }}'>
                                    <div class="resp-picker-chips" data-role="chips"></div>
                                    <div class="resp-picker-search">
                                        <i class="ri-search-line"></i>
                                        <input type="text" data-role="search" placeholder="Buscar por nombre, apellido o CI..." autocomplete="off">
                                    </div>
                                    <div class="resp-picker-list" data-role="list"></div>
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> Haz clic sobre un responsable para agregar/quitar</small>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Costos --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-danger text-danger"><i class="ri-money-dollar-circle-line"></i></span>
                            <span>Costos e Inscripciones</span>
                        </div>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Matrícula</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="matricula" id="f-matricula" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Colegiatura</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="colegiatura" id="f-colegiatura" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Certificación <small class="text-muted">(Convenio)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_certificacion" id="f-costo_certificacion" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Certificación <small class="text-muted">(Trámite Personal)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_certificacion_personal" id="f-costo_certificacion_personal" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">N° Cuotas Colegiatura</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-stack-line"></i></span>
                                    <input type="number" name="n_modulos" id="f-n_modulos" class="form-control" min="0" placeholder="0" title="Cantidad de cuotas de colegiatura a generar. Si es 0, se usa el valor por defecto (módulos - 1).">
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> 0 = automático según módulos</small>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch form-switch-success mb-2">
                                    <input type="checkbox" name="inscripciones_habilitadas" id="f-inscripciones_habilitadas" class="form-check-input" value="1" checked role="switch">
                                    <label class="form-check-label fw-semibold" for="f-inscripciones_habilitadas">
                                        <i class="ri-checkbox-circle-line text-success"></i> Inscripciones habilitadas
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted text-uppercase fs-11">Costos de Pago a la Universidad</label>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Inscripción</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_pago_inscripcion_universidad" id="f-costo_pago_inscripcion_universidad" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Matrícula</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_pago_matricula_universidad" id="f-costo_pago_matricula_universidad" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Colegiatura</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_pago_colegiatura_universidad" id="f-costo_pago_colegiatura_universidad" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Certificación</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" name="costo_pago_certificacion_universidad" id="f-costo_pago_certificacion_universidad" class="form-control" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Moodle --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-primary text-primary"><i class="ri-graduation-cap-line"></i></span>
                            <span>Enlace Moodle</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Enlace al curso en Moodle</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-link"></i></span>
                                    <input type="url" name="moodle_link" id="f-moodle_link" class="form-control" maxlength="500" placeholder="https://moodle.ejemplo.com/course/view.php?id=...">
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> Deja en blanco si el programa no tiene curso en Moodle.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: CEUB --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-success text-success"><i class="ri-gov-line"></i></span>
                            <span>Reconocimiento CEUB</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch form-switch-success mb-0">
                                    <input type="checkbox" name="ceub" id="f-ceub" class="form-check-input" value="1" checked role="switch">
                                    <label class="form-check-label fw-semibold" for="f-ceub">
                                        <i class="ri-checkbox-circle-line text-success"></i> Cuenta con reconocimiento CEUB
                                    </label>
                                </div>
                                <small class="text-muted"><i class="ri-information-line"></i> El Comité Ejecutivo de la Universidad Boliviana (CEUB) otorga validez nacional a los programas.</small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-save-programa" id="btnSavePrograma">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Asignar Responsables --}}
<div class="modal fade" id="modalAsignarResp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-team-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Asignar Responsables</h5>
                        <small class="modal-subtitle" id="mar-subtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="resp-picker" id="picker-asignar"
                     data-todos='{{ $responsablesJson->toJson() }}'>
                    <div class="resp-picker-chips" data-role="chips"></div>
                    <div class="resp-picker-search">
                        <i class="ri-search-line"></i>
                        <input type="text" data-role="search" placeholder="Buscar por nombre, apellido o CI..." autocomplete="off">
                    </div>
                    <div class="resp-picker-list" data-role="list"></div>
                </div>
                <small class="text-muted mt-2 d-block"><i class="ri-information-line"></i> Haz clic sobre un responsable para agregar/quitar de la lista</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-save-programa" id="btnGuardarResp">
                    <i class="ri-save-3-line align-bottom"></i> Guardar Responsables
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Costos Universidad (read-only) --}}
<div class="modal fade" id="modalCostosUniversidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-bank-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Costos de Pago a la Universidad</h5>
                        <small class="modal-subtitle" id="mcu-subtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="form-section-icon bg-soft-danger text-danger"><i class="ri-money-dollar-circle-line"></i></span>
                        <span>Costos por Participante</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <small class="text-muted text-uppercase fw-semibold fs-10">Pago Inscripción</small>
                                <h4 class="mb-0 fw-bold mt-1" id="mcu-costo_pago_inscripcion_universidad">Bs 0.00</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <small class="text-muted text-uppercase fw-semibold fs-10">Pago Matrícula</small>
                                <h4 class="mb-0 fw-bold mt-1" id="mcu-costo_pago_matricula_universidad">Bs 0.00</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <small class="text-muted text-uppercase fw-semibold fs-10">Pago Colegiatura</small>
                                <h4 class="mb-0 fw-bold mt-1" id="mcu-costo_pago_colegiatura_universidad">Bs 0.00</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <small class="text-muted text-uppercase fw-semibold fs-10">Pago Certificación</small>
                                <h4 class="mb-0 fw-bold mt-1" id="mcu-costo_pago_certificacion_universidad">Bs 0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminación --}}
<div class="modal fade" id="modalDeletePrograma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="delete-icon-wrap mx-auto mb-3">
                    <i class="ri-error-warning-line"></i>
                </div>
                <h4 class="mb-2 fw-bold">¿Eliminar este programa?</h4>
                <p class="text-muted mb-1">Estás a punto de eliminar</p>
                <p class="fw-semibold fs-15 text-dark mb-3" id="deleteProgramaNombre"></p>
                <div class="alert alert-soft-danger border-0 d-inline-flex align-items-center gap-2 py-2 px-3 mb-0">
                    <i class="ri-alert-line"></i>
                    <small class="mb-0">Esta acción no se puede deshacer</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4" id="btnConfirmDelete">
                    <i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar
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
    let activeTipoId = null;

    const iconosTipo = ['ri-book-open-line','ri-graduation-cap-line','ri-flask-line','ri-bar-chart-2-line','ri-global-line','ri-pencil-ruler-line','ri-team-line','ri-empathize-line','ri-lightbulb-line','ri-rocket-line'];

    // Estados con su color de marca (texto + fondo suave)
    const ESTADO_BADGE = {
        'En desarrollo': { hex: '#1f6fbf', soft: '#e7f1fb' },
        'Concluido':     { hex: '#0d8574', soft: '#e2f4f0' },
        'No Ejecutado':  { hex: '#b3243a', soft: '#fbe9ec' },
        'Inscripciones': { hex: '#cba135', soft: '#faf3df' },
    };

    function estadoBadgeHtml(nombre, total) {
        const c = ESTADO_BADGE[nombre] || { hex: '#2a5d97', soft: '#eef1f8' };
        return `<span class="pg-badge" style="color:${c.hex};background:${c.soft};">${nombre}: ${total}</span>`;
    }

    // ---- TABS ----
    function loadTab(tipoId, page = 1) {
        const container = document.getElementById('content-' + tipoId);

        container.innerHTML = `
            <div class="tab-content-loader">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando programas...</p>
            </div>
        `;

        const params = new URLSearchParams({ page });
        const gestion = document.getElementById('filter-gestion').value;
        const area = document.getElementById('filter-area').value;
        const estado = document.getElementById('filter-estado').value;
        const search = document.getElementById('filter-search').value.trim();
        if (gestion) params.append('gestion', gestion);
        if (area) params.append('area_id', area);
        if (estado) params.append('estado_id', estado);
        if (search) params.append('search', search);

        fetch('{{ url("admin/programas/tipo") }}/' + tipoId + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            container.innerHTML = data.html + data.pagination;
            const badge = document.querySelector('#tab-' + tipoId + ' .tab-count');
            if (badge) badge.textContent = data.total;
            const countSpan = document.getElementById('count-' + tipoId);
            if (countSpan) countSpan.textContent = data.total;
        })
        .catch(() => {
            container.innerHTML = '<div class="text-center text-danger py-4">Error al cargar programas</div>';
        });
    }

    document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const tipoId = this.dataset.tipoId;
            activeTipoId = tipoId;
            document.querySelectorAll('.nav-tabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active', 'show'));
            document.getElementById('tipo-' + tipoId).classList.add('active', 'show');
            loadTab(tipoId);
        });
    });

    // ---- STATS ----
    function loadStats(callback) {
        const gestion = document.getElementById('filter-gestion').value;
        const area = document.getElementById('filter-area').value;
        const estado = document.getElementById('filter-estado').value;
        const search = document.getElementById('filter-search').value.trim();
        const sContainer = document.getElementById('stats-container');
        const tContainer = document.getElementById('tipo-cards-container');

        const params = new URLSearchParams();
        if (gestion) params.append('gestion', gestion);
        if (area) params.append('area_id', area);
        if (estado) params.append('estado_id', estado);
        if (search) params.append('search', search);

        fetch('{{ url("admin/programas/stats") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            renderStatSummary(sContainer, data);
            renderTipoCards(tContainer, data);
            renderTabNav(data);
            renderTabStats(data);
            if (callback) callback();
        })
        .catch(() => {
            sContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger mb-0">Error al cargar estadísticas</div></div>';
            tContainer.innerHTML = '';
            if (callback) callback();
        });
    }

    function renderStatSummary(container, data) {
        const r = data.resumen;
        if (!r || r.total_programas === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info border-0 rounded-3 shadow-sm d-flex align-items-center mb-0" role="alert">
                        <i class="ri-info-card-line fs-20 me-2 text-info"></i>
                        <div>No hay programas para los filtros seleccionados</div>
                    </div>
                </div>`;
            return;
        }

        const cards = [
            { icon: 'ri-folder-2-line', label: 'Total Programas', value: r.total_programas.toLocaleString('es-BO'), accent: '#163b6b', soft: '#f1f5fa' },
            { icon: 'ri-bank-card-line', label: 'Total Matrículas', value: 'Bs ' + r.total_matricula.toLocaleString('es-BO', {minimumFractionDigits:2}), accent: '#cba135', soft: '#faf3df' },
            { icon: 'ri-funds-line', label: 'Total Colegiaturas', value: 'Bs ' + r.total_colegiatura.toLocaleString('es-BO', {minimumFractionDigits:2}), accent: '#0d8574', soft: '#e2f4f0' },
        ];

        container.innerHTML = cards.map(c => `
            <div class="col-xl col-md-6">
                <div class="card stat-card pg-stat h-100 mb-0" style="--pg-accent:${c.accent}; --pg-soft:${c.soft};">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div style="min-width:0;">
                                <p class="pg-stat-label text-truncate">${c.label}</p>
                                <h4 class="pg-stat-value pg-stat-value--sm mb-0">${c.value}</h4>
                            </div>
                            <span class="pg-stat-icon"><i class="${c.icon}"></i></span>
                        </div>
                    </div>
                </div>
            </div>`).join('');
    }

    function renderTipoCards(container, data) {
        const tipos = (data.tipos || []).filter(t => t.total > 0);
        if (!tipos.length) {
            container.innerHTML = '';
            return;
        }

        const temas = [
            { hex: '#163b6b', soft: '#f1f5fa' },
            { hex: '#cba135', soft: '#faf3df' },
            { hex: '#1f6fbf', soft: '#e7f1fb' },
            { hex: '#0d8574', soft: '#e2f4f0' },
            { hex: '#b7791f', soft: '#fbf0da' },
            { hex: '#b3243a', soft: '#fbe9ec' },
            { hex: '#2a5d97', soft: '#eef1f8' },
        ];

        container.innerHTML = tipos.map((t, i) => {
            const tema = temas[i % temas.length];
            const icon = iconosTipo[i % iconosTipo.length];
            const badges = t.estados.map(e => estadoBadgeHtml(e.nombre, e.total)).join('');
            return `
                <div class="pg-tipo-col">
                    <div class="card stat-card pg-stat pg-tipo-card h-100 mb-0" style="--pg-accent:${tema.hex}; --pg-soft:${tema.soft};"
                         data-tipo-id="${t.tipo_id}" role="button" tabindex="0" aria-label="Ver programas de ${t.tipo_nombre}">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <span class="pg-stat-label text-truncate mb-0">${t.tipo_nombre}</span>
                                <span class="pg-stat-icon"><i class="${icon}"></i></span>
                            </div>
                            <div class="d-flex align-items-end gap-2">
                                <h3 class="pg-stat-value mb-0">${t.total}</h3>
                                <span class="pg-stat-meta">programas</span>
                            </div>
                            <div class="pg-stat-badges">${badges}</div>
                            <div class="pg-tipo-cta"><span>Ver programas</span><i class="ri-arrow-right-line"></i></div>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    // Ingresar a un tipo haciendo clic (o Enter/Espacio) en su tarjeta.
    function activarTipo(tipoId) {
        if (! tipoId) return;
        const link = document.querySelector('.nav-tabs-pill .nav-link[data-tipo-id="' + tipoId + '"]');
        if (link) link.click();
    }

    const tiposContainer = document.getElementById('tipo-cards-container');
    if (tiposContainer) {
        tiposContainer.addEventListener('click', function (e) {
            const card = e.target.closest('.pg-tipo-card');
            if (card) activarTipo(card.dataset.tipoId);
        });
        tiposContainer.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') return;
            const card = e.target.closest('.pg-tipo-card');
            if (card) { e.preventDefault(); activarTipo(card.dataset.tipoId); }
        });
    }

    function renderTabNav(data) {
        const tipos = (data.tipos || []).filter(t => t.total > 0);
        const ul = document.querySelector('.nav-tabs-pill');
        if (!ul) return;

        // Sort by total descending, then by name
        tipos.sort((a, b) => b.total - a.total || a.tipo_nombre.localeCompare(b.tipo_nombre));

        const activeId = ul.querySelector('.nav-link.active')?.dataset.tipoId;
        let html = '';
        tipos.forEach((t, i) => {
            const active = String(t.tipo_id) === activeId || (!activeId && i === 0);
            html += `
                <li class="nav-item">
                    <a class="nav-link ${active ? 'active' : ''}" data-tipo-id="${t.tipo_id}" id="tab-${t.tipo_id}" data-bs-toggle="tab" href="#tipo-${t.tipo_id}" role="tab">
                        <i class="ri-folder-3-line"></i> ${t.tipo_nombre}
                        <span class="tab-count">${t.total}</span>
                    </a>
                </li>`;
        });
        ul.innerHTML = html;

        // Hide tab panes for tipos that have no results
        document.querySelectorAll('.tab-pane').forEach(pane => {
            const id = pane.id.replace('tipo-', '');
            const hasTab = tipos.some(t => String(t.tipo_id) === id);
            if (!hasTab) {
                pane.classList.remove('active', 'show');
            }
        });

        // Activar el panel del tab activo y cargar su contenido
        const activeLink = ul.querySelector('.nav-link.active');
        if (activeLink) {
            const tid = activeLink.dataset.tipoId;
            activeTipoId = tid;
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active', 'show'));
            const pane = document.getElementById('tipo-' + tid);
            if (pane) pane.classList.add('active', 'show');
            loadTab(tid);
        }

        // Re-bind click events for new tabs
        ul.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                const tipoId = this.dataset.tipoId;
                activeTipoId = tipoId;
                ul.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active', 'show'));
                const pane = document.getElementById('tipo-' + tipoId);
                if (pane) pane.classList.add('active', 'show');
                loadTab(tipoId);
            });
        });
    }

    function renderTabStats(data) {
        const tipos = (data.tipos || []).filter(t => t.total > 0);
        tipos.forEach(t => {
            const container = document.getElementById('tab-stats-' + t.tipo_id);
            if (!container) return;

            const badges = t.estados.map(e => estadoBadgeHtml(e.nombre, e.total)).join('');

            container.innerHTML = `
                <div class="tab-stat-item">
                    <span class="tab-stat-icon"><i class="ri-folder-2-line"></i></span>
                    <span><span class="num">${t.total}</span> programas registrados</span>
                </div>
                <div class="d-flex flex-wrap gap-1 align-items-center">${badges}</div>
            `;
        });
    }

    // ---- FILTER EVENTS ----
    document.getElementById('filter-gestion').addEventListener('change', function () {
        loadStats(function () {
            const activeLink = document.querySelector('.nav-tabs-pill .nav-link.active');
            if (activeLink) {
                activeTipoId = activeLink.dataset.tipoId;
                loadTab(activeTipoId);
            }
        });
    });

    document.getElementById('filter-area').addEventListener('change', function () {
        loadStats(function () {
            const activeLink = document.querySelector('.nav-tabs-pill .nav-link.active');
            if (activeLink) {
                activeTipoId = activeLink.dataset.tipoId;
                loadTab(activeTipoId);
            }
        });
    });

    document.getElementById('filter-estado').addEventListener('change', function () {
        loadStats(function () {
            const activeLink = document.querySelector('.nav-tabs-pill .nav-link.active');
            if (activeLink) {
                activeTipoId = activeLink.dataset.tipoId;
                loadTab(activeTipoId);
            }
        });
    });

    document.getElementById('btn-filter-clear').addEventListener('click', function () {
        document.getElementById('filter-area').value = '';
        document.getElementById('filter-estado').value = '';
        document.getElementById('filter-search').value = '';
        loadStats(function () {
            const activeLink = document.querySelector('.nav-tabs-pill .nav-link.active');
            if (activeLink) {
                activeTipoId = activeLink.dataset.tipoId;
                loadTab(activeTipoId);
            }
        });
    });

    // ---- BÚSQUEDA EN TIEMPO REAL ----
    let searchTimeout = null;
    document.getElementById('filter-search').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            loadStats(function () {
                const activeLink = document.querySelector('.nav-tabs-pill .nav-link.active');
                if (activeLink) {
                    activeTipoId = activeLink.dataset.tipoId;
                    loadTab(activeTipoId);
                }
            });
        }, 350);
    });

    // ---- PAGINACIÓN ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.page-link[data-page]');
        if (btn && activeTipoId) {
            e.preventDefault();
            loadTab(activeTipoId, parseInt(btn.dataset.page));
        }
    });

    // ---- MODAL NUEVO ---- (solo Super Admin ve el botón)
    const btnNuevoPrograma = document.getElementById('btn-nuevo-programa');
    if (btnNuevoPrograma) {
        btnNuevoPrograma.addEventListener('click', function () {
            resetForm();
            document.getElementById('modalProgramaTitle').textContent = 'Nuevo Programa';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('btnSavePrograma').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
            new bootstrap.Modal(document.getElementById('modalPrograma')).show();
        });
    }

    // ---- MODAL EDITAR ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-programa');
        if (!btn) return;
        const id = btn.dataset.id;

        fetch('{{ url("admin/programas") }}/' + id + '/data', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            resetForm();
            document.getElementById('modalProgramaTitle').textContent = 'Editar Programa';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('btnSavePrograma').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Actualizar';
            document.getElementById('programaId').value = id;
            document.getElementById('f-nombre').value = data.programa.nombre;
            document.getElementById('f-codigo').value = data.programa.codigo;
            document.getElementById('f-codigo_academico').value = data.programa.codigo_academico || '';
            document.getElementById('f-gestion').value = data.programa.gestion;
            document.getElementById('f-tipo_id').value = data.programa.tipo_id;
            document.getElementById('f-estado_id').value = data.programa.estado_id;
            document.getElementById('f-cantidad_sesiones').value = data.programa.cantidad_sesiones;
            document.getElementById('f-area_id').value = data.area_id || '';
            document.getElementById('f-sede_id').value = data.sede_id || '';
            document.getElementById('f-fecha_inicio').value = data.programa.fecha_inicio || '';
            document.getElementById('f-fecha_conclusion').value = data.programa.fecha_conclusion || '';
            document.getElementById('f-matricula').value = data.programa.matricula || 0;
            document.getElementById('f-colegiatura').value = data.programa.colegiatura || 0;
            document.getElementById('f-costo_certificacion').value = data.programa.costo_certificacion || 0;
            document.getElementById('f-costo_certificacion_personal').value = data.programa.costo_certificacion_personal || 0;
            document.getElementById('f-costo_pago_inscripcion_universidad').value = data.programa.costo_pago_inscripcion_universidad || 0;
            document.getElementById('f-costo_pago_matricula_universidad').value = data.programa.costo_pago_matricula_universidad || 0;
            document.getElementById('f-costo_pago_colegiatura_universidad').value = data.programa.costo_pago_colegiatura_universidad || 0;
            document.getElementById('f-costo_pago_certificacion_universidad').value = data.programa.costo_pago_certificacion_universidad || 0;
            document.getElementById('f-moodle_link').value = data.programa.moodle_link || '';
            document.getElementById('f-n_modulos').value = data.programa.n_modulos ?? 0;
            document.getElementById('f-inscripciones_habilitadas').checked = !!data.programa.inscripciones_habilitadas;
            document.getElementById('f-ceub').checked = !!data.programa.ceub;

            // Sincroniza el picker de responsables (modal editar)
            window.__pickerEdit && window.__pickerEdit.setSelected(data.responsables || []);

            new bootstrap.Modal(document.getElementById('modalPrograma')).show();
        })
        .catch(() => {
            alert('Error al cargar datos del programa');
        });
    });

    // ---- MODAL ELIMINAR ----
    let deleteId = null;
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-programa');
        if (!btn) return;
        deleteId = btn.dataset.id;
        document.getElementById('deleteProgramaNombre').textContent = btn.dataset.nombre;
        new bootstrap.Modal(document.getElementById('modalDeletePrograma')).show();
    });

    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        if (!deleteId) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Eliminando...';
        fetch('{{ url("admin/programas") }}/' + deleteId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            bootstrap.Modal.getInstance(document.getElementById('modalDeletePrograma')).hide();
            if (ok && data.success) {
                showToast(data.message || 'Programa eliminado correctamente', 'success');
                if (activeTipoId) loadTab(activeTipoId);
                loadStats();
            } else {
                showToast(data.message || 'Error al eliminar el programa', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión al eliminar el programa', 'danger'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar';
        });
    });

    // ---- VALIDACIÓN EN TIEMPO REAL ----
    const form = document.getElementById('formPrograma');
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        field.addEventListener('input', function () { validateField(this); });
        field.addEventListener('change', function () { validateField(this); });
        field.addEventListener('blur', function () { validateField(this); });
    });

    function validateField(field) {
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (!field.value || field.value.trim() === '') {
            field.classList.add('is-invalid');
            if (feedback) feedback.textContent = 'Este campo es obligatorio';
        } else {
            field.classList.remove('is-invalid');
            if (feedback) feedback.textContent = '';
        }

        if (field.id === 'f-fecha_inicio' && field.value) {
            const end = document.getElementById('f-fecha_conclusion');
            if (end && end.value && end.value < field.value) {
                end.classList.add('is-invalid');
                const fb = end.parentElement.querySelector('.invalid-feedback');
                if (fb) fb.textContent = 'Debe ser posterior a la fecha de inicio';
            }
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;
        requiredFields.forEach(f => { validateField(f); if (f.classList.contains('is-invalid')) valid = false; });

        if (!valid) {
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        const method = document.getElementById('formMethod').value;
        const id = document.getElementById('programaId').value;
        const url = method === 'PUT' ? '{{ url("admin/programas") }}/' + id : '{{ url("admin/programas") }}';

        const formData = new FormData(form);
        if (method === 'PUT') formData.append('_method', 'PUT');

        // Agregar responsables del picker
        if (window.__pickerEdit) {
            window.__pickerEdit.getSelected().forEach(function (rid) {
                formData.append('responsables[]', rid);
            });
        }

        // Limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        const btnSave = document.getElementById('btnSavePrograma');
        const originalText = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json().then(d => ({ ok: r.ok, status: r.status, data: d })))
        .then(({ ok, status, data }) => {
            if (ok && data.success !== false) {
                bootstrap.Modal.getInstance(document.getElementById('modalPrograma')).hide();
                showToast(method === 'PUT' ? 'Programa actualizado correctamente' : 'Programa registrado correctamente', 'success');
                if (activeTipoId) loadTab(activeTipoId);
                loadStats();
            } else if (status === 422 && data.errors) {
                // Errores de validación de Laravel
                for (const field in data.errors) {
                    const input = document.getElementById('f-' + field) || form.querySelector('[name="' + field + '"]');
                    if (input) {
                        input.classList.add('is-invalid');
                        const wrap = input.closest('.col-md-12, .col-md-8, .col-md-6, .col-md-4, .col-md-3');
                        const fb = (wrap && wrap.querySelector('.invalid-feedback')) || input.parentElement.querySelector('.invalid-feedback');
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
                const first = form.querySelector('.is-invalid');
                if (first) first.focus();
                showToast('Hay campos con errores. Revisa el formulario.', 'danger');
            } else {
                showToast(data.message || 'No se pudo guardar el programa', 'danger');
            }
        })
        .catch(() => {
            showToast('Error de conexión al guardar', 'danger');
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
        });
    });

    // ====== Toast helper ======
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
        el.setAttribute('role', 'alert');
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

    function resetForm() {
        form.reset();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.getElementById('programaId').value = '';
        document.getElementById('f-gestion').value = new Date().getFullYear();
        document.getElementById('f-inscripciones_habilitadas').checked = true;
        document.getElementById('f-ceub').checked = true;
        document.getElementById('f-sede_id').value = '1';
        window.__pickerEdit && window.__pickerEdit.setSelected([]);
    }

    // ===================================================================
    // ===========  RESPONSABLES PICKER (reusable widget)  ===============
    // ===================================================================
    function createRespPicker(rootSelector) {
        const root = document.querySelector(rootSelector);
        if (!root) return null;
        const todos = JSON.parse(root.dataset.todos || '[]');
        const chipsBox = root.querySelector('[data-role="chips"]');
        const listBox = root.querySelector('[data-role="list"]');
        const searchInp = root.querySelector('[data-role="search"]');
        const state = { selected: new Set(), query: '' };

        function colorOf(r) {
            return r.color && r.color.trim() ? r.color : '#878a99';
        }
        function inicialesOf(r) {
            return ((r.nombres || '').charAt(0) + (r.apellidos || '').charAt(0)).toUpperCase();
        }
        function escHtml(s) {
            return String(s ?? '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
        }

        function renderChips() {
            chipsBox.innerHTML = '';
            todos.forEach(function (r) {
                if (!state.selected.has(r.id)) return;
                const c = colorOf(r);
                const chip = document.createElement('span');
                chip.className = 'resp-chip';
                chip.style.borderColor = c;
                chip.style.color = c;
                chip.innerHTML =
                    '<span class="resp-chip-avatar" style="background:' + c + ';">' + escHtml(inicialesOf(r)) + '</span>' +
                    '<span>' + escHtml(r.apellidos) + ', ' + escHtml(r.nombres) + '</span>' +
                    '<span class="resp-chip-x" data-remove="' + r.id + '" title="Quitar"><i class="ri-close-line"></i></span>';
                chipsBox.appendChild(chip);
            });
        }
        function renderList() {
            const q = state.query.trim().toLowerCase();
            const filtered = todos.filter(function (r) {
                if (!q) return true;
                return (r.ci && String(r.ci).toLowerCase().includes(q))
                    || (r.nombres && r.nombres.toLowerCase().includes(q))
                    || (r.apellidos && r.apellidos.toLowerCase().includes(q));
            });
            if (filtered.length === 0) {
                listBox.innerHTML = '<div class="resp-empty"><i class="ri-search-line"></i> Sin resultados</div>';
                return;
            }
            let html = '';
            filtered.forEach(function (r) {
                const c = colorOf(r);
                const sel = state.selected.has(r.id) ? ' selected' : '';
                html += '<div class="resp-item' + sel + '" data-id="' + r.id + '">' +
                    '<div class="resp-item-avatar" style="background:' + c + ';">' + escHtml(inicialesOf(r)) + '</div>' +
                    '<div class="resp-item-info">' +
                        '<div class="resp-item-nombre">' + escHtml(r.apellidos) + ', ' + escHtml(r.nombres) + '</div>' +
                        '<div class="resp-item-meta"><i class="ri-passport-line"></i> CI: ' + escHtml(r.ci) + '</div>' +
                    '</div>' +
                '</div>';
            });
            listBox.innerHTML = html;
        }
        function renderAll() { renderChips(); renderList(); }

        // Events
        listBox.addEventListener('click', function (e) {
            const it = e.target.closest('.resp-item');
            if (!it) return;
            const id = parseInt(it.dataset.id);
            if (state.selected.has(id)) state.selected.delete(id);
            else state.selected.add(id);
            renderAll();
        });
        chipsBox.addEventListener('click', function (e) {
            const x = e.target.closest('[data-remove]');
            if (!x) return;
            state.selected.delete(parseInt(x.dataset.remove));
            renderAll();
        });
        searchInp.addEventListener('input', function () {
            state.query = this.value;
            renderList();
        });

        renderAll();

        return {
            getSelected: function () { return Array.from(state.selected); },
            setSelected: function (ids) {
                state.selected = new Set((ids || []).map(function (x) { return parseInt(x); }));
                state.query = '';
                searchInp.value = '';
                renderAll();
            },
            clear: function () { state.selected.clear(); renderAll(); }
        };
    }

    window.__pickerEdit = createRespPicker('#picker-edit');
    window.__pickerAsignar = createRespPicker('#picker-asignar');

    // ---- MODAL ASIGNAR RESPONSABLES (desde la tabla) ----
    let asignarRespProgramaId = null;
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-asignar-resp');
        if (!btn) return;
        asignarRespProgramaId = btn.dataset.id;
        document.getElementById('mar-subtitle').textContent = btn.dataset.nombre || '';
        window.__pickerAsignar.setSelected([]);

        fetch('{{ url("admin/programas") }}/' + asignarRespProgramaId + '/responsables-data', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.__pickerAsignar.setSelected(data.asignados || []);
            }
        });

        new bootstrap.Modal(document.getElementById('modalAsignarResp')).show();
    });

    document.getElementById('btnGuardarResp').addEventListener('click', function () {
        if (!asignarRespProgramaId) return;
        const btn = this;
        const ids = window.__pickerAsignar.getSelected();
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        ids.forEach(function (id) { formData.append('responsables[]', id); });

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('{{ url("admin/programas") }}/' + asignarRespProgramaId + '/asignar-responsables', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalAsignarResp')).hide();
                if (activeTipoId) loadTab(activeTipoId);
                loadStats();
            } else {
                alert(data.message || 'Error al guardar');
            }
        })
        .catch(() => alert('Error de conexión al guardar'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar Responsables';
        });
    });

    // ---- MODAL COSTOS UNIVERSIDAD (read-only) ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-costos-universidad');
        if (!btn) return;
        const id = btn.dataset.id;
        document.getElementById('mcu-subtitle').textContent = btn.dataset.nombre || '';

        fetch('{{ url("admin/programas") }}/' + id + '/costos-universidad', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const fmt = (v) => 'Bs ' + (parseFloat(v) || 0).toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('mcu-costo_pago_inscripcion_universidad').innerHTML = fmt(data.programa.costo_pago_inscripcion_universidad);
                document.getElementById('mcu-costo_pago_matricula_universidad').innerHTML = fmt(data.programa.costo_pago_matricula_universidad);
                document.getElementById('mcu-costo_pago_colegiatura_universidad').innerHTML = fmt(data.programa.costo_pago_colegiatura_universidad);
                document.getElementById('mcu-costo_pago_certificacion_universidad').innerHTML = fmt(data.programa.costo_pago_certificacion_universidad);
            }
        })
        .catch(() => showToast('Error al cargar costos universitarios', 'danger'));

        new bootstrap.Modal(document.getElementById('modalCostosUniversidad')).show();
    });

    // ---- INICIO ----
    // Preselecciona filtros desde query string (ej. ?area_id=3 desde Áreas)
    (function applyQueryFilters() {
        const params = new URLSearchParams(window.location.search);
        const areaId = params.get('area_id');
        if (areaId) {
            const sel = document.getElementById('filter-area');
            if (sel && sel.querySelector('option[value="' + areaId + '"]')) {
                sel.value = areaId;
            }
        }
        const estadoId = params.get('estado_id');
        if (estadoId) {
            const sel = document.getElementById('filter-estado');
            if (sel && sel.querySelector('option[value="' + estadoId + '"]')) {
                sel.value = estadoId;
            }
        }
        const gestion = params.get('gestion');
        if (gestion) {
            const sel = document.getElementById('filter-gestion');
            if (sel && sel.querySelector('option[value="' + gestion + '"]')) {
                sel.value = gestion;
            }
        }
    })();

    // Stats precargados desde el servidor (sin AJAX)
    const initialData = @json($initialStats);
    const sContainer = document.getElementById('stats-container');
    const tContainer = document.getElementById('tipo-cards-container');
    renderStatSummary(sContainer, initialData);
    renderTipoCards(tContainer, initialData);
    renderTabNav(initialData);
    renderTabStats(initialData);

    // Cargar primer tab
    const tipoIdParam = new URLSearchParams(window.location.search).get('tipo_id');
    let tabToActivate = null;
    if (tipoIdParam) {
        tabToActivate = document.querySelector('.nav-tabs .nav-link[data-tipo-id="' + tipoIdParam + '"]');
    }
    if (!tabToActivate) tabToActivate = document.querySelector('.nav-tabs .nav-link');
    if (tabToActivate) {
        const tipoId = tabToActivate.dataset.tipoId;
        activeTipoId = tipoId;
        tabToActivate.classList.add('active');
        const pane = document.getElementById('tipo-' + tipoId);
        if (pane) pane.classList.add('active', 'show');
        loadTab(tipoId);
    }
});
</script>
@endpush