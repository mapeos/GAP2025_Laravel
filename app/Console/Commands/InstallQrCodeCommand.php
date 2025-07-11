<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InstallQrCodeCommand extends Command
{
    protected $signature = 'qr:install';
    protected $description = 'Instala y configura la funcionalidad de códigos QR para diplomas';

    public function handle()
    {
        $this->info('🚀 Instalando funcionalidad de códigos QR para diplomas...');
        
        try {
            // 1. Verificar si la librería está instalada
            $this->info('📦 Verificando librería QR...');
            
            if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $this->warn('⚠️  La librería simple-qrcode no está instalada.');
                $this->info('💡 Ejecuta: composer require simplesoftwareio/simple-qrcode');
                return 1;
            }
            
            $this->info('✅ Librería QR encontrada');
            
            // 2. Crear directorio para QR codes
            $this->info('📁 Creando directorio para QR codes...');
            $qrDir = storage_path('app/public/qrcodes/cursos');
            
            if (!File::exists($qrDir)) {
                File::makeDirectory($qrDir, 0755, true);
                $this->info('✅ Directorio creado: ' . $qrDir);
            } else {
                $this->info('✅ Directorio ya existe: ' . $qrDir);
            }
            
            // 3. Verificar que las rutas públicas estén configuradas
            $this->info('🔗 Verificando rutas públicas...');
            
            $routesFile = base_path('routes/web.php');
            $routesContent = File::get($routesFile);
            
            if (strpos($routesContent, 'CursoPublicController') === false) {
                $this->warn('⚠️  Las rutas públicas no están configuradas.');
                $this->info('💡 Asegúrate de que las rutas estén en routes/web.php');
            } else {
                $this->info('✅ Rutas públicas configuradas');
            }
            
            // 4. Verificar vistas públicas
            $this->info('👁️  Verificando vistas públicas...');
            
            $viewsDir = resource_path('views/public/cursos');
            if (!File::exists($viewsDir)) {
                File::makeDirectory($viewsDir, 0755, true);
                $this->info('✅ Directorio de vistas creado: ' . $viewsDir);
            }
            
            $requiredViews = ['show.blade.php', 'not-found.blade.php', 'error.blade.php'];
            foreach ($requiredViews as $view) {
                $viewPath = $viewsDir . '/' . $view;
                if (File::exists($viewPath)) {
                    $this->info("✅ Vista {$view} encontrada");
                } else {
                    $this->warn("⚠️  Vista {$view} no encontrada");
                }
            }
            
            // 5. Verificar servicios
            $this->info('🔧 Verificando servicios...');
            
            $services = [
                'QrCodeService.php' => app_path('Services/QrCodeService.php'),
                'CursoPublicController.php' => app_path('Http/Controllers/Public/CursoPublicController.php')
            ];
            
            foreach ($services as $service => $path) {
                if (File::exists($path)) {
                    $this->info("✅ {$service} encontrado");
                } else {
                    $this->warn("⚠️  {$service} no encontrado");
                }
            }
            
            // 6. Probar generación de QR
            $this->info('🧪 Probando generación de QR...');
            
            try {
                $qrService = app(\App\Services\QrCodeService::class);
                $testQr = $qrService->generarQrParaCurso(1);
                
                if ($testQr) {
                    $this->info('✅ Generación de QR exitosa');
                } else {
                    $this->warn('⚠️  Generación de QR falló');
                }
            } catch (\Exception $e) {
                $this->error('❌ Error al generar QR: ' . $e->getMessage());
            }
            
            $this->info('🎉 Instalación completada!');
            $this->info('');
            $this->info('📋 Resumen de funcionalidades:');
            $this->info('   • Códigos QR únicos por curso');
            $this->info('   • Verificación pública de diplomas');
            $this->info('   • Vistas responsivas para móviles');
            $this->info('   • Logs de escaneos para tracking');
            $this->info('');
            $this->info('🔗 URL de ejemplo: ' . config('app.url') . '/cursos/1');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la instalación: ' . $e->getMessage());
            Log::error('[QR_INSTALL] Error: ' . $e->getMessage());
            return 1;
        }
    }
} 