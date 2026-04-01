<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recordatorio extends Model
{
    protected $table = 'recordatorios';

    protected $fillable = [
        'cita_id',
        'paciente_id',
        'tipo',
        'canal',
        'estado',
        'mensaje',
        'fecha_programada',
        'fecha_envio',
        'respuesta_api',
        'intentos',
        'error',
        'activo',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_envio'      => 'datetime',
        'activo'           => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')->where('activo', true);
    }

    public function scopeProgramadosParaHoy($query)
    {
        return $query->whereDate('fecha_programada', Carbon::today())->where('activo', true);
    }
}
