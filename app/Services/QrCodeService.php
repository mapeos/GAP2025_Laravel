<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Genera un código QR para un curso específico
     * 
     * @param int $cursoId
     * @return string Base64 del QR generado
     */
    public function generarQrParaCurso(int $cursoId): string
    {
        try {
            // Generar URL pública del curso
            $url = $this->generarUrlPublicaCurso($cursoId);
            
            // Generar QR como imagen SVG
            $qrCode = QrCode::size(200)
                ->format('svg')
                ->style('square')
                ->eye('square')
                ->margin(1)
                ->errorCorrection('M')
                ->generate($url);
            
            // Convertir SVG a base64 para incrustar en HTML
            $base64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            
            Log::info('[QR_CODE] QR generado para curso', [
                'curso_id' => $cursoId,
                'url' => $url,
                'tamaño' => strlen($qrCode)
            ]);
            
            return $base64;
            
        } catch (\Exception $e) {
            Log::error('[QR_CODE] Error al generar QR', [
                'curso_id' => $cursoId,
                'error' => $e->getMessage()
            ]);
            
            // Retornar un QR de fallback
            return $this->generarQrFallback($cursoId);
        }
    }
    
    /**
     * Genera la URL pública para un curso
     * 
     * @param int $cursoId
     * @return string
     */
    private function generarUrlPublicaCurso(int $cursoId): string
    {
        // URL base desde configuración o usar la actual
        $baseUrl = config('app.url', 'http://localhost');
        
        // Ruta pública del curso (se creará)
        return $baseUrl . '/cursos/' . $cursoId;
    }
    
    /**
     * Genera un QR de fallback en caso de error
     * 
     * @param int $cursoId
     * @return string
     */
    private function generarQrFallback(int $cursoId): string
    {
        $url = $this->generarUrlPublicaCurso($cursoId);
        
        // QR simple como fallback
        $qrCode = QrCode::size(200)
            ->format('svg')
            ->generate($url);
            
        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }
    
    /**
     * Genera y guarda un QR como archivo
     * 
     * @param int $cursoId
     * @param string $formato 'svg', 'png', 'eps'
     * @return string Ruta del archivo guardado
     */
    public function generarYGuardarQr(int $cursoId, string $formato = 'svg'): string
    {
        try {
            $url = $this->generarUrlPublicaCurso($cursoId);
            
            // Generar QR
            $qrCode = QrCode::size(300)
                ->format($formato)
                ->style('square')
                ->eye('square')
                ->margin(2)
                ->errorCorrection('M')
                ->generate($url);
            
            // Crear directorio si no existe
            $qrDir = 'qrcodes/cursos';
            if (!Storage::disk('public')->exists($qrDir)) {
                Storage::disk('public')->makeDirectory($qrDir);
            }
            
            // Nombre del archivo
            $filename = "curso_{$cursoId}_qr.{$formato}";
            $filepath = "{$qrDir}/{$filename}";
            
            // Guardar archivo
            Storage::disk('public')->put($filepath, $qrCode);
            
            Log::info('[QR_CODE] QR guardado', [
                'curso_id' => $cursoId,
                'archivo' => $filepath,
                'formato' => $formato
            ]);
            
            return $filepath;
            
        } catch (\Exception $e) {
            Log::error('[QR_CODE] Error al guardar QR', [
                'curso_id' => $cursoId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Verifica si existe un QR guardado para un curso
     * 
     * @param int $cursoId
     * @return bool
     */
    public function existeQrGuardado(int $cursoId): bool
    {
        $filepath = "qrcodes/cursos/curso_{$cursoId}_qr.svg";
        return Storage::disk('public')->exists($filepath);
    }
    
    /**
     * Obtiene la URL del QR guardado
     * 
     * @param int $cursoId
     * @return string|null
     */
    public function obtenerUrlQrGuardado(int $cursoId): ?string
    {
        $filepath = "qrcodes/cursos/curso_{$cursoId}_qr.svg";
        
        if (Storage::disk('public')->exists($filepath)) {
            return asset('storage/' . $filepath);
        }
        
        return null;
    }
} 