<?php
/**
 * ملف اختبار الإحصائيات - جميع المبيعات
 * Test Sales Statistics - All Sales Data
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// استيراد Laravel
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    // تحميل Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // بدء جمع الإحصائيات
    $stats = [];
    
    // 1. معلومات قاعدة البيانات الأساسية
    $stats['database_info'] = [
        'connection_status' => 'متصل بنجاح',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // 2. جميع المبيعات
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
    
    // 3. إحصائيات أساسية
    $stats['basic_statistics'] = [
        'total_sales_count' => $allSales->count(),
        'total_amount_ht' => number_format($allSales->sum('fctv_mnt_ht'), 2),
        'total_amount_ttc' => number_format($allSales->sum('fctv_mnt_ttc'), 2),
        'average_ticket' => number_format($allSales->avg('fctv_mnt_ttc'), 2),
        'first_sale_date' => $allSales->min('fctv_date'),
        'last_sale_date' => $allSales->max('fctv_date')
    ];
    
    // 4. إحصائيات اليوم
    $today = date('Y-m-d');
    $todaySales = $allSales->filter(function($sale) use ($today) {
        return substr($sale->fctv_date, 0, 10) === $today;
    });
    
    $stats['today_statistics'] = [
        'count' => $todaySales->count(),
        'amount_ttc' => number_format($todaySales->sum('fctv_mnt_ttc'), 2),
        'average_ticket' => $todaySales->count() > 0 ? number_format($todaySales->avg('fctv_mnt_ttc'), 2) : '0.00'
    ];
    
    // 5. توزيع وسائل الدفع
    $paymentMethods = [];
    $totalAmount = $allSales->sum('fctv_mnt_ttc');
    
    foreach ($allSales->groupBy('fctv_modepaiement') as $method => $sales) {
        $amount = $sales->sum('fctv_mnt_ttc');
        $paymentMethods[$method] = [
            'count' => $sales->count(),
            'amount' => number_format($amount, 2),
            'percentage' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0
        ];
    }
    
    $stats['payment_methods'] = $paymentMethods;
    
    // 6. أفضل الكاشيرين
    $topCashiers = [];
    foreach ($allSales->groupBy('fctv_utilisateur') as $cashier => $sales) {
        $topCashiers[$cashier] = [
            'count' => $sales->count(),
            'amount' => number_format($sales->sum('fctv_mnt_ttc'), 2)
        ];
    }
    
    // ترتيب حسب المبلغ
    uasort($topCashiers, function($a, $b) {
        return floatval(str_replace(',', '', $b['amount'])) <=> floatval(str_replace(',', '', $a['amount']));
    });
    
    $stats['top_cashiers'] = array_slice($topCashiers, 0, 10, true);
    
    // 7. المبيعات حسب الشهر (آخر 12 شهر)
    $monthlySales = [];
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-{$i} months"));
        $monthSales = $allSales->filter(function($sale) use ($month) {
            return substr($sale->fctv_date, 0, 7) === $month;
        });
        
        $monthlySales[$month] = [
            'count' => $monthSales->count(),
            'amount' => number_format($monthSales->sum('fctv_mnt_ttc'), 2)
        ];
    }
    
    $stats['monthly_sales'] = $monthlySales;
    
    // 8. عينة من المبيعات الحديثة (آخر 20 فاتورة)
    $recentSales = $allSales->take(20)->map(function($sale) {
        return [
            'reference' => $sale->FCTV_REF,
            'date' => $sale->fctv_date,
            'amount_ht' => number_format($sale->fctv_mnt_ht, 2),
            'amount_ttc' => number_format($sale->fctv_mnt_ttc, 2),
            'payment_method' => $sale->fctv_modepaiement,
            'cashier' => $sale->fctv_utilisateur,
            'customer_ref' => $sale->CLT_REF
        ];
    });
    
    $stats['recent_sales_sample'] = $recentSales;
    
    // 9. معلومات الجداول
    $tableInfo = [
        'FACTURE_VNT' => DB::table('FACTURE_VNT')->count(),
        'FACTURE_VNT_DETAIL' => DB::table('FACTURE_VNT_DETAIL')->count(),
        'ARTICLE' => DB::table('ARTICLE')->count(),
        'CLIENT' => DB::table('CLIENT')->count(),
        'UTILISATEUR' => DB::table('UTILISATEUR')->count()
    ];
    
    $stats['table_counts'] = $tableInfo;
    
    // النتيجة النهائية
    $result = [
        'status' => 'success',
        'message' => 'تم استرجاع إحصائيات المبيعات بنجاح',
        'generated_at' => date('Y-m-d H:i:s'),
        'data' => $stats
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $error = [
        'status' => 'error',
        'message' => 'خطأ في معالجة البيانات',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
