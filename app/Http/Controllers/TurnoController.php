<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\User;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Notifications\TurnoIniciadoConRequisitosPendientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TurnoController extends Controller
{
    private const FUEL_LEVELS = ['vacío', '1/4', '1/2', '3/4', 'lleno'];

    private const QUICK_CHECKS = [
        'luces' => 'Luces', 'frenos' => 'Frenos', 'llantas' => 'Llantas',
        'fluidos' => 'Niveles de fluidos', 'espejos_seguridad' => 'Espejos / seguridad',
    ];

    public function quickInspection(Request $request)
    {
        $operador = $request->user();
        $unidad = $operador->unidadAsignada;
        abort_unless($unidad && in_array($unidad->estado, ['operativa', 'activo'], true), 422, 'No tienes una unidad operativa asignada.');
        $data = $request->validate([
            'checklist' => ['nullable', 'array'],
            'checklist.luces' => ['nullable', 'boolean'], 'checklist.frenos' => ['nullable', 'boolean'],
            'checklist.llantas' => ['nullable', 'boolean'], 'checklist.fluidos' => ['nullable', 'boolean'],
            'checklist.espejos_seguridad' => ['nullable', 'boolean'],
            'observaciones' => ['required_if:hay_falla,1', 'nullable', 'string', 'max:500'],
            'hay_falla' => ['nullable', 'boolean'],
        ]);
        $checklist = collect(self::QUICK_CHECKS)->mapWithKeys(fn ($label, $key) => [$key => $request->boolean('checklist.'.$key)])->all();
        $fallas = collect($checklist)->filter(fn ($ok) => ! $ok)->keys()->map(fn ($key) => self::QUICK_CHECKS[$key])->all();
        $nota = trim((string) ($data['observaciones'] ?? ''));
        $hasFailure = count($fallas) > 0;
        if ($hasFailure && $nota === '') {
            return back()->withErrors(['observaciones' => 'Describe brevemente el punto que requiere atención.'])->withInput();
        }
        if (Inspeccion::where('unidad_id', $unidad->id)->whereDate('fecha_reporte', today())->exists()) {
            return back()->with('success', 'La inspección de hoy ya está registrada.');
        }

        $safetyFailure = $hasFailure && collect(['luces', 'frenos', 'llantas', 'espejos_seguridad'])
            ->contains(fn (string $key) => ! $checklist[$key]);

        $generatedReport = DB::transaction(function () use ($unidad, $operador, $checklist, $fallas, $nota, $hasFailure, $safetyFailure): ?Queja {
            $descripcion = $hasFailure
                ? 'Checklist rápido con hallazgo en: '.implode(', ', $fallas).'. '.$nota
                : 'Checklist rápido diario: todo OK, sin novedad.';
            Inspeccion::create([
                'unidad_id' => $unidad->id, 'operador_id' => $operador->id,
                'inspector_responsable' => $operador->name, 'tipo_dano' => $hasFailure ? 'Inspección rápida con hallazgo' : 'Checklist diario sin novedad',
                'prioridad' => $safetyFailure ? 'critica' : ($hasFailure ? 'media' : 'baja'), 'estado' => $hasFailure ? 'pendiente' : 'resuelto',
                'descripcion' => $descripcion, 'fecha_reporte' => now(), 'checklist' => $checklist,
                'dictamen' => $safetyFailure ? 'fuera_servicio' : 'operativa',
            ]);
            $report = null;
            if ($hasFailure) {
                $report = Queja::create([
                    'unidad_id' => $unidad->id, 'eco' => $unidad->economico, 'operador_id' => $operador->id,
                    'tipo_queja' => 'Falla mecánica', 'estado' => 'pendiente', 'urgencia' => $safetyFailure ? 'critica' : 'alta',
                    'descripcion' => $descripcion, 'comentarios' => $nota, 'medio_reporte' => 'interno',
                ]);
            }

            if ($safetyFailure) $unidad->update(['estado' => 'fuera_servicio']);

            return $report;
        });

        if ($generatedReport?->urgencia === 'critica') {
            app(\App\Support\OperationalMail::class)->criticalReport($generatedReport);
        }

        return back()->with('success', $safetyFailure
            ? 'Checklist guardado: se generó una alerta crítica y la unidad quedó fuera de servicio.'
            : ($hasFailure ? 'Checklist guardado; se generó un reporte de seguimiento.' : 'Checklist de hoy guardado: todo OK.'));
    }

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
