<?php

namespace App\Http\Controllers;

use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Turno;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $unidadAsignada = Unidad::where('operador_id', $user->id)->with('operador')->first();
        $unidades = Unidad::with(['operador', 'aseos' => fn ($query) => $query->latest('fecha_hora')->take(1)])
            ->orderBy('economico')->get();
        $empleados = User::with('unidadAsignada')->orderBy('name')->get();
        $inspecciones = Inspeccion::with('unidad')->latest()->take(8)->get();
        $aseos = Aseo::with('unidad')->latest()->take(8)->get();
        $unidadesLimpieza = Unidad::whereIn('estado', ['operativa', 'activo'])->orderBy('economico')->get();
        $aseoCompletadoHoyUnidadIds = Aseo::whereDate('fecha_hora', today())->where('estado', 'completado')->pluck('unidad_id')->unique()->values();
        $reportes = Queja::with('unidad')->where('operador_id', $user->id)->latest()->take(8)->get();
        $turnoAbierto = $user->role === 'operador'
            ? Turno::with('unidad')->where('operador_id', $user->id)->where('estado', 'abierto')->latest('inicio')->first()
            : null;
        // La inspección y el aseo son requisitos de registro diario por unidad,
        // independientemente del usuario o del estado histórico con que se guardaron.
        $inspeccionAprobadaHoy = (bool) ($unidadAsignada && Inspeccion::where('unidad_id', $unidadAsignada->id)
            ->whereDate('fecha_reporte', today())->exists());
        $aseoCompletadoHoy = (bool) ($unidadAsignada && Aseo::where('unidad_id', $unidadAsignada->id)
            ->whereDate('fecha_hora', today())->exists());
        $unidadOperativa = (bool) ($unidadAsignada && in_array($unidadAsignada->estado, ['operativa', 'activo'], true));
        $estadoFlota = [
            'operativa' => Unidad::whereIn('estado', ['operativa', 'activo'])->count(),
            'en_taller' => Unidad::whereIn('estado', ['en_taller', 'mantenimiento'])->count(),
            'en_limpieza' => Unidad::where('estado', 'en_limpieza')->count(),
            'fuera_servicio' => Unidad::where('estado', 'fuera_servicio')->count(),
        ];

        $serviciosProximos = $unidades->filter(function (Unidad $unidad) {
            $fechaCercana = $unidad->proximo_mantenimiento_fecha
                && $unidad->proximo_mantenimiento_fecha->lte(today()->addDays(14));
            $odometroCercano = $unidad->proximo_mantenimiento_odometro !== null
                && $unidad->odometro !== null
                && $unidad->proximo_mantenimiento_odometro <= $unidad->odometro + 500;

            return $fechaCercana || $odometroCercano;
        })->values();

        $alertas = [];
        $inspeccionesPendientes = Inspeccion::where('estado', 'pendiente')->count();
        $reportesAbiertos = Queja::whereIn('estado', ['pendiente', 'en_proceso'])->count();
        $quejasLimpiezaPendientes = Queja::where('tipo_queja', 'Limpieza')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->count();
        $unidadesEnLimpieza = Unidad::where('estado', 'en_limpieza')->count();

        if ($user->role === 'inspector' && $inspeccionesPendientes > 0) {
            $alertas[] = ['nivel' => 'warning', 'titulo' => $inspeccionesPendientes . ' inspecciones requieren seguimiento', 'detalle' => 'Revisa dictámenes y pendientes de la flota.', 'ruta' => route('inspecciones.index', [], false)];
        }

        if (in_array($user->role, ['admin', 'inspector'], true) && $reportesAbiertos > 0) {
            $alertas[] = ['nivel' => 'warning', 'titulo' => $reportesAbiertos . ' reportes están abiertos', 'detalle' => 'Da seguimiento a las incidencias reportadas en ruta.', 'ruta' => route('quejas.index', [], false)];
        }

        if ($user->role === 'limpieza' && $quejasLimpiezaPendientes > 0) {
            $alertas[] = ['nivel' => 'warning', 'titulo' => $quejasLimpiezaPendientes . ' reportes de limpieza requieren atención', 'detalle' => 'Registra la acción realizada y adjunta una foto para actualizar el seguimiento.', 'ruta' => route('quejas.index', [], false)];
        }

        if (in_array($user->role, ['admin', 'limpieza'], true) && $unidadesEnLimpieza > 0) {
            $alertas[] = ['nivel' => 'info', 'titulo' => $unidadesEnLimpieza . ' unidades están en limpieza', 'detalle' => 'Consulta el avance de sus procesos de aseo.', 'ruta' => route($user->role === 'admin' ? 'reportes.index' : 'aseos.index', [], false)];
        }

        $serviciosVisibles = $user->role === 'operador'
            ? $serviciosProximos->where('operador_id', $user->id)
            : ($user->role === 'admin' ? $serviciosProximos : collect());

        foreach ($serviciosVisibles as $unidadServicio) {
            $alertas[] = [
                'nivel' => 'warning',
                'titulo' => 'Servicio preventivo próximo · ' . $unidadServicio->economico,
                'detalle' => implode(' · ', array_filter([
                    $unidadServicio->proximo_mantenimiento_fecha?->format('d/m/Y'),
                    $unidadServicio->proximo_mantenimiento_odometro ? number_format($unidadServicio->proximo_mantenimiento_odometro) . ' km' : null,
                ])),
                'ruta' => route('unidades.show', [$unidadServicio->id], false),
            ];
        }

        if ($user->role === 'operador' && $unidadAsignada && ! in_array($unidadAsignada->estado, ['operativa', 'activo'], true)) {
            $estadoActual = ['en_taller' => 'En taller', 'en_limpieza' => 'En limpieza', 'fuera_servicio' => 'Fuera de servicio', 'fuera_de_servicio' => 'Fuera de servicio'][$unidadAsignada->estado] ?? ucfirst(str_replace('_', ' ', $unidadAsignada->estado));
            $alertas[] = ['nivel' => 'danger', 'titulo' => 'Tu unidad no está liberada', 'detalle' => 'Estado actual: ' . $estadoActual, 'ruta' => route('unidades.show', [$unidadAsignada->id], false)];
        }

        return view('dashboard', [
            'user' => $user,
            'userRole' => $user->role,
            'unidadAsignada' => $unidadAsignada,
            'unidades' => $unidades,
            'empleados' => $empleados,
            'inspecciones' => $inspecciones,
            'aseos' => $aseos,
            'unidadesLimpieza' => $unidadesLimpieza,
            'aseoCompletadoHoyUnidadIds' => $aseoCompletadoHoyUnidadIds,
            'reportes' => $reportes,
            'turnoAbierto' => $turnoAbierto,
            'inspeccionAprobadaHoy' => $inspeccionAprobadaHoy,
            'aseoCompletadoHoy' => $aseoCompletadoHoy,
            'unidadOperativa' => $unidadOperativa,
            'liberacionUnidad' => (bool) ($unidadAsignada && $inspeccionAprobadaHoy && $aseoCompletadoHoy && $unidadOperativa),
            'estadoFlota' => $estadoFlota,
            'alertas' => $alertas,
            'kpis' => [
                'unidades' => Unidad::count(),
                'empleados' => User::where('activo', true)->count(),
                'inspeccionesHoy' => Inspeccion::whereDate('fecha_reporte', today())->count(),
                'aseosCompletados' => Aseo::where('estado', 'completado')->count(),
                'aseosHoy' => Aseo::whereDate('fecha_hora', today())->where('estado', 'completado')->count(),
                'aseosPendientes' => Aseo::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                'inspeccionesPendientes' => Inspeccion::where('estado', 'pendiente')->count(),
                'inspeccionesCriticas' => Inspeccion::where('prioridad', 'critica')->whereNotIn('estado', ['resuelto', 'reparada'])->count(),
                'reportesAbiertos' => Queja::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                'reportesOperador' => Queja::where('operador_id', $user->id)->count(),
                'reportesPendientesOperador' => Queja::where('operador_id', $user->id)->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                'recorridosHoy' => Turno::where('operador_id', $user->id)->whereDate('inicio', today())->count(),
                'reportesLimpiezaPendientes' => $quejasLimpiezaPendientes,
            ],
        ]);
    }
}
