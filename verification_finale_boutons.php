<?php
/**
 * Script de vérification finale - Conversion complète des boutons modaux vers des pages séparées
 * 
 * Ce script vérifie que tous les boutons "Voir détails" sont maintenant des liens directs
 * vers des pages séparées au lieu d'utiliser le système de modal JavaScript.
 */

echo "=== VÉRIFICATION FINALE - CONVERSION DES BOUTONS MODAUX ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$fichier_principal = 'resources/views/admin/tableau-de-bord-moderne.blade.php';
$fichiers_details = [
    'resources/views/admin/chiffre-affaires-details.blade.php',
    'resources/views/admin/stock-rupture-details.blade.php',
    'resources/views/admin/top-clients-details.blade.php',
    'resources/views/admin/performance-horaire-details.blade.php',
    'resources/views/admin/modes-paiement-details.blade.php',
    'resources/views/admin/etat-tables-details.blade.php'
];

$routes_attendues = [
    'admin.dashboard.chiffre-affaires',
    'admin.dashboard.stock-rupture', 
    'admin.dashboard.top-clients',
    'admin.dashboard.performance-horaire',
    'admin.dashboard.modes-paiement',
    'admin.dashboard.etat-tables'
];

$erreurs = [];
$succes = [];

// 1. Vérifier que le fichier principal existe
if (!file_exists($fichier_principal)) {
    $erreurs[] = "Fichier principal non trouvé: $fichier_principal";
} else {
    echo "✓ Fichier principal trouvé: $fichier_principal\n";
    
    // 2. Vérifier qu'il n'y a plus de boutons avec onclick modal
    $contenu = file_get_contents($fichier_principal);
    
    // Chercher les anciens patterns de modal
    $patterns_modaux = [
        'onclick="openAdvancedModal',
        'openAdvancedModal(',
        'button.*href=.*target="_blank"',
        'button.*onclick.*modal'
    ];
    
    $modaux_trouves = false;
    foreach ($patterns_modaux as $pattern) {
        if (preg_match("/$pattern/i", $contenu)) {
            $erreurs[] = "Pattern modal trouvé: $pattern";
            $modaux_trouves = true;
        }
    }
    
    if (!$modaux_trouves) {
        $succes[] = "Aucun pattern modal détecté dans le fichier principal";
    }
    
    // 3. Vérifier que tous les boutons sont des liens <a href>
    $nb_liens_details = preg_match_all('/<a[^>]*href[^>]*>.*?Voir détails.*?<\/a>/is', $contenu);
    echo "✓ Nombre de liens 'Voir détails' trouvés: $nb_liens_details\n";
    
    if ($nb_liens_details >= 6) {
        $succes[] = "Nombre suffisant de liens 'Voir détails' trouvés ($nb_liens_details)";
    } else {
        $erreurs[] = "Nombre insuffisant de liens 'Voir détails' ($nb_liens_details, attendu >= 6)";
    }
    
    // 4. Vérifier que les routes sont utilisées correctement
    foreach ($routes_attendues as $route) {
        if (strpos($contenu, "route('$route')") !== false) {
            $succes[] = "Route trouvée: $route";
        } else {
            $erreurs[] = "Route manquante: $route";
        }
    }
}

// 5. Vérifier que toutes les pages de détails existent
echo "\n--- Vérification des pages de détails ---\n";
foreach ($fichiers_details as $fichier) {
    if (file_exists($fichier)) {
        $succes[] = "Page de détails existe: " . basename($fichier);
        echo "✓ " . basename($fichier) . "\n";
    } else {
        $erreurs[] = "Page de détails manquante: $fichier";
        echo "✗ " . basename($fichier) . " (MANQUANT)\n";
    }
}

// 6. Vérifier le fichier routes/web.php
echo "\n--- Vérification des routes ---\n";
$routes_file = 'routes/web.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    
    foreach ($routes_attendues as $route) {
        if (strpos($routes_content, "name('$route')") !== false) {
            $succes[] = "Route définie: $route";
            echo "✓ $route\n";
        } else {
            $erreurs[] = "Route non définie: $route";
            echo "✗ $route (MANQUANTE)\n";
        }
    }
} else {
    $erreurs[] = "Fichier routes/web.php non trouvé";
}

// 7. Résumé final
echo "\n" . str_repeat("=", 60) . "\n";
echo "RÉSUMÉ DE LA VÉRIFICATION\n";
echo str_repeat("=", 60) . "\n";

echo "\n✅ SUCCÈS (" . count($succes) . "):\n";
foreach ($succes as $success) {
    echo "   ✓ $success\n";
}

if (count($erreurs) > 0) {
    echo "\n❌ ERREURS (" . count($erreurs) . "):\n";
    foreach ($erreurs as $erreur) {
        echo "   ✗ $erreur\n";
    }
} else {
    echo "\n🎉 AUCUNE ERREUR DÉTECTÉE!\n";
}

// 8. Calcul du pourcentage de réussite
$total_verifications = count($succes) + count($erreurs);
$pourcentage = $total_verifications > 0 ? round((count($succes) / $total_verifications) * 100, 1) : 0;

echo "\n📊 TAUX DE RÉUSSITE: $pourcentage%\n";

if ($pourcentage == 100) {
    echo "\n🎯 CONVERSION COMPLÈTEMENT RÉUSSIE!\n";
    echo "Tous les boutons 'Voir détails' sont maintenant des liens directs vers des pages séparées.\n";
    echo "Le système de modal JavaScript a été complètement contourné.\n";
} elseif ($pourcentage >= 80) {
    echo "\n⚠️  CONVERSION MAJORITAIREMENT RÉUSSIE\n";
    echo "Quelques problèmes mineurs restent à corriger.\n";
} else {
    echo "\n❗ CONVERSION INCOMPLÈTE\n";
    echo "Des problèmes importants doivent être résolus.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Vérification terminée: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";

return $pourcentage == 100;
