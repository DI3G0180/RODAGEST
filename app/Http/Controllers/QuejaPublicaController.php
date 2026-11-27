<?php

namespace App\Http\Controllers;

use App\Models\Queja;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuejaPublicaController extends Controller
{
    public function preview()
    {
        return view('quejas.publica', [
            'eco' => null,
            'preview' => true,
        ]);
    }

    public function create(Request $request)
    {
        abort_if($request->user()?->role === 'admin', 403, 'La cuenta administradora no puede crear reportes.');

        $eco = $request->query('eco');
        $unidadId = $request->query('unidad_id');
        if ($eco === null && $unidadId !== null) {
            if (! is_string($unidadId) || ! ctype_digit($unidadId)) {
                abort(404);
            }
            $eco = Unidad::whereKey($unidadId)->value('economico');
            abort_unless($eco, 404);
        }
        if ($eco !== null && (! is_string($eco) || ! Unidad::where('economico', $eco)->exists())) {
            abort(404);
        }

        return view('quejas.publica', [
            'eco' => $eco,
            'preview' => false,
        ]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()?->role === 'admin', 403, 'La cuenta administradora no puede crear reportes.');

        $data = $request->validate([
            'eco' => ['required', 'string', 'max:30', 'exists:unidades,economico'],
            'ruta_reportada' => ['required', 'string', 'max:120'],
            'area' => ['required', 'string', 'max:80'],
            'tipo_queja' => ['required', 'in:Limpieza,Falla mecánica,Incidencia en ruta,Daño o seguridad,Otro'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'nombre_reportante' => ['nullable', 'string', 'max:120'],
            'evidencia_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sitio_web' => ['prohibited'],
        ], [
            'eco.required' => 'Escribe el número económico de la unidad.',
            'eco.exists' => 'El número económico no corresponde a una unidad registrada.',
            'area.required' => 'Indica el área del reporte.',
            'ruta_reportada.required' => 'Indica la ruta o terminal donde ocurrió.',
            'tipo_queja.required' => 'Selecciona el tipo de queja.',
            'descripcion.required' => 'Describe brevemente lo ocurrido.',
            'evidencia_foto.image' => 'Adjunta una imagen válida.',
            'evidencia_foto.max' => 'La foto debe pesar menos de 5 MB.',
        ]);
        unset($data['sitio_web']);
        $unit = Unidad::where('economico', $data['eco'])->firstOrFail();
        $data['unidad_id'] = $unit->id;
        $data['area_reportada'] = $data['area'];

        $photoPath = $request->hasFile('evidencia_foto')
            ? $request->file('evidencia_foto')->store('quejas/publicas', 'public')
            : null;
        $data['evidencia_foto'] = $photoPath;

        try {
            $queja = Queja::create([
                ...$data,
                'operador_id' => null,
                'medio_reporte' => 'qr',
                'urgencia' => 'normal',
                'estado' => 'pendiente',
                'codigo_seguimiento' => Str::upper(Str::random(24)),
            ]);
        } catch (\Throwable $exception) {
            if ($photoPath) Storage::disk('public')->delete($photoPath);
            throw $exception;
        }

        return redirect()->to(route('quejas.seguimiento', [$queja->codigo_seguimiento], false));
    }

    public function seguimiento(string $codigo)
    {
        $queja = Queja::with('unidad')->where('codigo_seguimiento', $codigo)->firstOrFail();

        return view('quejas.seguimiento', compact('queja'));
    }

    public function evidencia(string $codigo, string $tipo)
    {
        abort_unless(in_array($tipo, ['reporte', 'atencion'], true), 404);
        $queja = Queja::where('codigo_seguimiento', $codigo)->firstOrFail();
        $path = $tipo === 'reporte' ? $queja->evidencia_foto : $queja->evidencia_atencion;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    public function qr()
    {
        $publicUrl = rtrim(config('app.public_url'), '/') . route('quejas.publica', [], false);

        return view('quejas.qr-publico', compact('publicUrl'));
    }
}
