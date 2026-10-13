@extends('admin.layouts.master')

@section('title', 'Gastos de Autogestión - Publicidad - ' . config('app.name'))

@push('css')
<style>
.pub-hero { position: relative; background: linear-gradient(135deg,#12345b 0%,#1c486c 45%,#0a6a90 100%); border-radius: 18px; padding: 1.7rem 1.9rem; color:#fff; overflow:hidden; box-shadow: 0 18px 40px -22px rgba(18,52,91,.75); margin-bottom: 1.25rem; }
.pub-hero .hero-icon { width:56px;height:56px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);border-radius:14px;font-size:1.6rem; }
.pub-hero h4 { color:#fff; font-weight:700; margin:0; }
.pub-hero .hero-sub { color:rgba(255,255,255,.85); font-size:.88rem; margin:0; }
.pub-hero .breadcrumb { background:transparent; padding:0; margin:0; }
.pub-hero .breadcrumb-item, .pub-hero .breadcrumb-item a { color:rgba(255,255,255,.85); }
.pub-hero .breadcrumb-item.active { color:#fff; font-weight:600; }
.pub-hero .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.55); }

.stat-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; overflow:hidden; position:relative; }
.stat-card::before { content:""; position:absolute; top:0; left:0; width:4px; height:100%; background: var(--stat-accent,#405189); }
[data-layout-mode="dark"] .stat-card { background:#212529; border-color:#2a2f34; }
.stat-card .stat-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.5px; color:#878a99; font-weight:700; margin-bottom:.4rem; }
.stat-card .stat-value { font-size:1.15rem; font-weight:700; color:#212529; line-height:1.2; }
[data-layout-mode="dark"] .stat-card .stat-value { color:#fff; }
.stat-card .stat-icon { width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; font-size:1.25rem; }

.filter-card { border:1px solid #eef0f4; border-radius:14px; background:#fff; box-shadow:0 4px 18px -10px rgba(64,81,137,.18); }
[data-layout-mode="dark"] .filter-card { background:#1d2125; border-color:#2a2f34; }
.filter-card .card-header { background:transparent; border-bottom:1px dashed #eef0f4; padding:1rem 1.25rem; }
.form-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:#878a99; margin-bottom:.35rem; }

.tabla-pub { width:100%; }
.tabla-pub th, .tabla-pub td { vertical-align: middle; padding:.65rem .6rem; }
.tabla-pub thead th { background:#f8f9fc !important; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#495057; border-bottom:2px solid #eef0f4; }
[data-layout-mode="dark"] .tabla-pub thead th { background:#1a1d20 !important; border-bottom-color:#2a2f34; }
.tabla-pub tbody tr { border-bottom:1px solid #f1f3f5; transition:background-color .15s ease; }
.tabla-pub tbody tr:hover { background-color:#f8f9fc; }

.file-preview { display:inline-flex; align-items:center; gap:.4rem; padding:.25rem .6rem; background:#f1f3f5; border-radius:6px; font-size:.75rem; color:#495057; text-decoration:none; }
.file-preview:hover { background:#e9ecef; color:#405189; }

.swal-file-preview { border-radius:14px !important; }
.modal-file-preview img, .modal-file-preview iframe { max-height:80vh; }
</style>
@endpush

@section('content')
<div class="mkt-page">
<div class="row">
    <div class="col-12">
        <div class="pub-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="ri-hand-coin-line"></i></div>
                <div>
                    <h4>Gastos de Autogestión</h4>
                    <p class="hero-sub">Gastos de publicidad con recursos propios de los asesores · {{ $gestion }}</p>
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item">Marketing</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.marketing.publicidad.index') }}">Publicidad</a></li>
                <li class="breadcrumb-item active">Autogestión</li>
            </ol>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- KPIs --}}
<div class="row g-2 mb-4">
    <div class="col-lg-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#f59e0b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Total invertido</p>
                        <h4 class="stat-value mb-0" id="kpi-total">Bs {{ number_format($totalMonto, 2) }}</h4>
                        <div class="fs-11 text-muted mt-1" id="kpi-registros">{{ $gastos->total() }} registro(s)</div>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-hand-coin-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#0ab39c;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Aprobado</p>
                        <h4 class="stat-value mb-0" id="kpi-aprobado">Bs {{ number_format($totalAprobado, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-success text-success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card h-100" style="--stat-accent:#f7b84b;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label">Pendiente</p>
                        <h4 class="stat-value mb-0" id="kpi-pendiente">Bs {{ number_format($totalPendiente, 2) }}</h4>
                    </div>
                    <div class="stat-icon bg-soft-warning text-warning"><i class="ri-time-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="filter-card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="ri-filter-3-line"></i>
        <span class="fw-semibold fs-13">Filtros</span>
    </div>
    <div class="card-body p-3">
        <form method="GET" id="autogestion-filter-form" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Gestión</label>
                <select name="gestion" class="form-select">
                    @foreach($gestiones as $g)
                        <option value="{{ $g }}" {{ $g == $gestion ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mes</label>
                <select name="mes" class="form-select">
                    <option value="0">Todos</option>
                    @foreach($meses as $num => $nom)
                        <option value="{{ $num }}" {{ $num == $mes ? 'selected' : '' }}>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Asesor</label>
                <select name="asesor_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($asesores as $a)
                        <option value="{{ $a->id }}" {{ $a->id == $asesorId ? 'selected' : '' }}>{{ $a->apellidos }}, {{ $a->nombres }} ({{ $a->cargo_nombre }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pendiente" {{ $estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobado" {{ $estado === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="rechazado" {{ $estado === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-sm"><i class="ri-filter-3-line me-1"></i>Filtrar</button>
                <button type="button" class="btn btn-soft-secondary btn-sm" id="btnLimpiarFiltros"><i class="ri-close-line me-1"></i>Limpiar</button>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de gastos --}}
@if($gastos->isEmpty())
<div class="empty-state">
    <div class="empty-icon"><i class="ri-inbox-2-line"></i></div>
    <h5 class="fw-bold mb-1">Sin gastos de autogestión</h5>
    <p class="text-muted mb-0">No hay gastos registrados para los filtros seleccionados.</p>
</div>
@else
<div class="table-responsive table-card">
    <table class="table tabla-pub tabla-autogestion" style="width:100%;">
        <thead>
            <tr>
                <th class="text-center" style="width:36px;">#</th>
                <th>Descripción</th>
                <th>Asesor</th>
                <th class="text-center">Periodo</th>
                <th class="text-center">Plataf.</th>
                <th class="text-center">Programa</th>
                <th class="text-end">Monto</th>
                <th class="text-center">Archivo</th>
                <th class="text-center">Estado</th>
                <th class="text-center" style="width:100px;">Acciones</th>
            </tr>
        </thead>
        <tbody id="autogestion-tbody">
            @include('admin.marketing.publicidad._autogestion_rows')
        </tbody>
    </table>
</div>
<div id="autogestion-pagination">
    {{ $gastos->links('admin.marketing.publicidad._autogestion_pagination') }}
</div>
@endif

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditAutogestion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0"><i class="ri-edit-2-line text-warning me-1"></i>Editar gasto de autogestión</h5>
                        <small class="text-muted" id="modalEditAsesorName"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Gestión <span class="text-danger">*</span></label>
                            <input type="number" name="gestion" class="form-control" value="{{ now()->year }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mes <span class="text-danger">*</span></label>
                            <select name="mes" class="form-select" required>
                                @foreach($meses as $num => $nom)
                                    <option value="{{ $num }}">{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto (Bs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plataforma</label>
                            <select name="plataforma" class="form-select">
                                <option value="">— Seleccionar —</option>
                                <option value="Facebook">Facebook Ads</option>
                                <option value="Instagram">Instagram Ads</option>
                                <option value="TikTok">TikTok Ads</option>
                                <option value="Google">Google Ads</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Programa</label>
                            <select name="programa_id" class="form-select" id="modalEditPrograma">
                                <option value="">— Sin programa —</option>
                                @foreach($programas as $prg)
                                    <option value="{{ $prg->id }}">{{ $prg->nombre }} ({{ $prg->codigo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nombre de la campaña</label>
                            <input type="text" name="campana_nombre" class="form-control" placeholder="Ej: Diplomado en Auditoría">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Enlace de la campaña</label>
                            <input type="url" name="enlace_campana" class="form-control" placeholder="https://www.facebook.com/ads/...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Página de Facebook</label>
                            <input type="url" name="pagina_facebook" class="form-control" placeholder="https://www.facebook.com/tupagina">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea name="descripcion" rows="2" class="form-control" placeholder="Ej: Publicación pagada Facebook 3 días" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="form-control" placeholder="Notas internas (opcional)"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comprobante actual</label>
                            <div id="modalEditArchivoActual" class="fs-12 text-muted"></div>
                            <label class="form-label mt-2">Reemplazar comprobante</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Débito actual</label>
                            <div id="modalEditDebitoActual" class="fs-12 text-muted"></div>
                            <label class="form-label mt-2">Reemplazar débito</label>
                            <input type="file" name="archivo_debito" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="ri-save-line me-1"></i>Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar autogestión (Bootstrap) --}}
<div class="modal fade modal-sec" id="modalDeleteAutogestion" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" id="formDeleteAutogestion">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div style="width:60px;height:60px;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;background:#fef3f2;border-radius:50%;font-size:1.75rem;color:#f06548;">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <h5 class="fw-bold mb-1">¿Eliminar gasto?</h5>
                    <p class="text-muted mb-0" id="deleteAutoDesc">—</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal File Preview (Bootstrap) --}}
<div class="modal fade" id="modalFilePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewTitle"><i class="ri-file-text-line me-1"></i>Archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3" id="filePreviewContent">
                <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted mb-0">Cargando...</p></div>
            </div>
        </div>
    </div>
</div>
</div>{{-- /mkt-page --}}
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {

    var defaultMes = {{ $mes }};

    // ─── File Preview (Bootstrap Modal) ───
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-preview-file');
        if (!btn) return;
        var url = btn.getAttribute('data-url');
        var title = btn.getAttribute('title') || 'Archivo';
        var ext = url.split('.').pop().toLowerCase();
        document.getElementById('filePreviewTitle').textContent = title;
        var content = document.getElementById('filePreviewContent');
        if (['jpg','jpeg','png','webp'].includes(ext)) {
            content.innerHTML = '<img src="' + url + '" class="img-fluid" style="max-height:75vh;border-radius:8px;" onerror="this.parentElement.innerHTML=\'<p class=\\\'text-muted py-5\\\'>Error al cargar la imagen</p>\'">';
        } else if (ext === 'pdf') {
            content.innerHTML = '<iframe src="' + url + '" style="width:100%;height:75vh;border:none;border-radius:8px;"></iframe>';
        } else {
            window.open(url, '_blank');
            return;
        }
        var modal = new bootstrap.Modal(document.getElementById('modalFilePreview'));
        modal.show();
    });

    // ─── Delete: set form action before modal shows ───
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-delete-auto');
        if (!btn) return;
        var id = btn.getAttribute('data-id');
        var desc = btn.getAttribute('data-desc');
        document.getElementById('formDeleteAutogestion').action = '{{ route("admin.marketing.publicidad.autogestion.destroy", ":id") }}'.replace(':id', id);
        document.getElementById('deleteAutoDesc').textContent = desc;
    });

    // ─── Edit Button ───
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-edit-auto');
        if (!btn) return;
        var id = btn.getAttribute('data-id');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line spin"></i>';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '{{ route("admin.marketing.publicidad.autogestion.edit", ":id") }}'.replace(':id', id));
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if (xhr.status !== 200) return;
            var res = JSON.parse(xhr.responseText);
            if (!res.success) return;
            var g = res.gasto;
            var modalEl = document.getElementById('modalEditAutogestion');
            var form = modalEl.querySelector('form');
            form.action = '{{ route("admin.marketing.publicidad.autogestion.update", ":id") }}'.replace(':id', id);
            form.querySelector('[name="gestion"]').value = g.gestion;
            form.querySelector('[name="mes"]').value = g.mes;
            form.querySelector('[name="estado"]').value = g.estado;
            form.querySelector('[name="fecha_inicio"]').value = g.fecha_inicio ? g.fecha_inicio.split(' ')[0] : '';
            form.querySelector('[name="fecha_fin"]').value = g.fecha_fin ? g.fecha_fin.split(' ')[0] : '';
            form.querySelector('[name="monto"]').value = g.monto;
            form.querySelector('[name="plataforma"]').value = g.plataforma || '';
            form.querySelector('[name="programa_id"]').value = g.programa_id || '';
            form.querySelector('[name="campana_nombre"]').value = g.campana_nombre || '';
            form.querySelector('[name="enlace_campana"]').value = g.enlace_campana || '';
            form.querySelector('[name="pagina_facebook"]').value = g.pagina_facebook || '';
            form.querySelector('[name="descripcion"]').value = g.descripcion;
            form.querySelector('[name="observaciones"]').value = g.observaciones || '';

            var asesorName = (g.asesor ? g.asesor.apellidos + ', ' + g.asesor.nombres : '');
            document.getElementById('modalEditAsesorName').textContent = asesorName;

            var storageUrl = '{{ asset('storage') }}';
            var archivoHtml = g.archivo_path
                ? '<a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="' + storageUrl + '/' + g.archivo_path + '" title="' + (g.archivo_original || '') + '"><i class="ri-attachment-2"></i> ' + (g.archivo_original || 'Ver') + '</a>'
                : '<span class="text-muted">Sin archivo</span>';
            document.getElementById('modalEditArchivoActual').innerHTML = archivoHtml;

            var debitoHtml = g.archivo_debito_path
                ? '<a href="javascript:void(0)" class="file-preview btn-preview-file" data-url="' + storageUrl + '/' + g.archivo_debito_path + '" title="' + (g.archivo_debito_original || '') + '"><i class="ri-bank-card-line"></i> ' + (g.archivo_debito_original || 'Ver') + '</a>'
                : '<span class="text-muted">Sin archivo</span>';
            document.getElementById('modalEditDebitoActual').innerHTML = debitoHtml;

            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        };
        xhr.onloadend = function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-edit-2-line"></i>';
        };
        xhr.send();
    });

    // ─── AJAX load data ───
    function loadData(page) {
        var form = document.getElementById('autogestion-filter-form');
        var params = new URLSearchParams();
        params.append('page', page);
        params.append('gestion', form.querySelector('[name="gestion"]').value);
        params.append('mes', form.querySelector('[name="mes"]').value);
        params.append('asesor_id', form.querySelector('[name="asesor_id"]').value);
        params.append('estado', form.querySelector('[name="estado"]').value);

        document.getElementById('autogestion-tbody').innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm" role="status"></div> <span class="text-muted ms-1">Cargando...</span></td></tr>';

        fetch('{{ route("admin.marketing.publicidad.autogestion") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('autogestion-tbody').innerHTML = data.rows_html;
            document.getElementById('autogestion-pagination').innerHTML = data.pagination_html;
            document.getElementById('kpi-total').textContent = 'Bs ' + Number(data.totalMonto).toLocaleString('es-BO', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('kpi-aprobado').textContent = 'Bs ' + Number(data.totalAprobado).toLocaleString('es-BO', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('kpi-pendiente').textContent = 'Bs ' + Number(data.totalPendiente).toLocaleString('es-BO', {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('kpi-registros').textContent = data.totalRegistros + ' registro(s)';
        })
        .catch(function() {
            document.getElementById('autogestion-tbody').innerHTML = '<tr><td colspan="10" class="text-center text-danger py-3"><i class="ri-error-warning-line me-1"></i>Error al cargar datos</td></tr>';
        });
    }

    // ─── Filter form submit (AJAX) ───
    document.getElementById('autogestion-filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });

    // ─── Clear filters ───
    document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
        var form = document.getElementById('autogestion-filter-form');
        form.querySelector('[name="gestion"]').value = '{{ now()->year }}';
        form.querySelector('[name="mes"]').value = defaultMes;
        form.querySelector('[name="asesor_id"]').value = '';
        form.querySelector('[name="estado"]').value = 'todos';
        loadData(1);
    });

    // ─── Pagination clicks (delegated) ───
    document.addEventListener('click', function(e) {
        var pagWrap = document.getElementById('autogestion-pagination');
        if (!pagWrap) return;
        var btn = e.target.closest('[data-page]');
        if (!btn || !pagWrap.contains(btn)) return;
        e.preventDefault();
        loadData(parseInt(btn.getAttribute('data-page')));
    });

});
</script>
@endpush