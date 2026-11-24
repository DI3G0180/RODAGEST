@extends('layouts.app')
@section('title', 'Hoja de vida')
@section('content')
@php
    $personal = [
        'curp' => 'CURP', 'rfc' => 'RFC', 'nss' => 'NSS', 'escolaridad' => 'Escolaridad',
        'fecha_nacimiento' => 'Fecha de nacimiento', 'lugar_nacimiento' => 'Lugar de nacimiento',
        'estado_civil' => 'Estado civil', 'nombre_padre' => 'Nombre del padre',
        'nombre_madre' => 'Nombre de la madre', 'nombre_conyuge' => 'Cónyuge',
        'identificacion' => 'Identificación', 'calle' => 'Calle', 'numero' => 'Número',
        'colonia' => 'Colonia', 'municipio' => 'Municipio', 'estado' => 'Estado',
        'codigo_postal' => 'Código postal', 'telefono_alterno' => 'Teléfono alterno',
    ];
    $laboral = [
        'estatus_imss' => 'Estatus IMSS', 'tipo_ingreso' => 'Tipo de ingreso',
        'fecha_reingreso' => 'Fecha de reingreso', 'licencia' => 'Licencia', 'tarjeton' => 'Tarjetón',
    ];
@endphp
<div class="mx-auto w-full max-w-6xl space-y-5 px-4 py-5 sm:px-6 sm:py-8">
    <header class="flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#2E6F73]">{{ $editable ? 'Administración · acceso restringido' : 'Mi expediente' }}</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Hoja de vida del operador</h1><p class="mt-1 text-sm text-slate-600">Datos personales y laborales de {{ $empleado->name }}.</p></div>
        <a href="{{ route($editable ? 'empleados.index' : 'dashboard', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#7FB3B8] bg-white px-4 font-semibold text-[#2E6F73]">Volver</a>
    </header>
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_240px]">
        <div class="rounded-2xl border border-[#7FB3B8] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold">Resumen</h2>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach(['Clave' => $empleado->id, 'Nombre' => $empleado->name, 'Puesto' => $empleado->puesto, 'Correo' => $empleado->email, 'Teléfono' => $empleado->telefono, 'Económico' => $empleado->unidadAsignada?->economico, 'Fecha de ingreso' => $empleado->fecha_ingreso?->format('d/m/Y'), 'Antigüedad' => $empleado->fecha_ingreso ? $empleado->fecha_ingreso->diff(now())->format('%y años, %m meses') : null, 'Estatus laboral' => $empleado->activo ? 'Activo' : 'Baja'] as $label => $value)
                    <div class="min-w-0 rounded-xl bg-[#EADCC8] p-3"><dt class="text-xs text-slate-600">{{ $label }}</dt><dd class="mt-1 break-words font-semibold">{{ $value ?: '—' }}</dd></div>
                @endforeach
            </dl>
        </div>
        <div class="flex flex-col items-center justify-center rounded-2xl border border-[#7FB3B8] bg-white p-5">
            @if($empleado->foto)<img src="{{ '/storage/' . $empleado->foto }}" alt="Foto de {{ $empleado->name }}" class="h-40 w-40 rounded-2xl border border-[#EADCC8] object-cover">@else<div class="grid h-40 w-40 place-items-center rounded-2xl bg-[#EADCC8] text-4xl font-black text-[#2E6F73]">{{ strtoupper(substr($empleado->name, 0, 1)) }}</div>@endif
            <p class="mt-3 text-center text-sm font-bold">{{ $empleado->role === 'operador' ? 'Operador' : ucfirst($empleado->role) }}</p>
        </div>
    </section>

    @if($editable)<section class="overflow-hidden rounded-2xl border border-[#7FB3B8] bg-white shadow-sm"><div class="border-b border-[#EADCC8] p-5"><h2 class="text-lg font-bold">Historial operativo</h2><p class="text-sm text-slate-600">Registros relacionados con esta persona.</p></div><div class="divide-y divide-[#EADCC8]">@forelse($eventos as $evento)<article class="grid gap-1 p-4 sm:grid-cols-[150px_120px_minmax(0,1fr)] sm:items-center"><time class="text-xs text-slate-500">{{ $evento['fecha']?->format('d/m/Y H:i') }}</time><span class="font-semibold">{{ $evento['tipo'] }}</span><span class="break-words text-sm text-[#0F3D3E]">{{ $evento['unidad'] ?: 'Sin unidad' }} · {{ $evento['detalle'] }}</span></article>@empty<p class="p-5 text-sm text-slate-500">No hay registros operativos vinculados.</p>@endforelse</div></section>
    @endif

    @if($editable)
        <form action="{{ route('empleados.hoja-vida.update', [$empleado->id], false) }}" method="POST" class="space-y-5">@csrf @method('PATCH')
            <section class="rounded-2xl border border-[#7FB3B8] bg-white p-5 shadow-sm sm:p-6"><h2 class="text-lg font-bold">Personal</h2><p class="mt-1 text-xs text-slate-500">CURP, RFC, NSS, domicilio y datos familiares se guardan cifrados. Solo Administración puede verlos y editarlos.</p><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($personal as $field => $label)<label class="block min-w-0 text-sm font-semibold text-[#0F3D3E]">{{ $label }}<input name="{{ $field }}" value="{{ old($field, $perfil->{$field}) }}" @if($field==='fecha_nacimiento')type="date"@else type="text"@endif class="sheet-field mt-1"></label>@endforeach
            </div></section>
            <section class="rounded-2xl border border-[#7FB3B8] bg-white p-5 shadow-sm sm:p-6"><h2 class="text-lg font-bold">Laboral</h2><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($laboral as $field => $label)<label class="block min-w-0 text-sm font-semibold text-[#0F3D3E]">{{ $label }}<input name="{{ $field }}" value="{{ old($field, $field === 'fecha_reingreso' ? $perfil->fecha_reingreso?->format('Y-m-d') : $perfil->{$field}) }}" @if($field==='fecha_reingreso')type="date"@else type="text"@endif class="sheet-field mt-1"></label>@endforeach
            </div></section>
            <button class="min-h-12 w-full rounded-xl bg-[#C6601F] px-5 font-bold text-white sm:w-auto">Guardar hoja de vida</button>
        </form>
    @else
        <section class="rounded-2xl border border-[#7FB3B8] bg-white p-5 shadow-sm sm:p-6"><h2 class="text-lg font-bold">Datos de expediente</h2><dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([...$personal, ...$laboral] as $field => $label)@php($profileValue = $field === 'fecha_reingreso' ? $perfil->fecha_reingreso?->format('d/m/Y') : $perfil->{$field})<div class="min-w-0 rounded-xl border border-[#EADCC8] p-3"><dt class="text-xs text-slate-500">{{ $label }}</dt><dd class="mt-1 break-words font-medium">{{ $profileValue ?: '—' }}</dd></div>@endforeach
        </dl></section>
    @endif
</div>
<style>.sheet-field{display:block;width:100%;min-width:0;border:1px solid #7FB3B8;border-radius:.75rem;background:white;padding:.75rem .85rem;outline:none}.sheet-field:focus{border-color:#2E6F73;box-shadow:0 0 0 3px #2E6F7333}</style>
@endsection
