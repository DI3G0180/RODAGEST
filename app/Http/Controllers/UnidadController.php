<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $unidades = Unidad::all();
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        // Si no es admin, no puede entrar al formulario de creación
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('unidades.index')->with('error', 'Acceso denegado. Solo los administradores pueden registrar unidades.');
        }

        return view('unidades.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('unidades.index')->with('error', 'Acceso denegado.');
        }

        $request->validate([
            'economico' => 'required|unique:unidades,economico',
            'placa' => 'nullable',
            'tipo_unidad' => 'nullable'
        ]);

        Unidad::create($request->all());

        return redirect()->route('unidades.index')->with('success', 'Unidad creada correctamente');
    }

    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('unidades.index')->with('error', 'Acceso denegado.');
        }

        $unidad = Unidad::findOrFail($id);
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('unidades.index')->with('error', 'Acceso denegado.');
        }

        $unidad = Unidad::findOrFail($id);
        $unidad->update($request->all());
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