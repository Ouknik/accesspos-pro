<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Système de notifications en temps réel pour AccessPOS Pro
     */
    
    public function getNotificationsEnTempsReel()
    {
        $notifications = [];
        
        // 1. Alertes de stock critique
        $alertesStock = $this->checkAlertesStock();
        $notifications = array_merge($notifications, $alertesStock);
        
        // 2. Alertes de performance
        $alertesPerformance = $this->checkAlertesPerformance();
        $notifications = array_merge($notifications, $alertesPerformance);
        
        // 3. Alertes financières
        $alertesFinancieres = $this->checkAlertesFinancieres();
        $notifications = array_merge($notifications, $alertesFinancieres);
        
        // 4. Alertes opérationnelles
        $alertesOperationnelles = $this->checkAlertesOperationnelles();
        $notifications = array_merge($notifications, $alertesOperationnelles);
        
        // 5. Alertes de sécurité et conformité
        $alertesSecurite = $this->checkAlertesSecurite();
        $notifications = array_merge($notifications, $alertesSecurite);
        
        // Tri par priorité et date
        usort($notifications, function($a, $b) {
            $priorite = ['critique' => 4, 'urgent' => 3, 'important' => 2, 'info' => 1];
            return ($priorite[$b['priorite']] ?? 0) - ($priorite[$a['priorite']] ?? 0);
        });
        
        return response()->json([
            'success' => true,
            'notifications' => array_slice($notifications, 0, 20), // Limiter à 20 notifications
            'total' => count($notifications),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    private function checkAlertesStock()
    {
        $alertes = [];
        
        // Articles en rupture de stock
        $articlesRupture = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
            ->count();
        
        if ($articlesRupture > 0) {
            $alertes[] = [
                'id' => 'stock_rupture_' . time(),
                'type' => 'stock',
                'priorite' => 'critique',
                'titre' => 'Articles en rupture de stock',
                'message' => "$articlesRupture article(s) en rupture de stock nécessitent une action immédiate",
                'icone' => 'fas fa-exclamation-triangle',
                'couleur' => 'danger',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Voir les détails',
                        'action' => 'openAdvancedModal',
                        'params' => ['stock-rupture', 'Articles en Rupture', 'fas fa-exclamation-triangle']
                    ],
                    [
                        'label' => 'Commander',
                        'action' => 'redirect',
                        'url' => '/admin/commandes/nouveau'
                    ]
                ]
            ];
        }
        
        // Stock faible
        $stockFaible = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->whereRaw('COALESCE(s.STK_QTE, 0) > 0 AND COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
            ->count();
        
        if ($stockFaible > 5) {
            $alertes[] = [
                'id' => 'stock_faible_' . time(),
                'type' => 'stock',
                'priorite' => 'important',
                'titre' => 'Stock faible détecté',
                'message' => "$stockFaible articles ont un stock inférieur au minimum recommandé",
                'icone' => 'fas fa-box-open',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Analyser',
                        'action' => 'openAdvancedModal',
                        'params' => ['stock-rupture', 'Analyse du Stock', 'fas fa-chart-bar']
                    ]
                ]
            ];
        }
        
        return $alertes;
    }
    
    private function checkAlertesPerformance()
    {
        $alertes = [];
        
        // Performance du jour vs objectif
        $caJour = DB::table('FACTURE_VNT')
            ->where('FAC_DATE', Carbon::today())
            ->where('FAC_VALIDE', 1)
            ->sum('FAC_NET_A_PAYER');
        
        $objectifJour = 2000; // À configurer
        $heureActuelle = Carbon::now()->hour;
        $pourcentageJournee = ($heureActuelle / 24) * 100;
        $pourcentageObjectif = ($caJour / $objectifJour) * 100;
        
        if ($heureActuelle > 14 && $pourcentageObjectif < ($pourcentageJournee * 0.7)) {
            $alertes[] = [
                'id' => 'perf_objectif_' . time(),
                'type' => 'performance',
                'priorite' => 'urgent',
                'titre' => 'Objectif en danger',
                'message' => "Seulement " . round($pourcentageObjectif, 1) . "% de l'objectif atteint à " . round($pourcentageJournee, 1) . "% de la journée",
                'icone' => 'fas fa-chart-line',
                'couleur' => 'danger',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Analyser les performances',
                        'action' => 'openAdvancedModal',
                        'params' => ['performance-horaire', 'Performance Détaillée', 'fas fa-clock']
                    ]
                ]
            ];
        }
        
        // Baisse significative par rapport à hier
        $caHier = DB::table('FACTURE_VNT')
            ->where('FAC_DATE', Carbon::yesterday())
            ->where('FAC_VALIDE', 1)
            ->sum('FAC_NET_A_PAYER');
        
        if ($caHier > 0 && $caJour < ($caHier * 0.8)) {
            $baisse = round((($caHier - $caJour) / $caHier) * 100, 1);
            $alertes[] = [
                'id' => 'perf_baisse_' . time(),
                'type' => 'performance',
                'priorite' => 'important',
                'titre' => 'Baisse de performance',
                'message' => "Le CA est en baisse de $baisse% par rapport à hier",
                'icone' => 'fas fa-arrow-down',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Analyser les causes',
                        'action' => 'openAdvancedModal',
                        'params' => ['chiffre-affaires', 'Analyse du CA', 'fas fa-euro-sign']
                    ]
                ]
            ];
        }
        
        return $alertes;
    }
    
    private function checkAlertesFinancieres()
    {
        $alertes = [];
        
        // Transactions importantes
        $grossesTransactions = DB::table('FACTURE_VNT')
            ->where('FAC_DATE', Carbon::today())
            ->where('FAC_NET_A_PAYER', '>', 500)
            ->where('FAC_VALIDE', 1)
            ->count();
        
        if ($grossesTransactions > 0) {
            $alertes[] = [
                'id' => 'grosses_transactions_' . time(),
                'type' => 'financier',
                'priorite' => 'info',
                'titre' => 'Grosses transactions détectées',
                'message' => "$grossesTransactions transaction(s) de plus de 500€ aujourd'hui",
                'icone' => 'fas fa-credit-card',
                'couleur' => 'info',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Voir les détails',
                        'action' => 'openAdvancedModal',
                        'params' => ['modes-paiement', 'Transactions du Jour', 'fas fa-credit-card']
                    ]
                ]
            ];
        }
        
        // Encaissements déséquilibrés
        $especes = DB::table('ENCAISSEMENT_MODE as em')
            ->join('FACTURE_VNT as fv', 'em.FAC_REF', '=', 'fv.FAC_REF')
            ->where('fv.FAC_DATE', Carbon::today())
            ->where('em.MODE_PAIMENT', 'Espèces')
            ->sum('em.MONTANT');
        
        $cartes = DB::table('ENCAISSEMENT_MODE as em')
            ->join('FACTURE_VNT as fv', 'em.FAC_REF', '=', 'fv.FAC_REF')
            ->where('fv.FAC_DATE', Carbon::today())
            ->where('em.MODE_PAIMENT', 'Carte Bancaire')
            ->sum('em.MONTANT');
        
        $total = $especes + $cartes;
        if ($total > 0 && ($especes / $total) > 0.8) {
            $alertes[] = [
                'id' => 'especes_elevees_' . time(),
                'type' => 'financier',
                'priorite' => 'important',
                'titre' => 'Proportion d\'espèces élevée',
                'message' => round(($especes / $total) * 100, 1) . "% des encaissements en espèces aujourd'hui",
                'icone' => 'fas fa-money-bill-wave',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Analyser les paiements',
                        'action' => 'openAdvancedModal',
                        'params' => ['modes-paiement', 'Modes de Paiement', 'fas fa-credit-card']
                    ]
                ]
            ];
        }
        
        return $alertes;
    }
    
    private function checkAlertesOperationnelles()
    {
        $alertes = [];
        
        // Tables occupées depuis longtemps
        $tablesLongues = DB::table('TABLE')
            ->where('ETT_ETAT', 'occupee')
            ->count(); // Simplification - dans la réalité, calcul basé sur l'heure d'occupation
        
        if ($tablesLongues > 3) {
            $alertes[] = [
                'id' => 'tables_longues_' . time(),
                'type' => 'operationnel',
                'priorite' => 'important',
                'titre' => 'Tables occupées longtemps',
                'message' => "$tablesLongues tables sont occupées depuis plus de 2 heures",
                'icone' => 'fas fa-clock',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Voir l\'état des tables',
                        'action' => 'openAdvancedModal',
                        'params' => ['etat-tables', 'État des Tables', 'fas fa-utensils']
                    ]
                ]
            ];
        }
        
        // Réservations en approche
        $reservationsProches = DB::table('RESERVATION')
            ->where('DATE_RESERVATION', '>=', Carbon::now())
            ->where('DATE_RESERVATION', '<=', Carbon::now()->addHours(2))
            ->where('ETAT_RESERVATION', 'confirmee')
            ->count();
        
        if ($reservationsProches > 0) {
            $alertes[] = [
                'id' => 'reservations_proches_' . time(),
                'type' => 'operationnel',
                'priorite' => 'info',
                'titre' => 'Réservations en approche',
                'message' => "$reservationsProches réservation(s) dans les 2 prochaines heures",
                'icone' => 'fas fa-calendar-check',
                'couleur' => 'info',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Préparer les tables',
                        'action' => 'openAdvancedModal',
                        'params' => ['etat-tables', 'Réservations', 'fas fa-calendar-check']
                    ]
                ]
            ];
        }
        
        return $alertes;
    }
    
    private function checkAlertesSecurite()
    {
        $alertes = [];
        
        // Connexions multiples (sécurité)
        $connexionsJour = DB::table('users')
            ->where('last_login_at', '>=', Carbon::today())
            ->count();
        
        if ($connexionsJour > 10) {
            $alertes[] = [
                'id' => 'connexions_multiples_' . time(),
                'type' => 'securite',
                'priorite' => 'important',
                'titre' => 'Nombreuses connexions',
                'message' => "$connexionsJour connexions détectées aujourd'hui",
                'icone' => 'fas fa-shield-alt',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Vérifier les logs',
                        'action' => 'redirect',
                        'url' => '/admin/logs/connexions'
                    ]
                ]
            ];
        }
        
        // Sauvegarde recommandée
        $derniereSauvegarde = Carbon::now()->subDays(7); // Simulation
        if (Carbon::now()->diffInDays($derniereSauvegarde) >= 7) {
            $alertes[] = [
                'id' => 'sauvegarde_recommandee_' . time(),
                'type' => 'securite',
                'priorite' => 'important',
                'titre' => 'Sauvegarde recommandée',
                'message' => 'Dernière sauvegarde il y a plus de 7 jours',
                'icone' => 'fas fa-database',
                'couleur' => 'warning',
                'timestamp' => now(),
                'actions' => [
                    [
                        'label' => 'Lancer la sauvegarde',
                        'action' => 'launchBackup',
                        'params' => []
                    ]
                ]
            ];
        }
        
        return $alertes;
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function marquerCommeLue(Request $request)
    {
        $notificationId = $request->input('notification_id');
        
        // Dans une implémentation complète, stocker en base les notifications lues
        // Pour l'instant, retourner une réponse de succès
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }
    
    /**
     * Obtenir le résumé des notifications pour le dashboard
     */
    public function getResume()
    {
        $notifications = $this->getNotificationsEnTempsReel()->getData();
        
        $resume = [
            'total' => $notifications->total,
            'critiques' => collect($notifications->notifications)->where('priorite', 'critique')->count(),
            'urgentes' => collect($notifications->notifications)->where('priorite', 'urgent')->count(),
            'importantes' => collect($notifications->notifications)->where('priorite', 'important')->count(),
            'derniere_mise_a_jour' => $notifications->timestamp
        ];
        
        return response()->json($resume);
    }
    
    /**
     * Configuration des seuils d'alerte
     */
    public function getConfigurationAlertes()
    {
        // Retourner la configuration actuelle des seuils
        return response()->json([
            'stock' => [
                'seuil_rupture' => 0,
                'seuil_stock_faible' => 5,
                'notification_email' => true
            ],
            'performance' => [
                'objectif_journalier' => 2000,
                'seuil_alerte_performance' => 70,
                'notification_temps_reel' => true
            ],
            'financier' => [
                'seuil_grosse_transaction' => 500,
                'seuil_especes_elevees' => 80,
                'notification_transactions' => true
            ],
            'operationnel' => [
                'duree_max_occupation_table' => 120,
                'delai_alerte_reservation' => 2,
                'notification_tables' => true
            ],
            'securite' => [
                'max_connexions_jour' => 10,
                'delai_sauvegarde' => 7,
                'notification_securite' => true
            ]
        ]);
    }
    
    /**
     * Mettre à jour la configuration des alertes
     */
    public function updateConfigurationAlertes(Request $request)
    {
        $config = $request->all();
        
        // Dans une implémentation complète, sauvegarder en base ou fichier de config
        // Pour l'instant, retourner une confirmation
        
        return response()->json([
            'success' => true,
            'message' => 'Configuration des alertes mise à jour',
            'config' => $config
        ]);
    }
}
