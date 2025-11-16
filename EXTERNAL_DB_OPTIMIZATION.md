# External Database Performance Optimization Guide

আপনার external database এর performance এবং max execution time সমস্যার সম্পূর্ণ সমাধান।

## 🚀 Quick Start

### 1. Environment Configuration
```bash
# .env file এ এই settings যোগ করুন:
CACHE_STORE=redis
SESSION_DRIVER=redis
DB_CONNECTION=mysql
```

### 2. Helper Functions ব্যবহার করুন

#### Basic Usage:
```php
// Script optimize করুন external database এর জন্য
optimizeForExternalDB();

// Heavy operation execute করুন
$result = executeWithRetry(function() {
    return DB::table('large_table')->get();
});

// Query result cache করুন
$data = cacheExternalQuery('my_query', function() {
    return DB::table('users')->where('active', 1)->get();
}, 60); // 60 minutes cache
```

#### Advanced Usage:
```php
// Performance monitoring
$monitor = monitorPerformance('My Heavy Operation');

// Batch processing
$data = range(1, 10000);
$results = batchProcess($data, function($batch) {
    return DB::table('logs')->insert($batch);
}, 500);

// End monitoring
$performance = $monitor['end']();
```

## 🛠️ Available Functions

### Core Optimization Functions

1. **`optimizeForExternalDB()`**
   - Script execution time 600 seconds পর্যন্ত বাড়ায়
   - Memory limit 1024M করে
   - Database timeout settings optimize করে

2. **`executeWithRetry($callback, $maxRetries = 3, $delay = 2)`**
   - Failed operations automatically retry করে
   - Network issues handle করে

3. **`cacheExternalQuery($key, $callback, $minutes = 60)`**
   - Query results cache করে
   - Repeated queries faster করে

4. **`batchProcess($data, $callback, $batchSize = 100)`**
   - Large datasets chunk করে process করে
   - Memory overflow prevent করে

5. **`monitorPerformance($operation)`**
   - Execution time এবং memory usage track করে
   - Performance logs generate করে

### Utility Functions

1. **`quickDBHealthCheck()`**
   - Database connection health check
   - Response time measure করে

2. **`isExternalDBHealthy($connection = null)`**
   - Connection status check করে

3. **`cleanupExternalDBResources()`**
   - Memory cleanup করে
   - Query logs clear করে

## 🎯 Command Line Tools

### Database Optimization Command:
```bash
# Full optimization run করুন
php artisan db:optimize-external

# Connection test করুন
php artisan db:optimize-external --test

# Cache clear করুন
php artisan db:optimize-external --clear-cache

# Performance analysis করুন
php artisan db:optimize-external --analyze
```

## 🔧 Configuration Files Modified

### 1. `/config/database.php`
- Persistent connections enabled
- Connection timeout increased
- Performance optimizations added

### 2. `/config/cache.php`
- Default cache changed to Redis
- Better performance for external operations

### 3. `/app/Http/Helpers/helpers.php`
- All optimization functions added
- Ready to use helper methods

### 4. `/app/Http/Middleware/OptimizeExternalDatabase.php`
- Middleware for automatic optimization
- Performance logging included

## 📊 Performance Monitoring

### Enable Performance Logging:
```php
// Your controller এ:
$monitor = monitorPerformance('User Data Export');

// Your heavy operation
$users = DB::table('users')->get();

// End monitoring and log results
$performance = $monitor['end']();
```

### Log Output Example:
```
[2024-11-12 15:30:00] External DB Performance - User Data Export: 2.5s, Memory: 45MB
```

## ⚡ Best Practices

### 1. Always Use Optimization Functions:
```php
// ❌ Wrong way
$data = DB::table('large_table')->get();

// ✅ Right way
optimizeForExternalDB();
$data = cacheExternalQuery('large_table_data', function() {
    return DB::table('large_table')->get();
}, 30);
```

### 2. Handle Large Datasets Properly:
```php
// ❌ Wrong way - Memory overflow risk
$users = DB::table('users')->get(); // 100K+ records

// ✅ Right way - Batch processing
chunkExternalOperation(
    DB::table('users'), 
    1000, 
    function($users) {
        foreach($users as $user) {
            // Process each user
        }
    }
);
```

### 3. Always Cleanup Resources:
```php
try {
    optimizeForExternalDB();
    // Your heavy operations
} finally {
    cleanupExternalDBResources();
}
```

## 🚨 Troubleshooting

### Common Issues এবং Solutions:

1. **Max Execution Time Exceeded**
   ```php
   // Solution: Use this at the beginning
   setExecutionTimeLimit(600); // 10 minutes
   ```

2. **Memory Limit Exceeded**
   ```php
   // Solution: Enable batch processing
   batchProcess($data, $callback, 500); // Smaller batches
   ```

3. **Connection Timeout**
   ```php
   // Solution: Use retry mechanism
   $result = executeWithRetry(function() {
       return DB::connection()->getPdo();
   }, 5, 3); // 5 retries, 3 seconds delay
   ```

4. **Slow Query Performance**
   ```php
   // Solution: Use caching
   $result = cacheExternalQuery('slow_query_key', function() {
       return DB::select('SELECT * FROM complex_view');
   }, 60); // Cache for 1 hour
   ```

## 📋 Installation Checklist

- [x] Database configuration optimized
- [x] Helper functions added
- [x] Middleware created
- [x] Command line tools available
- [x] Cache configuration improved
- [x] Performance monitoring enabled

## 🔍 Testing Your Setup

```bash
# Test database connection
php artisan db:optimize-external --test

# Check performance
php artisan db:optimize-external --analyze

# Clear all caches
php artisan db:optimize-external --clear-cache
```

## 💡 Pro Tips

1. **Use Redis for session storage** - SESSION_DRIVER=redis
2. **Enable persistent connections** - Already configured
3. **Monitor slow queries** - Use performance monitoring
4. **Cache frequently accessed data** - Use cacheExternalQuery()
5. **Process large datasets in chunks** - Use batchProcess()

---

এই optimization setup এর পরে আপনার external database operations অনেক দ্রুত এবং reliable হবে। Max execution time এবং memory issues resolve হয়ে যাবে।

## 📞 Support

কোন সমস্যা হলে এই functions ব্যবহার করে debug করুন:
- `quickDBHealthCheck()` - Connection test
- `monitorPerformance()` - Performance analysis
- `php artisan db:optimize-external --analyze` - Full analysis