<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Device;

class AuthController extends Controller
{
    // Registro de usuario móvil
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pendiente', // Nuevo campo para estado de validación
        ]);
        // No asignar rol por defecto, el admin lo hará manualmente

        $token = $user->createToken('mobile')->plainTextToken;

        // Guardar datos del dispositivo si vienen
        if ($request->has('device_id')) {
            Device::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                ],
                [
                    'device_name' => $request->device_name,
                    'device_os' => $request->device_os,
                    'device_token' => $request->device_token,
                    'app_version' => $request->app_version,
                    'extra_data' => $request->extra_data ?? null,
                ]
            );
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login de usuario móvil
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        // Guardar datos del dispositivo si vienen
        if ($request->has('device_id')) {
            Device::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                ],
                [
                    'device_name' => $request->device_name,
                    'device_os' => $request->device_os,
                    'device_token' => $request->device_token,
                    'app_version' => $request->app_version,
                    'extra_data' => $request->extra_data ?? null,
                ]
            );
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    // Obtener información del usuario autenticado
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function storeDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'device_os' => 'nullable|string',
            'device_token' => 'nullable|string',
            'app_version' => 'nullable|string',
            'extra_data' => 'nullable|array',
        ]);

        $device = Device::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'device_id' => $request->device_id,
            ],
            [
                'device_name' => $request->device_name,
                'device_os' => $request->device_os,
                'device_token' => $request->device_token,
                'app_version' => $request->app_version,
                'extra_data' => $request->extra_data ?? null,
            ]
        );

        return response()->json($device, 201);
    }

    // Listar usuarios pendientes de validar (solo admin)
    public function pendingUsers()
    {
        // La protección de acceso debe hacerse en la ruta con middleware 'role:Administrador'
        $users = User::where('status', 'pendiente')->get();
        return response()->json($users);
    }

    // Asignar rol y activar usuario (solo admin)
    public function validateAndAssignRole(Request $request, $userId)
    {
        // La protección de acceso debe hacerse en la ruta con middleware 'role:Administrador'
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);
        $user = User::findOrFail($userId);
        $user->status = 'activo';
        $user->save();
        $user->syncRoles([$request->role]);
        return response()->json(['message' => 'Usuario validado y rol asignado', 'user' => $user]);
    }
}
