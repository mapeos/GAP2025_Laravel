<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_os',
        'device_token',
        'app_version',
        'extra_data', // JSON para cualquier otro dato
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
