<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\User;
use Illuminate\Http\Request;

class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $term = trim($filters['q'] ?? '');
        $unidades = Unidad::onlyTrashed()->with(['operador'])->latest('deleted_at');
        $empleados = User::onlyTrashed()->where('role', '!=', 'admin')->latest('deleted_at');

        if ($term !== '') {
            $unidades->where(fn ($query) => $query->where('economico', 'like', "%{$term}%")
                ->orWhere('motivo_baja', 'like', "%{$term}%"));
            $empleados->where(fn ($query) => $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('motivo_baja', 'like', "%{$term}%"));
        }

        return view('papelera.index', [
            'unidades' => $unidades->paginate(10, ['*'], 'unidades')->withQueryString(),
            'empleados' => $empleados->paginate(10, ['*'], 'empleados')->withQueryString(),
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
