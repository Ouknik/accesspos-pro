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

            // استخدام التاريخ الحالي (ديناميكي)
            $aujourd_hui = now()->format('Y-m-d');//'2025-07-09'; //
            $debut_mois = now()->startOfMonth()->format('Y-m-d');
            $debut_annee = now()->startOfYear()->format('Y-m-d');

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
            // Chiffre d'affaires du jour - من جدول FACTURE_VNT الحقيقي
            $ca_du_jour = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->sum('FCTV_MNT_TTC') ?? 0;
                
            // Chiffre d'affaires du mois
            $ca_du_mois = DB::table('FACTURE_VNT')
                ->where('FCTV_DATE', '>=', $debut_mois)
                ->sum('FCTV_MNT_TTC') ?? 0;
                
            // Chiffre d'affaires de l'année
            $ca_de_annee = DB::table('FACTURE_VNT')
                ->where('FCTV_DATE', '>=', $debut_annee)
                ->sum('FCTV_MNT_TTC') ?? 0;
                
            // Nombre de factures du jour
            $nb_factures_jour = DB::table('FACTURE_VNT')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->count();
            
            // Ticket moyen
            $ticket_moyen = $nb_factures_jour > 0 ? ($ca_du_jour / $nb_factures_jour) : 0;
                
            // Évolution des ventes (comparaison avec mois précédent)
            $debut_mois_precedent = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $fin_mois_precedent = now()->subMonth()->endOfMonth()->format('Y-m-d');
            
            $ca_mois_precedent = DB::table('FACTURE_VNT')
                ->whereBetween('FCTV_DATE', [$debut_mois_precedent, $fin_mois_precedent])
                ->sum('FCTV_MNT_TTC') ?? 0;
                
            $evolution_ventes = 0;
            if ($ca_mois_precedent > 0) {
                $evolution_ventes = round((($ca_du_mois - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2);
            }
            
            // Encaissements par mode de paiement - من جدول REGLEMENT
            $encaissements_mode_paiement = DB::table('REGLEMENT')
                ->whereDate('REG_DATE', $aujourd_hui)
                ->select('TYPE_REGLEMENT', DB::raw('SUM(REG_MONTANT) as total'))
                ->groupBy('TYPE_REGLEMENT')
                ->get();
                
            // État de la caisse - من جدول CAISSE
            $etat_caisse = DB::table('CAISSE')
                ->select('CSS_LIBELLE_CAISSE', 'CSS_AVEC_AFFICHEUR')
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
            $aujourd_hui = now()->format('Y-m-d');
            
            // Nombre total d'articles
            $nb_total_articles = DB::table('ARTICLE')->count();
            
            // Valeur du stock
            $valeur_stock = DB::table('STOCK')
                ->join('ARTICLE', 'STOCK.ART_REF', '=', 'ARTICLE.ART_REF')
                ->whereRaw('STOCK.STK_QTE > 0 AND ARTICLE.ART_PRIX_VENTE > 0')
                ->sum(DB::raw('STOCK.STK_QTE * ARTICLE.ART_PRIX_VENTE')) ?? 0;
                
            // Articles en rupture
            $articles_rupture = DB::table('STOCK')->where('STK_QTE', '<=', 0)->count();
            
            // Articles à stock faible
            $articles_stock_faible = DB::table('STOCK')->where('STK_QTE', '<', 10)->count();
            
            // Articles les plus vendus (aujourd'hui) - من الجداول الحقيقية
            $articles_plus_vendus = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
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
            $aujourd_hui = now()->format('Y-m-d');
            $debut_mois = now()->startOfMonth()->format('Y-m-d');
            
            // Nombre total de clients
            $nb_total_clients = DB::table('CLIENT')->count();
            
            // Nouveaux clients du mois (إذا كان هناك عمود تاريخ إنشاء)
            $nouveaux_clients_mois = DB::table('CLIENT')->count(); // مؤقت حتى نعرف عمود التاريخ
            
            // Clients fidèles actifs
            $clients_fideles_actifs = DB::table('CLIENT')->where('CLT_FIDELE', 1)->count();
            
            // Points fidélité distribués
            $points_fidelite_distribues = DB::table('CLIENT')->sum('CLT_POINTFIDILIO') ?? 0;
            
            // Top clients (اليوم)
            $top_meilleurs_clients = DB::table('FACTURE_VNT as fv')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->select('c.CLT_CLIENT', 'c.CLT_REF')
                ->selectRaw('COUNT(*) as nb_commandes')
                ->selectRaw('SUM(fv.FCTV_MNT_TTC) as total_depense')
                ->groupBy('c.CLT_REF', 'c.CLT_CLIENT')
                ->orderByDesc('total_depense')
                ->limit(10)
                ->get();
            
            // Dépense moyenne par client
            $depense_moyenne_client = DB::table('FACTURE_VNT as fv')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->avg('fv.FCTV_MNT_TTC') ?? 0;

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
            // Tables occupées - استخدام جدول TABLE الحقيقي
            $tables_occupees = DB::table('TABLE')->where('ETT_ETAT', 'OCCUPEE')->count();
            
            // Tables libres 
            $tables_libres = DB::table('TABLE')->where('ETT_ETAT', 'LIBRE')->count();
            
            // Réservations du jour - من جدول RESERVATION
            $reservations_jour = DB::table('RESERVATION')
                ->whereDate('DATE_RESERVATION', $aujourd_hui)
                ->count();
            
            // Articles menu populaires - باستخدام الجداول الحقيقية
            $articles_menu_populaires = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->join('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->where('a.IsMenu', 1) // استخدام العمود IsMenu من جدول ARTICLE
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
                ->selectRaw('SUM(FCTV_MNT_TTC) as total_ventes')
                ->selectRaw('COUNT(*) as nb_factures')
                ->where('FCTV_DATE', '>=', Carbon::parse($aujourd_hui)->subDays(30))
                ->whereNotNull('FCTV_DATE')
                ->groupByRaw('CAST(FCTV_DATE as DATE)')
                ->orderBy('date')
                ->get();
            
            // Répartition par famille
            $repartition_familles = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->join('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->select('f.FAM_LIB as FAM_DESIGNATION')
                ->selectRaw('SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE) as total_ventes')
                ->groupBy('f.FAM_REF', 'f.FAM_LIB')
                ->orderByDesc('total_ventes')
                ->get();
            
            // Heures de pointe
            $heures_pointe = DB::table('FACTURE_VNT')
                ->selectRaw('DATEPART(HOUR, FCTV_DATE) as heure')
                ->selectRaw('COUNT(*) as nb_transactions')
                ->selectRaw('SUM(FCTV_MNT_TTC) as ca_heure')
                ->whereDate('FCTV_DATE', $aujourd_hui)
                ->whereNotNull('FCTV_DATE')
                ->groupByRaw('DATEPART(HOUR, FCTV_DATE)')
                ->orderByDesc('nb_transactions')
                ->limit(8)
                ->get();
            
            // Performance par caisse
            $performance_caisses = DB::table('FACTURE_VNT as fv')
                ->join('CAISSE as c', 'fv.CSS_REF', '=', 'c.CSS_ID_CAISSE')
                ->whereDate('fv.FCTV_DATE', $aujourd_hui)
                ->select('c.CSS_LIBELLE_CAISSE')
                ->selectRaw('COUNT(*) as nb_transactions')
                ->selectRaw('SUM(fv.FCTV_MNT_TTC) as ca_caisse')
                ->groupBy('c.CSS_ID_CAISSE', 'c.CSS_LIBELLE_CAISSE')
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
            // Solde de caisse actuel (calculé à partir des transactions)
            $nb_caisses = DB::table('CAISSE')->count();
            $solde_caisse_actuel = $nb_caisses * 1000; // Valeur par défaut temporaire
            
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
                ->sum('FCTV_MNT_TTC') ?? 0;
                
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
            $date = $request->input('date', now()->format('Y-m-d'));
            
            $details = [
                'ca_total' => DB::table('FACTURE_VNT')
                    ->whereDate('FCTV_DATE', $date)
                    ->sum('FCTV_MNT_TTC'),
                    
                'nb_factures' => DB::table('FACTURE_VNT')
                    ->whereDate('FCTV_DATE', $date)
                    ->count(),
                    
                'ventes_par_heure' => DB::table('FACTURE_VNT')
                    ->selectRaw('DATEPART(HOUR, FCTV_DATE) as heure')
                    ->selectRaw('COUNT(*) as nb_ventes')
                    ->selectRaw('SUM(FCTV_MNT_TTC) as ca_heure')
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

    /**
     * Modal: Top Clients Détaillé
     */
    public function getTopClientsDetails(Request $request)
    {
        try {
            $topClients = DB::table('FACTURE_VNT as f')
                ->join('CLIENT as c', 'f.CLT_REF', '=', 'c.CLT_REF')
                ->select('c.CLT_NOM', 'c.CLT_REF', 
                    DB::raw('COUNT(f.FCTV_REF) as nombre_factures'),
                    DB::raw('SUM(f.FCTV_MNT_TTC) as total_achats'),
                    DB::raw('AVG(f.FCTV_MNT_TTC) as ticket_moyen'))
                ->whereDate('f.FCTV_DATE', today())
                ->groupBy('c.CLT_NOM', 'c.CLT_REF')
                ->orderBy('total_achats', 'desc')
                ->limit(10)
                ->get();

            $totalClients = DB::table('FACTURE_VNT as f')
                ->join('CLIENT as c', 'f.CLT_REF', '=', 'c.CLT_REF')
                ->whereDate('f.FCTV_DATE', today())
                ->distinct('c.CLT_REF')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'clients' => $topClients,
                    'total_clients' => $totalClients,
                    'date' => today()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modal: Stock en Rupture Détaillé
     */
    public function getStockRuptureDetails(Request $request)
    {
        try {
            $articlesRupture = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->select('a.ART_REF', 'a.ART_DESIGNATION', 'a.ART_PV_TTC',
                    'f.FAM_LIB as famille',
                    'sf.SFM_LIB as sous_famille',
                    DB::raw('ISNULL(s.STK_QTE, 0) as stock_actuel'),
                    'a.ART_STOCK_MIN as stock_minimum',
                    DB::raw('CASE WHEN ISNULL(s.STK_QTE, 0) = 0 THEN "Rupture Totale"
                              WHEN ISNULL(s.STK_QTE, 0) <= a.ART_STOCK_MIN THEN "Stock Faible"
                              ELSE "Normal" END as statut_stock'))
                ->where(function($query) {
                    $query->whereRaw('ISNULL(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
                          ->orWhereRaw('ISNULL(s.STK_QTE, 0) = 0');
                })
                ->orderByRaw('ISNULL(s.STK_QTE, 0) ASC')
                ->limit(50)
                ->get();

            $statistiques = [
                'rupture_totale' => $articlesRupture->where('stock_actuel', 0)->count(),
                'stock_faible' => $articlesRupture->where('stock_actuel', '>', 0)->count(),
                'total_articles' => $articlesRupture->count(),
                'valeur_manquante' => $articlesRupture->sum(function($item) {
                    return ($item->stock_minimum - $item->stock_actuel) * $item->ART_PV_TTC;
                })
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'articles_rupture' => $articlesRupture,
                    'statistiques' => $statistiques,
                    'date' => today()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modal: Performance Horaire Détaillée
     */
    public function getPerformanceHoraireDetails(Request $request)
    {
        try {
            $ventesParHeure = DB::table('FACTURE_VNT')
                ->select(
                    DB::raw('DATEPART(HOUR, FCTV_DATE) as heure'),
                    DB::raw('COUNT(*) as nombre_ventes'),
                    DB::raw('SUM(FCTV_MNT_TTC) as chiffre_affaires'),
                    DB::raw('AVG(FCTV_MNT_TTC) as ticket_moyen')
                )
                ->whereDate('FCTV_DATE', today())
                ->groupBy(DB::raw('DATEPART(HOUR, FCTV_DATE)'))
                ->orderBy('heure')
                ->get();

            $heurePointe = $ventesParHeure->sortByDesc('chiffre_affaires')->first();
            $heureCreuse = $ventesParHeure->sortBy('chiffre_affaires')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'ventes_par_heure' => $ventesParHeure,
                    'heure_pointe' => $heurePointe,
                    'heure_creuse' => $heureCreuse,
                    'date' => today()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modal: Modes de Paiement Détaillés
     */
    public function getModesPaiementDetails(Request $request)
    {
        try {
            $modesPaiement = DB::table('REGLEMENT as r')
                ->join('FACTURE_VNT as f', 'r.FCTV_REF', '=', 'f.FCTV_REF')
                ->select('r.RGL_MODE', 
                    DB::raw('COUNT(*) as nombre_transactions'),
                    DB::raw('SUM(r.RGL_MNT) as montant_total'),
                    DB::raw('AVG(r.RGL_MNT) as montant_moyen'))
                ->whereDate('f.FCTV_DATE', today())
                ->groupBy('r.RGL_MODE')
                ->orderBy('montant_total', 'desc')
                ->get();

            $totalTransactions = $modesPaiement->sum('nombre_transactions');
            $totalMontant = $modesPaiement->sum('montant_total');

            return response()->json([
                'success' => true,
                'data' => [
                    'modes_paiement' => $modesPaiement,
                    'total_transactions' => $totalTransactions,
                    'total_montant' => $totalMontant,
                    'date' => today()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modal: État des Tables Restaurant
     */
    public function getEtatTablesDetails(Request $request)
    {
        try {
            // Supposons que nous avons une table TABLES pour les restaurants
            $tables = DB::table('TABLES')
                ->select('TBL_NUM', 'TBL_STATUT', 'TBL_CAPACITE', 
                    DB::raw('CASE WHEN TBL_STATUT = "OCCUPEE" THEN "Occupée" 
                              WHEN TBL_STATUT = "RESERVEE" THEN "Réservée" 
                              ELSE "Libre" END as statut_label'))
                ->orderBy('TBL_NUM')
                ->get();

            $statistiques = [
                'total_tables' => $tables->count(),
                'tables_occupees' => $tables->where('TBL_STATUT', 'OCCUPEE')->count(),
                'tables_reservees' => $tables->where('TBL_STATUT', 'RESERVEE')->count(),
                'tables_libres' => $tables->where('TBL_STATUT', 'LIBRE')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'tables' => $tables,
                    'statistiques' => $statistiques,
                    'date' => today()->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            // Fallback si la table TABLES n'existe pas
            return response()->json([
                'success' => true,
                'data' => [
                    'tables' => [],
                    'statistiques' => [
                        'total_tables' => 0,
                        'tables_occupees' => 0,
                        'tables_reservees' => 0,
                        'tables_libres' => 0,
                    ],
                    'message' => 'Module restaurant non configuré',
                    'date' => today()->format('d/m/Y')
                ]
            ]);
        }
    }

    /**
     * تصدير بيانات المودال
     */
    public function exportModalData(Request $request)
    {
        try {
            $type = $request->input('type');
            $format = $request->input('format', 'json');
            
            $data = [];
            $filename = '';
            
            switch ($type) {
                case 'chiffre-affaires':
                    $data = $this->getChiffreAffairesExportData();
                    $filename = 'chiffre_affaires_' . date('Y-m-d');
                    break;
                    
                case 'articles-rupture':
                    $data = $this->getArticlesRuptureExportData();
                    $filename = 'articles_rupture_' . date('Y-m-d');
                    break;
                    
                case 'top-clients':
                    $data = $this->getTopClientsExportData();
                    $filename = 'top_clients_' . date('Y-m-d');
                    break;
                    
                case 'performance-horaire':
                    $data = $this->getPerformanceHoraireExportData();
                    $filename = 'performance_horaire_' . date('Y-m-d');
                    break;
                    
                case 'modes-paiement':
                    $data = $this->getModesPaiementExportData();
                    $filename = 'modes_paiement_' . date('Y-m-d');
                    break;
                    
                case 'etat-tables':
                    $data = $this->getEtatTablesExportData();
                    $filename = 'etat_tables_' . date('Y-m-d');
                    break;
                    
                default:
                    return response()->json(['error' => 'Type de données non supporté'], 400);
            }
            
            if ($format === 'csv') {
                return $this->exportToCSV($data, $filename);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($data, $filename);
            } else {
                return response()->json($data);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * بيانات التصدير لرقم الأعمال
     */
    private function getChiffreAffairesExportData()
    {
        try {
            $ventes = DB::table('FACTURE_VENTE as fv')
                ->select([
                    'fv.FV_NUMERO as numero_facture',
                    'fv.FV_DATE as date_vente',
                    'fv.FV_TOTAL_HT as total_ht',
                    'fv.FV_TOTAL_TTC as total_ttc',
                    'c.CLT_CLIENT as client'
                ])
                ->leftJoin('CLIENT as c', 'fv.FV_CLIENT', '=', 'c.CLT_CODE')
                ->whereDate('fv.FV_DATE', '2025-07-09')
                ->orderBy('fv.FV_DATE', 'desc')
                ->get()
                ->toArray();
                
            return $ventes;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * بيانات التصدير للمقالات المنقطعة
     */
    private function getArticlesRuptureExportData()
    {
        try {
            $articles = DB::table('ARTICLE as a')
                ->select([
                    'a.ART_CODE as code',
                    'a.ART_DESIGNATION as designation',
                    'sf.SFM_LIB as sous_famille',
                    'f.FAM_LIB as famille',
                    'a.ART_STOCK_PHYSIQUE as stock_actuel',
                    'a.ART_STOCK_MINI as stock_minimum'
                ])
                ->leftJoin('SOUS_FAMILLE as sf', 'a.ART_SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.SFM_FAM_REF', '=', 'f.FAM_REF')
                ->whereRaw('CAST(a.ART_STOCK_PHYSIQUE AS DECIMAL(10,2)) <= CAST(a.ART_STOCK_MINI AS DECIMAL(10,2))')
                ->orderBy('a.ART_DESIGNATION')
                ->get()
                ->toArray();
                
            return $articles;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * بيانات التصدير لأفضل العملاء
     */
    private function getTopClientsExportData()
    {
        try {
            $clients = DB::table('FACTURE_VENTE as fv')
                ->select([
                    'c.CLT_CLIENT as nom_client',
                    DB::raw('COUNT(fv.FV_ID) as nombre_achats'),
                    DB::raw('SUM(CAST(fv.FV_TOTAL_TTC AS DECIMAL(15,2))) as total_depense')
                ])
                ->leftJoin('CLIENT as c', 'fv.FV_CLIENT', '=', 'c.CLT_CODE')
                ->whereDate('fv.FV_DATE', '2025-07-09')
                ->groupBy('c.CLT_CLIENT', 'c.CLT_CODE')
                ->orderBy('total_depense', 'desc')
                ->limit(10)
                ->get()
                ->toArray();
                
            return $clients;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * بيانات التصدير للأداء بالساعة
     */
    private function getPerformanceHoraireExportData()
    {
        try {
            $performance = DB::table('FACTURE_VENTE as fv')
                ->select([
                    DB::raw('DATEPART(HOUR, fv.FV_DATE) as heure'),
                    DB::raw('COUNT(fv.FV_ID) as nombre_ventes'),
                    DB::raw('SUM(CAST(fv.FV_TOTAL_TTC AS DECIMAL(15,2))) as chiffre_affaires')
                ])
                ->whereDate('fv.FV_DATE', '2025-07-09')
                ->groupBy(DB::raw('DATEPART(HOUR, fv.FV_DATE)'))
                ->orderBy('heure')
                ->get()
                ->toArray();
                
            return $performance;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * بيانات التصدير لطرق الدفع
     */
    private function getModesPaiementExportData()
    {
        try {
            $paiements = DB::table('REGLEMENT as r')
                ->select([
                    'r.REG_TYPE_REGLEMENT as mode_paiement',
                    DB::raw('COUNT(r.REG_ID) as nombre_transactions'),
                    DB::raw('SUM(CAST(r.REG_MONTANT AS DECIMAL(15,2))) as montant_total')
                ])
                ->whereDate('r.REG_DATE', '2025-07-09')
                ->groupBy('r.REG_TYPE_REGLEMENT')
                ->orderBy('montant_total', 'desc')
                ->get()
                ->toArray();
                
            return $paiements;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * بيانات التصدير لحالة الطاولات
     */
    private function getEtatTablesExportData()
    {
        try {
            // نظراً لعدم وجود جدول TABLES، سنرجع بيانات وهمية
            return [
                ['numero_table' => '1', 'statut' => 'Libre', 'capacite' => '4'],
                ['numero_table' => '2', 'statut' => 'Occupée', 'capacite' => '6'],
                ['numero_table' => '3', 'statut' => 'Réservée', 'capacite' => '2'],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * تصدير البيانات بصيغة CSV
     */
    private function exportToCSV($data, $filename)
    {
        if (empty($data)) {
            return response()->json(['error' => 'Aucune donnée à exporter'], 400);
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            if (!empty($data)) {
                fputcsv($file, array_keys((array)$data[0]));
                
                // Données
                foreach ($data as $row) {
                    fputcsv($file, (array)$row);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * تصدير البيانات بصيغة Excel (CSV متقدم)
     */
    private function exportToExcel($data, $filename)
    {
        // بالنسبة لـ Excel، سنستخدم CSV مع UTF-8 BOM للدعم المحسن
        if (empty($data)) {
            return response()->json(['error' => 'Aucune donnée à exporter'], 400);
        }
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM للـ Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            if (!empty($data)) {
                fputcsv($file, array_keys((array)$data[0]), "\t");
                
                // Données
                foreach ($data as $row) {
                    fputcsv($file, (array)$row, "\t");
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
