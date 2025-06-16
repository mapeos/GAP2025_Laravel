<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;

class CursoController extends Controller
{

    public function index()
    {
        $cursos = Curso::all();
        return view('admin.cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('admin.cursos.create');
    }

    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        return view('admin.cursos.edit', compact('curso'));
    }

    public function update(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);
        // Validar y actualizar campos
        $curso->update($request->all());
        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        $curso->delete();
        return redirect()->route('admin.cursos.index')->with('success', 'Curso eliminado correctamente.');
    }

    public function show(Curso $curso)
    {
        return view('admin.cursos.show', compact('curso'));
    }

    public function listarCursosActivos()
    {
        $cursos = Curso::where('estado', 'activo')->get(); // Obtiene solo los cursos activos
        return view('admin.inscripciones.cursos_activos', compact('cursos')); // Retorna la vista con los cursos
    }

    public function uploadTemario(Request $request, Curso $curso)
    {
        $request->validate([
            'temario' => 'required|file|mimes:pdf,doc,docx|max:2048', // Solo permite archivos PDF, DOC y DOCX con un tamaño máximo de 2 MB
        ]);

        // Guardar el archivo en el almacenamiento
        $path = $request->file('temario')->store('temarios', 'public');

        // Puedes guardar la ruta del archivo en la base de datos si es necesario
        $curso->update(['temario_path' => $path]);

        return redirect()->route('admin.cursos.show', $curso->id)->with('success', 'Temario subido correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'plazas' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        Curso::create($request->all());

        return redirect()->route('admin.cursos.index')->with('success', 'Curso creado exitosamente.');
    }
}
