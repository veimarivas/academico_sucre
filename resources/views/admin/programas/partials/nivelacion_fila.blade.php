{{--
    Fila de participante en la tabla de nivelación de un módulo.
    Recibe: $r (objeto fila) y $idx (número de fila).
    Reutilizada tanto en el render inicial como en las respuestas AJAX
    de subir/validar comprobante (sin recargar la página).
--}}
<tr {{ $r->tiene_cuota ? 'id="niv-fila-' . $r->mp_id . '"' : '' }} class="{{ $r->tiene_cuota ? '' : 'table-light' }}">
    <td class="text-center text-muted">{{ $idx }}</td>
    <td>
        <div class="fw-semibold text-dark fs-13">{{ $r->apellidos }}, {{ $r->nombres }}</div>
        <div class="text-muted fs-12"><i class="ri-passport-line"></i> {{ $r->ci }}</div>
    </td>
    <td class="text-center">
        @php $notaMod = (float) $r->nota_modulo; @endphp
        <span class="badge {{ $notaMod >= 71 ? 'bg-soft-secondary text-dark' : 'bg-soft-danger text-danger' }}">{{ number_format($notaMod, 2) }}</span>
    </td>
    <td class="text-center">
        @if($r->nota_nivelacion && $r->nota_nivelacion > 0)
        @php $notaNiv = (float) $r->nota_nivelacion; @endphp
        <span class="badge {{ $notaNiv >= 71 ? 'bg-soft-secondary text-dark' : 'bg-soft-danger text-danger' }}">{{ number_format($notaNiv, 2) }}</span>
        @else
        <span class="text-muted">—</span>
        @endif
    </td>
    <td class="text-center">
        @if($r->tiene_cuota)
        <span class="fw-semibold">Bs {{ number_format($r->deuda, 2) }}</span>
        @else
        <span class="badge bg-soft-secondary text-secondary">Sin cuota</span>
        @endif
    </td>
    <td class="text-center">
        @if($r->tiene_cuota && $r->saldo !== null && $r->saldo <= 0)
        <span class="badge bg-soft-success text-success"><i class="ri-checkbox-circle-line"></i> Pagado</span>
        @elseif($r->tiene_cuota)
        <span class="badge bg-soft-warning text-warning"><i class="ri-time-line"></i> Debe Bs {{ number_format($r->saldo, 2) }}</span>
        @else
        <span class="text-muted">—</span>
        @endif
    </td>
    <td class="text-center">
        @if($r->tiene_cuota)
            @if($r->comprobante_pago)
            <div class="d-flex flex-column align-items-center gap-1">
                <span class="badge bg-soft-primary text-primary"><i class="ri-file-line me-1"></i>Subido</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-soft-info btn-icon btn-ver-comprobante-nivelacion" title="Ver comprobante"
                        data-url="{{ asset('storage/' . $r->comprobante_pago) }}"
                        data-name="{{ $r->comprobante_original ?? 'comprobante' }}"
                        data-participante="{{ $r->apellidos }}, {{ $r->nombres }}">
                        <i class="ri-eye-line"></i>
                    </button>
                    @if((int) $r->comprobante_validado !== 1)
                        @can('contable.cobrar')
                        <button type="button" class="btn btn-sm btn-soft-warning btn-icon btn-subir-comprobante-nivelacion" title="Reemplazar comprobante" data-cuota-id="{{ $r->mp_id }}">
                            <i class="ri-upload-2-line"></i>
                        </button>
                        @endcan
                    @endif
                </div>
            </div>
            @else
                @can('contable.cobrar')
                <button type="button" class="btn btn-sm btn-soft-primary d-flex align-items-center gap-1 btn-subir-comprobante-nivelacion" data-cuota-id="{{ $r->mp_id }}">
                    <i class="ri-upload-2-line"></i> Subir
                </button>
                @endcan
            @endif
        @else
        <span class="text-muted">—</span>
        @endif
    </td>
    <td class="text-center">
        @if($r->tiene_cuota && $r->comprobante_validado === 1)
        <span class="badge bg-soft-success text-success"><i class="ri-check-double-line"></i> Validado</span>
        <div class="text-muted fs-11 mt-1">Bs {{ number_format($r->pago_docente_cuota, 2) }}</div>
        @elseif($r->tiene_cuota && $r->comprobante_pago)
            @can('contable.cobrar')
            <button type="button" class="btn btn-sm btn-soft-success d-flex align-items-center gap-1 btn-validar-pago-nivelacion"
                data-cuota-id="{{ $r->mp_id }}"
                data-url="{{ asset('storage/' . $r->comprobante_pago) }}"
                data-name="{{ $r->comprobante_original ?? 'comprobante' }}"
                data-participante="{{ $r->apellidos }}, {{ $r->nombres }}">
                <i class="ri-check-line"></i> Validar pago
            </button>
            @endcan
        @else
        <span class="text-muted">—</span>
        @endif
    </td>
</tr>