<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    protected $fillable = [
        'nombre', 'contacto', 'telefono', 'whatsapp', 'email',
        'direccion', 'ciudad', 'especialidades', 'tiempo_entrega_dias',
        'notas', 'activo',
    ];

    protected $casts = [
        'especialidades'    => 'array',
        'activo'            => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────
    public function ordenes()
    {
        return $this->hasMany(OrdenLaboratorio::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getEspecialidadesLabelAttribute(): string
    {
        if (empty($this->especialidades)) {
            return '—';
        }

        $labels = [
            'coronas_puentes'    => 'Coronas y Puentes',
            'protesis_removible' => 'Prótesis Removible',
            'protesis_total'     => 'Prótesis Total',
            'implantologia'      => 'Implantología',
            'ortodoncia'         => 'Ortodoncia',
            'estetica'           => 'Estética Dental',
            'cirugia'            => 'Cirugía',
        ];

        return implode(', ', array_map(
            fn($e) => $labels[$e] ?? $e,
            $this->especialidades
        ));
    }
}
