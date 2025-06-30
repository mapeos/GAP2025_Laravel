<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id_origen',
        'user_id_destino',
        'mensaje',
        'leido',
    ];

    public function origen()
    {
        return $this->belongsTo(User::class, 'user_id_origen');
    }

    public function destino()
    {
        return $this->belongsTo(User::class, 'user_id_destino');
    }
}
