<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCita;
use App\Models\Facultativo;
use App\Models\EspecialidadMedica;
use App\Models\TratamientoMedico;
use App\Models\MotivoCita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FacultativoCalendarioController extends Controller
{
    /**
     * Muestra el calendario de facultativos
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Datos para el calendario
        $pacientes = \App\Models\User::role('Paciente')->get();

        $facultativos = Facultativo::with(['user', 'especialidad'])->activos()->get();

        $especialidades = EspecialidadMedica::activas()->get();

        $tratamientos = TratamientoMedico::activos()->with('especialidad')->get();

        // Obtener motivos de cita médicos
        $motivosCita = MotivoCita::getMotivosActivos('medico');

        return view('facultativo.calendario', compact('pacientes', 'facultativos', 'especialidades', 'tratamientos', 'motivosCita'));
    }

    /**
     * Obtiene las citas para el calendario en formato JSON
     */
    public function getCitas()
    {
        $userId = Auth::id();
        
        // Cache de citas por usuario
        $cacheKey = "citas.user.{$userId}";
        $citas = Cache::remember($cacheKey, 300, function () use ($userId) {
            $query = SolicitudCita::with(['alumno.user', 'facultativo.user', 'especialidad', 'tratamiento'])
                ->select(['id', 'alumno_id', 'facultativo_id', 'especialidad_id', 'tratamiento_id', 'fecha_propuesta', 'motivo', 'estado', 'duracion_minutos', 'tipo_sistema'])
                ->where('tipo_sistema', 'medico')
                ->where('status', true);

            // Si es facultativo, mostrar solo sus citas
            if (Auth::user()->hasRole('Facultativo')) {
                $facultativo = Auth::user()->facultativo;
                if ($facultativo) {
                    $query->where('facultativo_id', $facultativo->id);
                }
            }

            // Si es paciente, mostrar solo sus citas
            if (Auth::user()->hasRole('Paciente')) {
                $query->where('alumno_id', $userId);
            }

            return $query->get()
                ->map(function ($cita) {
                    // Calcular fecha fin basada en la duración
                    $fechaInicio = $cita->fecha_propuesta;
                    $fechaFin = $fechaInicio->copy()->addMinutes($cita->duracion_minutos ?? 30);
                    
                    return [
                        'id' => $cita->id,
                        'title' => $cita->alumno->user->name . ' - ' . ($cita->especialidad->nombre ?? 'Consulta'),
                        'start' => $fechaInicio->toISOString(),
                        'end' => $fechaFin->toISOString(),
                        'color' => $this->getColorByEstado($cita->estado),
                        'motivo' => $cita->motivo,
                        'estado' => $cita->estado,
                        'duracion' => $cita->duracion_minutos ?? 30,
                        'paciente' => $cita->alumno->user->name,
                        'facultativo' => $cita->facultativo->user->name ?? 'N/A',
                        'especialidad' => $cita->especialidad->nombre ?? 'N/A',
                        'tratamiento' => $cita->tratamiento->nombre ?? 'N/A',
                        'tipo_sistema' => $cita->tipo_sistema,
                        'url' => route('facultativo.cita', $cita->id)
                    ];
                });
        });

        return response()->json($citas);
    }

    /**
     * Obtiene el color según el estado de la cita
     */
    private function getColorByEstado($estado)
    {
        switch ($estado) {
            case 'confirmada':
                return '#28a745'; // Verde
            case 'pendiente':
                return '#ffc107'; // Amarillo
            case 'cancelada':
                return '#dc3545'; // Rojo
            case 'completada':
                return '#17a2b8'; // Azul
            default:
                return '#6c757d'; // Gris
        }
    }

    /**
     * Obtiene los motivos de cita médicos
     */
    public function getMotivosCita()
    {
        $motivos = MotivoCita::getMotivosParaSelect('medico');
        return response()->json($motivos);
    }

    /**
     * Obtiene tratamientos por especialidad
     */
    public function getTratamientosPorEspecialidad($especialidadId)
    {
        $tratamientos = TratamientoMedico::where('especialidad_id', $especialidadId)
            ->where('activo', true)
            ->get();
        
        return response()->json($tratamientos);
    }

    /**
     * Obtiene pacientes para el modal
     */
    public function getPacientes()
    {
        $pacientes = \App\Models\User::role('Paciente')->get(['id', 'name', 'email']);
        return response()->json($pacientes);
    }
} 