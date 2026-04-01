<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class Evolucion extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'evoluciones';

    protected static $campoPrefijo = 'numero_evolucion';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($evolucion) {
            if (empty($evolucion->numero_evolucion)) {
                $evolucion->numero_evolucion = static::generarNumero('EVO', 'numero_evolucion');
            }
        });
    }

    protected $fillable = [
        'numero_evolucion',
        'paciente_id',
        'historia_clinica_id',
        'user_id',
        'fecha',
        'hora',
        'procedimiento',
        'descripcion',
        'materiales',
        'presion_arterial',
        'frecuencia_cardiaca',
        'proxima_cita_fecha',
        'proxima_cita_procedimiento',
        'observaciones',
        'dientes_tratados',
        'activo',
        'firmado',
        'firma_data',
        'fecha_firma',
        'ip_firma',
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
        'fecha'              => 'date',
        'proxima_cita_fecha' => 'date:Y-m-d',
        'materiales'         => 'array',
        'activo'             => 'boolean',
        'firmado'            => 'boolean',
        'fecha_firma'        => 'datetime',
        'firma_timestamp'    => 'datetime',
    ];

    // ── Accessors ─────────────────────────────────────────────
    public function getFechaFormateadaAttribute(): string
    {
        return \Carbon\Carbon::parse($this->fecha)->format('d/m/Y');
    }

    public function getEstadoFirmaAttribute(): string
    {
        return $this->firmado ? 'Firmado' : 'Pendiente firma';
    }

    public function getEstadoFirmaColorAttribute(): string
    {
        return $this->firmado ? 'success' : 'warning';
    }

    // ── Relaciones ────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function correcciones()
    {
        return $this->hasMany(CorreccionEvolucion::class)->orderBy('created_at', 'desc');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function recetasMedicas()
    {
        return $this->hasMany(RecetaMedica::class);
    }

    // Evolución editable solo si no está firmada Y tiene menos de 24 horas
    public function getEsEditableAttribute(): bool
    {
        if (!$this->firmado) {
            return $this->created_at->diffInHours(now()) < 24;
        }
        return false;
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
