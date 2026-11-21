<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UnidadStoreRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'economico' => ['required', 'string', 'max:20', 'unique:unidades,economico'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:50'],
            'operador_id' => ['nullable', 'exists:users,id,role,operador'],
            'odometro' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'in:operativa,en_taller,en_limpieza,fuera_servicio,activo,mantenimiento'],
            'proximo_mantenimiento_fecha' => ['nullable', 'date'],
            'proximo_mantenimiento_odometro' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'economico.required' => 'El número económico es obligatorio.',
            'economico.unique' => 'Ya existe una unidad con este número económico.',
            'economico.max' => 'El número económico no puede tener más de 20 caracteres.',
            'area.max' => 'El área no puede tener más de 50 caracteres.',
        ];
    }
}
