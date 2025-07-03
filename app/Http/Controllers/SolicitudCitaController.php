<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCita;
use App\Models\User;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudCitaController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudCita::where('alumno_id', Auth::id())->with('profesor')->get();
        return view('solicitud_citas.index', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Intentando crear solicitud de cita', $request->all());

            // Detectar si es cita médica o académica
            $esCitaMedica = $request->has('facultativo_id') && $request->facultativo_id;
            
            if ($esCitaMedica) {
                // Validaciones para citas médicas
                $request->validate([
                    'facultativo_id' => 'required|exists:facultativos,id',
                    'motivo' => 'required|string|max:255',
                    'fecha_propuesta' => 'required|date|after:now',
                    'sintomas' => 'nullable|string|max:500',
                    'especialidad_id' => 'nullable|exists:especialidades_medicas,id',
                    'tratamiento_id' => 'nullable|exists:tratamientos_medicos,id',
                ]);

                $solicitud = SolicitudCita::create([
                    'alumno_id' => Auth::id(),
                    'facultativo_id' => $request->facultativo_id,
                    'motivo' => $request->motivo,
                    'fecha_propuesta' => $request->fecha_propuesta,
                    'estado' => 'pendiente',
                    'tipo_sistema' => 'medico',
                    'sintomas' => $request->sintomas,
                    'especialidad_id' => $request->especialidad_id,
                    'tratamiento_id' => $request->tratamiento_id,
                ]);
            } else {
                // Validaciones para citas académicas (comportamiento original)
            $request->validate([
                'profesor_id' => 'required|exists:users,id',
                'motivo' => 'required|string|max:255',
                'fecha_propuesta' => 'required|date|after:now',
            ]);

            $solicitud = SolicitudCita::create([
                'alumno_id' => Auth::id(),
                'profesor_id' => $request->profesor_id,
                'motivo' => $request->motivo,
                'fecha_propuesta' => $request->fecha_propuesta,
                'estado' => 'pendiente',
                    'tipo_sistema' => 'academico',
            ]);
            }

            Log::info('Solicitud creada exitosamente', [
                'solicitud_id' => $solicitud->id,
                'tipo' => $esCitaMedica ? 'medica' : 'academica'
            ]);

            return redirect()->back()->with('success', 'Solicitud enviada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear solicitud de cita', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    public function recibidas()
    {
        // Obtener solicitudes académicas (como profesor)
        $solicitudesAcademicas = SolicitudCita::where('profesor_id', Auth::id())
            ->where('tipo_sistema', 'academico')
            ->with('alumno')
            ->get();

        // Obtener solicitudes médicas (como facultativo)
        $facultativo = \App\Models\Facultativo::where('user_id', Auth::id())->first();
        $solicitudesMedicas = collect();
        
        if ($facultativo) {
            $solicitudesMedicas = SolicitudCita::where('facultativo_id', $facultativo->id)
                ->where('tipo_sistema', 'medico')
                ->with(['alumno', 'especialidad', 'tratamiento'])
                ->get();
        }

        return view('solicitud_citas.recibidas', compact('solicitudesAcademicas', 'solicitudesMedicas'));
    }

    public function ActualizarEstado(Request $request, SolicitudCita $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:confirmada,rechazada',
        ]);

        // Verificar permisos según el tipo de cita
        $tienePermiso = false;
        
        if ($solicitud->tipo_sistema === 'academico') {
            $tienePermiso = $solicitud->profesor_id === Auth::id();
        } elseif ($solicitud->tipo_sistema === 'medico') {
            $facultativo = \App\Models\Facultativo::where('user_id', Auth::id())->first();
            $tienePermiso = $facultativo && $solicitud->facultativo_id === $facultativo->id;
        }

        if (!$tienePermiso) {
            abort(403, 'No tienes permiso para actualizar esta solicitud.');
        }

        DB::beginTransaction();
        try {
            $solicitud->estado = $request->estado;
            $solicitud->save();

            // Si la solicitud se confirma, crear un evento en el calendario
            if ($request->estado === 'confirmada') {
                // Obtener el tipo de evento para citas
                $tipoEvento = TipoEvento::where('nombre', 'Cita')->first();
                if (!$tipoEvento) {
                    // Si no existe, crear el tipo de evento
                    $tipoEvento = TipoEvento::create([
                        'nombre' => 'Cita',
                        'color' => '#28a745', // Color verde para citas confirmadas
                        'descripcion' => 'Citas confirmadas entre profesores y alumnos'
                    ]);
                }

                // Determinar el título según el tipo de cita
                $titulo = $solicitud->tipo_sistema === 'medico' 
                    ? "Cita médica con {$solicitud->alumno->name}"
                    : "Cita con {$solicitud->alumno->name}";

                // Crear el evento
                $evento = Evento::create([
                    'titulo' => $titulo,
                    'descripcion' => $solicitud->motivo,
                    'fecha_inicio' => $solicitud->fecha_propuesta,
                    'fecha_fin' => date('Y-m-d H:i:s', strtotime($solicitud->fecha_propuesta . ' +1 hour')), // Duración de 1 hora por defecto
                    'tipo_evento_id' => $tipoEvento->id,
                    'creado_por' => Auth::id()
                ]);

                // Agregar participantes
                $participantes = [
                    $solicitud->alumno_id => ['rol' => 'alumno']
                ];

                if ($solicitud->tipo_sistema === 'academico') {
                    $participantes[$solicitud->profesor_id] = ['rol' => 'profesor'];
                } else {
                    // Para citas médicas, añadir el facultativo
                    $facultativo = \App\Models\Facultativo::find($solicitud->facultativo_id);
                    if ($facultativo) {
                        $participantes[$facultativo->user_id] = ['rol' => 'facultativo'];
                    }
                }

                $evento->participantes()->attach($participantes);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Estado de la solicitud actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }
}
