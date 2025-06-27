<?php

namespace App\Providers;

use CleverTIC\AppointmentSuggester\AiAppointmentSuggestionService;
use CleverTIC\AppointmentSuggester\AppointmentSuggesterFacade;
use CleverTIC\AppointmentSuggester\AppointmentSuggestionService;
use CleverTIC\AppointmentSuggester\AppointmentServiceProvider as AppointmentSuggesterServiceProvider;
use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar servicios de sugerencias de citas
        $this->app->singleton(AppointmentSuggesterInterface::class, function ($app) {
            return new AppointmentSuggesterFacade(
                new AppointmentSuggestionService(),
                new AiAppointmentSuggestionService()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(config('app.frontend_url') . "/reset-password?token=$token&email=" . urlencode($notifiable->getEmailForPasswordReset()));
            Log::info('Correo de recuperación de contraseña generado', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'token' => $token,
                'url' => $url,
            ]);
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Recuperación de contraseña')
                ->line('Has solicitado restablecer tu contraseña.')
                ->action('Restablecer contraseña', $url)
                ->line('Si no solicitaste este cambio, ignora este correo.');
        });

        // Forzar paginación Bootstrap en toda la app
        Paginator::useBootstrap();
    }
}
