<?php
namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\TieneUuid;
use Illuminate\Database\Eloquent\Model;

class FichaPeriodontal extends Model
{
    use GeneraNumeroDocumento, TieneUuid;

    protected $table = 'fichas_periodontales';
    protected static $campoPrefijo = 'numero_ficha';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (FichaPeriodontal $ficha) {
            if (empty($ficha->numero_ficha)) {
                $ficha->numero_ficha = static::generarNumero('PER', 'numero_ficha');
            }
        });
    }

    protected $fillable = [
        'numero_ficha','paciente_id','historia_clinica_id','user_id','fecha_inicio',
        'indice_placa_porcentaje','indice_placa_datos','fecha_indice_placa',
        'indice_gingival_porcentaje','indice_gingival_datos','fecha_indice_gingival',
        'sondaje_datos','fecha_sondaje',
        'clasificacion_periodontal','extension','severidad','factores_riesgo',
        'diagnostico_texto','plan_tratamiento','pronostico_general','pronostico_por_diente',
        'estado','notas','activo',
    ];

    protected $casts = [
        'fecha_inicio'          => 'date:Y-m-d',
        'fecha_indice_placa'    => 'date:Y-m-d',
        'fecha_indice_gingival' => 'date:Y-m-d',
        'fecha_sondaje'         => 'date:Y-m-d',
        'indice_placa_datos'    => 'array',
        'indice_gingival_datos' => 'array',
        'sondaje_datos'         => 'array',
        'factores_riesgo'       => 'array',
        'pronostico_por_diente' => 'array',
        'activo'                => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────
    public function paciente() { return $this->belongsTo(Paciente::class); }
    public function historiaClinica() { return $this->belongsTo(HistoriaClinica::class); }
    public function periodoncista() { return $this->belongsTo(User::class, 'user_id'); }
    public function controles() {
        return $this->hasMany(ControlPeriodontal::class)->orderBy('numero_sesion');
    }
    public function ultimoControl() {
        return $this->hasOne(ControlPeriodontal::class)->orderBy('created_at', 'desc');
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'activa'         => 'success',
            'en_tratamiento' => 'primary',
            'mantenimiento'  => 'warning',
            'finalizada'     => 'secondary',
            'abandonada'     => 'danger',
            default          => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'activa'         => 'Activa',
            'en_tratamiento' => 'En tratamiento',
            'mantenimiento'  => 'Mantenimiento',
            'finalizada'     => 'Finalizada',
            'abandonada'     => 'Abandonada',
            default          => ucfirst($this->estado),
        };
    }

    public function getClasificacionLabelAttribute(): string
    {
        return match($this->clasificacion_periodontal) {
            'salud_periodontal'            => 'Salud periodontal',
            'gingivitis_inducida_placa'    => 'Gingivitis inducida por placa',
            'gingivitis_no_inducida_placa' => 'Gingivitis no inducida por placa',
            'periodontitis_estadio_i'      => 'Periodontitis Estadio I',
            'periodontitis_estadio_ii'     => 'Periodontitis Estadio II',
            'periodontitis_estadio_iii'    => 'Periodontitis Estadio III',
            'periodontitis_estadio_iv'     => 'Periodontitis Estadio IV',
            'periodontitis_necrosante'     => 'Periodontitis necrosante',
            'absceso_periodontal'          => 'Absceso periodontal',
            'lesion_endoperio'             => 'Lesión endo-perio',
            'deformidades_condiciones'     => 'Deformidades y condiciones',
            default                        => $this->clasificacion_periodontal
                ? ucfirst(str_replace('_', ' ', $this->clasificacion_periodontal))
                : '—',
        };
    }

    public function getPorcentajeMejoraAttribute(): ?float
    {
        $ultimo = $this->ultimoControl;
        if (!$ultimo || !$ultimo->sondaje_control || !$this->sondaje_datos) return null;
        $promInicial = $this->calcularPromedioSondaje($this->sondaje_datos);
        $promUltimo  = $this->calcularPromedioSondaje($ultimo->sondaje_control);
        if ($promInicial <= 0) return null;
        return round((($promInicial - $promUltimo) / $promInicial) * 100, 1);
    }

    private function calcularPromedioSondaje(array $datos): float
    {
        $sum = 0; $count = 0;
        foreach ($datos as $diente => $d) {
            foreach (['mv','v','dv','ml','l','dl'] as $punto) {
                if (isset($d[$punto]) && is_numeric($d[$punto])) {
                    $sum += (float)$d[$punto];
                    $count++;
                }
            }
        }
        return $count > 0 ? $sum / $count : 0;
    }

    public function scopeActivas($query) { return $query->where('activo', true); }
}
