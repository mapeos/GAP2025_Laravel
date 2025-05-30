<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa los tipos de eventos en el sistema
 * 
 * @property int $id
 * @property string $nombre
 * @property string $color
 * @property bool $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */

class TipoEvento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada al modelo
     * 
     * @var string
     */

    protected $table = 'tipos_evento';

    /**
     * Los atributos que son asignables masivamente
     * 
     * @var array<string>
     */

    protected $fillable = [
        'nombre',
        'color',
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
     * Obtiene los eventos asociado a este tipo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'tipo_evento_id');
    }
}
