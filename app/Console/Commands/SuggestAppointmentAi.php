<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;

/*

php artisan appointments:suggest-ai 1 2 5 "2025-07-01" \
  --duration=60 \
  --tolerance=7 \
  --max=3 \
  --workingDays='{"monday":["08:00","14:00"],"tuesday":["10:00","18:00"],"wednesday":["08:00","14:00"],"thursday":["08:00","14:00"]}' \
  --excludedDates='["2025-07-04", "2025-07-15"]' \
  --preferences='{"times_of_day":"morning","preferred_days":["tuesday","thursday"],"hour_range":["09:00","11:00"]}'



  php artisan appointments:suggest-ai 1 2 5 "2025-07-01"   --duration=60   --tolerance=7   --max=3   --workingDays='{"monday":["08:00","14:00"],"tuesday":["10:00","18:00"],"wednesday":["08:00","14:00"],"thursday":["08:00","14:00"]}'   --excludedDates='["2025-07-04", "2025-07-15"]'   --preferences='{"times_of_day":"morning","preferred_days":["tuesday","thursday"],"hour_range":["09:00","11:00"]}'
  
*/
class SuggestAppointmentAi extends Command
{
    protected $signature = 'appointments:suggest-ai
        {patientId}
        {treatmentId}
        {doctorId}
        {approximateDate}
        {--duration=60}
        {--tolerance=5}
        {--max=3}
        {--workingDays=}
        {--excludedDates=}
        {--preferences=}';

    protected $description = 'Sugiere citas usando el motor de Inteligencia Artificial (simulado).';

    public function handle()
    {
        $suggester = app('appointments.suggester.ai');

        $request = $this->buildRequest();

        $suggestions = $suggester->suggest($request);

        $this->outputSuggestions($suggestions);
    }

    protected function buildRequest(): SuggestionRequest
    {
        $workingDays = $this->option('workingDays') ? json_decode($this->option('workingDays'), true) : [];
        $excludedDates = $this->option('excludedDates') ? json_decode($this->option('excludedDates'), true) : [];
        $preferences = $this->option('preferences') ? json_decode($this->option('preferences'), true) : [];

        return new SuggestionRequest(
            patientId: (int) $this->argument('patientId'),
            treatmentId: (int) $this->argument('treatmentId'),
            approximateDate: new \DateTime($this->argument('approximateDate')),
            doctorId: (int) $this->argument('doctorId'),
            workingDays: $workingDays,
            excludedDates: $excludedDates,
            patientPreferences: $preferences,
            durationMinutes: (int) $this->option('duration'),
            toleranceDays: (int) $this->option('tolerance'),
            maxSuggestions: (int) $this->option('max')
        );
    }

    protected function outputSuggestions($suggestions)
    {
        if ($suggestions->isEmpty()) {
            $this->warn('No se encontraron huecos disponibles.');
        } else {
            $this->info('Fechas sugeridas (motor de IA):');
            foreach ($suggestions as $suggestion) {
                $this->line($suggestion->format('Y-m-d H:i'));
            }
        }
    }
}
