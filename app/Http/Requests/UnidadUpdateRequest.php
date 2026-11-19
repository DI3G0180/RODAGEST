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
            'placa' => ['sometimes', 'nullable', 'string', 'max:10'],
            'tipo_unidad' => ['sometimes', 'nullable', 'string', 'max:50'],
            'area' => ['sometimes', 'nullable', 'string', 'max:50'],
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
            'placa.max' => 'La placa no puede tener más de 10 caracteres.',
            'tipo_unidad.max' => 'El tipo de unidad no puede tener más de 50 caracteres.',
            'area.max' => 'El área no puede tener más de 50 caracteres.',
        ];
    }
}