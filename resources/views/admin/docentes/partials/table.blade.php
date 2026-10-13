@if(count($docentes) > 0)
<div class="table-card-wrap">
    <div class="table-responsive">
        <table class="table mb-0 tabla-docentes">
            <thead>
                <tr>
                    <th style="width: 42px;" class="text-center">#</th>
                    <th>Docente</th>
                    <th style="width: 200px;">Contacto</th>
                    <th style="width: 75px;" class="text-center">Módulos</th>
                    <th style="width: 150px;" class="text-center">Programas</th>
                    <th style="width: 170px;">Banco / Cuenta</th>
                    <th style="width: 110px;" class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($docentes as $idx => $d)
                @php
                    $ini = strtoupper(substr($d->nombres ?? '', 0, 1) . substr($d->apellidos ?? '', 0, 1));
                    $progsCount = count($d->programas ?? []);
                @endphp
                <tr>
                    <td class="text-center text-muted fw-bold">{{ ($page - 1) * 20 + $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 cell-nombre">
                            <div class="docente-avatar">{{ $ini }}</div>
                            <div style="min-width:0;">
                                <div class="nombre text-truncate" title="{{ $d->apellidos }}, {{ $d->nombres }}">{{ $d->apellidos }}, {{ $d->nombres }}</div>
                                <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $d->ci }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="cell-contacto">
                        @if($d->celular)
                            <div class="line" title="{{ $d->celular }}"><i class="ri-phone-line"></i>{{ $d->celular }}</div>
                        @endif
                        @if($d->correo)
                            <div class="line" title="{{ $d->correo }}"><i class="ri-mail-line"></i>{{ $d->correo }}</div>
                        @endif
                        @if(!$d->celular && !$d->correo)
                            <span class="text-muted fst-italic" style="font-size:.75rem;">Sin contacto</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="modulos-count-badge">{{ $d->total_modulos }}</span>
                    </td>
                    <td class="text-center">
                        @if($progsCount > 0)
                            <button class="btn-programas-pill btn-ver-modulos" data-docente-id="{{ $d->id }}" data-docente-nombre="{{ $d->apellidos }}, {{ $d->nombres }}" title="Ver programas y módulos">
                                <i class="ri-booklet-line"></i> {{ $progsCount }} programa{{ $progsCount > 1 ? 's' : '' }}
                            </button>
                        @else
                            <span class="text-muted fst-italic" style="font-size:.72rem;">Sin asignación</span>
                        @endif
                    </td>
                    <td class="cell-banco">
                        @if($d->banco)
                            <div class="banco-nombre text-truncate" title="{{ $d->banco }}"><i class="ri-bank-line text-muted me-1"></i>{{ $d->banco }}</div>
                            @if($d->n_cuenta)
                                <span class="cuenta-num text-truncate d-block" title="{{ $d->n_cuenta }}">{{ $d->n_cuenta }}</span>
                            @endif
                        @else
                            <span class="text-muted fst-italic" style="font-size:.72rem;">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="hstack gap-1 justify-content-center">
                            @can('docentes.ver')
                            <a href="{{ route('admin.docentes.show', $d->id) }}" class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" title="Ver detalle">
                                <i class="ri-eye-line"></i>
                            </a>
                            @endcan
                            @can('docentes.credenciales')
                            <button class="btn btn-sm btn-icon btn-soft-info btn-credenciales" data-docente-id="{{ $d->id }}" data-bs-toggle="tooltip" title="Credenciales Moodle">
                                <i class="ri-shield-keyhole-line"></i>
                            </button>
                            @endcan
                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Responsable Académico']))
                             <button class="btn btn-sm btn-icon btn-soft-warning btn-edit-docente" data-docente-id="{{ $d->id }}" data-bs-toggle="tooltip" title="Editar">
                                 <i class="ri-pencil-line"></i>
                             </button>
                             @endif
                             <button class="btn btn-sm btn-icon btn-soft-success btn-estudios" data-docente-id="{{ $d->id }}" data-docente-nombre="{{ $d->apellidos }}, {{ $d->nombres }}" data-bs-toggle="tooltip" title="Estudios">
                                 <i class="ri-graduation-cap-line"></i>
                             </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($lastPage > 1)
@php
    $range = 2;
    $start = max(1, $page - $range);
    $end = min($lastPage, $page + $range);
    $showFirstEllipsis = $start > 2;
    $showLastEllipsis = $end < $lastPage - 1;
@endphp
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 px-1">
    <small class="text-muted">
        <i class="ri-file-list-3-line align-bottom me-1"></i>
        Página <strong class="text-dark">{{ $page }}</strong> de <strong class="text-dark">{{ $lastPage }}</strong> · {{ $total }} registros
    </small>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                <button class="page-link" data-page="1" title="Primera página"><i class="ri-skip-back-mini-line"></i></button>
            </li>
            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                <button class="page-link" data-page="{{ $page - 1 }}" title="Anterior"><i class="ri-arrow-left-s-line"></i></button>
            </li>

            @if($start > 1)
                <li class="page-item">
                    <button class="page-link" data-page="1">1</button>
                </li>
                @if($showFirstEllipsis)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
            @endif

            @for($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $i == $page ? 'active' : '' }}">
                    <button class="page-link" data-page="{{ $i }}">{{ $i }}</button>
                </li>
            @endfor

            @if($end < $lastPage)
                @if($showLastEllipsis)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
                <li class="page-item">
                    <button class="page-link" data-page="{{ $lastPage }}">{{ $lastPage }}</button>
                </li>
            @endif

            <li class="page-item {{ $page >= $lastPage ? 'disabled' : '' }}">
                <button class="page-link" data-page="{{ $page + 1 }}" title="Siguiente"><i class="ri-arrow-right-s-line"></i></button>
            </li>
            <li class="page-item {{ $page >= $lastPage ? 'disabled' : '' }}">
                <button class="page-link" data-page="{{ $lastPage }}" title="Última página"><i class="ri-skip-forward-mini-line"></i></button>
            </li>
        </ul>
    </nav>
</div>
@endif
@else
<div class="tabla-empty">
    <div class="empty-icon"><i class="ri-user-search-line"></i></div>
    <h5 class="text-dark fw-bold mb-1">No se encontraron docentes</h5>
    <p class="text-muted mb-0">Prueba ajustando la búsqueda o limpia los filtros para ver todos los registros.</p>
</div>
@endif
