<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Diploma;
use App\Models\Curso;
use App\Models\Persona;
use App\Services\DiplomaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegenerarDiplomasExistentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diplomas:regenerar-existentes {--curso= : ID del curso específico} {--participante= : ID del participante específico} {--force : Forzar regeneración sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenera todos los diplomas existentes para incluir códigos QR';

    protected $diplomaService;

    public function __construct(DiplomaService $diplomaService)
    {
        parent::__construct();
        $this->diplomaService = $diplomaService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando regeneración de diplomas existentes...');
        
        // Obtener parámetros
        $cursoId = $this->option('curso');
        $participanteId = $this->option('participante');
        $force = $this->option('force');
        
        // Confirmación si no es forzado
        if (!$force) {
            if (!$this->confirm('¿Estás seguro de que quieres regenerar todos los diplomas existentes? Esto puede tomar varios minutos.')) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }
        
        try {
            // Construir query base
            $query = Diploma::query();
            
            if ($cursoId) {
                $query->where('curso_id', $cursoId);
                $this->info("📚 Filtrando por curso ID: {$cursoId}");
            }
            
            if ($participanteId) {
                $query->where('participante_id', $participanteId);
                $this->info("👤 Filtrando por participante ID: {$participanteId}");
            }
            
            // Obtener diplomas
            $diplomas = $query->get();
            
            if ($diplomas->isEmpty()) {
                $this->warn('⚠️ No se encontraron diplomas para regenerar.');
                return 0;
            }
            
            $this->info("📄 Se encontraron {$diplomas->count()} diplomas para regenerar.");
            
            // Barra de progreso
            $progressBar = $this->output->createProgressBar($diplomas->count());
            $progressBar->start();
            
            $exitosos = 0;
            $errores = 0;
            
            foreach ($diplomas as $diploma) {
                try {
                    // Obtener curso y participante
                    $curso = Curso::find($diploma->curso_id);
                    $participante = Persona::find($diploma->participante_id);
                    
                    if (!$curso || !$participante) {
                        $this->error("❌ Diploma ID {$diploma->id}: Curso o participante no encontrado");
                        $errores++;
                        $progressBar->advance();
                        continue;
                    }
                    
                    // Regenerar diploma con QR
                    $resultado = $this->diplomaService->generarDiplomaParaParticipante(
                        $curso->id,
                        $participante->id,
                        true // forzar regeneración
                    );
                    
                    if ($resultado) {
                        $exitosos++;
                        $this->line(" ✅ Diploma regenerado: Curso '{$curso->titulo}' - Participante '{$participante->getNombreCompletoAttribute()}'");
                    } else {
                        $errores++;
                        $this->error(" ❌ Error al regenerar diploma ID {$diploma->id}");
                    }
                    
                } catch (\Exception $e) {
                    $errores++;
                    Log::error('[REGENERAR_DIPLOMAS] Error al regenerar diploma', [
                        'diploma_id' => $diploma->id,
                        'error' => $e->getMessage()
                    ]);
                    $this->error(" ❌ Error en diploma ID {$diploma->id}: {$e->getMessage()}");
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Resumen final
            $this->info("🎉 Regeneración completada:");
            $this->info(" ✅ Diplomas regenerados exitosamente: {$exitosos}");
            
            if ($errores > 0) {
                $this->warn(" ⚠️ Errores encontrados: {$errores}");
            }
            
            // Información adicional
            $this->newLine();
            $this->info("📋 Información adicional:");
            $this->info(" • Los diplomas ahora incluyen códigos QR");
            $this->info(" • Los QR apuntan a páginas de verificación públicas");
            $this->info(" • Puedes escanear los QR para verificar autenticidad");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("💥 Error general: {$e->getMessage()}");
            Log::error('[REGENERAR_DIPLOMAS] Error general', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
