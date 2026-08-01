<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use Illuminate\Http\Request;

class InspeccionController extends Controller
{
    public function index()
    {
        // Traemos las inspecciones cargando la relación con la unidad si la tienes configurada
        $inspecciones = Inspeccion::latest()->get();
        return view('inspecciones.index', compact('inspecciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unidad_id'             => 'required|integer|exists:unidades,id',
            'inspector_responsable' => 'required|string|max:255',
            'tipo_dano'             => 'required|string|max:255',
            'prioridad'             => 'required|in:baja,media,alta,critica',
            'fecha_reporte'         => 'required|date',
            'descripcion'           => 'required|string',
        ]);

        Inspeccion::create([
            'unidad_id'             => $request->unidad_id,
            'inspector_responsable' => $request->inspector_responsable,
            'tipo_dano'             => $request->tipo_dano,
            'prioridad'             => $request->prioridad,
            'estado'                => 'pendiente',
            'fecha_reporte'         => $request->fecha_reporte,
            'descripcion'           => $request->descripcion,
        ]);

        return redirect()->route('inspecciones.index')->with('success', 'Reporte de daño registrado con éxito.');
    }
}