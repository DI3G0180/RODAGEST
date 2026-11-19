@extends('layouts.app')

@section('title', 'Módulo de Quejas y Reportes')
@section('content')

<!-- Alertas -->
@if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2 animate-fade-in-up">
        <i class="ri-checkbox-circle-fill text-emerald-500 text-lg"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium flex items-center gap-2 animate-fade-in-up">
        <i class="ri-error-warning-fill text-red-500 text-lg"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Tarjeta de Estadísticas de Quejas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-primary text-white p-4 rounded-xl text-center">
        <div class="text-2xl font-bold">{{ $quejas->count() }}</div>
        <div class="text-sm opacity-90">Total Quejas</div>
    </div>
    <div class="bg-secondary text-white p-4 rounded-xl text-center">
        <div class="text-2xl font-bold">{{ $quejas->where('estado', 'pendiente')->count() }}</div>
        <div class="text-sm opacity-90">Pendientes</div>
    </div>
    <div class="bg-tertiary text-white p-4 rounded-xl text-center">
        <div class="text-2xl font-bold">{{ $quejas->where('estado', 'en_proceso')->count() }}</div>
        <div class="text-sm opacity-90">En Proceso</div>
    </div>
    <div class="bg-tertiary text-white p-4 rounded-xl text-center">
        <div class="text-2xl font-bold">{{ $quejas->where('estado', 'resuelto')->count() }}</div>
        <div class="text-sm opacity-90">Resueltas</div>
    </div>
</div>

