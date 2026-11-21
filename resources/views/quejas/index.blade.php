@extends('layouts.app')
@section('title', 'Quejas e incidencias · RODAGEST')
@section('content')
@php($role = auth()->user()->role)
<div class="mx-auto w-full max-w-7xl space-y-5 px-4 py-5 sm:px-6 sm:py-8">
    <header class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#018FC7]">Seguimiento operativo</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Quejas e incidencias</h1><p class="mt-1 text-sm text-slate-600">{{ $role === 'limpieza' ? 'Atiende reportes públicos de limpieza y deja evidencia para quien los envió.' : 'Consulta los reportes recibidos por QR, sus evidencias y su seguimiento.' }}</p></div><div class="flex flex-wrap gap-2">@if($role === 'admin')<a href="{{ route('quejas.qr-publico', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl bg-[#018FC7] px-4 text-sm font-bold text-white">QR para reportar</a>@endif<a href="{{ route('dashboard', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#B1D0E2] bg-white px-4 text-sm font-semibold text-[#018FC7]">Volver al panel</a></div></header>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="grid min-w-0 gap-3 lg:grid-cols-2">
        @forelse($quejas as $queja)
            <article class="min-w-0 overflow-hidden rounded-2xl border border-[#B1D0E2] bg-white shadow-sm">
                <div class="flex min-w-0 flex-wrap items-start justify-between gap-3 border-b border-[#DBE7F3] bg-[#FBF1E5] p-4 sm:p-5">
                    <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h2 class="break-words text-lg font-extrabold">{{ $queja->unidad?->economico ?? 'Unidad no disponible' }}</h2><span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-[#018FC7]">{{ strtoupper($queja->medio_reporte ?? 'interno') }}</span><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $queja->estado === 'resuelto' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">{{ str_replace('_',' ',$queja->estado) }}</span></div><p class="mt-1 break-words text-sm text-slate-600">{{ $queja->area_reportada ?: 'Equipo rodante' }} · Ruta {{ $queja->ruta_reportada ?: ($queja->unidad?->ruta ?: '—') }}</p></div>
                    <time class="text-xs text-slate-500">{{ $queja->created_at?->format('d/m/Y H:i') }}</time>
                </div>
                <div class="space-y-4 p-4 sm:p-5">
                    <div class="grid min-w-0 gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-[#018FC7]">{{ $queja->tipo_queja }} · {{ $queja->urgencia ?: 'normal' }}</p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm text-slate-700">{{ $queja->descripcion }}</p>
                            @if($queja->nombre_reportante || $queja->telefono_reportante)
                                <p class="mt-2 text-xs text-slate-500">
                                    Reportó: {{ $queja->nombre_reportante ?: 'Anónimo' }}
                                    @if($queja->telefono_reportante) · {{ $queja->telefono_reportante }} @endif
                                </p>
                            @endif
                            @if($queja->operador)
                                <p class="mt-2 text-xs text-slate-500">Operador: {{ $queja->operador->name }}</p>
                            @endif
                        </div>
                        @if($queja->evidencia_foto)<img src="{{ '/storage/' . $queja->evidencia_foto }}" alt="Foto enviada con el reporte" loading="lazy" class="max-h-48 w-full rounded-xl border border-[#DBE7F3] object-contain sm:max-w-48">@endif
                    </div>

                    @if($queja->accion_atencion)<div class="grid min-w-0 gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 sm:grid-cols-[minmax(0,1fr)_auto]"><div class="min-w-0"><p class="text-xs font-bold uppercase text-emerald-800">Acción de limpieza · {{ $queja->updated_at?->format('d/m/Y H:i') }}</p><p class="mt-1 whitespace-pre-line break-words text-sm text-slate-700">{{ $queja->accion_atencion }}</p></div>@if($queja->evidencia_atencion)<img src="{{ '/storage/' . $queja->evidencia_atencion }}" alt="Evidencia de atención" loading="lazy" class="max-h-40 w-full rounded-lg object-contain sm:max-w-40">@endif</div>@endif

                    @if($role === 'limpieza' && $queja->estado !== 'resuelto')
                        <form action="{{ route('quejas.accion-limpieza', [$queja->id], false) }}" method="POST" enctype="multipart/form-data" class="grid min-w-0 gap-3 rounded-xl bg-[#DBE7F3] p-3">@csrf<label class="text-sm font-semibold">Acción realizada<textarea name="accion_atencion" required maxlength="1000" placeholder="Ej. Se lavó y desinfectó el área reportada." class="field mt-1"></textarea></label><label class="text-sm font-semibold">Foto del trabajo terminado<input name="evidencia_atencion" type="file" accept="image/*" capture="environment" required class="field mt-1"></label><button class="min-h-11 rounded-xl bg-[#018FC7] px-4 font-bold text-white">Guardar acción y notificar seguimiento</button></form>
                    @endif

                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-[#DBE7F3] pt-3">
                        @if($queja->codigo_seguimiento)<a href="{{ route('quejas.seguimiento', [$queja->codigo_seguimiento], false) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center font-semibold text-[#018FC7]">Abrir seguimiento externo →</a>@else<span></span>@endif
                        @if(in_array($role, ['admin','inspector']))<form action="{{ route('quejas.actualizar-estado', [$queja->id], false) }}" method="POST" class="flex max-w-full items-center gap-2">@csrf @method('PUT')<select name="estado" class="min-w-0 rounded-lg border border-[#B1D0E2] bg-white px-2 py-2 text-sm"><option value="pendiente" @selected($queja->estado==='pendiente')>Pendiente</option><option value="en_proceso" @selected($queja->estado==='en_proceso')>En proceso</option><option value="resuelto" @selected($queja->estado==='resuelto')>Resuelto</option></select><button class="min-h-10 rounded-lg px-2 text-sm font-bold text-[#018FC7]">Actualizar</button></form>@endif
                        @if($role === 'admin' || ($role === 'operador' && $queja->operador_id === auth()->id()))<form action="{{ route('quejas.destroy', [$queja->id], false) }}" method="POST">@csrf @method('DELETE')<button class="min-h-10 px-2 text-sm font-semibold text-red-600" onclick="return confirm('¿Eliminar reporte?')">Eliminar</button></form>@endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#B1D0E2] bg-white p-8 text-center text-slate-500 lg:col-span-2">{{ $role === 'limpieza' ? 'No hay quejas de limpieza pendientes.' : 'No hay reportes para mostrar.' }}</div>
        @endforelse
    </section>
    <div class="rounded-xl bg-white p-3">{{ $quejas->links() }}</div>
</div>
<style>.field{display:block;width:100%;min-width:0;border:1px solid #B1D0E2;border-radius:.7rem;background:white;padding:.75rem .85rem;outline:none}.field:focus{border-color:#018FC7;box-shadow:0 0 0 3px #018FC733}</style>
@endsection
