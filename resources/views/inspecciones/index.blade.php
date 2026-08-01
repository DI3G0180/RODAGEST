@extends('layouts.app')

@section('title', 'Inspección de Daños y Reporte de Fallas')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
            <i class="ri-checkbox-circle-fill text-emerald-500 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO SUPERIOR DE REGISTRO DE DAÑO -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="ri-error-warning-line text-red-500 text-lg"></i>
            Nuevo Reporte de Daño / Falla
        </h3>

        <form action="{{ route('inspecciones.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf

            <!-- UNIDAD SELECT (1 AL 50) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Unidad / Económico</label>
                <select name="unidad_id" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">Seleccionar Unidad...</option>
                    @for ($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}">Unidad #{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- INSPECTOR RESPONSABLE -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Inspector Responsable</label>
                <input type="text" name="inspector_responsable" required placeholder="Nombre del inspector" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <!-- TIPO DE DAÑO -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tipo de Daño / Falla</label>
                <select name="tipo_dano" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">Seleccionar Tipo...</option>
                    <option value="Mecánico">Falla Mecánica</option>
                    <option value="Carrocería / Cristal">Carrocería / Cristales</option>
                    <option value="Eléctrico">Sistema Eléctrico</option>
                    <option value="Neumáticos">Neumáticos / Llantas</option>
                    <option value="Vandalismo / Asientos">Vandalismo / Asientos</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <!-- PRIORIDAD -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Severidad / Prioridad</label>
                <select name="prioridad" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="baja">Baja (Menor / Cosmético)</option>
                    <option value="media" selected>Media (Requiere Atención)</option>
                    <option value="alta">Alta (Prioritario)</option>
                    <option value="critica">Crítica (Unidad Fuera de Servicio)</option>
                </select>
            </div>

            <!-- FECHA Y HORA -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha y Hora</label>
                <input type="datetime-local" name="fecha_reporte" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <!-- DESCRIPCIÓN DEL DAÑO -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Descripción del Daño</label>
                <input type="text" name="descripcion" required placeholder="Detalla el problema o la avería encontrada..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                <button type="reset" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-md shadow-red-500/20 flex items-center gap-2">
                    <i class="ri-alert-line"></i> Guardar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- TABLA DE HISTORIAL DE INSPECCIONES -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Historial de Reportes de Daños</h3>
        </div>

        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 font-semibold">#</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold">Unidad</th>
                    <th class="px-6 py-3 font-semibold">Inspector</th>
                    <th class="px-6 py-3 font-semibold">Tipo</th>
                    <th class="px-6 py-3 font-semibold">Prioridad</th>
                    <th class="px-6 py-3 font-semibold">Estado</th>
                    <th class="px-6 py-3 font-semibold">Descripción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inspecciones as $inspeccion)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $inspeccion->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($inspeccion->fecha_reporte)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 font-semibold text-brand-600">Unidad #{{ $inspeccion->unidad_id }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $inspeccion->inspector_responsable }}</td>
                        <td class="px-6 py-4">{{ $inspeccion->tipo_dano }}</td>
                        <td class="px-6 py-4">
                            @if($inspeccion->prioridad === 'baja')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Baja</span>
                            @elseif($inspeccion->prioridad === 'media')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Media</span>
                            @elseif($inspeccion->prioridad === 'alta')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Alta</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Crítica</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($inspeccion->estado === 'pendiente')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">Pendiente</span>
                            @elseif($inspeccion->estado === 'en_proceso')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">En Proceso</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-300">Resuelto</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $inspeccion->descripcion }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate-400 text-sm">
                            No hay reportes de daños registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection