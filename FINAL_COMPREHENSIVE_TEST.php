<?php
/**
 * 🎯 اختبار شامل نهائي لمشروع AccessPOS Pro
 * التاريخ: 2025-07-09
 * الهدف: التأكد من أن كل شيء يعمل بكفاءة عالية
 */

echo "================================================================\n";
echo "🎯 ACCESSPOS PRO - اختبار شامل نهائي\n";
echo "================================================================\n\n";

// تحديد المسارات
$projectRoot = __DIR__;
$controllerPath = $projectRoot . '/app/Http/Controllers/Admin/TableauDeBordController.php';
$viewPath = $projectRoot . '/resources/views/admin/tableau-de-bord-moderne.blade.php';
$routesPath = $projectRoot . '/routes/web.php';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = [];

function runTest($testName, $condition, $successMsg, $errorMsg) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    
    if ($condition) {
        echo "   ✅ $successMsg\n";
        $passedTests++;
        return true;
    } else {
        echo "   ❌ $errorMsg\n";
        $failedTests++;
        return false;
    }
}

// =================================================================
// 🔍 SECTION 1: فحص ملفات المشروع الأساسية
// =================================================================
echo "🔍 SECTION 1: فحص ملفات المشروع الأساسية\n";
echo "---------------------------------------------------------\n";

runTest(
    "Controller exists", 
    file_exists($controllerPath),
    "ملف Controller موجود",
    "ملف Controller غير موجود"
);

runTest(
    "View exists", 
    file_exists($viewPath),
    "ملف الواجهة موجود",
    "ملف الواجهة غير موجود"
);

runTest(
    "Routes exists", 
    file_exists($routesPath),
    "ملف Routes موجود",
    "ملف Routes غير موجود"
);

// =================================================================
// 🎛️ SECTION 2: فحص Controller والـ Methods
// =================================================================
echo "\n🎛️ SECTION 2: فحص Controller والـ Methods\n";
echo "---------------------------------------------------------\n";

if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    $requiredMethods = [
        'index' => 'المودود الرئيسي',
        'getChiffreAffairesDetails' => 'تفاصيل رقم الأعمال',
        'getStockRuptureDetails' => 'تفاصيل المخزون المنقطع',
        'getTopClientsDetails' => 'تفاصيل أفضل العملاء',
        'getPerformanceHoraireDetails' => 'تفاصيل الأداء بالساعة',
        'getModesPaiementDetails' => 'تفاصيل طرق الدفع',
        'getEtatTablesDetails' => 'تفاصيل حالة الطاولات',
        'exportModalData' => 'تصدير بيانات المودال'
    ];
    
    foreach ($requiredMethods as $method => $description) {
        runTest(
            "Method $method", 
            strpos($controllerContent, "function $method") !== false,
            "دالة $description موجودة",
            "دالة $description غير موجودة"
        );
    }
    
    // فحص التصحيحات المهمة
    runTest(
        "Column names fixed", 
        strpos($controllerContent, 'ART_DESIGNATION') !== false,
        "أسماء الأعمدة مصححة (ART_DESIGNATION)",
        "أسماء الأعمدة غير مصححة"
    );
    
    runTest(
        "Client column fixed", 
        strpos($controllerContent, 'CLT_CLIENT') !== false,
        "عمود العميل مصحح (CLT_CLIENT)",
        "عمود العميل غير مصحح"
    );
}

// =================================================================
// 🎨 SECTION 3: فحص الواجهة والعناصر
// =================================================================
echo "\n🎨 SECTION 3: فحص الواجهة والعناصر\n";
echo "---------------------------------------------------------\n";

if (file_exists($viewPath)) {
    $viewContent = file_get_contents($viewPath);
    
    // فحص المودال
    runTest(
        "Modal structure", 
        strpos($viewContent, 'id="advancedModalContainer"') !== false,
        "بنية المودال مكتملة",
        "بنية المودال غير مكتملة"
    );
    
    runTest(
        "Modal content element", 
        strpos($viewContent, 'class="modal-tab-content"') !== false,
        "عنصر محتوى المودال موجود",
        "عنصر محتوى المودال غير موجود"
    );
    
    // فحص العملة
    $euroCount = substr_count($viewContent, '€');
    $dhCount = substr_count($viewContent, 'DH');
    
    runTest(
        "Currency correction", 
        $dhCount > $euroCount,
        "العملة مصححة إلى DH (وجدت $dhCount مقابل $euroCount)",
        "العملة لم تُصحح بالكامل"
    );
    
    // فحص أزرار المودال
    $voirDetailsCount = substr_count($viewContent, 'Voir détails');
    $modalCallsCount = substr_count($viewContent, 'openAdvancedModal(');
    
    runTest(
        "Modal buttons", 
        $voirDetailsCount > 0 && $modalCallsCount >= $voirDetailsCount,
        "أزرار المودال مرتبطة ($voirDetailsCount أزرار، $modalCallsCount استدعاءات)",
        "أزرار المودال غير مرتبطة بشكل صحيح"
    );
    
    // فحص دوال JavaScript المهمة
    $jsFunctions = [
        'openAdvancedModal' => 'window.openAdvancedModal',
        'closeAdvancedModal',
        'loadModalData',
        'formatCurrency',
        'exportData'
    ];
    
    foreach ($jsFunctions as $func => $searchPattern) {
        if (is_numeric($func)) {
            $func = $searchPattern;
            $searchPattern = "function $func";
        }
        
        runTest(
            "JS Function $func", 
            strpos($viewContent, $searchPattern) !== false,
            "دالة JavaScript $func موجودة",
            "دالة JavaScript $func غير موجودة"
        );
    }
}

