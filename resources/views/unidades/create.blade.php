@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 animate-fade-in-up">
        <h1 class="text-2xl font-bold primary mb-6 flex items-center gap-2">
            <i class="ri-file-line text-primary-500 text-lg"></i>
            Registrar Nueva Unidad
        </h1>

        <form action="{{ route('unidades.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Número Económico (Eco)</label>
                    <input type="text" name="economico" placeholder="Ej: ECO-101" required 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream transition-colors"
                           autocomplete="off">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Unidad</label>
                    <input type="text" name="tipo_unidad" placeholder="Ej: Autobús, Vagoneta" 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream transition-colors">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Placa</label>
                <input type="text" name="placa" placeholder="Ej: YTZ-000" 
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Área</label>
                <select name="area" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50">
                    <option value="">Seleccionar área</option>
                    <option value="Plataforma">Plataforma</option>
                    <option value="Terminal">Terminal</option>
                    <option value="Almacén">Almacén</option>
                    <option value="Oficina">Oficina</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('unidades.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all duration-300 ease-in-out">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white hover:bg-secondary transition-all duration-300 ease-in-out">
                <i class="ri-save-line text-sm mr-2 align-middle"></i>
                Guardar Unidad
            </button>
        </div>
        </form>
    </div>
</div>
@endsection