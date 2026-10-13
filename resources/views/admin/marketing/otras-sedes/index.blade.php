@extends('admin.layouts.master')

@section('title', 'Programas de otras sedes - ' . config('app.name'))

@push('css')
<style>
.pose-hero {
    position: relative;
    background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 50%, #299cdb 100%);
    border-radius: 16px;
    padding: 1.6rem;
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
.pg-stat {
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 12px;
    padding: .8rem 1rem;
    text-align: center;
}
.pg-stat .v { color: #fff; font-weight: 700; font-size: 1.3rem; line-height: 1.1; }
.pg-stat .l { color: rgba(255,255,255,.85); font-size: .68rem; text-transform: uppercase; letter-spacing: .4px; font-weight: 600; }
.table-pg thead th {
    background:#f8f9fc !important; font-size:.68rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.4px; color:#495057;
    border-bottom:2px solid #eef0f4;
}
.table-pg tbody td { vertical-align: middle; border-bottom:1px solid #f1f3f5; }
.table-pg tbody tr:hover { background:#fafbff; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="pose-hero">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fs-20 mb-1 fw-bold" style="color:#fff;"><i class="ri-building-2-line me-1"></i> Programas de otras sedes</h4>
            <div class="small" style="color:rgba(255,255,255,.85);">Programas registrados por asesores fuera de la sede central. La inscripción exige recibo y factura del participante.</div>
        </div>
        <button type="button" class="btn btn-light fw-semibold" onclick="abrirModal('','')"><i class="ri-add-line me-1"></i> Registrar programa</button>
    </div>
    <div class="row g-2 mt-2">
        <div class="col">
            <div class="pg-stat"><div class="v">{{ $resumen['programas'] }}</div><div class="l">Programas</div></div>
        </div>
        <div class="col">
            <div class="pg-stat"><div class="v">{{ $resumen['inscritos'] }}</div><div class="l">Inscritos</div></div>
        </div>
        <div class="col">
            <div class="pg-stat"><div class="v">Bs {{ number_format($resumen['cobrado'], 2, ',', '.') }}</div><div class="l">Cobrado</div></div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card" style="border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 4px 18px -10px rgba(0,0,0,.12);">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.marketing.programas-otras-sedes.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-auto">
                <label class="form-label mb-1 small text-muted fw-semibold">Gestión</label>
                <select name="gestion" class="form-select form-select-sm" onchange="this.form.submit()">
                    @forelse($gestiones as $g)
                        <option value="{{ $g }}" @if((string)$gestion === (string)$g) selected @endif>{{ $g }}</option>
                    @empty
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    @endforelse
                </select>
            </div>
            <div class="col-8">
                <label class="form-label small text-muted fw-semibold">Buscar</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Nombre o código">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="ri-filter-3-line me-1"></i> Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-pg align-middle mb-0">
                <thead>
                    <tr>
                        <th>Programa</th>
                        <th>Sede origen</th>
                        <th>Tipo</th>
                        <th class="text-center">Inscritos</th>
                        <th class="text-end">Cobrado</th>
                        <th class="text-center">Inscripciones</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($query as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->nombre }}</div>
                            <div class="small text-muted">{{ $p->codigo ?: '—' }} · Gestión {{ $p->gestion }}</div>
                        </td>
                        <td>{{ $p->sede_origen ?: '—' }}</td>
                        <td><span class="badge bg-soft-primary text-primary">{{ $p->tipo ?: 'General' }}</span></td>
                        <td class="text-center fw-semibold">{{ $p->inscritos }}</td>
                        <td class="text-end fw-semibold">Bs {{ number_format($p->total_cobrado, 2, ',', '.') }}</td>
                        <td class="text-center">
                            @if((int)$p->inscripciones_habilitadas === 1)
                                <span class="badge bg-soft-success text-success">Habilitadas</span>
                            @else
                                <span class="badge bg-soft-danger text-danger">Cerradas</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if((int)$p->estado === 1)
                                <span class="badge bg-soft-success text-success">Activo</span>
                            @else
                                <span class="badge bg-soft-muted text-muted">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.marketing.programas-otras-sedes.registro', $p->id) }}" class="btn btn-sm btn-soft-primary" title="Inscribir participantes"><i class="ri-add-circle-line"></i></a>
                            <button class="btn btn-sm btn-soft-warning" onclick="abrirModal('{{ $p->id }}','{{ addslashes($p->nombre) }}', {{ $p->tipo_id ?? 'null' }})" title="Editar"><i class="ri-pencil-line"></i></button>
                            <form action="{{ route('admin.marketing.programas-otras-sedes.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el programa y sus inscripciones?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-soft-danger" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="ri-inbox-2-line me-1"></i> No hay programas registrados para la gestión {{ $gestion }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal crear/editar --}}
<div class="modal fade" id="modalPrograma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formPrograma" method="POST" action="{{ route('admin.marketing.programas-otras-sedes.store') }}">
                @csrf
                <input type="hidden" name="_method" id="f-metodo" value="POST">
                <input type="hidden" name="id" id="f-id" value="">
                <div class="modal-header" style="border-bottom:1px dashed #eef0f4;">
                    <h5 class="modal-title"><i class="ri-building-2-line me-1"></i> <span id="modalTitulo">Registrar programa</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Nombre del programa <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="f-nombre" class="form-control" required placeholder="Ej. Diplomado en Gestión Comercial">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Código</label>
                            <input type="text" name="codigo" id="f-codigo" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Gestión</label>
                            <input type="number" name="gestion" class="form-control" value="{{ $gestion }}" min="2000" max="2100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Sede origen <span class="text-danger">*</span></label>
                            <input type="text" name="sede_origen" id="f-sede" class="form-control" required placeholder="Ej: Cochabamba">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Tipo (puntos)</label>
                            <select name="tipo_id" id="f-tipo_id" class="form-select">
                                <option value="">— Sin tipo —</option>
                                @foreach($tipos as $ti)
                                    <option value="{{ $ti->id }}">{{ $ti->nombre }} · {{ number_format($ti->puntos, 1, ',', '.') }} pts</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Modalidad</label>
                            <input type="text" name="modalidad" id="f-modalidad" class="form-control" placeholder="Virtual / Presencial">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Matrícula (Bs)</label>
                            <input type="number" name="matricula" id="f-matricula" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Colegiatura (Bs)</label>
                            <input type="number" name="colegiatura" id="f-colegiatura" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">N° módulos</label>
                            <input type="number" name="n_modulos" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" id="f-fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Fecha fin</label>
                            <input type="date" name="fecha_fin" id="f-fecha_fin" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Observación</label>
                            <textarea name="observacion" id="f-observacion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="inscripciones_habilitadas" id="f-insc" value="1" checked>
                                <label class="form-check-label" for="f-insc">Inscripciones habilitadas</label>
                            </div>
                            <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input" type="checkbox" name="estado" id="f-estado" value="1" checked>
                                <label class="form-check-label" for="f-estado">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px dashed #eef0f4;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
function abrirModal(id, nombre, tipoId) {
    document.getElementById('modalTitulo').textContent = id ? 'Editar programa' : 'Registrar programa';
    document.getElementById('f-id').value = id || '';
    document.getElementById('f-metodo').value = id ? 'PUT' : 'POST';
    document.getElementById('formPrograma').action = id
        ? '{{ url("admin/marketing/programas-otras-sedes") }}/' + id
        : '{{ route("admin.marketing.programas-otras-sedes.store") }}';
    document.getElementById('f-nombre').value = nombre || '';
    document.getElementById('f-tipo_id').value = (tipoId && tipoId !== 'null') ? tipoId : '';
    const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPrograma'));
    m.show();
}
</script>
@endpush