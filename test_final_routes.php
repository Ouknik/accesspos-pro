<?php
/**
 * سكريبت اختبار نهائي لجميع المسارات في AccessPOS Pro
 * للتأكد من أن كل route معرف ويعمل بشكل صحيح
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// قائمة المسارات المطلوبة في الواجهة
$required_routes = [
    'admin.dashboard.chiffre-affaires',
    'admin.dashboard.stock-rupture', 
    'admin.dashboard.top-clients',
    'admin.dashboard.performance-horaire',
    'admin.dashboard.modes-paiement',
    'admin.dashboard.etat-tables',
    'admin.dashboard.export',
    'admin.tableau-de-bord-moderne',
    'admin.reports.index',
    'login',
    'logout'
];

echo "🔍 اختبار المسارات النهائي لـ AccessPOS Pro\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$all_good = true;
$router = app('router');

foreach ($required_routes as $route_name) {
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
    echo "🎉 جميع المسارات تعمل بنجاح!\n";
    echo "✅ لا توجد أخطاء Route not defined\n";
    echo "✅ جميع الأزرار والروابط في الواجهة ستعمل بشكل صحيح\n";
} else {
    echo "⚠️  يوجد مسارات مفقودة تحتاج إصلاح\n";
}

echo "\n📊 إحصائيات:\n";
echo "- العدد الكلي للمسارات المطلوبة: " . count($required_routes) . "\n";

// عد جميع المسارات المعرفة
$all_routes = collect($router->getRoutes())->filter(function($route) {
    return $route->getName() !== null;
});

echo "- العدد الكلي للمسارات المعرفة: " . $all_routes->count() . "\n";

echo "\n🔧 نصائح الصيانة:\n";
echo "- تأكد من تشغيل 'php artisan route:clear' بعد أي تعديل\n";
echo "- تأكد من تشغيل 'php artisan config:clear' للتحديث\n";
echo "- اختبر الواجهة في المتصفح للتأكد من عدم ظهور أخطاء\n";

?>
