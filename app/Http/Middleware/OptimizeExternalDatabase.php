<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OptimizeExternalDatabase
{
    /**
     * Handle an incoming request for external database operations.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Optimize script for external database operations
        $this->optimizeScript();
        
        // Start performance monitoring
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Execute the request
        $response = $next($request);
        
        // Log performance if enabled
        if (config('app.debug')) {
            $this->logPerformance($startTime, $startMemory, $request);
        }
        
        // Cleanup resources
        $this->cleanup();
        
        return $response;
    }
    
    /**
     * Optimize script settings for external database operations
     */
    private function optimizeScript(): void
    {
        // Increase execution time limit
        if (function_exists('set_time_limit') && !ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
            @set_time_limit(600); // 10 minutes
        }
        
        // Increase memory limit
        @ini_set('memory_limit', '1024M');
        
        // Optimize database connection settings
        @ini_set('default_socket_timeout', 60);
        @ini_set('mysql.connect_timeout', 60);
        @ini_set('mysql.timeout', 60);
        
        // Optimize session handling for long operations
        @ini_set('session.gc_maxlifetime', 3600); // 1 hour
        
        // Disable output buffering for real-time feedback
        if (ob_get_level()) {
            @ob_end_flush();
        }
    }
    
    /**
     * Log performance metrics
     */
    private function logPerformance(float $startTime, int $startMemory, Request $request): void
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = round($endTime - $startTime, 4);
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        
        $route = $request->route() ? $request->route()->getName() : $request->path();
        
        Log::info("External DB Performance", [
            'route' => $route,
            'execution_time' => $executionTime . 's',
            'memory_used' => $memoryUsed . 'MB',
            'peak_memory' => $peakMemory . 'MB',
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
        ]);
    }
    
    /**
     * Cleanup resources after external database operations
     */
    private function cleanup(): void
    {
        // Clear Laravel query log to free memory
        DB::flushQueryLog();
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        // Clear any temporary caches
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    }
}