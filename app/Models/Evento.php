<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa los eventos en el sistema
 * 
 * @property int $id
 * @property string $titulo
 * @property string|null $descripcion
 * @property \Carbon\Carbon $fecha_inicio
 * @property \Carbon\Carbon $fecha_fin
 * @property string|null $ubicacion
 * @property string|null $url_virtual
 * @property int $tipo_evento_id
 * @property int $creado_por
 * @property bool $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */

class Evento extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     *La tabla asociada al modelo.
     * 
     *@var string 
     */    
    protected $table = 'eventos';    
    
    /**
     * Los atributos que son asignables masivamente.
     * 
     * @var array<string>
     */
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'url_virtual',
        'tipo_evento_id',
        'creado_por',
        'status'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Obtiene el tipo de evento asociado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }


    /**
     * Obtiene el usuario que creó el evento
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Obtiene los participantes del evento
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participantes()
    {
        return $this->belongsToMany(User::class, 'evento_participante')
                    ->withPivot('rol', 'estado_asistencia', 'notas')
                    ->withTimestamps();
    }
}
