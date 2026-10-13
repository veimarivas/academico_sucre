<tr data-id="{{ $c->id }}">
    <td class="text-center text-muted fw-semibold">{{ $idx }}</td>
    <td class="fs-12" style="min-width:180px;">
        @php
            $fi = \Carbon\Carbon::parse($c->fecha_inicio)->locale('es');
            $ff = \Carbon\Carbon::parse($c->fecha_fin)->locale('es');
        @endphp
        <div>{{ $fi->isoFormat('dddd D [de] MMMM [del] YYYY') }}</div>
        <div class="text-muted">al {{ $ff->isoFormat('dddd D [de] MMMM [del] YYYY') }}</div>
    </td>
    <td class="fs-12">
        <div class="fw-semibold">{{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }}</div>
        @if(!$mismoTitular)
            <span class="badge fs-10 px-2 py-1" style="background:#fef3c7; color:#92400e;" title="Gasto derivado, distinto al titular del presupuesto"><i class="ri-share-forward-line"></i> Derivado</span>
        @else
            <span class="text-muted fs-11">Titular</span>
        @endif
    </td>
    <td class="fs-12">
        <div>{{ $c->descripcion }}</div>
        @if($c->campana_nombre)
            <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $c->campana_nombre }}</div>
        @endif
        @if($c->enlace_campana)
            <div class="fs-11"><a href="{{ $c->enlace_campana }}" target="_blank" rel="noopener"><i class="ri-external-link-line"></i> Ver campaña</a></div>
        @endif
        @if($c->pagina_facebook)
            <div class="fs-11"><a href="{{ $c->pagina_facebook }}" target="_blank" rel="noopener"><i class="ri-facebook-line"></i> Facebook</a></div>
        @endif
        @if($c->observaciones_contable)
            <div class="mt-1 p-1 rounded" style="background:#fff8e1; color:#92400e; font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $c->observaciones_contable }}</div>
        @endif
    </td>
    <td class="text-center fs-11">{{ $c->plataforma }}</td>
    <td class="text-center fs-11">{{ $c->programa_nombre ?? '—' }}</td>
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
        <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$c->archivo_path) }}" title="{{ $c->archivo_original }}">
            <i class="ri-attachment-2"></i> Ver
        </a>
        @if($c->archivo_debito_path)
        <br><a href="javascript:void(0)" class="file-preview btn-preview-file fs-11 text-warning" data-url="{{ asset('storage/'.$c->archivo_debito_path) }}" title="{{ $c->archivo_debito_original }}">
            <i class="ri-bank-card-line"></i> Débito
        </a>
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
    <td class="text-center">
        <div class="d-flex gap-1 justify-content-center">
            @can('marketing.publicidad.verificar')
            @if($c->estado === 'pendiente')
                <button class="btn btn-sm btn-soft-success" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $c->id }}" title="Verificar"><i class="ri-checkbox-circle-line"></i></button>
            @else
                <button class="btn btn-sm btn-soft-info" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $c->id }}" title="Cambiar verificación"><i class="ri-edit-2-line"></i></button>
            @endif
            @endcan
            @can('marketing.publicidad.subir')
            @if($c->estado !== 'aprobado')
            <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $c->id }}" title="Editar comprobante"><i class="ri-edit-2-line"></i></button>
            @endif
            @endcan
            @if($c->estado !== 'aprobado')
            <form method="POST" action="{{ route('admin.marketing.publicidad.destroy', $c->id) }}" onsubmit="return confirm('¿Eliminar este comprobante?');" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-soft-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
            </form>
            @endif
        </div>
    </td>
</tr>