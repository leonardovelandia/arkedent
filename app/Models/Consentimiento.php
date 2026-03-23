<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Consentimiento extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'consentimientos';

    protected static $campoPrefijo = 'numero_consentimiento';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consentimiento) {
            if (empty($consentimiento->numero_consentimiento)) {
                $consentimiento->numero_consentimiento = static::generarNumero('CON', 'numero_consentimiento');
            }
        });
    }

    protected $fillable = [
        'numero_consentimiento',
        'paciente_id',
        'plantilla_id',
        'user_id',
        'nombre',
        'contenido',
        'fecha_generacion',
        'fecha_firma',
        'firmado',
        'firma_path',
        'firma_data',
        'ip_firma',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_firma'      => 'datetime',
        'firmado'          => 'boolean',
        'activo'           => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaConsentimiento::class, 'plantilla_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getEstadoAttribute(): string
    {
        return $this->firmado ? 'Firmado' : 'Pendiente firma';
    }

    public function getEstadoColorAttribute(): string
    {
        return $this->firmado ? 'success' : 'warning';
    }

    // ── Reemplazar variables del template ─────────────────────
    public function reemplazarVariables(Paciente $paciente, \App\Models\User $doctor): string
    {
        $mapa = [
            '{{nombre_paciente}}'    => $paciente->nombre,
            '{{apellido_paciente}}'  => $paciente->apellido,
            '{{documento_paciente}}' => $paciente->numero_documento,
            '{{fecha}}'              => now()->format('d/m/Y'),
            '{{doctor}}'             => $doctor->name,
            '{{procedimiento}}'      => $this->nombre,
        ];

        return str_replace(array_keys($mapa), array_values($mapa), $this->contenido);
    }
}
