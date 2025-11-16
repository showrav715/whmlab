# cPanel Deployment Guide - External Database Optimization

🚀 **cPanel hosting এ external database performance optimization এর সম্পূর্ণ setup guide।**

## 📁 File Structure for cPanel

```
public_html/
├── .htaccess (performance optimization)
├── index.php (Laravel entry point)
├── assets/ (your assets)
└── core/ (Laravel application - outside public_html for security)
    ├── app/
    ├── config/
    ├── .env
    └── ...
```

## 🛠️ Step 1: Upload Files

### 1. Upload Laravel files:
- **`core/`** folder টি public_html এর বাইরে upload করুন
- **`public/`** folder এর contents public_html এ copy করুন
- **.htaccess** file public_html এ place করুন

### 2. Update index.php path:
```php
// public_html/index.php এ path update করুন:
require __DIR__.'/../core/vendor/autoload.php';
$app = require_once __DIR__.'/../core/bootstrap/app.php';
```

## ⚙️ Step 2: Environment Configuration

### 1. .env file setup:
```bash
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost  # Usually localhost in cPanel
DB_PORT=3306
DB_DATABASE=cpanel_username_dbname
DB_USERNAME=cpanel_username_dbuser
DB_PASSWORD=your_db_password

# Cache Configuration (File-based for cPanel)
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# App Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Performance Settings
LOG_CHANNEL=single
LOG_LEVEL=error
```

### 2. Database সাধারণত এভাবে থাকে cPanel এ:
- **Database Name:** `cpanel_username_dbname`
- **Database User:** `cpanel_username_dbuser`  
- **Host:** `localhost`

## 🔧 Step 3: Performance Optimization

### 1. .htaccess Optimization:
```apache
# public_html/.htaccess file এ যোগ করুন:

# PHP Performance Settings
<IfModule mod_php.c>
    php_value max_execution_time 600
    php_value memory_limit 1024M
    php_value default_socket_timeout 60
    php_value mysql.connect_timeout 60
    php_value mysql.timeout 60
    php_value session.gc_maxlifetime 3600
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
</IfModule>

# Enable Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

### 2. Storage Directories:
```bash
# cPanel file manager এ এই folders এ write permission দিন:
core/storage/app/
core/storage/framework/cache/
core/storage/framework/sessions/
core/storage/framework/views/
core/storage/logs/
```

## 📊 Step 4: Database Setup

### 1. cPanel Database Creation:
- MySQL Databases section এ যান
- Database তৈরি করুন: `cpanel_username_dbname`
- Database user তৈরি করুন: `cpanel_username_dbuser`
- User কে database এর সব permission দিন

### 2. Migration Run করুন:
```bash
# Terminal access থাকলে:
cd /path/to/core
php artisan migrate

# অথবা web-based migration tool ব্যবহার করুন
```

## 🚀 Step 5: Optimization Functions Usage

### Basic Usage আপনার controller এ:
```php
<?php

