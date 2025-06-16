<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Persona;

class UserController extends Controller
{
    // Listar usuarios (incluidos los eliminados), ordenados por más recientes y filtrados por estado si se solicita
    public function index(Request $request)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $query = User::with(['roles', 'creator', 'updater', 'deleter'])
            ->withTrashed()
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(10);
        return view('admin.users.index', compact('users', 'roles'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'created_by' => Auth::id(),
            'status' => 'pendiente', // El usuario web también queda pendiente
            // Guarda automáticamente el user-agent (navegador, app, etc.) para análisis de procedencia
            'user_agent' => $request->userAgent(),
        ]);

        // No asignar rol por defecto, el admin lo hará manualmente
        // $user->syncRoles($validated['roles']); // El admin asignará el rol después

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    // Ver detalle del usuario
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }


    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->updated_by = Auth::id();
        $user->save();

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario (SoftDelete)
    public function destroy(User $user)
    {
        $user->deleted_by = Auth::id();
        $user->save();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    // Restaurar usuario eliminado
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')->with('success', 'Usuario restaurado correctamente.');
    }

    public function pendent()
    {
        $users = User::where('status', 'pendiente')->get();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.users.pendent', compact('users', 'roles'));
    }

    public function validateBulk(Request $request)
    {
        // Si viene user_id y role, es validación individual
        if ($request->has(['user_id', 'role'])) {
            $user = User::find($request->input('user_id'));
            if ($user && $user->status === 'pendiente') {
                $user->status = 'activo';
                $user->save();
                $user->syncRoles([$request->input('role')]);
                return redirect()->route('admin.users.pendent')->with('success', 'Usuario validado correctamente.');
            }
            return redirect()->route('admin.users.pendent')->with('error', 'No se pudo validar el usuario.');
        }
        // Si viene roles[] es validación masiva
        $request->validate([
            'roles' => 'required|array',
        ]);

        foreach ($request->input('roles') as $userId => $role) {
            $user = User::find($userId);
            if ($user && $user->status === 'pendiente') {
                // Cambiar estado y asignar rol
                $user->status = 'activo';
                $user->save();
                $user->syncRoles([$role]);

                // Crear una entrada en la tabla personas si no existe
                if (!$user->persona) {
                    Persona::create([
                        'nombre' => $user->name, // Usa el nombre del usuario
                        'apellido1' => '', // Ajusta según tus necesidades
                        'apellido2' => '',
                        'dni' => '', // Ajusta según tus necesidades
                        'tfno' => '',
                        'direccion_id' => null,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }

        return redirect()->route('admin.users.pendent')->with('success', 'Usuarios validados correctamente.');
    }

    // Cambiar estado de usuario (AJAX)
    public function toggleStatus($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            return response()->json(['error' => 'No se puede cambiar el estado de un usuario eliminado.'], 400);
        }
        $user->status = $user->status === 'activo' ? 'pendiente' : 'activo';
        $user->save();
        return response()->json(['status' => $user->status]);
    }

    // Cambiar rol de usuario (AJAX)
    public function changeRole(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            return response()->json(['error' => 'No se puede cambiar el rol de un usuario eliminado.'], 400);
        }
        $role = $request->input('role');
        if (!$role) {
            return response()->json(['error' => 'Rol no especificado.'], 400);
        }
        $user->syncRoles([$role]);
        return response()->json(['success' => true]);
    }

    public function homePendiente()
    {
        return view('pendientes.home');
    }

    public function getPersonaByUser($userId)
    {
        $user = User::with('persona')->findOrFail($userId); // Carga el usuario con su relación 'persona'
        return response()->json([
            'user' => $user,
            'persona' => $user->persona, // Devuelve los datos de la persona asociada
        ]);
    }
}
