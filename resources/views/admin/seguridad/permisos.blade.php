@extends('admin.layouts.master')

@section('title', 'Permisos - Seguridad - ' . config('app.name'))

@include('admin.seguridad._styles')

@section('content')
@php
    $totalPermisos = $permisos->count();
    $totalGrupos = $grupos->count();
    $sinRol = $permisos->where('roles_count', 0)->count();
    $conRol = $totalPermisos - $sinRol;
@endphp
<div class="sec-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-key-2-line"></i></div>
        <div>
            <h4>Permisos</h4>
            <p class="hero-sub">Gestiona los permisos del sistema. Convención: <code class="text-white-50">recurso.accion</code></p>
            <div class="sec-hero-badges">
                <span class="sec-hero-badge"><i class="ri-key-2-line"></i> {{ $totalPermisos }} permisos</span>
                <span class="sec-hero-badge"><i class="ri-folder-shield-line"></i> {{ $totalGrupos }} recursos</span>
            </div>
        </div>
    </div>
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
        <li class="breadcrumb-item">Seguridad</li>
        <li class="breadcrumb-item active">Permisos</li>
    </ol>
</div>

<div class="sec-kpis">
    <div class="sec-kpi" style="--k:#405189;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Permisos</div><div class="val">{{ $totalPermisos }}</div><div class="sub">registrados</div></div>
            <span class="ic" style="background:#eef1f8;color:#405189;"><i class="ri-key-2-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#0a6a90;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Recursos</div><div class="val">{{ $totalGrupos }}</div><div class="sub">grupos</div></div>
            <span class="ic" style="background:#e8f4fd;color:#1f6fbf;"><i class="ri-folder-shield-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#0ab39c;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Con roles</div><div class="val">{{ $conRol }}</div><div class="sub">asignados a algún rol</div></div>
            <span class="ic" style="background:#e6f7ee;color:#0a7d5a;"><i class="ri-shield-check-line"></i></span>
        </div>
    </div>
    <div class="sec-kpi" style="--k:#cba135;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div><div class="lbl">Sin rol</div><div class="val">{{ $sinRol }}</div><div class="sub">sin usar</div></div>
            <span class="ic" style="background:#fff7e6;color:#b7791f;"><i class="ri-error-warning-line"></i></span>
        </div>
    </div>
</div>

