<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 اختبار استعلامات لوحة القيادة مع تاريخ محدد...\n";
echo "====================================================\n";

// استخدام التاريخ المطلوب بشكل صريح
$today = '2025-07-09';
echo "📅 التاريخ المطلوب: $today\n\n";

echo "1️⃣ استعلام بسيط بـ whereDate:\n";
$invoices1 = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->count();
$sales1 = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->sum('FCTV_MNT_TTC') ?? 0;
echo "   فواتير: {$invoices1}\n";
echo "   مبيعات: " . number_format($sales1, 2) . " DH\n\n";

echo "2️⃣ استعلام مع between:\n";
$startOfDay = $today . ' 00:00:00';
$endOfDay = $today . ' 23:59:59';
$invoices2 = DB::table('FACTURE_VNT')->whereBetween('FCTV_DATE', [$startOfDay, $endOfDay])->count();
$sales2 = DB::table('FACTURE_VNT')->whereBetween('FCTV_DATE', [$startOfDay, $endOfDay])->sum('FCTV_MNT_TTC') ?? 0;
echo "   فواتير: {$invoices2}\n";
echo "   مبيعات: " . number_format($sales2, 2) . " DH\n\n";

echo "3️⃣ استعلام مع like:\n";
$invoices3 = DB::table('FACTURE_VNT')->where('FCTV_DATE', 'like', $today . '%')->count();
$sales3 = DB::table('FACTURE_VNT')->where('FCTV_DATE', 'like', $today . '%')->sum('FCTV_MNT_TTC') ?? 0;
echo "   فواتير: {$invoices3}\n";
echo "   مبيعات: " . number_format($sales3, 2) . " DH\n\n";

echo "4️⃣ مدفوعات اليوم:\n";
$payments1 = DB::table('REGLEMENT')->whereDate('REG_DATE', $today)->count();
$paymentsAmount1 = DB::table('REGLEMENT')->whereDate('REG_DATE', $today)->sum('REG_MONTANT') ?? 0;
echo "   مدفوعات: {$payments1}\n";
echo "   مبلغ: " . number_format($paymentsAmount1, 2) . " DH\n\n";

echo "5️⃣ مصروفات اليوم:\n";
$expenses1 = DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->count();
$expensesAmount1 = DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->sum('DEP_MONTANTHT') ?? 0;
echo "   مصروفات: {$expenses1}\n";
echo "   مبلغ: " . number_format($expensesAmount1, 2) . " DH\n\n";

echo "6️⃣ أكثر المنتجات مبيعاً اليوم:\n";
$topProducts = DB::table('FACTURE_VNT_DETAIL as fvd')
    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
    ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
    ->whereDate('fv.FCTV_DATE', $today)
    ->select('a.ART_DESIGNATION', DB::raw('SUM(fvd.FVD_QTE) as total_qty'), DB::raw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as total_amount'))
    ->groupBy('a.ART_DESIGNATION')
    ->orderBy('total_qty', 'desc')
    ->limit(5)
    ->get();

foreach ($topProducts as $product) {
    echo "   🏆 " . $product->ART_DESIGNATION . ": " . $product->total_qty . " قطعة - " . number_format($product->total_amount, 2) . " DH\n";
}

echo "\n7️⃣ توزيع المبيعات حسب الساعة:\n";
$salesByHour = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $today)
    ->select(DB::raw('DATEPART(HOUR, FCTV_DATE) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(FCTV_MNT_TTC) as amount'))
    ->groupBy(DB::raw('DATEPART(HOUR, FCTV_DATE)'))
    ->orderBy('hour')
    ->limit(10)
    ->get();

foreach ($salesByHour as $hour) {
    echo "   🕐 الساعة " . sprintf('%02d', $hour->hour) . ":00 - " . $hour->count . " فاتورة - " . number_format($hour->amount, 2) . " DH\n";
}

echo "\n8️⃣ إحصائيات إضافية:\n";
if ($invoices1 > 0) {
    echo "   📈 متوسط الفاتورة: " . number_format($sales1 / $invoices1, 2) . " DH\n";
    echo "   💰 صافي الربح (تقديري): " . number_format($sales1 - $expensesAmount1, 2) . " DH\n";
}

echo "\n✅ جميع البيانات جاهزة لتظهر في لوحة القيادة!\n";
