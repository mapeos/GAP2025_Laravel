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
}
