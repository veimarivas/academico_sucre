@if(count($tipos) > 0)
<div class="table-card-wrap">
    <div class="table-responsive">
        <table class="table mb-0 tabla-tipos">
            <thead>
                <tr>
                    <th style="width: 42px;" class="text-center">#</th>
                    <th>Tipo</th>
                    <th style="width: 130px;" class="text-center">Cant. Módulos</th>
                    <th style="width: 110px;" class="text-center">Puntos</th>
                    <th style="width: 150px;" class="text-center">Pago Docente Niv.</th>
                    <th style="width: 140px;" class="text-center">Programas</th>
                    <th>Descripción</th>
                    <th style="width: 95px;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $idx => $t)
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tipo-icon"><i class="ri-price-tag-3-line"></i></div>
                            <div style="min-width:0;">
                                <div class="tipo-nombre text-truncate" title="{{ $t->nombre }}">{{ $t->nombre }}</div>
                                <div class="tipo-id"><i class="ri-hashtag align-bottom"></i> ID: {{ $t->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="metric-chip metric-mods"><i class="ri-stack-line"></i>{{ $t->cantidad_modulo }}</span>
                    </td>
                    <td class="text-center">
                        @if((float) ($t->puntos ?? 0) > 0)
                            <span class="metric-chip metric-pts"><i class="ri-medal-line"></i>{{ number_format((float) $t->puntos, 2) }}</span>
                        @else
                            <span class="metric-chip metric-empty">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if((float) ($t->pago_docente_nivelacion ?? 0) > 0)
                            <span class="metric-chip metric-pago-niv"><i class="ri-money-dollar-circle-line"></i>Bs {{ number_format((float) $t->pago_docente_nivelacion, 2) }}</span>
                        @else
                            <span class="metric-chip metric-empty">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($t->programas_count > 0)
                            <span class="metric-chip metric-progs"><i class="ri-booklet-line"></i>{{ $t->programas_count }} programa{{ $t->programas_count > 1 ? 's' : '' }}</span>
                        @else
                            <span class="metric-chip metric-empty"><i class="ri-information-line"></i>Sin uso</span>
                        @endif
                    </td>
                    <td>
                        @if(trim((string)($t->descripcion ?? '')) !== '')
                            <div class="descripcion-cell" title="{{ $t->descripcion }}">{{ $t->descripcion }}</div>
                        @else
                            <span class="text-muted fst-italic" style="font-size:.78rem;">Sin descripción</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="hstack gap-1 justify-content-center">
                            @can('tipos.ver')
                            <a href="{{ route('admin.programas.index', ['tipo_id' => $t->id]) }}" class="btn btn-sm btn-icon btn-soft-info" data-bs-toggle="tooltip" title="Ver programas del tipo">
                                <i class="ri-eye-line"></i>
                            </a>
                            @endcan
                            @if(auth()->user()->hasRole('Super Admin'))
                            <button class="btn btn-sm btn-icon btn-soft-warning btn-edit-tipo" data-id="{{ $t->id }}" data-bs-toggle="tooltip" title="Editar">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn btn-sm btn-icon btn-soft-danger btn-delete-tipo" data-id="{{ $t->id }}" data-nombre="{{ $t->nombre }}" data-bs-toggle="tooltip" title="Eliminar">
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
    <div class="empty-icon"><i class="ri-price-tag-3-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">Sin tipos registrados</h5>
    <p class="text-muted mb-0">Crea el primer tipo de programa con el botón "Nuevo Tipo".</p>
</div>
@endif
