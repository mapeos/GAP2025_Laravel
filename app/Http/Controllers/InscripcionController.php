<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Persona;

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
        //dd($request->all());
        // Inspecciona los datos enviados


        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'rol_participacion_id' => 'required|exists:roles_participacion,id',
        ]);

        // Agrega la persona al curso con el rol especificado
        $curso->personas()->attach($request->persona_id, [
            'rol_participacion_id' => $request->rol_participacion_id,
            'estado' => 'pendiente', // Estado inicial
        ]);

        return redirect()->route('admin.inscripciones.cursos.inscribir.form', $curso->id)
            ->with('success', 'Persona inscrita correctamente.');
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
