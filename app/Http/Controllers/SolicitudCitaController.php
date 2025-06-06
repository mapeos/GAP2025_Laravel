<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCita;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class SolicitudCitaController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudCita::where('alumno_id', Auth::id())->with('profesor')->get();
        return view ('solicitud_citas.index', compact('solicitudes'));
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

        return redirect()->back()->with('success', 'Solicitud envada correctamente.');
    }

    public function recibidas()
    {
        $solicitudes = SolicitudCita::where('profesor_id', Auth::id())->with('alumno')->get();
        return view('solicitud_citas.recibidas', compact('solicitudes'));
    }

    public function ActualizarEstado(Request $request, SolicitudCita $solicitud)
    {
        $request->valiadate([
            'estado' => 'required|in:confirmada,rechazada',
        ]);

        if ($solicitud->profesor_id !== Auth::id()) {
            abort(403, 'No tienes permiso para actualizar esta solicitud.');
        }

        $solicitud->estado = $request->estado;
        $solicitud->save();

        return redirect()->back()->with('success', 'Estado de la solicitud actualizado correctamente.');

    }

}
