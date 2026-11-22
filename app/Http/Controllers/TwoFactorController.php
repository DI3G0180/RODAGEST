<?php

namespace App\Http\Controllers;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function setup(Request $request, Google2FA $google2fa)
    {
        $user = $request->user();
        $secret = $user->google2fa_secret;
        if (! $secret) {
            $secret = $google2fa->generateSecretKey();
            $user->forceFill(['google2fa_secret' => $secret])->save();
        }
        $uri = $google2fa->getQRCodeUrl(config('app.name', 'RODAGEST'), $user->email, $secret);
        $renderer = new ImageRenderer(new RendererStyle(240, 8), new SvgImageBackEnd());
        $qr = base64_encode((new Writer($renderer))->writeString($uri));

        return view('auth.two-factor-setup', compact('secret', 'qr', 'user'));
    }

    public function enable(Request $request, Google2FA $google2fa)
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $user = $request->user();
        abort_unless($user->google2fa_secret, 422, 'Genera primero la clave de vinculación.');
        if (! $google2fa->verifyKey($user->google2fa_secret, $data['code'], 1)) return back()->withErrors(['code' => 'El código no es válido o ya venció.']);

        $user->forceFill(['two_factor_enabled' => true])->save();
        return back()->with('status', 'two-factor-enabled');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $request->user()->forceFill(['two_factor_enabled' => false, 'google2fa_secret' => null])->save();
        return back()->with('status', 'two-factor-disabled');
    }

    public function challenge(Request $request)
    {
        abort_unless($request->session()->has('2fa_user_id'), 403);
        return view('auth.two-factor-verify');
    }

    public function verify(Request $request, Google2FA $google2fa)
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $userId = $request->session()->get('2fa_user_id');
        $user = $userId ? User::whereKey($userId)->where('activo', true)->first() : null;
        abort_unless($user && $user->two_factor_enabled && $user->google2fa_secret, 403);
        if (! $google2fa->verifyKey($user->google2fa_secret, $data['code'], 1)) return back()->withErrors(['code' => 'Código incorrecto. Inténtalo de nuevo.']);

        $remember = $request->session()->pull('2fa_remember', false);
        $request->session()->forget('2fa_user_id');
        auth()->login($user, $remember);
        $request->session()->regenerate();
        return redirect()->to(route('dashboard', [], false));
    }
}
