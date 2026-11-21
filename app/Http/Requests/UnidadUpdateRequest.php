<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadUpdateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'economico' => ['sometimes', 'required', 'string', 'max:20', 'unique:unidades,economico,' . $this->unidad],
            'modelo' => ['sometimes', 'nullable', 'string', 'max:100'],
            'area' => ['sometimes', 'nullable', 'string', 'max:50'],
            'operador_id' => ['sometimes', 'nullable', 'exists:users,id,role,operador'],
            'odometro' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'estado' => ['sometimes', 'in:operativa,en_taller,en_limpieza,fuera_servicio,activo,mantenimiento'],
            'proximo_mantenimiento_fecha' => ['sometimes', 'nullable', 'date'],
            'proximo_mantenimiento_odometro' => ['sometimes', 'nullable', 'integer', 'min:0'],
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
            'economico.unique' => 'Ya existe otra unidad con este número económico.',
            'economico.max' => 'El número económico no puede tener más de 20 caracteres.',
            'area.max' => 'El área no puede tener más de 50 caracteres.',
        ];
    }
}
