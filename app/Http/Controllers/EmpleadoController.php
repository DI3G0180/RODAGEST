<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Support\OperationalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'role' => ['nullable', 'in:admin,inspector,operador,limpieza'],
            'activo' => ['nullable', 'in:1,0'],
        ]);
        $term = trim($request->string('q')->toString());
        $empleados = User::with('unidadAsignada')->when($term !== '', function ($query) use ($term) {
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('puesto', 'like', "%{$term}%");
                if (ctype_digit($term)) $builder->orWhereKey((int) $term);
            });
        })->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(array_key_exists('activo', $filters), fn ($query) => $query->where('activo', (bool) $filters['activo']))
            ->orderBy('name')->paginate(12)->withQueryString();
        $unidades = Unidad::orderBy('economico')->get();
        return view('empleados.index', compact('empleados', 'unidades'));
    }

    public function show(User $empleado)
    {
        $empleado->loadMissing(['unidadAsignada', 'employeeProfile']);
        $perfil = $empleado->employeeProfile ?? new EmployeeProfile();
        $unidades = Unidad::orderBy('economico')->get();

        return view('empleados.show', compact('empleado', 'perfil', 'unidades'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $temporaryPassword = $data['password'];
        $unidadId = $data['unidad_id'] ?? null;
        unset($data['unidad_id']);
        $data['password'] = Hash::make($data['password']);
        $data['must_change_password'] = true;
        if ($request->hasFile('foto')) $data['foto'] = $request->file('foto')->store('empleados', 'public');
        $empleado = User::create($data);
        $this->assignUnit($empleado, $unidadId);
        app(OperationalMail::class)->employeeCreated($empleado, $temporaryPassword);

        return redirect()->to(route('empleados.index', [], false))->with('success', 'Empleado registrado correctamente.');
    }

    public function update(Request $request, User $empleado)
    {
        abort_if($empleado->is($request->user()), 403, 'No puedes cambiar tu propio estado desde esta pantalla.');
        $data = $this->validated($request, $empleado);
        $temporaryPassword = $data['password'] ?? null;
        $unidadId = $data['unidad_id'] ?? null;
        unset($data['unidad_id']);
        if (empty($data['password'])) unset($data['password']);
        else {
            $data['password'] = Hash::make($data['password']);
            $data['must_change_password'] = true;
        }
        if ($request->hasFile('foto')) {
            if ($empleado->foto) Storage::disk('public')->delete($empleado->foto);
            $data['foto'] = $request->file('foto')->store('empleados', 'public');
        }
        $empleado->update($data);
        $this->assignUnit($empleado, $unidadId);
        if ($temporaryPassword) app(OperationalMail::class)->temporaryPasswordReset($empleado, $temporaryPassword);

        return redirect()->to(route('empleados.show', [$empleado->id], false))->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Request $request, User $empleado)
    {
        abort_if($empleado->is($request->user()), 403, 'No puedes eliminar la cuenta con la que iniciaste sesión.');
        abort_if($empleado->role === 'admin', 403, 'La cuenta administradora está protegida.');
        $data = $request->validate(['motivo_baja' => ['required', 'string', 'max:1000']]);
        Unidad::where('operador_id', $empleado->id)->update(['operador_id' => null]);
        $empleado->forceFill(['activo' => false, 'motivo_baja' => $data['motivo_baja']])->save();
        $empleado->delete();
        return redirect()->to(route('papelera.index', [], false))->with('success', 'Empleado enviado a Papelera. Su historial y fotografía se conservaron.');
    }

    private function validated(Request $request, ?User $empleado = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($empleado?->id)],
            'role' => [$empleado ? 'sometimes' : 'required', Rule::in(['inspector', 'operador', 'limpieza'])],
            'puesto' => [$empleado ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'fecha_ingreso' => ['nullable', 'date', 'after_or_equal:2026-01-01'],
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
