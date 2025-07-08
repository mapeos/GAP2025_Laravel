<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Diploma;
use App\Models\Participacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DiplomaController extends Controller
{
    /**
     * Mostrar vista general de diplomas
     */
    public function index()
    {
        // Obtener todas las participaciones con relaciones
        $participantes = Participacion::with(['curso', 'persona.user', 'rol'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($participacion) {
                // Agregar información sobre si existe el diploma
                $participacion->diploma_existe = Diploma::existeParaParticipante(
                    $participacion->curso_id, 
                    $participacion->persona_id
                );
                return $participacion;
            });
        
        // Paginar manualmente
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedParticipantes = $participantes->slice($offset, $perPage);
        
        // Crear paginador manual
        $participantes = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedParticipantes,
            $participantes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
        
        return view('admin.diplomas.index', compact('participantes'));
    }

    /**
     * Generar todos los diplomas pendientes del sistema
     */
    public function generarTodos(Request $request)
    {
        try {
            $participaciones = Participacion::with(['curso', 'persona'])
                ->whereDoesntHave('diploma')
                ->get();
            
            $count = 0;
            $errors = [];
            
            // Importar el servicio de diplomas
            $diplomaService = app(\App\Services\DiplomaService::class);
            
            foreach ($participaciones as $participacion) {
                try {
                    // Verificar que el curso y la persona existan
                    if (!$participacion->curso || !$participacion->persona) {
                        $errors[] = "Participación {$participacion->id}: Curso o persona no encontrada";
                        continue;
                    }
                    
                    // Verificar si ya existe el diploma
                    if (!Diploma::existeParaParticipante($participacion->curso_id, $participacion->persona_id)) {
                        $diplomaService->generarYGuardarDiploma($participacion->curso, $participacion->persona);
                        $count++;
                    }
                } catch (\Exception $e) {
                    $personaNombre = $participacion->persona->nombre ?? 'Participante';
                    $cursoTitulo = $participacion->curso->titulo ?? 'Curso';
                    $errors[] = "Error con {$personaNombre} en {$cursoTitulo}: " . $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Se generaron {$count} diplomas correctamente",
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al generar todos los diplomas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar los diplomas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar cuántos diplomas hay generados
     */
    public function verificarDiplomas()
    {
        try {
            $count = Diploma::count();
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Hay {$count} diplomas generados"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al verificar diplomas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar los diplomas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar todos los diplomas del sistema en un archivo ZIP
     */
    public function descargarTodos()
    {
        try {
            $diplomas = Diploma::with(['curso', 'persona'])->get();
            
            if ($diplomas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay diplomas generados'
                ], 404);
            }
            
            $zip = new \ZipArchive();
            $zipName = "todos_los_diplomas_" . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipName);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('No se pudo crear el archivo ZIP');
            }
            
            foreach ($diplomas as $diploma) {
                if ($diploma->existeArchivoPdf()) {
                    $pdfPath = Storage::disk('public')->path($diploma->path_pdf);
                    $personaNombre = $diploma->persona->nombre ?? 'Participante';
                    $cursoTitulo = $diploma->curso->titulo ?? 'Curso';
                    $zipFileName = "diploma_{$personaNombre}_{$cursoTitulo}.pdf";
                    $zip->addFile($pdfPath, $zipFileName);
                }
            }
            
            $zip->close();
            
            return response()->download($zipPath, $zipName, [
                'Content-Type' => 'application/zip',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache'
            ])->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el archivo ZIP: ' . $e->getMessage()
            ], 500);
        }
    }
} 