<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DiplomaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diploma:generate {curso_id : ID del curso para generar el diploma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un diploma PDF para un curso específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cursoId = $this->argument('curso_id');
        
        try {
            $this->info("Buscando curso con ID: {$cursoId}");
            
            $curso = Curso::withTrashed()->find($cursoId);
            
            if (!$curso) {
                $this->error("No se encontró el curso con ID: {$cursoId}");
                return 1;
            }
            
            $this->info("Curso encontrado: {$curso->nombre}");
            
            // Generar HTML con ambas páginas
            $frontHtml = view('admin.cursos.diplomas.template', compact('curso'))->render();
            $backHtml = view('admin.cursos.diplomas.template-back', compact('curso'))->render();
            
            // Combinar ambas páginas
            $combinedHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap");
                    
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body { 
                        margin: 0; 
                        padding: 0; 
                        font-family: "Roboto", sans-serif;
                        background: white;
                    }
                    
                    .page { 
                        page-break-after: always; 
                        page-break-inside: avoid;
                        height: 100vh;
                        width: 100%;
                        position: relative;
                        display: block;
                        clear: both;
                        margin: 0;
                        padding: 0;
                    }
                    
                    .page:last-child { 
                        page-break-after: avoid; 
                    }
                </style>
            </head>
            <body>
                <div class="page">' . $frontHtml . '</div>
                <div class="page">' . $backHtml . '</div>
            </body>
            </html>';
            
            $this->info("Generando PDF...");
            
            // Configurar Browsershot con opciones optimizadas
            $browsershot = \Spatie\Browsershot\Browsershot::html($combinedHtml)
                ->format('A4')
                ->portrait()
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->noSandbox()
                ->disableGpu()
                ->timeout(120)
                ->waitUntilNetworkIdle()
                ->preferCssPageSize();
            
            // Intentar diferentes rutas de Chrome
            $chromePaths = [
                '/usr/bin/google-chrome',
                '/usr/bin/chromium-browser',
                '/usr/bin/chromium',
                'C:\Program Files\Google\Chrome\Application\chrome.exe',
                'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe'
            ];
            
            foreach ($chromePaths as $path) {
                if (file_exists($path)) {
                    $browsershot->setChromePath($path);
                    $this->info("Chrome encontrado en: {$path}");
                    break;
                }
            }
            
            $pdf = $browsershot->pdf();
            
            // Verificar que el PDF se generó correctamente
            if (empty($pdf) || strlen($pdf) < 1000) {
                throw new \Exception('El PDF generado está vacío o corrupto');
            }
            
            // Crear directorio si no existe
            $diplomaDir = storage_path('app/diplomas');
            if (!file_exists($diplomaDir)) {
                mkdir($diplomaDir, 0755, true);
            }
            
            $filename = 'diploma_' . $curso->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filepath = $diplomaDir . '/' . $filename;
            
            file_put_contents($filepath, $pdf);
            
            $this->info("✅ Diploma generado exitosamente!");
            $this->info("📁 Archivo guardado en: {$filepath}");
            $this->info("📄 Tamaño del archivo: " . number_format(filesize($filepath) / 1024, 2) . " KB");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error al generar el diploma: " . $e->getMessage());
            Log::error('Error en comando diploma:generate: ' . $e->getMessage());
            return 1;
        }
    }
} 