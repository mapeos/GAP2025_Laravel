<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;

class AlumnoController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $persona = $user->persona ?? null;
        // Cursos en los que está inscrito el alumno
        $misCursos = collect();
        if (method_exists($user, 'cursos')) {
            $misCursos = $user->cursos;
        } elseif ($persona && method_exists($persona, 'cursos')) {
            $misCursos = $persona->cursos;
        }
        // Cursos disponibles (no inscritos)
        $cursosDisponibles = Curso::whereNotIn('id', $misCursos->pluck('id'))->get();
        // Mensajes recientes y usuarios de chat (simulación, ajustar según tu lógica real)
        $mensajesRecientes = collect();
        $usuariosChat = collect();
        $unreadCounts = [];
        // Próximos eventos y citas (simulación, ajustar según tu lógica real)
        $proximosEventos = collect();
        $proximasCitas = collect();
        return view('alumno.home', compact('user', 'persona', 'misCursos', 'cursosDisponibles', 'mensajesRecientes', 'usuariosChat', 'unreadCounts', 'proximosEventos', 'proximasCitas'));
    }
}
