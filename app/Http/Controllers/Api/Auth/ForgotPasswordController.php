<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\CustomPasswordResetNotification;

class ForgotPasswordController extends Controller
{
    // Enviar email de recuperación
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.exists' => 'No existe una cuenta con este email.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida en solicitud de recuperación', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Solicitud de recuperación de contraseña recibida', ['email' => $request->email]);

        // Verificar que el usuario esté activo
        $user = User::where('email', $request->email)->first();
        if ($user && $user->status !== 'activo') {
            Log::warning('Intento de recuperación para usuario inactivo', [
                'email' => $request->email,
                'status' => $user->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta no está activa. Contacta al administrador.'
            ], 403);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        Log::info('Resultado del envío de email de recuperación', [
            'email' => $request->email,
            'status' => $status,
            'user_id' => $user->id ?? null
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un email con las instrucciones para recuperar tu contraseña.',
                'data' => [
                    'email' => $request->email,
                    'sent_at' => now()->toISOString()
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar el email de recuperación. Inténtalo de nuevo.',
                'error' => __($status)
            ], 400);
        }
    }

    // Resetear la contraseña
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ], [
            'token.required' => 'El token de recuperación es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.exists' => 'No existe una cuenta con este email.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida en reseteo de contraseña', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Intentando resetear contraseña', ['email' => $request->email]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                // Revocar todos los tokens existentes por seguridad
                $user->tokens()->delete();

                Log::info('Contraseña reseteada correctamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'tokens_revoked' => true
                ]);
            }
        );

        Log::info('Resultado del reseteo de contraseña', [
            'email' => $request->email,
            'status' => $status
        ]);

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Tu contraseña ha sido restablecida correctamente. Puedes iniciar sesión con tu nueva contraseña.',
                'data' => [
                    'email' => $request->email,
                    'reset_at' => now()->toISOString()
                ]
            ], 200);
        } else {
            $errorMessage = match($status) {
                Password::INVALID_TOKEN => 'El token de recuperación no es válido o ha expirado.',
                Password::INVALID_USER => 'No se encontró un usuario con este email.',
                default => 'No se pudo restablecer la contraseña. Inténtalo de nuevo.'
            };

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => __($status)
            ], 400);
        }
    }
}
