<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OptimizeDatabasePerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:optimize-external 
                            {--test : Test external database connection}
                            {--clear-cache : Clear database query cache}
                            {--analyze : Analyze slow queries}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize external database performance and troubleshoot connection issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting External Database Optimization...');

        if ($this->option('test')) {
            $this->testConnections();
        }

        if ($this->option('clear-cache')) {
            $this->clearDatabaseCaches();
        }

        if ($this->option('analyze')) {
            $this->analyzePerformance();
        }

        if (!$this->option('test') && !$this->option('clear-cache') && !$this->option('analyze')) {
            $this->runFullOptimization();
        }

        $this->info('✅ Database optimization completed successfully!');
    }

    /**
     * Test database connections
     */
    private function testConnections()
    {
        $this->info('🔍 Testing database connections...');

        try {
            // Test default connection
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->info("✅ Default connection: {$connectionTime}ms");

            // Test query execution
            $startTime = microtime(true);
            $result = DB::select('SELECT 1 as test');
            $queryTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->info("✅ Query execution: {$queryTime}ms");

            if ($connectionTime > 1000) {
                $this->warn("⚠️  Slow connection detected ({$connectionTime}ms). Consider optimizing network or database server.");
            }

        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clear database-related caches
     */
    private function clearDatabaseCaches()
    {
        $this->info('🧹 Clearing database caches...');

        // Clear query cache
        DB::flushQueryLog();
        $this->info('✅ Query log cleared');

        // Clear application cache
        Cache::flush();
        $this->info('✅ Application cache cleared');

        // Clear compiled views
        $this->call('view:clear');
        $this->info('✅ View cache cleared');

        // Clear route cache
        $this->call('route:clear');
        $this->info('✅ Route cache cleared');

        // Clear config cache
        $this->call('config:clear');
        $this->info('✅ Config cache cleared');
    }

    /**
     * Analyze database performance
     */
    private function analyzePerformance()
    {
        $this->info('📊 Analyzing database performance...');

        try {
            // Enable query logging
            DB::enableQueryLog();

            // Run a sample query to test
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);

            DB::table('general_settings')->first();

            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);

            $executionTime = round(($endTime - $startTime) * 1000, 2);
            $memoryUsed = round(($endMemory - $startMemory) / 1024, 2);

            $this->info("Query execution time: {$executionTime}ms");
            $this->info("Memory used: {$memoryUsed}KB");

            // Get query log
            $queries = DB::getQueryLog();
            if (count($queries) > 0) {
                $this->info("Total queries executed: " . count($queries));
                
                foreach ($queries as $query) {
                    if ($query['time'] > 100) { // Queries slower than 100ms
                        $this->warn("Slow query detected: {$query['time']}ms - " . substr($query['query'], 0, 100));
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error("Performance analysis failed: " . $e->getMessage());
        }
    }

    /**
     * Run full optimization
     */
    private function runFullOptimization()
    {
        $this->info('⚡ Running full database optimization...');

        // Test connections first
        $this->testConnections();

        // Clear caches
        $this->clearDatabaseCaches();

        // Optimize database tables (MySQL specific)
        try {
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            
            $this->info("🔧 Optimizing database tables in: {$databaseName}");
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                try {
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $this->info("✅ Optimized: {$tableName}");
                } catch (\Exception $e) {
                    $this->warn("⚠️  Could not optimize {$tableName}: " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("Table optimization not available: " . $e->getMessage());
        }

        // Generate performance recommendations
        $this->generateRecommendations();
    }

    /**
     * Generate performance recommendations
     */
    private function generateRecommendations()
    {
        $this->info('💡 Performance Recommendations:');
        
        $recommendations = [
            '1. Use Redis cache for session storage: SESSION_DRIVER=redis',
            '2. Enable persistent database connections in config/database.php',
            '3. Use database connection pooling if available',
            '4. Consider using read replicas for heavy read operations',
            '5. Implement query result caching for frequently accessed data',
            '6. Use database indexing for commonly queried columns',
            '7. Monitor slow query logs regularly',
            '8. Consider using a CDN for static assets',
            '9. Implement database query optimization',
            '10. Use background jobs for heavy database operations'
        ];

        foreach ($recommendations as $recommendation) {
            $this->line("   {$recommendation}");
        }

        $this->info("\n📋 To apply optimizations automatically, add the following to your .env:");
        $this->line("   CACHE_STORE=redis");
        $this->line("   SESSION_DRIVER=redis");
        $this->line("   QUEUE_CONNECTION=redis");
        $this->line("   DB_CHARSET=utf8mb4");
        $this->line("   DB_COLLATION=utf8mb4_unicode_ci");
    }
}