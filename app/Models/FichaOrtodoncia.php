<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\TieneUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FichaOrtodoncia extends Model
{
    use GeneraNumeroDocumento, TieneUuid;

    protected $table = 'fichas_ortodonticas';

    protected static $campoPrefijo = 'numero_ficha';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (FichaOrtodoncia $ficha) {
            if (empty($ficha->numero_ficha)) {
                $ficha->numero_ficha = static::generarNumero('ORT', 'numero_ficha');
            }
        });
    }

    protected $fillable = [
        'numero_ficha', 'paciente_id', 'historia_clinica_id', 'user_id',
        'fecha_inicio', 'fecha_fin_estimada', 'fecha_fin_real', 'duracion_meses_estimada',
        // Análisis facial
        'perfil', 'simetria_facial', 'biotipo_facial', 'analisis_facial_notas',
        // Análisis dental
        'clase_molar_derecha', 'clase_molar_izquierda',
        'clase_canina_derecha', 'clase_canina_izquierda',
        'overjet', 'overbite',
        'linea_media_superior', 'linea_media_inferior', 'desviacion_mm',
        'apinamiento_superior', 'apinamiento_inferior',
        'espaciamiento_superior', 'espaciamiento_inferior',
        'mordida_cruzada_anterior', 'mordida_cruzada_posterior',
        'mordida_abierta', 'mordida_profunda',
        // Tratamiento
        'tipo_ortodoncia', 'marca_brackets',
        'extracciones_indicadas', 'odontograma_ortodoncia',
        'arco_inicial_superior', 'arco_inicial_inferior',
        'diagnostico', 'plan_tratamiento', 'pronostico',
        'costo_total', 'estado', 'notas', 'activo',
    ];

    protected $casts = [
        'fecha_inicio'            => 'date',
        'fecha_fin_estimada'      => 'date',
        'fecha_fin_real'          => 'date',
        'extracciones_indicadas'  => 'array',
        'odontograma_ortodoncia'  => 'array',
        'espaciamiento_superior'  => 'boolean',
        'espaciamiento_inferior'  => 'boolean',
        'mordida_cruzada_anterior'=> 'boolean',
        'mordida_cruzada_posterior'=> 'boolean',
        'mordida_abierta'         => 'boolean',
        'mordida_profunda'        => 'boolean',
        'activo'                  => 'boolean',
        'overjet'                 => 'decimal:1',
        'overbite'                => 'decimal:1',
        'desviacion_mm'           => 'decimal:1',
        'costo_total'             => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function ortodoncista()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function controles()
    {
        return $this->hasMany(ControlOrtodoncia::class, 'ficha_ortodontica_id')
            ->orderBy('numero_sesion');
    }

    public function retencion()
    {
        return $this->hasOne(RetencionOrtodoncia::class, 'ficha_ortodontica_id');
    }

    public function ultimoControl()
    {
        return $this->hasOne(ControlOrtodoncia::class, 'ficha_ortodontica_id')
            ->orderBy('created_at', 'desc');
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'diagnostico' => 'info',
            'activo'      => 'success',
            'retencion'   => 'warning',
            'finalizado'  => 'secondary',
            'cancelado'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'diagnostico' => 'Diagnóstico',
            'activo'      => 'En tratamiento',
            'retencion'   => 'Retención',
            'finalizado'  => 'Finalizado',
            'cancelado'   => 'Cancelado',
            default       => ucfirst($this->estado),
        };
    }

    public function getProgresoAttribute(): int
    {
        $ultimo = $this->ultimoControl;
        if ($ultimo && $ultimo->progreso_porcentaje !== null) {
            return $ultimo->progreso_porcentaje;
        }
        if ($this->duracion_meses_estimada && $this->duracion_meses_estimada > 0) {
            $mesesTranscurridos = Carbon::parse($this->fecha_inicio)->diffInMonths(now());
            return min(100, (int) round(($mesesTranscurridos / $this->duracion_meses_estimada) * 100));
        }
        return 0;
    }

    public function getDuracionRealAttribute(): int
    {
        return Carbon::parse($this->fecha_inicio)->diffInMonths(
            $this->fecha_fin_real ?? now()
        );
    }

    public function getTipoOrtodonciaLabelAttribute(): string
    {
        return match($this->tipo_ortodoncia) {
            'fija_metal'      => 'Fija metálica',
            'fija_estetica'   => 'Fija estética',
            'fija_autoligado' => 'Autoligado',
            'removible'       => 'Removible',
            'alineadores'     => 'Alineadores',
            default           => $this->tipo_ortodoncia ?? '—',
        };
    }

    public function getClaseMolarLabelAttribute(): string
    {
        $d = $this->clase_molar_derecha ? strtoupper(str_replace('clase_', 'Cl. ', $this->clase_molar_derecha)) : '—';
        $i = $this->clase_molar_izquierda ? strtoupper(str_replace('clase_', 'Cl. ', $this->clase_molar_izquierda)) : '—';
        return "D: {$d} / I: {$i}";
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }
}
