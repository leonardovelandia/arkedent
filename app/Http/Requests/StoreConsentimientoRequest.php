<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsentimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'paciente_id'  => 'required|integer|exists:pacientes,id',
            'tipo'         => 'nullable|string|max:100',
            'contenido'    => 'nullable|string|max:10000',
            'observaciones'=> 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'El paciente es obligatorio.',
            'paciente_id.exists'   => 'El paciente seleccionado no existe.',
        ];
    }
}
