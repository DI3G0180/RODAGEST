@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <!-- Tarjeta de búsqueda y filtros -->
    <div class="bg-white rounded-xl border border-tertiary shadow-sm p-6 mb-6 animate-fade-in-up">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Filtrar por Área</label>
                <select class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50" @change="this.form.submit()">
                    <option value="">Todas las áreas</option>
                    <option value="Plataforma">Plataforma</option>
                    <option value="Terminal">Terminal</option>
                    <option value="Almacén">Almacén</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50" @change="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2"></label>
                <div class="flex gap-2">
                    <a href="{{ route('unidades.create') }}" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white hover:bg-secondary transition-all duration-300 ease-in-out">
                        <i class="ri-add-circle-line text-sm mr-2 align-middle"></i>
                        Nueva Unidad
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Unidades -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-800">Catálogo de Unidades <span class="text-slate-400 text-xs font-medium">Número Económico (Eco)</span></h2>
            
            <div class="hidden md:block flex items-center gap-2">
                <span class="text-slate-500 text-xs">Mostrando: <strong class="text-primary">{{ $unidades->count() }}</strong> de {{ $unidades->total() }} unidades</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Económico</th>
                        <th class="px-6 py-3 font-semibold">Tipo</th>
                        <th class="px-6 py-3 font-semibold">Placa</th>
                        <th class="px-6 py-3 font-semibold">Área</th>
                        <th class="px-6 py-3 font-semibold text-sm">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($unidades as $unidad)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-primary">{{ $unidad->economico }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $unidad->tipo_unidad ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $unidad->placa ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $unidad->area ?? 'No asignada' }}</td>
                        <td class="px-6 py-4 text-sm font-medium flex items-center space-x-3">
                            <!-- Botón Editar -->
                            <a href="{{ route('unidades.edit', $unidad->id) }}" class="text-primary-600 hover:text-primary-900 transition-all duration-300 ease-in-out flex items-center gap-1">
                                <i class="ri-edit-line text-xs"></i>
                                Editar
                            </a>
                            
                            <!-- Botón Ver Historial -->
                            <a href="#" class="text-primary-400 hover:text-primary-600 transition-all duration-300 ease-in-out flex items-center gap-1" data-bs-toggle="modal" data-bs-target="#historialModal">
                                <i class="ri-history-line text-xs"></i>
                                Historial
                            </a>
                            
                            <!-- Formulario de eliminación con confirmación -->
                            <form action="{{ route('unidades.destroy', $unidad->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="text-red-600 hover:text-red-900 transition-all duration-300 ease-in-out flex items-center gap-1"
                                    onclick="return confirm('¿Estás seguro de eliminar la unidad {{ $unidad->economico }}?')"
                                >
                                    <i class="ri-delete-bin-line text-xs"></i>
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            <i class="ri-file-text-line text-4xl text-slate-300 mb-3 d-block"></i>
                            <p class="text-slate-500">No hay unidades registradas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($unidades->hasMorePages())
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
            {{ $unidades->links() }}
        }
        @endif
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" x-show="showModal" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-150">
    <div class="bg-white rounded-lg p-8 max-w-sm w-full shadow-xl text-center animate-fade-in-up">
        <i class="ri-error-warning-line text-3xl text-red-500 mb-4 display-block"></i>
        <h3 class="text-xl font-bold text-primary-800 mb-2">Confirmar Eliminación</h3>
        <p class="text-slate-600 mb-6">¿Estás seguro de eliminar la unidad <span class="text-primary font-bold">{{ $unidadEconomico }}</span>? Esta acción no se puede deshacer.</p>
        
        <div class="flex gap-3 justify-end">
            <button @click="showModal = false" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-700 hover:bg-slate-300 transition">Cancelar</button>
            <form action="{{ route('unidades.destroy', $unidad->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-red-600 text-white hover:bg-red-700 transition">Eliminar</button>
            </form>
        </div>
    </div>
</div>
@endsection