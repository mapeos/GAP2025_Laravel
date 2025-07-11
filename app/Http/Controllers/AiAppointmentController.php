<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SolicitudCita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AiAppointmentController extends Controller
{
    /**
     * Genera sugerencias de horarios para citas/consultas usando IA
     */
    public function suggestAppointments(Request $request): JsonResponse
    {
        $request->validate([
            'profesor_id' => 'required|integer|exists:users,id',
            'motivo' => 'required|string|max:500',
            'duracion' => 'nullable|string|max:50',
        ]);

        try {
            // Obtener el servicio de sugerencias de IA
            $suggester = app('appointments.suggester.ai');

            // Construir la solicitud
            $suggestionRequest = $this->buildSuggestionRequest($request);

            // Generar sugerencias
            $suggestions = $suggester->suggest($suggestionRequest);

            // Formatear respuesta para la interfaz web
            $formattedSuggestions = $this->formatSuggestionsForWeb($suggestions, $request->motivo);

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $formattedSuggestions,
                    'message' => 'Sugerencias generadas exitosamente',
                    'source' => 'ollama'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en sugerencias de IA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            // Fallback: generar sugerencias automáticas
            $fallbackSuggestions = $this->generateFallbackSuggestions($request->motivo);

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $fallbackSuggestions,
                    'message' => 'Sugerencias generadas (modo fallback)',
                    'source' => 'fallback'
                ]
            ]);
        }
    }

    /**
     * Genera sugerencias para el modal del profesor
     */
    public function suggest(Request $request): JsonResponse
    {
        try {
            Log::info('Iniciando generación de sugerencias para profesor', [
                'profesor_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'alumno_id' => 'required|integer|exists:users,id',
                'motivo' => 'required|string|max:500',
                'tipo_consulta' => 'required|string|max:100',
                'duracion' => 'nullable|integer|min:15|max:480',
                'fecha_preferida' => 'nullable|date',
                'hora_preferida' => 'nullable|string',
                'prioridad' => 'nullable|string|in:baja,normal,alta,urgente',
                'preferencias' => 'nullable|string|max:500',
            ]);

            // Verificar conectividad con Ollama
            $ollamaUrl = config('app.ollama_host', 'ai') . ':' . config('app.ollama_port', '11434');
            Log::info('Verificando conectividad con Ollama', ['url' => $ollamaUrl]);

            try {
                $response = Http::timeout(5)->get("http://{$ollamaUrl}/api/tags");
                Log::info('Ollama connectivity check successful', ['response_status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning('Ollama connectivity check failed', [
                    'url' => $ollamaUrl,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Obteniendo servicio de sugerencias');
            $suggester = app('appointments.suggester.ai');
            Log::info('Suggester service obtained', [
                'suggester_class' => get_class($suggester)
            ]);

            Log::info('Construyendo request de sugerencias');
            $suggestionRequest = $this->buildProfessorRequest($request);
            Log::info('Suggestion request built', [
                'request_data' => $suggestionRequest
            ]);

            Log::info('Generando sugerencias con el servicio');
            $suggestions = $suggester->suggest($suggestionRequest);

            Log::info('Suggestions generated', [
                'count' => $suggestions->count(),
                'suggestions' => $suggestions->toArray()
            ]);

            Log::info('Formateando sugerencias');
            $formattedSuggestions = $this->formatSuggestionsForProfessor($suggestions, $request->motivo);

            Log::info('Suggestions formatted', [
                'formatted_count' => count($formattedSuggestions)
            ]);

            return response()->json([
                'success' => true,
                'suggestions' => $formattedSuggestions,
                'message' => 'Sugerencias generadas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en sugerencias para profesor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'profesor_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar sugerencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea la cita desde el modal del profesor
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'alumno_id' => 'required|integer|exists:users,id',
            'motivo' => 'required|string|max:500',
            'tipo_consulta' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'duracion' => 'nullable|integer|min:15|max:480',
        ]);

        try {
            DB::beginTransaction();

            // Crear la solicitud de cita
            $cita = SolicitudCita::create([
                'alumno_id' => $request->alumno_id,
                'profesor_id' => Auth::id(),
                'motivo' => $request->motivo,
                'fecha_propuesta' => $request->fecha_inicio,
                'duracion_minutos' => $request->duracion ?? 60,
                'observaciones_medicas' => "Cita creada con IA - Tipo: {$request->tipo_consulta}",
                'estado' => 'confirmada',
                'tipo_sistema' => 'academico',
            ]);

            // Obtener o crear el tipo de evento para citas
            $tipoEvento = \App\Models\TipoEvento::where('nombre', 'Cita')->first();
            if (!$tipoEvento) {
                $tipoEvento = \App\Models\TipoEvento::create([
                    'nombre' => 'Cita',
                    'color' => '#28a745', // Color verde para citas confirmadas
                    'descripcion' => 'Citas confirmadas entre profesores y alumnos'
                ]);
            }

            // Obtener el nombre del alumno
            $alumno = \App\Models\User::find($request->alumno_id);
            $alumnoName = $alumno ? $alumno->name : 'Alumno';

            // Crear el evento en el calendario
            $evento = \App\Models\Evento::create([
                'titulo' => "Cita con {$alumnoName} - {$request->tipo_consulta}",
                'descripcion' => $request->motivo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'tipo_evento_id' => $tipoEvento->id,
                'creado_por' => Auth::id()
            ]);

            // Agregar participantes (profesor y alumno)
            $evento->participantes()->attach([
                Auth::id() => ['rol' => 'profesor'],
                $request->alumno_id => ['rol' => 'alumno']
            ]);

            DB::commit();
            
            // Limpiar caché de eventos
            $this->clearEventosCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cita creada exitosamente y agregada al calendario',
                'data' => [
                    'cita_id' => $cita->id,
                    'evento_id' => $evento->id,
                    'fecha' => $cita->fecha_propuesta->format('d/m/Y H:i'),
                    'alumno' => $alumnoName,
                    'tipo_consulta' => $request->tipo_consulta
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando cita con IA', [
                'error' => $e->getMessage(),
                'profesor_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    private function buildSuggestionRequest(Request $request): SuggestionRequest
    {
        // Configuración por defecto para horarios de profesor
        $workingDays = [
            'monday' => ['08:00', '18:00'],
            'tuesday' => ['08:00', '18:00'],
            'wednesday' => ['08:00', '18:00'],
            'thursday' => ['08:00', '18:00'],
            'friday' => ['08:00', '18:00'],
        ];

        $preferences = [
            'times_of_day' => 'morning', // Por defecto mañana
            'preferred_days' => ['tuesday', 'thursday'], // Por defecto martes y jueves
        ];

        // Simplificado: solo necesitamos alumno y profesor
        // patientId -> alumnoId, treatmentId -> cursoId (genérico), workerId/doctorId -> profesorId
        return new SuggestionRequest(
            patientId: Auth::id(), // alumnoId: ID del alumno actual
            treatmentId: 1, // cursoId: valor genérico (no se especifica en el modal)
            workerId: (int) $request->profesor_id, // profesorId: ID del profesor seleccionado
            approximateDate: new \DateTime('+1 day'), // Mañana por defecto
            doctorId: (int) $request->profesor_id, // profesorId: ID del profesor (duplicado por compatibilidad)
            workingDays: $workingDays,
            excludedDates: [],
            patientPreferences: $preferences, // preferencias del alumno
            durationMinutes: 60,
            toleranceDays: 7,
            maxSuggestions: 5
        );
    }

    private function buildProfessorRequest(Request $request): SuggestionRequest
    {
        // Configurar días laborables por defecto
        $workingDays = [
            'monday' => ['08:00', '18:00'],
            'tuesday' => ['08:00', '18:00'],
            'wednesday' => ['08:00', '18:00'],
            'thursday' => ['08:00', '18:00'],
            'friday' => ['08:00', '18:00'],
        ];

        // Configurar preferencias basadas en los parámetros del modal
        $preferences = [
            'times_of_day' => 'morning', // Por defecto mañana
            'preferred_days' => ['tuesday', 'thursday'], // Por defecto martes y jueves
        ];

        // Si hay hora preferida, ajustar las preferencias
        if ($request->hora_preferida) {
            $hour = (int) substr($request->hora_preferida, 0, 2);
            if ($hour < 12) {
                $preferences['times_of_day'] = 'morning';
            } elseif ($hour < 17) {
                $preferences['times_of_day'] = 'afternoon';
            } else {
                $preferences['times_of_day'] = 'evening';
            }
        }

        // Fecha aproximada
        $approximateDate = $request->fecha_preferida 
            ? new \DateTime($request->fecha_preferida)
            : new \DateTime('+1 day');

        // Duración basada en el tipo de consulta o duración manual
        $duration = $request->duracion ?? 60;

        return new SuggestionRequest(
            patientId: (int) $request->alumno_id,
            treatmentId: 1, // Genérico
            workerId: Auth::id(), // ID del profesor actual
            approximateDate: $approximateDate,
            doctorId: Auth::id(),
            workingDays: $workingDays,
            excludedDates: [],
            patientPreferences: $preferences,
            durationMinutes: $duration,
            toleranceDays: 7,
            maxSuggestions: 5
        );
    }

    /**
     * Formatea las sugerencias para la interfaz web
     */
    private function formatSuggestionsForWeb($suggestions, string $motivo): array
    {
        $formatted = [];
        
        foreach ($suggestions as $index => $suggestion) {
            $formatted[] = [
                'fecha' => $suggestion->format('d-m-Y'),
                'hora_inicio' => $suggestion->format('H:i'),
                'razon' => "Consulta: {$motivo}",
                'prioridad' => $this->determinePriority($index)
            ];
        }

        return $formatted;
    }

    /**
     * Formatea las sugerencias para el modal del profesor
     */
    private function formatSuggestionsForProfessor($suggestions, string $motivo): array
    {
        $formatted = [];
        
        foreach ($suggestions as $index => $suggestion) {
            // Calcular fecha de fin basada en la duración (usar 60 minutos por defecto)
            $duration = 60; // Por defecto 60 minutos
            $endTime = (clone $suggestion)->modify("+{$duration} minutes");
            
            $formatted[] = [
                'id' => $index + 1,
                'fecha_inicio' => $suggestion->format('Y-m-d H:i:s'),
                'fecha_fin' => $endTime->format('Y-m-d H:i:s'),
                'fecha' => $suggestion->format('d/m/Y'),
                'hora' => $suggestion->format('H:i'),
                'duracion' => $duration,
                'afinidad' => 85 - ($index * 10), // Afinidad decreciente
                'motivo' => $motivo,
                'prioridad' => $this->determinePriority($index)
            ];
        }

        return $formatted;
    }

    private function getDayName(string $englishDay): string
    {
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];

        return $days[$englishDay] ?? $englishDay;
    }

    /**
     * Determina la prioridad basada en la posición
     */
    private function determinePriority(int $index): string
    {
        if ($index === 0) return 'alta';
        if ($index <= 2) return 'media';
        return 'baja';
    }

    /**
     * Genera sugerencias de fallback
     */
    private function generateFallbackSuggestions(string $motivo): array
    {
        $suggestions = [];
        $baseDate = new \DateTime('+1 day');

        for ($i = 0; $i < 3; $i++) {
            $date = (clone $baseDate)->modify("+{$i} days");
            $hour = 9 + ($i * 2); // 9:00, 11:00, 13:00

            $suggestions[] = [
                'fecha' => $date->format('d-m-Y'),
                'hora_inicio' => sprintf('%02d:00', $hour),
                'razon' => "Consulta: {$motivo}",
                'prioridad' => $this->determinePriority($i)
            ];
        }

        return $suggestions;
    }

    /**
     * Genera sugerencias para el modal del facultativo
     */
    public function suggestForFacultativo(Request $request): JsonResponse
    {
        try {
            Log::info('Iniciando generación de sugerencias para facultativo', [
                'facultativo_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'paciente_id' => 'required|integer|exists:users,id',
                'motivo' => 'required|string|max:500',
                'tipo_consulta' => 'required|string|max:100',
                'duracion' => 'nullable|integer|min:15|max:480',
                'fecha_preferida' => 'nullable|string',
                'hora_preferida' => 'nullable|string',
                'urgencia' => 'nullable|string|in:baja,normal,alta,urgente',
                'preferencias' => 'nullable|string|max:500',
            ]);

            // Verificar conectividad con Ollama
            $ollamaUrl = config('app.ollama_host', 'ai') . ':' . config('app.ollama_port', '11434');
            Log::info('Verificando conectividad con Ollama', ['url' => $ollamaUrl]);

            try {
                $response = Http::timeout(5)->get("http://{$ollamaUrl}/api/tags");
                Log::info('Ollama connectivity check successful', ['response_status' => $response->status()]);
            } catch (\Exception $e) {
                Log::warning('Ollama connectivity check failed', [
                    'url' => $ollamaUrl,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Obteniendo servicio de sugerencias');
            $suggester = app('appointments.suggester.ai');
            Log::info('Suggester service obtained', [
                'suggester_class' => get_class($suggester)
            ]);

            Log::info('Construyendo request de sugerencias');
            $suggestionRequest = $this->buildFacultativoRequest($request);
            Log::info('Suggestion request built', [
                'request_data' => $suggestionRequest
            ]);

            Log::info('Generando sugerencias con el servicio');
            $suggestions = $suggester->suggest($suggestionRequest);

            Log::info('Suggestions generated', [
                'count' => $suggestions->count(),
                'suggestions' => $suggestions->toArray()
            ]);

            Log::info('Formateando sugerencias');
            $formattedSuggestions = $this->formatSuggestionsForFacultativo($suggestions, $request->motivo);

            Log::info('Suggestions formatted', [
                'formatted_count' => count($formattedSuggestions)
            ]);

            return response()->json([
                'success' => true,
                'suggestions' => $formattedSuggestions,
                'message' => 'Sugerencias generadas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en sugerencias para facultativo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'facultativo_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar sugerencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea la cita desde el modal del facultativo
     */
    public function createForFacultativo(Request $request): JsonResponse
    {
        $request->validate([
            'paciente_id' => 'required|integer|exists:users,id',
            'motivo' => 'required|string|max:500',
            'tipo_consulta' => 'required|string|max:100',
            'fecha_inicio' => 'required|string',
            'fecha_fin' => 'required|string',
            'duracion' => 'nullable|integer|min:15|max:480',
            'urgencia' => 'nullable|string|in:baja,normal,alta,urgente',
        ]);

        try {
            DB::beginTransaction();

            // Obtener el facultativo actual
            $facultativo = \App\Models\Facultativo::where('user_id', Auth::id())->first();
            if (!$facultativo) {
                throw new \Exception('Facultativo no encontrado');
            }

            // Crear la solicitud de cita médica
            $cita = SolicitudCita::create([
                'alumno_id' => $request->paciente_id, // paciente_id se mapea a alumno_id
                'facultativo_id' => $facultativo->id,
                'motivo' => $request->motivo,
                'fecha_propuesta' => $request->fecha_inicio,
                'duracion_minutos' => $request->duracion ?? 45,
                'observaciones_medicas' => "Cita creada con IA - Tipo: {$request->tipo_consulta} - Urgencia: {$request->urgencia}",
                'estado' => 'confirmada',
                'tipo_sistema' => 'medico',
            ]);

            // Obtener o crear el tipo de evento para citas médicas
            $tipoEvento = \App\Models\TipoEvento::where('nombre', 'Cita Médica')->first();
            if (!$tipoEvento) {
                $tipoEvento = \App\Models\TipoEvento::create([
                    'nombre' => 'Cita Médica',
                    'color' => '#0d6efd', // Color azul para citas médicas
                    'descripcion' => 'Citas médicas confirmadas entre facultativos y pacientes'
                ]);
            }

            // Obtener el nombre del paciente
            $paciente = \App\Models\User::find($request->paciente_id);
            $pacienteName = $paciente ? $paciente->name : 'Paciente';

            // Crear el evento en el calendario
            $evento = \App\Models\Evento::create([
                'titulo' => "Cita médica con {$pacienteName} - {$request->tipo_consulta}",
                'descripcion' => $request->motivo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'tipo_evento_id' => $tipoEvento->id,
                'creado_por' => Auth::id()
            ]);

            // Agregar participantes (facultativo y paciente)
            $evento->participantes()->attach([
                Auth::id() => ['rol' => 'facultativo'],
                $request->paciente_id => ['rol' => 'paciente']
            ]);

            DB::commit();
            
            // Limpiar caché de eventos
            $this->clearEventosCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cita médica creada exitosamente y agregada al calendario',
                'data' => [
                    'cita_id' => $cita->id,
                    'evento_id' => $evento->id,
                    'fecha' => $cita->fecha_propuesta->format('d/m/Y H:i'),
                    'paciente' => $pacienteName,
                    'tipo_consulta' => $request->tipo_consulta
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando cita médica con IA', [
                'error' => $e->getMessage(),
                'facultativo_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cita médica: ' . $e->getMessage()
            ], 500);
        }
    }

    private function buildFacultativoRequest(Request $request): SuggestionRequest
    {
        // Configurar días laborables por defecto para facultativos
        $workingDays = [
            'monday' => ['08:00', '20:00'],
            'tuesday' => ['08:00', '20:00'],
            'wednesday' => ['08:00', '20:00'],
            'thursday' => ['08:00', '20:00'],
            'friday' => ['08:00', '20:00'],
            'saturday' => ['09:00', '14:00'],
        ];

        // Configurar preferencias basadas en los parámetros del modal
        $preferences = [
            'times_of_day' => 'morning', // Por defecto mañana
            'preferred_days' => ['monday', 'wednesday', 'friday'], // Por defecto lunes, miércoles y viernes
        ];

        // Si hay hora preferida, ajustar las preferencias
        if ($request->hora_preferida) {
            $hour = (int) substr($request->hora_preferida, 0, 2);
            if ($hour < 12) {
                $preferences['times_of_day'] = 'morning';
            } elseif ($hour < 17) {
                $preferences['times_of_day'] = 'afternoon';
            } else {
                $preferences['times_of_day'] = 'evening';
            }
        }

        // Fecha aproximada
        $approximateDate = $request->fecha_preferida 
            ? new \DateTime($request->fecha_preferida)
            : new \DateTime('+1 day');

        // Duración basada en el tipo de consulta o duración manual
        $duration = $request->duracion ?? 45;

        return new SuggestionRequest(
            patientId: (int) $request->paciente_id,
            treatmentId: 1, // Genérico
            workerId: Auth::id(), // ID del facultativo actual
            approximateDate: $approximateDate,
            doctorId: Auth::id(),
            workingDays: $workingDays,
            excludedDates: [],
            patientPreferences: $preferences,
            durationMinutes: $duration,
            toleranceDays: 7,
            maxSuggestions: 5
        );
    }

    /**
     * Formatea las sugerencias para el modal del facultativo
     */
    private function formatSuggestionsForFacultativo($suggestions, string $motivo): array
    {
        $formatted = [];
        
        foreach ($suggestions as $index => $suggestion) {
            // Calcular fecha de fin basada en la duración (usar 45 minutos por defecto)
            $duration = 45; // Por defecto 45 minutos
            $endTime = (clone $suggestion)->modify("+{$duration} minutes");
            
            $formatted[] = [
                'id' => $index + 1,
                'fecha_inicio' => $suggestion->format('Y-m-d H:i:s'),
                'fecha_fin' => $endTime->format('Y-m-d H:i:s'),
                'fecha' => $suggestion->format('d/m/Y'),
                'hora' => $suggestion->format('H:i'),
                'duracion' => $duration,
                'afinidad' => 85 - ($index * 10), // Afinidad decreciente
                'motivo' => $motivo,
                'prioridad' => $this->determinePriority($index)
            ];
        }

        return $formatted;
    }

    /**
     * Limpia el cache de eventos
     */
    private function clearEventosCache()
    {
        \Illuminate\Support\Facades\Cache::forget('eventos.index');
        \Illuminate\Support\Facades\Cache::forget('eventos.user.' . \Illuminate\Support\Facades\Auth::id());
        \Illuminate\Support\Facades\Cache::forget('profesores.calendar');
        \Illuminate\Support\Facades\Cache::forget('alumnos.calendar');
        \Illuminate\Support\Facades\Cache::forget('pacientes.calendar');
    }
}