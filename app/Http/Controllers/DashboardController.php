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

        // Obtener eventos para el calendario
        $eventos = \App\Models\Evento::all()->map(function($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha_inicio,
                'end' => $evento->fecha_fin,
                'descripcion' => $evento->descripcion,
            ];
        });

        return view('admin.dashboard.index', compact('stats', 'leadSources', 'eventos'));
    }
}
