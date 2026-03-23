<?php

namespace App\Traits;

/**
 * Trait FormateaCampos
 *
 * Normaliza automáticamente los datos de formularios antes de guardarlos en BD.
 * Aplica capitalización a textos y limpia caracteres no numéricos en teléfonos/documentos.
 *
 * USO EN CUALQUIER CONTROLADOR:
 * 1. Agregar al inicio: use App\Traits\FormateaCampos;
 * 2. Agregar dentro de la clase: use FormateaCampos;
 * 3. Después de validar: $datos = $this->formatearDatos($validated);
 * 4. Usar $datos para guardar en BD
 */
trait FormateaCampos
{
    /**
     * Formatea los campos del array según su tipo.
     *
     * @param  array $datos  Array proveniente del request (validado o sin validar)
     * @return array         Array con los campos normalizados
     */
    public function formatearDatos(array $datos): array
    {
        // ── Campos que reciben ucwords (cada palabra capitalizada) ────────
        $camposUcwords = [
            'nombre',
            'apellido',
            'ciudad',
            'direccion',
            'ocupacion',
            'nombre_acudiente',
            'nombre_consultorio',
            'razon_social',
            'nombre_proveedor',
            'nombre_laboratorio',
            'nombre_contacto',
            'procedimiento',
        ];

        // ── Campos que reciben ucfirst (solo primera letra del texto) ─────
        $camposUcfirst = [
            'descripcion',
            'observaciones',
            'motivo',
        ];

        // ── Campos que solo aceptan dígitos ───────────────────────────────
        $camposNumericos = [
            'telefono',
            'telefono_emergencia',
            'numero_documento',
            'nit',
            'celular',
            'telefono_contacto',
        ];

        foreach ($camposUcwords as $campo) {
            if (array_key_exists($campo, $datos) && !is_null($datos[$campo])) {
                $datos[$campo] = ucwords(strtolower(trim($datos[$campo])));
            }
        }

        foreach ($camposUcfirst as $campo) {
            if (array_key_exists($campo, $datos) && !is_null($datos[$campo])) {
                $datos[$campo] = ucfirst(strtolower(trim($datos[$campo])));
            }
        }

        foreach ($camposNumericos as $campo) {
            if (array_key_exists($campo, $datos) && !is_null($datos[$campo])) {
                $datos[$campo] = preg_replace('/[^0-9]/', '', $datos[$campo]);
            }
        }

        return $datos;
    }
}
