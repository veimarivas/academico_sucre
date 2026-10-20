@extends('admin.layouts.master')

@section('title', $programa->nombre . ' - ' . config('app.name'))

@push('css')
<style>
.table-detalle th { font-weight: 600; font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; }
.table-detalle td { vertical-align: middle; }
.chart-legends { display: flex; flex-wrap: wrap; justify-content: center; gap: 4px 12px; margin-bottom: 8px; }
.chart-legends span { font-size: .75rem; display: inline-flex; align-items: center; gap: 4px; color: #555; font-weight: 500; }
.legend-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
#asigdoc-ci { transition: border-color .2s; }
#asigdoc-ci:focus { border-color: #f7b84b; box-shadow: 0 0 0 0.15rem rgba(247,184,75,.25); }
.table-desarrollo { min-width: 900px; }
.table-desarrollo td, .table-desarrollo th { white-space: nowrap; }
.table-desarrollo th.mod-col, .table-desarrollo td.mod-cell { white-space: normal !important; }
.cell-grades-seq {
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    gap: 4px;
    line-height: 1.2;
}
.grade-seq {
    font-size: .72rem;
    font-weight: 700;
    padding: .15em .45em;
    border-radius: 4px;
    border: 1px solid;
    white-space: nowrap;
}
.grade-mod { color: #f06548; background: rgba(240,101,72,.1); border-color: rgba(240,101,72,.2); }
.grade-niv.niv-ok { color: #0ab39c; background: rgba(10,179,156,.08); border-color: rgba(10,179,156,.25); }
.grade-niv.niv-fail { color: #f06548; background: rgba(240,101,72,.08); border-color: rgba(240,101,72,.2); }
.grade-nivp.nivp-ok { color: #0ab39c; background: rgba(10,179,156,.12); border-color: rgba(10,179,156,.35); }
.grade-nivp.nivp-fail { color: #f06548; background: rgba(240,101,72,.12); border-color: rgba(240,101,72,.3); }
.table-desarrollo thead th {
    position: sticky;
    top: 0;
    z-index: 3;
    background: var(--vz-light, #f8f9fa);
    box-shadow: inset 0 1px 0 rgba(0,0,0,.04), inset 0 -1px 0 rgba(0,0,0,.06);
}
.table-desarrollo .sticky-col {
    position: sticky;
    background: #fff;
    z-index: 2;
}
.table-desarrollo .sticky-col-1 { left: 0; }
.table-desarrollo .sticky-col-2 { left: 46px; }
.table-desarrollo .sticky-col-3 { left: 205px; }
.table-desarrollo .sticky-col-4 { left: 370px; }
.table-desarrollo thead th.sticky-col { top: 0; z-index: 4; }
/* Columnas de módulos más angostas para que se vean las notas */
.table-desarrollo thead th.mod-col {
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100px;
    min-width: 90px;
    line-height: 1.3;
    vertical-align: middle;
    font-size: .7rem;
}
.table-desarrollo tbody td.mod-cell { max-width: 100px; min-width: 90px; }
.table-desarrollo .cell-grades-seq { flex-wrap: wrap; gap: 3px; justify-content: center; }
.table-desarrollo .grade-seq { font-size: .64rem; padding: .1em .32em; }
.table-desarrollo-wrap {
    overflow: auto;
    max-height: 520px;
}
.mod-group-0 { background: transparent; }
.mod-group-1 { background: rgba(0,0,0,.02); }

/* ===== Tabla Seguimiento Contable ===== */
.table-contable { min-width: 1200px; border-collapse: separate; border-spacing: 0; }
.table-contable th, .table-contable td { white-space: nowrap; padding: .55rem .55rem; font-size: .78rem; vertical-align: middle; }
.table-contable thead th {
    position: sticky; top: 0; z-index: 3;
    background: #f3f5f9 !important;
    color: #495057;
    font-weight: 700; font-size: .68rem;
    text-transform: uppercase; letter-spacing: .4px;
    box-shadow: inset 0 -2px 0 #e9ebec;
    text-align: center;
}
.table-contable .sticky-col {
    position: sticky;
    background: #fff;
    z-index: 2;
    border-right: 1px solid #eef0f4;
}
.table-contable thead th.sticky-col { z-index: 4; }
.table-contable .stk-1 { left: 0; min-width: 40px; max-width: 40px; }
.table-contable .stk-2 { left: 40px; min-width: 200px; max-width: 220px; }
.table-contable .stk-3 { left: 240px; min-width: 110px; text-align: right !important; background: #fafbfd; }
.table-contable .stk-4 { left: 350px; min-width: 110px; text-align: right !important; background: #fafbfd; border-right: 2px solid #d6dbe0; }
.table-contable thead th.stk-3 { background: #eef1f8 !important; }
.table-contable thead th.stk-4 { background: #eef1f8 !important; border-right: 2px solid #d6dbe0; }
[data-layout-mode="dark"] .table-contable .sticky-col { background: #1d2125; border-right-color: #2a2f34; }
[data-layout-mode="dark"] .table-contable .stk-3,
[data-layout-mode="dark"] .table-contable .stk-4 { background: #16191c; }

.contable-wrap { overflow: auto; max-height: 600px; border: 1px solid #eef0f4; border-radius: 12px; }
[data-layout-mode="dark"] .contable-wrap { border-color: #2a2f34; }

.table-contable tbody td.col-mod {
    text-align: center;
    min-width: 130px;
    border-right: 1px dashed #f1f3f5;
}
.table-contable tbody tr:nth-child(even) td:not(.sticky-col):not(.total-deuda-col) { background-color: rgba(0,0,0,.015); }
[data-layout-mode="dark"] .table-contable tbody tr:nth-child(even) td:not(.sticky-col):not(.total-deuda-col) { background-color: rgba(255,255,255,.02); }

.cell-mod {
    display: flex; flex-direction: column;
    gap: 2px;
    font-size: .74rem; line-height: 1.25;
    padding: 4px 2px;
}
.cell-mod .lbl { font-size: .6rem; color: #878a99; text-transform: uppercase; letter-spacing: .3px; font-weight: 700; }
.cell-mod .row-line {
    display: flex; align-items: center; justify-content: space-between;
    gap: .4rem;
    padding: 1px 6px;
    border-radius: 4px;
}
.cell-mod .row-line .lbl { flex: 0 0 auto; }
.cell-mod .row-line .val { font-weight: 700; color: #212529; font-size: .76rem; }
.cell-mod .row-line.deuda-row { background: rgba(64,81,137,.06); }
.cell-mod .row-line.dto-row { background: rgba(247,184,75,.08); }
.cell-mod .row-line.pagado-row { background: rgba(10,179,156,.08); }
.cell-mod .row-line.pagado-row .val { color: #0ab39c; }
.cell-mod .row-line.dto-row .val { color: #d97706; }
.cell-mod .saldo-line {
    margin-top: 4px;
    padding-top: 4px;
    border-top: 1px dashed #eef0f4;
    display: flex; align-items: center; justify-content: center;
    gap: .35rem;
}
.cell-mod .saldo-val {
    font-weight: 800;
    padding: .25em .7em;
    border-radius: 999px;
    font-size: .78rem;
    display: inline-flex; align-items: center; gap: 4px;
    border: 1px solid;
}
.cell-mod .saldo-val.deuda { background: #fee2e2; color: #f06548; border-color: #fecaca; }
.cell-mod .saldo-val.pagado { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.cell-mod .saldo-val.parcial { background: #fef3c7; color: #d97706; border-color: #fde68a; }

/* === Botón Definir Fechas === */
.btn-fechas {
    display: inline-flex; align-items: center; gap: 5px;
    padding: .25em .7em;
    font-size: .72rem; font-weight: 700;
    border: 1px solid #d4d8dd; border-radius: 999px;
    background: #f8f9fa; color: #495057;
    transition: all .15s;
    cursor: pointer;
}
.btn-fechas:hover { background: #4facfe; color: #fff; border-color: #4facfe; }
.btn-fechas.has-data { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.btn-fechas .bdg {
    display: inline-flex; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 50%;
    background: rgba(0,0,0,.1); font-size: .6rem; font-weight: 800;
}
.btn-fechas.has-data .bdg { background: rgba(10,179,156,.15); }



/* === Grid Fechas Participante === */
.fechas-grid-participante {
    display: flex; flex-direction: column; gap: 16px;
}
.fechas-col-participante {
    background: #fafbfd; border-radius: 10px; padding: 12px 14px;
    border: 1px solid #eef0f4;
}
.fechas-col-participante .fechas-col-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px;
}
.fechas-col-participante .fechas-col-title {
    font-weight: 700; font-size: .82rem; display: flex; align-items: center; gap: 5px;
}
.fechas-list .fecha-row {
    display: flex; align-items: center; gap: 8px;
    padding: 4px 0;
    border-bottom: 1px solid #f1f3f5;
}
.fechas-list .fecha-row:last-child { border-bottom: none; }
.fechas-list .fecha-row .num {
    width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%;
    background: #eef0f4;
    font-size: .7rem; font-weight: 700; color: #495057;
    flex-shrink: 0;
}
.fechas-list .fecha-row input[type=date] {
    flex: 1;
    padding: 3px 8px;
    font-size: .78rem;
    border: 1px solid #d4d8dd;
    border-radius: 6px;
    outline: none;
    transition: border-color .15s;
}
.fechas-list .fecha-row input[type=date]:focus {
    border-color: #4facfe;
    box-shadow: 0 0 0 2px rgba(79,172,254,.15);
}
.fechas-list .fecha-row .info-cell {
    font-size: .65rem; color: #878a99;
    display: flex; align-items: center; gap: 3px;
}

/* === Generador de fechas === */
.fechas-gen {
    background: #f1f5f9; border-radius: 8px; padding: 8px 10px; margin-bottom: 8px;
}
.fechas-gen-title {
    font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
    color: #878a99; margin-bottom: 6px; display: flex; align-items: center; gap: 4px;
}
.fechas-gen-row {
    display: flex; align-items: flex-end; gap: 6px; flex-wrap: wrap;
}
.fechas-gen-row .grp {
    display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 80px;
}
.fechas-gen-row .grp label {
    font-size: .6rem; font-weight: 700; text-transform: uppercase; color: #878a99; letter-spacing: .3px;
}
.fechas-gen-row .grp input {
    padding: 4px 8px; font-size: .75rem; border: 1px solid #d4d8dd; border-radius: 6px; outline: none;
    transition: border-color .15s;
}
.fechas-gen-row .grp input:focus {
    border-color: #4facfe; box-shadow: 0 0 0 2px rgba(79,172,254,.15);
}
.fechas-gen-row .btn-apply-gen {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 4px 10px; font-size: .7rem; font-weight: 700;
    border: 1px solid #4facfe; border-radius: 6px;
    background: #4facfe; color: #fff; cursor: pointer;
    transition: all .15s; white-space: nowrap;
}
.fechas-gen-row .btn-apply-gen:hover { background: #3d8cdb; border-color: #3d8cdb; }

#modalFechasParticipante .modal-header-icon {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%; background: #dbeafe; color: #4facfe; font-size: 1.1rem;
}

/* Encabezado de columna módulo más legible */
.table-contable .col-mod-header {
    background: linear-gradient(135deg, #eef1f8 0%, #dbeafe 100%) !important;
    font-weight: 700 !important;
    color: #405189 !important;
    border-bottom: 2px solid #c3ddfa !important;
}
.col-mod {
    min-width: 150px !important;
    border-right: 1px dashed #eef0f4 !important;
    vertical-align: top !important;
}

.cell-empty {
    color: #adb5bd;
    font-style: italic;
    font-size: .72rem;
}

.cost-cell { font-weight: 700; color: #212529; }
[data-layout-mode="dark"] .cost-cell { color: #fff; }
.cost-mat { color: #299cdb !important; }
.cost-col { color: #0ab39c !important; }

.total-deuda-col {
    position: sticky; right: 0;
    background: #fafbfd !important;
    border-left: 2px solid #d6dbe0;
    z-index: 10;
    min-width: 140px;
    text-align: right !important;
    box-shadow: -4px 0 8px -4px rgba(0,0,0,.12);
}
[data-layout-mode="dark"] .total-deuda-col { background: #16191c !important; box-shadow: -4px 0 8px -4px rgba(0,0,0,.35); }
.table-contable thead th.total-deuda-col { background: #eef1f8 !important; z-index: 11; }
[data-layout-mode="dark"] .table-contable thead th.total-deuda-col { background: #1d2125 !important; z-index: 11; }

.participante-cell {
    display: flex; align-items: center; gap: .5rem;
}
.pc-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-weight: 700; font-size: .68rem;
    flex-shrink: 0;
}
.pc-info { min-width: 0; }
.pc-name { font-weight: 600; color: #212529; font-size: .8rem; line-height: 1.2; white-space: normal; }
[data-layout-mode="dark"] .pc-name { color: #fff; }
.pc-ci { font-size: .65rem; color: #878a99; }

/* === Summary cards contable === */
.cont-summary-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    height: 100%;
    transition: box-shadow .2s ease;
    box-shadow: 0 4px 14px -8px rgba(64,81,137,.15);
}
.cont-summary-card:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .cont-summary-card { background: #212529; border-color: #2a2f34; }
.cont-card-mat { border-left: 4px solid #299cdb; }
.cont-card-col { border-left: 4px solid #0ab39c; }
.cont-card-total {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%);
    border: 0;
    color: #fff;
}
.cont-card-header { display: flex; align-items: center; gap: .65rem; margin-bottom: .85rem; padding-bottom: .65rem; border-bottom: 1px dashed #eef0f4; }
.cont-card-total .cont-card-header { border-bottom-color: rgba(255,255,255,.18); }
.cont-card-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.cont-card-title { font-weight: 700; color: #212529; font-size: .9rem; line-height: 1.1; }
[data-layout-mode="dark"] .cont-card-title { color: #fff; }
.cont-card-sub { font-size: .68rem; color: #878a99; margin-top: 2px; }
.cont-card-rows { display: flex; flex-direction: column; gap: .25rem; }
.cont-row {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .82rem; color: #495057;
    padding: .15rem 0;
}
.cont-row > span { display: inline-flex; align-items: center; gap: .35rem; font-weight: 600; }
.cont-row strong { font-size: .95rem; font-weight: 800; }
.cont-row.saldo-row { border-top: 1px dashed #eef0f4; padding-top: .35rem; margin-top: .2rem; }
.cont-progress {
    height: 6px; background: rgba(255,255,255,.18); border-radius: 999px; overflow: hidden;
}
.cont-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0ab39c, #fff);
    border-radius: 999px;
    transition: width .5s ease;
}

/* ===== Show Programa - Hero Header ===== */
.show-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 16px;
    padding: 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 12px 32px -14px rgba(64,81,137,.5);
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
.show-hero .hero-icon-lg {
    width: 70px; height: 70px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 18px;
    font-size: 2rem;
    backdrop-filter: blur(4px);
}
.show-hero h3 { color: #fff; font-weight: 700; letter-spacing: .2px; line-height: 1.3; }
.show-hero .hero-badge {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    padding: .35em .75em;
    border-radius: 999px;
    backdrop-filter: blur(4px);
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}
.show-hero .btn-volver {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    font-weight: 600;
    backdrop-filter: blur(4px);
    transition: all .2s ease;
}
.show-hero .btn-volver:hover {
    background: rgba(255,255,255,.28);
    color: #fff;
    transform: translateY(-1px);
}
.show-hero .btn-inscribir {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 8px 20px -8px rgba(10,179,156,.6);
    transition: all .15s ease;
}
.show-hero .btn-inscribir:hover:not(.disabled):not(:disabled) { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 24px -10px rgba(10,179,156,.75); }
.show-hero .btn-inscribir.disabled,
.show-hero .btn-inscribir:disabled {
    background: rgba(255,255,255,.12);
    color: rgba(255,255,255,.55);
    box-shadow: none;
    cursor: not-allowed;
    pointer-events: auto;
}

.show-hero .btn-toggle-insc {
    border: 1px solid rgba(255,255,255,.3);
    font-weight: 600;
    color: #fff;
    backdrop-filter: blur(4px);
    transition: all .15s ease;
}
.show-hero .btn-toggle-insc.off {
    background: rgba(247,184,75,.25);
    border-color: rgba(247,184,75,.55);
}
.show-hero .btn-toggle-insc.on {
    background: rgba(10,179,156,.3);
    border-color: rgba(10,179,156,.6);
}
.show-hero .btn-toggle-insc:hover { color: #fff; transform: translateY(-1px); }

/* ===== Modal Inscribir Participante ===== */
#modalInscribirP .modal-content { border: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(0,0,0,.25); }
#modalInscribirP .modal-header {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%);
    color: #fff; border: 0; padding: 1.1rem 1.5rem;
    position: relative; overflow: hidden;
}
#modalInscribirP .modal-header::after {
    content: ""; position: absolute; top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalInscribirP .modal-header > * { position: relative; z-index: 1; }
#modalInscribirP .modal-header-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 12px;
    font-size: 1.25rem; color: #fff;
}
#modalInscribirP .modal-title { color: #fff; font-weight: 700; }
#modalInscribirP .modal-subtitle { color: rgba(255,255,255,.85); font-size: .78rem; }
#modalInscribirP .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalInscribirP .modal-body { padding: 1.5rem; background: #fafbfd; }
#modalInscribirP .modal-footer { background: #fff; border-top: 1px solid #eef0f4; padding: 1rem 1.5rem; }

.inscribir-section {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}
.inscribir-section-title {
    display: flex; align-items: center; gap: .55rem;
    font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: #495057;
    margin-bottom: .85rem;
    padding-bottom: .6rem;
    border-bottom: 1px dashed #eef0f4;
}
.inscribir-section-icon {
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: 1rem;
}

.ci-search-box {
    position: relative;
}
.ci-search-box i.search-i {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    color: #878a99; font-size: 1.1rem;
}
.ci-search-box input {
    width: 100%;
    border: 1px solid #e9ebec;
    border-radius: 10px;
    padding: .65rem 2.5rem .65rem 2.4rem;
    font-size: 1rem; font-weight: 600;
    transition: all .2s ease;
}
.ci-search-box input:focus { outline: 0; border-color: #0ab39c; box-shadow: 0 0 0 .15rem rgba(10,179,156,.15); }
.ci-search-spinner {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%);
    color: #0ab39c;
    display: none;
}
.ci-search-box.loading .ci-search-spinner { display: inline-block; }

.ci-result-box { margin-top: .85rem; }
.ci-result-card {
    border: 1px solid #c3ddfa;
    background: linear-gradient(135deg, #dbeafe 0%, #eef1f8 100%);
    border-radius: 10px;
    padding: .85rem 1rem;
    display: flex; align-items: center; gap: .85rem;
}
.ci-result-card.notfound {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #fde68a;
}
.ci-result-card.warning {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-color: #fecaca;
}
.ci-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff; font-weight: 700; font-size: .9rem;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ci-info { flex: 1; min-width: 0; }
.ci-nombre { font-weight: 700; color: #212529; font-size: .95rem; line-height: 1.2; }
.ci-meta { font-size: .72rem; color: #6c757d; margin-top: 2px; }

.monto-card {
    display: flex; align-items: center; gap: .65rem;
    padding: .8rem .9rem;
    border-radius: 10px;
    border: 1px solid #eef0f4;
    background: #fff;
}
.monto-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.monto-label { font-size: .7rem; color: #878a99; text-transform: uppercase; font-weight: 700; letter-spacing: .4px; }
.monto-valor { font-size: 1.05rem; font-weight: 800; color: #212529; line-height: 1.1; }
.monto-deuda { font-size: .7rem; color: #f06548; font-weight: 700; margin-top: 2px; }
.monto-deuda.ok { color: #0ab39c; }

.btn-inscribir-save {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    border: 0; color: #fff; font-weight: 700;
    padding: .55rem 1.4rem;
    box-shadow: 0 6px 16px -6px rgba(10,179,156,.55);
    transition: transform .15s ease;
}
.btn-inscribir-save:hover:not(:disabled) { color: #fff; transform: translateY(-1px); }
.btn-inscribir-save:disabled { opacity: .6; }

/* Asesor dropdown */
.asesor-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 10px;
    box-shadow: 0 12px 28px -10px rgba(0,0,0,.2);
    max-height: 240px; overflow-y: auto;
    z-index: 1080;
    display: none;
}
.asesor-dropdown.open { display: block; }
.asesor-dropdown::-webkit-scrollbar { width: 6px; }
.asesor-dropdown::-webkit-scrollbar-thumb { background: #d6dbe0; border-radius: 3px; }
.asesor-opt {
    padding: .55rem .75rem;
    cursor: pointer;
    display: flex; align-items: center; gap: .55rem;
    border-bottom: 1px dashed #f1f3f5;
    transition: background-color .15s ease;
}
.asesor-opt:last-child { border-bottom: 0; }
.asesor-opt:hover { background: #f8f9fc; }
.asesor-opt-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f7b84b 0%, #ff9f43 100%);
    color: #fff; font-weight: 700; font-size: .7rem;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.asesor-opt-info { flex: 1; min-width: 0; }
.asesor-opt-nombre { font-weight: 600; color: #212529; font-size: .82rem; line-height: 1.2; }
.asesor-opt-ci { font-size: .68rem; color: #878a99; }
.asesor-empty { padding: .85rem; text-align: center; color: #878a99; font-size: .8rem; font-style: italic; }

.asesor-selected-card {
    display: inline-flex; align-items: center; gap: .55rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #fde68a;
    border-radius: 999px;
    padding: .3rem .35rem .3rem .35rem;
    font-size: .82rem; font-weight: 600;
    color: #92400e;
}
.asesor-selected-card .ri-close-circle-line { cursor: pointer; opacity: .7; }
.asesor-selected-card .ri-close-circle-line:hover { opacity: 1; color: #f06548; }

/* Cuotas */
.cuotas-resumen-box {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border: 1px solid #eef0f4;
    border-radius: 10px;
    padding: .85rem 1rem;
}
.cuotas-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #fff;
    border: 1px solid #c3ddfa;
    color: #299cdb;
    border-radius: 999px;
    padding: .3em .85em;
    font-size: .8rem; font-weight: 600;
}
.cuotas-pill strong { font-size: .9rem; }
.cuotas-pill-warning { color: #f7b84b; border-color: #fde68a; background: #fffbeb; }

.cuotas-detalle {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 8px;
    overflow: hidden;
    max-height: 220px;
    overflow-y: auto;
}
.cuotas-detalle table { margin: 0; width: 100%; font-size: .8rem; }
.cuotas-detalle thead th {
    background: #fafbfd !important;
    font-size: .65rem; text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d; padding: .5rem .65rem;
    border-bottom: 1px solid #eef0f4;
}
.cuotas-detalle tbody td { padding: .45rem .65rem; border-bottom: 1px dashed #f1f3f5; vertical-align: middle; }
.cuotas-detalle tbody tr:last-child td { border-bottom: 0; }
.cuotas-detalle tbody tr.ultima { background: #fffbeb; }
.show-hero .resp-bar {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,.18);
}
.show-hero .resp-label { color: rgba(255,255,255,.85); font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
.show-hero .resp-pill {
    background: rgba(255,255,255,.95);
    color: #495057;
    font-size: .76rem;
    font-weight: 500;
    padding: .35em .75em;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

/* ===== Stat Cards ===== */
.stat-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
    position: relative;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before {
    content: ""; position: absolute;
    top: 0; left: 0; width: 4px; height: 100%;
    background: var(--stat-accent, #405189);
}
.stat-card .stat-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #878a99;
    font-weight: 700;
    margin-bottom: .4rem;
}
.stat-card .stat-value { font-size: 1.15rem; font-weight: 700; color: #212529; line-height: 1.2; }
.stat-card .stat-icon {
    width: 44px; height: 44px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 12px;
    font-size: 1.25rem;
}
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }

/* ===== Tabs Show ===== */
.show-tabs-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .show-tabs-card { border-color: #2a2f34; }
.nav-tabs-show {
    border: 1px solid var(--ui-line);
    gap: .35rem;
    background: var(--ui-surface-2);
    padding: .4rem;
    border-radius: 12px;
    display: inline-flex;
    flex-wrap: wrap;
}
.nav-tabs-show .nav-link {
    border: 0 !important;
    border-radius: 9px;
    color: var(--ui-ink-2);
    font-weight: 600;
    font-size: .82rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: all .18s ease;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}
.nav-tabs-show .nav-link:hover { color: var(--ui-accent); background: rgba(64,81,137,.08); }
.nav-tabs-show .nav-link.active {
    background: #405189;
    color: #fff !important;
    box-shadow: 0 6px 14px -8px rgba(64,81,137,.6);
}
.nav-tabs-show .nav-link .badge { font-size: .68rem; padding: .25em .55em; }
.nav-tabs-show .nav-link.active .badge { background: rgba(255,255,255,.25) !important; color: #fff !important; }

/* ===== Chart Cards ===== */
.chart-card {
    border: 1px solid #eef0f4 !important;
    border-radius: 12px !important;
    transition: box-shadow .2s ease;
}
.chart-card:hover { box-shadow: 0 8px 22px -12px rgba(64,81,137,.3) !important; }
[data-layout-mode="dark"] .chart-card { border-color: #2a2f34 !important; }

/* ===== Chart Title ===== */
.chart-card .card-body { padding: 1.25rem; }
.chart-title {
    text-align: center;
    margin-bottom: 1rem;
    padding-bottom: .75rem;
    border-bottom: 1px dashed #eef0f4;
    color: #495057;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.chart-title i { font-size: 1rem; color: #405189; }
[data-layout-mode="dark"] .chart-title { color: #ced4da; border-bottom-color: #2a2f34; }

/* ===== Show tables ===== */
.show-tabs-card .table {
    margin-bottom: 0;
}
.show-tabs-card .table thead th {
    font-size: .7rem !important;
    font-weight: 700 !important;
    letter-spacing: .4px;
    padding: .75rem .55rem;
    border-bottom: 2px solid #eef0f4;
    background: #f8f9fc !important;
}
.show-tabs-card .table tbody td {
    padding: .65rem .55rem;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
}
.show-tabs-card .table-card {
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}
[data-layout-mode="dark"] .show-tabs-card .table thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .show-tabs-card .table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .show-tabs-card .table-card { border-color: #2a2f34; }

.show-tabs-card .table tbody tr { transition: background-color .15s ease; }
.show-tabs-card .table-hover tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .show-tabs-card .table-hover tbody tr:hover { background-color: #1a1d20; }

/* Compactar tablas de tabs */
.show-tabs-card .table-responsive { overflow-x: auto; }
.show-tabs-card .table { table-layout: auto; width: 100%; }
.show-tabs-card .table td, .show-tabs-card .table th { white-space: normal; }
.show-tabs-card .table td.text-end, .show-tabs-card .table th.text-end { white-space: nowrap; }

/* Tabla Inscritos compacta */
.tabla-inscritos {
    font-size: .82rem;
}
.tabla-inscritos th { font-size: .68rem !important; padding: .65rem .45rem !important; }
.tabla-inscritos td { padding: .65rem .45rem !important; }
.cell-participante { min-width: 180px; max-width: 240px; }
.cell-participante .nombre { font-weight: 600; color: #212529; font-size: .82rem; line-height: 1.25; }
.cell-participante .ci-info { font-size: .7rem; color: #6c757d; margin-top: 2px; }
.traspaso-badge { display: inline-flex; align-items: center; gap: 2px; background: rgba(240,101,72,.12); color: #f06548; font-size: .65rem; font-weight: 600; padding: 1px 5px; border-radius: 3px; vertical-align: middle; }
.traspaso-badge i { font-size: .7rem; }
.traspaso-row { background: rgba(240,101,72,.04) !important; }
.baja-academica-row { background: rgba(247,184,75,.12) !important; }
.baja-academica-row:hover td { background: rgba(247,184,75,.18) !important; }
.table-desarrollo .baja-academica-row td.sticky-col { background: rgba(247,184,75,.12) !important; }
.table-desarrollo .baja-academica-row:hover td.sticky-col { background: rgba(247,184,75,.18) !important; }
.baja-contable-row { background: rgba(41,156,219,.10) !important; }
.baja-contable-row:hover td { background: rgba(41,156,219,.16) !important; }
.table-desarrollo .baja-contable-row td.sticky-col { background: rgba(41,156,219,.10) !important; }
.table-desarrollo .baja-contable-row:hover td.sticky-col { background: rgba(41,156,219,.16) !important; }
[data-layout-mode="dark"] .baja-academica-row { background: rgba(247,184,75,.18) !important; }
[data-layout-mode="dark"] .baja-academica-row:hover td { background: rgba(247,184,75,.24) !important; }
[data-layout-mode="dark"] .table-desarrollo .baja-academica-row td.sticky-col { background: rgba(247,184,75,.18) !important; }
[data-layout-mode="dark"] .table-desarrollo .baja-academica-row:hover td.sticky-col { background: rgba(247,184,75,.24) !important; }
[data-layout-mode="dark"] .baja-contable-row { background: rgba(41,156,219,.15) !important; }
[data-layout-mode="dark"] .baja-contable-row:hover td { background: rgba(41,156,219,.22) !important; }
[data-layout-mode="dark"] .table-desarrollo .baja-contable-row td.sticky-col { background: rgba(41,156,219,.15) !important; }
[data-layout-mode="dark"] .table-desarrollo .baja-contable-row:hover td.sticky-col { background: rgba(41,156,219,.22) !important; }
.cell-contacto { font-size: .7rem; min-width: 140px; }
.cell-contacto .line { display: block; line-height: 1.3; }
.cell-money { min-width: 95px; font-size: .78rem; }
.money-stack { display: flex; flex-direction: column; align-items: flex-end; line-height: 1.2; gap: 1px; }
.money-stack .deuda { font-weight: 600; color: #212529; }
.money-stack .pagado { font-size: .68rem; color: #0ab39c; font-weight: 600; }
.money-stack .pendiente { font-size: .68rem; color: #f06548; font-weight: 600; }
.money-stack .neutral { font-size: .68rem; color: #adb5bd; font-weight: 500; }
[data-layout-mode="dark"] .cell-participante .nombre { color: #fff; }
[data-layout-mode="dark"] .money-stack .deuda { color: #fff; }

/* Pills compactas en tablas */
.pill-mini {
    font-size: .68rem;
    padding: .25em .55em;
    font-weight: 600;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1.2;
}
.sexo-h { background: #dbeafe; color: #3b82f6; }
.sexo-m { background: #fce7f3; color: #ec4899; }
[data-layout-mode="dark"] .sexo-h { background: rgba(59,130,246,.18); }
[data-layout-mode="dark"] .sexo-m { background: rgba(236,72,153,.18); }

/* Seguimiento Académico table */
.tabla-seguimiento thead th {
    background: #f8f9fc !important;
    font-size: .7rem !important;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: .85rem .75rem;
}
.tabla-seguimiento tbody td { padding: .85rem .75rem; vertical-align: middle; }
.tabla-seguimiento tbody tr { transition: background-color .15s ease; }
.tabla-seguimiento tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .tabla-seguimiento thead th { background: #1a1d20 !important; }
[data-layout-mode="dark"] .tabla-seguimiento tbody tr:hover { background-color: #1a1d20; }

.modulo-num {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 9px;
    font-weight: 700;
    font-size: .8rem;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.5);
}

.modulo-estado-badge {
    font-size: .72rem !important;
    padding: .45em .85em !important;
    border-radius: 999px !important;
    font-weight: 600 !important;
    border: 1px solid currentColor !important;
    background: transparent !important;
    display: inline-flex !important;
    align-items: center;
    gap: 4px;
}
.modulo-estado-badge::before {
    content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor;
}

.docente-cell { display: inline-flex; align-items: center; gap: .5rem; }
.docente-avatar {
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #299cdb 0%, #3577f1 100%);
    color: #fff;
    font-weight: 700;
    font-size: .7rem;
    flex-shrink: 0;
}
.docente-nombre { font-weight: 600; color: #212529; font-size: .82rem; }
.docente-rol { font-size: .68rem; color: #878a99; }
[data-layout-mode="dark"] .docente-nombre { color: #fff; }

/* ===== Módulos: navegación y seguimiento ===== */
.modulos-header-actions { gap: .5rem; }
.modulos-subtabs {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    padding: .45rem;
    margin: .15rem 0 0 !important;
    border: 1px solid var(--ui-line) !important;
    border-radius: 12px;
    background: var(--ui-surface-2);
}
.modulos-subtabs .nav-item { margin: 0; }
.modulos-subtabs .nav-link {
    display: inline-flex;
    align-items: center;
    min-height: 38px;
    padding: .52rem .78rem;
    border: 1px solid transparent !important;
    border-radius: 8px !important;
    color: var(--ui-ink-2);
    font-size: .78rem;
    font-weight: 700;
    line-height: 1.2;
    transition: background-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
}
.modulos-subtabs .nav-link:hover {
    background: var(--ui-surface);
    color: var(--ui-accent);
    border-color: var(--ui-line-strong) !important;
}
.modulos-subtabs .nav-link.active {
    background: #405189;
    border-color: #405189 !important;
    color: #fff !important;
    box-shadow: 0 5px 12px -8px rgba(64,81,137,.8);
}
.modulos-subtabs .nav-link:focus-visible {
    outline: 0;
    box-shadow: 0 0 0 3px rgba(64,81,137,.25);
}
.modulos-subtabs .badge { font-size: .62rem; font-weight: 700; }
.modulos-subtabs .nav-link.active .badge { background: rgba(255,255,255,.2) !important; color: #fff !important; }

.tabla-seguimiento { min-width: 1060px; }
.tabla-seguimiento tbody tr { border-left: 3px solid transparent; }
.tabla-seguimiento tbody tr:hover { border-left-color: #405189; }
.tabla-seguimiento tbody td + td { border-left: 1px solid rgba(238,240,244,.7); }
.tabla-seguimiento .modulo-nombre { color: #27304a; line-height: 1.35; }
.tabla-seguimiento .acciones-modulo { min-width: 185px; }
.tabla-seguimiento .acciones-modulo .hstack { flex-wrap: wrap; }
.tabla-seguimiento .notas-indicators { min-width: 115px; }
[data-layout-mode="dark"] .modulos-subtabs { background: #20252b; border-color: #30363d !important; }
[data-layout-mode="dark"] .modulos-subtabs .nav-link { color: #b8c1cc; }
[data-layout-mode="dark"] .modulos-subtabs .nav-link:hover { background: #292f36; border-color: #3b434d !important; color: #fff; }
[data-layout-mode="dark"] .tabla-seguimiento tbody td + td { border-left-color: rgba(42,47,52,.8); }
[data-layout-mode="dark"] .tabla-seguimiento .modulo-nombre { color: #fff; }

@media (max-width: 767.98px) {
    .modulos-header-actions { width: 100%; }
    .modulos-header-actions .btn { flex: 1 1 auto; justify-content: center; }
    .modulos-subtabs .nav-item { flex: 1 1 100%; }
    .modulos-subtabs .nav-link { width: 100%; justify-content: space-between; }
}

/* ===== Modales unificados ===== */
.modal-content.modal-pro,
#modalSesiones .modal-content,
#modalSesionExtra .modal-content,
#modalPagos .modal-content,
#modalInscritos .modal-content,
#modalEditarSesion .modal-content,
#modalEditarModulo .modal-content,
#modalAsignarDocente .modal-content {
    border: 0 !important;
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,.25) !important;
}
#modalSesiones .modal-header,
#modalSesionExtra .modal-header,
#modalPagos .modal-header,
#modalInscritos .modal-header,
#modalEditarSesion .modal-header,
#modalEditarModulo .modal-header,
#modalAsignarDocente .modal-header {
    background: linear-gradient(135deg, #405189 0%, #5e72b8 60%, #299cdb 100%) !important;
    color: #fff !important;
    border: 0 !important;
    padding: 1rem 1.5rem !important;
    position: relative;
    overflow: hidden;
}
#modalSesiones .modal-header::after,
#modalSesionExtra .modal-header::after,
#modalEditarSesion .modal-header::after,
#modalEditarModulo .modal-header::after,
#modalAsignarDocente .modal-header::after,
#modalPagos .modal-header::after,
#modalInscritos .modal-header::after {
    content: ""; position: absolute;
    top: -30px; right: -30px;
    width: 140px; height: 140px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
#modalSesiones .modal-header > *,
#modalSesionExtra .modal-header > *,
#modalPagos .modal-header > *,
#modalInscritos .modal-header > *,
#modalEditarSesion .modal-header > *,
#modalEditarModulo .modal-header > *,
#modalAsignarDocente .modal-header > * { position: relative; z-index: 1; }
#modalSesiones .modal-title,
#modalSesionExtra .modal-title,
#modalPagos .modal-title,
#modalInscritos .modal-title,
#modalEditarSesion .modal-title,
#modalEditarModulo .modal-title,
#modalAsignarDocente .modal-title { color: #fff !important; font-weight: 700 !important; font-size: 1rem !important; }
#modalSesiones .modal-header .btn-close,
#modalSesionExtra .modal-header .btn-close,
#modalPagos .modal-header .btn-close,
#modalInscritos .modal-header .btn-close,
#modalEditarSesion .modal-header .btn-close,
#modalEditarModulo .modal-header .btn-close,
#modalAsignarDocente .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .85; }
#modalSesiones .modal-header .avatar-title,
#modalSesionExtra .modal-header .avatar-title,
#modalEditarSesion .modal-header .avatar-title,
#modalEditarModulo .modal-header .avatar-title,
#modalAsignarDocente .modal-header .avatar-title {
    background: rgba(255,255,255,.2) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,.3);
}

/* Modal footers */
#modalSesiones .modal-footer,
#modalSesionExtra .modal-footer,
#modalPagos .modal-footer,
#modalInscritos .modal-footer,
#modalEditarSesion .modal-footer,
#modalEditarModulo .modal-footer,
#modalAsignarDocente .modal-footer {
    background: #fafbfd !important;
    border-top: 1px solid #eef0f4 !important;
    padding: 1rem 1.5rem !important;
}
[data-layout-mode="dark"] #modalSesiones .modal-footer,
[data-layout-mode="dark"] #modalSesionExtra .modal-footer,
[data-layout-mode="dark"] #modalPagos .modal-footer,
[data-layout-mode="dark"] #modalInscritos .modal-footer,
[data-layout-mode="dark"] #modalEditarSesion .modal-footer,
[data-layout-mode="dark"] #modalEditarModulo .modal-footer,
[data-layout-mode="dark"] #modalAsignarDocente .modal-footer { background: #1a1d20 !important; border-top-color: #2a2f34 !important; }

/* Modal body styling */
#modalSesionExtra .modal-body,
#modalEditarSesion .modal-body,
#modalEditarModulo .modal-body,
#modalAsignarDocente .modal-body { padding: 1.5rem; background: #fafbfd; }
[data-layout-mode="dark"] #modalSesionExtra .modal-body,
[data-layout-mode="dark"] #modalEditarSesion .modal-body,
[data-layout-mode="dark"] #modalEditarModulo .modal-body,
[data-layout-mode="dark"] #modalAsignarDocente .modal-body { background: #1a1d20; }

#modalSesionExtra .form-label,
#modalEditarSesion .form-label,
#modalEditarModulo .form-label,
#modalAsignarDocente .form-label {
    font-size: .72rem !important;
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 700 !important;
    color: #6c757d !important;
    margin-bottom: .35rem;
}
#modalSesionExtra .form-control,
#modalSesionExtra .form-select,
#modalEditarSesion .form-control,
#modalEditarSesion .form-select,
#modalEditarModulo .form-select,
#modalAsignarDocente .form-control,
#modalAsignarDocente .form-select {
    border-radius: 8px !important;
    border: 1px solid #e9ebec !important;
    transition: all .2s ease;
    font-size: .9rem;
    background: #fff;
}
#modalSesionExtra .form-control:focus,
#modalSesionExtra .form-select:focus,
#modalEditarSesion .form-control:focus,
#modalEditarSesion .form-select:focus,
#modalEditarModulo .form-select:focus,
#modalAsignarDocente .form-control:focus,
#modalAsignarDocente .form-select:focus {
    border-color: #405189 !important;
    box-shadow: 0 0 0 .15rem rgba(64,81,137,.12) !important;
}

/* Tabla en modal sesiones */
#modalSesiones .modal-body { padding: 0; background: #fff; }
#modalSesiones .table { margin: 0; }
#modalSesiones .table thead th {
    background: #f8f9fc !important;
    font-size: .7rem !important;
    font-weight: 700;
    color: #495057;
    letter-spacing: .4px;
    padding: .75rem .5rem;
    border-bottom: 2px solid #eef0f4;
}
#modalSesiones .table tbody td { padding: .65rem .5rem; vertical-align: middle; font-size: .82rem; }
[data-layout-mode="dark"] #modalSesiones .table thead th { background: #1a1d20 !important; }

/* Bloque info en editar sesión */
#edit-sesion-info {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%) !important;
    border-radius: 10px;
    padding: 1rem !important;
    border-left: 4px solid #405189;
    font-size: .85rem;
    line-height: 1.6;
}
[data-layout-mode="dark"] #edit-sesion-info { background: linear-gradient(135deg, #1a1d20 0%, #212529 100%) !important; }

/* Botones modales */
#modalSesiones .modal-footer .btn,
#modalSesionExtra .modal-footer .btn,
#modalPagos .modal-footer .btn,
#modalInscritos .modal-footer .btn,
#modalEditarSesion .modal-footer .btn,
#modalEditarModulo .modal-footer .btn,
#modalAsignarDocente .modal-footer .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: .5rem 1.1rem;
}
.btn-modal-save {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%) !important;
    border: 0 !important;
    color: #fff !important;
    box-shadow: 0 6px 16px -6px rgba(10,179,156,.55);
}
.btn-modal-save:hover { color: #fff !important; transform: translateY(-1px); box-shadow: 0 10px 22px -8px rgba(10,179,156,.7); }

/* Stat-card responsive size for narrow values */
.stat-card.is-currency .stat-value { font-size: .95rem; }
@media (max-width: 1399.98px) {
    .stat-card .stat-value { font-size: 1rem; }
}

/* ===== Section Card ===== */
.section-card {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
}
[data-layout-mode="dark"] .section-card { border-color: #2a2f34; }
.section-card .card-header {
    background: transparent;
    border-bottom: 1px dashed #eef0f4;
    padding: 1rem 1.25rem;
}
[data-layout-mode="dark"] .section-card .card-header { border-bottom-color: #2a2f34; }

/* ===== Tabla Fase Académica Concluida ===== */
.table-fase-concluida { min-width: 1600px; border-collapse: separate; border-spacing: 0; }
.table-fase-concluida th, .table-fase-concluida td { white-space: nowrap; padding: .65rem .55rem; font-size: .78rem; vertical-align: middle; }
.table-fase-concluida thead th {
    position: sticky; top: 0; z-index: 5;
    background: #f3f5f9 !important;
    color: #495057;
    font-weight: 700; font-size: .68rem;
    text-transform: uppercase; letter-spacing: .4px;
    box-shadow: inset 0 -2px 0 #e9ebec;
    text-align: center;
}
.table-fase-concluida tbody td { border-bottom: 1px solid #f1f3f5; }
.table-fase-concluida tbody tr { transition: background-color .15s ease; }
.table-fase-concluida tbody tr:hover { background-color: #f8f9fc; }
[data-layout-mode="dark"] .table-fase-concluida tbody tr:hover { background-color: #1a1d20; }
[data-layout-mode="dark"] .table-fase-concluida thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; }

.table-fase-concluida .sticky-col {
    position: sticky;
    background: #fff;
    z-index: 3;
    border-right: 1px solid #eef0f4;
}
.table-fase-concluida .sticky-col::after {
    content: ''; position: absolute; right: -6px; top: 0; bottom: 0; width: 6px;
    background: linear-gradient(90deg, rgba(0,0,0,.06), transparent);
    pointer-events: none;
}
.table-fase-concluida thead th.sticky-col { z-index: 6; background: #f3f5f9 !important; }
.table-fase-concluida thead th.sticky-col::after { display: none; }
.table-fase-concluida .stk-1 { left: 0; min-width: 40px; max-width: 40px; width: 40px; }
.table-fase-concluida .stk-2 { left: 40px; min-width: 200px; max-width: 220px; }
.table-fase-concluida tbody td.sticky-col.stk-1,
.table-fase-concluida tbody td.sticky-col.stk-2 { background: #fff; }
.table-fase-concluida tbody tr:hover td.sticky-col.stk-1,
.table-fase-concluida tbody tr:hover td.sticky-col.stk-2 { background: #f8f9fc; }
.table-fase-concluida tbody tr.fase-no-procede td.sticky-col.stk-1,
.table-fase-concluida tbody tr.fase-no-procede td.sticky-col.stk-2 { background: rgba(240, 101, 72, .06); }
.table-fase-concluida tbody tr.fase-no-procede:hover td.sticky-col.stk-1,
.table-fase-concluida tbody tr.fase-no-procede:hover td.sticky-col.stk-2 { background: rgba(240, 101, 72, .1); }
[data-layout-mode="dark"] .table-fase-concluida tbody td.sticky-col.stk-1,
[data-layout-mode="dark"] .table-fase-concluida tbody td.sticky-col.stk-2 { background: #212529; }
[data-layout-mode="dark"] .table-fase-concluida tbody tr:hover td.sticky-col.stk-1,
[data-layout-mode="dark"] .table-fase-concluida tbody tr:hover td.sticky-col.stk-2 { background: #1a1d20; }
[data-layout-mode="dark"] .table-fase-concluida thead th.sticky-col { background: #1a1d20 !important; }
[data-layout-mode="dark"] .table-fase-concluida tbody tr.fase-no-procede td.sticky-col.stk-1,
[data-layout-mode="dark"] .table-fase-concluida tbody tr.fase-no-procede td.sticky-col.stk-2 { background: rgba(240, 101, 72, .12); }
[data-layout-mode="dark"] .table-fase-concluida tbody tr.fase-no-procede:hover td.sticky-col.stk-1,
[data-layout-mode="dark"] .table-fase-concluida tbody tr.fase-no-procede:hover td.sticky-col.stk-2 { background: rgba(240, 101, 72, .18); }

.fase-wrap {
    overflow: auto;
    max-height: 680px;
    border: 1px solid #eef0f4;
    border-radius: 12px;
}
[data-layout-mode="dark"] .fase-wrap { border-color: #2a2f34; }

.fase-progress-dot {
    width: 24px; height: 24px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .58rem; font-weight: 700; flex-shrink: 0;
}
.fase-progress-dot.done { background: #0ab39c; color: #fff; }
.fase-progress-dot.pending { background: #e9ebec; color: #878a99; }
.fase-progress-dot.current {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #0ab39c;
}
.fase-progress-line {
    width: 10px; height: 2px; flex-shrink: 0;
}
.fase-progress-line.done { background: #0ab39c; }
.fase-progress-line.pending { background: #e9ebec; }

.fase-etapa-item {
    padding: .35rem .45rem;
    border-bottom: 1px dashed #f1f3f5;
    font-size: .72rem;
    line-height: 1.3;
}
.fase-etapa-item:last-child { border-bottom: 0; }
.fase-etapa-item .doc-label {
    display: inline-block; max-width: 120px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.fase-no-procede td {
    background-color: rgba(240, 101, 72, .06) !important;
}
.fase-no-procede td.sticky-col.stk-1,
.fase-no-procede td.sticky-col.stk-2 {
    background-color: rgba(240, 101, 72, .06) !important;
}
.fase-no-procede:hover td {
    background-color: rgba(240, 101, 72, .1) !important;
}
.fase-no-procede:hover td.sticky-col.stk-1,
.fase-no-procede:hover td.sticky-col.stk-2 {
    background-color: rgba(240, 101, 72, .1) !important;
}
[data-layout-mode="dark"] .fase-no-procede td {
    background-color: rgba(240, 101, 72, .12) !important;
}
[data-layout-mode="dark"] .fase-no-procede td.sticky-col.stk-1,
[data-layout-mode="dark"] .fase-no-procede td.sticky-col.stk-2 {
    background-color: rgba(240, 101, 72, .12) !important;
}

/* ===== Tab Rentabilidad - Diseño Mejorado ===== */
.rent-section { margin-bottom: 1.25rem; }
.rent-section-header {
    display: flex; align-items: center; gap: .65rem;
    margin-bottom: .85rem; padding-bottom: .65rem;
    border-bottom: 1px dashed #eef0f4;
}
.rent-section-header .rent-section-icon {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px; font-size: 1rem; flex-shrink: 0;
}
.rent-section-title {
    font-weight: 700; color: #212529; font-size: .82rem; line-height: 1.2;
}
.rent-section-sub {
    font-size: .65rem; color: #878a99; font-weight: 500; margin-top: 1px;
}
[data-layout-mode="dark"] .rent-section-title { color: #fff; }
[data-layout-mode="dark"] .rent-section-header { border-bottom-color: #2a2f34; }

.rent-param-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 16px;
    padding: 1.1rem 1.1rem .95rem;
    height: 100%;
    transition: all .25s ease;
    box-shadow: 0 2px 8px -6px rgba(64,81,137,.08);
    position: relative;
    overflow: hidden;
}
.rent-param-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--rent-accent, #405189);
    opacity: .7;
}
.rent-param-card:hover {
    box-shadow: 0 10px 28px -14px rgba(64,81,137,.22);
    transform: translateY(-2px);
    border-color: #dbe0e8;
}
[data-layout-mode="dark"] .rent-param-card { background: #212529; border-color: #2a2f34; }
[data-layout-mode="dark"] .rent-param-card:hover { border-color: #3a4046; }

.rent-param-header {
    display: flex; align-items: center; gap: .6rem;
    margin-bottom: .7rem; padding-bottom: .6rem;
    border-bottom: 1px dashed #f1f3f5;
}
[data-layout-mode="dark"] .rent-param-header { border-bottom-color: #2a2f34; }
.rent-param-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.rent-param-title { font-weight: 700; color: #212529; font-size: .82rem; line-height: 1.2; }
[data-layout-mode="dark"] .rent-param-title { color: #fff; }
.rent-param-sub { font-size: .65rem; color: #878a99; margin-top: 1px; font-weight: 500; }
.rent-param-body { display: flex; flex-direction: column; gap: .55rem; }
.rent-param-field label {
    display: block;
    font-size: .64rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .3px; color: #878a99; margin-bottom: 3px;
}
.rent-param-field .form-control, .rent-param-field .form-select {
    border-radius: 8px; border: 1px solid #e9ebec;
    font-weight: 700; font-size: .82rem; transition: all .2s;
    padding: .35rem .65rem;
}
.rent-param-field .form-control:focus, .rent-param-field .form-select:focus {
    border-color: var(--rent-accent, #405189);
    box-shadow: 0 0 0 3px rgba(64,81,137,.1);
}
[data-layout-mode="dark"] .rent-param-field .form-control,
[data-layout-mode="dark"] .rent-param-field .form-select {
    background: #1d2125; border-color: #2a2f34; color: #fff;
}
[data-layout-mode="dark"] .rent-param-field .form-control:focus,
[data-layout-mode="dark"] .rent-param-field .form-select:focus {
    border-color: #5e72b8; box-shadow: 0 0 0 3px rgba(94,114,184,.15);
}

.rent-result-card {
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 55%, #299cdb 100%);
    border: 0; border-radius: 16px;
    padding: 1.25rem 1.25rem 1.1rem;
    height: 100%; color: #fff;
    position: relative; overflow: hidden;
    box-shadow: 0 8px 24px -10px rgba(10,179,156,.45);
    transition: all .25s ease;
}
.rent-result-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px -14px rgba(10,179,156,.55);
}
.rent-result-card::after {
    content: ''; position: absolute;
    top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,.12) 0%, transparent 70%);
    border-radius: 50%;
}
.rent-result-card > * { position: relative; z-index: 1; }
.rent-result-header {
    display: flex; align-items: center; gap: .6rem;
    margin-bottom: .75rem; padding-bottom: .6rem;
    border-bottom: 1px solid rgba(255,255,255,.18);
}
.rent-result-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1rem; background: rgba(255,255,255,.22);
    flex-shrink: 0;
}
.rent-result-title { font-weight: 700; font-size: .84rem; line-height: 1.2; }
.rent-result-sub { font-size: .66rem; color: rgba(255,255,255,.85); margin-top: 1px; }
.rent-result-rows { display: flex; flex-direction: column; gap: .35rem; }
.rent-result-row {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .82rem; color: rgba(255,255,255,.92);
    padding: .1rem 0;
}
.rent-result-row strong { font-size: 1rem; color: #fff; }
.rent-result-row .rent-result-big { font-size: 1.2rem; font-weight: 800; }
.rent-result-margin-bar {
    height: 6px; background: rgba(255,255,255,.2); border-radius: 999px;
    overflow: hidden; margin-top: .5rem;
}
.rent-result-margin-fill {
    height: 100%;
    background: linear-gradient(90deg, #fff 0%, rgba(255,255,255,.85) 100%);
    border-radius: 999px; transition: width .5s ease;
}

.rent-summary-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    height: 100%;
    transition: all .25s ease;
    box-shadow: 0 2px 8px -6px rgba(64,81,137,.06);
    border-left: 4px solid var(--rent-accent, #405189);
}
.rent-summary-card:hover {
    box-shadow: 0 10px 28px -14px rgba(64,81,137,.22);
    transform: translateY(-2px);
}
[data-layout-mode="dark"] .rent-summary-card {
    background: #212529; border-color: #2a2f34;
}
.rent-summary-card .cont-card-rows { gap: .15rem; }

.rent-viabilidad-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    height: 100%;
    border-left: 4px solid #6559cc;
    transition: all .25s ease;
    box-shadow: 0 2px 8px -6px rgba(64,81,137,.06);
}
.rent-viabilidad-card:hover {
    box-shadow: 0 10px 28px -14px rgba(64,81,137,.22);
    transform: translateY(-2px);
}
[data-layout-mode="dark"] .rent-viabilidad-card { background: #212529; border-color: #2a2f34; }

.rent-breakeven-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    height: 100%;
    border-left: 4px solid #405189;
    transition: all .25s ease;
    box-shadow: 0 2px 8px -6px rgba(64,81,137,.06);
}
.rent-breakeven-card:hover {
    box-shadow: 0 10px 28px -14px rgba(64,81,137,.22);
    transform: translateY(-2px);
}
[data-layout-mode="dark"] .rent-breakeven-card { background: #212529; border-color: #2a2f34; }

.rent-bar-chart {
    display: flex; align-items: flex-end; justify-content: space-between;
    height: 100px; gap: 10px; padding: 0 6px;
}
.rent-bar-group {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px;
}
.rent-bar-value {
    font-size: .72rem; font-weight: 800; line-height: 1.1;
    transition: color .3s;
}
.rent-bar {
    width: 100%; border-radius: 6px 6px 0 0;
    transition: height .5s cubic-bezier(.22,1,.36,1);
    min-height: 20px;
    position: relative;
}
.rent-bar-actual { background: linear-gradient(180deg, #405189 0%, #5e72b8 100%); }
.rent-bar-pe { background: linear-gradient(180deg, #f06548 0%, #ff8a6c 100%); }
.rent-bar-proy { background: linear-gradient(180deg, #0ab39c 0%, #14b8a6 100%); opacity: .55; }
.rent-bar-label {
    font-size: .6rem; color: #878a99; font-weight: 600;
    text-transform: uppercase; letter-spacing: .3px;
}

.rent-table-card {
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .25s ease;
    box-shadow: 0 2px 8px -6px rgba(64,81,137,.06);
}
.rent-table-card:hover {
    box-shadow: 0 10px 28px -14px rgba(64,81,137,.22);
}
[data-layout-mode="dark"] .rent-table-card { background: #212529; border-color: #2a2f34; }

.rent-table-card .table { margin-bottom: 0; font-size: .8rem; }
.rent-table-card .table thead th {
    font-size: .66rem !important; font-weight: 700 !important;
    letter-spacing: .4px; text-transform: uppercase;
    padding: .7rem .65rem;
    border-bottom: 2px solid #eef0f4;
    background: #f8f9fc !important;
    color: #495057;
}
.rent-table-card .table tbody td {
    padding: .6rem .65rem;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
}
.rent-table-card .table tbody tr:last-child td { border-bottom: 0; }
.rent-table-card .table tbody tr { transition: background .15s; }
.rent-table-card .table tbody tr:hover { background: #f8f9fc; }
[data-layout-mode="dark"] .rent-table-card .table thead th {
    background: #1a1d20 !important; border-bottom-color: #2a2f34; color: #ced4da;
}
[data-layout-mode="dark"] .rent-table-card .table tbody td { border-bottom-color: #2a2f34; }
[data-layout-mode="dark"] .rent-table-card .table tbody tr:hover { background: #1a1d20; }

.rent-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700;
    padding: .35em .85em; border-radius: 999px;
    line-height: 1.2;
}
.rent-badge-success { background: #d1fae5; color: #0ab39c; }
.rent-badge-danger { background: #fee2e2; color: #f06548; }
.rent-badge-warning { background: #fef3c7; color: #d97706; }
.rent-badge-info { background: #dbeafe; color: #299cdb; }
.rent-badge-neutral { background: #f3f5f9; color: #495057; }
[data-layout-mode="dark"] .rent-badge-success { background: rgba(10,179,156,.18); }
[data-layout-mode="dark"] .rent-badge-danger { background: rgba(240,101,72,.18); }
[data-layout-mode="dark"] .rent-badge-warning { background: rgba(247,184,75,.18); }
[data-layout-mode="dark"] .rent-badge-info { background: rgba(41,156,219,.18); }
[data-layout-mode="dark"] .rent-badge-neutral { background: #1a1d20; color: #ced4da; }

.rent-breakeven-status {
    display: flex; align-items: center; gap: .65rem;
    padding: .7rem 1rem; border-radius: 12px;
    flex: 1;
    transition: all .3s ease;
}
.rent-breakeven-status.positive { background: #e8faf1; }
.rent-breakeven-status.negative { background: #fef0f0; }
.rent-breakeven-status.neutral { background: #f3f5f9; }
[data-layout-mode="dark"] .rent-breakeven-status.positive { background: rgba(10,179,156,.12); }
[data-layout-mode="dark"] .rent-breakeven-status.negative { background: rgba(240,101,72,.12); }
[data-layout-mode="dark"] .rent-breakeven-status.neutral { background: #1a1d20; }

.rent-btn-save {
    display: inline-flex; align-items: center; gap: 5px;
    padding: .4rem 1rem;
    font-size: .72rem; font-weight: 700;
    border: 0; border-radius: 8px;
    background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 100%);
    color: #fff;
    transition: all .2s ease;
    box-shadow: 0 4px 12px -6px rgba(10,179,156,.5);
    cursor: pointer;
}
.rent-btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px -8px rgba(10,179,156,.6);
    color: #fff;
}

.rent-alert-box {
    padding: .75rem 1rem;
    border-radius: 10px;
    font-size: .78rem;
    display: flex; align-items: flex-start; gap: .6rem;
    border: 1px solid;
    margin-bottom: .5rem;
}
.rent-alert-box i { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.rent-alert-info { background: #e8f4fd; color: #1a6fa0; border-color: #c3ddfa; }
.rent-alert-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.rent-alert-success { background: #e8faf1; color: #065f46; border-color: #a7f3d8; }
.rent-alert-danger { background: #fef0f0; color: #991b1b; border-color: #fecaca; }
[data-layout-mode="dark"] .rent-alert-info { background: rgba(41,156,219,.12); color: #7dd3fc; border-color: rgba(41,156,219,.3); }
[data-layout-mode="dark"] .rent-alert-warning { background: rgba(247,184,75,.12); color: #fde68a; border-color: rgba(247,184,75,.3); }
[data-layout-mode="dark"] .rent-alert-success { background: rgba(10,179,156,.12); color: #a7f3d8; border-color: rgba(10,179,156,.3); }
[data-layout-mode="dark"] .rent-alert-danger { background: rgba(240,101,72,.12); color: #fecaca; border-color: rgba(240,101,72,.3); }

.rent-flex-between {
    display: flex; align-items: center; justify-content: space-between;
}
.rent-metric-lg {
    font-size: 1.8rem; font-weight: 900; line-height: 1.2;
    color: #212529;
}
[data-layout-mode="dark"] .rent-metric-lg { color: #fff; }
.rent-metric-md {
    font-size: 1.15rem; font-weight: 800; line-height: 1.2;
}
.rent-metric-sm {
    font-size: .68rem; color: #878a99; font-weight: 600;
    text-transform: uppercase; letter-spacing: .3px;
}

.rent-sim-input {
    font-weight: 700; border-radius: 8px; border: 1px solid #e9ebec;
    max-width: 90px; padding: .35rem .55rem; font-size: .82rem;
}
.rent-sim-input:focus {
    border-color: #6559cc; box-shadow: 0 0 0 3px rgba(101,89,204,.12);
    outline: 0;
}

.costo-docente-estimado-box {
    background: #f8f9fc;
    border-radius: 8px; padding: .55rem .65rem;
    margin-top: .35rem;
    border: 1px dashed #e9ebec;
}
[data-layout-mode="dark"] .costo-docente-estimado-box {
    background: #16191c; border-color: #2a2f34;
}

/* ===== Tabs Contabilidad / Historial ===== */
.cht-card {
    background:#fff; border:1px solid #eef0f5; border-radius:12px;
    box-shadow:0 1px 2px rgba(30,40,80,.04); overflow:hidden;
}
.cht-card-header {
    padding:.75rem 1rem; border-bottom:1px solid #eef0f5;
    display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:wrap;
}
.cht-title { font-size:.8rem; font-weight:700; color:#2a2f43; display:flex; align-items:center; gap:.45rem; margin:0; }
.cht-title i { color:#405189; font-size:1rem; }
.cht-sub { font-size:.7rem; color:#98a0b5; font-weight:600; }
.cht-share { display:flex; align-items:center; gap:.45rem; min-width:90px; }
.cht-share .bar { flex:1; height:5px; border-radius:4px; background:#eef0f5; overflow:hidden; min-width:44px; }
.cht-share .bar > div { height:100%; border-radius:4px; }
.cht-share .txt { font-size:.68rem; font-weight:700; color:#98a0b5; width:34px; text-align:right; }
.cht-total-row td { background:#f6f8fc !important; border-top:2px solid #e8ebf3 !important; font-weight:800; }
.cht-pct-mini { display:flex; align-items:center; gap:.45rem; justify-content:center; }
.cht-pct-mini .bar { width:52px; height:5px; border-radius:4px; background:#eef0f5; overflow:hidden; }
.cht-pct-mini .bar > div { height:100%; border-radius:4px; }
.cht-pct-mini .txt { font-size:.7rem; font-weight:800; width:36px; text-align:left; }

/* ===== Modales Pagos / Inscritos del mes ===== */
.pm-modal .modal-content { border:0; border-radius:14px; overflow:hidden; }
.pm-modal .modal-header { background:linear-gradient(135deg,#405189 0%,#5e72b8 100%); border:0; padding:1rem 1.3rem; }
.pm-modal .modal-header .btn-close { filter:brightness(0) invert(1); opacity:.9; }
.pm-modal .pm-title { font-size:1rem; font-weight:700; color:#fff; margin:0 0 .1rem; }
.pm-modal .pm-sub { font-size:.75rem; color:rgba(255,255,255,.8); }
.pm-modal .modal-body { background:#fafbfd; padding:1rem 1.2rem; }

.pm-stats { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:.6rem; margin-bottom:.75rem; }
.pm-stat {
    background:#fff; border:1px solid #eef0f5; border-radius:10px;
    padding:.5rem .65rem; display:flex; align-items:center; gap:.55rem;
    box-shadow:0 1px 2px rgba(30,40,80,.04);
}
.pm-stat-ico {
    width:30px; height:30px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.95rem;
}
.pm-stat .num { font-size:.92rem; font-weight:800; line-height:1.15; color:#2a2f43; white-space:nowrap; }
.pm-stat .lbl { font-size:.58rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#878a99; }
@media (max-width:640px){ .pm-stats { grid-template-columns:repeat(2, minmax(0,1fr)); } }

.pm-wrap { background:#fff; border:1px solid #eef0f5; border-radius:12px; overflow:hidden; box-shadow:0 1px 2px rgba(30,40,80,.04); }
.pm-toolbar {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
    padding:.6rem .8rem; border-bottom:1px solid #eef0f5;
}
.pm-search { position:relative; flex:1 1 200px; max-width:280px; }
.pm-search i { position:absolute; left:.65rem; top:50%; transform:translateY(-50%); color:#aab0c0; font-size:.9rem; pointer-events:none; }
.pm-search input {
    width:100%; border:1px solid #e3e6ef; border-radius:8px;
    padding:.38rem .65rem .38rem 1.9rem; font-size:.78rem; outline:none;
    transition:border-color .15s, box-shadow .15s;
}
.pm-search input:focus { border-color:#8fa2d8; box-shadow:0 0 0 3px rgba(64,81,137,.08); }
.pm-countlbl { font-size:.7rem; color:#98a0b5; font-weight:600; white-space:nowrap; }
.pm-scroll { max-height:350px; overflow-y:auto; }
.pm-scroll::-webkit-scrollbar { width:8px; }
.pm-scroll::-webkit-scrollbar-thumb { background:#dfe3ec; border-radius:4px; }
.pm-table { width:100%; font-size:.78rem; margin:0; border-collapse:separate; border-spacing:0; }
.pm-table thead th {
    background:#f6f8fc; color:#5d6478; font-weight:700;
    font-size:.63rem; text-transform:uppercase; letter-spacing:.3px;
    padding:.5rem .7rem; position:sticky; top:0; z-index:1;
    border-bottom:1px solid #e8ebf3;
}
.pm-table tbody td { padding:.45rem .7rem; border-top:1px solid #f2f4f9; vertical-align:middle; }
.pm-table tbody tr:first-child td { border-top:0; }
.pm-table tbody tr:hover { background:#f8fafd; }
.pm-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.64rem; font-weight:700; color:#fff; letter-spacing:.3px;
}
.pm-part-cell { display:flex; align-items:center; gap:.6rem; min-width:0; }
.pm-part-name { font-weight:600; font-size:.79rem; color:#2a2f43; line-height:1.25; }
.pm-part-ci { font-size:.68rem; color:#98a0b5; }
.pm-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.14rem .6rem; border-radius:999px; font-size:.66rem; font-weight:700; white-space:nowrap;
}
/* ===== Nivelación ===== */
.nivelacion-stat {
    display:flex; align-items:center; gap:.8rem;
    padding:.9rem 1rem; border-radius:12px;
    background:#fff; border:1px solid #eef0f4; box-shadow:0 2px 8px -4px rgba(0,0,0,.06);
}
.nivelacion-stat-icon {
    width:38px; height:38px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1.05rem;
}
.nivelacion-stat-label { font-size:.68rem; font-weight:700; color:#878a99; text-transform:uppercase; letter-spacing:.3px; }
.nivelacion-stat-value { font-size:1rem; font-weight:700; color:#212529; }
.nivelacion-horario-dot {
    width:9px; height:9px; border-radius:50%; flex-shrink:0;
    background:#f7b84b; box-shadow:0 0 0 3px rgba(247,184,75,.18);
}
.nivelacion-accordion .accordion-item {
    overflow: hidden;
    margin-bottom: .75rem;
    border: 1px solid #e5e9f1;
    border-radius: 12px;
    box-shadow: 0 3px 12px -9px rgba(34,43,69,.35);
}
.nivelacion-accordion .accordion-button {
    gap: 1rem;
    padding: .9rem 1rem;
    background: #fff;
    color: #27304a;
    box-shadow: none;
}
.nivelacion-accordion .accordion-button:not(.collapsed) {
    background: #f5f7fc;
    color: #405189;
    box-shadow: inset 0 -1px 0 #e5e9f1;
}
.nivelacion-accordion .accordion-button:focus { box-shadow: 0 0 0 3px rgba(64,81,137,.16); }
.nivelacion-accordion .accordion-button::after { margin-left: .5rem; }
.nivelacion-accordion .nivelacion-accordion-title { min-width: 180px; font-size: .86rem; font-weight: 700; }
.nivelacion-accordion .nivelacion-accordion-meta {
    display: flex; flex: 1 1 auto; flex-wrap: wrap; align-items: center; gap: .4rem;
}
.nivelacion-accordion .accordion-body { padding: 1rem; background: #fff; }
.nivelacion-accordion .nivelacion-actions { display: flex; justify-content: flex-end; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
.nivelacion-no-corresponde {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: 1rem; border: 1px dashed #9bd8c9; border-radius: 10px;
    background: #f0fdf8; color: #167a65;
}
.nivelacion-no-corresponde i { font-size: 1.25rem; line-height: 1; }
.nivelacion-no-corresponde strong { display: block; font-size: .82rem; margin-bottom: .12rem; }
.nivelacion-no-corresponde span { font-size: .76rem; }
[data-layout-mode="dark"] .nivelacion-accordion .accordion-item { border-color: #30363d; }
[data-layout-mode="dark"] .nivelacion-accordion .accordion-button,
[data-layout-mode="dark"] .nivelacion-accordion .accordion-body { background: #1d2125; color: #e9ecef; }
[data-layout-mode="dark"] .nivelacion-accordion .accordion-button:not(.collapsed) { background: #252b31; color: #b8c7ff; box-shadow: inset 0 -1px 0 #30363d; }
[data-layout-mode="dark"] .nivelacion-no-corresponde { background: rgba(10,179,156,.1); border-color: rgba(10,179,156,.4); color: #63d6bf; }
@media (max-width: 767.98px) {
    .nivelacion-accordion .accordion-button { align-items: flex-start; }
    .nivelacion-accordion .nivelacion-accordion-title { min-width: 0; }
    .nivelacion-accordion .nivelacion-accordion-meta { margin-top: .35rem; }
    .nivelacion-accordion .nivelacion-actions .btn { flex: 1 1 auto; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="acad-page">
@php
    $mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $themeAccents = ['primary'=>'#405189','success'=>'#0ab39c','info'=>'#299cdb','warning'=>'#f7b84b','danger'=>'#f06548','secondary'=>'#878a99','dark'=>'#212529'];
    // Convierte "2026-03" o "2026-03-15" a "marzo del 2026"
    $mesEs = function ($val) use ($mesesEs) {
        if (!$val) return '—';
        if (preg_match('/^(\d{4})-(\d{2})/', (string) $val, $m)) {
            return ($mesesEs[(int)$m[2] - 1] ?? $m[2]) . ' del ' . $m[1];
        }
        return $val;
    };
    // Convierte "2026-03-15" a "15 de marzo del 2026"
    $fechaEs = function ($val) use ($mesesEs) {
        if (!$val) return '—';
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', (string) $val, $m)) {
            return (int)$m[3] . ' de ' . ($mesesEs[(int)$m[2] - 1] ?? $m[2]) . ' del ' . $m[1];
        }
        return $val;
    };

    // Permisos de las acciones del programa (según responsable / área).
    $authUser = auth()->user();
    $esSuperAdmin = $authUser->hasRole('Super Admin');
    $esAreaContable = $authUser->esAreaContable();
    $esResponsablePrograma = $authUser->esResponsableDelPrograma($programa->id);
    $puedeWhatsappPrograma = $authUser->puedeWhatsappPrograma($programa->id);
    $puedeGestionarBaja = $authUser->puedeGestionarBaja($programa->id);
    // Tabs Contabilidad / Historial: solo rol Contabilidad (+ Super Admin).
    $puedeVerContable = $esSuperAdmin || $authUser->hasRole('Contabilidad');
    // Cambiar estado de módulo: Super Admin, Responsable Académico, permiso específico
    // o el responsable asignado del programa (incluye usuarios con rol Academicos que
    // están vinculados como responsables del programa).
    $puedeCambiarEstado = $esSuperAdmin
        || $authUser->hasRole('Responsable Académico')
        || $authUser->can('programas.modulos.cambiar.estado')
        || $esResponsablePrograma;
    // Gestionar módulos (asistencia, notas, nivelación): Super Admin, personal con
    // permiso de módulos, Responsable Académico o responsable asignado del programa.
    $puedeGestionarModulo = $esSuperAdmin
        || $authUser->hasRole('Responsable Académico')
        || $authUser->can('programas.modulos.estado')
        || $authUser->can('programas.modulos.cambiar.estado')
        || $esResponsablePrograma;
    // Asignar docente a los módulos: Super Admin, Responsable Académico y, solo
    // para programas UPI (Diplomados = 6, Diplomado Autogestionado = 7),
    // Responsable Académico UPI.
    $esProgramaUpi = in_array((int) ($programa->tipo_id ?? 0), [6, 7], true);
    $puedeAsignarDocente = $esSuperAdmin
        || $authUser->hasRole('Responsable Académico')
        || ($authUser->hasRole('Responsable Académico UPI') && $esProgramaUpi);
    // Rebalancear cuotas: Super Admin / Administrador.
    $puedeRebalancear = $esSuperAdmin || $authUser->hasRole('Administrador');
@endphp
<div class="show-hero">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width:0;">
            <div class="hero-icon-lg flex-shrink-0">
                <i class="ri-graduation-cap-line"></i>
            </div>
            <div style="min-width:0;">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="hero-badge"><i class="ri-hashtag"></i>{{ $programa->codigo }}</span>
                    <span class="hero-badge"><i class="ri-folder-3-line"></i>{{ $programa->tipo_nombre }}</span>
                    <span class="hero-badge"><i class="ri-calendar-2-line"></i>Gestión {{ $programa->gestion }}</span>
                    @if($programa->sede_nombre)
                        <span class="hero-badge"><i class="ri-map-pin-line"></i>{{ $programa->sede_nombre }}</span>
                    @endif
                    @if($programa->estado_nombre)
                        @php
                            $estColor = [
                                'En desarrollo' => '#0ab39c',
                                'Concluido' => '#405189',
                                'No Ejecutado' => '#f06548',
                                'Inscripciones' => '#f7b84b',
                            ][$programa->estado_nombre] ?? '#878a99';
                        @endphp
                        <span class="hero-badge" style="background: {{ $estColor }}; border-color: {{ $estColor }};">
                            <i class="ri-flag-2-line"></i>{{ $programa->estado_nombre }}
                        </span>
                    @endif
                    @if($programa->ceub)
                        <span class="hero-badge" style="background:#0ab39c;border-color:#0ab39c;">
                            <i class="ri-gov-line"></i> CEUB
                        </span>
                    @endif
                    <span class="hero-badge hero-badge-moodle {{ $programa->moodle_link ? '' : 'hero-badge-moodle-empty' }}" id="heroProgramaMoodle" style="cursor:pointer;background:{{ $programa->moodle_link ? '#6a1b9a' : '#adb5bd' }};border-color:{{ $programa->moodle_link ? '#6a1b9a' : '#adb5bd' }};" title="{{ $programa->moodle_link ? 'Ir a Moodle' : 'Agregar enlace Moodle' }}" data-tiene-link="{{ $programa->moodle_link ? '1' : '0' }}" data-puede-editar="{{ $esSuperAdmin ? '1' : '0' }}" data-url="{{ $programa->moodle_link ?? '' }}">
                        <i class="ri-{{ $programa->moodle_link ? 'external-link-line' : 'add-line' }}"></i>
                        <span>Moodle</span>
                    </span>
                </div>
                <h3 class="fw-bold mb-0 fs-20">{{ $programa->nombre }}</h3>
            </div>
        </div>
        <div class="flex-shrink-0 d-flex gap-2 flex-wrap">
            @if((int)$programa->estado_id === 4)
                @php $inscHabilitadas = (int)($programa->inscripciones_habilitadas ?? 0) === 1; @endphp
                @can('inscripciones.cambiarestado')
                <button type="button" class="btn btn-toggle-insc d-flex align-items-center gap-1 {{ $inscHabilitadas ? 'on' : 'off' }}" id="btn-toggle-inscripciones" data-habilitadas="{{ $inscHabilitadas ? 1 : 0 }}">
                    <i class="ri-{{ $inscHabilitadas ? 'toggle-fill' : 'toggle-line' }}"></i>
                    <span id="lbl-toggle-insc">{{ $inscHabilitadas ? 'Inscripciones habilitadas' : 'Habilitar inscripciones' }}</span>
                </button>
                @endcan
                @can('inscripciones.registrar')
                <button type="button" class="btn btn-inscribir d-flex align-items-center gap-1 {{ $inscHabilitadas ? '' : 'disabled' }}" id="btn-inscribir-participante" {{ $inscHabilitadas ? '' : 'disabled' }} title="{{ $inscHabilitadas ? 'Inscribir nuevo participante' : 'Las inscripciones están deshabilitadas' }}">
                    <i class="ri-user-add-line"></i> Inscribir Participante
                </button>
                @endcan
            @endif
            <a href="{{ route('admin.programas.index') }}" class="btn btn-volver d-flex align-items-center gap-1">
                <i class="ri-arrow-left-line"></i> Volver
            </a>
        </div>
    </div>
    @if($responsables->count())
    <div class="resp-bar d-flex align-items-center flex-wrap gap-2">
        <span class="resp-label me-1"><i class="ri-team-line align-bottom me-1"></i>Responsable(s)</span>
        @foreach($responsables as $r)
            <span class="resp-pill" style="border-left: 3px solid {{ $r->color ?? '#6c757d' }};" title="{{ $r->nombres }} {{ $r->apellidos }} (CI: {{ $r->ci }})">
                <i class="ri-user-3-line text-muted"></i>
                {{ $r->nombres }} {{ $r->apellidos }}
            </span>
        @endforeach
    </div>
    @endif
</div>

@php
    $mainStats = [
        ['label' => 'Inscritos', 'value' => $resumen['total_inscritos'], 'icon' => 'ri-team-line', 'theme' => 'primary', 'accent' => '#405189'],
        ['label' => 'Activos', 'value' => $resumen['inscritos_activos'], 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success', 'accent' => '#0ab39c'],
        ['label' => 'Matrículas', 'value' => 'Bs ' . number_format($resumen['total_matricula'], 2), 'icon' => 'ri-bank-card-line', 'theme' => 'warning', 'accent' => '#f7b84b'],
        ['label' => 'Colegiaturas', 'value' => 'Bs ' . number_format($resumen['total_colegiatura'], 2), 'icon' => 'ri-funds-line', 'theme' => 'info', 'accent' => '#299cdb'],
        ['label' => 'Total Pagado', 'value' => 'Bs ' . number_format($resumen['total_pagado_matriculas'] + $resumen['total_pagado_colegiaturas'], 2), 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'success', 'accent' => '#0ab39c'],
        ['label' => 'Por Cobrar', 'value' => 'Bs ' . number_format(max(0, ($resumen['total_deuda_matriculas'] + $resumen['total_deuda_colegiaturas']) - ($resumen['total_pagado_matriculas'] + $resumen['total_pagado_colegiaturas'])), 2), 'icon' => 'ri-error-warning-line', 'theme' => 'danger', 'accent' => '#f06548'],
    ];
@endphp
<div class="row g-3 mb-4">
    @foreach($mainStats as $s)
    <div class="col-xl-4 col-md-4 col-sm-6">
        <div class="stat-card h-100" style="--stat-accent: {{ $s['accent'] }};">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">{{ $s['label'] }}</p>
                        <h4 class="stat-value text-truncate mb-0">{{ $s['value'] }}</h4>
                    </div>
                    <div class="stat-icon flex-shrink-0 bg-soft-{{ $s['theme'] }} text-{{ $s['theme'] }}">
                        <i class="{{ $s['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-12">
        <div class="card show-tabs-card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-show mb-3" role="tablist">
                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-total-registros" role="tab">
                            <i class="ri-file-list-3-line"></i> Total Registros Inscripciones
                            <span class="badge bg-primary">{{ count($inscripciones) }}</span>
                        </a>
                    </li>
                    @endcanany
                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-fase-formativa" role="tab">
                            <i class="ri-graduation-cap-line"></i> Inscritos Fase Formativa
                            <span class="badge bg-success">{{ $inscripciones->filter(fn($i) => !((int)$i->estado === 0 && !(int)($i->baja_operativa ?? 0) && !(int)($i->baja_academica ?? 0)))->count() }}</span>
                        </a>
                    </li>
                    @endcanany
                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-marketing-reales" role="tab">
                            <i class="ri-team-line"></i> Total Inscritos reales Marketing
                            <span class="badge bg-info" id="badge-marketing-reales">0</span>
                        </a>
                    </li>
                    @endcanany
                    @if($puedeVerContable)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-contabilidad" role="tab">
                            <i class="ri-bar-chart-2-line"></i> Contabilidad
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-historial" role="tab">
                            <i class="ri-history-line"></i> Historial
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-modulos" role="tab">
                            <i class="ri-bookmark-line"></i> Módulos
                            <span class="badge bg-primary">{{ $modulos->count() }}</span>
                        </a>
                    </li>
                    @can('programas.tab.desarrollo')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-desarrollo" role="tab">
                            <i class="ri-graduation-cap-line"></i> Desarrollo Académico
                        </a>
                    </li>
                    @endcan
                    @can('programas.tab.contable')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-contable" role="tab">
                            <i class="ri-money-dollar-circle-line"></i> Seguimiento Contable
                        </a>
                    </li>
                    @endcan
                    @if($esSuperAdmin)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-rentabilidad" role="tab">
                            <i class="ri-line-chart-line"></i> Rentabilidad
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-registros-universidad" role="tab">
                            <i class="ri-government-line"></i> Registros Universidad
                            <span class="badge bg-soft-info fs-10 ms-1">{{ count($folios) }}</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">
                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    {{-- TAB TOTAL REGISTROS INSCRIPCIONES --}}
                    <div class="tab-pane active show" id="tab-total-registros" role="tabpanel">
                        {{-- Sub-tabs: Inscritos | Matrículas | Colegiaturas --}}
                        <ul class="nav nav-tabs nav-tabs-show mt-3 mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#sub-tab-inscritos" role="tab">
                                    <i class="ri-team-line"></i> Inscritos
                                    <span class="badge bg-primary">{{ count($inscripciones) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sub-tab-matriculas" role="tab">
                                    <i class="ri-bank-card-line"></i> Matrículas
                                    <span class="badge bg-info">{{ $matriculas->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sub-tab-colegiaturas" role="tab">
                                    <i class="ri-funds-line"></i> Colegiaturas
                                    <span class="badge bg-info">{{ $colegiaturaStats['participantes_count'] }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- Sub-tab: Inscritos --}}
                            <div class="tab-pane active show" id="sub-tab-inscritos" role="tabpanel">
                                {{-- Stats cards -- Resumen de participantes --}}
                                <div class="row g-3 mb-4">
                                    @php
                                        $subResumen = [
                                            ['label' => 'Total Inscritos', 'value' => $resumen['total_inscritos'], 'icon' => 'ri-team-line', 'theme' => 'primary', 'accent' => '#405189'],
                                            ['label' => 'Operativos', 'value' => $resumen['operativos'], 'icon' => 'ri-check-double-line', 'theme' => 'success', 'accent' => '#0ab39c'],
                                            ['label' => 'Bajas Tempranas', 'value' => $resumen['bajas_tempranas'], 'icon' => 'ri-walk-line', 'theme' => 'danger', 'accent' => '#f06548'],
                                            ['label' => 'Bajas Operativas', 'value' => $resumen['bajas_operativas'], 'icon' => 'ri-close-circle-line', 'theme' => 'danger', 'accent' => '#f06548'],
                                            ['label' => 'Bajas Académicas', 'value' => $resumen['bajas_academicas'], 'icon' => 'ri-book-close-line', 'theme' => 'warning', 'accent' => '#f7b84b'],
                                            ['label' => 'Plan Contado', 'value' => $resumen['planes_contado'], 'icon' => 'ri-money-dollar-box-line', 'theme' => 'info', 'accent' => '#299cdb'],
                                            ['label' => 'Plan Regular', 'value' => $resumen['planes_regulares'], 'icon' => 'ri-calendar-todo-line', 'theme' => 'primary', 'accent' => '#405189'],
                                            ['label' => 'Registrados UNSXX', 'value' => $resumen['registrados_unsxx'], 'icon' => 'ri-government-line', 'theme' => 'secondary', 'accent' => '#878a99'],
                                            ['label' => 'Concluidos Modular', 'value' => $resumen['concluidos'], 'icon' => 'ri-award-line', 'theme' => 'success', 'accent' => '#0ab39c'],
                                        ];
                                    @endphp
                                    @foreach($subResumen as $item)
                                        <div class="col-xl-4 col-lg-4 col-md-6">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $item['accent'] }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Charts --}}
                                <style>
                                .chart-body-layout { display: flex; align-items: stretch; gap: 1rem; }
                                .chart-body-layout .chart-area { flex: 1; min-width: 0; display: flex; align-items: center; justify-content: center; }
                                .chart-body-layout .chart-area canvas { max-height: 200px; width: 100% !important; }
                                .chart-body-layout .chart-stats { flex-shrink: 0; display: flex; flex-direction: column; gap: .5rem; justify-content: center; min-width: 120px; }
                                .chart-stat-box { text-align: center; padding: .5rem .75rem; border-radius: 10px; }
                                .chart-stat-box .stat-num { font-size: 1.15rem; font-weight: 700; line-height: 1.2; }
                                .chart-stat-box .stat-label { font-size: .62rem; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; margin-top: 2px; }
                                @media (max-width: 575.98px) {
                                    .chart-body-layout { flex-direction: column; }
                                    .chart-body-layout .chart-stats { flex-direction: row; flex-wrap: wrap; justify-content: center; }
                                }
                                </style>
                                <div class="row g-3 mb-4">
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-pie-chart-2-line"></i>Estado General</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $resumen['operativos'] }}</div>
                                                            <div class="stat-label">Operativos</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $resumen['bajas_tempranas'] }}</div>
                                                            <div class="stat-label">B. Temp</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $resumen['bajas_operativas'] }}</div>
                                                            <div class="stat-label">B. Oper</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fff8f0;">
                                                            <div class="stat-num" style="color:#f7b84b;">{{ $resumen['bajas_academicas'] }}</div>
                                                            <div class="stat-label">B. Acad</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-wallet-3-line"></i>Plan de Pago</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-plan" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb;">{{ $resumen['planes_contado'] }}</div>
                                                            <div class="stat-label">Contado</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#f3efff;">
                                                            <div class="stat-num" style="color:#878a99;">{{ $resumen['planes_regulares'] }}</div>
                                                            <div class="stat-label">Regular</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-group-line"></i>Sexo</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-sexo" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb;">{{ $hombres }}</div>
                                                            <div class="stat-label">Hombres</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fce7f3;">
                                                            <div class="stat-num" style="color:#ec4899;">{{ $mujeres }}</div>
                                                            <div class="stat-label">Mujeres</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-map-2-line"></i>Departamento</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-depto" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        @php $deptColors = ['#163b6b','#1f6fbf','#0d8574','#cba135','#2a5d97','#b3243a','#4c7bb0','#b7791f','#7d9ec8','#ddb84d']; $di = 0; @endphp
                                                        @foreach($departamentos as $depto => $cant)
                                                        <div class="chart-stat-box" style="background:{{ $deptColors[$di % 10] }}22;">
                                                            <div class="stat-num" style="color:{{ $deptColors[$di++ % 10] }};">{{ $cant }}</div>
                                                            <div class="stat-label">{{ $depto }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Buscador en tiempo real --}}
                                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                                    <div class="position-relative flex-grow-1" style="max-width: 360px;">
                                        <i class="ri-search-line position-absolute" style="top:50%;left:12px;transform:translateY(-50%);color:#878a99;font-size:.9rem;"></i>
                                        <input type="text" class="form-control form-control-sm ps-4" id="buscador-inscritos" placeholder="Buscar por nombres, apellidos o CI..." style="font-size:.82rem;">
                                    </div>
                                    <span class="text-muted fs-11" id="resultados-inscritos">Mostrando <strong id="contador-inscritos">0</strong> resultados</span>
                                </div>

                                {{-- Table participantes --}}
                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 130px;">Asesor</th>
                                                <th style="width: 150px;">Contacto</th>
                                                <th style="width: 90px;" class="text-center">Sexo / Depto.</th>
                                                <th style="width: 130px;" class="text-end">Matrícula</th>
                                                <th style="width: 130px;" class="text-end">Colegiatura</th>
                                                <th style="width: 100px;" class="text-center">Plan / Fecha</th>
                                                <th style="width: 75px;" class="text-center">Estado</th>
                                                <th style="width: 55px;" class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inscripciones as $i)
                                            @php
                                                $pMat = $pagoMatriculaPorParticipante->get($i->participante_id);
                                                $pCol = $pagoColegiaturaPorParticipante->get($i->participante_id);
                                                $pagadoMat = $pMat ? $pMat->pagado : 0;
                                                $pagadoCol = $pCol ? $pCol->pagado : 0;

                                                $brutoMat = (float) $i->matricula;
                                                $descMat = (float) ($i->descuento_matricula ?? 0);
                                                $netoMat = max(0, $brutoMat - $descMat);
                                                $saldoMat = max(0, $netoMat - $pagadoMat);

                                                $brutoCol = (float) $i->monto_programa;
                                                $descCol = (float) ($i->descuento_colegiatura ?? 0);
                                                $netoCol = max(0, $brutoCol - $descCol);
                                                $saldoCol = max(0, $netoCol - $pagadoCol);

                                                $esBajaTemprana = (int)$i->estado === 0 && !(int)($i->baja_operativa ?? 0) && !(int)($i->baja_academica ?? 0);
                                                $esBajaOperativa = (int)($i->baja_operativa ?? 0);
                                                $esBajaAcademica = (int)($i->baja_academica ?? 0);
                                                $saldoTotal = $saldoMat + $saldoCol;
                                                $sinDeudaTotal = $saldoTotal <= 0 && ($netoMat > 0 || $netoCol > 0);
                                            @endphp
                                            <tr style="{{ $esBajaTemprana ? 'background: rgba(220,53,69,0.12) !important;' : ($esBajaOperativa ? 'background: rgba(255,193,7,0.15) !important;' : ($sinDeudaTotal ? 'background: rgba(10,179,156,0.08) !important;' : '')) }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante {{ $sinDeudaTotal ? 'bg-success-subtle' : '' }}">
                                                    <div class="nombre">{{ $i->apellidos }}, {{ $i->nombres }}@if(($i->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $i->ci }}</div>
                                                </td>
                                                <td class="text-center cell-asesor">
                                                    @if($i->asesor_nombres)
                                                        <span class="fw-semibold" style="font-size:.78rem;">{{ $i->asesor_apellidos }}, {{ $i->asesor_nombres }}</span>
                                                    @else
                                                        <span class="text-muted" style="font-size:.72rem;">—</span>
                                                    @endif
                                                </td>
                                                <td class="cell-contacto">
                                                    @if($i->celular)
                                                        <span class="line text-dark"><i class="ri-phone-line text-muted me-1"></i>{{ $i->celular }}</span>
                                                    @endif
                                                    @if($i->correo)
                                                        <span class="line text-muted text-truncate" style="max-width: 140px;" title="{{ $i->correo }}"><i class="ri-mail-line me-1"></i>{{ $i->correo }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php $sexoClass = strtoupper(substr($i->sexo ?? '', 0, 1)) === 'M' ? 'sexo-h' : 'sexo-m'; @endphp
                                                    <span class="pill-mini {{ $sexoClass }}">{{ $i->sexo }}</span>
                                                    <div class="text-muted mt-1" style="font-size:.68rem;"><i class="ri-map-pin-line"></i> {{ $i->departamento }}</div>
                                                </td>
                                                <td class="cell-money text-end {{ $sinDeudaTotal ? 'bg-success-subtle' : '' }}">
                                                    <div class="money-stack">
                                                        @if($descMat > 0)
                                                            <span class="text-muted" style="font-size:.62rem; text-decoration: line-through;">Bs {{ number_format($brutoMat, 2) }}</span>
                                                            <span class="text-muted" style="font-size:.62rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descMat, 2) }}</span>
                                                        @endif
                                                        <span class="deuda" title="Deuda neta (con descuento)">Bs {{ number_format($netoMat, 2) }}</span>
                                                        @if($pagadoMat > 0)
                                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($pagadoMat, 2) }}</span>
                                                        @endif
                                                        @if($saldoMat > 0)
                                                            <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($saldoMat, 2) }}</span>
                                                        @elseif($netoMat > 0)
                                                            <span class="pagado"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                        @else
                                                            <span class="pagado" style="font-weight:700;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="cell-money text-end {{ $sinDeudaTotal ? 'bg-success-subtle' : '' }}">
                                                    <div class="money-stack">
                                                        @if($descCol > 0)
                                                            <span class="text-muted" style="font-size:.62rem; text-decoration: line-through;">Bs {{ number_format($brutoCol, 2) }}</span>
                                                            <span class="text-muted" style="font-size:.62rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descCol, 2) }}</span>
                                                        @endif
                                                        <span class="deuda" title="Deuda neta (con descuento)">Bs {{ number_format($netoCol, 2) }}</span>
                                                        @if($pagadoCol > 0)
                                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($pagadoCol, 2) }}</span>
                                                        @endif
                                                        @if($saldoCol > 0)
                                                            <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($saldoCol, 2) }}</span>
                                                        @elseif($netoCol > 0)
                                                            <span class="pagado"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                        @else
                                                            <span class="pagado" style="font-weight:700;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($i->contado)
                                                        <span class="badge bg-soft-info text-info fs-10">Contado</span>
                                                    @else
                                                        <span class="badge bg-soft-primary text-primary fs-10">Regular</span>
                                                    @endif
                                                    @if($i->fecha_inscripcion)
                                                        <div class="text-muted mt-1" style="font-size:.65rem;"><i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($i->fecha_inscripcion)->format('d/m/Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($esBajaOperativa)
                                                        <span class="badge bg-soft-danger text-danger fs-10">Baja Operativa</span>
                                                    @elseif($esBajaAcademica)
                                                        <span class="badge bg-soft-warning text-warning fs-10">Baja Académica</span>
                                                    @elseif($esBajaTemprana)
                                                        <span class="badge bg-soft-danger text-danger fs-10">Baja Temprana</span>
                                                    @else
                                                        <span class="badge bg-soft-success text-success fs-10">Activo</span>
                                                    @endif
                                                    @if((int)($i->baja_contable ?? 0))
                                                        <span class="badge bg-soft-info text-info fs-10"><i class="ri-money-dollar-circle-line"></i> Baja Contable</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="hstack gap-1 justify-content-center">
                                                        @can('participantes.ver')
                                                        <a href="{{ route('admin.programas.participante-detalle', $i->participante_id) }}" class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle del participante">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        @endcan
                                                        {{-- Editar inscripción (asesor): solo Super Admin --}}
                                                        @if($esSuperAdmin)
                                                        <button class="btn btn-sm btn-icon btn-soft-warning btn-editar-inscripcion"
                                                            data-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-asesor-id="{{ $i->asesor_id }}"
                                                            title="Editar inscripción (asesor)">
                                                            <i class="ri-pencil-line"></i>
                                                        </button>
                                                        @endif

                                                        {{-- Gestionar baja: responsable del programa + área contable + Super Admin --}}
                                                        @if($puedeGestionarBaja)
                                                        <button type="button" class="btn btn-sm btn-icon btn-soft-secondary btn-toggle-baja"
                                                            data-inscripcion-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-baja-operativa="{{ $i->baja_operativa ?? 0 }}"
                                                            data-baja-academica="{{ $i->baja_academica ?? 0 }}"
                                                            data-baja-contable="{{ $i->baja_contable ?? 0 }}"
                                                            title="Gestionar baja">
                                                            <i class="ri-user-unfollow-line"></i>
                                                        </button>
                                                        @if($inscripcionesConBaja->contains($i->id))
                                                        <button type="button" class="btn btn-sm btn-icon btn-soft-info btn-baja-archivos"
                                                            data-inscripcion-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-tiene-archivo="{{ $archivosBaja->has($i->id) ? 1 : 0 }}"
                                                            data-archivo-nombre="{{ $archivosBaja->has($i->id) ? $archivosBaja[$i->id]->archivo_original : '' }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Documentos de la baja">
                                                            <i class="ri-file-pdf-line"></i>
                                                        </button>
                                                        @endif
                                                        @endif

                                                        {{-- Eliminar inscripción: solo Super Admin --}}
                                                        @if($esSuperAdmin)
                                                        <button class="btn btn-sm btn-icon btn-soft-danger btn-eliminar-inscripcion" data-id="{{ $i->id }}" data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}" data-ci="{{ $i->ci }}" title="Eliminar inscripción">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4 fs-14">No se encontraron inscritos</td>
                                            </tr>
                                            @endforelse
                                            @if($inscripciones->count() > 0)
                                            @php
                                                $iNetoMat = $inscripciones->sum(fn($i) => max(0, (float)$i->matricula - (float)($i->descuento_matricula ?? 0)));
                                                $iPagMat = $inscripciones->sum(fn($i) => ($pagoMatriculaPorParticipante->get($i->participante_id)?->pagado ?? 0));
                                                $iSalMat = max(0, $iNetoMat - $iPagMat);
                                                $iNetoCol = $inscripciones->sum(fn($i) => max(0, (float)$i->monto_programa - (float)($i->descuento_colegiatura ?? 0)));
                                                $iPagCol = $inscripciones->sum(fn($i) => ($pagoColegiaturaPorParticipante->get($i->participante_id)?->pagado ?? 0));
                                                $iSalCol = max(0, $iNetoCol - $iPagCol);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="cell-money text-end">
                                                    <div class="money-stack">
                                                        <span class="deuda">Bs {{ number_format($iNetoMat, 2) }}</span>
                                                        <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($iPagMat, 2) }}</span>
                                                        <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($iSalMat, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td class="cell-money text-end">
                                                    <div class="money-stack">
                                                        <span class="deuda">Bs {{ number_format($iNetoCol, 2) }}</span>
                                                        <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($iPagCol, 2) }}</span>
                                                        <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($iSalCol, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Sub-tab: Matrículas --}}
                            <div class="tab-pane" id="sub-tab-matriculas" role="tabpanel">
                                @php
                                    $matriculaCards = [
                                        ['label' => 'Total Matrícula (Bruto)', 'value' => 'Bs ' . number_format($matriculaStats['total_matricula_bruto'], 2), 'icon' => 'ri-bank-card-line', 'theme' => 'dark'],
                                        ['label' => 'Descuento Aplicado', 'value' => 'Bs ' . number_format($matriculaStats['descuento_inscripciones'], 2), 'icon' => 'ri-discount-percent-line', 'theme' => 'danger'],
                                        ['label' => 'Neto a Cobrar', 'value' => 'Bs ' . number_format($matriculaStats['neto_inscripciones'], 2), 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'primary'],
                                        ['label' => 'Deuda Actual en Matrícula', 'value' => 'Bs ' . number_format($matriculaStats['deuda_inscripciones'], 2), 'icon' => 'ri-error-warning-line', 'theme' => 'warning'],
                                        ['label' => 'Total Cobrado', 'value' => 'Bs ' . number_format($matriculaStats['cobrado_modulos'], 2), 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success'],
                                        ['label' => 'Participantes', 'value' => $matriculaStats['participantes_count'] . ' registros', 'icon' => 'ri-team-line', 'theme' => 'info'],
                                    ];
                                @endphp
                                <div class="row mb-4">
                                    @foreach($matriculaCards as $item)
                                        <div class="col-xxl col-xl-4 col-md-6 mb-3">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0" style="font-size: 1rem;">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-checkbox-circle-line"></i>Estado de Pagos · Matrículas</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-matricula-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $matriculaStats['pagados_count'] }}</div>
                                                            <div class="stat-label">Pagados</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $matriculaStats['pendientes_count'] }}</div>
                                                            <div class="stat-label">Pendientes</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-bank-card-line"></i>Resumen Financiero · Matrícula (Bs)</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-matricula-cobrado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb; font-size:1rem;">Bs {{ number_format($matriculaStats['deuda_inscripciones'], 2) }}</div>
                                                            <div class="stat-label">Deuda Actual</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c; font-size:1rem;">Bs {{ number_format($matriculaStats['cobrado_modulos'], 2) }}</div>
                                                            <div class="stat-label">Cobrado</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548; font-size:1rem;">Bs {{ number_format($matriculaStats['pendiente_modulos'], 2) }}</div>
                                                            <div class="stat-label">Pendiente</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 110px;" class="text-end">Deuda</th>
                                                <th style="width: 100px;" class="text-end">Dto.</th>
                                                <th style="width: 110px;" class="text-end">Pagado</th>
                                                <th style="width: 110px;" class="text-end">Pendiente</th>
                                                <th style="width: 55px;" class="text-center">#P</th>
                                                <th style="width: 95px;" class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matriculas as $m)
                                            @php $pendiente = max(0, $m->deuda_monto - $m->pagado); $esTraspasoM = $traspasoPorParticipante->get($m->participante_id, 0); @endphp
                                            <tr class="{{ $esTraspasoM ? 'traspaso-row' : '' }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante">
                                                    <div class="nombre">{{ $m->apellidos }}, {{ $m->nombres }}@if($esTraspasoM) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $m->ci }}</div>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">Bs {{ number_format($m->deuda_monto, 2) }}</td>
                                                <td class="text-end text-muted">{{ $m->descuento > 0 ? 'Bs '.number_format($m->descuento, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $m->pagado > 0 ? 'text-success' : 'text-muted' }}">{{ $m->pagado > 0 ? 'Bs '.number_format($m->pagado, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $pendiente > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendiente > 0 ? 'Bs '.number_format($pendiente, 2) : '—' }}</td>
                                                <td class="text-center"><span class="pill-mini" style="background:#eef0f4; color:#495057;">{{ $m->num_pagos }}</span></td>
                                                <td class="text-center">
                                                    @if($pendiente == 0 && $m->pagado > 0)
                                                        <span class="pill-mini" style="background:#d1fae5; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Pagado</span>
                                                    @elseif($m->pagado > 0 && $pendiente > 0)
                                                        <span class="pill-mini" style="background:#dbeafe; color:#299cdb;"><i class="ri-time-line"></i> Parcial</span>
                                                    @else
                                                        <span class="pill-mini" style="background:#fef3c7; color:#f7b84b;"><i class="ri-error-warning-line"></i> Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 fs-14">No se encontraron registros de matrícula</td>
                                            </tr>
                                            @endforelse
                                            @if($matriculas->count() > 0)
                                            @php
                                                $mSDeuda = $matriculas->sum('deuda_monto');
                                                $mSDto = $matriculas->sum('descuento');
                                                $mSPagado = $matriculas->sum('pagado');
                                                $mSPendiente = max(0, $mSDeuda - $mSPagado);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td class="text-end text-dark">Bs {{ number_format($mSDeuda, 2) }}</td>
                                                <td class="text-end text-muted">Bs {{ number_format($mSDto, 2) }}</td>
                                                <td class="text-end text-success">Bs {{ number_format($mSPagado, 2) }}</td>
                                                <td class="text-end text-danger">Bs {{ number_format($mSPendiente, 2) }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Sub-tab: Colegiaturas --}}
                            <div class="tab-pane" id="sub-tab-colegiaturas" role="tabpanel">
                                @php
                                    $colegiaturaCards = [
                                        ['label' => 'Total Programa (Bruto)', 'value' => 'Bs ' . number_format($colegiaturaStats['total_programa_bruto'], 2), 'icon' => 'ri-funds-line', 'theme' => 'dark'],
                                        ['label' => 'Descuento Aplicado', 'value' => 'Bs ' . number_format($colegiaturaStats['descuento_inscripciones'], 2), 'icon' => 'ri-discount-percent-line', 'theme' => 'danger'],
                                        ['label' => 'Neto a Cobrar', 'value' => 'Bs ' . number_format($colegiaturaStats['neto_inscripciones'], 2), 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'primary'],
                                        ['label' => 'Total Cobrado', 'value' => 'Bs ' . number_format($colegiaturaStats['cobrado_modulos'], 2), 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success'],
                                        ['label' => 'Pendiente por Cobrar', 'value' => 'Bs ' . number_format($colegiaturaStats['pendiente_modulos'], 2), 'icon' => 'ri-error-warning-line', 'theme' => 'warning'],
                                        ['label' => 'Participantes', 'value' => $colegiaturaStats['participantes_count'] . ' registros', 'icon' => 'ri-team-line', 'theme' => 'info'],
                                    ];
                                @endphp
                                <div class="row mb-4">
                                    @foreach($colegiaturaCards as $item)
                                        <div class="col-xxl col-xl-4 col-md-6 mb-3">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0" style="font-size: 1rem;">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-checkbox-circle-line"></i>Estado de Pagos · Colegiaturas</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-colegiatura-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $colegiaturaStats['pagados_count'] }}</div>
                                                            <div class="stat-label">Pagados</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $colegiaturaStats['pendientes_count'] }}</div>
                                                            <div class="stat-label">Pendientes</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-funds-line"></i>Resumen Financiero · Colegiatura (Bs)</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-colegiatura-cobrado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb; font-size:1rem;">Bs {{ number_format($colegiaturaStats['neto_inscripciones'], 2) }}</div>
                                                            <div class="stat-label">Neto a Cobrar</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c; font-size:1rem;">Bs {{ number_format($colegiaturaStats['cobrado_modulos'], 2) }}</div>
                                                            <div class="stat-label">Cobrado</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548; font-size:1rem;">Bs {{ number_format($colegiaturaStats['pendiente_modulos'], 2) }}</div>
                                                            <div class="stat-label">Pendiente</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 110px;" class="text-end">Deuda Total</th>
                                                <th style="width: 100px;" class="text-end">Dto.</th>
                                                <th style="width: 110px;" class="text-end">Pagado</th>
                                                <th style="width: 110px;" class="text-end">Pendiente</th>
                                                <th style="width: 55px;" class="text-center">#P</th>
                                                <th style="width: 95px;" class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($colegiaturaResumen as $c)
                                            @php $pendienteC = max(0, $c->total_deuda - $c->total_pagado); $esTraspasoC = $traspasoPorParticipante->get($c->participante_id, 0); @endphp
                                            <tr class="{{ $esTraspasoC ? 'traspaso-row' : '' }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante">
                                                    <div class="nombre">{{ $c->apellidos }}, {{ $c->nombres }}@if($esTraspasoC) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $c->ci }}</div>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">Bs {{ number_format($c->total_deuda, 2) }}</td>
                                                <td class="text-end text-muted">{{ $c->total_descuento > 0 ? 'Bs '.number_format($c->total_descuento, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $c->total_pagado > 0 ? 'text-success' : 'text-muted' }}">{{ $c->total_pagado > 0 ? 'Bs '.number_format($c->total_pagado, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $pendienteC > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendienteC > 0 ? 'Bs '.number_format($pendienteC, 2) : '—' }}</td>
                                                <td class="text-center"><span class="pill-mini" style="background:#eef0f4; color:#495057;">{{ $c->num_pagos }}</span></td>
                                                <td class="text-center">
                                                    @if($pendienteC == 0 && $c->total_pagado > 0)
                                                        <span class="pill-mini" style="background:#d1fae5; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Pagado</span>
                                                    @elseif($c->total_pagado > 0 && $pendienteC > 0)
                                                        <span class="pill-mini" style="background:#dbeafe; color:#299cdb;"><i class="ri-time-line"></i> Parcial</span>
                                                    @else
                                                        <span class="pill-mini" style="background:#fef3c7; color:#f7b84b;"><i class="ri-error-warning-line"></i> Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 fs-14">No se encontraron registros de colegiatura</td>
                                            </tr>
                                            @endforelse
                                            @if($colegiaturaResumen->count() > 0)
                                            @php
                                                $cSDeuda = $colegiaturaResumen->sum('total_deuda');
                                                $cSDto = $colegiaturaResumen->sum('total_descuento');
                                                $cSPagado = $colegiaturaResumen->sum('total_pagado');
                                                $cSPendiente = max(0, $cSDeuda - $cSPagado);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td class="text-end text-dark">Bs {{ number_format($cSDeuda, 2) }}</td>
                                                <td class="text-end text-muted">Bs {{ number_format($cSDto, 2) }}</td>
                                                <td class="text-end text-success">Bs {{ number_format($cSPagado, 2) }}</td>
                                                <td class="text-end text-danger">Bs {{ number_format($cSPendiente, 2) }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endcanany

                    @php
                        // Datos de la fase formativa: se calculan siempre porque el JS
                        // de gráficos los usa aunque el rol no tenga acceso a este tab.
                        $faseInscripciones = $inscripciones->filter(fn($i) => !((int)$i->estado === 0 && !(int)($i->baja_operativa ?? 0) && !(int)($i->baja_academica ?? 0)));
                        $faseOperativos = $resumen['operativos'];
                        $faseTotal = $faseInscripciones->count();
                        $faseHombres = $faseInscripciones->where('sexo', 'Hombre')->count();
                        $faseMujeres = $faseInscripciones->where('sexo', 'Mujer')->count();
                        $faseContado = $faseInscripciones->where('contado', 1)->count();
                        $faseRegular = $faseInscripciones->where('contado', 0)->count();
                        $faseUnsxx = $faseInscripciones->filter(fn($i) => $i->unsxx ?? false)->count();
                        $faseMatriculas = $matriculas->filter(fn($m) => $faseInscripciones->pluck('participante_id')->contains($m->participante_id));
                        $faseMatStats = [
                            'total' => $faseMatriculas->sum('deuda_monto'),
                            'descuento' => $faseMatriculas->sum('descuento'),
                            'pagado' => $faseMatriculas->sum('pagado'),
                            'neto' => $faseMatriculas->sum('deuda_monto') - $faseMatriculas->sum('descuento'),
                            'pendiente' => $faseMatriculas->sum(fn($m) => max(0, $m->deuda_monto - $m->pagado)),
                            'pagados_count' => $faseMatriculas->filter(fn($m) => $m->pagado >= $m->deuda_monto && $m->pagado > 0)->count(),
                            'pendientes_count' => $faseMatriculas->filter(fn($m) => $m->pagado < $m->deuda_monto)->count(),
                        ];
                        $faseColegiatura = $colegiaturaResumen->filter(fn($c) => $faseInscripciones->pluck('participante_id')->contains($c->participante_id));
                        $faseColStats = [
                            'total' => $faseColegiatura->sum('total_deuda'),
                            'descuento' => $faseColegiatura->sum('total_descuento'),
                            'pagado' => $faseColegiatura->sum('total_pagado'),
                            'neto' => $faseColegiatura->sum('total_deuda') - $faseColegiatura->sum('total_descuento'),
                            'pendiente' => $faseColegiatura->sum(fn($c) => max(0, $c->total_deuda - $c->total_pagado)),
                            'pagados_count' => $faseColegiatura->filter(fn($c) => $c->total_pagado >= $c->total_deuda && $c->total_pagado > 0)->count(),
                            'pendientes_count' => $faseColegiatura->filter(fn($c) => $c->total_pagado < $c->total_deuda)->count(),
                        ];
                    @endphp

                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    {{-- TAB INSCRITOS FASE FORMATIVA --}}
                    <div class="tab-pane" id="tab-fase-formativa" role="tabpanel">
                        @php
                            $faseInscripciones = $inscripciones->filter(fn($i) => !((int)$i->estado === 0 && !(int)($i->baja_operativa ?? 0) && !(int)($i->baja_academica ?? 0)));
                            $faseOperativos = $resumen['operativos'];
                            $faseTotal = $faseInscripciones->count();
                            $faseHombres = $faseInscripciones->where('sexo', 'Hombre')->count();
                            $faseMujeres = $faseInscripciones->where('sexo', 'Mujer')->count();
                            $faseContado = $faseInscripciones->where('contado', 1)->count();
                            $faseRegular = $faseInscripciones->where('contado', 0)->count();
                            $faseUnsxx = $faseInscripciones->filter(fn($i) => $i->unsxx ?? false)->count();
                        @endphp

                        {{-- Sub-tabs: Inscritos | Matrículas | Colegiaturas --}}
                        <ul class="nav nav-tabs nav-tabs-show mt-3 mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#sub-fase-inscritos" role="tab">
                                    <i class="ri-team-line"></i> Inscritos
                                    <span class="badge bg-primary">{{ $faseInscripciones->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sub-fase-matriculas" role="tab">
                                    <i class="ri-bank-card-line"></i> Matrículas
                                    <span class="badge bg-info">{{ $matriculas->filter(fn($m) => $faseInscripciones->pluck('participante_id')->contains($m->participante_id))->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sub-fase-colegiaturas" role="tab">
                                    <i class="ri-funds-line"></i> Colegiaturas
                                    <span class="badge bg-info">{{ $colegiaturaResumen->filter(fn($c) => $faseInscripciones->pluck('participante_id')->contains($c->participante_id))->count() }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- Sub-tab: Inscritos --}}
                            <div class="tab-pane active show" id="sub-fase-inscritos" role="tabpanel">
                                {{-- Stats cards --}}
                                <div class="row g-3 mb-4">
                                    @php
                                        $faseCards = [
                                            ['label' => 'Total Inscritos FF', 'value' => $faseTotal, 'icon' => 'ri-team-line', 'theme' => 'primary', 'accent' => '#405189'],
                                            ['label' => 'Operativos', 'value' => $faseOperativos, 'icon' => 'ri-check-double-line', 'theme' => 'success', 'accent' => '#0ab39c'],
                                            ['label' => 'Bajas Académicas', 'value' => $resumen['bajas_academicas'], 'icon' => 'ri-book-close-line', 'theme' => 'warning', 'accent' => '#f7b84b'],
                                            ['label' => 'Plan Contado', 'value' => $faseContado, 'icon' => 'ri-money-dollar-box-line', 'theme' => 'info', 'accent' => '#299cdb'],
                                            ['label' => 'Plan Regular', 'value' => $faseRegular, 'icon' => 'ri-calendar-todo-line', 'theme' => 'primary', 'accent' => '#405189'],
                                            ['label' => 'Registrados UNSXX', 'value' => $faseUnsxx, 'icon' => 'ri-government-line', 'theme' => 'secondary', 'accent' => '#878a99'],
                                            ['label' => 'Concluidos Modular', 'value' => $resumen['concluidos'], 'icon' => 'ri-award-line', 'theme' => 'success', 'accent' => '#0ab39c'],
                                        ];
                                    @endphp
                                    @foreach($faseCards as $item)
                                        <div class="col-xl-4 col-lg-4 col-md-6">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $item['accent'] }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Charts --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-pie-chart-2-line"></i>Estado General</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $faseOperativos }}</div>
                                                            <div class="stat-label">Operativos</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $resumen['bajas_operativas'] }}</div>
                                                            <div class="stat-label">B. Oper</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fff8f0;">
                                                            <div class="stat-num" style="color:#f7b84b;">{{ $resumen['bajas_academicas'] }}</div>
                                                            <div class="stat-label">B. Acad</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-wallet-3-line"></i>Plan de Pago</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-plan" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb;">{{ $faseContado }}</div>
                                                            <div class="stat-label">Contado</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#f3efff;">
                                                            <div class="stat-num" style="color:#878a99;">{{ $faseRegular }}</div>
                                                            <div class="stat-label">Regular</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-group-line"></i>Sexo</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-sexo" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb;">{{ $faseHombres }}</div>
                                                            <div class="stat-label">Hombres</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fce7f3;">
                                                            <div class="stat-num" style="color:#ec4899;">{{ $faseMujeres }}</div>
                                                            <div class="stat-label">Mujeres</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-map-2-line"></i>Departamento</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-depto" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        @php $deptColors = ['#163b6b','#1f6fbf','#0d8574','#cba135','#2a5d97','#b3243a','#4c7bb0','#b7791f','#7d9ec8','#ddb84d']; $di = 0; @endphp
                                                        @foreach($faseInscripciones->groupBy('departamento') as $depto => $items)
                                                        <div class="chart-stat-box" style="background:{{ $deptColors[$di % 10] }}22;">
                                                            <div class="stat-num" style="color:{{ $deptColors[$di++ % 10] }};">{{ $items->count() }}</div>
                                                            <div class="stat-label">{{ $depto }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Buscador --}}
                                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                                    <div class="position-relative flex-grow-1" style="max-width: 360px;">
                                        <i class="ri-search-line position-absolute" style="top:50%;left:12px;transform:translateY(-50%);color:#878a99;font-size:.9rem;"></i>
                                        <input type="text" class="form-control form-control-sm ps-4" id="buscador-fase-inscritos" placeholder="Buscar por nombres, apellidos o CI..." style="font-size:.82rem;">
                                    </div>
                                    <span class="text-muted fs-11" id="resultados-fase-inscritos">Mostrando <strong id="contador-fase-inscritos">0</strong> resultados</span>
                                </div>

                                {{-- Table --}}
                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-fase-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 130px;">Asesor</th>
                                                <th style="width: 150px;">Contacto</th>
                                                <th style="width: 90px;" class="text-center">Sexo / Depto.</th>
                                                <th style="width: 130px;" class="text-end">Matrícula</th>
                                                <th style="width: 130px;" class="text-end">Colegiatura</th>
                                                <th style="width: 100px;" class="text-center">Plan / Fecha</th>
                                                <th style="width: 75px;" class="text-center">Estado</th>
                                                <th style="width: 55px;" class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($faseInscripciones as $i)
                                            @php
                                                $pMat = $pagoMatriculaPorParticipante->get($i->participante_id);
                                                $pCol = $pagoColegiaturaPorParticipante->get($i->participante_id);
                                                $pagadoMat = $pMat ? $pMat->pagado : 0;
                                                $pagadoCol = $pCol ? $pCol->pagado : 0;
                                                $brutoMat = (float) $i->matricula;
                                                $descMat = (float) ($i->descuento_matricula ?? 0);
                                                $netoMat = max(0, $brutoMat - $descMat);
                                                $saldoMat = max(0, $netoMat - $pagadoMat);
                                                $brutoCol = (float) $i->monto_programa;
                                                $descCol = (float) ($i->descuento_colegiatura ?? 0);
                                                $netoCol = max(0, $brutoCol - $descCol);
                                                $saldoCol = max(0, $netoCol - $pagadoCol);
                                                $esBajaOperativa = (int)($i->baja_operativa ?? 0);
                                                $esBajaAcademica = (int)($i->baja_academica ?? 0);
                                                $sinDeudaFF = ($saldoMat + $saldoCol) <= 0 && ($netoMat > 0 || $netoCol > 0);
                                            @endphp
                                            <tr style="{{ $esBajaOperativa ? 'background: rgba(255,193,7,0.15) !important;' : ($sinDeudaFF ? 'background: rgba(10,179,156,0.08) !important;' : '') }}" class="{{ ($i->es_traspaso ?? 0) == 1 ? 'traspaso-row' : '' }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante {{ $sinDeudaFF ? 'bg-success-subtle' : '' }}">
                                                    <div class="nombre">{{ $i->apellidos }}, {{ $i->nombres }}@if(($i->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $i->ci }}</div>
                                                </td>
                                                <td class="text-center cell-asesor">
                                                    @if($i->asesor_nombres)
                                                        <span class="fw-semibold" style="font-size:.78rem;">{{ $i->asesor_apellidos }}, {{ $i->asesor_nombres }}</span>
                                                    @else
                                                        <span class="text-muted" style="font-size:.72rem;">—</span>
                                                    @endif
                                                </td>
                                                <td class="cell-contacto">
                                                    @if($i->celular)
                                                        <span class="line text-dark"><i class="ri-phone-line text-muted me-1"></i>{{ $i->celular }}</span>
                                                    @endif
                                                    @if($i->correo)
                                                        <span class="line text-muted text-truncate" style="max-width: 140px;" title="{{ $i->correo }}"><i class="ri-mail-line me-1"></i>{{ $i->correo }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @php $sexoClass = strtoupper(substr($i->sexo ?? '', 0, 1)) === 'M' ? 'sexo-h' : 'sexo-m'; @endphp
                                                    <span class="pill-mini {{ $sexoClass }}">{{ $i->sexo }}</span>
                                                    <div class="text-muted mt-1" style="font-size:.68rem;"><i class="ri-map-pin-line"></i> {{ $i->departamento }}</div>
                                                </td>
                                                <td class="cell-money text-end {{ $sinDeudaFF ? 'bg-success-subtle' : '' }}">
                                                    <div class="money-stack">
                                                        @if($descMat > 0)
                                                            <span class="text-muted" style="font-size:.62rem; text-decoration: line-through;">Bs {{ number_format($brutoMat, 2) }}</span>
                                                            <span class="text-muted" style="font-size:.62rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descMat, 2) }}</span>
                                                        @endif
                                                        <span class="deuda" title="Deuda neta (con descuento)">Bs {{ number_format($netoMat, 2) }}</span>
                                                        @if($pagadoMat > 0)
                                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($pagadoMat, 2) }}</span>
                                                        @endif
                                                        @if($saldoMat > 0)
                                                            <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($saldoMat, 2) }}</span>
                                                        @elseif($netoMat > 0)
                                                            <span class="pagado"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                        @else
                                                            <span class="pagado" style="font-weight:700;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="cell-money text-end {{ $sinDeudaFF ? 'bg-success-subtle' : '' }}">
                                                    <div class="money-stack">
                                                        @if($descCol > 0)
                                                            <span class="text-muted" style="font-size:.62rem; text-decoration: line-through;">Bs {{ number_format($brutoCol, 2) }}</span>
                                                            <span class="text-muted" style="font-size:.62rem;"><i class="ri-discount-percent-line"></i> Dto Bs {{ number_format($descCol, 2) }}</span>
                                                        @endif
                                                        <span class="deuda" title="Deuda neta (con descuento)">Bs {{ number_format($netoCol, 2) }}</span>
                                                        @if($pagadoCol > 0)
                                                            <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($pagadoCol, 2) }}</span>
                                                        @endif
                                                        @if($saldoCol > 0)
                                                            <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($saldoCol, 2) }}</span>
                                                        @elseif($netoCol > 0)
                                                            <span class="pagado"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                        @else
                                                            <span class="pagado" style="font-weight:700;"><i class="ri-checkbox-circle-line"></i> Sin deuda</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($i->contado)
                                                        <span class="badge bg-soft-info text-info fs-10">Contado</span>
                                                    @else
                                                        <span class="badge bg-soft-primary text-primary fs-10">Regular</span>
                                                    @endif
                                                    @if($i->fecha_inscripcion)
                                                        <div class="text-muted mt-1" style="font-size:.65rem;"><i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($i->fecha_inscripcion)->format('d/m/Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($esBajaOperativa)
                                                        <span class="badge bg-soft-danger text-danger fs-10">Baja Operativa</span>
                                                    @elseif($esBajaAcademica)
                                                        <span class="badge bg-soft-warning text-warning fs-10">Baja Académica</span>
@else
                                                        <span class="badge bg-soft-success text-success fs-10">Activo</span>
                                                    @endif
                                                    @if((int)($i->baja_contable ?? 0))
                                                        <span class="badge bg-soft-info text-info fs-10"><i class="ri-money-dollar-circle-line"></i> Baja Contable</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="hstack gap-1 justify-content-center">
                                                        @can('participantes.ver')
                                                        <a href="{{ route('admin.programas.participante-detalle', $i->participante_id) }}" class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle del participante">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        @endcan
                                                        {{-- Editar inscripción (asesor): solo Super Admin --}}
                                                        @if($esSuperAdmin)
                                                        <button class="btn btn-sm btn-icon btn-soft-warning btn-editar-inscripcion"
                                                            data-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-asesor-id="{{ $i->asesor_id }}"
                                                            title="Editar inscripción (asesor)">
                                                            <i class="ri-pencil-line"></i>
                                                        </button>
                                                        @endif

                                                        {{-- Gestionar baja: responsable del programa + área contable + Super Admin --}}
                                                        @if($puedeGestionarBaja)
                                                        <button type="button" class="btn btn-sm btn-icon btn-soft-secondary btn-toggle-baja"
                                                            data-inscripcion-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-baja-operativa="{{ $i->baja_operativa ?? 0 }}"
                                                            data-baja-academica="{{ $i->baja_academica ?? 0 }}"
                                                            data-baja-contable="{{ $i->baja_contable ?? 0 }}"
                                                            title="Gestionar baja">
                                                            <i class="ri-user-unfollow-line"></i>
                                                        </button>
                                                        @endif

                                                        {{-- Documentos de la baja: solo área contable (+ Super Admin) --}}
                                                        @if($esAreaContable)
                                                        @if($inscripcionesConBaja->contains($i->id))
                                                        <button type="button" class="btn btn-sm btn-icon btn-soft-info btn-baja-archivos"
                                                            data-inscripcion-id="{{ $i->id }}"
                                                            data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}"
                                                            data-ci="{{ $i->ci }}"
                                                            data-tiene-archivo="{{ $archivosBaja->has($i->id) ? 1 : 0 }}"
                                                            data-archivo-nombre="{{ $archivosBaja->has($i->id) ? $archivosBaja[$i->id]->archivo_original : '' }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Documentos de la baja">
                                                            <i class="ri-file-pdf-line"></i>
                                                        </button>
                                                        @endif
                                                        @endif

                                                        {{-- Eliminar inscripción: solo Super Admin --}}
                                                        @if($esSuperAdmin)
                                                        <button class="btn btn-sm btn-icon btn-soft-danger btn-eliminar-inscripcion" data-id="{{ $i->id }}" data-nombre="{{ $i->apellidos }}, {{ $i->nombres }}" data-ci="{{ $i->ci }}" title="Eliminar inscripción">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4 fs-14">No se encontraron inscritos en fase formativa</td>
                                            </tr>
                                            @endforelse
                                            @if($faseInscripciones->count() > 0)
                                            @php
                                                $fiNetoMat = $faseInscripciones->sum(fn($i) => max(0, (float)$i->matricula - (float)($i->descuento_matricula ?? 0)));
                                                $fiPagMat = $faseInscripciones->sum(fn($i) => ($pagoMatriculaPorParticipante->get($i->participante_id)?->pagado ?? 0));
                                                $fiSalMat = max(0, $fiNetoMat - $fiPagMat);
                                                $fiNetoCol = $faseInscripciones->sum(fn($i) => max(0, (float)$i->monto_programa - (float)($i->descuento_colegiatura ?? 0)));
                                                $fiPagCol = $faseInscripciones->sum(fn($i) => ($pagoColegiaturaPorParticipante->get($i->participante_id)?->pagado ?? 0));
                                                $fiSalCol = max(0, $fiNetoCol - $fiPagCol);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="cell-money text-end">
                                                    <div class="money-stack">
                                                        <span class="deuda">Bs {{ number_format($fiNetoMat, 2) }}</span>
                                                        <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($fiPagMat, 2) }}</span>
                                                        <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($fiSalMat, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td class="cell-money text-end">
                                                    <div class="money-stack">
                                                        <span class="deuda">Bs {{ number_format($fiNetoCol, 2) }}</span>
                                                        <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($fiPagCol, 2) }}</span>
                                                        <span class="pendiente"><i class="ri-time-line"></i> Saldo {{ number_format($fiSalCol, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Sub-tab: Matrículas --}}
                            <div class="tab-pane" id="sub-fase-matriculas" role="tabpanel">
                                @php
                                    $faseMatriculas = $matriculas->filter(fn($m) => $faseInscripciones->pluck('participante_id')->contains($m->participante_id));
                                    $faseMatStats = [
                                        'total' => $faseMatriculas->sum('deuda_monto'),
                                        'descuento' => $faseMatriculas->sum('descuento'),
                                        'pagado' => $faseMatriculas->sum('pagado'),
                                        'neto' => $faseMatriculas->sum('deuda_monto') - $faseMatriculas->sum('descuento'),
                                        'pendiente' => $faseMatriculas->sum(fn($m) => max(0, $m->deuda_monto - $m->pagado)),
                                        'pagados_count' => $faseMatriculas->filter(fn($m) => $m->pagado >= $m->deuda_monto && $m->pagado > 0)->count(),
                                        'pendientes_count' => $faseMatriculas->filter(fn($m) => $m->pagado < $m->deuda_monto)->count(),
                                    ];
                                @endphp
                                <div class="row mb-4">
                                    @php
                                        $faseMatCards = [
                                            ['label' => 'Total Matrícula (Bruto)', 'value' => 'Bs ' . number_format($faseMatStats['total'], 2), 'icon' => 'ri-bank-card-line', 'theme' => 'dark'],
                                            ['label' => 'Descuento Aplicado', 'value' => 'Bs ' . number_format($faseMatStats['descuento'], 2), 'icon' => 'ri-discount-percent-line', 'theme' => 'danger'],
                                            ['label' => 'Neto a Cobrar', 'value' => 'Bs ' . number_format($faseMatStats['neto'], 2), 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'primary'],
                                            ['label' => 'Total Cobrado', 'value' => 'Bs ' . number_format($faseMatStats['pagado'], 2), 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success'],
                                            ['label' => 'Pendiente', 'value' => 'Bs ' . number_format($faseMatStats['pendiente'], 2), 'icon' => 'ri-error-warning-line', 'theme' => 'warning'],
                                            ['label' => 'Participantes', 'value' => $faseMatriculas->count() . ' registros', 'icon' => 'ri-team-line', 'theme' => 'info'],
                                        ];
                                    @endphp
                                    @foreach($faseMatCards as $item)
                                        <div class="col-xxl col-xl-4 col-md-6 mb-3">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0" style="font-size: 1rem;">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-checkbox-circle-line"></i>Estado de Pagos · Matrículas</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-matricula-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $faseMatStats['pagados_count'] }}</div>
                                                            <div class="stat-label">Pagados</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $faseMatStats['pendientes_count'] }}</div>
                                                            <div class="stat-label">Pendientes</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-bank-card-line"></i>Resumen Financiero · Matrícula (Bs)</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-matricula-cobrado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb; font-size:1rem;">Bs {{ number_format($faseMatStats['pendiente'], 2) }}</div>
                                                            <div class="stat-label">Deuda Actual</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c; font-size:1rem;">Bs {{ number_format($faseMatStats['pagado'], 2) }}</div>
                                                            <div class="stat-label">Cobrado</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-fase-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 110px;" class="text-end">Deuda</th>
                                                <th style="width: 100px;" class="text-end">Dto.</th>
                                                <th style="width: 110px;" class="text-end">Pagado</th>
                                                <th style="width: 110px;" class="text-end">Pendiente</th>
                                                <th style="width: 55px;" class="text-center">#P</th>
                                                <th style="width: 95px;" class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($faseMatriculas as $m)
                                            @php $pendiente = max(0, $m->deuda_monto - $m->pagado); $esTraspasoFM = $traspasoPorParticipante->get($m->participante_id, 0); @endphp
                                            <tr class="{{ $esTraspasoFM ? 'traspaso-row' : '' }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante">
                                                    <div class="nombre">{{ $m->apellidos }}, {{ $m->nombres }}@if($esTraspasoFM) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $m->ci }}</div>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">Bs {{ number_format($m->deuda_monto, 2) }}</td>
                                                <td class="text-end text-muted">{{ $m->descuento > 0 ? 'Bs '.number_format($m->descuento, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $m->pagado > 0 ? 'text-success' : 'text-muted' }}">{{ $m->pagado > 0 ? 'Bs '.number_format($m->pagado, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $pendiente > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendiente > 0 ? 'Bs '.number_format($pendiente, 2) : '—' }}</td>
                                                <td class="text-center"><span class="pill-mini" style="background:#eef0f4; color:#495057;">{{ $m->num_pagos }}</span></td>
                                                <td class="text-center">
                                                    @if($pendiente == 0 && $m->pagado > 0)
                                                        <span class="pill-mini" style="background:#d1fae5; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Pagado</span>
                                                    @elseif($m->pagado > 0 && $pendiente > 0)
                                                        <span class="pill-mini" style="background:#dbeafe; color:#299cdb;"><i class="ri-time-line"></i> Parcial</span>
                                                    @else
                                                        <span class="pill-mini" style="background:#fef3c7; color:#f7b84b;"><i class="ri-error-warning-line"></i> Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 fs-14">No se encontraron registros de matrícula</td>
                                            </tr>
                                            @endforelse
                                            @if($faseMatriculas->count() > 0)
                                            @php
                                                $fmSDeuda = $faseMatriculas->sum('deuda_monto');
                                                $fmSDto = $faseMatriculas->sum('descuento');
                                                $fmSPagado = $faseMatriculas->sum('pagado');
                                                $fmSPendiente = max(0, $fmSDeuda - $fmSPagado);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td class="text-end text-dark">Bs {{ number_format($fmSDeuda, 2) }}</td>
                                                <td class="text-end text-muted">Bs {{ number_format($fmSDto, 2) }}</td>
                                                <td class="text-end text-success">Bs {{ number_format($fmSPagado, 2) }}</td>
                                                <td class="text-end text-danger">Bs {{ number_format($fmSPendiente, 2) }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Sub-tab: Colegiaturas --}}
                            <div class="tab-pane" id="sub-fase-colegiaturas" role="tabpanel">
                                @php
                                    $faseColegiatura = $colegiaturaResumen->filter(fn($c) => $faseInscripciones->pluck('participante_id')->contains($c->participante_id));
                                    $faseColStats = [
                                        'total' => $faseColegiatura->sum('total_deuda'),
                                        'descuento' => $faseColegiatura->sum('total_descuento'),
                                        'pagado' => $faseColegiatura->sum('total_pagado'),
                                        'neto' => $faseColegiatura->sum('total_deuda') - $faseColegiatura->sum('total_descuento'),
                                        'pendiente' => $faseColegiatura->sum(fn($c) => max(0, $c->total_deuda - $c->total_pagado)),
                                        'pagados_count' => $faseColegiatura->filter(fn($c) => $c->total_pagado >= $c->total_deuda && $c->total_pagado > 0)->count(),
                                        'pendientes_count' => $faseColegiatura->filter(fn($c) => $c->total_pagado < $c->total_deuda)->count(),
                                    ];
                                @endphp
                                <div class="row mb-4">
                                    @php
                                        $faseColCards = [
                                            ['label' => 'Total Programa (Bruto)', 'value' => 'Bs ' . number_format($faseColStats['total'], 2), 'icon' => 'ri-funds-line', 'theme' => 'dark'],
                                            ['label' => 'Descuento Aplicado', 'value' => 'Bs ' . number_format($faseColStats['descuento'], 2), 'icon' => 'ri-discount-percent-line', 'theme' => 'danger'],
                                            ['label' => 'Neto a Cobrar', 'value' => 'Bs ' . number_format($faseColStats['neto'], 2), 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'primary'],
                                            ['label' => 'Total Cobrado', 'value' => 'Bs ' . number_format($faseColStats['pagado'], 2), 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success'],
                                            ['label' => 'Pendiente por Cobrar', 'value' => 'Bs ' . number_format($faseColStats['pendiente'], 2), 'icon' => 'ri-error-warning-line', 'theme' => 'warning'],
                                            ['label' => 'Participantes', 'value' => $faseColegiatura->count() . ' registros', 'icon' => 'ri-team-line', 'theme' => 'info'],
                                        ];
                                    @endphp
                                    @foreach($faseColCards as $item)
                                        <div class="col-xxl col-xl-4 col-md-6 mb-3">
                                            <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="min-width-0 me-2">
                                                            <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                            <h4 class="stat-value text-truncate mb-0" style="font-size: 1rem;">{{ $item['value'] }}</h4>
                                                        </div>
                                                        <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                            <i class="{{ $item['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-checkbox-circle-line"></i>Estado de Pagos · Colegiaturas</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-colegiatura-estado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c;">{{ $faseColStats['pagados_count'] }}</div>
                                                            <div class="stat-label">Pagados</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548;">{{ $faseColStats['pendientes_count'] }}</div>
                                                            <div class="stat-label">Pendientes</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 mb-3">
                                        <div class="card chart-card h-100 mb-0">
                                            <div class="card-body">
                                                <h6 class="chart-title"><i class="ri-funds-line"></i>Resumen Financiero · Colegiatura (Bs)</h6>
                                                <div class="chart-body-layout">
                                                    <div class="chart-area">
                                                        <canvas id="chart-fase-colegiatura-cobrado" height="200"></canvas>
                                                    </div>
                                                    <div class="chart-stats">
                                                        <div class="chart-stat-box" style="background:#e8f4fd;">
                                                            <div class="stat-num" style="color:#299cdb; font-size:1rem;">Bs {{ number_format($faseColStats['neto'], 2) }}</div>
                                                            <div class="stat-label">Neto a Cobrar</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#e8faf1;">
                                                            <div class="stat-num" style="color:#0ab39c; font-size:1rem;">Bs {{ number_format($faseColStats['pagado'], 2) }}</div>
                                                            <div class="stat-label">Cobrado</div>
                                                        </div>
                                                        <div class="chart-stat-box" style="background:#fef0f0;">
                                                            <div class="stat-num" style="color:#f06548; font-size:1rem;">Bs {{ number_format($faseColStats['pendiente'], 2) }}</div>
                                                            <div class="stat-label">Pendiente</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive table-card">
                                    <table class="table table-borderless align-middle mb-0 table-hover tabla-fase-inscritos">
                                        <thead class="table-light text-muted text-uppercase">
                                            <tr>
                                                <th style="width: 38px;" class="text-center">#</th>
                                                <th>Participante</th>
                                                <th style="width: 110px;" class="text-end">Deuda Total</th>
                                                <th style="width: 100px;" class="text-end">Dto.</th>
                                                <th style="width: 110px;" class="text-end">Pagado</th>
                                                <th style="width: 110px;" class="text-end">Pendiente</th>
                                                <th style="width: 55px;" class="text-center">#P</th>
                                                <th style="width: 95px;" class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($faseColegiatura as $c)
                                            @php $pendienteC = max(0, $c->total_deuda - $c->total_pagado); $esTraspasoFC = $traspasoPorParticipante->get($c->participante_id, 0); @endphp
                                            <tr class="{{ $esTraspasoFC ? 'traspaso-row' : '' }}">
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td class="cell-participante">
                                                    <div class="nombre">{{ $c->apellidos }}, {{ $c->nombres }}@if($esTraspasoFC) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                    <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $c->ci }}</div>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">Bs {{ number_format($c->total_deuda, 2) }}</td>
                                                <td class="text-end text-muted">{{ $c->total_descuento > 0 ? 'Bs '.number_format($c->total_descuento, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $c->total_pagado > 0 ? 'text-success' : 'text-muted' }}">{{ $c->total_pagado > 0 ? 'Bs '.number_format($c->total_pagado, 2) : '—' }}</td>
                                                <td class="text-end fw-semibold {{ $pendienteC > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendienteC > 0 ? 'Bs '.number_format($pendienteC, 2) : '—' }}</td>
                                                <td class="text-center"><span class="pill-mini" style="background:#eef0f4; color:#495057;">{{ $c->num_pagos }}</span></td>
                                                <td class="text-center">
                                                    @if($pendienteC == 0 && $c->total_pagado > 0)
                                                        <span class="pill-mini" style="background:#d1fae5; color:#0ab39c;"><i class="ri-checkbox-circle-line"></i> Pagado</span>
                                                    @elseif($c->total_pagado > 0 && $pendienteC > 0)
                                                        <span class="pill-mini" style="background:#dbeafe; color:#299cdb;"><i class="ri-time-line"></i> Parcial</span>
                                                    @else
                                                        <span class="pill-mini" style="background:#fef3c7; color:#f7b84b;"><i class="ri-error-warning-line"></i> Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4 fs-14">No se encontraron registros de colegiatura</td>
                                            </tr>
                                            @endforelse
                                            @if($faseColegiatura->count() > 0)
                                            @php
                                                $fcSDeuda = $faseColegiatura->sum('total_deuda');
                                                $fcSDto = $faseColegiatura->sum('total_descuento');
                                                $fcSPagado = $faseColegiatura->sum('total_pagado');
                                                $fcSPendiente = max(0, $fcSDeuda - $fcSPagado);
                                            @endphp
                                            <tr class="fw-bold" style="background:#f3f5f9;">
                                                <td colspan="2" class="text-end text-uppercase text-muted fs-11">Totales</td>
                                                <td class="text-end text-dark">Bs {{ number_format($fcSDeuda, 2) }}</td>
                                                <td class="text-end text-muted">Bs {{ number_format($fcSDto, 2) }}</td>
                                                <td class="text-end text-success">Bs {{ number_format($fcSPagado, 2) }}</td>
                                                <td class="text-end text-danger">Bs {{ number_format($fcSPendiente, 2) }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endcanany

                    @canany(['programas.tab.inscritos', 'programas.tab.matriculas', 'programas.tab.colegiaturas'])
                    {{-- ===== Tab: Total Inscritos reales Marketing ===== --}}
                    <div class="tab-pane" id="tab-marketing-reales" role="tabpanel">
                        @php
                            $numModulos = max(1, $modulos->count());
                            $inscritosReales = [];
                            $noReales = [];

                            foreach ($inscripciones as $ins) {
                                $pId = $ins->participante_id;
                                $pMat = $pagoMatriculaPorParticipante->get($pId);
                                $pCol = $pagoColegiaturaPorParticipante->get($pId);

                                $brutoMat = (float) $ins->matricula;
                                $descMat = (float) ($ins->descuento_matricula ?? 0);
                                $netoMat = max(0, $brutoMat - $descMat);
                                $pagadoMat = $pMat ? (float) $pMat->pagado : 0;
                                $matOk = $netoMat == 0 || ($pagadoMat >= $netoMat);

                                $brutoCol = (float) $ins->monto_programa;
                                $descCol = (float) ($ins->descuento_colegiatura ?? 0);
                                $netoCol = max(0, $brutoCol - $descCol);
                                $pagadoCol = $pCol ? (float) $pCol->pagado : 0;

                                $esContado = (int) $ins->contado === 1;
                                $esReal = false;

                                if ($esContado) {
                                    $cuotaMinCol = $netoCol / $numModulos;
                                    $esReal = $pagadoCol >= $cuotaMinCol && $netoCol > 0;
                                } else {
                                    $colegiaturasPagadas = 0;
                                    foreach ($modulos as $mod) {
                                        $k = $pId . '_' . $mod->id;
                                        $cC = $contableMatrix[$k] ?? null;
                                        $totalPagadoMod = 0;
                                        if ($cC) {
                                            foreach ($cC['cuotas'] as $cuota) {
                                                if ($cuota['tipo'] === 'Colegiatura') {
                                                    $totalPagadoMod += (float) $cuota['total_pagado'];
                                                }
                                            }
                                        }
                                        if ($totalPagadoMod > 0) $colegiaturasPagadas++;
                                    }
                                    $esReal = $matOk && $colegiaturasPagadas >= 1;
                                }

                                // Calcular valor de una colegiatura individual
                                $valorUnaCol = 0;
                                $cantCuotasCol = 0;
                                foreach ($modulos as $mod) {
                                    $k = $pId . '_' . $mod->id;
                                    $cC = $contableMatrix[$k] ?? null;
                                    if ($cC) {
                                        foreach ($cC['cuotas'] as $cuota) {
                                            if ($cuota['tipo'] === 'Colegiatura') {
                                                $valorUnaCol = (float) $cuota['deuda'];
                                                $cantCuotasCol++;
                                            }
                                        }
                                    }
                                }

                                $item = [
                                    'participante_id' => $pId,
                                    'ci' => $ins->ci,
                                    'nombres' => $ins->nombres,
                                    'apellidos' => $ins->apellidos,
                                    'celular' => $ins->celular,
                                    'correo' => $ins->correo,
                                    'sexo' => $ins->sexo,
                                    'departamento' => $ins->departamento,
                                    'contado' => $esContado,
                                    'mat_pagado' => $pagadoMat,
                                    'mat_neto' => $netoMat,
                                    'col_pagado' => $pagadoCol,
                                    'col_neto' => $netoCol,
                                    'valor_una_col' => $valorUnaCol,
                                    'es_real' => $esReal,
                                    'es_traspaso' => (int) ($ins->es_traspaso ?? 0),
                                ];

                                if ($esReal) {
                                    $inscritosReales[] = $item;
                                } else {
                                    $noReales[] = $item;
                                }
                            }

                            $totalReales = count($inscritosReales);
                            $totalNoReales = count($noReales);
                            $porcentajeReales = ($totalReales + $totalNoReales) > 0 ? round(($totalReales / ($totalReales + $totalNoReales)) * 100) : 0;

                            // Ordenar: reales primero, luego no reales
                            $todosInscritos = array_merge($inscritosReales, $noReales);
                        @endphp

                        <div class="d-flex align-items-center gap-2 mb-3 mt-3">
                            <span class="avatar-xs"><span class="avatar-title bg-soft-info text-info rounded-circle fs-16"><i class="ri-team-line"></i></span></span>
                            <div>
                                <h6 class="mb-0 fs-14 fw-semibold">Total Inscritos reales Marketing</h6>
                                <small class="text-muted" style="font-size:.72rem;">
                                    Se considera inscrito real si:
                                    <strong>Regular</strong> = pagó matrícula + al menos 1 colegiatura ·
                                    <strong>Contado</strong> = pagó al menos 1/{<span id="mk-num-modulos">{{ $numModulos }}</span>} de la colegiatura
                                </small>
                            </div>
                        </div>

                        {{-- Cards resumen --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="stat-card h-100" style="--stat-accent:#405189;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div><p class="stat-label text-truncate">Total Inscritos</p><h4 class="stat-value mb-0">{{ $totalReales + $totalNoReales }}</h4></div>
                                            <div class="stat-icon flex-shrink-0 bg-soft-primary text-primary"><i class="ri-team-line"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card h-100" style="--stat-accent:#0ab39c;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div><p class="stat-label text-truncate">Inscritos Reales</p><h4 class="stat-value mb-0 text-success">{{ $totalReales }}</h4></div>
                                            <div class="stat-icon flex-shrink-0 bg-soft-success text-success"><i class="ri-checkbox-circle-line"></i></div>
                                        </div>
                                        <div class="mt-2"><small class="text-muted">{{ $porcentajeReales }}% del total</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card h-100" style="--stat-accent:#f06548;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div><p class="stat-label text-truncate">No Reales</p><h4 class="stat-value mb-0 text-danger">{{ $totalNoReales }}</h4></div>
                                            <div class="stat-icon flex-shrink-0 bg-soft-danger text-danger"><i class="ri-close-circle-line"></i></div>
                                        </div>
                                        <div class="mt-2"><small class="text-muted">{{ 100 - $porcentajeReales }}% del total</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card h-100" style="--stat-accent:#299cdb;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div><p class="stat-label text-truncate">Módulos</p><h4 class="stat-value mb-0">{{ $numModulos }}</h4></div>
                                            <div class="stat-icon flex-shrink-0 bg-soft-info text-info"><i class="ri-bookmark-line"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buscador --}}
                        <div class="p-3 border-bottom d-flex align-items-center gap-2">
                            <div class="position-relative flex-grow-1" style="max-width:360px;">
                                <i class="ri-search-line position-absolute" style="top:50%;left:12px;transform:translateY(-50%);color:#878a99;font-size:.9rem;"></i>
                                <input type="text" class="form-control form-control-sm ps-4" id="buscador-marketing-reales" placeholder="Buscar por nombres, apellidos o CI..." style="font-size:.82rem;">
                            </div>
                            <span class="text-muted fs-11" id="resultados-marketing-reales">Mostrando <strong id="contador-marketing-reales">0</strong> resultados</span>
                        </div>

                        {{-- Tabla --}}
                        <div class="table-responsive table-card">
                            <table class="table table-borderless align-middle mb-0 table-hover tabla-marketing-reales" id="tabla-marketing-reales">
                                <thead class="table-light text-muted text-uppercase">
                                    <tr>
                                        <th style="width:38px;" class="text-center">#</th>
                                        <th>Participante</th>
                                        <th style="width:100px;" class="text-center">Sexo / Depto.</th>
                                        <th style="width:90px;" class="text-center">Plan</th>
                                        <th style="width:120px;" class="text-end">Matrícula</th>
                                        <th style="width:120px;" class="text-end">Colegiatura</th>
                                        <th style="width:90px;" class="text-center">Inscrito Real</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todosInscritos as $idx => $item)
                                    @php
                                        $mkSexoClass = strtoupper(substr($item['sexo'] ?? '', 0, 1)) === 'M' ? 'sexo-h' : 'sexo-m';
                                        $mkRowClass = $item['es_real'] ? '' : 'opacity-75';
                                    @endphp
                                    <tr class="{{ $mkRowClass }} {{ $item['es_traspaso'] ? 'traspaso-row' : '' }}" style="{{ $item['es_real'] ? 'background:rgba(10,179,156,0.06) !important;' : '' }}">
                                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td class="cell-participante">
                                            <div class="nombre">{{ $item['apellidos'] }}, {{ $item['nombres'] }}@if($item['es_traspaso']) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                            <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $item['ci'] }}</div>
                                            @if($item['celular'] || $item['correo'])
                                            <div class="text-muted mt-1" style="font-size:.65rem;">
                                                @if($item['celular'])<span><i class="ri-phone-line"></i> {{ $item['celular'] }}</span>@endif
                                                @if($item['correo'])<span class="ms-1"><i class="ri-mail-line"></i> {{ $item['correo'] }}</span>@endif
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="pill-mini {{ $mkSexoClass }}">{{ $item['sexo'] }}</span>
                                            <div class="text-muted mt-1" style="font-size:.65rem;"><i class="ri-map-pin-line"></i> {{ $item['departamento'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($item['contado'])
                                                <span class="badge bg-soft-info text-info fs-10">Contado</span>
                                            @else
                                                <span class="badge bg-soft-primary text-primary fs-10">Regular</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="money-stack">
                                                <span class="deuda">Bs {{ number_format($item['mat_neto'], 2) }}</span>
                                                <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($item['mat_pagado'], 2) }}</span>
                                                @if($item['mat_pagado'] > 0 || $item['mat_neto'] == 0)
                                                    <span class="pagado"><i class="ri-checkbox-circle-line"></i> OK</span>
                                                @else
                                                    <span class="pendiente"><i class="ri-time-line"></i> Pendiente</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="money-stack">
                                                <span class="deuda">Bs {{ number_format($item['col_neto'], 2) }}</span>
                                                <span class="pagado"><i class="ri-check-line"></i> Pag {{ number_format($item['col_pagado'], 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($item['es_real'])
                                                <span class="badge bg-soft-success text-success fs-10"><i class="ri-checkbox-circle-line"></i> Real</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary fs-10"><i class="ri-close-circle-line"></i> No Real</span>
                                                <div class="text-danger mt-1" style="font-size:.65rem;font-weight:600;">
                                                    @php
                                                        $faltaMat = max(0, $item['mat_neto'] - $item['mat_pagado']);
                                                        $faltaCol = 0;
                                                        if ($item['contado']) {
                                                            $cuotaMinCol = $item['col_neto'] / $numModulos;
                                                            $faltaCol = max(0, $cuotaMinCol - $item['col_pagado']);
                                                        } else {
                                                            $faltaCol = $item['col_pagado'] > 0 ? 0 : ($item['valor_una_col'] > 0 ? $item['valor_una_col'] : 0);
                                                        }
                                                    @endphp
                                                    @if($faltaMat > 0)Falta pagar Mat: Bs {{ number_format($faltaMat, 2) }}@endif
                                                    @if($faltaCol > 0)
                                                        @if($faltaMat > 0)<br>@endif
                                                        Falta pagar Col: Bs {{ number_format($faltaCol, 2) }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4 fs-14">No hay inscripciones en este programa</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endcanany

                    @if($puedeVerContable)
                    {{-- TAB CONTABILIDAD INGRESOS --}}
                    <div class="tab-pane" id="tab-contabilidad" role="tabpanel">
                        @php
                            $mesesIngresos = $ingresosPorMes->groupBy('mes')->sortKeys();
                            $labelsIngresos = [];
                            $dataMatricula = [];
                            $dataColegiatura = [];
                            $dataNivelacionIng = [];
                            $totalIngMat = 0;
                            $totalIngCol = 0;
                            $totalIngNiv = 0;
                            foreach ($mesesIngresos as $mes => $items) {
                                $labelsIngresos[] = $mesEs($mes);
                                $matItem = $items->firstWhere('tipo', 'Matricula');
                                $colItem = $items->firstWhere('tipo', 'Colegiatura');
                                $nivItem = $items->firstWhere('tipo', 'Nivelacion');
                                $mt = $matItem ? (float) $matItem->total : 0;
                                $ct = $colItem ? (float) $colItem->total : 0;
                                $nt = $nivItem ? (float) $nivItem->total : 0;
                                $dataMatricula[] = $mt;
                                $dataColegiatura[] = $ct;
                                $dataNivelacionIng[] = $nt;
                                $totalIngMat += $mt;
                                $totalIngCol += $ct;
                                $totalIngNiv += $nt;
                            }
                        @endphp

                        @php
                            $totalIngGeneral = $totalIngMat + $totalIngCol + $totalIngNiv;
                            $numMesesIng = count($labelsIngresos);
                            $promedioMensualIng = $numMesesIng > 0 ? $totalIngGeneral / $numMesesIng : 0;
                            $mejorMesIng = null; $mejorMesMonto = 0;
                            foreach ($mesesIngresos as $mesK => $itemsK) {
                                $tK = (float) $itemsK->sum('total');
                                if ($tK > $mejorMesMonto) { $mejorMesMonto = $tK; $mejorMesIng = $mesK; }
                            }
                        @endphp
                        <div class="row mb-4">
                            @php
                                $contabilidadCards = [
                                    ['label' => 'Ingresos Matrícula', 'value' => 'Bs ' . number_format($totalIngMat, 2), 'sub' => $totalIngGeneral > 0 ? round($totalIngMat * 100 / $totalIngGeneral) . '% del total' : null, 'icon' => 'ri-bank-card-line', 'theme' => 'primary'],
                                    ['label' => 'Ingresos Colegiatura', 'value' => 'Bs ' . number_format($totalIngCol, 2), 'sub' => $totalIngGeneral > 0 ? round($totalIngCol * 100 / $totalIngGeneral) . '% del total' : null, 'icon' => 'ri-funds-line', 'theme' => 'success'],
                                    ['label' => 'Ingresos 2da Nivelación', 'value' => 'Bs ' . number_format($totalIngNiv, 2), 'sub' => $totalIngGeneral > 0 ? round($totalIngNiv * 100 / $totalIngGeneral) . '% del total' : null, 'icon' => 'ri-refund-2-line', 'theme' => 'warning'],
                                    ['label' => 'Total Ingresos', 'value' => 'Bs ' . number_format($totalIngGeneral, 2), 'sub' => $numMesesIng . ' mes' . ($numMesesIng !== 1 ? 'es' : '') . ' con ingresos', 'icon' => 'ri-money-dollar-circle-line', 'theme' => 'dark'],
                                    ['label' => 'Promedio Mensual', 'value' => 'Bs ' . number_format($promedioMensualIng, 2), 'sub' => $mejorMesIng ? 'Mejor mes: ' . $mesEs($mejorMesIng) : null, 'icon' => 'ri-line-chart-line', 'theme' => 'info'],
                                ];
                            @endphp
                            @foreach($contabilidadCards as $item)
                                <div class="col-xl-3 col-md-6 mb-3">
                                    <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="min-width-0 me-2">
                                                    <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                    <h4 class="stat-value text-truncate mb-0">{{ $item['value'] }}</h4>
                                                    @if(!empty($item['sub']))
                                                        <small class="text-muted fs-11">{{ $item['sub'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                    <i class="{{ $item['icon'] }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row g-3">
                            <div class="col-xl-8">
                                <div class="cht-card h-100">
                                    <div class="cht-card-header">
                                        <h6 class="cht-title"><i class="ri-table-line"></i> Ingresos mensuales</h6>
                                        <span class="cht-sub">{{ $numMesesIng }} mes{{ $numMesesIng !== 1 ? 'es' : '' }} · Bs {{ number_format($totalIngGeneral, 2) }}</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0 table-hover">
                                            <thead class="table-light text-muted text-uppercase fs-11">
                                                <tr>
                                                    <th>Mes</th>
                                                    <th class="text-end" style="width:15%">Matrícula (Bs)</th>
                                                    <th class="text-end" style="width:15%">Colegiatura (Bs)</th>
                                                    <th class="text-end" style="width:15%">2da Nivelación (Bs)</th>
                                                    <th class="text-end" style="width:13%">Total (Bs)</th>
                                                    <th style="width:15%">% del total</th>
                                                    <th class="text-center" style="width:8%">Detalle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($mesesIngresos as $mes => $items)
                                                @php
                                                    $matItem = $items->firstWhere('tipo', 'Matricula');
                                                    $colItem = $items->firstWhere('tipo', 'Colegiatura');
                                                    $nivItem = $items->firstWhere('tipo', 'Nivelacion');
                                                    $matTotal = $matItem ? (float) $matItem->total : 0;
                                                    $colTotal = $colItem ? (float) $colItem->total : 0;
                                                    $nivTotal = $nivItem ? (float) $nivItem->total : 0;
                                                    $mesTotal = $matTotal + $colTotal + $nivTotal;
                                                    $pctMes = $totalIngGeneral > 0 ? round($mesTotal * 100 / $totalIngGeneral) : 0;
                                                    $esMejor = $mes === $mejorMesIng;
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold text-dark">
                                                        <i class="ri-calendar-2-line text-muted me-1 align-bottom"></i>{{ $mesEs($mes) }}
                                                        @if($esMejor && $numMesesIng > 1)
                                                            <span class="badge bg-soft-success text-success ms-1 fs-10" title="Mes con mayores ingresos"><i class="ri-trophy-line"></i></span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-medium text-primary">Bs {{ number_format($matTotal, 2) }}</td>
                                                    <td class="text-end fw-medium text-success">Bs {{ number_format($colTotal, 2) }}</td>
                                                    <td class="text-end fw-medium" style="color:#f7b84b;">Bs {{ number_format($nivTotal, 2) }}</td>
                                                    <td class="text-end fw-bold text-dark">Bs {{ number_format($mesTotal, 2) }}</td>
                                                    <td>
                                                        <div class="cht-share">
                                                            <span class="bar"><div style="width: {{ $pctMes }}%; background:linear-gradient(90deg,#299cdb,#0ab39c);"></div></span>
                                                            <span class="txt">{{ $pctMes }}%</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-icon btn-soft-info btn-ver-pagos" data-mes="{{ $mes }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver pagos del mes">
                                                            <i class="ri-eye-line fs-14"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4 fs-14">
                                                        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
                                                        No se encontraron ingresos registrados
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            @if($numMesesIng > 0)
                                            <tfoot>
                                                <tr class="cht-total-row">
                                                    <td class="text-dark">TOTAL</td>
                                                    <td class="text-end text-primary">Bs {{ number_format($totalIngMat, 2) }}</td>
                                                    <td class="text-end text-success">Bs {{ number_format($totalIngCol, 2) }}</td>
                                                    <td class="text-end" style="color:#f7b84b;">Bs {{ number_format($totalIngNiv, 2) }}</td>
                                                    <td class="text-end text-dark">Bs {{ number_format($totalIngGeneral, 2) }}</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="cht-card h-100">
                                    <div class="cht-card-header">
                                        <h6 class="cht-title"><i class="ri-bar-chart-2-line"></i> Ingresos por mes</h6>
                                        <span class="cht-sub">Matrícula + Colegiatura + 2da Nivelación</span>
                                    </div>
                                    <div class="p-3">
                                        <div style="position:relative; height:320px">
                                            <canvas id="chart-ingresos-mes"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /tab-pane tab-contabilidad -->
                    @endif

                    @if($puedeVerContable)
                    {{-- TAB HISTORIAL INSCRIPCIONES --}}{{-- TAB HISTORIAL INSCRIPCIONES --}}
                    <div class="tab-pane" id="tab-historial" role="tabpanel">
                        @php
                            $totalContado = $historialInscripciones->sum('contado');
                            $totalRegular = $historialInscripciones->sum('regular');
                            $totalInscritosH = $historialInscripciones->sum('total');
                            $totalPagaronH = $pagaronPorMes->sum('pagaron');

                            $histLabels = $historialInscripciones->pluck('mes')->toArray();
            $histLabelsEs = collect($histLabels)->map($mesEs)->toArray();
                            $histContado = $historialInscripciones->pluck('contado')->toArray();
                            $histRegular = $historialInscripciones->pluck('regular')->toArray();

                            $histInsc = collect($histLabels)->mapWithKeys(fn($m) => [$m => 0]);
                            $histPag = collect($histLabels)->mapWithKeys(fn($m) => [$m => 0]);
                            foreach ($inscritosPorMes as $r) { if ($histInsc->has($r->mes)) $histInsc[$r->mes] = (int) $r->inscritos; }
                            foreach ($pagaronPorMes as $r) { if ($histPag->has($r->mes)) $histPag[$r->mes] = (int) $r->pagaron; }
                        @endphp

                        <div class="row mb-4">
                            @php
                                $historialCards = [
                                    ['label' => 'Total Inscritos', 'value' => $totalInscritosH, 'icon' => 'ri-team-line', 'theme' => 'primary'],
                                    ['label' => 'Plan Contado', 'value' => $totalContado, 'icon' => 'ri-money-dollar-box-line', 'theme' => 'info'],
                                    ['label' => 'Plan Regular', 'value' => $totalRegular, 'icon' => 'ri-calendar-todo-line', 'theme' => 'dark'],
                                    ['label' => 'Han Pagado / Total', 'value' => $totalPagaronH . ' / ' . $totalInscritosH, 'icon' => 'ri-checkbox-circle-line', 'theme' => 'success'],
                                ];
                            @endphp
                            @foreach($historialCards as $item)
                                <div class="col-xl-3 col-md-6 mb-3">
                                    <div class="stat-card h-100" style="--stat-accent: {{ $themeAccents[$item['theme']] ?? '#405189' }};">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="min-width-0 me-2">
                                                    <p class="stat-label text-truncate">{{ $item['label'] }}</p>
                                                    <h4 class="stat-value text-truncate mb-0">{{ $item['value'] }}</h4>
                                                </div>
                                                <div class="stat-icon flex-shrink-0 bg-soft-{{ $item['theme'] }} text-{{ $item['theme'] }}">
                                                    <i class="{{ $item['icon'] }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row g-3">
                            <div class="col-xl-8">
                                <div class="cht-card h-100">
                                    <div class="cht-card-header">
                                        <h6 class="cht-title"><i class="ri-table-line"></i> Inscripciones mensuales</h6>
                                        <span class="cht-sub">{{ $historialInscripciones->count() }} mes{{ $historialInscripciones->count() !== 1 ? 'es' : '' }} · {{ $totalInscritosH }} inscritos</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0 table-hover">
                                            <thead class="table-light text-muted text-uppercase fs-11">
                                                <tr>
                                                    <th>Mes</th>
                                                    <th class="text-center" style="width:11%">Contado</th>
                                                    <th class="text-center" style="width:11%">Regular</th>
                                                    <th class="text-center" style="width:11%">Total</th>
                                                    <th class="text-center" style="width:11%">Pagaron</th>
                                                    <th style="width:20%">% Pago</th>
                                                    <th class="text-center" style="width:9%">Detalle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($historialInscripciones as $h)
                                                @php
                                                    $ppH = $histInsc->has($h->mes) ? ['inscritos' => $histInsc[$h->mes], 'pagaron' => $histPag[$h->mes]] : ['inscritos' => 0, 'pagaron' => 0];
                                                    $pctH = $ppH['inscritos'] > 0 ? round($ppH['pagaron'] / $ppH['inscritos'] * 100) : 0;
                                                    $pctColor = $pctH >= 80 ? '#0ab39c' : ($pctH >= 40 ? '#f7b84b' : '#f06548');
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-calendar-2-line text-muted me-1 align-bottom"></i>{{ $mesEs($h->mes) }}</td>
                                                    <td class="text-center fw-medium text-info">{{ $h->contado }}</td>
                                                    <td class="text-center fw-medium text-primary">{{ $h->regular }}</td>
                                                    <td class="text-center fw-bold text-dark">{{ $h->total }}</td>
                                                    <td class="text-center fw-medium {{ $ppH['pagaron'] > 0 ? 'text-success' : 'text-muted' }}">{{ $ppH['pagaron'] }} / {{ $ppH['inscritos'] }}</td>
                                                    <td>
                                                        <div class="cht-pct-mini">
                                                            <span class="bar"><div style="width: {{ $pctH }}%; background: {{ $pctColor }};"></div></span>
                                                            <span class="txt" style="color: {{ $pctColor }};">{{ $pctH }}%</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-icon btn-soft-primary btn-ver-inscritos" data-mes="{{ $h->mes }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver inscritos del mes">
                                                            <i class="ri-eye-line fs-14"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4 fs-14">
                                                        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
                                                        No se encontraron inscripciones
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            @if($historialInscripciones->count() > 0)
                                            <tfoot>
                                                <tr class="cht-total-row">
                                                    <td class="text-dark">TOTAL</td>
                                                    <td class="text-center text-info">{{ $totalContado }}</td>
                                                    <td class="text-center text-primary">{{ $totalRegular }}</td>
                                                    <td class="text-center text-dark">{{ $totalInscritosH }}</td>
                                                    <td class="text-center text-success">{{ $totalPagaronH }} / {{ $totalInscritosH }}</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="cht-card h-100">
                                    <div class="cht-card-header">
                                        <h6 class="cht-title"><i class="ri-line-chart-line"></i> Inscripciones por mes</h6>
                                        <span class="cht-sub">Contado vs Regular</span>
                                    </div>
                                    <div class="p-3">
                                        <div style="position:relative; height:320px">
                                            <canvas id="chart-historial"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- tab-pane tab-historial -->
                    @endif

                    {{-- TAB MÓDULOS DEL PROGRAMA --}}
                    <div class="tab-pane" id="tab-modulos" role="tabpanel">
                        <div class="card section-card">
                            <div class="card-header d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                                    <h5 class="card-title fs-14 fw-semibold mb-0">
                                        <i class="ri-bookmark-line align-bottom me-1"></i> Módulos del Programa
                                        <span class="badge bg-soft-primary text-primary ms-1">{{ $modulos->count() }}</span>
                                    </h5>
                                    <div class="d-flex align-items-center flex-wrap flex-shrink-0 modulos-header-actions">
                                        <a href="{{ route('admin.programas.horarios-pdf', $programa->id) }}" class="btn btn-sm btn-soft-danger d-flex align-items-center gap-1" title="Horario de clases sin docentes">
                                            <i class="ri-file-pdf-line"></i> Horario PDF
                                        </a>
                                        <a href="{{ route('admin.programas.horarios-pdf-docentes', $programa->id) }}" class="btn btn-sm btn-soft-danger d-flex align-items-center gap-1" title="Horario de clases con docentes">
                                            <i class="ri-file-pdf-line"></i> Horario PDF (docentes)
                                        </a>
                                        @if((int)$programa->estado_id === 4)
                                        @can('programas.modulos.agregar')
                                        <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1" id="btn-agregar-modulos">
                                            <i class="ri-add-circle-line"></i> Agregar módulos
                                        </button>
                                        @endcan
                                        @endif
                                        @if((int)($programa->n_modulos ?? 0) > 0 && $puedeRebalancear)
                                        <button type="button" class="btn btn-sm btn-soft-warning d-flex align-items-center gap-1" id="btn-rebalancear-colegiaturas" title="Reajusta las cuotas de colegiatura según n_modulos">
                                            <i class="ri-refresh-line"></i> Rebalancear cuotas
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                <ul class="nav nav-tabs card-header-tabs mb-0 border-bottom-0 modulos-subtabs" role="tablist" aria-label="Secciones de módulos del programa">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-seguimiento-academico" role="tab" aria-selected="true">
                                            <i class="ri-bookmark-line align-bottom me-1"></i> Seguimiento Académico
                                            <span class="badge bg-soft-primary fs-10 ms-1">{{ $modulos->count() }} módulos</span>
                                        </a>
                                    </li>
                                    @if($todosModulosConcluidos && $esDiplomado)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tab-seguimiento-concluido" role="tab" aria-selected="false">
                                            <i class="ri-award-line align-bottom me-1"></i> Fase Académica Concluida
                                            <span class="badge bg-soft-success fs-10 ms-1">{{ count($seguimientoConcluido) }} participantes</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if($nivelacionModulos->count() > 0)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tab-nivelacion" role="tab" aria-selected="false">
                                            <i class="ri-refund-2-line align-bottom me-1"></i> Nivelación
                                            <span class="badge bg-soft-warning fs-10 ms-1">{{ $nivelacionTotalCuotas }} cuotas</span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    {{-- TAB 1: Seguimiento Académico --}}
                                    <div class="tab-pane active" id="tab-seguimiento-academico" role="tabpanel">
                                        <div class="table-responsive table-card">
                                            <table class="table table-borderless align-middle mb-0 tabla-seguimiento">
                                                <thead class="table-light text-muted text-uppercase">
                                                    <tr>
                                                        <th style="width: 60px;" class="text-center">N°</th>
                                                        <th>Módulo</th>
                                                        <th style="width: 220px;">Docente</th>
                                                        <th style="width: 150px;" class="text-center">Estado</th>
                                                        <th style="width: 130px;" class="text-center">Pago Doc.</th>
                                                        <th style="width: 55px;" class="text-center">Moodle</th>
                                                        <th style="width: 220px;" class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $horariosPorMod = $horariosModulos->groupBy('modulo_id'); @endphp
                                                    @forelse($modulos as $m)
                                                    @php $tieneHorarios = isset($horariosPorMod[$m->id]) && count($horariosPorMod[$m->id]) > 0; @endphp
                                                    @php
                                                        $colorMod = match($m->desarrollo) {
                                                            'Concluido' => '#0ab39c',
                                                            'En Desarrollo' => '#299cdb',
                                                            'No Inicio' => '#878a99',
                                                            'Pendiente' => '#f7b84b',
                                                            'Suspendido' => '#f06548',
                                                            default => '#878a99'
                                                        };
                                                        $iniciales = $m->docente_nombres ? strtoupper(substr($m->docente_nombres, 0, 1) . substr($m->docente_apellidos, 0, 1)) : '?';
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="modulo-num">{{ $loop->iteration }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="ri-book-2-line text-primary"></i>
                                                                <span class="fw-semibold fs-14 modulo-nombre">{{ $m->nombre }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($m->docente_apellidos)
                                                                <div class="docente-cell">
                                                                    <span class="docente-avatar">{{ $iniciales }}</span>
                                                                    <div>
                                                                        <div class="docente-nombre">{{ $m->docente_apellidos }}, {{ $m->docente_nombres }}</div>
                                                                        <div class="docente-rol"><i class="ri-user-star-line"></i> Docente asignado</div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic"><i class="ri-user-unfollow-line"></i> Sin asignar</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($puedeCambiarEstado)
                                                            <span class="modulo-estado-badge btn-editar-modulo" id="modulo-badge-{{ $m->id }}" role="button" style="color: {{ $colorMod }};" data-modulo-id="{{ $m->id }}" data-desarrollo="{{ $m->desarrollo }}" data-fecha-conclusion="{{ $m->fecha_conclusion }}" title="Haz clic para cambiar estado">
                                                            @else
                                                            <span class="modulo-estado-badge" id="modulo-badge-{{ $m->id }}" style="color: {{ $colorMod }};">
                                                            @endif
                                                                {{ $m->desarrollo }}
                                                            </span>
                                                            @if($m->desarrollo === 'Concluido' && $m->fecha_conclusion)
                                                            <div class="mt-1" style="font-size:.6rem;line-height:1.3;color:#878a99;">
                                                                <i class="ri-calendar-check-line"></i> Concluido: {{ \Carbon\Carbon::parse($m->fecha_conclusion)->format('d/m/Y') }}<br>
                                                                <span class="text-muted">Límite nivelación: {{ \Carbon\Carbon::parse($m->fecha_conclusion)->addMonths(2)->format('d/m/Y') }}</span>
                                                            </div>
                                                            @endif
                                                            @if($m->desarrollo === 'Concluido' && isset($estadoNotasPorModulo[$m->id]))
                                                            @php $enm = $estadoNotasPorModulo[$m->id]; @endphp
                                                            <div class="mt-1 notas-indicators" style="font-size:.65rem;line-height:1.3;">
                                                                @if($enm->con_nota_modulo > 0)
                                                                    <span class="badge bg-soft-success text-success" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota módulo</span>
                                                                @else
                                                                    <span class="badge bg-soft-secondary text-secondary" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota módulo</span>
                                                                @endif
                                                                <br>
                                                                @if($enm->total_reprobados > 0)
                                                                    @if($enm->con_nota_nivelacion > 0)
                                                                        <span class="badge bg-soft-success text-success mt-1" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota nivelación</span>
                                                                    @else
                                                                        <span class="badge bg-soft-secondary text-secondary mt-1" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota nivelación</span>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-soft-info text-info mt-1" style="font-size:.6rem;font-weight:500;"><i class="ri-check-line"></i> No corresponde</span>
                                                                @endif
                                                            </div>
@endif
                                                </td>

                                                @php
                                                    $mpgSePago = (int)($m->se_pago ?? 0);
                                                    $mpgPagoDocente = (float)($m->pago_docente ?? 0);
                                                    $mpgIva = (float)($m->iva ?? 0);
                                                    $mpgIt = (float)($m->it ?? 0);
                                                    $mpgFactura = (int)($m->factura ?? 0);
                                                    $mpgNetoDocente = $mpgFactura ? $mpgPagoDocente : max(0, $mpgPagoDocente - $mpgIva - $mpgIt);
                                                    $mpgTipoPago = $mpgSePago ? ($mpgFactura ? 'Factura' : 'Retención') : null;
                                                    $mpgTotalPagado = (float)($m->total_pagado ?? 0);
                                                    $mpgFechaPago = $m->fecha_pago ?? null;
                                                    $mpgFechaPagoConfig = $m->fecha_pago ?? '';
                                                    $mpgEsRegularizar = $mpgFechaPago ? \Carbon\Carbon::parse($mpgFechaPago)->isPast() : false;
                                                @endphp
                                                <td class="text-center">
                                                    @if($m->desarrollo === 'Concluido')
                                                        @if($mpgSePago)
                                                            <div class="d-flex flex-column align-items-center gap-1" style="font-size:.72rem;line-height:1.3;">
                                                                <span class="badge bg-soft-success text-success d-flex align-items-center gap-1" style="font-size:.65rem;font-weight:700;">
                                                                    <i class="ri-checkbox-circle-line"></i> Pagado
                                                                </span>
                                                                <span class="badge bg-soft-info text-info" style="font-size:.6rem;font-weight:600;">
                                                                    <i class="ri-{{ $mpgTipoPago === 'Factura' ? 'file-list-3-line' : 'file-paper-line' }}"></i> {{ $mpgTipoPago }}
                                                                </span>
                                                                @if($mpgNetoDocente > 0)
                                                                    <span class="fw-bold" style="font-size:.72rem;color:#212529;">Bs {{ number_format($mpgNetoDocente, 2) }}</span>
                                                                @endif
                                                                @if($mpgFechaPago)
                                                                    <span class="text-muted" style="font-size:.6rem;"><i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($mpgFechaPago)->format('d/m/Y') }}</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="d-flex flex-column align-items-center gap-1">
                                                                <span class="badge bg-soft-warning text-warning d-flex align-items-center gap-1" style="font-size:.65rem;font-weight:700;">
                                                                    <i class="ri-time-line"></i> Pendiente
                                                                </span>
                                                                 @if($mpgPagoDocente > 0)
                                                                 <span class="fw-bold" style="font-size:.7rem;color:#212529;">Bs {{ number_format($mpgNetoDocente, 2) }}</span>
                                                                 @if($mpgFechaPago)
                                                                 <span class="text-muted" style="font-size:.6rem;line-height:1.2;">
                                                                     <i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($mpgFechaPago)->format('d/m/Y') }}
                                                                     @if($mpgEsRegularizar)
                                                                     <span class="badge bg-soft-secondary text-secondary ms-1" style="font-size:.55rem;font-weight:600;">Regularizar</span>
                                                                     @endif
                                                                 </span>
                                                                 @endif
                                                                 @can('programas.modulos.pago.docente')
                                                                 <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 btn-procesar-pago-docente"
                                                                    data-modulo-id="{{ $m->id }}"
                                                                    data-modulo-nombre="{{ $m->nombre }}"
                                                                    data-pago-docente="{{ $m->pago_docente ?? 0 }}"
                                                                    data-factura="{{ $m->factura ?? 0 }}"
                                                                    data-iva="{{ $m->iva ?? 0 }}"
                                                                    data-it="{{ $m->it ?? 0 }}"
                                                                    data-gastos-extra="{{ $m->gastos_extra ?? 0 }}"
                                                                    data-proceso-pago="{{ $m->proceso_pago ?? '' }}"
                                                                    style="font-size:.65rem;font-weight:600;padding:.2rem .6rem;">
                                                                    <i class="ri-money-dollar-circle-line"></i> Procesar Pago
                                                                </button>
                                                                @endcan
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted" style="font-size:.7rem;">—</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    <div class="hstack gap-1 justify-content-center">
                                                                @if($m->moodle_link)
                                                                    <a href="{{ $m->moodle_link }}" target="_blank" class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ir a Moodle" onclick="event.stopPropagation();">
                                                                        <i class="ri-external-link-line fs-13"></i>
                                                                    </a>
                                                                @endif
                                                                @if($esSuperAdmin)
                                                                <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-soft-secondary btn-modulo-moodle" data-modulo-id="{{ $m->id }}" data-moodle-link="{{ $m->moodle_link ?? '' }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $m->moodle_link ? 'Editar enlace Moodle' : 'Agregar enlace Moodle' }}">
                                                                    <i class="ri-edit-line fs-13"></i>
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="acciones-modulo">
                                                            <div class="hstack gap-1 justify-content-center">
                                                                @if($puedeAsignarDocente)
                                                                @if($m->desarrollo !== 'Concluido')
                                                                <button class="btn btn-sm btn-icon btn-soft-warning btn-asignar-docente" data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Asignar docente">
                                                                    <i class="ri-user-add-line"></i>
                                                                </button>
                                                                @endif
                                                                @endif
                                                                @can('programas.modulos.sesion.extra')
                                                                @if($m->desarrollo !== 'Concluido' && $tieneHorarios)
                                                                    <button class="btn btn-sm btn-icon btn-soft-success btn-sesion-extra" data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar sesión extra">
                                                                        <i class="ri-add-line"></i>
                                                                    </button>
                                                                @endif
                                                                @endcan
                                                                @if(!$mpgSePago && auth()->user()->can('programas.modulos.pago.docente'))
                                                                <button class="btn btn-sm btn-icon btn-soft-dark btn-pago-docente"
                                                                    data-modulo-id="{{ $m->id }}"
                                                                    data-modulo-nombre="{{ $m->nombre }}"
                                                                    data-pago-docente="{{ $m->pago_docente ?? 0 }}"
                                                                    data-factura="{{ $m->factura ?? 0 }}"
                                                                    data-iva="{{ $m->iva ?? 0 }}"
                                                                    data-it="{{ $m->it ?? 0 }}"
                                                                    data-gastos-extra="{{ $m->gastos_extra ?? 0 }}"
                                                                    data-fecha-pago="{{ $mpgFechaPagoConfig }}"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Configurar pago docente y gastos">
                                                                    <i class="ri-money-dollar-circle-line"></i>
                                                                </button>
                                                                @endif
                                                                <a href="{{ route('admin.programas.modulo-detalle', $m->id) }}" class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                @if($tieneHorarios)
                                                                    <button class="btn btn-sm btn-soft-info d-flex align-items-center gap-1 btn-sesiones" data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver sesiones">
                                                                        <i class="ri-calendar-line"></i> Sesiones
                                                                    </button>
                                                                @else
                                                                    @can('programas.modulos.registrar.horarios')
                                                                    <button class="btn btn-sm btn-success d-flex align-items-center gap-1 btn-registrar-horarios" data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->nombre }}" title="Registrar sesiones para este módulo">
                                                                        <i class="ri-calendar-event-line"></i> Registrar sesiones
                                                                    </button>
                                                                    @endcan
                                                                @endif
                                                                @if($puedeWhatsappPrograma)
                                                                @if($m->desarrollo === 'Concluido' && isset($estadoNotasPorModulo[$m->id]) && $estadoNotasPorModulo[$m->id]->con_nota_modulo > 0)
                                                                <button class="btn btn-sm btn-soft-success d-flex align-items-center gap-1 btn-whatsapp-masivo" data-modulo-id="{{ $m->id }}" data-modulo-nombre="{{ $m->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar mensajes por WhatsApp">
                                                                    <i class="ri-whatsapp-line"></i> WhatsApp
                                                                </button>
                                                                @endif
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4 fs-14">No se encontraron módulos registrados</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- TAB 2: Seguimiento Concluido Fase Académica --}}
                                    @if($todosModulosConcluidos && $esDiplomado)
                                    @php
                                        $totalP = count($seguimientoConcluido);
                                        $conSeguimiento = collect($seguimientoConcluido)->filter(fn($s) => count($s['seguimientos']) > 0)->count();
                                        $enEtapa4 = collect($seguimientoConcluido)->filter(fn($s) => collect($s['seguimientos'])->contains(fn($seg) => (int)$seg->etapa === 4))->count();
                                        $enEtapa5 = collect($seguimientoConcluido)->filter(fn($s) => collect($s['seguimientos'])->contains(fn($seg) => (int)$seg->etapa === 5))->count();
                                    @endphp
                                    <div class="tab-pane" id="tab-seguimiento-concluido" role="tabpanel">
                                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-soft-primary text-primary fs-11 px-3 py-2 rounded-pill"><i class="ri-team-line me-1"></i>{{ $totalP }} participantes</span>
                                                <span class="badge bg-soft-info text-info fs-11 px-3 py-2 rounded-pill"><i class="ri-file-search-line me-1"></i>{{ $conSeguimiento }} con seguimiento</span>
                                                @if($enEtapa4 > 0)
                                                <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2 rounded-pill"><i class="ri-barcode-line me-1"></i>{{ $enEtapa4 }} en empaste</span>
                                                @endif
                                                @if($enEtapa5 > 0)
                                                <span class="badge bg-soft-success text-success fs-11 px-3 py-2 rounded-pill"><i class="ri-check-double-line me-1"></i>{{ $enEtapa5 }} finalizados</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btn-modalidad-global">
                                                <i class="ri-git-commit-line me-1"></i>Modalidad Titulación
                                            </button>
                                        </div>
                                        <div class="fase-wrap">
                                            <table class="table table-fase-concluida align-middle mb-0">
                                                <thead class="text-muted text-uppercase">
                                                    <tr>
                                                        <th class="sticky-col stk-1 text-center">#</th>
                                                        <th class="sticky-col stk-2 text-start">Participante</th>
                                                        <th style="width:110px">Aprobó Módulos</th>
                                                        <th style="width:120px">Deuda Matrícula</th>
                                                        <th style="width:120px">Deuda Colegiatura</th>
                                                        <th style="width:130px">Modalidad Titulación</th>
                                                        <th style="width:110px">Pago Tramitación</th>
                                                        <th style="width:110px">Registro UNSXX</th>
                                                        <th style="width:160px">Proceso Monografía</th>
                                                        <th style="width:200px">Etapa</th>
                                                        <th style="width:90px">Seguimiento</th>
                                                        <th style="width:150px">Documento</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($seguimientoConcluido as $idx => $sc)
                                                    @php
                                                        $pid = $sc['participante_id'];
                                                        $modalidadVal = $sc['modalidad_titulacion'];
                                                        $modalidadSinDefinir = $modalidadVal === null || $modalidadVal === '';
                                                        $noProcede = !$sc['aprobo_modulos'] || !$sc['deuda_matricula_ok'] || !$sc['deuda_colegiatura_ok'] || $modalidadSinDefinir || !$sc['pago_titulacion'];
                                                    @endphp
                                                    <tr class="{{ $noProcede ? 'fase-no-procede' : '' }} {{ $sc['es_traspaso'] ? 'traspaso-row' : '' }}">
                                                        <td class="sticky-col stk-1 text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                        <td class="sticky-col stk-2">
                                                            <div class="fw-semibold text-dark" style="font-size:.82rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }}@if($sc['es_traspaso']) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                            <div class="text-muted" style="font-size:.65rem;"><i class="ri-passport-line"></i> {{ $sc['ci'] }}</div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($sc['aprobo_modulos'])
                                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-checkbox-circle-line"></i> Aprobó</span>
                                                            @else
                                                                <button type="button" class="btn btn-link text-danger p-0 fs-10 fw-semibold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#modalModulos_{{ $pid }}">
                                                                        <i class="ri-close-circle-line"></i> No aprobó ({{ count($sc['detalle_modulos']) - collect($sc['detalle_modulos'])->where('aprobo', true)->count() }})
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($sc['deuda_matricula_ok'])
                                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                            @else
                                                                <button type="button" class="btn btn-link text-warning p-0 fs-10 fw-semibold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#modalMatricula_{{ $pid }}">
                                                                        <i class="ri-error-warning-line"></i> Pendiente
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($sc['deuda_colegiatura_ok'])
                                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                                                            @else
                                                                <button type="button" class="btn btn-link text-warning p-0 fs-10 fw-semibold text-decoration-underline" data-bs-toggle="modal" data-bs-target="#modalColegiatura_{{ $pid }}">
                                                                        <i class="ri-error-warning-line"></i> Pendiente
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $modalidadNombre = $modalidadSinDefinir ? 'Sin definir' : ((int)$modalidadVal === 0 ? 'Personal' : 'Convenio');
                                                                $modalidadClass = $modalidadSinDefinir ? 'secondary' : ((int)$modalidadVal === 0 ? 'info' : 'primary');
                                                                $puedeCambiarModalidad = !$sc['tiene_deuda_certificacion'];
                                                            @endphp
                                                            @if($puedeCambiarModalidad)
                                                                <button type="button" class="btn btn-sm btn-link p-0 border-0 btn-modalidad-single" data-participante="{{ $sc['participante_id'] }}" title="Cambiar modalidad">
                                                                    <span class="badge bg-soft-{{ $modalidadClass }} text-{{ $modalidadClass }} fs-10 px-2 py-1" style="cursor:pointer;">{{ $modalidadNombre }} <i class="ri-edit-box-line ms-1 fs-10"></i></span>
                                                                </button>
                                                            @else
                                                                <span class="badge bg-soft-{{ $modalidadClass }} text-{{ $modalidadClass }} fs-10 px-2 py-1">{{ $modalidadNombre }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($sc['pago_titulacion'])
                                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-checkbox-circle-line"></i> Pagado</span>
                                                            @else
                                                                <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-time-line"></i> Pendiente</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($sc['registrado_u'])
                                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-checkbox-circle-line"></i> Registrado</span>
                                                            @else
                                                                <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-time-line"></i> Pendiente</span>
                                                            @endif
                                                        </td>
                                                        @if($noProcede)
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-soft-danger px-2 py-1 fs-10 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPendientes_{{ $pid }}" title="Ver detalles pendientes">
                                                                <i class="ri-alert-line me-1"></i>Pendientes
                                                            </button>
                                                        </td>
                                                        <td class="text-center"><span class="text-muted fst-italic fs-10">No procede</span></td>
                                                        <td class="text-center"><span class="text-muted fst-italic fs-10">No procede</span></td>
                                                        <td class="text-center"><span class="text-muted fst-italic fs-10">No procede</span></td>
                                                        @else
                                                        @php
                                                            $etapasCompletadas = collect($sc['seguimientos'])->pluck('etapa')->unique()->sort()->values()->toArray();
                                                            $maxEtapa = count($etapasCompletadas) > 0 ? max($etapasCompletadas) : 0;
                                                            $totalEtapas = 5;
                                                            $ultimoSeg = count($sc['seguimientos']) > 0 ? $sc['seguimientos'][count($sc['seguimientos']) - 1] : null;
                                                            $coloresEtapa = [1=>'#299cdb', 2=>'#405189', 3=>'#f7b84b', 4=>'#f06548', 5=>'#0ab39c'];
                                                            $etapaColor = $coloresEtapa[$maxEtapa] ?? '#878a99';
                                                        @endphp
                                                        <td class="text-center">
                                                            @if(count($sc['seguimientos']) > 0)
                                                                <div class="d-flex align-items-center justify-content-center gap-0 mb-1">
                                                                    @for($e = 1; $e <= $totalEtapas; $e++)
                                                                        @php
                                                                            $completada = in_array($e, $etapasCompletadas);
                                                                            $esActual = $e == $maxEtapa;
                                                                        @endphp
                                                                        <span class="fase-progress-dot {{ $completada ? 'done' : 'pending' }} {{ $esActual && $completada ? 'current' : '' }}">{{ $e }}</span>
                                                                        @if($e < $totalEtapas)
                                                                            <span class="fase-progress-line {{ in_array($e, $etapasCompletadas) && in_array($e + 1, $etapasCompletadas) ? 'done' : 'pending' }}"></span>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                                <div style="font-size:.62rem;color:#878a99;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">
                                                                    @if($maxEtapa == 1) Solicitud inicio
                                                                    @elseif($maxEtapa == 2) Entrega documento
                                                                    @elseif($maxEtapa == 3) Revisión
                                                                    @elseif($maxEtapa == 4) Empaste
                                                                    @elseif($maxEtapa == 5) Documento Final
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic fs-10">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($ultimoSeg)
                                                            <span class="badge fs-10 fw-semibold px-2 py-1" style="background:{{ $etapaColor }};color:#fff;">
                                                                Etapa {{ $ultimoSeg->etapa }}: {{ $ultimoSeg->estado }}
                                                            </span>
                                                            @else
                                                            <span class="text-muted fst-italic fs-10">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            @if(count($sc['seguimientos']) > 0)
                                                                <button type="button" class="btn btn-sm btn-soft-info px-2 py-1 fs-10 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalSeguimientoFechas_{{ $pid }}" title="Ver cronograma de fechas">
                                                                    <i class="ri-calendar-line"></i> Fechas
                                                                </button>
                                                            @else
                                                                <span class="text-muted fst-italic fs-10">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($ultimoSeg)
                                                            <div style="font-size:.72rem;">
                                                                @if((int)$ultimoSeg->etapa === 4)
                                                                <span class="badge bg-soft-warning text-warning fw-semibold px-2" style="font-size:.65rem;word-break:break-all;">
                                                                    <i class="ri-barcode-line"></i> {{ $ultimoSeg->estado }}
                                                                </span>
                                                                @elseif($ultimoSeg->documento)
                                                                <span class="doc-label" title="{{ $ultimoSeg->documento }}">
                                                                    <i class="ri-file-text-line text-muted me-1"></i>{{ $ultimoSeg->documento }}
                                                                </span>
                                                                @else
                                                                <span class="text-muted">—</span>
                                                                @endif
                                                            </div>
                                                            @else
                                                            <span class="text-muted fst-italic fs-10">—</span>
                                                            @endif
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="12" class="text-center text-muted py-4 fs-14">No hay participantes inscritos</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- TAB 3: Nivelación (por módulo concluido) --}}
                                    @if($nivelacionModulos->count() > 0)
                                    <div class="tab-pane" id="tab-nivelacion" role="tabpanel">
                                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                            <span class="badge bg-soft-primary text-primary fs-11 px-3 py-2 rounded-pill"><i class="ri-bookmark-line me-1"></i>{{ $nivelacionModulos->count() }} módulo{{ $nivelacionModulos->count() !== 1 ? 's' : '' }} concluido{{ $nivelacionModulos->count() !== 1 ? 's' : '' }}</span>
                                            <span class="badge bg-soft-warning text-warning fs-11 px-3 py-2 rounded-pill"><i class="ri-refund-2-line me-1"></i>{{ $nivelacionTotalCuotas }} cuotas generadas</span>
                                            <span class="badge bg-soft-info text-info fs-11 px-3 py-2 rounded-pill"><i class="ri-money-dollar-circle-line me-1"></i>Costo/part.: Bs {{ number_format($nivelacionCostoParticipante ?: $nivelacionCostoDefault, 2) }}</span>
                                            <span class="badge bg-soft-dark text-dark fs-11 px-3 py-2 rounded-pill"><i class="ri-wallet-3-line me-1"></i>Esperado Bs {{ number_format($nivelacionTotalEsperado, 2) }} · Cobrado Bs {{ number_format($nivelacionTotalCobrado, 2) }}</span>
                                            <span class="badge bg-soft-dark text-dark fs-11 px-3 py-2 rounded-pill"><i class="ri-user-star-line me-1"></i>Honorario doc.: Bs {{ number_format($nivelacionPagoDocente ?: $nivelacionPagoDocenteDefault, 2) }}/participante</span>
                                            <span class="badge bg-soft-success text-success fs-11 px-3 py-2 rounded-pill"><i class="ri-check-double-line me-1"></i>{{ $nivelacionTotalValidados }} pago{{ $nivelacionTotalValidados !== 1 ? 's' : '' }} validado{{ $nivelacionTotalValidados !== 1 ? 's' : '' }} · Docente: Bs {{ number_format($nivelacionTotalHonorario, 2) }}</span>
                                        </div>

                                        <div class="accordion nivelacion-accordion" id="accordionNivelacion">
                                        @foreach($nivelacionModulos as $nm)
                                        @php
                                            $requiereNivelacion = (int) $nm->elegibles_count > 0;
                                            $panelNivelacionId = 'nivelacion-modulo-' . $nm->id;
                                            $cabeceraNivelacionId = 'nivelacion-cabecera-' . $nm->id;
                                        @endphp
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="{{ $cabeceraNivelacionId }}">
                                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $panelNivelacionId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $panelNivelacionId }}">
                                                    <span class="nivelacion-accordion-title"><i class="ri-book-2-line me-1"></i>{{ $nm->nombre }}</span>
                                                    <span class="nivelacion-accordion-meta">
                                                        <span class="badge bg-soft-success text-success"><i class="ri-checkbox-circle-line me-1"></i>Concluido</span>
                                                        @if($nm->fecha_conclusion)
                                                        <span class="badge bg-soft-primary text-primary"><i class="ri-calendar-check-line me-1"></i>Concluido: {{ \Carbon\Carbon::parse($nm->fecha_conclusion)->format('d/m/Y') }}</span>
                                                        @if($nm->fecha_limite)
                                                            @php $vencido = \Carbon\Carbon::parse($nm->fecha_limite)->lt(\Carbon\Carbon::today()); @endphp
                                                            <span class="badge {{ $vencido ? 'bg-soft-danger text-danger' : 'bg-soft-info text-info' }}"><i class="ri-time-line me-1"></i>Límite: {{ \Carbon\Carbon::parse($nm->fecha_limite)->format('d/m/Y') }} {{ $vencido ? '(vencido)' : '' }}</span>
                                                        @endif
                                                        @endif
                                                        @if($requiereNivelacion)
                                                        <span class="badge bg-soft-warning text-warning"><i class="ri-user-follow-line me-1"></i>{{ $nm->elegibles_count }} estudiante{{ $nm->elegibles_count !== 1 ? 's' : '' }} para nivelación</span>
                                                        <span class="badge bg-soft-secondary text-secondary"><i class="ri-bank-card-line me-1"></i>{{ $nm->cuotas_count }} cuota{{ $nm->cuotas_count !== 1 ? 's' : '' }}</span>
                                                        @if($nm->docente)
                                                        <span class="badge bg-soft-dark text-dark"><i class="ri-user-star-line me-1"></i>Docente: {{ $nm->docente->apellidos }}, {{ $nm->docente->nombres }}</span>
                                                        @endif
                                                        @else
                                                        <span class="badge bg-soft-success text-success"><i class="ri-checkbox-circle-line me-1"></i>No corresponde nivelación</span>
                                                        @endif
                                                    </span>
                                                </button>
                                            </h2>
                                            <div id="{{ $panelNivelacionId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $cabeceraNivelacionId }}" data-bs-parent="#accordionNivelacion">
                                                <div class="accordion-body">
                                                    @if($nm->fecha_conclusion && $nm->fecha_limite)
                                                    @php $vencidoBody = \Carbon\Carbon::parse($nm->fecha_limite)->lt(\Carbon\Carbon::today()); @endphp
                                                    <div class="alert {{ $vencidoBody ? 'alert-danger' : 'alert-info' }} py-2 px-3 mb-3 d-flex align-items-center gap-2" style="border:0;">
                                                        <i class="ri-calendar-check-line fs-15"></i>
                                                        <div class="fs-12">
                                                            <strong>Plazo de nivelación:</strong> módulo concluido el
                                                            <b>{{ \Carbon\Carbon::parse($nm->fecha_conclusion)->format('d/m/Y') }}</b>
                                                            · Fecha límite para nivelar:
                                                            <b>{{ \Carbon\Carbon::parse($nm->fecha_limite)->format('d/m/Y') }}</b>
                                                            (2 meses después de la conclusión){{ $vencidoBody ? ' · <b class="text-danger">Plazo vencido</b>' : '' }}.
                                                        </div>
                                                    </div>
                                                    @endif
<div class="nivelacion-actions">
                                                        @if($puedeGestionarModulo)
                                                        <button type="button" class="btn btn-sm btn-soft-warning d-flex align-items-center gap-1 btn-configurar-nivelacion" data-modulo-id="{{ $nm->id }}" data-modulo-nombre="{{ $nm->nombre }}">
                                                            <i class="ri-settings-4-line"></i> Configurar nivelación
                                                        </button>
                                                        @endif
                                                        @if($puedeGestionarModulo)
                                                        <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 btn-agregar-horario-nivelacion" data-modulo-id="{{ $nm->id }}" data-modulo-nombre="{{ $nm->nombre }}">
                                                            <i class="ri-calendar-event-line"></i> Agregar sesión de nivelación
                                                        </button>
                                                        @endif
                                                        @if((int) $nm->validados_count > 0)
                                                            @if($puedeGestionarModulo)
                                                            <button type="button" class="btn btn-sm btn-warning d-flex align-items-center gap-1 btn-notas-nivelacion" data-modulo-id="{{ $nm->id }}" data-modulo-nombre="{{ $nm->nombre }}">
                                                                <i class="ri-pencil-line"></i> Registrar notas nivelación
                                                            </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    @if(!$requiereNivelacion)
                                                    <div class="nivelacion-no-corresponde mb-3">
                                                        <i class="ri-checkbox-circle-line"></i>
                                                        <div>
                                                            <strong>No corresponde nivelación para este módulo.</strong>
                                                            <span>Todos los estudiantes activos aprobaron la fase formativa.</span>
                                                        </div>
                                                    </div>
                                                    @else
                                                <div class="row g-3 mb-3">
                                                    <div class="col-6 col-lg-3">
                                                        <div class="nivelacion-stat">
                                                            <div class="nivelacion-stat-icon bg-soft-primary text-primary"><i class="ri-user-follow-line"></i></div>
                                                            <div>
                                                                <div class="nivelacion-stat-label">Reprobados</div>
                                                                <div class="nivelacion-stat-value">{{ $nm->elegibles_count }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="nivelacion-stat">
                                                            <div class="nivelacion-stat-icon bg-soft-warning text-warning"><i class="ri-refund-2-line"></i></div>
                                                            <div>
                                                                <div class="nivelacion-stat-label">Cuotas</div>
                                                                <div class="nivelacion-stat-value">{{ $nm->cuotas_count }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="nivelacion-stat">
                                                            <div class="nivelacion-stat-icon bg-soft-primary text-primary"><i class="ri-wallet-3-line"></i></div>
                                                            <div>
                                                                <div class="nivelacion-stat-label">Esperado</div>
                                                                <div class="nivelacion-stat-value">Bs {{ number_format($nm->total_esperado, 2) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="nivelacion-stat">
                                                            <div class="nivelacion-stat-icon bg-soft-success text-success"><i class="ri-check-double-line"></i></div>
                                                            <div>
                                                                <div class="nivelacion-stat-label">Cobrado</div>
                                                                <div class="nivelacion-stat-value">Bs {{ number_format($nm->total_cobrado, 2) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="nivelacion-stat">
                                                            <div class="nivelacion-stat-icon bg-soft-success text-success"><i class="ri-check-double-line"></i></div>
                                                            <div>
                                                                <div class="nivelacion-stat-label">Pago docente validado</div>
                                                                <div class="nivelacion-stat-value">Bs {{ number_format($nm->total_honorario, 2) }}</div>
                                                                <div class="text-muted fs-11">{{ $nm->validados_count }} participante{{ $nm->validados_count !== 1 ? 's' : '' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="table-responsive table-card mb-3">
                                                    <table class="table table-borderless align-middle mb-0">
                                                        <thead class="table-light text-muted text-uppercase">
                                                            <tr>
                                                                <th style="width:40px;" class="text-center">#</th>
                                                                <th>Participante</th>
                                                                <th class="text-center" style="width:90px;">Nota módulo</th>
                                                                <th class="text-center">Nota niv.</th>
                                                                <th class="text-center" style="width:100px;">Cuota</th>
                                                                <th class="text-center" style="width:120px;">Estado</th>
                                                                <th class="text-center" style="width:170px;">Comprobante</th>
                                                                <th class="text-center" style="width:110px;">Pago docente</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($nm->rows as $r)
                                                            @include('admin.programas.partials.nivelacion_fila', ['r' => $r, 'idx' => $loop->iteration])
                                                            @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center text-muted py-4 fs-14">No hay participantes reprobados en este módulo</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="card mb-0 section-card">
                                                    <div class="card-header">
                                                        <h5 class="card-title fs-13 fw-semibold mb-0"><i class="ri-calendar-line align-bottom me-1"></i> Sesiones de nivelación</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @forelse($nm->horarios as $hn)
                                                        <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                                                            <span class="nivelacion-horario-dot"></span>
                                                            <div class="flex-grow-1">
                                                                <div class="text-muted fs-12">
                                                                    <i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($hn->fecha_desarrollo)->format('d/m/Y') }}
                                                                    · {{ $hn->hora_inicio }} - {{ $hn->hora_fin }}
                                                                </div>
                                                            </div>
                                                            <span class="text-muted fs-12">
                                                                @if($hn->asignado_nombres)
                                                                <i class="ri-user-star-line"></i> {{ $hn->asignado_apellidos }}
                                                                @else
                                                                —
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @empty
                                                        <div class="text-center text-muted py-4">
                                                            <i class="ri-calendar-event-line fs-3 mb-2 d-block"></i>
                                                            No hay sesiones de nivelación registradas
                                                        </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('programas.tab.desarrollo')
                    {{-- TAB DESARROLLO ACADÉMICO --}}
                    <div class="tab-pane" id="tab-desarrollo" role="tabpanel">
                        @if($todosModulosConcluidos)
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('admin.programas.notas-pdf', $programa->id) }}" class="btn btn-sm btn-soft-danger d-inline-flex align-items-center gap-1" target="_blank">
                                <i class="ri-file-pdf-line"></i> Reporte General de Notas PDF
                            </a>
                        </div>
                        @endif
                        @php
                            // Excluir bajas tempranas
                            $participantesDesarrollo = $participantesDesarrollo->filter(fn($p) => !((int)$p->estado === 0 && !(int)($p->baja_operativa ?? 0) && !(int)($p->baja_academica ?? 0)));
                            // Total de columnas dinámicas (cada módulo = 1 columna con la nota efectiva)
                            $totalColModulos = 0;
                            $modulosConcluidos = 0;
                            $esPrimerMod = true;
                            foreach ($modulosDesarrollo as $mod) {
                                $estaConcluido = ($mod->desarrollo ?? 'Pendiente') === 'Concluido';
                                $esModCero = ($programa->modulo_cero ?? 0) == 1 && $esPrimerMod;
                                if (!$esModCero) {
                                    $totalColModulos++;
                                }
                                if ($estaConcluido && !$esModCero) $modulosConcluidos++;
                                $esPrimerMod = false;
                            }
                            // 7 fijas (#, Estado, Moodle, Acad., CI, Apellidos, Celular) + módulos + 3 resumen (aprobados, promedio, estado)
                            $colspanTotal = 7 + $totalColModulos + 3;
                        @endphp
                        <div class="table-desarrollo-wrap">
                            <table class="table table-bordered table-centered align-middle table-nowrap mb-0 table-hover table-desarrollo">
                                <thead class="table-light text-muted text-uppercase fs-11">
                                    <tr>
                                        <th class="sticky-col sticky-col-1 text-center" style="width:3%">#</th>
                                        <th class="sticky-col sticky-col-2 text-center" style="width:60px;">Estado</th>
                                        <th class="sticky-col text-center" style="width:50px;">Moodle</th>
                                        <th class="sticky-col text-center" style="width:55px;">Acad.</th>
                                        <th class="sticky-col sticky-col-3" style="width:10%">CI</th>
                                        <th class="sticky-col sticky-col-4" style="width:18%">Apellidos, Nombres</th>
                                        <th style="width:12%">Celular</th>
                                        @foreach($modulosDesarrollo as $mod)
                                        @php $esModCero = ($programa->modulo_cero ?? 0) == 1 && $loop->first; @endphp
                                        @if(!$esModCero)
                                        @php
                                            $modBg = $loop->index % 2 === 0 ? 'mod-group-0' : 'mod-group-1';
                                            $modDesarrollo = $mod->desarrollo ?? 'Pendiente';
                                            $esConcluido = $modDesarrollo === 'Concluido';
                                            $enm = $estadoNotasPorModulo->get($mod->id);
                                            $tieneNivelacion = $esConcluido && $enm && (int)$enm->con_nota_nivelacion > 0;
                                            $tieneNivPago = $esConcluido && $notasDesarrollo->contains(function($item) use($mod) {
                                                return $item->modulo_id == $mod->id && $item->nota_nivelacion_pago !== null && (float)$item->nota_nivelacion_pago > 0;
                                            });
                                        @endphp
                                        <th class="text-center mod-col {{ $modBg }}">
                                            <span class="d-block fs-9 fw-semibold">{{ $mod->nombre }}</span>
                                            <span class="d-inline-block fs-10 text-muted fw-normal mt-1">
                                                @if($esConcluido)
                                                    <span class="text-primary">Mod</span>
                                                    @if($tieneNivelacion) / <span class="text-warning">Niv</span>@endif
                                                    @if($tieneNivPago) / <span class="text-info">NivP</span>@endif
                                                @else
                                                    <span>{{ $modDesarrollo }}</span>
                                                @endif
                                            </span>
                                        </th>
                                        @endif
                                        @endforeach
                                        {{-- Columnas resumen --}}
                                        <th class="text-center bg-light" style="width:90px; border-left: 2px solid #dee2e6;">
                                            <span class="d-block fs-10 fw-semibold text-primary">APROBADOS</span>
                                            <span class="d-block fs-9 text-muted fw-normal">de {{ $modulosConcluidos }}</span>
                                        </th>
                                        <th class="text-center bg-light" style="width:90px;">
                                            <span class="d-block fs-10 fw-semibold text-primary">PROMEDIO</span>
                                            <span class="d-block fs-9 text-muted fw-normal">final</span>
                                        </th>
                                        <th class="text-center bg-light" style="width:110px;">
                                            <span class="d-block fs-10 fw-semibold text-primary">ESTADO</span>
                                            <span class="d-block fs-9 text-muted fw-normal">final</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participantesDesarrollo as $i => $p)
                                    @php
                                        $pId = $p->id;
                                        // Acumuladores para columnas resumen
                                        $sumNotas = 0;
                                        $countNotas = 0;
                                        $aprobados = 0;
                                        $reprobados = 0;
                                    @endphp
                                    @php
                                        $esRetirado = isset($p->estado) && (int)$p->estado === 0;
                                        $esBajaOperativa = isset($p->baja_operativa) && (int)$p->baja_operativa === 1;
                                        $esBajaAcademica = isset($p->baja_academica) && (int)$p->baja_academica === 1;
                                        $esBajaContable = isset($p->baja_contable) && (int)$p->baja_contable === 1;
                                        $moodleHabilitado = isset($p->plataforma_bloqueado) && (int)$p->plataforma_bloqueado === 0;
                                        $academicoHabilitado = isset($p->baja_academica) && (int)$p->baja_academica === 0;
                                        $bgBaja = $esBajaAcademica ? 'rgba(247,184,75,.15)' : ($esBajaContable ? 'rgba(41,156,219,.12)' : '');
                                    @endphp
                                    <tr class="{{ ($p->es_traspaso ?? 0) == 1 ? 'traspaso-row' : '' }}" @if($esBajaOperativa) style="background: rgba(255,193,7,0.15) !important;" @elseif($esRetirado) style="background: rgba(220,53,69,0.08) !important;" @endif>
                                        <td class="sticky-col sticky-col-1 text-center text-muted fw-semibold" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>{{ $loop->iteration }}</td>
                                        <td class="sticky-col sticky-col-2 text-center" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>
                                            @if($esRetirado)
                                                <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Retirado</span>
                                            @elseif($esBajaAcademica)
                                                <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1"><i class="ri-book-close-line"></i> Baja Acad.</span>
                                            @elseif($esBajaContable)
                                                <span class="badge bg-soft-info text-info fs-10 px-2 py-1"><i class="ri-money-dollar-circle-line"></i> Baja Cont.</span>
                                            @else
                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
                                            @endif
                                        </td>
                                        <td class="sticky-col text-center fw-semibold" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>
                                            @if($moodleHabilitado)
                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-check-line"></i></span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1"><i class="ri-close-line"></i></span>
                                            @endif
                                        </td>
                                        <td class="sticky-col text-center fw-semibold" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>
                                            @if($esBajaAcademica)
                                                <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1" title="Baja Académica"><i class="ri-book-close-line"></i> Baja</span>
                                            @elseif($academicoHabilitado)
                                                <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-check-line"></i></span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1"><i class="ri-close-line"></i></span>
                                            @endif
                                        </td>
                                        <td class="sticky-col sticky-col-3 fw-medium text-dark" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>{{ $p->ci }}</td>
                                        <td class="sticky-col sticky-col-4 fw-medium text-dark" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>{{ $p->apellidos }}, {{ $p->nombres }}@if(($p->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</td>
                                        <td class="fw-medium" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>
                                            @if($p->celular)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->celular) }}" target="_blank" class="text-success d-inline-flex align-items-center gap-1" title="Enviar WhatsApp">
                                                    <i class="ri-whatsapp-line"></i> {{ $p->celular }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        @foreach($modulosDesarrollo as $mod)
                                        @php $esModCero = ($programa->modulo_cero ?? 0) == 1 && $loop->first; @endphp
                                        @if(!$esModCero)
                                        @php
                                            $modBg = $loop->index % 2 === 0 ? 'mod-group-0' : 'mod-group-1';
                                            $modDesarrollo = $mod->desarrollo ?? 'Pendiente';
                                            $esConcluido = $modDesarrollo === 'Concluido';
                                            $enm = $estadoNotasPorModulo->get($mod->id);
                                            $tieneNivelacion = $esConcluido && $enm && (int)$enm->con_nota_nivelacion > 0;
                                            $key = $pId . '_' . $mod->id;
                                            $nota = $notasDesarrollo->get($key);
                                            $nm = $nota ? (float) $nota->nota_modulo : null;
                                            $nn = $nota ? (float) $nota->nota_nivelacion : null;
                                            $nnp = $nota ? (float) $nota->nota_nivelacion_pago : null;
                                            // Nota efectiva: la última instancia que reemplaza
                                            $necesitaNiv = $nm !== null && $nm < 71;
                                            $tieneNiv = $nn !== null && $nn > 0;
                                            $tieneNivPago = $nnp !== null && $nnp > 0;
                                            $notaEfectiva = null;
                                            if ($necesitaNiv && $tieneNivPago) {
                                                $notaEfectiva = $nnp;
                                            } elseif ($necesitaNiv && $tieneNiv) {
                                                $notaEfectiva = $nn;
                                            } elseif ($nm !== null) {
                                                $notaEfectiva = $nm;
                                            }
                                            // Color para nota aprobada (>=71)
                                            $bgEf = $notaEfectiva !== null ? ($notaEfectiva >= 71 ? 'bg-soft-secondary' : 'bg-soft-danger') : '';
                                            $txtEf = $notaEfectiva !== null ? ($notaEfectiva >= 71 ? 'text-dark' : 'text-danger') : 'text-muted';

                                            // Acumular para resumen: solo módulos concluidos no-cero cuentan
                                            if ($esConcluido) {
                                                // Nota final: la 2da instancia reemplaza a la del módulo
                                                $notaFinal = $notaEfectiva;
                                                if ($notaFinal !== null) {
                                                    $sumNotas += $notaFinal;
                                                    $countNotas++;
                                                    if ($notaFinal >= 71) $aprobados++; else $reprobados++;
                                                }
                                            }
                                        @endphp
                                        @if($esConcluido)
                                        <td class="text-center fw-semibold mod-cell {{ $modBg }}" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>
                                            @if($necesitaNiv)
                                                <div class="cell-grades-seq">
                                                    <span class="grade-seq grade-mod">{{ number_format($nm, 2) }}</span>
                                                    @if($tieneNiv)
                                                        <span class="grade-seq grade-niv {{ $nn >= 71 ? 'niv-ok' : 'niv-fail' }}">{{ number_format($nn, 2) }}</span>
                                                    @endif
                                                    @if($tieneNivPago)
                                                        <span class="grade-seq grade-nivp {{ $nnp >= 71 ? 'nivp-ok' : 'nivp-fail' }}">{{ number_format($nnp, 2) }}</span>
                                                    @endif
                                                </div>
                                            @elseif($notaEfectiva !== null)
                                                <span class="{{ $bgEf }} {{ $txtEf }}" style="padding:.25em .55em;border-radius:999px;font-size:.8rem;">{{ number_format($notaEfectiva, 2) }}</span>
                                            @endif
                                        </td>
                                        @else
                                        <td class="text-center mod-cell {{ $modBg }}" @if($bgBaja) style="background:{{ $bgBaja }} !important;" @endif>&nbsp;</td>
                                        @endif
                                        @endif
                                        @endforeach

                                        {{-- Resumen del participante --}}
                                        @php
                                            $promedio = $countNotas > 0 ? $sumNotas / $countNotas : null;
                                            $todosConcluidos = $modulosConcluidos > 0 && $countNotas >= $modulosConcluidos;
                                            if ($promedio === null) {
                                                $estadoFinal = 'Sin notas';
                                                $estadoColor = '#878a99';
                                            } elseif (!$todosConcluidos) {
                                                $estadoFinal = 'En curso';
                                                $estadoColor = '#299cdb';
                                            } elseif ($reprobados === 0 && round($promedio) >= 71) {
                                                $estadoFinal = 'Aprobado';
                                                $estadoColor = '#0ab39c';
                                            } else {
                                                $estadoFinal = 'Reprobado';
                                                $estadoColor = '#f06548';
                                            }
                                        @endphp
                                        <td class="text-center fw-bold" style="{{ $bgBaja ? 'background:'.$bgBaja.' !important;border-left: 2px solid #dee2e6;' : 'border-left: 2px solid #dee2e6; background: #fafbfd;' }}">
                                            @if($modulosConcluidos > 0)
                                                <span style="color: {{ $aprobados === $modulosConcluidos ? '#0ab39c' : ($aprobados > 0 ? '#f7b84b' : '#f06548') }};">
                                                    {{ $aprobados }}/{{ $modulosConcluidos }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold" style="{{ $bgBaja ? 'background:'.$bgBaja.' !important;' : 'background: #fafbfd;' }}">
                                            @if($promedio !== null)
                                                <span style="color: {{ round($promedio) >= 71 ? '#0ab39c' : '#f06548' }}; font-size: .95rem;">{{ round($promedio) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center" style="{{ $bgBaja ? 'background:'.$bgBaja.' !important;' : 'background: #fafbfd;' }}">
                                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:.7rem; font-weight:700; padding:.3em .65em; border-radius:999px; border:1px solid {{ $estadoColor }}; color:{{ $estadoColor }};">
                                                <span style="width:5px; height:5px; border-radius:50%; background:{{ $estadoColor }};"></span>
                                                {{ $estadoFinal }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ $colspanTotal }}" class="text-center text-muted py-4 fs-14">No hay participantes inscritos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                             </table>
                        </div> <!-- /table-desarrollo-wrap -->
                    </div> <!-- tab-pane tab-desarrollo -->
                    @endcan
                    @can('programas.tab.contable')
                    {{-- ============ TAB SEGUIMIENTO CONTABLE ============ --}}
                    <div class="tab-pane" id="tab-contable" role="tabpanel">
                        {{-- Sub-tabs --}}
                        <ul class="nav nav-tabs nav-tabs-show mt-3 mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#sub-tab-pagos-mat-col" role="tab">
                                    <i class="ri-bank-card-line"></i> Pagos Matrículas y Colegiatura
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sub-tab-pagos-cert" role="tab">
                                    <i class="ri-award-line"></i> Pagos Certificación
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- ===== Sub-tab: Pagos Matrículas y Colegiatura ===== --}}
                            <div class="tab-pane active show" id="sub-tab-pagos-mat-col" role="tabpanel">
                                @php
                                    $totalParticipantesC = count($inscripciones);
                                    $totMatBruto = 0; $totMatDesc = 0; $totMatNeto = 0; $totMatPagado = 0;
                                    $totColBruto = 0; $totColDesc = 0; $totColNeto = 0; $totColPagado = 0;

                                    foreach ($inscripciones as $insC) {
                                        $totMatBruto += (float) $programa->matricula;
                                        $totMatDesc += (float) ($insC->descuento_matricula ?? 0);
                                        $totColBruto += (float) $programa->colegiatura;
                                        $totColDesc += (float) ($insC->descuento_colegiatura ?? 0);
                                        $pMatC = $pagoMatriculaPorParticipante->get($insC->participante_id);
                                        $pColC = $pagoColegiaturaPorParticipante->get($insC->participante_id);
                                        $totMatPagado += $pMatC ? (float) $pMatC->pagado : 0;
                                        $totColPagado += $pColC ? (float) $pColC->pagado : 0;
                                    }
                                    $totMatNeto = max(0, $totMatBruto - $totMatDesc);
                                    $totColNeto = max(0, $totColBruto - $totColDesc);
                                    $totMatSaldo = max(0, $totMatNeto - $totMatPagado);
                                    $totColSaldo = max(0, $totColNeto - $totColPagado);
                                    $totGenDeuda = $totMatNeto + $totColNeto;
                                    $totGenPagado = $totMatPagado + $totColPagado;
                                    $totGenSaldo = $totMatSaldo + $totColSaldo;
                                    $pctCobrado = $totGenDeuda > 0 ? round(($totGenPagado / $totGenDeuda) * 100) : 0;
                                @endphp

                                {{-- Header --}}
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="avatar-xs"><span class="avatar-title bg-soft-success text-success rounded-circle fs-16"><i class="ri-money-dollar-circle-line"></i></span></span>
                                    <div>
                                        <h6 class="mb-0 fs-14 fw-semibold">Pagos Matrículas y Colegiatura</h6>
                                        <small class="text-muted" style="font-size:.72rem;">Detalle de deudas, descuentos y pagos por participante y módulo · {{ $totalParticipantesC }} participantes · {{ $modulos->count() }} módulos</small>
                                    </div>
                                </div>

                                {{-- Resumen agregado: Matrícula / Colegiatura / Total --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="cont-summary-card cont-card-mat">
                                            <div class="cont-card-header">
                                                <span class="cont-card-icon" style="background:#dbeafe; color:#299cdb;"><i class="ri-bank-card-line"></i></span>
                                                <div>
                                                    <div class="cont-card-title">Matrícula</div>
                                                    <div class="cont-card-sub">Bruto Bs {{ number_format($totMatBruto, 2) }} @if($totMatDesc > 0) · Dto Bs {{ number_format($totMatDesc, 2) }}@endif</div>
                                                </div>
                                            </div>
                                            <div class="cont-card-rows">
                                                <div class="cont-row"><span><i class="ri-error-warning-line"></i> Deuda neta</span> <strong class="text-primary">Bs {{ number_format($totMatNeto, 2) }}</strong></div>
                                                <div class="cont-row"><span><i class="ri-check-line"></i> Cobrado</span> <strong class="text-success">Bs {{ number_format($totMatPagado, 2) }}</strong></div>
                                                <div class="cont-row saldo-row"><span><i class="ri-{{ $totMatSaldo > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                                                    <strong class="{{ $totMatSaldo > 0 ? 'text-danger' : 'text-success' }}">Bs {{ number_format($totMatSaldo, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="cont-summary-card cont-card-col">
                                            <div class="cont-card-header">
                                                <span class="cont-card-icon" style="background:#d1fae5; color:#0ab39c;"><i class="ri-funds-line"></i></span>
                                                <div>
                                                    <div class="cont-card-title">Colegiatura</div>
                                                    <div class="cont-card-sub">Bruto Bs {{ number_format($totColBruto, 2) }} @if($totColDesc > 0) · Dto Bs {{ number_format($totColDesc, 2) }}@endif</div>
                                                </div>
                                            </div>
                                            <div class="cont-card-rows">
                                                <div class="cont-row"><span><i class="ri-error-warning-line"></i> Deuda neta</span> <strong class="text-info">Bs {{ number_format($totColNeto, 2) }}</strong></div>
                                                <div class="cont-row"><span><i class="ri-check-line"></i> Cobrado</span> <strong class="text-success">Bs {{ number_format($totColPagado, 2) }}</strong></div>
                                                <div class="cont-row saldo-row"><span><i class="ri-{{ $totColSaldo > 0 ? 'time-line' : 'checkbox-circle-line' }}"></i> Saldo</span>
                                                    <strong class="{{ $totColSaldo > 0 ? 'text-danger' : 'text-success' }}">Bs {{ number_format($totColSaldo, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="cont-summary-card cont-card-total">
                                            <div class="cont-card-header">
                                                <span class="cont-card-icon" style="background:rgba(255,255,255,.25); color:#fff;"><i class="ri-coins-line"></i></span>
                                                <div>
                                                    <div class="cont-card-title" style="color:#fff;">Total General</div>
                                                    <div class="cont-card-sub" style="color:rgba(255,255,255,.85);">{{ $pctCobrado }}% cobrado del total</div>
                                                </div>
                                            </div>
                                            <div class="cont-card-rows">
                                                <div class="cont-row" style="color:rgba(255,255,255,.9);"><span><i class="ri-error-warning-line"></i> Deuda total</span> <strong style="color:#fff;">Bs {{ number_format($totGenDeuda, 2) }}</strong></div>
                                                <div class="cont-row" style="color:rgba(255,255,255,.9);"><span><i class="ri-check-line"></i> Cobrado</span> <strong style="color:#fff;">Bs {{ number_format($totGenPagado, 2) }}</strong></div>
                                                <div class="cont-row saldo-row" style="color:rgba(255,255,255,.9); border-top-color:rgba(255,255,255,.2);"><span><i class="ri-time-line"></i> Saldo</span>
                                                    <strong style="color:#fff; background: {{ $totGenSaldo > 0 ? 'rgba(255,255,255,.2)' : 'rgba(255,255,255,.2)' }}; padding:.2em .6em; border-radius:6px;">Bs {{ number_format($totGenSaldo, 2) }}</strong>
                                                </div>
                                            </div>
                                            <div class="cont-progress mt-2">
                                                <div class="cont-progress-fill" style="width: {{ $pctCobrado }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($totalParticipantesC === 0)
                                    <div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>No hay participantes inscritos en este programa</div>
                                @else
                                <div class="contable-wrap">
                                    <table class="table table-bordered align-middle mb-0 table-contable">
                                        <thead>
                                            <tr>
                                                <th class="sticky-col stk-1">#</th>
                                                <th class="sticky-col stk-2" style="text-align:left !important;">Participante</th>
                                                <th class="sticky-col stk-3" title="Deuda neta de matrícula (bruto − descuento)"><i class="ri-bank-card-line text-primary"></i> Deuda Matrícula</th>
                                                <th class="sticky-col stk-4" title="Deuda neta de colegiatura (bruto − descuento)"><i class="ri-funds-line text-info"></i> Deuda Colegiatura</th>
                                                @foreach($modulos as $mod)
                                                    <th class="col-mod col-mod-header" title="{{ $mod->nombre }}">{{ $mod->nombre }}</th>
                                                @endforeach
                                                @for($ei = 0; $ei < ($maxExtras ?? 0); $ei++)
                                                    <th class="col-mod col-mod-header" style="background:#fef3c7 !important; color:#92400e;" title="Colegiatura extra {{ $ei+1 }}"><i class="ri-add-circle-line"></i> Extra {{ $ei+1 }}</th>
                                                @endfor
                                                 <th class="total-deuda-col"><i class="ri-error-warning-line text-danger"></i> Deuda Pend.</th>
                                                   <th class="text-center" style="min-width:80px; background:#f3f5f9 !important; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px;"><i class="ri-calendar-check-line"></i> Fechas Pago</th>
                                                   <th class="text-center" style="min-width:60px; background:#f3f5f9 !important; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px;"><i class="ri-settings-3-line"></i> Plan</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscripciones as $idxIns => $insC)
                                            @php
                                                $iniC = strtoupper(substr($insC->nombres, 0, 1) . substr($insC->apellidos, 0, 1));
                                                $deudaMatIns = max(0, (float) $programa->matricula - (float) ($insC->descuento_matricula ?? 0));
                                                $deudaColIns = max(0, (float) $programa->colegiatura - (float) ($insC->descuento_colegiatura ?? 0));
                                                $pagadoMatP = 0;
                                                $pagadoColP = 0;
                                                foreach ($modulos as $modC) {
                                                    $kC = $insC->participante_id . '_' . $modC->id;
                                                    $cC = $contableMatrix[$kC] ?? null;
                                                    if ($cC) {
                                                        foreach ($cC['cuotas'] as $cuota) {
                                                            if ($cuota['tipo'] === 'Matricula') {
                                                                $pagadoMatP += (float) $cuota['total_pagado'];
                                                            } elseif ($cuota['tipo'] === 'Colegiatura') {
                                                                $pagadoColP += (float) $cuota['total_pagado'];
                                                            }
                                                        }
                                                    }
                                                }
                                                $pagadoTotalP = $pagadoMatP + $pagadoColP;
                                                $matPend = max(0, $deudaMatIns - $pagadoMatP);
                                                $colPend = max(0, $deudaColIns - $pagadoColP);
                                                $deudaPendiente = $matPend + $colPend;
                                                $esBajaTemprana = (int)$insC->estado === 0 && !(int)($insC->baja_operativa ?? 0) && !(int)($insC->baja_academica ?? 0);
                                                $esBajaOperativa = (int)($insC->baja_operativa ?? 0);
                                                $esPagadoTotal = $deudaPendiente <= 0;
                                            @endphp
                                            <tr class="{{ ($insC->es_traspaso ?? 0) == 1 ? 'traspaso-row' : '' }}" style="{{ $esBajaTemprana ? 'background: rgba(220,53,69,0.12) !important;' : ($esBajaOperativa ? 'background: rgba(255,193,7,0.15) !important;' : ($esPagadoTotal ? 'background: rgba(10,179,156,0.08) !important;' : '')) }}">
                                                <td class="sticky-col stk-1 text-center text-muted fw-bold">{{ $idxIns + 1 }}</td>
                                                <td class="sticky-col stk-2 {{ $esPagadoTotal ? 'bg-success-subtle' : '' }}">
                                                    <div class="participante-cell">
                                                        <div class="pc-avatar">{{ $iniC }}</div>
                                                        <div class="pc-info">
                                                            <div class="pc-name"><a href="{{ route('admin.programas.participante-detalle', ['participante' => $insC->participante_id]) }}" class="text-reset text-decoration-none">{{ $insC->apellidos }}, {{ $insC->nombres }}</a>@if(($insC->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                                                            <div class="pc-ci"><i class="ri-passport-line"></i> {{ $insC->ci }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="sticky-col stk-3 cost-cell cost-mat {{ $esPagadoTotal ? 'bg-success-subtle' : '' }}" title="Deuda real (Bruto Bs {{ number_format($programa->matricula, 2) }} − Dto Bs {{ number_format($insC->descuento_matricula ?? 0, 2) }})">
                                                    Bs {{ number_format($deudaMatIns, 2) }}
                                                    @if(($insC->descuento_matricula ?? 0) > 0)
                                                        <div style="font-size:.6rem; color:#878a99; font-weight:500; margin-top:1px;">
                                                            <span style="text-decoration:line-through;">Bs {{ number_format($programa->matricula, 2) }}</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="sticky-col stk-4 cost-cell cost-col {{ $esPagadoTotal ? 'bg-success-subtle' : '' }}" title="Deuda real (Bruto Bs {{ number_format($programa->colegiatura, 2) }} − Dto Bs {{ number_format($insC->descuento_colegiatura ?? 0, 2) }})">
                                                    Bs {{ number_format($deudaColIns, 2) }}
                                                    @if(($insC->descuento_colegiatura ?? 0) > 0)
                                                        <div style="font-size:.6rem; color:#878a99; font-weight:500; margin-top:1px;">
                                                            <span style="text-decoration:line-through;">Bs {{ number_format($programa->colegiatura, 2) }}</span>
                                                        </div>
                                                    @endif
                                                </td>

                                                @foreach($modulos as $modC)
                                                @php
                                                    $kC = $insC->participante_id . '_' . $modC->id;
                                                    $cC = $contableMatrix[$kC] ?? null;
                                                @endphp
                                                <td class="col-mod">
                                                    @if($cC)
                                                        @php
                                                            $saldoC = max(0, $cC['deuda'] - $cC['descuento'] - $cC['total_pagado']);
                                                            $estadoSaldo = 'deuda';
                                                            if ($saldoC === 0.0 || $saldoC == 0) {
                                                                $estadoSaldo = $cC['deuda'] > 0 ? 'pagado' : 'pagado';
                                                            } elseif ($cC['total_pagado'] > 0) {
                                                                $estadoSaldo = 'parcial';
                                                            }
                                                            $agrupadas = [];
                                                            foreach ($cC['cuotas'] as $cuota) {
                                                                $t = $cuota['tipo'];
                                                                if (!isset($agrupadas[$t])) {
                                                                    $agrupadas[$t] = ['deuda' => 0, 'descuento' => 0, 'total_pagado' => 0];
                                                                }
                                                                $agrupadas[$t]['deuda'] += $cuota['deuda'];
                                                                $agrupadas[$t]['descuento'] += $cuota['descuento'];
                                                                $agrupadas[$t]['total_pagado'] += $cuota['total_pagado'];
                                                            }
                                                        @endphp
                                                        <div class="cell-mod">
                                                            @foreach($agrupadas as $tipo => $vals)
                                                                <div class="text-center mb-1">
                                                                    <span class="badge {{ $tipo === 'Matricula' ? 'bg-soft-primary text-primary' : 'bg-soft-info text-info' }}" style="font-size:.6rem; padding:.15em .55em;">
                                                                        <i class="ri-{{ $tipo === 'Matricula' ? 'bank-card-line' : 'funds-line' }}"></i> {{ $tipo === 'Matricula' ? 'Matrícula' : 'Colegiatura' }}
                                                                    </span>
                                                                </div>
                                                                <div class="row-line deuda-row"><span class="lbl"><i class="ri-error-warning-line"></i> Deuda</span> <span class="val">Bs {{ number_format($vals['deuda'], 2) }}</span></div>
                                                                @if($vals['descuento'] > 0)
                                                                    <div class="row-line dto-row"><span class="lbl"><i class="ri-discount-percent-line"></i> Dto.</span> <span class="val">Bs {{ number_format($vals['descuento'], 2) }}</span></div>
                                                                @endif
                                                                <div class="row-line pagado-row"><span class="lbl"><i class="ri-check-line"></i> Pagado</span> <span class="val">Bs {{ number_format($vals['total_pagado'], 2) }}</span></div>
                                                            @endforeach
                                                            <div class="saldo-line">
                                                                <span class="saldo-val {{ $estadoSaldo }}">
                                                                    @if($estadoSaldo === 'pagado')<i class="ri-checkbox-circle-line"></i> Cancelado
                                                                    @elseif($estadoSaldo === 'parcial')<i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoC, 2) }}
                                                                    @else<i class="ri-error-warning-line"></i> Bs {{ number_format($saldoC, 2) }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="cell-empty">—</span>
                                                    @endif
                                                </td>
                                                @endforeach

                                                @for($ei = 0; $ei < ($maxExtras ?? 0); $ei++)
                                                @php
                                                    $extraData = ($extrasMatrix[$insC->participante_id] ?? [])[$ei] ?? null;
                                                    $matCuota = $extraData['matricula'] ?? null;
                                                @endphp
                                                <td class="col-mod" style="background: #fffbeb;">
                                                    @if($extraData)
                                                    @php
                                                        $saldoExtra = max(0, $extraData['deuda'] - $extraData['total_pagado']);
                                                        $saldoMat = $matCuota ? max(0, $matCuota['deuda'] - $matCuota['descuento'] - $matCuota['total_pagado']) : 0;
                                                        $saldoTotalExt = $saldoMat + $saldoExtra;
                                                        $pagadoTotalExt = ($matCuota ? (float)$matCuota['total_pagado'] : 0) + (float)$extraData['total_pagado'];
                                                        $estExtra = $saldoTotalExt == 0 && ($matCuota || $extraData['deuda'] > 0) ? 'pagado' : ($pagadoTotalExt > 0 ? 'parcial' : 'deuda');
                                                    @endphp
                                                    <div class="cell-mod">
                                                        @if($matCuota)
                                                            <div class="text-center mb-1">
                                                                <span class="badge bg-soft-primary text-primary" style="font-size:.6rem; padding:.15em .55em;">
                                                                    <i class="ri-bank-card-line"></i> Matrícula
                                                                </span>
                                                            </div>
                                                            <div class="row-line deuda-row"><span class="lbl">Deuda</span> <span class="val">Bs {{ number_format($matCuota['deuda'], 2) }}</span></div>
                                                            @if($matCuota['descuento'] > 0)
                                                            <div class="row-line dto-row"><span class="lbl">Dto.</span> <span class="val">Bs {{ number_format($matCuota['descuento'], 2) }}</span></div>
                                                            @endif
                                                            <div class="row-line pagado-row mb-1"><span class="lbl">Pagado</span> <span class="val">Bs {{ number_format($matCuota['total_pagado'], 2) }}</span></div>
                                                        @endif
                                                        <div class="text-center mb-1">
                                                            <span class="badge bg-soft-warning text-warning" style="font-size:.6rem; padding:.15em .55em;">
                                                                <i class="ri-funds-line"></i> Coleg. Extra
                                                            </span>
                                                        </div>
                                                        <div class="row-line deuda-row"><span class="lbl">Deuda</span> <span class="val">Bs {{ number_format($extraData['deuda'], 2) }}</span></div>
                                                        @if($extraData['descuento'] > 0)
                                                        <div class="row-line dto-row"><span class="lbl">Dto.</span> <span class="val">Bs {{ number_format($extraData['descuento'], 2) }}</span></div>
                                                        @endif
                                                        <div class="row-line pagado-row"><span class="lbl">Pagado</span> <span class="val">Bs {{ number_format($extraData['total_pagado'], 2) }}</span></div>
                                                        <div class="saldo-line">
                                                            <span class="saldo-val {{ $estExtra }}">
                                                                @if($estExtra === 'pagado')<i class="ri-checkbox-circle-line"></i> Cancelado
                                                                @elseif($estExtra === 'parcial')<i class="ri-time-line"></i> Saldo Bs {{ number_format($saldoTotalExt, 2) }}
                                                                @else<i class="ri-error-warning-line"></i> Bs {{ number_format($saldoTotalExt, 2) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <span class="cell-empty">—</span>
                                                    @endif
                                                </td>
                                                @endfor

                                                <td class="total-deuda-col">
                                                    <div class="d-flex flex-column gap-1" style="font-size:.72rem; width:100%;">
                                                        @if($deudaPendiente > 0)
                                                            @if($deudaMatIns > 0)
                                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                                    <span style="font-size:.62rem; color:#878a99; font-weight:600; white-space:nowrap;"><i class="ri-bank-card-line text-primary"></i> Matrícula</span>
                                                                    <span style="font-weight:700; color:#f06548;">Bs {{ number_format($matPend, 2) }}</span>
                                                                </div>
                                                            @endif
                                                            @if($deudaColIns > 0)
                                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                                    <span style="font-size:.62rem; color:#878a99; font-weight:600; white-space:nowrap;"><i class="ri-funds-line text-info"></i> Colegiatura</span>
                                                                    <span style="font-weight:700; color:#f06548;">Bs {{ number_format($colPend, 2) }}</span>
                                                                </div>
                                                            @endif
                                                            <hr style="margin:1px 0; border-top:1px dashed #d6dbe0; width:100%;">
                                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                                <span style="font-size:.62rem; color:#495057; font-weight:600;">Total Pend.</span>
                                                                <span style="display:inline-flex; align-items:center; gap:3px; font-weight:800; padding:.15em .6em; border-radius:999px; background:#fee2e2; color:#f06548; font-size:.82rem; border:1px solid #fecaca;">
                                                                    <i class="ri-error-warning-line"></i> Bs {{ number_format($deudaPendiente, 2) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <span style="display:inline-flex; align-items:center; gap:4px; font-weight:800; padding:.35em .75em; border-radius:999px; background:#d1fae5; color:#0ab39c; font-size:.82rem; border:1px solid #a7f3d8;">
                                                                    <i class="ri-checkbox-circle-line"></i> Cancelado
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div class="text-muted text-end" style="font-size:.62rem; text-transform:uppercase; letter-spacing:.3px; font-weight:700; margin-top:2px;">
                                                            Cobrado: <span class="text-success fw-bold">Bs {{ number_format($pagadoTotalP, 2) }}</span>
                                                        </div>
                                                     </div>
                                                  </td>
                                                  @can('contable.asignaciones.definir')
                                                   <td class="text-center" style="vertical-align:middle;">
                                                       <button type="button" class="btn-fechas btn-open-fechas-participante"
                                                           data-id="{{ $insC->participante_id }}"
                                                            data-programa="{{ $programa->id }}"
                                                           data-nombre="{{ $insC->apellidos }}, {{ $insC->nombres }}"
                                                           title="Registrar fechas de pago">
                                                           <i class="ri-calendar-check-line"></i> Definir
                                                       </button>
                                                   </td>
                                                    <td class="text-center" style="vertical-align:middle;">
                                                          <button type="button" class="btn btn-sm btn-icon btn-soft-secondary btn-toggle-baja"
                                                               data-inscripcion-id="{{ $insC->id }}"
                                                               data-nombre="{{ $insC->apellidos }}, {{ $insC->nombres }}"
                                                               data-ci="{{ $insC->ci }}"
                                                               data-baja-operativa="{{ $insC->baja_operativa ?? 0 }}"
                                                               data-baja-academica="{{ $insC->baja_academica ?? 0 }}"
                                                               data-baja-contable="{{ $insC->baja_contable ?? 0 }}"
                                                               title="Gestionar baja">
                                                               <i class="ri-user-unfollow-line"></i>
                                                          </button>
                                                          @if($inscripcionesConBaja->contains($insC->id))
                                                          <button type="button" class="btn btn-sm btn-icon btn-soft-info btn-baja-archivos"
                                                               data-inscripcion-id="{{ $insC->id }}"
                                                               data-nombre="{{ $insC->apellidos }}, {{ $insC->nombres }}"
                                                               data-ci="{{ $insC->ci }}"
                                                               data-tiene-archivo="{{ $archivosBaja->has($insC->id) ? 1 : 0 }}"
                                                               data-archivo-nombre="{{ $archivosBaja->has($insC->id) ? $archivosBaja[$insC->id]->archivo_original : '' }}"
                                                               data-bs-toggle="tooltip" data-bs-placement="top" title="Documentos de la baja">
                                                               <i class="ri-file-pdf-line"></i>
                                                          </button>
                                                          @endif
                                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-cambiar-plan"
                                                               data-participante="{{ $insC->participante_id }}"
                                                               data-programa="{{ $programa->id }}"
                                                               data-nombre="{{ $insC->apellidos }}, {{ $insC->nombres }}"
                                                               data-plan="{{ $insC->contado ? 'contado' : 'regular' }}"
                                                               data-desc-mat="{{ $insC->descuento_matricula ?? 0 }}"
                                                               data-desc-col="{{ $insC->descuento_colegiatura ?? 0 }}"
                                                               data-matricula="{{ $programa->matricula ?? 0 }}"
                                                               data-colegiatura="{{ $programa->colegiatura ?? 0 }}"
                                                               data-es-traspaso="{{ $insC->es_traspaso ?? 0 }}"
                                                               title="Cambiar plan de pagos">
                                                              @if($insC->contado)
                                                                  <i class="ri-flashlight-line"></i> Contado
                                                              @else
                                                                  <i class="ri-equalizer-line"></i> Regular
                                                              @endif
                                                          </button>
                                                      </td>
                                                   @endcan
                                              </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                                 @endif
                            </div>

                            {{-- ===== Sub-tab: Pagos Certificación ===== --}}
                            <div class="tab-pane" id="sub-tab-pagos-cert" role="tabpanel">
                                @php
                                    $costoCertificacion = (float) ($programa->costo_certificacion ?? 0);
                                    $costoCertPersonal = (float) ($programa->costo_certificacion_personal ?? 0);
                                    $totalPendientesCert = 0;
                                    $totalPagadosCert = 0;
                                    $totalDeudaCert = 0;
                                    $totalPagadoCert = 0;
                                    $participantesSinCert = 0;
                                    $totalConvenio = 0;
                                    $totalPersonal = 0;
                                    foreach ($inscripciones as $insCert) {
                                        $certDebt = $certificacionDebts->get($insCert->participante_id);
                                        if ($certDebt && (float)$certDebt->total_pagado >= (float)$certDebt->deuda) {
                                            $totalPagadosCert++;
                                        } else {
                                            $totalPendientesCert++;
                                        }
                                        if (!$certDebt) {
                                            $participantesSinCert++;
                                        }
                                        $totalDeudaCert += (float)($certDebt->deuda ?? 0);
                                        $totalPagadoCert += (float)($certDebt->total_pagado ?? 0);
                                        if ((int)($insCert->modalidad_titulacion ?? 1) === 1) {
                                            $totalConvenio++;
                                        } else {
                                            $totalPersonal++;
                                        }
                                    }
                                @endphp

                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="avatar-xs"><span class="avatar-title bg-soft-warning text-warning rounded-circle fs-16"><i class="ri-award-line"></i></span></span>
                                    <div>
                                        <h6 class="mb-0 fs-14 fw-semibold">Pagos Certificación</h6>
                                        <small class="text-muted" style="font-size:.72rem;">Convenio: Bs {{ number_format($costoCertificacion, 2) }} · Personal: Bs {{ number_format($costoCertPersonal, 2) }} · {{ $totalPagadosCert }} pagados · {{ $totalPendientesCert }} pendientes</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-lg-5 col-md-8">
                                        <div class="cont-summary-card cont-card-mat">
                                            <div class="cont-card-header">
                                                <span class="cont-card-icon" style="background:#d1fae5; color:#0ab39c;"><i class="ri-award-line"></i></span>
                                                <div>
                                                    <div class="cont-card-title">Certificación</div>
                                                    <div class="cont-card-sub">Según modalidad de titulación</div>
                                                </div>
                                            </div>
                                            <div class="cont-card-rows">
                                                <div class="cont-row"><span><i class="ri-briefcase-line"></i> Convenio <small class="text-muted">({{ $totalConvenio }} part.)</small></span> <strong class="text-primary">Bs {{ number_format($costoCertificacion, 2) }}</strong></div>
                                                <div class="cont-row"><span><i class="ri-user-line"></i> Trámite Personal <small class="text-muted">({{ $totalPersonal }} part.)</small></span> <strong class="text-primary">Bs {{ number_format($costoCertPersonal, 2) }}</strong></div>
                                                <div class="cont-row"><span><i class="ri-check-line"></i> Pagados</span> <strong class="text-success">{{ $totalPagadosCert }} participantes</strong></div>
                                                <div class="cont-row saldo-row"><span><i class="ri-time-line"></i> Pendientes</span>
                                                    <strong class="{{ $totalPendientesCert > 0 ? 'text-danger' : 'text-success' }}">{{ $totalPendientesCert }} participantes</strong>
                                                </div>
                                                <div class="cont-row"><span><i class="ri-funds-line"></i> Total recaudado</span> <strong class="text-primary">Bs {{ number_format($totalPagadoCert, 2) }} / Bs {{ number_format($totalDeudaCert, 2) }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(count($inscripciones) === 0)
                                    <div class="text-center text-muted py-5"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>No hay participantes inscritos en este programa</div>
                                @else
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-generar-deudas-cert">
                                        <i class="ri-refresh-line"></i> Generar Deudas de Certificación
                                    </button>
                                </div>
                                <div class="contable-wrap">
                                    <table class="table table-bordered align-middle mb-0 table-contable" id="table-certificacion">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th style="text-align:left !important;">Participante</th>
                                                <th style="width:110px;" class="text-center">Deuda Matrícula</th>
                                                <th style="width:110px;" class="text-center">Deuda Colegiatura</th>
                                                <th style="width:90px;" class="text-center">Modalidad</th>
                                                <th style="width:100px;" class="text-center">Costo Cert.</th>
                                                <th style="width:110px;" class="text-center">Saldo</th>
                                                <th style="width:80px;" class="text-center">Estado</th>
                                                <th style="width:160px;" class="text-center">Fechas de Pago</th>
                                                <th style="width:75px;" class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscripciones as $idxCert => $insCert)
                                            @php
                                                $certDebt = $certificacionDebts->get($insCert->participante_id);
                                                $deudaCert = $certDebt ? (float)$certDebt->deuda : 0;
                                                $pagadoCert = $certDebt ? (float)$certDebt->total_pagado : 0;
                                                $saldoCert = max(0, $deudaCert - $pagadoCert);
                                                $tieneDeuda = $certDebt !== null;
                                                $estaPagado = $tieneDeuda && $pagadoCert >= $deudaCert;
                                                $detallePagos = $tieneDeuda && $certDebt->detalle_pagos ? json_decode($certDebt->detalle_pagos, true) : [];
                                                $pMatCert = $pagoMatriculaPorParticipante->get($insCert->participante_id);
                                                $pColCert = $pagoColegiaturaPorParticipante->get($insCert->participante_id);
                                                $deudaMatCert = $pMatCert ? max(0, (float) $pMatCert->deuda - (float) $pMatCert->pagado) : 0;
                                                $deudaColCert = $pColCert ? max(0, (float) $pColCert->deuda - (float) $pColCert->pagado) : 0;
                                                $modalidadVal = $insCert->modalidad_titulacion ?? null;
                                                $esConvenio = $modalidadVal !== null && (int)$modalidadVal === 1;
                                                $modalidadNombre = $modalidadVal === null || $modalidadVal === '' ? 'Sin definir' : ($esConvenio ? 'Convenio' : 'Personal');
                                                $modalidadClass = $modalidadVal === null || $modalidadVal === '' ? 'secondary' : ($esConvenio ? 'primary' : 'info');
                                                $iniCert = strtoupper(substr($insCert->nombres, 0, 1) . substr($insCert->apellidos, 0, 1));
                                                $esBajaTempranaCert = (int)$insCert->estado === 0 && !(int)($insCert->baja_operativa ?? 0) && !(int)($insCert->baja_academica ?? 0);
                                                $esBajaOperativaCert = (int)($insCert->baja_operativa ?? 0);
                                            @endphp
                                            <tr class="{{ ($insCert->es_traspaso ?? 0) == 1 ? 'traspaso-row' : '' }}" id="cert-row-{{ $insCert->participante_id }}" style="{{ $esBajaTempranaCert ? 'background: rgba(220,53,69,0.12) !important;' : ($esBajaOperativaCert ? 'background: rgba(255,193,7,0.15) !important;' : '') }}">
                                                <td class="text-center text-muted fw-bold">{{ $idxCert + 1 }}</td>
                                                <td>
                                                    <div class="participante-cell">
                                                        <div class="pc-avatar">{{ $iniCert }}</div>
                                                        <div class="pc-info">
                                                            <div class="pc-name">{{ $insCert->apellidos }}, {{ $insCert->nombres }}@if(($insCert->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif
                                                                @if($esBajaTempranaCert)
                                                                    <span class="badge bg-soft-danger text-danger fs-9 ms-1"><i class="ri-close-circle-line"></i> Baja Temprana</span>
                                                                @elseif($esBajaOperativaCert)
                                                                    <span class="badge bg-soft-warning text-warning fs-9 ms-1"><i class="ri-error-warning-line"></i> Baja Operativa</span>
                                                                @endif
                                                            </div>
                                                            <div class="pc-ci"><i class="ri-passport-line"></i> {{ $insCert->ci }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($deudaMatCert > 0)
                                                        <span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">
                                                            <i class="ri-error-warning-line"></i> Bs {{ number_format($deudaMatCert, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-success text-success fs-11 px-2 py-1">
                                                            <i class="ri-checkbox-circle-line"></i> Cancelado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($deudaColCert > 0)
                                                        <span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">
                                                            <i class="ri-error-warning-line"></i> Bs {{ number_format($deudaColCert, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-success text-success fs-11 px-2 py-1">
                                                            <i class="ri-checkbox-circle-line"></i> Cancelado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-{{ $modalidadClass }} text-{{ $modalidadClass }} fs-10 px-2 py-1">{{ $modalidadNombre }}</span>
                                                </td>
                                                <td class="text-center fw-semibold fs-14">
                                                    @if($tieneDeuda)
                                                        Bs {{ number_format($deudaCert, 2) }}
                                                    @else
                                                        @php $costoBase = $esConvenio ? $costoCertificacion : $costoCertPersonal; @endphp
                                                        Bs {{ number_format($costoBase, 2) }}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($tieneDeuda)
                                                        @if($saldoCert > 0)
                                                            <span class="badge bg-soft-danger text-danger fs-11 px-2 py-1">
                                                                <i class="ri-error-warning-line"></i> Bs {{ number_format($saldoCert, 2) }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-soft-success text-success fs-11 px-2 py-1">
                                                                <i class="ri-checkbox-circle-line"></i> Cancelado
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1">
                                                            <i class="ri-hourglass-line"></i> Pendiente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($tieneDeuda)
                                                        <span class="badge certificacion-status-badge {{ $estaPagado ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} fs-11 px-2 py-1">
                                                            <i class="ri-{{ $estaPagado ? 'checkbox-circle-line' : 'close-circle-line' }}"></i>
                                                            {{ $estaPagado ? 'Pagado' : 'Pendiente' }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1">Sin deuda</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(count($detallePagos) > 0)
                                                        <div style="display:flex;flex-direction:column;gap:2px;align-items:center;">
                                                            @foreach($detallePagos as $dp)
                                                                <span class="badge bg-soft-info text-info fs-10 px-2 py-1" style="font-weight:500;white-space:nowrap;">
                                                                    {{ \Carbon\Carbon::parse($dp['fecha'])->format('d/m/Y') }} · Bs {{ number_format((float)$dp['monto'], 2) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @elseif($tieneDeuda)
                                                        <span class="text-muted fs-12">—</span>
                                                    @else
                                                        <span class="text-muted fs-12">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($tieneDeuda)
                                                        <button type="button" class="btn btn-sm btn-icon btn-icon-ghost btn-pagar-cert" data-participante="{{ $insCert->participante_id }}" data-participante-nombre="{{ $insCert->apellidos }}, {{ $insCert->nombres }}" data-deuda="{{ $deudaCert }}" data-pagado="{{ $pagadoCert }}" data-saldo="{{ $saldoCert }}" title="Realizar pago">
                                                            <i class="ri-money-dollar-circle-line text-success" style="font-size:1.15rem;"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted fs-12">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
@endcan

@php
    // Se calculan siempre (no solo para Super Admin) porque el JS de la página
    // usa estos valores; el tab de Rentabilidad se muestra solo al Super Admin.
    $rentInscritos = $totalInscritos;
    $rentIngresoBruto = $ingresoBrutoTotal;
    $rentIngresoNeto = $ingresoNetoReal;
    $rentDescMatTotal = $inscripciones->sum('descuento_matricula');
    $rentDescColTotal = $inscripciones->sum('descuento_colegiatura');
    $rentDescTotal = $rentDescMatTotal + $rentDescColTotal;
    $rentCostoDocente = $costoDocenteReal;
    $rentPagoDocenteBase = $totalPagoDocente;
    $rentIva = $totalIva;
    $rentIt = $totalIt;
    $rentGastosExtra = $totalGastosExtra;
    $rentCostoDocenteSinExtra = $totalPagoDocente + $totalIva + $totalIt;
    $rentModulosFactura = $modulosConFactura;
    $rentTotalModulos = $totalModulos;
    $rentModulosCount = (($programa->modulo_cero ?? 1) ? max(1, $totalModulos - 1) : max(1, $totalModulos));
    $rentIngresoXParticipante = $ingresoPromedioParticipante;
    $rentPresupuestoMarketing = $presupuestoCampana;
    $rentCostoUni = $costoUniversidadAlumno;
    $rentAdminMensual = $gastosAdminMensual;
    $rentMesesCampana = $mesesCampana;
    $rentDuracionMeses = (($programa->modulo_cero ?? 1) ? max(1, $totalModulos - 1) : max(1, $totalModulos));
    $rentPrecioLista = $precioListaAlumno;
    $rentTotalBajas = $totalBajas;
@endphp

@if($esSuperAdmin)
{{-- ============ TAB RENTABILIDAD ============ --}}
<div class="tab-pane" id="tab-rentabilidad" role="tabpanel">

                        {{-- Header --}}
                        <div class="rent-section-header">
                            <span class="rent-section-icon" style="background:#e8faf1;color:#0ab39c;"><i class="ri-line-chart-line"></i></span>
                            <div>
                                <div class="rent-section-title">Análisis de Rentabilidad</div>
                                <div class="rent-section-sub">
                                    Punto de equilibrio · Proyección financiera del programa
                                    @if($rentInscritos > 0)
                                        · {{ $rentInscritos }} inscrito{{ $rentInscritos !== 1 ? 's' : '' }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Alertas del análisis --}}
                        <div id="rent-alertas" class="mb-3"></div>

                        {{-- Parámetros del análisis (persistentes) --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="rent-metric-sm"><i class="ri-settings-3-line"></i> Parámetros del análisis</span>
                            <button type="button" class="rent-btn-save" id="rent-btn-guardar">
                                <i class="ri-save-3-line"></i> Guardar parámetros
                            </button>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-param-card" style="--rent-accent:#f7b84b;">
                                    <div class="rent-param-header">
                                        <span class="rent-param-icon" style="background:#fff8f0;color:#f7b84b;"><i class="ri-megaphone-line"></i></span>
                                        <div>
                                            <div class="rent-param-title">Marketing</div>
                                            <div class="rent-param-sub" id="rent-marketing-subtitle">Total campaña: Bs 0.00</div>
                                        </div>
                                    </div>
                                    <div class="rent-param-body">
                                        <div class="rent-param-field">
                                            <label><i class="ri-money-dollar-circle-line"></i> Monto mensual (Bs)</label>
                                            <input type="number" class="form-control form-control-sm" id="rent-marketing-monto"
                                                value="{{ $rentPresupuestoMarketing }}" min="0" step="0.01">
                                        </div>
                                        <div class="rent-param-field">
                                            <label><i class="ri-calendar-line"></i> Meses de campaña</label>
                                            <input type="number" class="form-control form-control-sm" id="rent-marketing-meses"
                                                value="{{ $rentMesesCampana }}" min="1" max="60" step="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-param-card" style="--rent-accent:#405189;">
                                    <div class="rent-param-header">
                                        <span class="rent-param-icon" style="background:#eef1f8;color:#405189;"><i class="ri-building-2-line"></i></span>
                                        <div>
                                            <div class="rent-param-title">Administrativos</div>
                                            <div class="rent-param-sub" id="rent-admin-subtitle">Total: Bs 0.00</div>
                                        </div>
                                    </div>
                                    <div class="rent-param-body">
                                        <div class="rent-param-field">
                                            <label><i class="ri-money-dollar-circle-line"></i> Gasto mensual (Bs)</label>
                                            <input type="number" class="form-control form-control-sm" id="rent-admin-mensual"
                                                value="{{ $rentAdminMensual }}" min="0" step="0.01">
                                        </div>
                                        <div class="rent-param-field">
                                            <label><i class="ri-time-line"></i> Duración programa (meses)</label>
                                            <input type="number" class="form-control form-control-sm" id="rent-duracion-meses"
                                                value="{{ $rentDuracionMeses }}" min="1" max="120" step="1">
                                            @if($duracionDerivada > 0)
                                            <small style="font-size:.6rem;color:#878a99;display:block;margin-top:2px;"><i class="ri-information-line"></i> Según fechas: {{ $duracionDerivada }} meses</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-param-card" style="--rent-accent:#0ab39c;">
                                    <div class="rent-param-header">
                                        <span class="rent-param-icon" style="background:#e8faf1;color:#0ab39c;"><i class="ri-school-line"></i></span>
                                        <div>
                                            <div class="rent-param-title">Costo Universidad</div>
                                            <div class="rent-param-sub">Costo variable por alumno</div>
                                        </div>
                                    </div>
                                    <div class="rent-param-body">
                                        <div class="rent-param-field">
                                            <label><i class="ri-money-dollar-circle-line"></i> Bs por alumno</label>
                                            <input type="number" class="form-control form-control-sm" id="rent-costo-uni"
                                                value="{{ $rentCostoUni }}" min="0" step="0.01">
                                        </div>
                                        <div class="rent-flex-between" style="border-top:1px dashed #eef0f4;padding-top:7px;">
                                            <span style="font-size:.66rem;color:#878a99;"><i class="ri-price-tag-3-line"></i> Precio lista</span>
                                            <strong style="font-size:.85rem;">Bs {{ number_format($rentPrecioLista, 2) }}</strong>
                                        </div>
                                        <div class="rent-flex-between" style="margin-top:-2px;">
                                            <span style="font-size:.66rem;color:#878a99;"><i class="ri-scales-3-line"></i> Margen contribución</span>
                                            <strong style="font-size:.85rem;" id="rent-margen-contrib-label">Bs 0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-result-card">
                                    <div class="rent-result-header">
                                        <span class="rent-result-icon"><i class="ri-funds-box-line"></i></span>
                                        <div>
                                            <div class="rent-result-title">Resultado Final</div>
                                            <div class="rent-result-sub">Utilidad / Pérdida proyectada</div>
                                        </div>
                                    </div>
                                    <div class="rent-result-rows">
                                        <div class="rent-result-row">
                                            <span><i class="ri-money-dollar-circle-line"></i> Utilidad</span>
                                            <strong class="rent-result-big" id="rent-utilidad-label">Bs 0.00</strong>
                                        </div>
                                        <div class="rent-result-row">
                                            <span><i class="ri-percent-line"></i> Margen</span>
                                            <strong class="rent-result-big" id="rent-margen-label">0%</strong>
                                        </div>
                                        <div class="rent-result-margin-bar">
                                            <div class="rent-result-margin-fill" id="rent-margen-bar" style="width:0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cards resumen --}}
                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-summary-card" style="--rent-accent:#299cdb;">
                                    <div class="cont-card-header" style="margin-bottom:.65rem;padding-bottom:.55rem;">
                                        <span class="cont-card-icon" style="background:#dbeafe;color:#299cdb;"><i class="ri-bank-card-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Ingresos</div>
                                            <div class="cont-card-sub">Bruto − Descuentos</div>
                                        </div>
                                    </div>
                                    <div class="cont-card-rows">
                                        <div class="cont-row"><span>Bruto</span> <strong>Bs {{ number_format($rentIngresoBruto, 2) }}</strong></div>
                                        <div class="cont-row"><span>Descuentos</span> <strong style="color:#f06548;">− Bs {{ number_format($rentDescTotal, 2) }}</strong></div>
                                        <div class="cont-row saldo-row" style="border-top:1px dashed #eef0f4;padding-top:.4rem;margin-top:.15rem;">
                                            <span><i class="ri-check-line text-success"></i> Neto</span>
                                            <strong style="color:#0ab39c;font-size:1rem;">Bs {{ number_format($rentIngresoNeto, 2) }}</strong>
                                        </div>
                                        <div class="cont-row" style="margin-top:3px;padding-top:3px;border-top:1px dashed #f1f3f5;">
                                            <span style="font-size:.66rem;color:#878a99;"><i class="ri-group-line"></i> Por inscrito</span>
                                            <strong style="font-size:.85rem;">Bs {{ number_format($rentIngresoXParticipante, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="rent-summary-card" style="--rent-accent:#f06548;">
                                    <div class="cont-card-header" style="margin-bottom:.65rem;padding-bottom:.55rem;">
                                        <span class="cont-card-icon" style="background:#fee2e2;color:#f06548;"><i class="ri-user-star-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Costos Docentes</div>
                                            <div class="cont-card-sub">{{ $rentModulosFactura }}/{{ $rentTotalModulos }} mód. con factura</div>
                                        </div>
                                    </div>
                                    <div class="cont-card-rows">
                                        <div style="border-bottom:1px dashed #eef0f4;padding-bottom:7px;margin-bottom:5px;">
                                            <div class="rent-flex-between" style="margin-bottom:6px;">
                                                <span style="font-size:.7rem;color:#878a99;"><i class="ri-pencil-line"></i> Estimar costo docente</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="flex:1;">
                                                    <label style="font-size:.6rem;color:#878a99;font-weight:600;display:block;margin-bottom:1px;">Bs por docente</label>
                                                    <input type="number" step="0.01" min="0" id="rent-pago-docente"
                                                        class="form-control form-control-sm text-end fw-bold" value="0" placeholder="0.00"
                                                        style="font-size:.78rem;padding:2px 6px;border:1px solid #e0e3e8;border-radius:6px;">
                                                </div>
                                                <div style="flex:0 0 68px;">
                                                    <label style="font-size:.6rem;color:#878a99;font-weight:600;display:block;margin-bottom:1px;">Cantidad</label>
                                                    <input type="number" step="1" min="0" id="rent-cant-docentes"
                                                        class="form-control form-control-sm text-end fw-bold" value="{{ $rentModulosCount }}" placeholder="0"
                                                        style="font-size:.78rem;padding:2px 6px;border:1px solid #e0e3e8;border-radius:6px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cont-row"><span>Pago real módulos</span> <strong>Bs {{ number_format($rentPagoDocenteBase, 2) }}</strong></div>
                                        @if($rentIva > 0 || $rentIt > 0)
                                        <div class="cont-row"><span>Retenciones (IVA+IT)</span> <strong style="color:#d97706;">Bs {{ number_format($rentIva + $rentIt, 2) }}</strong></div>
                                        @endif
                                        @if($rentGastosExtra > 0)
                                        <div class="cont-row"><span>Gastos extra presencial</span> <strong style="color:#299cdb;">Bs {{ number_format($rentGastosExtra, 2) }}</strong></div>
                                        @endif
                                        <div class="cont-row saldo-row" style="border-top:1px dashed #eef0f4;padding-top:.4rem;margin-top:.15rem;">
                                            <span><i class="ri-error-warning-line" style="color:#f06548;"></i> Costo total estimado</span>
                                            <strong style="color:#f06548;font-size:1rem;" id="rent-costo-docente-label">Bs 0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="rent-viabilidad-card">
                                    <div class="cont-card-header" style="margin-bottom:.65rem;padding-bottom:.55rem;">
                                        <span class="cont-card-icon" style="background:#f0eefb;color:#6559cc;"><i class="ri-rocket-2-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Viabilidad de Inicio</div>
                                            <div class="cont-card-sub">¿Cuántos inscritos se necesitan como mínimo para iniciar el programa?</div>
                                        </div>
                                    </div>
                                    <div class="cont-card-rows">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-4 text-center" style="border-right:1px dashed #eef0f4;">
                                                <div class="rent-metric-sm">Mínimo para iniciar</div>
                                                <div style="font-size:2.2rem;font-weight:900;line-height:1.2;color:#6559cc;" id="rent-min-inicio">—</div>
                                                <div style="font-size:.64rem;color:#878a99;font-weight:600;">inscritos (precio lista)</div>
                                            </div>
                                            <div class="col-8">
                                                <div class="cont-row"><span style="font-size:.68rem;color:#878a99;">Precio lista por alumno</span>
                                                    <strong style="font-size:.82rem;">Bs {{ number_format($rentPrecioLista, 2) }}</strong></div>
                                                <div class="cont-row"><span style="font-size:.68rem;color:#878a99;">− Costo variable (universidad)</span>
                                                    <strong style="font-size:.82rem;color:#f06548;" id="rent-via-costo-uni">Bs 0.00</strong></div>
                                                <div class="cont-row" style="border-top:1px dashed #eef0f4;margin-top:2px;padding-top:4px;">
                                                    <span style="font-size:.68rem;color:#878a99;">= Margen por alumno</span>
                                                    <strong style="font-size:.82rem;color:#0ab39c;" id="rent-via-margen">Bs 0.00</strong></div>
                                                <div class="cont-row"><span style="font-size:.68rem;color:#878a99;">Costos fijos a cubrir</span>
                                                    <strong style="font-size:.82rem;" id="rent-via-fijos">Bs 0.00</strong></div>
                                            </div>
                                        </div>
                                        <div class="mt-2 pt-2" style="border-top:1px dashed #eef0f4;">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="mb-0" style="font-size:.66rem;font-weight:700;color:#878a99;text-transform:uppercase;white-space:nowrap;">
                                                    <i class="ri-user-add-line"></i> Simular inscritos:
                                                </label>
                                                <input type="number" class="rent-sim-input" id="rent-sim-alumnos"
                                                    value="{{ max($rentInscritos, 10) }}" min="0" step="1">
                                                <span class="rent-badge rent-badge-neutral flex-grow-1 text-center" id="rent-sim-resultado" style="font-size:.75rem;">—</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Punto de equilibrio --}}
                        <div class="row g-3">
                            <div class="col-lg-5">
                                <div class="rent-breakeven-card">
                                    <div class="cont-card-header" style="margin-bottom:.65rem;padding-bottom:.55rem;">
                                        <span class="cont-card-icon" style="background:#eef1f8;color:#405189;"><i class="ri-crosshair-2-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Punto de Equilibrio</div>
                                            <div class="cont-card-sub">Inscritos necesarios para cubrir costos</div>
                                        </div>
                                    </div>
                                    <div class="cont-card-rows">
                                        @php
                                            $rentCostosFijosBase = $rentCostoDocente
                                                + ($rentPresupuestoMarketing * $rentMesesCampana)
                                                + ($rentAdminMensual * $rentDuracionMeses);
                                            $rentMargenContribBase = $rentIngresoXParticipante - $rentCostoUni;
                                            $puntoEqBase = $rentMargenContribBase > 0
                                                ? (int) ceil($rentCostosFijosBase / $rentMargenContribBase)
                                                : 0;
                                            $costoXParticipanteBase = $rentInscritos > 0
                                                ? ($rentCostosFijosBase + $rentCostoUni * $rentInscritos) / $rentInscritos
                                                : 0;
                                        @endphp
                                        <div style="text-align:center;padding:.6rem 0;">
                                            <div class="rent-metric-sm">Inscritos actuales</div>
                                            <div class="rent-metric-lg" style="color:#405189;" id="rent-actual-inscritos">{{ $rentInscritos }}</div>
                                        </div>
                                        <div style="text-align:center;padding:.5rem 0;border-top:1px dashed #eef0f4;">
                                            <div class="rent-metric-sm" style="margin-bottom:3px;">
                                                <i class="ri-arrow-right-line" style="color:#0ab39c;"></i> Punto de Equilibrio
                                            </div>
                                            <div style="font-size:2.2rem;font-weight:900;line-height:1.2;color:#f06548;" id="rent-equilibrio-label">{{ $puntoEqBase }}</div>
                                            <div class="rent-metric-sm" style="margin-top:2px;">participantes</div>
                                        </div>
                                        <div style="text-align:center;padding:.5rem 0;border-top:1px dashed #eef0f4;">
                                            <div class="rent-metric-sm" style="margin-bottom:3px;">
                                                <i class="ri-funds-line" style="color:#0ab39c;"></i> Ingreso por inscrito
                                            </div>
                                            <div style="font-size:1.3rem;font-weight:800;color:#0ab39c;" id="rent-ingreso-xp-label">
                                                Bs {{ number_format($rentIngresoXParticipante, 2) }}
                                            </div>
                                        </div>
                                        <div style="padding:.55rem 0 0 0;border-top:1px dashed #eef0f4;">
                                            <div class="rent-flex-between" style="font-size:.8rem;">
                                                <span style="color:#878a99;font-weight:600;"><i class="ri-money-dollar-circle-line"></i> Costo total</span>
                                                <strong style="font-size:.95rem;" id="rent-costo-total-label">Bs {{ number_format($rentCostosFijosBase + $rentCostoUni * $rentInscritos, 2) }}</strong>
                                            </div>
                                            <div class="rent-flex-between" style="font-size:.8rem;margin-top:4px;">
                                                <span style="color:#878a99;font-weight:600;"><i class="ri-group-line"></i> Costo por inscrito</span>
                                                <strong style="font-size:.95rem;" id="rent-costo-xp-label">Bs {{ number_format($costoXParticipanteBase, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="rent-summary-card" style="--rent-accent:#0ab39c;">
                                    <div class="cont-card-header" style="margin-bottom:.65rem;padding-bottom:.55rem;">
                                        <span class="cont-card-icon" style="background:#e8faf1;color:#0ab39c;"><i class="ri-bar-chart-grouped-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Estado frente al Punto de Equilibrio</div>
                                            <div class="cont-card-sub" id="rent-estado-subtitle">Calculando...</div>
                                        </div>
                                    </div>
                                    <div class="cont-card-rows" style="padding:.35rem 0 0;">
                                        <div style="position:relative;padding:.8rem 0 1.2rem;">
                                            <div class="rent-bar-chart">
                                                <div class="rent-bar-group">
                                                    <span class="rent-bar-value" style="color:#405189;" id="rent-bar-actual-val">{{ $rentInscritos }}</span>
                                                    <div class="rent-bar rent-bar-actual" id="rent-bar-actual" title="Inscritos actuales"></div>
                                                    <span class="rent-bar-label">Actual</span>
                                                </div>
                                                <div class="rent-bar-group">
                                                    <span class="rent-bar-value" style="color:#f06548;" id="rent-bar-pe-val">{{ $puntoEqBase }}</span>
                                                    <div class="rent-bar rent-bar-pe" id="rent-bar-pe" title="Punto de equilibrio"></div>
                                                    <span class="rent-bar-label">Punto Eq.</span>
                                                </div>
                                                <div class="rent-bar-group">
                                                    <span class="rent-bar-value" style="color:#0ab39c;" id="rent-bar-proy-val">{{ max($rentInscritos, $puntoEqBase) }}</span>
                                                    <div class="rent-bar rent-bar-proy" id="rent-bar-proy" title="Proyección"></div>
                                                    <span class="rent-bar-label">Proy.</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="padding:.4rem 0 0 0;border-top:1px dashed #eef0f4;">
                                            <div class="rent-breakeven-status positive" id="rent-badge-estado">
                                                <i class="ri-checkbox-circle-line fs-5" id="rent-badge-icon" style="color:#0ab39c;"></i>
                                                <div>
                                                    <div style="font-size:.82rem;font-weight:700;color:#0ab39c;" id="rent-badge-text">Por encima del punto de equilibrio</div>
                                                    <div style="font-size:.66rem;color:#878a99;" id="rent-badge-detalle">Margen de seguridad</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabla de costos por módulo --}}
                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <div class="rent-table-card">
                                    <div class="cont-card-header" style="margin-bottom:0;padding:.85rem 1rem;border-bottom:1px dashed #eef0f4;">
                                        <span class="cont-card-icon" style="background:#fee2e2;color:#f06548;"><i class="ri-user-star-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Costos por Módulo</div>
                                            <div class="cont-card-sub">Registra el pago acordado con cada docente · Los cambios se reflejan en Rentabilidad</div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0" style="font-size:.8rem;">
                                            <thead class="text-muted text-uppercase">
                                                <tr>
                                                    <th style="width:30px;" class="text-center">#</th>
                                                    <th>Módulo</th>
                                                    <th class="text-end" style="width:110px;">Pago Docente</th>
                                                    <th class="text-center" style="width:70px;">Factura</th>
                                                    <th class="text-end" style="width:80px;">IVA ret.</th>
                                                    <th class="text-end" style="width:80px;">IT ret.</th>
                                                    <th class="text-end" style="width:110px;">Gastos Extra</th>
                                                    <th class="text-end" style="width:100px;">Costo Total</th>
                                                    <th style="width:40px;" class="text-center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($modulos as $modCosto)
                                                @php
                                                    $mcPago = (float)($modCosto->pago_docente ?? 0);
                                                    $mcIva = (float)($modCosto->iva ?? 0);
                                                    $mcIt = (float)($modCosto->it ?? 0);
                                                    $mcExtra = (float)($modCosto->gastos_extra ?? 0);
                                                    $mcFactura = (int)($modCosto->factura ?? 0);
                                                    $mcTotal = $mcPago + $mcIva + $mcIt + $mcExtra;
                                                @endphp
                                                <tr>
                                                    <td class="text-center text-muted fw-bold">{{ (($programa->modulo_cero ?? 1) ? $loop->index : $loop->iteration) }}</td>
                                                    <td class="fw-semibold text-dark">{{ $modCosto->nombre }}</td>
                                                    <td class="text-end fw-bold">Bs {{ number_format($mcPago, 2) }}</td>
                                                    <td class="text-center">
                                                        @if($mcFactura)
                                                            <span class="rent-badge rent-badge-success"><i class="ri-check-line"></i> Sí</span>
                                                        @else
                                                            <span class="rent-badge rent-badge-neutral">No</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">{{ $mcIva > 0 ? 'Bs '.number_format($mcIva, 2) : '—' }}</td>
                                                    <td class="text-end">{{ $mcIt > 0 ? 'Bs '.number_format($mcIt, 2) : '—' }}</td>
                                                    <td class="text-end">{{ $mcExtra > 0 ? 'Bs '.number_format($mcExtra, 2) : '—' }}</td>
                                                    <td class="text-end fw-bold" style="color:#212529;">Bs {{ number_format($mcTotal, 2) }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-icon btn-soft-dark btn-pago-docente"
                                                            data-modulo-id="{{ $modCosto->id }}"
                                                            data-modulo-nombre="{{ $modCosto->nombre }}"
                                                            data-pago-docente="{{ $mcPago }}"
                                                            data-factura="{{ $mcFactura }}"
                                                            data-iva="{{ $mcIva }}"
                                                            data-it="{{ $mcIt }}"
                                                            data-gastos-extra="{{ $mcExtra }}"
                                                            data-bs-toggle="tooltip" title="Editar costos">
                                                            <i class="ri-pencil-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-3 fs-13">No hay módulos registrados</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabla de costos detallada --}}
                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <div class="rent-table-card">
                                    <div class="cont-card-header" style="margin-bottom:0;padding:.85rem 1rem;border-bottom:1px dashed #eef0f4;">
                                        <span class="cont-card-icon" style="background:#e8faf1;color:#0ab39c;"><i class="ri-bar-chart-grouped-line"></i></span>
                                        <div>
                                            <div class="cont-card-title">Desglose de Costos</div>
                                            <div class="cont-card-sub">Análisis detallado de ingresos, costos y margen de rentabilidad</div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0" style="font-size:.82rem;">
                                            <thead class="text-muted text-uppercase">
                                                <tr>
                                                    <th style="width:200px;">Concepto</th>
                                                    <th class="text-end" style="width:140px;">Monto (Bs)</th>
                                                    <th class="text-end" style="width:100px;">% Ingreso</th>
                                                    <th style="width:120px;">Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-bank-card-line text-primary me-1"></i> Ingreso Bruto</td>
                                                    <td class="text-end fw-bold text-dark">Bs {{ number_format($rentIngresoBruto, 2) }}</td>
                                                    <td class="text-end text-muted">100%</td>
                                                    <td><span class="rent-badge rent-badge-success">Ingreso</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold text-muted"><i class="ri-discount-percent-line text-danger me-1"></i> Descuentos</td>
                                                    <td class="text-end fw-semibold text-danger">− Bs {{ number_format($rentDescTotal, 2) }}</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-desc">—</td>
                                                    <td><span class="rent-badge rent-badge-danger">Egreso</span></td>
                                                </tr>
                                                <tr style="border-bottom:2px solid #eef0f4;">
                                                    <td class="fw-bold" style="color:#0ab39c;"><i class="ri-check-line me-1"></i> Ingreso Neto</td>
                                                    <td class="text-end fw-bold" style="color:#0ab39c;font-size:.95rem;">Bs {{ number_format($rentIngresoNeto, 2) }}</td>
                                                    <td class="text-end fw-bold" style="color:#0ab39c;" id="rent-td-pct-neto">—</td>
                                                    <td><span class="rent-badge rent-badge-success">Base cálculo</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-user-star-line" style="color:#f06548;"></i> Pago Docentes</td>
                                                    <td class="text-end fw-bold text-danger" id="rent-td-costo-docente">Bs {{ number_format($rentCostoDocenteSinExtra, 2) }}</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-docente">—</td>
                                                    <td><span class="rent-badge rent-badge-danger">Costo fijo/variable</span></td>
                                                </tr>
                                                @if($rentGastosExtra > 0)
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-tools-line" style="color:#299cdb;"></i> Gastos Extra Presenciales</td>
                                                    <td class="text-end fw-bold" style="color:#299cdb;">Bs {{ number_format($rentGastosExtra, 2) }}</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-gastos-extra">—</td>
                                                    <td><span class="rent-badge rent-badge-info">Costo variable</span></td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-school-line" style="color:#0ab39c;"></i> Universidad <small class="text-muted" id="rent-td-uni-detalle"></small></td>
                                                    <td class="text-end fw-bold" style="color:#0ab39c;" id="rent-td-uni">Bs 0.00</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-uni">—</td>
                                                    <td><span class="rent-badge rent-badge-success">Costo variable</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-megaphone-line" style="color:#f7b84b;"></i> Marketing <small class="text-muted" id="rent-td-marketing-detalle"></small></td>
                                                    <td class="text-end fw-bold" style="color:#d97706;" id="rent-td-marketing">Bs 0.00</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-marketing">—</td>
                                                    <td><span class="rent-badge rent-badge-warning">Costo fijo</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold text-dark"><i class="ri-building-2-line" style="color:#405189;"></i> Administrativos <small class="text-muted" id="rent-td-admin-detalle"></small></td>
                                                    <td class="text-end fw-bold" style="color:#405189;" id="rent-td-admin">Bs 0.00</td>
                                                    <td class="text-end text-muted" id="rent-td-pct-admin">—</td>
                                                    <td><span class="rent-badge" style="background:#eef1f8;color:#405189;">Costo fijo</span></td>
                                                </tr>
                                                <tr style="border-top:2px solid #eef0f4;background:#f8f9fc;">
                                                    <td class="fw-bold text-dark fs-14"><i class="ri-funds-line me-1"></i> COSTO TOTAL</td>
                                                    <td class="text-end fw-bold text-dark fs-15" id="rent-td-costo-total">Bs 0.00</td>
                                                    <td class="text-end fw-bold text-dark" id="rent-td-pct-total">—</td>
                                                    <td></td>
                                                </tr>
                                                <tr style="background:#f8f9fc;">
                                                    <td class="fw-bold fs-15" id="rent-td-utilidad-label" style="color:#0ab39c;">
                                                        <i class="ri-money-dollar-circle-line me-1"></i> UTILIDAD / PÉRDIDA
                                                    </td>
                                                    <td class="text-end fw-bold fs-16" id="rent-td-utilidad">Bs 0.00</td>
                                                    <td class="text-end fw-bold fs-15" id="rent-td-margen">0%</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============ TAB REGISTROS UNIVERSIDAD ============ --}}
                    <div class="tab-pane" id="tab-registros-universidad" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-0 fs-14 fw-semibold"><i class="ri-government-line me-1"></i> Registros Universidad</h6>
                                <small class="text-muted">Folios de pago — cree los folios desde <a href="{{ route('admin.folios.index') }}" class="text-decoration-none fw-semibold">Folios</a> y registre participantes aquí</small>
                            </div>
                        </div>

                        @if(count($folios) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light text-muted text-uppercase fs-11">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Nombre</th>
                                        <th style="width:100px;">Fecha</th>
                                        <th style="width:150px;">Conceptos</th>
                                        <th style="width:60px;" class="text-center">Particip.</th>
                                        <th style="width:75px;" class="text-center">Estado</th>
                                        <th style="width:90px;" class="text-center">Inscripción</th>
                                        <th style="width:85px;" class="text-center">Matrícula</th>
                                        <th style="width:90px;" class="text-center">Colegiatura</th>
                                        <th style="width:90px;" class="text-center">Certificación</th>
                                        <th style="width:90px;" class="text-center">Total</th>
                                        <th style="width:80px;" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($folios as $idx => $fol)
                                    @php
                                        $folConceptos = json_decode($fol->concepto, true) ?? [];
                                        $folConceptosJson = htmlspecialchars(json_encode($folConceptos), ENT_QUOTES, 'UTF-8');
                                    @endphp
                                    <tr class="{{ $fol->status === 'cerrado' ? 'opacity-75' : '' }}">
                                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="fw-semibold fs-13">{{ $fol->nombre }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($fol->fecha_registro)->format('d/m/Y') }}</td>
                                        <td>
                                            @if(is_array($folConceptos) && count($folConceptos) > 0)
                                                @foreach($folConceptos as $fc)
                                                    <span class="badge bg-soft-info text-info fs-10 me-1">{{ $fc }}</span>
                                                @endforeach
                                            @else
                                                <small class="text-muted">—</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $fol->cantidad_participantes }}</td>
                                        <td class="text-center">
                                            @if($fol->status === 'abierto')
                                                <span class="badge bg-soft-success text-success fs-10">
                                                    <i class="ri-lock-unlock-line me-1"></i>Abierto
                                                </span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fs-10">
                                                    <i class="ri-lock-line me-1"></i>Cerrado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">Bs {{ number_format($fol->total_inscripcion, 2) }}</td>
                                        <td class="text-center fw-semibold">Bs {{ number_format($fol->total_matricula, 2) }}</td>
                                        <td class="text-center fw-semibold">Bs {{ number_format($fol->total_colegiatura, 2) }}</td>
                                        <td class="text-center fw-semibold">Bs {{ number_format($fol->total_certificacion, 2) }}</td>
                                        <td class="text-center fw-bold text-primary">Bs {{ number_format($fol->total_general, 2) }}</td>
                                        <td class="text-center">
                                            @if($fol->status === 'abierto')
                                            <button type="button" class="btn btn-sm btn-soft-success px-2 btn-registrar-folio"
                                                data-folio-id="{{ $fol->id }}"
                                                data-folio-name="{{ $fol->nombre }}"
                                                data-conceptos="{{ $folConceptosJson }}">
                                                <i class="ri-user-add-line"></i>
                                            </button>
                                            @else
                                            <span class="text-muted fs-11">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center text-muted py-5">
                            <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
                                    <p class="mb-0">No hay folios registrados aún</p>
                                    <small>Cree folios desde la sección <a href="{{ route('admin.folios.index') }}" class="text-decoration-none fw-semibold">Folios</a></small>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div> <!-- tab-content -->
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- col-12 -->
</div> <!-- row -->

{{-- Modal Fechas de Pago por Participante --}}
@can('contable.asignaciones.definir')
<div class="modal fade" id="modalFechasParticipante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-check-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Fechas de Pago</h5>
                        <small class="modal-subtitle" id="mfpSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="mfpLoader" class="text-center py-3 text-muted" style="display:none;">
                    <span class="spinner-border spinner-border-sm me-1"></span>Cargando...
                </div>
                <div id="mfpContent" style="display:none;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-soft-info text-info fs-12" id="mfpPlan">—</span>
                        <span class="text-muted fs-13" id="mfpTotalCuotas">—</span>
                    </div>
                    <div class="fechas-grid-participante">
                        <div class="fechas-col-participante" data-tipo="Matricula">
                            <div class="fechas-col-header">
                                <span class="fechas-col-title"><i class="ri-bank-card-line text-primary"></i> Matrícula</span>
                                <span class="badge bg-soft-primary text-primary" id="mfpMatCount">0 cuotas</span>
                            </div>
                            <div class="fechas-gen">
                                <div class="fechas-gen-title"><i class="ri-magic-line"></i> Generar fechas</div>
                                <div class="fechas-gen-row">
                                    <div class="grp">
                                        <label>Día</label>
                                        <input type="number" min="1" max="31" placeholder="15" class="mfp-gen-dia" data-mfp-gen-dia="Matricula">
                                    </div>
                                    <div class="grp">
                                        <label>Mes inicio</label>
                                        <input type="month" class="mfp-gen-mes" data-mfp-gen-mes="Matricula">
                                    </div>
                                    <button type="button" class="btn-apply-gen" data-mfp-gen-apply="Matricula">
                                        <i class="ri-check-line"></i> Aplicar
                                    </button>
                                </div>
                            </div>
                            <div class="fechas-list" id="mfpMatList"></div>
                        </div>
                        <div class="fechas-col-participante" data-tipo="Colegiatura">
                            <div class="fechas-col-header">
                                <span class="fechas-col-title"><i class="ri-funds-line text-success"></i> Colegiatura</span>
                                <span class="badge bg-soft-success text-success" id="mfpColCount">0 cuotas</span>
                            </div>
                            <div class="fechas-gen">
                                <div class="fechas-gen-title"><i class="ri-magic-line"></i> Generar fechas</div>
                                <div class="fechas-gen-row">
                                    <div class="grp">
                                        <label>Día</label>
                                        <input type="number" min="1" max="31" placeholder="15" class="mfp-gen-dia" data-mfp-gen-dia="Colegiatura">
                                    </div>
                                    <div class="grp">
                                        <label>Mes inicio</label>
                                        <input type="month" class="mfp-gen-mes" data-mfp-gen-mes="Colegiatura">
                                    </div>
                                    <button type="button" class="btn-apply-gen" data-mfp-gen-apply="Colegiatura">
                                        <i class="ri-check-line"></i> Aplicar
                                    </button>
                                </div>
                            </div>
                            <div class="fechas-list" id="mfpColList"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnSaveFechasParticipante">
                    <i class="ri-save-3-line"></i> Guardar Fechas
                </button>
            </div>
        </div>
    </div>
</div>
@endcan



{{-- Modal Cambiar Plan de Pago --}}
<div class="modal fade" id="modalCambiarPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-settings-3-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Cambiar Plan de Pagos</h5>
                        <small class="modal-subtitle" id="mcpSubtitle">{{ $programa->nombre }} · {{ $programa->codigo }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="inscribir-section">
                    <div class="inscribir-section-title">
                        <span class="inscribir-section-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></span>
                        <span>Costos del Programa</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="monto-card">
                                <div class="monto-icon bg-soft-primary text-primary"><i class="ri-bank-card-line"></i></div>
                                <div class="flex-grow-1">
                                    <div class="monto-label">Matrícula</div>
                                    <div class="monto-valor">Bs {{ number_format($programa->matricula ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="monto-card">
                                <div class="monto-icon bg-soft-info text-info"><i class="ri-funds-line"></i></div>
                                <div class="flex-grow-1">
                                    <div class="monto-label">Colegiatura</div>
                                    <div class="monto-valor">Bs {{ number_format($programa->colegiatura ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Descuento Matrícula</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" min="0" max="{{ $programa->matricula ?? 0 }}" id="mcp-desc-mat" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Descuento Colegiatura</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" step="0.01" min="0" max="{{ $programa->colegiatura ?? 0 }}" id="mcp-desc-col" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mcp-contado">
                                <label class="form-check-label small fw-semibold" for="mcp-contado">
                                    <i class="ri-money-dollar-box-line text-success"></i> Pago al contado
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mcp-traspaso">
                                <label class="form-check-label small fw-semibold" for="mcp-traspaso">
                                    <i class="ri-swap-line text-warning"></i> Es traspaso de otra unidad de negocio
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-cambiar-plan-confirm" onclick="cambiarPlanGuardar(this)">
                    <i class="ri-check-line align-bottom"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modales Detalle Módulos --}}
@foreach($seguimientoConcluido as $sc)
@php $pid = $sc['participante_id']; @endphp
@if(!$sc['aprobo_modulos'])
<div class="modal fade" id="modalModulos_{{ $pid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f06548 0%,#ff8a6c 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-book-open-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Módulos no aprobados</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }} · {{ $sc['ci'] }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
                    <thead class="table-light text-muted text-uppercase" style="font-size:.68rem;">
                        <tr>
                            <th>Módulo</th>
                            <th class="text-center" style="width:100px;">Nota Módulo</th>
                            <th class="text-center" style="width:100px;">Nota Nivel.</th>
                            <th class="text-center" style="width:80px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sc['detalle_modulos'] as $dm)
                        @php $dmAprobo = $dm['aprobo']; @endphp
                        <tr class="{{ !$dmAprobo ? 'table-danger' : '' }}">
                            <td class="fw-medium">{{ $dm['modulo_nombre'] }}</td>
                            <td class="text-center">{{ $dm['nota_modulo'] !== null ? number_format($dm['nota_modulo'], 2) : '—' }}</td>
                            <td class="text-center">{{ $dm['nota_nivelacion'] !== null ? number_format($dm['nota_nivelacion'], 2) : '—' }}</td>
                            <td class="text-center">
                                @if($dmAprobo)
                                    <span class="badge bg-soft-success text-success fs-10"><i class="ri-check-line"></i></span>
                                @else
                                    <span class="badge bg-soft-danger text-danger fs-10"><i class="ri-close-line"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Detalle Deuda Matrícula --}}
@if(!$sc['deuda_matricula_ok'])
<div class="modal fade" id="modalMatricula_{{ $pid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f7b84b 0%,#ffb547 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Deuda Matrícula</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body text-center" style="padding:2rem 1.5rem;background:#fafbfd;">
                <div class="fs-1 text-warning mb-2"><i class="ri-error-warning-line"></i></div>
                <h6 class="fw-bold mb-1">Monto pendiente</h6>
                <p class="fs-3 fw-bold text-warning mb-0">Bs {{ number_format($sc['deuda_matricula_monto'], 2) }}</p>
            </div>
            <div class="modal-footer justify-content-center" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Detalle Deuda Colegiatura --}}
@if(!$sc['deuda_colegiatura_ok'])
<div class="modal fade" id="modalColegiatura_{{ $pid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f7b84b 0%,#ffb547 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Deuda Colegiatura</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body text-center" style="padding:2rem 1.5rem;background:#fafbfd;">
                <div class="fs-1 text-warning mb-2"><i class="ri-error-warning-line"></i></div>
                <h6 class="fw-bold mb-1">Monto pendiente</h6>
                <p class="fs-3 fw-bold text-warning mb-0">Bs {{ number_format($sc['deuda_colegiatura_monto'], 2) }}</p>
            </div>
            <div class="modal-footer justify-content-center" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Seguimiento Fechas Monografía --}}
@if(count($sc['seguimientos']) > 0)
<div class="modal fade" id="modalSeguimientoFechas_{{ $pid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#299cdb 0%,#405189 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-calendar-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Seguimiento Monografía</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }} · {{ $sc['ci'] }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                <table class="table table-bordered align-middle mb-0" style="font-size:.82rem;">
                    <thead class="table-light text-muted text-uppercase" style="font-size:.68rem;">
                        <tr>
                            <th class="text-center" style="width:60px;">Etapa</th>
                            <th>Estado</th>
                            <th class="text-center" style="width:110px;">Fecha</th>
                            <th>Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sc['seguimientos'] as $seg)
                        <tr>
                            <td class="text-center fw-bold">{{ $seg->etapa }}</td>
                            <td>{{ $seg->estado }}</td>
                            <td class="text-center">{{ $seg->fecha ? \Carbon\Carbon::parse($seg->fecha)->format('d/m/Y') : '—' }}</td>
                            <td style="font-size:.75rem;">
                                @if((int)$seg->etapa === 4)
                                    <span class="badge bg-soft-warning text-warning fs-10"><i class="ri-barcode-line"></i> {{ $seg->estado }}</span>
                                @else
                                    {{ $seg->documento ?: '—' }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Pendientes (No procede) --}}
<div class="modal fade" id="modalPendientes_{{ $pid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f06548 0%,#f7b84b 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-alert-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Pendientes del participante</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }} · {{ $sc['ci'] }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                @php
                    $modulosNoAprobados = collect($sc['detalle_modulos'])->where('aprobo', false);
                @endphp

                @if(!$sc['aprobo_modulos'] && $modulosNoAprobados->count() > 0)
                <div class="mb-3">
                    <h6 class="text-danger fw-semibold fs-12 mb-2"><i class="ri-close-circle-line me-1"></i>Módulos no aprobados</h6>
                    <table class="table table-bordered align-middle mb-0" style="font-size:.78rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size:.65rem;">
                            <tr>
                                <th>Módulo</th>
                                <th class="text-center" style="width:90px;">Nota</th>
                                <th class="text-center" style="width:90px;">Nivelación</th>
                                <th class="text-center" style="width:70px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modulosNoAprobados as $dm)
                            <tr class="table-danger">
                                <td class="fw-medium">{{ $dm['modulo_nombre'] }}</td>
                                <td class="text-center">{{ $dm['nota_modulo'] !== null ? number_format($dm['nota_modulo'], 2) : '—' }}</td>
                                <td class="text-center">{{ $dm['nota_nivelacion'] !== null ? number_format($dm['nota_nivelacion'], 2) : '—' }}</td>
                                <td class="text-center"><span class="badge bg-soft-danger text-danger fs-10"><i class="ri-close-line"></i></span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if(!$sc['deuda_matricula_ok'])
                <div class="mb-3">
                    <h6 class="text-warning fw-semibold fs-12 mb-2"><i class="ri-money-dollar-circle-line me-1"></i>Deuda Matrícula</h6>
                    <div class="bg-white rounded p-3 border" style="font-size:.85rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Monto pendiente</span>
                            <span class="fw-bold text-warning fs-5">Bs {{ number_format($sc['deuda_matricula_monto'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if(!$sc['deuda_colegiatura_ok'])
                <div class="mb-3">
                    <h6 class="text-warning fw-semibold fs-12 mb-2"><i class="ri-money-dollar-circle-line me-1"></i>Deuda Colegiatura</h6>
                    <div class="bg-white rounded p-3 border" style="font-size:.85rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Monto pendiente</span>
                            <span class="fw-bold text-warning fs-5">Bs {{ number_format($sc['deuda_colegiatura_monto'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($modalidadSinDefinir)
                <div class="mb-3">
                    <h6 class="text-secondary fw-semibold fs-12 mb-2"><i class="ri-error-warning-line me-1"></i>Modalidad Titulación</h6>
                    <div class="bg-white rounded p-3 border" style="font-size:.85rem;">
                        <span class="text-muted">Sin definir. Seleccione Personal o Convenio.</span>
                    </div>
                </div>
                @endif

                @if(!$sc['pago_titulacion'])
                <div class="mb-0">
                    <h6 class="text-secondary fw-semibold fs-12 mb-2"><i class="ri-time-line me-1"></i>Pago Tramitación</h6>
                    <div class="bg-white rounded p-3 border" style="font-size:.85rem;">
                        <span class="text-muted">Pendiente de cancelación.</span>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:.75rem 1.5rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Ver Sesiones --}}
<div class="modal fade" id="modalSesiones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-xs flex-shrink-0">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-14">
                            <i class="ri-calendar-check-line"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="modal-title fs-14 fw-semibold" id="modalSesionesTitle">Sesiones</h5>
                        <span class="badge badge-soft-info fs-10" id="modalSesionesCount">0 sesiones</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light text-muted text-uppercase fs-11">
                            <tr>
                                <th style="width:36px" class="text-center">#</th>
                                <th style="width:140px"><i class="ri-calendar-line me-1"></i>Fecha</th>
                                <th style="width:68px" class="text-center"><i class="ri-time-line me-1"></i>Inicio</th>
                                <th style="width:68px" class="text-center"><i class="ri-time-line me-1"></i>Fin</th>
                                <th><i class="ri-user-line me-1"></i>Asignado</th>
                                <th style="width:100px" class="text-center"><i class="ri-flag-line me-1"></i>Estado</th>
                                <th><i class="ri-chat-1-line me-1"></i>Observaciones</th>
                                <th style="width:160px" class="text-center"><i class="ri-edit-line me-1"></i>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="modalSesionesBody">
                            <tr><td colspan="8" class="text-center text-muted py-4">
                                <i class="ri-loader-4-line ri-spin fs-4 d-block mb-1"></i>
                                <span class="fs-12">Cargando sesiones...</span>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light justify-content-between">
                <span class="text-muted fs-11" id="modalSesionesFooterCount"></span>
                <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@can('programas.modulos.sesion.extra')
{{-- Modal Agregar Sesión Extra --}}
<div class="modal fade" id="modalSesionExtra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formSesionExtra">
                <input type="hidden" name="modulo_id" id="se-modulo-id">
                <input type="hidden" name="programa_id" value="{{ $programa->id }}">
                @csrf
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-success text-success rounded-circle fs-14">
                                <i class="ri-add-line"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold" id="modalSesionExtraTitle">Agregar Sesión Extra</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Módulo</label>
                        <input type="text" class="form-control border-light bg-light" id="se-modulo-nombre" readonly>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium fs-12">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_desarrollo" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium fs-12">Hora Inicio <span class="text-danger">*</span></label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium fs-12">Hora Fin <span class="text-danger">*</span></label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Asignado</label>
                        <select name="asignado_id" class="form-select">
                            <option value="">Seleccionar</option>
                            @foreach($asignados as $as)
                            <option value="{{ $as->id }}">{{ $as->apellidos }}, {{ $as->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="Programado">Programado</option>
                            <option value="Confirmado">Confirmado</option>
                            <option value="Postergado">Postergado</option>
                        </select>
                    </div>
                    <div class="mb-3" id="se-fecha-container" style="display:none">
                        <label class="form-label text-muted fw-medium fs-12">Nueva Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="nueva_fecha" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Agregar observaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarSesion">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@can('programas.editar')
{{-- Modal Configurar Nivelación --}}
<div class="modal fade" id="modalNivelacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <form id="formNivelacion">
                <input type="hidden" name="programa_id" value="{{ $programa->id }}">
                <input type="hidden" name="modulo_id" id="niv-modulo-id">
                @csrf
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-14">
                                <i class="ri-refund-2-line"></i>
                            </span>
                        </span>
                        <div>
                            <h5 class="modal-title fs-14 fw-semibold">Configurar Nivelación</h5>
                            <small class="text-muted fs-11" id="niv-modulo-nombre">—</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-medium fs-12">Costo por participante (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="costo_participante" class="form-control" value="{{ $nivelacionCostoParticipante ?: $nivelacionCostoDefault }}" required>
                            <div class="text-muted fs-11 mt-1">Valor sugerido para este tipo: Bs {{ number_format($nivelacionCostoDefault, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-medium fs-12">Honorario docente (Bs/participante)</label>
                            <div class="form-control bg-light text-dark d-flex align-items-center justify-content-between" id="niv-honorario-por-participante" style="pointer-events:none;height:38px;">
                                <span>Bs {{ number_format($nivelacionPagoDocente ?: $nivelacionPagoDocenteDefault, 2) }}</span>
                                <i class="ri-lock-line text-muted fs-14"></i>
                            </div>
                            <div class="text-muted fs-11 mt-1">Según el tipo del programa. Se paga solo por participantes con comprobante validado.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-medium fs-12">Docente de la nivelación</label>
                            <div class="form-control bg-light d-flex align-items-center justify-content-between" id="niv-docente-fijo" style="pointer-events:none;height:38px;">
                                <span class="text-muted">Selecciona el módulo</span>
                                <i class="ri-lock-line text-muted fs-14"></i>
                            </div>
                            <div class="text-muted fs-11 mt-1">Fijo: el docente que desarrolló el módulo regular.</div>
                        </div>
                    </div>
                    <div class="alert alert-soft-primary py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background:#eef4ff;border:1px solid #d6e3ff;color:#2c5cc5;">
                        <i class="ri-wallet-3-line fs-14"></i>
                        <div class="fs-12"><b id="niv-honorario-total">Bs 0.00</b> <span class="text-muted">honorario docente total (por <span id="niv-honorario-count">0</span> participante seleccionado)</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12 d-flex align-items-center gap-2">
                            Participantes reprobados del módulo
                            <span class="badge bg-soft-primary text-primary" id="niv-participantes-count">0</span>
                        </label>
                        <div class="table-responsive table-card border rounded" style="max-height:280px;overflow:auto;">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <thead class="table-light text-muted text-uppercase">
                                    <tr>
                                        <th style="width:42px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="niv-check-todos">
                                        </th>
                                        <th>Participante</th>
                                        <th class="text-center">Nota módulo</th>
                                        <th class="text-center">Nota niv. gratuita</th>
                                        <th class="text-center">Cuota</th>
                                    </tr>
                                </thead>
                                <tbody id="niv-participantes-body">
                                    <tr><td colspan="5" class="text-center text-muted py-3">Selecciona el módulo para cargar participantes</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="alert alert-soft-warning mb-0" style="background:#fff8eb;border:1px solid #fde7be;color:#7c5e10;">
                        <i class="ri-information-line me-1"></i>Al guardar se reemplazarán las cuotas de nivelación de <b>este módulo</b> por las de los participantes seleccionados. Las notas de nivelación registradas se conservan.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarNivelacion">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@can('programas.modulos.registrar.horarios')
{{-- Modal Agregar Sesión de Nivelación --}}
<div class="modal fade" id="modalHorarioNivelacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formHorarioNivelacion">
                <input type="hidden" name="programa_id" value="{{ $programa->id }}">
                <input type="hidden" name="modulo_id" id="niv-horario-modulo-id">
                @csrf
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-14">
                                <i class="ri-calendar-event-line"></i>
                            </span>
                        </span>
                        <div>
                            <h5 class="modal-title fs-14 fw-semibold">Agregar Sesión de Nivelación</h5>
                            <small class="text-muted fs-11" id="niv-horario-modulo-nombre">—</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Módulo</label>
                        <input type="text" class="form-control border-light bg-light" id="niv-horario-modulo-nombre-display" readonly>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium fs-12">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_desarrollo" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium fs-12">Hora Inicio <span class="text-danger">*</span></label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-medium fs-12">Hora Fin <span class="text-danger">*</span></label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Asignado <span class="text-danger">*</span></label>
                        <select name="asignado_id" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($asignados as $as)
                            <option value="{{ $as->id }}">{{ $as->apellidos }}, {{ $as->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarHorarioNivelacion">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modal Notas de Nivelación (masivo) --}}
<div class="modal fade" id="modalNotasNivelacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-warning" style="color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-xs flex-shrink-0">
                        <span class="avatar-title bg-white text-warning rounded-circle fs-14">
                            <i class="ri-pencil-line"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="modal-title fs-14 fw-semibold mb-0">Registrar Notas de Nivelación</h5>
                        <small style="color:rgba(255,255,255,.9);" class="fs-11" id="mnnMasivoSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#fafbfd;padding:1.25rem;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
                        <thead class="table-light text-muted text-uppercase" style="font-size:.68rem;">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th style="width:14%">CI</th>
                                <th>Apellidos, Nombres</th>
                                <th style="width:12%;" class="text-center">Nota Módulo</th>
                                <th style="width:16%;" class="text-center">Nota Nivelación <small class="text-muted d-block fs-10 fw-normal">(0-100)</small></th>
                            </tr>
                        </thead>
                        <tbody id="mnnMasivoCuerpo">
                            <tr><td colspan="5" class="text-center text-muted py-3">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarNotasNivelacion"><i class="ri-save-3-line"></i> Guardar todas las notas</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Comprobante de Pago Nivelación (ver / validar) --}}
<div class="modal fade" id="modalComprobanteNivelacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-xs flex-shrink-0">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-14">
                            <i class="ri-file-line"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="modal-title fs-14 fw-semibold">Comprobante de pago</h5>
                        <small class="text-muted fs-11" id="mcnSubtitle">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted fs-12" id="mcnArchivo"><i class="ri-file-download-line me-1"></i>—</span>
                    <a href="#" id="mcnDescargar" class="btn btn-sm btn-soft-primary" target="_blank" download>
                        <i class="ri-download-2-line me-1"></i>Descargar
                    </a>
                </div>
                <div class="p-3 text-center" id="mcnPreview">
                    <div class="text-muted py-5"><span class="spinner-border spinner-border-sm me-1"></span>Cargando...</div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-sm btn-success" id="btnValidarComprobanteNivelacion" style="display:none;">
                    <i class="ri-check-double-line me-1"></i>Validar pago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pagos del Mes (Contabilidad) --}}
<div class="modal fade pm-modal" id="modalPagos" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="pm-title" id="modalPagosTitle">Pagos del mes</h5>
                    <div class="pm-sub" id="modalPagosSub">Detalle de pagos registrados</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pm-stats" id="modalPagosStats"></div>
                <div class="pm-wrap">
                    <div class="pm-toolbar">
                        <div class="pm-search">
                            <i class="ri-search-line"></i>
                            <input type="text" class="pm-search-input" data-target="modalPagosBody" data-count="modalPagosCount" placeholder="Buscar por nombre o CI..." autocomplete="off">
                        </div>
                        <span class="pm-countlbl" id="modalPagosCount"></span>
                    </div>
                    <div class="pm-scroll">
                        <table class="pm-table">
                            <thead>
                                <tr>
                                    <th style="width:38px;" class="text-center">#</th>
                                    <th>Participante</th>
                                    <th class="text-end" style="width:15%">Matrícula</th>
                                    <th class="text-end" style="width:15%">Colegiatura</th>
                                    <th class="text-end" style="width:15%">2da Nivelación</th>
                                    <th class="text-end" style="width:15%">Total</th>
                                </tr>
                            </thead>
                            <tbody id="modalPagosBody">
                                <tr><td colspan="6" class="text-center text-muted py-3">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Inscritos del Mes (Historial) --}}
<div class="modal fade pm-modal" id="modalInscritos" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="pm-title" id="modalInscritosTitle">Inscritos del mes</h5>
                    <div class="pm-sub" id="modalInscritosSub">Detalle de inscripciones del mes</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="pm-stats" id="modalInscritosStats"></div>
                <div class="pm-wrap">
                    <div class="pm-toolbar">
                        <div class="pm-search">
                            <i class="ri-search-line"></i>
                            <input type="text" class="pm-search-input" data-target="modalInscritosBody" data-count="modalInscritosCount" placeholder="Buscar por nombre o CI..." autocomplete="off">
                        </div>
                        <span class="pm-countlbl" id="modalInscritosCount"></span>
                    </div>
                    <div class="pm-scroll">
                        <table class="pm-table">
                            <thead>
                                <tr>
                                    <th style="width:38px;" class="text-center">#</th>
                                    <th>Participante</th>
                                    <th class="text-center" style="width:16%">Plan</th>
                                    <th class="text-center" style="width:16%">Pagó</th>
                                </tr>
                            </thead>
                            <tbody id="modalInscritosBody">
                                <tr><td colspan="4" class="text-center text-muted py-3">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Sesión --}}
<div class="modal fade" id="modalEditarSesion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarSesion">
                @csrf
                <input type="hidden" name="horario_id" id="edit-horario-id">
                <input type="hidden" id="edit-sesion-modulo-id">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-14">
                                <i class="ri-edit-line"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold">Editar Sesión</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light rounded p-3 mb-3 fs-12" id="edit-sesion-info"></div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Estado</label>
                        <select name="estado" id="edit-estado" class="form-select">
                            <option value="Confirmado">Confirmado</option>
                            <option value="Programado">Programado</option>
                            <option value="Desarrollado">Desarrollado</option>
                            <option value="Postergado">Postergado</option>
                        </select>
                    </div>
                    <div class="mb-3" id="edit-fecha-container" style="display:none">
                        <label class="form-label text-muted fw-medium fs-12">Nueva Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="nueva_fecha" id="edit-nueva-fecha" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Observaciones</label>
                        <textarea name="observaciones" id="edit-observaciones" class="form-control" rows="3" placeholder="Agregar observaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarEdicion">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Fecha y Hora de la Sesión --}}
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

@if($puedeCambiarEstado)
{{-- Modal Editar Estado del Módulo --}}
<div class="modal fade" id="modalEditarModulo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarModulo">
                @csrf
                <input type="hidden" name="modulo_id" id="edit-modulo-id">
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
                        <select name="desarrollo" class="form-select" id="edit-modulo-desarrollo">
                            <option value="No Inicio">No Inicio</option>
                            <option value="En Desarrollo">En Desarrollo</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Suspendido">Suspendido</option>
                            <option value="Concluido">Concluido</option>
                        </select>
                    </div>
                    <div class="mb-0" id="edit-modulo-fecha-wrap" style="display:none;">
                        <label class="form-label text-muted fw-medium fs-12">Fecha de conclusión</label>
                        <input type="date" name="fecha_conclusion" id="edit-modulo-fecha-conclusion" class="form-control">
                        <small class="text-muted mt-1 d-block"><i class="ri-information-line"></i> Al concluir el módulo se habilitará la nivelación. La fecha límite para nivelar será 2 meses después de esta fecha.</small>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btnGuardarModulo">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Editar Enlace Moodle --}}
<div class="modal fade" id="modalEditarMoodle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarMoodle">
                @csrf
                <input type="hidden" name="modulo_id" id="edit-moodle-modulo-id">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-14">
                                <i class="ri-link"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold">Editar Enlace Moodle</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Enlace Moodle</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-link"></i></span>
                            <input type="url" name="moodle_link" id="edit-moodle-link" class="form-control" maxlength="500" placeholder="https://moodle.ejemplo.com/course/...">
                        </div>
                        <small class="text-muted mt-1 d-block"><i class="ri-information-line"></i> Deja en blanco si no tiene curso en Moodle.</small>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnGuardarMoodle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProgramaMoodle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-graduation-cap-line me-1"></i>Enlace Moodle del Programa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProgramaMoodle">
                @csrf
                <div class="modal-body" style="padding:1rem;">
                    <div class="mb-2">
                        <label class="form-label fs-12 text-muted">URL del curso en Moodle</label>
                        <input type="url" name="moodle_link" id="edit-programa-moodle-link" class="form-control" maxlength="500" placeholder="https://moodle.ejemplo.com/course/...">
                    </div>
                    <small class="text-muted">Deja en blanco para eliminar el enlace.</small>
                </div>
                <div class="modal-footer" style="padding:.65rem 1rem;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnGuardarProgramaMoodle">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999" id="toastContainer"></div>

@if($puedeAsignarDocente)
{{-- Modal Asignar Docente --}}
<div class="modal fade" id="modalAsignarDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <form id="formAsignarDocente">
                @csrf
                <input type="hidden" name="modulo_id" id="asigdoc-modulo-id">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-14">
                                <i class="ri-user-add-line"></i>
                            </span>
                        </span>
                        <h5 class="modal-title fs-14 fw-semibold" id="modalAsignarDocenteTitle">Asignar Docente</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">N° Carnet de Identidad</label>
                        <input type="text" name="ci" class="form-control form-control-sm" id="asigdoc-ci" placeholder="Ingrese el CI del docente" autocomplete="off">
                        <div class="mt-2" id="asigdoc-resultado"></div>
                    </div>
                    <div id="asigdoc-registro" style="display:none">
                        <hr class="my-2">
                        <h6 class="text-muted fs-12 fw-semibold mb-2">Registrar nuevo docente</h6>
                        <div class="mb-2">
                            <input type="text" name="nombres" class="form-control form-control-sm" placeholder="Nombres" id="asigdoc-nombres">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="apellidos" class="form-control form-control-sm" placeholder="Apellidos" id="asigdoc-apellidos">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col">
                                <input type="email" name="correo" class="form-control form-control-sm" placeholder="Correo (opcional)" id="asigdoc-correo">
                            </div>
                            <div class="col">
                                <input type="text" name="celular" class="form-control form-control-sm" placeholder="Celular (opcional)" id="asigdoc-celular">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-sm btn-primary" id="btnRegistrarDocente">
                                <i class="ri-save-line me-1"></i>Registrar y Asignar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light" id="asigdoc-footer" style="display:none">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-warning" id="btnAsignarDocente">
                        <i class="ri-user-add-line me-1"></i>Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if((int)$programa->estado_id === 4)
@can('inscripciones.registrar')
{{-- Modal Inscribir Participante --}}
<div class="modal fade" id="modalInscribirP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon"><i class="ri-user-add-line"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Inscribir Participante</h5>
                        <small class="modal-subtitle">{{ $programa->nombre }} · {{ $programa->codigo }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- 0. Asesor --}}
                <div class="inscribir-section">
                    <div class="inscribir-section-title">
                        <span class="inscribir-section-icon bg-soft-warning text-warning"><i class="ri-user-star-line"></i></span>
                        <span>Asesor que realiza la inscripción</span>
                    </div>
                    <div class="ci-search-box" style="position: relative;">
                        <i class="ri-user-search-line search-i"></i>
                        <input type="text" id="ip-asesor" placeholder="Buscar asesor por CI o nombre..." autocomplete="off" maxlength="60">
                        <div id="ip-asesor-dropdown" class="asesor-dropdown"></div>
                    </div>
                    <div id="ip-asesor-selected" class="mt-2"></div>
                    <input type="hidden" id="ip-asesor-id" value="">
                </div>

                {{-- 1. Búsqueda por CI del participante --}}
                <div class="inscribir-section">
                    <div class="inscribir-section-title">
                        <span class="inscribir-section-icon bg-soft-primary text-primary"><i class="ri-search-line"></i></span>
                        <span>Buscar Participante por CI</span>
                    </div>
                    <div class="ci-search-box">
                        <i class="ri-passport-line search-i"></i>
                        <input type="text" id="ip-ci" placeholder="Ingrese el CI (mín. 7 dígitos)..." autocomplete="off" maxlength="13">
                        <span class="ci-search-spinner"><span class="spinner-border spinner-border-sm" role="status"></span></span>
                    </div>
                    <div class="ci-result-box" id="ip-result"></div>
                </div>

                {{-- 2. Registrar nuevo (oculto hasta no encontrar) --}}
                <div class="inscribir-section" id="ip-section-nuevo" style="display:none;">
                    <div class="inscribir-section-title">
                        <span class="inscribir-section-icon bg-soft-warning text-warning"><i class="ri-user-add-line"></i></span>
                        <span>Registrar Nuevo Participante</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Nombres <span class="text-danger">*</span></label>
                            <input type="text" id="ip-nombres" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" id="ip-apellidos" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Sexo</label>
                            <select id="ip-sexo" class="form-select">
                                <option value="">—</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Celular</label>
                            <input type="text" id="ip-celular" class="form-control" maxlength="30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Correo</label>
                            <input type="email" id="ip-correo" class="form-control" maxlength="120">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Departamento</label>
                            <input type="text" id="ip-departamento" class="form-control" maxlength="80">
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-warning" id="btn-registrar-participante">
                                <i class="ri-save-3-line align-bottom"></i> Registrar y continuar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. Datos de inscripción (oculto hasta tener participante seleccionado) --}}
                <div id="ip-section-inscripcion" style="display:none;">
                    <div class="inscribir-section">
                        <div class="inscribir-section-title">
                            <span class="inscribir-section-icon bg-soft-success text-success"><i class="ri-money-dollar-circle-line"></i></span>
                            <span>Costos del Programa</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="monto-card">
                                    <div class="monto-icon bg-soft-primary text-primary"><i class="ri-bank-card-line"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="monto-label">Matrícula</div>
                                        <div class="monto-valor">Bs {{ number_format($programa->matricula ?? 0, 2) }}</div>
                                        <div class="monto-deuda ok" id="ip-deuda-mat">Deuda: Bs {{ number_format($programa->matricula ?? 0, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="monto-card">
                                    <div class="monto-icon bg-soft-info text-info"><i class="ri-funds-line"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="monto-label">Colegiatura</div>
                                        <div class="monto-valor">Bs {{ number_format($programa->colegiatura ?? 0, 2) }}</div>
                                        <div class="monto-deuda ok" id="ip-deuda-col">Deuda: Bs {{ number_format($programa->colegiatura ?? 0, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Descuento Matrícula</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" min="0" max="{{ $programa->matricula ?? 0 }}" id="ip-desc-mat" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Descuento Colegiatura</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" step="0.01" min="0" max="{{ $programa->colegiatura ?? 0 }}" id="ip-desc-col" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ip-contado">
                                    <label class="form-check-label small fw-semibold" for="ip-contado">
                                        <i class="ri-money-dollar-box-line text-success"></i> Pago al contado
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ip-traspaso">
                                    <label class="form-check-label small fw-semibold" for="ip-traspaso">
                                        <i class="ri-swap-line text-warning"></i> Es traspaso de otra unidad de negocio
                                    </label>
                                </div>
                            </div>
    </div>
</div>
                    @endcan


                    {{-- Distribución de Cuotas --}}
                    <div class="inscribir-section">
                        <div class="inscribir-section-title">
                            <span class="inscribir-section-icon bg-soft-info text-info"><i class="ri-stack-line"></i></span>
                            <span>Distribución de la Colegiatura en Cuotas</span>
                        </div>
                        @php
                            $totalMods = $modulos->count();
                            $cuotasCol = max(1, $totalMods - 1);
                        @endphp
                        <div class="cuotas-resumen-box">
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                                <span class="cuotas-pill" style="background:#dbeafe; color:#299cdb; border-color:#c3ddfa;">
                                    <i class="ri-bank-card-line"></i> 1<sup>er</sup> módulo: <strong>Matrícula</strong>
                                </span>
                                <span class="cuotas-pill"><i class="ri-stack-line"></i> <strong>{{ $cuotasCol }}</strong> cuota{{ $cuotasCol != 1 ? 's' : '' }} de colegiatura</span>
                                <span class="cuotas-pill"><i class="ri-money-dollar-circle-line"></i> Cuota: <strong id="ip-cuota-entera">Bs 0</strong></span>
                                <span class="cuotas-pill cuotas-pill-warning"><i class="ri-flag-line"></i> Última: <strong id="ip-cuota-ultima">Bs 0</strong></span>
                            </div>
                            <small class="text-muted d-block"><i class="ri-information-line"></i> El <strong>primer módulo</strong> contiene la cuota de <strong>matrícula</strong>; la <strong>colegiatura</strong> se reparte entre los módulos restantes (números enteros + saldo en la última).</small>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-soft-info" id="ip-toggle-cuotas">
                                    <i class="ri-eye-line align-bottom"></i> Ver desglose por módulo
                                </button>
                            </div>
                            <div id="ip-cuotas-detalle" class="cuotas-detalle mt-2" style="display:none;"></div>
                            <input type="hidden" id="ip-modulos-data" value='@json($modulos->values()->pluck("nombre"))'>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-inscribir-save" id="btn-inscribir-confirm" disabled>
                    <i class="ri-check-line align-bottom"></i> Inscribir Participante
                </button>
            </div>
        </div>
    </div>
</div>
                @endif

                @if((int)$programa->estado_id === 4)
@can('inscripciones.cambiarestado')
{{-- Modal Confirmar Toggle Inscripciones --}}
<div class="modal fade" id="modalToggleInsc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-body text-center p-4">
                <div id="ti-icon-wrap" style="width:80px; height:80px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:2.5rem;">
                    <i id="ti-icon" class="ri-checkbox-circle-line"></i>
                </div>
                <h4 class="mb-2 fw-bold" id="ti-title">¿Habilitar inscripciones?</h4>
                <p class="text-muted mb-3" id="ti-desc">Los participantes podrán inscribirse en este programa.</p>
                <div class="alert border-0 d-inline-flex align-items-start gap-2 py-2 px-3 mb-0 text-start" id="ti-alert" style="max-width: 460px; background: #eef1f8;">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <small class="mb-0" id="ti-alert-text">—</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4" style="background:#fff;">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn px-4" id="ti-confirm">
                    <i class="ri-check-line align-bottom"></i> Sí, continuar
                </button>
            </div>
        </div>
    </div>
</div>
@endcan

@can('programas.modulos.agregar')
{{-- Modal Agregar Módulos --}}
<div class="modal fade" id="modalAgregarModulos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%); color:#fff; border:0; padding:1.1rem 1.5rem; position:relative; overflow:hidden;">
                <div style="position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%); border-radius:50%;"></div>
                <div class="d-flex align-items-center gap-3" style="position:relative; z-index:1;">
                    <div style="width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:12px; font-size:1.25rem; color:#fff;">
                        <i class="ri-stack-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">Agregar Módulos</h5>
                        <small style="color: rgba(255,255,255,.85); font-size:.78rem;">{{ $programa->nombre }} · {{ $programa->codigo }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:.85; position:relative; z-index:1;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem; background:#fafbfd; max-height: calc(100vh - 230px); overflow-y: auto;">
                <div class="alert alert-soft-info border-0 d-flex align-items-start gap-2 py-2 px-3 mb-3" style="font-size:.82rem;">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <div>
                        Solo necesitas ingresar el <strong>nombre de cada módulo</strong>. Se crearán con estado
                        <strong>"No Inicio"</strong>, proceso de pago <strong>"Procesado no iniciado"</strong>, factura/montos en 0
                        y sin docente asignado.
                    </div>
                </div>

                <label class="form-label fw-semibold text-muted small mb-2" style="text-transform:uppercase; letter-spacing:.4px;">
                    <i class="ri-list-check"></i> Lista de Módulos a Crear
                </label>
                <div id="modulos-inputs"></div>

                <button type="button" class="btn btn-sm btn-soft-success mt-2" id="btn-agregar-fila-mod">
                    <i class="ri-add-line align-bottom"></i> Agregar otro módulo
                </button>
            </div>
            <div class="modal-footer" style="background:#fff; border-top:1px solid #eef0f4; padding:1rem 1.5rem;">
                <span class="text-muted me-auto" style="font-size:.78rem;"><i class="ri-stack-line"></i> <strong id="contador-mods">0</strong> módulo(s) en la lista</span>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-modulos">
                    <i class="ri-save-3-line align-bottom"></i> Crear módulos
                </button>
            </div>
        </div>
    </div>
</div>
@endcan
@endif

{{-- Modal Registrar Sesiones (Horarios) --}}
<div class="modal fade" id="modalRegistrarHorarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0ab39c 0%, #14b8a6 60%, #299cdb 100%); color:#fff; border:0; padding:1.1rem 1.5rem; position:relative; overflow:hidden;">
                <div style="position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%); border-radius:50%;"></div>
                <div class="d-flex align-items-center gap-3" style="position:relative; z-index:1;">
                    <div style="width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:12px; font-size:1.25rem; color:#fff;">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">Registrar Sesiones del Módulo</h5>
                        <small style="color: rgba(255,255,255,.85); font-size:.78rem;" id="rh-modulo-nombre">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:.85; position:relative; z-index:1;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem; background:#fafbfd;">
                <div class="alert alert-soft-success border-0 d-flex align-items-start gap-2 py-2 px-3 mb-3" style="font-size:.82rem;">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <div>
                        Se registrarán <strong id="rh-count-info">0</strong> sesiones (según <code>programas.cantidad_sesiones</code>).
                        Horario por defecto <strong>19:00 — 22:00</strong> y estado <strong>Confirmado</strong>.
                        El asignado se toma del <strong>responsable del programa</strong> automáticamente.
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.82rem;">
                        <thead style="background:#eef1f8;">
                            <tr>
                                <th style="width:60px;" class="text-center">N°</th>
                                <th>Fecha</th>
                                <th style="width:130px;">Hora Inicio</th>
                                <th style="width:130px;">Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody id="rh-tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff; border-top:1px solid #eef0f4; padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-horarios">
                    <i class="ri-save-3-line align-bottom"></i> Guardar Sesiones
                </button>
            </div>
        </div>
    </div>
</div>

@can('inscripciones.eliminar')
{{-- Modal Confirmar Eliminación de Inscripción --}}
<div class="modal fade" id="modalEliminarInscripcion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-body text-center p-4">
                <div style="width:80px; height:80px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #fff5f3 0%, #ffe7e2 100%); color:#f06548; border-radius:50%; font-size:2.5rem; box-shadow:0 0 0 8px rgba(240,101,72,.08);">
                    <i class="ri-error-warning-line"></i>
                </div>
                <h4 class="mb-2 fw-bold">¿Eliminar inscripción?</h4>
                <p class="text-muted mb-1">Estás a punto de eliminar la inscripción de</p>
                <p class="fw-semibold fs-15 text-dark mb-3" id="ei-nombre">—</p>
                <div class="alert alert-soft-danger border-0 d-inline-flex align-items-start gap-2 py-2 px-3 mb-0 text-start" style="max-width: 460px;">
                    <i class="ri-alert-line fs-5 mt-1"></i>
                    <small class="mb-0">
                        Se eliminarán los registros de <strong>inscripciones</strong> y <strong>modulos_participantes</strong> asociados a este programa.
                        El <strong>participante</strong> se mantendrá. Esta acción no se puede deshacer.
                    </small>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4" style="background:#fff;">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4" id="ei-confirm">
                    <i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Baja --}}
<div class="modal fade" id="modalToggleBaja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg, #f06548 0%, #dc3545 100%); color:#fff; border:0; padding:1.1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;">
                        <i class="ri-user-unfollow-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold" id="tbTitle">Gestión de Baja</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="tbParticipante">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="tbInscripcionId" value="">
                <input type="hidden" id="tbAccion" value="">
                <div id="tbInfoEstado" class="alert d-flex align-items-start gap-2 py-2 px-3 mb-3 border-0" style="display:none;border-radius:10px;">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <div><strong id="tbInfoTitle"></strong><br><small id="tbInfoDesc" class="text-muted"></small></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de baja</label>
                    <div class="d-flex gap-3" id="tbTipoGroup">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tbTipo" id="tbTipoTemprana" value="temprana">
                            <label class="form-check-label fw-medium" for="tbTipoTemprana">
                                <i class="ri-close-circle-line text-warning"></i> Temprana
                                <small class="d-block text-muted fs-11">Durante inscripciones</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tbTipo" id="tbTipoOperativa" value="operativa">
                            <label class="form-check-label fw-medium" for="tbTipoOperativa">
                                <i class="ri-error-warning-line text-danger"></i> Operativa
                                <small class="d-block text-muted fs-11">Durante desarrollo</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">Motivo <small class="text-muted">(opcional)</small></label>
                    <textarea class="form-control" id="tbMotivo" rows="2" maxlength="500" placeholder="Describa el motivo de la baja..."></textarea>
                </div>
                <div class="mb-3" id="tbExtrasGroup" style="display:none;">
                    <label class="form-label fw-semibold">Aspectos de la baja</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tbBajaAcademica">
                            <label class="form-check-label fw-medium" for="tbBajaAcademica">
                                <i class="ri-book-close-line text-warning"></i> Baja Académica
                                <small class="d-block text-muted fs-11">Registra la baja en el área académica</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tbBajaContable">
                            <label class="form-check-label fw-medium" for="tbBajaContable">
                                <i class="ri-money-dollar-circle-line text-danger"></i> Baja Contable
                                <small class="d-block text-muted fs-11">Registra la baja en el área contable</small>
                            </label>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label fw-semibold mb-1">Documento de respaldo <small class="text-muted">(PDF o imagen · máx. 5MB)</small></label>
                        <input type="file" class="form-control form-control-sm" id="tbArchivo" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <div id="tbArchivoNombre" class="text-muted mt-1 fs-11"></div>
                    </div>
                </div>
                <div class="mt-3" id="tbHistorialContainer" style="display:none;">
                    <hr>
                    <label class="form-label fw-semibold">Historial de cambios</label>
                    <div id="tbHistorialList" style="max-height:180px;overflow-y:auto;font-size:.82rem;"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between border-0 pt-0 pb-4 px-4">
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-light px-3" onclick="cargarHistorialBaja()">
                        <i class="ri-history-line"></i> Ver historial
                    </button>
                    <button type="button" class="btn btn-outline-primary px-3" id="tbPdfBtn">
                        <i class="ri-file-pdf-line"></i> Reporte PDF
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="button" class="btn px-3 text-white" id="tbConfirm" style="background:#f06548;">
                        <i class="ri-check-line align-bottom"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Modal Descargar Reporte de Baja --}}
<div class="modal fade" id="modalBajaArchivos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg, #0ab39c 0%, #0c8de4 100%); color:#fff; border:0; padding:1.1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;">
                        <i class="ri-file-download-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">Documentos de la Baja</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="bapParticipante">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-soft-info border-0 d-flex align-items-start gap-2 py-2 px-3 mb-3" style="border-radius:10px;">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <div><small class="mb-0">Descargue el reporte oficial de la baja y el documento de respaldo registrado.</small></div>
                </div>
                <div class="d-grid gap-3">
                    <a href="#" target="_blank" class="btn btn-outline-primary btn-lg d-flex justify-content-between align-items-center px-3" id="bapPdfBtn" style="border-radius:12px;">
                        <span class="d-flex align-items-center gap-2"><i class="ri-file-pdf-2-line fs-4"></i><span><span class="d-block fw-semibold">Reporte de la baja</span><small class="d-block text-muted">PDF con datos, historial y pagos</small></span></span>
                        <i class="ri-download-2-line fs-4"></i>
                    </a>
                    <div class="text-center text-muted" style="font-size:.8rem;">— o —</div>
                    <a href="#" class="btn btn-outline-secondary btn-lg d-flex justify-content-between align-items-center px-3" id="bapArchivoBtn" style="border-radius:12px;">
                        <span class="d-flex align-items-center gap-2"><i class="ri-attachment-line fs-4"></i><span><span class="d-block fw-semibold">Documento de respaldo</span><small class="d-block text-muted" id="bapArchivoNombre">Archivo adjuntado</small></span></span>
                        <i class="ri-download-2-line fs-4"></i>
                    </a>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@can('inscripciones.asesor.cambiar')
{{-- Modal Editar Inscripción (Asesor) --}}
<div class="modal fade" id="modalEditarInscripcion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color:#fff; border:0; padding:1.1rem 1.5rem; position:relative; overflow:hidden;">
                <div style="position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%); border-radius:50%;"></div>
                <div class="d-flex align-items-center gap-3" style="position:relative; z-index:1;">
                    <div style="width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:12px; font-size:1.25rem; color:#fff;">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-white fw-bold">Editar Inscripción</h5>
                        <small style="color: rgba(255,255,255,.85); font-size:.78rem;" id="ei-participante-nombre">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:.85; position:relative; z-index:1;"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem; background:#fafbfd;">
                <div class="inscribir-section">
                    <div class="inscribir-section-title">
                        <span class="inscribir-section-icon bg-soft-warning text-warning"><i class="ri-user-star-line"></i></span>
                        <span>Asesor que realizó la inscripción</span>
                    </div>
                    <div class="ci-search-box" style="position: relative;">
                        <i class="ri-user-search-line search-i"></i>
                        <input type="text" id="ei-asesor" placeholder="Buscar asesor por CI o nombre..." autocomplete="off" maxlength="60">
                        <div id="ei-asesor-dropdown" class="asesor-dropdown"></div>
                    </div>
                    <div id="ei-asesor-selected" class="mt-2"></div>
                    <input type="hidden" id="ei-asesor-id" value="">
                    <input type="hidden" id="ei-inscripcion-id" value="">
                </div>
            </div>
            <div class="modal-footer" style="background:#fff; border-top:1px solid #eef0f4; padding:1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="ei-guardar">
                    <i class="ri-save-3-line align-bottom"></i> Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endcan

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
                        <span class="ms-auto fs-12 text-muted" id="wa-sel-info"></span>
                    </div>
                    <div class="px-2 py-1 border-bottom fs-12 text-muted" id="wa-bajas-info" style="display:none;">
                        <i class="ri-user-unfollow-line text-danger"></i>
                        Los dados de baja se muestran en rojo y quedan sin seleccionar: no reciben el envío salvo que los marques.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light text-muted text-uppercase">
                                <tr>
                                    <th style="width:36px" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="wa-check-all" title="Seleccionar a todos los activos con nota">
                                    </th>
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
                <span class="text-muted fs-12 me-auto">Envío automático por Seguimiento ESAM, con pausas anti-baneo.</span>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btn-wa-esam-todos"><i class="ri-whatsapp-line"></i> Enviar por Seguimiento ESAM</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL PAGO DOCENTE ===== --}}
<div class="modal fade" id="modalPagoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border-radius:10px;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold" style="color:#fff;">Pago Docente · <span id="mpd-modulo-nombre"></span></h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">Configurar honorarios, retenciones y gastos extra</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="form-pago-docente" style="background:#fafbfd;">
                <input type="hidden" id="mpd-modulo-id">
                <div class="modal-body" style="padding:1.5rem;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                <i class="ri-money-dollar-circle-line text-dark"></i> Pago Docente (Bs)
                            </label>
                            <input type="number" class="form-control" id="mpd-pago-docente" step="0.01" min="0"
                                style="border-radius:8px;border-color:#e9ebec;font-weight:700;">
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mpd-factura" role="switch">
                                <label class="form-check-label fw-semibold" for="mpd-factura" style="font-size:.85rem;">
                                    <i class="ri-file-list-3-line"></i> Emite factura
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="mpd-iva-group">
                            <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                <i class="ri-percent-line text-danger"></i> IVA 13% (Bs)
                                <small class="text-muted fw-normal" style="text-transform:none;letter-spacing:0;">· retención automática</small>
                            </label>
                            <input type="number" class="form-control" id="mpd-iva" step="0.01" min="0" readonly
                                style="border-radius:8px;border-color:#e9ebec;font-weight:600;background:#f8f9fc;cursor:not-allowed;">
                        </div>
                        <div class="col-md-6" id="mpd-it-group">
                            <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                <i class="ri-percent-line text-warning"></i> IT 3% (Bs)
                                <small class="text-muted fw-normal" style="text-transform:none;letter-spacing:0;">· retención automática</small>
                            </label>
                            <input type="number" class="form-control" id="mpd-it" step="0.01" min="0" readonly
                                style="border-radius:8px;border-color:#e9ebec;font-weight:600;background:#f8f9fc;cursor:not-allowed;">
                        </div>
                        <div class="col-12">
                            <hr class="my-1" style="border-style:dashed;opacity:.4;">
                        </div>
                        <div class="col-12">
                            <div class="p-3" style="background:linear-gradient(135deg,#f8f9fc 0%,#eef1f8 100%);border-radius:12px;border:1px solid #e9ebec;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#878a99;">
                                            <i class="ri-calculator-line"></i> Total a Pagar
                                        </span>
                                        <div style="font-size:.62rem;color:#adb5bd;font-weight:500;margin-top:2px;">
                                            <span id="mpd-total-label-detalle">Pago Docente + Gastos Extra</span>
                                        </div>
                                    </div>
                                    <strong id="mpd-total-pagar" class="fw-bold" style="font-size:1.3rem;color:#0ab39c;">Bs 0.00</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row g-2 align-items-end">
                                <div class="col-7">
                                    <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                        <i class="ri-calendar-line"></i> Fecha de Pago
                                    </label>
                                    <input type="date" class="form-control" id="mpd-fecha-pago"
                                        style="border-radius:8px;border-color:#e9ebec;font-weight:600;font-size:.82rem;">
                                </div>
                                <div class="col-5 d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-soft-primary btn-mpd-fecha-prox" data-dia="10" style="font-size:.68rem;font-weight:600;padding:.3rem .5rem;flex:1;">
                                        <i class="ri-calendar-check-line"></i> Próx. 10
                                    </button>
                                    <button type="button" class="btn btn-sm btn-soft-primary btn-mpd-fecha-prox" data-dia="20" style="font-size:.68rem;font-weight:600;padding:.3rem .5rem;flex:1;">
                                        <i class="ri-calendar-check-line"></i> Próx. 20
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="mpd-regularizar">
                                    <label class="form-check-label" for="mpd-regularizar" style="font-size:.75rem;font-weight:600;">
                                        <i class="ri-history-line"></i> Regularizar
                                    </label>
                                </div>
                                <small class="text-muted" style="font-size:.65rem;">Permite seleccionar cualquier fecha</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:#878a99;">
                                <i class="ri-tools-line text-info"></i> Gastos Extra (Bs)
                                <small class="text-muted fw-normal" style="text-transform:none;letter-spacing:0;">· materiales, logística, refrigerios</small>
                            </label>
                            <input type="number" class="form-control" id="mpd-gastos-extra" step="0.01" min="0"
                                style="border-radius:8px;border-color:#e9ebec;font-weight:600;font-size:.82rem;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btn-mpd-save" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 100%);border:0;box-shadow:0 6px 16px -6px rgba(64,81,137,.55);">
                        <i class="ri-save-3-line"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Procesar Pago Docente (marcar como pagado) --}}
<div class="modal fade" id="modalProcesarPagoDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 60%,#299cdb 100%);color:#fff;border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border-radius:10px;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Procesar Pago Docente · <span id="ppd-modulo-nombre"></span></h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;">Confirma el pago al docente del módulo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="form-procesar-pago-docente" style="background:#fafbfd;">
                <input type="hidden" id="ppd-modulo-id">
                <div class="modal-body" style="padding:1.5rem;">
                    <div class="d-flex flex-column gap-3">
                        {{-- Resumen del pago --}}
                        <div class="p-3" style="background:#fff;border:1px solid #eef0f4;border-radius:12px;">
                            <h6 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#878a99;margin-bottom:.75rem;">
                                <i class="ri-information-line"></i> Resumen del Pago
                            </h6>
                            <div class="d-flex flex-column gap-2" style="font-size:.82rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Pago Docente</span>
                                    <strong id="ppd-resumen-monto" class="fw-bold">Bs 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Tipo</span>
                                    <strong id="ppd-resumen-tipo" class="fw-bold">—</strong>
                                </div>
                                <div id="ppd-resumen-retenciones" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size:.75rem;">IVA 13%</span>
                                        <strong id="ppd-resumen-iva-valor" class="fw-bold text-danger">Bs 0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size:.75rem;">IT 3%</span>
                                        <strong id="ppd-resumen-it-valor" class="fw-bold text-warning">Bs 0.00</strong>
                                    </div>
                                </div>
                                <div id="ppd-resumen-extra" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size:.75rem;">Gastos Extra</span>
                                        <strong id="ppd-resumen-extra-valor" class="fw-bold text-info">Bs 0.00</strong>
                                    </div>
                                </div>
                                <hr style="border-style:dashed;margin:.35rem 0;opacity:.4;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total a Pagar</span>
                                    <strong id="ppd-resumen-total" class="fw-bold" style="font-size:1rem;color:#0ab39c;">Bs 0.00</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha de pago --}}
                        <div>
                            <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                <i class="ri-calendar-line"></i> Fecha de Pago
                            </label>
                            <input type="date" class="form-control" id="ppd-fecha-pago"
                                style="border-radius:8px;border-color:#e9ebec;font-weight:600;"
                                value="{{ now()->toDateString() }}">
                        </div>

                        {{-- Proceso pago --}}
                        <div>
                            <label class="form-label" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6c757d;">
                                <i class="ri-file-list-3-line"></i> Detalle del Pago
                            </label>
                            <div class="p-3" style="background:#fff;border:1px solid #eef0f4;border-radius:10px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ppd-detalle-pago" id="ppd-detalle-factura" value="factura" checked>
                                        <label class="form-check-label fw-semibold" for="ppd-detalle-factura">
                                            <i class="ri-file-list-3-line text-primary"></i> Con Factura
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ppd-detalle-pago" id="ppd-detalle-retencion" value="retencion">
                                        <label class="form-check-label fw-semibold" for="ppd-detalle-retencion">
                                            <i class="ri-file-paper-line text-warning"></i> Con Retención
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size:.7rem;display:block;margin-top:.5rem;">
                                    <i class="ri-information-line"></i> Selecciona cómo se realizó el pago al docente.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line"></i> Cancelar
                    </button>
                    <button type="submit" class="btn d-flex align-items-center gap-1" id="btn-ppd-save" style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:600;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);">
                        <i class="ri-checkbox-circle-line"></i> Marcar como Pagado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Generar Deudas de Certificación --}}
<div class="modal fade" id="modalGenerarCert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0; border-radius:16px; overflow:hidden; box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-body text-center p-4">
                <div style="width:80px; height:80px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #e8faf1 0%, #d1fae5 100%); color:#0ab39c; border-radius:50%; font-size:2.5rem; box-shadow:0 0 0 8px rgba(10,179,156,.08);">
                    <i class="ri-award-line"></i>
                </div>
                <h4 class="mb-2 fw-bold">Generar Deudas de Certificación</h4>
                <p class="text-muted mb-3">Se crearán deudas según la modalidad de titulación de cada participante.</p>
                @php
                    $costoCertificacion = (float) ($programa->costo_certificacion ?? 0);
                    $costoCertPersonal = (float) ($programa->costo_certificacion_personal ?? 0);
                    $sinDeudaConvenio = 0;
                    $sinDeudaPersonal = 0;
                    foreach ($inscripciones as $insCert) {
                        if (!$certificacionDebts->has($insCert->participante_id)) {
                            if ((int)($insCert->modalidad_titulacion ?? 1) === 1) {
                                $sinDeudaConvenio++;
                            } else {
                                $sinDeudaPersonal++;
                            }
                        }
                    }
                    // Se calcula acá para no depender del tab "Pagos Certificación",
                    // que puede no renderizarse según los permisos del usuario.
                    $participantesSinCert = $sinDeudaConvenio + $sinDeudaPersonal;
                @endphp
                <div class="d-flex justify-content-center gap-4 mb-3">
                    <div class="text-center">
                        <div class="fs-2 fw-bold text-primary">Bs {{ number_format($costoCertificacion, 2) }}</div>
                        <small class="text-muted">Convenio</small>
                        <div><span class="badge bg-soft-primary text-primary fs-10">{{ $sinDeudaConvenio }} pend.</span></div>
                    </div>
                    <div class="text-center">
                        <div class="fs-2 fw-bold text-info">Bs {{ number_format($costoCertPersonal, 2) }}</div>
                        <small class="text-muted">Trámite Personal</small>
                        <div><span class="badge bg-soft-info text-info fs-10">{{ $sinDeudaPersonal }} pend.</span></div>
                    </div>
                </div>
                @if($participantesSinCert === 0)
                <div class="alert alert-soft-success border-0 d-inline-flex align-items-center gap-2 py-2 px-3 mb-0">
                    <i class="ri-checkbox-circle-line fs-5"></i>
                    <small>Todos los participantes ya tienen una deuda de certificación generada.</small>
                </div>
                @else
                <div class="alert alert-soft-warning border-0 d-inline-flex align-items-start gap-2 py-2 px-3 mb-0 text-start">
                    <i class="ri-information-line fs-5 mt-1"></i>
                    <small class="mb-0">Se generarán deudas para <strong>{{ $participantesSinCert }} participante{{ $participantesSinCert !== 1 ? 's' : '' }}</strong> según su modalidad de titulación.</small>
                </div>
                @endif
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4" style="background:#fff;">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="ri-close-line align-bottom"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success px-4" id="cert-gen-confirm" {{ $participantesSinCert === 0 ? 'disabled' : '' }}>
                    <i class="ri-refresh-line align-bottom"></i> Sí, generar deudas
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Registrar Pago Certificación --}}
<div class="modal fade" id="modalPagoCert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0ab39c 0%,#28c76f 100%);color:#fff;border:0;padding:1rem 1.25rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border-radius:8px;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-14 fw-semibold">Registrar Pago Certificación</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.72rem;" id="mpc-participante-nombre">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="form-pago-cert">
                <input type="hidden" id="mpc-participante-id">
                <input type="hidden" id="mpc-deuda-total">
                <input type="hidden" id="mpc-pagado-actual">
                <div class="modal-body" style="padding:1.25rem;">
                    <div class="d-flex justify-content-around mb-3 text-center">
                        <div>
                            <div class="fs-5 fw-bold text-primary">Bs <span id="mpc-deuda-show">0.00</span></div>
                            <small class="text-muted">Deuda total</small>
                        </div>
                        <div>
                            <div class="fs-5 fw-bold text-success">Bs <span id="mpc-pagado-show">0.00</span></div>
                            <small class="text-muted">Pagado</small>
                        </div>
                        <div>
                            <div class="fs-5 fw-bold" id="mpc-saldo-show" style="color:#f06548;">Bs 0.00</div>
                            <small class="text-muted">Saldo</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium fs-12">Monto del pago (Bs)</label>
                        <input type="number" class="form-control" id="mpc-monto" step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium fs-12">Fecha de pago</label>
                        <input type="date" class="form-control" id="mpc-fecha" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-top" style="background:#fafbfd;padding:.75rem 1.25rem;">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="btn-mpc-save">
                        <i class="ri-save-line me-1"></i>Guardar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Cambiar Modalidad Titulación --}}
<div class="modal fade" id="modalModalidadTitulacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b 0%,#f97316 100%);color:#fff;border:0;padding:1rem 1.5rem;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);border-radius:50%;"></div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.25);border-radius:10px;">
                        <i class="ri-git-commit-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Modalidad de Titulación</h5>
                        <small id="modalidad-subtitle" style="color:rgba(255,255,255,.85);font-size:.72rem;">Seleccione Convenio o Trámite Personal</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <div class="modal-body p-3" id="modalidad-body">
                <div id="modalidad-single-view" style="display:none;">
                    <div class="text-center mb-3">
                        <div style="width:64px;height:64px;margin:0 auto 0.75rem;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);color:#f59e0b;border-radius:50%;font-size:1.5rem;box-shadow:0 0 0 6px rgba(245,158,11,.1);">
                            <i class="ri-user-search-line"></i>
                        </div>
                        <h6 class="fw-bold mb-1 fs-15" id="modalidad-single-nombre">—</h6>
                        <span class="badge bg-soft-secondary text-secondary fs-10" id="modalidad-single-ci">—</span>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <div class="text-center px-3 py-2 rounded-3 border bg-light" style="min-width:100px;">
                            <small class="text-muted d-block fs-11">Actual</small>
                            <span class="fw-bold fs-14" id="modalidad-single-actual">—</span>
                        </div>
                        <div class="text-center px-3 py-2 rounded-3 border bg-light" style="min-width:100px;">
                            <small class="text-muted d-block fs-11">Nueva</small>
                            <select class="form-select form-select-sm border-0 bg-transparent fw-bold fs-14 text-primary text-center p-0" id="modalidad-single-select" style="cursor:pointer;">
                                <option value="1">Convenio</option>
                                <option value="0">Trámite Personal</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="modalidad-multi-view">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-muted">Participantes sin deuda de certificación</small>
                        <span class="badge bg-soft-primary text-primary fs-10" id="modalidad-count">0</span>
                    </div>
                    <div class="table-responsive" style="max-height:320px;overflow-y:auto;">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <thead class="table-light text-muted text-uppercase fs-11 sticky-top" style="background:#f8f9fa;">
                                <tr>
                                    <th style="width:30px;" class="text-center">#</th>
                                    <th>Participante</th>
                                    <th style="width:130px;" class="text-center">Modalidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seguimientoConcluido as $idx => $sc)
                                @if(!$sc['tiene_deuda_certificacion'])
                                <tr class="modalidad-row" data-participante="{{ $sc['participante_id'] }}">
                                    <td class="text-center text-muted fs-12">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-semibold fs-13">{{ $sc['apellidos'] }}, {{ $sc['nombres'] }}</span>
                                        <small class="text-muted d-block fs-10">{{ $sc['ci'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <select class="form-select form-select-sm modalidad-select" data-participante="{{ $sc['participante_id'] }}" style="width:130px;display:inline-block;font-size:.75rem;">
                                            <option value="1" {{ (int)($sc['modalidad_titulacion'] ?? 1) === 1 ? 'selected' : '' }}>Convenio</option>
                                            <option value="0" {{ (int)($sc['modalidad_titulacion'] ?? 1) === 0 ? 'selected' : '' }}>Trámite Personal</option>
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4 fs-13">No hay participantes disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-success px-3" id="btn-guardar-modalidad">
                    <i class="ri-save-line me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Nuevo Folio Universidad --}}
<div class="modal fade" id="modalRegistrarFolio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd 0%,#0a58ca 100%);color:#fff;border:0;padding:1rem 1.5rem;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);border-radius:50%;"></div>
                <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.25);border-radius:10px;">
                        <i class="ri-government-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fs-15 fw-semibold">Registrar Participantes</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.72rem;" id="mrfSubtitle">Seleccione participantes para agregar al folio</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="form-registrar-folio">
                <div class="modal-body p-3">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium fs-12">Folio</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-soft-primary text-primary fs-13 px-3 py-2" id="mrfFolioName">—</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium fs-12">Tipos de Pago del Folio</label>
                            <div class="d-flex flex-wrap gap-2 mt-1" id="mrfConceptos">
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-reg-concepto" value="Inscripcion" checked disabled>
                                    <label class="form-check-label fs-12">Inscripción</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-reg-concepto" value="Matricula" checked disabled>
                                    <label class="form-check-label fs-12">Matrícula</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-reg-concepto" value="Colegiatura" checked disabled>
                                    <label class="form-check-label fs-12">Colegiatura</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input folio-reg-concepto" value="Certificacion" checked disabled>
                                    <label class="form-check-label fs-12">Certificación</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-muted">Seleccione los participantes a registrar</small>
                        <input type="text" class="form-control form-control-sm" id="folio-reg-filtro" placeholder="Buscar..." style="width:180px;">
                    </div>

                    <div class="table-responsive" style="max-height:200px;overflow-y:auto;border:1px solid #eef0f4;border-radius:8px;">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <thead class="table-light text-muted text-uppercase fs-11 sticky-top" style="background:#f8f9fa;">
                                <tr>
                                    <th style="width:36px;" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="folio-reg-select-all">
                                    </th>
                                    <th>Participante</th>
                                    <th style="width:80px;" class="text-center">CI</th>
                                    <th style="width:160px;" class="text-center">Conflictos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inscripciones as $ins)
                                @php
                                    $conflictos = $conceptosRegistrados->get($ins->participante_id, collect());
                                @endphp
                                <tr class="folio-reg-row {{ ($ins->es_traspaso ?? 0) == 1 ? 'traspaso-row' : '' }}" data-search="{{ strtolower($ins->apellidos . ' ' . $ins->nombres . ' ' . $ins->ci) }}" data-participante="{{ $ins->participante_id }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input folio-reg-cb" value="{{ $ins->participante_id }}">
                                    </td>
                                    <td><span class="fs-13">{{ $ins->apellidos }}, {{ $ins->nombres }}@if(($ins->es_traspaso ?? 0) == 1) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</span></td>
                                    <td class="text-center text-muted fs-12">{{ $ins->ci }}</td>
                                    <td class="text-center folio-conflictos-cell">
                                        @if($conflictos->count() > 0)
                                            @foreach($conflictos as $conf)
                                                <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1 me-1" title="Folio: {{ $conf['folio_nombre'] }}">
                                                    <i class="ri-information-line"></i>
                                                    {{ implode(', ', $conf['conceptos']) }}
                                                    <small>({{ $conf['folio_nombre'] }})</small>
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted fs-11">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="folio-reg-conflict-alert" class="mt-2" style="display:none;">
                        <div class="alert alert-soft-warning border-0 py-2 px-3 mb-0 d-flex align-items-center gap-2">
                            <i class="ri-alert-line"></i>
                            <small id="folio-reg-conflict-text"></small>
                        </div>
                    </div>

                    <input type="hidden" id="folio-reg-id" value="">

                    <div class="row g-2 mt-3">
                        <div class="col-12">
                            <div class="bg-light rounded-3 p-3 border">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <small class="text-muted fw-medium">Costos a registrar (por participante)</small>
                                    <span class="badge bg-soft-primary text-primary fs-10" id="folio-reg-selected-count">0 seleccionados</span>
                                </div>
                                <div class="row g-2 mt-2 text-center">
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Inscripción</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-reg-total" id="folio-reg-total-insc">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Matrícula</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-reg-total" id="folio-reg-total-mat">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Colegiatura</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-reg-total" id="folio-reg-total-col">Bs 0.00</span>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="text-muted d-block fs-11">Certificación</small>
                                        </div>
                                        <span class="fw-bold fs-14 folio-reg-total" id="folio-reg-total-cert">Bs 0.00</span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-end">
                                    <span class="fw-bold fs-5 text-primary">Total: Bs <span id="folio-reg-total-general">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #eef0f4;padding:.75rem 1.25rem;">
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success px-3" id="btn-registrar-folio">
                        <i class="ri-save-line me-1"></i>Registrar Participantes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>{{-- /acad-page --}}
@endsection

@php
    $nivelacionNotasData = $nivelacionModulos->map(function ($nm) {
        return [
            'id' => $nm->id,
            'nombre' => $nm->nombre,
            'participantes' => $nm->rows
                ->filter(fn($r) => $r->tiene_cuota && (int) $r->comprobante_validado === 1)
                ->values()
                ->map(fn($r) => [
                    'mp_id' => $r->mp_id,
                    'ci' => $r->ci,
                    'nombre' => $r->apellidos . ', ' . $r->nombres,
                    'nota_modulo' => $r->nota_modulo,
                    'nota' => $r->nota_nivelacion !== null && $r->nota_nivelacion > 0 ? $r->nota_nivelacion : null,
                ])
                ->values()
                ->all(),
        ];
    })->values()->all();
@endphp

@push('js')
@include('admin.partials.whatsapp-lote-modal')
<script src="{{ asset('backend/assets/libs/chart.js/chart.umd.js') }}"></script>
<script>
function showToast(msg, type) {
    type = type || 'success';
    const colors = { success:'#0d8574', danger:'#b3243a', warning:'#b7791f', info:'#1f6fbf' };
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-white border-0';
    el.setAttribute('role','alert'); el.setAttribute('aria-live','assertive'); el.setAttribute('aria-atomic','true');
    el.style.background = colors[type] || colors.success;
    el.innerHTML = '<div class="d-flex"><div class="toast-body fs-12 fw-medium">' + msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    document.getElementById('toastContainer').appendChild(el);
    var t = new bootstrap.Toast(el, { delay: 4000 });
    t.show();
    el.addEventListener('hidden.bs.toast', function () { el.remove(); });
}

// ---- WhatsApp Masivo ----
// Listener propio: el modal funciona aunque otro bloque de la página falle.
document.addEventListener('DOMContentLoaded', function () {
    let whatsappMensajes = [];
    let whatsappModuloId = null;

    // Participantes marcados en el modal (los dados de baja empiezan sin marcar).
    function waSeleccionados() {
        return Array.prototype.map.call(
            document.querySelectorAll('#whatsapp-body .wa-sel:checked'),
            function (c) { return parseInt(c.value, 10); }
        );
    }

    function waActualizarSeleccion() {
        var checks = document.querySelectorAll('#whatsapp-body .wa-sel:not(:disabled)');
        var marcados = waSeleccionados().length;
        var info = document.getElementById('wa-sel-info');
        if (info) info.textContent = marcados + ' de ' + checks.length + ' seleccionado(s)';
        var all = document.getElementById('wa-check-all');
        if (all) {
            var activos = document.querySelectorAll('#whatsapp-body .wa-sel:not(:disabled):not([data-baja="1"])');
            var activosMarcados = document.querySelectorAll('#whatsapp-body .wa-sel:not(:disabled):not([data-baja="1"]):checked');
            all.checked = activos.length > 0 && activos.length === activosMarcados.length;
            all.indeterminate = activosMarcados.length > 0 && activosMarcados.length < activos.length;
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList && e.target.classList.contains('wa-sel')) {
            e.target.closest('tr').classList.toggle('opacity-50', !e.target.checked);
            waActualizarSeleccion();
        }
        if (e.target.id === 'wa-check-all') {
            document.querySelectorAll('#whatsapp-body .wa-sel:not(:disabled):not([data-baja="1"])').forEach(function (c) {
                c.checked = e.target.checked;
                c.closest('tr').classList.toggle('opacity-50', !c.checked);
            });
            waActualizarSeleccion();
        }
    });

    async function enviarWhatsappEsam(tipo, trigger) {
        if (! whatsappModuloId) return;
        var n = waSeleccionados().length;
        if (n === 0) { showToast('Seleccioná al menos un participante.', 'warning'); return; }
        if (! confirm('¿Enviar por WhatsApp con Seguimiento ESAM a ' + n + ' participante(s) seleccionado(s)' +
            (tipo === 'aprobado' ? ' (solo aprobados)' : tipo === 'reprobado' ? ' (solo reprobados)' : '') + '?')) return;
        if (trigger) trigger.disabled = true;
        try {
            const res = await fetch('{{ url("admin/programas/modulo") }}/' + whatsappModuloId + '/whatsapp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ tipo: tipo || 'all', participante_ids: waSeleccionados() }),
            });
            const data = await res.json().catch(function () { return {}; });
            if (res.ok && data.success) {
                var extra = (data.sin_telefono > 0) ? (' · sin teléfono: ' + data.sin_telefono) : '';
                if (data.batch_id) {
                    EsamLote.abrir(data.batch_id, 'Notas — Lote #' + data.batch_id + ' · ' + data.total + ' mensaje(s)' + extra);
                } else {
                    showToast('Encolados ' + data.total + ' mensaje(s). Lote #' + data.batch_id + extra + '.', 'success');
                }
            } else {
                showToast(data.message || 'No se pudo enviar.', 'danger');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'danger');
        } finally {
            if (trigger) trigger.disabled = false;
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-whatsapp-masivo');
        if (!btn) return;

        const moduloId = btn.dataset.moduloId;
        const moduloNombre = btn.dataset.moduloNombre;
        whatsappModuloId = moduloId;

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
                document.getElementById('whatsapp-body').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No hay participantes en este módulo</td></tr>';
                document.getElementById('whatsapp-content').style.display = 'block';
                waActualizarSeleccion();
                return;
            }

            whatsappMensajes = data.mensajes;
            var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (ch) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]; }); };
            var html = '';
            var hayBajas = false;
            data.mensajes.forEach(function (m, i) {
                var sinNota = m.tipo === 'sin_nota';
                var baja = !!m.baja;
                if (baja) hayBajas = true;
                // Activos con nota: marcados. Dados de baja: sin marcar. Sin nota: no se puede enviar.
                var marcado = !baja && !sinNota;
                var badgeClass = m.tipo === 'aprobado' ? 'bg-soft-success text-success' : (sinNota ? 'bg-light text-muted' : 'bg-soft-danger text-danger');
                var waCell = (m.celular && m.texto)
                    ? '<a href="https://wa.me/' + esc(m.celular) + '?text=' + encodeURIComponent(m.texto) + '" target="_blank" class="btn btn-sm ' + (baja ? 'btn-outline-secondary' : 'btn-success') + ' d-flex align-items-center gap-1" style="width:fit-content;margin:0 auto;">' +
                        '<i class="ri-whatsapp-line"></i> Abrir WhatsApp</a>'
                    : '<span class="text-muted fs-11">' + (m.celular ? '—' : 'Sin celular') + '</span>';
                html += '<tr class="' + (baja ? 'table-danger' : '') + (marcado ? '' : ' opacity-50') + '">' +
                    '<td class="text-center"><input type="checkbox" class="form-check-input wa-sel" value="' + esc(m.participante_id) + '"' +
                        (baja ? ' data-baja="1"' : '') + (marcado ? ' checked' : '') + (sinNota ? ' disabled title="Sin nota del módulo"' : '') + '></td>' +
                    '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                    '<td><span class="fw-semibold ' + (baja ? 'text-danger' : 'text-dark') + '">' + esc(m.apellidos) + ', ' + esc(m.nombres) + '</span>' +
                        (baja ? '<br><span class="badge bg-danger fs-10"><i class="ri-user-unfollow-line"></i> ' + esc(m.baja) + '</span>' : '') +
                    '</td>' +
                    '<td class="text-wrap" style="max-width:400px;font-size:.8rem;">' +
                        '<span class="badge ' + badgeClass + ' fs-10 me-1">' + (sinNota ? 'sin nota' : esc(m.tipo)) + '</span> ' +
                        (sinNota ? '<span class="text-muted">Sin nota del módulo: no se le envía mensaje.</span>' : esc(m.texto)) +
                    '</td>' +
                    '<td class="text-center">' + waCell + '</td>' +
                '</tr>';
            });
            document.getElementById('whatsapp-body').innerHTML = html;
            document.getElementById('wa-bajas-info').style.display = hayBajas ? 'block' : 'none';
            document.getElementById('whatsapp-content').style.display = 'block';
            waActualizarSeleccion();
        })
        .catch(function () {
            document.getElementById('whatsapp-loading').style.display = 'none';
            document.getElementById('whatsapp-body').innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar los datos</td></tr>';
            document.getElementById('whatsapp-content').style.display = 'block';
        });
    });

    document.getElementById('btn-wa-todos')?.addEventListener('click', function () {
        enviarWhatsappEsam('all', this);
    });

    document.getElementById('btn-wa-aprobados')?.addEventListener('click', function () {
        enviarWhatsappEsam('aprobado', this);
    });

    document.getElementById('btn-wa-reprobados')?.addEventListener('click', function () {
        enviarWhatsappEsam('reprobado', this);
    });

    document.getElementById('btn-wa-esam-todos')?.addEventListener('click', function () {
        enviarWhatsappEsam('all', this);
    });

});

document.addEventListener('DOMContentLoaded', function () {
    const COLORS = ['#163b6b','#cba135','#1f6fbf','#0d8574','#b7791f','#7d9ec8','#b3243a','#2a5d97','#4c7bb0','#ddb84d'];
    const ES_SUPER_ADMIN = @json($esSuperAdmin);
    const PUEDE_ASIGNAR_DOCENTE = @json($puedeAsignarDocente);
    let charts = {};

    // ---- Buscador en tiempo real de inscritos ----
    var buscador = document.getElementById('buscador-inscritos');
    var contador = document.getElementById('contador-inscritos');
    if (buscador) {
        var tablaRows = document.querySelectorAll('.tabla-inscritos tbody tr');
        var totalRows = tablaRows.length;
        if (contador) contador.textContent = totalRows;

        buscador.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var visible = 0;
            tablaRows.forEach(function (row) {
                if (row.cells.length < 3) { row.style.display = ''; visible++; return; }
                var texto = row.textContent.toLowerCase();
                if (q === '' || texto.indexOf(q) !== -1) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            if (contador) contador.textContent = visible;
        });
    }

    function fmt(n) {
        var p = Number(n).toFixed(2).split('.');
        p[0] = p[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return p.join('.');
    }

    const MESES_ES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    function mesEs(val) {
        if (!val) return '—';
        var m = String(val).match(/^(\d{4})-(\d{2})/);
        if (m) return (MESES_ES[parseInt(m[2], 10) - 1] || m[2]) + ' del ' + m[1];
        return val;
    }
    function fechaEs(val) {
        if (!val) return '—';
        var m = String(val).match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (m) return parseInt(m[3], 10) + ' de ' + (MESES_ES[parseInt(m[2], 10) - 1] || m[2]) + ' del ' + m[1];
        return val;
    }

    function adjustStickyCols() {
        var wrap = document.querySelector('.table-desarrollo-wrap');
        if (!wrap) return;
        var table = wrap.querySelector('.table-desarrollo');
        if (!table) return;
        var firstRow = table.querySelector('thead tr') || table.querySelector('tbody tr');
        if (!firstRow) return;
        var refCells = firstRow.querySelectorAll('.sticky-col');
        if (refCells.length < 3) return;
        var leftVal = 0;
        var leftVals = [];
        refCells.forEach(function (cell) {
            leftVals.push(leftVal);
            leftVal += cell.offsetWidth;
        });
        table.querySelectorAll('tr').forEach(function (row) {
            var cols = row.querySelectorAll('.sticky-col');
            cols.forEach(function (cell, idx) {
                if (leftVals[idx] !== undefined) {
                    cell.style.left = leftVals[idx] + 'px';
                }
            });
        });
    }

    function getChartTheme() {
        var dark = document.body.getAttribute('data-layout-mode') === 'dark'
            || document.documentElement.getAttribute('data-bs-theme') === 'dark';
        return {
            dark: dark,
            text: dark ? '#b3bcc9' : '#5b6572',
            strong: dark ? '#eef1f5' : '#1f2733',
            muted: '#8b95a3',
            grid: dark ? '#2a303a' : '#eef0f4',
            stroke: dark ? '#1b1f27' : '#ffffff',
            tooltipBg: dark ? '#21262f' : '#1f2733',
            tooltipText: '#ffffff'
        };
    }

    const chartThemePlugin = {
        id: 'avipTheme',
        beforeDraw(chart) {
            const t = getChartTheme();
            const p = chart.options.plugins || {};
            if (p.legend && p.legend.labels) p.legend.labels.color = t.text;
            if (p.tooltip) {
                p.tooltip.backgroundColor = t.tooltipBg;
                p.tooltip.titleColor = t.tooltipText;
                p.tooltip.bodyColor = t.tooltipText;
                p.tooltip.borderColor = t.grid;
                p.tooltip.borderWidth = 1;
            }
            if (chart.options.scales) {
                Object.keys(chart.options.scales).forEach(function (k) {
                    const sc = chart.options.scales[k];
                    if (sc && sc.ticks) sc.ticks.color = t.text;
                    if (sc && sc.grid) sc.grid.color = t.grid;
                    if (sc && sc.border) sc.border.color = t.grid;
                    if (sc && sc.title) sc.title.color = t.text;
                });
            }
            if (chart.config.type === 'doughnut') {
                chart.data.datasets.forEach(function (ds) {
                    ds.borderColor = t.stroke;
                    ds.borderWidth = 2;
                });
            }
        }
    };

    Chart.register(chartThemePlugin);
    Chart.defaults.font.family = 'Manrope, system-ui, -apple-system, "Segoe UI", sans-serif';
    Chart.defaults.color = getChartTheme().text;

    const centerPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            if (chart.config.type !== 'doughnut') return;
            const cfg = chart.options.plugins?.centerText;
            if (!cfg) return;
            const { ctx, chartArea: { top, left, right, bottom } } = chart;
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;
            const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            const isCurrency = cfg.currency;
            const label = cfg.label || '';
            const displayVal = isCurrency ? fmt(total) : total;
            const t = getChartTheme();
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = '800 20px Manrope, system-ui, sans-serif';
            ctx.fillStyle = t.strong;
            ctx.fillText(displayVal, centerX, centerY - (label ? 4 : 0));
            if (label) {
                ctx.font = '600 10px Manrope, system-ui, sans-serif';
                ctx.fillStyle = t.muted;
                ctx.fillText(label, centerX, centerY + 18);
            }
            ctx.restore();
        }
    };

    Chart.register(centerPlugin);

    const chartOpts = {
        responsive: true, maintainAspectRatio: true,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11, weight: '600' } }
            },
            tooltip: {
                padding: 10, cornerRadius: 8, boxPadding: 4,
                titleFont: { size: 11, weight: '700' }, bodyFont: { size: 11 },
                callbacks: { label: ctx => ' ' + ctx.raw + ' inscritos' }
            }
        }
    };

    function initCharts() {
        if (!document.getElementById('chart-estado')) return;
        charts.estado = new Chart(document.getElementById('chart-estado'), {
            type: 'doughnut',
            data: {
                labels: ['Operativos', 'Bajas Tempranas', 'Bajas Operativas', 'Bajas Académicas'],
                datasets: [{ data: [{{ $resumen['operativos'] }}, {{ $resumen['bajas_tempranas'] }}, {{ $resumen['bajas_operativas'] }}, {{ $resumen['bajas_academicas'] }}], backgroundColor: COLORS.slice(0, 4), borderWidth: 0 }]
            },
            options: chartOpts
        });

        charts.plan = new Chart(document.getElementById('chart-plan'), {
            type: 'doughnut',
            data: {
                labels: ['Plan Contado', 'Plan Regular'],
                datasets: [{ data: [{{ $resumen['planes_contado'] }}, {{ $resumen['planes_regulares'] }}], backgroundColor: ['#1f6fbf', '#2a5d97'], borderWidth: 0 }]
            },
            options: chartOpts
        });

        charts.sexo = new Chart(document.getElementById('chart-sexo'), {
            type: 'doughnut',
            data: {
                labels: ['Hombres', 'Mujeres'],
                datasets: [{ data: [{{ $hombres }}, {{ $mujeres }}], backgroundColor: ['#1f6fbf', '#cba135'], borderWidth: 0 }]
            },
            options: chartOpts
        });

        const deptos = @json($departamentos);
        charts.depto = new Chart(document.getElementById('chart-depto'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(deptos),
                datasets: [{ data: Object.values(deptos), backgroundColor: COLORS.slice(0, Object.keys(deptos).length), borderWidth: 0 }]
            },
            options: chartOpts
        });
    }

    initCharts();

    // Re-tema de gráficos al cambiar claro/oscuro
    (function () {
        var lastDark = getChartTheme().dark;
        function apply() {
            var d = getChartTheme().dark;
            if (d === lastDark) return;
            lastDark = d;
            Chart.defaults.color = getChartTheme().text;
            document.querySelectorAll('canvas').forEach(function (c) {
                var ch = Chart.getChart(c);
                if (ch) ch.update('none');
            });
        }
        if (window.MutationObserver) {
            new MutationObserver(apply).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
            new MutationObserver(apply).observe(document.body, { attributes: true, attributeFilter: ['data-layout-mode'] });
        }
    })();

    // ---- Matrícula Charts (lazy init on tab shown) ----
    let matriculaCharts = {};

    function initMatriculaCharts() {
        if (matriculaCharts.estado) return;

        function mkOpts(currency) {
            return {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11, weight: '600' } } },
                    tooltip: {
                        padding: 10, cornerRadius: 8, boxPadding: 4,
                        titleFont: { size: 11, weight: '700' }, bodyFont: { size: 11 },
                        callbacks: {
                            label: ctx => {
                                const v = ctx.raw;
                                return currency ? ' Bs ' + fmt(v) : ' ' + v + ' participante' + (v !== 1 ? 's' : '');
                            }
                        }
                    },
                    centerText: { currency, label: currency ? 'Total (Bs)' : 'Total' }
                },
                cutout: '72%',
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 8
            };
        }

        matriculaCharts.estado = new Chart(document.getElementById('chart-matricula-estado'), {
            type: 'doughnut',
            data: {
                labels: ['Pagados', 'Pendientes'],
                datasets: [{ data: [{{ $matriculaStats['pagados_count'] }}, {{ $matriculaStats['pendientes_count'] }}], backgroundColor: ['#0d8574', '#b3243a'] }]
            },
            options: mkOpts(false)
        });

        matriculaCharts.cobrado = new Chart(document.getElementById('chart-matricula-cobrado'), {
            type: 'doughnut',
            data: {
                labels: ['Cobrado', 'Pendiente'],
                datasets: [{ data: [{{ $matriculaStats['cobrado_modulos'] }}, {{ $matriculaStats['pendiente_modulos'] }}], backgroundColor: ['#0d8574', '#b3243a'] }]
            },
            options: mkOpts(true)
        });
    }

    document.querySelector('[data-bs-toggle="tab"][href="#sub-tab-matriculas"]')?.addEventListener('shown.bs.tab', initMatriculaCharts);

    // ---- Colegiatura Charts (lazy init on tab shown) ----
    let colegiaturaCharts = {};

    function initColegiaturaCharts() {
        if (colegiaturaCharts.estado) return;

        function mkOpts(currency) {
            return {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 11, weight: '600' } } },
                    tooltip: {
                        padding: 10, cornerRadius: 8, boxPadding: 4,
                        titleFont: { size: 11, weight: '700' }, bodyFont: { size: 11 },
                        callbacks: {
                            label: ctx => {
                                const v = ctx.raw;
                                return currency ? ' Bs ' + fmt(v) : ' ' + v + ' participante' + (v !== 1 ? 's' : '');
                            }
                        }
                    },
                    centerText: { currency, label: currency ? 'Total (Bs)' : 'Total' }
                },
                cutout: '72%',
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 8
            };
        }

        colegiaturaCharts.estado = new Chart(document.getElementById('chart-colegiatura-estado'), {
            type: 'doughnut',
            data: {
                labels: ['Pagados', 'Pendientes'],
                datasets: [{ data: [{{ $colegiaturaStats['pagados_count'] }}, {{ $colegiaturaStats['pendientes_count'] }}], backgroundColor: ['#0d8574', '#b3243a'] }]
            },
            options: mkOpts(false)
        });

        colegiaturaCharts.cobrado = new Chart(document.getElementById('chart-colegiatura-cobrado'), {
            type: 'doughnut',
            data: {
                labels: ['Cobrado', 'Pendiente'],
                datasets: [{ data: [{{ $colegiaturaStats['cobrado_modulos'] }}, {{ $colegiaturaStats['pendiente_modulos'] }}], backgroundColor: ['#0d8574', '#b3243a'] }]
            },
            options: mkOpts(true)
        });
    }

    document.querySelector('[data-bs-toggle="tab"][href="#sub-tab-colegiaturas"]')?.addEventListener('shown.bs.tab', initColegiaturaCharts);

    // ---- Fase Formativa Charts (lazy init on parent tab shown) ----
    let faseCharts = {};

    function initFaseCharts() {
        if (faseCharts.estado) return;
        try {
            faseCharts.estado = new Chart(document.getElementById('chart-fase-estado'), {
                type: 'doughnut',
                data: {
                    labels: ['Operativos', 'Bajas Operativas', 'Bajas Académicas'],
                    datasets: [{ data: [{{ $faseOperativos }}, {{ $resumen['bajas_operativas'] }}, {{ $resumen['bajas_academicas'] }}], backgroundColor: ['#0d8574', '#b3243a', '#b7791f'], borderWidth: 0 }]
                },
                options: chartOpts
            });

            faseCharts.plan = new Chart(document.getElementById('chart-fase-plan'), {
                type: 'doughnut',
                data: {
                    labels: ['Plan Contado', 'Plan Regular'],
                    datasets: [{ data: [{{ $faseContado }}, {{ $faseRegular }}], backgroundColor: ['#1f6fbf', '#2a5d97'], borderWidth: 0 }]
                },
                options: chartOpts
            });

            faseCharts.sexo = new Chart(document.getElementById('chart-fase-sexo'), {
                type: 'doughnut',
                data: {
                    labels: ['Hombres', 'Mujeres'],
                    datasets: [{ data: [{{ $faseHombres }}, {{ $faseMujeres }}], backgroundColor: ['#1f6fbf', '#cba135'], borderWidth: 0 }]
                },
                options: chartOpts
            });

            var faseDeptos = @json($faseInscripciones->groupBy('departamento')->map->count());
            faseCharts.depto = new Chart(document.getElementById('chart-fase-depto'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(faseDeptos),
                    datasets: [{ data: Object.values(faseDeptos), backgroundColor: COLORS.slice(0, Object.keys(faseDeptos).length), borderWidth: 0 }]
                },
                options: chartOpts
            });
        } catch (e) {}
    }

    document.querySelector('[data-bs-toggle="tab"][href="#tab-fase-formativa"]')?.addEventListener('shown.bs.tab', function () { setTimeout(initFaseCharts, 150); });

    let faseMatriculaCharts = {};

    function initFaseMatriculaCharts() {
        if (faseMatriculaCharts.estado) return;
        try {
            faseMatriculaCharts.estado = new Chart(document.getElementById('chart-fase-matricula-estado'), {
                type: 'doughnut',
                data: {
                    labels: ['Pagados', 'Pendientes'],
                    datasets: [{ data: [{{ $faseMatStats['pagados_count'] }}, {{ $faseMatStats['pendientes_count'] }}], backgroundColor: ['#0d8574', '#b3243a'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, cutout: '70%' }
            });

            faseMatriculaCharts.cobrado = new Chart(document.getElementById('chart-fase-matricula-cobrado'), {
                type: 'doughnut',
                data: {
                    labels: ['Deuda Actual', 'Cobrado'],
                    datasets: [{ data: [{{ $faseMatStats['pendiente'] }}, {{ $faseMatStats['pagado'] }}], backgroundColor: ['#1f6fbf', '#0d8574'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, cutout: '70%' }
            });
        } catch (e) {}
    }

    document.querySelector('[data-bs-toggle="tab"][href="#sub-fase-matriculas"]')?.addEventListener('shown.bs.tab', initFaseMatriculaCharts);

    let faseColegiaturaCharts = {};

    function initFaseColegiaturaCharts() {
        if (faseColegiaturaCharts.estado) return;
        try {
            faseColegiaturaCharts.estado = new Chart(document.getElementById('chart-fase-colegiatura-estado'), {
                type: 'doughnut',
                data: {
                    labels: ['Pagados', 'Pendientes'],
                    datasets: [{ data: [{{ $faseColStats['pagados_count'] }}, {{ $faseColStats['pendientes_count'] }}], backgroundColor: ['#0d8574', '#b3243a'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, cutout: '70%' }
            });

            faseColegiaturaCharts.cobrado = new Chart(document.getElementById('chart-fase-colegiatura-cobrado'), {
                type: 'doughnut',
                data: {
                    labels: ['Cobrado', 'Pendiente'],
                    datasets: [{ data: [{{ $faseColStats['pagado'] }}, {{ $faseColStats['pendiente'] }}], backgroundColor: ['#0d8574', '#b3243a'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, cutout: '70%' }
            });
        } catch (e) {}
    }

    document.querySelector('[data-bs-toggle="tab"][href="#sub-fase-colegiaturas"]')?.addEventListener('shown.bs.tab', initFaseColegiaturaCharts);

    // ---- Buscador en tiempo real de inscritos fase formativa ----
    var buscadorFase = document.getElementById('buscador-fase-inscritos');
    var contadorFase = document.getElementById('contador-fase-inscritos');
    if (buscadorFase) {
        var tablaFaseRows = document.querySelectorAll('.tabla-fase-inscritos tbody tr');
        var totalFaseRows = tablaFaseRows.length;
        if (contadorFase) contadorFase.textContent = totalFaseRows;

        buscadorFase.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            var visible = 0;
            tablaFaseRows.forEach(function (row) {
                if (row.cells.length < 3) { row.style.display = ''; visible++; return; }
                var texto = row.textContent.toLowerCase();
                if (q === '' || texto.indexOf(q) !== -1) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            if (contadorFase) contadorFase.textContent = visible;
        });
    }

    // ---- Helpers modales Pagos/Inscritos ----
    const pmAvatarColors = ['#163b6b', '#0d8574', '#1f6fbf', '#b3243a', '#b7791f', '#2a5d97', '#4c7bb0', '#7d9ec8'];
    function pmEsc(s) {
        return (s == null ? '' : String(s))
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    function pmAvatarCell(nombres, apellidos, ci) {
        var nombre = ((apellidos || '') + ', ' + (nombres || '')).replace(/^, /, '').replace(/, $/, '');
        var iniciales = (((nombres || ' ').charAt(0)) + ((apellidos || ' ').charAt(0))).toUpperCase().trim() || '?';
        var colorIdx = 0;
        for (var i = 0; i < nombre.length; i++) colorIdx = (colorIdx + nombre.charCodeAt(i)) % pmAvatarColors.length;
        return '<div class="pm-part-cell">' +
               '<span class="pm-avatar" style="background:' + pmAvatarColors[colorIdx] + ';">' + pmEsc(iniciales) + '</span>' +
               '<div style="min-width:0;"><div class="pm-part-name">' + pmEsc(nombre) + '</div>' +
               '<div class="pm-part-ci">CI: ' + pmEsc(ci || '—') + '</div></div></div>';
    }
    function pmStat(ico, bg, color, num, lbl) {
        return '<div class="pm-stat">' +
               '<div class="pm-stat-ico" style="background:' + bg + ';color:' + color + ';"><i class="' + ico + '"></i></div>' +
               '<div style="min-width:0;"><div class="num">' + num + '</div><div class="lbl">' + lbl + '</div></div>' +
               '</div>';
    }
    // Buscador compartido de los modales pm-
    document.addEventListener('input', function (e) {
        const input = e.target.closest('.pm-search-input');
        if (!input) return;
        const tbody = document.getElementById(input.dataset.target);
        if (!tbody) return;
        const q = input.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr[data-search]');
        let visibles = 0;
        rows.forEach(function (r) {
            const show = !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
            r.style.display = show ? '' : 'none';
            if (show) visibles++;
        });
        const cnt = document.getElementById(input.dataset.count);
        if (cnt) cnt.textContent = visibles + ' de ' + rows.length;
    });

    // ---- Pago detail modal ----
    const pagosData = @json($pagosAgrupados ?? collect());
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-pagos');
        if (!btn) return;
        const mes = btn.dataset.mes;
        const items = (pagosData[mes] || []).filter(function (p) { return p.total > 0; });
        const tbody = document.getElementById('modalPagosBody');
        document.getElementById('modalPagosTitle').textContent = 'Pagos de ' + mesEs(mes);
        document.getElementById('modalPagosSub').textContent = items.length + ' participante' + (items.length !== 1 ? 's' : '') + ' con pagos este mes';

        const totMat = items.reduce((s, p) => s + (parseFloat(p.matricula) || 0), 0);
        const totCol = items.reduce((s, p) => s + (parseFloat(p.colegiatura) || 0), 0);
        const totNiv = items.reduce((s, p) => s + (parseFloat(p.nivelacion) || 0), 0);
        const statsEl = document.getElementById('modalPagosStats');
        statsEl.innerHTML =
            pmStat('ri-group-line', '#eef1f8', '#405189', items.length, 'Participantes') +
            pmStat('ri-bank-card-line', '#dde7ff', '#1c3e9e', 'Bs ' + fmt(totMat), 'Matrícula') +
            pmStat('ri-funds-line', '#d4f6ee', '#0a7059', 'Bs ' + fmt(totCol), 'Colegiatura') +
            pmStat('ri-refund-2-line', '#fff4dc', '#9a6b00', 'Bs ' + fmt(totNiv), '2da Nivelación') +
            pmStat('ri-money-dollar-circle-line', '#e8e5ff', '#5b4bc4', 'Bs ' + fmt(totMat + totCol + totNiv), 'Total del mes');

        const searchInput = document.querySelector('.pm-search-input[data-target="modalPagosBody"]');
        if (searchInput) searchInput.value = '';
        document.getElementById('modalPagosCount').textContent = items.length + ' de ' + items.length;

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>No hay pagos en este mes</td></tr>';
        } else {
            tbody.innerHTML = items.map((p, i) => '<tr data-search="' + pmEsc(((p.apellidos || '') + ' ' + (p.nombres || '') + ' ' + (p.ci || '')).toLowerCase()) + '">' +
                '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                '<td>' + pmAvatarCell(p.nombres, p.apellidos, p.ci) + '</td>' +
                '<td class="text-end fw-medium ' + (p.matricula > 0 ? 'text-primary' : 'text-muted') + '">' + (p.matricula > 0 ? 'Bs ' + fmt(p.matricula) : '—') + '</td>' +
                '<td class="text-end fw-medium ' + (p.colegiatura > 0 ? 'text-success' : 'text-muted') + '">' + (p.colegiatura > 0 ? 'Bs ' + fmt(p.colegiatura) : '—') + '</td>' +
                '<td class="text-end fw-medium ' + (p.nivelacion > 0 ? 'text-warning' : 'text-muted') + '">' + (p.nivelacion > 0 ? 'Bs ' + fmt(p.nivelacion) : '—') + '</td>' +
                '<td class="text-end fw-bold text-dark">Bs ' + fmt(p.total) + '</td>' +
                '</tr>').join('');
        }
        new bootstrap.Modal(document.getElementById('modalPagos')).show();
    });

    // ---- Historial Charts (lazy init on tab shown) ----
    let historialInit = false;
    document.querySelector('[data-bs-toggle="tab"][href="#tab-historial"]')?.addEventListener('shown.bs.tab', function () {
        if (historialInit) return;
        historialInit = true;

        const labelsH = @json($histLabelsEs ?? []);
        const contadoH = @json($historialInscripciones ? $historialInscripciones->pluck('contado') : []);
        const regularH = @json($historialInscripciones ? $historialInscripciones->pluck('regular') : []);

        function stackTotalLabels(formatFn) {
            return function (chart) {
                const { ctx } = chart;
                const meta0 = chart.getDatasetMeta(0);
                meta0.data.forEach(function (bar, i) {
                    let total = 0;
                    let topY = bar.y;
                    chart.data.datasets.forEach(function (ds, di) {
                        const v = parseFloat(ds.data[i]) || 0;
                        total += v;
                        const m = chart.getDatasetMeta(di);
                        if (m.data[i] && m.data[i].y < topY) topY = m.data[i].y;
                    });
                    if (total <= 0) return;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    const t = getChartTheme();
                    ctx.font = '700 10px Manrope, system-ui, sans-serif';
                    ctx.fillStyle = t.strong;
                    ctx.shadowColor = t.dark ? 'rgba(0,0,0,.5)' : 'rgba(255,255,255,.9)';
                    ctx.shadowBlur = 3;
                    ctx.fillText(formatFn(total), bar.x, topY - 3);
                    ctx.restore();
                });
            };
        }
        new Chart(document.getElementById('chart-historial'), {
            type: 'bar',
            data: {
                labels: labelsH,
                datasets: [
                    { label: 'Contado', data: contadoH, backgroundColor: '#1f6fbf', borderRadius: 4, maxBarThickness: 42 },
                    { label: 'Regular', data: regularH, backgroundColor: '#2a5d97', borderRadius: 4, maxBarThickness: 42 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 16 } },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 }, usePointStyle: true, pointStyle: 'circle' } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.raw + ' inscrito' + (ctx.raw !== 1 ? 's' : ''),
                            footer: items => 'Total: ' + items.reduce((s, it) => s + (parseFloat(it.raw) || 0), 0)
                        }
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {
                        stacked: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { stepSize: 1, font: { size: 10 } }
                    }
                }
            },
            plugins: [{ afterDraw: stackTotalLabels(v => v) }]
        });
    });

    // ---- Contabilidad Charts (lazy init on tab shown) ----
    let contabilidadInit = false;
    document.querySelector('[data-bs-toggle="tab"][href="#tab-contabilidad"]')?.addEventListener('shown.bs.tab', function () {
        if (contabilidadInit) return;
        contabilidadInit = true;

        const labels = @json($labelsIngresos ?? []);
        const matData = @json($dataMatricula ?? []);
        const colData = @json($dataColegiatura ?? []);
        const nivData = @json($dataNivelacionIng ?? []);

        function ingresosTotalLabels(chart) {
            const { ctx } = chart;
            const meta0 = chart.getDatasetMeta(0);
            meta0.data.forEach(function (bar, i) {
                let total = 0;
                let topY = bar.y;
                chart.data.datasets.forEach(function (ds, di) {
                    const v = parseFloat(ds.data[i]) || 0;
                    total += v;
                    const m = chart.getDatasetMeta(di);
                    if (m.data[i] && m.data[i].y < topY) topY = m.data[i].y;
                });
                if (total <= 0) return;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.font = '700 10px system-ui, sans-serif';
                ctx.fillStyle = '#2a2f43';
                ctx.shadowColor = 'rgba(255,255,255,.9)';
                ctx.shadowBlur = 3;
                ctx.fillText(fmt(total), bar.x, topY - 3);
                ctx.restore();
            });
        }

        new Chart(document.getElementById('chart-ingresos-mes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Matrícula', data: matData, backgroundColor: '#1f6fbf', borderRadius: 4, maxBarThickness: 42 },
                    { label: 'Colegiatura', data: colData, backgroundColor: '#0d8574', borderRadius: 4, maxBarThickness: 42 },
                    { label: '2da Nivelación', data: nivData, backgroundColor: '#b7791f', borderRadius: 4, maxBarThickness: 42 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 16 } },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 }, usePointStyle: true, pointStyle: 'circle' } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.dataset.label + ': Bs ' + fmt(ctx.raw),
                            footer: items => 'Total: Bs ' + fmt(items.reduce((s, it) => s + (parseFloat(it.raw) || 0), 0))
                        }
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {
                        stacked: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { callback: v => 'Bs ' + fmt(v), font: { size: 10 } }
                    }
                }
            },
            plugins: [{ afterDraw: ingresosTotalLabels }]
        });
    });

    // ---- Inscritos detail modal ----
    const inscritosData = @json($inscripcionesAgrupadas ?? collect());
    const pagMatLookup = @json($pagoMatriculaPorParticipante ?? collect());
    const pagColLookup = @json($pagoColegiaturaPorParticipante ?? collect());
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-inscritos');
        if (!btn) return;
        const mes = btn.dataset.mes;
        const items = inscritosData[mes] || [];
        const tbody = document.getElementById('modalInscritosBody');
        document.getElementById('modalInscritosTitle').textContent = 'Inscritos de ' + mesEs(mes);
        document.getElementById('modalInscritosSub').textContent = items.length + ' inscripcion' + (items.length !== 1 ? 'es' : '') + ' registrada' + (items.length !== 1 ? 's' : '') + ' este mes';

        let nContado = 0, nRegular = 0, nPagaron = 0;
        const rows = items.map(function (ins, i) {
            var pM = pagMatLookup[ins.participante_id] || {pagado:0};
            var pC = pagColLookup[ins.participante_id] || {pagado:0};
            var siPago = pM.pagado > 0 || pC.pagado > 0;
            if (ins.contado) nContado++; else nRegular++;
            if (siPago) nPagaron++;
            return '<tr data-search="' + pmEsc(((ins.apellidos || '') + ' ' + (ins.nombres || '') + ' ' + (ins.ci || '')).toLowerCase()) + '">' +
                '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                '<td>' + pmAvatarCell(ins.nombres, ins.apellidos, ins.ci) + '</td>' +
                '<td class="text-center"><span class="pm-pill" style="' + (ins.contado ? 'background:#dde7ff;color:#1c3e9e;' : 'background:#e8e5ff;color:#5b4bc4;') + '">' + (ins.contado ? 'Contado' : 'Regular') + '</span></td>' +
                '<td class="text-center"><span class="pm-pill" style="' + (siPago ? 'background:#d4f6ee;color:#0a7059;' : 'background:#eef0f5;color:#5d6478;') + '">' + (siPago ? '<i class="ri-checkbox-circle-line"></i> Sí' : 'No') + '</span></td>' +
                '</tr>';
        });

        const statsEl = document.getElementById('modalInscritosStats');
        const pctPago = items.length > 0 ? Math.round(nPagaron * 100 / items.length) : 0;
        statsEl.innerHTML =
            pmStat('ri-group-line', '#eef1f8', '#405189', items.length, 'Inscritos') +
            pmStat('ri-money-dollar-box-line', '#dde7ff', '#1c3e9e', nContado, 'Plan Contado') +
            pmStat('ri-calendar-todo-line', '#e8e5ff', '#5b4bc4', nRegular, 'Plan Regular') +
            pmStat('ri-checkbox-circle-line', '#d4f6ee', '#0a7059', nPagaron + ' (' + pctPago + '%)', 'Han pagado');

        const searchInput = document.querySelector('.pm-search-input[data-target="modalInscritosBody"]');
        if (searchInput) searchInput.value = '';
        document.getElementById('modalInscritosCount').textContent = items.length + ' de ' + items.length;

        tbody.innerHTML = items.length === 0
            ? '<tr><td colspan="4" class="text-center text-muted py-4"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i>No hay inscritos en este mes</td></tr>'
            : rows.join('');
        new bootstrap.Modal(document.getElementById('modalInscritos')).show();
    });

    // ---- Seguimiento Académico ----
    const horariosData = @json($horariosModulos);

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-sesiones');
        if (!btn) return;

        const moduloId = parseInt(btn.dataset.moduloId);
        const moduloNombre = btn.dataset.moduloNombre;
        document.getElementById('modalSesionesTitle').textContent = moduloNombre;
        document.getElementById('modalSesionesCount').textContent = 'Sesiones';

        const sesiones = horariosData.filter(h => h.modulo_id === moduloId);
        const dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
        const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        document.getElementById('modalSesionesCount').textContent = sesiones.length + (sesiones.length === 1 ? ' sesión' : ' sesiones');
        document.getElementById('modalSesionesFooterCount').textContent = 'Total: ' + sesiones.length + (sesiones.length === 1 ? ' sesión' : ' sesiones');

        const rows = sesiones.map((h, i) => {
            const partes = h.fecha_desarrollo.split('-');
            const fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
            const fechaStr = dias[fechaObj.getDay()] + ' ' + partes[2] + ' de ' + meses[fechaObj.getMonth()] + ' del ' + partes[0];

            const esOverdue = h.estado === 'Confirmado' && fechaObj < hoy;

            const asignado = h.asignado_nombres ? h.asignado_nombres + ' ' + h.asignado_apellidos : '—';
            const obs = h.observaciones ? h.observaciones : '<span class="text-muted fst-italic">Sin observaciones</span>';
            const rowClass = esOverdue ? 'table-danger' : '';
            const obsAttr = h.observaciones ? h.observaciones.replace(/'/g, '&#39;') : '';

            return `<tr class="${rowClass}" data-horario-id="${h.id}">
                <td class="text-center text-muted fw-semibold">${i + 1}</td>
                <td class="fw-medium text-dark">${fechaStr}</td>
                <td class="text-center fw-medium text-primary">${h.hora_inicio ? h.hora_inicio.substring(0, 5) : '<span class="text-muted">—</span>'}</td>
                <td class="text-center fw-medium text-danger">${h.hora_fin ? h.hora_fin.substring(0, 5) : '<span class="text-muted">—</span>'}</td>
                <td class="fw-medium text-dark">${asignado}</td>
                <td class="text-center" id="estado-cell-${h.id}">
                    <span class="badge bg-${esOverdue ? 'danger' : 'success'} fs-10 fw-semibold px-2 py-1">${esOverdue ? 'Vencido' : h.estado}</span>
                    ${h.tipo === 'Nivelacion' ? '<div class="mt-1"><span class="badge fs-10 fw-semibold px-2 py-1" style="background:#f7b84b;color:#fff;"><i class="ri-refund-2-line me-1"></i>Nivelación</span></div>' : ''}
                </td>
                <td class="text-dark fs-12">${obs}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        ${'<button class="btn btn-sm btn-icon btn-soft-primary btn-editar-sesion" data-horario-id="' + h.id + '" data-modulo-id="' + h.modulo_id + '" data-estado="' + h.estado + '" data-vencido="' + esOverdue + '" data-observaciones="' + obsAttr + '" data-fecha="' + h.fecha_desarrollo + '" data-hora-inicio="' + (h.hora_inicio || '') + '" data-hora-fin="' + (h.hora_fin || '') + '" data-asignado="' + asignado + '" title="Cambiar estado"><i class="ri-flag-line fs-13"></i></button>'}
                        ${'<button class="btn btn-sm btn-icon btn-soft-warning btn-editar-fecha-sesion" data-horario-id="' + h.id + '" data-modulo-id="' + h.modulo_id + '" data-fecha="' + h.fecha_desarrollo + '" data-hora-inicio="' + (h.hora_inicio || '') + '" data-hora-fin="' + (h.hora_fin || '') + '" title="Editar fecha y hora"><i class="ri-calendar-line fs-13"></i></button>'}
                    </div>
                </td>
            </tr>`;
        });

        document.getElementById('modalSesionesBody').innerHTML = rows.length
            ? rows.join('')
            : '<tr><td colspan="8" class="text-center text-muted py-4"><i class="ri-inbox-2-line ri-2x d-block mb-2"></i><span class="fs-12">No hay sesiones registradas para este módulo</span></td></tr>';

        new bootstrap.Modal(document.getElementById('modalSesiones')).show();
    });

    // ---- Nivelación: configurar por módulo ----
    const nivelacionModulosData = @json($nivelacionModulos);
    const modalNivelacionEl = document.getElementById('modalNivelacion');
    const formNivelacion = document.getElementById('formNivelacion');
    if (formNivelacion && modalNivelacionEl) {
        const modalNivelacion = new bootstrap.Modal(modalNivelacionEl);

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-configurar-nivelacion');
            if (!btn) return;
            const modId = parseInt(btn.dataset.moduloId);
            const mod = nivelacionModulosData.find(m => parseInt(m.id) === modId);
            if (!mod) return;

            document.getElementById('niv-modulo-id').value = mod.id;
            document.getElementById('niv-modulo-nombre').textContent = mod.nombre;

            const docenteFijo = document.getElementById('niv-docente-fijo');
            if (mod.docente && mod.docente.apellidos) {
                docenteFijo.querySelector('span').textContent = mod.docente.apellidos + ', ' + mod.docente.nombres;
            } else {
                docenteFijo.querySelector('span').textContent = 'Sin docente asignado al módulo';
            }

            const honorarioPorParticipante = Number(mod.pago_docente_por_participante || 0);
            document.getElementById('niv-honorario-por-participante').querySelector('span').textContent = 'Bs ' + honorarioPorParticipante.toFixed(2);

            const tbody = document.getElementById('niv-participantes-body');
            const rows = mod.rows || [];
            document.getElementById('niv-participantes-count').textContent = rows.length;
            if (rows.length) {
                tbody.innerHTML = rows.map(function (r) {
                    return '<tr>' +
                        '<td class="text-center"><input type="checkbox" name="participantes[]" value="' + r.participante_id + '" class="form-check-input niv-check-participante"' + (r.tiene_cuota ? ' checked' : '') + '></td>' +
                        '<td><div class="fw-semibold fs-13">' + String(r.apellidos).replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }) + ', ' + String(r.nombres).replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }) + '</div><div class="text-muted fs-12"><i class="ri-passport-line"></i> ' + String(r.ci).replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }) + '</div></td>' +
                        '<td class="text-center"><span class="badge bg-soft-danger text-danger">' + Number(r.nota_modulo).toFixed(2) + '</span></td>' +
                        '<td class="text-center">' + (r.nota_nivelacion > 0 ? '<span class="badge bg-soft-danger text-danger">' + Number(r.nota_nivelacion).toFixed(2) + '</span>' : '<span class="text-muted">—</span>') + '</td>' +
                        '<td class="text-center">' + (r.tiene_cuota ? '<span class="badge bg-soft-warning text-warning">Generada</span>' : '<span class="text-muted">—</span>') + '</td>' +
                    '</tr>';
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay participantes reprobados en este módulo</td></tr>';
            }
            actualizarHonorarioNivelacion();
            modalNivelacion.show();
        });

        function actualizarHonorarioNivelacion() {
            const checks = document.querySelectorAll('.niv-check-participante');
            const seleccionados = Array.prototype.filter.call(checks, function (c) { return c.checked; }).length;
            const porParticipante = parseFloat(document.getElementById('niv-honorario-por-participante').querySelector('span').textContent.replace(/[^0-9.\-]/g, '')) || 0;
            const total = seleccionados * porParticipante;
            document.getElementById('niv-honorario-total').textContent = 'Bs ' + total.toFixed(2);
            document.getElementById('niv-honorario-count').textContent = seleccionados;
        }

        document.addEventListener('change', function (e) {
            if (e.target.classList && e.target.classList.contains('niv-check-participante')) {
                actualizarHonorarioNivelacion();
            }
        });

        const checkTodos = document.getElementById('niv-check-todos');
        if (checkTodos) {
            checkTodos.addEventListener('change', function () {
                document.querySelectorAll('.niv-check-participante').forEach(function (c) {
                    c.checked = checkTodos.checked;
                });
                actualizarHonorarioNivelacion();
            });
        }

        formNivelacion.addEventListener('submit', function (e) {
            e.preventDefault();
            const modId = document.getElementById('niv-modulo-id').value;
            if (!modId) { showToast('Selecciona el módulo', 'warning'); return; }
            const btn = document.getElementById('btnGuardarNivelacion');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            fetch('{{ route("admin.programas.modulo-nivelacion-programa", ["programa" => $programa->id, "modulo" => "MID"]) }}'.replace('MID', modId), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(this)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    modalNivelacion.hide();
                    showToast(data.message || 'Nivelación actualizada', 'success');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    showToast(data.message || 'Error al guardar', 'danger');
                }
            })
            .catch(function () { showToast('Error de conexión', 'danger'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
            });
        });
    }

    // ---- Nivelación: agregar horario (por módulo) ----
    const modalHorarioNivEl = document.getElementById('modalHorarioNivelacion');
    const formHorarioNiv = document.getElementById('formHorarioNivelacion');
    if (formHorarioNiv && modalHorarioNivEl) {
        const modalHorarioNiv = new bootstrap.Modal(modalHorarioNivEl);
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-agregar-horario-nivelacion');
            if (!btn) return;
            formHorarioNiv.reset();
            document.getElementById('niv-horario-modulo-id').value = btn.dataset.moduloId;
            document.getElementById('niv-horario-modulo-nombre').textContent = btn.dataset.moduloNombre;
            document.getElementById('niv-horario-modulo-nombre-display').value = btn.dataset.moduloNombre;
            modalHorarioNiv.show();
        });
        formHorarioNiv.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnGuardarHorarioNivelacion');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            fetch('{{ route("admin.programas.nivelacion-horario", $programa->id) }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(this)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    modalHorarioNiv.hide();
                    if (data.nuevo_horario) horariosData.push(data.nuevo_horario);
                    showToast(data.message || 'Sesión de nivelación agregada', 'success');
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    showToast(data.message || 'Error al guardar', 'danger');
                }
            })
            .catch(function () { showToast('Error de conexión', 'danger'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
            });
        });
    }

    // ---- Nivelación: subir/ver/validar comprobante de pago por participante ----
    const nivelacionFileInput = document.createElement('input');
    nivelacionFileInput.type = 'file';
    nivelacionFileInput.accept = 'image/*,.pdf,.jpg,.jpeg,.png,.webp';
    nivelacionFileInput.style.display = 'none';
    document.body.appendChild(nivelacionFileInput);
    let nivelacionCuotaActual = null;
    let nivelacionValidarCuota = null;
    const modalCompNiv = document.getElementById('modalComprobanteNivelacion') ? new bootstrap.Modal(document.getElementById('modalComprobanteNivelacion')) : null;

    function reemplazarFilaNivelacion(rowHtml) {
        if (!rowHtml) return;
        const temp = document.createElement('tbody');
        temp.innerHTML = rowHtml.trim();
        const nueva = temp.firstElementChild;
        if (!nueva || !nueva.id) return;
        const actual = document.getElementById(nueva.id);
        if (actual) {
            actual.replaceWith(nueva);
            return;
        }
        const cuerpo = document.querySelector('#modalNivelacion tbody') ||
            document.querySelector('.nivelacion-table tbody') ||
            (document.querySelector('.table-responsive tbody'));
        if (cuerpo) {
            const filas = cuerpo.querySelectorAll('tr');
            const ref = Array.from(filas).find(function (f) {
                const btn = f.querySelector('.btn-subir-comprobante-nivelacion, .btn-validar-pago-nivelacion, .btn-ver-comprobante-nivelacion');
                return btn && btn.dataset.cuotaId === nueva.id.replace('niv-fila-', '');
            });
            if (ref) ref.replaceWith(nueva); else cuerpo.appendChild(nueva);
        }
    }

    function cargarComprobanteEnModal(url, name, participante, mostrarValidar) {
        if (!modalCompNiv) return;
        document.getElementById('mcnSubtitle').textContent = participante || 'Nivelación';
        document.getElementById('mcnArchivo').innerHTML = '<i class="ri-file-download-line me-1"></i>' + (name || 'comprobante');
        const link = document.getElementById('mcnDescargar');
        link.href = url;
        link.setAttribute('download', name || 'comprobante');
        const preview = document.getElementById('mcnPreview');
        const lower = (name || '').toLowerCase();
        if (lower.endsWith('.pdf')) {
            preview.innerHTML = '<iframe src="' + url + '" style="width:100%;height:60vh;border:0;border-radius:8px;"></iframe>';
        } else {
            preview.innerHTML = '<img src="' + url + '" class="img-fluid rounded" style="max-height:60vh;" alt="Comprobante">';
        }
        document.getElementById('btnValidarComprobanteNivelacion').style.display = mostrarValidar ? '' : 'none';
        modalCompNiv.show();
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-subir-comprobante-nivelacion');
        if (!btn) return;
        nivelacionCuotaActual = btn.dataset.cuotaId;
        nivelacionFileInput.value = '';
        nivelacionFileInput.click();
    });

    nivelacionFileInput.addEventListener('change', function () {
        if (!nivelacionCuotaActual || !this.files.length) return;
        const file = this.files[0];
        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('comprobante', file);
        showToast('Subiendo comprobante...', 'info');

        fetch('{{ url("admin/programas/cuota") }}/' + nivelacionCuotaActual + '/comprobante-nivelacion', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (r) {
            if (r.ok && r.data.success) {
                showToast(r.data.message, 'success');
                reemplazarFilaNivelacion(r.data.row_html);
            } else {
                showToast(r.data.message || 'No se pudo subir el comprobante', 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); });
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-comprobante-nivelacion');
        if (!btn) return;
        cargarComprobanteEnModal(btn.dataset.url, btn.dataset.name, btn.dataset.participante, false);
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-validar-pago-nivelacion');
        if (!btn) return;
        nivelacionValidarCuota = btn.dataset.cuotaId;
        cargarComprobanteEnModal(btn.dataset.url, btn.dataset.name, btn.dataset.participante, true);
    });

    const btnValidarCompNiv = document.getElementById('btnValidarComprobanteNivelacion');
    if (btnValidarCompNiv) {
        btnValidarCompNiv.addEventListener('click', function () {
            if (!nivelacionValidarCuota) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Validando...';
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fetch('{{ url("admin/programas/cuota") }}/' + nivelacionValidarCuota + '/validar-pago-nivelacion', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd,
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (r.ok && r.data.success) {
                    modalCompNiv.hide();
                    showToast(r.data.message, 'success');
                    reemplazarFilaNivelacion(r.data.row_html);
                } else {
                    showToast(r.data.message || 'No se pudo validar el pago', 'danger');
                }
            })
            .catch(function () { showToast('Error de conexión', 'danger'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-double-line me-1"></i>Validar pago';
            });
        });
    }

    // ---- Nivelación: registrar notas de nivelación de todos los validados (masivo) ----
    const nivelacionNotasData = @json($nivelacionNotasData);
    const modalNotasNiv = document.getElementById('modalNotasNivelacion') ? new bootstrap.Modal(document.getElementById('modalNotasNivelacion')) : null;
    let nivelacionNotasModuloId = null;

    function reemplazarFilasNivelacion(rowsHtml) {
        if (!rowsHtml) return;
        Object.keys(rowsHtml).forEach(function (mpId) {
            reemplazarFilaNivelacion(rowsHtml[mpId]);
        });
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-notas-nivelacion');
        if (!btn) return;
        nivelacionNotasModuloId = btn.dataset.moduloId;
        const mod = nivelacionNotasData.find(function (m) { return String(m.id) === String(nivelacionNotasModuloId); });
        document.getElementById('mnnMasivoSubtitle').textContent = (mod ? mod.nombre : 'Módulo') + ' · ' + (mod ? mod.participantes.length : 0) + ' participante(s) con pago validado';
        const cuerpo = document.getElementById('mnnMasivoCuerpo');
        if (!mod || mod.participantes.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay participantes con pago validado</td></tr>';
        } else {
            cuerpo.innerHTML = mod.participantes.map(function (p, i) {
                return '<tr>' +
                    '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                    '<td class="fw-medium">' + p.ci + '</td>' +
                    '<td class="fw-medium">' + p.nombre + '</td>' +
                    '<td class="text-center"><span class="nota-cell bg-soft-danger-badge text-danger">' + Number(p.nota_modulo).toFixed(2) + '</span></td>' +
                    '<td class="text-center"><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm notas-nivelacion-input" data-mp-id="' + p.mp_id + '" value="' + (p.nota !== null ? Number(p.nota).toFixed(2) : '') + '" placeholder="0.00" style="width:110px;margin:0 auto;"></td>' +
                    '</tr>';
            }).join('');
        }
        modalNotasNiv.show();
    });

    const btnGuardarNotasNiv = document.getElementById('btnGuardarNotasNivelacion');
    if (btnGuardarNotasNiv) {
        btnGuardarNotasNiv.addEventListener('click', function () {
            if (!nivelacionNotasModuloId) return;
            const inputs = document.querySelectorAll('#mnnMasivoCuerpo .notas-nivelacion-input');
            if (!inputs.length) return;
            const notas = {};
            let hayNota = false;
            inputs.forEach(function (inp) {
                const val = inp.value.trim();
                const n = parseFloat(val);
                if (val === '' || isNaN(n)) {
                    notas[inp.dataset.mpId] = null;
                    return;
                }
                if (n < 0 || n > 100) { showToast('La nota debe estar entre 0 y 100 (' + inp.dataset.mpId + ')', 'warning'); return; }
                notas[inp.dataset.mpId] = n;
                hayNota = true;
            });
            if (inputs.length && !hayNota) { showToast('Ingresa al menos una nota', 'warning'); return; }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            Object.keys(notas).forEach(function (mpId) {
                fd.append('notas[' + mpId + ']', notas[mpId] === null ? '' : notas[mpId]);
            });
            fetch('{{ url("admin/programas/modulo") }}/' + nivelacionNotasModuloId + '/notas-nivelacion', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd,
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (r.ok && r.data.success) {
                    modalNotasNiv.hide();
                    showToast(r.data.message, 'success');
                    reemplazarFilasNivelacion(r.data.rows_html);
                } else {
                    showToast(r.data.message || 'No se pudieron guardar las notas', 'danger');
                }
            })
            .catch(function () { showToast('Error de conexión', 'danger'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-3-line me-1"></i> Guardar todas las notas';
            });
        });
    }

    // ---- Editar sesión (abrir modal) ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-editar-sesion');
        if (!btn) return;

        document.getElementById('edit-horario-id').value = btn.dataset.horarioId;
        document.getElementById('edit-sesion-modulo-id').value = btn.dataset.moduloId;
        document.getElementById('edit-observaciones').value = btn.dataset.observaciones;
        document.getElementById('edit-fecha-container').style.display = 'none';
        document.getElementById('edit-nueva-fecha').value = '';

        const editEstado = document.getElementById('edit-estado');
        const esVencido = btn.dataset.vencido === 'true';
        const estadoActual = btn.dataset.estado;
        if (esVencido) {
            editEstado.innerHTML = '<option value="Desarrollado">Desarrollado</option>';
        } else {
            editEstado.innerHTML =
                '<option value="Confirmado">Confirmado</option>' +
                '<option value="Programado">Programado</option>' +
                '<option value="Desarrollado">Desarrollado</option>' +
                '<option value="Postergado">Postergado</option>';
        }
        editEstado.value = esVencido ? 'Desarrollado' : estadoActual;

        const fe = btn.dataset.fecha;
        const hi = btn.dataset.horaInicio;
        const hf = btn.dataset.horaFin;
        const asig = btn.dataset.asignado;
        const dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
        const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        let fechaFormateada = fe;
        if (fe) {
            const p = fe.split('-');
            const fechaObj = new Date(p[0], p[1] - 1, p[2]);
            fechaFormateada = dias[fechaObj.getDay()] + ', ' + parseInt(p[2]) + ' de ' + meses[parseInt(p[1]) - 1] + ' del ' + p[0];
        }
        document.getElementById('edit-sesion-info').innerHTML =
            '<strong>Fecha:</strong> ' + fechaFormateada + '<br>' +
            '<strong>Horario:</strong> ' + (hi ? hi.substring(0,5) : '—') + ' - ' + (hf ? hf.substring(0,5) : '—') + '<br>' +
            '<strong>Asignado:</strong> ' + asig;

        new bootstrap.Modal(document.getElementById('modalEditarSesion')).show();
    });

    // ---- Mostrar/ocultar fecha reprogramación ----
    document.addEventListener('change', function (e) {
        if (e.target.id !== 'edit-estado') return;
        document.getElementById('edit-fecha-container').style.display = e.target.value === 'Postergado' ? 'block' : 'none';
    });

    // ---- Guardar cambios de sesión ----
    document.getElementById('formEditarSesion')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarEdicion');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch('{{ route("admin.programas.horario-estado") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEditarSesion')).hide();
                    showToast(data.message || 'Sesión actualizada correctamente', 'success');
                    // Actualizar datos locales
                const id = parseInt(document.getElementById('edit-horario-id').value);
                const idx = horariosData.findIndex(function (h) { return h.id === id; });
                if (idx !== -1) {
                    horariosData[idx].estado = document.getElementById('edit-estado').value;
                    horariosData[idx].observaciones = document.getElementById('edit-observaciones').value;
                }
                // Si se creó un nuevo horario (Postergado), agregarlo a la lista local
                if (data.nuevo_horario) {
                    horariosData.push(data.nuevo_horario);
                }
                const sesionesBtn = document.querySelector('.btn-sesiones[data-modulo-id="' + document.getElementById('edit-sesion-modulo-id').value + '"]');
                if (sesionesBtn) sesionesBtn.click();
            } else {
                showToast('Error al guardar: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () {
            showToast('Error de conexión al guardar', 'danger');
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i>Guardar';
        });
    });

    // ---- Editar fecha/hora de sesión (abrir modal) ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-editar-fecha-sesion');
        if (!btn) return;

        document.getElementById('edit-fecha-horario-id').value = btn.dataset.horarioId;

        const fechaIso = btn.dataset.fecha || '';
        document.getElementById('edit-fecha-fecha').value = fechaIso;
        if (fechaIso) {
            const partes = fechaIso.split('-');
            const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            document.getElementById('edit-fecha-formateada').textContent = parseInt(partes[2]) + ' de ' + meses[parseInt(partes[1]) - 1] + ' del ' + partes[0];
        } else {
            document.getElementById('edit-fecha-formateada').textContent = '';
        }

        // Actualizar formato al cambiar la fecha
        document.getElementById('edit-fecha-fecha')?.addEventListener('change', function () {
            const val = this.value;
            if (val) {
                const p = val.split('-');
                const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                document.getElementById('edit-fecha-formateada').textContent = parseInt(p[2]) + ' de ' + meses[parseInt(p[1]) - 1] + ' del ' + p[0];
            } else {
                document.getElementById('edit-fecha-formateada').textContent = '';
            }
        });

        document.getElementById('edit-fecha-hora-inicio').value = btn.dataset.horaInicio ? btn.dataset.horaInicio.substring(0,5) : '';
        document.getElementById('edit-fecha-hora-fin').value = btn.dataset.horaFin ? btn.dataset.horaFin.substring(0,5) : '';

        new bootstrap.Modal(document.getElementById('modalEditarFechaSesion')).show();
    });

    // ---- Guardar cambios de fecha/hora ----
    document.getElementById('formEditarFechaSesion')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarFechaSesion');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch('{{ route("admin.programas.horario-fecha") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarFechaSesion')).hide();
                showToast(data.message || 'Fecha y hora actualizadas correctamente', 'success');
                // Actualizar datos locales
                const id = parseInt(document.getElementById('edit-fecha-horario-id').value);
                const idx = horariosData.findIndex(function (h) { return h.id === id; });
                if (idx !== -1) {
                    horariosData[idx].fecha_desarrollo = document.getElementById('edit-fecha-fecha').value || horariosData[idx].fecha_desarrollo;
                    horariosData[idx].hora_inicio = document.getElementById('edit-fecha-hora-inicio').value || horariosData[idx].hora_inicio;
                    horariosData[idx].hora_fin = document.getElementById('edit-fecha-hora-fin').value || horariosData[idx].hora_fin;
                }
                const mid = horariosData[idx] ? horariosData[idx].modulo_id : null;
                if (mid) {
                    const sesBtn = document.querySelector('.btn-sesiones[data-modulo-id="' + mid + '"]');
                    if (sesBtn) sesBtn.click();
                }
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

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-sesion-extra');
        if (!btn) return;

        document.getElementById('se-modulo-id').value = btn.dataset.moduloId;
        document.getElementById('se-modulo-nombre').value = btn.dataset.moduloNombre;
        document.getElementById('modalSesionExtraTitle').textContent = 'Agregar Sesión Extra - ' + btn.dataset.moduloNombre;
        document.getElementById('formSesionExtra').reset();
        document.getElementById('se-fecha-container').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('modalSesionExtra'));
        modal.show();
    });

    // ---- Mostrar/ocultar fecha reprogramación en sesión extra ----
    document.addEventListener('change', function (e) {
        if (e.target.name !== 'estado' || !e.target.closest('#formSesionExtra')) return;
        const container = document.getElementById('se-fecha-container');
        container.style.display = e.target.value === 'Postergado' ? 'block' : 'none';
    });

    document.getElementById('formSesionExtra')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarSesion');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch('{{ route("admin.programas.sesion-extra") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalSesionExtra')).hide();
                showToast(data.message || 'Sesión extra agregada correctamente', 'success');
                if (data.nuevo_horario) {
                    horariosData.push(data.nuevo_horario);
                }
                var modId = document.getElementById('se-modulo-id').value;
                var sesBtn = document.querySelector('.btn-sesiones[data-modulo-id="' + modId + '"]');
                if (sesBtn) sesBtn.click();
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

    // Sincronizar visibilidad del tab Fase Académica al cargar
    toggleFaseAcademicaTab();

    // ---- Cambiar estado del módulo ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-editar-modulo');
        if (!btn) return;

        const modalEl = document.getElementById('modalEditarModulo');
        if (!modalEl) return;

        const moduloId = btn.dataset.moduloId;
        if (!moduloId) return;

        document.getElementById('edit-modulo-id').value = moduloId;
        document.querySelector('#formEditarModulo select[name="desarrollo"]').value = btn.dataset.desarrollo;
        document.getElementById('edit-modulo-fecha-conclusion').value = btn.dataset.fechaConclusion || '';

        const selDev = document.getElementById('edit-modulo-desarrollo');
        const fechaWrap = document.getElementById('edit-modulo-fecha-wrap');
        if (fechaWrap) {
            fechaWrap.style.display = selDev.value === 'Concluido' ? '' : 'none';
        }

        new bootstrap.Modal(modalEl).show();
    });

    const selDev = document.getElementById('edit-modulo-desarrollo');
    const fechaWrap = document.getElementById('edit-modulo-fecha-wrap');
    if (selDev && fechaWrap) {
        selDev.addEventListener('change', function () {
            fechaWrap.style.display = this.value === 'Concluido' ? '' : 'none';
            if (this.value === 'Concluido' && !document.getElementById('edit-modulo-fecha-conclusion').value) {
                document.getElementById('edit-modulo-fecha-conclusion').value = new Date().toISOString().split('T')[0];
            }
        });
    }

    const formEditarModulo = document.getElementById('formEditarModulo');
    if (formEditarModulo) formEditarModulo.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarModulo');
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
                bootstrap.Modal.getInstance(document.getElementById('modalEditarModulo')).hide();
                showToast(data.message || 'Estado actualizado correctamente', 'success');
                const mid = document.getElementById('edit-modulo-id').value;
                const nuevo = document.querySelector('#formEditarModulo select[name="desarrollo"]').value;
                const badge = document.getElementById('modulo-badge-' + mid);
                if (badge) {
                    const colorMap = { 'Concluido':'#0ab39c', 'En Desarrollo':'#299cdb', 'No Inicio':'#878a99', 'Pendiente':'#f7b84b', 'Suspendido':'#f06548' };
                    badge.className = 'modulo-estado-badge btn-editar-modulo';
                    badge.style.color = colorMap[nuevo] || '#878a99';
                    badge.textContent = nuevo;
                    badge.dataset.desarrollo = nuevo;
                }
                // Actualizar columna acciones según estado
                const row = document.getElementById('modulo-badge-' + mid).closest('tr');
                if (row) {
                    const hstack = row.querySelector('.hstack');
                    const existingBtn = hstack.querySelector('.btn-asignar-docente');
                    if (nuevo === 'Concluido') {
                        if (existingBtn) existingBtn.remove();
                    } else {
                        if (!existingBtn && PUEDE_ASIGNAR_DOCENTE) {
                            const modNombre = row.querySelector('td:nth-child(3) .fw-semibold')?.textContent?.trim() || '';
                            const btn = document.createElement('button');
                            btn.className = 'btn btn-sm btn-icon btn-soft-warning btn-asignar-docente';
                            btn.dataset.moduloId = mid;
                            btn.dataset.moduloNombre = modNombre;
                            btn.dataset.bsToggle = 'tooltip';
                            btn.dataset.bsPlacement = 'top';
                            btn.title = 'Asignar docente a este módulo';
                            btn.innerHTML = '<i class="ri-user-add-line fs-14"></i>';
                            hstack.insertBefore(btn, hstack.firstChild);
                        }
                    }

                    // Actualizar indicadores de notas
                    updateNotasEstado(mid, nuevo, row);
                    // Mostrar/ocultar tab Fase Académica Concluida según estado global
                    toggleFaseAcademicaTab();
                }
                // Recargar para reflejar la fecha de conclusión y la sección de nivelación
                if (nuevo === 'Concluido') {
                    setTimeout(function () { location.reload(); }, 1200);
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

    // ---- Editar enlace Moodle ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-modulo-moodle');
        if (!btn) return;

        const moduloId = btn.dataset.moduloId;
        if (!moduloId) return;

        document.getElementById('edit-moodle-modulo-id').value = moduloId;
        document.getElementById('edit-moodle-link').value = btn.dataset.moodleLink || '';

        new bootstrap.Modal(document.getElementById('modalEditarMoodle')).show();
    });

    document.getElementById('formEditarMoodle').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarMoodle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        const moduloId = document.getElementById('edit-moodle-modulo-id').value;
        fetch('{{ route("admin.programas.modulo-moodle-link", ["modulo" => "_MODULO_ID_"]) }}'.replace('_MODULO_ID_', moduloId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarMoodle')).hide();
                showToast(data.message || 'Enlace actualizado correctamente', 'success');
                const mid = document.getElementById('edit-moodle-modulo-id').value;
                const nuevoLink = document.getElementById('edit-moodle-link').value.trim();
                const row = document.getElementById('modulo-badge-' + mid)?.closest('tr');
                if (row) {
                    const editBtn = row.querySelector('.btn-modulo-moodle');
                    if (editBtn) {
                        editBtn.dataset.moodleLink = nuevoLink;
                        editBtn.title = nuevoLink ? 'Editar enlace Moodle' : 'Agregar enlace Moodle';
                    }
                    const tdMoodle = editBtn ? editBtn.closest('td') : null;
                    if (tdMoodle) {
                        const existingLink = tdMoodle.querySelector('a[target="_blank"]');
                        if (nuevoLink && !existingLink) {
                            const link = document.createElement('a');
                            link.href = nuevoLink;
                            link.target = '_blank';
                            link.className = 'btn btn-sm btn-icon btn-soft-primary';
                            link.dataset.bsToggle = 'tooltip';
                            link.dataset.bsPlacement = 'top';
                            link.title = 'Ir a Moodle';
                            link.innerHTML = '<i class="ri-external-link-line fs-13"></i>';
                            link.onclick = function (e) { e.stopPropagation(); };
                            tdMoodle.querySelector('.hstack').insertBefore(link, editBtn);
                        } else if (!nuevoLink && existingLink) {
                            existingLink.remove();
                        } else if (nuevoLink && existingLink) {
                            existingLink.href = nuevoLink;
                        }
                    }
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

    // ---- Editar enlace Moodle del Programa ----
    document.getElementById('heroProgramaMoodle')?.addEventListener('click', function (e) {
        e.preventDefault();
        const tieneLink = this.dataset.tieneLink === '1';
        const url = this.dataset.url;
        if (tieneLink && url && !e.shiftKey) {
            window.open(url, '_blank');
            return;
        }
        if (this.dataset.puedeEditar !== '1') {
            showToast('No tenés permisos para editar el enlace Moodle.', 'danger');
            return;
        }
        document.getElementById('edit-programa-moodle-link').value = url || '';
        new bootstrap.Modal(document.getElementById('modalProgramaMoodle')).show();
    });

    document.getElementById('formProgramaMoodle')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarProgramaMoodle');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData(this);

        fetch('{{ route("admin.programas.programa-moodle-link", ["programa" => $programa->id]) }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalProgramaMoodle')).hide();
                showToast(data.message || 'Enlace actualizado correctamente', 'success');
                const nuevoLink = document.getElementById('edit-programa-moodle-link').value.trim();
                const badge = document.getElementById('heroProgramaMoodle');
                if (nuevoLink) {
                    badge.style.background = '#6a1b9a';
                    badge.style.borderColor = '#6a1b9a';
                    badge.classList.remove('hero-badge-moodle-empty');
                    badge.innerHTML = '<i class="ri-external-link-line"></i><span>Moodle</span>';
                    badge.title = 'Ir a Moodle';
                    badge.onclick = function (e) {
                        e.preventDefault();
                        window.open(nuevoLink, '_blank');
                    };
                } else {
                    badge.style.background = '#adb5bd';
                    badge.style.borderColor = '#adb5bd';
                    badge.classList.add('hero-badge-moodle-empty');
                    badge.innerHTML = '<i class="ri-add-line"></i><span>Moodle</span>';
                    badge.title = 'Agregar enlace Moodle';
                    badge.onclick = function (e) {
                        e.preventDefault();
                        document.getElementById('edit-programa-moodle-link').value = '';
                        new bootstrap.Modal(document.getElementById('modalProgramaMoodle')).show();
                    };
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

    function updateNotasEstado(moduloId, estado, row) {
        const cell = row.querySelector('td:nth-child(4)');
        if (!cell) return;
        const existingIndicators = cell.querySelector('.notas-indicators');
        if (existingIndicators) existingIndicators.remove();
        if (estado !== 'Concluido') return;
        fetch('{{ url("admin/programas/modulo") }}/' + moduloId + '/notas-estado', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (!d.success) return;
            const div = document.createElement('div');
            div.className = 'mt-1 notas-indicators';
            div.style.cssText = 'font-size:.65rem;line-height:1.3;';
            div.innerHTML =
                (d.con_nota_modulo > 0
                    ? '<span class="badge bg-soft-success text-success" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota m\u00f3dulo</span>'
                    : '<span class="badge bg-soft-secondary text-secondary" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota m\u00f3dulo</span>') +
                '<br>' +
                (d.con_nota_nivelacion > 0
                    ? '<span class="badge bg-soft-success text-success mt-1" style="font-size:.6rem;font-weight:500;"><i class="ri-checkbox-circle-line"></i> Nota nivelaci\u00f3n</span>'
                    : '<span class="badge bg-soft-secondary text-secondary mt-1" style="font-size:.6rem;font-weight:500;"><i class="ri-time-line"></i> Nota nivelaci\u00f3n</span>');
            cell.appendChild(div);
        })
        .catch(function () {});
    }

    function toggleFaseAcademicaTab() {
        const badges = document.querySelectorAll('#tab-seguimiento-academico .modulo-estado-badge');
        let todosConcluidos = badges.length > 0;
        badges.forEach(function (b) {
            if (b.textContent.trim() !== 'Concluido') todosConcluidos = false;
        });
        const navItem = document.querySelector('[href="#tab-seguimiento-concluido"]')?.closest('.nav-item');
        const tabPane = document.getElementById('tab-seguimiento-concluido');
        if (todosConcluidos) {
            if (navItem) navItem.style.display = '';
            if (tabPane) tabPane.style.display = '';
        } else {
            if (navItem) {
                navItem.style.display = 'none';
                // Si el tab oculto estaba activo, activar el primer tab
                const tabLink = navItem.querySelector('.nav-link');
                if (tabLink && tabLink.classList.contains('active')) {
                    document.querySelector('[href="#tab-seguimiento-academico"]').click();
                }
            }
            if (tabPane) tabPane.style.display = 'none';
        }
    }

    // ---- Asignar Docente ----
    var asigdocTimer;
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-asignar-docente');
        if (!btn) return;
        document.getElementById('asigdoc-modulo-id').value = btn.dataset.moduloId;
        document.getElementById('modalAsignarDocenteTitle').textContent = 'Asignar Docente - ' + btn.dataset.moduloNombre;
        document.getElementById('asigdoc-resultado').innerHTML = '';
        document.getElementById('asigdoc-registro').style.display = 'none';
        document.getElementById('asigdoc-footer').style.display = 'none';
        document.getElementById('formAsignarDocente').reset();
        new bootstrap.Modal(document.getElementById('modalAsignarDocente')).show();
        document.getElementById('asigdoc-ci').focus();
    });

    document.getElementById('asigdoc-ci')?.addEventListener('input', function () {
        clearTimeout(asigdocTimer);
        const ci = this.value.trim();
        const resultado = document.getElementById('asigdoc-resultado');
        const registro = document.getElementById('asigdoc-registro');
        const footer = document.getElementById('asigdoc-footer');
        if (ci.length < 3) {
            resultado.innerHTML = '';
            registro.style.display = 'none';
            footer.style.display = 'none';
            return;
        }
        resultado.innerHTML = '<div class="text-center text-muted py-2"><span class="spinner-border spinner-border-sm me-1"></span>Buscando...</div>';
        asigdocTimer = setTimeout(function () {
            const formData = new FormData();
            formData.append('ci', ci);
            formData.append('modulo_id', document.getElementById('asigdoc-modulo-id').value || '');
            formData.append('_token', document.querySelector('#formAsignarDocente input[name="_token"]').value);
            fetch('{{ route("admin.programas.docente-por-ci") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.docente) {
                    const d = data.docente;
                    const colorDot = d.color || '#6c757d';
                    resultado.innerHTML =
                        '<div class="p-2 border rounded bg-light d-flex align-items-center gap-2 mt-1" style="border-left:4px solid ' + colorDot + '">' +
                            '<div class="flex-shrink-0"><span class="avatar-xs"><span class="avatar-title bg-soft-primary text-primary rounded-circle fs-12 fw-semibold">' + d.nombres.charAt(0) + d.apellidos.charAt(0) + '</span></span></div>' +
                            '<div class="flex-grow-1"><div class="fw-semibold text-dark fs-12">' + d.apellidos + ', ' + d.nombres + '</div><div class="text-muted fs-11">CI: ' + d.ci + (d.correo ? ' &middot; ' + d.correo : '') + '</div></div>' +
                        '</div>';
                    registro.style.display = 'none';
                    footer.style.display = 'block';
                    document.getElementById('formAsignarDocente').dataset.docenteId = d.id;
                } else {
                    resultado.innerHTML = '<div class="alert alert-warning py-2 mb-0 mt-1 fs-12 d-flex align-items-center gap-2"><i class="ri-error-warning-line"></i> Docente no encontrado con CI: <strong>' + ci + '</strong></div>';
                    document.getElementById('asigdoc-ci').dataset.ciBuscado = ci;
                    registro.style.display = 'block';
                    footer.style.display = 'none';
                }
            })
            .catch(function () {
                resultado.innerHTML = '<div class="text-danger py-1 fs-12">Error de conexión</div>';
            });
        }, 400);
    });

    document.getElementById('btnRegistrarDocente')?.addEventListener('click', function () {
        const btn = this;
        const ci = document.getElementById('asigdoc-ci').value.trim();
        const nombres = document.getElementById('asigdoc-nombres').value.trim();
        const apellidos = document.getElementById('asigdoc-apellidos').value.trim();
        const correo = document.getElementById('asigdoc-correo').value.trim();
        const celular = document.getElementById('asigdoc-celular').value.trim();
        if (!nombres || !apellidos) {
            showToast('Nombres y Apellidos son obligatorios', 'warning');
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Registrando...';
        const formData = new FormData();
        formData.append('ci', ci);
        formData.append('nombres', nombres);
        formData.append('apellidos', apellidos);
        formData.append('correo', correo);
        formData.append('celular', celular);
        formData.append('modulo_id', document.getElementById('asigdoc-modulo-id').value || '');
        formData.append('_token', document.querySelector('#formAsignarDocente input[name="_token"]').value);
        fetch('{{ route("admin.programas.docente-registrar") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('formAsignarDocente').dataset.docenteId = data.docente.id;
                document.getElementById('asigdoc-registro').style.display = 'none';
                document.getElementById('asigdoc-footer').style.display = 'block';
                document.getElementById('asigdoc-resultado').innerHTML =
                    '<div class="p-2 border rounded bg-light d-flex align-items-center gap-2 mt-1">' +
                        '<div class="flex-shrink-0"><span class="avatar-xs"><span class="avatar-title bg-soft-primary text-primary rounded-circle fs-12 fw-semibold">' + data.docente.nombres.charAt(0) + data.docente.apellidos.charAt(0) + '</span></span></div>' +
                        '<div class="flex-grow-1"><div class="fw-semibold text-dark fs-12">' + data.docente.apellidos + ', ' + data.docente.nombres + '</div><div class="text-muted fs-11">CI: ' + data.docente.ci + '</div></div>' +
                    '</div>';
            } else {
                showToast('Error: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-line me-1"></i>Registrar y Asignar';
        });
    });

    document.getElementById('formAsignarDocente')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const docenteId = this.dataset.docenteId;
        const moduloId = document.getElementById('asigdoc-modulo-id').value;
        if (!docenteId) {
            showToast('Busque un docente primero', 'warning');
            return;
        }
        const btn = document.getElementById('btnAsignarDocente');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Asignando...';
        const formData = new FormData();
        formData.append('modulo_id', moduloId);
        formData.append('docente_id', docenteId);
        formData.append('_token', document.querySelector('#formAsignarDocente input[name="_token"]').value);
        fetch('{{ route("admin.programas.asignar-docente") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalAsignarDocente')).hide();
                showToast(data.message, 'success');
                var td = document.querySelector('.btn-asignar-docente[data-modulo-id="' + moduloId + '"]').closest('tr').querySelector('td:nth-child(4)');
                if (td) {
                    td.innerHTML = '<i class="ri-user-star-line text-muted me-1 align-bottom"></i>' + data.docente.apellidos + ', ' + data.docente.nombres;
                }
            } else {
                showToast('Error: ' + (data.message || 'desconocido'), 'danger');
            }
        })
        .catch(function () { showToast('Error de conexión', 'danger'); })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-user-add-line me-1"></i>Asignar';
        });
    });

    // ---- Sticky columns ajuste dinámico ----
    adjustStickyCols();
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(adjustStickyCols, 200);
    });
    document.querySelector('[data-bs-toggle="tab"][href="#tab-desarrollo"]')?.addEventListener('shown.bs.tab', function () {
        setTimeout(adjustStickyCols, 100);
    });

    // ===== Toggle Inscripciones Habilitadas (estado_id=4) =====
    (function () {
        const $btnToggle = document.getElementById('btn-toggle-inscripciones');
        if (!$btnToggle) return;
        const PROG_ID_T = {{ $programa->id }};
        const $modal = document.getElementById('modalToggleInsc');
        const $confirm = document.getElementById('ti-confirm');
        const $iconWrap = document.getElementById('ti-icon-wrap');
        const $icon = document.getElementById('ti-icon');
        const $title = document.getElementById('ti-title');
        const $desc = document.getElementById('ti-desc');
        const $alert = document.getElementById('ti-alert');
        const $alertText = document.getElementById('ti-alert-text');

        function configurarModal(seraHabilitar) {
            if (seraHabilitar) {
                $iconWrap.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d8 100%)';
                $iconWrap.style.color = '#0ab39c';
                $iconWrap.style.boxShadow = '0 0 0 8px rgba(10,179,156,.08)';
                $icon.className = 'ri-checkbox-circle-line';
                $title.textContent = '¿Habilitar inscripciones?';
                $desc.textContent = 'Los participantes podrán inscribirse en este programa.';
                $alert.style.background = '#d1fae5';
                $alert.style.color = '#0ab39c';
                $alertText.textContent = 'El botón "Inscribir Participante" quedará habilitado para registrar nuevos inscritos.';
                $confirm.className = 'btn btn-success px-4';
                $confirm.innerHTML = '<i class="ri-check-line align-bottom"></i> Sí, habilitar';
            } else {
                $iconWrap.style.background = 'linear-gradient(135deg, #fff5f3 0%, #ffe7e2 100%)';
                $iconWrap.style.color = '#f06548';
                $iconWrap.style.boxShadow = '0 0 0 8px rgba(240,101,72,.08)';
                $icon.className = 'ri-error-warning-line';
                $title.textContent = '¿Deshabilitar inscripciones?';
                $desc.textContent = 'Ya no se podrán registrar nuevos participantes.';
                $alert.style.background = '#fff5f3';
                $alert.style.color = '#f06548';
                $alertText.textContent = 'El botón "Inscribir Participante" quedará deshabilitado hasta que vuelvas a habilitarlo.';
                $confirm.className = 'btn btn-danger px-4';
                $confirm.innerHTML = '<i class="ri-prohibited-line align-bottom"></i> Sí, deshabilitar';
            }
        }

        $btnToggle.addEventListener('click', function () {
            const wasOn = parseInt(this.dataset.habilitadas) === 1;
            configurarModal(!wasOn);
            new bootstrap.Modal($modal).show();
        });

        $confirm.addEventListener('click', function () {
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Aplicando...';

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');

            fetch('{{ url("admin/programas") }}/' + PROG_ID_T + '/toggle-inscripciones', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json().then(d => ({ ok: r.ok, status: r.status, data: d })))
            .then(({ ok, status, data }) => {
                if (ok && data && data.success) {
                    bootstrap.Modal.getInstance($modal).hide();
                    showToast(data.message || 'Cambio guardado', 'success');
                    setTimeout(() => location.reload(), 700);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    showToast((data && data.message) || ('Error HTTP ' + status), 'danger');
                }
            })
            .catch((err) => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                showToast('Error de conexión: ' + (err.message || ''), 'danger');
            });
        });
    })();

    // ===== Agregar Módulos (estado_id=4) =====
    @if((int)$programa->estado_id === 4)
    (function () {
        const PROG_ID_M = {{ $programa->id }};
        const $modal = document.getElementById('modalAgregarModulos');
        const $btnOpen = document.getElementById('btn-agregar-modulos');
        const $list = document.getElementById('modulos-inputs');
        const $btnAddRow = document.getElementById('btn-agregar-fila-mod');
        const $btnSave = document.getElementById('btn-guardar-modulos');
        const $counter = document.getElementById('contador-mods');

        // Sin permiso para agregar módulos, el modal no se renderiza.
        if (!$modal || !$btnOpen || !$list || !$btnAddRow || !$btnSave || !$counter) return;

        function updateCounter() {
            $counter.textContent = $list.querySelectorAll('input[name="nombres[]"]').length;
        }
        function addRow(value = '') {
            const wrap = document.createElement('div');
            wrap.className = 'd-flex align-items-center gap-2 mb-2';
            wrap.innerHTML =
                '<div class="input-group">' +
                    '<span class="input-group-text" style="background:#fff; border-right:0;"><i class="ri-book-2-line text-primary"></i></span>' +
                    '<input type="text" name="nombres[]" class="form-control" placeholder="Nombre del módulo" value="' + value.replace(/"/g, '&quot;') + '" maxlength="255">' +
                '</div>' +
                '<button type="button" class="btn btn-sm btn-icon btn-soft-danger btn-quitar-fila" title="Quitar"><i class="ri-close-line"></i></button>';
            $list.appendChild(wrap);
            const input = wrap.querySelector('input');
            input.focus();
            updateCounter();
        }
        $list.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-quitar-fila');
            if (!btn) return;
            const row = btn.closest('div.d-flex');
            if ($list.querySelectorAll('input[name="nombres[]"]').length <= 1) {
                row.querySelector('input').value = '';
            } else {
                row.remove();
            }
            updateCounter();
        });
        $list.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.matches('input[name="nombres[]"]')) {
                e.preventDefault();
                addRow();
            }
        });
        $btnAddRow.addEventListener('click', () => addRow());

        $btnOpen.addEventListener('click', function () {
            $list.innerHTML = '';
            // pre-cargar 3 filas vacías
            for (let i = 0; i < 3; i++) addRow();
            new bootstrap.Modal($modal).show();
        });

        $btnSave.addEventListener('click', function () {
            const nombres = Array.from($list.querySelectorAll('input[name="nombres[]"]'))
                .map(i => i.value.trim()).filter(n => n !== '');
            if (nombres.length === 0) {
                showToast('Debes ingresar al menos un nombre de módulo', 'warning');
                return;
            }
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creando...';

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            nombres.forEach(n => fd.append('nombres[]', n));

            fetch('{{ url("admin/programas") }}/' + PROG_ID_M + '/crear-modulos', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance($modal).hide();
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 900);
                } else {
                    showToast(data.message || 'Error al crear módulos', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });
    })();
    @endif

    @if((int)($programa->n_modulos ?? 0) > 0)
    // ===== Rebalancear Cuotas de Colegiatura =====
    (function () {
        const PROG_ID_R = {{ $programa->id }};
        const $btn = document.getElementById('btn-rebalancear-colegiaturas');
        if (!$btn) return;

        $btn.addEventListener('click', function () {
            if (!confirm('¿Estás seguro de rebalancear las cuotas de colegiatura? Se reajustarán las deudas de participantes sin pagos.')) return;

            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Rebalanceando...';

            fetch('{{ url("admin/programas") }}/' + PROG_ID_R + '/rebalancear-colegiaturas', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ '_token': '{{ csrf_token() }}' })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message || 'Error al rebalancear', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });
    })();
    @endif

    // ===== Registrar Horarios (cuando módulo no tiene sesiones) =====
    (function () {
        const PROG_ID_H = {{ $programa->id }};
        const CANT_SES = {{ (int)($programa->cantidad_sesiones ?? 0) }};
        const $modal = document.getElementById('modalRegistrarHorarios');
        const $tbody = document.getElementById('rh-tbody');
        const $btnSave = document.getElementById('btn-guardar-horarios');
        const $nombreLbl = document.getElementById('rh-modulo-nombre');
        const $countInfo = document.getElementById('rh-count-info');
        let currentModuloId = null;

        function buildRows(n) {
            $tbody.innerHTML = '';
            for (let i = 1; i <= n; i++) {
                const row = document.createElement('tr');
                row.innerHTML =
                    '<td class="text-center text-muted fw-bold">' + i + '</td>' +
                    '<td><input type="date" class="form-control form-control-sm rh-fecha" required></td>' +
                    '<td><input type="time" class="form-control form-control-sm rh-hi" value="19:00" required></td>' +
                    '<td><input type="time" class="form-control form-control-sm rh-hf" value="22:00" required></td>';
                $tbody.appendChild(row);
            }
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-registrar-horarios');
            if (!btn) return;
            if (CANT_SES <= 0) {
                showToast('El programa no tiene "cantidad_sesiones" configurada', 'warning');
                return;
            }
            currentModuloId = btn.dataset.moduloId;
            $nombreLbl.textContent = btn.dataset.moduloNombre + ' · ' + CANT_SES + ' sesiones';
            $countInfo.textContent = CANT_SES;
            buildRows(CANT_SES);
            new bootstrap.Modal($modal).show();
        });

        $btnSave.addEventListener('click', function () {
            if (!currentModuloId) return;
            const rows = Array.from($tbody.querySelectorAll('tr'));
            const horarios = [];
            for (const row of rows) {
                const fecha = row.querySelector('.rh-fecha').value;
                const hi = row.querySelector('.rh-hi').value;
                const hf = row.querySelector('.rh-hf').value;
                if (!fecha || !hi || !hf) {
                    showToast('Completa fecha y horas en todas las filas', 'warning');
                    return;
                }
                horarios.push({
                    fecha_desarrollo: fecha,
                    hora_inicio: hi,
                    hora_fin: hf,
                });
            }

            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('modulo_id', currentModuloId);
            horarios.forEach((h, i) => {
                fd.append('horarios[' + i + '][fecha_desarrollo]', h.fecha_desarrollo);
                fd.append('horarios[' + i + '][hora_inicio]', h.hora_inicio);
                fd.append('horarios[' + i + '][hora_fin]', h.hora_fin);
            });

            fetch('{{ url("admin/programas") }}/' + PROG_ID_H + '/crear-horarios', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance($modal).hide();
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Error al guardar', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        });
    })();

    // ===== Eliminar Inscripción =====
    (function () {
        const PROG_ID_DEL = {{ $programa->id }};
        let deleteInsId = null;
        let deleteRowEl = null;

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-eliminar-inscripcion');
            if (!btn) return;
            deleteInsId = btn.dataset.id;
            deleteRowEl = btn.closest('tr');
            document.getElementById('ei-nombre').textContent = btn.dataset.nombre + ' (CI: ' + btn.dataset.ci + ')';
            new bootstrap.Modal(document.getElementById('modalEliminarInscripcion')).show();
        });

        document.getElementById('ei-confirm')?.addEventListener('click', function () {
            if (!deleteInsId) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Eliminando...';

            const fd = new URLSearchParams({ _method: 'DELETE', _token: '{{ csrf_token() }}' });
            fetch('{{ url("admin/programas") }}/' + PROG_ID_DEL + '/inscripcion/' + deleteInsId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminarInscripcion')).hide();
                if (data.success) {
                    showToast(data.message || 'Inscripción eliminada', 'success');
                    if (deleteRowEl) deleteRowEl.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Error al eliminar', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-delete-bin-line align-bottom"></i> Sí, eliminar';
                deleteInsId = null; deleteRowEl = null;
            });
        });
    })();

    // ===== Toggle Baja =====
    (function () {
        const PROG_ID_TB = {{ $programa->id }};
        const ESTADO_ID_TB = {{ $programa->estado_id }};

        const NOMBRE_ESTADO = ESTADO_ID_TB === 4 ? 'Inscripciones' : 'En desarrollo';
        const TIPO_AUTO = 'operativa';

        let currentInscripcionId = null;

        function resetTbExtras() {
            document.getElementById('tbBajaAcademica').checked = false;
            document.getElementById('tbBajaContable').checked = false;
            document.getElementById('tbArchivo').value = '';
            document.getElementById('tbArchivoNombre').textContent = '';
            document.getElementById('tbExtrasGroup').style.display = 'none';
        }

        document.getElementById('tbPdfBtn')?.addEventListener('click', function () {
            if (!currentInscripcionId) return;
            window.open('{{ url("admin/programas") }}/' + PROG_ID_TB + '/inscripcion/' + currentInscripcionId + '/baja-pdf', '_blank');
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-baja-archivos');
            if (!btn) return;
            const inscId = btn.dataset.inscripcionId;
            if (!inscId) return;
            document.getElementById('bapParticipante').textContent = btn.dataset.nombre + ' (CI: ' + btn.dataset.ci + ')';
            const base = '{{ url("admin/programas") }}/' + PROG_ID_TB + '/inscripcion/' + inscId;
            document.getElementById('bapPdfBtn').href = base + '/baja-pdf';
            const archivoBtn = document.getElementById('bapArchivoBtn');
            if (parseInt(btn.dataset.tieneArchivo) === 1) {
                archivoBtn.href = base + '/baja-archivo';
                archivoBtn.style.display = 'flex';
                document.getElementById('bapArchivoNombre').textContent = btn.dataset.archivoNombre || 'Archivo adjuntado';
            } else {
                archivoBtn.removeAttribute('href');
                archivoBtn.style.display = 'none';
            }
            new bootstrap.Modal(document.getElementById('modalBajaArchivos')).show();
        });

        document.getElementById('tbArchivo')?.addEventListener('change', function () {
            const f = this.files && this.files[0];
            document.getElementById('tbArchivoNombre').textContent = f ? '<i class="ri-file-line"></i> ' + f.name : '';
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-toggle-baja');
            if (!btn) return;
            currentInscripcionId = btn.dataset.inscripcionId;
            document.getElementById('tbInscripcionId').value = currentInscripcionId;
            document.getElementById('tbParticipante').textContent = btn.dataset.nombre + ' (CI: ' + btn.dataset.ci + ')';
            const bajaOp = parseInt(btn.dataset.bajaOperativa);
            const bajaAc = parseInt(btn.dataset.bajaAcademica);
            const bajaCont = parseInt(btn.dataset.bajaContable);
            document.getElementById('tbHistorialContainer').style.display = 'none';
            document.getElementById('tbHistorialList').innerHTML = '';
            document.getElementById('tbInfoEstado').style.display = 'none';
            resetTbExtras();

            document.getElementById('tbTipoTemprana').disabled = true;
            document.getElementById('tbTipoOperativa').disabled = true;

            if (bajaOp) {
                document.getElementById('tbTitle').textContent = 'Dar de Alta';
                document.getElementById('tbAccion').value = 'alta';
                document.getElementById('tbConfirm').innerHTML = '<i class="ri-check-line align-bottom"></i> Confirmar Alta';
                document.getElementById('tbTipoOperativa').checked = true;
                document.getElementById('tbInfoEstado').style.display = 'none';
            } else {
                document.getElementById('tbTitle').textContent = 'Gestión de Baja';
                document.getElementById('tbAccion').value = 'baja';
                document.getElementById('tbConfirm').innerHTML = '<i class="ri-check-line align-bottom"></i> Confirmar';

                const esInscripciones = ESTADO_ID_TB === 4;
                if (esInscripciones) {
                    document.getElementById('tbTipoTemprana').checked = true;
                } else {
                    document.getElementById('tbTipoOperativa').checked = true;
                    document.getElementById('tbBajaAcademica').checked = !!bajaAc;
                    document.getElementById('tbBajaContable').checked = !!bajaCont;
                    document.getElementById('tbExtrasGroup').style.display = 'block';
                }
                document.getElementById('tbInfoEstado').className = 'alert ' + (esInscripciones ? 'alert-soft-warning' : 'alert-soft-danger') + ' d-flex align-items-start gap-2 py-2 px-3 mb-3 border-0';
                document.getElementById('tbInfoTitle').textContent = (esInscripciones ? 'Baja Temprana' : 'Baja Operativa') + ' — Programa en "' + NOMBRE_ESTADO + '"';
                document.getElementById('tbInfoDesc').textContent = esInscripciones
                    ? 'Se registrará una baja temprana, correspondiente al período de inscripciones.'
                    : 'Se registrará una baja operativa, correspondiente al período de desarrollo.';
                document.getElementById('tbInfoEstado').style.display = 'flex';
            }
            cargarHistorialBaja(currentInscripcionId);
            new bootstrap.Modal(document.getElementById('modalToggleBaja')).show();
        });

        document.getElementById('tbConfirm')?.addEventListener('click', function () {
            const btn = this;
            if (!currentInscripcionId) return;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

            const accion = document.getElementById('tbAccion').value;
            const tipo = document.querySelector('input[name="tbTipo"]:checked')?.value || 'operativa';
            const motivo = document.getElementById('tbMotivo').value.trim();
            const conAcademica = document.getElementById('tbBajaAcademica').checked;
            const conContable = document.getElementById('tbBajaContable').checked;
            const archivo = document.getElementById('tbArchivo').files[0];

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('accion', accion);
            fd.append('tipo', tipo);
            if (motivo) fd.append('motivo', motivo);
            if (accion === 'baja') {
                if (conAcademica) fd.append('baja_academica', '1');
                if (conContable) fd.append('baja_contable', '1');
                if (archivo) fd.append('archivo', archivo);
            }

            fetch('{{ url("admin/programas") }}/' + PROG_ID_TB + '/inscripcion/' + currentInscripcionId + '/toggle-baja', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('modalToggleBaja')).hide();
                if (data.success) {
                    const pdfUrl = '{{ url("admin/programas") }}/' + PROG_ID_TB + '/inscripcion/' + currentInscripcionId + '/baja-pdf';
                    const msg = data.message || 'Estado actualizado';
                    showToast(msg + ' <a href="' + pdfUrl + '" target="_blank" style="color:#fff;font-weight:700;text-decoration:underline;margin-left:6px;"><i class="ri-file-pdf-line"></i> Ver reporte PDF</a>', 'success');
                    setTimeout(() => location.reload(), 4500);
                } else {
                    showToast(data.message || 'Error al procesar', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-line align-bottom"></i> Confirmar';
                currentInscripcionId = null;
                document.getElementById('tbMotivo').value = '';
                resetTbExtras();
            });
        });

        window.cargarHistorialBaja = function (inscripcionId) {
            const id = inscripcionId || currentInscripcionId;
            if (!id) return;
            const container = document.getElementById('tbHistorialContainer');
            const list = document.getElementById('tbHistorialList');
            list.innerHTML = '<div class="text-muted text-center py-2"><span class="spinner-border spinner-border-sm me-1"></span>Cargando...</div>';
            container.style.display = 'block';
            fetch('{{ url("admin/programas") }}/' + PROG_ID_TB + '/inscripcion/' + id + '/baja-historial', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const historial = data.historial || data;
                if (!historial || historial.length === 0) {
                    list.innerHTML = '<div class="text-muted text-center py-2 fs-13">Sin historial</div>';
                    return;
                }
                list.innerHTML = historial.map(function (h) {
                    const badge = h.accion === 'baja' ? '<span class="badge bg-danger-subtle text-danger">Baja</span>' : '<span class="badge bg-success-subtle text-success">Alta</span>';
                    const tipoLabel = h.tipo === 'temprana' ? 'Temprana' : (h.tipo === 'academica' ? 'Académica' : 'Operativa');
                    let extra = '';
                    if (parseInt(h.baja_academica || 0)) extra += ' <span class="badge bg-warning-subtle text-warning">Académica</span>';
                    if (parseInt(h.baja_contable || 0)) extra += ' <span class="badge bg-info-subtle text-info">Contable</span>';
                    const arch = h.archivo_original ? '<br><small class="text-muted"><i class="ri-attachment-line"></i> ' + h.archivo_original + '</small>' : '';
                    return '<div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2"><div><div>' + badge + ' <span class="fw-medium">' + tipoLabel + '</span>' + extra + '</div><small class="text-muted">' + (h.motivo || 'Sin motivo') + '</small>' + arch + '</div><small class="text-muted text-end" style="min-width:120px;">' + h.created_at + '<br>' + (h.user_nombre || '—') + '</small></div>';
                }).join('');
            })
            .catch(function () {
                list.innerHTML = '<div class="text-danger text-center py-2">Error al cargar historial</div>';
            });
        };
    })();

    // ===== Editar Inscripción (Asesor) =====
    (function () {
        const PROG_ID_EI = {{ $programa->id }};

        const $eiModal = document.getElementById('modalEditarInscripcion');
        const $eiInscId = document.getElementById('ei-inscripcion-id');
        const $eiAsesorInp = document.getElementById('ei-asesor');
        const $eiAsesorDrop = document.getElementById('ei-asesor-dropdown');
        const $eiAsesorSelected = document.getElementById('ei-asesor-selected');
        const $eiAsesorIdInp = document.getElementById('ei-asesor-id');

        // Si el usuario no tiene permiso para cambiar el asesor, el modal no se
        // renderiza. Salir sin romper el resto de los scripts de la página.
        if (!$eiModal || !$eiInscId || !$eiAsesorInp || !$eiAsesorDrop || !$eiAsesorSelected || !$eiAsesorIdInp) return;

        let eiAsesorTimer = null;

        // Abrir modal al hacer clic en el botón de editar
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-editar-inscripcion');
            if (!btn) return;
            const inscId = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const ci = btn.dataset.ci;
            const asesorId = btn.dataset.asesorId;

            $eiInscId.value = inscId;
            document.getElementById('ei-participante-nombre').textContent = nombre + ' · CI: ' + ci;

            // Si ya tiene asesor, mostrar su info
            $eiAsesorIdInp.value = asesorId || '';
            $eiAsesorInp.value = '';
            $eiAsesorSelected.innerHTML = '';

            if (asesorId) {
                fetch('{{ route("admin.programas.buscar-asesores") }}?q=' + asesorId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    const asesor = data.asesores && data.asesores.find(a => a.id == asesorId);
                    if (asesor) {
                        const ini = (asesor.nombres.charAt(0) + asesor.apellidos.charAt(0)).toUpperCase();
                        $eiAsesorSelected.innerHTML =
                            '<span class="asesor-selected-card">' +
                                '<span class="asesor-opt-avatar">' + ini + '</span>' +
                                '<span>' + asesor.apellidos + ', ' + asesor.nombres + '</span>' +
                                '<i class="ri-close-circle-line" data-clear-asesor></i>' +
                            '</span>';
                    }
                });
            }

            new bootstrap.Modal($eiModal).show();
        });

        // Búsqueda de asesores
        $eiAsesorInp.addEventListener('input', function () {
            clearTimeout(eiAsesorTimer);
            const q = this.value.trim();
            $eiAsesorDrop.classList.add('open');
            $eiAsesorDrop.innerHTML = '<div class="asesor-empty"><span class="spinner-border spinner-border-sm me-1"></span>Buscando...</div>';
            eiAsesorTimer = setTimeout(() => {
                fetch('{{ route("admin.programas.buscar-asesores") }}?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.asesores || data.asesores.length === 0) {
                        $eiAsesorDrop.innerHTML = '<div class="asesor-empty">Sin resultados</div>';
                        return;
                    }
                    let html = '';
                    data.asesores.forEach(a => {
                        const ini = ((a.nombres || '').charAt(0) + (a.apellidos || '').charAt(0)).toUpperCase();
                        html += '<div class="asesor-opt" data-id="' + a.id + '" data-nombre="' + (a.apellidos + ', ' + a.nombres).replace(/"/g, '&quot;') + '">' +
                            '<div class="asesor-opt-avatar">' + ini + '</div>' +
                            '<div class="asesor-opt-info">' +
                                '<div class="asesor-opt-nombre">' + a.apellidos + ', ' + a.nombres + '</div>' +
                                '<div class="asesor-opt-ci"><i class="ri-passport-line"></i> CI: ' + (a.ci || '—') + (a.celular ? ' · ' + a.celular : '') + '</div>' +
                            '</div>' +
                        '</div>';
                    });
                    $eiAsesorDrop.innerHTML = html;
                });
            }, 300);
        });

        $eiAsesorInp.addEventListener('focus', function () {
            if (this.value.trim().length === 0) {
                $eiAsesorDrop.classList.add('open');
                $eiAsesorDrop.innerHTML = '<div class="asesor-empty">Escribe para buscar...</div>';
                this.dispatchEvent(new Event('input'));
            }
        });

        document.addEventListener('click', function (e) {
            if (!$eiAsesorInp.parentElement.contains(e.target)) {
                $eiAsesorDrop.classList.remove('open');
            }
        });

        $eiAsesorDrop.addEventListener('click', function (e) {
            const opt = e.target.closest('.asesor-opt');
            if (!opt) return;
            const id = opt.dataset.id;
            const nombre = opt.dataset.nombre;
            const ini = nombre.split(',')[1].trim().charAt(0) + nombre.split(',')[0].trim().charAt(0);
            $eiAsesorIdInp.value = id;
            $eiAsesorInp.value = '';
            $eiAsesorDrop.classList.remove('open');
            $eiAsesorSelected.innerHTML =
                '<span class="asesor-selected-card">' +
                    '<span class="asesor-opt-avatar">' + ini.toUpperCase() + '</span>' +
                    '<span>' + nombre + '</span>' +
                    '<i class="ri-close-circle-line" data-clear-asesor></i>' +
                '</span>';
        });

        $eiAsesorSelected.addEventListener('click', function (e) {
            if (e.target.closest('[data-clear-asesor]')) {
                $eiAsesorIdInp.value = '';
                $eiAsesorSelected.innerHTML = '';
            }
        });

        // Guardar
        document.getElementById('ei-guardar')?.addEventListener('click', function () {
            const btn = this;
            const inscId = $eiInscId.value;
            if (!inscId) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('_method', 'PUT');
            fd.append('asesor_id', $eiAsesorIdInp.value);

            fetch('{{ url("admin/programas") }}/' + PROG_ID_EI + '/inscripcion/' + inscId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                bootstrap.Modal.getInstance($eiModal).hide();
                if (data.success) {
                    showToast(data.message || 'Asesor actualizado', 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Error al guardar', 'danger');
                }
            })
            .catch(() => showToast('Error de conexión', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar cambios';
            });
        });
    })();

    // =========================================================
    // =============  INSCRIBIR PARTICIPANTE  ==================
    // =========================================================
    @if((int)$programa->estado_id === 4)
    (function () {
        const PROG_ID = {{ $programa->id }};
        const MATRICULA = {{ (float)($programa->matricula ?? 0) }};
        const COLEGIATURA = {{ (float)($programa->colegiatura ?? 0) }};
        const TOTAL_MODS = {{ $modulos->count() }};

        const $modal = document.getElementById('modalInscribirP');
        const $btnOpen = document.getElementById('btn-inscribir-participante');
        const $ci = document.getElementById('ip-ci');

        // Sin permiso de inscripción el modal no se renderiza: salir sin romper
        // el resto de los scripts (WhatsApp masivo, pagos, etc.).
        if (!$modal || !$btnOpen || !$ci) return;

        const MODULOS_NOMBRES = JSON.parse(document.getElementById('ip-modulos-data').value || '[]');
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

        // Asesor
        const $asesorInp = document.getElementById('ip-asesor');
        const $asesorDrop = document.getElementById('ip-asesor-dropdown');
        const $asesorSelected = document.getElementById('ip-asesor-selected');
        const $asesorIdInp = document.getElementById('ip-asesor-id');

        // Cuotas
        const $cuotaEntera = document.getElementById('ip-cuota-entera');
        const $cuotaUltima = document.getElementById('ip-cuota-ultima');
        const $cuotasDetalle = document.getElementById('ip-cuotas-detalle');
        const $toggleCuotas = document.getElementById('ip-toggle-cuotas');

        let currentParticipante = null;
        let searchTimer = null;
        let asesorTimer = null;

        function resetAll() {
            $ci.value = '';
            $result.innerHTML = '';
            $secNuevo.style.display = 'none';
            $secInsc.style.display = 'none';
            $btnConfirm.disabled = true;
            currentParticipante = null;
            ['ip-nombres','ip-apellidos','ip-celular','ip-correo','ip-departamento'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('ip-sexo').value = '';
            $descMat.value = 0; $descCol.value = 0; $contado.checked = false; $traspaso.checked = false;
            $asesorInp.value = ''; $asesorIdInp.value = '';
            $asesorSelected.innerHTML = '';
            $asesorDrop.classList.remove('open');
            $cuotasDetalle.style.display = 'none';
            recalcDeudas();
        }

        function recalcDeudas() {
            const dm = Math.min(MATRICULA, parseFloat($descMat.value || 0));
            const dc = Math.min(COLEGIATURA, parseFloat($descCol.value || 0));
            const restMat = Math.max(0, MATRICULA - dm);
            const restCol = Math.max(0, COLEGIATURA - dc);
            $deudaMat.textContent = 'Deuda: Bs ' + restMat.toFixed(2);
            $deudaCol.textContent = 'Deuda: Bs ' + restCol.toFixed(2);
            $deudaMat.classList.toggle('ok', restMat === 0);
            $deudaCol.classList.toggle('ok', restCol === 0);
            recalcCuotas(restCol);
        }

        // Al activar "Pago al contado": auto-llenar descuento matrícula con el total
        $contado.addEventListener('change', function () {
            if (this.checked) {
                $descMat.value = MATRICULA.toFixed(2);
                $descMat.dataset.contadoAutofill = '1';
            } else if ($descMat.dataset.contadoAutofill === '1') {
                $descMat.value = 0;
                $descMat.dataset.contadoAutofill = '';
            }
            recalcDeudas();
        });
        function recalcCuotas(deudaCol) {
            if (TOTAL_MODS === 0) {
                $cuotaEntera.textContent = 'Bs 0';
                $cuotaUltima.textContent = 'Bs 0';
                $cuotasDetalle.innerHTML = '';
                return;
            }
            const esContado = $contado.checked;
            const deudaMat = Math.max(0, MATRICULA - parseFloat($descMat.value || 0));

            let cuotaInt, ultima, cuotasCol;
            if (esContado && TOTAL_MODS >= 3) {
                // CONTADO: solo módulos 2 y 3 llevan colegiatura
                cuotasCol = 2;
                cuotaInt = Math.floor(deudaCol / 2);
                ultima = +(deudaCol - cuotaInt).toFixed(2);
            } else if (esContado && TOTAL_MODS === 2) {
                cuotasCol = 1;
                cuotaInt = deudaCol;
                ultima = deudaCol;
            } else {
                // REGULAR
                cuotasCol = Math.max(1, TOTAL_MODS - 1);
                cuotaInt = Math.floor(deudaCol / cuotasCol);
                ultima = +(deudaCol - cuotaInt * (cuotasCol - 1)).toFixed(2);
            }
            $cuotaEntera.textContent = 'Bs ' + cuotaInt.toFixed(2);
            $cuotaUltima.textContent = 'Bs ' + ultima.toFixed(2);

            // Tabla detalle
            let html = '<table><thead><tr><th style="width:50px;" class="text-center">N°</th><th>Módulo</th><th class="text-center" style="width:90px;">Tipo</th><th class="text-end">Monto</th></tr></thead><tbody>';

            // Primer módulo: Matrícula
            const primerNombre = MODULOS_NOMBRES[0] || 'Módulo 1';
            html += '<tr style="background:#dbeafe;">' +
                '<td class="text-center text-muted fw-bold">1</td>' +
                '<td>' + primerNombre + '</td>' +
                '<td class="text-center"><span class="badge bg-soft-primary text-primary" style="font-size:.65rem;">Matrícula</span></td>' +
                '<td class="text-end fw-semibold text-primary">Bs ' + deudaMat.toFixed(2) + '</td>' +
            '</tr>';

            // Resto de módulos
            if (TOTAL_MODS > 1) {
                for (let i = 1; i < TOTAL_MODS; i++) {
                    let monto = 0;
                    let extraClass = '';
                    let isEnRango = false;

                    if (esContado && TOTAL_MODS >= 3) {
                        if (i === 1) { monto = cuotaInt; isEnRango = true; }
                        else if (i === 2) { monto = ultima; isEnRango = true; extraClass = 'ultima'; }
                        else { monto = 0; }
                    } else if (esContado && TOTAL_MODS === 2) {
                        monto = deudaCol; isEnRango = true;
                    } else {
                        const esUltima = (i === TOTAL_MODS - 1);
                        monto = esUltima ? ultima : cuotaInt;
                        if (esUltima) extraClass = 'ultima';
                    }

                    const nombreMod = MODULOS_NOMBRES[i] || ('Módulo ' + (i + 1));
                    const rowStyle = (monto === 0 && esContado) ? 'opacity:.55;' : '';
                    const montoColor = monto === 0 ? 'text-muted' : '';
                    html += '<tr class="' + extraClass + '" style="' + rowStyle + '">' +
                        '<td class="text-center text-muted fw-bold">' + (i + 1) + '</td>' +
                        '<td>' + nombreMod + (extraClass === 'ultima' ? ' <span class="text-warning" style="font-size:.65rem; font-weight:700;">· saldo</span>' : '') + '</td>' +
                        '<td class="text-center"><span class="badge bg-soft-info text-info" style="font-size:.65rem;">Colegiatura</span></td>' +
                        '<td class="text-end fw-semibold ' + montoColor + '">Bs ' + monto.toFixed(2) + '</td>' +
                    '</tr>';
                }
            }
            html += '</tbody></table>';
            $cuotasDetalle.innerHTML = html;
        }
        $descMat.addEventListener('input', recalcDeudas);
        $descCol.addEventListener('input', recalcDeudas);
        $toggleCuotas.addEventListener('click', function () {
            const visible = $cuotasDetalle.style.display !== 'none';
            $cuotasDetalle.style.display = visible ? 'none' : 'block';
            this.innerHTML = (visible ? '<i class="ri-eye-line align-bottom"></i> Ver desglose por módulo' : '<i class="ri-eye-off-line align-bottom"></i> Ocultar desglose');
        });

        // ===== ASESOR SEARCH =====
        $asesorInp.addEventListener('input', function () {
            clearTimeout(asesorTimer);
            const q = this.value.trim();
            $asesorDrop.classList.add('open');
            $asesorDrop.innerHTML = '<div class="asesor-empty"><span class="spinner-border spinner-border-sm me-1"></span>Buscando...</div>';
            asesorTimer = setTimeout(() => {
                fetch('{{ route("admin.programas.buscar-asesores") }}?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.asesores || data.asesores.length === 0) {
                        $asesorDrop.innerHTML = '<div class="asesor-empty">Sin resultados</div>';
                        return;
                    }
                    let html = '';
                    data.asesores.forEach(a => {
                        const ini = ((a.nombres || '').charAt(0) + (a.apellidos || '').charAt(0)).toUpperCase();
                        html += '<div class="asesor-opt" data-id="' + a.id + '" data-nombre="' + (a.apellidos + ', ' + a.nombres).replace(/"/g, '&quot;') + '">' +
                            '<div class="asesor-opt-avatar">' + ini + '</div>' +
                            '<div class="asesor-opt-info">' +
                                '<div class="asesor-opt-nombre">' + a.apellidos + ', ' + a.nombres + '</div>' +
                                '<div class="asesor-opt-ci"><i class="ri-passport-line"></i> CI: ' + (a.ci || '—') + (a.celular ? ' · ' + a.celular : '') + '</div>' +
                            '</div>' +
                        '</div>';
                    });
                    $asesorDrop.innerHTML = html;
                });
            }, 300);
        });
        $asesorInp.addEventListener('focus', function () {
            if (this.value.trim().length === 0) {
                $asesorDrop.classList.add('open');
                $asesorDrop.innerHTML = '<div class="asesor-empty">Escribe para buscar...</div>';
                this.dispatchEvent(new Event('input'));
            }
        });
        document.addEventListener('click', function (e) {
            if (!$asesorInp.parentElement.contains(e.target)) {
                $asesorDrop.classList.remove('open');
            }
        });
        $asesorDrop.addEventListener('click', function (e) {
            const opt = e.target.closest('.asesor-opt');
            if (!opt) return;
            const id = opt.dataset.id;
            const nombre = opt.dataset.nombre;
            const ini = nombre.split(',')[1].trim().charAt(0) + nombre.split(',')[0].trim().charAt(0);
            $asesorIdInp.value = id;
            $asesorInp.value = '';
            $asesorDrop.classList.remove('open');
            $asesorSelected.innerHTML =
                '<span class="asesor-selected-card">' +
                    '<span class="asesor-opt-avatar">' + ini.toUpperCase() + '</span>' +
                    '<span>' + nombre + '</span>' +
                    '<i class="ri-close-circle-line" data-clear-asesor></i>' +
                '</span>';
        });
        $asesorSelected.addEventListener('click', function (e) {
            if (e.target.closest('[data-clear-asesor]')) {
                $asesorIdInp.value = '';
                $asesorSelected.innerHTML = '';
            }
        });

        $btnOpen.addEventListener('click', function () {
            resetAll();
            new bootstrap.Modal($modal).show();
            setTimeout(() => $ci.focus(), 300);
        });

        function showFound(p, yaInscrito) {
            const ini = (p.nombres.charAt(0) + p.apellidos.charAt(0)).toUpperCase();
            if (yaInscrito) {
                $result.innerHTML =
                    '<div class="ci-result-card warning">' +
                        '<div class="ci-avatar" style="background:#f06548;">' + ini + '</div>' +
                        '<div class="ci-info">' +
                            '<div class="ci-nombre">' + p.apellidos + ', ' + p.nombres + '</div>' +
                            '<div class="ci-meta"><i class="ri-error-warning-line text-danger"></i> Ya está inscrito en este programa</div>' +
                        '</div>' +
                    '</div>';
                $secNuevo.style.display = 'none';
                $secInsc.style.display = 'none';
                $btnConfirm.disabled = true;
                currentParticipante = null;
                return;
            }
            $result.innerHTML =
                '<div class="ci-result-card">' +
                    '<div class="ci-avatar">' + ini + '</div>' +
                    '<div class="ci-info">' +
                        '<div class="ci-nombre">' + p.apellidos + ', ' + p.nombres + '</div>' +
                        '<div class="ci-meta"><i class="ri-passport-line"></i> CI: ' + p.ci +
                            (p.celular ? ' · <i class="ri-phone-line"></i> ' + p.celular : '') +
                            (p.correo ? ' · <i class="ri-mail-line"></i> ' + p.correo : '') +
                        '</div>' +
                    '</div>' +
                    '<i class="ri-checkbox-circle-line text-success fs-3"></i>' +
                '</div>';
            $secNuevo.style.display = 'none';
            $secInsc.style.display = '';
            currentParticipante = p;
            $btnConfirm.disabled = false;
        }
        function showNotFound(ci) {
            $result.innerHTML =
                '<div class="ci-result-card notfound">' +
                    '<i class="ri-user-search-line fs-3 text-warning"></i>' +
                    '<div class="ci-info">' +
                        '<div class="ci-nombre">Participante no encontrado</div>' +
                        '<div class="ci-meta">Completa el formulario debajo para registrarlo como nuevo.</div>' +
                    '</div>' +
                '</div>';
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
                    $result.innerHTML = '<div class="ci-result-box" style="padding:.5rem .75rem;"><small class="text-muted"><i class="ri-information-line align-bottom me-1"></i>Mínimo 7 caracteres</small></div>';
                }
                return;
            }
            $ciBox.classList.add('loading');
            searchTimer = setTimeout(() => {
                fetch('{{ url("admin/programas") }}/' + PROG_ID + '/buscar-participante?ci=' + encodeURIComponent(ci), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    $ciBox.classList.remove('loading');
                    if (data.found) showFound(data.participante, data.ya_inscrito);
                    else showNotFound(ci);
                })
                .catch(() => { $ciBox.classList.remove('loading'); });
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

            fetch('{{ url("admin/programas") }}/' + PROG_ID + '/registrar-participante', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showFound(data.participante, false);
                } else {
                    alert(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Error al registrar'));
                }
            })
            .catch(() => alert('Error de conexión'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Registrar y continuar';
            });
        });

        $btnConfirm.addEventListener('click', function () {
            if (!currentParticipante) return;
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
            fd.append('asesor_id', document.getElementById('ip-asesor-id').value);

            fetch('{{ url("admin/programas") }}/' + PROG_ID + '/inscribir-participante', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance($modal).hide();
                    showToast(data.message || 'Inscripción registrada', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    alert(data.message || 'Error al inscribir');
                }
            })
            .catch(() => alert('Error de conexión'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-line align-bottom"></i> Inscribir Participante';
            });
        });
    })();
    @endif
    // ===== RENTABILIDAD CALCULATOR =====
    const RENT_INSCRITOS = {{ $rentInscritos }};
    const RENT_INGRESO_NETO = {{ $rentIngresoNeto }};
    const RENT_INGRESO_XP = {{ $rentIngresoXParticipante }};
    const RENT_COSTO_DOCENTE = {{ $rentCostoDocente }};
    const RENT_COSTO_DOCENTE_SIN_EXTRA = {{ $rentCostoDocenteSinExtra }};
    const RENT_GASTOS_EXTRA = {{ $rentGastosExtra }};
    const RENT_DESC_TOTAL = {{ $rentDescTotal }};
    const RENT_INGRESO_BRUTO = {{ $rentIngresoBruto }};
    const RENT_PRECIO_LISTA = {{ $rentPrecioLista }};
    const RENT_TOTAL_BAJAS = {{ $rentTotalBajas }};

    const $rentMktMonto = document.getElementById('rent-marketing-monto');
    const $rentMktMeses = document.getElementById('rent-marketing-meses');
    const $rentAdminMensual = document.getElementById('rent-admin-mensual');
    const $rentDuracionMeses = document.getElementById('rent-duracion-meses');
    const $rentCostoUni = document.getElementById('rent-costo-uni');
    const $rentPagoDocente = document.getElementById('rent-pago-docente');
    const $rentCantDocentes = document.getElementById('rent-cant-docentes');
    const $rentSimAlumnos = document.getElementById('rent-sim-alumnos');

    const $el = (id) => document.getElementById(id);
    const fmtN = (v) => (v < 0 ? '−' : '') + Math.abs(v).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtBs = (v) => 'Bs ' + fmtN(v);

    // Si no se estima manualmente, se usa el costo docente real registrado en los módulos
    function getCostoDocenteEstimado() {
        const pago = parseFloat($rentPagoDocente.value) || 0;
        const cant = parseInt($rentCantDocentes.value) || 0;
        const estimado = pago * cant;
        return estimado > 0 ? estimado : RENT_COSTO_DOCENTE;
    }

    let rentInitialized = false;

    function calcRentabilidad() {
        const montoMkt = parseFloat($rentMktMonto.value) || 0;
        const mesesMkt = parseInt($rentMktMeses.value) || 1;
        const adminMensual = parseFloat($rentAdminMensual.value) || 0;
        const duracionMeses = parseInt($rentDuracionMeses.value) || 1;
        const costoUni = parseFloat($rentCostoUni.value) || 0;
        const costoDocente = getCostoDocenteEstimado();

        // Costos fijos (no dependen del nº de alumnos) y variables (crecen por alumno)
        const totalMarketing = montoMkt * mesesMkt;
        const totalAdmin = adminMensual * duracionMeses;
        const costosFijos = costoDocente + totalMarketing + totalAdmin;
        const totalUniversidad = costoUni * RENT_INSCRITOS;
        const totalCostos = costosFijos + totalUniversidad;

        const utilidad = RENT_INGRESO_NETO - totalCostos;
        const margen = RENT_INGRESO_NETO > 0 ? (utilidad / RENT_INGRESO_NETO) * 100 : 0;

        // Punto de equilibrio = costos fijos / margen de contribución por alumno
        const margenContrib = RENT_INGRESO_XP - costoUni;
        const puntoEquilibrio = margenContrib > 0 ? Math.ceil(costosFijos / margenContrib) : 0;
        const costoXParticipante = RENT_INSCRITOS > 0 ? totalCostos / RENT_INSCRITOS : 0;
        const porEncima = puntoEquilibrio > 0 && RENT_INSCRITOS >= puntoEquilibrio;
        const diferencia = Math.abs(RENT_INSCRITOS - puntoEquilibrio);

        // Subtítulos de parámetros
        $el('rent-marketing-subtitle').textContent = 'Total campaña: ' + fmtBs(totalMarketing);
        $el('rent-admin-subtitle').textContent = 'Total: ' + fmtBs(totalAdmin) + ' · ' + duracionMeses + ' mes' + (duracionMeses !== 1 ? 'es' : '');
        const $mcLabel = $el('rent-margen-contrib-label');
        $mcLabel.textContent = fmtBs(margenContrib);
        $mcLabel.style.color = margenContrib > 0 ? '#0ab39c' : '#f06548';

        // Result card
        const utilidadClass = utilidad >= 0 ? '#0ab39c' : '#f06548';
        $el('rent-utilidad-label').textContent = fmtBs(utilidad);
        $el('rent-utilidad-label').style.color = utilidadClass;
        $el('rent-margen-label').textContent = margen.toFixed(1) + '%';
        $el('rent-margen-label').style.color = utilidad >= 0 ? '#fff' : '#f06548';
        $el('rent-margen-bar').style.width = Math.max(0, Math.min(100, margen)) + '%';
        $el('rent-margen-bar').style.background = utilidad >= 0
            ? 'linear-gradient(90deg, #0ab39c, #fff)'
            : 'linear-gradient(90deg, #f06548, #fff)';

        // Break-even
        $el('rent-equilibrio-label').textContent = puntoEquilibrio.toLocaleString('es-BO');
        $el('rent-equilibrio-label').style.color = porEncima ? '#0ab39c' : '#f06548';
        $el('rent-costo-docente-label').textContent = fmtBs(costoDocente);
        $el('rent-costo-total-label').textContent = fmtBs(totalCostos);
        $el('rent-costo-xp-label').textContent = fmtBs(costoXParticipante);

        // Bars
        const maxVal = Math.max(RENT_INSCRITOS, puntoEquilibrio, 1);
        const hActual = Math.max(20, (RENT_INSCRITOS / maxVal) * 100);
        const hPe = Math.max(20, (puntoEquilibrio / maxVal) * 100);
        const hProy = Math.max(20, (Math.max(RENT_INSCRITOS, puntoEquilibrio) / maxVal) * 100);
        $el('rent-bar-actual').style.height = hActual + 'px';
        $el('rent-bar-pe').style.height = hPe + 'px';
        $el('rent-bar-proy').style.height = hProy + 'px';
        $el('rent-bar-actual-val').textContent = RENT_INSCRITOS;
        $el('rent-bar-pe-val').textContent = puntoEquilibrio;
        $el('rent-bar-proy-val').textContent = Math.max(RENT_INSCRITOS, puntoEquilibrio);

        // Status badge
        const badge = $el('rent-badge-estado');
        const icon = $el('rent-badge-icon');
        const text = $el('rent-badge-text');
        const detalle = $el('rent-badge-detalle');
        if (RENT_INSCRITOS === 0) {
            badge.style.background = '#fef0f0';
            icon.className = 'ri-error-warning-line fs-5';
            icon.style.color = '#f06548';
            text.textContent = 'Sin participantes inscritos';
            text.style.color = '#f06548';
            detalle.textContent = 'No hay datos para calcular rentabilidad';
        } else if (porEncima) {
            badge.style.background = '#e8faf1';
            icon.className = 'ri-checkbox-circle-line fs-5';
            icon.style.color = '#0ab39c';
            text.textContent = 'Por encima del punto de equilibrio';
            text.style.color = '#0ab39c';
            detalle.textContent = diferencia + ' participante' + (diferencia !== 1 ? 's' : '') + ' por encima · Margen de seguridad ' + margen.toFixed(1) + '%';
        } else if (puntoEquilibrio === 0) {
            badge.style.background = '#fef0f0';
            icon.className = 'ri-error-warning-line fs-5';
            icon.style.color = '#f06548';
            text.textContent = 'Margen de contribución insuficiente';
            text.style.color = '#f06548';
            detalle.textContent = 'El costo variable por alumno (' + fmtBs(costoUni) + ') iguala o supera el ingreso por alumno (' + fmtBs(RENT_INGRESO_XP) + ') — no hay punto de equilibrio posible';
        } else {
            badge.style.background = '#fef0f0';
            icon.className = 'ri-arrow-down-circle-line fs-5';
            icon.style.color = '#f06548';
            text.textContent = 'Debajo del punto de equilibrio';
            text.style.color = '#f06548';
            detalle.textContent = 'Faltan ' + diferencia + ' participante' + (diferencia !== 1 ? 's' : '') + ' para cubrir costos';
        }

        // Table rows
        $el('rent-td-pct-desc').textContent = RENT_INGRESO_BRUTO > 0 ? ((RENT_DESC_TOTAL / RENT_INGRESO_BRUTO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-pct-neto').textContent = RENT_INGRESO_BRUTO > 0 ? ((RENT_INGRESO_NETO / RENT_INGRESO_BRUTO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-costo-docente').textContent = fmtBs(costoDocente);
        $el('rent-td-pct-docente').textContent = RENT_INGRESO_NETO > 0 ? ((costoDocente / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';
        var pctGastosExtraEl = $el('rent-td-pct-gastos-extra');
        if (pctGastosExtraEl) pctGastosExtraEl.textContent = RENT_INGRESO_NETO > 0 ? ((RENT_GASTOS_EXTRA / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-uni').textContent = fmtBs(totalUniversidad);
        $el('rent-td-uni-detalle').textContent = costoUni > 0 ? '(' + fmtBs(costoUni) + ' × ' + RENT_INSCRITOS + ' inscritos)' : '';
        $el('rent-td-pct-uni').textContent = RENT_INGRESO_NETO > 0 ? ((totalUniversidad / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-marketing').textContent = fmtBs(totalMarketing);
        $el('rent-td-marketing-detalle').textContent = totalMarketing > 0 ? '(' + fmtBs(montoMkt) + ' × ' + mesesMkt + ' meses)' : '';
        $el('rent-td-pct-marketing').textContent = RENT_INGRESO_NETO > 0 ? ((totalMarketing / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-admin').textContent = fmtBs(totalAdmin);
        $el('rent-td-admin-detalle').textContent = totalAdmin > 0 ? '(' + fmtBs(adminMensual) + ' × ' + duracionMeses + ' meses)' : '';
        $el('rent-td-pct-admin').textContent = RENT_INGRESO_NETO > 0 ? ((totalAdmin / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';
        $el('rent-td-costo-total').textContent = fmtBs(totalCostos);
        $el('rent-td-pct-total').textContent = RENT_INGRESO_NETO > 0 ? ((totalCostos / RENT_INGRESO_NETO) * 100).toFixed(1) + '%' : '0%';

        const utilLabel = $el('rent-td-utilidad-label');
        const utilTd = $el('rent-td-utilidad');
        const margenTd = $el('rent-td-margen');
        if (utilidad >= 0) {
            utilLabel.style.color = '#0ab39c';
            utilLabel.innerHTML = '<i class="ri-money-dollar-circle-line me-1"></i> UTILIDAD';
            utilTd.style.color = '#0ab39c';
            utilTd.textContent = fmtBs(utilidad);
            margenTd.style.color = '#0ab39c';
            margenTd.textContent = margen.toFixed(1) + '%';
        } else {
            utilLabel.style.color = '#f06548';
            utilLabel.innerHTML = '<i class="ri-error-warning-line me-1"></i> PÉRDIDA';
            utilTd.style.color = '#f06548';
            utilTd.textContent = fmtBs(utilidad);
            margenTd.style.color = '#f06548';
            margenTd.textContent = margen.toFixed(1) + '%';
        }

        // ===== VIABILIDAD DE INICIO (precio de lista) =====
        const margenLista = RENT_PRECIO_LISTA - costoUni;
        const minInicio = margenLista > 0 ? Math.ceil(costosFijos / margenLista) : null;
        const $minInicio = $el('rent-min-inicio');
        $minInicio.textContent = minInicio !== null ? minInicio : '∞';
        $minInicio.style.color = minInicio !== null ? '#6559cc' : '#f06548';
        $el('rent-via-costo-uni').textContent = fmtBs(costoUni);
        const $viaMargen = $el('rent-via-margen');
        $viaMargen.textContent = fmtBs(margenLista);
        $viaMargen.style.color = margenLista > 0 ? '#0ab39c' : '#f06548';
        $el('rent-via-fijos').textContent = fmtBs(costosFijos);

        // Simulador: utilidad proyectada con N inscritos a precio de lista
        const simAlumnos = parseInt($rentSimAlumnos.value) || 0;
        const utilidadSim = simAlumnos * margenLista - costosFijos;
        const $simRes = $el('rent-sim-resultado');
        if (margenLista <= 0) {
            $simRes.textContent = 'Margen por alumno negativo';
            $simRes.style.background = '#fef0f0';
            $simRes.style.color = '#f06548';
        } else if (utilidadSim >= 0) {
            $simRes.textContent = 'Con ' + simAlumnos + ' inscritos: utilidad de ' + fmtBs(utilidadSim);
            $simRes.style.background = '#e8faf1';
            $simRes.style.color = '#0ab39c';
        } else {
            $simRes.textContent = 'Con ' + simAlumnos + ' inscritos: pérdida de ' + fmtBs(Math.abs(utilidadSim)) + ' · faltan ' + Math.max(0, minInicio - simAlumnos) + ' inscritos';
            $simRes.style.background = '#fef0f0';
            $simRes.style.color = '#f06548';
        }

        // ===== ALERTAS =====
        const alertas = [];
        if (RENT_INSCRITOS > 0 && (RENT_TOTAL_BAJAS / RENT_INSCRITOS) > 0.15) {
            alertas.push({ tipo: 'warning', icono: 'ri-user-unfollow-line', titulo: 'Alta deserción',
                texto: RENT_TOTAL_BAJAS + ' de ' + RENT_INSCRITOS + ' inscritos son bajas (' + ((RENT_TOTAL_BAJAS / RENT_INSCRITOS) * 100).toFixed(0) + '%). Revisa las causas antes de proyectar nuevos ingresos.' });
        }
        if (RENT_INSCRITOS > 0 && margenContrib <= 0 && costoUni > 0) {
            alertas.push({ tipo: 'danger', icono: 'ri-alert-line', titulo: 'Crítico',
                texto: 'Cada alumno genera pérdida: el costo universidad (' + fmtBs(costoUni) + ') supera el ingreso promedio por alumno (' + fmtBs(RENT_INGRESO_XP) + ').' });
        }
        if (RENT_INSCRITOS > 0) {
            if (utilidad < 0) {
                alertas.push({ tipo: 'danger', icono: 'ri-error-warning-line', titulo: 'Pérdida proyectada',
                    texto: 'Con los inscritos actuales el programa pierde ' + fmtBs(Math.abs(utilidad)) + '. Se necesitan ' + Math.max(0, puntoEquilibrio - RENT_INSCRITOS) + ' inscritos más para cubrir costos.' });
            } else if (margen < 15) {
                alertas.push({ tipo: 'info', icono: 'ri-information-line', titulo: 'Margen bajo',
                    texto: 'Utilidad de ' + fmtBs(utilidad) + ' (' + margen.toFixed(1) + '%). El programa cubre costos pero con poco margen de seguridad.' });
            } else {
                alertas.push({ tipo: 'success', icono: 'ri-checkbox-circle-line', titulo: 'Programa rentable',
                    texto: 'Utilidad proyectada de ' + fmtBs(utilidad) + ' con un margen del ' + margen.toFixed(1) + '%.' });
            }
        } else if (minInicio !== null) {
            alertas.push({ tipo: 'info', icono: 'ri-rocket-2-line', titulo: 'Programa sin inscritos',
                texto: 'Para iniciar cubriendo costos se necesitan al menos ' + minInicio + ' inscritos a precio de lista (' + fmtBs(RENT_PRECIO_LISTA) + ' por alumno).' });
        }
        const colores = { danger: ['#fef0f0', '#f06548'], warning: ['#fff8f0', '#f7b84b'], info: ['#eef7fb', '#299cdb'], success: ['#e8faf1', '#0ab39c'] };
        $el('rent-alertas').innerHTML = alertas.map(function (a) {
            const c = colores[a.tipo];
            return '<div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 mb-2" style="background:' + c[0] + ';border-left:4px solid ' + c[1] + ';">'
                + '<i class="' + a.icono + ' fs-5" style="color:' + c[1] + ';"></i>'
                + '<div><span style="font-size:.8rem;font-weight:700;color:' + c[1] + ';">' + a.titulo + ':</span> '
                + '<span style="font-size:.78rem;color:#495057;">' + a.texto + '</span></div></div>';
        }).join('');
    }

    // Init rentabilidad tab on first show
    const tabRent = document.querySelector('a[href="#tab-rentabilidad"]');
    if (tabRent) {
        tabRent.addEventListener('shown.bs.tab', function () {
            if (!rentInitialized) {
                rentInitialized = true;
                calcRentabilidad();
            }
        });
    }

    // Input listeners
    [$rentMktMonto, $rentMktMeses, $rentAdminMensual, $rentDuracionMeses, $rentCostoUni, $rentPagoDocente, $rentCantDocentes, $rentSimAlumnos].filter(Boolean).forEach(function (el) {
        el.addEventListener('input', calcRentabilidad);
        el.addEventListener('change', calcRentabilidad);
    });

    // Guardar parámetros de rentabilidad
    document.getElementById('rent-btn-guardar')?.addEventListener('click', function () {
        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('costo_universidad_alumno', parseFloat($rentCostoUni.value) || 0);
        fd.append('gastos_admin_mensual', parseFloat($rentAdminMensual.value) || 0);
        fd.append('presupuesto_campana', parseFloat($rentMktMonto.value) || 0);
        fd.append('meses_campana', parseInt($rentMktMeses.value) || 1);
        fd.append('duracion_meses', parseInt($rentDuracionMeses.value) || 1);

        fetch('{{ url("admin/programas") }}/{{ $programa->id }}/rentabilidad', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            if (ok && data && data.success) {
                showToast(data.message || 'Parámetros guardados', 'success');
            } else {
                showToast((data && data.message) || 'Error al guardar', 'danger');
            }
        })
        .catch((err) => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showToast('Error de conexión: ' + (err.message || ''), 'danger');
        });
    });

    // ===== MODAL PAGO DOCENTE =====
    const $mpdModal = document.getElementById('modalPagoDocente');
    const $mpdForm = document.getElementById('form-pago-docente');
    const $mpdModuloId = document.getElementById('mpd-modulo-id');
    const $mpdModuloNombre = document.getElementById('mpd-modulo-nombre');
    const $mpdPagoDocente = document.getElementById('mpd-pago-docente');
    const $mpdFactura = document.getElementById('mpd-factura');
    const $mpdIva = document.getElementById('mpd-iva');
    const $mpdIt = document.getElementById('mpd-it');
    const $mpdIvaGroup = document.getElementById('mpd-iva-group');
    const $mpdItGroup = document.getElementById('mpd-it-group');
    const $mpdGastosExtra = document.getElementById('mpd-gastos-extra');
    const $mpdTotalPagar = document.getElementById('mpd-total-pagar');
    const $mpdTotalLabelDetalle = document.getElementById('mpd-total-label-detalle');
    const $mpdFechaPago = document.getElementById('mpd-fecha-pago');
    const $mpdRegularizar = document.getElementById('mpd-regularizar');

    function mpdCalcularRetenciones() {
        const pago = parseFloat($mpdPagoDocente.value) || 0;
        const emiteFactura = $mpdFactura.checked;
        if (emiteFactura) {
            $mpdIva.value = '0';
            $mpdIt.value = '0';
            $mpdIva.readOnly = false;
            $mpdIt.readOnly = false;
        } else {
            $mpdIva.value = (pago * 0.13).toFixed(2);
            $mpdIt.value = (pago * 0.03).toFixed(2);
            $mpdIva.readOnly = true;
            $mpdIt.readOnly = true;
        }
        mpdActualizarTotal();
    }

    function mpdActualizarTotal() {
        const pago = parseFloat($mpdPagoDocente.value) || 0;
        const iva = parseFloat($mpdIva.value) || 0;
        const it = parseFloat($mpdIt.value) || 0;
        const gastos = parseFloat($mpdGastosExtra.value) || 0;
        const emiteFactura = $mpdFactura.checked;
        const total = emiteFactura ? pago + gastos : Math.max(0, pago - iva - it) + gastos;
        $mpdTotalPagar.textContent = 'Bs ' + total.toFixed(2);
        if ($mpdTotalLabelDetalle) {
            $mpdTotalLabelDetalle.textContent = emiteFactura
                ? 'Pago Docente + Gastos Extra'
                : '(Pago Docente - IVA - IT) + Gastos Extra';
        }
    }

    function mpdToggleFactura() {
        const show = $mpdFactura.checked;
        $mpdIvaGroup.style.display = show ? 'none' : '';
        $mpdItGroup.style.display = show ? 'none' : '';
        mpdCalcularRetenciones();
    }
    $mpdFactura.addEventListener('change', mpdToggleFactura);
    $mpdPagoDocente.addEventListener('input', function () {
        if (!$mpdFactura.checked) mpdCalcularRetenciones();
        else mpdActualizarTotal();
    });
    $mpdGastosExtra.addEventListener('input', mpdActualizarTotal);

    function mpdProximaFecha(dia) {
        var today = new Date();
        var y = today.getFullYear();
        var m = today.getMonth();
        var cand = new Date(y, m, dia);
        if (cand < today) {
            cand = new Date(y, m + 1, dia);
        }
        return cand.toISOString().split('T')[0];
    }

    document.addEventListener('click', function (e) {
        var btnFecha = e.target.closest('.btn-mpd-fecha-prox');
        if (btnFecha) {
            var dia = parseInt(btnFecha.dataset.dia);
            $mpdFechaPago.value = mpdProximaFecha(dia);
            return;
        }
    });

    $mpdRegularizar.addEventListener('change', function () {
        if ($mpdRegularizar.checked) {
            $mpdFechaPago.removeAttribute('min');
        } else {
            var hoy = new Date().toISOString().split('T')[0];
            if ($mpdFechaPago.value && $mpdFechaPago.value < hoy) {
                $mpdFechaPago.value = mpdProximaFecha(10);
            }
        }
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-pago-docente');
        if (!btn) return;
        $mpdModuloId.value = btn.dataset.moduloId;
        $mpdModuloNombre.textContent = btn.dataset.moduloNombre;
        $mpdPagoDocente.value = parseFloat(btn.dataset.pagoDocente) || 0;
        $mpdFactura.checked = parseInt(btn.dataset.factura) === 1;
        $mpdIva.value = parseFloat(btn.dataset.iva) || 0;
        $mpdIt.value = parseFloat(btn.dataset.it) || 0;
        $mpdGastosExtra.value = parseFloat(btn.dataset.gastosExtra) || 0;
        $mpdFechaPago.value = btn.dataset.fechaPago || mpdProximaFecha(10);
        $mpdRegularizar.checked = false;
        mpdToggleFactura();
        new bootstrap.Modal($mpdModal).show();
    });

    $mpdForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btn-mpd-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('pago_docente', $mpdPagoDocente.value || 0);
        formData.append('factura', $mpdFactura.checked ? 1 : 0);
        formData.append('iva', $mpdIva.value || 0);
        formData.append('it', $mpdIt.value || 0);
        formData.append('gastos_extra', $mpdGastosExtra.value || 0);
        formData.append('fecha_pago', $mpdFechaPago.value || '');

        fetch('{{ url("admin/programas/modulo") }}/' + $mpdModuloId.value + '/pago-docente', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                bootstrap.Modal.getInstance($mpdModal).hide();
                showToast(data.message || 'Guardado correctamente', 'success');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                alert(data.message || 'Error al guardar');
            }
        })
        .catch(function () { alert('Error de conexión'); })
        .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-save-3-line"></i> Guardar';
                });
            });

            // ===== MODAL PROCESAR PAGO DOCENTE =====
            const $ppdModal = document.getElementById('modalProcesarPagoDocente');
            const $ppdForm = document.getElementById('form-procesar-pago-docente');
            const $ppdModuloId = document.getElementById('ppd-modulo-id');
            const $ppdModuloNombre = document.getElementById('ppd-modulo-nombre');
            const $ppdFechaPago = document.getElementById('ppd-fecha-pago');
            const $ppdResumenMonto = document.getElementById('ppd-resumen-monto');
            const $ppdResumenTipo = document.getElementById('ppd-resumen-tipo');
            const $ppdResumenRetenciones = document.getElementById('ppd-resumen-retenciones');
            const $ppdResumenIvaValor = document.getElementById('ppd-resumen-iva-valor');
            const $ppdResumenItValor = document.getElementById('ppd-resumen-it-valor');
            const $ppdResumenExtra = document.getElementById('ppd-resumen-extra');
            const $ppdResumenExtraValor = document.getElementById('ppd-resumen-extra-valor');
            const $ppdResumenTotal = document.getElementById('ppd-resumen-total');
            const $ppdDetalleFactura = document.getElementById('ppd-detalle-factura');
            const $ppdDetalleRetencion = document.getElementById('ppd-detalle-retencion');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-procesar-pago-docente');
                if (!btn) return;

                const moduloId = btn.dataset.moduloId;
                const moduloNombre = btn.dataset.moduloNombre;
                const pagoDocente = parseFloat(btn.dataset.pagoDocente) || 0;
                const factura = parseInt(btn.dataset.factura) === 1;
                const gastosExtra = parseFloat(btn.dataset.gastosExtra) || 0;

                const iva = factura ? 0 : Math.round(pagoDocente * 0.13 * 100) / 100;
                const it = factura ? 0 : Math.round(pagoDocente * 0.03 * 100) / 100;

                $ppdModuloId.value = moduloId;
                $ppdModuloNombre.textContent = moduloNombre;
                $ppdFechaPago.value = new Date().toISOString().split('T')[0];

                $ppdResumenMonto.textContent = 'Bs ' + pagoDocente.toFixed(2);

                if (factura) {
                    $ppdResumenTipo.textContent = 'Con Factura';
                    $ppdDetalleFactura.checked = true;
                    $ppdResumenRetenciones.style.display = 'none';
                } else {
                    $ppdResumenTipo.textContent = 'Con Retención';
                    $ppdDetalleRetencion.checked = true;
                    $ppdResumenRetenciones.style.display = '';
                    $ppdResumenIvaValor.textContent = 'Bs ' + iva.toFixed(2);
                    $ppdResumenItValor.textContent = 'Bs ' + it.toFixed(2);
                }

                if (gastosExtra > 0) {
                    $ppdResumenExtra.style.display = '';
                    $ppdResumenExtraValor.textContent = 'Bs ' + gastosExtra.toFixed(2);
                } else {
                    $ppdResumenExtra.style.display = 'none';
                }

                const total = factura ? (pagoDocente + gastosExtra) : (pagoDocente - iva - it) + gastosExtra;
                $ppdResumenTotal.textContent = 'Bs ' + Math.max(0, total).toFixed(2);

                new bootstrap.Modal($ppdModal).show();
            });

            $ppdForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('btn-ppd-save');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

                const detallePago = document.querySelector('input[name="ppd-detalle-pago"]:checked');
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('fecha_pago', $ppdFechaPago.value || '');
                formData.append('detalle_pago', detallePago ? detallePago.value : 'factura');

                fetch('{{ url("admin/programas/modulo") }}/' + $ppdModuloId.value + '/procesar-pago', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance($ppdModal).hide();
                        showToast(data.message || 'Pago procesado correctamente', 'success');
                        setTimeout(function () { location.reload(); }, 800);
                    } else {
                        alert(data.message || 'Error al procesar el pago');
                    }
                })
                .catch(function () { alert('Error de conexión'); })
                .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-checkbox-circle-line"></i> Marcar como Pagado';
                });
            });

            // ---- Fechas de pago por participante (tab contable) ----
            var mfpModalEl = document.getElementById('modalFechasParticipante');
            var mfpModal = mfpModalEl ? new bootstrap.Modal(mfpModalEl) : null;
            var mfpProgId = null;
            var mfpPartId = null;
            var mfpPlan = null;
            var mfpCuotas = { Matricula: [], Colegiatura: [] };

            document.querySelectorAll('.btn-open-fechas-participante').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    mfpProgId = this.dataset.programa;
                    mfpPartId = this.dataset.id;
                    document.getElementById('mfpSubtitle').textContent = this.dataset.nombre;
                    cargarFechasParticipante();
                });
            });

            function cargarFechasParticipante() {
                document.getElementById('mfpLoader').style.display = 'block';
                document.getElementById('mfpContent').style.display = 'none';
                mfpModal.show();

                fetch('/admin/programas/' + mfpProgId + '/participante/' + mfpPartId + '/fechas', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    document.getElementById('mfpLoader').style.display = 'none';
                    document.getElementById('mfpContent').style.display = 'block';
                    if (!data.success) {
                        showToast(data.message || 'Error al cargar', 'danger');
                        return;
                    }
                    mfpPlan = data.plan;
                    mfpCuotas = data.cuotas;
                    document.getElementById('mfpPlan').textContent = data.plan + (data.contado === 1 ? ' (Contado)' : ' (Regular)');
                    document.getElementById('mfpTotalCuotas').textContent = data.total_matricula + ' Matrícula · ' + data.total_colegiatura + ' Colegiatura';

                    renderFechasParticipante('Matricula', data.cuotas.Matricula, 'mfpMatList', 'mfpMatCount');
                    renderFechasParticipante('Colegiatura', data.cuotas.Colegiatura, 'mfpColList', 'mfpColCount');
                })
                .catch(function () {
                    document.getElementById('mfpLoader').style.display = 'none';
                    document.getElementById('mfpContent').style.display = 'block';
                    showToast('Error de conexión', 'danger');
                });
            }

            function renderFechasParticipante(tipo, arr, listId, countId) {
                var list = document.getElementById(listId);
                var countEl = document.getElementById(countId);
                if (!list) return;
                var html = '';
                (arr || []).forEach(function (it, idx) {
                    html +=
                        '<div class="fecha-row">' +
                            '<span class="num">' + (idx + 1) + '</span>' +
                            '<input type="date" value="' + (it.fecha_pago || '') + '" data-cuota-id="' + it.id + '">' +
                        '</div>';
                });
                list.innerHTML = html;
                if (countEl) countEl.textContent = (arr ? arr.length : 0) + ' cuota' + ((arr ? arr.length : 0) !== 1 ? 's' : '');
            }

            function recogerFechasParticipante() {
                ['Matricula', 'Colegiatura'].forEach(function (tipo) {
                    var containerId = tipo === 'Matricula' ? 'mfpMatList' : 'mfpColList';
                    var inputs = document.querySelectorAll('#' + containerId + ' input[type=date]');
                    var arr = [];
                    inputs.forEach(function (inp) {
                        arr.push({ id: inp.dataset.cuotaId, fecha_pago: inp.value });
                    });
                    mfpCuotas[tipo] = arr;
                });
            }

            function pad2(n) { return String(n).padStart(2, '0'); }
            function isoDate(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }
            function safeDate(year, monthIdx, day) {
                var maxDay = new Date(year, monthIdx + 1, 0).getDate();
                if (day <= maxDay) return new Date(year, monthIdx, day);
                return new Date(year, monthIdx + 1, 1);
            }

            // ---- Generar fechas (Aplicar) ----
            document.querySelectorAll('[data-mfp-gen-apply]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var tipo = this.dataset.mfpGenApply;
                    var diaInp = document.querySelector('[data-mfp-gen-dia="' + tipo + '"]');
                    var mesInp = document.querySelector('[data-mfp-gen-mes="' + tipo + '"]');
                    var dia = parseInt(diaInp.value) || 0;
                    var mesVal = mesInp.value;
                    var listId = tipo === 'Matricula' ? 'mfpMatList' : 'mfpColList';
                    var inputs = document.querySelectorAll('#' + listId + ' input[type=date]');

                    if (inputs.length === 0) {
                        showToast('No hay cuotas para generar fechas', 'warning');
                        return;
                    }
                    if (!dia && !mesVal) {
                        showToast('Ingresa al menos el día o el mes/año de inicio', 'warning');
                        return;
                    }

                    inputs.forEach(function (inp, i) {
                        var useDay = dia;
                        if (!useDay && inp.value) {
                            useDay = parseInt(inp.value.split('-')[2]);
                        }
                        if (!useDay) useDay = 1;

                        var year, month;
                        if (mesVal) {
                            var parts = mesVal.split('-').map(Number);
                            year = parts[0];
                            var startMonth0 = parts[1] - 1;
                            var total = startMonth0 + i;
                            year = year + Math.floor(total / 12);
                            month = total % 12;
                        } else if (inp.value) {
                            var p = inp.value.split('-').map(Number);
                            year = p[0]; month = p[1] - 1;
                        } else {
                            var t = new Date();
                            year = t.getFullYear(); month = t.getMonth();
                        }

                        var d = safeDate(year, month, useDay);
                        inp.value = isoDate(d);
                    });

                    showToast(inputs.length + ' fechas generadas para ' + tipo, 'info');
                });
            });

            document.getElementById('btnSaveFechasParticipante')?.addEventListener('click', function () {
                recogerFechasParticipante();
                var btn = this;
                var orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

                var fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                ['Matricula', 'Colegiatura'].forEach(function (tipo) {
                    (mfpCuotas[tipo] || []).forEach(function (it) {
                        if (it.fecha_pago) fd.append('fechas[' + tipo + '][]', it.fecha_pago);
                    });
                });

                fetch('/admin/programas/' + mfpProgId + '/participante/' + mfpPartId + '/fechas', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd,
                })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
                .then(function (r) {
                    if (r.ok && r.data.success) {
                        showToast(r.data.message + ' · ' + r.data.cuotas_actualizadas + ' cuotas', 'success');
                        mfpModal.hide();
                    } else {
                        showToast(r.data.message || 'No se pudo guardar', 'danger');
                    }
                })
                .catch(function () { showToast('Error de conexión', 'danger'); })
                .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
            });

            // ---- Certificación: Abrir modal de pago ----
            const modalPagoCertEl = document.getElementById('modalPagoCert');
            let mpcModal = null;
            if (modalPagoCertEl) mpcModal = new bootstrap.Modal(modalPagoCertEl);

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-pagar-cert');
                if (!btn || !mpcModal) return;
                document.getElementById('mpc-participante-id').value = btn.dataset.participante;
                document.getElementById('mpc-participante-nombre').textContent = btn.dataset.participanteNombre;
                document.getElementById('mpc-deuda-total').value = btn.dataset.deuda;
                document.getElementById('mpc-pagado-actual').value = btn.dataset.pagado;
                document.getElementById('mpc-deuda-show').textContent = parseFloat(btn.dataset.deuda).toFixed(2);
                document.getElementById('mpc-pagado-show').textContent = parseFloat(btn.dataset.pagado).toFixed(2);
                const saldo = parseFloat(btn.dataset.saldo);
                const saldoEl = document.getElementById('mpc-saldo-show');
                saldoEl.textContent = 'Bs ' + saldo.toFixed(2);
                saldoEl.style.color = saldo > 0 ? '#f06548' : '#0ab39c';
                document.getElementById('mpc-monto').value = saldo.toFixed(2);
                document.getElementById('mpc-monto').max = saldo;
                document.getElementById('mpc-fecha').value = new Date().toISOString().split('T')[0];
                mpcModal.show();
            });

            // ---- Certificación: Guardar pago ----
            document.getElementById('form-pago-cert')?.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('btn-mpc-save');
                btn.disabled = true;
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

                const programaId = '{{ $programa->id }}';
                const participanteId = document.getElementById('mpc-participante-id').value;
                const monto = document.getElementById('mpc-monto').value;
                const fecha = document.getElementById('mpc-fecha').value;

                if (!monto || parseFloat(monto) <= 0) {
                    showToast('Ingrese un monto válido', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('monto', monto);
                formData.append('fecha', fecha);

                fetch('{{ url("admin/programas") }}/' + programaId + '/participante/' + participanteId + '/certificacion-pago', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        mpcModal.hide();
                        showToast(data.message || 'Pago registrado correctamente', 'success');
                        setTimeout(function () { location.reload(); }, 1200);
                    } else {
                        showToast(data.message || 'Error al registrar pago', 'danger');
                    }
                })
                .catch(function () { showToast('Error de conexión', 'danger'); })
                .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
            });

            // ---- Certificación: Generar deudas en masa (modal) ----
            const btnGenCert = document.getElementById('btn-generar-deudas-cert');
            const modalGenCert = document.getElementById('modalGenerarCert');
            if (btnGenCert && modalGenCert) {
                const modal = new bootstrap.Modal(modalGenCert);
                btnGenCert.addEventListener('click', function () {
                    modal.show();
                });
                document.getElementById('cert-gen-confirm')?.addEventListener('click', function () {
                    const btn = this;
                    btn.disabled = true;
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generando...';

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ url("admin/programas") }}/' + '{{ $programa->id }}' + '/generar-deudas-certificacion', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        modal.hide();
                        if (data.success) {
                            showToast(data.message || 'Deudas generadas correctamente', 'success');
                            setTimeout(function () { location.reload(); }, 1200);
                        } else {
                            showToast(data.message || 'Error al generar deudas', 'danger');
                        }
                    })
                    .catch(function () { showToast('Error de conexión', 'danger'); })
                    .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
                });
            }

            // ---- Modalidad Titulación ----
            const modalidadModal = document.getElementById('modalModalidadTitulacion');
            const modalidadSingleView = document.getElementById('modalidad-single-view');
            const modalidadMultiView = document.getElementById('modalidad-multi-view');
            const modalidadSingleNombre = document.getElementById('modalidad-single-nombre');
            const modalidadSingleCi = document.getElementById('modalidad-single-ci');
            const modalidadSingleActual = document.getElementById('modalidad-single-actual');
            const modalidadSingleSelect = document.getElementById('modalidad-single-select');
            const modalidadCount = document.getElementById('modalidad-count');
            let modalidadModalIns = null;
            if (modalidadModal) modalidadModalIns = new bootstrap.Modal(modalidadModal);

            // Header button: mostrar todos
            document.getElementById('btn-modalidad-global')?.addEventListener('click', function () {
                modalidadSingleView.style.display = 'none';
                modalidadMultiView.style.display = 'block';
                document.getElementById('modalidad-subtitle').textContent = 'Seleccione Convenio o Trámite Personal para cada participante';
                modalidadModalIns.show();
            });

            // Badge por participante: mostrar solo ese
            document.querySelectorAll('.btn-modalidad-single').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const pid = parseInt(this.dataset.participante);
                    const row = document.querySelector('.modalidad-row[data-participante="' + pid + '"]');
                    if (!row) return;
                    const nombre = row.querySelector('td:nth-child(2) .fw-semibold').textContent.trim();
                    const ci = row.querySelector('td:nth-child(2) small')?.textContent.trim() || '';
                    const select = row.querySelector('.modalidad-select');
                    const valorActual = select.value;
                    modalidadSingleNombre.textContent = nombre;
                    modalidadSingleCi.textContent = ci;
                    modalidadSingleActual.textContent = valorActual === '1' ? 'Convenio' : 'Trámite Personal';
                    modalidadSingleSelect.value = valorActual;
                    // Marcar para que al guardar solo se envie este
                    modalidadModal.dataset.singlePid = pid;
                    modalidadSingleView.style.display = 'block';
                    modalidadMultiView.style.display = 'none';
                    document.getElementById('modalidad-subtitle').textContent = 'Cambiar modalidad de ' + nombre;
                    modalidadModalIns.show();
                });
            });

            // Guardar cambios
            document.getElementById('btn-guardar-modalidad')?.addEventListener('click', function () {
                const isSingle = modalidadSingleView.style.display !== 'none' && modalidadSingleView.style.display !== '';
                let participantes = [];

                if (isSingle) {
                    const pid = parseInt(modalidadModal.dataset.singlePid);
                    participantes.push({
                        participante_id: pid,
                        modalidad: parseInt(modalidadSingleSelect.value),
                    });
                } else {
                    document.querySelectorAll('.modalidad-select').forEach(function (sel) {
                        participantes.push({
                            participante_id: parseInt(sel.dataset.participante),
                            modalidad: parseInt(sel.value),
                        });
                    });
                }

                if (participantes.length === 0) {
                    showToast('No hay participantes para actualizar', 'warning');
                    return;
                }

                const btn = this;
                btn.disabled = true;
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

                const fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                participantes.forEach(function (p, i) {
                    fd.append('participantes[' + i + '][participante_id]', p.participante_id);
                    fd.append('participantes[' + i + '][modalidad]', p.modalidad);
                });

                fetch('{{ url("admin/programas") }}/' + '{{ $programa->id }}' + '/participantes/modalidad-titulacion', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        showToast(data.message || 'Modalidades actualizadas', 'success');
                        setTimeout(function () { location.reload(); }, 1000);
                    } else {
                        showToast(data.message || 'Error al guardar', 'danger');
                    }
                })
                .catch(function () { showToast('Error de conexión', 'danger'); })
                .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
            });

            // ---- Folios Universidad: Registrar participantes en folio ----
            const COSTO_UNIV_INSC = {{ $programa->costo_pago_inscripcion_universidad ?? 0 }};
            const COSTO_UNIV_MAT = {{ $programa->costo_pago_matricula_universidad ?? 0 }};
            const COSTO_UNIV_COL = {{ $programa->costo_pago_colegiatura_universidad ?? 0 }};
            const COSTO_UNIV_CERT = {{ $programa->costo_pago_certificacion_universidad ?? 0 }};

            let folioRegConceptos = [];

            function actualizarRegTotales() {
                const cbs = document.querySelectorAll('.folio-reg-cb:checked');
                const count = cbs.length;
                document.getElementById('folio-reg-selected-count').textContent = count + ' seleccionado' + (count !== 1 ? 's' : '');
                const incluir = {
                    Inscripcion: folioRegConceptos.includes('Inscripcion'),
                    Matricula: folioRegConceptos.includes('Matricula'),
                    Colegiatura: folioRegConceptos.includes('Colegiatura'),
                    Certificacion: folioRegConceptos.includes('Certificacion'),
                };
                const tInsc = incluir.Inscripcion ? (COSTO_UNIV_INSC * count) : 0;
                const tMat = incluir.Matricula ? (COSTO_UNIV_MAT * count) : 0;
                const tCol = incluir.Colegiatura ? (COSTO_UNIV_COL * count) : 0;
                const tCert = incluir.Certificacion ? (COSTO_UNIV_CERT * count) : 0;
                document.getElementById('folio-reg-total-insc').textContent = 'Bs ' + tInsc.toFixed(2);
                document.getElementById('folio-reg-total-mat').textContent = 'Bs ' + tMat.toFixed(2);
                document.getElementById('folio-reg-total-col').textContent = 'Bs ' + tCol.toFixed(2);
                document.getElementById('folio-reg-total-cert').textContent = 'Bs ' + tCert.toFixed(2);
                document.getElementById('folio-reg-total-general').textContent = (tInsc + tMat + tCol + tCert).toFixed(2);

                const conflictAlert = document.getElementById('folio-reg-conflict-alert');
                const conflictText = document.getElementById('folio-reg-conflict-text');
                let conflictos = [];
                cbs.forEach(function (cb) {
                    const row = cb.closest('.folio-reg-row');
                    const cell = row ? row.querySelector('.folio-conflictos-cell') : null;
                    if (cell) {
                        const badges = cell.querySelectorAll('.badge');
                        badges.forEach(function (b) { conflictos.push(b.textContent.trim()); });
                    }
                });
                if (conflictos.length > 0) {
                    conflictAlert.style.display = 'block';
                    conflictText.textContent = conflictos.length + ' participante(s) ya tienen conceptos en otros folios. Revise la columna Conflictos.';
                } else {
                    conflictAlert.style.display = 'none';
                }
            }

            // Abrir modal al hacer clic en btn-registrar-folio
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-registrar-folio');
                if (!btn) return;
                const folioId = btn.dataset.folioId;
                const folioName = btn.dataset.folioName;
                folioRegConceptos = JSON.parse(btn.dataset.conceptos || '[]');

                document.getElementById('folio-reg-id').value = folioId;
                document.getElementById('mrfFolioName').textContent = folioName;
                document.getElementById('mrfSubtitle').textContent = 'Agregando participantes a: ' + folioName;

                // Marcar conceptos del folio
                document.querySelectorAll('.folio-reg-concepto').forEach(function (cb) {
                    cb.checked = folioRegConceptos.includes(cb.value);
                });

                // Reset selección
                document.querySelectorAll('.folio-reg-cb').forEach(function (cb) { cb.checked = false; });
                document.getElementById('folio-reg-select-all').checked = false;
                document.getElementById('folio-reg-filtro').value = '';
                document.querySelectorAll('.folio-reg-row').forEach(function (r) { r.style.display = ''; });
                document.getElementById('folio-reg-conflict-alert').style.display = 'none';
                actualizarRegTotales();
                new bootstrap.Modal(document.getElementById('modalRegistrarFolio')).show();
            });

            // Select all
            document.getElementById('folio-reg-select-all')?.addEventListener('change', function () {
                document.querySelectorAll('.folio-reg-cb').forEach(function (cb) {
                    const row = cb.closest('.folio-reg-row');
                    if (row.style.display !== 'none') cb.checked = this.checked;
                }, this);
                actualizarRegTotales();
            });

            document.querySelectorAll('.folio-reg-cb').forEach(function (cb) {
                cb.addEventListener('change', actualizarRegTotales);
            });

            document.getElementById('folio-reg-filtro')?.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.folio-reg-row').forEach(function (r) {
                    r.style.display = q === '' || r.dataset.search.includes(q) ? '' : 'none';
                });
            });

            // Submit
            document.getElementById('form-registrar-folio')?.addEventListener('submit', function (e) {
                e.preventDefault();
                const folioId = document.getElementById('folio-reg-id').value;
                const seleccionados = Array.from(document.querySelectorAll('.folio-reg-cb:checked')).map(function (cb) {
                    return parseInt(cb.value);
                });

                if (!folioId) { showToast('Seleccione un folio', 'warning'); return; }
                if (seleccionados.length === 0) { showToast('Seleccione al menos un participante', 'warning'); return; }

                const btn = document.getElementById('btn-registrar-folio');
                btn.disabled = true;
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Registrando...';

                const fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                folioRegConceptos.forEach(function (c, i) { fd.append('conceptos[' + i + ']', c); });
                seleccionados.forEach(function (pid, i) { fd.append('participantes[' + i + ']', pid); });

                fetch('{{ url("admin/programas") }}/' + '{{ $programa->id }}' + '/folios/' + folioId + '/registrar-participantes', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalRegistrarFolio')).hide();
                        showToast(data.message, 'success');
                        setTimeout(function () { location.reload(); }, 1200);
                    } else {
                        showToast(data.message || 'Error al registrar participantes', 'danger');
                    }
                })
                .catch(function () { showToast('Error de conexión', 'danger'); })
                .finally(function () { btn.disabled = false; btn.innerHTML = orig; });
            });


        });

        // ============================================================
        // Cambiar Plan de Pagos (modal con descuentos)
        // ============================================================
        (function () {
            var mcpModalInstance = null;

            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-cambiar-plan');
                if (!btn) return;

                window._mcpPartId = btn.dataset.participante;
                window._mcpProgId = btn.dataset.programa;

                document.getElementById('mcpSubtitle').textContent = btn.dataset.nombre;
                document.getElementById('mcp-desc-mat').value = btn.dataset.descMat;
                document.getElementById('mcp-desc-mat').max = btn.dataset.matricula;
                document.getElementById('mcp-desc-col').value = btn.dataset.descCol;
                document.getElementById('mcp-desc-col').max = btn.dataset.colegiatura;
                document.getElementById('mcp-contado').checked = (btn.dataset.plan === 'contado');
                document.getElementById('mcp-traspaso').checked = (btn.dataset.esTraspaso === '1');

                if (!mcpModalInstance) {
                    var el = document.getElementById('modalCambiarPlan');
                    if (!el) return;
                    mcpModalInstance = new bootstrap.Modal(el);
                }
                mcpModalInstance.show();
            });
        })();

        function cambiarPlanGuardar(btn) {
            var descMat = document.getElementById('mcp-desc-mat').value;
            var descCol = document.getElementById('mcp-desc-col').value;
            var contado = document.getElementById('mcp-contado').checked ? 1 : 0;
            var plan = contado ? 'contado' : 'regular';

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            var fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('plan', plan);
            fd.append('descuento_matricula', descMat || 0);
            fd.append('descuento_colegiatura', descCol || 0);
            fd.append('es_traspaso', document.getElementById('mcp-traspaso').checked ? 1 : 0);

            var url = '{{ url("admin/contable/programa") }}/' + window._mcpProgId + '/participante/' + window._mcpPartId + '/cambiar-plan';

            fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd,
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (r.ok && r.data.success) {
                    var modalEl = document.getElementById('modalCambiarPlan');
                    if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
                    setTimeout(function () {
                        showToast(r.data.message, 'success');
                        setTimeout(function () { location.reload(); }, 3000);
                    }, 300);
                } else {
                    showToast(r.data.message || 'Error al cambiar plan', 'danger');
                }
            })
            .catch(function () { showToast('Error de conexión', 'danger'); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-line align-bottom"></i> Guardar Cambios';
            });
        }

        // --- Marketing Inscritos Reales ---
        (function () {
            var badge = document.getElementById('badge-marketing-reales');
            var table = document.getElementById('tabla-marketing-reales');
            if (!badge || !table) return;
            var realCount = table.querySelectorAll('tbody tr td:last-child .badge.bg-soft-success').length;
            badge.textContent = realCount;

            var input = document.getElementById('buscador-marketing-reales');
            var counter = document.getElementById('contador-marketing-reales');
            var resultsSpan = document.getElementById('resultados-marketing-reales');
            if (!input) return;

            function filterMarketing() {
                var q = input.value.toLowerCase().trim();
                var rows = table.querySelectorAll('tbody tr');
                var visible = 0;
                rows.forEach(function (row) {
                    if (row.querySelector('td[colspan]')) return;
                    var text = row.textContent.toLowerCase();
                    var match = !q || text.indexOf(q) !== -1;
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                if (counter) counter.textContent = visible;
                if (resultsSpan) {
                    var total = rows.length - (table.querySelector('tbody tr td[colspan]') ? 1 : 0);
                    resultsSpan.innerHTML = 'Mostrando <strong>' + visible + '</strong> de <strong>' + total + '</strong> resultados';
                }
            }
            input.addEventListener('input', filterMarketing);
            filterMarketing();
        })();
        </script>
@endpush
