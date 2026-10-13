{{-- Stats secundarias --}}
<div class="row mb-4">
    <div class="col-xl col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent: #0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Plan Contado</p>
                        <h4 class="stat-value mb-0" id="stat-contado">{{ $stats['contado'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-money-dollar-box-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent: #405189;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Plan Regular</p>
                        <h4 class="stat-value mb-0" id="stat-regular">{{ $stats['regular'] }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-primary text-primary"><i class="ri-calendar-todo-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent: #3577f1;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Hombres</p>
                        <h4 class="stat-value mb-0" id="stat-hombres">{{ $stats['hombres'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background:#dbeafe; color:#3577f1;"><i class="ri-men-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent: #ec4899;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Mujeres</p>
                        <h4 class="stat-value mb-0" id="stat-mujeres">{{ $stats['mujeres'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background:#fce7f3; color:#ec4899;"><i class="ri-women-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-3">
        <div class="stat-card h-100" style="--stat-accent: #8b5cf6;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="min-width-0 me-2">
                        <p class="stat-label text-truncate">Puntos Totales</p>
                        <h4 class="stat-value mb-0" id="stat-puntos">{{ number_format($stats['puntos'], 1, ',', '.') }}</h4>
                    </div>
                    <div class="stat-icon" style="background:#f3e8ff; color:#8b5cf6;"><i class="ri-star-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cumplimiento de meta mensual --}}
@php
    $hayMeta = $metaMensual > 0;
    $mesMeta = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $gestionNumerica = (int) $gestionFiltro;
    $gestionActual = (int) date('Y');
    $mesActual = (int) date('m');
    $ultimoMes = ($gestionNumerica === $gestionActual) ? max(0, $mesActual - 1) : (($gestionNumerica < $gestionActual) ? 12 : 0);
@endphp
@if($gestionFiltro !== '' && ( $hayMeta || collect($metasAsesor)->sum(fn($m) => $m['real'] ?? 0) > 0 ))
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body pb-0">
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;">
                        <i style="color:#8b5cf6;font-size:1rem;" class="ri-flag-2-line"></i> Cumplimiento de Meta Mensual — Gestión {{ $gestionFiltro }}
                    </div>
                    <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">
                        Cargo: <strong>{{ $asesor->cargo_nombre ?? 'Sin cargo' }}</strong>
                        @if($hayMeta)
                            · Meta/mes: <strong>{{ number_format($metaMensual, 0, ',', '.') }} pts</strong>
                        @else
                            · Sin meta asignada
                        @endif
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-1" style="min-width:560px;">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th class="text-center">Meta</th>
                                <th class="text-center">Sede</th>
                                <th class="text-center">Otras sedes</th>
                                <th class="text-center">Real</th>
                                <th class="text-center">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(range(1, $ultimoMes) as $m)
                                @php $mm = $metasAsesor[$m] ?? null; @endphp
                                @if($mm && (($mm['real'] ?? 0) > 0 || $hayMeta))
                                    @php $real = (float)($mm['real'] ?? 0); $diff = $mm['diff'] ?? 0; $realOtras = (float)($mm['real_otras'] ?? 0); $realSede = $real - $realOtras; @endphp
                                    <tr>
                                        <td class="fw-semibold fs-12">{{ $mesMeta[$m - 1] }} <span class="text-muted">({{ str_pad($m, 2, '0', STR_PAD_LEFT) }})</span></td>
                                        <td class="text-center fs-12">{{ $hayMeta ? number_format($metaMensual, 0, ',', '.') : '—' }}</td>
                                        <td class="text-center fs-12">{{ number_format($realSede, 1, ',', '.') }}</td>
                                        <td class="text-center fs-12">
                                            @if($realOtras > 0)
                                                <span class="badge bg-soft-purple text-purple fs-11" style="color:#8b5cf6;">{{ number_format($realOtras, 1, ',', '.') }} pts</span>
                                            @else
                                                <span class="text-muted">0.0</span>
                                            @endif
                                        </td>
                                        <td class="text-center fs-12">{{ number_format($real, 1, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if(!$hayMeta)
                                                <span class="text-muted fs-12">—</span>
                                            @elseif($real >= $metaMensual)
                                                <span class="badge bg-soft-success text-success fs-11">Cumplida (+{{ number_format($real - $metaMensual, 1, ',', '.') }})</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger fs-11">Faltó ({{ number_format($metaMensual - $real, 1, ',', '.') }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Chart: Inscripciones por Mes --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body pb-0">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#405189;font-size:1rem;" class="ri-bar-chart-grouped-line"></i> Inscripciones por Mes
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">Gestión <strong>{{ $gestionFiltro }}</strong></span>
                    @endif
                </div>
                <div id="chart-asesor-meses" style="height:340px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Desglose de inscritos por tipo --}}
@php
    $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $inscritosTipoTotal = collect($inscritosTipoSeries)->sum(fn($s) => array_sum($s['data']));
@endphp
@if($inscritosTipoTotal > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body pb-0">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#405189;font-size:1rem;" class="ri-price-tag-3-line"></i> Inscritos por Tipo
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;"><strong>{{ $inscritosTipoTotal }}</strong> inscrito(s)</span>
                    @endif
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(range(1, 12) as $m)
                        @php $idx = $m - 1; @endphp
                        @if(collect($inscritosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                            <div style="padding:6px 10px;background:#f8f9fc;border-radius:8px;border:1px solid #eef0f4;min-width:140px;flex:1;">
                                <div style="font-size:.7rem;font-weight:700;color:#495057;margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                                @foreach($inscritosTipoSeries as $serie)
                                    @if(($serie['data'][$idx] ?? 0) > 0)
                                        <div style="font-size:.68rem;display:flex;justify-content:space-between;align-items:center;padding:1px 0;">
                                            <span style="display:flex;align-items:center;gap:4px;">
                                                <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                                <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                            </span>
                                            <span style="font-weight:700;color:#212529;">{{ $serie['data'][$idx] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Gráfico de puntos por mes --}}
@php $puntosTipoTotal = collect($puntosTipoSeries)->sum(fn($s) => array_sum($s['data'])); @endphp
@if($puntosTipoTotal > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body pb-0">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#8b5cf6;font-size:1rem;" class="ri-star-line"></i> Puntos por Mes
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">Total: <strong>{{ number_format($puntosTipoTotal, 1, ',', '.') }}</strong> pts</span>
                    @endif
                </div>
                <div id="chart-asesor-puntos" style="height:300px;"></div>
                <div style="margin-top:.75rem;display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(range(1, 12) as $m)
                        @php $idx = $m - 1; @endphp
                        @if(collect($puntosTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                            <div style="padding:6px 10px;background:#f8f9fc;border-radius:8px;border:1px solid #eef0f4;min-width:140px;flex:1;">
                                <div style="font-size:.7rem;font-weight:700;color:#495057;margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                                @foreach($puntosTipoSeries as $serie)
                                    @if(($serie['data'][$idx] ?? 0) > 0)
                                        <div style="font-size:.68rem;display:flex;justify-content:space-between;align-items:center;padding:1px 0;">
                                            <span style="display:flex;align-items:center;gap:4px;">
                                                <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                                <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                            </span>
                                            <span style="font-weight:700;color:#212529;">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Gráfico de puntos reales por mes --}}
@php $puntosRealesTotal = collect($puntosRealesTipoSeries)->sum(fn($s) => array_sum($s['data'])); @endphp
@if($puntosRealesTotal > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body pb-0">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#0ab39c;font-size:1rem;" class="ri-checkbox-circle-line"></i> Puntos Reales por Mes
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">Total: <strong>{{ number_format($puntosRealesTotal, 1, ',', '.') }}</strong> pts</span>
                    @endif
                </div>
                <div id="chart-asesor-puntos-reales" style="height:300px;"></div>
                <div style="margin-top:.75rem;display:flex;flex-wrap:wrap;gap:8px;">
@foreach(range(1, 12) as $m)
                            @php $idx = $m - 1; @endphp
                            @if(collect($puntosRealesTipoSeries)->sum(fn($s) => $s['data'][$idx]) > 0)
                                <div style="padding:6px 10px;background:#f8f9fc;border-radius:8px;border:1px solid #eef0f4;min-width:140px;flex:1;">
                                    <div style="font-size:.7rem;font-weight:700;color:#495057;margin-bottom:3px;">{{ $mesesNombres[$idx] }}</div>
                                    @if($gestionFiltro !== '' && !empty($metasAsesor[$m]))
                                        @php $mre = (float)($metasAsesor[$m]['real'] ?? 0); $diffMeta = $metasAsesor[$m]['diff'] ?? 0; @endphp
                                        <div style="font-size:.67rem;font-weight:700;margin-bottom:4px;padding:3px 6px;border-radius:6px;display:flex;justify-content:space-between;align-items:center;background:{{ ($mre >= $metaMensual && $hayMeta) ? '#d1fae5' : '#fee2e2' }};color:{{ ($mre >= $metaMensual && $hayMeta) ? '#0ab39c' : '#f06548' }};">
                                            <span>Meta {{ $hayMeta ? number_format($metaMensual, 0, ',', '.') : '—' }}</span>
                                            <span>{{ $hayMeta ? ($mre >= $metaMensual ? '+' . number_format($mre - $metaMensual, 1, ',', '.') : '-' . number_format($metaMensual - $mre, 1, ',', '.')) : '—' }}</span>
                                        </div>
                                    @endif
                                    @foreach($puntosRealesTipoSeries as $serie)
                                    @if(($serie['data'][$idx] ?? 0) > 0)
                                        <div style="font-size:.68rem;display:flex;justify-content:space-between;align-items:center;padding:1px 0;">
                                            <span style="display:flex;align-items:center;gap:4px;">
                                                <span style="width:8px;height:8px;border-radius:2px;background:{{ $serie['color'] }};display:inline-block;"></span>
                                                <span style="color:#6c757d;">{{ $serie['name'] }}</span>
                                            </span>
                                            <span style="font-weight:700;color:#212529;">{{ number_format($serie['data'][$idx], 1, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Inscripciones de otras sedes --}}
@if(!empty($inscripcionesOtraSede) && count($inscripcionesOtraSede) > 0)
<div class="program-block" style="border:1px solid #e9d5ff;">
    <div class="program-block-header" style="background:linear-gradient(135deg,#faf5ff 0%,#f3e8ff 100%);">
        <div class="d-flex align-items-center gap-2" style="min-width:0;">
            <span class="program-icon" style="background:linear-gradient(135deg,#8b5cf6 0%,#6d28d9 100%);"><i class="ri-building-2-line"></i></span>
            <div style="min-width:0;">
                <h6 class="program-title">Inscripciones de otras sedes</h6>
                <div class="program-meta">
                    <span class="badge" style="background:#8b5cf6;color:#fff;border-radius:999px;padding:.35em .85em;font-size:.7rem;font-weight:700;"><i class="ri-building-2-line me-1"></i>OTRA SEDE</span>
                    @if($gestionFiltro !== '')<span><i class="ri-calendar-line"></i>Gestión {{ $gestionFiltro }}</span>@endif
                    <span><i class="ri-team-line"></i>{{ $statsOtraSede['total'] }} inscrito(s)</span>
                    @if($statsOtraSede['activos'] > 0)<span><i class="ri-check-line"></i>{{ $statsOtraSede['activos'] }} activo(s)</span>@endif
                    @if($statsOtraSede['monto'] > 0)<span><i class="ri-money-dollar-circle-line"></i>Bs {{ number_format($statsOtraSede['monto'], 2, ',', '.') }}</span>@endif
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="modern-table table">
            <thead>
                <tr>
                    <th style="width:42px;" class="text-center">#</th>
                    <th>Participante</th>
                    <th>Programa / Sede</th>
                    <th style="width:95px;" class="text-center">Plan</th>
                    <th style="width:100px;" class="text-center">Estado</th>
                    <th style="width:110px;" class="text-end">Monto</th>
                    <th style="width:150px;">Recibo</th>
                    <th style="width:150px;">Factura</th>
                    <th style="width:140px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inscripcionesOtraSede as $i => $insc)
                @php $iniR = strtoupper(substr($insc->nombres ?? ' ', 0, 1) . substr($insc->apellidos ?? ' ', 0, 1)); @endphp
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 cell-nombre">
                            <div class="mini-avatar" style="background:linear-gradient(135deg,#8b5cf6 0%,#6d28d9 100%);">{{ $iniR }}</div>
                            <div style="min-width:0;">
                                <div class="nombre text-truncate">{{ $insc->apellidos }}, {{ $insc->nombres }}</div>
                                <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $insc->ci }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.78rem;">
                        <div class="fw-semibold">{{ $insc->programa_nombre ?? '—' }}</div>
                        <div class="small text-muted"><i class="ri-building-2-line"></i> {{ $insc->sede_origen ?: 'Otra sede' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="estado-pill {{ (int) $insc->contado === 1 ? 'plan-contado' : 'plan-regular' }}">{{ (int) $insc->contado === 1 ? 'Contado' : 'Regular' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="estado-pill {{ (int) $insc->estado === 1 ? 'est-activo' : 'est-retirado' }}">{{ (int) $insc->estado === 1 ? 'Activo' : 'Retirado' }}</span>
                    </td>
                    <td class="text-end fw-semibold fs-12">Bs {{ number_format($insc->monto_total, 2, ',', '.') }}</td>
                    <td>
                        @if ($insc->recibo_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($insc->recibo_path) }}" target="_blank" class="badge bg-soft-success text-success text-decoration-none" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><i class="ri-checkbox-circle-line me-1"></i>{{ $insc->recibo_original }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($insc->factura_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($insc->factura_path) }}" target="_blank" class="badge bg-soft-primary text-primary text-decoration-none" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><i class="ri-receipt-line me-1"></i>{{ $insc->factura_original }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:.78rem;">
                        <i class="ri-calendar-2-line align-bottom me-1"></i>{{ $insc->fecha_inscripcion ? \Carbon\Carbon::parse($insc->fecha_inscripcion)->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Inscripciones agrupadas por programa --}}
@if($porPrograma->count() > 0)
    @foreach($porPrograma as $programaId => $rows)
    @php $first = $rows->first();
        $activosPrg = $rows->filter(fn($r) => (int)$r->estado === 1 && !(int)($r->baja_operativa ?? 0) && !(int)($r->baja_academica ?? 0))->count();
        $retiradosPrg = $rows->filter(fn($r) => (int)$r->estado === 0 || (int)($r->baja_operativa ?? 0) || (int)($r->baja_academica ?? 0))->count();
        $contadoPrg = $rows->where('contado', 1)->count();
        $regularPrg = $rows->where('contado', 0)->count();
        $totalPrg = $rows->count();
    @endphp
    <div class="program-block">
        <div class="program-block-header">
            <div class="d-flex align-items-center gap-2" style="min-width:0;">
                <span class="program-icon"><i class="ri-booklet-line"></i></span>
                <div style="min-width:0;">
                    <h6 class="program-title text-truncate" title="{{ $first->programa_nombre }}">{{ $first->programa_nombre }}</h6>
                    <div class="program-meta">
                        <span><i class="ri-hashtag"></i>{{ $first->programa_codigo }}</span>
                        @if($first->tipo_nombre)<span>·</span><span><i class="ri-price-tag-3-line"></i>{{ $first->tipo_nombre }}</span>@endif
                        <span>·</span><span><i class="ri-calendar-line"></i>Gestión {{ $first->gestion }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="program-count-badge"><i class="ri-team-line"></i>{{ $totalPrg }} inscrito{{ $totalPrg > 1 ? 's' : '' }}</span>
                @php $puntosPrg = $rows->sum(fn($r) => (float)($r->puntos ?? 0)); @endphp
                @if($puntosPrg > 0)
                    <span style="display:inline-flex;align-items:center;gap:4px;background:#f3e8ff;color:#8b5cf6;border-radius:999px;padding:.35em .85em;font-size:.78rem;font-weight:700;border:1px solid #e9d5ff;">
                        <i class="ri-star-line"></i>{{ number_format($puntosPrg, 1, ',', '.') }} pts
                    </span>
                @endif
                @if($contadoPrg > 0)<span class="estado-pill plan-contado" style="font-size:.68rem;"><i class="ri-bank-card-line"></i>{{ $contadoPrg }} contado</span>@endif
                @if($regularPrg > 0)<span class="estado-pill plan-regular" style="font-size:.68rem;"><i class="ri-funds-line"></i>{{ $regularPrg }} regular</span>@endif
                @if($activosPrg > 0)<span class="estado-pill est-activo" style="font-size:.68rem;"><i class="ri-check-line"></i>{{ $activosPrg }} activo{{ $activosPrg > 1 ? 's' : '' }}</span>@endif
                @if($retiradosPrg > 0)<span class="estado-pill est-retirado" style="font-size:.68rem;"><i class="ri-close-line"></i>{{ $retiradosPrg }} retirado{{ $retiradosPrg > 1 ? 's' : '' }}</span>@endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="modern-table table">
                <thead>
                    <tr>
                        <th style="width: 42px;" class="text-center">#</th>
                        <th>Participante</th>
                        <th style="width: 90px;" class="text-center">Sexo</th>
                        <th style="width: 190px;">Contacto</th>
                        <th style="width: 95px;" class="text-center">Plan</th>
                        <th style="width: 100px;" class="text-center">Estado</th>
                        <th style="width: 200px;">Fecha Inscripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $r)
                        @php
                            $iniR = strtoupper(substr($r->nombres, 0, 1) . substr($r->apellidos, 0, 1));
                            $sexoClass = $r->sexo === 'Mujer' ? 'female' : ($r->sexo === 'Hombre' ? 'male' : 'muted');
                            $sexoIcon = $r->sexo === 'Mujer' ? 'ri-women-line' : ($r->sexo === 'Hombre' ? 'ri-men-line' : 'ri-user-line');
                            $esBajaOperativa = (int)($r->baja_operativa ?? 0);
                            $esBajaAcademica = (int)($r->baja_academica ?? 0);
                            if ($esBajaOperativa) {
                                $estCls = 'est-retirado'; $estTxt = 'Baja Operativa'; $estIcon = 'ri-close-circle-line';
                            } elseif ($esBajaAcademica) {
                                $estCls = 'est-retirado'; $estTxt = 'Baja Académica'; $estIcon = 'ri-close-circle-line';
                            } elseif (!(int)$r->estado) {
                                $estCls = 'est-retirado'; $estTxt = 'Baja Temprana'; $estIcon = 'ri-close-line';
                            } else {
                                $estCls = 'est-activo'; $estTxt = 'Activo'; $estIcon = 'ri-check-line';
                            }
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2 cell-nombre">
                                    <div class="mini-avatar {{ $sexoClass }}">{{ $iniR }}</div>
                                    <div style="min-width:0;">
                                        <div class="nombre text-truncate" title="{{ $r->apellidos }}, {{ $r->nombres }}">{{ $r->apellidos }}, {{ $r->nombres }}</div>
                                        <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $r->ci }}@if($r->departamento) · {{ $r->departamento }}@endif</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($r->sexo)
                                    <span class="estado-pill sexo-{{ $sexoClass }}"><i class="{{ $sexoIcon }}"></i>{{ $r->sexo }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="font-size:.75rem;">
                                @if($r->celular)
                                    <div><i class="ri-phone-line text-muted me-1"></i>{{ $r->celular }}</div>
                                @endif
                                @if($r->correo)
                                    <div class="text-truncate" style="max-width:170px;" title="{{ $r->correo }}"><i class="ri-mail-line text-muted me-1"></i>{{ $r->correo }}</div>
                                @endif
                                @if(!$r->celular && !$r->correo)
                                    <span class="text-muted fst-italic">Sin contacto</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="estado-pill {{ $r->contado ? 'plan-contado' : 'plan-regular' }}">{{ $r->contado ? 'Contado' : 'Regular' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="estado-pill {{ $estCls }}"><i class="{{ $estIcon }}"></i> {{ $estTxt }}</span>
                            </td>
                        <td class="text-muted" style="font-size:.78rem;">
                            <i class="ri-calendar-2-line align-bottom me-1"></i>
                            {{ \Carbon\Carbon::parse($r->fecha_inscripcion)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if($totalPrg > 0)
                <tfoot>
                    <tr style="background:#f8f9fc;border-top:2px solid #eef0f4;">
                        <td class="text-center text-muted fw-bold">{{ $totalPrg }}</td>
                        <td class="fw-bold text-dark" style="font-size:.82rem;">
                            <i class="ri-team-line align-bottom me-1 text-muted"></i>Total {{ $totalPrg }} inscrito{{ $totalPrg > 1 ? 's' : '' }}
                        </td>
                        <td class="text-center">
                            <span style="font-size:.68rem;font-weight:700;color:#878a99;">
                                <i class="ri-men-line" style="color:#3577f1;"></i> {{ $rows->where('sexo','Hombre')->count() }}
                                <span class="mx-1">·</span>
                                <i class="ri-women-line" style="color:#ec4899;"></i> {{ $rows->where('sexo','Mujer')->count() }}
                            </span>
                        </td>
                        <td style="font-size:.75rem;color:#878a99;">
                            @php
                                $conCel = $rows->whereNotNull('celular')->where('celular','!=','')->count();
                                $conCor = $rows->whereNotNull('correo')->where('correo','!=','')->count();
                            @endphp
                            <i class="ri-phone-line"></i> {{ $conCel }} <span class="mx-1">·</span> <i class="ri-mail-line"></i> {{ $conCor }}
                        </td>
                        <td class="text-center">
                            <span class="fw-bold" style="font-size:.72rem;color:#0ab39c;">{{ $contadoPrg }} C</span>
                            <span class="mx-1" style="color:#d6dbe0;">/</span>
                            <span class="fw-bold" style="font-size:.72rem;color:#405189;">{{ $regularPrg }} R</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold" style="font-size:.72rem;color:#0ab39c;">{{ $activosPrg }} A</span>
                            <span class="mx-1" style="color:#d6dbe0;">/</span>
                            <span class="fw-bold" style="font-size:.72rem;color:#f06548;">{{ $retiradosPrg }} R</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endforeach
@else
<div class="empty-state">
    <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">Sin inscripciones</h5>
    <p class="text-muted mb-0">No se encontraron inscripciones para los filtros seleccionados.</p>
</div>
@endif

{{-- Gastos de Autogestión por Programa (chart) --}}
@if(isset($soloAutoAsesor) && $soloAutoAsesor->isNotEmpty())
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#f59e0b;font-size:1rem;" class="ri-hand-coin-line"></i> Gastos de Autogestión por Programa
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">{{ $mesFiltro ? \Carbon\Carbon::create()->month((int)$mesFiltro)->locale('es')->translatedFormat('F') : 'Todos' }} <strong>{{ $gestionFiltro }}</strong></span>
                    @endif
                </div>
                <div id="chart-auto-asesor" style="height:420px;"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Total Invertido por Programa (Asignado + Autogestión) — chart --}}
@if(isset($programasResumenAsesor) && $programasResumenAsesor->isNotEmpty())
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(64,81,137,.18);">
            <div class="card-body">
                <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#495057;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px dashed #eef0f4;">
                    <i style="color:#405189;font-size:1rem;" class="ri-bar-chart-grouped-line"></i> Total Invertido por Programa (Asignado + Autogestión)
                    @if($gestionFiltro !== '')
                        <span class="text-muted ms-auto" style="font-size:.72rem;text-transform:none;letter-spacing:0;">{{ $mesFiltro ? \Carbon\Carbon::create()->month((int)$mesFiltro)->locale('es')->translatedFormat('F') : 'Todos' }} <strong>{{ $gestionFiltro }}</strong></span>
                    @endif
                </div>
                <div id="chart-total-asesor" style="height:420px;"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Comprobantes de Publicidad --}}
@php
    $mesesPub = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
    $c = $comprobantesAsesor ?? collect();
@endphp
@if($c->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="pub-comp-card">
            <div class="pub-comp-head">
                <span class="pc-ic"><i class="ri-bill-line"></i></span>
                <div style="min-width:0;">
                    <p class="pc-title">Mis Comprobantes de Publicidad</p>
                    <p class="pc-sub">Cargas de publicidad del asesor · {{ $c->count() }} comprobante(s)</p>
                </div>
                @can('marketing.publicidad.subir')
                <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#modalSubirAsesor">
                    <i class="ri-upload-2-line me-1"></i> Subir comprobante
                </button>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table tabla-pub mb-0" id="comprobantes-asesor">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:36px;">#</th>
                            <th>Inicio / Fin</th>
                            <th>Presupuesto</th>
                            <th>Descripción / Campaña</th>
                            <th class="text-center" style="width:90px;">Plataforma</th>
                            <th class="text-center" style="width:110px;">Programa</th>
                            <th class="text-end" style="width:95px;">Monto</th>
                            <th class="text-center" style="width:80px;">Archivo</th>
                            <th class="text-center" style="width:100px;">Estado</th>
                            <th class="text-center" style="width:110px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="comprobantes-body">
                        <tr><td colspan="10" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary me-1"></div>Cargando comprobantes...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal de Subir Comprobante desde el perfil --}}
@can('marketing.publicidad.subir')
<div class="modal fade" id="modalSubirAsesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form class="form-subir-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="asesor_id" value="{{ $asesor->id }}">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-upload-cloud-2-line me-1"></i>Subir comprobante</h5>
                        <small class="text-muted">{{ $asesor->apellidos }}, {{ $asesor->nombres }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info fs-12 py-2 mb-3">
                        <i class="ri-information-line me-1"></i> Selecciona la asignación presupuestaria contra la cual cargar este comprobante.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Asignación presupuestaria <span class="text-danger">*</span></label>
                            <select name="presupuesto_id" class="form-select validate-required" required>
                                <option value="">— Seleccionar presupuesto —</option>
                                @foreach($presupuestosAsesor as $pa)
                                    <option value="{{ $pa->id }}">
                                        Q{{ $pa->quincena }} {{ $mesesPub[$pa->mes] ?? $pa->mes }} {{ $pa->gestion }} — Bs {{ number_format($pa->monto_asignado, 2) }}
                                        @if($pa->supervisor_id == $asesor->id) (Supervisor) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Seleccione una asignación presupuestaria.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control validate-required" required>
                            <div class="invalid-feedback">La fecha de inicio es obligatoria.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_fin" class="form-control validate-required validate-after" required>
                            <div class="invalid-feedback">La fecha de fin debe ser igual o posterior a la de inicio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control validate-required" placeholder="0.00" required>
                            <div class="invalid-feedback">Ingrese un monto válido mayor a 0.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plataforma</label>
                            <select name="plataforma" class="form-select">
                                <option value="Facebook">Facebook Ads</option>
                                <option value="Instagram">Instagram Ads</option>
                                <option value="TikTok">TikTok Ads</option>
                                <option value="Google">Google Ads</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre de la campaña</label>
                            <input type="text" name="campana_nombre" class="form-control" placeholder="Ej: Diplomado en Auditoría - Julio">
                        </div>
                            <div class="col-md-12">
                                <label class="form-label">Enlace de la campaña</label>
                                <input type="url" name="enlace_campana" class="form-control validate-url" placeholder="https://www.facebook.com/ads/...">
                                <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Programa promocionado</label>
                                <select name="programa_id" class="form-select">
                                    <option value="">— Sin programa —</option>
                                    @foreach($programas ?? collect() as $prg)
                                        <option value="{{ $prg->id }}">{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Página de Facebook</label>
                                <input type="url" name="pagina_facebook" class="form-control validate-url" placeholder="https://www.facebook.com/tupagina">
                                <div class="invalid-feedback">Ingrese una URL válida (opcional).</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Descripción del gasto <span class="text-danger">*</span></label>
                                <textarea name="descripcion" rows="2" class="form-control validate-required" placeholder="Ej: Publicación pagada Facebook 3 días para promoción del diplomado" required></textarea>
                                <div class="invalid-feedback">La descripción es obligatoria.</div>
                            </div>
                        <div class="col-md-12">
                            <label class="form-label">Comprobante (PDF o imagen, máx 5MB) <span class="text-danger">*</span></label>
                            <input type="file" name="archivo" class="form-control validate-file" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                            <div class="invalid-feedback">Seleccione un archivo PDF o imagen (máx 5MB).</div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-danger d-none" id="error-global-asesor"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-2 w-100 justify-content-between">
                        <div class="spinner-border spinner-border-sm d-none text-success" role="status" id="spinner-asesor">
                            <span class="visually-hidden">Subiendo...</span>
                        </div>
                        <div class="d-flex gap-2 ms-auto">
                            <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success btn-subir"><i class="ri-upload-2-line me-1"></i>Subir comprobante</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modales de verificación por comprobante --}}
@can('marketing.publicidad.verificar')
@foreach($c as $comp)
<div class="modal fade modal-verif" id="modalVerifAsesor{{ $comp->id }}" tabindex="-1" aria-hidden="true" data-comprobante-id="{{ $comp->id }}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form class="form-verificar-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.verificar', $comp->id) }}" novalidate>
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-verified-badge-line me-1"></i>Verificar comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:#f8f9fc;border:1px solid #eef0f4;">
                                <div class="fs-12 mb-2"><strong>Detalle del comprobante</strong></div>
                                <table class="fs-12" style="width:100%;">
                                    <tr><td class="text-muted" style="width:90px;">Monto:</td><td class="fw-bold">Bs {{ number_format($comp->monto, 2) }}</td></tr>
                                    <tr><td class="text-muted">Descripción:</td><td>{{ $comp->descripcion }}</td></tr>
                                    <tr><td class="text-muted">Plataforma:</td><td>{{ $comp->plataforma }}</td></tr>
                                    @if($comp->campana_nombre)
                                    <tr><td class="text-muted">Campaña:</td><td>{{ $comp->campana_nombre }}</td></tr>
                                    @endif
                                    <tr><td class="text-muted">Programa:</td><td>{{ $comp->programa_nombre ?? '—' }}</td></tr>
                                    @if($comp->pagina_facebook)
                                    <tr><td class="text-muted">Facebook:</td><td><a href="{{ $comp->pagina_facebook }}" target="_blank" rel="noopener"><i class="ri-facebook-line"></i> Abrir</a></td></tr>
                                    @endif
                                    <tr><td class="text-muted">Periodo:</td><td>
                                        @php
                                            $fiV = \Carbon\Carbon::parse($comp->fecha_inicio)->locale('es');
                                            $ffV = \Carbon\Carbon::parse($comp->fecha_fin)->locale('es');
                                        @endphp
                                        {{ $fiV->isoFormat('D [de] MMM [del] YYYY') }} — {{ $ffV->isoFormat('D [de] MMM [del] YYYY') }}
                                    </td></tr>
                                    <tr><td class="text-muted">Estado:</td>
                                        <td>
                                            @if($comp->estado === 'pendiente')
                                                <span class="badge bg-soft-warning text-warning">Pendiente</span>
                                            @elseif($comp->estado === 'aprobado')
                                                <span class="badge bg-soft-success text-success">Aprobado</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">Rechazado</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Resultado <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="estado" value="aprobado" id="est-aprobar-{{ $comp->id }}" @checked($comp->estado === 'aprobado') required>
                                        <label class="form-check-label fw-semibold text-success" for="est-aprobar-{{ $comp->id }}">Aprobar</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="estado" value="rechazado" id="est-rechazar-{{ $comp->id }}" @checked($comp->estado === 'rechazado') required>
                                        <label class="form-check-label fw-semibold text-danger" for="est-rechazar-{{ $comp->id }}">Rechazar</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback d-block" id="error-estado-{{ $comp->id }}"></div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Observaciones (opcional)</label>
                                <textarea name="observaciones_contable" rows="3" class="form-control" placeholder="Detalle de la verificación...">{{ $comp->observaciones_contable }}</textarea>
                            </div>
                            <div class="alert alert-danger d-none mt-2 error-verif" id="error-verif-asesor-{{ $comp->id }}"></div>
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-soft-secondary flex-fill" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary flex-fill btn-guardar-verif">
                                    <i class="ri-save-3-line me-1"></i>Guardar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comprobante</label>
                            <div class="p-2 rounded bg-light text-center" style="min-height:300px;border:1px solid #eef0f4;">
                                @php
                                    $extVerif = strtolower(pathinfo($comp->archivo_original, PATHINFO_EXTENSION));
                                    $urlVerif = asset('storage/'.$comp->archivo_path);
                                @endphp
                                @if(in_array($extVerif, ['jpg','jpeg','png','webp']))
                                    <img src="{{ $urlVerif }}" class="img-fluid" style="max-height:500px;object-fit:contain;" alt="Comprobante">
                                @elseif($extVerif === 'pdf')
                                    <iframe src="{{ $urlVerif }}" class="w-100" style="height:500px;border:none;"></iframe>
                                @else
                                    <div class="py-5"><i class="ri-file-download-line ri-2x"></i><p class="mt-2"><a href="{{ $urlVerif }}" target="_blank" class="btn btn-primary"><i class="ri-download-line me-1"></i>Descargar archivo</a></p></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endcan
