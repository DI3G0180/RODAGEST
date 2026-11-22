@extends('layouts.app')
@section('title', 'Reportar queja · RODAGEST')
@section('content')
<div class="mx-auto w-full max-w-2xl px-4 py-6 sm:px-6 sm:py-10">
    <section class="overflow-hidden rounded-3xl border border-[#B1D0E2] bg-white shadow-sm">
        <div class="bg-[#018FC7] px-5 py-5 text-white sm:px-7 sm:py-7"><p class="text-xs font-bold uppercase tracking-[.16em] text-white/80">RODAGEST · Atención ciudadana</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Reportar una queja</h1><p class="mt-2 text-sm text-white/90">Indica el ECO, la ruta y qué ocurrió. Puedes adjuntar una foto.</p></div>
        <div class="p-5 sm:p-7">
            @if($errors->any())<div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('quejas.publica.store', [], false) }}" method="POST" enctype="multipart/form-data" class="grid min-w-0 gap-4 sm:grid-cols-2">@csrf
                <label class="form-label sm:col-span-2">Unidad / ECO<select id="unidad-publica" name="unidad_id" required class="public-field"><option value="">Selecciona el número económico</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" data-route="{{ $unidad->ruta }}" @selected((string)old('unidad_id', $unidadSeleccionada)===(string)$unidad->id)>{{ $unidad->economico }}</option>@endforeach</select></label>
                <label class="form-label">Ruta<input id="ruta-reportada" name="ruta_reportada" required maxlength="120" value="{{ old('ruta_reportada') }}" placeholder="Ej. Pantitlán–Chimalhuacán" class="public-field"></label>
                <label class="form-label">Área<select name="area_reportada" required class="public-field"><option value="">Selecciona el área</option>@foreach(['Equipo rodante','Terminal o patio','Servicio en ruta','Otro'] as $area)<option @selected(old('area_reportada')===$area)>{{ $area }}</option>@endforeach</select></label>
                <label class="form-label sm:col-span-2">Tipo de queja<select name="tipo_queja" required class="public-field"><option value="">¿Qué necesitas reportar?</option>@foreach(['Limpieza','Falla mecánica','Incidencia en ruta','Daño o seguridad','Otro'] as $tipo)<option @selected(old('tipo_queja')===$tipo)>{{ $tipo }}</option>@endforeach</select></label>
                <label class="form-label sm:col-span-2">Descripción<textarea name="descripcion" required maxlength="1000" rows="4" placeholder="Describe brevemente lo ocurrido y dónde se encuentra la unidad." class="public-field">{{ old('descripcion') }}</textarea></label>
                <label class="form-label sm:col-span-2">Foto (opcional)<input name="evidencia_foto" type="file" accept="image/*" capture="environment" class="public-field file-field"><span class="mt-1 block text-xs font-normal text-slate-500">JPG, PNG o imagen del teléfono; máximo 5 MB.</span></label>
                <details class="sm:col-span-2"><summary class="min-h-10 cursor-pointer py-2 text-sm font-semibold text-[#018FC7]">Agregar nombre y WhatsApp para recibir seguimiento (opcional)</summary><div class="mt-2 grid min-w-0 gap-3 sm:grid-cols-2"><label class="form-label">Nombre<input name="nombre_reportante" maxlength="120" value="{{ old('nombre_reportante') }}" class="public-field"></label><label class="form-label">WhatsApp<input name="telefono_reportante" type="tel" inputmode="tel" maxlength="30" value="{{ old('telefono_reportante') }}" placeholder="+52 55 1234 5678" class="public-field"></label></div></details>
                <label class="honeypot" aria-hidden="true">Dejar vacío<input name="sitio_web" tabindex="-1" autocomplete="off"></label>
                <p class="text-xs leading-5 text-slate-500 sm:col-span-2">Tu reporte se enviará al equipo de RODAGEST. El teléfono es opcional; al enviar recibirás un folio privado para consultar el avance.</p>
                <button class="min-h-12 rounded-xl bg-[#018FC7] px-5 font-bold text-white shadow-sm transition hover:bg-sky-700 sm:col-span-2">Enviar reporte</button>
            </form>
        </div>
    </section>
</div>
<style>.form-label{display:block;min-width:0;font-size:.875rem;font-weight:650;color:#334155}.public-field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #B1D0E2;border-radius:.8rem;background:#fff;padding:.8rem .9rem;font-size:1rem;font-weight:400;outline:none}.public-field:focus{border-color:#018FC7;box-shadow:0 0 0 3px #018FC733}.file-field{font-size:.85rem}.honeypot{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}</style>
<script>
    const unitSelector = document.getElementById('unidad-publica');
    const fillRoute = event => {
        const routeField = document.getElementById('ruta-reportada');
        const selectedRoute = event.target.selectedOptions[0]?.dataset.route || '';
        if (selectedRoute && !routeField.value) routeField.value = selectedRoute;
    };
    unitSelector?.addEventListener('change', fillRoute);
    if (unitSelector?.value) fillRoute({target: unitSelector});
</script>
@endsection
