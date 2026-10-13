@extends('admin.layouts.master')

@section('title', 'Cobranza y Retención - ' . config('app.name'))

@push('css')
<style>
/* ── Hero ── */
.ret-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px; padding: 1.6rem 1.8rem; color: #fff; overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75); margin-bottom: 1.25rem;
}
.ret-hero::before { content:""; position:absolute; top:-60px; right:-40px; width:240px; height:240px; background: radial-gradient(circle, rgba(255,255,255,.16) 0%, transparent 70%); border-radius:50%; }
.ret-hero::after { content:""; position:absolute; bottom:-70px; left:28%; width:280px; height:280px; background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%); border-radius:50%; }
.ret-hero > * { position: relative; z-index: 1; }
.ret-hero .hero-ic { width:60px; height:60px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.28); border-radius:16px; font-size:1.7rem; }
.ret-hero h4 { color:#fff; font-weight:800; margin:0; letter-spacing:-.01em; }
.ret-hero .hero-sub { color: rgba(255,255,255,.82); font-size:.85rem; margin: 2px 0 0; }
.ret-chip { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.28); color:#fff; font-size:.72rem; font-weight:700; padding:.3em .8em; border-radius:999px; }
.ret-hero select { border-radius:10px; }

/* ── KPIs ── */
.ret-kpi { position:relative; background:var(--ui-surface,#fff); border:1px solid var(--ui-line,#eef0f4); border-radius:16px; padding:1.05rem 1.15rem; height:100%; overflow:hidden; transition:transform .18s ease, box-shadow .18s ease; }
.ret-kpi::before { content:""; position:absolute; top:0; left:0; bottom:0; width:4px; background: var(--accent, #405189); }
.ret-kpi:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -20px rgba(64,81,137,.55); }
.ret-kpi .lbl { font-size:.65rem; text-transform:uppercase; letter-spacing:.45px; color:var(--ui-muted,#878a99); font-weight:800; }
.ret-kpi .val { font-size:1.55rem; font-weight:800; color:var(--ui-ink,#1b2537); line-height:1.05; letter-spacing:-.02em; font-variant-numeric: tabular-nums; }
.ret-kpi .sub { font-size:.72rem; color:var(--ui-muted,#98a2b3); margin-top:.15rem; }
.ret-kpi .ic { width:44px; height:44px; border-radius:13px; display:inline-flex; align-items:center; justify-content:center; font-size:1.25rem; background: var(--soft, #eef1f8); color: var(--accent, #405189); flex-shrink:0; }

/* ── Cards ── */
.ret-card { border:1px solid var(--ui-line,#eef0f4); border-radius:16px; background:var(--ui-surface,#fff); box-shadow: var(--ui-shadow, 0 8px 24px -18px rgba(64,81,137,.4)); overflow:hidden; }
.ret-card-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.9rem 1.15rem; border-bottom:1px solid var(--ui-line,#f1f3f5); }
.ret-card-head h6 { margin:0; font-size:.9rem; font-weight:800; color:var(--ui-ink,#1b2537); display:flex; align-items:center; gap:.5rem; }
.ret-card-head h6 i { color:var(--ui-accent,#405189); font-size:1.05rem; }

/* ── Ring chart ── */
.ret-ring { --pct:0; width:110px; height:110px; border-radius:50%; flex-shrink:0;
    background: conic-gradient(#1aa971 calc(var(--pct) * 1%), #e9eef6 0);
    display:flex; align-items:center; justify-content:center; position:relative; }
.ret-ring::after { content:""; position:absolute; inset:13px; border-radius:50%; background:var(--ui-surface,#fff); }
.ret-ring span { position:relative; z-index:1; font-weight:800; font-size:1.2rem; color:#0a7d5a; }

/* ── Bars & legend ── */
.ret-bar { height:8px; border-radius:999px; background:var(--ui-line-strong,#eef1f8); overflow:hidden; }
.ret-bar > span { display:block; height:100%; border-radius:999px; }
.ret-legend { display:flex; flex-wrap:wrap; gap:.4rem 1.1rem; font-size:.76rem; color:var(--ui-muted,#667085); }
.ret-legend .dot { width:9px; height:9px; border-radius:3px; display:inline-block; margin-right:.35rem; vertical-align:middle; }

/* ── Badges ── */
.ret-badge { display:inline-flex; align-items:center; gap:4px; font-size:.68rem; font-weight:800; padding:.24em .62em; border-radius:999px; white-space:nowrap; }
.ret-badge.mora { background:var(--ui-danger-soft,#fee4e2); color:var(--ui-danger,#b42318); }
.ret-badge.ok { background:var(--ui-success-soft,#d1fae5); color:var(--ui-success,#067647); }
.ret-badge.neutral { background:var(--ui-surface-2,#eef2f6); color:var(--ui-muted,#667085); }
.ret-badge.warn { background:var(--ui-warning-soft,#fbf0da); color:var(--ui-warning,#b54708); }
.ret-badge.info { background:var(--ui-info-soft,#e7f1fb); color:var(--ui-info,#1f6fbf); }

/* ── Table base ── */
.ret-table thead th { font-size:.65rem; text-transform:uppercase; letter-spacing:.4px; color:var(--ui-muted,#98a2b3); font-weight:800; border-bottom:1px solid var(--ui-line,#eef0f4); white-space:nowrap; }
.ret-table tbody tr { transition: background-color .15s ease; }
.ret-table tbody tr:hover { background:var(--ui-surface-2,#f8fafc); }

/* ── Por programa ── */
.ret-prog-table { table-layout: fixed; width: 100%; }
.ret-prog-table th, .ret-prog-table td { padding: .7rem .55rem; vertical-align: middle; }
.ret-prog-main { font-size: 1.15rem; font-weight: 800; color: var(--ui-ink,#1b2537); line-height: 1; font-variant-numeric: tabular-nums; }
.ret-prog-avatar { width:40px; height:40px; border-radius:12px; color:#fff; font-weight:800; font-size:.78rem; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; letter-spacing:.02em; }
.ret-money { display: grid; grid-template-columns: auto 1fr; gap: 2px 8px; font-size: .72rem; align-items: center; }
.ret-money .lbl { color: var(--ui-muted,#98a2b3); font-weight: 800; text-transform: uppercase; letter-spacing: .3px; font-size: .6rem; }
.ret-money .val { text-align: right; font-weight: 700; color: var(--ui-ink,#1b2537); font-variant-numeric: tabular-nums; }
@media (max-width: 767.98px) {
    .ret-prog-table thead { display: none; }
    .ret-prog-table, .ret-prog-table tbody, .ret-prog-table tr, .ret-prog-table td { display: block; width: 100% !important; }
    .ret-prog-table tr { border-bottom: 1px solid var(--ui-line,#eef0f4); padding: .5rem .25rem; }
    .ret-prog-table td { border: 0; padding: .35rem .6rem; text-align: left !important; }
}

/* ══════════════════════════════════════════════════
   BAJAS RECIENTES — diseño mejorado
   ══════════════════════════════════════════════════ */
.ret-bajas-wrap {
    overflow-x: hidden;
    max-height: 460px;
    overflow-y: auto;
}
.ret-bajas-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.ret-bajas-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: var(--ui-surface,#fff);
    font-size: .64rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .05em;
    color: var(--ui-muted,#98a2b3);
    padding: .65rem .7rem;
    border-bottom: 2px solid var(--ui-line,#eef0f4);
}
.ret-bajas-table tbody td {
    padding: .6rem .7rem;
    border-bottom: 1px solid var(--ui-line,#eef0f4);
    font-size: .8rem;
    color: var(--ui-ink-2,#475467);
    vertical-align: middle;
}
.ret-bajas-table tbody tr { transition: background .15s ease; }
.ret-bajas-table tbody tr:hover { background: var(--ui-surface-2,#f8fafc); }
.ret-bajas-table tbody tr:last-child td { border-bottom: 0; }

.ret-baja-name {
    font-weight: 700; color: var(--ui-ink,#1b2537);
    font-size: .82rem; line-height: 1.25;
}
.ret-baja-name small {
    display: block; font-weight: 500; color: var(--ui-muted,#98a2b3);
    font-size: .7rem; margin-top: 1px;
}
.ret-baja-programa {
    font-size: .74rem; font-weight: 600;
    color: var(--ui-accent,#405189);
    background: var(--ui-accent-soft,#eef1f8);
    padding: .2em .55em; border-radius: 6px;
    display: inline-block; max-width: 160px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.ret-baja-flags {
    display: flex; flex-wrap: wrap; gap: 3px;
}
.ret-baja-flags .ret-badge { font-size: .6rem; padding: .18em .5em; }
.ret-baja-motivo {
    font-size: .74rem; color: var(--ui-muted,#667085);
    max-width: 220px;
    overflow: hidden; text-overflow: ellipsis;
    white-space: nowrap;
}
.ret-baja-fecha {
    font-size: .72rem; font-weight: 600;
    color: var(--ui-muted,#98a2b3);
    white-space: nowrap;
}
@media (max-width: 991.98px) {
    .ret-baja-motivo { max-width: 160px; }
}
@media (max-width: 767.98px) {
    .ret-bajas-table { display: block; }
    .ret-bajas-table thead { display: none; }
    .ret-bajas-table tbody { display: block; width: 100%; }
    .ret-bajas-table tr {
        display: grid;
        grid-template-columns: 1fr auto;
        grid-template-rows: auto auto;
        gap: 2px .75rem;
        padding: .65rem .7rem;
        border-bottom: 1px solid var(--ui-line,#eef0f4);
    }
    .ret-bajas-table td {
        border: 0; padding: 0;
        font-size: .78rem;
    }
    .ret-bajas-table td:nth-child(1) { grid-column: 1 / -1; }
    .ret-bajas-table td:nth-child(2) { grid-column: 1; }
    .ret-bajas-table td:nth-child(3) { grid-column: 1 / -1; }
    .ret-bajas-table td:nth-child(4) { grid-column: 1 / -1; }
    .ret-baja-motivo { max-width: none; white-space: normal; }
}

/* ══════════════════════════════════════════════════
   AVISOS POR TIPO — diseño mejorado
   ══════════════════════════════════════════════════ */
.ret-avisos-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: .5rem;
    overflow-x: hidden;
}
.ret-aviso-item {
    display: flex; align-items: center; gap: .75rem;
    padding: .7rem .85rem;
    background: var(--ui-surface-2,#f7f9fc);
    border: 1px solid var(--ui-line,#eef0f4);
    border-radius: 12px;
    transition: border-color .18s ease, box-shadow .18s ease;
    overflow: hidden;
}
.ret-aviso-item:hover {
    border-color: var(--ui-accent,#405189);
    box-shadow: 0 2px 8px -4px rgba(64,81,137,.25);
}
.ret-aviso-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.ret-aviso-info { flex: 1; min-width: 0; }
.ret-aviso-label {
    font-size: .78rem; font-weight: 700;
    color: var(--ui-ink,#1b2537);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.ret-aviso-sub { font-size: .66rem; color: var(--ui-muted,#98a2b3); margin-top: 1px; }
.ret-aviso-count {
    font-size: 1.25rem; font-weight: 800;
    color: var(--ui-ink,#1b2537);
    font-variant-numeric: tabular-nums;
    flex-shrink: 0; min-width: 36px; text-align: right;
}

/* Colores por tipo de mensaje */
.ret-aviso-icon.wa-gestion    { background:#e6f7ee; color:#128C7E; }
.ret-aviso-icon.wa-recordatorio { background:#e7f1fb; color:#1f6fbf; }
.ret-aviso-icon.wa-cobranza   { background:#fbf0da; color:#b7791f; }
.ret-aviso-icon.wa-general    { background:#eef1f8; color:#405189; }
.ret-aviso-icon.wa-default    { background:var(--ui-surface-2,#eef2f6); color:var(--ui-muted,#667085); }

/* ── Empty state ── */
.ret-empty { text-align:center; padding:2.5rem 1rem; color:var(--ui-muted,#98a2b3); }
.ret-empty i { font-size:1.8rem; display:block; margin-bottom:.5rem; opacity:.6; }

/* ── Responsive general ── */
@media (max-width: 575.98px) {
    .ret-hero { padding: 1.2rem 1rem; }
    .ret-hero .hero-ic { width: 46px; height: 46px; font-size: 1.3rem; }
}
</style>
@endpush

@section('content')
@php
    $deuda = (float) ($totales['deuda'] ?? 0);
    $pagado = (float) ($totales['pagado'] ?? 0);
    $saldo = (float) ($totales['saldo'] ?? 0);
    $pctCobrado = $deuda > 0 ? (int) min(100, round($pagado / $deuda * 100)) : 0;
    $pctSaldo = max(0, 100 - $pctCobrado);
    $paleta = ['#163b6b', '#1f6fbf', '#0d8574', '#cba135', '#b7791f', '#2a5d97', '#b3243a', '#4c7bb0'];
@endphp

{{-- Hero --}}
<div class="ret-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-ic"><i class="ri-user-heart-line"></i></div>
            <div>
                <h4>Cobranza y Retención</h4>
                <p class="hero-sub">Mora, efectividad de avisos y bajas · gestión {{ $gestion }}</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="ret-chip"><i class="ri-group-line"></i> {{ number_format($totales['inscritos']) }} inscritos</span>
                    <span class="ret-chip"><i class="ri-error-warning-line"></i> {{ number_format($totales['con_mora']) }} con mora</span>
                    <span class="ret-chip"><i class="ri-whatsapp-line"></i> {{ number_format($totales['mensajes']) }} avisos</span>
                </div>
            </div>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="mb-0 small text-white-50">Gestión</label>
            <select name="gestion" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:110px;">
                @foreach($gestiones as $g)
                    <option value="{{ $g }}" @selected((int)$g === $gestion)>{{ $g }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-3">
    @php
        $kpis = [
            ['Inscritos', number_format($totales['inscritos']), 'en la gestión', '#405189', '#eef1f8', 'ri-group-line'],
            ['Con mora', number_format($totales['con_mora']), 'requieren cobro', '#f06548', '#fee4e2', 'ri-error-warning-line'],
            ['Deuda', 'Bs '.number_format($deuda, 0), 'total en cuotas', '#5e72b8', '#e9edfb', 'ri-file-list-3-line'],
            ['Cobrado', 'Bs '.number_format($pagado, 0), $totales['pct'].'% del total', '#25D366', '#e6f7ee', 'ri-money-dollar-circle-line'],
        ];
    @endphp
    @foreach($kpis as [$lbl, $val, $sub, $accent, $soft, $icon])
    <div class="col-6 col-xl-3">
        <div class="ret-kpi" style="--accent: {{ $accent }}; --soft: {{ $soft }};">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                    <div class="lbl">{{ $lbl }}</div>
                    <div class="val">{{ $val }}</div>
                    <div class="sub">{{ $sub }}</div>
                </div>
                <span class="ic"><i class="{{ $icon }}"></i></span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Resumen de cobranza + retención --}}
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="ret-card h-100">
            <div class="ret-card-head">
                <h6><i class="ri-pie-chart-2-line"></i> Resumen de cobranza</h6>
                <span class="ret-badge ok"><i class="ri-check-double-line"></i> {{ $pctCobrado }}% cobrado</span>
            </div>
            <div class="p-3 d-flex flex-column flex-sm-row align-items-center gap-3">
                <div class="ret-ring" style="--pct: {{ $pctCobrado }};"><span>{{ $pctCobrado }}%</span></div>
                <div class="flex-grow-1 w-100">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between" style="font-size:.78rem;">
                            <span style="color:var(--ui-muted);">Cobrado</span>
                            <strong style="color:var(--ui-success,#25D366);">Bs {{ number_format($pagado, 0) }}</strong>
                        </div>
                        <div class="ret-bar mt-1"><span style="width: {{ $pctCobrado }}%; background:#25D366;"></span></div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between" style="font-size:.78rem;">
                            <span style="color:var(--ui-muted);">Saldo por cobrar</span>
                            <strong style="color:var(--ui-warning,#b54708);">Bs {{ number_format($saldo, 0) }}</strong>
                        </div>
                        <div class="ret-bar mt-1"><span style="width: {{ $pctSaldo }}%; background:#f7b84b;"></span></div>
                    </div>
                    <div class="ret-legend mt-3">
                        <span><span class="dot" style="background:#25D366;"></span>Cobrado</span>
                        <span><span class="dot" style="background:#f7b84b;"></span>Saldo</span>
                        <span><span class="dot" style="background:#e9eef6;"></span>Deuda total Bs {{ number_format($deuda, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="ret-card h-100">
            <div class="ret-card-head"><h6><i class="ri-shield-star-line"></i> Retención</h6></div>
            <div class="d-flex h-100">
                <div class="flex-fill text-center p-3 d-flex flex-column justify-content-center" style="border-right:1px solid var(--ui-line,#f1f3f5);">
                    <div class="text-uppercase" style="font-size:.65rem;font-weight:800;letter-spacing:.45px;color:var(--ui-muted,#98a2b3);">Bajas</div>
                    <div class="ret-mini-num my-1" style="color:var(--ui-warning,#b54708);">{{ number_format($totales['bajas']) }}</div>
                    <div style="font-size:.72rem;color:var(--ui-muted,#98a2b3);">en la gestión</div>
                </div>
                <div class="flex-fill text-center p-3 d-flex flex-column justify-content-center">
                    <div class="text-uppercase" style="font-size:.65rem;font-weight:800;letter-spacing:.45px;color:var(--ui-muted,#98a2b3);">Avisos WhatsApp</div>
                    <div class="ret-mini-num my-1" style="color:#128C7E;">{{ number_format($totales['mensajes']) }}</div>
                    <div style="font-size:.72rem;color:var(--ui-muted,#98a2b3);">enviados</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Por programa --}}
<div class="ret-card mb-3">
    <div class="ret-card-head">
        <h6><i class="ri-building-4-line"></i> Por programa</h6>
        <span class="ret-badge neutral">{{ $filas->count() }} programas</span>
    </div>
    <table class="table align-middle mb-0 ret-table ret-prog-table">
        <thead>
            <tr>
                <th style="width:32%;">Programa</th>
                <th class="text-center" style="width:13%;">Alumnos</th>
                <th style="width:26%;">Cobranza (Bs)</th>
                <th style="width:16%;">% cobrado</th>
                <th class="text-center" style="width:13%;">Retención</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $i => $f)
                @php $color = $paleta[$i % count($paleta)]; @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="ret-prog-avatar" style="background: {{ $color }};">{{ strtoupper(mb_substr($f->nombre, 0, 2)) }}</span>
                            <div style="min-width:0;">
                                <div class="fw-semibold text-dark text-truncate" title="{{ $f->nombre }}">{{ $f->nombre }}</div>
                                <div class="text-muted" style="font-size:.7rem;">{{ $f->codigo }} · {{ $f->tipo }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="ret-prog-main">{{ $f->inscritos }}</div>
                        @if($f->con_mora > 0)
                            <span class="ret-badge mora mt-1"><i class="ri-alarm-warning-line"></i> {{ $f->con_mora }} mora</span>
                        @else
                            <div class="text-muted" style="font-size:.66rem;">sin mora</div>
                        @endif
                    </td>
                    <td>
                        <div class="ret-money">
                            <span class="lbl">Deuda</span><span class="val">{{ number_format($f->deuda, 0) }}</span>
                            <span class="lbl">Cobrado</span><span class="val text-success">{{ number_format($f->pagado, 0) }}</span>
                            <span class="lbl">Saldo</span><span class="val" style="{{ $f->saldo > 0 ? 'color:var(--ui-warning,#b54708);' : '' }}">{{ number_format($f->saldo, 0) }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ret-bar flex-grow-1"><span style="width: {{ $f->pct }}%; background: linear-gradient(90deg,#1aa971,#25D366);"></span></div>
                            <span style="font-size:.72rem;font-weight:700;color:var(--ui-muted,#667085);min-width:34px;text-align:right;">{{ $f->pct }}%</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="ret-badge {{ $f->bajas > 0 ? 'mora' : 'neutral' }} d-inline-flex mb-1"><i class="ri-user-unfollow-line"></i> {{ $f->bajas }}</div>
                        <div class="ret-badge neutral d-inline-flex"><i class="ri-whatsapp-line"></i> {{ $f->mensajes }}</div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="ret-empty"><i class="ri-inbox-2-line"></i>Sin programas en la gestión.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ═══ Bajas recientes + Avisos por tipo ═══ --}}
<div class="row g-3 mb-3">
    {{-- Bajas recientes --}}
    <div class="col-lg-8">
        <div class="ret-card h-100">
            <div class="ret-card-head">
                <h6><i class="ri-user-unfollow-line" style="color:var(--ui-danger,#f06548);"></i> Bajas recientes</h6>
                <span class="ret-badge neutral">{{ $bajas->count() }} registro{{ $bajas->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="ret-bajas-wrap">
                <table class="ret-bajas-table">
                    <thead>
                        <tr>
                            <th style="width:24%;">Participante</th>
                            <th style="width:18%;">Programa</th>
                            <th style="width:16%;">Tipo</th>
                            <th style="width:26%;">Motivo</th>
                            <th style="width:16%;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bajas as $b)
                            @php
                                $flags = [];
                                if ($b->baja_operativa) $flags[] = 'Operativa';
                                if ($b->baja_academica) $flags[] = 'Académica';
                                if ($b->baja_contable) $flags[] = 'Contable';
                            @endphp
                            <tr>
                                <td>
                                    <div class="ret-baja-name">
                                        {{ $b->apellidos }}
                                        <small>{{ $b->nombres }}</small>
                                    </div>
                                </td>
                                <td><span class="ret-baja-programa" title="{{ $b->programa }}">{{ $b->programa }}</span></td>
                                <td>
                                    <div class="ret-baja-flags">
                                        @forelse($flags as $flag)
                                            <span class="ret-badge mora">{{ $flag }}</span>
                                        @empty
                                            <span class="ret-badge warn">{{ $b->tipo_baja ?? 'Baja' }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td><span class="ret-baja-motivo" title="{{ $b->motivo }}">{{ \Illuminate\Support\Str::limit($b->motivo, 90) ?: '—' }}</span></td>
                                <td><span class="ret-baja-fecha">{{ $b->fecha_baja ? \Illuminate\Support\Carbon::parse($b->fecha_baja)->format('d/m/Y') : '—' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="ret-empty"><i class="ri-inbox-2-line"></i>Sin bajas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Avisos por tipo --}}
    <div class="col-lg-4">
        <div class="ret-card h-100">
            <div class="ret-card-head">
                <h6><i class="ri-whatsapp-line" style="color:#25D366;"></i> Avisos por tipo</h6>
                <span class="ret-badge neutral">{{ $mensajesTipo->count() }} tipo{{ $mensajesTipo->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="p-3">
                @php
                    $waColorMap = [
                        'gestion' => ['#e6f7ee','#128C7E','ri-calendar-check-line'],
                        'recordatorio' => ['#e7f1fb','#1f6fbf','ri-alarm-line'],
                        'cobranza' => ['#fbf0da','#b7791f','ri-money-dollar-circle-line'],
                        'aviso' => ['#e6f7ee','#128C7E','ri-notification-3-line'],
                        'general' => ['#eef1f8','#405189','ri-megaphone-line'],
                    ];
                    $totalMensajes = $mensajesTipo->sum();
                @endphp
                <div class="ret-avisos-grid">
                    @forelse($mensajesTipo as $tipo => $total)
                        @php
                            $tipoLower = strtolower($tipo);
                            $matchColor = null;
                            foreach ($waColorMap as $key => $colors) {
                                if (str_contains($tipoLower, $key)) { $matchColor = $colors; break; }
                            }
                            $bg = $matchColor[0] ?? '#eef2f6';
                            $fg = $matchColor[1] ?? '#667085';
                            $ic = $matchColor[2] ?? 'ri-message-3-line';
                            $pct = $totalMensajes > 0 ? round($total / $totalMensajes * 100) : 0;
                        @endphp
                        <div class="ret-aviso-item">
                            <div class="ret-aviso-icon" style="background:{{ $bg }}; color:{{ $fg }};">
                                <i class="{{ $ic }}"></i>
                            </div>
                            <div class="ret-aviso-info">
                                <div class="ret-aviso-label">{{ ucfirst(str_replace(['whatsapp_', 'whatsapp'], '', $tipo)) ?: ucfirst($tipo) }}</div>
                                <div class="ret-aviso-sub">{{ $pct }}% del total</div>
                            </div>
                            <div class="ret-aviso-count">{{ number_format($total) }}</div>
                        </div>
                    @empty
                        <div class="ret-empty"><i class="ri-whatsapp-line"></i>Sin avisos registrados en esta gestión.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
