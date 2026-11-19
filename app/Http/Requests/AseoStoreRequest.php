<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AseoStoreRequest extends BaseFormRequest
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
            'intendente_responsable' => ['required', 'string', 'max:100'],
            'area_terminal' => ['required', 'string', 'max:100'],
            'fecha_hora' => ['required', 'date'],
            'comentarios' => ['nullable', 'string', 'max:500'],
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
            'intendente_responsable.required' => 'El nombre del intendente es obligatorio.',
            'intendente_responsable.max' => 'El nombre del intendente no puede tener más de 100 caracteres.',
            'area_terminal.required' => 'El área o terminal es obligatoria.',
            'area_terminal.max' => 'El área o terminal no puede tener más de 100 caracteres.',
            'fecha_hora.required' => 'La fecha y hora es obligatoria.',
            'fecha_hora.date' => 'La fecha y hora debe ser una fecha válida.',
            'comentarios.max' => 'Los comentarios no pueden tener más de 500 caracteres.',
        ];
    }
}