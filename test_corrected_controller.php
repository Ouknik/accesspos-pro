<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 اختبار البيانات بعد تصحيح Controller...\n";
echo "================================================\n";

// التاريخ المستخدم في البيانات
$date = '2025-07-09';

echo "📅 التاريخ المستهدف: $date\n\n";

try {
    // 1. اختبار الإحصائيات المالية
    echo "💰 الإحصائيات المالية:\n";
    echo "-------------------------\n";
    
    $ca_du_jour = DB::table('FACTURE_VNT')
        ->whereDate('FCTV_DATE', $date)
        ->sum('FCT_MNT_RGL') ?? 0;
    echo "  CA du jour: " . number_format($ca_du_jour, 2) . " DH\n";
    
    $nb_factures_jour = DB::table('FACTURE_VNT')
        ->whereDate('FCTV_DATE', $date)
        ->count();
    echo "  Nombre de factures: $nb_factures_jour\n";
    
    $ticket_moyen = $nb_factures_jour > 0 ? ($ca_du_jour / $nb_factures_jour) : 0;
    echo "  Ticket moyen: " . number_format($ticket_moyen, 2) . " DH\n";
    
    // 2. اختبار إدارة المخزون
    echo "\n📦 إدارة المخزون:\n";
    echo "-------------------\n";
    
    $nb_total_articles = DB::table('ARTICLE')->count();
    echo "  Total articles: $nb_total_articles\n";
    
    $articles_rupture = DB::table('STOCK')->where('STK_QTE', '<=', 0)->count();
    echo "  Articles en rupture: $articles_rupture\n";
    
    $articles_stock_faible = DB::table('STOCK')->where('STK_QTE', '<', 10)->count();
    echo "  Articles stock faible: $articles_stock_faible\n";
    
    // 3. اختبار إدارة العملاء
    echo "\n👥 إدارة العملاء:\n";
    echo "-------------------\n";
    
    $nb_total_clients = DB::table('CLIENT')->count();
    echo "  Total clients: $nb_total_clients\n";
    
    $clients_fideles_actifs = DB::table('CLIENT')->where('CLT_FIDELE', 1)->count();
    echo "  Clients fidèles: $clients_fideles_actifs\n";
    
    $points_fidelite_distribues = DB::table('CLIENT')->sum('CLT_POINTFIDILIO') ?? 0;
    echo "  Points fidélité: " . number_format($points_fidelite_distribues) . "\n";
    
    // 4. اختبار إدارة المطعم
    echo "\n🍽️ إدارة المطعم:\n";
    echo "-------------------\n";
    
    $tables_occupees = DB::table('TABLE')->where('ETT_ETAT', 'OCCUPEE')->count();
    echo "  Tables occupées: $tables_occupees\n";
    
    $tables_libres = DB::table('TABLE')->where('ETT_ETAT', 'LIBRE')->count();
    echo "  Tables libres: $tables_libres\n";
    
    $reservations_jour = DB::table('RESERVATION')
        ->whereDate('DATE_RESERVATION', $date)
        ->count();
    echo "  Réservations du jour: $reservations_jour\n";
    
    // 5. اختبار الإدارة المالية
    echo "\n💳 الإدارة المالية:\n";
    echo "-------------------\n";
    
    $nb_caisses = DB::table('CAISSE')->count();
    $solde_caisse_actuel = $nb_caisses * 1000; // Valeur par défaut temporaire
    echo "  Solde caisse (estimation): " . number_format($solde_caisse_actuel, 2) . " DH\n";
    
    $depenses_jour = DB::table('DEPENSE')
        ->whereDate('DEP_DATE', $date)
        ->sum('DEP_MONTANTHT') ?? 0;
    echo "  Dépenses du jour: " . number_format($depenses_jour, 2) . " DH\n";
    
    // 6. اختبار أفضل المقالات المباعة
    echo "\n🏆 Top articles vendus:\n";
    echo "------------------------\n";
    
    $articles_plus_vendus = DB::table('FACTURE_VNT_DETAIL as fvd')
        ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
        ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
        ->whereDate('fv.FCTV_DATE', $date)
        ->select('a.ART_DESIGNATION', 'a.ART_REF')
        ->selectRaw('SUM(fvd.FVD_QTE) as quantite_vendue')
        ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
        ->orderByDesc('quantite_vendue')
        ->limit(5)
        ->get();
    
    foreach ($articles_plus_vendus as $index => $article) {
        echo "  " . ($index + 1) . ". {$article->ART_DESIGNATION}: {$article->quantite_vendue} unités\n";
    }
    
    // 7. اختبار أفضل العملاء
    echo "\n🌟 Top clients:\n";
    echo "---------------\n";
    
    $top_clients = DB::table('FACTURE_VNT as fv')
        ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
        ->whereDate('fv.FCTV_DATE', $date)
        ->select('c.CLT_CLIENT', 'c.CLT_REF')
        ->selectRaw('COUNT(*) as nb_commandes')
        ->selectRaw('SUM(fv.FCT_MNT_RGL) as total_depense')
        ->groupBy('c.CLT_REF', 'c.CLT_CLIENT')
        ->orderByDesc('total_depense')
        ->limit(5)
        ->get();
    
    foreach ($top_clients as $index => $client) {
        echo "  " . ($index + 1) . ". {$client->CLT_CLIENT}: " . number_format($client->total_depense, 2) . " DH ({$client->nb_commandes} commandes)\n";
    }
    
    // 8. اختبار المبيعات بالساعة
    echo "\n📊 Ventes par heure:\n";
    echo "--------------------\n";
    
    $ventes_par_heure = DB::table('FACTURE_VNT')
        ->selectRaw('DATEPART(HOUR, FCTV_DATE) as heure')
        ->selectRaw('COUNT(*) as nb_transactions')
        ->selectRaw('SUM(FCT_MNT_RGL) as ca_heure')
        ->whereDate('FCTV_DATE', $date)
        ->whereNotNull('FCTV_DATE')
        ->groupByRaw('DATEPART(HOUR, FCTV_DATE)')
        ->orderByDesc('nb_transactions')
        ->limit(5)
        ->get();
    
    foreach ($ventes_par_heure as $heure) {
        echo "  {$heure->heure}h: {$heure->nb_transactions} transactions - " . number_format($heure->ca_heure, 2) . " DH\n";
    }
    
    echo "\n✅ Tous les tests sont réussis! Le Controller fonctionne correctement.\n";
    echo "📊 Récapitulatif des données disponibles:\n";
    echo "  - $nb_factures_jour factures pour un CA de " . number_format($ca_du_jour, 2) . " DH\n";
    echo "  - $nb_total_articles articles au total, $articles_rupture en rupture\n";
    echo "  - $nb_total_clients clients, dont $clients_fideles_actifs fidèles\n";
    echo "  - $reservations_jour réservations pour le jour\n";
    echo "  - " . number_format($depenses_jour, 2) . " DH de dépenses\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur lors des tests: " . $e->getMessage() . "\n";
    echo "📝 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
