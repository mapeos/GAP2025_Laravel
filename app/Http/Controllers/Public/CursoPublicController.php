<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CursoPublicController extends Controller
{
    /**
     * Mostrar información pública de un curso
     * Esta es la vista que se muestra al escanear el QR
     */
    public function show($id)
    {
        try {
            // Buscar el curso (incluyendo soft deleted para mostrar información histórica)
            $curso = Curso::withTrashed()->findOrFail($id);
            
            // Log para tracking de escaneos
            Log::info('[QR_CODE] Curso consultado públicamente', [
                'curso_id' => $id,
                'titulo' => $curso->titulo,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            // Preparar datos para la vista
            $datos = [
                'curso' => $curso,
                'fechaActual' => now(),
                'esActivo' => !$curso->trashed() && $curso->estado === 'activo',
                'plazasDisponibles' => $curso->getPlazasDisponibles(),
                'totalInscritos' => $curso->getInscritosCount(),
                'porcentajeOcupacion' => $curso->plazas > 0 ? round(($curso->getInscritosCount() / $curso->plazas) * 100, 1) : 0
            ];
            
            return view('public.cursos.show', $datos);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('[QR_CODE] Curso no encontrado', [
                'curso_id' => $id,
                'ip' => request()->ip()
            ]);
            
            return view('public.cursos.not-found', [
                'cursoId' => $id,
                'mensaje' => 'El curso solicitado no existe o ha sido eliminado.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('[QR_CODE] Error al mostrar curso público', [
                'curso_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return view('public.cursos.error', [
                'mensaje' => 'Error al cargar la información del curso.'
            ]);
        }
    }
    
    /**
     * API para verificar la existencia de un curso
     * Útil para validaciones AJAX
     */
    public function verificar($id)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'curso' => [
                    'id' => $curso->id,
                    'titulo' => $curso->titulo,
                    'estado' => $curso->estado,
                    'fecha_inicio' => $curso->fechaInicio?->format('d/m/Y'),
                    'fecha_fin' => $curso->fechaFin?->format('d/m/Y'),
                    'plazas' => $curso->plazas,
                    'inscritos' => $curso->getInscritosCount(),
                    'disponibles' => $curso->getPlazasDisponibles(),
                    'es_activo' => !$curso->trashed() && $curso->estado === 'activo',
                    'tiene_precio' => !empty($curso->precio),
                    'precio' => $curso->precio ? number_format($curso->precio, 2) . ' €' : 'Gratuito'
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el curso'
            ], 500);
        }
    }
} 