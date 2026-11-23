<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->activo, 403, 'La cuenta está inactiva. Contacta al administrador.');

        if ($request->user()->must_change_password && ! $request->routeIs('password.temporary.*')) {
            return redirect()->to(route('password.temporary.edit', [], false));
        }

        return $next($request);
    }
}
