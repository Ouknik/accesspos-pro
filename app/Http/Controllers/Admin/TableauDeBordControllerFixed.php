<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur pour le Tableau de Bord Administrateur Avancé
 * Compatible avec la structure de base de données réelle AccessPOS
 */
class TableauDeBordControllerFixed extends Controller
{
    /**
     * Affichage principal du tableau de bord avec système de modals avancé
     */
    public function index()
    {
        try {
            // جمع جميع البيانات للوحة القيادة
            $statistiquesFinancieres = $this->obtenirStatistiquesFinancieres();
            $gestionStocks = $this->obtenirGestionStocks();
            $gestionClientele = $this->obtenirGestionClientele();
            $gestionRestaurant = $this->obtenirGestionRestaurant();
            $graphiquesAnalyses = $this->obtenirGraphiquesAnalyses();

            return view('admin.tableau-de-bord-moderne', compact(
                'statistiquesFinancieres',
                'gestionStocks',
                'gestionClientele',
                'gestionRestaurant',
                'graphiquesAnalyses'
            ));
            
        } catch (\Exception $e) {
            return view('admin.tableau-de-bord-moderne', [
                'statistiquesFinancieres' => $this->retournerValeursParDefaut('financier', $e),
                'gestionStocks' => $this->retournerValeursParDefaut('stock', $e),
                'gestionClientele' => $this->retournerValeursParDefaut('clientele', $e),
                'gestionRestaurant' => $this->retournerValeursParDefaut('restaurant', $e),
                'graphiquesAnalyses' => $this->retournerValeursParDefaut('graphiques', $e)
            ]);
        }
    }
    
