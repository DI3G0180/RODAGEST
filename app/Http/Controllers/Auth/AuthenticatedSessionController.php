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

        if ($request->user()->two_factor_enabled) {
            $user = $request->user();
            $request->session()->put(['2fa_user_id' => $user->id, '2fa_remember' => $request->boolean('remember')]);
            Auth::logout();
            $request->session()->regenerate();
            return redirect()->to(route('2fa.challenge', [], false));
        }

        $request->session()->regenerate();

        if ($request->user()->must_change_password) {
            return redirect()->to(route('password.temporary.edit', [], false));
        }

        return redirect()->to(route('dashboard', [], false));
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
