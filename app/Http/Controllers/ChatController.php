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
        // Traer todos los usuarios menos el actual, con la relación persona
        $usuariosQuery = User::where('id', '!=', Auth::id())->with('persona');
        $usuariosTodos = $usuariosQuery->select('id', 'name')->get();
        // Agrupar por rol si se desea mantener la separación visual
        $usuariosPorRol = [
            'profesor' => $usuariosTodos->filter(fn($u) => $u->hasRole('Profesor')),
            'alumno' => $usuariosTodos->filter(fn($u) => $u->hasRole('Alumno')),
            'otros' => $usuariosTodos->filter(fn($u) => !$u->hasRole('Profesor') && !$u->hasRole('Alumno')),
        ];
        $mensajesRecibidos = $getLastChats->execute(Auth::id(), 10);
        $unreadCounts = $getUnreadCount->execute(Auth::id());
        return view('chat.index', [
            'usuarios' => $usuariosPorRol,
            'mensajesRecibidos' => $mensajesRecibidos,
            'unreadCounts' => $unreadCounts,
        ]);
    }

    // Mostrar conversación con un usuario
    public function show($id, GetMessagesBetweenUsers $getMessages, MarkMessagesAsRead $markAsRead)
    {
        $user = User::findOrFail($id);
        $markAsRead->execute(Auth::id(), $id);
        $mensajes = $getMessages->execute(Auth::id(), $id, 50);
        if (request()->ajax() || request('ajax')) {
            // Formatear fechas y foto para JS
            $mensajes = collect($mensajes)->map(function($m) use ($user) {
                $m = (object) $m;
                $m->createdAt_fmt = isset($m->createdAt) && $m->createdAt ? \Carbon\Carbon::parse($m->createdAt)->format('d/m/Y H:i') : '';
                if ($m->senderId != Auth::id()) {
                    $m->foto_perfil = optional($user->persona)->foto_perfil;
                } else {
                    $m->foto_perfil = optional(Auth::user()->persona)->foto_perfil;
                }
                return $m;
            });
            return response()->json([
                'user' => [ 'id' => $user->id, 'name' => $user->name ],
                'mensajes' => $mensajes,
                'authId' => Auth::id(),
            ]);
        }
        return view('chat.show', compact('user', 'mensajes'));
    }

    // Enviar mensaje
    public function store(Request $request, $id, SendMessage $sendMessage)
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000',
        ]);
        $sendMessage->execute(Auth::id(), $id, $request->mensaje);
        if ($request->ajax() || $request->wantsJson()) {
            // Devolver mensajes actualizados para el chat
            $getMessages = app(\App\Application\Chat\GetMessagesBetweenUsers::class);
            $user = \App\Models\User::with('persona')->findOrFail($id);
            $mensajes = $getMessages->execute(Auth::id(), $id, 50);
            $mensajes = collect($mensajes)->map(function($m) use ($user) {
                $m = (object) $m;
                $m->createdAt_fmt = isset($m->createdAt) && $m->createdAt ? \Carbon\Carbon::parse($m->createdAt)->format('d/m/Y H:i') : '';
                if ($m->senderId != Auth::id()) {
                    $m->foto_perfil = optional($user->persona)->foto_perfil;
                } else {
                    $m->foto_perfil = optional(Auth::user()->persona)->foto_perfil;
                }
                return $m;
            });
            return response()->json([
                'user' => [ 'id' => $user->id, 'name' => $user->name ],
                'mensajes' => $mensajes,
                'authId' => Auth::id(),
            ]);
        }
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