class YourController extends Controller 
{
    public function heavyDatabaseOperation() 
    {
        // cPanel optimized external DB operation
        optimizeForExternalDB();
        
        try {
            // Cache করা query
            $data = cacheExternalQuery('heavy_query_key', function() {
                return DB::table('large_table')
                    ->where('status', 'active')
                    ->get();
            }, 60); // 60 minutes cache
            
            // Batch processing
            $largeDataset = collect(range(1, 10000));
            $results = batchProcess($largeDataset->toArray(), function($batch) {
                return DB::table('processing_table')->insert($batch);
            }, 500);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'processed' => count($results)
            ]);
            
        } catch (Exception $e) {
            Log::error('Heavy operation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Operation failed'
            ], 500);
        } finally {
            // Always cleanup
            cleanupExternalDBResources();
        }
    }
    
    public function healthCheck() 
    {
        $health = quickDBHealthCheck();
        return response()->json($health);
    }
}
```

## 🔍 Step 6: Testing & Debugging

### 1. Connection Test:
```php
// আপনার route এ test endpoint তৈরি করুন:
Route::get('/db-test', function() {
    try {
        $health = quickDBHealthCheck();
        return response()->json($health);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});
```

### 2. Performance Monitor:
```php
// Performance tracking
Route::get('/performance-test', function() {
    $monitor = monitorPerformance('cPanel DB Test');
    
    // Your heavy operation
    $data = DB::table('users')->limit(1000)->get();
    
    $performance = $monitor['end']();
    
    return response()->json([
        'data_count' => $data->count(),
        'performance' => $performance
    ]);
});
```

## 🚨 Common cPanel Issues & Solutions

### 1. **"Max execution time exceeded"**
```php
// Solution: .htaccess এ যোগ করুন:
php_value max_execution_time 600

// অথবা code এ:
optimizeForExternalDB(); // Helper function use করুন
```

### 2. **"Memory limit exceeded"**
```php
// .htaccess এ:
php_value memory_limit 1024M

// Code এ:
batchProcess($largeData, $callback, 100); // Smaller batches
```

### 3. **"Cannot write to storage directory"**
```bash
# File Manager এ এই folders এর permission 755 বা 775 করুন:
core/storage/
core/storage/app/
core/storage/framework/
core/storage/logs/
```

### 4. **"Database connection failed"**
```php
// .env file এ correct database info দিন:
DB_HOST=localhost  # Not 127.0.0.1
DB_DATABASE=cpanel_username_dbname
DB_USERNAME=cpanel_username_dbuser
```

### 5. **"500 Internal Server Error"**
```bash
# Check error logs in cPanel:
# Error Logs > Main Domain > View latest logs

# Common fixes:
# 1. Check file permissions
# 2. Check .htaccess syntax
# 3. Check storage directory permissions
# 4. Check .env file format
```

## 📋 cPanel Deployment Checklist

- [x] Laravel files uploaded to correct directories
- [x] Database created and configured in cPanel  
- [x] .env file configured with correct database credentials
- [x] .htaccess file placed with performance optimizations
- [x] Storage directories have write permissions (755/775)
- [x] index.php path updated for cPanel structure
- [x] Cache configured to use file storage
- [x] Performance optimization functions working
- [x] Database connection tested
- [x] Error logging configured

## 🔧 Performance Monitoring Commands

যেহেতু cPanel এ terminal access সীমিত, web-based monitoring করুন:

### Create a admin route for monitoring:
```php
// routes/admin.php এ যোগ করুন:
Route::get('/system/db-health', function() {
    if (!auth()->guard('admin')->check()) {
        abort(403);
    }
    
    $health = quickDBHealthCheck();
    $monitor = monitorPerformance('Admin Health Check');
    
    // Sample operations
    $userCount = DB::table('users')->count();
    $adminCount = DB::table('admins')->count();
    
    $performance = $monitor['end']();
    
    return response()->json([
        'database_health' => $health,
        'performance' => $performance,
        'stats' => [
            'users' => $userCount,
            'admins' => $adminCount
        ],
        'timestamp' => now()
    ]);
})->name('admin.system.db-health');
```

## 💡 cPanel Pro Tips

1. **File Permissions:** 
   - Files: 644
   - Directories: 755
   - Storage directories: 775

2. **Database Naming:**
   - Always prefix with your cPanel username
   - Example: `username_whmlab_db`

3. **Error Logging:**
   - Enable error logs in cPanel
   - Check logs regularly for issues

4. **Backup Strategy:**
   - Use cPanel backup tools
   - Regular database exports

5. **Performance:**
   - Use file cache instead of Redis
   - Enable compression in .htaccess
   - Monitor resource usage

---

🎉 **Setup Complete!** আপনার cPanel hosting এ external database operations এখন optimized এবং fast হবে।

**Test URL:** `https://yourdomain.com/admin/system/performance` (admin login required)

## 🎮 Admin Dashboard Features

### Performance Monitor Dashboard:
- **Real-time health monitoring** - Database response time, memory usage, execution limits
- **Performance testing** - Test external database operations with detailed metrics
- **Cache management** - Clear all caches with one click
- **Recommendations** - Automatic performance recommendations
- **System information** - Complete overview of your cPanel environment

### Navigation:
Admin Panel → Extra → Performance Monitor

### Keyboard Shortcuts:
- `Ctrl + R` - Refresh health check
- `Ctrl + T` - Run performance tests  
- `Ctrl + C` - Clear all caches

### Dashboard Features:
✅ Real-time database health monitoring  
✅ Memory usage tracking  
✅ Performance test suite  
✅ Automatic recommendations  
✅ cPanel-specific optimizations  
✅ Cache management tools  
✅ System configuration overview