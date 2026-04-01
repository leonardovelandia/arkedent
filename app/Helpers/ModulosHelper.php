<?php

namespace App\Helpers;

class ModulosHelper
{
    /**
     * Verifica si un módulo está activo según el plan configurado.
     */
    public static function activo(string $modulo): bool
    {
        $planActivo = config('modulos.plan_activo', 'premium');

        if ($planActivo === 'personalizado') {
            return config("modulos.modulos_activos.{$modulo}", false);
        }

        $modulosPlan = config("modulos.planes.{$planActivo}.modulos", []);
        return in_array($modulo, $modulosPlan);
    }

    /**
     * Retorna la lista de módulos activos según el plan.
     */
    public static function modulosActivos(): array
    {
        $planActivo = config('modulos.plan_activo', 'premium');

        if ($planActivo === 'personalizado') {
            return array_keys(array_filter(
                config('modulos.modulos_activos', [])
            ));
        }

        return config("modulos.planes.{$planActivo}.modulos", []);
    }

    /**
     * Retorna la clave del plan activo.
     */
    public static function planActivo(): string
    {
        return config('modulos.plan_activo', 'premium');
    }

    /**
     * Retorna información del plan activo.
     */
    public static function infoPlan(): array
    {
        $plan = self::planActivo();
        return config("modulos.planes.{$plan}", [
            'nombre'      => 'Plan Personalizado',
            'descripcion' => 'Módulos seleccionados manualmente',
            'precio'      => 0,
        ]);
    }
}
