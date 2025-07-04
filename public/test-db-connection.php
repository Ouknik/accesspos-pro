<?php
/**
 * اختبار سريع للتقارير المالية - استخدام .env فقط
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // تحميل Laravel bootstrap لقراءة .env
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // تحميل Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // قراءة إعدادات قاعدة البيانات من .env
    $dbConnection = config('database.default');
    $dbConfig = config("database.connections.{$dbConnection}");
    
    $pdo = null;
    $dbPath = null;
    
    // إنشاء الاتصال حسب نوع قاعدة البيانات
    if ($dbConnection === 'sqlite') {
        $dbPath = $dbConfig['database'];
        
        if (!file_exists($dbPath)) {
            throw new Exception("ملف قاعدة البيانات غير موجود: {$dbPath}");
        }
        
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='FACTURE_VNT'")->fetch();
        if (!$result) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
    } elseif ($dbConnection === 'sqlsrv') {
        // إعداد SQL Server
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات
        $result = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FACTURE_VNT'")->fetch();
        if (!$result) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
        
        $dbPath = "SQL Server: {$host}:{$port}/{$database}";
    } else {
        // دعم قواعد البيانات الأخرى (MySQL, PostgreSQL, etc.)
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        $dsn = "{$dbConnection}:host={$host};port={$port};dbname={$database}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات
        $result = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'FACTURE_VNT'")->fetch();
        if (!$result) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
        
        $dbPath = "{$dbConnection}: {$host}:{$port}/{$database}";
    }
    
    // اختبار بسيط للبيانات
    $testQuery = "SELECT COUNT(*) as count, SUM(fctv_mnt_ttc) as total FROM FACTURE_VNT LIMIT 1";
    $result = $pdo->query($testQuery)->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'database_connection' => $dbConnection,
        'database_path' => $dbPath,
        'test_result' => $result,
        'message' => 'قاعدة البيانات تعمل بشكل صحيح - تم قراءة الإعدادات من .env'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'suggestion' => 'تأكد من إعدادات قاعدة البيانات في ملف .env'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
