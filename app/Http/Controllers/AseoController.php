<?php

namespace App\Http\Controllers;

use App\Models\Aseo;
use App\Models\Unidad; // Importamos el modelo Unidad
use Illuminate\Http\Request;

class AseoController extends Controller
{
    public function index()
    {
        $aseos = Aseo::latest()->get();
        $unidades = Unidad::all(); // Obtenemos todas las unidades de la DB

        return view('aseos.index', compact('aseos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unidad_id'              => 'required|integer|exists:unidades,id', // 'exists' evita que reviente con pantalla roja
            'intendente_responsable' => 'required|string|max:255',
            'area_terminal'          => 'required|string|max:255',
            'fecha_hora'             => 'required|date',
            'comentarios'            => 'nullable|string',
        ]);

        Aseo::create($request->all());

        return redirect()->route('aseos.index')->with('success', 'Registro de aseo guardado correctamente.');
    }
}