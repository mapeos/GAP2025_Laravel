<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Curso;
use App\Models\Participacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class SolicitudInscripcionController extends Controller
{
    /**
     * Mostrar la lista de solicitudes de inscripción
     */
    public function index(Request $request)
    {
        // Obtener todas las solicitudes de inscripción
        $solicitudes = $this->obtenerSolicitudes($request);
        
        // Si es una petición AJAX, devolver solo la tabla
        if ($request->ajax()) {
            return view('admin.solicitudes._tabla_paginada', ['solicitudes' => $solicitudes]);
        }
        
        return view('admin.solicitudes.index', ['solicitudes' => $solicitudes]);
    }

    /**
     * Mostrar detalles de una solicitud específica
     */
    public function show($cursoId, $personaId)
    {
        $persona = Persona::with('user')->findOrFail($personaId);
        $curso = Curso::findOrFail($cursoId);
        
        // Asegurar que las fechas sean objetos Carbon
        if (is_string($curso->fechaInicio)) {
            $curso->fechaInicio = \Carbon\Carbon::parse($curso->fechaInicio);
        }
        if (is_string($curso->fechaFin)) {
            $curso->fechaFin = \Carbon\Carbon::parse($curso->fechaFin);
        }
        
        // Obtener la participación específica
        $participacion = Participacion::where('curso_id', $cursoId)
            ->where('persona_id', $personaId)
            ->first();
            
        if (!$participacion) {
            return redirect()->route('admin.solicitudes.index')
                ->with('error', 'Solicitud no encontrada.');
        }

        return view('admin.solicitudes.show', compact('persona', 'curso', 'participacion'));
    }

    /**
     * Actualizar el estado de una solicitud
     */
    public function update(Request $request, $cursoId, $personaId)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,activo,rechazado'
        ]);

        try {
            Log::info('[SOLICITUD_INSCRIPCION] Iniciando actualización', [
                'curso_id' => $cursoId,
                'persona_id' => $personaId,
                'nuevo_estado' => $request->estado,
                'admin_id' => Auth::id()
            ]);

            // Verificar que existe la participación
            $participacionExistente = \Illuminate\Support\Facades\DB::table('participacion')
                ->where('curso_id', $cursoId)
                ->where('persona_id', $personaId)
                ->first();

            if (!$participacionExistente) {
                Log::warning('[SOLICITUD_INSCRIPCION] Participación no encontrada', [
                    'curso_id' => $cursoId,
                    'persona_id' => $personaId
                ]);
                return redirect()->back()->with('error', 'Solicitud no encontrada.');
            }

            Log::info('[SOLICITUD_INSCRIPCION] Participación encontrada', [
                'estado_actual' => $participacionExistente->estado
            ]);

            $estadoAnterior = $participacionExistente->estado;
            
            // Actualizar usando DB::table() directamente
            $resultado = \Illuminate\Support\Facades\DB::table('participacion')
                ->where('curso_id', $cursoId)
                ->where('persona_id', $personaId)
                ->update([
                    'estado' => $request->estado,
                    'updated_at' => now()
                ]);

            Log::info('[SOLICITUD_INSCRIPCION] Resultado de actualización', [
                'filas_afectadas' => $resultado,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->estado
            ]);

            // Verificar que se actualizó correctamente
            $participacionActualizada = \Illuminate\Support\Facades\DB::table('participacion')
                ->where('curso_id', $cursoId)
                ->where('persona_id', $personaId)
                ->first();

            Log::info('[SOLICITUD_INSCRIPCION] Verificación post-actualización', [
                'estado_verificado' => $participacionActualizada->estado,
                'coincide' => $participacionActualizada->estado === $request->estado
            ]);

            return redirect()->back()->with('success', 'Estado de la solicitud actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('[SOLICITUD_INSCRIPCION] Error al actualizar estado', [
                'curso_id' => $cursoId,
                'persona_id' => $personaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al actualizar el estado de la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Obtener solicitudes con filtros
     */
    private function obtenerSolicitudes(Request $request)
    {
        $query = Persona::whereHas('cursos', function($q) use ($request) {
            $estados = ['pendiente', 'activo', 'rechazado'];
            
            // Aplicar filtro por estado si se especifica
            if ($request->filled('estado')) {
                // Manejar múltiples estados separados por coma
                if (str_contains($request->estado, ',')) {
                    $estados = explode(',', $request->estado);
                } else {
                    $estados = [$request->estado];
                }
            }
            
            $q->whereIn('participacion.estado', $estados);
        })->with(['cursos' => function($q) {
            $q->withPivot('estado', 'created_at');
        }, 'user']);

        $solicitudes = $query->get()
            ->flatMap(function($persona) {
                return $persona->cursos->map(function($curso) use ($persona) {
                    // Asegurar que las fechas sean objetos Carbon
                    if (is_string($curso->fechaInicio)) {
                        $curso->fechaInicio = \Carbon\Carbon::parse($curso->fechaInicio);
                    }
                    if (is_string($curso->fechaFin)) {
                        $curso->fechaFin = \Carbon\Carbon::parse($curso->fechaFin);
                    }
                    
                    $curso->pivot->persona = $persona;
                    $curso->curso = $curso;
                    return $curso;
                });
            })
            ->sortByDesc(function($solicitud) {
                return $solicitud->pivot->created_at ?? $solicitud->created_at;
            })
            ->values();

        // Aplicar filtros adicionales
        if ($request->filled('estado')) {
            if (str_contains($request->estado, ',')) {
                $estadosFiltro = explode(',', $request->estado);
                $solicitudes = $solicitudes->filter(function($solicitud) use ($estadosFiltro) {
                    return in_array($solicitud->pivot->estado, $estadosFiltro);
                });
            } else {
                $solicitudes = $solicitudes->filter(function($solicitud) use ($request) {
                    return $solicitud->pivot->estado === $request->estado;
                });
            }
        }

        // Paginación manual
        $perPage = 10;
        $page = $request->input('page', 1);
        
        return new LengthAwarePaginator(
            $solicitudes->forPage($page, $perPage),
            $solicitudes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
} 