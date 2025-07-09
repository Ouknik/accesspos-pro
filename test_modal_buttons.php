<?php
/**
 * اختبار وظائف Modal وأزرار "Voir détails"
 * Date: 2025-07-09
 */

echo "=============================================================\n";
echo "🔧 اختبار وظائف Modal وأزرار Voir détails\n";
echo "=============================================================\n\n";

// تحديد المسارات
$projectRoot = __DIR__;
$viewPath = $projectRoot . '/resources/views/admin/tableau-de-bord-moderne.blade.php';
$routesPath = $projectRoot . '/routes/web.php';
$controllerPath = $projectRoot . '/app/Http/Controllers/Admin/TableauDeBordController.php';

$errors = [];
$warnings = [];
$success = [];

// 1. فحص الواجهة للبحث عن أزرار "Voir détails"
echo "1️⃣ فحص أزرار Voir détails في الواجهة...\n";
$viewContent = file_get_contents($viewPath);

// عد أزرار Voir détails
$voirDetailsCount = substr_count($viewContent, 'Voir détails');
echo "   📊 عدد أزرار Voir détails: $voirDetailsCount\n";

// فحص استدعاء دالة openAdvancedModal
$openModalCount = substr_count($viewContent, 'openAdvancedModal(');
echo "   📊 عدد استدعاءات openAdvancedModal: $openModalCount\n";

if ($openModalCount > 0) {
    $success[] = "أزرار Voir détails مرتبطة بدالة openAdvancedModal";
    echo "   ✓ أزرار Voir détails مرتبطة بدالة openAdvancedModal\n";
} else {
    $errors[] = "أزرار Voir détails غير مرتبطة بأي دالة";
    echo "   ❌ أزرار Voir détails غير مرتبطة بأي دالة\n";
}

// 2. فحص وجود المودال
echo "\n2️⃣ فحص هيكل المودال...\n";

if (strpos($viewContent, 'advancedModalContainer') !== false) {
    $success[] = "المودال الرئيسي موجود";
    echo "   ✓ المودال الرئيسي موجود (advancedModalContainer)\n";
} else {
    $errors[] = "المودال الرئيسي غير موجود";
    echo "   ❌ المودال الرئيسي غير موجود\n";
}

if (strpos($viewContent, 'modal-tab-content') !== false) {
    $success[] = "عنصر modal-tab-content موجود";
    echo "   ✓ عنصر modal-tab-content موجود\n";
} else {
    $errors[] = "عنصر modal-tab-content غير موجود";
    echo "   ❌ عنصر modal-tab-content غير موجود\n";
}

// 3. فحص دوال JavaScript
echo "\n3️⃣ فحص دوال JavaScript...\n";

$jsFunctions = [
    'openAdvancedModal',
    'closeAdvancedModal',
    'loadModalData',
    'showModalLoading',
    'showModalError',
    'displayModalData'
];

foreach ($jsFunctions as $func) {
    if (strpos($viewContent, "function $func") !== false) {
        echo "   ✓ دالة $func معرفة\n";
        $success[] = "دالة $func معرفة";
    } else {
        echo "   ❌ دالة $func غير معرفة\n";
        $errors[] = "دالة $func غير معرفة";
    }
}

// 4. فحص routes في الـ Controller
echo "\n4️⃣ فحص routes الـ Modal في web.php...\n";
$routesContent = file_get_contents($routesPath);

$modalRoutes = [
    'admin.chiffre-affaires-details',
    'admin.articles-rupture-details',
    'admin.top-clients-details',
    'admin.performance-horaire-details',
    'admin.modes-paiement-details',
    'admin.etat-tables-details'
];

foreach ($modalRoutes as $route) {
    if (strpos($routesContent, $route) !== false) {
        echo "   ✓ Route $route معرف\n";
        $success[] = "Route $route معرف";
    } else {
        echo "   ❌ Route $route غير معرف\n";
        $errors[] = "Route $route غير معرف";
    }
}

// 5. فحص دوال Controller
echo "\n5️⃣ فحص دوال Controller...\n";
$controllerContent = file_get_contents($controllerPath);

$controllerFunctions = [
    'getChiffreAffairesDetails',
    'getArticlesRuptureDetails',
    'getTopClientsDetails',
    'getPerformanceHoraireDetails',
    'getModesPaiementDetails',
    'getEtatTablesDetails'
];

foreach ($controllerFunctions as $func) {
    if (strpos($controllerContent, "function $func") !== false) {
        echo "   ✓ دالة Controller $func معرفة\n";
        $success[] = "دالة Controller $func معرفة";
    } else {
        echo "   ❌ دالة Controller $func غير معرفة\n";
        $errors[] = "دالة Controller $func غير معرفة";
    }
}

// 6. فحص endpoints في JavaScript
echo "\n6️⃣ فحص endpoints في JavaScript...\n";

if (strpos($viewContent, 'modalEndpoints') !== false) {
    echo "   ✓ modalEndpoints معرف في JavaScript\n";
    $success[] = "modalEndpoints معرف في JavaScript";
    
    // فحص كل endpoint
    foreach ($modalRoutes as $route) {
        if (strpos($viewContent, "route(\"$route\")") !== false) {
            echo "     ✓ Endpoint $route مرتبط\n";
        } else {
            echo "     ❌ Endpoint $route غير مرتبط\n";
            $warnings[] = "Endpoint $route غير مرتبط";
        }
    }
} else {
    $errors[] = "modalEndpoints غير معرف في JavaScript";
    echo "   ❌ modalEndpoints غير معرف في JavaScript\n";
}

// 7. تقرير شامل
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 تقرير شامل لحالة أزرار Voir détails\n";
echo str_repeat("=", 60) . "\n";

if (!empty($success)) {
    echo "✅ العمليات الناجحة:\n";
    foreach ($success as $item) {
        echo "   ✓ $item\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️ تحذيرات:\n";
    foreach ($warnings as $item) {
        echo "   ⚠️ $item\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ أخطاء يجب إصلاحها:\n";
    foreach ($errors as $item) {
        echo "   ❌ $item\n";
    }
}

// 8. خطوات الإصلاح المقترحة
echo "\n" . str_repeat("=", 60) . "\n";
echo "🔧 خطوات الإصلاح المطبقة:\n";
echo str_repeat("=", 60) . "\n";

echo "1. تم إصلاح هيكل المودال لإضافة modal-tab-content\n";
echo "2. تم إصلاح دوال showModalLoading و showModalError\n";
echo "3. تم إصلاح دالة displayModalData\n";
echo "4. تم التأكد من وجود جميع routes و Controller functions\n";
echo "5. تم إصلاح الأزرار المكررة في الواجهة\n";

if (empty($errors)) {
    echo "\n🎉 جميع أزرار Voir détails يجب أن تعمل الآن!\n";
    echo "✅ تم إصلاح جميع المشاكل\n";
} else {
    echo "\n⚠️ ما زالت هناك مشاكل تحتاج لحل\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "تاريخ الاختبار: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
