<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $email;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->getResetUrl();
        
        return (new MailMessage)
            ->subject('Recuperación de Contraseña - ' . config('app.name'))
            ->greeting('¡Hola!')
            ->line('Has recibido este email porque hemos recibido una solicitud de recuperación de contraseña para tu cuenta.')
            ->line('Tu código de recuperación es: **' . $this->token . '**')
            ->action('Restablecer Contraseña', $resetUrl)
            ->line('Este enlace de recuperación expirará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
            ->line('Si no solicitaste la recuperación de contraseña, no es necesario que hagas nada.')
            ->line('Por seguridad, no compartas este código con nadie.')
            ->salutation('Saludos cordiales,<br>El equipo de ' . config('app.name'));
    }

    /**
     * Get the password reset URL.
     */
    protected function getResetUrl(): string
    {
        // For mobile app, you might want to use a deep link or custom scheme
        // For web, use the standard Laravel reset route
        
        // Option 1: Web URL (default)
        $webUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));
        
        // Option 2: Mobile deep link (uncomment if you have a mobile app)
        // $mobileUrl = "gapapp://reset-password?token={$this->token}&email=" . urlencode($this->email);
        
        return $webUrl;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(config('auth.passwords.'.config('auth.defaults.passwords').'.expire')),
        ];
    }
}
