<?php
/**
 * تقارير مالية ومبيعات شاملة - AccessPOS Pro
 * Comprehensive Financial and Sales Reports
 * 
 * استخلاص جميع التقارير المعقولة من قاعدة البيانات الحقيقية
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    // تحميل Laravel bootstrap لقراءة .env
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // تحميل Laravel app
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // قراءة إعدادات قاعدة البيانات من .env
    $dbConnection = config('database.default');
    $dbConfig = config("database.connections.{$dbConnection}");
    
    $pdo = null;
    $dbPath = null;
    
    // إنشاء الاتصال حسب نوع قاعدة البيانات
    if ($dbConnection === 'sqlite') {
        $dbPath = $dbConfig['database'];
        
        if (!file_exists($dbPath)) {
            throw new Exception("ملف قاعدة البيانات غير موجود: {$dbPath}");
        }
        
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='FACTURE_VNT'")->fetch();
        if (!$result) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
    } elseif ($dbConnection === 'sqlsrv') {
        // إعداد SQL Server
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات
        $result = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FACTURE_VNT'")->fetch();
        if (!$result) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
        
        $dbPath = "SQL Server: {$host}:{$port}/{$database}";
    } else {
        // دعم قواعد البيانات الأخرى (MySQL, PostgreSQL, etc.)
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        $dsn = "{$dbConnection}:host={$host};port={$port};dbname={$database}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // التحقق من وجود جدول المبيعات (للقواعد التي تدعم information_schema)
        try {
            $result = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'FACTURE_VNT'")->fetch();
            if (!$result) {
                // محاولة بديلة للتحقق من الجدول
                $result = $pdo->query("SELECT 1 FROM FACTURE_VNT LIMIT 1")->fetch();
            }
        } catch (Exception $e) {
            throw new Exception('جدول المبيعات FACTURE_VNT غير موجود في قاعدة البيانات');
        }
        
        $dbPath = "{$dbConnection}: {$host}:{$port}/{$database}";
    }
    
    // =================================================================
    // 1. التقارير المالية الأساسية
    // =================================================================
    
    // إجمالي الإحصائيات
    $basicStatsQuery = "SELECT 
        COUNT(*) as total_invoices,
        SUM(fctv_mnt_ht) as total_ht,
        SUM(fctv_mnt_ttc) as total_ttc,
        AVG(fctv_mnt_ttc) as avg_ticket,
        MIN(fctv_date) as first_sale,
        MAX(fctv_date) as last_sale
        FROM FACTURE_VNT";
    $basicStats = $pdo->query($basicStatsQuery)->fetch(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 2. تقارير المبيعات حسب الوقت
    // =================================================================
    
    // مبيعات اليوم
    $todayQuery = "SELECT 
        COUNT(*) as count,
        COALESCE(SUM(fctv_mnt_ttc), 0) as amount,
        COALESCE(AVG(fctv_mnt_ttc), 0) as avg_ticket
        FROM FACTURE_VNT 
        WHERE CAST(fctv_date AS DATE) = CAST(GETDATE() AS DATE)";
    $todayStats = $pdo->query($todayQuery)->fetch(PDO::FETCH_ASSOC);
    
    // مبيعات آخر 7 أيام
    $last7DaysQuery = "SELECT 
        CAST(fctv_date AS DATE) as sale_date,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        WHERE fctv_date >= DATEADD(day, -7, GETDATE())
        GROUP BY CAST(fctv_date AS DATE)
        ORDER BY sale_date DESC";
    $last7Days = $pdo->query($last7DaysQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // مبيعات آخر 30 يوم
    $last30DaysQuery = "SELECT 
        CAST(fctv_date AS DATE) as sale_date,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        WHERE fctv_date >= DATEADD(day, -30, GETDATE())
        GROUP BY CAST(fctv_date AS DATE)
        ORDER BY sale_date DESC";
    $last30Days = $pdo->query($last30DaysQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // المبيعات حسب الشهر
    $monthlyQuery = "SELECT 
        FORMAT(fctv_date, 'yyyy-MM') as month,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        WHERE fctv_date >= DATEADD(month, -12, GETDATE())
        GROUP BY FORMAT(fctv_date, 'yyyy-MM')
        ORDER BY month DESC";
    $monthlySales = $pdo->query($monthlyQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // المبيعات حسب السنة
    $yearlyQuery = "SELECT 
        YEAR(fctv_date) as year,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        GROUP BY YEAR(fctv_date)
        ORDER BY year DESC";
    $yearlySales = $pdo->query($yearlyQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 3. تقارير المبيعات حسب الساعة
    // =================================================================
    
    // أفضل الساعات في اليوم
    $hourlyQuery = "SELECT 
        DATEPART(hour, fctv_date) as hour,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        GROUP BY DATEPART(hour, fctv_date)
        ORDER BY amount DESC";
    $hourlySales = $pdo->query($hourlyQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // أفضل أيام الأسبوع
    $weekdayQuery = "SELECT 
        CASE DATEPART(weekday, fctv_date)
            WHEN 1 THEN 'الأحد'
            WHEN 2 THEN 'الإثنين'
            WHEN 3 THEN 'الثلاثاء'
            WHEN 4 THEN 'الأربعاء'
            WHEN 5 THEN 'الخميس'
            WHEN 6 THEN 'الجمعة'
            WHEN 7 THEN 'السبت'
        END as weekday,
        DATEPART(weekday, fctv_date) as weekday_num,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        GROUP BY DATEPART(weekday, fctv_date)
        ORDER BY amount DESC";
    $weekdaySales = $pdo->query($weekdayQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 4. تقارير الكاشيرين والموظفين
    // =================================================================
    
    // أداء الكاشيرين
    $cashierQuery = "SELECT 
        fctv_utilisateur as cashier,
        COUNT(*) as total_transactions,
        SUM(fctv_mnt_ttc) as total_amount,
        AVG(fctv_mnt_ttc) as avg_ticket,
        MIN(fctv_date) as first_sale,
        MAX(fctv_date) as last_sale
        FROM FACTURE_VNT 
        GROUP BY fctv_utilisateur
        ORDER BY total_amount DESC";
    $cashierPerformance = $pdo->query($cashierQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // أداء الكاشيرين في آخر 30 يوم
    $cashierRecentQuery = "SELECT 
        fctv_utilisateur as cashier,
        COUNT(*) as transactions_30d,
        SUM(fctv_mnt_ttc) as amount_30d,
        AVG(fctv_mnt_ttc) as avg_ticket_30d
        FROM FACTURE_VNT 
        WHERE fctv_date >= DATE('now', '-30 days')
        GROUP BY fctv_utilisateur
        ORDER BY amount_30d DESC";
    $cashierRecent = $pdo->query($cashierRecentQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 5. تقارير وسائل الدفع
    // =================================================================
    
    // توزيع وسائل الدفع
    $paymentMethodsQuery = "SELECT 
        fctv_modepaiement as payment_method,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket,
        ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM FACTURE_VNT)), 2) as percentage_count,
        ROUND((SUM(fctv_mnt_ttc) * 100.0 / (SELECT SUM(fctv_mnt_ttc) FROM FACTURE_VNT)), 2) as percentage_amount
        FROM FACTURE_VNT 
        GROUP BY fctv_modepaiement
        ORDER BY amount DESC";
    $paymentMethods = $pdo->query($paymentMethodsQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 6. تقارير العملاء
    // =================================================================
    
    // أفضل العملاء
    $topCustomersQuery = "SELECT 
        CLT_REF as customer_ref,
        COUNT(*) as total_orders,
        SUM(fctv_mnt_ttc) as total_spent,
        AVG(fctv_mnt_ttc) as avg_order_value,
        MIN(fctv_date) as first_order,
        MAX(fctv_date) as last_order
        FROM FACTURE_VNT 
        WHERE CLT_REF IS NOT NULL AND CLT_REF != '0'
        GROUP BY CLT_REF
        ORDER BY total_spent DESC
        LIMIT 20";
    $topCustomers = $pdo->query($topCustomersQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 7. تقارير الفترات المقارنة
    // =================================================================
    
    // مقارنة هذا الشهر مع الشهر الماضي
    $thisMonthQuery = "SELECT 
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        WHERE YEAR(fctv_date) = YEAR(GETDATE()) AND MONTH(fctv_date) = MONTH(GETDATE())";
    $thisMonth = $pdo->query($thisMonthQuery)->fetch(PDO::FETCH_ASSOC);
    
    $lastMonthQuery = "SELECT 
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        WHERE YEAR(fctv_date) = YEAR(DATEADD(month, -1, GETDATE())) 
        AND MONTH(fctv_date) = MONTH(DATEADD(month, -1, GETDATE()))";
    $lastMonth = $pdo->query($lastMonthQuery)->fetch(PDO::FETCH_ASSOC);
    
    // حساب النمو
    $monthlyGrowth = [
        'sales_growth' => $lastMonth['amount'] > 0 ? 
            round((($thisMonth['amount'] - $lastMonth['amount']) / $lastMonth['amount']) * 100, 2) : 0,
        'transactions_growth' => $lastMonth['count'] > 0 ? 
            round((($thisMonth['count'] - $lastMonth['count']) / $lastMonth['count']) * 100, 2) : 0,
        'avg_ticket_growth' => $lastMonth['avg_ticket'] > 0 ? 
            round((($thisMonth['avg_ticket'] - $lastMonth['avg_ticket']) / $lastMonth['avg_ticket']) * 100, 2) : 0
    ];
    
    // =================================================================
    // 8. تقارير متقدمة وإحصائيات
    // =================================================================
    
    // أفضل الأيام في التاريخ
    $bestDaysQuery = "SELECT 
        DATE(fctv_date) as sale_date,
        strftime('%w', fctv_date) as weekday,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        GROUP BY DATE(fctv_date)
        ORDER BY amount DESC
        LIMIT 10";
    $bestDays = $pdo->query($bestDaysQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // أسوأ الأيام
    $worstDaysQuery = "SELECT 
        DATE(fctv_date) as sale_date,
        strftime('%w', fctv_date) as weekday,
        COUNT(*) as count,
        SUM(fctv_mnt_ttc) as amount,
        AVG(fctv_mnt_ttc) as avg_ticket
        FROM FACTURE_VNT 
        GROUP BY DATE(fctv_date)
        ORDER BY amount ASC
        LIMIT 10";
    $worstDays = $pdo->query($worstDaysQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // الإحصائيات المتقدمة
    $advancedStatsQuery = "SELECT 
        COUNT(DISTINCT CLT_REF) as unique_customers,
        COUNT(DISTINCT fctv_utilisateur) as unique_cashiers,
        COUNT(DISTINCT fctv_modepaiement) as payment_methods_count,
        COUNT(DISTINCT DATE(fctv_date)) as active_days,
        MAX(fctv_mnt_ttc) as highest_sale,
        MIN(fctv_mnt_ttc) as lowest_sale
        FROM FACTURE_VNT";
    $advancedStats = $pdo->query($advancedStatsQuery)->fetch(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 9. تقارير الاتجاهات والأنماط
    // =================================================================
    
    // الاتجاه العام للمبيعات (آخر 90 يوم)
    $trendQuery = "SELECT 
        DATE(fctv_date) as sale_date,
        SUM(fctv_mnt_ttc) as daily_amount
        FROM FACTURE_VNT 
        WHERE fctv_date >= DATE('now', '-90 days')
        GROUP BY DATE(fctv_date)
        ORDER BY sale_date";
    $salesTrend = $pdo->query($trendQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // =================================================================
    // 10. ملخص شامل
    // =================================================================
    
    $comprehensiveSummary = [
        'period_analyzed' => [
            'from' => $basicStats['first_sale'],
            'to' => $basicStats['last_sale'],
            'total_days' => round((strtotime($basicStats['last_sale']) - strtotime($basicStats['first_sale'])) / (60*60*24))
        ],
        'financial_overview' => [
            'total_revenue' => number_format($basicStats['total_ttc'], 2),
            'total_revenue_ht' => number_format($basicStats['total_ht'], 2),
            'total_transactions' => number_format($basicStats['total_invoices']),
            'average_transaction_value' => number_format($basicStats['avg_ticket'], 2),
            'daily_average' => number_format($basicStats['total_ttc'] / max(1, round((strtotime($basicStats['last_sale']) - strtotime($basicStats['first_sale'])) / (60*60*24))), 2)
        ],
        'business_insights' => [
            'best_performing_cashier' => $cashierPerformance[0] ?? null,
            'most_popular_payment_method' => $paymentMethods[0] ?? null,
            'best_day_of_week' => $weekdaySales[0] ?? null,
            'peak_hour' => $hourlySales[0] ?? null,
            'monthly_growth_rate' => $monthlyGrowth
        ]
    ];
    
    // النتيجة النهائية الشاملة
    $result = [
        'status' => 'success',
        'message' => 'تم استخلاص جميع التقارير المالية والمبيعات بنجاح',
        'generated_at' => date('Y-m-d H:i:s'),
        'database_info' => [
            'path' => $dbPath,
            'size_mb' => number_format(filesize($dbPath) / 1024 / 1024, 2)
        ],
        
        // التقارير الأساسية
        'basic_statistics' => $basicStats,
        'comprehensive_summary' => $comprehensiveSummary,
        
        // تقارير الوقت
        'time_based_reports' => [
            'today' => $todayStats,
            'last_7_days' => $last7Days,
            'last_30_days' => $last30Days,
            'monthly_breakdown' => $monthlySales,
            'yearly_breakdown' => $yearlySales,
            'hourly_performance' => $hourlySales,
            'weekday_performance' => $weekdaySales,
            'sales_trend_90_days' => $salesTrend
        ],
        
        // تقارير الأداء
        'performance_reports' => [
            'cashier_performance' => $cashierPerformance,
            'recent_cashier_performance' => $cashierRecent,
            'payment_methods_analysis' => $paymentMethods,
            'top_customers' => $topCustomers
        ],
        
        // تقارير المقارنة
        'comparison_reports' => [
            'this_month_vs_last_month' => [
                'current_month' => $thisMonth,
                'previous_month' => $lastMonth,
                'growth_metrics' => $monthlyGrowth
            ],
            'best_days_ever' => $bestDays,
            'worst_days_ever' => $worstDays
        ],
        
        // إحصائيات متقدمة
        'advanced_analytics' => [
            'business_metrics' => $advancedStats,
            'seasonal_patterns' => [
                'note' => 'يمكن تحليل الأنماط الموسمية بناءً على البيانات المتاحة'
            ]
        ]
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $error = [
        'status' => 'error',
        'message' => 'خطأ في استخلاص التقارير',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'suggestion' => 'تأكد من وجود قاعدة البيانات وصحة المسار'
    ];
    
    http_response_code(500);
    echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
