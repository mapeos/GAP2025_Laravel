<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\TipoEvento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventoApiController extends Controller
{
    /**
     * Helper method to check if user has a specific role
     *
     * @param User|null $user
     * @param string $role
     * @return bool
     */
    private function userHasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }

        // Check if user has the hasRole method (from Spatie Laravel Permission)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return false;
    }

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
            if ($this->userHasRole(Auth::user(), 'alumno')) {
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
            if ($this->userHasRole(Auth::user(), 'alumno') && $request->has('tipo_evento_id')) {
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
     * 📅 Obtener eventos para agenda/calendario con formato optimizado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agenda(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();

            // Parámetros de filtrado
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');
            $tipoEvento = $request->get('tipo_evento_id');
            $vista = $request->get('vista', 'month'); // month, week, day, agenda

            // Construir query base
            $query = Evento::with(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
                ->select(['id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'tipo_evento_id', 'creado_por', 'ubicacion', 'url_virtual', 'status'])
                ->where('status', true);

            // Filtros de fecha
            if ($fechaInicio) {
                $query->where('fecha_inicio', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->where('fecha_fin', '<=', $fechaFin);
            }

            // Filtro por tipo de evento
            if ($tipoEvento) {
                $query->where('tipo_evento_id', $tipoEvento);
            }

            // Aplicar filtros de permisos según rol
            $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            if ($this->userHasRole($user, 'alumno')) {
                // Alumnos ven eventos públicos + sus recordatorios personales
                $query->where(function ($q) use ($tipoRecordatorio, $userId) {
                    $q->where(function ($subQ) use ($tipoRecordatorio) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', '!=', $tipoRecordatorio->id);
                        }
                    })->orWhere(function ($subQ) use ($tipoRecordatorio, $userId) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', $tipoRecordatorio->id)
                                 ->where('creado_por', $userId);
                        }
                    });
                });
            } elseif ($this->userHasRole($user, 'profesor')) {
                // Profesores ven todos los eventos + sus recordatorios
                $query->where(function ($q) use ($tipoRecordatorio, $userId) {
                    $q->where(function ($subQ) use ($tipoRecordatorio) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', '!=', $tipoRecordatorio->id);
                        }
                    })->orWhere(function ($subQ) use ($tipoRecordatorio, $userId) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', $tipoRecordatorio->id)
                                 ->where('creado_por', $userId);
                        }
                    });
                });
            }
            // Administradores ven todo (sin filtros adicionales)

            $eventos = $query->orderBy('fecha_inicio', 'asc')->get();

            // Formatear eventos para diferentes vistas
            $eventosFormateados = $eventos->map(function ($evento) use ($vista) {
                $eventoData = [
                    'id' => $evento->id,
                    'title' => $evento->titulo,
                    'start' => $evento->fecha_inicio->toISOString(),
                    'end' => $evento->fecha_fin->toISOString(),
                    'description' => $evento->descripcion,
                    'location' => $evento->ubicacion,
                    'url_virtual' => $evento->url_virtual,
                    'color' => $evento->tipoEvento->color ?? '#3788d8',
                    'backgroundColor' => $evento->tipoEvento->color ?? '#3788d8',
                    'borderColor' => $evento->tipoEvento->color ?? '#3788d8',
                    'textColor' => '#ffffff',
                    'tipo_evento' => [
                        'id' => $evento->tipoEvento->id,
                        'nombre' => $evento->tipoEvento->nombre,
                        'color' => $evento->tipoEvento->color
                    ],
                    'creado_por' => $evento->creado_por,
                    'participantes_count' => $evento->participantes->count(),
                    'es_creador' => $evento->creado_por === Auth::id()
                ];

                // Información adicional para vista de agenda
                if ($vista === 'agenda') {
                    $eventoData['fecha_inicio_formatted'] = $evento->fecha_inicio->format('d/m/Y H:i');
                    $eventoData['fecha_fin_formatted'] = $evento->fecha_fin->format('d/m/Y H:i');
                    $eventoData['duracion'] = $evento->fecha_inicio->diffForHumans($evento->fecha_fin, true);
                    $eventoData['participantes'] = $evento->participantes->map(function ($participante) {
                        return [
                            'id' => $participante->id,
                            'name' => $participante->name,
                            'email' => $participante->email,
                            'rol' => $participante->pivot->rol ?? 'participante'
                        ];
                    });
                }

                return $eventoData;
            });

            return response()->json([
                'success' => true,
                'message' => 'Eventos de agenda obtenidos correctamente',
                'data' => $eventosFormateados,
                'meta' => [
                    'total' => $eventosFormateados->count(),
                    'vista' => $vista,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'filtros_aplicados' => [
                        'tipo_evento' => $tipoEvento,
                        'fecha_rango' => $fechaInicio && $fechaFin
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][AGENDA] Error al obtener eventos de agenda', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos de agenda'
            ], 500);
        }
    }

    /**
     * 📊 Obtener resumen de eventos para dashboard
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumen(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();

            $hoy = now()->startOfDay();
            $finSemana = now()->endOfWeek();
            $finMes = now()->endOfMonth();

            // Query base con permisos
            $queryBase = Evento::where('status', true);

            $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            // Aplicar filtros de permisos
            if ($this->userHasRole($user, 'alumno')) {
                $queryBase->where(function ($q) use ($tipoRecordatorio, $userId) {
                    $q->where(function ($subQ) use ($tipoRecordatorio) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', '!=', $tipoRecordatorio->id);
                        }
                    })->orWhere(function ($subQ) use ($tipoRecordatorio, $userId) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', $tipoRecordatorio->id)
                                 ->where('creado_por', $userId);
                        }
                    });
                });
            } elseif ($this->userHasRole($user, 'profesor')) {
                $queryBase->where(function ($q) use ($tipoRecordatorio, $userId) {
                    $q->where(function ($subQ) use ($tipoRecordatorio) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', '!=', $tipoRecordatorio->id);
                        }
                    })->orWhere(function ($subQ) use ($tipoRecordatorio, $userId) {
                        if ($tipoRecordatorio) {
                            $subQ->where('tipo_evento_id', $tipoRecordatorio->id)
                                 ->where('creado_por', $userId);
                        }
                    });
                });
            }

            // Contadores
            $eventosHoy = (clone $queryBase)->whereDate('fecha_inicio', $hoy)->count();
            $eventosSemana = (clone $queryBase)->whereBetween('fecha_inicio', [$hoy, $finSemana])->count();
            $eventosMes = (clone $queryBase)->whereBetween('fecha_inicio', [$hoy, $finMes])->count();

            // Próximos eventos (los 5 más cercanos)
            $proximosEventos = (clone $queryBase)
                ->with(['tipoEvento:id,nombre,color'])
                ->where('fecha_inicio', '>=', now())
                ->orderBy('fecha_inicio', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($evento) {
                    return [
                        'id' => $evento->id,
                        'titulo' => $evento->titulo,
                        'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                        'fecha_inicio_formatted' => $evento->fecha_inicio->format('d/m/Y H:i'),
                        'tiempo_restante' => $evento->fecha_inicio->diffForHumans(),
                        'tipo_evento' => [
                            'nombre' => $evento->tipoEvento->nombre,
                            'color' => $evento->tipoEvento->color
                        ]
                    ];
                });

            // Eventos por tipo
            $eventosPorTipo = (clone $queryBase)
                ->join('tipos_evento', 'eventos.tipo_evento_id', '=', 'tipos_evento.id')
                ->selectRaw('tipos_evento.nombre, tipos_evento.color, COUNT(*) as total')
                ->groupBy('tipos_evento.id', 'tipos_evento.nombre', 'tipos_evento.color')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Resumen de eventos obtenido correctamente',
                'data' => [
                    'contadores' => [
                        'hoy' => $eventosHoy,
                        'esta_semana' => $eventosSemana,
                        'este_mes' => $eventosMes
                    ],
                    'proximos_eventos' => $proximosEventos,
                    'eventos_por_tipo' => $eventosPorTipo,
                    'fecha_actual' => now()->toISOString(),
                    'fecha_actual_formatted' => now()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][RESUMEN_EVENTOS] Error al obtener resumen', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen de eventos'
            ], 500);
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
