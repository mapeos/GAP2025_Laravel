<?php

namespace App\Providers;

use CleverTIC\AppointmentSuggester\AiAppointmentSuggestionService;
use CleverTIC\AppointmentSuggester\AppointmentSuggesterFacade;
use CleverTIC\AppointmentSuggester\AppointmentSuggestionService;
use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
        //
    }
}
