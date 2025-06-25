<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TratamientoMedico extends Model
{
    use HasFactory;

    protected $table = 'tratamientos_medicos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
        'duracion_minutos',
        'especialidad_id',
        'activo',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function especialidad()
    {
        return $this->belongsTo(EspecialidadMedica::class, 'especialidad_id');
    }

    public function solicitudesCitas()
    {
        return $this->hasMany(SolicitudCita::class, 'tratamiento_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getCostoFormateadoAttribute()
    {
        return '€' . number_format($this->costo, 2);
    }

    public function getDuracionFormateadaAttribute()
    {
        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }
        
        return $minutos . ' minutos';
    }
} 