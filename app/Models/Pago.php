<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos'; // Especificar el nombre correcto de la tabla

    protected $fillable = [
        'metodo',
        'monto',
        'estado',
        'transaccion_id',
        'fecha_pago',
        'user_id',
        'plan_id',
    ];

    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class);
    }
}
