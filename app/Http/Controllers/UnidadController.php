<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnidadStoreRequest;
use App\Http\Requests\UnidadUpdateRequest;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:40'],
        ]);
        $unidades = Unidad::with('operador')
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($sub) => $sub->where('economico', 'like', "%{$term}%")->orWhere('area', 'like', "%{$term}%")))
            ->when($filters['estado'] ?? null, fn ($query, $status) => $query->where('estado', $status))
            ->latest()->paginate(10)->withQueryString();
        return view('unidades.index', compact('unidades'));
    }

    public function show(Unidad $unidad)
    {
        $user = request()->user();

        abort_unless(
            $user->role !== 'operador' || (int) $unidad->operador_id === (int) $user->id,
            403
        );

        $unidad->load('operador');

        $eventos = collect()
            ->concat($unidad->inspecciones()->with('inspector')->latest('fecha_reporte')->take(15)->get()->map(fn ($item) => [
                'tipo' => 'Inspección',
                'fecha' => $item->fecha_reporte,
                'titulo' => $item->tipo_dano,
                'detalle' => $item->descripcion,
                'estado' => $item->dictamen ?: $item->estado,
                'responsable' => $item->inspector?->name ?: $item->inspector_responsable,
                'foto' => $item->evidencia_foto,
            ]))
            ->concat($unidad->aseos()->latest('fecha_hora')->take(15)->get()->map(fn ($item) => [
                'tipo' => 'Aseo',
                'fecha' => $item->fecha_hora,
                'titulo' => $item->tipo_limpieza ?: 'Proceso de limpieza',
                'detalle' => $item->observaciones ?: $item->producto_desinfectante,
                'estado' => $item->estado,
                'responsable' => $item->intendente_responsable,
                'foto' => $item->evidencia_foto,
            ]))
            ->concat($unidad->quejas()->with('operador')->latest()->take(15)->get()->map(fn ($item) => [
                'tipo' => 'Incidencia',
                'fecha' => $item->created_at,
                'titulo' => $item->tipo_queja,
                'detalle' => $item->descripcion,
                'estado' => $item->estado,
                'responsable' => $item->operador?->name,
                'foto' => $item->evidencia_foto,
            ]))
            ->concat($unidad->turnos()->with('operador')->latest('inicio')->take(15)->get()->map(fn ($item) => [
                'tipo' => 'Turno',
                'fecha' => $item->inicio,
                'titulo' => 'Turno ' . $item->estado,
                'detalle' => 'Odómetro: ' . ($item->odometro_inicial ?? '—') . ($item->odometro_final ? ' → ' . $item->odometro_final : ''),
                'estado' => $item->estado,
                'responsable' => $item->operador?->name,
                'foto' => null,
            ]))
            ->sortByDesc('fecha')
            ->take(40)
            ->values();

        return view('unidades.show', compact('unidad', 'eventos'));
    }

    public function create()
    {
        $operadores = \App\Models\User::where('role', 'operador')->where('activo', true)->orderBy('name')->get();

        return view('unidades.create', compact('operadores'));
    }

    public function store(UnidadStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['estado'] = $validated['estado'] ?? 'operativa';
        
        Unidad::create($validated);

        return redirect()->to(route('unidades.index', [], false))->with('success', 'Unidad creada correctamente');
    }

    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        $operadores = \App\Models\User::where('role', 'operador')->where('activo', true)->orderBy('name')->get();
        return view('unidades.edit', compact('unidad', 'operadores'));
    }

    public function update(UnidadUpdateRequest $request, $id)
    {
        $unidad = Unidad::findOrFail($id);
        $validated = $request->validated();
        $unidad->update($validated);

        return redirect()->to(route('unidades.index', [], false))->with('success', 'Unidad actualizada correctamente');
    }

    public function destroy(Request $request, $id)
    {
        // Validación de seguridad estricta para evitar que un operador elimine registros
        if (auth()->user()->role !== 'admin') {
            return redirect()->to(route('unidades.index', [], false))->with('error', 'No tienes permisos de administrador para eliminar unidades.');
        }

        $data = $request->validate(['motivo_baja' => ['required', 'string', 'max:1000']]);
        $unidad = Unidad::findOrFail($id);
        $unidad->update(['motivo_baja' => $data['motivo_baja'], 'operador_id' => null]);
        $unidad->delete();
        return redirect()->to(route('papelera.index', [], false))->with('success', 'Unidad enviada a Papelera. Su historial se conservó.');
    }
}
