<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            {{-- Izquierda: menú lateral --}}
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon" aria-label="Mostrar u ocultar menú">
                    <span class="hamburger-icon">
                        <span></span><span></span><span></span>
                    </span>
                </button>
            </div>

            @php
                $authUser = Auth::user();
                $puedeEsam = $authUser && ($authUser->hasRole('Super Admin') || $authUser->whatsapp_autorizado);
            @endphp

            {{-- Derecha --}}
            <div class="d-flex align-items-center gap-1 gap-sm-2">
                @if($puedeEsam)
                    <a href="{{ route('admin.ajustes.whatsapp') }}"
                       class="esam-status d-none d-md-inline-flex align-items-center"
                       id="esamStatus"
                       data-estado-url="{{ route('admin.seguimiento-esam.estado') }}"
                       title="Estado de la conexión con Seguimiento ESAM">
                        <span class="esam-dot" id="esamDot"></span>
                        <span class="esam-body">
                            <span class="esam-label">Seguimiento ESAM</span>
                            <span class="esam-text" id="esamText">Verificando…</span>
                        </span>
                    </a>
                    <span class="topbar-sep d-none d-md-block"></span>
                @endif

                <button type="button" class="btn-topbar-icon d-inline-flex light-dark-mode" aria-label="Cambiar tema">
                    <i class='bx bx-moon fs-22'></i>
                </button>

                {{-- Notificaciones --}}
                <div class="dropdown d-inline-block" id="notificacionesDropdown">
                    <button type="button" class="btn-topbar-icon noti-btn" id="page-header-notificaciones-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Notificaciones">
                        <i class="ri-notification-3-line fs-20"></i>
                        <span class="badge rounded-pill bg-danger noti-badge" id="notiBadge" style="display:none;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end noti-dropdown" aria-labelledby="page-header-notificaciones-dropdown" style="width:400px;padding:0;border-radius:14px;overflow:hidden;">
                        <div class="dropdown-header noti-header d-flex align-items-center justify-content-between" style="padding:.8rem 1rem;">
                            <span class="fw-semibold fs-13"><i class="ri-notification-3-line me-1"></i>Notificaciones</span>
                            <span class="badge bg-danger rounded-pill noti-header-count" id="notiHeaderCount">0</span>
                        </div>
                        <div id="notiList" style="max-height:400px;overflow-y:auto;">
                            <div class="text-center text-muted py-4" id="notiEmpty">
                                <i class="ri-inbox-line fs-32 d-block mb-2"></i>
                                <span style="font-size:.82rem;">Sin notificaciones pendientes</span>
                            </div>
                        </div>
                        <div class="dropdown-footer text-center border-top" id="notiFooter" style="display:none;padding:.45rem;">
                            <a href="{{ route('admin.notificaciones.index') }}" class="fw-semibold fs-12">
                                Ver todas <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <span class="topbar-sep"></span>

                @php
                    $authNombre = $authUser->fullname ?? $authUser->username ?? 'Usuario';
                    $authIniciales = strtoupper(collect(explode(' ', trim($authNombre)))
                        ->filter()->take(2)->map(fn($p) => substr($p, 0, 1))->implode(''));
                    $authRol = $authUser->getRoleNames()->first()
                        ?: ($authUser->asesor_id
                            ? DB::table('cargos')->join('asesores', 'asesores.cargo_id', '=', 'cargos.id')
                                ->where('asesores.id', $authUser->asesor_id)->value('cargos.nombre')
                            : null)
                        ?: ucwords(str_replace('_', ' ', $authUser->role ?? 'Usuario'));
                @endphp
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item shadow-none d-flex align-items-center topbar-user" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="topbar-avatar rounded-circle d-inline-flex align-items-center justify-content-center"
                              style="width:34px; height:34px; background:linear-gradient(135deg,#163b6b,#2a5d97); color:#fff; font-weight:800; font-size:.78rem;">
                            {{ $authIniciales }}
                        </span>
                        <span class="d-none d-md-flex flex-column ms-2 text-start lh-sm">
                            <span class="fw-bold text-dark" style="font-size:.82rem;">{{ $authNombre }}</span>
                            <span class="topbar-role"><i class="ri-shield-user-line"></i> {{ $authRol ?: 'Usuario' }}</span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width:260px; padding:0; border-radius:12px; overflow:hidden; box-shadow:0 18px 40px -16px rgba(10,26,58,.4);">
                        <div style="background:linear-gradient(135deg,#102e54 0%,#163b6b 100%); padding:.95rem 1rem; color:#fff; display:flex; align-items:center; gap:.7rem;">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:42px; height:42px; background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3); font-weight:800;">{{ $authIniciales }}</span>
                            <div style="min-width:0;">
                                <div class="fw-bold text-truncate" style="font-size:.85rem;">{{ $authNombre }}</div>
                                <div class="text-truncate" style="font-size:.7rem; opacity:.85;"><i class="ri-at-line"></i> {{ $authUser->username }}</div>
                            </div>
                        </div>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.profile') }}">
                            <i class="ri-user-3-line text-primary"></i> Mi Perfil
                        </a>
                        @if($authUser->canAccessMarketing())
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.profile') }}#tab-ofertas">
                            <i class="ri-megaphone-line text-success"></i> Ofertas en inscripción
                        </a>
                        @endif
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.profile') }}#tab-password">
                            <i class="ri-lock-password-line text-warning"></i> Cambiar contraseña
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                <i class="ri-logout-box-line"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
/* ===================== Topbar ===================== */
#page-topbar .navbar-header { gap: .5rem; }

