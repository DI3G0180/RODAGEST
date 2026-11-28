@extends('layouts.app')
@section('title', 'Inspección diaria')
@section('content')
@php($role = auth()->user()->role)
<div class="mx-auto w-full max-w-7xl space-y-4 px-3 py-4 sm:space-y-5 sm:px-6 sm:py-8">
    <header class="flex flex-wrap items-center justify-between gap-2">
        <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-[.16em] text-[#0C7075]">{{ $role === 'admin' ? 'Supervisión · Administración' : 'Operación · Inspector' }}</p><h1 class="mt-1 text-xl font-bold tracking-tight text-[#083344] sm:text-2xl">Inspección diaria</h1><p class="text-xs text-slate-500 sm:text-sm">Registra daños, fallas, ubicación y evidencia fotográfica o en video.</p></div>
        <a href="{{ route('dashboard', [], false) }}" class="inline-flex min-h-10 shrink-0 items-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-[#0E7490]">Volver</a>
    </header>
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form action="{{ route('inspecciones.index', [], false) }}" method="GET" class="grid min-w-0 grid-cols-2 gap-2 rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm sm:p-4 lg:grid-cols-5"><label class="sr-only" for="inspection-filter-unit">Unidad</label><select id="inspection-filter-unit" name="unidad_id" class="inspection-field mt-0 h-10 text-sm"><option value="">Todas las unidades</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" @selected((string) request('unidad_id')===(string) $unidad->id)>{{ $unidad->economico }}</option>@endforeach</select><label class="sr-only" for="inspection-filter-from">Desde</label><input id="inspection-filter-from" name="desde" type="date" value="{{ request('desde') }}" aria-label="Desde" class="inspection-field mt-0 h-10 text-sm"><label class="sr-only" for="inspection-filter-to">Hasta</label><input id="inspection-filter-to" name="hasta" type="date" value="{{ request('hasta') }}" aria-label="Hasta" class="inspection-field mt-0 h-10 text-sm"><label class="sr-only" for="inspection-filter-type">Tipo de falla</label><select id="inspection-filter-type" name="tipo_dano" class="inspection-field mt-0 h-10 text-sm"><option value="">Todos los tipos</option>@foreach(['Mecánica','Carrocería','Eléctrica','Neumáticos','Vandalismo','Limpieza','Otro'] as $tipo)<option value="{{ $tipo }}" @selected(request('tipo_dano')===$tipo)>{{ $tipo }}</option>@endforeach</select><button class="col-span-2 h-10 rounded-xl bg-[#0E7490] px-3 text-sm font-bold text-white lg:col-span-1">Filtrar historial</button></form>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        @foreach(['hoy'=>'Inspecciones de hoy','pendientes'=>'Pendientes','criticas'=>'Fallas críticas abiertas'] as $key=>$label)<article class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm"><p class="text-sm text-[#294D61]">{{ $label }}</p><p class="mt-1 text-3xl font-extrabold text-[#0C7075]">{{ $estadisticas[$key] }}</p></article>@endforeach
    </section>

    @if($role === 'inspector')
    <section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-5 shadow-sm sm:p-6"><details @if(request()->boolean('crear') || $errors->any()) open @endif class="group">
        <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 text-lg font-bold marker:hidden"><span>Inspecciones</span><span class="rounded-lg bg-[#0F969C] px-4 py-2 text-sm font-bold text-white group-open:hidden">+ Nueva inspección</span><span class="hidden rounded-lg border border-[#6DA5C0]/50 px-4 py-2 text-sm font-bold text-[#0C7075] group-open:inline-flex">Cerrar formulario</span></summary>
        <form action="{{ route('inspecciones.store', [], false) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-3">@csrf
            <label class="form-label">Económico / ECO<select name="unidad_id" id="inspection-unit" required class="inspection-field"><option value="">Selecciona unidad</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" data-operator="{{ $unidad->operador_id }}">{{ $unidad->economico }}</option>@endforeach</select></label>
            <label class="form-label">Operador<select name="operador_id" id="inspection-operator" class="inspection-field"><option value="">Sin operador asignado</option>@foreach($operadores as $operador)<option value="{{ $operador->id }}">{{ $operador->name }}</option>@endforeach</select></label>
            <label class="form-label">Fecha y hora<input type="datetime-local" name="fecha_reporte" required value="{{ now()->format('Y-m-d\TH:i') }}" class="inspection-field"></label>
            <label class="form-label">Persona que reporta<input name="persona_reporta" maxlength="150" placeholder="Nombre de quien detectó la falla" class="inspection-field"></label>
            <label class="form-label">Daño o falla<select name="tipo_dano" required class="inspection-field"><option value="">Selecciona</option>@foreach(['Mecánica','Carrocería','Eléctrica','Neumáticos','Vandalismo','Limpieza','Otro'] as $tipo)<option>{{ $tipo }}</option>@endforeach</select></label>
            <label class="form-label">Categoría del evento<select name="categoria_evento" class="inspection-field"><option value="">Selecciona</option>@foreach(['Falla mecánica','Accidente','Vandalismo','Daño a usuario','Incidencia operativa','Otro'] as $categoria)<option>{{ $categoria }}</option>@endforeach</select></label>
            <label class="form-label">Ubicación del daño<input name="ubicacion_dano" maxlength="180" placeholder="Ej. puerta trasera, eje delantero" class="inspection-field"></label>
            <label class="form-label">Municipio<input name="municipio" maxlength="120" class="inspection-field" placeholder="Municipio"></label>
            <label class="form-label">Dirección / lugar<input name="direccion" maxlength="255" class="inspection-field" placeholder="Calle, terminal o referencia"></label>
            <label class="form-label">Prioridad<select name="prioridad" required class="inspection-field"><option value="baja">Baja</option><option value="media" selected>Media</option><option value="alta">Alta</option><option value="critica">Crítica</option></select></label>
            <label class="form-label">Dictamen operativo<select name="dictamen" required class="inspection-field"><option value="operativa">Puede operar</option><option value="en_taller">Enviar a taller</option><option value="fuera_servicio">Fuera de servicio</option></select></label>
            <fieldset class="rounded-xl border border-[#F4F9FB] p-3 sm:col-span-2 lg:col-span-3"><legend class="px-1 text-sm font-bold">Inspección del vehículo</legend><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@foreach(['frenos'=>'Frenos','neumaticos'=>'Neumáticos','luces'=>'Luces','niveles'=>'Niveles','puertas'=>'Puertas articuladas','documentacion'=>'Documentación'] as $key=>$label)<label class="form-label">{{ $label }}<select name="checklist[{{ $key }}]" required class="inspection-field mt-1"><option value="1">Correcto</option><option value="0">Requiere atención</option></select></label>@endforeach</div></fieldset>
            <label class="form-label sm:col-span-2 lg:col-span-3">Descripción<textarea name="descripcion" required maxlength="500" rows="4" placeholder="Describe con claridad qué ocurrió y qué necesita atención." class="inspection-field"></textarea></label>
            <label class="form-label sm:col-span-2">Fotos<input name="fotos[]" type="file" accept="image/*" capture="environment" multiple class="inspection-field"><span class="mt-1 block text-xs font-normal text-[#294D61]">Hasta 8 imágenes de 10 MB cada una.</span></label>
            <label class="form-label sm:col-span-2 lg:col-span-1">Videos<input name="videos[]" type="file" accept="video/mp4,video/quicktime,video/webm" capture="environment" multiple class="inspection-field"><span class="mt-1 block text-xs font-normal text-[#294D61]">MP4, MOV o WEBM; máximo 2 videos de 20 MB.</span></label>
            <button class="min-h-12 rounded-xl bg-[#0F969C] px-5 font-bold text-white hover:bg-[#072E33] sm:col-span-2 lg:col-span-3">Guardar inspección</button>
        </form>
    </details></section>
    @endif

    <section class="space-y-4">
        <div><h2 class="text-lg font-bold text-[#083344]">Historial de inspecciones</h2><p class="text-xs text-slate-500 sm:text-sm">Inspecciones registradas en el periodo seleccionado.</p></div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($inspecciones as $inspeccion)
            @php($nextState = \App\Support\ProgressiveStatus::next('inspeccion', $inspeccion->estado))
            <article class="min-w-0 rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-3"><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="text-lg font-extrabold">{{ $inspeccion->unidad?->etiqueta_economico ?? 'Unidad no disponible' }}</h3><span class="rounded-full bg-[#F4F9FB] px-3 py-1 text-xs font-bold text-[#0C7075]">{{ $inspeccion->tipo_dano }}</span><span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">{{ ['pendiente'=>'Pendiente','en_proceso'=>'En proceso','resuelto'=>'Resuelto'][$inspeccion->estado] ?? ucfirst(str_replace('_',' ', $inspeccion->estado)) }}</span></div><p class="mt-1 break-words text-sm text-[#294D61]">{{ $inspeccion->fecha_reporte?->format('d/m/Y H:i') }} · Inspector {{ $inspeccion->inspector_responsable }} · Prioridad {{ ucfirst($inspeccion->prioridad) }}</p><p class="mt-1 break-words text-sm text-[#294D61]">{{ $inspeccion->categoria_evento ?: 'Inspección diaria' }} · {{ $inspeccion->municipio ?: 'Ubicación no indicada' }} · {{ $inspeccion->direccion ?: '—' }}</p></div>
                    @if($role === 'inspector')@if($nextState)<form action="{{ route('inspecciones.actualizar-estado', [$inspeccion->id], false) }}" method="POST" class="flex max-w-full flex-wrap items-center gap-2">@csrf<input type="hidden" name="estado" value="{{ $nextState }}"><span class="text-xs font-medium text-[#294D61]">Siguiente: {{ ['en_proceso'=>'En proceso','resuelto'=>'Resuelta'][$nextState] }}</span><button class="min-h-10 rounded-lg bg-[#0E7490] px-3 text-sm font-bold text-white hover:bg-[#083344]">Avanzar</button></form>@else<span class="rounded-full bg-emerald-100 px-3 py-2 text-xs font-bold text-emerald-800">Estado final · solo lectura</span>@endif @endif
                </div>
                @php($legacyEvidenceOnly = $inspeccion->evidencia_foto && $inspeccion->evidencias->isEmpty())
                <details class="mt-3 rounded-xl bg-[#F4F9FB] p-3"><summary class="cursor-pointer text-sm font-bold text-[#0C7075]">Ver reporte y evidencias ({{ $inspeccion->evidencias->count() + (int) $legacyEvidenceOnly }})</summary><div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3"><p class="whitespace-pre-line break-words text-sm sm:col-span-2 lg:col-span-3">{{ $inspeccion->descripcion }}</p><p class="text-sm"><b>Reportó:</b> {{ $inspeccion->persona_reporta ?: '—' }} · <b>Operador:</b> {{ $inspeccion->operador?->name ?? '—' }}</p><p class="text-sm"><b>Ubicación del daño:</b> {{ $inspeccion->ubicacion_dano ?: '—' }} · <b>Dictamen:</b> {{ str_replace('_',' ', $inspeccion->dictamen) }}</p><div class="grid gap-3 sm:col-span-2 lg:col-span-3 sm:grid-cols-2 lg:grid-cols-3">@if($legacyEvidenceOnly)<img src="{{ '/storage/' . $inspeccion->evidencia_foto }}" alt="Evidencia de inspección" loading="lazy" class="max-h-56 w-full rounded-lg border border-[#6DA5C0]/50 bg-white object-contain">@endif @foreach($inspeccion->evidencias as $evidencia)<div class="min-w-0 rounded-lg border border-[#6DA5C0]/50 bg-white p-2">@if($evidencia->tipo_media==='video')<video controls preload="metadata" class="w-full rounded-lg"><source src="{{ '/storage/' . $evidencia->ruta_archivo }}"></video>@else<img src="{{ '/storage/' . $evidencia->ruta_archivo }}" alt="Evidencia de inspección" loading="lazy" class="max-h-56 w-full rounded-lg object-contain">@endif<p class="mt-1 truncate text-xs text-[#294D61]">{{ $evidencia->nombre_original }}</p></div>@endforeach</div></div></details>
                @if($role === 'inspector')<div class="mt-3 flex justify-end"><form action="{{ route('inspecciones.destroy', [$inspeccion->id], false) }}" method="POST">@csrf @method('DELETE')<button class="min-h-10 px-3 text-sm font-semibold text-red-600" onclick="return confirm('¿Eliminar la inspección y sus evidencias?')">Eliminar</button></form></div>@endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#6DA5C0]/50 bg-white p-8 text-center text-[#294D61]">Aún no hay inspecciones.</div>
        @endforelse
        </div>
        <div class="rounded-xl bg-white p-3">{{ $inspecciones->links() }}</div>
    </section>
</div>
<style>.form-label{display:block;min-width:0;font-size:.875rem;font-weight:650;color:#294D61}.inspection-field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #6DA5C0;border-radius:.8rem;background:#fff;padding:.75rem .85rem;font-size:1rem;font-weight:400;outline:none}.inspection-field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}</style>
<script>
    document.getElementById('inspection-unit')?.addEventListener('change', event => {
        const operator = document.getElementById('inspection-operator');
        const assigned = event.target.selectedOptions[0]?.dataset.operator || '';
        if (assigned) operator.value = assigned;
    });
</script>
@endsection
