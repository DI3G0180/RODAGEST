<?php

namespace App\Http\Controllers;

use App\Http\Requests\InspeccionStoreRequest;
use App\Models\Inspeccion;
use App\Models\Unidad;
use Illuminate\Http\Request;

class InspeccionController extends Controller
{
    public function index(Request $request)
    {
        // Filtrar por rol: operador solo ve daños de su unidad asignada
        $query = Inspeccion::with('unidad');

        if (auth()->user()->role === 'operador') {
            // El operador solo ve daños de su unidad asignada
            $query->whereHas('unidad', function ($q) {
                $q->where('operador_id', auth()->id());
            });
        }

        // Filtrar por estado si se solicita
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por intendente si se especifica
        if ($request->filled('intendente_responsable')) {
            $query->where('intendente_responsable', $request->intendente_responsable);
        }

        $inspecciones = $query->latest()->paginate(10);
        $unidades = Unidad::all();

        return view('inspecciones.index', compact('inspecciones', 'unidades'));
    }

    public function store(InspeccionStoreRequest $request)
    {
        $validatedData = $request->validated();

        // Establecer estado inicial como 'pendiente'
        $validatedData['estado'] = 'pendiente';

        // Asignar operador si el usuario es operador
        if (auth()->user()->role === 'operador') {
            $validatedData['operador_id'] = auth()->id();
        }

        $inspeccion = Inspeccion::create($validatedData);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reporte de daño registrado con éxito.',
                'inspeccion' => $inspeccion->load('unidad', 'operador')
            ], 201);
        }

        return redirect()->route('inspecciones.index')->with('success', 'Reporte de daño registrado con éxito.');
    }

    public function show(Inspeccion $inspeccion)
    {
        return view('inspecciones.show', compact('inspeccion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto',
        ]);

        $inspeccion = Inspeccion::findOrFail($id);
        
        // Validación de seguridad: solo admin o el intendente asignado pueden actualizar estado
        if (auth()->user()->role !== 'admin' && 
            auth()->user()->role !== 'intendente' && 
            auth()->user()->id !== $inspeccion->intendente_responsable) {
            return redirect()->route('inspecciones.index')->with('error', 'No tiene permisos para actualizar este reporte.');
        }

        $inspeccion->update(['estado' => $request->estado]);

        return redirect()->route('inspecciones.index')->with('success', 'Estado actualizado correctamente.');
    }

    public function cambiarEstado($id, string $nuevoEstado)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto',
        ]);

        $inspeccion = Inspeccion::findOrFail($id);
        
        // Validación de seguridad: solo admin o intendente pueden cambiar estado
        if (auth()->user()->role !== 'admin' && 
            auth()->user()->role !== 'intendente') {
            return back()->with('error', 'No tiene permisos para cambiar el estado.');
        }

        $inspeccion->update(['estado' => $nuevoEstado]);

        return redirect()->route('inspecciones.index')->with('success', 'Estado actualizado a ' . $nuevoEstado . '.');
    }
}