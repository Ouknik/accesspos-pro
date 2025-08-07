<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur pour les rapports de chiffre d'affaires - AccessPos Pro
 * Gestion complète des rapports CA avec export Excel/PDF
 */
class ChiffreAffairesController extends Controller
{
    /**
     * Affichage de la page principale des rapports de CA
     * Dashboard avec statistiques générales et graphiques
     */
    public function index(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            // KPIs principaux
            $kpis = $this->getKPIs($dateDebut, $dateFin);
            
            // Evolution du CA par jour pour graphique
            $evolutionCA = $this->getEvolutionCA($dateDebut, $dateFin);

            // Répartition des moyens de paiement
            $repartitionPaiements = $this->getRepartitionPaiements($dateDebut, $dateFin);
            
            // Top produits et catégories
            $topProduits = $this->getTopProduits($dateDebut, $dateFin);
            $topCategories = $this->getTopCategories($dateDebut, $dateFin);
            
            // Détails des ventes récentes
            $ventesDetails = $this->getVentesDetails($dateDebut, $dateFin);

            return view('admin.chiffre-affaires.index', compact(
                'kpis', 'evolutionCA', 'repartitionPaiements', 'topProduits', 'topCategories', 'ventesDetails',
                'dateDebut', 'dateFin'
            ));
            
        } catch (\Exception $e) {
            return view('admin.chiffre-affaires.index', [
                'error' => 'Erreur lors du chargement des données: ' . $e->getMessage(),
                'kpis' => null, 'evolutionCA' => ['labels' => [], 'data' => []], 
                'repartitionPaiements' => ['labels' => [], 'data' => []], 'topProduits' => [], 'topCategories' => [], 'ventesDetails' => [],
                'dateDebut' => $dateDebut, 'dateFin' => $dateFin
            ]);
        }
    }

    /**
     * Rapport de CA par serveur
     * Analyse détaillée des performances par serveur
     */
    public function rapportServeur(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $serveurs = collect(DB::select("
                SELECT 
                    ISNULL(fv.FCTV_SERVEUR, 'Non défini') as code_serveur,
                    CASE 
                        WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                        THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                        WHEN u.USR_PRENOM IS NOT NULL 
                        THEN u.USR_PRENOM
                        WHEN u.USR_NOM IS NOT NULL 
                        THEN u.USR_NOM
                        ELSE ISNULL(fv.FCTV_SERVEUR, 'Non défini')
                    END as nom_serveur,
                    COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                    SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                    AVG(fv.FCTV_MNT_TTC) as moyenne_facture
                FROM FACTURE_VNT fv
                LEFT JOIN UTILISATEUR u ON (
                    fv.FCTV_SERVEUR = u.USR_LOGIN OR 
                    fv.FCTV_SERVEUR = u.USR_REF OR 
                    fv.FCTV_SERVEUR = u.USR_ABREV
                )
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY fv.FCTV_SERVEUR, u.USR_PRENOM, u.USR_NOM
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.serveur', compact('serveurs', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport serveur: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de CA par famille de produits
     * Analyse des ventes par catégorie avec quantités
     */
    public function rapportFamille(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $familles = collect(DB::select("
                SELECT 
                    ISNULL(f.FAM_LIB, 'Non défini') as famille,
                    COUNT(DISTINCT fvd.FCTV_REF) as nombre_factures,
                    SUM(fvd.FVD_QTE) as quantite_vendue,
                    SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                    AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen
                FROM FACTURE_VNT_DETAIL fvd
                INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY f.FAM_REF, f.FAM_LIB
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.famille', compact('familles', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport familles: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de CA par article/produit
     * Top 50 des produits les plus vendus avec détails complets
     */
    public function rapportArticle(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $articles = collect(DB::select("
                SELECT TOP 50
                    a.ART_REF as reference,
                    a.ART_DESIGNATION as designation,
                    ISNULL(f.FAM_LIB, 'Non défini') as famille,
                    SUM(fvd.FVD_QTE) as quantite_vendue,
                    SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                    AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen,
                    COUNT(DISTINCT fvd.FCTV_REF) as nombre_ventes,
                    MIN(fv.FCTV_DATE) as premiere_vente,
                    MAX(fv.FCTV_DATE) as derniere_vente
                FROM FACTURE_VNT_DETAIL fvd
                INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY a.ART_REF, a.ART_DESIGNATION, f.FAM_LIB
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.article', compact('articles', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport articles: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de CA par mode de paiement
     * Analyse de la répartition des moyens de paiement avec pourcentages
     */
    public function rapportPaiement(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $paiements = collect(DB::select("
                SELECT 
                    CASE 
                        WHEN FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                        WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                        WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                        WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                        WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                        ELSE ISNULL(FCTV_MODEPAIEMENT, 'Espèces')
                    END as mode_paiement,
                    COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                    SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                    AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                    (SUM(fv.FCTV_MNT_TTC) * 100.0 / 
                        (SELECT SUM(FCTV_MNT_TTC) FROM FACTURE_VNT WHERE FCTV_DATE BETWEEN ? AND ? AND FCTV_VALIDE = 1)
                    ) as pourcentage
                FROM FACTURE_VNT fv
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY fv.FCTV_MODEPAIEMENT
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin, $dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.paiement', compact('paiements', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport paiements: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de CA par client
     * Top 30 des meilleurs clients avec historique des achats
     */
    public function rapportClient(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $clients = collect(DB::select("
                SELECT TOP 30
                    c.CLT_REF,
                    c.CLT_CLIENT,
                    c.CLT_TELEPHONE,
                    c.CLT_EMAIL,
                    ISNULL(a.ADR_ADRESSE, 'Non défini') as adresse,
                    ISNULL(a.ADR_VILLE, 'Non défini') as ville,
                    COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                    SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                    AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                    MIN(fv.FCTV_DATE) as premiere_visite,
                    MAX(fv.FCTV_DATE) as derniere_visite,
                    DATEDIFF(day, MIN(fv.FCTV_DATE), MAX(fv.FCTV_DATE)) as duree_fidelite
                FROM FACTURE_VNT fv
                INNER JOIN CLIENT c ON fv.CLT_REF = c.CLT_REF
                LEFT JOIN ADRESSE a ON c.CLT_REF = a.CLT_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY c.CLT_REF, c.CLT_CLIENT, c.CLT_TELEPHONE, c.CLT_EMAIL, a.ADR_ADRESSE, a.ADR_VILLE
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.client', compact('clients', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport clients: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de CA par caissier/utilisateur
     * Performance des utilisateurs avec heures de travail
     */
    public function rapportCaissier(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $caissiers = collect(DB::select("
                SELECT 
                    ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as code_caissier,
                    CASE 
                        WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                        THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                        WHEN u.USR_PRENOM IS NOT NULL 
                        THEN u.USR_PRENOM
                        WHEN u.USR_NOM IS NOT NULL 
                        THEN u.USR_NOM
                        ELSE ISNULL(fv.FCTV_UTILISATEUR, 'Non défini')
                    END as nom_caissier,
                    COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                    SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                    AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                    MIN(fv.FCTV_DATE) as premiere_vente,
                    MAX(fv.FCTV_DATE) as derniere_vente,
                    COUNT(DISTINCT CAST(fv.FCTV_DATE as DATE)) as jours_travailles
                FROM FACTURE_VNT fv
                LEFT JOIN UTILISATEUR u ON (
                    fv.FCTV_UTILISATEUR = u.USR_LOGIN OR 
                    fv.FCTV_UTILISATEUR = u.USR_REF OR 
                    fv.FCTV_UTILISATEUR = u.USR_ABREV
                )
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY fv.FCTV_UTILISATEUR, u.USR_PRENOM, u.USR_NOM
                ORDER BY chiffre_affaires DESC
            ", [$dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.caissier', compact('caissiers', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport caissiers: ' . $e->getMessage());
        }
    }

    /**
     * Rapport d'analyse horaire des ventes
     * Distribution du CA par tranche horaire pour optimiser les équipes
     */
    public function rapportHoraire(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            $horaires = collect(DB::select("
                SELECT 
                    DATEPART(HOUR, fv.FCTV_DATE) as heure,
                    COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                    SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                    AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                    (SUM(fv.FCTV_MNT_TTC) * 100.0 / 
                        (SELECT SUM(FCTV_MNT_TTC) FROM FACTURE_VNT WHERE FCTV_DATE BETWEEN ? AND ? AND FCTV_VALIDE = 1)
                    ) as pourcentage_ca
                FROM FACTURE_VNT fv
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY DATEPART(HOUR, fv.FCTV_DATE)
                ORDER BY heure
            ", [$dateDebut, $dateFin, $dateDebut, $dateFin]));
            
            return view('admin.chiffre-affaires.horaire', compact('horaires', 'dateDebut', 'dateFin'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du rapport horaire: ' . $e->getMessage());
        }
    }

    /**
     * Rapport de synthèse générale avec tous les KPIs
     * Vue d'ensemble complète pour la direction
     */
    public function rapportSynthese(Request $request)
    {
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            // KPIs principaux consolidés
            $kpis = $this->getKPIs($dateDebut, $dateFin);
            
            // Evolution temporelle du CA
            $evolution = $this->getEvolutionCA($dateDebut, $dateFin);
            
            // Répartition des moyens de paiement
            $repartitionPaiements = $this->getRepartitionPaiements($dateDebut, $dateFin);
            
            // Top des produits performants
            $topProduits = $this->getTopProduits($dateDebut, $dateFin);
            
            // Top catégories par CA
            $topCategories = $this->getTopCategories($dateDebut, $dateFin);
            
            return view('admin.chiffre-affaires.synthese', compact(
                'kpis', 'evolution', 'repartitionPaiements', 'topProduits', 'topCategories',
                'dateDebut', 'dateFin'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement de la synthèse: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les indicateurs clés de performance (KPIs)
     * Calcul des métriques principales pour le dashboard
     */
    private function getKPIs($dateFrom, $dateTo)
    {
        try {
            $kpis = DB::selectOne("
                SELECT 
                    COUNT(DISTINCT FCTV_REF) as total_factures,
                    SUM(FCTV_MNT_TTC) as chiffre_affaires_total,
                    AVG(FCTV_MNT_TTC) as moyenne_facture,
                    MIN(FCTV_MNT_TTC) as plus_petite_facture,
                    MAX(FCTV_MNT_TTC) as plus_grande_facture,
                    COUNT(DISTINCT CLT_REF) as nombre_clients_uniques,
                    COUNT(DISTINCT FCTV_UTILISATEUR) as nombre_caissiers
                FROM FACTURE_VNT
                WHERE FCTV_DATE BETWEEN ? AND ?
                    AND FCTV_VALIDE = 1
            ", [$dateFrom, $dateTo]);
            
            return $kpis;
        } catch (\Exception $e) {
            return (object) [
                'total_factures' => 0,
                'chiffre_affaires_total' => 0,
                'moyenne_facture' => 0,
                'plus_petite_facture' => 0,
                'plus_grande_facture' => 0,
                'nombre_clients_uniques' => 0,
                'nombre_caissiers' => 0
            ];
        }
    }

    /**
     * Obtenir l'évolution du chiffre d'affaires jour par jour
     * Données pour les graphiques de tendance
     */
    private function getEvolutionCA($dateFrom, $dateTo)
    {
        try {
            $evolution = DB::select("
                SELECT 
                    CAST(FCTV_DATE as DATE) as date_vente,
                    SUM(FCTV_MNT_TTC) as ca_jour,
                    COUNT(DISTINCT FCTV_REF) as nb_factures_jour,
                    AVG(FCTV_MNT_TTC) as moyenne_jour
                FROM FACTURE_VNT
                WHERE FCTV_DATE BETWEEN ? AND ?
                    AND FCTV_VALIDE = 1
                GROUP BY CAST(FCTV_DATE as DATE)
                ORDER BY date_vente
            ", [$dateFrom, $dateTo]);
            
            $labels = [];
            $data = [];
            $factures = [];
            
            foreach ($evolution as $row) {
                $labels[] = Carbon::parse($row->date_vente)->format('d/m');
                $data[] = floatval($row->ca_jour);
                $factures[] = intval($row->nb_factures_jour);
            }
            
            return [
                'labels' => $labels,
                'data' => $data,
                'factures' => $factures
            ];
        } catch (\Exception $e) {
            return ['labels' => [], 'data' => [], 'factures' => []];
        }
    }

    /**
     * Obtenir la répartition des moyens de paiement
     * Analyse de la distribution des paiements avec pourcentages
     */
    private function getRepartitionPaiements($dateFrom, $dateTo)
    {
        try {
            $paiements = DB::select("
                SELECT 
                    CASE 
                        WHEN FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                        WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                        WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                        WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                        WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal'
                        ELSE ISNULL(FCTV_MODEPAIEMENT, 'Espèces')
                    END as mode_paiement,
                    SUM(FCTV_MNT_TTC) as montant,
                    COUNT(DISTINCT FCTV_REF) as nb_transactions
                FROM FACTURE_VNT
                WHERE FCTV_DATE BETWEEN ? AND ?
                    AND FCTV_VALIDE = 1
                GROUP BY FCTV_MODEPAIEMENT
                ORDER BY montant DESC
            ", [$dateFrom, $dateTo]);
            
            $labels = [];
            $data = [];
            $transactions = [];
            
            foreach ($paiements as $paiement) {
                $labels[] = $paiement->mode_paiement;
                $data[] = floatval($paiement->montant);
                $transactions[] = intval($paiement->nb_transactions);
            }
            
            return [
                'labels' => $labels,
                'data' => $data,
                'transactions' => $transactions
            ];
        } catch (\Exception $e) {
            return ['labels' => [], 'data' => [], 'transactions' => []];
        }
    }

    /**
     * الحصول على الشرائح الزمنية
     * Obtenir les tranches horaires
     */
    private function getTrancheHoraire($dateFrom, $dateTo)
    {
        try {
            $tranches = DB::select("
                SELECT 
                    DATEPART(HOUR, FCTV_DATE) as heure,
                    SUM(FCTV_MNT_TTC) as ca_heure
                FROM FACTURE_VNT
                WHERE FCTV_DATE BETWEEN ? AND ?
                    AND FCTV_VALIDE = 1
                GROUP BY DATEPART(HOUR, FCTV_DATE)
                ORDER BY heure
            ", [$dateFrom, $dateTo]);
            
            $labels = [];
            $data = [];
            
            foreach ($tranches as $tranche) {
                $labels[] = sprintf('%02d:00', $tranche->heure);
                $data[] = floatval($tranche->ca_heure);
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Obtenir le top des produits les plus vendus
     * Classification par chiffre d'affaires avec détails
     */
    private function getTopProduits($dateFrom, $dateTo)
    {
        try {
            return DB::select("
                SELECT TOP 10
                    a.ART_REF,
                    a.ART_DESIGNATION,
                    ISNULL(f.FAM_LIB, 'Non défini') as famille,
                    SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                    SUM(fvd.FVD_QTE) as quantite_vendue,
                    AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen,
                    COUNT(DISTINCT fvd.FCTV_REF) as nombre_ventes
                FROM FACTURE_VNT_DETAIL fvd
                INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY a.ART_REF, a.ART_DESIGNATION, f.FAM_LIB
                ORDER BY chiffre_affaires DESC
            ", [$dateFrom, $dateTo]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtenir le top des catégories par performance
     * Analyse des familles de produits les plus rentables
     */
    private function getTopCategories($dateFrom, $dateTo)
    {
        try {
            return DB::select("
                SELECT 
                    ISNULL(f.FAM_LIB, 'Non défini') as famille,
                    COUNT(DISTINCT fvd.FCTV_REF) as nombre_factures,
                    SUM(fvd.FVD_QTE) as quantite_totale,
                    SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                    AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen
                FROM FACTURE_VNT_DETAIL fvd
                INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                GROUP BY f.FAM_REF, f.FAM_LIB
                ORDER BY chiffre_affaires DESC
            ", [$dateFrom, $dateTo]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtenir les détails des ventes récentes
     * Liste des dernières transactions avec informations complètes
     */
    private function getVentesDetails($dateFrom, $dateTo)
    {
        try {
            return DB::select("
                SELECT TOP 50
                    fv.FCTV_REF as numero_facture,
                    fv.FCTV_DATE as date_vente,
                    c.CLT_CLIENT as client,
                    fv.FCTV_MNT_TTC as montant,
                    CASE 
                        WHEN fv.FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                        WHEN fv.FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                        WHEN fv.FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                        ELSE ISNULL(fv.FCTV_MODEPAIEMENT, 'Espèces')
                    END as mode_paiement,
                    ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as caissier
                FROM FACTURE_VNT fv
                LEFT JOIN CLIENT c ON fv.CLT_REF = c.CLT_REF
                WHERE fv.FCTV_DATE BETWEEN ? AND ?
                    AND fv.FCTV_VALIDE = 1
                ORDER BY fv.FCTV_DATE DESC
            ", [$dateFrom, $dateTo]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Export du rapport en différents formats
     * Gestion des exports PDF, Excel et impression
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf'); // pdf, excel, print
        $report = $request->get('report', 'dashboard'); // dashboard, serveur, famille, article, caissier
        $dateDebut = $request->get('date_debut', $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')));
        $dateFin = $request->get('date_fin', $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')));
        
        try {
            // التحقق من صحة البيانات
            if (!in_array($type, ['pdf', 'excel', 'print'])) {
                return redirect()->back()->with('error', 'Type d\'export non supporté: ' . $type);
            }
            
            if (!in_array($report, ['dashboard', 'serveur', 'famille', 'article', 'caissier', 'paiement', 'client', 'ventes-details'])) {
                return redirect()->back()->with('error', 'Type de rapport non supporté: ' . $report);
            }
            
            switch ($type) {
                case 'pdf':
                    return $this->exportPDF($report, $dateDebut, $dateFin);
                case 'excel':
                    return $this->exportExcel($report, $dateDebut, $dateFin);
                case 'print':
                    return $this->exportPrint($report, $dateDebut, $dateFin);
                default:
                    return redirect()->back()->with('error', 'Type d\'export non supporté');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur export rapport: ' . $e->getMessage(), [
                'type' => $type,
                'report' => $report,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Export en format PDF
     * Génération de documents PDF avec mise en page professionnelle
     */
    private function exportPDF($report, $dateDebut, $dateFin)
    {
        $data = $this->getExportData($report, $dateDebut, $dateFin);
        $title = $this->getReportTitle($report);
        $headers = $this->getReportHeaders($report);
        
        // Créer un résumé si nécessaire
        $summary = $this->getReportSummary($report, $data);
        
        $pdf = \PDF::loadView('admin.chiffre-affaires.pdf.template', [
            'data' => $data,
            'title' => $title,
            'headers' => $headers,
            'summary' => $summary,
            'dateDebut' => Carbon::parse($dateDebut)->format('d/m/Y'),
            'dateFin' => Carbon::parse($dateFin)->format('d/m/Y')
        ]);
        
        $filename = 'rapport_' . $report . '_' . $dateDebut . '_' . $dateFin . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export en format Excel
     * Génération de fichiers Excel avec données structurées
     */
    private function exportExcel($report, $dateDebut, $dateFin)
    {
        try {
            $data = $this->getExportData($report, $dateDebut, $dateFin);
            
            // التحقق من وجود البيانات
            if (empty($data)) {
                return redirect()->back()->with('error', 'Aucune donnée à exporter pour la période sélectionnée.');
            }
            
            $filename = 'rapport_' . $report . '_' . str_replace('-', '_', $dateDebut) . '_' . str_replace('-', '_', $dateFin) . '.xlsx';
            
            return \Excel::download(new \App\Exports\ChiffreAffairesExport($data, $report, $dateDebut, $dateFin), $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Préparation pour l'impression
     * Vue optimisée pour l'impression directe
     */
    private function exportPrint($report, $dateDebut, $dateFin)
    {
        $data = $this->getExportData($report, $dateDebut, $dateFin);
        $title = $this->getReportTitle($report);
        $headers = $this->getReportHeaders($report);
        $summary = $this->getReportSummary($report, $data);
        
        return view('admin.chiffre-affaires.print.template', [
            'data' => $data,
            'title' => $title,
            'headers' => $headers,
            'summary' => $summary,
            'dateDebut' => Carbon::parse($dateDebut)->format('d/m/Y'),
            'dateFin' => Carbon::parse($dateFin)->format('d/m/Y')
        ]);
    }

    /**
     * Obtenir les données pour l'export selon le type de rapport
     * Préparation des données pour tous les formats d'export
     */
    private function getExportData($report, $dateDebut, $dateFin)
    {
        switch ($report) {
            case 'serveur':
                return DB::select("
                    SELECT 
                        ISNULL(fv.FCTV_SERVEUR, 'Non défini') as code_serveur,
                        CASE 
                            WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                            THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                            ELSE ISNULL(fv.FCTV_SERVEUR, 'Non défini')
                        END as nom_serveur,
                        COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                        SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                        AVG(fv.FCTV_MNT_TTC) as moyenne_facture
                    FROM FACTURE_VNT fv
                    LEFT JOIN UTILISATEUR u ON (fv.FCTV_SERVEUR = u.USR_LOGIN OR fv.FCTV_SERVEUR = u.USR_REF)
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    GROUP BY fv.FCTV_SERVEUR, u.USR_PRENOM, u.USR_NOM
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin]);
                
            case 'famille':
                return DB::select("
                    SELECT 
                        ISNULL(f.FAM_LIB, 'Non défini') as famille,
                        COUNT(DISTINCT fvd.FCTV_REF) as nombre_factures,
                        SUM(fvd.FVD_QTE) as quantite_vendue,
                        SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                        AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen
                    FROM FACTURE_VNT_DETAIL fvd
                    INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                    INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                    LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                    LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    GROUP BY f.FAM_REF, f.FAM_LIB
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin]);
                
            case 'article':
                return DB::select("
                    SELECT TOP 50
                        a.ART_REF as reference,
                        a.ART_DESIGNATION as designation,
                        ISNULL(f.FAM_LIB, 'Non défini') as famille,
                        SUM(fvd.FVD_QTE) as quantite_vendue,
                        SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as chiffre_affaires,
                        AVG(fvd.FVD_PRIX_VNT_TTC) as prix_moyen,
                        COUNT(DISTINCT fvd.FCTV_REF) as nombre_ventes
                    FROM FACTURE_VNT_DETAIL fvd
                    INNER JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF
                    INNER JOIN ARTICLE a ON fvd.ART_REF = a.ART_REF
                    LEFT JOIN SOUS_FAMILLE sf ON a.SFM_REF = sf.SFM_REF
                    LEFT JOIN FAMILLE f ON sf.FAM_REF = f.FAM_REF
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    GROUP BY a.ART_REF, a.ART_DESIGNATION, f.FAM_LIB
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin]);
                
            case 'caissier':
                return DB::select("
                    SELECT 
                        ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as code_caissier,
                        CASE 
                            WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                            THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                            ELSE ISNULL(fv.FCTV_UTILISATEUR, 'Non défini')
                        END as nom_caissier,
                        COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                        SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                        AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                        MIN(fv.FCTV_DATE) as premiere_vente,
                        MAX(fv.FCTV_DATE) as derniere_vente
                    FROM FACTURE_VNT fv
                    LEFT JOIN UTILISATEUR u ON (fv.FCTV_UTILISATEUR = u.USR_LOGIN OR fv.FCTV_UTILISATEUR = u.USR_REF)
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    GROUP BY fv.FCTV_UTILISATEUR, u.USR_PRENOM, u.USR_NOM
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin]);
                
            case 'paiement':
                return DB::select("
                    SELECT 
                        CASE 
                            WHEN FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                            WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                            WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                            WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                            WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                            ELSE ISNULL(FCTV_MODEPAIEMENT, 'Espèces')
                        END as mode_paiement,
                        COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                        SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                        AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                        (SUM(fv.FCTV_MNT_TTC) * 100.0 / 
                            (SELECT SUM(FCTV_MNT_TTC) FROM FACTURE_VNT WHERE FCTV_DATE BETWEEN ? AND ? AND FCTV_VALIDE = 1)
                        ) as pourcentage
                    FROM FACTURE_VNT fv
                    WHERE fv.FCTV_DATE BETWEEN ? AND ?
                        AND fv.FCTV_VALIDE = 1
                    GROUP BY fv.FCTV_MODEPAIEMENT
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin, $dateDebut, $dateFin]);
                
            case 'client':
                return DB::select("
                    SELECT TOP 30
                        c.CLT_REF,
                        c.CLT_CLIENT,
                        c.CLT_TELEPHONE,
                        c.CLT_EMAIL,
                        ISNULL(a.ADR_ADRESSE, 'Non défini') as adresse,
                        ISNULL(a.ADR_VILLE, 'Non défini') as ville,
                        COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
                        SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
                        AVG(fv.FCTV_MNT_TTC) as moyenne_facture,
                        MIN(fv.FCTV_DATE) as premiere_visite,
                        MAX(fv.FCTV_DATE) as derniere_visite
                    FROM FACTURE_VNT fv
                    INNER JOIN CLIENT c ON fv.CLT_REF = c.CLT_REF
                    LEFT JOIN ADRESSE a ON c.CLT_REF = a.CLT_REF
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    GROUP BY c.CLT_REF, c.CLT_CLIENT, c.CLT_TELEPHONE, c.CLT_EMAIL, a.ADR_ADRESSE, a.ADR_VILLE
                    ORDER BY chiffre_affaires DESC
                ", [$dateDebut, $dateFin]);
                
            case 'ventes-details':
                return DB::select("
                    SELECT TOP 100
                        fv.FCTV_REF as numero_facture,
                        fv.FCTV_DATE as date_vente,
                        ISNULL(c.CLT_CLIENT, 'Non défini') as client,
                        fv.FCTV_MNT_TTC as montant,
                        CASE 
                            WHEN fv.FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                            WHEN fv.FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                            WHEN fv.FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                            ELSE ISNULL(fv.FCTV_MODEPAIEMENT, 'Espèces')
                        END as mode_paiement,
                        ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as caissier
                    FROM FACTURE_VNT fv
                    LEFT JOIN CLIENT c ON fv.CLT_REF = c.CLT_REF
                    WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
                    ORDER BY fv.FCTV_DATE DESC
                ", [$dateDebut, $dateFin]);
                
            default:
                return [];
        }
    }

    /**
     * Obtenir le titre du rapport selon le type
     * Titres en français pour tous les rapports
     */
    private function getReportTitle($report)
    {
        $titles = [
            'dashboard' => 'Rapport de Chiffre d\'Affaires Complet',
            'serveur' => 'Rapport de CA par Serveur',
            'famille' => 'Rapport de CA par Famille',
            'article' => 'Rapport de CA par Article',
            'caissier' => 'Rapport de CA par Caissier',
            'paiement' => 'Rapport de CA par Mode de Paiement',
            'client' => 'Rapport de CA par Client',
            'horaire' => 'Rapport de CA par Tranche Horaire'
        ];
        
        return $titles[$report] ?? 'Rapport de Chiffre d\'Affaires';
    }

    /**
     * Obtenir les en-têtes de colonnes du rapport
     * Headers adaptés à chaque type de rapport
     */
    private function getReportHeaders($report)
    {
        $headers = [
            'serveur' => ['Code Serveur', 'Nom Serveur', 'Nb Factures', 'CA (DH)', 'Moyenne (DH)'],
            'famille' => ['Famille', 'Nb Factures', 'Quantité', 'CA (DH)', 'Prix Moyen (DH)'],
            'article' => ['Référence', 'Désignation', 'Famille', 'Quantité', 'CA (DH)', 'Prix Moyen', 'Nb Ventes'],
            'caissier' => ['Code', 'Nom Caissier', 'Nb Factures', 'CA (DH)', 'Moyenne (DH)', '1ère Vente', 'Dernière Vente'],
            'paiement' => ['Mode Paiement', 'Nb Factures', 'CA (DH)', 'Moyenne (DH)', 'Pourcentage (%)'],
            'client' => ['Réf Client', 'Nom Client', 'Téléphone', 'Nb Factures', 'CA (DH)', 'Moyenne (DH)', 'Dernière Visite']
        ];
        
        return $headers[$report] ?? [];
    }

    /**
     * Obtenir le résumé statistique du rapport
     * Calculs et métriques consolidées
     */
    private function getReportSummary($report, $data)
    {
        if (empty($data)) return null;
        
        $collection = collect($data);
        
        return [
            'Total Enregistrements' => $collection->count(),
            'CA Total' => number_format($collection->sum('chiffre_affaires'), 2, ',', ' ') . ' DH',
            'CA Moyen' => number_format($collection->avg('chiffre_affaires'), 2, ',', ' ') . ' DH',
            'CA Médian' => number_format($collection->median('chiffre_affaires'), 2, ',', ' ') . ' DH'
        ];
    }

}
