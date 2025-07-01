<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * 🔥 Helper: Format Curso with full URLs for portada and temario.
     */
    private function formatCurso($curso)
    {
        return [
            'id' => $curso->id,
            'titulo' => $curso->titulo,
            'descripcion' => $curso->descripcion,
            'fecha_inicio' => $curso->fecha_inicio,
            'fecha_fin' => $curso->fecha_fin,
            'plazas' => $curso->plazas,
            'estado' => $curso->estado,
            'precio' => $curso->precio,
            'portada_url' => $curso->portada_path 
                ? asset('storage/' . $curso->portada_path) 
                : null,
            'temario_url' => $curso->temario_path 
                ? asset('storage/' . $curso->temario_path) 
                : null,
            'created_at' => $curso->created_at,
            'updated_at' => $curso->updated_at,
            'deleted_at' => $curso->deleted_at,
        ];
    }

    /**
     * 📜 Listar cursos con filtros, búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $estado = $request->get('estado');
        $search = $request->get('search');
        $order = $request->get('order', 'desc');

        $query = Curso::withTrashed();

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%$search%")
                  ->orWhere('descripcion', 'like', "%$search%");
            });
        }

        $cursos = $query->orderBy('created_at', $order)->paginate($perPage);

        $data = $cursos->getCollection()->map(function ($curso) {
            return $this->formatCurso($curso);
        });

        return response()->json([
            'status' => 200,
            'message' => 'Lista de cursos obtenida correctamente',
            'data' => $data,
            'pagination' => [
                'current_page' => $cursos->currentPage(),
                'last_page' => $cursos->lastPage(),
                'per_page' => $cursos->perPage(),
                'total' => $cursos->total(),
            ]
        ]);
    }

    /**
     * 🔍 Obtener un curso por ID.
     */
    public function show($id)
    {
        $curso = Curso::withTrashed()->find($id);

        if (!$curso) {
            return response()->json([
                'status' => 404,
                'message' => 'Curso no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Curso obtenido correctamente',
            'data' => $this->formatCurso($curso)
        ]);
    }

    /**
     * ✅ Cursos activos.
     */
    public function activos()
    {
        $cursos = Curso::where('estado', 'activo')->get();

        $data = $cursos->map(function ($curso) {
            return $this->formatCurso($curso);
        });

        return response()->json([
            'status' => 200,
            'message' => 'Cursos activos obtenidos',
            'data' => $data
        ]);
    }

    /**
     * 🚫 Cursos inactivos.
     */
    public function inactivos()
    {
        $cursos = Curso::where('estado', 'inactivo')->get();

        $data = $cursos->map(function ($curso) {
            return $this->formatCurso($curso);
        });

        return response()->json([
            'status' => 200,
            'message' => 'Cursos inactivos obtenidos',
            'data' => $data
        ]);
    }

    /**
     * 📅 Cursos ordenados por fecha de inicio descendente.
     */
    public function ordenadosPorFechaInicioDesc()
    {
        $cursos = Curso::orderBy('fecha_inicio', 'desc')->get();

        $data = $cursos->map(function ($curso) {
            return $this->formatCurso($curso);
        });

        return response()->json([
            'status' => 200,
            'message' => 'Cursos ordenados por fecha de inicio descendente',
            'data' => $data
        ]);
    }

    /**
     * 🕒 Últimos N cursos.
     */
    public function ultimosCursos($n = 5)
    {
        $cursos = Curso::orderBy('created_at', 'desc')->take($n)->get();

        $data = $cursos->map(function ($curso) {
            return $this->formatCurso($curso);
        });

        return response()->json([
            'status' => 200,
            'message' => "Últimos {$n} cursos obtenidos",
            'data' => $data
        ]);
    }
}

