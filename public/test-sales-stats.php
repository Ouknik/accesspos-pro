<?php
/**
 * ملف اختبار الإحصائيات - جميع المبيعات
 * Test Sales Statistics - All Sales Data
 * 
 * هذا الملف يجرب الاتصال بقاعدة البيانات ويسترجع جميع المبيعات
 * مع الإحصائيات الأساسية على شكل JSON
 */

// استيراد المكتبات المطلوبة
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// تعيين header للـ JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    // تحميل Laravel bootstrap
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // تحميل Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // إحصائيات أساسية للمبيعات
    $stats = [];
    
    // 1. جميع المبيعات
    $allSales = DB::table('FACTURE_VNT')
        ->select([
            'FCTV_REF',
            'fctv_date',
            'fctv_mnt_ht',
            'fctv_mnt_ttc',
            'fctv_modepaiement',
            'fctv_utilisateur',
            'CLT_REF'
        ])
        ->orderBy('fctv_date', 'desc')
        ->get();
    
    $stats['total_sales'] = $allSales->count();
    $stats['total_amount_ht'] = $allSales->sum('fctv_mnt_ht');
    $stats['total_amount_ttc'] = $allSales->sum('fctv_mnt_ttc');
    $stats['average_ticket'] = $allSales->avg('fctv_mnt_ttc');
    
    // 2. إحصائيات اليوم
    $today = Carbon::today();
    $todaySales = $allSales->filter(function($sale) use ($today) {
        return Carbon::parse($sale->fctv_date)->isToday();
    });
    
    $stats['today'] = [
        'count' => $todaySales->count(),
        'amount_ttc' => $todaySales->sum('fctv_mnt_ttc'),
        'average_ticket' => $todaySales->avg('fctv_mnt_ttc') ?: 0
    ];
    
    // 3. إحصائيات هذا الشهر
    $thisMonth = Carbon::now()->startOfMonth();
    $monthSales = $allSales->filter(function($sale) use ($thisMonth) {
        return Carbon::parse($sale->fctv_date)->greaterThanOrEqualTo($thisMonth);
    });
    
    $stats['this_month'] = [
        'count' => $monthSales->count(),
        'amount_ttc' => $monthSales->sum('fctv_mnt_ttc'),
        'average_ticket' => $monthSales->avg('fctv_mnt_ttc') ?: 0
    ];
    
    // 4. أفضل الأيام (آخر 30 يوم)
    $last30Days = Carbon::now()->subDays(30);
    $recentSales = $allSales->filter(function($sale) use ($last30Days) {
        return Carbon::parse($sale->fctv_date)->greaterThanOrEqualTo($last30Days);
    });
    
    $salesByDay = $recentSales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->format('Y-m-d');
    })->map(function($group) {
        return [
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc')
        ];
    })->sortByDesc('amount')->take(10);
    
    $stats['top_days_last_30'] = $salesByDay;
    
    // 5. توزيع وسائل الدفع
    $paymentMethods = $allSales->groupBy('fctv_modepaiement')->map(function($group) {
        return [
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc'),
            'percentage' => 0 // سيتم حسابها لاحقاً
        ];
    });
    
    $totalAmount = $allSales->sum('fctv_mnt_ttc');
    foreach ($paymentMethods as $method => $data) {
        $paymentMethods[$method]['percentage'] = $totalAmount > 0 ? 
            round(($data['amount'] / $totalAmount) * 100, 2) : 0;
    }
    
    $stats['payment_methods'] = $paymentMethods;
    
    // 6. المبيعات بالساعة (اليوم)
    $hourlyToday = $todaySales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->format('H');
    })->map(function($group) {
        return [
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc')
        ];
    });
    
    $stats['hourly_today'] = $hourlyToday;
    
    // 7. أفضل المستخدمين/الكاشيرين
    $topCashiers = $allSales->groupBy('fctv_utilisateur')->map(function($group) {
        return [
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc')
        ];
    })->sortByDesc('amount')->take(10);
    
    $stats['top_cashiers'] = $topCashiers;
    
    // 8. تفاصيل المبيعات (آخر 50 فاتورة)
    $recentSalesDetails = $allSales->take(50)->map(function($sale) {
        return [
            'reference' => $sale->FCTV_REF,
            'date' => $sale->fctv_date,
            'amount_ht' => floatval($sale->fctv_mnt_ht),
            'amount_ttc' => floatval($sale->fctv_mnt_ttc),
            'payment_method' => $sale->fctv_modepaiement,
            'cashier' => $sale->fctv_utilisateur,
            'customer_ref' => $sale->CLT_REF
        ];
    });
    
    $stats['recent_sales'] = $recentSalesDetails;
    
    // 9. معلومات قاعدة البيانات
    $dbInfo = [
        'total_tables' => [
            'FACTURE_VNT' => DB::table('FACTURE_VNT')->count(),
            'FACTURE_VNT_DETAIL' => DB::table('FACTURE_VNT_DETAIL')->count(),
            'ARTICLE' => DB::table('ARTICLE')->count(),
            'CLIENT' => DB::table('CLIENT')->count(),
            'UTILISATEUR' => DB::table('UTILISATEUR')->count()
        ],
        'first_sale_date' => $allSales->min('fctv_date'),
        'last_sale_date' => $allSales->max('fctv_date')
    ];
    
    $stats['database_info'] = $dbInfo;
    
    // 10. ملخص سريع
    $quickSummary = [
        'message' => 'البيانات تم استرجاعها بنجاح',
        'timestamp' => Carbon::now()->toDateTimeString(),
        'total_records' => $stats['total_sales'],
        'status' => 'success'
    ];
    
    // النتيجة النهائية
    $result = [
        'status' => 'success',
        'summary' => $quickSummary,
        'statistics' => $stats
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // في حالة وجود خطأ
    $error = [
        'status' => 'error',
        'message' => 'خطأ في الاتصال بقاعدة البيانات أو معالجة البيانات',
        'error_details' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
