<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller{

    /* falta el getNewsCarousel */

    /* api/news */
    /* ?page='numero' => devuelve una nueva pagina con, por defecto, 10 noticias */
    /* ?per_page='numero' => devuelve una nueva pagina con la cantidad de noticias que quieras noticias */
    /* ?category='numero' => devuelve noticias con la categoria, el numero es el id de la categoria*/
    /* ?order=asc => devuelve las noticias en order acendente por fecha, por defecto son decendente*/
    public function index(Request $request){
        try {
            $perPage = $request->get('per_page', 10);
            $categoryId = $request->get('category');
            $order = $request->get('order', 'desc');

            $query = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
                ->leftJoin('categorias', 'news_has_categorias.news_id', '=', 'categorias.id')
                ->select(
                    'news.id',
                    'news.titulo',
                    'news.contenido',
                    'news.fecha_publicacion',
                    'categorias.nombre as categoria'
                );

            if ($categoryId) {
                $query->where('categorias.id', $categoryId);
            }

            $news = $query->orderBy('news.fecha_publicacion', $order)
                ->paginate($perPage);

            return response()->json([
                $news
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al obtener las noticias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /* api/news/{id} */
    public function getNoticiaById($id)
    {
        try {
            $news = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
                ->leftJoin('categorias', 'news_has_categorias.news_id', '=', 'categorias.id')
                ->select(
                    'news.id',
                    'news.titulo',
                    'news.contenido',
                    'news.fecha_publicacion',
                    'categorias.nombre as categoria',
                )
                ->where('news.id', $id)
                ->get();

            return response()->json([
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al obtener las noticias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function getAll(Request $request){
        try {
            $news = News::leftJoin('news_has_categorias', 'news.id', '=', 'news_has_categorias.news_id')
                ->leftJoin('categorias', 'news_has_categorias.news_id', '=', 'categorias.id')
                ->select(
                    'news.id',
                    'news.titulo',
                    'news.contenido',
                    'news.fecha_publicacion',
                    'categorias.nombre as categoria',
                )->get();

            return response()->json([
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al obtener las noticias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
