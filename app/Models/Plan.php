<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes_alquila'; // Especificar el nombre correcto de la tabla

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion',
        'max_publicaciones',
        'precio',
        'destacar',
    ];
}
