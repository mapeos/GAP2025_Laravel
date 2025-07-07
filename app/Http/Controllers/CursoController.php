<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Services\DiplomaService;

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
            'fechaInicio' => 'required|date|after_or_equal:today',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'plazas' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
            'precio' => 'nullable|numeric|min:0',
            'temario' => 'nullable|file|mimes:pdf,doc,docx|max:25600',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'fechaInicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fechaFin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'temario.max' => 'El temario no puede ser mayor a 25MB.',
            'portada.max' => 'La portada no puede ser mayor a 10MB.',
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
        // Cargar el curso con todas las relaciones necesarias para los partials
        $curso = Curso::withTrashed()
            ->with(['personas.user', 'personas.participaciones.rol'])
            ->findOrFail($id);
            
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
            'fechaInicio' => 'required|date|after_or_equal:today',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
            'plazas' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
            'precio' => 'nullable|numeric|min:0',
            'temario' => 'nullable|file|mimes:pdf,doc,docx|max:25600',
            'portada' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'fechaInicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fechaFin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'temario.max' => 'El temario no puede ser mayor a 25MB.',
            'portada.max' => 'La portada no puede ser mayor a 10MB.',
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
     * Verificar y crear enlace simbólico de storage si no existe
     */
    private function ensureStorageLink()
    {
        $linkPath = public_path('storage');
        
        if (!file_exists($linkPath)) {
            try {
                Artisan::call('storage:link');
                Log::info('Enlace simbólico de storage creado correctamente.');
            } catch (\Exception $e) {
                Log::error('Error al crear enlace simbólico: ' . $e->getMessage());
            }
        }
    }

    /**
     * Subir archivo de temario para un curso específico
     */
    public function uploadTemario(Request $request, $id)
    {
        try {
            // Verificar enlace simbólico de storage
            $this->ensureStorageLink();
            
            $curso = Curso::findOrFail($id);

            $request->validate([
                'temario' => 'required|file|mimes:pdf,doc,docx|max:25600',
            ], [
                'temario.required' => 'Debe seleccionar un archivo para subir.',
                'temario.file' => 'El archivo seleccionado no es válido.',
                'temario.mimes' => 'El archivo debe ser de tipo: PDF, DOC o DOCX.',
                'temario.max' => 'El archivo no puede ser mayor a 25MB.',
            ]);

            if ($request->hasFile('temario')) {
                $archivo = $request->file('temario');
                
                // Verificar que el archivo se subió correctamente
                if (!$archivo->isValid()) {
                    return redirect()->back()->with('error', 'Error al subir el archivo. Inténtelo de nuevo.');
                }

                // Crear directorio si no existe
                $directorio = 'temarios';
                if (!Storage::disk('public')->exists($directorio)) {
                    Storage::disk('public')->makeDirectory($directorio);
                }

                // Eliminar temario anterior si existe
                if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path)) {
                    Storage::disk('public')->delete($curso->temario_path);
                }

                // Generar nombre único para el archivo
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = 'curso_' . $curso->id . '_' . time() . '.' . $extension;
                $ruta = $directorio . '/' . $nombreArchivo;

                // Guardar el archivo
                $archivoGuardado = Storage::disk('public')->putFileAs($directorio, $archivo, $nombreArchivo);
                
                if (!$archivoGuardado) {
                    return redirect()->back()->with('error', 'Error al guardar el archivo en el servidor.');
                }

                // Actualizar el curso con la nueva ruta
                $curso->update(['temario_path' => $ruta]);
                
                return redirect()->back()->with('success', 'Temario subido correctamente.');
            }

            return redirect()->back()->with('error', 'No se seleccionó ningún archivo.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error al subir temario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno del servidor al subir el temario.');
        }
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

    /**
     * Subir imagen de portada para un curso específico
     */
    public function uploadPortada(Request $request, $id)
    {
        try {
            // Verificar enlace simbólico de storage
            $this->ensureStorageLink();
            
            $curso = Curso::findOrFail($id);

            $request->validate([
                'portada' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            ], [
                'portada.required' => 'Debe seleccionar una imagen para subir.',
                'portada.image' => 'El archivo debe ser una imagen válida.',
                'portada.mimes' => 'La imagen debe ser de tipo: JPG, JPEG, PNG o WEBP.',
                'portada.max' => 'La imagen no puede ser mayor a 10MB.',
            ]);

            if ($request->hasFile('portada')) {
                $archivo = $request->file('portada');
                
                // Verificar que el archivo se subió correctamente
                if (!$archivo->isValid()) {
                    return redirect()->back()->with('error', 'Error al subir la imagen. Inténtelo de nuevo.');
                }

                // Crear directorio si no existe
                $directorio = 'portadas';
                if (!Storage::disk('public')->exists($directorio)) {
                    Storage::disk('public')->makeDirectory($directorio);
                }

                // Eliminar portada anterior si existe
                if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path)) {
                    Storage::disk('public')->delete($curso->portada_path);
                }

                // Generar nombre único para el archivo
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = 'curso_' . $curso->id . '_' . time() . '.' . $extension;
                $ruta = $directorio . '/' . $nombreArchivo;

                // Guardar el archivo
                $archivoGuardado = Storage::disk('public')->putFileAs($directorio, $archivo, $nombreArchivo);
                
                if (!$archivoGuardado) {
                    return redirect()->back()->with('error', 'Error al guardar la imagen en el servidor.');
                }

                // Actualizar el curso con la nueva ruta
                $curso->update(['portada_path' => $ruta]);
                
                return redirect()->back()->with('success', 'Portada subida correctamente.');
            }

            return redirect()->back()->with('error', 'No se seleccionó ninguna imagen.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error al subir portada: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno del servidor al subir la portada.');
        }
    }

    /**
     * Eliminar temario de un curso
     */
    public function deleteTemario($id)
    {
        try {
            $curso = Curso::findOrFail($id);

            if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path)) {
                // Eliminar archivo físico
                Storage::disk('public')->delete($curso->temario_path);
                
                // Actualizar curso (eliminar referencia)
                $curso->update(['temario_path' => null]);
                
                return redirect()->back()->with('success', 'Temario eliminado correctamente.');
            }

            return redirect()->back()->with('warning', 'No hay temario para eliminar.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar temario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el temario.');
        }
    }

    /**
     * Eliminar portada de un curso
     */
    public function deletePortada($id)
    {
        try {
            $curso = Curso::findOrFail($id);

            if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path)) {
                // Eliminar archivo físico
                Storage::disk('public')->delete($curso->portada_path);
                
                // Actualizar curso (eliminar referencia)
                $curso->update(['portada_path' => null]);
                
                return redirect()->back()->with('success', 'Portada eliminada correctamente.');
            }

            return redirect()->back()->with('warning', 'No hay portada para eliminar.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Curso no encontrado.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar portada: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar la portada.');
        }
    }

    /**
     * Mostrar vista del diploma del curso
     */
    public function diploma($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        return view('admin.cursos.diplomas.index', compact('curso'));
    }

    /**
     * Mostrar vista completa del diploma (pantalla completa)
     */
    public function diplomaFull($id)
    {
        $curso = Curso::withTrashed()->findOrFail($id);
        return view('admin.cursos.diplomas.full', compact('curso'));
    }

    /**
     * Descargar diploma del curso como PDF
     */
    public function downloadDiploma($id, DiplomaService $diplomaService)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            $pdf = $diplomaService->generarDiplomaPdf($curso);
            $filename = 'diploma_' . $curso->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdf))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        } catch (\Exception $e) {
            Log::error('Error al generar diploma: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el diploma. ' . $e->getMessage());
        }
    }

    /**
     * Descargar diploma en PDF 
     */
}