@extends('admin.layouts.master')

@section('title', 'Usuarios - Seguridad - ' . config('app.name'))

@include('admin.seguridad._styles')

@section('content')
@php
    $totalUsers = $users->count();
    $enLinea = $users->filter(fn($u) => $u->isOnline())->count();
    $conRol = $users->filter(fn($u) => $u->roles->isNotEmpty())->count();
    $sinRol = $totalUsers - $conRol;
    $conEsam = $users->filter(fn($u) => $u->whatsapp_autorizado && trim((string) $u->whatsapp_numero) !== '')->count();

    // Color por rol: fondo sólido con texto blanco (alto contraste).
    $roleColors = [
        ['#405189', '#ffffff'],
        ['#0a6a90', '#ffffff'],
        ['#128c7e', '#ffffff'],
        ['#b7791f', '#ffffff'],
        ['#c11574', '#ffffff'],
        ['#7c3aed', '#ffffff'],
        ['#b42318', '#ffffff'],
        ['#0e7490', '#ffffff'],
        ['#4d7c0f', '#ffffff'],
        ['#9333ea', '#ffffff'],
    ];
@endphp
<div class="sec-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-team-line"></i></div>
        <div>
            <h4>Usuarios</h4>
            <p class="hero-sub">Gestiona los usuarios y los roles asignados a cada uno.</p>
            <div class="sec-hero-badges">
                <span class="sec-hero-badge"><i class="ri-group-line"></i> {{ $totalUsers }} usuarios</span>
                <span class="sec-hero-badge"><i class="ri-record-circle-line"></i> {{ $enLinea }} en línea</span>
                <span class="sec-hero-badge"><i class="ri-shield-user-line"></i> {{ $conRol }} con rol</span>
                <span class="sec-hero-badge"><i class="ri-whatsapp-line"></i> {{ $conEsam }} con Seguimiento ESAM</span>
            </div>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item">Seguridad</li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
</div>

<div class="sec-kpis">
    <div class="sec-kpi" style="--k:#405189;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Usuarios</div><div class="val">{{ $totalUsers }}</div><div class="sub">registrados</div></div>
            <span class="ic" style="background:#eef1f8;color:#405189;"><i class="ri-group-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#0ab39c;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">En línea</div><div class="val">{{ $enLinea }}</div><div class="sub">últimos 5 min</div></div>
            <span class="ic" style="background:#e6f7ee;color:#0a7d5a;"><i class="ri-record-circle-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#0a6a90;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Con rol</div><div class="val">{{ $conRol }}</div><div class="sub">asignados</div></div>
            <span class="ic" style="background:#e8f4fd;color:#1f6fbf;"><i class="ri-shield-check-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#cba135;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Sin rol</div><div class="val">{{ $sinRol }}</div><div class="sub">pendientes</div></div>
            <span class="ic" style="background:#fff7e6;color:#b7791f;"><i class="ri-error-warning-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#128c7e;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Con ESAM</div><div class="val">{{ $conEsam }}</div><div class="sub">autorizados</div></div>
            <span class="ic" style="background:#e6f7ee;color:#0a7d5a;"><i class="ri-whatsapp-line"></i></span>
        </div>
    </div>
</div>