<!-- Formulario de Registro de Queja -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6 animate-fade-in-up max-w-2xl">
    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
        <i class="ri-error-warning-line text-primary-500 text-lg"></i>
        Nuevo Reporte de Queja
    </h3>

    <form action="{{ route('quejas.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        
        <!-- UNIDAD -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Unidad / Económico</label>
            <select name="unidad_id" required class="w-full bg-cream border-tertiary rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                <option value="">Seleccionar Unidad...</option>
                @foreach($unidades as $unidad)
                    <option value="{{ $unidad->id }}" {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}>
                        Eco: {{ $unidad->economico }} - {{ $unidad->tipo_unidad ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- TIPO DE QUEJA -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Tipo de Queja</label>
            <select name="tipo_queja" required class="w-full bg-cream border-tertiary rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                <option value="">Seleccionar tipo...</option>
                <option value="Limpieza">Limpieza</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Daño">Daño</option>
                <option value="Confort">Confort / Asientos</option>
                <option value="Vandalismo">Vandalismo</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <!-- DESCRIPCIÓN -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-600 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" placeholder="Detalle la queja o inconveniente..." 
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream resize-none text-sm"
                      required></textarea>
        </div>

        <!-- ESTADO INICIAL -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Estado Inicial</label>
            <select name="estado" class="w-full bg-cream border-tertiary rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                <option value="pendiente" selected>Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="resuelto">Resuelto</option>
            </select>
        </div>

        <div class="md:col-span-2 flex justify-end gap-2 mt-4">
            <button type="reset" class="px-4 py-2 text-sm font-medium text-slate-600 bg-cream rounded-lg hover:bg-secondary transition">Cancelar</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-secondary transition shadow-md shadow-primary/20">
                <i class="ri-file-text-line text-sm mr-2 align-middle"></i>
                Registrar Queja
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Historial de Quejas -->
<div class="bg-cream rounded-xl border border-tertiary shadow-sm overflow-hidden animate-fade-in-up">
    <div class="px-6 py-4 border-b border-tertiary flex items-center justify-between">
        <h3 class="font-extrabold text-primary">Historial de Quejas y Reportes</h3>
        
        <!-- Filtro por estado -->
        <div class="hidden md:flex items-center gap-2">
            <select @change="this.form.submit()" class="px-3 py-1 rounded border border-slate-300 text-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
            </select>
        </div>
    </div>

    <table class="w-full text-left text-sm text-slate-500">
        <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
            <tr>
                <th class="px-6 py-3 font-semibold">#</th>
                <th class="px-6 py-3 font-semibold">Unidad</th>
                <th class="px-6 py-3 font-semibold">Tipo</th>
                <th class="px-6 py-3 font-semibold">Operador</th>
                <th class="px-6 py-3 font-semibold">Estado</th>
                <th class="px-6 py-3 font-semibold">Fecha</th>
                <th class="px-6 py-3 font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($quejas as $queja)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-bold text-slate-800">{{ $queja->id }}</td>
                <td class="px-6 py-4 font-semibold text-primary">
                    Eco: {{ $queja->unidad->economico }} - {{ $queja->unidad->tipo_unidad ?? 'N/A' }}
                </td>
                <td class="px-6 py-4">
                    <span class="capitalize text-slate-400 text-sm">{{ $queja->tipo_queja }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($queja->operador)
                        <span class="px-2 py-0.5 rounded-xs text-xs bg-secondary text-primary">
                            {{ $queja->operador->name }}
                        </span>
                    @else
                        <span class="text-slate-400 text-sm">No asignado</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="capitalize text-slate-400 text-sm">{{ $queja->tipo_queja }}</span>
                </td>
                <td class="px-6 py-4">
                    <!-- Badge de estado animado -->
                    <div class="flex items-center gap-1.5">
                        @if($queja->estado === 'pendiente')
                            <button class="px-2 py-0.5 rounded-full text-xs font-semibold bg-secondary text-primary border border-tertiary hover:bg-tertiary/50 transition pulse">
                                <i class="ri-alert-line text-xs align-middle"></i>
                                Pendiente
                            </button>
                        @elseif($queja->estado === 'en_proceso')
                            <button class="px-2 py-0.5 rounded-full text-xs font-semibold bg-tertiary text-primary border border-tertiary hover:bg-tertiary/50 transition">
                                <i class="ri-play-line text-xs align-middle"></i>
                                En proceso
                            </button>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-tertiary text-primary border border-tertiary">
                                <i class="ri-check-double-line text-xs align-middle"></i>
                                Resuelto
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-400 whitespace-nowrap">
                    <span class="text-xs">{{ \Carbon\Carbon::parse($queja->created_at)->format('d/m/Y H:i') }}</span>
                </td>
                <td class="px-6 py-4 flex gap-1">
                    <a href="#" class="text-primary-400 hover:text-primary-600 transition flex items-center gap-1" data-bs-toggle="modal" data-bs-target="#quejaModal{{ $queja->id }}">
                        <i class="ri-eye-line text-xs"></i>
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <form action="{{ route('quejas.destroy', $queja->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 transition flex items-center gap-1" onclick="return confirm('¿Eliminar queja #{{ $queja->id }}?')">
                            <i class="ri-delete-bin-line text-xs"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">
                    <i class="ri-file-text-line text-4xl text-slate-300 mb-3 d-block"></i>
                    <p>No hay quejas registradas.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modales de detalle de queja -->
@forelse($quejas as $queja)
<div id="quejaModal{{ $queja->id }}" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" x-show="mostrarModal{{ $queja->id }}" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-150">
    <div class="bg-white rounded-xl w-full max-w-2xl m-6 p-8 shadow-xl transform scale-95" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-150">
        <h3 class="text-2xl font-bold text-primary-800 mb-4">Queja #{{ $queja->id }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-slate-500 text-sm mb-2">Eco Unidad</p>
                <p class="text-primary-600 font-bold">{{ $queja->unidad->economico }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm mb-2">Tipo</p>
                <p class="text-primary-600 font-bold capitalize">{{ $queja->tipo_queja }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm mb-2">Operador</p>
                <p class="text-primary-600 font-bold">{{ $queja->operador ? $queja->operador->name : 'No asignado' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm mb-2">Estado</p>
                <p class="text-primary-600 font-bold">
                    @if($queja->estado === 'pendiente')
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-secondary text-primary border border-tertiary">Pendiente</span>
                    @elseif($queja->estado === 'en_proceso')
                        <span class="px-2 py-0.5 rounded-xs text-xs bg-emerald-100 text-emerald-600 border border-emerald-300">En Proceso</span>
                    @else
                        <span class="px-2 py-0.5 rounded-xs text-xs bg-emerald-100 text-emerald-600 border border-emerald-300">Resuelto</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-slate-500 text-sm mb-2">Descripción</p>
                <p class="text-slate-700 break-all">{{ $queja->descripcion }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-sm mb-2">Fecha Registro</p>
                <p class="text-slate-400">{{ \Carbon\Carbon::parse($queja->created_at)->format('d-m-Y H:i') }}</p>
            </div>
        </div>
        
        @if(auth()->user()->role === 'admin')
        <div class="mt-6">
            <button @click="$dispatch('actualizarEstado', {id: {{ $queja->id }}, nuevoEstado: 'en_proceso'})" class="px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white hover:bg-secondary transition">
                <i class="ri-play-line text-sm mr-2 align-middle"></i>
                Cambiar a En proceso
            </button>
            <button @click="$dispatch('actualizarEstado', {id: {{ $queja->id }}, nuevoEstado: 'resuelto'})" class="px-4 py-2 rounded-lg text-sm font-medium bg-emerald-500 text-emerald-200 hover:bg-emerald-400 transition">
                <i class="ri-check-double-line text-sm mr-2 align-middle"></i>
                Cambiar a Resuelto
            </button>
        </div>
        @endif
        
        <button @click="mostrarModal{{ $queja->id }} = false" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-600 hover:bg-slate-300 transition mt-4">
            Cerrar
        </button>
    </div>
</div>
@endforelse

@endsection