<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SolicitudCita;
use App\Models\EspecialidadMedica;
use App\Models\TratamientoMedico;
use App\Models\Facultativo;
use Carbon\Carbon;

class PacienteController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        
        // Obtener citas del paciente
        $citasPendientes = SolicitudCita::where('alumno_id', $user->id)
            ->where('tipo_sistema', 'medico')
            ->where('estado', 'pendiente')
            ->count();
            
        $citasConfirmadas = SolicitudCita::where('alumno_id', $user->id)
            ->where('tipo_sistema', 'medico')
            ->where('estado', 'confirmada')
            ->count();
            
        $proximasCitas = SolicitudCita::where('alumno_id', $user->id)
            ->where('tipo_sistema', 'medico')
            ->where('estado', 'confirmada')
            ->where('fecha_propuesta', '>=', Carbon::now())
            ->with(['facultativo.user', 'especialidad'])
            ->orderBy('fecha_propuesta')
            ->take(5)
            ->get();
            
        // Obtener especialidades para el modal
        $especialidades = EspecialidadMedica::activas()->get();
        
        return view('paciente.home', compact(
            'user',
            'citasPendientes',
            'citasConfirmadas',
            'proximasCitas',
            'especialidades'
        ));
    }
    
    public function solicitarCita(Request $request)
    {
        $request->validate([
            'especialidad_id' => 'required|exists:especialidades_medicas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos_medicos,id',
            'fecha_propuesta' => 'required|date|after:now',
            'hora_propuesta' => 'required|date_format:H:i',
            'motivo' => 'required|string|max:500',
        ]);
        
        // Combinar fecha y hora
        $fechaHora = $request->fecha_propuesta . ' ' . $request->hora_propuesta;
        
        // Crear la solicitud de cita
        $cita = SolicitudCita::create([
            'alumno_id' => Auth::id(),
            'especialidad_id' => $request->especialidad_id,
            'tratamiento_id' => $request->tratamiento_id,
            'fecha_propuesta' => $fechaHora,
            'motivo' => $request->motivo,
            'estado' => 'pendiente',
            'tipo_sistema' => 'medico',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitud de cita enviada exitosamente. El médico la revisará y te confirmará.',
            'cita_id' => $cita->id
        ]);
    }
    
    public function getTratamientos($especialidadId)
    {
        $tratamientos = TratamientoMedico::where('especialidad_id', $especialidadId)
            ->where('activo', true)
            ->get(['id', 'nombre', 'duracion_minutos']);
            
        return response()->json($tratamientos);
    }
    
    public function solicitarCitaPage()
    {
        $especialidades = EspecialidadMedica::activas()->get();
        return view('paciente.solicitar-cita', compact('especialidades'));
    }
    
    public function misCitas()
    {
        $user = Auth::user();
        
        $citas = SolicitudCita::where('alumno_id', $user->id)
            ->where('tipo_sistema', 'medico')
            ->with(['facultativo.user', 'especialidad', 'tratamiento'])
            ->orderBy('fecha_propuesta', 'desc')
            ->get();
            
        return view('paciente.mis-citas', compact('citas'));
    }
} 