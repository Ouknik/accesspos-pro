<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 فحص أعمدة جدول CAISSE...\n";
echo "==============================\n";

try {
    // فحص بنية الجدول
    $caisses = DB::table('CAISSE')->limit(5)->get();
    
    if ($caisses->count() > 0) {
        echo "✅ الجدول موجود، العينة الأولى:\n";
        foreach ($caisses as $index => $caisse) {
            echo "📋 Caisse " . ($index + 1) . ":\n";
            foreach ($caisse as $key => $value) {
                echo "  $key: $value\n";
            }
            echo "\n";
            break; // عرض أول عنصر فقط
        }
    } else {
        echo "❌ الجدول فارغ\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

// فحص أعمدة أخرى محتملة
echo "\n🔍 فحص الأعمدة الصحيحة...\n";
echo "==============================\n";

try {
    $result = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'CAISSE'");
    
    echo "📋 أعمدة جدول CAISSE:\n";
    foreach ($result as $column) {
        echo "  - " . $column->COLUMN_NAME . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطأ في فحص الأعمدة: " . $e->getMessage() . "\n";
}
