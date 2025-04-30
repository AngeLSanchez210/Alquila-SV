<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'direccion',
        'telefono',
        'role',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }

    public function puntuaciones()
    {
        return $this->hasMany(Puntuacion::class, 'usuario_id');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function suscripcionActiva()
{
    return $this->hasOne(Suscripcion::class)
        ->where('estado', 'activa'); 
}

    public function seguidores()
    {
        return $this->hasMany(Seguidor::class, 'seguido_id');
    }

    public function seguidos()
    {
        return $this->hasMany(Seguidor::class, 'seguidor_id');
    }

    public function image()
    {
        return $this->hasOne(ImgUser::class);
    }
}
