<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class CorreccionHistoria extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'correcciones_historia';

    protected static $campoPrefijo = 'numero_correccion';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($correccion) {
            if (empty($correccion->numero_correccion)) {
                $correccion->numero_correccion = static::generarNumero('CRH', 'numero_correccion');
            }
        });
    }

    protected $fillable = [
        'numero_correccion',
        'historia_clinica_id',
        'user_id',
        'campo_corregido',
        'valor_anterior',
        'valor_nuevo',
        'motivo',
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
        'firmado'          => 'boolean',
        'fecha_firma'      => 'datetime',
        'firma_timestamp'  => 'datetime',
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
            'motivo_consulta'            => 'Motivo de consulta',
            'enfermedad_actual'          => 'Enfermedad actual',
            'antecedentes_medicos'       => 'Antecedentes médicos',
            'medicamentos_actuales'      => 'Medicamentos actuales',
            'alergias'                   => 'Alergias',
            'antecedentes_familiares'    => 'Antecedentes familiares',
            'antecedentes_odontologicos' => 'Antecedentes odontológicos',
            'habitos'                    => 'Hábitos',
            'presion_arterial'           => 'Presión arterial',
            'frecuencia_cardiaca'        => 'Frecuencia cardiaca',
            'temperatura'                => 'Temperatura',
            'peso'                       => 'Peso',
            'talla'                      => 'Talla',
            'observaciones_generales'    => 'Observaciones generales',
        ];

        return $campos[$this->campo_corregido] ?? $this->campo_corregido;
    }

    // ── Relaciones ────────────────────────────────────────────
    public function historia()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
