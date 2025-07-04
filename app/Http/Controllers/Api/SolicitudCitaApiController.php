<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SolicitudCita;
use App\Models\User;
// Medical models removed - not supported by current database schema
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller for appointment requests (academic and medical)
 * Handles both academic consultations and medical appointments
 */
class SolicitudCitaApiController extends Controller
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
     * 📋 Get user's appointment requests
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $tipo = $request->get('tipo', 'all'); // all, academico, medico
            $estado = $request->get('estado'); // pendiente, confirmada, rechazada
            
            $query = SolicitudCita::with(['profesor:id,name,email', 'alumno:id,name,email']);
            
            // Filter by user role
            if ($this->userHasRole($user, 'alumno')) {
                $query->where('alumno_id', $user->id);
            } elseif ($this->userHasRole($user, 'profesor')) {
                $query->where('profesor_id', $user->id);
            } else {
                // Administrators can see all
            }
            
            // Note: Only academic appointments supported in current database schema
            // Medical appointments filtering removed due to database limitations
            
            // Filter by status
            if ($estado) {
                $query->where('estado', $estado);
            }
            
            $solicitudes = $query->orderBy('created_at', 'desc')->get();
            
            $data = $solicitudes->map(function ($solicitud) {
                return $this->formatSolicitudForApi($solicitud);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitudes obtenidas correctamente',
                'data' => $data,
                'meta' => [
                    'total' => $data->count(),
                    'filtros' => [
                        'tipo' => $tipo,
                        'estado' => $estado
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('[API][SOLICITUDES_CITA] Error al obtener solicitudes', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las solicitudes'
            ], 500);
        }
    }

    /**
     * 📝 Create a new appointment request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Base validation rules - compatible with current database
            $rules = [
                'motivo' => 'required|string|max:255', // Match HTTP controller limit
                'fecha_propuesta' => 'required|date|after:now',
                'profesor_id' => 'required|exists:users,id', // Only academic appointments supported
            ];

            // Note: Medical appointments not supported due to database schema limitations
            // Only academic appointments are compatible with current database structure
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Create academic appointment data - compatible with current database
            $data = [
                'alumno_id' => $user->id,
                'profesor_id' => $request->profesor_id,
                'motivo' => $request->motivo,
                'fecha_propuesta' => $request->fecha_propuesta,
                'estado' => 'pendiente',
            ];

            // Verify professor role
            $profesor = User::find($request->profesor_id);
            if (!$profesor || !$this->userHasRole($profesor, 'profesor')) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no es un profesor válido'
                ], 400);
            }
            
            $solicitud = SolicitudCita::create($data);
            
            // Load relationships for response - only academic fields available
            $solicitud->load(['profesor:id,name,email', 'alumno:id,name,email']);
            
            DB::commit();
            
            Log::info('[API][SOLICITUD_CITA] Solicitud creada exitosamente', [
                'solicitud_id' => $solicitud->id,
                'user_id' => $user->id,
                'tipo' => $request->tipo_sistema
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitud de cita enviada correctamente',
                'data' => $this->formatSolicitudForApi($solicitud)
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('[API][SOLICITUD_CITA] Error al crear solicitud', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la solicitud de cita'
            ], 500);
        }
    }

    /**
     * 👁️ Show specific appointment request
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $solicitud = SolicitudCita::with([
                'alumno:id,name,email',
                'profesor:id,name,email',
                'especialidad:id,nombre,descripcion,color',
                'tratamiento:id,nombre,descripcion,costo,duracion_minutos'
            ])->find($id);
            
            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }
            
            // Check permissions
            if (!$this->userHasRole($user, 'administrador') && 
                $solicitud->alumno_id !== $user->id && 
                $solicitud->profesor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver esta solicitud'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitud obtenida correctamente',
                'data' => $this->formatSolicitudForApi($solicitud, true)
            ]);
            
        } catch (\Exception $e) {
            Log::error('[API][SOLICITUD_CITA] Error al obtener solicitud', [
                'solicitud_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la solicitud'
            ], 500);
        }
    }

    /**
     * Format appointment request for API response
     * 
     * @param SolicitudCita $solicitud
     * @param bool $detailed
     * @return array
     */
    private function formatSolicitudForApi(SolicitudCita $solicitud, bool $detailed = false): array
    {
        $data = [
            'id' => $solicitud->id,
            'tipo_sistema' => 'academico', // Fixed value since only academic appointments supported
            'motivo' => $solicitud->motivo,
            'fecha_propuesta' => $solicitud->fecha_propuesta->toISOString(),
            'fecha_propuesta_formatted' => $solicitud->fecha_propuesta->format('d/m/Y H:i'),
            'estado' => $solicitud->estado,
            'created_at' => $solicitud->created_at->toISOString(),
            'created_at_formatted' => $solicitud->created_at->format('d/m/Y H:i'),
        ];
        
        // Add user information
        if ($solicitud->alumno) {
            $data['alumno'] = [
                'id' => $solicitud->alumno->id,
                'name' => $solicitud->alumno->name,
                'email' => $solicitud->alumno->email
            ];
        }
        
        if ($solicitud->profesor) {
            $data['profesor'] = [
                'id' => $solicitud->profesor->id,
                'name' => $solicitud->profesor->name,
                'email' => $solicitud->profesor->email
            ];
        }
        
        // Note: Medical appointments not supported due to database schema limitations
        // Only academic appointments are compatible with current database structure

        if ($detailed) {
            $data['duracion_estimada'] = 60; // Default 1 hour for academic appointments
            $data['tipo_cita'] = 'Cita Académica';
        }
        
        return $data;
    }

    /**
     * ✅ Update appointment request status (for professors/doctors)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:confirmada,rechazada',
                'observaciones_medicas' => 'nullable|string|max:1000',
                'diagnostico' => 'nullable|string|max:1000',
                'fecha_proxima_cita' => 'nullable|date|after:today',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $solicitud = SolicitudCita::find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            // Check permissions - only the assigned professor/doctor can update
            if ($solicitud->profesor_id !== $user->id && !$this->userHasRole($user, 'administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar esta solicitud'
                ], 403);
            }

            DB::beginTransaction();

            // Update appointment status
            $solicitud->estado = $request->estado;

            // Update medical fields if provided
            if ($request->has('observaciones_medicas')) {
                $solicitud->observaciones_medicas = $request->observaciones_medicas;
            }

            if ($request->has('diagnostico')) {
                $solicitud->diagnostico = $request->diagnostico;
            }

            if ($request->has('fecha_proxima_cita')) {
                $solicitud->fecha_proxima_cita = $request->fecha_proxima_cita;
            }

            $solicitud->save();

            // If confirmed, create calendar event
            if ($request->estado === 'confirmada') {
                $this->createCalendarEvent($solicitud);
            }

            DB::commit();

            // Load relationships for response
            $solicitud->load(['alumno:id,name,email', 'profesor:id,name,email', 'especialidad:id,nombre,color', 'tratamiento:id,nombre,costo,duracion_minutos']);

            Log::info('[API][SOLICITUD_CITA] Estado actualizado', [
                'solicitud_id' => $solicitud->id,
                'nuevo_estado' => $request->estado,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de la solicitud actualizado correctamente',
                'data' => $this->formatSolicitudForApi($solicitud, true)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('[API][SOLICITUD_CITA] Error al actualizar estado', [
                'solicitud_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de la solicitud'
            ], 500);
        }
    }

    /**
     * 🗑️ Cancel appointment request (for students)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        try {
            $user = Auth::user();

            $solicitud = SolicitudCita::find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            // Check permissions - only the student who created it can cancel
            if ($solicitud->alumno_id !== $user->id && !$this->userHasRole($user, 'administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cancelar esta solicitud'
                ], 403);
            }

            // Can only cancel pending requests
            if ($solicitud->estado !== 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cancelar solicitudes pendientes'
                ], 400);
            }

            // Note: Database enum doesn't support 'cancelada', using 'rechazada' instead
            $solicitud->estado = 'rechazada';
            $solicitud->save();

            Log::info('[API][SOLICITUD_CITA] Solicitud cancelada', [
                'solicitud_id' => $solicitud->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud cancelada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('[API][SOLICITUD_CITA] Error al cancelar solicitud', [
                'solicitud_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la solicitud'
            ], 500);
        }
    }

    /**
     * 📊 Get appointment statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $user = Auth::user();

            $query = SolicitudCita::query();

            // Filter by user role
            if ($this->userHasRole($user, 'alumno')) {
                $query->where('alumno_id', $user->id);
            } elseif ($this->userHasRole($user, 'profesor')) {
                $query->where('profesor_id', $user->id);
            }

            $total = $query->count();
            $pendientes = (clone $query)->where('estado', 'pendiente')->count();
            $confirmadas = (clone $query)->where('estado', 'confirmada')->count();
            $rechazadas = (clone $query)->where('estado', 'rechazada')->count();
            $canceladas = (clone $query)->where('estado', 'cancelada')->count();

            $academicas = (clone $query)->where('tipo_sistema', 'academico')->count();
            $medicas = (clone $query)->where('tipo_sistema', 'medico')->count();

            // Recent appointments
            $recientes = (clone $query)
                ->with(['profesor:id,name', 'especialidad:id,nombre,color'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($solicitud) {
                    return [
                        'id' => $solicitud->id,
                        'tipo_sistema' => $solicitud->tipo_sistema,
                        'motivo' => $solicitud->motivo,
                        'estado' => $solicitud->estado,
                        'fecha_propuesta_formatted' => $solicitud->fecha_propuesta->format('d/m/Y H:i'),
                        'profesor_name' => $solicitud->profesor ? $solicitud->profesor->name : null,
                        'especialidad' => $solicitud->especialidad ? [
                            'nombre' => $solicitud->especialidad->nombre,
                            'color' => $solicitud->especialidad->color
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas correctamente',
                'data' => [
                    'contadores' => [
                        'total' => $total,
                        'pendientes' => $pendientes,
                        'confirmadas' => $confirmadas,
                        'rechazadas' => $rechazadas,
                        'canceladas' => $canceladas
                    ],
                    'por_tipo' => [
                        'academicas' => $academicas,
                        'medicas' => $medicas
                    ],
                    'recientes' => $recientes
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('[API][SOLICITUD_CITA] Error al obtener estadísticas', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas'
            ], 500);
        }
    }

    /**
     * Create calendar event for confirmed appointment
     *
     * @param SolicitudCita $solicitud
     * @return void
     */
    private function createCalendarEvent(SolicitudCita $solicitud)
    {
        try {
            // Get or create event type - standardized with HTTP controller
            $tipoEvento = TipoEvento::firstOrCreate([
                'nombre' => 'Cita' // Consistent with SolicitudCitaController
            ], [
                'color' => '#28a745', // Green for confirmed appointments
                'descripcion' => 'Citas confirmadas entre profesores y alumnos'
            ]);

            // Calculate end time
            $duracion = $solicitud->duracion_minutos ?? 60;
            $fechaFin = $solicitud->fecha_propuesta->copy()->addMinutes($duracion);

            // Create event - consistent with HTTP controller format
            $evento = Evento::create([
                'titulo' => "Cita con {$solicitud->alumno->name}",
                'descripcion' => $solicitud->motivo,
                'fecha_inicio' => $solicitud->fecha_propuesta,
                'fecha_fin' => $fechaFin,
                'tipo_evento_id' => $tipoEvento->id,
                'creado_por' => $solicitud->profesor_id
            ]);

            // Add participants
            $evento->participantes()->attach([
                $solicitud->profesor_id => ['rol' => 'profesor'],
                $solicitud->alumno_id => ['rol' => 'alumno']
            ]);

        } catch (\Exception $e) {
            Log::error('[API][SOLICITUD_CITA] Error al crear evento de calendario', [
                'solicitud_id' => $solicitud->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
