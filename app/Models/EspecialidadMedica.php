<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspecialidadMedica extends Model
{
    use HasFactory;

    protected $table = 'especialidades_medicas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function tratamientos()
    {
        return $this->hasMany(TratamientoMedico::class, 'especialidad_id');
    }

    public function facultativos()
    {
        return $this->hasMany(Facultativo::class, 'especialidad_id');
    }

    public function solicitudesCitas()
    {
        return $this->hasMany(SolicitudCita::class, 'especialidad_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
} 