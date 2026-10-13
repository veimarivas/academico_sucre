@if(count($areas) > 0)
<div class="table-card-wrap">
    <div class="table-responsive">
        <table class="table mb-0 tabla-areas">
            <thead>
                <tr>
                    <th style="width: 42px;" class="text-center">#</th>
                    <th>Área</th>
                    <th style="width: 80px;" class="text-center">Color</th>
                    <th style="width: 140px;" class="text-center">Programas</th>
                    <th>Descripción</th>
                    <th style="width: 95px;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($areas as $idx => $a)
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tipo-icon" style="background: {{ $a->color ?? '#6c757d' }};"><i class="ri-apps-2-line"></i></div>
                            <div style="min-width:0;">
                                <div class="area-nombre text-truncate" title="{{ $a->nombre }}">{{ $a->nombre }}</div>
                                <div class="area-id"><i class="ri-hashtag align-bottom"></i> ID: {{ $a->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="col-color-preview" style="background: {{ $a->color ?? '#6c757d' }};"></span>
                    </td>
                    <td class="text-center">
                        @if($a->programas_count > 0)
                            <span class="metric-chip metric-progs"><i class="ri-booklet-line"></i>{{ $a->programas_count }} programa{{ $a->programas_count > 1 ? 's' : '' }}</span>
                        @else
                            <span class="metric-chip metric-empty"><i class="ri-information-line"></i>Sin uso</span>
                        @endif
                    </td>
                    <td>
                        @if(trim((string)($a->descripcion ?? '')) !== '')
                            <div class="descripcion-cell" title="{{ $a->descripcion }}">{{ $a->descripcion }}</div>
                        @else
                            <span class="text-muted fst-italic" style="font-size:.78rem;">Sin descripción</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="hstack gap-1 justify-content-center">
                            @can('areas.ver')
                            <a href="{{ route('admin.programas.index', ['area_id' => $a->id]) }}" class="btn btn-sm btn-icon btn-soft-info" data-bs-toggle="tooltip" title="Ver programas del área">
                                <i class="ri-eye-line"></i>
                            </a>
                            @endcan
                            @if(auth()->user()->hasRole('Super Admin'))
                            <button class="btn btn-sm btn-icon btn-soft-warning btn-edit-area" data-id="{{ $a->id }}" data-bs-toggle="tooltip" title="Editar">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-soft-danger btn-delete-area" data-id="{{ $a->id }}" data-nombre="{{ $a->nombre }}" data-bs-toggle="tooltip" title="Eliminar">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="tabla-empty">
    <div class="empty-icon"><i class="ri-apps-2-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">Sin áreas registradas</h5>
    <p class="text-muted mb-0">Crea la primera área con el botón "Nueva Área".</p>
</div>
@endif
