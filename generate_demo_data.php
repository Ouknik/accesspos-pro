<?php
/**
 * سكريپت إضافة بيانات تجريبية لتاريخ اليوم
 * AccessPOS Pro - Demo Data Generator
 * 
 * هذا السكريپت يقوم بإضافة بيانات تجريبية شاملة لتاريخ اليوم
 * لاختبار نظام التقارير والتحليلات
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class DemoDataGenerator
{
    private $today;
    private $now;
    
    public function __construct()
    {
        // استخدام التاريخ الحالي بدلاً من الأمس
        $this->today = Carbon::parse('2025-07-09'); // يستخدم التاريخ الحالي
        $this->now = Carbon::parse('2025-07-09 ' . date('H:i:s'));
    }
    
    public function run()
    {
        echo "🚀 AccessPOS Pro - مولد البيانات التجريبية\n";
        echo "=====================================\n";
        echo "📅 التاريخ: " . $this->today->format('Y-m-d') . "\n";
        echo "⏰ الوقت: " . $this->now->format('H:i:s') . "\n\n";
        
        try {
            // 1. تنظيف البيانات القديمة (اختياري)
            $this->cleanOldData();
            
            // 2. إضافة البيانات الأساسية
            $this->addBasicData();
            
            // 3. إضافة العملاء
            $this->addCustomers();
            
            // 4. إضافة المنتجات
            $this->addProducts();
            
            // 5. إضافة المبيعات
            $this->addSales();
            
            // 6. إضافة المدفوعات
            $this->addPayments();
            
            // 7. إضافة بيانات المطعم
            $this->addRestaurantData();
            
            // 8. إضافة المصروفات
            $this->addExpenses();
            
            // 9. إضافة إشعارات
            $this->addNotifications();
            
            echo "\n✅ تم إنتاج جميع البيانات التجريبية بنجاح!\n";
            echo "📊 يمكنك الآن اختبار التقارير والتحليلات\n";
            
            // عرض ملخص البيانات المضافة
            $this->showSummary();
            
        } catch (Exception $e) {
            echo "❌ خطأ: " . $e->getMessage() . "\n";
            echo "📍 السطر: " . $e->getLine() . "\n";
            echo "📁 الملف: " . $e->getFile() . "\n";
        }
    }
    
    /**
     * تنظيف البيانات القديمة
     */
    private function cleanOldData()
    {
        echo "🧹 تنظيف البيانات القديمة لتاريخ اليوم...\n";
        
        // حذف تفاصيل الفواتير أولاً
        $invoicesToDelete = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', $this->today)
            ->pluck('FCTV_REF');
            
        if ($invoicesToDelete->isNotEmpty()) {
            DB::table('FACTURE_VNT_DETAIL')
                ->whereIn('FCTV_REF', $invoicesToDelete)
                ->delete();
        }
        
        // حذف المدفوعات المرتبطة بالعملاء
        DB::table('REGLEMENT')
            ->whereDate('REG_DATE', $this->today)
            ->delete();
            
        // حذف فواتير اليوم
        $deletedInvoices = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', $this->today)
            ->delete();
            
        if ($deletedInvoices > 0) {
            echo "  🗑️ تم حذف {$deletedInvoices} فاتورة قديمة\n";
        }
        
        // حذف المصروفات
        DB::table('DEPENSE')
            ->whereDate('DEP_DATE', $this->today)
            ->delete();
            
        echo "  ✅ تم التنظيف\n\n";
    }
    
    /**
     * إضافة البيانات الأساسية
     */
    private function addBasicData()
    {
        echo "⚙️ إضافة البيانات الأساسية...\n";
        
        // فئات العملاء
        $this->insertIfNotExists('CATEGORIE_CLIENT', 'CLTCAT_REF', [
            ['CLTCAT_REF' => 'CAT001', 'CLTCAT_LIBELLE' => 'عملاء عاديين'],
            ['CLTCAT_REF' => 'CAT002', 'CLTCAT_LIBELLE' => 'عملاء VIP'],
            ['CLTCAT_REF' => 'CAT003', 'CLTCAT_LIBELLE' => 'شركات']
        ]);
        
        // عائلات المنتجات
        $this->insertIfNotExists('FAMILLE', 'FAM_REF', [
            ['FAM_REF' => 'FAM001', 'FAM_LIB' => 'مشروبات ساخنة'],
            ['FAM_REF' => 'FAM002', 'FAM_LIB' => 'مشروبات باردة'],
            ['FAM_REF' => 'FAM003', 'FAM_LIB' => 'وجبات رئيسية'],
            ['FAM_REF' => 'FAM004', 'FAM_LIB' => 'حلويات ومعجنات']
        ]);
        
        // فئات فرعية
        $this->insertIfNotExists('SOUS_FAMILLE', 'SFM_REF', [
            ['SFM_REF' => 'SFM001', 'FAM_REF' => 'FAM001', 'SFM_LIB' => 'قهوة وشاي'],
            ['SFM_REF' => 'SFM002', 'FAM_REF' => 'FAM002', 'SFM_LIB' => 'عصائر طبيعية'],
            ['SFM_REF' => 'SFM003', 'FAM_REF' => 'FAM003', 'SFM_LIB' => 'برغر وساندويتش'],
            ['SFM_REF' => 'SFM004', 'FAM_REF' => 'FAM004', 'SFM_LIB' => 'كعك وبسكويت']
        ]);
        
        // وحدات القياس
        $this->insertIfNotExists('UNITE_MESURE', 'UNM_ABR', [
            ['UNM_ABR' => 'PC', 'UNM_LIB' => 'قطعة'],
            ['UNM_ABR' => 'KG', 'UNM_LIB' => 'كيلوغرام'],
            ['UNM_ABR' => 'L', 'UNM_LIB' => 'لتر']
        ]);
        
        // المخزن الرئيسي
        $this->insertIfNotExists('ENTREPOT', 'ETP_REF', [
            ['ETP_REF' => 'ETP001', 'ETP_LIBELLE' => 'المخزن الرئيسي']
        ]);
        
        // المناطق
        $this->insertIfNotExists('ZONE', 'ZON_REF', [
            ['ZON_REF' => 'ZON001', 'ZON_LIB' => 'الصالة الرئيسية'],
            ['ZON_REF' => 'ZON002', 'ZON_LIB' => 'التراس الخارجي']
        ]);
        
        echo "  ✅ تمت إضافة البيانات الأساسية\n\n";
    }
    
    /**
     * إضافة العملاء
     */
    private function addCustomers()
    {
        echo "👥 إضافة عملاء متنوعين...\n";
        
        $customers = [
            // عملاء دائمين
            [
                'CLT_REF' => 'CLT001_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'أحمد محمد العلي',
                'CLT_TELEPHONE' => '0612345678',
                'CLT_EMAIL' => 'ahmed.ali@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 150,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT002_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT002',
                'CLT_CLIENT' => 'فاطمة الزهراء محمد',
                'CLT_TELEPHONE' => '0623456789',
                'CLT_EMAIL' => 'fatima.zahra@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 350,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT003_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT003',
                'CLT_CLIENT' => 'شركة النور للتجارة',
                'CLT_TELEPHONE' => '0534567890',
                'CLT_EMAIL' => 'info@alnoor-trade.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 500,
                'CLT_FIDELE' => 1,
                'CLT_ISENTREPRISE' => 1,
                'CLT_RAISONSOCIAL' => 'شركة النور للتجارة العامة',
                'CLT_CREDIT' => 0.00
            ],
            // عملاء إضافيين لمحاكاة يوم حافل
            [
                'CLT_REF' => 'CLT004_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'يوسف بن عبدالله',
                'CLT_TELEPHONE' => '0645678901',
                'CLT_EMAIL' => 'youssef.abdullah@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 75,
                'CLT_FIDELE' => 0,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT005_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT002',
                'CLT_CLIENT' => 'مريم الإدريسي',
                'CLT_TELEPHONE' => '0656789012',
                'CLT_EMAIL' => 'mariam.idrissi@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 220,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT006_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'خالد الرشيد',
                'CLT_TELEPHONE' => '0667890123',
                'CLT_EMAIL' => 'khalid.rachid@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 120,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT007_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT002',
                'CLT_CLIENT' => 'سارة بنت المغرب',
                'CLT_TELEPHONE' => '0678901234',
                'CLT_EMAIL' => 'sara.bentalmaghrib@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 450,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT008_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT003',
                'CLT_CLIENT' => 'مؤسسة الأطلس الكبير',
                'CLT_TELEPHONE' => '0589012345',
                'CLT_EMAIL' => 'contact@atlas-group.ma',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 800,
                'CLT_FIDELE' => 1,
                'CLT_ISENTREPRISE' => 1,
                'CLT_RAISONSOCIAL' => 'مؤسسة الأطلس الكبير للخدمات',
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT009_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'عمر الفاسي',
                'CLT_TELEPHONE' => '0590123456',
                'CLT_EMAIL' => 'omar.fassi@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 85,
                'CLT_FIDELE' => 0,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT010_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'زينب الأندلسي',
                'CLT_TELEPHONE' => '0601234567',
                'CLT_EMAIL' => 'zainab.andalusi@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 300,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ]
        ];
        
        foreach ($customers as $customer) {
            $exists = DB::table('CLIENT')->where('CLT_CLIENT', $customer['CLT_CLIENT'])->exists();
            if (!$exists) {
                DB::table('CLIENT')->insert($customer);
                echo "  ✅ " . $customer['CLT_CLIENT'] . "\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * إضافة المنتجات
     */
    private function addProducts()
    {
        echo "📦 إضافة منتجات جديدة...\n";
        
        $products = [
            // مشروبات ساخنة
            [
                'ART_REF' => 'ART001_' . date('Ymd'),
                'SFM_REF' => 'SFM001',
                'ART_DESIGNATION' => 'قهوة أمريكية',
                'ART_PRIX_ACHAT' => 8.00,
                'ART_PRIX_VENTE' => 15.00,
                'ART_PRIX_ACHAT_HT' => 7.50,
                'ART_PRIX_VENTE_HT' => 14.00,
                'ART_LIBELLE_CAISSE' => 'قهوة أمريكية',
                'ART_LIBELLE_ARABE' => 'قهوة أمريكية',
                'ART_STOCK_MIN' => 10,
                'ART_STOCK_MAX' => 100,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 1
            ],
            [
                'ART_REF' => 'ART002_' . date('Ymd'),
                'SFM_REF' => 'SFM001',
                'ART_DESIGNATION' => 'كابتشينو',
                'ART_PRIX_ACHAT' => 10.00,
                'ART_PRIX_VENTE' => 18.00,
                'ART_PRIX_ACHAT_HT' => 9.50,
                'ART_PRIX_VENTE_HT' => 17.00,
                'ART_LIBELLE_CAISSE' => 'كابتشينو',
                'ART_LIBELLE_ARABE' => 'كابتشينو',
                'ART_STOCK_MIN' => 5,
                'ART_STOCK_MAX' => 50,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 2
            ],
            [
                'ART_REF' => 'ART003_' . date('Ymd'),
                'SFM_REF' => 'SFM001',
                'ART_DESIGNATION' => 'لاتيه',
                'ART_PRIX_ACHAT' => 12.00,
                'ART_PRIX_VENTE' => 20.00,
                'ART_PRIX_ACHAT_HT' => 11.50,
                'ART_PRIX_VENTE_HT' => 19.00,
                'ART_LIBELLE_CAISSE' => 'لاتيه',
                'ART_LIBELLE_ARABE' => 'لاتيه',
                'ART_STOCK_MIN' => 5,
                'ART_STOCK_MAX' => 50,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 3
            ],
            // عصائر
            [
                'ART_REF' => 'ART004_' . date('Ymd'),
                'SFM_REF' => 'SFM002',
                'ART_DESIGNATION' => 'عصير برتقال طبيعي',
                'ART_PRIX_ACHAT' => 6.00,
                'ART_PRIX_VENTE' => 12.00,
                'ART_PRIX_ACHAT_HT' => 5.50,
                'ART_PRIX_VENTE_HT' => 11.00,
                'ART_LIBELLE_CAISSE' => 'عصير برتقال',
                'ART_LIBELLE_ARABE' => 'عصير برتقال',
                'ART_STOCK_MIN' => 10,
                'ART_STOCK_MAX' => 80,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 4
            ],
            // وجبات
            [
                'ART_REF' => 'ART005_' . date('Ymd'),
                'SFM_REF' => 'SFM003',
                'ART_DESIGNATION' => 'برغر دجاج',
                'ART_PRIX_ACHAT' => 25.00,
                'ART_PRIX_VENTE' => 45.00,
                'ART_PRIX_ACHAT_HT' => 23.00,
                'ART_PRIX_VENTE_HT' => 42.00,
                'ART_LIBELLE_CAISSE' => 'برغر دجاج',
                'ART_LIBELLE_ARABE' => 'برغر دجاج',
                'ART_STOCK_MIN' => 2,
                'ART_STOCK_MAX' => 20,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 5
            ],
            [
                'ART_REF' => 'ART006_' . date('Ymd'),
                'SFM_REF' => 'SFM003',
                'ART_DESIGNATION' => 'ساندويتش تونة',
                'ART_PRIX_ACHAT' => 12.00,
                'ART_PRIX_VENTE' => 22.00,
                'ART_PRIX_ACHAT_HT' => 11.00,
                'ART_PRIX_VENTE_HT' => 20.50,
                'ART_LIBELLE_CAISSE' => 'ساندويتش تونة',
                'ART_LIBELLE_ARABE' => 'ساندويتش تونة',
                'ART_STOCK_MIN' => 3,
                'ART_STOCK_MAX' => 30,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 6
            ],
            // حلويات
            [
                'ART_REF' => 'ART007_' . date('Ymd'),
                'SFM_REF' => 'SFM004',
                'ART_DESIGNATION' => 'كعكة الشوكولاتة',
                'ART_PRIX_ACHAT' => 8.00,
                'ART_PRIX_VENTE' => 16.00,
                'ART_PRIX_ACHAT_HT' => 7.50,
                'ART_PRIX_VENTE_HT' => 15.00,
                'ART_LIBELLE_CAISSE' => 'كعكة شوكولاتة',
                'ART_LIBELLE_ARABE' => 'كعكة شوكولاتة',
                'ART_STOCK_MIN' => 5,
                'ART_STOCK_MAX' => 40,
                'ART_STOCKABLE' => 1,
                'ART_VENTE' => 1,
                'ART_TVA_VENTE' => 20.0,
                'UNM_ABR' => 'PC',
                'ART_ORDRE_AFFICHAGE' => 7
            ]
        ];
        
        foreach ($products as $product) {
            $exists = DB::table('ARTICLE')->where('ART_DESIGNATION', $product['ART_DESIGNATION'])->exists();
            if (!$exists) {
                DB::table('ARTICLE')->insert($product);
                echo "  ✅ " . $product['ART_DESIGNATION'] . " - " . $product['ART_PRIX_VENTE'] . " DH\n";
                
                // إضافة كمية مخزون
                $stockQuantity = rand(5, 50);
                if ($product['ART_DESIGNATION'] === 'برغر دجاج') {
                    $stockQuantity = 2; // لمحاكاة نفاد المخزون
                }
                
                DB::table('STOCK')->insert([
                    'ART_REF' => $product['ART_REF'],
                    'ETP_REF' => 'ETP001',
                    'STK_QTE' => $stockQuantity
                ]);
            }
        }
        
        echo "\n";
    }
    
    /**
     * إضافة المبيعات (محاكاة يوم عمل كامل)
     */
    private function addSales()
    {
        echo "💰 إضافة مبيعات يوم عمل كامل...\n";
        
        $customers = DB::table('CLIENT')->where('CLT_REF', 'like', '%' . date('Ymd') . '%')->get();
        $products = DB::table('ARTICLE')->where('ART_REF', 'like', '%' . date('Ymd') . '%')->get();
        
        if ($customers->isEmpty() || $products->isEmpty()) {
            echo "  ⚠️ لا توجد عملاء أو منتجات\n";
            return;
        }
        
        // ساعات العمل من 7 صباحاً إلى 11 مساءً مع توزيع واقعي
        $workingHours = [
            7 => ['min_sales' => 2, 'max_sales' => 4, 'description' => 'افتتاح - فطار مبكر'],
            8 => ['min_sales' => 5, 'max_sales' => 8, 'description' => 'ذروة الفطار'],
            9 => ['min_sales' => 4, 'max_sales' => 7, 'description' => 'فطار متأخر'],
            10 => ['min_sales' => 3, 'max_sales' => 5, 'description' => 'هدوء صباحي'],
            11 => ['min_sales' => 2, 'max_sales' => 4, 'description' => 'استراحة الضحى'],
            12 => ['min_sales' => 8, 'max_sales' => 12, 'description' => 'بداية الغداء'],
            13 => ['min_sales' => 10, 'max_sales' => 15, 'description' => 'ذروة الغداء'],
            14 => ['min_sales' => 8, 'max_sales' => 12, 'description' => 'غداء متأخر'],
            15 => ['min_sales' => 4, 'max_sales' => 7, 'description' => 'استراحة العصر'],
            16 => ['min_sales' => 5, 'max_sales' => 8, 'description' => 'قهوة العصر'],
            17 => ['min_sales' => 6, 'max_sales' => 9, 'description' => 'نهاية الدوام'],
            18 => ['min_sales' => 7, 'max_sales' => 10, 'description' => 'مقبلات العشاء'],
            19 => ['min_sales' => 12, 'max_sales' => 18, 'description' => 'ذروة العشاء المبكر'],
            20 => ['min_sales' => 15, 'max_sales' => 20, 'description' => 'ذروة العشاء'],
            21 => ['min_sales' => 10, 'max_sales' => 15, 'description' => 'عشاء متأخر'],
            22 => ['min_sales' => 5, 'max_sales' => 8, 'description' => 'مشروبات ليلية'],
            23 => ['min_sales' => 2, 'max_sales' => 4, 'description' => 'إغلاق متأخر']
        ];
        
        $totalSales = 0;
        $invoiceCount = 0;
        
        foreach ($workingHours as $hour => $settings) {
            $salesInHour = rand($settings['min_sales'], $settings['max_sales']);
            echo "  🕐 الساعة {$hour}:00 - {$settings['description']} ({$salesInHour} عملية بيع)\n";
            
            for ($i = 0; $i < $salesInHour; $i++) {
                $invoiceRef = 'FCTV_' . date('Ymd') . '_' . sprintf('%03d', $invoiceCount + 1);
                $customer = $customers->random();
                $dateTime = $this->today->copy()->setHour($hour)->setMinute(rand(0, 59))->setSecond(rand(0, 59));
                
                $totalHT = 0;
                $totalTTC = 0;
                
                // اختيار منتجات حسب وقت اليوم
                $numProducts = $this->getProductCountByHour($hour);
                $selectedProducts = $this->selectProductsByHour($products, $hour, $numProducts);
                
                $details = [];
                
                foreach ($selectedProducts as $product) {
                    $quantity = $this->getQuantityByHour($hour);
                    $priceHT = $product->ART_PRIX_VENTE_HT;
                    $priceTTC = $product->ART_PRIX_VENTE;
                    
                    $details[] = [
                        'FCTV_REF' => $invoiceRef,
                        'ART_REF' => $product->ART_REF,
                        'FVD_QTE' => $quantity,
                        'FVD_PRIX_VNT_HT' => $priceHT,
                        'FVD_PRIX_VNT_TTC' => $priceTTC,
                        'FVD_TVA' => 20.0,
                        'FVD_REMISE' => 0.0,
                        'FVD_COLISAGE' => 1.0,
                        'FVD_NBR_COLIS' => 1.0,
                        'FVD_NBR__GRATUITE' => 0.0,
                        'FVD_NUMBL' => ''
                    ];
                    
                    $totalHT += $priceHT * $quantity;
                    $totalTTC += $priceTTC * $quantity;
                }
                
                // إدراج الفاتورة
                DB::table('FACTURE_VNT')->insert([
                    'FCTV_REF' => $invoiceRef,
                    'CLT_REF' => $customer->CLT_REF,
                    'ETP_REF' => 'ETP001',
                    'FCTV_NUMERO' => 'F' . date('Ymd') . sprintf('%04d', $invoiceCount + 1),
                    'FCTV_DATE' => $dateTime->format('Y-m-d H:i:s'),
                    'FCTV_MNT_HT' => $totalHT,
                    'FCTV_MNT_TTC' => $totalTTC,
                    'FCTV_REMISE' => 0.0,
                    'FCTV_VALIDE' => 1,
                    'FCTV_EXONORE' => 0,
                    'FCTV_UTILISATEUR' => 'ADMIN',
                    'FCT_MNT_TOTAL' => $totalTTC,
                    'FCT_MNT_RGL' => $totalTTC
                ]);
                
                // إدراج تفاصيل الفاتورة
                foreach ($details as $detail) {
                    DB::table('FACTURE_VNT_DETAIL')->insert($detail);
                }
                
                $totalSales += $totalTTC;
                $invoiceCount++;
            }
        }
        
        echo "  ✅ تم إضافة {$invoiceCount} فاتورة بإجمالي: " . number_format($totalSales, 2) . " DH\n";
        echo "  📈 متوسط الفاتورة: " . number_format($totalSales / $invoiceCount, 2) . " DH\n\n";
    }
    
    /**
     * تحديد عدد المنتجات حسب الساعة
     */
    private function getProductCountByHour($hour)
    {
        if ($hour >= 7 && $hour <= 9) return rand(1, 2); // فطار - منتجات قليلة
        if ($hour >= 12 && $hour <= 14) return rand(2, 4); // غداء - وجبات كاملة
        if ($hour >= 19 && $hour <= 21) return rand(2, 5); // عشاء - أكبر طلبات
        return rand(1, 3); // باقي الأوقات
    }
    
    /**
     * اختيار المنتجات حسب وقت اليوم
     */
    private function selectProductsByHour($products, $hour, $count)
    {
        $preferred = collect(); // استخدام collect() بدلاً من []
        
        if ($hour >= 7 && $hour <= 10) {
            // ساعات الفطار - تفضيل المشروبات الساخنة
            $preferred = $products->filter(function($p) {
                return strpos($p->ART_DESIGNATION, 'قهوة') !== false || 
                       strpos($p->ART_DESIGNATION, 'كابتشينو') !== false ||
                       strpos($p->ART_DESIGNATION, 'لاتيه') !== false;
            });
        } elseif ($hour >= 12 && $hour <= 15) {
            // ساعات الغداء - تفضيل الوجبات
            $preferred = $products->filter(function($p) {
                return strpos($p->ART_DESIGNATION, 'برغر') !== false || 
                       strpos($p->ART_DESIGNATION, 'ساندويتش') !== false;
            });
        } elseif ($hour >= 16 && $hour <= 18) {
            // العصر - مشروبات وحلويات
            $preferred = $products->filter(function($p) {
                return strpos($p->ART_DESIGNATION, 'عصير') !== false || 
                       strpos($p->ART_DESIGNATION, 'كعكة') !== false ||
                       strpos($p->ART_DESIGNATION, 'قهوة') !== false;
            });
        }
        
        // إذا لم نجد منتجات مفضلة، استخدم كل المنتجات
        $availableProducts = $preferred->isNotEmpty() ? $preferred : $products;
        
        return $availableProducts->random(min($count, $availableProducts->count()));
    }
    
    /**
     * تحديد الكمية حسب الساعة
     */
    private function getQuantityByHour($hour)
    {
        if ($hour >= 19 && $hour <= 21) return rand(1, 4); // ذروة العشاء - كميات أكبر
        if ($hour >= 12 && $hour <= 14) return rand(1, 3); // الغداء
        return rand(1, 2); // باقي الأوقات
    }
    
    /**
     * إضافة المدفوعات
     */
    private function addPayments()
    {
        echo "💳 إضافة مدفوعات متنوعة...\n";
        
        $invoices = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', $this->today)
            ->get();
        
        $paymentMethods = ['ESPECES', 'CARTE', 'CHEQUE', 'VIREMENT'];
        $methodCounts = ['ESPECES' => 0, 'CARTE' => 0, 'CHEQUE' => 0, 'VIREMENT' => 0];
        
        foreach ($invoices as $invoice) {
            $method = $paymentMethods[array_rand($paymentMethods)];
            $methodCounts[$method]++;
            
            DB::table('REGLEMENT')->insert([
                'REG_REF' => 'REG_' . $invoice->FCTV_REF,
                'CLT_REF' => $invoice->CLT_REF,
                'REG_MONTANT' => $invoice->FCTV_MNT_TTC,
                'TYPE_REGLEMENT' => $method,
                'REG_DATE' => $invoice->FCTV_DATE
            ]);
        }
        
        foreach ($methodCounts as $method => $count) {
            if ($count > 0) {
                echo "  ✅ {$method}: {$count} دفعة\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * إضافة بيانات المطعم
     */
    private function addRestaurantData()
    {
        echo "🍽️ إضافة بيانات المطعم...\n";
        
        // الطاولات
        $tables = [
            ['TAB_REF' => 'TAB001', 'TAB_LIB' => 'طاولة 1', 'ZON_REF' => 'ZON001', 'TAB_NBR_Couvert' => 4, 'ETT_ETAT' => 'LIBRE'],
            ['TAB_REF' => 'TAB002', 'TAB_LIB' => 'طاولة 2', 'ZON_REF' => 'ZON001', 'TAB_NBR_Couvert' => 2, 'ETT_ETAT' => 'OCCUPEE'],
            ['TAB_REF' => 'TAB003', 'TAB_LIB' => 'طاولة 3', 'ZON_REF' => 'ZON001', 'TAB_NBR_Couvert' => 6, 'ETT_ETAT' => 'RESERVEE'],
            ['TAB_REF' => 'TAB004', 'TAB_LIB' => 'طاولة 4', 'ZON_REF' => 'ZON001', 'TAB_NBR_Couvert' => 4, 'ETT_ETAT' => 'LIBRE'],
            ['TAB_REF' => 'TAB005', 'TAB_LIB' => 'طاولة 5', 'ZON_REF' => 'ZON002', 'TAB_NBR_Couvert' => 8, 'ETT_ETAT' => 'OCCUPEE'],
            ['TAB_REF' => 'TAB006', 'TAB_LIB' => 'طاولة 6', 'ZON_REF' => 'ZON002', 'TAB_NBR_Couvert' => 4, 'ETT_ETAT' => 'LIBRE']
        ];
        
        foreach ($tables as $table) {
            $this->insertIfNotExists('TABLE', 'TAB_REF', [$table]);
        }
        
        // الحجوزات
        $customers = DB::table('CLIENT')->limit(3)->get();
        if (!$customers->isEmpty()) {
            $reservations = [
                [
                    'RES_REF' => 'RES_' . date('Ymd') . '_001',
                    'CLT_REF' => $customers->first()->CLT_REF,
                    'NUMERO_RESERVATION' => 'RES' . date('Ymd') . '001',
                    'DATE_RESERVATION' => $this->today->copy()->setHour(19)->setMinute(30)->format('Y-m-d H:i:s'),
                    'NBRCOUVERT_TABLE' => 4,
                    'ETAT_RESERVATION' => 'CONFIRMEE',
                    'DELAI_RESERVATION' => 30
                ],
                [
                    'RES_REF' => 'RES_' . date('Ymd') . '_002',
                    'CLT_REF' => $customers->last()->CLT_REF,
                    'NUMERO_RESERVATION' => 'RES' . date('Ymd') . '002',
                    'DATE_RESERVATION' => $this->today->copy()->setHour(20)->setMinute(0)->format('Y-m-d H:i:s'),
                    'NBRCOUVERT_TABLE' => 6,
                    'ETAT_RESERVATION' => 'CONFIRMEE',
                    'DELAI_RESERVATION' => 60
                ]
            ];
            
            foreach ($reservations as $reservation) {
                $exists = DB::table('RESERVATION')->where('RES_REF', $reservation['RES_REF'])->exists();
                if (!$exists) {
                    DB::table('RESERVATION')->insert($reservation);
                    echo "  ✅ حجز رقم: " . $reservation['NUMERO_RESERVATION'] . "\n";
                }
            }
        }
        
        echo "\n";
    }
    
    /**
     * إضافة المصروفات
     */
    private function addExpenses()
    {
        echo "💸 إضافة مصروفات اليوم...\n";
        
        $expenses = [
            ['description' => 'فاتورة الكهرباء والماء', 'amount' => 485.50, 'time' => 9],
            ['description' => 'مواد تنظيف ومعقمات', 'amount' => 125.00, 'time' => 10],
            ['description' => 'صيانة آلة القهوة الإسبريسو', 'amount' => 380.00, 'time' => 11],
            ['description' => 'مشتريات مكتبية وأدوات كتابة', 'amount' => 95.50, 'time' => 12],
            ['description' => 'وقود للمولد الكهربائي', 'amount' => 220.00, 'time' => 13],
            ['description' => 'صيانة نظام التبريد', 'amount' => 450.00, 'time' => 14],
            ['description' => 'شراء مواد خام للمطبخ', 'amount' => 680.75, 'time' => 15],
            ['description' => 'خدمات الإنترنت والاتصالات', 'amount' => 199.00, 'time' => 16],
            ['description' => 'مصاريف النقل والتوصيل', 'amount' => 85.25, 'time' => 17],
            ['description' => 'إصلاح نظام الصوت', 'amount' => 320.00, 'time' => 18]
        ];
        
        $totalExpenses = 0;
        
        foreach ($expenses as $expense) {
            $expenseRef = 'DEP_' . date('Ymd') . '_' . uniqid();
            
            try {
                DB::table('DEPENSE')->insert([
                    'DEP_REF' => $expenseRef,
                    'DEP_DATE' => $this->today->copy()->setHour($expense['time'])->setMinute(rand(0, 59))->format('Y-m-d H:i:s'),
                    'DEP_MONTANTHT' => $expense['amount'],
                    'DEP_COMMENTAIRE' => $expense['description'],
                    'MTF_DPS_MOTIF' => 'CHARGES',
                    'CSS_ID_CAISSE' => 'CAISSE001'
                ]);
                
                $totalExpenses += $expense['amount'];
                echo "  ✅ " . $expense['description'] . ": " . number_format($expense['amount'], 2) . " DH\n";
                
            } catch (Exception $e) {
                echo "  ⚠️ تخطي المصروف: " . $expense['description'] . " (خطأ: " . $e->getMessage() . ")\n";
            }
        }
        
        echo "  📊 إجمالي المصروفات: " . number_format($totalExpenses, 2) . " DH\n\n";
    }
    
    /**
     * إضافة إشعارات
     */
    private function addNotifications()
    {
        echo "🔔 إضافة إشعارات النظام...\n";
        
        // محاكاة إشعارات المخزون المنخفض
        $lowStockItems = DB::table('ARTICLE as a')
            ->join('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->where('s.STK_QTE', '<=', 5)
            ->get();
            
        echo "  ⚠️ تم اكتشاف " . count($lowStockItems) . " منتج بمخزون منخفض\n";
        echo "  📈 تم إنشاء " . DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $this->today)->count() . " فاتورة اليوم\n";
        echo "  🎯 نظام الإشعارات نشط ومتاح\n\n";
    }
    
    /**
     * عرض ملخص البيانات
     */
    private function showSummary()
    {
        echo "📊 ملخص البيانات المضافة:\n";
        echo "========================\n";
        
        $summary = [
            'العملاء' => DB::table('CLIENT')->where('CLT_REF', 'like', '%' . date('Ymd') . '%')->count(),
            'المنتجات' => DB::table('ARTICLE')->where('ART_REF', 'like', '%' . date('Ymd') . '%')->count(),
            'فواتير اليوم' => DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $this->today)->count(),
            'إجمالي المبيعات' => number_format(DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $this->today)->sum('FCTV_MNT_TTC'), 2) . ' DH',
            'المدفوعات' => DB::table('REGLEMENT')->whereDate('REG_DATE', $this->today)->count(),
            'المصروفات' => number_format(DB::table('DEPENSE')->whereDate('DEP_DATE', $this->today)->sum('DEP_MONTANTHT'), 2) . ' DH',
            'الطاولات' => DB::table('TABLE')->count(),
            'الحجوزات' => DB::table('RESERVATION')->whereDate('DATE_RESERVATION', $this->today)->count()
        ];
        
        foreach ($summary as $item => $value) {
            echo "  📈 {$item}: {$value}\n";
        }
        
        echo "\n🎯 النظام جاهز للاختبار!\n";
        echo "💻 يمكنك الآن الوصول إلى: /admin/tableau-de-bord-moderne\n";
    }
    
    /**
     * مساعد للإدراج إذا لم يكن موجوداً
     */
    private function insertIfNotExists($table, $key, $data)
    {
        foreach ($data as $row) {
            $exists = DB::table($table)->where($key, $row[$key])->exists();
            if (!$exists) {
                DB::table($table)->insert($row);
            }
        }
    }
}

// تشغيل المولد
try {
    $generator = new DemoDataGenerator();
    $generator->run();
} catch (Exception $e) {
    echo "❌ خطأ في تشغيل المولد: " . $e->getMessage() . "\n";
    exit(1);
}
?>
