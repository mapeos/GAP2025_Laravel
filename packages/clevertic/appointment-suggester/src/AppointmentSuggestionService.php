<?php

namespace CleverTIC\AppointmentSuggester;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;
use App\Models\Appointment;

class AppointmentSuggestionService implements AppointmentSuggesterInterface
{
    public function suggest(SuggestionRequest $request): Collection
    {
        $suggestions = collect();

        $startDate = (clone $request->approximateDate)->modify("-{$request->toleranceDays} days");
        $endDate = (clone $request->approximateDate)->modify("+{$request->toleranceDays} days");

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            (clone $endDate)->modify('+1 day')
        );

        // Cargar citas existentes del doctor en el rango
        $existingAppointments = Appointment::query()
            ->where('worker_id', $request->doctorId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->whereNotIn('status', ['canceled']) // Solo contar citas activas
            ->get()
            ->map(function ($appointment) {
                return [
                    'start' => $appointment->start_time,
                    'end' => $appointment->end_time,
                ];
            });

        foreach ($period as $day) {
            $dayName = strtolower($day->format('l'));
            $dateFormatted = $day->format('Y-m-d');

            if (!isset($request->workingDays[$dayName])) {
                continue;
            }

            if (in_array($dateFormatted, $request->excludedDates)) {
                continue;
            }

            if (!empty($request->patientPreferences['preferred_days']) &&
                !in_array($dayName, $request->patientPreferences['preferred_days'])) {
                continue;
            }

            [$startTime, $endTime] = $request->workingDays[$dayName];
            $start = new \DateTimeImmutable("{$dateFormatted} {$startTime}");
            $end = new \DateTimeImmutable("{$dateFormatted} {$endTime}");

            $current = $start;
            while ($current < $end) {
                $slotEnd = $current->modify("+{$request->durationMinutes} minutes");

                // Comprobar preferencia de mañana
                if (isset($request->patientPreferences['morning']) && $request->patientPreferences['morning']) {
                    if ((int) $current->format('H') >= 13) {
                        break;
                    }
                }

                // Comprobar conflictos con citas existentes
                $conflict = $existingAppointments->first(function ($appointment) use ($current, $slotEnd) {
                    return $this->overlaps($current, $slotEnd, $appointment['start'], $appointment['end']);
                });

                if (!$conflict) {
                    $suggestions->push($current);
                }

                $current = $current->modify("+{$request->durationMinutes} minutes");
            }

            if ($suggestions->count() >= $request->maxSuggestions) {
                break;
            }
        }

        return $suggestions->sort()->take($request->maxSuggestions);
    }

    
    private function overlaps($start1, $end1, $start2, $end2): bool
    {
        return $start1 < $end2 && $start2 < $end1;
    }
    

    /* Ejemplo de uso:
    use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
    use CleverTIC\AppointmentSuggester\AppointmentSuggestionService;
    
$request = new SuggestionRequest(
    patientId: 1,
    treatmentId: 2,
    approximateDate: new \DateTime('2025-07-01'),
    doctorId: 5,
    workingDays: [
        'monday' => ['08:00', '14:00'],
        'tuesday' => ['10:00', '18:00'],
        'thursday' => ['08:00', '14:00'],
    ],
    excludedDates: ['2025-07-04', '2025-07-15'],
    patientPreferences: [
        'morning' => true,
        'preferred_days' => ['tuesday', 'thursday'],
    ],
    durationMinutes: 45,
    maxSuggestions: 5
);

    */
}
