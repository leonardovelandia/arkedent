<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\TieneUuid;
use Illuminate\Database\Eloquent\Model;

class AutorizacionDatos extends Model
{
    use GeneraNumeroDocumento, TieneUuid;

    protected $table = 'autorizaciones_datos';

    protected static $campoPrefijo = 'numero_autorizacion';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (AutorizacionDatos $aut) {
            if (empty($aut->numero_autorizacion)) {
                $aut->numero_autorizacion = static::generarNumero('AUT', 'numero_autorizacion');
            }
        });
    }

    protected $fillable = [
        'numero_autorizacion',
        'paciente_id',
        'user_id',
        'fecha_autorizacion',
        'acepta_almacenamiento',
        'acepta_contacto_whatsapp',
        'acepta_contacto_email',
        'acepta_contacto_llamada',
        'acepta_recordatorios',
        'acepta_compartir_entidades',
        'firmado',
        'firma_data',
        'fecha_firma',
        'ip_firma',
        'metodo_firma',
        'observaciones',
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
        'fecha_autorizacion'          => 'date',
        'fecha_firma'                 => 'datetime',
        'firma_timestamp'             => 'datetime',
        'acepta_almacenamiento'       => 'boolean',
        'acepta_contacto_whatsapp'    => 'boolean',
        'acepta_contacto_email'       => 'boolean',
        'acepta_contacto_llamada'     => 'boolean',
        'acepta_recordatorios'        => 'boolean',
        'acepta_compartir_entidades'  => 'boolean',
        'firmado'                     => 'boolean',
        'activo'                      => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getEstadoAttribute(): string
    {
        return $this->firmado ? 'Firmada' : 'Pendiente';
    }

    public function getEstadoColorAttribute(): string
    {
        return $this->firmado ? 'success' : 'warning';
    }
}
