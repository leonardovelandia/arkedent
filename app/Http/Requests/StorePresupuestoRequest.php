<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresupuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'paciente_id'   => 'required|integer|exists:pacientes,id',
            'fecha'         => 'required|date',
            'vigencia_dias' => 'nullable|integer|min:1|max:365',
            'observaciones' => 'nullable|string|max:2000',
            'items'         => 'nullable|array',
            'items.*.descripcion' => 'required_with:items|string|max:300',
            'items.*.cantidad'    => 'required_with:items|numeric|min:1',
            'items.*.precio'      => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'El paciente es obligatorio.',
            'paciente_id.exists'   => 'El paciente seleccionado no existe.',
            'fecha.required'       => 'La fecha del presupuesto es obligatoria.',
        ];
    }
}
