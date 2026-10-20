@php
    /**
     * Fila de un asesor derivado (agrupada). Si se derivó N veces al mismo asesor
     * se renderiza una sola fila y el detalle se despliega al hacer clic.
     *
     * Vars: $d (grupo), $raiz (presupuesto origen, opcional = huérfano), $meses
     */
    $esHuerfano = !isset($raiz) || $raiz === null;
    $derId = $d->grupo_id;
    $items = $d->items ?? collect();
    $desglose = $items->map(fn($i) => 'Bs ' . number_format((float) $i->monto_asignado, 2))->implode(' + ');
    $itemArchivo = $items->first(fn($i) => !empty($i->archivo_derivacion_path));
@endphp
<tr class="row-derivado {{ ($ultimoDerivado ?? false) ? 'ultimo-derivado' : '' }}">
    <td>
        <div class="pres-asesor {{ $esHuerfano ? 'deriv-suelto' : 'deriv-cell' }}">
            <span class="pres-avatar pres-avatar-sm"><i class="ri-corner-down-right-line"></i></span>
            <div class="flex-grow-1">
                <div class="pres-name">
                    @if($d->ase_apellidos)
                        {{ $d->ase_apellidos }}, {{ $d->ase_nombres }}
                    @else
                        <span class="fst-italic text-muted" style="font-weight:400;">Sin asesor asignado</span>
                    @endif
                    <span class="chip chip-deriv"><i class="ri-share-forward-line"></i>Derivado</span>
                    <span class="chip"><i class="ri-file-list-3-line"></i>{{ $d->cantidad }} asignación(es)</span>
                    @if($esHuerfano || $d->sin_origen)
                        <span class="chip chip-warn" title="El presupuesto origen (#{{ $d->origen_id }}) no está en este listado: filtrado por gestión o pertenece a otro periodo"><i class="ri-link-unlink-line"></i>Origen #{{ $d->origen_id }} fuera de este periodo</span>
                    @endif
                </div>
                <div class="pres-sub">
                    @if($d->supervisores->isNotEmpty())
                        @if($d->supervisores->count() === 1)
                            Supervisor: {{ $d->supervisores[0]->apellidos }}, {{ $d->supervisores[0]->nombres }}
                        @else
                            Supervisores: @foreach($d->supervisores as $sup){{ $sup->apellidos }}{{ $loop->last ? '' : ', ' }}@endforeach
                        @endif
                        ·
                    @endif
                    <span class="pres-sub-id">Asignación #{{ $items->pluck('id')->join(', #') }}</span>
                    · {{ $meses[$items->first()->mes] ?? '' }} {{ $items->first()->gestion ?? '' }} · Q{{ $items->first()->quincena ?? '' }}
                </div>
            </div>
        </div>
    </td>
    <td class="text-end num">
        <span class="amount-strong">Bs {{ number_format($d->monto_asignado, 2) }}</span>
        @if($d->cantidad > 1)
            <div class="pres-sub" title="Detalle de cada asignación">{{ $desglose }}</div>
        @endif
        @if($d->monto_reembolsado > 0)
            <div class="pres-sub text-danger">Reembolsado Bs {{ number_format($d->monto_reembolsado, 2) }}</div>
        @endif
    </td>
    <td class="text-end num">
        <span class="{{ $d->total_gastado > 0 ? '' : 'text-muted' }}">Bs {{ number_format($d->total_gastado, 2) }}</span>
    </td>
    <td class="text-end num">
        <span class="text-success fw-semibold">Bs {{ number_format($d->total_aprobado, 2) }}</span>
        @if($d->total_aprobado_obs > 0)
            <div class="pres-sub text-info">+ Bs {{ number_format($d->total_aprobado_obs, 2) }} c/obs</div>
        @endif
    </td>
    <td class="text-center">
        <span class="chip">{{ $d->total_comprobantes }}</span>
    </td>
    <td class="text-center">
        @if($d->periodo_cerrado)
            <span class="pres-sub"><i class="ri-lock-line"></i> Cerrado</span>
        @elseif($d->estado === 'activo')
            <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
        @else
            <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1">Cerrado</span>
        @endif
    </td>
    <td class="text-center">
        <div class="d-flex align-items-center gap-1 justify-content-center">
            @if($d->asesor_id)
            <a href="{{ route('admin.marketing.publicidad.index', ['gestion' => $raiz->gestion ?? $items->first()->gestion, 'mes' => $raiz->mes ?? $items->first()->mes, 'quincena' => $raiz->quincena ?? $items->first()->quincena, 'asesor_id' => $d->asesor_id]) }}" class="btn btn-sm btn-icon btn-soft-primary" title="Ver comprobantes de este asesor">
                <i class="ri-eye-line"></i>
            </a>
            @endif
            @if($itemArchivo)
            <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-soft-info btn-preview-file"
               data-url="{{ asset('storage/' . $itemArchivo->archivo_derivacion_path) }}"
               data-ext="{{ pathinfo($itemArchivo->archivo_derivacion_original ?? '', PATHINFO_EXTENSION) }}"
               title="Ver archivo de derivación">
                <i class="ri-attachment-2"></i>
            </a>
            @endif
            <button type="button" class="btn btn-sm btn-icon btn-soft-amber btn-detalle-derivado" data-der="{{ $derId }}" title="Ver detalle de asignaciones" aria-expanded="false" aria-controls="der-detalle-{{ $derId }}">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
    </td>
