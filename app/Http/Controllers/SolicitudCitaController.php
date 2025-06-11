<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCita;
use App\Models\User;
use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudCitaController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudCita::where('alumno_id', Auth::id())->with('profesor')->get();
        return view('solicitud_citas.index', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'profesor_id' => 'required|exists:users,id',
            'motivo' => 'required|string|max:255',
            'fecha_propuesta' => 'required|date|after:now',
        ]);

        SolicitudCita::create([
            'alumno_id' => Auth::id(),
            'profesor_id' => $request->profesor_id,
            'motivo' => $request->motivo,
            'fecha_propuesta' => $request->fecha_propuesta,
            'estado' => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente.');
    }

    public function recibidas()
    {
        $solicitudes = SolicitudCita::where('profesor_id', Auth::id())->with('alumno')->get();
        return view('solicitud_citas.recibidas', compact('solicitudes'));
    }

    public function ActualizarEstado(Request $request, SolicitudCita $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:confirmada,rechazada',
        ]);

        if ($solicitud->profesor_id !== Auth::id()) {
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

                // Crear el evento
                $evento = Evento::create([
                    'titulo' => "Cita con {$solicitud->alumno->name}",
                    'descripcion' => $solicitud->motivo,
                    'fecha_inicio' => $solicitud->fecha_propuesta,
                    'fecha_fin' => date('Y-m-d H:i:s', strtotime($solicitud->fecha_propuesta . ' +1 hour')), // Duración de 1 hora por defecto
                    'tipo_evento_id' => $tipoEvento->id,
                    'creado_por' => Auth::id()
                ]);

                // Agregar participantes (profesor y alumno)
                $evento->participantes()->attach([
                    $solicitud->profesor_id => ['rol' => 'profesor'],
                    $solicitud->alumno_id => ['rol' => 'alumno']
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Estado de la solicitud actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }
}
