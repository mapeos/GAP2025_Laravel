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

        // Buscar por device_id primero
        $device = Device::where('device_id', $request->device_id)->first();
        if ($device) {
            // Si el device_id existe, reasignar user_id y actualizar fcm_token
            $device->user_id = $user->id;
            $device->fcm_token = $request->fcm_token;
            $device->device_name = $request->device_name ?? 'Web Browser';
            $device->device_os = $request->device_os ?? 'web';
            $device->app_version = $request->app_version ?? 'web-1.0';
            $device->save();
        } else {
            // Si no existe, crear nuevo registro
            Device::create([
                'user_id' => $user->id,
                'device_id' => $request->device_id,
                'fcm_token' => $request->fcm_token,
                'device_name' => $request->device_name ?? 'Web Browser',
                'device_os' => $request->device_os ?? 'web',
                'app_version' => $request->app_version ?? 'web-1.0',
            ]);
        }

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
            'data' => 'nullable|array',
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
                'data' => $request->input('data', [
                    'title' => $request->input('title'),
                    'body' => $request->input('body'),
                ]),
            ]
        ];

        $fcmResponse = Http::withToken($accessToken)
            ->post($fcmUrl, $fcmPayload);

        return response()->json($fcmResponse->json(), $fcmResponse->status());
    }

    /**
     * Enviar notificación webpush personalizada (WebPush API)
     */
    public function sendWebPush(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'icon' => 'nullable|string',
            'actions' => 'nullable|array',
            'data' => 'nullable|array',
        ]);

        $credentialsPath = env('FIREBASE_CREDENTIALS');
        $credentials = json_decode(file_get_contents($credentialsPath), true);

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

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        $accessToken = $tokenResponse->json('access_token');

        $projectId = $credentials['project_id'];
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $webpush = [
            'headers' => [
                'Urgency' => 'high',
            ],
            'notification' => [
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'icon' => $request->input('icon', null),
                'actions' => $request->input('actions', []),
            ],
        ];
        if ($request->has('data')) {
            $webpush['data'] = $request->input('data');
        }

        $fcmPayload = [
            'message' => [
                'token' => $request->input('token'),
                'webpush' => $webpush,
            ]
        ];

        $fcmResponse = Http::withToken($accessToken)
            ->post($fcmUrl, $fcmPayload);

        return response()->json($fcmResponse->json(), $fcmResponse->status());
    }
}
