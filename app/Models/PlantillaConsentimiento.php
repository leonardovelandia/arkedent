<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaConsentimiento extends Model
{
    protected $table = 'plantillas_consentimiento';

    protected $fillable = [
        'nombre',
        'tipo',
        'contenido',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function consentimientos()
    {
        return $this->hasMany(Consentimiento::class, 'plantilla_id');
    }
}
