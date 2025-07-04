<?php
/**
 * فحص سريع لإعدادات قاعدة البيانات في .env
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
    
    echo json_encode([
        'status' => 'success',
        'database_connection' => $dbConnection,
        'database_config' => $dbConfig,
        'env_values' => [
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
