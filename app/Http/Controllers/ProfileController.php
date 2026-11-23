<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Si el usuario es admin, también actualizar credenciales adicionales
        if (auth()->user()->role === 'admin') {
            // Aquí podrían añadirse actualizaciones adicionales para admin
        }

        return Redirect::to(route('profile.edit', [], false))->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        abort_if($user->role === 'admin', 403, 'La cuenta administradora no se puede eliminar desde el perfil.');
        $user->forceFill(['activo' => false, 'motivo_baja' => 'Baja solicitada por el usuario'])->save();
        \App\Models\Unidad::where('operador_id', $user->id)->update(['operador_id' => null]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