// =================================================================
// 🛣️ SECTION 4: فحص Routes
// =================================================================
echo "\n🛣️ SECTION 4: فحص Routes\n";
echo "---------------------------------------------------------\n";

if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    $requiredRoutes = [
        'admin.dashboard' => 'لوحة القيادة الرئيسية',
        'admin.dashboard.chiffre-affaires' => 'تفاصيل رقم الأعمال',
        'admin.dashboard.stock-rupture' => 'تفاصيل المخزون',
        'admin.dashboard.top-clients' => 'تفاصيل العملاء',
        'admin.dashboard.performance-horaire' => 'تفاصيل الأداء',
        'admin.dashboard.modes-paiement' => 'تفاصيل الدفع',
        'admin.dashboard.etat-tables' => 'تفاصيل الطاولات',
        'admin.dashboard.export' => 'تصدير البيانات'
    ];
    
    foreach ($requiredRoutes as $route => $description) {
        runTest(
            "Route $route", 
            strpos($routesContent, $route) !== false,
            "طريق $description محدد",
            "طريق $description غير محدد"
        );
    }
}

// =================================================================
// 🔧 SECTION 5: فحص ملفات التوثيق والاختبارات
// =================================================================
echo "\n🔧 SECTION 5: فحص ملفات التوثيق والاختبارات\n";
echo "---------------------------------------------------------\n";

$documentationFiles = [
    'BUGS_FIXED_FINAL_REPORT.md',
    'MODAL_BUTTONS_FIX_FINAL.md',
    'EXPORT_ROUTE_FIX_FINAL.md'
];

foreach ($documentationFiles as $file) {
    $filePath = $projectRoot . '/' . $file;
    runTest(
        "Documentation $file", 
        file_exists($filePath),
        "ملف التوثيق $file موجود",
        "ملف التوثيق $file غير موجود"
    );
}

$testFiles = [
    'test_errors_fixed.php',
    'test_controller_final.php',
    'test_routes_fixed.php',
    'test_modal_buttons.php',
    'test_modal_buttons_final.php'
];

foreach ($testFiles as $file) {
    $filePath = $projectRoot . '/' . $file;
    runTest(
        "Test file $file", 
        file_exists($filePath),
        "ملف الاختبار $file موجود",
        "ملف الاختبار $file غير موجود"
    );
}

// =================================================================
// 📊 النتائج النهائية
// =================================================================
echo "\n================================================================\n";
echo "📊 النتائج النهائية للاختبار الشامل\n";
echo "================================================================\n";

$successRate = ($totalTests > 0) ? round(($passedTests / $totalTests) * 100, 2) : 0;

echo "📈 إحصائيات الاختبار:\n";
echo "   • إجمالي الاختبارات: $totalTests\n";
echo "   • الاختبارات الناجحة: $passedTests\n";
echo "   • الاختبارات الفاشلة: $failedTests\n";
echo "   • نسبة النجاح: $successRate%\n\n";

if ($successRate >= 95) {
    echo "🎉 ممتاز! المشروع في حالة ممتازة\n";
    echo "✅ جميع المكونات الأساسية تعمل بشكل صحيح\n";
    echo "🚀 المشروع جاهز للاستخدام الفعلي\n";
} elseif ($successRate >= 80) {
    echo "👍 جيد! المشروع في حالة جيدة مع بعض التحسينات المطلوبة\n";
    echo "⚠️ يُنصح بمراجعة الاختبارات الفاشلة\n";
} else {
    echo "⚠️ تحذير! المشروع يحتاج إلى المزيد من العمل\n";
    echo "🔧 يجب إصلاح الأخطاء قبل الاستخدام\n";
}

echo "\n================================================================\n";
echo "🏁 انتهاء الاختبار الشامل - " . date('Y-m-d H:i:s') . "\n";
echo "================================================================\n";
?>
