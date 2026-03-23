<?php

namespace App\Traits;

trait GeneraNumeroDocumento
{
    /**
     * Genera un número correlativo con prefijo para el campo indicado.
     * Ej: HC-0001, EVO-0002, CIT-0003 …
     */
    public static function generarNumero(string $prefijo, string $campo): string
    {
        $ultimo = static::whereNotNull($campo)
            ->orderByRaw("CAST(SUBSTRING_INDEX({$campo}, '-', -1) AS UNSIGNED) DESC")
            ->value($campo);

        if ($ultimo) {
            $partes  = explode('-', $ultimo);
            $numero  = (int) end($partes) + 1;
        } else {
            $numero = 1;
        }

        return $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Devuelve el número formateado o un placeholder si aún no tiene.
     */
    public function getNumeroFormateadoAttribute(): string
    {
        $campo = static::$campoPrefijo ?? null;

        if ($campo && $this->{$campo}) {
            return $this->{$campo};
        }

        // fallback al ID
        return '#' . $this->id;
    }
}
