<section class="grid grid-cols-2 gap-2.5 sm:gap-4 md:grid-cols-3">
    <a href="#aseo-rapido" class="metric"><div><span>Aseos completados hoy</span><b>{{ $kpis['aseosHoy'] }}</b></div><div class="metric-footer"><span>Servicios completados</span><span class="font-bold text-[#0C7075]">Ver →</span></div></a>
    <a href="{{ route('aseos.index', [], false) }}" class="metric"><div><span>Pendientes o en proceso</span><b>{{ $kpis['aseosPendientes'] }}</b></div><div class="metric-footer"><span>Unidades por atender</span><span class="font-bold text-[#0C7075]">Ver →</span></div></a>
    <a href="{{ route('quejas.index', [], false) }}" class="metric"><div><span>Reportes por atender</span><b>{{ $kpis['reportesLimpiezaPendientes'] }}</b></div><div class="metric-footer"><span>Incidencias de limpieza</span><span class="font-bold text-[#0C7075]">Ver →</span></div></a>
</section>

<section id="aseo-rapido" class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-3 sm:p-6">
    <div><h2 class="text-lg font-extrabold">Aseo rápido de hoy</h2><p class="mt-1 text-sm text-[#294D61]">Completa las cuatro etapas en un envío. La foto solo se pide si reportas una incidencia.</p></div>
    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($unidadesLimpieza as $unidad)
            @php($aseoListoHoy = $aseoCompletadoHoyUnidadIds->contains($unidad->id))
            <article class="rounded-xl border {{ $aseoListoHoy ? 'border-emerald-200 bg-emerald-50' : 'border-[#6DA5C0]/40 bg-[#F4F9FB]' }} p-4">
                <div class="flex items-center justify-between gap-2"><h3 class="text-lg font-extrabold">{{ $unidad->economico }}</h3><span class="rounded-full px-3 py-1 text-xs font-bold {{ $aseoListoHoy ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">{{ $aseoListoHoy ? 'Completado' : 'Pendiente' }}</span></div>
                @if($aseoListoHoy)
                    <p class="mt-3 text-sm font-medium text-emerald-900">Aseo 4/4 registrado hoy.</p>
                @else
                    <form action="{{ route('aseos.completar-rapido', [], false) }}" method="POST" class="mt-3">@csrf<input type="hidden" name="unidad_id" value="{{ $unidad->id }}"><button class="min-h-12 w-full rounded-xl bg-[#0F969C] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#0C7075]">Completar aseo 4/4 hoy</button></form>
                    <details class="mt-2 group">
                        <summary class="min-h-11 cursor-pointer py-2 text-center text-sm font-semibold text-[#0C7075]">Reportar incidencia en limpieza</summary>
                        <form action="{{ route('aseos.completar-rapido', [], false) }}" method="POST" enctype="multipart/form-data" class="mt-2 space-y-2 rounded-xl border border-amber-200 bg-white p-3">@csrf
                            <input type="hidden" name="unidad_id" value="{{ $unidad->id }}"><input type="hidden" name="incidencia" value="1">
                            <label class="block text-xs font-semibold text-[#294D61]">Describe la incidencia<textarea name="observaciones" required maxlength="1000" rows="2" class="field mt-1" placeholder="Ej. falta de insumos, daño o suciedad persistente"></textarea></label>
                            <label class="block text-xs font-semibold text-[#294D61]">Foto requerida<input type="file" name="evidencia_foto" accept="image/jpeg,image/png,image/webp" capture="environment" required class="field mt-1"></label>
                            <button class="min-h-11 w-full rounded-lg bg-amber-700 px-3 font-bold text-white">Guardar incidencia</button>
                        </form>
                    </details>
                @endif
            </article>
        @empty
            <p class="rounded-xl bg-[#F4F9FB] p-4 text-sm text-[#294D61] sm:col-span-2 lg:col-span-3">No hay unidades operativas asignadas a la flota.</p>
        @endforelse
    </div>
</section>

<section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-5 sm:p-6">
    <details id="form-limpieza" class="group"><summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 font-bold"><span>Registro detallado (opcional)</span><span class="text-sm font-semibold text-[#0C7075] group-open:hidden">Mostrar</span><span class="hidden text-sm font-semibold text-[#0C7075] group-open:inline">Ocultar</span></summary>
        <form action="{{ route('aseos.store', [], false) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2">@csrf
            <label class="block text-sm font-semibold">Unidad<select name="unidad_id" required class="field mt-1"><option value="">Selecciona unidad</option>@foreach($unidadesLimpieza as $unidad)<option value="{{ $unidad->id }}">{{ $unidad->economico }}</option>@endforeach</select></label>
            <label class="block text-sm font-semibold">Tipo<select name="tipo_limpieza" required class="field mt-1"><option>Lavado y desinfección</option><option>Lavado</option><option>Desinfección</option></select></label>
            <label class="block text-sm font-semibold">Intendente<input name="intendente_responsable" value="{{ $user->name }}" required class="field mt-1"></label>
            <label class="block text-sm font-semibold">Producto<input name="producto_desinfectante" value="Desinfectante habitual" required class="field mt-1"></label>
            <label class="block text-sm font-semibold sm:col-span-2">Evidencia (opcional)<input name="evidencia_foto" type="file" accept="image/*" capture="environment" class="field mt-1"></label>
            <fieldset class="grid grid-cols-2 gap-2 sm:col-span-2"><legend class="mb-2 font-semibold">Etapas</legend>@foreach(['barrido'=>'Barrido','pasamanos_asientos'=>'Pasamanos y asientos','cabina_exterior'=>'Cabina y exterior','nebulizacion'=>'Nebulización'] as $key=>$label)<label class="flex min-h-12 items-center gap-2 rounded-lg bg-[#F4F9FB] p-3 text-sm"><input type="checkbox" name="etapas[{{ $key }}]" value="1">{{ $label }}</label>@endforeach</fieldset>
            <input type="hidden" name="fecha_hora" value="{{ now()->format('Y-m-d H:i:s') }}"><input type="hidden" name="area_terminal" value="Patio"><button name="lista_ruta" value="1" class="min-h-12 rounded-lg bg-[#0F969C] px-4 py-3 font-bold text-white sm:col-span-2">Guardar proceso detallado</button>
        </form>
    </details>
</section>

<details class="group rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 sm:p-5"><summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-3 font-bold"><span>Aseos recientes</span><span class="text-sm font-semibold text-[#0C7075] group-open:rotate-180">⌄</span></summary><div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">@forelse($aseos as $aseo)<article class="min-w-0 rounded-xl border border-slate-200 p-3"><div class="flex items-start justify-between gap-2"><b class="text-lg text-[#0C7075]">{{ $aseo->unidad?->etiqueta_economico ?? '—' }}</b><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $aseo->estado==='completado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">{{ ucfirst(str_replace('_',' ',$aseo->estado)) }}</span></div><time class="mt-2 block text-xs text-[#294D61]">{{ $aseo->fecha_hora?->format('d/m/Y H:i') }}</time><p class="mt-2 text-sm">Etapas: <b>{{ collect($aseo->etapas ?? [])->filter()->count() }}/4</b></p></article>@empty<p class="rounded-xl bg-[#F4F9FB] p-4 text-sm text-[#294D61] md:col-span-2 lg:col-span-3">No hay aseos recientes.</p>@endforelse</div></details>
