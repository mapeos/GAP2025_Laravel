<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Helpers\UserAgentClassifier;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'activo')->count(),
            'pendingUsers' => User::where('status', 'pendiente')->count(),
            'totalNews' => \App\Models\News::count(),
            'publishedNews' => 0, // No filtrar por status, ya que la columna no existe
            'totalCursos' => \App\Models\Curso::count(),
        ];

        // Clasificación de usuarios por user-agent
        $users = User::select('user_agent')->get();
        $web = $api = $otro = 0;
        foreach ($users as $user) {
            $tipo = UserAgentClassifier::classify($user->user_agent);
            if ($tipo === 'Web') $web++;
            elseif ($tipo === 'API') $api++;
            else $otro++;
        }
        $leadSources = [
            'Web' => $web,
            'API' => $api,
            'Otro' => $otro
        ];

        // Obtener eventos para el calendario, incluyendo el color del tipo de evento
        $eventos = \App\Models\Evento::with('tipoEvento')->get()->map(function($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha_inicio,
                'end' => $evento->fecha_fin,
                'descripcion' => $evento->descripcion,
                'color' => $evento->tipoEvento ? $evento->tipoEvento->color : null,
                'tipoEvento' => $evento->tipoEvento ? [
                    'id' => $evento->tipoEvento->id,
                    'nombre' => $evento->tipoEvento->nombre,
                    'color' => $evento->tipoEvento->color,
                ] : null,
            ];
        });

        // Obtener alumnos para el modal del profesor
        $alumnos = User::role('alumno')->where('status', 'activo')->get();

        return view('admin.dashboard.index', compact('stats', 'leadSources', 'eventos', 'alumnos'));
    }
}
