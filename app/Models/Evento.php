<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'cupo_maximo',
    ];

    public function organizador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}