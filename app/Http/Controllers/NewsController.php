<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Categorias;

class NewsController extends Controller
{

    /**
     * Mostrar una lista de las noticias.
     */
    public function index()
    {
        // Paginación de 10 noticias por página
        // $news = News::paginate(10);

        // Carga las noticias junto con sus categorías relacionadas, paginando de 10 en 10
        // $news = News::with('categorias')->paginate(10);
        $news = News::withTrashed()->with('categorias')->paginate(10);
        
        // MEJORA FILTROS: Obtener todas las categorías para el filtro
        $categorias = Categorias::orderBy('nombre')->get();
        
        return view('admin.news.index', compact('news', 'categorias'));
    }

    /**
     * Mostrar el formulario para crear una nueva noticia.
     */
    public function create()
    {
        $categorias = Categorias::all();
        return view('admin.news.create', compact('categorias'));
    }

    /**
     * Almacenar una nueva noticia en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|unique:news|max:50',
            'contenido' => 'required',
            'autor' => 'nullable|integer',
            'fecha_publicacion' => 'required|date',
            'categorias' => 'nullable|array',
            // 'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para la imagen
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480', // 20MB = 20480KB

        ]);

        $data = $request->only(['titulo', 'contenido', 'autor', 'fecha_publicacion']);
        // Guardar el valor del checkbox 'slide' (si está presente será true, si no, false)
        $data['slide'] = $request->has('slide');

        // Manejar la subida de la imagen
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('storage/news'), $nombreImagen);
            $data['imagen'] = 'storage/news/' . $nombreImagen;
        }

        //Crear la noticia
        $news = News::create($data);

        //Asignar categorias a las noticias
        $news->categorias()->sync($request->categorias ?? []);

        //Enviar mensajes de exito a la vista sanso session
        return redirect()->route('admin.news.index')->with('success', 'Noticia creada exitosamente.');
    }

    /**
     * Mostrar los detalles de una noticia.
     */
    public function show($id)
    {
        $news = News::withTrashed()->with('categorias')->findOrFail($id);
        return view('admin.news.show', compact('news'));
    }


    /**
     * Mostrar el formulario para editar una noticia.
     */
    public function edit($id)
    {
        $news = News::withTrashed()->findOrFail($id);
        $categorias = Categorias::all();
        return view('admin.news.edit', compact('news', 'categorias'));
    }

    /**
     * Actualizar una noticia en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $news = News::withTrashed()->findOrFail($id);
        $request->validate([
            'titulo' => 'required|max:50|unique:news,titulo,' . $news->id,
            'contenido' => 'required',
            'autor' => 'nullable|integer',
            'fecha_publicacion' => 'required|date',
            'categorias' => 'nullable|array',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480', // 20MB = 20480KB
        ]);

        $data = $request->only(['titulo', 'contenido', 'autor', 'fecha_publicacion']);
        // Guardar el valor del checkbox 'slide' (si está presente será true, si no, false)
        $data['slide'] = $request->has('slide');

        // Manejar la actualización de la imagen
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($news->imagen && file_exists(public_path($news->imagen))) {
                unlink(public_path($news->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('storage/news'), $nombreImagen);
            $data['imagen'] = 'storage/news/' . $nombreImagen;
        }

        // Actualizar la noticia
        $news->update($data);

        // Actualizar las categorías asociadas
        $news->categorias()->sync($request->categorias ?? []);

        // Enviar mensaje de éxito a la vista usando session
        return redirect()->route('admin.news.index')->with('success', 'Noticia actualizada exitosamente.');
    }

    // eliminar por medio de Ajax
    public function toggleStatus($id)
    {
        $news = News::withTrashed()->findOrFail($id);

        if ($news->trashed()) {
            $news->restore();
            return response()->json(['status' => 'publicada']);
        } else {
            $news->delete();
            return response()->json(['status' => 'eliminada']);
        }
    }

    /**
     * Eliminar una noticia de la base de datos.
     */
    public function destroy(News $news)
    {
        // Eliminar la noticia
        $news->delete();

        // Enviar mensaje de éxito a la vista usando session
        return redirect()->route('admin.news.index')->with('success', 'Noticia eliminada exitosamente.');
    }

    /**
     * REstaurar noticia de la base de datos (que estan eliminadas).
     */
    public function restore($id)
    {
        $news = News::onlyTrashed()->findOrFail($id);
        $news->restore();

        return redirect()->route('admin.news.index')->with('success', 'Noticia restaurada correctamente.');
    }
}
