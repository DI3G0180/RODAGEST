<?php

namespace App\Http\Controllers;

use App\Http\Requests\InspeccionStoreRequest;
use App\Models\Inspeccion;
use App\Models\Unidad;
use App\Models\User;
use App\Support\ProgressiveStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InspeccionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);
        $query = Inspeccion::with(['unidad.operador', 'operador', 'evidencias'])->latest();
        if ($request->filled('unidad_id')) $query->where('unidad_id', $request->integer('unidad_id'));
        if ($request->filled('desde')) $query->whereDate('fecha_reporte', '>=', $request->date('desde'));
        if ($request->filled('hasta')) $query->whereDate('fecha_reporte', '<=', $request->date('hasta'));
        if ($request->filled('estado')) $query->where('estado', $request->input('estado'));
        if ($request->filled('tipo_dano')) $query->where('tipo_dano', $request->input('tipo_dano'));
        $inspecciones = $query->paginate(12)->withQueryString();
        $unidades = Unidad::with('operador')->orderBy('economico')->get();
        $operadores = User::where('role', 'operador')->where('activo', true)->orderBy('name')->get(['id', 'name']);
        $estadisticas = [
            'hoy' => Inspeccion::whereDate('fecha_reporte', today())->count(),
            'pendientes' => Inspeccion::where('estado', 'pendiente')->count(),
            'criticas' => Inspeccion::where('prioridad', 'critica')->where('estado', '!=', 'resuelto')->count(),
        ];

        return view('inspecciones.index', compact('inspecciones', 'unidades', 'operadores', 'estadisticas'));
    }

    public function create()
    {
        return view('inspecciones.index', [
            'inspecciones' => Inspeccion::with('unidad')->latest()->paginate(12),
            'unidades' => Unidad::with('operador')->orderBy('economico')->get(),
            'operadores' => User::where('role', 'operador')->where('activo', true)->orderBy('name')->get(['id', 'name']),
            'estadisticas' => [
                'hoy' => Inspeccion::whereDate('fecha_reporte', today())->count(),
                'pendientes' => Inspeccion::where('estado', 'pendiente')->count(),
                'criticas' => Inspeccion::where('prioridad', 'critica')->where('estado', '!=', 'resuelto')->count(),
            ],
            'abrirModal' => true,
        ]);
    }

    public function store(InspeccionStoreRequest $request)
    {
        $data = $request->validated();
        $data['estado'] = 'pendiente';
        $data['inspector_id'] = $request->user()->id;
        $data['inspector_responsable'] = $request->user()->name;
        $unit = Unidad::with('operador')->findOrFail($data['unidad_id']);
        $data['operador_id'] = $data['operador_id'] ?? $unit->operador_id;
        $photoFiles = array_values(array_filter(array_merge(
            $request->file('fotos', []),
            [$request->file('evidencia_foto')]
        )));
        $videoFiles = $request->file('videos', []);
        unset($data['fotos'], $data['videos'], $data['evidencia_foto']);

        $storedPaths = [];
        try {
            $evidence = [];
            foreach ($photoFiles as $photo) {
                $path = $photo->store('inspecciones/evidencias', 'public');
                $storedPaths[] = $path;
                $evidence[] = ['ruta_archivo' => $path, 'tipo_media' => 'imagen', 'nombre_original' => $photo->getClientOriginalName()];
            }
            foreach ($videoFiles as $video) {
                $path = $video->store('inspecciones/evidencias', 'public');
                $storedPaths[] = $path;
                $evidence[] = ['ruta_archivo' => $path, 'tipo_media' => 'video', 'nombre_original' => $video->getClientOriginalName()];
            }

            $data['evidencia_foto'] = collect($evidence)->firstWhere('tipo_media', 'imagen')['ruta_archivo'] ?? null;
            $inspeccion = DB::transaction(function () use ($data, $evidence) {
                $inspeccion = Inspeccion::create($data);
                foreach ($evidence as $item) $inspeccion->evidencias()->create($item);
                Unidad::whereKey($inspeccion->unidad_id)->update(['estado' => $inspeccion->dictamen]);

                return $inspeccion;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }
        return $request->wantsJson()
            ? response()->json(['message' => 'Inspección registrada.', 'inspeccion' => $inspeccion->load('unidad', 'evidencias')], 201)
            : redirect()->to(route('inspecciones.index', [], false))->with('success', 'Inspección registrada correctamente.');
    }

    public function show(Inspeccion $inspeccion)
    {
        return view('inspecciones.show', ['inspeccion' => $inspeccion->load('unidad')]);
    }

    public function update(Request $request, Inspeccion $inspeccion)
    {
        $data = $request->validate(['estado' => ['required', 'in:pendiente,en_proceso,resuelto']]);
        abort_unless(ProgressiveStatus::next('inspeccion', $inspeccion->estado) === $data['estado'], 422, 'El estado solo puede avanzar a la siguiente etapa; los estados finales están bloqueados.');
        $inspeccion->update($data);
        if ($data['estado'] === 'resuelto' && $inspeccion->dictamen === 'operativa') {
            $inspeccion->unidad()->update(['estado' => 'operativa']);
        }
        return redirect()->to(route('inspecciones.index', [], false))->with('success', 'Estado actualizado.');
    }

    public function updateEstado(Request $request, Inspeccion $inspeccion)
    {
        return $this->update($request, $inspeccion);
    }

    public function cambiarEstado(Request $request, Inspeccion $inspeccion)
    {
        return $this->update($request, $inspeccion);
    }

    public function destroy(Inspeccion $inspeccion)
    {
        $inspeccion->loadMissing('evidencias');
        Storage::disk('public')->delete($inspeccion->evidencias->pluck('ruta_archivo')->push($inspeccion->evidencia_foto)->filter()->all());
        $inspeccion->delete();
        return redirect()->to(route('inspecciones.index', [], false))->with('success', 'Inspección eliminada.');
    }
}