<div class="sec-card">
    <div class="card-header">
        <h5><i class="ri-user-line"></i> Listado de usuarios <span class="count-badge">{{ $totalUsers }}</span></h5>
        @can('usuarios.crear')
        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalUserCreate"><i class="ri-user-add-line"></i> Nuevo usuario</button>
        @endcan
    </div>
    <div class="card-body">
        <div class="sec-search">
            <i class="ri-search-line search-icon"></i>
            <input type="text" id="userSearch" class="form-control" placeholder="Buscar por nombre, usuario o correo...">
        </div>

        <ul class="user-tabs" id="userTabs">
            <li><button type="button" class="user-tab active" data-role="all">Todos <span class="tab-count">{{ $totalUsers }}</span></button></li>
            @foreach($roles as $rol)
                @php $rolCount = $users->filter(fn($u) => $u->roles->contains('id', $rol->id))->count(); @endphp
                @if($rolCount > 0)
                <li><button type="button" class="user-tab" data-role="{{ $rol->name }}">{{ $rol->name }} <span class="tab-count">{{ $rolCount }}</span></button></li>
                @endif
            @endforeach
            @if($sinRol > 0)
            <li><button type="button" class="user-tab" data-role="__sin__">Sin rol <span class="tab-count">{{ $sinRol }}</span></button></li>
            @endif
        </ul>

        <div class="user-list" id="userList">
            @forelse($users as $u)
                @php
                    $iniU = strtoupper(collect(explode(' ', trim($u->fullname ?? $u->username)))->filter()->take(2)->map(fn($x)=>substr($x,0,1))->implode(''));
                    $search = strtolower(($u->fullname ?? '').' '.$u->username.' '.($u->email ?? ''));
                    $rolesCsv = strtolower($u->roles->pluck('name')->implode(','));
                    $isOnline = $u->isOnline();
                    $lastSeen = $u->lastSeen();
                @endphp
                <article class="user-item user-tr" data-name="{{ $search }}" data-roles="{{ $rolesCsv }}">
                    <div class="user-item-main">
                        <span class="user-avatar">{{ $iniU }}</span>
                        <div style="min-width:0;">
                            <div class="user-name">{{ $u->fullname ?? $u->username }}</div>
                            <div class="user-sub"><i class="ri-at-line"></i> {{ $u->username }}</div>
                        </div>
                    </div>

                    <div class="user-item-grid">
                        <div class="user-field">
                            <span class="user-field-lbl"><i class="ri-mail-line"></i> Correo</span>
                            <span class="user-field-val" title="{{ $u->email }}">{{ $u->email ?? '—' }}</span>
                        </div>
                        <div class="user-field">
                            <span class="user-field-lbl"><i class="ri-user-star-line"></i> Asesor / Responsable</span>
                            <span class="user-field-val">
                                @if($u->asesor)
                                    <span class="role-chip" style="background:#1f6fbf;color:#fff;border-color:#1f6fbf;" title="Asesor: {{ $u->asesor->apellidos }}, {{ $u->asesor->nombres }}"><i class="ri-user-voice-line"></i> {{ $u->asesor->apellidos }}, {{ $u->asesor->nombres }}</span>
                                @elseif($u->responsable && (int)$u->responsable_id > 0)
                                    <span class="role-chip" style="background:#b54708;color:#fff;border-color:#b54708;" title="Responsable: {{ $u->responsable->apellidos }}, {{ $u->responsable->nombres }}"><i class="ri-user-star-line"></i> {{ $u->responsable->apellidos }}, {{ $u->responsable->nombres }}</span>
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">—</span>
                                @endif
                            </span>
                        </div>
                        <div class="user-field">
                            <span class="user-field-lbl"><i class="ri-shield-user-line"></i> Roles</span>
                            <span class="user-field-val">
                                @forelse($u->roles as $rol)
                                    @php $rc = $roleColors[abs(crc32(strtolower($rol->name))) % count($roleColors)]; @endphp
                                    <span class="role-chip" style="background:{{ $rc[0] }};color:{{ $rc[1] }};border-color:{{ $rc[0] }};"><i class="ri-shield-check-line"></i> {{ $rol->name }}</span>
                                @empty
                                    <span class="role-chip muted"><i class="ri-question-line"></i> Sin rol</span>
                                @endforelse
                            </span>
                        </div>
                        <div class="user-field">
                            <span class="user-field-lbl"><i class="ri-pulse-line"></i> Estado</span>
                            <span class="user-field-val">
                                @if ($isOnline)
                                    <span class="estado-pill on" title="En línea ahora"><span class="dot"></span> En línea</span>
                                @elseif (str_starts_with($lastSeen ?? '', 'Hace'))
                                    <span class="estado-pill recent" title="Última actividad: {{ $lastSeen }}"><span class="dot"></span> {{ $lastSeen }}</span>
                                @elseif ($lastSeen)
                                    <span class="estado-pill none" title="Última actividad: {{ $lastSeen }}"><span class="dot"></span> {{ $lastSeen }}</span>
                                @else
                                    <span class="estado-pill none" title="Sin actividad registrada"><span class="dot"></span> Sin actividad</span>
                                @endif
                            </span>
                        </div>
                        <div class="user-field">
                            <span class="user-field-lbl"><i class="ri-whatsapp-line"></i> Seguimiento ESAM</span>
                            <span class="user-field-val">
                                @if($u->whatsapp_autorizado && trim((string) $u->whatsapp_numero) !== '')
                                    <span class="esam-pill on"><i class="ri-checkbox-circle-fill"></i> Habilitado</span>
                                    <span class="esam-meta">{{ $u->whatsapp_numero }}@if($u->whatsapp_area) · {{ \App\Models\User::AREAS_WHATSAPP[$u->whatsapp_area] ?? $u->whatsapp_area }}@endif</span>
                                @elseif(trim((string) $u->whatsapp_numero) !== '')
                                    <span class="esam-pill warn"><i class="ri-error-warning-fill"></i> Sin autorizar</span>
                                    <span class="esam-meta">{{ $u->whatsapp_numero }}</span>
                                @else
                                    <span class="esam-pill off"><i class="ri-close-circle-line"></i> Sin número</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="user-item-actions">
                        @can('usuarios.editar')
                        <button class="btn-icon btn-edit" data-bs-toggle="modal" data-bs-target="#modalUserEdit-{{ $u->id }}" title="Editar"><i class="ri-edit-line"></i></button>
                        <button class="btn-icon btn-reset" title="Restablecer contraseña" data-reset-id="{{ $u->id }}" data-reset-name="{{ $u->fullname ?? $u->username }}" data-bs-toggle="modal" data-bs-target="#modalResetPassword"><i class="ri-lock-unlock-line"></i></button>
                        @endcan
                        @can('usuarios.eliminar')
                        @if($u->id !== auth()->id())
                        <button class="btn-icon btn-del" title="Eliminar" data-user-id="{{ $u->id }}" data-user-name="{{ $u->fullname ?? $u->username }}" data-bs-toggle="modal" data-bs-target="#modalDeleteUser"><i class="ri-delete-bin-line"></i></button>
                        @endif
                        @endcan
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <div class="empty-icon"><i class="ri-user-search-line"></i></div>
                    <h5 class="text-dark fw-bold mb-1">Sin usuarios</h5>
                    <p class="text-muted mb-0">No hay usuarios registrados.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal crear --}}
