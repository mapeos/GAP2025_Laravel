<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCita;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Facultativo;
use App\Models\EspecialidadMedica;
use App\Models\TratamientoMedico;
use Illuminate\Support\Facades\Auth;

class FacultativoController extends Controller
{
    //index
    public function index()
    {
        $citasPendientes = SolicitudCita::where('estado', 'pendiente')->get();
        $eventosMes = SolicitudCita::where('estado', 'pendiente')->count();
        return view('facultativo.home', [ 'citasPendientes' => $citasPendientes, 'eventosMes' => $eventosMes]);
    }
    
    public function citas()
    {
        // Obtener datos para el modal de citas médicas
        $pacientes = User::role('Paciente')->get(); // Pacientes (usuarios con rol Paciente)
        $facultativos = Facultativo::with(['user', 'especialidad'])->activos()->get();
        $especialidades = EspecialidadMedica::activas()->get();
        $tratamientos = TratamientoMedico::activos()->with('especialidad')->get();
        
        // Obtener citas según el rol del usuario
        $user = Auth::user();
        $citas = collect();
        
        if ($user->hasRole('Administrador')) {
            // Los administradores ven todas las citas médicas
            $citas = SolicitudCita::where('tipo_sistema', 'medico')
                ->with(['alumno', 'especialidad', 'tratamiento', 'facultativo.user'])
                ->orderBy('fecha_propuesta')
                ->get();
        } else {
            // Los facultativos ven solo sus citas
            $facultativo = Facultativo::where('user_id', Auth::id())->first();
            if ($facultativo) {
                $citas = SolicitudCita::where('facultativo_id', $facultativo->id)
                    ->where('tipo_sistema', 'medico')
                    ->with(['alumno', 'especialidad', 'tratamiento'])
                    ->orderBy('fecha_propuesta')
                    ->get();
            }
        }
        
        return view('facultativo.citas', compact('pacientes', 'facultativos', 'especialidades', 'tratamientos', 'citas'));
    }
    
    public function cita($id = null)
    {
        if ($id) {
            $facultativo = Facultativo::where('user_id', Auth::id())->first();
            $cita = null;
            
            if ($facultativo) {
                $cita = SolicitudCita::where('id', $id)
                    ->where('facultativo_id', $facultativo->id)
                    ->where('tipo_sistema', 'medico')
                    ->with(['alumno', 'especialidad', 'tratamiento'])
                    ->first();
            }
            
            return view('facultativo.cita', compact('cita'));
        }
        
        return view('facultativo.cita');
    }
    
    public function newCita()
    {
        // Obtener datos para el modal de citas médicas
        $pacientes = User::role('Paciente')->get(); // Pacientes (usuarios con rol Paciente)
        $facultativos = Facultativo::with(['user', 'especialidad'])->activos()->get();
        $especialidades = EspecialidadMedica::activas()->get();
        $tratamientos = TratamientoMedico::activos()->with('especialidad')->get();
        
        return view('facultativo.nuevaCita', compact('pacientes', 'facultativos', 'especialidades', 'tratamientos'));
    }
    
    public function citasConfirmadas()
    {
        // Obtener citas confirmadas del facultativo actual
        $facultativo = Facultativo::where('user_id', Auth::id())->first();
        $citas = collect();
        
        if ($facultativo) {
            $citas = SolicitudCita::where('facultativo_id', $facultativo->id)
                ->where('tipo_sistema', 'medico')
                ->where('estado', 'confirmada')
                ->with(['alumno', 'especialidad', 'tratamiento'])
                ->orderBy('fecha_propuesta')
                ->get();
        }
        
        return view('facultativo.citasConfirmadas', compact('citas'));
    }
    
    public function citasPendientes()
    {
        // Obtener citas pendientes del facultativo actual
        $facultativo = Facultativo::where('user_id', Auth::id())->first();
        $citas = collect();
        
        if ($facultativo) {
            $citas = SolicitudCita::where('facultativo_id', $facultativo->id)
                ->where('tipo_sistema', 'medico')
                ->where('estado', 'pendiente')
                ->with(['alumno', 'especialidad', 'tratamiento'])
                ->orderBy('fecha_propuesta')
                ->get();
        }
        
        return view('facultativo.citasPendientes', compact('citas'));
    }
    
    public function pacientes()
    {
        // Obtener pacientes del facultativo actual
        $facultativo = Facultativo::where('user_id', Auth::id())->first();
        $pacientes = collect();
        
        if ($facultativo) {
            $pacientes = User::role('Paciente')
                ->whereHas('solicitudesCitas', function($query) use ($facultativo) {
                    $query->where('facultativo_id', $facultativo->id)
                          ->where('tipo_sistema', 'medico');
                })
                ->with(['solicitudesCitas' => function($query) use ($facultativo) {
                    $query->where('facultativo_id', $facultativo->id)
                          ->where('tipo_sistema', 'medico')
                          ->orderBy('fecha_propuesta', 'desc');
                }])
                ->get();
        }
        
        return view('facultativo.pacientes', compact('pacientes'));
    }
    
    public function paciente()
    {
        return view('facultativo.paciente');
    }
    
    public function tratamientos()
    {
        $tratamientos = TratamientoMedico::with('especialidad')->get();
        return view('facultativo.tratamientos', compact('tratamientos'));
    }
    
    public function tratamiento()
    {
        return view('facultativo.tratamiento');
    }
    
    public function newTratamiento()
    {
        $especialidades = EspecialidadMedica::activas()->get();
        return view('facultativo.nuevoTratamiento', compact('especialidades'));
    }
    public function editTratamiento()
    {
        return view('facultativo.editTratamiento');
    }
}
