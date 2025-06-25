<?php

namespace CleverTIC\AppointmentSuggester;

use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;
use App\Models\SolicitudCita;

class AppointmentSuggestionService implements AppointmentSuggesterInterface
{
    public function suggest(SuggestionRequest $request): Collection
    {
        $suggestions = collect();

        // Convertir a DateTimeImmutable para usar modify()
        $approximateDate = $request->approximateDate instanceof \DateTimeImmutable 
            ? $request->approximateDate 
            : \DateTimeImmutable::createFromInterface($request->approximateDate);

        $startDate = $approximateDate->modify("-{$request->toleranceDays} days");
        $endDate = $approximateDate->modify("+{$request->toleranceDays} days");

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->modify('+1 day')
        );

        // Cargar citas existentes del profesor en el rango
        $existingAppointments = SolicitudCita::query()
            ->where('profesor_id', $request->doctorId)
            ->whereBetween('fecha_propuesta', [$startDate, $endDate])
            ->whereNotIn('estado', ['cancelada', 'rechazada']) // Solo contar citas activas
            ->get()
            ->map(function ($appointment) {
                return [
                    'start' => $appointment->fecha_propuesta,
                    'end' => $appointment->fecha_propuesta->modify("+{$appointment->duracion_minutos} minutes"),
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
                if (isset($request->patientPreferences['times_of_day']) && 
                    $request->patientPreferences['times_of_day'] === 'morning') {
                    if ((int) $current->format('H') >= 13) {
                        break;
                    }
                }

                // Comprobar preferencia de tarde
                if (isset($request->patientPreferences['times_of_day']) && 
                    $request->patientPreferences['times_of_day'] === 'afternoon') {
                    if ((int) $current->format('H') < 13) {
                        $current = $current->modify("+{$request->durationMinutes} minutes");
                        continue;
                    }
                }

                // Comprobar rango horario específico
                if (!empty($request->patientPreferences['hour_range']) && is_array($request->patientPreferences['hour_range'])) {
                    [$startHour, $endHour] = $request->patientPreferences['hour_range'];
                    $currentHour = (int) $current->format('H');
                    if ($currentHour < (int) $startHour || $currentHour >= (int) $endHour) {
                        $current = $current->modify("+{$request->durationMinutes} minutes");
                        continue;
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
