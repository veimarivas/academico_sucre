@extends('admin.layouts.master')

@section('title', 'Lista de Asesores - Marketing - ' . config('app.name'))

@push('css')
<style>
.mkt-hero {
    position: relative;
    background: linear-gradient(135deg, #12345b 0%, #1c486c 45%, #0a6a90 100%);
    border-radius: 18px;
    padding: 1.6rem 1.8rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 18px 40px -22px rgba(18,52,91,.75);
    margin-bottom: 1.25rem;
}
.mkt-hero::before { content:""; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius:50%; }
.mkt-hero::after  { content:""; position:absolute; bottom:-60px; left:30%; width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius:50%; }
.mkt-hero > * { position:relative; z-index:1; }
.mkt-hero .hero-icon { width:56px; height:56px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.25); border-radius:14px; font-size:1.6rem; backdrop-filter: blur(4px); }
.mkt-hero h4 { color:#fff; font-weight:700; margin:0; }
.mkt-hero .hero-sub { color: rgba(255,255,255,.85); font-size:.88rem; margin: 2px 0 0; }
.mkt-hero .breadcrumb { background:transparent; padding:0; margin:0; }
.mkt-hero .breadcrumb-item, .mkt-hero .breadcrumb-item a { color: rgba(255,255,255,.85); }
.mkt-hero .breadcrumb-item.active { color:#fff; font-weight:600; }

.kpi { background:#fff; border:1px solid #eef0f4; border-radius:14px; padding:1rem 1.15rem; height:100%; position:relative; overflow:hidden; transition: all .2s ease; }
.kpi::before { content:""; position:absolute; top:0; left:0; height:100%; width:4px; background: var(--k-accent, #299cdb); }
.kpi:hover { transform: translateY(-2px); box-shadow: 0 12px 26px -14px rgba(64,81,137,.3); }
[data-layout-mode="dark"] .kpi { background:#1d2125; border-color:#2a2f34; }
.kpi .k-lbl { font-size:.66rem; text-transform:uppercase; letter-spacing:.45px; color:#878a99; font-weight:700; }
.kpi .k-val { font-size:1.4rem; font-weight:800; color:#212529; line-height:1.1; }
[data-layout-mode="dark"] .kpi .k-val { color:#fff; }
.kpi .k-sub { font-size:.7rem; color:#878a99; margin-top:2px; }
.kpi .k-ic { width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:1.2rem; background: var(--k-bg, #eef1f8); color: var(--k-color, #405189); }

.cargo-group {
    border: 1px solid #eef0f4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 18px -10px rgba(64,81,137,.18);
    margin-bottom: 1.25rem;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.cargo-group:hover { box-shadow: 0 10px 24px -12px rgba(64,81,137,.25); }
[data-layout-mode="dark"] .cargo-group { background:#1d2125; border-color:#2a2f34; }
.cargo-header {
    padding: .9rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
    border-bottom: 1px solid #eef0f4;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    cursor: pointer;
    user-select: none;
}
[data-layout-mode="dark"] .cargo-header { background:#1a1d20; border-bottom-color:#2a2f34; }
.cargo-header:hover { background: linear-gradient(135deg, #eef1f8 0%, #e2e6f0 100%); }
.cargo-nombre {
    font-weight: 700;
    color: #212529;
    font-size: .95rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
[data-layout-mode="dark"] .cargo-nombre { color: #fff; }
.cargo-nombre i { color: #405189; font-size: 1.1rem; }
.cargo-count {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
    color: #fff;
    border-radius: 999px;
    padding: .3em .85em;
    font-size: .75rem;
    font-weight: 700;
    box-shadow: 0 4px 10px -4px rgba(64,81,137,.45);
}
.cargo-actions { display: flex; align-items: center; gap: .4rem; }
.cargo-actions .btn-cargo { padding: .3rem .65rem; font-size: .72rem; border-radius: 8px; font-weight: 700; transition: all .15s ease; }

.table-asesores { width: 100%; table-layout: fixed; }
.table-asesores th, .table-asesores td { vertical-align: middle; padding: .6rem .5rem; font-size: .78rem; white-space: normal; overflow: hidden; word-break: break-word; }

/* Móvil: la tabla tiene 8 columnas; en pantallas angostas se desplaza en
   horizontal en lugar de comprimirse (el contenedor deja de recortar). */
@media (max-width: 767.98px) {
    .cargo-group { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-asesores { table-layout: auto; min-width: 720px; }
    .table-asesores th, .table-asesores td { white-space: nowrap; }
}
.table-asesores thead th {
    background: #fafbfd !important;
    font-size: .66rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4 !important;
    padding: .65rem .75rem;
}
[data-layout-mode="dark"] .table-asesores thead th { background: #1d2125 !important; border-bottom-color:#2a2f34 !important; }
.table-asesores tbody tr { border-bottom: 1px solid #f1f3f5; transition: background-color .15s ease; }
.table-asesores tbody tr:hover { background-color: #f8f9fc; }
.table-asesores tbody tr:last-child td { border-bottom: none; }
[data-layout-mode="dark"] .table-asesores tbody tr { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .table-asesores tbody tr:hover { background-color: #1a1d20; }

.mini-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .7rem; color: #fff; flex-shrink: 0;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 100%);
}
.asesor-nombre { font-weight: 600; color: #212529; font-size: .82rem; line-height: 1.2; }
.asesor-ci { font-size: .68rem; color: #878a99; margin-top: 2px; }
[data-layout-mode="dark"] .asesor-nombre { color: #fff; }

.est-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .68rem; font-weight: 700;
    padding: .25em .7em; border-radius: 999px;
    border: 1px solid;
}
.est-hab { background: #d1fae5; color: #0ab39c; border-color: #a7f3d8; }
.est-des { background: #fee2e2; color: #f06548; border-color: #fecaca; }
.est-toggle {
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
}
.est-toggle:hover { transform: scale(1.05); filter: brightness(.95); }
.est-toggle.est-hab:hover { box-shadow: 0 0 0 2px rgba(10,179,156,.25); }
.est-toggle.est-des:hover { box-shadow: 0 0 0 2px rgba(240,101,72,.25); }
.est-toggle:active { transform: scale(.95); }

.btn-accion {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .72rem; font-weight: 700;
    padding: .3em .75em; border-radius: 8px;
    transition: all .15s ease;
    border: 1px solid #e9ebec;
    background: #fff;
    color: #495057;
}
.btn-accion:hover { background: #f1f3f5; border-color: #d6dbe0; }
.btn-accion.editar:hover { background: #dbeafe; color: #299cdb; border-color: #bae6fd; }
.btn-accion.eliminar:hover { background: #fee2e2; color: #f06548; border-color: #fecaca; }

.sin-cargo-warning {
    border: 1px dashed #f7b84b;
    border-radius: 14px;
    background: #fffbeb;
    padding: .75rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: 1rem;
    font-size: .82rem;
    color: #92400e;
}
[data-layout-mode="dark"] .sin-cargo-warning { background: #1d2125; border-color:#f7b84b; color: #f7b84b; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="mkt-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-group-2-line"></i></div>
        <div>
            <h4>Lista de Asesores</h4>
            <p class="hero-sub">Asesores agrupados por cargo — Gestión de personal de marketing</p>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Marketing</a></li>
        <li class="breadcrumb-item active">Lista de Asesores</li>
    </ol>
    <div class="d-flex gap-2">
        @can('asesores.registrar')
        <button type="button" class="btn btn-sm btn-soft-primary d-inline-flex align-items-center gap-1" id="btn-nuevo-asesor">
            <i class="ri-add-circle-line"></i> Registrar Asesor
        </button>
        <button type="button" class="btn btn-sm btn-soft-primary d-inline-flex align-items-center gap-1" id="btn-nuevo-cargo">
            <i class="ri-briefcase-2-line"></i> Nuevo Cargo
        </button>
        @endcan
    </div>
</div>

{{-- KPIs --}}
<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Total asesores</div><div class="k-val">{{ $resumen['total_asesores'] }}</div><div class="k-sub">en el sistema</div></div>
                <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-user-voice-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Habilitados</div><div class="k-val">{{ $resumen['habilitados'] }}</div><div class="k-sub">asesores activos</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-check-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#f06548;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Deshabilitados</div><div class="k-val">{{ $resumen['deshabilitados'] }}</div><div class="k-sub">asesores inactivos</div></div>
                <span class="k-ic" style="--k-bg:#fef2f2; --k-color:#f06548;"><i class="ri-close-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#f7b84b;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Cargos</div><div class="k-val">{{ $resumen['total_cargos'] }}</div><div class="k-sub">distintos cargos</div></div>
                <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-briefcase-line"></i></span>
            </div>
        </div>
    </div>
</div>

{{-- Asesores sin cargo --}}
@if($sinCargoHab->isNotEmpty())
    <div class="sin-cargo-warning">
        <i class="ri-alert-line" style="font-size:1.2rem;"></i>
        <span><strong>{{ $sinCargoHab->count() }} asesor(es) habilitado(s)</strong> no tienen un cargo asignado. Asigne un cargo desde la edición del asesor.</span>
    </div>
@endif

{{-- Grupos por cargo --}}
<div id="cargos-container">
    @forelse($cargos as $cargo)
        @php $asesoresGrupo = $habilitadosPorCargo->get($cargo->id, collect()); @endphp
        <div class="cargo-group" data-cargo-id="{{ $cargo->id }}">
            <div class="cargo-header" data-bs-toggle="collapse" data-bs-target="#collapseCargo{{ $cargo->id }}" aria-expanded="true">
                <div class="d-flex align-items-center gap-3">
                    <div class="cargo-nombre">
                        <i class="ri-briefcase-4-line"></i>
                        <span class="cargo-nombre-text">{{ $cargo->nombre }}</span>
                    </div>
                    @if(!empty($cargo->metas_puntos))
                        <span class="cargo-count" style="background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%);"><i class="ri-star-line"></i> Meta: {{ number_format($cargo->metas_puntos, 0, ',', '.') }} pts/mes</span>
                    @endif
                    <span class="cargo-count"><i class="ri-team-line"></i> {{ $asesoresGrupo->count() }} asesor(es)</span>
                </div>
                <div class="cargo-actions">
                    @can('asesores.gestionar')
                    <button type="button" class="btn-cargo btn-editar-cargo" title="Editar cargo" style="background:#dbeafe;color:#299cdb;border:1px solid #bae6fd;"
                        onclick="event.stopPropagation(); editarCargo({{ $cargo->id }}, '{{ e($cargo->nombre) }}', {{ $cargo->metas_puntos ?? 'null' }})">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn-cargo btn-eliminar-cargo" title="Eliminar cargo" style="background:#fee2e2;color:#f06548;border:1px solid #fecaca;"
                        onclick="event.stopPropagation(); eliminarCargo({{ $cargo->id }}, '{{ e($cargo->nombre) }}')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    @endcan
                    <i class="ri-arrow-down-s-line" style="font-size:1.2rem;color:#878a99;"></i>
                </div>
            </div>
            <div class="collapse show" id="collapseCargo{{ $cargo->id }}">
                @if($asesoresGrupo->isNotEmpty())
                <div>
                    <table class="table-asesores table">
                        <thead>
                            <tr>
                                <th style="width:38px;" class="text-center">#</th>
                                <th>Asesor</th>
                                <th style="width:90px;">CI</th>
                                <th style="width:108px;">Celular</th>
                                <th style="width:180px;">Correo</th>
                                <th style="width:86px;" class="text-center">Sexo</th>
                                <th style="width:92px;" class="text-center">Estado</th>
                                <th style="width:110px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asesoresGrupo as $i => $a)
                            @php
                                $iniA = strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1));
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="mini-avatar">{{ $iniA }}</div>
                                        <div style="min-width:0;">
                                            <div class="asesor-nombre" title="{{ $a->apellidos }}, {{ $a->nombres }}">{{ $a->apellidos }}, {{ $a->nombres }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $a->ci }}</td>
                                <td>{{ $a->celular }}</td>
                                <td title="{{ $a->correo }}">{{ $a->correo }}</td>
                                <td class="text-center">
                                    @if($a->sexo === 'Hombre')
                                        <span class="est-pill" style="background:#dbeafe;color:#3577f1;border-color:#c3ddfa;"><i class="ri-men-line"></i> Hombre</span>
                                    @elseif($a->sexo === 'Mujer')
                                        <span class="est-pill" style="background:#fce7f3;color:#ec4899;border-color:#fbcfe8;"><i class="ri-women-line"></i> Mujer</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('asesores.gestionar')
                                        <span class="est-pill est-toggle {{ $a->estado === 'Habilitado' ? 'est-hab' : 'est-des' }}"
                                            onclick="toggleEstado({{ $a->id }}, this)"
                                            title="Haga clic para cambiar estado">
                                            <i class="ri-{{ $a->estado === 'Habilitado' ? 'check-line' : 'close-line' }}"></i>
                                            {{ $a->estado === 'Habilitado' ? 'Habilitado' : $a->estado }}
                                        </span>
                                    @else
                                        <span class="est-pill {{ $a->estado === 'Habilitado' ? 'est-hab' : 'est-des' }}">
                                            <i class="ri-{{ $a->estado === 'Habilitado' ? 'check-line' : 'close-line' }}"></i>
                                            {{ $a->estado === 'Habilitado' ? 'Habilitado' : $a->estado }}
                                        </span>
                                    @endcan
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('asesores.vercompleto')
                                        <a href="{{ route('admin.marketing.asesor-show', ['id' => $a->id, 'gestion' => now()->format('Y'), 'mes' => now()->format('m')]) }}" class="btn-accion" title="Ver asesor" style="text-decoration:none;">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endcan
                                        @can('asesores.gestionar')
                                        <button type="button" class="btn-accion editar" title="Editar asesor"
                                            onclick="editarAsesor({{ $a->id }})">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn-accion eliminar" title="Eliminar asesor"
                                            onclick="eliminarAsesor({{ $a->id }}, '{{ e($a->apellidos) }}, {{ e($a->nombres) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="padding:2rem 1.25rem;text-align:center;color:#878a99;">
                    <i class="ri-inbox-2-line" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.5;"></i>
                    <p class="mb-0" style="font-size:.85rem;">No hay asesores en este cargo.</p>
                </div>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:3rem 1rem;background:linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);border-radius:14px;border:1px dashed #d6dbe0;">
            <div style="width:80px;height:80px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fff;border-radius:50%;font-size:2.5rem;color:#adb5bd;box-shadow:0 8px 20px -10px rgba(0,0,0,.15);">
                <i class="ri-briefcase-4-line"></i>
            </div>
            <h5 class="text-dark fw-bold mb-1">Sin cargos registrados</h5>
            <p class="text-muted mb-3">No hay cargos creados. Cree un nuevo cargo para empezar a organizar los asesores.</p>
            @can('asesores.registrar')
            <button type="button" class="btn btn-sm" id="btn-nuevo-cargo-empty"
                style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:600;padding:.5rem 1.25rem;">
                <i class="ri-add-circle-line"></i> Crear primer cargo
            </button>
            @endcan
        </div>
    @endforelse

    {{-- Asesores sin cargo (habilitados) --}}
    @if($sinCargoHab->isNotEmpty())
    <div class="cargo-group" style="border-color:#f7b84b;">
        <div class="cargo-header" data-bs-toggle="collapse" data-bs-target="#collapseSinCargoHab" aria-expanded="true" style="background:linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="cargo-nombre">
                    <i class="ri-question-line" style="color:#f7b84b;"></i>
                    <span style="color:#92400e;">Sin cargo asignado</span>
                </div>
                <span class="cargo-count" style="background:linear-gradient(135deg, #f7b84b 0%, #f59e0b 100%);"><i class="ri-team-line"></i> {{ $sinCargoHab->count() }} habilitado(s)</span>
            </div>
            <i class="ri-arrow-down-s-line" style="font-size:1.2rem;color:#92400e;"></i>
        </div>
        <div class="collapse show" id="collapseSinCargoHab">
            <div>
                <table class="table-asesores table">
                    <thead>
                        <tr>
                            <th style="width:38px;" class="text-center">#</th>
                            <th>Asesor</th>
                            <th style="width:90px;">CI</th>
                            <th style="width:108px;">Celular</th>
                            <th style="width:180px;">Correo</th>
                            <th style="width:86px;" class="text-center">Sexo</th>
                            <th style="width:92px;" class="text-center">Estado</th>
                            <th style="width:110px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sinCargoHab as $i => $a)
                        @php $iniA = strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1)); @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="mini-avatar">{{ $iniA }}</div>
                                    <div style="min-width:0;"><div class="asesor-nombre">{{ $a->apellidos }}, {{ $a->nombres }}</div></div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $a->ci }}</td>
                            <td>{{ $a->celular }}</td>
                            <td title="{{ $a->correo }}">{{ $a->correo }}</td>
                            <td class="text-center">
                                @if($a->sexo === 'Hombre')
                                    <span class="est-pill" style="background:#dbeafe;color:#3577f1;border-color:#c3ddfa;"><i class="ri-men-line"></i> Hombre</span>
                                @elseif($a->sexo === 'Mujer')
                                    <span class="est-pill" style="background:#fce7f3;color:#ec4899;border-color:#fbcfe8;"><i class="ri-women-line"></i> Mujer</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('asesores.gestionar')
                                    <span class="est-pill est-toggle {{ $a->estado === 'Habilitado' ? 'est-hab' : 'est-des' }}"
                                        onclick="toggleEstado({{ $a->id }}, this)"
                                        title="Haga clic para cambiar estado">
                                        <i class="ri-{{ $a->estado === 'Habilitado' ? 'check-line' : 'close-line' }}"></i>
                                        {{ $a->estado === 'Habilitado' ? 'Habilitado' : $a->estado }}
                                    </span>
                                @else
                                    <span class="est-pill {{ $a->estado === 'Habilitado' ? 'est-hab' : 'est-des' }}">
                                        <i class="ri-{{ $a->estado === 'Habilitado' ? 'check-line' : 'close-line' }}"></i>
                                        {{ $a->estado === 'Habilitado' ? 'Habilitado' : $a->estado }}
                                    </span>
                                @endcan
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                        @can('asesores.vercompleto')
                                        <a href="{{ route('admin.marketing.asesor-show', ['id' => $a->id, 'gestion' => now()->format('Y'), 'mes' => now()->format('m')]) }}" class="btn-accion" title="Ver asesor" style="text-decoration:none;">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endcan
                                        @can('asesores.gestionar')
                                        <button type="button" class="btn-accion editar" title="Editar asesor" onclick="editarAsesor({{ $a->id }})">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn-accion eliminar" title="Eliminar asesor" onclick="eliminarAsesor({{ $a->id }}, '{{ e($a->apellidos) }}, {{ e($a->nombres) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Deshabilitados (agrupados por cargo) --}}
    @php
        $cargosDes = $cargos->filter(fn($c) => $deshabilitadosPorCargo->get($c->id, collect())->isNotEmpty());
    @endphp
    @if($cargosDes->isNotEmpty() || $sinCargoDes->isNotEmpty())
    <div style="margin-top:1.75rem;margin-bottom:.75rem;display:flex;align-items:center;gap:.65rem;">
        <span style="width:5px;height:30px;background:#f06548;border-radius:4px;display:inline-block;"></span>
        <h5 style="margin:0;font-weight:700;color:#212529;font-size:.95rem;">
            <i class="ri-close-circle-line me-1" style="color:#f06548;"></i>
            Asesores Deshabilitados
        </h5>
        <span class="badge" style="background:#f06548;font-weight:600;font-size:.7rem;">{{ $resumen['deshabilitados'] }}</span>
    </div>
    @foreach($cargos as $cargo)
        @php $desGrupo = $deshabilitadosPorCargo->get($cargo->id, collect()); @endphp
        @if($desGrupo->isNotEmpty())
        <div class="cargo-group" style="border-color:#fecaca;">
            <div class="cargo-header" data-bs-toggle="collapse" data-bs-target="#collapseDesCargo{{ $cargo->id }}" aria-expanded="true" style="background:linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="cargo-nombre">
                        <i class="ri-briefcase-4-line" style="color:#f06548;"></i>
                        <span>{{ $cargo->nombre }}</span>
                    </div>
                    <span class="cargo-count" style="background:linear-gradient(135deg, #f06548 0%, #e11d48 100%);"><i class="ri-team-line"></i> {{ $desGrupo->count() }} deshabilitado(s)</span>
                </div>
                <i class="ri-arrow-down-s-line" style="font-size:1.2rem;color:#92400e;"></i>
            </div>
            <div class="collapse show" id="collapseDesCargo{{ $cargo->id }}">
                <div>
                    <table class="table-asesores table">
                        <thead>
                            <tr>
                                <th style="width:38px;" class="text-center">#</th>
                                <th>Asesor</th>
                                <th style="width:90px;">CI</th>
                                <th style="width:108px;">Celular</th>
                                <th style="width:180px;">Correo</th>
                                <th style="width:86px;" class="text-center">Sexo</th>
                                <th style="width:92px;" class="text-center">Estado</th>
                                <th style="width:110px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($desGrupo as $i => $a)
                            @php $iniA = strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1)); @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="mini-avatar" style="background:linear-gradient(135deg, #f06548 0%, #e11d48 100%);">{{ $iniA }}</div>
                                        <div style="min-width:0;">
                                            <div class="asesor-nombre" title="{{ $a->apellidos }}, {{ $a->nombres }}">{{ $a->apellidos }}, {{ $a->nombres }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $a->ci }}</td>
                                <td>{{ $a->celular }}</td>
                                <td title="{{ $a->correo }}">{{ $a->correo }}</td>
                                <td class="text-center">
                                    @if($a->sexo === 'Hombre')
                                        <span class="est-pill" style="background:#dbeafe;color:#3577f1;border-color:#c3ddfa;"><i class="ri-men-line"></i> Hombre</span>
                                    @elseif($a->sexo === 'Mujer')
                                        <span class="est-pill" style="background:#fce7f3;color:#ec4899;border-color:#fbcfe8;"><i class="ri-women-line"></i> Mujer</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('asesores.gestionar')
                                        <span class="est-pill est-toggle est-des"
                                            onclick="toggleEstado({{ $a->id }}, this)"
                                            title="Haga clic para cambiar estado">
                                            <i class="ri-close-line"></i>
                                            {{ $a->estado !== 'Habilitado' ? $a->estado : 'Deshabilitado' }}
                                        </span>
                                    @else
                                        <span class="est-pill est-des">
                                            <i class="ri-close-line"></i>
                                            {{ $a->estado !== 'Habilitado' ? $a->estado : 'Deshabilitado' }}
                                        </span>
                                    @endcan
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('asesores.vercompleto')
                                        <a href="{{ route('admin.marketing.asesor-show', ['id' => $a->id, 'gestion' => now()->format('Y'), 'mes' => now()->format('m')]) }}" class="btn-accion" title="Ver asesor" style="text-decoration:none;">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endcan
                                        @can('asesores.gestionar')
                                        <button type="button" class="btn-accion editar" title="Editar asesor"
                                            onclick="editarAsesor({{ $a->id }})">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn-accion eliminar" title="Eliminar asesor"
                                            onclick="eliminarAsesor({{ $a->id }}, '{{ e($a->apellidos) }}, {{ e($a->nombres) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
</table>
                </div>
        @endif
    @endforeach

    {{-- Sin cargo deshabilitados --}}
    @if($sinCargoDes->isNotEmpty())
    <div class="cargo-group" style="border-color:#fecaca;">
        <div class="cargo-header" data-bs-toggle="collapse" data-bs-target="#collapseSinCargoDes" aria-expanded="true" style="background:linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="cargo-nombre">
                    <i class="ri-question-line" style="color:#f06548;"></i>
                    <span style="color:#991b1b;">Sin cargo asignado</span>
                </div>
                <span class="cargo-count" style="background:linear-gradient(135deg, #f06548 0%, #e11d48 100%);"><i class="ri-team-line"></i> {{ $sinCargoDes->count() }} deshabilitado(s)</span>
            </div>
            <i class="ri-arrow-down-s-line" style="font-size:1.2rem;color:#991b1b;"></i>
        </div>
        <div class="collapse show" id="collapseSinCargoDes">
            <div>
                <table class="table-asesores table">
                    <thead>
                        <tr>
                            <th style="width:38px;" class="text-center">#</th>
                            <th>Asesor</th>
                            <th style="width:90px;">CI</th>
                            <th style="width:108px;">Celular</th>
                            <th style="width:180px;">Correo</th>
                            <th style="width:86px;" class="text-center">Sexo</th>
                            <th style="width:92px;" class="text-center">Estado</th>
                            <th style="width:110px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sinCargoDes as $i => $a)
                        @php $iniA = strtoupper(substr($a->nombres ?? '', 0, 1) . substr($a->apellidos ?? '', 0, 1)); @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="mini-avatar" style="background:linear-gradient(135deg, #f06548 0%, #e11d48 100%);">{{ $iniA }}</div>
                                    <div style="min-width:0;"><div class="asesor-nombre">{{ $a->apellidos }}, {{ $a->nombres }}</div></div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $a->ci }}</td>
                            <td>{{ $a->celular }}</td>
                            <td title="{{ $a->correo }}">{{ $a->correo }}</td>
                            <td class="text-center">
                                @if($a->sexo === 'Hombre')
                                    <span class="est-pill" style="background:#dbeafe;color:#3577f1;border-color:#c3ddfa;"><i class="ri-men-line"></i> Hombre</span>
                                @elseif($a->sexo === 'Mujer')
                                    <span class="est-pill" style="background:#fce7f3;color:#ec4899;border-color:#fbcfe8;"><i class="ri-women-line"></i> Mujer</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('asesores.gestionar')
                                    <span class="est-pill est-toggle est-des"
                                        onclick="toggleEstado({{ $a->id }}, this)"
                                        title="Haga clic para cambiar estado">
                                        <i class="ri-close-line"></i>
                                        {{ $a->estado !== 'Habilitado' ? $a->estado : 'Deshabilitado' }}
                                    </span>
                                @else
                                    <span class="est-pill est-des">
                                        <i class="ri-close-line"></i>
                                        {{ $a->estado !== 'Habilitado' ? $a->estado : 'Deshabilitado' }}
                                    </span>
                                @endcan
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                        @can('asesores.vercompleto')
                                        <a href="{{ route('admin.marketing.asesor-show', ['id' => $a->id, 'gestion' => now()->format('Y'), 'mes' => now()->format('m')]) }}" class="btn-accion" title="Ver asesor" style="text-decoration:none;">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endcan
                                        @can('asesores.gestionar')
                                        <button type="button" class="btn-accion editar" title="Editar asesor" onclick="editarAsesor({{ $a->id }})">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn-accion eliminar" title="Eliminar asesor" onclick="eliminarAsesor({{ $a->id }}, '{{ e($a->apellidos) }}, {{ e($a->nombres) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>

{{-- Modal Registrar Asesor (reutilizado del index principal) --}}
@can('asesores.registrar')
<div class="modal fade" id="modalRegistrarAsesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;position:relative;overflow:hidden;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;">
                        <i class="ri-user-voice-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;" id="modalAsesorTitle">Registrar Asesor</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="modalAsesorSub">Nuevo asesor de marketing</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="formAsesor" novalidate>
                @csrf
                <input type="hidden" name="_method" id="f-asesor-method" value="POST">
                <input type="hidden" name="id" id="f-asesor-id" value="">
                <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Nombres <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-user-line"></i></span>
                                <input type="text" name="nombres" id="f-nombres" class="form-control" required maxlength="255" placeholder="Nombres" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Apellidos <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-user-line"></i></span>
                                <input type="text" name="apellidos" id="f-apellidos" class="form-control" required maxlength="255" placeholder="Apellidos" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Cédula (CI) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-passport-line"></i></span>
                                <input type="text" name="ci" id="f-ci" class="form-control" required maxlength="10" placeholder="1234567890" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Sexo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-men-line"></i></span>
                                <select name="sexo" id="f-sexo" class="form-control" required style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                                    <option value="">Seleccionar...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Celular <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-phone-line"></i></span>
                                <input type="text" name="celular" id="f-celular" class="form-control" required maxlength="10" placeholder="0987654321" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Correo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-mail-line"></i></span>
                                <input type="email" name="correo" id="f-correo" class="form-control" required maxlength="255" placeholder="correo@ejemplo.com" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Cargo</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-briefcase-4-line"></i></span>
                                <select name="cargo_id" id="f-cargo_id" class="form-control" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                                    <option value="">Sin cargo</option>
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Estado</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-toggle-line"></i></span>
                                <select name="estado" id="f-estado" class="form-control" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                                    <option value="Habilitado">Habilitado</option>
                                    <option value="Deshabilitado">Deshabilitado</option>
                                </select>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line align-bottom"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" id="btnSaveAsesor"
                        style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:600;padding:.5rem 1.25rem;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);transition:transform .15s ease;"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Cargo --}}
<div class="modal fade" id="modalCargo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -15px rgba(0,0,0,.25);">
            <div class="modal-header" style="background:linear-gradient(135deg,#405189 0%,#5e72b8 60%,#299cdb 100%);color:#fff;border:0;padding:1.1rem 1.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:12px;font-size:1.25rem;color:#fff;">
                        <i class="ri-briefcase-4-line"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" style="color:#fff;font-weight:700;" id="modalCargoTitle">Nuevo Cargo</h5>
                        <small style="color:rgba(255,255,255,.85);font-size:.78rem;" id="modalCargoSub">Crear un nuevo cargo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1);opacity:.85;"></button>
            </div>
            <form id="formCargo" novalidate>
                @csrf
                <input type="hidden" name="_method" id="f-cargo-method" value="POST">
                <input type="hidden" name="id" id="f-cargo-id" value="">
                <div class="modal-body" style="padding:1.5rem;background:#fafbfd;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Nombre del cargo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-briefcase-4-line"></i></span>
                                <input type="text" name="nombre" id="f-cargo-nombre" class="form-control" required maxlength="255" placeholder="Ej: Asesor Senior" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight:600;font-size:.72rem;color:#6c757d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.35rem;">Meta de puntos por mes</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8f9fc;color:#878a99;border-right:0;"><i class="ri-star-line"></i></span>
                                <input type="number" step="0.1" min="0" name="metas_puntos" id="f-metas_puntos" class="form-control" placeholder="Ej: 40" style="border-radius:0 8px 8px 0;border:1px solid #e9ebec;">
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted" style="font-size:.68rem;">Puntos mínimos por mes que debe cumplir cada asesor de este cargo. Déjalo vacío si no tiene.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#fff;border-top:1px solid #eef0f4;padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="ri-close-line align-bottom"></i> Cancelar</button>
                    <button type="submit" class="btn" id="btnSaveCargo"
                        style="background:linear-gradient(135deg,#0ab39c 0%,#14b8a6 100%);border:0;color:#fff;font-weight:600;padding:.5rem 1.25rem;box-shadow:0 6px 16px -6px rgba(10,179,156,.55);transition:transform .15s ease;"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        <i class="ri-save-3-line align-bottom"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalAsesor = new bootstrap.Modal(document.getElementById('modalRegistrarAsesor'));
    const modalCargo = new bootstrap.Modal(document.getElementById('modalCargo'));

    // === Registrar / Editar Asesor ===
    document.getElementById('btn-nuevo-asesor')?.addEventListener('click', function () {
        resetAsesorForm();
        document.getElementById('modalAsesorTitle').textContent = 'Registrar Asesor';
        document.getElementById('modalAsesorSub').textContent = 'Nuevo asesor de marketing';
        document.getElementById('f-asesor-method').value = 'POST';
        document.getElementById('f-asesor-id').value = '';
        modalAsesor.show();
    });

    document.getElementById('formAsesor').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = document.getElementById('f-asesor-id').value;
        const method = document.getElementById('f-asesor-method').value;
        const btn = document.getElementById('btnSaveAsesor');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        let url = '{{ route("admin.marketing.asesores.store") }}';
        if (method === 'PUT') {
            url = '/admin/marketing/asesores/' + id;
            formData.set('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalAsesor.hide();
                location.reload();
            } else if (data.errors) {
                for (const field in data.errors) {
                    const input = document.getElementById('f-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const fb = input.closest('.col-md-6, .col-12').querySelector('.invalid-feedback');
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(() => alert('Error al guardar el asesor'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // === Nuevo / Editar Cargo ===
    function openCargoModal(title, sub, method, id, nombre, metas) {
        document.getElementById('modalCargoTitle').textContent = title;
        document.getElementById('modalCargoSub').textContent = sub;
        document.getElementById('f-cargo-method').value = method;
        document.getElementById('f-cargo-id').value = id || '';
        document.getElementById('f-cargo-nombre').value = nombre || '';
        document.getElementById('f-metas_puntos').value = (metas !== null && metas !== undefined && metas !== '') ? metas : '';
        document.getElementById('formCargo').querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.getElementById('formCargo').querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        modalCargo.show();
    }

    document.getElementById('btn-nuevo-cargo')?.addEventListener('click', function () {
        openCargoModal('Nuevo Cargo', 'Crear un nuevo cargo', 'POST', '', '', '');
    });

    document.getElementById('btn-nuevo-cargo-empty')?.addEventListener('click', function () {
        openCargoModal('Nuevo Cargo', 'Crear un nuevo cargo', 'POST', '', '', '');
    });

    document.getElementById('formCargo').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = document.getElementById('f-cargo-id').value;
        const method = document.getElementById('f-cargo-method').value;
        const btn = document.getElementById('btnSaveCargo');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        let url = '{{ route("admin.marketing.cargos.store") }}';
        if (method === 'PUT') {
            url = '/admin/marketing/cargos/' + id;
            formData.set('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                modalCargo.hide();
                location.reload();
            } else if (data.errors) {
                for (const field in data.errors) {
                    const input = document.getElementById('f-cargo-' + field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const fb = input.closest('.col-md-6, .col-12').querySelector('.invalid-feedback');
                        if (fb) fb.textContent = data.errors[field][0];
                    }
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(() => alert('Error al guardar el cargo'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-save-3-line align-bottom"></i> Guardar';
        });
    });

    // === Toggle Estado ===
    window.toggleEstado = function (id, el) {
        const originalHtml = el.innerHTML;
        el.style.pointerEvents = 'none';
        el.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;"></span>';

        fetch('/admin/marketing/asesores/' + id + '/toggle-estado', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new URLSearchParams({ '_method': 'PATCH' })
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                const habilitado = data.nuevo_estado === 'Habilitado';
                el.className = 'est-pill est-toggle ' + (habilitado ? 'est-hab' : 'est-des');
                el.innerHTML = '<i class="ri-' + (habilitado ? 'check-line' : 'close-line') + '"></i> ' + data.nuevo_estado;
                el.title = 'Haga clic para cambiar estado';
            } else {
                el.innerHTML = originalHtml;
                alert(data.message || 'Error al cambiar estado');
            }
        })
        .catch(function () {
            el.innerHTML = originalHtml;
            alert('Error al cambiar estado');
        })
        .finally(function () {
            el.style.pointerEvents = '';
        });
    };

    // === Utilidades ===
    function resetAsesorForm() {
        document.getElementById('formAsesor').reset();
        document.getElementById('formAsesor').querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.getElementById('formAsesor').querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    // === Exponer funciones globales ===
    window.editarAsesor = function (id) {
        // Cargar datos del asesor via GET
        fetch('/admin/marketing/asesor/' + id + '?ajax=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success === false) {
                alert(data.message || 'Error al cargar datos del asesor');
                return;
            }
            // If it's the show page response, we need to redirect to edit differently
            // Let's use a simple approach: load data from a dedicated endpoint
            fetch('/admin/marketing/asesor/' + id + '/data', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success === false) { alert(d.message); return; }
                const a = d.asesor;
                document.getElementById('f-asesor-id').value = a.id;
                document.getElementById('f-nombres').value = a.nombres;
                document.getElementById('f-apellidos').value = a.apellidos;
                document.getElementById('f-ci').value = a.ci;
                document.getElementById('f-sexo').value = a.sexo;
                document.getElementById('f-celular').value = a.celular;
                document.getElementById('f-correo').value = a.correo;
                document.getElementById('f-cargo_id').value = a.cargo_id || '';
                document.getElementById('f-estado').value = a.estado || 'Habilitado';
                document.getElementById('modalAsesorTitle').textContent = 'Editar Asesor';
                document.getElementById('modalAsesorSub').textContent = a.apellidos + ', ' + a.nombres;
                document.getElementById('f-asesor-method').value = 'PUT';
                modalAsesor.show();
            })
            .catch(() => alert('Error al cargar datos del asesor'));
        })
        .catch(() => alert('Error al cargar datos del asesor'));
    };

    window.eliminarAsesor = function (id, nombre) {
        if (!confirm('¿Está seguro de eliminar a "' + nombre + '"? Esta acción no se puede deshacer.')) return;
        fetch('/admin/marketing/asesores/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new URLSearchParams({ '_method': 'DELETE' })
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar el asesor');
            }
        })
        .catch(() => alert('Error al eliminar el asesor'));
    };

    window.editarCargo = function (id, nombre, metas) {
        document.getElementById('modalCargoTitle').textContent = 'Editar Cargo';
        document.getElementById('modalCargoSub').textContent = nombre;
        document.getElementById('f-cargo-method').value = 'PUT';
        document.getElementById('f-cargo-id').value = id;
        document.getElementById('f-cargo-nombre').value = nombre;
        document.getElementById('f-metas_puntos').value = (metas !== null && metas !== undefined && metas !== '') ? metas : '';
        document.getElementById('formCargo').querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.getElementById('formCargo').querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        modalCargo.show();
    };

    window.eliminarCargo = function (id, nombre) {
        if (!confirm('¿Está seguro de eliminar el cargo "' + nombre + '"? Los asesores asignados quedarán sin cargo.')) return;
        fetch('/admin/marketing/cargos/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new URLSearchParams({ '_method': 'DELETE' })
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar el cargo');
            }
        })
        .catch(() => alert('Error al eliminar el cargo'));
    };
});
</script>
@endpush
