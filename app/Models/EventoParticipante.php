<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa la relación entre eventos y participantes
 * 
 * @property int $id
 * @property int $evento_id
 * @property int $user_id
 * @property string $rol
 * @property string $estado_asistencia
 * @property string|null $notas
 * @property bool $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */

class EventoParticipante extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada al modelo
     * 
     * @var string
     */

    protected $table = 'evento_participante';

    /**
     * Los atributos que son asignables masivamente
     * 
     * @var array<string>
     */

    protected $fillable = [
        'evento_id',
        'user_id',
        'rol',
        'estado_asistencia',
        'notas',
        'status',
    ];

    /**
     * Los atributos que deben ser convertidos
     * 
     * @var array<string, string>
     */

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Obtiene el evento asociado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    /**
     * Obtiene el usuario participante
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
