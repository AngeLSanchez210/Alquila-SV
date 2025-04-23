<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'fecha_inicio', 'fecha_fin', 'estado', 'plan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($suscripcion) {
            if (!$suscripcion->fecha_fin) {
                $suscripcion->fecha_fin = $suscripcion->fecha_inicio->copy()->addDays(30);
            }
        });
    }

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

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
