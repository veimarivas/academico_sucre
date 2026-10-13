@extends('admin.layouts.master')

@section('title', 'Roles - Seguridad - ' . config('app.name'))

@include('admin.seguridad._styles')

@section('content')
@php
    $totalPermisos = \Spatie\Permission\Models\Permission::count();
    $rolesConPermisos = $roles->where('permissions_count', '>', 0)->count();
    $totalAsignaciones = $roles->sum('users_count');
@endphp
<div class="sec-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-shield-keyhole-line"></i></div>
        <div>
            <h4>Roles</h4>
            <p class="hero-sub">Define los roles y los permisos asignados a cada uno.</p>
            <div class="sec-hero-badges">
                <span class="sec-hero-badge"><i class="ri-shield-user-line"></i> {{ $roles->count() }} roles</span>
                <span class="sec-hero-badge"><i class="ri-key-2-line"></i> {{ $totalPermisos }} permisos</span>
                <span class="sec-hero-badge"><i class="ri-group-line"></i> {{ $totalAsignaciones }} asignaciones</span>
            </div>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item">Seguridad</li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>
</div>

<div class="sec-kpis">
    <div class="sec-kpi" style="--k:#405189;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Roles</div><div class="val">{{ $roles->count() }}</div><div class="sub">registrados</div></div>
            <span class="ic" style="background:#eef1f8;color:#405189;"><i class="ri-shield-user-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#0ab39c;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Con permisos</div><div class="val">{{ $rolesConPermisos }}</div><div class="sub">de {{ $roles->count() }}</div></div>
            <span class="ic" style="background:#e6f7ee;color:#0a7d5a;"><i class="ri-key-2-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#cba135;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Permisos del sistema</div><div class="val">{{ $totalPermisos }}</div><div class="sub">disponibles</div></div>
            <span class="ic" style="background:#fff7e6;color:#b7791f;"><i class="ri-shield-keyhole-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#299cdb;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Asignaciones</div><div class="val">{{ $totalAsignaciones }}</div><div class="sub">usuario(s) con rol</div></div>
            <span class="ic" style="background:#e8f4fd;color:#1f6fbf;"><i class="ri-group-line"></i></span>
        </div>
    </div>
</div>

