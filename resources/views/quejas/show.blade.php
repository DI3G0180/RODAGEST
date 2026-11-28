@extends('layouts.app')
@section('title', 'Detalle de queja')
@section('content')
<div class="mx-auto w-full max-w-4xl space-y-5 px-4 py-5 sm:px-6 sm:py-8">
    @php($role = auth()->user()->role)
    @php($nextState = \App\Support\ProgressiveStatus::next('queja', $queja->estado))
    <header class="flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#0C7075]">{{ $role === 'admin' ? 'Consulta y seguimiento' : 'Seguimiento operativo' }}</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Queja · {{ $queja->eco ?: ($queja->unidad?->economico ?? 'Unidad no disponible') }}</h1><p class="mt-1 text-sm text-[#294D61]">Detalle del reporte y formulario utilizado para registrarlo.</p></div>
        <a href="{{ route('quejas.index', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#6DA5C0]/50 bg-white px-4 font-semibold text-[#0C7075] hover:bg-[#F4F9FB]">← Bandeja de quejas</a>
    </header>

    <section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#F4F9FB] pb-4"><div><p class="text-xs font-bold uppercase tracking-wide text-[#0C7075]">Formulario de reporte</p><h2 class="mt-1 text-xl font-extrabold">{{ $queja->tipo_queja }}</h2></div><span class="rounded-full px-3 py-1 text-sm font-bold {{ $queja->estado === 'resuelto' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">{{ ['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Atendida'][$queja->estado] ?? ucfirst(str_replace('_',' ',$queja->estado)) }}</span></div>
        <dl class="mt-4 grid min-w-0 gap-4 sm:grid-cols-2">
            @foreach([['Económico / ECO', $queja->eco ?: ($queja->unidad?->economico ?? '—')], ['Área', $queja->area ?: ($queja->area_reportada ?: '—')], ['Ruta', $queja->ruta_reportada ?: '—'], ['Urgencia', ucfirst($queja->urgencia ?: 'normal')], ['Medio', ucfirst($queja->medio_reporte ?: 'interno')], ['Fecha y hora', $queja->created_at?->format('d/m/Y H:i') ?: '—'], ['Nombre reportante', $queja->nombre_reportante ?: 'No indicado'], ['Operador', $queja->operador?->name ?: 'No indicado']] as [$label, $value])
                <div class="min-w-0 rounded-xl bg-[#F4F9FB] p-3"><dt class="text-xs font-bold uppercase tracking-wide text-[#294D61]">{{ $label }}</dt><dd class="mt-1 break-words font-semibold text-[#05161A]">{{ $value }}</dd></div>
            @endforeach
            <div class="rounded-xl bg-[#F4F9FB] p-3 sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-[#294D61]">Descripción de la queja</dt><dd class="mt-1 whitespace-pre-line break-words text-[#05161A]">{{ $queja->descripcion }}</dd></div>
        </dl>

        @if($queja->evidencia_foto)<div class="mt-4"><p class="text-sm font-bold">Evidencia adjunta</p><a href="{{ '/storage/'.$queja->evidencia_foto }}" target="_blank" rel="noopener"><img src="{{ '/storage/'.$queja->evidencia_foto }}" alt="Evidencia de la queja" loading="lazy" class="mt-2 max-h-96 max-w-full rounded-xl border border-[#6DA5C0]/40 object-contain"></a></div>@endif
    </section>

    @if($queja->accion_atencion)
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-6"><h2 class="font-extrabold text-emerald-900">Respuesta / seguimiento de limpieza</h2><p class="mt-2 whitespace-pre-line break-words text-sm text-emerald-950">{{ $queja->accion_atencion }}</p>@if($queja->evidencia_atencion)<img src="{{ '/storage/'.$queja->evidencia_atencion }}" alt="Evidencia de atención" loading="lazy" class="mt-3 max-h-80 max-w-full rounded-xl border border-emerald-200 object-contain">@endif</section>
    @endif

    @if(in_array($role, ['admin', 'inspector']))
        <section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 sm:p-6"><h2 class="font-extrabold">Actualizar seguimiento</h2>@if($nextState)<form action="{{ route('quejas.actualizar-estado', [$queja->id], false) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-3">@csrf @method('PUT')<input type="hidden" name="estado" value="{{ $nextState }}"><p class="text-sm text-[#294D61]">Siguiente estado: <b>{{ ['en_proceso'=>'En proceso','resuelto'=>'Resuelta'][$nextState] }}</b></p><button class="min-h-11 rounded-xl bg-[#0E7490] px-5 font-bold text-white hover:bg-[#083344]">Avanzar seguimiento</button></form>@else<p class="mt-2 inline-flex rounded-full bg-emerald-100 px-3 py-2 text-sm font-bold text-emerald-800">Estado final · {{ ucfirst(str_replace('_',' ',$queja->estado)) }}. Registro de solo lectura.</p>@endif</section>
    @endif
</div>
@endsection
