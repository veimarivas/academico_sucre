{{-- Estilos de las vistas de envío por WhatsApp (programa, prospectos). --}}
<style>
    :root { --wa-green: #25D366; --wa-green-dark: #128C7E; }

    /* ── Hero ─────────────────────────────────────────────── */
    .wa-hero {
        position: relative;
        background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
        border-radius: 18px;
        padding: 1.65rem 1.9rem;
        color: #fff;
        overflow: hidden;
        box-shadow: 0 18px 40px -22px rgba(64, 81, 137, .55);
        margin-bottom: 1.25rem;
    }
    .wa-hero::before { content:""; position:absolute; top:-60px; right:-50px; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.20) 0%, transparent 68%); border-radius:50%; }
    .wa-hero::after  { content:""; position:absolute; bottom:-80px; left:30%; width:300px; height:300px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
    .wa-hero > * { position: relative; z-index: 1; }
    .wa-hero .hero-icon {
        width: 58px; height: 58px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 16px; font-size: 1.8rem; flex-shrink: 0;
        backdrop-filter: blur(5px);
    }
    .wa-hero h4 { color:#fff; font-weight: 800; margin: 0; letter-spacing: -.3px; }
    .wa-hero .hero-sub { color: rgba(255,255,255,.9); font-size: .86rem; margin: 3px 0 0; }
    .wa-hero .wa-chips { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.9rem; }
    .wa-hero .wa-chip {
        display:inline-flex; align-items:center; gap:.35rem;
        background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.24);
        border-radius: 999px; padding: .24rem .72rem; font-size: .72rem; font-weight: 700; color:#fff;
    }
    .wa-hero .btn-back {
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.32); color:#fff;
        border-radius: 11px; padding: .52rem 1rem; font-weight: 700; font-size: .82rem;
        display:inline-flex; align-items:center; gap:.4rem; transition: all .15s ease;
    }
    .wa-hero .btn-back:hover { background: rgba(255,255,255,.28); color:#fff; transform: translateY(-1px); }

    /* ── Cards ────────────────────────────────────────────── */
    .wa-card {
        background: #fff; border: 1px solid #eef0f4; border-radius: 16px;
        box-shadow: 0 14px 34px -26px rgba(64, 81, 137, .45);
        overflow: hidden; height: 100%; display: flex; flex-direction: column;
    }
    [data-layout-mode="dark"] .wa-card { background: #1d2125; border-color: #2a2f34; }
    .wa-card-head {
        display: flex; align-items: center; gap: .8rem;
        padding: 1rem 1.25rem; border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(135deg, #fbfcfe 0%, #f1f4fa 100%);
    }
    [data-layout-mode="dark"] .wa-card-head { background: #1a1d20; border-bottom-color: #2a2f34; }
    .wa-card-ic {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;
        color: #fff; background: linear-gradient(135deg, var(--wa-green-dark), var(--wa-green));
        box-shadow: 0 6px 14px -8px rgba(18,140,126,.85);
    }
    .wa-card-head h6 { margin: 0; font-weight: 800; color: #1c2434; font-size: .96rem; }
    [data-layout-mode="dark"] .wa-card-head h6 { color: #e9ecef; }
    .wa-card-head p { margin: 0; font-size: .75rem; color: #6b7280; }
    .wa-card-body { padding: 1.2rem 1.3rem; flex: 1; display: flex; flex-direction: column; }

    /* status pills */
    .wa-status { display:inline-flex; align-items:center; gap:.35rem; flex-shrink:0; border-radius: 999px; padding: .24rem .66rem; font-size: .68rem; font-weight: 800; font-variant-numeric: tabular-nums; }
    .wa-status.ok { background: #e8f8f0; color: #0f766e; border: 1px solid #b7ebd3; }
    .wa-status.off { background: #fff4ed; color: #b54708; border: 1px solid #fde3c8; }
    .wa-status.info { background: #eef1f8; color: #405189; border: 1px solid #c3cfe2; }
    [data-layout-mode="dark"] .wa-status.info { background:#232a3d; color:#a9b7e0; border-color:#313b57; }

    /* controls */
    .wa-label { display:block; font-size:.68rem; font-weight: 800; text-transform: uppercase; letter-spacing:.45px; color:#6c757d; margin-bottom:.32rem; }
    [data-layout-mode="dark"] .wa-label { color:#98a2b3; }
    .wa-input, .wa-select, .wa-textarea {
        width:100%; border:1px solid #e3e6ea; border-radius: 10px;
        padding: .55rem .82rem; font-size: .86rem; color:#2b3440; background:#fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    [data-layout-mode="dark"] .wa-input, [data-layout-mode="dark"] .wa-select, [data-layout-mode="dark"] .wa-textarea { background:#1d2125; border-color:#2a2f34; color:#ced4da; }
    .wa-input:focus, .wa-select:focus, .wa-textarea:focus { outline:0; border-color:#5e72b8; box-shadow: 0 0 0 .18rem rgba(94,114,184,.18); }
    .wa-textarea { resize: vertical; }
    .wa-help { font-size:.72rem; color:#6b7280; margin-top:.32rem; }
    .wa-help code { background:#eef1f8; color:#405189; padding:.05rem .38rem; border-radius:5px; font-size:.72rem; }

    .wa-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:.42rem;
        border-radius:10px; padding:.56rem 1.05rem; font-weight:700; font-size:.82rem;
        border:1px solid transparent; cursor:pointer; transition: all .15s ease; white-space:nowrap;
    }
    .wa-btn-primary { background: linear-gradient(135deg, #405189 0%, #5e72b8 100%); color:#fff; box-shadow: 0 10px 20px -12px rgba(64,81,137,.85); }
    .wa-btn-primary:hover { color:#fff; transform: translateY(-1px); box-shadow: 0 14px 26px -12px rgba(64,81,137,.95); }
    .wa-btn-soft { background:#eef1f8; color:#405189; border-color:#c3cfe2; }
    .wa-btn-soft:hover { background:#e1e7f3; color:#405189; }
    .wa-btn-ghost { background:#fff; color:#495057; border-color:#e3e6ea; }
    [data-layout-mode="dark"] .wa-btn-ghost { background:#232829; color:#ced4da; border-color:#2a2f34; }
    .wa-btn-sm { padding:.42rem .8rem; font-size:.78rem; }
    .wa-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

    .wa-divider { height:1px; background:#f1f3f5; margin:1.15rem 0; }
    [data-layout-mode="dark"] .wa-divider { background:#2a2f34; }

    /* grupo registrado (solo lectura) */
    .wa-group-current {
        display:flex; align-items:center; justify-content:space-between; gap:.75rem;
        background: linear-gradient(135deg, #f6f9fe, #eef3fb); border:1px solid #e1e9f6;
        border-radius:12px; padding:.75rem .85rem;
    }
    [data-layout-mode="dark"] .wa-group-current { background:#1a2233; border-color:#2b3853; }
    .wa-group-info { display:flex; align-items:center; gap:.6rem; min-width:0; }
    .wa-group-ic { width:38px; height:38px; border-radius:10px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; color:#0f766e; background:#e8f8f0; font-size:1.15rem; }
    .wa-group-current .nm { font-weight:800; font-size:.9rem; color:#1c2434; line-height:1.2; }
    [data-layout-mode="dark"] .wa-group-current .nm { color:#e9ecef; }
    .wa-group-current .id { font-size:.72rem; color:#6b7280; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; word-break:break-all; }

    .wa-note { display:flex; align-items:center; gap:.5rem; background:#fff8ec; border:1px solid #fde3c8; color:#b54708; border-radius:10px; padding:.65rem .85rem; font-size:.82rem; font-weight:600; }

    /* variables */
    .wa-var {
        display:inline-flex; align-items:center; gap:.28rem;
        background:#eef1f8; color:#405189; border:1px dashed #b9c5e0;
        border-radius:999px; padding:.2rem .62rem; font-size:.72rem; font-weight:700; cursor:pointer;
        transition: all .12s ease;
    }
    .wa-var:hover { background:#e1e7f3; transform: translateY(-1px); }

    /* preview */
    .wa-preview { background: linear-gradient(180deg, #e5ddd5, #dcd3c9); border-radius: 14px; padding: .85rem; position: sticky; top: 1rem; }
    [data-layout-mode="dark"] .wa-preview { background:#15191c; }
    .wa-preview .meta { font-size:.68rem; color:#5d6b73; margin-bottom:.45rem; font-weight:800; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:.3rem; }
    [data-layout-mode="dark"] .wa-preview .meta { color:#8a97a0; }
    .wa-preview-bubble {
        background:#dcf8c6; border-radius: 10px 10px 10px 3px; padding:.7rem .8rem;
        font-size:.82rem; color:#1f2a24; white-space:pre-wrap; word-break:break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,.1); min-height: 3rem;
    }
    [data-layout-mode="dark"] .wa-preview-bubble { background:#005c4b; color:#e9edef; }
    .wa-preview-file { display:flex; align-items:center; gap:.4rem; margin-top:.5rem; font-size:.72rem; color:#4b5a63; background:rgba(255,255,255,.55); border-radius:8px; padding:.35rem .55rem; }
    [data-layout-mode="dark"] .wa-preview-file { color:#cfd8dc; background:rgba(255,255,255,.06); }

    /* participants */
    .wa-search { position:relative; }
    .wa-search i { position:absolute; top:50%; left:.78rem; transform:translateY(-50%); color:#adb5bd; }
    .wa-search input { padding-left:2.35rem; }
    .wa-list { border:1px solid #eef0f4; border-radius: 12px; max-height: 320px; overflow-y:auto; }
    [data-layout-mode="dark"] .wa-list { border-color:#2a2f34; }
    .wa-part { display:flex; align-items:center; gap:.75rem; padding:.6rem .8rem; border-bottom:1px solid #f4f6f8; cursor:pointer; transition: background .12s ease; }
    [data-layout-mode="dark"] .wa-part { border-bottom-color:#232829; }
    .wa-part:last-child { border-bottom:0; }
    .wa-part:hover { background:#f8fafd; }
    [data-layout-mode="dark"] .wa-part:hover { background:#1a1d20; }
    .wa-part.selected { background:#f0f4fc; }
    [data-layout-mode="dark"] .wa-part.selected { background:#1c2338; }
    .wa-avatar {
        width:36px; height:36px; border-radius:50%; flex-shrink:0; color:#fff;
        display:inline-flex; align-items:center; justify-content:center; font-size:.74rem; font-weight:800;
        background: linear-gradient(135deg, var(--wa-green-dark), var(--wa-green));
    }
    .wa-part .nm { font-size:.83rem; font-weight:700; color:#2b3440; line-height:1.15; }
    [data-layout-mode="dark"] .wa-part .nm { color:#dee2e6; }
    .wa-part .ph { font-size:.72rem; color:#6b7280; font-variant-numeric: tabular-nums; }
    .wa-part .no-tel { color:#e8833a; font-weight:700; }
    /* Dados de baja: en rojo para no enviarles mensajes personalizados */
    .wa-part.wa-part-baja { background:#fff1f1; border-left:3px solid #dc3545; }
    .wa-part.wa-part-baja:hover { background:#ffe6e6; }
    .wa-part.wa-part-baja.selected { background:#ffd9d9; }
    .wa-part.wa-part-baja .nm { color:#b42318; }
    .wa-part.wa-part-baja .wa-avatar { background:#dc3545; color:#fff; }
    [data-layout-mode="dark"] .wa-part.wa-part-baja { background:#3a1d20; }
    [data-layout-mode="dark"] .wa-part.wa-part-baja .nm { color:#f19a9a; }
    .wa-baja-tag { display:inline-flex; align-items:center; gap:.2rem; margin:.1rem 0; padding:0 .4rem; border-radius:999px;
        font-size:.66rem; font-weight:700; color:#fff; background:#dc3545; }
    .wa-bajas-hint { margin-bottom:.5rem; padding:.4rem .7rem; border-radius:8px; font-size:.78rem; color:#b42318; background:#fff1f1; }
    [data-layout-mode="dark"] .wa-bajas-hint { background:#3a1d20; color:#f19a9a; }

    .form-check-input:checked { background-color:#405189; border-color:#405189; }
</style>
