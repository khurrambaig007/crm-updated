<?php

use App\Http\Middleware\EnsureCompanyProfileIsComplete;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user->isSuperAdmin() || $user->can('dashboard.view')) {
                return route('dashboard');
            }

            foreach (config('system.navigation') as $section) {
                foreach ($section['items'] as $item) {
                    if ($user->can($item['permission'] ?? '__none__') && Route::has($item['route'])) {
                        return route($item['route']);
                    }
                }
            }

            return route('logout');
        });

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);

        // A dedicated group so the profile gate runs straight after authentication.
        //
        // This must be a NEW group name, not an extension of 'auth'. Two traps:
        //  - appendToGroup('auth', ...) would create a group literally named
        //    'auth', and MiddlewareNameResolver checks the group map (line 35)
        //    before the alias map (line 44), so it would shadow the 'auth' alias
        //    and Illuminate\Auth\Middleware\Authenticate would never run.
        //  - alias(['auth' => [ ... ]]) also fails: the alias map is only read at
        //    line 44 via string concatenation, so an array value throws
        //    "Array to string conversion". Only Closure aliases short-circuit.
        $middleware->appendToGroup('authenticated', [
            Authenticate::class,
            EnsureCompanyProfileIsComplete::class,
        ]);
    })
    ->withEvents(discover: __DIR__.'/../app/Listeners')
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
