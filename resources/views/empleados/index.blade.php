@extends('layouts.app')
@section('title', 'Gestión de personal')
@section('content')
<div class="mx-auto max-w-7xl space-y-4 p-3 sm:space-y-5 sm:p-6">
    <header class="flex flex-wrap items-center justify-between gap-2">
        <div class="min-w-0"><p class="text-xs font-extrabold uppercase tracking-widest text-[#0C7075]">Administración</p><h1 class="mt-1 text-xl font-bold tracking-tight text-[#083344] sm:text-2xl">Personal y accesos</h1><p class="mt-1 text-xs text-slate-500 sm:text-sm">Busca al personal y gestiona cada cuenta desde su expediente.</p></div>
        <div class="flex shrink-0 flex-wrap gap-2"><button type="button" onclick="const form=document.getElementById('employee-create');form?.setAttribute('open','open');form?.scrollIntoView({behavior:'smooth',block:'start'})" class="inline-flex min-h-10 items-center rounded-xl bg-[#0E7490] px-3 text-xs font-bold text-white sm:text-sm">+ Nuevo empleado</button><a href="{{ route('dashboard', [], false) }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-[#0E7490] sm:text-sm">Volver</a></div>
    </header>
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    <form id="employee-filter-form" action="{{ route('empleados.index', [], false) }}" method="GET" class="grid min-w-0 grid-cols-2 gap-2 rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm sm:p-4 lg:grid-cols-[minmax(0,1fr)_190px_190px_auto_auto]">
        <div class="col-span-2 flex min-w-0 gap-2 lg:col-span-1"><label class="sr-only" for="employee-search">Buscar empleado</label><div class="relative min-w-0 flex-1"><span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">⌕</span><input id="employee-search" name="q" value="{{ request('q') }}" placeholder="Buscar personal..." class="field h-10 pl-9 text-sm"></div><button aria-label="Buscar empleado" class="h-10 shrink-0 rounded-xl bg-[#0E7490] px-3 text-sm font-bold text-white">Buscar</button></div>
        <label class="sr-only" for="employee-role">Filtrar por rol</label><select id="employee-role" name="role" class="field h-10 text-sm"><option value="">Todos los roles</option>@foreach(['admin'=>'Administrador','inspector'=>'Inspector','operador'=>'Operador','limpieza'=>'Limpieza'] as $key=>$label)<option value="{{ $key }}" @selected(request('role')===$key)>{{ $label }}</option>@endforeach</select>
        <label class="sr-only" for="employee-status">Filtrar por estado</label><select id="employee-status" name="activo" class="field h-10 text-sm"><option value="">Todos los estados</option><option value="1" @selected(request('activo')==='1')>Activos</option><option value="0" @selected(request('activo')==='0')>Inactivos</option></select>
        <button class="h-10 rounded-xl bg-[#0E7490] px-3 text-sm font-bold text-white">Filtrar</button><a href="{{ route('empleados.index', [], false) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 px-3 text-sm font-semibold text-[#0E7490]">Limpiar</a>
    </form>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:p-5">
        <details id="employee-create" class="group"><summary class="flex min-h-10 cursor-pointer list-none items-center justify-between gap-3 font-bold marker:hidden"><span>Alta de personal</span><span class="rounded-xl bg-[#0E7490] px-4 py-2 text-sm font-bold text-white group-open:hidden">+ Nuevo empleado</span><span class="hidden rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-[#0E7490] group-open:inline-flex">Cerrar</span></summary>
            <form action="{{ route('empleados.store', [], false) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-3">@csrf
                <label class="form-label">Nombre completo<input class="field mt-1" name="name" required value="{{ old('name') }}" placeholder="Nombre completo"></label>
                <label class="form-label">Correo electrónico<input class="field mt-1" name="email" type="email" required value="{{ old('email') }}" placeholder="correo@empresa.com"></label>
                <label class="form-label">Rol<select class="field mt-1" name="role" required><option value="">Selecciona un rol</option>@foreach(['inspector'=>'Inspector','operador'=>'Operador','limpieza'=>'Limpieza'] as $key=>$label)<option value="{{ $key }}" @selected(old('role')===$key)>{{ $label }}</option>@endforeach</select></label>
                <label class="form-label">Teléfono<input class="field mt-1" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono"></label>
                <label class="form-label">Fecha de alta<input class="field mt-1" name="fecha_ingreso" type="date" min="2026-01-01" value="{{ old('fecha_ingreso') }}"></label>
                <label class="form-label">Unidad asignada<select class="field mt-1" name="unidad_id"><option value="">Sin unidad asignada</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" @selected(old('unidad_id')==$unidad->id)>{{ $unidad->economico }}</option>@endforeach</select></label>
                @include('components.password-policy', ['id' => 'new-employee-password', 'required' => true, 'label' => 'Contraseña inicial', 'placeholder' => 'Contraseña inicial', 'containerClass' => 'sm:col-span-2'])
                <label class="form-label">Foto de perfil<input class="field mt-1" name="foto" type="file" accept="image/jpeg,image/png,image/webp" capture="user"></label>
                <button class="min-h-12 rounded-xl bg-[#0F969C] px-4 font-bold text-white transition hover:bg-[#072E33] sm:col-span-2 lg:col-span-3">Crear cuenta</button>
            </form>
        </details>
    </section>

    <section aria-label="Directorio de personal" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($empleados as $empleado)
            <article class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                <div class="flex min-w-0 items-center gap-3">
                    @if($empleado->foto)<img src="{{ '/storage/'.$empleado->foto }}" alt="Foto de {{ $empleado->name }}" loading="lazy" class="h-14 w-14 shrink-0 rounded-full border border-slate-200 object-cover">@else<div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-[#DBE7F3] text-lg font-extrabold text-[#0C7075]">{{ strtoupper(mb_substr($empleado->name, 0, 1)) }}</div>@endif
                    <div class="min-w-0 flex-1"><h2 class="truncate text-base font-extrabold">{{ $empleado->name }}</h2><p class="truncate text-sm text-[#294D61]">{{ $empleado->email }}</p></div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-2"><span class="rounded-full bg-[#DBE7F3] px-3 py-1 text-xs font-bold text-[#294D61]">{{ ['admin'=>'Administrador','inspector'=>'Inspector','operador'=>'Operador','limpieza'=>'Limpieza'][$empleado->role] ?? ucfirst($empleado->role) }}</span><span class="rounded-full px-3 py-1 text-xs font-bold {{ $empleado->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $empleado->activo ? 'Activo' : 'Inactivo' }}</span></div>
                <dl class="mt-4 grid grid-cols-2 gap-2 text-sm"><div class="rounded-xl bg-[#F4F9FB] p-3"><dt class="text-xs text-[#294D61]">Fecha de alta / antigüedad</dt><dd class="mt-1 font-semibold">{{ $empleado->fecha_ingreso?->format('d/m/Y') ?? '—' }}@if($empleado->fecha_ingreso)<span class="mt-1 block text-xs font-normal text-[#294D61]">{{ $empleado->fecha_ingreso->diff(now())->format('%y años, %m meses') }}</span>@endif</dd></div><div class="rounded-xl bg-[#F4F9FB] p-3"><dt class="text-xs text-[#294D61]">Unidad asignada</dt><dd class="mt-1 truncate font-semibold">{{ $empleado->unidadAsignada?->economico ?? '—' }}</dd></div></dl>
                <a href="{{ route('empleados.show', [$empleado->id], false) }}" class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-[#0F969C] px-4 text-sm font-bold text-white transition hover:bg-[#072E33]">Ver expediente y gestionar →</a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#6DA5C0]/50 bg-white p-8 text-center text-sm text-[#294D61] md:col-span-2 lg:col-span-3">No hay empleados con esos filtros.</div>
        @endforelse
    </section>
    <div class="rounded-xl bg-white p-3">{{ $empleados->links() }}</div>
</div>
<style>.field{display:block;width:100%;min-width:0;min-height:2.75rem;border:1px solid #6DA5C0;border-radius:.75rem;background:white;padding:.7rem .8rem;outline:none}.field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}.form-label{display:block;min-width:0;font-size:.875rem;font-weight:650;color:#294D61}</style>
<script>document.addEventListener('DOMContentLoaded',()=>{const form=document.getElementById('employee-filter-form'),search=document.getElementById('employee-search');if(!form||!search)return;const submit=()=>typeof form.requestSubmit==='function'?form.requestSubmit():form.submit();let timer;search.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(submit,400)});for(const filter of [document.getElementById('employee-role'),document.getElementById('employee-status')])filter?.addEventListener('change',submit)});</script>
@endsection
