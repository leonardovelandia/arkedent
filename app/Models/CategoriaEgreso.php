<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CategoriaEgreso extends Model
{
    protected $table = 'categorias_egreso';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'icono',
        'es_fijo',
        'activo',
    ];

    protected $casts = [
        'es_fijo' => 'boolean',
        'activo'  => 'boolean',
    ];

    // ─── Relaciones ────────────────────────────────────────────────

    public function egresos()
    {
        return $this->hasMany(Egreso::class, 'categoria_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getTotalMesAttribute(): float
    {
        $hoy = Carbon::today();
        return $this->egresos()
            ->whereMonth('fecha_egreso', $hoy->month)
            ->whereYear('fecha_egreso', $hoy->year)
            ->where('anulado', false)
            ->sum('valor');
    }
}
