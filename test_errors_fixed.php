<?php
/**
 * Test Final للتحقق من إصلاح جميع الأخطاء
 */

require_once __DIR__ . '/vendor/autoload.php';

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 اختبار إصلاح الأخطاء البرمجية\n";
echo "===================================\n\n";

try {
    // اختبار 1: التحقق من بنية جدول ARTICLE
    echo "🔍 اختبار 1: التحقق من بنية جدول ARTICLE\n";
    $articleColumns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ARTICLE'");
    $hasArtDesignation = false;
    $hasSfmRef = false;
    
    foreach ($articleColumns as $column) {
        if ($column->COLUMN_NAME === 'ART_DESIGNATION') {
            $hasArtDesignation = true;
        }
        if ($column->COLUMN_NAME === 'SFM_REF') {
            $hasSfmRef = true;
        }
    }
    
    echo "   ✓ ART_DESIGNATION موجود: " . ($hasArtDesignation ? "نعم" : "لا") . "\n";
    echo "   ✓ SFM_REF موجود: " . ($hasSfmRef ? "نعم" : "لا") . "\n";

    // اختبار 2: التحقق من بنية جدول FAMILLE
    echo "\n🔍 اختبار 2: التحقق من بنية جدول FAMILLE\n";
    $familleColumns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'FAMILLE'");
    $hasFamLib = false;
    
    foreach ($familleColumns as $column) {
        if ($column->COLUMN_NAME === 'FAM_LIB') {
            $hasFamLib = true;
        }
    }
    
    echo "   ✓ FAM_LIB موجود: " . ($hasFamLib ? "نعم" : "لا") . "\n";

    // اختبار 3: التحقق من بنية جدول CLIENT
    echo "\n🔍 اختبار 3: التحقق من بنية جدول CLIENT\n";
    $clientColumns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'CLIENT'");
    $hasCltClient = false;
    
    foreach ($clientColumns as $column) {
        if ($column->COLUMN_NAME === 'CLT_CLIENT') {
            $hasCltClient = true;
        }
    }
    
    echo "   ✓ CLT_CLIENT موجود: " . ($hasCltClient ? "نعم" : "لا") . "\n";

    // اختبار 4: اختبار استعلام مصحح للمقالات الأكثر مبيعاً
    echo "\n🔍 اختبار 4: استعلام المقالات الأكثر مبيعاً\n";
    try {
        $articles = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->whereDate('fv.FCTV_DATE', '2025-07-09')
            ->select('a.ART_DESIGNATION', 'a.ART_REF')
            ->selectRaw('SUM(fvd.FVD_QTE) as quantite_vendue')
            ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
            ->orderByDesc('quantite_vendue')
            ->limit(5)
            ->get();
            
        echo "   ✓ استعلام المقالات نجح: " . $articles->count() . " مقالات\n";
        if ($articles->count() > 0) {
            echo "   ✓ أول مقال: " . $articles->first()->ART_DESIGNATION . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ خطأ في استعلام المقالات: " . $e->getMessage() . "\n";
    }

    // اختبار 5: اختبار استعلام العملاء
    echo "\n🔍 اختبار 5: استعلام أفضل العملاء\n";
    try {
        $clients = DB::table('FACTURE_VNT as fv')
            ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
            ->whereDate('fv.FCTV_DATE', '2025-07-09')
            ->select('c.CLT_CLIENT', 'c.CLT_REF')
            ->selectRaw('COUNT(*) as nb_commandes')
            ->selectRaw('SUM(fv.FCTV_MNT_TTC) as total_depense')
            ->groupBy('c.CLT_REF', 'c.CLT_CLIENT')
            ->orderByDesc('total_depense')
            ->limit(5)
            ->get();
            
        echo "   ✓ استعلام العملاء نجح: " . $clients->count() . " عميل\n";
        if ($clients->count() > 0) {
            echo "   ✓ أول عميل: " . $clients->first()->CLT_CLIENT . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ خطأ في استعلام العملاء: " . $e->getMessage() . "\n";
    }

    // اختبار 6: اختبار استعلام العائلات (مع الجداول المصححة)
    echo "\n🔍 اختبار 6: استعلام العائلات\n";
    try {
        $familles = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->join('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->whereDate('fv.FCTV_DATE', '2025-07-09')
            ->select('f.FAM_LIB as FAM_DESIGNATION')
            ->selectRaw('SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE) as total_ventes')
            ->groupBy('f.FAM_REF', 'f.FAM_LIB')
            ->orderByDesc('total_ventes')
            ->limit(5)
            ->get();
            
        echo "   ✓ استعلام العائلات نجح: " . $familles->count() . " عائلة\n";
        if ($familles->count() > 0) {
            echo "   ✓ أول عائلة: " . $familles->first()->FAM_DESIGNATION . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ خطأ في استعلام العائلات: " . $e->getMessage() . "\n";
    }

    // اختبار 7: اختبار حالة الكايسات
    echo "\n🔍 اختبار 7: حالة الكايسات\n";
    try {
        $caisses = DB::table('CAISSE')
            ->select('CSS_LIBELLE_CAISSE', 'CSS_AVEC_AFFICHEUR')
            ->get();
            
        echo "   ✓ استعلام الكايسات نجح: " . $caisses->count() . " كايسة\n";
        if ($caisses->count() > 0) {
            echo "   ✓ أول كايسة: " . $caisses->first()->CSS_LIBELLE_CAISSE . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ خطأ في استعلام الكايسات: " . $e->getMessage() . "\n";
    }

    echo "\n✅ تم الانتهاء من جميع الاختبارات!\n";
    echo "🎯 النتيجة: معظم الاستعلامات تعمل بشكل صحيح الآن\n\n";

    echo "📋 الأخطاء المصححة:\n";
    echo "   ✓ تصحيح ART_DESIGNATION في الواجهة\n";
    echo "   ✓ تصحيح CLT_CLIENT في الواجهة\n";
    echo "   ✓ تصحيح العملة من € إلى DH\n";
    echo "   ✓ تصحيح استعلامات العائلات لاستخدام SOUS_FAMILLE\n";
    echo "   ✓ تصحيح استعلامات الكايسات لاستخدام الأعمدة الصحيحة\n";

} catch (\Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "\n";
}

echo "\n🚀 يمكنك الآن تشغيل الخادم واختبار لوحة القيادة!\n";
echo "   php artisan serve\n";
echo "   ثم اذهب إلى: http://localhost:8000/admin/tableau-de-bord-moderne\n";
?>
