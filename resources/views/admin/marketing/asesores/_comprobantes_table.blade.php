@foreach($comprobantes as $comp)
    @php $globalIdx = ($comprobantes->currentPage() - 1) * $comprobantes->perPage() + $loop->iteration; @endphp
    <tr data-id="{{ $comp->id }}">
        <td class="text-center text-muted fw-semibold">{{ $globalIdx }}</td>
        <td class="fs-12" style="min-width:170px;">
            @php
                $fi = \Carbon\Carbon::parse($comp->fecha_inicio)->locale('es');
                $ff = \Carbon\Carbon::parse($comp->fecha_fin)->locale('es');
            @endphp
            <div>{{ $fi->isoFormat('D [de] MMM [del] YYYY') }}</div>
            <div class="text-muted fs-11">al {{ $ff->isoFormat('D [de] MMM [del] YYYY') }}</div>
        </td>
        <td class="fs-12">
            <span class="badge badge-quincena-{{ $comp->quincena }} fs-10 px-2 py-1">
                Q{{ $comp->quincena }} {{ $mesesPub[$comp->mes] ?? $comp->mes }} {{ $comp->gestion }}
            </span>
        </td>
        <td class="fs-12">
            <div>{{ $comp->descripcion }}</div>
            @if($comp->campana_nombre)
                <div class="text-muted fs-11"><i class="ri-hashtag"></i> {{ $comp->campana_nombre }}</div>
            @endif
            @if($comp->enlace_campana)
                <div class="fs-11"><a href="{{ $comp->enlace_campana }}" target="_blank" rel="noopener"><i class="ri-external-link-line"></i> Ver campaña</a></div>
            @endif
            @if($comp->pagina_facebook)
                <div class="fs-11"><a href="{{ $comp->pagina_facebook }}" target="_blank" rel="noopener"><i class="ri-facebook-line"></i> Facebook</a></div>
            @endif
            @if($comp->observaciones_contable)
                <div class="mt-1 p-1 rounded" style="background:#fff8e1;color:#92400e;font-size:.72rem;"><i class="ri-chat-4-line"></i> {{ $comp->observaciones_contable }}</div>
            @endif
        </td>
        <td class="text-center"><span class="plat-chip">{{ $comp->plataforma }}</span></td>
        <td class="text-center fs-11">{{ $comp->programa_nombre ?? '—' }}</td>
        <td class="text-end fw-bold">Bs {{ number_format($comp->monto, 2) }}</td>
        <td class="text-center">
            <a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="{{ asset('storage/'.$comp->archivo_path) }}" title="{{ $comp->archivo_original }}">
                <i class="ri-attachment-2"></i> Ver
            </a>
        </td>
        <td class="text-center">
            @if($comp->estado === 'pendiente')
                <span class="estado-dot pendiente"><i class="ri-time-line"></i> Pendiente</span>
            @elseif($comp->estado === 'aprobado')
                <span class="estado-dot aprobado"><i class="ri-check-line"></i> Aprobado</span>
            @else
                <span class="estado-dot rechazado"><i class="ri-close-line"></i> Rechazado</span>
            @endif
            @if($comp->verificador_nombre)
                <div class="fs-10 text-muted mt-1">por {{ $comp->verificador_nombre }}</div>
            @endif
        </td>
        <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
                @can('marketing.publicidad.verificar')
                @if($comp->estado === 'pendiente')
                    <button class="btn btn-sm btn-soft-success" data-bs-toggle="modal" data-bs-target="#modalVerifAsesor{{ $comp->id }}" title="Verificar"><i class="ri-checkbox-circle-line"></i></button>
                @else
                    <button class="btn btn-sm btn-soft-info" data-bs-toggle="modal" data-bs-target="#modalVerifAsesor{{ $comp->id }}" title="Cambiar verificación"><i class="ri-edit-2-line"></i></button>
                @endif
                @endcan
                @can('marketing.publicidad.subir')
                @if($comp->estado !== 'aprobado')
                <form method="POST" action="{{ route('admin.marketing.publicidad.destroy', $comp->id) }}" onsubmit="return confirm('¿Eliminar este comprobante?');" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-soft-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                </form>
                @endif
                @endcan
            </div>
        </td>
    </tr>
@endforeach
