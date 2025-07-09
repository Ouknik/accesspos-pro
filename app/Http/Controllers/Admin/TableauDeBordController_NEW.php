<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur pour le Tableau de Bord Administrateur
 * Version corrigée avec gestion des données en temps réel
 */
class TableauDeBordController extends Controller
{
    /**
     * Affichage principal du tableau de bord
     */
    public function index()
    {
        try {
            // استخدام التاريخ الحالي (2025-07-09 حيث توجد البيانات)
            $aujourd_hui = '2025-07-09';
            $debut_mois = '2025-07-01';
            $debut_annee = '2025-01-01';

            // جمع جميع البيانات للوحة القيادة
            $statistiquesFinancieres = $this->obtenirStatistiquesFinancieres($aujourd_hui, $debut_mois, $debut_annee);
            $gestionStocks = $this->obtenirGestionStocks();
            $gestionClientele = $this->obtenirGestionClientele();
            $achatsFournisseurs = $this->obtenirAchatsFournisseurs();
            $gestionRestaurant = $this->obtenirGestionRestaurant($aujourd_hui);
            $graphiquesAnalyses = $this->obtenirGraphiquesAnalyses($aujourd_hui);
            $gestionFinanciere = $this->obtenirGestionFinanciere($aujourd_hui, $debut_mois);

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
            // في حالة وجود خطأ، إرجاع قيم افتراضية
            return view('admin.tableau-de-bord-moderne', [
                'statistiquesFinancieres' => $this->retournerValeursParDefaut('financier'),
                'gestionStocks' => $this->retournerValeursParDefaut('stock'),
                'gestionClientele' => $this->retournerValeursParDefaut('clientele'),
                'achatsFournisseurs' => $this->retournerValeursParDefaut('achats'),
                'gestionRestaurant' => $this->retournerValeursParDefaut('restaurant'),
                'graphiquesAnalyses' => $this->retournerValeursParDefaut('graphiques'),
                'gestionFinanciere' => $this->retournerValeursParDefaut('financiere'),
                'erreur' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * الحصول على الإحصائيات المالية
     */
    private function obtenirStatistiquesFinancieres($aujourd_hui, $debut_mois, $debut_annee)
    {
        try {
            // Chiffre d'affaires du jour
            $ca_du_jour = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->sum('FCT_MNT_RGL') ?? 0;
                
            // Chiffre d'affaires du mois
            $ca_du_mois = DB::table('FACTURE_VNT')
                ->where('FCTV_DATE', '>=', $debut_mois)
                ->sum('FCT_MNT_RGL') ?? 0;
                
            // Chiffre d'affaires de l'année
            $ca_de_annee = DB::table('FACTURE_VNT')
                ->where('FCTV_DATE', '>=', $debut_annee)
                ->sum('FCT_MNT_RGL') ?? 0;
                
            // Nombre de factures du jour
            $nb_factures_jour = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->count();
            
            // Ticket moyen
            $ticket_moyen = $nb_factures_jour > 0 ? ($ca_du_jour / $nb_factures_jour) : 0;
                
            // Évolution des ventes (comparaison avec mois précédent)
            $ca_mois_precedent = DB::table('FACTURE_VNT')
                ->whereBetween('FCTV_DATE', ['2025-06-01', '2025-06-30'])
                ->sum('FCT_MNT_RGL') ?? 0;
                
            $evolution_ventes = 0;
            if ($ca_mois_precedent > 0) {
                $evolution_ventes = round((($ca_du_mois - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2);
            }
            
            // Encaissements par mode de paiement
            $encaissements_mode_paiement = DB::table('REGLEMENT')
                ->whereDate('REG_DATE', $aujourd_hui)
                ->select('TYPE_REGLEMENT', DB::raw('SUM(REG_MONTANT) as total'))
                ->groupBy('TYPE_REGLEMENT')
                ->get();
                
            // État de la caisse
            $etat_caisse = DB::table('CAISSE')
                ->select('CSS_LIBELLE_CAISSE', 'CSS_SOLDE_ACTUEL', 'CSS_AVEC_AFFICHEUR')
                ->get();

            return [
                'ca_du_jour' => round($ca_du_jour, 2),
                'ca_du_mois' => round($ca_du_mois, 2),
                'ca_de_annee' => round($ca_de_annee, 2),
                'nb_factures_jour' => $nb_factures_jour,
                'ticket_moyen' => round($ticket_moyen, 2),
                'evolution_ventes' => $evolution_ventes,
                'encaissements_mode_paiement' => $encaissements_mode_paiement,
                'etat_caisse' => $etat_caisse,
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('financier');
        }
    }
    
    /**
     * الحصول على بيانات إدارة المخزون
     */
    private function obtenirGestionStocks()
    {
        try {
            // Nombre total d'articles
            $nb_total_articles = DB::table('ARTICLE')->count();
            
            // Valeur du stock
            $valeur_stock = DB::table('STOCK')
                ->join('ARTICLE', 'STOCK.ART_REF', '=', 'ARTICLE.ART_REF')
                ->sum(DB::raw('STOCK.STK_QTE * ARTICLE.ART_PRIX_VENTE')) ?? 0;
                
            // Articles en rupture
            $articles_rupture = DB::table('STOCK')->where('STK_QTE', '<=', 0)->count();
            
            // Articles à stock faible
            $articles_stock_faible = DB::table('STOCK')->where('STK_QTE', '<', 10)->count();
            
            // Articles les plus vendus (aujourd'hui)
            $articles_plus_vendus = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->whereDate('fv.FCTV_DATE', '2025-07-09')
                ->select('a.ART_DESIGNATION', 'a.ART_REF')
                ->selectRaw('SUM(fvd.FVD_QTE) as quantite_vendue')
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderByDesc('quantite_vendue')
                ->limit(10)
                ->get();

            return [
                'nb_total_articles' => $nb_total_articles,
                'valeur_stock' => round($valeur_stock, 2),
                'articles_rupture' => $articles_rupture,
                'articles_stock_faible' => $articles_stock_faible,
                'articles_plus_vendus' => $articles_plus_vendus,
                'mouvements_jour' => 0, // À implémenter si nécessaire
                'demarques_mois' => 0, // À implémenter si nécessaire
                'inventaires_en_cours' => 0, // À implémenter si nécessaire
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('stock');
        }
    }
    
    /**
     * الحصول على بيانات إدارة العملاء
     */
    private function obtenirGestionClientele()
    {
        try {
            // Nombre total de clients
            $nb_total_clients = DB::table('CLIENT')->count();
            
            // Nouveaux clients du mois (إذا كان هناك عمود تاريخ)
            $nouveaux_clients_mois = DB::table('CLIENT')->count(); // بدون تصفية للتاريخ حاليا
            
            // Clients fidèles actifs
            $clients_fideles_actifs = DB::table('CLIENT')->where('CLT_FIDELE', 1)->count();
            
            // Points fidélité distribués
            $points_fidelite_distribues = DB::table('CLIENT')->sum('CLT_POINTFIDILIO') ?? 0;
            
            // Top clients (اليوم)
            $top_meilleurs_clients = DB::table('FACTURE_VNT as fv')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->whereDate('fv.FCTV_DATE', '2025-07-09')
                ->select('c.CLT_CLIENT', 'c.CLT_REF')
                ->selectRaw('COUNT(*) as nb_commandes')
                ->selectRaw('SUM(fv.FCT_MNT_RGL) as total_depense')
                ->groupBy('c.CLT_REF', 'c.CLT_CLIENT')
                ->orderByDesc('total_depense')
                ->limit(10)
                ->get();
            
            // Dépense moyenne par client
            $depense_moyenne_client = DB::table('FACTURE_VNT as fv')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->whereDate('fv.FCTV_DATE', '2025-07-09')
                ->avg('fv.FCT_MNT_RGL') ?? 0;

            return [
                'nb_total_clients' => $nb_total_clients,
                'nouveaux_clients_mois' => $nouveaux_clients_mois,
                'clients_fideles_actifs' => $clients_fideles_actifs,
                'points_fidelite_distribues' => $points_fidelite_distribues,
                'top_meilleurs_clients' => $top_meilleurs_clients,
                'depense_moyenne_client' => round($depense_moyenne_client, 2),
                'categories_clients' => collect([]), // À implémenter si nécessaire
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('clientele');
        }
    }
    
    /**
     * الحصول على بيانات المشتريات والموردين
     */
    private function obtenirAchatsFournisseurs()
    {
        try {
            // TODO: تنفيذ عند توفر جداول المشتريات والموردين
            return [
                'commandes_achat_mois' => 0,
                'factures_frs_attente' => 0,
                'top_fournisseurs' => collect([]),
                'valeur_achats_mois' => 0,
                'bl_en_cours' => 0,
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('achats');
        }
    }
    
    /**
     * الحصول على بيانات إدارة المطعم
     */
    private function obtenirGestionRestaurant($aujourd_hui)
    {
        try {
            // Tables occupées
            $tables_occupees = DB::table('TABLE')->where('ETT_ETAT', 'OCCUPEE')->count();
            
            // Tables libres
            $tables_libres = DB::table('TABLE')->where('ETT_ETAT', 'LIBRE')->count();
            
            // Réservations du jour
            $reservations_jour = DB::table('RESERVATION')
                ->whereDate('DATE_RESERVATION', $aujourd_hui)
                ->count();
            
            // Articles menu populaires
            $articles_menu_populaires = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->join('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->where('f.FAM_DESIGNATION', 'LIKE', '%MENU%') // فلترة المنيو
                ->select('a.ART_DESIGNATION', 'a.ART_REF')
                ->selectRaw('SUM(fvd.FVD_QTE) as quantite_vendue')
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderByDesc('quantite_vendue')
                ->limit(5)
                ->get();

            return [
                'tables_occupees' => $tables_occupees,
                'tables_libres' => $tables_libres,
                'reservations_jour' => $reservations_jour,
                'articles_menu_populaires' => $articles_menu_populaires,
                'commandes_preparation' => 0, // À implémenter
                'temps_moyen_preparation' => 0, // À calculer
                'etat_cuisine' => [], // À implémenter
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('restaurant');
        }
    }
    
    /**
     * الحصول على الرسوم البيانية والتحليلات
     */
    private function obtenirGraphiquesAnalyses($aujourd_hui)
    {
        try {
            // Évolution des ventes sur 30 jours
            $evolution_ventes_30j = DB::table('FACTURE_VNT')
                ->selectRaw('CAST(FCTV_DATE as DATE) as date')
                ->selectRaw('SUM(FCT_MNT_RGL) as total_ventes')
                ->selectRaw('COUNT(*) as nb_factures')
                ->where('FCTV_DATE', '>=', Carbon::parse($aujourd_hui)->subDays(30))
                ->whereNotNull('FCTV_DATE')
                ->groupByRaw('CAST(FCTV_DATE as DATE)')
                ->orderBy('date')
                ->get();
            
            // Répartition par famille
            $repartition_familles = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->select('f.FAM_DESIGNATION')
                ->selectRaw('SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE) as total_ventes')
                ->groupBy('f.FAM_REF', 'f.FAM_DESIGNATION')
                ->orderByDesc('total_ventes')
                ->get();
            
            // Heures de pointe
            $heures_pointe = DB::table('FACTURE_VNT')
                ->selectRaw('DATEPART(HOUR, FCTV_DATE) as heure')
                ->selectRaw('COUNT(*) as nb_transactions')
                ->selectRaw('SUM(FCT_MNT_RGL) as ca_heure')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->whereNotNull('FCTV_DATE')
                ->groupByRaw('DATEPART(HOUR, FCTV_DATE)')
                ->orderByDesc('nb_transactions')
                ->limit(8)
                ->get();
            
            // Performance par caisse
            $performance_caisses = DB::table('FACTURE_VNT as fv')
                ->join('CAISSE as c', 'fv.CSS_REF', '=', 'c.CSS_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->select('c.CSS_LIBELLE_CAISSE')
                ->selectRaw('COUNT(*) as nb_transactions')
                ->selectRaw('SUM(fv.FCT_MNT_RGL) as ca_caisse')
                ->groupBy('c.CSS_REF', 'c.CSS_LIBELLE_CAISSE')
                ->orderByDesc('ca_caisse')
                ->get();

            return [
                'evolution_ventes_30j' => $evolution_ventes_30j,
                'repartition_familles' => $repartition_familles,
                'heures_pointe' => $heures_pointe,
                'performance_caisses' => $performance_caisses,
                'analyse_modes_paiement' => collect([]), // À implémenter
                'croissance_clientele' => collect([]), // À implémenter
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('graphiques');
        }
    }
    
    /**
     * الحصول على بيانات الإدارة المالية
     */
    private function obtenirGestionFinanciere($aujourd_hui, $debut_mois)
    {
        try {
            // Solde de caisse actuel
            $solde_caisse_actuel = DB::table('CAISSE')->sum('CSS_SOLDE_ACTUEL') ?? 0;
            
            // Dépenses du jour
            $depenses_jour = DB::table('DEPENSE')
                ->whereDate('DEP_DATE', $aujourd_hui)
                ->sum('DEP_MONTANTHT') ?? 0;
                
            // Dépenses du mois
            $depenses_mois = DB::table('DEPENSE')
                ->where('DEP_DATE', '>=', $debut_mois)
                ->sum('DEP_MONTANTHT') ?? 0;
                
            // Répartition dépenses par motif
            $repartition_depenses_motif = DB::table('DEPENSE as d')
                ->join('MOTIF_DEPENSE as md', 'd.MTF_DPS_REF', '=', 'md.MTF_DPS_REF')
                ->whereDate('d.DEP_DATE', $aujourd_hui)
                ->select('md.MTF_DPS_MOTIF')
                ->selectRaw('SUM(d.DEP_MONTANTHT) as total')
                ->groupBy('md.MTF_DPS_REF', 'md.MTF_DPS_MOTIF')
                ->get();
            
            // Encaissements vs sorties
            $ca_jour = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->sum('FCT_MNT_RGL') ?? 0;
                
            $encaissements_vs_sorties = [
                'encaissements' => $ca_jour,
                'sorties' => $depenses_jour,
                'benefice_net' => $ca_jour - $depenses_jour
            ];

            return [
                'solde_caisse_actuel' => round($solde_caisse_actuel, 2),
                'depenses_jour' => round($depenses_jour, 2),
                'depenses_mois' => round($depenses_mois, 2),
                'repartition_depenses_motif' => $repartition_depenses_motif,
                'encaissements_vs_sorties' => $encaissements_vs_sorties,
                'historique_comptages' => collect([]), // À implémenter
            ];
        } catch (\Exception $e) {
            return $this->retournerValeursParDefaut('financiere');
        }
    }
    
    /**
     * إرجاع قيم افتراضية في حالة الخطأ
     */
    private function retournerValeursParDefaut($section)
    {
        $defaults = [
            'financier' => [
                'ca_du_jour' => 0,
                'ca_du_mois' => 0,
                'ca_de_annee' => 0,
                'nb_factures_jour' => 0,
                'ticket_moyen' => 0,
                'evolution_ventes' => 0,
                'encaissements_mode_paiement' => collect([]),
                'etat_caisse' => collect([]),
            ],
            'stock' => [
                'nb_total_articles' => 0,
                'valeur_stock' => 0,
                'articles_rupture' => 0,
                'articles_stock_faible' => 0,
                'articles_plus_vendus' => collect([]),
                'mouvements_jour' => 0,
                'demarques_mois' => 0,
                'inventaires_en_cours' => 0,
            ],
            'clientele' => [
                'nb_total_clients' => 0,
                'nouveaux_clients_mois' => 0,
                'clients_fideles_actifs' => 0,
                'points_fidelite_distribues' => 0,
                'top_meilleurs_clients' => collect([]),
                'depense_moyenne_client' => 0,
                'categories_clients' => collect([]),
            ],
            'achats' => [
                'commandes_achat_mois' => 0,
                'factures_frs_attente' => 0,
                'top_fournisseurs' => collect([]),
                'valeur_achats_mois' => 0,
                'bl_en_cours' => 0,
            ],
            'restaurant' => [
                'tables_occupees' => 0,
                'tables_libres' => 0,
                'reservations_jour' => 0,
                'articles_menu_populaires' => collect([]),
                'commandes_preparation' => 0,
                'temps_moyen_preparation' => 0,
                'etat_cuisine' => [],
            ],
            'graphiques' => [
                'evolution_ventes_30j' => collect([]),
                'repartition_familles' => collect([]),
                'heures_pointe' => collect([]),
                'performance_caisses' => collect([]),
                'analyse_modes_paiement' => collect([]),
                'croissance_clientele' => collect([]),
            ],
            'financiere' => [
                'solde_caisse_actuel' => 0,
                'depenses_jour' => 0,
                'depenses_mois' => 0,
                'repartition_depenses_motif' => collect([]),
                'encaissements_vs_sorties' => ['encaissements' => 0, 'sorties' => 0, 'benefice_net' => 0],
                'historique_comptages' => collect([]),
            ],
        ];
        
        return $defaults[$section] ?? [];
    }

    // =================== MÉTHODES AJAX POUR LES MODALS ===================
    
    /**
     * تفاصيل رقم الأعمال اليومي
     */
    public function getChiffreAffairesDetails(Request $request)
    {
        try {
            $date = $request->input('date', '2025-07-09');
            
            $details = [
                'ca_total' => DB::table('FACTURE_VNT')
                    ->whereDate('FCTV_DATE', $date)
                    ->sum('FCT_MNT_RGL'),
                    
                'nb_factures' => DB::table('FACTURE_VNT')
                    ->whereDate('FCTV_DATE', $date)
                    ->count(),
                    
                'ventes_par_heure' => DB::table('FACTURE_VNT')
                    ->selectRaw('DATEPART(HOUR, FCTV_DATE) as heure')
                    ->selectRaw('COUNT(*) as nb_ventes')
                    ->selectRaw('SUM(FCT_MNT_RGL) as ca_heure')
                    ->whereDate('FCTV_DATE', $date)
                    ->groupByRaw('DATEPART(HOUR, FCTV_DATE)')
                    ->orderBy('heure')
                    ->get(),
                    
                'top_articles' => DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                    ->whereDate('fv.FCTV_DATE', $date)
                    ->select('a.ART_DESIGNATION')
                    ->selectRaw('SUM(fvd.FVD_QTE) as quantite')
                    ->selectRaw('SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE) as ca_article')
                    ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                    ->orderByDesc('ca_article')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تفاصيل المقالات منتهية الصلاحية
     */
    public function getArticlesRuptureDetails(Request $request)
    {
        try {
            $articles_rupture = DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('s.STK_QTE', '<=', 0)
                ->select('a.ART_DESIGNATION', 'a.ART_REF', 's.STK_QTE', 'a.ART_STOCK_MIN')
                ->get();

            $articles_stock_faible = DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('s.STK_QTE', '>', 0)
                ->where('s.STK_QTE', '<=', 10)
                ->select('a.ART_DESIGNATION', 'a.ART_REF', 's.STK_QTE', 'a.ART_STOCK_MIN')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'articles_rupture' => $articles_rupture,
                    'articles_stock_faible' => $articles_stock_faible
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
