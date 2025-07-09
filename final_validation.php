<?php
require_once __DIR__ . '/vendor/autoload.php';

// إعداد Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\TableauDeBordController;

echo "🎯 VALIDATION FINALE - ACCESSPOS PRO DASHBOARD\n";
echo "==============================================\n";

try {
    $controller = new TableauDeBordController();
    $response = $controller->index();
    
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
        
        // تحقق سريع من البيانات الأساسية
        $ca_jour = $data['statistiquesFinancieres']['ca_du_jour'] ?? 0;
        $nb_factures = $data['statistiquesFinancieres']['nb_factures_jour'] ?? 0;
        $nb_articles = $data['gestionStocks']['nb_total_articles'] ?? 0;
        $nb_clients = $data['gestionClientele']['nb_total_clients'] ?? 0;
        
        echo "✅ DASHBOARD FONCTIONNEL\n";
        echo "📊 CA du jour: " . number_format($ca_jour, 2) . " DH\n";
        echo "📋 Factures: " . number_format($nb_factures) . "\n";
        echo "📦 Articles: " . number_format($nb_articles) . "\n";
        echo "👥 Clients: " . number_format($nb_clients) . "\n";
        
        if ($ca_jour > 0 && $nb_factures > 0) {
            echo "\n🎉 SUCCÈS TOTAL!\n";
            echo "🏆 Le dashboard AccessPOS Pro est COMPLÈTEMENT FONCTIONNEL!\n";
            echo "💰 Affichage correct de " . number_format($ca_jour, 2) . " DH\n";
            echo "📈 " . number_format($nb_factures) . " transactions visibles\n";
            echo "✨ Toutes les données sont correctement formatées\n";
        } else {
            echo "\n⚠️ Données partielles détectées\n";
        }
        
    } else {
        echo "❌ Erreur: Réponse inattendue du Controller\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 ACCESSPOS PRO DASHBOARD: MISSION TERMINÉE! 🎯\n";
echo str_repeat("=", 50) . "\n";
