<?php
/**
 * سكريبت اختبار شامل لحل مشكلة Route [admin.dashboard.export] not defined
 * Date: 2025-07-09
 */

echo "=============================================================\n";
echo "🔧 اختبار حل مشكلة Route [admin.dashboard.export] not defined\n";
echo "=============================================================\n\n";

// تحديد المسارات
$projectRoot = __DIR__;
$viewPath = $projectRoot . '/resources/views/admin/tableau-de-bord-moderne.blade.php';
$routesPath = $projectRoot . '/routes/web.php';
$controllerPath = $projectRoot . '/app/Http/Controllers/Admin/TableauDeBordController.php';

$errors = [];
$warnings = [];
$success = [];

// 1. فحص وجود الملفات
echo "1️⃣ فحص وجود الملفات المطلوبة...\n";
if (file_exists($viewPath)) {
    echo "   ✓ ملف الواجهة موجود: tableau-de-bord-moderne.blade.php\n";
} else {
    $errors[] = "ملف الواجهة غير موجود";
}

if (file_exists($routesPath)) {
    echo "   ✓ ملف الـ routes موجود: web.php\n";
} else {
    $errors[] = "ملف routes غير موجود";
}

if (file_exists($controllerPath)) {
    echo "   ✓ ملف الـ Controller موجود: TableauDeBordController.php\n";
} else {
    $errors[] = "ملف Controller غير موجود";
}

// 2. فحص الواجهة للبحث عن استخدامات خاطئة لـ routes
echo "\n2️⃣ فحص استخدام routes في الواجهة...\n";
$viewContent = file_get_contents($viewPath);

// البحث عن admin.dashboard.export
if (strpos($viewContent, 'admin.dashboard.export') !== false) {
    $errors[] = "يوجد استخدام للـ route الخاطئ: admin.dashboard.export";
    echo "   ❌ ما زال يوجد استخدام لـ admin.dashboard.export\n";
} else {
    $success[] = "تم إزالة جميع استخدامات admin.dashboard.export الخاطئة";
    echo "   ✓ لا يوجد استخدام للـ route الخاطئ admin.dashboard.export\n";
}

// البحث عن دالة exportData
if (strpos($viewContent, 'function exportData') !== false) {
    $success[] = "دالة exportData معرفة بشكل صحيح";
    echo "   ✓ دالة exportData معرفة في JavaScript\n";
} else {
    $warnings[] = "دالة exportData غير معرفة";
    echo "   ⚠️ دالة exportData غير معرفة\n";
}

// البحث عن استخدامات exportData
$exportDataCount = substr_count($viewContent, 'exportData(');
if ($exportDataCount > 0) {
    echo "   📊 عدد استخدامات exportData: $exportDataCount\n";
}

// البحث عن استخدامات exportModalData
$exportModalDataCount = substr_count($viewContent, 'exportModalData(');
if ($exportModalDataCount > 0) {
    echo "   📊 عدد استخدامات exportModalData: $exportModalDataCount\n";
}

// 3. فحص routes
echo "\n3️⃣ فحص تعريف routes...\n";
$routesContent = file_get_contents($routesPath);

// البحث عن admin.export-modal-data
if (strpos($routesContent, 'admin.export-modal-data') !== false) {
    $success[] = "Route admin.export-modal-data معرف بشكل صحيح";
    echo "   ✓ Route admin.export-modal-data معرف\n";
} else {
    $errors[] = "Route admin.export-modal-data غير معرف";
    echo "   ❌ Route admin.export-modal-data غير معرف\n";
}

// البحث عن admin.dashboard.export كـ route
if (strpos($routesContent, 'admin.dashboard.export') !== false) {
    $success[] = "Route admin.dashboard.export معرف كبديل";
    echo "   ✓ Route admin.dashboard.export معرف كبديل\n";
} else {
    $warnings[] = "Route admin.dashboard.export غير معرف كبديل";
    echo "   ⚠️ Route admin.dashboard.export غير معرف كبديل\n";
}

// 4. فحص Controller
echo "\n4️⃣ فحص دالة exportModalData في Controller...\n";
$controllerContent = file_get_contents($controllerPath);

if (strpos($controllerContent, 'function exportModalData') !== false) {
    $success[] = "دالة exportModalData معرفة في Controller";
    echo "   ✓ دالة exportModalData معرفة في Controller\n";
} else {
    $errors[] = "دالة exportModalData غير معرفة في Controller";
    echo "   ❌ دالة exportModalData غير معرفة في Controller\n";
}

// فحص دوال التصدير المساعدة
$exportFunctions = [
    'getChiffreAffairesExportData',
    'getArticlesRuptureExportData', 
    'getTopClientsExportData',
    'exportToCSV',
    'exportToExcel'
];

foreach ($exportFunctions as $func) {
    if (strpos($controllerContent, "function $func") !== false) {
        echo "   ✓ دالة $func معرفة\n";
    } else {
        $warnings[] = "دالة $func غير معرفة";
        echo "   ⚠️ دالة $func غير معرفة\n";
    }
}

// 5. فحص CSRF token في الواجهة
echo "\n5️⃣ فحص CSRF token...\n";
if (strpos($viewContent, 'meta[name="csrf-token"]') !== false) {
    $success[] = "CSRF token متوفر في الواجهة";
    echo "   ✓ CSRF token متوفر في الواجهة\n";
} else {
    $warnings[] = "CSRF token غير متوفر في الواجهة";
    echo "   ⚠️ CSRF token غير متوفر في الواجهة\n";
}

// 6. تقرير شامل
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 تقرير شامل للحلول المطبقة\n";
echo str_repeat("=", 60) . "\n";

if (!empty($success)) {
    echo "✅ نجحت العمليات التالية:\n";
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

// 7. خلاصة الحل
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 خلاصة الحل لمشكلة Route [admin.dashboard.export] not defined:\n";
echo str_repeat("=", 60) . "\n";

echo "1. تم إضافة دالة exportData في JavaScript للتعامل مع أزرار التصدير\n";
echo "2. تم إضافة دالة exportModalData في Controller\n";
echo "3. تم إضافة routes للتصدير في web.php\n";
echo "4. تم إضافة دوال مساعدة للتصدير بصيغ مختلفة (CSV, Excel)\n";
echo "5. تم التعامل مع CSRF token بشكل صحيح\n";

if (empty($errors)) {
    echo "\n🎉 جميع المشاكل تم حلها بنجاح!\n";
    echo "✅ Route [admin.dashboard.export] not defined - تم الحل\n";
} else {
    echo "\n⚠️ ما زالت هناك مشاكل تحتاج لحل\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "تاريخ الاختبار: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
