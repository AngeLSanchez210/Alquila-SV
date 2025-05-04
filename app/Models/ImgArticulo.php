<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImgArticulo extends Model
{
    use HasFactory;

    protected $fillable = ['articulo_id', 'link'];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
 
    }

  

}
