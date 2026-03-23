<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use GeneraNumeroDocumento;

    protected static $campoPrefijo = 'numero_tratamiento';

    protected $fillable = [
        'numero_tratamiento',
        'paciente_id', 'historia_clinica_id', 'user_id',
        'nombre', 'valor_total', 'saldo_pendiente',
        'estado', 'fecha_inicio', 'fecha_fin', 'notas', 'activo',
    ];

    protected $casts = [
        'valor_total'     => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_inicio'    => 'date',
        'fecha_fin'       => 'date',
        'activo'          => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tratamiento) {
            if (is_null($tratamiento->saldo_pendiente)) {
                $tratamiento->saldo_pendiente = $tratamiento->valor_total;
            }
            if (empty($tratamiento->numero_tratamiento)) {
                $tratamiento->numero_tratamiento = static::generarNumero('TRT', 'numero_tratamiento');
            }
        });
    }

    // ── Relaciones ─────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class)->orderBy('fecha_pago', 'desc');
    }

    public function presupuesto()
    {
        return $this->hasOne(Presupuesto::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getTotalPagadoAttribute()
    {
        return $this->pagos()->where('anulado', false)->sum('valor');
    }

    public function getPorcentajePagadoAttribute()
    {
        if ($this->valor_total <= 0) return 0;
        $pagado = $this->total_pagado;
        return min(100, round(($pagado / $this->valor_total) * 100));
    }

    public function recalcularSaldo()
    {
        $pagado = $this->pagos()->where('anulado', false)->sum('valor');
        $this->saldo_pendiente = max(0, $this->valor_total - $pagado);
        $this->save();
    }
}
