<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class MobileAuthController extends Controller
{
    public function login(Request $request, Google2FA $google2fa)
    {
        $data = $request->validate(['email' => ['required','email','max:255'], 'password' => ['required','string','max:255'], 'code' => ['nullable','digits:6'], 'device_name' => ['nullable','string','max:100']]);
        $user = User::where('email', $data['email'])->first();
        if (! $user || ! $user->activo || ! Hash::check($data['password'], $user->password)) return response()->json(['message'=>'Credenciales inválidas.'], 422);
        if ($user->must_change_password) return response()->json(['message'=>'Actualiza tu contraseña desde el acceso web antes de usar la API.','must_change_password'=>true], 403);
        if ($user->two_factor_enabled && (! isset($data['code']) || ! $google2fa->verifyKey($user->google2fa_secret, $data['code'], 1))) return response()->json(['message'=>'Se requiere un código 2FA válido.'], 422);
        $token = $user->createToken($data['device_name'] ?? 'mobile', ['*'], now()->addDays(30))->plainTextToken;
        return response()->json(['data'=>['token'=>$token,'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role]],'message'=>'Autenticación correcta.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['data'=>null,'message'=>'Sesión móvil cerrada.']);
    }
}
