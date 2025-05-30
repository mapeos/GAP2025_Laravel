<?php

namespace CleverTIC\AppointmentSuggester\Contracts;

use CleverTIC\AppointmentSuggester\DTO\SuggestionRequest;
use Illuminate\Support\Collection;

interface AppointmentSuggesterInterface
{
    public function suggest(SuggestionRequest $request): Collection;
}
