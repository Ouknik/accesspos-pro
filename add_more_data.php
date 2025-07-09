<?php
/**
 * سكريپت إضافة بيانات تجريبية إضافية لليوم الحالي
 * AccessPOS Pro - Additional Demo Data Generator
 * 
 * هذا السكريپت يضيف بيانات إضافية لمحاكاة يوم مزدحم أكثر
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class AdditionalDataGenerator
{
    private $today;
    
    public function __construct()
    {
        $this->today = Carbon::parse('2025-07-09');
    }
    
    public function run()
    {
        echo "🚀 إضافة بيانات تجريبية إضافية لليوم الحالي\n";
        echo "================================================\n";
        echo "📅 التاريخ: " . $this->today->format('Y-m-d') . "\n\n";
        
        try {
            // 1. إضافة عملاء جدد
            $this->addMoreCustomers();
            
            // 2. إضافة مبيعات إضافية
            $this->addMoreSales();
            
            // 3. إضافة مصروفات
            $this->addExpenses();
            
            // 4. إضافة حجوزات
            $this->addReservations();
            
            // عرض ملخص البيانات
            $this->showSummary();
            
        } catch (Exception $e) {
            echo "❌ خطأ: " . $e->getMessage() . "\n";
        }
    }
    
    private function addMoreCustomers()
    {
        echo "👥 إضافة عملاء إضافيين...\n";
        
        $newCustomers = [
            [
                'CLT_REF' => 'CLT_NEW_001_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'سارة الغامدي',
                'CLT_TELEPHONE' => '0567890123',
                'CLT_EMAIL' => 'sara.alghamdi@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 80,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT_NEW_002_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT002',
                'CLT_CLIENT' => 'خالد البراك',
                'CLT_TELEPHONE' => '0578901234',
                'CLT_EMAIL' => 'khalid.albarrak@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 420,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT_NEW_003_' . date('Ymd'),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'نورا القحطاني',
                'CLT_TELEPHONE' => '0589012345',
                'CLT_EMAIL' => 'nora.alqahtani@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 95,
                'CLT_FIDELE' => 0,
                'CLT_CREDIT' => 0.00
            ]
        ];
        
        foreach ($newCustomers as $customer) {
            $exists = DB::table('CLIENT')->where('CLT_CLIENT', $customer['CLT_CLIENT'])->exists();
            if (!$exists) {
                DB::table('CLIENT')->insert($customer);
                echo "  ✅ " . $customer['CLT_CLIENT'] . "\n";
            }
        }
        echo "\n";
    }
    
    private function addMoreSales()
    {
        echo "💰 إضافة مبيعات إضافية لمحاكاة يوم مزدحم...\n";
        
        $allCustomers = DB::table('CLIENT')->get();
        $allProducts = DB::table('ARTICLE')->get();
        
        if ($allCustomers->isEmpty() || $allProducts->isEmpty()) {
            echo "  ⚠️ لا توجد عملاء أو منتجات\n";
            return;
        }
        
        // إضافة 50 فاتورة إضافية موزعة على ساعات اليوم
        $additionalSales = 50;
        $totalAmount = 0;
        
        // الحصول على آخر رقم فاتورة
        $lastInvoice = DB::table('FACTURE_VNT')
            ->where('FCTV_REF', 'like', 'FCTV_' . date('Ymd') . '_%')
            ->orderBy('FCTV_REF', 'desc')
            ->first();
        
        $invoiceCounter = 1;
        if ($lastInvoice) {
            $parts = explode('_', $lastInvoice->FCTV_REF);
            $invoiceCounter = intval(end($parts)) + 1;
        }
        
        for ($i = 0; $i < $additionalSales; $i++) {
            $hour = rand(7, 23);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            
            $invoiceRef = 'FCTV_' . date('Ymd') . '_' . sprintf('%03d', $invoiceCounter);
            $customer = $allCustomers->random();
            $dateTime = $this->today->copy()->setHour($hour)->setMinute($minute)->setSecond($second);
            
            // اختيار منتجات عشوائية
            $numProducts = rand(1, 4);
            $selectedProducts = $allProducts->random($numProducts);
            
            $totalHT = 0;
            $totalTTC = 0;
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $priceHT = $product->ART_PRIX_VENTE_HT ?? $product->ART_PRIX_VENTE * 0.8;
                $priceTTC = $product->ART_PRIX_VENTE;
                
                DB::table('FACTURE_VNT_DETAIL')->insert([
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
                ]);
                
                $totalHT += $priceHT * $quantity;
                $totalTTC += $priceTTC * $quantity;
            }
            
            // إدراج الفاتورة
            DB::table('FACTURE_VNT')->insert([
                'FCTV_REF' => $invoiceRef,
                'CLT_REF' => $customer->CLT_REF,
                'ETP_REF' => 'ETP001',
                'FCTV_NUMERO' => 'F' . date('Ymd') . sprintf('%04d', $invoiceCounter),
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
            
            // إضافة المدفوعة
            $paymentMethods = ['ESPECES', 'CARTE', 'CHEQUE', 'VIREMENT'];
            $method = $paymentMethods[array_rand($paymentMethods)];
            
            DB::table('REGLEMENT')->insert([
                'REG_REF' => 'REG_' . $invoiceRef,
                'CLT_REF' => $customer->CLT_REF,
                'REG_MONTANT' => $totalTTC,
                'TYPE_REGLEMENT' => $method,
                'REG_DATE' => $dateTime->format('Y-m-d H:i:s')
            ]);
            
            $totalAmount += $totalTTC;
            $invoiceCounter++;
        }
        
        echo "  ✅ تم إضافة {$additionalSales} فاتورة إضافية بقيمة: " . number_format($totalAmount, 2) . " DH\n\n";
    }
    
    private function addExpenses()
    {
        echo "💸 إضافة مصروفات اليوم...\n";
        
        $expenses = [
            [
                'DEP_REF' => 'DEP_' . date('Ymd') . '_001',
                'DEP_DATE' => $this->today->copy()->setHour(8)->setMinute(30)->format('Y-m-d H:i:s'),
                'DEP_MONTANTHT' => 150.00,
                'DEP_COMMENTAIRE' => 'شراء مواد تنظيف',
                'MTF_DPS_MOTIF' => 'مصروفات تشغيلية',
                'CSS_ID_CAISSE' => 'CAISSE001'
            ],
            [
                'DEP_REF' => 'DEP_' . date('Ymd') . '_002',
                'DEP_DATE' => $this->today->copy()->setHour(11)->setMinute(15)->format('Y-m-d H:i:s'),
                'DEP_MONTANTHT' => 300.00,
                'DEP_COMMENTAIRE' => 'صيانة معدات المطبخ',
                'MTF_DPS_MOTIF' => 'صيانة وإصلاح',
                'CSS_ID_CAISSE' => 'CAISSE001'
            ],
            [
                'DEP_REF' => 'DEP_' . date('Ymd') . '_003',
                'DEP_DATE' => $this->today->copy()->setHour(14)->setMinute(45)->format('Y-m-d H:i:s'),
                'DEP_MONTANTHT' => 75.00,
                'DEP_COMMENTAIRE' => 'شراء مستلزمات مكتبية',
                'MTF_DPS_MOTIF' => 'مصروفات إدارية',
                'CSS_ID_CAISSE' => 'CAISSE001'
            ],
            [
                'DEP_REF' => 'DEP_' . date('Ymd') . '_004',
                'DEP_DATE' => $this->today->copy()->setHour(17)->setMinute(20)->format('Y-m-d H:i:s'),
                'DEP_MONTANTHT' => 450.00,
                'DEP_COMMENTAIRE' => 'شراء مواد خام للمطبخ',
                'MTF_DPS_MOTIF' => 'مشتريات',
                'CSS_ID_CAISSE' => 'CAISSE001'
            ]
        ];
        
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
            $exists = DB::table('DEPENSE')->where('DEP_REF', $expense['DEP_REF'])->exists();
            if (!$exists) {
                DB::table('DEPENSE')->insert($expense);
                $totalExpenses += $expense['DEP_MONTANTHT'];
                echo "  ✅ " . $expense['DEP_COMMENTAIRE'] . " - " . $expense['DEP_MONTANTHT'] . " DH\n";
            }
        }
        
        echo "  💰 إجمالي المصروفات: " . number_format($totalExpenses, 2) . " DH\n\n";
    }
    
    private function addReservations()
    {
        echo "📅 إضافة حجوزات للأيام القادمة...\n";
        
        $customers = DB::table('CLIENT')->limit(5)->get();
        if ($customers->isEmpty()) {
            echo "  ⚠️ لا توجد عملاء للحجوزات\n\n";
            return;
        }
        
        $reservations = [
            [
                'RES_REF' => 'RES_' . date('Ymd') . '_001',
                'CLT_REF' => $customers->random()->CLT_REF,
                'NUMERO_RESERVATION' => 'R' . date('Ymd') . '001',
                'NBRCOUVERT_TABLE' => 4,
                'DATE_RESERVATION' => $this->today->copy()->addDay()->setHour(19)->setMinute(30)->format('Y-m-d H:i:s'),
                'HEURE_RESERVATION' => '19:30',
                'REMARQUE_RESERVATION' => 'حجز عشاء عائلي'
            ],
            [
                'RES_REF' => 'RES_' . date('Ymd') . '_002',
                'CLT_REF' => $customers->random()->CLT_REF,
                'NUMERO_RESERVATION' => 'R' . date('Ymd') . '002',
                'NBRCOUVERT_TABLE' => 2,
                'DATE_RESERVATION' => $this->today->copy()->addDay()->setHour(12)->setMinute(0)->format('Y-m-d H:i:s'),
                'HEURE_RESERVATION' => '12:00',
                'REMARQUE_RESERVATION' => 'غداء عمل'
            ],
            [
                'RES_REF' => 'RES_' . date('Ymd') . '_003',
                'CLT_REF' => $customers->random()->CLT_REF,
                'NUMERO_RESERVATION' => 'R' . date('Ymd') . '003',
                'NBRCOUVERT_TABLE' => 6,
                'DATE_RESERVATION' => $this->today->copy()->addDays(2)->setHour(20)->setMinute(0)->format('Y-m-d H:i:s'),
                'HEURE_RESERVATION' => '20:00',
                'REMARQUE_RESERVATION' => 'مناسبة خاصة'
            ]
        ];
        
        foreach ($reservations as $reservation) {
            $exists = DB::table('RESERVATION')->where('RES_REF', $reservation['RES_REF'])->exists();
            if (!$exists) {
                DB::table('RESERVATION')->insert($reservation);
                echo "  ✅ " . $reservation['REMARQUE_RESERVATION'] . " - " . $reservation['DATE_RESERVATION'] . "\n";
            }
        }
        echo "\n";
    }
    
    private function showSummary()
    {
        echo "📊 ملخص البيانات الحالية:\n";
        echo "==========================\n";
        
        $today = '2025-07-09';
        
        $invoices = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->count();
        $sales = DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->sum('FCTV_MNT_TTC');
        $payments = DB::table('REGLEMENT')->whereDate('REG_DATE', $today)->count();
        $expenses = DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->sum('DEP_MONTANTHT');
        $customers = DB::table('CLIENT')->count();
        $products = DB::table('ARTICLE')->count();
        
        echo "📋 فواتير اليوم: {$invoices}\n";
        echo "💰 إجمالي المبيعات: " . number_format($sales, 2) . " DH\n";
        echo "💳 عدد المدفوعات: {$payments}\n";
        echo "💸 إجمالي المصروفات: " . number_format($expenses, 2) . " DH\n";
        echo "👥 إجمالي العملاء: {$customers}\n";
        echo "📦 إجمالي المنتجات: {$products}\n";
        
        if ($invoices > 0) {
            echo "\n🎉 تم! البيانات جاهزة لتظهر في لوحة القيادة.\n";
            echo "📈 متوسط الفاتورة: " . number_format($sales / $invoices, 2) . " DH\n";
        }
    }
}

// تشغيل المولد
$generator = new AdditionalDataGenerator();
$generator->run();
