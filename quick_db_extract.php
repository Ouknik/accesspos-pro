<?php

/**
 * سكريبت سريع لاستخراج جداول وأعمدة قاعدة البيانات
 * يمكن تشغيله مباشرة من سطر الأوامر: php quick_db_extract.php
 */

// تحديد مسار Laravel
define('LARAVEL_START', microtime(true));

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// بدء التطبيق
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "🔄 استخراج سريع لبنية قاعدة البيانات...\n";

try {
    // الحصول على الجداول
    $tables = DB::select("
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_TYPE = 'BASE TABLE' 
        ORDER BY TABLE_NAME
    ");

    $output = "=== استخراج سريع لبنية قاعدة البيانات ===\n";
    $output .= "التاريخ: " . date('Y-m-d H:i:s') . "\n";
    $output .= "عدد الجداول: " . count($tables) . "\n\n";

    foreach ($tables as $table) {
        $tableName = $table->TABLE_NAME;
        echo "📋 " . $tableName . "\n";
        
        $columns = DB::select("
            SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = ? 
            ORDER BY ORDINAL_POSITION
        ", [$tableName]);

        $output .= "جدول: {$tableName} ({columns} عمود)\n";
        $output = str_replace('{columns}', count($columns), $output);
        
        foreach ($columns as $column) {
            $output .= "  - {$column->COLUMN_NAME} ({$column->DATA_TYPE})\n";
        }
        $output .= "\n";
    }

    // حفظ الملف
    file_put_contents('public/db_quick.text', $output);
    
    echo "✅ تم حفظ النتائج في: public/db_quick.text\n";
    echo "📊 إجمالي الجداول: " . count($tables) . "\n";

} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

echo "✨ انتهى الاستخراج السريع!\n";
