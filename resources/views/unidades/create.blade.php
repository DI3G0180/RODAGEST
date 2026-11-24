@extends('layouts.app')
@section('title', 'Registrar unidad')
@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-5 sm:px-6 sm:py-8">
    <section class="rounded-3xl border border-[#7FB3B8] bg-white p-5 shadow-sm sm:p-7">
        <p class="text-xs font-bold uppercase tracking-[.16em] text-[#2E6F73]">Administración de flota</p>
        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Registrar unidad</h1>
        <p class="mt-2 text-sm text-slate-600">Captura el económico, asignación y próximo servicio preventivo.</p>
        @if($errors->any())<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('unidades.store', [], false) }}" method="POST" class="mt-5 grid min-w-0 gap-4 sm:grid-cols-2">@csrf
            <label class="field-label">Número económico<input name="economico" required maxlength="20" value="{{ old('economico') }}" placeholder="ECO-104" class="field"></label>
            <label class="field-label">Modelo<input name="modelo" value="{{ old('modelo') }}" placeholder="Modelo" class="field"></label>
            <label class="field-label">Área<input name="area" value="{{ old('area') }}" placeholder="Área o terminal" class="field"></label>
            <label class="field-label">Estado inicial<select name="estado" class="field">@foreach(['operativa'=>'Operativa','en_taller'=>'En taller','en_limpieza'=>'En limpieza','fuera_servicio'=>'Fuera de servicio'] as $value=>$label)<option value="{{ $value }}" @selected(old('estado','operativa')===$value)>{{ $label }}</option>@endforeach</select></label>
            <label class="field-label sm:col-span-2">Operador asignado<select name="operador_id" class="field"><option value="">Sin operador asignado</option>@foreach($operadores as $operador)<option value="{{ $operador->id }}" @selected((string)old('operador_id')===(string)$operador->id)>{{ $operador->name }}</option>@endforeach</select></label>
            <fieldset class="grid min-w-0 gap-4 rounded-2xl border border-[#EADCC8] bg-[#EADCC8] p-4 sm:col-span-2 sm:grid-cols-2 sm:p-5">
                <legend class="px-2 font-bold text-[#0A0F14]">Mantenimiento preventivo</legend>
                <label class="field-label">Próxima fecha<input name="proximo_mantenimiento_fecha" type="date" value="{{ old('proximo_mantenimiento_fecha') }}" class="field bg-white"></label>
                <p class="text-xs leading-5 text-slate-600 sm:col-span-2">Se mostrarán alertas cuando se acerque la fecha del servicio. El odómetro se actualiza automáticamente desde los turnos registrados.</p>
            </fieldset>
            <div class="flex flex-col-reverse gap-2 pt-1 sm:col-span-2 sm:flex-row sm:justify-end">
                <a href="{{ route('unidades.index', [], false) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#7FB3B8] px-5 font-semibold text-[#2E6F73]">Cancelar</a>
                <button class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#C6601F] px-5 font-bold text-white hover:bg-[#A84D17]">Guardar unidad</button>
            </div>
        </form>
    </section>
</div>
<style>.field-label{display:block;min-width:0;font-size:.875rem;font-weight:600;color:#334155}.field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #7FB3B8;border-radius:.75rem;background:#fff;padding:.75rem .85rem;font-weight:400;outline:none}.field:focus{border-color:#2E6F73;box-shadow:0 0 0 3px #2E6F7333}</style>
@endsection
