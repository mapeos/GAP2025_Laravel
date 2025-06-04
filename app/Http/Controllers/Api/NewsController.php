<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /* get all news an select id, title, content, published_date and category */
    /* Obtener todas las noticias y seleccionar titulo, contenido, descripsion y sus categoria */
    public function index()
    {
        $data = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
        ->leftJoin('categorias', 'news_has_categorias.categorias_id', '=', 'categorias.id')
        ->select(
            'news.id',
            'news.titulo',
            'news.contenido',
            'news.fecha_publicacion',
            'categorias.nombre as categoria'
        )
        ->orderBy('news.fecha_publicacion', 'asc')
        ->get();

        return response()->json([
            'status' => '200',
            'message' => 'Noticias recuperadas exitosamente',
            'data' => $data
        ]);
    }

    /* get news by id */
    public function show($id)
    {
        $data = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
        ->leftJoin('categorias', 'news_has_categorias.categorias_id', '=', 'categorias.id')
        ->select(
            'news.id',
            'news.titulo',
            'news.contenido',
            'news.fecha_publicacion',
            'categorias.nombre as categoria'
        )
        ->where('news.id', $id)
        ->firstOrFail();

        return response()->json([
            'status' => '200',
            'message' => 'Noticia recuperada exitosamente',
            'data' => $data
        ]);
    }

    /* get news by category */
    public function getByCategory($category)
    {
        $data = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
        ->leftJoin('categorias', 'news_has_categorias.categorias_id', '=', 'categorias.id')
        ->select(
            'news.id',
            'news.titulo',
            'news.contenido',
            'news.fecha_publicacion',
            'categorias.nombre as categoria'
        )
        ->where('categorias.nombre', $category)
        ->orderBy('news.fecha_publicacion', 'desc')
        ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No news found for this category'
            ], 404);
        }

        return response()->json([
            'status' => '200',
            'message' => 'Noticias recuperadas exitosamente',
            'data' => $data
        ]);
    }

    /* get latest news */
    public function latest($number = 5)
    {
        $data = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
        ->leftJoin('categorias', 'news_has_categorias.categorias_id', '=', 'categorias.id')
        ->select(
            'news.id',
            'news.titulo',
            'news.contenido',
            'news.fecha_publicacion',
            'categorias.nombre as categoria'
        )
        ->orderBy('news.fecha_publicacion', 'desc')
        ->take($number)
        ->get();

        return response()->json([
            'status' => '200',
            'message' => 'Últimas noticias recuperadas exitosamente',
            'data' => $data
        ]);
    }



}

