<?php

namespace CleverTIC\AppointmentSuggester;

use Illuminate\Support\ServiceProvider;
use CleverTIC\AppointmentSuggester\Contracts\AppointmentSuggesterInterface;

class AppointmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('appointments.suggester.rule', function ($app) {
            return new AppointmentSuggestionService();
        });
        
        $this->app->bind('appointments.suggester.ai', function ($app) {
            return new AiAppointmentSuggestionService();
        });
    }
}
