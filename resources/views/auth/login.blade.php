<x-guest-layout>
    <div class="avip-auth">
        <aside class="avip-auth__aside">
            <span class="avip-auth__aside-shine"></span>

            <div class="avip-brand">
                <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="{{ config('app.name') }}" class="avip-brand__logo">
            </div>

            <div class="avip-auth__aside-body">
                <h1>Plataforma de <span>Cronogramas</span></h1>
                <p class="avip-lead">Administra programas, inscripciones y seguimiento académico desde un solo panel.</p>

                <ul class="avip-features">
                    <li><i class="ri-calendar-check-line"></i> Programación y cronogramas</li>
                    <li><i class="ri-team-line"></i> Control de participantes</li>
                    <li><i class="ri-line-chart-line"></i> Seguimiento y reportes</li>
                </ul>
            </div>

            <div class="avip-auth__aside-foot">
                <i class="ri-lock-2-line"></i> Conexión segura y cifrada
            </div>
        </aside>

        <section class="avip-auth__main">
            <div class="avip-auth__main-inner">
                <div class="avip-form-brand">
                    <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="{{ config('app.name') }}">
                </div>
                <div class="avip-form-head">
                    <h2>Inicia sesión</h2>
                    <p>Ingresa tus credenciales para acceder al sistema.</p>
                </div>

                <x-auth-session-status class="avip-alert avip-alert--success" :status="session('status')" />

                @if (session('error'))
                    <div class="avip-alert avip-alert--danger" role="alert">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="avip-field">
                        <label for="email" class="avip-label">Correo o usuario</label>
                        <div class="avip-input-wrap">
                            <i class="ri-user-3-line"></i>
                            <input id="email" type="text" name="email" value="{{ old('email') }}"
                                class="avip-input @error('email') is-invalid @enderror"
                                required autofocus autocomplete="username"
                                placeholder="ejemplo@correo.com o usuario">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="avip-error" />
                    </div>

                    <div class="avip-field">
                        <div class="avip-label-row">
                            <label for="password" class="avip-label">Contraseña</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                            @endif
                        </div>
                        <div class="avip-input-wrap auth-pass-inputgroup">
                            <i class="ri-lock-2-line"></i>
                            <input id="password" type="password" name="password"
                                class="avip-input password-input @error('password') is-invalid @enderror"
                                required autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="avip-toggle password-addon" aria-label="Mostrar contraseña">
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="avip-error" />
                    </div>

                    <div class="avip-remember">
                        <label class="avip-switch" for="remember_me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span class="avip-switch__track"></span>
                            Recordarme
                        </label>
                    </div>

                    <button type="submit" class="avip-btn">
                        <i class="ri-login-circle-line"></i> Iniciar sesión
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-guest-layout>

@push('auth_js')
<script src="{{ asset('backend/assets/js/pages/password-addon.init.js') }}"></script>
@endpush
