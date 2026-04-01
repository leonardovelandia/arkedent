<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'paciente_id'   => 'required|integer|exists:pacientes,id',
            'fecha'         => 'required|date|before_or_equal:today',
            'hora_inicio'   => 'nullable|date_format:H:i',
            'hora_fin'      => 'nullable|date_format:H:i|after:hora_inicio',
            'procedimiento' => 'required|string|max:500',
            'descripcion'   => 'nullable|string|max:5000',
            'materiales'    => 'nullable|string|max:2000',
            'observaciones' => 'nullable|string|max:2000',
            'indicaciones'  => 'nullable|string|max:2000',
            'dientes'       => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'  => 'El paciente es obligatorio.',
            'paciente_id.exists'    => 'El paciente seleccionado no existe.',
            'fecha.required'        => 'La fecha es obligatoria.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'procedimiento.required'=> 'El procedimiento es obligatorio.',
        ];
    }
}
