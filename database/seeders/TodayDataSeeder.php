<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Seeder لإضافة بيانات تجريبية لتاريخ اليوم
 * يقوم بإضافة مبيعات، عملاء، مخزون، وبيانات مطعم للاختبار
 */
class TodayDataSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        echo "🚀 بدء إضافة البيانات التجريبية لتاريخ: " . $today->format('Y-m-d') . "\n";
        
        // 1. إضافة عملاء جدد
        $this->addClients();
        
        // 2. إضافة مقالات جديدة
        $this->addArticles();
        
        // 3. إضافة فواتير مبيعات اليوم
        $this->addSalesToday();
        
        // 4. إضافة حركات مخزون
        $this->addStockMovements();
        
        // 5. إضافة دفوعات متنوعة
        $this->addPayments();
        
        // 6. إضافة طاولات وحجوزات مطعم
        $this->addRestaurantData();
        
        // 7. إضافة مصاريف اليوم
        $this->addExpenses();
        
        echo "✅ تم إضافة جميع البيانات التجريبية بنجاح!\n";
    }
    
    /**
     * إضافة عملاء جدد
     */
    private function addClients()
    {
        echo "👥 إضافة عملاء جدد...\n";
        
        $clients = [
            [
                'CLT_REF' => 'CLT_' . uniqid(),
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
                'CLT_REF' => 'CLT_' . uniqid(),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'فاطمة الزهراء',
                'CLT_TELEPHONE' => '0623456789',
                'CLT_EMAIL' => 'fatima.zahra@email.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 230,
                'CLT_FIDELE' => 1,
                'CLT_CREDIT' => 0.00
            ],
            [
                'CLT_REF' => 'CLT_' . uniqid(),
                'CLTCAT_REF' => 'CAT001',
                'CLT_CLIENT' => 'شركة النور للتجارة',
                'CLT_TELEPHONE' => '0534567890',
                'CLT_EMAIL' => 'info@alnoor-trade.com',
                'CLT_BLOQUE' => 0,
                'CLT_POINTFIDILIO' => 500,
                'CLT_FIDELE' => 1,
                'CLT_ISENTREPRISE' => 1,
                'CLT_RAISONSOCIAL' => 'شركة النور للتجارة العامة',
                'CLT_CREDIT' => 0.00
            ]
        ];
        
        // تحقق من وجود فئة العملاء
        $categoryExists = DB::table('CATEGORIE_CLIENT')->where('CLTCAT_REF', 'CAT001')->exists();
        if (!$categoryExists) {
            DB::table('CATEGORIE_CLIENT')->insert([
                'CLTCAT_REF' => 'CAT001',
                'CLTCAT_LIBELLE' => 'عملاء عاديين'
            ]);
        }
        
        foreach ($clients as $client) {
            // تحقق من عدم وجود العميل مسبقاً
            $exists = DB::table('CLIENT')->where('CLT_CLIENT', $client['CLT_CLIENT'])->exists();
            if (!$exists) {
                DB::table('CLIENT')->insert($client);
                echo "  ✅ تمت إضافة العميل: " . $client['CLT_CLIENT'] . "\n";
            }
        }
    }
    
    /**
     * إضافة مقالات جديدة
     */
    private function addArticles()
    {
        echo "📦 إضافة مقالات جديدة...\n";
        
        // تحقق من وجود عائلة المنتجات
        $familyExists = DB::table('FAMILLE')->where('FAM_REF', 'FAM001')->exists();
        if (!$familyExists) {
            DB::table('FAMILLE')->insert([
                'FAM_REF' => 'FAM001',
                'FAM_DESIGNATION' => 'مشروبات ساخنة'
            ]);
        }
        
        $familyExists2 = DB::table('FAMILLE')->where('FAM_REF', 'FAM002')->exists();
        if (!$familyExists2) {
            DB::table('FAMILLE')->insert([
                'FAM_REF' => 'FAM002',
                'FAM_DESIGNATION' => 'وجبات رئيسية'
            ]);
        }
        
        // تحقق من وجود الفئة الفرعية
        $subFamilyExists = DB::table('SOUS_FAMILLE')->where('SFM_REF', 'SFM001')->exists();
        if (!$subFamilyExists) {
            DB::table('SOUS_FAMILLE')->insert([
                'SFM_REF' => 'SFM001',
                'FAM_REF' => 'FAM001',
                'SFM_DESIGNATION' => 'قهوة وشاي'
            ]);
        }
        
        $subFamilyExists2 = DB::table('SOUS_FAMILLE')->where('SFM_REF', 'SFM002')->exists();
        if (!$subFamilyExists2) {
            DB::table('SOUS_FAMILLE')->insert([
                'SFM_REF' => 'SFM002',
                'FAM_REF' => 'FAM002',
                'SFM_DESIGNATION' => 'برغر وساندويتش'
            ]);
        }
        
        $articles = [
            [
                'ART_REF' => 'ART_' . uniqid(),
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
                'ART_REF' => 'ART_' . uniqid(),
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
                'ART_REF' => 'ART_' . uniqid(),
                'SFM_REF' => 'SFM002',
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
                'ART_ORDRE_AFFICHAGE' => 3
            ],
            [
                'ART_REF' => 'ART_' . uniqid(),
                'SFM_REF' => 'SFM002',
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
                'ART_ORDRE_AFFICHAGE' => 4
            ]
        ];
        
        foreach ($articles as $article) {
            $exists = DB::table('ARTICLE')->where('ART_DESIGNATION', $article['ART_DESIGNATION'])->exists();
            if (!$exists) {
                DB::table('ARTICLE')->insert($article);
                echo "  ✅ تمت إضافة المقال: " . $article['ART_DESIGNATION'] . "\n";
                
                // إضافة كمية مخزون أولية
                DB::table('STOCK')->insert([
                    'ART_REF' => $article['ART_REF'],
                    'ETP_REF' => 'ETP001', // مخزن افتراضي
                    'STK_QUANTITE' => rand(5, 50)
                ]);
            }
        }
        
        // تحقق من وجود المخزن
        $entrepotExists = DB::table('ENTREPOT')->where('ETP_REF', 'ETP001')->exists();
        if (!$entrepotExists) {
            DB::table('ENTREPOT')->insert([
                'ETP_REF' => 'ETP001',
                'ETP_LIBELLE' => 'المخزن الرئيسي',
                'ETP_ACTIVE' => 1
            ]);
        }
    }
    
    /**
     * إضافة فواتير مبيعات اليوم
     */
    private function addSalesToday()
    {
        echo "💰 إضافة فواتير مبيعات اليوم...\n";
        
        $today = Carbon::today();
        $clients = DB::table('CLIENT')->limit(3)->get();
        $articles = DB::table('ARTICLE')->limit(4)->get();
        
        if ($clients->isEmpty() || $articles->isEmpty()) {
            echo "  ⚠️ لا توجد عملاء أو مقالات لإنشاء فواتير\n";
            return;
        }
        
        // إنشاء عدة فواتير لساعات مختلفة من اليوم
        $hours = [9, 12, 14, 16, 18, 20];
        
        foreach ($hours as $hour) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $factureRef = 'FCTV_' . uniqid();
                $client = $clients->random();
                $dateTime = $today->copy()->setHour($hour)->setMinute(rand(0, 59));
                
                $totalHT = 0;
                $totalTTC = 0;
                
                // تفاصيل الفاتورة
                $details = [];
                $numArticles = rand(1, 3);
                
                for ($j = 0; $j < $numArticles; $j++) {
                    $article = $articles->random();
                    $quantity = rand(1, 3);
                    $prixHT = $article->ART_PRIX_VENTE_HT;
                    $prixTTC = $article->ART_PRIX_VENTE;
                    
                    $details[] = [
                        'FCTV_REF' => $factureRef,
                        'ART_REF' => $article->ART_REF,
                        'FCTVD_QUANTITE' => $quantity,
                        'FCTVD_PRIX_UNITAIRE_HT' => $prixHT,
                        'FCTVD_PRIX_UNITAIRE_TTC' => $prixTTC,
                        'FCTVD_PRIX_TOTAL' => $prixTTC * $quantity,
                        'FCTVD_TVA' => 20.0,
                        'FCTVD_REMISE' => 0.0
                    ];
                    
                    $totalHT += $prixHT * $quantity;
                    $totalTTC += $prixTTC * $quantity;
                }
                
                // إدراج الفاتورة
                DB::table('FACTURE_VNT')->insert([
                    'FCTV_REF' => $factureRef,
                    'CLT_REF' => $client->CLT_REF,
                    'ETP_REF' => 'ETP001',
                    'FCTV_NUMERO' => 'F' . date('Ymd') . sprintf('%04d', rand(1, 9999)),
                    'FCTV_DATE' => $dateTime,
                    'FCTV_MNT_HT' => $totalHT,
                    'FCTV_MNT_TTC' => $totalTTC,
                    'FCTV_REMISE' => 0.0,
                    'FCTV_VALIDE' => 1,
                    'FCTV_EXONORE' => 0,
                    'FCTV_UTILISATEUR' => 'ADMIN'
                ]);
                
                // إدراج تفاصيل الفاتورة
                foreach ($details as $detail) {
                    DB::table('FACTURE_VNT_DETAIL')->insert($detail);
                }
                
                echo "  ✅ تمت إضافة فاتورة بقيمة: " . number_format($totalTTC, 2) . " DH في الساعة " . $hour . ":00\n";
            }
        }
    }
    
    /**
     * إضافة حركات مخزون
     */
    private function addStockMovements()
    {
        echo "📦 تحديث حركات المخزون...\n";
        
        $articles = DB::table('ARTICLE')->get();
        
        foreach ($articles as $article) {
            // تحديث أو إضافة كمية مخزون
            $stockExists = DB::table('STOCK')
                ->where('ART_REF', $article->ART_REF)
                ->where('ETP_REF', 'ETP001')
                ->exists();
                
            if ($stockExists) {
                DB::table('STOCK')
                    ->where('ART_REF', $article->ART_REF)
                    ->where('ETP_REF', 'ETP001')
                    ->update([
                        'STK_QUANTITE' => rand(0, 100) // بعض المقالات قد تكون في حالة نفاد
                    ]);
            } else {
                DB::table('STOCK')->insert([
                    'ART_REF' => $article->ART_REF,
                    'ETP_REF' => 'ETP001',
                    'STK_QUANTITE' => rand(0, 100)
                ]);
            }
        }
        
        echo "  ✅ تم تحديث كميات المخزون\n";
    }
    
    /**
     * إضافة دفوعات متنوعة
     */
    private function addPayments()
    {
        echo "💳 إضافة دفوعات متنوعة...\n";
        
        $factures = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', Carbon::today())
            ->get();
        
        $paymentMethods = ['ESPECES', 'CARTE', 'CHEQUE', 'VIREMENT'];
        
        foreach ($factures as $facture) {
            $method = $paymentMethods[array_rand($paymentMethods)];
            
            DB::table('REGLEMENT')->insert([
                'REG_REF' => 'REG_' . uniqid(),
                'FCTV_REF' => $facture->FCTV_REF,
                'REG_MONTANT' => $facture->FCTV_MNT_TTC,
                'REG_MODE' => $method,
                'REG_DATE' => $facture->FCTV_DATE,
                'REG_UTILISATEUR' => 'ADMIN'
            ]);
        }
        
        echo "  ✅ تمت إضافة " . count($factures) . " دفعة بطرق مختلفة\n";
    }
    
    /**
     * إضافة بيانات المطعم
     */
    private function addRestaurantData()
    {
        echo "🍽️ إضافة بيانات المطعم...\n";
        
        // إضافة منطقة
        $zoneExists = DB::table('ZONE')->where('ZON_REF', 'ZON001')->exists();
        if (!$zoneExists) {
            DB::table('ZONE')->insert([
                'ZON_REF' => 'ZON001',
                'ZON_LIBELLE' => 'الصالة الرئيسية'
            ]);
        }
        
        // إضافة طاولات
        $tables = [
            ['TAB_REF' => 'TAB001', 'TAB_LIBELLE' => 'طاولة 1', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 4, 'TAB_ETAT' => 'LIBRE'],
            ['TAB_REF' => 'TAB002', 'TAB_LIBELLE' => 'طاولة 2', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 2, 'TAB_ETAT' => 'OCCUPEE'],
            ['TAB_REF' => 'TAB003', 'TAB_LIBELLE' => 'طاولة 3', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 6, 'TAB_ETAT' => 'RESERVEE'],
            ['TAB_REF' => 'TAB004', 'TAB_LIBELLE' => 'طاولة 4', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 4, 'TAB_ETAT' => 'LIBRE'],
            ['TAB_REF' => 'TAB005', 'TAB_LIBELLE' => 'طاولة 5', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 8, 'TAB_ETAT' => 'OCCUPEE']
        ];
        
        foreach ($tables as $table) {
            $exists = DB::table('TABLE')->where('TAB_REF', $table['TAB_REF'])->exists();
            if (!$exists) {
                DB::table('TABLE')->insert($table);
                echo "  ✅ تمت إضافة " . $table['TAB_LIBELLE'] . "\n";
            }
        }
        
        // إضافة حجوزات
        $clients = DB::table('CLIENT')->limit(2)->get();
        if (!$clients->isEmpty()) {
            $reservations = [
                [
                    'RES_REF' => 'RES_' . uniqid(),
                    'CLT_REF' => $clients->first()->CLT_REF,
                    'TAB_REF' => 'TAB003',
                    'RES_DATE' => Carbon::today()->setHour(19)->setMinute(30),
                    'RES_NBR_PERSONNE' => 4,
                    'RES_REMARQUE' => 'حجز لعشاء عائلي',
                    'RES_ETAT' => 'CONFIRMEE'
                ]
            ];
            
            foreach ($reservations as $reservation) {
                DB::table('RESERVATION')->insert($reservation);
                echo "  ✅ تمت إضافة حجز للعميل: " . $clients->first()->CLT_CLIENT . "\n";
            }
        }
    }
    
    /**
     * إضافة مصاريف اليوم
     */
    private function addExpenses()
    {
        echo "💸 إضافة مصاريف اليوم...\n";
        
        // إضافة أنواع المصاريف
        $motifs = [
            'كهرباء وماء',
            'مواد تنظيف',
            'صيانة',
            'مشتريات مكتبية'
        ];
        
        foreach ($motifs as $motif) {
            $exists = DB::table('MOTIF_DEPENSE')->where('MTF_LIBELLE', $motif)->exists();
            if (!$exists) {
                DB::table('MOTIF_DEPENSE')->insert(['MTF_LIBELLE' => $motif]);
            }
        }
        
        $expenses = [
            ['MTF_LIBELLE' => 'كهرباء وماء', 'DEP_MONTANT' => 250.00, 'DEP_DESCRIPTION' => 'فاتورة الكهرباء والماء'],
            ['MTF_LIBELLE' => 'مواد تنظيف', 'DEP_MONTANT' => 80.00, 'DEP_DESCRIPTION' => 'مواد تنظيف للمطعم'],
            ['MTF_LIBELLE' => 'صيانة', 'DEP_MONTANT' => 150.00, 'DEP_DESCRIPTION' => 'صيانة آلة القهوة'],
            ['MTF_LIBELLE' => 'مشتريات مكتبية', 'DEP_MONTANT' => 45.00, 'DEP_DESCRIPTION' => 'أوراق وأقلام']
        ];
        
        foreach ($expenses as $expense) {
            DB::table('DEPENSE')->insert([
                'DEP_REF' => 'DEP_' . uniqid(),
                'DEP_DATE' => Carbon::today()->setHour(rand(9, 17)),
                'DEP_MONTANT' => $expense['DEP_MONTANT'],
                'DEP_DESCRIPTION' => $expense['DEP_DESCRIPTION'],
                'DEP_UTILISATEUR' => 'ADMIN'
            ]);
            
            echo "  ✅ تمت إضافة مصروف: " . $expense['DEP_DESCRIPTION'] . " - " . $expense['DEP_MONTANT'] . " DH\n";
        }
    }
}
