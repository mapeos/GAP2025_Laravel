<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;

class SuggestAppointmentRule extends Command
{
    protected $signature = 'appointments:suggest-rule
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

    protected $description = 'Sugiere citas usando el motor de reglas.';

    public function handle()
    {
        $suggester = app('appointments.suggester.rule');

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
            $this->info('Fechas sugeridas (motor de reglas):');
            foreach ($suggestions as $suggestion) {
                $this->line($suggestion->format('Y-m-d H:i'));
            }
        }
    }
}
