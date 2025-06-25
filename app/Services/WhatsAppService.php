<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->client = new Client();
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
    }

    public function sendMessage($to, $message) {
        $url = "https://graph.facebook.com/v19.0/645712628633115/messages";
        $numbers = ['34684245005', '34687784254'];
        $results = [];
        foreach ($numbers as $number) {
            try {
                $response = $this->client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer EAAimfxJoF0UBO79QeiKgqzpTKabtyvdeUImQi10ztgOATR8KcJFyIS1NrZCEv23IOkljvsNr3Trn0oUSp0Y4RhmpEqSORPMV5KAjemsZCNGVxIkGjLF7Y4vXoI1mlB3rS3tkp17005NF8Y9bJWmVM1HXsYkCbDvu3iPyVwoLAUFAZCMeA9lPwkQ4cgwdKArCY2vFcuCLLL3HrreR0tprvjZCGp3XOoNVP7x7iRqX6IPSWLUCSFdLN1heNQUZD',
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => [
                        'messaging_product' => 'whatsapp',
                        'to' => $number,
                        'type' => 'template',
                        'template' => [ 
                            "name"=> "hello_world", 
                            "language"=> [ "code"=> "en_US" ]
                        ]
                    ]
                ]);
                Log::info('WhatsApp plantilla enviado', ['to' => $number, 'response' => (string) $response->getBody()]);
                $results[$number] = json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                Log::error('Error WhatsApp', ['to' => $number, 'error' => $e->getMessage()]);
                $results[$number] = ['error' => $e->getMessage()];
            }
        }
        return $results;
    }

}