<div class="modal fade modal-sec" id="modalUserCreate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.seguridad.usuarios.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title"><i class="ri-user-add-line me-1"></i> Nuevo usuario</h5>
                        <p class="modal-subtitle">Datos, acceso, roles y WhatsApp corporativo.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-id-card-line"></i> Datos personales</div>
                        <div class="row g-3">
                            <div class="col-md-6"><label>Nombre completo</label><input type="text" name="fullname" class="form-control" required></div>
                            <div class="col-md-6"><label>Usuario</label><input type="text" name="username" class="form-control" required></div>
                            <div class="col-12"><label>Correo</label><input type="email" name="email" class="form-control"></div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-lock-password-line"></i> Acceso</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label>Rol (legacy)</label>
                                <select name="role" class="form-select">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                    <option value="academico_proyectos">academico_proyectos</option>
                                    <option value="marketing">marketing</option>
                                    <option value="contable">contable</option>
                                </select>
                            </div>
                            <div class="col-md-4"><label>Contraseña</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                            <div class="col-md-4"><label>Confirmar contraseña</label><input type="password" name="password_confirmation" class="form-control" required minlength="6"></div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-links-line"></i> Asignación</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label><i class="ri-user-voice-line"></i> Asesor</label>
                                <select name="asesor_id" class="form-select">
                                    <option value="">— Sin asesor —</option>
                                    @foreach($asesores as $a)
                                        <option value="{{ $a->id }}">{{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label><i class="ri-user-star-line"></i> Responsable</label>
                                <select name="responsable_id" class="form-select">
                                    <option value="">— Sin responsable —</option>
                                    @foreach($responsables as $r)
                                        <option value="{{ $r->id }}">{{ $r->apellidos }}, {{ $r->nombres }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title wa"><i class="ri-whatsapp-line"></i> WhatsApp corporativo <span class="hint">Seguimiento ESAM</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Número de WhatsApp</label>
                                <input type="text" name="whatsapp_numero" class="form-control" placeholder="5917XXXXXX" maxlength="20">
                                <small class="text-muted">Solo dígitos (con o sin 591).</small>
                            </div>
                            <div class="col-md-6">
                                <label>Área</label>
                                <select name="whatsapp_area" class="form-select">
                                    <option value="">— Sin área —</option>
                                    @foreach(\App\Models\User::AREAS_WHATSAPP as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="switch-row mb-0">
                                    <input type="checkbox" name="whatsapp_autorizado" value="1" class="form-check-input mt-0">
                                    <span class="txt">Autorizado para usar Seguimiento ESAM</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="sec-block mb-0">
                        <div class="sec-block-title"><i class="ri-shield-user-line"></i> Roles del sistema <span class="hint">Spatie</span></div>
                        <div class="perm-group mb-0">
                            @foreach($roles as $rol)
                                <label class="perm-check">
                                    <input type="checkbox" name="roles[]" value="{{ $rol->id }}">
                                    <i class="ri-shield-check-line text-primary"></i> {{ $rol->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-add"><i class="ri-save-line"></i> Crear usuario</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modales editar --}}
@foreach($users as $u)
<div class="modal fade modal-sec" id="modalUserEdit-{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.seguridad.usuarios.update', $u->id) }}">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title"><i class="ri-edit-line me-1"></i> Editar — {{ $u->fullname ?? $u->username }}</h5>
                        <p class="modal-subtitle">Actualizá datos, acceso, roles y WhatsApp corporativo.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php $esamOn = $u->whatsapp_autorizado && trim((string) $u->whatsapp_numero) !== ''; @endphp
                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-id-card-line"></i> Datos personales</div>
                        <div class="row g-3">
                            <div class="col-md-6"><label>Nombre completo</label><input type="text" name="fullname" class="form-control" required value="{{ $u->fullname }}"></div>
                            <div class="col-md-6"><label>Usuario</label><input type="text" name="username" class="form-control" required value="{{ $u->username }}"></div>
                            <div class="col-12"><label>Correo</label><input type="email" name="email" class="form-control" value="{{ $u->email }}"></div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-lock-password-line"></i> Acceso</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label>Rol (legacy)</label>
                                <select name="role" class="form-select">
                                    @foreach(['user','admin','academico_proyectos','marketing','contable'] as $rl)
                                        <option value="{{ $rl }}" @selected($u->role===$rl)>{{ $rl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4"><label>Nueva contraseña <small class="text-muted">(opcional)</small></label><input type="password" name="password" class="form-control" minlength="6"></div>
                            <div class="col-md-4"><label>Confirmar contraseña</label><input type="password" name="password_confirmation" class="form-control" minlength="6"></div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title"><i class="ri-links-line"></i> Asignación</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label><i class="ri-user-voice-line"></i> Asesor</label>
                                <select name="asesor_id" class="form-select">
                                    <option value="">— Sin asesor —</option>
                                    @foreach($asesores as $a)
                                        <option value="{{ $a->id }}" @selected($u->asesor_id === $a->id)>{{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->ci }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label><i class="ri-user-star-line"></i> Responsable</label>
                                <select name="responsable_id" class="form-select">
                                    <option value="">— Sin responsable —</option>
                                    @foreach($responsables as $r)
                                        <option value="{{ $r->id }}" @selected((int)$u->responsable_id === $r->id)>{{ $r->apellidos }}, {{ $r->nombres }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="sec-block">
                        <div class="sec-block-title wa">
                            <i class="ri-whatsapp-line"></i> WhatsApp corporativo
                            <span class="hint">
                                @if($esamOn)<span class="esam-pill on"><i class="ri-checkbox-circle-fill"></i> Habilitado</span>
                                @elseif(trim((string) $u->whatsapp_numero) !== '')<span class="esam-pill warn"><i class="ri-error-warning-fill"></i> Sin autorizar</span>
                                @else<span class="esam-pill off"><i class="ri-close-circle-line"></i> Sin número</span>
                                @endif
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Número de WhatsApp</label>
                                <input type="text" name="whatsapp_numero" class="form-control" value="{{ $u->whatsapp_numero }}" placeholder="5917XXXXXX" maxlength="20">
                                <small class="text-muted">Solo dígitos (con o sin 591).</small>
                            </div>
                            <div class="col-md-6">
                                <label>Área</label>
                                <select name="whatsapp_area" class="form-select">
                                    <option value="">— Sin área —</option>
                                    @foreach(\App\Models\User::AREAS_WHATSAPP as $val => $label)
                                        <option value="{{ $val }}" @selected($u->whatsapp_area === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="switch-row mb-0">
                                    <input type="checkbox" name="whatsapp_autorizado" value="1" class="form-check-input mt-0" @checked($u->whatsapp_autorizado)>
                                    <span class="txt">Autorizado para usar Seguimiento ESAM</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="sec-block mb-0">
                        <div class="sec-block-title"><i class="ri-shield-user-line"></i> Roles del sistema <span class="hint">Spatie</span></div>
                        <div class="perm-group mb-0">
                            @php $rolesAsig = $u->roles->pluck('id')->toArray(); @endphp
                            @foreach($roles as $rol)
                                <label class="perm-check">
                                    <input type="checkbox" name="roles[]" value="{{ $rol->id }}" @checked(in_array($rol->id, $rolesAsig))>
                                    <i class="ri-shield-check-line text-primary"></i> {{ $rol->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-add"><i class="ri-save-line"></i> Guardar cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modal eliminar --}}
<div class="modal fade modal-sec" id="modalDeleteUser" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" id="formDeleteUser">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="width:60px;height:60px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fef3f2;border-radius:50%;font-size:1.75rem;color:#f06548;">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <h5 class="fw-bold mb-1">¿Eliminar usuario?</h5>
                    <p class="text-muted mb-0" id="deleteUserName">—</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal restablecer contraseña --}}
<div class="modal fade modal-sec" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" id="formResetPassword">
            @csrf
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="width:60px;height:60px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fffbeb;border-radius:50%;font-size:1.75rem;color:#f59e0b;">
                        <i class="ri-lock-unlock-line"></i>
                    </div>
                    <h5 class="fw-bold mb-1">¿Restablecer contraseña?</h5>
                    <p class="text-muted mb-0" id="resetPasswordName">—</p>
                    <p class="text-muted small mt-2 mb-0">La nueva contraseña será <strong>12345678</strong>.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ri-lock-unlock-line"></i> Restablecer</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
(function() {
    // === Modal handlers ===
    document.addEventListener('click', function(e) {
        const delBtn = e.target.closest('[data-user-id]');
        if (delBtn) {
            const id = delBtn.dataset.userId;
            const name = delBtn.dataset.userName;
            document.getElementById('formDeleteUser').action = '{{ route("admin.seguridad.usuarios.destroy", "PLACEHOLDER") }}'.replace('/PLACEHOLDER', '/' + id);
            document.getElementById('deleteUserName').textContent = name;
        }
        const resetBtn = e.target.closest('[data-reset-id]');
        if (resetBtn) {
            const id = resetBtn.dataset.resetId;
            const name = resetBtn.dataset.resetName;
            document.getElementById('formResetPassword').action = '{{ route("admin.seguridad.usuarios.reset-password", "PLACEHOLDER") }}'.replace('/PLACEHOLDER', '/' + id);
            document.getElementById('resetPasswordName').textContent = name;
        }
    });

    // === Real-time validation ===
    function validateField(field) {
        const val = field.value.trim();
        const name = field.name;
        const errorEl = field.parentElement.querySelector('.invalid-feedback-custom') || (function() {
            const el = document.createElement('div');
            el.className = 'invalid-feedback-custom';
            field.parentElement.appendChild(el);
            return el;
        })();
        let valid = true;
        let msg = '';

        if (name === 'fullname') {
            if (!val) { valid = false; msg = 'El nombre completo es obligatorio.'; }
            else if (val.length < 2) { valid = false; msg = 'Mínimo 2 caracteres.'; }
        } else if (name === 'username') {
            if (!val) { valid = false; msg = 'El usuario es obligatorio.'; }
            else if (val.length < 3) { valid = false; msg = 'Mínimo 3 caracteres.'; }
            else if (!/^[a-zA-Z0-9_.-]+$/.test(val)) { valid = false; msg = 'Solo letras, números, puntos, guiones y guión bajo.'; }
        } else if (name === 'email') {
            if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { valid = false; msg = 'Correo electrónico no válido.'; }
        } else if (name === 'password') {
            const isCreate = field.closest('#modalUserCreate') !== null;
            if (isCreate && !val) { valid = false; msg = 'La contraseña es obligatoria.'; }
            else if (val && val.length < 6) { valid = false; msg = 'Mínimo 6 caracteres.'; }
            // Check confirmation
            const form = field.closest('form');
            if (form) {
                const conf = form.querySelector('input[name="password_confirmation"]');
                if (conf && conf.value) {
                    if (val !== conf.value) {
                        validateField(conf);
                    }
                }
            }
        } else if (name === 'password_confirmation') {
            const form = field.closest('form');
            if (form) {
                const pwd = form.querySelector('input[name="password"]');
                if (pwd && pwd.value && val !== pwd.value) { valid = false; msg = 'Las contraseñas no coinciden.'; }
            }
        }

        field.classList.toggle('is-invalid', !valid && val !== '');
        field.classList.toggle('is-valid', valid && val !== '');
        errorEl.textContent = msg;
        return valid;
    }

    document.querySelectorAll('.modal-sec form').forEach(function(form) {
        form.querySelectorAll('input[name="fullname"], input[name="username"], input[name="email"], input[name="password"], input[name="password_confirmation"]').forEach(function(field) {
            field.addEventListener('input', function() { validateField(this); });
            field.addEventListener('blur', function() { validateField(this); });
        });
    });

    // === Filtro por búsqueda + tabs de rol ===
    let activeRole = 'all';

    function applyUserFilters() {
        const q = (document.getElementById('userSearch')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.user-tr').forEach(function(el) {
            const roles = (el.dataset.roles || '').toLowerCase();
            const name = el.dataset.name || '';
            let okRole = true;
            if (activeRole === '__sin__') {
                okRole = roles === '';
            } else if (activeRole !== 'all') {
                okRole = roles.split(',').indexOf(activeRole.toLowerCase()) !== -1;
            }
            const okSearch = !q || name.includes(q);
            el.style.display = (okRole && okSearch) ? '' : 'none';
        });
    }

    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', applyUserFilters);
    }

    document.querySelectorAll('.user-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.user-tab').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            activeRole = this.dataset.role || 'all';
            applyUserFilters();
        });
    });
})();
</script>
@endpush
@endsection
