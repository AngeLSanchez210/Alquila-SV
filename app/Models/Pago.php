<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'metodo',
        'monto',
        'estado',
        'transaccion_id',
        'fecha_pago',
    ];

    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class);
    }
}
