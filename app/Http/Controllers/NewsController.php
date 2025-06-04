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

        return view('admin.news.index', compact('news'));
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
        ]);

        //Crear la noticia
        $news = News::create($request->only(['titulo', 'contenido', 'autor', 'fecha_publicacion']));

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
    public function edit(News $news)
    {
        $categorias = Categorias::all();
        return view('admin.news.edit', compact('news', 'categorias'));
    }

    /**
     * Actualizar una noticia en la base de datos.
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'titulo' => 'required|max:50|unique:news,titulo,' . $news->id,
            'contenido' => 'required',
            'autor' => 'nullable|integer',
            'fecha_publicacion' => 'required|date',
            'categorias' => 'nullable|array',
        ]);

        // Actualizar la noticia
        $news->update($request->only(['titulo', 'contenido', 'autor', 'fecha_publicacion']));

        // Actualizar las categorías asociadas
        $news->categorias()->sync($request->categorias ?? []);

        // Enviar mensaje de éxito a la vista usando session
        return redirect()->route('admin.news.index')->with('success', 'Noticia actualizada exitosamente.');
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
