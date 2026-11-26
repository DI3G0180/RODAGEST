<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\User;
use App\Notifications\TurnoIniciadoConRequisitosPendientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        abort_unless(in_array($unidad->estado, ['operativa', 'activo'], true), 422, 'La unidad debe estar operativa para iniciar el turno.');

        $data = $request->validate([
            'odometro_inicio' => ['required', 'integer', 'min:0'],
            'combustible_inicio' => ['required', Rule::in(self::FUEL_LEVELS)],
        ]);
        if ($unidad->odometro !== null && $data['odometro_inicio'] < $unidad->odometro) {
            return back()->withErrors(['odometro_inicio' => 'El odómetro inicial no puede ser menor al último registro.'])->withInput();
        }

        $inspeccionHoy = \App\Models\Inspeccion::where('unidad_id', $unidad->id)->whereDate('fecha_reporte', today())->exists();
        $aseoHoy = \App\Models\Aseo::where('unidad_id', $unidad->id)->whereDate('fecha_hora', today())->exists();
        $pendientes = array_values(array_filter([
            ! $inspeccionHoy ? 'Inspección de hoy' : null,
            ! $aseoHoy ? 'Aseo completado hoy' : null,
        ]));
        $observacion = $pendientes
            ? 'Turno iniciado con requisitos pendientes: '.implode(', ', $pendientes).'.'
            : null;

        DB::transaction(function () use ($operador, $unidad, $data, $observacion, $pendientes): void {
            $operador->turnos()->create([
                'unidad_id' => $unidad->id,
                'odometro_inicial' => $data['odometro_inicio'],
                'combustible_inicial' => $data['combustible_inicio'],
                'inicio' => now(),
                'estado' => 'abierto',
                'observaciones' => $observacion,
            ]);

            $unidad->update(['odometro' => $data['odometro_inicio']]);

            if ($pendientes) {
                User::where('role', 'admin')->where('activo', true)->each(
                    fn (User $admin) => $admin->notify(new TurnoIniciadoConRequisitosPendientes($unidad, $operador, $pendientes))
                );
            }
        });
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
