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
    /**
     * Register device without user association.
     */
    public function registerDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'nullable|string|max:1024',
            'device_name' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'extra_data' => 'nullable|array',
        ]);

        $device = Device::where('device_id', $request->device_id)
            ->whereNull('user_id')
            ->first();

        if (!$device) {
            $device = Device::create([
                'device_id'    => $request->device_id,
                'fcm_token'    => $request->fcm_token,
                'device_name'  => $request->device_name,
                'device_os'    => $request->device_os,
                'app_version'  => $request->app_version,
                'extra_data'   => $request->extra_data ?? [],
                'first_seen_at' => now(),
                'user_id'      => null,
            ]);
        } else {
            $device->update([
                'fcm_token'   => $request->fcm_token,
                'device_name' => $request->device_name,
                'device_os'   => $request->device_os,
                'app_version' => $request->app_version,
                'extra_data'  => $request->extra_data ?? [],
            ]);
        }

        return response()->json($device, 201);
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

        $token = $user->createToken('mobile')->plainTextToken;

        if ($request->has('device_id')) {
            $device = Device::where('device_id', $request->device_id)
                ->whereNull('user_id')
                ->first();

            if ($device) {
                $device->user_id = $user->id;
                $device->first_token = $token;
                $device->fcm_token = $request->fcm_token;
                $device->save();
            } else {
                Device::updateOrCreate(
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
                        'first_token'=> $token,
                    ]
                );
            }
        }

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login a user and register/update device.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
            'password'   => 'required',
            'device_id'  => 'nullable|string|max:255',
            'fcm_token'  => 'nullable|string|max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        if ($request->filled('device_id')) {
            Device::updateOrCreate(
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
        }

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user and revoke current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
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
    public function storeDevice(Request $request)
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
}
