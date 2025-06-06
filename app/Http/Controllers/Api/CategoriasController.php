<?php

namespace App\Http\Controllers\Api;
use App\Models\Categorias;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{   
    /* Obtener todas las categorías y seleccionar id, nombre y descripción */
    /* get all categories an select id, name and description */
    public function index()
    {
        return Categorias::all()->select('id', 'nombre', 'descripcion');
    }
}
