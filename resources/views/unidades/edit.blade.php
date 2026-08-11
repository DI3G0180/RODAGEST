@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Unidad: {{ $unidad->economico }}</h1>

        <form action="{{ route('unidades.update', $unidad->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Económico</label>
                <input type="text" name="economico" value="{{ old('economico', $unidad->economico) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Unidad</label>
                <input type="text" name="tipo_unidad" value="{{ old('tipo_unidad', $unidad->tipo_unidad) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Placa</label>
                <input type="text" name="placa" value="{{ old('placa', $unidad->placa) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('unidades.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Actualizar Unidad</button>
            </div>
        </form>
    </div>
</div>
@endsection