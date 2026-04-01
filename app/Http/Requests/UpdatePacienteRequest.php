<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'tipo_documento'   => 'nullable|string|max:20',
            'numero_documento' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero'           => 'nullable|in:masculino,femenino,otro',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'direccion'        => 'nullable|string|max:300',
            'ciudad'           => 'nullable|string|max:100',
            'ocupacion'        => 'nullable|string|max:150',
            'eps'              => 'nullable|string|max:150',
            'grupo_sanguineo'  => 'nullable|string|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre del paciente es obligatorio.',
            'apellido.required' => 'El apellido del paciente es obligatorio.',
            'email.email'       => 'El correo electrónico no es válido.',
        ];
    }
}
