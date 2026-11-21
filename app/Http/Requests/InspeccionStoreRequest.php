<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InspeccionStoreRequest extends BaseFormRequest
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
            'operador_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'operador')],
            'inspector_responsable' => ['nullable', 'string', 'max:100'],
            'persona_reporta' => ['nullable', 'string', 'max:150'],
            'ubicacion_dano' => ['nullable', 'string', 'max:180'],
            'municipio' => ['nullable', 'string', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'categoria_evento' => ['nullable', 'string', 'max:80'],
            'tipo_dano' => ['required', 'string', 'max:50'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
            'descripcion' => ['required', 'string', 'max:500'],
            'fecha_reporte' => ['required', 'date'],
            'evidencia_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'fotos' => ['nullable', 'array', 'max:8'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'videos' => ['nullable', 'array', 'max:2'],
            'videos.*' => ['file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'mimes:mp4,mov,webm', 'max:51200'],
            'checklist' => ['required', 'array'],
            'checklist.frenos' => ['required', 'boolean'],
            'checklist.neumaticos' => ['required', 'boolean'],
            'checklist.luces' => ['required', 'boolean'],
            'checklist.niveles' => ['required', 'boolean'],
            'checklist.puertas' => ['required', 'boolean'],
            'checklist.documentacion' => ['required', 'boolean'],
            'dictamen' => ['required', 'in:operativa,en_taller,fuera_servicio'],
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
            'inspector_responsable.required' => 'El nombre del inspector es obligatorio.',
            'inspector_responsable.max' => 'El nombre del inspector no puede tener más de 100 caracteres.',
            'tipo_dano.required' => 'El tipo de daño es obligatorio.',
            'tipo_dano.max' => 'El tipo de daño no puede tener más de 50 caracteres.',
            'prioridad.required' => 'La prioridad es obligatoria.',
            'prioridad.in' => 'La prioridad debe ser: baja, media, alta o crítica.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: pendiente, en proceso o resuelto.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
            'fecha_reporte.required' => 'La fecha y hora es obligatoria.',
            'fecha_reporte.date' => 'La fecha y hora debe ser una fecha válida.',
            'intendente_responsable.max' => 'El nombre del intendente no puede tener más de 100 caracteres.',
        ];
    }
}
