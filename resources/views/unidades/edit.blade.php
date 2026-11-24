@extends('layouts.app')
@section('title', 'Editar unidad ' . $unidad->economico)
@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-5 sm:px-6 sm:py-8"><section class="rounded-3xl border border-[#6DA5C0]/50 bg-white p-5 shadow-sm sm:p-7">
    <p class="text-xs font-bold uppercase tracking-[.16em] text-[#0C7075]">Administración de flota</p><h1 class="mt-1 break-words text-2xl font-extrabold sm:text-3xl">Editar unidad {{ $unidad->economico }}</h1>
    @if($errors->any())<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form action="{{ route('unidades.update', [$unidad->id], false) }}" method="POST" class="mt-5 grid min-w-0 gap-4 sm:grid-cols-2">@csrf @method('PUT')
        <label class="field-label">Número económico<input value="{{ $unidad->economico }}" readonly aria-readonly="true" class="field cursor-not-allowed bg-[#F4F9FB]" title="El económico es inmutable"></label>
        <label class="field-label">Modelo<input value="{{ $unidad->modelo }}" readonly aria-readonly="true" class="field cursor-not-allowed bg-[#F4F9FB]" title="El modelo es inmutable"></label>
        <label class="field-label">Área<input name="area" value="{{ old('area', $unidad->area) }}" class="field"></label>
        <label class="field-label">Estado<select name="estado" class="field">@foreach(['operativa'=>'Operativa','en_taller'=>'En taller','en_limpieza'=>'En limpieza','fuera_servicio'=>'Fuera de servicio'] as $value=>$label)<option value="{{ $value }}" @selected(old('estado',$unidad->estado)===$value)>{{ $label }}</option>@endforeach</select></label>
        <label class="field-label sm:col-span-2">Operador asignado<select name="operador_id" class="field"><option value="">Sin operador asignado</option>@foreach($operadores as $operador)<option value="{{ $operador->id }}" @selected((string)old('operador_id',$unidad->operador_id)===(string)$operador->id)>{{ $operador->name }}</option>@endforeach</select></label>
        <fieldset class="grid min-w-0 gap-4 rounded-2xl border border-[#F4F9FB] bg-[#F4F9FB] p-4 sm:col-span-2 sm:grid-cols-2 sm:p-5">
            <legend class="px-2 font-bold">Mantenimiento preventivo</legend>
            <label class="field-label">Próxima fecha<input name="proximo_mantenimiento_fecha" type="date" value="{{ old('proximo_mantenimiento_fecha', $unidad->proximo_mantenimiento_fecha?->format('Y-m-d')) }}" class="field bg-white"></label>
            <p class="text-xs leading-5 text-[#294D61] sm:col-span-2">El odómetro actual se actualiza automáticamente al iniciar y cerrar turnos.</p>
        </fieldset>
        <div class="flex flex-col-reverse gap-2 pt-1 sm:col-span-2 sm:flex-row sm:justify-between"><a href="{{ route('unidades.show', [$unidad->id], false) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#6DA5C0]/50 px-5 font-semibold text-[#0C7075]">Ver ficha</a><div class="flex flex-col-reverse gap-2 sm:flex-row"><a href="{{ route('unidades.index', [], false) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#6DA5C0]/50 px-5 font-semibold text-[#0C7075]">Cancelar</a><button class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F969C] px-5 font-bold text-white">Guardar cambios</button></div></div>
    </form>
</section></div>
<style>.field-label{display:block;min-width:0;font-size:.875rem;font-weight:600;color:#294D61}.field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #6DA5C0;border-radius:.75rem;background:#fff;padding:.75rem .85rem;font-weight:400;outline:none}.field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}</style>
@endsection
