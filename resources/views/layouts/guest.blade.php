<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $assetVersion = function ($relative) {
            $path = public_path($relative);
            return file_exists($path) ? filemtime($path) : time();
        };
    @endphp

    <script src="{{ asset('backend/assets/js/layout.js') }}"></script>
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/custom.min.css') }}?v={{ $assetVersion('backend/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/avip-auth.css') }}?v={{ $assetVersion('backend/assets/css/avip-auth.css') }}" rel="stylesheet" type="text/css" />

    @stack('css')
</head>
<body class="avip-auth-body">
    <div class="avip-auth-bg">
        <span class="avip-orb avip-orb--gold"></span>
        <span class="avip-orb avip-orb--red"></span>
        <span class="avip-orb avip-orb--blue"></span>
    </div>

    <main class="avip-auth-stage">
        <div class="avip-card">
            {{ $slot }}
        </div>

        <footer class="avip-auth-footer">
            &copy; {{ date('Y') }} <strong>{{ config('app.name') === 'Laravel' ? 'Sistema de Cronogramas · Postgrado' : config('app.name') }}</strong>. Todos los derechos reservados.
        </footer>
    </main>

    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugins.js') }}"></script>

    @stack('auth_js')
</body>
</html>
