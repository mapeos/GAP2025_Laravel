<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        'fecha_inicio' => $curso->fechaInicio,  // ✅ Correct column name
        'fecha_fin' => $curso->fechaFin,        // ✅ Correct column name
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

    /**
     * 📝 Suscribir al usuario autenticado a un curso.
     *
     * @param Request $request
     * @param int $cursoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function suscribirse(Request $request, $cursoId)
    {
        try {
            // Obtener el usuario autenticado
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar que el usuario tenga una persona asociada
            $persona = $user->persona;
            if (!$persona) {
                return response()->json([
                    'status' => 400,
                    'message' => 'El usuario no tiene un perfil de persona asociado'
                ], 400);
            }

            // Buscar el curso
            $curso = Curso::find($cursoId);
            if (!$curso) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Curso no encontrado'
                ], 404);
            }

            // Verificar que el curso esté activo
            if ($curso->estado !== 'activo') {
                return response()->json([
                    'status' => 400,
                    'message' => 'El curso no está disponible para inscripciones'
                ], 400);
            }

            // Verificar si ya está inscrito
            $yaInscrito = $curso->personas()->where('persona_id', $persona->id)->exists();
            if ($yaInscrito) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Ya estás inscrito en este curso'
                ], 409);
            }

            // Verificar plazas disponibles
            $plazasDisponibles = $curso->getPlazasDisponibles();
            if ($plazasDisponibles <= 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No hay plazas disponibles en este curso'
                ], 400);
            }

            // Obtener el rol de "Alumno"
            $rolAlumno = \App\Models\RolParticipacion::where('nombre', 'Alumno')->first();
            if (!$rolAlumno) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Error del sistema: rol de alumno no encontrado'
                ], 500);
            }

            // Inscribir al usuario
            $curso->personas()->attach($persona->id, [
                'rol_participacion_id' => $rolAlumno->id,
                'estado' => 'pendiente',
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Te has inscrito correctamente al curso',
                'data' => [
                    'curso_id' => $curso->id,
                    'curso_titulo' => $curso->titulo,
                    'estado_inscripcion' => 'pendiente',
                    'plazas_restantes' => $curso->getPlazasDisponibles() - 1
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][CURSO_SUSCRIPCION] Error al suscribir usuario', [
                'user_id' => $request->user()?->id,
                'curso_id' => $cursoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * 📋 Obtener los cursos en los que está inscrito el usuario autenticado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function misCursos(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $persona = $user->persona;
            if (!$persona) {
                return response()->json([
                    'status' => 400,
                    'message' => 'El usuario no tiene un perfil de persona asociado'
                ], 400);
            }

            // Obtener los cursos del usuario con información de la participación
            $cursos = $persona->cursos()->with('participaciones.rol')->get();

            $data = $cursos->map(function ($curso) use ($persona) {
                $participacion = $curso->participaciones->where('persona_id', $persona->id)->first();

                $cursoData = $this->formatCurso($curso);
                $cursoData['inscripcion'] = [
                    'estado' => $participacion ? $participacion->estado : null,
                    'rol' => $participacion && $participacion->rol ? $participacion->rol->nombre : null,
                    'fecha_inscripcion' => $participacion ? $participacion->created_at : null
                ];

                return $cursoData;
            });

            return response()->json([
                'status' => 200,
                'message' => 'Cursos del usuario obtenidos correctamente',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('[API][MIS_CURSOS] Error al obtener cursos del usuario', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * 🚫 Cancelar suscripción a un curso.
     *
     * @param Request $request
     * @param int $cursoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarSuscripcion(Request $request, $cursoId)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $persona = $user->persona;
            if (!$persona) {
                return response()->json([
                    'status' => 400,
                    'message' => 'El usuario no tiene un perfil de persona asociado'
                ], 400);
            }

            // Buscar el curso
            $curso = Curso::find($cursoId);
            if (!$curso) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Curso no encontrado'
                ], 404);
            }

            // Verificar si está inscrito
            $inscrito = $curso->personas()->where('persona_id', $persona->id)->first();
            if (!$inscrito) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No estás inscrito en este curso'
                ], 404);
            }

            // Cancelar la inscripción
            $curso->personas()->detach($persona->id);

            return response()->json([
                'status' => 200,
                'message' => 'Suscripción cancelada correctamente',
                'data' => [
                    'curso_id' => $curso->id,
                    'curso_titulo' => $curso->titulo
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][CANCELAR_SUSCRIPCION] Error al cancelar suscripción', [
                'user_id' => $request->user()?->id,
                'curso_id' => $cursoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}

