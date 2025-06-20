<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAuthController extends Controller
{
    public function login(Request $request, FirebaseAuth $firebaseAuth)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->id_token);
            $firebaseUserId = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $firebaseAuth->getUser($firebaseUserId);
            $email = $firebaseUser->email;

            // Busca o crea el usuario local
            $user = User::firstOrCreate([
                'email' => $email,
            ], [
                'name' => $firebaseUser->displayName ?? $email,
                'password' => bcrypt(uniqid()), // Contraseña aleatoria
            ]);

            Auth::login($user, true);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }
    }
}
