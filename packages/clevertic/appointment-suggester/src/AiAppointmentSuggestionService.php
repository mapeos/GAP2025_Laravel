<?php

namespace CleverTIC\AppointmentSuggester;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SolicitudCita;

class AiAppointmentSuggestionService implements AppointmentSuggesterInterface
{

    private string $ollamaUrl;
    private string $model;
    private string $apiKey;
    private const API_OLLAMA = "AAAAC3NzaC1lZDI1NTE5AAAAIDpUjBgYFtR7gAeDIzlSkBhN7/jeqXQiSqt5IC03vBbO";

    public function __construct(string $ollamaUrl = null, string $model = 'mistral')
    {
        $this->ollamaUrl = $ollamaUrl ?? $this->buildOllamaUrl();
        $this->model = $model;
        $this->apiKey = env('OLLAMA_API_KEY', self::API_OLLAMA);
    }

    private function buildOllamaUrl(): string
    {
        $host = env('OLLAMA_HOST', 'ai');
        $port = env('OLLAMA_PORT', '11434');
        return "http://{$host}:{$port}";
    }

    /**
     * Envía un prompt real a Ollama vía POST HTTP.
     * Recibe una respuesta generada por el modelo.
     * Extrae fechas en el formato YYYY-MM-DD HH:MM.
     * @return Collection<\DateTimeImmutable>
     */
    public function suggest(SuggestionRequest $request): Collection
    {
        try {
            $prompt = $this->buildPrompt($request);

            $headers = [];
            if (!empty($this->apiKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            // Intentar con timeout más corto para evitar HTTP 504
            try {
                $response = Http::withHeaders($headers)
                    ->timeout(30) // 30 segundos máximo para evitar timeout del frontend
                    ->post("{$this->ollamaUrl}/api/generate", [
                        'model' => $this->model,
                        'prompt' => $prompt,
                        'stream' => false
                    ]);

                if ($response->ok()) {
                    $output = $response->json('response');
                    $suggestions = $this->parseSuggestions($output, $request->maxSuggestions);
                    return $this->filterConflicts($suggestions, $request);
                }
            } catch (\Exception $e) {
                Log::warning('IA timeout, usando fallback', [
                    'error' => $e->getMessage()
                ]);
            }

            // Si la IA falla o tarda demasiado, usar fallback inmediatamente
            Log::info('Usando sugerencias de fallback debido a timeout de IA');
            return $this->fallbackSuggestions($request);

        } catch (\Exception $e) {
            Log::error('Error en AiAppointmentSuggestionService', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->fallbackSuggestions($request);
        }
    }

    private function buildPrompt(SuggestionRequest $request): string
    {
        $preferencesText = '';
        if (!empty($request->patientPreferences)) {
            if (!empty($request->patientPreferences['times_of_day'])) {
                if ($request->patientPreferences['times_of_day'] === 'morning') {
                    $preferencesText .= ' citas preferiblemente por la mañana (8:00 a 13:00)';
                } elseif ($request->patientPreferences['times_of_day'] === 'afternoon') {
                    $preferencesText .= ' citas preferiblemente por la tarde (13:00 a 20:00)';
                }
            }

            if (!empty($request->patientPreferences['hour_range']) && is_array($request->patientPreferences['hour_range'])) {
                [$startHour, $endHour] = $request->patientPreferences['hour_range'];
                $preferencesText .= sprintf(' citas en el rango horario de %s a %s', $startHour, $endHour);
            }

            if (!empty($request->patientPreferences['preferred_days'])) {
                $days = implode(', ', $request->patientPreferences['preferred_days']);
                $preferencesText .= sprintf(' preferiblemente los días %s', $days);
            }
        }

        $workingDaysText = '';
        foreach ($request->workingDays as $day => [$start, $end]) {
            $workingDaysText .= ucfirst($day) . " de {$start} a {$end}. ";
        }

        // Obtener citas existentes para el prompt
        $existingAppointments = $this->getExistingAppointments($request);
        $existingAppointmentsText = '';
        if ($existingAppointments->isNotEmpty()) {
            $existingAppointmentsText = "\n- Citas ya programadas: ";
            foreach ($existingAppointments as $appointment) {
                $existingAppointmentsText .= $appointment->format('Y-m-d H:i') . ", ";
            }
            $existingAppointmentsText = rtrim($existingAppointmentsText, ", ");
        }

        return sprintf(
            "Quiero que actúes como un asistente para asignar citas en una agenda de forma optimizada.\n\n" .
            "- Sugiere %d huecos de cita disponibles en las fechas cercanas al %s.\n" .
            "- Cada cita debe durar %d minutos.\n" .
            "- El cliente prefiere%s.\n" .
            "- La persona que lo va a atender trabaja estos días: %s\n" .
            "- No propongas citas en estos días bloqueados: %s.\n" .
            "%s\n" .
            "- Las sugerencias deben estar en formato exacto: YYYY-MM-DD HH:MM.\n" .
            "- NO propongas horarios que coincidan con las citas ya programadas.\n" .
            "Responde únicamente con las fechas sugeridas, una por línea, sin texto adicional.",
            $request->maxSuggestions,
            $request->approximateDate->format('Y-m-d'),
            $request->durationMinutes,
            $preferencesText ?: ' sin preferencias',
            $workingDaysText,
            implode(', ', $request->excludedDates),
            $existingAppointmentsText
        );
    }

    private function getExistingAppointments(SuggestionRequest $request): Collection
    {
        $approximateDate = $request->approximateDate instanceof \DateTimeImmutable 
            ? $request->approximateDate 
            : \DateTimeImmutable::createFromInterface($request->approximateDate);

        $startDate = $approximateDate->modify("-{$request->toleranceDays} days");
        $endDate = $approximateDate->modify("+{$request->toleranceDays} days");

        return SolicitudCita::query()
            ->where('profesor_id', $request->doctorId)
            ->whereBetween('fecha_propuesta', [$startDate, $endDate])
            ->whereNotIn('estado', ['cancelada', 'rechazada'])
            ->get()
            ->map(function ($appointment) {
                return $appointment->fecha_propuesta;
            });
    }

    private function filterConflicts(Collection $suggestions, SuggestionRequest $request): Collection
    {
        $existingAppointments = $this->getExistingAppointments($request);
        
        return $suggestions->filter(function ($suggestion) use ($existingAppointments, $request) {
            foreach ($existingAppointments as $existing) {
                $existingEnd = $existing->modify("+{$request->durationMinutes} minutes");
                $suggestionEnd = $suggestion->modify("+{$request->durationMinutes} minutes");
                
                if ($this->overlaps($suggestion, $suggestionEnd, $existing, $existingEnd)) {
                    return false;
                }
            }
            return true;
        });
    }

    private function overlaps($start1, $end1, $start2, $end2): bool
    {
        return $start1 < $end2 && $start2 < $end1;
    }

    private function parseSuggestions(string $output, int $max): Collection
    {
        $lines = explode("\n", trim($output));
        $dates = collect();

        foreach ($lines as $line) {
            if (preg_match('/(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2})/', $line, $matches)) {
                try {
                    $dateTime = new \DateTimeImmutable("{$matches[1]} {$matches[2]}");
                    $dates->push($dateTime);
                } catch (\Exception $e) {
                    continue; // Ignorar líneas mal formateadas
                }
            }
            if ($dates->count() >= $max) {
                break;
            }
        }

        return $dates;
    }

    private function fallbackSuggestions(SuggestionRequest $request): Collection
    {
        Log::info('Usando sugerencias de fallback', [
            'request' => $request
        ]);

        // Fallback inteligente basado en reglas simples
        $suggestions = collect();
        $approximateDate = $request->approximateDate instanceof \DateTimeImmutable 
            ? $request->approximateDate 
            : \DateTimeImmutable::createFromInterface($request->approximateDate);

        // Generar sugerencias en días laborables
        $workingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $currentDate = clone $approximateDate;
        
        // Retroceder hasta encontrar un día laborable
        while (!in_array(strtolower($currentDate->format('l')), $workingDays)) {
            $currentDate = $currentDate->modify('-1 day');
        }

        for ($i = 0; $i < $request->maxSuggestions; $i++) {
            // Avanzar al siguiente día laborable
            $suggestionDate = clone $currentDate;
            $suggestionDate = $suggestionDate->modify("+{$i} days");
            
            // Asegurar que sea un día laborable
            while (!in_array(strtolower($suggestionDate->format('l')), $workingDays)) {
                $suggestionDate = $suggestionDate->modify('+1 day');
            }

            // Generar horarios en el rango laboral (9:00 - 17:00)
            $hour = 9 + ($i % 8); // Distribuir en 8 horas
            $minute = [0, 15, 30, 45][$i % 4]; // Intervalos de 15 mins

            $suggestion = $suggestionDate->setTime($hour, $minute);
            
            // Verificar que no esté en las fechas excluidas
            $isExcluded = false;
            foreach ($request->excludedDates as $excludedDate) {
                if ($suggestion->format('Y-m-d') === $excludedDate) {
                    $isExcluded = true;
                    break;
                }
            }
            
            if (!$isExcluded) {
                $suggestions->push($suggestion);
            }
        }

        Log::info('Sugerencias de fallback generadas', [
            'count' => $suggestions->count(),
            'suggestions' => $suggestions->map(fn($s) => $s->format('Y-m-d H:i'))->toArray()
        ]);

        return $suggestions->sort();
    }
}
