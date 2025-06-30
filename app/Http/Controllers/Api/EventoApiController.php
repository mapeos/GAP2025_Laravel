<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventoApiController extends Controller
{
    /**
     * Obtiene la lista de eventos para el usuario autenticado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = Auth::id();

        // Cache de tipos de evento
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        // Optimizar consulta con eager loading y select específico
        $query = Evento::with(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
            ->select(['id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'tipo_evento_id', 'creado_por', 'ubicacion', 'url_virtual', 'status'])
            ->where('status', true);

        // Si es un recordatorio personal, solo mostrar los creados por el usuario actual
        if ($tipoRecordatorio) {
            $query->where(function($q) use ($userId, $tipoRecordatorio) {
                $q->where('tipo_evento_id', '!=', $tipoRecordatorio->id)
                  ->orWhere(function($q2) use ($userId, $tipoRecordatorio) {
                      $q2->where('tipo_evento_id', $tipoRecordatorio->id)
                        ->where('creado_por', $userId);
                  });
            });
        }

        // Cache de eventos por usuario
        $cacheKey = "eventos.api.user.{$userId}";
        $eventos = Cache::remember($cacheKey, 300, function () use ($query) {
            return $query->orderBy('fecha_inicio', 'desc')->get()
                ->map(function ($evento) {
                    return [
                        'id' => $evento->id,
                        'titulo' => $evento->titulo,
                        'descripcion' => $evento->descripcion,
                        'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                        'fecha_fin' => $evento->fecha_fin->toISOString(),
                        'ubicacion' => $evento->ubicacion,
                        'url_virtual' => $evento->url_virtual,
                        'tipo_evento' => [
                            'id' => $evento->tipoEvento->id,
                            'nombre' => $evento->tipoEvento->nombre,
                            'color' => $evento->tipoEvento->color
                        ],
                        'creado_por' => $evento->creado_por,
                        'participantes' => $evento->participantes->map(function($participante) {
                            return [
                                'id' => $participante->id,
                                'nombre' => $participante->name,
                                'email' => $participante->email,
                                'rol' => $participante->pivot->rol,
                                'estado_asistencia' => $participante->pivot->estado_asistencia
                            ];
                        })
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Muestra un evento específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $evento = Evento::with(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
                ->findOrFail($id);

            // Verificar si es un recordatorio personal y si el usuario actual es el creador
            $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este recordatorio personal.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                    'fecha_fin' => $evento->fecha_fin->toISOString(),
                    'ubicacion' => $evento->ubicacion,
                    'url_virtual' => $evento->url_virtual,
                    'tipo_evento' => [
                        'id' => $evento->tipoEvento->id,
                        'nombre' => $evento->tipoEvento->nombre,
                        'color' => $evento->tipoEvento->color
                    ],
                    'creado_por' => $evento->creado_por,
                    'participantes' => $evento->participantes->map(function($participante) {
                        return [
                            'id' => $participante->id,
                            'nombre' => $participante->name,
                            'email' => $participante->email,
                            'rol' => $participante->pivot->rol,
                            'estado_asistencia' => $participante->pivot->estado_asistencia
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }
    }

    /**
     * Almacena un nuevo evento
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'url_virtual' => 'nullable|url|max:255',
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['creado_por'] = Auth::id();

            // Verificar si el usuario es alumno y forzar tipo de evento a Recordatorio Personal
            if (Auth::user()->hasRole('alumno')) {
                $recordatorioPersonal = Cache::remember('tipo_recordatorio', 3600, function () {
                    return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
                });
                if ($recordatorioPersonal) {
                    $data['tipo_evento_id'] = $recordatorioPersonal->id;
                }
            }

            $evento = Evento::create($data);

            // Sincronizar participantes si se proporcionan
            if ($request->has('participantes')) {
                $evento->participantes()->attach($request->participantes);
            }

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            // Cargar la relación tipoEvento para obtener el color
            $evento->load('tipoEvento:id,nombre,color');

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'data' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                    'fecha_fin' => $evento->fecha_fin->toISOString(),
                    'tipo_evento' => [
                        'id' => $evento->tipoEvento->id,
                        'nombre' => $evento->tipoEvento->nombre,
                        'color' => $evento->tipoEvento->color
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza un evento específico
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $evento = Evento::findOrFail($id);

            // Verificar si es un recordatorio personal y si el usuario actual es el creador
            $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar este recordatorio personal.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
                'ubicacion' => 'nullable|string|max:255',
                'url_virtual' => 'nullable|url|max:255',
                'tipo_evento_id' => 'sometimes|required|exists:tipos_evento,id',
                'participantes' => 'nullable|array',
                'participantes.*' => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Si el usuario es alumno, no permitir cambiar el tipo de evento
            if (Auth::user()->hasRole('alumno') && $request->has('tipo_evento_id')) {
                $recordatorioPersonal = Cache::remember('tipo_recordatorio', 3600, function () {
                    return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
                });
                if ($recordatorioPersonal && $recordatorioPersonal->id != $request->tipo_evento_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para cambiar el tipo de evento.'
                    ], 403);
                }
            }

            DB::beginTransaction();
            try {
                $evento->update($request->only([
                    'titulo',
                    'descripcion',
                    'fecha_inicio',
                    'fecha_fin',
                    'ubicacion',
                    'url_virtual',
                    'tipo_evento_id',
                    'status'
                ]));

                // Sincronizar participantes si se proporcionan
                if ($request->has('participantes')) {
                    $evento->participantes()->sync($request->participantes);
                }

                // Limpiar cache
                $this->clearEventosCache();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'data' => $evento->load(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
                ]);

            } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el evento: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }
    }

    /**
     * Elimina un evento específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $evento = Evento::findOrFail($id);

            // Verificar si es un recordatorio personal y si el usuario actual es el creador
            $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este recordatorio personal.'
                ], 403);
            }

            DB::beginTransaction();
            try {
                // Eliminar participantes primero
                $evento->participantes()->detach();

                // Eliminar evento
                $evento->delete();

                // Limpiar cache
                $this->clearEventosCache();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Evento eliminado exitosamente'
                ]);

            } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar evento: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }
    }

    /**
     * Limpia la caché relacionada con eventos
     */
    private function clearEventosCache()
    {
        Cache::forget('eventos.index');

        // Optimización: Limpiar solo la caché del usuario actual en lugar de todos los usuarios
        $userId = Auth::id();
        if ($userId) {
            Cache::forget("eventos.user.{$userId}");
            Cache::forget("eventos.api.user.{$userId}");
        } else {
            // Fallback: limpiar cache de eventos por usuario solo si es necesario
            $users = DB::table('users')->pluck('id');
            foreach ($users as $userId) {
                Cache::forget("eventos.user.{$userId}");
                Cache::forget("eventos.api.user.{$userId}");
            }
        }
    }
}
