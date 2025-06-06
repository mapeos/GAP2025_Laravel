<?php
// Este helper clasifica los user-agents en 'Web', 'API' u 'Otro'
namespace App\Helpers;

class UserAgentClassifier
{
    public static function classify($userAgent)
    {
        if (!$userAgent) return 'Desconocido';
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'mozilla') || str_contains($ua, 'chrome') || str_contains($ua, 'firefox') || str_contains($ua, 'safari')) {
            return 'Web';
        }
        if (str_contains($ua, 'postman') || str_contains($ua, 'okhttp') || str_contains($ua, 'curl') || str_contains($ua, 'insomnia')) {
            return 'API';
        }
        return 'Otro';
    }
}
