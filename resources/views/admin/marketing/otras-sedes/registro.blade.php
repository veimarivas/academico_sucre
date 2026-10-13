@extends('admin.layouts.master')

@section('title', 'Inscribir - ' . $programa->nombre . ' - ' . config('app.name'))

@push('css')
<style>
.pose-hero {
    position: relative;
    background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 50%, #299cdb 100%);
    border-radius: 16px;
    padding: 1.5rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 12px 32px -14px rgba(109,40,217,.55);
    margin-bottom: 1.25rem;
}
.pose-hero::after {
    content: ""; position: absolute;
    bottom: -70px; right: -40px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
}
.pose-hero > * { position: relative; z-index: 1; }
.tabla-pg thead th {
    background:#f8f9fc !important; font-size:.68rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.4px; color:#495057;
    border-bottom:2px solid #eef0f4;
}
.tabla-pg tbody td { vertical-align: middle; border-bottom:1px solid #f1f3f5; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="pose-hero">
    <div class="d-flex flex-wrap align-items-center gap-3">
        <a href="{{ route('admin.marketing.programas-otras-sedes.index') }}" class="btn btn-volver btn-sm" style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;"><i class="ri-arrow-left-line me-1"></i> Volver</a>
        <div style="min-width:0;">
            <h4 class="fs-20 mb-1 fw-bold" style="color:#fff;">{{ $programa->nombre }}</h4>
            <div class="small" style="color:rgba(255,255,255,.85);">
                @if($programa->codigo)<span><i class="ri-hashtag"></i> {{ $programa->codigo }}</span> @endif
                @if($programa->sede_origen)<span><i class="ri-building-fill"></i> {{ $programa->sede_origen }}</span> @endif
                @if($programa->tipo)<span><i class="ri-price-tag-3-line"></i> {{ $programa->tipo }}</span> @endif
                <span><i class="ri-calendar-line"></i> Gestión {{ $programa->gestion }}</span>
            </div>
        </div>
        <div class="ms-auto d-flex gap-2">
            <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;"><i class="ri-user-add-line me-1"></i>{{ $inscripciones->count() }} inscritos</span>
            <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;">Mat: Bs {{ number_format($programa->matricula, 2, ',', '.') }}</span>
            <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;">Coleg: Bs {{ number_format($programa->colegiatura, 2, ',', '.') }}</span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="ri-check-double-line me-1"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="ri-error-warning-line me-1"></i>Revise los campos:</strong>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if((int)$programa->inscripciones_habilitadas === 1)
<div class="card mb-4" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(0,0,0,.12);">
    <div class="card-header bg-transparent" style="border-bottom:1px dashed #eef0f4;">
        <h6 class="mb-0 fw-semibold"><i class="ri-add-circle-line me-1" style="color:#8b5cf6;"></i> Nueva inscripción</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.marketing.programas-otras-sedes.inscribir', $programa->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" id="ci-buscar" class="form-control" placeholder="Buscar participante por CI" autocomplete="off">
                        <button type="button" class="btn btn-outline-primary" onclick="buscarParticipante()"><i class="ri-search-line me-1"></i> Buscar</button>
                        <span class="input-group-text bg-light text-muted small" id="ci-estado"></span>
                    </div>
                    <div class="form-text">Si el CI no existe, se registrará un participante nuevo con los datos del formulario.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">CI <span class="text-danger">*</span></label>
                    <input type="text" name="ci" id="f-ci" class="form-control" required maxlength="10">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" id="f-nombres" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellidos" id="f-apellidos" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Sexo</label>
                    <select name="sexo" id="f-sexo" class="form-select">
                        <option value="Hombre">Hombre</option>
                        <option value="Mujer">Mujer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Celular</label>
                    <input type="text" name="celular" id="f-celular" class="form-control" maxlength="11">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Correo</label>
                    <input type="email" name="correo" id="f-correo" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Departamento</label>
                    <input type="text" name="departamento" id="f-departamento" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Asesor</label>
                    <select name="asesor_id" class="form-select">
                        <option value="">— Sin asesor —</option>
                        @foreach($asesores as $as)
                            <option value="{{ $as->id }}">{{ $as->apellidos }}, {{ $as->nombres }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Fecha inscripción</label>
                    <input type="date" name="fecha_inscripcion" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Monto total (Bs)</label>
                    <input type="number" name="monto_total" class="form-control" step="0.01" min="0"
                           value="{{ $programa->matricula + $programa->colegiatura }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="contado" id="f-contado" value="1">
                        <label class="form-check-label" for="f-contado">Contado</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Recibo de pago <span class="text-danger">*</span></label>
                    <input type="file" name="recibo" class="form-control" required accept=".jpg,.jpeg,.png,.webp,.pdf">
                    <div class="form-text">Obligatorio para considerar la inscripción.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Factura <span class="text-danger">*</span></label>
                    <input type="file" name="factura" class="form-control" required accept=".jpg,.jpeg,.png,.webp,.pdf">
                    <div class="form-text">Obligatorio para considerar la inscripción.</div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Observación</label>
                    <textarea name="observacion" class="form-control" rows="1"></textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary"><i class="ri-check-double-line me-1"></i> Registrar inscripción</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<div class="alert alert-warning mb-4"><i class="ri-close-circle-line me-1"></i> Las inscripciones de este programa están cerradas.</div>
@endif

<div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(0,0,0,.12);">
    <div class="card-header bg-transparent" style="border-bottom:1px dashed #eef0f4;">
        <h6 class="mb-0 fw-semibold"><i class="ri-group-line me-1" style="color:#8b5cf6;"></i> Participantes inscritos <span class="badge bg-soft-primary text-primary ms-1">{{ $inscripciones->count() }}</span></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-pg align-middle mb-0">
                <thead>
                    <tr>
                        <th>Participante</th>
                        <th>CI</th>
                        <th>Asesor</th>
                        <th>Fecha</th>
                        <th class="text-center">Pago</th>
                        <th class="text-end">Monto</th>
                        <th>Recibo</th>
                        <th>Factura</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscripciones as $insc)
                    <tr>
                        <td class="fw-semibold">{{ $insc->p_apellidos }}, {{ $insc->p_nombres }}</td>
                        <td>{{ $insc->ci }}</td>
                        <td>{{ $insc->a_nombres ? $insc->a_apellidos . ', ' . $insc->a_nombres : '—' }}</td>
                        <td>{{ $insc->fecha_inscripcion ? \Carbon\Carbon::parse($insc->fecha_inscripcion)->format('d/m/Y') : '—' }}</td>
                        <td class="text-center">
                            @if((int)$insc->contado === 1)
                                <span class="badge bg-soft-success text-success">Contado</span>
                            @else
                                <span class="badge bg-soft-primary text-primary">Regular</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">Bs {{ number_format($insc->monto_total, 2, ',', '.') }}</td>
                        <td>
                            @if ($insc->recibo_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($insc->recibo_path) }}" target="_blank" class="badge bg-soft-success text-success text-decoration-none"><i class="bi bi-file-earmark-check me-1"></i>{{ $insc->recibo_original }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($insc->factura_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($insc->factura_path) }}" target="_blank" class="badge bg-soft-primary text-primary text-decoration-none"><i class="bi bi-receipt me-1"></i>{{ $insc->factura_original }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.marketing.programas-otras-sedes.inscripcion.destroy', $insc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta inscripción? Se borrarán también el recibo y la factura.');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-soft-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4"><i class="ri-group-line me-1"></i> Aún no hay participantes inscritos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cb = document.getElementById('ci-buscar');
    const ciReal = document.getElementById('f-ci');
    if (cb && ciReal) {
        cb.addEventListener('input', function () { ciReal.value = this.value.trim(); });
    }
});

