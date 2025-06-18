<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tokens;
    protected $title;
    protected $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $tokens, string $title, string $body)
    {
        $this->tokens = $tokens;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('[FCM][Job] Iniciando envío de notificaciones', [
            'tokens' => $this->tokens,
            'title' => $this->title,
            'body' => $this->body,
        ]);
        $credentialsPath = env('FIREBASE_CREDENTIALS');
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        Log::info('[FCM][Job] Credenciales cargadas', [
            'project_id' => $credentials['project_id'] ?? null,
            'client_email' => $credentials['client_email'] ?? null,
        ]);
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
        $jwt = \Firebase\JWT\JWT::encode($payload, $privateKey, 'RS256');
        Log::info('[FCM][Job] JWT generado');
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        $accessToken = $tokenResponse->json('access_token');
        Log::info('[FCM][Job] Access token obtenido', [
            'accessToken' => $accessToken ? 'OK' : 'FALLO',
            'response' => $tokenResponse->json(),
        ]);
        $projectId = $credentials['project_id'];
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        foreach ($this->tokens as $token) {
            $fcmPayload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                ]
            ];
            Log::info('[FCM][Job] Enviando payload a FCM', [
                'token' => $token,
                'payload' => $fcmPayload,
            ]);
            $response = Http::withToken($accessToken)
                ->post($fcmUrl, $fcmPayload);
            if ($response->failed()) {
                Log::error('[FCM][Job] Error enviando notificación FCM', [
                    'token' => $token,
                    'response' => $response->body(),
                ]);
            } else {
                Log::info('[FCM][Job] Notificación FCM enviada', [
                    'token' => $token,
                    'response' => $response->body(),
                ]);
            }
        }
    }
}
