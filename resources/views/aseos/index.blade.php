@extends('layouts.app')

@section('title', 'Control de Aseo de Unidades')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
            <i class="ri-checkbox-circle-fill text-emerald-500 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO SUPERIOR RESTRUCTURADO -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="ri-add-circle-line text-blue-600 text-lg"></i>
            Nuevo Registro de Aseo
        </h3>

        <!-- Grid de 3 columnas para que los inputs respiren bien -->
        <form action="{{ route('aseos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            
            <!-- UNIDAD -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Unidad / Económico</label>
                <select name="unidad_id" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Seleccionar Unidad...</option>
                    @for ($i = 1; $i <= 50; $i++)
                        <option value="{{ $i }}">Unidad #{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- INTENDENTE (CAMBIADO A TEXTO MANUAL) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Intendente Responsable</label>
                <input type="text" name="intendente_responsable" required placeholder="Ej. JUAN PÉREZ" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- ÁREA / TERMINAL -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Área / Terminal</label>
                <input type="text" name="area_terminal" required placeholder="Ej. PLATAFORMA PANTITLAN" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- FECHA Y HORA -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha y Hora</label>
                <input type="datetime-local" name="fecha_hora" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- COMENTARIOS (Abarca 2 columnas para no apretar el botón) -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Comentarios</label>
                <input type="text" name="comentarios" placeholder="LIMPIEZA REALIZADA" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- BOTONES CON COLOR AZUL OFICIAL -->
            <div class="md:col-span-3 flex justify-end gap-2 mt-2 pt-2 border-t border-slate-100">
                <button type="reset" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-md shadow-blue-500/20 flex items-center gap-2" style="background-color: #2563eb !important; color: #ffffff !important;">
                    <i class="ri-save-line text-base"></i> Guardar Bitácora
                </button>
            </div>
        </form>
    </div>

    <!-- TABLA HISTORIAL -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Historial de Bitácoras</h3>
        </div>

        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 font-semibold">#</th>
                    <th class="px-6 py-3 font-semibold">Fecha y Hora</th>
                    <th class="px-6 py-3 font-semibold">Unidad</th>
                    <th class="px-6 py-3 font-semibold">Intendente</th>
                    <th class="px-6 py-3 font-semibold">Área / Terminal</th>
                    <th class="px-6 py-3 font-semibold">Comentarios</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($aseos as $aseo)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $aseo->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($aseo->fecha_hora)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 font-semibold text-blue-600">Unidad #{{ $aseo->unidad_id }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $aseo->intendente_responsable }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $aseo->area_terminal }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $aseo->comentarios ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">
                            No hay registros de aseo cargados en la base de datos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection