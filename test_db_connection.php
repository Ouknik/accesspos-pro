<?php
/**
 * سكريپت اختبار اتصال قاعدة البيانات وإضافة بيانات تجريبية بسيطة
 * AccessPOS Pro - Simple Demo Data Generator
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 AccessPOS Pro - اختبار قاعدة البيانات\n";
echo "==========================================\n";

try {
    // اختبار الاتصال
    echo "📡 اختبار الاتصال بقاعدة البيانات...\n";
    $connection = DB::connection();
    $pdo = $connection->getPdo();
    echo "✅ تم الاتصال بنجاح!\n";
    
    // عرض معلومات قاعدة البيانات
    $database = $connection->getDatabaseName();
    echo "📊 قاعدة البيانات: {$database}\n";
    
    // اختبار قراءة الجداول الأساسية
    echo "\n🔍 فحص الجداول الموجودة...\n";
    
    $tables = [
        'CLIENT' => 'العملاء',
        'ARTICLE' => 'المقالات', 
        'FACTURE_VNT' => 'فواتير المبيعات',
        'STOCK' => 'المخزون',
        'TABLE' => 'طاولات المطعم'
    ];
    
    foreach ($tables as $table => $name) {
        try {
            $count = DB::table($table)->count();
            echo "  📋 {$name} ({$table}): {$count} سجل\n";
        } catch (Exception $e) {
            echo "  ❌ {$name} ({$table}): غير موجود أو خطأ\n";
        }
    }
    
    // إحصائيات سريعة لليوم
    echo "\n📈 إحصائيات اليوم (" . date('Y-m-d') . "):\n";
    
    try {
        $todaySales = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', Carbon::today())
            ->count();
        echo "  💰 فواتير اليوم: {$todaySales}\n";
        
        if ($todaySales > 0) {
            $totalAmount = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', Carbon::today())
                ->sum('FCTV_MNT_TTC');
            echo "  💵 إجمالي المبيعات: " . number_format($totalAmount, 2) . " DH\n";
        }
    } catch (Exception $e) {
        echo "  ⚠️ لا يمكن قراءة إحصائيات المبيعات\n";
    }
    
    // محاولة إضافة بيانات بسيطة
    echo "\n🎯 محاولة إضافة بيانات تجريبية بسيطة...\n";
    
    // إضافة عميل تجريبي
    try {
        $testClientRef = 'TEST_CLIENT_' . date('Ymd_His');
        
        // التحقق من وجود فئة عملاء أولاً
        $categoryExists = DB::table('CATEGORIE_CLIENT')->exists();
        if (!$categoryExists) {
            echo "  ⚠️ لا توجد فئات عملاء، سيتم إنشاء فئة افتراضية...\n";
            
            try {
                DB::table('CATEGORIE_CLIENT')->insert([
                    'CLTCAT_REF' => 'DEFAULT',
                    'CLTCAT_LIBELLE' => 'فئة افتراضية'
                ]);
                echo "  ✅ تم إنشاء فئة العملاء الافتراضية\n";
            } catch (Exception $e) {
                echo "  ❌ خطأ في إنشاء فئة العملاء: " . $e->getMessage() . "\n";
            }
        }
        
        // إضافة العميل التجريبي
        $clientData = [
            'CLT_REF' => $testClientRef,
            'CLTCAT_REF' => 'DEFAULT',
            'CLT_CLIENT' => 'عميل تجريبي - ' . date('H:i:s'),
            'CLT_TELEPHONE' => '0600000000',
            'CLT_BLOQUE' => 0,
            'CLT_FIDELE' => 0,
            'CLT_CREDIT' => 0.00
        ];
        
        DB::table('CLIENT')->insert($clientData);
        echo "  ✅ تم إضافة عميل تجريبي: {$testClientRef}\n";
        
    } catch (Exception $e) {
        echo "  ❌ خطأ في إضافة العميل التجريبي: " . $e->getMessage() . "\n";
    }
    
    // محاولة إضافة منتج تجريبي
    try {
        // التحقق من الفئات المطلوبة
        $familyExists = DB::table('FAMILLE')->where('FAM_REF', 'TEST_FAM')->exists();
        if (!$familyExists) {
            DB::table('FAMILLE')->insert([
                'FAM_REF' => 'TEST_FAM',
                'FAM_DESIGNATION' => 'فئة تجريبية'
            ]);
            echo "  ✅ تم إنشاء فئة المنتجات التجريبية\n";
        }
        
        $subFamilyExists = DB::table('SOUS_FAMILLE')->where('SFM_REF', 'TEST_SFM')->exists();
        if (!$subFamilyExists) {
            DB::table('SOUS_FAMILLE')->insert([
                'SFM_REF' => 'TEST_SFM',
                'FAM_REF' => 'TEST_FAM',
                'SFM_DESIGNATION' => 'فئة فرعية تجريبية'
            ]);
            echo "  ✅ تم إنشاء الفئة الفرعية التجريبية\n";
        }
        
        // إضافة المنتج
        $testArticleRef = 'TEST_ART_' . date('Ymd_His');
        
        $articleData = [
            'ART_REF' => $testArticleRef,
            'SFM_REF' => 'TEST_SFM',
            'ART_DESIGNATION' => 'منتج تجريبي - ' . date('H:i:s'),
            'ART_PRIX_VENTE' => 10.00,
            'ART_PRIX_ACHAT_HT' => 8.00,
            'ART_PRIX_VENTE_HT' => 9.50,
            'ART_LIBELLE_CAISSE' => 'منتج تجريبي',
            'ART_LIBELLE_ARABE' => 'منتج تجريبي',
            'ART_VENTE' => 1,
            'UNM_ABR' => 'PC'
        ];
        
        DB::table('ARTICLE')->insert($articleData);
        echo "  ✅ تم إضافة منتج تجريبي: {$testArticleRef}\n";
        
    } catch (Exception $e) {
        echo "  ❌ خطأ في إضافة المنتج التجريبي: " . $e->getMessage() . "\n";
    }
    
    // محاولة إضافة فاتورة بسيطة
    try {
        $testInvoiceRef = 'TEST_INV_' . date('Ymd_His');
        $now = Carbon::now();
        
        // الحصول على عميل ومنتج للاختبار
        $client = DB::table('CLIENT')->first();
        $article = DB::table('ARTICLE')->first();
        
        if ($client && $article) {
            $invoiceData = [
                'FCTV_REF' => $testInvoiceRef,
                'CLT_REF' => $client->CLT_REF,
                'FCTV_NUMERO' => 'TEST_' . date('YmdHis'),
                'FCTV_DATE' => $now,
                'FCTV_MNT_TTC' => 25.00,
                'FCTV_VALIDE' => 1,
                'FCTV_UTILISATEUR' => 'TEST'
            ];
            
            DB::table('FACTURE_VNT')->insert($invoiceData);
            echo "  ✅ تم إضافة فاتورة تجريبية: {$testInvoiceRef}\n";
            
            // إضافة تفاصيل الفاتورة
            $detailData = [
                'FCTV_REF' => $testInvoiceRef,
                'ART_REF' => $article->ART_REF,
                'FCTVD_QUANTITE' => 1,
                'FCTVD_PRIX_TOTAL' => 25.00
            ];
            
            try {
                DB::table('FACTURE_VNT_DETAIL')->insert($detailData);
                echo "  ✅ تم إضافة تفاصيل الفاتورة\n";
            } catch (Exception $e) {
                echo "  ⚠️ تم إضافة الفاتورة لكن فشل في إضافة التفاصيل\n";
            }
            
        } else {
            echo "  ⚠️ لا توجد عملاء أو منتجات لإنشاء فاتورة\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ خطأ في إضافة الفاتورة التجريبية: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 انتهى الاختبار!\n";
    echo "💡 يمكنك الآن زيارة لوحة القيادة لمشاهدة البيانات\n";
    echo "🔗 الرابط: http://localhost:8000/admin/tableau-de-bord-moderne\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الاتصال بقاعدة البيانات:\n";
    echo "   الرسالة: " . $e->getMessage() . "\n";
    echo "   الملف: " . $e->getFile() . "\n";
    echo "   السطر: " . $e->getLine() . "\n\n";
    
    echo "🔧 للحل:\n";
    echo "1. تأكد من تشغيل SQL Server\n";
    echo "2. تحقق من إعدادات .env:\n";
    echo "   DB_CONNECTION=sqlsrv\n";
    echo "   DB_HOST=127.0.0.1\n";
    echo "   DB_PORT=1433\n";
    echo "   DB_DATABASE=RestoWinxo\n";
    echo "   DB_USERNAME=access_user2\n";
    echo "   DB_PASSWORD=1234567890\n";
    echo "3. تأكد من صلاحيات المستخدم على قاعدة البيانات\n";
}
?>
