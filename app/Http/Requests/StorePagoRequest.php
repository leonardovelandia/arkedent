<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'paciente_id'    => 'required|integer|exists:pacientes,id',
            'fecha'          => 'required|date',
            'monto'          => 'required|numeric|min:0',
            'metodo_pago'    => 'nullable|string|max:50',
            'concepto'       => 'nullable|string|max:500',
            'comprobante'    => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'El paciente es obligatorio.',
            'fecha.required'       => 'La fecha del pago es obligatoria.',
            'monto.required'       => 'El monto es obligatorio.',
            'monto.min'            => 'El monto no puede ser negativo.',
        ];
    }
}
