<?php
/**
 * AccessPOS Pro - مولد البيانات التجريبية المحسن
 * يعمل مع بنية قاعدة البيانات الفعلية
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class OptimizedDemoDataGenerator
{
    private $today;
    private $errors = [];
    private $success = [];
    
    public function __construct()
    {
        $this->today = Carbon::today();
    }
    
    public function run()
    {
        echo "🚀 AccessPOS Pro - مولد البيانات المحسن\n";
        echo "========================================\n";
        echo "📅 التاريخ: " . $this->today->format('Y-m-d') . "\n\n";
        
        try {
            // اختبار الاتصال
            $this->testConnection();
            
            // تنظيف البيانات القديمة
            $this->cleanTodayData();
            
            // إضافة البيانات بطريقة آمنة
            $this->addSafeData();
            
            // عرض النتائج
            $this->showResults();
            
        } catch (Exception $e) {
            echo "❌ خطأ رئيسي: " . $e->getMessage() . "\n";
        }
    }
    
    private function testConnection()
    {
        echo "🔍 اختبار الاتصال...\n";
        $pdo = DB::connection()->getPdo();
        echo "  ✅ متصل بقاعدة البيانات: " . DB::connection()->getDatabaseName() . "\n\n";
    }
    
    private function cleanTodayData()
    {
        echo "🧹 تنظيف بيانات اليوم...\n";
        
        try {
            $deleted = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $this->today)
                ->delete();
            if ($deleted > 0) {
                echo "  🗑️ تم حذف {$deleted} فاتورة قديمة\n";
            }
        } catch (Exception $e) {
            echo "  ⚠️ خطأ في حذف الفواتير: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function addSafeData()
    {
        echo "📦 إضافة البيانات الجديدة...\n";
        
        // 1. إضافة عملاء
        $this->addSafeCustomers();
        
        // 2. إضافة منتجات (إذا أمكن)
        $this->addSafeProducts();
        
        // 3. إضافة مبيعات بسيطة
        $this->addSafeSales();
        
        // 4. إضافة مدفوعات
        $this->addSafePayments();
        
        // 5. تحديث حالة الطاولات
        $this->updateTableStatus();
    }
    
    private function addSafeCustomers()
    {
        echo "  👥 إضافة عملاء...\n";
        
        try {
            // فحص الأعمدة المطلوبة في جدول CLIENT
            $columns = DB::getSchemaBuilder()->getColumnListing('CLIENT');
            
            $customers = [
                [
                    'CLT_REF' => 'DEMO_' . date('Ymd') . '_001',
                    'CLT_CLIENT' => 'أحمد محمد التجريبي',
                    'CLT_TELEPHONE' => '0612345678'
                ],
                [
                    'CLT_REF' => 'DEMO_' . date('Ymd') . '_002', 
                    'CLT_CLIENT' => 'فاطمة الزهراء التجريبية',
                    'CLT_TELEPHONE' => '0623456789'
                ],
                [
                    'CLT_REF' => 'DEMO_' . date('Ymd') . '_003',
                    'CLT_CLIENT' => 'شركة النور التجريبية',
                    'CLT_TELEPHONE' => '0534567890'
                ]
            ];
            
            // إضافة فئة عملاء افتراضية إذا لزم الأمر
            if (in_array('CLTCAT_REF', $columns)) {
                $this->ensureCategoryExists();
                foreach ($customers as &$customer) {
                    $customer['CLTCAT_REF'] = 'DEMO_CAT';
                }
            }
            
            // إضافة القيم الافتراضية للأعمدة المطلوبة
            foreach ($customers as &$customer) {
                if (in_array('CLT_BLOQUE', $columns)) $customer['CLT_BLOQUE'] = 0;
                if (in_array('CLT_FIDELE', $columns)) $customer['CLT_FIDELE'] = 1;
                if (in_array('CLT_CREDIT', $columns)) $customer['CLT_CREDIT'] = 0.00;
                if (in_array('CLT_POINTFIDILIO', $columns)) $customer['CLT_POINTFIDILIO'] = rand(50, 300);
            }
            
            foreach ($customers as $customer) {
                $exists = DB::table('CLIENT')->where('CLT_CLIENT', $customer['CLT_CLIENT'])->exists();
                if (!$exists) {
                    DB::table('CLIENT')->insert($customer);
                    echo "    ✅ " . $customer['CLT_CLIENT'] . "\n";
                    $this->success[] = "عميل: " . $customer['CLT_CLIENT'];
                }
            }
            
        } catch (Exception $e) {
            echo "    ❌ خطأ في إضافة العملاء: " . $e->getMessage() . "\n";
            $this->errors[] = "العملاء: " . $e->getMessage();
        }
    }
    
    private function ensureCategoryExists()
    {
        try {
            $exists = DB::table('CATEGORIE_CLIENT')->where('CLTCAT_REF', 'DEMO_CAT')->exists();
            if (!$exists) {
                DB::table('CATEGORIE_CLIENT')->insert([
                    'CLTCAT_REF' => 'DEMO_CAT',
                    'CLTCAT_LIBELLE' => 'عملاء تجريبيين'
                ]);
            }
        } catch (Exception $e) {
            // تجاهل خطأ فئة العملاء
        }
    }
    
    private function addSafeProducts()
    {
        echo "  📦 إضافة منتجات...\n";
        
        try {
            // فحص الأعمدة المطلوبة
            $columns = DB::getSchemaBuilder()->getColumnListing('ARTICLE');
            
            // إنشاء فئة منتجات تجريبية
            $this->ensureProductCategoryExists();
            
            $products = [
                [
                    'ART_REF' => 'DEMO_PROD_' . date('Ymd') . '_001',
                    'ART_DESIGNATION' => 'قهوة تجريبية',
                    'ART_PRIX_VENTE' => 15.00,
                    'ART_LIBELLE_CAISSE' => 'قهوة تجريبية',
                    'ART_LIBELLE_ARABE' => 'قهوة تجريبية'
                ],
                [
                    'ART_REF' => 'DEMO_PROD_' . date('Ymd') . '_002',
                    'ART_DESIGNATION' => 'ساندويتش تجريبي',
                    'ART_PRIX_VENTE' => 25.00,
                    'ART_LIBELLE_CAISSE' => 'ساندويتش تجريبي', 
                    'ART_LIBELLE_ARABE' => 'ساندويتش تجريبي'
                ]
            ];
            
            // إضافة القيم الافتراضية
            foreach ($products as &$product) {
                if (in_array('SFM_REF', $columns)) $product['SFM_REF'] = 'DEMO_SFM';
                if (in_array('ART_PRIX_ACHAT_HT', $columns)) $product['ART_PRIX_ACHAT_HT'] = $product['ART_PRIX_VENTE'] * 0.7;
                if (in_array('ART_PRIX_VENTE_HT', $columns)) $product['ART_PRIX_VENTE_HT'] = $product['ART_PRIX_VENTE'] * 0.9;
                if (in_array('ART_VENTE', $columns)) $product['ART_VENTE'] = 1;
                if (in_array('UNM_ABR', $columns)) $product['UNM_ABR'] = 'PC';
                if (in_array('ART_ORDRE_AFFICHAGE', $columns)) $product['ART_ORDRE_AFFICHAGE'] = 999;
            }
            
            foreach ($products as $product) {
                $exists = DB::table('ARTICLE')->where('ART_DESIGNATION', $product['ART_DESIGNATION'])->exists();
                if (!$exists) {
                    DB::table('ARTICLE')->insert($product);
                    echo "    ✅ " . $product['ART_DESIGNATION'] . " - " . $product['ART_PRIX_VENTE'] . " DH\n";
                    $this->success[] = "منتج: " . $product['ART_DESIGNATION'];
                }
            }
            
        } catch (Exception $e) {
            echo "    ❌ خطأ في إضافة المنتجات: " . $e->getMessage() . "\n";
            $this->errors[] = "المنتجات: " . $e->getMessage();
        }
    }
    
    private function ensureProductCategoryExists()
    {
        try {
            // إنشاء عائلة منتجات
            $familyExists = DB::table('FAMILLE')->where('FAM_REF', 'DEMO_FAM')->exists();
            if (!$familyExists) {
                DB::table('FAMILLE')->insert([
                    'FAM_REF' => 'DEMO_FAM',
                    'FAM_DESIGNATION' => 'منتجات تجريبية'
                ]);
            }
            
            // إنشاء فئة فرعية
            $subFamilyExists = DB::table('SOUS_FAMILLE')->where('SFM_REF', 'DEMO_SFM')->exists();
            if (!$subFamilyExists) {
                DB::table('SOUS_FAMILLE')->insert([
                    'SFM_REF' => 'DEMO_SFM',
                    'FAM_REF' => 'DEMO_FAM',
                    'SFM_DESIGNATION' => 'فئة فرعية تجريبية'
                ]);
            }
        } catch (Exception $e) {
            // تجاهل الأخطاء
        }
    }
    
    private function addSafeSales()
    {
        echo "  💰 إضافة مبيعات...\n";
        
        try {
            $customers = DB::table('CLIENT')->where('CLT_REF', 'like', 'DEMO_%')->get();
            $products = DB::table('ARTICLE')->where('ART_REF', 'like', 'DEMO_%')->get();
            
            if ($customers->isEmpty()) {
                echo "    ⚠️ لا توجد عملاء تجريبيين\n";
                return;
            }
            
            // استخدام منتجات موجودة إذا لم تكن هناك منتجات تجريبية
            if ($products->isEmpty()) {
                $products = DB::table('ARTICLE')->limit(3)->get();
            }
            
            if ($products->isEmpty()) {
                echo "    ⚠️ لا توجد منتجات\n";
                return;
            }
            
            $invoiceCount = 0;
            $totalSales = 0;
            
            // إنشاء فواتير لساعات مختلفة
            $hours = [10, 13, 16, 19];
            
            foreach ($hours as $hour) {
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $invoiceRef = 'DEMO_INV_' . date('Ymd') . '_' . sprintf('%03d', $invoiceCount + 1);
                    $customer = $customers->random();
                    $product = $products->random();
                    $quantity = rand(1, 2);
                    $amount = $product->ART_PRIX_VENTE * $quantity;
                    
                    $dateTime = $this->today->copy()->setHour($hour)->setMinute(rand(0, 59));
                    
                    $invoiceData = [
                        'FCTV_REF' => $invoiceRef,
                        'CLT_REF' => $customer->CLT_REF,
                        'FCTV_NUMERO' => 'DEMO_' . date('YmdHis') . '_' . ($invoiceCount + 1),
                        'FCTV_DATE' => $dateTime,
                        'FCTV_MNT_TTC' => $amount,
                        'FCTV_VALIDE' => 1,
                        'FCTV_UTILISATEUR' => 'DEMO'
                    ];
                    
                    // إضافة الأعمدة الاختيارية
                    $columns = DB::getSchemaBuilder()->getColumnListing('FACTURE_VNT');
                    if (in_array('ETP_REF', $columns)) $invoiceData['ETP_REF'] = 'ETP001';
                    if (in_array('FCTV_MNT_HT', $columns)) $invoiceData['FCTV_MNT_HT'] = $amount * 0.9;
                    if (in_array('FCTV_REMISE', $columns)) $invoiceData['FCTV_REMISE'] = 0.0;
                    if (in_array('FCTV_EXONORE', $columns)) $invoiceData['FCTV_EXONORE'] = 0;
                    
                    DB::table('FACTURE_VNT')->insert($invoiceData);
                    
                    // إضافة تفاصيل الفاتورة إذا أمكن
                    try {
                        $detailData = [
                            'FCTV_REF' => $invoiceRef,
                            'ART_REF' => $product->ART_REF,
                            'FCTVD_QUANTITE' => $quantity,
                            'FCTVD_PRIX_TOTAL' => $amount
                        ];
                        
                        $detailColumns = DB::getSchemaBuilder()->getColumnListing('FACTURE_VNT_DETAIL');
                        if (in_array('FCTVD_PRIX_UNITAIRE_TTC', $detailColumns)) {
                            $detailData['FCTVD_PRIX_UNITAIRE_TTC'] = $product->ART_PRIX_VENTE;
                        }
                        if (in_array('FCTVD_TVA', $detailColumns)) {
                            $detailData['FCTVD_TVA'] = 20.0;
                        }
                        
                        DB::table('FACTURE_VNT_DETAIL')->insert($detailData);
                    } catch (Exception $e) {
                        // تجاهل خطأ تفاصيل الفاتورة
                    }
                    
                    $totalSales += $amount;
                    $invoiceCount++;
                }
            }
            
            echo "    ✅ تم إضافة {$invoiceCount} فاتورة بإجمالي: " . number_format($totalSales, 2) . " DH\n";
            $this->success[] = "مبيعات: {$invoiceCount} فاتورة، " . number_format($totalSales, 2) . " DH";
            
        } catch (Exception $e) {
            echo "    ❌ خطأ في إضافة المبيعات: " . $e->getMessage() . "\n";
            $this->errors[] = "المبيعات: " . $e->getMessage();
        }
    }
    
    private function addSafePayments()
    {
        echo "  💳 إضافة مدفوعات...\n";
        
        try {
            $invoices = DB::table('FACTURE_VNT')
                ->where('FCTV_REF', 'like', 'DEMO_%')
                ->whereDate('FCTV_DATE', $this->today)
                ->get();
            
            if ($invoices->isEmpty()) {
                echo "    ⚠️ لا توجد فواتير لإضافة مدفوعات\n";
                return;
            }
            
            $paymentMethods = ['ESPECES', 'CARTE', 'CHEQUE'];
            $paymentCount = 0;
            
            foreach ($invoices as $invoice) {
                try {
                    $method = $paymentMethods[array_rand($paymentMethods)];
                    
                    $paymentData = [
                        'REG_REF' => 'DEMO_PAY_' . $invoice->FCTV_REF,
                        'FCTV_REF' => $invoice->FCTV_REF,
                        'REG_MONTANT' => $invoice->FCTV_MNT_TTC,
                        'REG_MODE' => $method,
                        'REG_DATE' => $invoice->FCTV_DATE
                    ];
                    
                    $columns = DB::getSchemaBuilder()->getColumnListing('REGLEMENT');
                    if (in_array('REG_UTILISATEUR', $columns)) {
                        $paymentData['REG_UTILISATEUR'] = 'DEMO';
                    }
                    
                    DB::table('REGLEMENT')->insert($paymentData);
                    $paymentCount++;
                } catch (Exception $e) {
                    // تجاهل خطأ الدفعة الواحدة
                }
            }
            
            echo "    ✅ تم إضافة {$paymentCount} دفعة\n";
            $this->success[] = "مدفوعات: {$paymentCount} دفعة";
            
        } catch (Exception $e) {
            echo "    ❌ خطأ في إضافة المدفوعات: " . $e->getMessage() . "\n";
            $this->errors[] = "المدفوعات: " . $e->getMessage();
        }
    }
    
    private function updateTableStatus()
    {
        echo "  🍽️ تحديث حالة الطاولات...\n";
        
        try {
            $tables = DB::table('TABLE')->limit(5)->get();
            
            if ($tables->isEmpty()) {
                echo "    ⚠️ لا توجد طاولات\n";
                return;
            }
            
            $statuses = ['LIBRE', 'OCCUPEE', 'RESERVEE'];
            $updatedCount = 0;
            
            foreach ($tables as $table) {
                $newStatus = $statuses[array_rand($statuses)];
                
                try {
                    DB::table('TABLE')
                        ->where('TAB_REF', $table->TAB_REF)
                        ->update(['TAB_ETAT' => $newStatus]);
                    $updatedCount++;
                } catch (Exception $e) {
                    // تجاهل خطأ الطاولة الواحدة
                }
            }
            
            echo "    ✅ تم تحديث {$updatedCount} طاولة\n";
            $this->success[] = "طاولات: {$updatedCount} محدثة";
            
        } catch (Exception $e) {
            echo "    ❌ خطأ في تحديث الطاولات: " . $e->getMessage() . "\n";
            $this->errors[] = "الطاولات: " . $e->getMessage();
        }
    }
    
    private function showResults()
    {
        echo "\n📊 نتائج العملية:\n";
        echo "================\n";
        
        echo "✅ العمليات الناجحة:\n";
        foreach ($this->success as $item) {
            echo "  - {$item}\n";
        }
        
        if (!empty($this->errors)) {
            echo "\n⚠️ الأخطاء:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
        }
        
        // إحصائيات نهائية
        echo "\n📈 الإحصائيات النهائية:\n";
        
        try {
            $todayInvoices = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $this->today)
                ->count();
            echo "  💰 فواتير اليوم: {$todayInvoices}\n";
            
            if ($todayInvoices > 0) {
                $totalAmount = DB::table('FACTURE_VNT')
                    ->whereDate('FCTV_DATE', $this->today)
                    ->sum('FCTV_MNT_TTC');
                echo "  💵 إجمالي المبيعات: " . number_format($totalAmount, 2) . " DH\n";
            }
            
            $demoCustomers = DB::table('CLIENT')->where('CLT_REF', 'like', 'DEMO_%')->count();
            echo "  👥 العملاء التجريبيين: {$demoCustomers}\n";
            
            $demoProducts = DB::table('ARTICLE')->where('ART_REF', 'like', 'DEMO_%')->count();
            echo "  📦 المنتجات التجريبية: {$demoProducts}\n";
            
        } catch (Exception $e) {
            echo "  ⚠️ لا يمكن عرض الإحصائيات\n";
        }
        
        echo "\n🎉 انتهت العملية!\n";
        echo "💻 يمكنك الآن زيارة لوحة القيادة:\n";
        echo "🔗 http://localhost:8000/admin/tableau-de-bord-moderne\n";
    }
}

// تشغيل المولد
try {
    $generator = new OptimizedDemoDataGenerator();
    $generator->run();
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "\n";
}
?>
