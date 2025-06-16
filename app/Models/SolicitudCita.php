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
        'motivo',
        'fecha_propuesta',
        'estado',
    ];

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }
}
