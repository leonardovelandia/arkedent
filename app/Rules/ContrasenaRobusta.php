<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContrasenaRobusta implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('La contraseña debe tener mínimo 8 caracteres.');
            return;
        }
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('La contraseña debe tener al menos una letra mayúscula.');
            return;
        }
        if (!preg_match('/[a-z]/', $value)) {
            $fail('La contraseña debe tener al menos una letra minúscula.');
            return;
        }
        if (!preg_match('/[0-9]/', $value)) {
            $fail('La contraseña debe tener al menos un número.');
            return;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('La contraseña debe tener al menos un símbolo especial (@, #, $, etc.).');
            return;
        }

        $prohibidas = [
            'password', 'contraseña', '12345678',
            'qwerty123', 'admin1234', 'colombia1',
        ];
        if (in_array(strtolower($value), $prohibidas)) {
            $fail('Esta contraseña es demasiado común. Elige una más segura.');
        }
    }
}
