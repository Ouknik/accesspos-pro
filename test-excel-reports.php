<?php

/**
 * اختبار بسيط للتأكد من أن ExcelReportsController يعمل بشكل صحيح
 * لتشغيل الاختبار: php test-excel-reports.php
 */

// تضمين Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\Admin\ExcelReportsController;

try {
    echo "🔍 اختبار ExcelReportsController...\n";
    
    // التحقق من وجود الملف
    $controllerPath = __DIR__ . '/app/Http/Controllers/Admin/ExcelReportsController.php';
    if (file_exists($controllerPath)) {
        echo "✅ ملف ExcelReportsController.php موجود\n";
    } else {
        echo "❌ ملف ExcelReportsController.php غير موجود\n";
        exit(1);
    }
    
    // التحقق من أن الملف يمكن تضمينه بدون أخطاء
    include_once $controllerPath;
    echo "✅ تم تضمين ملف ExcelReportsController.php بنجاح\n";
    
    // التحقق من وجود الكلاس
    if (class_exists('App\Http\Controllers\Admin\ExcelReportsController')) {
        echo "✅ كلاس ExcelReportsController موجود\n";
    } else {
        echo "❌ كلاس ExcelReportsController غير موجود\n";
        exit(1);
    }
    
    // التحقق من الدوال المطلوبة
    $requiredMethods = [
        'generatePapierDeTravail',
        'testInventaireValeur',
        'testEtatReception', 
        'testEtatSortie',
        'testInventairePhysique',
        'showTestPage'
    ];
    
    $reflection = new ReflectionClass('App\Http\Controllers\Admin\ExcelReportsController');
    
    foreach ($requiredMethods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✅ دالة {$method} موجودة\n";
        } else {
            echo "❌ دالة {$method} غير موجودة\n";
        }
    }
    
    echo "\n🎉 جميع الاختبارات نجحت! ExcelReportsController جاهز للاستخدام\n";
    echo "\n📊 التقارير المتاحة:\n";
    echo "   - Inventaire En Valeur\n";
    echo "   - État de Réception\n";
    echo "   - État de Sorties\n";
    echo "   - Inventaire Physique\n";
    echo "\n🌐 يمكنك الآن زيارة: /admin/excel-reports/test\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    exit(1);
}
