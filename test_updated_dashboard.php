<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 اختبار المنطق المحدث للوحة القيادة...\n";
echo "=========================================\n";

$aujourdhui = Carbon::parse('2025-07-09');
$debutMois = Carbon::parse('2025-07-01');
$debutAnnee = Carbon::parse('2025-01-01');

echo "📅 التاريخ المستخدم: " . $aujourdhui->format('Y-m-d') . "\n\n";

// اختبار الإحصائيات المالية الجديدة
echo "💰 الإحصائيات المالية:\n";

$ca_du_jour = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $aujourdhui)
    ->sum('FCT_MNT_RGL') ?? 0;

$nb_factures_jour = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $aujourdhui)
    ->count();

$ticket_moyen = DB::table('FACTURE_VNT')
    ->whereDate('FCTV_DATE', $aujourdhui)
    ->avg('FCT_MNT_RGL') ?? 0;

echo "   🏆 CA du jour: " . number_format($ca_du_jour, 2) . " DH\n";
echo "   📊 Nombre de factures: {$nb_factures_jour}\n";
echo "   📈 Ticket moyen: " . number_format($ticket_moyen, 2) . " DH\n\n";

// اختبار المصروفات
echo "💸 المصروفات:\n";
$depenses_jour = DB::table('DEPENSE')
    ->whereDate('DEP_DATE', $aujourdhui)
    ->sum('DEP_MONTANTHT') ?? 0;

echo "   💰 Dépenses du jour: " . number_format($depenses_jour, 2) . " DH\n\n";

// اختبار الحجوزات
echo "🏠 الحجوزات:\n";
$reservations_jour = DB::table('RESERVATION')
    ->whereDate('DATE_RESERVATION', $aujourdhui)
    ->count();

echo "   📅 Réservations du jour: {$reservations_jour}\n\n";

// اختبار المدفوعات
echo "💳 المدفوعات:\n";
$payments_count = DB::table('REGLEMENT')
    ->whereDate('REG_DATE', $aujourdhui)
    ->count();

$payments_amount = DB::table('REGLEMENT')
    ->whereDate('REG_DATE', $aujourdhui)
    ->sum('REG_MONTANT') ?? 0;

echo "   🔢 Nombre de paiements: {$payments_count}\n";
echo "   💰 Montant total: " . number_format($payments_amount, 2) . " DH\n\n";

// ملخص
echo "📊 ملخص سريع:\n";
echo "=" . str_repeat("=", 40) . "\n";
if ($ca_du_jour > 0) {
    echo "✅ لوحة القيادة ستظهر البيانات بنجاح!\n";
    echo "🎯 CA du jour: " . number_format($ca_du_jour, 2) . " DH\n";
    echo "📋 Factures: {$nb_factures_jour}\n";
    echo "💸 Dépenses: " . number_format($depenses_jour, 2) . " DH\n";
    echo "💰 Profit estimé: " . number_format($ca_du_jour - $depenses_jour, 2) . " DH\n";
} else {
    echo "❌ لا توجد بيانات لليوم المحدد\n";
}
