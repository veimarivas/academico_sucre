<div class="presupuesto-item mb-3 p-3">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="badge badge-quincena-{{ $p->quincena }} fs-12 px-3 py-1">Q{{ $p->quincena }} · {{ $rango }}</span>
            <div>
                <div class="fw-bold fs-14 text-dark">
                    @if($p->ase_apellidos)
                        <i class="ri-user-star-line text-warning me-1"></i>{{ $p->ase_apellidos }}, {{ $p->ase_nombres }}
                    @else
                        <i class="ri-team-line text-muted me-1"></i>Presupuesto general (asignar a un asesor)
                    @endif
                </div>
                <div class="text-muted fs-11">
                    @if($p->sup_apellidos)
                        Supervisor: <strong>{{ $p->sup_apellidos }}, {{ $p->sup_nombres }}</strong> ·
                    @endif
                    {{ $meses[$p->mes] }} {{ $p->gestion }}
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($p->presupuesto_origen_id)
                <span class="badge" style="background:#fef3c7;color:#92400e;font-size:.68rem;font-weight:600;"><i class="ri-share-forward-line"></i> Derivado</span>
            @endif
            @if($p->periodo_cerrado)
                <span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1"><i class="ri-lock-line"></i> Periodo cerrado</span>
            @elseif($p->estado === 'activo')
                <span class="badge bg-soft-success text-success fs-11 px-2 py-1"><i class="ri-radio-button-line"></i> Activo</span>
            @else
                <span class="badge bg-soft-secondary text-secondary fs-11 px-2 py-1"><i class="ri-check-double-line"></i> Cerrado</span>
            @endif

            @if(!$p->periodo_cerrado)
            @can('marketing.publicidad.subir')
            @if($p->estado === 'activo')
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSubir{{ $p->id }}">
                <i class="ri-upload-2-line me-1"></i>Subir comprobante
            </button>
            @endif
            @endcan

            @can('marketing.publicidad.presupuestos')
            @if($p->estado === 'activo')
            <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#modalReembolso{{ $p->id }}">
                <i class="ri-hand-coin-line me-1"></i>Reembolsar
            </button>
            @if(!$p->presupuesto_origen_id)
            <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="modal" data-bs-target="#modalDerivar{{ $p->id }}">
                <i class="ri-share-forward-line me-1"></i>Derivar
            </button>
            @endif
            @endif
            @endcan
            @endif
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-3 col-6">
            <div class="p-2 rounded card-monto">
                <div class="stat-label mb-0">
                    @if($p->monto_derivado_total > 0)
                        <i class="ri-funds-line me-1" style="color:#b45309;" title="Presupuesto con derivaciones"></i>Original
                    @else
                        Asignado
                    @endif
                </div>
                @if($p->monto_derivado_total > 0)
                    <div class="fw-bold fs-14 amount-original">Bs {{ number_format($p->monto_original, 2) }}</div>
                    <div class="mt-1 fs-11" style="color:#6c757d;">
                        <span class="amount-derived"><i class="ri-share-forward-line"></i> Derivado: <strong>Bs {{ number_format($p->monto_derivado_total, 2) }}</strong></span><br>
                        <span class="amount-neto"><i class="ri-checkbox-blank-circle-line"></i> Neto: <strong>Bs {{ number_format($p->monto_asignado, 2) }}</strong></span>
                    </div>
                @else
                    <div class="fw-bold amount-neto">Bs {{ number_format($p->monto_asignado, 2) }}</div>
                @endif
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-2 rounded card-monto">
                <div class="stat-label mb-0">Gastado ({{ $p->total_comprobantes }} recibos)</div>
                <div class="fw-bold">Bs {{ number_format($p->total_gastado, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-2 rounded card-monto">
                <div class="stat-label mb-0">Aprobado</div>
                <div class="fw-bold text-success">Bs {{ number_format($p->total_aprobado, 2) }}</div>
                @if(($p->total_aprobado_obs ?? 0) > 0)
                <div class="fs-11 text-info mt-1"><i class="ri-chat-4-line"></i> {{ number_format($p->total_aprobado_obs, 2) }} con obs.</div>
                @endif
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-2 rounded card-monto">
                <div class="stat-label mb-0">Reembolsado</div>
                <div class="fw-bold text-info">Bs {{ number_format($p->monto_reembolsado, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="progress-bar-custom {{ $porc > 100 ? 'warn' : '' }} mb-1"><span style="width: {{ $porc }}%;"></span></div>
    <div class="d-flex justify-content-between fs-11 text-muted mb-3">
        <span>Avance del gasto: <strong>{{ number_format($porc, 1) }}%</strong></span>
        <span>Pendiente por verificar: <strong class="text-warning">Bs {{ number_format($p->total_pendiente, 2) }}</strong></span>
    </div>

    @if($p->observaciones)
    <div class="p-2 mb-3 rounded fs-12" style="background:#fff8e1; color:#92400e; white-space:pre-line;">
        <i class="ri-chat-quote-line"></i> {{ $p->observaciones }}
    </div>
    @endif

    {{-- Presupuestos derivados de este con detalle completo de comprobantes --}}
    @php $derivados = $derivadosPorPresupuesto->get($p->id) ?? collect(); @endphp
    @if($derivados->count() > 0)
    <div class="mb-3 deriv-section">
        <div class="px-3 py-2 deriv-header">
            <div class="d-flex align-items-center gap-2 fs-12 fw-semibold deriv-title" style="color:#b45309;">
                <i class="ri-share-forward-line"></i> Presupuestos derivados ({{ $derivados->count() }})
            </div>
        </div>
        @foreach($derivados as $d)
        @php
            $compsDerivado = $comprobantesPorPresupuesto[$d->id] ?? collect();
            $porcD = $d->monto_asignado > 0 ? min(100, ($d->total_gastado / $d->monto_asignado) * 100) : 0;
        @endphp
        <div class="deriv-row">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2 deriv-row-alt">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="display:inline-flex;align-items:center;gap:5px;font-weight:700;color:#92400e;font-size:.82rem;">
                        <i class="ri-user-star-line" style="color:#d97706;"></i> {{ $d->ase_apellidos ?? '?' }}, {{ $d->ase_nombres ?? '?' }}
                    </span>
                    <span class="badge badge-quincena-{{ $d->quincena }} fs-10 px-2 py-1">Q{{ $d->quincena }}</span>
                    <span class="fw-bold fs-13" style="color:#b45309;">Bs {{ number_format($d->monto_asignado, 2) }}</span>
                    <div style="width:60px;height:4px;background:#e9ecef;border-radius:99px;overflow:hidden;">
                        <span style="display:block;height:100%;width:{{ $porcD }}%;background:linear-gradient(90deg,#0ab39c,{{ $porcD > 100 ? '#f06548' : '#299cdb' }});border-radius:99px;"></span>
                    </div>
                    <span class="text-muted fs-11">{{ $porcD }}%</span>
                    <span class="fs-11 text-muted">Gastado: <strong>Bs {{ number_format($d->total_gastado, 2) }}</strong></span>
                    <span class="fs-11 text-success">Aprobado: <strong>Bs {{ number_format($d->total_aprobado, 2) }}</strong>
                        @if(($d->total_aprobado_obs ?? 0) > 0)
                        <span class="text-info" title="Con observaciones"><i class="ri-chat-4-line"></i> {{ number_format($d->total_aprobado_obs, 2) }}</span>
                        @endif
                    </span>
                    @if($d->estado === 'activo')
                        <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
                    @else
                        <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1">Cerrado</span>
                    @endif
                    @if($d->observaciones)
                        <span style="color:#878a99;font-size:.72rem;" title="{{ $d->observaciones }}"><i class="ri-chat-4-line"></i></span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="text-muted fs-11">{{ $compsDerivado->count() }} comprobante(s)</span>
                    @if($d->archivo_derivacion_path)
                    <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-soft-info file-preview btn-preview-file" data-url="{{ asset('storage/'.$d->archivo_derivacion_path) }}" title="{{ $d->archivo_derivacion_original }}">
                        <i class="ri-attachment-2"></i>
                    </a>
                    @endif
                    @can('marketing.publicidad.presupuestos')
                    @if(!$p->periodo_cerrado && $compsDerivado->count() === 0)
                    <button type="button" class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="modal" data-bs-target="#modalEditarDerivacion{{ $d->id }}" title="Editar derivación">
                        <i class="ri-edit-2-line"></i>
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 tabla-pub" id="comprobantes-deriv-{{ $d->id }}" data-per-page="10">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:36px;">#</th>
                            <th style="width:110px;">Inicio / Fin</th>
                            <th style="width:180px;">Asesor del gasto</th>
                            <th>Descripción / Campaña</th>
                            <th class="text-center" style="width:80px;">Plataforma</th>
                            <th class="text-center" style="width:120px;">Programa</th>
                            <th class="text-end" style="width:100px;">Monto</th>
                            <th class="text-center" style="width:80px;">Archivo</th>
                            <th class="text-center" style="width:100px;">Estado</th>
                            <th class="text-center" style="width:130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compsDerivado as $subIdx => $c)
                        @php
                            $mismoTitularD = $d->asesor_id && (int)$c->asesor_id === (int)$d->asesor_id;
                        @endphp
                        @include('admin.marketing.publicidad._comprobante_row', ['c' => $c, 'p' => $d, 'mismoTitular' => $mismoTitularD, 'idx' => $subIdx + 1])
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-3 fs-12"><i class="ri-inbox-2-line me-1"></i>Sin comprobantes cargados por este asesor</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-comprobantes d-flex justify-content-center align-items-center gap-2 mt-2" data-table-id="comprobantes-deriv-{{ $d->id }}"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Comprobantes de este presupuesto --}}
    <div class="table-responsive">
        <table class="table mb-0 tabla-pub" id="comprobantes-{{ $p->id }}" data-per-page="10">
            <thead>
                <tr>
                    <th class="text-center" style="width:36px;">#</th>
                    <th style="width:110px;">Inicio / Fin</th>
                    <th style="width:180px;">Asesor del gasto</th>
                    <th>Descripción / Campaña</th>
                    <th class="text-center" style="width:80px;">Plataforma</th>
                    <th class="text-center" style="width:120px;">Programa</th>
                    <th class="text-end" style="width:100px;">Monto</th>
                    <th class="text-center" style="width:80px;">Archivo</th>
                    <th class="text-center" style="width:100px;">Estado</th>
                    <th class="text-center" style="width:130px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comps->groupBy('asesor_id') as $asesorIdGrupo => $compsGrupo)
                @php
                    $primer = $compsGrupo->first();
                    $totalGrupo = $compsGrupo->sum('monto');
                @endphp
                <tr style="background:var(--ui-surface-2);">
                    <td colspan="10" class="fs-12 py-2 border-bottom-0">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;background:#fff;border-radius:8px;">
                                <i class="ri-user-star-line text-warning fs-13"></i>
                            </span>
                            <strong class="fs-13">{{ $primer->asesor_apellidos }}, {{ $primer->asesor_nombres }}</strong>
                            <span class="badge bg-primary bg-opacity-10 text-primary fs-10 px-2 py-1 ms-1">{{ $compsGrupo->count() }} recibo(s)</span>
                        </div>
                    </td>
                </tr>
                @foreach($compsGrupo as $subIdx => $c)
                @php $mismoTitular = $p->asesor_id && (int)$c->asesor_id === (int)$p->asesor_id; @endphp
                @include('admin.marketing.publicidad._comprobante_row', ['c' => $c, 'p' => $p, 'mismoTitular' => $mismoTitular, 'idx' => $subIdx + 1])
                @endforeach
                <tr style="background:#f8f9fc;">
                    <td colspan="6" class="text-end fs-12 py-2 fw-semibold text-dark">
                        <i class="ri-calculator-line me-1 text-muted"></i>Total {{ $primer->asesor_apellidos }}, {{ $primer->asesor_nombres }}
                    </td>
                    <td class="text-end fw-bold fs-14 text-success py-2">Bs {{ number_format($totalGrupo, 2) }}</td>
                    <td colspan="3" class="py-2"></td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="10" class="text-center text-muted py-3 fs-12"><i class="ri-inbox-2-line me-1"></i>Sin comprobantes cargados en esta asignación</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-comprobantes d-flex justify-content-center align-items-center gap-2 mt-2" data-table-id="comprobantes-{{ $p->id }}"></div>
    </div>
</div>

@foreach($derivados as $d)
@can('marketing.publicidad.presupuestos')
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
@endcan
@endforeach