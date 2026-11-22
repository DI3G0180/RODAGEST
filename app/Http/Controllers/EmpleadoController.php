<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $term = trim($request->string('q')->toString());
        $empleados = User::with('unidadAsignada')->when($term !== '', function ($query) use ($term) {
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('puesto', 'like', "%{$term}%");
                if (ctype_digit($term)) $builder->orWhereKey((int) $term);
            });
        })->orderBy('name')->paginate(12)->withQueryString();
        $unidades = Unidad::orderBy('economico')->get();
        return view('empleados.index', compact('empleados', 'unidades'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $unidadId = $data['unidad_id'] ?? null;
        unset($data['unidad_id']);
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('foto')) $data['foto'] = $request->file('foto')->store('empleados', 'public');
        $empleado = User::create($data);
        $this->assignUnit($empleado, $unidadId);

        return redirect()->to(route('empleados.index', [], false))->with('success', 'Empleado registrado correctamente.');
    }

    public function update(Request $request, User $empleado)
    {
        abort_if($empleado->is($request->user()), 403, 'No puedes cambiar tu propio estado desde esta pantalla.');
        $data = $this->validated($request, $empleado);
        $unidadId = $data['unidad_id'] ?? null;
        unset($data['unidad_id']);
        if (empty($data['password'])) unset($data['password']);
        else $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('foto')) {
            if ($empleado->foto) Storage::disk('public')->delete($empleado->foto);
            $data['foto'] = $request->file('foto')->store('empleados', 'public');
        }
        $empleado->update($data);
        $this->assignUnit($empleado, $unidadId);

        return redirect()->to(route('empleados.index', [], false))->with('success', 'Empleado actualizado correctamente.');
    }

    private function validated(Request $request, ?User $empleado = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($empleado?->id)],
            'role' => [$empleado ? 'sometimes' : 'required', Rule::in(['inspector', 'operador', 'limpieza'])],
            'puesto' => [$empleado ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'fecha_ingreso' => ['nullable', 'date'],
            'activo' => ['nullable', 'boolean'],
            'unidad_id' => ['nullable', 'exists:unidades,id'],
            'password' => [$empleado ? 'nullable' : 'required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function assignUnit(User $empleado, ?int $unidadId): void
    {
        // La base de datos aplica el valor por defecto de "activo" al insertar;
        // recargar evita evaluar como null el atributo que aún no estaba en memoria.
        $empleado->refresh();
        Unidad::where('operador_id', $empleado->id)->update(['operador_id' => null]);
        if ($unidadId && $empleado->role === 'operador' && $empleado->activo) {
            Unidad::whereKey($unidadId)->update(['operador_id' => $empleado->id]);
        }
    }
}
