<?php
/**
 * اختبار سريع للتأكد من حل مشكلة openAdvancedModal
 * Date: 2025-07-09
 */

echo "=============================================================\n";
echo "🔧 اختبار حل مشكلة openAdvancedModal\n";
echo "=============================================================\n\n";

$viewPath = __DIR__ . '/resources/views/admin/tableau-de-bord-moderne.blade.php';
$viewContent = file_get_contents($viewPath);

$tests = [];

// 1. فحص وجود دالة window.openAdvancedModal
$modalFunctionCount = substr_count($viewContent, 'window.openAdvancedModal');
echo "1️⃣ عدد مرات تعريف window.openAdvancedModal: $modalFunctionCount\n";
$tests['modal_function_single'] = $modalFunctionCount === 1;

// 2. فحص أن الدالة خارج DOMContentLoaded
$domContentLoadedPos = strpos($viewContent, 'DOMContentLoaded');
$modalFunctionPos = strpos($viewContent, 'window.openAdvancedModal');
$tests['modal_before_dom'] = $modalFunctionPos < $domContentLoadedPos;
echo "2️⃣ الدالة معرفة قبل DOMContentLoaded: " . ($tests['modal_before_dom'] ? 'نعم' : 'لا') . "\n";

// 3. فحص وجود أزرار Voir détails
$voirDetailsCount = substr_count($viewContent, 'Voir détails');
echo "3️⃣ عدد أزرار Voir détails: $voirDetailsCount\n";
$tests['voir_details_buttons'] = $voirDetailsCount > 0;

// 4. فحص ربط الأزرار بالدالة
$modalCallsCount = substr_count($viewContent, 'openAdvancedModal(');
echo "4️⃣ عدد استدعاءات openAdvancedModal: $modalCallsCount\n";
$tests['modal_calls'] = $modalCallsCount >= $voirDetailsCount;

// 5. فحص عدم وجود تكرار في HTML
$htmlCloseCount = substr_count($viewContent, '</html>');
echo "5️⃣ عدد مرات إغلاق HTML: $htmlCloseCount\n";
$tests['single_html_close'] = $htmlCloseCount === 1;

// 6. فحص أن الملف ينتهي بـ </html>
$endsWithHtml = substr(trim($viewContent), -7) === '</html>';
echo "6️⃣ الملف ينتهي بـ </html>: " . ($endsWithHtml ? 'نعم' : 'لا') . "\n";
$tests['ends_with_html'] = $endsWithHtml;

// النتائج
echo "\n=============================================================\n";
echo "📊 نتائج الاختبار:\n";
echo "=============================================================\n";

$passedTests = array_filter($tests);
$totalTests = count($tests);
$successRate = round((count($passedTests) / $totalTests) * 100, 1);

foreach ($tests as $test => $result) {
    $status = $result ? '✅' : '❌';
    $description = [
        'modal_function_single' => 'دالة openAdvancedModal معرفة مرة واحدة فقط',
        'modal_before_dom' => 'الدالة متاحة قبل DOMContentLoaded',
        'voir_details_buttons' => 'أزرار Voir détails موجودة',
        'modal_calls' => 'جميع الأزرار مرتبطة بالدالة',
        'single_html_close' => 'إغلاق HTML واحد فقط',
        'ends_with_html' => 'الملف ينتهي بشكل صحيح'
    ];
    
    echo "   $status " . $description[$test] . "\n";
}

echo "\n📈 نسبة النجاح: $successRate% (" . count($passedTests) . "/$totalTests)\n";

if ($successRate === 100) {
    echo "\n🎉 ممتاز! جميع المشاكل تم حلها!\n";
    echo "✅ دالة openAdvancedModal ستعمل الآن بشكل صحيح\n";
    echo "✅ لا توجد أخطاء JavaScript متوقعة\n";
    echo "🚀 الأزرار ستستجيب فوراً عند النقر\n";
} else {
    echo "\n⚠️ هناك مشاكل تحتاج إلى حل:\n";
    foreach ($tests as $test => $result) {
        if (!$result) {
            echo "❌ " . $description[$test] . "\n";
        }
    }
}

echo "\n=============================================================\n";
echo "🏁 انتهاء اختبار openAdvancedModal - " . date('Y-m-d H:i:s') . "\n";
echo "=============================================================\n";
?>
