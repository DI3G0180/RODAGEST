<?php

namespace App\Http\Controllers;

use App\Http\Requests\AseoStoreRequest;
use App\Models\Aseo;
use App\Models\Unidad;
use Illuminate\Http\Request;

class AseoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aseo::with('unidad');

        // Filtrar por rol: operador solo ve sus propias unidades
        if (auth()->user()->role === 'operador') {
            $query->whereHas('unidad', function ($q) {
                $q->where('operador_id', auth()->id());
            });
        }

        // Filtrar por intendente si se especifica
        if ($request->filled('intendente_responsable')) {
            $query->where('intendente_responsable', $request->intendente_responsable);
        }

        $aseos = $query->latest()->paginate(10);
        $unidades = Unidad::all();

        return view('aseos.index', compact('aseos', 'unidades'));
    }

    public function store(AseoStoreRequest $request)
    {
        $validatedData = $request->validated();

        $aseo = Aseo::create($validatedData);

        return redirect()->route('aseos.index')->with('success', 'Registro de aseo guardado correctamente.');
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string',
        ]);

        $aseo = Aseo::findOrFail($id);
        $aseo->update(['estado' => $request->estado]);

        return redirect()->route('aseos.index')->with('success', 'Estado actualizado correctamente.');
    }
}