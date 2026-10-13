@extends('admin.layouts.master')

@section('title', 'Programas - Marketing - ' . config('app.name'))

@push('css')
<style>
.mkt-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.mkt-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.mkt-hero > * { position: relative; z-index: 1; }
.mkt-hero .hero-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px); }
.mkt-hero h4 { color: #fff !important; font-weight: 700; letter-spacing: .2px; margin: 0; }
.mkt-hero .hero-sub { color: rgba(255,255,255,.85) !important; font-size: .88rem; margin: 0; }
.mkt-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.mkt-hero .breadcrumb-item, .mkt-hero .breadcrumb-item a { color: rgba(255,255,255,.85) !important; }
.mkt-hero .breadcrumb-item.active { color: #fff !important; font-weight: 600; }
.mkt-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55) !important; }

.stat-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; transition: transform .2s ease, box-shadow .2s ease; overflow: hidden; position: relative; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -14px rgba(64,81,137,.3); }
.stat-card::before { content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--stat-accent, #405189); }
[data-layout-mode="dark"] .stat-card { background: #212529; border-color: #2a2f34; }
.stat-card .stat-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: #878a99; font-weight: 700; margin-bottom: .4rem; }
.stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #212529; line-height: 1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color: #fff; }
.stat-card .stat-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.25rem; }

.filter-card { border: 1px solid #eef0f4; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card { background: #1d2125; border-color: #2a2f34; }
.filter-card .card-header { background: transparent; border-bottom: 1px dashed #eef0f4; padding: 1rem 1.25rem; }
[data-layout-mode="dark"] .filter-card .card-header { border-bottom-color: #2a2f34; }
.filter-card .form-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; color: #878a99; margin-bottom: .35rem; }

.table-card { border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .table-card { border-color: #2a2f34; }
.tabla-prog { width: 100%; font-size: .82rem; }
.tabla-prog th, .tabla-prog td { vertical-align: middle; padding: .65rem .6rem; }
.tabla-prog thead th { background: #f3f6f9 !important; font-size: .66rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #495057; border-bottom: none; white-space: nowrap; }
[data-layout-mode="dark"] .tabla-prog thead th { background: #1a1d20 !important; border-bottom-color: #2a2f34; color: #ced4da; }
.tabla-prog tbody tr { transition: all 0.3s ease; }
.tabla-prog tbody tr td { border-bottom: 1px solid #f1f3f5; }
[data-layout-mode="dark"] .tabla-prog tbody tr td { border-bottom-color: #2a2f34; }
.tabla-prog tbody tr:hover { background-color: rgba(64,81,137,0.03); }
[data-layout-mode="dark"] .tabla-prog tbody tr:hover { background-color: rgba(255,255,255,0.02); }

.prog-nombre a { color: #212529; font-weight: 700; text-decoration: none; border-bottom: 1px dashed transparent; transition: color .15s, border-color .15s; }
.prog-nombre a:hover { color: #405189; border-bottom-color: #405189; }
[data-layout-mode="dark"] .prog-nombre a { color: #fff; }

.campana-badge { display: inline-flex; align-items: center; gap: .35rem; font-size: .72rem; font-weight: 600; padding: .3em .7em; border-radius: 8px; background: #f3f5f9; color: #495057; white-space: nowrap; }
[data-layout-mode="dark"] .campana-badge { background: #212529; color: #ced4da; }

.progress-mini { height: 6px; border-radius: 999px; background: #eef0f4; overflow: hidden; min-width: 90px; display: flex; }
.progress-mini .bar { height: 100%; transition: width .3s ease; }
.progress-mini .bar.bar-start { border-radius: 999px 0 0 999px; }
.progress-mini .bar.bar-end { border-radius: 0 999px 999px 0; }
.progress-mini .bar.bar-single { border-radius: 999px; }

.faltan-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .7rem; font-weight: 700; padding: .25em .7em; border-radius: 999px; white-space: nowrap; }
.faltan-pill.ok { background: rgba(10,179,156,.12); color: #0ab39c; }
.faltan-pill.pendiente { background: rgba(247,184,75,.15); color: #d69511; }
.faltan-pill.sin-meta { background: #f3f5f9; color: #878a99; }

.modulo-fecha { font-size: .72rem; line-height: 1.4; }
.modulo-fecha .m-label { font-weight: 700; color: #405189; font-size: .64rem; text-transform: uppercase; letter-spacing: .3px; }
[data-layout-mode="dark"] .modulo-fecha .m-label { color: #8ab4f8; }

.modulos-list { display: flex; flex-direction: column; gap: .35rem; }
.modulo-item { border-left: 3px solid #405189; padding-left: .6rem; background: #f8f9fc; border-radius: 0 6px 6px 0; padding: .35rem .5rem .35rem .6rem; transition: background .15s; }
.modulo-item:hover { background: #f0f2f8; }
[data-layout-mode="dark"] .modulo-item { background: #1a1d20; border-left-color: #8ab4f8; }
[data-layout-mode="dark"] .modulo-item:hover { background: #22262a; }
.modulo-item .m-name { font-weight: 700; font-size: .7rem; color: #212529; line-height: 1.3; }
[data-layout-mode="dark"] .modulo-item .m-name { color: #fff; }
.modulo-item .m-dates { font-size: .66rem; color: #555; display: flex; align-items: center; gap: .3rem; flex-wrap: wrap; }
[data-layout-mode="dark"] .modulo-item .m-dates { color: #adb5bd; }
.modulo-item .m-dates .m-date-icon { color: #878a99; }
.modulo-item .m-dates .m-date-sep { color: #ced4da; font-weight: 300; }
.modulo-item .m-dates .m-date-range { display: inline-flex; align-items: center; gap: .25rem; }
.modulo-more { font-size: .66rem; color: #405189; font-weight: 600; cursor: pointer; padding: .15rem .5rem; background: #f0f2f8; border-radius: 999px; display: inline-flex; align-items: center; gap: .25rem; border: none; transition: background .15s; }
.modulo-more:hover { background: #e2e6f0; }
[data-layout-mode="dark"] .modulo-more { background: #212529; color: #8ab4f8; }
[data-layout-mode="dark"] .modulo-more:hover { background: #2a2f34; }

.btn-edit-minimo { border: none; background: transparent; color: #878a99; padding: 0 .25rem; font-size: .8rem; }
.btn-edit-minimo:hover { color: #405189; }

.estado-badge { font-size: .68rem; font-weight: 700; padding: .3em .75em; border-radius: 999px; white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="mkt-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-booklet-line"></i></div>
                <div>
                    <h4>Programas — Marketing</h4>
                    <p class="hero-sub">Campañas, metas de inscripción y fechas de los primeros módulos</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item active">Programas</li>
            </ol>
        </div>
    </div>
</div>

{{-- Resumen --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card h-100" style="--stat-accent: #405189;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="min-width-0 me-2">
                    <p class="stat-label text-truncate">Programas</p>
                    <h4 class="stat-value mb-0">{{ $resumen['total_programas'] }}</h4>
                </div>
                <div class="stat-icon bg-soft-primary text-primary"><i class="ri-booklet-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card h-100" style="--stat-accent: #299cdb;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="min-width-0 me-2">
                    <p class="stat-label text-truncate">En Inscripciones</p>
                    <h4 class="stat-value mb-0">{{ $resumen['en_inscripciones'] }}</h4>
                </div>
                <div class="stat-icon bg-soft-info text-info"><i class="ri-megaphone-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-md-0">
        <div class="stat-card h-100" style="--stat-accent: #0ab39c;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="min-width-0 me-2">
                    <p class="stat-label text-truncate">Total Inscritos</p>
                    <h4 class="stat-value mb-0">{{ number_format($resumen['total_inscritos']) }}</h4>
                </div>
                <div class="stat-icon bg-soft-success text-success"><i class="ri-team-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent: #f7b84b;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="min-width-0 me-2">
                    <p class="stat-label text-truncate">Meta Alcanzada</p>
                    <h4 class="stat-value mb-0">{{ $resumen['meta_alcanzada'] }}</h4>
                </div>
                <div class="stat-icon bg-soft-warning text-warning"><i class="ri-flag-2-line"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card mb-0">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-2">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16"><i class="ri-equalizer-line"></i></span>
                    </div>
                    <h5 class="card-title mb-0 fs-14 fw-semibold">Filtros de Búsqueda</h5>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketing.programas') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Gestión</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                                <select name="gestion" class="form-select">
                                    <option value="todas" @if($gestion === 'todas') selected @endif>Todas las gestiones</option>
                                    @foreach($gestiones as $g)
                                        <option value="{{ $g }}" @if((string)$g === (string)$gestion) selected @endif>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-flag-2-line"></i></span>
                                <select name="estado_id" class="form-select">
                                    <option value="">Todos los estados</option>
                                    @foreach($estados as $e)
                                        <option value="{{ $e->id }}" @if((string)$e->id === (string)$estadoId) selected @endif>{{ $e->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Buscar programa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Nombre o código..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <a href="{{ route('admin.marketing.programas') }}" class="btn btn-soft-danger w-50 d-flex align-items-center justify-content-center gap-1">
                                <i class="ri-filter-off-line"></i> Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-1">
                                <i class="ri-search-line"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="row">
    <div class="col-12">
        @php
            $programasPorTipo = $programas->groupBy(fn($p) => $p->tipo_nombre ?: 'Sin Tipo');
        @endphp
        <div class="card table-card">
            <div class="card-header border-bottom-0 pb-0">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-0" role="tablist">
                    @foreach($programasPorTipo as $tipo => $progs)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }} fw-semibold" data-bs-toggle="tab" href="#tab-tipo-{{ Str::slug($tipo) }}" role="tab">
                            {{ $tipo }} <span class="badge bg-soft-secondary text-secondary ms-1">{{ $progs->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body pt-3">
                <div class="tab-content">
                    @foreach($programasPorTipo as $tipo => $progs)
                    <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab-tipo-{{ Str::slug($tipo) }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table tabla-prog align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:30px;">#</th>
                                        <th>Programa</th>
                                        <th>Estado</th>
                                        <th class="text-end">Inversión Publ.<br><small class="text-muted fw-normal" style="font-size: 10px; text-transform: none; letter-spacing: 0;">Esam Sucre Central</small></th>
                                        <th class="text-center">Mínimo</th>
                                        <th>Inscritos / Faltantes</th>
                                        <th class="text-center">Inscritos Reales</th>
                                        <th>Módulos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($progs as $i => $p)
                                @php
                                    $minimo = (int) $p->cantidad_inscritos_minimo;
                                    $inscritos = (int) $p->inscritos;
                                    $faltantes = max(0, $minimo - $inscritos);
                                    $pct = $minimo > 0 ? min(100, round($inscritos / $minimo * 100)) : 0;
                                    $barColor = $pct >= 100 ? '#0ab39c' : ($pct >= 60 ? '#299cdb' : ($pct >= 30 ? '#f7b84b' : '#f06548'));
                                    $mods = $modulosFechas->get($p->id, collect());
                                    $fmtFecha = fn($f) => $f ? \Carbon\Carbon::parse($f)->translatedFormat('d M Y') : null;
                                    $fmtFechaModulo = fn($f) => $f ? \Carbon\Carbon::parse($f)->locale('es')->translatedFormat('l j \d\e F \d\e\l Y') : '—';
                                    $esInscripciones = $p->estado_nombre === 'Inscripciones';
                                    $estadoColores = [
                                        'En desarrollo' => ['bg' => 'rgba(41,156,219,.12)', 'color' => '#299cdb'],
                                        'Concluido' => ['bg' => 'rgba(10,179,156,.12)', 'color' => '#0ab39c'],
                                        'No Ejecutado' => ['bg' => 'rgba(240,101,72,.12)', 'color' => '#f06548'],
                                        'Inscripciones' => ['bg' => 'rgba(64,81,137,.12)', 'color' => '#405189'],
                                    ];
                                    $ec = $estadoColores[$p->estado_nombre] ?? ['bg' => '#f3f5f9', 'color' => '#878a99'];
                                @endphp
                                <tr>
                                    <td class="text-muted fw-semibold">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="prog-nombre">
                                            <a href="{{ route('admin.programas.show', $p->id) }}">{{ $p->nombre }}</a>
                                        </div>
                                        <small class="text-muted">{{ $p->codigo }} · {{ $p->tipo_nombre }} · Gestión {{ $p->gestion }}</small>
                                    </td>
                                    <td>
                                        <span class="estado-badge" style="background: {{ $ec['bg'] }}; color: {{ $ec['color'] }};">{{ $p->estado_nombre ?? 'Sin estado' }}</span>
                                    </td>
                                    <td class="text-end">
                                        @php $invs = $inversionesPorEstado->get($p->id, collect()); @endphp
                                        @if($invs->isNotEmpty())
                                            @foreach($invs as $inv)
                                                <div class="d-flex justify-content-between align-items-center mb-1 pb-1" style="border-bottom: 1px dashed rgba(0,0,0,0.05);">
                                                    <span class="text-muted fs-11 text-uppercase me-3">{{ $inv->estado ?: 'S/E' }}</span>
                                                    <div class="text-end lh-1">
                                                        <div class="fw-bold fs-12 text-dark">Bs {{ number_format($inv->total_monto, 2) }}</div>
                                                        @if($inv->total_aprobado > 0)
                                                            <div class="text-success fw-semibold" style="font-size:0.65rem;">Aprob: Bs {{ number_format($inv->total_aprobado, 2) }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="fw-bold text-primary fs-13 mt-1 pt-1" style="border-top: 1px solid rgba(0,0,0,0.05);">
                                                Total Aprob: Bs {{ number_format($p->inversion_publicidad ?? 0, 2) }}
                                            </div>
                                        @else
                                            <span class="text-muted fs-12 fst-italic">Bs 0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold" id="minimo-val-{{ $p->id }}">{{ $minimo }}</span>
                                        <button type="button" class="btn-edit-minimo btn-editar-minimo" title="Editar mínimo de inscritos"
                                                data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}" data-minimo="{{ $minimo }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </td>
                                    <td style="min-width:180px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="fw-bold fs-12">{{ $inscritos }} <span class="text-muted fw-normal">inscritos</span></span>
                                                    @if($minimo > 0)
                                                        <span class="text-muted fs-11">{{ $pct }}%</span>
                                                    @endif
                                                </div>
                                                <div class="progress-mini" id="prog-bar-{{ $p->id }}">
                                                    <div class="bar bar-single" style="width: {{ $minimo > 0 ? $pct : 0 }}%; background: {{ $barColor }};"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-1 d-flex align-items-center gap-2" style="font-size:.72rem;">
                                            <span class="text-success fw-semibold"><i class="ri-checkbox-circle-line align-middle"></i> <span class="mk-reales-label" data-prog="{{ $p->id }}">0</span> reales</span>
                                            <span class="text-muted">·</span>
                                            <span class="text-secondary"><i class="ri-close-circle-line align-middle"></i> <span class="mk-no-reales-label" data-prog="{{ $p->id }}">0</span> no reales</span>
                                            <span class="text-muted">·</span>
                                            <span style="color:#7c6ee6;"><i class="ri-swap-line align-middle"></i> <span class="mk-traspasos-reales-label" data-prog="{{ $p->id }}">0</span>r / <span class="mk-traspasos-no-reales-label" data-prog="{{ $p->id }}">0</span>nr <span class="text-muted">trasp.</span></span>
                                        </div>
                                        <div class="mt-1" id="faltantes-reales-container-{{ $p->id }}" data-minimo="{{ $minimo }}">
                                            @if($minimo <= 0)
                                                <span class="faltan-pill sin-meta" id="faltantes-reales-pill-{{ $p->id }}"><i class="ri-question-line"></i> Sin meta definida</span>
                                            @elseif($faltantes === 0)
                                                <span class="faltan-pill ok" id="faltantes-reales-pill-{{ $p->id }}"><i class="ri-checkbox-circle-fill"></i> Meta alcanzada</span>
                                            @else
                                                <span class="faltan-pill pendiente" id="faltantes-reales-pill-{{ $p->id }}">
                                                    <i class="ri-user-add-line"></i> Faltan <span id="faltantes-reales-count-{{ $p->id }}">{{ $faltantes }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm px-3 py-1 btn-inscritos-reales fw-bold" data-id="{{ $p->id }}" style="font-size:.7rem;border-radius:999px;background:rgba(10,179,156,.1);color:#0ab39c;border:1px solid rgba(10,179,156,.2);white-space:nowrap;" title="Ver detalle de inscritos">
                                            <span id="mk-reales-count-{{ $p->id }}">0</span> reales <span class="text-muted" style="color:rgba(10,179,156,.4) !important;">·</span> <span id="mk-no-reales-count-{{ $p->id }}" style="color:#f06548;">0</span> <span style="color:#f06548;">no reales</span>
                                        </button>
                                    </td>
                                    <td>
                                        @if($mods->isNotEmpty())
                                            <div class="modulos-list">
                                                @foreach($mods->take(2) as $mod)
                                                    <div class="modulo-item">
                                                        <div class="m-name">{{ \Illuminate\Support\Str::limit($mod->nombre, 34) }}</div>
                                                        <div class="m-dates">
                                                            <span class="m-date-range"><i class="ri-calendar-line m-date-icon"></i>{{ $fmtFechaModulo($mod->fecha_inicio) }}</span>
                                                            @if($mod->fecha_inicio != $mod->fecha_fin)
                                                                <span class="m-date-sep">→</span>
                                                                <span class="m-date-range">{{ $fmtFechaModulo($mod->fecha_fin) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($mods->count() > 2)
                                                    <div class="text-muted fs-11 ms-2 mt-1 fw-semibold"><i class="ri-add-line align-middle"></i> {{ $mods->count() - 2 }} módulos más...</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted fs-12">Sin horarios</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="ri-inbox-2-line fs-2 mb-2 d-block"></i>
                                        No se encontraron programas con los filtros seleccionados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
    </div>
</div>

{{-- Modal: Editar mínimo de inscritos --}}
<div class="modal fade" id="modalMinimo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;">
            <div class="modal-header bg-light border-bottom" style="padding:.85rem 1rem;">
                <h6 class="modal-title fs-14 fw-semibold"><i class="ri-flag-2-line me-1"></i>Mínimo de Inscritos</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1rem;">
                <p class="text-muted fs-12 mb-2" id="minimoProgramaNombre">—</p>
                <label class="form-label text-muted fw-semibold fs-12">Cantidad mínima para iniciar</label>
                <input type="number" min="0" max="65535" class="form-control" id="inputMinimo" value="0">
            </div>
            <div class="modal-footer" style="padding:.75rem 1rem;">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnGuardarMinimo"><i class="ri-save-3-line"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal: Detalle de Inscritos Reales / No Reales --}}
<div class="modal fade" id="modalInscritosReales" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);border:0;padding:1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-light text-white rounded-circle fs-18" style="background:rgba(255,255,255,.2) !important;border:1px solid rgba(255,255,255,.3);">
                            <i class="ri-team-line"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="modal-title text-white fw-bold fs-15 mb-0" id="modalInscritosRealesTitle">Inscritos Reales</h6>
                        <small class="text-white-50 fs-12" id="modalInscritosRealesSub">Cargando...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#fafbfd;padding:1.25rem;" id="modalInscritosRealesBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando datos...</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';
    const urlMinimoBase = '{{ route("admin.marketing.programas.inscritos-minimo", ["id" => "PID"]) }}';
    let currentProgramaId = null;

    document.querySelectorAll('.btn-editar-minimo').forEach(btn => {
        btn.addEventListener('click', function () {
            currentProgramaId = this.dataset.id;
            document.getElementById('minimoProgramaNombre').textContent = this.dataset.nombre;
            document.getElementById('inputMinimo').value = this.dataset.minimo;
            new bootstrap.Modal(document.getElementById('modalMinimo')).show();
        });
    });

    document.getElementById('btnGuardarMinimo').addEventListener('click', function () {
        if (!currentProgramaId) return;
        const valor = document.getElementById('inputMinimo').value;
        if (valor === '' || parseInt(valor) < 0) { mostrarToast('error', 'Ingresa un valor válido'); return; }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(urlMinimoBase.replace('PID', currentProgramaId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ cantidad_inscritos_minimo: parseInt(valor) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalMinimo')).hide();
                mostrarToast('success', data.message);
                setTimeout(function () { location.reload(); }, 700);
            } else {
                mostrarToast('error', data.message || 'Error al guardar');
            }
        })
        .catch(function () { mostrarToast('error', 'Error de conexión'); })
        .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="ri-save-3-line"></i> Guardar'; });
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

    // === Inscritos Reales ===
    const urlInscritosReales = '{{ route("admin.marketing.programas.inscritos-reales", ["id" => "PID"]) }}';
    let modalInscritosReales = null;
    const modalInscritosRealesEl = document.getElementById('modalInscritosReales');
    if (modalInscritosRealesEl) {
        modalInscritosReales = new bootstrap.Modal(modalInscritosRealesEl);
    }

    // Cargar conteos de reales/no reales para todos los programas visibles
    document.querySelectorAll('.btn-inscritos-reales').forEach(function (btn) {
        const progId = btn.dataset.id;
        cargarConteoReales(progId);
    });

    // Delegación de eventos para botones
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-inscritos-reales');
        if (btn) {
            const progId = btn.dataset.id;
            abrirDetalleInscritosReales(progId);
        }
    });

    function cargarConteoReales(programaId) {
        fetch(urlInscritosReales.replace('PID', programaId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                var reales = data.total_reales;
                var noReales = data.total_no_reales;
                // Botones de la columna "Inscritos Reales"
                document.getElementById('mk-reales-count-' + programaId).textContent = reales;
                document.getElementById('mk-no-reales-count-' + programaId).textContent = noReales;
                // Labels en la columna "Inscritos / Faltantes"
                document.querySelector('.mk-reales-label[data-prog="' + programaId + '"]').textContent = reales;
                document.querySelector('.mk-no-reales-label[data-prog="' + programaId + '"]').textContent = noReales;
                document.querySelector('.mk-traspasos-reales-label[data-prog="' + programaId + '"]').textContent = data.traspasos_reales || 0;
                document.querySelector('.mk-traspasos-no-reales-label[data-prog="' + programaId + '"]').textContent = data.traspasos_no_reales || 0;
                // Progress bar multicolor: reales (verde) · no reales (rojo) · faltantes (gris base)
                var progBar = document.getElementById('prog-bar-' + programaId);
                var container = document.getElementById('faltantes-reales-container-' + programaId);
                if (progBar && container) {
                    var minimo = parseInt(container.dataset.minimo) || 0;
                    var realPct = minimo > 0 ? Math.min(100, reales / minimo * 100) : 0;
                    var noRealPct = minimo > 0 ? Math.min(Math.max(0, 100 - realPct), noReales / minimo * 100) : 0;
                    var segments = [];
                    if (realPct > 0) segments.push({ pct: realPct, color: '#0ab39c', type: 'reales' });
                    if (noRealPct > 0) segments.push({ pct: noRealPct, color: '#f06548', type: 'no-reales' });
                    var html = '';
                    for (var s = 0; s < segments.length; s++) {
                        var cls = 'bar';
                        if (segments.length === 1) {
                            cls += ' bar-single';
                        } else if (s === 0) {
                            cls += ' bar-start';
                        } else if (s === segments.length - 1) {
                            cls += ' bar-end';
                        }
                        html += '<div class="' + cls + '" style="width:' + segments[s].pct + '%;background:' + segments[s].color + ';"></div>';
                    }
                    progBar.innerHTML = html;
                }
                // Actualizar faltantes considerando reales
                if (!container) return;
                var minimo2 = parseInt(container.dataset.minimo) || 0;
                var pill = document.getElementById('faltantes-reales-pill-' + programaId);
                if (!pill) return;
                if (minimo2 <= 0) {
                    pill.className = 'faltan-pill sin-meta';
                    pill.innerHTML = '<i class="ri-question-line"></i> Sin meta definida';
                } else if (reales >= minimo2) {
                    pill.className = 'faltan-pill ok';
                    pill.innerHTML = '<i class="ri-checkbox-circle-fill"></i> Meta alcanzada (' + reales + ' reales)';
                } else {
                    var falta = minimo2 - reales;
                    pill.className = 'faltan-pill pendiente';
                    pill.innerHTML = '<i class="ri-user-add-line"></i> Faltan ' + falta + ' reales';
                    document.getElementById('faltantes-reales-count-' + programaId).textContent = falta;
                }
            }
        })
        .catch(function () {});
    }

    function abrirDetalleInscritosReales(programaId) {
        const titleEl = document.getElementById('modalInscritosRealesTitle');
        const subEl = document.getElementById('modalInscritosRealesSub');
        const bodyEl = document.getElementById('modalInscritosRealesBody');

        if (!titleEl || !subEl || !bodyEl || !modalInscritosReales) {
            if (bodyEl) bodyEl.innerHTML = '<div class="alert alert-danger mb-0">Error: no se encontraron elementos del modal</div>';
            return;
        }

        titleEl.textContent = 'Inscritos Reales Marketing';
        subEl.textContent = 'Cargando...';
        bodyEl.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2 mb-0">Cargando datos...</p></div>';

        modalInscritosReales.show();

        fetch(urlInscritosReales.replace('PID', programaId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (r) {
            if (!r.ok) {
                throw new Error('HTTP ' + r.status);
            }
            return r.json();
        })
        .then(function (data) {
            if (data.success) {
                subEl.textContent = data.total_reales + ' reales · ' + data.total_no_reales + ' no reales';
                bodyEl.innerHTML = data.html;
            } else {
                bodyEl.innerHTML = '<div class="alert alert-danger mb-0">' + (data.message || 'Error al cargar datos') + '</div>';
            }
        })
        .catch(function (err) {
            bodyEl.innerHTML = '<div class="alert alert-danger mb-0">Error: ' + (err.message || 'Error de conexión') + '</div>';
        });
    }
});
</script>
@endpush
