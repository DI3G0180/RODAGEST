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

        return $next($request);
    }
}
