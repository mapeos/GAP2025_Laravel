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
    public function tratamiento($id)
    {
        $tratamiento = TratamientoMedico::with('especialidad')->findOrFail($id);
        return view('facultativo.tratamiento', compact('tratamiento'));
    }
    
    public function newTratamiento()
    {
        $especialidades = EspecialidadMedica::activas()->get();
        return view('facultativo.nuevoTratamiento', compact('especialidades'));
    }
    public function editTratamiento($id)
    {
        $tratamiento = TratamientoMedico::with('especialidad')->findOrFail($id);
        $especialidades = EspecialidadMedica::activas()->get();
        return view('facultativo.editTratamiento', compact('tratamiento', 'especialidades'));
    }
    
    public function editCita($id)
    {
        $cita = SolicitudCita::with(['alumno', 'especialidad', 'tratamiento', 'facultativo.user'])->findOrFail($id);
        $pacientes = User::role('Paciente')->get();
        $facultativos = Facultativo::with(['user', 'especialidad'])->activos()->get();
        $especialidades = EspecialidadMedica::activas()->get();
        $tratamientos = TratamientoMedico::activos()->with('especialidad')->get();
        
        return view('facultativo.editCita', compact('cita', 'pacientes', 'facultativos', 'especialidades', 'tratamientos'));
    }
    
    public function storeTratamiento(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades_medicas,id',
            'duracion_minutos' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
        ]);
        
        TratamientoMedico::create($request->all());
        
        return redirect()->route('facultativo.tratamientos')->with('success', 'Tratamiento creado exitosamente.');
    }
    
    public function updateTratamiento(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades_medicas,id',
            'duracion_minutos' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
        ]);
        
        $tratamiento = TratamientoMedico::findOrFail($id);
        $tratamiento->update($request->all());
        
        return redirect()->route('facultativo.tratamientos')->with('success', 'Tratamiento actualizado exitosamente.');
    }
    
    public function destroyTratamiento($id)
    {
        $tratamiento = TratamientoMedico::findOrFail($id);
        $tratamiento->delete();
        
        return redirect()->route('facultativo.tratamientos')->with('success', 'Tratamiento eliminado exitosamente.');
    }
    
    public function storeCita(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:users,id',
            'facultativo_id' => 'required|exists:facultativos,id',
            'especialidad_id' => 'required|exists:especialidades_medicas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos_medicos,id',
            'fecha_propuesta' => 'required|date',
            'hora_propuesta' => 'required|date_format:H:i',
            'motivo' => 'required|string',
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);
        
        // Combinar fecha y hora
        $fechaHora = $request->fecha_propuesta . ' ' . $request->hora_propuesta;
        
        // Validar que la fecha y hora combinada sea futura
        $fechaCompleta = \Carbon\Carbon::parse($fechaHora);
        if ($fechaCompleta->isPast()) {
            return back()->withErrors(['fecha_propuesta' => 'La fecha y hora de la cita debe ser futura.'])->withInput();
        }
        
        SolicitudCita::create([
            'alumno_id' => $request->alumno_id,
            'facultativo_id' => $request->facultativo_id,
            'especialidad_id' => $request->especialidad_id,
            'tratamiento_id' => $request->tratamiento_id,
            'fecha_propuesta' => $fechaHora,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
            'tipo_sistema' => 'medico',
        ]);
        
        return redirect()->route('facultativo.citas')->with('success', 'Cita creada exitosamente.');
    }
    
    public function updateCita(Request $request, $id)
    {
        $request->validate([
            'alumno_id' => 'required|exists:users,id',
            'facultativo_id' => 'required|exists:facultativos,id',
            'especialidad_id' => 'required|exists:especialidades_medicas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos_medicos,id',
            'fecha_propuesta' => 'required|date',
            'hora_propuesta' => 'required|date_format:H:i',
            'motivo' => 'required|string',
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);
        
        $cita = SolicitudCita::findOrFail($id);
        $fechaHora = $request->fecha_propuesta . ' ' . $request->hora_propuesta;
        
        $cita->update([
            'alumno_id' => $request->alumno_id,
            'facultativo_id' => $request->facultativo_id,
            'especialidad_id' => $request->especialidad_id,
            'tratamiento_id' => $request->tratamiento_id,
            'fecha_propuesta' => $fechaHora,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
        ]);
        
        return redirect()->route('facultativo.citas')->with('success', 'Cita actualizada exitosamente.');
    }
}
