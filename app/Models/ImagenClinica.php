<?php

namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use App\Traits\TieneUuid;
use Illuminate\Database\Eloquent\Model;

class ImagenClinica extends Model
{
    use GeneraNumeroDocumento, TieneUuid;

    protected $table = 'imagenes_clinicas';

    protected static $campoPrefijo = 'numero_imagen';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($imagen) {
            if (empty($imagen->numero_imagen)) {
                $imagen->numero_imagen = static::generarNumero('IMG', 'numero_imagen');
            }
        });
    }

    protected $fillable = [
        'numero_imagen',
        'paciente_id',
        'historia_clinica_id',
        'evolucion_id',
        'user_id',
        'tipo',
        'titulo',
        'descripcion',
        'archivo_path',
        'archivo_nombre',
        'archivo_tipo',
        'archivo_tamanio',
        'diente',
        'fecha_toma',
        'es_comparativo',
        'grupo_comparativo',
        'orden_comparativo',
        'activo',
    ];

    protected $casts = [
        'fecha_toma'     => 'date',
        'es_comparativo' => 'boolean',
        'activo'         => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function evolucion()
    {
        return $this->belongsTo(Evolucion::class);
    }

    public function autor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->archivo_path);
    }

    public function getTipoLabelAttribute(): string
    {
        $tipos = [
            'fotografia_intraoral'   => 'Fotografía Intraoral',
            'fotografia_extraoral'   => 'Fotografía Extraoral',
            'radiografia_periapical' => 'Radiografía Periapical',
            'radiografia_panoramica' => 'Radiografía Panorámica',
            'radiografia_bitewing'   => 'Radiografía Bitewing',
            'foto_antes'             => 'Foto Antes del Tratamiento',
            'foto_durante'           => 'Foto Durante el Tratamiento',
            'foto_despues'           => 'Foto Después del Tratamiento',
            'foto_sonrisa'           => 'Foto de Sonrisa',
            'otra'                   => 'Otra',
        ];
        return $tipos[$this->tipo] ?? $this->tipo;
    }

    public function getTipoIconoAttribute(): string
    {
        $iconos = [
            'fotografia_intraoral'   => 'bi-camera',
            'fotografia_extraoral'   => 'bi-person-bounding-box',
            'radiografia_periapical' => 'bi-radioactive',
            'radiografia_panoramica' => 'bi-film',
            'radiografia_bitewing'   => 'bi-film',
            'foto_antes'             => 'bi-arrow-left-circle',
            'foto_durante'           => 'bi-play-circle',
            'foto_despues'           => 'bi-arrow-right-circle',
            'foto_sonrisa'           => 'bi-emoji-smile',
            'otra'                   => 'bi-image',
        ];
        return $iconos[$this->tipo] ?? 'bi-image';
    }

    public function getTamanioFormateadoAttribute(): string
    {
        if (!$this->archivo_tamanio) return '—';
        $bytes = $this->archivo_tamanio;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeComparativas($query)
    {
        return $query->where('es_comparativo', true);
    }
}
