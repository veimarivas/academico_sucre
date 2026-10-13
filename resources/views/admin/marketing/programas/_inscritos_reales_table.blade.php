<div class="d-flex align-items-center gap-2 mb-3">
    <span class="avatar-xs"><span class="avatar-title bg-soft-info text-info rounded-circle fs-16"><i class="ri-team-line"></i></span></span>
    <div>
        <h6 class="mb-0 fs-14 fw-semibold">{{ $programa->nombre }} — Inscritos Reales Marketing</h6>
        <small class="text-muted" style="font-size:.72rem;">
            <strong>Real</strong> = pagó matrícula + al menos 1 colegiatura (Regular) / pagó al menos 1/{{ $numModulos }} de colegiatura (Contado)
        </small>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="p-2 rounded" style="background:#e8faf1;border:1px solid #a7f3d8;">
            <small class="text-muted fs-11">Inscritos Reales</small>
            <h5 class="mb-0 text-success fw-bold">{{ $totalReales }} <small class="text-muted fw-normal fs-11">({{ $porcentajeReales }}%)</small></h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-2 rounded" style="background:#fef0f0;border:1px solid #fecaca;">
            <small class="text-muted fs-11">No Reales</small>
            <h5 class="mb-0 text-danger fw-bold">{{ $totalNoReales }} <small class="text-muted fw-normal fs-11">({{ 100 - $porcentajeReales }}%)</small></h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-2 rounded" style="background:#f3f5f9;border:1px solid #eef0f4;">
            <small class="text-muted fs-11">Total Inscritos</small>
            <h5 class="mb-0 fw-bold">{{ $total }}</h5>
        </div>
    </div>
</div>

<div class="table-responsive" style="max-height:420px;overflow-y:auto;">
    <table class="table table-sm table-borderless align-middle mb-0 table-hover" style="font-size:.78rem;">
        <thead class="table-light text-muted text-uppercase" style="position:sticky;top:0;z-index:2;">
            <tr>
                <th style="width:32px;" class="text-center">#</th>
                <th>Participante</th>
                <th style="width:120px;">Asesor</th>
                <th style="width:60px;" class="text-center">Plan</th>
                <th style="width:70px;" class="text-center">Traspaso</th>
                <th style="width:110px;" class="text-end">Matrícula</th>
                <th style="width:110px;" class="text-end">Colegiatura</th>
                <th style="width:80px;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @php $allItems = array_merge($reales, $noReales); @endphp
            @forelse($allItems as $idx => $item)
            @php
                $rowBg = $item['es_real'] ? 'background:rgba(10,179,156,0.06) !important;' : '';
                $traspaso = $item['es_traspaso'] ? 'traspaso-row' : '';
            @endphp
            <tr style="{{ $rowBg }}" class="{{ $traspaso }}">
                <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                <td>
                    <div class="fw-semibold text-dark">{{ $item['apellidos'] }}, {{ $item['nombres'] }}@if($item['es_traspaso']) <span class="traspaso-badge"><i class="ri-swap-line"></i></span>@endif</div>
                    <div class="text-muted" style="font-size:.68rem;"><i class="ri-profile-line align-bottom"></i> CI: {{ $item['ci'] }}</div>
                </td>
                <td>
                    @if($item['asesor_nombres'])
                        <span class="fw-semibold" style="font-size:.75rem;">{{ $item['asesor_apellidos'] }}, {{ $item['asesor_nombres'] }}</span>
                    @else
                        <span class="text-muted" style="font-size:.7rem;">—</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($item['contado'])
                        <span class="badge bg-soft-info text-info fs-10">Contado</span>
                    @else
                        <span class="badge bg-soft-primary text-primary fs-10">Regular</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($item['es_traspaso'])
                        <span class="badge bg-soft-warning text-warning fs-10"><i class="ri-swap-line"></i> Traspaso</span>
                    @else
                        <span class="text-muted fs-10" style="font-size:.68rem;">—</span>
                    @endif
                </td>
                <td class="text-end">
                    <div class="money-stack" style="line-height:1.3;">
                        <span class="fw-semibold text-dark">Bs {{ number_format($item['mat_neto'], 2) }}</span>
                        <span class="text-success" style="font-size:.68rem;"><i class="ri-check-line"></i> Pag {{ number_format($item['mat_pagado'], 2) }}</span>
                        @if($item['falta_mat'] > 0)
                            <span class="text-danger" style="font-size:.68rem;"><i class="ri-time-line"></i> Falta Bs {{ number_format($item['falta_mat'], 2) }}</span>
                        @elseif($item['mat_neto'] > 0)
                            <span class="text-success" style="font-size:.68rem;"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                        @else
                            <span class="text-muted" style="font-size:.68rem;">—</span>
                        @endif
                    </div>
                </td>
                <td class="text-end">
                    <div class="money-stack" style="line-height:1.3;">
                        <span class="fw-semibold text-dark">Bs {{ number_format($item['col_neto'], 2) }}</span>
                        <span class="text-success" style="font-size:.68rem;"><i class="ri-check-line"></i> Pag {{ number_format($item['col_pagado'], 2) }}</span>
                        @if($item['falta_col'] > 0)
                            <span class="text-danger" style="font-size:.68rem;"><i class="ri-time-line"></i> Falta Bs {{ number_format($item['falta_col'], 2) }}</span>
                        @elseif($item['col_pagado'] > 0)
                            <span class="text-success" style="font-size:.68rem;"><i class="ri-checkbox-circle-line"></i> Cancelado</span>
                        @else
                            <span class="text-muted" style="font-size:.68rem;">—</span>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    @if($item['es_real'])
                        <span class="badge bg-soft-success text-success fs-10"><i class="ri-checkbox-circle-line"></i> Real</span>
                    @else
                        <span class="badge bg-soft-secondary text-secondary fs-10"><i class="ri-close-circle-line"></i> No Real</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4 fs-13">No hay inscripciones en este programa</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
