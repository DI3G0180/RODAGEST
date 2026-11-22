@extends('layouts.app')
@section('title', 'Verificación en dos pasos')
@section('content')
<div class="mx-auto max-w-3xl space-y-5 px-4 py-6 sm:px-6">
    <header><p class="text-xs font-bold uppercase tracking-widest text-[#018FC7]">Seguridad de la cuenta</p><h1 class="text-2xl font-extrabold">Verificación en dos pasos</h1><p class="mt-1 text-sm text-slate-600">Vincula Google Authenticator o cualquier aplicación compatible con TOTP.</p></header>
    @if(session('status') === 'two-factor-enabled')<p class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">La verificación en dos pasos está activada.</p>@endif
    @if(session('status') === 'two-factor-disabled')<p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800">La verificación en dos pasos fue desactivada.</p>@endif
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="rounded-2xl border border-[#B1D0E2] bg-white p-5 sm:p-6">
        @if($user->two_factor_enabled)
            <p class="rounded-lg bg-emerald-50 p-3 text-sm font-semibold text-emerald-800">2FA está activado. El secreto no se vuelve a mostrar.</p><form action="{{ route('2fa.disable', [], false) }}" method="POST" class="mt-4 max-w-md space-y-2">@csrf @method('DELETE')<label class="block text-sm font-semibold">Confirma tu contraseña actual<input type="password" name="password" required autocomplete="current-password" class="mt-1 block min-h-11 w-full rounded-xl border border-[#B1D0E2] px-3"></label><button class="min-h-11 rounded-xl border border-red-200 px-4 font-semibold text-red-700">Desactivar 2FA</button></form>
        @else
            <div class="grid gap-5 sm:grid-cols-[260px_1fr]"><div class="mx-auto w-full max-w-[240px] rounded-xl border border-[#DBE7F3] p-2"><img class="h-auto w-full" src="data:image/svg+xml;base64,{{ $qr }}" alt="Código QR de vinculación de Google Authenticator"></div><div class="min-w-0"><h2 class="font-bold">1. Escanea el código QR</h2><p class="mt-1 text-sm text-slate-600">En Google Authenticator, toca “+” y escanea el QR. Si no puedes escanearlo, agrega esta clave manual:</p><code class="mt-3 block break-all rounded-lg bg-[#DBE7F3] p-3 text-sm font-bold tracking-wider">{{ $secret }}</code><p class="mt-3 text-xs text-slate-500">Cuenta: {{ $user->email }} · La clave se almacena cifrada.</p><form action="{{ route('2fa.enable', [], false) }}" method="POST" class="mt-5 space-y-3">@csrf<label class="block text-sm font-semibold">2. Ingresa el código de 6 dígitos<input name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required class="mt-1 block min-h-12 w-full rounded-xl border border-[#B1D0E2] px-4 text-lg tracking-[.4em]" placeholder="000000"></label><button class="min-h-11 w-full rounded-xl bg-[#018FC7] px-4 font-bold text-white">Confirmar y activar</button></form></div></div>
        @endif
    </section>
</div>
@endsection
