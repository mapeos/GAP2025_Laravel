<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'dni',
        'tfno',
        'direccion_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Relación user personas
    }
    
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'participacion')
            ->withPivot('rol_participacion_id')
            ->withTimestamps();
    }
    
}
