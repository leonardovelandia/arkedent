<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RecetaMedica extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'recetas_medicas';

    protected static $campoPrefijo = 'numero_receta';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RecetaMedica $receta) {
            if (empty($receta->numero_receta)) {
                $receta->numero_receta = static::generarNumero('RECMED', 'numero_receta');
            }
        });
    }

    protected $fillable = [
        'numero_receta',
        'paciente_id',
        'user_id',
        'evolucion_id',
        'fecha',
        'diagnostico',
        'medicamentos',
        'indicaciones_generales',
        'firmado',
        'firma_data',
        'fecha_firma',
        'ip_firma',
        'activo',
    ];

    protected $casts = [
        'fecha'       => 'date',
        'medicamentos'=> 'array',
        'firmado'     => 'boolean',
        'fecha_firma' => 'datetime',
        'activo'      => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function evolucion()
    {
        return $this->belongsTo(Evolucion::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getEstadoFirmaAttribute(): string
    {
        return $this->firmado ? 'Firmada' : 'Pendiente';
    }

    public function getEstadoFirmaColorAttribute(): string
    {
        return $this->firmado ? 'success' : 'warning';
    }

    public function getTotalMedicamentosAttribute(): int
    {
        return count($this->medicamentos ?? []);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeFirmadas($query)
    {
        return $query->where('firmado', true);
    }
}
