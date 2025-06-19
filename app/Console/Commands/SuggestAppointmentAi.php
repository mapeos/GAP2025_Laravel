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
        {alumnoId : ID del alumno/estudiante}
        {cursoId : ID del curso o materia}
        {profesorId : ID del profesor}
        {approximateDate : Fecha aproximada (YYYY-MM-DD)}
        {--duration=60 : Duración de la cita en minutos}
        {--tolerance=5 : Días de tolerancia alrededor de la fecha}
        {--max=3 : Número máximo de sugerencias}
        {--workingDays= : JSON con días laborables y horarios}
        {--excludedDates= : JSON con fechas bloqueadas}
        {--preferences= : JSON con preferencias del alumno}';

    protected $description = 'Sugiere horarios de citas/consultas usando el motor de Inteligencia Artificial (Ollama + Mistral).';

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
            patientId: (int) $this->argument('alumnoId'),
            treatmentId: (int) $this->argument('cursoId'),
            workerId: (int) $this->argument('profesorId'),
            approximateDate: new \DateTime($this->argument('approximateDate')),
            doctorId: (int) $this->argument('profesorId'),
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
            $this->warn('No se encontraron horarios disponibles.');
        } else {
            $this->info('Horarios sugeridos (motor de IA):');
            foreach ($suggestions as $suggestion) {
                $this->line($suggestion->format('Y-m-d H:i'));
            }
        }
    }
}
