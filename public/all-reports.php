<?php
/**
 * تقارير مالية ومبيعات شاملة - AccessPOS Pro
 * Comprehensive Financial and Sales Reports
 * 
 * استخلاص جميع التقارير المعقولة من قاعدة البيانات الحقيقية
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// استيراد Laravel (نفس طريقة sales-stats-api.php)
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    // تحميل Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // =================================================================
    // 1. جلب جميع المبيعات (مثل sales-stats-api.php)
    // =================================================================
    
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
    
    // =================================================================
    // 2. الإحصائيات المالية الأساسية
    // =================================================================
    
    $basicStats = [
        'total_invoices' => $allSales->count(),
        'total_amount_ht' => $allSales->sum('fctv_mnt_ht'),
        'total_amount_ttc' => $allSales->sum('fctv_mnt_ttc'),
        'average_ticket' => $allSales->avg('fctv_mnt_ttc'),
        'first_sale_date' => $allSales->min('fctv_date'),
        'last_sale_date' => $allSales->max('fctv_date')
    ];
    
    // =================================================================
    // 3. تقارير المبيعات حسب الوقت
    // =================================================================
    
    // مبيعات اليوم
    $today = Carbon::today();
    $todaySales = $allSales->filter(function($sale) use ($today) {
        return Carbon::parse($sale->fctv_date)->isToday();
    });
    
    $todayStats = [
        'count' => $todaySales->count(),
        'amount_ttc' => $todaySales->sum('fctv_mnt_ttc'),
        'average_ticket' => $todaySales->count() > 0 ? $todaySales->avg('fctv_mnt_ttc') : 0
    ];
    
    // مبيعات آخر 7 أيام
    $last7Days = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $daySales = $allSales->filter(function($sale) use ($date) {
            return Carbon::parse($sale->fctv_date)->isSameDay($date);
        });
        
        $last7Days[] = [
            'date' => $date->format('Y-m-d'),
            'weekday' => $date->locale('ar')->dayName,
            'count' => $daySales->count(),
            'amount' => $daySales->sum('fctv_mnt_ttc'),
            'avg_ticket' => $daySales->count() > 0 ? $daySales->avg('fctv_mnt_ttc') : 0
        ];
    }
    
    // مبيعات آخر 30 يوم
    $last30Days = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $daySales = $allSales->filter(function($sale) use ($date) {
            return Carbon::parse($sale->fctv_date)->isSameDay($date);
        });
        
        $last30Days[] = [
            'date' => $date->format('Y-m-d'),
            'count' => $daySales->count(),
            'amount' => $daySales->sum('fctv_mnt_ttc')
        ];
    }
    
    // المبيعات حسب الشهر
    $monthlySales = $allSales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->format('Y-m');
    })->map(function($group, $month) {
        return [
            'month' => $month,
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket' => $group->avg('fctv_mnt_ttc')
        ];
    })->sortByDesc('month')->take(12);
    
    // =================================================================
    // 4. تقارير المبيعات حسب الساعة
    // =================================================================
    
    $hourlySales = $allSales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->format('H');
    })->map(function($group, $hour) {
        return [
            'hour' => intval($hour),
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket' => $group->avg('fctv_mnt_ttc')
        ];
    })->sortBy('hour');
    
    // أفضل أيام الأسبوع
    $weekdaySales = $allSales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->dayOfWeek;
    })->map(function($group, $weekday) {
        $weekdayNames = [
            0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء',
            4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'
        ];
        
        return [
            'weekday' => $weekdayNames[$weekday] ?? 'غير محدد',
            'weekday_num' => $weekday,
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket' => $group->avg('fctv_mnt_ttc')
        ];
    })->sortByDesc('amount');
    
    // =================================================================
    // 5. تقارير الكاشيرين والموظفين
    // =================================================================
    
    $cashierPerformance = $allSales->groupBy('fctv_utilisateur')->map(function($group, $cashier) {
        return [
            'cashier' => $cashier,
            'total_transactions' => $group->count(),
            'total_amount' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket' => $group->avg('fctv_mnt_ttc'),
            'first_sale' => $group->min('fctv_date'),
            'last_sale' => $group->max('fctv_date')
        ];
    })->sortByDesc('total_amount');
    
    // أداء الكاشيرين في آخر 30 يوم
    $last30DaysDate = Carbon::now()->subDays(30);
    $recentSales = $allSales->filter(function($sale) use ($last30DaysDate) {
        return Carbon::parse($sale->fctv_date)->greaterThanOrEqualTo($last30DaysDate);
    });
    
    $cashierRecent = $recentSales->groupBy('fctv_utilisateur')->map(function($group, $cashier) {
        return [
            'cashier' => $cashier,
            'transactions_30d' => $group->count(),
            'amount_30d' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket_30d' => $group->avg('fctv_mnt_ttc')
        ];
    })->sortByDesc('amount_30d');
    
    // =================================================================
    // 6. تقارير وسائل الدفع
    // =================================================================
    
    $totalAmount = $allSales->sum('fctv_mnt_ttc');
    $paymentMethods = $allSales->groupBy('fctv_modepaiement')->map(function($group, $method) use ($totalAmount, $allSales) {
        $amount = $group->sum('fctv_mnt_ttc');
        return [
            'payment_method' => $method,
            'count' => $group->count(),
            'amount' => $amount,
            'avg_ticket' => $group->avg('fctv_mnt_ttc'),
            'percentage_count' => $allSales->count() > 0 ? round(($group->count() / $allSales->count()) * 100, 2) : 0,
            'percentage_amount' => $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0
        ];
    })->sortByDesc('amount');
    
    // =================================================================
    // 7. تقارير العملاء
    // =================================================================
    
    $topCustomers = $allSales->where('CLT_REF', '!=', '0')
        ->where('CLT_REF', '!=', null)
        ->groupBy('CLT_REF')->map(function($group, $customer) {
            return [
                'customer_ref' => $customer,
                'total_orders' => $group->count(),
                'total_spent' => $group->sum('fctv_mnt_ttc'),
                'avg_order_value' => $group->avg('fctv_mnt_ttc'),
                'first_order' => $group->min('fctv_date'),
                'last_order' => $group->max('fctv_date')
            ];
        })->sortByDesc('total_spent')->take(20);
    
    // =================================================================
    // 8. تقارير الفترات المقارنة
    // =================================================================
    
    // مقارنة هذا الشهر مع الشهر الماضي
    $thisMonth = $allSales->filter(function($sale) {
        return Carbon::parse($sale->fctv_date)->isCurrentMonth();
    });
    
    $lastMonth = $allSales->filter(function($sale) {
        return Carbon::parse($sale->fctv_date)->month === Carbon::now()->subMonth()->month &&
               Carbon::parse($sale->fctv_date)->year === Carbon::now()->subMonth()->year;
    });
    
    $thisMonthStats = [
        'count' => $thisMonth->count(),
        'amount' => $thisMonth->sum('fctv_mnt_ttc'),
        'avg_ticket' => $thisMonth->avg('fctv_mnt_ttc') ?: 0
    ];
    
    $lastMonthStats = [
        'count' => $lastMonth->count(),
        'amount' => $lastMonth->sum('fctv_mnt_ttc'),
        'avg_ticket' => $lastMonth->avg('fctv_mnt_ttc') ?: 0
    ];
    
    // حساب النمو
    $monthlyGrowth = [
        'sales_growth' => $lastMonthStats['amount'] > 0 ? 
            round((($thisMonthStats['amount'] - $lastMonthStats['amount']) / $lastMonthStats['amount']) * 100, 2) : 0,
        'transactions_growth' => $lastMonthStats['count'] > 0 ? 
            round((($thisMonthStats['count'] - $lastMonthStats['count']) / $lastMonthStats['count']) * 100, 2) : 0,
        'avg_ticket_growth' => $lastMonthStats['avg_ticket'] > 0 ? 
            round((($thisMonthStats['avg_ticket'] - $lastMonthStats['avg_ticket']) / $lastMonthStats['avg_ticket']) * 100, 2) : 0
    ];
    
    // =================================================================
    // 9. أفضل وأسوأ الأيام
    // =================================================================
    
    $dailySales = $allSales->groupBy(function($sale) {
        return Carbon::parse($sale->fctv_date)->format('Y-m-d');
    })->map(function($group, $date) {
        return [
            'sale_date' => $date,
            'weekday' => Carbon::parse($date)->dayOfWeek,
            'count' => $group->count(),
            'amount' => $group->sum('fctv_mnt_ttc'),
            'avg_ticket' => $group->avg('fctv_mnt_ttc')
        ];
    });
    
    $bestDays = $dailySales->sortByDesc('amount')->take(10);
    $worstDays = $dailySales->where('count', '>', 0)->sortBy('amount')->take(10);
    
    // =================================================================
    // 10. إحصائيات متقدمة
    // =================================================================
    
    $advancedStats = [
        'unique_customers' => $allSales->where('CLT_REF', '!=', '0')->where('CLT_REF', '!=', null)->pluck('CLT_REF')->unique()->count(),
        'unique_cashiers' => $allSales->pluck('fctv_utilisateur')->unique()->count(),
        'payment_methods_count' => $allSales->pluck('fctv_modepaiement')->unique()->count(),
        'active_days' => $dailySales->count(),
        'highest_sale' => $allSales->max('fctv_mnt_ttc'),
        'lowest_sale' => $allSales->min('fctv_mnt_ttc')
    ];
    
    // =================================================================
    // 11. ملخص شامل
    // =================================================================
    
    $firstSaleDate = Carbon::parse($basicStats['first_sale_date']);
    $lastSaleDate = Carbon::parse($basicStats['last_sale_date']);
    $totalDays = $firstSaleDate->diffInDays($lastSaleDate);
    
    $comprehensiveSummary = [
        'period_analyzed' => [
            'from' => $basicStats['first_sale_date'],
            'to' => $basicStats['last_sale_date'],
            'total_days' => $totalDays
        ],
        'financial_overview' => [
            'total_revenue' => number_format($basicStats['total_amount_ttc'], 2),
            'total_revenue_ht' => number_format($basicStats['total_amount_ht'], 2),
            'total_transactions' => number_format($basicStats['total_invoices']),
            'average_transaction_value' => number_format($basicStats['average_ticket'], 2),
            'daily_average' => $totalDays > 0 ? number_format($basicStats['total_amount_ttc'] / $totalDays, 2) : '0.00'
        ],
        'business_insights' => [
            'best_performing_cashier' => $cashierPerformance->first(),
            'most_popular_payment_method' => $paymentMethods->first(),
            'best_day_of_week' => $weekdaySales->first(),
            'peak_hour' => $hourlySales->sortByDesc('amount')->first(),
            'monthly_growth_rate' => $monthlyGrowth
        ]
    ];
    
    // =================================================================
    // النتيجة النهائية الشاملة
    // =================================================================
    
    $result = [
        'status' => 'success',
        'message' => 'تم استخلاص جميع التقارير المالية والمبيعات بنجاح',
        'generated_at' => Carbon::now()->toDateTimeString(),
        'database_info' => [
            'connection_status' => 'متصل بنجاح باستخدام إعدادات .env',
            'total_records_analyzed' => $allSales->count()
        ],
        
        // التقارير الأساسية
        'basic_statistics' => $basicStats,
        'comprehensive_summary' => $comprehensiveSummary,
        
        // تقارير الوقت
        'time_based_reports' => [
            'today' => $todayStats,
            'last_7_days' => $last7Days,
            'last_30_days' => $last30Days,
            'monthly_breakdown' => $monthlySales->values(),
            'hourly_performance' => $hourlySales->values(),
            'weekday_performance' => $weekdaySales->values()
        ],
        
        // تقارير الأداء
        'performance_reports' => [
            'cashier_performance' => $cashierPerformance->values(),
            'recent_cashier_performance' => $cashierRecent->values(),
            'payment_methods_analysis' => $paymentMethods->values(),
            'top_customers' => $topCustomers->values()
        ],
        
        // تقارير المقارنة
        'comparison_reports' => [
            'this_month_vs_last_month' => [
                'current_month' => $thisMonthStats,
                'previous_month' => $lastMonthStats,
                'growth_metrics' => $monthlyGrowth
            ],
            'best_days_ever' => $bestDays->values(),
            'worst_days_ever' => $worstDays->values()
        ],
        
        // إحصائيات متقدمة
        'advanced_analytics' => [
            'business_metrics' => $advancedStats
        ]
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $error = [
        'status' => 'error',
        'message' => 'خطأ في استخلاص التقارير',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'timestamp' => Carbon::now()->toDateTimeString()
    ];
    
    http_response_code(500);
    echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
