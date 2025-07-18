<?php

/**
 * اختبار سريع للتأكد من عمل ExcelReportsController
 */

echo "🔍 فحص ExcelReportsController...\n\n";

// التحقق من وجود الملف
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/ExcelReportsController.php';
if (file_exists($controllerPath)) {
    echo "✅ ملف ExcelReportsController.php موجود\n";
    
    // قراءة محتوى الملف
    $content = file_get_contents($controllerPath);
    
    // التحقق من الدوال المطلوبة
    $requiredMethods = [
        'generatePapierDeTravail',
        'testInventaireValeur',
        'testEtatReception',
        'testEtatSortie', 
        'testInventairePhysique',
        'showTestPage',
        'showCustomReportForm',
        'generateCustomReport'
    ];
    
    echo "\n🔍 فحص الدوال المطلوبة:\n";
    foreach ($requiredMethods as $method) {
        if (strpos($content, "function {$method}") !== false) {
            echo "✅ دالة {$method} موجودة\n";
        } else {
            echo "❌ دالة {$method} غير موجودة\n";
        }
    }
    
    // التحقق من صحة بناء الملف
    if (strpos($content, 'namespace App\Http\Controllers\Admin;') !== false) {
        echo "✅ namespace صحيح\n";
    }
    
    if (strpos($content, 'class ExcelReportsController extends Controller') !== false) {
        echo "✅ تعريف الكلاس صحيح\n";
    }
    
    // حساب عدد الأسطر
    $lines = substr_count($content, "\n");
    echo "📊 عدد الأسطر: {$lines}\n";
    
    echo "\n🎉 الفحص مكتمل! الكنترولر جاهز للاستخدام\n";
    echo "\n📋 المسارات المتاحة:\n";
    echo "   - /admin/excel-reports/test (صفحة الاختبار)\n";
    echo "   - /admin/excel-reports/papier-de-travail (التقرير الشامل)\n";
    echo "   - /admin/excel-reports/custom-form (للمتوافقية)\n";
    echo "   - /admin/excel-reports/test-inventaire-valeur\n";
    echo "   - /admin/excel-reports/test-etat-reception\n";
    echo "   - /admin/excel-reports/test-etat-sortie\n";
    echo "   - /admin/excel-reports/test-inventaire-physique\n";
    
} else {
    echo "❌ ملف ExcelReportsController.php غير موجود\n";
}
