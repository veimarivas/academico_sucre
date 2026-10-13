@extends('admin.layouts.master')

@section('title', 'Docentes - ' . config('app.name'))

@push('css')
<style>
.is-invalid { border-color: #f06548 !important; }
.invalid-feedback { display: block; }

/* ===== Hero ===== */
.docentes-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.docentes-hero::before {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.docentes-hero::after {
    content: ""; position: absolute;
    bottom: -60px; left: 30%;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%);
    border-radius: 50%;
}
.docentes-hero > * { position: relative; z-index: 1; }
.docentes-hero .hero-icon {
    width: 56px; height: 56px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 14px;
    font-size: 1.6rem;
    backdrop-filter: blur(4px);
}
.docentes-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.docentes-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }
.docentes-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.docentes-hero .breadcrumb-item, .docentes-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.docentes-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.docentes-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }

/* ===== Filter Card ===== */
.filter-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
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
    font-weight: 700;
    color: #878a99;
    margin-bottom: .35rem;
}
.filter-card .input-group-text {
    background: #fff;
    border: 1px solid #e9ebec;
    border-right: 0;
    color: #878a99;
}
.filter-card .form-control,
.filter-card .form-select {
    border: 1px solid #e9ebec;
    background-color: #fff;
    font-weight: 500;
    transition: all .2s ease;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}
.filter-card .input-group:focus-within .input-group-text {
    border-color: #405189;
    color: #405189;
}
[data-layout-mode="dark"] .filter-card .input-group-text,
[data-layout-mode="dark"] .filter-card .form-control,
[data-layout-mode="dark"] .filter-card .form-select {
    background-color: #1d2125;
    border-color: #2a2f34;
    color: #ced4da;
}

