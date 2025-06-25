<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultativo extends Model
{
    use HasFactory;

    protected $table = 'facultativos';

    protected $fillable = [
        'user_id',
        'numero_colegiado',
        'especialidad_id',
        'horario_inicio',
        'horario_fin',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'horario_inicio' => 'datetime',
        'horario_fin' => 'datetime',
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(EspecialidadMedica::class, 'especialidad_id');
    }

    public function solicitudesCitas()
    {
        return $this->hasMany(SolicitudCita::class, 'profesor_id', 'user_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->user->name ?? 'Sin nombre';
    }

    public function getHorarioFormateadoAttribute()
    {
        return $this->horario_inicio->format('H:i') . ' - ' . $this->horario_fin->format('H:i');
    }
} 