<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TemporaryPasswordController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()->must_change_password, 404);

        return view('auth.temporary-password');
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->must_change_password, 404);
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ])->save();

        return redirect()->to(route('dashboard', [], false))->with('success', 'Contraseña actualizada. Ya puedes continuar.');
    }
}
