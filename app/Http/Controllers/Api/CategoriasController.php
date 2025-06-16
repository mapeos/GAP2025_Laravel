<?php

namespace App\Http\Controllers\Api;

use App\Models\Categorias;
use App\Http\Controllers\Controller;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categorias::select('id', 'nombre', 'descripcion')->get();
        return response()->json(['data' => $categorias]);
    }
}
