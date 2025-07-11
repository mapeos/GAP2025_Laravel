<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCita extends Model
{
    use HasFactory;

    protected $table = 'solicitud_citas';

    protected $fillable = [
        'alumno_id',
        'profesor_id',
        'facultativo_id',
        'motivo',
        'fecha_propuesta',
        'estado',
        // Campos médicos
        'tipo_sistema',
        'especialidad_id',
        'tratamiento_id',
        'sintomas',
        'diagnostico',
        'costo',
        'duracion_minutos',
        'observaciones_medicas',
        'fecha_proxima_cita',
    ];

    protected $casts = [
        'fecha_propuesta' => 'datetime',
        'fecha_proxima_cita' => 'date',
        'costo' => 'decimal:2',
    ];

    // Relaciones académicas (existentes)
    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    // Relación con facultativo (nueva)
    public function facultativo()
    {
        return $this->belongsTo(Facultativo::class, 'facultativo_id');
    }

    // Relaciones médicas (nuevas)
    public function especialidad()
    {
        return $this->belongsTo(EspecialidadMedica::class, 'especialidad_id');
    }

    public function tratamiento()
    {
        return $this->belongsTo(TratamientoMedico::class, 'tratamiento_id');
    }

    public function historialMedico()
    {
        return $this->hasOne(HistorialMedico::class, 'cita_id');
    }

    // Scopes
    public function scopeAcademicas($query)
    {
        return $query->where('tipo_sistema', 'academico');
    }

    public function scopeMedicas($query)
    {
        return $query->where('tipo_sistema', 'medico');
    }

    public function scopePorEspecialidad($query, $especialidadId)
    {
        return $query->where('especialidad_id', $especialidadId);
    }

    // Accessors
    public function getEsTipoMedicoAttribute()
    {
        return $this->tipo_sistema === 'medico';
    }

    public function getEsTipoAcademicoAttribute()
    {
        return $this->tipo_sistema === 'academico';
    }

    public function getCostoFormateadoAttribute()
    {
        return $this->costo ? '€' . number_format($this->costo, 2) : 'No especificado';
    }

    public function getDuracionFormateadaAttribute()
    {
        if (!$this->duracion_minutos) return 'No especificada';
        
        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }
        
        return $minutos . ' minutos';
    }
}
