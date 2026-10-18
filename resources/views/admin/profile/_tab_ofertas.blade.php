{{-- Pestaña "Ofertas en inscripción" (cargada por AJAX). --}}
<div class="tab-toolbar">
    <div>
        <p class="tb-title"><i class="ri-megaphone-line"></i> Ofertas académicas en inscripción</p>
        <p class="tb-sub">Programas abiertos con estado de inscripciones activo.</p>
    </div>
    <span class="badge bg-success-subtle text-success-emphasis">{{ $ofertas->count() }} programa(s)</span>
</div>
@if($ofertas->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="ri-megaphone-line"></i></div>
        <h5 class="text-dark fw-bold mb-1">Sin ofertas activas</h5>
        <p class="text-muted mb-0">No hay programas en estado "Inscripciones" actualmente.</p>
    </div>
@else
    @include('admin.programas.partials.table', ['programas' => $ofertas, 'soloVer' => true])
@endif
