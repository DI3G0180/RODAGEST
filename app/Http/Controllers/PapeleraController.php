<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\User;

class PapeleraController extends Controller
{
    public function index()
    {
        return view('papelera.index', [
            'unidades' => Unidad::onlyTrashed()->with(['operador'])->latest('deleted_at')->paginate(10, ['*'], 'unidades'),
            'empleados' => User::onlyTrashed()->where('role', '!=', 'admin')->latest('deleted_at')->paginate(10, ['*'], 'empleados'),
        ]);
    }

    public function restoreUnit(int $id)
    {
        $unit = Unidad::onlyTrashed()->findOrFail($id);
        $unit->forceFill(['motivo_baja' => null])->save();
        $unit->restore();

        return back()->with('success', 'Unidad restaurada. Revisa su estado y asignación antes de volver a operarla.');
    }

    public function restoreEmployee(int $id)
    {
        $employee = User::onlyTrashed()->where('role', '!=', 'admin')->findOrFail($id);
        $employee->restore();
        $employee->forceFill(['activo' => true, 'motivo_baja' => null])->save();

        return back()->with('success', 'Empleado restaurado. La contraseña y el acceso se conservan como estaban antes de la baja.');
    }
}
