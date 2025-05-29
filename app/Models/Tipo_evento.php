<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEvento extends Model
{
    use SoftDeletes;

    protected $table = 'tipos_evento';

    protected $fillable = [
        'nombre',
        'color',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relación con eventos
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'tipo_evento_id');
    }
}
