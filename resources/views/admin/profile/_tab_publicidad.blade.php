<div class="tab-toolbar">
    <div>
        <p class="tb-title"><i class="ri-shopping-cart-2-line"></i> Presupuesto de publicidad</p>
        <p class="tb-sub">Asignado, gastado, aprobado y comprobantes por periodo.</p>
    </div>
</div>

{{-- Mensajes de registro de gastos de autogestión (éxito, error de subida o validación) --}}
@if(session('publicidad_success'))
<div class="alert alert-success alert-dismissible fade show fs-12" role="alert">
    <i class="ri-check-double-line me-1"></i>{{ session('publicidad_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('publicidad_error'))
<div class="alert alert-danger alert-dismissible fade show fs-12" role="alert">
    <i class="ri-error-warning-line me-1"></i>{{ session('publicidad_error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show fs-12" role="alert">
    <i class="ri-error-warning-line me-1"></i>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
    $totalAsignado = $presupuestos->sum('monto_asignado');
    $totalGastado = $presupuestos->sum('total_gastado');
    $totalAprobado = $presupuestos->sum('total_aprobado');
    $totalPendiente = $presupuestos->sum('total_pendiente');
    $totalComprobantes = $comprobantes->count();

    // Index derived presupuestos by their origen_id for quick lookup
    $derivadosPorOrigen = $presupuestosDerivados->groupBy('presupuesto_origen_id');

    // Group ALL presupuestos (own + derived) by gestion-mes-quincena
    $periodos = collect();
    foreach ($presupuestos as $p) {
        $key = $p->gestion . '-' . $p->mes . '-' . $p->quincena;
        if (!$periodos->has($key)) {
            $periodos->put($key, (object) [
                'gestion' => $p->gestion,
                'mes' => $p->mes,
                'quincena' => $p->quincena,
                'total_asignado' => 0,
                'total_gastado' => 0,
                'total_aprobado' => 0,
                'total_pendiente' => 0,
                'total_comprobantes' => 0,
                'total_presupuestos' => 0,
                'items' => collect(),
                'derivados' => collect(),
                'periodo_cerrado' => $p->periodo_cerrado ?? false,
            ]);
        }
        $per = $periodos->get($key);
        $per->total_asignado += (float) $p->monto_asignado;
        $per->total_gastado += (float) $p->total_gastado;
        $per->total_aprobado += (float) $p->total_aprobado;
        $per->total_pendiente += (float) $p->total_pendiente;
        $per->total_comprobantes += (int) ($p->total_comprobantes ?? 0);
        $per->total_presupuestos++;
        $per->items->push($p);
        // Add derived presupuestos for this period
        $derivados = $derivadosPorOrigen->get($p->id, collect());
        foreach ($derivados as $d) {
            $per->derivados->push($d);
            $per->total_gastado += (float) $d->total_gastado;
            $per->total_aprobado += (float) $d->total_aprobado;
            $per->total_pendiente += (float) $d->total_pendiente;
            $per->total_comprobantes += (int) ($d->total_comprobantes ?? 0);
        }
        // Keep periodo_cerrado from own presupuesto
        if (!($p->periodo_cerrado ?? false)) {
            $per->periodo_cerrado = false;
        }
    }

    // Group comprobantes by gestion-mes-quincena
    $comprobantesPorPeriodo = collect();
    foreach ($comprobantes as $c) {
        $ckey = ($c->gestion ?? $c->presupuesto->gestion ?? $presupuestoActual->gestion ?? now()->year) . '-'
              . ($c->mes ?? $c->presupuesto->mes ?? $presupuestoActual->mes ?? now()->month) . '-'
              . ($c->quincena ?? $c->presupuesto->quincena ?? $presupuestoActual->quincena ?? (now()->day <= 15 ? 1 : 2));
        if (!$comprobantesPorPeriodo->has($ckey)) {
            $comprobantesPorPeriodo->put($ckey, collect());
        }
        $comprobantesPorPeriodo->get($ckey)->push($c);
    }
@endphp

{{-- KPIs --}}
<div class="row g-2 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#405189;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Asignado</div><div class="k-val">Bs {{ number_format($totalAsignado, 2) }}</div><div class="k-sub">presupuesto del periodo</div></div>
                <span class="k-ic" style="--k-bg:#eef1f8; --k-color:#405189;"><i class="ri-wallet-3-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#0ab39c;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Gastado</div><div class="k-val">Bs {{ number_format($totalGastado, 2) }}</div><div class="k-sub">según comprobantes</div></div>
                <span class="k-ic" style="--k-bg:#ecfdf5; --k-color:#0ab39c;"><i class="ri-money-dollar-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#299cdb;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Aprobado</div><div class="k-val">Bs {{ number_format($totalAprobado, 2) }}</div><div class="k-sub">verificado</div></div>
                <span class="k-ic" style="--k-bg:#f0f9ff; --k-color:#299cdb;"><i class="ri-checkbox-circle-line"></i></span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="kpi" style="--k-accent:#f7b84b;">
            <div class="d-flex align-items-center justify-content-between">
                <div><div class="k-lbl">Comprobantes</div><div class="k-val">{{ $totalComprobantes }}</div><div class="k-sub">{{ $comprobantes->where('estado', 'pendiente')->count() }} pendientes</div></div>
                <span class="k-ic" style="--k-bg:#fffbeb; --k-color:#f7b84b;"><i class="ri-receipt-line"></i></span>
            </div>
        </div>
    </div>
</div>

{{-- Sub-tabs de navegación --}}
<div class="profile-tabs-wrap mb-3">
<ul class="nav profile-tabs" id="pubSubTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pub-resumen-tab" data-bs-toggle="tab" data-bs-target="#pub-resumen" type="button" role="tab">
            <i class="ri-bar-chart-grouped-line"></i> Resumen
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pub-presupuestos-tab" data-bs-toggle="tab" data-bs-target="#pub-presupuestos" type="button" role="tab">
            <i class="ri-wallet-3-line"></i> Presupuestos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pub-comprobantes-tab" data-bs-toggle="tab" data-bs-target="#pub-comprobantes" type="button" role="tab">
            <i class="ri-receipt-line"></i> Comprobantes
            @if($todosComprobantes->isNotEmpty())
            <span class="tab-count">{{ $todosComprobantes->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pub-autogestion-tab" data-bs-toggle="tab" data-bs-target="#pub-autogestion" type="button" role="tab">
            <i class="ri-hand-coin-line"></i> Autogestión
        </button>
    </li>
</ul>
</div>

<div class="tab-content" id="pubSubTabsContent">

{{-- TAB 1: RESUMEN POR PERIODO --}}
<div class="tab-pane fade show active" id="pub-resumen" role="tabpanel">
    @if($periodosData->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
        <p class="mb-0">No hay presupuestos asignados para mostrar.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table tabla-pub" style="width:100%;">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Quincena</th>
                    <th class="text-end">Asignado</th>
                    <th class="text-end">Gastado</th>
                    <th class="text-end">Aprobado</th>
                    <th class="text-end">Pendiente</th>
                    <th class="text-center">Comprob.</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodosData as $per)
                @php
                    $porc = $per->total_asignado > 0 ? min(100, round(($per->total_gastado / $per->total_asignado) * 100, 1)) : 0;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $meses[$per->mes] ?? $per->mes }} {{ $per->gestion }}</td>
                    <td>
                        <span class="badge {{ $per->quincena === 1 ? 'badge-q1' : 'badge-q2' }} fs-10 px-2 py-1 rounded-pill">
                            Q{{ $per->quincena }}
                        </span>
                    </td>
                    <td class="text-end fw-semibold">Bs {{ number_format($per->total_asignado, 2) }}</td>
                    <td class="text-end">Bs {{ number_format($per->total_gastado, 2) }}</td>
                    <td class="text-end text-success fw-semibold">Bs {{ number_format($per->total_aprobado, 2) }}</td>
                    <td class="text-end text-warning">Bs {{ number_format($per->total_pendiente, 2) }}</td>
                    <td class="text-center">{{ $per->total_comprobantes }}</td>
                    <td class="text-center">
                        @if($per->periodo_cerrado)
                            <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                        @else
                            <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- TAB 2: PRESUPUESTOS CON COMPROBANTES (vista actual) --}}
<div class="tab-pane fade" id="pub-presupuestos" role="tabpanel">
    @if($periodos->isEmpty() && $presupuestosOrigen->isEmpty() && !$esPrincipal)
    <div class="text-center text-muted py-4">
        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
        <p class="mb-0">Aún no tienes presupuestos de publicidad asignados.</p>
        <small>Cuando te asignen un presupuesto, podrás cargar comprobantes de gastos aquí.</small>
    </div>
    @else
    <div class="info-card mb-4" style="height:auto;">
        @foreach($periodos->sortByDesc(function($p) { return $p->gestion * 100 + $p->mes * 10 + $p->quincena; }) as $per)
        @php
            $pkey = $per->gestion . '-' . $per->mes . '-' . $per->quincena;
            $compsDelPeriodo = $comprobantesPorPeriodo->get($pkey, collect());
            $porc = $per->total_asignado > 0 ? min(100, round(($per->total_gastado / $per->total_asignado) * 100, 1)) : 0;
            $primerPresupuesto = $per->items->first();
            $tieneDerivados = $per->derivados->isNotEmpty();
        @endphp
        <div class="periodo-card mb-3" style="border:1px solid #eef0f4; border-radius:12px; overflow:hidden;">
            {{-- Encabezado del periodo --}}
            <div class="periodo-header d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2" style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe);border-left:4px solid #405189;cursor:pointer;">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="period-icon"><i class="ri-calendar-line"></i></span>
                    <strong class="fs-14">{{ $meses[$per->mes] ?? $per->mes }} {{ $per->gestion }}</strong>
                    <span class="badge {{ $per->quincena === 1 ? 'badge-q1' : 'badge-q2' }} fs-11 px-3 py-2 rounded-pill">
                        Q{{ $per->quincena }} ({{ $per->quincena === 1 ? '1-15' : '16-fin' }})
                    </span>
                    @if($porc > 0)
                    <div class="bar-progress {{ $porc > 100 ? 'warn' : '' }} ms-1">
                        <span style="width:{{ $porc }}%;"></span>
                    </div>
                    <span class="text-muted fs-11">{{ $porc }}%</span>
                    @endif
                    @if($tieneDerivados)
                    <span class="badge" style="background:#fef3c7;color:#92400e;font-size:.65rem;font-weight:600;padding:.3em .7em;"><i class="ri-share-forward-line"></i> {{ $per->derivados->count() }} derivado(s)</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12">
                        <strong>Bs {{ number_format($per->total_asignado, 2) }}</strong> asignado
                    </span>
                    @if($per->periodo_cerrado)
                        <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                    @else
                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                    @endif
                    @can('marketing.publicidad.subir')
                    @if($per->items->where('estado', 'activo')->isNotEmpty() && !$per->periodo_cerrado)
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalPubSubir"
                            data-presupuesto-id="{{ $primerPresupuesto->id }}"
                            data-periodo="{{ $meses[$per->mes] }} {{ $per->gestion }}"
                            data-gestion="{{ $per->gestion }}"
                            data-mes="{{ $per->mes }}"
                            data-quincena="{{ $per->quincena }}"
                            data-monto="{{ $primerPresupuesto->monto_asignado }}">
                        <i class="ri-upload-2-line me-1"></i>Subir comprobante
                    </button>
                    @endif
                    @endcan
                    @can('marketing.publicidad.presupuestos')
                    @if($esPrincipal && $per->items->where('estado', 'activo')->whereNull('presupuesto_origen_id')->isNotEmpty() && !$per->periodo_cerrado)
                    @php $presupuestoParaDerivar = $per->items->where('estado', 'activo')->whereNull('presupuesto_origen_id')->first(); @endphp
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalPubDerivar"
                            data-presupuesto-id="{{ $presupuestoParaDerivar->id }}"
                            data-periodo="{{ $meses[$per->mes] }} {{ $per->gestion }}"
                            data-gestion="{{ $per->gestion }}"
                            data-mes="{{ $per->mes }}"
                            data-quincena="{{ $per->quincena }}"
                            data-disponible="{{ $presupuestoParaDerivar->monto_asignado }}">
                        <i class="ri-share-forward-line me-1"></i>Derivar
                    </button>
                    @endif
                    @endcan
                    <span class="periodo-toggle" style="color:#878a99;font-size:1.1rem;transition:transform .2s;">
                        <i class="ri-arrow-down-s-line"></i>
                    </span>
                </div>
            </div>

            {{-- Detalle del periodo (comprobantes + derivaciones) --}}
            <div class="periodo-detalle" style="display:none;">
                @php
                    $misPresupuestoIds = $per->items->pluck('id');
                    $derivadosPresupuestoIds = $per->derivados->pluck('id');
                    $compsPorPresupuesto = $compsDelPeriodo->groupBy('presupuesto_id');

                    $misComps = collect();
                    foreach ($misPresupuestoIds as $pid) {
                        if ($compsPorPresupuesto->has($pid)) {
                            $misComps = $misComps->merge($compsPorPresupuesto->get($pid));
                        }
                    }

                    $compsPorDerivado = collect();
                    foreach ($per->derivados as $d) {
                        $compsDelDerivado = $compsPorPresupuesto->get($d->id, collect());
                        $compsPorDerivado->push((object)[
                            'presupuesto' => $d,
                            'comprobantes' => $compsDelDerivado,
                        ]);
                    }
                @endphp

                @if($misComps->isNotEmpty())
                <div class="px-3 py-2" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;">
                    <div class="d-flex align-items-center gap-2 fs-12 fw-semibold text-success">
                        <i class="ri-user-star-line"></i> Mis comprobantes
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table tabla-pub mb-0" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:36px;">#</th>
                                <th>Descripción</th>
                                <th class="text-center">Plataf.</th>
                                <th class="text-center">Programa</th>
                                <th class="text-end" style="width:95px;">Monto</th>
                                <th class="text-center" style="width:70px;">Archivo</th>
                                <th class="text-center" style="width:90px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($misComps as $idx => $c)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                <td class="fs-12">
                                    <div>{{ $c->descripcion }}</div>
                                    @if($c->campana_nombre)
                                        <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $c->campana_nombre }}</div>
                                    @endif
                                    @if(!empty($c->observaciones_contable))
                                        <div class="mt-1 p-1 rounded" style="background:#fff8e1;color:#92400e;font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $c->observaciones_contable }}</div>
                                    @endif
                                    <div class="text-muted fs-11 mt-1">
                                        @php
                                            $fi = \Carbon\Carbon::parse($c->fecha_inicio)->locale('es');
                                            $ff = \Carbon\Carbon::parse($c->fecha_fin)->locale('es');
                                        @endphp
                                        {{ $fi->isoFormat('D MMM') }} al {{ $ff->isoFormat('D MMM') }}
                                    </div>
                                </td>
                                <td class="text-center fs-11">{{ $c->plataforma }}</td>
                                <td class="text-center fs-11">{{ empty($c->programa_nombre) ? '—' : $c->programa_nombre }}</td>
                                <td class="text-end fw-bold">
                                    @if($c->estado === 'aprobado_obs' && $c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                                        @php $montante = (float)$c->monto_aprobado; $orig = (float)$c->monto; @endphp
                                        <span class="text-decoration-line-through text-muted fs-11">Bs {{ number_format($orig, 2) }}</span><br>
                                        Bs {{ number_format($montante, 2) }}
                                    @else
                                        Bs {{ number_format($c->monto, 2) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div><span class="badge bg-primary px-1 py-0" style="font-size:.62rem!important;"><i class="ri-attachment-2"></i> Comp.</span>
                                    <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_path) }}" title="{{ $c->archivo_original }}">
                                        <i class="ri-attachment-2"></i> Ver
                                    </a></div>
                                    @if($c->archivo_debito_path)
                                    <div class="mt-1"><span class="badge bg-warning text-dark px-1 py-0" style="font-size:.62rem!important;"><i class="ri-bank-card-line"></i> Débito</span>
                                    <a href="javascript:void(0)" class="file-preview btn-preview-file fs-11 text-warning" data-url="{{ asset('storage/'.$c->archivo_debito_path) }}" title="{{ $c->archivo_debito_original }}">
                                        <i class="ri-bank-card-line"></i> Ver
                                    </a></div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($c->estado === 'pendiente')
                                        <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1">Pendiente</span>
                                    @elseif($c->estado === 'aprobado')
                                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Aprobado</span>
                                        @if($c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                                        <div class="fs-11 text-success mt-1"><i class="ri-check-line"></i> Bs {{ number_format($c->monto_aprobado, 2) }}</div>
                                        @endif
                                    @elseif($c->estado === 'aprobado_obs')
                                        <span class="badge bg-soft-info text-info fs-10 px-2 py-1">Aprobado</span>
                                        <div class="fs-11 text-info mt-1">
                                            <i class="ri-check-double-line"></i> Bs {{ number_format($c->monto_aprobado ?? $c->monto, 2) }}
                                            @if($c->observaciones_contable)
                                            <br><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Rechazado</span>
                                        @if($c->observaciones_contable)
                                        <div class="fs-11 text-danger mt-1"><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}</div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif(!$tieneDerivados)
                <div class="text-center text-muted py-3 fs-12">
                    <i class="ri-inbox-2-line me-1"></i>Sin comprobantes en este periodo
                </div>
                @endif

                @foreach($compsPorDerivado as $item)
                @php $d = $item->presupuesto; $compsDerivado = $item->comprobantes; @endphp
                <div class="px-3 py-2" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border-bottom:1px solid #fde68a;border-top:1px solid #fde68a;">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span style="display:inline-flex;align-items:center;gap:5px;font-weight:700;color:#92400e;font-size:.82rem;">
                            <i class="ri-share-forward-line" style="color:#d97706;"></i> Derivado a: {{ $d->ase_apellidos }}, {{ $d->ase_nombres }}
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:4px;background:#fff;border:1px solid #fde68a;border-radius:6px;padding:.2rem .6rem;font-size:.75rem;font-weight:600;color:#b45309;">
                            Bs {{ number_format($d->monto_asignado, 2) }}
                        </span>
                        @if($d->observaciones)
                        <span style="color:#878a99;font-size:.72rem;" title="{{ $d->observaciones }}"><i class="ri-chat-4-line"></i> {{ $d->observaciones }}</span>
                        @endif
                        @if($compsDerivado->isNotEmpty())
                        <span class="badge bg-soft-info text-info fs-10 px-2 py-1 ms-auto">{{ $compsDerivado->count() }} comprobante(s)</span>
                        @endif
                        @if($d->archivo_derivacion_path)
                        <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-soft-info file-preview btn-preview-file" data-url="{{ asset('storage/'.$d->archivo_derivacion_path) }}" title="{{ $d->archivo_derivacion_original }}">
                            <i class="ri-attachment-2"></i>
                        </a>
                        @endif
                        @can('marketing.publicidad.presupuestos')
                        @if($compsDerivado->count() === 0)
                        <button type="button" class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="modal" data-bs-target="#modalEditarDerivacion{{ $d->id }}" title="Editar derivación">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        @endif
                        @endcan
                    </div>
                </div>
                @if($compsDerivado->isNotEmpty())
                <div class="table-responsive">
                    <table class="table tabla-pub mb-0" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:36px;">#</th>
                                <th>Descripción</th>
                                <th class="text-center">Plataf.</th>
                                <th class="text-center">Programa</th>
                                <th class="text-end" style="width:95px;">Monto</th>
                                <th class="text-center" style="width:70px;">Archivo</th>
                                <th class="text-center" style="width:90px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compsDerivado as $idx => $c)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                <td class="fs-12">
                                    <div>{{ $c->descripcion }}</div>
                                    @if($c->campana_nombre)
                                        <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $c->campana_nombre }}</div>
                                    @endif
                                    @if(!empty($c->observaciones_contable))
                                        <div class="mt-1 p-1 rounded" style="background:#fff8e1;color:#92400e;font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $c->observaciones_contable }}</div>
                                    @endif
                                    <div class="text-muted fs-11 mt-1">
                                        @php
                                            $fi = \Carbon\Carbon::parse($c->fecha_inicio)->locale('es');
                                            $ff = \Carbon\Carbon::parse($c->fecha_fin)->locale('es');
                                        @endphp
                                        {{ $fi->isoFormat('D MMM') }} al {{ $ff->isoFormat('D MMM') }}
                                    </div>
                                </td>
                                <td class="text-center fs-11">{{ $c->plataforma }}</td>
                                <td class="text-center fs-11">{{ empty($c->programa_nombre) ? '—' : $c->programa_nombre }}</td>
                                <td class="text-end fw-bold">
                                    @if($c->estado === 'aprobado_obs' && $c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                                        @php $montante = (float)$c->monto_aprobado; $orig = (float)$c->monto; @endphp
                                        <span class="text-decoration-line-through text-muted fs-11">Bs {{ number_format($orig, 2) }}</span><br>
                                        Bs {{ number_format($montante, 2) }}
                                    @else
                                        Bs {{ number_format($c->monto, 2) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div><span class="badge bg-primary px-1 py-0" style="font-size:.62rem!important;"><i class="ri-attachment-2"></i> Comp.</span>
                                    <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_path) }}" title="{{ $c->archivo_original }}">
                                        <i class="ri-attachment-2"></i> Ver
                                    </a></div>
                                    @if($c->archivo_debito_path)
                                    <div class="mt-1"><span class="badge bg-warning text-dark px-1 py-0" style="font-size:.62rem!important;"><i class="ri-bank-card-line"></i> Débito</span>
                                    <a href="javascript:void(0)" class="file-preview btn-preview-file fs-11 text-warning" data-url="{{ asset('storage/'.$c->archivo_debito_path) }}" title="{{ $c->archivo_debito_original }}">
                                        <i class="ri-bank-card-line"></i> Ver
                                    </a></div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($c->estado === 'pendiente')
                                        <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1">Pendiente</span>
                                    @elseif($c->estado === 'aprobado')
                                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Aprobado</span>
                                        @if($c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                                        <div class="fs-11 text-success mt-1"><i class="ri-check-line"></i> Bs {{ number_format($c->monto_aprobado, 2) }}</div>
                                        @endif
                                    @elseif($c->estado === 'aprobado_obs')
                                        <span class="badge bg-soft-info text-info fs-10 px-2 py-1">Aprobado</span>
                                        <div class="fs-11 text-info mt-1">
                                            <i class="ri-check-double-line"></i> Bs {{ number_format($c->monto_aprobado ?? $c->monto, 2) }}
                                            @if($c->observaciones_contable)
                                            <br><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Rechazado</span>
                                        @if($c->observaciones_contable)
                                        <div class="fs-11 text-danger mt-1"><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}</div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-2 fs-12" style="border-bottom:1px solid #eef0f4;">
                    <i class="ri-inbox-2-line me-1"></i>Sin comprobantes cargados por este asesor
                </div>
                @endif
                @endforeach

                @can('marketing.publicidad.presupuestos')
                @foreach($compsPorDerivado as $item)
                @php $d = $item->presupuesto; @endphp
                <div class="modal fade" id="modalEditarDerivacion{{ $d->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.marketing.publicidad.presupuesto.update', $d->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="ri-edit-2-line text-warning me-1"></i>Editar derivación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info fs-12 py-2 mb-3">
                                        <i class="ri-information-line me-1"></i>
                                        Editando derivación de <strong>{{ $d->ase_apellidos ?? '?' }}, {{ $d->ase_nombres ?? '?' }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="monto_asignado" class="form-control" value="{{ $d->monto_asignado }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Respaldo de transferencia</label>
                                        <input type="file" name="archivo_derivacion" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                                        <small class="text-muted fs-11">
                                            @if($d->archivo_derivacion_path)
                                            Actualmente: <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$d->archivo_derivacion_path) }}" title="{{ $d->archivo_derivacion_original }}">{{ $d->archivo_derivacion_original }}</a>
                                            @else
                                            Imagen o PDF (máx. 5 MB)
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning"><i class="ri-save-line me-1"></i>Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @endcan

                <div style="background:#f8f9fc;padding:.5rem 1rem;display:flex;align-items:center;justify-content:flex-end;gap:2rem;font-size:.82rem;border-top:1px solid #eef0f4;">
                    <span>Asignado: <strong>Bs {{ number_format($per->total_asignado, 2) }}</strong></span>
                    <span>Gastado: <strong>Bs {{ number_format($per->total_gastado, 2) }}</strong></span>
                    <span class="text-success">Aprobado: <strong>Bs {{ number_format($per->total_aprobado, 2) }}</strong></span>
                    <span class="text-warning">Pendiente: <strong>Bs {{ number_format($per->total_pendiente, 2) }}</strong></span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- TAB 3: LISTA COMPLETA DE COMPROBANTES --}}
