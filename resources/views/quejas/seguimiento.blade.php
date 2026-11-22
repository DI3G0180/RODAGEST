@extends('layouts.app')
@section('title', 'Seguimiento de reporte · RODAGEST')
@section('content')
@php($estadoLabel=['pendiente'=>'Recibido','en_proceso'=>'En atención','resuelto'=>'Atendido'][$queja->estado] ?? ucfirst($queja->estado))
<div class="mx-auto w-full max-w-2xl space-y-4 px-4 py-6 sm:px-6 sm:py-10">
    <section class="overflow-hidden rounded-3xl border border-[#B1D0E2] bg-white shadow-sm">
        <div class="bg-[#018FC7] p-5 text-white sm:p-7"><p class="text-xs font-bold uppercase tracking-[.16em] text-white/80">Seguimiento RODAGEST</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $estadoLabel }}</h1><p class="mt-2 text-sm text-white/90">Folio privado: <span class="font-mono font-bold">{{ $queja->codigo_seguimiento }}</span></p></div>
        <div class="space-y-5 p-5 sm:p-7">
            <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2"><div class="rounded-xl bg-[#DBE7F3] p-3"><p class="text-xs text-slate-600">Unidad / ECO</p><p class="mt-1 break-words font-bold">{{ $queja->eco ?: ($queja->unidad?->economico ?? '—') }}</p></div><div class="rounded-xl bg-[#DBE7F3] p-3"><p class="text-xs text-slate-600">Ruta</p><p class="mt-1 break-words font-bold">{{ $queja->ruta_reportada ?: '—' }}</p></div><div class="rounded-xl bg-[#DBE7F3] p-3"><p class="text-xs text-slate-600">Área</p><p class="mt-1 break-words font-bold">{{ $queja->area ?: ($queja->area_reportada ?: '—') }}</p></div><div class="rounded-xl bg-[#DBE7F3] p-3"><p class="text-xs text-slate-600">Fecha del reporte</p><p class="mt-1 font-bold">{{ $queja->created_at?->format('d/m/Y H:i') }}</p></div></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-[#018FC7]">{{ $queja->tipo_queja }}</p><p class="mt-1 whitespace-pre-line break-words text-slate-700">{{ $queja->descripcion }}</p></div>
            @if($queja->evidencia_foto)<div><p class="text-sm font-bold">Foto del reporte</p><img src="{{ route('quejas.seguimiento.evidencia', [$queja->codigo_seguimiento, 'reporte'], false) }}" alt="Foto enviada con la queja" class="mt-2 max-h-72 max-w-full rounded-xl border border-[#DBE7F3] object-contain"></div>@endif
            @if($queja->accion_atencion)<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-5"><p class="text-xs font-bold uppercase tracking-wide text-emerald-800">Respuesta del equipo de limpieza</p><p class="mt-2 whitespace-pre-line break-words text-slate-700">{{ $queja->accion_atencion }}</p>@if($queja->evidencia_atencion)<img src="{{ route('quejas.seguimiento.evidencia', [$queja->codigo_seguimiento, 'atencion'], false) }}" alt="Foto del trabajo realizado" class="mt-3 max-h-72 max-w-full rounded-xl border border-emerald-200 object-contain">@endif</div>@elseif($queja->tipo_queja==='Limpieza')<p class="rounded-xl bg-[#FBF1E5] p-4 text-sm text-slate-600">El equipo de limpieza todavía no registra una acción. Vuelve a consultar este enlace más tarde.</p>@endif
            <p class="text-xs leading-5 text-slate-500">Guarda este enlace para consultar cambios. El folio es privado; compártelo solo con quien deba revisar el reporte.</p>
            @if(!auth()->check() || auth()->user()->role !== 'admin')<a href="{{ route('quejas.publica', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#B1D0E2] px-4 font-semibold text-[#018FC7]">Enviar otro reporte</a>@endif
        </div>
    </section>
</div>
@endsection
