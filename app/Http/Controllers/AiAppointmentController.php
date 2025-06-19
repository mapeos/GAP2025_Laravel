<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
                'success' => false,
                'data' => [
                    'suggestions' => $fallbackSuggestions,
                    'message' => 'IA no disponible. Mostrando sugerencias automáticas.',
                    'source' => 'fallback'
                ]
            ]);
        }
    }

    /**
     * Construye la solicitud de sugerencias
     */
    private function buildSuggestionRequest(Request $request): SuggestionRequest
    {
        // Configuración por defecto para horarios académicos más variados
        $workingDays = [
            'monday' => ['08:00', '18:00'],
            'tuesday' => ['09:00', '17:00'],
            'wednesday' => ['08:30', '17:30'],
            'thursday' => ['09:00', '18:00'],
            'friday' => ['08:00', '16:00']
        ];

        // Preferencias más variadas
        $preferences = [
            'times_of_day' => 'flexible', // Cambiado de 'morning' a 'flexible'
            'preferred_days' => ['tuesday', 'thursday', 'wednesday'], // Agregado miércoles
            'hour_range' => ['08:00', '17:00'] // Rango más amplio
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
     * Determina la prioridad basada en la posición
     */
    private function determinePriority(int $index): string
    {
        if ($index === 0) return 'alta';
        if ($index <= 2) return 'media';
        return 'baja';
    }

    /**
     * Genera sugerencias automáticas como fallback
     */
    private function generateFallbackSuggestions(string $motivo): array
    {
        $suggestions = [];
        $baseDate = new \DateTime('+1 day');
        
        // Horarios variados para evitar duplicados
        $horarios = ['09:00', '10:30', '14:00', '15:30', '16:00'];
        
        // Generar 5 sugerencias automáticas con horarios diferentes
        for ($i = 0; $i < 5; $i++) {
            $date = clone $baseDate;
            $date->modify("+{$i} days");
            
            // Solo días laborables (lunes a viernes)
            while ($date->format('N') > 5) {
                $date->modify('+1 day');
            }
            
            // Usar horario diferente para cada sugerencia
            $horario = $horarios[$i % count($horarios)];
            
            $suggestions[] = [
                'fecha' => $date->format('d-m-Y'),
                'hora_inicio' => $horario,
                'razon' => "Consulta: {$motivo}",
                'prioridad' => $this->determinePriority($i)
            ];
        }

        return $suggestions;
    }
} 