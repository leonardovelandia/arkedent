<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'numero_recibo', 'paciente_id', 'tratamiento_id', 'presupuesto_id', 'user_id',
        'concepto', 'valor', 'metodo_pago', 'referencia_pago',
        'fecha_pago', 'observaciones', 'anulado', 'motivo_anulacion', 'activo',
        'es_pago_libre',
    ];

    protected $casts = [
        'valor'      => 'decimal:2',
        'fecha_pago' => 'date',
        'anulado'    => 'boolean',
        'activo'     => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generar número de recibo
        static::creating(function ($pago) {
            if (empty($pago->numero_recibo)) {
                $ultimo = static::orderBy('id', 'desc')->value('id') ?? 0;
                $pago->numero_recibo = 'REC-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        // Actualizar saldo del tratamiento al crear pago
        static::created(function ($pago) {
            if ($pago->tratamiento_id) {
                $pago->tratamiento?->recalcularSaldo();
            }
            // Registrar en libro contable
            $nombrePaciente = $pago->paciente->nombre_completo ?? '';
            \App\Models\LibroContable::registrarMovimiento(
                tipo:            'ingreso',
                origen:          'pago_paciente',
                origenId:        $pago->id,
                origenTipo:      'App\Models\Pago',
                concepto:        "Pago paciente — {$nombrePaciente} — {$pago->concepto}",
                valor:           (float) $pago->valor,
                fechaMovimiento: $pago->fecha_pago,
                metodoPago:      $pago->metodo_pago,
                referencia:      $pago->numero_recibo,
                categoria:       'Ingresos por servicios',
            );
        });

        // Actualizar saldo del tratamiento al actualizar pago (ej. anular)
        static::updated(function ($pago) {
            if ($pago->tratamiento_id) {
                $pago->tratamiento?->recalcularSaldo();
            }
            // Excluir del libro si el pago se anula
            if ($pago->isDirty('anulado') && $pago->anulado) {
                \App\Models\LibroContable::where('origen', 'pago_paciente')
                    ->where('origen_id', $pago->id)
                    ->update([
                        'excluido'         => true,
                        'motivo_exclusion' => 'Pago anulado',
                    ]);
            }
        });
    }

    // ── Relaciones ─────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('anulado', false);
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getMetodoPagoLabelAttribute()
    {
        return match ($this->metodo_pago) {
            'efectivo'        => 'Efectivo',
            'transferencia'   => 'Transferencia',
            'tarjeta_credito' => 'Tarjeta Crédito',
            'tarjeta_debito'  => 'Tarjeta Débito',
            'cheque'          => 'Cheque',
            'otro'            => 'Otro',
            default           => $this->metodo_pago,
        };
    }

    public function getValorFormateadoAttribute()
    {
        return '$ ' . number_format($this->valor, 0, ',', '.');
    }
}
