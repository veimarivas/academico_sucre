@if($soloAuto->isNotEmpty())
<div class="filter-card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="ri-hand-coin-line"></i>
        <span class="fw-semibold fs-13">Gastos de Autogestión por Programa</span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table tabla-pub" style="width:100%;">
                <thead>
                    <tr>
                        <th>Programa</th>
                        <th class="text-center" style="width:100px;"># Gastos</th>
                        <th class="text-end" style="width:160px;">Total invertido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($soloAuto as $p)
                    <tr>
                        <td class="fs-12 fw-medium">{{ $p->programa_label }}</td>
                        <td class="text-center">{{ $p->auto_gastos }}</td>
                        <td class="text-end fw-bold">Bs {{ number_format($p->auto_monto, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
