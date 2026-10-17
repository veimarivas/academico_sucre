@foreach($gastos as $idx => $g)
<tr>
    <td class="text-center text-muted fw-semibold">{{ $gastos->firstItem() + $idx }}</td>
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
        @if($g->fecha_inicio)
        <div class="text-muted fs-11 mt-1">
            @php
                $fi = \Carbon\Carbon::parse($g->fecha_inicio)->locale('es');
                $ff = $g->fecha_fin ? \Carbon\Carbon::parse($g->fecha_fin)->locale('es') : null;
            @endphp
            {{ $fi->isoFormat('D MMM') }}{{ $ff ? ' al '.$ff->isoFormat('D MMM') : '' }}
        </div>
        @endif
    </td>
    <td class="fs-11">{{ $g->asesor_apellidos }}, {{ $g->asesor_nombres }}</td>
    <td class="text-center fs-11">{{ $meses[$g->mes] ?? $g->mes }} {{ $g->gestion }}</td>
    <td class="text-center fs-11">{{ $g->plataforma ?? '—' }}</td>
    <td class="text-center fs-11">{{ $g->programa_nombre ?? '—' }}</td>
    <td class="text-end fw-bold">Bs {{ number_format($g->monto, 2) }}</td>
    <td class="text-center">
        @php
            $compOk = $g->archivo_path ? \Illuminate\Support\Facades\Storage::disk('public')->exists($g->archivo_path) : false;
            $debOk = $g->archivo_debito_path ? \Illuminate\Support\Facades\Storage::disk('public')->exists($g->archivo_debito_path) : false;
        @endphp
        @if($g->archivo_path)
        <div><span class="badge bg-primary px-1 py-0" style="font-size:.62rem!important;"><i class="ri-attachment-2"></i> Comp.</span>
        @if($compOk)
        <a href="{{ route('admin.marketing.publicidad.autogestion.archivo', ['id' => $g->id, 'tipo' => 'comprobante']) }}" class="file-preview btn-preview-file" data-ext="{{ pathinfo($g->archivo_original ?? $g->archivo_path, PATHINFO_EXTENSION) }}" title="{{ $g->archivo_original }}">
            <i class="ri-attachment-2"></i> Ver
        </a>
        @else
        <span class="text-danger fs-11" title="La ruta está registrada pero el archivo no se encontró en el servidor"><i class="ri-error-warning-line"></i> No encontrado</span>
        @endif
        </div>
        @endif
        @if($g->archivo_debito_path)
        <div class="mt-1"><span class="badge bg-warning text-dark px-1 py-0" style="font-size:.62rem!important;"><i class="ri-bank-card-line"></i> Débito</span>
        @if($debOk)
        <a href="{{ route('admin.marketing.publicidad.autogestion.archivo', ['id' => $g->id, 'tipo' => 'debito']) }}" class="file-preview btn-preview-file fs-11 text-warning" data-ext="{{ pathinfo($g->archivo_debito_original ?? $g->archivo_debito_path, PATHINFO_EXTENSION) }}" title="{{ $g->archivo_debito_original }}">
            <i class="ri-bank-card-line"></i> Ver
        </a>
        @else
        <span class="text-danger fs-11" title="La ruta está registrada pero el archivo no se encontró en el servidor"><i class="ri-error-warning-line"></i> No encontrado</span>
        @endif
        </div>
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
    <td class="text-center">
        <div class="d-flex gap-1 justify-content-center">
            @can('marketing.publicidad.subir')
            <button type="button" class="btn btn-sm btn-icon btn-soft-warning btn-edit-auto" data-id="{{ $g->id }}" title="Editar">
                <i class="ri-edit-2-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-soft-danger btn-delete-auto" data-id="{{ $g->id }}" data-desc="{{ Str::limit($g->descripcion, 40) }}" title="Eliminar" data-bs-toggle="modal" data-bs-target="#modalDeleteAutogestion">
                <i class="ri-delete-bin-7-line"></i>
            </button>
            @endcan
        </div>
    </td>
</tr>
@endforeach
