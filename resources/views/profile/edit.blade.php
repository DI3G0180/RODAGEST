@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- TARJETA DE INFORMACIÓN GENERAL -->
    <div class="bg-white rounded-2xl shadow-sm border border-tertiary overflow-hidden">
        <div class="p-6 sm:p-8 bg-primary-100 text-primary flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center font-bold text-2xl text-white shadow-lg shadow-blue-500/30 shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-xl font-bold text-primary truncate">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-[#294D61] truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="px-3.5 py-1.5 bg-primary-100 border border-primary/30 rounded-full text-xs font-semibold uppercase tracking-wider text-primary shrink-0">
                Rol: {{ Auth::user()->role ?? 'Operador' }}
            </div>
        </div>

        <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <span class="text-xs font-bold text-[#294D61] uppercase tracking-wider">Nombre Completo</span>
                <p class="text-[#05161A] font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">{{ Auth::user()->name }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-[#294D61] uppercase tracking-wider">Correo Electrónico</span>
                <p class="text-[#05161A] font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">{{ Auth::user()->email }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-[#294D61] uppercase tracking-wider">Terminal Asignada</span>
                <p class="text-[#05161A] font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">Terminal Chimalhuacán</p>
            </div>
            <div class="space-y-1">
                <span class="text-xs font-bold text-[#294D61] uppercase tracking-wider">Área</span>
                <p class="text-[#05161A] font-medium bg-slate-50 p-3 rounded-xl border border-slate-200">Equipo Rodante</p>
            </div>
        </div>
    </div>

    <!-- TARJETA DE SEGURIDAD / ACTUALIZAR CONTRASEÑA -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <h3 class="text-base font-bold text-[#05161A] mb-1">Seguridad de la Cuenta</h3>
        <p class="text-xs text-[#294D61] mb-6">Asegúrate de mantener tu cuenta protegida actualizando tu contraseña regularmente.</p>

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

        <form method="post" action="{{ route('password.update', [], false) }}" class="space-y-4 max-w-xl">
            @csrf
            @method('put')

            <div>
                <label class="block text-xs font-bold text-[#294D61] uppercase tracking-wider mb-1">Contraseña Actual</label>
                <input type="password" name="current_password"
                       class="w-full rounded-xl border border-slate-200 bg-white text-[#05161A] px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#294D61] uppercase tracking-wider mb-1">Nueva Contraseña</label>
                <input type="password" name="password"
                       class="w-full rounded-xl border border-slate-200 bg-white text-[#05161A] px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#294D61] uppercase tracking-wider mb-1">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-xl border border-slate-200 bg-white text-[#05161A] px-3 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 text-sm shadow-sm outline-none"
                       required>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl bg-[#0E7490] px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-[#083344] sm:w-auto">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
