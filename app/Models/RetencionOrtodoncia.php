<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetencionOrtodoncia extends Model
{
    protected $table = 'retencion_ortodoncia';

    protected $fillable = [
        'ficha_ortodontica_id', 'paciente_id', 'user_id',
        'fecha_retiro_brackets', 'tipo_retenedor_superior', 'tipo_retenedor_inferior',
        'fecha_entrega_retenedor', 'instrucciones_uso',
        'duracion_retencion_meses', 'controles_retencion',
        'estado', 'notas',
    ];

    protected $casts = [
        'fecha_retiro_brackets'   => 'date',
        'fecha_entrega_retenedor' => 'date',
        'controles_retencion'     => 'array',
    ];

    public function fichaOrtodoncia()
    {
        return $this->belongsTo(FichaOrtodoncia::class, 'ficha_ortodontica_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function ortodoncista()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'Pendiente',
            'activa'     => 'Activa',
            'finalizada' => 'Finalizada',
            default      => ucfirst($this->estado),
        };
    }

    public function getRetenedorSuperiorLabelAttribute(): string
    {
        return match($this->tipo_retenedor_superior) {
            'fijo_alambre'       => 'Fijo de alambre',
            'removible_hawley'   => 'Hawley removible',
            'alineador_retencion'=> 'Alineador de retención',
            'ninguno'            => 'Ninguno',
            default              => '—',
        };
    }

    public function getRetenedorInferiorLabelAttribute(): string
    {
        return match($this->tipo_retenedor_inferior) {
            'fijo_alambre'       => 'Fijo de alambre',
            'removible_hawley'   => 'Hawley removible',
            'alineador_retencion'=> 'Alineador de retención',
            'ninguno'            => 'Ninguno',
            default              => '—',
        };
    }
}
