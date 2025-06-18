<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Storage;

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
        $cursos = Curso::where('estado', 'activo')->get();
        return view('admin.inscripciones.cursos_activos', compact('cursos'));
    }

    public function uploadTemario(Request $request, $id)
    {
        //dd($request->all()); 
        // Encuentra el curso por ID
        $curso = Curso::findOrFail($id);

        // Validación del archivo
        $request->validate([
            'temario' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB máximo
        ]);

        // Subida y almacenamiento del archivo
        if ($request->hasFile('temario')) {
            $archivo = $request->file('temario');
            $nombreArchivo = $curso->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs('temarios', $nombreArchivo, 'public');
            // dd($ruta);

            // Guardar la ruta en la BD en el campo correcto (temario_path)
            $curso->update(['temario_path' => $ruta]);

            return redirect()->back()->with('success', 'Temario subido correctamente.');
        }

        return redirect()->back()->with('error', 'Error al subir el temario.');
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
            'temario' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // <-- validación de imagen
        ]);

        $data = $request->all();

        if ($request->hasFile('temario')) {
            $path = $request->file('temario')->store('temarios', 'public');
            $data['temario_path'] = $path;
        }

        if ($request->hasFile('portada')) {
            $portadaPath = $request->file('portada')->store('portadas', 'public');
            $data['portada_path'] = $portadaPath;
        }

        $curso = Curso::create($data);
        return redirect()->route('admin.cursos.show', $curso->id)->with('success', 'Curso creado exitosamente.');
    }
}
