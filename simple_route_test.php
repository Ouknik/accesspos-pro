<?php
/**
 * اختبار بسيط للمسارات
 */

require_once 'vendor/autoload.php';

// إنشاء Laravel Application
$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    echo "=== اختبار المسارات ===\n";
    
    // اختبار route helper
    $routes = [
        'admin.dashboard.chiffre-affaires',
        'admin.dashboard.stock-rupture',
        'admin.dashboard.top-clients',
        'admin.dashboard.performance-horaire',
        'admin.dashboard.modes-paiement',
        'admin.dashboard.etat-tables'
    ];
    
    foreach ($routes as $routeName) {
        try {
            $url = route($routeName);
            echo "✅ $routeName => $url\n";
        } catch (Exception $e) {
            echo "❌ $routeName => ERROR: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "خطأ عام: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
