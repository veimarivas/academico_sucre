<table class="table mb-0 programas-table">
    <thead>
        <tr>
            <th class="text-center" style="width:38px;">#</th>
            <th>Programa</th>
            <th class="text-center" style="width:10%;">Estado</th>
            <th class="pv-col-resp" style="width:13%;">Responsable(s)</th>
            <th class="text-center pv-col-periodo" style="width:10%;">Periodo / Ses.</th>
            <th class="text-center" style="width:12%;">Inscritos</th>
            <th class="text-center pv-col-bajas" style="width:9%;">Bajas</th>
            <th class="text-center" style="width:200px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($programas as $programa)
        @php
            $reales = (int) ($programa->inscritos_reales ?? 0);
            $bajasOper = (int) ($programa->bajas_operativas ?? 0);
            $regulares = max(0, $reales - $bajasOper);
            $traspasos = (int) ($programa->traspasos ?? 0);
            $bajasTemp = (int) ($programa->bajas_tempranas ?? 0);
            $totalBajas = $bajasTemp + $bajasOper;
            $estadoBadge = match($programa->estado_nombre) {
                'En desarrollo' => 'success',
                'Concluido' => 'primary',
                'No Ejecutado' => 'danger',
                'Inscripciones' => 'info',
                default => 'secondary',
            };
        @endphp
        <tr>
            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
            <td>
                <div class="d-flex align-items-start gap-2">
                    @if($programa->moodle_link)
                        <a href="{{ $programa->moodle_link }}" target="_blank" rel="noopener" class="moodle-icon-link" title="Abrir aula Moodle del programa">
                            <i class="ri-graduation-cap-line"></i>
                        </a>
                    @endif
                    <div class="min-width-0">
                        <a href="{{ route('admin.programas.show', $programa->id) }}" class="pv-prog-name" title="{{ $programa->nombre }}">{{ $programa->nombre }}</a>
                        <div class="pv-badges">
                            <span class="mini-badge"><i class="ri-hashtag"></i>{{ $programa->codigo }}</span>
                            @if($programa->codigo_academico)
                                <span class="mini-badge"><i class="ri-school-line"></i>{{ $programa->codigo_academico }}</span>
                            @endif
                            @if($programa->area_nombre)
                                <span class="mini-badge area" style="--area-color: {{ $programa->area_color ?? '#6c757d' }};" title="{{ $programa->area_nombre }}">{{ $programa->area_nombre }}</span>
                            @endif
                            @if($programa->ceub)
                                <span class="mini-badge ceub"><i class="ri-gov-line"></i>CEUB</span>
                            @endif
                            <span class="mini-badge"><i class="ri-calendar-2-line"></i>{{ $programa->gestion }}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-center">
                <div class="pv-estado">
                    <span class="badge bg-soft-{{ $estadoBadge }} text-{{ $estadoBadge }}">{{ $programa->estado_nombre ?? $programa->desarrollo }}</span>
                    @if($programa->estado_nombre === 'Inscripciones')
                        <small class="text-{{ $programa->inscripciones_habilitadas ? 'success' : 'danger' }} fw-semibold" style="font-size:.6rem;">
                            <i class="ri-{{ $programa->inscripciones_habilitadas ? 'checkbox-circle' : 'close-circle' }}-line"></i> {{ $programa->inscripciones_habilitadas ? 'abiertas' : 'cerradas' }}
                        </small>
                    @endif
                </div>
            </td>
            <td class="pv-col-resp">
                @if($programa->responsables_raw)
                    @php
                        $responsables = explode(';;', $programa->responsables_raw);
                        $totalResp = count($responsables);
                    @endphp
                    <div class="pv-resp">
                        @foreach($responsables as $i => $resp)
                            @php $parts = explode('||', $resp); @endphp
                            @if(count($parts) >= 4 && $i < 2)
                                @php
                                    $nom = explode(' ', trim($parts[2]))[0] ?? '';
                                    $ape = explode(' ', trim($parts[1]))[0] ?? '';
                                @endphp
                                <span class="rv" title="{{ $parts[2] }} {{ $parts[1] }}">
                                    <span class="dot" style="background:{{ $parts[3] }};"></span>
                                    <span>{{ $nom }} {{ $ape }}</span>
                                </span>
                            @endif
                        @endforeach
                        @if($totalResp > 2)
                            <span class="mini-badge" title="{{ $totalResp }} responsables en total">+{{ $totalResp - 2 }} más</span>
                        @endif
                    </div>
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td class="text-center pv-col-periodo">
                @if($programa->fecha_inicio || $programa->fecha_conclusion)
                    <div class="text-muted" style="font-size:.7rem;line-height:1.2;">
                        {{ $programa->fecha_inicio ? \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') : '?' }}
                        @if($programa->fecha_inicio && $programa->fecha_conclusion) — @endif
                        {{ $programa->fecha_conclusion ? \Carbon\Carbon::parse($programa->fecha_conclusion)->format('d/m/Y') : '?' }}
                    </div>
                @endif
                <span class="mini-badge mt-1"><i class="ri-list-ordered"></i>{{ $programa->cantidad_sesiones ?? 0 }} ses.</span>
            </td>
            <td class="text-center">
                <div class="metric">
                    <span class="metric-main">{{ $programa->inscritos ?? 0 }}</span>
                    <span class="metric-sub">{{ $reales }} reales · {{ $regulares }} reg.@if($traspasos > 0) · {{ $traspasos }} trasp.@endif</span>
                </div>
            </td>
            <td class="text-center pv-col-bajas">
                <div class="metric">
                    <span class="metric-main {{ $totalBajas > 0 ? 'text-danger' : '' }}">{{ $totalBajas }}</span>
                    <span class="metric-sub">{{ $bajasTemp }} temp · {{ $bajasOper }} oper</span>
                </div>
            </td>
            <td>
                <div class="hstack gap-1 justify-content-center">
                    @isset($soloVer)
                    <a href="{{ route('admin.programas.show', $programa->id) }}" class="btn btn-sm btn-icon btn-icon-ghost" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">
                        <i class="ri-eye-line text-info"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-icon btn-icon-ghost btn-inscribir-oferta"
                        data-programa-id="{{ $programa->id }}"
                        data-programa-nombre="{{ $programa->nombre }}"
                        data-codigo="{{ $programa->codigo }}"
                        data-matricula="{{ $programa->matricula ?? 0 }}"
                        data-colegiatura="{{ $programa->colegiatura ?? 0 }}"
                        data-cantidad-modulos="{{ $programa->total_modulos ?? $programa->cantidad_sesiones ?? 0 }}"
                        data-modulos-nombres="{{ $programa->modulos_nombres ?? '' }}"
                        data-inscripciones-habilitadas="{{ $programa->inscripciones_habilitadas ?? 0 }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Inscribir participante">
                        <i class="ri-user-add-line text-success"></i>
                    </button>
                    @else
                    {{-- Ver detalle: todos los que acceden al módulo --}}
                    @can('programas.ver')
                    <a href="{{ route('admin.programas.show', $programa->id) }}" class="btn btn-sm btn-icon btn-icon-ghost" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle">
                        <i class="ri-eye-line text-info"></i>
                    </a>
                    @endcan

                    {{-- WhatsApp del programa: solo responsables asignados + Super Admin --}}
                    @if(auth()->user()->puedeWhatsappPrograma($programa->id))
                    <a href="{{ route('admin.programas.whatsapp.index', $programa->id) }}" class="btn btn-sm btn-icon btn-icon-ghost" data-bs-toggle="tooltip" data-bs-placement="top" title="WhatsApp del programa">
                        <i class="ri-whatsapp-line text-success"></i>
                    </a>
                    @endif

                    {{-- Acciones de administración: solo Super Admin --}}
                    @if(auth()->user()->hasRole('Super Admin'))
                    <button class="btn btn-sm btn-icon btn-icon-ghost btn-edit-programa" data-id="{{ $programa->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar programa">
                        <i class="ri-pencil-line text-warning"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-icon-ghost btn-asignar-resp" data-id="{{ $programa->id }}" data-nombre="{{ $programa->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Asignar responsables">
                        <i class="ri-team-line text-success"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-icon-ghost btn-costos-universidad" data-id="{{ $programa->id }}" data-nombre="{{ $programa->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Costos universidad">
                        <i class="ri-bank-line text-secondary"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-icon-ghost btn-delete-programa" data-id="{{ $programa->id }}" data-nombre="{{ $programa->nombre }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar programa">
                        <i class="ri-delete-bin-line text-danger"></i>
                    </button>
                    @endif
                    @endisset
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:var(--ui-surface-2);color:var(--ui-muted);font-size:1.6rem;">
                    <i class="ri-inbox-2-line"></i>
                </div>
                <h6 class="fw-bold mb-1">No se encontraron programas</h6>
                <p class="text-muted mb-0" style="font-size:.85rem;">Ajusta los filtros o registra un nuevo programa.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
