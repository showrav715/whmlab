<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemPerformanceController extends Controller
{
    /**
     * Display system performance dashboard
     */
    public function dashboard()
    {
        $pageTitle = 'System Performance Monitor';
        
        // Get basic system info
        $systemInfo = $this->getSystemInfo();
        $dbHealth = $this->getDatabaseHealth();
        $performanceStats = $this->getPerformanceStats();
        
        return view('admin.system.performance', compact(
            'pageTitle', 
            'systemInfo', 
            'dbHealth', 
            'performanceStats'
        ));
    }
    
    /**
     * AJAX endpoint for real-time health check
     */
    public function healthCheck(Request $request)
    {
        try {
            $monitor = monitorPerformance('Admin Health Check');
            
            // Database health
            $dbHealth = quickDBHealthCheck();
            
            // System info
            $systemInfo = [
                'php_version' => phpversion(),
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'memory_limit' => ini_get('memory_limit'),
                'execution_time_limit' => ini_get('max_execution_time'),
                'disk_free_space' => disk_free_space('.'),
                'disk_total_space' => disk_total_space('.'),
            ];
            
            // Database stats
            $dbStats = [
                'users_count' => DB::table('users')->count(),
                'admins_count' => DB::table('admins')->count(),
                'total_tables' => $this->getTableCount(),
            ];
            
            // Performance test
            $performance = $monitor['end']();
            
            return response()->json([
                'success' => true,
                'database_health' => $dbHealth,
                'system_info' => $systemInfo,
                'database_stats' => $dbStats,
                'performance' => $performance,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'recommendations' => $this->getRecommendations($dbHealth, $systemInfo)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Health check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Health check failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test external database performance
     */
    public function testExternalDB(Request $request)
    {
        try {
            optimizeForExternalDB();
            
            $monitor = monitorPerformance('External DB Performance Test');
            
            // Test various operations
            $tests = [
                'connection' => $this->testConnection(),
                'simple_query' => $this->testSimpleQuery(),
                'complex_query' => $this->testComplexQuery(),
                'batch_operation' => $this->testBatchOperation(),
                'cache_operation' => $this->testCacheOperation(),
            ];
            
            $performance = $monitor['end']();
            
            cleanupExternalDBResources();
            
            return response()->json([
                'success' => true,
                'tests' => $tests,
                'overall_performance' => $performance,
                'recommendations' => $this->getPerformanceRecommendations($tests)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'External DB test failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all caches
     */
    public function clearCaches(Request $request)
    {
        try {
            // Clear application cache
            Cache::flush();
            
            // Clear query log
            DB::flushQueryLog();
            
            // Force garbage collection
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Cache clear failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Private helper methods
    
    private function getSystemInfo()
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'execution_time_limit' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'session_driver' => config('session.driver'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
        ];
    }
    
    private function getDatabaseHealth()
    {
        return quickDBHealthCheck();
    }
    
    private function getPerformanceStats()
    {
        $monitor = monitorPerformance('Performance Stats Collection');
        
        // Collect various stats
        $stats = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'uptime' => $this->getUptime(),
        ];
        
        $performance = $monitor['end']();
        $stats['collection_time'] = $performance;
        
        return $stats;
    }
    
    private function testConnection()
    {
        $startTime = microtime(true);
        try {
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => true,
                'time_ms' => $connectionTime,
                'status' => $connectionTime < 100 ? 'excellent' : ($connectionTime < 500 ? 'good' : 'slow')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function testSimpleQuery()
    {
        $startTime = microtime(true);
        try {
            DB::select('SELECT 1 as test');
            $queryTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => true,
                'time_ms' => $queryTime,
                'status' => $queryTime < 50 ? 'excellent' : ($queryTime < 200 ? 'good' : 'slow')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function testComplexQuery()
    {
        $startTime = microtime(true);
        try {
            DB::table('general_settings')->first();
            $queryTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => true,
                'time_ms' => $queryTime,
                'status' => $queryTime < 100 ? 'excellent' : ($queryTime < 500 ? 'good' : 'slow')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function testBatchOperation()
    {
        $startTime = microtime(true);
        try {
            // Test batch processing with dummy data
            $data = range(1, 100);
            batchProcess($data, function($batch) {
                // Simulate batch processing
                return count($batch);
            }, 25);
            
            $batchTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => true,
                'time_ms' => $batchTime,
                'status' => $batchTime < 200 ? 'excellent' : ($batchTime < 1000 ? 'good' : 'slow')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function testCacheOperation()
    {
        $startTime = microtime(true);
        try {
            $result = cacheExternalQuery('test_cache_key', function() {
                return ['test' => 'data', 'timestamp' => time()];
            }, 1);
            
            $cacheTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => true,
                'time_ms' => $cacheTime,
                'cached_data' => $result,
                'status' => $cacheTime < 50 ? 'excellent' : ($cacheTime < 200 ? 'good' : 'slow')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function getTableCount()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            return count($tables);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getUptime()
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Uptime"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getRecommendations($dbHealth, $systemInfo)
    {
        $recommendations = [];
        
        if (!$dbHealth['healthy']) {
            $recommendations[] = 'Database connection issue detected. Check your database credentials.';
        }
        
        if (isset($dbHealth['response_time_ms']) && $dbHealth['response_time_ms'] > 1000) {
            $recommendations[] = 'Database response time is slow. Consider optimizing queries or upgrading hosting.';
        }
        
        $memoryLimit = $this->parseBytes($systemInfo['memory_limit']);
        $memoryUsage = $systemInfo['memory_usage'];
        
        if ($memoryUsage > ($memoryLimit * 0.8)) {
            $recommendations[] = 'Memory usage is high. Consider increasing memory limit or optimizing code.';
        }
        
        if ($systemInfo['execution_time_limit'] < 300) {
            $recommendations[] = 'Execution time limit is low. Consider increasing for external database operations.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'All systems are running optimally!';
        }
        
        return $recommendations;
    }
    
    private function getPerformanceRecommendations($tests)
    {
        $recommendations = [];
        
        foreach ($tests as $testName => $result) {
            if (!$result['success']) {
                $recommendations[] = "Fix {$testName} issue: " . $result['error'];
            } elseif ($result['status'] === 'slow') {
                $recommendations[] = "Optimize {$testName} performance (current: {$result['time_ms']}ms)";
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'All performance tests passed successfully!';
        }
        
        return $recommendations;
    }
    
    private function parseBytes($size)
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int) $size;
        
        switch ($unit) {
            case 'G':
                return $value * 1024 * 1024 * 1024;
            case 'M':
                return $value * 1024 * 1024;
            case 'K':
                return $value * 1024;
            default:
                return $value;
        }
    }
}