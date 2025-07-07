<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

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
    public function downloadDiploma($id)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            
            // Generar HTML con ambas páginas
            $frontHtml = view('admin.cursos.diplomas.template', compact('curso'))->render();
            $backHtml = view('admin.cursos.diplomas.template-back', compact('curso'))->render();
            
            // Combinar ambas páginas con CSS simplificado y optimizado
            $combinedHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap");
                    
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body { 
                        margin: 0; 
                        padding: 0; 
                        font-family: "Roboto", sans-serif;
                        background: white;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    .page { 
                        page-break-after: always; 
                        page-break-inside: avoid;
                        height: 100vh;
                        width: 100%;
                        position: relative;
                        display: block;
                        clear: both;
                        margin: 0;
                        padding: 0;
                    }
                    
                    .page:last-child { 
                        page-break-after: avoid; 
                    }
                    
                    .diploma {
                        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
                        width: 100%;
                        height: 100vh;
                        position: relative;
                        border-radius: 20px;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                        border: 8px solid #2c3e50;
                        page-break-inside: avoid;
                    }
                    
                    .diploma::before {
                        content: "";
                        position: absolute;
                        top: 20px;
                        left: 20px;
                        right: 20px;
                        bottom: 20px;
                        border: 2px solid #e74c3c;
                        border-radius: 15px;
                        pointer-events: none;
                    }
                    
                    .diploma::after {
                        content: "";
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-image: 
                            radial-gradient(circle at 20% 20%, rgba(231, 76, 60, 0.05) 0%, transparent 50%),
                            radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.05) 0%, transparent 50%);
                        pointer-events: none;
                    }
                    
                    .diploma-content {
                        position: relative;
                        z-index: 1;
                        padding: 40px;
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        align-items: center;
                        text-align: center;
                        min-height: 100vh;
                    }
                    
                    .logo {
                        font-family: "Playfair Display", serif;
                        font-size: 2.5rem;
                        font-weight: 900;
                        color: #2c3e50;
                        margin-bottom: 10px;
                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    
                    .institution {
                        font-size: 1.2rem;
                        color: #7f8c8d;
                        font-weight: 300;
                        letter-spacing: 2px;
                        text-transform: uppercase;
                    }
                    
                    .diploma-title {
                        font-family: "Playfair Display", serif;
                        font-size: 3rem;
                        font-weight: 700;
                        color: #2c3e50;
                        margin: 40px 0;
                        line-height: 1.2;
                        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
                    }
                    
                    .diploma-text {
                        font-size: 1.4rem;
                        color: #34495e;
                        line-height: 1.6;
                        margin-bottom: 30px;
                        max-width: 800px;
                    }
                    
                    .course-name {
                        font-family: "Playfair Display", serif;
                        font-size: 2rem;
                        font-weight: 700;
                        color: #e74c3c;
                        margin: 20px 0;
                        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
                    }
                    
                    .course-info {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 20px;
                        margin: 30px 0;
                        width: 100%;
                        max-width: 800px;
                    }
                    
                    .info-item {
                        text-align: center;
                        padding: 20px;
                        background: rgba(255, 255, 255, 0.9);
                        border-radius: 15px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                        border: 1px solid rgba(231, 76, 60, 0.1);
                    }
                    
                    .info-label {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        margin-bottom: 5px;
                    }
                    
                    .info-value {
                        font-size: 1.1rem;
                        color: #2c3e50;
                        font-weight: 500;
                    }
                    
                    .signature-section {
                        margin-top: 40px;
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 40px;
                        width: 100%;
                        max-width: 800px;
                    }
                    
                    .signature-item {
                        text-align: center;
                        padding: 20px;
                    }
                    
                    .signature-line {
                        width: 200px;
                        height: 2px;
                        background: #2c3e50;
                        margin: 20px auto;
                    }
                    
                    .signature-name {
                        font-size: 1.1rem;
                        color: #2c3e50;
                        font-weight: 500;
                    }
                    
                    .signature-title {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        margin-top: 5px;
                    }
                    
                    .diploma-date {
                        position: absolute;
                        bottom: 40px;
                        right: 60px;
                        font-size: 1rem;
                        color: #7f8c8d;
                        font-style: italic;
                    }
                </style>
            </head>
            <body>
                <div class="page">' . $frontHtml . '</div>
                <div class="page">' . $backHtml . '</div>
            </body>
            </html>';
            
            // Configurar Browsershot con opciones optimizadas
            $browsershot = \Spatie\Browsershot\Browsershot::html($combinedHtml)
                ->format('A4')
                ->portrait()
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->noSandbox()
                ->disableGpu()
                ->timeout(120)
                ->waitUntilNetworkIdle()
                ->preferCssPageSize();
            
            // Intentar diferentes rutas de Chrome
            $chromePaths = [
                '/usr/bin/google-chrome',
                '/usr/bin/chromium-browser',
                '/usr/bin/chromium',
                'C:\Program Files\Google\Chrome\Application\chrome.exe',
                'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe'
            ];
            
            foreach ($chromePaths as $path) {
                if (file_exists($path)) {
                    $browsershot->setChromePath($path);
                    break;
                }
            }
            
            $pdf = $browsershot->pdf();
            
            // Verificar que el PDF se generó correctamente
            if (empty($pdf) || strlen($pdf) < 1000) {
                throw new \Exception('El PDF generado está vacío o corrupto');
            }
            
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
     * Descargar diploma en PDF con mejor separación de páginas
     */
    public function downloadDiplomaImproved($id)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            
            // Generar HTML con ambas páginas
            $frontHtml = view('admin.cursos.diplomas.template', compact('curso'))->render();
            $backHtml = view('admin.cursos.diplomas.template-back', compact('curso'))->render();
            
            // Combinar ambas páginas con CSS simplificado y efectivo
            $combinedHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap");
                    
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body { 
                        margin: 0; 
                        padding: 0; 
                        font-family: "Roboto", sans-serif;
                        background: white;
                    }
                    
                    .page { 
                        page-break-after: always; 
                        page-break-inside: avoid;
                        height: 100vh;
                        width: 100%;
                        position: relative;
                        display: block;
                        clear: both;
                        margin: 0;
                        padding: 0;
                    }
                    
                    .page:last-child { 
                        page-break-after: avoid; 
                    }
                    
                    .diploma {
                        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
                        width: 100%;
                        height: 100vh;
                        position: relative;
                        border-radius: 20px;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                        border: 8px solid #2c3e50;
                        page-break-inside: avoid;
                    }
                    
                    .diploma::before {
                        content: "";
                        position: absolute;
                        top: 20px;
                        left: 20px;
                        right: 20px;
                        bottom: 20px;
                        border: 2px solid #e74c3c;
                        border-radius: 15px;
                        pointer-events: none;
                    }
                    
                    .diploma::after {
                        content: "";
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-image: 
                            radial-gradient(circle at 20% 20%, rgba(231, 76, 60, 0.05) 0%, transparent 50%),
                            radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.05) 0%, transparent 50%);
                        pointer-events: none;
                    }
                    
                    .diploma-content {
                        position: relative;
                        z-index: 1;
                        padding: 40px;
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        align-items: center;
                        text-align: center;
                        min-height: 100vh;
                    }
                    
                    .logo {
                        font-family: "Playfair Display", serif;
                        font-size: 2.5rem;
                        font-weight: 900;
                        color: #2c3e50;
                        margin-bottom: 10px;
                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    
                    .institution {
                        font-size: 1.2rem;
                        color: #7f8c8d;
                        font-weight: 300;
                        letter-spacing: 2px;
                        text-transform: uppercase;
                    }
                    
                    .diploma-title {
                        font-family: "Playfair Display", serif;
                        font-size: 3rem;
                        font-weight: 700;
                        color: #2c3e50;
                        margin: 40px 0;
                        line-height: 1.2;
                        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
                    }
                    
                    .diploma-text {
                        font-size: 1.4rem;
                        color: #34495e;
                        line-height: 1.6;
                        margin-bottom: 30px;
                        max-width: 800px;
                    }
                    
                    .course-name {
                        font-family: "Playfair Display", serif;
                        font-size: 2rem;
                        font-weight: 700;
                        color: #e74c3c;
                        margin: 20px 0;
                        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
                    }
                    
                    .course-info {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 20px;
                        margin: 30px 0;
                        width: 100%;
                        max-width: 800px;
                    }
                    
                    .info-item {
                        text-align: center;
                        padding: 20px;
                        background: rgba(255, 255, 255, 0.9);
                        border-radius: 15px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                        border: 1px solid rgba(231, 76, 60, 0.1);
                    }
                    
                    .info-label {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        margin-bottom: 5px;
                    }
                    
                    .info-value {
                        font-size: 1.1rem;
                        color: #2c3e50;
                        font-weight: 500;
                    }
                    
                    .signature-section {
                        margin-top: 40px;
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 40px;
                        width: 100%;
                        max-width: 800px;
                    }
                    
                    .signature-item {
                        text-align: center;
                        padding: 20px;
                    }
                    
                    .signature-line {
                        width: 200px;
                        height: 2px;
                        background: #2c3e50;
                        margin: 20px auto;
                    }
                    
                    .signature-name {
                        font-size: 1.1rem;
                        color: #2c3e50;
                        font-weight: 500;
                    }
                    
                    .signature-title {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        margin-top: 5px;
                    }
                    
                    .diploma-date {
                        position: absolute;
                        bottom: 40px;
                        right: 60px;
                        font-size: 1rem;
                        color: #7f8c8d;
                        font-style: italic;
                    }
                    
                    .diploma-details {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                        gap: 30px;
                        margin: 30px 0;
                        width: 100%;
                    }
                    
                    .detail-section {
                        background: rgba(255, 255, 255, 0.8);
                        padding: 25px;
                        border-radius: 15px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    }
                    
                    .detail-title {
                        font-size: 1.2rem;
                        color: #2c3e50;
                        font-weight: 600;
                        margin-bottom: 15px;
                        border-bottom: 2px solid #e74c3c;
                        padding-bottom: 5px;
                    }
                    
                    .detail-item {
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    
                    .detail-label {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        font-weight: 500;
                    }
                    
                    .detail-value {
                        font-size: 1rem;
                        color: #2c3e50;
                        font-weight: 600;
                    }
                    
                    .verification-section {
                        text-align: center;
                        margin: 40px 0;
                    }
                    
                    .qr-code {
                        width: 120px;
                        height: 120px;
                        background: #f8f9fa;
                        border: 2px solid #2c3e50;
                        border-radius: 10px;
                        margin: 0 auto 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 0.8rem;
                        color: #7f8c8d;
                    }
                    
                    .verification-text {
                        font-size: 0.9rem;
                        color: #7f8c8d;
                        margin-bottom: 10px;
                    }
                    
                    .verification-url {
                        font-size: 0.8rem;
                        color: #3498db;
                        font-weight: 500;
                    }
                    
                    .additional-info {
                        background: rgba(52, 152, 219, 0.1);
                        padding: 20px;
                        border-radius: 15px;
                        margin: 30px 0;
                    }
                    
                    .additional-title {
                        font-size: 1.1rem;
                        color: #2c3e50;
                        font-weight: 600;
                        margin-bottom: 15px;
                        text-align: center;
                    }
                    
                    .additional-text {
                        font-size: 0.9rem;
                        color: #34495e;
                        line-height: 1.6;
                        text-align: justify;
                    }
                    
                    .diploma-footer {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: 30px;
                    }
                    
                    .footer-left {
                        text-align: left;
                    }
                    
                    .footer-right {
                        text-align: right;
                    }
                    
                    .footer-text {
                        font-size: 0.8rem;
                        color: #7f8c8d;
                    }
                    
                    .decorative-corner {
                        position: absolute;
                        width: 60px;
                        height: 60px;
                        border: 3px solid #e74c3c;
                    }
                    
                    .corner-tl {
                        top: 40px;
                        left: 40px;
                        border-right: none;
                        border-bottom: none;
                    }
                    
                    .corner-tr {
                        top: 40px;
                        right: 40px;
                        border-left: none;
                        border-bottom: none;
                    }
                    
                    .corner-bl {
                        bottom: 40px;
                        left: 40px;
                        border-right: none;
                        border-top: none;
                    }
                    
                    .corner-br {
                        bottom: 40px;
                        right: 40px;
                        border-left: none;
                        border-top: none;
                    }
                </style>
            </head>
            <body>
                <div class="page">' . $frontHtml . '</div>
                <div class="page">' . $backHtml . '</div>
            </body>
            </html>';
            
            $pdf = \Spatie\Browsershot\Browsershot::html($combinedHtml)
                ->setChromePath('/usr/bin/google-chrome')
                ->format('A4')
                ->portrait()
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->noSandbox()
                ->disableGpu()
                ->timeout(120)
                ->pdf();
            
            $filename = 'diploma_' . $curso->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('Error al generar diploma: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el diploma. ' . $e->getMessage());
        }
    }

    /**
     * Método de prueba para generar PDF simple y verificar que funciona
     */
    public function downloadDiplomaTest($id)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            
            // HTML simple para prueba
            $combinedHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { 
                        margin: 0; 
                        padding: 20px; 
                        font-family: Arial, sans-serif;
                        background: white;
                    }
                    
                    .page { 
                        page-break-after: always; 
                        page-break-inside: avoid;
                        height: 100vh;
                        width: 100%;
                        border: 2px solid #333;
                        margin-bottom: 20px;
                        padding: 40px;
                        box-sizing: border-box;
                    }
                    
                    .page:last-child { 
                        page-break-after: avoid; 
                    }
                    
                    .page-title {
                        font-size: 2rem;
                        text-align: center;
                        margin-bottom: 30px;
                        color: #2c3e50;
                    }
                    
                    .content {
                        font-size: 1.2rem;
                        line-height: 1.6;
                        text-align: center;
                    }
                    
                    .course-name {
                        font-size: 1.5rem;
                        color: #e74c3c;
                        font-weight: bold;
                        margin: 20px 0;
                    }
                </style>
            </head>
            <body>
                <div class="page">
                    <div class="page-title">FRENTE DEL DIPLOMA</div>
                    <div class="content">
                        <p>Este es el frente del diploma para el curso:</p>
                        <div class="course-name">' . $curso->nombre . '</div>
                        <p>Fecha: ' . date('d/m/Y') . '</p>
                        <p>Página 1 de 2</p>
                    </div>
                </div>
                
                <div class="page">
                    <div class="page-title">REVERSO DEL DIPLOMA</div>
                    <div class="content">
                        <p>Este es el reverso del diploma para el curso:</p>
                        <div class="course-name">' . $curso->nombre . '</div>
                        <p>Descripción: ' . $curso->descripcion . '</p>
                        <p>Página 2 de 2</p>
                    </div>
                </div>
            </body>
            </html>';
            
            // Configurar Browsershot con opciones optimizadas
            $browsershot = \Spatie\Browsershot\Browsershot::html($combinedHtml)
                ->format('A4')
                ->portrait()
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->noSandbox()
                ->disableGpu()
                ->timeout(120)
                ->waitUntilNetworkIdle()
                ->preferCssPageSize();
            
            // Intentar diferentes rutas de Chrome
            $chromePaths = [
                '/usr/bin/google-chrome',
                '/usr/bin/chromium-browser',
                '/usr/bin/chromium',
                'C:\Program Files\Google\Chrome\Application\chrome.exe',
                'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe'
            ];
            
            foreach ($chromePaths as $path) {
                if (file_exists($path)) {
                    $browsershot->setChromePath($path);
                    break;
                }
            }
            
            $pdf = $browsershot->pdf();
            
            // Verificar que el PDF se generó correctamente
            if (empty($pdf) || strlen($pdf) < 1000) {
                throw new \Exception('El PDF generado está vacío o corrupto');
            }
            
            $filename = 'diploma_test_' . $curso->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdf))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
                
        } catch (\Exception $e) {
            Log::error('Error al generar diploma de prueba: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el diploma de prueba. ' . $e->getMessage());
        }
    }
}