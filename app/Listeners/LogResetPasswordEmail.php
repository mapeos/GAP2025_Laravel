<?php

namespace App\Listeners;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class LogResetPasswordEmail
{
    /**
     * Handle the event.
     *
     * @param  ResetPassword  $notification
     * @param  array  $data
     * @return void
     */
    public function handle($notification, $data)
    {
        $email = $data['notifiable']->email ?? null;
        $token = $notification->token;
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $email,
        ], false));
        Log::info('Correo de recuperación de contraseña generado', [
            'email' => $email,
            'token' => $token,
            'url' => $url,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            ResetPassword::class,
            [self::class, 'handle']
        );
    }
}
