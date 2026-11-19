<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuejaStoreRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'unidad_id' => ['required', 'exists:unidades,id'],
            'tipo_queja' => ['required', 'string', 'max:50'],
            'descripcion' => ['required', 'string', 'max:500'],
            'estado' => ['required', 'in:pendiente,en_proceso,resuelto'],
            'operador_id' => ['nullable', 'exists:users,id'],
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
            'unidad_id.required' => 'Debe seleccionar una unidad.',
            'unidad_id.exists' => 'La unidad seleccionada no es válida.',
            'tipo_queja.required' => 'El tipo de queja es obligatorio.',
            'tipo_queja.max' => 'El tipo de queja no puede tener más de 50 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: pendiente, en proceso o resuelto.',
            'operador_id.exists' => 'El operador asignado no es válido.',
        ];
    }
}