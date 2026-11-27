<?php

namespace App\Http\Controllers;

use App\Models\Queja;
use App\Models\Unidad;
use App\Support\OperationalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuejaController extends Controller
{
    public function create(Request $request)
    {
        abort_if($request->user()->role === 'admin', 403, 'La cuenta administradora solo puede consultar y dar seguimiento a quejas.');

        return redirect()->to(route('quejas.index', ['crear' => 1], false));
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'estado' => ['nullable', 'in:pendiente,en_proceso,resuelto'],
            'urgencia' => ['nullable', 'in:normal,alta,critica'],
        ]);
        $query = Queja::with(['unidad', 'operador']);

        if ($request->user()->role === 'operador') {
            $query->where('operador_id', $request->user()->id);
        } elseif ($request->user()->role === 'limpieza') {
            $query->where('tipo_queja', 'Limpieza');
        }

        $query->when($filters['q'] ?? null, fn ($builder, $term) => $builder->where(fn ($sub) => $sub
            ->where('eco', 'like', "%{$term}%")
            ->orWhere('area', 'like', "%{$term}%")
            ->orWhere('descripcion', 'like', "%{$term}%")
            ->orWhereHas('unidad', fn ($unit) => $unit->where('economico', 'like', "%{$term}%"))))
            ->when($filters['estado'] ?? null, fn ($builder, $state) => $builder->where('estado', $state))
            ->when($filters['urgencia'] ?? null, fn ($builder, $urgency) => $builder->where('urgencia', $urgency))
            ->when($filters['desde'] ?? null, fn ($builder, $date) => $builder->whereDate('created_at', '>=', $date))
            ->when($filters['hasta'] ?? null, fn ($builder, $date) => $builder->whereDate('created_at', '<=', $date));

        // Filtrar por unidad si se especifica
        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        // Filtrar por operador si se especifica
        if ($request->filled('operador_id')) {
            $query->where('operador_id', $request->operador_id);
        }

        $quejas = $query->latest()->paginate(10);

        $unidades = $request->user()->role === 'operador'
            ? $request->user()->unidadesOperadas()->get()
            : Unidad::orderBy('economico')->get();

        return view('quejas.index', compact('quejas', 'unidades', 'filters'));
    }

    public function show(Request $request, Queja $queja)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['admin', 'inspector', 'operador', 'limpieza'], true), 403);
        abort_if($user->role === 'operador' && (int) $queja->operador_id !== (int) $user->id, 404);
        abort_if($user->role === 'limpieza' && $queja->tipo_queja !== 'Limpieza', 404);

        $queja->load(['unidad', 'operador']);

        return view('quejas.show', compact('queja'));
    }

    public function store(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['inspector', 'operador', 'limpieza'], true), 403, 'Este rol no puede crear quejas internas.');

        $data = $request->validate([
            'eco' => ['required', 'string', 'max:30', Rule::exists('unidades', 'economico')],
            'area' => ['required', 'string', 'max:80'],
            'ruta_reportada' => ['nullable', 'string', 'max:120'],
            'tipo_queja' => ['required', 'in:Limpieza,Falla mecánica,Incidencia en ruta,Daño o seguridad,Otro'],
            'urgencia' => ['required', 'in:normal,alta,critica'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'evidencia_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $unit = Unidad::where('economico', $data['eco'])->firstOrFail();
        if ($request->user()->role === 'operador' && $unit->operador_id !== $request->user()->id) {
            abort(403, 'Como operador solo puedes reportar sobre tu unidad asignada.');
        }

        $photoPath = $request->file('evidencia_foto')?->store('quejas/internas', 'public');
        try {
            $queja = Queja::create([
                'unidad_id' => $unit->id,
                'eco' => $data['eco'],
                'area' => $data['area'],
                'area_reportada' => $data['area'],
                'ruta_reportada' => $data['ruta_reportada'] ?? null,
                'operador_id' => $request->user()->role === 'operador' ? $request->user()->id : null,
                'tipo_queja' => $data['tipo_queja'],
                'urgencia' => $data['urgencia'],
                'descripcion' => $data['descripcion'],
                'evidencia_foto' => $photoPath,
                'estado' => 'pendiente',
                'medio_reporte' => 'interno',
            ]);
        } catch (\Throwable $exception) {
            if ($photoPath) Storage::disk('public')->delete($photoPath);
            throw $exception;
        }

        if ($queja->urgencia === 'critica') {
            if (in_array($queja->tipo_queja, ['Falla mecánica', 'Daño o seguridad'], true)) {
                $unit->update(['estado' => 'fuera_servicio']);
            }
            app(OperationalMail::class)->criticalReport($queja);
        }

        return redirect()->to(route('quejas.index', [], false))->with('success', 'Reporte guardado correctamente.');
    }

    public function updateEstado(Request $request, $id)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'inspector'], true), 403);
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto',
        ]);

        $queja = Queja::findOrFail($id);
        $queja->update(['estado' => $request->estado]);
        app(OperationalMail::class)->reportStatusChanged($queja);

        return redirect()->to(route('quejas.index', [], false))->with('success', 'Estado de queja actualizado correctamente.');
    }

    public function accionLimpieza(Request $request, Queja $queja)
    {
        abort_unless($request->user()->role === 'limpieza', 403);
        abort_unless($queja->tipo_queja === 'Limpieza', 404);

        $data = $request->validate([
            'accion_atencion' => ['required', 'string', 'max:1000'],
            'evidencia_atencion' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'accion_atencion.required' => 'Describe qué trabajo realizó el equipo de limpieza.',
            'evidencia_atencion.required' => 'Adjunta una foto del trabajo terminado.',
            'evidencia_atencion.image' => 'La evidencia debe ser una imagen válida.',
            'evidencia_atencion.max' => 'La foto debe pesar menos de 5 MB.',
        ]);

        $newPhoto = $request->file('evidencia_atencion')->store('quejas/atenciones', 'public');
        $oldPhoto = $queja->evidencia_atencion;
        $queja->update([
            'accion_atencion' => $data['accion_atencion'],
            'evidencia_atencion' => $newPhoto,
            'estado' => 'resuelto',
        ]);
        if ($oldPhoto) Storage::disk('public')->delete($oldPhoto);

        return redirect()->to(route('quejas.index', [], false))->with('success', 'Acción de limpieza guardada y visible en el seguimiento externo.');
    }

    public function destroy(Request $request, Queja $queja)
    {
        abort_unless($request->user()->role === 'operador' && $queja->operador_id === $request->user()->id, 403);
        if ($queja->evidencia_foto) Storage::disk('public')->delete($queja->evidencia_foto);
        if ($queja->evidencia_atencion) Storage::disk('public')->delete($queja->evidencia_atencion);
        $queja->delete();
        return redirect()->to(route('quejas.index', [], false))->with('success', 'Reporte eliminado.');
    }
}