function buscarParticipante() {
    const ci = document.getElementById('ci-buscar').value.trim();
    const estado = document.getElementById('ci-estado');
    if (!ci) { estado.textContent = 'Ingrese CI'; return; }

    fetch('{{ route("admin.marketing.programas-otras-sedes.buscar-participante") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ ci: ci })
    })
    .then(r => r.json())
    .then(data => {
        if (data.existe) {
            estado.innerHTML = '<i class="ri-check-line me-1"></i>Participante encontrado';
            document.getElementById('f-ci').value = data.participante.ci;
            document.getElementById('f-nombres').value = data.participante.nombres;
            document.getElementById('f-apellidos').value = data.participante.apellidos;
            document.getElementById('f-sexo').value = data.participante.sexo;
            document.getElementById('f-celular').value = data.participante.celular || '';
            document.getElementById('f-correo').value = data.participante.correo || '';
            document.getElementById('f-departamento').value = data.participante.departamento || '';
        } else {
            estado.innerHTML = '<i class="ri-information-line me-1"></i>' + (data.message || 'No existe');
            document.getElementById('f-nombres').value = '';
            document.getElementById('f-apellidos').value = '';
            document.getElementById('f-celular').value = '';
            document.getElementById('f-correo').value = '';
        }
    })
    .catch(() => { estado.innerHTML = '<i class="ri-error-warning-line me-1"></i>Error'; });
}
</script>
@endpush