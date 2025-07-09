<?php
/**
 * اختبار نهائي للواجهة والتأكد من عدم وجود أخطاء Route not defined
 * يجب تشغيل الخادم أولاً: php artisan serve
 */

echo "🔍 اختبار الواجهة النهائي - AccessPOS Pro\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// التحقق من أن الخادم يعمل
$server_url = 'http://127.0.0.1:8000';
$dashboard_url = $server_url . '/admin/tableau-de-bord-moderne';

echo "🌐 اختبار الاتصال بالخادم...\n";

// استخدام curl للتحقق من الصفحة
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboard_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ خطأ في الاتصال: $error\n";
    echo "💡 تأكد من تشغيل الخادم: php artisan serve\n";
} else {
    echo "✅ الخادم يعمل - HTTP Code: $http_code\n";
    
    if ($response) {
        // البحث عن أخطاء Route not defined في HTML
        if (strpos($response, 'Route [') !== false && strpos($response, 'not defined') !== false) {
            echo "❌ تم العثور على أخطاء Route في الصفحة\n";
            
            // استخراج اسم الـ route المفقود
            preg_match('/Route \[([^\]]+)\] not defined/', $response, $matches);
            if ($matches) {
                echo "🔍 Route مفقود: " . $matches[1] . "\n";
            }
        } else {
            echo "✅ لا توجد أخطاء Route في الصفحة\n";
        }
        
        // التحقق من وجود النص "Voir détails" 
        $details_count = substr_count($response, 'Voir détails');
        echo "📊 عدد أزرار 'Voir détails' في الصفحة: $details_count\n";
        
        // التحقق من وجود روابط admin.dashboard
        $dashboard_links = substr_count($response, 'admin.dashboard.');
        echo "🔗 عدد روابط admin.dashboard في الصفحة: $dashboard_links\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🏁 انتهاء الاختبار\n";
echo "\n💡 للاختبار اليدوي:\n";
echo "1. تشغيل الخادم: php artisan serve\n";
echo "2. فتح المتصفح على: $dashboard_url\n";
echo "3. اختبار النقر على أزرار 'Voir détails'\n";
echo "4. التأكد من فتح صفحات التفاصيل بدون أخطاء\n";

?>
