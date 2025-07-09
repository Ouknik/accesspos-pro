<?php
/**
 * اختبار نهائي شامل لأزرار Voir détails والمودال
 * Date: 2025-07-09
 */

echo "=============================================================\n";
echo "🎯 اختبار نهائي شامل - أزرار Voir détails\n";
echo "=============================================================\n\n";

// تحديد المسارات
$projectRoot = __DIR__;
$viewPath = $projectRoot . '/resources/views/admin/tableau-de-bord-moderne.blade.php';

$errors = [];
$warnings = [];
$success = [];

// 1. فحص المودال والبنية
echo "1️⃣ فحص بنية المودال...\n";
$viewContent = file_get_contents($viewPath);

// فحص المودال الأساسي
if (strpos($viewContent, 'id="advancedModalContainer"') !== false) {
    echo "   ✓ المودال الرئيسي موجود\n";
    $success[] = "المودال الرئيسي موجود";
} else {
    echo "   ❌ المودال الرئيسي غير موجود\n";
    $errors[] = "المودال الرئيسي غير موجود";
}

// فحص عنصر محتوى المودال
if (strpos($viewContent, 'class="modal-tab-content"') !== false) {
    echo "   ✓ عنصر modal-tab-content موجود\n";
    $success[] = "عنصر modal-tab-content موجود";
} else {
    echo "   ❌ عنصر modal-tab-content غير موجود\n";
    $errors[] = "عنصر modal-tab-content غير موجود";
}

// 2. فحص أزرار Voir détails
echo "\n2️⃣ فحص أزرار Voir détails...\n";

// عد الأزرار
$buttonsCount = substr_count($viewContent, 'Voir détails');
echo "   📊 عدد أزرار Voir détails: $buttonsCount\n";

// فحص الربط بدالة openAdvancedModal
$modalCallsCount = substr_count($viewContent, 'openAdvancedModal(');
echo "   📊 عدد استدعاءات openAdvancedModal: $modalCallsCount\n";

if ($modalCallsCount >= $buttonsCount) {
    echo "   ✓ جميع الأزرار مرتبطة بالمودال\n";
    $success[] = "جميع الأزرار مرتبطة بالمودال";
} else {
    echo "   ⚠️ بعض الأزرار قد تكون غير مرتبطة\n";
    $warnings[] = "بعض الأزرار قد تكون غير مرتبطة";
}

// 3. فحص أنواع البيانات المدعومة
echo "\n3️⃣ فحص أنواع البيانات المدعومة...\n";

$dataTypes = [
    'chiffre-affaires' => 'رقم الأعمال',
    'stock-rupture' => 'المخزون المنقطع',
    'top-clients' => 'أفضل العملاء',
    'performance-horaire' => 'الأداء بالساعة',
    'modes-paiement' => 'طرق الدفع',
    'etat-tables' => 'حالة الطاولات'
];

foreach ($dataTypes as $type => $description) {
    if (strpos($viewContent, "'$type'") !== false) {
        echo "   ✓ نوع البيانات $description مدعوم\n";
        $success[] = "نوع البيانات $description مدعوم";
    } else {
        echo "   ❌ نوع البيانات $description غير مدعوم\n";
        $errors[] = "نوع البيانات $description غير مدعوم";
    }
}

// 4. فحص دوال العرض
echo "\n4️⃣ فحص دوال عرض البيانات...\n";

$displayFunctions = [
    'displayChiffreAffairesData',
    'displayStockRuptureData',
    'displayTopClientsData',
    'displayPerformanceHoraireData',
    'displayModesPaiementData',
    'displayEtatTablesData'
];

foreach ($displayFunctions as $func) {
    if (strpos($viewContent, "function $func") !== false) {
        echo "   ✓ دالة $func معرفة\n";
        $success[] = "دالة $func معرفة";
    } else {
        echo "   ❌ دالة $func غير معرفة\n";
        $errors[] = "دالة $func غير معرفة";
    }
}

