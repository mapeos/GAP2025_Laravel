<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Message;
use App\Notifications\CustomPasswordResetNotification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'user_agent', // Permite asignación masiva del user-agent
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var list<string>
     */
    protected $dates = ['deleted_at'];


    // Relaciones para acceder al nombre de quien realizó las acciones.
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function persona()
    {
        return $this->hasOne(Persona::class);
    }

    // Relación con mensajes enviados y recibidos (ajustada a ChatMessage)
    public function mensajesEnviados()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordResetNotification($token, $this->email));
    }
}
