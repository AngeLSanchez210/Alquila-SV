<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table = 'planes_alquila'; // Nombre de la tabla en la base de datos

    protected $fillable = ['nombre', 'max_publicaciones', 'destacar', 'precio'];
}
