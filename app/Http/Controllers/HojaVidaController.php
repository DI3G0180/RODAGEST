<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Http\Request;

class HojaVidaController extends Controller
{
    public function own(Request $request)
    {
        abort_unless($request->user()->role === 'operador', 403);

        return $this->show($request->user(), false);
    }

    public function showEmployee(User $empleado)
    {
        return $this->show($empleado, true);
    }

    public function update(Request $request, User $empleado)
    {
        $data = $request->validate([
            'curp' => ['nullable', 'string', 'max:18'],
            'rfc' => ['nullable', 'string', 'max:13'],
            'nss' => ['nullable', 'string', 'max:20'],
            'escolaridad' => ['nullable', 'string', 'max:120'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:150'],
            'estado_civil' => ['nullable', 'string', 'max:80'],
            'nombre_padre' => ['nullable', 'string', 'max:180'],
            'nombre_madre' => ['nullable', 'string', 'max:180'],
            'nombre_conyuge' => ['nullable', 'string', 'max:180'],
            'identificacion' => ['nullable', 'string', 'max:30'],
            'calle' => ['nullable', 'string', 'max:180'],
            'numero' => ['nullable', 'string', 'max:40'],
            'colonia' => ['nullable', 'string', 'max:150'],
            'municipio' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'string', 'max:100'],
            'codigo_postal' => ['nullable', 'string', 'max:12'],
            'telefono_alterno' => ['nullable', 'string', 'max:30'],
            'estatus_imss' => ['nullable', 'string', 'max:80'],
            'tipo_ingreso' => ['nullable', 'string', 'max:80'],
            'fecha_reingreso' => ['nullable', 'date'],
            'licencia' => ['nullable', 'string', 'max:80'],
            'tarjeton' => ['nullable', 'string', 'max:80'],
        ]);

        $empleado->employeeProfile()->updateOrCreate([], $data);

        return redirect()->to(route('empleados.hoja-vida', [$empleado->id], false))
            ->with('success', 'Hoja de vida actualizada. Los datos personales quedan cifrados en la base de datos.');
    }

    private function show(User $empleado, bool $editable)
    {
        $empleado->loadMissing(['employeeProfile', 'unidadAsignada']);
        $perfil = $empleado->employeeProfile ?? new EmployeeProfile();
        $eventos = collect();
        if ($editable) {
            $eventos = $eventos
                ->concat(Inspeccion::with('unidad')->where(fn ($query) => $query->where('inspector_id', $empleado->id)->orWhere('operador_id', $empleado->id))->latest('fecha_reporte')->take(20)->get()->map(fn ($item) => ['tipo' => 'Inspección', 'fecha' => $item->fecha_reporte, 'unidad' => $item->unidad?->etiqueta_economico, 'detalle' => $item->tipo_dano.' · '.($item->estado ?? '')]))
                ->concat(Aseo::with('unidad')->where('intendente_responsable', $empleado->name)->latest('fecha_hora')->take(20)->get()->map(fn ($item) => ['tipo' => 'Aseo', 'fecha' => $item->fecha_hora, 'unidad' => $item->unidad?->etiqueta_economico, 'detalle' => ($item->tipo_limpieza ?: 'Limpieza').' · '.$item->estado]))
                ->concat(Queja::with('unidad')->where('operador_id', $empleado->id)->latest()->take(20)->get()->map(fn ($item) => ['tipo' => 'Reporte', 'fecha' => $item->created_at, 'unidad' => $item->eco ?: $item->unidad?->etiqueta_economico, 'detalle' => $item->tipo_queja.' · '.$item->estado]))
                ->sortByDesc('fecha')->take(40)->values();
        }

        return view('empleados.hoja-de-vida', compact('empleado', 'perfil', 'editable', 'eventos'));
    }
}
