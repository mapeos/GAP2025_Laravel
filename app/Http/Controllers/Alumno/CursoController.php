<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Diploma;
use Illuminate\Support\Facades\Auth;

class CursoController extends Controller
{
    /**
     * Mostrar lista de cursos disponibles para el alumno
     */
    public function index()
    {
        $user = Auth::user();
        $persona = $user->persona;
        
        // Obtener cursos activos
        $cursosActivos = Curso::where('estado', 'activo')
            ->where('fechaInicio', '>=', now()->startOfDay())
            ->orderBy('fechaInicio', 'asc')
            ->get();
            
        // Obtener cursos en los que está inscrito el alumno
        $misCursos = collect();
        if ($persona) {
            $misCursos = $persona->cursos()->withPivot('estado')->get();
        }
        
        // Filtrar cursos disponibles (no inscritos)
        $cursosDisponibles = $cursosActivos->filter(function($curso) use ($misCursos) {
            return !$misCursos->contains('id', $curso->id);
        });
        
        return view('alumno.cursos.index', compact('cursosDisponibles', 'misCursos'));
    }

    /**
     * Mostrar detalles de un curso específico
     */
    public function show($id)
    {
        $curso = Curso::findOrFail($id);
        $alumno = Auth::user()->persona;
        $diploma = null;
        
        if ($alumno) {
            $diploma = Diploma::obtenerParaParticipante($curso->id, $alumno->id);
        }
        
        // Verificar si el alumno está inscrito en este curso
        $estaInscrito = false;
        $estadoInscripcion = null;
        
        if ($alumno) {
            $participacion = $alumno->cursos()->where('curso_id', $curso->id)->first();
            if ($participacion) {
                $estaInscrito = true;
                $estadoInscripcion = $participacion->pivot->estado;
            }
        }
        
        return view('alumno.cursos.show', compact('curso', 'diploma', 'estaInscrito', 'estadoInscripcion'));
    }

    /**
     * Solicitar inscripción a un curso
     */
    public function solicitarInscripcion($id)
    {
        try {
            $curso = Curso::findOrFail($id);
            $user = Auth::user();
            $persona = $user->persona;
            
            // Validar que el curso esté activo
            if ($curso->estado !== 'activo') {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'Este curso no está disponible para inscripción.');
            }
            
            // Validar que el curso no haya comenzado
            if ($curso->fechaInicio < now()->startOfDay()) {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'Este curso ya ha comenzado.');
            }
            
            if (!$persona) {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'No tienes un perfil de persona asociado.');
            }
            
            // Verificar si ya existe la participación
            $participacionExistente = \App\Models\Participacion::where('curso_id', $curso->id)
                ->where('persona_id', $persona->id)
                ->first();
            
            if ($participacionExistente) {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'Ya tienes una inscripción en este curso.');
            }
            
            // Verificar que hay plazas disponibles
            if (!$curso->tienePlazasDisponibles()) {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'Este curso no tiene plazas disponibles.');
            }
            
            // Crear la participación
            $participacion = \App\Models\Participacion::crearParticipacionSegura(
                $curso->id,
                $persona->id,
                1, // 1 = alumno
                'pendiente'
            );
            
            if (!$participacion) {
                return redirect()->route('alumno.cursos.show', $curso->id)
                    ->with('error', 'No se pudo procesar la inscripción. Ya podrías estar inscrito.');
            }
            
            return redirect()->route('alumno.cursos.show', $curso->id)
                ->with('success', 'Solicitud de inscripción enviada correctamente. El administrador la revisará.');
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[ALUMNO_SOLICITUD_INSCRIPCION] Error al solicitar inscripción', [
                'curso_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('alumno.cursos.show', $id)
                ->with('error', 'Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
        }
    }
} 