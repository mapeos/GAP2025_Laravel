<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Application\Chat\GetLastChatsForUser;
use App\Application\Chat\GetUnreadCountForUser;
use App\Models\User;

class AlumnoController extends Controller
{
    public function home(GetLastChatsForUser $getLastChats, GetUnreadCountForUser $getUnreadCount)
    {
        $user = Auth::user();
        $persona = $user->persona ?? null;
        // Cursos en los que está inscrito el alumno
        $misCursos = method_exists($user, 'cursos') ? $user->cursos : ($persona && method_exists($persona, 'cursos') ? $persona->cursos : collect());
        // Cursos disponibles (no inscritos)
        $cursosDisponibles = \App\Models\Curso::whereNotIn('id', $misCursos->pluck('id'))->get();
        // Mensajes recientes y usuarios de chat reales
        $mensajesRecientes = collect($getLastChats->execute($user->id, 5));
        $usuariosIds = $mensajesRecientes->map(fn($m) => $m->senderId == $user->id ? $m->receiverId : $m->senderId)->unique();
        $usuariosChat = User::whereIn('id', $usuariosIds)->get();
        $unreadCounts = $getUnreadCount->execute($user->id);
        // Próximos eventos y citas (simulación, ajustar según tu lógica real)
        $proximosEventos = collect();
        $proximasCitas = collect();
        return view('alumno.home', compact('user', 'persona', 'misCursos', 'cursosDisponibles', 'mensajesRecientes', 'usuariosChat', 'unreadCounts', 'proximosEventos', 'proximasCitas'));
    }
}
