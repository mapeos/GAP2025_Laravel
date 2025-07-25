<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Persona;
use App\Models\Diploma;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class DiplomaService
{
    protected $qrCodeService;
    
    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Genera el PDF del diploma para un curso dado
     * @param Curso $curso
     * @return string (PDF binario)
     * @throws \Exception
     */
    public function generarDiplomaPdf(Curso $curso): string
    {
        // Generar QR para el curso
        $qrCode = $this->qrCodeService->generarQrParaCurso($curso->id);
        
        // Renderizar ambas caras con el QR
        $frontHtml = View::make('admin.cursos.diplomas.template2', compact('curso'))->render();
        $backHtml = View::make('admin.cursos.diplomas.template-back', compact('curso', 'qrCode'))->render();

        // HTML combinado
        $combinedHtml = $this->generarHtmlContenedor($frontHtml, $backHtml);

        return $this->generarPdfDesdeHtml($combinedHtml);
    }

    /**
     * Genera el PDF del diploma para un participante específico
     * @param Curso $curso
     * @param Persona $persona
     * @return string (PDF binario)
     * @throws \Exception
     */
    public function generarDiplomaPdfParaParticipante(Curso $curso, Persona $persona): string
    {
        // Generar QR para el curso
        $qrCode = $this->qrCodeService->generarQrParaCurso($curso->id);
        
        // Renderizar ambas caras con datos del participante y el QR
        $frontHtml = View::make('admin.cursos.diplomas.template2', compact('curso', 'persona'))->render();
        $backHtml = View::make('admin.cursos.diplomas.template-back', compact('curso', 'persona', 'qrCode'))->render();

        // HTML combinado
        $combinedHtml = $this->generarHtmlContenedor($frontHtml, $backHtml);

        return $this->generarPdfDesdeHtml($combinedHtml);
    }

    /**
     * Genera y guarda el diploma para un participante específico
     * @param Curso $curso
     * @param Persona $persona
     * @return Diploma
     * @throws \Exception
     */
    public function generarYGuardarDiploma(Curso $curso, Persona $persona): Diploma
    {
        // Verificar si ya existe un diploma para este participante en este curso
        if (Diploma::existeParaParticipante($curso->id, $persona->id)) {
            throw new \Exception('Ya existe un diploma para este participante en este curso');
        }

        // Generar el PDF
        $pdfContent = $this->generarDiplomaPdfParaParticipante($curso, $persona);

        // Crear el registro del diploma
        $diploma = new Diploma();
        $diploma->curso_id = $curso->id;
        $diploma->persona_id = $persona->id;
        $diploma->fecha_expedicion = now()->toDateString();
        
        // Generar nombre único para el archivo
        $nombreArchivo = $diploma->generarNombreArchivo();
        $diploma->path_pdf = 'diplomas/' . $nombreArchivo;
        
        // Guardar el PDF en storage
        Storage::disk('public')->put($diploma->path_pdf, $pdfContent);
        
        // Guardar el registro en la base de datos
        $diploma->save();

        Log::info("Diploma generado para participante {$persona->id} en curso {$curso->id}", [
            'curso_id' => $curso->id,
            'persona_id' => $persona->id,
            'archivo' => $diploma->path_pdf
        ]);

        return $diploma;
    }

    /**
     * Genera diploma para un participante (soporta regeneración forzada)
     * @param int $cursoId
     * @param int $participanteId
     * @param bool $forzarRegeneracion
     * @return bool
     */
    public function generarDiplomaParaParticipante(int $cursoId, int $participanteId, bool $forzarRegeneracion = false): bool
    {
        try {
            $curso = Curso::findOrFail($cursoId);
            $participante = Persona::findOrFail($participanteId);
            
            // Verificar si ya existe un diploma
            $diplomaExistente = Diploma::where('curso_id', $cursoId)
                ->where('participante_id', $participanteId)
                ->first();
            
            if ($diplomaExistente && !$forzarRegeneracion) {
                Log::info("Diploma ya existe para participante {$participanteId} en curso {$cursoId}");
                return true; // Ya existe, consideramos éxito
            }
            
            // Si existe y queremos regenerar, eliminar el anterior
            if ($diplomaExistente && $forzarRegeneracion) {
                // Eliminar archivo anterior
                if (Storage::disk('public')->exists($diplomaExistente->path_pdf)) {
                    Storage::disk('public')->delete($diplomaExistente->path_pdf);
                }
                
                // Eliminar registro
                $diplomaExistente->delete();
                
                Log::info("Diploma anterior eliminado para regeneración", [
                    'curso_id' => $cursoId,
                    'participante_id' => $participanteId
                ]);
            }
            
            // Generar nuevo diploma
            $diploma = $this->generarYGuardarDiploma($curso, $participante);
            
            Log::info("Diploma generado exitosamente", [
                'curso_id' => $cursoId,
                'participante_id' => $participanteId,
                'diploma_id' => $diploma->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al generar diploma", [
                'curso_id' => $cursoId,
                'participante_id' => $participanteId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Genera el PDF desde HTML usando Browsershot
     * @param string $html
     * @return string (PDF binario)
     * @throws \Exception
     */
    private function generarPdfDesdeHtml(string $html): string
    {
        // Configurar Browsershot
        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->landscape()
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->noSandbox()
            ->disableGpu()
            ->scale(1)
            ->timeout(120)
            ->waitUntilNetworkIdle()
            ->preferCssPageSize(); // Dejar que el CSS defina el tamaño

        // Detección automática de Chrome
        $chromePaths = [
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe'
        ];
        foreach ($chromePaths as $path) {
            if (file_exists($path)) {
                $browsershot->setChromePath($path);
                break;
            }
        }

        $pdf = $browsershot->pdf();

        // Verificar que el PDF se generó correctamente
        if (empty($pdf) || strlen($pdf) < 1000) {
            throw new \Exception('El PDF generado está vacío o corrupto');
        }

        return $pdf;
    }

    /**
     * Envuelve ambas páginas HTML en una estructura común imprimible.
     *
     * @param string $frontHtml
     * @param string $backHtml
     * @return string
     */
    private function generarHtmlContenedor(string $frontHtml, string $backHtml): string
    {
        return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            @page { size: A4 landscape; margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Roboto', sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white;
            }
            .page {
                width: 297mm;
                height: 210mm;
                page-break-after: always;
                page-break-inside: avoid;
                overflow: hidden;
                position: relative;
            }
            .page:last-child {
                page-break-after: avoid;
            }
        </style>
        <link href='https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap' rel='stylesheet'>
        </head>
        <body>
            <div class='page page-front'>
                {$frontHtml}
            </div>
            <div class='page page-back'>
                {$backHtml}
            </div>
        </body>
        </html>
        ";
    }
} 