<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'historias_clinicas';

    protected static $campoPrefijo = 'numero_historia';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($historia) {
            if (empty($historia->numero_historia)) {
                $historia->numero_historia = static::generarNumero('HC', 'numero_historia');
            }
        });
    }

    protected $fillable = [
        'numero_historia',
        'paciente_id',
        'fecha_apertura',
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes_medicos',
        'medicamentos_actuales',
        'alergias',
        'antecedentes_familiares',
        'antecedentes_odontologicos',
        'habitos',
        'presion_arterial',
        'frecuencia_cardiaca',
        'temperatura',
        'peso',
        'talla',
        'odontograma',
        'hallazgos',
        'observaciones_generales',
        'activo',
        'firmado',
        'firma_data',
        'fecha_firma',
        'ip_firma',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
        'odontograma'    => 'array',
        'hallazgos'      => 'array',
        'activo'         => 'boolean',
        'firmado'        => 'boolean',
        'fecha_firma'    => 'datetime',
    ];

    // ── Accessors ─────────────────────────────────────────────
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

    public function evoluciones()
    {
        return $this->hasMany(Evolucion::class)->orderBy('fecha', 'desc');
    }

    public function correcciones()
    {
        return $this->hasMany(CorreccionHistoria::class)->orderBy('created_at', 'desc');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenClinica::class)
            ->where('activo', true)
            ->orderBy('fecha_toma', 'desc');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
