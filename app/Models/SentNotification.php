<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentNotification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_ids', // JSON array of user IDs
    ];

    protected $casts = [
        'user_ids' => 'array',
    ];

    /**
     * Obtener los usuarios destinatarios de la notificación.
     * Devuelve una colección de modelos User.
     */
    public function users()
    {
        return User::whereIn('id', $this->user_ids ?? [])->get();
    }
}
