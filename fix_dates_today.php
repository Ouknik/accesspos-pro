<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 إصلاح تواريخ البيانات لليوم الحالي...\n";
echo "===============================================\n";

$today = Carbon::now()->format('Y-m-d');
$todayWithTime = Carbon::now()->format('Y-m-d H:i:s.v');

echo "📅 التاريخ الحالي: $today\n";
echo "🕐 التاريخ مع الوقت: $todayWithTime\n\n";

// البحث عن جميع الفواتير التي ليست لليوم الحالي
$invoices = DB::table('FACTURE_VNT')
    ->whereNotNull('FCTV_DATE')
    ->get();

echo "📋 العثور على " . count($invoices) . " فاتورة\n";

$updatedInvoices = 0;
foreach ($invoices as $invoice) {
    // استخراج الوقت من التاريخ الأصلي
    $originalDate = $invoice->FCTV_DATE;
    $time = '12:00:00.000'; // وقت افتراضي
    
    if ($originalDate) {
        // محاولة استخراج الوقت من التاريخ الأصلي
        $parts = explode(' ', $originalDate);
        if (count($parts) > 1) {
            $time = $parts[1];
        }
    }
    
    $newDateTime = $today . ' ' . $time;
    
    DB::table('FACTURE_VNT')
        ->where('FCTV_REF', $invoice->FCTV_REF)
        ->update(['FCTV_DATE' => $newDateTime]);
    
    $updatedInvoices++;
}

echo "✅ تم تحديث {$updatedInvoices} فاتورة\n";

// تحديث المدفوعات
$payments = DB::table('REGLEMENT')
    ->whereNotNull('REG_DATE')
    ->get();

echo "💳 العثور على " . count($payments) . " مدفوعة\n";

$updatedPayments = 0;
foreach ($payments as $payment) {
    $originalDate = $payment->REG_DATE;
    $time = '12:00:00.000';
    
    if ($originalDate) {
        $parts = explode(' ', $originalDate);
        if (count($parts) > 1) {
            $time = $parts[1];
        }
    }
    
    $newDateTime = $today . ' ' . $time;
    
    DB::table('REGLEMENT')
        ->where('REG_REF', $payment->REG_REF)
        ->update(['REG_DATE' => $newDateTime]);
    
    $updatedPayments++;
}

echo "✅ تم تحديث {$updatedPayments} مدفوعة\n";

// تحديث المصروفات
$expenses = DB::table('DEPENSE')
    ->whereNotNull('DEP_DATE')
    ->get();

echo "💰 العثور على " . count($expenses) . " مصروف\n";

$updatedExpenses = 0;
foreach ($expenses as $expense) {
    $originalDate = $expense->DEP_DATE;
    $time = '12:00:00.000';
    
    if ($originalDate) {
        $parts = explode(' ', $originalDate);
        if (count($parts) > 1) {
            $time = $parts[1];
        }
    }
    
    $newDateTime = $today . ' ' . $time;
    
    DB::table('DEPENSE')
        ->where('DEP_REF', $expense->DEP_REF)
        ->update(['DEP_DATE' => $newDateTime]);
    
    $updatedExpenses++;
}

echo "✅ تم تحديث {$updatedExpenses} مصروف\n";

// تحديث الحجوزات
$reservations = DB::table('RESERVATION')
    ->whereNotNull('DATE_RESERVATION')
    ->get();

echo "🏠 العثور على " . count($reservations) . " حجز\n";

$updatedReservations = 0;
foreach ($reservations as $reservation) {
    $originalDate = $reservation->DATE_RESERVATION;
    $time = '12:00:00.000';
    
    if ($originalDate) {
        $parts = explode(' ', $originalDate);
        if (count($parts) > 1) {
            $time = $parts[1];
        }
    }
    
    $newDateTime = $today . ' ' . $time;
    
    DB::table('RESERVATION')
        ->where('RES_REF', $reservation->RES_REF)
        ->update(['DATE_RESERVATION' => $newDateTime]);
    
    $updatedReservations++;
}

echo "✅ تم تحديث {$updatedReservations} حجز\n";

echo "\n🎉 تم إصلاح جميع التواريخ لليوم الحالي!\n";
echo "📊 إجمالي التحديثات:\n";
echo "   - فواتير: {$updatedInvoices}\n";
echo "   - مدفوعات: {$updatedPayments}\n";
echo "   - مصروفات: {$updatedExpenses}\n";
echo "   - حجوزات: {$updatedReservations}\n";
