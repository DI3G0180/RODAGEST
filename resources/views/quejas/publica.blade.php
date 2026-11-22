@extends('layouts.app')
@section('title', 'Reportar queja')
@section('content')
<div class="mx-auto w-full max-w-2xl px-4 py-6 sm:px-6 sm:py-10">
    <section class="overflow-hidden rounded-3xl border border-[#B1D0E2] bg-white shadow-sm">
        <div class="bg-[#018FC7] px-5 py-5 text-white sm:px-7 sm:py-7"><p class="text-xs font-bold uppercase tracking-[.16em] text-white/80">RODAGEST · Atención ciudadana</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Reportar una queja</h1><p class="mt-2 text-sm text-white/90">Indica el ECO, la ruta y qué ocurrió. Puedes adjuntar una foto.</p></div>
        <div class="p-5 sm:p-7">
            @if($errors->any())<div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('quejas.publica.store', [], false) }}" method="POST" enctype="multipart/form-data" class="grid min-w-0 gap-4 sm:grid-cols-2">@csrf
                <label class="form-label">Número económico (ECO)<input name="eco" required maxlength="30" value="{{ old('eco', $eco) }}" placeholder="Ej. ECO-104" class="public-field"></label>
                <label class="form-label">Ruta<input id="ruta-reportada" name="ruta_reportada" required maxlength="120" value="{{ old('ruta_reportada') }}" placeholder="Ej. Pantitlán–Chimalhuacán" class="public-field"></label>
                <label class="form-label sm:col-span-2">Área<input name="area" required maxlength="80" value="{{ old('area') }}" placeholder="Ej. carrocería, salón, terminal, ruta" class="public-field"></label>
                <label class="form-label sm:col-span-2">Tipo de queja<select name="tipo_queja" required class="public-field"><option value="">¿Qué necesitas reportar?</option>@foreach(['Limpieza','Falla mecánica','Incidencia en ruta','Daño o seguridad','Otro'] as $tipo)<option @selected(old('tipo_queja')===$tipo)>{{ $tipo }}</option>@endforeach</select></label>
                <label class="form-label sm:col-span-2">Descripción<textarea name="descripcion" required maxlength="1000" rows="4" placeholder="Describe brevemente lo ocurrido y dónde se encuentra la unidad." class="public-field">{{ old('descripcion') }}</textarea></label>
                <label class="form-label sm:col-span-2">Foto (opcional)<input name="evidencia_foto" type="file" accept="image/*" capture="environment" class="public-field file-field"><span class="mt-1 block text-xs font-normal text-slate-500">JPG, PNG o imagen del teléfono; máximo 5 MB.</span></label>
                <label class="form-label sm:col-span-2">Nombre (opcional)<input name="nombre_reportante" maxlength="120" value="{{ old('nombre_reportante') }}" class="public-field"></label>
                <label class="honeypot" aria-hidden="true">Dejar vacío<input name="sitio_web" tabindex="-1" autocomplete="off"></label>
                <p class="text-xs leading-5 text-slate-500 sm:col-span-2">Tu reporte se enviará al equipo de RODAGEST. Al enviarlo recibirás un folio privado para consultar el avance.</p>
                <button class="min-h-12 rounded-xl bg-[#018FC7] px-5 font-bold text-white shadow-sm transition hover:bg-sky-700 sm:col-span-2">Enviar reporte</button>
            </form>
        </div>
    </section>
</div>
<style>.form-label{display:block;min-width:0;font-size:.875rem;font-weight:650;color:#334155}.public-field{display:block;width:100%;min-width:0;margin-top:.4rem;border:1px solid #B1D0E2;border-radius:.8rem;background:#fff;padding:.8rem .9rem;font-size:1rem;font-weight:400;outline:none}.public-field:focus{border-color:#018FC7;box-shadow:0 0 0 3px #018FC733}.file-field{font-size:.85rem}.honeypot{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}</style>
@endsection
