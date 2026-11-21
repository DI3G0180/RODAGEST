<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TurnoController extends Controller
{
    private const FUEL_LEVELS = ['vacío', '1/4', '1/2', '3/4', 'lleno'];

    public function store(Request $request)
    {
        $operador = $request->user();
        $unidad = $operador->unidadAsignada;
        abort_unless($unidad, 422, 'No tienes una unidad asignada para iniciar turno.');
        abort_if($operador->turnos()->where('estado', 'abierto')->exists(), 422, 'Ya tienes un turno abierto.');

        $data = $request->validate([
            'odometro_inicio' => ['required', 'integer', 'min:0'],
            'combustible_inicio' => ['required', Rule::in(self::FUEL_LEVELS)],
        ]);
        if ($unidad->odometro !== null && $data['odometro_inicio'] < $unidad->odometro) {
            return back()->withErrors(['odometro_inicio' => 'El odómetro inicial no puede ser menor al último registro.'])->withInput();
        }

        $operador->turnos()->create([
            'unidad_id' => $unidad->id,
            'odometro_inicial' => $data['odometro_inicio'],
            'combustible_inicial' => $data['combustible_inicio'],
            'inicio' => now(),
            'estado' => 'abierto',
        ]);

        $unidad->update(['odometro' => $data['odometro_inicio']]);
        return back()->with('success', 'Turno iniciado. ¡Buen recorrido!');
    }

    public function close(Request $request, Turno $turno)
    {
        abort_unless($turno->operador_id === $request->user()->id && $turno->estado === 'abierto', 404);
        $data = $request->validate([
            'odometro_fin' => ['required', 'integer', 'min:'.$turno->odometro_inicial],
            'combustible_fin' => ['required', Rule::in(self::FUEL_LEVELS)],
        ]);

        $turno->update(['odometro_final' => $data['odometro_fin'], 'combustible_final' => $data['combustible_fin'], 'fin' => now(), 'estado' => 'cerrado']);
        $turno->unidad()->update(['odometro' => $data['odometro_fin']]);
        return back()->with('success', 'Turno cerrado y odómetro actualizado.');
    }
}
