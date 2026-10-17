<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="Admin Dashboard" />
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#102e54" media="(prefers-color-scheme: dark)">

    @php
        $assetVersion = function ($relative) {
            $path = public_path($relative);
            return file_exists($path) ? filemtime($path) : time();
        };
    @endphp

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
    (function () {
        try {
            var saved = localStorage.getItem('avip-theme');
            if (saved === 'dark' || saved === 'light') {
                document.documentElement.setAttribute('data-bs-theme', saved);
            }
        } catch (e) {}
    })();
    </script>

    <script src="{{ asset('backend/assets/js/layout.js') }}"></script>
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/custom.min.css') }}?v={{ $assetVersion('backend/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/avip-theme.css') }}?v={{ $assetVersion('backend/assets/css/avip-theme.css') }}" rel="stylesheet" type="text/css" />

    @stack('css')

    <link href="{{ asset('backend/assets/css/avip-ui.css') }}?v={{ $assetVersion('backend/assets/css/avip-ui.css') }}" rel="stylesheet" type="text/css" />

    @if (request()->routeIs([
        'admin.programas.*',
        'admin.tipos.*',
        'admin.areas.*',
        'admin.cronogramas.*',
        'admin.seguimiento.*',
        'admin.seguimiento-diario.*',
        'admin.responsables.*',
        'admin.control-responsables.*',
    ]))
        <link href="{{ asset('backend/assets/css/avip-academico.css') }}?v={{ $assetVersion('backend/assets/css/avip-academico.css') }}" rel="stylesheet" type="text/css" />
    @endif

    @if (request()->routeIs('admin.marketing.*'))
        <link href="{{ asset('backend/assets/css/avip-marketing.css') }}?v={{ $assetVersion('backend/assets/css/avip-marketing.css') }}" rel="stylesheet" type="text/css" />
    @endif

    @if (request()->routeIs('admin.profile'))
        <link href="{{ asset('backend/assets/css/avip-profile.css') }}?v={{ $assetVersion('backend/assets/css/avip-profile.css') }}" rel="stylesheet" type="text/css" />
    @endif

    {{-- Capa responsive móvil: se carga al final para ajustar todo en tablet y teléfono --}}
    <link href="{{ asset('backend/assets/css/avip-mobile.css') }}?v={{ $assetVersion('backend/assets/css/avip-mobile.css') }}" rel="stylesheet" type="text/css" />

    @stack('css-after')
</head>
<body>
    <script>
    (function () {
        var root = document.documentElement;

        function currentMode() {
            return root.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        }

        function syncLayoutMode() {
            document.body.setAttribute('data-layout-mode', currentMode());
        }

        function syncThemeIcon() {
            var icon = document.querySelector('.light-dark-mode i');
            if (!icon) return;
            var dark = currentMode() === 'dark';
            icon.classList.toggle('bx-moon', !dark);
            icon.classList.toggle('bx-sun', dark);
        }

        function persistTheme() {
            var mode = currentMode();
            try { localStorage.setItem('avip-theme', mode); } catch (e) {}
            try {
                sessionStorage.setItem('data-bs-theme', mode);
                var attrs = root.attributes, snap = {};
                for (var i = 0; i < attrs.length; i++) {
                    var a = attrs[i];
                    if (a && a.nodeName && a.nodeName !== 'undefined') {
                        snap[a.nodeName] = a.nodeValue;
                        sessionStorage.setItem(a.nodeName, a.nodeValue);
                    }
                }
                sessionStorage.setItem('defaultAttribute', JSON.stringify(snap));
            } catch (e) {}
        }

        syncLayoutMode();
        document.addEventListener('DOMContentLoaded', syncThemeIcon);
        window.addEventListener('load', syncThemeIcon);
        if (window.MutationObserver) {
            new MutationObserver(function () {
                syncLayoutMode();
                syncThemeIcon();
                persistTheme();
            }).observe(root, { attributes: true, attributeFilter: ['data-bs-theme'] });
        }
    })();
    </script>
    <div id="layout-wrapper">
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        {{-- Velo del menú lateral en móvil: al tocarlo, el menú se cierra --}}
        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            @include('admin.partials.footer')
        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugins.js') }}"></script>
    <script>
    !document.getElementById('removeNotificationModal')&&(e=document.createElement('div'),e.id='removeNotificationModal',e.style.display='none',document.body.appendChild(e));
    !document.getElementById('back-to-top')&&(e=document.createElement('div'),e.id='back-to-top',e.style.display='none',document.body.appendChild(e));
    </script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <script>
    (function () {
        // En móvil, cerrar el menú lateral al tocar un enlace de navegación.
        var MOBILE = 767.98;

        function closeSidebar() {
            document.body.classList.remove('vertical-sidebar-enable');
            document.documentElement.setAttribute('data-sidebar-size', 'lg');
        }

        // El tema base marca el icono del menú como "abierto" en móvil, lo que
        // lo convierte en una flecha. En móvil el menú se abre con un toque,
        // así que se restaura el icono de hamburguesa.
        function resetHamburger() {
            var icon = document.querySelector('.hamburger-icon');
            if (icon && window.innerWidth <= MOBILE) icon.classList.remove('open');
        }

        resetHamburger();
        window.addEventListener('load', resetHamburger);

        document.addEventListener('click', function (e) {
            if (window.innerWidth > MOBILE) return;

            var link = e.target.closest ? e.target.closest('#navbar-nav a') : null;
            if (!link) return;

            // Los toggles de submenú solo abren/cierran la sección.
            if (link.getAttribute('data-bs-toggle') === 'collapse') return;
            if (link.getAttribute('href') === '#') return;

            closeSidebar();
            resetHamburger();
        }, true);

        // Al girar o ampliar la pantalla, restaurar el estado de escritorio.
        window.addEventListener('resize', function () {
            if (window.innerWidth > MOBILE) {
                document.body.classList.remove('vertical-sidebar-enable');
            } else {
                resetHamburger();
            }
        });
    })();
    </script>

    @stack('js')
</body>
</html>