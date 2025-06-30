<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Application\Chat\SendMessage;
use App\Application\Chat\GetMessagesBetweenUsers;
use App\Application\Chat\GetLastChatsForUser;
use App\Application\Chat\GetUnreadCountForUser;
use App\Application\Chat\MarkMessagesAsRead;
use App\Domain\Chat\Message as DomainMessage;

class ChatController extends Controller
{
    // Mostrar lista de usuarios para chatear
    public function index(GetLastChatsForUser $getLastChats, GetUnreadCountForUser $getUnreadCount)
    {
        $profesores = User::role('Profesor')->where('id', '!=', Auth::id())->select('id', 'name')->paginate(10, ['*'], 'profesores');
        $alumnos = User::role('Alumno')->where('id', '!=', Auth::id())->select('id', 'name')->paginate(10, ['*'], 'alumnos');
        $usuarios = [
            'profesor' => $profesores,
            'alumno' => $alumnos,
        ];
        $mensajesRecibidos = $getLastChats->execute(Auth::id(), 10);
        $unreadCounts = $getUnreadCount->execute(Auth::id());
        return view('chat.index', compact('usuarios', 'mensajesRecibidos', 'unreadCounts'));
    }

    // Mostrar conversación con un usuario
    public function show($id, GetMessagesBetweenUsers $getMessages, MarkMessagesAsRead $markAsRead)
    {
        $user = User::findOrFail($id);
        // Marcar como leídos los mensajes recibidos de este usuario
        $markAsRead->execute(Auth::id(), $id);
        $mensajes = $getMessages->execute(Auth::id(), $id, 50);
        return view('chat.show', compact('user', 'mensajes'));
    }

    // Enviar mensaje
    public function store(Request $request, $id, SendMessage $sendMessage)
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000',
        ]);
        $sendMessage->execute(Auth::id(), $id, $request->mensaje);
        return redirect()->route('chat.show', $id);
    }

    // Búsqueda AJAX de usuarios por rol y nombre
    public function searchUsers(Request $request, GetUnreadCountForUser $getUnreadCount)
    {
        $rol = $request->input('rol');
        $search = $request->input('search');
        $query = User::role(ucfirst($rol))
            ->where('id', '!=', Auth::id());
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        $usuarios = $query->select('id', 'name')->paginate(10, ['*'], $rol);
        $unreadCounts = $getUnreadCount->execute(Auth::id());
        return view('chat._user_list', compact('usuarios', 'unreadCounts'))->render();
    }
}