<div class="sec-card">
    <div class="card-header">
        <h5><i class="ri-shield-user-line"></i> Listado de roles <span id="roleTotalCount" class="count-badge">{{ $roles->count() }}</span></h5>
        @can('roles.crear')
        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalRoleCreate"><i class="ri-add-line"></i> Nuevo rol</button>
        @endcan
    </div>
    <div class="card-body">
        <div class="sec-search">
            <i class="ri-search-line search-icon"></i>
            <input type="text" id="roleSearch" class="form-control" placeholder="Buscar rol por nombre...">
        </div>

        @if($roles->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-shield-keyhole-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Sin roles registrados</h5>
                <p class="text-muted mb-0">Crea el primer rol del sistema.</p>
            </div>
        @else
        <div class="role-grid" id="roleCardsContainer">
            @foreach($roles as $r)
                @php
                    $esSuper = strtolower($r->name) === 'super admin';
                    $pct = $totalPermisos > 0 ? (int) round($r->permissions_count / $totalPermisos * 100) : 0;
                @endphp
                <div class="role-card" data-name="{{ strtolower($r->name) }}">
                    <div class="role-card-top">
                        <div class="role-id">
                            <span class="role-avatar {{ $esSuper ? 'super' : '' }}"><i class="ri-shield-{{ $esSuper ? 'star' : 'check' }}-line"></i></span>
                            <div style="min-width:0;">
                                <div class="role-name text-truncate" title="{{ $r->name }}">{{ $r->name }}</div>
                                <div class="role-meta"><i class="ri-group-line"></i> {{ $r->users_count }} usuario(s) · <i class="ri-key-2-line"></i> {{ $r->permissions_count }} permiso(s)</div>
                            </div>
                        </div>
                        <div class="role-actions">
                            @can('roles.editar')
                            <button class="btn-icon btn-edit" data-bs-toggle="modal" data-bs-target="#modalRoleEdit-{{ $r->id }}" title="Editar"><i class="ri-edit-line"></i></button>
                            @endcan
                            @can('roles.eliminar')
                            @if(!$esSuper)
                            <button class="btn-icon btn-del" title="Eliminar" data-role-id="{{ $r->id }}" data-role-name="{{ $r->name }}" data-bs-toggle="modal" data-bs-target="#modalDeleteRole"><i class="ri-delete-bin-line"></i></button>
                            @endif
                            @endcan
                        </div>
                    </div>
                    <div class="role-coverage">
                        <div class="bar"><span style="width: {{ $pct }}%;"></span></div>
                        <span class="pct">{{ $pct }}%</span>
                    </div>
                    <div class="role-perms">
                        @if($r->permissions_count > 0)
                            @foreach($r->permissions->take(6) as $p)
                            <span class="perm-chip">{{ $p->name }}</span>
                            @endforeach
                            @if($r->permissions_count > 6)
                            <span class="perm-chip" style="background:#f1f3f5;color:#98a2b3;border-color:#e6eaf0;">+{{ $r->permissions_count - 6 }} más</span>
                            @endif
                        @else
                            <span class="text-muted" style="font-size:.74rem;font-weight:600;">Sin permisos asignados</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div id="roleEmptyFilter" class="sec-empty-filter" style="display:none;">
            <i class="ri-search-eye-line"></i>
            No se encontraron roles con ese nombre.
        </div>
        @endif
    </div>
</div>

{{-- Modal crear --}}
<div class="modal fade modal-sec" id="modalRoleCreate" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.seguridad.roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-add-circle-line me-1"></i> Nuevo rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre del rol</label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej. Coordinador Académico" data-validate="required|min:3" data-msg="El nombre del rol es obligatorio (mín. 3 caracteres).">
                        <div class="invalid-feedback-custom"></div>
                    </div>
                    <label class="mb-2">Permisos</label>
                    @include('admin.seguridad._perm_grid', ['permisos' => $permisos, 'asignados' => []])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-add"><i class="ri-save-line"></i> Crear rol</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modales editar --}}
@foreach($roles as $r)
<div class="modal fade modal-sec" id="modalRoleEdit-{{ $r->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" action="{{ route('admin.seguridad.roles.update', $r->id) }}">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-edit-line me-1"></i> Editar rol — {{ $r->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre del rol</label>
                        <input type="text" name="name" class="form-control" required value="{{ $r->name }}" {{ strtolower($r->name)==='super admin' ? 'readonly' : '' }} data-validate="required|min:3" data-msg="El nombre del rol es obligatorio (mín. 3 caracteres).">
                        <div class="invalid-feedback-custom"></div>
                    </div>
                    <label class="mb-2">Permisos</label>
                    @include('admin.seguridad._perm_grid', ['permisos' => $permisos, 'asignados' => $r->permissions->pluck('id')->toArray()])
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
<div class="modal fade modal-sec" id="modalDeleteRole" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" id="formDeleteRole">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="width:60px;height:60px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fef3f2;border-radius:50%;font-size:1.75rem;color:#f06548;">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <h5 class="fw-bold mb-1">¿Eliminar rol?</h5>
                    <p class="text-muted mb-0" id="deleteRoleName">—</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
(function() {
    // === Delete modal handler ===
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-role-id]');
        if (!btn) return;
        document.getElementById('formDeleteRole').action = '{{ route("admin.seguridad.roles.destroy", "PLACEHOLDER") }}'.replace('/PLACEHOLDER', '/' + btn.dataset.roleId);
        document.getElementById('deleteRoleName').textContent = btn.dataset.roleName;
    });

    // === Real-time validation ===
    function validateRoleField(field) {
        const val = field.value.trim();
        const errorEl = field.parentElement.querySelector('.invalid-feedback-custom');
        if (!errorEl) return true;
        let valid = true;
        let msg = '';

        if (!val) { valid = false; msg = 'El nombre del rol es obligatorio.'; }
        else if (val.length < 3) { valid = false; msg = 'Mínimo 3 caracteres.'; }

        field.classList.toggle('is-invalid', !valid && val !== '');
        field.classList.toggle('is-valid', valid && val !== '');
        errorEl.textContent = msg;
        return valid;
    }

    document.querySelectorAll('.modal-sec form').forEach(function(form) {
        form.querySelectorAll('input[name="name"]').forEach(function(field) {
            field.addEventListener('input', function() { validateRoleField(this); });
            field.addEventListener('blur', function() { validateRoleField(this); });
        });
    });

    // === Search/filter ===
    const searchInput = document.getElementById('roleSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.role-card');
            let totalVisible = 0;

            cards.forEach(function(card) {
                const name = card.dataset.name;
                const match = !q || name.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) totalVisible++;
            });

            document.getElementById('roleTotalCount').textContent = totalVisible;
            document.getElementById('roleEmptyFilter').style.display = totalVisible === 0 ? 'block' : 'none';
        });
    }
})();
</script>
@endpush
@endsection
