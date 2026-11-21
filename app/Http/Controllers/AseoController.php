<?php

namespace App\Http\Controllers;

use App\Models\Aseo;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AseoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);
        $query = Aseo::with('unidad')->latest();
        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(function ($builder) use ($term) {
                $builder->where('intendente_responsable', 'like', "%{$term}%")
                    ->orWhere('area_terminal', 'like', "%{$term}%")
                    ->orWhereHas('unidad', fn ($unit) => $unit->where('economico', 'like', "%{$term}%"));
            });
        }
        if ($request->filled('desde')) $query->whereDate('fecha_hora', '>=', $request->date('desde'));
        if ($request->filled('hasta')) $query->whereDate('fecha_hora', '<=', $request->date('hasta'));
        $aseos = $query->paginate(12)->withQueryString();
        $unidades = Unidad::orderBy('economico')->get();
        return view('aseos.index', compact('aseos', 'unidades'));
    }

    public function create()
    {
        return view('aseos.index', [
            'aseos' => Aseo::with('unidad')->latest()->paginate(12),
            'unidades' => Unidad::orderBy('economico')->get(),
            'abrirModal' => true,
        ]);
    }

    public function store(Request $request)
    {
        $stageKeys = ['barrido', 'pasamanos_asientos', 'cabina_exterior', 'nebulizacion'];
        $request->merge(['etapas' => array_merge(array_fill_keys($stageKeys, false), $request->input('etapas', []))]);
        $data = $request->validate([
            'unidad_id' => ['required', 'exists:unidades,id'],
            'tipo_limpieza' => ['required', 'string', 'max:100'],
            'intendente_responsable' => ['nullable', 'string', 'max:150'],
            'area_terminal' => ['nullable', 'string', 'max:150'],
            'fecha_hora' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'producto_desinfectante' => ['required', 'string', 'max:150'],
            'etapas' => ['required', 'array'],
            'etapas.barrido' => ['required', 'boolean'],
            'etapas.pasamanos_asientos' => ['required', 'boolean'],
            'etapas.cabina_exterior' => ['required', 'boolean'],
            'etapas.nebulizacion' => ['required', 'boolean'],
            'lista_ruta' => ['nullable', 'boolean'],
            'evidencia_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $allStagesDone = collect($data['etapas'])->every(fn ($done) => (bool) $done);
        $listaRuta = $request->boolean('lista_ruta');
        if ($listaRuta && !$allStagesDone) {
            return back()->withErrors(['etapas' => 'Completa las cuatro etapas antes de liberar la unidad.'])->withInput();
        }
        $data['intendente_responsable'] = $data['intendente_responsable'] ?: $request->user()->name;
        $data['area_terminal'] = $data['area_terminal'] ?? 'Patio';
        $data['estado'] = $allStagesDone ? 'completado' : 'en_proceso';
        // Compatibilidad con las columnas históricas del registro de aseos.
        $data['comentarios'] = $data['observaciones'] ?? null;
        if ($request->hasFile('evidencia_foto')) $data['evidencia_foto'] = $request->file('evidencia_foto')->store('aseos', 'public');
        unset($data['lista_ruta']);
        Aseo::create($data);

        Unidad::whereKey($data['unidad_id'])->update(['estado' => $listaRuta && $allStagesDone ? 'operativa' : 'en_limpieza']);

        return redirect()->to(route('aseos.index', [], false))->with('success', 'Registro de limpieza guardado.');
    }

    public function update(Request $request, Aseo $aseo)
    {
        $data = $request->validate([
            'tipo_limpieza' => ['required', 'string', 'max:100'],
            'intendente_responsable' => ['required', 'string', 'max:150'],
            'area_terminal' => ['required', 'string', 'max:150'],
            'fecha_hora' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'producto_desinfectante' => ['nullable', 'string', 'max:150'],
            'evidencia_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $oldPhoto = $aseo->evidencia_foto;
        if ($request->hasFile('evidencia_foto')) {
            $data['evidencia_foto'] = $request->file('evidencia_foto')->store('aseos', 'public');
        }
        $data['comentarios'] = $data['observaciones'] ?? null;
        $aseo->update($data);
        if ($request->hasFile('evidencia_foto') && $oldPhoto) Storage::disk('public')->delete($oldPhoto);

        return redirect()->to(route('aseos.index', [], false))->with('success', 'Registro de aseo actualizado.');
    }

    public function updateEstado(Request $request, Aseo $aseo)
    {
        $data = $request->validate(['estado' => ['required', 'in:pendiente,en_proceso,completado']]);
        if ($data['estado'] === 'completado' && !collect($aseo->etapas ?? [])->every(fn ($done) => (bool) $done)) {
            return back()->withErrors(['estado' => 'Completa las cuatro etapas de limpieza antes de liberar la unidad.']);
        }
        $aseo->update($data);
        $aseo->unidad()->update(['estado' => $data['estado'] === 'completado' ? 'operativa' : 'en_limpieza']);
        return redirect()->to(route('aseos.index', [], false))->with('success', 'Estado de limpieza actualizado.');
    }

    public function destroy(Aseo $aseo)
    {
        if ($aseo->evidencia_foto) Storage::disk('public')->delete($aseo->evidencia_foto);
        $aseo->delete();
        return redirect()->to(route('aseos.index', [], false))->with('success', 'Registro de aseo eliminado.');
    }
}
