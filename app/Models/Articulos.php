<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Articulos extends Model
{

    // Nombre de la tabla asociada
    protected $table = 'articulos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'categoria_id',
    ];

    // Relación con otra tabla (por ejemplo, categorías)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}