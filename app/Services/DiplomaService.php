<?php

namespace App\Services;

use App\Models\Curso;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class DiplomaService
{
    /**
     * Genera el PDF del diploma para un curso dado
     * @param Curso $curso
     * @return string (PDF binario)
     * @throws \Exception
     */
    public function generarDiplomaPdf(Curso $curso): string
    {
        // Renderizar ambas caras
        $frontHtml = View::make('admin.cursos.diplomas.template', compact('curso'))->render();
        $backHtml = View::make('admin.cursos.diplomas.template-back', compact('curso'))->render();

        // HTML combinado con separación de páginas
        $combinedHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap");
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { margin: 0; padding: 0; font-family: "Roboto", sans-serif; background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .page { page-break-after: always; page-break-inside: avoid; height: 100vh; width: 100%; position: relative; display: block; clear: both; margin: 0; padding: 0; overflow: hidden; }
                .page:last-child { page-break-after: avoid; }
                @page { size: A4 portrait; margin: 0; }
                .page-front { background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%); }
                .page-back { background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%); }
            </style>
        </head>
        <body>
            <div class="page page-front">' . $frontHtml . '</div>
            <div class="page page-back">' . $backHtml . '</div>
        </body>
        </html>';

        // Configurar Browsershot
        $browsershot = Browsershot::html($combinedHtml)
            ->format('A4')
            ->portrait()
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->noSandbox()
            ->disableGpu()
            ->timeout(120)
            ->waitUntilNetworkIdle()
            ->preferCssPageSize();

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
} 