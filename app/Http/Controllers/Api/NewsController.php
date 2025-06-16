<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = News::with('categorias')->orderBy('fecha_publicacion', 'desc');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('titulo', 'like', "%$q%")
                         ->orWhere('contenido', 'like', "%$q%");
            });
        }

        if ($request->filled('category')) {
            $category = $request->query('category');
            $query->whereHas('categorias', fn($q) => $q->where('nombre', $category));
        }

        $news = $query->paginate($perPage);
        return response()->json($news);
    }

    public function show($id)
    {
        $news = News::with('categorias')->findOrFail($id);
        return response()->json($news);
    }

    public function latest()
    {
        $latest = News::with('categorias')
            ->orderBy('fecha_publicacion', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status' => '200',
            'data' => "" //$data
        ]);
    }

 public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|unique:news|max:50',
            'contenido' => 'required',
            'autor' => 'nullable|integer',
            'fecha_publicacion' => 'required|date',
            'categorias' => 'nullable|array',
            'categorias.*' => 'integer|exists:categorias,id',
        ]);

        $news = News::create($request->only(['titulo', 'contenido', 'autor', 'fecha_publicacion']));
        if ($request->filled('categorias')) {
            $news->categorias()->sync($request->categorias);
        }

        return response()->json(['message' => 'News created', 'data' => $news], 201);
    }

}

