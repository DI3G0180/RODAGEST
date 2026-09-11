<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\Unidad;
use Illuminate\Http\Request;

class InspeccionController extends Controller
{
    public function index(Request $request)
    {
        // Traemos las inspecciones cargando la relación 'unidad'
        $inspecciones = Inspeccion::with('unidad')->latest()->get();
        $unidades = Unidad::all(); // Enviamos las unidades a la vista

        if ($request->wantsJson()) {
            return response()->json([
                'inspecciones' => $inspecciones,
                'unidades' => $unidades
            ]);
        }

        return view('inspecciones.index', compact('inspecciones', 'unidades'));
    }

    public function store(Request $request)
    {
        // Bloqueo de seguridad: El operador NO puede guardar reportes
        if (auth()->check() && auth()->user()->role === 'operador') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Los operadores no tienen permiso para registrar inspecciones o daños.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permisos para registrar reportes de inspección.');
        }

        $validatedData = $request->validate([
            'unidad_id'             => 'required|integer',
            'inspector_responsable' => 'required|string|max:255',
            'tipo_dano'             => 'required|string|max:255',
            'prioridad'             => 'required|in:baja,media,alta,critica',
            'fecha_reporte'         => 'required|date',
            'descripcion'           => 'required|string',
        ]);

        $inspeccion = Inspeccion::create([
            'unidad_id'             => $validatedData['unidad_id'],
            'inspector_responsable' => $validatedData['inspector_responsable'],
            'tipo_dano'             => $validatedData['tipo_dano'],
            'prioridad'             => $validatedData['prioridad'],
            'estado'                => 'pendiente',
            'fecha_reporte'         => $validatedData['fecha_reporte'],
            'descripcion'           => $validatedData['descripcion'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Reporte de daño registrado con éxito.',
                'inspeccion' => $inspeccion->load('unidad')
            ], 201);
        }

        return redirect()->route('inspecciones.index')->with('success', 'Reporte de daño registrado con éxito.');
    }
}