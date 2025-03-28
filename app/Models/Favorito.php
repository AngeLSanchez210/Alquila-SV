<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'articulo_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
