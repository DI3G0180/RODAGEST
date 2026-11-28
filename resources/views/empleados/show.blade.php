@extends('layouts.app')
@section('title', 'Expediente de personal')
@section('content')
@php
    $personal = ['curp'=>'CURP','rfc'=>'RFC','nss'=>'NSS','escolaridad'=>'Escolaridad','lugar_nacimiento'=>'Lugar de nacimiento','estado_civil'=>'Estado civil','nombre_padre'=>'Nombre del padre','nombre_madre'=>'Nombre de la madre','nombre_conyuge'=>'Cónyuge','identificacion'=>'Identificación','calle'=>'Calle','numero'=>'Número','colonia'=>'Colonia','municipio'=>'Municipio','estado'=>'Estado','codigo_postal'=>'Código postal','telefono_alterno'=>'Teléfono alterno'];
    $laboral = ['estatus_imss'=>'Estatus IMSS','tipo_ingreso'=>'Tipo de ingreso','licencia'=>'Licencia','tarjeton'=>'Tarjetón'];
@endphp
<div class="mx-auto max-w-5xl space-y-5 p-4 sm:p-6" x-data="{ tab: 'expediente' }">
    <header class="flex flex-wrap items-center justify-between gap-3"><div class="flex min-w-0 items-center gap-3">
        @if($empleado->foto)<img src="{{ '/storage/'.$empleado->foto }}" alt="Foto de {{ $empleado->name }}" class="h-14 w-14 shrink-0 rounded-full border border-slate-200 object-cover">@else<div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-[#DBE7F3] text-xl font-extrabold text-[#0C7075]">{{ strtoupper(mb_substr($empleado->name,0,1)) }}</div>@endif
        <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-widest text-[#0C7075]">Expediente individual</p><h1 class="truncate text-xl font-extrabold sm:text-2xl">{{ $empleado->name }}</h1><p class="truncate text-sm text-[#294D61]">{{ $empleado->email }}</p></div>
    </div><a href="{{ route('empleados.index', [], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#6DA5C0]/50 bg-white px-4 text-sm font-semibold text-[#0C7075]">← Personal</a></header>
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    <nav aria-label="Secciones del expediente" class="grid min-w-0 grid-cols-2 gap-2 rounded-2xl border border-slate-200 bg-white p-2">
        <button type="button" @click="tab='expediente'" :aria-current="tab==='expediente' ? 'page' : null" :class="tab==='expediente' ? 'bg-[#0C7075] text-white' : 'text-slate-700 hover:bg-[#DBE7F3]'" class="min-h-11 min-w-0 whitespace-normal rounded-xl px-2 py-2.5 text-center text-xs font-bold sm:px-3 sm:text-sm">Expediente / hoja de vida</button>
        <button type="button" @click="tab='cuenta'" :aria-current="tab==='cuenta' ? 'page' : null" :class="tab==='cuenta' ? 'bg-[#0C7075] text-white' : 'text-slate-700 hover:bg-[#DBE7F3]'" class="min-h-11 min-w-0 whitespace-normal rounded-xl px-2 py-2.5 text-center text-xs font-bold sm:px-3 sm:text-sm">Editar accesos y cuenta</button>
        @if($empleado->role !== 'admin' && $empleado->id !== auth()->id())<button type="button" @click="tab='baja'" :aria-current="tab==='baja' ? 'page' : null" :class="tab==='baja' ? 'bg-red-700 text-white' : 'text-red-700 hover:bg-red-50'" class="min-h-11 min-w-0 whitespace-normal rounded-xl px-2 py-2.5 text-center text-xs font-bold sm:px-3 sm:text-sm">Zona de baja</button>@endif
    </nav>

    <section x-show="tab==='cuenta'" x-cloak class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4"><h2 class="text-lg font-extrabold">Cuenta y accesos</h2><p class="mt-1 text-sm text-[#294D61]">Actualiza la identidad, asignación, vigencia y contraseña del trabajador.</p></div>
        @if($empleado->is(auth()->user()))<p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">Por seguridad, la cuenta administradora con la que iniciaste sesión no se puede editar desde esta pantalla.</p>
        @else
        <form action="{{ route('empleados.update', [$empleado->id], false) }}" method="POST" enctype="multipart/form-data" class="grid min-w-0 gap-3 sm:grid-cols-2">@csrf @method('PATCH')
            <label class="form-label">Nombre completo<input name="name" required maxlength="255" value="{{ old('name',$empleado->name) }}" class="field mt-1"></label>
            <label class="form-label">Correo electrónico<input name="email" type="email" required value="{{ old('email',$empleado->email) }}" class="field mt-1"></label>
            @if($empleado->role === 'admin')<div class="rounded-xl bg-[#F4F9FB] p-3 text-sm font-semibold text-[#294D61]">Rol protegido: Administrador</div>@else<label class="form-label">Rol<select name="role" required class="field mt-1">@foreach(['inspector'=>'Inspector','operador'=>'Operador','limpieza'=>'Limpieza'] as $key=>$label)<option value="{{ $key }}" @selected(old('role',$empleado->role)===$key)>{{ $label }}</option>@endforeach</select></label>@endif
            <label class="form-label">Estado de acceso<select name="activo" class="field mt-1"><option value="1" @selected((string)old('activo',(int)$empleado->activo)==='1')>Activo</option><option value="0" @selected((string)old('activo',(int)$empleado->activo)==='0')>Inactivo</option></select></label>
            <label class="form-label">Teléfono<input name="telefono" maxlength="30" value="{{ old('telefono',$empleado->telefono) }}" class="field mt-1"></label>
            <label class="form-label">Fecha de alta<input name="fecha_ingreso" type="date" min="2026-01-01" value="{{ old('fecha_ingreso',$empleado->fecha_ingreso?->format('Y-m-d')) }}" class="field mt-1"></label>
            <label class="form-label sm:col-span-2">Unidad asignada<select name="unidad_id" class="field mt-1"><option value="">Sin unidad asignada</option>@foreach($unidades as $unidad)<option value="{{ $unidad->id }}" @selected(old('unidad_id',$empleado->unidadAsignada?->id)==$unidad->id)>{{ $unidad->economico }} · {{ $unidad->estado }}</option>@endforeach</select></label>
            @include('components.password-policy', ['id'=>'employee-manage-password-'.$empleado->id,'required'=>false,'label'=>'Contraseña temporal nueva (opcional)','placeholder'=>'Déjala vacía para conservar la actual','containerClass'=>'sm:col-span-2'])
            <label class="form-label sm:col-span-2">Foto de perfil<input name="foto" type="file" accept="image/jpeg,image/png,image/webp" capture="user" class="field mt-1"><span class="mt-1 block text-xs font-normal">JPG, PNG o WEBP, máximo 5 MB.</span></label>
            <button class="min-h-12 rounded-xl bg-[#0F969C] px-5 font-bold text-white transition hover:bg-[#072E33] sm:col-span-2">Guardar cuenta y accesos</button>
        </form>
        @endif
    </section>

    <section x-show="tab==='expediente'" x-cloak class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4"><h2 class="text-lg font-extrabold">Hoja de vida / expediente laboral</h2><p class="mt-1 text-sm text-[#294D61]">Los datos personales y laborales se guardan cifrados cuando así está definido por el modelo.</p></div>
        <form action="{{ route('empleados.hoja-vida.update', [$empleado->id], false) }}" method="POST" class="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-3">@csrf @method('PATCH')
            @foreach($personal as $field=>$label)<label class="form-label">{{ $label }}<input name="{{ $field }}" value="{{ old($field,$perfil->{$field}) }}" class="field mt-1"></label>@endforeach
            <label class="form-label">Fecha de nacimiento<input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento',$perfil->fecha_nacimiento) }}" class="field mt-1"></label>
            @foreach($laboral as $field=>$label)<label class="form-label">{{ $label }}<input name="{{ $field }}" value="{{ old($field,$perfil->{$field}) }}" class="field mt-1"></label>@endforeach
            <label class="form-label">Fecha de reingreso<input type="date" name="fecha_reingreso" value="{{ old('fecha_reingreso',$perfil->fecha_reingreso?->format('Y-m-d')) }}" class="field mt-1"></label>
            <label class="form-label">Contacto de emergencia<input name="contacto_emergencia" maxlength="180" value="{{ old('contacto_emergencia',$perfil->contacto_emergencia) }}" class="field mt-1"></label>
            <label class="form-label">Teléfono de emergencia<input name="telefono_emergencia" maxlength="30" value="{{ old('telefono_emergencia',$perfil->telefono_emergencia) }}" class="field mt-1"></label>
            <button class="min-h-12 rounded-xl bg-[#0F969C] px-5 font-bold text-white transition hover:bg-[#072E33] sm:col-span-2 lg:col-span-3">Guardar expediente</button>
        </form>
    </section>

    @if($empleado->role !== 'admin' && $empleado->id !== auth()->id())
    <section x-show="tab==='baja'" x-cloak class="rounded-2xl border border-red-200 bg-white p-4 shadow-sm sm:p-6">
        <h2 class="text-lg font-extrabold text-red-800">Zona de baja lógica</h2><p class="mt-1 text-sm text-[#294D61]">La cuenta se enviará a Papelera y conservará su historial; no se elimina físicamente.</p>
        <form action="{{ route('empleados.destroy', [$empleado->id], false) }}" method="POST" class="mt-4 space-y-3" onsubmit="return confirm('¿Dar de baja a este trabajador? Podrás restaurarlo desde Papelera.')">@csrf @method('DELETE')
            <label class="form-label">Motivo de baja<textarea name="motivo_baja" required maxlength="1000" rows="3" class="field mt-1" placeholder="Escribe el motivo de la baja"></textarea></label>
            <button class="min-h-12 rounded-xl bg-red-700 px-5 font-bold text-white transition hover:bg-red-800">Dar de baja trabajador</button>
        </form>
    </section>
    @endif
</div>
<style>.field{display:block;width:100%;min-width:0;border:1px solid #6DA5C0;border-radius:.75rem;background:#fff;padding:.72rem .8rem;outline:none}.field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}.form-label{display:block;min-width:0;font-size:.875rem;font-weight:650;color:#294D61}</style>
<script>setTimeout(()=>document.querySelectorAll('.toast').forEach(el=>{el.style.transition='opacity .35s';el.style.opacity='0';setTimeout(()=>el.remove(),400)}),4500)</script>
@endsection
