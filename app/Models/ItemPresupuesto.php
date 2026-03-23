<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPresupuesto extends Model
{
    protected $table = 'items_presupuesto';

    protected $fillable = [
        'presupuesto_id',
        'numero_item',
        'procedimiento',
        'diente',
        'cara',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'completado',
        'notas',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'valor_total'    => 'decimal:2',
        'completado'     => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->valor_total = $item->cantidad * $item->valor_unitario;
        });
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }
}
