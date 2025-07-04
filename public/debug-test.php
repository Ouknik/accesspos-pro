<?php
// استيراد المكتبات في الأعلى
use Illuminate\Support\Facades\DB;

echo "بدء الاختبار...\n";

try {
    // تحميل Laravel
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "تم تحميل autoload\n";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "تم تحميل Laravel app\n";
    
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "تم تهيئة Laravel\n";
    
    // اختبار قاعدة البيانات
    $count = DB::table('FACTURE_VNT')->count();
    echo "عدد الفواتير: $count\n";
    
    echo "الاختبار نجح!\n";
    
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
    echo "في الملف: " . $e->getFile() . "\n";
    echo "في السطر: " . $e->getLine() . "\n";
}
?>
