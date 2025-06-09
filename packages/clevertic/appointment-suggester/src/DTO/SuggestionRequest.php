<?php

namespace CleverTIC\AppointmentSuggester\DTO;

class SuggestionRequest
{
    public function __construct(
        public int $patientId,
        public int $treatmentId,
        public int $workerId,
        public \DateTimeInterface $approximateDate,
        public int $doctorId,
        public array $workingDays, // ej: ['monday' => ['08:00', '14:00'], ...]
        public array $excludedDates = [], // ['2025-07-04', '2025-07-15']
        public array $patientPreferences = [], // ['morning' => true, 'preferred_days' => ['tuesday', 'thursday']]
        public int $durationMinutes = 60, // Por defecto 60 min (1h)
        public int $toleranceDays = 5, // +-5 días alrededor de approximateDate
        public int $maxSuggestions = 3
    ) {}
}


        