<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'precio_unitario',
        'proveedor_habitual',
        'ubicacion',
        'activo',
    ];

    protected $casts = [
        'stock_actual'    => 'decimal:2',
        'stock_minimo'    => 'decimal:2',
        'stock_maximo'    => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'activo'          => 'boolean',
    ];

    // ── Relaciones ─────────────────────────────────────────────

    public function categoria()
    {
        return $this->belongsTo(CategoriaInventario::class, 'categoria_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class)->orderBy('created_at', 'desc');
    }

    public function itemsCompra()
    {
        return $this->hasMany(ItemCompra::class);
    }

    public function proveedores()
    {
        return $this->hasManyThrough(Proveedor::class, ItemCompra::class, 'material_id', 'id', 'id', 'compra_id');
    }

    public function precioPromedio()
    {
        return $this->itemsCompra()
            ->whereHas('compra', fn($q) => $q->where('estado', 'pagada'))
            ->avg('precio_unitario');
    }

    public function ultimoPrecio()
    {
        return $this->itemsCompra()
            ->whereHas('compra', fn($q) => $q->where('estado', 'pagada'))
            ->orderByDesc('created_at')
            ->value('precio_unitario');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo');
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getEstadoStockAttribute(): string
    {
        $actual  = (float) $this->stock_actual;
        $minimo  = (float) $this->stock_minimo;

        if ($actual <= $minimo) {
            return 'critico';
        }
        if ($actual <= $minimo * 1.5) {
            return 'bajo';
        }
        return 'normal';
    }

    public function getEstadoStockColorAttribute(): string
    {
        return match($this->estado_stock) {
            'critico' => 'danger',
            'bajo'    => 'warning',
            default   => 'success',
        };
    }

    public function getPorcentajeStockAttribute(): ?float
    {
        if (!$this->stock_maximo || (float) $this->stock_maximo <= 0) {
            return null;
        }
        return min(100, round(((float) $this->stock_actual / (float) $this->stock_maximo) * 100, 1));
    }

    // ── Métodos de movimiento ───────────────────────────────────

    public function registrarSalida(float $cantidad, string $concepto, int $userId, int $evolucionId = null): void
    {
        $stockAnterior = (float) $this->stock_actual;
        $stockPosterior = max(0, $stockAnterior - $cantidad);

        $this->update(['stock_actual' => $stockPosterior]);

        MovimientoInventario::create([
            'material_id'      => $this->id,
            'user_id'          => $userId,
            'tipo'             => 'salida',
            'cantidad'         => $cantidad,
            'stock_anterior'   => $stockAnterior,
            'stock_posterior'  => $stockPosterior,
            'concepto'         => $concepto,
            'evolucion_id'     => $evolucionId,
            'fecha_movimiento' => now()->toDateString(),
        ]);
    }

    public function registrarEntrada(float $cantidad, string $concepto, int $userId, float $precio = null, string $proveedor = null, string $factura = null): void
    {
        $stockAnterior  = (float) $this->stock_actual;
        $stockPosterior = $stockAnterior + $cantidad;

        $this->update(['stock_actual' => $stockPosterior]);

        MovimientoInventario::create([
            'material_id'      => $this->id,
            'user_id'          => $userId,
            'tipo'             => 'entrada',
            'cantidad'         => $cantidad,
            'stock_anterior'   => $stockAnterior,
            'stock_posterior'  => $stockPosterior,
            'concepto'         => $concepto,
            'precio_unitario'  => $precio,
            'proveedor'        => $proveedor,
            'numero_factura'   => $factura,
            'fecha_movimiento' => now()->toDateString(),
        ]);
    }

    public function ajustarStock(float $cantidadNueva, string $motivo, int $userId): void
    {
        $stockAnterior = (float) $this->stock_actual;

        $this->update(['stock_actual' => $cantidadNueva]);

        MovimientoInventario::create([
            'material_id'      => $this->id,
            'user_id'          => $userId,
            'tipo'             => 'ajuste',
            'cantidad'         => abs($cantidadNueva - $stockAnterior),
            'stock_anterior'   => $stockAnterior,
            'stock_posterior'  => $cantidadNueva,
            'concepto'         => 'Ajuste manual: ' . $motivo,
            'fecha_movimiento' => now()->toDateString(),
        ]);
    }
}
