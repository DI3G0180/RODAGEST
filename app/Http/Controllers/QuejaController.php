<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuejaStoreRequest;
use App\Models\Queja;
use App\Models\Unidad;
use Illuminate\Http\Request;

class QuejaController extends Controller
{
    public function index(Request $request)
    {
        $query = Queja::with(['unidad', 'operador']);

        // Filtrar por estado si se solicita
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por unidad si se especifica
        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        // Filtrar por operador si se especifica
        if ($request->filled('operador_id')) {
            $query->where('operador_id', $request->operador_id);
        }

        $quejas = $query->latest()->paginate(10);

        $unidades = Unidad::all();

        return view('quejas.index', compact('quejas', 'unidades'));
    }

    public function store(QuejaStoreRequest $request)
    {
        $validatedData = $request->validated();

        // Asignar el operador actual si tiene role operador, sino dejarlo null para admin/inspector
        if (auth()->user()->role === 'operador') {
            $validatedData['operador_id'] = auth()->id();
        }

        $queja = Queja::create($validatedData);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Queja registrada con éxito.',
                'queja' => $queja->load('unidad', 'operador')
            ], 201);
        }

        return redirect()->route('quejas.index')->with('success', 'Queja registrada con éxito.');
    }

    public function show(Queja $queja)
    {
        return view('quejas.show', compact('queja'));
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto',
        ]);

        $queja = Queja::findOrFail($id);
        $queja->update(['estado' => $request->estado]);

        return redirect()->route('quejas.index')->with('success', 'Estado de queja actualizado correctamente.');
    }

    public function uploadEvidencia(Request $request, $id)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:jpg,png,webp', 'max:5120'], // 5MB max
        ]);

        $queja = Queja::findOrFail($id);

        // Generar nombre de archivo único
        $extension = $request->file('archivo')->getClientOriginalExtension();
        $nombreArchivo = 'queja_' . $queja->id . '_' . now('Ymd_His') . '.' . $extension;

        // Mover al storage/app/public/quejas
        $request->file('archivo')->move(storage_path('app/public/quejas'), $nombreArchivo);

        $queja->ruta_archivo = '/storage/quejas/' . $nombreArchivo;
        $queja->save();

        return redirect()->route('quejas.show', $queja)->with('success', 'Evidencia subida correctamente.');
    }
}