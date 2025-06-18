<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;

use function Laravel\Prompts\search;

class CursoController extends Controller
{
    // Listar todos los cursos
    public function getAllCursos()
    {
        try {
            $query = Curso::all();

            return response()->json([
                'status' => '200',
                'message' => 'Cursos obtenidos correctamente',
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
    public function getCursoById($id)
    {
        try {
            $query = Curso::find($id);

            return response()->json([
                'status' => '200',
                'message' => 'Curso obtenidos correctamente',
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
        try {
            $query = Curso::where('estado', 'activo')->get();

            return response()->json([
                'status' => '200',
                'message' => 'Cursos activos obtenidos correctamente',
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
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $state = $request->get('estado');
            $order = $request->get('order', 'desc');
            $search = $request->get('search');

            $query = Curso::select(
                'cursos.id',
                'cursos.titulo',
                'cursos.descripcion',
                'cursos.fechaInicio',
            );

            if ($state) {
                $query->where('cursos.estado',  $state);
            }

            if ($search) {
                $query->where('cursos.titulo', 'like', "%$search%")
                    ->orWhere('cursos.descripcion', 'like', "%$search%");
            }

            $cursos = $query->orderBy('cursos.created_at', $order)
                ->paginate($perPage);

            return response()->json([
                $cursos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al filtrar los cursos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
