<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\TieneUuid;
use Illuminate\Database\Eloquent\Model;

class ControlOrtodoncia extends Model
{
    use GeneraNumeroDocumento, TieneUuid;

    protected $table = 'controles_ortodoncia';

    protected static $campoPrefijo = 'numero_control';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ControlOrtodoncia $control) {
            if (empty($control->numero_control)) {
                $control->numero_control = static::generarNumero('CORT', 'numero_control');
            }
        });
    }

    protected $fillable = [
        'numero_control', 'ficha_ortodontica_id', 'paciente_id', 'cita_id', 'user_id',
        'fecha_control', 'hora_inicio', 'hora_fin', 'numero_sesion',
        'arco_superior', 'arco_inferior',
        'tipo_arco_superior', 'tipo_arco_inferior',
        'calibre_superior', 'calibre_inferior',
        'ligadura_superior', 'ligadura_inferior', 'color_ligadura',
        'elasticos', 'tipo_elasticos',
        'brackets_reemplazados', 'odontograma_sesion',
        'progreso_porcentaje', 'observaciones',
        'proxima_cita_semanas', 'indicaciones_paciente',
    ];

    protected $casts = [
        'fecha_control'        => 'date',
        'hora_inicio'          => 'datetime:H:i',
        'hora_fin'             => 'datetime:H:i',
        'brackets_reemplazados'=> 'array',
        'odontograma_sesion'   => 'array',
        'elasticos'            => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function fichaOrtodoncia()
    {
        return $this->belongsTo(FichaOrtodoncia::class, 'ficha_ortodontica_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function ortodoncista()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getDuracionAttribute(): ?string
    {
        if (!$this->hora_inicio || !$this->hora_fin) return null;
        $inicio = \Carbon\Carbon::parse($this->hora_inicio);
        $fin    = \Carbon\Carbon::parse($this->hora_fin);
        $mins   = $inicio->diffInMinutes($fin);
        if ($mins <= 0) return null;
        $h = (int) floor($mins / 60);
        $m = $mins % 60;
        if ($h === 0) return "{$m} min";
        return $m > 0 ? "{$h}h {$m}min" : "{$h}h";
    }

    public function getTipoArcoSuperiorColorAttribute(): string
    {
        return match($this->tipo_arco_superior) {
            'niti'        => '#D97706',
            'acero'       => '#6B7280',
            'tma'         => '#7C3AED',
            'fibra_vidrio'=> '#059669',
            default       => '#9CA3AF',
        };
    }

    public function getResumenArcosAttribute(): string
    {
        $sup = implode(' ', array_filter([
            $this->tipo_arco_superior ? strtoupper($this->tipo_arco_superior) : null,
            $this->calibre_superior,
        ]));
        $inf = implode(' ', array_filter([
            $this->tipo_arco_inferior ? strtoupper($this->tipo_arco_inferior) : null,
            $this->calibre_inferior,
        ]));
        return trim(($sup ? "S: {$sup}" : '') . ($inf ? " / I: {$inf}" : ''));
    }
}
