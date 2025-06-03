<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para la gestión de tokens API independientes.
 * Esta tabla puede ser utilizada por otros equipos para gestionar tokens
 * de acceso a la API, asociados a usuarios y/o dispositivos.
 */
class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'token',
        'type',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con dispositivo (opcional)
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
