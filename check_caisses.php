<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 التحقق من الكاش الموجود...\n";
$caisses = DB::table('CAISSE')->get(['CSS_ID_CAISSE', 'CSS_LIBELLE_CAISSE']);

if ($caisses->isNotEmpty()) {
    echo "📋 الكاش الموجود:\n";
    foreach ($caisses as $caisse) {
        echo "  - " . $caisse->CSS_ID_CAISSE . " (" . ($caisse->CSS_LIBELLE_CAISSE ?? 'بدون وصف') . ")\n";
    }
} else {
    echo "⚠️ لا توجد كاش في قاعدة البيانات\n";
    echo "🔧 إضافة كاش افتراضي...\n";
    DB::table('CAISSE')->insert([
        'CSS_ID_CAISSE' => 'CAISSE001',
        'CSS_LIBELLE_CAISSE' => 'الكاش الرئيسي',
        'CSS_AVEC_AFFICHEUR' => 0,
        'CSS_NUM_CMD' => '001',
        'CSS_NUM_FACT' => '001'
    ]);
    echo "  ✅ تم إضافة الكاش الرئيسي\n";
}
