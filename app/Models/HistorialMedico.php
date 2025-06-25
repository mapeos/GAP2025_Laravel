<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historial_medico';

    protected $fillable = [
        'persona_id',
        'cita_id',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'proxima_cita',
        'estado_seguimiento',
    ];

    protected $casts = [
        'proxima_cita' => 'date',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function cita()
    {
        return $this->belongsTo(SolicitudCita::class, 'cita_id');
    }

    public function scopePorPersona($query, $personaId)
    {
        return $query->where('persona_id', $personaId);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado_seguimiento', $estado);
    }

    public function scopeRecientes($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado_seguimiento) {
            'pendiente' => 'warning',
            'en_proceso' => 'info',
            'completado' => 'success',
            default => 'secondary'
        };
    }
} 