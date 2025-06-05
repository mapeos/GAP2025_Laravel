<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;

class CursoController extends Controller
{
    // Listar todos los cursos
    public function index()
    {
        $cursos = Curso::all();
        return response()->json([
            'status' => '200',
            'message' => 'Cursos obtenidos correctamente',
            'data' => $cursos
        ]);
    }

    // Obtener un curso por ID
    public function show($id)
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return response()->json([
                'status' => 'error',
                'message' => 'Curso no encontrado'
            ], 404);
        }
        return response()->json([
            'status' => '200',
            'message' => 'Curso obtenido correctamente',
            'data' => $curso
        ]);
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
        $cursos = Curso::where('estado', 'inactivos')->get();
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

    // Crear un nuevo curso
    /* public function store(Request $request)
    {
        $curso = Curso::create($request->all());
        return response()->json([
            'status' => '201',
            'data' => $curso
        ], 201);
    } */

    // Actualizar un curso existente
    /* public function update(Request $request, $id)
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return response()->json([
                'status' => 'error',
                'message' => 'Curso no encontrado'
            ], 404);
        }
        $curso->update($request->all());
        return response()->json([
            'status' => '200',
            'data' => $curso
        ]);
    } */

    // Eliminar un curso
    /* public function destroy($id)
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return response()->json([
                'status' => 'error',
                'message' => 'Curso no encontrado'
            ], 404);
        }
        $curso->delete();
        return response()->json([
            'status' => '200',
            'message' => 'Curso eliminado correctamente'
        ]);
    } */
}
