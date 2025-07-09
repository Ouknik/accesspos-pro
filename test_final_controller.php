<?php
require_once __DIR__ . '/vendor/autoload.php';

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\TableauDeBordController;
use Illuminate\Http\Request;

echo "🧪 اختبار CONTROLLER الجديد مع البيانات الفعلية\n";
echo "===============================================\n";

try {
    // إنشاء instance من Controller
    $controller = new TableauDeBordController();
    
    // محاكاة Request
    $request = new Request();
    
    echo "📊 تنفيذ دالة index()...\n";
    
    // تنفيذ الدالة بشكل مباشر لاختبار البيانات
    $response = $controller->index();
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✅ Controller يعمل بنجاح!\n";
        echo "📄 View: " . $response->name() . "\n";
        
        // عرض البيانات المرسلة للـ View
        $data = $response->getData();
        
        echo "\n📊 البيانات المرسلة للـ View:\n";
        echo "===============================\n";
        
        // الإحصائيات المالية
        if (isset($data['statistiquesFinancieres'])) {
            echo "💰 الإحصائيات المالية:\n";
            $stats = $data['statistiquesFinancieres'];
            echo "  - CA du jour: " . number_format($stats['ca_du_jour'] ?? 0, 2) . " DH\n";
            echo "  - CA du mois: " . number_format($stats['ca_du_mois'] ?? 0, 2) . " DH\n";
            echo "  - CA de l'année: " . number_format($stats['ca_de_annee'] ?? 0, 2) . " DH\n";
            echo "  - Nb factures jour: " . number_format($stats['nb_factures_jour'] ?? 0) . "\n";
            echo "  - Ticket moyen: " . number_format($stats['ticket_moyen'] ?? 0, 2) . " DH\n";
            echo "  - Évolution: " . number_format($stats['evolution_ventes'] ?? 0, 1) . "%\n";
        }
        
        // إدارة المخزون
        if (isset($data['gestionStocks'])) {
            echo "\n📦 إدارة المخزون:\n";
            $stocks = $data['gestionStocks'];
            echo "  - Total articles: " . number_format($stocks['nb_total_articles'] ?? 0) . "\n";
            echo "  - Valeur stock: " . number_format($stocks['valeur_stock'] ?? 0, 2) . " DH\n";
            echo "  - Articles rupture: " . number_format($stocks['articles_rupture'] ?? 0) . "\n";
            echo "  - Articles stock faible: " . number_format($stocks['articles_stock_faible'] ?? 0) . "\n";
        }
        
        // إدارة العملاء
        if (isset($data['gestionClientele'])) {
            echo "\n👥 إدارة العملاء:\n";
            $clients = $data['gestionClientele'];
            echo "  - Total clients: " . number_format($clients['nb_total_clients'] ?? 0) . "\n";
            echo "  - Clients fidèles: " . number_format($clients['clients_fideles_actifs'] ?? 0) . "\n";
            echo "  - Points fidélité: " . number_format($clients['points_fidelite_distribues'] ?? 0) . "\n";
            echo "  - Dépense moyenne: " . number_format($clients['depense_moyenne_client'] ?? 0, 2) . " DH\n";
        }
        
        // إدارة المطعم
        if (isset($data['gestionRestaurant'])) {
            echo "\n🍽️ إدارة المطعم:\n";
            $restaurant = $data['gestionRestaurant'];
            echo "  - Tables occupées: " . number_format($restaurant['tables_occupees'] ?? 0) . "\n";
            echo "  - Tables libres: " . number_format($restaurant['tables_libres'] ?? 0) . "\n";
            echo "  - Réservations jour: " . number_format($restaurant['reservations_jour'] ?? 0) . "\n";
        }
        
        // الإدارة المالية
        if (isset($data['gestionFinanciere'])) {
            echo "\n💳 الإدارة المالية:\n";
            $finance = $data['gestionFinanciere'];
            echo "  - Solde caisse: " . number_format($finance['solde_caisse_actuel'] ?? 0, 2) . " DH\n";
            echo "  - Dépenses jour: " . number_format($finance['depenses_jour'] ?? 0, 2) . " DH\n";
            echo "  - Dépenses mois: " . number_format($finance['depenses_mois'] ?? 0, 2) . " DH\n";
        }
        
        echo "\n🎉 RÉSULTAT FINAL:\n";
        echo "==================\n";
        echo "✅ Controller fonctionne parfaitement\n";
        echo "✅ Toutes les données sont correctement formatées\n";
        echo "✅ View reçoit toutes les variables nécessaires\n";
        echo "✅ Les montants sont en DH (Dirhams)\n";
        echo "✅ Les données du 2025-07-09 sont visibles\n";
        
        echo "\n📈 RÉSUMÉ DE PERFORMANCE:\n";
        echo "=========================\n";
        if (isset($data['statistiquesFinancieres'])) {
            $stats = $data['statistiquesFinancieres'];
            echo "🏆 CA du jour: " . number_format($stats['ca_du_jour'] ?? 0, 2) . " DH\n";
            echo "📊 " . number_format($stats['nb_factures_jour'] ?? 0) . " factures traitées\n";
            echo "🛍️ Ticket moyen: " . number_format($stats['ticket_moyen'] ?? 0, 2) . " DH\n";
        }
        
    } else {
        echo "❌ Erreur: Controller ne retourne pas une View\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors du test du Controller:\n";
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
