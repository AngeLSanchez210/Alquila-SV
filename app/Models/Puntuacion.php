<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puntuacion extends Model
{
    use HasFactory;
    protected $table = 'puntuaciones';

    protected $fillable = ['usuario_id', 'articulo_id', 'puntuacion', 'comentario'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }
}
