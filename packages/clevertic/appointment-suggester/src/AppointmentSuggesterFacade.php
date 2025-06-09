<?php 
namespace CleverTIC\AppointmentSuggester;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;

class AppointmentSuggesterFacade implements AppointmentSuggesterInterface
{
    private AppointmentSuggestionService $ruleSuggester;
    private AiAppointmentSuggestionService $aiSuggester;

    public function __construct(
        AppointmentSuggestionService $ruleSuggester,
        AiAppointmentSuggestionService $aiSuggester
    ) {
        $this->ruleSuggester = $ruleSuggester;
        $this->aiSuggester = $aiSuggester;
    }

    public function suggest(SuggestionRequest $request): Collection
    {
        $mode = $request->patientPreferences['mode'] ?? 'mixed';

        if ($mode === 'rule') {
            return $this->ruleSuggester->suggest($request);
        }

        if ($mode === 'ai') {
            return $this->aiSuggester->suggest($request);
        }

        if ($mode === 'mixed') {
            $startTime = microtime(true);

            $aiResults = $this->aiSuggester->suggest($request);
            $ruleResults = $this->ruleSuggester->suggest($request);

            $results = $aiResults->merge($ruleResults);

            // Eliminar duplicados por fecha y hora
            $results = $results->unique(function ($date) {
                return $date->format('Y-m-d H:i');
            });

            // Ordenar por fecha
            $results = $results->sort();

            // Limitar al máximo de sugerencias pedido
            if ($request->maxSuggestions) {
                $results = $results->take($request->maxSuggestions);
            }

            $elapsed = microtime(true) - $startTime;

            // Log opcional o control de tiempos
            logger()->info('Mixed mode suggestion generated in ' . $elapsed . ' seconds.');

            return $results->values(); // reindexar
        }

        throw new \InvalidArgumentException("Modo de sugerencia no soportado: $mode");
    }
}