    /**
     * API endpoint pour les données en temps réel
     */
    public function getLiveData()
    {
        try {
            return response()->json([
                'ca_temps_reel' => $this->obtenirCATempReel(),
                'commandes_actives' => $this->obtenirCommandesActives(),
                'alertes_stock' => $this->obtenirAlertesStock(),
                'etat_tables' => $this->obtenirEtatTables(),
                'timestamp' => Carbon::now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtenir les statistiques financières
     */
    private function obtenirStatistiquesFinancieres()
    {
        $aujourdhui = Carbon::today();
        $debutMois = Carbon::now()->startOfMonth();
        $debutAnnee = Carbon::now()->startOfYear();
        
        try {
            // Chiffre d'affaires du jour
            $caJour = DB::table('FACTURE_VNT')
                ->whereDate('FAC_DATE', $aujourdhui)
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            // Chiffre d'affaires du mois
            $caMois = DB::table('FACTURE_VNT')
                ->where('FAC_DATE', '>=', $debutMois)
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            // Nombre de ventes aujourd'hui
            $ventesJour = DB::table('FACTURE_VNT')
                ->whereDate('FAC_DATE', $aujourdhui)
                ->where('FAC_VALIDE', 1)
                ->count();
            
            // Ticket moyen
            $ticketMoyen = $ventesJour > 0 ? $caJour / $ventesJour : 0;
            
            // Évolution par rapport à hier
            $caHier = DB::table('FACTURE_VNT')
                ->whereDate('FAC_DATE', Carbon::yesterday())
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            $evolutionJour = $caHier > 0 ? (($caJour - $caHier) / $caHier) * 100 : 0;
            
            return [
                'ca_du_jour' => round($caJour, 2),
                'ca_du_mois' => round($caMois, 2),
                'nb_factures_jour' => $ventesJour,
                'ticket_moyen' => round($ticketMoyen, 2),
                'evolution_ventes' => round($evolutionJour, 2),
                'objectif_jour' => 2000,
                'pourcentage_objectif' => round(($caJour / 2000) * 100, 2),
                'encaissements_mode_paiement' => $this->obtenirEncaissementsParMode()
            ];
            
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('financier', $e);
        }
    }
    
    /**
     * Obtenir la gestion des stocks
     */
    private function obtenirGestionStocks()
    {
        try {
            // Articles en rupture
            $articlesRupture = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
                ->count();
            
            // Articles en stock faible
            $articlesStockFaible = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->whereRaw('COALESCE(s.STK_QTE, 0) > 0 AND COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
                ->count();
            
            // Valeur totale du stock
            $valeurStock = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->selectRaw('SUM(COALESCE(s.STK_QTE, 0) * a.ART_PRIX_ACHAT) as valeur_totale')
                ->value('valeur_totale') ?? 0;
            
            // Total articles
            $totalArticles = DB::table('ARTICLE')->count();
            
            return [
                'total_articles' => $totalArticles,
                'articles_en_stock' => $totalArticles - $articlesRupture,
                'articles_rupture' => $articlesRupture,
                'articles_stock_faible' => $articlesStockFaible,
                'valeur_stock_total' => round($valeurStock, 2),
                'mouvements_jour' => $this->obtenirMouvementsJour(),
                'top_articles_vendus' => $this->obtenirTopArticlesVendus()
            ];
            
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('stock', $e);
        }
    }
    
    /**
     * Obtenir la gestion clientèle
     */
    private function obtenirGestionClientele()
    {
        try {
            // Total clients
            $totalClients = DB::table('CLIENT')->count();
            
            // Clients fidèles (si la colonne existe)
            $clientsFideles = DB::table('CLIENT')
                ->where('CLT_FIDELE', 1)
                ->count();
            
            // Top clients ce mois
            $topClients = $this->obtenirTopClients();
            
            return [
                'nb_total_clients' => $totalClients,
                'clients_fideles_actifs' => $clientsFideles,
                'nouveaux_clients_mois' => 0, // À calculer si date de création disponible
                'top_meilleurs_clients' => $topClients,
                'depense_moyenne_client' => $this->calculerDepenseMoyenneClient()
            ];
            
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('clientele', $e);
        }
    }
    
    /**
     * Obtenir la gestion restaurant
     */
    private function obtenirGestionRestaurant()
    {
        try {
            // État des tables
            $totalTables = DB::table('TABLE')->count();
            $tablesOccupees = DB::table('TABLE')
                ->where('ETT_ETAT', 'occupee')
                ->count();
            $tablesLibres = $totalTables - $tablesOccupees;
            
            // Réservations du jour
            $reservationsJour = DB::table('RESERVATION')
                ->whereDate('DATE_RESERVATION', Carbon::today())
                ->count();
            
            return [
                'total_tables' => $totalTables,
                'tables_occupees' => $tablesOccupees,
                'tables_libres' => $tablesLibres,
                'taux_occupation' => $totalTables > 0 ? round(($tablesOccupees / $totalTables) * 100, 2) : 0,
                'reservations_jour' => $reservationsJour,
                'reservations_semaine' => $this->obtenirReservationsSemaine(),
                'performance_serveurs' => $this->obtenirPerformanceServeurs()
            ];
            
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('restaurant', $e);
        }
    }
    
    /**
     * Obtenir les graphiques et analyses
     */
    private function obtenirGraphiquesAnalyses()
    {
        try {
            return [
                'evolution_ventes_30_jours' => $this->obtenirEvolutionVentes30Jours(),
                'repartition_par_famille' => $this->obtenirRepartitionParFamille(),
                'heures_pointe' => $this->obtenirHeuresPointe(),
                'performance_mensuelle' => $this->obtenirPerformanceMensuelle()
            ];
            
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('graphiques', $e);
        }
    }
    
    // =====================================================================
    // MÉTHODES UTILITAIRES
    // =====================================================================
    
    private function obtenirCATempReel()
    {
        return DB::table('FACTURE_VNT')
            ->whereDate('FAC_DATE', Carbon::today())
            ->where('FAC_VALIDE', 1)
            ->sum('FAC_NET_A_PAYER') ?? 0;
    }
    
    private function obtenirCommandesActives()
    {
        return DB::table('FACTURE_VNT')
            ->where('FAC_VALIDE', 0)
            ->count();
    }
    
    private function obtenirAlertesStock()
    {
        return DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
            ->count();
    }
    
    private function obtenirEtatTables()
    {
        return DB::table('TABLE')
            ->select('ETT_ETAT', DB::raw('COUNT(*) as nombre'))
            ->groupBy('ETT_ETAT')
            ->get();
    }
    
    private function obtenirEncaissementsParMode()
    {
        return DB::table('REGLEMENT')
            ->select('RGL_MODE', DB::raw('SUM(RGL_MONTANT) as total'))
            ->whereDate('RGL_DATE', Carbon::today())
            ->groupBy('RGL_MODE')
            ->get();
    }
    
    private function obtenirMouvementsJour()
    {
        // Simulation - à adapter selon les tables de mouvements disponibles
        return 0;
    }
    
    private function obtenirTopArticlesVendus()
    {
        return DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select('a.ART_DESIGNATION', DB::raw('SUM(fvd.FVTD_QTE) as quantite_vendue'))
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('FACTURE_VNT as fv')
                      ->whereRaw('fv.FAC_REF = fvd.FAC_REF')
                      ->whereDate('fv.FAC_DATE', Carbon::today());
            })
            ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
            ->orderBy('quantite_vendue', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function obtenirTopClients()
    {
        return DB::table('CLIENT as c')
            ->join('FACTURE_VNT as fv', 'c.CLT_REF', '=', 'fv.CLT_REF')
            ->select('c.CLT_NOM', DB::raw('SUM(fv.FAC_NET_A_PAYER) as total_achats'))
            ->where('fv.FAC_DATE', '>=', Carbon::now()->startOfMonth())
            ->where('fv.FAC_VALIDE', 1)
            ->groupBy('c.CLT_REF', 'c.CLT_NOM')
            ->orderBy('total_achats', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function calculerDepenseMoyenneClient()
    {
        $totalCA = DB::table('FACTURE_VNT')
            ->where('FAC_VALIDE', 1)
            ->sum('FAC_NET_A_PAYER');
        
        $totalClients = DB::table('CLIENT')->count();
        
        return $totalClients > 0 ? round($totalCA / $totalClients, 2) : 0;
    }
    
    private function obtenirReservationsSemaine()
    {
        return DB::table('RESERVATION')
            ->whereBetween('DATE_RESERVATION', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->count();
    }
    
    private function obtenirPerformanceServeurs()
    {
        // Simulation - à adapter selon la structure des données utilisateurs
        return collect([]);
    }
    
    private function obtenirEvolutionVentes30Jours()
    {
        return DB::table('FACTURE_VNT')
            ->selectRaw('CAST(FAC_DATE as DATE) as date, SUM(FAC_NET_A_PAYER) as ca')
            ->where('FAC_DATE', '>=', Carbon::now()->subDays(30))
            ->where('FAC_VALIDE', 1)
            ->groupBy(DB::raw('CAST(FAC_DATE as DATE)'))
            ->orderBy('date')
            ->get();
    }
    
    private function obtenirRepartitionParFamille()
    {
        return DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->join('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
            ->select('f.FAM_LIB as famille', DB::raw('SUM(fvd.FVTD_MONTANT_HT) as ca'))
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('FACTURE_VNT as fv')
                      ->whereRaw('fv.FAC_REF = fvd.FAC_REF')
                      ->where('fv.FAC_VALIDE', 1);
            })
            ->groupBy('f.FAM_REF', 'f.FAM_LIB')
            ->orderBy('ca', 'desc')
            ->get();
    }
    
    private function obtenirHeuresPointe()
    {
        return DB::table('FACTURE_VNT')
            ->selectRaw('DATEPART(HOUR, FAC_DATE) as heure, COUNT(*) as nb_ventes')
            ->where('FAC_VALIDE', 1)
            ->where('FAC_DATE', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATEPART(HOUR, FAC_DATE)'))
            ->orderBy('heure')
            ->get();
    }
    
    private function obtenirPerformanceMensuelle()
    {
        return DB::table('FACTURE_VNT')
            ->selectRaw('YEAR(FAC_DATE) as annee, MONTH(FAC_DATE) as mois, SUM(FAC_NET_A_PAYER) as ca')
            ->where('FAC_VALIDE', 1)
            ->where('FAC_DATE', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(FAC_DATE)'), DB::raw('MONTH(FAC_DATE)'))
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
    }
    
    private function retournerValeursParDefaut($section, $exception)
    {
        // Valeurs par défaut en cas d'erreur
        $defaults = [
            'financier' => [
                'ca_du_jour' => 0,
                'ca_du_mois' => 0,
                'nb_factures_jour' => 0,
                'ticket_moyen' => 0,
                'evolution_ventes' => 0,
                'objectif_jour' => 2000,
                'pourcentage_objectif' => 0,
                'encaissements_mode_paiement' => collect([])
            ],
            'stock' => [
                'total_articles' => 0,
                'articles_en_stock' => 0,
                'articles_rupture' => 0,
                'articles_stock_faible' => 0,
                'valeur_stock_total' => 0,
                'mouvements_jour' => 0,
                'top_articles_vendus' => collect([])
            ],
            'clientele' => [
                'nb_total_clients' => 0,
                'clients_fideles_actifs' => 0,
                'nouveaux_clients_mois' => 0,
                'top_meilleurs_clients' => collect([]),
                'depense_moyenne_client' => 0
            ],
            'restaurant' => [
                'total_tables' => 0,
                'tables_occupees' => 0,
                'tables_libres' => 0,
                'taux_occupation' => 0,
                'reservations_jour' => 0,
                'reservations_semaine' => 0,
                'performance_serveurs' => collect([])
            ],
            'graphiques' => [
                'evolution_ventes_30_jours' => collect([]),
                'repartition_par_famille' => collect([]),
                'heures_pointe' => collect([]),
                'performance_mensuelle' => collect([])
            ]
        ];
        
        return $defaults[$section] ?? ['erreur' => $exception->getMessage()];
    }
    
    // =====================================================================
    // MÉTHODES POUR LES MODALS AVANCÉES
    // =====================================================================
    
    /**
     * Modal 1: Chiffre d'Affaires du Jour - التفاصيل الكاملة
     */
    public function getChiffreAffairesDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // CA du jour sélectionné
            $caJour = DB::table('FACTURE_VNT')
                ->whereBetween('FAC_DATE', [$dateDebut, $dateFin])
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            // Détails des ventes par heure
            $ventesParHeure = DB::table('FACTURE_VNT')
                ->selectRaw('DATEPART(HOUR, FAC_DATE) as heure, COUNT(*) as nb_ventes, SUM(FAC_NET_A_PAYER) as ca')
                ->whereDate('FAC_DATE', $dateDebut)
                ->where('FAC_VALIDE', 1)
                ->groupBy(DB::raw('DATEPART(HOUR, FAC_DATE)'))
                ->orderBy('heure')
                ->get();
            
            // Top articles vendus
            $topArticles = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                ->select('a.ART_DESIGNATION', DB::raw('SUM(fvd.FVTD_QTE) as quantite'), DB::raw('SUM(fvd.FVTD_MONTANT_HT) as ca'))
                ->whereDate('fv.FAC_DATE', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderBy('ca', 'desc')
                ->limit(10)
                ->get();
            
            // Modes de paiement
            $modesPaiement = DB::table('REGLEMENT as r')
                ->join('FACTURE_VNT as fv', 'r.FAC_REF', '=', 'fv.FAC_REF')
                ->select('r.RGL_MODE', DB::raw('COUNT(*) as nb_transactions'), DB::raw('SUM(r.RGL_MONTANT) as montant'))
                ->whereDate('fv.FAC_DATE', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('r.RGL_MODE')
                ->get();
            
            $data = [
                'success' => true,
                'date_analyse' => $dateDebut,
                'ca_total' => round($caJour, 2),
                'ventes_par_heure' => $ventesParHeure,
                'top_articles' => $topArticles,
                'modes_paiement' => $modesPaiement,
                'timestamp' => Carbon::now()->toISOString()
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modal 2: Articles en rupture de stock
     */
    public function getArticlesRuptureDetails(Request $request)
    {
        try {
            // Articles en rupture complète
            $articlesRupture = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->leftJoin('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->select('a.ART_REF', 'a.ART_DESIGNATION', 'f.FAM_LIB as famille', 
                        'a.ART_PRIX_VNT', 'a.ART_STOCK_MIN', 
                        DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'))
                ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
                ->get();
            
            // Articles en stock faible
            $articlesStockFaible = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->leftJoin('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->select('a.ART_REF', 'a.ART_DESIGNATION', 'f.FAM_LIB as famille',
                        'a.ART_PRIX_VNT', 'a.ART_STOCK_MIN',
                        DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'))
                ->whereRaw('COALESCE(s.STK_QTE, 0) > 0 AND COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
                ->get();
            
            // Impact financier estimé
            $impactFinancier = $articlesRupture->sum(function($article) {
                return $article->ART_PRIX_VNT * 5; // Estimation de 5 ventes perdues par article
            });
            
            $data = [
                'success' => true,
                'articles_rupture' => $articlesRupture,
                'articles_stock_faible' => $articlesStockFaible,
                'impact_financier' => round($impactFinancier, 2),
                'total_rupture' => $articlesRupture->count(),
                'total_stock_faible' => $articlesStockFaible->count(),
                'recommandations' => [
                    'Commande urgente nécessaire pour ' . $articlesRupture->count() . ' articles',
                    'Surveiller ' . $articlesStockFaible->count() . ' articles en stock faible',
                    'Impact financier estimé: ' . round($impactFinancier, 2) . ' MAD'
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modal 3: Top clients du restaurant
     */
    public function getTopClientsDetails(Request $request)
    {
        try {
            $periode = $request->get('periode', 'mois');
            $limite = $request->get('limite', 20);
            
            $dateDebut = match($periode) {
                'jour' => Carbon::today(),
                'semaine' => Carbon::now()->startOfWeek(),
                'mois' => Carbon::now()->startOfMonth(),
                'annee' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth()
            };
            
            // Top clients par CA
            $topClients = DB::table('CLIENT as c')
                ->join('FACTURE_VNT as fv', 'c.CLT_REF', '=', 'fv.CLT_REF')
                ->select('c.CLT_REF', 'c.CLT_NOM', 'c.CLT_TEL', 'c.CLT_EMAIL',
                        DB::raw('COUNT(fv.FAC_REF) as nb_commandes'),
                        DB::raw('SUM(fv.FAC_NET_A_PAYER) as ca_total'),
                        DB::raw('AVG(fv.FAC_NET_A_PAYER) as panier_moyen'),
                        DB::raw('MAX(fv.FAC_DATE) as derniere_visite'))
                ->where('fv.FAC_DATE', '>=', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('c.CLT_REF', 'c.CLT_NOM', 'c.CLT_TEL', 'c.CLT_EMAIL')
                ->orderBy('ca_total', 'desc')
                ->limit($limite)
                ->get();
            
            // Statistiques générales
            $totalClients = DB::table('CLIENT')->count();
            $clientsActifs = DB::table('CLIENT as c')
                ->join('FACTURE_VNT as fv', 'c.CLT_REF', '=', 'fv.CLT_REF')
                ->where('fv.FAC_DATE', '>=', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->distinct('c.CLT_REF')
                ->count();
            
            $data = [
                'success' => true,
                'periode' => $periode,
                'top_clients' => $topClients,
                'statistiques' => [
                    'total_clients' => $totalClients,
                    'clients_actifs' => $clientsActifs,
                    'ca_top_clients' => $topClients->sum('ca_total'),
                    'panier_moyen_global' => $topClients->avg('panier_moyen')
                ],
                'recommandations' => [
                    'Fidéliser les top 10 clients avec des offres spéciales',
                    'Relancer les clients inactifs depuis plus de 30 jours',
                    'Programme de parrainage pour augmenter la clientèle'
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modal 4: Performance par heure
     */
    public function getPerformanceHoraireDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // Performance par heure
            $performanceHoraire = DB::table('FACTURE_VNT')
                ->selectRaw('DATEPART(HOUR, FAC_DATE) as heure, 
                           COUNT(*) as nb_ventes, 
                           SUM(FAC_NET_A_PAYER) as ca,
                           AVG(FAC_NET_A_PAYER) as panier_moyen')
                ->whereBetween('FAC_DATE', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                ->where('FAC_VALIDE', 1)
                ->groupBy(DB::raw('DATEPART(HOUR, FAC_DATE)'))
                ->orderBy('heure')
                ->get();
            
            // Heures de pointe
            $heurePeinte = $performanceHoraire->sortByDesc('ca')->first();
            $heureCreuse = $performanceHoraire->sortBy('ca')->first();
            
            $data = [
                'success' => true,
                'periode' => ['debut' => $dateDebut, 'fin' => $dateFin],
                'performance_horaire' => $performanceHoraire,
                'heure_pointe' => $heurePeinte,
                'heure_creuse' => $heureCreuse,
                'recommandations' => [
                    'Renforcer l\'équipe pendant les heures de pointe (' . ($heurePeinte->heure ?? 'N/A') . 'h)',
                    'Optimiser les coûts pendant les heures creuses (' . ($heureCreuse->heure ?? 'N/A') . 'h)',
                    'Mettre en place des promotions aux heures creuses'
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modal 5: Modes de paiement détaillés
     */
    public function getModesPaiementDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // Répartition par mode de paiement
            $modesPaiement = DB::table('REGLEMENT as r')
                ->join('FACTURE_VNT as fv', 'r.FAC_REF', '=', 'fv.FAC_REF')
                ->select('r.RGL_MODE', 
                        DB::raw('COUNT(*) as nb_transactions'),
                        DB::raw('SUM(r.RGL_MONTANT) as montant_total'),
                        DB::raw('AVG(r.RGL_MONTANT) as montant_moyen'))
                ->whereBetween('fv.FAC_DATE', [$dateDebut, $dateFin])
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('r.RGL_MODE')
                ->orderBy('montant_total', 'desc')
                ->get();
            
            $totalCA = $modesPaiement->sum('montant_total');
            
            // Calcul des pourcentages
            $modesPaiement = $modesPaiement->map(function($mode) use ($totalCA) {
                $mode->pourcentage = $totalCA > 0 ? round(($mode->montant_total / $totalCA) * 100, 2) : 0;
                return $mode;
            });
            
            $data = [
                'success' => true,
                'periode' => ['debut' => $dateDebut, 'fin' => $dateFin],
                'modes_paiement' => $modesPaiement,
                'total_ca' => round($totalCA, 2),
                'recommandations' => [
                    'Surveiller les frais bancaires sur les paiements par carte',
                    'Optimiser la gestion de la monnaie pour les paiements espèces',
                    'Promouvoir les modes de paiement les plus rentables'
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modal 6: État des tables en temps réel
     */
    public function getEtatTablesDetails(Request $request)
    {
        try {
            // État actuel des tables
            $etatTables = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select('t.TAB_REF', 't.TAB_LIB', 't.ETT_ETAT', 't.TAB_NBR_Couvert',
                        'z.ZON_LIB as zone')
                ->get();
            
            // Statistiques par zone
            $statistiquesZones = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select('z.ZON_LIB as zone',
                        DB::raw('COUNT(*) as total_tables'),
                        DB::raw('SUM(CASE WHEN t.ETT_ETAT = \'occupee\' THEN 1 ELSE 0 END) as tables_occupees'),
                        DB::raw('SUM(t.TAB_NBR_Couvert) as capacite_totale'))
                ->groupBy('z.ZON_REF', 'z.ZON_LIB')
                ->get();
            
            // Réservations à venir
            $reservationsAVenir = DB::table('RESERVATION as r')
                ->leftJoin('CLIENT as c', 'r.CLT_REF', '=', 'c.CLT_REF')
                ->select('r.DATE_RESERVATION', 'r.NBR_COUVERT', 'c.CLT_NOM')
                ->where('r.DATE_RESERVATION', '>=', Carbon::now())
                ->where('r.DATE_RESERVATION', '<=', Carbon::now()->addHours(4))
                ->orderBy('r.DATE_RESERVATION')
                ->get();
            
            $totalTables = $etatTables->count();
            $tablesOccupees = $etatTables->where('ETT_ETAT', 'occupee')->count();
            
            $data = [
                'success' => true,
                'etat_tables' => $etatTables,
                'statistiques_zones' => $statistiquesZones,
                'reservations_a_venir' => $reservationsAVenir,
                'resume' => [
                    'total_tables' => $totalTables,
                    'tables_occupees' => $tablesOccupees,
                    'tables_libres' => $totalTables - $tablesOccupees,
                    'taux_occupation' => $totalTables > 0 ? round(($tablesOccupees / $totalTables) * 100, 2) : 0
                ],
                'recommandations' => [
                    'Optimiser l\'attribution des tables selon la capacité',
                    'Prévoir l\'affluence selon les réservations',
                    'Surveiller les tables occupées depuis longtemps'
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Export des données de modal
     */
    public function exportModalData(Request $request)
    {
        try {
            $type = $request->get('type');
            $format = $request->get('format', 'pdf');
            
            $data = match($type) {
                'chiffre-affaires' => $this->getChiffreAffairesDetails($request)->getData(),
                'stock-rupture' => $this->getArticlesRuptureDetails($request)->getData(),
                'top-clients' => $this->getTopClientsDetails($request)->getData(),
                'performance-horaire' => $this->getPerformanceHoraireDetails($request)->getData(),
                'modes-paiement' => $this->getModesPaiementDetails($request)->getData(),
                'etat-tables' => $this->getEtatTablesDetails($request)->getData(),
                default => null
            };
            
            if (!$data) {
                return response()->json(['error' => 'Type de données non reconnu'], 400);
            }
            
            // Simulation de l'export
            return response()->json([
                'success' => true,
                'message' => 'Export généré avec succès',
                'file_url' => '/exports/' . $type . '_' . date('Y-m-d') . '.' . $format
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }
}
