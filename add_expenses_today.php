<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "💸 إضافة مصروفات لليوم (2025-07-09)...\n";
echo "========================================\n";

$today = '2025-07-09';

// إضافة أنواع المصروفات أولاً
$expenseTypes = [
    'مشتريات',
    'مصروفات تشغيلية', 
    'صيانة وإصلاح',
    'رواتب وأجور',
    'مصروفات إدارية'
];

echo "📝 إضافة أنواع المصروفات...\n";
foreach ($expenseTypes as $type) {
    $exists = DB::table('MOTIF_DEPENSE')->where('MTF_DPS_MOTIF', $type)->exists();
    if (!$exists) {
        DB::table('MOTIF_DEPENSE')->insert(['MTF_DPS_MOTIF' => $type]);
        echo "  ✅ {$type}\n";
    }
}

echo "\n💰 إضافة المصروفات...\n";

$expenses = [
    [
        'DEP_REF' => 'DEP_' . date('Ymd') . '_001',
        'DEP_DATE' => $today . ' 08:30:00',
        'DEP_MONTANTHT' => 450.00,
        'DEP_COMMENTAIRE' => 'شراء مواد خام للمطبخ',
        'MTF_DPS_MOTIF' => 'مشتريات',
        'CSS_ID_CAISSE' => '01'
    ],
    [
        'DEP_REF' => 'DEP_' . date('Ymd') . '_002',
        'DEP_DATE' => $today . ' 10:15:00',
        'DEP_MONTANTHT' => 200.00,
        'DEP_COMMENTAIRE' => 'فواتير كهرباء وماء',
        'MTF_DPS_MOTIF' => 'مصروفات تشغيلية',
        'CSS_ID_CAISSE' => '01'
    ],
    [
        'DEP_REF' => 'DEP_' . date('Ymd') . '_003',
        'DEP_DATE' => $today . ' 14:45:00',
        'DEP_MONTANTHT' => 150.00,
        'DEP_COMMENTAIRE' => 'صيانة معدات',
        'MTF_DPS_MOTIF' => 'صيانة وإصلاح',
        'CSS_ID_CAISSE' => '01'
    ],
    [
        'DEP_REF' => 'DEP_' . date('Ymd') . '_004',
        'DEP_DATE' => $today . ' 16:20:00',
        'DEP_MONTANTHT' => 120.00,
        'DEP_COMMENTAIRE' => 'مستلزمات تنظيف',
        'MTF_DPS_MOTIF' => 'مصروفات تشغيلية',
        'CSS_ID_CAISSE' => '01'
    ],
    [
        'DEP_REF' => 'DEP_' . date('Ymd') . '_005',
        'DEP_DATE' => $today . ' 18:10:00',
        'DEP_MONTANTHT' => 300.00,
        'DEP_COMMENTAIRE' => 'راتب موظف يومي',
        'MTF_DPS_MOTIF' => 'رواتب وأجور',
        'CSS_ID_CAISSE' => '01'
    ]
];

$totalExpenses = 0;
foreach ($expenses as $expense) {
    $exists = DB::table('DEPENSE')->where('DEP_REF', $expense['DEP_REF'])->exists();
    if (!$exists) {
        DB::table('DEPENSE')->insert($expense);
        $totalExpenses += $expense['DEP_MONTANTHT'];
        echo "  ✅ " . $expense['DEP_COMMENTAIRE'] . " - " . number_format($expense['DEP_MONTANTHT'], 2) . " DH\n";
    } else {
        echo "  ⚠️ " . $expense['DEP_COMMENTAIRE'] . " - موجود مسبقاً\n";
    }
}

echo "\n💰 إجمالي المصروفات المضافة: " . number_format($totalExpenses, 2) . " DH\n";

// التحقق من المصروفات الإجمالية لليوم
$allExpenses = DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->sum('DEP_MONTANTHT');
echo "📊 إجمالي مصروفات اليوم: " . number_format($allExpenses, 2) . " DH\n";

echo "\n✅ تم! الآن لوحة القيادة ستظهر المصروفات أيضاً.\n";
