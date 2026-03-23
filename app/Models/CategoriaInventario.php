<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaInventario extends Model
{
    protected $table = 'categorias_inventario';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function materiales()
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
