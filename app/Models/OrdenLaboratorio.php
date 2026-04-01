<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class OrdenLaboratorio extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'ordenes_laboratorio';

    protected static string $campoPrefijo = 'numero_orden';

    protected $fillable = [
        'numero_orden', 'paciente_id', 'laboratorio_id', 'user_id',
        'evolucion_id', 'cita_id', 'tipo_trabajo', 'descripcion',
        'dientes', 'color_diente', 'material',
        'fecha_envio', 'fecha_entrega_estimada', 'fecha_recepcion', 'fecha_instalacion',
        'estado', 'precio_laboratorio',
        'observaciones_envio', 'observaciones_recepcion',
        'calidad_recibida', 'requiere_ajuste', 'motivo_cancelacion', 'activo',
    ];

    protected $casts = [
        'fecha_envio'             => 'date',
        'fecha_entrega_estimada'  => 'date',
        'fecha_recepcion'         => 'date',
        'fecha_instalacion'       => 'date',
        'requiere_ajuste'         => 'boolean',
        'activo'                  => 'boolean',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_orden)) {
                $ultimo = static::whereNotNull('numero_orden')
                    ->orderByDesc('numero_orden')
                    ->value('numero_orden');

                if ($ultimo) {
                    $partes  = explode('-', $ultimo);
                    $numero  = (int) end($partes) + 1;
                } else {
                    $numero = 1;
                }

                $model->numero_orden = 'LAB-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updated(function ($orden) {
            if ($orden->isDirty('estado') && $orden->estado === 'recibido' && $orden->precio_laboratorio > 0) {
                $nombreLab = $orden->laboratorio->nombre ?? '';
                $numFormateado = $orden->numero_formateado;
                $fechaMov = $orden->fecha_recepcion
                    ? $orden->fecha_recepcion->toDateString()
                    : now()->toDateString();
                \App\Models\LibroContable::registrarMovimiento(
                    tipo:            'egreso',
                    origen:          'gasto_laboratorio',
                    origenId:        $orden->id,
                    origenTipo:      'App\Models\OrdenLaboratorio',
                    concepto:        "Laboratorio {$numFormateado} — {$nombreLab} — {$orden->tipo_trabajo}",
                    valor:           (float) $orden->precio_laboratorio,
                    fechaMovimiento: $fechaMov,
                    referencia:      $numFormateado,
                    categoria:       'Gastos de laboratorio',
                );
            }
            if ($orden->isDirty('estado') && $orden->estado === 'cancelado') {
                \App\Models\LibroContable::where('origen', 'gasto_laboratorio')
                    ->where('origen_id', $orden->id)
                    ->update([
                        'excluido'         => true,
                        'motivo_exclusion' => 'Orden de laboratorio cancelada',
                    ]);
            }
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function evolucion()
    {
        return $this->belongsTo(Evolucion::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'enviado', 'en_proceso']);
    }

    public function scopeVencidas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'enviado', 'en_proceso'])
            ->whereDate('fecha_entrega_estimada', '<', today());
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getNumeroFormateadoAttribute(): string
    {
        return $this->numero_orden ?? 'LAB-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getEstadoColorAttribute(): string
    {
        $colores = [
            'pendiente'  => 'warning',
            'enviado'    => 'info',
            'en_proceso' => 'primary',
            'recibido'   => 'success',
            'instalado'  => 'dark',
            'cancelado'  => 'danger',
        ];
        return $colores[$this->estado] ?? 'secondary';
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'pendiente'  => 'Pendiente',
            'enviado'    => 'Enviado',
            'en_proceso' => 'En Proceso',
            'recibido'   => 'Recibido',
            'instalado'  => 'Instalado',
            'cancelado'  => 'Cancelado',
        ];
        return $labels[$this->estado] ?? $this->estado;
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_entrega_estimada) {
            return null;
        }
        return today()->diffInDays($this->fecha_entrega_estimada, false);
    }

    public function getEstaVencidoAttribute(): bool
    {
        if (!$this->fecha_entrega_estimada) {
            return false;
        }
        return !in_array($this->estado, ['recibido', 'instalado', 'cancelado'])
            && $this->fecha_entrega_estimada->lt(today());
    }
}
