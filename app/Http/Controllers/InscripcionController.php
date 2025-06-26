<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Persona;
use App\Models\RolParticipacion;

class InscripcionController extends Controller
{
    /**
     * Muestra la lista de cursos activos disponibles para inscribir personas.
     * 
     * @return \Illuminate\View\View
     */
    public function cursosActivos()
    {
        // Obtener cursos activos con información de inscritos
        $cursos = Curso::where('estado', 'activo')
            ->withCount(['personas as inscritos_count'])
            ->orderBy('fechaInicio', 'asc')
            ->get();

        return view('admin.inscripciones.cursos-activos', compact('cursos'));
    }

    /**
     * Muestra el formulario para inscribir personas en un curso.
     * 
     * @param int $curso - El ID del curso al que se van a inscribir personas
     * @return \Illuminate\View\View
     */
    public function inscribir($curso)
    {
        // Buscar el curso por ID
        $curso = Curso::findOrFail($curso);
        
        // Obtener todas las personas
        $personasDisponibles = Persona::with('user')->get();
        
        // Obtener las personas ya inscritas en el curso
        $inscritos = $curso->personas()->with('user')->get();
        
        // Obtener todos los roles de participación disponibles
        $rolesParticipacion = RolParticipacion::all();

        return view('admin.inscripciones.inscribir', compact(
            'curso', 
            'personasDisponibles', 
            'inscritos', 
            'rolesParticipacion'
        ));
    }

    /**
     * Inscribe una persona en un curso con validaciones mejoradas.
     * 
     * @param Request $request - Datos del formulario
     * @param int $curso - El ID del curso donde inscribir
     * @return \Illuminate\Http\RedirectResponse
     */
    public function agregarInscripcion(Request $request, $curso)
    {
        // Buscar el curso por ID
        $curso = Curso::findOrFail($curso);
        
        // Validar los datos de entrada
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'rol_participacion_id' => 'required|exists:roles_participacion,id',
        ], [
            'persona_id.required' => 'Debe seleccionar una persona.',
            'persona_id.exists' => 'La persona seleccionada no existe.',
            'rol_participacion_id.required' => 'Debe seleccionar un rol.',
            'rol_participacion_id.exists' => 'El rol seleccionado no existe.',
        ]);

        // Verificar si la persona ya está inscrita en el curso
        $yaInscrito = $curso->personas()->where('persona_id', $request->persona_id)->exists();

        if ($yaInscrito) {
            return redirect()->back()
                ->with('error', 'La persona ya está inscrita en este curso.')
                ->withInput();
        }

        // Verificar si hay plazas disponibles (solo para alumnos)
        $rolSeleccionado = RolParticipacion::find($request->rol_participacion_id);
        if ($rolSeleccionado && strtolower($rolSeleccionado->nombre) === 'alumno') {
            $alumnosInscritos = $curso->personasPorRol('Alumno')->count();
            if ($alumnosInscritos >= $curso->plazas) {
                return redirect()->back()
                    ->with('error', 'El curso ya no tiene plazas disponibles para alumnos.')
                    ->withInput();
            }
        }

        try {
            // Inscribir a la persona en el curso
            $curso->personas()->attach($request->persona_id, [
                'rol_participacion_id' => $request->rol_participacion_id,
                'estado' => 'activo', // Cambiamos de 'pendiente' a 'activo' por defecto
            ]);

            return redirect()->back()
                ->with('success', 'Persona inscrita correctamente en el curso.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al inscribir a la persona: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra el listado de personas inscritas en un curso con información detallada.
     * 
     * @param int $curso - El ID del curso del que mostrar los inscritos
     * @return \Illuminate\View\View
     */
    public function verInscritos($curso)
    {
        // Buscar el curso por ID
        $curso = Curso::findOrFail($curso);
        
        // Obtener las personas inscritas con sus relaciones
        $inscritos = $curso->personas()->with('user')->get();
        
        // Obtener todos los roles para mostrar los nombres
        $roles = RolParticipacion::all()->keyBy('id');

        return view('admin.inscripciones.inscritos', compact('curso', 'inscritos', 'roles'));
    }

    /**
     * Elimina una inscripción de una persona en un curso (dar de baja).
     * 
     * @param int $curso - El ID del curso del que dar de baja
     * @param Persona $persona - La persona a dar de baja
     * @return \Illuminate\Http\RedirectResponse
     */
    public function darBaja($curso, Persona $persona)
    {
        // Buscar el curso por ID
        $curso = Curso::findOrFail($curso);
        
        try {
            // Verificar que la persona esté inscrita en el curso
            $inscrito = $curso->personas()->where('persona_id', $persona->id)->first();
            
            if (!$inscrito) {
                return redirect()->back()
                    ->with('error', 'La persona no está inscrita en este curso.');
            }

            // Dar de baja a la persona
            $curso->personas()->detach($persona->id);

            return redirect()->route('admin.inscripciones.cursos.inscritos', $curso->id)
                ->with('success', 'El participante ha sido dado de baja correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al dar de baja: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza el estado de una inscripción (activo/inactivo).
     * 
     * @param Request $request - Datos del formulario
     * @param int $curso - El ID del curso
     * @param Persona $persona - La persona
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actualizarEstado(Request $request, $curso, Persona $persona)
    {
        // Buscar el curso por ID
        $curso = Curso::findOrFail($curso);
        
        $request->validate([
            'estado' => 'required|in:activo,inactivo,pendiente'
        ]);

        try {
            $curso->personas()->updateExistingPivot($persona->id, [
                'estado' => $request->estado
            ]);

            return redirect()->back()
                ->with('success', 'Estado actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
