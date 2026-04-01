<?php
namespace App\Models;

use App\Traits\GeneraNumeroDocumento;
use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{
    use GeneraNumeroDocumento;

    protected $table = 'importaciones';
    protected static $campoPrefijo = 'numero_importacion';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Importacion $imp) {
            if (empty($imp->numero_importacion)) {
                $imp->numero_importacion = static::generarNumero('IMP', 'numero_importacion');
            }
        });
    }

    protected $fillable = [
        'numero_importacion','user_id','fuente','tipo_datos',
        'archivo_nombre','archivo_path',
        'total_registros','registros_importados','registros_omitidos',
        'registros_error','registros_duplicados',
        'estado','log_importacion','errores',
        'puede_revertir','fecha_importacion','notas','activo',
    ];

    protected $casts = [
        'log_importacion'   => 'array',
        'errores'           => 'array',
        'puede_revertir'    => 'boolean',
        'activo'            => 'boolean',
        'fecha_importacion' => 'datetime',
    ];

    public function registradoPor() { return $this->belongsTo(User::class, 'user_id'); }
    public function detalles() { return $this->hasMany(ImportacionDetalle::class); }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'secondary',
            'procesando' => 'primary',
            'completado' => 'success',
            'error'      => 'danger',
            'revertido'  => 'warning',
            default      => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'Pendiente',
            'procesando' => 'Procesando',
            'completado' => 'Completado',
            'error'      => 'Error',
            'revertido'  => 'Revertido',
            default      => ucfirst($this->estado),
        };
    }

    public function getPorcentajeExitoAttribute(): float
    {
        if (!$this->total_registros) return 0;
        return round(($this->registros_importados / $this->total_registros) * 100, 1);
    }

    public function getFuenteLabelAttribute(): string
    {
        return match($this->fuente) {
            'dentox'          => 'Dentox',
            'odontosof'       => 'OdontoSoft',
            'dentalpro'       => 'DentalPro',
            'excel_generico'  => 'Excel Genérico',
            'csv_generico'    => 'CSV Genérico',
            'sql_dump'        => 'SQL Dump',
            default           => ucfirst($this->fuente),
        };
    }

    public function getTipoDatosLabelAttribute(): string
    {
        return match($this->tipo_datos) {
            'pacientes'       => 'Pacientes',
            'historia_clinica'=> 'Historia Clínica',
            'citas'           => 'Citas',
            'tratamientos'    => 'Tratamientos',
            'pagos'           => 'Pagos',
            'evoluciones'     => 'Evoluciones',
            'consentimientos' => 'Consentimientos',
            'todo'            => 'Todo',
            default           => ucfirst($this->tipo_datos),
        };
    }
}
