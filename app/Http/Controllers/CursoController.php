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
        $cursos = Curso::withTrashed()->paginate(15);
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
        // Validaciones mejoradas para actualización
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'plazas' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
            'precio' => 'nullable|numeric|min:0',
            'temario' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $curso = Curso::findOrFail($id);
        $data = $request->except(['temario', 'portada']);

        // Procesar archivo de temario si se proporciona
        if ($request->hasFile('temario')) {
            // Eliminar temario anterior si existe
            if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path)) {
                Storage::disk('public')->delete($curso->temario_path);
            }
            $data['temario_path'] = $request->file('temario')->store('temarios', 'public');
        }

        // Procesar imagen de portada si se proporciona
        if ($request->hasFile('portada')) {
            // Eliminar portada anterior si existe
            if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path)) {
                Storage::disk('public')->delete($curso->portada_path);
            }
            $data['portada_path'] = $request->file('portada')->store('portadas', 'public');
        }

        $curso->update($data);
        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
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
            'precio' => 'nullable|numeric|min:0',
            'temario' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except(['temario', 'portada']);

        // Procesar archivo de temario si se proporciona
        if ($request->hasFile('temario')) {
            $data['temario_path'] = $request->file('temario')->store('temarios', 'public');
        }

        // Procesar imagen de portada si se proporciona
        if ($request->hasFile('portada')) {
            $data['portada_path'] = $request->file('portada')->store('portadas', 'public');
        }

        $curso = Curso::create($data);
        return redirect()->route('admin.cursos.show', $curso->id)->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Subir archivo de temario para un curso específico
     */
    public function uploadTemario(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);

        $request->validate([
            'temario' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('temario')) {
            // Eliminar temario anterior si existe
            if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path)) {
                Storage::disk('public')->delete($curso->temario_path);
            }

            $archivo = $request->file('temario');
            $nombreArchivo = $curso->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs('temarios', $nombreArchivo, 'public');

            $curso->update(['temario_path' => $ruta]);
            return redirect()->back()->with('success', 'Temario subido correctamente.');
        }

        return redirect()->back()->with('error', 'Error al subir el temario.');
    }

    /**
     * Toggle del estado del curso (activo/inactivo)
     */
    public function toggleEstado($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        
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
     * Eliminar curso (soft delete)
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
     * Restaurar curso eliminado (soft delete)
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
            'message' => 'Curso restaurado correctamente'
        ]);
    }

    /**
     * Toggle del estado de eliminación del curso (soft delete/restore)
     * Método alternativo para manejar ambos casos desde una sola ruta
     */
    public function toggleStatus($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);

        if ($curso->trashed()) {
            $curso->restore();
            return response()->json(['status' => 'activo']);
        } else {
            $curso->delete();
            return response()->json(['status' => 'eliminado']);
        }
    }
}