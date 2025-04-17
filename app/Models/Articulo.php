<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'estado', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }


    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'articulo_categoria');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }

    public function puntuaciones()
    {
        return $this->hasMany(Puntuacion::class, 'articulo_id');
    }
    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class);
    }

   
    public function imagenes()
    {
        return $this->hasMany(ImgArticulo::class, 'articulo_id');
    }
}
