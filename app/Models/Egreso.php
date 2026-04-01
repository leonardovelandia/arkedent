<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\FormateaCampos;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    use GeneraNumeroDocumento, FormateaCampos;

    protected static string $campoPrefijo = 'numero_egreso';

    protected $fillable = [
        'numero_egreso',
        'categoria_id',
        'user_id',
        'concepto',
        'descripcion',
        'valor',
        'metodo_pago',
        'fecha_egreso',
        'numero_comprobante',
        'comprobante_path',
        'es_recurrente',
        'frecuencia_recurrente',
        'dia_recurrente',
        'proxima_fecha',
        'anulado',
        'motivo_anulacion',
        'notas',
        'activo',
    ];

    protected $casts = [
        'fecha_egreso'   => 'date',
        'proxima_fecha'  => 'date',
        'es_recurrente'  => 'boolean',
        'anulado'        => 'boolean',
        'activo'         => 'boolean',
        'valor'          => 'decimal:2',
    ];

    // ─── Boot ──────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($egreso) {
            $categoriaNombre = $egreso->categoria?->nombre;
            \App\Models\LibroContable::registrarMovimiento(
                tipo:            'egreso',
                origen:          'egreso_manual',
                origenId:        $egreso->id,
                origenTipo:      'App\Models\Egreso',
                concepto:        $egreso->concepto,
                valor:           (float) $egreso->valor,
                fechaMovimiento: $egreso->fecha_egreso,
                metodoPago:      $egreso->metodo_pago,
                referencia:      $egreso->numero_comprobante,
                categoria:       $categoriaNombre,
                descripcion:     $egreso->descripcion,
            );
        });

        static::updated(function ($egreso) {
            if ($egreso->isDirty('anulado') && $egreso->anulado) {
                \App\Models\LibroContable::where('origen', 'egreso_manual')
                    ->where('origen_id', $egreso->id)
                    ->update([
                        'excluido'         => true,
                        'motivo_exclusion' => 'Egreso anulado',
                    ]);
            }
        });
    }

    // ─── Relaciones ────────────────────────────────────────────────

    public function categoria()
    {
        return $this->belongsTo(CategoriaEgreso::class, 'categoria_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getValorFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->valor, 0, ',', '.');
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        $labels = [
            'efectivo'        => 'Efectivo',
            'transferencia'   => 'Transferencia',
            'tarjeta_credito' => 'Tarjeta Crédito',
            'tarjeta_debito'  => 'Tarjeta Débito',
            'cheque'          => 'Cheque',
            'otro'            => 'Otro',
        ];
        return $labels[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function getFrecuenciaLabelAttribute(): string
    {
        $labels = [
            'diario'      => 'Diario',
            'semanal'     => 'Semanal',
            'quincenal'   => 'Quincenal',
            'mensual'     => 'Mensual',
            'bimestral'   => 'Bimestral',
            'trimestral'  => 'Trimestral',
            'semestral'   => 'Semestral',
            'anual'       => 'Anual',
        ];
        return $labels[$this->frecuencia_recurrente] ?? ($this->frecuencia_recurrente ?? '');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('anulado', false);
    }

    public function scopeDelMes($query, int $mes, int $ano)
    {
        return $query->whereMonth('fecha_egreso', $mes)->whereYear('fecha_egreso', $ano);
    }

    public function scopeRecurrentes($query)
    {
        return $query->where('es_recurrente', true)->where('activo', true)->where('anulado', false);
    }

    // ─── Métodos ───────────────────────────────────────────────────

    public function generarProximaFecha(): ?Carbon
    {
        if (!$this->es_recurrente || !$this->frecuencia_recurrente) {
            return null;
        }

        $base = $this->proxima_fecha ?? $this->fecha_egreso ?? Carbon::today();

        return match ($this->frecuencia_recurrente) {
            'diario'     => $base->copy()->addDay(),
            'semanal'    => $base->copy()->addWeek(),
            'quincenal'  => $base->copy()->addDays(15),
            'mensual'    => $base->copy()->addMonth(),
            'bimestral'  => $base->copy()->addMonths(2),
            'trimestral' => $base->copy()->addMonths(3),
            'semestral'  => $base->copy()->addMonths(6),
            'anual'      => $base->copy()->addYear(),
            default      => null,
        };
    }
}
