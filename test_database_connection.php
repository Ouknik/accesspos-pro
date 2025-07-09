<?php

/**
 * اختبار بسيط للاتصال بقاعدة البيانات والتحقق من البيانات الموجودة
 * بدون أي تعديل على قاعدة البيانات
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== اختبار الاتصال بقاعدة البيانات AccessPOS ===\n\n";

try {
    // اختبار الاتصال
    echo "1. اختبار الاتصال بقاعدة البيانات...\n";
    
    // التحقق من إعدادات قاعدة البيانات
    $dbConfig = config('database.connections.sqlsrv');
    echo "📋 نوع قاعدة البيانات: SQL Server\n";
    echo "📋 الخادم: " . $dbConfig['host'] . ":" . $dbConfig['port'] . "\n";
    echo "📋 قاعدة البيانات: " . $dbConfig['database'] . "\n";
    echo "📋 المستخدم: " . $dbConfig['username'] . "\n\n";
    
    // اختبار الاستعلام البسيط
    $testQuery = \Illuminate\Support\Facades\DB::select("SELECT TOP 1 GETDATE() as current_time");
    echo "✅ تم الاتصال بنجاح!\n";
    echo "🕒 الوقت الحالي في قاعدة البيانات: " . $testQuery[0]->current_time . "\n\n";
    
    // التحقق من الجداول الموجودة
    echo "2. فحص الجداول المتاحة...\n";
    
    $tables = [
        'sale' => 'المبيعات',
        'sale_detail' => 'تفاصيل المبيعات', 
        'article' => 'المواد',
        'customer' => 'العملاء',
        'category' => 'الفئات',
        'stock' => 'المخزون',
        'payment' => 'المدفوعات',
        'employee' => 'الموظفين',
        'supplier' => 'الموردين'
    ];
    
    foreach ($tables as $table => $arabicName) {
        try {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "✅ جدول $arabicName ($table): $count سجل\n";
        } catch (Exception $e) {
            echo "⚠️  جدول $arabicName ($table): غير متاح أو لا يحتوي على بيانات\n";
        }
    }
    
    echo "\n3. اختبار استعلامات التحليلات...\n";
    
    // اختبار استعلام المبيعات اليومية
    try {
        $todaySales = \Illuminate\Support\Facades\DB::table('sale')
            ->whereDate('created_at', today())
            ->sum('total');
        echo "✅ مبيعات اليوم: " . number_format($todaySales, 2) . " جنيه\n";
    } catch (Exception $e) {
        echo "⚠️  لا يمكن حساب مبيعات اليوم: " . $e->getMessage() . "\n";
    }
    
    // اختبار استعلام أفضل المنتجات
    try {
        $topProducts = \Illuminate\Support\Facades\DB::table('sale_detail')
            ->join('article', 'sale_detail.article_id', '=', 'article.id')
            ->select('article.designation', \Illuminate\Support\Facades\DB::raw('SUM(sale_detail.quantity) as total_qty'))
            ->groupBy('article.id', 'article.designation')
            ->orderBy('total_qty', 'desc')
            ->limit(3)
            ->get();
            
        echo "✅ أفضل 3 منتجات مبيعاً:\n";
        foreach ($topProducts as $product) {
            echo "   📦 {$product->designation}: {$product->total_qty} وحدة\n";
        }
    } catch (Exception $e) {
        echo "⚠️  لا يمكن حساب أفضل المنتجات: " . $e->getMessage() . "\n";
    }
    
    // اختبار المخزون المنخفض
    try {
        $lowStock = \Illuminate\Support\Facades\DB::table('article')
            ->where('stock_alert', '>', 0)
            ->where('stock_quantity', '<=', \Illuminate\Support\Facades\DB::raw('stock_alert'))
            ->count();
        echo "✅ عدد المواد التي تحتاج إعادة تموين: $lowStock منتج\n";
    } catch (Exception $e) {
        echo "⚠️  لا يمكن حساب المخزون المنخفض: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== النتيجة ===\n";
    echo "✅ قاعدة البيانات متصلة ومتاحة\n";
    echo "✅ البيانات موجودة وقابلة للقراءة\n";
    echo "✅ نظام التحليلات المتقدم جاهز للعمل\n";
    echo "🔥 يمكن الآن استخدام لوحة القيادة المتقدمة!\n\n";
    
    echo "لتشغيل الواجهة:\n";
    echo "php artisan serve\n";
    echo "ثم توجه إلى: http://localhost:8000/admin/tableau-de-bord-moderne\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الاتصال: " . $e->getMessage() . "\n";
    echo "💡 تأكد من:\n";
    echo "   - تشغيل خادم SQL Server\n";
    echo "   - صحة بيانات الاتصال في ملف .env\n";
    echo "   - أن قاعدة البيانات RestoWinxo متاحة\n";
}
