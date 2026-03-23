<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'material_id',
        'user_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'concepto',
        'evolucion_id',
        'precio_unitario',
        'proveedor',
        'numero_factura',
        'fecha_movimiento',
        'observaciones',
    ];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'cantidad'         => 'decimal:2',
        'stock_anterior'   => 'decimal:2',
        'stock_posterior'  => 'decimal:2',
        'precio_unitario'  => 'decimal:2',
    ];

    // ── Relaciones ─────────────────────────────────────────────

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function evolucion()
    {
        return $this->belongsTo(Evolucion::class);
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'entrada' => '#166534',
            'salida'  => '#dc2626',
            'ajuste'  => '#1e40af',
            default   => '#374151',
        };
    }

    public function getTipoIconoAttribute(): string
    {
        return match($this->tipo) {
            'entrada' => 'bi-arrow-down-circle',
            'salida'  => 'bi-arrow-up-circle',
            'ajuste'  => 'bi-pencil-square',
            default   => 'bi-circle',
        };
    }
}
