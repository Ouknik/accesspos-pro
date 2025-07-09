<?php
/**
 * سكريبت اختبار المسارات - لحل مشكلة Route [admin.dashboard.chiffre-affaires] not defined
 */

// تحديد مسار Laravel
$laravelPath = __DIR__;

// تشغيل Artisan لمسح الـ cache
echo "=== تنظيف الـ Cache ===\n";
exec("php artisan route:clear", $output1);
echo implode("\n", $output1) . "\n";

exec("php artisan config:clear", $output2);
echo implode("\n", $output2) . "\n";

exec("php artisan view:clear", $output3);
echo implode("\n", $output3) . "\n";

echo "\n=== فحص المسارات المتاحة ===\n";

// إنشاء instance من التطبيق لفحص المسارات
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

try {
    // إنشاء route collection
    $routes = $app->make('router')->getRoutes();
    
    echo "عدد المسارات المعرفة: " . count($routes) . "\n\n";
    
    // البحث عن المسارات المطلوبة
    $searchRoutes = [
        'admin.dashboard.chiffre-affaires',
        'admin.dashboard.stock-rupture',
        'admin.dashboard.top-clients',
        'admin.dashboard.performance-horaire',
        'admin.dashboard.modes-paiement',
        'admin.dashboard.etat-tables'
    ];
    
    $foundRoutes = [];
    $missingRoutes = [];
    
    foreach ($routes as $route) {
        $routeName = $route->getName();
        if (in_array($routeName, $searchRoutes)) {
            $foundRoutes[] = $routeName . ' => ' . $route->uri();
        }
    }
    
    // البحث عن المسارات المفقودة
    foreach ($searchRoutes as $searchRoute) {
        $found = false;
        foreach ($routes as $route) {
            if ($route->getName() === $searchRoute) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $missingRoutes[] = $searchRoute;
        }
    }
    
    echo "=== المسارات الموجودة ===\n";
    if (count($foundRoutes) > 0) {
        foreach ($foundRoutes as $route) {
            echo "✓ " . $route . "\n";
        }
    } else {
        echo "❌ لا توجد مسارات\n";
    }
    
    echo "\n=== المسارات المفقودة ===\n";
    if (count($missingRoutes) > 0) {
        foreach ($missingRoutes as $route) {
            echo "❌ " . $route . "\n";
        }
    } else {
        echo "✅ جميع المسارات موجودة\n";
    }
    
    // اختبار إنشاء URL
    echo "\n=== اختبار إنشاء URLs ===\n";
    try {
        $url = route('admin.dashboard.chiffre-affaires');
        echo "✅ URL للـ chiffre-affaires: " . $url . "\n";
    } catch (Exception $e) {
        echo "❌ خطأ في إنشاء URL: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في فحص المسارات: " . $e->getMessage() . "\n";
}

echo "\n=== فحص ملف web.php ===\n";
$webPhpContent = file_get_contents('routes/web.php');

// البحث عن المسارات في الملف
$routePatterns = [
    'admin.dashboard.chiffre-affaires',
    'admin.dashboard.stock-rupture',
    'admin.dashboard.top-clients'
];

foreach ($routePatterns as $pattern) {
    if (strpos($webPhpContent, $pattern) !== false) {
        echo "✅ تم العثور على $pattern في ملف web.php\n";
    } else {
        echo "❌ لم يتم العثور على $pattern في ملف web.php\n";
    }
}

echo "\n=== التوصيات ===\n";
echo "1. تأكد من تشغيل: php artisan route:clear\n";
echo "2. تأكد من تشغيل: php artisan config:clear\n"; 
echo "3. تحقق من أن ملف routes/web.php يحتوي على المسارات الصحيحة\n";
echo "4. تأكد من أن الـ middleware و group مكتوبة بشكل صحيح\n";

echo "\n=== انتهى الفحص ===\n";
