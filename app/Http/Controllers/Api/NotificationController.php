<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    // Save or update the FCM token for the authenticated user
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'device_os' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $user = $request->user();

        Device::updateOrCreate(
            ['device_id' => $request->device_id, 'user_id' => $user->id],
            [
                'fcm_token' => $request->fcm_token,
                'device_name' => $request->device_name ?? 'Web Browser',
                'device_os' => $request->device_os ?? 'web',
                'app_version' => $request->app_version ?? 'web-1.0',
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Enviar notificación push usando FCM HTTP v1 y Service Account JSON
     */
    public function sendFcmV1(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $credentialsPath = env('FIREBASE_CREDENTIALS');
        $credentials = json_decode(file_get_contents($credentialsPath), true);

        // 1. Crear JWT para obtener access_token
        $now = time();
        $payload = [
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
        ];
        $privateKey = $credentials['private_key'];
        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        // 2. Solicitar access_token
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        $accessToken = $tokenResponse->json('access_token');

        // 3. Enviar notificación
        $projectId = $credentials['project_id'];
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $fcmPayload = [
            'message' => [
                'token' => $request->input('token'),
                'notification' => [
                    'title' => $request->input('title'),
                    'body' => $request->input('body'),
                ],
                // Puedes agregar 'data' => [...] si quieres datos personalizados
            ]
        ];

        $fcmResponse = Http::withToken($accessToken)
            ->post($fcmUrl, $fcmPayload);

        return response()->json($fcmResponse->json(), $fcmResponse->status());
    }
}
