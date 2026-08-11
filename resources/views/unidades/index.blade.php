@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Catálogo de Unidades</h1>
        <a href="{{ route('unidades.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow transition">
            + Nueva Unidad
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Económico</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Placa</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($unidades as $unidad)
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $unidad->economico }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $unidad->tipo_unidad ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $unidad->placa ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm font-medium flex items-center space-x-3">
                        <a href="{{ route('unidades.edit', $unidad->id) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                        
                        <form action="{{ route('unidades.destroy', $unidad->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta unidad?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay unidades registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection