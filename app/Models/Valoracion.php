<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'valoraciones';

    protected static $campoPrefijo = 'numero_valoracion';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($valoracion) {
            if (empty($valoracion->numero_valoracion)) {
                $valoracion->numero_valoracion = static::generarNumero('VAL', 'numero_valoracion');
            }
        });
    }

    protected $fillable = [
        'numero_valoracion',
        'paciente_id',
        'historia_clinica_id',
        'cita_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo_consulta',
        'extraoral_cara',
        'extraoral_atm',
        'extraoral_ganglios',
        'extraoral_labios',
        'extraoral_observaciones',
        'intraoral_encias',
        'intraoral_mucosa',
        'intraoral_lengua',
        'intraoral_paladar',
        'intraoral_higiene',
        'intraoral_observaciones',
        'diagnosticos',
        'plan_tratamiento',
        'pronostico',
        'observaciones_generales',
        'odontograma',
        'hallazgos',
        'presupuesto_id',
        'estado',
        'activo',
    ];

    protected $casts = [
        'fecha'            => 'date',
        'hora_inicio'      => 'datetime:H:i',
        'hora_fin'         => 'datetime:H:i',
        'diagnosticos'     => 'array',
        'plan_tratamiento' => 'array',
        'odontograma'      => 'array',
        'hallazgos'        => 'array',
        'activo'           => 'boolean',
    ];

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

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function getNumeroFormateadoAttribute(): string
    {
        return $this->numero_valoracion ?? '#' . $this->id;
    }

    public function getEstadoColorAttribute(): array
    {
        return match ($this->estado) {
            'en_proceso'  => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'En proceso'],
            'completada'  => ['bg' => '#d1fae5', 'text' => '#166534', 'label' => 'Completada'],
            'cancelada'   => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Cancelada'],
            default       => ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => ucfirst($this->estado)],
        };
    }

    public function getPronosticoColorAttribute(): array
    {
        return match ($this->pronostico) {
            'excelente' => ['bg' => '#d1fae5', 'text' => '#166534', 'label' => 'Excelente'],
            'bueno'     => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Bueno'],
            'reservado' => ['bg' => '#fef9c3', 'text' => '#854d0e', 'label' => 'Reservado'],
            'malo'      => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Malo'],
            default     => ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => '—'],
        };
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
}
