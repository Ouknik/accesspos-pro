<?php
/**
 * اختبار شامل نهائي لحل مشكلة Route [admin.chiffre-affaires-details] not defined
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎯 حل مشكلة Route [admin.chiffre-affaires-details] not defined\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// 1. التحقق من المسار المحدد
echo "1️⃣ اختبار المسار المطلوب:\n";
try {
    $url = route('admin.chiffre-affaires-details');
    echo "✅ admin.chiffre-affaires-details -> $url\n";
} catch (Exception $e) {
    echo "❌ admin.chiffre-affaires-details -> خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. التحقق من جميع المسارات ذات الصلة
echo "\n2️⃣ اختبار جميع مسارات التفاصيل:\n";
$detail_routes = [
    'admin.chiffre-affaires-details',
    'admin.articles-rupture-details',
    'admin.top-clients-details', 
    'admin.performance-horaire-details',
    'admin.modes-paiement-details',
    'admin.etat-tables-details'
];

$working_routes = 0;
foreach ($detail_routes as $route_name) {
    try {
        $url = route($route_name);
        echo "✅ $route_name\n";
        $working_routes++;
    } catch (Exception $e) {
        echo "❌ $route_name -> " . $e->getMessage() . "\n";
    }
}

// 3. التحقق من المسارات الأساسية
echo "\n3️⃣ اختبار المسارات الأساسية:\n";
$basic_routes = [
    'admin.dashboard.chiffre-affaires',
    'admin.dashboard.stock-rupture',
    'admin.dashboard.top-clients',
    'admin.dashboard.etat-tables',
    'admin.tableau-de-bord-moderne',
    'login',
    'logout'
];

$working_basic = 0;
foreach ($basic_routes as $route_name) {
    try {
        $url = route($route_name);
        echo "✅ $route_name\n";
        $working_basic++;
    } catch (Exception $e) {
        echo "❌ $route_name -> " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 نتائج الاختبار:\n";
echo "- مسارات التفاصيل: $working_routes/" . count($detail_routes) . "\n";
echo "- المسارات الأساسية: $working_basic/" . count($basic_routes) . "\n";

$total_working = $working_routes + $working_basic;
$total_routes = count($detail_routes) + count($basic_routes);

if ($total_working == $total_routes) {
    echo "\n🎉 تم حل المشكلة بنجاح!\n";
    echo "✅ Route [admin.chiffre-affaires-details] معرف وجاهز\n";
    echo "✅ جميع المسارات تعمل بشكل صحيح\n";
    echo "✅ لن تظهر أخطاء Route not defined في الواجهة\n";
    
    echo "\n🔧 خطوات التحقق النهائية:\n";
    echo "1. تشغيل الخادم: php artisan serve\n";
    echo "2. فتح المتصفح على لوحة القيادة\n";
    echo "3. اختبار الأزرار للتأكد من عدم ظهور أخطاء\n";
} else {
    echo "\n⚠️  يوجد مسارات تحتاج إصلاح إضافي\n";
    echo "المطلوب: إضافة " . ($total_routes - $total_working) . " مسار إضافي\n";
}

?>
