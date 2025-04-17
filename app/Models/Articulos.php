<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Articulos extends Model
{

    
    protected $table = 'articulos';

   
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'categoria_id',
    ];

   
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}