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
        $frontHtml = View::make('admin.cursos.diplomas.template2', compact('curso'))->render();
        $backHtml = View::make('admin.cursos.diplomas.template-back', compact('curso'))->render();

        // HTML combinado
        $combinedHtml = $this->generarHtmlContenedor($frontHtml, $backHtml);

        // HTML combinado con separación de páginas
       /* $combinedHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap");
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body { margin: 10 0; padding: 0; font-family: "Roboto", sans-serif; background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                
                @page { size: 210mm 297mm; margin: 0; }
                .page-front { background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%); }
                .page-back { background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%); }
                .page {
                border: 1px solid blue;
                    width: 210mm;
                    height: 297mm;
                    page-break-after: always;
                    page-break-inside: avoid;
                    position: relative;
                    overflow: hidden;
                    margin: 0 auto;
                }
                .page:last-child { page-break-after: avoid; }
            </style>
        </head>
        <body>
            <div class="page page-front">' . $frontHtml . '</div>
            <div class="page page-back">' . $backHtml . '</div>
        </body>
        </html>'; */

        // Configurar Browsershot
        $browsershot = Browsershot::html($combinedHtml)
            ->format('A4')
            ->landscape()
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->noSandbox()
            ->disableGpu()
            ->scale(1) // valor entre 0.1 y 2
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