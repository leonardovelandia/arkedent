<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cita extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'citas';

    protected static $campoPrefijo = 'numero_cita';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cita) {
            if (empty($cita->numero_cita)) {
                $cita->numero_cita = static::generarNumero('CIT', 'numero_cita');
            }
        });
    }

    protected $fillable = [
        'numero_cita',
        'paciente_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'procedimiento',
        'estado',
        'motivo_cancelacion',
        'notas',
        'recordatorio_enviado',
        'activo',
    ];

    protected $casts = [
        'fecha'                => 'date',
        'recordatorio_enviado' => 'boolean',
        'activo'               => 'boolean',
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

    public function valoracion()
    {
        return $this->hasOne(Valoracion::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today())->orderBy('hora_inicio');
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha)->orderBy('hora_inicio');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // ── Helpers ───────────────────────────────────────────────
    public static function coloresPorEstado(): array
    {
        return [
            'pendiente'  => ['bg' => '#FFF3CD', 'texto' => '#856404',  'badge' => 'warning'],
            'confirmada' => ['bg' => 'var(--color-badge-bg)', 'texto' => 'var(--color-badge-texto)', 'badge' => 'morado'],
            'en_proceso' => ['bg' => '#CCE5FF', 'texto' => '#004085',  'badge' => 'info'],
            'atendida'   => ['bg' => '#D4EDDA', 'texto' => '#155724',  'badge' => 'success'],
            'cancelada'  => ['bg' => '#F8D7DA', 'texto' => '#721C24',  'badge' => 'danger'],
            'no_asistio' => ['bg' => '#E2E3E5', 'texto' => '#383D41',  'badge' => 'secondary'],
        ];
    }
}