<div class="tab-pane fade" id="pub-comprobantes" role="tabpanel">
    @if($todosComprobantes->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
        <p class="mb-0">No hay comprobantes registrados.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table tabla-pub" id="tablaComprobantes" style="width:100%;">
            <thead>
                <tr>
                    <th class="text-center" style="width:36px;">#</th>
                    <th>Descripción</th>
                    <th class="text-center">Periodo</th>
                    <th class="text-center">Plataf.</th>
                    <th class="text-center">Programa</th>
                    <th>Asesor</th>
                    <th class="text-end" style="width:95px;">Monto</th>
                    <th class="text-center" style="width:70px;">Archivo</th>
                    <th class="text-center" style="width:90px;">Estado</th>
                </tr>
            </thead>
            <tbody id="tbodyComprobantes">
                @foreach($todosComprobantes as $idx => $c)
                <tr>
                    <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                    <td class="fs-12">
                        <div>{{ $c->descripcion }}</div>
                        @if($c->campana_nombre)
                            <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $c->campana_nombre }}</div>
                        @endif
                        @if($c->enlace_campana)
                            <div class="text-muted fs-11"><i class="ri-link"></i> <a href="{{ $c->enlace_campana }}" target="_blank" rel="noopener">Enlace</a></div>
                        @endif
                        @if(!empty($c->observaciones_contable))
                            <div class="mt-1 p-1 rounded" style="background:#fff8e1;color:#92400e;font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $c->observaciones_contable }}</div>
                        @endif
                        <div class="text-muted fs-11 mt-1">
                            @php
                                $fi = \Carbon\Carbon::parse($c->fecha_inicio)->locale('es');
                                $ff = \Carbon\Carbon::parse($c->fecha_fin)->locale('es');
                            @endphp
                            {{ $fi->isoFormat('D MMM') }} al {{ $ff->isoFormat('D MMM') }}
                        </div>
                    </td>
                    <td class="text-center fs-11">
                        @php
                            $g = $c->pres_gestion ?? $c->gestion;
                            $m = $c->pres_mes ?? $c->mes;
                            $q = $c->pres_quincena ?? $c->quincena;
                        @endphp
                        {{ $meses[$m] ?? $m }} {{ $g }}
                        <br><span class="badge {{ $q == 1 ? 'badge-q1' : 'badge-q2' }} fs-10 px-1 py-0">Q{{ $q }}</span>
                    </td>
                    <td class="text-center fs-11">{{ $c->plataforma }}</td>
                    <td class="text-center fs-11">{{ empty($c->programa_nombre) ? '—' : $c->programa_nombre }}</td>
                    <td class="fs-11">{{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }}</td>
                    <td class="text-end fw-bold">
                        @if($c->estado === 'aprobado_obs' && $c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                            @php $montante = (float)$c->monto_aprobado; $orig = (float)$c->monto; @endphp
                            <span class="text-decoration-line-through text-muted fs-11">Bs {{ number_format($orig, 2) }}</span><br>
                            Bs {{ number_format($montante, 2) }}
                        @else
                            Bs {{ number_format($c->monto, 2) }}
                        @endif
                    </td>
                    <td class="text-center">
                        <div><span class="badge bg-primary px-1 py-0" style="font-size:.62rem!important;"><i class="ri-attachment-2"></i> Comp.</span>
                        <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_path) }}" title="{{ $c->archivo_original }}">
                            <i class="ri-attachment-2"></i> Ver
                        </a></div>
                        @if($c->archivo_debito_path)
                        <div class="mt-1"><span class="badge bg-warning text-dark px-1 py-0" style="font-size:.62rem!important;"><i class="ri-bank-card-line"></i> Débito</span>
                        <a href="javascript:void(0)" class="file-preview btn-preview-file fs-11 text-warning" data-url="{{ asset('storage/'.$c->archivo_debito_path) }}" title="{{ $c->archivo_debito_original }}">
                            <i class="ri-bank-card-line"></i> Ver
                        </a></div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($c->estado === 'pendiente')
                            <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1">Pendiente</span>
                        @elseif($c->estado === 'aprobado')
                            <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Aprobado</span>
                            @if($c->monto_aprobado && (float)$c->monto_aprobado !== (float)$c->monto)
                            <div class="fs-11 text-success mt-1"><i class="ri-check-line"></i> Bs {{ number_format($c->monto_aprobado, 2) }}</div>
                            @endif
                        @elseif($c->estado === 'aprobado_obs')
                            <span class="badge bg-soft-info text-info fs-10 px-2 py-1">Aprobado</span>
                            <div class="fs-11 text-info mt-1">
                                <i class="ri-check-double-line"></i> Bs {{ number_format($c->monto_aprobado ?? $c->monto, 2) }}
                                @if($c->observaciones_contable)
                                <br><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}
                                @endif
                            </div>
                        @else
                            <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Rechazado</span>
                            @if($c->observaciones_contable)
                            <div class="fs-11 text-danger mt-1"><i class="ri-chat-4-line"></i> {{ Str::limit($c->observaciones_contable, 60) }}</div>
                            @endif
                        @endif
                        @if($c->verificador_nombre)
                        <div class="fs-11 text-muted mt-1"><i class="ri-user-check-line"></i> {{ $c->verificador_nombre }}</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3" id="pubPagination">
        <div class="text-muted fs-12" id="pubPagInfo"></div>
        <nav aria-label="Paginación de comprobantes">
            <ul class="pagination pagination-sm mb-0" id="pubPagPages"></ul>
        </nav>
    </div>
    @endif
