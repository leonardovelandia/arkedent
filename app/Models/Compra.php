<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'compras';

    protected static string $campoPrefijo = 'numero_compra';

    protected $fillable = [
        'numero_compra',
        'proveedor_id',
        'user_id',
        'fecha_compra',
        'numero_factura',
        'subtotal',
        'descuento_valor',
        'total',
        'metodo_pago',
        'estado',
        'fecha_vencimiento',
        'notas',
        'activo',
    ];

    protected $casts = [
        'fecha_compra'      => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal'          => 'decimal:2',
        'descuento_valor'   => 'decimal:2',
        'total'             => 'decimal:2',
        'activo'            => 'boolean',
    ];

    // ── Boot ───────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_compra)) {
                $model->numero_compra = static::generarNumero('COM', 'numero_compra');
            }
        });

        static::updated(function ($compra) {
            if ($compra->isDirty('estado') && $compra->estado === 'pagada') {
                $nombreProveedor = $compra->proveedor->nombre ?? '';
                $numeroFormateado = $compra->numero_formateado ?? $compra->numero_compra;
                \App\Models\LibroContable::registrarMovimiento(
                    tipo:            'egreso',
                    origen:          'compra_proveedor',
                    origenId:        $compra->id,
                    origenTipo:      'App\Models\Compra',
                    concepto:        "Compra {$numeroFormateado} — {$nombreProveedor}",
                    valor:           (float) $compra->total,
                    fechaMovimiento: $compra->fecha_compra,
                    metodoPago:      $compra->metodo_pago,
                    referencia:      $compra->numero_factura ?? $numeroFormateado,
                    categoria:       'Compras a proveedores',
                );
            }
            if ($compra->isDirty('estado') && $compra->estado === 'cancelada') {
                \App\Models\LibroContable::where('origen', 'compra_proveedor')
                    ->where('origen_id', $compra->id)
                    ->update([
                        'excluido'         => true,
                        'motivo_exclusion' => 'Compra cancelada',
                    ]);
            }
        });
    }

    // ── Relaciones ─────────────────────────────────────────────

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(ItemCompra::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'warning',
            'pagada'     => 'success',
            'cancelada'  => 'danger',
            default      => 'secondary',
        };
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo'        => 'Efectivo',
            'transferencia'   => 'Transferencia',
            'tarjeta_credito' => 'Tarjeta Crédito',
            'tarjeta_debito'  => 'Tarjeta Débito',
            'cheque'          => 'Cheque',
            'credito'         => 'Crédito',
            'otro'            => 'Otro',
            default           => $this->metodo_pago,
        };
    }
}
