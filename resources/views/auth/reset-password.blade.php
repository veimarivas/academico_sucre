<x-guest-layout>
    <div class="card-body p-4">
        <div class="text-center mt-2">
            <h5 class="text-primary">Restablecer contraseña</h5>
            <p class="text-muted">Ingresa tu nueva contraseña</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" class="form-label" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Enter email" />
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
            <button class="btn btn-success w-100" type="submit">Restablecer Contraseña</button>
        </div>
    </form>
    </div>
</x-guest-layout>

@push('auth_js')
<script src="{{ asset('backend/assets/js/pages/password-addon.init.js') }}"></script>
@endpush