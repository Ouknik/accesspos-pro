<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 تحديث جميع البيانات لليوم الحالي (2025-07-09)...\n";
echo "=========================================================\n";

$targetDate = '2025-07-09';
echo "📅 التاريخ المطلوب: $targetDate\n\n";

// تحديث جميع الفواتير
$invoiceUpdates = DB::table('FACTURE_VNT')
    ->whereNotLike('FCTV_DATE', $targetDate . '%')
    ->update([
        'FCTV_DATE' => DB::raw("'" . $targetDate . " ' + CAST(DATEPART(HOUR, FCTV_DATE) AS VARCHAR) + ':' + CAST(DATEPART(MINUTE, FCTV_DATE) AS VARCHAR) + ':' + CAST(DATEPART(SECOND, FCTV_DATE) AS VARCHAR)")
    ]);

echo "✅ تم تحديث {$invoiceUpdates} فاتورة\n";

// تحديث جميع المدفوعات
$paymentUpdates = DB::table('REGLEMENT')
    ->whereNotLike('REG_DATE', $targetDate . '%')
    ->update([
        'REG_DATE' => DB::raw("'" . $targetDate . " ' + CAST(DATEPART(HOUR, REG_DATE) AS VARCHAR) + ':' + CAST(DATEPART(MINUTE, REG_DATE) AS VARCHAR) + ':' + CAST(DATEPART(SECOND, REG_DATE) AS VARCHAR)")
    ]);

echo "✅ تم تحديث {$paymentUpdates} مدفوعة\n";

// طريقة مبسطة: تحديث مباشر بتاريخ ثابت
DB::statement("UPDATE FACTURE_VNT SET FCTV_DATE = REPLACE(CAST(FCTV_DATE AS VARCHAR), '2025-07-08', '2025-07-09') WHERE FCTV_DATE LIKE '2025-07-08%'");
DB::statement("UPDATE REGLEMENT SET REG_DATE = REPLACE(CAST(REG_DATE AS VARCHAR), '2025-07-08', '2025-07-09') WHERE REG_DATE LIKE '2025-07-08%'");
DB::statement("UPDATE DEPENSE SET DEP_DATE = REPLACE(CAST(DEP_DATE AS VARCHAR), '2025-07-08', '2025-07-09') WHERE DEP_DATE LIKE '2025-07-08%'");
DB::statement("UPDATE RESERVATION SET DATE_RESERVATION = REPLACE(CAST(DATE_RESERVATION AS VARCHAR), '2025-07-08', '2025-07-09') WHERE DATE_RESERVATION LIKE '2025-07-08%'");

echo "✅ تم تحديث التواريخ بنجاح!\n\n";

// التحقق من النتائج
$todayInvoices = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $targetDate)
    ->count();

$todayPayments = DB::table('REGLEMENT')
    ->whereDate('REG_DATE', $targetDate)
    ->count();

echo "📊 النتائج:\n";
echo "   - فواتير اليوم ($targetDate): {$todayInvoices}\n";
echo "   - مدفوعات اليوم ($targetDate): {$todayPayments}\n";

if ($todayInvoices > 0) {
    echo "\n🎉 ممتاز! البيانات الآن تظهر لليوم الحالي.\n";
} else {
    echo "\n⚠️ لا توجد فواتير لليوم الحالي. ربما نحتاج لإنشاء بيانات جديدة.\n";
}
