<?php

namespace App\Models;

use App\Traits\FormateaCampos;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use FormateaCampos;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'nombre',
        'nit',
        'contacto',
        'telefono',
        'whatsapp',
        'email',
        'direccion',
        'ciudad',
        'categorias',
        'tiempo_entrega_dias',
        'condiciones_pago',
        'calificacion',
        'notas',
        'activo',
    ];

    protected $casts = [
        'categorias'   => 'array',
        'activo'       => 'boolean',
        'calificacion' => 'decimal:1',
    ];

    // ── Relaciones ─────────────────────────────────────────────

    public function compras()
    {
        return $this->hasMany(Compra::class)->orderBy('fecha_compra', 'desc');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getTotalComprasAttribute(): float
    {
        return (float) $this->compras()->where('estado', 'pagada')->sum('total');
    }

    public function getCalificacionLabelAttribute(): string
    {
        $cal = (float) $this->calificacion;
        if ($cal >= 4.5) return 'Excelente';
        if ($cal >= 3.5) return 'Bueno';
        if ($cal >= 2.5) return 'Regular';
        return 'Malo';
    }

    public function getCalificacionColorAttribute(): string
    {
        $cal = (float) $this->calificacion;
        if ($cal >= 4.5) return 'success';
        if ($cal >= 3.5) return 'primary';
        if ($cal >= 2.5) return 'warning';
        return 'danger';
    }

    // ── Etiquetas de categorías ─────────────────────────────────

    public static function etiquetasCategorias(): array
    {
        return [
            'anestesia'           => 'Anestesia',
            'instrumental'        => 'Instrumental dental',
            'materiales_obturacion' => 'Materiales de obturación',
            'materiales_impresion'  => 'Materiales de impresión',
            'higiene'             => 'Higiene y profilaxis',
            'desinfeccion'        => 'Desinfección y esterilización',
            'radiologia'          => 'Radiología',
            'consumibles'         => 'Consumibles',
            'medicamentos'        => 'Medicamentos',
            'equipos'             => 'Equipos y herramientas',
            'otro'                => 'Otro',
        ];
    }
}
