<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Obtener el rol del usuario
        $role = Auth::user()?->role;

        // Definir la redirección por defecto según el rol
        $fallback = match($role) {
            'admin' => route('dashboard'),
            'intendente' => route('aseos.index'),
            'inspector' => route('inspecciones.index'),
            'operador' => route('aseos.index'),
            'limpieza' => route('aseos.index'),
            default => route('dashboard'),
        };

        // Verificar si hay una URL intended y es válida para su rol
        $intended = $request->session()->get('intended', null);
        if ($intended && $this->isValidRedirect($intended, $role)) {
            return redirect()->intended($intended);
        }

        return redirect()->intended($fallback);
    }

    /**
     * Verificar si la URL de redirección es válida para el rol del usuario.
     */
    protected function isValidRedirect(string $url, string $role): bool
    {
        // URLs permitidas por rol
        $allowed = [
            'admin' => ['dashboard', 'unidades.index', 'inspecciones.index', 'quejas.index', 'aseos.index', 'profile.edit'],
            'intendente' => ['aseos.index', 'profile.edit'],
            'inspector' => ['inspecciones.index', 'profile.edit'],
            'operador' => ['aseos.index', 'profile.edit'],
            'limpieza' => ['aseos.index', 'profile.edit'],
        ];

        // Verificar si la URL está en la lista de permitida para este rol
        $allowedRoutes = $allowed[$role] ?? [];

        // Verificar coincidencia exacta o por prefijo
        foreach ($allowedRoutes as $allowedRoute) {
            if (route($allowedRoute) === $url || 
                str_contains($url, $allowedRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}