<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;

class CursoController extends Controller
{
    // Listar todos los cursos
    public function index()
    {
        try {
            $query = Curso::all();

            return response()->json([
                'data' => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al obtener los cursos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener un curso por ID
    public function getCursoById($id){
        try {
            $query = Curso::find($id);

            return response()->json([
                'data' => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al obtener los curso.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener cursos con estado activo
    public function activos()
    {
        $cursos = Curso::where('estado', 'activo')->get();
        return response()->json([
            'status' => '200',
            'message' => 'Cursos activos obtenidos correctamente',
            'data' => $cursos
        ]);
    }
    // Obtener cursos con estado inactivo
    public function inactivos()
    {
        $cursos = Curso::where('estado', 'inactivo')->get();
        return response()->json([
            'status' => '200',
            'message' => 'Cursos inactivos obtenidos correctamente',
            'data' => $cursos
        ]);
    }

    public function ordenadosPorFechaInicioDesc()
    {
        $cursos = Curso::orderBy('fechaInicio', 'desc')->get();
        return response()->json([
            'status' => '200',
            'message' => 'Cursos ordenados por fecha de inicio descendente obtenidos correctamente',
            'data' => $cursos
        ]);
    }

    public function ultimosCursos($number = 5)
    {
        $cursos = Curso::orderBy('created_at', 'desc')->take($number)->get();
        return response()->json([
            'status' => '200',
            'message' => 'Últimos cursos obtenidos correctamente',
            'data' => $cursos
        ]);
    }
    public function buscarFiltrar(Request $request)
{
    $query = Curso::query();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where('titulo', 'like', "%{$search}%")
              ->orWhere('descripcion', 'like', "%{$search}%");
    }

    if ($request->has('estado')) {
        $estado = $request->input('estado'); // 'activo' o 'inactivo'
        $query->where('estado', $estado);
    }

    if ($request->has('orden')) {
        $orden = $request->input('orden'); // 'asc' o 'desc'
        $query->orderBy('fecha_inicio', $orden);
    }

    $cursos = $query->paginate(20);
    return response()->json($cursos);
    }
}
