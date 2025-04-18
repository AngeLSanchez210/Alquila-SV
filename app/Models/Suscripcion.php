<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'articulo_id', 'fecha_inicio', 'fecha_fin', 'estado', 'plan'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
