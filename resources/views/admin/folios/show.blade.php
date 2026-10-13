@extends('admin.layouts.master')

@section('title', $folio->nombre . ' - Folios - ' . config('app.name'))

@section('content')
<div class="folios-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="hero-icon"><i class="ri-government-line"></i></div>
        <div>
            <h4 class="mb-1">
                {{ $folio->nombre }}
                @if($folio->status === 'abierto')
                    <span class="badge bg-soft-success text-success ms-1"><i class="ri-lock-unlock-line me-1"></i>Abierto</span>
                @else
                    <span class="badge bg-soft-danger text-danger ms-1"><i class="ri-lock-line me-1"></i>Cerrado</span>
                @endif
            </h4>
            <p class="hero-sub mb-0">Registrado el {{ \Carbon\Carbon::parse($folio->fecha_registro)->format('d/m/Y') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.folios.edit', $folio->id) }}" class="btn btn-sm btn-soft-primary">
            <i class="ri-pencil-line me-1"></i>Editar
        </a>
        <a href="{{ route('admin.folios.index') }}" class="btn btn-sm btn-light">
            <i class="ri-arrow-left-line me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Participantes</small>
                            <span class="fw-bold fs-3">{{ count($participantes) }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Inscripción</small>
                            <span class="fw-bold fs-5">Bs {{ number_format($folio->total_inscripcion, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Matrícula</small>
                            <span class="fw-bold fs-5">Bs {{ number_format($folio->total_matricula, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Colegiatura</small>
                            <span class="fw-bold fs-5">Bs {{ number_format($folio->total_colegiatura, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Certificación</small>
                            <span class="fw-bold fs-5">Bs {{ number_format($folio->total_certificacion, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="border rounded-3 p-3 h-100" style="border-color:var(--ui-accent) !important; background:var(--ui-accent-soft);">
                            <small class="text-muted d-block text-uppercase fs-11 fw-semibold">Total General</small>
                            <span class="fw-bold fs-5 text-primary">Bs {{ number_format($folio->total_general, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Participants grouped by program --}}
                <h6 class="fw-semibold fs-14 mb-2">
                    <i class="ri-team-line me-1"></i>Participantes
                </h6>

                @php
                    $participantesPorPrograma = $participantes->groupBy('programa_nombre');
                @endphp

                @if($participantesPorPrograma->count() > 0)
                    @foreach($participantesPorPrograma as $progNombre => $grupo)
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-soft-primary text-primary fs-11 px-3 py-1">
                                <i class="ri-booklet-line me-1"></i>{{ $progNombre }}
                            </span>
                            <small class="text-muted">{{ count($grupo) }} participante(s)</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light text-muted text-uppercase fs-11">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Participante</th>
                                        <th style="width:80px;" class="text-center">CI</th>
                                        <th style="width:100px;" class="text-center">Inscripción</th>
                                        <th style="width:90px;" class="text-center">Matrícula</th>
                                        <th style="width:100px;" class="text-center">Colegiatura</th>
                                        <th style="width:100px;" class="text-center">Certificación</th>
                                        <th style="width:100px;" class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupo as $p)
                                    @php
                                        $pTotal = $p->monto_inscripcion + $p->monto_matricula + $p->monto_colegiatura + $p->monto_certificacion;
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                        <td><span class="fw-semibold fs-13">{{ $p->apellidos }}, {{ $p->nombres }}</span></td>
                                        <td class="text-center">{{ $p->ci }}</td>
                                        <td class="text-center">Bs {{ number_format($p->monto_inscripcion, 2) }}</td>
                                        <td class="text-center">Bs {{ number_format($p->monto_matricula, 2) }}</td>
                                        <td class="text-center">Bs {{ number_format($p->monto_colegiatura, 2) }}</td>
                                        <td class="text-center">Bs {{ number_format($p->monto_certificacion, 2) }}</td>
                                        <td class="text-center fw-bold text-primary">Bs {{ number_format($pTotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ri-inbox-2-line ri-2x d-block mb-2"></i>
                        <p class="mb-0">No hay participantes registrados en este folio</p>
                        <small>
                            <a href="{{ route('admin.folios.edit', $folio->id) }}" class="text-decoration-none">
                                Agregar participantes desde edición
                            </a>
                        </small>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
