<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Storage;

class CursoController extends Controller
{
    /**
     * Mostrar lista de todos los cursos (activos y eliminados)
     */
    public function index()
    {
        $cursos = Curso::withTrashed()->get();
        return view('admin.cursos.index', compact('cursos'));
    }

    /**
     * Mostrar formulario para crear un nuevo curso
     */
    public function create()
    {
        return view('admin.cursos.create');
    }

    /**
     * Mostrar formulario para editar un curso existente
     */
    public function edit($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        return view('admin.cursos.edit', compact('curso'));
    }

    /**
     * Actualizar un curso existente en la base de datos
    */
    public function update(Request $request, $id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        $curso->update($request->all());
        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
    }

    /**
     * Eliminar un curso de la base de datos (soft delete)
     */
    public function destroy($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        $curso->delete();
        return redirect()->route('admin.cursos.index')->with('success', 'Curso eliminado correctamente.');
    }

    /**
     * Mostrar detalles de un curso específico
     */
    public function show($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        return view('admin.cursos.show', compact('curso'));
    }

    /**
     * Listar solo los cursos activos para inscripciones
     */
    public function listarCursosActivos()
    {
        $cursos = Curso::where('estado', 'activo')->get();
        return view('admin.inscripciones.cursos_activos', compact('cursos'));
    }

    /**
     * Subir archivo de temario para un curso específico
     */
    public function uploadTemario(Request $request, $id)
    {
        // Encuentra el curso por ID (incluyendo eliminados)
        $curso = Curso::withTrashed()->findOrFail($id);

        // Validación del archivo
        $request->validate([
            'temario' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB máximo
        ]);

        // Subida y almacenamiento del archivo
        if ($request->hasFile('temario')) {
            $archivo = $request->file('temario');
            $nombreArchivo = $curso->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs('temarios', $nombreArchivo, 'public');

            // Guardar la ruta en la BD en el campo correcto (temario_path)
            $curso->update(['temario_path' => $ruta]);

            return redirect()->back()->with('success', 'Temario subido correctamente.');
        }

        return redirect()->back()->with('error', 'Error al subir el temario.');
    }

    /**
     * Almacenar un nuevo curso en la base de datos
     */
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
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->all();

        // Procesar archivo de temario si se proporciona
        if ($request->hasFile('temario')) {
            $path = $request->file('temario')->store('temarios', 'public');
            $data['temario_path'] = $path;
        }

        // Procesar imagen de portada si se proporciona
        if ($request->hasFile('portada')) {
            $portadaPath = $request->file('portada')->store('portadas', 'public');
            $data['portada_path'] = $portadaPath;
        }

        $curso = Curso::create($data);
        return redirect()->route('admin.cursos.show', $curso->id)->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Toggle del estado de eliminación del curso (soft delete/restore)
     */
    public function toggleStatus($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);

        if ($curso->trashed()) {
            // Restaurar curso eliminado
            $curso->restore();
            return response()->json(['status' => 'activo']);
        } else {
            // Eliminar curso (soft delete)
            $curso->delete();
            return response()->json(['status' => 'eliminado']);
        }
    }

    /**
     * Toggle del estado del curso (activo/inactivo) - independiente de eliminación
     */
    public function toggleEstado($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        
        // Cambiar estado
        $nuevoEstado = $curso->estado === 'activo' ? 'inactivo' : 'activo';
        $curso->update(['estado' => $nuevoEstado]);
        
        $mensaje = $nuevoEstado === 'activo' 
            ? 'Curso activado correctamente' 
            : 'Curso desactivado correctamente';
        
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'estado' => $nuevoEstado
        ]);
    }

    /**
     * Eliminar curso (soft delete) - independiente del estado
     */
    public function delete($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        
        if ($curso->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'El curso ya está eliminado'
            ]);
        }
        
        $curso->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Curso eliminado correctamente'
        ]);
    }

    /**
     * Restaurar curso eliminado (soft delete) - independiente del estado
     */
    public function restore($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        
        if (!$curso->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'El curso no está eliminado'
            ]);
        }
        
        $curso->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'Curso activado correctamente'
        ]);
    }
}
