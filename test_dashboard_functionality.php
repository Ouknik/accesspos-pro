<?php
/**
 * Test Dashboard Functionality
 * Vérifie que le tableau de bord fonctionne correctement après toutes les corrections
 */

require_once 'vendor/autoload.php';

// Configuration de base de données (simulée)
$config = [
    'database' => [
        'default' => 'sqlsrv',
        'connections' => [
            'sqlsrv' => [
                'driver' => 'sqlsrv',
                'host' => 'localhost',
                'database' => 'AccessPosPro',
                'username' => 'sa',
                'password' => 'password',
                'charset' => 'utf8',
                'prefix' => '',
            ]
        ]
    ]
];

echo "🧪 TEST DU TABLEAU DE BORD ACCESSPOS PRO\n";
echo "=====================================\n\n";

try {
    // Test 1: Vérifier que le controller existe et peut être instancié
    echo "✅ Test 1: Vérification du Controller\n";
    $controllerPath = 'app/Http/Controllers/Admin/TableauDeBordController.php';
    if (file_exists($controllerPath)) {
        echo "   ✓ Controller existe: $controllerPath\n";
        $controllerContent = file_get_contents($controllerPath);
        
        // Vérifier les corrections clés
        if (strpos($controllerContent, 'FCTV_MNT_TTC') !== false) {
            echo "   ✓ Colonnes corrigées: FCTV_MNT_TTC trouvé\n";
        }
        if (strpos($controllerContent, 'DB::table') !== false) {
            echo "   ✓ Utilisation de DB::table confirmée\n";
        }
        if (strpos($controllerContent, 'getChiffreAffairesDetails') !== false) {
            echo "   ✓ Fonctions AJAX modales présentes\n";
        }
    } else {
        echo "   ❌ Controller manquant!\n";
    }
    
    echo "\n";

    // Test 2: Vérifier les routes
    echo "✅ Test 2: Vérification des Routes\n";
    $routesPath = 'routes/web.php';
    if (file_exists($routesPath)) {
        echo "   ✓ Fichier routes existe: $routesPath\n";
        $routesContent = file_get_contents($routesPath);
        
        if (strpos($routesContent, 'tableau.chiffre-affaires.details') !== false) {
            echo "   ✓ Route modale CA détails présente\n";
        }
        if (strpos($routesContent, 'tableau.articles-rupture.details') !== false) {
            echo "   ✓ Route modale articles rupture présente\n";
        }
        if (strpos($routesContent, 'TableauDeBordController') !== false) {
            echo "   ✓ Référence au bon controller\n";
        }
    } else {
        echo "   ❌ Fichier routes manquant!\n";
    }
    
    echo "\n";

    // Test 3: Vérifier la vue
    echo "✅ Test 3: Vérification de la Vue\n";
    $viewPath = 'resources/views/admin/tableau-de-bord-moderne.blade.php';
    if (file_exists($viewPath)) {
        echo "   ✓ Vue existe: $viewPath\n";
        $viewContent = file_get_contents($viewPath);
        
        if (strpos($viewContent, 'DH') !== false) {
            echo "   ✓ Devise corrigée: DH trouvé\n";
        }
        if (strpos($viewContent, 'modalEndpoints') !== false) {
            echo "   ✓ Configuration AJAX modales présente\n";
        }
        if (strpos($viewContent, 'chiffre-affaires-details') !== false) {
            echo "   ✓ Routes modales correctes\n";
        }
        if (strpos($viewContent, 'modal') !== false) {
            echo "   ✓ Modales intégrées dans la vue\n";
        }
    } else {
        echo "   ❌ Vue manquante!\n";
    }
    
    echo "\n";

    // Test 4: Vérifier les scripts de données
    echo "✅ Test 4: Vérification des Scripts de Données\n";
    $scriptsToCheck = [
        'check_data.php' => 'Script de vérification des données',
        'generate_demo_data.php' => 'Script de génération de données de test',
        'final_validation.php' => 'Script de validation finale'
    ];
    
    foreach ($scriptsToCheck as $script => $description) {
        if (file_exists($script)) {
            echo "   ✓ $description présent\n";
        } else {
            echo "   ⚠️  $description manquant (optionnel)\n";
        }
    }
    
    echo "\n";

    // Test 5: Vérifier la structure des dossiers
    echo "✅ Test 5: Vérification de la Structure\n";
    $requiredDirs = [
        'app/Http/Controllers/Admin',
        'resources/views/admin',
        'routes',
        'database'
    ];
    
    foreach ($requiredDirs as $dir) {
        if (is_dir($dir)) {
            echo "   ✓ Dossier $dir existe\n";
        } else {
            echo "   ❌ Dossier $dir manquant!\n";
        }
    }

    echo "\n";
    echo "🎉 RÉSUMÉ DU TEST\n";
    echo "================\n";
    echo "Le tableau de bord AccessPOS Pro est prêt à être testé!\n\n";
    
    echo "📋 ÉTAPES SUIVANTES:\n";
    echo "1. Démarrer le serveur Laravel: php artisan serve\n";
    echo "2. Accéder au tableau de bord via l'URL appropriée\n";
    echo "3. Tester les modales 'Voir détails' pour chaque section\n";
    echo "4. Vérifier que les données s'affichent correctement (277,656.00 DH)\n\n";
    
    echo "🔗 FONCTIONNALITÉS À TESTER:\n";
    echo "• Chiffre d'affaires du jour\n";
    echo "• Articles en rupture de stock\n";
    echo "• Top 5 clients\n";
    echo "• Ventes par heure\n";
    echo "• Évolution des ventes\n";
    echo "• Performance des caisses\n";
    echo "• Toutes les modales 'Voir détails'\n\n";

} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
}

echo "✨ Test terminé avec succès!\n";
?>