</div>

{{-- Upload modal (dinámico: presupuesto_id se setea vía JS) --}}
@can('marketing.publicidad.subir')
<div class="modal fade" id="modalPubSubir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form class="form-subir-comprobante" method="POST" action="{{ route('admin.marketing.publicidad.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="asesor_id" value="{{ $asesor->id }}">
                <input type="hidden" name="presupuesto_id" id="modalPubPresupuestoId" value="">
                <input type="hidden" name="gestion" id="modalPubGestion" value="">
                <input type="hidden" name="mes" id="modalPubMes" value="">
                <input type="hidden" name="quincena" id="modalPubQuincena" value="">
                <input type="hidden" name="_redirect_to" value="profile">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-upload-cloud-2-line me-1"></i>Subir comprobante</h5>
                        <small class="text-muted">{{ $asesor->apellidos }}, {{ $asesor->nombres }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalPubPeriodoInfo" class="rounded p-3 mb-3" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1px solid #bae6fd;display:none;">
                        <div class="d-flex align-items-center gap-3 flex-wrap" id="modalPubPeriodoData"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de fin <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="0.00" required>
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
                            <input type="text" name="campana_nombre" class="form-control" placeholder="Ej: Diplomado en Auditoría">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Enlace de la campaña</label>
                            <input type="url" name="enlace_campana" class="form-control" placeholder="https://www.facebook.com/ads/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Programa promocionado</label>
                            <select name="programa_id" class="form-select">
                                <option value="">— Sin programa —</option>
                                @foreach($programas as $prg)
                                    <option value="{{ $prg->id }}">{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Página de Facebook</label>
                            <input type="url" name="pagina_facebook" class="form-control" placeholder="https://www.facebook.com/tupagina">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción del gasto <span class="text-danger">*</span></label>
                            <textarea name="descripcion" rows="2" class="form-control" placeholder="Ej: Publicación pagada Facebook 3 días" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Comprobante (PDF o imagen, máx 5MB) <span class="text-danger">*</span></label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Comprobante de débito de meta (PDF o imagen, máx 5MB)</label>
                            <input type="file" name="archivo_debito" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="ri-upload-2-line me-1"></i>Subir comprobante</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Derivar modal (dinámico) --}}
@can('marketing.publicidad.presupuestos')
@if($esPrincipal)
<div class="modal fade" id="modalPubDerivar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.marketing.publicidad.presupuesto.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="supervisor_id" value="{{ $asesor->id }}">
                <input type="hidden" name="presupuesto_origen_id" id="modalDerivarPresupuestoId" value="">
                <input type="hidden" name="gestion" id="modalDerivarGestion" value="">
                <input type="hidden" name="mes" id="modalDerivarMes" value="">
                <input type="hidden" name="quincena" id="modalDerivarQuincena" value="">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-share-forward-line text-info me-1"></i>Derivar presupuesto</h5>
                        <small class="text-muted">{{ $asesor->apellidos }}, {{ $asesor->nombres }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalDerivarPeriodoInfo" class="rounded p-3 mb-3" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1px solid #bae6fd;display:none;">
                        <div class="d-flex align-items-center gap-3 flex-wrap" id="modalDerivarPeriodoData"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asesor destino (Coordinador/Asesor/Freelance) <span class="text-danger">*</span></label>
                        <select name="asesor_id" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($asesoresDerivados as $ad)
                                <option value="{{ $ad->id }}">{{ $ad->cargo_nombre }} — {{ $ad->apellidos }}, {{ $ad->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto a derivar (Bs) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="monto_asignado" id="modalDerivarMonto" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Respaldo de transferencia</label>
                        <input type="file" name="archivo_derivacion" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <small class="text-muted fs-11">Imagen o PDF (máx. 5 MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" class="form-control" placeholder="Motivo de la derivación (opcional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info"><i class="ri-share-forward-line me-1"></i>Derivar presupuesto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

{{-- TAB 4: AUTOGESTIÓN --}}
<div class="tab-pane fade" id="pub-autogestion" role="tabpanel">

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="section-title m-0 border-0 p-0">
        <i class="ri-hand-coin-line" style="color:#f59e0b;"></i> Mis Gastos de Publicidad (Autogestión)
    </div>
    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalAutogestionSubir">
        <i class="ri-add-line me-1"></i>Registrar gasto
    </button>
</div>

<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#f59e0b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Total invertido</p>
                        <h4 class="stat-value mb-0">Bs {{ number_format($totalAutogestion, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-hand-coin-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#f59e0b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Registros</p>
                        <h4 class="stat-value mb-0">{{ $gastosAutogestion->count() }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-receipt-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($gastosAutogestion->isEmpty())
<div class="text-center text-muted py-4">
    <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
    <p class="mb-0">Aún no has registrado gastos de publicidad con tus propios recursos.</p>
    <small>Puedes registrar tus inversiones en anuncios y promociones para llevar un control.</small>
</div>
@else
<div class="table-responsive">
    <table class="table tabla-pub mb-0" style="width:100%;">
        <thead>
            <tr>
                <th class="text-center" style="width:36px;">#</th>
                <th>Descripción</th>
                <th class="text-center">Periodo</th>
                <th class="text-center">Plataf.</th>
                <th class="text-center">Programa</th>
                <th class="text-end" style="width:95px;">Monto</th>
                <th class="text-center" style="width:70px;">Archivo</th>
                <th class="text-center" style="width:90px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gastosAutogestion as $idx => $g)
            <tr>
                <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                <td class="fs-12">
                    <div>{{ $g->descripcion }}</div>
                    @if($g->campana_nombre)
                        <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $g->campana_nombre }}</div>
                    @endif
                    @if($g->enlace_campana)
                        <div class="text-muted fs-11"><i class="ri-link"></i> <a href="{{ $g->enlace_campana }}" target="_blank" rel="noopener">Enlace</a></div>
                    @endif
                    @if($g->pagina_facebook)
                        <div class="text-muted fs-11"><i class="ri-facebook-line"></i> <a href="{{ $g->pagina_facebook }}" target="_blank" rel="noopener">Página</a></div>
                    @endif
                    @if($g->observaciones)
                        <div class="mt-1 p-1 rounded" style="background:#fff8e1;color:#92400e;font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $g->observaciones }}</div>
                    @endif
                </td>
                <td class="text-center fs-11">{{ $meses[$g->mes] ?? $g->mes }} {{ $g->gestion }}</td>
                <td class="text-center fs-11">{{ $g->plataforma ?? '—' }}</td>
                <td class="text-center fs-11">{{ $g->programa?->nombre ?? ($g->programa_id ? '—' : '—') }}</td>
                <td class="text-end fw-bold">Bs {{ number_format($g->monto, 2) }}</td>
                <td class="text-center">
                    @if($g->archivo_path)
                    <div><span class="badge bg-primary px-1 py-0" style="font-size:.62rem!important;"><i class="ri-attachment-2"></i> Comp.</span>
                    <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$g->archivo_path) }}" title="{{ $g->archivo_original }}">
                        <i class="ri-attachment-2"></i> Ver
                    </a></div>
                    @endif
                    @if($g->archivo_debito_path)
                    <div class="mt-1"><span class="badge bg-warning text-dark px-1 py-0" style="font-size:.62rem!important;"><i class="ri-bank-card-line"></i> Débito</span>
                    <a href="javascript:void(0)" class="file-preview btn-preview-file fs-11 text-warning" data-url="{{ asset('storage/'.$g->archivo_debito_path) }}" title="{{ $g->archivo_debito_original }}">
                        <i class="ri-bank-card-line"></i> Ver
                    </a></div>
                    @endif
                    @if(!$g->archivo_path && !$g->archivo_debito_path)
                    <span class="text-muted fs-11">—</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($g->estado === 'pendiente')
                        <span class="badge bg-soft-warning text-warning fs-10 px-2 py-1">Pendiente</span>
                    @elseif($g->estado === 'aprobado')
                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Aprobado</span>
                    @else
                        <span class="badge bg-soft-danger text-danger fs-10 px-2 py-1">Rechazado</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

</div>{{-- /pub-autogestion tab-pane --}}
</div>{{-- /tab-content --}}

{{-- Modal: Registrar gasto de autogestión --}}
<div class="modal fade" id="modalAutogestionSubir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.profile.publicidad.autogestion.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="gestion" value="{{ now()->format('Y') }}">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-hand-coin-line text-warning me-1"></i>Registrar gasto de publicidad</h5>
                        <small class="text-muted">{{ $asesor->apellidos }}, {{ $asesor->nombres }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning fs-12 py-2 mb-3">
                        <i class="ri-information-line me-1"></i>
                        Estás registrando un gasto realizado con tus <strong>propios recursos</strong>. Esto no está vinculado al presupuesto de empresa.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plataforma</label>
                            <select name="plataforma" class="form-select">
                                <option value="">— Seleccionar —</option>
                                <option value="Facebook">Facebook Ads</option>
                                <option value="Instagram">Instagram Ads</option>
                                <option value="TikTok">TikTok Ads</option>
                                <option value="Google">Google Ads</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre de la campaña</label>
                            <input type="text" name="campana_nombre" class="form-control" placeholder="Ej: Diplomado en Auditoría">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Enlace de la campaña</label>
                            <input type="url" name="enlace_campana" class="form-control" placeholder="https://www.facebook.com/ads/...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Página de Facebook</label>
                            <input type="url" name="pagina_facebook" class="form-control" placeholder="https://www.facebook.com/tupagina">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Programa promocionado</label>
                            <select name="programa_id" class="form-select">
                                <option value="">— Sin programa —</option>
                                @foreach($programas as $prg)
                                    <option value="{{ $prg->id }}">{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mes de gestión</label>
                            <select name="mes" class="form-select">
                                @foreach($meses as $num => $nom)
                                    <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea name="descripcion" rows="2" class="form-control" placeholder="Ej: Publicación pagada Facebook 3 días" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comprobante (PDF o imagen, máx 5MB)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <small class="text-muted fs-11">Opcional, pero recomendado para respaldo. Límite del servidor: {{ ini_get('upload_max_filesize') ?: 'no definido' }}.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comprobante de débito de meta (PDF o imagen, máx 5MB)</label>
                            <input type="file" name="archivo_debito" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <small class="text-muted fs-11">Límite del servidor: {{ ini_get('upload_max_filesize') ?: 'no definido' }}.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ri-save-line me-1"></i>Registrar gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.nav-tabs-custom { border-bottom:1px solid #eef0f4; gap:0; }
.nav-tabs-custom .nav-link { border:1px solid transparent; border-bottom:none; font-size:.78rem; font-weight:600; color:#878a99; padding:.55rem .9rem; margin-bottom:-1px; border-radius:8px 8px 0 0; background:transparent; transition:all .15s ease; }
.nav-tabs-custom .nav-link:hover { color:#405189; background:#f8f9fc; }
.nav-tabs-custom .nav-link.active { color:#405189; background:#fff; border-color:#eef0f4; border-bottom-color:#fff; }
.nav-tabs-custom .nav-link .badge { font-size:.62rem; font-weight:600; padding:.2em .55em; vertical-align:middle; }
[data-layout-mode="dark"] .nav-tabs-custom { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link { color:#878a99; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link:hover { color:#fff; background:#1d2125; }
[data-layout-mode="dark"] .nav-tabs-custom .nav-link.active { color:#fff; background:#1d2125; border-color:#2a2f34; border-bottom-color:#1d2125; }

.stat-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; overflow:hidden; position:relative; }
.stat-card::before { content:""; position:absolute; top:0; left:0; width:4px; height:100%; background:var(--stat-accent,#405189); }
[data-layout-mode="dark"] .stat-card { background:#212529; border-color:#2a2f34; }
.stat-card .stat-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.5px; color:#878a99; font-weight:700; margin-bottom:.4rem; }
.stat-card .stat-value { font-size:1.15rem; font-weight:700; color:#212529; line-height:1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color:#fff; }
.stat-card .stat-icon { width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; font-size:1.25rem; }

.period-icon { width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:#405189;color:#fff;border-radius:10px;font-size:1.05rem; }
.badge-q1 { background:#dbeafe; color:#1e40af; font-weight:600; }
.badge-q2 { background:#fef3c7; color:#92400e; font-weight:600; }
[data-layout-mode="dark"] .badge-q1 { background:#1e3a5f; color:#93c5fd; }
[data-layout-mode="dark"] .badge-q2 { background:#451a03; color:#fbbf24; }
.bar-progress { width:80px;height:5px;background:#e9ecef;border-radius:99px;overflow:hidden; }
.bar-progress > span { display:block;height:100%;background:linear-gradient(90deg,#0ab39c,#299cdb);border-radius:99px; }
.bar-progress.warn > span { background:linear-gradient(90deg,#f7b84b,#f06548); }
[data-layout-mode="dark"] .bar-progress { background:#2a2f34; }

.tabla-pub th, .tabla-pub td { vertical-align: middle; padding:.65rem .6rem; }
.tabla-pub thead th { background:#f8f9fc !important; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#495057; border-bottom:2px solid #eef0f4; }
[data-layout-mode="dark"] .tabla-pub thead th { background:#1a1d20 !important; border-bottom-color:#2a2f34; }
.tabla-pub tbody tr { border-bottom:1px solid #f1f3f5; transition:background-color .15s ease; }
.tabla-pub tbody tr:hover { background-color:#f8f9fc; }
[data-layout-mode="dark"] .tabla-pub tbody tr { border-bottom-color:#2a2f34; }
[data-layout-mode="dark"] .tabla-pub tbody tr:hover { background-color:#1d2125; }
.file-preview { display:inline-flex; align-items:center; gap:.4rem; padding:.25rem .6rem; background:#f1f3f5; border-radius:6px; font-size:.75rem; color:#495057; text-decoration:none; }
.file-preview:hover { background:#e9ecef; color:#405189; }
.periodo-card:last-child { margin-bottom:0 !important; }
.periodo-header:hover { background:linear-gradient(135deg,#e8edf8,#dce6f8) !important; }
[data-layout-mode="dark"] .periodo-header { background:linear-gradient(135deg,#1a1f2e,#1d2125) !important; border-left-color:#405189; }
[data-layout-mode="dark"] .periodo-header:hover { background:linear-gradient(135deg,#232839,#272c30) !important; }
</style>