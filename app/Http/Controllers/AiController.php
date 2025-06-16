<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;

class AppointmentSuggestionController extends Controller
{
    private AppointmentSuggesterInterface $suggester;

    public function __construct(AppointmentSuggesterInterface $suggester)
    {
        $this->suggester = $suggester;
    }

    /**
     * Handle the incoming request to suggest appointment dates.
     * Petition example:
     *  {
     *       "mode": "ai",    //  "ai" or "rule" for rule-based suggestions
     *       "patient_id": 1,
     *       "treatment_id": 2,
     *       "worker_id": 3,
     *       "approximate_date": "2025-07-01",
     *       "working_days": {
     *           "monday": ["08:00", "14:00"],
     *           "tuesday": ["10:00", "18:00"],
     *           "wednesday": ["08:00", "14:00"]
     *       },
     *       "preferences": {
     *           "mode": "mixed",
     *           "times_of_day": "morning",
     *           "preferred_days": ["monday", "tuesday"],
     *           "hour_range": ["09:00", "11:00"]
     *       }
     *   }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'treatment_id' => 'required|integer',
            'worker_id' => 'required|integer',
            'approximate_date' => 'required|date',
            'duration' => 'nullable|integer',
            'tolerance' => 'nullable|integer',
            'max' => 'nullable|integer',
            'working_days' => 'required|array',
            'excluded_dates' => 'nullable|array',
            'preferences' => 'nullable|array',
        ]);

        $suggestionRequest = SuggestionRequest::fromArray($validated);

        $suggestions = $this->suggester->suggest($suggestionRequest);

        return response()->json([
            'suggestions' => $suggestions->map(fn($dt) => $dt->format('Y-m-d H:i'))->values(),
        ]);
    }
}
