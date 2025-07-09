<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateDemoData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'demo:generate {--clean : Clean existing data first}';

    /**
     * The console command description.
     */
    protected $description = 'Generate demo data for AccessPOS Pro system testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 AccessPOS Pro - Demo Data Generator');
        $this->info('=====================================');
        
        $today = Carbon::today();
        $this->info('📅 Target Date: ' . $today->format('Y-m-d'));
        
        if ($this->option('clean')) {
            $this->cleanExistingData();
        }
        
        $this->info('🔧 Starting data generation...');
        
        try {
            // Progress bar
            $bar = $this->output->createProgressBar(8);
            $bar->start();
            
            // 1. Basic data
            $this->addBasicData();
            $bar->advance();
            
            // 2. Customers
            $this->addCustomers();
            $bar->advance();
            
            // 3. Products
            $this->addProducts();
            $bar->advance();
            
            // 4. Sales
            $this->addSales();
            $bar->advance();
            
            // 5. Payments
            $this->addPayments();
            $bar->advance();
            
            // 6. Restaurant data
            $this->addRestaurantData();
            $bar->advance();
            
            // 7. Expenses
            $this->addExpenses();
            $bar->advance();
            
            // 8. Summary
            $this->showSummary();
            $bar->advance();
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info('✅ Demo data generation completed successfully!');
            $this->info('💻 Access your dashboard at: /admin/tableau-de-bord-moderne');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('📍 Line: ' . $e->getLine());
            return 1;
        }
        
        return 0;
    }
    
    private function cleanExistingData()
    {
        $this->warn('🧹 Cleaning existing data for today...');
        
        $today = Carbon::today();
        
        // Delete today's invoices and related data
        $deletedInvoices = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', $today)
            ->delete();
            
        if ($deletedInvoices > 0) {
            $this->line("  🗑️ Deleted {$deletedInvoices} invoices");
        }
        
        // Delete payments
        DB::table('REGLEMENT')->whereDate('REG_DATE', $today)->delete();
        
        // Delete expenses
        DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->delete();
        
        $this->info('  ✅ Cleanup completed');
    }
    
    private function addBasicData()
    {
        // Categories
        $this->insertIfNotExists('CATEGORIE_CLIENT', 'CLTCAT_REF', [
            ['CLTCAT_REF' => 'CAT001', 'CLTCAT_LIBELLE' => 'عملاء عاديين'],
            ['CLTCAT_REF' => 'CAT002', 'CLTCAT_LIBELLE' => 'عملاء VIP'],
            ['CLTCAT_REF' => 'CAT003', 'CLTCAT_LIBELLE' => 'شركات']
        ]);
        
        // Families
        $this->insertIfNotExists('FAMILLE', 'FAM_REF', [
            ['FAM_REF' => 'FAM001', 'FAM_DESIGNATION' => 'مشروبات ساخنة'],
            ['FAM_REF' => 'FAM002', 'FAM_DESIGNATION' => 'مشروبات باردة'],
            ['FAM_REF' => 'FAM003', 'FAM_DESIGNATION' => 'وجبات رئيسية'],
            ['FAM_REF' => 'FAM004', 'FAM_DESIGNATION' => 'حلويات ومعجنات']
        ]);
        
        // Sub-families
        $this->insertIfNotExists('SOUS_FAMILLE', 'SFM_REF', [
            ['SFM_REF' => 'SFM001', 'FAM_REF' => 'FAM001', 'SFM_DESIGNATION' => 'قهوة وشاي'],
            ['SFM_REF' => 'SFM002', 'FAM_REF' => 'FAM002', 'SFM_DESIGNATION' => 'عصائر طبيعية'],
            ['SFM_REF' => 'SFM003', 'FAM_REF' => 'FAM003', 'SFM_DESIGNATION' => 'برغر وساندويتش'],
            ['SFM_REF' => 'SFM004', 'FAM_REF' => 'FAM004', 'SFM_DESIGNATION' => 'كعك وبسكويت']
        ]);
        
        // Units
        $this->insertIfNotExists('UNITE_MESURE', 'UNM_ABR', [
            ['UNM_ABR' => 'PC', 'UNM_LIBELLE' => 'قطعة'],
            ['UNM_ABR' => 'KG', 'UNM_LIBELLE' => 'كيلوغرام'],
            ['UNM_ABR' => 'L', 'UNM_LIBELLE' => 'لتر']
        ]);
        
        // Warehouse
        $this->insertIfNotExists('ENTREPOT', 'ETP_REF', [
            ['ETP_REF' => 'ETP001', 'ETP_LIBELLE' => 'المخزن الرئيسي', 'ETP_ACTIVE' => 1]
        ]);
        
        // Zones
        $this->insertIfNotExists('ZONE', 'ZON_REF', [
            ['ZON_REF' => 'ZON001', 'ZON_LIBELLE' => 'الصالة الرئيسية'],
            ['ZON_REF' => 'ZON002', 'ZON_LIBELLE' => 'التراس الخارجي']
        ]);
    }
    
    private function addCustomers()
    {
        $customers = [
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
            ]
        ];
        
        foreach ($customers as $customer) {
            $exists = DB::table('CLIENT')->where('CLT_CLIENT', $customer['CLT_CLIENT'])->exists();
            if (!$exists) {
                DB::table('CLIENT')->insert($customer);
            }
        }
    }
    
    private function addProducts()
    {
        $products = [
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
                'ART_ORDRE_AFFICHAGE' => 3
            ]
        ];
        
        foreach ($products as $product) {
            $exists = DB::table('ARTICLE')->where('ART_DESIGNATION', $product['ART_DESIGNATION'])->exists();
            if (!$exists) {
                DB::table('ARTICLE')->insert($product);
                
                // Add stock
                DB::table('STOCK')->insert([
                    'ART_REF' => $product['ART_REF'],
                    'ETP_REF' => 'ETP001',
                    'STK_QUANTITE' => rand(5, 50)
                ]);
            }
        }
    }
    
    private function addSales()
    {
        $customers = DB::table('CLIENT')->whereRaw("CLT_REF LIKE '%' + ? + '%'", [date('Ymd')])->get();
        $products = DB::table('ARTICLE')->whereRaw("ART_REF LIKE '%' + ? + '%'", [date('Ymd')])->get();
        
        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }
        
        $today = Carbon::today();
        $hours = [9, 11, 13, 15, 17, 19, 21];
        $invoiceCount = 0;
        
        foreach ($hours as $hour) {
            $salesInHour = rand(2, 5);
            
            for ($i = 0; $i < $salesInHour; $i++) {
                $invoiceRef = 'FCTV_' . date('Ymd') . '_' . sprintf('%03d', $invoiceCount + 1);
                $customer = $customers->random();
                $dateTime = $today->copy()->setHour($hour)->setMinute(rand(0, 59));
                
                $totalHT = 0;
                $totalTTC = 0;
                $numProducts = rand(1, 3);
                $selectedProducts = $products->random($numProducts);
                
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 2);
                    $priceHT = $product->ART_PRIX_VENTE_HT;
                    $priceTTC = $product->ART_PRIX_VENTE;
                    
                    // Insert invoice detail
                    DB::table('FACTURE_VNT_DETAIL')->insert([
                        'FCTV_REF' => $invoiceRef,
                        'ART_REF' => $product->ART_REF,
                        'FCTVD_QUANTITE' => $quantity,
                        'FCTVD_PRIX_UNITAIRE_HT' => $priceHT,
                        'FCTVD_PRIX_UNITAIRE_TTC' => $priceTTC,
                        'FCTVD_PRIX_TOTAL' => $priceTTC * $quantity,
                        'FCTVD_TVA' => 20.0,
                        'FCTVD_REMISE' => 0.0
                    ]);
                    
                    $totalHT += $priceHT * $quantity;
                    $totalTTC += $priceTTC * $quantity;
                }
                
                // Insert invoice
                DB::table('FACTURE_VNT')->insert([
                    'FCTV_REF' => $invoiceRef,
                    'CLT_REF' => $customer->CLT_REF,
                    'ETP_REF' => 'ETP001',
                    'FCTV_NUMERO' => 'F' . date('Ymd') . sprintf('%04d', $invoiceCount + 1),
                    'FCTV_DATE' => $dateTime,
                    'FCTV_MNT_HT' => $totalHT,
                    'FCTV_MNT_TTC' => $totalTTC,
                    'FCTV_REMISE' => 0.0,
                    'FCTV_VALIDE' => 1,
                    'FCTV_EXONORE' => 0,
                    'FCTV_UTILISATEUR' => 'ADMIN'
                ]);
                
                $invoiceCount++;
            }
        }
    }
    
    private function addPayments()
    {
        $invoices = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', Carbon::today())
            ->get();
        
        $paymentMethods = ['ESPECES', 'CARTE', 'CHEQUE', 'VIREMENT'];
        
        foreach ($invoices as $invoice) {
            $method = $paymentMethods[array_rand($paymentMethods)];
            
            DB::table('REGLEMENT')->insert([
                'REG_REF' => 'REG_' . $invoice->FCTV_REF,
                'FCTV_REF' => $invoice->FCTV_REF,
                'REG_MONTANT' => $invoice->FCTV_MNT_TTC,
                'REG_MODE' => $method,
                'REG_DATE' => $invoice->FCTV_DATE,
                'REG_UTILISATEUR' => 'ADMIN'
            ]);
        }
    }
    
    private function addRestaurantData()
    {
        // Tables
        $tables = [
            ['TAB_REF' => 'TAB001', 'TAB_LIBELLE' => 'طاولة 1', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 4, 'TAB_ETAT' => 'LIBRE'],
            ['TAB_REF' => 'TAB002', 'TAB_LIBELLE' => 'طاولة 2', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 2, 'TAB_ETAT' => 'OCCUPEE'],
            ['TAB_REF' => 'TAB003', 'TAB_LIBELLE' => 'طاولة 3', 'ZON_REF' => 'ZON001', 'TAB_NBR_PLACE' => 6, 'TAB_ETAT' => 'RESERVEE'],
            ['TAB_REF' => 'TAB004', 'TAB_LIBELLE' => 'طاولة 4', 'ZON_REF' => 'ZON002', 'TAB_NBR_PLACE' => 4, 'TAB_ETAT' => 'LIBRE']
        ];
        
        foreach ($tables as $table) {
            $this->insertIfNotExists('TABLE', 'TAB_REF', [$table]);
        }
    }
    
    private function addExpenses()
    {
        $expenses = [
            ['description' => 'فاتورة الكهرباء', 'amount' => 285.50],
            ['description' => 'مواد تنظيف', 'amount' => 95.00],
            ['description' => 'صيانة آلة القهوة', 'amount' => 180.00],
            ['description' => 'مشتريات مكتبية', 'amount' => 67.50]
        ];
        
        foreach ($expenses as $expense) {
            DB::table('DEPENSE')->insert([
                'DEP_REF' => 'DEP_' . date('Ymd') . '_' . uniqid(),
                'DEP_DATE' => Carbon::today()->setHour(rand(9, 17)),
                'DEP_MONTANT' => $expense['amount'],
                'DEP_DESCRIPTION' => $expense['description'],
                'DEP_UTILISATEUR' => 'ADMIN'
            ]);
        }
    }
    
    private function showSummary()
    {
        $today = Carbon::today();
        
        $summary = [
            'العملاء' => DB::table('CLIENT')->whereRaw("CLT_REF LIKE '%' + ? + '%'", [date('Ymd')])->count(),
            'المنتجات' => DB::table('ARTICLE')->whereRaw("ART_REF LIKE '%' + ? + '%'", [date('Ymd')])->count(),
            'فواتير اليوم' => DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->count(),
            'إجمالي المبيعات' => number_format(DB::table('FACTURE_VNT')->whereDate('FCTV_DATE', $today)->sum('FCTV_MNT_TTC'), 2) . ' DH',
            'المدفوعات' => DB::table('REGLEMENT')->whereDate('REG_DATE', $today)->count(),
            'المصروفات' => number_format(DB::table('DEPENSE')->whereDate('DEP_DATE', $today)->sum('DEP_MONTANT'), 2) . ' DH'
        ];
        
        $this->newLine();
        $this->info('📊 Summary of generated data:');
        
        foreach ($summary as $item => $value) {
            $this->line("  📈 {$item}: {$value}");
        }
    }
    
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
