<?php

namespace App\Http\Controllers;

use App\Models\Aseo;
use App\Models\Unidad;
use Illuminate\Http\Request;

class AseoController extends Controller
{
    public function index(Request $request)
    {
        // Traemos los aseos con la relación 'unidad' cargada
        $aseos = Aseo::with('unidad')->latest()->get();
        $unidades = Unidad::all();

        // Si la petición viene desde Vue / Axios / API
        if ($request->wantsJson()) {
            return response()->json([
                'aseos' => $aseos,
                'unidades' => $unidades
            ]);
        }

        return view('aseos.index', compact('aseos', 'unidades'));
    }

    public function store(Request $request)
    {
        // Bloqueo de seguridad a nivel Backend si el usuario es Operador
        if (auth()->check() && auth()->user()->role === 'operador') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Los operadores no tienen permiso para realizar registros.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permisos para registrar aseos.');
        }

        $validatedData = $request->validate([
            'unidad_id'              => 'required|exists:unidades,id',
            'intendente_responsable' => 'required|string|max:255',
            'area_terminal'          => 'required|string|max:255',
            'fecha_hora'             => 'required',
            'comentarios'            => 'nullable|string',
        ]);

        $aseo = Aseo::create($validatedData);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registro de aseo guardado correctamente.',
                'aseo' => $aseo->load('unidad')
            ], 201);
        }

        return redirect()->route('aseos.index')->with('success', 'Registro de aseo guardado correctamente.');
    }
}