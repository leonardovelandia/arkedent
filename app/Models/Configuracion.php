<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Configuracion
 * 
 * Almacena los datos globales del consultorio.
 * Se accede desde cualquier vista mediante el View Composer global.
 * 
 * IMPORTANTE: Nunca hardcodear el nombre del consultorio en el código.
 * Siempre usar $config->nombre_consultorio desde las vistas.
 */
class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_consultorio',
        'slogan',
        'nit',
        'registro_medico',
        'telefono',
        'telefono_whatsapp',
        'email',
        'direccion',
        'ciudad',
        'pais',
        'logo_path',
        'firma_path',
        'firma_nombre_doctor',
        'firma_cargo',
        'firma_registro',
        'tema',
        'fuente_principal',
        'fuente_titulos',
        'duracion_cita_minutos',
        'hora_apertura',
        'hora_cierre',
        'formato_hora',
        'dias_laborales',
        'moneda',
        'simbolo_moneda',
        'recordatorios_activos',
        'horas_anticipacion_recordatorio',
        'recordatorios_email_activo',
        'recordatorios_whatsapp_activo',
        'horas_anticipacion',
        'ultramsg_token',
        'ultramsg_instance',
        'twilio_account_sid',
        'twilio_auth_token',
        'twilio_whatsapp_from',
        'whatsapp_provider',
        'mail_from_name',
        'mail_from_address',
        'plantilla_email',
        'plantilla_whatsapp',
        'plantilla_interactiva_whatsapp',
        'plantilla_confirmacion_whatsapp',
        'plantilla_cancelacion_whatsapp',
        'plantilla_reprogramacion_whatsapp',
        'modo_recordatorio',
        'hora_envio_recordatorio',
        'activo',
    ];

    protected $casts = [
        'dias_laborales'                => 'array',
        'recordatorios_activos'         => 'boolean',
        'recordatorios_email_activo'    => 'boolean',
        'recordatorios_whatsapp_activo' => 'boolean',
        'activo'                        => 'boolean',
    ];

    /**
     * Obtiene la configuración activa del consultorio.
     * Siempre devuelve un objeto (crea uno por defecto si no existe).
     */
    public static function obtener(): self
    {
        $config = self::where('activo', true)->first();

        if (!$config) {
            // Si no existe configuración, creamos una por defecto
            $config = self::create([
                'nombre_consultorio' => 'Consultorio Odontológico',
                'activo'             => true,
            ]);
        }

        return $config;
    }

    /**
     * URL completa del logo del consultorio.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }

        return null;
    }
}