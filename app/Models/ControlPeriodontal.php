<?php
namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class ControlPeriodontal extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'controles_periodontales';
    protected static $campoPrefijo = 'numero_control';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ControlPeriodontal $ctrl) {
            if (empty($ctrl->numero_control)) {
                $ctrl->numero_control = static::generarNumero('CPER', 'numero_control');
            }
        });
    }

    protected $fillable = [
        'numero_control','ficha_periodontal_id','paciente_id','user_id',
        'fecha_control','hora_inicio','hora_fin','numero_sesion','tipo_sesion',
        'zonas_tratadas','sondaje_control',
        'indice_placa_control','indice_gingival_control',
        'anestesia_utilizada','instrumentos_utilizados',
        'observaciones','indicaciones_paciente','proxima_cita_semanas',
    ];

    protected $casts = [
        'fecha_control'   => 'date:Y-m-d',
        'hora_inicio'     => 'datetime:H:i',
        'hora_fin'        => 'datetime:H:i',
        'zonas_tratadas'  => 'array',
        'sondaje_control' => 'array',
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

    public function fichaPeriodontal() { return $this->belongsTo(FichaPeriodontal::class); }
    public function paciente() { return $this->belongsTo(Paciente::class); }
    public function periodoncista() { return $this->belongsTo(User::class, 'user_id'); }

    public function getTipoSesionLabelAttribute(): string
    {
        return match($this->tipo_sesion) {
            'raspado_alisado'     => 'Raspado y alisado radicular',
            'curetaje'            => 'Curetaje',
            'cirugia_periodontal' => 'Cirugía periodontal',
            'mantenimiento'       => 'Mantenimiento periodontal',
            'reevaluacion'        => 'Reevaluación',
            'otro'                => 'Otro',
            default               => ucfirst($this->tipo_sesion ?? ''),
        };
    }

    public function getTipoSesionColorAttribute(): string
    {
        return match($this->tipo_sesion) {
            'raspado_alisado'     => '#0ea5e9',
            'curetaje'            => '#8b5cf6',
            'cirugia_periodontal' => '#ef4444',
            'mantenimiento'       => '#22c55e',
            'reevaluacion'        => '#f59e0b',
            default               => '#6b7280',
        };
    }
}
