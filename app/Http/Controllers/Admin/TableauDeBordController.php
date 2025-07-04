<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Article;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\Famille;
use App\Models\Caisse;
use App\Models\FactureVenteDetail;
use App\Models\Reglement;
use App\Models\Depense;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur pour le Tableau de Bord Administrateur
 * Toutes les statistiques et données en français
 */
class TableauDeBordController extends Controller
{
    /**
     * Affichage principal du tableau de bord
     */
    public function index()
    {
        try {
            // 1. Statistiques Financières
            $statistiquesFinancieres = $this->obtenirStatistiquesFinancieres();
            
            // 2. Gestion des Stocks
            $gestionStocks = $this->obtenirGestionStocks();
            
            // 3. Gestion Clientèle
            $gestionClientele = $this->obtenirGestionClientele();
            
            // 4. Achats et Fournisseurs
            $achatsFournisseurs = $this->obtenirAchatsFournisseurs();
            
            // 5. Gestion Restaurant
            $gestionRestaurant = $this->obtenirGestionRestaurant();
            
            // 6. Graphiques et Analyses
            $graphiquesAnalyses = $this->obtenirGraphiquesAnalyses();
            
            // 7. Gestion Financière
            $gestionFinanciere = $this->obtenirGestionFinanciere();
            
            return view('admin.tableau-de-bord-moderne', compact(
                'statistiquesFinancieres',
                'gestionStocks',
                'gestionClientele',
                'achatsFournisseurs',
                'gestionRestaurant',
                'graphiquesAnalyses',
                'gestionFinanciere'
            ));
            
        } catch (\Exception $e) {
            return view('admin.tableau-de-bord-moderne')
                ->with('erreur', 'Erreur lors du chargement des données : ' . $e->getMessage());
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
            return [
                // Chiffre d'affaires
                'ca_du_jour' => Sale::whereDate('fctv_date', $aujourdhui)
                    ->sum('fctv_mnt_ttc') ?? 0,
                    
                'ca_du_mois' => Sale::where('fctv_date', '>=', $debutMois)
                    ->sum('fctv_mnt_ttc') ?? 0,
                    
                'ca_de_annee' => Sale::where('fctv_date', '>=', $debutAnnee)
                    ->sum('fctv_mnt_ttc') ?? 0,
                    
                // Transactions
                'nb_factures_jour' => Sale::whereDate('fctv_date', $aujourdhui)->count(),
                
                'ticket_moyen' => Sale::whereDate('fctv_date', $aujourdhui)
                    ->avg('fctv_mnt_ttc') ?? 0,
                    
                // Évolution
                'evolution_ventes' => $this->calculerEvolutionVentes(),
                
                // Encaissements par mode de paiement
                'encaissements_mode_paiement' => $this->obtenirEncaissementsParMode(),
                
                // État de la caisse
                'etat_caisse' => $this->obtenirEtatCaisse(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('statistiques_financieres', $e);
        }
    }
    
    /**
     * Obtenir la gestion des stocks
     */
    private function obtenirGestionStocks()
    {
        try {
            return [
                // Inventaire général
                'nb_total_articles' => Article::count(),
                
                'valeur_stock' => Stock::join('ARTICLE', 'STOCK.ART_REF', '=', 'ARTICLE.ART_REF')
                    ->sum(DB::raw('STOCK.STK_QUANTITE * ARTICLE.art_prix_vente')) ?? 0,
                    
                'articles_rupture' => Stock::where('STK_QUANTITE', '<=', 0)->count(),
                
                'articles_stock_faible' => Stock::where('STK_QUANTITE', '<', 10)->count(),
                
                // Mouvements
                'mouvements_jour' => $this->obtenirMouvementsJour(),
                
                // Articles les plus vendus
                'articles_plus_vendus' => $this->obtenirArticlesPlusVendus(),
                
                // Démarques
                'demarques_mois' => $this->obtenirDemarquesMois(),
                
                // Inventaires
                'inventaires_en_cours' => $this->obtenirInventairesEnCours(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('gestion_stocks', $e);
        }
    }
    
    /**
     * Obtenir la gestion clientèle
     */
    private function obtenirGestionClientele()
    {
        $debutMois = Carbon::now()->startOfMonth();
        
        try {
            return [
                // Statistiques générales
                'nb_total_clients' => Customer::count(),
                
                'nouveaux_clients_mois' => Customer::count(), // À adapter selon la colonne date
                
                'clients_fideles_actifs' => Customer::where('clt_fidele', true)->count(),
                
                // Programme fidélité
                'points_fidelite_distribues' => Customer::sum('clt_pointfidilio') ?? 0,
                
                // Top clients
                'top_meilleurs_clients' => $this->obtenirTopClients(),
                
                // Catégories
                'categories_clients' => $this->obtenirCategoriesClients(),
                
                // Dépense moyenne
                'depense_moyenne_client' => $this->calculerDepenseMoyenneClient(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('gestion_clientele', $e);
        }
    }
    
    /**
     * Obtenir les achats et fournisseurs
     */
    private function obtenirAchatsFournisseurs()
    {
        $debutMois = Carbon::now()->startOfMonth();
        
        try {
            return [
                // Commandes d'achat
                'commandes_achat_mois' => 0, // À implémenter selon les tables disponibles
                
                // Factures fournisseurs
                'factures_frs_attente' => 0, // À implémenter
                
                // Top fournisseurs
                'top_fournisseurs' => collect([]), // À implémenter
                
                // Valeur des achats
                'valeur_achats_mois' => 0, // À implémenter
                
                // Bons de livraison
                'bl_en_cours' => 0, // À implémenter
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('achats_fournisseurs', $e);
        }
    }
    
    /**
     * Obtenir la gestion restaurant
     */
    private function obtenirGestionRestaurant()
    {
        try {
            return [
                // Tables
                'tables_occupees' => Table::where('tab_etat', 'OCCUPEE')->count(),
                'tables_libres' => Table::where('tab_etat', 'LIBRE')->count(),
                
                // Réservations
                'reservations_jour' => Reservation::whereDate('rsv_date', Carbon::today())->count(),
                
                // Préparation
                'commandes_preparation' => 0, // À implémenter selon PREPARATION
                
                // Articles menu populaires
                'articles_menu_populaires' => $this->obtenirArticlesMenuPopulaires(),
                
                // Temps moyen
                'temps_moyen_preparation' => 0, // À calculer
                
                // État cuisine
                'etat_cuisine' => $this->obtenirEtatCuisine(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('gestion_restaurant', $e);
        }
    }
    
    /**
     * Obtenir les graphiques et analyses
     */
    private function obtenirGraphiquesAnalyses()
    {
        try {
            return [
                // Évolution des ventes
                'evolution_ventes_30j' => $this->obtenirEvolutionVentes30Jours(),
                
                // Répartition par famille
                'repartition_familles' => $this->obtenirRepartitionParFamille(),
                
                // Heures de pointe
                'heures_pointe' => $this->obtenirHeuresPointe(),
                
                // Performance par caisse
                'performance_caisses' => $this->obtenirPerformanceCaisses(),
                
                // Analyse modes de paiement
                'analyse_modes_paiement' => $this->analyserModesPaiement(),
                
                // Croissance clientèle
                'croissance_clientele' => $this->obtenirCroissanceClientele(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('graphiques_analyses', $e);
        }
    }
    
    /**
     * Obtenir la gestion financière
     */
    private function obtenirGestionFinanciere()
    {
        try {
            return [
                // Solde de caisse
                'solde_caisse_actuel' => Caisse::sum('CSS_SOLDE_ACTUEL') ?? 0,
                
                // Dépenses
                'depenses_jour' => Depense::whereDate('DEP_DATE', Carbon::today())
                    ->sum('DEP_MONTANT') ?? 0,
                    
                'depenses_mois' => Depense::where('DEP_DATE', '>=', Carbon::now()->startOfMonth())
                    ->sum('DEP_MONTANT') ?? 0,
                    
                // Répartition dépenses
                'repartition_depenses_motif' => Depense::statistiquesParMotif(
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ),
                
                // Encaissements vs sorties
                'encaissements_vs_sorties' => $this->calculerEncaissementsVsSorties(),
                
                // Historique comptages
                'historique_comptages' => $this->obtenirHistoriqueComptages(),
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('gestion_financiere', $e);
        }
    }
    
    // =================== MÉTHODES UTILITAIRES ===================
    
    private function calculerEvolutionVentes()
    {
        $moisActuel = Sale::where('fctv_date', '>=', Carbon::now()->startOfMonth())
            ->sum('fctv_mnt_ttc');
            
        $moisPrecedent = Sale::whereBetween('fctv_date', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->sum('fctv_mnt_ttc');
        
        if ($moisPrecedent > 0) {
            return round((($moisActuel - $moisPrecedent) / $moisPrecedent) * 100, 2);
        }
        
        return 0;
    }
    
    private function obtenirEncaissementsParMode()
    {
        return Reglement::statistiquesParModePaiement(
            Carbon::today(),
            Carbon::today()->endOfDay()
        );
    }
    
    private function obtenirEtatCaisse()
    {
        return Caisse::select('CSS_DESIGNATION', 'CSS_SOLDE_ACTUEL', 'CSS_ETAT')
            ->where('CSS_ETAT', true)
            ->get();
    }
    
    private function obtenirArticlesPlusVendus()
    {
        return FactureVenteDetail::join('ARTICLE', 'FACTURE_VNT_DETAIL.ART_REF', '=', 'ARTICLE.ART_REF')
            ->select('ARTICLE.art_designation', 'ARTICLE.ART_REF')
            ->selectRaw('SUM(FACTURE_VNT_DETAIL.FCTVD_QUANTITE) as quantite_vendue')
            ->groupBy('ARTICLE.ART_REF', 'ARTICLE.art_designation')
            ->orderByDesc('quantite_vendue')
            ->limit(10)
            ->get();
    }
    
    private function obtenirTopClients()
    {
        return Sale::join('CLIENT', 'FACTURE_VNT.CLT_REF', '=', 'CLIENT.CLT_REF')
            ->select('CLIENT.clt_client', 'CLIENT.CLT_REF')
            ->selectRaw('COUNT(*) as nb_commandes')
            ->selectRaw('SUM(FACTURE_VNT.fctv_mnt_ttc) as total_depense')
            ->groupBy('CLIENT.CLT_REF', 'CLIENT.clt_client')
            ->orderByDesc('total_depense')
            ->limit(10)
            ->get();
    }
    
    private function obtenirEvolutionVentes30Jours()
    {
        return Sale::selectRaw('CAST(fctv_date as DATE) as date')
            ->selectRaw('SUM(fctv_mnt_ttc) as total_ventes')
            ->where('fctv_date', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('fctv_date')
            ->groupByRaw('CAST(fctv_date as DATE)')
            ->orderBy('date')
            ->get();
    }
    
    private function obtenirRepartitionParFamille()
    {
        return FactureVenteDetail::join('ARTICLE', 'FACTURE_VNT_DETAIL.ART_REF', '=', 'ARTICLE.ART_REF')
            ->join('FAMILLE', 'ARTICLE.fam_ref', '=', 'FAMILLE.FAM_REF')
            ->select('FAMILLE.FAM_DESIGNATION')
            ->selectRaw('SUM(FACTURE_VNT_DETAIL.FCTVD_PRIX_TOTAL) as total_ventes')
            ->groupBy('FAMILLE.FAM_REF', 'FAMILLE.FAM_DESIGNATION')
            ->orderByDesc('total_ventes')
            ->get();
    }
    
    private function obtenirHeuresPointe()
    {
        return Sale::selectRaw('DATEPART(HOUR, fctv_date) as heure')
            ->selectRaw('COUNT(*) as nb_transactions')
            ->selectRaw('SUM(fctv_mnt_ttc) as ca_heure')
            ->whereNotNull('fctv_date')
            ->groupByRaw('DATEPART(HOUR, fctv_date)')
            ->orderByDesc('nb_transactions')
            ->limit(8)
            ->get();
    }
    
    private function retournerValeursParDefaut($section, $exception)
    {
        // Valeurs par défaut en cas d'erreur
        $defaults = [
            'statistiques_financieres' => [
                'ca_du_jour' => 0,
                'ca_du_mois' => 0,
                'ca_de_annee' => 0,
                'nb_factures_jour' => 0,
                'ticket_moyen' => 0,
                'evolution_ventes' => 0,
                'erreur' => $exception->getMessage()
            ],
            'gestion_stocks' => [
                'nb_total_articles' => 0,
                'valeur_stock' => 0,
                'articles_rupture' => 0,
                'articles_stock_faible' => 0,
                'erreur' => $exception->getMessage()
            ],
            // Ajouter d'autres sections selon les besoins
        ];
        
        return $defaults[$section] ?? ['erreur' => $exception->getMessage()];
    }
    
    // Méthodes à implémenter selon les besoins spécifiques
    private function obtenirMouvementsJour() { return 0; }
    private function obtenirDemarquesMois() { return 0; }
    private function obtenirInventairesEnCours() { return 0; }
    private function obtenirCategoriesClients() { return collect([]); }
    private function calculerDepenseMoyenneClient() { return 0; }
    private function obtenirArticlesMenuPopulaires() { return collect([]); }
    private function obtenirEtatCuisine() { return []; }
    private function obtenirPerformanceCaisses() { return collect([]); }
    private function analyserModesPaiement() { return collect([]); }
    private function obtenirCroissanceClientele() { return collect([]); }
    private function calculerEncaissementsVsSorties() { return []; }
    private function obtenirHistoriqueComptages() { return collect([]); }
}
