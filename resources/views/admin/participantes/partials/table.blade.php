@if(count($participantes) > 0)
<div class="table-card-wrap">
    <div class="table-responsive">
        <table class="table mb-0 tabla-participantes">
            <thead>
                <tr>
                    <th style="width: 42px;" class="text-center">#</th>
                    <th>Participante</th>
                    <th style="width: 65px;" class="text-center">Sexo</th>
                    <th class="th-contacto" style="width: 200px;">Contacto</th>
                    <th>Programas</th>
                    <th style="width: 80px;" class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participantes as $idx => $p)
                @php
                    $isFemale = in_array(strtolower(trim((string) $p->sexo)), ['mujer', 'f', 'femenino'], true);
                    $sexoChar = $isFemale ? 'F' : 'M';
                    $avatarClass = $isFemale ? 'female' : '';
                    $sexoBadgeClass = $isFemale ? 'female' : 'male';
                    $ini = strtoupper(substr($p->nombres ?? '', 0, 1) . substr($p->apellidos ?? '', 0, 1));
                    $totalProgs = count($p->programas ?? []);
                    $visibleProgs = array_slice($p->programas ?? [], 0, 2);
                    $extraProgs = $totalProgs - count($visibleProgs);
                    $fullName = $p->apellidos . ', ' . $p->nombres;
                @endphp
                <tr>
                    <td class="text-center text-muted fw-bold">{{ ($page - 1) * 20 + $idx + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 cell-nombre">
                            <div class="participante-avatar {{ $avatarClass }}">{{ $ini }}</div>
                            <div style="min-width:0;">
                                <div class="nombre text-truncate" title="{{ $fullName }}">{{ $fullName }}</div>
                                <div class="ci-info"><i class="ri-profile-line align-bottom"></i> CI: {{ $p->ci }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($p->sexo)
                            <span class="sexo-badge {{ $sexoBadgeClass }}">{{ $sexoChar }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="cell-contacto td-contacto">
                        @if($p->celular)
                            <div class="line" title="{{ $p->celular }}"><i class="ri-phone-line"></i>{{ $p->celular }}</div>
                        @endif
                        @if($p->correo)
                            <div class="line" title="{{ $p->correo }}"><i class="ri-mail-line"></i>{{ $p->correo }}</div>
                        @endif
                        @if(!$p->celular && !$p->correo)
                            <span class="text-muted fst-italic" style="font-size:.73rem;">Sin contacto</span>
                        @endif
                    </td>
                    <td>
                        @if($totalProgs > 0)
                            <div class="programas-list">
                                @foreach($visibleProgs as $prog)
                                    <span class="programa-pill" title="{{ $prog }}">{{ $prog }}</span>
                                @endforeach
                                @if($extraProgs > 0)
                                    <span class="programa-pill programa-pill-more" title="{{ implode(' • ', array_slice($p->programas, 2)) }}">+{{ $extraProgs }} más</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted fst-italic" style="font-size:.73rem;"><i class="ri-information-line"></i> Sin programas</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-action-group">
                            @can('participantes.ver')
                            <a href="{{ route('admin.programas.participante-detalle', $p->id) }}" class="btn-action view" data-bs-toggle="tooltip" title="Ver detalle">
                                <i class="ri-eye-line"></i>
                            </a>
                            @endcan
                            <button type="button" class="btn-action edit btn-edit-participante"
                                    data-id="{{ $p->id }}"
                                    data-name="{{ $fullName }}"
                                    data-bs-toggle="tooltip" title="Editar participante">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
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
                <li class="page-item"><button class="page-link" data-page="1">1</button></li>
                @if($showFirstEllipsis)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
            @endif
            @for($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $i == $page ? 'active' : '' }}"><button class="page-link" data-page="{{ $i }}">{{ $i }}</button></li>
            @endfor
            @if($end < $lastPage)
                @if($showLastEllipsis)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                <li class="page-item"><button class="page-link" data-page="{{ $lastPage }}">{{ $lastPage }}</button></li>
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
    <h5 class="text-dark fw-bold mb-1">No se encontraron participantes</h5>
    <p class="text-muted mb-0">Prueba ajustando los filtros o limpia la búsqueda para ver todos los registros.</p>
</div>
@endif
