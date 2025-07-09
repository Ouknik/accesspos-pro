<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 التحقق من البيانات المضافة...\n";
echo "======================================\n";

// التحقق من الفواتير
$yesterday = '2025-07-09'; // تاريخ الأمس
$invoices = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $yesterday)->get();
echo "📋 فواتير $yesterday: " . $invoices->count() . "\n";

if ($invoices->count() > 0) {
    $total = $invoices->sum('FCTV_MNT_TTC');
    echo "💰 إجمالي المبيعات: " . number_format($total, 2) . " DH\n";
    echo "📅 أول فاتورة: " . $invoices->first()->FCTV_DATE . "\n";
    echo "📅 آخر فاتورة: " . $invoices->last()->FCTV_DATE . "\n";
}

// التحقق من اليوم الحالي
$todayActual = '2025-07-09';
echo "\n🔍 التحقق من اليوم الحالي: $todayActual\n";
$invoicesToday = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $todayActual)->get();
echo "📋 فواتير اليوم الحالي: " . $invoicesToday->count() . "\n";

if ($invoicesToday->count() > 0) {
    $totalToday = $invoicesToday->sum('FCTV_MNT_TTC');
    echo "💰 إجمالي مبيعات اليوم: " . number_format($totalToday, 2) . " DH\n";
}

// عد المدفوعات
$paymentsToday = DB::table('REGLEMENT')->whereDate('REG_DATE', $todayActual)->count();
echo "💳 مدفوعات اليوم: $paymentsToday\n";

// عد الحجوزات
$reservationsToday = DB::table('RESERVATION')->whereDate('DATE_RESERVATION', $todayActual)->count();
echo "🏠 حجوزات اليوم: $reservationsToday\n";

// التحقق من جميع الفواتير
$allInvoices = DB::table('FACTURE_VNT')->orderBy('FCTV_DATE', 'desc')->limit(10)->get();
echo "\n📊 آخر 10 فواتير:\n";
foreach ($allInvoices as $invoice) {
    echo "  - {$invoice->FCTV_REF}: {$invoice->FCTV_DATE} - " . number_format($invoice->FCTV_MNT_TTC, 2) . " DH\n";
}
