<x-guest-layout>
    <div class="card-body p-4">
        <div class="text-center mt-2">
            <h5 class="text-primary">¿Olvidaste tu contraseña?</h5>
            <p class="text-muted">Te enviaremos un enlace para restablecerla</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="form-label" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <button class="btn btn-success w-100" type="submit">Enviar Enlace</button>
            </div>
        </form>
    </div>
</x-guest-layout>