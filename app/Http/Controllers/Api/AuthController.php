<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\CustomPasswordResetNotification;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Primer contacto: solo guardar FCM token, sin device_id ni usuario
     */
    public function registerDevice(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:1024',
            'device_name' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'extra_data' => 'nullable|array',
        ]);

        Log::info('[Device][FCM] Primer contacto: solo FCM token recibido', [
            'fcm_token' => $request->fcm_token,
            'device_name' => $request->device_name,
            'device_os' => $request->device_os,
            'app_version' => $request->app_version,
        ]);

        // Generar un device_id temporal único para cumplir la restricción de la base de datos
        $device_id = 'temp-' . \Illuminate\Support\Str::uuid();
        $device = new \App\Models\Device();
        $device->device_id = $device_id;
        $device->fcm_token = $request->fcm_token;
        $device->device_name = $request->device_name;
        $device->device_os = $request->device_os;
        $device->app_version = $request->app_version;
        $device->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Register a new user and optionally associate a device.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'pendiente',
        ]);

        // Crear la persona asociada al usuario
        $persona = \App\Models\Persona::create([
            'nombre' => $request->name,
            'apellido1' => '', // Puedes ajustar si recibes más datos
            'apellido2' => '',
            'dni' => null,
            'tfno' => '',
            'direccion_id' => null,
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        if ($request->has('device_id')) {
            $device = Device::where('device_id', $request->device_id)
                ->whereNull('user_id')
                ->first();

            if ($device) {
                $device->user_id = $user->id;
                $device->fcm_token = $request->fcm_token;
                $device->save();
                Log::info('[Device] Dispositivo asociado a usuario', [
                    'device_id' => $device->device_id,
                    'user_id' => $user->id
                ]);
            } else {
                $device = Device::updateOrCreate(
                    [
                        'user_id'   => $user->id,
                        'device_id' => $request->device_id,
                    ],
                    [
                        'fcm_token'   => $request->fcm_token,
                        'device_name' => $request->device_name ?? 'unknown',
                        'device_os'   => $request->device_os ?? 'unknown',
                        'app_version' => $request->app_version ?? '1.0',
                        'extra_data'  => $request->extra_data ?? [],
                    ]
                );
                Log::info('[Device] Dispositivo creado y asociado a usuario', [
                    'device_id' => $device->device_id,
                    'user_id' => $user->id
                ]);
            }
            return response()->json(['ok' => true, 'device_id' => $request->device_id]);
        }

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login: usuario y password, aquí se genera el device_id y se responde
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
            'password'   => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('[Device][LOGIN] Error de validación login', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::info('[Device][LOGIN] Credenciales incorrectas', ['email' => $request->email]);
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;
        $deviceId = 'web-' . Str::random(12);

        Log::info('[Device][LOGIN] Login exitoso, device_id generado', [
            'user_id' => $user->id,
            'device_id' => $deviceId
        ]);

        // Buscar el dispositivo por user_id y fcm_token (así detectamos si el usuario cambió de device_id o de móvil)
        $device = Device::where('user_id', $user->id)
            ->where('fcm_token', $request->fcm_token)
            ->first();

        if ($device) {
            // Si existe, actualizar el device_id y demás datos
            $device->device_id = $deviceId;
            $device->device_name = $request->device_name;
            $device->device_os = $request->device_os;
            $device->app_version = $request->app_version;
            $device->save();
        } else {
            // Si no existe, crear el registro
            $device = Device::create([
                'user_id' => $user->id,
                'device_id' => $deviceId,
                'fcm_token' => $request->fcm_token,
                'device_name' => $request->device_name,
                'device_os' => $request->device_os,
                'app_version' => $request->app_version,
            ]);
        }

        Log::info('[Device][LOGIN][DEBUG] Enviando device_id a la app móvil', [
            'user_id' => $user->id,
            'device_id' => $deviceId
        ]);
        return response()->json([
            'ok' => true,
            'device_id' => $deviceId,
            'token' => $token,
        ]);
    }

    /**
     * Relacionar FCM token y device_id con el usuario autenticado
     */
    public function storeDevice(Request $request)
    {
        Log::info('[Device][DEBUG][storeDevice] Request recibido', $request->all());
        $request->validate([
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'required|string|max:1024',
            'device_name' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'extra_data' => 'nullable|array',
        ]);

        $user = $request->user();
        $device = Device::updateOrCreate(
            [
                'user_id'   => $user->id,
                'device_id' => $request->device_id,
            ],
            [
                'fcm_token'   => $request->fcm_token,
                'device_name' => $request->device_name ?? 'unknown',
                'device_os'   => $request->device_os ?? 'unknown',
                'app_version' => $request->app_version ?? '1.0',
                'extra_data'  => $request->extra_data ?? [],
            ]
        );
        Log::info('[Device][RELACION] FCM token y device_id asociados a usuario', [
            'user_id' => $user->id,
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token
        ]);
        return response()->json(['ok' => true]);
    }

    /**
     * Logout user and revoke current token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        Log::info('[Device][LOGOUT] Usuario cerró sesión en la app móvil', [
            'user_id' => $user->id,
            'email' => $user->email,
            'device_id' => $request->header('Device-Id') ?? null,
            'ip' => $request->ip(),
        ]);
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    /**
     * Return authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Optional: Store/update device manually.
     */
    public function manualStoreDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'required|string|max:1024',
            'device_name' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'extra_data' => 'nullable|array',
        ]);

        $device = Device::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'device_id' => $request->device_id,
            ],
            [
                'fcm_token'   => $request->fcm_token,
                'device_name' => $request->device_name ?? 'unknown',
                'device_os'   => $request->device_os ?? 'unknown',
                'app_version' => $request->app_version ?? '1.0',
                'extra_data'  => $request->extra_data ?? [],
            ]
        );

        return response()->json($device, 201);
    }

    /**
     * Admin: List users pending validation.
     */
    public function pendingUsers()
    {
        $users = User::where('status', 'pendiente')->get();
        return response()->json($users);
    }

    /**
     * Admin: Validate and assign role to user.
     */
    public function validateAndAssignRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($userId);
        $user->status = 'activo';
        $user->save();
        $user->syncRoles([$request->role]);

        return response()->json([
            'message' => 'Usuario validado y rol asignado',
            'user' => $user
        ]);
    }

    /**
     * Send password reset notification to user.
     */
    public function sendPasswordResetEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.exists' => 'No existe una cuenta con este email.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->status !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta no está activa. Contacta al administrador.'
            ], 403);
        }

        // Generate a simple token for mobile app
        $token = Str::random(6); // 6-digit code for mobile

        // Store the token in cache for 15 minutes
        cache()->put("password_reset_{$user->email}", [
            'token' => $token,
            'user_id' => $user->id,
            'created_at' => now()
        ], now()->addMinutes(15));

        // Send notification
        $user->notify(new CustomPasswordResetNotification($token, $user->email));

        Log::info('Password reset email sent via AuthController', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token_length' => strlen($token)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Se ha enviado un código de recuperación a tu email.',
            'data' => [
                'email' => $user->email,
                'expires_in_minutes' => 15
            ]
        ]);
    }

    /**
     * Verify password reset token and reset password.
     */
    public function resetPasswordWithToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check token in cache
        $resetData = cache()->get("password_reset_{$request->email}");

        if (!$resetData || $resetData['token'] !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'El código de recuperación no es válido o ha expirado.'
            ], 400);
        }

        $user = User::find($resetData['user_id']);
        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all tokens for security
        $user->tokens()->delete();

        // Remove the reset token from cache
        cache()->forget("password_reset_{$request->email}");

        Log::info('Password reset completed via AuthController', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tu contraseña ha sido restablecida correctamente.',
            'data' => [
                'email' => $user->email,
                'reset_at' => now()->toISOString()
            ]
        ]);
    }
}
