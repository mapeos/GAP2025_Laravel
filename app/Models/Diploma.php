<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Diploma extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'persona_id',
        'fecha_expedicion',
        'path_pdf',
        'otros_datos',
    ];

    protected $casts = [
        'fecha_expedicion' => 'date',
        'otros_datos' => 'array',
    ];

    /**
     * Curso al que pertenece este diploma
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Persona que recibió este diploma
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Verificar si el diploma existe para un curso y persona específicos
     */
    public static function existeParaParticipante(int $cursoId, int $personaId): bool
    {
        return static::where('curso_id', $cursoId)
                    ->where('persona_id', $personaId)
                    ->exists();
    }

    /**
     * Obtener diploma por curso y persona
     */
    public static function obtenerParaParticipante(int $cursoId, int $personaId): ?self
    {
        return static::where('curso_id', $cursoId)
                    ->where('persona_id', $personaId)
                    ->first();
    }

    /**
     * Generar nombre único para el archivo PDF
     */
    public function generarNombreArchivo(): string
    {
        $cursoSlug = Str::slug($this->curso->titulo ?? 'curso');
        $personaSlug = Str::slug($this->persona->nombre ?? 'persona');
        $fecha = $this->fecha_expedicion->format('Y-m-d');
        
        return "diploma_{$cursoSlug}_{$personaSlug}_{$fecha}.pdf";
    }

    /**
     * Obtener la URL pública del PDF
     */
    public function getUrlPdfAttribute(): string
    {
        return asset('storage/' . $this->path_pdf);
    }

    /**
     * Verificar si el archivo PDF existe
     */
    public function existeArchivoPdf(): bool
    {
        return Storage::disk('public')->exists($this->path_pdf);
    }
}