<div class="sec-card">
    <div class="card-header">
        <h5><i class="ri-key-line"></i> Listado de permisos <span id="permTotalCount" class="count-badge">{{ $permisos->count() }}</span></h5>
        @can('permisos.crear')
        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalPermCreate"><i class="ri-add-line"></i> Nuevo permiso</button>
        @endcan
    </div>
    <div class="card-body">
        <div class="sec-search">
            <i class="ri-search-line search-icon"></i>
            <input type="text" id="permSearch" class="form-control" placeholder="Buscar permiso por nombre...">
        </div>

        @if($grupos->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="ri-key-2-line"></i></div>
                <h5 class="text-dark fw-bold mb-1">Sin permisos</h5>
            </div>
        @else
            <div id="permGroupsContainer">
                @foreach($grupos as $g => $items)
                <div class="perm-group-wrapper mb-4" data-group="{{ $g }}">
                    <div class="perm-section-title">
                        <i class="ri-folder-shield-line"></i> {{ ucfirst($g) }}
                        <span class="count-badge">{{ $items->count() }}</span>
                    </div>
                    <div class="perm-list perm-cards-container">
                        @foreach($items as $p)
                        <div class="perm-card" data-name="{{ strtolower($p->name) }}">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="perm-card-name">
                                    <i class="ri-key-line"></i>
                                    <span>{{ $p->name }}</span>
                                </div>
                                @if($p->roles->isNotEmpty())
                                <div class="perm-card-roles">
                                    @foreach($p->roles as $rol)
                                    <span class="perm-card-role {{ strtolower(str_replace(' ','-',$rol->name)) }}"><i class="ri-shield-check-line"></i> {{ $rol->name }}</span>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-muted" style="font-size:.68rem;">Sin roles</span>
                                @endif
                            </div>
                            <div class="perm-card-actions">
                                @can('permisos.editar')
                                <button class="btn-icon btn-edit" data-bs-toggle="modal" data-bs-target="#modalPermEdit-{{ $p->id }}" title="Editar"><i class="ri-edit-line"></i></button>
                                @endcan
                                @can('permisos.eliminar')
                                <button class="btn-icon btn-del" title="Eliminar" data-perm-id="{{ $p->id }}" data-perm-name="{{ $p->name }}" data-bs-toggle="modal" data-bs-target="#modalDeletePerm"><i class="ri-delete-bin-line"></i></button>
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div id="permEmptyFilter" class="sec-empty-filter" style="display:none;">
                <i class="ri-search-eye-line"></i>
                No se encontraron permisos con ese nombre.
            </div>
        @endif
    </div>
</div>

{{-- Modal crear --}}
<div class="modal fade modal-sec" id="modalPermCreate" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.seguridad.permisos.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-add-circle-line me-1"></i> Nuevo permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Nombre del permiso</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ej. reportes.exportar" data-validate="required|pattern:/^[a-z0-9.]+$/" data-msg="Formato: recurso.accion (solo minúsculas y puntos)">
                    <div class="invalid-feedback-custom"></div>
                    <small class="text-muted d-block mt-1" style="font-size:.7rem;"><i class="ri-information-line"></i> Convención: <strong>recurso.accion</strong> (todo minúsculas).</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-add"><i class="ri-save-line"></i> Crear</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modales editar --}}
@foreach($permisos as $p)
<div class="modal fade modal-sec" id="modalPermEdit-{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.seguridad.permisos.update', $p->id) }}">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-edit-line me-1"></i> Editar permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Nombre del permiso</label>
                    <input type="text" name="name" class="form-control" required value="{{ $p->name }}" data-validate="required|pattern:/^[a-z0-9.]+$/" data-msg="Formato: recurso.accion (solo minúsculas y puntos)">
                    <div class="invalid-feedback-custom"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-add"><i class="ri-save-line"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modal eliminar --}}
<div class="modal fade modal-sec" id="modalDeletePerm" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" id="formDeletePerm">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="width:60px;height:60px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fef3f2;border-radius:50%;font-size:1.75rem;color:#f06548;">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <h5 class="fw-bold mb-1">¿Eliminar permiso?</h5>
                    <p class="text-muted mb-0" id="deletePermName">—</p>
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
        const btn = e.target.closest('[data-perm-id]');
        if (!btn) return;
        document.getElementById('formDeletePerm').action = '{{ route("admin.seguridad.permisos.destroy", "PLACEHOLDER") }}'.replace('/PLACEHOLDER', '/' + btn.dataset.permId);
        document.getElementById('deletePermName').textContent = btn.dataset.permName;
    });

    // === Real-time validation ===
    function validatePermField(field) {
        const val = field.value.trim();
        const name = field.name;
        const errorEl = field.parentElement.querySelector('.invalid-feedback-custom');
        if (!errorEl) return true;
        let valid = true;
        let msg = '';

        if (name === 'name') {
            if (!val) { valid = false; msg = 'El nombre del permiso es obligatorio.'; }
            else if (val.length < 3) { valid = false; msg = 'Mínimo 3 caracteres.'; }
            else if (!/^[a-z0-9.]+$/.test(val)) { valid = false; msg = 'Solo minúsculas, números y puntos.'; }
        }

        field.classList.toggle('is-invalid', !valid && val !== '');
        field.classList.toggle('is-valid', valid && val !== '');
        errorEl.textContent = msg;
        return valid;
    }

    document.querySelectorAll('.modal-sec form').forEach(function(form) {
        form.querySelectorAll('input[name="name"]').forEach(function(field) {
            field.addEventListener('input', function() { validatePermField(this); });
            field.addEventListener('blur', function() { validatePermField(this); });
        });
    });

    // === Search/filter ===
    const searchInput = document.getElementById('permSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const groups = document.querySelectorAll('.perm-group-wrapper');
            let totalVisible = 0;

            groups.forEach(function(group) {
                const cards = group.querySelectorAll('.perm-card');
                let groupVisible = 0;

                cards.forEach(function(card) {
                    const name = card.dataset.name;
                    const match = !q || name.includes(q);
                    card.style.display = match ? '' : 'none';
                    if (match) groupVisible++;
                });

                const groupCount = group.querySelector('.count-badge');
                if (groupCount) groupCount.textContent = groupVisible;

                if (groupVisible === 0) {
                    group.style.display = 'none';
                } else {
                    group.style.display = '';
                    totalVisible += groupVisible;
                }
            });

            document.getElementById('permTotalCount').textContent = totalVisible;
            document.getElementById('permEmptyFilter').style.display = totalVisible === 0 ? 'block' : 'none';
        });
    }
})();
</script>
@endpush
@endsection