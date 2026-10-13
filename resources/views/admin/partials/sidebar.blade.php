@php
    // Mapeo: ruta -> id de grupo padre (collapse) que debe quedar abierto/activo
    $routeGroup = [
        'admin.programas.*'         => 'sidebarAcademico',
        'admin.tipos.*'             => 'sidebarAcademico',
        'admin.areas.*'             => 'sidebarAcademico',
        'admin.cronogramas.*'       => 'sidebarAcademico',
        'admin.seguimiento.*'       => 'sidebarAcademico',
        'admin.seguimiento-diario.*' => 'sidebarAcademico',
        'admin.control-responsables.*' => 'sidebarAcademico',
        'admin.responsables.*'      => 'sidebarAcademico',

        'admin.contable.*'          => 'sidebarContable',

        'admin.participantes.*'     => 'sidebarPersonas',
        'admin.docentes.*'          => 'sidebarPersonas',

        'admin.marketing.*'         => 'sidebarMarketing',

        'admin.seguridad.*'         => 'sidebarSeguridad',
        'admin.folios.*'            => 'sidebarSeguridad',
    ];

    $currentGroup = null;
    foreach ($routeGroup as $pattern => $gid) {
        if (request()->routeIs($pattern)) { $currentGroup = $gid; break; }
    }

    $isActive  = fn(...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
    $isGroupOpen = fn($gid) => $currentGroup === $gid;
@endphp

<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="36">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="44">
            </span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="36">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="" height="44">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover shadow-none" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">

                {{-- ============ CUENTA ============ --}}
                <li class="menu-title"><span>Cuenta</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isActive('admin.profile') }}" href="{{ route('admin.profile') }}">
                        <i class="ri-user-3-line"></i> <span>Mi perfil</span>
                    </a>
                </li>

                {{-- ============ GENERAL ============ --}}
                @can('dashboard.listar')
                <li class="menu-title"><span>General</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isActive('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isActive('admin.programas-vigentes*') }}" href="{{ route('admin.programas-vigentes') }}">
                        <i class="ri-bar-chart-line"></i> <span>Programas Vigentes</span>
                    </a>
                </li>
                @endcan

                {{-- Cronogramas: área académica + gerencia/supervisor de marketing --}}
                @if(auth()->user()?->hasAnyRole(['Super Admin', 'Academicos', 'Gerencia Marketing', 'Supervisor de Marketing', 'Responsable Académico']))
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isActive('admin.cronogramas.*') }}" href="{{ route('admin.cronogramas.index') }}">
                        <i class="ri-calendar-line"></i> <span>Cronogramas</span>
                    </a>
                </li>
                @endif

                {{-- ============ ACADÉMICO ============ --}}
                @canany(['programas.listar', 'tipos.listar', 'areas.listar', 'seguimiento.listar', 'responsables.listar'])
                <li class="menu-title"><span>Académico</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isGroupOpen('sidebarAcademico') ? 'active' : '' }}"
                       href="#sidebarAcademico" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ $isGroupOpen('sidebarAcademico') ? 'true' : 'false' }}" aria-controls="sidebarAcademico">
                        <i class="ri-graduation-cap-line"></i> <span>Área Académica</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGroupOpen('sidebarAcademico') ? 'show' : '' }}" id="sidebarAcademico">
                        <ul class="nav nav-sm flex-column">
                            @can('programas.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.programas.index') }}" class="nav-link {{ $isActive('admin.programas.*') && !$isActive('admin.programas.pagos-docentes') }}">
                                    <i class="ri-booklet-line"></i> Programas
                                </a>
                            </li>
                            @endcan
                            @if(auth()->user()?->hasAnyRole(['Super Admin', 'Responsable Académico', 'Contabilidad']))
                            <li class="nav-item">
                                <a href="{{ route('admin.programas.pagos-docentes') }}" class="nav-link {{ $isActive('admin.programas.pagos-docentes') }}">
                                    <i class="ri-money-dollar-circle-line"></i> Pagos a Docentes
                                </a>
                            </li>
                            @endif
                            @can('tipos.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.tipos.index') }}" class="nav-link {{ $isActive('admin.tipos.*') }}">
                                    <i class="ri-price-tag-3-line"></i> Tipos de Programa
                                </a>
                            </li>
                            @endcan
                            @can('areas.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.areas.index') }}" class="nav-link {{ $isActive('admin.areas.*') }}">
                                    <i class="ri-apps-2-line"></i> Áreas
                                </a>
                            </li>
                            @endcan
                            @can('programas.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.folios.index') }}" class="nav-link {{ $isActive('admin.folios.*') }}">
                                    <i class="ri-government-line"></i> Folios
                                </a>
                            </li>
                            @endcan
                            @if(auth()->user()?->hasAnyRole(['Super Admin', 'Responsable Académico']))
                            <li class="nav-item">
                                <a href="{{ route('admin.seguimiento.index') }}" class="nav-link {{ $isActive('admin.seguimiento.*') }}">
                                    <i class="ri-radar-line"></i> Seguimiento Académico
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.seguimiento-diario.index') }}" class="nav-link {{ $isActive('admin.seguimiento-diario.*') }}">
                                    <i class="ri-calendar-check-line"></i> Seguimiento Diario
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.control-responsables.index') }}" class="nav-link {{ $isActive('admin.control-responsables.*') }}">
                                    <i class="ri-user-follow-line"></i> Control Responsables
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.responsables.index') }}" class="nav-link {{ $isActive('admin.responsables.*') }}">
                                    <i class="ri-user-settings-line"></i> Responsables
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endcanany

                {{-- ============ CONTABLE (solo rol Contabilidad + Super Admin) ============ --}}
                @if(auth()->user()?->hasAnyRole(['Super Admin', 'Contabilidad']))
                <li class="menu-title"><span>Contable</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isGroupOpen('sidebarContable') ? 'active' : '' }}"
                       href="#sidebarContable" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ $isGroupOpen('sidebarContable') ? 'true' : 'false' }}" aria-controls="sidebarContable">
                        <i class="ri-money-dollar-circle-line"></i> <span>Contable</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGroupOpen('sidebarContable') ? 'show' : '' }}" id="sidebarContable">
                        <ul class="nav nav-sm flex-column">
                            <li class="menu-subtitle">Resumen</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contable.seguimiento') }}" class="nav-link {{ $isActive('admin.contable.seguimiento') }}">
                                    <i class="ri-line-chart-line"></i> Seguimiento Contable
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contable.retencion') }}" class="nav-link {{ $isActive('admin.contable.retencion') }}">
                                    <i class="ri-user-unfollow-line"></i> Cobranza y Retención
                                </a>
                            </li>
                            <li class="menu-subtitle">Gestión de cobros</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contable.asignaciones') }}" class="nav-link {{ $isActive('admin.contable.asignaciones') }}">
                                    <i class="ri-calendar-todo-line"></i> Asignaciones del Mes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contable.cuotas-vencidas') }}" class="nav-link {{ $isActive('admin.contable.cuotas-vencidas') }}">
                                    <i class="ri-error-warning-line"></i> Cuotas Vencidas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contable.cuotas-proximas') }}" class="nav-link {{ $isActive('admin.contable.cuotas-proximas') }}">
                                    <i class="ri-time-line"></i> Próximas Cuotas
                                </a>
                            </li>
                            <li class="menu-subtitle">Verificación</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.comprobantes-pago.index') }}" class="nav-link {{ $isActive('admin.comprobantes-pago.*') }}">
                                    <i class="ri-receipt-line"></i> Comprobantes
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                {{-- ============ WHATSAPP SEGUIMIENTO ESAM (usuarios autorizados) ============ --}}
                @if(auth()->user()?->whatsapp_autorizado || auth()->user()?->hasRole('Super Admin'))
                <li class="menu-title"><span>WhatsApp Seguimiento ESAM</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isActive('admin.ajustes.whatsapp') }}" href="{{ route('admin.ajustes.whatsapp') }}">
                        <i class="ri-whatsapp-line"></i> <span>Ajustes WhatsApp</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.seguimiento-esam') }}" target="_blank">
                        <i class="ri-external-link-line"></i> <span>Seguimiento ESAM</span>
                    </a>
                </li>
                @endif

                {{-- ============ PERSONAS (contable + académico) ============ --}}
                @if(auth()->user()?->hasAnyRole(['Super Admin', 'Contabilidad', 'Academicos', 'Responsable Académico']))
                <li class="menu-title"><span>Personas</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isGroupOpen('sidebarPersonas') ? 'active' : '' }}"
                       href="#sidebarPersonas" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ $isGroupOpen('sidebarPersonas') ? 'true' : 'false' }}" aria-controls="sidebarPersonas">
                        <i class="ri-group-line"></i> <span>Personas</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGroupOpen('sidebarPersonas') ? 'show' : '' }}" id="sidebarPersonas">
                        <ul class="nav nav-sm flex-column">
                            @can('participantes.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.participantes.index') }}" class="nav-link {{ $isActive('admin.participantes.index') }}">
                                    <i class="ri-team-line"></i> Participantes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.participantes.buscar') }}" class="nav-link {{ $isActive('admin.participantes.buscar') }}">
                                    <i class="ri-user-search-line"></i> Buscar Participante
                                </a>
                            </li>
                            @endcan
                            @can('docentes.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.docentes.index') }}" class="nav-link {{ $isActive('admin.docentes.*') }}">
                                    <i class="ri-user-star-line"></i> Docentes
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif

                {{-- ============ MARKETING (sin asesores) ============ --}}
                @if(auth()->user()?->hasAnyRole(['Super Admin', 'Contabilidad', 'Gerencia Marketing', 'Supervisor de Marketing']))
                <li class="menu-title"><span>Marketing</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isGroupOpen('sidebarMarketing') ? 'active' : '' }}"
                       href="#sidebarMarketing" data-bs-toggle="collapse" role="button"
                       aria-expanded="{{ $isGroupOpen('sidebarMarketing') ? 'true' : 'false' }}" aria-controls="sidebarMarketing">
                        <i class="ri-megaphone-line"></i> <span>Marketing</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGroupOpen('sidebarMarketing') ? 'show' : '' }}" id="sidebarMarketing">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.estadisticas') }}" class="nav-link {{ $isActive('admin.marketing.estadisticas') }}">
                                    <i class="ri-bar-chart-2-line"></i> Estadísticas Generales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.inversiones') }}" class="nav-link {{ $isActive('admin.marketing.inversiones') }}">
                                    <i class="ri-bar-chart-grouped-line"></i> Inversiones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.asesores') }}" class="nav-link {{ $isActive('admin.marketing.asesores', 'admin.marketing.asesor-show') }}">
                                    <i class="ri-user-voice-line"></i> Asesores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.asesores.lista') }}" class="nav-link {{ $isActive('admin.marketing.asesores.lista') }}">
                                    <i class="ri-list-check"></i> Listar Asesores
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.masterclass.index') }}" class="nav-link {{ $isActive('admin.marketing.masterclass') }}">
                                    <i class="ri-presentation-line"></i> Masterclass
                                </a>
                            </li>
