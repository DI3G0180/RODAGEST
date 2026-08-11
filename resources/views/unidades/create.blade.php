@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Registrar Nueva Unidad</h1>

        <form action="{{ route('unidades.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Económico</label>
                <input type="text" name="economico" placeholder="Ej: ECO-12" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Unidad</label>
                <input type="text" name="tipo_unidad" placeholder="Ej: Ordinario" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Placa</label>
                <input type="text" name="placa" placeholder="Ej: YTZ-000" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('unidades.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Unidad</button>
            </div>
        </form>
    </div>
</div>
@endsection