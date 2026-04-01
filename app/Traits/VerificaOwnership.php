<?php

namespace App\Traits;

trait VerificaOwnership
{
    /**
     * Verifica que el recurso pertenece al usuario autenticado.
     * Si es administrador puede ver todo.
     * Aborta con 403 si no tiene acceso.
     */
    protected function verificarAcceso($modelo, string $campo = 'user_id'): void
    {
        if (auth()->user()->hasRole('administrador')) {
            return;
        }

        if (isset($modelo->{$campo}) && $modelo->{$campo} !== auth()->id()) {
            abort(403, 'No tienes permiso para acceder a este recurso.');
        }
    }

    /**
     * Verifica que el paciente existe y está activo.
     */
    protected function verificarPaciente($pacienteId): \App\Models\Paciente
    {
        return \App\Models\Paciente::where('id', $pacienteId)
            ->where('activo', true)
            ->firstOrFail();
    }
}
