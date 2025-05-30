<?php

namespace App\Console\Commands;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Console\Command;

/**
 * Class SuggestAppointment
 *
 * Comando para sugerir fechas de cita basadas en la disponibilidad del personal para atender,
 * preferencias del paciente y otros parámetros.
 * 
 * php artisan appointments:suggest 1 2 5 "2025-07-01" \
    --duration=45 \
    --tolerance=7 \
    --max=5 \
    --workingDays='{"monday":["08:00","14:00"],"tuesday":["10:00","18:00"],"thursday":["08:00","14:00"]}' \
    --excludedDates='["2025-07-04","2025-07-15"]' \
    --preferences='{"morning":true,"preferred_days":["tuesday","thursday"]}'
 */
class SuggestAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:suggest
        {clientId}
        {serviceId}
        {attendantId}
        {approximateDate}
        {--duration=60 : Duración en minutos}
        {--tolerance=5 : Días de tolerancia alrededor de la fecha}
        {--max=3 : Máximo de sugerencias}
        {--workingDays= : JSON de días laborales}
        {--excludedDates= : JSON de fechas excluidas}
        {--preferences= : JSON de preferencias del paciente}';
    


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sugiere fechas de cita para un usuario y servicio dados, considerando agenda y preferencias.';

    public function __construct(
        //protected AppointmentSuggesterInterface $appointmentSuggester
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $suggester = app('appointments.suggester.rule'); // usa el motor basado en reglas

        $request = $this->buildRequest();

        $suggestions = $suggester->suggest($request);

        $this->outputSuggestions($suggestions);
    }


    public function handle22222()
    {
        $patientId = (int) $this->argument('clientId');
        $treatmentId = (int) $this->argument('serviceId');
        $doctorId = (int) $this->argument('attendantId');
        $date = new \DateTime($this->argument('approximateDate'));

         // Parseo de opciones JSON
        $workingDays = $this->option('workingDays') ? json_decode($this->option('workingDays'), true) : [];
        $excludedDates = $this->option('excludedDates') ? json_decode($this->option('excludedDates'), true) : [];
        $preferences = $this->option('preferences') ? json_decode($this->option('preferences'), true) : [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Error en el formato JSON de algún parámetro.');
            return 1;
        }

        $request = new SuggestionRequest(
            patientId: $patientId,
            treatmentId: $treatmentId,
            approximateDate: $date,
            doctorId: $doctorId,
            workingDays: $workingDays,
            excludedDates: $excludedDates,
            patientPreferences: $preferences,
            durationMinutes: (int) $this->option('duration'),
            toleranceDays: (int) $this->option('tolerance'),
            maxSuggestions: (int) $this->option('max')
        );

        $suggestions = $this->appointmentSuggester->suggest($request);

        if ($suggestions->isEmpty()) {
            $this->warn('No se encontraron huecos disponibles.');
        } else {
            $this->info('Fechas sugeridas:');
            foreach ($suggestions as $suggestion) {
                $this->line($suggestion->format('Y-m-d H:i'));
            }
        }

        return 0;
    }
}
