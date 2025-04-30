<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImgUser extends Model
{
    use HasFactory;

    protected $table = 'img_user';

    protected $fillable = ['user_id', 'image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