.btn-topbar-icon {
    width: 40px; height: 40px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid transparent;
    border-radius: 12px;
    background: transparent;
    color: var(--avip-ink-600, #475467);
    transition: background .18s ease, color .18s ease, border-color .18s ease, transform .15s ease;
}
.btn-topbar-icon:hover { background: var(--avip-navy-50, #eef2f8); color: var(--avip-navy-700, #163b6b); }
.btn-topbar-icon:active { transform: scale(.95); }

.topbar-sep { width: 1px; height: 26px; background: var(--avip-line, #e4e9f0); }

.topbar-user { border-radius: 12px; padding: .25rem .5rem .25rem .25rem; transition: background .18s ease; }
.topbar-user:hover { background: var(--avip-navy-50, #eef2f8); }
.topbar-role { font-size: .68rem; color: #6c7a91; display: inline-flex; align-items: center; gap: .25rem; }
.topbar-role i { color: #405189; font-size: .75rem; }
[data-bs-theme="dark"] .topbar-role { color: #9aa7bd; }
[data-bs-theme="dark"] .topbar-role i { color: #7ea6d8; }

/* ===================== Notificaciones ===================== */
.noti-btn { position: relative; }
.noti-btn > .noti-badge {
    position: absolute;
    top: 5px; right: 4px;
    left: auto; bottom: auto;
    min-width: 18px; height: 18px;
    padding: 0 5px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .62rem; font-weight: 700; line-height: 1;
    border: 2px solid #fff;
    box-shadow: 0 3px 8px -2px rgba(16, 32, 60, .55) !important;
    pointer-events: none;
}
.noti-dropdown .noti-header {
    background: linear-gradient(135deg, #102e54 0%, #1d4a80 100%);
    color: #fff;
}
.noti-dropdown .noti-header .noti-header-count { background: rgba(255,255,255,.22) !important; }
.noti-dropdown .noti-item {
    padding: .65rem 1rem;
    border-bottom: 1px solid rgba(0,0,0,.05);
    transition: background .15s;
    cursor: pointer;
}
.noti-dropdown .noti-item:hover { background: rgba(64,81,137,.04); }
.noti-dropdown .noti-item:last-child { border-bottom: none; }
.noti-dropdown .noti-item .noti-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
}
.noti-dropdown .noti-item .noti-dot.dot-warning { background: #f7b84b; }
.noti-dropdown .noti-item .noti-dot.dot-danger  { background: #f06548; }
.noti-dropdown .noti-item .noti-dot.dot-info    { background: #299cdb; }
.noti-dropdown .noti-item .noti-dot.dot-success { background: #0ab39c; }
.noti-dropdown .noti-item .noti-grupo-tag {
    font-size: .6rem; font-weight: 600;
    padding: .05rem .4rem; border-radius: 8px;
    white-space: nowrap;
    line-height: 1.5;
}

/* ===================== Indicador Seguimiento ESAM ===================== */
.esam-status {
    gap: .55rem;
    padding: .3rem .75rem .3rem .65rem;
    border: 1px solid var(--avip-line, #e4e9f0);
    border-radius: 999px;
    background: #fff;
    text-decoration: none;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}
.esam-status:hover { border-color: #cfd8e3; box-shadow: 0 8px 18px -12px rgba(16,32,60,.55); }
.esam-dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #cbd5e1; flex-shrink: 0;
    transition: background .2s ease, box-shadow .2s ease;
}
.esam-body { display: flex; flex-direction: column; line-height: 1.15; }
.esam-label { font-size: .58rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; }
.esam-text { font-size: .76rem; font-weight: 700; color: #475467; }
.esam-status.is-online { border-color: #a7e5d3; background: #f2fbf7; }
.esam-status.is-online .esam-dot { background: #0ab39c; box-shadow: 0 0 0 4px rgba(10,179,156,.18); }
.esam-status.is-online .esam-text { color: #0a7d5a; }
.esam-status.is-online .esam-label { color: #57b79e; }
.esam-status.is-offline { border-color: #f6c9c0; background: #fef6f5; }
.esam-status.is-offline .esam-dot { background: #f06548; box-shadow: 0 0 0 4px rgba(240,101,72,.16); }
.esam-status.is-offline .esam-text { color: #c0392b; }
.esam-status.is-offline .esam-label { color: #d98a7d; }
[data-bs-theme="dark"] .esam-status { background: #1b2330; border-color: #2a3342; }
[data-bs-theme="dark"] .esam-text { color: #cbd5e1; }
[data-bs-theme="dark"] .topbar-sep { background: #2a3342; }
</style>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Notificaciones =====
    const notiBadge = document.getElementById('notiBadge');
    const notiHeaderCount = document.getElementById('notiHeaderCount');
    const notiList = document.getElementById('notiList');
    const notiEmpty = document.getElementById('notiEmpty');
    const notiUrl = '{{ route("admin.programas.notificaciones") }}';

    var diasSemana = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    function formatearFecha(fechaStr) {
        if (!fechaStr) return '';
        var p = fechaStr.split('-');
        if (p.length !== 3) return fechaStr;
        var d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
        return diasSemana[d.getDay()] + ' ' + parseInt(p[2]) + ' de ' + meses[d.getMonth()] + ' del ' + p[0];
    }

    function cargarNotificaciones() {
        if (!notiBadge) return;
        fetch(notiUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var items = data.items || [];
                var total = data.total || 0;

                if (total > 0) {
                    notiBadge.textContent = total > 99 ? '99+' : total;
                    notiBadge.style.display = '';
                } else {
                    notiBadge.style.display = 'none';
                }
                notiHeaderCount.textContent = total;
                document.getElementById('notiFooter').style.display = total > 5 ? '' : 'none';

                var mostrar = items.slice(0, 5);
                notiEmpty.style.display = mostrar.length > 0 ? 'none' : '';

                var existing = notiList.querySelectorAll('.noti-item');
                for (var i = 0; i < existing.length; i++) { existing[i].remove(); }

                for (var i = 0; i < mostrar.length; i++) {
                    var it = mostrar[i];
                    var dotClass = it.tipo === 'modulo_no_inicio' ? 'dot-warning'
                        : it.tipo === 'horario_vencido' && it.estado === 'Confirmado' ? 'dot-danger'
                        : 'dot-info';
                    var grupoLabel = it.tipo === 'modulo_no_inicio' ? 'Cambio estado' : 'Asistencia';
                    var grupoColor = it.tipo === 'modulo_no_inicio' ? '#f7b84b' : '#f06548';
                    var fechaFormateada = formatearFecha(it.fecha);
                    var el = document.createElement('a');
                    el.href = it.url;
                    el.className = 'noti-item d-flex align-items-start gap-2 text-decoration-none text-reset';
                    el.innerHTML =
                        '<span class="noti-dot ' + dotClass + '"></span>' +
                        '<div style="min-width:0;flex:1;">' +
                            '<div class="fw-semibold" style="font-size:.71rem;color:' + grupoColor + ';line-height:1.3;">' + fechaFormateada + '</div>' +
                            '<div class="fs-12 lh-sm my-1">' + it.mensaje + '</div>' +
                            '<div class="d-flex align-items-center gap-2">' +
                                '<span class="noti-grupo-tag" style="background:' + grupoColor + '18;color:' + grupoColor + ';">' + grupoLabel + '</span>' +
                            '</div>' +
                        '</div>';
                    notiList.appendChild(el);
                }
            })
            .catch(function () {});
    }

    cargarNotificaciones();
    setInterval(cargarNotificaciones, 60000);

    // ===== Estado de Seguimiento ESAM =====
    var esam = document.getElementById('esamStatus');
    if (!esam) return;
    var esamText = document.getElementById('esamText');

    function cargarEstadoEsam() {
        fetch(esam.dataset.estadoUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                esam.classList.toggle('is-online', !!d.connected);
                esam.classList.toggle('is-offline', !d.connected);
                if (d.connected) {
                    esamText.textContent = d.phone ? ('Conectado · ' + d.phone) : 'Conectado';
                } else if (d.status === 'disabled') {
                    esamText.textContent = 'No configurado';
                } else {
                    esamText.textContent = 'Desconectado';
                }
            })
            .catch(function () {
                esam.classList.remove('is-online');
                esam.classList.add('is-offline');
                esamText.textContent = 'Sin respuesta';
            });
    }

    cargarEstadoEsam();
    setInterval(cargarEstadoEsam, 60000);
});
</script>
@endpush
