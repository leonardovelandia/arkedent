<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LibroContable extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'libro_contable';

    protected static string $campoPrefijo = 'numero_asiento';

    protected $fillable = [
        'numero_asiento',
        'tipo',
        'origen',
        'origen_id',
        'origen_tipo',
        'concepto',
        'descripcion',
        'valor',
        'fecha_movimiento',
        'metodo_pago',
        'referencia',
        'categoria',
        'excluido',
        'motivo_exclusion',
        'user_id',
        'activo',
    ];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'excluido'         => 'boolean',
        'activo'           => 'boolean',
        'valor'            => 'decimal:2',
    ];

    // ── Boot ───────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_asiento)) {
                $model->numero_asiento = static::generarNumero('ASI', 'numero_asiento');
            }
        });
    }

    // ── Relaciones ─────────────────────────────────────────────────────────

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getNumeroFormateadoAttribute(): string
    {
        return $this->numero_asiento ?? 'ASI-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getTipoColorAttribute(): string
    {
        return $this->tipo === 'ingreso' ? '#28a745' : '#DC3545';
    }

    public function getTipoIconoAttribute(): string
    {
        return $this->tipo === 'ingreso' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle';
    }

    public function getOrigenLabelAttribute(): string
    {
        $labels = [
            'pago_paciente'    => 'Pago de Paciente',
            'egreso_manual'    => 'Egreso Manual',
            'compra_proveedor' => 'Compra a Proveedor',
            'gasto_laboratorio'=> 'Gasto de Laboratorio',
            'ajuste_manual'    => 'Ajuste Manual',
        ];
        return $labels[$this->origen] ?? $this->origen;
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->valor, 0, ',', '.');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('excluido', false);
    }

    public function scopeDelMes($query, int $mes, int $ano)
    {
        return $query->whereMonth('fecha_movimiento', $mes)->whereYear('fecha_movimiento', $ano);
    }

    public function scopeDelPeriodo($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_movimiento', [$desde, $hasta]);
    }

    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeEgresos($query)
    {
        return $query->where('tipo', 'egreso');
    }

    // ── Métodos estáticos ──────────────────────────────────────────────────

    public static function registrarMovimiento(
        string $tipo,
        string $origen,
        int $origenId,
        string $origenTipo,
        string $concepto,
        float $valor,
        $fechaMovimiento,
        string $metodoPago = null,
        string $referencia = null,
        string $categoria = null,
        string $descripcion = null
    ): self {
        return self::create([
            'tipo'             => $tipo,
            'origen'           => $origen,
            'origen_id'        => $origenId,
            'origen_tipo'      => $origenTipo,
            'concepto'         => $concepto,
            'descripcion'      => $descripcion,
            'valor'            => $valor,
            'fecha_movimiento' => $fechaMovimiento instanceof Carbon
                ? $fechaMovimiento->toDateString()
                : $fechaMovimiento,
            'metodo_pago'      => $metodoPago,
            'referencia'       => $referencia,
            'categoria'        => $categoria,
            'user_id'          => auth()->id() ?? 1,
            'excluido'         => false,
        ]);
    }
}
