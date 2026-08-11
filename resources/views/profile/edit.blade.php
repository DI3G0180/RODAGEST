@extends('layouts.app')

@section('title', 'Mi Perfil - RodaGest')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- TARJETA DE INFORMACIÓN GENERAL -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center font-bold text-2xl text-white shadow-lg shadow-blue-500/30 shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-xl font-bold text-white truncate">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-slate-300 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="px-3.5 py-1.5 bg-blue-500/20 border border-blue-400/30 rounded-full text-xs font-semibold uppercase tracking-wider text-blue-300 shrink-0">
                Rol: {{ Auth::user()->role ?? 'Operador' }}
            </div>
        </div>

        <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nombre Completo</span>
                <p class="text-slate-800 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">{{ Auth::user()->name }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Correo Electrónico</span>
                <p class="text-slate-800 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">{{ Auth::user()->email }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terminal Asignada</span>
                <p class="text-slate-800 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">Terminal Chimalhuacán</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Área</span>
                <p class="text-slate-800 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">Equipo Rodante</p>
            </div>
        </div>
    </div>

    <!-- TARJETA DE SEGURIDAD / ACTUALIZAR CONTRASEÑA -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-800 mb-1">Seguridad de la Cuenta</h3>
        <p class="text-xs text-slate-500 mb-6">Asegúrate de mantener tu cuenta protegida actualizando tu contraseña regularmente.</p>

        @if (session('status') === 'password-updated')
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                ¡Contraseña actualizada correctamente!
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('put')

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Contraseña Actual</label>
                <input type="password" name="current_password"
                       class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nueva Contraseña</label>
                <input type="password" name="password"
                       class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-600/30 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</div>
@endsection