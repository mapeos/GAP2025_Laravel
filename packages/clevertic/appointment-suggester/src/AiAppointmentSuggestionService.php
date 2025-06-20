<?php

namespace CleverTIC\AppointmentSuggester;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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
        $prompt = $this->buildPrompt($request);

        $headers = [];
        if (!empty($this->apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        $response = Http::withHeaders($headers)
            ->timeout(120)
            ->post("{$this->ollamaUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false
            ]);

        if (!$response->ok()) {
            throw new \RuntimeException('Error comunicándose con Ollama: ' . $response->body());
        }

        $output = $response->json('response');

        return $this->parseSuggestions($output, $request->maxSuggestions);
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

        return sprintf(
            "Quiero que actúes como un asistente para asignar citas en una agenda de forma optimizada.\n\n" .
            "- Sugiere %d huecos de cita disponibles en las fechas cercanas al %s.\n" .
            "- Cada cita debe durar %d minutos.\n" .
            "- El cliente prefiere%s.\n" .
            "- La persona que lo va a atender trabaja estos días: %s\n" .
            "- No propongas citas en estos días bloqueados: %s.\n" .
            "- Las sugerencias deben estar en formato exacto: YYYY-MM-DD HH:MM.\n" .
            "Responde únicamente con las fechas sugeridas, una por línea, sin texto adicional.",
            $request->maxSuggestions,
            $request->approximateDate->format('Y-m-d'),
            $request->durationMinutes,
            $preferencesText ?: ' sin preferencias',
            $workingDaysText,
            implode(', ', $request->excludedDates)
        );
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

    /*public function suggest2222(SuggestionRequest $request): Collection
    {
        // Simulación inicial: Devuelve 3 fechas aleatorias cerca de la fecha aproximada
        $suggestions = collect();

        for ($i = 1; $i <= $request->maxSuggestions; $i++) {
            $daysToAdd = rand(-$request->toleranceDays, $request->toleranceDays);
            $hour = rand(8, 17); // Horario razonable de clínica
            $minute = [0, 15, 30, 45][rand(0, 3)]; // Intervalos de 15 mins

            $suggestion = (clone $request->approximateDate)
                ->modify("+{$daysToAdd} days")
                ->setTime($hour, $minute);

            $suggestions->push($suggestion);
        }

        return $suggestions->sort();
    }*/
}