</tr>

{{-- Detalle de cada asignación --}}
<tr class="row-detalle-derivado" id="der-detalle-{{ $derId }}" data-der-detalle="{{ $derId }}" style="display:none;">
    <td colspan="7" class="p-0">
        <div class="deriv-panel">
            <div class="deriv-panel-head">
                <div class="dp-title">
                    <i class="ri-file-list-3-line"></i>
                    Detalle de asignaciones
                    @if($d->ase_apellidos)
                        <span class="dp-asesor">· {{ $d->ase_apellidos }}, {{ $d->ase_nombres }}</span>
                    @endif
                </div>
                <div class="dp-meta">
                    {{ $d->cantidad }} registro(s) ·
                    @if($raiz)
                        Origen: <b>#{{ $raiz->id }} {{ $raiz->ase_apellidos }}, {{ $raiz->ase_nombres }}</b> ·
                    @elseif($d->sin_origen)
                        Origen: <b>#{{ $d->origen_id }}</b> ·
                    @endif
                    Total <b>Bs {{ number_format($d->monto_asignado, 2) }}</b>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table deriv-detail-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px;">#</th>
                            <th class="text-end" style="width:110px;">Asignado</th>
                            <th class="text-end" style="width:105px;">Reembolsado</th>
                            <th class="text-end" style="width:105px;">Gastado</th>
                            <th class="text-end" style="width:110px;">Aprobado</th>
                            <th class="text-end" style="width:105px;">Pendiente</th>
                            <th class="text-center" style="width:80px;">Recibos</th>
                            <th>Observaciones</th>
                            <th class="text-center" style="width:90px;">Archivo</th>
                            <th style="width:135px;">Fecha</th>
                            <th class="text-center" style="width:90px;">Estado</th>
                            <th class="text-center" style="width:70px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $item)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $i + 1 }}</td>
                            <td class="text-end num"><span class="fw-bold">Bs {{ number_format((float) $item->monto_asignado, 2) }}</span></td>
                            <td class="text-end num">
                                @if((float) $item->monto_reembolsado > 0)
                                    <span class="text-danger">Bs {{ number_format((float) $item->monto_reembolsado, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end num">
                                <span class="{{ (float) $item->total_gastado > 0 ? '' : 'text-muted' }}">Bs {{ number_format((float) $item->total_gastado, 2) }}</span>
                            </td>
                            <td class="text-end num">
                                <span class="text-success fw-semibold">Bs {{ number_format((float) $item->total_aprobado, 2) }}</span>
                                @if((float) $item->total_aprobado_obs > 0)
                                    <div class="pres-sub text-info">+ Bs {{ number_format((float) $item->total_aprobado_obs, 2) }}</div>
                                @endif
                            </td>
                            <td class="text-end num">
                                @if((float) $item->total_pendiente > 0)
                                    <span class="text-warning fw-semibold">Bs {{ number_format((float) $item->total_pendiente, 2) }}</span>
                                @else
                                    <span class="text-muted">Bs 0.00</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="chip">{{ $item->total_comprobantes }}</span></td>
                            <td class="obs-cell">
                                @if(!empty($item->observaciones))
                                    <span class="deriv-obs" title="{{ $item->observaciones }}">{{ \Illuminate\Support\Str::limit($item->observaciones, 90) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($item->archivo_derivacion_path))
                                <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-soft-info btn-preview-file"
                                   data-url="{{ asset('storage/'.$item->archivo_derivacion_path) }}"
                                   data-ext="{{ pathinfo($item->archivo_derivacion_original ?? '', PATHINFO_EXTENSION) }}"
                                   title="{{ $item->archivo_derivacion_original }}">
                                    <i class="ri-attachment-2"></i>
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fs-11 text-muted">
                                <div>{{ \Illuminate\Support\Str::of((string) $item->created_at)->before(' ') }}</div>
                                <div class="deriv-fecha-hora">{{ \Illuminate\Support\Str::of((string) $item->created_at)->after(' ') }}</div>
                            </td>
                            <td class="text-center">
                                @if($item->periodo_cerrado)
                                    <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                                @elseif($item->estado === 'activo')
                                    <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1">Cerrado</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('marketing.publicidad.presupuestos')
                                @if($item->puede_eliminar)
                                <button type="button" class="btn btn-sm btn-icon btn-soft-danger btn-eliminar-pres"
                                        data-id="{{ $item->id }}"
                                        data-asesor="{{ $item->ase_apellidos ? $item->ase_apellidos . ', ' . $item->ase_nombres : 'Sin asesor asignado' }}"
                                        data-periodo="{{ $meses[$item->mes] }} {{ $item->gestion }} · Q{{ $item->quincena }}"
                                        data-monto="{{ number_format((float) $item->monto_asignado, 2) }}"
                                        title="Eliminar asignación sin gastos">
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-icon btn-soft-secondary" disabled
                                        title="{{ $item->total_comprobantes > 0 ? 'Tiene comprobantes: no se puede eliminar' : (($item->num_derivados ?? 0) > 0 ? 'Tiene derivaciones: no se puede eliminar' : ($item->periodo_cerrado ? 'El periodo está cerrado' : 'No se puede eliminar')) }}">
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                                @endif
                                @else
                                <span class="text-muted">—</span>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-3 fs-12"><i class="ri-inbox-2-line me-1"></i>Sin asignaciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-center fw-bold">Σ</td>
                            <td class="text-end num fw-bold">Bs {{ number_format($d->monto_asignado, 2) }}</td>
                            <td class="text-end num fw-bold">{{ $d->monto_reembolsado > 0 ? 'Bs ' . number_format($d->monto_reembolsado, 2) : '—' }}</td>
                            <td class="text-end num fw-bold">Bs {{ number_format($d->total_gastado, 2) }}</td>
                            <td class="text-end num fw-bold text-success">Bs {{ number_format($d->total_aprobado, 2) }}</td>
                            <td class="text-end num fw-bold">{{ $d->total_pendiente > 0 ? 'Bs ' . number_format($d->total_pendiente, 2) : '—' }}</td>
                            <td class="text-center fw-bold">{{ $d->total_comprobantes }}</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </td>
</tr>
