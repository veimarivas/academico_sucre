<x-guest-layout>
    <div class="card-body p-4">
        <div class="text-center mt-2">
            <h5 class="text-primary">Verifica tu email</h5>
            <p class="text-muted">Gracias por registrarte. Por favor verifica tu dirección de email.</p>
        </div>

        <div class="alert alert-info mb-4">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 d-flex justify-content-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn btn-success" type="submit">Reenviar Email de Verificación</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</x-guest-layout>