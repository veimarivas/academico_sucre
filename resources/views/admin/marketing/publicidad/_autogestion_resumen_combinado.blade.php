@if($programasResumen->isNotEmpty())
<div class="filter-card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="ri-bar-chart-grouped-line"></i>
        <span class="fw-semibold fs-13">Total Invertido por Programa (Asignado + Autogestión)</span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table tabla-pub" style="width:100%;">
                <thead>
                    <tr>
                        <th>Programa</th>
                        <th class="text-end" style="width:150px;">Asignado (empresa)</th>
                        <th class="text-end" style="width:150px;">Autogestión</th>
                        <th class="text-end" style="width:150px;">Total invertido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programasResumen as $p)
                    <tr>
                        <td class="fs-12 fw-medium">{{ $p->programa_label }}</td>
                        <td class="text-end">Bs {{ number_format($p->comp_monto, 2) }}</td>
                        <td class="text-end">Bs {{ number_format($p->auto_monto, 2) }}</td>
                        <td class="text-end fw-bold">Bs {{ number_format($p->total_invertido, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
