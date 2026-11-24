@extends('layouts.app')
@section('title', 'Reportes operativos')
@section('content')
@php($totalRegistros = $inspecciones->count() + $aseos->count() + $incidencias->count())
<div class="mx-auto w-full max-w-7xl space-y-5 px-4 py-5 sm:px-6 sm:py-8">
    <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#0C7075]">Supervisión</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Reportes operativos</h1><p class="mt-1 text-sm text-[#294D61]">Inspecciones, aseos e incidencias registrados en el periodo.</p></div><a href="{{ route('dashboard', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#6DA5C0]/50 bg-white px-4 text-sm font-semibold text-[#0C7075]">← Panel</a></div>

    <section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm sm:p-5">
        <form action="{{ route('reportes.index', [], false) }}" method="GET" class="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
            <label class="text-sm font-semibold">Desde<input type="date" name="desde" value="{{ $desde->toDateString() }}" class="report-field"></label>
            <label class="text-sm font-semibold">Hasta<input type="date" name="hasta" value="{{ $hasta->toDateString() }}" class="report-field"></label>
            <label class="text-sm font-semibold lg:col-span-2">Buscar ECO o detalle<input type="search" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="ECO, hallazgo, descripción" class="report-field"></label>
            <label class="text-sm font-semibold">Estado<select name="estado" class="report-field"><option value="">Todos</option>@foreach(['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Resuelto','completado'=>'Completado','reparada'=>'Reparada'] as $key=>$label)<option value="{{ $key }}" @selected(($filtros['estado'] ?? '')===$key)>{{ $label }}</option>@endforeach</select></label>
            <label class="text-sm font-semibold">Urgencia / prioridad<select name="urgencia" class="report-field"><option value="">Todas</option>@foreach(['normal'=>'Normal','baja'=>'Baja','media'=>'Media','alta'=>'Alta','critica'=>'Crítica'] as $key=>$label)<option value="{{ $key }}" @selected(($filtros['urgencia'] ?? '')===$key)>{{ $label }}</option>@endforeach</select></label>
            <div class="flex flex-col gap-2 min-[420px]:flex-row sm:col-span-2 lg:col-span-6"><button class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl bg-[#0F969C] px-4 font-bold text-white">Aplicar filtros</button><a href="{{ route('reportes.index', [], false) }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-[#6DA5C0]/50 px-4 text-center text-sm font-bold text-[#0C7075]">Limpiar</a><a href="{{ route('reportes.export', array_merge(['desde'=>$desde->toDateString(), 'hasta'=>$hasta->toDateString()], $filtros), false) }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-[#6DA5C0]/50 px-4 text-center text-sm font-bold text-[#0C7075]">Descargar Excel</a></div>
        </form>
        @if($errors->any())<p class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>@endif
        <p class="mt-3 text-xs text-[#294D61]">Periodo: {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</p>
    </section>

    <section class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
        @foreach([['Registros del periodo',$totalRegistros],['Inspecciones',$inspecciones->count()],['Aseos',$aseos->count()],['Incidencias',$incidencias->count()]] as [$label,$value])
            <article class="min-w-0 rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs leading-5 text-[#294D61] sm:text-sm">{{ $label }}</p><p class="mt-2 text-2xl font-extrabold text-[#0C7075] sm:text-3xl">{{ $value }}</p></article>
        @endforeach
    </section>

    <section class="grid min-w-0 gap-4 lg:grid-cols-2">
        @foreach([['Inspecciones por hallazgo',$inspeccionesPorTipo,'#0C7075'],['Incidencias por tipo',$incidenciasPorTipo,'#6DA5C0'],['Aseos por proceso',$aseosPorTipo,'#0C7075']] as [$title,$series,$color])
            <article class="min-w-0 rounded-2xl border border-[#6DA5C0]/50 bg-white p-5 shadow-sm"><h2 class="font-extrabold">{{ $title }}</h2>
                @if($series->isEmpty())<p class="mt-4 rounded-xl bg-[#F4F9FB] p-4 text-sm text-[#294D61]">No hay datos para este periodo.</p>@else
                    @php($maxSerie = max(1, (int)$series->max()))
                    <div class="mt-4 space-y-4">@foreach($series as $label=>$total)<div class="min-w-0"><div class="mb-1 flex min-w-0 justify-between gap-3 text-sm"><span class="break-words">{{ $label }}</span><b class="shrink-0">{{ $total }}</b></div><div class="h-3 overflow-hidden rounded-full bg-[#F4F9FB]"><div class="h-full rounded-full" style="width: {{ min(100, max(4, round($total / $maxSerie * 100))) }}%;background:{{ $color }}"></div></div></div>@endforeach</div>
                @endif
            </article>
        @endforeach
        <article class="min-w-0 rounded-2xl border border-[#6DA5C0]/50 bg-white p-5 shadow-sm"><h2 class="font-extrabold">Estado actual de la flota</h2>
            @php($labelsEstado=['operativa'=>'Operativas','activo'=>'Operativas','en_taller'=>'En taller','mantenimiento'=>'En taller','en_limpieza'=>'En limpieza','fuera_servicio'=>'Fuera de servicio','fuera_de_servicio'=>'Fuera de servicio'])
            @if($estadosFlota->isEmpty())<p class="mt-4 text-sm text-[#294D61]">No hay unidades registradas.</p>@else<div class="mt-4 grid grid-cols-2 gap-3">@foreach($estadosFlota as $estado=>$total)<div class="rounded-xl bg-[#F4F9FB] p-3"><p class="text-xs text-[#294D61]">{{ $labelsEstado[$estado] ?? ucfirst(str_replace('_',' ',$estado)) }}</p><p class="mt-1 text-2xl font-extrabold text-[#0C7075]">{{ $total }}</p></div>@endforeach</div>@endif
        </article>
    </section>

    <section class="grid min-w-0 gap-4 lg:grid-cols-3">
        @foreach([['Inspecciones recientes',$inspecciones->take(8),'Inspección'],['Aseos recientes',$aseos->take(8),'Aseo'],['Incidencias recientes',$incidencias->take(8),'Incidencia']] as [$title,$items,$type])
            <article class="min-w-0 rounded-2xl border border-[#6DA5C0]/50 bg-white p-5 shadow-sm"><h2 class="font-extrabold">{{ $title }}</h2><div class="mt-3 space-y-2">
                @forelse($items as $item)
                    @php($date=$type==='Inspección' ? $item->fecha_reporte : ($type==='Aseo' ? $item->fecha_hora : $item->created_at))
                    @php($statusValue=$type==='Inspección' ? ($item->dictamen ?: $item->estado) : $item->estado)
                    @php($statusLabels=['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Atendido','operativa'=>'Operativa','activo'=>'Operativa','en_taller'=>'En taller','mantenimiento'=>'En taller','en_limpieza'=>'En limpieza','fuera_servicio'=>'Fuera de servicio','fuera_de_servicio'=>'Fuera de servicio','completado'=>'Completado'])
                    <div class="min-w-0 rounded-xl bg-[#F4F9FB] p-3"><div class="flex min-w-0 items-start justify-between gap-2"><b class="break-words text-sm">{{ $type==='Inspección' ? $item->tipo_dano : ($type==='Aseo' ? ($item->tipo_limpieza ?: 'Limpieza') : $item->tipo_queja) }}</b><span class="shrink-0 rounded-full bg-white px-2 py-1 text-[11px] font-semibold">{{ $statusLabels[$statusValue] ?? ucfirst(str_replace('_',' ',$statusValue)) }}</span></div><p class="mt-1 text-xs text-[#294D61]">{{ $item->unidad?->etiqueta_economico ?? 'Sin unidad' }} · {{ $date?->format('d/m/Y H:i') }}</p></div>
                @empty<p class="rounded-xl bg-[#F4F9FB] p-3 text-sm text-[#294D61]">Sin registros en el periodo.</p>@endforelse
            </div></article>
        @endforeach
    </section>
</div>
<style>.report-field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #6DA5C0;border-radius:.75rem;background:#fff;padding:.75rem .85rem;font-weight:400;outline:none}.report-field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}</style>
@endsection
