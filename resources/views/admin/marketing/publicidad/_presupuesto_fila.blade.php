@php
    $nivel = $nivel ?? 'raiz';
    $derivados = $p->derivados ?? collect();
    $totalDerivados = (int) ($p->num_derivados ?? $derivados->sum('cantidad'));
    $totalAsesores = $derivados->count();
@endphp
<tr class="row-raiz">
    <td>
        <div class="pres-asesor">
            <span class="pres-avatar"><i class="ri-user-star-line"></i></span>
            <div>
                <div class="pres-name">
                    @if($p->ase_apellidos)
                        {{ $p->ase_apellidos }}, {{ $p->ase_nombres }}
                    @else
                        <span class="fst-italic text-muted" style="font-weight:400;">Sin asesor asignado</span>
                    @endif
                    @if($totalAsesores > 0)
                        <span class="chip"><i class="ri-share-forward-line"></i>{{ $totalAsesores }} asesor(es) · {{ $totalDerivados }} derivación(es)</span>
                    @endif
                </div>
                @if($p->sup_apellidos)
                    <div class="pres-sub">Supervisor: {{ $p->sup_apellidos }}, {{ $p->sup_nombres }}</div>
                @endif
            </div>
        </div>
    </td>
    <td class="text-end num">
        <span class="amount-strong">Bs {{ number_format($p->monto_asignado, 2) }}</span>
        @if($totalDerivados > 0)
            <div class="pres-sub">Original Bs {{ number_format($p->monto_asignado + $p->monto_derivado_total, 2) }} · derivó Bs {{ number_format($p->monto_derivado_total, 2) }}</div>
        @endif
    </td>
    <td class="text-end num">
        <span class="{{ (float) $p->total_gastado > 0 ? '' : 'text-muted' }}">Bs {{ number_format($p->total_gastado, 2) }}</span>
    </td>
    <td class="text-end num">
        <span class="text-success fw-semibold">Bs {{ number_format($p->total_aprobado, 2) }}</span>
        @if($p->total_aprobado_obs > 0)
            <div class="pres-sub text-info">+ Bs {{ number_format($p->total_aprobado_obs, 2) }} c/obs</div>
        @endif
    </td>
    <td class="text-center">
        <span class="chip">{{ $p->total_comprobantes }}</span>
    </td>
    <td class="text-center">
        @if($p->periodo_cerrado)
            <span class="pres-sub"><i class="ri-lock-line"></i> Cerrado</span>
        @elseif($p->estado === 'activo')
            <span class="badge bg-soft-success text-success fs-10 px-2 py-1">Activo</span>
        @else
            <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1">Cerrado</span>
        @endif
    </td>
    <td class="text-center">
        <div class="d-flex align-items-center gap-1 justify-content-center">
            <a href="{{ route('admin.marketing.publicidad.index', ['gestion' => $p->gestion, 'mes' => $p->mes, 'quincena' => $p->quincena]) }}" class="btn btn-sm btn-icon btn-soft-primary" title="Ver comprobantes del periodo">
                <i class="ri-eye-line"></i>
            </a>
            @can('marketing.publicidad.presupuestos')
            @if($p->puede_eliminar)
            <button type="button" class="btn btn-sm btn-icon btn-soft-danger btn-eliminar-pres"
                    data-id="{{ $p->id }}"
                    data-asesor="{{ $p->ase_apellidos ? $p->ase_apellidos . ', ' . $p->ase_nombres : 'Sin asesor asignado' }}"
                    data-periodo="{{ $meses[$p->mes] }} {{ $p->gestion }} · Q{{ $p->quincena }}"
                    data-monto="{{ number_format($p->monto_asignado, 2) }}"
                    title="Eliminar presupuesto sin gastos">
                <i class="ri-delete-bin-6-line"></i>
            </button>
            @else
            <button type="button" class="btn btn-sm btn-icon btn-soft-secondary" disabled
                    title="{{ $p->total_comprobantes > 0 ? 'Tiene comprobantes: no se puede eliminar' : ($totalDerivados > 0 ? 'Tiene derivaciones: no se puede eliminar' : ($p->periodo_cerrado ? 'El periodo está cerrado' : 'No se puede eliminar')) }}">
                <i class="ri-delete-bin-6-line"></i>
            </button>
            @endif
            @endcan
        </div>
    </td>
</tr>
