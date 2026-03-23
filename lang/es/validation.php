<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email'    => 'El campo :attribute debe ser una dirección de correo válida.',
    'min'      => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max'      => [
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'unique'    => 'El :attribute ya está registrado.',

    'attributes' => [
        'email'                 => 'correo electrónico',
        'password'              => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'name'                  => 'nombre',
    ],
];