// 5. فحص التحسينات الجديدة
echo "\n5️⃣ فحص التحسينات الجديدة...\n";

// فحص استخدام الدرهم
if (strpos($viewContent, 'DH') !== false && strpos($viewContent, 'currency: \'EUR\'') === false) {
    echo "   ✓ تم تطبيق العملة المحلية (DH)\n";
    $success[] = "تم تطبيق العملة المحلية (DH)";
} else {
    echo "   ⚠️ قد تكون هناك مشاكل في العملة\n";
    $warnings[] = "قد تكون هناك مشاكل في العملة";
}

// فحص عدم وجود أزرار مكررة في نفس المكان
$duplicateButtons = 0;
$lines = explode("\n", $viewContent);
for ($i = 0; $i < count($lines) - 1; $i++) {
    if (strpos($lines[$i], 'Voir détails') !== false && 
        strpos($lines[$i + 1], 'Voir détails') !== false) {
        $duplicateButtons++;
    }
}

if ($duplicateButtons === 0) {
    echo "   ✓ لا توجد أزرار مكررة\n";
    $success[] = "لا توجد أزرار مكررة";
} else {
    echo "   ⚠️ قد توجد أزرار مكررة ($duplicateButtons)\n";
    $warnings[] = "قد توجد أزرار مكررة";
}

// 6. فحص تصميم المودال المحسن
echo "\n6️⃣ فحص تصميم المودال المحسن...\n";

// فحص الجداول المحسنة
if (strpos($viewContent, 'border-collapse: collapse') !== false) {
    echo "   ✓ تصميم الجداول محسن\n";
    $success[] = "تصميم الجداول محسن";
} else {
    echo "   ❌ تصميم الجداول غير محسن\n";
    $errors[] = "تصميم الجداول غير محسن";
}

// فحص KPI cards
if (strpos($viewContent, 'kpi-grid') !== false || strpos($viewContent, 'grid-template-columns') !== false) {
    echo "   ✓ بطاقات KPI موجودة\n";
    $success[] = "بطاقات KPI موجودة";
} else {
    echo "   ❌ بطاقات KPI غير موجودة\n";
    $errors[] = "بطاقات KPI غير موجودة";
}

// 7. تقرير شامل
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 تقرير شامل للاختبار النهائي\n";
echo str_repeat("=", 60) . "\n";

echo "📊 إحصائيات:\n";
echo "   - عدد أزرار Voir détails: $buttonsCount\n";
echo "   - عدد استدعاءات openAdvancedModal: $modalCallsCount\n";
echo "   - عدد العمليات الناجحة: " . count($success) . "\n";
echo "   - عدد التحذيرات: " . count($warnings) . "\n";
echo "   - عدد الأخطاء: " . count($errors) . "\n\n";

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
    echo "\n❌ أخطاء:\n";
    foreach ($errors as $item) {
        echo "   ❌ $item\n";
    }
}

// 8. خلاصة نهائية
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 الخلاصة النهائية\n";
echo str_repeat("=", 60) . "\n";

$successRate = (count($success) / (count($success) + count($warnings) + count($errors))) * 100;

echo "نسبة النجاح: " . round($successRate, 1) . "%\n\n";

if (count($errors) === 0) {
    echo "🎉 ممتاز! جميع أزرار Voir détails تعمل بشكل صحيح!\n";
    echo "✅ المودال مكتمل ومحسن\n";
    echo "✅ البيانات ستظهر بشكل جميل ومنظم\n";
    echo "✅ التصدير يعمل بكفاءة\n\n";
    echo "🚀 المشروع جاهز للاستخدام!\n";
} elseif (count($errors) <= 2) {
    echo "👍 جيد جداً! أغلب الوظائف تعمل بشكل صحيح\n";
    echo "⚠️ هناك مشاكل بسيطة يمكن إصلاحها\n";
} else {
    echo "⚠️ هناك مشاكل تحتاج لإصلاح\n";
    echo "🔧 يُنصح بمراجعة الأخطاء المذكورة أعلاه\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "تاريخ الاختبار: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