/* ===== Results Card ===== */
.results-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .results-card { border-color: #2a2f34; }
.results-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: .75rem; margin-bottom: 1rem;
    padding-bottom: .75rem; border-bottom: 1px dashed #eef0f4;
    flex-wrap: wrap;
}
[data-layout-mode="dark"] .results-header { border-bottom-color: #2a2f34; }
.results-count {
    display: inline-flex; align-items: center; gap: .5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    padding: .4em .9em; border-radius: 999px;
    font-size: .82rem; font-weight: 600; color: #495057;
    border: 1px solid #eef0f4;
}
.results-count .count-num {
    background: linear-gradient(135deg, #f7b84b 0%, #ff9f43 100%);
    color: #fff; font-weight: 700;
    padding: .15em .6em; border-radius: 999px;
    font-size: .78rem;
}
[data-layout-mode="dark"] .results-count { background: #1a1d20; border-color: #2a2f34; color: #ced4da; }

/* ===== Tabla Docentes ===== */
.tabla-docentes { table-layout: fixed; width: 100%; }
.tabla-docentes th, .tabla-docentes td {
    vertical-align: middle;
    padding: .7rem .55rem;
    overflow: hidden;
}
.tabla-docentes thead th {
    background: #f8f9fc !important;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #495057;
    border-bottom: 2px solid #eef0f4;
}
[data-layout-mode="dark"] .tabla-docentes thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }
.tabla-docentes tbody tr { border-bottom: 1px solid #f1f3f5; transition: background-color .15s ease; }
.tabla-docentes tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .tabla-docentes tbody tr { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .tabla-docentes tbody tr:hover { background-color: #1a1d20; }

.table-card-wrap {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
[data-layout-mode="dark"] .table-card-wrap { border-color: #2a2f34; }

.docente-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: .75rem;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f7b84b 0%, #ff9f43 100%);
    color: #fff;
    box-shadow: 0 4px 10px -4px rgba(247,184,75,.55);
}

.cell-nombre .nombre { font-weight: 600; color: #212529; font-size: .85rem; line-height: 1.2; }
.cell-nombre .ci-info { font-size: .7rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .cell-nombre .nombre { color: #fff; }

.cell-contacto { font-size: .75rem; min-width: 0; }
.cell-contacto .line {
    display: flex; align-items: center; gap: .35rem;
    line-height: 1.3;
    color: #495057;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cell-contacto .line + .line { margin-top: 2px; }
.cell-contacto .line i { color: #adb5bd; flex-shrink: 0; }
[data-layout-mode="dark"] .cell-contacto .line { color: #ced4da; }

.cell-banco { font-size: .76rem; }
.cell-banco .banco-nombre { font-weight: 600; color: #212529; line-height: 1.25; }
.cell-banco .cuenta-num { font-size: .7rem; color: #878a99; font-family: monospace; margin-top: 2px; display: block; }
[data-layout-mode="dark"] .cell-banco .banco-nombre { color: #fff; }

.btn-programas-pill {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #299cdb;
    border: 1px solid #c3ddfa;
    font-size: .72rem;
    font-weight: 600;
    border-radius: 999px;
    padding: .3em .85em;
    display: inline-flex; align-items: center; gap: .35rem;
    transition: all .15s ease;
    cursor: pointer;
}
.btn-programas-pill:hover { background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%); color: #1976d2; transform: translateY(-1px); box-shadow: 0 4px 10px -4px rgba(41,156,219,.4); }

.modulos-count-badge {
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 8px;
    padding: .25em .6em;
    font-size: .78rem;
    font-weight: 700;
    min-width: 32px;
    box-shadow: 0 3px 8px -3px rgba(64,81,137,.5);
}

/* Empty / loading */
.tabla-loading, .tabla-empty {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-radius: 12px;
}
.tabla-empty .empty-icon {
    width: 80px; height: 80px;
    margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #adb5bd;
    box-shadow: 0 8px 20px -10px rgba(0,0,0,.15);
}

/* Paginación */
.pagination .page-link {
    border-radius: 8px !important;
    margin: 0 2px;
    border: 1px solid #e9ebec;
    color: #495057;
    font-weight: 600;
    font-size: .82rem;
    transition: all .15s ease;
}
.pagination .page-link:hover { background: #f8f9fc; border-color: #405189; color: #405189; }
.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.55);
}
.pagination .page-item.disabled .page-link { color: #adb5bd; background: #fafbfd; }

/* ===== Modales unificados ===== */
#modalModulos .modal-content,
#modalCredenciales .modal-content,
#modalNuevoDocente .modal-content,
#modalEditDocente .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25);
}
#modalModulos .modal-header,
#modalCredenciales .modal-header,
#modalNuevoDocente .modal-header,
#modalEditDocente .modal-header,
#modalEstudios .modal-header,
#modalFormEstudio .modal-header,
#modalNuevoCatalogo .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    color: #fff;
    border: 0;
    padding: 1.1rem 1.5rem;
    position: relative;
    overflow: hidden;
}
#modalModulos .modal-header::after,
#modalCredenciales .modal-header::after,
#modalNuevoDocente .modal-header::after,
#modalEditDocente .modal-header::after,
#modalEstudios .modal-header::after,
#modalFormEstudio .modal-header::after,
#modalNuevoCatalogo .modal-header::after {
    content: ""; position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalModulos .modal-header > *,
#modalCredenciales .modal-header > *,
#modalNuevoDocente .modal-header > *,
#modalEditDocente .modal-header > *,
#modalEstudios .modal-header > *,
#modalFormEstudio .modal-header > *,
#modalNuevoCatalogo .modal-header > * { position: relative; z-index: 1; }
.modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem;
    color: #fff;
}
#modalModulos .modal-title,
#modalCredenciales .modal-title,
#modalNuevoDocente .modal-title,
#modalEditDocente .modal-title,
#modalEstudios .modal-title,
#modalFormEstudio .modal-title,
#modalNuevoCatalogo .modal-title { color: #fff; font-weight: 700; }
.modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalModulos .modal-header .btn-close,
#modalCredenciales .modal-header .btn-close,
#modalNuevoDocente .modal-header .btn-close,
#modalEditDocente .modal-header .btn-close,
#modalEstudios .modal-header .btn-close,
#modalFormEstudio .modal-header .btn-close,
#modalNuevoCatalogo .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }

#modalModulos .modal-body,
#modalCredenciales .modal-body,
#modalNuevoDocente .modal-body,
#modalEditDocente .modal-body,
#modalEstudios .modal-body,
#modalFormEstudio .modal-body,
#modalNuevoCatalogo .modal-body {
    background: #fafbfd;
    padding: 1.5rem;
    max-height: 75vh;
}
[data-layout-mode="dark"] #modalModulos .modal-body,
[data-layout-mode="dark"] #modalCredenciales .modal-body,
[data-layout-mode="dark"] #modalNuevoDocente .modal-body,
[data-layout-mode="dark"] #modalEditDocente .modal-body,
[data-layout-mode="dark"] #modalEstudios .modal-body,
[data-layout-mode="dark"] #modalFormEstudio .modal-body,
[data-layout-mode="dark"] #modalNuevoCatalogo .modal-body { background: #1a1d20; }

#modalModulos .modal-footer,
#modalCredenciales .modal-footer,
#modalNuevoDocente .modal-footer,
#modalEditDocente .modal-footer,
#modalEstudios .modal-footer,
#modalFormEstudio .modal-footer,
#modalNuevoCatalogo .modal-footer {
    background: #fff;
    border-top: 1px solid #eef0f4;
    padding: 1rem 1.5rem;
}
[data-layout-mode="dark"] #modalModulos .modal-footer,
[data-layout-mode="dark"] #modalCredenciales .modal-footer,
[data-layout-mode="dark"] #modalNuevoDocente .modal-footer,
[data-layout-mode="dark"] #modalEditDocente .modal-footer,
[data-layout-mode="dark"] #modalEstudios .modal-footer,
[data-layout-mode="dark"] #modalFormEstudio .modal-footer,
[data-layout-mode="dark"] #modalNuevoCatalogo .modal-footer { background: #212529; border-top-color: #2a2f34; }

/* Form sections (Editar) */
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
    display: flex; align-items: center; gap: .6rem;
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
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

#modalNuevoDocente .form-label,
#modalEditDocente .form-label,
#modalFormEstudio .form-label,
#modalNuevoCatalogo .form-label {
    font-weight: 600;
    font-size: .72rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: .35rem;
}
#modalNuevoDocente .form-control,
#modalNuevoDocente .form-select,
#modalNuevoDocente .input-group-text,
#modalEditDocente .form-control,
#modalEditDocente .form-select,
#modalEditDocente .input-group-text,
#modalFormEstudio .form-control,
#modalFormEstudio .form-select,
#modalFormEstudio .input-group-text,
#modalNuevoCatalogo .form-control {
    border-radius: 8px;
    border: 1px solid #e9ebec;
    transition: all .2s ease;
    font-size: .9rem;
}
#modalNuevoDocente .input-group .form-control,
#modalNuevoDocente .input-group .form-select,
#modalEditDocente .input-group .form-control,
#modalEditDocente .input-group .form-select,
#modalFormEstudio .input-group .form-control,
#modalFormEstudio .input-group .form-select {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
#modalNuevoDocente .input-group-text,
#modalEditDocente .input-group-text,
#modalFormEstudio .input-group-text {
    background: #f8f9fc;
    color: #878a99;
    border-right: 0;
}
#modalNuevoDocente .input-group:focus-within .input-group-text,
#modalEditDocente .input-group:focus-within .input-group-text,
#modalFormEstudio .input-group:focus-within .input-group-text {
    border-color: #405189;
    color: #405189;
    background: #fff;
}
#modalNuevoDocente .form-control:focus,
#modalNuevoDocente .form-select:focus,
#modalEditDocente .form-control:focus,
#modalEditDocente .form-select:focus,
#modalFormEstudio .form-control:focus,
#modalFormEstudio .form-select:focus,
#modalNuevoCatalogo .form-control:focus {
    border-color: #405189;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12);
}
#modalNuevoDocente .form-text.ci-status { font-size: .75rem; }
#modalNuevoDocente .form-text.ci-status.text-success { color: #0ab39c !important; }
#modalNuevoDocente .form-text.email-status { font-size: .75rem; }
#modalNuevoDocente .form-text.email-status.text-success { color: #0ab39c !important; }
.btn-save-docente {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0; color: #fff; font-weight: 600;
    padding: .5rem 1.25rem;
    box-shadow: 0 6px 16px -6px rgba(10,179,156,.55);
    transition: transform .15s ease;
}
.btn-save-docente:hover:not(:disabled) { color: #fff; transform: translateY(-1px); }

/* Credenciales modal estilo */
.credenciales-hero {
    text-align: center;
    padding: 1rem 0 1.25rem;
    border-bottom: 1px dashed #eef0f4;
    margin-bottom: 1.25rem;
}
[data-layout-mode="dark"] .credenciales-hero { border-bottom-color: #2a2f34; }
.cred-avatar-lg {
    width: 80px; height: 80px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: 1.8rem;
    background: linear-gradient(135deg, #f7b84b 0%, #ff9f43 100%);
    color: #fff;
    box-shadow: 0 8px 20px -8px rgba(247,184,75,.55);
    margin-bottom: .75rem;
}
.cred-box {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
[data-layout-mode="dark"] .cred-box { background: #212529; border-color: #2a2f34; }
.cred-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .85rem 1.1rem;
    gap: 1rem;
}
.cred-row + .cred-row { border-top: 1px dashed #eef0f4; }
[data-layout-mode="dark"] .cred-row + .cred-row { border-top-color: #2a2f34; }
.cred-label { display: inline-flex; align-items: center; gap: .5rem; font-size: .78rem; font-weight: 600; color: #6c757d; }
.cred-label i {
    width: 28px; height: 28px;
    background: #eef1f8; color: #405189; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .95rem;
}
.cred-value {
    font-weight: 700;
    color: #212529;
    font-family: monospace;
    font-size: .95rem;
    background: #f8f9fc;
    padding: .3em .75em;
    border-radius: 6px;
}
[data-layout-mode="dark"] .cred-value { background: #1a1d20; color: #fff; }

/* Modal módulos contenido */
.programa-block {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1rem;
}
[data-layout-mode="dark"] .programa-block { background: #212529; border-color: #2a2f34; }
.programa-block-header {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    padding: .75rem 1rem;
    display: flex; align-items: center; gap: .65rem;
    border-bottom: 1px solid #eef0f4;
}
[data-layout-mode="dark"] .programa-block-header { background: #1a1d20; border-bottom-color: #2a2f34; }
.programa-block-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.programa-block-title { font-size: .9rem; font-weight: 700; color: #212529; margin: 0; }
[data-layout-mode="dark"] .programa-block-title { color: #fff; }
.programa-block table { margin: 0; }
.programa-block table thead th {
    background: #fafbfd;
    font-size: .68rem !important;
    text-transform: uppercase;
    letter-spacing: .3px;
    padding: .55rem .65rem;
    color: #6c757d;
    border-bottom: 1px solid #eef0f4;
    font-weight: 700;
}
.programa-block table tbody td { padding: .6rem .65rem; vertical-align: middle; font-size: .82rem; }
[data-layout-mode="dark"] .programa-block table thead th { background: #1d2125; border-bottom-color: #2a2f34; }
.modulo-badge {
    font-size: .68rem !important;
    padding: .25em .65em !important;
    border-radius: 999px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center; gap: 3px;
    border: 1px solid currentColor;
    background: transparent !important;
}
.modulo-badge::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

/* ===== Estudio card ===== */
.estudio-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: .85rem;
    transition: box-shadow .2s ease;
}
.estudio-card:hover { box-shadow: 0 4px 14px -8px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .estudio-card { background: #212529; border-color: #2a2f34; }
.estudio-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: .75rem;
}
.estudio-univ {
    font-weight: 700; font-size: .92rem; color: #212529; line-height: 1.3;
}
.estudio-grado {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .3px;
    padding: .2em .65em; border-radius: 999px;
}
.estudio-grado.is-end { background: #d1fae5; color: #0ab39c; }
.estudio-grado.is-curso { background: #dbeafe; color: #299cdb; }
.estudio-profesion {
    font-size: .82rem; color: #6c757d; margin-top: .25rem;
    display: flex; align-items: center; gap: .35rem;
}
.estudio-actions { display: flex; gap: .35rem; flex-shrink: 0; }

.area-badge {
    font-size: .68rem; font-weight: 600;
    padding: .15em .6em; border-radius: 999px;
    border: 1px solid;
    display: inline-flex; align-items: center;
    background: color-mix(in srgb, var(--area-color, #6c757d) 12%, transparent);
}

/* ===== Modal Form Estudio ===== */
#modalFormEstudio .input-group .btn-outline-primary {
    border-color: #e9ebec;
    border-radius: 0 8px 8px 0;
}
#modalFormEstudio .input-group .btn-outline-primary:hover {
    background: #405189;
    color: #fff;
    border-color: #405189;
}
[data-layout-mode="dark"] #modalFormEstudio .input-group .btn-outline-primary {
    border-color: #2a2f34;
    color: #ced4da;
}
[data-layout-mode="dark"] #modalFormEstudio .input-group .btn-outline-primary:hover {
    background: #405189;
    color: #fff;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="docentes-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-user-star-line"></i></div>
                <div>
                    <h4>Docentes</h4>
                    <p class="hero-sub">Administra el cuerpo docente, credenciales y módulos asignados</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item active">Docentes</li>
            </ol>
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
                    <div class="col-md-8">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filter-search" class="form-control" placeholder="Buscar por CI, nombres o apellidos..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button id="btn-filter-clear" class="btn btn-soft-danger w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="ri-filter-off-line"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card results-card">
            <div class="card-body">
                <div class="results-header">
                    <div class="results-count">
                        <i class="ri-user-star-line"></i>
                        <span><span id="docente-count" class="count-num">0</span> docentes encontrados</span>
                    </div>
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Responsable Académico']))
                    <button type="button" class="btn btn-success" id="btn-nuevo-docente">
                        <i class="ri-user-add-line align-bottom me-1"></i> Registrar Docente
                    </button>
                    @endif
                </div>
                <div id="table-container">
                    <div class="tabla-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted mb-0">Cargando docentes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Módulos del Docente --}}
<div class="modal fade" id="modalModulos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-stack-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalModulosTitle">Módulos del Docente</h5>
                        <small class="modal-subtitle">Programas y módulos asignados con su estado y pago</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalModulosBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando módulos...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

@can('docentes.credenciales')
{{-- Modal Credenciales Moodle --}}
<div class="modal fade" id="modalCredenciales" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-shield-keyhole-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalCredencialesTitle">Credenciales Moodle</h5>
                        <small class="modal-subtitle">Acceso a la plataforma educativa</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalCredencialesBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando credenciales...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cerrar</button>
                <button type="button" class="btn btn-success" id="btnEnviarWhatsAppCredenciales" disabled>
                    <i class="ri-whatsapp-line align-bottom"></i> Enviar por WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Modal Registrar Docente --}}
<div class="modal fade" id="modalNuevoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-user-add-line"></i></div>
                    <div>
                        <h5 class="modal-title">Registrar Docente</h5>
                        <small class="modal-subtitle">Ingresa los datos del nuevo docente</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- Step 1: Search by CI --}}
            <div id="nuevo-search-step">
                <div class="modal-body">
                    <div class="text-center py-2">
                        <div class="avatar-sm mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-4">
                                <i class="ri-search-line"></i>
                            </span>
                        </div>
                        <h6 class="fw-semibold">Buscar por Carnet de Identidad</h6>
                        <p class="text-muted small mb-3">Ingresa el CI del docente para verificar si ya está registrado</p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="ri-passport-line"></i></span>
                                    <input type="text" id="search-ci-input" class="form-control" placeholder="Ingresa el CI (mín. 7 dígitos)" maxlength="13" autocomplete="off">
                                    <button class="btn btn-primary" type="button" id="btn-search-ci">
                                        <i class="ri-search-line align-bottom me-1"></i> Buscar
                                    </button>
                                </div>
                                <div id="search-ci-feedback" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                </div>
            </div>

            {{-- Step 2: Register form --}}
            <div id="nuevo-register-step" style="display:none;">
                <form id="formNuevoDocente" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="form-section-icon bg-soft-primary text-primary"><i class="ri-user-line"></i></span>
                                <span>Datos Personales</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">CI <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-passport-line"></i></span>
                                        <input type="text" name="ci" id="nuevo-ci" class="form-control" required maxlength="13" readonly>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="nombres" id="nuevo-nombres" class="form-control" required maxlength="100">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" id="nuevo-apellidos" class="form-control" required maxlength="100">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="form-section-icon bg-soft-info text-info"><i class="ri-contacts-line"></i></span>
                                <span>Contacto</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Correo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="correo" id="nuevo-correo" class="form-control" required maxlength="100">
                                    </div>
                                    <div class="invalid-feedback"></div>
                                    <small class="form-text text-muted email-status"></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Celular <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                        <input type="text" name="celular" id="nuevo-celular" class="form-control" required maxlength="20">
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="form-section-icon bg-soft-warning text-warning"><i class="ri-shield-keyhole-line"></i></span>
                                <span>Credenciales Moodle</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-user-3-line"></i></span>
                                        <input type="text" name="usuario" id="nuevo-usuario" class="form-control" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                        <input type="password" name="contrasena" id="nuevo-contrasena" class="form-control" maxlength="50">
                                        <button class="btn btn-outline-secondary" type="button" id="toggle-nuevo-password" style="border-radius: 0 8px 8px 0;">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-section-title">
                                <span class="form-section-icon bg-soft-success text-success"><i class="ri-bank-line"></i></span>
                                <span>Datos Bancarios</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Banco</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-building-2-line"></i></span>
                                        <input type="text" name="banco" id="nuevo-banco" class="form-control" maxlength="200">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">N° Cuenta</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-bank-card-line"></i></span>
                                        <input type="text" name="n_cuenta" id="nuevo-n_cuenta" class="form-control" maxlength="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" id="btn-back-search">
                            <i class="ri-arrow-left-line align-bottom"></i> Volver
                        </button>
                        <button type="submit" class="btn btn-save-docente" id="btnSaveNuevoDocente">
                            <i class="ri-save-3-line align-bottom"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Modal Editar Docente --}}
<div class="modal fade" id="modalEditDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-edit-2-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalEditDocenteTitle">Editar Docente</h5>
                        <small class="modal-subtitle">Actualiza la información del docente</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditDocente" novalidate>
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="docente_id" id="edit-docente_id">
                <div class="modal-body">

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-primary text-primary"><i class="ri-user-line"></i></span>
                            <span>Datos Personales</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">CI <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-passport-line"></i></span>
                                    <input type="text" name="ci" id="edit-ci" class="form-control" required maxlength="10">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" id="edit-nombres" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" id="edit-apellidos" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-info text-info"><i class="ri-contacts-line"></i></span>
                            <span>Contacto</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Correo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                    <input type="email" name="correo" id="edit-correo" class="form-control" required maxlength="100">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Celular <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                    <input type="text" name="celular" id="edit-celular" class="form-control" required maxlength="20">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-warning text-warning"><i class="ri-shield-keyhole-line"></i></span>
                            <span>Credenciales Moodle</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-3-line"></i></span>
                                    <input type="text" name="usuario" id="edit-usuario" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                    <input type="password" name="contrasena" id="edit-contrasena" class="form-control" maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password" style="border-radius: 0 8px 8px 0;">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="form-section-icon bg-soft-success text-success"><i class="ri-bank-line"></i></span>
                            <span>Datos Bancarios</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Banco</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-building-2-line"></i></span>
                                    <input type="text" name="banco" id="edit-banco" class="form-control" maxlength="200">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N° Cuenta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-bank-card-line"></i></span>
                                    <input type="text" name="n_cuenta" id="edit-n_cuenta" class="form-control" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-save-docente" id="btnSaveDocente">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Modal Estudios del Docente --}}
<div class="modal fade" id="modalEstudios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-graduation-cap-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalEstudiosTitle">Estudios del Docente</h5>
                        <small class="modal-subtitle">Registro de formación académica</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalEstudiosBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando estudios...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnNuevoEstudio"><i class="ri-add-line align-bottom"></i> Agregar Estudio</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Formulario Estudio --}}
<div class="modal fade" id="modalFormEstudio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-graduation-cap-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalFormEstudioTitle">Agregar Estudio</h5>
                        <small class="modal-subtitle">Completa los datos del estudio académico</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEstudio" novalidate>
                @csrf
                <input type="hidden" name="estudio_id" id="form-estudio_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Universidad <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-building-4-line"></i></span>
                                <select name="universidad_id" id="est-universidad_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                                <button class="btn btn-outline-primary" type="button" title="Agregar nueva universidad" data-catalogo="universidad">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grado Académico <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-award-line"></i></span>
                                <select name="grado_academico_id" id="est-grado_academico_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                                <button class="btn btn-outline-primary" type="button" title="Agregar nuevo grado académico" data-catalogo="grado_academico">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-checkbox-circle-line"></i></span>
                                <select name="estado" id="est-estado" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="En curso">En curso</option>
                                    <option value="Concluido">Concluido</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Profesión <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-user-star-line"></i></span>
                                <select name="profesion_id" id="est-profesion_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                </select>
                                <button class="btn btn-outline-primary" type="button" title="Agregar nueva profesión" data-catalogo="profesion">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Áreas de competencia <span class="text-danger">*</span></label>
                            <div class="row g-2" id="est-areas-container">
                                <div class="text-muted small">Selecciona al menos un área</div>
                            </div>
                            <div class="invalid-feedback" id="est-areas-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cancelar</button>
                    <button type="submit" class="btn btn-save-docente" id="btnSaveEstudio"><i class="ri-save-3-line align-bottom"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal agregar catálogo --}}
<div class="modal fade" id="modalNuevoCatalogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-add-circle-line"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalNuevoCatalogoTitle">Agregar</h5>
                        <small class="modal-subtitle">Nuevo valor para el catálogo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoCatalogo" novalidate>
                @csrf
                <input type="hidden" name="tipo" id="catalogo-tipo">
                <div class="modal-body">
                    <label class="form-label" id="catalogo-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="catalogo-nombre" class="form-control" required maxlength="255" placeholder="Ingresa el nombre">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveCatalogo"><i class="ri-save-3-line align-bottom"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
@endsection

@php $authNombreWA = Auth::user()->fullname ?? Auth::user()->username ?? 'Administrador'; @endphp
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSearch = document.getElementById('filter-search');
    const tableContainer = document.getElementById('table-container');
    const countEl = document.getElementById('docente-count');

    let debounceTimer = null;
    let currentPage = 1;

    function loadData(page) {
        const params = new URLSearchParams();
        const search = filterSearch.value.trim();

        if (page) currentPage = page;
        params.append('page', currentPage);
        if (search) params.append('search', search);

        tableContainer.innerHTML = `
            <div class="tabla-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted mb-0">Cargando docentes...</p>
            </div>
        `;

        fetch('{{ route("admin.docentes.index") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            tableContainer.innerHTML = data.html;
            if (countEl) countEl.textContent = data.total;
        })
        .catch(() => {
            tableContainer.innerHTML = '<div class="text-center text-danger py-4">Error al cargar docentes</div>';
        });
    }

    filterSearch.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        currentPage = 1;
        debounceTimer = setTimeout(loadData, 300);
    });

    document.getElementById('btn-filter-clear').addEventListener('click', function () {
        filterSearch.value = '';
        currentPage = 1;
        loadData();
    });

    document.addEventListener('click', function (e) {
        const pageBtn = e.target.closest('.page-link[data-page]');
        if (pageBtn) {
            e.preventDefault();
            loadData(parseInt(pageBtn.dataset.page));
            return;
        }

        const moduloBtn = e.target.closest('.btn-ver-modulos');
        if (moduloBtn) {
            const docenteId = moduloBtn.dataset.docenteId;
            const docenteNombre = moduloBtn.dataset.docenteNombre;
            const modal = new bootstrap.Modal(document.getElementById('modalModulos'));
            document.getElementById('modalModulosTitle').textContent = docenteNombre;
            document.getElementById('modalModulosBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando módulos...</p>
                </div>
            `;
            modal.show();

            fetch('{{ url("admin/docentes") }}/' + docenteId + '/modulos', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                let html = '';
                if (data.programas.length === 0) {
                    html = '<div class="text-center py-5 text-muted"><i class="ri-inbox-2-line fs-48 d-block mb-2"></i>Sin módulos asignados</div>';
                } else {
                    data.programas.forEach(function (prog) {
                        html += `
                            <div class="programa-block">
                                <div class="programa-block-header">
                                    <span class="programa-block-icon"><i class="ri-booklet-line"></i></span>
                                    <h6 class="programa-block-title">${prog.programa}</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;" class="text-center">#</th>
                                                <th>Módulo</th>
                                                <th style="width: 130px;">Desarrollo</th>
                                                <th style="width: 100px;" class="text-end">Pago (Bs)</th>
                                                <th style="width: 140px;">Proceso Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;
                        prog.modulos.forEach(function (mod, i) {
                            const colorMap = {
                                'En desarrollo': '#299cdb',
                                'Desarrollado': '#0ab39c',
                                'Concluido': '#0ab39c',
                                'No Inicio': '#878a99',
                                'Pendiente': '#f7b84b',
                                'Suspendido': '#f06548',
                            };
                            const color = colorMap[mod.desarrollo] || '#878a99';
                            html += `
                                <tr>
                                    <td class="text-center text-muted fw-bold">${i + 1}</td>
                                    <td class="fw-semibold text-dark">${mod.nombre}</td>
                                    <td><span class="modulo-badge" style="color:${color};">${mod.desarrollo}</span></td>
                                    <td class="text-end fw-semibold text-success">${parseFloat(mod.pago_docente).toFixed(2)}</td>
                                    <td class="fs-12 text-truncate" style="max-width: 140px;" title="${mod.proceso_pago}">${mod.proceso_pago || '—'}</td>
                                </tr>
                            `;
                        });
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById('modalModulosBody').innerHTML = html;
            })
            .catch(function () {
                document.getElementById('modalModulosBody').innerHTML = '<div class="text-center text-danger py-4">Error al cargar módulos</div>';
            });
            return;
        }

        const credBtn = e.target.closest('.btn-credenciales');
        if (credBtn) {
            const docenteId = credBtn.dataset.docenteId;
            const modal = new bootstrap.Modal(document.getElementById('modalCredenciales'));
            document.getElementById('modalCredencialesBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando credenciales...</p>
                </div>
            `;
            modal.show();

            fetch('{{ url("admin/docentes") }}/' + docenteId + '/data', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const d = data.docente;
                const ini = (d.nombres.charAt(0) + d.apellidos.charAt(0)).toUpperCase();
                document.getElementById('modalCredencialesTitle').textContent = d.apellidos + ', ' + d.nombres;
                document.getElementById('modalCredencialesBody').innerHTML = `
                    <div class="credenciales-hero">
                        <div class="cred-avatar-lg">${ini}</div>
                        <h5 class="mb-1 fw-bold text-dark">${d.apellidos}, ${d.nombres}</h5>
                        <span class="badge bg-soft-primary text-primary fs-11"><i class="ri-passport-line align-bottom me-1"></i>CI: ${d.ci}</span>
                    </div>
                    <div class="cred-box">
                        <div class="cred-row">
                            <span class="cred-label"><i class="ri-user-3-line"></i> Usuario</span>
                            <span class="cred-value">${d.usuario || '—'}</span>
                        </div>
                        <div class="cred-row">
                            <span class="cred-label"><i class="ri-lock-line"></i> Contraseña</span>
                            <span class="cred-value">${d.contrasena || '—'}</span>
                        </div>
                    </div>
                `;
                const waBtn = document.getElementById('btnEnviarWhatsAppCredenciales');
                var cel = (d.celular || '').replace(/[^0-9]/g, '');
                if (cel.length === 8) cel = '591' + cel;
                if (cel && d.usuario && d.contrasena) {
                    var nombreDocente = ((d.apellidos || '') + ', ' + (d.nombres || '')).replace(/^, /, '');
                    var msg = 'Hola ' + nombreDocente + ','
                        + '\n\nPlataforma: https://moodle.esam.edu.bo/'
                        + '\nUsuario: ' + d.usuario
                        + '\nContraseña: ' + d.contrasena
                        + '\n\nAtentamente,'
                        + '\n{{ $authNombreWA }}'
                        + '\nÁrea Académica'
                        + '\nEsam Sucre Central';
                    waBtn.onclick = function () { window.open('https://wa.me/' + cel + '?text=' + encodeURIComponent(msg), '_blank'); };
                    waBtn.disabled = false;
                } else {
                    waBtn.disabled = true;
                    waBtn.onclick = null;
                }
            })
            .catch(function () {
                document.getElementById('modalCredencialesBody').innerHTML = '<div class="text-center text-danger py-4">Error al cargar credenciales</div>';
            });
            return;
        }

        const editBtn = e.target.closest('.btn-edit-docente');
        if (editBtn) {
            const docenteId = editBtn.dataset.docenteId;
            const modal = new bootstrap.Modal(document.getElementById('modalEditDocente'));
            document.getElementById('modalEditDocenteTitle').textContent = 'Editar Docente';
            document.getElementById('formEditDocente').reset();
            document.getElementById('formEditDocente').querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
            document.getElementById('formEditDocente').querySelectorAll('.invalid-feedback').forEach(function (el) { el.textContent = ''; });
            modal.show();

            fetch('{{ url("admin/docentes") }}/' + docenteId + '/data', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const d = data.docente;
                document.getElementById('edit-docente_id').value = d.id;
                document.getElementById('edit-ci').value = d.ci;
                document.getElementById('edit-nombres').value = d.nombres;
                document.getElementById('edit-apellidos').value = d.apellidos;
                document.getElementById('edit-correo').value = d.correo;
                document.getElementById('edit-celular').value = d.celular;
                document.getElementById('edit-usuario').value = d.usuario || '';
                document.getElementById('edit-contrasena').value = d.contrasena || '';
                document.getElementById('edit-banco').value = d.banco || '';
                document.getElementById('edit-n_cuenta').value = d.n_cuenta || '';
            })
            .catch(function () {
                alert('Error al cargar datos del docente');
            });
        }
    });

    document.getElementById('toggle-password').addEventListener('click', function () {
        const input = document.getElementById('edit-contrasena');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line';
        }
    });

    document.getElementById('formEditDocente').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('edit-docente_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        document.getElementById('btnSaveDocente').disabled = true;
        document.getElementById('btnSaveDocente').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('{{ url("admin/docentes") }}/' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditDocente')).hide();
                mostrarToast('success', data.message || 'Docente actualizado correctamente');
                loadData(currentPage);
            } else if (data.errors) {
                for (let field in data.errors) {
                    const input = document.getElementById('edit-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-md-4, .col-md-6')?.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = data.errors[field][0];
                    }
                }
            }
        })
        .catch(function () {
            alert('Error al guardar el docente');
        })
        .finally(function () {
            document.getElementById('btnSaveDocente').disabled = false;
            document.getElementById('btnSaveDocente').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // ---- NUEVO DOCENTE (Two-step) ----
    const modalNuevo = new bootstrap.Modal(document.getElementById('modalNuevoDocente'));
    const searchStep = document.getElementById('nuevo-search-step');
    const registerStep = document.getElementById('nuevo-register-step');
    const searchInput = document.getElementById('search-ci-input');
    const searchFeedback = document.getElementById('search-ci-feedback');
    const btnSearch = document.getElementById('btn-search-ci');

    function showSearchStep() {
        searchStep.style.display = '';
        registerStep.style.display = 'none';
        searchInput.value = '';
        searchFeedback.innerHTML = '';
        searchInput.focus();
        // Reset register form
        if (nuevoForm) nuevoForm.reset();
        document.querySelectorAll('#nuevo-register-step .is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        document.querySelectorAll('#nuevo-register-step .invalid-feedback').forEach(function (el) { el.textContent = ''; });
        document.querySelector('#nuevo-register-step .email-status').textContent = '';
    }

    function showRegisterStep(ci) {
        searchStep.style.display = 'none';
        registerStep.style.display = '';
        document.getElementById('nuevo-ci').value = ci;
        document.getElementById('nuevo-nombres').focus();
        document.getElementById('btnSaveNuevoDocente').disabled = false;
        document.getElementById('btnSaveNuevoDocente').innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
    }

    document.getElementById('btn-nuevo-docente')?.addEventListener('click', function () {
        showSearchStep();
        modalNuevo.show();
    });

    // Search by CI
    function searchByCi() {
        const ci = searchInput.value.trim();
        if (!ci) {
            searchFeedback.innerHTML = '<div class="text-danger small"><i class="ri-error-warning-line align-bottom me-1"></i>Ingresa un CI para buscar</div>';
            return;
        }
        if (ci.length < 7) {
            searchFeedback.innerHTML = '<div class="text-danger small"><i class="ri-error-warning-line align-bottom me-1"></i>El CI debe tener al menos 7 caracteres</div>';
            return;
        }
        searchFeedback.innerHTML = '<div class="text-muted small"><span class="spinner-border spinner-border-sm me-1"></span>Buscando...</div>';
        fetch('{{ route("admin.docentes.check-ci") }}?ci=' + encodeURIComponent(ci), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.exists) {
                const d = data.docente;
                searchFeedback.innerHTML = `
                    <div class="alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-0 mt-2">
                        <i class="ri-information-line fs-16"></i>
                        <span>El docente <strong>${d.apellidos}, ${d.nombres}</strong> (CI: ${d.ci}) ya está registrado.</span>
                    </div>
                `;
            } else {
                showRegisterStep(ci);
            }
        })
        .catch(function () {
            searchFeedback.innerHTML = '<div class="text-danger small"><i class="ri-error-warning-line align-bottom me-1"></i>Error al buscar</div>';
        });
    }

    btnSearch.addEventListener('click', searchByCi);
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchByCi();
        }
    });

    // Back to search
    document.getElementById('btn-back-search').addEventListener('click', function () {
        showSearchStep();
    });

    // Hidden when modal closes — reset to search step
    document.getElementById('modalNuevoDocente').addEventListener('hidden.bs.modal', function () {
        showSearchStep();
    });

    // Toggle password visibility
    document.getElementById('toggle-nuevo-password').addEventListener('click', function () {
        const input = document.getElementById('nuevo-contrasena');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line';
        }
    });

    // ---- REAL-TIME VALIDATION ----
    const nuevoForm = document.getElementById('formNuevoDocente');

    function showFieldError(input, msg) {
        input.classList.add('is-invalid');
        const fb = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-md-4, .col-md-6')?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = msg;
    }

    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        const fb = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-md-4, .col-md-6')?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = '';
    }

    // Required fields validation on blur
    ['nuevo-nombres', 'nuevo-apellidos', 'nuevo-correo', 'nuevo-celular'].forEach(function (id) {
        const input = document.getElementById(id);
        input.addEventListener('blur', function () {
            if (!this.value.trim()) {
                showFieldError(this, 'Este campo es requerido');
            } else {
                clearFieldError(this);
            }
        });
        input.addEventListener('input', function () {
            if (this.value.trim()) {
                clearFieldError(this);
            }
        });
    });

    // Email format + uniqueness validation
    let emailTimer = null;
    const emailInput = document.getElementById('nuevo-correo');
    emailInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            showFieldError(this, 'Ingresa un correo válido');
        }
    });
    emailInput.addEventListener('input', function () {
        clearTimeout(emailTimer);
        const val = this.value.trim();
        const statusEl = document.querySelector('#nuevo-register-step .email-status');
        if (!val || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            statusEl.textContent = '';
            return;
        }
        statusEl.textContent = 'Verificando...';
        statusEl.className = 'form-text email-status text-muted';
        emailTimer = setTimeout(function () {
            fetch('{{ route("admin.docentes.check-email") }}?email=' + encodeURIComponent(val), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    showFieldError(emailInput, 'Este correo ya está registrado');
                    statusEl.textContent = 'Correo no disponible';
                    statusEl.className = 'form-text email-status text-danger';
                } else {
                    clearFieldError(emailInput);
                    statusEl.textContent = 'Correo disponible';
                    statusEl.className = 'form-text email-status text-success';
                }
            })
            .catch(function () {
                statusEl.textContent = '';
            });
        }, 400);
    });

    // ---- SUBMIT ----
    nuevoForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear all previous errors
        nuevoForm.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        nuevoForm.querySelectorAll('.invalid-feedback').forEach(function (el) { el.textContent = ''; });

        const formData = new FormData(this);
        const btn = document.getElementById('btnSaveNuevoDocente');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('{{ route("admin.docentes.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                modalNuevo.hide();
                mostrarToast('success', data.message || 'Docente registrado correctamente');
                loadData(1);
            } else if (data.errors) {
                for (let field in data.errors) {
                    const input = document.getElementById('nuevo-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-md-4, .col-md-6')?.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = data.errors[field][0];
                    }
                }
            }
        })
        .catch(function () {
            alert('Error al guardar el docente');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // ---- ESTUDIOS ----
    let currentEstudioDocenteId = null;
    let catalogsCache = null;

    function cargarCatalogos() {
        if (catalogsCache) return Promise.resolve(catalogsCache);
        return fetch('{{ route("admin.docentes.estudios.catalogos") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            catalogsCache = data;
            return data;
        });
    }

    function poblarSelect(id, items, selectedId) {
        const select = document.getElementById(id);
        select.innerHTML = '<option value="">Seleccionar...</option>';
        items.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nombre;
            if (selectedId && item.id == selectedId) opt.selected = true;
            select.appendChild(opt);
        });
    }

    function poblarAreas(items, selectedIds) {
        const container = document.getElementById('est-areas-container');
        container.innerHTML = '<div class="text-muted small">Selecciona al menos un área</div>';
        if (!items || items.length === 0) return;
        const row = document.createElement('div');
        row.className = 'd-flex flex-wrap gap-2 mt-1';
        items.forEach(function (item) {
            const checked = selectedIds && selectedIds.indexOf(item.id) !== -1;
            const wrapper = document.createElement('div');
            wrapper.className = 'form-check form-check-inline mb-0';
            wrapper.innerHTML = `
                <input class="form-check-input area-checkbox" type="checkbox" name="area_ids[]" value="${item.id}" id="area-${item.id}" ${checked ? 'checked' : ''}>
                <label class="form-check-label" for="area-${item.id}">${item.nombre}</label>
            `;
            row.appendChild(wrapper);
        });
        container.appendChild(row);
    }

    function abrirFormEstudio(estudio) {
        const isEdit = !!estudio;
        document.getElementById('formEstudio').reset();
        document.getElementById('form-estudio_id').value = isEdit ? estudio.id : '';
        document.getElementById('modalFormEstudioTitle').textContent = isEdit ? 'Editar Estudio' : 'Agregar Estudio';
        document.getElementById('formEstudio').querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        document.getElementById('formEstudio').querySelectorAll('.invalid-feedback').forEach(function (el) { el.textContent = ''; });

        cargarCatalogos().then(function (cats) {
            poblarSelect('est-universidad_id', cats.universidades, isEdit ? estudio.universidad_id : null);
            poblarSelect('est-grado_academico_id', cats.grados_academicos, isEdit ? estudio.grado_academico_id : null);
            poblarSelect('est-profesion_id', cats.profesiones, isEdit ? estudio.profesion_id : null);
            poblarAreas(cats.areas, isEdit ? estudio.area_ids : []);
            if (isEdit) {
                document.getElementById('est-estado').value = estudio.estado;
            }
        });

        new bootstrap.Modal(document.getElementById('modalFormEstudio')).show();
    }

    document.addEventListener('click', function (e) {
        const estudioBtn = e.target.closest('.btn-estudios');
        if (estudioBtn) {
            currentEstudioDocenteId = estudioBtn.dataset.docenteId;
            const docenteNombre = estudioBtn.dataset.docenteNombre;
            const modal = new bootstrap.Modal(document.getElementById('modalEstudios'));
            document.getElementById('modalEstudiosTitle').textContent = 'Estudios - ' + docenteNombre;
            document.getElementById('modalEstudiosBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2 text-muted">Cargando estudios...</p>
                </div>
            `;
            modal.show();
            cargarEstudios(currentEstudioDocenteId);
            return;
        }

        const editEstudioBtn = e.target.closest('.btn-edit-estudio');
        if (editEstudioBtn) {
            const estudio = JSON.parse(editEstudioBtn.dataset.estudio);
            abrirFormEstudio(estudio);
            return;
        }

        const deleteEstudioBtn = e.target.closest('.btn-delete-estudio');
        if (deleteEstudioBtn) {
            if (!confirm('¿Estás seguro de eliminar este estudio?')) return;
            const estudioId = deleteEstudioBtn.dataset.id;
            const btn = deleteEstudioBtn;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            fetch('{{ url("admin/docentes/estudios") }}/' + estudioId, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    mostrarToast('success', data.message || 'Estudio eliminado');
                    cargarEstudios(currentEstudioDocenteId);
                }
            })
            .catch(function () {
                alert('Error al eliminar el estudio');
            });
            return;
        }

        const addCatalogoBtn = e.target.closest('[data-catalogo]');
        if (addCatalogoBtn && e.target.closest('#modalFormEstudio')) {
            const tipo = addCatalogoBtn.dataset.catalogo;
            const labels = {
                universidad: 'Universidad',
                grado_academico: 'Grado Académico',
                profesion: 'Profesión'
            };
            document.getElementById('catalogo-tipo').value = tipo;
            document.getElementById('catalogo-label').textContent = labels[tipo] || 'Nombre';
            document.getElementById('modalNuevoCatalogoTitle').textContent = 'Agregar ' + (labels[tipo] || '');
            document.getElementById('formNuevoCatalogo').reset();
            document.getElementById('formNuevoCatalogo').querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
            document.getElementById('formNuevoCatalogo').querySelectorAll('.invalid-feedback').forEach(function (el) { el.textContent = ''; });
            document.getElementById('catalogo-nombre').focus();
            new bootstrap.Modal(document.getElementById('modalNuevoCatalogo')).show();
            return;
        }
    });

    function cargarEstudios(docenteId) {
        Promise.all([
            fetch('{{ url("admin/docentes") }}/' + docenteId + '/estudios', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()),
            cargarCatalogos()
        ])
        .then(function ([data, cats]) {
            const areaMap = {};
            (cats.areas || []).forEach(function (a) { areaMap[a.id] = a; });

            let html = '';
            if (data.estudios.length === 0) {
                html = '<div class="text-center py-5 text-muted"><i class="ri-graduation-cap-line fs-48 d-block mb-2"></i>Sin estudios registrados</div>';
            } else {
                data.estudios.forEach(function (est) {
                    const isEnd = est.estado === 'Concluido';
                    const gradoClass = isEnd ? 'is-end' : 'is-curso';
                    const icon = isEnd ? 'ri-checkbox-circle-line' : 'ri-loader-4-line';

                    let areasHtml = '';
                    if (est.area_ids && est.area_ids.length > 0) {
                        const chips = est.area_ids.map(function (aid) {
                            const a = areaMap[aid];
                            return '<span class="area-badge" ' + (a && a.color ? 'style="--area-color:' + a.color + ';border-color:' + a.color + ';color:' + a.color + ';"' : '') + '>' + (a ? a.nombre : aid) + '</span>';
                        });
                        areasHtml = '<div class="d-flex flex-wrap gap-1 mt-1">' + chips.join('') + '</div>';
                    }

                    html += `
                        <div class="estudio-card">
                            <div class="estudio-header">
                                <div style="min-width:0;">
                                    <div class="estudio-univ text-truncate" title="${est.universidad}"><i class="ri-building-4-line text-primary me-1"></i>${est.universidad}</div>
                                    <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                        <span class="estudio-grado ${gradoClass}"><i class="${icon}"></i>${est.grado_academico}</span>
                                        <span class="estudio-grado ${gradoClass}"><i class="${icon}"></i>${est.estado}</span>
                                    </div>
                                    <div class="estudio-profesion"><i class="ri-user-star-line text-muted"></i>${est.profesion}</div>
                                    ${areasHtml}
                                </div>
                                <div class="estudio-actions">
                                    <button class="btn btn-sm btn-icon btn-soft-warning btn-edit-estudio" data-estudio='${JSON.stringify(est).replace(/'/g, "&#39;")}' data-bs-toggle="tooltip" title="Editar"><i class="ri-pencil-line"></i></button>
                                    <button class="btn btn-sm btn-icon btn-soft-danger btn-delete-estudio" data-id="${est.id}" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            document.getElementById('modalEstudiosBody').innerHTML = html;
        })
        .catch(function () {
            document.getElementById('modalEstudiosBody').innerHTML = '<div class="text-center text-danger py-4">Error al cargar estudios</div>';
        });
    }

    document.getElementById('btnNuevoEstudio').addEventListener('click', function () {
        abrirFormEstudio(null);
    });

    document.getElementById('formNuevoCatalogo').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const btn = document.getElementById('btnSaveCatalogo');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('{{ route("admin.docentes.estudios.catalogo.store") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevoCatalogo')).hide();
                catalogsCache = null;
                const tipo = document.getElementById('catalogo-tipo').value;
                cargarCatalogos().then(function (cats) {
                    const map = { universidad: 'est-universidad_id', grado_academico: 'est-grado_academico_id', profesion: 'est-profesion_id' };
                    const listMap = { universidad: 'universidades', grado_academico: 'grados_academicos', profesion: 'profesiones' };
                    poblarSelect(map[tipo], cats[listMap[tipo]], data.id);
                });
                mostrarToast('success', 'Agregado correctamente');
            } else if (data.errors) {
                for (let field in data.errors) {
                    const input = document.getElementById('catalogo-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const fb = input.parentElement.querySelector('.invalid-feedback');
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
            } else if (data.error) {
                const input = document.getElementById('catalogo-nombre');
                input.classList.add('is-invalid');
                const fb = input.parentElement.querySelector('.invalid-feedback');
                if (fb) fb.textContent = data.error;
            }
        })
        .catch(function () {
            alert('Error al guardar');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // ---- REAL-TIME VALIDATION ESTUDIOS ----
    function showEstError(input, msg) {
        input.classList.add('is-invalid');
        const fb = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-12, .col-md-6')?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = msg;
    }

    function clearEstError(input) {
        input.classList.remove('is-invalid');
        const fb = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-12, .col-md-6')?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = '';
    }

    function validarAreaRealtime() {
        const areaFeedback = document.getElementById('est-areas-feedback');
        const checks = document.querySelectorAll('.area-checkbox:checked');
        if (checks.length === 0) {
            areaFeedback.textContent = 'Debes seleccionar al menos un área';
        } else {
            areaFeedback.textContent = '';
        }
    }

    ['est-universidad_id', 'est-grado_academico_id', 'est-profesion_id', 'est-estado'].forEach(function (id) {
        const input = document.getElementById(id);
        input.addEventListener('change', function () {
            if (this.value) {
                clearEstError(this);
            } else {
                showEstError(this, 'Debes seleccionar una opción');
            }
        });
        input.addEventListener('blur', function () {
            if (!this.value) {
                showEstError(this, 'Debes seleccionar una opción');
            }
        });
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('area-checkbox')) {
            validarAreaRealtime();
        }
    });

    document.getElementById('formEstudio').addEventListener('submit', function (e) {
        e.preventDefault();

        let hasError = false;

        ['est-universidad_id', 'est-grado_academico_id', 'est-profesion_id', 'est-estado'].forEach(function (id) {
            const input = document.getElementById(id);
            if (!input.value) {
                showEstError(input, 'Debes seleccionar una opción');
                hasError = true;
            }
        });

        const areaChecks = document.querySelectorAll('.area-checkbox:checked');
        const areaFeedback = document.getElementById('est-areas-feedback');
        if (areaChecks.length === 0) {
            areaFeedback.textContent = 'Debes seleccionar al menos un área';
            hasError = true;
        } else {
            areaFeedback.textContent = '';
        }

        if (hasError) return;

        const estudioId = document.getElementById('form-estudio_id').value;
        const isEdit = !!estudioId;
        const formData = new FormData(this);
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const btn = document.getElementById('btnSaveEstudio');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const url = isEdit
            ? '{{ url("admin/docentes/estudios") }}/' + estudioId
            : '{{ url("admin/docentes") }}/' + currentEstudioDocenteId + '/estudios';

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalFormEstudio')).hide();
                mostrarToast('success', data.message);
                cargarEstudios(currentEstudioDocenteId);
            } else if (data.errors) {
                for (let field in data.errors) {
                    const input = document.getElementById('est-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback') || input.closest('.col-12, .col-md-6')?.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = data.errors[field][0];
                    }
                }
            }
        })
        .catch(function () {
            alert('Error al guardar el estudio');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    loadData();
});

function mostrarToast(type, message) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    const bg = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line';
    const html = '<div class="toast align-items-center text-white ' + bg + ' border-0 show" role="alert">' +
        '<div class="d-flex">' +
            '<div class="toast-body d-flex align-items-center gap-2"><i class="' + icon + '"></i>' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
        '</div>' +
    '</div>';
    toastContainer.insertAdjacentHTML('beforeend', html);
    setTimeout(function () {
        const t = toastContainer.querySelector('.toast');
        if (t) t.remove();
    }, 4000);
}
</script>
@endpush
