<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 تحديث تواريخ البيانات لليوم الحالي...\n";
echo "=============================================\n";

$yesterday = '2025-07-08';
$today = '2025-07-09';

echo "📅 من: $yesterday\n";
echo "📅 إلى: $today\n\n";

// تحديث تواريخ الفواتير
$updated = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $yesterday)
    ->update([
        'FCTV_DATE' => DB::raw("REPLACE(CAST(FCTV_DATE AS VARCHAR), '2025-07-08', '2025-07-09')")
    ]);
    
echo "✅ تم تحديث {$updated} فاتورة\n";

// تحديث تواريخ المدفوعات
$updatedPayments = DB::table('REGLEMENT')
    ->whereDate('REG_DATE', $yesterday)
    ->update([
        'REG_DATE' => DB::raw("REPLACE(CAST(REG_DATE AS VARCHAR), '2025-07-08', '2025-07-09')")
    ]);
    
echo "✅ تم تحديث {$updatedPayments} دفعة\n";

// تحديث تواريخ المصروفات
$updatedExpenses = DB::table('DEPENSE')
    ->whereDate('DEP_DATE', $yesterday)
    ->update([
        'DEP_DATE' => DB::raw("REPLACE(CAST(DEP_DATE AS VARCHAR), '2025-07-08', '2025-07-09')")
    ]);
    
echo "✅ تم تحديث {$updatedExpenses} مصروف\n";

// تحديث تواريخ الحجوزات
$updatedReservations = DB::table('RESERVATION')
    ->whereDate('DATE_RESERVATION', $yesterday)
    ->update([
        'DATE_RESERVATION' => DB::raw("REPLACE(CAST(DATE_RESERVATION AS VARCHAR), '2025-07-08', '2025-07-09')")
    ]);
    
echo "✅ تم تحديث {$updatedReservations} حجز\n";

echo "\n🎉 تم التحديث بنجاح! البيانات الآن لليوم الحالي.\n";
