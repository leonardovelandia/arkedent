<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class CorreccionEvolucion extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'correcciones_evolucion';

    protected static $campoPrefijo = 'numero_correccion';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($correccion) {
            if (empty($correccion->numero_correccion)) {
                $correccion->numero_correccion = static::generarNumero('CRE', 'numero_correccion');
            }
        });
    }

    protected $fillable = [
        'numero_correccion',
        'evolucion_id',
        'user_id',
        'campo_corregido',
        'valor_anterior',
        'valor_nuevo',
        'motivo',
        'firmado',
        'firma_data',
        'fecha_firma',
        'ip_firma',
    ];

    protected $casts = [
        'firmado'     => 'boolean',
        'fecha_firma' => 'datetime',
    ];

    // ── Accessors ─────────────────────────────────────────────
    public function getEstadoFirmaAttribute(): string
    {
        return $this->firmado ? 'Firmada' : 'Pendiente firma';
    }

    public function getEstadoFirmaColorAttribute(): string
    {
        return $this->firmado ? 'success' : 'warning';
    }

    public function getCampoLabelAttribute(): string
    {
        $campos = [
            'procedimiento'              => 'Procedimiento',
            'descripcion'                => 'Descripción clínica',
            'materiales'                 => 'Materiales utilizados',
            'presion_arterial'           => 'Presión arterial',
            'frecuencia_cardiaca'        => 'Frecuencia cardiaca',
            'dientes_tratados'           => 'Dientes tratados',
            'proxima_cita_procedimiento' => 'Próxima cita — procedimiento',
            'observaciones'              => 'Observaciones',
        ];

        return $campos[$this->campo_corregido] ?? $this->campo_corregido;
    }

    // ── Relaciones ────────────────────────────────────────────
    public function evolucion()
    {
        return $this->belongsTo(Evolucion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
