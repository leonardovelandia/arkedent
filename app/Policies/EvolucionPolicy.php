<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Evolucion;



class EvolucionPolicy
{
    public function view(User $user, Evolucion $evolucion)
    {
        return $user->id === $evolucion->user_id
            || $user->hasRole('administrador')
            || $user->hasRole('asistente');
    }

    public function update(User $user, Evolucion $evolucion)
    {
        return $user->id === $evolucion->user_id
            || $user->hasRole('administrador');
    }

    public function delete(User $user, Evolucion $evolucion)
    {
        return $user->hasRole('administrador');
    }
}