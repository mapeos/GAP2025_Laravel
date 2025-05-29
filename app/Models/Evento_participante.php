<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoParticipante extends Model
{
    use SoftDeletes;

    protected $table = 'evento_participante';

    protected $fillable = [
        'evento_id',
        'user_id',
        'rol',
        'estado_asistencia',
        'notas',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relación con el evento
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
