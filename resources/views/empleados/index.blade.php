@extends('layouts.app')
@section('title', 'Gestión de personal')
@section('content')
<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <header class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-extrabold uppercase tracking-widest text-[#018FC7]">Administración</p><h1 class="mt-1 text-2xl font-extrabold">Personal y accesos</h1><p class="mt-1 text-sm text-slate-600">Edita roles, asignaciones, fotos y vigencia de cada cuenta.</p></div><a href="{{ route('dashboard', [], false) }}" class="rounded-lg border border-[#B1D0E2] px-4 py-2 text-sm font-bold text-[#018FC7]">Volver al panel</a></header>
    @if(session('success'))<div class="toast rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    <section class="rounded-2xl border border-[#B1D0E2] bg-white p-5 shadow-sm sm:p-6"><h2 class="text-lg font-bold">Crear empleado</h2>
        <form action="{{ route('empleados.store', [], false) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">@csrf
            <input class="field" name="name" required placeholder="Nombre completo"><input class="field" name="email" type="email" required placeholder="Correo electrónico"><select class="field" name="role" required><option value="">Rol</option><option value="inspector">Inspector</option><option value="operador">Operador</option><option value="limpieza">Limpieza</option></select>
            <input class="field" name="telefono" placeholder="Teléfono"><input class="field" name="fecha_ingreso" type="date"><input class="field" name="password" type="password" minlength="8" required placeholder="Contraseña inicial"><input class="field" name="foto" type="file" accept="image/*" capture="user">
            <select class="field sm:col-span-2" name="unidad_id"><option value="">Sin unidad asignada</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}">{{ $unidad->economico }}</option>@endforeach</select><button class="rounded-xl bg-[#018FC7] px-4 py-3 font-bold text-white hover:bg-[#006b96]">Crear cuenta</button>
        </form>
    </section>

    <form action="{{ route('empleados.index', [], false) }}" method="GET" class="grid min-w-0 gap-2 rounded-2xl border border-[#B1D0E2] bg-white p-4 sm:grid-cols-[minmax(0,1fr)_auto_auto]"><label class="sr-only" for="employee-search">Buscar por nombre, correo, puesto o clave</label><input id="employee-search" name="q" value="{{ request('q') }}" placeholder="Buscar nombre, correo, puesto o clave" class="field"><button class="min-h-11 rounded-xl bg-[#018FC7] px-4 font-bold text-white">Buscar</button><a href="{{ route('empleados.index', [], false) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#B1D0E2] px-4 font-semibold text-[#018FC7]">Limpiar</a></form>

    <section class="grid gap-4 lg:grid-cols-2">
        @forelse($empleados as $empleado)
        <article class="overflow-hidden rounded-2xl border border-[#B1D0E2] bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-[#DBE7F3] bg-[#FBF1E5] p-4">
                @if($empleado->foto)<img src="{{ '/storage/' . ($empleado->foto) }}" alt="Foto de {{ $empleado->name }}" class="h-12 w-12 rounded-full border border-white object-cover shadow-sm">@else<div class="grid h-12 w-12 place-items-center rounded-full bg-[#DBE7F3] font-extrabold text-[#018FC7]">{{ strtoupper(substr($empleado->name,0,1)) }}</div>@endif
                <div class="min-w-0 flex-1"><h3 class="truncate font-extrabold">{{ $empleado->name }}</h3><p class="truncate text-xs text-slate-500">{{ $empleado->email }}</p></div>
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-extrabold uppercase {{ $empleado->activo ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">{{ $empleado->activo ? 'Activo' : 'Inactivo' }}</span>
            </div>
            <div class="p-4 sm:p-5">
                <div class="mb-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500"><span>Rol: <b class="text-slate-700">{{ $empleado->role }}</b></span><span>Antigüedad: <b class="text-slate-700">@if($empleado->fecha_ingreso){{ $empleado->fecha_ingreso->diff(now())->format('%y años %m meses') }}@else—@endif</b></span><span>Unidad: <b class="text-slate-700">{{ $empleado->unidadAsignada?->economico ?? '—' }}</b></span><a href="{{ route('empleados.hoja-vida', [$empleado->id], false) }}" class="font-bold text-[#018FC7]">Abrir hoja de vida →</a></div>
                @if($empleado->id === auth()->id())<p class="rounded-lg bg-[#DBE7F3] p-3 text-xs text-slate-600">La cuenta con la que iniciaste sesión no puede cambiarse desde este formulario.</p>@else
                <form action="{{ route('empleados.update', [$empleado->id], false) }}" method="POST" enctype="multipart/form-data" class="grid gap-2 sm:grid-cols-2">@csrf @method('PATCH')
                    <input class="field" name="name" required value="{{ $empleado->name }}" aria-label="Nombre"><input class="field" name="email" type="email" required value="{{ $empleado->email }}" aria-label="Correo">@if($empleado->role === 'admin')<p class="rounded-lg bg-[#DBE7F3] p-3 text-sm font-semibold text-[#018FC7] sm:col-span-2">Rol administrador protegido</p>@else<select class="field" name="role" aria-label="Rol"><option value="inspector" @selected($empleado->role==='inspector')>Inspector</option><option value="operador" @selected($empleado->role==='operador')>Operador</option><option value="limpieza" @selected($empleado->role==='limpieza')>Limpieza</option></select>@endif<select class="field" name="activo" aria-label="Acceso"><option value="1" @selected($empleado->activo)>Acceso activo</option><option value="0" @selected(!$empleado->activo)>Dar de baja</option></select>
                    <input class="field" name="telefono" value="{{ $empleado->telefono }}" placeholder="Teléfono"><input class="field" name="fecha_ingreso" type="date" value="{{ $empleado->fecha_ingreso?->format('Y-m-d') }}"><select class="field" name="unidad_id"><option value="">Sin unidad asignada</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" @selected($empleado->unidadAsignada?->id===$unidad->id)>{{ $unidad->economico }}</option>@endforeach</select>
                    <input class="field" name="password" type="password" minlength="8" placeholder="Nueva contraseña (opcional)"><input class="field" name="foto" type="file" accept="image/*" capture="user"><button class="rounded-xl bg-[#018FC7] px-4 py-2.5 font-bold text-white sm:col-span-2">Guardar cambios</button>
                </form>@endif
            </div>
        </article>
        @empty<div class="rounded-xl border border-dashed border-[#B1D0E2] bg-white p-10 text-center text-slate-500">No hay empleados registrados.</div>@endforelse
    </section>
    <div class="rounded-xl bg-white p-3">{{ $empleados->links() }}</div>
</div>
<style>.field{width:100%;min-width:0;border:1px solid #B1D0E2;border-radius:.7rem;background:#fff;padding:.68rem .75rem;font-size:.84rem;outline:none}.field:focus{border-color:#018FC7;box-shadow:0 0 0 3px #018FC722}</style>
<script>setTimeout(()=>document.querySelectorAll('.toast').forEach(el=>{el.style.transition='opacity .35s';el.style.opacity='0';setTimeout(()=>el.remove(),400)}),4000)</script>
@endsection
