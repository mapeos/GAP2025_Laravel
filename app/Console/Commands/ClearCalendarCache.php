<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCalendarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:clear-cache {--user= : Clear cache for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear calendar cache for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');

        if ($userId) {
            // Limpiar cache específico de un usuario
            Cache::forget("eventos.user.{$userId}");
            $this->info("Calendar cache cleared for user ID: {$userId}");
        } else {
            // Limpiar todo el cache del calendario
            $this->clearAllCalendarCache();
            $this->info('All calendar cache cleared successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Clear all calendar related cache
     */
    private function clearAllCalendarCache()
    {
        // Cache de eventos
        Cache::forget('eventos.index');
        
        // Cache de usuarios
        Cache::forget('profesores.calendar');
        Cache::forget('alumnos.calendar');
        
        // Cache de tipos de evento
        Cache::forget('tipo_recordatorio');
        Cache::forget('tipos_evento.active');
        
        // Limpiar cache de eventos por usuario (buscar patrones)
        $keys = Cache::get('calendar_user_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('calendar_user_cache_keys');
    }
} 