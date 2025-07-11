<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controlador API especializado para funcionalidades de agenda/calendario
 * Proporciona endpoints optimizados para vistas de calendario y agenda
 */
class AgendaController extends Controller
{
    /**
     * Helper method to check if user has a specific role
     *
     * @param \App\Models\User|null $user
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
     * 📅 Obtener eventos del mes actual para vista de calendario
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mesActual(Request $request)
    {
        try {
            $user = Auth::user();
            $mes = $request->get('mes', now()->month);
            $año = $request->get('año', now()->year);
            
            $inicioMes = Carbon::create($año, $mes, 1)->startOfMonth();
            $finMes = Carbon::create($año, $mes, 1)->endOfMonth();
            
            $eventos = $this->getEventosConPermisos($user)
                ->whereBetween('fecha_inicio', [$inicioMes, $finMes])
                ->with(['tipoEvento:id,nombre,color'])
                ->orderBy('fecha_inicio', 'asc')
                ->get();

            $eventosFormateados = $eventos->map(function ($evento) {
                return $this->formatearEventoParaCalendario($evento);
            });

            return response()->json([
                'success' => true,
                'message' => 'Eventos del mes obtenidos correctamente',
                'data' => $eventosFormateados,
                'meta' => [
                    'mes' => $mes,
                    'año' => $año,
                    'total_eventos' => $eventosFormateados->count(),
                    'periodo' => [
                        'inicio' => $inicioMes->toISOString(),
                        'fin' => $finMes->toISOString()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][AGENDA_MES] Error al obtener eventos del mes', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos del mes'
            ], 500);
        }
    }

    /**
     * 📋 Obtener eventos del día actual para vista de agenda diaria
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function diaActual(Request $request)
    {
        try {
            $user = Auth::user();
            $fecha = $request->get('fecha', now()->toDateString());
            $fechaCarbon = Carbon::parse($fecha);
            
            $eventos = $this->getEventosConPermisos($user)
                ->whereDate('fecha_inicio', $fechaCarbon)
                ->with(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
                ->orderBy('fecha_inicio', 'asc')
                ->get();

            $eventosFormateados = $eventos->map(function ($evento) {
                return $this->formatearEventoParaAgenda($evento);
            });

            return response()->json([
                'success' => true,
                'message' => 'Eventos del día obtenidos correctamente',
                'data' => $eventosFormateados,
                'meta' => [
                    'fecha' => $fecha,
                    'fecha_formatted' => $fechaCarbon->format('d/m/Y'),
                    'dia_semana' => $fechaCarbon->locale('es')->dayName,
                    'total_eventos' => $eventosFormateados->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][AGENDA_DIA] Error al obtener eventos del día', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos del día'
            ], 500);
        }
    }

    /**
     * 📊 Obtener estadísticas de eventos para dashboard
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadisticas()
    {
        try {
            $user = Auth::user();
            $hoy = now();
            
            $queryBase = $this->getEventosConPermisos($user);
            
            // Contadores por período
            $eventosHoy = (clone $queryBase)->whereDate('fecha_inicio', $hoy)->count();
            $eventosSemana = (clone $queryBase)->whereBetween('fecha_inicio', [
                $hoy->startOfWeek(), 
                $hoy->copy()->endOfWeek()
            ])->count();
            $eventosMes = (clone $queryBase)->whereBetween('fecha_inicio', [
                $hoy->startOfMonth(), 
                $hoy->copy()->endOfMonth()
            ])->count();

            // Próximos 3 eventos
            $proximosEventos = (clone $queryBase)
                ->where('fecha_inicio', '>=', now())
                ->with(['tipoEvento:id,nombre,color'])
                ->orderBy('fecha_inicio', 'asc')
                ->limit(3)
                ->get()
                ->map(function ($evento) {
                    return [
                        'id' => $evento->id,
                        'titulo' => $evento->titulo,
                        'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                        'fecha_inicio_formatted' => $evento->fecha_inicio->format('d/m H:i'),
                        'tiempo_restante' => $evento->fecha_inicio->diffForHumans(),
                        'tipo_evento' => [
                            'nombre' => $evento->tipoEvento->nombre,
                            'color' => $evento->tipoEvento->color
                        ]
                    ];
                });

            // Distribución por tipo de evento
            $eventosPorTipo = (clone $queryBase)
                ->join('tipos_evento', 'eventos.tipo_evento_id', '=', 'tipos_evento.id')
                ->selectRaw('tipos_evento.nombre, tipos_evento.color, COUNT(*) as total')
                ->groupBy('tipos_evento.id', 'tipos_evento.nombre', 'tipos_evento.color')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas de eventos obtenidas correctamente',
                'data' => [
                    'contadores' => [
                        'hoy' => $eventosHoy,
                        'esta_semana' => $eventosSemana,
                        'este_mes' => $eventosMes
                    ],
                    'proximos_eventos' => $proximosEventos,
                    'distribucion_tipos' => $eventosPorTipo,
                    'fecha_actual' => now()->toISOString(),
                    'fecha_actual_formatted' => now()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][AGENDA_ESTADISTICAS] Error al obtener estadísticas', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas de eventos'
            ], 500);
        }
    }

    /**
     * 🔍 Buscar eventos por título o descripción
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscar(Request $request)
    {
        try {
            $user = Auth::user();
            $termino = $request->get('q', '');
            $limite = $request->get('limite', 10);
            
            if (strlen($termino) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'El término de búsqueda debe tener al menos 2 caracteres'
                ], 400);
            }

            $eventos = $this->getEventosConPermisos($user)
                ->where(function ($query) use ($termino) {
                    $query->where('titulo', 'like', "%{$termino}%")
                          ->orWhere('descripcion', 'like', "%{$termino}%");
                })
                ->with(['tipoEvento:id,nombre,color'])
                ->orderBy('fecha_inicio', 'desc')
                ->limit($limite)
                ->get();

            $eventosFormateados = $eventos->map(function ($evento) {
                return $this->formatearEventoParaAgenda($evento);
            });

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada',
                'data' => $eventosFormateados,
                'meta' => [
                    'termino_busqueda' => $termino,
                    'resultados_encontrados' => $eventosFormateados->count(),
                    'limite_aplicado' => $limite
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][AGENDA_BUSCAR] Error en búsqueda de eventos', [
                'user_id' => Auth::id(),
                'termino' => $request->get('q'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda de eventos'
            ], 500);
        }
    }

    /**
     * Obtener query base con permisos aplicados según el rol del usuario
     */
    private function getEventosConPermisos($user)
    {
        $query = Evento::where('status', true);
        $userId = $user->id;
        
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

        return $query;
    }

