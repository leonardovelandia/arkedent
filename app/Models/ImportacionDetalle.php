<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ImportacionDetalle extends Model
{
    protected $fillable = [
        'importacion_id','fila_numero','datos_originales','datos_transformados',
        'modelo','registro_id','estado','mensaje',
    ];
    protected $casts = [
        'datos_originales'   => 'array',
        'datos_transformados'=> 'array',
    ];
    public function importacion() { return $this->belongsTo(Importacion::class); }
}
