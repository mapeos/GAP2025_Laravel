<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCita;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfesorController extends Controller
{
    public function home()
    {
        $profesor = Auth::user();
        
        // Obtener solicitudes pendientes
        $solicitudesPendientes = SolicitudCita::where('profesor_id', $profesor->id)
            ->where('estado', 'pendiente')
            ->count();

        // Obtener eventos del mes actual
        $eventosMes = Evento::whereHas('participantes', function($query) use ($profesor) {
                $query->where('user_id', $profesor->id);
            })
            ->whereMonth('fecha_inicio', Carbon::now()->month)
            ->count();

        // Obtener total de estudiantes (usuarios con rol estudiante)
        $totalEstudiantes = User::role('estudiante')->count();

        // Obtener próximas citas
        $proximasCitas = SolicitudCita::where('profesor_id', $profesor->id)
            ->where('fecha_propuesta', '>=', Carbon::now())
            ->orderBy('fecha_propuesta')
            ->take(5)
            ->get();

        // Obtener actividad reciente (últimos eventos y citas)
        $actividadReciente = Evento::whereHas('participantes', function($query) use ($profesor) {
                $query->where('user_id', $profesor->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('profesor.home', compact(
            'solicitudesPendientes',
            'eventosMes',
            'totalEstudiantes',
            'proximasCitas',
            'actividadReciente'
        ));
    }
} 