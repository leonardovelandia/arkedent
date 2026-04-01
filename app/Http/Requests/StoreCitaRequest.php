<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
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
            'hora_inicio'   => 'nullable|date_format:H:i',
            'hora_fin'      => 'nullable|date_format:H:i|after:hora_inicio',
            'procedimiento' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:2000',
            'estado'        => 'nullable|in:programada,confirmada,cancelada,no_asistio,realizada',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'El paciente es obligatorio.',
            'paciente_id.exists'   => 'El paciente seleccionado no existe.',
            'fecha.required'       => 'La fecha de la cita es obligatoria.',
        ];
    }
}
