<?php
/**
 * اختبار سريع للمسارات المطلوبة في ملف tableau-de-bord-moderne.blade.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 اختبار المسارات المطلوبة في الواجهة\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// قائمة المسارات المطلوبة في ملف modalEndpoints
$modal_routes = [
    'admin.chiffre-affaires-details',
    'admin.articles-rupture-details', 
    'admin.top-clients-details',
    'admin.performance-horaire-details',
    'admin.modes-paiement-details',
    'admin.etat-tables-details'
];

$all_good = true;

foreach ($modal_routes as $route_name) {
    try {
        $url = route($route_name);
        echo "✅ $route_name -> $url\n";
    } catch (Exception $e) {
        echo "❌ $route_name -> خطأ: " . $e->getMessage() . "\n";
        $all_good = false;
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

if ($all_good) {
    echo "🎉 جميع المسارات المطلوبة للواجهة تعمل بنجاح!\n";
    echo "✅ لن تظهر أخطاء Route not defined في JavaScript\n";
} else {
    echo "⚠️  يوجد مسارات مفقودة تحتاج إصلاح\n";
}

?>
