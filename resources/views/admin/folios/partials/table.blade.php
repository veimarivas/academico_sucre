<div class="table-card-wrap">
    <table class="table table-sm table-bordered align-middle mb-0 tabla-folios">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Nombre</th>
                <th style="width:100px;">Fecha</th>
                <th style="width:140px;">Conceptos</th>
                <th style="width:80px;" class="text-center">Particip.</th>
                <th style="width:80px;" class="text-center">Estado</th>
                <th style="width:100px;" class="text-center">Total</th>
                <th style="width:120px;" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($folios as $idx => $fol)
            @php
                $folConceptos = json_decode($fol->concepto, true) ?? [];
                $conceptosList = is_array($folConceptos) ? implode(', ', array_map(function($c) {
                    return match($c) { 'Inscripcion'=>'Insc.', 'Matricula'=>'Mat.', 'Colegiatura'=>'Col.', 'Certificacion'=>'Cert.', default => $c };
                }, $folConceptos)) : '—';
            @endphp
            <tr>
                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                <td>
                    <a href="{{ route('admin.folios.show', $fol->id) }}" class="fw-semibold fs-13 text-decoration-none">{{ $fol->nombre }}</a>
                </td>
                <td>{{ \Carbon\Carbon::parse($fol->fecha_registro)->format('d/m/Y') }}</td>
                <td>
                    @if(is_array($folConceptos) && count($folConceptos) > 0)
                        @foreach($folConceptos as $fc)
                            <span class="badge bg-soft-info text-info fs-10 me-1">{{ $fc }}</span>
                        @endforeach
                    @else
                        <span class="text-muted fs-11">—</span>
                    @endif
                </td>
                <td class="text-center">{{ $fol->participantes_count }}</td>
                <td class="text-center">
                    @if($fol->status === 'abierto')
                        <span class="badge bg-soft-success text-success fs-10"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                    @else
                        <span class="badge bg-soft-danger text-danger fs-10"><i class="ri-lock-line me-1"></i>Cerrado</span>
                    @endif
                </td>
                <td class="text-center fw-bold text-primary">Bs {{ number_format($fol->total_general, 2) }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('admin.folios.show', $fol->id) }}" class="btn btn-sm btn-soft-info px-2" title="Ver detalle">
                            <i class="ri-eye-line"></i>
                        </a>
                        <a href="{{ route('admin.folios.edit', $fol->id) }}" class="btn btn-sm btn-soft-primary px-2" title="Editar">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-soft-danger px-2 btn-delete-folio"
                            data-id="{{ $fol->id }}" data-name="{{ $fol->nombre }}" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
                        <p class="mb-0">No hay folios registrados</p>
                        <small>Presione "Nuevo Folio" para crear el primer registro</small>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
