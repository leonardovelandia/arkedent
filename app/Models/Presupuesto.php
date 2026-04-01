<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use GeneraNumeroDocumento;

    protected static $campoPrefijo = 'numero_presupuesto';

    protected $fillable = [
        'numero_presupuesto',
        'paciente_id',
        'historia_clinica_id',
        'user_id',
        'fecha_generacion',
        'fecha_vencimiento',
        'estado',
        'subtotal',
        'descuento_porcentaje',
        'descuento_valor',
        'total',
        'condiciones_pago',
        'validez_dias',
        'observaciones',
        'motivo_rechazo',
        'fecha_aprobacion',
        'firmado',
        'firma_data',
        'ip_firma',
        'tratamiento_id',
        'activo',
        // Trazabilidad
        'firma_user_agent',
        'firma_timestamp',
        'firma_timezone',
        'firma_hash',
        'documento_hash',
        'firma_dispositivo',
        'firma_navegador',
        'firma_verificacion_token',
    ];

    protected $casts = [
        'fecha_generacion'  => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_aprobacion'  => 'datetime',
        'firma_timestamp'   => 'datetime',
        'firmado'           => 'boolean',
        'activo'            => 'boolean',
        'subtotal'          => 'decimal:2',
        'descuento_valor'   => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($presupuesto) {
            if (empty($presupuesto->numero_presupuesto)) {
                $presupuesto->numero_presupuesto = static::generarNumero('PRE', 'numero_presupuesto');
            }
        });
    }

    // ── Relaciones ──────────────────────────────────────────────

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

    public function items()
    {
        return $this->hasMany(ItemPresupuesto::class)->orderBy('numero_item');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }

    public function valoracion()
    {
        return $this->hasOne(Valoracion::class);
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->whereNotIn('estado', ['rechazado', 'vencido'])->where('activo', true);
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getNumeroFormateadoAttribute(): string
    {
        return $this->numero_presupuesto ?? '#' . $this->id;
    }

    public function getEstaVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento < Carbon::today()
            && !in_array($this->estado, ['aprobado', 'rechazado']);
    }

    public function getEstadoColorAttribute(): array
    {
        return match ($this->estado) {
            'borrador'  => ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => 'Borrador'],
            'enviado'   => ['bg' => '#cce5ff', 'text' => '#004085', 'label' => 'Enviado'],
            'aprobado'  => ['bg' => '#d4edda', 'text' => '#155724', 'label' => 'Aprobado'],
            'rechazado' => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => 'Rechazado'],
            'vencido'   => ['bg' => '#fff3cd', 'text' => '#856404', 'label' => 'Vencido'],
            default     => ['bg' => '#e2e3e5', 'text' => '#383d41', 'label' => ucfirst($this->estado)],
        };
    }

    public function getDiasRestantesAttribute(): int
    {
        return (int) Carbon::today()->diffInDays($this->fecha_vencimiento, false);
    }

    // ── Métodos de negocio ──────────────────────────────────────

    public function calcularTotales(): void
    {
        $subtotal = $this->items()->sum('valor_total');
        $descuentoValor = $subtotal * ($this->descuento_porcentaje / 100);
        $total = $subtotal - $descuentoValor;

        $this->update([
            'subtotal'       => $subtotal,
            'descuento_valor' => $descuentoValor,
            'total'          => $total,
        ]);
    }

    public function aprobar(): void
    {
        $this->update([
            'estado'           => 'aprobado',
            'fecha_aprobacion' => now(),
        ]);

        if (!$this->tratamiento_id) {
            $this->crearTratamiento();
        }
    }

    public function crearTratamiento(): void
    {
        $tratamiento = Tratamiento::create([
            'paciente_id'        => $this->paciente_id,
            'historia_clinica_id' => $this->historia_clinica_id,
            'user_id'            => $this->user_id,
            'nombre'             => 'Plan de Tratamiento - ' . $this->numero_formateado,
            'valor_total'        => $this->total,
            'saldo_pendiente'    => $this->total,
            'estado'             => 'activo',
            'fecha_inicio'       => now()->toDateString(),
        ]);

        $this->update(['tratamiento_id' => $tratamiento->id]);
    }
}