<li class="nav-item">
    <a href="{{ route('admin.marketing.programas') }}" class="nav-link {{ $isActive('admin.marketing.programas') }}">
        <i class="ri-booklet-line"></i> Programas
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.marketing.programas-otras-sedes.index') }}" class="nav-link {{ $isActive('admin.marketing.programas-otras-sedes.*') }}">
        <i class="ri-building-2-line"></i> Programas otras sedes
    </a>
</li>
                            @if(auth()->user()?->hasAnyRole(['Super Admin', 'Contabilidad']))
                            <li class="nav-item">
                                <a href="{{ route('admin.marketing.publicidad.index') }}" class="nav-link {{ $isActive('admin.marketing.publicidad.*') }}">
                                    <i class="ri-shopping-cart-2-line"></i> Publicidad
                                </a>
                            </li>
                            <li class="nav-item ms-3">
                                <a href="{{ route('admin.marketing.publicidad.presupuestos') }}" class="nav-link py-1 {{ $isActive('admin.marketing.publicidad.presupuestos') }}" style="font-size:.82rem;">
                                    <i class="ri-wallet-3-line"></i> Presupuestos
                                </a>
                            </li>
                            <li class="nav-item ms-3">
                                <a href="{{ route('admin.marketing.publicidad.autogestion') }}" class="nav-link py-1 {{ $isActive('admin.marketing.publicidad.autogestion') }}" style="font-size:.82rem;">
                                    <i class="ri-hand-coin-line"></i> Autogestión
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(auth()->user()?->hasRole('Super Admin'))
                <li class="nav-item">
                    <a class="nav-link menu-link" data-bs-toggle="collapse"
                       href="#sidebarSeguridad" role="button"
                       aria-expanded="{{ $isGroupOpen('sidebarSeguridad') ? 'true' : 'false' }}" aria-controls="sidebarSeguridad">
                        <i class="ri-shield-keyhole-line"></i> <span>Seguridad</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGroupOpen('sidebarSeguridad') ? 'show' : '' }}" id="sidebarSeguridad">
                        <ul class="nav nav-sm flex-column">
                            @can('usuarios.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.seguridad.usuarios.index') }}" class="nav-link {{ $isActive('admin.seguridad.usuarios.*') }}">
                                    <i class="ri-team-line"></i> Usuarios
                                </a>
                            </li>
                            @endcan
                            @can('roles.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.seguridad.roles.index') }}" class="nav-link {{ $isActive('admin.seguridad.roles.*') }}">
                                    <i class="ri-shield-user-line"></i> Roles
                                </a>
                            </li>
                            @endcan
                            @can('permisos.listar')
                            <li class="nav-item">
                                <a href="{{ route('admin.seguridad.permisos.index') }}" class="nav-link {{ $isActive('admin.seguridad.permisos.*') }}">
                                    <i class="ri-key-2-line"></i> Permisos
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif

                {{-- ============ APPS DINÁMICAS ============ --}}
                @if(isset($menu) && count($menu) > 0)
                    <li class="menu-title"><span>Apps</span></li>

                    @foreach($menu as $item)
                        <li class="nav-item">
                            @if(isset($item['children']) && count($item['children']) > 0)
                                <a class="nav-link menu-link" href="#{{ $item['id'] }}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="{{ $item['id'] }}">
                                    <i class="{{ $item['icon'] ?? 'ri-folder-line' }}"></i> <span>{{ $item['label'] }}</span>
                                </a>
                                <div class="collapse menu-dropdown" id="{{ $item['id'] }}">
                                    <ul class="nav nav-sm flex-column">
                                        @foreach($item['children'] as $child)
                                            <li class="nav-item">
                                                <a href="{{ $child['url'] }}" class="nav-link">{{ $child['label'] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <a class="nav-link menu-link" href="{{ $item['url'] ?? '#' }}">
                                    <i class="{{ $item['icon'] ?? 'ri-folder-line' }}"></i> <span>{{ $item['label'] }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
