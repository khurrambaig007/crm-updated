<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware as SpatiePermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, mixed $permission, mixed $guard = null): Response
    {
        $authGuard = app(AuthManager::class)->guard($guard);

        if ($authGuard->check() && $authGuard->user()->isSuperAdmin()) {
            return $next($request);
        }

        return (new SpatiePermissionMiddleware)->handle($request, $next, $permission, $guard);
    }
}
