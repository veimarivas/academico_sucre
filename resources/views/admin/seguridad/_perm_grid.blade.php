@php
    $asignados = $asignados ?? [];
@endphp
<div class="perm-grid-search-box">
    <i class="ri-search-line perm-grid-search-icon"></i>
    <input type="text" class="form-control perm-grid-filter" placeholder="Buscar permiso...">
</div>
<div class="d-flex justify-content-end mb-2 gap-3" style="font-size:.7rem;">
    <a href="javascript:;" class="toggle-all" onclick="document.querySelectorAll('input[name=\'permissions[]\']:not(:disabled)').forEach(c=>c.checked=true);">Seleccionar todos</a>
    <a href="javascript:;" class="toggle-all" style="color:#f06548;" onclick="document.querySelectorAll('input[name=\'permissions[]\']:not(:disabled)').forEach(c=>c.checked=false);">Quitar todos</a>
</div>
<div class="perm-grid-items">
@foreach($permisos as $grupo => $items)
    <div class="perm-group" data-group="{{ $grupo }}">
        <div class="perm-group-title">
            <span><i class="ri-folder-shield-line me-1"></i> {{ ucfirst($grupo) }}</span>
            <span class="perm-group-count text-muted" style="font-size:.65rem;">{{ $items->count() }}</span>
        </div>
        <div>
            @foreach($items as $p)
                <label class="perm-check" data-name="{{ strtolower($p->name) }}">
                    <input type="checkbox" name="permissions[]" value="{{ $p->id }}" @checked(in_array($p->id, $asignados))>
                    {{ $p->name }}
                </label>
            @endforeach
        </div>
    </div>
@endforeach
</div>

@push('js')
<script>
(function() {
    document.querySelectorAll('.perm-grid-filter').forEach(function(input) {
        input.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const body = this.closest('.modal-body') || this.closest('form');
            const groups = body.querySelectorAll('.perm-group');
            let totalVisible = 0;

            groups.forEach(function(group) {
                const checks = group.querySelectorAll('.perm-check');
                let groupVisible = 0;

                checks.forEach(function(c) {
                    const name = c.dataset.name || c.textContent.toLowerCase().trim();
                    const match = !q || name.includes(q);
                    c.style.display = match ? '' : 'none';
                    if (match) groupVisible++;
                });

                const countEl = group.querySelector('.perm-group-count');
                if (countEl) countEl.textContent = groupVisible;

                group.style.display = groupVisible === 0 ? 'none' : '';
                if (groupVisible > 0) totalVisible += groupVisible;
            });
        });
    });
})();
</script>
@endpush
