<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'programa.acceso' => \App\Http\Middleware\EnsureProgramaAccess::class,
            'whatsapp.autorizado' => \App\Http\Middleware\EnsureWhatsappAuthorized::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 404 → perfil (o login) con aviso.
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Página no encontrada'], 404);
            }

            if (Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('admin.profile')
                    ->with('error', 'La página que intentaste visitar no existe.');
            }

            return redirect()->route('login')
                ->with('error', 'La página que intentaste visitar no existe. Inicia sesión para continuar.');
        });

        // 403 (sin permisos) → perfil con aviso, en lugar de la pantalla de error.
        $exceptions->render(function (Throwable $e, Illuminate\Http\Request $request) {
            $forbidden = $e instanceof Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                ? $e->getStatusCode() === 403
                : $e instanceof Illuminate\Auth\Access\AuthorizationException;

            if (! $forbidden) {
                return null;
            }

            $message = 'No tenés permisos para acceder a esa vista.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            if (Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('admin.profile')->with('error', $message);
            }

            return redirect()->route('login')->with('error', $message);
        });
    })->create();
