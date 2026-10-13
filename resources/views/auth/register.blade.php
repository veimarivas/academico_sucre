<x-guest-layout>
    <div class="card-body p-4">
        <div class="text-center mt-2">
            <h5 class="text-primary">Registro</h5>
            <p class="text-muted">Crea tu cuenta</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="name" :value="__('Name')" class="form-label" />
            <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" class="form-label" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" class="form-label" />
            <div class="position-relative auth-pass-inputgroup mb-3">
                <x-text-input id="password" class="form-control pe-5 password-input"
                    type="password"
                    name="password"
                    required autocomplete="new-password"
                    placeholder="Enter password" />
                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="form-label" />
            <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <button class="btn btn-success w-100" type="submit">Registrarse</button>
        </div>

        <div class="mt-4 text-center">
            <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Inicia Sesión</a></p>
        </div>
    </form>
    </div>
</x-guest-layout>@push('auth_js')
<script src="{{ asset('backend/assets/js/pages/password-addon.init.js') }}"></script>
@endpush