<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnidadStoreRequest;
use App\Http\Requests\UnidadUpdateRequest;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $unidades = Unidad::latest()->paginate(10);
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(UnidadStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Generar el código económico con formato ECO-XXX si no viene con formato
        if (!preg_match('/^ECO-\d+$/i', $validated['economico'])) {
            $ultimaUnidad = Unidad::latest('economico')->first();
            $numeroSiguiente = ($ultimaUnidad ? intval(explode('-', $ultimaUnidad->economico)[1]) : 0) + 1;
            $validated['economico'] = 'ECO-' . str_pad($numeroSiguiente, 3, '0', STR_PAD_LEFT);
        }

        Unidad::create($validated);

        return redirect()->route('unidades.index')->with('success', 'Unidad creada correctamente');
    }

    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        return view('unidades.edit', compact('unidad'));
    }

    public function update(UnidadUpdateRequest $request, $id)
    {
        $unidad = Unidad::findOrFail($id);
        $validated = $request->validated();
        
        // Evitar cambiar el económico a uno que ya exista (excluyendo el actual)
        if (isset($validated['economico'])) {
            $exists = Unidad::where('economico', $validated['economico'])
                ->where('id', '!=', $unidad->id)
                ->exists();
            
            if ($exists) {
                return back()->with('error', 'Ya existe otra unidad con este número económico.');
            }
        }

        $unidad->update($validated);

        return redirect()->route('unidades.index')->with('success', 'Unidad actualizada correctamente');
    }

    public function destroy($id)
    {
        // Validación de seguridad estricta para evitar que un operador elimine registros
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('unidades.index')->with('error', 'No tienes permisos de administrador para eliminar unidades.');
        }

        $unidad = Unidad::findOrFail($id);
        $unidad->delete();
        return redirect()->route('unidades.index')->with('success', 'Unidad eliminada correctamente');
    }
}