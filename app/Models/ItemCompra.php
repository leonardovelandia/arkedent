<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCompra extends Model
{
    protected $table = 'items_compra';

    protected $fillable = [
        'compra_id',
        'material_id',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'precio_unitario',
        'valor_total',
        'actualizo_inventario',
    ];

    protected $casts = [
        'cantidad'             => 'decimal:2',
        'precio_unitario'      => 'decimal:2',
        'valor_total'          => 'decimal:2',
        'actualizo_inventario' => 'boolean',
    ];

    // ── Boot ───────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->valor_total = (float) $model->cantidad * (float) $model->precio_unitario;
        });
    }

    // ── Relaciones ─────────────────────────────────────────────

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
