<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Persona;
use Illuminate\Support\Facades\Log;

class InscripcionController extends Controller
{
    /**
     * Muestra el formulario para inscribir personas en un curso.
     */
    public function inscribir(Curso $curso)
    {
        $personas = Persona::all(); // Obtiene todas las personas disponibles
        $inscritos = $curso->personas; // Obtiene las personas inscritas en el curso
        $rolesParticipacion = \App\Models\RolParticipacion::all(); // Obtiene todos los roles de participación

        return view('admin.inscripciones.inscribir', compact('curso', 'personas', 'inscritos', 'rolesParticipacion'));
    }

    /**
     * Inscribe una persona en un curso.
     */
    public function agregarInscripcion(Request $request, Curso $curso)
    {
        Log::info('[INSCRIPCION] Intentando inscribir persona', [
            'curso_id' => $curso->id,
            'persona_id' => $request->persona_id,
            'rol_participacion_id' => $request->rol_participacion_id
        ]);
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'rol_participacion_id' => 'required|exists:roles_participacion,id',
        ]);

        $yaInscrito = $curso->personas()->where('persona_id', $request->persona_id)->exists();
        Log::info('[INSCRIPCION] ¿Ya inscrito?', ['yaInscrito' => $yaInscrito]);

        if ($yaInscrito) {
            Log::warning('[INSCRIPCION] Persona ya inscrita', ['curso_id' => $curso->id, 'persona_id' => $request->persona_id]);
            return redirect()->back()->with('error', 'La persona ya está inscrita en este curso.');
        }

        $curso->personas()->attach($request->persona_id, [
            'rol_participacion_id' => $request->rol_participacion_id,
            'estado' => 'pendiente',
        ]);
        Log::info('[INSCRIPCION] Persona inscrita correctamente', ['curso_id' => $curso->id, 'persona_id' => $request->persona_id]);

        return redirect()->back()->with('success', 'Persona inscrita correctamente.');
    }

    /**
     * Muestra el listado de personas inscritas en un curso.
     */
    public function verInscritos(Curso $curso)
    {
        $inscritos = $curso->personas; // Obtiene las personas inscritas en el curso
        $roles = \App\Models\RolParticipacion::all()->keyBy('id'); // Obtiene todos los roles de participación para mostrarlos por nombre

        return view('admin.inscripciones.inscritos', compact('curso', 'inscritos'));
    }

    /**
     * Elimina una inscripción de una persona en un curso.
     */
    public function darBaja(Curso $curso, Persona $persona)
    {

        $curso->personas()->detach($persona->id);

        return redirect()->route('admin.inscripciones.cursos.inscritos', $curso->id)
            ->with('success', 'El participante ha sido dado de baja correctamente.');
    }
}
