<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeCalendarQueries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo aplicar optimizaciones en rutas del calendario
        if ($this->isCalendarRoute($request)) {
            // Habilitar query logging para debugging
            if (config('app.debug')) {
                DB::enableQueryLog();
            }

            // Optimizar consultas de base de datos
            $this->optimizeDatabaseQueries();

            $response = $next($request);

            // Log de consultas si está en debug
            if (config('app.debug')) {
                $this->logQueryPerformance();
            }

            return $response;
        }

        return $next($request);
    }

    /**
     * Verificar si la ruta es del calendario
     */
    private function isCalendarRoute(Request $request): bool
    {
        $calendarRoutes = [
            'events.calendar',
            'events.json',
            'admin.events.*',
            'calendario',
            'eventos.json'
        ];

        $routeName = $request->route()?->getName();
        
        foreach ($calendarRoutes as $pattern) {
            if (str_contains($pattern, '*')) {
                $basePattern = str_replace('.*', '', $pattern);
                if (str_starts_with($routeName, $basePattern)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }

    /**
     * Optimizar consultas de base de datos
     */
    private function optimizeDatabaseQueries(): void
    {
        // Configurar timeouts más largos para consultas complejas
        DB::statement('SET SESSION sql_mode = ""');
        
        // Optimizar configuración de MySQL para consultas de calendario
        if (config('database.default') === 'mysql') {
            DB::statement('SET SESSION innodb_lock_wait_timeout = 50');
            DB::statement('SET SESSION lock_wait_timeout = 50');
        }
    }

    /**
     * Log del rendimiento de consultas
     */
    private function logQueryPerformance(): void
    {
        $queries = DB::getQueryLog();
        $totalTime = 0;
        $slowQueries = [];

        foreach ($queries as $query) {
            $totalTime += $query['time'];
            
            // Identificar consultas lentas (> 100ms)
            if ($query['time'] > 100) {
                $slowQueries[] = [
                    'sql' => $query['query'],
                    'time' => $query['time'],
                    'bindings' => $query['bindings']
                ];
            }
        }

        // Log de estadísticas
        Log::info('Calendar Query Performance', [
            'total_queries' => count($queries),
            'total_time' => $totalTime,
            'average_time' => count($queries) > 0 ? $totalTime / count($queries) : 0,
            'slow_queries_count' => count($slowQueries)
        ]);

        // Log de consultas lentas
        if (!empty($slowQueries)) {
            Log::warning('Slow Calendar Queries Detected', [
                'slow_queries' => $slowQueries
            ]);
        }

        // Limpiar query log
        DB::disableQueryLog();
    }
} 