    /**
     * Formatear evento para vista de calendario (FullCalendar)
     */
    private function formatearEventoParaCalendario($evento)
    {
        return [
            'id' => $evento->id,
            'title' => $evento->titulo,
            'start' => $evento->fecha_inicio->toISOString(),
            'end' => $evento->fecha_fin->toISOString(),
            'color' => $evento->tipoEvento->color ?? '#3788d8',
            'backgroundColor' => $evento->tipoEvento->color ?? '#3788d8',
            'borderColor' => $evento->tipoEvento->color ?? '#3788d8',
            'textColor' => '#ffffff',
            'extendedProps' => [
                'descripcion' => $evento->descripcion,
                'ubicacion' => $evento->ubicacion,
                'url_virtual' => $evento->url_virtual,
                'tipo_evento' => $evento->tipoEvento->nombre,
                'es_creador' => $evento->creado_por === Auth::id()
            ]
        ];
    }

    /**
     * Formatear evento para vista de agenda detallada
     */
    private function formatearEventoParaAgenda($evento)
    {
        return [
            'id' => $evento->id,
            'titulo' => $evento->titulo,
            'descripcion' => $evento->descripcion,
            'fecha_inicio' => $evento->fecha_inicio->toISOString(),
            'fecha_fin' => $evento->fecha_fin->toISOString(),
            'fecha_inicio_formatted' => $evento->fecha_inicio->format('H:i'),
            'fecha_fin_formatted' => $evento->fecha_fin->format('H:i'),
            'duracion' => $evento->fecha_inicio->diffForHumans($evento->fecha_fin, true),
            'ubicacion' => $evento->ubicacion,
            'url_virtual' => $evento->url_virtual,
            'tipo_evento' => [
                'id' => $evento->tipoEvento->id,
                'nombre' => $evento->tipoEvento->nombre,
                'color' => $evento->tipoEvento->color
            ],
            'es_creador' => $evento->creado_por === Auth::id(),
            'participantes_count' => $evento->participantes ? $evento->participantes->count() : 0
        ];
    }
}
